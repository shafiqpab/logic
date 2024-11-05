<? 
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Graph", "", "", 1, $unicode, $multi_select, '');
?>
<link rel="stylesheet" href="home_css/styles.css">

<script>
	var permission = '<? echo $permission; ?>';
	//alert (permission);
	var comp="";
	var locat="";
	var lnk="";
	
	function change_color(v_id,e_color)
	{
		var clss;
		$('td').click(function() {
			var myCol = $(this).index();
			clss='res'+myCol;
		
		});
		
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
			$('.'+clss).removeAttr('bgColor');
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
			$('.'+clss).attr('bgColor','#33CC00');
		}
	}
	
	function show_summary()
	{
		page_link='summary_popup.php?action=summary_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','');
	}
	
	function show_summary_val()
	{
		page_link='summary_popup.php?action=summary_popup_value';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','');
	}
	
	//show_graph( "settings_value", "data_value", "column", "chartdiv", "", "", 1, 400, 750 )
	
	
function show_data ( lnk, lid  )
{
	//alert(lnk+"="+lid);
	var comp=$('#cbo_company_home').val();
	var locat=$('#cbo_location_home').val();
	
	if( lid == 1 ) //Static Graph design
	{
		//alert(lnk);	
		if( lnk == 'VG9kYXlfSG91cmx5X1Byb2R1Y3Rpb24=')//VG9kYXlfSG91cmx5X1Byb2R1Y3Rpb24= //Today_Hourly_Production' )
		{
			window.open('today_production_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xbnR5')//b3JkZXJfaW5faGFuZF9xbnR5 //order_in_hand_qnty
		{ 
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWw')//b3JkZXJfaW5faGFuZF92YWw //order_in_hand_val
		{
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'c3RhY2tfcW50eQ==')//c3RhY2tfcW50eQ== //stack_qnty
		{
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'c3RhY2tfdmFsdWU=')//c3RhY2tfdmFsdWU= //stack_value
		{
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'Y29tcGFueV9rcGk=')//Y29tcGFueV9rcGk= //company_kpi
		{
			var data='';
			var page_link='forecast_popup.php?action=opendate_popup&data='+data;
			//var page_link='graph_grp.php?action=opendate_popup';
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=280px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("txt_date_from").value;
				if(theemail!='')
				{
					window.open('dash_board.php?m='+lnk+'&cp='+comp+'&date_data='+theemail, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
				//window.open("requires/knitting_bill_issue_controller.php?data=" + data+'&action='+action, true );
				//alert(theemail);
			}
			//window.open('dash_board.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'ZmFicmljX29yZGVyX2FuYWx5c2lz')//Fabric & order Analysis
		{ 
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=fabric_and_order_analysis_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=800px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var cbo_buyer_name	=this.contentDoc.getElementById("cbo_buyer_name").value;
				var cbo_constraction=this.contentDoc.getElementById("cbo_constraction").value;
				var cbo_composition	=this.contentDoc.getElementById("cbo_composition").value;
				var cbo_gsm			=this.contentDoc.getElementById("cbo_gsm").value;
				var cbo_start_month	=this.contentDoc.getElementById("cbo_start_month").value;
				var cbo_end_month	=this.contentDoc.getElementById("cbo_end_month").value;
				var cbo_year		=this.contentDoc.getElementById("cbo_year").value;
				var txt_previous_period=this.contentDoc.getElementById("txt_previous_period").value;
				var cbo_value		=this.contentDoc.getElementById("cbo_value").value;

				var data=comp+"_"+locat+"_"+cbo_buyer_name+"_"+cbo_constraction+"_"+cbo_composition+"_"+cbo_gsm+"_"+cbo_start_month+"_"+cbo_end_month+"_"+cbo_year+"_"+txt_previous_period+"_"+cbo_value;
				
				window.open('fabric_order_analysis_graph.php?m='+lnk+'&data='+data, "MY PAGE");
			}
			
		}
		else if( lnk == 'c2FsZXNfZm9yZWNhc3RfcW50eQ==') //c2FsZXNfZm9yZWNhc3RfdmFsdWU= // sales_forecast_qnty
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=forecast_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=700px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var start_date=this.contentDoc.getElementById("start_date");
				var end_date=this.contentDoc.getElementById("end_date");
				var team_leader=this.contentDoc.getElementById("team_leader");
				var agent=this.contentDoc.getElementById("agent");
				var buyer_name=this.contentDoc.getElementById("buyer_name");
				
				var split_start_date=start_date.value.split(',');
				var split_end_date=end_date.value.split(',');
				var split_team_leader=team_leader.value.split(',');
				var split_agent=agent.value.split(',');
				var split_buyer_name=buyer_name.value.split(',');
				var cur="";
				for(var i=0; i<split_start_date.length; i++)
				{  
					if(i==0)
					{ 
						var cur=split_start_date[i]+'*'+split_end_date[i]+'*'+split_team_leader[i]+'*'+split_agent[i]+'*'+split_buyer_name[i];
					}
					else
					{
						var cur=cur+"**"+split_start_date[i]+'*'+split_end_date[i]+'*'+split_team_leader[i]+'*'+split_agent[i]+'*'+split_buyer_name[i];
					}
				}
				window.open('order_forecast_graph.php?m='+lnk+'&cp='+comp+"__"+locat+'&ddate='+cur, "MY PAGE");
			}
			
		}
		else if( lnk == 'c2FsZXNfZm9yZWNhc3RfdmFsdWU=') //c2FsZXNfZm9yZWNhc3RfdmFsdWU= // sales_forecast_value
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=forecast_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=700px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var start_date=this.contentDoc.getElementById("start_date");
				var end_date=this.contentDoc.getElementById("end_date");
				var team_leader=this.contentDoc.getElementById("team_leader");
				var agent=this.contentDoc.getElementById("agent");
				var buyer_name=this.contentDoc.getElementById("buyer_name");
				
				var split_start_date=start_date.value.split(',');
				var split_end_date=end_date.value.split(',');
				var split_team_leader=team_leader.value.split(',');
				var split_agent=agent.value.split(',');
				var split_buyer_name=buyer_name.value.split(',');
				var cur="";
				for(var i=0; i<split_start_date.length; i++)
				{  
					if(i==0)
					{ 
						var cur=split_start_date[i]+'*'+split_end_date[i]+'*'+split_team_leader[i]+'*'+split_agent[i]+'*'+split_buyer_name[i];
					}
					else
					{
						var cur=cur+"**"+split_start_date[i]+'*'+split_end_date[i]+'*'+split_team_leader[i]+'*'+split_agent[i]+'*'+split_buyer_name[i];
					}
				}
				window.open('order_forecast_graph.php?m='+lnk+'&cp='+comp+"__"+locat+'&ddate='+cur, "MY PAGE");
			}
		}
		else if( lnk == 'ZGFpbHlfZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg==') //daily_finishing_capacity_achievment_iron
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=daily_finishing_capacity_achievment_iron_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var txt_date_from	=this.contentDoc.getElementById("txt_date_from").value;
				var txt_date_to	=this.contentDoc.getElementById("txt_date_to").value;
				var data=comp+"_"+locat+"_"+txt_date_from+"_"+txt_date_to;
				window.open('daily_finishing_capacity_achievment_iron.php?m='+lnk+'&data='+data, "MY PAGE");
				//window.open('daily_finishing_capacity_achievment_iron.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
				
			}

		}
		else if( lnk == 'eWFybl9zdG9ja19ncmFwaA==')//yarn_stock_graph
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var action='report_generate';//New report generate page action 
				var hompage_flag=1;
				window.open('yarn_stock_graph.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "PP Sample Approval Pending");
			}
		}
		else if( lnk == 'eWFybl9jb25zdW1wdGlvbl9ncmFw')//yarn_consumption_grap
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var action='report_generate';//New report generate page action 
				var hompage_flag=1;
				window.open('yarn_consumption_grap.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "PP Sample Approval Pending");
			}
		}
			
		
		
		else if( lnk == 'ZHllaW5nX2NhcGFjaXR5X3ZzX2xvYWQ=')//ZHllaW5nX2NhcGFjaXR5X3ZzX2xvYWQ= dyeing_capacity_vs_load
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=dyeing_capacity_vs_load_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var txt_date_from	=this.contentDoc.getElementById("txt_date_from").value;
				var txt_date_to	=this.contentDoc.getElementById("txt_date_to").value;
				if(txt_date_from=="")
				{
					alert("Please Select Form Date.");return;
				}
				var data=comp+"_"+locat+"_"+txt_date_from+"_"+txt_date_to;
				window.open('dyeing_capacity_vs_load.php?m='+lnk+'&data='+data, "MY PAGE");
				
			}

		}
		
		else if( lnk == 'a25pdHRpbmdfY2FwYWNpdHlfdnNfbG9hZA==')//knitting_capacity_vs_load
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=dyeing_capacity_vs_load_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var txt_date_from	=this.contentDoc.getElementById("txt_date_from").value;
				var txt_date_to	=this.contentDoc.getElementById("txt_date_to").value;
				if(txt_date_from=="")
				{
					alert("Please Select Form Date.");return;
				}
				var data=comp+"_"+locat+"_"+txt_date_from+"_"+txt_date_to;
				window.open('knitting_capacity_vs_load.php?m='+lnk+'&data='+data, "MY PAGE");
				
			}

		}
		
		else if( lnk == 'MzBfZGF5c19rbml0X2VmZl90cmVuZA==') //MzBfZGF5c19rbml0X2VmZl90cmVuZA== // 30_days_knit_eff_trend
		{
			window.open('knitting_dyeing_production_trend.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'dG9kYXlfcHJvZHVjdGlvbl9ncmFwaF93b3JraW5nX2NvbXBhbnk=') //today_production_graph_working_company// 30_days_knit_eff_trend
		{
			window.open('today_production_graph_working_company.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		
		else if( lnk == 'MzBfZGF5c19keWVuX2VmZl90cmVuZA==') //MzBfZGF5c19keWVuX2VmZl90cmVuZA== // 30_days_dyen_eff_trend
		{
			window.open('knitting_dyeing_production_trend.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19zZXduX2VmZl90cmVuZA==') //MzBfZGF5c19zZXduX2VmZl90cmVuZA== // 30_days_sewn_eff_trend
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('sewn_eff_trend.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2xpbmVz') //MzBfZGF5c19pZGxlX2xpbmVz // 30_days_idle_lines
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_line_rmg_worker_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX3JtZ193b3JrZXI=') //MzBfZGF5c19pZGxlX3JtZ193b3JrZXI= // 30_days_idle_rmg_worker
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_line_rmg_worker_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2tuaXRfbWNobg==') //MzBfZGF5c19pZGxlX2tuaXRfbWNobg== // 30_days_idle_knit_mchn
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_knit_dyen_mchn.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2R5ZW5fbWNobg==') //MzBfZGF5c19pZGxlX2R5ZW5fbWNobg== // 30_days_idle_dyen_mchn
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_knit_dyen_mchn.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'Y2FwYWNpdHlfc3RhdHVzX3Ntdg==') //Y2FwYWNpdHlfc3RhdHVzX3Ntdg== // capacity_status_smv
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			
			var tval=0;
			var r=confirm("Press ok to present data in HOURS. \n \nPress Cancle to present data in MINUTE.")
			if (r==true)
			{
				tval=1;
			}
			else
			{
				tval=0;
			}
			
			window.open('capacity_status_smv_graph.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+tval, "MY PAGE");
			//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		
		else if( lnk == 'Y2FwYWNpdHlfY29tcGFyaXNvbl9pbl9ob3Vy') //Y2FwYWNpdHlfY29tcGFyaXNvbl9pbl9ob3Vy // capacity_comparison_in_hour
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('capacity_comparison_in_hour.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
			//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		
		//ZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg==
		else if( lnk == 'ZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg==') //ZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg== // finishing_capacity_achievment_iron
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			
			window.open('finishing_capacity_achievment_iron.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
			//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		else if( lnk == 'Y2FwYWNpdHlfYm9va2VkX3F0eQ==') //Y2FwYWNpdHlfYm9va2VkX3F0eQ== // capacity_booked_qty
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('capacity_booked_qty_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		else if( lnk == 'Z210c19yZWplY3RfYWx0ZXJfcGVy') //Z210c19yZWplY3RfYWx0ZXJfcGVy // Gmts Reject Alter Percentage
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('gmts_reject_alter_per.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		else
		{
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");//ZGF0YQ==
		}
			
		return; 
	}
	else
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+lnk+'</body</html>');
		d.close(); 
	}
	
}

	
function show_report_data ( lnk )
{
	var comp=$('#cbo_company_home').val();
	var locat=$('#cbo_location_home').val();
	//alert(lnk);	
	if( lnk == 'c2hpcG1lbnRfcGVuZGluZ19yZXBvcnQ=')//c2hpcG1lbnRfcGVuZGluZ19yZXBvcnQ= //shipment_pending_report //existing report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var action='report_generate';//existing report generate page action 
			window.open('reports/management_report/merchandising_report/requires/shipment_pending_report_controller.php?action='+action+'&cbo_company_id='+comp+'&cbo_location_id='+locat, "Shipment Pending Report");
		}
	}
	else if( lnk == 'Y291bnRyeV93aXNlX3NoaXBtZW50X3BlbmRpbmdfcmVwb3J0')//Y291bnRyeV93aXNlX3NoaXBtZW50X3BlbmRpbmdfcmVwb3J0 //country_wise_shipment_pending_report //existing report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var action='report_generate';//existing report generate page action 
			window.open('reports/management_report/merchandising_report/requires/country_wise_shipment_pending_report_controller.php?action='+action+'&cbo_company_id='+comp+'&cbo_location_id='+locat, "Shipment Pending Report");
		}
	}
	else if( lnk == 'cXVvdGF0aW9uX3N1Ym1pc3Npb25fcGVuZGluZw==')//cXVvdGF0aW9uX3N1Ym1pc3Npb25fcGVuZGluZw== //quotation_submission_pending //new report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var action='report_generate';//new report generate page action 
			var hompage_flag=1;
			window.open('home_report/quotation_submission_pending_report.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag,"Quotation Submission Pending");
		}
	}
	else if( lnk == 'YnV5ZXJfaW5xdWlyeV9zdGF0dXNfcmVwb3J0')//YnV5ZXJfaW5xdWlyeV9zdGF0dXNfcmVwb3J0 //buyer_inquiry_status_report //new report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var action='report_generate';//New report generate page action 
			var hompage_flag=1;
			//window.open('reports/management_report/merchandising_report/requires/buyer_inquery_qote_submit_controller.php?action='+action+'&cbo_company_name='+comp+'&hompage_flag='+hompage_flag, "Price Quotation Statement");
			window.open('home_report/quotation_finalization_pending_report.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "Quotation Finalization Pending");
		}
	}
	else if( lnk == 'eWFybl9pc3N1ZV9wZW5kaW5n')//eWFybl9pc3N1ZV9wZW5kaW5n //yarn_issue_pending //new report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var action='report_generate';//New report generate page action 
			var hompage_flag=1;
			window.open('home_report/yarn_issue_pending.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_name='+locat+'&hompage_flag='+hompage_flag, "Yarn Issue Pending");
		}
	}
	else if( lnk == 'UFBfc2FtcGxlX2FwcHJvdmFsX3BlbmRpbmc=')//UFBfc2FtcGxlX2FwcHJvdmFsX3BlbmRpbmc= //PP_sample_approval_pending //new report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var action='report_generate';//New report generate page action 
			var hompage_flag=1;
			window.open('home_report/pp_sample_approval_pending.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "PP Sample Approval Pending");
		}
	}
	
	
	
	else if( lnk == 'RXhfRmFjdG9yeV92c19jb21tZXJjaWFsX2FjdGl2aXRpZXM=')//RXhfRmFjdG9yeV92c19jb21tZXJjaWFsX2FjdGl2aXRpZXM= //Ex_Factory_vs_commercial_activities //new report
	{
		if(comp==0 || comp=="")
		{
			alert ('Select Company Name');
			return;
		}
		else
		{
			var data=comp+"_"+locat;
			var page_link='home_report/home_report_popup.php?action=ex_factory_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=300px, height=200px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var txt_year=this.contentDoc.getElementById("txt_year").value;
				if(txt_year=="")
				{
					alert ('Write Year');
					return;
				}
				else
				{
					var action='report_generate';
					window.open('ex_factory_vs_commercial_activities_report.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&txt_year='+txt_year, "Ex-Factory vs Commercial Activities");
				}
			}
			
		}
	}
	else
	{
		alert('Development Running');
		return;
	}
		
}
	
</script>
<style>
.search-bar{
  margin-top: 5px;
  border-bottom: 5px solid #D7D8DA;
  background: linear-gradient(to right, #799203 40%,#799203 20%,#90AB10 40%,#90AB10 100%);
  padding: 5px;
  color: #fff;
  font-family: "Lato",sans-serif;
  font-size: 13px;
}
.search-bar select{border-radius:0; border-color:#DDD; cursor:pointer;}

</style>

<div>
<? //echo $manufacturing_company; ?>
<table class="search-bar" width="100%" cellpadding="0" cellspacing="0" border="1">
<tr>
    <td align="center">
        <strong>Company Name :</strong>&nbsp;&nbsp;
        <? 
        echo create_drop_down( "cbo_company_home", 230, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "	---- All Company ---- ", $selected,"load_drop_down( 'today_production_graph', this.value, 'load_drop_down_location', 'sp_location' );" );
        ?>&nbsp;<strong>Location Name :</strong>&nbsp;<span id="sp_location" ><? 
        echo create_drop_down( "cbo_location_home", 230, $blank_array,"", 1, "-- Select Location--", $selected, "",0 );
        ?> </span>
    </td>
</tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;" border="1">
<tr>    
    
    <td style="border:1px solid #F00; height:450px; border:1px solid #CCC;" align="center">
    <table><tr><td>
			<?
			$sql=sql_select("select id,module_id,item_id,user_id,sequence_no from home_page_priviledge where USER_ID='".$_SESSION['logic_erp']['user_id']."' order by module_id,sequence_no");//this query use HOME PAGE Graph and report PRIVILEDGE for user
			foreach( $sql as $rows )
			{
				$priv_items[$rows[csf("module_id")]][$rows[csf("item_id")]]['seq']=$rows[csf("sequence_no")];
			}
			$k=1;
            foreach( $priv_items as $mod=>$item_arr )
            {
				if($mod==1)
				{
					 foreach( $item_arr as $item_id=>$seq )
					 {
						?>
						<a href="##" onclick="show_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>',<? echo $k; ?>)">
							<span class="panel-<? echo $item_id; ?>">
								<img src="home_css/graph-<? echo $item_id; ?>.png">
								<? echo $home_page_array[$mod][$item_id]['name']; ?>
							</span>
						</a>
						<?
					 }
				}
            }
            ?>
           
			<?
            foreach( $priv_items as $mod=>$item_arr )
            {
                if($mod==2)
                {
                    foreach( $item_arr as $item_id=>$seq )
                    {
                        ?>
                        <a href="##" onclick="show_report_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>')">
                            <span class="panel-<? echo $item_id; ?>">
                                <img src="home_css/pending-<? echo $item_id; ?>.png">
                                <? echo $home_page_array[$mod][$item_id]['name']; ?>
                            </span>
                        </a>
                        <?
                    }
                }
            }
            ?>
            </td></tr></table>
            
            
    </td>
</tr>
</table>
</div>

<script src="ext_resource/hschart/hschart.js"></script>

<script src="includes/functions_bottom.js" type="text/javascript"></script>
<script>

$(document).ready(function(){
    $('#my_div div').each(function(graph_grp) {
	  	$(this).attr('onMouseOver',"hover_effect(this)");
		$(this).attr('onMouseOut',"mouseout_effect(this)");
	});
});
 
function hover_effect( divclass )
{
	//alert ('running development');
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").css( "-webkit-transform"," scale(1.3)" );
   	$("."+cls[0]+" img").css('transform', 'scale(1.3)'); 
}

function mouseout_effect( divclass )
{
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").removeAttr( 'style' );
   	//$("."+cls).removeAttr( 'style' );
}


</script>

<?php
function add_month($orgDate,$mon)
{
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
	return $retDAY;
}


?>