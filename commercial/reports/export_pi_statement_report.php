<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Export PI Statement Report
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	15-02-2022
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
//-------------------------------------------------  html start -----------------------------------
echo load_html_head_contents("Export PI Statement Report","../../", 1, 1, $unicode,1,1);
?>
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    function openmypage_pi(title)
    {
        var cbo_company = $("#cbo_company_name").val();
        var category_ids = $("#cbo_item_category_id").val();

        if(form_validation("cbo_company_name","Company Name")==false){
            return;
        }

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_pi_statement_report_controller.php?action=pi_number_popup&company='+cbo_company+"&category_ids="+category_ids, title, 'width=450px,height=380px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var pi_id=this.contentDoc.getElementById("hidden_pi_id").value;
            var pi_number=this.contentDoc.getElementById("hidden_pi_number").value;
            $("#sys_id").val(pi_id);
            $("#pi_no").val(pi_number);
            disable_enable_fields('pi_no*sys_id',1);
        }
    }

    function generate_report(rpt_type)
    {
        var cbo_company = $("#cbo_company_name").val();
        var pi_no = $("#pi_no").val();
        var sys_id = $("#sys_id").val();
        var from_date = $("#txt_date_from").val();
        var to_date = $("#txt_date_to").val();

        if(form_validation("cbo_company_name","Company Name")==false){
            return;
        }
        else
        {
            if(pi_no == "" && sys_id ==""  && from_date == "" && to_date == "")
            {
                alert("Either Select PI NO Or System ID Or Date Range");
                $("#pi_no").focus();
                return;
            }
            else
            {
                var report_title = $('.form_caption').html();
                var data  = "action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_item_category_id*pi_no*sys_id*txt_date_from*txt_date_to','../../')+"&report_title="+report_title+"&rpt_type="+rpt_type;
                // alert(data);return;
            }

            freeze_window(3);
            http.open("POST","requires/export_pi_statement_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {
            var reponse = trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] +'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;' + '<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" target="_blank" style="width:100px"/>';
            document.getElementById('report_container2').innerHTML=reponse[0];
            show_msg('3');
            //setFilterGrid("table_body_id",-1); //,tableFilters
            release_freezing();
        }
    }

    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body></html>');
        d.close();
    }
    
    function openmypage_image(pi_id)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_pi_statement_report_controller.php?action=image_file&pi_id='+pi_id, "File/Image", 'width=450px,height=380px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
        }
    }

    function openmypage_wo(pi_id)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/export_pi_statement_report_controller.php?action=wo_details&pi_id='+pi_id, "WO Wiew", 'width=230px,height=250px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
        }
    }  

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width: 100%; text-align: center">
        <?php echo load_freeze_divs("../../", $permission);?>
        <form name="export_pi_statement_report" id="export_pi_statement_report" autocomplete="off">
            <h3 id="accordion_h1" style="width:1000px; text-align: left; margin: 0 auto" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div style="width:1000px; text-align: center; margin:0 auto;" id="content_search_panel">
                <fieldset style="width:1000px;">
                    <table class="rpt_table" width="995" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        <tr>
                            <th width="150" class="must_entry_caption">Company</th>
                            <th width="170" >Item Category</th>
                            <th width="150" >PI No</th>
                            <th width="150" >System ID</th>
                            <th width="165" >Date Range</th>
                            <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('export_pi_statement_report','report_container*report_container2','','', 'disable_enable_fields(\'pi_no*sys_id\',0);')" /></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
                            <td id="cat_td">
                                <?php 
                                    echo create_drop_down( "cbo_item_category_id", 170,$export_item_category,"", 0, "", $selected, "1,2,3,4,5,10,11,20,22,23,24,30,31,35,36,37,45","","","","","");
                                ?> 
                            </td>
                            <td align="center">
                                <input style="width:137px;"  name="pi_no" id="pi_no"  ondblclick="openmypage_pi('PI Number')"  class="text_boxes" placeholder="Browse or Write"   />
                            </td>
                            <td align="center">
                                <input style="width:137px;"  name="sys_id" id="sys_id"  ondblclick="openmypage_pi('System ID Number')"  class="text_boxes" placeholder="Browse or Write"   />
                            </td>
                            <td style="text-align: center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;"/>
                            </td>

                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:50px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Show-2" onClick="generate_report(2)" style="width:50px" class="formbutton" />
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </div>
            <div id="report_container" style="margin-top: 15px;text-align: center"></div>
            <div id="report_container2" ></div>
        </form>
    </div>
</body>
<script>
    set_multiselect('cbo_item_category_id','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
