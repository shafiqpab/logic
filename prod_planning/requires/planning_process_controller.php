<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include( '../../includes/common.php' );
 
$action="process_planning_board";

$tie=time();
//for($i=0; $i<10000000; $i++)
$i=0;/*
while( $i<10000000 )
{
	 $var='sumon';
	 $i++;
}
echo time()-$tie;
die;*/

if( $action=="process_planning_board" )
{
	$con = connect();
	$data=explode("__",$data);
	$resource_allocation_type=return_library_array("select auto_update,company_name from  variable_settings_production where  variable_list=23", "company_name","auto_update");
	//$resource_allocation_type= return_field_value("auto_update", "variable_settings_production"," company_name='".$data[0]."' and variable_list=23");
	
	if($db_type==2)
	{
		$from_date=date('d-M-y',time());
		$to_date=date("d-M-y",strtotime(add_date($from_date,180)));
	}
	else
	{
		$from_date=date('Y-m-d',time());
		$to_date=date("Y-m-d",strtotime(add_date($from_date,180)));
	}
	
 	$from_date='20-Jan-2015';
	$weeklist=return_library_array("select week,week_date from  week_of_year where week_date > '$from_date' order by week_date ", "week_date","week");
	
//List all offdays
	$sql="select a.mst_id,a.month_id,a.date_calc,a.day_status,comapny_id,capacity_source,location_id from  lib_capacity_calc_dtls a, lib_capacity_calc_mst b where b.id=a.mst_id and date_calc between '".$from_date."' and '".$to_date."'  and day_status=2";
	
	$sql_data=sql_select($sql);
	foreach($sql_data as $rows)
	{
		$day_status[$rows[csf("comapny_id")]][change_date_format( str_replace("'","",trim($rows[csf("date_calc")])),'','',0)]=$rows[csf("day_status")];
	}
//List all offdays

	 	 $new_line_resource=return_library_array("select id,line_name from lib_sewing_line      order by sewing_line_serial ", "id","line_name");
	
	foreach($new_line_resource as $ids=>$vals)
		$line_names_ids[$ids]=$ids;
		
		
		if( $resource_allocation_typess!=1 )
			$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and status_active=1 and is_deleted=0 and production_date between '".$from_date."' and '".$to_date."'  and sewing_line in ( ".implode(",",$line_names_ids)." )  group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date"; //and po_break_down_id in (".implode(",",$po_break_down_array).") 
		else
			$sql="select po_break_down_id,sum(production_quantity) as production_quantity,production_date,sewing_line,company_id,location from   pro_garments_production_mst where production_type=5 and status_active=1 and is_deleted=0 and production_date between '".$from_date."' and '".$to_date."'  and sewing_line in ( ".implode(",",$line_allocated)." )  group by production_date,po_break_down_id,sewing_line, company_id,location order by sewing_line,po_break_down_id,production_date"; // and po_break_down_id in (".implode(",",$po_break_down_array).") 
		//print_r($sewing_resource);  
		
		$sql_data=sql_select($sql);
		$k=0;
	 print_r($sql_data);die;
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
	 
	// print_r($production_details_arr); 
	foreach($production_details_arr as $id=>$val)
	{
		if($production_details_string=="")
			$production_details_string=$id."_".$val;
		else
			$production_details_string .="**".$id."_".$val;
	}
echo "<pre>";
 print_r($day_status); die;




	  $sql="select id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,day_wise_plan,company_id,location_id,item_number_id,off_day_plan,order_complexity,ship_date,extra_param from  ppl_sewing_plan_board where  ( start_date > '".$from_date."'  or  end_date > '".$from_date."') order by line_id,start_date,end_date"; //and line_id in (174,175)  
	$sql_data=sql_select($sql);  
	$i=0;
	$rcount=count( $sql_data ) ;
	$line_wise_days=array();
	
	foreach( $sql_data as $rows )
	{
		$i++;
		if( $new_line[$rows[csf('line_id')]]=='' && $i!=1 )
		{
			echo "<br>";
		}
		//$line_wise_days
		$start_date=date("Y-m-d",strtotime( $rows[csf('start_date')] ));
		$end_date=date("Y-m-d",strtotime( $rows[csf('end_date')] ));
		echo $rows[csf('day_wise_plan')]."=".$rows[csf('plan_qnty')]."<br>";
		
		//Check duplicate Plans here to delete them automatically Start
		if( $chk_plan_start[$rows[csf('line_id')]][$start_date]!='' && $chk_plan_end[$rows[csf('line_id')]][$end_date]==$chk_plan_start[$rows[csf('line_id')]][$start_date] )
		{
			$delete[$rows[csf('plan_id')]]=$rows[csf('plan_id')];
		}//Check duplicate Plans here to delete them automatically Ends
		else
		{
			$chk_plan_start[$rows[csf('line_id')]][$start_date]=$rows[csf('plan_id')];
			$chk_plan_end[$rows[csf('line_id')]][$end_date]=$rows[csf('plan_id')];
			
			for( $k=0; $k< $rows[csf('duration')]; $k++ )
			{
				$ndate=date("Y-m-d",strtotime(add_date($start_date,$k)));
				if($line_wise_days[$rows[csf('line_id')]][$ndate][$rows[csf('plan_id')]]=='')
				{
					$line_wise_days[$rows[csf('line_id')]][$ndate][$rows[csf('plan_id')]]=$rows[csf('plan_id')];
				}
				
				if($chk_plan[$rows[csf('line_id')]][$ndate]!='') 
				{ 
					echo "duplicate"."="; 
					 $delete[$rows[csf('plan_id')]]=$rows[csf('plan_id')]; 
				}
				echo $k."=".$ndate.'='.$rows[csf('plan_id')].'='.$rows[csf('line_id')].'='.date("d-M-y",strtotime( $ndate ))."<br>";
				$chk_plan[$rows[csf('line_id')]][$ndate]=$rows[csf('plan_id')];
			}
			echo "<br>";
			//if($i==5) die;
			if( $i==$rcount )
			{
				//echo "<br>";
				//$new_line[$rows[csf('line_id')]]=$rows[csf('line_id')];
			}
		}
		//echo $rows[csf('line_id')]."==";
		$new_line[$rows[csf('line_id')]]=$rows[csf('line_id')];
	}
	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 $rID1=execute_query("delete from ppl_sewing_plan_board where plan_id in (".implode(",",$delete).")");
		 	
		if($db_type==0)
		{
			mysql_query("COMMIT");  
			 
		}
		if($db_type==2 || $db_type==1 )
		{
			oci_commit($con);  
			  
		}
		//echo "SDSD";
	 
	
	//0**109**5941**0**19-04-2016**0**19-04-2016**0**1**500**1**1000**100**1200**0**19-04-2016**19-04-2016**921750-1676 S-4**5.95**5**-134524**01-04-2016**2**01-04-2016**20160418**H n M**5.95**Moa Tank Top**0**12-04-2016
	echo '<pre>';
		print_r($delete);
 //	print_r($line_wise_days);
}

?>