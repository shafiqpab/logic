<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Wise Purchase Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	30-10-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Wise Purchase Report", "../../../", 1, 1, $unicode, 1, 1);
?>
<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	function openmypage_item_account() {
		if (form_validation('cbo_company_name*cbo_item_category_id', 'Company Name*Category Item') == false) {
			return;
		}
		var data = document.getElementById('cbo_company_name').value + "_" + document.getElementById('cbo_item_category_id').value + "_" + document.getElementById("txt_item_group_id").value;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_wise_purchase_report_controller.php?action=item_account_popup&data=' + data, 'Item Account Popup', 'width=913px,height=450px,center=1,resize=0', '../../')

		emailwindow.onclose = function() {
			var theemail = this.contentDoc.getElementById("item_account_id");
			var theemailv = this.contentDoc.getElementById("item_account_val");
			var response = theemail.value.split('_');
			if (theemail.value != "") {
				freeze_window(5);
				document.getElementById("txt_item_account_id").value = response[0];
				document.getElementById("txt_item_acc").value = theemailv.value;
				//reset_form();
				get_php_form_data(response[0], "item_account_dtls_popup", "requires/item_wise_purchase_report_controller");
				release_freezing();
			}
		}
	}

	function company_onchange(com_id)
	{
		var com_all_data = return_global_ajax_value(com_id, 'com_wise_all_data', '', 'requires/item_wise_purchase_report_controller');
		var com_all_data_arr=com_all_data.split("**");
		var JSONObject_print_report = JSON.parse(com_all_data_arr[0]);
		$('#search').hide();
		$('#search2').hide();		 
		for (var key of Object.keys(JSONObject_print_report).sort())
		{
			if(JSONObject_print_report[key]==108){$('#search').show();}
			if(JSONObject_print_report[key]==195){$('#search2').show();}
			 
		}
	}

	function openmypage_item_group() {
		var data = document.getElementById('cbo_company_name').value + "_" + document.getElementById('cbo_item_category_id').value + "_" + document.getElementById('txt_item_group_id').value;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_wise_purchase_report_controller.php?action=item_group_popup&data=' + data, 'Item Group Popup', 'width=400px,height=370px,center=1,resize=0,scrolling=0', '../../')

		emailwindow.onclose = function() {
			/*var theemail=this.contentDoc.getElementById("item_name_id");
			var response=theemail.value.split('_');
			//alert (response[1]);
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById("txt_item_group_id").value=response[0];
				document.getElementById("txt_item_group").value=response[1];
				release_freezing();
			}*/

			var theform = this.contentDoc.forms[0] //("search_order_frm"); //Access the form inside the modal window
			var item_process_id = this.contentDoc.getElementById("hidden_item_process_id").value; //Access form field with id="emailfield"
			var item_process_name = this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_item_group_id').val(item_process_id);
			$('#txt_item_group').val(item_process_name);



		}
	}

	function generate_report(operation) {
		if (form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to', 'Company Name*Category Item*From Date* To Date') == false) {
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_product_id = $("#txt_product_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var txt_item_code = $("#txt_item_code").val();
		var cbo_supplier_name = $("#cbo_supplier_name").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		
		var dataString = "&cbo_company_name=" + cbo_company_name + "&cbo_store_name=" + cbo_store_name + "&cbo_item_category_id=" + cbo_item_category_id + "&txt_item_code=" + txt_item_code + "&txt_product_id=" + txt_product_id + "&from_date=" + from_date + "&to_date=" + to_date + "&item_account_id=" + item_account_id + "&item_group_id=" + item_group_id + "&cbo_supplier_name=" + cbo_supplier_name;

		if(operation==3){
			var data = "action=generate_report" + dataString;	
		}
		else if(operation==4){
			var data = "action=generate_report2" + dataString;	
		}
		
		//alert (data);
		freeze_window(operation);
		http.open("POST", "requires/item_wise_purchase_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function new_window() {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "650px";
	}

	function change_color(v_id, e_color) {
		if (document.getElementById(v_id).bgColor == "#33CC00") {
			document.getElementById(v_id).bgColor = e_color;
		} else {
			document.getElementById(v_id).bgColor = "#33CC00";
		}
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../../", $permission);  ?><br />
		<form name="itemwisepurchase_1" id="itemwisepurchase_1" autocomplete="off">
			<h3 style="width:1010px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:1010px">
				<fieldset>
					<table class="rpt_table" width="1010" cellpadding="0" cellspacing="0">
						<thead>
							<th width="130" class="must_entry_caption">Company</th>
							<th width="120" class="must_entry_caption">Item Category</th>
							<th width="90">Item Group</th>
							<th width="90">Item Account</th>
							<th width="120">Supplier</th>
							<th width="120">Store</th>
							<th class="must_entry_caption">Purchase Date</th>
							<th width="120"><input type="reset" name="res" id="res" value="Reset" style="width:120px" class="formbutton" onClick="reset_form('itemwisepurchase_1','report_container*report_container2','','','')" /></th>
						</thead>
						<tbody>
							<tr>
								<td>
									<?
									echo create_drop_down("cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/item_wise_purchase_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );company_onchange(this.value);");
									?>
								</td>
								<td>
									<?php
									echo create_drop_down("cbo_item_category_id", 120, $general_item_category, "", 1, "-- Select Category--", $selected, "");
									?>
									<input type="hidden" name="txt_product_id" id="txt_product_id" style="width:90px;" />
								</td>
								<td>
									<input style="width:90px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
									<input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;" />
								</td>
								<td>
									<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
									<input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;" />
								</td>
								<td width="120" id="supplier_td">
									<?
									echo create_drop_down("cbo_supplier_name", 120, $blank_array, "", 1, "--Select Supplier--", "", "");
									?>
								</td>
								<td width="120" id="store_td">
									<?
									echo create_drop_down("cbo_store_name", 120, $blank_array, "", 1, "--Select Store--", "", "");
									?>
								</td>
								<td>
									<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:66px;" />
									To
									<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:66px;" />
								</td>
								<td>
									<input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:60px" class="formbutton" />
									<input type="button" name="search" id="search2" value="Show2" onClick="generate_report(4)" style="width:57px" class="formbutton" />
								</td>
								 
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
							</tr>
						</tfoot>
					</table>
				</fieldset>
			</div>
			<br />
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script>
	set_multiselect('cbo_item_category_id', '0', '0', '0', '0');
	$("#multi_select_cbo_item_category_id a").click(function() {
		load_store();
	});

	function load_store() {
		var item = $("#cbo_item_category_id").val();
		var company = $("#cbo_company_name").val();
		var data = company+"_"+item;
		load_drop_down('requires/item_wise_purchase_report_controller', data, 'load_drop_down_store2', 'store_td');
	}
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>