<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Dyeing Capacity Plan Report
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Barik Tipu
Creation date 	: 	26-10-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: 

*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Dyeing Capacity Plan Report", "../../", 1, 1, '', '', '');
echo load_html_head_contents("Dyeing Capacity Plan Report", "../../", 1, 1,'',1,'');

?>
<script>

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(report_type) 
    {
        if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company*From Date*To Date') == false) 
        {
            return;
        }	

        var report_title = $("div.form_caption").html();

        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;
        // alert(data);return;

        freeze_window(5);
        http.open("POST", "requires/dyeing_capacity_plan_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse() 
    {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("####");

            $("#report_container2").html(response[0]);
          
            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window() 
    {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";


        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "250px";

        $("tr th:first-child").show();
        $("tr td:first-child").show();
    }

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", ''); ?>
    <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        <h3 style="width:750px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:750px;">
                <table class="rpt_table" width="350" cellpadding="1" cellspacing="2" align="center" >
                    <thead>
                    <th class="must_entry_caption" width="180">Company Name</th>
                    <th class="must_entry_caption" width="170" colspan="2">Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
    							<?php
    							echo create_drop_down("cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
    							?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("01-m-Y"); ?>" class="datepicker" style="width:70px" placeholder="From Date" readonly="" />
                            </td>
                            <td>
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:70px" placeholder="To Date" readonly="" />
                            </td>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
        <div style="width:100%;margin-top:10px;">
        </div>
        <br>
    </form>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    $('#cbo_year').val(0);
    set_multiselect('cbo_cust_buyer_id*cbo_team_leader','0*0','0*0','','0*0');
</script>
</html>