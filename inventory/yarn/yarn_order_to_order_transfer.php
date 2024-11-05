<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Order To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	26-06-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Order To Order Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage_systemId()
	{
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}

		var title = 'Item Transfer Info';	
		var page_link = 'requires/yarn_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
		var theform=this.contentDoc.forms[0];
		var transfer_id=this.contentDoc.getElementById("transfer_id").value;
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/yarn_order_to_order_transfer_controller" );
		load_drop_down( 'requires/yarn_order_to_order_transfer_controller', $('#txt_from_order_id').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/yarn_order_to_order_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/yarn_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_id=this.contentDoc.getElementById("order_id").value;
		
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/yarn_order_to_order_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/yarn_order_to_order_transfer_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
		}
	}
}

function openmypage_orderInfo(type)
{
	var txt_order_no = $('#txt_'+type+'_order_no').val();
	var txt_order_id = $('#txt_'+type+'_order_id').val();

	if (form_validation('txt_'+type+'_order_no','Order No')==false)
	{
		alert("Please Select Order No.");
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/yarn_order_to_order_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}


function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title,"yarn_order_to_order_transfer_print","requires/yarn_order_to_order_transfer_controller") 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no*cbo_item_desc*txt_transfer_qnty','Company*Transfer Date*From Order No*To Order No*Item Description*Transfered Qnty')==false )
		{
			return;
		}	
		
		var current_date = '<? echo date("d-m-Y"); ?>';
		if (date_compare($('#txt_transfer_date').val(), current_date) == false) {
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}

		if(($("#txt_transfer_qnty").val()*1 > $("#txt_transferable_qnty").val()*1+$("#hide_transfer_qnty").val()*1)) 
		{
			alert("Transfer Quantity Exceeds Stock Quantity.");
			$("#txt_transfer_qnty").focus();
			return;
		}

		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*cbo_item_category*cbo_item_desc*txt_transfer_qnty*cbo_uom*update_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");

		freeze_window(operation);
		http.open("POST","requires/yarn_order_to_order_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_transfer_entry_reponse;
	}
}

function fnc_yarn_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');	
		if (reponse[0] * 1 == 20 * 1) {
			alert(reponse[1]);
			release_freezing();
			return;
		}

		if(reponse[0]==0 || reponse[0]==1)
		{
			show_msg(reponse[0]);
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			$('#cbo_company_id').attr('disabled','disabled');
			$('#txt_from_order_no').attr('disabled','disabled');
			$('#txt_to_order_no').attr('disabled','disabled');
			
			reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*update_trans_issue_id*update_trans_recv_id*hide_transfer_qnty*txt_cum_issue_qnty*txt_tot_transfer_qnty*txt_transferable_qnty','','','');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/yarn_order_to_order_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		}	
		release_freezing();
	}
}

function reset_dropDown()
{
	$('#itemDescTd').html('<? echo create_drop_down( "cbo_item_desc", 300, $blank_array,'', 1, "--Select Item Description--", 0, "" ); ?>');
}

function load_item_stock_data(prod_id)
{
	var po_id=$("#txt_from_order_id").val();
	get_php_form_data(po_id+"**"+prod_id, "populate_data_from_item_stock", "requires/yarn_order_to_order_transfer_controller" );
}

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
			<div style="width:100%;">   
				<fieldset style="width:900px;">
					<legend>Yarn Order To Order Transfer Entry</legend>
					<br>
					<fieldset style="width:800px;">
						<table width="760" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
							<tr>
								<td colspan="3" align="right"><strong>Transfer System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
								</td>
							</tr>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company</td>
								<td>
									<? 
									echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
									?>
								</td>
								<td class="must_entry_caption">Transfer Date</td>
								<td>
									<input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
								</td> 
								<td>Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br>
					<table width="800" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
						<tr>
							<td width="49%" valign="top">
								<fieldset>
									<legend>From Order</legend>
									<table id="from_order_info"  cellpadding="0" cellspacing="1" width="100%">										
										<tr>
											<td width="30%" class="must_entry_caption">Order No</td>
											<td>
												<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
												<input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
											</td>
										</tr>
										<tr>
											<td>Order Qnty</td>
											<td>
												<input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
											</tr>
											<tr>	
												<td>Buyer</td>
												<td>
													<? 
													echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
													?>	  	
												</td>
											</tr>						
											<tr>
												<td>Style Ref.</td>
												<td>
													<input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
												</tr>
												<tr>
													<td>Job No</td>						
													<td>                       
														<input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
													</td>
												</tr>
												<tr>
													<td>Gmts Item</td>
													<td>
														<input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
													</tr>
													<tr>
														<td>Shipment Date</td>						
														<td>
															<input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />&nbsp;
															<input type="button" class="formbutton" style="width:80px" value="View" onClick="openmypage_orderInfo('from');">
														</td>
													</tr>
												</table>
											</fieldset>
										</td>
										<td width="2%" valign="top"></td>
										<td width="49%" valign="top">
											<fieldset>
												<legend>To Order</legend>					
												<table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
													<tr>
														<td width="30%" class="must_entry_caption">Order No</td>
														<td>
															<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
															<input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
														</td>
													</tr>
													<tr>
														<td>Order Qnty</td>
														<td>
															<input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
														</tr>
														<tr>	
															<td>Buyer</td>
															<td>
																<? 
																echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
																?>	  	
															</td>
														</tr>						
														<tr>
															<td>Style Ref.</td>
															<td>
																<input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
															</tr>
															<tr>
																<td>Job No</td>						
																<td>                       
																	<input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
																</td>
															</tr>
															<tr>
																<td>Gmts Item</td>
																<td>
																	<input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
																</tr>
																<tr>
																	<td>Shipment Date</td>						
																	<td>
																		<input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />&nbsp;
																		<input type="button" class="formbutton" style="width:80px" value="View" onClick="openmypage_orderInfo('to');">
																	</td>
																</tr>											
															</table>                  
														</fieldset>	
													</td>
												</tr>	
												<tr>
													<td>
														<fieldset style="margin-top:10px">
															<legend>Item Info</legend>
															<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">										
																<tr>
																	<td>Item Category</td>
																	<td>
																		<?
																		echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',1 );
																		?>
																	</td>
																</tr>
																<tr>
																	<td class="must_entry_caption">Item Description</td>
																	<td id="itemDescTd">
																		<?
																		echo create_drop_down( "cbo_item_desc", 250, $blank_array,'', 1, "--Select Item Description--", 0, "" );
																		?>	
																	</td>
																</tr>
																<tr>
																	<td class="must_entry_caption">Transfered Qnty</td>
																	<td>
																		<input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:150px;" />
																		<input type="hidden" name="hide_transfer_qnty" id="hide_transfer_qnty" class="text_boxes_numeric" style="width:150px;" />
																	</td>
																</tr>
																<tr>
																	<td>UOM</td>
																	<td>
																		<?
																		echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 0, "", '', "",1,12 );
																		?>
																	</td>
																</tr>
															</table>
														</fieldset>
													</td>
													<td width="2%" valign="top"></td>
													<td valign="top">
														<fieldset style="margin-top:10px">
															<legend>Dispaly</legend>
															<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">										
																<tr>
																	<td>Cumulative Issued Qty.</td>
																	<td><input class="text_boxes_numeric" type="text" name="txt_cum_issue_qnty" id="txt_cum_issue_qnty" style="width:150px;" placeholder="Display" readonly /></td>
																</tr>
																<tr>
																	<td>Total Transfered Qty.</td>
																	<td><input type="text" name="txt_tot_transfer_qnty" id="txt_tot_transfer_qnty" class="text_boxes_numeric" style="width:150px;" placeholder="Display" readonly /></td>
																</tr>
																<tr>
																	<td>Order Stcok Qty.</td>
																	<td><input type="text" name="txt_transferable_qnty" id="txt_transferable_qnty" class="text_boxes_numeric" style="width:150px;" placeholder="Display" readonly /></td>
																</tr>
															</table>
														</fieldset>
													</td>
												</tr> 	
												<tr>
													<td align="center" colspan="3" class="button_container" width="100%">
														<?
														echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','disable_enable_fields(\'cbo_company_id\');reset_dropDown();')",1);
														?>
														<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
														<input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
														<input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
													</td>
												</tr>
											</table>
											<div style="width:780px;" id="div_transfer_item_list"></div>
										</fieldset>
									</div>
								</form>
							</div>    
						</body>  
						<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
						</html>
