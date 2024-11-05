<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create line_bar_chart
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

//echo load_html_head_contents("30 Days Idle knit days mchn Graph", "", "", $popup, $unicode, $multi_select, 1);
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];


if($m=="test_line_bar_chart")
{
	$caption="test line bar chart";
	$header="test line bar chart";
}
else
{
	$caption="test line bar chart";	
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

//$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");

?>
	<script>
		var lnk='<? echo $m; ?>';
	</script>
    
    <style type="text/css">
		#chartdiv {#chartdiv {
		width		: 100%;
		height		: 500px;
		font-size	: 11px;
		}	
		width		: 100%;
		height		: 500px;
		font-size	: 11px;
		}	
		/*.amcharts-graph-graph2 .amcharts-graph-stroke {
		stroke-dasharray: 4px 5px;
		stroke-linejoin: round;
		stroke-linecap: round;
		-webkit-animation: am-moving-dashes 1s linear infinite;
		animation: am-moving-dashes 1s linear infinite;
		}
		
		@-webkit-keyframes am-moving-dashes {
		100% {
		stroke-dashoffset: -28px;
		}
		}
		@keyframes am-moving-dashes {
		100% {
		stroke-dashoffset: -28px;
		}
		}*/
	</style>
    <div align="center" style="width:100%;">
    	<div id="chartdiv" style="width: 900px; height: 500px;"></div>	
    </div>
    
	<script src="Chart.js-master/amcharts/amcharts.js"></script>
    <script src="Chart.js-master/amcharts/serial.js"></script>
    <script src="Chart.js-master/amcharts/light.js"></script>

    <script>
	var chart = AmCharts.makeChart("chartdiv", {
		  "type": "serial",
		  "addClassNames": true,
		  "theme": "light",
		  "autoMargins": false,
		  "marginLeft": 30,
		  "marginRight": 8,
		  "marginTop": 10,
		  "marginBottom": 26,
		  "balloon": {
			"adjustBorderColor": false,
			"horizontalPadding": 10,
			"verticalPadding": 8,
			"color": "#008000"
		  },
		  "dataProvider": [{
			"year": 2009,
			"income": 23.5,
			"expenses": 21.1
		  }, {
			"year": 2010,
			"income": 26.2,
			"expenses": 30.5
		  }, {
			"year": 2011,
			"income": 30.1,
			"expenses": 34.9
		  }, {
			"year": 2012,
			"income": 29.5,
			"expenses": 31.1
		  }, {
			"year": 2013,
			"income": 30.6,
			"expenses": 28.2,
		  }, {
			"year": 2014,
			"income": 34.1,
			"expenses": 32.9
		  }],
		  "valueAxes": [{
			"axisAlpha": 0,
			"position": "left"
		  }],
		  "startDuration": 1,
		  "graphs": [{
			"alphaField": "alpha",
			"balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
			"fillAlphas": 1,
			"title": "Income",
			"type": "column",
			"valueField": "income",
			"dashLengthField": "dashLengthColumn"
		  }, {
			"id": "graph2",
			"balloonText": "<span style='font-size:12px;'>[[title]] in [[category]]:<br><span style='font-size:20px;'>[[value]]</span> [[additional]]</span>",
			"bullet": "round",
			"lineThickness": 3,
			"bulletSize": 7,
			"bulletBorderAlpha": 1,
			"bulletColor": "#008000",
			"useLineColorForBulletBorder": true,
			"bulletBorderThickness": 3,
			"fillAlphas": 0,
			"lineAlpha": 1,
			"title": "Expenses",
			"valueField": "expenses"
		  }],
		  "categoryField": "year",
			"categoryAxis": {
			"gridPosition": "start",
			"axisAlpha": 0,
			"tickLength": 0
			},
		  "export": {
			"enabled": true
		  }
		});
</script>
