<? 
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Graph", "", "", 1, $unicode, $multi_select, '');
?>
<link rel="stylesheet" href="home_css/styles.css">

<script>
	var permission = '<? echo $permission; ?>';
	//alert (permission);
	var comp="";
	var locat="";
	var lnk="";
		
</script>
<?
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	
	
	$currYear=date('Y',time());
	$prevYear_1=$currYear-1;
	$prevYear_2=$currYear-2;
	
?>
<table width="100%">	
    <tr><td align="center"><h1><? echo $company_library[$cbo_company_name];?></h1></td></tr>
</table>

<?	
	
	
	if($db_type==0) 
	{
		$where_con=" and (a.receive_date like '".$currYear."%"."' or a.issue_date like '".$prevYear_1."%"."' or a.issue_date like '".$prevYear_2."%"."') ";
		$yearFiel=" year(a.receive_date) as year";
	}
	else
	{
		$where_con=" and (to_char(a.receive_date,'YYYY') like '".$currYear."%"."' or to_char(a.receive_date,'YYYY') like '".$prevYear_1."%"."' or to_char(a.receive_date,'YYYY') like '".$prevYear_2."%"."') ";
		$yearFiel=" to_char(a.receive_date,'YYYY') as year";
	}
	
$sql="select $yearFiel, b.cons_quantity as rec_qnty, b.return_qnty, c.yarn_count_id,d.yarn_count from inv_receive_master a, inv_transaction b, product_details_master c,lib_yarn_count d where a.item_category=1 and a.entry_form=1 and a.company_id=$cbo_company_name and c.yarn_count_id=d.id $where_con and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by d.yarn_count desc";	
	
$result=sql_select($sql);
foreach($result as $rows){
	$dataArr[$rows[csf('yarn_count')]][$rows[csf('year')]]+=($rows[csf('rec_qnty')]-$rows[csf('return_qnty')]);
}
unset($result);

$categories="[";
$currYearData="{ name: $currYear, data:[";
$currYear_1Data="{ name: $prevYear_1, data:[";
$currYear_2Data="{ name: $prevYear_2, data:[";
$htmlTH="<thead><th>Year</th>";
$htmlTR="<tr bgcolor='#90ee7e'><td align='center'>$currYear</td>";
$htmlTR_1="<tr bgcolor='#f7a35c'><td align='center'>$prevYear_1</td>";
$htmlTR_2="<tr bgcolor='#F63D7C'><td align='center'>$prevYear_2</td>";
$i=0;
foreach($dataArr as $yarnCount=>$yarnCountArr){
	if($categories=="["){$categories.="'".$yarnCount."'";}
	else{$categories.=",'".$yarnCount."'";}
	$htmlTH.="<th>$yarnCount</th>";
	$htmlTR.="<td align='right'>".$dataArr[$yarnCount][$currYear]."</td>";
	$htmlTR_1.="<td align='right'>".$dataArr[$yarnCount][$prevYear_1]."</td>";
	$htmlTR_2.="<td align='right'>".$dataArr[$yarnCount][$prevYear_2]."</td>";
	$dataArr[$yarnCount][$currYear];
	$dataArr[$yarnCount][$prevYear_1];
	$dataArr[$yarnCount][$prevYear_2];
	
	if($i==0){
		$currYearData.=$dataArr[$yarnCount][$currYear]*1;
		$currYear_1Data.=$dataArr[$yarnCount][$prevYear_1]*1;
		$currYear_2Data.=$dataArr[$yarnCount][$prevYear_2]*1;
	}
	else
	{
		$currYearData.=','.$dataArr[$yarnCount][$currYear]*1;
		$currYear_1Data.=','.$dataArr[$yarnCount][$prevYear_1]*1;
		$currYear_2Data.=','.$dataArr[$yarnCount][$prevYear_2]*1;
	}
	
	
$i++;	
}
$categories.="]";

$currYearData.="], stack: 'none'}";
$currYear_1Data.="], stack: 'none'}";
$currYear_2Data.="], stack: 'none'}";
$data='['.$currYear_2Data.','.$currYear_1Data.','.$currYearData.']';
$htmlTH.="</thead>";
$htmlTR.="</tr>";
$htmlTR_1.="</tr>";
$htmlTR_2.="</tr>";
$table = "<table class='rpt_table' border='1' rules='all'> $htmlTH $htmlTR $htmlTR_1 $htmlTR_2 </table>";

	
?>

<div id="chartdiv" style="width:100%; height:445px; background-color:#FFFFFF"></div>


<script src="ext_resource/hschart/hschart.js"></script>

<script>
	
Highcharts.theme = {
   colors: ["#F63D7C", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7cb5ec", "#aaeeee"],
   chart: {
      backgroundColor: null, //null
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '12px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '12px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
	  
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
	  
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },


   // General
   background2: '#FF0000'
   
};
// Apply the theme
Highcharts.setOptions(Highcharts.theme);

	
	window.onload = function()
	{
		hs_homegraph(1);
		
	}
	
	function hs_homegraph( gtype ) 
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
			var uom=" USD";

		$('#chartdiv').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: ' Yarn Consumption '
			},
	
			xAxis: {
				categories: <? echo $categories;?>
			},
	
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: 'Total Values'
				}
			},
	
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b> ' +
						 ': ' + this.y + ' KG <br/>' ;
						//+ 'Total: ' + this.point.stackTotal;  this.series.name + ': ' + this.y + uom +'<br/>' ;
				}
			},
	
			plotOptions: {
				column: {
					stacking: false //'normal'
				}
			},
		
			series: <? echo $data;?>
		});
		
		
	}
	
</script>
<script src="includes/functions_bottom.js" type="text/javascript"></script>
<script>

$(document).ready(function(){
    $('#my_div div').each(function(graph_grp) {
	  	$(this).attr('onMouseOver',"hover_effect(this)");
		$(this).attr('onMouseOut',"mouseout_effect(this)");
	});
});
 
function hover_effect( divclass )
{
	//alert ('running development');
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").css( "-webkit-transform"," scale(1.3)" );
   	$("."+cls[0]+" img").css('transform', 'scale(1.3)'); 
}

function mouseout_effect( divclass )
{
	var cls= $(divclass).attr('class').split(" ");
   	$("."+cls[0]+" img").removeAttr( 'style' );
   	//$("."+cls).removeAttr( 'style' );
}


</script>

<div style="margin:0 5px;"><?php echo $table; ?></div>