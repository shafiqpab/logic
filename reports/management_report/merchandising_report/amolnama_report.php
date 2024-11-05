<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Revenue Report2
				
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	18-06-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:	Code is poetry, I try to do that :)
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
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_order_status*cbo_from_year*cbo_to_year',"../../../")+'&report_title='+report_title+'&type='+type;
		
		freeze_window(3);
		http.open("POST","requires/amolnama_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
			$("#report_container6").html(''); 
			$("#report_container5").html(''); 
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

	function report_generate_by_year(year,client_id)
	{		
		var type = 2;
		var report_title="Monthly Revenue Report";
		var data="action=report_generate_by_year"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_order_status',"../../../")+'&report_title='+report_title+'&year='+year+'&client_id='+client_id;
		
		freeze_window(3);
		http.open("POST","requires/amolnama_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_by_year_reponse;  
		$("#report_container6").html(''); 
		$("#report_container5").html(''); 
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
			$('#monthly_revenue_report').css('display','none');
			$('#buyer_summary_report').css('display','none');
			show_msg('3');
			release_freezing();
		}
		
	}

	function report_generate_by_month_year(month_year,client_id)
	{	
		var company_name = $("#cbo_company_id").val();
		var report_title='Daily Revenue Report';
		var data="action=report_generate_by_month_year"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_order_status',"../../../")+'&report_title='+report_title+'&month_year='+month_year+'&client_id='+client_id;
		
		freeze_window(3);
		http.open("POST","requires/amolnama_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generate_by_month_year_reponse;
		$("#report_container6").html('');
		$("#report_container5").html('');
		$("#report_container4").html('');
	}

	function report_generate_by_month_year_reponse()
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

	function report_generate_by_buyer_month_year(buyer,month_year,client_id)
	{	
		var company_name = $("#cbo_company_id").val();
		var report_title='Daily Revenue Report';
		var data="action=report_generate_by_buyer_month_year"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_order_status',"../../../")+'&report_title='+report_title+'&month_year='+month_year+'&buyer='+buyer+'&client_id='+client_id;
		
		freeze_window(3);
		http.open("POST","requires/amolnama_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generate_by_buyer_month_year_reponse; 
		$("#report_container6").html('');
		$("#report_container5").html('');
	}

	function report_generate_by_buyer_month_year_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container5").html(reponse[0]);
			$("#report_container").html('');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
		
	}

	function report_generate_by_intref_wise(buyer_id,month_year,client_id)
	{	
		var company_name = $("#cbo_company_id").val();
		var report_title='Daily Revenue Report';
		var data="action=report_generate_by_intref_wise"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_order_status',"../../../")+'&report_title='+report_title+'&month_year='+month_year+'&buyer_id='+buyer_id+'&client_id='+client_id;
		
		freeze_window(3);
		http.open("POST","requires/amolnama_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = report_generate_by_intref_wise_reponse; 
		$("#report_container6").html('');
	}

	function report_generate_by_intref_wise_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container6").html(reponse[0]);
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
	


</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    
    <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    
    <div id="content_search_panel" > 
	    <form name="shipmentpending_1" id="shipmentpending_1" autocomplete="off" > 
	        <fieldset style="width:840px" >
	            <table class="rpt_table" width="840" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                	<tr>
		                	<th width="150" rowspan="2" class="must_entry_caption">Company Name</th>
		                	<th width="130" rowspan="2" class="must_entry_caption">Location Name</th>
		                	<th width="130" rowspan="2" class="must_entry_caption">Client/BSU</th>
		                	<th width="130" rowspan="2" class="must_entry_caption">Shipment Status</th>
		                	<th width="200" colspan="2" class="must_entry_caption">Fiscal Calender</th>
		                    <th width="100" rowspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>
		                </tr>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
	                        	echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/amolnama_report_controller',this.value,'load_drop_down_location', 'location_td' );load_drop_down( 'requires/amolnama_report_controller',this.value,'load_drop_down_buyer_client', 'client_td' );" );
	                        ?>                                     
	                    </td>
                        <td id="location_td"  align="center">
                            <?
                            echo create_drop_down("cbo_location_id", 130, $blank_array, "", 1, "-- Select Location --", $selected, "", "", "");
                            ?>
                        </td>

                        <td id="client_td">
                            <?
                            echo create_drop_down("cbo_client", 130, $blank_array, "", 1, "-- All -- ", $selected, "");
                            ?>  
                        </td>
                        <td id=""  align="center">
                            <?
                            $order_Status = array(1 =>'Open Order',2 =>'Close Order');
                            echo create_drop_down("cbo_order_status", 130, $order_Status, "", 0, "--Select Order Status--", 2, "", "", "");
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
		    <div id="report_container5"></div> 
		    <div id="report_container6"></div> 
		</div>     
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>