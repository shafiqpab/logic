<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Dyeing Unit Wise Production Summary Report crm 11156
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Barik Tipu
Creation date 	: 	05-07-2023
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
//------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyeing Unit Wise Production Summary Report", "../../", 1, 1, '', '', '');

?>
<script>

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(report_type) 
    {
        var fso_no = $('#fso_no').val();
        var txt_date_from = $('#txt_date_from').val();
        var txt_date_to = $('#txt_date_to').val();
		
        if (fso_no != "") 
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

        var data = "action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year*fso_no*hidden_fso_no*booking_no*hidden_booking_no*batch_number_show*batch_number*cbo_floor_id*cbo_shift_name*txt_machine_id*txt_date_from*txt_date_to', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;

        freeze_window(5);
        http.open("POST", "requires/dyeing_unit_wise_production_summary_report_controller.php", true);
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
	
    function openmypage_fso(id)
    {
        if (form_validation('cbo_company_name', 'Company') == false)
        {
            return;
        }
        var company_name = document.getElementById('cbo_company_name').value;
        var year = document.getElementById('cbo_year').value;

        var year = document.getElementById('cbo_year').value;
        var page_link = "requires/dyeing_unit_wise_production_summary_report_controller.php?action=fso_no_popup&company_name=" + company_name + "&year=" + year;
        var title = "Order Number";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=420px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
            var theform=this.contentDoc.forms[0];
            var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
            var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
            $('#fso_no').val(fso_no);
            $('#hidden_fso_no').val(fso_id);

            release_freezing();
        }
    }

    function openmypage_booking()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var cbo_year = $("#cbo_year").val();

        var page_link='requires/dyeing_unit_wise_production_summary_report_controller.php?action=booking_no_popup&companyID='+companyID+ '&cbo_year='+cbo_year;
        var title='Booking No Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var booking_no=this.contentDoc.getElementById("hide_job_no").value;
            var booking_id=this.contentDoc.getElementById("hide_job_id").value;
            $('#hidden_booking_no').val(booking_no);
            $('#booking_no').val(booking_no);
        }
    }

    function batchnumber()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var page_link='requires/dyeing_unit_wise_production_summary_report_controller.php?action=batch_no_search_popup&companyID='+companyID;
        var title='Batch Number';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hide_batch_no=this.contentDoc.getElementById("hide_batch_no").value;
            var hide_batch_id=this.contentDoc.getElementById("hide_batch_id").value;
            $('#batch_number_show').val(hide_batch_no);
            $('#batch_number').val(hide_batch_id);   
        }
    }

    function openmypage_machine()
    {
        if (form_validation('cbo_company_name', 'Company Name') == false)
        {
            return;
        }
        var data = document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_floor_id').value;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_unit_wise_production_summary_report_controller.php?action=machine_no_popup&data=' + data, 'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0', '../');

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("hid_machine_id");
            var theemailv = this.contentDoc.getElementById("hid_machine_name");
            var response = theemail.value.split('_');
            if (theemail.value != "")
            {
                freeze_window(5);
                document.getElementById("txt_machine_id").value = theemail.value;
                document.getElementById("txt_machine_name").value = theemailv.value;
                release_freezing();
            }
        }
    }

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs("../../", ''); ?>
    <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        <h3 style="width:940px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:940px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                        <th class="must_entry_caption" width="180">Working Company</th>
                        <th width="70">Job Year</th>
                        <th width="120">FSO</th>
                        <th width="60">F.Booking</th>
                        <th width="60">Batch No</th>
                        <th width="100">Floor</th>
                        <th width="50">Shift</th>
                        <th width="60">Machine</th>
                        <th class="must_entry_caption" width="170" colspan="2">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?php
							echo create_drop_down("cbo_company_name", 180, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dyeing_unit_wise_production_summary_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );");
							?>
                        </td>
                        <td>
							<?php echo create_drop_down("cbo_year", 70, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, ""); ?>
                        </td>
                        <td>
                            <input type="text"  name="fso_no" id="fso_no" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="openmypage_fso()">
                            <input type="hidden" name="hidden_fso_no" id="hidden_fso_no">
                        </td>
                        <td>
                            <input type="text"  name="booking_no" id="booking_no" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="openmypage_booking()">
                            <input type="hidden" name="hidden_booking_no" id="hidden_booking_no">
                        </td>
                        <td>
                            <input type="text"  name="batch_number_show" id="batch_number_show" class="text_boxes" style="width:60px;" placeholder="Wr/Br" onDblClick="batchnumber();">
                            <input type="hidden" name="batch_number" id="batch_number">
                        </td>
                        <td id="floor_td">
                            <? echo create_drop_down("cbo_floor_id", 100, $blank_array, "", 1, "-Select Floor-", 0, "", 1); ?>
                        </td>
                        <td>
                            <? echo create_drop_down("cbo_shift_name", 50, $shift_name, "", 1, "--Shift--", 0, "", 0, "", "", "", "", ""); ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_machine()" readonly />
                            <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:50px" />
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
</script>
</html>