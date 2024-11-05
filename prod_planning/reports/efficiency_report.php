<?php
/*********************************************** Comments *************************************
*	Purpose			:   This Form Will Create Efficiency report
*	Functionality	:
*	JS Functions	:
*	Created by		:	Sapayth Hossain
*	Creation date 	: 	31-10-2020
*	Updated by 		:
*	Update date		:
*	QC Performed BY	:
*	QC Date			:
*	Comments		:
************************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Cutting Status Report', '../../', 1, 1, $unicode, 1, '');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
	var permission = '<?php echo $permission; ?>';
	var isValidated = false;

	function loadLocation() {
		var companyIds = document.getElementById('cbo_company_name').value;
		var workingCompanyIds = document.getElementById('cbo_working_company_name').value;
		var allIds = companyIds + ',' + workingCompanyIds;
		allIds = allIds.replace(/,\s*$/, ""); // remove last comma or whitespace

		load_drop_down('requires/efficiency_report_controller', allIds, 'load_drop_down_location', 'td_location');
		set_multiselect('cbo_location_name', '0', '0', '', '0');
		setTimeout[($("#td_location a").attr("onclick","disappear_list(cbo_location_name,'0');loadLine();"), 3000)];
	}

	function loadBuyer() {
		var companyIds = document.getElementById('cbo_company_name').value;
		load_drop_down('requires/efficiency_report_controller', companyIds, 'load_drop_down_buyer', 'buyer_td');
	}

	function loadLine() {
		var locationIds = document.getElementById('cbo_location_name').value;
		locationIds = locationIds.replace(/,\s*$/, ""); // remove last comma or whitespace

		load_drop_down('requires/efficiency_report_controller', locationIds, 'load_drop_down_sewing_output_line', 'sewing_line_td');

		// set_multiselect('cbo_location_name', '0', '0', '', '0');
	}

	function openStylePopup(type) {
		if( form_validation('cbo_company_name', 'Company')==false ) { return; }

		var cbo_company_name = document.getElementById('cbo_company_name').value;// $("#cbo_company_name").val();	
		var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
		var cbo_job_year = document.getElementById('cbo_year_selection').value;

		var page_link='requires/efficiency_report_controller.php?action=style_search_popup&company='+cbo_company_name+'&buyer='+cbo_buyer_name+'&job_year='+cbo_job_year;
		var title="Style Reference";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function() {
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var data=style_no.split("_");
			if(type == 1) {
				$('#txt_job_no').val(data[1]);
			} else {
				$('#txt_style_no').val(data[2]);
			}
		}
	}

	function openOrderPopup() {
		if(form_validation('cbo_company_name', 'Company')==false) {
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var job_no = $("#txt_job_no").val();
		var cbo_job_year = $("#cbo_year_selection").val();
		var page_link='requires/efficiency_report_controller.php?action=order_search_popup&companyID='+companyID+'&job_no='+job_no+'&job_year='+cbo_job_year;
		var title='Order No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function() {
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hdnOrderNo").value;
			// var order_id=this.contentDoc.getElementById("hdnOrderId").value;

			/*$('#hdn_order_no').val(order_no);
			$('#hdn_order_id').val(order_id);*/
			// var orderNoStr = order_no.replace(/\*/g, ',');
			$('#txt_order_no').val(order_no);
		}		
	}

	function checkFields() {
		var buyer = document.getElementById('cbo_buyer_name').value;
		var styleNo = document.getElementById('txt_style_no').value;
		var jobNo = document.getElementById('txt_job_no').value;
		var orderNo = document.getElementById('txt_order_no').value;

		if(buyer == 0 && styleNo == '' && jobNo == '' && orderNo == '') {
			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To') ) {
				return;
			}
		}

		isValidated = true;
	}

	function generateReport(type) {
		if(form_validation('show_textcbo_working_company_name', 'Working Company')==false) {
			return;
		}

		if(!isValidated) {
			return;
		}

		var dataString = get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_location_name*cbo_buyer_name*txt_style_no*txt_job_no*txt_order_no*cbo_sewing_line*txt_date_from*txt_date_to*cbo_year_selection', '../../');

		data ="action=generate_report"+"&type="+type+dataString;
		
		
		freeze_window(5);
		http.open("POST","requires/efficiency_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		if (type==3) {
			http.onreadystatechange = generate_report_reponse3;
		}else{
			http.onreadystatechange = generate_report_reponse;
		}

		isValidated = false;
	}

	function generate_report_reponse() {
	 	if(http.readyState == 4) {
	 		var response=trim(http.responseText).split('**');
	        // $("#report_container").html(response[0]);
	        document.getElementById('report_container').innerHTML = response[0];
	        document.getElementById('report_summery_container').innerHTML = response[1];

			document.getElementById('report_container3').innerHTML = '<a href="requires/' + response[2] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	        var tableFilters = {
			   	col_operation:
			   {
			   	id: ["total_production_qty","total_produce_minutes","total_operator","total_helper","total_manpower","total_available_minutes"],
			   	col: [12,13,14,15,16,18],
			   	operation: ["sum","sum","sum","sum","sum","sum"],
			   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("report1_body", -1, tableFilters);
			release_freezing();
	 	}
	}
	function generate_report_reponse3() {
		if(http.readyState == 4) {
	 		var response=trim(http.responseText).split('**');
	        // $("#report_container").html(response[0]);
	        document.getElementById('report_container').innerHTML = response[0];
			response[1] = response[1] ?? "";  
			document.getElementById('report_summery_container').innerHTML = response[1]
			document.getElementById('report_container3').innerHTML = '<a href="requires/' + response[2] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("report1_body", -1);
			release_freezing();
	 	}
	}

	function new_window()
	{
		$('#report1_body tr:nth-child(1)').css('display','none');

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' + '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body><div style="margin: 10px auto; display: inline-flex;">' + document.getElementById('report_summery_container').innerHTML + '</div>' + document.getElementById('report_container').innerHTML + '</body</html>'); // media="print"
		d.close();
		
		$('#report1_body tr:nth-child(1)').css('display','');
	}

	function fnc_efc_details(poNo, prodMstId) {
		var action = 'production_details';
		var popupTitle = 'Production Details';
	    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/efficiency_report_controller.php?poNo='+poNo+'&prodMstId='+prodMstId+'&action='+action, popupTitle, 'width=1000px,height=320px,center=1,resize=0', '../../');
	    // emailwindow.onclose=function() {}
	}

</script>
</head>
<?
	$width="1330";
?>
<body>
	<div style="width:<?= $width+20?>px;" align="center">
		<?php echo load_freeze_divs('../', $permission); ?>
    	<form id="efficiencyReport_1">
			<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel">      
				<fieldset style="width:<?= $width+20?>px;">
					<table class="rpt_table" width="<?= $width?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
					       	<th>Company</th>
					       	<th class="must_entry_caption">Working Company</th>
					       	<th>Location</th>
					        <th>Buyer</th>
					        <th>Style</th>
					        <th>Job No</th>
					        <th>Order</th>
					        <th>Line</th>
					        <th colspan="2" id="date_range">Date Range</th>
					        <th width="230"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('efficiencyReport_1','','','','');resetWorkingComp();" class="formbutton" style="width:50px" /></th>
					    </thead>
					    <tbody>
					        <tr class="general">
					        	<td id="td_company">
					            <?php
					               echo create_drop_down('cbo_company_name', 142, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "loadBuyer();");
					             ?>
					            </td>
					            <td id="td_wk_company">
					            <?php
					               echo create_drop_down('cbo_working_company_name', 142, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 0, '-- Select  --', $selected, '');
					             ?>
					            </td>
					            <td id="td_location">
						            <?php
						               echo create_drop_down('cbo_location_name', 142, $blank_array, '', 0, '-- Select --', $selected, '');
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
					                <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openStylePopup(1);" readonly />
					            </td>
					            <td>
					                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openOrderPopup();" readonly />
					            </td>
					            <td id="sewing_line_td">
					            	<?php
					            		echo create_drop_down('cbo_sewing_line', 120, $blank_array, '', 1, '-- Select Line --', $selected, '', '', '');
					            	?>
					            </td>
					            <td>
					            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" readonly>
					            </td>
					            <td>
					            	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
					            </td>
					            <td>
					            	<input type="button" id="show_powise" class="formbutton" style="width:70px;" value="Show" onClick="checkFields();generateReport(1)" />
					            	<input type="button" id="show_stylewise" class="formbutton" style="width:70px;" value="Show 2" onClick="checkFields();generateReport(2)" />
					            	<input type="button" id="show_stylewise" class="formbutton" style="width:70px;" value="Show 3" onClick="checkFields();generateReport(3)" />

					            	<input type="hidden" id="hdn_style_ref_id" name="hdn_style_ref_id" />
					            	<input type="hidden" id="hdn_order_id" name="hdn_order_id" />
					            	<input type="hidden" id="hdn_order_no" name="hdn_order_no" />
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
    	<div id="report_summery_container" style="margin: 30px auto; display: inline-flex;"></div>
    	<div id="report_container" style="margin: 30px 0 50px 0; width: 98%;"></div>
	</div>
</body>
<script>
	// set_multiselect('cbo_company_name', '0', '0', '', '0');
	set_multiselect('cbo_working_company_name', '0', '0', '', '0');
	set_multiselect('cbo_location_name','0','0','','0');
	// set_multiselect('cbo_working_floor_id','0','0','','0');

	// setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');loadLocation();loadBuyer();"), 3000)];
	setTimeout[($("#td_wk_company a").attr("onclick","disappear_list(cbo_working_company_name,'0');loadLocation();"), 3000)];

	function resetWorkingComp() {
		load_drop_down( 'requires/efficiency_report_controller', '', 'load_drop_down_working_company', 'td_wk_company');
		set_multiselect('cbo_working_company_name', '0', '0', '', '0');
	}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>