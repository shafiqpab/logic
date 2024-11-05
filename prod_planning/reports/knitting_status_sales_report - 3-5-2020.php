<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Status Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	9-03-2017
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
echo load_html_head_contents("Knitting Status Report", "../../", 1, 1, '', 1, 1);

?>

<script>

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(presentationType) {
        if (form_validation('cbo_company_name', 'Comapny Name') == false) {
            return;
        }

        if (presentationType == 2) {
            var txt_sales_no = $("#txt_sales_no").val();
            var txt_booking_no = $("#txt_booking_no").val();
            if (txt_sales_no == "" && txt_booking_no == "") {
                alert("Please Insert Job or Order No.");
                $('#txt_sales_no').focus();
                return;
            }
        }

        var data = "action=report_generate" + get_submitted_data_string('cbo_type*cbo_company_name*cbo_buyer_name*cbo_buyer_id*txt_sales_no*txt_machine_dia*cbo_party_type*txt_booking_no*txt_program_no*txt_machine_no*txt_date_from*txt_date_to*cbo_knitting_status*cbo_based_on*cbo_year', "../../") + '&presentationType=' + presentationType;
        freeze_window(3);
        http.open("POST", "requires/knitting_status_report_sales_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }


    function fn_report_generated_reponse() {
        if (http.readyState == 4) {
            var response = trim(http.responseText).split("####");
            $('#report_container2').html(response[0]);
            document.getElementById('report_container').innerHTML = '<a href="' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;<input type="hidden" onclick="generate_requisition_report()" value="Requisition Print" name="Print" class="formbutton" style="width:150px"/>&nbsp;&nbsp;<input type="button" onclick="generate_requisition_report_two()" value="Requisition Print2" name="Print" class="formbutton" style="width:150px"/>';

            //append_report_checkbox('table_header_1',1);
            // $("input:checkbox").hide();
            show_msg('3');
            release_freezing();
        }

    }

    function openmypage_booking() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var page_link = 'requires/knitting_status_report_sales_controller.php?action=order_no_search_popup&companyID=' + companyID;
        var title = 'Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            //var machine_no=this.contentDoc.getElementById("hide_machine").value.split("_");
            var order_no = this.contentDoc.getElementById("hide_order_no").value.split("_");
            //var order_id=this.contentDoc.getElementById("hide_order_id").value;

            $('#txt_booking_no').val(order_no[1]);
            //$('#hide_order_id').val(order_id);
        }
    }

    function openmypage_sales_no() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var page_link = 'requires/knitting_status_report_sales_controller.php?action=job_no_search_popup&companyID=' + companyID;
        var title = 'Sales No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_no = this.contentDoc.getElementById("hide_job_no").value.split("_");
            //var job_no=this.contentDoc.getElementById("hide_job_no").value;
            //var job_id=this.contentDoc.getElementById("hide_job_id").value;

            $('#txt_sales_no').val(sales_no[1]);
            //$('#hide_job_id').val(order_id);
        }
    }


    function generate_report(company_id, booking_id, booking_no, sales_job_no,report_print_btn) {
        // print_report( company_id+'*'+program_id, "print", "requires/knitting_status_report_sales_controller" ) ;
        if(report_print_btn==116)
        {
            var data = company_id + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*' + $("div.form_caption").html();
            window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
        }
        else
        {
            var data = company_id + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*' + $("div.form_caption").html();
            window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
        }

        return;
    }

    function generate_report_party(company_id, program_id) {
        print_report(company_id + '*' + program_id, "print", "requires/knitting_status_report_sales_controller");
    }


    function selected_row(rowNo) {
        var isChecked = $('#tbl_' + rowNo).is(":checked");
        var job_no = $('#job_no_' + rowNo).val();
        var source_no=$('#source_id_'+rowNo).val();
        var party_no=$('#party_id_'+rowNo).val();

        if (isChecked == true) {
            var tot_row = $('#tbl_list_search tbody tr').length;
            for (var i = 1; i <= tot_row; i++) {
                if (i != rowNo) {
                    try {
                        if ($('#tbl_' + i).is(":checked")) {
                            // for checking same source
                            var source_noCurrent = $('#source_id_' + i).val();
                            if ((source_no != source_noCurrent)) {
                                alert("Please Select Same Source.");
                                $('#tbl_' + rowNo).attr('checked', false);
                                return;
                            }
                            // for party same
                            var party_noCurrent = $('#party_id_' + i).val();
                            if ((party_no != party_noCurrent)) {
                                alert("Please Select Same Party.");
                                $('#tbl_' + rowNo).attr('checked', false);
                                return;
                            }
                        }
                    }
                    catch (e) {
                        //got error no operation
                    }
                }
            }
        }
    }

    function generate_requisition_report() {
        var program_ids = "";
        var total_tr = $('#tbl_list_search tbody tr').length;
        for (i = 1; i < total_tr; i++) {
            try {
                if ($('#tbl_' + i).is(":checked")) {
                    program_id = $('#promram_id_' + i).val();
                    if (program_ids == "") program_ids = program_id; else program_ids += ',' + program_id;
                }
            }
            catch (e) {
                //got error no operation
            }
        }

        if (program_ids == "") {
            alert("Please Select At Least One Program");
            return;
        }

        print_report(program_ids, "requisition_print", "requires/knitting_status_report_sales_controller");
    }


    function generate_requisition_report_two() {

        //alert('ok')
        var program_ids = "";
        var total_tr = $('#tbl_list_search tbody tr').length;
        var typeForAttention = $("#typeForAttention").val();
        for (i = 1; i < total_tr; i++) {
            try {
                if ($('#tbl_' + i).is(":checked")) {
                    program_id = $('#promram_id_' + i).val();
                    if (program_ids == "") program_ids = program_id; else program_ids += ',' + program_id;
                }
            }
            catch (e) {
                //got error no operation
            }
        }

        if (program_ids == "") {
            alert("Please Select At Least One Program");
            return;
        }
        print_report(program_ids + "**" + typeForAttention, "requisition_print_two", "requires/knitting_status_report_sales_controller");
    }

    function new_window() {
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";

        //$("#tbl_list_search").find('input([name="check"])').hide();
        $('input[type="checkbox"]').hide();

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
            '<html><head><title></title><link rel="stylesheet" type="text/css" href="../../css/style_common.css" media="print" /></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
        $('input[type="checkbox"]').show();
        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "330px";
    }

    function fn_open_machine() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var page_link = 'requires/knitting_status_report_sales_controller.php?action=machine_no_search_popup&companyID=' + companyID;
        var title = 'Machine No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=260px,height=300px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var machine_no = this.contentDoc.getElementById("hide_machine").value.split("_");
            $('#txt_machine_no').val(machine_no[1]);
        }
    }


    function generate_report2(company_id, program_id) {
        var path = '../../';
        print_report(company_id + '*' + program_id + '*' + path, "print", "../requires/yarn_requisition_entry_sales_controller")
    }

</script>


</head>

<body onLoad="set_hotkey();">

<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">

		<? echo load_freeze_divs("../../", ''); ?>

        <h3 style="width:1450px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1450px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Po Company</th>
                    <th>Po Buyer</th>
                    <th>Sales Year</th>
                    <th>Sales Order No</th>

                    <th>Fab. Booking No</th>
                    <th>Machine Dia</th>
                    <th>Type</th>
                    <th>Party Name</th>
                    <th>Program No</th>
                    <th>Machine No</th>
                    <th>Status</th>
                    <th>Based On</th>
                    <th>Date</th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?
							echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "- Select Company -", $selected, "");
							?>
                        </td>
                        <td>
							<?
							echo create_drop_down("cbo_buyer_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "- All Company -", $selected, "load_drop_down( 'requires/knitting_status_report_sales_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );", 0, "");
							// echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
							?>
                        </td>

                        <td id="buyer_td">
							<?
							echo create_drop_down("cbo_buyer_id", 110, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0  group by id, buyer_name order by buyer_name", "id,buyer_name", 1, "- All Buyer -", $selected, "", 0, "");
							// echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
							?>
                        </td>
                        <td>
							<? //date("Y",time())
							echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", "", "", 0, "");
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_sales_no" id="txt_sales_no" class="text_boxes"
                                   onDblClick="openmypage_sales_no();" style="width:80px" placeholder=" Write/Browse"
                                   autocomplete="off">
                            <!--<input type="hidden" name="hide_job_id" id="hide_job_id" readonly> onDblClick="" onChange="$('#hide_job_id').val('');"-->
                        </td>

                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
                                   style="width:90px" placeholder="Write/Browse" onDblClick="openmypage_booking();"
                                   autocomplete="off">

                        </td>
                        <td>
                            <input name="txt_machine_dia" id="txt_machine_dia" class="text_boxes" style="width:55px">
                        </td>
                        <td>
							<?
							$search_by_arr = array(0 => "--All--", 1 => "Inside", 3 => "Outside");
							echo create_drop_down("cbo_type", 102, $search_by_arr, "", 0, "", "", "load_drop_down( 'requires/knitting_status_report_sales_controller',this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_party_type', 'party_type_td' );", 0);
							?>
                        </td>
                        <td id="party_type_td">
							<?
							echo create_drop_down("cbo_party_type", 110, $blank_array, "", 1, "--Select--", "", '', 1);
							?>
                        </td>
                        <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric"
                                   style="width:60px">
                        </td>
                        <td>
                            <input name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:90px"
                                   onDblClick="fn_open_machine()" placeholder="Browse or Write">
                        </td>
                        <td align="center">
							<?
							echo create_drop_down("cbo_knitting_status", 110, $knitting_program_status, "", 0, "- Select -", $selected, "", 0, "");
							?>
                        </td>
                        <td>
							<?
							$based_on_arr = array(1 => "Plan Date", 2 => "Program Date");
							echo create_drop_down("cbo_based_on", 97, $based_on_arr, "", 0, "", "", '', 0);
							?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker"
                                   style="width:50px" placeholder="From Date"/>
                            &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker"
                                   style="width:50px" placeholder="To Date"/>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                        <td colspan="3" align="right">
                            <input type="hidden" id="show_button" class="formbutton" style="width:100px"
                                   value="Job/Order Status" onClick="fn_report_generated(2)"/>
                            <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show"
                                   onClick="fn_report_generated(1)"/>
                            <input type="hidden" id="show_button" class="formbutton" style="width:70px" value="Summary"
                                   onClick="fn_report_generated(3)"/>
                            <input type="hidden" id="show_button" class="formbutton" style="width:60px" value="Short"
                                   onClick="fn_report_generated(4)"/>
                        </td>
                        <td align="center">
                            <input type="reset" name="res" id="res" value="Reset"
                                   onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')"
                                   class="formbutton" style="width:60px"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</form>
</body>
<script>
    set_multiselect('cbo_knitting_status', '0', '0', '', '');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
