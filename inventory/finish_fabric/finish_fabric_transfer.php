<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Transfer Entry

Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	27-06-2013
Updated by 		: 	Kausar (Creating Report)
Update date		: 	15-12-2013
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
echo load_html_head_contents("Finish Fabric Transfer Info","../../", 1, 1, '','','');

?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function active_inactive(str)
	{
		reset_form('transferEntry_1','div_transfer_item_list','','',"",'cbo_transfer_criteria');

		if(str==1)
		{
			$('#cbo_company_id_to').removeAttr('disabled','disabled');
			$('#cbo_location_to').removeAttr('disabled','disabled');
			$('#txt_to_order_no').val('').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_location_to').val('0').removeAttr('disabled','disabled');
			if(str==4)
			{
				$('#txt_from_order_no').val("").attr('disabled',false).attr('placeholder','Browse');
				$('#txt_from_order_id').val("");
				$('#txt_to_order_no').val("").attr('disabled',false).attr('placeholder','Browse');
				$('#txt_to_order_id').val("");
				$('#cbo_company_id_to').val('0').attr('disabled','disabled');
			// $('#cbo_location_to').val('0').attr('disabled','disabled');
		}
		else
		{
			$('#txt_from_order_no').val('').attr('disabled',true).attr('placeholder','Display');
			$('#txt_to_order_no').val('').attr('disabled','disabled').attr('placeholder','Display');
			$('#cbo_company_id_to').val('0').attr('disabled','disabled');
			$('#cbo_location_to').val('0').attr('disabled','disabled');

		}
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
	var page_link = 'requires/finish_fabric_transfer_controller.php?cbo_company_id='+cbo_company_id+'&action=itemTransfer_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=910px,height=420px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		var result = transfer_id.split('_');
		// alert(result[0]);return;
		reset_form('transferEntry_1','div_transfer_item_list','','');
		get_php_form_data(result[0], "populate_data_from_transfer_master", "requires/finish_fabric_transfer_controller" );
		show_list_view(result[0],'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_transfer_controller','');

		show_list_view(result[1],'show_transfer_requ_listview','div_transfer_requ_item_list','requires/finish_fabric_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_requisition_no()
{
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}

	var title = 'Item Transfer Requisition Info';
	var page_link = 'requires/finish_fabric_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=itemTransfer_requisition_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=910px,height=420px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		// alert(transfer_id);
		reset_form('transferEntry_1','div_transfer_requ_item_list','','');
		get_php_form_data(transfer_id, "populate_data_from_transfer_requ_master", "requires/finish_fabric_transfer_controller" );
		show_list_view(transfer_id,'show_transfer_requ_listview','div_transfer_requ_item_list','requires/finish_fabric_transfer_controller','');
		//set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_itemDescription()
{
	var transfer_criteria = $('#cbo_transfer_criteria').val()*1;
	var cbo_company_id = $('#cbo_company_id').val()*1;
	var cbo_store_name = $('#cbo_store_name').val()*1;
	var cbo_floor = $('#cbo_floor').val()*1;
	var cbo_room = $('#cbo_room').val()*1;
	var txt_rack = $('#txt_rack').val()*1;
	var txt_shelf = $('#txt_shelf').val()*1;
	var cbo_bin = $('#cbo_bin').val()*1;
	var txt_from_order_id = $('#txt_from_order_id').val();
	var txt_from_order_no = encodeURIComponent($('#txt_from_order_no').val());
	if(transfer_criteria==4)
	{
		if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name*txt_from_order_no','Transfer Criteria*Company*From store Name*From Order')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name','Transfer Criteria*Company*From store Name')==false)
		{
			return;
		}
	}


	var title = 'Item Description Info';
	var page_link = 'requires/finish_fabric_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&cbo_floor='+cbo_floor+'&cbo_room='+cbo_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&cbo_bin='+cbo_bin+'&transfer_criteria='+transfer_criteria+'&txt_from_order_no='+txt_from_order_no+'&txt_from_order_id='+txt_from_order_id+'&action=itemDescription_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_ref=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		var product_ref=encodeURIComponent(product_ref);
		//alert(product_id); +"**"+transfer_criteria
		get_php_form_data(product_ref+"__"+transfer_criteria, "populate_data_from_product_master", "requires/finish_fabric_transfer_controller" );

		if(transfer_criteria==1)// Company to Company
		{
			$('#cbo_store_name').attr('disabled','disabled');
			$('#cbo_floor').attr('disabled','disabled');
			$('#cbo_room').attr('disabled','disabled');
			$('#txt_rack').attr('disabled','disabled');
			$('#txt_shelf').attr('disabled','disabled');
			$('#cbo_bin').attr('disabled','disabled');
		}
		else
		{
			$('#cbo_store_name').attr('disabled','disabled');
			$('#cbo_floor').attr('disabled','disabled');
			$('#cbo_room').attr('disabled','disabled');
			$('#txt_rack').attr('disabled','disabled');
			$('#txt_shelf').attr('disabled','disabled');
			$('#cbo_bin').attr('disabled','disabled');

		}
	}
}



function calculate_value()
{
	var requisition_id = $('#txt_requisition_id').val()*1;
	if (requisition_id!="") // Requisition basis 5% (+5% between -5%) tolerance
	{
		var actual_qnty = $('#hidden_transfer_qnty').val()*1;
		var txt_transfer_qnty = $('#txt_transfer_qnty').val()*1;
		var current_stock = $('#txt_current_stock').val()*1;
		var toleranceIncr = (0.05*actual_qnty*1)+actual_qnty*1;
		var toleranceDecr = actual_qnty*1-(actual_qnty*1*0.05);
		//alert(txt_transfer_qnty+'*<=*'+toleranceIncr +'*&&*'+ txt_transfer_qnty+'*>=*'+toleranceDecr +'*&&*'+ txt_transfer_qnty+'*<=*'+current_stock);//return;

		// (txt_transfer_qnty<=toleranceIncr) || (txt_transfer_qnty>=toleranceDecr) || (txt_transfer_qnty<=current_stock)
		 //15*<=*15.75*&&*15*>=*14.25*&&*15*<=*50
		 if((txt_transfer_qnty<=toleranceIncr) && (txt_transfer_qnty>=toleranceDecr) && (txt_transfer_qnty<=current_stock))
		 {
		 	var txt_rate = $('#txt_rate').val()*1;
		 	var transfer_value=txt_transfer_qnty*txt_rate;
		 	$('#txt_transfer_value').val(transfer_value.toFixed(4));
		 }
		 else
		 {
		 	alert("Transfered Qnty can not be greater than Requisition Qnty.\nRequisition qnty. = " + actual_qnty);
		 	$('#txt_transfer_qnty').val('');
		 	$('#txt_transfer_value').val('');
		 }
		}
		else
		{
			var current_stock = $('#txt_current_stock').val()*1;
			var txt_transfer_qnty = $('#txt_transfer_qnty').val()*1;
			if(txt_transfer_qnty<=current_stock)
			{
				var txt_rate = $('#txt_rate').val()*1;

				var transfer_value=txt_transfer_qnty*txt_rate;
				$('#txt_transfer_value').val(transfer_value.toFixed(4));
			}
			else
			{
				alert("Stock not available.\nCurrent Stock = "+current_stock);
				$('#txt_transfer_qnty').val('');
				$('#txt_transfer_value').val('');
			}
		}

	}

	function openmypage_order(str)
	{
		var transfer_criteria = $('#cbo_transfer_criteria').val()*1;
		var cbo_company_id = $('#cbo_company_id').val()*1;
		if(transfer_criteria=="")
		{
			alert("Please Select Transfer Criteria");return;
		}
		if(str==2 && transfer_criteria==1)
		{
			var cbo_company_id_to = $('#cbo_company_id_to').val()*1;
			var cbo_store_name_to = $('#cbo_store_name_to').val()*1;
		if(cbo_company_id_to == cbo_company_id)// Company to Company
		{
			alert("Same Company Transfer is not allowed!!");
			$('#cbo_company_id_to').val('0');
			$('#cbo_location_to').val('0');
			$('#cbo_store_name_to').val('0');
			return;
		}

		if (form_validation('cbo_transfer_criteria*cbo_company_id_to*cbo_store_name_to','Transfer Criteria*Company To*To Store')==false)
		{
			return;
		}
	}
	else
	{
		var cbo_company_id_to = $('#cbo_company_id').val()*1;
		var cbo_store_name_to = $('#cbo_store_name').val()*1;
		if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_store_name','Transfer Criteria*Company From*From Store')==false)
		{
			return;
		}
	}

	var txt_item_desc = $('#txt_item_desc').val();
	var from_product_id = $('#from_product_id').val();
	if(str==2 && txt_item_desc =="")
	{
		alert("Please select item.");
		return;
	}


	var title = 'PO Info';
	var page_link = 'requires/finish_fabric_transfer_controller.php?cbo_company_id_to='+cbo_company_id_to+'&cbo_store_name_to='+cbo_store_name_to+'&popup_source='+str+'&action=po_search_popup';
	//alert(page_link);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value;
		var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value;
		var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
		var hidden_booking_id=this.contentDoc.getElementById("hidden_booking_id").value;
		//alert(hidden_order_no+"__"+hidden_order_id);
		if(str==2)
		{
			$("#txt_to_order_no").val(hidden_order_no);
			$("#txt_to_order_id").val(hidden_order_id);
			$("#hdn_to_booking_no").val(hidden_booking_no);
			$("#hdn_to_booking_id").val(hidden_booking_id);

			load_drop_down('requires/finish_fabric_transfer_controller',0+'_'+hidden_order_id+'_'+from_product_id+'_' +transfer_criteria +'_'+2, 'load_body_part', 'to_body_part' );
		}
		else
		{
			$("#txt_from_order_no").val(hidden_order_no);
			$("#txt_from_order_id").val(hidden_order_id);
		}
	}
}

function change_body_part(id)
{
	var transfer_criteria = $('#cbo_transfer_criteria').val()*1;
	if(transfer_criteria == 2)
	{
		if(id == "cbo_from_body_part")
		{
			$("#cbo_to_body_part").val($("#cbo_from_body_part").val());
		}
	}
}


function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		if($('#update_id').val()*1 !=0){
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_transfer_print", "requires/finish_fabric_transfer_controller" )
			return;
		}else{
			alert("Transfer System ID not found");return;
		}
	}
	if(operation==5)
	{
		if($('#update_id').val()*1 !=0){
			var report_title="Delivery Challan";
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_transfer_print_2", "requires/finish_fabric_transfer_controller" )
			return;
		}else{
			alert("Transfer System ID not found");return;
		}
	}
	else if(operation==6)
	{
		var trns_trit=$('#cbo_transfer_criteria').val();
		if(trns_trit==4)
		{
			if($('#update_id').val()*1 !=0){
				var report_title="Knit Finish Fabric Transfer Challan";
				print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_transfer_print_3", "requires/finish_fabric_transfer_controller" )
				return;
			}else{
				alert("Transfer System ID not found");return;
			}
		}
		else
		{
			alert("This print button only for Order to Order Criteria");
		}
	}
	else if(operation==7)
	{
		var trns_trit=$('#cbo_transfer_criteria').val();
		//if(trns_trit==1)

		if($('#update_id').val()*1 !=0){
			var report_title="Knit Finish Fabric Transfer Challan";
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_transfer_print_4", "requires/finish_fabric_transfer_controller" )
			return;
		}
		else
		{
			alert("Transfer System ID not found");return;
		}
	}
		// else
		// {
		// 	alert("This print button only for Company to Company Criteria");
		// }

	// if(operation==5)
	// {
	// 	if($('#update_id').val()*1 !=0){
	// 		var report_title="Delivery Challan";
	// 		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "finish_fabric_transfer_print_4", "requires/finish_fabric_transfer_controller" )
	// 		return;
	// 	}else{
	// 		alert("Transfer System ID not found");return;
	// 	}
	// }
	else if(operation==0 || operation==1 || operation==2)
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/


		if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_transfer_date*cbo_store_name*cbo_store_name_to*txt_item_desc*txt_transfer_qnty*txt_from_order_no*txt_to_order_no*cbo_from_body_part*cbo_to_body_part','Transfer Criteria*Company*Transfer Date*From Store*To Store*Item Description*Transfered Qnty*From Order*To Order*From Body Part*To Body Part')==false )
		{
			return;
		}

		if('<? echo chop(implode('*',$_SESSION['logic_erp']['mandatory_field'][14]),'*');?>')
		{
			if (form_validation('<? echo chop(implode('*',$_SESSION['logic_erp']['mandatory_field'][14]),'*');?>','<? echo chop(implode('*',$_SESSION['logic_erp']['mandatory_message'][14]),'*');?>')==false)
			{					
				return;
			}
		}

		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}

		if(operation!=2)
		{
			if($("#txt_transfer_qnty").val()*1>$("#hidden_current_stock").val()*1)
			{
				alert("Trasfer Quantity Can not be Greater Than Current Stock.");
				$("#txt_transfer_qnty").focus();
				return;
			}
		}

		var transfer_criteria=$('#cbo_transfer_criteria').val();
		if(transfer_criteria==1)
		{
			if($("#cbo_company_id").val()*1==$("#cbo_company_id_to").val()*1)
			{
				alert("Same Company Not Allow.");
				$("#cbo_company_id_to").focus();
				return;
			}
		}
		else if(transfer_criteria==2)
		{
			if($("#cbo_store_name").val()*1==$("#cbo_store_name_to").val()*1)
			{
				/*
				alert("Same Store Not Allow.");
				$("#cbo_store_name_to").focus();
				return;
				*/
			}
		}
		else
		{
			// if($("#txt_from_order_no").val()*1==$("#txt_to_order_no").val()*1)
			if($("#txt_from_order_id").val()*1==$("#txt_to_order_id").val()*1)
			{
				alert("Same Order Not Allow.");
				$("#txt_to_order_no").focus();
				return;
			}
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var txt_floor=$('#cbo_floor').val()*1;
		var txt_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var txt_bin=$('#cbo_bin').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0 || txt_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (txt_floor==0 || txt_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && txt_floor==0 || txt_room==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && txt_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}

		var store_update_upto_to=$('#store_update_upto_to').val()*1;
		var txt_floor_to=$('#cbo_floor_to').val()*1;
		var txt_room_to=$('#cbo_room_to').val()*1;
		var txt_rack_to=$('#txt_rack_to').val()*1;
		var txt_shelf_to=$('#txt_shelf_to').val()*1;
		var txt_bin_to=$('#cbo_bin_to').val()*1;
		
		if(store_update_upto_to > 1)
		{
			if(store_update_upto_to==6 && (txt_floor_to==0 || txt_room_to==0 || txt_rack_to==0 || txt_shelf_to==0 || txt_bin_to==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==5 && (txt_floor_to==0 || txt_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==4 && (txt_floor_to==0 || txt_room_to==0 || txt_rack_to==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==3 && txt_floor_to==0 || txt_room_to==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto_to==2 && txt_floor_to==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End

		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*cbo_location*cbo_location_to*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*cbo_store_name_to*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*cbo_bin_to*txt_transfer_qnty*txt_color*txt_rate*txt_transfer_value*hidden_transfer_value*cbo_uom*update_id*hide_color_id*from_product_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_from_prod_id*previous_to_prod_id*hidden_transfer_qnty*txt_item_desc*txt_from_order_id*txt_to_order_id*txt_to_order_no*batch_id*txt_remarks*cbo_fabric_shade*txt_batch_no*previous_from_batch_id*previous_to_batch_id*previous_to_order_id*previous_to_store*previous_to_company_id*cbo_from_body_part*cbo_to_body_part*txt_no_of_roll*hdn_to_booking_id*hdn_to_booking_no*txt_requisition_no*txt_requisition_id*hidden_requ_dtls_id*store_update_upto_to*store_update_upto";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");

		// alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_fabric_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_transfer_entry_response;
	}
}

function fnc_yarn_transfer_entry_response()
{
	if(http.readyState == 4)
	{
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			if (response[0]==2 && response[3]==1) // is mst delete reset form
			{
				reset_form('transferEntry_1','div_transfer_item_list','','','active_inactive(0);');
			}
			else
			{
				reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_location*cbo_company_id_to*cbo_location_to*txt_transfer_date*txt_challan_no*cbo_store_name*cbo_store_name_to*txt_requisition_no*txt_requisition_id*store_update_upto*store_update_upto_to');

				$("#update_id").val(response[1]);
				$("#txt_system_id").val(response[2]);
				show_list_view(response[1],'show_transfer_listview','div_transfer_item_list','requires/finish_fabric_transfer_controller','');
			}
			
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);

			disable_enable_fields('cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin',0);
			disable_enable_fields('cbo_company_id*cbo_transfer_criteria*cbo_location*cbo_company_id_to*cbo_location_to*txt_transfer_date*txt_challan_no*cbo_store_name*cbo_store_name_to*txt_requisition_no*txt_requisition_id',1);

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

function company_on_change(company)
{
	if (form_validation('cbo_transfer_criteria','cbo_transfer_criteria')==false) return;

	load_drop_down( 'requires/finish_fabric_transfer_controller',company, 'load_drop_down_location', 'from_location_td' );

	if($("#cbo_transfer_criteria").val() != 1)
	{
		$("#cbo_company_id_to").val(company);
		load_drop_down( 'requires/finish_fabric_transfer_controller',company, 'load_drop_down_location_to', 'to_location_td' );
	}

	var item_category = $('#cbo_item_category').val();
	page_link = 'cbo_company_id='+company+'&item_category='+item_category+'&action=requ_variable_settings';

	$.ajax({
		url: 'requires/finish_fabric_transfer_controller.php',
		type: 'POST',
		data: page_link,
		success: function (response)
		{
			var variable_settings = response.split("**");
			if (variable_settings[0]==1)
			{
				$('#txt_requisition_no').attr('disabled',false);
			}
			else
			{
				$('#txt_requisition_no').attr('disabled','disabled');
			}
			$('#store_update_upto').val(variable_settings[1]);
			if($("#cbo_transfer_criteria").val() != 1)
			{
				$('#store_update_upto_to').val(variable_settings[1]);
			}
		}
	});
}

function to_company_on_change(to_company)
{
	var fromCompan=$('#cbo_company_id').val();
	if($('#cbo_transfer_criteria').val()*1 == 2 || $('#cbo_transfer_criteria').val()*1 == 4)
	{
		alert('Both Company must be same for this Criteria');
		$('#cbo_company_id_to').val(fromCompan); return;
	}
	if($('#cbo_company_id').val()*1 == to_company && $('#cbo_transfer_criteria').val()*1 == 1 )
	{
		alert('Same Company Transfer is not allowed!!');
		$('#cbo_company_id_to').val('0'); return;
	}
	load_drop_down('requires/finish_fabric_transfer_controller',to_company, 'load_drop_down_location_to', 'to_location_td');

	var item_category = $('#cbo_item_category').val();
	page_link = 'cbo_company_id='+to_company+'&item_category='+item_category+'&action=requ_variable_settings';

	$.ajax({
		url: 'requires/finish_fabric_transfer_controller.php',
		type: 'POST',
		data: page_link,
		success: function (response)
		{
			var variable_settings = response.split("**");			
			$('#store_update_upto_to').val(variable_settings[1]);
		}
	});

}

function store_update_upto_disable() 
{
	var store_update_upto_to=$('#store_update_upto_to').val()*1;	 
	if(store_update_upto_to==4)
	{
		$('#txt_shelf_to').prop("disabled", true);
	}
	else if(store_update_upto_to==3)
	{
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);
	}
	else if(store_update_upto_to==2)
	{	
		$('#cbo_room_to').prop("disabled", true);
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);	
		$('#cbo_bin_to').prop("disabled", true);	
	}
	else if(store_update_upto_to==1)
	{
		$('#cbo_floor_to').prop("disabled", true);
		$('#cbo_room_to').prop("disabled", true);
		$('#txt_rack_to').prop("disabled", true);
		$('#txt_shelf_to').prop("disabled", true);	
		$('#cbo_bin_to').prop("disabled", true);	
	}
}

function fnc_item_blank()
{
	$('#from_product_id').val('');
	$('#txt_item_desc').val('');
	$('#batch_id').val('');
	$('#txt_batch_no').val('');
	$('#txt_color').val('');
	$('#hide_color_id').val('');
	$('#txt_from_order_no').val('');
	$('#txt_from_order_id').val('');
}

</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />
		<form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
			<div style="width:950px; float:left;" align="center">
				<fieldset style="width:950px;">
					<legend>Finish Fabric Transfer Entry</legend>
					<br>
					<fieldset style="width:900px;">
						<table width="880" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
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
									echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,4');
									?>
								</td>
								<td class="must_entry_caption">Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value)" );
									?>
								</td>
								<td>To Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change(this.value)",1 );
									?>
								</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Transfer Date</td>
								<td>
									<input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
								</td>

								<td width="100" class="must_entry_caption">Location</td>
								<td id="from_location_td">
									<?
									echo create_drop_down( "cbo_location", 160, $blank_array,"", 1, "--Select location--", 0, "" );
									?>
								</td>
								<td width="100" class="">To Location</td>
								<td id="to_location_td">
									<?
									echo create_drop_down( "cbo_location_to", 160, $blank_array,"", 1, "--Select location--", 0, "",1 );
									?>
								</td>
							</tr>
							<tr>
								<td>Challan No.</td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
								</td>
								<td>Item Category</td>
								<td>
									<?
									echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',2 );
									?>
								</td>
								<td>Requisition Basis</td>
								<td>
									<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:150px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_requisition_no();"  disabled="disabled"/>
									<input type="hidden" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width:150px" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br>
					<table width="910" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
						<tr>
							<td width="65%" valign="top">
								<div style="float: left; width:49%">
									<fieldset>
										<legend>From Store</legend>
										<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%" style="float: left;">
											<tr>
												<td width="100" class="must_entry_caption">From Store</td>
												<td id="from_store_td">
													<?
													echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" );
													?>
												</td>
											</tr>
											<tr>
												<td>Floor</td>
												<td id="floor_td">
													<? echo create_drop_down( "cbo_floor", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												</td>
											</tr>
											<tr>
												<td>Room</td>
												<td id="room_td">
													<? echo create_drop_down( "cbo_room", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												</td>
											</tr>
											<tr>
												<td>Rack</td>
												<td id="rack_td">
													<? echo create_drop_down( "txt_rack", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												</td>
											</tr>
											<tr>
												<td>Shelf</td>
												<td id="shelf_td">
													<? echo create_drop_down( "txt_shelf", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												</td>
											</tr>
											<tr>
												<td>Bin Box</td>
												<td id="bin_td">
													<? echo create_drop_down( "cbo_bin", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												</td>
											</tr>
											<tr>
												<td class="must_entry_caption">From Order</td>
												<td>
													<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px" onDblClick="openmypage_order(1);" placeholder="Display" disabled="disabled" />
													<input type="hidden" name="txt_from_order_id" id="txt_from_order_id" class="text_boxes" style="width:150px" />
												</td>
											</tr>
											<tr>
												<td class="must_entry_caption">Item Description</td>
												<td>
													<input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:150px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_itemDescription();" />
													<input type="hidden" name="from_product_id" id="from_product_id" value="" />
													<input type="hidden" name="batch_id" id="batch_id" value="" />
												</td>
											</tr>
											<tr>
												<td class="must_entry_caption">Body Part</td>
												<td id="from_body_td">
													<? echo create_drop_down( "cbo_from_body_part", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												</td>
												<input type="hidden" name="pre_from_body_part" id="pre_from_body_part" class="text_boxes" style="width:150px" />
											</tr>
											<tr>
												<td class="must_entry_caption">Transfered Qnty</td>
												<td>
													<input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:150px;" onblur="calculate_value( );" />
													<input type="hidden" name="hidden_requ_dtls_id" id="hidden_requ_dtls_id" readonly /></td>
												</tr>
												<tr>
													<td>No of Roll</td>
													<td>
														<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:150px" />
													</td>
												</tr>

												<tr>
													<td>Color</td>
													<td>
														<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:150px" disabled="disabled" />
														<input type="hidden" name="hide_color_id" id="hide_color_id" class="text_boxes" style="width:150px" disabled="disabled" />
													</td>
												</tr>
												<tr>
													<td>Fabric Shade</td>
													<td>
														<?
														echo create_drop_down( "cbo_fabric_shade", 160, $fabric_shade,"",1, "-- Select --", 0, "",1 );
														?>
													</td>
												</tr>
												<tr>
													<td>Batch No</td>
													<td>
														<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:150px" disabled="disabled" readonly />
													</td>
												</tr>
											</table>
										</fieldset>
									</div>
									<div style="float: right; width:49%">
										<fieldset>
											<legend>To Store</legend>
											<table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%" style="float: left;">
												<tr>
													<td width="100" class="must_entry_caption">To Store</td>
													<td id="to_store_td">
														<?
														echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "" );
														?>
													</td>
												</tr>
												<tr>
													<td>Floor</td>
													<td id="floor_td_to">
														<? echo create_drop_down( "cbo_floor_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													</td>
												</tr>
												<tr>
													<td>Room</td>
													<td id="room_td_to">
														<? echo create_drop_down( "cbo_room_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													</td>
												</tr>
												<tr>
													<td>Rack</td>
													<td id="rack_td_to">
														<? echo create_drop_down( "txt_rack_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													</td>
												</tr>
												<tr>
													<td>Shelf</td>
													<td id="shelf_td_to">
														<? echo create_drop_down( "txt_shelf_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													</td>
												</tr>
												<tr>
													<td>Bin Box</td>
													<td id="bin_td_to">
														<? echo create_drop_down( "cbo_bin_to", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													</td>
												</tr>
												<tr>
													<td class="must_entry_caption">To Order</td>
													<td>
														<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px" placeholder="Double Click To Search" onDblClick="openmypage_order(2);" readonly />
														<input type="hidden" name="txt_to_order_id" id="txt_to_order_id" class="text_boxes" style="width:150px" />
														<input type="hidden" name="previous_to_order_id" id="previous_to_order_id" />
														<input type="hidden" name="previous_to_store" id="previous_to_store" />
														<input type="hidden" name="previous_to_company_id" id="previous_to_company_id" />
														<input type="hidden" name="hdn_to_booking_no" id="hdn_to_booking_no" />
														<input type="hidden" name="hdn_to_booking_id" id="hdn_to_booking_id" />

													</td>
												</tr>
												<tr>
													<td class="must_entry_caption">Body Part</td>
													<td id="to_body_part">
														<? echo create_drop_down( "cbo_to_body_part", 160,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													</td>
													<input type="hidden" name="pre_to_body_part" id="pre_to_body_part" class="text_boxes" style="width:150px" />
												</tr>
												<tr>
													<td>Remarks</td>
													<td>
														<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:150px"  />
													</td>
												</tr>
											</table>
										</fieldset>
									</div>
								</td>
								<td width="1%" valign="top"></td>
								<td width="40%" valign="top">
									<fieldset>
										<legend>Display</legend>
										<table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >
											<tr>
												<td>Current Stock</td>
												<td>
													<input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px" disabled />
													<input type="hidden" name="hidden_current_stock" id="hidden_current_stock" readonly>
												</td>
											</tr>
											<tr>
												<td>Avg. Rate</td>
												<td><input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:150px" disabled /></td>
											</tr>
											<tr>
												<td>Transfer Value </td>
												<td>
												<input type="text" name="txt_transfer_value" id="txt_transfer_value" class="text_boxes_numeric" style="width:150px" disabled />
												<input type="hidden" name="hidden_transfer_value" id="hidden_transfer_value" class="text_boxes_numeric" />
												</td>
											</tr>
											<tr>
												<td>UOM</td>
												<td>
													<?
													echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 1, "-UOM-", 12, "",1,"" );

													?>
												</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="button_container" width="100%">
									<?
									echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','active_inactive(0);')",1);
									//disable_enable_fields(\'cbo_company_id\');
									?>
									<input type="button" id="print2" class="formbutton" style="width: 80px;"  onClick="fnc_yarn_transfer_entry(5)" name="print2" value="Print 2">
									<input type="button" id="print3" class="formbutton" style="width: 80px;"  onClick="fnc_yarn_transfer_entry(6)" name="print2" value="Print 3">
									<input type="button" id="print3" class="formbutton" style="width: 80px;"  onClick="fnc_yarn_transfer_entry(7)" name="print2" value="Print 4">

									<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly />
									<input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly />
									<input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly />

									<input type="hidden" name="previous_from_prod_id" id="previous_from_prod_id" readonly />
									<input type="hidden" name="previous_to_prod_id" id="previous_to_prod_id" readonly />
									<input type="hidden" name="hidden_transfer_qnty" id="hidden_transfer_qnty" readonly />
									<input type="hidden" name="previous_from_batch_id" id="previous_from_batch_id" readonly />
									<input type="hidden" name="previous_to_batch_id" id="previous_to_batch_id" readonly />
									<input type="hidden" name="store_update_upto" id="store_update_upto" readonly>
									<input type="hidden" name="store_update_upto_to" id="store_update_upto_to" readonly>

								</td>
							</tr>
						</table>
						<div style="width:880px;" id="div_transfer_item_list"></div>
					</fieldset>
				</div>
				<div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
				<div id="div_transfer_requ_item_list" style="max-height:500px; width:360px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
