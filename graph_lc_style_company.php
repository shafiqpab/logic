<? 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Graph", "", "", 1, $unicode, $multi_select, '');
$type=1; //1= publish ship date;
?>
<link rel="stylesheet" href="home_css/styles.css">

<script>
	var permission = '<? echo $permission; ?>';
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','home');
	}
	
	function show_summary_val()
	{
		page_link='summary_popup.php?action=summary_popup_value';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=800px, height=400px, center=1, resize=0, scrolling=0','home');
	}
	
	
function show_data( lnk, lid  )
{
	
	var prod_comp=$('#cbo_prod_company_home').val();
	var comp=$('#cbo_company_home').val();
	var locat=$('#cbo_location_home').val();
	var cbo_floor=$('#cbo_floor').val();
	if( lid == 1 ) //Static Graph design
	{
		 //alert(lnk);
		 
		 var linkData='m='+lnk+'&cp='+comp+"__"+prod_comp+"__"+locat+"__"+cbo_floor
		 
		 	
		if( lnk == 'VG9kYXlfSG91cmx5X1Byb2R1Y3Rpb24=')// Today_Hourly_Production' )
		{
			window.open('home_graph/dashborad/today_production_graph.php?'+linkData, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xbnR5')// order_in_hand_qnty
		{ 
			window.open('home_graph/dashborad/show_graph.php?'+linkData, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xbnR5X1BTRA==')// order_in_hand_qnty_PSD
		{ 
			window.open('home_graph/dashborad/order_in_hand_qnty_PSD.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWw=')// order_in_hand_val
		{
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'c3RhY2tfcW50eQ==')// stack_qnty
		{
			//window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
			var data='';
			var page_link='forecast_popup.php?action=opendate_type_search_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=420px, height=250px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var from_date=this.contentDoc.getElementById("txt_date_from").value;
				var to_date=this.contentDoc.getElementById("txt_date_to").value;
				var type=this.contentDoc.getElementById("cbo_type").value;
				
				window.open('show_graph.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp+'&from_date='+from_date+'&to_date='+to_date+'&type='+type, "MY PAGE");
			}
				
		
		
					
			
		}
		else if( lnk == 'c3RhY2tfdmFsdWU=')// stack_value
		{
			//window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
			var data='';
			var page_link='forecast_popup.php?action=opendate_type_search_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=420px, height=250px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var from_date=this.contentDoc.getElementById("txt_date_from").value;
				var to_date=this.contentDoc.getElementById("txt_date_to").value;
				var type=this.contentDoc.getElementById("cbo_type").value;
				
				window.open('show_graph.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp+'&from_date='+from_date+'&to_date='+to_date+'&type='+type, "MY PAGE");
			}
				
		
		
		}
		else if( lnk == 'Y29tcGFueV9rcGk=')// company_kpi
		{
			var data='';
			var page_link='forecast_popup.php?action=opendate_popup&data='+data;
			//var page_link='graph_grp.php?action=opendate_popup';
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=280px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("txt_date_from").value;
				if(theemail!='')
				{
					window.open('dash_board.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp+'&date_data='+theemail, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
			}
		}
		
		else if( lnk == 'Y29tcGFueV9rcGlfd292ZW4=')// company_kpi woven
		{
			var data='';
			var page_link='forecast_popup.php?action=opendate_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=280px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("txt_date_from").value;
				if(theemail!='')
				{
					window.open('home_graph/dashborad/company_key_performance_woven.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp+'&date_data='+theemail, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
			}
		}
		
		
		
		else if( lnk == 'dGV4dGlsZV9ncmFwaA==')// Textile Graph
		{
			var data='';
			var page_link='home_graph/dashborad/textile_graph.php?action=opendate_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=280px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var date_from=this.contentDoc.getElementById("txt_date_from").value;
				var date_to=this.contentDoc.getElementById("txt_date_to").value;
				if(date_from!='' && date_to!='')
				{
					window.open('textile_graph.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp+'&date_data='+date_from+"__"+date_to, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
			}
		}
		
		
		else if( lnk == 'Z2FybWVudHNfZ3JhcGg=')// Garments Graph
		{
			var data='';
			var page_link='home_graph/dashborad/garments_graph.php?action=opendate_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=280px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var date_from=this.contentDoc.getElementById("txt_date_from").value;
				var date_to=this.contentDoc.getElementById("txt_date_to").value;
				if(date_from!='' && date_to!='')
				{
					window.open('garments_graph.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp+'&date_data='+date_from+"__"+date_to, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
			}
		}
		
		
		
		
		
		else if( lnk == 'dG90YWxfYWN0aXZpdGllc19hdXRvX21haWw=')//Total Activities mail data;
		{
			var data='';
			var page_link='forecast_popup.php?action=opendate_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=280px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("txt_date_from").value;
				if(theemail!='')
				{
					window.open('home_graph/dashborad/total_activities_auto_mail.php?m='+lnk+'&cp='+comp+'&date_data='+theemail, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
				
			}
			
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
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=800px, height=300px, center=1, resize=0, scrolling=0','home')
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
		else if( lnk == 'c2FsZXNfZm9yZWNhc3RfcW50eQ==') // sales_forecast_qnty
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=forecast_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=700px, height=300px, center=1, resize=0, scrolling=0','home')
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
		else if( lnk == 'c2FsZXNfZm9yZWNhc3RfdmFsdWU=') // sales_forecast_value
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=forecast_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=700px, height=300px, center=1, resize=0, scrolling=0','home')
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
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','home')
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
			
		else if( lnk == 'ZHllaW5nX2NhcGFjaXR5X3ZzX2xvYWQ=')// dyeing_capacity_vs_load
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=dyeing_capacity_vs_load_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','home')
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
			
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}

			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=dyeing_capacity_vs_load_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var txt_date_from	=this.contentDoc.getElementById("txt_date_from").value;
				var txt_date_to	=this.contentDoc.getElementById("txt_date_to").value;
				if(txt_date_from=="")
				{
					alert("Please Select Form Date.");return;
				}
				var data=comp+"__"+locat+"__"+txt_date_from+"__"+txt_date_to+"__"+cbo_floor+"__"+prod_comp;
				window.open('knitting_capacity_vs_load.php?m='+lnk+'&data='+data, "MY PAGE");
				
			}

		}
		
		else if( lnk == 'MzBfZGF5c19rbml0X2VmZl90cmVuZA==') // 30_days_knit_eff_trend
		{
			
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('knitting_dyeing_production_trend.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
			
		}
		
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xdHlfdGVhbV9sZWFkZXJfd2lzZQ==') // order_in_hand_qty_team_leader_wise
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/order_in_hand_qty_team_leader_wise.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWxfdGVhbV9sZWFkZXJfd2lzZQ==') // order_in_hand_val_team_leader_wise
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/order_in_hand_val_team_leader_wise.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		
		else if( lnk == 'MzBfZGF5c19keWVuX2VmZl90cmVuZA==') // 30_days_dyen_eff_trend
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('knitting_dyeing_production_trend.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
			
		}
		
		else if( lnk == 'dG9kYXlfcHJvZHVjdGlvbl9ncmFwaF93b3JraW5nX2NvbXBhbnk=') //today_production_graph_working_company// 30_days_knit_eff_trend
		{
			window.open('home_graph/dashborad/today_production_graph_working_company.php?'+linkData, "MY PAGE");
		}
		
		else if( lnk == 'MzBfZGF5c19zZXduX2VmZl90cmVuZA==') //MzBfZGF5c19zZXduX2VmZl90cmVuZA== // 30_days_sewn_eff_trend
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('sewn_eff_trend.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2xpbmVz') //30_days_idle_lines
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_line_rmg_worker_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX3JtZ193b3JrZXI=') // 30_days_idle_rmg_worker
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_line_rmg_worker_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2tuaXRfbWNobg==') // 30_days_idle_knit_mchn
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_knit_dyen_mchn.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2R5ZW5fbWNobg==') // 30_days_idle_dyen_mchn
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_knit_dyen_mchn.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'Y2FwYWNpdHlfc3RhdHVzX3Ntdg==') // capacity_status_smv
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
		
		else if( lnk == 'Y2FwYWNpdHlfY29tcGFyaXNvbl9pbl9ob3Vy') // capacity_comparison_in_hour
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
		else if( lnk == 'ZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg==') // finishing_capacity_achievment_iron
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			
			window.open('finishing_capacity_achievment_iron.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
			//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		else if( lnk == 'Y2FwYWNpdHlfYm9va2VkX3F0eQ==')  //capacity_booked_qty
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('capacity_booked_qty_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");//google_line_bar_chart//test_line_bar_chart//amchart_line_bar //capacity_status_smv
		}
		else if( lnk == 'Z210c19yZWplY3RfYWx0ZXJfcGVy')  //Gmts Reject Alter Percentage
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('gmts_reject_alter_per.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
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
		if( lnk == 'c2hpcG1lbnRfcGVuZGluZ19yZXBvcnQ=')//c2hpcG1lbnRfcGVuZGluZ19yZXBvcnQ= //shipment_pending_report 
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				//var action='report_generate';//existing report generate page action 
				var action='report_generate2';//existing report generate page action 
				window.open('reports/management_report/merchandising_report/requires/shipment_pending_report_controller.php?action='+action+'&cbo_company_id='+comp+'&cbo_date_category=2&cbo_location_id='+locat, "Shipment Pending Report");
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
				emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=300px, height=200px, center=1, resize=0, scrolling=0','home')
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
	
	function fn_reset_com(str){
		if(str==2)$("#cbo_prod_company_home").val(0);
		else $("#cbo_company_home").val(0);
	}
	
</script>
<?

	
	$no_of_company='';
	if($_SESSION['logic_erp']["data"]=="")
	{ 
		if($db_type==0) 
		{
			$manufacturing_company=return_field_value("group_concat(comp.id)","lib_company as comp","comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond");
		}
		else
		{
			$manufacturing_company= return_field_value("LISTAGG(comp.id, ', ') WITHIN GROUP (ORDER BY comp.id) company","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company");
		}
		
		$no_of_company=count(explode(",",$manufacturing_company));
		$date=date("Y",time());
		$month_prev=add_month(date("Y-m-d",time()),-3);
		$month_next=add_month(date("Y-m-d",time()),8);
		$start_yr=date("Y",strtotime($month_prev));
		$end_yr=date("Y",strtotime($month_next));
		for($e=0;$e<=11;$e++)
		{
			$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
			$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
		}
		
	
		
		
		
		$exFactory_arr=array();
		$exfactory_data=sql_select("select po_break_down_id,country_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where 1=1  and status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
		foreach($exfactory_data as $row)
		{
			$exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		
		
		
		
		$i=1; $html='<tbody>'; $html2='<tbody>';
		
//*************************************************************************************		
		$where_con=" and (";
		$con=0;
		foreach($yr_mon_part as $key=>$val)
		{
			if($db_type==0) 
			{	if($type==1){
					if($con==0){$where_con.=" b.pub_shipment_date like '".$val."-%"."' ";}
					else{$where_con.=" or b.pub_shipment_date like '".$val."-%"."'";}
				}
				else
				{
					if($con==0){$where_con.=" a.country_ship_date like '".$val."-%"."' ";}
					else{$where_con.=" or a.country_ship_date like '".$val."-%"."'";}
				}
				$con=1;
			}
			else
			{
				if($type==1){
					if($con==0){$where_con.=" to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."'";}
					else{$where_con.=" or to_char(b.pub_shipment_date,'YYYY-MM-DD') like '".$val."-%"."'";}
				}
				else
				{
					if($con==0){$where_con.=" to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."'";}
					else{$where_con.=" or to_char(a.country_ship_date,'YYYY-MM-DD') like '".$val."-%"."'";}
				}
				
				$con=1;
			}
			
		}
		$where_con.=" ) ";

		if($db_type==0) 
		{	
			if($type==1){
				$group_con =" group by b.id, c.company_name,a.country_id,b.pub_shipment_date";
			}
			else
			{
				$group_con =" group by b.id, c.company_name,a.country_id,a.country_ship_date";
			}
		}
		else
		{
		
			if($type==1){
				$group_con ="  group by c.company_name,b.id, c.total_set_qnty,b.unit_price,b.pub_shipment_date";
			}
			else
			{
				$group_con ="  group by c.company_name,b.id, a.country_id,c.total_set_qnty,b.unit_price,a.country_ship_date";
			}
		
		}


		if($db_type==0) 
		{
			if($type==1){
				$sql="select 
				c.company_name,
				DATE_FORMAT(b.pub_shipment_date,'%Y-%m') as yr_mon,
				b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, 
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS 'confpoval', 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS 'projpoval' ,
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS 'confpoqty', 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS 'projpoqty' 
				
				from wo_po_break_down as b, wo_po_details_master as c where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
			else
			{
				$sql="select 
				c.company_name,
				DATE_FORMAT(a.country_ship_date,'%Y-%m') as yr_mon,
				b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, 
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS 'confpoval', 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS 'projpoval' ,
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS 'confpoqty', 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS 'projpoqty' 
				
				from wo_po_color_size_breakdown as a, wo_po_break_down as b, wo_po_details_master as c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
		}
		else
		{
			if($type==1){
				$sql="select c.company_name,to_char(b.pub_shipment_date,'YYYY-MM') as yr_mon,b.id as po_id, c.total_set_qnty as ratio, b.unit_price, 
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoval, 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoval ,
				sum(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) AS confpoqty, 
				sum(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) AS projpoqty 
				
				from wo_po_break_down b, wo_po_details_master c where b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
			else
			{
				
				$sql="select c.company_name,to_char(a.country_ship_date,'YYYY-MM') as yr_mon,b.id as po_id, c.total_set_qnty as ratio, b.unit_price, a.country_id, 
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_total ELSE 0 END) AS confpoval, 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_total ELSE 0 END) AS projpoval ,
				sum(CASE WHEN b.is_confirmed=1 THEN a.order_quantity ELSE 0 END) AS confpoqty, 
				sum(CASE WHEN b.is_confirmed=2 THEN a.order_quantity ELSE 0 END) AS projpoqty 
				
				from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($manufacturing_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $where_con $group_con";
			}
		}
		

      
	$result=sql_select($sql);
	foreach($result as $rows){
		$dataArr[$rows[csf('yr_mon')]][]=$rows;
	}
	
	
	
	unset($result);

		
	
	
	
//*********************************************************************		
		
		foreach($yr_mon_part as $key=>$val)
		{
			
			
			$confPoQty=0; $projPoQty=0; $confPoVal=0; $projPoVal=0; $exFactoryQty=0; $exFactoryVal=0;
			foreach($dataArr[$val] as $row)
			{ 
				$confPoQty+=$row[csf('confpoqty')]; 
				$projPoQty+=$row[csf('projpoqty')];
				
				$confPoVal+=$row[csf('confpoval')]; 
				$projPoVal+=$row[csf('projpoval')];
				
				$exFactoryQty+=$exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]];
				$exFactoryVal+=($exFactory_arr[$row[csf('po_id')]][$row[csf('country_id')]]/$row[csf('ratio')])*$row[csf('unit_price')];
				
			
			
			$orderDataArr[$row[csf('company_name')]][$val][csf('povalue')]+=($row[csf('confpoval')]+$row[csf('projpoval')]);
			$orderDataArr[$row[csf('company_name')]][$val][csf('poqty')]+=($row[csf('confpoqty')]+$row[csf('projpoqty')]);
			}




			$conf_tot_for_graph_stack[$key]=$confPoQty;
			$proj_tot_for_graph_stack[$key]=$projPoQty;
			
			$conf_tot_for_graph_stack_val[$key]=$confPoVal;
			$proj_tot_for_graph_stack_val[$key]=$projPoVal;
			
			$totQty=$projPoQty+$confPoQty;
			$perc=($exFactoryQty/$totQty)*100;
			$tot_for_graph[$key]=$totQty;
			
			$totVal=$projPoVal+$confPoVal;
			$perc_val=($exFactoryVal/$totVal)*100;
			$tot_for_graph_val[$key]=$totVal;
			
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$html.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
			$html.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
					<td align="right">'.number_format($projPoQty,0).'</td>
					<td align="right">'.number_format($confPoQty,0).'</td>
					<td align="right">'.number_format($totQty,0).'</td>
					<td align="right">'.number_format($exFactoryQty,0).'</td>
					<td align="right">'.number_format($perc,2).'</td>';
			$html.='</tr>';
			
			$html2.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
			$html2.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
					<td align="right">'.number_format($projPoVal,0).'</td>
					<td align="right">'.number_format($confPoVal,0).'</td>
					<td align="right">'.number_format($totVal,0).'</td>
					<td align="right">'.number_format($exFactoryVal,0).'</td>
					<td align="right">'.number_format($perc_val,2).'</td>';
			$html2.='</tr>';
			
			$totProjQty+=$projPoQty;
			$totConfQty+=$confPoQty;  
			$totExFactoryQty+=$exFactoryQty; 
			$grandTotQty+=$totQty;
			
			$totProjVal+=$projPoVal;
			$totConfVal+=$confPoVal;  
			$totExFactoryVal+=$exFactoryVal; 
			$grandTotVal+=$totVal;

			$i++;
		}
		// var_dump($orderDataArr);
		
		
		$totPerc=($totExFactoryQty/$grandTotQty)*100;
		$html.='</tr></tbody><tfoot><th>Total</th>'; 
        $html.='<th align="right">'.number_format($totProjQty,0).'</th>
				<th align="right">'.number_format($totConfQty,0).'</th>
				<th align="right">'.number_format($grandTotQty,0).'</th>
                <th align="right">'.number_format($totExFactoryQty,0).'</th>
				<th align="right">'.number_format($totPerc,2).'</th></tfoot>'; 
		
		$totPercVal=($totExFactoryVal/$grandTotVal)*100;		
        $html2.='</tr></tbody><tfoot><th>Total</th>'; 
        $html2.='<th align="right">'.number_format($totProjVal,0).'</th>
				<th align="right">'.number_format($totConfVal,0).'</th>
				<th align="right">'.number_format($grandTotVal,0).'</th>
				<th align="right">'.number_format($totExFactoryVal,0).'</th>
				<th align="right">'.number_format($totPercVal,2).'</th></tfoot>'; 

		$catg="[";
		for($i=0;$i<=11;$i++)
		{
			if($i!=11) $catg .="'".date("M",strtotime($yr_mon_part[$i].'-01'))."',"; else $catg .="'".date("M",strtotime($yr_mon_part[$i].'-01'))."']";
		}
		
		
		$capacity_in_value_arr=return_library_array("select company_name,capacity_in_value from variable_settings_commercial where variable_list=5 ", "company_name","capacity_in_value");
	
		
		
		$sql_comp=sql_select("select comp.id as id, comp.company_name,company_short_name from lib_company comp where comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.id asc");
		$k=1;
		$data .="["; 
		$data_qnt .="[";
		$com=0;
		foreach($sql_comp as $row_comp)
		{
			$val=$capacity_in_value_arr[$row_comp[csf('id')]];
			if ($capacity!="")$capacity=$capacity.", ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
			else $capacity="Capacity: ".$row_comp[csf('company_short_name')].": $ ".number_format($val,2,'.',',');
			$cname=$row_comp[csf('company_short_name')];
			$data .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			$data_qnt .="{ name: '".$row_comp[csf('company_short_name')]."', data:[";
			for($i=0;$i<=11;$i++)
			{
				$value=0;
				if($db_type==0) $year_field="b.pub_shipment_date"; else $year_field="to_char(b.pub_shipment_date,'YYYY-MM-DD')";
				
				
				$value=$orderDataArr[$row_comp[csf('id')]][$yr_mon_part[$i]][csf('povalue')];
				$qty=$orderDataArr[$row_comp[csf('id')]][$yr_mon_part[$i]][csf('poqty')];
				
				if( $i!=11) 
				{
					$data .=number_format( $value,0,'.','').",";
					$data_qnt .=number_format( $qty,0,'.','').",";
				}
				else 
				{
					$data .=number_format( $value,0,'.','').""; 
					$data_qnt .=number_format( $qty,0,'.','').""; 
				}
			}
			if(count($sql_comp)!=$k) 
			{
				 $data .="], stack: 'none'}, ";
				 $data_qnt .="], stack: 'none'}, ";
			}
			else 
			{
				$data .="], stack: 'none'}] ";
				$data_qnt .="], stack: 'none'}] ";
			}
			$k++;
			$com++;
		}
		
		
		$data_qnt_stck .="[{ name: 'Confirmed', data:[";
		$data_val_stck .="[{ name: 'Confirmed', data:[";
		foreach($tot_for_graph as $key=>$value )
		{
			if( $i!=11)  $data_qnt_stck .=number_format( $conf_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $conf_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $conf_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		 $data_qnt_stck .="], stack: 'conf'}, ";
		 $data_val_stck .="], stack: 'conf', color: 'green'}, ";
		 
		 
		 
		$data_qnt_stck .="{ name: 'Projected', data:[";
		$data_val_stck .="{ name: 'Projected', data:[";
		foreach($tot_for_graph as $key=>$value )
		{
			if( $i!=11)  $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','').","; else $data_qnt_stck .=number_format( $proj_tot_for_graph_stack[$key],0,'.','')."";
			if( $i!=11)  $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','').","; else $data_val_stck .=number_format( $proj_tot_for_graph_stack_val[$key],0,'.','')."";
		}
		$data_qnt_stck .="], stack: 'conf'}] ";
		$data_val_stck .="], stack: 'conf', color: 'red'}] ";
		
		
		
		$_SESSION['logic_erp']["data"]=$data;
		$_SESSION['logic_erp']["data_qnt"]=$data_qnt;
		$_SESSION['logic_erp']["capacity"]=$capacity;
		$_SESSION['logic_erp']["catg"]=$catg;
		$_SESSION['logic_erp']["data_qnt_stck"]=$data_qnt_stck;
		$_SESSION['logic_erp']["data_val_stck"]=$data_val_stck;
		
		$_SESSION['logic_erp']["data_summ_qty"]=$html;
		$_SESSION['logic_erp']["data_summ_val"]=$html2;
		$_SESSION['logic_erp']["no_of_company"]=$no_of_company;
	}
	else
	{
		$data=$_SESSION['logic_erp']["data"];
		$data_qnt=$_SESSION['logic_erp']["data_qnt"];
		$capacity=$_SESSION['logic_erp']["capacity"];
		$catg=$_SESSION['logic_erp']["catg"];
		$data_qnt_stck=$_SESSION['logic_erp']["data_qnt_stck"];
		$data_val_stck=$_SESSION['logic_erp']["data_val_stck"];
		
		$html=$_SESSION['logic_erp']["data_summ_qty"];
		$html2=$_SESSION['logic_erp']["data_summ_val"];
		$no_of_company=$_SESSION['logic_erp']["no_of_company"];
	}
	

?>

<div style="width:1110px;">
<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;">
<tr height="30px">
    <td width="80%" align="center" style="margin-top:2px; margin-left:20px; margin-right:20px; border-radius:8px; font-size:16px;" valign="top">
       <div class="gr-search-panel-lc-style">
       LC Comp : 
	   <? 
        echo create_drop_down( "cbo_company_home", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "	---- All Company ---- ", $selected,"load_drop_down( 'home_graph/dashborad/today_production_graph_working_company', this.value, 'load_drop_down_location', 'sp_location' );fn_reset_com(2)" );
	   ?>
        Prod. Comp :
        <? 
        echo create_drop_down( "cbo_prod_company_home", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "	---- All Company ---- ", $selected,"load_drop_down( 'home_graph/dashborad/today_production_graph_working_company', this.value, 'load_drop_down_location', 'sp_location');fn_reset_com(1)" );
	    ?>
        &nbsp;Location: <span id="sp_location"><? 
        echo create_drop_down( "cbo_location_home", 160, $blank_array,"", 1, "-- All --", $selected, "",0 );
        ?> </span>
        &nbsp;Floor:<span id="sp_floor" ><? 
        echo create_drop_down( "cbo_floor", 100, $blank_array,"", 1, "-- All --", $selected, "",0 );
		?> </span>
       </div> 
    </td>
    <td width="20%" align="center" rowspan="2" valign="top">
  		<div style="margin-left:5px; margin-top:3px; width:150px;" align="center" id="my_div">
			<?
			$sql=sql_select("select id,module_id,item_id,user_id,sequence_no from home_page_priviledge where USER_ID='".$_SESSION['logic_erp']['user_id']."' order by module_id,sequence_no");
			foreach( $sql as $rows )
			{
				$priv_items[$rows[csf("module_id")]][$rows[csf("item_id")]]['seq']=$rows[csf("sequence_no")];
			}
			
			
			$k=1;
            foreach( $priv_items as $mod=>$item_arr )
            {
				if($mod==1)
				{	$showNumber=1;	
					 foreach( $item_arr as $item_id=>$seq )
					 {
						 if($showNumber<10){
						?>
						<a href="##" onclick="show_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>',<? echo $k; ?>)">
							<div class="panel-<? echo $item_id; ?>">
								<img src="home_css/graph-<? echo $item_id; ?>.png">
								<? echo $home_page_array[$mod][$item_id]['name']; ?>
							</div>
						</a>
						<?
							
						 }
						 $showNumber++;
					 }
				}
            }
            ?>
       </div>
    </td>
</tr>

<tr height="445">
    <td width="80%" align="center" valign="top">
    <?
    if($no_of_company>1)
    {
    ?>
    <table width="910" cellpadding="0" cellspacing="0" align="center">
       
        <tr>
            <td align="center" height="445" width="910">
                <div id="chartdiv" style="width:900; height:445px; background-color:#FFFFFF"></div>
            </td>
        </tr>
       
    </table>
    <?
    }
    else
    {
    ?>
    <table width="1000" cellpadding="0" cellspacing="0">
        <tr>
            <td height="30" valign="middle" align="center" colspan="2">
                <font size="2" color="#4D4D4D"> <strong><span id="caption_text"></span> <? // echo "$start_yr"."-"."$end_yr"; ?></strong></font>
            </td>
            <td colspan="2" rowspan="2" valign="top" align="center"> 
                <div style="margin-left:5px; margin-top:45px">
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="300" id="tableQty">
                        <thead>
                            <th width="50">Month</th>
                            <th>Proj.</th>
                            <th>Conf.</th>
                            <th>Total</th>
                            <th>Ship Out</th>
                            <th>%</th>
                        </thead>
                        <? echo $html; ?>
                    </table>
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="300" id="tableVal">
                        <thead>
                            <th width="50">Month</th>
                            <th>Proj.</th>
                            <th>Conf.</th>
                            <th>Total</th>
                            <th>Ship Out</th>
                            <th>%</th>
                        </thead>
                        <? echo $html2; ?>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td width="8" bgcolor=" "></td>
            <td align="center" height="445" width="700">
                <div id="chartdiv" style="width:700px; height:445px; background-color:#FFFFFF"></div>
            </td>
        </tr>
    </table>
    <?
    }
    ?> 
    </td>
</tr>

<tr valign="top">
	 <td width="80%" colspan="2" >
        <div style="margin-left:20px;" id="my_div">
        <?
        foreach( $priv_items as $mod=>$item_arr )
        {
			
			

			if($mod==1)
			{	
				$showNumber=1;	
				 foreach( $item_arr as $item_id=>$seq )
				 {
					 if($showNumber>=10){
					?>
					<a href="##" onclick="show_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>',<? echo $k; ?>)">
						<div class="panel-a-<? echo $item_id; ?>">
							<img src="home_css/graph-<? echo $item_id; ?>.png">
							<? echo $home_page_array[$mod][$item_id]['name']; ?>
						</div>
					</a>
					<?
						
					 }
					 $showNumber++;
				 }
			}
            			
			if($mod==2)
			{
				foreach( $item_arr as $item_id=>$seq )
				{
					?>
					<a href="##" onclick="show_report_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>')">
                        <div class="panel-a-<? echo $item_id; ?>">
                        	<img src="home_css/pending-<? echo $item_id; ?>.png">
                        	<? echo $home_page_array[$mod][$item_id]['name']; ?>
                        </div>
					</a>
					<?
				}
			}
		}
        ?>
        </div>
     </td>
</tr>
</table>
</div>

<script src="ext_resource/hschart/hschart.js"></script>

<script>
	
Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null, //null
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '13px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
	  
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
	  
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },


   // General
   background2: '#FF0000'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

var ccount='<? echo $com; ?>';
	
	window.onload = function()
	{
		hs_homegraph(1);
	}
	
	function hs_homegraph( gtype ) 
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
		var data_qnty=<? echo $data_qnt; ?>;
		var data=<? echo $data; ?>;
		if(gtype==1)
		{
			var ddd=data;
			var msg="Total Values"
			var uom=" USD";
			

			$('#tableQty').hide();
			$('#tableVal').show();
		}
		else
		{
			var ddd=data_qnty; 
			var msg="Total Pcs"
			var uom=" PCS";
			
			$('#tableQty').show();
			$('#tableVal').hide();
		}
		$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: ' <? echo $capacity; ?> '
			},
	
			xAxis: {
				categories: <? echo $catg; ?>
			},
	
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
	
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b> ' +
						 ': ' + this.y + uom +'<br/>' ;
						//+ 'Total: ' + this.point.stackTotal;  this.series.name + ': ' + this.y + uom +'<br/>' ;
				}
			},
	
			plotOptions: {
				column: {
					stacking: false //'normal'
				}
			},
		
			series: ddd
		});
		
		
	}
	
	function hs_homegraph_stack( gtype )
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
		 
		 if(gtype==1)
		 {
			 var datas=<? echo $data_val_stck; ?>;
			 var msg="Total Values";
			 var cur="USD";
			 
			 $('#tableQty').hide();
			 $('#tableVal').show();
		 }
		 else
		 {
			 var datas=<? echo $data_qnt_stck; ?>;
			 var msg="Total Qnty";
			 var cur="PCS";
			 
			 $('#tableQty').show();
			 $('#tableVal').hide();
		 }
		 
		$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: ' <? echo $capacity; ?> '
			},
	
			xAxis: {
				categories: <? echo $catg; ?>
			},
	
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
	
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b><br/>' +
						this.series.name + ': '+cur+" " + this.y + '<br/>' 
						+ 'Total: '+cur+" " + this.point.stackTotal;
				}
			},
	
			plotOptions: {
				column: {
					stacking: 'normal'
				}
			},
		
			series: datas
		});
	}
</script>
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
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").css( "-webkit-transform"," scale(1.3)" );
   	$("."+cls[0]+" img").css('transform', 'scale(1.3)'); 
}

function mouseout_effect( divclass )
{
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").removeAttr( 'style' );
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