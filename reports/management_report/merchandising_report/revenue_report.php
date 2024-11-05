<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Revenue Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	03-10-2019
Updated by 		: 	Aziz	
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
echo load_html_head_contents("Shipment pending Report","../../../", 1, 1, $unicode,1,'');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(type)
	{
		if( form_validation('cbo_company_id*cbo_from_year*cbo_to_year','Company Name')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var report_type=$("#cbo_company_id" ).val();
	//	alert(report_type);
		if(report_type==1)
		{ 
		var data="action=report_generate_sheet_kal"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_to_year*exchange_rate',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(report_type==2)
		{ 
		var data="action=report_generate_sheet_jm"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_to_year*exchange_rate',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(report_type==3) //Ratanput KAL RMG
		{ 
		var data="action=report_generate_ratanpur_kal_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_to_year*exchange_rate',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(report_type==4)
		{ 
		var data="action=report_generate_ashulia_kal_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_to_year*exchange_rate',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(report_type==5)
		{ 
		var data="action=report_generate_jm_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_to_year*exchange_rate',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		
		freeze_window(3);
		http.open("POST","requires/revenue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;   
			$("#report_container4").html(''); 
			$("#report_container3").html(''); 
			$("#report_container2").html(''); 
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
		
	}

	function report_generate_by_year(year,typeId)
	{		
		var type = 2;
		var report_type=$("#cbo_company_id").val();
		var report_title="Monthly Revenue Report";
		if(report_type==1)
		{
		var data="action=report_generate_by_year_sheet_kal"+get_submitted_data_string('cbo_company_id*exchange_rate',"../../../")+'&report_title='+report_title+'&year='+year;
		}
		else if(report_type==2)//JM
		{
		var data="action=report_generate_by_year_sheet_jm"+get_submitted_data_string('cbo_company_id*exchange_rate',"../../../")+'&report_title='+report_title+'&year='+year;
		}
		else if(report_type==3)//Kal Ratanpur
		{
		var data="action=report_generate_by_year_ratanpur_kal_rmg"+get_submitted_data_string('cbo_company_id*exchange_rate',"../../../")+'&report_title='+report_title+'&year='+year;
		}
		else if(report_type==4)//Ashulia KAL RMG
		{
		var data="action=report_generate_by_year_ashulia_kal_rmg"+get_submitted_data_string('cbo_company_id*exchange_rate',"../../../")+'&report_title='+report_title+'&year='+year;
		}
		else if(report_type==5)//Ashulia KAL RMG
		{
		var data="action=report_generate_by_year_jm_rmg"+get_submitted_data_string('cbo_company_id*exchange_rate',"../../../")+'&report_title='+report_title+'&year='+year;
		}
		freeze_window(3);
		http.open("POST","requires/revenue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_by_year_reponse;  
		$("#report_container4").html(''); 
		$("#report_container3").html(''); 
	}

	function generate_report_by_year_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container3").html(reponse[0]);
			$("#report_container").html('');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
		
	}

	function report_generate_by_month(month_year,typeId)
	{	
		var company_name = $("#cbo_company_id").val();
		var exchange_rate = $("#exchange_rate").val();
		var report_title='Daily Revenue Report';
		//alert(typeId);
		if(typeId==1)
		{
		var data="action=report_generate_by_month_sheet_kal&report_title="+report_title+"&cbo_company_id="+company_name+"&month_year="+month_year;
		
		}
		else if(typeId==2)
		{
			var data="action=report_generate_by_month_sheet_jm&report_title="+report_title+"&cbo_company_id="+company_name+"&month_year="+month_year;
		}
		else if(typeId==3)
		{
			var data="action=report_generate_by_month_ratanpur_rmg&report_title="+report_title+"&cbo_company_id="+company_name+"&month_year="+month_year;
		}
		else if(typeId==4)
		{
			var data="action=report_generate_by_month_ashulia_rmg&report_title="+report_title+"&cbo_company_id="+company_name+"&month_year="+month_year;
		}
		else if(typeId==5)
		{
			var data="action=report_generate_by_month_jm_rmg&report_title="+report_title+"&cbo_company_id="+company_name+"&month_year="+month_year;
		}
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/revenue_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_by_month_reponse;  
		$("#report_container4").html('');
	}

	function generate_report_by_month_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container4").html(reponse[0]);
			$("#report_container").html('');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
		
	}

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		// $('#scroll_body tr:last').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('all_report_container').innerHTML+'</body</html>');
		d.close(); 
	
		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="380px";
		// $('#scroll_body tr:last').show();
	}
	
	function order_dtls_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_controller.php?action=work_progress_report_details&job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type, 'Work Progress Report Details', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=500px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	 
	function fnc_gmt_kal_popup(string_data,action,type,width_th)
	{
		//alert(string_data);
		var popup_width=width_th+'px';
		//country_trims_dtls_popup
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/revenue_report_controller.php?string_data='+string_data+'&type='+type+'&action='+action, 'Details View', 'width='+popup_width+', height=490px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	


</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    
    <h3 style="width:530px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    
    <div id="content_search_panel" > 
	    <form name="shipmentpending_1" id="shipmentpending_1" autocomplete="off" > 
	        <fieldset style="width:510px" >
	            <table class="rpt_table" width="510" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                	<tr>
		                	<th width="150" rowspan="2" class="must_entry_caption">Report Template</th>
		                	<th width="200" colspan="2" class="must_entry_caption">Fiscal Calender</th>
		                	<th style="display:none" width="60" rowspan="2">Ex. Rate</th>
		                    <th width="100" rowspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
		                </tr>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
	                        	$report_temp_arr=array(1=>"Top Sheet KAL",2=>"Top Sheet JM",3=>"Ratanpur KAL RMG",4=>"Ashulia KAL RMG",5=>"JM RMG");
								echo create_drop_down( "cbo_company_id", 172, $report_temp_arr,"", 0, "-- Select Templete --", $selected, "" );
	                        ?>                                     
	                    </td>
	                    <td>
	                       <select name="cbo_from_year" class="combo_boxes" id="cbo_from_year" style="width: 100px">
								<option value="0">From Year</option>
								<?php
								$dates = range(date('Y',strtotime("-4 year")), date('Y',strtotime("5 year")));
								foreach($dates as $date){

								    if (date('m', strtotime($date)) <= 6) {//Upto June
								        $year = ($date-1) . '-' . $date;
								    } else {//After June
								        $year = $date . '-' . ($date + 1);
								    }

								    echo "<option value='$year'>$year</option>";
								}
								?>
							</select> 
	                    </td>
	                    <td>
	                       <select name="cbo_to_year" class="combo_boxes" id="cbo_to_year" style="width: 100px">
								<option value="0">To Year</option>
								<?php
								$dates = range(date('Y',strtotime("-4 year")), date('Y',strtotime("5 year")));
								foreach($dates as $date){

								    if (date('m', strtotime($date)) <= 6) {//Upto June
								        $year = ($date-1) . '-' . $date;
								    } else {//After June
								        $year = $date . '-' . ($date + 1);
								    }

								    echo "<option value='$year'>$year</option>";
								}
								?>
							</select>
	                    </td>
						<td style="display:none">
	                       <input type="text" name="exchange_rate" id="exchange_rate" value="83" class="text_boxes" style="width: 60px">
	                    </td>
	                    <td>
	                    	<input type="button" name="show" id="formbutton1" onClick="generate_report(1);" class="formbutton" style="width:80px" value="Show" />
	                    </td>
	                </tr>
	            </table>
	        </fieldset>
	    </form>
	    </div>
	     
		<div id="report_container" align="center" style="padding: 10px"></div>
		<div id="all_report_container">		
		    <div id="report_container2"></div>      
		    <div id="report_container3"></div>      
		    <div id="report_container4"></div> 
		</div>     
    </div>
</body>

<script>set_multiselect('cbo_buyer_name','0','0','','0','0');</script>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>