<?php
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Program Wise Knitting Production Report
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman 
Creation date 	: 	14.11.2020
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
echo load_html_head_contents("Program Wise Knitting Production", "../../", 1, 1, '', '', '');

?>
<script>
    if ($('#index_page', window.parent.document).val() != 1)
		window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';
	//document.oncontextmenu = function () { return false; }

    function fn_report_generated(report_type)
	{
		var txt_sales_order = $('#txt_sales_order').val();
		var txt_date = $('#txt_date').val();

		if (txt_sales_order != "")
		{
			if (form_validation('cbo_company_name', 'Company') == false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_name*txt_date', 'Company*From Date') == false)
			{
				return;
			}
		}

		var report_title = $("div.form_caption").html();
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_type*cbo_year*txt_sales_order*cbo_knitting_source*txt_date*cbo_within_group*cbo_booking_type*cbo_floor_id', "../../") + '&report_title=' + report_title + '&report_type=' + report_type;
		
		freeze_window(5);
		http.open("POST", "requires/program_wise_knitting_production_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
    }
	
    function fn_report_generated_reponse()
	{
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("####");

            $("#report_container2").html(response[0]);
            //alert (response[0]);
            //document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function fn_report_generated_today()
	{
        var job_no = $('#txt_job').val();
        var txt_sales_order = $('#txt_sales_order').val();
        if (job_no != "" || txt_sales_order != "") {
            if (form_validation('cbo_company_name', 'Company') == false) {
                return;
            }
        }
        else {
            if (form_validation('cbo_company_name*txt_date', 'Company*From Date') == false) {
                return;
            }
        }

        var report_title = $("div.form_caption").html();

        var data = "action=report_generate_today" + get_submitted_data_string('cbo_company_name*cbo_type*cbo_buyer_name*cbo_year*txt_job*txt_sales_order*cbo_knitting_source*cbo_floor_id*txt_date*txt_date_to*cbo_within_group', "../../") + '&report_title=' + report_title;

        freeze_window(5);
        http.open("POST", "requires/program_wise_knitting_production_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    //new_window
	function new_window()
	{
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
    }

    function selected_row(rowNo)
	{
        var isChecked = $('#tbl_' + rowNo).is(":checked");
        var in_out = $('#source_' + rowNo).val();
        if (isChecked == true)
		{
            var tot_row = $('#table_body tbody tr').length;
            for (var i = 1; i <= tot_row; i++)
			{
                if (i != rowNo)
				{
                    try
					{
                        if ($('#tbl_' + i).is(":checked")) {
                            var inOut_noCurrent = $('#source_' + i).val();
                            if ((in_out != inOut_noCurrent)) {
                                alert("Please Select Same Kniting Source.");
                                $('#tbl_' + rowNo).attr('checked', false);
                                return;
                            }
                        }
                    }
                    catch (e)
					{
                        //got error no operation
                    }
                }
            }
        }
    }

    //generate_delivery_challan_report
	function generate_delivery_challan_report()
	{
        var program_ids = "";
        var source_ids = "";
        var total_tr = $('#table_body tbody tr').length;
        var company = $('#cbo_company_name').val();
        var from_date = $('#txt_date').val();
        var to_date = $('#txt_date_to').val();
        for (i = 1; i < total_tr; i++)
		{
            try
			{
                if ($('#tbl_' + i).is(":checked"))
				{
                    program_id = $('#production_id_' + i).val();
                    source_id = $('#source_' + i).val();
                    if (program_ids == "") program_ids = program_id; else program_ids += ',' + program_id;
                    if (source_ids == "") source_ids = source_id; else source_ids += ',' + source_id;
                }
            }
            catch (e)
			{
                //got error no operation
            }
        }

        if (program_ids == "")
		{
            alert("Please Select At Least One Program");
            return;
        }
        //alert (program_ids)
        print_report(program_ids + '_' + source_ids + '_' + company + '_' + from_date + '_' + to_date, "delivery_challan_print", "requires/program_wise_knitting_production_controller");
    }

    //openmypage_sales_order
	function openmypage_sales_order()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var ordType = $("#cbo_type").val();
        var cbo_within_group = $("#cbo_within_group").val();
        var page_link = 'requires/program_wise_knitting_production_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&ordType=' + ordType + '&cbo_within_group=' + cbo_within_group;
        var title = 'Sales Order No Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;
            $('#txt_sales_order').val(sales_job_no);
        }
    }
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", ''); ?>
    <form name="programWiseKnittingProduction" id="programWiseKnittingProduction">
        <h3 style="width:1050px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption" width="130">Company Name</th>
                        <th width="120">Type</th>
                        <th width="70">Year</th>
                        <th width="80">Within Group</th>
                        <th width="120">Sales Order No</th>
                        <th width="120">Booking Type</th>
                        <th width="120">Knitting Source</th>
                        <th width="120">Floor</th>
                        <th class="must_entry_caption" width="70">Production Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('programWiseKnittingProduction', 'report_container*report_container2', '', '', '')" class="formbutton" style="width:70px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?php
							echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/program_wise_knitting_production_controller', $('#cbo_company_name').val(), 'load_drop_down_floor', 'floor_td');");
							?>
                        </td>
                        <td align="center">
							<?php
							$gen_type = array(1 => "Self Order", 2 => "Subcontract Order");
							echo create_drop_down("cbo_type", 120, $gen_type, "", 1, "-- All --", 0, "", 0, '');
							?>
                        </td>
                        <td>
							<?php echo create_drop_down("cbo_year", 70, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, ""); ?>
                        </td>
                        <td align="center">
							<?php echo create_drop_down("cbo_within_group", 70, array(1 => "Yes", 2 => "No"), "", 1, "-- Select --", 0, "", 0, ''); ?>
                        </td>
                        <td>
                            <input type="text" id="txt_sales_order" name="txt_sales_order" class="text_boxes" style="width:120px;" placeholder="Brows Order No" onDblClick="openmypage_sales_order();" readonly/>
                        </td>
                        <td>
                            <select id="cbo_booking_type" name="cbo_booking_type" class="combo_boxes" style="width: 120px;">
                                <option value="0">-- Select --</option>
                                <option value="118">Main Fabric Booking</option>
                                <option value="108">Partial Fabric Booking</option>
                                <option value="88">Short Fabric Booking</option>
                                <option value="89">Sample Fabric Booking With Order</option>
                                <option value="90">Sample Fabric Booking Without Order</option>
                            </select>
                        </td>
                        <td>
							<?php echo create_drop_down("cbo_knitting_source", 120, $knitting_source, "", 1, "-- All --", 0, "", 0, ''); ?>
                        </td>
                        <td id="floor_td">
                            <?php echo create_drop_down("cbo_floor_id", 120, $blank_array, "", 1, "-- Select Floor --", 0, "", 0); ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date" id="txt_date" value=" <? echo date("d-m-Y", time()); ?>" class="datepicker" style="width:70px" placeholder="From Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:120px" value="Prog. Wise Balance" onClick="fn_report_generated(1)"/>
                            <input type="button" id="show_button" class="formbutton"   title="Select signle a day"style="width:120px" value=" Prod. Against FSO" onClick="fn_report_generated(2)"/>
                        </td>
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