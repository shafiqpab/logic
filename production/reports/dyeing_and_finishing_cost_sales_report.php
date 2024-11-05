<?
/*-------------------------------------------- Comments

Purpose         :   This form will Create Batch Reprot
Functionality   :
JS Functions    :
Created by      :   Abdul Barik Tipu
Creation date   :   23-01-2022
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

    var tableFilters2 =
    {
        col_operation: {
        id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
        col: [27,28,29,30],
        operation: ["sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters3 =
    {
        col_operation: {
        id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
        col: [27,28,29,30],
        operation: ["sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters4 =
    {
        col_operation: {
        id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
        col: [27,28,29,30],
        operation: ["sum","sum","sum","sum"],
        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters5 =
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
        var fso_number=document.getElementById('txt_job_no').value;
        var batch_number_show=document.getElementById('batch_number_show').value;

        if( batch_number_show !="" || fso_number != "" )
        {
            if(form_validation('cbo_company_name','Company')==false)
            {
                return;
            }
        }
        else
        {
            if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date*To date')==false)
            {
                return;
            }
        }
        freeze_window(5);
        var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_issue_type*batch_number_show*batch_id*txt_job_no*txt_job_hidden_id*txt_date_from*txt_date_to*cbo_year_selection',    "../../");

        http.open("POST","requires/dyeing_and_finishing_cost_sales_report_controller.php",true);
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
            setFilterGrid("table_body3",-1);
            setFilterGrid("table_body4",-1);
            setFilterGrid("table_body5",-1);

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

        if(document.getElementById('table_body3'))
        {
            document.getElementById('scroll_body_batch').style.overflow="auto";
            document.getElementById('scroll_body_batch').style.maxHeight="none";
            $('#scroll_body_batch tr:first').hide();
        }
        if(document.getElementById('table_body4'))
        {
            document.getElementById('scroll_body_indep').style.overflow="auto";
            document.getElementById('scroll_body_indep').style.maxHeight="none";
            $('#scroll_body_indep tr:first').hide();
        }
        if(document.getElementById('table_body5'))
        {
            document.getElementById('scroll_body_mc').style.overflow="auto";
            document.getElementById('scroll_body_mc').style.maxHeight="none";
            $('#scroll_body_mc tr:first').hide();
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
        if(document.getElementById('table_body3'))
        {
            document.getElementById('scroll_body_batch').style.overflow="auto";
            document.getElementById('scroll_body_batch').style.maxHeight="397px";
            $('#scroll_body_batch tr:first').show();
        }
        if(document.getElementById('table_body4'))
        {
            document.getElementById('scroll_body_indep').style.overflow="auto";
            document.getElementById('scroll_body_indep').style.maxHeight="397px";
            $('#scroll_body_indep tr:first').show();
        }
        if(document.getElementById('table_body5'))
        {
            document.getElementById('scroll_body_mc').style.overflow="auto";
            document.getElementById('scroll_body_mc').style.maxHeight="397px";
            $('#scroll_body_mc tr:first').show();
        }
    }

    function openmypage_fso()
    {
        var company_name = $('#cbo_company_name').val();
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }

        var page_link = "requires/dyeing_and_finishing_cost_sales_report_controller.php?action=fso_no_popup&company_name=" + company_name;
        var title = "Order Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=420px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theform=this.contentDoc.forms[0];
            var fso_no=this.contentDoc.getElementById("hidden_job_no").value;
            var fso_id=this.contentDoc.getElementById("hidden_job_id").value;
            $('#txt_job_no').val(fso_no);
            $('#txt_job_hidden_id').val(fso_id);

            release_freezing();
        }
    }

    function batchnumber()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var page_link='requires/dyeing_and_finishing_cost_sales_report_controller.php?action=batch_no_search_popup&companyID='+companyID;
        var title='Batch Number';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hide_batch_no=this.contentDoc.getElementById("hide_batch_no").value;
            var hide_batch_id=this.contentDoc.getElementById("hide_batch_id").value;
            $('#batch_number_show').val(hide_batch_no);
            $('#batch_id').val(hide_batch_id);   
        }
    }
</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
        <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:740px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
            <div id="content_search_panel" >
                <fieldset style="width:740px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Sales Order</th>
                            <th>Batch No</th>
                            <th>Issue Type</th>
                            <th>Date Range</th>
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
                                    <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" placeholder="Double Click" onDblClick="openmypage_fso()" readonly/>
                                    <input type="hidden" name="txt_job_hidden_id" id="txt_job_hidden_id" readonly>
                                </td>
                                <td>
                                    <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:100px;" placeholder="Wr/Br" onDblClick="batchnumber();">
                                    <input type="hidden" name="batch_id" id="batch_id">
                                </td>
                                <td>
                                    <?
                                        $issue_type = array('1' => 'Without Issue', '2' => 'With Issue');
                                        echo create_drop_down( "cbo_issue_type", 90, $issue_type,"", 0, "-- All --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                    &nbsp;To&nbsp;
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="12">
                                    <? echo load_month_buttons(1); ?>
                                </td>
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