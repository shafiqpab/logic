<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Gmts Reject Alter Percentage Graph
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	06.01.2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
require_once('includes/common.php');

echo load_html_head_contents("Gmts Reject Alter Percentage", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];
$floor=$cps[2];
$pro_company=$cps[3];

	if($m=="capacity_status_smv")
	{
		$caption="Garment Alter And Reject Comparison With Standard";
	}
	else
	{
		$caption="Garment Alter And Reject Comparison With Standard";
	}
	
	if($company!=0){ $company=$company; } else { $company=""; }
	if($location!=0){ $location=$location; } else { $location=""; }

	$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
	$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	$month_data="[";	
	$month_prev=add_month(date("Y-m-d",time()),-3);
	$month_next=add_month(date("Y-m-d",time()),8);
	$start_yr=date("Y",strtotime($month_prev));
	$end_yr=date("Y",strtotime($month_next));
	for($e=0;$e<=11;$e++)
	{
		$tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
		$yr_mon_part[$e]=date("Y-m",strtotime($tmp));
		$month_array[$e]=date("M",strtotime($tmp))." '".date("y",strtotime($tmp));
		if($e!=11) $month_data .="'".date("M",strtotime($tmp))." ".date("y",strtotime($tmp))."',"; else $month_data .="'".date("M",strtotime($tmp))." ".date("y",strtotime($tmp))."']";
	}
	//echo $month_data;
	
	if($db_type==0)
	{
		$country_ship_date_fld="a.country_ship_date";
		$production_date_con="production_date";
		$ex_factory_date_con="b.ex_factory_date";
		
		$ex_factory_date_con_2="a.ex_factory_date";
	}
	else
	{
		$country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
		$production_date_con="to_char(production_date,'YYYY-MM-DD')";
		$ex_factory_date_con="to_char(b.ex_factory_date,'YYYY-MM-DD')";
		$ex_factory_date_con_2="to_char(a.ex_factory_date,'YYYY-MM-DD')";
	}
	
	if($location!="") $location_cond= "and location_id=$location "; else $location_cond=""; 
	if($pro_company){$companyCon="and id=$pro_company";}else{$companyCon="and id=$company";}
	$alter_reject_lib_arr=array();
	$allocationData=sql_select("select id,alter_standard_per,reject_standard_per from lib_company where status_active=1 and is_deleted=0 $companyCon $location_cond");
	
	foreach($allocationData as $row)
	{
		$alter_reject_lib_arr['alter']=$row[csf('alter_standard_per')];
		$alter_reject_lib_arr['reject']=$row[csf('reject_standard_per')];
	}
	
	if($location!="") $location_cond= "and a.location_id=$location "; else $location_cond="";
	if($pro_company){$companyCon="and a.comapny_id=$pro_company";}else{$companyCon="and a.comapny_id=$company";}
	$allocation_lib_arr=array();
	$allocationData=sql_select("select a.year, b.month_id, sum(b.capacity_month_min) as capa_min from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id $companyCon and a.status_active=1 and a.is_deleted=0 $location_cond group by a.year, b.month_id");
	foreach($allocationData as $row)
	{
		$allocation_lib_arr[$row[csf('year')]][$row[csf('month_id')]]=$row[csf('capa_min')];
	}
	
	

	
	if($location!="") $location_booked_cond= "and c.location_name=$location "; else $location_booked_cond="";
	if($location!="") $location_product_cond= "and location=$location "; else $location_product_cond="";
	if($location!="") $location_delivery_cond= "and a.location_id=$location "; else $location_delivery_cond="";	
	if($pro_company){$companyCon="and serving_company=$pro_company";}else{$companyCon="and company_id=$company";}

	if($floor!=0 and $floor!="") $floor_con= "and a.floor_id=$floor "; else $floor_con="";


	$total_pro_quantity=0;
	$reject_per=0;
	$alter_per=0;
	$i=1; 
	$alter_stander_data="[";
	$reject_stander_data="[";
	
	$reject_data="[";
	$alter_data="[";
	$dataSew_arr=array();
	foreach($yr_mon_part as $key=>$val)
	{
		
/*		$dataSew_arr=sql_select("select production_date, po_break_down_id, item_number_id, sum(production_quantity) as qcpass_quantity, sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty, sum(spot_qnty) as spot_qnty from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 $companyCon $floor_con and $production_date_con like '".$val."-%"."' group by production_date,po_break_down_id,item_number_id");
*/		
		
		
		$dataSew_arr=sql_select("select a.production_date, a.po_break_down_id, a.item_number_id, sum(b.production_qnty) as qcpass_quantity, sum(b.alter_qty) as alter_qnty, sum(b.reject_qty) as reject_qnty, sum(b.spot_qty) as spot_qnty,sum(b.replace_qty) as replace_qty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.production_type=5 and b.production_type=5 $companyCon $floor_con and $production_date_con like '".$val."-%"."' group by production_date,po_break_down_id,item_number_id");
		

	
	
		
		
		$qcpass_quantity=0;
		$alter_qnty=0;
		$reject_qnty=0;
		$spot_qnty=0;
		$replace_qty=0;
		foreach($dataSew_arr as $row)
		{
			
			$qcpass_quantity+=$row[csf("qcpass_quantity")];
			$alter_qnty+=$row[csf("alter_qnty")];
			$reject_qnty+=$row[csf("reject_qnty")];
			$spot_qnty+=$row[csf("spot_qnty")];
			$replace_qty+=$row[csf("replace_qty")];
		}
		
		$total_pro_quantity=($qcpass_quantity+$alter_qnty+$reject_qnty+$spot_qnty+$replace_qty);
		
		$reject_per=round((($reject_qnty/$total_pro_quantity)*100),4);
		$alter_per=round((($alter_qnty/$total_pro_quantity)*100),4);
		
		//echo "$val Production: ".$total_pro_quantity." Reject :".$reject_qnty." Alter :".$alter_qnty."<br>";
		
		
		 if (is_nan($reject_per)) {$reject_per=0;} else {$reject_per=$reject_per*1;}		
		 if (is_nan($alter_per)) { $alter_per=0;} else {$alter_per=$alter_per*1;}		
		
		
		$year=date("Y",strtotime($val));
		$month=date("m",strtotime($val));
		if($alter_reject_lib_arr['alter']=="") $alter=0; else $alter=$alter_reject_lib_arr['alter'];
		if($alter_reject_lib_arr['reject']=="") $reject=0; else $reject=$alter_reject_lib_arr['reject'];
		
		if($i!=12) $alter_stander_data .="".$alter.","; else $alter_stander_data .="".$alter."]";
		if($i!=12) $reject_stander_data .="".$reject.","; else $reject_stander_data .="".$reject."]";
		
		if($i!=12) $reject_data .="".$reject_per.","; else $reject_data .="".$reject_per."]";
		if($i!=12) $alter_data .="".$alter_per.","; else $alter_data .="".$alter_per."]";
		$i++;
	}

?>
	<script>
		var lnk='<? echo $m; ?>';
		var caption='<? echo $caption; ?>';
    </script>
    <div align="center" style="width:100%;">
   		<!--<div style="margin-left:30px; margin-top:10px"><a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<br />&nbsp;&nbsp;-->
        <div align="center" style="width:100%; font-size:14px; "><? echo "<b>Company :</b> ". $comp_arr[$company?$company:$pro_company];  if($location!=""){echo ",<b> Location </b>: ". $loc_name_arr[$location];}?></div>
        <div align="center" id="container" style="width:1000px; height:500px; background-color:#FFFFFF"></div>
    </div>
    
    <script src="ext_resource/hschart/hschart.js"></script>

<script>
var msg="Gmts Qty %"
Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null,
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

   // General
   background2: '#F0F0EA'
};
// Apply the theme
Highcharts.setOptions(Highcharts.theme);
	var cur="min";	
	$(function () {
		$('#container').highcharts({
			title: { text: '<? echo $caption; ?>'},
			xAxis: {
				categories: <? echo $month_data; ?>
			},
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
			labels: {
				items: [{
				   // html: 'Total fruit consumption',
					style: {
						left: '50px',
						top: '18px',
						color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
					}
				}]
			},
			series: [{
				type: 'column',
				name: 'Actual Alter %',
				data: <? echo $alter_data; ?>
			},{
				type: 'column',
				name: 'Actual Reject %',
				data: <? echo $reject_data; ?>
			},{
				type: 'spline',
				name: 'Alter Standard',
				data: <? echo $alter_stander_data; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[8],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' %'
            	}
			},{
				type: 'spline',
				name: 'Reject Standard',
				data: <? echo $reject_stander_data; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[8],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' %'
            	}
			}],
			tooltip: {
			valueSuffix: ' '
			}
		});
	});
</script>

<?

	
function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>
