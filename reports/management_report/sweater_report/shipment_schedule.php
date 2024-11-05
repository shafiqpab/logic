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
			col: [21, 22, 25, 31, 34, 35, 36],
			operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum"],
			write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
		}
	}

	function generate_report_main(rpt_type) {
		var cbo_company = document.getElementById('cbo_company_name').value;
		var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
		var cbo_style_owner = document.getElementById('cbo_style_owner').value;
		var cbo_team_name = document.getElementById('cbo_team_name').value;
		var cbo_team_member = document.getElementById('cbo_team_member').value;

		var txt_search_string = document.getElementById('txt_search_string').value;
		var txt_file = document.getElementById('txt_file').value;
		var txt_ref = document.getElementById('txt_ref').value;
		var cbo_season = document.getElementById('cbo_season').value;

		var date_from = document.getElementById('txt_date_from').value;
		var date_to = document.getElementById('txt_date_to').value;

		var divData = "";
		var msgData = "";
		if (date_from == "" && date_to == "") {
			if (cbo_company == 0 && cbo_style_owner != 0 && cbo_team_name == 0 && cbo_team_member == 0 && txt_search_string == "" && txt_file == "" && txt_ref == "") {
				var divData = "txt_date_from*txt_date_to";
				var msgData = "From Date*To Date";
			} else if (cbo_company != 0 && cbo_style_owner == 0 && cbo_buyer_name == 0 && cbo_team_name == 0 && cbo_team_member == 0 && txt_search_string == "" && txt_file == "" && txt_ref == "") {
				var divData = "txt_date_from*txt_date_to";
				var msgData = "From Date*To Date";
			} else if (cbo_company == 0 && cbo_style_owner == 0 && cbo_team_name == 0 && cbo_team_member == 0 && txt_search_string == "" && txt_file == "" && txt_ref == "") {
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
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_style_owner*cbo_buyer_name*cbo_year*cbo_team_name*cbo_team_member*cbo_search_by*txt_search_string*txt_hidden_string*txt_po_hidden_string*txt_date_from*txt_date_to*cbo_category_by*txt_file*txt_ref*cbo_season*cbo_ordstatus*cbo_brand_name*cbo_season_year*cbo_team_leader', "../../../") + '&report_title=' + report_title + '&rpt_type=' + rpt_type;
		//alert (data); return;
		freeze_window(3);
		http.open("POST", "requires/shipment_schedule_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse() {
		if (http.readyState == 4) {
			var reponse = trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			if (reponse[2] == 1) //Aziz
			{
				document.getElementById('content_summary3_panel').innerHTML = document.getElementById('shipment_performance').innerHTML;
				percent_set();
			}

			document.getElementById('report_container').innerHTML = report_convert_button('../../../');
			//document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if (reponse[2] != 3) {
				append_report_checkbox('table_header_1', 1);
				setFilterGrid("table_body", -1, tableFilters);
			}
			show_msg('3');
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
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_schedule_controller.php?action=' + action + '&job_no=' + job_no + '&id=' + id, 'Last Ex-Factory Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
	}

	function order_status(action, id, width) {
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_schedule_controller.php?action=' + action + '&id=' + id, 'Po Status', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
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
		var page_link = 'requires/shipment_schedule_controller.php?action=buyer_popup&companyID=' + companyID;
		var title = 'Buyer Search';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0', '../../');
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var party_name = this.contentDoc.getElementById("hide_party_name").value;
			var party_id = this.contentDoc.getElementById("hide_party_id").value;

			$('#txt_buyer_name').val(party_name);
			$('#cbo_buyer_name').val(party_id);

			load_drop_down('requires/shipment_schedule_controller', party_id, 'load_drop_down_season', 'season_td');
			load_drop_down('requires/shipment_schedule_controller', party_id, 'load_drop_down_brand', 'brand_td');
			set_multiselect('cbo_brand_name', '0', '0', '', '0');

		}
	}

	function fnc_brandload() {
		var buyer = $('#cbo_buyer_name').val();
		if (buyer != 0) {
			load_drop_down('requires/shipment_schedule_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
	function openmypage_style(type_id)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var search_ID = $("#txt_search_string").val();
		if(type_id==1)
		{
			title='Order Search';
		}
		else{
			title='Style Search';
		}
		var page_link='requires/shipment_schedule_controller.php?action=style_search_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&type_id='+type_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hide_style_no").value;
			var style_id=this.contentDoc.getElementById("hide_style_id").value;
			var po_no=this.contentDoc.getElementById("hide_po_name").value;
			var po_id=this.contentDoc.getElementById("hide_po_id").value;
			//alert(po_id);
			if(type_id==1)
				{
					$('#txt_search_string').val(po_no);
					$('#search_by_td').val(po_id);	
					$('#txt_po_hidden_string').val(po_id);
				}
				else
				{
					$('#txt_search_string').val(style_no);
					$('#search_by_td').val(style_id);	
					$('#txt_hidden_string').val(style_id);
				}
			
				

			
		}
	}
</script>
</head>

<body onLoad="set_hotkey();fnc_brandload;">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs("../../../");  ?>
		<form name="shipmentschedule_1" id="shipmentschedule_1" autocomplete="off">
			<h3 style="width:1450px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:1450px;">
					<table class="rpt_table" width="1450" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th width="140">Company</th>
								<th width="130">Style Owner</th>
								<th width="130">Buyer</th>
								<th width="70">Brand</th>
								<th width="60">Year</th>
								<th width="100">Team</th>
								<th width="100">Team Leader</th>
								<th width="100">Team Member</th>
								<th width="80">Search By</th>
								<th width="70" id="search_by_td_up">Order No</th>
								<th width="70">Season</th>
								<th width="50">Season Year</th>
								<th width="60">File No</th>
								<th width="60">Ref No</th>
								<th width="110" colspan="2">Date</th>
								<th width="100">Date Category</th>
								<th width="80">Order Status</th>
								<th><input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td><? echo create_drop_down("cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "--Select Company--", $selected, ""); //load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_buyer', 'buyer_td');  
								?></td>
							<td><? echo create_drop_down("cbo_style_owner", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-Style Owner-", $selected, ""); ?></td>
							<td id="buyer_td">
								<input type="text" id="txt_buyer_name" name="txt_buyer_name" class="text_boxes" style="width:120px" onDblClick="openmypage_buyer();" placeholder="Browse" readonly />
								<input type="hidden" id="cbo_buyer_name" name="cbo_buyer_name" class="text_boxes" style="width:60px" />
								<? //echo create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_season', 'season_td');" ); 
								?>
							</td>
							<td id="brand_td"><?= create_drop_down("cbo_brand_name", 70, $blank_array, "", 1, "--Select--", $selected, "", 0, ""); ?></td>
							<td><? echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", $selected, "", 0, ""); ?></td>
							<td><? echo create_drop_down("cbo_team_name", 100, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name", "id,team_name", 1, "-Team Name-", $selected, "load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_team_leader', 'team_lead_td' );load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_team_member', 'team_td' )"); ?></td>
							<td id="team_lead_td"><? echo create_drop_down("cbo_team_leader", 100, $blank_array, "", 1, "-Team Leader-", $selected, ""); ?></td>
							<td id="team_td"><? echo create_drop_down("cbo_team_member", 100, $blank_array, "", 1, "-Team Member-", $selected, ""); ?></td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Order Wise", 2 => "Style Wise"); //,2=>"Job Wise"
								echo create_drop_down("cbo_search_by", 80, $search_by_arr, "", 0, "", "", 'search_by(this.value)', 0);
								?>
							</td>
							<td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:60px" onDblClick="openmypage_style($('#cbo_search_by').val());$('#txt_hidden_string').val('');$('#txt_po_hidden_string').val('');" placeholder="Browse or Write"/>
							<input type="hidden" id="txt_hidden_string" name="txt_hidden_string" class="text_boxes" style="width:60px" />
							<input type="hidden" id="txt_po_hidden_string" name="txt_po_hidden_string" class="text_boxes" style="width:60px" />
						</td>
							<td id="season_td"><? echo create_drop_down("cbo_season", 70, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0  order by season_name ASC", "id,season_name", 1, "-- Select Season--", "", ""); ?></td>
							<td><?= create_drop_down("cbo_season_year", 50, create_year_array(), "", 1, "-All-", $selected, "", 0, ""); ?></td>

							<td><input type="text" name="txt_file" id="txt_file" class="text_boxes" style="width:50px" placeholder="Write" /></td>
							<td><input type="text" name="txt_ref" id="txt_ref" class="text_boxes" style="width:50px" placeholder="Write" /></td>
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
							<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
							<td>
								<select name="cbo_category_by" id="cbo_category_by" style="width:100px" class="combo_boxes">
									<option value="1">Ship Date Wise </option>
									<option value="2">PO Rec. Date Wise </option>
									<option value="3">PO Insert Date Wise </option>
								</select>
							</td>
							<td>
								<?
								$ordstatusArr = array(1 => "Confirmed", 2 => "Projected"); //,2=>"Job Wise"
								echo create_drop_down("cbo_ordstatus", 80, $ordstatusArr, "", 1, "-All-", "1", '', 0);
								?>
							</td>
							<td><input type="button" name="search" id="search" value="Details" onClick="generate_report_main(1)" style="width:60px" class="formbutton" /></td>
						</tr>
						<tr>
							<td colspan="12" align="center">
								<? echo load_month_buttons(1); ?>
							</td>
							<td colspan="3" align="center">
								<input type="button" name="search" id="search" value="Short" onClick="generate_report_main(2)" style="width:60px" class="formbutton" />
								<input type="button" name="search" id="search" value="Size Wise" onClick="generate_report_main(3)" style="width:60px" class="formbutton" />
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