<?php

/*********************************************** Comments *************************************
 *	Purpose			:   This Form Will Create Efficiency report
 *	Functionality	:
 *	JS Functions	:
 *	Created by		:	Shariar Ahmed
 *	Creation date 	: 	11-05-2023
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
echo load_html_head_contents('Cutting Status Report', '../../', 1, 1, $unicode, 1, '');
?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = '../../logout.php';
	var permission = '<?php echo $permission; ?>';
	var isValidated = false;


	function loadBuyer() {
		var companyIds = document.getElementById('cbo_company_name').value;
		load_drop_down('requires/work_study_report_controller', companyIds, 'load_drop_down_buyer', 'buyer_td');
	}

	function openStylePopup(type) {
		if (form_validation('cbo_buyer_name', 'Buyer') == false) {
			return;
		}

		var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
		var cbo_job_year = document.getElementById('cbo_year_selection').value;

		var page_link = 'requires/work_study_report_controller.php?action=style_search_popup&buyer=' + cbo_buyer_name + '&job_year=' + cbo_job_year;
		var title = "Style Reference";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=370px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var style_no = this.contentDoc.getElementById("txt_selected_no").value; // product Description

			//var data=style_no.split("_");

			if (type == 1) {
				$('#txt_job_no').val(data[1]);
			} else {
				$('#txt_style_no').val(style_no);
			}
		}
	}

	function generateReport(type) {

		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		if (txt_date_from != '' && txt_date_to != "") {
			if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false) {
				return;
			}
		} else {
			if (form_validation('cbo_buyer_name', 'Buyer Name') == false)

			{
				return;
			}
		}


		var dataString = get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_no*cbo_date_category*txt_date_from*txt_date_to', '../../');

		if (type == 1) {
			data = "action=generate_report_1" + dataString;
		} else {
			data = "action=generate_report_2" + dataString;
		}

		freeze_window(5);
		http.open("POST", "requires/work_study_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split('**');
			// $("#report_container").html(response[0]);
			document.getElementById('report_container').innerHTML = response[0];
			document.getElementById('report_container3').innerHTML = '<a href="requires/' + response[2] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = {
				col_operation: {
					id: ["total_production_qty", "total_produce_minutes", "total_operator", "total_helper", "total_manpower", "total_available_minutes"],
					col: [12, 13, 14, 15, 16, 18],
					operation: ["sum", "sum", "sum", "sum", "sum", "sum"],
					write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
				}
			}
			setFilterGrid("report1_body", -1, tableFilters);
			release_freezing();
		}
	}

	function new_window() {
		$('#report1_body tr:nth-child(1)').css('display', 'none');

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body><div style="margin: 10px auto; display: inline-flex;">' + document.getElementById('report_container').innerHTML + '</body</html>'); // media="print"
		d.close();

		$('#report1_body tr:nth-child(1)').css('display', '');
	}

	function fnc_efc_details(poNo, prodMstId) {
		var action = 'production_details';
		var popupTitle = 'Production Details';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/work_study_report_controller.php?poNo=' + poNo + '&prodMstId=' + prodMstId + '&action=' + action, popupTitle, 'width=1000px,height=320px,center=1,resize=0', '../../');
		// emailwindow.onclose=function() {}
	}
</script>
</head>
 
<body>
	<div style="width:100%;" align="center">
		<?php echo load_freeze_divs('../', $permission); ?>
		<form id="efficiencyReport_1">
			<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:99%;">
					<table class="rpt_table" width="70%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th>Company</th>
							<th>Buyer</th>
							<th>Style</th>
							<th>Type</th>
							<th colspan="2" id="date_range">Date Range</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('efficiencyReport_1','','','','');" class="formbutton" style="width:50px" /></th>
						</thead>
						<tbody>
							<tr class="general">
								<td id="td_company">
									<?php
									echo create_drop_down('cbo_company_name', 142, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "loadBuyer();");
									?>
								</td>
								<td id="buyer_td">
									<?php
									echo create_drop_down('cbo_buyer_name', 120, $blank_array, '', 1, '-- Select Buyer --', $selected, '', '', '');
									?>
								</td>
								<td>
									<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openStylePopup(2);" readonly />
								</td>
								<td>
									<?
									$date_type_arr = array(1 => 'Select', 2 => 'Date Wise');
									echo create_drop_down("cbo_date_category", 100, $date_type_arr, "", 1, "--All--", 2, "", 0, "", "");
									?>
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
								</td>
								<td>
									<input type="button" id="show_powise" class="formbutton" style="width:70px;" value="Show" onClick="generateReport(1)" />
									<input type="hidden" id="hdn_style_ref_id" name="hdn_style_ref_id" />
								</td>
							</tr>
							<tr>
								<td colspan="11" align="center"><?php echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</form>
		<div style="margin-top:10px" id="report_container3" align="center"></div>
		<div id="report_container" style="margin: 30px 0 50px 0; width: 98%;"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>