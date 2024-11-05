<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create LC Wise Export Status Report.
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	03-03-2020
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

//--------------------------------------------------------------------------------------
echo load_html_head_contents('LC Wise Export Status Report', '../../', 1, 1,'','','');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';

 	function openmypage_lc()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var cbo_company_name = $("#cbo_company_name").val();	
		var page_link='requires/lc_wise_export_status_report_controller.php?action=lc_popup&cbo_company_name='+cbo_company_name; 
		var title='LC Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var lc_sc_id=this.contentDoc.getElementById("hidden_lc_sc_id").value; // lc_sc ID
			var lc_sc_no=this.contentDoc.getElementById("hidden_lc_sc_no").value; // lc_sc no
			$("#txt_lc_sc_no").val(lc_sc_no);
			$("#txt_lc_sc_id").val(lc_sc_id);
		}
	}

	function generate_report(operation)
	{
		var lc_sc_id = $("#txt_lc_sc_id").val();
		var file_no = $("#txt_file_no").val();
		//alert(lc_sc_id);return;
		if (lc_sc_id != '' || file_no != '')
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false)
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*txt_file_no*txt_lc_sc_id*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/lc_wise_export_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="formlcwiseexportstatus" name="formlcwiseexportstatus">
			<div style="width:850px;">
				<h3 align="left" id="accordion_h1" style="width:850px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:850px;">
						<table class="rpt_table" cellspacing="0" cellpadding="0" width="850" border="1" rules="all">
							<thead>
								<th width="150" class="must_entry_caption">Company</th>
								<th width="150">Buyer</th>
								<th width="120">File No</th>
								<th width="120">LC No</th>
								<th width="180" class="must_entry_caption">LC Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('formlcwiseexportstatus','report_container*report_container2','','','')" /></th>
							</thead>
							<tbody>
								<tr>
									<td>
										<?
										echo create_drop_down( 'cbo_company_name', 150, "select id, company_name from lib_company where status_active =1 and is_deleted=0 and core_business in(1,3) order by company_name",'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down( 'requires/lc_wise_export_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
										?>
									</td>
									<td id="buyer_td">
										<?
										echo create_drop_down( 'cbo_buyer_name', 150, $blank_array, '', 1, '-- All Buyer --', $selected, '', 0, '' ); ?>						
									</td>
									<td>
										<input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:120px;" placeholder="Write" >
									</td>
									<td>
										<input  type="text" style="width:120px;"  name="txt_lc_sc_no" id="txt_lc_sc_no"  ondblclick="openmypage_lc()"  class="text_boxes" placeholder="Browse"  readonly/>   
                        				<input type="hidden" name="txt_lc_sc_id" id="txt_lc_sc_id"/>
									</td>
									<td>
										&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:75px" placeholder="From Date">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px" placeholder="To Date">
									</td>
									<td align="center">
										<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton"/>
									</td>
								</tr>
								<tr>
									<td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1); ?>
									</tr>
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>
				<div id="report_container" align="center"></div>
				<div id="report_container2"></div>
			</form>
		</div>
	</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
