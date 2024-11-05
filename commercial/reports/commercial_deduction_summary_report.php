<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Commercial Deduction Summary Report
Functionality	:
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	9-11-2023
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
echo load_html_head_contents("Commercial Deduction Summary Report", "../../", 1, 1,$unicode,1,'');
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function generate_report(operation)
{
    var company_id=$("#cbo_company_name").val();
    if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
    {
        release_freezing();
        return;
    }
    else
    {	
        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_exchange_rate',"../../")+'&report_title='+report_title+'&report_type='+operation;
        // alert(data);return;
		freeze_window(3);
		http.open("POST","requires/commercial_deduction_summary_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
    }
}

function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide();

	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();

	$('#table_body tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
}

function discount_amount_usd_details(action,datas)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/commercial_deduction_summary_report_controller.php?action='+action+'&datas='+datas, 'Discount Ammount', 'width=1100,height=320px,center=1,resize=0,scrolling=0','../');	
}

function rebate_details(action,datas)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/commercial_deduction_summary_report_controller.php?action='+action+'&datas='+datas, 'Rebate', 'width=800,height=320px,center=1,resize=0,scrolling=0','../');	
}

function other_deduction_usd(action,datas)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/commercial_deduction_summary_report_controller.php?action='+action+'&datas='+datas, 'Other Deduction USD', 'width=1050,height=320px,center=1,resize=0,scrolling=0','../');	
}

function discount_at_sight_payment_popup(action,datas)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/commercial_deduction_summary_report_controller.php?action='+action+'&datas='+datas, 'Discount For At Sight Payment USD', 'width=800,height=320px,center=1,resize=0,scrolling=0','../');	
}

function air_freight_ammount_bdt_popup(action,datas)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/commercial_deduction_summary_report_controller.php?action='+action+'&datas='+datas, 'Air Freight Ammount BDT', 'width=800,height=320px,center=1,resize=0,scrolling=0','../');	
}

function miscellaneous_expense_usd_popup(action,datas)
{ 
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/commercial_deduction_summary_report_controller.php?action='+action+'&datas='+datas, 'Miscellaneous Expense USD', 'width=1350,height=320px,center=1,resize=0,scrolling=0','../');	
}

</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:900px" align="center">
		<form id="file_wise_explort_import_status" action="" autocomplete="off" method="post">
			<? echo load_freeze_divs ("../../"); ?>
            <h3 style="width:840px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
			<div id="content_search_panel" style="width:840px;">
				<fieldset style="width:100%" >
					<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="800">
						<thead>
							<th class="must_entry_caption" width="200px">Company Name</th>
							<th width="200px">Buyer Name</th>		
							<th width="80px">Exchange Rate</th>		
							<th colspan="2" width="300px">Date Range</th>
							<th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('cash_incentive_report2','report_container*report_container2','','','')" /></th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
								?>
							</td>
							<td align="center" id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_name", 200, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
								?>
							</td>
                            <td>
								<input name="txt_exchange_rate" type="text" id="txt_exchange_rate" value="107" style="width:120px" placeholder="Exchange Rate">
							</td>
                            <td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date" >
							</td>
							<td align="center">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date" >
							</td>
							<td align="center">
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
							</td>
						</tr>
                        <tr>
							<td align="center" colspan="6"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script>set_multiselect('cbo_company_name','0','0','','0',"load_drop_down( 'requires/commercial_deduction_summary_report_controller',$('#cbo_company_name').val(), 'load_drop_down_buyer', 'buyer_td' )");</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
