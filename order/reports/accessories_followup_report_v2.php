<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Accessories Followup Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	10-02-2013
Updated by 		: 	Kaiyum [add Year search field]	
Update date		: 	22-10-2016	   
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
echo load_html_head_contents("Accessories Followup Report", "../../", 1, 1, $unicode, '1', '');
?>

<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(action) {
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();

		var txt_job_no = $("#txt_job_no").val();
		var hidd_job_id = $("#hidd_job_id").val();

		var txt_style_ref = $("#txt_style_ref").val();
		var hidden_style_id = $("#hidden_style_id").val();

		var txt_order_no = $("#txt_order_no").val();
		var hidd_po_id = $("#hidd_po_id").val();

		var txt_internal_ref = $("#txt_internal_ref").val();
		var txt_file_no = $("#txt_file_no").val();
		var budget_version = $('#cbo_budget_version').val();

		if (action == 'report_generate6') {
			if(txt_date_from.length > 0 && txt_date_to.length > 0){
				if (form_validation('cbo_company_name', 'Company Name') == false) {
					$("#txt_style_ref").focus();
					return;
				}
			}
			else{
				if (form_validation('cbo_company_name*txt_style_ref', 'Company Name*Style Ref') == false) {
					$("#txt_style_ref").focus();
					return;
				}
			}
			
		}
		if (form_validation('cbo_company_name', 'Company Name') == false) //*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		} else {			
			if ((txt_date_from.length > 0 && txt_date_to.length > 0) || txt_internal_ref.length > 0 || txt_file_no.length > 0 || txt_job_no.length > 0 || txt_order_no.length > 0 || txt_style_ref.length > 0) {
				var data = "action=" + action + get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_dept*cbo_team_leader*cbo_item_group*txt_date_from*txt_date_to*cbo_year*txt_job_no*txt_style_ref*txt_order_no*cbo_search_by*cbo_year_selection*txt_internal_ref*txt_file_no*hidd_job_id*hidd_po_id*cbo_season_name*cbo_budget_version*cbo_date_type*cbo_brand_id*cbo_season_year', "../../");
				//console.log(data); return;

				freeze_window(3);
				if (budget_version == 1) {
					http.open("POST", "requires/accessories_followup_report_controller_v2.php", true);
				} else {
					http.open("POST", "requires/accessories_followup_report_controller2_v2.php", true);
				}
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;

			} else {
				alert('Pub. Ship Date / Job No / Style Ref. / Internal Ref. / File No / Order No  is Mandatory');
			}

		}
	}


	function fn_report_generated_reponse() {
		if (http.readyState == 4) {
			console.log(http.responseText);
			var reponse = trim(http.responseText).split("****");
			var tot_rows = reponse[2];
			var search_by = document.getElementById('cbo_search_by').value;

			if (reponse[3] == "report_generate4") {
				//$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(' + tot_rows + ')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				document.getElementById('excel').click();

				show_msg('3');
				release_freezing();
				return;
			}

			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(' + tot_rows + ')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			if (reponse[3] == "report_generate2") {
				if (tot_rows * 1 > 0) {
					if (search_by == 1) {

						var tableFilters = {
							col_operation: {
								id: ["value_pre_costing", "value_wo_qty"],
								col: [18, 21],

								operation: ["sum", "sum"],
								write_method: ["innerHTML", "innerHTML"]
							}
						}
						//alert(tableFilters);

					}
					if (search_by == 2) {
						var tableFilters = {
							col_operation: {
								id: ["value_pre_costing", "value_wo_qty"],
								col: [19, 22],
								operation: ["sum", "sum"],
								write_method: ["innerHTML", "innerHTML"]
							}
						}
					}
					setFilterGrid("table_body", -1, tableFilters);
					/*var index=0;
					var tr=$("#table_body tr:first").clone();
					$("#table_body  tr:eq("+index+")").remove();
					 $("#ddd thead").append(tr);*/
				}
			}
			show_msg('3');
			release_freezing();
		}
	}

	function generate_report_v3(company_name, job_no, style_ref_no, buyer_name, costing_date, po_ids, type) {

		freeze_window(3);
		if (type == "summary" || type == "budget3_details" || type == "budget_4") {
			if (type == 'summary') {
				var rpt_type = 3;
				var comments_head = 0;
			} else if (type == 'budget3_details') {
				var rpt_type = 4;
				var comments_head = 1;
			} else if (type == 'budget_4') {
				var rpt_type = 7;
				comments_head = 1;
			}

			var report_title = "Budget/Cost Sheet";
			//	var comments_head=0;
			var cbo_company_name = company_name;
			var cbo_buyer_name = buyer_name;
			var txt_style_ref = style_ref_no;
			var txt_style_ref_id = "";
			var txt_quotation_id = "";
			var sign = 0;
			var txt_order = "";
			var txt_order_id = "";
			var txt_season_id = "";
			var txt_season = "";
			var txt_file_no = "";
			var data = "action=report_generate&reporttype=" + rpt_type +
				'&cbo_company_name=' + "'" + cbo_company_name + "'" +
				'&cbo_buyer_name=' + "'" + cbo_buyer_name + "'" +
				'&txt_style_ref=' + "'" + txt_style_ref + "'" +
				'&txt_style_ref_id=' + "'" + txt_style_ref_id + "'" +
				'&txt_order=' + "'" + txt_order + "'" +
				'&txt_order_id=' + "'" + txt_order_id + "'" +
				'&txt_season=' + "'" + txt_season + "'" +

				'&txt_season_id=' + "'" + txt_season_id + "'" +
				'&txt_file_no=' + "'" + txt_file_no + "'" +
				'&txt_quotation_id=' + "'" + txt_quotation_id + "'" +
				'&txt_hidden_quot_id=' + "'" + txt_quotation_id + "'" +
				'&comments_head=' + "'" + comments_head + "'" +
				'&sign=' + "'" + sign + "'" +
				'&report_title=' + "'" + report_title + "'" +
				'&path=../../../';

			http.open("POST", "../../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php", true);

			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = function() {
				if (http.readyState == 4) {
					var w = window.open("Surprise", "_blank");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><title></title></head><body>' + http.responseText + '</body</html>'); //<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
					d.close();
					release_freezing();
				}
			}
		} else {
			var rate_amt = 2;
			var zero_val = '';
			if (type != 'mo_sheet' && type != 'budgetsheet' && type != 'materialSheet' && type != 'materialSheet2' && type != 'mo_sheet_3') {
				var r = confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
			}

			if (type == 'materialSheet' || type == 'materialSheet2') {
				var r = confirm("Press \"OK\" to show Qty  Excluding Allowance.\nPress \"Cancel\" to show Qty Including Allowance.");
			}

			var excess_per_val = "";

			if (type == 'mo_sheet') {
				excess_per_val = prompt("Please enter your Excess %", "0");
				if (excess_per_val == null) excess_per_val = 0;
				else excess_per_val = excess_per_val;
			}

			if (type == 'budgetsheet') {
				var r = confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
			}

			if (type == 'mo_sheet_3') {
				excess_per_val = prompt("Please enter your Excess %", "0");
				if (excess_per_val == null) excess_per_val = 0;
				else excess_per_val = excess_per_val;
			}

			if (r == true) zero_val = "1";
			else zero_val = "0";
			var print_option_id = "";
			//company_name,job_no,style_ref_no,buyer_name,costing_date,po_ids,type
			//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
			var data = "action=" + type + "&zero_value=" + zero_val + "&rate_amt=" + rate_amt + "&excess_per_val=" + excess_per_val + "&txt_job_no='" + job_no + "'&cbo_company_name=" + company_name + "&cbo_buyer_name=" + buyer_name + "&txt_style_ref='" + style_ref_no + "'&cbo_costing_per=" + costing_date + "&print_option_id=" + print_option_id + "&txt_po_breack_down_id=" + po_ids;

			freeze_window(3);
			http.open("POST", "../woven_order/requires/pre_cost_entry_controller_v3.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_v3_reponse;
		}

	}

	function fnc_generate_report_v3_reponse() {
		if (http.readyState == 4) {
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>' + http.responseText + '</body</html>');
			d.close();
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

		//if(html_filter_print*1>1) $("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "400px";

		if (html_filter_print * 1 > 1) $("#table_body tr:first").show();
	}

	function generate_woven_report(company,job_no,buyer_name,style_ref_no,costing_date,po_id,type)
	{
		
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var rate_amt=2;
		var data="action="+type
			+"&rate_amt="+rate_amt
			+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name="+company
			+"&cbo_buyer_name="+buyer_name
			+"&txt_style_ref='"+style_ref_no
			+"'&txt_costing_date='"+costing_date
			+"'&txt_po_breack_down_id="+po_id
		;

			http.open("POST","../woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
	
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_woven_generate_report_reponse;
	}
	
	function fnc_woven_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}


	function generate_report(company, job_no, type) {
		//alert(type);
		if (type == 'summary') {
			var rpt_type = 3;
			var comments_head = 0;
		} else if (type == 'budget3_details') {
			var rpt_type = 4;
			var comments_head = 1;
		}
		var report_title = "Budget/Cost Sheet"; //report_title
		var zero_val = 1;
		var path = '../../';
		var data = "action=" + type + "&txt_job_no='" + job_no + "'&cbo_company_name='" + company + "'&reporttype='" + rpt_type + "'&comments_head='" + comments_head + "'&report_title=" + report_title + "'&zero_value=" + zero_val + "'&img_path=" + path;
		//alert(data);

		http.open("POST", "../woven_order/requires/pre_cost_entry_controller_v2.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}

	function fnc_generate_report_reponse() {
		if (http.readyState == 4) {
			/*var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();*/
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><title></title></head><body>' + http.responseText + '</body</html>');
			d.close();
		}
	}

	function openmypage(po_id, item_name, job_no, book_num, trim_dtla_id, action) { //alert(book_num);
		var cbo_company_name = $("#cbo_company_name").val();
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?po_id=' + po_id + '&item_name=' + item_name + '&job_no=' + job_no + '&book_num=' + book_num + '&trim_dtla_id=' + trim_dtla_id + '&action=' + action + '&cbo_company_name=' + cbo_company_name, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_booking_info(booking_dtls_id, action) { //alert(book_num);
		var cbo_company_name = $("#cbo_company_name").val();
		var popup_width = '650px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?booking_dtls_id=' + booking_dtls_id + '&action=' + action + '&cbo_company_name=' + cbo_company_name, 'Details Veiw', 'width=' + popup_width + ', height=400px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_inhouse(po_id, item_name, action) {
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?po_id=' + po_id + '&item_name=' + item_name + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_inhouse_info(tr_id, action) {
		var popup_width = '1090px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?tr_id=' + tr_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=400px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_issue(po_id, item_name, action) {
		var popup_width = '900px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?po_id=' + po_id + '&item_name=' + item_name + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function openmypage_issue_info(tr_id, action) {
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?tr_id=' + tr_id + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=400px,center=1,resize=0,scrolling=0', '../');
	}

	function order_qty_popup(company, job_no, po_id, buyer, action) {
		//alert(po_id);
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?company=' + company + '&job_no=' + job_no + '&po_id=' + po_id + '&buyer=' + buyer + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function order_req_qty_popup(company, job_no, po_id, buyer, rate, item_group, boook_no, description, country_id, trim_dtla_id, start_date, end_date, action) {
		//alert(country_id);
		var popup_width = '800px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?company=' + company + '&job_no=' + job_no + '&po_id=' + po_id + '&buyer=' + buyer + '&rate=' + rate + '&item_group=' + item_group + '&boook_no=' + boook_no + '&description=' + description + '&country_id_string=' + country_id + '&trim_dtla_id=' + trim_dtla_id + '&start_date=' + start_date + '&end_date=' + end_date + '&action=' + action, 'Details Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
	}

	function search_populate(str) {
		if (str == 1) document.getElementById('search_by_th_up').innerHTML = "Shipment Date";
		else if (str == 2) document.getElementById('search_by_th_up').innerHTML = "Precost Date";
	}

	function fnRemoveHidden(str) {
		document.getElementById(str).value = '';
	}

	function openmypage_job() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		} else {
			var data = $("#cbo_company_name").val() + "_" + $("#cbo_buyer_name").val() + "_" + $("#cbo_year").val() + "_" + $("#cbo_brand_id").val() + "_" + $("#cbo_season_name").val() + "_" + $("#cbo_season_year").val();
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?data=' + data + '&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theemailid = this.contentDoc.getElementById("txt_job_id").value;
				var theemailjob = this.contentDoc.getElementById("txt_job_no").value;
				var theemailstyle = this.contentDoc.getElementById("txt_style_ref").value;
				//var response=theemailid.value.split('_');
				if (theemailid != "") {
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(theemailid);
					$("#txt_job_no").val(theemailjob);
					$("#txt_style_ref").val(theemailstyle);
					release_freezing();
				}
			}
		}
	}

	function openmypage_po() {
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		} else {
			var data = $("#cbo_company_name").val() + "_" + $("#cbo_buyer_name").val() + "_" + $("#cbo_year").val() + "_" + $("#cbo_brand_id").val() + "_" + $("#cbo_season_name").val() + "_" + $("#cbo_season_year").val();
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_report_controller_v2.php?data=' + data + '&action=po_no_popup', 'Po No Search', 'width=450px,height=420px,center=1,resize=0,scrolling=0', '../')
			emailwindow.onclose = function() {
				var theemailid = this.contentDoc.getElementById("txt_selected_id").value;
				var theemailpo = this.contentDoc.getElementById("txt_selected").value;
				//var response=theemailid.value.split('_');
				if (theemailid != "") {
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_po_id").val(theemailid);
					$("#txt_order_no").val(theemailpo);
					release_freezing();
				}
			}
		}
	}


	function print_report_button_setting(report_ids) {
		$("#show_button1").hide();
		$("#show_button2").hide();
		$("#show_button3").hide();
		$("#show_button5").hide();
		$("#show_button6").hide();
		var report_id = report_ids.split(",");
		for (var k = 0; k < report_id.length; k++) {
			//alert(report_id[k]);


			if (report_id[k] == 178) {
				$("#show_button1").show();
			} else if (report_id[k] == 179) {
				$("#show_button2").show();
			} else if (report_id[k] == 180) {
				$("#show_button3").show();
			} else if (report_id[k] == 23) {
				$("#show_button5").show();
			} else if (report_id[k] == 825) {
				$("#show_button6").show();
			} else {
				$("#show_button1").hide();
				$("#show_button2").hide();
				$("#show_button3").hide();
				$("#show_button5").hide();
				$("#show_button6").hide();
			}
		}
	}

	function search_by(val, type) {
		$('#txt_date_from').val('');
		$('#txt_date_to').val('');

		if (val == 1) $('#search_by_th_up').html('Country Shipment Date');
		else if (val == 2) $('#search_by_th_up').html('Pub. Ship Date');
		else if (val == 3) $('#search_by_th_up').html('Org. Ship Date');
		else if (val == 4) $('#search_by_th_up').html('PO Insert Date');
		else $('#search_by_th_up').html('Shipment Date');
	}

	function fnc_brandload() {
		var buyer = $('#cbo_buyer_name').val();
		if (buyer != 0) {
			load_drop_down('requires/accessories_followup_report_controller_v2', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
</script>

</head>

<body onLoad="set_hotkey(); fnc_brandload();">
	<form id="accessoriesFollowup_report">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../", ''); ?>
			<h3 align="left" id="accordion_h1" style="width:1740px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:1740px;">
					<table class="rpt_table" width="1740px" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
						<thead>
							<tr>
								<th width="130" class="must_entry_caption">Company Name</th>
								<th width="130">Buyer Name</th>
								<th width="100">Prod. Dept</th>
								<th width="70">Brand</th>
								<th width="100">Team Leader</th>
								<th width="70">Season</th>
								<th width="70">Season Year</th>
								<th width="80">Type</th>
								<th width="60">Year</th>
								<th width="80">Job No</th>
								<th width="80">Style Ref.</th>
								<th width="70">Internal Ref.</th>
								<th width="70">File No</th>
								<th width="80">Order No</th>
								<th width="100">Item Group</th>
								<th width="80">Budget Version</th>
								<th width="80">Date Type</th>
								<th width="130" colspan="2" id="search_by_th_up">Country Shipment Date</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td><? echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/accessories_followup_report_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data(this.value, 'set_print_button', 'requires/accessories_followup_report_controller_v2' );"); ?> </td>
								<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, ""); ?></td>
								<td align="center">
									<?
									echo create_drop_down("cbo_dept", 120, $product_dept, "", 1, "-- Select Dept. --", $selected, "");
									?>
								</td>

								<td id="brand_td"><? echo create_drop_down("cbo_brand_id", 70, $blank_array, '', 1, "--Brand--", $selected, ""); ?></td>
								<td align="center">
									<?
									echo create_drop_down("cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-- Select --", 0, "", 0);
									?>
								</td>
								<td id="season_td"><? echo create_drop_down("cbo_season_name", 70, $blank_array, "", 1, "-- Select Season --", $selected, ""); ?></td>

								<td><?= create_drop_down("cbo_season_year", 70, create_year_array(), "", 1, "-Year-", $selected, ""); ?></td>
								<td><?
									$search_by_arr1 = array(1 => "Order Wise", 2 => "Style Wise");
									echo create_drop_down("cbo_search_by", 80, $search_by_arr1, "", 0, "", "", '', 0); //search_by(this.value)
									?>
								</td>
								<td><? $selected_year = date("Y");
									echo create_drop_down("cbo_year", 60, $year, "", 1, "--Select Year--", $selected_year, "", 0); ?></td>
								<td>
									<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id')" onDblClick="openmypage_job();">
									<input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:70px" />
								</td>
								<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id')" onDblClick="openmypage_job();"></td>
								<td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
								<td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
								<td>
									<input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_po_id')" onDblClick="openmypage_po();">
									<input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:70px" />
								</td>
								<td><? echo create_drop_down("cbo_item_group", 100, "select item_name,id from lib_item_group where is_deleted=0 and status_active=1 order by item_name", "id,item_name", 0, "", $selected, ""); ?></td>

								<td>
									<?
									// $pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
									$pre_cost_class_arr = array(2 => 'Pre Cost 2');
									echo create_drop_down("cbo_budget_version", 80, $pre_cost_class_arr, "", 0, "--Select--", 2, "", 0);
									?>
								</td>
								<td>
									<?
									$date_type_arr = array(1 => "Country Ship Date", 2 => "Pub. Ship Date", 3 => "Org. Ship Date", 4 => "PO Insert Date");
									echo create_drop_down("cbo_date_type", 80, $date_type_arr, "", 0, "-Select-", 1, "search_by(this.value,1);", 0, "");
									?>
								</td>
								<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
								<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"></td>

								<td>
									<input type="button" id="show_button1" class="formbutton" style="width:70px;display:none;" value="Show" onClick="fn_report_generated('report_generate')" />

								</td>
							</tr>
						</tbody>
					</table>
					<table>
						<tr>
							<td>
								<? echo load_month_buttons(1); ?>
								&nbsp;&nbsp;&nbsp;
								<input type="button" id="show_button3" class="formbutton" style="width:100px;display:none;" value="Group By Style" onClick="fn_report_generated('report_generate3')" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


								<input type="button" id="show_button4" class="formbutton" style="width:100px;" value="Excel Generate" onClick="fn_report_generated('report_generate4')" /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

								<input type="button" id="show_button2" class="formbutton" style="width:100px;display:none;" value="Show With Html" onClick="fn_report_generated('report_generate2')" />
								<input type="button" id="show_button5" class="formbutton" style="width:70px;display:none;" value="Summary" onClick="fn_report_generated('report_generate5')" />
								<input type="button" id="show_button6" class="formbutton" style="width:90px;display:none;" value="Rcv Summary" onClick="fn_report_generated('report_generate6')" />

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