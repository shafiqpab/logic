<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly Guage Wise Inspection Report
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	01-09-2021
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
echo load_html_head_contents("Monthly Guage Wise Inspection Report","../../../", 1, 1, $unicode,1); 
?>	  
<script src="../../../js/highchart/highcharts.js"></script>
<script src="../../../js/highchart/highcharts-3d.js"></script>
<script src="../../../js/highchart/exporting.js"></script>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
		{
			/*col_operation: {
				id: ["value_order_qty"],
				col: [6],
				operation: ["sum"], 
				write_method: ["innerHTML"]
			}*/	
		}
		var tableFilters_1 = 
		{
			/*col_operation: {
				id: ["value_order_qty_1","value_linking_rec_qty_1"],
				col: [6,8],
				operation: ["sum","sum"], 
				write_method: ["innerHTML","innerHTML"]
			}*/	
		}

	function generate_report(type)
	{		
		if( form_validation('cbo_company_id*cbo_start_year*cbo_start_month*cbo_end_year*cbo_end_month','Company Name*Start Year*Start Month*End Year*End Month')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_start_year*cbo_start_month*cbo_end_year*cbo_end_month',"../../../")+'&report_title='+report_title+'&type='+type;		
		
		// alert(data); return;
		freeze_window(3);
		http.open("POST","requires/monthly_guage_wise_inspection_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split("####");

				// alert(reponse[2]);				

				$("#report_container2").html(reponse[0]); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

                $("#chart_container").html('');

                if(reponse[2]=='1')
                {
                    showChart(reponse[3], reponse[4], reponse[5]);
                }
				/*if(reponse[2]=='1')
				{
					setFilterGrid("table_body",-1,tableFilters);
				}*/
				if(reponse[2]=='2')
				{
					setFilterGrid("table_body_1",-1,tableFilters_1);
				}
								
				show_msg('3');
				release_freezing();
			}
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
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}
	
	

    function showChart(styleName, defectQty, rejectQty) 
    {
        // $("#chart_container").show('fast');
        var style_name_arr = styleName.split('__');
        // var style_name_arr = style_name_arr.toString();
        var defect_total_arr = defectQty.split('__');
        var defect_total_arr = defect_total_arr.map(Number);

        var reject_total_arr = rejectQty.split('__');
        var reject_total_arr = reject_total_arr.map(Number);
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
                text: 'Monthly Guage Wise Inspection Report Chart',
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
                text: '<b>Monthly Alter% And Defect%</b>' 
            },
            plotOptions: 
            {
                column: 
                {
                    depth: 25,
                },
                series: 
                {
                    dataLabels: 
                    {
                        align: 'center',
                        enabled: true,
                    }
                }
            },
            xAxis: 
            {
                categories: style_name_arr,
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
                    text: 'Monthly Alter% And Defect%',
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
            series: [
    	        {
    	            name: ['Defect%'],
    	            data: defect_total_arr,
    	            color: '#3DB2FF'
    	        },
    	        {
    	            name: ['Reject%'],
    	            data: reject_total_arr,
    	            color: '#FF2442'
            	}
            ]
        });

    } 
</script>

  <style type="text/css">
    #chart_container {
      max-height: 400px;
      min-width: 400px;
      max-width: 960px;
      margin: 0 auto;
    }
 </style>
</head>
<body onLoad="set_hotkey();">
<form id="bundleTrackReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:700px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:700px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="120" class="must_entry_caption">Working Company</th>
                    <th width="100" class="must_entry_caption">Start Year</th>
                    <th width="100" class="must_entry_caption">Start Month</th>
                    <th width="100" class="must_entry_caption">End Year</th>
                    <th width="100" class="must_entry_caption">End Month</th>
                    <th width="145">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form( 'bundleTrackReport_1', 'report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                        	<? echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "" ); ?>
                        		
                        </td> 
                        <td>
                            <? echo create_drop_down( "cbo_start_year", 100,$year,"id,year", 1, "-Select Year-", date('Y'),"" ); ?>
                                
                        </td>
                        <td><? echo create_drop_down( "cbo_start_month", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                        <td><? echo create_drop_down( "cbo_end_year", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_end_month", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
						
                        <td>
                        	<input type="button" name="search1" id="search1" value="Guage Wise" onClick="generate_report(1);" style="width:70px" class="formbutton" />
                        	<input type="button" name="search1" id="search1" value="Yearly" onClick="generate_report(2);" style="width:70px" class="formbutton" />
						
						</td>
                    </tr>
                    
                </tbody>
            </table>
			<table>

            </table> 
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding: 5px 0;"></div>
    <div id="report_container2" align="left"></div>    
    <div id="chart_container"></div>
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
