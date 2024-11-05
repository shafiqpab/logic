<?
/*-------------------------------------------- Comments

Purpose         :   This form will Create Batch Reprot
Functionality   :
JS Functions    :
Created by      :   Abdul Barik Tipu
Creation date   :   17-08-2022
Updated by      :
Update date     :
QC Performed BY :
QC Date         :
Comments        :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Batch Creation Report", "../../", 1, 1,'','','');
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
    var permission='<? echo $permission; ?>';

    var tableFilters =
    {
        col_operation: {
        id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
        col: [27,28,29,30],
        operation: ["sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    function fn_report_generated(operation)
    {
        if(form_validation('cbo_company_name*cbo_to_month*txt_production_running','Company*To Month*Production Running')==false)
        {
            return;
        }

        freeze_window(5);
        var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_year*cbo_to_month*txt_production_running',"../../");

        http.open("POST","requires/machine_wise_knitting_production_report_v3_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_show_batch_report;
    }

    function fnc_show_batch_report()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
                document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            setFilterGrid("table_body",-1);
            setFilterGrid("table_body2",-1);

            show_msg('3');
            release_freezing();

        }
    }

    function new_window()
    {
        if(document.getElementById('table_body'))
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#scroll_body tr:first').hide();
        }

        if(document.getElementById('table_body2'))
        {
            document.getElementById('scroll_body_inbound').style.overflow="auto";
            document.getElementById('scroll_body_inbound').style.maxHeight="none";
            $('#scroll_body_inbound tr:first').hide();
        }

        var w = window.open("Surprise", "#");

        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();

        if(document.getElementById('table_body'))
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="397px";
            $('#scroll_body tr:first').show();
        }

        if(document.getElementById('table_body2'))
        {
            document.getElementById('scroll_body_inbound').style.overflow="auto";
            document.getElementById('scroll_body_inbound').style.maxHeight="397px";
            $('#scroll_body_inbound tr:first').show();
        }
    }

</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
        <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:500px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
            <div id="content_search_panel" >
                <fieldset style="width:500px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Year</th>
                            <th class="must_entry_caption">To Month</th>
                            <th class="must_entry_caption">Production Running</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                                    ?>
                                </td>
                                <td>
                                    <?
                                        $selected_year=date("Y");
                                        echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Year--", $selected_year, "",0 );
                                    ?>
                                </td>
                                <td>
                                    <?
                                        $selected_month=date("m");
                                        echo create_drop_down( "cbo_to_month", 80, $months,"", 1, "--To Month--", 0, "",0 );
                                    ?>
                                </td>
                                <td align="center">
                                    <input type="text"  name="txt_production_running" id="txt_production_running" class="text_boxes" style="width:50px;">
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id="report_container"></div>
            <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>