<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Daily Sales Order Report
Functionality	:	
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	02-02-2022
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
echo load_html_head_contents("Daily Sales Order Report", "../../", 1, 1, '', '', '');

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
		
        if (txt_sales_order != "" || txt_booking_no != "") {
            if (form_validation('cbo_company_name', 'Company') == false) {
                return;
            }
        }
        else {
            if (form_validation('cbo_company_name*txt_date_from*txt_date_to*cbo_date_category', 'Company*From Date*To Date*Date Type') == false) {
                return;
            }
        }
		

        var report_title = $("div.form_caption").html();

        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_sales_order_type*cbo_cust_buyer_id*txt_sales_order*txt_booking_no*cbo_date_category*txt_date_from*txt_date_to', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;

        freeze_window(5);
        http.open("POST", "requires/daily_sales_order_report_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse() 
    {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("####");

            $("#report_container2").html(response[0]);
          
            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
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
        document.getElementById('scroll_body').style.maxHeight = "none";

        $("tr th:first-child").show();
        $("tr td:first-child").show();
    }

    function openmypage_sales_order() 
    {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var ordType = $("#cbo_type").val();
        var page_link = 'requires/daily_sales_order_report_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&ordType=' + ordType;
        var title = 'Sales Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;

            $('#txt_sales_order').val(sales_job_no);
        }
    }

    function change_title(id)
	{
		// alert(id);
		var text = '';
		switch(id)
		{
			case '1' :
				text = "Insert Date";
				break;
			case '2' :
				text = "Booking Date";
				break;
			case '3' :
				text = "Delivery Start Date";
				break;
            case '4' :
				text = "Delivery End Date";
				break;
            default:
                text = "Date Range";
		}
		// alert(text);
		$("#date_category").text(text);
	}

    function generate_report(company_id, booking_id, booking_no, sales_job_no, within_group) 
    {        
        var data = company_id + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*' + $("div.form_caption").html();
        
        if (within_group == 2){
        window.open("../../production/requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
        }else{
            alert("This report available for Within Group No");
        }
       
        return;
    }

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", ''); ?>
    <form name="dailySalesOrderReport_1" id="dailySalesOrderReport_1">
        <h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1000px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                    <th class="must_entry_caption" width="130">Company Name</th>
                    <th width="150">Sales Order Type</th>
                    <th width="100">Cust Buyer</th>
                    <th width="100">Sales Order No</th>
                    <th width="100">Sales / Booking No</th>
                    <th width="150">Date Type</th>
                    <th class="must_entry_caption" id="date_category" width="170" colspan="2">Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset"
                               onClick="reset_form('dailySalesOrderReport_1','report_container*report_container2','','','')"
                               class="formbutton" style="width:70px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?php
							echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_sales_order_report_controller', $('#cbo_company_name').val(), 'load_drop_down_cust_buyer', 'cust_buyer_td');");
							?>
                        </td>
                        <td align="center">
							<?php
                            echo create_drop_down("cbo_sales_order_type", 150, $sales_order_type_arr, "", 1, "--Select--", "", "", 0);
							?>
                        </td>
                        <td id="cust_buyer_td">
							<?php echo create_drop_down("cbo_cust_buyer_id", 100, $blank_array, "", 1, "-- Select Cust Buyer --", 0, "",0, ''); ?>
                        </td>
                      
                        <td>
                            <input type="text" id="txt_sales_order" name="txt_sales_order" class="text_boxes"
                                   style="width:100px;" placeholder="Write or Brows Order No"
                                   onDblClick="openmypage_sales_order();" />
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" value="" class="text_boxes"
                                   style="width:100px" placeholder="Write Booking No"
                                     />
                        </td>
                        <td>
							<?php 
                            $date_cat = array(1 => "Insert Date", 2 => "Booking Date", 3 => "Delivert Start Date" , 4 => "Delivert End Date");
							echo create_drop_down( "cbo_date_category", 150, $date_cat,"", 1, "-- Select --", 0, "change_title(this.value)",0 ); 
                            ?>
                        </td>
                        <td align="center">
                        <input name="txt_date_from" value="" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                           
                        </td>
                        <td>
                        <input name="txt_date_to" value="" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px"
                                   value="Show" onClick="fn_report_generated(1)"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <br>
    </form>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>