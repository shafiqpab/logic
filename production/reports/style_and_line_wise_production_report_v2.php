<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Line wise Production Report V2
				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	04-07-2021
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
echo load_html_head_contents("Daily Line wise Production Report V2","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}	
var tableFilters2 = {}	
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var work_company=$("#cbo_working_company_id").val();
		var job=$("#txt_job_no").val();
		var int_ref=$("#txt_internal_ref").val();
		var date_from=$("#txt_date_from").val();
		var date_to=$("#txt_date_to").val();

		if(work_company==''){
			if( form_validation('cbo_working_company_id*txt_date_from*txt_date_to','Working Company*Date from*Date To')==false )
			{
				return;
			}
		}
		if(job=='' && date_from=='' && int_ref=='')
		{
			if(form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
			{
				return;
			}
		}
		

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_working_company_id*cbo_location_id*cbo_floor_id*cbo_line_id*cbo_lc_company_id*cbo_buyer_id*cbo_job_year*txt_job_no*txt_date_from*txt_date_to*txt_internal_ref',"../../")+'&report_title='+report_title+'&type='+type;
		
		// alert(data); return;
		
		freeze_window(3);
		http.open("POST","requires/style_and_line_wise_production_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);  
				
				release_freezing();
				//document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
				//document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(type==1)
				{
					// setFilterGrid("html_search",-1,tableFilters);
				}
				else
				{
					// setFilterGrid("table_body",-1,tableFilters2);
				}				
				
				show_msg('3');
				release_freezing();
			}
		}   
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}

	function type_wise_fnc(data)
	{
		if(data==1)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Job No");
			$("#txt_search_val").attr("placeholder","          Job No");
			
		}
		else if(data==2)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Style");
			$("#txt_search_val").attr("placeholder","            Style");

		}
		else if(data==3)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("PO");
			$("#txt_search_val").attr("placeholder","               PO");
		}
		else
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("");
			$("#txt_search_val").attr("placeholder","");
		}
	}
	
	function openProdPopup(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_v2_controller.php?action=open_prod_popup&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopup2(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_v2_controller.php?action=open_prod_popup2&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_v2_controller.php?action=open_prod_popup_wip&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP2(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_v2_controller.php?action=open_prod_popup_wip2&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}





	function getWorkingCompanyId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_location&data="+working_company_id;
		  //alert(data);die;
		  http.open("POST","requires/style_and_line_wise_production_report_v2_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
	              setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	              //========================
	              // load_drop_down( 'requires/style_and_line_wise_production_report_v2_controller', working_company_id, 'load_drop_down_buyer', 'buyer_td' );
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_floor&data="+working_company_id+'_'+location_id;
		  //alert(data);die;
		  http.open("POST","requires/style_and_line_wise_production_report_v2_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0'); 
				  setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
	          }			 
	      };
	    }         
	}

	function getFloorId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    var floor_id = document.getElementById('cbo_floor_id').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_line&data="+working_company_id+'_'+location_id+'_'+floor_id;
		  //alert(data);die;
		  http.open("POST","requires/style_and_line_wise_production_report_v2_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#line_td').html(response);
	              set_multiselect('cbo_line_id','0','0','','0');
	          }			 
	      };
	    }         
	}
	
	
	function open_job_no()
	{
		if( form_validation('cbo_working_company_id','Company Name')==false )
		{
			return;
		}
		// var company = $("#cbo_company_name").val();	
		
		var job_year = $("#cbo_job_year").val();		
	
		var page_link='requires/style_and_line_wise_production_report_v2_controller.php?action=job_popup&job_year='+job_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var year=this.contentDoc.getElementById("txt_year").value; // product Description
		//	alert(year);
			$("#txt_job_no").val(style_des);
		
			$("#txt_year").val(year); 
		}
	}
	
</script>

</head>
<body onLoad="set_hotkey();">
	<form id="StyleandLineWiseProductionReport_1">
		<div style="width:100%;" align="center">    
			<? echo load_freeze_divs ("../../",'');  ?>
			<h3 style="width:1350px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel" >      
				<fieldset style="width:1350px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th width="140">LC Company</th>
							<th width="140" class="must_entry_caption">Working Company</th>
							<th width="100">Location</th>
							<th width="100">Floor</th>
							<th width="100">Line No</th>                   
							<th width="120" class="">Buyer</th>
							<th width="100" class="">Job No</th>
							<th width="100" class="">IR/IB</th>
							<th width="80" class="">Job Year</th>    					         
							<th width="200" class="must_entry_caption">Production Date</th>
							<th width="130">
								<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
							</th>
						</thead>
						<tbody>
							<tr class="general">
								<td id="cbo_lc_company_td">
									<? 
										echo create_drop_down( "cbo_lc_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/style_and_line_wise_production_report_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
									?>                            
								</td>
								<td id="working_company_td">
									<? 
										echo create_drop_down( "cbo_working_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", 0, "load_drop_down( 'requires/style_and_line_wise_production_report_v2_controller', this.value, 'load_drop_down_location', 'location_td' );" );
									?>                            
								</td>
								<td id="location_td">
									<? 
										echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location --", "", "" );
									?>                            
								</td>                        
								<td id="floor_td">
									<? 
										echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "-- Select Floor --", "", "" );
									?>                            
								</td>                         
								<td id="line_td">
									<? 
										echo create_drop_down( "cbo_line_id", 100, $blank_array,"", "", "-- Select Line --", "", "" );
									?> 
								</td>
							
								<td id="buyer_td">
									<? 
										echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
									?>                            
								</td>  
								<td>
									<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px;" onDblClick="open_job_no()" placeholder="Browse/Write">
								</td>                      
								<td>
									<input type="text"  name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px;" />
								</td>                      
								<td>
									<? 
										$selected_year=date("Y");                               
										echo create_drop_down( "cbo_job_year", 80, $year,"", 1, "--Select Year--",$selected_year,'',0);
									?>                      
								</td>               
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
								</td>                        
								<td>
									<input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
									<input type="button" name="search2" id="search2" value="Show 2" onClick="generate_report(2)" style="width:60px" class="formbutton" />
								</td>
							</tr>
							<tr>
								<td colspan="12" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
		<div id="report_container" align="center" style="padding: 10px 0;"></div>
		<div id="report_container2" align="left">
			<div style="float:left; " id="report_container3"></div>
		</div>
	</form> 
</body>
<script>
	set_multiselect('cbo_working_company_id','0','0','0','0');	
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	set_multiselect('cbo_line_id','0','0','','0');

	setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company_id,'0');getWorkingCompanyId();") ,3000)]; 
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
