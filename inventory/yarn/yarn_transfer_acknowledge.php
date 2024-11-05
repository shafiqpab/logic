<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Transfer Acknowledgement
				
Functionality	:	
JS Functions	:
Created by		: Tipu	
Creation date 	: 29-06-2020
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
echo load_html_head_contents("Trims Transfer Acknowledgement","../../", 1, 1, '','',''); 

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


function fnc_transfer_acknowledgement( operation )
{
	
	if(form_validation('cbo_company_id_to*cbo_store_name_to*challan_id*txt_transfer_date','Company*Store*Challan No.*Transfer Date')==false)
	{
		return;
	}
	var auto_transfer_receive=$('#auto_transfer_receive').val();

	

	if(operation==0 || operation==1)
	{  
		var cbo_company_id_to 		= $('#cbo_company_id_to').val();
		var cbo_company_id 			= $('#cbo_company_id').val();
		var cbo_store_name_to 		= $('#cbo_store_name_to').val();
		var cbo_transfer_criteria 	= $('#cbo_transfer_criteria').val();
		var txt_transfer_date 		= $('#txt_transfer_date').val();
		var txt_remarks 			= $('#txt_remarks').val();update_id
		var challan_id 				= $('#challan_id').val();
		var update_id 				= $('#update_id').val();
		var auto_transfer_receive   = $('#auto_transfer_receive').val();
		var store_update_upto       = $('#store_update_upto').val()*1;
		
		//alert(store_update_upto);
		var j=0; data_all=''; 
		var numRow = $('table#pay_dtls_table tbody tr').length; //alert(numRow);

		if (auto_transfer_receive != 1)
		{
			for (var i=1; i<=numRow; i++)
			{
				
				if(store_update_upto == 2)
				{
					if (form_validation('cbo_floor_to_'+i,'Floor')==false)
					{
						return;
					}
				}
				else if(store_update_upto == 3)
				{
					if (form_validation('cbo_floor_to_'+i+'*cbo_room_to_'+i,'Floor*Room')==false)
					{
						return;
					}
				}
				else if(store_update_upto == 4)
				{
					if (form_validation('cbo_floor_to_'+i+'*cbo_room_to_'+i+'*txt_rack_to_'+i,'Floor*Room*Rack')==false)
					{
						return;
					}
					
				}
				else if(store_update_upto == 5)
				{
					if (form_validation('cbo_floor_to_'+i+'*cbo_room_to_'+i+'*txt_rack_to_'+i+'*txt_shelf_to_'+i,'Floor*Room*Rack*Shelf')==false)
					{
						return;
					}
				}
				else if(store_update_upto == 6)
				{
					if (form_validation('cbo_floor_to_'+i+'*cbo_room_to_'+i+'*txt_rack_to_'+i+'*txt_shelf_to_'+i+'*cbo_bin_to_'+i,'Floor*Room*Rack*Shelf*Bin Box')==false)
					{
						return;
					}

					
				}
				
			}
		}
		

		$("#pay_dtls_table tbody tr").each(function()
		{
			var txtTransDate 		= $(this).find('input[name="txtTransDate[]"]').val();
			var fromCompanyId 		= $(this).find('select[name="fromCompanyId[]"]').val();
			var fromStoreId 		= $(this).find('select[name="fromStoreId[]"]').val();
			var txtDtlsID 			= $(this).find('input[name="txtDtlsID[]"]').val();
			var txtDtlsAcID 		= $(this).find('input[name="txtDtlsAcID[]"]').val();
			var txtTransID 			= $(this).find('input[name="txtTransID[]"]').val();
			var txtItemDesc 		= $(this).find('input[name="txtItemDesc[]"]').val();
			var productID 			= $(this).find('input[name="productID[]"]').val();
			var fromProductID 		= $(this).find('input[name="fromProductID[]"]').val();
			var colorID 			= $(this).find('input[name="colorID[]"]').val();
			var cboFloorTo 			= $(this).find('select[name="cboFloorTo[]"]').val();
			var cboRoomTo 			= $(this).find('select[name="cboRoomTo[]"]').val();
			var txtRackTo 			= $(this).find('select[name="txtRackTo[]"]').val();
			var txtShelfTo 			= $(this).find('select[name="txtShelfTo[]"]').val();
			var txtBinTo 			= $(this).find('select[name="txtBinTo[]"]').val();
			var hiddenTranQnty 		= $(this).find('input[name="hiddenTranQnty[]"]').val();
			var hiddenTranRate 		= $(this).find('input[name="hiddenTranRate[]"]').val();
			var hiddenTranValue 	= $(this).find('input[name="hiddenTranValue[]"]').val();
			var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
			var txtRemarks 			= $(this).find('input[name="txtRemarks[]"]').val();
			
			var hiddenStoreRate 	= $(this).find('input[name="hiddenStoreRate[]"]').val();
			var hiddenStoreValue 	= $(this).find('input[name="hiddenStoreValue[]"]').val();
			
			j++;

			data_all += "&txtTransDate_" + j + "='" + txtTransDate + "'&fromCompanyId_" + j + "='" + fromCompanyId + "'&fromStoreId_" + j + "='" + fromStoreId+ "'&txtDtlsID_" + j + "='" + txtDtlsID  + "'&txtDtlsAcID_" + j + "='" + txtDtlsAcID  + "'&txtTransID_" + j + "='" + txtTransID  + "'&txtItemDesc_" + j + "='" + txtItemDesc + "'&productID_" + j + "='" + productID+ "'&fromProductID_" + j + "='" + fromProductID+ "'&cboFloorTo_" + j + "='" + cboFloorTo + "'&cboRoomTo_" + j + "='" + cboRoomTo + "'&txtRackTo_" + j + "='" + txtRackTo + "'&txtShelfTo_" + j + "='" + txtShelfTo + "'&txtBinTo_" + j + "='" + txtBinTo + "'&hiddenTranQnty_" + j + "='" + hiddenTranQnty + "'&hiddenTranRate_" + j + "='" + hiddenTranRate + "'&hiddenTranValue_" + j + "='" + hiddenTranValue + "'&txtRemarks_" + j + "='" + txtRemarks +"'&cboUom_" + j + "='" + cboUom + "'&hiddenStoreRate_" + j + "='" + hiddenStoreRate +"'&hiddenStoreValue_" + j + "='" + hiddenStoreValue + "'";
		});

		var data="action=save_update_delete&operation="+operation+'&cbo_company_id_to='+cbo_company_id_to+'&cbo_company_id='+cbo_company_id+'&cbo_store_name_to='+cbo_store_name_to+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&txt_transfer_date='+txt_transfer_date+'&txt_remarks='+txt_remarks+'&challan_id='+challan_id+'&update_id='+update_id+'&numRow='+numRow+data_all;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","requires/yarn_transfer_acknowledge_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_transfer_acknowledgement_response;
	}
}



function fnc_transfer_acknowledgement_response()
{	
	if(http.readyState == 4) 
	{	  		
		var response=trim(http.responseText).split('**');		
		
		show_msg(response[0]); 	
			
		if(response[0]==0 || response[0]==1)
		{
			//alert(); return;
			//reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_location*cbo_company_id_to*cbo_location_to*txt_transfer_date*txt_challan_no*cbo_store_name*cbo_store_name_to');
			
			$("#update_id").val(response[1]);
			$("#txt_system_id").val(response[1]);
			//$("#txt_system_id").val(response[2]);
			$('#cbo_company_id_to').attr('disabled','disabled');
			$('#cbo_store_name_to').attr('disabled','disabled');
			$('#cbo_transfer_criteria').attr('disabled','disabled');
			$('#txt_challan_no').attr('disabled','disabled');
		
			show_list_view(response[1],'show_dtls_list_view_update','payDtlsTableTbody','requires/yarn_transfer_acknowledge_controller','');
			
			set_button_status(1, permission, 'fnc_transfer_acknowledgement',1,1);
		}
		else if(response[0]==20)
		{
			alert(response[1]);
			release_freezing();
			return;
		}	
		release_freezing();
	}
}

// ==============End Floor Room Rack Shelf Bin upto disable============
function storeUpdateUptoDisable() 
{
	var j=0; data_all=''; 
	var numRow = $('table#pay_dtls_table tbody tr').length; //alert(numRow);
	var store_update_upto=$('#store_update_upto').val()*1;

	for (var i=1; i<=numRow; i++)
	{
		alert(store_update_upto);
		if(store_update_upto==4)
		{
			
			$('#cbo_bin_to_1').prop("disabled", true);
			//$("#cbo_bin_to_"+i).prop("disabled", true);
			//$("#cbo_bin_to_"+i).val('pro');
		}
	}
	
	
		
	
}

function openmypage_challan()
{
	if (form_validation('cbo_company_id_to*cbo_transfer_criteria','Company*Transfer Criteria')==false)
	{
		return;
	}
	var company = $("#cbo_company_id_to").val();
	var store = $("#cbo_store_name_to").val();
	var transfer_criteria = $("#cbo_transfer_criteria").val();
	var page_link='requires/yarn_transfer_acknowledge_controller.php?action=itemTransfer_popup&company='+company+'&transfer_criteria='+transfer_criteria+'&store='+store;
	var title="Search Lc Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../')
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var transfer_data_str=this.contentDoc.getElementById("transfer_data_str").value;
		var transfer_data = transfer_data_str.split("_");
		var transfer_id = transfer_data[0];
		var store_id = transfer_data[1];
		if(transfer_id!='')
		{
			$('#challan_id').val(transfer_id);
			get_php_form_data(transfer_data_str, "populate_data_from_transfer_master", "requires/yarn_transfer_acknowledge_controller" );
			show_list_view(transfer_id,'show_dtls_list_view','payDtlsTableTbody','requires/yarn_transfer_acknowledge_controller','');	
		}
	}
}

function openmypage_systemId()
{
	var cbo_company_id_to = $('#cbo_company_id_to').val();

	if (form_validation('cbo_company_id_to','Company')==false)
	{
		return;
	}
	
	var title = 'Acknowledgement Info';	
	var page_link = 'requires/yarn_transfer_acknowledge_controller.php?cbo_company_id_to='+cbo_company_id_to+'&action=itemAcknowle_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transAcknowledgement','','','','','auto_transfer_receive');
		get_php_form_data(transfer_id, "populate_data_from_transfer_master_update", "requires/yarn_transfer_acknowledge_controller" );
		show_list_view(transfer_id,'show_dtls_list_view_update','payDtlsTableTbody','requires/yarn_transfer_acknowledge_controller','');
		set_button_status(1, permission, 'fnc_transfer_acknowledgement',1,1);
	}
}


function reset_room_rack_shelf(id,fieldName){

	if (fieldName=="cbo_store_name_to") 
	{
		var numRow = $('table#pay_dtls_table tbody tr').length; 
		for (var i = 1;numRow>=i; i++) {
			$("#cbo_floor_to_"+i).val('');
			$("#cbo_room_to_"+i).val('');
			$("#txt_rack_to_"+i).val('');
			$("#txt_shelf_to_"+i).val('');
			$("#cbo_bin_to_"+id).val('');
		}
	}
	else if (fieldName=="cbo_floor_to") 
	{
		$("#cbo_room_to_"+id).val('');
		$("#txt_rack_to_"+id).val('');
		$("#txt_shelf_to_"+id).val('');
		$("#cbo_bin_to_"+id).val('');
	}
	else if (fieldName=="cbo_room_to")  
	{
		$("#txt_rack_to_"+id).val('');
		$("#txt_shelf_to_"+id).val('');
		$("#cbo_bin_to_"+id).val('');
		
	}
	else if (fieldName=="txt_rack_to")  
	{
		$("#txt_shelf_to_"+id).val('');
		$("#cbo_bin_to_"+id).val('');
		
	}
	else if (fieldName=="txt_shelf_to")  
	{
		$("#cbo_bin_to_"+id).val('');
		
	}

}

function company_varriable_setting_auto_receive(to_company)
{
	var data='cbo_company_id_to='+to_company+'&action=varriable_setting_auto_receive';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("auto_transfer_receive").value = this.responseText;				
        }
    }
    xmlhttp.open("POST", "requires/yarn_transfer_acknowledge_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
}

function fnc_floor_load(datas){
	var data=datas.split("_");
	var storeId=data[0];
	var company=data[1];

	var numRow = $('table#pay_dtls_table tbody tr').length; 
	for (var i = 1;numRow>=i; i++) {
		var floor_tds="floor_td_to_"+i;
		load_drop_down( 'requires/yarn_transfer_acknowledge_controller',storeId+'_'+company+'_'+i, 'load_drop_down_floor_to', floor_tds );
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission);
		$i=1;
		?><br/>
        <fieldset style="width:1020px; margin-bottom:5px;">
           	<form name="transAcknowledgement" id="transAcknowledgement" autocomplete="off" method="POST"  >
           		<legend>Finish Fabric Transfer Acknowledgement</legend>
        		<br>
            	<fieldset style="width:900px;">
                <table width="880" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                	<tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            <input type="hidden" id="update_id" name="updateId[]" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company</td>
                        <td>
                        <?
                            echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/yarn_transfer_acknowledge_controller',this.value, 'load_drop_down_store_to', 'to_store_td' );company_varriable_setting_auto_receive(this.value);" );
                        ?> 
                        <input type="hidden" id="cbo_company_id" name="cbo_company_id[]" value="" />
                        <input type="hidden" id="auto_transfer_receive" name="auto_transfer_receive[]" value="" />
                        </td>
                        
                        <td class="must_entry_caption">Store</td>
                        <td id="to_store_td">
                        <?
                        echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "" );
                        ?>	
                        </td>

                        <td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"",'','1,2');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer ID</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;"  placeholder="Browse" onDblClick="openmypage_challan();" readonly />
                            <input type="hidden" id="challan_id" name="challan_id[]" value="" />
                        </td>
                        <td class="must_entry_caption">Acknowledge Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
                        <td>Remarks</td>
                        <td> <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px" /></td>
                    </tr>
                </table>
            </fieldset> 
            	<fieldset style="width:1005px;">
                    <table id="pay_dtls_table" width="1000" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Trans Date</th>
                                <th title="To Product ID">Prod. ID</th>
                                <th>Item Description</th>
                                <th>Quantity</th>
                                <th>UOM</th>
                                <th>From Company</th>
                                <th>From Store</th>
                                <th>Floor</th>
                                <th>Room</th>
                                <th>Rack</th>
                                <th>Shelf</th>
                                <th>Bin/Box</th>
                                <th>Remarks</th>
                            </tr>    
                        </thead>
                        <tbody id="payDtlsTableTbody">
                            <tr id="dtlsTbodyTr_1">
                                <td><input type="text" id="sl_1" name="sl[]" class="text_boxes"  style="width:20px" value="<?php echo $i; ?>"/></td>
                                <td>
                                	<input type="text" id="txtTransDate_1" name="txtTransDate[]" value=""  class="text_boxes"  style="width:56px" readonly />
                                </td>
                                <td>
                                	<input type="text" name="productID_1" id="productID[]" class="text_boxes" style="width:56px;" readonly " />
                                </td>
                                <td>
                                	<input type="text" name="txtItemDesc_1" id="txtItemDesc[]" class="text_boxes" style="width:120px;" readonly " />
                                </td>
                                <td>
                                    <input type="text"  id="txtTransQnty_1" name="txtTransQnty[]" class="text_boxes_numeric" style="width:70px;" readonly />
                                    <input type="hidden" name="hiddenTranQnty[]" id="hiddenTranQnty_1" readonly />
                                </td>
                                <td>
                                    <? echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,'', 0, "", 27, "",1,'',"","","","","","cboUom[]" );?>
                                </td>
                                <td>
                                    <? echo create_drop_down( "cbo_from_company_id", 100, $blank_array,'', 1, "--Select--", 0, "",1,'',"","","","","","fromCompanyId[]" );
                                    ?>
                                </td>
                                <td>
                                    <? echo create_drop_down( "cbo_from_store_id", 100, $blank_array,'', 1, "--Select--", 0, "",1,'',"","","","","","fromStoreId[]" );?>
                                </td>
                                <td id="floor_td">
                                    <? echo create_drop_down( "cbo_floor_to", 100,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboFloorTo[]" ); ?>
                                </td>
                                <td id="room_td">
                                	<? echo create_drop_down( "cbo_room_to", 100,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboRoomTo[]" ); ?>
                                </td>
                                <td id="rack_td">
                                	<? echo create_drop_down( "txt_rack_to", 100,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtRackTo[]" ); ?>
                                </td>
                                <td id="shelf_td">
                                	<? echo create_drop_down( "txt_shelf_to", 100,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtShelfTo[]" ); ?>
                                </td>
                                <td id="bin_td">
                                	<? echo create_drop_down( "cbo_bin_to", 100,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtBinTo[]" ); ?>
                                </td>
                                <td>
                                	<input type="text" id="txtRemarks_1" name="txtRemarks[]" value=""  class="text_boxes"  style="width:80px" readonly />
                                </td>
                               
                            </tr>
                        </tbody>    
                    </table>   
            	</fieldset>         
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr> 
                       	<td colspan="6" align="center">
                        	<span id="check_acc_intregation" style="color:crimson; font-family:'Comic Sans MS', cursive; font-weight:bold;"></span>
                      	</td>
                	</tr>
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                            <? 
                                echo load_submit_buttons( $permission, "fnc_transfer_acknowledgement", 0,0,"reset_form('transAcknowledgement','','','','','')",1);
                            ?>
							<input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
                       		<input type="hidden" name="store_update_upto_to" id="store_update_upto_to" readonly>
                        </td>
                   </tr> 
                </table>   
            </form>
        </fieldset> 
 	</div>
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
