<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Capacity status and SMV Graph
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	08.11.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
require_once('includes/common.php');

echo load_html_head_contents("Capacity status and SMV", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];
$tval=$cps[2];
	
	if($m=="capacity_status_smv")
	{
		if($tval==1)
		{
			$caption="Capacity Status In HOURS";
		}
		else
		{
			$caption="Capacity Status In MINUTE";
		}
	}
	else
	{
		if($tval==1)
		{
			$caption="Capacity Status In HOURS";
		}
		else
		{
			$caption="Capacity Status In MINUTE";
		}
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
		$country_ship_date_fld="a.country_ship_date";
		$production_date_con="production_date";
		$ex_factory_date_con="b.ex_factory_date";
		$ex_factory_date_con_2="a.ex_factory_date";
		$current_month_end_date=$current_month_end_date;
	}
	else
	{
		$country_ship_date_fld="to_char(a.country_ship_date,'YYYY-MM-DD')";
		$production_date_con="to_char(production_date,'YYYY-MM-DD')";
		$ex_factory_date_con="to_char(b.ex_factory_date,'YYYY-MM-DD')";
		$ex_factory_date_con_2="to_char(a.ex_factory_date,'YYYY-MM-DD')";
		$current_month_end_date=change_date_format($current_month_end_date,'yyyy-mm-dd','-',1);
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
	$booked_data="[";
	$produced_data="[";
	$delivery_data="[";
	$pending_data="[";
	foreach($yr_mon_part as $key=>$val)
	{
		//echo "select production_date, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where company_id in($company) and status_active=1 and is_deleted=0 and production_type=5 and $production_date_con like '".$val."-%"."'  $location_product_cond group by production_date,po_break_down_id,item_number_id";
		
		$dataSew_arr=sql_select("select production_date, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where company_id in($company) and status_active=1 and is_deleted=0 and production_type=5 and $production_date_con like '".$val."-%"."'  $location_product_cond group by production_date,po_break_down_id,item_number_id");
		$producedQnty=0;
		foreach($dataSew_arr as $row)
		{
			$production_date=date("Y-m",strtotime($row[csf("production_date")]));
			$item_smv=$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			if($tval==1)
			{
				$producedQnty+=round(($row[csf("production_quantity")]*$item_smv)/60);
			}
			else
			{
				$producedQnty+=$row[csf("production_quantity")]*$item_smv;
			}
		}
		
		
		//echo "select a.ex_factory_date,a.po_break_down_id,a.item_number_id,sum(a.ex_factory_qnty*c.set_smv) as delivery_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and $ex_factory_date_con_2 like '".$val."-%"."' and c.company_name in($company)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $location_delivery_condition group by a.ex_factory_date,a.po_break_down_id,a.item_number_id";
		
		if($location!="") $location_delivery_condition= "and c.location_name=$location "; else $location_delivery_condition="";
		$data_arr=sql_select("select a.ex_factory_date,a.po_break_down_id,a.item_number_id,sum(a.ex_factory_qnty*c.set_smv) as delivery_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and $ex_factory_date_con_2 like '".$val."-%"."' and c.company_name in($company)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $location_delivery_condition group by a.ex_factory_date,a.po_break_down_id,a.item_number_id");
		//echo "</br>"; echo "</br>";
		
		$deliveryQty=0;
		foreach($data_arr as $row)
		{
			//$ex_factory_date=date("Y-m",strtotime($row[csf("ex_factory_date")]));
			//$item_smv_ex=$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			//$deliveryQty+=$row[csf("ex_factory_qnty")]*$item_smv_ex;
			if($tval==1)
			{
				$deliveryQty+=round($row[csf("delivery_qnty")]/60);
			}
			else
			{
				$deliveryQty+=$row[csf("delivery_qnty")];
			}
		}
		
		//echo "</br>"; echo "</br>";
		
		//$sql="select b.id as po_id, b.unit_price, c.set_smv, c.total_set_qnty, sum(a.order_quantity) AS qnty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $country_ship_date_fld like '".$val."-%"."' $location_booked_cond group by b.id, b.unit_price, c.set_smv, c.total_set_qnty";
		//$sql="select c.location_name,b.id as po_id, b.unit_price, a.country_id, c.set_smv, c.total_set_qnty, sum(a.order_quantity/c.total_set_qnty) AS qnty, sum(a.order_total) AS amnt from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $country_ship_date_fld like '".$val."-%"."' $location_booked_cond group by c.location_name, b.id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id";
		
		
		
		$sql="select b.id as po_id, b.unit_price, c.set_smv, c.total_set_qnty, a.item_number_id, a.order_quantity AS qnty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $country_ship_date_fld like '".$val."-%"."' $location_booked_cond";
		 //echo $sql; die;
		$result=sql_select($sql);
		$bookedQty=0; $order_wise_smv=array();
		foreach($result as $row)
		{
			$item_smv=$item_smv_array[$row[csf('po_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			
			if($tval==1)
			{ 
				$bookedQty+=round(($row[csf('qnty')]*$item_smv)/60);
			}
			else
			{
				$bookedQty+=$row[csf('qnty')]*$item_smv;
			}
		}
		
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
				$pending_date_con ="and $country_ship_date_fld like '".$val."-%"."'";
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
		
		if($sql_sew!=""){$result_sew=sql_select($sql_sew);}
		$pending_qty=0;
		foreach($result_sew as $row)
		{
			$item_smv=$item_smv_array[$row[csf('po_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			if($tval==1)
			{ 
				$pending_qty+=((($row[csf('order_quantity')]-$sew_qty_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]])*$item_smv)/60);
			}
			else
			{
				$pending_qty+=(($row[csf('order_quantity')]-$sew_qty_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]])*$item_smv);
			}
		}
		
		$pending_qty=number_format($pending_qty,0,'.','');
		
		$year=date("Y",strtotime($val));
		$month=date("m",strtotime($val));
		if($allocation_lib_arr[$year][(int) $month]=="") 
		{
			$capacity=0; 
		}
		else 
		{
			if($tval==1)
			{
				$capacity=round(($allocation_lib_arr[$year][(int) $month])/60);
			}
			else
			{
				$capacity=$allocation_lib_arr[$year][(int) $month];
			}
		}
		
		if($i!=12) $capacity_data .="".$capacity.","; else $capacity_data .="".$capacity."]";
		if($i!=12) $booked_data .="".$bookedQty.","; else $booked_data .="".$bookedQty."]";
		if($i!=12) $produced_data .="".$producedQnty.","; else $produced_data .="".$producedQnty."]";
		if($i!=12) $delivery_data .="".$deliveryQty.","; else $delivery_data .="".$deliveryQty."]";
		if($i!=12) $pending_data .="".$pending_qty.","; else $pending_data .="".$pending_qty."]";
		$i++;
	}
	//echo $capacity_data."</br>";
	 //echo $booked_data."</br>";
	//echo $produced_data."</br>";
	//echo $pending_data;
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

	if(tval==1)
	{
		var cur="hrs";	
	}
	else
	{
		var cur="min";	
	}
	
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
			}],
			tooltip: {
			valueSuffix: ' '+cur
			}
		});
	});
</script>

<?php
function add_month($orgDate,$mon)
{
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>
