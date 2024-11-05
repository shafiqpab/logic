<!-- Styles -->
<style>
#chartdiv {
	width		: 1000px;
	height		: 400px;
	font-size	: 11px;
}					
</style>

<!-- Resources -->

<script src="Chart.js-master/amcharts/amcharts.js"></script>
<script src="Chart.js-master/amcharts/serial.js"></script>
<script src="Chart.js-master/amcharts/plugins/export.min.js"></script>
<link rel="stylesheet" href="Chart.js-master/amcharts/plugins/export.css" type="text/css" media="all" />
<script src="Chart.js-master/amcharts/themes/light.js"></script>
<?php 
$data_array=array();

$data_array["USA"]=1025;
$data_array["China"]=982;
$data_array["Japan"]=409;
$data_array["Germany"]=822;
$data_array["UK"]=522;
$data_array["France"]=814;
$data_array["India"]=984;
$data_array["Spain"]=711;
$data_array["Russia"]=522;
$data_array["South Korea"]=814;
$data_array["Brazil"]=984;
$data_array["Canada"]=711;
$data_array["Netherlands"]=1245;
$chart_data='[';
foreach($data_array as $country=>$value)
{
	$chart_data.="{country: '".$country."',visits: $value},";
}
$chart_data=rtrim($chart_data,',');
$chart_data.=']';

 ?>
<!-- Chart code -->
<script>
//var chart_data=< ?= $chart_data; ?>;

var chart = AmCharts.makeChart( "chartdiv", {
  "type": "serial",
  "theme": "light",
  "dataProvider":[{country: 'USA',visits: 1025},{country: 'BD',visits: 2333}] ,
  "valueAxes": [ {
    "gridColor": "#FFFFFF",
    "gridAlpha": 0.2,
    "dashLength": 0
  } ],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [ {
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "visits"
  } ],
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "country",
  "categoryAxis": {
    "gridPosition": "start",
    "gridAlpha": 0,
    "tickPosition": "start",
    "tickLength": 20
  },
  "export": {
    "enabled": true
  }

} );
</script>

<!-- HTML -->
<div id="chartdiv"></div>	