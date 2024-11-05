<?
/*-------------------------------------------- Comments
Purpose         :   This form will Create Total Production Activity Sales
Functionality   :   
JS Functions    :
Created by      :   Abdul Barik Tipu
Creation date   :   10-09-2023
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        : 

*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
// echo load_html_head_contents("Total Production Activity Sales", "../../", 1, 1, '', '', '');
echo load_html_head_contents("Total Production Activity Sales", "../../", 1, 1, $unicode,1,1); 

?>
<script>

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(report_type) 
    {
        var txt_sales_order = $('#txt_sales_order').val();
        var txt_booking_no = $('#txt_booking_no').val();
        var txt_date_from = $('#txt_date_from').val();
        var txt_date_to = $('#txt_date_to').val();

        if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
        {
            return;
        }
        

        var report_title = $("div.form_caption").html();

        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_type*cbo_year*txt_sales_order*txt_date_from*txt_date_to*txt_booking_no*cbo_within_group*cbo_booking_type*cbo_working_company_id*cbo_location_id', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;

        freeze_window(5);
        http.open("POST", "requires/total_production_activity_report_sales_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse() {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("####");

            $("#report_container2").html(response[0]);
            //alert (response[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window() 
    {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
        document.getElementById('scroll_body2').style.overflow = "auto";
        document.getElementById('scroll_body2').style.maxHeight = "none";
        document.getElementById('scroll_body3').style.overflow = "auto";
        document.getElementById('scroll_body3').style.maxHeight = "none";
        document.getElementById('scroll_body4').style.overflow = "auto";
        document.getElementById('scroll_body4').style.maxHeight = "none";
        document.getElementById('scroll_body5').style.overflow = "auto";
        document.getElementById('scroll_body5').style.maxHeight = "none";
        document.getElementById('scroll_body6').style.overflow = "auto";
        document.getElementById('scroll_body6').style.maxHeight = "none";
        document.getElementById('scroll_body7').style.overflow = "auto";
        document.getElementById('scroll_body7').style.maxHeight = "none";
        document.getElementById('scroll_body8').style.overflow = "auto";
        document.getElementById('scroll_body8').style.maxHeight = "none";
        document.getElementById('scroll_body9').style.overflow = "auto";
        document.getElementById('scroll_body9').style.maxHeight = "none";

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "250px";
        document.getElementById('scroll_body2').style.overflowY = "scroll";
        document.getElementById('scroll_body2').style.maxHeight = "250px";
        document.getElementById('scroll_body3').style.overflowY = "scroll";
        document.getElementById('scroll_body3').style.maxHeight = "250px";
        document.getElementById('scroll_body4').style.overflowY = "scroll";
        document.getElementById('scroll_body4').style.maxHeight = "250px";
        document.getElementById('scroll_body5').style.overflowY = "scroll";
        document.getElementById('scroll_body5').style.maxHeight = "250px";
        document.getElementById('scroll_body6').style.overflowY = "scroll";
        document.getElementById('scroll_body6').style.maxHeight = "250px";
        document.getElementById('scroll_body7').style.overflowY = "scroll";
        document.getElementById('scroll_body7').style.maxHeight = "250px";
        document.getElementById('scroll_body8').style.overflowY = "scroll";
        document.getElementById('scroll_body8').style.maxHeight = "250px";
        document.getElementById('scroll_body9').style.overflowY = "scroll";
        document.getElementById('scroll_body9').style.maxHeight = "250px";

        $("tr th:first-child").show();
        $("tr td:first-child").show();
    }

    function new_window_______() {

        document.getElementById('scroll_body2').style.overflow = "auto";
        document.getElementById('scroll_body2').style.maxHeight = "none";
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();

        document.getElementById('scroll_body2').style.overflowY = "scroll";
        document.getElementById('scroll_body2').style.maxHeight = "none";

        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "none";

        $("tr th:first-child").show();
        $("tr td:first-child").show();
    }

    function openmypage_booking_no() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var ordType = $("#cbo_type").val();
        var cbo_booking_type = $("#cbo_booking_type").val();
        var page_link = 'requires/total_production_activity_report_sales_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&ordType=' + ordType+'&cbo_booking_type='+cbo_booking_type;
        var title = 'Booking No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_data").value;

            $('#txt_booking_no').val(booking_no);
        }
    }

    function openmypage_sales_order() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var ordType = $("#cbo_type").val();
        var cbo_within_group = $("#cbo_within_group").val();
        var page_link = 'requires/total_production_activity_report_sales_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&ordType=' + ordType + '&cbo_within_group=' + cbo_within_group;
        var title = 'Sales Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;

            $('#txt_sales_order').val(sales_job_no);
        }
    }

    function fn_disable_com(str)
    {
        if(str==2){$("#show_textcbo_company_name").attr('disabled','disabled');}
        else{ $('#cbo_company_name').removeAttr("disabled");}
        if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
        else{ $('#cbo_working_company_id').removeAttr("disabled");}

        if(str==1)
        {
            if($('#show_textcbo_company_name').val()==0){$("#cbo_working_company_id").removeAttr('disabled');}
        } else {
            if($('#cbo_working_company_id').val()==0){$("#show_textcbo_company_name").removeAttr('disabled');}
        }
    }

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs("../../", ''); ?>
    <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        <h3 style="width:1190px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1190px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                    <th class="must_entry_caption" width="130">Company Name</th>
                    <th class="must_entry_caption" width="150">Working Company</th>
                    <th class="must_entry_caption" width="150">Working Location</th>
                    <th width="70">Type</th>
                    <th width="70">Year</th>
                    <th width="80">Within Group</th>
                    <th width="100">Sales Order No</th>
                    <th width="100">Booking Type</th>
                    <th width="100">Booking No</th>
                    <th class="must_entry_caption" width="170" colspan="2">Production Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset"
                               onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')"
                               class="formbutton" style="width:70px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td id="td_company">
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        
                        <td width="150" align="center">
                            <?
                                echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fn_disable_com(2);load_drop_down('requires/total_production_activity_report_sales_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>

                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td align="center">
                            <?php
                            $gen_type = array(1 => "Self Order", 2 => "Subcontract Order");
                            echo create_drop_down("cbo_type", 70, $gen_type, "", 1, "-- All --", 0, "load_drop_down( 'requires/total_production_activity_report_sales_controller', $('#cbo_company_name').val()+'**'+this.value,); fnc_active_inactive(this.value);", 0, '');
                            ?>
                        </td>
                        <td>
                            <?php echo create_drop_down("cbo_year", 70, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, ""); ?>
                        </td>
                        <td align="center">
                            <?php echo create_drop_down("cbo_within_group", 70, array(1 => "Yes", 2 => "No"), "", 1, "-- Select --", 0, "", 0, ''); ?>
                        </td>
                        <td>
                            <input type="text" id="txt_sales_order" name="txt_sales_order" class="text_boxes"
                                   style="width:100px;" placeholder="Brows Order No"
                                   onDblClick="openmypage_sales_order();" readonly/>
                        </td>
                        <td>
                            <select id="cbo_booking_type" name="cbo_booking_type" class="combo_boxes" style="width: 100px;">
                                <option value="0">-- Select --</option>
                                <option value="118">Main Fabric Booking</option>
                                <option value="108">Partial Fabric Booking</option>
                                <option value="88">Short Fabric Booking</option>
                                <option value="89">Sample Fabric Booking With Order</option>
                                <option value="90">Sample Fabric Booking Without Order</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" value="" class="text_boxes"
                                   style="width:100px" placeholder="Brows Booking No"
                                   onDblClick="openmypage_booking_no();"  readonly/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from"
                                   value=" <? echo date("d-m-Y", time() - 86400); ?>" class="datepicker"
                                   style="width:70px" placeholder="From Date"/>
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to"
                                   value=" <? echo date("d-m-Y", time() - 86400); ?>" class="datepicker"
                                   style="width:70px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton"   title="Select single day" style="width:80px" value="Show" onClick="fn_report_generated(1)"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                        <td colspan="2" align="center"></td>
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
    set_multiselect('cbo_company_name','0','0','0','0');
    setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];
    $('#cbo_floor_id').val(0);
    $('#cbo_year').val(0);
</script>
</html>