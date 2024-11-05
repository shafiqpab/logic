<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise Budget Sweater Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	27-09-2018
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

echo load_html_head_contents("Order Wise Budget Sweater Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
		var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["total_order_qnty","total_order_amount2","total_yarn_cost2","total_yarn_cost_per","total_purchase_cost","total_trim_cost","total_print_amount","total_embroidery_amount","total_special_amount","total_wash_cost","total_other_amount","total_commercial_cost","total_foreign_amount","total_local_amount","total_test_cost_amount","total_freight_amount","total_inspection_amount","total_certificate_amount","total_common_oh_amount","total_currier_amount","total_cm_amount","total_tot_cost","total_fabric_profit","total_profit_fab_percentage","tot_yarn_cons"],
					//col: [11,12,15,16,17,19,20,22,24,25,27,29,30,31,32,33,34,36,37,38,39,40,41,42,44,45,47,48,49,51,52,53,54],
					col: [13,16,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,37,38,39,40,41],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			var tableFilters_style = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["total_order_qnty","total_order_amount2","total_yarn_cost2","total_yarn_cost_per","total_purchase_cost","total_trim_cost","total_print_amount","total_embroidery_amount","total_special_amount","total_wash_cost","total_other_amount","total_commercial_cost","total_foreign_amount","total_local_amount","total_test_cost_amount","total_freight_amount","total_inspection_amount","total_certificate_amount","total_common_oh_amount","total_currier_amount","total_cm_amount","total_tot_cost","total_fabric_profit","total_profit_fab_percentage","tot_yarn_cons"],
					//col: [11,12,15,16,17,19,20,22,24,25,27,29,30,31,32,33,34,36,37,38,39,40,41,42,44,45,47,48,49,51,52,53,54],
					col: [12,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,39,40],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
	function fn_report_generated(type)
	{
		var job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var season=document.getElementById('txt_season').value;
		var file_no=document.getElementById('txt_file_no').value;
		var internal_ref=document.getElementById('txt_internal_ref').value;
		
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
		
		
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_id*txt_order_no*cbo_year*cbo_order_status*cbo_search_date*txt_season*txt_season_id*txt_file_no*txt_internal_ref',"../../../");
		freeze_window(3);
		
		http.open("POST","requires/order_wise_budget_sweater_report_controller.php",true);
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
			//var tot_rows=reponse[0];
			
				$('#report_container2').html(reponse[0]); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(reponse[2]==1)
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				else
				{
					setFilterGrid("table_body",-1,tableFilters_style);
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
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_job_no").val();
		var txt_style_ref_id = $("#txt_job_id").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_wise_budget_sweater_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(job_no);
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
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_job_id').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_budget_sweater_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
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
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_fab_purchase_detail(po_id,job_no,company_id,buyer_id,fabric_source,action)
	{  
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}	
	
	function generate_pre_cost_knit_popup(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_washing_report(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
		
	function generate_precost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_precost_trim_cost_detail(po_id,job_no,company_id,buyer_id,style_ref,action)
	{  
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	//Pre cost end
	
	function generate_pricecost_yarnavg_popup(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{ //alert(quotation_id); 
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricecost_purchase_popup(po_id,job_no,company_id,buyer_id,fabric_source,quotation_id,action)
	{ 
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	
	
	
	function generate_pricost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_finishing_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_fab_all_over_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	

	
	function generate_precost_embell_cost_detail(po_id,job_no,company_id,buyer_id,style,action,embl_type)
	{  
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style='+style+'&action='+action+'&embl_type='+embl_type, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function generate_pricost_embell_cost_detail(po_id,job_no,company_id,buyer_id,quotation_id,action)
	{   //alert(quotation_id);
		var popup_width='750px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&quotation_id='+quotation_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?po_id='+po_id+'&country_date='+country_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function country_order_dtls_trim(po_id,country_id,buyer_id,job_no,action)
	{  
		 
		var popup_width='850px';
		//country_trims_dtls_popup
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?po_id='+po_id+'&country_id='+country_id+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
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
	
	function precost_bom_pop(po_id,job_no,company_id,buyer_id)
	{ 
		//alert(po_id);  
		/*var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_budget_sweater_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');*/
		var data="&action=bomRpt"+
					'&txt_po_breack_down_id='+"'"+po_id+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'";
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
		var page_link='requires/order_wise_budget_sweater_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
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
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/order_wise_budget_sweater_report_controller' ); 
	}
	 
	function print_report_button_setting(report_ids) 
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==222)
			{
				$('#data_panel').append( '<input type="button"  id="report1" class="formbutton" style="width:90px;" value="Show"  name="report1"  onClick="fn_report_generated(1)" />&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==223)
			{
				$('#data_panel').append( '<input type="button"  id="report2" class="formbutton" style="width:90px;" value="Style Wise"  name="report2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;&nbsp;' );
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
          <h3 align="left" id="accordion_h1" style="width:1150px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1000px;" id="content_search_panel">
            <table class="rpt_table" width="1000" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Job No.</th>
                    <th>Order Status</th>
                    <th>File No</th>
                    <th>Internal Ref.</th>
                    <th>Order</th>
                    <th>Season</th>
                    <th>Search By</th>
                    <th colspan="2" id="search_by_th_up" class="must_entry_caption">Shipment Date</th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
							<?
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_budget_sweater_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );print_button_setting();" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                             <input type="hidden" id="report_ids" name="report_ids"/>
                            
                        </td>
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
								$search_by = array(1=>'Shipment Date',2=>'Po Received Date',3=>'Po Insert Date');
								$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_search_date", 100, $search_by,"",0, "--Select--", $selected,$dd,0 );
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
                        <td colspan="12">
                        	<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                    <tr >
                    	<td colspan="12" align="right" id="data_panel">
                           
                       	</td>
                    </tr>
                    <tr >
                    	<td colspan="12" align="center">
                            <input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('budgetReport_1','report_container*report_container2','','','')" />
                       	</td>
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