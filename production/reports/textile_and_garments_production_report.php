<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	FSO Wise AOP Send and Receive Report.
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	16-01-2021
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Knitting Production QC Report","../../", 1, 1, $unicode,0,0);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_taka","td_total_hole_defect","td_total_loop_defect","td_total_press_defect_count","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_dust_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_patta_defect_count","td_total_neddle_defect_count","td_total_sinker_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_neps_defect_count","td_total_black_defect_count","td_total_oilink_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect_count","td_total_slub_hole_defect_count","td_total_needle_mark_defect_count","td_total_totalDefect_point","td_total_reject_qty"],

	   col: [28,30,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,59],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	var tableFilters4 = 
	{
		//col_30: "none",
		col_operation: {
		id: ["td_total_qc_pass_qty","td_total_taka","td_total_hole_defect","td_total_loop_defect","td_total_press_defect_count","td_total_lycraout_defect_count","td_total_lycradrop_defect_count","td_total_dust_defect_count","td_total_oilspot_defect_count","td_total_flyconta_defect_count","td_total_slub_defect_count","td_total_patta_defect_count","td_total_neddle_defect_count","td_total_sinker_defect_count","td_total_wheel_defect_count","td_total_count_defect_count","td_total_yarn_defect_count","td_total_neps_defect_count","td_total_black_defect_count","td_total_oilink_defect_count","td_total_setup_defect_count","td_total_pin_hole_defect","td_total_slub_hole_defect","td_total_needle_mark_defect","td_total_totalDefect_point","td_total_reject_qty"],
	   	col: [33,35,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,64],
	   	operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	function fn_report_generated(type)
	{
		var cbo_report_type=$('#cbo_report_type').val();
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_po_company=$('#cbo_po_company').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var txt_style_no=$('#txt_style_no').val().trim();
		var txt_sales_order_no=$('#txt_sales_order_no').val().trim();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
        if (cbo_report_type == 2){
            alert("Please choose type textile");
			$('#cbo_report_type').focus();
			release_freezing();
			return;
		}

		if (txt_sales_order_no=='' && txt_style_no=='')
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
			{
				release_freezing();
				return;
			}
		}
		else{
			if(form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
	
		var data="action=textile_report_generate&&report_format="+type+get_submitted_data_string('cbo_company_name*cbo_report_type*cbo_po_company*cbo_buyer_name*txt_style_no*txt_sales_order_no*txt_sales_order_id*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/textile_and_garments_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
			
	}
		
    function js_garments_report_generate(type)
	{
        
		var cbo_report_type=$('#cbo_report_type').val();
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_po_company=$('#cbo_po_company').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var txt_style_no=$('#txt_style_no').val().trim();
		var hidden_job_id=$('#hidden_job_id').val().trim();
		var txt_order_no=$('#txt_order_no').val().trim();
		var hidden_order_id=$('#hidden_order_id').val().trim();
		var txt_date_from=$('#txt_date_from').val();
		var txt_date_to=$('#txt_date_to').val();
        
		if (cbo_report_type == 1){
            alert("Please choose type garments!");
			release_freezing();
			return;
		}
        if(cbo_po_company == "0"){
            if(form_validation('cbo_po_company','Working Company')==false){
				release_freezing();
				return;
			}
        }

        if(txt_style_no =='' && txt_order_no ==''){
            if(form_validation('txt_date_from*txt_date_to','From date*To date')==false){
				release_freezing();
				return;
			}
        }

        //alert(cbo_company_name); return false;
		var data="action=garments_report_generate&&report_format="+type+get_submitted_data_string('cbo_company_name*cbo_report_type*cbo_po_company*cbo_buyer_name*txt_style_no*txt_order_no*hidden_job_id*hidden_order_id*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/textile_and_garments_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
			
	}


	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");

			$('#report_container2').html(reponse[0]);

			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
		}
	}

	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
	
	function openmypage_booking_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var buyer_id=document.getElementById('cbo_buyer_name').value;
		
		page_link='requires/textile_and_garments_production_report_controller.php?action=booking_popup'+'&company_id='+company_id+'&buyer_name='+buyer_id;
		
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Job Info", 'width=1000px,height=370px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
				document.getElementById('txt_booking_no').value=booking_no;
			}
		}
	}	

	function open_job_no()
	{
		var cbo_report_type = $("#cbo_report_type").val();
        if(cbo_report_type == 1){
            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }
        }
		var buyer=$("#cbo_buyer_name").val();
		var cbo_company_name=$("#cbo_company_name").val();
	    var page_link='requires/textile_and_garments_production_report_controller.php?action=job_popup&buyer='+buyer+'&cbo_company_name='+cbo_company_name+'&cbo_report_type='+cbo_report_type;
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var style_no=job_data[1];

			$("#txt_style_no").val(style_no);
			$("#hidden_job_id").val(job_hidden_id);
		}
	}


	function openmypage_FSONo() { 
		var cbo_company_id = $('#cbo_company_name').val();

		var cbo_buyer_name = $('#cbo_buyer_name').val();

		if (form_validation('cbo_company_name', 'Company') == false) {
			return;
		}
		else {
			var title = 'Job Selection Form';
			var page_link = 'requires/textile_and_garments_production_report_controller.php?cbo_company_id=' + cbo_company_id+'&cbo_buyer_name='+cbo_buyer_name + '&action=fso_no_popup';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../');

			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_booking_data = this.contentDoc.getElementById("hidden_booking_data").value;
				var booking_data = hidden_booking_data.split("**");
				var job_id = booking_data[9];

				$('#txt_sales_order_no').val(booking_data[0]);
				$('#txt_sales_order_id').val(booking_data[1]);
			}
		}
	}

	function open_order_no()
	{
		var cbo_report_type = $("#cbo_report_type").val();
        if(cbo_report_type == 1){
            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }
        }
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
	    var page_link='requires/textile_and_garments_production_report_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no;
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_order_no").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	}

	function fnc_purchase_details(prod_id,action)
	{
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/textile_and_garments_production_report_controller.php?prod_id='+prod_id+'&action='+action,'Details Popup', 'width=1100px,height=320px,center=1,resize=0','../');
		emailwindow.onclose=function()
		{
			
		}
	}

	function fnc_disable_fields(value)
	{
		if(value==1)
		{
            $('#txt_sales_order_no').prop('disabled',false);
			$('#cbo_po_company').prop('disabled',true);
			$('#txt_order_no').prop('disabled',true);
			$('#cbo_po_company').val(0);
			$('#txt_order_no').val('');
			$('#hidden_order_id').val('');
		}
		else
		{
			$('#cbo_po_company').prop('disabled',false);
			$('#txt_order_no').prop('disabled',false);
			$('#txt_sales_order_no').prop('disabled',true);
		}
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
        if (booking_entry_form=='SM') // Sample with order
        {
            var report_title = 'Sample Fabric Booking -With order';
            if(report_print_btn==16) // Print Booking 3
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
                var data = 'action=' + 'show_fabric_booking_report_print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_print_booking_3', true);
            }
            else if(report_print_btn==38) // Print Booking
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
            else if(report_print_btn==64) // Metro Print
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
        }
        if (booking_entry_form=='SMN') // Sample without order
        {
            var report_title = 'Sample Fabric Booking -Without order';
            if(report_print_btn==34) // Print 1
            {
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==35) // Print 2
            {
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==36) // Print 3
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==37) // Print 4
            {
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==64) // Print 5
            {
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==72) // Print 6
            {
                var data = 'action=' + 'show_fabric_booking_report6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report6', true);
            }
            else if(report_print_btn==174) // Print 7
            {
                var data = 'action=' + 'show_fabric_booking_report7' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report7', true);
            }
        }
        if (booking_entry_form==108) // Short Fabric Booking
        {
            var report_title = 'Partial Fabric Booking';
            if(report_print_btn==84) // Print 2
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

                var data = 'action=' + 'show_fabric_booking_report_urmi_per_job' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi_per_job', true);
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

                var data = 'action=' + 'print_booking_3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_3', true);
            }
            else if(report_print_btn==143) // Print 1
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

                var data = 'action=' + 'show_fabric_booking_report_urmi' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
            }
            else if(report_print_btn==151) // AAL Print
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

                var data = 'action=' + 'show_fabric_booking_report_advance_attire_ltd' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_advance_attire_ltd', true);
            }
            else if(report_print_btn==160) // Print 4
            {
                var show_yarn_rate='';
                var data = 'action=' + 'print_booking_5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_5', true);
            }
            else if(report_print_btn==175) // Print 5
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

                var data = 'action=' + 'print_booking_6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_6', true);
            }
            else if(report_print_btn==218) // Northan
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

                var data = 'action=' + 'print_booking_7' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_7', true);
            }
            else if(report_print_btn==220) // Print 8
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

                var data = 'action=' + 'print_booking_northern_new' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_northern_new', true);
            }
            else if(report_print_btn==235) // Print 9
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

                var data = 'action=' + 'print_booking_northern_9' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_northern_9', true);
            }
            else if(report_print_btn==241) // Print 11
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

                var data = 'action=' + 'print_booking_11' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_11', true);
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

                var data = 'action=' + 'print_booking_11' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_15', true);
            }
            else if(report_print_btn==269) // Print 12
            {
                var show_yarn_rate='';
                var data = 'action=' + 'print_booking_12' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_12', true);
            }
            else if(report_print_btn==274) // Print 10
            {
                var show_yarn_rate='';
                var r=confirm("Do You Want to Hide Buyer and Style Name?");
				if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";

                var data = 'action=' + 'print_booking_10' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id+ '&report_title=' + report_title + '&show_yarn_rate=' + show_yarn_rate;

                window.open("../../order/woven_order/requires/partial_fabric_booking_controller.php?data=" + data + '&action=print_booking_10', true);
            }
        }

        return;
    }

    function fnc_fabric_sales_order_print(company_name, booking_id, booking_no, sales_job_no, entry_form, report_print_btn,within_group) {
        var data = company_name + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*Fabric Sales Order Entry';

        if(entry_form==472)
        {
            window.open("../requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
        }
        if(entry_form==109)
        {
            if(report_print_btn==115) // Print
            {
                if (within_group == 1)
                {
                    window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
                }
                else 
                {
                    window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
                }
            }
            else if(report_print_btn==136) // Print 3
            {
                // var data = company_name + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*Fabric Sales Order Entry';
                var update_id="0";
                var report_title="Fabric Sales Order Entry";
                var data="action=fabric_sales_order_print4"+'&companyId='+company_name+'&bookingId='+booking_id+'&bookingNo='+booking_no+'&salesOrderNo='+sales_job_no+'&formCaption='+report_title+'&update_id='+update_id;

                window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print4', true);
            }
            else  // Print 2    //if(report_print_btn==116 )
            {			            
                //window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);

                if (within_group == 1)
                {
                    window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
                }
                else 
                {
                    window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print5', true);
                }
            }

            // window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
        }
    }

    function fnc_order_view(company_id,job_no)
    {
        dhtmlmodal.open('EmailBox', 'iframe', 'requires/textile_and_garments_production_report_controller.php?company_id=' + company_id + '&job_no=' + job_no + '&action=order_popup', 'Order Popup', 'width=640px,height=350px,center=1,resize=1,scrolling=0', '')
    }
</script>

</head>
 
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../"); ?>
		<h3 style="width:1130px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
		<div id="content_search_panel">
		<fieldset style="width:960px;">
            <table class="rpt_table" width="1130" cellpadding="1" cellspacing="2" align="center">
            	<thead>
                	<tr> 
						<th width="80">Type</th>
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="120">Working Company</th>
                        <th width="120">Buyer Name</th>
                        <th width="80">Style No</th>
                        <th width="100">Tex. Ref No</th>
                        <th width="100">Order</th>
                        <th width="180" class="must_entry_caption">Date Range</th>
                        <th width="200">
                        <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','')" />
                        </th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
					<td><?php echo create_drop_down( "cbo_report_type", 80, array(1=>'Textile',2=>'Garments'),"", 0, "",1, "fnc_disable_fields(this.value)","0" ); ?></td>
                    <td align="center"> 
						<?
                        echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/textile_and_garments_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                    </td>
                    <td><? echo create_drop_down( "cbo_po_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "",1 ); ?> </td>

                    <td id="buyer_td">
                    <? echo create_drop_down( "cbo_buyer_name", 120, "","", 1, "-- Select Buyer --", '', "","0" ); ?>
                    </td>
                    
					<td>
                       <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:80px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write"  />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                    	<input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" onDblClick="openmypage_FSONo()" placeholder="Browse/Write" style="width:87px"  />
                    	<input type="hidden" name="txt_sales_order_id" id="txt_sales_order_id" class="text_boxes" style="width:87px" />
                    </td>
					<td>
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:80px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse/Write"  disabled/>
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                    <td>
                  		<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                  		<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" >
                    </td>
                   	<td>
                   	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Fabric" onClick="fn_report_generated(1);" />
                    <input type="button" id="garments_show_button" class="formbutton" style="width:70px" value="Garments" onClick="js_garments_report_generate(2);" />
                  </td>
                </tr>
                </tbody>
            </table>
            <table>
                <tr>
                    <td>
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
    	</fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
