<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Monthly Export Status Summery
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	23-1-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------
echo load_html_head_contents("Monthly Export Status Summery", "../../", 1, 1,'','','');
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function generate_report(rpt_type)
{
	if(form_validation('cbo_company_name*cbo_year*cbo_month','Company Name*Year*File No')==false)
	{
		return;
	}

	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_location_id*cbo_buyer_id*cbo_year*cbo_month","../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
	freeze_window(3);
	http.open("POST","requires/monthly_export_status_summery_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}

function fn_report_generated_reponse()
{
	if(http.readyState == 4)
	{
		var response=trim(http.responseText).split("####");
		//alert(http.responseText);return;
		$('#report_container2').html(response[0]);
		//document.getElementById('report_container').innerHTML=report_convert_button('../../');
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		show_msg('3');
		release_freezing();
	}
}

function new_window()
{
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
}

function openmypage_invoice(realized_type,company_id,location_id,buyer_id,start_date,end_date,title,month_year)
{
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=invoice_details&realized_type='+realized_type+'&company_id='+company_id+'&location_id='+location_id+'&buyer_id='+buyer_id+'&start_date='+start_date+'&end_date='+end_date+'&month_year='+month_year, title, 'width=600px,height=350px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
	}
} 

function openmypage_invoice_v2(realized_type,company_id,location_id,buyer_id,start_date,end_date,title,month_year)
{
	if(realized_type==2)
	{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=invoice_details_V2&realized_type='+realized_type+'&company_id='+company_id+'&location_id='+location_id+'&buyer_id='+buyer_id+'&start_date='+start_date+'&end_date='+end_date+'&month_year='+month_year+'&title='+title, title, 'width=660px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	else if(realized_type==3)
	{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=invoice_details_V2&realized_type='+realized_type+'&company_id='+company_id+'&location_id='+location_id+'&buyer_id='+buyer_id+'&start_date='+start_date+'&end_date='+end_date+'&month_year='+month_year+'&title='+title, title, 'width=710px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	else if(realized_type==4)
	{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=invoice_details_V2&realized_type='+realized_type+'&company_id='+company_id+'&location_id='+location_id+'&buyer_id='+buyer_id+'&start_date='+start_date+'&end_date='+end_date+'&month_year='+month_year+'&title='+title, title, 'width=800px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	else{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=invoice_details_V2&realized_type='+realized_type+'&company_id='+company_id+'&location_id='+location_id+'&buyer_id='+buyer_id+'&start_date='+start_date+'&end_date='+end_date+'&month_year='+month_year+'&title='+title, title, 'width=600px,height=350px,center=1,resize=0,scrolling=0','../');
	}
	
	emailwindow.onclose=function()
	{
	}
} 

function openmypage_invoice_v3(realized_type,company_id,location_id,buyer_id,start_date,end_date,title,month_year)
{
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=invoice_details_v3&realized_type='+realized_type+'&company_id='+company_id+'&location_id='+location_id+'&buyer_id='+buyer_id+'&start_date='+start_date+'&end_date='+end_date+'&month_year='+month_year, title, 'width=1300px,height=350px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
	}
}

function openmypage_realization(realization_id,title,month_year)
{
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=realization_details&realization_id='+realization_id+'&month_year='+month_year, title, 'width=600px,height=350px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
	}
}  

function openmypage_short(realization_id,title)
{
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_status_summery_controller.php?action=short_realization_details&realization_id='+realization_id, title, 'width=1100px,height=370px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
	}
}  

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<form id="monthlyExportStatusSummery" action="" autocomplete="off" method="post">
			<? echo load_freeze_divs ("../../"); ?>
			<h3 align="center" id="accordion_h1" style="width:960px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:960px;">

				<fieldset style="width:100%" >
					<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="930">
						<thead>
							<th class="must_entry_caption" width="170px">Company Name</th>
							<th width="170px">Location</th>
							<th width="170px">Buyer</th>
							<th class="must_entry_caption" width="100px">Year</th>
							<th class="must_entry_caption" width="120px">Month</th>
							<th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthlyExportStatusSummery','report_container*report_container2','','','')" /></th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
									echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_export_status_summery_controller',this.value, 'load_drop_down_location','location_td' );load_drop_down( 'requires/monthly_export_status_summery_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/monthly_export_status_summery_controller' );" );
								?>
							</td>
							<td align="center" id="location_td">
								<?echo create_drop_down( "cbo_location_id", 170, $blank_array,"", 1, "-- All Location --", $selected, "",0,"" );?>
							</td>
							<td align="center" id="buyer_td">
								<?echo create_drop_down( "cbo_buyer_id", 170, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );?>
							</td>
							<td><?echo create_drop_down( "cbo_year", 80,$year,"", 1, "-- Select --", date('Y'),"");?></td>
                            <td><? echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
							<td align="center">
								<span id="button_data_panel"></span>
								<!-- <input type="button" name="show" id="show" onClick="generate_report(1);" class="formbutton" style="width:80px" value="Show" />

								<input type="button" name="show2" id="show2" onClick="generate_report(2);" class="formbutton" style="width:80px" value="Show 2" />

								<input type="button" name="show2" id="show2" onClick="generate_report(3);" class="formbutton" style="width:80px" value="Show 3" />
								<input type="button" name="show2" id="show2" onClick="generate_report(4);" class="formbutton" style="width:80px" value="Show 4" /> -->
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
