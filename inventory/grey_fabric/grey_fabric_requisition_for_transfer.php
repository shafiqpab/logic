<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Requisition For Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Tipu 
Creation date 	: 	08-12-2019
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
echo load_html_head_contents("Grey Fabric Requisition For Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

	if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
	{
		return;
	}
	
	var title = 'Transfer Requisition Info';	
	var page_link = 'requires/grey_fabric_requisition_for_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=925px,height=380px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"

		reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*previous_from_prod_id*previous_to_prod_id*txt_stock*hide_trans_qty','','','');
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/grey_fabric_requisition_for_transfer_controller" );
		load_drop_down( 'requires/grey_fabric_requisition_for_transfer_controller', $('#txt_from_order_id').val(), 'load_drop_down_item_desc', 'itemDescTd' );
		show_list_view($('#txt_from_order_id').val(),'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_requisition_for_transfer_controller','');
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_requisition_for_transfer_controller','');
		disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*txt_from_order_no*txt_to_order_no*cbo_company_id_to', 1, '', '' );
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_company_id_to = $('#cbo_company_id_to').val();

	if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
	{
		return;
	}
	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_requisition_for_transfer_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&cbo_company_id_to='+cbo_company_id_to+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		var transfer_criteria = $("#cbo_transfer_criteria").val();
		get_php_form_data(order_id+"**"+type+"**"+transfer_criteria, "populate_data_from_order", "requires/grey_fabric_requisition_for_transfer_controller" );
		if(type=='from')
		{
			load_drop_down( 'requires/grey_fabric_requisition_for_transfer_controller', order_id, 'load_drop_down_item_desc', 'itemDescTd' );
			show_list_view(order_id,'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_requisition_for_transfer_controller','');
		}
	}
}


function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_transfer_requition_print", "requires/grey_fabric_requisition_for_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no*cbo_store_name*cbo_store_name_to*cbo_item_desc*txt_transfer_qnty','Transfer Criteria*Company*Transfer Date*From Order No*To Order No*From Store*To Store*Item Description*Transfered Qnty')==false )
		{
			return;
		}

		var store_update_upto=$('#store_update_upto').val()*1;
		var floor=$("#cbo_floor").val(); var room=$("#cbo_room").val(); var rack=$("#txt_rack").val(); var shelf=$("#txt_shelf").val(); var floorTo=$("#cbo_floor_to").val(); var roomTo=$("#cbo_room_to").val(); var rackTo=$("#txt_rack_to").val(); 
		var shelfTo=$("#txt_shelf_to").val();
		if(store_update_upto > 1)
		{
			if(store_update_upto==5 && (floor==0 || room==0 || rack==0 || shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");
				$("#txt_shelf").focus();return;
			}
			else if(store_update_upto==4 && (floor==0 || room==0 || rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");
				$("#txt_rack").focus();return;
			}
			else if(store_update_upto==3 && (floor==0 || room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");
				$("#cbo_room").focus();return;
			}
			else if(store_update_upto==2 && floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");
				$("#cbo_floor").focus();return;
			}

			if(store_update_upto==5 && (floorTo==0 || roomTo==0 || rackTo==0 || shelfTo==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");
				$("#txt_shelf_to").focus();return;
			}
			else if(store_update_upto==4 && (floorTo==0 || roomTo==0 || rackTo==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");
				$("#txt_rack_to").focus();return;
			}
			else if(store_update_upto==3 && (floorTo==0 || roomTo==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");
				$("#cbo_room_to").focus();return;
			}
			else if(store_update_upto==2 && floorTo==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");
				$("#cbo_floor_to").focus();return;
			}
		}

        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		if(($("#txt_transfer_qnty").val()*1 > $("#txt_stock").val()*1+$("#hide_trans_qty").val()*1)) 
		{
			alert("Transfered Quantity Exceeds Current Stock (Order) Quantity.");
			return;
		}

		var is_approved=$('#is_approved').val();		
		if(is_approved==1 || is_approved==3)
		{
			alert("Update not allowed. This Requisition is already Approved.");
			return;	
		}

        disable_enable_fields("cbo_transfer_criteria*cbo_company_id*txt_from_order_no*cbo_company_id_to*txt_to_order_no",1,"","");

		//var rack=$(this).find('select[name="txt_rack_to[]"]').val();

		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*cbo_ready_to_approved*txt_from_order_id*txt_to_order_id*cbo_item_category" +
                        "*cbo_item_desc*txt_transfer_qnty*txt_rate*txt_transfer_value*txt_roll*cbo_uom*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*txt_form_prog*txt_to_prog*update_id*update_dtls_id*previous_from_prod_id*previous_to_prod_id";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		// alert(data);return;

		freeze_window(operation);
		http.open("POST","requires/grey_fabric_requisition_for_transfer_controller.php",true);
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
		//alert(http.responseText);release_freezing();return;
        if (reponse[0] * 1 == 20 * 1) {
            alert(reponse[1]);
            release_freezing();
            return;
        }
                
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			reset_form('','','update_dtls_id*cbo_item_desc*txt_transfer_qnty*txt_roll*txt_ycount*hid_ycount*txt_ybrand*hid_ybrand*txt_ylot*stitch_length*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*previous_from_prod_id*previous_to_prod_id*txt_form_prog*txt_to_prog*txt_stock*hide_trans_qty','','','');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/grey_fabric_requisition_for_transfer_controller','');
			show_list_view($('#txt_from_order_id').val(),'show_dtls_list_view','list_fabric_desc_container','requires/grey_fabric_requisition_for_transfer_controller','');
			disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		}	
		release_freezing();
	}
}

function reset_form_all()
{
	$('#itemDescTd').html('<? echo create_drop_down( "cbo_item_desc", 300, $blank_array,'', 1, "--Select Item Description--", 0, "" ); ?>');
	disable_enable_fields('cbo_transfer_criteria*cbo_company_id*txt_from_order_no*txt_to_order_no',0);
	reset_form('transferEntry_1','div_transfer_item_list','','','');
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
	var page_link = 'requires/grey_fabric_requisition_for_transfer_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

function set_form_data(data)
{
	var data=data.split("**");
	$("#cbo_item_desc").val(data[0]);
	$("#txt_ycount").val(data[1]);
	$("#hid_ycount").val(data[2]);
	$("#txt_ybrand").val(data[3]);
	$("#hid_ybrand").val(data[4]);
	$("#txt_ylot").val(data[5]);
	$("#txt_rack").val(data[6]);
	$("#txt_shelf").val(data[7]);	
	$("#txt_form_prog").val(data[8]);
	$("#txt_to_prog").val(data[8]);
	$("#stitch_length").val(data[9]);
	$("#txt_rate").val(data[10]);	
	$("#cbo_store_name").val(data[11]);	
	$("#cbo_floor").val(data[12]);	
	$("#cbo_room").val(data[13]);	
	$("#txt_stock").val(data[14]);	
	//alert(data[12]);
	//populate_stock();
}

function populate_stock()
{
	var txt_order_id = $('#txt_from_order_id').val();
	var prod_id = $('#cbo_item_desc').val();
	var program_no = $('#txt_form_prog').val();
	var company_id = $('#cbo_company_id').val();
	get_php_form_data(txt_order_id+"**"+prod_id+"**"+program_no+"**"+company_id, "populate_data_about_order", "requires/grey_fabric_requisition_for_transfer_controller" );
}

function fnc_company_onchang(company_id)
{
	$("#cbo_company_id_to").val(company_id);	
	var item_category = 13;
	page_link = 'cbo_company_id='+company_id+'&item_category='+item_category+'&action=requ_variable_settings';

	$.ajax({
		url: 'requires/grey_fabric_requisition_for_transfer_controller.php',
		type: 'POST',
		data: page_link,
		success: function (response)
		{
			var variable_settings = response.split("**");
			//alert(variable_settings[0]+'='+variable_settings[1]);
			if(variable_settings[1]==2)
			{
				$('#store_update_upto').val(variable_settings[1]);
			}
			else
			{
				$('#store_update_upto').val("");
			}
		}
	});
}

function active_inactive(str)
{
	if(str==2) // Store to Store
	{
		$('#cbo_company_id_to').attr('disabled','disabled');	
		$('#txt_to_order_no').attr('disabled','disabled');	
	}
	else if(str==4) // Order to Order
	{
		$('#cbo_company_id_to').attr('disabled','disabled');
		$('#txt_to_order_no').removeAttr('disabled','disabled');
	}
	else
	{
		$('#cbo_company_id_to').removeAttr('disabled','disabled');
		$('#txt_to_order_no').removeAttr('disabled','disabled');
	}
	$('#txt_to_order_no').val('');
	$('#txt_to_order_id').val('');
	$('#txt_to_po_qnty').val('');
	$('#cbo_to_buyer_name').val('');
	$('#txt_to_style_ref').val('');
	$('#txt_to_job_no').val('');
	$('#txt_to_gmts_item').val('');
	$('#txt_to_shipment_date').val('');
}

function calculate_value()
{
	$('#txt_transfer_qnty').live('keyup keydown', function(e)
	{
	    var stock_qty = parseInt($('#txt_stock').val());
	    var transfered_qty = parseInt($('#txt_transfer_qnty').val());
	    if ($(this).val() > stock_qty && e.keyCode !== 46 && e.keyCode !== 8)
	    {
	       e.preventDefault();     
	       $(this).val('');
	       alert('Over Qty Not Alowed');
	    }
	});
	
	var txt_transfer_qnty = $('#txt_transfer_qnty').val()*1;
	var txt_rate = $('#txt_rate').val()*1;
	
	var transfer_value=txt_transfer_qnty*txt_rate;
	$('#txt_transfer_value').val(transfer_value.toFixed(4));
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:760px; float:left" align="center">   
            <fieldset style="width:760px;">
            <legend>Grey Fabric Requisition For Transfer Entry</legend>
            <br>
                <fieldset style="width:750px;">
                    <table width="740" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                        <tr>
                            <td colspan="3" align="right"><strong>Requisition System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                            <td colspan="3" align="left">
                                <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td class="must_entry_caption">Transfer Criteria</td>
	                        <td>
	                            <?
	                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,4');
	                            ?>
	                        </td>
                            <td class="must_entry_caption">From Company</td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0,"fnc_company_onchang(this.value);load_room_rack_self_bin('requires/grey_fabric_requisition_for_transfer_controller*13', 'store','from_store_td', this.value);load_drop_down( 'requires/grey_fabric_requisition_for_transfer_controller',this.value, 'load_drop_down_store_to', 'to_store_td' );" );
                                ?>
                            </td>
                            <td class="must_entry_caption">Requisition Date</td>
                            <td>
                                <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                            </td>
                        </tr>
                        <tr>                        	 
                            <td>Challan No.</td>
                            <td>
                                <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                            </td>
                            <td>Ready To Approved</td>
	                        <td>
								<? echo create_drop_down( "cbo_ready_to_approved", 160, $yes_no,"", 1, "-- Select--", 2, "","","" );?>
								<input type="hidden" name="store_update_upto" id="store_update_upto">
								<input type="hidden" name="is_approved" id="is_approved" value="">
	                        </td>
	                        <td></td>
	                        <td>
	                        	<span id="approved" style="text-align:center; font-size:24px; color:#FF0000;"></span> 
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
	                                    <td class="must_entry_caption">To Company</td>
	                                    <td>
			                                <? 
			                                    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/grey_fabric_requisition_for_transfer_controller',this.value, 'load_drop_down_store_to', 'to_store_td' );","" );
			                                ?>
			                            </td>
		                            <tr>                                	
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
                                            <input type="button" class="formbutton" style="width:80px" value="View" onClick="openmypage_orderInfo('to');">
                                        </td>
                                    </tr>											
                                </table>                  
                           </fieldset>	
                        </td>
                    </tr>
                    <tr>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>From Store</legend>
                        		<table>
                        			 <tr>
	                                	<td width="100" class="must_entry_caption">From Store</td>
	                                    <td>
	                                        <?
	                                           echo create_drop_down( "cbo_store_name", 152, "SELECT a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "",1 );
	                                        ?>	
	                                    </td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Floor</td>
										<td>
											<? echo create_drop_down( "cbo_floor", 152,"select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select--", 0, "",1 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Room</td>
										<td>
											<? echo create_drop_down( "cbo_room", 152,"select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select--", 0, "",1 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Rack</td>
										<td>
											<? echo create_drop_down( "txt_rack", 152,"select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select--", 0, "",1 ); ?>
										</td>
                           			 </tr>
                           			 <tr>
                           			 	<td>Shelf</td>
										<td>
											<? echo create_drop_down( "txt_shelf", 152,"select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select--", 0, "",1 ); ?>
										</td>
                           			 </tr>
                        		</table>
                            </fieldset>
                        </td>
                        <td width="2%" valign="top"></td>
                        <td width="49%" valign="top">
                            <fieldset>
                            <legend>To Store</legend>
                        		<table>
                        			 <tr>
	                                	<td width="100" class="must_entry_caption">To Store</td>
	                                    <td id="to_store_td">
	                                        <?
	                                           echo create_drop_down( "cbo_store_name_to", 152, $blank_array,"", 1, "--Select store--", 0, "" );
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
                        		</table>
                           </fieldset>	
                        </td>
                    </tr>	
                    <tr>
                        <td colspan="3">
                            <fieldset style="margin-top:10px">
                            <legend>Item Info</legend>
                                <table id="tbl_item_info" cellpadding="0" cellspacing="1" border="0" width="100%">										
                                    <tr>
                                        <td width="110">Item Category</td>
                                        <td>
                                            <? echo create_drop_down( "cbo_item_category", 135, $item_category,'', 0, '', '', '','1',13 ); ?>
                                        </td>
                                        <td width="80">Y/Count</td>
                                        <td>
                                            <input type="text" name="txt_ycount" id="txt_ycount" class="text_boxes" style="width:125px" placeholder="Display" readonly />
                                            <input type="hidden" name="hid_ycount" id="hid_ycount" class="text_boxes" style="width:80px"/>
                                        </td>
                                        <td width="60">Y/Brand</td>
                                        <td>
                                            <input type="text" name="txt_ybrand" id="txt_ybrand" class="text_boxes" style="width:125px" placeholder="Display" readonly />
                                            <input type="hidden" name="hid_ybrand" id="hid_ybrand" class="text_boxes" style="width:80px"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Item Description</td>
                                        <td colspan="3" id="itemDescTd">
                                            <? echo create_drop_down( "cbo_item_desc", 368, $blank_array,'', 1, "--Select Item Description--", 0, "",1 ); ?>	
                                        </td>
                                        <td >Y/Lot</td>
                                        <td>
                                            <input type="text" name="txt_ylot" id="txt_ylot" class="text_boxes" style="width:125px" placeholder="Display" readonly />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="must_entry_caption">Requisition Qty</td>
                                        <td width="150"><input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_value();" /> 
                                        <? echo create_drop_down( "cbo_uom", 60, $unit_of_measurement,'', 0, "", '', "",1,12 ); ?></td>
                                        <td>Avg. Rate</td>						
                                    	<td>
                                    		<input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:125px" disabled />
                                    	</td>
                                        <td>Transfer Value </td>
                                    	<td>
                                    		<input type="text" name="txt_transfer_value" id="txt_transfer_value" class="text_boxes_numeric" style="width:125px" disabled />
                                    	</td>
                                    </tr>
                                    <tr>
                                        <td>Roll</td>
                                        <td>
                                           <input type="text" name="txt_roll" id="txt_roll" class="text_boxes_numeric" style="width:125px"   />
                                        </td>
                                        <td>From Prog</td>
                                        <td>
                                           <input type="text" name="txt_form_prog" id="txt_form_prog" class="text_boxes_numeric" style="width:125px"  placeholder="Display" readonly  />
                                        </td>
                                        <td>To Prog</td>
                                        <td>
                                            <input type="text" name="txt_to_prog" id="txt_to_prog" class="text_boxes" style="width:125px" />
                                        </td>
                                    </tr>
                                    <tr>                                        
                                        <td>Stitch Length</td>
                                        <td><input type="text" name="stitch_length" id="stitch_length" class="text_boxes" style="width:125px" placeholder="Display" readonly /></td>
                                        <td>Current Stock (Order)</td>
                                        <td>
                                           <input type="text" name="txt_stock" id="txt_stock" class="text_boxes_numeric" style="width:125px"  placeholder="Display" readonly  />
                                           <input type="hidden" name="hide_trans_qty" id="hide_trans_qty"/>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr> 	
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form_all()",1);
                            ?>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
                            <input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
                            <input type="hidden" name="previous_from_prod_id" id="previous_from_prod_id" readonly>
                            <input type="hidden" name="previous_to_prod_id" id="previous_to_prod_id" readonly>
                        </td>
                    </tr>
                </table>
                <div style="width:740px;" id="div_transfer_item_list"></div>
            </fieldset>
        </div>
        <div id="list_fabric_desc_container" style="width:550px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
	</form>
</div>  
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
