<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Reconciliation Report.
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	01-01-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//-----------------------------------------------------------------------------
echo load_html_head_contents('Fabric Reconciliation Report', '../../', 1, 1,'', '', '');
?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';  
var permission = '<? echo $permission; ?>';

var tableFilters =
{	
	col_operation: {
		//37
		id: ['tot_order_qty_id','tot_plan_cut_qty_id','value_tot_yarn_qty','value_tot_sam_booking_grey_qnty','value_tot_shortFabBooking_grey_fab_qnty','value_tot_total_yarn_req_qty','value_tot_issue_qnty','value_tot_issue_balance','value_tot_grey_fab_rec_qty','value_tot_grey_rec_bal_qty','value_tot_grey_issue_rollwise','value_tot_net_grey_transfer','value_tot_gray_stockinhand','value_tot_fin_fab_req','value_tot_shortFabBooking_fin_fab_qnty','value_tot_total_fin_fab_req_qty','value_tot_grey_used_qty','value_tot_fin_fab_rec_qty',
		'value_tot_fab_meter_qnty','value_tot_fab_store_rec_bal','value_tot_finish_issue','value_tot_sample_issue','value_tot_finish_iss_re_process', 'value_tot_fin_net_trans_qnty','value_tot_finish_iss_scrap','value_tot_fab_stock_in_hand', 'tot_possible_cutt_pcs','tot_cutting_qty','tot_cutting_bal','value_tot_actual_cutt_cons','value_tot_fab_used_kg','value_tot_cutting_westage_kg','value_tot_cutting_westage_prsnt','value_tot_cutting_in_hand_kg','value_tot_fin_iss_retn_qnty','value_tot_cut_rtn_reprocess','value_tot_all_stock'],
		
		col: [7,8,12,13,14,	15,16,17,18,19,	20,21,22,23,25,	26,28,29,31,32,33,34,35,36,37,	38,39,40, 41,43,44,	45,46 ,47,48,49,50],
		operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
		write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
	}
}

 
function fn_report_generated()
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}

	var cbo_report_type = $('#cbo_report_type').val();
	if (cbo_report_type == 2)
	{
		var job_no   = $('#txt_job_no').val();
		var style_no = $('#txt_style_no').val();
		if (job_no == '' && style_no == '')
		{
			if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}
	}
	
	var data='action=report_generate'+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_job_year*txt_job_no*txt_style_no*cbo_report_type*cbo_fabric_nature*txt_date_from*txt_date_to','../../');
	freeze_window(3);
	http.open('POST','requires/fabric_reconciliation_report_controller.php',true);
	http.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;

}	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split('####');
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid('table_body',-1,tableFilters);
		show_msg('3');
		release_freezing();
 	}
	
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none';
	$('#table_body tbody').find('tr:first').hide();
	var w = window.open('Surprise', '#');
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('#table_body tbody').find('tr:first').show();
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='300px';	
}

function change_color(v_id,e_color)
{
	if (document.getElementById(v_id).bgColor=='#33CC00')
	{
		document.getElementById(v_id).bgColor=e_color;
	}
	else
	{
		document.getElementById(v_id).bgColor='#33CC00';
	}
}

function is_disabled()
{
	var report_type = $('#cbo_report_type').val();
	if (report_type == 2)
	{		
		$('#txt_date_from').removeAttr('disabled');
		$('#txt_date_to').removeAttr('disabled');
	}
	else
	{
		$('#txt_date_from').val('');
		$('#txt_date_to').val('');
		$('#txt_date_from').attr('disabled','disabled');
		$('#txt_date_to').attr('disabled','disabled');
	}	
}

function openmypage_popup(data, order_id, action, popup_width)
{  		
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_reconciliation_report_controller.php?data='+data+'&order_id='+order_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}

</script>
</head>
 
<body onLoad="set_hotkey();">
	<form id="fabricReconciliationReport_1">
		<div style="width:100%;" align="center">			
			<? echo load_freeze_divs ("../../",'');  ?>			
			<h3 style="width:1100px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
			<div id="content_search_panel" >
				<fieldset style="width:1100px;">					
					<table class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th class="must_entry_caption" width="140">Company Name</th>
							<th width="140">Buyer Name</th>
							<th width="90">Job Year</th>
							<th width="100">Job No.</th>
							<th width="100">Style No.</th>
							<th width="100">Report Type</th>
							<th width="100">Fabric Nature</th>
							<th colspan="2" width="200">Close Date</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:90px" /></th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									<?
									echo create_drop_down( 'cbo_company_name', 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select --', $selected, "load_drop_down( 'requires/fabric_reconciliation_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
									?>
								</td>
								<td id="buyer_td">
									<?
									echo create_drop_down( 'cbo_buyer_name', 140, $blank_array,'', 1, '-- All Buyer --', $selected, '',0,'' );
									?>
								</td>
								<td >
									<?
									echo create_drop_down( 'cbo_job_year', 80, create_year_array(),'', 1,'-- All --', date('Y',time()), '',0,'' );
									?>
								</td>
								<td>
									<input name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write" style="width:100px">
								</td>
								<td>
									<input name="txt_style_no" id="txt_style_no" class="text_boxes" placeholder="Write" style="width:100px">
								</td>
								<td>
									<?
									$report_type=array(1=>'Running', 2=>'Closed');
									echo create_drop_down( 'cbo_report_type', 100, $report_type, 1, '-- Select --', $selected, '', 'is_disabled()');
									?>
								</td>
								<td>
									<?
									$fabric_nature_arr = array(2=>'Knit Fabric', 3=>'Woven Fabric');
									echo create_drop_down( 'cbo_fabric_nature', 100, $fabric_nature_arr,'', 1, '-- All --', $selected, '',0 );
									?>
								</td>								
								
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" disabled="disabled">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" disabled="disabled">
								</td>
								<td>
									<input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated()" />
								</td>
							</tr>
						</tbody>
					</table>
					<br/>
				</fieldset>
			</div>
		</div>
		<div id="report_container" align="center"></div>
		<div id="report_container2" align="left"></div>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>