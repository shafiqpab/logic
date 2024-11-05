 <? 
session_start();
include('../../includes/common.php');
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);


?>
 
 
<div id="chartdiv" style="width:1100px; height:400px; background-color:#FFFFFF">zzzz</div>
   
<script src="../ext_resource/hschart/hschart.js"></script>

<script>




Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null, //null
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
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
         fontSize: '13px'
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
   background2: '#FF0000'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);


			var msg="Total Values"
			var uom=" USD";
		
		
$('#chartdiv').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: 'Graphp Titile'
			},
			xAxis: {
				categories:['May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr']
			},
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b> ' +
						 ': ' + this.y + uom +'<br/>' ;
						//+ 'Total: ' + this.point.stackTotal;  this.series.name + ': ' + this.y + uom +'<br/>' ;
				}
			},
			plotOptions: {
				column: {
					stacking: false //'normal'
				}
			},
			series: [
				{ name: 'FAL', data:[7613632,8992730,2752938,8279632,1855679,57000,15400,0,951800,180000,0,0], stack: 'none'},
				{ name: 'OG', data:[1191805,7290312,3811165,4825491,899466,762919,57265,480000,180000,0,0,0], stack: 'none'}, 
				{ name: 'UG', data:[103740,6832336,181000,804602,602860,1026240,26460000,7923342,31270250,0,0,0], stack: 'none'}
			]
		});
		
		
	
	

	</script>