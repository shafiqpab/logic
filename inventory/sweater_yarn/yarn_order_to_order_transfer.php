<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Style To Style Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	26-11-2018
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
echo load_html_head_contents("Yarn Style To Style Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	
	function active_inactive(str)
	{
		reset_form('','','cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*txt_remarks*update_dtls_id*cbo_item_desc*txt_transfer_qnty*update_trans_issue_id*update_trans_recv_id*hide_transfer_qnty*txt_cum_issue_qnty*txt_tot_transfer_qnty*txt_transferable_qnty','','','');
		load_room_rack_self_bin('requires/yarn_order_to_order_transfer_controller*1', 'store','store_td', 0,'','','','','','','','');
		load_room_rack_self_bin('requires/yarn_order_to_order_transfer_controller*1*cbo_store_name_to', 'store','to_store_td', 0);
		if(str==1)
		{
			$('#cbo_company_id_to').removeAttr('disabled','disabled');	
		}
		else
		{
			$('#cbo_company_id_to').attr('disabled','disabled');
		}
	}
	
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
			//var 
			load_drop_down( 'requires/yarn_order_to_order_transfer_controller', $('#txt_from_job').val()+'**'+$('#cbo_store_name').val(), 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/yarn_order_to_order_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		}
	}

	function openmypage_orderNo(type)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_company_id_to = $('#cbo_company_id_to').val();
		var cbo_store_name = $('#cbo_store_name').val();
		var cbo_store_name_to = $('#cbo_store_name_to').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		var title = 'Order Info';
		if(type=='to')
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_transfer_criteria*cbo_company_id_to*cbo_store_name_to','Transfer Criteria*Company*Store')==false)
				{
					return;
				}
				var page_link = 'requires/yarn_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id_to+'&cbo_store_name='+cbo_store_name_to+'&transfer_criteria='+cbo_transfer_criteria+'&type='+type+'&action=order_popup';
			}
			else
			{
				if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name_to','Transfer Criteria*Company*Store')==false)
				{
					return;
				}
				var page_link = 'requires/yarn_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name_to+'&transfer_criteria='+cbo_transfer_criteria+'&type='+type+'&action=order_popup';
			}
		}
		else
		{
			if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name','Transfer Criteria*Company*Store')==false)
			{
				return;
			}
			var page_link = 'requires/yarn_order_to_order_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&transfer_criteria='+cbo_transfer_criteria+'&type='+type+'&action=order_popup';
		}		
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_id=this.contentDoc.getElementById("order_id").value.split("_");
			get_php_form_data(order_id[0]+"**"+type+"**"+cbo_transfer_criteria, "populate_data_from_order", "requires/yarn_order_to_order_transfer_controller" );
			if(type=='from')
			{
				load_drop_down( 'requires/yarn_order_to_order_transfer_controller', order_id[1]+"**"+cbo_store_name, 'load_drop_down_item_desc', 'itemDescTd' );
				$('#cbo_store_name').attr('disabled',true);
			}
			else
			{
				$('#cbo_store_name_to').attr('disabled',true);
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
			var transfer_criteria=$( "#cbo_transfer_criteria" ).val();
			
			if(transfer_criteria==1)
			{
				if( form_validation('cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*cbo_store_name*txt_from_job*cbo_store_name_to*txt_to_job*cbo_item_desc*txt_transfer_qnty','Transfer Criteria*Company*Transfer Date*Store*From Order No*To Store*To Order No*Item Description*Transfered Qnty')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_transfer_date*cbo_store_name*txt_from_job*cbo_store_name_to*txt_to_job*cbo_item_desc*txt_transfer_qnty','Transfer Criteria*Company*Transfer Date*Store*From Order No*To Store*To Order No*Item Description*Transfered Qnty')==false )
				{
					return;
				}
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
			
			var item_desc=$('#cbo_item_desc').find(":selected").text().split("**");
			var item_lot=item_desc[0];
			var product_name_dtls=item_desc[1];
			//alert(item_lot+"="+product_name_dtls);return;
			//txt_system_id*update_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_job*txt_from_job_id*cbo_from_buyer_name*txt_from_style_ref*txt_to_job*txt_to_job_id*cbo_to_buyer_name*txt_to_style_ref*cbo_item_category*cbo_item_desc*txt_transfer_qnty*hide_transfer_qnty*cbo_uom*update_dtls_id*update_trans_issue_id*update_trans_recv_id

			 // Store upto validation start
			var store_update_upto=$('#store_update_upto').val()*1;
            var cbo_floor=$('#cbo_floor_to').val()*1;
            var cbo_room=$('#cbo_room_to').val()*1;
            var txt_rack=$('#txt_rack_to').val()*1;
            var txt_shelf=$('#txt_shelf_to').val()*1;
            var cbo_bin = $('#cbo_bin_to').val()*1; //no bin field on sweater yarn issue 
            // var cbo_bin=$('#cbo_bin').val()*1;

            if(store_update_upto > 1)
            {
                if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
                {
                    alert("Up To Bin Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
                {
                    alert("Up To Shelf Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
                {
                    alert("Up To Rack Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
                {
                    alert("Up To Room Value Full Fill Required For Inventory");return;
                }
                else if(store_update_upto==2 && cbo_floor==0)
                {
                    alert("Up To Floor Value Full Fill Required For Inventory");return;
                }
            }
            // Store upto validation End
			var dataString = "txt_system_id*update_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*txt_remarks*cbo_store_name*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*txt_from_job*txt_from_job_id*cbo_from_buyer_name*txt_from_style_ref*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*txt_to_job*txt_to_job_id*cbo_to_buyer_name*txt_to_style_ref*cbo_item_category*cbo_item_desc*txt_transfer_qnty*hide_transfer_qnty*cbo_uom*update_dtls_id*update_trans_issue_id*update_trans_recv_id*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin";
			var data="action=save_update_delete&operation="+operation+"&item_lot="+item_lot+"&product_name_dtls="+product_name_dtls+get_submitted_data_string(dataString,"../../");
			//alert (data);return;
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
				$('#txt_from_job').attr('disabled','disabled');
				$('#txt_to_job').attr('disabled','disabled');
				
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
		var company = $("#cbo_company_id").val();
		var job_no=$("#txt_from_job").val();
		var cbo_store_name=$("#cbo_store_name").val();
		get_php_form_data(job_no+"**"+prod_id+"**"+cbo_store_name+"**"+company, "populate_data_from_item_stock", "requires/yarn_order_to_order_transfer_controller" );
	}

	function to_company_value(comp)
	{
		var transfer_criteria=$( "#cbo_transfer_criteria" ).val();
		if(transfer_criteria != 1)
		{
			$("#cbo_company_id_to").val(comp);
		}
	}
	function load_all_dropdowns()
	{
		var company = $("#cbo_company_id").val();
		load_drop_down( 'requires/yarn_order_to_order_transfer_controller',company, 'load_drop_down_floor', 'floor_td' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_room', 'room_td' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_rack', 'rack_td' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_shelf', 'shelf_td' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_bin', 'bin_td' );
	}
	function load_all_dropdowns_to()
	{
		var company = $("#cbo_company_id").val();
		load_drop_down( 'requires/yarn_order_to_order_transfer_controller',company, 'load_drop_down_floor_to', 'floor_td_to' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_room_to', 'room_td_to' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_rack_to', 'rack_td_to' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_shelf_to', 'shelf_td_to' );
        load_drop_down( 'requires/yarn_order_to_order_transfer_controller', company, 'load_drop_down_bin_to', 'bin_td_to' );
	}
	function independence_basis_controll_function(data)
    {
        var status = return_global_ajax_value(data, 'upto_variable_settings', '', 'requires/yarn_order_to_order_transfer_controller').trim();
        $('#store_update_upto').val(status);
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
								<td colspan="3" align="right"><strong>Transfer System ID</strong></td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                                    <input type="hidden" name="update_id" id="update_id" />
								</td>
							</tr>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
                            	<td class="must_entry_caption">Transfer Criteria</td>
                                <td>
                                    <?
                                        echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,3,6,7');
                                    ?>
                                </td>
								<td class="must_entry_caption">Company</td>
								<td>
									<? 
									echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/yarn_order_to_order_transfer_controller*1', 'store','store_td', this.value,'','','','','','','','');load_room_rack_self_bin('requires/yarn_order_to_order_transfer_controller*1*cbo_store_name_to', 'store','to_store_td', this.value);to_company_value(this.value);independence_basis_controll_function(this.value);" );
									?>
								</td>
                                <td>To Company</td>
                                <td>
                                    <? 
                                        echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/yarn_order_to_order_transfer_controller*1*cbo_store_name_to', 'store','to_store_td', this.value);",1 );
                                        //load_drop_down( 'requires/yarn_transfer_controller', this.value, 'load_drop_down_store_to', 'store_td' );
                                    ?>
                                </td>
                            </tr>
							<tr>
								<td class="must_entry_caption">Transfer Date</td>
								<td>
									<input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
								</td> 
								<td>Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
								</td>
                                <td>Remarks </td>
                                <td>
                                    <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px;"  placeholder="remarks" />
                                </td>
							</tr>
						</table>
					</fieldset>
					<br>
					<table width="800" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
						<tr>
							<td width="49%" valign="top">
								<fieldset>
									<legend>From Style</legend>
									<table id="from_order_info"  cellpadding="0" cellspacing="1" width="100%">										
										<tr>
											<td width="30%" class="must_entry_caption">Store Name</td>
											<td id="store_td">
												<?
                                                echo create_drop_down("cbo_store_name", 162, $blank_array, "", 1, "-- Select Store --", 0, "", 0,'','','','','','',"cbo_store_name");
                                                ?>
                                            </td>
										</tr>
										<tr>
                               			 	<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Bin/Box</td>
											<td id="bin_td">
												<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                                        <tr>
											<td width="30%" class="must_entry_caption">Job No</td>
											<td>
												<input type="text" name="txt_from_job" id="txt_from_job" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
												<input type="hidden" name="txt_from_job_id" id="txt_from_job_id" readonly>
											</td>
										</tr>
										<tr>
											<td>Job Qnty</td>
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
												<tr style="display:none;">
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
													<tr style="display:none;">
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
												<legend>To Style</legend>					
												<table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
													
                                                    <tr>
                                                        <td width="30%" class="must_entry_caption">Store Name</td>
                                                        <td id="to_store_td">
                                                            <?
                                                            echo create_drop_down("cbo_store_name_to", 162, $blank_array, "", 1, "-- Select Store --", 0, "", 0,'','','','','','',"cbo_store_name_to");
                                                            ?>
                                                        </td>
                                                    </tr>
													<tr>
														<td>Floor</td>
														<td id="floor_td_to">
															<? echo create_drop_down( "cbo_floor_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
														<td>Room</td>
														<td id="room_td_to">
															<? echo create_drop_down( "cbo_room_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
														<td>Rack</td>
														<td id="rack_td_to">
															<? echo create_drop_down( "txt_rack_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
														<td>Shelf</td>
														<td id="shelf_td_to">
															<? echo create_drop_down( "txt_shelf_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
													<tr>
														<td>Bin Box</td>
														<td id="bin_td_to">
															<? echo create_drop_down( "cbo_bin_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
														</td>
													</tr>
                                                    <tr>
														<td width="30%" class="must_entry_caption">Job No</td>
														<td>
															<input type="text" name="txt_to_job" id="txt_to_job" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
															<input type="hidden" name="txt_to_job_id" id="txt_to_job_id" readonly>
														</td>
													</tr>
													<tr>
														<td>Job Qnty</td>
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
															<tr  style="display:none;">
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
																<tr style="display:none;">
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
                                                                        <input type="hidden" name="hide_item_description" id="hide_item_description" />	
																	</td>
																</tr>
																<tr>
																	<td class="must_entry_caption">Transfered Qnty</td>
																	<td>
																		<input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:150px;" />
																		<input type="hidden" name="hide_transfer_qnty" id="hide_transfer_qnty" />
																	</td>
																</tr>
																<tr>
																	<td>UOM</td>
																	<td>
																		<?
																		echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 1, "Select UOM", '', "",1 );
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
																	<td>Cumulative Stock Qty.</td>
																	<td><input class="text_boxes_numeric" type="text" name="txt_cum_issue_qnty" id="txt_cum_issue_qnty" style="width:150px;" placeholder="Display" readonly /></td>
																</tr>
																<tr>
																	<td>Total Transfered Qty.</td>
																	<td><input type="text" name="txt_tot_transfer_qnty" id="txt_tot_transfer_qnty" class="text_boxes_numeric" style="width:150px;" placeholder="Display" readonly /></td>
																</tr>
																<tr>
																	<td>Job Stcok Qty.</td>
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
														<input type="hidden" name="store_update_upto" id="store_update_upto">
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
