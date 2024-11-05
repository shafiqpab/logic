<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Partial Fabric booking Proceed for Advanced wise report
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	07-02-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:	Emdadul Haque Maruf
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Advance Fabric Booking Status report", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();

	function fn_report_generated(rept_type)
	{		
		var style_ref=$('#txt_style_ref').val();
		var job_no=$('#txt_job_no').val();
		var booking_no=$('#txt_booking_no').val();
		if(style_ref=='' && job_no =='' && booking_no ==''){
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name,From Date*To date')==false)
			{
				return;
			}
			else
			{	
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_factory*cbo_buyer_name*txt_style_ref*txt_job_no*txt_booking_no*cbo_date_for*txt_date_from*txt_date_to*cbo_fabric_category',"../../");			
				freeze_window(3);
				http.open("POST","requires/advance_fabric_booking_status_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
		}
		if(style_ref!='' || job_no !='' || booking_no !=''){
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
			else
			{	
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_factory*cbo_buyer_name*txt_style_ref*txt_job_no*txt_booking_no*cbo_date_for*txt_date_from*txt_date_to*cbo_fabric_category',"../../");			
				freeze_window(3);
				http.open("POST","requires/advance_fabric_booking_status_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
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
</script>

</head>

<body onLoad="set_hotkey();">
<?php 
$first_day_this_month = date('01-m-Y'); // hard-coded '01' for first day
$last_day_this_month  = date("t-m-Y", time() +1296000*10); //date('t-m-Y');
?>
<form id="afbs_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th class="must_entry_caption" width="120">Company</th>
                            <th width="120">Working Comp</th>
                            <th width="120">Buyer</th>
                            <th width="80">Style No.</th>
                            <th width="80">Job No</th>
                            <th width="80">Booking No.</th>
                            <th width="80">Date For</th>
                            <th width="160">Date Range</th>
                            <th width="80">Fabric Category</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/advance_fabric_booking_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td><?=create_drop_down( "cbo_working_factory", 140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Working Company", $selected, ""); ?>                        	
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td>
                         	<input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Styel Ref" >
                         </td>
                         <td>
                         	<input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Job No" >
                         </td>
                         <td>
                         	<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:70px" placeholder="Booking No" >
                         </td>
                         <td>
							 <? 
							 $date_for_arr=array(1=>"Ship Date",2=>"PO Rcv Date",3=>"Booking Date");
                                echo create_drop_down( "cbo_date_for", 100, $date_for_arr,"", 1, "Select", $selected, "" );
                             ?>	
                         </td>
                         <td>
	                  	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" value="">&nbsp; To
	                   <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" value=""></td>

                    	</td> 
                    	<td>
							 <? 
							 $fabric_category_arr=array(1=>"Grey",2=>"Finish");
                                echo create_drop_down( "cbo_fabric_category", 100, $fabric_category_arr,"", 1, "Select", $selected, "" );
                             ?>	
                         </td>
                       
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)"/>
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
