<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Commercial Incentive Report.
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	16-11-2021
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
echo load_html_head_contents("Commercial Incentive Report", "../../", 1, 1,'','','');
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function openmypage_lcsc()
{
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_year = $("#cbo_year").val();

	if(form_validation("cbo_company_name*cbo_year","Company Name*Year")==false){
		return;
	}
	var title='LC/SC Form';

	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/com_incentive_report_controller.php?action=lcsc_popup&cbo_company_name='+cbo_company_name+"&cbo_year="+cbo_year, title, 'width=900px,height=360px,center=1,resize=0,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var file_no=this.contentDoc.getElementById("hidden_data").value.split("_");		
		document.getElementById('txt_lcsc_id').value=file_no[0];
		document.getElementById('lcsc_type').value=file_no[1];
		document.getElementById('txt_lc_sc_no').value=file_no[2];
	}
}

function generate_report(rpt_type)
{
	if(form_validation('cbo_company_name*cbo_year*txt_lc_sc_no','Company Name*Year*LC/SC No')==false)
	{
		return;
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_year*txt_lc_sc_no*txt_lcsc_id*lcsc_type","../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
	//alert(data);return;
	freeze_window(3);
	http.open("POST","requires/com_incentive_report_controller.php",true);
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

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:1000px" align="center">
		<form id="commercialIncentiveReport" action="" autocomplete="off" method="post">
			<? echo load_freeze_divs ("../../"); ?>
			<h3 align="left" id="accordion_h1" style="width:980px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:980px;">

				<fieldset style="width:100%" >
					<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="950">
						<thead>
							<th class="must_entry_caption" width="170px">Company Name</th>
							<th width="170px">Buyer</th>
							<th class="must_entry_caption" width="100px">Year</th>
							<th class="must_entry_caption" width="170px">LC/SC No</th>
							<th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('commercialIncentiveReport','report_container*report_container2','','','')" /></th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
									echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/com_incentive_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
							</td>
							<td align="center" id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_name", 170, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
								?>
							</td>							
							<td >
								<?
									echo create_drop_down( "cbo_year", 100,$year,"", 1, "-- Select --", date('Y'),"");
								?>
							</td>
							<td align="left">
								<input type="text" name="txt_lc_sc_no" id="txt_lc_sc_no" class="text_boxes" style="width:90%" ondblclick="openmypage_lcsc()"  placeholder="Browse"/>
								<input type="hidden" id="txt_lcsc_id" name="txt_lcsc_id">
								<input type="hidden" id="lcsc_type" name="lcsc_type">
							</td>
							<td align="center">
								<input type="button" name="bgmea" id="bgmea" onClick="generate_report(1);" class="formbutton" style="width:80px" value="BGMEA Inc." />
								<input type="button" name="prc" id="prc" onClick="generate_report(2);" class="formbutton" style="width:80px" value="PRC Inc." />
								<input type="button" name="cash" id="cash" onClick="generate_report(3);" class="formbutton" style="width:80px" value="Cash Inc." />
								<input type="button" name="master" id="master" onClick="generate_report(4);" class="formbutton" style="width:80px" value="Master Inc." />
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
