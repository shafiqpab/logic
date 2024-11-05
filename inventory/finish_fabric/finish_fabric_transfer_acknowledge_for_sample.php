<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Transfer Acknowledgement For Sample

Functionality	:
JS Functions	:
Created by		:	Tipu
Creation date 	: 	25-01-2019
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
echo load_html_head_contents("Knit Finish Fabric Transfer Acknowledgement For Sample","../../", 1, 1, '','','');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


	function fnc_transfer_acknowledgement( operation )
	{
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Receive Date Can not Be Greater Than Current Date");
			return;
		}
		if(form_validation('cbo_company_id_to*cbo_store_name_to*txt_challan_no*txt_transfer_date*txtItemDesc*txtTransQnty','Company*Store*Challan No.*Transfer Date*Description*Quantity')==false)
		{
			return;
		}

		if(operation==0 || operation==1)
		{
			var data_string="txt_system_id*update_id*cbo_company_id_to*cbo_location_to*cbo_store_name_to*txt_challan_no*challan_id*cbo_transfer_criteria*txt_transfer_date*cbo_item_category*txt_remarks*txtOrderNo*txtOrderID*txtItemDesc*productID*cbo_fabric_type*txtTransQnty*hidden_rcv_qnty*cbo_uom*update_trans_id*update_dtls_id*update_dtls_ac_id*update_prop_id*batch_id*color_id*item_rate*save_string*txt_deleted_trans_ids*from_trans_id";
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(data_string,"../../");

			freeze_window(operation);
			http.open("POST","requires/finish_fabric_transfer_acknowledge_for_sample_controller.php",true);
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
				$("#update_id").val(response[1]);
				$("#txt_system_id").val(response[1]);
				$('#pay_dtls_table').find('input,select').val('');
				show_list_view(response[1],'show_dtls_list_view_update','list_container','requires/finish_fabric_transfer_acknowledge_for_sample_controller','');
				show_list_view(response[2]+"**"+1,'show_dtls_list_view','transfer_item','requires/finish_fabric_transfer_acknowledge_for_sample_controller','');
				$('#cbo_store_name_to').attr('disabled','disabled');
				$('#txt_challan_no').attr('disabled','disabled');
				set_button_status(0, permission, 'fnc_transfer_acknowledgement',1,1);
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

	function openmypage_challan()
	{
		var cbo_company_id_to = $('#cbo_company_id_to').val();
		if (form_validation('cbo_company_id_to','Company')==false)
		{
			return;
		}
		var company  = $("#cbo_company_id_to").val();
		var location = $("#cbo_location_to").val();
		var store 	 = $("#cbo_store_name_to").val();
		var page_link= 'requires/finish_fabric_transfer_acknowledge_for_sample_controller.php?action=itemTransfer_popup&company='+company+'&location='+location+'&store='+store;
		var title 	 = "Transfer Challan List";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theform 	= this.contentDoc.forms[0];
			var transfer_id = this.contentDoc.getElementById("transfer_id").value;
			if(transfer_id!='')
			{
				$('#challan_id').val(transfer_id);
				get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/finish_fabric_transfer_acknowledge_for_sample_controller" );
				show_list_view(transfer_id+"**"+0,'show_dtls_list_view','transfer_item','requires/finish_fabric_transfer_acknowledge_for_sample_controller','');
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
		var page_link = 'requires/finish_fabric_transfer_acknowledge_for_sample_controller.php?cbo_company_id_to='+cbo_company_id_to+'&action=itemAcknowle_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value.split("_"); //Access form field with id="emailfield"
		//alert(transfer_id[0]+"="+transfer_id[1]);return;
		reset_form('transAcknowledgement','','','');
		get_php_form_data(transfer_id[0], "populate_data_from_transfer_master_update", "requires/finish_fabric_transfer_acknowledge_for_sample_controller" );
		//show_list_view(transfer_id,'show_dtls_list_view_update','payDtlsTableTbody','requires/finish_fabric_transfer_acknowledge_for_sample_controller','');
		show_list_view(transfer_id[0],'show_dtls_list_view_update','list_container','requires/finish_fabric_transfer_acknowledge_for_sample_controller','');
		show_list_view(transfer_id[1]+"**"+1,'show_dtls_list_view','transfer_item','requires/finish_fabric_transfer_acknowledge_for_sample_controller','');
		set_button_status(0, permission, 'fnc_transfer_acknowledgement',1,1);
	}
}

function openmypage_qnty()
{
	var cbo_company_id_to = $('#cbo_company_id_to').val();
	var store 	 = $("#cbo_store_name_to").val();
	var location = $("#cbo_location_to").val();
	var save_string = $("#save_string").val();
	var hidden_rcv_qnty = $("#hidden_rcv_qnty").val();

	if (form_validation('cbo_company_id_to*cbo_store_name_to*txt_challan_no*txtItemDesc','Company*Store*Challan No*Item Description')==false)
	{
		return;
	}

	var title = 'Quantity Popup';
	var page_link = 'requires/finish_fabric_transfer_acknowledge_for_sample_controller.php?cbo_company_id_to='+cbo_company_id_to+'&store='+store+'&location='+location+'&save_string='+save_string+'&hidden_rcv_qnty='+hidden_rcv_qnty+'&action=quantity_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_string=this.contentDoc.getElementById("save_string").value;
		var ac_qnty=this.contentDoc.getElementById("hidden_rcv_qnty").value;
		var deleted_trans_ids=this.contentDoc.getElementById("txt_deleted_trans_ids").value;

		$("#save_string").val(save_string);
		$("#txtTransQnty").val(ac_qnty);
		$("#txt_deleted_trans_ids").val(deleted_trans_ids);
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
		}
	}
	else if (fieldName=="cbo_floor_to")
	{
		$("#cbo_room_to_"+id).val('');
		$("#txt_rack_to_"+id).val('');
		$("#txt_shelf_to_"+id).val('');
	}
	else if (fieldName=="cbo_room_to")
	{
		$("#txt_rack_to_"+id).val('');
		$("#txt_shelf_to_"+id).val('');

	}
	else if (fieldName=="txt_rack_to")
	{
		$("#txt_shelf_to_"+id).val('');

	}

}

function fnc_floor_load(datas){

	var data=datas.split("_");
	var storeId=data[0];
	var company=data[1];
	var location=data[2];
	var floor_tds="floor_td";
	load_drop_down( 'requires/finish_fabric_transfer_acknowledge_for_sample_controller',storeId+'_'+company+'_'+location, 'load_drop_down_floor_to', floor_tds );
}

function set_form_data(prod_data,action_type)
{
	var save_string = $('#save_string').val();
	var prod_ref=prod_data.split("__");
	$('#pay_dtls_table').find('input,select').val('');

	if(save_string=="" && action_type==0){
		$('#productID').val(prod_ref[0]);
		$('#txtItemDesc').val(prod_ref[1]);
		$('#cbo_uom').val(prod_ref[2]);
		$('#txtOrderNo').val(prod_ref[3]);
		$('#txtOrderID').val(prod_ref[4]);
		$('#cbo_fabric_type').val(prod_ref[5]);
		$('#batch_id').val(prod_ref[9]);
		$('#color_id').val(prod_ref[10]);
		$('#item_rate').val(prod_ref[11]);
		$('#save_string').val(prod_ref[12]);
		$('#from_trans_id').val(prod_ref[15]);

		//$('#txtTransQnty').val(prod_ref[14]);
		$('#hidden_rcv_qnty').val(prod_ref[14]);

		if(prod_ref[13]!="")
		{
			set_button_status(1, permission, 'fnc_transfer_acknowledgement',1,1);
		}
		else
		{
			set_button_status(0, permission, 'fnc_transfer_acknowledgement',1,1);
		}
	}else{
		if(save_string!="" && action_type==0){
			$('#productID').val('');
			$('#txtItemDesc').val('');
			$('#cbo_uom').val('');
			$('#txtOrderNo').val('');
			$('#txtOrderID').val('');
			$('#cbo_fabric_type').val('');
			$('#batch_id').val('');
			$('#color_id').val('');
			$('#item_rate').val('');
			$('#save_string').val('');
			$('#from_trans_id').val('');
			$('#txtTransQnty').val('');
			$('#hidden_rcv_qnty').val('');

			set_button_status(0, permission, 'fnc_transfer_acknowledgement',1,1);
		}else{
			$('#productID').val(prod_ref[0]);
			$('#txtItemDesc').val(prod_ref[1]);
			$('#cbo_uom').val(prod_ref[2]);
			$('#txtOrderNo').val(prod_ref[3]);
			$('#txtOrderID').val(prod_ref[4]);
			$('#cbo_fabric_type').val(prod_ref[5]);
			$('#batch_id').val(prod_ref[9]);
			$('#color_id').val(prod_ref[10]);
			$('#item_rate').val(prod_ref[11]);
			$('#save_string').val(prod_ref[12]);
			$('#from_trans_id').val(prod_ref[15]);

			$('#txtTransQnty').val(prod_ref[14]);
			$('#hidden_rcv_qnty').val(prod_ref[14]);

			if(prod_ref[13]!="")
			{
				set_button_status(1, permission, 'fnc_transfer_acknowledgement',1,1);
			}
			else
			{
				set_button_status(0, permission, 'fnc_transfer_acknowledgement',1,1);
			}
		}
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);
		$i=1;
		?>
		<div style="width:900px; float:left; position:relative" align="center">
			<fieldset style="width:900px;">
				<form name="transAcknowledgement" id="transAcknowledgement" autocomplete="off" method="POST"  >
					<legend>Knit Finish Fabric Transfer Acknowledgement For Sample</legend>
					<br>
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
								echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/finish_fabric_transfer_acknowledge_for_sample_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );" );
								?>
								<input type="hidden" id="cbo_company_id" name="cbo_company_id[]" value="" />
							</td>
							<td class="">Location</td>
							<td id="to_location_td">
								<? echo create_drop_down( "cbo_location_to", 160, $blank_array,"", 1, "--Select Location--", 0, "",1 ); ?>
							</td>
							<td class="must_entry_caption">Store</td>
							<td id="to_store_td">
								<? echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select store--", 0, "" ); ?>
							</td>
						</tr>
						<tr>
							<td class="must_entry_caption">Challan No.</td>
							<td>
								<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;"  placeholder="Browse" onDblClick="openmypage_challan();" readonly />
								<input type="hidden" id="challan_id" name="challan_id[]" value="" />
							</td>
							<td>Transfer Criteria</td>
							<td>
								<? echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"",''); ?>
							</td>
							<td class="must_entry_caption">Transfer Date</td>
							<td>
								<input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" value="<? echo date("d-m-Y");?>" />
							</td>
						</tr>
						<tr>
							<td>Item Category</td>
							<td>
								<? echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',2 ); ?>
							</td>
							<td>Remarks</td>
							<td> <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px" /></td>
						</tr>
					</table>
					<br>
					<legend>Finish Fabric Transfer Details</legend>
					<br>
					<table width="880" cellspacing="2" cellpadding="2" border="0" id="pay_dtls_table" >
						<tr>
							<td width="100">Order</td>
							<td width="190">
								<input type="text" id="txtOrderNo" name="txtOrderNo[]" value=""  class="text_boxes"  style="width:140px" readonly />
								<input type="hidden" id="txtOrderID" name="txtOrderID[]" class="text_boxes" />
							</td>
							<td width="100" class="must_entry_caption">Item Description</td>
							<td width="190">
								<input type="text" id="txtItemDesc" name="txtItemDesc[]" class="text_boxes" style="width:140px;" readonly />
								<input type="hidden" id="productID" name="productID[]" value="" />
							</td>
							<td width="100">F. Shade</td>
							<td>
								<? echo create_drop_down( "cbo_fabric_type", 150, $fabric_shade,'', 1, "-- Select --", "", "",1,"","","","","","","cboFabricShade[]" );?>
							</td>
						</tr>

						<tr>
							<td>UOM</td>
							<td>
								<? echo create_drop_down( "cbo_uom", 150, $unit_of_measurement,'', 0, "", 12, "",1,"","","","","","","cboUom[]" );?>
							</td>
							<td class="must_entry_caption">Quantity</td>
							<td id="room_td">
								<input type="text" id="txtTransQnty" name="txtTransQnty[]" class="text_boxes_numeric" style="width:140px;" placeholder="Browse" onDblClick="openmypage_qnty();" readonly="readonly" />
								<input type="hidden" id="hidden_rcv_qnty" name="hidden_rcv_qnty[]" />
							</td>

							<input type="hidden" id="update_trans_id" name="update_trans_id[]" value="" />
							<input type="hidden" id="update_dtls_id" name="update_trans_id[]" value="" />
							<input type="hidden" id="update_dtls_ac_id" name="update_dtls_ac_id[]" value="" />
							<input type="hidden" id="update_prop_id" name="update_prop_id[]" value="" />
							<input type="hidden" id="batch_id" name="batch_id[]" value="" />
							<input type="hidden" id="color_id" name="color_id[]" value="" />
							<input type="hidden" id="item_rate" name="item_rate[]" value="" />
							<input type="hidden" id="save_string" name="save_string" value="" />
							<input type="hidden" id="txt_deleted_trans_ids" name="txt_deleted_trans_ids" value="" />
							<input type="hidden" id="from_trans_id" name="from_trans_id" value="" />
						</tr>

					</table>
					<br>
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

							</td>
						</tr>
					</table>
				</form>
			</fieldset>
			<fieldset>
				<div style="width:850px;" id="list_container"></div>
			</fieldset>
		</div>
		<div style="float:left; position:relative; margin-left:15px" align="left" id="transfer_item"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
