<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Category Wise Monthly Budget vs Requisition Report
Functionality	:	
JS Functions	:
Created by		:	Zayed 
Creation date 	: 	02-03-2023
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
echo load_html_head_contents("Category Wise Monthly Budget vs  Requisition Report", "../../", 1, 1, '', '', '');
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function generate_report(rep_type)
	{
		if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company Name*Date From*Date To') == false) {
			return;
		}

		var report_title = $("div.form_caption").html();
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_category_name*txt_date_from*txt_date_to', "../../") + '&report_title=' + report_title;
		// alert(data);return;
		freeze_window(3);
		http.open("POST", "requires/category_wise_monthly_budget_vs_requisition_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if (http.readyState == 4) {
			var response = trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);

			document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>'); // media="print"
		d.close();
	}

	function openmypage(company_id,budget_id, category_id)
	{
		page_link = 'requires/category_wise_monthly_budget_vs_requisition_report_controller.php?action=req_details&company_id=' + company_id + '&budget_id=' + budget_id + '&category_id=' + category_id;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Requisition Details', 'width=520px,height=350px,center=1,resize=0,scrolling=0', '../');
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<form id="category_wise_monthly_budget_vs_requ_rpt">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", ''); ?>
			<h3 align="left" id="accordion_h1" style="width:745px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:745px;">
					<table class="rpt_table" width="750" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
						<thead>
							<tr>
								<th width="200" class="must_entry_caption">Company name</th>
								<th width="180">Item Category</th>
								<th colspan="2" width="240" class="must_entry_caption">Applying Period</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('category_wise_monthly_budget_vs_requ_rpt','report_container*report_container2','','','')" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_company_name", 200, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --");
									?>
								</td>
								<td align="center">
									<? echo create_drop_down("cbo_category_name", 180, "SELECT category_id, short_name from  lib_item_category_list where category_type=1 and status_active=1 and is_deleted=0 order by short_name", "category_id,short_name", 1, "-- Select Category --"); ?>
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date" disabled>
								</td>
								<td align="center">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date" disabled>
								</td>
								<td align="center">
									<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
								</td>
							</tr>
							<tr>
								<td align="center" colspan="9"><? echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
		<div style="margin-top:10px" id="report_container" align="center"></div>
		<div id="report_container2" align="center"></div>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>