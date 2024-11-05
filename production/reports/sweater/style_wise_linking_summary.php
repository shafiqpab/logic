<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Style Wise Linking Summary Report
Functionality	:	
JS Functions	:
Created by		:	Thorat
Creation date 	: 	17-07-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Style Wise Linking Summary Report", "../../../", 1, 1, $unicode, 1);
?>
<script src="../../../js/highchart/highcharts.js"></script>
<script src="../../../js/highchart/highcharts-3d.js"></script>
<script src="../../../js/highchart/exporting.js"></script>
<script>
	var permission = '<? echo $permission; ?>';
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";

	var tableFilters = {
		/*col_operation: {
			id: ["value_order_qty"],
			col: [6],
			operation: ["sum"], 
			write_method: ["innerHTML"]
		}*/
	}
	var tableFilters_1 = {
		/*col_operation: {
			id: ["value_order_qty_1","value_linking_rec_qty_1"],
			col: [6,8],
			operation: ["sum","sum"], 
			write_method: ["innerHTML","innerHTML"]
		}*/
	}

	function generate_report(type) {
		if (form_validation('cbo_company_id*txt_date_from', 'Date') == false) {
			return;
		}

		var report_title = $("div.form_caption").html();

		var data = "action=report_generate" + get_submitted_data_string('cbo_company_id*cbo_wo_company_id*cbo_buyer_id*txt_job_no*txt_style_ref_no*hide_job_id*cbo_shipment_status*txt_date_from', "../../../") + '&report_title=' + report_title + '&type=' + type;

		// alert(data); return;
		freeze_window(3);
		http.open("POST", "requires/style_wise_linking_summary_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function() {
			if (http.readyState == 4) {
				$("#report_container2").html('');
				var reponse = trim(http.responseText).split("####");

				// alert(reponse[2]);
				if (reponse[2] == '2') {
					showChart(reponse[3], reponse[4], reponse[5]);
					release_freezing();
					return;
				}
				if (reponse[2] == '4') {
					showChart2(reponse[3], reponse[4], reponse[4], reponse[5]);
					release_freezing();
					return;
				}

				$("#report_container2").html(reponse[0]);
				document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if (reponse[2] == '1') {
					setFilterGrid("table_body", -1, tableFilters);
				} else if (reponse[2] == '3') {
					setFilterGrid("table_body_1", -1, tableFilters_1);
				}

				show_msg('3');
				release_freezing();
			}
		}
	}

	function new_window() {
		document.getElementById('scroll_body').style.overflow = "auto";
		document.getElementById('scroll_body').style.maxHeight = "none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
			'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY = "scroll";
		document.getElementById('scroll_body').style.maxHeight = "450px";

		$("#table_body tr:first").show();
	}

	function open_job_no() {
		if (form_validation('cbo_company_id', 'Company Name') == false) {
			return;
		}
		var company = $("#cbo_company_id").val();
		var buyer = $("#cbo_buyer_id").val();
		var page_link = 'requires/style_wise_linking_summary_controller.php?action=job_no_search_popup&company=' + company + '&style=0';
		var title = "Search Order Popup";
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0', '../../')
		emailwindow.onclose = function() {
			var theform = this.contentDoc.forms[0];
			var prodID = this.contentDoc.getElementById("hide_job_id").value;
			//alert(prodID); // product ID
			var prodDescription = this.contentDoc.getElementById("hide_job_no").value; // product Description
			$("#txt_job_no").val(prodDescription);
			$("#hide_job_id").val(prodID);
			//alert($("#hidden_job_id").val())
		}
	}


	function fnc_bundelDtls(companyid, bundleNo, action) {
		var popup_width = '1100px';
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_linking_summary_controller.php?companyid=' + companyid + '&bundleNo=' + bundleNo + '&action=' + action, 'Details View', 'width=' + popup_width + ', height=350px,center=1,resize=0,scrolling=0', '../');
	}

	function showChart(styleName, defectQty, rejectQty) {
		// $("#chart_container").show('fast');
		var style_name_arr = styleName.split('__');
		// var style_name_arr = style_name_arr.toString();
		var defect_total_arr = defectQty.split('__');
		var defect_total_arr = defect_total_arr.map(Number);

		var reject_total_arr = rejectQty.split('__');
		var reject_total_arr = reject_total_arr.map(Number);
		// alert(value);

		Highcharts.chart('report_container2', {
			chart: {
				type: 'column',
				options3d: {
					enabled: true,
					alpha: 5,
					beta: 5,
					depth: 70
				}
			},
			title: {
				text: 'Style Wise Delivery to Linking Report Chart',
				style: {
					color: 'black',
					fontSize: '22px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				useHTML: true,
				align: 'center',
				y: 40,
				text: '<b>Style wise Alter% And Defect%</b>'
			},
			plotOptions: {
				column: {
					depth: 25,
				},
				series: {
					dataLabels: {
						align: 'center',
						enabled: true,
					}
				}
			},
			xAxis: {
				categories: style_name_arr,
				labels: {
					skew3d: true,
					style: {
						fontSize: '14px',
						color: 'black',
						fontWeight: 'bold'
					}
				},
			},
			yAxis: {
				title: {
					text: 'Style wise Alter% And Defect%',
					style: {
						color: 'black',
						fontSize: '14px',
						fontWeight: 'bold'
					}
				}
			},
			credits: {
				enabled: false
			},
			series: [{
					name: ['Defect%'],
					data: defect_total_arr,
					color: '#3DB2FF'
				},
				{
					name: ['Reject%'],
					data: reject_total_arr,
					color: '#FF2442'
				}
			]
		});

	}
</script>

<style type="text/css">
	/*#report_container2 {
      height: 400px;
      min-width: 400px;
      max-width: 960px;
      margin: 0 auto;
    }*/
</style>
</head>

<body onLoad="set_hotkey();">
	<form id="bundleTrackReport_1">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs("../../../", '');  ?>
			<h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel">
				<fieldset style="width:900px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th width="120" class="must_entry_caption">Company Name</th>
							<th width="120">Working Company</th>
							<th width="120">Buyer</th>
							<th width="80">Job No</th>
							<th width="100">Style Ref. No.</th>
							<th width="120" class="">Shipment Status</th>
							<th width="110" class="must_entry_caption" id="process_name">Linking Production Date </th>
							<th width="70">
								<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form( 'bundleTrackReport_1', 'report_container','','','')" />
							</th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<? echo create_drop_down("cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/style_wise_delivery_to_kniting_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>

								</td>
								<td>
									<? echo create_drop_down("cbo_wo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", 0, ""); ?>

								</td>

								<td id="buyer_td">
									<? echo create_drop_down("cbo_buyer_id", 120, $blank_array, "", 1, "-- Select Buyer --", 0, ""); ?></td>
								<td>
									<input type="text" id="txt_job_no" name="txt_job_no" style="width:70px" class="text_boxes" placeholder="Wr/Br" onDblClick="open_job_no();" />
									<input type="hidden" name="hide_job_id" id="hide_job_id" value="">
								</td>
								<td>
									<input type="text" id="txt_style_ref_no" name="txt_style_ref_no" style="width:90px" class="text_boxes" placeholder="Wr/Br" onDblClick="open_job_no();" />
								</td>
								<td id="">
									<? echo create_drop_down("cbo_shipment_status", 120, $shipment_status, "", 1, "-- All --", 0, ""); ?>
								</td>

								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:110px" placeholder="From Date">&nbsp;
									<!-- <input name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  placeholder="To Date"  ></td> -->
								<td>
									<input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" />

								</td>
							</tr>
							<tr>
								<td colspan="8">
									<? echo load_month_buttons(1); ?>
								</td>
							</tr>
						</tbody>
					</table>
					<table>

					</table>
				</fieldset>
			</div>
		</div>
		<div id="report_container" align="center" style="padding: 5px 0;"></div>
		<div id="report_container2" align="left">
			<!-- <div id="chart_container"></div> -->
		</div>
	</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>