<?
/*-------------------------------------------- Comments
Purpose			:	This form will create Tube/Ref. Breakdown Report
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	29-12-2021
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
    echo load_html_head_contents("Tube/Ref. Breakdown Report", "../../", 1, 1, '', '', '');
    ?>
<script>
	if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
	
	//for func_show_details
	function func_show_details(type)
	{
		if (form_validation('cbo_company_name', 'Company') == false)
		{
			return;
		}
		
		if ($('#cbo_within_group').val() == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false)
			{
				return;
			}
		}
		
		if($('#txt_job_no').val() == '' && $('#txt_booking_no').val() == '' && $('#txt_batch_no').val() == '' && $('#txt_program_no').val() == '')
		{
			if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)
			{
				return;
			}
		}
		
		var data = "action=actn_show_details" + get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*txt_job_no*hide_job_id*txt_booking_no*txt_batch_no*txt_program_no*txt_date_from*txt_date_to', "../../") + '&type=' + type;
		freeze_window(5);
		http.open("POST", "requires/tube_ref_breakdown_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = reponse_show_details;
	}
	
	//for reponse_show_details
    function reponse_show_details()
	{
        if (http.readyState == 4)
		{
			var response=trim(http.responseText).split("**");
            $('#container_program_details').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('18');
			//setFilterGrid("table_body",-1);
            release_freezing();
        }
    }

    //for openmypage_job
	function openmypage_job()
	{
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var within_group = $("#cbo_within_group").val();
		
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        if(within_group == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false)
			{
				return;
			}
		}

        var page_link = 'requires/tube_ref_breakdown_report_controller.php?action=actn_job_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
        ;
        var title = 'Style Ref./ Job No. Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=400px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var job_no = this.contentDoc.getElementById("hide_job_no").value;
            var job_id = this.contentDoc.getElementById("hide_job_id").value;

            $('#txt_job_no').val(job_no);
            $('#hide_job_id').val(job_id);
        }
    }
	
	//for openmypage_booking
    function openmypage_booking()
	{
        var companyID = $("#cbo_company_name").val();
        var cbo_within_group = $("#cbo_within_group").val();

        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }
		
        if(cbo_within_group == 1)
		{
			if (form_validation('cbo_buyer_name', 'PO Company') == false)
			{
				return;
			}
		}

        var page_link = 'requires/tube_ref_breakdown_report_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
        var title = 'Booking Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=860px,height=370px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
            $('#txt_booking_no').val(booking_no);
        }
    }
	
	//for new_window
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		//$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><style></style></head><body>'+document.getElementById('container_program_details').innerHTML+'</body</html>');
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		//$("#table_body tr:first").show();
		d.close(); 
	}
	
	function func_fab_qty(batch_no, prog_no, machine_id)
	{
		var report_title = 'Fabric qty';
        var data = 'action=' + 'actn_fab_qty' + '&batch_no='+batch_no+ '&prog_no=' + prog_no+ '&machine_id=' + machine_id + '&formCaption=' + report_title;
        window.open("requires/tube_ref_breakdown_report_controller.php?data=" + data + '&action=actn_fab_qty', true);
        return;
	}
	
	function generate_fso_report(companyId, bookingId, bookingNo, salesOrderNo, update_id, report_print_btn="") 
    {
        var report_title = 'KNITTING & DYEING SCHEDULE';
        var data = 'action=' + 'fabric_sales_order_print4' + '&companyId='+companyId+ '&bookingId=' + bookingId + '&bookingNo='+ bookingNo + '&salesOrderNo='+ salesOrderNo + '&formCaption=' + report_title + '&update_id='+update_id + '&excel_generate='+1;

        window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print4', true);
        return;
    }
	
    function generate_booking_report(txt_booking_no, cbo_company_name, txt_order_no_id, cbo_fabric_natu, cbo_fabric_source, id_approved_id, txt_job_no, booking_entry_form, report_print_btn) 
    {
        if (booking_entry_form==86) // Budget Wise Fabric Booking
        {
            if(report_print_btn==73) // Print B6
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==1) // Print GP
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==2) // Print B1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==3) // Print B2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==4) // Print Cut1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report1' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report1', true);
            }
            else if(report_print_btn==5) // Print Cut2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==6) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==7) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==28) // Print B13
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_akh' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_akh', true);
            }
            else if(report_print_btn==39) // Print Booking2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==45) // Print B4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }            
            else if(report_print_btn==53) // Print B4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_jk' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_jk', true);
            }
            else if(report_print_btn==78) // Print
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==84) // Print 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==85) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==93) // Print B9
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==129) // Print 5
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==269) // Print B12
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==280) // Print B14
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==304) // Print B15
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==339) // Print B18
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==719) // Print B16
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==723) // Print B17
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
        }

        if (booking_entry_form==118) // Main Fabric Booking V2
        {
            var report_title = 'Main Fabric Booking V2';
            if(report_print_btn==73) // Print B6
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                
                var data = 'action=' + 'show_fabric_booking_report_mf' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_mf', true);
            }
            else if(report_print_btn==1) // Print GP
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_mf' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_mf', true);
            }
            else if(report_print_btn==2) // Print B1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==3) // Print B2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==4) // Print Cut1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report1' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report1', true);
            }
            else if(report_print_btn==5) // Print Cut2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==6) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==7) // Print B3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==28) // Print B13
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_akh' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_akh', true);
            }
            else if(report_print_btn==39) // Print Booking2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print39' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print39', true);
            }
            else if(report_print_btn==45) // Print B4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var report_title = 'Budget Wise Fabric Booking';
                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }            
            else if(report_print_btn==53) // Print B5
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_jk' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_jk', true);
            }
            else if(report_print_btn==78) // Print
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==84) // Print 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_islam' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_islam', true);
            }
            else if(report_print_btn==85) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_b6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
            }
            else if(report_print_btn==93) // Print B9
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_libas' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_libas', true);
            }
            else if(report_print_btn==129) // Print 5---pro
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'show_fabric_booking_report_print5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name='+"'"+cbo_company_name+"'"+'&txt_order_no_id='+"'"+txt_order_no_id +"'"+'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+'&id_approved_id='+"'"+id_approved_id+"'"+'&txt_job_no='+"'"+txt_job_no+"'"+'&report_title='+report_title+'&show_yarn_rate=' + show_yarn_rate+'&path=../../';

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?action=" + data + '&action=show_fabric_booking_report_print5', true);
            }
            else if(report_print_btn==193) // Print 4
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print4', true);
            }
            else if(report_print_btn==269) // Print B12
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_knit' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_knit', true);
            }
            else if(report_print_btn==280) // Print B14
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_print14' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print14', true);
            }
            else if(report_print_btn==304) // Print B15
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report10' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report10', true);
            }
            else if(report_print_btn==339) // Print B18
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report18' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report18', true);
            }
            else if(report_print_btn==719) // Print B16
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report16' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report16', true);
            }
            else if(report_print_btn==723) // Print B17
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report17' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report17', true);
            }
        }

        if (booking_entry_form==88) // Short Fabric Booking
        {
            var report_title = 'Short Fabric Booking';
            if(report_print_btn==8) // Print Booking
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==9) // Print Booking 2
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==10) // Print Booking
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==46) // Short Fabric Booking Urmi
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }
            else if(report_print_btn==136) // Print 3
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=print_booking_3', true);
            }
            else if(report_print_btn==244) // Fabric For NTG
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                var data = 'action=' + 'show_fabric_booking_report_ntg' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate + '&report_type=' + 1;

                window.open("../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_ntg', true);
            }
        }

        if (booking_entry_form=="SM") // Sample With Order Fabric Booking
        {
            var report_title = 'Sample Fabric Booking -With order';
            if(report_print_btn==16) // Print Booking 3
            {
                var data = 'action=' + 'show_fabric_booking_report_print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_print_booking_3', true);
            }
            else if(report_print_btn==38) // Print Booking 1
            {
                var show_yarn_rate='';
                var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
                if (r==true)
                {
                    show_yarn_rate="1";
                }
                else
                {
                    show_yarn_rate="0";
                }
                
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==39) // Print Booking 2
            {
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==64) // Metro
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
        }

        if (booking_entry_form=="SMN") // Sample Without Order Fabric Booking
        {
            var report_title = 'Sample Fabric Booking -Without order';
            if(report_print_btn==34) // Print Booking // Print 1
            {
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==35) // Print Booking 2 // Print 2
            {
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==36) // Print Amana // Print 3
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==37) // Print AKH // Print 4
            {
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==64) // Metro // Print 5
            {
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==72) // Print 6 // Print 6
            {
                var data = 'action=' + 'show_fabric_booking_report6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report6', true);
            }
            else if(report_print_btn==174) // Print For UG // Print 7
            {
                var data = 'action=' + 'show_fabric_booking_report7' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report7', true);
            }
        }
        return;
    }

    function generate_ref_report(reference_id) 
    {
        window.open("../requires/program_wise_ref_creation_controller.php?data=" + reference_id + '&action=print', true);
        return;
    }
    
</script>
</head>
<body>
    <div style="width:100%;" align="center">
        <form name="refCreation_1" id="refCreation_1">
          <? echo load_freeze_divs("../../", $permission); ?>
          <h3 style="width:1140px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">Program Wise Ref. Creation</h3>
          <div id="content_search_panel">
            <fieldset style="width:1140px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Within Group</th>
                    <th>PO Company</th>
                    <th>Sales Order No</th>
                    <th>Booking No</th>
                    <th>Batch No</th>
                    <th>Program No</th>
                    <th class="must_entry_caption">Planned Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('refCreation_1','container_program_details','','','')" class="formbutton" style="width:100px"/></th>
                 </thead>
                 <tbody>
                    <tr class="general">
                        <td>
                         <?
                         echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                         ?>
                     </td>
                     <td>
                         <?php echo create_drop_down("cbo_within_group", 120, $yes_no, "", 0, "-- Select --", 0, ""); ?>
                     </td>
                     <td>
                         <?
                         echo create_drop_down("cbo_buyer_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "");
                         ?>
                     </td>
                     <td>
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:110px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                        <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                    </td>
                    <td>
                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:110px" placeholder="Browse Or Write" onDblClick="openmypage_booking();">
                    </td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:110px" placeholder="Write">
                    </td>
                    <td>
                        <input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:110px" placeholder="Write">
                    </td>
                    <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                    </td>
                    <td>
                    	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="func_show_details(1)"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>
</form>
<div id="report_container" align="left" style="margin-top:10px;"></div>
<div id="container_program_details" align="center" style="margin-top:10px;"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>