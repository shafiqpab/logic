//Do not change 
function createObject() {
	var request_type;
	var browser = navigator.appName;
	if( browser == "Microsoft Internet Explorer") {
		request_type = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else request_type = new XMLHttpRequest();
	
	return request_type;
}
var http = createObject();
 
 
 function generate_login_history_report()
	{
		$("#messagebox").removeClass('messagebox').addClass('messagebox_ok').text('Generating Report, Please wait....').fadeIn(1000);
		
		var cbo_user_name		= escape(document.getElementById('cbo_user_name').value);
		var cbo_search_by		=escape(document.getElementById('cbo_search_by').value);
		var search_value		=escape(document.getElementById('search_value').value);
		var txt_date_from		= escape(document.getElementById('txt_date_from').value);
		var txt_date_to		= escape(document.getElementById('txt_date_to').value);
		 
		if($('#txt_date_from').val()==""){						
			$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
				$('#cbo_company_mst').focus();
				$(this).html('Please Select a Date Range.').removeClass('messagebox_ok').addClass('messageboxerror').fadeTo(900,1);
			});		
		}
		else if($('#txt_date_from').val()>txt_date_to){						
			$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
				$('#txt_date_to').focus();
				$(this).html('Please check a Date Range.').removeClass('messagebox_ok').addClass('messageboxerror').fadeTo(900,1);
			});		
		}
		else
		{	
			if (txt_date_to=="")
			{
				document.getElementById('txt_date_to').value=txt_date_from;
				txt_date_to=txt_date_from;
			}
			nocache = Math.random();
			http.open('get','generate_repots.php?action=login_history_report'+
						'&cbo_user_name='+cbo_user_name+
						'&cbo_search_by='+cbo_search_by+
						'&search_value='+search_value+
						'&txt_date_from='+txt_date_from+
						'&txt_date_to='+txt_date_to+
						'&nocache ='+nocache);
			http.onreadystatechange = generate_login_history_report_reply;
			http.send(null);
		}	
	}
	
function generate_login_history_report_reply() 
{
	if(http.readyState == 4)
	{
		var response =  http.responseText.split('####');
		 
		 document.getElementById('report_container').innerHTML="";
		 document.getElementById('report_container').innerHTML=response[0];
			
		if (response[1]==2)
		{
			$("#messagebox").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('Report Generated Succesfully.').removeClass('messagebox_ok').addClass('messagebox_ok').fadeTo(900,1);
				 
				
			});
		}
		
	}
}	
	function generate_activities_history_report()
	{
		$("#messagebox").removeClass('messagebox').addClass('messagebox_ok').text('Generating Report, Please wait....').fadeIn(1000);
		
		var cbo_user_name		= escape(document.getElementById('cbo_user_name').value);
		var cbo_search_by		=escape(document.getElementById('cbo_search_by').value);
		 
		var txt_date_from		= escape(document.getElementById('txt_date_from').value);
		var txt_date_to		= escape(document.getElementById('txt_date_to').value);
		var cbo_mdule_name		= escape(document.getElementById('cbo_mdule_name').value);
		var cbo_menu_name		= escape(document.getElementById('cbo_menu_name').value);
		
		if($('#cbo_user_name').val()==0){						
			$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
				$('#cbo_user_name').focus();
				$(this).html('Please Select an User.').removeClass('messagebox_ok').addClass('messageboxerror').fadeTo(900,1);
			});		
		}
		else if($('#cbo_mdule_name').val()==0){						
			$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
				$('#cbo_mdule_name').focus();
				$(this).html('Please Select a Module.').removeClass('messagebox_ok').addClass('messageboxerror').fadeTo(900,1);
			});		
		} 
		else if($('#txt_date_from').val()==""){						
			$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
				$('#cbo_company_mst').focus();
				$(this).html('Please Select a Date Range.').removeClass('messagebox_ok').addClass('messageboxerror').fadeTo(900,1);
			});		
		}
		else if($('#txt_date_from').val()>txt_date_to){						
			$("#messagebox").fadeTo(200,0.1,function(){  //start fading the messagebox
				$('#txt_date_to').focus();
				$(this).html('Please check a Date Range.').removeClass('messagebox_ok').addClass('messageboxerror').fadeTo(900,1);
			});		
		}
		else
		{	
			if (txt_date_to=="")
			{
				document.getElementById('txt_date_to').value=txt_date_from;
				txt_date_to=txt_date_from;
			}
			nocache = Math.random();
			http.open('get','generate_repots.php?action=activities_history_report'+
						'&cbo_user_name='+cbo_user_name+
						'&cbo_mdule_name='+cbo_mdule_name+
						'&cbo_menu_name='+cbo_menu_name+
						'&cbo_search_by='+cbo_search_by+
						'&txt_date_from='+txt_date_from+
						'&txt_date_to='+txt_date_to+
						'&nocache ='+nocache);
			http.onreadystatechange = generate_activities_history_report_reply;
			http.send(null);
		}	
	}
	
function generate_activities_history_report_reply() 
{
	if(http.readyState == 4)
	{
		var response =  http.responseText.split('####');
		 
		 document.getElementById('report_container').innerHTML="";
		 document.getElementById('report_container').innerHTML=response[0];
			
		if (response[1]==2)
		{
			$("#messagebox").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
				$(this).html('Report Generated Succesfully.').removeClass('messagebox_ok').addClass('messagebox_ok').fadeTo(900,1);
				 
				
			});
		}
		
	}
}	