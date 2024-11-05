<?php
/*-------------------------------------------- Comments
Purpose			: This form will create Yarn Requisition Entry for sample without order
Functionality	:	
JS Functions	:
Created by		: Md. Nuruzzaman
Creation date 	: 12.3.2020
Updated by 		:
Update date		:
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Requisition Entry", "../", 1, 1, '', '', '');
?>
<script>
    if ($('#index_page', window.parent.document).val() != 1)
		window.location.href = "../logout.php";
    var permission = '<? echo $permission; ?>';
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_booking
	|--------------------------------------------------------------------------
	|
	*/
    function openmypage_booking()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var title = 'Booking Search PopUp';
        var page_link = 'requires/yarn_requisition_entry_for_sample_without_order_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&buyerID=' + buyerID;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hide_booking_no").value;
            var booking_no_data = booking_no.split("_");
            $('#txt_booking_no').val(booking_no_data[2]);
        }
    }

	/*
	|--------------------------------------------------------------------------
	| fn_report_generated
	|--------------------------------------------------------------------------
	|
	*/
    function fn_report_generated(type)
	{
        if (form_validation('cbo_company_name', 'Comapny Name') == false)
		{
            return;
        }

        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*txt_internal_ref*txt_machine_dia*cbo_type*cbo_planning_status', "../");
        //alert(data);
        freeze_window(4);
        http.open("POST", "requires/yarn_requisition_entry_for_sample_without_order_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

	/*
	|--------------------------------------------------------------------------
	| fn_report_generated_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function fn_report_generated_reponse()
	{
        if (http.readyState == 4)
		{
            var response = trim(http.responseText).split("####");
            $('#report_container').html(response[0]);
            show_msg('4');
            release_freezing();
        }
    }

    
	/*
	|--------------------------------------------------------------------------
	| change_color
	|--------------------------------------------------------------------------
	|
	*/
	function change_color(v_id, e_color)
	{
        if (document.getElementById(v_id).bgColor == "#33CC00")
		{
            document.getElementById(v_id).bgColor = e_color;
        }
        else
		{
            document.getElementById(v_id).bgColor = "#33CC00";
        }
    }

	/*
	|--------------------------------------------------------------------------
	| openmypage_yarnReq
	|--------------------------------------------------------------------------
	|
	*/
    //function openmypage_yarnReq(row_no, knit_dtlsId, companyID, comps, job_no, reqs_no, booking_no,po_ids,program_qnty) {
	function openmypage_yarnReq(row_no, knit_dtlsId, companyID, comps, reqs_no, booking_no, po_ids, program_qnty)
	{
        //alert(row_no+'='+knit_dtlsId+'='+companyID+'='+comps+'='+booking_no+'='+po_ids+'='+program_qnty);
		var title = 'Yarn Requisition Entry Info';
        //var page_link = 'requires/yarn_requisition_entry_controller.php?action=yarn_req_qnty_popup&knit_dtlsId=' + knit_dtlsId + '&companyID=' + companyID + '&comps=' + comps + '&job_no=' + job_no + '&reqs_no=' + reqs_no + '&booking_no=' + booking_no + '&po_ids=' + po_ids + '&program_qnty=' + program_qnty;
		var page_link = 'requires/yarn_requisition_entry_for_sample_without_order_controller.php?action=yarn_req_qnty_popup&knit_dtlsId=' + knit_dtlsId + '&companyID=' + companyID + '&comps=' + comps + '&reqs_no=' + reqs_no + '&booking_no=' + booking_no + '&po_ids=' + po_ids + '&program_qnty=' + program_qnty;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030,height=430px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var yarn_req_qnty = this.contentDoc.getElementById("hidden_yarn_req_qnty").value;
            //$('#txt_yarn_req_qnty_'+row_no).val(yarn_req_qnty);
        }
    }

	/*
	|--------------------------------------------------------------------------
	| generate_report2
	|--------------------------------------------------------------------------
	|
	*/
	function generate_report2(company_id, program_id)
	{
        var path = '../';
        print_report(company_id + '*' + program_id + '*' + path, "print", "requires/yarn_requisition_entry_for_sample_without_order_controller")
    }
</script>
</head>
<body onLoad="set_hotkey();">
<form name="requisitionEntry_1" id="requisitionEntry_1">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs("../", ''); ?>
        <h3 style="width:870px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:870px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all"
                       align="center">
                    <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Booking No</th>
                    <th>IR/CN</th>
                    <th>Machine Dia</th>
                    <th>Type</th>
                    <th>Requisition Status</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionEntry_1','report_container','','','')" class="formbutton" style="width:100px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_requisition_entry_for_sample_without_order_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
                        <td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 130, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, ""); ?></td>
                        <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:110px" placeholder="Browse" onDblClick="openmypage_booking();" readonly></td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_machine_dia" id="txt_machine_dia" class="text_boxes" style="width:80px"></td>
                        <td><? $search_by_arr = array(1 => "Inside", 3 => "Outside", 0 => "Without Source"); echo create_drop_down("cbo_type", 120, $search_by_arr, "", 0, "", "1", '', 0); ?></td>
                        <td><? echo create_drop_down("cbo_planning_status", 125, $planning_status, "", 0, "", $selected, "", "", "1,3"); ?></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)"/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container"></div>
</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>