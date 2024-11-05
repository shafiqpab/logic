<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Recipe Performance Report 
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	15-Jul-2023
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
echo load_html_head_contents("Recipe Performance Report", "../../", 1, 1, '', '', '');

?>
<script>

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(report_type) 
    {
        var txt_recipe_no = $('#txt_recipe_no').val();
        var txt_date_from = $('#txt_date_from').val();
        var txt_date_to = $('#txt_date_to').val();
        var txt_batch_no = $('#txt_batch_no').val();
        var txt_batch_id = $('#txt_batch_id').val();
        var txt_recipe_no = $('#txt_recipe_no').val();
        var cbo_method = $('#cbo_method').val();
        var cbo_result = $('#cbo_result').val();
        var cbo_re_process = $('#cbo_re_process').val();
        if(txt_batch_no!="" || txt_recipe_no!="")
        {
            if (form_validation('cbo_company_name', 'Company') == false) 
            {
                return;
            }
        }
        else
        {
             
            if (form_validation('cbo_company_name*txt_date_from*txt_date_to', 'Company*From Date*To Date') == false) 
            {
                return;
            }
        }
       
        var report_title = $("div.form_caption").html();
        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*txt_batch_id*txt_recipe_no*txt_batch_no*txt_date_from*txt_date_to*cbo_method*cbo_result*cbo_re_process', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;
        //alert(data);

        freeze_window(5);
        http.open("POST", "requires/recipe_performance_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse() 
    {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("####");

            $("#report_container2").html(response[0]);
            setFilterGrid("table_body",-1);
          
            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window() 
    {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
        $('#scroll_body tr:first').hide()

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "250px";

        $('#scroll_body tr:first').show()
    }
	
    function openmypage_sales_order() 
    {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var page_link = 'requires/recipe_performance_report_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&yearID=' + yearID;
        var title = 'Sales Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;

            $('#txt_sales_order').val(sales_job_no);
        }
    }

    function batchnumber(type)
	{ 
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var cbo_result=document.getElementById('cbo_result').value;
		var page_link="requires/recipe_performance_report_controller.php?action=batch_popup&company_name="+company_name+'&cbo_result='+cbo_result+'&type='+type;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
			var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
            if(type==1)
            {
                document.getElementById('txt_batch_no').value=batch_no;
                document.getElementById('txt_batch_id').value=batch_id;
            }
            else
            {
                document.getElementById('txt_recipe_no').value=batch_id;
            }
                
			//document.getElementById('batch_no').value=batch[1];
			release_freezing();
		}
	}	

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", ''); ?>
    <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        <h3 style="width:740px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:740px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                    <th class="must_entry_caption" width="180">Company</th>
                    <th width="70">Batch NO</th>
                    <th width="70">Recipe No.</th> 
                    <th width="70">Method</th>
                    <th width="70">Result</th> 
                    <th width="70">Re-process Type</th>
                    
                    <th class="must_entry_caption" width="170" colspan="2">Dyeing Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                    </thead>

                    <tbody>
                    <tr class="general">
                        
                    <td>
							<?php
							    echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
							?>
                   </td>

                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes"  placeholder="Write" onDblClick="batchnumber(1);" style="width:70px" />
                        <input type="hidden" id="txt_batch_id">     
                        </td>
                        <td>
                             <input type="text" name="txt_recipe_no" id="txt_recipe_no" class="text_boxes"  placeholder="Write"  style="width:70px" />
                        </td>
                        <td>
							<?php
							    echo create_drop_down("cbo_method",70, $ltb_btb, "", 1, "-- Select --", $selected, "l");
							?>
                        </td>
                        <td>
							<?php
							    echo create_drop_down("cbo_result",70, $dyeing_result, "", 1, "-- Select --", $selected, "l","","1,2,3,4,5,6,20");
							?>
                        </td>
                        <td>
							<?php
							    echo create_drop_down("cbo_re_process",70, $dyeing_re_process, "", 1, "-- Select --", $selected, "l");
							?>
                        </td>
                         
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date"/>
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)"/>
                        </td>
                    </td>
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
</script>
</html>