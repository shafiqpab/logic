<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create amchart_line_bar
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	13.10.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
require_once('includes/common.php');

echo load_html_head_contents("Line Bar Chart", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];


if($m=="test_line_bar_chart")
{
	$caption="Line Bar Chart";
	$header="test line bar chart";
}
else
{
	$caption="Line Bar Chart";
	$header="test line bar chart";
}

if($company!=0)
{
	$company=$company;
}
else
{
	$company="";
}
$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");

?>
	<script>
		var lnk='<? echo $m; ?>';
		var caption='<? echo $caption; ?>';
	</script>
    
    
    <div align="center" style="width:100%;">
    	<div align="center" style="width:100%; font-size:16px;"><? echo $caption; ?></div>
        <div align="center" style="width:100%; font-size:14px;"><? echo " Company : ". $comp_arr[$company]; ?></div>
    	<div id="chartdiv" style="width:95%; height:600px;"></div>	
    </div>
    
	<script src="Chart.js-master/amcharts/amcharts.js"></script>
    <script src="Chart.js-master/amcharts/serial.js"></script>
    <script src="Chart.js-master/amcharts/light.js"></script>
    <script src="Chart.js-master/amcharts/dataloader.min.js"></script>

    <script>
		
		var chart = AmCharts.makeChart( "chartdiv", {
		"type": "serial",
		"dataLoader": {
		"url": "chartdata.php",
		"format": "json"
		},
		"creditsPosition": "top-right",
		"categoryField": "Date",
		"categoryAxis": {
		"gridAlpha": 0.07,
		"gridPosition": "start",
		"tickPosition": "start",
		"title": "Date"
		},
		"valueAxes": [ {
		"id": "v1",
		"gridAlpha": 0.07,
		"title": "Users/Sessions"
		}, {
		"id": "v2",
		"gridAlpha": 0,
		"position": "right",
		"title": "Page views"
		} ],
		"graphs": [ {
		"type": "column",
		"title": "Sessions",
		"valueField": "sessions",
		"lineAlpha": 0,
		"fillAlphas": 0.6
		}, {
		"type": "column",
		"title": "Users",
		"valueField": "users",
		"lineAlpha": 0,
		"fillAlphas": 0.6
		}, {
		"type": "line",
		"valueAxis": "v2",
		"title": "Page views",
		"valueField": "pageviews",
		"lineThickness": 2,
		"bullet": "round"
		} ],
		"legend": {}
		} );
	</script>
