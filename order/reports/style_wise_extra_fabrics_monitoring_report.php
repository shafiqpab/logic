<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for Style Wise Extra Fabrics Monitoring Report
Functionality	:	
JS Functions	:   
Created by		:	Md. Sakibul Islam
Creation date 	: 	19-09-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:	Rayhan	
QC Date			:	20-09-23 
Comments		:
*/ 

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Monitoring Report","../../", 1, 1, $unicode,1,1);
?>	
<script> 
	var permission = '<? echo $permission; ?>';	
		
	function fn_report_generated(type)
	{
		//alert(type);
		if($('#txt_job_no').val()!=''){
			var file="cbo_company_name";
			var message="Comapny";
		}
		else if($('#txt_style_ref').val()!=''){
			var file="cbo_company_name";
			var message="Comapny";
		}
		else if($('#txt_order_no').val()!=''){
			var file="cbo_company_name";
			var message="Comapny";
		}
		else{
			var file="cbo_company_name*txt_date_from*txt_date_to";
			var message="Comapny*From Date*To Date";
		}
			
		if (form_validation(file,message)==false)
		{
			return;
		}
		else
		{

			
			if(type == 1)
			{
				var report_title=$( "div.form_caption" ).html();	
				var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_ref*cbo_order_status*cbo_active_status*txt_order_no*cbo_date_type*txt_date_from*txt_date_to*cbo_team_leader*cbo_year*cbo_ship_status*cbo_fabric_nature*cbo_fabric_source*txt_job_id*txt_po_id',"../../")+'&report_title='+report_title+'&type='+type;
				
				//alert(data);
				freeze_window();
				http.open("POST","requires/style_wise_extra_fabrics_monitoring_report_controller.php",true);
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
			var response=trim(http.responseText).split("****");

			/*$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );		
			$('#report_container').html(response[0]);*/

			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);
			//alert(response[2]);
			if(response[2]==1)//show
			{
				var tableFilters = {
					col_operation: {
					id: ["td_po_quantity","td_plan_quantity","value_td_po_total","smv_tdshow","yarn_issue_td","td_gf_qnty","yet_to_issue","td_gp_qty","td_gp_to_qty","td_daying_qnty","td_ff_qnty","td_fabrics_avl_qnty","td_blance","td_tot_cumul_balance","td_trim_blance","td_cut_qnty","td_print_recv","td_emb_issue_qnty","td_emb_rec_qnty","td_sp_issue_qnty","td_sp_rec_qnty","td_wash_issue_qnty","td_wash_rec_qnty","td_sewing_in_qnty","td_sewing_out_qnty","td_ex_qnty","td_ship_bal_qnty","td_ship_bal_val","td_short_ship_qnty","td_short_ship_val","td_excess_ship_qnty","td_excess_ship_val"],
					
					col: [12,13,15,27,29,30,31,32,33,34,36,37,38,39,40,42,44,45,46,47,48,49,50,51,52,56,58,59,60,61,62,63],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}	
			}
			if(response[2]==5)//FSO
			{
				var tableFilters = {
					col_operation: {
					id: ["td_po_quantity","td_cut_qnty","td_print_sent","td_print_recv","td_emb_issue_qnty","td_emb_rec_qnty","td_sp_issue_qnty","td_sp_rec_qnty","td_sewing_in_qnty","td_sewing_out_qnty","td_sewing_finish_qnty","td_inspection_qnty","td_ex_qnty","td_short_ship_qnty","td_short_ship_val","td_excess_ship_qnty","td_excess_ship_val","td_ship_bal_qnty"], 
					col: [12,30,31,32,33,34,35,36,37,38,39,40,41,43,44,45,46,48], 
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}	
			}
				
			setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}
	function fn_report_generated_reponse_gmts_color()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");

			/*$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );		
			$('#report_container').html(response[0]);*/

			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			append_report_checkbox('table_header_1',1);	
			
		

				var tableFilters_2 = {
					col_operation: {
					id: ["yarn_allocation_td_1","yarn_issue_td_1","td_gf_qnty_1","td_gp_qty_1","td_gp_to_qty_1","td_cut_qnty_1","td_po_quantity_1","value_td_po_total_1","td_daying_qnty_1","td_ff_qnty_1","td_fabrics_avl_qnty_1","td_fabrics_blance_1","td_tot_cutting","td_print_sent_1","td_print_recv_1","td_emb_issue_qnty_1","td_emb_rec_qnty_1","td_sp_issue_qnty_1","td_sp_rec_qnty_1","td_sewing_in_qnty_1","td_sewing_out_qnty_1","td_sewing_finish_qnty_1","td_inspection_qnty_1","td_ex_qnty_1","td_ex_qnty_val_1","td_short_ship_qnty_1","td_short_ship_val_1","td_excess_ship_qnty_1","td_excess_ship_val_1","td_ship_bal_qnty_1"],
					col: [20,21,22,23,24,25,27,29,30,31,32,33,34,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52], 
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				
		
			setFilterGrid("table_body_2",-1,tableFilters_2);
			show_msg('3');
			release_freezing();
		}
	}
	
	function fn_report_generated_reponse_summary()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container').html( '<br><b>Convert To </b><a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#report_container').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window_summary()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );
			
			document.getElementById('report_container2').innerHTML=response[0];
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			setFilterGrid("table_body_1",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window_summary()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}
		
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
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
	
	
	function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='Ship Date';
		}
		else if(str==2)
		{			
			document.getElementById('search_date_td').innerHTML='Original Ship Date';
		}

		else if(str==4)
		{			
			document.getElementById('search_date_td').innerHTML='Booking Date';
		}


		else
		{
			document.getElementById('search_date_td').innerHTML='Insert Date';
		}
	}
	


		function openmypage_job(type)
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			
			var companyID = $("#cbo_company_name").val();
			var buyer_name = $("#cbo_buyer_name").val();
			var cbo_year_id = $("#cbo_year").val();
			
			var txt_job_no = $("#txt_job_no").val();
			var txt_style_ref = $("#txt_style_ref").val();
			var txt_order_no = $("#txt_order_no").val();
			//var cbo_month_id = $("#cbo_month").val();
			var page_link='requires/style_wise_extra_fabrics_monitoring_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&type='+type+'&txt_job_no='+txt_job_no+'&txt_style_ref='+txt_style_ref;
			var title='Job No Search';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var job_no=this.contentDoc.getElementById("hide_job_no").value;
				var job_id=this.contentDoc.getElementById("hide_job_id").value;
				if(type==1 || type==2)
				{
					$('#txt_job_no').val(job_no);
					$('#txt_job_id').val(job_id);
				}
				else if(type==2)
				{
					$('#txt_style_ref').val(job_no);
					$('#txt_job_id').val(job_id);
				}
				else
				{
					$('#txt_order_no').val(job_no);
					$('#txt_po_id').val(job_id);
				}	
				
				 
			}
		}
			
		function openmypage_image(page_link,title)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
			}
		}
		function changeDateType(data)
		{
			if(data==1)
			{
				 $("#cbo_date_type option[value='1']").show();
				$("#cbo_date_type option[value='2']").show();
				$("#cbo_date_type option[value='3']").show();
				$("#cbo_date_type option[value='4']").show();
				$("#cbo_date_type option[value='5']").hide();
				$("#cbo_date_type option[value='6']").hide();
			}
			else if(data==2)
			{
				$("#cbo_date_type option[value='1']").show();
				$("#cbo_date_type option[value='2']").show();
				$("#cbo_date_type option[value='3']").show();
				$("#cbo_date_type option[value='4']").show();
				$("#cbo_date_type option[value='5']").show();

				$("#cbo_date_type option[value='6']").hide();
			}
			else if(data==3)
			{
				$("#cbo_date_type option[value='1']").show();
				$("#cbo_date_type option[value='2']").show();
				$("#cbo_date_type option[value='3']").show();
				$("#cbo_date_type option[value='4']").show();
				$("#cbo_date_type option[value='6']").show();

				$("#cbo_date_type option[value='5']").hide();
			}
			else
			{
				$("#cbo_date_type option[value='1']").show();
				$("#cbo_date_type option[value='2']").show();
				$("#cbo_date_type option[value='3']").show();
				$("#cbo_date_type option[value='4']").show();
				$("#cbo_date_type option[value='5']").show();
				$("#cbo_date_type option[value='6']").show();
			}
		}
	
		function openPopup(param,title,action)
		{
			
			var page_link='requires/style_wise_extra_fabrics_monitoring_report_controller.php?action='+action+'&data='+param;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../../');
		}

	function print_button_setting(company)
	{
		$('#button_data_panel').html('');
		$('#button_data_panel2').html('');
		// alert(company);
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/style_wise_extra_fabrics_monitoring_report_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==108)
			{
				$('#button_data_panel')
					.append( '<td align="right"><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>&nbsp;&nbsp;&nbsp;' );
			}
					
		}
	}
	function generate_fabric_report(type,txt_booking_no,cbo_company_name,txt_order_no,cbo_fabric_natu,cbo_fabric_source,id_approved_id,txt_job_no){ 
		
		var report_title= "Main Fabric Booking New";
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&report_title='+report_title+
		'&path=../../'; 

		http.open("POST","../woven_order/requires/fabric_booking_urmi_controller.php",true);
		
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
				release_freezing();
		   }
		}
	} 
</script>
</head>

<body onLoad="set_hotkey();">
<form id="Order_monitoring_report">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../");  ?>
        <h3 style="width:1450px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1450px;">
                <table class="rpt_table" width="1450" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                        <th width="120" class="must_entry_caption">Company</th>
                      
                        <th width="100">Buyer</th>
                        <th width="100">Team Leader</th> 
                        <th width="50">Year</th>                  	
                        <th width="60">Job</th>                  	
                        <th width="60" id="search_text_td">Style Ref.</th>
                        <th width="60">Order</th> 
                       	
                        <th width="80">Order Status</th>
                         <th width="70">Fabric Nature</th> 
                        <th width="70">Fabric Source</th> 
                        
                        <th width="130">Ship Status</th> 
                        <th width="100">Active Status</th>
                        <th width="100">Date Category</th>                 	
                        <th width="150" id="search_date_td">Ship Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                    </thead>
                    <tr class="general">
                        <td> 
							<?
                           	 echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business=1 $company_cond order by company_name","id,company_name",0, "-- Select Company --", $selected, "");
                            ?>
                        </td>
                      
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="team_leader_td">
							<? echo create_drop_down( "cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "" );
                        ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --", '', "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<input type="text"  id="txt_job_no" class="text_boxes" style="width:60px"  onDblClick="openmypage_job(1);" placeholder="Wr./Br. Job" >
							<input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                        	<input type="text"  id="txt_style_ref" class="text_boxes" style="width:60px" onDblClick="openmypage_job(2);" placeholder="Wr./Br. Style">
                        </td>
                        <td>
                        	<input type="text"  id="txt_order_no" class="text_boxes" style="width:60px" onDblClick="openmypage_job(3);" placeholder="Wr./Br. Order">
							<input type="hidden" id="txt_po_id" name="txt_po_id"/>
                        </td>
                        
                        <td>
							<? echo create_drop_down( "cbo_order_status", 80, $order_status, "", 1, "----All----",0, "",0,"" ); ?>
                        </td>

                         <td>
							<? echo create_drop_down( "cbo_fabric_nature",70, $item_category, "", 1, "----All----",0, "",0,"2,3" ); ?>
                        </td>
                        <td>
							<? echo create_drop_down( "cbo_fabric_source", 70, $fabric_source, "", 1, "----All----",0, "",0,"" ); ?>
                        </td>
                       
                         <td>
							<? echo create_drop_down( "cbo_ship_status", 130, $shipment_status, "", 1, "----Select----",0, "",0,"" ); ?>
                        </td>

                        <td>
							<?
							$active_status=array(1=>"Active",2=>"In-Active",3=>"Cancel",4=>"All"); 
							 echo create_drop_down( "cbo_active_status", 80, $active_status, "", 1, "----Select----",1, "changeDateType(this.value);",0,"" ); ?>
                        </td>
                        
                       	<td>
							<? 
                            //4=>Extended Ship Date,5=>'In-Active Date',6=>'Cancel Date',7=>'Ref. Close Date' (Changed)
							$date_type_arr=array(1=>'Ship Date',2=>'Original Ship Date',3=>'PO Insert Date',4=>'Booking Date');
							echo create_drop_down( "cbo_date_type", 100, $date_type_arr, "", 1, "----Select----",1, "date_fill_change(this.value);",0,"" ); 
							?>
                        </td>
                       
                        <td>
                           <?
							$current_date = date("d-m-Y",strtotime(add_time(date("H:i:s",time()),0)));
							$previous_date = date('d-m-Y', strtotime('-4 day', strtotime($current_date))); 
						   ?>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" value="" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" value=""  ></td>

							<td align="right"><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /></td>                    
                    </tr>
                    <tr>
                        <td colspan="15" align="center"><? echo load_month_buttons(1); ?></td>
						<td  id="button_data_panel2" align="center"> </td>                       
                    </tr>
                </table> 
            <br /> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="center"></div>
</form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
set_multiselect('cbo_ship_status*cbo_company_name','0*0','0','','0*0');
//$("#cbo_active_status").val(1);
$("#multiselect_dropdown_table_headercbo_company_name").click(function(){
	var data=$("#cbo_company_name").val();
	load_drop_down( 'requires/style_wise_extra_fabrics_monitoring_report_controller',data, 'load_drop_down_buyer', 'buyer_td' );
	load_drop_down( 'requires/style_wise_extra_fabrics_monitoring_report_controller', data, 'load_drop_down_agent', 'agent_td' );

	$(function(){
		print_button_setting();
	});
	
});
</script>
</html>