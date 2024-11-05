<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Shipment Schedule Report [Sweater]
Functionality	         :
JS Functions	         :
Created by		         :	Kausar
Creation date 	         :	20-03-2019
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         : 	Jahid
Update date		         : 	07-06-15
QC Performed BY	         :
QC Date			         :
Comments		         : 

*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info", "../../../", 1, 1, $unicode, 1, '');
?>
<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	var tableFilters = {
		col_38: "select",
		display_all_text: 'Show All',
		col_operation: {
			id: ["total_order_qnty_pcs", "total_order_qnty", "value_total_order_value", "total_ex_factory_qnty", "total_short_access_qnty", "value_total_short_access_value", "value_yarn_req_tot"],
			col: [21, 22, 25, 32, 34, 35, 36],
			operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum"],
			write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
		}
	}

	function generate_report_main(rpt_type) {
		var cbo_company = document.getElementById('cbo_company_name').value;
		var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
		var date_from = document.getElementById('txt_date_from').value;
		var date_to = document.getElementById('txt_date_to').value;

		var divData = "";
		var msgData = "";
		if (date_from == "" && date_to == "") {
			if (cbo_company == 0) {
				var divData = "txt_date_from*txt_date_to";
				var msgData = "From Date*To Date";
			} else if (cbo_company != 0 && cbo_buyer_name == 0) {
				var divData = "txt_date_from*txt_date_to";
				var msgData = "From Date*To Date";
			}
		}

		if (divData != "") {
			if (form_validation(divData, msgData) == false) {
				return;
			}
		}

		var report_title = $("div.form_caption").html();
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*cbo_brand_name*txt_style_ref*cbo_year_selection*cbo_year', "../../../") + '&report_title=' + report_title + '&rpt_type=' + rpt_type;
		//alert (data); return;
		freeze_window(3);
		http.open("POST", "requires/time_weight_details_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
           
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = {
				col_operation: {
				id: ["tot_order_qty"],
				col: [10],
				operation: ["sum"],
				write_method: ["innerHTML"]
				}
			}
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();

		}
	}

	function new_window() {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "380px";
	}

	function percent_set() {
		var tot_row = document.getElementById('tot_row').value;
		var tot_value_js = document.getElementById('total_value').value;
		for (var i = 1; i < tot_row; i++) {
			var value_js = document.getElementById('value_' + i).value;
			var percent_value_js = ((value_js * 1) / (tot_value_js * 1)) * 100
			document.getElementById('value_percent_' + i).innerHTML = percent_value_js.toFixed(2);
		}
	}

	function openmypage_image(page_link, title) {
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '../../')
		emailwindow.onclose = function() {}
	}

	function last_ex_factory_popup(action, job_no, id, width) {
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/time_weight_details_controller.php?action=' + action + '&job_no=' + job_no + '&id=' + id, 'Last Ex-Factory Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
	}

	function order_status(action, id, width) {
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/time_weight_details_controller.php?action=' + action + '&id=' + id, 'Po Status', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
	}

	function search_by(val) {
		$('#txt_search_string').val('');
		if (val == 1) {
			$('#search_by_td_up').html('Order No');
		} else {
			$('#search_by_td_up').html('Style No');
		}
	}

	function openmypage_buyer() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var page_link = 'requires/time_weight_details_controller.php?action=buyer_popup&companyID=' + companyID;
		var title = 'Buyer Search';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0', '../../');
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var party_name = this.contentDoc.getElementById("hide_party_name").value;
			var party_id = this.contentDoc.getElementById("hide_party_id").value;

			$('#txt_buyer_name').val(party_name);
			$('#cbo_buyer_name').val(party_id);

			load_drop_down('requires/time_weight_details_controller', party_id, 'load_drop_down_season', 'season_td');
			load_drop_down('requires/time_weight_details_controller', party_id, 'load_drop_down_brand', 'brand_td');
			set_multiselect('cbo_brand_name', '0', '0', '', '0');

		}
	}

	function fnc_brandload() {
		var buyer = $('#cbo_buyer_name').val();
		if (buyer != 0) {
			load_drop_down('requires/time_weight_details_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
</script>
</head>

<body onLoad="set_hotkey();fnc_brandload;">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../../");  ?>
		<form name="shipmentschedule_1" id="shipmentschedule_1" autocomplete="off">
			<h3 style="width:780px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:780px;">
					<table class="rpt_table" width="720" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
							<th width="150" class="must_entry_caption">Company Name</th>
								<th width="150">Buyer Name</th>
								<th width="100">Brand</th>
								<th width="100">Style Ref.</th>
								<th width="65">Year</th>
								<th width="150" colspan="2" id="search_by_th_up">First Tod Range</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td><? echo create_drop_down("cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "--Select Company--", $selected, "");
								?></td>
							<td id="buyer_td">
								<input type="text" id="txt_buyer_name" name="txt_buyer_name" class="text_boxes" style="width:150px" onDblClick="openmypage_buyer();" placeholder="Browse" readonly />
								<input type="hidden" id="cbo_buyer_name" name="cbo_buyer_name" class="text_boxes" style="width:60px" />
							</td>
							<td id="brand_td"><?= create_drop_down("cbo_brand_name", 100, $blank_array, "", 1, "--Select--", $selected, "", 0, ""); ?></td>
							<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px" placeholder="Write"></td>
							<td><? echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
							
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
							<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
							<td><input type="button" name="search" id="search" value="Details" onClick="generate_report_main(1)" style="width:60px" class="formbutton" /></td>
						</tr>
						<tr>
							<td colspan="12" align="center">
								<? echo load_month_buttons(1); ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		</form>
		<div id="report_container" align="center"></div>
		<div id="report_container2"></div>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_company_name', '0', '0', '', '0');
	set_multiselect('cbo_brand_name', '0', '0', '', '0');
</script>

</html>