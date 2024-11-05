<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Capacity Comparison In Hour
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	21.03.2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
require_once('includes/common.php');

echo load_html_head_contents("Capacity Comparison In Hour", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];
$tval=$cps[2];
	
	if($m=="capacity_comparison_in_hour")
	{
		$caption="Capacity Comparison In HOURS";
	}
	else
	{
		$caption="Capacity Comparison In HOURS";
	}
	
	if($company!=0){ $company=$company; } else { $company=""; }
	if($location!=0){ $location=$location; } else { $location=""; }
	//echo $location;

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
	$c_month=date("Y-m");
	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($db_type==0)
	{
		$pub_ship_date_fld="b.pub_shipment_date";
		$country_ship_date_fld="a.country_ship_date";
		$production_date_con="production_date";
		$ex_factory_date_con="b.ex_factory_date";
		$ex_factory_date_con_2="a.ex_factory_date";
		$current_month_end_date=$current_month_end_date;
	}
	else
	{
		$pub_ship_date_fld="to_char(b.pub_shipment_date,'YYYY-MM-DD')";
		$country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
		$production_date_con="to_char(production_date,'YYYY-MM-DD')";
		$ex_factory_date_con="to_char(b.ex_factory_date,'YYYY-MM-DD')";
		$ex_factory_date_con_2="to_char(a.ex_factory_date,'YYYY-MM-DD')";
		$current_month_end_date=change_date_format($current_month_end_date,'yyyy-mm-dd','-',1);
	}
	
	$working_hour_arr=array();
	$workingData=sql_select("select applying_period_date, working_hour from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id in($company)");
	foreach($workingData as $row)
	{
		$working_hour_arr[date("Y",strtotime($row[csf('applying_period_date')]))][(int)(date("m",strtotime($row[csf('applying_period_date')])))]=$row[csf('working_hour')];
	}
	
	
	if($location!="") $location_cond= "and a.location_id=$location "; else $location_cond="";
	$allocation_lib_arr=array();
	$allocationData=sql_select("select a.year, b.month_id, sum(b.capacity_month_min) as capa_min from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id in($company) and a.status_active=1 and a.is_deleted=0 $location_cond group by a.year, b.month_id");
	foreach($allocationData as $row)
	{
		$allocation_lib_arr[$row[csf('year')]][$row[csf('month_id')]]=$row[csf('capa_min')];
	}
	
	
	$item_smv_array=array();
	$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
	}
	
	$sew_qty_arr=array();
	$sewing_qnty=sql_select("SELECT po_break_down_id, country_id, item_number_id, sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 and country_id>0 group by po_break_down_id, country_id, item_number_id");
	foreach($sewing_qnty as $row_sew)
	{
		$sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('country_id')]][$row_sew[csf('item_number_id')]]=$row_sew[csf('production_quantity')];
	}
	//print_r($sew_qty_arr);
	
	if($location!="") $location_booked_cond= "and c.location_name=$location "; else $location_booked_cond="";
	if($location!="") $location_product_cond= "and location=$location "; else $location_product_cond="";
	if($location!="") $location_delivery_cond= "and a.location_id=$location "; else $location_delivery_cond="";
		
	
	
	$i=1; 
	$capacity_data="[";
	$capacity_8hours_data="[";
	$booked_data="[";
	$produced_data="[";
	$delivery_data="[";
	$pending_data="[";
	foreach($yr_mon_part as $key=>$val)
	{
		
		$dataSew_arr=sql_select("select production_date, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where company_id in($company) and status_active=1 and is_deleted=0 and production_type=5 and $production_date_con like '".$val."-%"."'  $location_product_cond group by production_date,po_break_down_id,item_number_id");
		
		
		$producedQnty=0;
		foreach($dataSew_arr as $row)
		{
			$production_date=date("Y-m",strtotime($row[csf("production_date")]));
			$item_smv=$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			$producedQnty+=round(($row[csf("production_quantity")]*$item_smv)/60);
		}
		
		
		
		if($location!="") $location_delivery_condition= "and c.location_name=$location "; else $location_delivery_condition="";
		$data_arr=sql_select("select a.ex_factory_date,a.po_break_down_id,a.item_number_id,sum(a.ex_factory_qnty*c.set_smv) as delivery_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and $ex_factory_date_con_2 like '".$val."-%"."' and c.company_name in($company)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $location_delivery_condition group by a.ex_factory_date,a.po_break_down_id,a.item_number_id");
		//echo "</br>"; echo "</br>";
		
		$deliveryQty=0;
		foreach($data_arr as $row)
		{
			$deliveryQty+=round($row[csf("delivery_qnty")]/60);
		}
		
		
		
		
		/*$sql="select b.id as po_id, b.unit_price, c.set_smv, c.total_set_qnty, a.item_number_id, a.order_quantity AS qnty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $pub_ship_date_fld like '".$val."-%"."' $location_booked_cond";*/
		
		
	$sql="SELECT a.set_smv, a.total_set_qnty, b.id as po_id, b.pub_shipment_date as shipment_date, b.po_total_price,
	(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
	(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
	(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
	(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
	FROM wo_po_details_master a, wo_po_break_down b
	WHERE a.job_no=b.job_no_mst AND a.company_name =$company $location_booked_cond and $pub_ship_date_fld like '".$val."-%"."'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		
		
		
		
		//echo "</br>"; echo "</br>";
		$result=sql_select($sql);
		$bookedQty=0; $order_wise_smv=array();
		foreach($result as $row)
		{
			//$item_smv=$item_smv_array[$row[csf('po_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			//$bookedQty+=round(($row[csf('qnty')]*$item_smv)/60);
			
			$confirm_qty=($row[csf("confirm_qty")])*$row[csf("set_smv")]/60;
			$projected_qty=($row[csf("projected_qty")])*$row[csf("set_smv")]/60;
			$bookedQty+=$confirm_qty+$projected_qty;
		}
		
		$bookedQty=round($bookedQty);
		
		
		if($val==$c_month) 
		{
			$start_date=change_date_format($val,'yyyy-mm-dd','-',1);
		    $str_cond3="  and a.country_ship_date between '$start_date' and '$current_month_end_date'";
			$pending_date_con ="$str_cond3";
			$sql_sew="select b.id as po_id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id, a.item_number_id, sum(a.order_quantity) as order_quantity 
			from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.shiping_status!=3 $pending_date_con $location_booked_cond 
			group by b.id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id, a.item_number_id";
		}
		else 
		{
			if($val<$c_month)
			{
				$pending_date_con ="and $pub_ship_date_fld like '".$val."-%"."'";
				$sql_sew="select b.id as po_id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id, a.item_number_id, sum(a.order_quantity) as order_quantity 
				from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c 
				where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.shiping_status!=3 $pending_date_con $location_booked_cond 
				group by b.id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id, a.item_number_id";
			}
			else
			{
				$sql_sew="";
			}
			
		}
		//echo $sql_sew;
		//echo "</br>"; echo "</br>";
		
		$result_sew=sql_select($sql_sew);
		$pending_qty=0;
		foreach($result_sew as $row)
		{
			$item_smv=$item_smv_array[$row[csf('po_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			$pending_qty+=((($row[csf('order_quantity')]-$sew_qty_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]])*$item_smv)/60);
		}
		
		$pending_qty=number_format($pending_qty,0,'.','');
		
		$year=date("Y",strtotime($val));
		$month=date("m",strtotime($val));
		if($allocation_lib_arr[$year][(int) $month]=="") 
		{
			$capacity=0; 
			$capacity_8hours=0;
		}
		else 
		{
			$capacity=round(($allocation_lib_arr[$year][(int) $month])/60);
			$capacity_8hours=round(($capacity/$working_hour_arr[$year][(int) $month])*8);
			
		}
		
		if($i!=12) $capacity_data .="".$capacity.","; else $capacity_data .="".$capacity."]";
		if($i!=12) $capacity_8hours_data .="".$capacity_8hours.","; else $capacity_8hours_data .="".$capacity_8hours."]";
		if($i!=12) $booked_data .="".$bookedQty.","; else $booked_data .="".$bookedQty."]";
		if($i!=12) $produced_data .="".$producedQnty.","; else $produced_data .="".$producedQnty."]";
		if($i!=12) $delivery_data .="".$deliveryQty.","; else $delivery_data .="".$deliveryQty."]";
		if($i!=12) $pending_data .="".$pending_qty.","; else $pending_data .="".$pending_qty."]";
		$i++;
	}
	
	
?>
	<script>
	var tval='<? echo $tval; ?>';
    var lnk='<? echo $m; ?>';
    var caption='<? echo $caption; ?>';
    </script>
    <div align="center" style="width:100%;">
        <!--<div style="margin-left:30px; margin-top:10px"><a href="index.php">Main Page</a>&nbsp;&nbsp;<a href="index.php?g=1">Dash Board</a>&nbsp;&nbsp;<br />&nbsp;&nbsp;-->
        <div align="center" style="width:100%; font-size:14px; "><? echo "<b>Company :</b> ". $comp_arr[$company];  if($location!=""){echo ",<b> Location </b>: ". $loc_name_arr[$location];}?></div>
        <div align="center" id="container" style="width:1000px; height:500px; background-color:#FFFFFF"></div>
    </div>
    
    <script src="ext_resource/hschart/hschart.js"></script>

<script>

var msg="Total SMV"
Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#ff4d4d","#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
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

	var cur="hrs";	
	
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
				name: 'Booked',
				data: <? echo $booked_data; ?>
			},{
				type: 'column',
				name: 'Produced',
				data: <? echo $produced_data; ?>
			},{
				type: 'column',
				name: 'Sewing Pending ',
				data: <? echo $pending_data; ?>
			},{
				type: 'column',
				name: 'Delivery',
				data: <? echo $delivery_data; ?>
			},{
				type: 'spline',
				name: 'Capacity',
				data: <? echo $capacity_data; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[8],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' '+cur
            	}
			},{
				type: 'spline',
				name: 'Capacity Comparison',
				data: <? echo $capacity_8hours_data; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[8],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' '+cur
            	}
			}],
			tooltip: {
			valueSuffix: ' '+cur
			}
		});
	});
	
	/*
	,{
				type: 'spline',
				name: 'Capacity Comparison',
				data: <?// echo $capacity_8hours_data; ?>,
				marker: {
					lineWidth: 3,
					lineColor: Highcharts.getOptions().colors[6],
					fillColor: 'white'
				}
	*/
	
</script>

<?php
function add_month($orgDate,$mon)
{
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>
