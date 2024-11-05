<? 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Graph", "", "", 1, $unicode, $multi_select, '');

if($_SESSION[logic_erp][company_id]!=''){$company_cond=" and id in(".$_SESSION[logic_erp][company_id].")";}

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
	
	
function show_data( lnk, lid )
{
	var prod_comp=$('#cbo_prod_company_home').val();
	var comp=$('#cbo_company_home').val();
	var locat=$('#cbo_location_home').val();
	var cbo_floor=$('#cbo_floor').val();
	if( lid == 1 )
	{
		var linkData='m='+lnk+'&cp='+comp+"__"+prod_comp+"__"+locat+"__"+cbo_floor
		if( lnk == 'VG9kYXlfSG91cmx5X1Byb2R1Y3Rpb24=')
		{
			window.open('home_graph/dashborad/today_production_graph.php?'+linkData, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xbnR5')
		{ 
			window.open('home_graph/dashborad/show_graph.php?'+linkData, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xbnR5X1BTRA==')
		{ 
			window.open('home_graph/dashborad/order_in_hand_qnty_PSD.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}else if( lnk == 'bW9udGhseV9leF9mYWN0b3J5X3N0YXR1cw==')
		{ 
			window.open('home_graph/dashborad/monthly_ex_factory_status.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'c3RhdGVtZW50X29mX3NoaXBtZW50X2FuZF9yZWFsaXphdGlvbg==') // Statement of Shipment and Realization
		{ 
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var data=comp+"_"+locat;
				var page_link='home_graph/dashborad/statement_of_shipment_and_realization.php?action=year_popup&data='+data;

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
						window.open('statement_of_shipment_and_realization.php?action='+action+'&company='+comp+'&location='+locat+'&txt_year='+txt_year, "Statement of Shipment and Realization");
					}
				}
			}
		}
		else if( lnk == 'YjJiX2xpYWJpbGl0eV9jaGFydA==') //B2B LIABILITY CHART
		{ 
			{ 
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var data=comp+"_"+locat;
				var page_link='home_graph/dashborad/b2b_liability_chart.php?action=year_popup&data='+data;

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
						window.open('b2b_liability_chart.php?action='+action+'&company='+comp+'&location='+locat+'&txt_year='+txt_year, "Statement of Shipment and Realization");
					}
				}
			}
		}
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWw=')
		{
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");
		}
		else if( lnk == 'c3RhY2tfcW50eQ==')
		{
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
		else if( lnk == 'c3RhY2tfdmFsdWU=')
		{
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
		else if( lnk == 'Y29tcGFueV9rcGk=')
		{
			var data='';
			var page_link='forecast_popup.php?action=opendate_popup&data='+data;
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
		else if( lnk == 'Y29tcGFueV9rcGlfd292ZW4=')
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
		else if( lnk == 'dGV4dGlsZV9ncmFwaA==')
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
		else if( lnk == 'Z2FybWVudHNfZ3JhcGg=')
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
		else if( lnk == 'dG90YWxfYWN0aXZpdGllc19hdXRvX21haWw=')
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
		else if( lnk == 'ZmFicmljX29yZGVyX2FuYWx5c2lz')
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
		else if( lnk == 'c2FsZXNfZm9yZWNhc3RfcW50eQ==')
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
		else if( lnk == 'c2FsZXNfZm9yZWNhc3RfdmFsdWU=')
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
		else if( lnk == 'ZGFpbHlfZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg==')
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
			}

		}
		else if( lnk == 'eWFybl9zdG9ja19ncmFwaA==')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var action='report_generate';
				var hompage_flag=1;
				window.open('yarn_stock_graph.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "PP Sample Approval Pending");
			}
		}
		else if( lnk == 'eWFybl9jb25zdW1wdGlvbl9ncmFw')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var action='report_generate';
				var hompage_flag=1;
				window.open('yarn_consumption_grap.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "PP Sample Approval Pending");
			}
		}
		else if( lnk == 'bW9udGhseV9vcmRlcl9xdHlfdnNfc2V3aW5nX2JhbGFuY2VfcXR5')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				window.open('home_graph/dashborad/monthly_order_qty_vs_sewing_balance_qty.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
			}

		}
		
		else if( lnk == 'ZHllaW5nX2NhcGFjaXR5X3ZzX2xvYWQ=')
		{
			if(comp==0 || comp=="")
			{
				//alert ('Select Company Name');
				//return;
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
		else if( lnk == 'a25pdHRpbmdfY2FwYWNpdHlfdnNfbG9hZA==')
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
		else if( lnk == 'MzBfZGF5c19rbml0X2VmZl90cmVuZA==')
		{
			
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('knitting_dyeing_production_trend.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
			
		}
		else if( lnk == 'MzBfZGF5c19keWVuX2VmZl90cmVuZA==')
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('knitting_dyeing_production_trend.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
			
		}
		
		else if( lnk == 'dG9kYXlfcHJvZHVjdGlvbl9ncmFwaF93b3JraW5nX2NvbXBhbnk=')
		{
			window.open('home_graph/dashborad/today_production_graph_working_company.php?'+linkData, "MY PAGE");
		}
		
		else if( lnk == 'MzBfZGF5c19zZXduX2VmZl90cmVuZA==')
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('sewn_eff_trend.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xdHlfdGVhbV9sZWFkZXJfd2lzZQ==')
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/order_in_hand_qty_team_leader_wise.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWxfdGVhbV9sZWFkZXJfd2lzZQ==')
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/order_in_hand_val_team_leader_wise.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		
		
		else if( lnk == 'b3JkZXJfaW5faGFuZF9xdHlfd2Vla193aXNl')
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/order_in_hand_qty_week_wise.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}
		else if( lnk == 'b3JkZXJfaW5faGFuZF92YWxfd2Vla193aXNl')
		{
			if((comp==0 || comp=="") && (prod_comp==0 || prod_comp==""))
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/order_in_hand_val_week_wise.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
		}


		else if( lnk == 'MzBfZGF5c19pZGxlX2xpbmVz')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_line_rmg_worker_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX3JtZ193b3JrZXI=')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('idle_line_rmg_worker_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'MzBfZGF5c19pZGxlX2tuaXRfbWNobg==')
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
		else if( lnk == 'Y2FwYWNpdHlfc3RhdHVzX3Ntdg==')
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
		}
		else if( lnk == 'Y2FwYWNpdHlfY29tcGFyaXNvbl9pbl9ob3Vy') // capacity_comparison_in_hour
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('capacity_comparison_in_hour.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'ZmluaXNoaW5nX2NhcGFjaXR5X2FjaGlldm1lbnRfaXJvbg==') // finishing_capacity_achievment_iron
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('finishing_capacity_achievment_iron.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}
		else if( lnk == 'Y2FwYWNpdHlfYm9va2VkX3F0eQ==')  //capacity_booked_qty
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('capacity_booked_qty_graph.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
		}


		else if( lnk == 'Y2FwYWNpdHlfc2FoX3ZzX2Jvb2tlZF9zYWg=') 
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			window.open('home_graph/dashborad/capacity_sah_vs_booked_sah.php?m='+lnk+'&cp='+comp+"__"+locat, "MY PAGE");
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
		else if( lnk == 'aG91cmx5X3Byb2R1Y3Rpb25fbW9uaXRvcmluZ19yZXBvcnRz')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var data=comp+"_"+locat;
				var page_link='home_report/home_report_popup.php?action=hourly_production_monitoring_report_popup&data='+data;
				emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=300px, height=200px, center=1, resize=0, scrolling=0','home')
				emailwindow.onclose=function()
				{
					var date_from=this.contentDoc.getElementById("txt_date_from").value;
					var efficiency_per=this.contentDoc.getElementById("txt_efficiency_per").value;
					if(date_from=="")
					{
						alert ('Please Chose Production Date');
						return;
					}
					else
					{
						var action='report_generate2';
						window.open('../production/reports/requires/hourly_production_monitoring_report_controller.php?action='+action+'&cbo_company_id='+comp+'&txt_date='+date_from+'&txt_parcentage='+efficiency_per+'&cbo_no_prod_type=1&report_title=Hourly Production Monitoring Reports', "");
					}
				}				
				
			}
		}	
		
		
		else if( lnk == 'dHJpbXNfb3JkZXJfcmVjZWl2ZV9zYWxlc192YWx1ZQ==')
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				window.open('home_graph/dashborad/trims_order_receive_sales_value.php?m='+lnk+'&cp='+comp+"__"+locat+"__"+cbo_floor+"__"+prod_comp, "MY PAGE");
			}
		}
		else if( lnk =='dG5hX3JlcG9ydF9jb250cm9sbGVy') //TNA Progress Report;
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=open_tna_progress_report_date_type_search_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var txt_date_from	= this.contentDoc.getElementById("txt_date_from").value;
				var txt_date_to		= this.contentDoc.getElementById("txt_date_to").value;
				var cbo_type		= this.contentDoc.getElementById("cbo_type").value;
				
				if(txt_date_from=="" || txt_date_to=="")
				{
					alert("Please Select Date");return;
				}
				
				var data="graph=1&action=generate_report&cbo_tna_status=1&cbo_company_name="+comp+"&cbo_search_type="+cbo_type+"&cbo_shipment_status=0&cbo_shipment_status=0&cbo_order_status=1&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to;
				
				window.open('tna/report/requires/tna_progress_vs_actual_plan_controller.php?'+data, "MY PAGE");
				
			}

		}
		else if( lnk =='bW9udGhseV9maXJzdF9pbnNwZWN0aW9uX2FsdGVyX2FuZF9kYW1hZ2VfcGVyY2VudGFnZQ==') //Monthly First Inspection Alter And Damage Percentage;
		{
			if(comp==0 || comp=="" || locat==0 || locat=='')
			{
				alert ('Select Company Name and Location');
				return;
			}
			var data='';
			var page_link='home_graph/dashborad/monthly_first_inspection_alter_and_damage_percentage.php?action=opendate_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Date Selection', 'width=300px, height=180px, center=1, resize=0, scrolling=0','home')
			emailwindow.onclose=function()
			{
				var date_month=this.contentDoc.getElementById("cbo_month").value;
				var date_year=this.contentDoc.getElementById("cbo_year").value;
				if(date_month!='' && date_year!='')
				{
					window.open('monthly_first_inspection_alter_and_damage_percentage.php?m='+lnk+'&cp='+comp+"__"+locat+'&date_data='+date_month+"__"+date_year, "MY PAGE");
				}
				else
				{
					alert('Please Select Date.');
					return;
				}
			}

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
		if( lnk == 'c2hpcG1lbnRfcGVuZGluZ19yZXBvcnQ=')//c2hpcG1lbnRfcGVuZGluZ19yZXBvcnQ= //shipment_pending_report 
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			else
			{
				var page_link='home_graph/dashborad/requires/graph_index_controller.php?action=ex_factory_date_category_popup';
				emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Year Selection', 'width=300px, height=200px, center=1, resize=0, scrolling=0','home')
				emailwindow.onclose=function()
				{
					var cbo_date_type=this.contentDoc.getElementById("cbo_date_type").value;
					var txt_from_date=this.contentDoc.getElementById("txt_from_date").value;
					var txt_to_date=this.contentDoc.getElementById("txt_to_date").value;
					var action='report_generate2';
					window.open('../../../reports/management_report/merchandising_report/requires/shipment_pending_report_controller.php?action='+action+'&cbo_company_id='+comp+'&cbo_location_id='+locat+'&cbo_date_category='+cbo_date_type+'&txt_from_date='+txt_from_date+'&txt_to_date='+txt_to_date, "Shipment Pending Report");
				}
			}
		}
		
		else if( lnk =='dG5hX3JlcG9ydF9jb250cm9sbGVy') //TNA Progress Report;
		{
			if(comp==0 || comp=="")
			{
				alert ('Select Company Name');
				return;
			}
			var data=comp+"_"+locat;
			var page_link='forecast_popup.php?action=open_tna_progress_report_date_type_search_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, 'Month Range Selection', 'width=400px, height=300px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var txt_date_from	= this.contentDoc.getElementById("txt_date_from").value;
				var txt_date_to		= this.contentDoc.getElementById("txt_date_to").value;
				var cbo_type		= this.contentDoc.getElementById("cbo_type").value;
				
				if(txt_date_from=="" || txt_date_to=="")
				{
					alert("Please Select Date");return;
				}
				
				var data="graph=1&action=generate_report&cbo_tna_status=1&cbo_company_name="+comp+"&cbo_search_type="+cbo_type+"&cbo_shipment_status=0&cbo_shipment_status=0&cbo_order_status=1&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to;
				
				window.open('tna/report/requires/tna_progress_vs_actual_plan_controller.php?'+data, "MY PAGE");
				
			}

		}
		
		else if( lnk == 'Y291bnRyeV93aXNlX3NoaXBtZW50X3BlbmRpbmdfcmVwb3J0')
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
		else if( lnk == 'cXVvdGF0aW9uX3N1Ym1pc3Npb25fcGVuZGluZw==')//quotation_submission_pending //new report
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
		else if( lnk == 'YnV5ZXJfaW5xdWlyeV9zdGF0dXNfcmVwb3J0')
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
				window.open('home_report/quotation_finalization_pending_report.php?action='+action+'&cbo_company_name='+comp+'&cbo_location_id='+locat+'&hompage_flag='+hompage_flag, "Quotation Finalization Pending");
			}
		}
		else if( lnk == 'eWFybl9pc3N1ZV9wZW5kaW5n')
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
		
		
		
		else if( lnk == 'UFBfc2FtcGxlX2FwcHJvdmFsX3BlbmRpbmc=')
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


		
		else if( lnk == 'RXhfRmFjdG9yeV92c19jb21tZXJjaWFsX2FjdGl2aXRpZXM=')
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
			window.open('show_graph.php?m='+lnk+'&cp='+comp, "MY PAGE");//ZGF0YQ==
		}
			
			
	}
	
	function fn_reset_com(str){
		if(str==2)$("#cbo_prod_company_home").val(0);
		else $("#cbo_company_home").val(0);
	}
	
</script>


<div style="width:98%;">
<table width="100%"  cellpadding="0" cellspacing="0" style="margin-top:2px;">
<tr height="30px">
    <td width="80%" align="center" style="margin-top:2px; margin-left:20px; margin-right:20px; border-radius:8px; font-size:16px;" valign="top">
       <div class="gr-search-panel-lc-style">
       LC Comp : 
	   <? 
        echo create_drop_down( "cbo_company_home", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "---- All Company ---- ", $selected,"load_drop_down( 'home_graph/dashborad/today_production_graph_working_company', this.value, 'load_drop_down_location', 'sp_location' );fn_reset_com(2)" );
	   ?>
        Prod. Comp :
        <? 
        echo create_drop_down( "cbo_prod_company_home", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "---- All Company ----", $selected,"load_drop_down( 'home_graph/dashborad/today_production_graph_working_company', this.value, 'load_drop_down_location', 'sp_location');fn_reset_com(1)" );
	    ?>
        &nbsp;Location: <span id="sp_location"><? 
        echo create_drop_down( "cbo_location_home", 160, $blank_array,"", 1, "-- All --", $selected, "",0 );
        ?> </span>
        &nbsp;Floor:<span id="sp_floor" ><? 
        echo create_drop_down( "cbo_floor", 100, $blank_array,"", 1, "-- All --", $selected, "",0 );
		?> </span>
       </div> 
    </td>
    <td width="15%" align="center" rowspan="2" valign="top">
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
						 if($showNumber<11){
						?>
						<a href="##" onclick="show_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>',<? echo $k; ?>)">
							<div class="panel-<? echo rand(1,25);?> gpanel">
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

<?php
// echo $home_graph_arr[$_SESSION['logic_erp']['graph_id']];die;
?>

<tr>
	<td width="80%" height="400" valign="top">
		<?
		include('home_graph/dashborad/'.$home_graph_arr[$_SESSION['logic_erp']['graph_id']]); 
		  //echo 'home_graph/dashborad/'.$home_graph_arr[$_SESSION['logic_erp']['graph_id']];die;
		?>
    </td>
</tr>

<tr valign="top">
	 <td width="80%" colspan="2">
        <div style="margin-left:12px;" id="my_div">
        <? 
		$k=1;
        foreach( $priv_items as $mod=>$item_arr )
        {
			
			if($mod==1)
			{	
				$showNumber=1;	
				 foreach( $item_arr as $item_id=>$seq )
				 {
					 if($showNumber>=11){
					?>
					<a href="##" onclick="show_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>',<? echo $k; ?>)">
						<div class="panel-<? echo rand(1,25);?> gpanel2">
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
					<a href="##" onclick="show_report_data( '<? echo base64_encode($home_page_array[$mod][$item_id]['lnk']); ?>',<? echo $k; ?>)">
                        <div class="panel-<? echo $item_id; ?> gpanel2">
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
<? //echo base64_encode('tna_report_controller');?>

