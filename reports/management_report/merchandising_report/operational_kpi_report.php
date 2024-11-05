<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Operational KPI Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	10-7-2023
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
echo load_html_head_contents("KPI Report","../../../", 1, 1, $unicode,1,'');
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(type)
	{
		if( form_validation('cbo_company_id*cbo_from_year','Company*Fiscal Year')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var cbo_company_id=$("#cbo_company_id" ).val();
		var templete_id=$("#cbo_templete_id" ).val();
	//	alert(report_type);
	if(templete_id==1) //Company Wise
	{
		if(type==1)
		{ 
		var data="action=report_generate_sheet_kal"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(type==2)
		{ 
		var data="action=report_generate_sheet_jm"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(type==3) //Ratanput KAL RMG
		{ 
		var data="action=report_generate_ratanpur_kal_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(type==4)
		{ 
		var data="action=report_generate_ashulia_kal_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
	}
	else if(templete_id==2) //Unit Wise
	{
		if(type==1)
		{ 
			var data="action=report_generate_sheet_kal"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(type==2)
		{ 
			var data="action=report_generate_sheet_jm"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(type==3) //Ratanput KAL RMG
		{ 
			var data="action=report_generate_ratanpur_kal_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}
		else if(type==4)
		{ 
			var data="action=report_generate_ashulia_kal_rmg"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;       
		}  
	}

	else if(templete_id==3) //MKT TL Wise
	{
		var data="action=report_generate_mkt_tl_wise"+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&type='+type;    
	}
		 
		
		freeze_window(3);//operational_kpi_report_unit_controller
		if(templete_id==2) //Unit Wise
		{
			http.open("POST","requires/operational_kpi_report_unit_controller.php",true);
		}
		else if(templete_id==1)
		{
			http.open("POST","requires/operational_kpi_report_controller.php",true);
		}
		else if(templete_id==3)
		{
			http.open("POST","requires/operational_kpi_report_controller.php",true);
		}
		
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

	function report_generate_by_unit(unit_str,typeId)
	{		
		var unitStr=unit_str.split("_");
		 
		var company_id=unitStr[0];
		var cbo_templete_id=$("#cbo_templete_id").val();
		var report_type=unitStr[1];

		
		if(report_type==1)
		{
		 var report_title="Tejgaon Yarn Date Wise KPI";
		var data="action=report_generate_by_year_date_wise"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;  
		}
		else if((report_type==3 || report_type==4) && company_id==2) //---Kal--Ashulia
		{
			var report_title="Unit Date Wise KPI";
		var data="action=report_generate_by_year_ashulia_kal"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		else if((report_type==3) && company_id==1) //---Kal--Nayapara Cut Sewing
		{
			var report_title="Unit Nayapara Cut & Sew KPI";
		var data="action=report_generate_by_year_nayapara_sew_kal"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		else if((report_type==2) && (company_id==2 || company_id==1)) //---Kal- Shafipur//Jm Nayapara Fabric
		{
			if(company_id==2)
			{
				var report_title="Unit Shafipur KPI";
			}
			else{
				var report_title="Unit Nayapara Fabric KPI";
			}
			
		var data="action=report_generate_by_year_shafipur_kal"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		else if(report_type==5) //---Kal- marchandising//Jm marchandising
		{
			 
		var report_title="Unit Marchandising KPI";
		var data="action=report_generate_by_year_marchandising"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		 
		freeze_window(3);
		http.open("POST","requires/operational_kpi_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_by_year_reponse;  
		$("#report_container4").html(''); 
		$("#report_container3").html(''); 
	}

	function report_generate_by_unit_location(unit_str,typeId)
	{		
		var unitStr=unit_str.split("_");
		 
		var company_id=unitStr[0];
		var cbo_templete_id=$("#cbo_templete_id").val();
		var report_type=unitStr[1];
		var location_id=unitStr[3];

		
		if(report_type==1)
		{
		 	var report_title="Tejgaon Yarn Date Wise KPI";
			var data="action=report_generate_by_year_date_wise_for_location"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&location_id='+location_id+'&report_type='+report_type;  
		}
		else if((report_type==3 || report_type==4) && company_id==2) //---Kal--Ashulia
		{
			var report_title="Unit Date Wise KPI";
		var data="action=report_generate_by_year_ashulia_kal"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		else if((report_type==3) && company_id==1) //---Kal--Nayapara Cut Sewing
		{
			var report_title="Unit Nayapara Cut & Sew KPI";
			var data="action=report_generate_by_year_nayapara_sew_kal"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		else if((report_type==2) && (company_id==2 || company_id==1)) //---Kal- Shafipur//Jm Nayapara Fabric
		{
			if(company_id==2)
			{
				var report_title="Unit Shafipur KPI";
			}
			else{
				var report_title="Unit Nayapara Fabric KPI";
			}
			
		var data="action=report_generate_by_year_shafipur_kal"+get_submitted_data_string('cbo_from_year*cbo_templete_id',"../../../")+'&report_title='+report_title+'&report_type='+report_type+'&company_id='+company_id+'&report_type='+report_type;
		}
		 
		freeze_window(3);
		http.open("POST","requires/operational_kpi_report_unit_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_by_year_reponse;  
		$("#report_container4").html(''); 
		$("#report_container3").html(''); 
	}

	function report_generate_by_mkt_tl_popup(unit_str,typeId)
	{		
		var unitStr=unit_str.split("_");
		 
		var company_id=unitStr[0];
		var team_id=unitStr[1];
		var member_id=unitStr[2];
		var templete_id=$("#cbo_templete_id").val();
		//var report_type=unitStr[1];
		
		 if(templete_id==3) //---Kal- marchandising//Jm marchandising//TL 
		{
			 
		var report_title="Unit Team Member  KPI";
		var data="action=report_generate_by_year_team_member_mkt"+get_submitted_data_string('cbo_from_year*cbo_templete_id*cbo_company_id',"../../../")+'&report_title='+report_title+'&typeId='+typeId+'&company_id='+company_id+'&team_id='+team_id+'&member_id='+member_id;
		}
		 
		freeze_window(3);
		http.open("POST","requires/operational_kpi_report_controller.php",true);
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
		http.open("POST","requires/operational_kpi_report_controller.php",true);
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/operational_kpi_report_controller.php?string_data='+string_data+'&type='+type+'&action='+action, 'Details View', 'width='+popup_width+', height=490px,center=1,resize=0,scrolling=0','../../');
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
		                	<th width="150" rowspan="2" class="must_entry_caption">Company</th>
							<th width="150" rowspan="2" class="must_entry_caption">Report Template</th>
		                	<th width="200" class="must_entry_caption">Fiscal Year</th>
		                	 
		                    <th width="100" rowspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
		                </tr>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
	                        	echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- All --", $selected, "" );
	                        ?>                                     
	                    </td>
						<td>
							<?
	                        	$report_temp_arr=array(1=>"Company Wise",2=>"Unit Wise",3=>"MKT TL Wise");
								echo create_drop_down( "cbo_templete_id", 172, $report_temp_arr,"", 0, "-- Select Templete --", $selected, "" );

								 
	                        ?>                                     
	                    </td>
	                    <td>
	                       <select name="cbo_from_year" class="combo_boxes" id="cbo_from_year" style="width: 100px">
								<option value="0">From Year</option>
								<?php
								$dates = range(date('Y',strtotime("-4 year")), date('Y',strtotime("5 year")));
								$k=1;
								foreach($dates as $date){

								    if (date('m', strtotime($date)) <= 6) {//Upto June
								        $year = ($date-1) . '-' . $date;
								    } else {//After June
								        $year = $date . '-' . ($date + 1);
								    }
									 
									if($k==4)
									{
										echo "<option value='$year' selected>$year</option>";
									}
									else
									{
										echo "<option value='$year'>$year</option>";
									}

								    
									$k++;
								}
								?>
							</select> 
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
 

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>