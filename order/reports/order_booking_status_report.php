<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Booking Status Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	06-11-2016
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
echo load_html_head_contents("Order Booking Status Report", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();

	function fn_report_generated(rept_type)
	{
		
		
		var cbo_search_type=$('#cbo_search_type').val();
		
		//alert(cbo_search_type);
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name,From Date*To date')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			if(cbo_search_type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==2)
			{
			var data="action=report_generate_merchand"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			
			else if(cbo_search_type==3)
			{
			var data="action=report_generate_bh_mer"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==4)
			{
			var data="action=report_generate_style"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==5)
			{
			var data="action=report_generate_job"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==6)
			{
			var data="action=report_generate_composition"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==7)
			{
			var data="action=report_generate_const"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==8)
			{
			var data="action=report_generate_count"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==9)
			{
			var data="action=report_generate_opd"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==10)
			{
			var data="action=report_generate_pcode"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==11)
			{
			var data="action=report_generate_tod"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			else if(cbo_search_type==12)
			{
			var data="action=report_generate_status"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			}
			
			
			freeze_window(3);
			http.open("POST","requires/order_booking_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			release_freezing();
			var tot_rows=reponse[2];
			//console.log(reponse[2]);
			var search_by=reponse[3];
			//console.log(reponse[3]);
			//$('#report_container2').html(reponse[0]);
			document.getElementById('report_container2').innerHTML=reponse[0];
			//console.log(4);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//value_td_week_qty_256
			if(search_by==1)
			{
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req","td_po_qty","td_po_val","td_po_smv"],
					   col: [11,16,21,23],
					   operation: ["sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body",-1,tableFilters);
			}
			if(search_by==2)  //Marchant
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_marchant",-1,tableFilters);
			}
			
			if(search_by==3)  //BH Marchant
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_bh",-1,tableFilters);
			}
			if(search_by==4) //style
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_style",-1,tableFilters);
			}
			if(search_by==5) //Job
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_job",-1,tableFilters);
			}
			if(search_by==6) //Comp
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_comp",-1,tableFilters);
			}
			if(search_by==7) //const
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","value_total_order_smv","value_total_order_exfc_qty","value_total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_const",-1,tableFilters);
			}
			if(search_by==8) //count
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_count",-1,tableFilters);
			}
			if(search_by==9) //OPD Week
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_opd",-1,tableFilters);
			}
			if(search_by==10) //Prod Code
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_pcode",-1,tableFilters);
			}
			if(search_by==11) //TOD Week
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_tod",-1,tableFilters);
			}
			if(search_by==12) //Status
			{
				//alert(search_by);
			 	var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["td_yarn_req_march","td_po_qty_march","td_po_val_march","total_order_smv","total_order_exfc_qty","total_order_exfc_val"],
					   col: [11,16,21,23,24,25],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					},
						col_1: "select",col_2: "select",col_3: "select",col_4: "select",col_5: "select",col_6: "select",col_7: "select",col_8: "select",col_9: "select",
						col_10: "select",col_11: "select",col_12: "select",col_13: "select",col_14: "select",col_15: "select",col_16: "select",col_17: "select",col_18: "select",
						col_20: "select",col_21: "select",col_22: "select",col_23: "select",col_24: "select",
						display_all_text: "- All -",	
				 }
				setFilterGrid("table_body_status",-1,tableFilters);
			}
			
			show_msg('3');
			//
		}
		
	}
	
	function fn_report_generated_bh_summary(rept_type)
	{
		
		
		var cbo_search_type=$('#cbo_search_type').val();
		
		
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name,From Date*To date')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			
			var data="action=report_generate_bh_summary"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*txt_date_from*txt_date_to*txt_job_no*txt_style_ref*txt_order_no*cbo_year_selection*cbo_search_type*txt_yarn_count*cbo_bh_mer_name',"../../");
			//alert(data);
			
			
			freeze_window(3);
			http.open("POST","requires/order_booking_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse_bh;
		}
	}
		
	
	function fn_report_generated_reponse_bh()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			
			const el = document.querySelector('#report_container2');
			if (el) {
				$('#report_container2').html(reponse[0]);
			}
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//value_td_week_qty_256
			 var tableFilters = 
				 {
					  
					col_operation: {
					   id: ["total_month_qty"],
					   col: [2],
					   operation: ["sum"],
					   write_method: ["innerHTML"]
					},
						col_1: "select",display_all_text: "- All -",
				 }
			setFilterGrid("table_body",-1,tableFilters);
			
			
			show_msg('3');
			release_freezing();
		}
		
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	/*function  check_th(table_body)
	{
		
		var tb_row=$('.fltrow').val();
		var row_num=$('.fltrow td').length-1;
	// alert(row_num);	
	}*/

	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		if(html_filter_print*1>1) $("#table_body tr:first").hide();
			$("#table_body_marchant tr:first").hide();
			$("#table_body_bh tr:first").hide();
			$("#table_body_job tr:first").hide();
			$("#table_body_style tr:first").hide();
			$("#table_body_comp tr:first").hide();
			$("#table_body_const tr:first").hide();
			$("#table_body_count tr:first").hide();
			$("#table_body_opd tr:first").hide();
			$("#table_body_pcode tr:first").hide();
			$("#table_body_tod tr:first").hide();
			$("#table_body_status tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		if(html_filter_print*1>1) $("#table_body tr:first").show();
		$("#table_body_marchant tr:first").show();
		$("#table_body_bh tr:first").show();
		$("#table_body_job tr:first").show();
		$("#table_body_style tr:first").show();
		$("#table_body_comp tr:first").show();
		$("#table_body_const tr:first").show();
		$("#table_body_count tr:first").show();
		$("#table_body_opd tr:first").show();
		$("#table_body_pcode tr:first").show();
		$("#table_body_tod tr:first").show();
		$("#table_body_status tr:first").show();
	}	

	
	
	function booking_date_popup(po_id,color_id)
	{
		//alert(country_id);
		var popup_width='320px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_booking_status_report_controller.php?action=booking_date_view&po_id='+po_id+'&color_id='+color_id, 'Details Veiw', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../');
	}
	
	
	
	
	function fn_week_wise_detail_popup(job_no,po_id,group_by_id,status_id,from_date,to_date,weeks_id,type,action)
	{ 	//alert(po_id)
		var companyID = $("#cbo_company_name").val();
		var popup_width='1350px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_booking_status_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&job_no='+job_no+'&group_by_id='+group_by_id+'&status_id='+status_id+'&from_date='+from_date+'&to_date='+to_date+'&weeks_id='+weeks_id+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
/*function subcode_journalcheck(row) 
{
	//alert(row);	
	if ($('#chk_subcode_journal_show_'+row).is(":checked"))
	{
		//$('#chk_ac_code_show_'+row).val(1);
		$('.subcode_journal_content_'+row).show("fast");

	}
	else 
	{ 
		//$('#chk_ac_code_show_'+row).val(0);
		$('.subcode_journal_content_'+row).hide("fast");
	}

}  
  */ 
</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style> 
</head>

<body onLoad="set_hotkey();">
<?php 
$first_day_this_month = date('01-m-Y'); // hard-coded '01' for first day
$last_day_this_month  = date("t-m-Y", time() +1296000*10); //date('t-m-Y');
?>
<form id="OBS_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1340px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1340px;">
                <table class="rpt_table" width="1340" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>BH Mer.</th>
                            <th>Team</th>
                            <th>Team Member</th>
                            <th>Job No</th>
                            <th>Style Ref.</th>
                            <th>Order No</th>
                            <th>Count Name</th>
                            <th>Group By</th>
                            
                            <th align="center" class="must_entry_caption">Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:40px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
								echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_booking_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td>
                             <?
                                    echo create_drop_down( "cbo_bh_mer_name", 100, "select distinct bh_merchant as id,bh_merchant from wo_po_details_master  where status_active =1 and is_deleted=0 and bh_merchant is not null order by bh_merchant","id,bh_merchant", 1, "-- Select BH Mer --", $selected, "" );
                              ?>
                         </td>
                         <td>
                             <?
                                    echo create_drop_down( "cbo_team_name", 120, "select id,team_leader_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_booking_status_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                              ?>
                         </td>
                         <td id="team_td">
							 <? 
                                echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
                             ?>	
                         </td>
                        <td align="center">
                    	<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Job No" >
                        </td>
                        <td align="center">
                    	<input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Styel Ref" >
                        </td>
                        
                         <td align="center">
                    	<input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Order No" >
                        </td>
                        <td align="center">
                    	<input name="txt_yarn_count" id="txt_yarn_count" class="text_boxes" style="width:70px" placeholder="Yarn Count" >
                        </td>
                         <td>
							 <?  
							 //,3=>"Style Wise",4=>"Job Wise",5=>"Composition",6=>"Construction Wise",7=>"Count Wise",8=>"OPD Week Wise",9=>"Prod. Dept",10=>"TOD Week",11=>"Status"
							 $search_type_arr=array(1=>"Order Wise",2=>"Merchandiser Wise",3=>"Buying House BH Mer",4=>"Style Wise",5=>"Job Wise",6=>"Composition Wise",7=>"Construction Wise",8=>"Count Wise",9=>"OPD Week Wise",10=>"Prod. Dept",11=>"TOD Week",12=>"Status");
                                echo create_drop_down( "cbo_search_type", 100, $search_type_arr,"", 0, "", $selected, "" );
                             ?>	
                         </td>
                    
                       
                    			
                  <td>
                  <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" value="<?php echo $first_day_this_month;?>">&nbsp; To
                   <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" value="<?php echo $last_day_this_month;?>"></td>
                    </td> 
                       
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:40px" value="Show" onClick="fn_report_generated(1)" /> <input type="button" id="show_button" class="formbutton" style="width:50px" value="BH Mon" onClick="fn_report_generated_bh_summary(2)" />
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
 <br>
  <input type="button" id="myBtn" value="OPen" style="display:none"/>
    <div id="myModal" class="modal">
  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2 id="td_title"></h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>
   
  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
<script>
//============modal=========
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata_po(data,title_name){
	
	document.getElementById('ccc').innerHTML=data;
	document.getElementById('td_title').innerHTML=title_name;
	document.getElementById('myBtn').click();
}
</script>  
</body>
<script>
	//set_multiselect('cbo_item_group','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
