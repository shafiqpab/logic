<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch Reprot
Functionality	:
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	23-01-2022
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
echo load_html_head_contents("Daily Batch Creation Report", "../../", 1, 1,'','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters =
		{
			col_operation: {
			id: ["value_batch_qnty","value_total_trims_weight","value_batch_weight","value_roll_no"],
			col: [27,28,29,30],
			operation: ["sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}

	function fn_report_generated(operation)
	{
		var fso_number=document.getElementById('txt_job_no').value;
		var booking_no=document.getElementById('txt_booking_no').value;

		if( booking_no !="" || fso_number != "" )
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date*To date')==false)
			{
				return;
			}
		}
		freeze_window(5);
	 	var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_buyer_name*txt_booking_no*txt_job_no*txt_job_hidden_id*txt_date_from*txt_date_to*cbo_date_search_type',    "../../");

		http.open("POST","requires/sales_order_report_for_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_batch_report;
	}
	function fnc_show_batch_report()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid("table_body",-1,tableFilters);

			show_msg('3');
			release_freezing();

		}
	}
	function new_window()
		{
			if(document.getElementById('table_body'))
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				$('#scroll_body tr:first').hide();
			}

			if(document.getElementById('table_body2'))
			{
				document.getElementById('scroll_body_subcon').style.overflow="auto";
				document.getElementById('scroll_body_subcon').style.maxHeight="none";
				$('#scroll_body_subcon tr:first').hide();
			}

			var w = window.open("Surprise", "#");

			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();

			if(document.getElementById('table_body'))
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="397px";
		        $('#scroll_body tr:first').show();
		    }

	        if(document.getElementById('table_body2'))
			{
				document.getElementById('scroll_body_subcon').style.overflow="auto";
				document.getElementById('scroll_body_subcon').style.maxHeight="397px";
		        $('#scroll_body_subcon tr:first').show();
			}
		}

	function openmypage_fabricBooking()
	{
		var cbo_company_id = $('#cbo_company_name').val();	
		var cbo_within_group = $('#cbo_within_group').val();	

		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';	
			var page_link = 'requires/sales_order_report_for_urmi_controller.php?cbo_company_id='+cbo_company_id+'&cbo_within_group='+cbo_within_group+'&action=booking_No_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=420px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var booking_data=this.contentDoc.getElementById("hidden_booking_no").value;	 //Access form field with id="emailfield"
				var job_data=this.contentDoc.getElementById("hidden_job_no").value;
				var job_id=this.contentDoc.getElementById("hidden_job_id").value;
				
				$('#txt_booking_no').val(booking_data);
				$('#txt_job_no').val(job_data);
				$('#txt_job_hidden_id').val(job_id);
				$('#txt_job_no').attr('disabled',true);
			}
		}
	}

	function openmypage_jobNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Job Selection Form';	
			var page_link = 'requires/sales_order_report_for_urmi_controller.php?cbo_company_id='+cbo_company_id+'&action=jobNo_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var job_id=this.contentDoc.getElementById("hidden_job_id").value;
				var job_no=this.contentDoc.getElementById("hidden_job_no").value;	 
				$('#txt_job_no').val(job_no);
				$('#txt_job_hidden_id').val(job_id);
				$('#txt_booking_no').attr('disabled',true);
			}
		}
	}

	function search_populate(str)
    {
        if (str == 1)
        {
            document.getElementById('search_by_th_up').innerHTML = "Delivery Date";
            // $('#search_by_th_up').css('color', 'blue');
        } 
        else if (str == 2)
        {
            document.getElementById('search_by_th_up').innerHTML = "Booking Date";
            // $('#search_by_th_up').css('color', 'blue');
        }
    }

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
         <h3 style="width:920px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:920px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Within Group</th>
                            <th>Buyer</th>
                            <th>Fabric Booking</th>
                            <th>Sales Order</th>
                            <th>Date Type</th>
                            <th width="150" id="search_by_th_up">Delivery Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/sales_order_report_for_urmi_controller',this.value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                        //load_drop_down('requires/sales_order_report_for_urmi_controller',this.value, 'load_drop_down_buyer','cbo_buyer_name_td' );
                                    ?>
                                </td>
                                <td>
                                	<?
										echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- All --", $selected, "",0,"" );
										// load_drop_down('requires/sales_order_report_for_urmi_controller',this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer','cbo_buyer_name_td' );
									?>
                                </td>
                                <td id="cbo_buyer_name_td">
                                	<?
										echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
									?>
                                </td>
                                <td>
                               		<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px;" placeholder="Double Click " onDblClick="openmypage_fabricBooking()" maxlength="30" title="Maximum Characters 30" readonly/>
                                </td>
                                <td>
                                	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" placeholder="Double Click" onDblClick="openmypage_jobNo()" readonly/>
                                    <input type="hidden" name="txt_job_hidden_id" id="txt_job_hidden_id" readonly>
                                </td>
                                <td>
	                                <?
                                    $date_search_type_arr = array(1 => "Delivery Date", 2 => "Booking Date");
                                    $fnc_name = "search_populate(this.value)";
                                    echo create_drop_down("cbo_date_search_type", 100, $date_search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                                    ?>
	                            </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                    &nbsp;To&nbsp;
                                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>
                            </tr>
                            <tr>
                                <td align="center" colspan="12">
                                    <? echo load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id="report_container"></div>
    		<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>