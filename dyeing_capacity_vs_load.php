<?
	/*-------------------------------------------- Comments -----------------------
	Purpose			: 	This Graph Will Create Dyeing Capacity VS Load
	Functionality	:	
	JS Functions	:
	Created by		:	Jahid 
	Creation date 	: 	28.04.2016
	Updated by 		: 		
	Update date		: 		   
	QC Performed BY	:		
	QC Date			:	
	Comments		:
	*/
	
	
	session_start();
	require_once('includes/common.php');
	require_once('includes/class3/class.conditions.php');
	require_once('includes/class3/class.reports.php');
	require_once('includes/class3/class.fabrics.php');
	
	echo load_html_head_contents("Dyeing Capacity VS Load", "", "", $popup, $unicode, '', $amchart);//this $amchart is not wark in this page
	 
	$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
	$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	
	$group_arr=return_library_array("select id,GROUP_NAME from LIB_GROUP", "id","GROUP_NAME");

	//print_r($group_arr);die;
	$groupName=implode(',',$group_arr);

	
	extract( $_REQUEST );
	$m= base64_decode($m);
	list($company,$location,$from_date,$to_date)=explode("_",$data);
	
	$caption="Dyeing Capacity VS Load";

	
	
	if($db_type==0)
	{
		$firstDate = change_date_format($from_date,'yyyy-mm-dd');
		$lastDate = change_date_format($to_date,'yyyy-mm-dd');
	}
	else
	{
		$firstDate = change_date_format($from_date,'','',1);
		$lastDate = change_date_format($to_date,'','',1);
	}
	
	if($company){$com_con="and company_id=$company ";}
	$lib_machine_cap=sql_select("select id, prod_capacity from lib_machine_name where  category_id=2 $com_con and status_active=1 and is_deleted=0");
	$lib_machine_data=array();$all_machine_id_arr=array();
	foreach( $lib_machine_cap as $row )
	{
		$lib_machine_data_hour[$row[csf("id")]]=($row[csf("prod_capacity")]/24);
		$lib_machine_data[$row[csf("id")]]=$row[csf("prod_capacity")];
		$all_machine_id_arr[$row[csf("id")]]=$row[csf("id")];
		//$total_cap+=$row[csf("prod_capacity")];
	}
	
	
	
	
	$all_machine_id=implode(",", $all_machine_id_arr);
	 
	$datediff = datediff( 'd', $firstDate, $lastDate);
	for($j=0;$j<$datediff;$j++)
	{
		$newdate =add_date($firstDate,$j);
		$date_array[$j]=date("d-m", strtotime($newdate));
		$pro_date[date("d-m-Y", strtotime($newdate))]=date("d-m-Y", strtotime($newdate));
		foreach($lib_machine_data as $mcid=>$val)
		{
			$daily_machine_wise_capacity[$mcid][date("d-m-Y", strtotime($newdate))]=$val;
		}
	}
	
	
	$machine_idle_sql=sql_select("select id, machine_entry_tbl_id, from_date, from_hour, from_minute, to_date, to_hour, to_minute from  pro_cause_of_machine_idle where machine_entry_tbl_id in($all_machine_id) and machine_idle_cause<>3");
	 
	foreach($machine_idle_sql as $row)
	{
		 
		$from_date=date("Y-m-d H:i:s",strtotime( $row[csf("from_date")]." ". $row[csf("from_hour")].":".$row[csf("from_minute")].":00"));
		$to_date=date("Y-m-d H:i:s",strtotime( $row[csf("to_date")]." ". $row[csf("to_hour")].":".$row[csf("to_minute")].":00"));
		$day_diff=datediff( 'd', $row[csf("from_date")], $row[csf("to_date")]);
		$hour_diff = datediff( 'n', $from_date, $to_date);
			
		if($day_diff>1)
		{
			for( $i=1; $i<=$day_diff;$i++ )
			{
				$cdate=add_date($row[csf("from_date")],$i-1);
				if($i==1)
				{
					$hour = datediff( 'h', $from_date, $cdate."23:59:59")+1;
				}
				else if($i==$day_diff)
				{
					$hour = datediff( 'h', $cdate, $to_date);
				}
				 
				$lost_cap=$lib_machine_data_hour[$row[csf("machine_entry_tbl_id")]]*$hour;
				$daily_machine_wise_capacity[$row[csf("machine_entry_tbl_id")]][date("d-m-Y", strtotime($cdate))]=$daily_machine_wise_capacity[$row[csf("machine_entry_tbl_id")]][date("d-m-Y", strtotime($cdate))]-$lost_cap;
				
				
			}
		}
		else
		{
			$hour= datediff( 'h', $from_date, $to_date); 
			$lost_cap=$lib_machine_data_hour[$row[csf("machine_entry_tbl_id")]]*$hour;
			$daily_machine_wise_capacity[$row[csf("machine_entry_tbl_id")]][date("d-m-Y", strtotime($row[csf("from_date")]))]=$daily_machine_wise_capacity[$row[csf("machine_entry_tbl_id")]][date("d-m-Y", strtotime($row[csf("from_date")]))]-$lost_cap;
		}
	}
	
	foreach($daily_machine_wise_capacity as $m_id=>$value)
	{
		foreach($value as $c_day=>$val)
		{
			$daily_machine_tot_capacity[$c_day]+=$val;
		}
	}
	
	
	
	
	$condition= new condition();
	if(str_replace("'","",$company)>0)
	{
		$condition->company_name("=$company");
	}
	
	
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_req_qnty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
	//echo $fabric->getquery();die;
	//print_r($fabric_req_qnty_arr);die;
	
	$firstDate_prev=add_date($firstDate,-30);
	if($db_type==0) $firstDate_prev=change_date_format($firstDate_prev,'yyyy-mm-dd'); else $firstDate_prev=change_date_format($firstDate_prev,'','',1);
	
	
	if($company){$comCon_c=" and c.company_name=$company";}
	$tna_dye_sql=sql_select("select a.po_number_id, a.task_start_date, a.task_finish_date 
	from  tna_process_mst a, wo_po_break_down b, wo_po_details_master c 
	where a.po_number_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 $comCon_c and a.is_deleted=0 and a.task_number=61 and (a.task_start_date between '$firstDate_prev' and '$lastDate')");
	$booking_data=array();
	foreach($tna_dye_sql as $row)
	{
		$daily_book=0;
		$book_day_diff=datediff( 'd', $row[csf("task_start_date")], $row[csf("task_finish_date")]);
		$daily_book=$fabric_req_qnty_arr["knit"]["grey"][$row[csf("po_number_id")]]/$book_day_diff;
		if($book_day_diff>1)
		{
			for( $i=1; $i<=$book_day_diff;$i++ )
			{
				$book_date=add_date($row[csf("task_start_date")],$i-1);
				if((strtotime($book_date) <= strtotime($lastDate)) && (strtotime($book_date) >= strtotime($firstDate)) )
				{
					$booking_data[date("d-m-Y", strtotime($book_date))]+=$daily_book;
				}
			}
			
		}
		else
		{
			if(strtotime($row[csf("task_start_date")]) == strtotime($firstDate))
			{
				$booking_data[date("d-m-Y", strtotime($row[csf("task_start_date")]))]+=$daily_book;
			}
		}
		$all_order.=$row[csf("po_number_id")].",";
		
	}
	
	$all_order=implode(",",array_unique(explode(",",chop($all_order,","))));
	
	
	if($company){$comCon_a=" and a.company_id=$company";}
	$produce_sql=sql_select("select a.process_end_date, sum(b.batch_weight) as batch_weight from pro_fab_subprocess a, pro_batch_create_mst b where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and a.result=1 and a.status_active=1 $comCon_a and a.is_deleted=0 and a.service_source=1 and a.process_end_date between '$firstDate' and '$lastDate'  group by a.process_end_date");
	$produce_data=array();
	foreach($produce_sql as $row)
	{
		$produce_data[date("d-m-Y", strtotime($row[csf("process_end_date")]))]=$row[csf("batch_weight")];
	}
	

	//$comp_arr=return_library_array("select id,company_name from lib_company", "id","company_name");
	//$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	foreach($pro_date as $key=>$val)
	{
		$producedQnty=($produce_data[$val])?$produce_data[$val]:0;
		$producedQnty=number_format($producedQnty,2,'.','')*1;

		$bookedQty=($booking_data[$val])?$booking_data[$val]:0;
		$bookedQty=number_format($bookedQty,2,'.','')*1;
		
		$capacity=($daily_machine_tot_capacity[$val])?$daily_machine_tot_capacity[$val]:0;
		$capacity=number_format($capacity,2,'.','')*1;
		
		$capacity_data[]=$capacity;
		$booked_data[]=$bookedQty;
		$produced_data[]=$producedQnty;
	}
	

	$date_array= json_encode($date_array);
	$booked_data= json_encode($booked_data);
	$produced_data= json_encode($produced_data);
	$capacity_data= json_encode($capacity_data);
	$comp = ($company==0)?$groupName:$comp_arr[$company];


	
?>	
    <div align="center" style="width:100%;">
        <div align="center" style="width:100%; font-size:14px; "><? echo "</b> ". $comp;  if($location!=""){echo ", ". $loc_name_arr[$location];}?></div>
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

	
	var cur="KG";
	
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

