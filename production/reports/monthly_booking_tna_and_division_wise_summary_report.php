<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Monthly Booking, TNA and Division Wise Summary Report
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Barik Tipu
Creation date 	: 	26-07-2023
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
//echo load_html_head_contents("Monthly Booking, TNA and Division Wise Summary Report", "../../", 1, 1, '', '', '');
echo load_html_head_contents("Monthly Booking, TNA and Division Wise Summary Report", "../../", 1, 1,'',1,'');

?>
<script>

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(report_type) 
    {
        //var txt_date_from = $('#txt_date_from').val();
        //var txt_date_to = $('#txt_date_to').val();		

        if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company*From Date*To Date') == false) 
        {
            return;
        }	

        var report_title = $("div.form_caption").html();

        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_id*cbo_cust_buyer_id*cbo_team_leader*cbo_within_group*cbo_date_range_type*txt_date_from*txt_date_to', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;
        // alert(data);return;

        freeze_window(5);
        http.open("POST", "requires/monthly_booking_tna_and_division_wise_summary_report_controller.php", true);
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

    function getCompanyId____() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        // alert(company_id);
        if(company_id !='') 
        {
            var data="action=load_drop_down_buyer&choosenCompany="+company_id;
            http.open("POST","requires/monthly_booking_tna_and_division_wise_summary_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data); 
            http.onreadystatechange = function()
            {
                if(http.readyState == 4) 
                {
                    var response = trim(http.responseText);//.split("**");
                    $('#cbo_buyer_name_td').html(response);
                    //$('#cbo_cust_buyer_name_td').html(response);
                    set_multiselect('cbo_buyer_name','0','0','','0');
                    getCompanyIdCust();
                }
            };
        }         
    }

    function getCompanyId() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        // alert(company_id);
        if(company_id !='')
        {
            var data="action=load_drop_down_cust_buyer&choosenCompany="+company_id;
            http.open("POST","requires/monthly_booking_tna_and_division_wise_summary_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data); 
            http.onreadystatechange = function()
            {
                if(http.readyState == 4) 
                {
                    var response = trim(http.responseText);//.split("**");
                    $('#cust_buyer_td').html(response);
                    set_multiselect('cbo_cust_buyer_id','0','0','','0');
                }            
            };
        }         
    }

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", ''); ?>
    <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        <h3 style="width:940px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:940px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                    <th class="must_entry_caption" width="180">Company Name</th>
                    <th width="130">Buyer</th>
                    <th width="130">Cust. Buyer</th>
                    <th width="130">Team Leader</th>
                    <th width="80">Within Group</th>
                    <th width="120">Date Range Type</th>
                    <th class="must_entry_caption" width="170" colspan="2">Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?php
							echo create_drop_down("cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_booking_tna_and_division_wise_summary_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );getCompanyId();");
                            //load_drop_down( 'requires/monthly_booking_tna_and_division_wise_summary_report_controller',this.value, 'load_drop_down_cust_buyer', 'cust_buyer_td' );
							?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-- Select Buyer --", 0, "",0);
                            ?>
                        </td>
                        <td id="cust_buyer_td">
                            <?
                                echo create_drop_down( "cbo_cust_buyer_id", 130, $blank_array,"", 1, "-- Select Cust Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                $teamArr=array();
                                $teamSql=sql_select("select id, team_name, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name");
                                foreach($teamSql as $row)
                                {
                                    $teamArr[$row[csf("id")]]=$row[csf("team_leader_name")].'['.$row[csf("team_name")].']';
                                }
                                unset($teamSql);
                                echo create_drop_down( "cbo_team_leader", 130, $teamArr,"", 1, "-- Select Cust Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", 2, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                $date_range_type = array('1' => 'Delivery Date', '2' => 'Booking Date'); 
                                echo create_drop_down( "cbo_date_range_type", 120, $date_range_type,"", 0, "-- Select --", "", "","","" );
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
                    <tr>
                        <td colspan="14" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                    </tbody>
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
