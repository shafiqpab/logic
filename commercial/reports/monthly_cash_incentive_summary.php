<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Monthly Cash Incentive Summary
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	29-9-2022
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
echo load_html_head_contents("Monthly Cash Incentive Summary", "../../", 1, 1,'','','');
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function generate_report(rpt_type)
{
	if(form_validation('cbo_company_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to','Company Name*From Year*From Month*To Year*To Month')==false)
	{
		return;
	}

	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_id*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to","../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
	freeze_window(3);
	http.open("POST","requires/monthly_cash_incentive_summary_controller.php",true);
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

	function fn_rlz_open_details(buyer_id,company_id,start_date,end_date,action,title,page_width,popup_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_cash_incentive_summary_controller.php?buyer_id='+buyer_id+'&company_id='+company_id+'&start_date='+start_date+'&end_date='+end_date+'&title='+title+'&action='+action+'&popup_type='+popup_type, title, 'width='+page_width+'px,height=390px,center=1,resize=0,scrolling=0','../');
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<form id="monthlyCashIncentiveSummary" name="monthlyCashIncentiveSummary" autocomplete="off" method="post">
			<? echo load_freeze_divs ("../../"); ?>
			<h3 align="center" id="accordion_h1" style="width:880px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:880px;">

				<fieldset style="width:100%" >
					<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="850">
						<thead>
							<tr>
								<th class="must_entry_caption" width="170px">Company Name</th>
								<th width="170px">Buyer</th>
								<th class="must_entry_caption" width="90px">From Year</th>
								<th class="must_entry_caption" width="110px">From Month</th>
								<th class="must_entry_caption" width="90px">To Year</th>
								<th class="must_entry_caption" width="110px">To Month</th>
								<th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthlyCashIncentiveSummary','report_container*report_container2','','','')" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td align="center">
									<?
										echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business in(1,3) order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_cash_incentive_summary_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
									?>
								</td>
								<td align="center" id="buyer_td">
									<?echo create_drop_down( "cbo_buyer_id", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );?>
								</td>
								<td><?echo create_drop_down( "cbo_year_from", 80,$year,"", 0, "-- Select --", date('Y'),"");?></td>
								<td><? echo create_drop_down( "cbo_month_from", 100,$months,"", 0, "-- Select --", date('m'),"" ); ?></td>
								<td><?echo create_drop_down( "cbo_year_to", 80,$year,"", 0, "-- Select --", date('Y'),"");?></td>
								<td><? echo create_drop_down( "cbo_month_to", 100,$months,"", 0, "-- Select --", date('m'),"" ); ?></td>
								<td align="center">
									<input type="button" name="show" id="show" onClick="generate_report(1);" class="formbutton" style="width:80px" value="Show" />
								</td>
							</tr>
						</tbody>
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
