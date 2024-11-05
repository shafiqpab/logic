<?php
header('Content-type:text/html; charset=utf-8');
session_start();
//if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include( '../../includes/common.php' );

if($db_type==0) $yr=date("Y",time()); else $yr=date("y",time());

if($db_type==0) $todaydate=date("Y-m-d",time()); else  $todaydate=date("d-M-y",time());
if($db_type==0) $nextdate=date( ($yr+1)."-m-d",time()); else  $nextdate=date("d-M-".($yr+1),time());
	
	
	$plan_uses_sts=return_field_value("is_locked", "table_status_on_transaction"," form_name='".$data[3]."'");
	 
	
	if( $data[2]=="") $data[2]=date("d-m-Y",time());
	$from_date=date("Y-m-d", strtotime( $data[2]) );
	$days_forward=120;
	$width=(30*$days_forward)+350;
	$to_date=add_date($from_date,$days_forward);
	
	$weeklist=return_library_array("select week,week_date from  week_of_year where week_date between '$todaydate' and '$nextdate' order by week_date ", "week_date","week");
	
	
	// and line_id in ( ".implode(",",$line_names_ids)." )  and company_id='".$data[0]."' and location_id='".$data[1]."'  and 
	
	 $sql="select id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,day_wise_plan,company_id,location_id,item_number_id,off_day_plan,order_complexity,ship_date from  ppl_sewing_plan_board where (start_date between '".$todaydate."' and '".$nextdate."'  or  end_date between '".$todaydate."' and '".$nextdate."') or ( start_date < '".$todaydate."' and end_date> '".$nextdate."')  order by po_break_down_id";
	$sql_data=sql_select($sql);  
	$m=0;
	$plan_data="";
	foreach($sql_data as $rows)
	{
		$m++;
		$crossed_plan=0;
		$actual_start_date=$rows[csf("start_date")];
		$actual_end_date=$rows[csf("end_date")];
			//if Any plan cross the dash board, before or after starts
		$po_break_down_array[$rows[csf("po_break_down_id")]]=$rows[csf("po_break_down_id")];
		//echo (change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0))."==". (change_date_format( str_replace("'","",trim($from_date)),'','',0)).'----';
		
		if(strtotime(change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0)) < strtotime(change_date_format( str_replace("'","",trim($from_date)),'','',0)))
		{
			$dur=datediff("d",change_date_format( str_replace("'","",trim($rows[csf("start_date")])),'','',0),change_date_format( str_replace("'","",trim($from_date)),'','',0));
			$actual_start_date=$rows[csf("start_date")];
			$rows[csf("start_date")]=$from_date;
			$rows[csf("duration")]=$rows[csf("duration")]-$dur+1;
			$crossed_plan=1;
		}
		if(strtotime(change_date_format( str_replace("'","",trim($rows[csf("end_date")])),'','',0)) > strtotime(change_date_format( str_replace("'","",trim($to_date)),'','',0)))
		{
			$dur=datediff("d",change_date_format( str_replace("'","",trim($rows[csf("end_date")])),'','',0),change_date_format( str_replace("'","",trim($to_date)),'','',0));
			 $actual_end_date=$rows[csf("end_date")];
			 $rows[csf("end_date")]=$to_date;
			 $rows[csf("duration")]=$rows[csf("duration")]-$dur+1;
			 $crossed_plan=1;
		}
		//if Any plan cross the dash board, before or after ends
		//echo $crossed_plan."==";
		if($po_number_array[$rows[csf("po_break_down_id")]]=="")
		{
			$number=return_field_value("po_number","wo_po_break_down", "id='".$rows[csf("po_break_down_id")]."'");
			$po_number_array[$rows[csf("po_break_down_id")]]=$number;
		}
		if($rows[csf("ship_date")]=='' || $rows[csf("ship_date")]==0) $rows[csf("ship_date")]="99999999"; else $rows[csf("ship_date")]=$rows[csf("ship_date")];
		if( $plan_data=="")
				$plan_data=$rows[csf("id")]."**".$rows[csf("line_id")]."**".$rows[csf("po_break_down_id")]."**".$rows[csf("plan_id")]."**".change_date_format( $rows[csf("start_date")])."**".$rows[csf("start_hour")]."**".change_date_format( $rows[csf("end_date")])."**".$rows[csf("end_hour")]."**".$rows[csf("duration")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("comp_level")]."**".$rows[csf("first_day_output")]."**".$rows[csf("increment_qty")]."**".$rows[csf("terget")]."**".$crossed_plan."**".change_date_format($actual_start_date)."**".change_date_format($actual_end_date)."**".$number."**".$rows[csf("day_wise_plan")]."**".$rows[csf("item_number_id")]."**".$rows[csf("company_id")]."**".$rows[csf("location_id")]."**".$rows[csf("off_day_plan")]."**".$rows[csf("order_complexity")]."**".$rows[csf("ship_date")];
		else
			$plan_data .="**__**".$rows[csf("id")]."**".$rows[csf("line_id")]."**".$rows[csf("po_break_down_id")]."**".$rows[csf("plan_id")]."**".change_date_format( $rows[csf("start_date")])."**".$rows[csf("start_hour")]."**".change_date_format( $rows[csf("end_date")])."**".$rows[csf("end_hour")]."**".$rows[csf("duration")]."**".$rows[csf("plan_qnty")]."**".$rows[csf("comp_level")]."**".$rows[csf("first_day_output")]."**".$rows[csf("increment_qty")]."**".$rows[csf("terget")]."**".$crossed_plan."**".change_date_format($actual_start_date)."**".change_date_format($actual_end_date)."**".$number."**".$rows[csf("day_wise_plan")]."**".$rows[csf("item_number_id")]."**".$rows[csf("company_id")]."**".$rows[csf("location_id")]."**".$rows[csf("off_day_plan")]."**".$rows[csf("order_complexity")]."**".$rows[csf("ship_date")];
//5**169**4631**5**11-03-2015****21-03-2015**0**11**10000**1**1000**100**1200**0**11-03-2015**21-03-2015**B/19032015**1000,1100,0,1200,1200,1200,1200,1200,1200,0,700**2**1**1****90



		for($k=0; $k<$rows[csf("duration")]; $k++)
		{
			$dates=add_date($rows[csf("start_date")],$k);
			$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("line_id")]][change_date_format( str_replace("'","",trim($dates)),'','',0)]=$rows[csf("id")];
		}
	}
 	
	 //List all offdays
	$sql="select a.mst_id,a.month_id,a.date_calc,a.day_status,comapny_id,capacity_source,location_id from  lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id and date_calc between '".$from_date."' and '".$to_date."' and comapny_id='$data[0]' and location_id='$data[1]'  and day_status=2";
	
	$sql_data=sql_select($sql);
	foreach($sql_data as $rows)
	{
		$day_status[change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=$rows[csf("day_status")];
		$day_status_days[change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0);
	}
	//List all off days ends
	
	
	if( count($po_break_down_array)>0 )
	{
		if($resource_allocation_type!=1)
			$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and po_break_down_id in (".implode(",",$po_break_down_array).") and status_active=1 and is_deleted=0 and production_date between '".$from_date."' and '".$to_date."'  and sewing_line in ( ".implode(",",$line_names_ids)." )  group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
		else
			$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and po_break_down_id in (".implode(",",$po_break_down_array).") and status_active=1 and is_deleted=0 and production_date between '".$from_date."' and '".$to_date."'  and sewing_line in ( ".implode(",",$line_allocated)." )  group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date";
		//print_r($sewing_resource);  
		
		$sql_data=sql_select($sql);
		$k=0;
		//print_r($po_plan_info);die;
		foreach($sql_data as $rows)
		{
			 //echo $po_plan_info[$rows[csf("po_break_down_id")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]; die;
			//print_r($sewing_resource);  
			if($resource_allocation_type==1)
			{
				$production_details[$po_plan_info[$rows[csf("po_break_down_id")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]+=$rows[csf("production_quantity")];
			
			//[$po_plan_info[$rows[csf("po_break_down_id")]]][$po_plan_info[$rows[csf("sewing_line")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]['prod_qnty']+=$rows[csf("production_quantity")];
			
				$production_details_arr[$sewing_resource[$rows[csf("sewing_line")]]][$rows[csf("po_break_down_id")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]+= $rows[csf("production_quantity")];
				
			}
			else
			{
 				$production_details[$po_plan_info[$rows[csf("po_break_down_id")]][$rows[csf("sewing_line")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]+=$rows[csf("production_quantity")];
			
			//[$po_plan_info[$rows[csf("po_break_down_id")]]][$po_plan_info[$rows[csf("sewing_line")]][$sewing_resource[$rows[csf("sewing_line")]]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]]['prod_qnty']+=$rows[csf("production_quantity")];
			
				$production_details_arr[$rows[csf("sewing_line")]][$rows[csf("po_break_down_id")]][change_date_format( str_replace("'","",trim($rows[csf("production_date")])),'','',0)]+= $rows[csf("production_quantity")];
			}
			 
		}
	}
	// print_r($production_details_arr); 
	foreach($production_details_arr as $id=>$val)
	{
		if($production_details_string=="")
			$production_details_string=$id."_".$val;
		else
			$production_details_string .="**".$id."_".$val;
	}
	
	$tot_month=datediff("m",$from_date,$to_date)+1;
	for( $i=0; $i<$tot_month; $i++ )
	{
		$next_month=month_add($from_date,$i);
		$ldays=cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($next_month)), date("Y",strtotime($next_month)))."-".date("m",strtotime($next_month))."-". date("Y",strtotime($next_month)); // 31
		
		if($i==0) $days[$i]=datediff("d", $from_date, $ldays);
		else if($i==$tot_month-1) $days[$i]=datediff("d", "01-".date("m",strtotime($next_month))."-". date("Y",strtotime($next_month)), $to_date);
		else $days[$i]= cal_days_in_month(CAL_GREGORIAN, date("m",strtotime($next_month)), date("Y",strtotime($next_month)));
	}
	
	if($production_details_arr=='') $production_details_arr='{}';
	if($production_details=='') $production_details='{}';

?>