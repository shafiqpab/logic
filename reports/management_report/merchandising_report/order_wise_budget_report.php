<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise Budget Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	7-06-2014
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

echo load_html_head_contents("Order Wise Budget Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
			
	function fn_report_generated(type)
	{
		var job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('txt_season').value;
		var file_no=document.getElementById('txt_file_no').value;
		var internal_ref=document.getElementById('txt_internal_ref').value;
		var budget_version=document.getElementById('cbo_budget_version').value;
		var search_date=document.getElementById('cbo_search_date').value;
		var costing_per=document.getElementById('cbo_costing_per').value;
		
		if((search_date==4 && type!=10) || (search_date!=4 && type==10)){
			alert("Search by Country ship date only for Budget 4 button");
			return;
		}

		if((costing_per!=0 && type!=11) || (costing_per==0 && type==11)){
			alert("Costing Per only for Budget 5 button");
			return;
		}
		
		
		if (type!=1)
		{
			if(job_no!="" || order_no!="" || season!="" || file_no!="" || internal_ref!="")
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
		
		
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_year*cbo_order_status*cbo_search_date*txt_season*txt_season_id*txt_file_no*txt_internal_ref*cbo_costing_per*cbo_team_name*cbo_team_member',"../../../");
		freeze_window(3);
		if(type==1 || type==2 || type==3 || type==4 || type==7 || type==8 || type==9 || type==10 || type==11 || type==13)
		{
			if(budget_version==2 || budget_version==3) //Budget V2
			{
				http.open("POST","requires/order_wise_budget_report_controller2.php",true);
			}
			else
			{
				http.open("POST","requires/order_wise_budget_report_controller.php",true);
			}
		}
		else if (type==5 || type==6  || type==12)
		{
			if(budget_version==2 || budget_version==3)//Budget V2
			{
				http.open("POST","requires/order_wise_budget_report2_controller2.php",true);
			}
			else
			{
				http.open("POST","requires/order_wise_budget_report2_controller.php",true);
			}
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated2(type)
	{
		var job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('txt_season').value;
		var file_no=document.getElementById('txt_file_no').value;
		var cbo_ready_to=document.getElementById('cbo_ready_to').value;
		//alert(cbo_ready_to);
		var internal_ref=document.getElementById('txt_internal_ref').value;
		
		if (type!=1)
		{
			if(job_no!="" || order_no!="" || season!="" || file_no!="" || internal_ref!="")
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
		
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_year*cbo_order_status*cbo_search_date*txt_season*txt_season_id*txt_file_no*txt_internal_ref*cbo_ready_to*cbo_team_name*cbo_team_member',"../../../");
		freeze_window(3);
		if(type==1 || type==2 || type==3 || type==4 || type==8)
		{
			http.open("POST","requires/order_wise_budget_report_controller.php",true);
		}
		else if (type==5 || type==6)
		{
			http.open("POST","requires/order_wise_budget_report2_controller.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			//alert(reponse[0]);
			//var tot_rows=reponse[0];
			
			//alert(reponse[2]);
			
			if(reponse[2]==1 || reponse[2]==5 || reponse[2]==4 || reponse[2]==9)
			{
				$('#report_container2').html(reponse[0]); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			else
			{
				$('#report_container2').html(reponse[0]); 
				
				//document.getElementById('report_container2').innerHTML=reponse[0];
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			
			if(reponse[2]==3)
			{
				var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_country_ship_qty","value_order_qty","value_gross_fob_val","value_foreign_cost","value_local_cost","value_net_fob_value","value_cost_of_material_service","value_yarn_dyeing_cost","value_purchase_cost","value_conver_cost","value_knit_cost","value_fabric_dyeing_cost","value_yarn_dyed_cost","value_all_over_cost","value_heat_setting_cost","value_fabric_finish","value_washing_cost","value_trim_cost","value_embell_cost","value_print_cost","value_embroidery_cost","value_special_cost","value_other_direct_expenses","value_contribution_value","value_cm_cost","value_gross_profit","value_commercial_cost","value_operating_exp","value_operating_profit","value_depreciation_amortization","value_interet","value_incomeTax","value_netProfit"],
					col: [11,12,15,16,17,19,20,22,24,25,27,29,30,31,32,33,34,36,37,38,39,40,41,42,44,45,47,48,49,51,52,53,54],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[2]==2)
			{
				
				var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_total_order_qnty","value_total_order_amount2","value_total_yarn_cost2","value_total_yarn_cost_per","value_total_purchase_cost","value_total_knitting_cost","value_total_yarn_dyeing_cost","value_total_fabric_dyeing_cost4","value_total_heat_setting_cost","value_total_finishing_cost","value_total_washing_cost","value_all_over_print_cost","value_total_trim_cost","value_total_print_amount","value_total_embroidery_amount","value_total_special_amount","value_total_wash_cost","value_total_other_amount","value_total_commercial_cost","value_total_foreign_amount","value_total_local_amount","value_total_test_cost_amount","value_total_freight_amount","value_total_inspection_amount","value_total_certificate_amount","value_total_common_oh_amount","value_total_currier_amount","value_total_deffd_amount","value_total_design_amount","value_total_studio_amount","value_total_interest_amount","value_total_income_amount","value_total_depr_amount","value_total_cm_amount","value_total_tot_cost","value_total_fabric_profit","value_total_profit_fab_percentage","value_total_expected_profit","value_total_expected_variance","value_tot_yarn_cons"],
					//col: [11,12,15,16,17,19,20,22,24,25,27,29,30,31,32,33,34,36,37,38,39,40,41,42,44,45,47,48,49,51,52,53,54],
					col: [13,15,18,19,20,22,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,52,53,54,55,56,57,59,60],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			
			//append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1);
			//alert(document.getElementById('graph_data').value);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		
		$("#table_body tr:first").show();
	}

	function openImgFile(job_no,action)
	{
	var page_link='requires/order_wise_budget_report_controller2.php?action='+action+'&job_no='+job_no;
	if(action=='img') var title='Image View'; else var title='File View';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var budget_version = $("#cbo_budget_version").val();
		
		//var cbo_month_id = $("#cbo_month").val();
		if(budget_version==1)
		{
			var page_link='requires/order_wise_budget_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		}
		else
		{
			var page_link='requires/order_wise_budget_report_controller2.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		}
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var budget_version = $("#cbo_budget_version").val();
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		if(budget_version==1)
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_budget_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_budget_report_controller2.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
		}
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var budget_version = $("#cbo_budget_version").val();
		var popup_width='900px';
		if(budget_version==1)
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
		}
	}
	
	function generate_precost_fab_purchase_detail(po_id,job_no,company_id,buyer_id,fabric_source,action)
	{  
		var budget_version = $("#cbo_budget_version").val();
		var popup_width='900px';
		if(budget_version==1)
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
		}
	}	
	
	function generate_pre_cost_knit_popup(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='700px';
		var budget_version = $("#cbo_budget_version").val();
		if(budget_version==1)
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
		}
		else
		{
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
		}
	}
	
	function generate_precost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,action)
	{  
		var popup_width='750px';
		var budget_version = $("#cbo_budget_version").val();
		//alert(budget_version);
		if(budget_version==1)
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
		else
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
	
	}
	
	function generate_precost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_washing_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
		
	function generate_precost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		var budget_version = $("#cbo_budget_version").val();
		if(budget_version==1)
		{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
	}
	
	function generate_precost_trim_cost_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	//Pre cost end
	
	function generate_pricecost_yarnavg_popup(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{ //alert(quotation_id); 
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricecost_purchase_popup(po_id,job_no,company_id,buyer_id,fabric_source,quotation_id,action)
	{ 
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pri_cost_knit_popup(po_id,job_no,company_id,buyer_id,cons_process,quotation_id,action)
	{  
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_trim_cost_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_embell_cost_detail(po_id,job_no,company_id,buyer_id,style,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style='+style+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_embell_cost_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{   //alert(quotation_id);
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function country_order_dtls(po_id,country_date,buyer_id,job_no,action)
	{  
		if (action=="country_trims_dtls_popup")
		{
			var popup_width='850px';
		}
		else
		{
			var popup_width='750px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?po_id='+po_id+'&country_date='+country_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function country_order_dtls_trim(po_id,country_id,buyer_id,job_no,action)
	{  
		 
		var popup_width='850px';
		//country_trims_dtls_popup
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller2.php?po_id='+po_id+'&country_id='+country_id+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function new_window1(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="yarn_summary";
			//scroll_div='scroll_body';
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
	}
	
	function precost_bom_pop(po_id,job_no,company_id,buyer_id,costing_date,action)
	{ 
		//alert(po_id);  
		/*var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');*/
		//	var data="&action=bomRpt"+
		var zero_val=1;
		var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
		

		if (r==true) zero_val=1; else zero_val=0;
		//var path='../../../';	
		var data="&action="+action+
					'&txt_po_breack_down_id='+"'"+po_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&zero_value='+zero_val+
					/* '&img_path='+path+ */
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_costing_date='+"'"+costing_date+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&path=../../../';;
					//alert(data);
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
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
		   }
		}
	}
	
		
	function precost_job_report(po_id,job_no,company_id,buyer_id,costing_date,type)
	{ 
		//alert(po_id);  
		/*var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');*/
	//	var data="&action=bomRpt"+
		var zero_val=1;
		var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
		

		if (r==true) zero_val=1; else zero_val=0;

		// var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&"
		// +get_submitted_data_string('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date*txt_po_breack_down_id*cbo_costing_per*print_option_id'
		// ,"../../");
		var data="action="+type+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+	
				'&txt_costing_date='+"'"+costing_date+"'"+			
				'&txt_job_no='+"'"+job_no+"'"+							
				'&path=../../../';
					//alert(data);
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
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
		   }
		}
	}
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Received Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
	}
	
	function openmypage_season()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var job_no = $("#txt_job_no").val();
		var page_link='requires/order_wise_budget_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
		var title='Season Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_season=this.contentDoc.getElementById("hide_season").value;
			var hide_season_id=this.contentDoc.getElementById("hide_season_id").value;
	
			$('#txt_season').val(hide_season);
			$('#txt_season_id').val(hide_season_id);
		}
	}
	//for print button
	function print_button_setting()
	{
		$('#data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/order_wise_budget_report_controller' ); 
	}
	 
	function print_report_button_setting(report_ids) 
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==23)
			{
				$('#data_panel').append( '<input type="button"  id="summary" class="formbutton" style="width:90px;" value="Summary"  name="summary"  onClick="fn_report_generated(1)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==24)
			{
				$('#data_panel').append( '<input type="button"  id="budget" class="formbutton" style="width:90px;" value="Budget"  name="budget"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==25)
			{
				$('#data_panel').append( '<input type="button"  id="budget2" class="formbutton" style="width:150px;" value="Country Ship Date Wise"  name="budget2"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==26)
			{
				$('#data_panel').append( '<input type="button"  id="quotebudget" class="formbutton" style="width:120px;" value="Mkt.Cost Vs Budget"  name="quotebudget"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==27)
			{
				$('#data_panel').append( '<input type="button"  id="budgetship" class="formbutton" style="width:120px;" value="Budget On Shipout"  name="budgetship"  onClick="fn_report_generated(5)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==29)
			{
				$('#data_panel').append( '<input type="button"  id="cDateBudShipout" class="formbutton" style="width:150px;" value="Bud. On Shipout(C.Date)"  name="cDateBudShipout"  onClick="fn_report_generated(6)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==182)
			{
				$('#data_panel').append( '<input type="button"  id="cDateBudShipout" class="formbutton" style="width:150px;" value="Budget Report 3"  name="cDateBudShipout"  onClick="fn_report_generated(7)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==285)
			{
				$('#data_panel').append( '<input type="button" id="quotebudget" class="formbutton" style="width:120px;" value="Spot Cost Vs Budget"  name="quotebudget"  onClick="fn_report_generated(8)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==309)
			{
				$('#data_panel').append( '<input type="button" id="quotebudgetwvn" class="formbutton" style="width:120px;" value="PQ Vs Budget Wvn"  name="quotebudget"  onClick="fn_report_generated(9)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==160)
			{
				$('#data_panel').append( '<input type="button" id="quotebudgetwvn" class="formbutton" style="width:120px;" value="Budget 4"  name="quotebudget"  onClick="fn_report_generated(10)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==580)
			{
				$('#data_panel').append( '<input type="button" id="budgetrpt5" class="formbutton" style="width:120px;" value="Budget 5"  name="budgetrpt5"  onClick="fn_report_generated(11)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==72)
			{
				$('#data_panel').append( '<input type="button"  id="budgetrpt6" class="formbutton" style="width:120px;" value="Budget 6"  name="budgetrpt6"  onClick="fn_report_generated(12)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==191)
			{
				$('#data_panel').append( '<input type="button"  id="budgetrpt7" class="formbutton" style="width:120px;" value="Budget 7"  name="budgetrpt7"  onClick="fn_report_generated(13)" />&nbsp;&nbsp;&nbsp;' );
			}


		}
	}
	
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_report2_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey();print_button_setting();">
<form id="budgetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1370px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1370px;" id="content_search_panel">
            <table class="rpt_table" width="1370" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
					<th>Team</th>
                    <th>Team Member</th>
                    <th>Year</th>
                    <th>Job No.</th>
                    <th>Costing Per</th>
                    <th>Order Status</th>
                    <th>File No</th>
                    <th>Internal Ref.</th>
                    <th>Order</th>
                    <th>Season</th>
                    <th>Search By</th>
                    <th width="100">Budget Version</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Shipment Date</th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_budget_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );print_button_setting();" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
						<td>
                             <?
                                    // echo create_drop_down( "cbo_team_name", 120, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_wise_budget_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );

							echo 	create_drop_down( "cbo_team_name", 120, "select id,team_leader_name from lib_marketing_team where   status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/order_wise_budget_report_controller', this.value, 'load_drop_down_team_member', 'team_td' );" );

							
                              ?>
                         </td>
                         <td id="team_td">
							 <? 
                                echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
                             ?>	
                         </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                             <input type="hidden" id="report_ids" name="report_ids"/>
                            
                        </td>
                        <td><? echo create_drop_down( "cbo_costing_per", 80, $unit_of_measurement,"", 1, "--Select--", 0, "","","1,2" );  ?></td>
                        <td>
							<? 
								$order_status=array(0=>"All",1=>"Confirmed",2=>"Projected"); 
								echo create_drop_down( "cbo_order_status", 80, $order_status,"", 0, "", 0, "" ); 
                            ?>
                        </td>
                         <td>
                            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px"  placeholder="Write File"  />                           
                        </td>
                         <td>
                            <input type="text" id="txt_internal_ref" name="txt_internal_ref" class="text_boxes" style="width:80px"  placeholder="Write Ref"  />                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order();" placeholder="Wr./Br. Order"  />
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                        <td align="center">
                        	<input type="text" name="txt_season" id="txt_season" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_season();" readonly/>
                            <input type="hidden" name="txt_season_id" id="txt_season_id" style="width:50px;"/>
                        </td>
                        <td width="" align="center">
							<?  
								$search_by = array(1=>'Shipment Date',2=>'Po Received Date',3=>'Po Insert Date',4=>'Country Ship Date');
								$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_search_date", 100, $search_by,"",0, "--Select--", $selected,$dd,0 );
                            ?>
                        </td>
                         <td>
							<?  
                                 $pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2',3=>'Pre Cost 3');
                                 echo create_drop_down( "cbo_budget_version", 100, $pre_cost_class_arr,"",0, "--Select--", 2,"",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" >
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date" >
                        </td>
                    </tr>
                    <tr align="center"  class="general">
                        <td colspan="16"><? echo load_month_buttons(1); ?></td>
                    </tr>
                    <tr>
                    	<td colspan="16" align="center" id="data_panel">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td colspan="16" align="center"><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('budgetReport_1','report_container*report_container2','','','')" /></td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>