<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric & Order Analysis Graph.
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	14.03.2016	
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('includes/common.php');

echo load_html_head_contents("Order Forecasting Graph", "", "", $popup, $unicode, $multi_select, 1);
 
extract( $_REQUEST );
$m= base64_decode($m);
list($company,$location,$cbo_buyer_name, $cbo_constraction, $cbo_composition, $cbo_gsm, $cbo_start_month, $cbo_end_month, $cbo_year, $txt_previous_period, $cbo_value)=explode("_",$data);
//echo $m;die;

if($m=="fabric_order_analysis")
{
	$caption="Fabric & Order Analysis";	
}


if($company!=0)
{
	$company=$company;
}
else
{
	$company="";
	$caption="Please Select Company Name";
}

if($location!="") $location_cond= "and a.location_name=$location "; else $location_cond="";

$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
$buyer_name_arr=return_library_array("select id,buyer_name from lib_buyer", "id","buyer_name");

$process = array( &$_POST );
extract(check_magic_quote_gpc( $process ));
//--------------------------------------------------------------------------------------------------------------------
?>
	<script>
    	var lnk='<? echo $m; ?>';
    </script>
   
   
<?

$sql="select po_break_down_id,job_no_mst from wo_po_color_size_breakdown where country_ship_date  ";




?>   
   
   
   
   
   <!-- <script src="Chart.js-master/Chart.js"></script>-->



		<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
		<style type="text/css">
${demo.css}
		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Historic World Population by Region'
        },
        subtitle: {
            text: 'Source:Wikipedia.org'
        },
        xAxis: {
            categories: ['Africa', 'America', 'Asia', 'Europe', 'Oceania'],
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Population (millions)',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' millions'
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Year 1800',
            data: [107, 31, 635, 203, 2]
        }, {
            name: 'Year 1900',
            data: [133, 156, 947, 408, 6]
        }, {
            name: 'Year 2012',
            data: [1052, 954, 4250, 740, 38]
        }]
    });
});
		</script>
<!--<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>-->
<script src="ext_resource/hschart/hschart.js"></script>
<div id="container" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto"></div>


        
     