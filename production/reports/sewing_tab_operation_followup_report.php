<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sweing Tab Operation Followup Report
				
Functionality	:	
JS Functions	:
Created by		:	Zayed
Creation date 	: 	06-06-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sweing Tab Operation Followup Report", "../../", 1, 1, $unicode, '', '');

?>
<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	function generate_report(rptType) {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var report_title = $("div.form_caption").html();
		var cbo_company_name = $("#cbo_company_name").val();
		var txt_order_no = $("#txt_order_no").val();
		var txt_barcode_no = $("#txt_barcode_no").val();
		var txt_worker_id = $("#txt_worker_id").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();

		if (txt_order_no == "" && txt_barcode_no == "" && txt_worker_id == "" && txt_date_from == "" && txt_date_to == "") {
			alert("Please enter atleast one search criteria");
			return;
		}

		var dataString = "&rptType=" + rptType + "&cbo_company_name=" + cbo_company_name + "&txt_order_no=" + txt_order_no + "&txt_barcode_no=" + txt_barcode_no + "&txt_worker_id=" + txt_worker_id + "&txt_date_from=" + txt_date_from + "&txt_date_to=" + txt_date_to + "&report_title=" + report_title;

		var data = "action=generate_report" + dataString;
		freeze_window(3);
		http.open("POST", "requires/sewing_tab_operation_followup_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			// setFilterGrid("table_body_id", -1, tableFilters);

			show_msg('3');
			release_freezing();
		}
	}

	function new_window(type) {
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", $permission);  ?><br />
		<form name="sweing_tab_operation_followup_rpt" id="sweing_tab_operation_followup_rpt" autocomplete="off">
			<h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:900px">
				<fieldset>
					<table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th width="150" class="must_entry_caption">Company</th>
							<th width="120">Order No</th>
							<th width="120">Barcode No</th>
							<th width="120">Worker ID</th>
							<th width="180" class="must_entry_caption">Operation Date Range</th>
							<th>
								<input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('sweing_tab_operation_followup_rpt','report_container*report_container2','','','')" />
							</th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?
									echo create_drop_down("cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "Select Company", $selected, "");
									?>
								</td>
								<td>
									<input type="text" id="txt_order_no" name="" class="text_boxes" style="width:120px;">
								</td>
								<td>
									<input type="text" id="txt_barcode_no" name="" class="text_boxes" style="width:120px;">
								</td>
								<td>
									<input type="text" id="txt_worker_id" name="" class="text_boxes" style="width:120px;">
								</td>
								<td>
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" />&nbsp;To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" />
								</td>
								<td>
									<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
								</td>
							</tr>
							<tr>
								<td colspan="6" align="left">
									<? echo load_month_buttons(1);  ?>&nbsp;&nbsp;
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center" style="width:1150px;"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>