<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Efficiency Report V2
Functionality   :   
JS Functions    :
Created by      :  Kausar 
Creation date   :  08-01-2023
Updated by      :       
Update date     :    
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Efficiency Report V2", "../../", 1, 1,$unicode,1,1);

?>  
<script src="../../js/highchart/highcharts.js"></script>
<script src="../../js/highchart/highcharts-3d.js"></script>
<script src="../../js/highchart/exporting.js"></script>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
	var tableFilters = 
        {
            col_0: "none", 
        } 
                
	var tableFilters1 = 
        {
            col_0: "none", 
        } 
                    
	function fn_report_generated(type)
	{
		if(type==0)
		{
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Working Company*From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_name','Working Company')==false)
			{
				return;
			}
		}

		freeze_window(3);
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*txt_date_from*txt_date_to',"../../")+'&type='+type;
		
		http.open("POST","requires/efficiency_v2_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			show_msg('3'); 
			var reponse=trim(http.responseText).split("####"); 
			if(reponse[2]=="show_chart")
			{
				// alert(reponse[3]+reponse[4]);
				//showChart(reponse[3],reponse[4]);
			}
			$('#report_container2').html(reponse[0]);
			// alert(reponse[2]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow='auto';
		document.getElementById('scroll_body').style.maxHeight='none'; 
		$("#table_body tr:first").hide();
		$("#table_body1 tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
		d.close();
		
		document.getElementById('scroll_body').style.overflowY='scroll';
		document.getElementById('scroll_body').style.maxHeight='425px';
		$("#table_body tr:first").show();
	}    
            
	function showChart(floor_name,floor_total) 
	{
		// $("#chart_container").show('fast');
		var floor_name_arr = floor_name.split(',');
		// var floor_name_arr = floor_name_arr.toString();
		var floor_total_arr = floor_total.split(',');
		var floor_total_arr = floor_total_arr.map(Number);
		// alert(value);
		
		Highcharts.chart('chart_container', {
			chart: {
				type: 'column',
				options3d: {
					enabled: true,
					alpha: 5,
					beta: 5,
					depth: 70
				}
			},
			title: 
			{
				text: 'Floor Wise Sewing WIP Report Chart',
				style:
					{
						color: 'black',
						fontSize: '22px',
						fontWeight: 'bold'
					}
			},
			subtitle: 
			{
				useHTML: true,
				align: 'center',
				y: 40,
				text: '<b>Date : '+$("#txt_date").val()+'</b>' 
			},
			plotOptions: 
			{
				column: 
				{
					depth: 25
				},
				series: 
				{
					dataLabels: 
					{
						align: 'center',
						enabled: true
					}
				}
			},
			xAxis: 
			{
				categories: floor_name_arr,
				labels: 
				{
					skew3d: true,
					style: 
					{
						fontSize: '14px',
						color: 'black',
						fontWeight: 'bold'
					}
				},
			},
			yAxis: 
			{
				title: 
				{
					text: 'Floor Wise Total Quantity',
					style:
					{
						color: 'black',
						fontSize: '14px',
						fontWeight: 'bold'
					}
				}
			},
			credits: 
			{
				enabled: false
			},
			series: [{
				name: ['Floor Wise Total'],
				data: floor_total_arr
			}]
		});
	}   

	function getCompanyId() 
	{
		var company_id = document.getElementById('cbo_company_name').value;
		if(company_id !='') {
			var data="action=load_drop_down_location&company_id="+company_id;
			http.open("POST","requires/efficiency_v2_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data); 
			http.onreadystatechange = function(){
				if(http.readyState == 4) 
				{
					var response = trim(http.responseText);
					//$('#location_td').html(response);
					$('#location_td').html(response);
					set_multiselect('cbo_location','0','0','','0');
					setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location,'0'); getLocationId();") ,3000)];
				}          
			};
		}     
	}
	
	function getLocationId() 
	{
		var company_id = document.getElementById('cbo_company_name').value;
		var location_id = document.getElementById('cbo_location').value;
		var floor_id = document.getElementById('cbo_floor').value;
		//var search_type = document.getElementById('cbo_search_by').value;
		if(location_id !='') {
		  var data="action=load_drop_down_floor&location_id="+location_id;
		  http.open("POST","requires/efficiency_v2_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
			  if(http.readyState == 4) 
			  {
				  var response = trim(http.responseText);
				  $('#floor_td').html(response);
				  set_multiselect('cbo_floor','0','0','','0');
				 // setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0'); getLocationId();") ,3000)];
			  }          
		  };
		}     
	}

	function show_details(search_string)
	{		

		var page_link='requires/efficiency_v2_report_controller.php?action=details_popup&search_string='+search_string;
		var title='Line Wise Details';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			
		}
	}

</script>
  <style type="text/css">
    #chart_container {
      height: 400px;
      min-width: 400px;
      max-width: 800px;
      margin: 0 auto;
    }
 </style>
</head>
<body onLoad="set_hotkey();">
<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <fieldset style="width:750px;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="750px" cellpadding="0" cellspacing="0" border="1" align="center" rules="all">
               <thead>                    
                    <tr>
                        <th width="150" class="must_entry_caption">Working Company</th>
                        <th width="150">Location</th>
                        <th width="150">Floor</th>
                        <th colspan="2" class="must_entry_caption">Prod. Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:120px" value="Reset" /></th>
                    </tr>    
                 </thead>
                 <tbody>
                     <tr class="general">
                        <td id="td_company"><?=create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" ); //load_drop_down( 'requires/efficiency_v2_report_controller', this.value, 'load_drop_down_location', 'location_td' );?></td>                   
                        <td id="location_td"><?=create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select --", $selected, " ", 1, "" ); //load_drop_down( 'requires/efficiency_v2_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );?></td>
                        
                        <td id="floor_td"><?=create_drop_down( "cbo_floor", 150, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td> 
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" ></td>   
                        <td><input type="button" id="show_button" class="formbutton" style="width:55px" value="Show" onClick="fn_report_generated(0);" /><input type="button" id="show_button" class="formbutton" style="width:60px" value="Summary" onClick="fn_report_generated(1);" title="Date range will not apply this button. Allways use current date" /></td>
                    </tr>
                    <tr>
                    	<td colspan="6" align="center"><?=load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div id="chart_container"></div>
 </form>    
</body>
<script>
    set_multiselect('cbo_company_name','0','0','','0');
	set_multiselect('cbo_location','0','0','','0');
	set_multiselect('cbo_floor','0','0','','0');
	
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
