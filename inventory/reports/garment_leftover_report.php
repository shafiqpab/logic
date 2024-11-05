<?php
/*********************************************** Comments *************************************
*	Purpose			:
*	Functionality	:
*	JS Functions	:
*	Created by		:	Sapayth Hossain
*	Creation date 	: 	23-09-2020
*	Updated by 		:	Shafiq
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
echo load_html_head_contents('Cutting Status Report', '../../', 1, 1, $unicode, 1, 1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
var permission = '<?php echo $permission; ?>';

window.onload = yearListeners;

function yearListeners() {
	document.getElementById('cbo_year_selection').onchange = function() {
		document.getElementById('cbo_job_year').value = document.getElementById('cbo_year_selection').value;
	};

	document.getElementById('cbo_job_year').onchange = function() {
		document.getElementById('cbo_year_selection').value = document.getElementById('cbo_job_year').value;
	};
}

function loadBuyer() {
	var workingCompanyIds = document.getElementById('cbo_working_company_name').value;
	// load_drop_down( plink, data, action, container );
	load_drop_down('requires/garment_leftover_report_controller', workingCompanyIds, 'load_drop_down_buyer', 'buyer_td');
}

function loadLocation() {
	var workingCompanyIds = document.getElementById('cbo_working_company_name').value;

	load_drop_down('requires/garment_leftover_report_controller', workingCompanyIds, 'load_drop_down_working_location', 'working_location_td');

	set_multiselect('cbo_working_location_name', '0', '0', '', '0');
	setTimeout[($("#working_location_td a").attr("onclick", "disappear_list(cbo_working_location_name,'0');loadFloor();"), 3000)];
}

function loadFloor() {
	var workingLocationIds = document.getElementById('cbo_working_location_name').value;

	load_drop_down('requires/garment_leftover_report_controller', workingLocationIds, 'load_drop_down_working_floor', 'working_floor_td');
	set_multiselect('cbo_working_floor_id', '0', '0', '', '0');
}

function openStylePopup() {
	if( form_validation('show_textcbo_working_company_name', 'Working Company')==false ) { return; }

	var cbo_working_company_name = document.getElementById('cbo_working_company_name').value;// $("#cbo_company_name").val();	
	var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
	var cbo_job_year = document.getElementById('cbo_job_year').value;
	/* var txt_style_ref_id = $("#txt_style_ref_id").val();
	var txt_style_ref = $("#txt_style_ref").val();*/

	var page_link='requires/garment_leftover_report_controller.php?action=style_search_popup&company='+cbo_working_company_name+'&buyer='+cbo_buyer_name+'&job_year='+cbo_job_year;
	var title="Style Reference";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function() {
		var theform=this.contentDoc.forms[0];
		var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		var data=style_no.split("_");
		$('#hdn_style_ref_id').val(data[0]);
		$('#txt_job_no').val(data[1]);
		// $('#hdn_job_no').val(data[1]); 
  		$('#txt_ref_no').val(data[2]);
  		$('#txt_job_no').attr('disabled','true'); 
	}
}

function openJobPopup() {
	console.log('job popup');
}
function openPoPopup() {
	if(form_validation('show_textcbo_working_company_name', 'Working Company')==false) {
		return;
	}
	
	var companyID = $("#cbo_working_company_name").val();
	var job_no = $("#txt_job_no").val();
	var cbo_job_year = document.getElementById('cbo_job_year').value;
	var page_link='requires/garment_leftover_report_controller.php?action=pono_search_popup&companyID='+companyID+'&job_no='+job_no+'&job_year='+cbo_job_year;
	var title='Order No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function() {
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hdnOrderNo").value;
		var order_id=this.contentDoc.getElementById("hdnOrderId").value;

		$('#hdn_order_no').val(order_no);
		$('#hdn_order_id').val(order_id);
		var orderNoStr = order_no.replace(/\*/g, ',');
		$('#txt_po_no').val(orderNoStr);
	}		
}

function generateReport(type) {
	if( !form_validation('show_textcbo_working_company_name', 'Working Company') ) { return; }

	var data = '';
	var jobNo = document.getElementById('txt_job_no').value;
	var poNo = document.getElementById('txt_po_no').value;
	var dateFrom = document.getElementById('txt_date_from').value;
	var dateTo = document.getElementById('txt_date_to').value;

	if(jobNo.trim()=='' && poNo.trim()=='') {
		if( !form_validation('txt_date_from*txt_date_to', 'Date From*Date To') ) { return; }
	}


	var dataString = get_submitted_data_string('cbo_working_company_name*cbo_working_location_name*cbo_working_floor_id*cbo_job_year*cbo_buyer_name*txt_ref_no*txt_job_no*txt_po_no*txt_date_from*txt_date_to', '../../../');

	if(type==1) {
		data ="action=generate_report_powise"+dataString;
	} else {
		data ="action=generate_report_stylewise"+dataString;
	}
	
	freeze_window(5);
	http.open("POST","requires/garment_leftover_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;
}

function generate_report_reponse() {
 	if(http.readyState == 4) {
 		var response=trim(http.responseText).split('**');
        // $("#report_container").html(response[0]);
        document.getElementById('report_container').innerHTML = response[0];
        document.getElementById('preview-buttons').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		release_freezing();
 	}		
}

function new_window() {
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	d.close();
}

function fnc_qty_details(poBreakDownIds, workingCompanyId, action, popupTitle) {
	var dateFrom = document.getElementById('txt_date_from').value;
	var dateTo = document.getElementById('txt_date_to').value;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/garment_leftover_report_controller.php?poBreakDownIds='+poBreakDownIds+'&workingCompanyId='+workingCompanyId+'&dateFrom='+dateFrom+'&dateTo='+dateTo+'&action='+action, popupTitle, 'width=1000px,height=320px,center=1,resize=0', '../../');
    // emailwindow.onclose=function() {}
}

</script>

</head>
 
<body>
    <div style="width:100%;" align="center">
    	<?php echo load_freeze_divs('../', $permission); ?>
    	<form id="garmentLeftoverReport_1">
			<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel">      
			<fieldset style="width:99%;">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				 	<thead>
				       	<th class="must_entry_caption">Working Company</th>
				       	<th>Working Location</th>
				       	<th>Working Floor</th>
				       	<th>Year</th>
				        <th>Buyer Name</th>
				        <th>Style Ref.</th>
				        <th>Job No</th>
				        <th>PO NO.</th>
				        <th colspan="2" id="date_range"><abbr title="Receive Date Range">Date Range</abbr></th>
				        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('garmentLeftoverReport_1','','','','')" class="formbutton" style="width:50px" /></th>
				    </thead>
				    <tbody>
				        <tr class="general">
				            <td id="td_wk_company">
				            <?php
				               echo create_drop_down('cbo_working_company_name', 142, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 0, '-- Select  --', $selected, '');// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
				             ?>
				            </td>
				            <td id="working_location_td">
				            <?php
				               echo create_drop_down('cbo_working_location_name', 142, $blank_array, '', 0, '-- Select  --', $selected, '');// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
				             ?>
				            </td>
							<td id="working_floor_td">
								<?php
									echo create_drop_down('cbo_working_floor_id', 142, $blank_array, '', 0, '-- Select --', $selected, '', 1, '');
				                ?>
				            </td>
				            <td>
				            	<?php
									$selected_year=date('Y');
									echo create_drop_down('cbo_job_year', 60, $year, '', 1, '--All--', $selected_year, '', 0, '',  '');
				                ?>
				            </td>
				            <td id="buyer_td">
				            	<?php
				            	echo create_drop_down('cbo_buyer_name', 120, $blank_array, '', 1, '-- Select Buyer --', $selected, '', '', '');
				            	?>
				            </td>
				            <td>
					            <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openStylePopup();" readonly  />
					        </td>
				            <td>
				                <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openJobPopup();"  disabled  />
				            </td>
				            <td>
				                <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openPoPopup();" autocomplete="off">
				            </td>
				            
				            <td>
				            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" readonly>
				            </td>
				            <td>
				            	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
				            </td>
				            <td>
				            	<input type="button" id="show_powise" class="formbutton" style="width:70px;" value="PO Wise" onClick="generateReport(1)" />
				            	<input type="button" id="show_stylewise" class="formbutton" style="width:70px;" value="Style Wise" onClick="generateReport(2)" />

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
    	<div id="preview-buttons" style="margin: 50px 0;"></div>
    	<div id="report_container"></div>
    </div>
</body>
<script>
	// var workingCompanies = document.getElementById().value;
	set_multiselect('cbo_working_company_name', '0', '0', '', '0');
	set_multiselect('cbo_working_location_name','0','0','','0');
	set_multiselect('cbo_working_floor_id','0','0','','0');

	setTimeout[($("#td_wk_company a").attr("onclick","disappear_list(cbo_working_company_name,'0');loadLocation();loadBuyer();") ,3000)];

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>