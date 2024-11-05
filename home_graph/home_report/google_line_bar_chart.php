<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create line_bar_chart
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	05.11.2015
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
$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
?>
<html>
  <head>
  <!--  <script type="text/javascript" src="https://www.google.com/jsapi"></script>-->
    <script src="Chart.js-master/google_chart/google_chart.js"></script>
    <script src="Chart.js-master/google_chart/google_loader.js"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawVisualization);

      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
         ['Month', 'Bolivia', 'Ecuador', 'Papua New Guinea',	'Papua New Guinea', 'Average'],
         ['2004/05',  165,      938,           998, 				 998,          	614.6],
         ['2005/06',  135,      1120,          1268,  				1268,         	  382],
         ['2006/07',  157,      1167,          807, 				807,           	  723],
         ['2007/08',  139,      1110,          968, 				968,           	409.4],
         ['2008/09',  136,      691,           1026, 				1026,         	  869.6]
      ]);

    var options = {
      title : 'Monthly Coffee Production by Country',
      vAxis: {title: 'Cups'},
      hAxis: {title: 'Month'},
      seriesType: 'bars',
      series: {4: {type: 'line'}}
    };

    var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
    </script>
  </head>
  <body>
        <div align="center" style="width:100%;">
        	<div align="center" id="chart_div" style="width: 900px; height: 500px;"></div>
        </div>
  </body>
</html>
