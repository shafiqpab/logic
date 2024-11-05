<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Order To Order Transfer Info
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	16-04-2017
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
echo load_html_head_contents("Finish Fabric Order To Order Transfer Info","../../", 1, 1, '','',''); 

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
	var page_link = 'requires/finish_fabric_order_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=380px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/finish_fabric_order_to_order_roll_transfer_controller" );
		show_list_view(transfer_id+"**"+$('#txt_from_order_id').val(),'show_transfer_listview','tbl_details','requires/finish_fabric_order_to_order_roll_transfer_controller','');

		var row_num=$('#scanning_tbl tbody tr').length;
		var selected_roll_wgt=0;
		for (var j=1; j<=row_num; j++)
		{
			if($('#tbl_'+j).is(':checked'))
			{
				selected_roll_wgt += $('#rollWgt_'+j).val()*1;
			}
		}
		$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));

		setFilterGrid('scanning_tbl',-1);
		disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
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
	var page_link = 'requires/finish_fabric_order_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=955px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(order_id+"**"+type, "populate_data_from_order", "requires/finish_fabric_order_to_order_roll_transfer_controller" );
		if(type=='from')
		{
			show_list_view(order_id,'show_dtls_list_view','tbl_details','requires/finish_fabric_order_to_order_roll_transfer_controller','');
			setFilterGrid('scanning_tbl',-1);
		}
		
	}
}


function fnc_finish_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_from_order_id').val()+'*'+$('#txt_to_order_id').val(), "finish_fabric_order_to_order_transfer_print", "requires/finish_fabric_order_to_order_roll_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no','Company*Transfer Date*From Order No*To Order No')==false )
		{
			return;
		}
		
                 var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		var row_num=$('#scanning_tbl tbody tr').length-1;
		var txt_deleted_id=''; var selected_row=0; var i=0; var data_all=''; 
		
		for (var j=1; j<=row_num; j++)
		{
			var updateIdDtls=$('#dtlsId_'+j).val();
			
			if(updateIdDtls!="" && $('#tbl_'+j).is(':not(:checked)'))
			{
				var transIdFrom=$('#transIdFrom_'+j).val();
				var transIdTo=$('#transIdTo_'+j).val();
				var rolltableId=$('#rolltableId_'+j).val();
				var rollId=$('#rollId_'+j).val();
				

				
				selected_row++;
				if(txt_deleted_id=="") txt_deleted_id=updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId; 
				else txt_deleted_id+=","+updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId; 
			}
			
			if($('#tbl_'+j).is(':checked'))
			{
				i++;
				data_all+="&barcodeNo_" + i + "='" + $('#barcodeNo_'+j).val()+"'"+"&rollNo_" + i + "='" + $('#rollNo_'+j).val()+"'"+"&progId_" + i + "='" + $('#progId_'+j).val()+"'"+"&productId_" + i + "='" + $('#productId_'+j).val()+"'"+"&rollId_" + i + "='" + $('#rollId_'+j).val()+"'"+"&rollWgt_" + i + "='" + $('#rollWgt_'+j).val()+"'"+"&yarnLot_" + i + "='" + $('#yarnLot_'+j).val()+"'"+"&yarnCount_" + i + "='" + $('#yarnCount_'+j).val()+"'"+"&stichLn_" + i + "='" + $('#stichLn_'+j).val()+"'"+"&brandId_" + i + "='" + $('#brandId_'+j).val()+"'"+"&rack_" + i + "='" + $('#rack_'+j).val()+"'"+"&shelf_" + i + "='" + $('#shelf_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&machine_no_id_" + i + "='" + $('#machine_no_id_'+j).val()+"'"+"&batch_id_" + i+ "='"+ $('#batch_id_'+j).val()+"'"+"&dtlsId_" + i + "='" + $('#dtlsId_'+j).val()+"'"+"&transIdFrom_" + i + "='" + $('#transIdFrom_'+j).val()+"'"+"&transIdTo_" + i + "='" + $('#transIdTo_'+j).val()+"'"+"&rolltableId_" + i + "='" + $('#rolltableId_'+j).val()+"'"+"&transRollId_" + i + "='" + $('#transRollId_'+j).val()+"'"+"&reProcess_" + i + "='" + $('#reProcess_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'";
						
				selected_row++;
			}
		}
		
		if(selected_row<1)
		{
			alert("Please Select Barcode No.");
			return;
		}
		
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_from_order_id*txt_to_order_id*update_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+data_all;
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_order_to_order_roll_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_finish_transfer_entry_reponse;
	}
}

function fnc_finish_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		//alert(http.responseText);release_freezing();return;
                if(reponse[0]*1 == 20)
                {
                        alert(reponse[1]);
                        release_freezing();
                        return;
                } 
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			
			//show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_order_to_order_roll_transfer_controller','');
			show_list_view(reponse[1]+"**"+$('#txt_from_order_id').val(),'show_transfer_listview','tbl_details','requires/finish_fabric_order_to_order_roll_transfer_controller','');
			set_button_status(1, permission, 'fnc_finish_transfer_entry',1,1);
			disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
		}	
		release_freezing();
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
	var page_link = 'requires/finish_fabric_order_to_order_roll_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() 
			{
				//$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
				if($(this).css('display') == 'none')
				{
					$(this).find('input[name="check[]"]').attr('checked', false);
					
				}
				else
				{
					$(this).find('input[name="check[]"]').attr('checked', true);
				}
				
				
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		}

		var row_num=$('#scanning_tbl tbody tr').length;
		var selected_roll_wgt=0;
		for (var j=1; j<=row_num; j++)
		{	

			if($('#tbl_'+j).is(':checked'))
			{
				selected_roll_wgt += $('#rollWgt_'+j).val()*1;
			}
		}
		$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));
	}

	function show_selected_total(str)
	{
		var roll_wgt=0; var pre_wgt = 0;
		roll_wgt =$('#rollWgt_'+str).val()*1;
		pre_wgt = $("#selected_roll_wgt_show").text()*1;
		if($('#tbl_'+str).is(":checked"))
		{
			var total_roll_wgt=pre_wgt+roll_wgt;
			$("#selected_roll_wgt_show").text(total_roll_wgt.toFixed(2));
		}
		else
		{
			var total_roll_wgt=pre_wgt-roll_wgt;
			$("#selected_roll_wgt_show").text(total_roll_wgt.toFixed(2));
		}
	}
	
	function reset_form_all()
	{
		disable_enable_fields('cbo_company_id*txt_from_order_no*txt_to_order_no',0);
		reset_form('transferEntry_1','tbl_details','','','');
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:760px;">
        <legend>Roll Wise Finish Fabric Order To Order Transfer Entry</legend>
        <br>
            <fieldset style="width:750px;">
                <table width="740" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
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
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
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
            <table width="750" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>From Order</legend>
                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
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
                                        <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        <input type="button" class="formbutton" style="width:80px; display:none" value="View" onClick="openmypage_orderInfo('from');">
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
                                        <input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                        <input type="button" class="formbutton" style="width:80px; display:none" value="View" onClick="openmypage_orderInfo('to');">
                                    </td>
                                </tr>											
                            </table>                  
                       </fieldset>	
                    </td>
                </tr>
			</table>	
            <fieldset style="width:1040px;text-align:left">
				<table cellpadding="0" width="1020" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="30"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    	<th width="30">SL</th>
                        <th width="110">Batch No</th>
                        <th width="100">Barcode No</th>
                        <th width="70">Roll No</th>
                        <th width="80">Program No</th>
                        <th width="50">Product Id</th>
                        <th width="220">Fabric Description</th>
                        <th width="70">Machine No</th>
                        <th width="80">GSM</th>
                        <th width="55">Shelf</th>
                        <th>Roll Wgt.</th>
                    </thead>
                 </table>
                 <div style="width:1040px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1020" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="tbl_details">
                        </tbody>
                	</table>
                </div>
                <table cellpadding="0" cellspacing="0" width="1020" border="1" rules="all" class="rpt_table">
                	<tfoot>
                    	<tr>
                    		<th width="30"></th>
                    		<th width="30"></th>
	                        <th width="110"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="50"></th>
	                        <th width="220"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="55">Total</th>
	                        <th id="selected_roll_wgt_show">&nbsp;</th>
                    	</tr>

                    </tfoot>
                </table>
                <br>
                <table width="1020" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                            	 echo load_submit_buttons($permission, "fnc_finish_transfer_entry", 0,1,"reset_form_all()",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </fieldset>
	</form>
</div>   
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
