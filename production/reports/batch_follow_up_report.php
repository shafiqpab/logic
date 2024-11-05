
<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Bundle Status Report.
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	09-09-2021
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

//-----------------------------------------------------------------------------------------------------
echo load_html_head_contents("Batch Follow Up Report", "../../", 1, 1,'',1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
 	var permission = '<? echo $permission; ?>';
	
	function openmypage_job() 
	{
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var within_group = $("#cbo_within_group").val();
		var page_link = 'requires/batch_follow_up_report_controller.php?action=style_ref_search_popup&companyID=' + companyID + '&buyerID=' + buyerID + '&within_group=' + within_group;
		;
		var title = 'Style Ref./ Job No. Search';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var job_no = this.contentDoc.getElementById("hide_job_no").value;
			var job_id = this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_sales_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage_booking() 
	{
		if (form_validation('cbo_company_name', 'Company Name') == false) {
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_within_group = $("#cbo_within_group").val();
		var page_link = 'requires/batch_follow_up_report_controller.php?action=booking_no_search_popup&companyID=' + companyID + '&cbo_within_group=' + cbo_within_group;
		var title = 'Booking Search';

		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0', '../');
		emailwindow.onclose = function () {
			var theform = this.contentDoc.forms[0];
			var booking_no = this.contentDoc.getElementById("hidden_booking_no").value;
			var booking_num = this.contentDoc.getElementById("hidden_booking_num").value;

			$('#txt_booking_no').val(booking_no);
			$('#hide_booking_id').val(booking_num);
		}
	}

	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();	
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Roll Position Tracking Report';	
			var page_link = 'requires/batch_follow_up_report_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';		  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=470px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose = function () 
			{
				var theform = this.contentDoc.forms[0];
				var batch_number = this.contentDoc.getElementById("selected_batch_no").value;
				var hdn_batch_no = this.contentDoc.getElementById("selected_batch_id").value;

				$('#txt_batch_no').val(batch_number);
				$('#hdn_batch_no').val(hdn_batch_no);
			}
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| fnc_report_generated
	|--------------------------------------------------------------------------
	|
	*/
	function fnc_report_generated()
	{
		var job_no = $('#txt_sales_job_no').val();
		var booking_no = $('#txt_booking_no').val();
		var batch_no = $('#txt_batch_no').val();

		if(job_no=="" && booking_no=="" && batch_no=="")
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From Date*To Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name','Company')==false )
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_booking_no*txt_date_from*txt_date_to*txt_sales_job_no*cbo_year_selection*cbo_within_group*hide_job_id*hide_booking_id*txt_batch_no*hdn_batch_no', "../../")+'&report_title='+report_title;

		freeze_window(3);
		http.open("POST","requires/batch_follow_up_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	/*
	|--------------------------------------------------------------------------
	| fnc_report_generated_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// setFilterGrid("tbl_list_dtls",-1,'');
	 		show_msg('3');
			release_freezing();
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| fnc_print_and_excel_file_generated
	|--------------------------------------------------------------------------
	|
	*/
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		// $('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		// $('#scroll_body tr:first').show();
		
		
		//document.getElementById('scroll_body').style.maxWidth="120px";
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
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="bulk_yarn_allocation_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 align="left" id="accordion_h1" style="width:990px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:990px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
							<th>Buyer Name</th>
							<th>Within Group</th>
							<th>Sales Order No</th>
							<th>Booking No</th>
							<th>Batch No</th>
							<th colspan="2" class="must_entry_caption">Date Range</th>							
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('bulk_yarn_allocation_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                      	<td>
							<?php
							echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/batch_follow_up_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
						</td>
						<td id="buyer_td">
							<?php
							echo create_drop_down("cbo_buyer_name", 120, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
							?>
						</td>
						<td>
							<?php
							echo create_drop_down("cbo_within_group", 120, array(1 => "Yes", 2 => "No"), "", 1, "-- Select --", 0, "", 0, "");
							?>
						</td>
						<td>
							<input type="text" name="txt_sales_job_no" id="txt_sales_job_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_job();" />
							<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						</td>						                                                
						<td id="search_by_td">
							<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:115px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"/>
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</td>
						<td align="center">
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" placeholder="Write Or Browse" onDblClick="openmypage_batchNo();" style="width:110px" />
                            <input type="hidden" name="hdn_batch_no" id="hdn_batch_no" class="text_boxes"  onDblClick="" style="width:110px" />
                        </td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
						</td>
						<td>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
						</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table> 
                <div><?php echo load_month_buttons(1); ?></div>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div align="center" id="report_container2"></div>
   
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
