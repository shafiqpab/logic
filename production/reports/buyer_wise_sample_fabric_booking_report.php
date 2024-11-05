<?php
/**-------------------------------------------- Comments -----------------------
 * Purpose         :    This Form Will Create Buyer Wise Sample Fabric Booking Report.
 * Functionality   :
 * JS Functions    :
 * Created by      :    Md. Shafiqul Islam Shafiq
 * Creation date   :    19-06-2019
 * Updated by      :
 * Update date     :
 * QC Performed BY :
 * QC Date         :
 * Comments        :
 */
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
	require_once('../../includes/common.php');
	$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Buyer Wise Sample Fabric Booking Report", "../../", 1, 1, '', 1, 1,1);
	?>
	<script>
		if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
		var permission = '<?php echo $permission; ?>';

		var tableFilters = 
		{
			col_7: "select",
			display_all_text:'Show All',
		} 

		function fn_report_generated(type) 
		{
			var job_no = $('#txt_job_no').val();
			var po_no = $('#txt_search_string').val();
			var date_from_po = $('#txt_date_from_po').val();

			if (form_validation('cbo_company_name', 'Comapny Name*Date Form*Date To') == false) {
				return;
			}

			var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*txt_date_from*txt_date_to*cbo_year*cbo_team_leader*cbo_dealing_merchant*hide_booking_id', "../../");
			

			freeze_window(3);
			http.open("POST", "requires/buyer_wise_sample_fabric_booking_report_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}

		function fn_report_generated_reponse() {
			if (http.readyState == 4) {
				var response = trim(http.responseText).split("####");
				$('#report_container2').html(response[0]);
				document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid('scroll_body',-1,tableFilters);
				show_msg('3');
				release_freezing();
			}
		}

		function new_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$(".flt").css("display","none");
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			
			document.getElementById('scroll_body').style.overflowY="scroll"; 
			document.getElementById('scroll_body').style.maxHeight="400px";
			$(".flt").css("display","block");
		}

		function generate_order_report(type,booking_no,company_id,is_approved,fabric_natu)
		{
			
			if(type==2)
			{
				var data="action=show_fabric_booking_report2&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";
			}
			else if(type==1)
			{
				var data="action=show_fabric_booking_report&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";
			}
			else if(type==3)
			{
				var data="action=show_fabric_booking_report3&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";
			}
			else if(type==4)
			{
				var data="action=show_fabric_booking_report4&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";
			}
			else if(type==5)
			{
				var data="action=show_fabric_booking_report5&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";
			}
			else if(type==6)
			{
				var data="action=show_fabric_booking_report6&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";

			}
			else if(type==7)
			{
				var data="action=show_fabric_booking_report7&txt_booking_no='"+trim(booking_no)+"'&cbo_company_name='"+company_id+"'&id_approved_id='"+is_approved+"'&cbo_fabric_natu='"+fabric_natu+"'";
			}
			
			//var data="action=show_fabric_booking_report"+'&txt_booking_no='+"'"+trim(booking_no)+"'"+'&cbo_company_name='+"'"+company_id+"'"+'&id_approved_id='+"'"+is_approved+"'";
			
			http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
			
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}

		function generate_fabric_report_reponse() {
			if (http.readyState == 4) {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
			}

		}
		

		function openmypage_booking() 
		{
			if (form_validation('cbo_company_name', 'Company Name') == false) {
				return;
			}
			var companyID = $("#cbo_company_name").val();
			var page_link = 'requires/buyer_wise_sample_fabric_booking_report_controller.php?action=booking_no_search_popup&companyID=' + companyID;
			var title = 'Booking Search';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
				var booking_num = this.contentDoc.getElementById("hidden_booking_num").value;

				$('#txt_booking_no').val(booking_no);
				$('#hide_booking_id').val(booking_num);
			}
		}

	</script>
</head>
<body>
	<?php echo load_freeze_divs("../../", ''); ?>
	<form id="fabricReceiveStatusReport_1">
		<div align="center">
			<h3 style="width:1070px;" align="left" id="accordion_h1" class="accordion_h"
			onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:1070px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
					align="center">
					<thead>
						<th class="must_entry_caption">Company Name</th>
						<th>Buyer Name</th>
						<th>Team Leader</th>
						<th>Dealing Merchant</th>
						<th>Year</th>
						<th>Booking No</th>
						<th colspan="2">Booking Date</th>
						<th colspan="2"><input type="reset" name="res" id="res" value="Reset"
							onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')"
							class="formbutton" style="width:70px"/></th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?php
									echo create_drop_down("cbo_company_name", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/buyer_wise_sample_fabric_booking_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
									?>
								</td>
								<td id="buyer_td">
									<?php
									echo create_drop_down("cbo_buyer_name", 145, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
									?>
								</td>
								<td>
									<?
                                    echo create_drop_down( "cbo_team_leader", 145, "select id,team_leader_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_leader_name","id,team_leader_name", 1, "-- Select --", $selected, " load_drop_down( 'requires/buyer_wise_sample_fabric_booking_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                              		?>
								</td>
								<td id="team_td">
									<?
                                    echo create_drop_down( "cbo_dealing_merchant", 145, "$blank_array","", 1, "-- Select --", $selected, "" );
                              		?>
								</td>
								<td>
									<?
									$selected_year = date('Y');
                                    echo create_drop_down( "cbo_year", 90, $year,"", 1, "-- Select --", $selected_year, "" );
                              		?>
								</td>                       
								<td id="search_by_td">
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"/>
									<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
									placeholder="From Date" readonly>
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
									placeholder="To Date" readonly>
								</td> 
								<td>
									<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show"
									onClick="fn_report_generated(1)"/>
								</td>
							</tr>
						</tbody>
					</table>
					<div><?php echo load_month_buttons(1); ?></div>
				</fieldset>
			</div>
		</div>
		<div style="display:none" id="data_panel"></div>
		<div style="padding: 10px;" id="report_container" align="center"></div>
		<div id="report_container2" align="center"></div>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
