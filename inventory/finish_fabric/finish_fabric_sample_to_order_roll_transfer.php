<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Sample To Order Roll Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	MD.Didarul Alam
Creation date 	: 	10-06-2018
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
echo load_html_head_contents("Finish Fabric Sample To Order Roll Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

// Existing Data bring for updating 
function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/finish_fabric_sample_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=sampleToOrderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/finish_fabric_sample_to_order_roll_transfer_controller" );
		show_list_view(transfer_id+"**"+$('#txt_sam_book_id').val(),'show_transfer_listview','tbl_details','requires/finish_fabric_sample_to_order_roll_transfer_controller','');
	}
} 

// Sample browsing 
function fn_open_semple(type)
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Sample Information';	
	var page_link = 'requires/finish_fabric_sample_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=sample_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sample_id=this.contentDoc.getElementById("sample_id").value; //Access form field with id="emailfield"
		get_php_form_data(sample_id+"**"+type, "populate_data_from_sample", "requires/finish_fabric_sample_to_order_roll_transfer_controller" );
		if(type=='from')
		{
			show_list_view(sample_id,'show_dtls_list_view','tbl_details','requires/finish_fabric_sample_to_order_roll_transfer_controller','');
		}
	}
}



// Order browsing 
function openmypage_orderNo()
{
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/finish_fabric_sample_to_order_roll_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=955px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(order_id, "populate_data_from_order", "requires/finish_fabric_sample_to_order_roll_transfer_controller" );		
	}
}


// Save/update/print operation 
function fnc_finish_transfer_entry(operation)
{
	if(operation==4) // print Operation
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_order_to_order_transfer_print", "requires/finish_fabric_sample_to_order_roll_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_sam_book_id*txt_order_id','Company*Transfer Date*Sample Booking No*To Order No')==false )
		{
			return;
		}
		
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		var row_num=$('#scanning_tbl tbody tr').length;
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
				data_all+="&barcodeNo_" + i + "='" + $('#barcodeNo_'+j).val()+"'"+"&rollNo_" + i + "='" + $('#rollNo_'+j).val()+"'"+"&productId_" + i + "='" + $('#productId_'+j).val()+"'"+"&rollId_" + i + "='" + $('#rollId_'+j).val()+"'"+"&rollWgt_" + i + "='" + $('#rollWgt_'+j).val()+"'"+"&rack_" + i + "='" + $('#rack_'+j).val()+"'"+"&shelf_" + i + "='" + $('#shelf_'+j).val()+"'"+"&feb_description_id_" + i + "='" + $('#feb_description_id_'+j).val()+"'"+"&color_id_" + i + "='" + $('#color_id_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&dia_width_" + i + "='" + $('#dia_width_'+j).val()+"'"+"&dtlsId_" + i + "='" + $('#dtlsId_'+j).val()+"'"+"&transIdFrom_" + i + "='" + $('#transIdFrom_'+j).val()+"'"+"&transIdTo_" + i + "='" + $('#transIdTo_'+j).val()+"'"+"&rolltableId_" + i + "='" + $('#rolltableId_'+j).val()+"'"+"&transRollId_" + i + "='" + $('#transRollId_'+j).val()+"'";
						
				selected_row++;
			}
		}
		
		if(selected_row<1)
		{
			alert("Please Select Barcode No.");
			return;
		}
		
		var dataString = "txt_system_id*cbo_company_id*txt_transfer_date*txt_challan_no*txt_sam_book_id*txt_order_id*update_id";
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+data_all;
		//alert(data);return;
		
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_sample_to_order_roll_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_finish_transfer_entry_reponse;
	}
}

// Ajax Respons 
function fnc_finish_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		//alert(http.responseText);release_freezing();return;
                if (reponse[0] * 1 == 20 * 1) 
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
			$('#cbo_company_id').attr('disabled','disabled');
			
			show_list_view(reponse[1]+"**"+$('#txt_sam_book_id').val(),'show_transfer_listview','tbl_details','requires/finish_fabric_sample_to_order_roll_transfer_controller','');
			set_button_status(1, permission, 'fnc_finish_transfer_entry',1,1);
		}	
		release_freezing();
	}
}

function check_all(tot_check_box_id)
{
	if ($('#'+tot_check_box_id).is(":checked"))
	{ 
		$('#scanning_tbl tbody tr').each(function() {
			$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
		});
	}
	else
	{ 
		$('#scanning_tbl tbody tr').each(function() {
			$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
		});
	} 
}

</script>
</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:760px;">
        <legend>Roll Wise Finish Fabric Sample To Order Transfer Entry</legend>
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
                        <legend>From Sample</legend>
                            <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >	
                            	<tr>
                                    <td width="30%" class="must_entry_caption">SBWO No</td>
                                    <td>
                                        <input type="text" name="txt_sam_book_no" id="txt_sam_book_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="fn_open_semple('from');" readonly />
                                        <input type="hidden" name="txt_sam_book_id" id="txt_sam_book_id" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Booking Qnty.</td>
                                    <td>
                                        <input type="text" name="txt_sam_booking_qnty" id="txt_sam_booking_qnty" class="text_boxes_numeric" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
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
                                    <td height="19">Body Part</td>
                                    <td>
                                        <?  echo create_drop_down( "cbo_garments_item", 162, $body_part,"", 1, "--Select--", $selected, "",1 ); ?>	
                                    </td>
                                </tr>								
                            </table>                  
                       </fieldset>	
                    </td>
                    <td width="2%" valign="top"></td>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>To Order</legend>
                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
                                <tr>
                                    <td width="30%" class="must_entry_caption">Order No</td>
                                    <td>
                                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo();" readonly />
                                        <input type="hidden" name="txt_order_id" id="txt_order_id" readonly>
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Order Qnty</td>
                                    <td>
                                        <input type="text" name="txt_po_qnty" id="txt_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_gmts_item" id="txt_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_shipment_date" id="txt_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
			</table>	
            <fieldset style="width:920px;text-align:left">
				<table cellpadding="0" width="900" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
                    	<th width="40">SL</th>
                        <th width="80">Barcode No</th>
                        <th width="50">Roll No</th>
                        <th width="60">Product Id</th>
                        <th width="180">Fabric Description</th>
                        <th width="50">Color</th> 	
                        <th width="50">GSM</th>	
                        <th width="50">Dia</th>
                        <th width="55">Rack</th>
                        <th width="55">Shelf</th>
                        <th width="50">Roll Wgt.</th>
                    </thead>
                 </table>
                 <div style="width:920px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="900" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody id="tbl_details">
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="920" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? echo load_submit_buttons($permission, "fnc_finish_transfer_entry",0,1,"reset_form('transferEntry_1','tbl_details','','','disable_enable_fields(\'cbo_company_id\');')",1);
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
