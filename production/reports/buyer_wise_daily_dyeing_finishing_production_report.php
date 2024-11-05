<?
/*-------------------------------------------- Comments
Purpose			: 	This Report will Buyer Wise Daily Dyeing Finishing Production Report
Functionality	:
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	20-09-2020
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
echo load_html_head_contents("Buyer Wise Daily Dyeing Finishing Production Report","../../", 1, 1, $unicode,1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		var cbo_company_name=$("#cbo_company_name").val();
		var txt_job_no=$("#txt_job_no").val();
		var txt_booking_number=$("#txt_booking_number").val();
		var txt_batch_number=$("#txt_batch_number").val();

		if(txt_job_no=='' && txt_booking_number=='' && txt_batch_number=='')
		{
			var dataId='cbo_company_name*txt_date_from*txt_date_to';
			var dataMsg='Company*From date Fill*To date Fill';
		}
		else
		{
			var dataId='cbo_company_name';
			var dataMsg='Working Company';
		}
		
		if(form_validation(dataId,dataMsg)==false)
		{
			return;
		}
		freeze_window(5);
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_type*cbo_process_type*cbo_company_name*cbo_location_id*txt_job_no*txt_booking_number*cbo_buyer_name*txt_batch_number*txt_batch_id*cbo_floor_id*cbo_machine_name*txt_batch_color*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;

		
		
		http.open("POST","requires/buyer_wise_daily_dyeing_finishing_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_response;
	}

	function fn_report_generated_response()
	{
		if(http.readyState ==4)
		{
			var response=trim(http.responseText).split("****");
			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
			setFilterGrid("table_body",-1);
		}
	}
	
	
	function new_window(type)
	{

		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
			document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="none";
		}

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
			document.getElementById('scroll_body1').style.overflowY="scroll";
			document.getElementById('scroll_body1').style.maxHeight="none";

		}
		$("tr th:first-child").show();
		$("tr td:first-child").show();
	}
	<!--BookingNumber -->
	
	function bookingnumber()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var txt_batch_id=document.getElementById('txt_batch_number').value;
		var page_link="requires/buyer_wise_daily_dyeing_finishing_production_report_controller.php?action=bookingnumbershow&company_name="+company_name;
		var title="Booking Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('txt_booking_id').value=batch[0];
			document.getElementById('txt_booking_number').value=batch[1];
			release_freezing();
		}
	}
	<!--BatchNumber -->
	function batchnumber()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var txt_batch_number=document.getElementById('txt_batch_number').value;
		var page_link="requires/buyer_wise_daily_dyeing_finishing_production_report_controller.php?action=batchnumbershow&company_name="+company_name;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('txt_batch_id').value=batch[0];
			document.getElementById('txt_batch_number').value=batch[1];
			document.getElementById('batch_extension').value=batch[2];
			release_freezing();
		}
	}





</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
			<h3 style="width:1400px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>  <div id="content_search_panel" >
				<fieldset style="width:1400px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
                            <th class="must_entry_caption">Prod Company</th>
							<th>Location</th>
                            <th>Order Type</th>
                            <th>Buyer</th>
							<th>Job No</th>
							<th>Booking</th>
                            <th>Batch No</th>
							<th>Process</th>
                            <th>Batch Color</th>
							<th>Floor</th>
							<th>Machine Name</th>
							<th class="must_entry_caption" id="th_date">Date</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
						</thead>
						<tbody>
						<tr>
                            <td align="center">
                            	<?
                            	echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/buyer_wise_daily_dyeing_finishing_production_report_controller', this.value, 'load_drop_down_location','location_td');load_drop_down('requires/buyer_wise_daily_dyeing_finishing_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_name_td');" );
                            	?>
                            </td>
                            <td id="location_td" align="center">
                            	<?
                            	echo create_drop_down( "cbo_location_id", 110, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            	?>
                            </td>
                            
                            <td align="center">
                                <?
                                $report_type=array(0=>"--All--",1 =>'Inhouse',2=>'Subcontract');
                                echo create_drop_down( "cbo_type",120, $report_type,"",0, "", "","",0 );
                                ?>
                            </td>

                            
                            <td id="buyer_name_td">
                            	<?
                            	echo create_drop_down( "cbo_buyer_name", 110,$blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            	?>
                            </td>
                           <td align="center">
                            	<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write" >
                            </td>
                            <td align="center">
                            	<input type="text"  name="txt_booking_number" id="txt_booking_number" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="bookingnumber();">
                            	<input type="hidden" name="txt_booking_id" id="txt_booking_id">
                            </td>
                            <td align="center">
                            	<input type="text"  name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Browse/Write" onDblClick="batchnumber();"  >
                            	<input type="hidden" name="txt_batch_id" id="txt_batch_id">
                            	<input type="hidden" name="batch_extension" id="batch_extension">
                            </td>
                            <td align="center">
								<? 
                                echo create_drop_down( "cbo_process_type",90, $conversion_cost_head_array,"",1, "--All--", "","",0,"33,94,63,171,65,156,179,200,170,209,231" );
                            	?>
                            </td>
                            <td align="center">
                            	<input type="text"  name="txt_batch_color" id="txt_batch_color" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write" >
                            </td>
                            
                            <td id="floor_td" align="center">
                            	<?
                            	echo create_drop_down( "cbo_floor_id", 110, $blank_array,"", 1, "-- Select Floor --", 0, "" );
                            	?>
                            </td>
                            <td id="machine_td" align="center">
								<?
									echo create_drop_down("cbo_machine_name", 135, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); 
                                ?>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            	&nbsp;To&nbsp;
                            	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                            </td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table>
                	<tr>
                		<td colspan="13">
                			<? echo load_month_buttons(1); ?>
                		</td>
                	</tr>
                </table>
                <br />
            </fieldset>
            <div id="report_container" align="center"></div>
            <div id="report_container2" align="center"></div>
        </div>
    </form>

</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_process_type','0','0','0','0');
</script>
</html>