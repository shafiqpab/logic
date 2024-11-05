<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dailly Finished Fabric Delivery to Garments FSO Report Multi Issue Challan.
Functionality	:
JS Functions	:
Created by		:	Tipu
Creation date 	: 	29-12-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dailly Finished Fabric Delivery to Garments FSO Report", "../../../", 1, 1,'',1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission = '<? echo $permission; ?>';
	var tableFilters =
	{
		col_27: "none",
		col_operation: {
			id: ["value_total_issue_qnty"],
			col: [20],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}	
	}

	function fn_report_generated(type)
	{
		var txt_fso_no 		=  trim($("#txt_fso_no").val());
		var booking_no_show =  trim($("#txt_booking_no_show").val());
		var batch_no        =  trim($("#txt_batch_no").val());
		var challan_no 		=  trim($("#txt_challan_no").val());
		var validate_id = "";
		var validate_msg = "";
		if(txt_fso_no =="" && booking_no_show =="" && batch_no =="" && challan_no =="")
		{
			validate_id += "*txt_date_from*txt_date_to";
			validate_msg += "**Date From*Date To";
		}
		if (form_validation('cbo_company_id'+validate_id,'Comapny Name'+validate_msg)==false)
		{
			return;
		}
		var report_action= "report_generate";
		var report_title=$( "div.form_caption" ).html();
		var data="action="+report_action+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_buyer_name*txt_fso_no*hdn_fso_id*txt_booking_no_show*txt_booking_no*txt_batch_no*txt_batch_id*txt_date_from*txt_date_to*cbo_year*txt_challan_no',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/daily_finished_fabric_delivery_to_garments_fso_multi_issue_challan_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
	

	function openmypage_fsoNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_within_group = $('#cbo_within_group').val();
		//var color_from_library = $('#color_from_library').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'FSO Selection Form';	
			var page_link = 'requires/daily_finished_fabric_delivery_to_garments_fso_multi_issue_challan_report_controller.php?action=fsoNo_popup&cbo_company_id='+cbo_company_id+'&cbo_within_group='+cbo_within_group;
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var fso_id=this.contentDoc.getElementById("hidden_fso_id").value;
				var fso_no=this.contentDoc.getElementById("hidden_fso_no").value;	 
				$('#txt_fso_no').val(fso_no);
				$('#hdn_fso_id').val(fso_id);
			}
		}
	}
	function openPopupBatch()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/daily_finished_fabric_delivery_to_garments_fso_multi_issue_challan_report_controller.php?action=batch_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Batch Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var batch_no=this.contentDoc.getElementById("hide_batch_no").value;
			var batch_id=this.contentDoc.getElementById("hide_batch_id").value;
			$('#txt_batch_no').val(batch_no);
			$('#txt_batch_id').val(batch_id);
		}
	}
	function openPopupBooking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/daily_finished_fabric_delivery_to_garments_fso_multi_issue_challan_report_controller.php?action=booking_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Order No Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_booking_no_show').val(booking_no);
			$('#txt_booking_no').val(booking_id);
		}
	}	
	function openmypage_roll_qnty(ref_data,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='920px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_finished_fabric_delivery_to_garments_fso_multi_issue_challan_report_controller.php?companyID='+companyID+'&ref_data='+ref_data+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function func_onchange_company()
	{
		$('#cbo_within_group').val(0);
		if($('#cbo_company_id').val() != 0)
		{
			$('#cbo_buyer_name').val(0).attr('disabled', 'disabled');
		}
		else
		{
			$('#cbo_buyer_name').val(0).removeAttr('disabled');
		}
	}
	function generate_report(company_id, booking_id, booking_no, sales_job_no,report_print_btn) 
	{
        // print_report( company_id+'*'+program_id, "print", "requires/knitting_status_report_sales_controller" ) ;
        if(report_print_btn==116)
        {
            var data = company_id + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*' + $("div.form_caption").html();
            window.open("../../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print3', true);
        }
        else
        {
            var data = company_id + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*' + $("div.form_caption").html();
            window.open("../../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
        }

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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report1', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_akh', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_jk', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_mf', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_mf', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report1', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_akh', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print39', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_jk', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_islam', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_b6', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_libas', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?action=" + data + '&action=show_fabric_booking_report_print5', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print4', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_knit', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report_print14', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report10', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report18', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report16', true);
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

                window.open("../../../order/woven_order/requires/fabric_booking_urmi_controller.php?data=" + data + '&action=show_fabric_booking_report17', true);
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

                window.open("../../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
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

                window.open("../../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
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

                window.open("../../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
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

                window.open("../../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_urmi', true);
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

                window.open("../../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=print_booking_3', true);
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

                window.open("../../../order/woven_order/requires/short_fabric_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_ntg', true);
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

                window.open("../../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report_print_booking_3', true);
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

                window.open("../../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==39) // Print Booking 2
            {	
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==64) // Metro Print
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&txt_order_no_id=' + txt_order_no_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&cbo_fabric_source=' + cbo_fabric_source + '&id_approved_id=' + id_approved_id + '&txt_job_no=' + "'"+txt_job_no+"'" + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
        }
        if (booking_entry_form=='SMN') // Sample without order
        {
            var report_title = 'Sample Fabric Booking -Without order';
            if(report_print_btn==34) // Print 1
            {
                var data = 'action=' + 'show_fabric_booking_report' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report', true);
            }
            else if(report_print_btn==35) // Print 2
            {
                var data = 'action=' + 'show_fabric_booking_report2' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report2', true);
            }
            else if(report_print_btn==36) // Print 3
            {
                var data = 'action=' + 'show_fabric_booking_report3' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report3', true);
            }
            else if(report_print_btn==37) // Print 4
            {
                var data = 'action=' + 'show_fabric_booking_report4' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report4', true);
            }
            else if(report_print_btn==64) // Print 5
            {
                var data = 'action=' + 'show_fabric_booking_report5' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report5', true);
            }
            else if(report_print_btn==72) // Print 6
            {
                var data = 'action=' + 'show_fabric_booking_report6' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report6', true);
            }
            else if(report_print_btn==174) // Print 7
            {
                var data = 'action=' + 'show_fabric_booking_report7' + '&txt_booking_no='+"'"+txt_booking_no+"'" + '&cbo_company_name=' + cbo_company_name + '&id_approved_id=' + id_approved_id + '&cbo_fabric_natu=' + cbo_fabric_natu + '&report_title=' + report_title;

                window.open("../../../order/woven_order/requires/sample_booking_non_order_controller.php?data=" + data + '&action=show_fabric_booking_report7', true);
            }
        }

        return;
    }
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
    <div id="content_search_panel" >
    	<fieldset style="width:930px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th class="must_entry_caption">Company</th>
                    <th>Within Group</th>
                    <th>Party Name</th>
                    <th>Year</th>
                    <th>Sales Order No</th>
                    <th>Booking No</th>
                    <th>Batch No</th>
                    <th>Multi Issue Challan No</th>
                    <th colspan="2" class="must_entry_caption">Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
            	<tbody>
                    <tr align="center" class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "func_onchange_company();" ); ?>
                        </td>
                        <td>
							<?php
							echo create_drop_down("cbo_within_group", 70, array(1 => "Yes", 2 => "No"), "", 1, "-- Select --", 0,"load_drop_down( 'requires/daily_finished_fabric_delivery_to_garments_fso_multi_issue_challan_report_controller',this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_party_type', 'buyer_td' );",0 );
							?>
						</td>
                        <td id="buyer_td">
							<?php
							echo create_drop_down("cbo_buyer_name", 120, $blank_array, "", 1, "-- All Party --", $selected, "", 0, "");
							?>
						</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:80px;" placeholder="Write/Browse" onDblClick="openmypage_fsoNo()"/>
                            <input type="hidden" name="hdn_fso_id" id="hdn_fso_id" readonly>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no_show" name="txt_booking_no_show" class="text_boxes" style="width:100px" onDblClick="openPopupBooking()" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_booking_no" name="txt_booking_no"/>
                        </td>
                        <td>
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:100px" onDblClick="openPopupBatch()" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_batch_id" name="txt_batch_id"/>
                        </td>
						<td>
                            <input type="text" id="txt_challan_no" name="txt_challan_no" class="text_boxes" style="width:100px"  placeholder="Write" />
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" readonly/>
                        </td>
                        <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" /></td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>                        
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
