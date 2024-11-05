<?php
/*********************************************** Comments *************************************
*	Purpose			: 	This Form Will Create Requisition  against demand status Report
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Aziz
*	Creation date 	: 	7-02-2016
*	Updated by 		: 	MD Didarul Alam	
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
************************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST); 
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Requisition  against demand status Report", "../../", 1, 1,'',1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	var tableFilters = {
			col_0: "none",col_21: "none",
			col_operation: {
			id: ["value_tot_prog","value_tot_demand","value_tot_requsition","value_tot_issue","value_tot_balance3","value_to_issue_return"],
			col: [14,15,16,17,18,19],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
	}//,"value_tot_balance"


	var tableFilters2 = {
			col_operation: {
			id: ["value_tot_prog","value_tot_requsition","value_tot_issue","value_tot_balance3","value_to_issue_return"],
			col: [18,19,20,21,22],
			operation: ["sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
	}//,"value_tot_balance"




			
	function fnc_generate_report(type)
	{
			var file_no=document.getElementById('txt_file_no').value;
			var ref_no=document.getElementById('txt_ref_no').value;
			var txt_prog_no=document.getElementById('txt_prog_no').value;
			var txt_req_no=document.getElementById('txt_req_no').value;
			var txt_requisition_date_from=document.getElementById('txt_requisition_date_from').value;
			var txt_requisition_date_to=document.getElementById('txt_requisition_date_to').value;
			var fabric_booking_no=document.getElementById('txt_fabric_booking_no').value;
			var txt_demand_no=document.getElementById('txt_demand_no').value;
			
			
			if(fabric_booking_no!="" || file_no!="" || ref_no!="" ||  txt_prog_no!="" || txt_req_no!="" || txt_demand_no!='')
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(txt_requisition_date_from=="" && txt_requisition_date_to=="")
				{
					if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
					{
						return;
					}
				}
				
			}
			
			if(type==1){var actions="report_generate";}
			else if(type==2){var actions="report_generate_requistion";}
			
			var data="action="+actions+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sales_order_no*txt_fabric_booking_no*txt_file_no*txt_ref_no*txt_prog_no*txt_req_no*txt_date_from*txt_date_to*txt_requisition_date_from*txt_requisition_date_to*txt_demand_no',"../../");
			//alert(data);return;
			freeze_window('3');
			http.open("POST","requires/requisition_against_demand_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			show_msg('3');
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert(tableFilters);
			if(response[2]==2){setFilterGrid("tbl_list_search",-1,tableFilters2);}
			else{setFilterGrid("tbl_list_search",-1,tableFilters);}
			
			release_freezing();
		}
	}

	function fnc_generate_report_xl()
	{
		var file_no=document.getElementById('txt_file_no').value;
		var ref_no=document.getElementById('txt_ref_no').value;
		var txt_prog_no=document.getElementById('txt_prog_no').value;
		var txt_req_no=document.getElementById('txt_req_no').value;
		var txt_requisition_date_from=document.getElementById('txt_requisition_date_from').value;
		var txt_requisition_date_to=document.getElementById('txt_requisition_date_to').value;
		var fabric_booking_no=document.getElementById('txt_fabric_booking_no').value;
		var txt_demand_no=document.getElementById('txt_demand_no').value;
		
		
		if(fabric_booking_no!="" || file_no!="" || ref_no!="" ||  txt_prog_no!="" || txt_req_no!="" || txt_demand_no!='')
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(txt_requisition_date_from=="" && txt_requisition_date_to=="")
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
			
		}
		
		var actions="report_generate_xl";
		
		var data="action="+actions+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_sales_order_no*txt_fabric_booking_no*txt_file_no*txt_ref_no*txt_prog_no*txt_req_no*txt_date_from*txt_date_to*txt_requisition_date_from*txt_requisition_date_to*txt_demand_no',"../../");
		//alert(data);return;
		freeze_window('3');
		http.open("POST","requires/requisition_against_demand_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse_xl;
		
	}

	function fn_report_generated_reponse_xl()
	{
		if(http.readyState == 4) 
			{  
				var reponse=trim(http.responseText).split("####");
				// alert(reponse[0]);
				if(reponse!='')
				{
					$('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
					document.getElementById('aa1').click();
				}
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
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}


	function openmy_popup(requ_id,prod_id,action,date_from,date_to,company_id)
	{
		popup_width='490px';
		

		var demand_no=document.getElementById('txt_hidden_demand_no_'+requ_id).value;
		//alert(demand_no);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/requisition_against_demand_status_report_controller.php?requ_id='+requ_id+'&prod_id='+prod_id+'&action='+action+'&date_from='+date_from+'&date_to='+date_to+'&company_id='+company_id+'&demand_no='+demand_no , 'Detail Veiw', 'width='+popup_width+', height=380px,center=1,resize=0,scrolling=0','../');
	}
</script>
</head>
 
<body onLoad="set_hotkey();">
	<form id="requsitionDemandnReport_1">
		<div style="width:100%;" align="center">    
			<? echo load_freeze_divs ("../../",'');  ?>
			<h3 style="width:1150px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel">      
			<fieldset style="width:1150px;">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					<thead>
						<th class="must_entry_caption">Company Name</th>
						<th>Buyer Name</th>
						<th>Order No.</th>
						<th>Fabric Booking No.</th>
						<th>File No</th>
						<th>Ref. No</th>
						<th>Program No</th>
						<th>Req. No</th>
						<th colspan="2">Requisition Date</th>
						<th>Demand No</th>
						<th colspan="2" class="must_entry_caption">Demand Date</th>
						<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requsitionDemandnReport_1','','','','')" class="formbutton" style="width:70px" /></th>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/requisition_against_demand_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?> 
								<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
							</td>
							<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?></td>

							<td><input type="text" name="txt_sales_order_no" id="txt_sales_order_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>

							<td><input type="text" name="txt_fabric_booking_no" id="txt_fabric_booking_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>
						
							<td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>
							<td>
						
							<input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Write" />  </td>
							<td>                                                 
								<input type="text" name="txt_prog_no" id="txt_prog_no" class="text_boxes" style="width:100px" placeholder="Write Prog." />
							</td>
							<td><input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>

							<td><input name="txt_requisition_date_from" id="txt_requisition_date_from" class="datepicker" style="width:70px"  placeholder="From Date" ></td>
							<td><input name="txt_requisition_date_to" id="txt_requisition_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
							<td><input type="text" name="txt_demand_no" id="txt_demand_no" class="text_boxes" style="width:100px;" placeholder="Write" /></td>
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" ></td>
							<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
							<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fnc_generate_report(1)" /></td>
							<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Only Excel" onClick="fnc_generate_report_xl()" /></td>
							<a href="" id="aa1"></a>
							
						</tr>
					</tbody>
				</table>
				<table>
					<tr>
						<td>
						<? echo load_month_buttons(1); ?>
						<input type="button" id="show_button" class="formbutton" style="width:80px" value="Requisition" onClick="fnc_generate_report(2)" />
						</td>
					</tr>
				</table> 
			</fieldset>
			</div>
		</div>
		<br>
		<div id="report_container" align="center"></div><br>
		<div id="report_container2" align="center"></div>  
    
    
 	</form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>