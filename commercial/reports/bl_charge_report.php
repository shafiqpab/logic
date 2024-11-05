<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create BL Charge Report.
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	31-7-2021
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

//--------------------------------------------------------------------------------
echo load_html_head_contents('BL Charge Report', '../../', 1, 1, $unicode, 1, '', '');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';

	function generate_report()
	{		
		var cbo_company_name = $("#cbo_company_name").val();

		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Date Range')==false)
        {
            return;
        }
        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_forwarder_name*txt_invoice_no*txt_date_from*txt_date_to*cbo_year_selection*cbo_based_on","../../")+'&report_title='+report_title;

		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/bl_charge_report_controller.php",true);
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
			setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		// document.getElementById('table_body').style.overflowY='auto';
		// document.getElementById('table_body').style.maxHeight='none';
		$('#table_body').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body').find('tr:first').show();
		// document.getElementById('table_body').style.overflowY='scroll';
		// document.getElementById('table_body').style.maxHeight='300px';
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="reportpage1" name="reportpage1">
			<div style="width:900px;">
				<h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:880px;">
                        <table width="880" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption">Company Name</th>
                                    <th>Buyer Name</th>
                                    <th >Fromwarder Name</th>
                                    <th >Invoice No.</th>
									<th >Based On</th>
                                    <th colspan="2" class="must_entry_caption">Date Range</th>
                                    <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                    </th>
                                </tr>        
                            </thead>
                            <tbody>
                                <tr class="general">
                                    <td> 
                                        <? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'requires/bl_charge_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/bl_charge_report_controller',this.value, 'load_drop_down_forwarder', 'forwarder_td' );",'' ); ?>
                                    </td>
                                    <td id="buyer_td">
                                        <? echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "--- Select Buyer ---", $selected, ""); ?>
                                    </td>               
                                    <td id="forwarder_td">
                                        <? echo create_drop_down("cbo_forwarder_name", 140, $blank_array, "", 1, "--- Select Fromwarder ---", $selected, "");?>
                                    <td>
                                        <input type="text" name="txt_invoice_no" id="txt_invoice_no" style="width:120px" class="text_boxes"/>
                                    </td>
									<td>
									<?
           						     echo create_drop_down( "cbo_based_on",100,array(1=>"Ex-Factory Date",2=>"Bill Date" ,3=>"Invoice Date"),'',1,'--Select--',0,"",0);
                                     ?>
                                    </td>
                                    <td>
                                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">
                                    </td>
                                    <td>
                                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                                    </td> 
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Show" onClick="generate_report()" style="width:100px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="9"><? echo load_month_buttons(1); ?></td>
                                </tr>
                            </tbody>
                        </table>
					</fieldset>
				</div>
			</div>
			<br/>
			<div id="report_container"></div><br/>
            <div id="report_container2" align="center"></div>
		</form>
	</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>