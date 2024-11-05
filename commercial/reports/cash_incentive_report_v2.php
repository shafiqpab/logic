<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cash Incentive Report V2.
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	24-7-2022
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
echo load_html_head_contents('Cash Incentive Report V2', '../../', 1, 1, $unicode, 1, '', '');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';

	function generate_report(operation)
	{		
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Date Range')==false)
        {
            return;
        }

        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_bank_name*txt_incentive_bank_no*cbo_data_type*txt_search_data*cbo_date_type*txt_date_from*txt_date_to","../../")+'&report_title='+report_title+'&report_type='+operation;

		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/cash_incentive_report_v2_controller.php",true);
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
		document.getElementById('table_body').style.overflowY='auto';
		document.getElementById('table_body').style.maxHeight='none';
		$('#table_body').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body').find('tr:first').show();
		document.getElementById('table_body').style.overflowY='scroll';
		document.getElementById('table_body').style.maxHeight='300px';
	}

	function fnc_caption_change(type)
	{
		if(type==1){$("#data_name").html("Submission ID");}
		else if(type==2){$("#data_name").html("Receive ID");}
		else if(type==3){$("#data_name").html("Invoice No");}
		else if(type==4){$("#data_name").html("Exp No");}
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="reportpage1" name="reportpage1">
			<div style="width:1150px;">
				<h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:1150px;">
                        <table width="1150" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption">Company Name</th>
                                    <th>Buyer</th>
                                    <th>Bank</th>
                                    <th>Incentive Bank No</th>
                                    <th >Data Type</th>
                                    <th id="data_name">Submission ID</th>
                                    <th >Date Type</th>
                                    <th colspan="2" class="must_entry_caption">Date Range</th>
                                    <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
                                </tr>        
                            </thead>
                            <tbody>
                                <tr class="general">
                                    <td> 
                                        <? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and comp.core_business in(1,3)$company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"",'' ); ?>
                                    </td>
                                    <td>
										<? echo create_drop_down("cbo_buyer_name", 140, "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0 $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", 0, "");
                                        ?>
                                    </td>
                                    <td>
                                        <? echo create_drop_down("cbo_bank_name", 140, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, ""); ?>
                                    </td>
                                    <td ><input type="text" name="txt_incentive_bank_no" id="txt_incentive_bank_no" style="width:120px" class="text_boxes"/></td>               
									<td>
										<? $data_type=array(1=>"Submission ID",2=>"Receive ID",3=>"Invoice No",4=>"Exp No");
										echo create_drop_down("cbo_data_type", 110, $data_type, "", 0, "-- Select --", 0, "fnc_caption_change(this.value);"); ?>
                                    </td>
                                    <td ><input type="text" name="txt_search_data" id="txt_search_data" style="width:120px" class="text_boxes"/></td>               
                                    <td>
										<? $date_type=array(1=>"Submission",2=>"Received");
										echo create_drop_down("cbo_date_type", 110, $date_type, "", 0, "-- Select --", 0, ""); ?>
                                    </td>
                                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"></td>
                                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date"></td> 
                                    <td align="center">
                                        <input type="button" name="button" class="formbutton" value="Summary" onClick="generate_report(1)" style="width:80px;" />
									</td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="9"><? echo load_month_buttons(1); ?></td>
									<td align="center">
                                        <input type="button" name="button" class="formbutton" value="Details" onClick="generate_report(2)" style="width:80px;" />
									</td>
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