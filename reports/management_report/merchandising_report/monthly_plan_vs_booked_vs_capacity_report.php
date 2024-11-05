<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Month Capacity VS Booked VS Capacity Order Report
Functionality	         :	
JS Functions	         :
Created by		         :	Md. Saidul Islam REZA
Creation date 	         :  27-02-2020
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 	   
QC Performed BY	         :		
QC Date			         :	
Comments		         : 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Month Capacity VS Booked VS Capacity", "../../../", "", $popup, 1);
?>	
<script>
	var permission='<? echo $permission; ?>';
 
	function fn_report_generated()
	{
		if(form_validation('cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end','Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_year_name*cbo_month*cbo_month_end*cbo_end_year_name',"../../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/monthly_plan_vs_booked_vs_capacity_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
	 		show_msg('3');
			release_freezing();
			hs_chart("["+reponse[2]+"]","["+reponse[3]+"]","["+reponse[4]+"]","["+reponse[5]+"]");
		}
	}
	
	
	
	
	
	

function hs_chart(allocatedVal,planVal,Month,capacityVal){
	
	$('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Monthly Plan VS Booked'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories:eval(Month),
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: '',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth: 0
        },
		
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Booked',
            data: eval(allocatedVal)
        }, {
            name: 'Plan',
            data: eval(planVal)
        }
		
		, {
            name: 'Capacity',
            data: eval(capacityVal)
        }
		]
    });
		
}	
	
	
	
	
	
</script>

<script src="../../../ext_resource/hschart/hschart.js"></script>
  
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 align="left" id="accordion_h1" class="accordion_h" style="width:760px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel"> 
            <form id="monthlyCapacityVSallocatedOrder_1" name="monthlyCapacityVSallocatedOrder_1">
                <div style="width:760px">
                    <fieldset>  
                        <table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
                            <thead>  
                                <th width="150">Company</th>
                                <th width="120">Location</th>
                                <th width="100" class="must_entry_caption">Start Year</th>
                                <th width="100" class="must_entry_caption">Start Month</th>
                                <th width="100" class="must_entry_caption">End Year</th>
                                <th width="100" class="must_entry_caption">End Month</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" /></th>
                            </thead>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected,"load_drop_down( 'requires/monthly_plan_vs_booked_vs_capacity_report_controller', this.value, 'load_drop_down_location', 'location_id_td' )" ); ?></td>
                                <td id="location_id_td"><? 
								echo create_drop_down( "cbo_location_id", 120,array(),"", 0, "--All-", 1,"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-Select Year-", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                                <td><? echo create_drop_down( "cbo_end_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" ); ?></td>
                                <td><input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(1);" style="width:80px" class="formbutton" /></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </form>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>