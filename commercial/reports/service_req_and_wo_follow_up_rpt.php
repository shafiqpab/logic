<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Service Requisition and WO Follow Up Report.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	19-09-2022
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
echo load_html_head_contents("Service Requisition and WO Follow Up Report", "../../",  1, 1, $unicode, 1, '');
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function generate_report() {
		if ($('#txt_req_no').val() != "" || $('#txt_wo_no').val() != "") {
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}
		} else {
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to*cbo_date_type', 'Company Name*Date*Date*Date Type') == false) {
				return;
			}
		}
		var report_title = $("div.form_caption").html();
		var data = "action=report_generate" + get_submitted_data_string("cbo_company_name*cbo_location*cbo_store*cbo_service_for*txt_req_no*cbo_job_year*txt_wo_no*cbo_value_with*txt_date_from*txt_date_to*cbo_date_type", "../../") + '&report_title=' + report_title;
		freeze_window(3);
		http.open("POST", "requires/service_req_and_wo_follow_up_rpt_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse() {
		if (http.readyState == 4) {
			var response = trim(http.responseText).split("####");
			//alert(response[0]);
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
			setFilterGrid("table_body", -1);
			setFilterGrid("table_body2", -1);
		}
	}


	function new_window() {
		//alert(document.getElementById('report_container2').innerHTML);
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";
		document.getElementById('scroll_body2').style.overflow = "auto";
		document.getElementById('scroll_body2').style.maxHeight = "none";
		document.getElementById('scroll_body3').style.overflow = "auto";
		document.getElementById('scroll_body3').style.maxHeight = "none";
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		$('#table_body3 tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		$('#table_body3 tr:first').show();
		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "300px";
		document.getElementById('scroll_body2').style.overflowY = "scroll";
		document.getElementById('scroll_body2').style.maxHeight = "300px";
		document.getElementById('scroll_body3').style.overflowY = "scroll";
		document.getElementById('scroll_body3').style.maxHeight = "300px";

	}

	function fn_ceckReqWo(str) {

		if (str == 1) {
			$('#txt_req_no').attr("disabled", false);
			if ($('#txt_req_no').val() == "") {
				$('#txt_wo_no').val("").attr("disabled", false);
			} else {

				$('#txt_wo_no').val("").attr("disabled", true);
			}

		} else {

			$('#txt_wo_no').attr("disabled", false);
			if ($('#txt_wo_no').val() == "") {
				$('#txt_req_no').val("").attr("disabled", false);
			} else {

				$('#txt_req_no').val("").attr("disabled", true);
			}

		}
	}

	function fnc_req_report(company_name, id, report_title, action) {
		print_report( company_name+'*'+id+'*'+report_title+'*'+1, action, "../work_order/requires/service_requisition_controller" );
	}
	
	function fnc_wo_report(type,company_name, id, report_title) {
		// alert(type); return;
		if(type==1)
		{
			print_report( company_name+'*'+id+'*'+report_title, "service_work_order_print", "../work_order/requires/service_work_order_controller" );
		}
		else if(type==2)
		{
			print_report( company_name+'*'+id+'*'+report_title, "service_work_order_po_print", "../work_order/requires/service_work_order_controller" );
		}
		else if(type==3)
		{
			print_report( company_name+'*'+id+'*'+report_title, "service_work_order_print_2", "../work_order/requires/service_work_order_controller" );
		}
		else
		{
			print_report( company_name+'*'+id+'*'+report_title, "service_work_order_print", "../work_order/requires/service_work_order_controller" );
		}
	}

	function fnc_matrial_list(prod_id){
		var page_link = 'requires/service_req_and_wo_follow_up_rpt_controller.php?action=tag_materials_popup&tagMaterials='+prod_id;
		var title = 'Search Item Details';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../", ''); ?>
		<form id="frmServiceReqAndWO_rpt" name="frmServiceReqAndWO_rpt">
			<div style="width:1350px;">
				<h3 align="left" id="accordion_h1" style="width:1350px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:1350px;">
						<table class="rpt_table" cellspacing="0" cellpadding="0" width="1330" rules="all">
							<thead>
								<tr>
									<th width="140" class="must_entry_caption">Company</th>
									<th width="140">Location</th>
									<th width="100">Store</th>
									<th width="100">Service For</th>
									<th width="80">Year</th>
									<th width="100">Reqsn No</th>
									<th width="100">WO No</th>
									<th width="110">Value</th>
									<th width="110" class="must_entry_caption">Date Type</th>
									<th width="180" colspan="2" class="must_entry_caption">Reqsn Date Range</th>
									<th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frmServiceReqAndWO_rpt','report_container*report_container2','','','')" /></th>
								</tr>
							</thead>
							<tbody>
								<tr class="general">
									<td align="center">
										<?
										echo create_drop_down("cbo_company_name", 140, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_req_and_wo_follow_up_rpt_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/service_req_and_wo_follow_up_rpt_controller', this.value+'_'+document.getElementById('cbo_location').value, 'load_drop_down_store', 'store_td' )", "");
										?>
									</td>
									<td id="location_td" align="center">
										<?
										echo create_drop_down("cbo_location", 140, "select id,location_name from lib_location", "id,location_name", 1, "-- Select Location --", $selected, "", 0, "");
										?>
									</td>
									<td id="store_td" align="center">
										<?
										echo create_drop_down("cbo_store", 140, $blank_array, "", 1, "-- Select Store --", $selected, "", 0, "");
										?>
									</td>
									<td align="center">
										<?
										echo create_drop_down("cbo_service_for", 90, $service_for_arr, "", 1, "-- Select --", $selected, "", 0, "");
										?>
									</td>
									<td align="center">
										<?
										$year_current = date("Y");
										echo create_drop_down("cbo_job_year", 70, $year, "", 1, "-Select-", $year_current);
										?>
									</td>

									<td align="center">
										<input type="text" id="txt_req_no" name="txt_req_no" style="width:90px;" class="text_boxes_numeric" onBlur="fn_ceckReqWo(1);">
									</td>
									<td align="center">
										<input type="text" id="txt_wo_no" name="txt_wo_no" style="width:90px;" class="text_boxes_numeric" onBlur="fn_ceckReqWo(2);">
									</td>
									<td>
										<? $valueWithArr = array(0 => 'Value With 0', 1 => 'Value Without 0', 2 => 'Value Only 0');
										echo create_drop_down("cbo_value_with", 115, $valueWithArr, "", 0, "--  --", 0, "", "", ""); ?>
									</td>
									<td> 
									<? $dateArr=array(1=>'Requisition Date', 2=>'Independent WO Date');
									echo create_drop_down( "cbo_date_type", 110, $dateArr, "", 1, "-- Select --", 0, "", "", ""); ?>
								</td>
									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
									</td>
									<td align="center">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
									</td>
									<td align="center">
										<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
									</td>
								</tr>
								<tr>
									<td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
								</tr>
							</tbody>
						</table>
					</fieldset>
				</div>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
			<div style="display:none" id="data_panel"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>