<?php

/*********************************************** Comments *************************************
 *	Purpose			: 	This Form Will Create Requisition  against demand status For Sales
 *	Functionality	:
 *	JS Functions	:
 *	Created by		:	Jahid
 *	Creation date 	: 	22-10-2017
 *	Updated by 		:
 *	Update date		:
 *	QC Performed BY	:
 *	QC Date			:
 *	Comments		:
 ************************************************************************************************/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Requisition  against demand status Report", "../../", 1, 1, '', 1, 1);
?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	var tableFilters = {
		col_0: "none",
		col_operation: {
			id: ["value_tot_progs", "value_total_rec_qty", "value_tot_demand", "value_tot_balance2", "value_tot_issue", "value_tot_iss_balance_qnty", "value_tot_returnable_qnty", "value_tot_production_qnty", "value_tot_return_qnty"],
			col: [18, 19, 20, 21, 22, 23, 24, 25, 26],
			operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
			write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
		}
	}

	function fnc_generate_report() {
		var sales_order_no = document.getElementById('txt_sales_order_no').value;
		var fab_booking_no = document.getElementById('txt_fab_booking_no').value;
		var prog_no = document.getElementById('txt_prog_no').value;
		var req_no = document.getElementById('txt_req_no').value;
		var date_from_requ = document.getElementById('txt_date_from_requ').value;
		var date_to_requ = document.getElementById('txt_date_to_requ').value;
		var ir_no = document.getElementById('txt_ir_no').value;
		var date_from = document.getElementById('txt_date_from').value;
		var date_to = document.getElementById('txt_date_to').value;

		if (sales_order_no == "" && fab_booking_no == "" && prog_no == "" && req_no == "" && ir_no == "" && (date_from_requ == "" || date_to_requ == "") && (date_from == "" || date_to == "")) {
			if (form_validation('cbo_company_name*txt_date_from_requ*txt_date_to_requ', 'Company*Requisition From Date*Requisition To Date') == false) {
				return;
			}
		} else {
			if (form_validation('cbo_company_name', 'Company') == false) {
				return;
			}
		}

		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sales_order_no*txt_fab_booking_no*txt_prog_no*txt_req_no*txt_date_from_requ*txt_date_to_requ*txt_date_from*txt_date_to*cbo_get_upto_qnty*txt_qnty*cbo_knitting_source*cbo_year_selection*txt_ir_no', "../../");
		//alert(data);return;
		freeze_window('3');
		http.open("POST", "requires/requisition_against_demand_status_for_sales_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

	}

	function fn_report_generated_reponse() {
		if (http.readyState == 4) {
			//alert(http.responseText);
			show_msg('3');
			var response = trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);

			//document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('report_container').innerHTML = '<a href="' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>' + '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert(tableFilters);
			setFilterGrid("tbl_list_search", -1, tableFilters);

			release_freezing();
		}
	}

	function fnc_generate_report_xl() {
		var sales_order_no = document.getElementById('txt_sales_order_no').value;
		var fab_booking_no = document.getElementById('txt_fab_booking_no').value;
		var prog_no = document.getElementById('txt_prog_no').value;
		var req_no = document.getElementById('txt_req_no').value;
		var date_from_requ = document.getElementById('txt_date_from_requ').value;
		var date_to_requ = document.getElementById('txt_date_to_requ').value;
		var ir_no = document.getElementById('txt_ir_no').value;
		var date_from = document.getElementById('txt_date_from').value;
		var date_to = document.getElementById('txt_date_to').value;

		if (sales_order_no == "" && fab_booking_no == "" && prog_no == "" && req_no == "" && ir_no == "" && (date_from_requ == "" || date_to_requ == "") && (date_from == "" || date_to == "")) {
			if (form_validation('cbo_company_name*txt_date_from_requ*txt_date_to_requ', 'Company*Requisition From Date*Requisition To Date') == false) {
				return;
			}
		} else {
			if (form_validation('cbo_company_name', 'Company') == false) {
				return;
			}
		}

		var data = "action=report_generate_xl" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sales_order_no*txt_fab_booking_no*txt_prog_no*txt_req_no*txt_date_from_requ*txt_date_to_requ*txt_date_from*txt_date_to*cbo_get_upto_qnty*txt_qnty*cbo_knitting_source*cbo_year_selection*txt_ir_no', "../../");
		//alert(data);return;
		freeze_window('3');
		http.open("POST", "requires/requisition_against_demand_status_for_sales_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse_xl;

	}

	function fn_report_generated_reponse_xl() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split("####");
			// alert(reponse[0]);
			if (reponse != '') {
				$('#aa1').removeAttr('href').attr('href', 'requires/' + reponse[0]);
				document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
		// if(http.readyState == 4)
		// {
		// 	//alert(http.responseText);
		// 	show_msg('3');
		// 	var response=trim(http.responseText).split("####");
		// 	$('#report_container2').html(response[0]);

		// 	//document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		// 	document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		// 	//alert(tableFilters);
		// 	setFilterGrid("tbl_list_search",-1,tableFilters);

		// 	release_freezing();
		// }
	}

	function new_window() {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "380px";
		$('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}

	function openmypage_issue(requ_id, prod_id, action, date_from, date_to) {
		popup_width = '490px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/requisition_against_demand_status_for_sales_controller.php?requ_id=' + requ_id + '&prod_id=' + prod_id + '&action=' + action + '&date_from=' + date_from + '&date_to=' + date_to, 'Detail Veiw', 'width=' + popup_width + ', height=380px,center=1,resize=0,scrolling=0', '../');
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<form id="requsitionDemandnReport_1">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", '');  ?>
			<h3 style="width:1490px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:1490px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th class="must_entry_caption">Company Name</th>
							<th> Knitting Source </th>
							<th> Buyer Name </th>

							<!-- <th>File No</th>
                    <th>Ref. No</th> -->
							<th>Sales Order No</th>
							<th>IR/IB</th>
							<th>Fabric Booking No</th>
							<th>Program No</th>
							<th>Req. No</th>
							<th colspan="2" class="must_entry_caption">Requisition Date</th>
							<th colspan="2" class="must_entry_caption">Demand Date</th>
							<th>Get Upto</th>
							<th>Qty.</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requsitionDemandnReport_1','','','','')" class="formbutton" style="width:70px" /></th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<? echo create_drop_down("cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/requisition_against_demand_status_for_sales_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
									<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
								</td>

								<td>
									<?
									echo create_drop_down("cbo_knitting_source", 150, $knitting_source, "", 1, "-- Select --", $selected, "", "", '1,3');
									?>
								</td>

								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
									?>
								</td>

								<!-- <td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>
                        <td>

                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Write" />  </td> -->
								<!--  new dev -->
								<td><input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:100px;" placeholder="Write Full Order" /></td>

								<td><input type="text" name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:100px;" placeholder="Write " /></td>

								<td>
									<input type="text" name="txt_fab_booking_no" id="txt_fab_booking_no" class="text_boxes" style="width:100px" placeholder="Write Full Booking" />
								</td>

								<td>



									<input type="text" name="txt_prog_no" id="txt_prog_no" class="text_boxes" style="width:100px" placeholder="Write Prog." />

								</td>
								<td><input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>


								<td><input name="txt_date_from" id="txt_date_from_requ" class="datepicker" style="width:70px" placeholder="From Date"></td>
								<td><input name="txt_date_to" id="txt_date_to_requ" class="datepicker" style="width:70px" placeholder="To Date"></td>

								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"></td>
								<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"></td>
								<td>
									<?
									echo create_drop_down("cbo_get_upto_qnty", 70, $get_upto, "", 1, "- All -", 0, "", 0);
									?>
								</td>
								<td>
									<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
								</td>
								<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fnc_generate_report()" /></td>
								<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Only Excel" onClick="fnc_generate_report_xl()" /></td>
								<a href="" id="aa1"></a>
							</tr>
						</tbody>
					</table>
					<table>
						<tr>
							<td><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>
		<br>
		<div id="report_container" align="center"></div><br>
		<div id="report_container2" align="center"></div>


	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>