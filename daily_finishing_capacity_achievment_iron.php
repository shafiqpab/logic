<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Day Finishing Capacity and Achivment(Iron)
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	19.03.2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
require_once('includes/common.php');

echo load_html_head_contents("Day Finishing Capacity and Achivment(Iron)", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page
 
extract( $_REQUEST );
$m= base64_decode($m);
list($company,$location,$from_date,$to_date)=explode("_",$data);

	if($m=="daily_finishing_capacity_achievment_iron")
	{
		$caption="Daily Finishing Capacity and Achievment(Iron) In HOURS";
	}
	else
	{
		$caption="Daily Finishing Capacity and Achievment(Iron) In HOURS";
	}
	
	if($company!=0){ $company=$company; } else { $company=""; }
	if($location!=0){ $location=$location; } else { $location=""; }



	if($db_type==0)
	{
		$firstDate = date("Y-m-d", strtotime($from_date));
		$lastDate = date("Y-m-d", strtotime($to_date));
	}
	else
	{
		$firstDate = date("d-M-Y", strtotime($from_date));
		$lastDate = date("d-M-Y", strtotime($to_date));	
	}

	$datediff = datediff( 'd', $firstDate, $lastDate);
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-M,y", strtotime($newdate));
		if($db_type==0) $pro_date[$j]=date("Y-m-d", strtotime($newdate)); else $pro_date[$j]=date("d-M-y", strtotime($newdate));

	}

	$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
	$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	
	
	if($db_type==0)
	{
		$country_ship_date_fld="a.country_ship_date";
		$production_date_con="production_date";
	}
	else
	{
		$country_ship_date_fld="to_char(a.country_ship_date,'DD-MM-YYYY')";
		$production_date_con="to_char(production_date,'DD-MM-YYYY')";
	}
	
	
	
	if($location!="") $location_cond= "and a.location_id=$location "; else $location_cond="";
	$allocation_lib_arr=array();
	$allocationData=sql_select("select a.year, b.month_id,b.day_id, sum(b.capacity_mint) as capa_min from lib_fin_gmts_capacity_cal_mst a,  lib_fin_gmts_capacity_cal_dtls b where a.id=b.mst_id and a.company_id in($company) and a.status_active=1 and a.is_deleted=0 and fin_type=1 $location_cond group by a.year, b.month_id,b.day_id");
	foreach($allocationData as $row)
	{
		$allocation_lib_arr[$row[csf('year')].'-'.$row[csf('month_id')].'-'.$row[csf('day_id')]]+=$row[csf('capa_min')];
	}
	
	$item_smv_array=array();
	$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.finsmv_pcs as smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
	}
	
	
	
	if($location!="") $location_booked_cond= "and c.location_name=$location "; else $location_booked_cond="";
	if($location!="") $location_product_cond= "and location=$location "; else $location_product_cond="";
	if($location!="") $location_delivery_cond= "and a.location_id=$location "; else $location_delivery_cond="";
	
	if($from_date!='' && $to_date!=''){
		if($db_type==0){
			
			$from_date=change_date_format($from_date);
			$to_date=change_date_format($to_date);
		}
		else{
			$from_date=change_date_format($from_date,'','',-1);
			$to_date=change_date_format($to_date,'','',-1);
		}
		$production_date_con="and production_date between '$from_date' and  '$to_date'";
		$country_ship_date_con="and a.country_ship_date between '$from_date' and  '$to_date'";
	}
	else{
		$date_con_from="";	
		$date_con_to="";	
	}
	
		
		
		$dataSew_arr=sql_select("select production_date, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where company_id in($company) and status_active=1 and is_deleted=0 and production_type=7 $production_date_con  $location_product_cond group by production_date,po_break_down_id,item_number_id");
		$producedQnty=0;
		foreach($dataSew_arr as $row)
		{
			$item_smv=$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			$producedQntyArr[$row[csf("production_date")]]+=(($row[csf("production_quantity")]*$item_smv)/60);
		}
		
		
		$sql="select a.country_ship_date,b.id as po_id, b.unit_price, c.set_smv, c.total_set_qnty, a.item_number_id, a.order_quantity AS qnty from wo_po_color_size_breakdown a, wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name in($company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $country_ship_date_con $location_booked_cond";
		$result=sql_select($sql);
		$bookedQty=0;
		foreach($result as $row)
		{
			$item_smv=$item_smv_array[$row[csf('po_id')]][$row[csf('item_number_id')]]['smv_pcs'];
			$bookedQtyArr[$row[csf('country_ship_date')]]+=(($row[csf('qnty')]*$item_smv)/60);
		}
	
	foreach($pro_date as $key=>$val)
	{
		$producedQnty=($producedQntyArr[csf($val)])?$producedQntyArr[csf($val)]:0;
		$producedQnty=number_format($producedQnty,2,'.','')*1;

		$bookedQty=($bookedQtyArr[csf($val)])?$bookedQtyArr[csf($val)]:0;
		$bookedQty=number_format($bookedQty,2,'.','')*1;
		
		$year=date("Y",strtotime($val));
		$month=date("m",strtotime($val))*1;
		$day=date("d",strtotime($val))*1;
		$key=$year.'-'.$month.'-'.$day;
		if($allocation_lib_arr[$key]=="") 
		{
			$capacity=0; 
		}
		else 
		{
			$capacity=(($allocation_lib_arr[$key])/60);
		}
		
		$capacity_data[]=$capacity;
		$booked_data[]=$bookedQty;
		$produced_data[]=$producedQnty;
	}
	

	$date_array= json_encode($date_array);
	$booked_data= json_encode($booked_data);
	$produced_data= json_encode($produced_data);
	$capacity_data= json_encode($capacity_data);

	
?>	
    <div align="center" style="width:100%;">
        <div align="center" style="width:100%; font-size:14px; "><? echo "<b>Company :</b> ". $comp_arr[$company];  if($location!=""){echo ",<b> Location </b>: ". $loc_name_arr[$location];}?></div>
        <div align="center" id="container" style="width:100%; height:500px; background-color:#FFFFFF"></div>
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
				categories: <? echo $date_array; ?>
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
				data:  <? echo $produced_data; ?>
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

