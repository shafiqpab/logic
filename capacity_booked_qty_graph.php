<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Capacity status and Booked Qty  Graph
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	20.12.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
require_once('includes/common.php');

echo load_html_head_contents("Capacity and Order Booked", "", "", $popup, $unicode, $multi_select, $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
$cps=explode("__",$cp);

$company=$cps[0];
$location=$cps[1];

	
	if($m=="capacity_status_smv")
	{
		$caption="Capacity and Order Booked";
	}
	else
	{
		$caption="Capacity and Order Booked";
	}
	
	if($company!=0){ $company=$company; } else { $company=""; }
	if($location!=0){ $location=$location; } else { $location=""; }
	//echo $company."==".$location;

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
	
	$AVG_Rate=array();
	$Asking_AVG_Rate=sql_select("select applying_period_date,asking_avg_rate from lib_standard_cm_entry where company_id=$company and status_active=1 and is_deleted=0 order by applying_period_date");
	foreach($Asking_AVG_Rate as $row)
	{
		$year=date("Y",strtotime($row[csf('applying_period_date')]));
		$month= date("m",strtotime($row[csf('applying_period_date')]));
		$month = strval(intval($month));
		//$allocation_lib_arr[$row[csf('year')]][$row[csf('month_id')]]=$row[csf('capa_pcs')];
		$AVG_Rate[$year][$month]=$row[csf('asking_avg_rate')];
	}
	//print_r($AVG_Rate);
	
	if($location!="") $location_cond= "and a.location_id=$location "; else $location_cond="";
	$allocation_lib_arr=array();
	$allocation_val_arr=array();
	$allocationData=sql_select("select a.year, b.month_id, b.capacity_month_pcs, b.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id in($company) and a.status_active=1 and a.is_deleted=0 $location_cond ");
	foreach($allocationData as $row)
	{
		$allocation_lib_arr[$row[csf('year')]][$row[csf('month_id')]]+=$row[csf('capacity_month_pcs')];
		$allocation_val_arr[$row[csf('year')]][$row[csf('month_id')]]+=($row[csf('capacity_month_pcs')]*$AVG_Rate[$row[csf('year')]][$row[csf('month_id')]]);
	}
	//print_r($allocation_val_arr);
	
	
	/*if($location!="") $location_cond= "and a.location_id=$location "; else $location_cond="";
	$allocation_lib_arr=array();
	$allocation_val_arr=array();
	$allocationData=sql_select("select a.year, b.month_id, sum(b.capacity_month_pcs) as capa_pcs, sum(b.capacity_month_pcs*a.basic_smv) as capa_val from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id in($company) and a.status_active=1 and a.is_deleted=0 $location_cond group by a.year, b.month_id");
	foreach($allocationData as $row)
	{
		$allocation_lib_arr[$row[csf('year')]][$row[csf('month_id')]]=$row[csf('capa_pcs')];
		$allocation_val_arr[$row[csf('year')]][$row[csf('month_id')]]=$row[csf('capa_val')];
	}
	print_r($allocation_val_arr);*/
	
	
	$item_smv_array=array();
	$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
	}

	
	if($location!="") $location_booked_cond= "and c.location_name=$location "; else $location_booked_cond="";
	if($location!="") $location_product_cond= "and location=$location "; else $location_product_cond="";
	if($location!="") $location_delivery_cond= "and a.location_id=$location "; else $location_delivery_cond="";	
	
	$i=1; 
	$capacity_data="[";
	$capacity_val_data="[";
	
	$booked_data="[";
	$booked_data_value="[";
	//$produced_data="[";
	
	$delivery_data="[";
	$delivery_data_value="[";
	
	foreach($yr_mon_part as $key=>$val)
	{
		/*$dataSew_arr=sql_select("select production_date, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where company_id in($company) and status_active=1 and is_deleted=0 and production_type=5 and $production_date_con like '".$val."-%"."'  $location_product_cond group by production_date,po_break_down_id,item_number_id");
		$producedQnty=0;
		foreach($dataSew_arr as $row)
		{
			$production_date=date("Y-m",strtotime($row[csf("production_date")]));
			$item_smv=$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			$producedQnty+=$row[csf("production_quantity")]*$item_smv;
		}*/
		
		if($location!="") $location_delivery_condition= "and c.location_name=$location "; else $location_delivery_condition="";
		//echo "select a.ex_factory_date,a.po_break_down_id,a.item_number_id,b.unit_price,c.total_set_qnty,sum(a.ex_factory_qnty) as delivery_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and $ex_factory_date_con_2 like '".$val."-%"."' and c.company_name in($company)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $location_delivery_condition group by a.ex_factory_date,a.po_break_down_id,a.item_number_id,b.unit_price,c.total_set_qnty";
				
		$data_arr=sql_select("select a.ex_factory_date,a.po_break_down_id,a.item_number_id,b.unit_price,c.total_set_qnty,sum(a.ex_factory_qnty) as delivery_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and $ex_factory_date_con_2 like '".$val."-%"."' and c.company_name in($company)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $location_delivery_condition group by a.ex_factory_date,a.po_break_down_id,a.item_number_id,b.unit_price,c.total_set_qnty");
		//echo "</br>"; echo "</br>";
		
		$deliveryQty=0;
		$deliveryvalue=0;
		foreach($data_arr as $row)
		{
			$unit_price=$row[csf('unit_price')]/$row[csf('total_set_qnty')];
			$deliveryQty+=$row[csf("delivery_qnty")];
			$deliveryvalue+=($row[csf("delivery_qnty")]*$unit_price);
		}
		
		 $sql="select c.location_name,b.id as po_id, b.unit_price, a.country_id, c.set_smv, c.total_set_qnty, sum(a.order_quantity) AS qnty, sum(a.order_total) AS amnt from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $country_ship_date_fld like '".$val."-%"."' $location_booked_cond group by c.location_name, b.id, b.unit_price, c.set_smv, c.total_set_qnty, a.country_id";
		//echo "</br>"; echo "</br>";
		$result=sql_select($sql);
		$bookedQty=0;
		$bookedvalue=0;  
		foreach($result as $row)
		{ 
			//$bookedQty+=$row[csf('qnty')]*$row[csf('set_smv')];
			$bookedQty+=$row[csf('qnty')]; //*$row[csf('total_set_qnty')]
			$bookedvalue+=$row[csf('amnt')];
		}
		
		$year=date("Y",strtotime($val));
		$month=date("m",strtotime($val));
		
		if($allocation_lib_arr[$year][(int) $month]=="") $capacity=0; else $capacity=$allocation_lib_arr[$year][(int) $month];
		if($allocation_val_arr[$year][(int) $month]=="") $capacity_val=0; else $capacity_val=$allocation_val_arr[$year][(int) $month];
		
		if($i!=12) $capacity_data .="".$capacity.","; else $capacity_data .="".$capacity."]";
		if($i!=12) $capacity_val_data .="".$capacity_val.","; else $capacity_val_data .="".$capacity_val."]";
		
		if($i!=12) $booked_data .="".$bookedQty.","; else $booked_data .="".$bookedQty."]";
		if($i!=12) $booked_data_value .="".$bookedvalue.","; else $booked_data_value .="".$bookedvalue."]";
		
		//if($i!=12) $produced_data .="".$producedQnty.","; else $produced_data .="".$producedQnty."]";
		if($i!=12) $delivery_data .="".$deliveryQty.","; else $delivery_data .="".$deliveryQty."]";
		if($i!=12) $delivery_data_value .="".$deliveryvalue.","; else $delivery_data_value .="".$deliveryvalue."]";
		$i++;
	}
	//echo $capacity_data."</br>";
	//echo $booked_data."</br>";
	//echo $produced_data."</br>";
	//echo $delivery_data;
	//die;
?>
	<script>
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

Highcharts.theme = {
   /*colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],*/
	 colors: ["#f45b5b", "#8085e9", "#90ee7e", "#f7a35c", "#8d4654", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
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

	var msg="Quantity and Amount"
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
				name: 'Booked Amount',
				data: <? echo $booked_data_value; ?>
			},{
				type: 'column',
				name: 'Booked Basic Qty',
				data: <? echo $booked_data; ?>
			},{
				type: 'column',
				name: 'Delivery Amount',
				data: <? echo $delivery_data_value; ?>
			},{
				type: 'column',
				name: 'Delivery Basic Qty',
				data: <? echo $delivery_data; ?>
			},{
				type: 'spline',
				name: 'Capacity Basic Qty',
				data: <? echo $capacity_data; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[7],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' PCS'
            	}
			},{
				type: 'spline',
				name: 'Capacity Amount',
				data: <? echo $capacity_val_data; ?>,
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[7],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' USD'
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
