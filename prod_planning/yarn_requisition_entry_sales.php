<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Requisition Entry
Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	19-08-2013
Updated by 		: 	Al-Hassan	
Update date		: 	10-10-2023	   
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

    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
    var permission = '<? echo $permission; ?>';

    <?
    $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][120] );
    echo "var field_level_data= ". $data_arr . ";\n";
    
    ?>
	var isClickShowBtn = 0;
    function fn_report_generated(type)
	{
        if (form_validation('cbo_company_name', 'Comapny Name') == false)
		{
            return;
        }
		
		isClickShowBtn = 1;
        var data = "action=report_generate" + get_submitted_data_string('cbo_type*cbo_company_name*cbo_within_group*cbo_buyer_name*txt_machine_dia*cbo_planning_status*txt_job_no*hide_job_id*txt_booking_no*txt_barcode*txt_internal_ref*txt_prog*txt_requistionNo', "../");
        freeze_window(4);
        http.open("POST", "requires/yarn_requisition_entry_sales_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

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

    function openmypage_yarnReq(row_no, knit_dtlsId, companyID, comps, job_no, reqs_no, sale_order_id, cbo_within_group,prog_qnty)
	{
        var page_link = 'requires/yarn_requisition_entry_sales_controller.php?action=yarn_req_qnty_popup&knit_dtlsId=' + knit_dtlsId + '&companyID=' + companyID + '&comps=' + comps + '&job_no=' + job_no + '&reqs_no=' + reqs_no + '&sale_order_id=' + sale_order_id + '&cbo_within_group=' + cbo_within_group + '&prog_qnty=' + prog_qnty;
        var title = 'Yarn Requisition Entry Info'; 

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=430px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{ 
            var theform = this.contentDoc.forms[0];
            var yarn_req_qnty = this.contentDoc.getElementById("hidden_yarn_req_qnty").value;
            //$('#txt_yarn_req_qnty_'+row_no).val(yarn_req_qnty);
			fn_report_generated(1);
        }
    }


    function openmypage_job()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }
		
        if($("#cbo_within_group").val() == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false) {
				return;
			}
		}

		var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var within_group = $("#cbo_within_group").val();
        var page_link = 'requires/yarn_requisition_entry_sales_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
        var title = 'Style Ref./ Job No. Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var job_no = this.contentDoc.getElementById("hide_job_no").value;
            var job_id = this.contentDoc.getElementById("hide_job_id").value;
            var booking_id = this.contentDoc.getElementById("hide_booking_no").value;

            $('#txt_job_no').val(job_no);
            $('#hide_job_id').val(job_id);
            $('#hide_booking_no').val(booking_id);
        }
    }

    function openmypage_booking()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }
		
        if($("#cbo_within_group").val() == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false) {
				return;
			}
		}

        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var within_group = $("#cbo_within_group").val();
        var page_link = 'requires/yarn_requisition_entry_sales_controller.php?action=get_program_by_booking_for_req&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
        var title = 'Style Ref./ Job No. Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var booking_id = this.contentDoc.getElementById("hide_booking_no").value;
            $('#txt_booking_no').val(booking_id);
        }
    }

    function openmypage_internal_ref()
    {
        if (form_validation('cbo_company_name', 'Company Name') == false)
        {
            return;
        }
        
        if($("#cbo_within_group").val() == 1)
        {
            if (form_validation('cbo_buyer_name', 'PO Company') == false) {
                return;
            }
        }

        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var within_group = $("#cbo_within_group").val();
        var page_link = 'requires/yarn_requisition_entry_sales_controller.php?action=get_internal_ref&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
        var title = 'IR/IB and Job No. Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
        {
            var theform = this.contentDoc.forms[0];
            var internalRef = this.contentDoc.getElementById("hide_internalref").value;
            $('#txt_internal_ref').val(internalRef);
        }
    }

    function generate_report2(company_id, program_id, within_group, reportType)
	{

        if (reportType==78) 
        {
            print_report(company_id + '*' + program_id, "print_popup", "requires/yarn_requisition_entry_sales_controller");
        }
        else if (reportType==84) 
        {
            var path = '';
            print_report(program_id + '**0**' + path + '**' + within_group, "requisition_print_two", "reports/requires/knitting_status_report_sales_controller");  
        }
        else if (reportType==85) 
        {
              print_report(company_id + '*' + program_id + '*' + 'hyperLink', "requisition_print3", "requires/yarn_requisition_entry_sales_controller");
        }
        else
        {
            var path = '../';
            print_report(program_id + '**0**' + path + '**' + within_group, "requisition_print_two", "reports/requires/knitting_status_report_sales_controller");  
        }

    }

    function openmypage_barcode()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var within_group = $("#cbo_within_group").val();
        var page_link = 'requires/yarn_requisition_entry_sales_controller.php?action=barcode_popup&companyID=' + companyID + '&within_group=' + within_group;
        ;
        var title = 'Style Ref./ Job No. Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
		{
            var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
            
            if(barcode_nos!="")
            {
                $("#txt_barcode").val(barcode_nos);
                fn_report_generated(1);
               //openmypage_prog(1);
            }
        }
    }

    $('#txt_barcode').live('keydown', function(e) {
        if (e.keyCode === 13) 
        {
            e.preventDefault();
            var txt_barcode= $("#txt_barcode").val();
            if(txt_barcode)
			{
                fn_report_generated(1);
                //openmypage_prog(1);
            }
        }
    });
	
	$(".drag-controls").live("click",function(){
		if(isClickShowBtn == 1)
		{
			fn_report_generated(1);
		}
	});
</script>
</head>
<body onLoad="set_hotkey();">
<form name="requisitionEntry_1" id="requisitionEntry_1">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs("../", ''); ?>
        <h3 style="width:1540px;" align="left" id="accordion_h1" class="accordion_h"
            onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1440px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Within Group</th>
                    <th>PO Company</th>
                    <th>Sales Order No</th>
                    <th>Booking No</th>
                    <th title="Internal Ref">IR/IB</th>
                    <th>Prog. No</th>
                    <th>Req. No</th>
                    <th>Machine Dia</th>
                    <th>Type</th>
                    <th>Requisition Status</th>
                    <th>Barcode</th>
                    <th>
                        <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionEntry_1','report_container','','','')" class="formbutton" style="width:100px"/></th>
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td>
							<?
							echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
							?>
                        </td>
                        <td>
							<?php echo create_drop_down("cbo_within_group", 110, $yes_no, "", 0, "-- Select --", 0, ""); ?>
                        </td>
                        <td>
							<?
							echo create_drop_down("cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:130px"
                                   placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                            <input type="hidden" name="hide_booking_no" id="hide_booking_no" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
                                   style="width:130px" placeholder="Browse" onDblClick="openmypage_booking();"
                                   autocomplete="off" readonly>
                            <input type="hidden" name="hide_booking_id" id="hide_booking_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_internal_ref();" autocomplete="off" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_prog" id="txt_prog" class="text_boxes" style="width:100px" placeholder="Write">
                        </td>
                        <td>
                            <input type="text" name="txt_requistionNo" id="txt_requistionNo" class="text_boxes" style="width:100px" placeholder="Write">
                        </td>
                        <td>
                            <input name="txt_machine_dia" id="txt_machine_dia" class="text_boxes" style="width:80px">
                        </td>
                        <td>
							<?
							$search_by_arr = array(1 => "Inside", 3 => "Outside", 0 => "Without Source");
							echo create_drop_down("cbo_type", 120, $search_by_arr, "", 0, "", "1", '', 0);
							?>
                        </td>
                        <td>
							<?
							echo create_drop_down("cbo_planning_status", 125, $planning_status, "", 0, "", $selected, "", "", "1,3");
							?>
                        </td>
                        <td>
                       		<input type="text" name="txt_barcode" id="txt_barcode" class="text_boxes" style="width:100px" onDblClick="openmypage_barcode()" placeholder="Write/Scan/Browse">
                    	</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)"/>
                        </td>
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