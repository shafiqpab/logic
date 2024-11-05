<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Marchendising Kpi Report Report.
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	19-01-2015
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
echo load_html_head_contents("Marchendising Kpi Report", "../../", 1, 1, $unicode, '1', '');
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated() {
		if (form_validation('cbo_company_name', 'Company Name') == false) //*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		} else {
			var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_leader*cbo_dealing_merchant*txt_job_no*txt_order_no*txt_style_ref*txt_internal_ref*cbo_search_by*txt_date_from*txt_date_to*cbo_year_selection', "../../");
			//alert(data);
			freeze_window(3);
			http.open("POST", "requires/merchandising_kpi_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}


	function fn_report_generated_reponse() {
		if (http.readyState == 4) {

			var reponse = trim(http.responseText).split("****");
			var tot_rows = reponse[2];
			var search_by = reponse[3];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(' + tot_rows + ')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			/*if(tot_rows*1>1)
			{
			if(search_by==1)
			    {
			
				 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["total_order_qnty","total_order_qnty_in_pcs","value_req_qnty","value_pre_costing","value_wo_qty","value_in_qty","value_rec_qty","value_issue_qty","value_leftover_qty"],
					   col: [5,7,14,15,16,17,18,19,20],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
			if(search_by==2)
			    {
			
				 var tableFilters = 
				 {
					 
					col_operation: {
					   id: ["total_order_qnty","value_rec_qty","value_issue_qty","value_leftover_qty"],
					   col: [5,8,9,10],
					   operation: ["sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
				setFilterGrid("table_body",-1);
			}*/
			//setFilterGrid("table_body_style",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function change_color(v_id, e_color) {
		if (document.getElementById(v_id).bgColor == "#33CC00") {
			document.getElementById(v_id).bgColor = e_color;
		} else {
			document.getElementById(v_id).bgColor = "#33CC00";
		}
	}


	function new_window(html_filter_print) {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";

		if (html_filter_print * 1 > 1) $("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "400px";

		if (html_filter_print * 1 > 1) $("#table_body tr:first").show();
	}


	function generate_report(company, job_no, type) {
		var data = "action=" + type + "&txt_job_no='" + job_no + "'&cbo_company_name=" + company;
		http.open("POST", "../woven_order/requires/pre_cost_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}

	function fnc_generate_report_reponse() {
		if (http.readyState == 4) {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><body>' + http.responseText + '</body</html>');
			d.close();
		}
	}

	function openmypage(po_id, item_name, job_no, book_num, trim_dtla_id, action) { //alert(book_num);
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/merchandising_kpi_controller.php?po_id=' + po_id + '&item_name=' + item_name + '&job_no=' + job_no + '&book_num=' + book_num + '&trim_dtla_id=' + trim_dtla_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_inhouse(po_id, item_name, action) {
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/merchandising_kpi_controller.php?po_id=' + po_id + '&item_name=' + item_name + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_issue(po_id, item_name, action) {
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/merchandising_kpi_controller.php?po_id=' + po_id + '&item_name=' + item_name + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function order_qty_popup(company, job_no, po_id, buyer, action) {
		//alert(po_id);
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/merchandising_kpi_controller.php?company=' + company + '&job_no=' + job_no + '&po_id=' + po_id + '&buyer=' + buyer + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function order_req_qty_popup(company, job_no, po_id, buyer, rate, item_group, boook_no, description, country_id, trim_dtla_id, start_date, end_date, action) {
		//alert(country_id);
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/merchandising_kpi_controller.php?company=' + company + '&job_no=' + job_no + '&po_id=' + po_id + '&buyer=' + buyer + '&rate=' + rate + '&item_group=' + item_group + '&boook_no=' + boook_no + '&description=' + description + '&country_id_string=' + country_id + '&trim_dtla_id=' + trim_dtla_id + '&start_date=' + start_date + '&end_date=' + end_date + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function search_populate(str) {

		if (str == 1) {
			document.getElementById('search_by_th_up').innerHTML = "Shipment Date";
			//document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"	value=""  />';
		} else if (str == 2) {
			document.getElementById('search_by_th_up').innerHTML = "Precost Date";
			///document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"	value=""  />';
		}

	}
</script>

</head>

<body onLoad="set_hotkey();">
	<form id="accessoriesFollowup_report">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", ''); ?>
			<h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:1340px;">
					<table class="rpt_table" width="1340" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
						<thead>
							<tr>
								<th>Company Name</th>
								<th>Buyer Name</th>
								<th>Team</th>
								<th>Team Member</th>


								<th>Job No</th>
								<th>Styel Ref</th>
								<th>Internal Ref</th>
								<th>Order No</th>

								<th>Type</th>
								<th align="center" id="search_by_th_up">Date Range</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?
									echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/merchandising_kpi_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
									?>
								</td>
								<td id="buyer_td">
									<?
									echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
									?>
								</td>

								<td>
									<?
									echo create_drop_down("cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/merchandising_kpi_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' ) ");
									?>
								</td>

								<td id="div_marchant">
									<?
									echo create_drop_down("cbo_dealing_merchant", 172, $blank_array, "", 1, "-- Select Team Member --", $selected, "");
									?>
								</td>



								<td align="center">
									<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Job No">
								</td>

								<td align="center">
									<input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Style Ref">
								</td>

								<td align="center">
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px" placeholder="Internal Ref">
								</td>

								<td align="center">
									<input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Order No">
								</td>



								<td align="center">
									<?
									$search_by_arr1 = array(1 => "Shipment Date Wise", 2 => "Po Rcvd Date Wise", 3 => "PO Insert Date Wise");
									echo create_drop_down("cbo_search_by", 100, $search_by_arr1, "", 0, "", "", '', 0); //search_by(this.value)
									?>
								</td>

								<td id="search_by_td">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">&nbsp; To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
								</td>
								</td>

								<td>
									<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
								</td>
							</tr>
						</tbody>
					</table>
					<table>
						<tr>
							<td>
								<? echo load_month_buttons(1); ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div id="report_container" align="center"></div>
		<div id="report_container2"></div>
	</form>
</body>
<script>
	set_multiselect('cbo_item_group', '0', '0', '0', '0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>