<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Stock Ledger

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	24-08-2013
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
echo load_html_head_contents("Daily Yarn Stock", "../../../", 1, 1, $unicode, 1, 1);

?>

<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	function generate_report(type) {

		if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false) {
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_dyed_type = $("#cbo_dyed_type").val();
		var yarn_type_id = $("#txt_yarn_type_id").val();
		var txt_count = $("#txt_yarn_count_id").val();
		var txt_lot_no = $("#txt_lot_no").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var value_with = $("#cbo_value_with").val();
		//alert(value_with);//Tipu
		var store_wise = $("#cbo_store_wise").val();
		var store_name = $("#cbo_store_name").val();
		var txt_supplier = $("#txt_supplier_id").val();
		var cbo_get_upto = $("#cbo_get_upto").val();
		var txt_days = $("#txt_days").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		var txt_excange_rate = $("#txt_excange_rate").val();
		var txt_composition = $("#txt_composition").val();
		var txt_composition_id = $("#txt_composition_id").val();
		var source_name = $("#cbo_source_name").val();
		var txt_color = $("#txt_yarn_color_id").val();

		var lot_search_type = 0

		if (type == 18) {
			if (cbo_dyed_type != 2) {
				alert("This button only works for Non Dyed Yarn");
				return;
			}
		} else if (type == 19) {

			if (cbo_get_upto != 1) {
				alert('This button only works for Get Upto option: Greater Than.');
				return;
			} else if (txt_days * 1 <= 0) {
				alert("Please Insert Days.");
				return;
			}

		} else {

			if (cbo_get_upto != 0 && txt_days * 1 <= 0) {
				alert("Please Insert Days.");
				$("#txt_days").focus();
				return;
			}

			if (cbo_get_upto_qnty != 0 && txt_qnty * 1 <= 0) {
				alert("Please Insert Qty.");
				$("#txt_qnty").focus();
				return;
			}
		}

		if ($('#lot_search_type').is(":checked")) {
			lot_search_type = 1;
		}





		var show_val_column = '';
		if (type == 1 || type == 8 || type == 9 || type == 12 || type == 13 || type == 17) {
			var r = confirm("Press \"OK\" to open with Rate & value column\nPress \"Cancel\" to open without Rate & value column");
			if (r == true) {
				show_val_column = "1";
			} else {
				show_val_column = "0";
			}
		}

		var dataString = "&cbo_company_name=" + cbo_company_name + "&cbo_dyed_type=" + cbo_dyed_type + "&yarn_type_id=" + yarn_type_id + "&txt_count=" + txt_count + "&txt_lot_no=" + txt_lot_no + "&from_date=" + from_date + "&to_date=" + to_date + "&store_wise=" + store_wise + "&store_name=" + store_name + "&value_with=" + value_with + "&txt_supplier=" + txt_supplier + "&show_val_column=" + show_val_column + "&get_upto=" + cbo_get_upto + "&txt_days=" + txt_days + "&get_upto_qnty=" + cbo_get_upto_qnty + "&txt_qnty=" + txt_qnty + "&txt_excange_rate=" + txt_excange_rate + "&type=" + type + "&txt_composition=" + txt_composition + "&txt_composition_id=" + txt_composition_id + "&lot_search_type=" + lot_search_type + "&source_name=" + source_name + "&txt_color=" + txt_color;
		var data = "action=generate_report" + dataString;
		freeze_window(3);
		http.open("POST", "requires/daily_yarn_stock_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			/*var tableFilters = {
				col_0: "none",
				col_operation: {
					id: ["value_total_opening_balance","value_total_purchase","value_total_inside_return","value_total_outside_return","value_total_rcv_loan","value_total_total_rcv","value_total_issue_inside","value_total_issue_outside","value_total_receive_return","value_total_issue_loan","value_total_total_delivery","value_total_stock_in_hand","value_total_alocatted","value_total_free_stock"],
					col: [8,9,10,11,12,13,14,15,16,17,18,19,20,21],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body",-1,tableFilters);*/

			show_msg('3');
			release_freezing();
		}
	}


	function new_window() {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";

		//$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "350px";

		//$("#table_body tr:first").show();
	}

	function openmypage(prod_id, action) {
		var companyID = $("#cbo_company_name").val();
		var popup_width = '1200px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?companyID=' + companyID + '&prod_id=' + prod_id + '&action=' + action, 'Yarn Allocation Statement', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../../');
	}

	function openmypage_stock(prod_id, action) {
		//alert(prod_id);
		var popup_width = '750px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?prod_id=' + prod_id + '&action=' + action, 'Yarn Stock Details', 'width=' + popup_width + ', height=400px,center=1,resize=0,scrolling=0', '../../');
	}

	function openmypage_stock_upto(prod_id, action) {
		//alert(prod_id);
		var popup_width = '1250px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?prod_id=' + prod_id + '&action=' + action, 'Yarn Stock Details', 'width=' + popup_width + ', height=400px,center=1,resize=0,scrolling=0', '../../');
	}


	function openmypage_trans(prod_id, trans_type, store_name, from_date, to_date, action) {
		if (action = 'action_remarks') {
			action = 'action_remarks';
		} else {
			action = action;
		}
		var popup_width = '450px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?prod_id=' + prod_id + '&trans_type=' + trans_type + '&store_name=' + store_name + '&from_date=' + from_date + '&to_date=' + to_date + '&action=' + action, 'Yarn Transfer Details', 'width=' + popup_width + ', height=200px,center=1,resize=0,scrolling=0', '../../');
	}

	function openmypage_remarks(prod_id) {
		var popup_width = '450px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?prod_id=' + prod_id + '&action=mrr_remarks', 'Yarn Transfer Details', 'width=570px,height=350px,center=1,resize=0,scrolling=0', '../../');
	}

	function validate(e) {
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
		// control keys
		if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27))
			return true;
		// numbers
		else if ((("%").indexOf(keychar) > -1))
			return false;
		else
			return true;
	}

	$(document).ready(function() {
		$('#txt_composition').bind('copy paste cut', function(e) {
			e.preventDefault(); //disable cut,copy,paste
		});
	});

	function openmypage_composition() {
		var pre_composition_id = $("#txt_composition_id").val();
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?action=composition_popup&pre_composition_id=' + pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
			var composition_des = this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
			var composition_id = this.contentDoc.getElementById("hidden_composition_id").value;
			$("#txt_composition").val(composition_des);
			$("#txt_composition_id").val(composition_id);

		}
	}

	function openmypage_supplier() {
		/* if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		} */
		var companyID = $("#cbo_company_name").val();

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?action=supplier_popup&companyID=' + companyID, 'Supplier Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
			var supplier_des = this.contentDoc.getElementById("hidden_supplier").value; //Access form field with id="emailfield"
			var supplier_id = this.contentDoc.getElementById("hidden_supplier_id").value;
			$("#txt_supplier").val(supplier_des);
			$("#txt_supplier_id").val(supplier_id);

		}
	}

	function openmypage_yarn_type() {
		var companyID = $("#cbo_company_name").val();

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?action=yarn_type_popup&companyID=' + companyID, 'Yarn Type Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
			var yarn_type_des = this.contentDoc.getElementById("hidden_yarn_type").value; //Access form field with id="emailfield"
			var yarn_type_id = this.contentDoc.getElementById("hidden_yarn_type_id").value;
			$("#txt_yarn_type").val(yarn_type_des);
			$("#txt_yarn_type_id").val(yarn_type_id);

		}
	}

	function openmypage_yarn_count() {
		var companyID = $("#cbo_company_name").val();

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?action=yarn_count_popup&companyID=' + companyID, 'Yarn Count Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
			var yarn_count_des = this.contentDoc.getElementById("hidden_yarn_count").value; //Access form field with id="emailfield"
			var yarn_count_id = this.contentDoc.getElementById("hidden_yarn_count_id").value;
			$("#txt_yarn_count").val(yarn_count_des);
			$("#txt_yarn_count_id").val(yarn_count_id);

		}
	}

	function openmypage_yarn_color() {
		var companyID = $("#cbo_company_name").val();

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_report_controller.php?action=yarn_color_popup&companyID=' + companyID, 'Color Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0', '../../');

		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
			var yarn_color_des = this.contentDoc.getElementById("hidden_yarn_color").value; //Access form field with id="emailfield"
			var yarn_color_id = this.contentDoc.getElementById("hidden_yarn_color_id").value;
			$("#txt_yarn_color").val(yarn_color_des);
			$("#txt_yarn_color_id").val(yarn_color_id);

		}
	}


	function show_test_report(company_id, productID) {
		generate_report_file(company_id + '*' + productID, 'yarn_test_report', 'requires/daily_yarn_stock_report_controller');
	}

	function show_test_report2(company_id, productID) {
		generate_report_file(company_id + '*' + productID, 'yarn_test_report2', 'requires/daily_yarn_stock_report_controller');
	}

	function generate_report_file(data, action, page) {
		window.open("requires/daily_yarn_stock_report_controller.php?data=" + data + '&action=' + action, true);
	}

	function downloiadFile(id) {
		var title = 'Yarn Test New File Download';
		var page_link = 'requires/daily_yarn_stock_report_controller.php?action=get_yarn_test_file&id=' + id;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0', '');
		emailwindow.onclose = function() {}
	}
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="left">
		<? echo load_freeze_divs("../../../", $permission);  ?>
		<form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off">
			<div style="width:100%;" align="center">
				<h3 style="width:2220px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div style="width:100%;" id="content_search_panel">
					<fieldset style="width:2120px;">
						<table class="rpt_table" width="2170" cellpadding="0" cellspacing="0" border="1" rules="all">
							<thead>
								<tr>
									<th>Company</th>
									<th>Supplier</th>
									<th>Dyed Type</th>
									<th>Yarn Type</th>
									<th>Count</th>
									<th>Composition</th>
									<th>Color</th>
									<th>Lot<br><input type="checkbox" name="lot_search_type" id="lot_search_type" title="Lot Search start with"></th>
									<th>Value</th>
									<th class="must_entry_caption" colspan="2">Date</th>
									<th>Store Wise</th>
									<th>Store Name</th>
									<th>Source</th>
									<th>Get Upto</th>
									<th>Days</th>
									<th>Get Upto</th>
									<th>Qty.</th>
									<th> Ex.Rate</th>
									<th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
								</tr>
							</thead>
							<tr>
								<td>
									<?
									echo create_drop_down("cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", "id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/daily_yarn_stock_report_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_yarn_stock_report_controller' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/daily_yarn_stock_report_controller' );");
									?>
								</td>
								<td>

									<input type="text" id="txt_supplier" name="txt_supplier" class="text_boxes" style="width:150px" value="" onDblClick="openmypage_supplier();" placeholder="Browse" readonly />

									<input type="hidden" id="txt_supplier_id" name="txt_supplier_id" class="text_boxes" style="width:70px" value="" />

									<?
									/* 	echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0); */

									//echo create_drop_down( "cbo_supplier", 120, $blank_array,"",0, "--- Select Supplier ---", $selected, "",0);
									?>
								</td>
								<td align="center">
									<?
									$dyedType = array(0 => 'All', 1 => 'Dyed Yarn', 2 => 'Non Dyed Yarn');
									echo create_drop_down("cbo_dyed_type", 80, $dyedType, "", 0, "--Select--", $selected, "", "", "");
									?>
								</td>
								<td>
									<input type="text" id="txt_yarn_type" name="txt_yarn_type" class="text_boxes" style="width:80px" value="" onDblClick="openmypage_yarn_type();" placeholder="Browse" readonly />

									<input type="hidden" id="txt_yarn_type_id" name="txt_yarn_type_id" class="text_boxes" style="width:70px" value="" />
									<?
									//echo create_drop_down( "cbo_yarn_type", 80, $yarn_type,"", 1, "--Select--", 0, "",0 );
									// echo create_drop_down("cbo_yarn_type",130,$yarn_type,"",0, "-- Select --", $selected, "");
									?>
								</td>
								<td>
									<input type="text" id="txt_yarn_count" name="txt_yarn_count" class="text_boxes" style="width:120px" value="" onDblClick="openmypage_yarn_count();" placeholder="Browse" readonly />

									<input type="hidden" id="txt_yarn_count_id" name="txt_yarn_count_id" class="text_boxes" style="width:70px" value="" />
									<?
									// echo create_drop_down("cbo_yarn_count",120,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
									?>
								</td>
								<td>
									<!-- <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onKeyPress="return validate(event);" /> -->
									<input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />

									<input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value="" />
								</td>
								<td>
									<input type="text" id="txt_yarn_color" name="txt_yarn_color" class="text_boxes" style="width:80px" value="" onDblClick="openmypage_yarn_color();" placeholder="Browse" readonly />

									<input type="hidden" id="txt_yarn_color_id" name="txt_yarn_color_id" class="text_boxes" style="width:70px" value="" />
								</td>
								<td>
									<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
								</td>
								<td>
									<?
									$valueWithArr = array(0 => 'Value With 0', 1 => 'Value Without 0');
									echo create_drop_down("cbo_value_with", 110, $valueWithArr, "", 0, "", 1, "", "", "");
									//$field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400); ?>" class="datepicker" style="width:55px" readonly />
								</td>
								<td align="center">

									<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:55px" readonly />
								</td>
								<td>
									<?
									echo create_drop_down("cbo_store_wise", 50, $yes_no, "", 0, "--Select--", 2, "load_drop_down( 'requires/daily_yarn_stock_report_controller', document.getElementById('cbo_company_name').value+'**'+this.value, 'load_drop_down_store', 'store_td' );", 0);
									?>
								</td>
								<td id="store_td">
									<?
									echo create_drop_down("cbo_store_name", 100, $blank_array, "", 1, "-- All Store --", $storeName, "", 1);
									?>
								</td>
								<td>
									<?
									echo create_drop_down("cbo_source_name", 100, $source, "", 1, "--Select--", 0, "", "", "");
									?>
								</td>
								<td>
									<?
									$get_upto = array(1 => "Greater Than", 2 => "Less Than", 3 => "Greater/Equal", 4 => "Less/Equal", 5 => "Equal");
									echo create_drop_down("cbo_get_upto", 70, $get_upto, "", 1, "- All -", 0, "", 0);
									?>
								</td>
								<td>
									<input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
								</td>
								<td>
									<?
									echo create_drop_down("cbo_get_upto_qnty", 70, $get_upto, "", 1, "- All -", 0, "", 0);
									?>
								</td>
								<td>
									<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
								</td>
								<td>
									<input type="text" id="txt_excange_rate" name="txt_excange_rate" class="text_boxes_numeric" style="width:30px" value="" />
								</td>
								<td colspan="2">
									<input type="button" name="search" id="show_1" value="Show" onClick="generate_report(1)" style="width:60px;display:none;" class="formbutton" />
									<input type="button" name="search" id="show_2" value="Show 2" onClick="generate_report(9)" style="width:60px;display:none;" class="formbutton" />
									<input type="button" name="search" id="yarn_test" value="Yarn Test" onClick="generate_report(12)" style="width:60px;display:none;" class="formbutton" />
									<input type="button" name="search13" id="rack_wise" value="Rack Wise" onClick="generate_report(13)" style="width:60px;display:none;" class="formbutton" />
								</td>
							</tr>
							<tr>
								<td colspan="21">&nbsp;&nbsp;&nbsp;&nbsp;<? echo load_month_buttons(1); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="button" name="search" id="count_wise_summ" value="Count Wise Summ." onClick="generate_report(2)" style="width:100px;display:none;" class="formbutton" />
									<input type="button" name="search" id="type_wise_summ" value="Type Wise Summ." onClick="generate_report(3)" style="width:100px;display:none;" class="formbutton" />
									<input type="button" name="search" id="composition_wise_summ" value="Composition Wise Summ." onClick="generate_report(4)" style="width:140px;display:none;" class="formbutton" />
									<input type="button" name="search" id="composition_wise_summ2" value="Composition Wise Summ. 2" onClick="generate_report(16)" style="width:160px;display:none;" class="formbutton" />
									<input type="button" name="search" id="stock_only" value="Stock Only" onClick="generate_report(5)" style="width:80px;display:none;" class="formbutton" />
									<input type="button" name="search" id="mrr_wise_stock" value="MRR Wise Stock" onClick="generate_report(6)" style="width:100px; display:none;" class="formbutton" />
									<input type="button" name="search" id="count_type_wise_2" value="Count & Type Wise - 2" onClick="generate_report(7)" style="width:140px;display:none;" class="formbutton" />
									<input type="button" name="search" id="report_1" value="Report" onClick="generate_report(8)" style="width:90px;display:none;" class="formbutton" />

									<input type="button" name="search" id="stock_only2" value="Stock Only2" onClick="generate_report(10)" style="width:80px;display:none;" class="formbutton" />
									<input type="button" name="search" id="search10" value="Summary" onClick="generate_report(18)" style="width:80px;display:none;" class="formbutton" />

									<input type="button" name="search" id="count_composition" value="Count & Composition" onClick="generate_report(11)" style="width:120px;display:none;" class="formbutton" />
									<input type="button" name="search" id="count_composition_lot" value="Count & Composition & Lot" onClick="generate_report(19)" style="width:150px;display:none;" class="formbutton" />
									<input type="button" name="source_wise" id="source_wise" value="Source Wise" onClick="generate_report(14)" style="width:100px;display:none;" class="formbutton" />
									<input type="button" name="cc_wise" id="cc_wise" value="CC Wise Summery" onClick="generate_report(15)" style="width:100px;display:none;" class="formbutton" />
									<input type="button" name="rack_wise" id="rack_wise" value="Rack Wise 1" onClick="generate_report(17)" style="width:100px;display:none;" class="formbutton" />

								</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
			<br />
			<!-- Result Contain Start-->

			<div id="report_container" align="center"></div>
			<div id="report_container2" style="margin-left:5px"></div>

			<!-- Result Contain END-->


		</form>
	</div>
</body>
<script>
	//set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script>

</html>