<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
require_once('../../../../includes/common.php');
 


extract($_REQUEST);

$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
//$location_arr = return_library_array("select id,location_name from lib_location","id", "location_name"); 
//$floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id", "floor_name"); 


if($action=="report_generate_sheet_kal") //Monthly KPI PER% of KAL
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$cbo_templete_id 	= str_replace("'","",$cbo_templete_id);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	//$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	 
	$fiscal_year_arr = array();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	  //echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	//$startDate="28-May-2023";
	 //echo $startDate.'='.$endDate;die();
	 $company_conds="";
	 if($cbo_company_id>0)
	 {
		$company_conds=" and a.company_name =$cbo_company_id";
	 }
	//$location_library = return_library_array("select id, location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ========================= for Plan ======================
	  $sql_po_plan="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id=48   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0  and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result = sql_select($sql_po_plan);
	foreach ($sql_po_plan_result as $val) 
	{
		$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
		//$plandate=date('d-m-Y', strtotime($val['PLAN_DATE']));
		$plandate=strtotime($val['PLAN_DATE']);
		//$plan_qty_array[$plandate][$val['POID']][$val['COMPANY_ID']]['planQty']+=$val['PLAN_QTY'];
		 $plan_poIdArr[$val['POID']]=$val['POID'];
		// $poIdArr[$val['POID']]=$val['GROUPING'];
		 $com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=89");
	 //oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 1, $plan_poIdArr, $empty_arr);//PO ID Ref from=1
  $sql_po_plan_current="SELECT a.company_name as COMPANY_ID,b.id as POID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and c.task_id=48   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0 $company_conds and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result_current = sql_select($sql_po_plan_current);
	//$previ_plan_cumulative_cal=0;
	foreach ($sql_po_plan_result_current as $val) 
	{
		$plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		//echo $plandate.'<br>';;
		$plan_qty_array[1][$plandate][$val['POID']][$val['COMPANY_ID']]['planQty']+=$val['PLAN_QTY'];
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
	}
	unset($sql_po_plan_result_current);
   
	$sql_po_allocate="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.QNTY,c.allocation_date as ALLOC_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, inv_material_allocat_hystory c where a.id=b.job_id and b.id=c.po_break_down_id and  b.status_active=1 and b.is_deleted=0   and c.allocation_date between '$startDate' and '$endDate' $company_conds order by c.allocation_date asc"; 
	$sql_po_allocate_result = sql_select($sql_po_allocate); //and b.id in(20325,20326,20327,20328,20329,20330)
	foreach ($sql_po_allocate_result as $val) 
	{
		//$allocatedate=date('d-m-Y', strtotime($val['ALLOC_DATE']));
		//$allocatedate=strtotime($val['ALLOC_DATE']);
		$allocat_poIdArr[$val['POID']]=$val['POID'];
		//$Allocat_dateArr[$allocatedate]=$allocatedate;
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		// $poIdArr[$val['POID']]=$val['GROUPING'];
		  $com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 
	}
	
	 fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 2, $allocat_poIdArr, $empty_arr);//PO ID Ref from=2

	  $curr_sql_po_allocate="SELECT a.company_name as COMPANY_ID,b.id as POID, c.QNTY,c.allocation_date as ALLOC_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, inv_material_allocat_hystory c,gbl_temp_engine g  where a.id=b.job_id and b.id=c.po_break_down_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=89  and c.allocation_date <= '$endDate' and  b.status_active=1 and b.is_deleted=0   $company_conds order by c.allocation_date,c.id asc";
	$sql_po_allocate_result_curr = sql_select($curr_sql_po_allocate); 
	foreach ($sql_po_allocate_result_curr as $val) 
	{
		$allocatedate=date('d-M-Y',strtotime($val['ALLOC_DATE']));
		$plan_qty_array[1][$allocatedate][$val['POID']][$val['COMPANY_ID']]['alloQty']+=$val['QNTY'];
	}
	unset($sql_po_allocate_result_curr);
	
	// echo "<pre>";
	// print_r($plan_qty_array);
	
	//===================Allocation Wise Calculation=======
	
	 
  // echo "<pre>";
//print_r($till_today_planArr);
 //ksort($till_today_AllQtyArr);
 // =============================Calculation of KPI============= //plan asy  allocatio ni  day dorbo
 $company_kip_cal_Arr=array();$plan_cumulative_cal=0;$allo_cumulative_cal=0;//$company_wise_arr=array();
ksort($plan_qty_array);

	unset($sql_po_plan_result);
	unset($sql_po_allocate_result);
	 
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=89");
	oci_commit($con);
	disconnect($con); 

	$unitArray=array(1=>"Tejgaon Yarn",2=>"Shafipur",3=>"Ashulia",4=>"Ratanpur",5=>"Merchandising",6=>"Average");
	$avg_company_kip_perArr=array();$tot_avg_kpiArr=array();//$avg_allo_plan_Day_wise_calArr=array();
	foreach ($company_wise_arr as   $com_id) 
	{
		foreach ($unitArray as $unit_id => $unit_val) 
		{	
			 foreach ($fiscalMonth_arr as $year_mon => $val) 
			{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					
					//Tejgoan Yarn
					if($unit_id==1)
					{
						$diff_days=datediff('d',$from_date,$to_date);
						$company_kip_cal_Arr=array();//$po_planQtyArr=array();
						$plan_cumulative_cal=0;$allo_cumulative_cal=0;$tot_kpi_cal = 0;
						$allo_Day_wise_calArr=array();
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate = date('d-M-Y', strtotime("+1 day", strtotime($newdate)));
							}
							
							//echo $new_date = date('d-M-Y', strtotime($from_date) + (3600*24));
							//echo "<br />";
							//$newdate =change_date_format($from_date,'','',1);
							$kpi_dates=strtoupper(date('d-m-Y',$newdate)); //previ_plan_cumulative_cal
							$till_mon_day_chkPlanArr=array();
							$po_planQty=$po_alloQty=array(); $mon_allocat_cumulative_cal=$mon_plan_cumulative_cal=0;
							$plan_Day_wise_calArr=array();
							
							foreach($plan_qty_array[$unit_id][$newdate] as $poid=>$poData)
							{
								$planQty=$alloQty=0;
								$planQty=$poData[$com_id]['planQty'];
								$alloQty=$poData[$com_id]['alloQty'];
								if($alloQty=='') $alloQty=0;
								if($planQty>0)
								{
									// echo $newdate."=".$poid."=".$planQty."+".$po_planQty_arr[$prev_date[$poid]][$poid]."<br />";
									$po_planQty_arr[$newdate][$poid]=$planQty+$po_planQty_arr[$prev_date[$poid]][$poid];
									$prev_date[$poid] = $newdate;
								}
								if($planQty>0 || $alloQty>0)
								{
									if($planQty>0 && $alloQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_alloQty_arr[$newdate][$poid]=$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
										//echo "A=".$poid;
									}
									 
									if($planQty==0 && $alloQty>0) 
									{
										$po_alloQty_arr[$newdate][$poid]=$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planQtycal=0;
									$planQtycal=$plan_qty_array[1][$newdate][$poid][$com_id]['planQty'];//Plan
									
									if($plan_qty_array[$unit_id][$newdate][$poid][$com_id]['alloQty']!="" || $planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										
										$po_alloQty_arr[$newdate][$poid]=$alloQty+$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
										
									}
									
								}
								
							} 
							
						} //Days loop end
				   } 
				   
				   
			} 
			//Month Loop end here //Month Loop
			
			
		} //unit
		//  echo "<pre>";
		// print_r($po_alloQty_arr);
		 //  print_r($po_planQty_arr);
		//print_r($num_of_plan_days);
		 // echo "</pre>";
	} //Company
	 //print_r($avg_tot_kpi_calArr);
	  //echo $ttt.'d';
	 	 $month_wise_kpiArr=array();
	  	foreach($com_poIdArr as $comp_id=>$comData)
		{
		 foreach($comData as $poid=>$IR)
	     {
				 
            	 foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
					$company_kip_cal_Arr=array();//$po_planQtyArr=array();
					 
						//if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						//{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_all = date('d-M-Y', strtotime($from_date));
									//$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
									//$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}

								//echo $day_all.'='.$po_alloQty_arr[$day_all][$poid].'/'.$po_planQty_arr[$day_all][$poid].'<br>';
								$planQtycal=0;
								$planQtycal=$plan_qty_array[1][$day_all][$poid][$comp_id]['planQty'];//alloQty
								//$kom_kpi_per=$po_alloQty_arr[$day_all][$poid]/$po_planQty_arr[$day_all][$poid]*100;
								//$kom_kpi_per = ($kom_kpi_per>100)?100:$kom_kpi_per;
								if($planQtycal>0)
								{
									//$mon_wiseAlloPlanQty_tillCalculateArr[$day_all]['alloc_qty']+=$po_alloQty_arr[$day_all][$poid];
									$mon_alloc_qty=$po_alloQty_arr[$day_all][$poid];
									$month_wise_kpiArr[$comp_id][$day_all]['allo']+=$mon_alloc_qty;
								}
								$month_wise_kpiArr[$comp_id][$day_all]['plan']+=$po_planQty_arr[$day_all][$poid];
							//	$mon_wise_allocateQty_tillCalculateArr[$day_all]+=$po_alloQty_arr[$day_all][$poid];
							 
						 }
					 //}
					
				}
		 }
	   }
	   foreach($month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
		   foreach($comData as $day_key=>$row)
		   {
			  $month_allo=$row['allo']; 
			   $month_plan=$row['plan'];
			 
			   $month_kpi_per=$month_allo/$month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				
				//$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
				if($month_allo>0 && $month_plan>0)
				{
					if($month_kpi_per>100)  $month_kpi_per=100;
					 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
				$yarn_month_wise_kpiArr[$comp_id][1][$yr_month]+=$month_kpi_per;
				}
				if($month_plan>0)
				{
					$num_of_plan_days[$yr_month]++;
				}
				
		   }
			   
	   }
	   $comp_avg_perArr=array();
	   foreach($yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
		foreach($comData as $unit_id=>$monData) //Month Wise KPI Percentage summary part
		{
			foreach($monData as $monYr=>$per) //Month Wise KPI Percentage summary part
			{
				$comp_avg_perArr[$comp_id][$unit_id]+=$per/$num_of_plan_days[$monYr];
			}
		}

	   }
	    // echo "<pre>";
	  // print_r($comp_avg_perArr);
	  //print_r($mon_wise_allocateQty_tillCalculateArr);
	    //  echo "<pre>"; 


	//$unit_width=count($unitArray)*80;
//	$mon_width=count($fiscalMonth_arr)*80;
	$tbl_width = 240+(count($fiscalMonth_arr)*80);
	ob_start();	
	
	$tbl_width2 = 5000;
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total PO wise dtls Start============================= style="display:none" =====================================-->

	   <!-- <table width="<? //echo $tbl_width;?>"  cellspacing="0">-->
       <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" style="display:none" cellpadding="0" cellspacing="0">
	        
	        <!--<tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title;?> </strong></td>
	        </tr>-->
            <tr>
            <thead>
             <th width="70">IR </th>
             <?	
			 ksort($com_poIdArr);
             	foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
					$company_kip_cal_Arr=array();//$po_planQtyArr=array();
					$plan_cumulative_cal=0;$allo_cumulative_cal=0;$tot_kpi_cal = 0;
					$allo_Day_wise_calArr=array();//MAY-2023 JUN-2023
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_count = date('d-M-Y', strtotime($from_date));
									$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_count = date('d-M-Y', strtotime("+1 day", strtotime($day_count)));
									$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}
				 ?>
					  <th width="80"> <?=$day_name; ?> </th>
             <?
						 }
					 }
				}
			 ?>
              </thead>
            </tr>
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$IR)
			     {
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
             $comp_id=2;
             foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
					$company_kip_cal_Arr=array();//$po_planQtyArr=array();
					$plan_cumulative_cal=0;$allo_cumulative_cal=0;$tot_kpi_cal = 0;
					$allo_Day_wise_calArr=array();//MAY-2023 JUN-2023
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_count = date('d-M-Y', strtotime($from_date));
									$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_count = date('d-M-Y', strtotime("+1 day", strtotime($day_count)));
									$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}
								
								 
								$planQty_cal=$alloQty_cal=0;
								$planQty_cal=$plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$po_planQty_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$po_planQty_arr[$day_count][$poid]; ?> </td>
             <?
							 
						 }
					 }
					
				}
			 ?>
             
            </tr>
            <?
			 	$p++;
					}
				}
			//PO end
			?>
            <tr bgcolor="#999966">
            <td><b> Till Plan</b></td>
            <?
            foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
					$company_kip_cal_Arr=array();//$po_planQtyArr=array();
					$plan_cumulative_cal=0;$allo_cumulative_cal=0;$tot_kpi_cal = 0;
					$allo_Day_wise_calArr=array();//MAY-2023 JUN-2023
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_count = date('d-M-Y', strtotime($from_date));
									$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_count = date('d-M-Y', strtotime("+1 day", strtotime($day_count)));
									$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}
								
				 ?>
					  <td width="80" title=""> <?=$po_planQty_tillCalculteArr[$day_count];  ?> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <!--Plan End-->
              <? 
			  $a=1;
			  $po_alloQty_tillCalculteArr=array();
			  foreach($com_poIdArr as $comp_id=>$comData)
				{
				  foreach($comData as $poid=>$IR)
			      {
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
              
             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trallo_<? echo $a; ?>','<? echo $bgcolor; ?>')" id="trallo_<? echo $a; ?>"> 
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
             
             foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
					$company_kip_cal_Arr=array();//$po_planQtyArr=array();
					$plan_cumulative_cal=0;$allo_cumulative_cal=0;$tot_kpi_cal = 0;
					$allo_Day_wise_calArr=array();//MAY-2023 JUN-2023
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_count = date('d-M-Y', strtotime($from_date));
									$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_count = date('d-M-Y', strtotime("+1 day", strtotime($day_count)));
									$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}
								$planQtycal=0;
								$planQtycal=$plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								$alloQty=0;
								$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								
								$alloQty_cal=0;
								if($po_planQty_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$po_alloQty_arr[$day_count][$poid];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$alloQty;  ?>"> <?=$alloQty_cal;  ?> </td>
             <?
						 }
					 }
					 
				}
			 ?>
             
            </tr>
            <?
			$a++;
				  }
			}
			//po id load end
			?>
            <tr bgcolor="#99CC99">
            <td> Till Allcation</td>
            <?
            foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
				
					
					$allo_Day_wise_calArr=array();//MAY-2023 JUN-2023
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_count = date('d-M-Y', strtotime($from_date));
									$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_count = date('d-M-Y', strtotime("+1 day", strtotime($day_count)));
									$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}
								 
				 ?>
					  <td width="80" title=""> <?=$po_alloQty_tillCalculteArr[$day_count]; ?> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
            <td> KPI Per</td>
             <?
            foreach ($fiscalMonth_arr as $year_mon => $val) 
				{
					$start_date=date('Y-m', strtotime($year_mon));
					$start_date=$start_date.'-01';
					$from_date=change_date_format($start_date,'','',1);
					$last_day=date('t', strtotime($year_mon));
					$last_date_cal=date('Y-m', strtotime($year_mon));
					$last_date=$last_date_cal.'-'.$last_day;
					$to_date=change_date_format($last_date,'','',1);
					$diff_days=datediff('d',$from_date,$to_date);
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023')
						{
							for($j=0;$j<$diff_days;$j++)
							{
								if($j==0)
								{
									$day_count = date('d-M-Y', strtotime($from_date));
									$day_name = date('d-M-y', strtotime($from_date));
								}
								else
								{
									$day_count = date('d-M-Y', strtotime("+1 day", strtotime($day_count)));
									$day_name = date('d-M-y', strtotime("+1 day", strtotime($day_name)));
								}
							$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
							$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
							 
								 
				 ?>
					  <td width="80" title="Allocation/Plan*100"> <?=fn_number_format($company_kpi_per,2); ?> </td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
            
	    </table>
        <?
       // die;
		?>
          <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title;?> </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>KPI Company Wise[<? echo $from_year; ?>]</b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		<!-- <caption></caption> -->
	        <thead>
	            <th width="80">Company</th>
	            <?
				rsort($company_wise_arr);
	            ?>
	            <th width="80">KPI Unit</th>
                <th width="80">Average</th>
				<?
				foreach ($fiscalMonth_arr as $year_mon => $val) 
	            {?>
                <th width="80"><?=$year_mon;?></th>
				<?
				}
				?>
               
	        </thead>
		        <tbody>   
		        <?
				foreach ($company_wise_arr as  $com_key) 
				{
					$com_span=0;
					foreach ($unitArray as $unit_id => $unit_val) 
					{
						$com_span++;
					}
					$companySpanArr[$com_key]=$com_span;
					//echo  $com.'D';
				}
		        	 
					 $total_avg_kpi=0;$i=1;$tot_avg_kpiArr=array();$tot_mon_kpiPerArr=array();$tot_overall_kpiPerArr=array();$tot_avg_kpiArr=array();
		        	foreach ($company_wise_arr as   $com_id) 
		        	{
						$j=1;
						foreach ($unitArray as $unit_id => $unit_val) 
		        		{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 	        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
						<?
						if($j==1)
						{
							?>
								 <td rowspan="<? echo $companySpanArr[$com_id];?>"><? echo $company_arr[$com_id];?></td>
						<? }
						
				            
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
						   if($unit_id!=6)
						   {
							?>
							  <td align="center" title="<?=$unit_id;?>"><a href="javascript:void()" onclick="report_generate_by_year('<? echo $com_id.'_'.$unit_id.'_'.$year_mon;?>','1')"><? echo $unit_val;?></a></td>
							<?
						   }
						   else
						   { ?>
								 <td align="center" bgcolor="#CCCCCC" > <? echo $unit_val;?></td>
						  <? }

							if($unit_id!=6) //$comp_avg_perArr[$comp_id][$unit_id]
							{
								?>
								<td align="center" title="Tot KPI/12">   <?  echo fn_number_format($comp_avg_perArr[$com_id][$unit_id]/12,2).'%'; ?></td>
								<?
								$tot_avg_kpiArr[6][$com_id]+=$comp_avg_perArr[$com_id][$unit_id]/12;
								//echo $avg_tot_mon_kpiPerArr[$com_id][$unit_id].'dss'; 
							}
							else //$tot_avg_kpiArr[$unit_id][$com_id]
							{ ?>
								<td align="center" bgcolor="#CCCCCC"  title="Unit Avg(<?=$tot_avg_kpiArr[$unit_id][$com_id];?>)/5"> <?  echo fn_number_format($tot_avg_kpiArr[$unit_id][$com_id]/5,2).'%'; ?></td>
							<? 
							$total_avg_kpi+=$tot_avg_kpiArr[$unit_id][$com_id]/5;
							}
							
							foreach ($fiscalMonth_arr as $year_mon => $val) 
							{
								// $start_date=date('Y-m', strtotime($year_mon));
								// $start_date=$start_date.'-01';
								// $from_date=change_date_format($start_date,'','',1);
								// $last_day=date('t', strtotime($year_mon));
								// $last_date_cal=date('Y-m', strtotime($year_mon));
								// $last_date=$last_date_cal.'-'.$last_day;
								// $to_date=change_date_format($last_date,'','',1);
								
								//Tejgoan Yarn
								$yarn_month_wise_kpi=0;
								if($unit_id==1)
								{
									$diff_days=$num_of_plan_days[$year_mon];
									// echo $year_mon . "==";
									$yarn_month_wise_kpi =$yarn_month_wise_kpiArr[$com_id][$unit_id][$year_mon]/$diff_days;//$month_wise_kpi[$unit_id][$year_mon]/$diff_days;
							    } 
								//Tejgao Yarn
							   
								if($unit_id!=6)
								{
									//$mon_company_kip_per=$company_kip_cal_Arr[$unit_id][$com_id][$year_mon]['kpi_per']/$company_kip_cal_Arr[$unit_id][$com_id][$year_mon]['kpi_days'];
									if($yarn_month_wise_kpi>0)
									{
									$tot_mon_kpiPerArr[$com_id][$year_mon]+=$yarn_month_wise_kpi;
									
									
									}
								?>
								<td width="80" align="center" title="<?=$unit_id.',Tot Days='.$diff_days;?>" ><?  if($yarn_month_wise_kpi>0) echo fn_number_format($yarn_month_wise_kpi,2).'%';else echo "";?></td>
								<?
								}
								else
								{ ?>
									<td width="80" align="center" title="Date Wise Avg(<?=$tot_mon_kpiPerArr[$com_id][$year_mon];?>)/5" bgcolor="#CCCCCC" ><?  if($tot_mon_kpiPerArr[$com_id][$year_mon]>0) echo fn_number_format($tot_mon_kpiPerArr[$com_id][$year_mon]/5,2).'%';?></td>
									<?
									if($tot_mon_kpiPerArr[$com_id][$year_mon]>0)
									{
									$tot_overall_kpiPerArr[$year_mon]+=$tot_mon_kpiPerArr[$com_id][$year_mon]/5;
									}
								}
							} //Month Loop end here
							?>
                           
				            
				        </tr>
				        <?
				       // $total_avg_kpi += $dying_floor_tot+$main_array[$year]['subFinish'];//$main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				      
						$j++;$i++;
				        
				    } //Unit
				} //Company
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right" colspan=2>Overall Planning KPI</th>
				 
	            
                <th width="80" align="center"><? echo fn_number_format($total_avg_kpi/2,2).'%';?></th>
				<?
				foreach ($fiscalMonth_arr as $year_mon => $val) 
	            {?>
                <th width="80" align="center" title="All avg kpi/2"><? echo fn_number_format($tot_overall_kpiPerArr[$year_mon]/2,2).'%';?></th>
				<?
				}
				?>
				 
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}  

if($action=="report_generate_by_year_sheet_kal") //Top Sheet Kal Monthly
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";
	}
	$sql_floor=sql_select("select id as ID,floor_name as FLOOR_NAME from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row['ID']]['floor']=$row['FLOOR_NAME'];
		$floor_library[$row['ID']]['floor_id']=$row['ID'];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select COST_PER_MINUTE, APPLYING_PERIOD_DATE, APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row['APPLYING_PERIOD_DATE'],'','',1);
			$applying_period_to_date=change_date_format($row['APPLYING_PERIOD_TO_DATE'],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row['COST_PER_MINUTE'];
			}
		}
	
	//$sql_fin_prod="SELECT a.location,a.po_break_down_id";
	  $sql_fin_prod=" SELECT b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID,a.embel_name as EMBEL_NAME,a.location as LOCATION,a.po_break_down_id as POID,a.item_number_id as ITEM_ID,
to_char(a.production_date,'MON-YYYY') as MONTH_YEAR,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS MSEW_OUT,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS MPRINT_RECV,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS MEMBRO_RECV,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS MWASH_RECV
	 
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5,3) and b.production_qnty>0 and a.production_source in(1)  and a.location>0   ";

	//and a.location in(3,5)
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val['POID']] = $val['POID'];
		//if($val['LOCATION']==3 || $val['LOCATION']==5)
		//{
		//$sewing_qty_array[$val['POID']] += $val['MSEW_OUT'];
		//}
	}

	// ========================= for kniting ======================
	
	 $sql_kniting_dyeing=" SELECT b.po_breakdown_id as POID,b.is_sales as IS_SALES,a.febric_description_id as DETER_ID,to_char(c.receive_date,'MON-YYYY') as MONTH_YEAR,
 (b.quantity) as GREY_RECEIVE_QNTY from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and c.entry_form in(2)  order by b.po_breakdown_id asc";

	/*$sql_kniting_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'YYYY') as year,sum(a.grey_receive_qnty) as qty from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";*/
	// echo $sql_kniting_dyeing;die();
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
		 $po_id_array[$val['POID']] = $val['POID'];
		}
	}
	// ========================= for dyeing ======================
	
	$dying_prod_sql=" SELECT a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'MON-YYYY') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY,f.detarmination_id as DETER_ID  from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d,product_details_master f where c.batch_id=a.id  and a.id=d.mst_id and c.batch_id=d.mst_id and f.id=d.prod_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against=1   and c.service_company in($cbo_company_id)  and c.process_end_date between '$startDate' and '$endDate' and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id,c.process_end_date ";
	$dying_prod_sql_res = sql_select($dying_prod_sql);
	foreach ($dying_prod_sql_res as $val) 
	{
		$dying_is_sales_id=$val['IS_SALES'];
		if($dying_is_sales_id==1)
		{
		$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
		$po_id_array[$val['POID']] = $val['POID'];
		}
		
		$batch_array[$val['FLOOR_ID']][$val['MONTH_YEAR']]+= $val['BATCH_QNTY'];
	}
	
	$sub_dying_prod_sql=" SELECT a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'MON-YYYY') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d where c.batch_id=a.id  and a.id=d.mst_id and c.batch_id=d.mst_id and c.entry_form=38 and c.load_unload_id=2 and a.batch_against=1  and c.service_company in($cbo_company_id)  and c.process_end_date between '$startDate' and '$endDate' and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id,c.process_end_date ";
	$sub_dying_prod_sql_res = sql_select($sub_dying_prod_sql);
	foreach ($sub_dying_prod_sql_res as $val) 
	{
		$sub_po_id_array[$val['POID']] = $val['POID'];
		$batch_array[$val['FLOOR_ID']][$val['MONTH_YEAR']]+= $val['BATCH_QNTY'];
	}

	/*$poIdCond=where_con_using_array($po_id_array,0,'b.id');
	$poIds = implode(",", array_unique($po_id_array));
	$salesIds = implode(",", array_unique($sales_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	if($salesIds !="")
	{
		$sales_cond="";
		if(count($sales_id_array)>999)
		{
			$chunk_arr=array_chunk($sales_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sales_cond=="") $sales_cond.=" and ( a.id in ($ids) ";
				else
					$sales_cond.=" or   a.id in ($ids) "; 
			}
			$sales_cond.=") ";

		}
		else
		{
			$sales_cond.=" and a.id in ($salesIds) ";
		}
	}*/
	// =================================== SubCon Sewout =============================
	 
	   $sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id as LOCATION_ID,to_char(a.production_date,'MON-YYYY') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY  from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.status_active=1 and a.location_id in(3,5)";
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	foreach ($sql_sub_sewOut_result as $val) 
	{
		$sub_po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
	}
	// =================================== subcon kniting =============================
	 $sql_sub_knit=" SELECT b.order_id as ORDER_ID,b.process as PROCESS,to_char(a.product_date,'MON-YYYY') as MONTH_YEAR,
	  (CASE WHEN a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS SUB_KNITTING_PROD,
	  (CASE WHEN a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id)  THEN b.product_qnty END) AS FIN_PROD
	  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4)  and a.status_active=1  and b.status_active=1 ";
	//entry_form=292, product_type=4
	
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	foreach ($sql_sub_knit_res as $val) 
	{
		$sub_po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
	}
	//==============Temp table===============
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
			
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 2, $sales_id_array, $empty_arr);//Sales ID Ref from=2
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	
	 $sql_sales = "select  b.id as DTLS_ID,a.id as ID,a.job_no as JOB_NO, a.within_group as WITHIN_GROUP,b.color_id as COLOR_ID,b.determination_id as DETER_ID,b.process_id as PROCESS_ID,b.process_seq as PROCESS_SEQ,b.body_part_id as BODY_PART_ID from fabric_sales_order_mst a,fabric_sales_order_dtls b,gbl_temp_engine g where a.id=b.mst_id and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sales_cond order by b.id desc";
		$sql_sales_result = sql_select($sql_sales); // and a.company_id in($cbo_company_id)
		foreach ($sql_sales_result as $val) 
		{		
				if($val['PROCESS_SEQ'])
				{
					$process_id=array_unique(explode(",",$val['PROCESS_ID']));
					$process_seqArr=array_unique(explode(",",$val['PROCESS_SEQ']));
					foreach($process_id as $p_key)
					{
							foreach($process_seqArr as $val_rate)
							{
								$process_Rate=explode("__",$val_rate);
								$process_Id=$process_Rate[0];
								$process_rate=$process_Rate[1];
								if($p_key==$process_Id && $process_rate>0)
								{
								$sales_data_array[$val['ID']][$val['DETER_ID']][$val['COLOR_ID']][$p_key]['process_rate'] = $process_rate;
								$sales_data_knit_array[$val['ID']][$val['DETER_ID']][$p_key]['process_rate'] = $process_rate;
								}
							}
					}
				}
		}
		
	//$cm_po_cond = str_replace("id", "b.id", $po_cond);
	//$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	//$sql_sew_po="SELECT b.id,b.po_quantity,b.pub_shipment_date,b.shipment_date,b.job_no_mst from wo_po_break_down b where b.status_active=1 and b.is_deleted=0 $po_cond";
	 $sql_sew_po="SELECT b.id as POID,b.po_quantity as POQTY,b.pub_shipment_date as PUBSHIPDATE,b.shipment_date as SHIPDATE,b.job_no_mst as JOB_NO from wo_po_break_down b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and  b.status_active=1 and b.is_deleted=0 "; 
	 
	$sql_po_sew_result = sql_select($sql_sew_po);
	foreach ($sql_po_sew_result as $val) 
	{
		$po_qty_array[$val['POID']] = $val['POQTY'];
		$po_job_array[$val['POID']]= $val['JOB_NO'];
		$po_date_array[$val['JOB_NO']]['ship_date'].= $val['SHIPDATE'].',';
		$po_date_array[$val['JOB_NO']]['pub_date'].= $val['PUBSHIPDATE'].',';
		
	}
	unset($sql_po_sew_result);
	
	//$cm_po_cond3 = str_replace("id", "c.po_break_down_id", $po_cond);
	//$cm_po_cond3 = str_replace("id", "b.po_id", $po_cond);
	
 /* $sql_pre_wash="SELECT  b.emb_name,d.id,d.color_number_id,d.po_break_down_id as po_id,c.id as color_size_id,d.rate from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b where d.job_id=a.id   and c.job_no_mst=a.job_no and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id  and b.id=d.pre_cost_emb_cost_dtls_id and  b.job_id=a.id  and  b.job_id=c.job_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.requirment>0 and d.rate>0  $cm_po_cond3 order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name b.color_size_break_down_id,a.embel_name,
	{
		$po_color_rate_array[$val[csf('emb_name')]][$val[csf('po_id')]][$val[csf('color_number_id')]]['rate'] = $val[csf('rate')];
		$po_color_array[$val[csf('color_size_id')]]['color_id'] = $val[csf('color_number_id')];
	}
	unset($sql_wash_result);*/
	
	/* $sql_pre_wash="SELECT  b.emb_name as EMB_NAME,d.id as AVG_ID,d.color_number_id as COLOR_ID,d.po_break_down_id as POID,c.id as COLOR_SIZE_ID,d.rate as RATE from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b,gbl_temp_engine g where d.job_id=a.id   and c.job_no_mst=a.job_no and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id  and b.id=d.pre_cost_emb_cost_dtls_id and  b.job_id=a.id  and  b.job_id=c.job_id and g.ref_val=c.po_break_down_id  and g.ref_val=d.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.requirment>0 and d.rate>0   order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
	}
	unset($sql_wash_result);*/
	$sql_pre_wash="SELECT b.emb_name as EMB_NAME, d.id as AVG_ID, d.color_number_id as COLOR_ID, d.po_break_down_id as POID, d.color_size_table_id as COLOR_SIZE_ID, d.rate as RATE 
	from wo_pre_cost_embe_cost_dtls b, wo_pre_cos_emb_co_avg_con_dtls d, gbl_temp_engine g 
	where b.id=d.pre_cost_emb_cost_dtls_id and d.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.requirment>0 and d.rate>0 order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
	}
	unset($sql_wash_result);
	

	
	// $sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,f.id as conv_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond order by f.id,b.id asc";
	
	// $sql_po="SELECT b.id as POID,c.color_number_id as COLOR_ID,d.color_size_sensitive as COLOR_SIZE_SENSITIVE,d.lib_yarn_count_deter_id as DETER_ID,f.cons_process as CONS_PROCESS,f.id as CONV_ID,f.charge_unit as CHARGE_UNIT,f.color_break_down as COLOR_BREAK_DOWN from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f,gbl_temp_engine g where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and b.id=g.ref_val and g.ref_val=c.po_break_down_id and g.ref_val=e.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  order by f.id,b.id asc"; 
	$sql_conv="SELECT d.color_size_sensitive as COLOR_SIZE_SENSITIVE, d.lib_yarn_count_deter_id as DETER_ID, e.po_break_down_id as POID, e.color_number_id as COLOR_ID, f.id as CONV_ID, f.cons_process as CONS_PROCESS, f.charge_unit as CHARGE_UNIT, f.color_break_down as COLOR_BREAK_DOWN 
	
	from wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e, wo_pre_cost_fab_conv_cost_dtls f, gbl_temp_engine g 
	
	where d.id=e.pre_cost_fabric_cost_dtls_id and d.id=f.fabric_description and e.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 order by f.id, e.po_break_down_id asc";
	 
	 
	$sql_conv_result = sql_select($sql_conv);
	foreach ($sql_conv_result as $val) 
	{
		if($val['COLOR_SIZE_SENSITIVE']==3)
		{
		$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'] = $val['COLOR_SIZE_SENSITIVE'];
		}
		if($val['COLOR_BREAK_DOWN']!="")
		{
		$color_break_down=$val['COLOR_BREAK_DOWN'];
		}
		
		if($val['CONS_PROCESS']==31 && $color_break_down!='')
		{
			//$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];
			if($val['COLOR_SIZE_SENSITIVE']==3) //Contrst
			{ //po_color_fab_brk_array
				$po_color_fab_brk_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
				// echo $val[csf('color_break_down')].', ';
			}
			else
			{
				$po_color_fab_brk_array[$val['POID']][$val['COLOR_ID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
			}
			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
				if($arr_2[1])
				{
				$po_color_fab_array[$val['POID']][$arr_2[3]][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$arr_2[1];
				}
			
			}
		}
		else if($val['CONS_PROCESS']==33) //Heatset
		{
			$po_color_fab_array2[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$val['CHARGE_UNIT'];
		}
		else
		{
			$po_color_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] = $val['CHARGE_UNIT'];
		}
		//if($val[csf('cons_process')]==1)
		//{
		$po_color_knit_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['knit_rate'] = $val['CHARGE_UNIT'];
		//}
		
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	/*$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	}*/
	$cm_sql = "SELECT c.costing_date as COSTING_DATE,c.costing_per as COSTING_PER,c.sew_smv as SEW_SMV,a.cm_cost as CM_COST,b.id as POID,d.smv_set as SMV_SET,d.gmts_item_id as GMTS_ID from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d,gbl_temp_engine g where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and d.job_id=b.job_id and d.job_id=c.job_id  and g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and a.status_active=1 and b.status_active=1 and c.status_active=1  ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val['POID']] = $val['CM_COST'];
		$pre_cost_smv_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_cost_array[$val['POID']]['costing_date'] = $val['COSTING_DATE'];
		$costing_per_arr[$val['POID']]= $val['COSTING_PER'];
	}
	
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");

	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();*/
	// var_dump($condition);
	//$conversion= new conversion($condition);
	//$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	//$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	/*$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
 //echo "<pre>";print_r($emblishment_costing_arr_wash);die();
	// =========================== getting subcon order qty ====================================
	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//==================================== subcon order data =============================
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	$sql_subcon_po="SELECT b.id as ID,b.rate as RATE from subcon_ord_dtls b,gbl_temp_engine g where  g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0";
	$sql_subcon_po_res = sql_select($sql_subcon_po);
	foreach($sql_subcon_po_res as $row)
	{
		$order_wise_rate[$row['ID']]=$row['RATE'];
	}
	unset($sql_subcon_po_res);
	// print_r($order_wise_rate);
	
	// =================================== subcon dyeing =============================
	$main_array = array();
	$locationArray = array();
	$year_prod_cost_arr=array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		
			$fyear=$val["MONTH_YEAR"];
			if($val['LOCATION']==3 || $val['LOCATION']==5)
			{
				$main_array[$fyear]['qty']+=$val['MSEW_OUT'];//msew_out
				$main_array[$fyear]['location'] = $val['LOCATION'];
			}//EMBEL_NAME
			/*$print_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][1];
			$print_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][1];
			$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];*/
			//echo $print_cost.'D';
			$dzn_qnty=0;
			$costing_per_id=$costing_per_arr[$val['POID']];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$color_id=$po_color_array[$val['COLOR_SIZE_BREAK_DOWN_ID']]['color_id'];
			$po_color_rate=$po_color_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['rate'];
		
			
			if($val['MPRINT_RECV']>0)
			{
			$print_avg_rate=$po_color_rate/$dzn_qnty;
			$print_amount=$val['MPRINT_RECV']*$print_avg_rate;
			$year_prod_cost_arr[$fyear]['print_recv'] += $print_amount;
			}
			if($val['MEMBRO_RECV']>0)
			{
			$embro_avg_rate=$po_color_rate/$dzn_qnty;
			$embro_amount=$val['MEMBRO_RECV']*$embro_avg_rate;
			$year_prod_cost_arr[$fyear]['embo_recv'] += $embro_amount;
			}
			if($val['MWASH_RECV']>0)
			{
			$wash_avg_rate=$po_color_rate/$dzn_qnty;
			$wash_amount=$val['MWASH_RECV']*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			
			
			
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val['POID']][$val['ITEM_ID']]['sew_smv'];
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val['POID']]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				// $po_qty=$po_qty_array[$val[csf('po_break_down_id')]];
				//$cm_avg_cost=$cm_cost/12;
				//$finish_cost=$cm_avg_cost*$val[csf($myear)];
			//	if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
				if($val['MSEW_OUT']>0 && ($val['LOCATION']==3 || $val['LOCATION']==5))
				{
				//echo $sew_smv.'='.$val[csf($myear)].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
				$finish_cost=($sew_smv*$val['MSEW_OUT']*$cost_per_minute)/$exch_rate;
				if($finish_cost>0)
				{
				$year_location_qty_array[$fyear][$val['LOCATION']]['finishing'] += $finish_cost;
				}
				}
			}
		
			  if($val['LOCATION']==3 || $val['LOCATION']==5)
				{
				$locationArray[$val['LOCATION']] = $val['LOCATION'];
				}
		
		// $year_location_qty_array[$fiscalYear][$val[csf('location')]]['finishing'] += $finish_cost;			
	}
	// print_r($year_prod_cost_arr);die();
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	//b.po_breakdown_id as POID,b.is_sales as IS_SALES,a.febric_description_id as DETER_ID,to_char(c.receive_date,'MON-YYYY') as MONTH_YEAR,(b.quantity) as GREY_RECEIVE_QNTY
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		
			$myear=$val["MONTH_YEAR"];
			$is_sales_id=$val['IS_SALES'];
			$knit_qnty=$val['GREY_RECEIVE_QNTY'];
			//$kniting_cost=0;
			//$kniting_qty=0;		//grey_receive_qnty			
			if($val['POID']>0)
			{
				if($is_sales_id!=1)
				{
					//$kniting_cost = array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
					//$kniting_qty = array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);	
					
					$knit_rate=$po_color_knit_array[$val['POID']][$val['DETER_ID']][1]['knit_rate'];
				}
				else
				{
					$knit_rate=$sales_data_knit_array[$val['POID']][$val['DETER_ID']][1]['process_rate'];
				}
				//	echo $knit_rate.'DD';
				if($knit_rate>0)
				{
				$knitingCost =$knit_rate*$knit_qnty;	
				$main_array[$myear]['kniting'] += $knitingCost;
				
					/*if($myear=='OCT-2020')
					{
						$knit_array[$val[csf('po_breakdown_id')]] .=$knit_rate.'*'.$knit_qnty.',';
					$tot_knit_po_Idarray[$myear].=$val[csf('po_breakdown_id')].',';
					}*/
				}
			}	
			
			// $fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
			// $dyeing_kniting_qty_array[$fyear][2] += $knitingCost;
	}
 	// print_r($knit_array);die();
	// ======================== calcutate dyeing amount ====================
	 $process_array=array(1,30,35);
	//$dying_prod_sql_res = sql_select($dying_prod_sql);
	// a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'MON-YYYY') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY,f.detarmination_id as DETER_ID
	$dying_prod_qty_array=array();
	foreach ($dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$entry_formId=$val['entry_form'];
			$po_id=$val['POID'];
			$is_sales_id=$val['IS_SALES'];
			$prodQty=$val['BATCH_QNTY'];
			if($is_sales_id!=1)
			{
			$sensitive_id=$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'];
			}
			$dyeing_cost=0;
			$dyeing_qty=0;
			foreach ($conversion_cost_head_array as $key => $value) 
			{
				if($val['POID']>0)
				{
					if(!in_array($key, $process_array ))
					{
						if($is_sales_id!=1)
						{
							if($key==31)
							{
								
								$conv_rate=$po_color_fab_array[$po_id][$val['COLOR_ID']][$val['DETER_ID']][$key]['rate'];
								 $conv_amount=$conv_rate*$prodQty;
							}
							else if($key==33) //Heatset
							{
								$conv_rate=$po_color_fab_array2[$po_id][$val['DETER_ID']][$key]['rate'];
							   $conv_amount=$conv_rate*$prodQty;
							}
							else
							{
								$conv_rate=$po_color_fab_array[$po_id][$val['DETER_ID']][$key]['rate'];
								$conv_amount=$conv_rate*$prodQty;
							}
						}
						else
						{
							$conv_rate=$sales_data_array[$po_id][$val['DETER_ID']][$val['COLOR_ID']][$key]['process_rate'];
							$conv_amount=$conv_rate*$prodQty;
						}
						
						if($conv_amount>0)
						{
						$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['dyeing_prod']+=$conv_amount;
						}
					}
					
				}
			
			}
			//echo $dyeing_cost.'='.$dyeing_qty.'<br> ';
		
		
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
		
	}
	
	//print_r($dying_prod_qty_array);
	//echo $conversion_costing_arr[33788][188][12].'A';
	

	// ========================== subcontact ===============================
	foreach ($sub_dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$entry_formId=$val['ENTRY_FORM'];
			$po_id=$val['POID'];
			
			//echo $dyeing_cost.'='.$dyeing_qty.'<br> ';
			
	  if($entry_formId==38 && $val['BATCH_QNTY']>0)
		{
			$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['sub_dyeing_prod']+=($val['BATCH_QNTY']*$order_wise_rate[$val['POID']]/$exch_rate);
		}
		
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
		
	}
	ksort($floorArray);
	$subcon_ord_qty = array();
	foreach ($sql_sub_res as $val) 
	{	
		$subFiscalDyeingYear=$val["year"].'-'.($val["year"]+1);
		$subcon_ord_qty[$subFiscalDyeingYear][$val['main_process_id']] += $val['order_quantity'];
		
	}
	 $fab_dyeing_chk_key=31;
	 //SELECT b.order_id as ,b.process as PROCESS,to_char(a.product_date,'MON-YYYY') as MONTH_YEAR,
	  //(CASE WHEN a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS SUB_KNITTING_PROD,
	 // (CASE WHEN a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id)  THEN b.product_qnty END) AS FIN_PROD
	foreach ($sql_sub_knit_res as $val) 
	{	
			$myear=$val["MONTH_YEAR"];	
			$processArr=array_unique(explode(",",$val["PROCESS"]));							
			$subKnit_cost =$order_wise_rate[$val['ORDER_ID']]*$val["SUB_KNITTING_PROD"];	
			$subFinish_cost =$order_wise_rate[$val['ORDER_ID']]*$val["FIN_PROD"];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate.'='.$val[csf("fin_prod")].'<br>';
			if($subKnit_cost>0)
			{
				$subKnit_costUSD = $subKnit_cost/$rate;
				$main_array[$myear]['subKnit'] += $subKnit_costUSD;
			}
			if(!in_array($fab_dyeing_chk_key, $processArr ))
			{
			if($subFinish_cost>0)
			{
				$subFin_costUSD = $subFinish_cost/$rate;
				$main_array[$myear]['subFinish'] += $subFin_costUSD;
			}
			}
	}
	foreach ($sql_sub_dye_res as $val) 
	{			
			$myear=$val["MONTH_YEAR"];				
			$subDye_cost =$order_wise_rate[$val['ORDER_ID']]*$val["knitting_prod"];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subDye_cost>0)
			{
			$subDye_costUSD = $subDye_cost/$rate;	
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);
			$main_array[$myear]['subDye'] += $subDye_costUSD;	
			}
	}
	//SubCon Sewing Out
	//a.order_id as ORDER_ID,a.location_id as LOCATION_ID,to_char(a.production_date,'MON-YYYY') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY
	foreach ($sql_sub_sewOut_result as $val) 
	{	
			$myear=$val["MONTH_YEAR"];	
			$subsewOut_cost =$order_wise_rate[$val['ORDER_ID']]*$val['PRODUCTION_QNTY'];	
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);//
			$main_array[$myear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_location_sewOut_array[$myear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;
			}
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);
	
	$floor_width=count($floorArray)*80;
	$tbl_width = 780+(count($locationArray)*80)+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <!--<table width="< echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
        <br>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Monthly Revenue Report <? echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Month</th>
	            <?
				asort($locationArray);
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="80" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> gmt</th>
	            	<?
	            }
	            ?>
	            <th width="80">Total gmt</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?></th>
	            	<?
	            }
	            ?>
                <th width="80">Finishing</th>
                <th width="80">Total Dyeing</th>
                
	            <th width="80">Knitting </th>
	            <th width="80">Printing</th>
	            <th width="80">Embroidery</th>
	            <th width="80">Washing</th>
                <th width="80">Total Textile</th>
	            <th width="80">Total Knit Asia</th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;$subConKnit=0;
					$total_textile=$total_knit_asia=0;
					$total_finish=0;$total_print_cost=0;$total_embro_cost=0;$total_wash_cost=0;
		        //	foreach ($fiscal_year_arr as $year => $val) 
					foreach ($fiscalMonth_arr as $year => $val) 
		        	{
		        		$year_ex = explode("-", $year);
		        		$fiscal_total = 0;		        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				           <td><a href="javascript:void()" onclick="report_generate_by_month('<? echo $year?>',1)"><? echo date('F-y',strtotime($year));?></a></td>
				            <?
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	//  echo $year_location_qty_array[$year][$loc_id]['finishing'].', ';
								 
								  ?>
				            	<td align="right" title="Sewing Out*SMV*CPM/Exchange Rate+SubCon SewOut*SubCon Order Rate(<? echo $subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];?>)"><?  echo number_format($year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'],0);?></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            }
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
                            <?
							$dying_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$dying_prod_qty=$dying_prod_qty_array[$year][$floor_id]['dyeing_prod']+$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
								 
								  ?>
				            	<td align="right" title="PoId=<? echo rtrim($dying_prod_qty_array[$year][$floor_id]['dyeing_po_id'],',');?>,Without Knitting+YarnDying+AOP Rate from PreCost*Dying Prod Qty+SubConDyeing Prod*SubCOn Order Rate(<? echo $dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];?>)"><?  echo number_format($dying_prod_qty,0);?></td>
				            	<?
								$dying_floor_total[$floor_id]+=$dying_prod_qty;
								$dying_floor_tot+=$dying_prod_qty;
				            }
							
							$tot_textile=$dying_floor_tot+$main_array[$year]['subFinish']+$main_array[$year]['kniting']+$main_array[$year]['subKnit']+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv']+$year_prod_cost_arr[$year]['wash_recv']
							?>
                            <td align="right" title="SubCon Finish*SubCon Order Rate"><? echo number_format($main_array[$year]['subFinish'],0); ?></td>
                            <td align="right" title="All Dying Floor+Finish"><? echo number_format($dying_floor_tot+$main_array[$year]['subFinish'],0); ?></td>
                           
				            <td align="right" title="Knitting Prod*Pre Cost Knit Avg Rate+SubCon Knit*SubCon Order Rate(<? echo $main_array[$year]['subKnit'];?>)"><? echo number_format($main_array[$year]['kniting']+$main_array[$year]['subKnit'],0); ?></td>
				            <td align="right" title="Print Recv*PreCost Print Avg Rate"><? echo number_format($year_prod_cost_arr[$year]['print_recv'],0); ?></td>
				            <td align="right"><? echo number_format($year_prod_cost_arr[$year]['embo_recv'],0); ?></td>
				            <td align="right"><? echo number_format($year_prod_cost_arr[$year]['wash_recv'],0); ?></td>
                             <td align="right" title="Total Dying+Knit+Print+Embro+Wash"><? ;
							 echo number_format($tot_textile,0); ?></td>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($total_gmts+$tot_textile,0); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $dying_floor_tot+$main_array[$year]['subFinish'];//$main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
						$total_finish+=$main_array[$year]['subFinish'];
						$total_print_cost+=$year_prod_cost_arr[$year]['print_recv'];
						$total_embro_cost+=$year_prod_cost_arr[$year]['embo_recv'];
						$total_wash_cost+=$year_prod_cost_arr[$year]['wash_recv'];
						$subConKnit+=$main_array[$year]['subKnit'];
						
						$total_textile+=$tot_textile;
						$total_knit_asia+=$total_gmts+$tot_textile;
								
						
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,0); ?></th>
	             <?
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($dying_floor_total[$floor_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($total_finish,0); ?></th>
	            <th><? echo number_format($gr_dyeing_total,0); ?></th>
	            <th title="<? echo $subConKnit;?>"><? echo number_format($gr_kniting_total,0); ?></th>
	            <th><? echo number_format($total_print_cost,0); ?></th>
	            <th><? echo number_format($total_embro_cost,0); ?></th>
	            <th><? echo number_format($total_wash_cost,0); ?></th>
                 <th><? echo number_format($total_textile,0); ?></th>
	            <th><? echo number_format($total_knit_asia,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 

if($action=="report_generate_by_month_sheet_kal") //Top Sheet KAL Daily
{
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$month_year);
	// getting month from fiscal year
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";
	}
	$sql_floor=sql_select("select id as ID,floor_name as FLOOR_NAME from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row['ID']]['floor']=$row['FLOOR_NAME'];
		$floor_library[$row['ID']]['floor_id']=$row['ID'];
	}
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select COST_PER_MINUTE, APPLYING_PERIOD_DATE, APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row['APPLYING_PERIOD_DATE'],'','',1);
			$applying_period_to_date=change_date_format($row['APPLYING_PERIOD_TO_DATE'],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row['COST_PER_MINUTE'];
			}
		}
	
	//$sql_fin_prod="SELECT a.location,a.po_break_down_id";
	
	  $sql_fin_prod="SELECT b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID,a.embel_name as EMBEL_NAME,a.location as LOCATION,a.po_break_down_id as POID,a.item_number_id as ITEM_ID,to_char(a.production_date,'DD-MON') as MONTH_YEAR,
	 (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS MSEW_OUT,
	 (CASE WHEN  a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS MPRINT_RECV,
	 (CASE WHEN  a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS MEMBRO_RECV,
	 (CASE WHEN a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS MWASH_RECV
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5,3) and a.production_source in(1)   and a.location>0 and b.production_qnty>0  and a.status_active=1  and b.status_active=1";
	 // echo  $sql_fin_prod;die;
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val['POID']] = $val['POID'];
		//if($val['LOCATION']==3 || $val['LOCATION']==5 )
		//{
		//$sewing_qty_array[$val['POID']] += $val['MSEW_OUT'];
		//}
	}

	// ========================= for kniting ======================
	
	/* $sql_kniting_dyeing="SELECT b.po_breakdown_id,b.is_sales,a.febric_description_id as deter_id,to_char(c.receive_date,'DD-MON') as month_year,(b.quantity) as grey_receive_qnty from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and to_char(c.receive_date,'MON-YYYY')='$year' and b.trans_type=1    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2)";*/
	 $sql_kniting_dyeing=" SELECT b.po_breakdown_id as POID,b.is_sales as IS_SALES,a.febric_description_id as DETER_ID,to_char(c.receive_date,'DD-MON') as MONTH_YEAR,(b.quantity) as GREY_RECEIVE_QNTY from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and to_char(c.receive_date,'MON-YYYY')='$year'  and b.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(2) and b.entry_form in(2) order by b.po_breakdown_id asc   ";
	 
 //MON-YYYY
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];
		}
	}
	// ========================= for dyeing ======================and d.po_id in(2617,2279,1224,837,835,834)
	/* $dying_prod_sql=" SELECT a.color_id,a.is_sales,a.batch_no,a.sales_order_no,c.floor_id,d.po_id as po_breakdown_id,c.entry_form,to_char(c.process_end_date,'DD-MON') as month_year,(d.batch_qnty) as batch_qnty,f.detarmination_id as deter_id from  pro_batch_create_mst a,pro_batch_create_dtls d,pro_fab_subprocess c,product_details_master f where a.id=d.mst_id and a.id=c.batch_id and c.batch_id=d.mst_id  and f.id=d.prod_id and c.load_unload_id=2 and a.batch_against=1 and c.entry_form=35 and c.service_company in($cbo_company_id)    and to_char(c.process_end_date,'MON-YYYY')='$year'  and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id ,c.process_end_date";*/
 	$dying_prod_sql=" SELECT a.id as ID,a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'DD-MON') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY,f.detarmination_id as DETER_ID  from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d,product_details_master f where c.batch_id=a.id  and a.id=d.mst_id and c.batch_id=d.mst_id and f.id=d.prod_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against=1   and c.service_company in($cbo_company_id)  and to_char(c.process_end_date,'MON-YYYY')='$year' and a.status_active=1 and d.status_active=1 and c.status_active=1  order by c.floor_id,c.process_end_date ";
	
	$dying_prod_sql_res = sql_select($dying_prod_sql);
	foreach ($dying_prod_sql_res as $val) 
	{
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];
		}
	}
	// print_r($po_id_array);die;
	
	  $sub_dying_prod_sql=" SELECT a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,to_char(c.process_end_date,'DD-MON') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY from  pro_batch_create_mst a,pro_batch_create_dtls d,pro_fab_subprocess c where a.id=d.mst_id and a.id=c.batch_id and c.batch_id=d.mst_id and c.load_unload_id=2 and a.batch_against=1 and c.entry_form=38 and c.service_company in($cbo_company_id)  and to_char(c.process_end_date,'MON-YYYY')='$year'  and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id ,c.process_end_date";
	$sub_dying_prod_sql_res = sql_select($sub_dying_prod_sql);
	foreach ($sub_dying_prod_sql_res as $val) 
	{
		$sub_po_id_array[$val['POID']] = $val['POID'];
	}
	
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id as LOCATION_ID,to_char(a.production_date,'DD-MON') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.production_type=2 and a.location_id in(3,5) and a.status_active=1 ";
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	foreach ($sql_sub_sewOut_result as $val) 
	{
		$sub_po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
	}
	// =================================== subcon kniting =============================
	  $sql_sub_knit=" SELECT b.order_id as ORDER_ID,b.process as PROCESS,to_char(a.product_date,'DD-MON') as MONTH_YEAR,
	  (CASE WHEN a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS SUB_KNITTING_PROD,
	  (CASE WHEN a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id)  THEN b.product_qnty END) AS FIN_PROD
	  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)   and to_char(a.product_date,'MON-YYYY')='$year'  and a.product_type in(2,4)   and a.status_active=1 and b.status_active=1"; 
	//entry_form=292, product_type=4
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	foreach ($sql_sub_knit_res as $val) 
	{
		$sub_po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
	}
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
			
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 2, $sales_id_array, $empty_arr);//Sales ID Ref from=2
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	
	$process_array_chk=array(1,30,35);
	 
	// $sql_sales = "select b.id as dtls_id,a.id,a.job_no, a.within_group,b.color_id,b.determination_id as deter_id,b.process_id,b.process_seq,b.body_part_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $sales_cond order by b.id desc";
	$sql_sales = "select b.id as DTLS_ID,a.id as ID,a.job_no as JOB_NO, a.within_group as WITHING_GROUP,b.color_id as COLOR_ID,b.determination_id as DETER_ID,b.process_id as PROCESS_ID,b.process_seq as PROCESS_SEQ,b.body_part_id as BODY_PART_ID from fabric_sales_order_mst a,fabric_sales_order_dtls b,gbl_temp_engine g  where a.id=b.mst_id and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by b.id desc";
	  
	$sql_sales_result = sql_select($sql_sales);//and a.company_id in($cbo_company_id)
	foreach ($sql_sales_result as $val) 
	{		
		if($val['PROCESS_SEQ'])
		{
			$process_id=array_unique(explode(",",$val['PROCESS_ID']));
			$process_seqArr=array_unique(explode(",",$val['PROCESS_SEQ']));
			foreach($process_id as $p_key)
			{
				foreach($process_seqArr as $val_rate)
				{
					$process_Rate=explode("__",$val_rate);
					$process_Id=$process_Rate[0];
					$process_rate=$process_Rate[1];
					if($p_key==$process_Id && $process_rate>0)
					{
						$sales_data_array[$val['ID']][$val['DETER_ID']][$val['COLOR_ID']][$p_key]['process_rate'] = $process_rate;
						$sales_data_knit_array[$val['ID']][$val['DETER_ID']][$p_key]['process_rate'] = $process_rate;
					}
				}
			}
		}
	}
	//	print_r($sales_data_array);
	
	$sql_sew_po="SELECT b.id as POID,b.po_quantity as POQTY,b.pub_shipment_date as PUBSHIPDATE,b.shipment_date as SHIPDATE,b.job_no_mst as JOB_NO from wo_po_break_down b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and  b.status_active=1 and b.is_deleted=0 "; 
	$sql_po_sew_result = sql_select($sql_sew_po);
	foreach ($sql_po_sew_result as $val) 
	{
		$po_qty_array[$val['POID']] = $val['POQTY'];
		$po_job_array[$val['POID']]= $val['JOB_NO'];
		$po_date_array[$val['JOB_NO']]['ship_date'].= $val['SHIPDATE'].',';
		$po_date_array[$val['JOB_NO']]['pub_date'].= $val['PUBSHIPDATE'].',';
	}
	
	unset($sql_po_sew_result);
	
	/*$sql_pre_wash="SELECT  b.emb_name as EMB_NAME,d.id as AVG_ID,d.color_number_id as COLOR_ID,d.po_break_down_id as POID,c.id as COLOR_SIZE_ID,d.rate as RATE from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b,gbl_temp_engine g where d.job_id=a.id   and c.job_no_mst=a.job_no and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id  and b.id=d.pre_cost_emb_cost_dtls_id and  b.job_id=a.id  and  b.job_id=c.job_id and g.ref_val=c.po_break_down_id  and g.ref_val=d.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.requirment>0 and d.rate>0   order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
	}
	unset($sql_wash_result);*/
	$sql_pre_wash="SELECT b.emb_name as EMB_NAME, d.id as AVG_ID, d.color_number_id as COLOR_ID, d.po_break_down_id as POID, d.color_size_table_id as COLOR_SIZE_ID, d.rate as RATE 
	from wo_pre_cost_embe_cost_dtls b, wo_pre_cos_emb_co_avg_con_dtls d, gbl_temp_engine g 
	where b.id=d.pre_cost_emb_cost_dtls_id and d.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.requirment>0 and d.rate>0 order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
	}
	unset($sql_wash_result);
	
	/* $sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,d.color_size_sensitive,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond";*/
	/*$poID_day="99,14796,13795,14812,14918,14959,14444,12120,14914,14746,14898,13632,13636,14912,14678,14743,14751,14939,14529,81,14954,14627,14587,13131,12460,14911,14377,14602,13641,12169,12119,13471,14597,14236,14542,14259,11276,12854,14631,14629,12178,14232,10311,14901,13631,12166,14355,14714,14719,14781,14744,14600,14364,14882,14585,12462,14909,13808,14920,12461,14937,14258,14921,13581,14532,14759,13630,14913,14879,14632,14752,14931,14603,13788,13466,13468,12467,12732,12465,14750,14881,13555,13506,12575,13045,14999,14892,15104,14535,14537,14543,14760,15075,14541,10422,10727,14998,12582,15068,14878,13798,14556,14895,14008,13796,14883,13647,13470,13784,15152,14927,15021,15074,15160,15150,15142,14732,15151,14378,15144,15149,15261,15159,15263,15244,15155,15143,14974,14975,14634,14601,14984,15262,14559,13584,13287,15061,15153,15158,14628,14923,15146,15210,14910,14973,15260,58,14897,15002,15080,14919,14896,14966 ";*/
	 /*$sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,f.id as conv_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  $cm_po_cond order by f.id, b.id asc";*/
	
	/*$sql_po="SELECT a.buyer_name as BUYER_NAME,b.id as ID,b.job_no_mst as JOB_NO_MST,b.shipment_date as SHIPMENT_DATE,b.po_quantity as PO_QUANTITY,b.pub_shipment_date as PUB_SHIPMENT_DATE,b.grouping as REF_NO,c.color_number_id as COLOR_ID,d.color_size_sensitive as COLOR_SIZE_SENSITIVE,d.lib_yarn_count_deter_id as DETER_ID,f.id as CONV_ID,f.cons_process as CONS_PROCESS,f.charge_unit as CHARGE_UNIT,f.color_break_down as COLOR_BREAK_DOWN from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f,gbl_temp_engine g where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and b.id=g.ref_val and g.ref_val=c.po_break_down_id and g.ref_val=e.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  order by f.id,b.id asc"; */
	
		$sql_conv="SELECT d.color_size_sensitive as COLOR_SIZE_SENSITIVE, d.lib_yarn_count_deter_id as DETER_ID, e.po_break_down_id as POID, e.color_number_id as COLOR_ID, f.id as CONV_ID, f.cons_process as CONS_PROCESS, f.charge_unit as CHARGE_UNIT, f.color_break_down as COLOR_BREAK_DOWN 
	
	from wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e, wo_pre_cost_fab_conv_cost_dtls f, gbl_temp_engine g 
	
	where d.id=e.pre_cost_fabric_cost_dtls_id and d.id=f.fabric_description and e.po_break_down_id=g.ref_val and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 order by f.id, e.po_break_down_id asc";
	 
	 
	$sql_conv_result = sql_select($sql_conv);
	 
	 
	 
	//$sql_po_result = sql_select($sql_po);
	foreach ($sql_conv_result as $val) 
	{
		//$color_break_down=$val[csf('color_break_down')];
		/*$po_qty_array[$val['POID']] = $val['PO_QUANTITY'];
		$po_job_array[$val['POID']]= $val['JOB_NO_MST'];
		$po_date_array[$val['JOB_NO_MST']]['ship_date'].= $val['SHIPMENT_DATE'].',';
		$po_date_array[$val['JOB_NO_MST']]['pub_date'].= $val['PUB_SHIPMENT_DATE'].',';*/
		
		if($val['COLOR_SIZE_SENSITIVE']==3)
		{
		$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'] = $val['COLOR_SIZE_SENSITIVE'];
		}
		if($val['COLOR_BREAK_DOWN']!="")
		{
		$color_break_down=$val['COLOR_BREAK_DOWN'];
		}
		
		if($val['CONS_PROCESS']==31 && $color_break_down!='')
		{
			//$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];
			if($val['COLOR_SIZE_SENSITIVE']==3) //Contrst
			{
				$po_color_fab_brk_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
				// echo $val[csf('color_break_down')].', ';
			}
			else
			{
				$po_color_fab_brk_array[$val['POID']][$val['COLOR_ID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
			}
			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
				if($arr_2[1])
				{
				$po_color_fab_array[$val['POID']][$arr_2[3]][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$arr_2[1];
				}
			
			}
		}
		else if($val['CONS_PROCESS']==33) //Heatset
		{
			$po_color_fab_array2[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$val['CHARGE_UNIT'];
		}
		else
		{
			$po_color_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] = $val['CHARGE_UNIT'];
		}
		$po_color_knit_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['knit_rate'] = $val['CHARGE_UNIT'];
		
		
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	/*$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_smv_cost_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	}*/  
	 $cm_sql = "SELECT c.costing_date as COSTING_DATE,c.costing_per as COSTING_PER,c.sew_smv as SEW_SMV,a.cm_cost as CM_COST,b.id as POID,d.smv_set as SMV_SET,d.gmts_item_id as GMTS_ID from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d,gbl_temp_engine g where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and d.job_id=b.job_id and d.job_id=c.job_id  and g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and a.status_active=1 and b.status_active=1 and c.status_active=1  "; 
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val['POID']] = $val['CM_COST'];
		//$pre_cost_smv_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_smv_cost_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_cost_array[$val['POID']]['costing_date'] = $val['COSTING_DATE'];
		$costing_per_arr[$val['POID']]= $val['COSTING_PER'];
	}
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();*/
	// var_dump($condition);
	//$conversion= new conversion($condition);
		//echo $conversion->getQuery();die;
	//$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	//$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	
	/*$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
 //echo "<pre>";print_r($emblishment_costing_arr_wash);die();
	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')]; 
	$exch_rate = $sql_rate_res[0][csf('rate')];
	
	//==================================== subcon order data =============================
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
		 $sql_subcon_po="SELECT b.id as ID,b.rate as RATE from subcon_ord_dtls b,gbl_temp_engine g where  g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0";
	$sql_subcon_po_res = sql_select($sql_subcon_po);
	foreach($sql_subcon_po_res as $row)
	{
		$order_wise_rate[$row['ID']]=$row['RATE'];
	}
	 //print_r($order_wise_rate);die;
	unset($sql_subcon_po_res);
	 
	
	// =================================== subcon dyeing =============================
	$main_array = array();
	$locationArray = array();
	$year_prod_cost_arr=array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	// b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID,a.embel_name as EMBEL_NAME,a.location as LOCATION,a.po_break_down_id as POID,a.item_number_id as ITEM_ID,to_char(a.production_date,'DD-MON') as MONTH_YEAR,
	// (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS MSEW_OUT,
	// (CASE WHEN  a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS MPRINT_RECV,
	// (CASE WHEN  a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS MEMBRO_RECV,
	// (CASE WHEN a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS MWASH_RECV
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
			$fyear=$val["MONTH_YEAR"];
			if($val['LOCATION']==3 || $val['LOCATION']==5 )
			{
				$main_array[$fyear]['qty']+=$val['MSEW_OUT'];//msew_out
				$main_array[$fyear]['location'] = $val['LOCATION'];
			}
			//$print_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][1];
			//$print_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][1];
			
			//$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			//$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			
			//$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			//$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];
			//echo $print_cost.'D';
			$dzn_qnty=0;
			$costing_per_id=$costing_per_arr[$val['POID']];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			//echo $print_cost.'='.$val[csf('mprint_recv')].'='.$dzn_qnty.'<br>';
			
			$color_id=$po_color_array[$val['COLOR_SIZE_BREAK_DOWN_ID']]['color_id'];
			$po_color_rate=$po_color_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['rate'];
	
			if($val['MPRINT_RECV']>0)
			{
			$print_avg_rate=$po_color_rate/$dzn_qnty;
			$print_amount=$val['MPRINT_RECV']*$print_avg_rate;
			$year_prod_cost_arr[$fyear]['print_recv'] += $print_amount;
			}
			if($val['MEMBRO_RECV']>0)
			{
			$embro_avg_rate=$po_color_rate/$dzn_qnty;;
			$embro_amount=$val['MEMBRO_RECV']*$embro_avg_rate;
			$year_prod_cost_arr[$fyear]['embo_recv'] += $embro_amount;
			}
			if($val['MWASH_RECV']>0)
			{
			$wash_avg_rate=$po_color_rate/$dzn_qnty;;
			$wash_amount=$val['MWASH_RECV']*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_smv_cost_array[$val['POID']][$val['ITEM_ID']]['sew_smv'];
			
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val['POID']]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
				//echo $sew_smv.'='.$val[csf('msew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
				if($val['MSEW_OUT']>0 && ($val['LOCATION']==3 || $val['LOCATION']==5) )
				{
					$finish_cost=($sew_smv*$val['MSEW_OUT']*$cost_per_minute)/$exch_rate;
					if($finish_cost>0)
					{
					$year_location_qty_array[$fyear][$val['LOCATION']]['finishing'] += $finish_cost;
					}
				}
			}
		if($val['LOCATION']==3 || $val['LOCATION']==5 )
		{
		$locationArray[$val['LOCATION']] = $val['LOCATION'];
		}
	}
	// print_r($year_prod_cost_arr);die();
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	$tot_knit_array=0;
	// b.po_breakdown_id as POID,b.is_sales as IS_SALES,a.febric_description_id as DETER_ID,to_char(c.receive_date,'DD-MON') as MONTH_YEAR,(b.quantity) as GREY_RECEIVE_QNTY
	foreach ($sql_kniting_dyeing_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];
			$is_sales_id=$val['IS_SALES'];
			$knit_qnty=$val['GREY_RECEIVE_QNTY'];
			$kniting_cost=0;
			$kniting_qty=0;		//grey_receive_qnty	
			//echo $is_sales_id.', ';		
			if($val['POID']>0)
			{
				if($is_sales_id!=1)
				{
				//$kniting_cost = array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
				//$kniting_qty = array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);	
				
				$knit_rate=$po_color_knit_array[$val['POID']][$val['DETER_ID']][1]['knit_rate'];
				}
				else
				{
					$knit_rate=$sales_data_knit_array[$val['POID']][$val['DETER_ID']][1]['process_rate'];
				}
				//echo $knit_rate.'DD';
				if($knit_rate>0)
				{
				$knitingCost =$knit_rate*$knit_qnty;	
				$main_array[$myear]['kniting'] += $knitingCost;
				$tot_knit_po_array[$val['POID']]=$val['POID'];
				
				$knit_array[$val['POID']] .=$knit_rate.'*'.$knit_qnty.',';
				}
			}	
			
	}
	//echo "D=".implode(",",$tot_knit_po_array);
	// echo "<pre>";
	// print_r($knit_array);//die();
	 //echo "</pre>";
	// ======================== calcutate dyeing amount ====================
	 $process_array=array(1,30,35);
	//$dying_prod_sql_res = sql_select($dying_prod_sql);
	//a.id as ID,a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id,d.po_id as POID,c.entry_form as entry_form,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'DD-MON') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY,f.detarmination_id as DETER_ID 
	$dying_prod_qty_array=array();$floorArray=array();
	foreach ($dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$dyeing_cost=0;
			$dyeing_qty=0;
			$po_id=$val['POID'];
			$is_sales_id=$val['IS_SALES'];
			$entry_formId=$val['ENTRY_FORM'];
			$prodQty=$val['BATCH_QNTY'];
			if($is_sales_id!=1)
			{
			$sensitive_id=$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'];
			}
			
			foreach ($conversion_cost_head_array as $key => $value) 
			{
				if($po_id>0)
				{
					if(!in_array($key, $process_array ))
					{
						if($is_sales_id!=1)
						{
							if($key==31)
							{
								$conv_rate=$po_color_fab_array[$po_id][$val['COLOR_ID']][$val['DETER_ID']][$key]['rate'];
								$conv_amount=$conv_rate*$prodQty;
							}
							else if($key==33) //Heatset
							{
								$conv_rate=$po_color_fab_array2[$po_id][$val['DETER_ID']][$key]['rate'];
								$conv_amount=$conv_rate*$prodQty;
							}
							else
							{
								$conv_rate=$po_color_fab_array[$po_id][$val['DETER_ID']][$key]['rate'];
								$conv_amount=$conv_rate*$prodQty;
							}
						}
						else // For Sales
						{
							$conv_rate=$sales_data_array[$po_id][$val['DETER_ID']][$val['COLOR_ID']][$key]['process_rate'];
							//$sales_data_array[$val[csf('id')]][$val[csf('deter_id')]][$val[csf('color_id')]][$p_key]['process_rate'] = $process_rate;
						 	$conv_amount=$conv_rate*$prodQty;
							//echo $myear.'D';
							if($myear=='05-DEC' && $val['FLOOR_ID']==40)
							{
								//echo $conv_rate.'*'.$prodQty.'<br>';
							$saleIdAmtArr[$po_id]+=$conv_rate*$prodQty;
							$saleIdQtyAmtArr[$val['ID']]+=$conv_rate*$prodQty;//$conv_rate*$prodQty;
							}

						}
							if($conv_amount>0)  
							{
								//echo $conv_rate.'='.$val[csf('batch_qnty')].', ';
							//$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['dying_revenue']+= $conv_rate*$val[csf('batch_qnty')];
							$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['dyeing_prod']+=$conv_amount;
							}
						
					} //4 in_array
							
				} //3 PO
				
			
			}//2
		//	echo $dyeing_cost.'='.$dyeing_qty.', ';
			//echo $val[csf('po_breakdown_id')].'='.$dyeing_cost.'='.$dyeing_qty.'<br> ';
				if($dyeing_cost>0 && $val['BATCH_QNTY']>0)
				{	
				//$avg_dyeing_rate = $dyeing_cost/$dyeing_qty;	
				//$conv_dyeing_cost =$avg_dyeing_rate*$val[csf('batch_qnty')];	
				//echo $dyeing_cost.'='.$dyeing_qty;
				$main_array[$fyear]['dyeing'] += $dyeing_cost;
				//$dying_prod_qty_array[$myear][$val[csf('floor_id')]]['dyeing_po_id'].=$val[csf('po_breakdown_id')].',';
				}
		 // echo $entry_formId.'='.$exch_rate.'d';
		 	
			$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
			//echo $floor_id.', ';
			if($floor_id)
			{
			$floorArray[$floor_id] = $floor_id;
			}
	} //1
	// echo implode(',',$saleIdQtyAmtArr);
	///print_r($saleIdQtyAmtArr);
	// ========================== subcontact ===============================
	//$dying_prod_qty_array=array();
	// a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_from as ENTRY_FORM,to_char(c.process_end_date,'DD-MON') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY
	foreach ($sub_dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$po_id=$val['POID'];
			$entry_formId=$val['ENTRY_FORM'];
			if($val['BATCH_QNTY']>0)
			{	
			$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['dyeing_po_id'].=$val['POID'].',';
		    }
		
		  if($entry_formId==38 && $val['BATCH_QNTY']>0) //SubCon
			{
				// echo $entry_formId.'='.$exch_rate.'='.$val[csf('batch_qnty')].'='.$order_wise_rate[$val[csf('po_breakdown_id')]].'d';
				$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['sub_dyeing_prod']+=($val['BATCH_QNTY']*$order_wise_rate[$val['POID']])/$exch_rate;
				//echo "SUBC";
			}
			
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', '; 
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	//print_r($floorArray);
	ksort($floorArray);
	
	$subcon_ord_qty = array();
	foreach ($sql_sub_res as $val) 
	{	
		$subFiscalDyeingYear=$val["year"].'-'.($val["year"]+1);
		$subcon_ord_qty[$subFiscalDyeingYear][$val['main_process_id']] += $val['order_quantity'];
	}
	 $fab_dyeing_chk_key=31;
	foreach ($sql_sub_knit_res as $val) 
	{	
			$myear=$val["MONTH_YEAR"];	
			$processArr=array_unique(explode(",",$val['PROCESS']));							
			$subKnit_cost =$order_wise_rate[$val['ORDER_ID']]*$val["SUB_KNITTING_PROD"];	
			$subFinish_cost =$order_wise_rate[$val['ORDER_ID']]*$val["FIN_PROD"];	
			// echo $order_wise_rate[$val['ORDER_ID']].'='.$val["FIN_PROD"].', ';
			if($subKnit_cost>0)
			{
				$subKnit_costUSD = $subKnit_cost/$rate;
				$main_array[$myear]['subKnit'] += $subKnit_costUSD;
			}
			if(!(in_array($fab_dyeing_chk_key, $processArr )))
			{
				if($subFinish_cost>0)
				{
				$subFin_costUSD = $subFinish_cost/$rate;
				$main_array[$myear]['subFinish'] += $subFin_costUSD;
				}
			}
	}
	//print_r($main_array2);
	foreach ($sql_sub_dye_res as $val) 
	{			
			$myear=$val["MONTH_YEAR"];				
			$subDye_cost =$order_wise_rate[$val['ORDER_ID']]*$val["KNITTING_PROD"];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$subDye_costUSD = $subDye_cost/$rate;	
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);
			$main_array[$myear]['subDye'] += $subDye_costUSD;	
	}
	//SubCon Sewing Out
	//a.order_id as ORDER_ID,a.location_id as LOCATION_ID,to_char(a.production_date,'DD-MON') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			$myear=$val["MONTH_YEAR"];	//		
			$subsewOut_cost =$order_wise_rate[$val['ORDER_ID']]*$val['PRODUCTION_QNTY'];	
			//echo $order_wise_rate[$val[csf('order_id')]].'='.$val[csf('production_qnty')].', ';
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);//
			$main_array[$myear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_location_sewOut_array[$myear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;
			}
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);
	
	$floor_width=count($floorArray)*80;
	$tbl_width = 780+(count($locationArray)*80)+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->
		<br>
	    <!--<table width="<? //echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Daily Revenue Report <? echo date('F-Y',strtotime($year)); ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Date</th>
	            <?
				$gmt_year='';
				$gmt_year=date('Y',strtotime($year));
				asort($locationArray);
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="80" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> gmt</th>
	            	<?
	            }
	            ?>
	            <th width="80">Total gmt</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?></th>
	            	<?
	            }
	            ?>
                <th width="80">Finishing</th>
                <th width="80">Total Dyeing</th>
                
	            <th width="80">Knitting </th>
	            <th width="80">Printing</th>
	            <th width="80">Embroidery</th>
	            <th width="80">Washing</th>
                <th width="80">Total Textile</th>
	            <th width="80"><p>Total Knit Asia</p></th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;$subConKnit=0;
					$total_textile=$total_knit_asia=0;
					$total_finish=0;$total_print_cost=0;$total_embro_cost=0;$total_wash_cost=0;
		        //	foreach ($fiscal_year_arr as $year => $val) 
				$i=1;
					foreach ($days_arr as $year => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmt_date=date('d-M',strtotime($year)).'-'.$gmt_year;
		        		$fiscal_total = 0;	
						        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trd_<? echo $i; ?>">
				          <td title="<? echo  $gmt_date; ?>"><? echo date('d-F',strtotime($year));?></td>
				            <?
							$i++;	 
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	//  echo $year_location_qty_array[$year][$loc_id]['finishing'].', ';
								 $gmt_loc_qty=$year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
								  
								  ?>
				            	<td align="right" title="Sewing Out*SMV*CPM/Exchange Rate+SubCon SewOut*SubCon Order Rate(<? echo $subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];?>)"> <a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$loc_id; ?>','gmt_location_kal',1,'850');" >  <?  echo number_format($gmt_loc_qty,0);?> </a></td>
				            	<? //dying_floor_kal
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            }
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts,0);?></td>
                            <?
							$dying_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$dying_prod_qty=$dying_prod_qty_array[$year][$floor_id]['dyeing_prod']+$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
								 
								  ?>
				            	<td align="right" title="Without Knitting+YarnDying+AOP Rate from PreCost*Dying Prod Qty+SubConDyeing Prod*SubCOn Order Rate(<? echo $dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];?>)"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','dying_floor_kal',2,'820');" ><?  echo number_format($dying_prod_qty,0);?></a></td>
				            	<?
								$dying_floor_total[$floor_id]+=$dying_prod_qty;
								$dying_floor_subcon_total[$floor_id]+=$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
								$dying_floor_tot+=$dying_prod_qty;
				            }
							//
							$tot_textile=$dying_floor_tot+$main_array[$year]['subFinish']+$main_array[$year]['kniting']+$main_array[$year]['subKnit']+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv']+$year_prod_cost_arr[$year]['wash_recv']
							?>
                            <td align="right" title="SubCon Finish*SubCon Order Rate"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','finish_prod_kal',3,'650');" ><? echo number_format($main_array[$year]['subFinish'],0); ?></a></td>
                            <td align="right" title="All Dying Floor+Finish"><? echo number_format($dying_floor_tot+$main_array[$year]['subFinish'],0); ?></td>
                           
				            <td align="right" title="Knitting Prod*Pre Cost Knit Avg Rate+SubCon Knit*SubCon Order Rate(<? echo $main_array[$year]['subKnit'];?>)"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','knitting_prod_kal',4,'770');" ><? echo number_format($main_array[$year]['kniting']+$main_array[$year]['subKnit'],0); ?></a> </td>
				            <td align="right" title="Print Recv*PreCost Print Avg Rate"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','print_prod_kal',5,'820');" ><? echo number_format($year_prod_cost_arr[$year]['print_recv'],0); ?></a></td>
				            <td align="right"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','embro_prod_kal',6,'820');" ><? echo number_format($year_prod_cost_arr[$year]['embo_recv'],0); ?></a></td>
				            <td align="right"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','wash_prod_kal',5,'820');" ><? echo number_format($year_prod_cost_arr[$year]['wash_recv'],0); ?></a></td>
                             <td align="right" title="Total Dying+Knit+Print+Embro+Wash"><? ;
							 echo number_format($tot_textile,0); ?></td>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($total_gmts+$tot_textile,0); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $dying_floor_tot+$main_array[$year]['subFinish'];//$main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
						$total_finish+=$main_array[$year]['subFinish'];
						$total_print_cost+=$year_prod_cost_arr[$year]['print_recv'];
						$total_embro_cost+=$year_prod_cost_arr[$year]['embo_recv'];
						$total_wash_cost+=$year_prod_cost_arr[$year]['wash_recv'];
						$subConKnit+=$main_array[$year]['subKnit'];
						
						$total_textile+=$tot_textile;
						$total_knit_asia+=$total_gmts+$tot_textile;
								
						
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $value) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,0); ?></th>
	             <?
	            foreach ($floorArray as $floor_id => $valu) 
	            {
	            	?>
	            	<th title="SubCon=<? echo $dying_floor_subcon_total[$floor_id];?>"><?  echo number_format($dying_floor_total[$floor_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($total_finish,0); ?></th>
	            <th><? echo number_format($gr_dyeing_total,0); ?></th>
	            <th title="<? echo $subConKnit;?>"><? echo number_format($gr_kniting_total,0); ?></th>
	            <th><? echo number_format($total_print_cost,0); ?></th>
	            <th><? echo number_format($total_embro_cost,0); ?></th>
	            <th><? echo number_format($total_wash_cost,0); ?></th>
                 <th><? echo number_format($total_textile,0); ?></th>
	            <th><? echo number_format($total_knit_asia,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}
if($action=="report_generate_ashulia_kal_rmg") //Yearly Ashulia of KAL
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exlastYear[1];
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$fiscal_year_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$fiscal_year_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	// print_r($fiscal_year_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";//KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	if($prod_reso_allocation==1)
	{
	$sql_fin_prod="SELECT C.LINE_NUMBER,A.FLOOR_ID,A.PO_BREAK_DOWN_ID,A.ITEM_NUMBER_ID AS ITEM_ID,A.PRODUCTION_DATE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS M$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS SEW$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS PR$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS EMBR$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS WR$fyear ";
	}
	    $sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS SEW_OUT from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and a.location=3 order by a.floor_id";
	}
	else
	{
			$sql_fin_prod="SELECT A.SEWING_LINE AS LINE_NUMBER,A.FLOOR_ID,A.PO_BREAK_DOWN_ID,A.ITEM_NUMBER_ID AS ITEM_ID,A.PRODUCTION_DATE";
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$exydata=explode("_",$ydata);
			$fyear=str_replace("-","_",$fyear);
			
			$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS M$fyear ";
			$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS DEW$fyear ";
			$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS PR$fyear ";
			$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS EMBR$fyear ";
			$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS WR$fyear ";
		}
		  $sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS SEW_OUT from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and a.location=3 order by a.floor_id";
	}
	
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		$sewing_qty_array[$val['PO_BREAK_DOWN_ID']] = $val['SEW_OUT'];
	}

	/*$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}*/
	//$cm_po_cond = str_replace("id", "b.id", $po_cond);
	//$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,3) and ENTRY_FORM=3");
	oci_commit($con);
			
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	//fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 2, $sales_id_array, $empty_arr);//Sales ID Ref from=2
	//fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	/*$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}*/
	
	 $sql_sew_po="SELECT b.id as POID,b.po_quantity as POQTY,b.pub_shipment_date as PUBSHIPDATE,b.shipment_date as SHIPDATE,b.job_no_mst as JOB_NO from wo_po_break_down b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and  b.status_active=1 and b.is_deleted=0 "; 
	$sql_po_sew_result = sql_select($sql_sew_po);
	foreach ($sql_po_sew_result as $val) 
	{
		$po_qty_array[$val['POID']] = $val['POQTY'];
		$po_job_array[$val['POID']]= $val['JOB_NO'];
		$po_date_array[$val['JOB_NO']]['ship_date'].= $val['SHIPDATE'].',';
		$po_date_array[$val['JOB_NO']]['pub_date'].= $val['PUBSHIPDATE'].',';
	}
	unset($sql_po_sew_result);
	
	
	//$cm_sql = "SELECT c.costing_date,c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	/*$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";

	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]= $val[csf('costing_date')];
	}*/
	
	$cm_sql = "SELECT c.costing_date as COSTING_DATE,c.costing_per as COSTING_PER,c.sew_smv as SEW_SMV,a.cm_cost as CM_COST,b.id as POID,d.smv_set as SMV_SET,d.gmts_item_id as GMTS_ID from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d,gbl_temp_engine g where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and d.job_id=b.job_id and d.job_id=c.job_id  and g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and a.status_active=1 and b.status_active=1 and c.status_active=1  ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val['POID']] = $val['CM_COST'];
		$pre_cost_smv_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_cost_array[$val['POID']]['costing_date'] = $val['COSTING_DATE'];
		$costing_per_arr[$val['POID']]= $val['COSTING_PER'];
	}
	unset($cm_sql_res);
	

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	
	// print_r($order_wise_rate);

	// =================================== SubCon Sewout =============================
	$sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.floor_id as FLOOR_ID,a.production_date as PRODUCTION_DATE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS m$fyear ";
	}
	  $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id=3 and a.status_active=1  order by a.floor_id";
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	foreach ($sql_sub_sewOut_result as $val) 
	{
		$sub_po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
	}
	// =================================== subcon kniting =============================
	$sql_sub_knit="SELECT b.order_id as ORDER_ID,a.product_date as PRODUCT_DATE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS m$fyear ";
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id) THEN b.product_qnty END) AS fm$fyear ";
	}
	 $sql_sub_knit.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4) and a.location_id=3 and a.status_active=1";
	//entry_form=292, product_type=4

	// $sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	// echo $sql_sub_knit;die();
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	foreach ($sql_sub_knit_res as $val) 
	{
		$sub_po_id_array[$val['ORDER_ID']] = $val['ORDER_ID'];
	}
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	
	$sql_subcon_po="SELECT b.id as ID,b.rate as RATE from subcon_ord_dtls b,gbl_temp_engine g where  g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0";
	$sql_subcon_po_res = sql_select($sql_subcon_po);
	foreach($sql_subcon_po_res as $row)
	{
		$order_wise_rate[$row['ID']]=$row['RATE'];
	}
	unset($sql_subcon_po_res);
	
	// =================================== subcon dyeing =============================
	$sql_sub_dye="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_dye.=", (CASE WHEN a.product_date between '$exydata[0]' and '$exydata[1]' THEN b.product_qnty END) AS m$fyear ";
	}
	$sql_sub_dye.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 and a.location_id=3 and a.status_active=1 ";
	$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			//$myear='m'.$nyear;
			$myear='SEW'.$nyear;
			$pmyear='PR'.$nyear;
			$embrmyear='EMBR'.$nyear;
			$wrmyear='WR'.$nyear;
			$sew_out_prod=$val[$myear];
			$main_array[$fyear]['qty']+=$sew_out_prod;
			$main_array[$fyear]['location'] = $val['location'];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val['PO_BREAK_DOWN_ID']][$val['ITEM_ID']]['sew_smv'];
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val['PO_BREAK_DOWN_ID']]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['PO_BREAK_DOWN_ID']]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['PO_BREAK_DOWN_ID']]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['PO_BREAK_DOWN_ID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['PO_BREAK_DOWN_ID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
				$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
				$year_floor_array[$fyear][$val['FLOOR_ID']]['finishing'] += $finish_cost;
			}
		}
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;				
			$sew_out_prod=$val[$myear];
			$subsewOut_cost =$order_wise_rate[$val['ORDER_ID']]*$sew_out_prod;	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);//
			$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_floor_sewOut_array[$fyear][$val["FLOOR_ID"]]['subSew'] += $subSewOut_costUSD;
			}
		}	
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);
	
	$floor_width=count($floorArray)*80;
	$tbl_width = 140+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Yearly Revenue Report <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Fiscal Year</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?></th>
	            	<?
	            }
	            ?>
	            <th width="80">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
		        	foreach ($fiscal_year_arr as $year => $val) 
		        	{
		        		//$fiscal_total = 0;		        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				            <td><a href="javascript:void()" onclick="report_generate_by_year('<? echo $year?>','4')"><? echo $year;?></a></td>
                            <?
							$rmg_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$floor_rmg=$year_floor_array[$year][$floor_id]['finishing']+$subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];?>)"><?  echo number_format($floor_rmg,0);?></td>
				            	<?
								$rmg_floor_total[$floor_id]+=$floor_rmg;
								$rmg_floor_tot+=$floor_rmg;
				            }
							?>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($rmg_floor_tot,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$floor_rmg;
	            }
	            ?>
	            <th><? echo number_format($total_ashulia_rmg,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="report_generate_by_year_ashulia_kal_rmg") //Monthly Ashulia of RMG
{
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	if($prod_reso_allocation==1)
	{
	 $sql_fin_prod=" SELECT c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b, prod_resource_mst c where a.id=b.mst_id  and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.location=3 and a.floor_id is not null and a.floor_id <> 0 order by a.floor_id";
	}
	else
	{
			 $sql_fin_prod=" SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.location=3 and a.floor_id is not null and a.floor_id <> 0 order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	//$con = connect();
	//execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,3) and ENTRY_FORM=3");
	//oci_commit($con);
	//fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";

	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);


	  $sql_sub_sewOut=" SELECT a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id=3 and a.status_active=1  order by a.floor_id";
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		//foreach($fiscal_year_arr as $fyear=>$ydata)
		//{
			$fyear=$val[csf("month_year")];
			$sew_out_prod=$val[csf("sew_out")];
			$main_array[$fyear]['qty']+=$sew_out_prod;//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		//	echo $sew_smv.'='.$sew_smv.'X';
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
				$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
				$year_floor_array[$fyear][$val[csf('floor_id')]]['finishing'] += $finish_cost;
			}
		//}
		$floor_id=$floor_library[$val[csf('floor_id')]]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$myear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
			$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_floor_sewOut_array[$myear][$val[csf("floor_id")]]['subSew'] += $subSewOut_costUSD;
			}
			
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	/*$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);*/
	
	$floor_width=count($floorArray)*80;
	$tbl_width = 140+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->
	<br>
	   <!-- <table width="<? //echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id].'(Ashulia RMG)'; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Monthly Revenue Report <? echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Month</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?></th>
	            	<?
	            }
	            ?>
	            <th width="80">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
		        	foreach ($fiscalMonth_arr as $year => $val) 
		        	{
		        		 $year_ex = explode("-", $year);
		        		$fiscal_total = 0;		      	        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				              <td><a href="javascript:void()" onclick="report_generate_by_month('<? echo $year?>',4)"><? echo date('F-y',strtotime($year));?></a></td>
                            <?
							$rmg_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$floor_rmg=$year_floor_array[$year][$floor_id]['finishing']+$subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];?>)"><?  echo number_format($floor_rmg,0);?></td>
				            	<?
								$rmg_floor_total[$floor_id]+=$floor_rmg;
								$rmg_floor_tot+=$floor_rmg;
				            }
							?>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($rmg_floor_tot,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$floor_rmg;
	            }
	            ?>
	            <th><? echo number_format($total_ashulia_rmg,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="report_generate_by_month_ashulia_rmg") //Daily Ashulia of RMG
{
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$month_year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	if($prod_reso_allocation==1)
	{
	$sql_fin_prod=" SELECT c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.location=3 and a.floor_id is not null and a.floor_id <> 0 order by a.floor_id";
	}
	else
	{
		$sql_fin_prod=" SELECT a.sewing_line as line_number a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.location=3 and a.floor_id is not null and a.floor_id <> 0 order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);


	 $sql_sub_sewOut=" SELECT a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year' and a.production_type=2 and a.location_id=3 and a.status_active=1 order by a.floor_id";
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		//foreach($fiscal_year_arr as $fyear=>$ydata)
		//{
			$fyear=$val[csf("month_year")];
			$sew_out_prod=$val[csf("sew_out")];
			$main_array[$fyear]['qty']+=$sew_out_prod;//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		//	echo $sew_smv.'='.$sew_smv.'X';
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
				$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
				$year_floor_array[$fyear][$val[csf('floor_id')]]['finishing'] += $finish_cost;
			}
		//}
		$floor_id=$floor_library[$val[csf('floor_id')]]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$myear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
			$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_floor_sewOut_array[$myear][$val[csf("floor_id")]]['subSew'] += $subSewOut_costUSD;
			}
			
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floorArray)*80;
	$tbl_width = 140+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <!--<table width="<?// echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
        <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Daily Revenue Report <? echo $year; ?></b></h3>
	    <div id="daily_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Date</th>
                 <?
				 $gmt_year='';
				$gmt_year=date('Y',strtotime($year));
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="8" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?></th>
	            	<?
	            }
	            ?>
	            <th width="80">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
		        	foreach ($days_arr as $year => $val) 
		        	{
		        		$gmt_date=date('d-M',strtotime($year)).'-'.$gmt_year;
						
		        		$fiscal_total = 0;		      	        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				               <td><? echo date('d-F',strtotime($year));?></td>
                            <?
							$rmg_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$floor_rmg=$year_floor_array[$year][$floor_id]['finishing']+$subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod(<? echo $year_floor_array[$year][$floor_id]['finishing'];?>)*SubCon Order Rate(<? echo $subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id; ?>','ashulia_floor_kal',11,'850');" ><?  echo number_format($floor_rmg,0);?></a></td>
				            	<?
								$rmg_floor_total[$floor_id]+=$floor_rmg;
								$rmg_floor_tot+=$floor_rmg;
				            }
							?>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($rmg_floor_tot,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$floor_rmg;
	            }
	            ?>
	            <th><? echo number_format($total_ashulia_rmg,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 

if($action=="report_generate_ratanpur_kal_rmg") //Yearly Ratanpur of KAL
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exlastYear[1];
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$fiscal_year_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$fiscal_year_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	// print_r($fiscal_year_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2"; //KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		//print_r($financial_para_cpm);
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc"; 
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17)//3rd //test-Fid=49
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==15) //4th
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==53) //Sample Floor -F-id=50
			{
			$floor_group_samf_arr[$flr_grp].=$val[csf('id')].',';
			}
			
		}
		//print_r($floor_group_ff_arr);
		if($prod_reso_allocation==1)
		{
			$sql_fin_prod="SELECT a.sewing_line,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,a.production_date,c.line_number";
			foreach($fiscal_year_arr as $fyear=>$ydata)
			{
				$exydata=explode("_",$ydata);
				$fyear=str_replace("-","_",$fyear); //prod_resource_mst
				
				$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS m$fyear ";
				$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS sew$fyear ";
			}//sewing_line
		$sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type in(5)  and a.production_source in(1) and a.location=5   and a.floor_id is not null and a.floor_id <> 0  order by a.floor_id";
		}
		else
		{
			$sql_fin_prod="SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,a.production_date";
			foreach($fiscal_year_arr as $fyear=>$ydata)
			{
				$exydata=explode("_",$ydata);
				$fyear=str_replace("-","_",$fyear); //prod_resource_mst
				
				$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS m$fyear ";
				$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS sew$fyear ";
			}//sewing_line
			
			$sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type in(5)  and a.production_source in(1) and a.location=5   and a.floor_id is not null and a.floor_id <> 0  order by a.floor_id";
		}
	
	
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	 $sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0   $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	//print_r($po_job_array);
	
	// $cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1   $cm_po_cond ";
	 $cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	// =================================== SubCon Sewout =============================
	if($prod_reso_allocation==1)
	{
	$sql_sub_sewOut="SELECT c.line_number,a.order_id,a.floor_id,a.production_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS m$fyear ";
	}
	  $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a,prod_resource_mst c where  c.id=a.line_id and a.company_id in($cbo_company_id) and a.location_id=5 and a.production_date between '$startDate' and '$endDate' and a.production_type=2 order by a.floor_id";
	}
	else
	{
		$sql_sub_sewOut="SELECT a.line_id as line_number,a.order_id,a.floor_id,a.production_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS m$fyear ";
	}
	   $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.location_id=5 and a.production_date between '$startDate' and '$endDate' and a.production_type=2 order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== subcon kniting =============================
	$sql_sub_knit="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS m$fyear ";
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id) THEN b.product_qnty END) AS fm$fyear ";
	}
	 $sql_sub_knit.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4) and a.location_id=5 ";
	//entry_form=292, product_type=4

	// $sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	// echo $sql_sub_knit;die();
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// =================================== subcon dyeing =============================
	$sql_sub_dye="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_dye.=", (CASE WHEN a.product_date between '$exydata[0]' and '$exydata[1]' THEN b.product_qnty END) AS m$fyear ";
	}
	$sql_sub_dye.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$year_floor_array=array();$year_floor_array2=array();$year_floor_array3=array();$year_floor_array4=array();
	foreach ($sql_fin_prod_res as $val) 
	{	
		
		//$floor_idArr=$val[csf('floor_id')].'_'.$val[csf('floor_id')];
		//$floor_group=rtrim($floor_line_group_arr[$val[csf('floor_id')]],',');
		//echo $floor_line_group_arr[$val[csf('floor_id')]].', ';
	//	echo $val[csf('floor_id')].'='.$floor_group.'<br>';
	 //echo $floor_group.', ';  
	// print_r($floor_group_ff_arr);
	//echo $val[csf('floor_id')].'x,';
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			//$myear='m'.$nyear;
			$myear='sew'.$nyear;
			$pmyear='pr'.$nyear;
			$embrmyear='embr'.$nyear;
			$wrmyear='wr'.$nyear;
			$sew_oput_prod=$val[csf($myear)];
			$main_array[$fyear]['qty']+=$sew_oput_prod;
			$main_array[$fyear]['location'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			//$line_group=rtrim($floor_line_group_arr[$val[csf('floor_id')]],",");
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			 $cm_cost_based_on_date="";
			// echo $val[csf('po_break_down_id')].'='.$cm_cost_method_based_on.', ';
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				//echo $shipment_date.', ';
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
			 
			  $line_number=array_unique(explode(",",$val[csf('line_number')]));
			  //echo $line_number.'d,';
			  $ff_floor_id=$val[csf('floor_id')];
			foreach($floor_group_ff_arr as $flr_grop1=>$val1) //1st Floor
			{
				 
				$flr_grop_ex=explode("_",$flr_grop1);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$ff_floor_id) 
				{
				if($sew_smv>0)
				{
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array[$fyear][$flr_grop1][$lineId]['finishing'] += $finish_cost;
					}
				 }
				}
				 //Group end
			} //floor_group_sf_arr
			foreach($floor_group_sf_arr as $flr_grop2=>$val2) //2st Floor
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				if($flr_grop_ex2[0]==$ff_floor_id) 
				{
				if($sew_smv>0)
				{
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array2[$fyear][$flr_grop2][$lineId]['finishing'] += $finish_cost;
					}
				 }
				}
				 //Group end
			} //floor_group_sf_arr
			foreach($floor_group_gf_arr as $flr_grop3=>$val3) //3rd Floor
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				if($flr_grop_ex3[0]==$ff_floor_id) 
				{
				if($sew_smv>0)
				{
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array3[$fyear][$flr_grop3][$lineId]['finishing'] += $finish_cost;
					}
				 }
				}
				 //Group end
			} 
			foreach($floor_group_tf_arr as $flr_grop4=>$val4) //4rd Floor
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				if($flr_grop_ex4[0]==$ff_floor_id) 
				{
				if($sew_smv>0)
				{
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array4[$fyear][$flr_grop4][$lineId]['finishing'] += $finish_cost;
					}
				 }
				}
				 //Group end
			}//End
			foreach($floor_group_samf_arr as $flr_grop5=>$val5) //Sample Floor
			{
				$flr_grop_ex5=explode("_",$flr_grop5);
				if($flr_grop_ex5[0]==$ff_floor_id) 
				{
				if($sew_smv>0)
				{
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array5[$fyear][$flr_grop5][$lineId]['finishing'] += $finish_cost;
					}
				 }
				}
				 //Group end
			} 
			
			
		}
	}
	// print_r($year_floor_array2);
	//SubCon Sewing Out
$subCon_year_floor_sewOut_array=array();$subCon_year_floor_sewOut_array2=array();
$subCon_year_floor_sewOut_array3=array();
$subCon_year_floor_sewOut_array4=array();

	foreach ($sql_sub_sewOut_result as $val) 
	{			
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='m'.$nyear;		
			$sub_sew_prod=$val[csf($myear)];
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$sub_sew_prod;	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$sub_ff_floor_id=$val[csf('floor_id')];
		//	$line_number=$val[csf('line_number')];
			 $line_number=array_unique(explode(",",$val[csf('line_number')]));
			foreach($floor_group_ff_arr as $s_flr_grop=>$val1)//1st 1
			{
				$flr_grop_ex=explode("_",$s_flr_grop);
				
				if($flr_grop_ex[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					
					foreach( $line_number as $lineId)
					  {
						 // echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$subSewOut_costUSD.'T, ';
					  $subCon_year_floor_sewOut_array[$fyear][$s_flr_grop][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					 }
				}
			}//flr Group End
			foreach($floor_group_sf_arr as $flr_grop2=>$val2)//1st 1
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					foreach( $line_number as $lineId)
					  {	
					  $subCon_year_floor_sewOut_array2[$fyear][$flr_grop2][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}//flr Group End
			foreach($floor_group_gf_arr as $flr_grop3=>$val3)//1st 1
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					foreach( $line_number as $lineId)
					  {		
					$subCon_year_floor_sewOut_array3[$fyear][$flr_grop3][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}//flr Group End
			foreach($floor_group_tf_arr as $flr_grop4=>$val4)//1st 1
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$sub_ff_floor_id) 
				{
				if($subsewOut_cost>0)
				{
				$subSewOut_costUSD = $subsewOut_cost/$rate;	
				$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
				$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
				 foreach( $line_number as $lineId)
				 {	
				 $subCon_year_floor_sewOut_array4[$fyear][$flr_grop4][$lineId]['subSew'] += $subSewOut_costUSD;
				 }
				}
				}
			}//flr Group End
			
		}	
	}
	//print_r($subCon_year_floor_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floor_group_ff_arr)*70+count($floor_group_sf_arr)*70+count($floor_group_gf_arr)*70+count($floor_group_tf_arr)*70+count($floor_group_samf_arr)*70;
	$tbl_width = 400+$floor_width;
	ob_start();	
	//echo count($floor_group_gf_arr).'AZAAAAAAAAAA';
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Yearly Revenue Report <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="70">Fiscal Year</th>
                 <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_ff_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id); 
				
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);?></th>
	            	<?
	            }
	            ?>
                 <th width="70">1st Floor</th>
                  <? 
				  
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_sf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
	            ?>
                 <th width="70">2nd Floor</th>
                 
                  <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_gf_arr>0))
				{ 
	            ?>
                 <th width="70">3nd Floor</th>
                 
                  <? 
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_tf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">4th Floor</th>
                 <?
				}
				foreach ($floor_group_samf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th width="70">Sample Floor</th>
                 <?
				}
				
				 ?>
	        <th width="70">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($fiscal_year_arr as $year => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	        		
			        	?>     
				         <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
				            <td><a href="javascript:void()" onclick="report_generate_by_year('<? echo $year?>','3')"><? echo $year;?></a></td>
                            <?
							$i++;
							$ff_rmg_floor_tot=0;
				            foreach ($floor_group_ff_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$ff_floor_rmg_line=0;$sub_ff_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
									$ff_floor_rmg_line+=$year_floor_array[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										$sub_ff_floor_rmg_line+=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										//echo $subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'].'d ';
									}
								$ff_floor_rmg=$ff_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_ff_floor_rmg_line;?>)"><?  echo number_format($ff_floor_rmg,0);?></td>
				            	<?
								$ff_rmg_floor_total[$floor_id]+=$ff_floor_rmg;
								$ff_rmg_floor_tot+=$ff_floor_rmg;
				            }
							?>
                                 <td align="right" title="All 1st Floor"><? echo number_format($ff_rmg_floor_tot,0); ?></td>
                             <?
							$sf_rmg_floor_tot=0;
				            foreach ($floor_group_sf_arr as $floor_id => $val)
				            {
									$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$sf_floor_rmg_line=0;$sub_sf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$sf_floor_rmg_line+=$year_floor_array2[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									$sub_sf_floor_rmg_line+=$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									}
								$sf_floor_rmg=$sf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_sf_floor_rmg_line;?>)"><?  echo number_format($sf_floor_rmg,0);?></td>
				            	<?
								$sf_rmg_floor_total[$floor_id]+=$sf_floor_rmg;
								$sf_rmg_floor_tot+=$sf_floor_rmg;
				            }
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($sf_rmg_floor_tot,0); ?></td>
                             <?
							$gf_rmg_floor_tot=0;
				            foreach ($floor_group_gf_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$gf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$gf_floor_rmg_line+=$year_floor_array3[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
									}
								$gf_floor_rmg=$gf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];?>)"><?  echo number_format($gf_floor_rmg,0);?></td>
				            	<?
								$gf_rmg_floor_total[$floor_id]+=$gf_floor_rmg;
								$gf_rmg_floor_tot+=$gf_floor_rmg;
				            }
							if(count($floor_group_gf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($gf_rmg_floor_tot,0); ?></td>
                             <?
					       }
							$tf_rmg_floor_tot=0;
				            foreach ($floor_group_tf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tf_floor_rmg_line+=$year_floor_array4[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
								}
								$tf_floor_rmg=$tf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];?>)"><?  echo number_format($tf_floor_rmg,0);?></td>
				            	<?
								$tf_rmg_floor_total[$floor_id]+=$tf_floor_rmg;
								$tf_rmg_floor_tot+=$tf_floor_rmg;
				            }
							if(count($floor_group_tf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($tf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$samf_rmg_floor_tot=0;
				            foreach ($floor_group_samf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$samf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$samf_floor_rmg_line+=$year_floor_array5[$year][$floor_id][$lId]['finishing'];
								}
								$samf_floor_rmg=$samf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];?>)"><?  echo number_format($samf_floor_rmg,0);?></td>
				            	<?
								$samf_rmg_floor_total[$floor_id]+=$samf_floor_rmg;
								$samf_rmg_floor_tot+=$samf_floor_rmg;
				            }
							if(count($floor_group_samf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($samf_rmg_floor_tot,0); ?></td>
                            <?
							}
							
							$total_floor_ratanpur=$tf_rmg_floor_tot+$gf_rmg_floor_tot+$sf_rmg_floor_tot+$ff_rmg_floor_tot+$samf_rmg_floor_tot;
							?>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($total_floor_ratanpur,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody> 
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floor_group_ff_arr as $floor_id => $val)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><?  echo number_format($ff_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$ff_rmg_floor_total[$floor_id]; 
	            }
	            ?>
  				<th><? echo number_format($gr_rmg_floor_tot,0); ?></th>
				 <?
				 $sf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_sf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($sf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$sf_gr_rmg_floor_tot+=$sf_rmg_floor_total[$floor_id]; 
	            }
	            ?>
                <th><? echo number_format($sf_gr_rmg_floor_tot,0); ?></th>
                 <?
				 $gf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf_gr_rmg_floor_tot+=$gf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf_arr>0))
				{
	            ?>
                <th><? echo number_format($gf_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				 $gr_tf_floor_tot=0;
	            foreach ($floor_group_tf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_tf_floor_tot+=$tf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_tf_floor_tot,0); ?></th>
                 <?
				}
				 $gr_samf_floor_tot=0;
	            foreach ($floor_group_samf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($samf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_samf_floor_tot+=$samf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_samf_floor_tot,0); ?></th>
                 <?
				}
	            ?>
                <th><? echo number_format($gr_tf_floor_tot+$gf_gr_rmg_floor_tot+$sf_gr_rmg_floor_tot+$gr_rmg_floor_tot+$gr_samf_floor_tot,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="report_generate_by_year_ratanpur_kal_rmg") //Monthly Ratanpur Kal of RMG
{
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";//KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17) //3rd //test-Fid=49
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==15)
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==53)//Sample //Sample Floor -F-id=50
			{
			$floor_group_samf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	//print_r($floor_group_ff_arr);
	//echo $prod_reso_allocation.'d';;
	if($prod_reso_allocation==1)
	{
	  $sql_fin_prod=" SELECT c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and    '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id  and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.location=5 and a.floor_id is not null and a.floor_id <> 0     order by a.floor_id";
	}
	else
	{
		 $sql_fin_prod=" SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.location=5 and a.floor_id is not null and a.floor_id <> 0   order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	 
	
	//$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	if($prod_reso_allocation==1)
	{
	  $sql_sub_sewOut=" SELECT c.line_number,a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a ,prod_resource_mst c where c.id=a.line_id and a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id=5 and a.location_id=5  order by a.floor_id";
	}
	else 
	{
		$sql_sub_sewOut=" SELECT a.line_id as line_number,a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a ,prod_resource_mst c where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id=5 and a.status_active=1 order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$year_floor_array=array();$year_floor_array2=array();$year_floor_array3=array();$year_floor_array4=array();
	foreach ($sql_fin_prod_res as $val) 
	{	
		
			$fyear=$val[csf("month_year")];
			$sew_out_prod=$val[csf("sew_out")];
			$main_array[$fyear]['qty']+=$val[csf("sew_out")];//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			
		//	echo $sew_smv.'='.$sew_smv.'X';
		 $ff_floor_id=$val[csf('floor_id')];
		 $sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		 $cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				
				 $line_number=array_unique(explode(",",$val[csf('line_number')]));
				 $tot_sew_out_qty2=0;
			foreach($floor_group_ff_arr as $flr_grop1=>$val1) //1st Floor
			{
				$flr_grop_ex=explode("_",$flr_grop1);
				if($flr_grop_ex[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
					//	echo $sew_smv.'='.$sew_out_prod.'='.$cost_per_minute.'='.$exch_rate.'<br>';
					
						foreach( $line_number as $lineId)
					    {
						$year_floor_array[$fyear][$flr_grop1][$lineId]['finishing'] += $finish_cost;
						 $tot_sew_out_qty2+=$exch_rate;;
						}
					}
				}
			}
		
			foreach($floor_group_sf_arr as $flr_grop2=>$val2) //2nd Floor
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				if($flr_grop_ex2[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array2[$fyear][$flr_grop2][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
			foreach($floor_group_gf_arr as $flr_grop3=>$val3) //3rd Floor
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				if($flr_grop_ex3[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array3[$fyear][$flr_grop3][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
			foreach($floor_group_tf_arr as $flr_grop4=>$val4) //4th Floor
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				if($flr_grop_ex4[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array4[$fyear][$flr_grop4][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
			
			foreach($floor_group_samf_arr as $flr_grop5=>$val5) //Sample Floor
			{
				$flr_grop_ex5=explode("_",$flr_grop5);
				if($flr_grop_ex5[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array5[$fyear][$flr_grop5][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
		
	}
		//echo $tot_sew_out_qty2.'=A';;
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	$subCon_year_floor_sewOut_array=array();$subCon_year_floor_sewOut_array2=array();
	$subCon_year_floor_sewOut_array3=array();
	$subCon_year_floor_sewOut_array4=array();

	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$myear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$sub_ff_floor_id=$val[csf('floor_id')];
			 $line_number=array_unique(explode(",",$val[csf('line_number')]));
			foreach($floor_group_ff_arr as $s_flr_grop=>$val1)//1st 1
			{
				$flr_grop_ex=explode("_",$s_flr_grop);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					foreach( $line_number as $lineId)
					  {	
					$subCon_year_floor_sewOut_array[$myear][$s_flr_grop][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			foreach($floor_group_sf_arr as $s_flr_grop2=>$val2)//2nd
			{
				$flr_grop_ex2=explode("_",$s_flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					  {	
					 // echo $subSewOut_costUSD.',';
					$subCon_year_floor_sewOut_array2[$myear][$s_flr_grop2][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			foreach($floor_group_gf_arr as $s_flr_grop3=>$val3)//2nd
			{
				$flr_grop_ex3=explode("_",$s_flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					  {	
					$subCon_year_floor_sewOut_array3[$myear][$s_flr_grop3][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			foreach($floor_group_tf_arr as $s_flr_grop4=>$va4l)//2nd
			{
				$flr_grop_ex4=explode("_",$s_flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					  {	
					$subCon_year_floor_sewOut_array4[$myear][$s_flr_grop4][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floor_group_ff_arr)*70+count($floor_group_sf_arr)*70+count($floor_group_gf_arr)*70+count($floor_group_tf_arr)*70+count($floor_group_samf_arr)*70;
	$tbl_width = 400+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	  <!--  <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
        <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Monthly Revenue Report <? echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	             <th width="60">Month Year</th>
                 <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_ff_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id); 
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);?></th>
	            	<?
	            }
	            ?>
                 <th width="70">1st Floor</th>
                  <? 
				  
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_sf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
	            ?>
                 <th width="70">2nd Floor</th>
                 
                  <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_gf_arr>0))
				{ 
	            ?>
                 <th width="70">3nd Floor</th>
                 
                  <? 
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_tf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">4th Floor</th>
                 <?
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_samf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th width="70">Sample Floor</th>
                 <?
				}
				
				 ?>
	        <th width="100">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
		        	foreach ($fiscalMonth_arr as $year => $val) 
		        	{
		        		 $year_ex = explode("-", $year);
		        		$fiscal_total = 0;		      	        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				              <td><a href="javascript:void()" onclick="report_generate_by_month('<? echo $year?>',3)"><? echo date('F-y',strtotime($year));?></a></td>
                            <?
							$i++;
							$ff_rmg_floor_tot=0;
				            foreach ($floor_group_ff_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$ff_floor_rmg_line=0;$sub_ff_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$sub_ff_floor_rmg_line+=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										$subSew=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										if($subSew>0) $subSew_m=$subSew;else $subSew_m=0;
										if($year_floor_array[$year][$floor_id][$lId]['finishing']>0)
										{
											//echo $lId.'='.$year_floor_array[$year][$floor_id][$lId]['finishing'].'<br>';
										$ff_floor_rmg_line+=$year_floor_array[$year][$floor_id][$lId]['finishing']+$subSew_m;		
										}
									}
									
								$ff_floor_rmg=$ff_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_ff_floor_rmg_line;?>)"><?  echo number_format($ff_floor_rmg,0);?></td>
				            	<?
								$ff_rmg_floor_total[$floor_id]+=$ff_floor_rmg;
								$ff_rmg_floor_tot+=$ff_floor_rmg;
				            }
							?>
                                 <td align="right" title="All 1st Floor"><? echo number_format($ff_rmg_floor_tot,0); ?></td>
                             <?
							$sf_rmg_floor_tot=0;
				            foreach ($floor_group_sf_arr as $floor_id => $val)
				            {
									$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$sf_floor_rmg_line=0;$sub_sf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										//echo $subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'].'D, ';
										$sub_ff_floor_rmg_line+=$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
										$sf_floor_rmg_line+=$year_floor_array2[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									}
				            	
								$sf_floor_rmg=$sf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_sf_floor_rmg_line;?>)"><?  echo number_format($sf_floor_rmg,0);?></td>
				            	<?
								$sf_rmg_floor_total[$floor_id]+=$sf_floor_rmg;
								$sf_rmg_floor_tot+=$sf_floor_rmg;
				            }
							?>
                            <td align="right" title=""><? echo number_format($sf_rmg_floor_tot,0); ?></td>
                             <?
							$gf_rmg_floor_tot=0;
				            foreach ($floor_group_gf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$gf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$gf_floor_rmg_line+=$year_floor_array3[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
								}
									
								$gf_floor_rmg=$gf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];?>)"><?  echo number_format($gf_floor_rmg,0);?></td>
				            	<?
								$gf_rmg_floor_total[$floor_id]+=$gf_floor_rmg;
								$gf_rmg_floor_tot+=$gf_floor_rmg;
				            }
							if(count($floor_group_gf_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($gf_rmg_floor_tot,0); ?></td>
                             <?
					       }
							$tf_rmg_floor_tot=0;
				            foreach ($floor_group_tf_arr as $floor_id => $val)
				            {
				           		 $lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tf_floor_rmg_line+=$year_floor_array4[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
								}
								
								$tf_floor_rmg=$tf_floor_rmg_line+$subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];?>)"><?  echo number_format($tf_floor_rmg,0);?></td>
				            	<?
								$tf_rmg_floor_total[$floor_id]+=$tf_floor_rmg;
								$tf_rmg_floor_tot+=$tf_floor_rmg;
				            }
							if(count($floor_group_tf_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($tf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$samf_rmg_floor_tot=0;
				            foreach ($floor_group_samf_arr as $floor_id => $val)
				            {
				           		 $lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$samf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$samf_floor_rmg_line+=$year_floor_array5[$year][$floor_id][$lId]['finishing'];
								}
								$samf_floor_rmg=$samf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];?>)"><?  echo number_format($samf_floor_rmg,0);?></td>
				            	<?
								$samf_rmg_floor_total[$floor_id]+=$samf_floor_rmg;
								$samf_rmg_floor_tot+=$samf_floor_rmg;
				            }
							if(count($floor_group_samf_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($samf_rmg_floor_tot,0); ?></td>
                            <?
							}
							
							$total_floor_ratanpur=$tf_rmg_floor_tot+$gf_rmg_floor_tot+$sf_rmg_floor_tot+$ff_rmg_floor_tot+$samf_rmg_floor_tot;
							?>
                            
				            <td align="right" title=""><? echo number_format($total_floor_ratanpur,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floor_group_ff_arr as $floor_id => $val)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><?  echo number_format($ff_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$ff_rmg_floor_total[$floor_id]; 
	            }
	            ?>
  				<th><? echo number_format($gr_rmg_floor_tot,0); ?></th>
				 <?
				 $sf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_sf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($sf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$sf_gr_rmg_floor_tot+=$sf_rmg_floor_total[$floor_id]; 
	            }
	            ?>
                <th><? echo number_format($sf_gr_rmg_floor_tot,0); ?></th>
                 <?
				 $gf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf_gr_rmg_floor_tot+=$gf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf_arr>0))
				{
	            ?>
                <th><? echo number_format($gf_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				 $gr_tf_floor_tot=0;
	            foreach ($floor_group_tf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_tf_floor_tot+=$tf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_tf_floor_tot,0); ?></th>
                 <?
				}
				 $gr_samf_floor_tot=0;
	            foreach ($floor_group_samf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($samf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_samf_floor_tot+=$samf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_samf_floor_tot,0); ?></th>
                 <?
				}
	            ?>
                <th><? 
				//echo $gr_tf_floor_tot.'='.$gf_gr_rmg_floor_tot.'='.$sf_gr_rmg_floor_tot.'='.$gr_rmg_floor_tot.'='.$gr_samf_floor_tot;
				echo number_format($gr_tf_floor_tot+$gf_gr_rmg_floor_tot+$sf_gr_rmg_floor_tot+$gr_rmg_floor_tot+$gr_samf_floor_tot,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="report_generate_by_month_ratanpur_rmg") //Daily Ratanpur Kal of RMG
{
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$month_year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2"; //KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17)//3rd //test-Fid=49
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==15) //4th
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==53) //Sample Floor -F-id=50
			{
			$floor_group_samf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	if($prod_reso_allocation==1)
	{
	 		 $sql_fin_prod=" SELECT a.sewing_line,c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0   and a.location=5 order by a.floor_id";
	}
	else
	{
			$sql_fin_prod=" SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.location=5 and a.floor_id is not null and a.floor_id <> 0   order by a.floor_id";
	}
	//echo $prod_reso_allocation;

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	 /*$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('sew_smv')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}*/
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);


	if($prod_reso_allocation==1)
	{
	  $sql_sub_sewOut=" SELECT c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year'  and a.status_active=1 and a.production_type=2  and a.location_id=5 order by a.floor_id";
	}
	else
	{
		$sql_sub_sewOut=" SELECT a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.production_type=2  and a.location_id=5 order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$tot_sew_out_qty=0;
	foreach ($sql_fin_prod_res as $val) 
	{	
		 
			$fyear=$val[csf("month_year")];
			//echo $val[csf("sew_out")].'<br> ';
			$sew_out_qty=$val[csf("sew_out")];
		
			$main_array[$fyear]['qty']+=$val[csf("sew_out")];//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		//	echo $sew_smv.'='.$sew_smv.'X';
			$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				$line_number=array_unique(explode(",",$val[csf('line_number')]));
			  //echo $line_number.'d,';
			  $ff_floor_id=$val[csf('floor_id')];
			  $tot_sew_out_qty=0;
			foreach($floor_group_ff_arr as $flr_grop1=>$val1) //1st Floor
			{
				$flr_grop_ex=explode("_",$flr_grop1);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						//echo $fyear.'d';
						//echo $sew_smv.'='.$sew_out_qty.'='.$cost_per_minute.'='.$exch_rate.'<br>';
							
						foreach( $line_number as $lineId)
						{
							//echo $lineId.'='.$sew_smv.'='.$sew_out_qty.'='.$cost_per_minute.'/'.$exch_rate.'<br> ';
						$year_floor_array[$fyear][$flr_grop1][$lineId]['finishing'] += $finish_cost;
						$tot_sew_out_qty+=$exch_rate;
						}
						
					}
				}
			} //Floor End
			
			foreach($floor_group_sf_arr as $flr_grop2=>$val2) //1st Floor
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
					//if($sew_out_qty=="") $sew_out_qty=0;else $sew_out_qty=$sew_out_qty;
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array2[$fyear][$flr_grop2][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
			foreach($floor_group_gf_arr as $flr_grop3=>$val3) //1st Floor
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array3[$fyear][$flr_grop3][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
			foreach($floor_group_tf_arr as $flr_grop4=>$val4) //4th Floor
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array4[$fyear][$flr_grop4][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
			foreach($floor_group_samf_arr as $flr_grop5=>$val5) //Sample Floor
			{
				$flr_grop_ex5=explode("_",$flr_grop5);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex5[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array5[$fyear][$flr_grop5][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
		
	}
	//echo $tot_sew_out_qty.'=B';;
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$fyear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."=".$val[csf('production_qnty')].'/'.$rate."<br>";
			$sub_ff_floor_id=$val[csf('floor_id')];
		   $line_number=array_unique(explode(",",$val[csf('line_number')]));
			foreach($floor_group_ff_arr as $s_flr_grop=>$val1)//1st 1
			{
				$flr_grop_ex=explode("_",$s_flr_grop);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					 {
					$subCon_year_floor_sewOut_array[$fyear][$s_flr_grop][$lineId]['subSew'] += $subSewOut_costUSD;
					 }
					}
				}
			}//flr Group End
			foreach($floor_group_sf_arr as $flr_grop2=>$val2)//1st 1
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					 {
					$subCon_year_floor_sewOut_array2[$fyear][$flr_grop2][$lineId]['subSew'] += $subSewOut_costUSD;
					 }
					}
				}
			}//flr Group End
			foreach($floor_group_gf_arr as $flr_grop3=>$val3)//1st 1
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					 {
					$subCon_year_floor_sewOut_array3[$fyear][$flr_grop3][$lineId]['subSew'] += $subSewOut_costUSD;
					 }
					}
				}
			}//flr Group End
			foreach($floor_group_tf_arr as $flr_grop4=>$val4)//1st 1
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$sub_ff_floor_id) 
				{
				if($subsewOut_cost>0)
				{
				$subSewOut_costUSD = $subsewOut_cost/$rate;	
				$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
				$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
				foreach( $line_number as $lineId)
				 {	
				 $subCon_year_floor_sewOut_array4[$fyear][$flr_grop4][$lineId]['subSew'] += $subSewOut_costUSD;
				 }
				}
				}
			}//flr Group End
			
	}
	//	print_r($subCon_year_floor_sewOut_array); //floor_group_samf_arr
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floor_group_ff_arr)*70+count($floor_group_sf_arr)*70+count($floor_group_gf_arr)*70+count($floor_group_tf_arr)*70+count($floor_group_samf_arr)*70;
	$tbl_width = 400+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->
<!--
	    <table width="<? //echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
          <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Daily Revenue Report <? echo $year; ?></b></h3>
	    <div id="daily_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	             <th width="60">Date</th>
                 <? 
				 $gmt_year='';
				$gmt_year=date('Y',strtotime($year));
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_ff_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id); 
				
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
	            ?>
                 <th width="70">1st Floor</th>
                  <? 
				  
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_sf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
	            ?>
                 <th width="70">2nd Floor</th>
                 
                  <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_gf_arr>0))
				{ 
	            ?>
                 <th width="70">3nd Floor</th>
                 
                  <? 
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_tf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);;//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">4th Floor</th>
                 <?
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_samf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);;//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">Sample Floor</th>
                 <?
				}
				 ?>
	        <th width="70">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($days_arr as $year => $val) 
		        	{
		        		$gmt_date=date('d-M',strtotime($year)).'-'.$gmt_year;
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        		$fiscal_total = 0;		      	        		
			        	?>     
				         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('trd_<?=$i; ?>','<?=$bgcolor;?>')" id="trd_<?=$i; ?>" style="font-size:12px">
				               <td><? echo date('d-F',strtotime($year));?></td>
                            <?
							$i++;
							$ff_rmg_floor_tot=0;
				            foreach ($floor_group_ff_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$ff_floor_rmg_line=0;	$sub_ff_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$subSew=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										if($subSew>0) $subSew_d=$subSew;else $subSew_d=0;
										if($year_floor_array[$year][$floor_id][$lId]['finishing']>0)
										{
										//echo $floor_id.',';
										$ff_floor_rmg_line+=$year_floor_array[$year][$floor_id][$lId]['finishing']+$subSew_d;
										}
									$sub_ff_floor_rmg_line+=$subSew_d;
									}
									//ratanpur_floor_kal
								$ff_floor_rmg=$ff_floor_rmg_line;//+$subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_ff_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','ratanpur_floor_kal',9,'850');" ><?  echo number_format($ff_floor_rmg,0);?></a></td>
				            	<?
								$ff_rmg_floor_total[$floor_id]+=$ff_floor_rmg;
								$ff_rmg_floor_tot+=$ff_floor_rmg;
				            }
							?>
                                 <td align="right" title="All 1st Floor"><? echo number_format($ff_rmg_floor_tot,0); ?></td>
                             <?
							$sf_rmg_floor_tot=0;
				            foreach ($floor_group_sf_arr as $floor_id => $val)
				            {
				            	
									$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$sf_floor_rmg_line=0;$sub_sf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$sf_floor_rmg_line+=$year_floor_array2[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];;
										$sub_sf_floor_rmg_line+=$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									}
									
								$sf_floor_rmg=$sf_floor_rmg_line;//+$subCon_year_floor_sewOut_array2[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_sf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','ratanpur_floor_kal',10,'850');" ><?  echo number_format($sf_floor_rmg,0);?></a></td>
				            	<?
								$sf_rmg_floor_total[$floor_id]+=$sf_floor_rmg;
								$sf_rmg_floor_tot+=$sf_floor_rmg;
				            }
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($sf_rmg_floor_tot,0); ?></td>
                             <?
							$gf_rmg_floor_tot=0;
				            foreach ($floor_group_gf_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$gf_floor_rmg_line=0;$sub_gf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										
										$gf_floor_rmg_line+=$year_floor_array3[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
										$sub_gf_floor_rmg_line+=$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
									}
									
								$gf_floor_rmg=$gf_floor_rmg_line;//+$subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_gf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','ratanpur_floor_kal',9,'850');" ><?  echo number_format($gf_floor_rmg,0);?></a></td>
				            	<?
								$gf_rmg_floor_total[$floor_id]+=$gf_floor_rmg;
								$gf_rmg_floor_tot+=$gf_floor_rmg;
				            }
							if(count($floor_group_gf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($gf_rmg_floor_tot,0); ?></td>
                             <?
					       }
							$tf_rmg_floor_tot=0;
				            foreach ($floor_group_tf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
								$tf_floor_rmg_line=0;$sub_tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tf_floor_rmg_line+=$year_floor_array4[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
									$sub_tf_floor_rmg_line+=$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
								}
									
								$tf_floor_rmg=$tf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_tf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$$lineIds_all=implode(",",array_unique(explode(",",$lineId)));; ?>','ratanpur_floor_kal',9,'850');" ><?  echo number_format($tf_floor_rmg,0);?></a></td>
				            	<?
								$tf_rmg_floor_total[$floor_id]+=$tf_floor_rmg;
								$tf_rmg_floor_tot+=$tf_floor_rmg;
				            }
							if(count($floor_group_tf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($tf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$samf_rmg_floor_tot=0;
				            foreach ($floor_group_samf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
								$samf_floor_rmg_line=0;$sub_tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$samf_floor_rmg_line+=$year_floor_array5[$year][$floor_id][$lId]['finishing'];
									//$sub_tf_floor_rmg_line+=$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
								}
									
								$samf_floor_rmg=$samf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_tf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$$lineIds_all=implode(",",array_unique(explode(",",$lineId)));; ?>','ratanpur_floor_kal',9,'850');" ><?  echo number_format($samf_floor_rmg,0);?></a></td>
				            	<?
								$samf_rmg_floor_total[$floor_id]+=$samf_floor_rmg;
								$samf_rmg_floor_tot+=$samf_floor_rmg;
				            }
							if(count($floor_group_samf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($samf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$total_floor_ratanpur=$tf_rmg_floor_tot+$gf_rmg_floor_tot+$sf_rmg_floor_tot+$ff_rmg_floor_tot+$samf_rmg_floor_tot;
							?>
                            
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($total_floor_ratanpur,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	           <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floor_group_ff_arr as $floor_id => $val)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><?  echo number_format($ff_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$ff_rmg_floor_total[$floor_id]; 
	            }
	            ?>
  				<th><? echo number_format($gr_rmg_floor_tot,0); ?></th>
				 <?
				 $sf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_sf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($sf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$sf_gr_rmg_floor_tot+=$sf_rmg_floor_total[$floor_id]; 
	            }
	            ?>
                <th><? echo number_format($sf_gr_rmg_floor_tot,0); ?></th>
                 <?
				 $gf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf_gr_rmg_floor_tot+=$gf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf_arr>0))
				{
	            ?>
                <th><? echo number_format($gf_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				 $gr_tf_floor_tot=0;
	            foreach ($floor_group_tf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_tf_floor_tot+=$tf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_tf_floor_tot,0); ?></th>
                 <?
				}
				 $gr_samf_floor_tot=0;
	            foreach ($floor_group_samf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($samf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_samf_floor_tot+=$samf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_samf_floor_tot,0); ?></th>
                 <?
				}
				
	            ?>
              <th><? echo number_format($gr_tf_floor_tot+$gf_gr_rmg_floor_tot+$sf_gr_rmg_floor_tot+$gr_rmg_floor_tot+$gr_samf_floor_tot,0); ?></th>
                
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 


if($action=="report_generate_sheet_jm") //Yearly Top Sheet of JM
{ 
 
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exlastYear[1];
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$fiscal_year_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$fiscal_year_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	// print_r($fiscal_year_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute as COST_PER_MINUTE, applying_period_date as APPLYING_PERIOD_DATE, applying_period_to_date as APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row['APPLYING_PERIOD_DATE'],'','',1);
			$applying_period_to_date=change_date_format($row['APPLYING_PERIOD_TO_DATE'],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row['COST_PER_MINUTE'];
			}
		}
	
	//$sql_fin_prod="SELECT a.location as LOACTION,a.po_break_down_id as POID,a.production_date as PRODUCTION_Date,a.item_number_id as ITEM_ID";
	$sql_fin_prod="SELECT a.location as LOCATION,a.embel_name as EMBEL_NAME,a.po_break_down_id as POID,a.production_date as PRODUCTION_DATE,a.item_number_id as ITEM_ID,b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS M$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS SEW$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS PR$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS EMBR$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS WR$fyear ";
	}
	$sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS SEW_OUT from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5,3)    and a.production_source in(1) and a.location <> 0 order by a.location";
	//echo $sql_fin_prod; 

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val['POID']] = $val['POID'];
		$sewing_qty_array[$val['POID']] = $val['SEW_OUT'];
	}

	// ========================= for kniting ======================
	$sql_kniting_dyeing="SELECT a.febric_description_id as DETER_ID,b.po_breakdown_id as POID,b.is_sales as IS_SALES,c.receive_date as RECEIVE_DATE,c.knitting_location_id as KNIT_LOCTION_ID";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_kniting_dyeing.=", (CASE WHEN c.receive_date between '$exydata[0]' and '$exydata[1]' THEN a.grey_receive_qnty END) AS M$fyear ";
	}
	$sql_kniting_dyeing.=" from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2)   and c.knitting_location_id>0 order by c.knitting_location_id ";


	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];

		}
	}
	// ========================= for dyeing ======================
	
	$dying_prod_sql="SELECT a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,f.detarmination_id as DETER_ID";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$dying_prod_sql.=", (CASE WHEN c.process_end_date between '$exydata[0]' and '$exydata[1]' THEN d.batch_qnty END) AS M$fyear ";
	}
	  $dying_prod_sql.="  from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d,product_details_master f where c.batch_id=a.id  and a.id=d.mst_id and c.batch_id=d.mst_id and f.id=d.prod_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against=1 and c.service_company in($cbo_company_id) and  c.process_end_date between '$startDate' and '$endDate' and a.status_active=1  and d.status_active=1 and c.status_active=1  order by c.floor_id,c.process_end_date ";
	$dying_prod_sql_res = sql_select($dying_prod_sql);
	foreach ($dying_prod_sql_res as $val) 
	{
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];
		}
	}
	
	$sub_dying_prod_sql="SELECT a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sub_dying_prod_sql.=", (CASE WHEN c.process_end_date between '$exydata[0]' and '$exydata[1]' THEN d.batch_qnty END) AS m$fyear ";
	}
	 $sub_dying_prod_sql.="  from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d where c.batch_id=a.id  and a.id=d.mst_id and c.batch_id=d.mst_id and c.load_unload_id=2 and a.batch_against=1 and c.entry_form=38 and c.service_company in($cbo_company_id)  and c.process_end_date between '$startDate' and '$endDate' and a.status_active=1 and d.status_active=1 and c.status_active=1  order by c.floor_id,c.process_end_date ";
	$sub_dying_prod_sql_res = sql_select($sub_dying_prod_sql);
	foreach ($sub_dying_prod_sql_res as $val) 
	{
	//	$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		$sub_po_id_array[$val['POID']] = $val['POID'];
	}
	

	
	/*$poIds = implode(",", array_unique($po_id_array));
	$salesIds = implode(",", array_unique($sales_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	if($salesIds !="")
	{
		$sales_cond="";
		if(count($sales_id_array)>999)
		{
			$chunk_arr=array_chunk($sales_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sales_cond=="") $sales_cond.=" and ( a.id in ($ids) ";
				else
					$sales_cond.=" or   a.id in ($ids) "; 
			}
			$sales_cond.=") ";

		}
		else
		{
			$sales_cond.=" and a.id in ($salesIds) ";
		}
	}*/
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
			
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 2, $sales_id_array, $empty_arr);//Sales ID Ref from=2
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	
	// $sql_sales = "select a.id,a.job_no, a.within_group,b.color_id,b.determination_id as DETER_ID,b.process_id,b.process_seq,b.body_part_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0   $sales_cond order by a.id desc";
	 $sql_sales = "select b.id as DTLS_ID,a.id as ID,a.job_no as JOB_NO, a.within_group as WITHING_GROUP,b.color_id as COLOR_ID,b.determination_id as DETER_ID,b.process_id as PROCESS_ID,b.process_seq as PROCESS_SEQ,b.body_part_id as BODY_PART_ID from fabric_sales_order_mst a,fabric_sales_order_dtls b,gbl_temp_engine g  where a.id=b.mst_id and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by b.id desc";
		$sql_sales_result = sql_select($sql_sales);//and a.company_id in($cbo_company_id)
		foreach ($sql_sales_result as $val) 
		{		
				$process_id=array_unique(explode(",",$val['PROCESS_ID']));
				$process_seqArr=array_unique(explode(",",$val['PROCESS_SEQ']));
				foreach($process_id as $p_key)
				{
						foreach($process_seqArr as $val_rate)
						{
							$process_Rate=explode("__",$val_rate);
							$process_Id=$process_Rate[0];
							$process_rate=$process_Rate[1];
							if($p_key==$process_Id)
							{
							$sales_data_array[$val['ID']][$val['DETER_ID']][$val['COLOR_ID']][$p_key]['process_rate'] = $process_rate;
							$sales_data_knit_array[$val['ID']][$val['DETER_ID']][$p_key]['process_rate'] = $process_rate;
							}
						}
				}
		}
	//$cm_po_cond = str_replace("id", "b.id", $po_cond);
	//$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
//$sql_po_sew="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
 
 $sql_sew_po="SELECT b.id as POID,b.po_quantity as POQTY,b.pub_shipment_date as PUBSHIPDATE,b.shipment_date as SHIPDATE,b.job_no_mst as JOB_NO from wo_po_break_down b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and  b.status_active=1 and b.is_deleted=0 "; 
	$sql_po_sew_result = sql_select($sql_sew_po);
	foreach ($sql_po_sew_result as $val) 
	{
		/*$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';*/
		$po_qty_array[$val['POID']] = $val['POQTY'];
		$po_job_array[$val['POID']]= $val['JOB_NO'];
		$po_date_array[$val['JOB_NO']]['ship_date'].= $val['SHIPDATE'].',';
		$po_date_array[$val['JOB_NO']]['pub_date'].= $val['PUBSHIPDATE'].',';
	}
	unset($sql_po_sew_result );
 /*$sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,d.color_size_sensitive,f.id as conv_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond order by f.id, b.id asc";*/
   $sql_po="SELECT b.id as POID,c.color_number_id as COLOR_ID,d.color_size_sensitive as COLOR_SIZE_SENSITIVE,d.lib_yarn_count_deter_id as DETER_ID,f.cons_process as CONS_PROCESS,f.id as CONV_ID,f.charge_unit as CHARGE_UNIT,f.color_break_down as COLOR_BREAK_DOWN from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f,gbl_temp_engine g where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and b.id=g.ref_val and g.ref_val=c.po_break_down_id and g.ref_val=e.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  order by f.id,b.id asc"; 
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		if($val['COLOR_BREAK_DOWN']!="")
		{
		$color_break_down=$val['COLOR_BREAK_DOWN'];
		}
		
		if($val['COLOR_SIZE_SENSITIVE']==3)
		{
		$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'] = $val['COLOR_SIZE_SENSITIVE'];
		}
		if($val['CONS_PROCESS']==31 && $color_break_down!='')
		{
			if($val['COLOR_SIZE_SENSITIVE']==3) //Contrast
			{
			$po_color_brk_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
			}
			else
			{
			$po_color_brk_fab_array[$val['POID']][$val['COLOR_ID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];

			}
			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val['POID']][$arr_2[3]][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$arr_2[1];
			
			}
		} 
		else if($val['CONS_PROCESS']==33) //Heatset
		{
			$po_color_fab_array2[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$val['CHARGE_UNIT'];
		}
		else
		{
			$po_color_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] = $val['CHARGE_UNIT'];
		}
		$po_color_knit_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['knit_rate'] = $val['CHARGE_UNIT'];
	}
	
	
	// $cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date as COSTING_DATE,c.costing_per as COSTING_PER,c.sew_smv as SEW_SMV,a.cm_cost as CM_COST,b.id as POID,d.smv_set as SMV_SET,d.gmts_item_id as GMTS_ID from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d,gbl_temp_engine g where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and d.job_id=b.job_id and d.job_id=c.job_id  and g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and a.status_active=1 and b.status_active=1 and c.status_active=1  ";
	$cm_sql_res = sql_select($cm_sql);
	
	$cm_cost_array = array();$pre_costing_per_arr = array();
	foreach ($cm_sql_res as $val) 
	{
		/*$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$pre_costing_per_arr[$val[csf('id')]]['costing_per']= $val[csf('costing_per')];*/
		
		$cm_cost_array[$val['POID']] = $val['CM_COST'];
		$pre_cost_smv_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_cost_array[$val['POID']]['costing_date'] = $val['COSTING_DATE'];
		$costing_per_arr[$val['POID']]= $val['COSTING_PER'];
	}
	//print_r($pre_costing_per_arr);
	/*echo $dying_prod_sql = "SELECT c.process_end_date,c.floor_id,d.production_qty,b.id as batch_id from pro_fab_subprocess c,pro_fab_subprocess_dtls d, pro_batch_create_mst a,pro_batch_create_dtls b where c.id=b.mst_id and c.batch_id=a.id and a.id=b.mst_id and b.mst_id=c.batch_id and  c.load_unload_id=2 and b.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond2 ";
	$sql_dyeing="SELECT b.po_breakdown_id";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_dyeing.=", SUM(CASE WHEN c.receive_date between '$exydata[0]' and '$exydata[1]' THEN a.receive_qnty END) AS m$fyear ";
	}
	
	
	$condition= new condition();
	//$condition->company_name("=$cbo_company_id");

	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();*/
	// var_dump($condition);

	//$conversion= new conversion($condition);
	//$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	//$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	/*$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname(); */
//echo "<pre>";print_r($emblishment_costing_arr_wash);die();

    $sql_pre_wash="SELECT  b.emb_name as EMB_NAME,d.id as AVG_ID,d.color_number_id as COLOR_ID,d.po_break_down_id as POID,c.id as COLOR_SIZE_ID,d.REQUIRMENT as REQUIRMENT,d.rate as RATE from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b,gbl_temp_engine g where d.job_id=a.id   and c.job_no_mst=a.job_no and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id  and b.id=d.pre_cost_emb_cost_dtls_id and  b.job_id=a.id  and  b.job_id=c.job_id and g.ref_val=c.po_break_down_id  and g.ref_val=d.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and d.requirment>0 and d.rate>0   order by d.id asc";

	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_rate']+= $val['RATE'];
		$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_req']+= $val['REQUIRMENT'];
		
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
	}
	unset($sql_wash_result);
	
	// ======================================= getting subcontact data =================================
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	$sql_subcon_po="SELECT b.id as ID,b.rate as RATE from subcon_ord_dtls b,gbl_temp_engine g where  g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0";
	$sql_subcon_po_res = sql_select($sql_subcon_po);
	foreach($sql_subcon_po_res as $row)
	{
		$order_wise_rate[$row['ID']]=$row['RATE'];
	}
	unset($sql_subcon_po_res);
	
	// print_r($order_wise_rate);

	// =================================== SubCon Sewout =============================
	$sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id as LOCATION_ID,a.production_date as PRODUCTION_DATE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS M$fyear ";
	}
	  $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id>0 order by a.location_id" ;
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== subcon kniting =============================
	$sql_sub_knit="SELECT b.order_id as ORDER_ID,a.product_date as PRODUCT_DATE,a.location_id as LOCATION_ID";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS M$fyear ";
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id) THEN b.product_qnty END) AS FM$fyear ";
	}
	 $sql_sub_knit.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4) and a.location_id>0 order by a.location_id ";
	//entry_form=292, product_type=4

	// $sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	// echo $sql_sub_knit;die();
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// =================================== subcon dyeing =============================
	$sql_sub_dye="SELECT b.order_id as ORDER_ID,a.product_date as PRODUCT_DATE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_dye.=", (CASE WHEN a.product_date between '$exydata[0]' and '$exydata[1]' THEN b.product_qnty END) AS M$fyear ";
	}
	$sql_sub_dye.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";

	// $sql_sub_dye = "SELECT b.order_id,to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=4";
	// echo $sql_sub_dye;die();
	$sql_sub_dye_res = sql_select($sql_sub_dye);

	$main_array = array();
	$locationArray = array();
	//$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	// a.location as LOACTION,a.po_break_down_id as POID,a.production_date as PRODUCTION_Date,a.item_number_id as ITEM_ID
	$tot_print_amount=$tot_print_qty=0;
	foreach ($sql_fin_prod_res as $val) 
	{	
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			//$myear='m'.$nyear;
			$myear='SEW'.$nyear;
			$pmyear='PR'.$nyear;
			$embrmyear='EMBR'.$nyear;
			$wrmyear='WR'.$nyear;
			$main_array[$fyear]['qty']+=$val[$myear];
			$main_array[$fyear]['location'] = $val['LOCATION'];
			
			/*$print_cost=$emblishment_costing_arr_name[$val['POID']][1];
			$print_qty=$emblishment_qty_arr_name[$val['POID']][1];
			
			$embro_cost=$emblishment_costing_arr_name[$val['POID']][2];
			$embro_qty=$emblishment_qty_arr_name[$val['POID']][2];
			
			$wash_cost=$emblishment_costing_arr_wash[$val['POID']][3];
			$wash_qty=$emblishment_qty_arr_wash[$val['POID']][3];*/
			//echo $print_cost.'D';
			$dzn_qnty=0;
			//$costing_per_arr[$val['POID']]
			$costing_per_id=$costing_per_arr[$val['POID']]; //$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			//echo $costing_perId.',';
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$color_id=$po_color_array[$val['COLOR_SIZE_BREAK_DOWN_ID']]['color_id'];
			if($color_id=='') $color_id=0;
			//$po_color_rate=$po_color_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['rate'];
			$avg_rate= $po_color_avg_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['avg_rate'];
			$avg_req= $po_color_avg_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['avg_req'];
			$po_color_rate=$avg_rate/$avg_req;
			
			$print_recv=$val[$pmyear]; 
			$print_avg_rate=$po_color_rate/$dzn_qnty;
			if($print_recv>0 && $print_avg_rate>0)
			{
				//echo $fyear.'='.$print_recv.'='.$print_cost.'='.$print_qty.'='.$dzn_qnty.'<br>';
				
				//echo $print_avg_rate.'B'.$dzn_qnty.'<br>';
			//$print_avg_rate=($print_cost/$print_qty)/$dzn_qnty;
			$print_amount=$print_recv*$print_avg_rate;
			$year_prod_cost_arr[$fyear]['print_recv'] += $print_amount;
			$tot_print_amount += $print_amount;
			$tot_print_qty += $print_recv;
			}
			$embro_avg_rate=$po_color_rate/$dzn_qnty;
			if($val[$embrmyear]>0 && $embro_avg_rate>0)
			{
			//$embro_avg_rate=($embro_cost/$embro_qty)/$dzn_qnty;
			
			$embro_amount=$val[$embrmyear]*$embro_avg_rate;
			$year_prod_cost_arr[$fyear]['embo_recv'] += $embro_amount;
			}
			$wash_avg_rate=$po_color_rate/$dzn_qnty;
			if($val[$wrmyear]>0 && $wash_avg_rate>0)
			{
				
			//$wash_avg_rate=($wash_cost/$wash_qty)/$dzn_qnty;
			$wash_amount=$val[$wrmyear]*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val['POID']][$val['ITEM_ID']]['sew_smv'];
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val['POID']]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				// $po_qty=$po_qty_array[$val[csf('po_break_down_id')]];
				//$cm_avg_cost=$cm_cost/12;
				//$finish_cost=$cm_avg_cost*$val[csf($myear)];
				if($val[$myear]=="") $val[$myear]=0;else $val[$myear]=$val[$myear];
				//echo $sew_smv.'='.$val[csf($myear)].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
				$finish_cost=($sew_smv*$val[$myear]*$cost_per_minute)/$exch_rate;
				$year_location_qty_array[$fyear][$val['LOCATION']]['finishing'] += $finish_cost;
			}
		}
		
		$locationArray[$val['LOCATION']] = $val['LOCATION'];
		
		// $year_location_qty_array[$fiscalYear][$val[csf('location')]]['finishing'] += $finish_cost;			
	}
	// print_r($locationArray);die();
	 
		//	echo $tot_print_amount.'=A='.$tot_print_qty;
	// ======================== calcutate kniting amount ====================
	//SELECT a.febric_description_id as DETER_ID,b.po_breakdown_id as POID,b.is_sales as IS_SALES,c.receive_date as RECEIVE_DATE,c.knitting_location_id as KNIT_LOCTION_ID
	$dyeing_kniting_qty_array = array();
	$knit_location_arr=array();
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;
			$is_sales_id=$val['IS_SALES'];
			// $main_array[$fyear]['qty']+=$val[csf($myear)];
			$kniting_cost=0;
			$kniting_qty=0;					
			if($val['POID']>0)
			{
			
				if($is_sales_id!=1)
				{
				$knit_rate=$po_color_knit_array[$val['POID']][$val['DETER_ID']][1]['knit_rate'];
				}
				else
				{
				$knit_rate=$sales_data_knit_array[$val['POID']][$val['DETER_ID']][1]['process_rate'];
				}

				//echo $avg_kniting_rate.'DD';
			}	
			if($knit_rate>0)
			{
			$knitingCost =$knit_rate*$val[$myear];	
			$main_array[$fyear][$val['KNIT_LOCTION_ID']]['kniting'] += $knitingCost;//
			}
		}
		$knit_location_arr[$val['KNIT_LOCTION_ID']]=$val['KNIT_LOCTION_ID'];
		
	}
	// print_r($main_array);die();
	// ======================== calcutate dyeing amount ====================
	
	
	 $process_array=array(1,30,35);
	//$dying_prod_sql_res = sql_select($dying_prod_sql);
	//a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,f.detarmination_id as DETER_ID
	foreach ($dying_prod_sql_res as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;
			$dyeing_cost=0;
			$dyeing_qty=0;
			$entry_formId=$val['ENTRY_FORM'];
			$po_id=$val['POID'];
			$is_sales_id=$val['IS_SALES'];
			$sensitive_id=$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'];
			$prodQty=$val[$myear];
			foreach ($conversion_cost_head_array as $key => $value) 
			{
				//echo $conversion_costing_arr[$val[csf('po_breakdown_id')]][$key][12].', ';
				if($po_id>0)
				{
					if(!in_array($key, $process_array ))
					{
						if($is_sales_id!=1)
						{ 
							if($key==31) //Fabric Dying
							{
								//echo $sensitive_id.',';
								
								$conv_rate=$po_color_fab_array[$po_id][$val['COLOR_ID']][$val['DETER_ID']][$key]['rate'];
								$conv_amount=$conv_rate*$prodQty;
							}
							else if($key==33) //Heatset
							{
								$conv_rate=$po_color_fab_array2[$po_id][$val['DETER_ID']][$key]['rate'];
								$conv_amount=$conv_rate*$prodQty;
							}
							else
							{
								$conv_rate=$po_color_fab_array[$po_id][$val['DETER_ID']][$key]['rate'];
								//echo $conv_rate.'b,';
								$conv_amount=$conv_rate*$prodQty;
							}
						}
						else //Sales
						{
							$conv_rate=$sales_data_array[$po_id][$val['DETER_ID']][$val['COLOR_ID']][$key]['process_rate'];
							$conv_amount=$conv_rate*$prodQty;
						}
						
						if($conv_amount>0)
						{
							//echo $val[csf($myear)].'='.$conv_rate.'b,';
						$dying_prod_qty_array[$fyear][$val['FLOOR_ID']]['dyeing_prod']+=$conv_amount;//$conv_rate*$val[csf($myear)];
						//$main_array[$fyear]['dyeing'] += $conv_rate*$val[csf($myear)];	
						}
					}
				}
			
			}
			
		}
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
		
	}
	
	foreach ($sub_dying_prod_sql_res as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;
			$entry_formId=$val['ENTRY_FORM'];
			if($val[$myear]>0)
				{
					$dying_prod_qty_array[$fyear][$val['FLOOR_ID']]['sub_dyeing_prod']+=($val[$myear]*$order_wise_rate[$val['POID']])/$exch_rate;
					//$main_array[$fyear]['dyeing'] += $dyeing_cost;
				}
		}
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
		
	}
	ksort($floorArray);
	//print_r($dying_prod_qty_array);
	//echo $conversion_costing_arr[33788][188][12].'A';
	foreach ($sql_sub_knit_res as $val) 
	{	
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;	
			$fmyear='FM'.$nyear;							
			$subKnit_cost =$order_wise_rate[$val['ORDER_ID']]*$val[$myear];	
			$subFinish_cost =$order_wise_rate[$val['ORDER_ID']]*$val[$fmyear];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subKnit_cost>0)
			{
			$subKnit_costUSD = $subKnit_cost/$rate;
			$main_array[$fyear][$val['LOCATION_ID']]['subKnit'] += $subKnit_costUSD; //
			}
			if($subFinish_cost>0)
			{
				$subFin_costUSD = $subFinish_cost/$rate;
				$main_array[$fyear][$val['LOCATION_ID']]['subFinish'] += $subFin_costUSD;
			}
			//$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
			
			
		}
		
	}
	//print_r($main_array);
	foreach ($sql_sub_dye_res as $val) 
	{			
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;				
			$subDye_cost =$order_wise_rate[$val['ORDER_ID']]*$val[$myear];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$subDye_costUSD = $subDye_cost/$rate;	
			$fiscalDyeingYear=$val["YEAR"].'-'.($val["YEAR"]+1);
			if($subDye_cost>0)
			{
			$main_array[$fyear]['subDye'] += $subDye_costUSD;	
			}
		}	
	}
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;				
			$subsewOut_cost =$order_wise_rate[$val['ORDER_ID']]*$val[$myear];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val["YEAR"].'-'.($val["YEAR"]+1);//
			$main_array[$fyear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_location_sewOut_array[$fyear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;
			}
		}	
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);
	
	$knit_width=count($knit_location_arr)*80;
	$floor_width=count($floorArray)*80;
	$tbl_width = 460+(count($locationArray)*80)+$floor_width+$knit_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Yearly Revenue Report <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Fiscal Year</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="80" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> Gmt</th>
	            	<?
	            }
	            ?>
	            <th width="80">Total gmt</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?> </th>
	            	<?
	            }//knit_location_arr
	            ?>
                <th width="80">Total Dyeing</th>
                
	             <?
	            foreach ($knit_location_arr as $knit_loc_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="LocationId=<? echo $knit_loc_id;?>"><?  echo ucfirst($location_library[$knit_loc_id]);?> Knit</th>
	            	<?
	            }//knit_location_arr
	            ?>
                <th width="80">Total Knitting</th>
	            <th width="80">Printing</th>
				<th width="80">Embroidery</th>
              
                <th width="80">Total </th>
	           
	        </thead>
		        <tbody>   
		        <?
				
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;
					$total_textile=$total_knit_asia=0;
					$total_finish=0;$total_print_cost=0;$total_embro_cost=0;$total_wash_cost=0;
					$i=1;
		        	foreach ($fiscal_year_arr as $year => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$fiscal_total = 0;	
							        		
			        	?>     
				       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
				            <td><a href="javascript:void()" onclick="report_generate_by_year('<? echo $year?>','2')"><? echo $year;?></a></td>
				            <?
							$i++;
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	//  echo $year_location_qty_array[$year][$loc_id]['finishing'].', ';
								 
								  ?>
				            	<td align="right" title="Sewing Out*SMV*CPM/Exchange Rate+SubCon SewOut*SubCon Order Rate(<? echo $subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];?>)"><?  echo number_format($year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'],0);?></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            }
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
                            <?
							$dying_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$dying_prod_qty=$dying_prod_qty_array[$year][$floor_id]['dyeing_prod']+$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
								 
								  ?>
				            	<td align="right" title="Without Knitting+YarnDying+AOP Rate from PreCost*Dying Prod Qty+SubConDyeing Prod*SubCOn Order Rate(<? echo $dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'].',ExchangeRate='.$exch_rate;?>)"><?  echo number_format($dying_prod_qty,0);?></td>
				            	<?
								$dying_floor_total[$floor_id]+=$dying_prod_qty;
								$dying_floor_tot+=$dying_prod_qty;
				            }
							
							//$tot_textile=$dying_floor_tot+$main_array[$year]['subFinish']+$main_array[$year]['kniting']+$main_array[$year]['subKnit']+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv']+$year_prod_cost_arr[$year]['wash_recv']
							?>
                           
                            <td align="right" title="All Dying Floor"><? echo number_format($dying_floor_tot,0); ?></td>
				           
                            <?
							$tot_knitting=0;
                             foreach ($knit_location_arr as $knit_loc_id => $loc_name) 
				            {
							?>
                              <td align="right" title="All Knitting"><? echo number_format($main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'],0); ; ?></td>
                              <?
							  $tot_knitting+=$main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
							  $tot_knitting_arr[$knit_loc_id]+=$main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
							}
							  ?>
                               <td align="right" title="All Knitting"><? echo number_format($tot_knitting,0); ?></td>
				            <td align="right" title="Print Recv*PreCost Print Avg Rate"><? echo number_format($year_prod_cost_arr[$year]['print_recv'],0); ?></td>

				            <td align="right"><? echo number_format($year_prod_cost_arr[$year]['embo_recv'],0); ?></td>
				            <td align="right" title="Total gmt+Total Dying +Total Knitting+Print Rcv"><? echo number_format($total_gmts+$dying_floor_tot+$tot_knitting+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv'],0); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $dying_floor_tot+$main_array[$year]['subFinish'];//$main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
						$total_finish+=$main_array[$year]['subFinish'];
						$total_print_cost+=$year_prod_cost_arr[$year]['print_recv'];
						$total_embro_cost+=$year_prod_cost_arr[$year]['embo_recv'];
						$total_wash_cost+=$year_prod_cost_arr[$year]['wash_recv'];
						
						$total_textile+=$tot_textile;
						$total_knit_asia+=$total_gmts+$tot_textile;
								
						
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,0); ?></th>
	             <?
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($dying_floor_total[$floor_id],0);?></th>
	            	<?
	            }
	            ?>
	            
	            <th><? echo number_format($gr_dyeing_total,0); ?></th>
                 <?
				 $total_knitting=0;
				 foreach ($knit_location_arr as $knit_loc_id => $loc_name) 
				{
					$total_knitting+=$tot_knitting_arr[$knit_loc_id]
				?>
	            <th><? echo number_format($tot_knitting_arr[$knit_loc_id],0); ?></th>
                <?
				}
				?>
                <th><? echo number_format($total_knitting,0); ?></th>
	            <th><? echo number_format($total_print_cost,0); ?></th>
				<th><? echo number_format($total_embro_cost,0); ?></th>
	            <th title="Total Gmts+Dying+Knitting"><? echo number_format($gr_fiscal_total+$gr_dyeing_total+$total_knitting+$total_print_cost+$total_embro_cost,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}  
if($action=="report_generate_by_year_sheet_jm") //Top Sheet JM Monthly
{ 
 
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//Top Sheet JM
	}
	$sql_floor=sql_select("select ID,floor_name as FLOOR_NAME from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row['ID']]['floor']=$row['FLOOR_NAME'];
		$floor_library[$row['ID']]['floor_id']=$row['ID'];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	
		$sql_std_para=sql_select("select cost_per_minute as COST_PER_MINUTE, applying_period_date as APPLYING_PERIOD_DATE, applying_period_to_date as APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row['APPLYING_PERIOD_DATE'],'','',1);
			$applying_period_to_date=change_date_format($row['APPLYING_PERIOD_TO_DATE'],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row['COST_PER_MINUTE'];
			}
		}
		
	
	//$sql_fin_prod="SELECT a.location,a.po_break_down_id";
	  // $sql_fin_prod="SELECT a.location as LOCATION,a.embel_name as EMBEL_NAME,a.po_break_down_id as POID,a.production_date as PRODUCTION_DATE,a.item_number_id as ITEM_ID,b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID";
	    $sql_fin_prod=" SELECT a.location as LOCATION,a.po_break_down_id  as POID,a.item_number_id as ITEM_ID,to_char(a.production_date,'MON-YYYY') as MONTH_YEAR,a.embel_name as EMBEL_NAME,
	   b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS MSEW_OUT,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS MPRINT_RECV ,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS MEMBRO_RECV ,
	 (CASE WHEN a.production_date between '$startDate' and '$endDate' and a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS MWASH_RECV 
	 
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5,3) and a.production_source in(1)  and a.location <> 0  order by a.location";
	 // echo  $sql_fin_prod;die;  //and a.po_break_down_id=16464 

	
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val['POID']] = $val['POID'];
		$sewing_qty_array[$val['POID']] = $val['MSEW_OUT'];
	}

	// ========================= for kniting ======================
	
	  $sql_kniting_dyeing=" SELECT a.febric_description_id as DETER_ID,c.knitting_location_id as KNIT_LOCTION_ID,b.po_breakdown_id as POID,b.is_sales as IS_SALES,to_char(c.receive_date,'MON-YYYY') as MONTH_YEAR,(a.grey_receive_qnty) as GREY_RECEIVE_QNTY from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and c.knitting_location_id>0 order by c.knitting_location_id";

	/*$sql_kniting_dyeing = "SELECT b.po_breakdown_id,b.entry_form,to_char(c.receive_date,'YYYY') as year,sum(a.grey_receive_qnty) as qty from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company=$cbo_company_id and c.receive_date between '$startDate' and '$endDate' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) group by b.po_breakdown_id,b.entry_form,c.receive_date order by b.entry_form";*/
	// echo $sql_kniting_dyeing;die();.///	$knit_location_arr[$val[csf('knitting_location_id')]]=$val[csf('knitting_location_id')];
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$is_sales_id=$val[('IS_SALES')];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];

		}
	}
	// ========================= for dyeing ======================
	
	//$dying_prod_sql="SELECT a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,f.detarmination_id as DETER_ID";
	$dying_prod_sql=" SELECT a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'MON-YYYY') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY,f.detarmination_id as DETER_ID from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d,product_details_master f where c.batch_id=a.id and a.id=d.mst_id  and f.id=d.prod_id and c.batch_id=d.mst_id and c.load_unload_id=2 and a.batch_against=1 and c.entry_form=35 and c.service_company in($cbo_company_id)  and c.process_end_date between '$startDate' and '$endDate' and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id,c.process_end_date ";
	$dying_prod_sql_res = sql_select($dying_prod_sql);
	foreach ($dying_prod_sql_res as $val) 
	{
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];
		}
		$batch_array[$val['FLOOR_ID']][$val['MONTH_YEAR']]+= $val['BATCH_QNTY'];
	}
	//a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE
	$sub_dying_prod_sql=" SELECT a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'MON-YYYY') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d where c.batch_id=a.id and a.id=d.mst_id  and c.batch_id=d.mst_id and c.load_unload_id=2 and a.batch_against=1  and c.entry_form=38  and c.service_company in($cbo_company_id)  and c.process_end_date between '$startDate' and '$endDate' and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id,c.process_end_date ";
	$sub_dying_prod_sql_res = sql_select($sub_dying_prod_sql);
	foreach ($sub_dying_prod_sql_res as $val) 
	{
		//$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		$batch_array[$val['FLOOR_ID']][$val['MONTH_YEAR']]+= $val['BATCH_QNTY'];
		$sub_po_id_array[$val['POID']] = $val['POID'];
	}

	
	/*$poIds = implode(",", array_unique($po_id_array));
	$salesIds = implode(",", array_unique($sales_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	if($salesIds !="")
	{
		$sales_cond="";
		if(count($sales_id_array)>999)
		{
			$chunk_arr=array_chunk($sales_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sales_cond=="") $sales_cond.=" and ( a.id in ($ids) ";
				else
					$sales_cond.=" or   a.id in ($ids) "; 
			}
			$sales_cond.=") ";

		}
		else
		{
			$sales_cond.=" and a.id in ($salesIds) ";
		}
	}*/
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
			
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 2, $sales_id_array, $empty_arr);//Sales ID Ref from=2
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	
	// $sql_sales = "select a.id,a.job_no, a.within_group,b.color_id,b.determination_id as deter_id,b.process_id,b.process_seq,b.body_part_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  $sales_cond order by a.id desc";
	 $sql_sales = "select b.id as DTLS_ID,a.id as ID,a.job_no as JOB_NO, a.within_group as WITHING_GROUP,b.color_id as COLOR_ID,b.determination_id as DETER_ID,b.process_id as PROCESS_ID,b.process_seq as PROCESS_SEQ,b.body_part_id as BODY_PART_ID from fabric_sales_order_mst a,fabric_sales_order_dtls b,gbl_temp_engine g  where a.id=b.mst_id and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by b.id desc";
		$sql_sales_result = sql_select($sql_sales);// and a.company_id in($cbo_company_id)
		foreach ($sql_sales_result as $val) 
		{		
				$process_id=array_unique(explode(",",$val['PROCESS_ID']));
				$process_seqArr=array_unique(explode(",",$val['PROCESS_SEQ']));
				foreach($process_id as $p_key)
				{
						if($val['PROCESS_SEQ']!="")
						{
							foreach($process_seqArr as $val_rate)
							{
								$process_Rate=explode("__",$val_rate);
								$process_Id=$process_Rate[0];
								$process_rate=$process_Rate[1];
								if($p_key==$process_Id)
								{
								$sales_data_array[$val['ID']][$val['DETER_ID']][$val['COLOR_ID']][$p_key]['process_rate'] = $process_rate;
								$sales_data_knit_array[$val['ID']][$val['DETER_ID']][$p_key]['process_rate'] = $process_rate;
								}
							}
						}
				}
		}
		
	//$cm_po_cond = str_replace("id", "b.id", $po_cond);
	//$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	/*$sql_po_sew="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	//$sql_po_sew="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_sew_result = sql_select($sql_po_sew);
	foreach ($sql_po_sew_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}*/
	 $sql_sew_po="SELECT b.id as POID,b.po_quantity as POQTY,b.pub_shipment_date as PUBSHIPDATE,b.shipment_date as SHIPDATE,b.job_no_mst as JOB_NO from wo_po_break_down b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and  b.status_active=1 and b.is_deleted=0 "; 
	$sql_po_sew_result = sql_select($sql_sew_po);
	foreach ($sql_po_sew_result as $val) 
	{
		/*$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';*/
		$po_qty_array[$val['POID']] = $val['POQTY'];
		$po_job_array[$val['POID']]= $val['JOB_NO'];
		$po_date_array[$val['JOB_NO']]['ship_date'].= $val['SHIPDATE'].',';
		$po_date_array[$val['JOB_NO']]['pub_date'].= $val['PUBSHIPDATE'].',';
	}
	unset($sql_po_sew_result );
	
		 
	// $sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.id as conv_id,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond  order by f.id,b.id asc";
	 $sql_po="SELECT b.id as POID,c.color_number_id as COLOR_ID,d.color_size_sensitive as COLOR_SIZE_SENSITIVE,d.lib_yarn_count_deter_id as DETER_ID,f.cons_process as CONS_PROCESS,f.id as CONV_ID,f.charge_unit as CHARGE_UNIT,f.color_break_down as COLOR_BREAK_DOWN from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f,gbl_temp_engine g where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and b.id=g.ref_val and g.ref_val=c.po_break_down_id and g.ref_val=e.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  order by f.id,b.id asc"; 
	 
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val['COLOR_BREAK_DOWN'];
		if($val['COLOR_SIZE_SENSITIVE']==3)
		{
		$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'] = $val['COLOR_SIZE_SENSITIVE'];
		}
		
		if($val['CONS_PROCESS']==31 && $color_break_down!='')
		{
			if($val['COLOR_SIZE_SENSITIVE']==3) //Contrst
			{
			$po_color_brk_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
			}
			else
			{
			$po_color_brk_fab_array[$val['POID']][$val['COLOR_ID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];

			}
			
			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val['POID']][$arr_2[3]][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$arr_2[1];
			
			}
		}
		else if($val['CONS_PROCESS']==33) //Heatset
		{
			$po_color_fab_array2[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$val['CHARGE_UNIT'];
		}
		else
		{
			$po_color_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] = $val['CHARGE_UNIT'];
		}
		$po_color_knit_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['knit_rate'] = $val['CHARGE_UNIT'];

	}
	
	
//	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
 /*$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	}*/
	$cm_sql = "SELECT c.costing_date as COSTING_DATE,c.costing_per as COSTING_PER,c.sew_smv as SEW_SMV,a.cm_cost as CM_COST,b.id as POID,d.smv_set as SMV_SET,d.gmts_item_id as GMTS_ID from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d,gbl_temp_engine g where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and d.job_id=b.job_id and d.job_id=c.job_id  and g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and a.status_active=1 and b.status_active=1 and c.status_active=1  ";
	$cm_sql_res = sql_select($cm_sql);
	
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val['POID']] = $val['CM_COST'];
		$pre_cost_smv_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_cost_array[$val['POID']]['costing_date'] = $val['COSTING_DATE'];
		$costing_per_arr[$val['POID']]= $val['COSTING_PER'];
	}
	
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");

	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();*/
	// var_dump($condition);
	//$conversion= new conversion($condition);
	//$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	//$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	/*$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
//echo "<pre>";print_r($emblishment_costing_arr_wash);die();
 $sql_pre_wash="SELECT  b.emb_name as EMB_NAME,d.id as AVG_ID,d.color_number_id as COLOR_ID,d.po_break_down_id as POID,c.id as COLOR_SIZE_ID,d.REQUIRMENT as REQUIRMENT,d.rate as RATE from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b,gbl_temp_engine g where d.job_id=a.id   and c.job_no_mst=a.job_no and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id  and b.id=d.pre_cost_emb_cost_dtls_id and  b.job_id=a.id  and  b.job_id=c.job_id and g.ref_val=c.po_break_down_id  and g.ref_val=d.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.requirment>0 and d.rate>0   order by d.id asc";

	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_rate']+= $val['RATE'];
		$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_req']+= $val['REQUIRMENT'];
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
	}
	unset($sql_wash_result);
	
	// =========================== getting subcon order qty ====================================
	// ======================================= getting subcontact data =================================and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//==================================== subcon order data =============================
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	$sql_subcon_po="SELECT b.id as ID,b.rate as RATE from subcon_ord_dtls b,gbl_temp_engine g where  g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0";
	$sql_subcon_po_res = sql_select($sql_subcon_po);
	foreach($sql_subcon_po_res as $row)
	{
		$order_wise_rate[$row['ID']]=$row['RATE'];
	}
	unset($sql_subcon_po_res);
	
	// print_r($order_wise_rate);
	// =================================== SubCon Sewout =============================
	//$sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id as LOCATION_ID,a.production_date as PRODUCTION_DATE";
	   $sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id  as LOCATION_ID,to_char(a.production_date,'MON-YYYY') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id>0 order by a.location_id ";
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== subcon kniting =============================
	// b.order_id as ORDER_ID,a.product_date as PRODUCT_DATE,a.location_id as LOCATION_ID
	  $sql_sub_knit=" SELECT a.location_id as LOCATION_ID, b.order_id as ORDER_ID,to_char(a.product_date,'MON-YYYY') as MONTH_YEAR,
	  (CASE WHEN a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS SUB_KNITTING_PROD,
	  (CASE WHEN a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id)  THEN b.product_qnty END) AS FIN_PROD
	  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4) and a.location_id>0 order by a.location_id";
	//entry_form=292, product_type=4
	
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// =================================== subcon dyeing =============================
	$main_array = array();
	$locationArray = array();
	$year_prod_cost_arr=array();
	//$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1); $tot_print_amount=$tot_print_qty=0;
	foreach ($sql_fin_prod_res as $val) 
	{	
		
			$myear=$val["MONTH_YEAR"];
			
			$main_array[$myear]['qty']+=$val['MSEW_OUT'];//msew_out
			$main_array[$myear]['location'] = $val['LOCATION'];
			/*$print_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][1];
			$print_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][1];
			$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];*/
			//echo $print_cost.'D';
			$dzn_qnty=0;
			$costing_per_id=$costing_per_arr[$val['POID']];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$color_id=$po_color_array[$val['COLOR_SIZE_BREAK_DOWN_ID']]['color_id'];
			if($color_id=='') $color_id=0;
			//$po_color_rate=$po_color_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['rate'];
			$avg_rate=$po_color_avg_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['avg_rate'];
			$avg_req=$po_color_avg_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['avg_req'];
			$po_color_rate=$avg_rate/$avg_req;
			$print_recv=$val['MPRINT_RECV'];
			$print_avg_rate=$po_color_rate/$dzn_qnty;
			if($print_recv>0 && $print_avg_rate>0)
			{
			
			
			//$print_avg_rate=($print_cost/$print_qty)/$dzn_qnty;
				//echo $myear.'='.$val['MPRINT_RECV'].'='.$print_avg_rate.'='.$po_color_rate.'='.$dzn_qnty.'<br>';
				//if($color_id=='') echo  $val['POID'].'='.$po_color_rate.'<br>';
				
			//	echo $print_avg_rate.'='.$costing_per_id.'<br>';
				
			$print_amount=$print_recv*$print_avg_rate;
			$year_prod_cost_arr[$myear]['print_recv'] += $print_amount;
			$tot_print_amount+=$print_recv*$print_avg_rate;
			$tot_print_qty+=$print_recv;
			}
			$embro_avg_rate=$po_color_rate/$dzn_qnty;
			if($val['MEMBRO_RECV']>0 && $embro_avg_rate>0)
			{
			//$embro_avg_rate=($embro_cost/$embro_qty)/$dzn_qnty;
			
			$embro_amount=$val['MEMBRO_RECV']*$embro_avg_rate;
			$year_prod_cost_arr[$myear]['embo_recv'] += $embro_amount;
			}
			$wash_avg_rate=$po_color_rate/$dzn_qnty;
			if($val['MWASH_RECV']>0 && $wash_avg_rate>0)
			{
			
			//$wash_avg_rate=($wash_cost/$wash_qty)/$dzn_qnty;;
			$wash_amount=$val['MWASH_RECV']*$wash_avg_rate;
			$year_prod_cost_arr[$myear]['wash_recv'] += $wash_amount;
			}
			
			
			
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val['POID']][$val['ITEM_ID']]['sew_smv'];
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val['POID']]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				// $po_qty=$po_qty_array[$val[csf('po_break_down_id')]];
				//$cm_avg_cost=$cm_cost/12;
				//$finish_cost=$cm_avg_cost*$val[csf($myear)];
				if($val[$myear]=="") $val[$myear]=0;else $val[$myear]=$val[$myear];
				//echo $sew_smv.'='.$val[csf('msew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
				$finish_cost=($sew_smv*$val['MSEW_OUT']*$cost_per_minute)/$exch_rate;
				$year_location_qty_array[$myear][$val['LOCATION']]['finishing'] += $finish_cost;
			}
		
		
		$locationArray[$val['LOCATION']] = $val['LOCATION'];
		
		// $year_location_qty_array[$fiscalYear][$val[csf('location')]]['finishing'] += $finish_cost;			
	}
	// print_r($year_prod_cost_arr);die();
	//echo $tot_print_amount.'='.$tot_print_qty;
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		
			$myear=$val["MONTH_YEAR"];	
			$is_sales_id=$val['IS_SALES'];		// $main_array[$fyear]['qty']+=$val[csf($myear)];
			$kniting_cost=0;
			$kniting_qty=0;		//grey_receive_qnty			
			if($val['POID']>0)
			{
				/*$kniting_cost = array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
				$kniting_qty = array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);	
				if($kniting_cost>0)
				{
				$avg_kniting_rate = $kniting_cost/$kniting_qty;
				}*/
				//echo $avg_kniting_rate.'DD';
				if($is_sales_id!=1)
				{
				$knit_rate=$po_color_knit_array[$val['POID']][$val['DETER_ID']][1]['knit_rate'];
				}
				else
				{
				$knit_rate=$sales_data_knit_array[$val['POID']][$val['DETER_ID']][1]['process_rate'];
				}
			}	
			if($knit_rate>0)
			{
			$knitingCost =$knit_rate*$val['GREY_RECEIVE_QNTY'];	
			$main_array[$myear][$val['KNIT_LOCTION_ID']]['kniting'] += $knitingCost;
			}
			// $fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);
			// $dyeing_kniting_qty_array[$fyear][2] += $knitingCost;
		$knit_location_arr[$val['KNIT_LOCTION_ID']]=$val['KNIT_LOCTION_ID'];
	}
	// print_r($main_array);die();
	// ======================== calcutate dyeing amount ====================
	 $process_array=array(1,30,35);
	//$dying_prod_sql_res = sql_select($dying_prod_sql);
	$dying_prod_qty_array=array();
	foreach ($dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$entry_formId=$val['ENTRY_FORM'];
			$po_id=$val['POID'];
			$is_sales_id=$val['IS_SALES'];
			$sensitive_id=$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'];
			
			//$po_color_fab_array[$val[csf('po_breakdown_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'];
			foreach ($conversion_cost_head_array as $key => $value) 
			{
				if($val['POID']>0)
				{
					if(!in_array($key, $process_array ))
					{
						if($is_sales_id!=1)
						{
							if($key==31) //Fabric Dyeing....
							{
								
								$conv_rate=$po_color_fab_array[$po_id][$val['COLOR_ID']][$val['DETER_ID']][$key]['rate'];
								 $dyeing_cost=$conv_rate*$val['BATCH_QNTY'];	
							}
							else if($key==33) //Heatset
							{ 
								$conv_rate=$po_color_fab_array2[$po_id][$val['DETER_ID']][$key]['rate'];
								 $dyeing_cost=$conv_rate*$val['BATCH_QNTY'];
							}
							else
							{
								$conv_rate=$po_color_fab_array[$po_id][$val['DETER_ID']][$key]['rate'];
								//echo $conv_rate.'b,';
								$dyeing_cost =$conv_rate*$val['BATCH_QNTY'];	
							}
						}
						else
						{
							//$conv_rate=$po_color_fab_array[$po_id][$val[csf('color_id')]][$val[csf('deter_id')]][$key]['rate'];
							$conv_rate=$sales_data_array[$po_id][$val['DETER_ID']][$val['COLOR_ID']][$key]['process_rate'];
							$dyeing_cost =$conv_rate*$val['BATCH_QNTY'];	
						}
						
						if($dyeing_cost>0)
						{
						$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['dyeing_prod']+=$dyeing_cost;
						//$main_array[$fyear]['dyeing'] += $dyeing_cost;
						}
					}
				}
			}
		
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
		
	}
	foreach ($sub_dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$entry_formId=$val['ENTRY_FORM'];
		   if($val['BATCH_QNTY']>0)
				{
				$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['sub_dyeing_prod']+=($val['BATCH_QNTY']*$order_wise_rate[$val['POID']])/$exch_rate;
					//$main_array[$fyear]['dyeing'] += $dyeing_cost;
				}
		
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
		
	}
	ksort($floorArray);
	//print_r($dying_prod_qty_array);
	//echo $conversion_costing_arr[33788][188][12].'A';
	// ========================== subcontact ===============================
	foreach ($sql_sub_knit_res as $val) 
	{	
			$myear=$val["MONTH_YEAR"];								
			$subKnit_cost =$order_wise_rate[$val['ORDER_ID']]*$val["SUB_KNITTING_PROD"];	
			$subFinish_cost =$order_wise_rate[$val['ORDER_ID']]*$val["FIN_PROD"];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate.'='.$val[csf("fin_prod")].'<br>';
			if($subKnit_cost>0)
			{
				$subKnit_costUSD = $subKnit_cost/$rate;
				$main_array[$myear][$val["LOCATION_ID"]]['subKnit'] += $subKnit_costUSD;
			}
			if($subFinish_cost>0)
			{
				$subFin_costUSD = $subFinish_cost/$rate;
				$main_array[$myear][$val["LOCATION_ID"]]['subFinish'] += $subFin_costUSD;
			}
			//$knit_location_arr[$val[csf('location_id')]]=$val[csf('location_id')];
	}
	ksort($knit_location_arr);
	foreach ($sql_sub_dye_res as $val) 
	{			
			$myear=$val["MONTH_YEAR"];				
			$subDye_cost =$order_wise_rate[$val['ORDER_ID']]*$val["knitting_prod"];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subDye_cost>0)
			{
			$subDye_costUSD = $subDye_cost/$rate;	
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);
			$main_array[$myear]['subDye'] += $subDye_costUSD;	
			}
	}
	//  $sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id  as LOCATION_ID,to_char(a.production_date,'MON-YYYY') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 and a.location_id>0 order by a.location_id ";
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	foreach ($sql_sub_sewOut_result as $val) 
	{	
			$myear=$val["MONTH_YEAR"];	
			$subsewOut_cost =$order_wise_rate[$val['ORDER_ID']]*$val['PRODUCTION_QNTY'];	
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			$fiscalDyeingYear=$val["YEAR"].'-'.($val["YEAR"]+1);//
			$main_array[$myear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_location_sewOut_array[$myear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;
			}
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);
	
	$location_width=count($knit_location_arr)*80;
	$floor_width=count($floorArray)*80;
	$tbl_width = 460+(count($locationArray)*80)+$floor_width+$location_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	   <!-- <table width="<? //echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
          <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Monthly Revenue Report <? echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Month</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="80" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> Gmt</th>
	            	<?
	            }
	            ?>
	            <th width="80">Total gmt</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?> </th>
	            	<?
	            }
	            ?>
               
                <th width="80">Total Dyeing</th>
                 <?
	            foreach ($knit_location_arr as $knit_loc_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	           	<th width="80" title="<? echo $knit_loc_id;?>"><?  echo ucfirst($location_library[$knit_loc_id]);?> Knit</th>
                <?
				}
				?>
                <th width="80">Total Knitting </th>
	            <th width="80">Printing</th>
				<th width="80">Embroidery</th>
	            <th width="80">Total</th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;$subConKnit=0;
					$total_textile=$total_knit_asia=0;
					$total_finish=0;$total_print_cost=0;$total_embro_cost=0;$total_wash_cost=0;
		        //	foreach ($fiscal_year_arr as $year => $val) 
				$k=1;
					foreach ($fiscalMonth_arr as $year => $val) 
		        	{
		        		if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$year_ex = explode("-", $year);
		        		$fiscal_total = 0;		        		
			        	?>     
				         <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trm_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trm_<? echo $i; ?>" style="font-size:11px">
				           <td><a href="javascript:void()" onclick="report_generate_by_month('<? echo $year?>',2)"><? echo date('F-y',strtotime($year));?></a></td>
				            <?
							$k++;
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	//  echo $year_location_qty_array[$year][$loc_id]['finishing'].', ';
								 
								  ?>
				            	<td align="right" title="Sewing Out*SMV*CPM/Exchange Rate+SubCon SewOut*SubCon Order Rate(<? echo $subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];?>)"><?  echo number_format($year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'],0);?></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            }
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
                            <?
							$dying_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$dying_prod_qty=$dying_prod_qty_array[$year][$floor_id]['dyeing_prod']+$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
								 
								  ?>
				            	<td align="right" title="Without Knitting+YarnDying+AOP Rate from PreCost*Dying Prod Qty+SubConDyeing Prod*SubCOn Order Rate(<? echo $dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'].',ExchangeRate='.$exch_rate;?>)"><?  echo number_format($dying_prod_qty,0);?></td>
				            	<?
								$dying_floor_total[$floor_id]+=$dying_prod_qty;
								$dying_floor_tot+=$dying_prod_qty;
				            }
							
						//	$tot_textile=$dying_floor_tot+$main_array[$year]['subFinish']+$main_array[$year]['kniting']+$main_array[$year]['subKnit']+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv']+$year_prod_cost_arr[$year]['wash_recv']
							?>
                           
                            <td align="right" title="All Dying Floor"><? echo number_format($dying_floor_tot,0); ?></td>
                             <?
							 $tot_knitting=0;
							foreach ($knit_location_arr as $knit_loc_id => $val) 
							{ //$floor_library[$row[csf('id')]]['floor']
								?>
							  <td align="right" title="Knitting Prod*Pre Cost Knit Avg Rate+SubCon Knit*SubCon Order Rate(<? echo $main_array[$year][$knit_loc_id]['subKnit'];?>)"><? echo number_format($main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'],0); ?></td>
							<?
							$tot_knitting_arr[$knit_loc_id]+=$main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
							$tot_knitting+=$main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
							}
							?>
				            <td align="right"><? echo number_format($tot_knitting,0); ?></td>
				            <td align="right" title="Print Recv*PreCost Print Avg Rate"><? echo number_format($year_prod_cost_arr[$year]['print_recv'],0); ?></td>
							<td align="right"><? echo number_format($year_prod_cost_arr[$year]['embo_recv'],0); ?></td>
				            <td align="right" title="Total gmt+Total Dying+Total Knit+Print Rcv"><? echo number_format($total_gmts+$dying_floor_tot+$tot_knitting+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv'],0); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $dying_floor_tot+$main_array[$year]['subFinish'];//$main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
						$total_finish+=$main_array[$year]['subFinish'];
						$total_print_cost+=$year_prod_cost_arr[$year]['print_recv'];
						$total_embro_cost+=$year_prod_cost_arr[$year]['embo_recv'];
						$subConKnit+=$main_array[$year]['subKnit'];
						
						$total_textile+=$tot_textile;
						$total_knit_asia+=$total_gmts+$tot_textile;
								
						
				        $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($total_gmts_array[$loc_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,0); ?></th>
	             <?
				 $gr_dyeing_total=0;
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($dying_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_dyeing_total+=$dying_floor_total[$floor_id];
	            }
	            ?>
	             
	            <th><? echo number_format($gr_dyeing_total,0); ?></th>
                  <?
				  $total_knitting=0;
	            foreach ($knit_location_arr as $knit_loc_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($tot_knitting_arr[$knit_loc_id],0);?></th>
	            	<?
					$total_knitting+=$tot_knitting_arr[$knit_loc_id];
	            }
	            ?>
	            <th title="<? //echo $subConKnit;?>"><? echo number_format($total_knitting,0); ?></th>
	            <th><? echo number_format($total_print_cost,0); ?></th>
	          
				<th><? echo number_format($total_embro_cost,0); ?></th>
	            <th><? echo number_format($total_knitting+$gr_dyeing_total+$gr_fiscal_total+$total_print_cost+$total_embro_cost,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="report_generate_by_month_sheet_jm") //Top Sheet JM Daily
{ 
 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$month_year);
	// getting month from fiscal year
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1"; //Top Sheet JM
	}
	 
	$sql_floor=sql_select("select ID,floor_name as FLOOR_NAME from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row['ID']]['floor']=$row['FLOOR_NAME'];
		$floor_library[$row['ID']]['floor_id']=$row['ID'];
	}
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	//$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
	$sql_std_para=sql_select("select cost_per_minute as COST_PER_MINUTE, applying_period_date as APPLYING_PERIOD_DATE, applying_period_to_date as APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row['APPLYING_PERIOD_DATE'],'','',1);
			$applying_period_to_date=change_date_format($row['APPLYING_PERIOD_TO_DATE'],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row['COST_PER_MINUTE'];
			}
		}
	
	//$sql_fin_prod="SELECT a.location,a.po_break_down_id";
	
	 $sql_fin_prod="SELECT a.location as LOCATION,a.po_break_down_id as POID,a.item_number_id as ITEM_ID,a.embel_name as EMBEL_NAME,to_char(a.production_date,'DD-MON') as MONTH_YEAR,
	 b.COLOR_SIZE_BREAK_DOWN_ID as COLOR_SIZE_BREAK_DOWN_ID,
	 (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS MSEW_OUT,
	 (CASE WHEN  a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS MPRINT_RECV,
	 (CASE WHEN  a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS MEMBRO_RECV,
	 (CASE WHEN a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS MWASH_RECV
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5,3) and a.production_source in(1) and a.location is not null and a.location <> 0  order by a.location";
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val['POID']] = $val['POID'];
		$sewing_qty_array[$val['POID']] = $val['MSEW_OUT'];
	}

	// ========================= for kniting ======================
	
	$sql_kniting_dyeing=" SELECT a.febric_description_id as DETER_ID,c.knitting_location_id as KNIT_LOCTION_ID,b.po_breakdown_id as POID,b.is_sales as IS_SALES,to_char(c.receive_date,'DD-MON') as MONTH_YEAR,(a.grey_receive_qnty) as GREY_RECEIVE_QNTY from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and to_char(c.receive_date,'MON-YYYY')='$year' and b.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) and c.knitting_location_id>0 order by c.knitting_location_id";
//MON-YYYY
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$is_sales_id=$val[('IS_SALES')];
		if($is_sales_id==1)
		{
		// $sales_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			//$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			$po_id_array[$val['POID']] = $val['POID'];
		}
	}
	// ========================= for dyeing ======================
	 $dying_prod_sql=" SELECT a.color_id as COLOR_ID,a.is_sales as IS_SALES,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'DD-MON') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY,f.detarmination_id as DETER_ID from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d ,product_details_master f where c.batch_id=a.id and a.id=d.mst_id  and c.batch_id=d.mst_id and f.id=d.prod_id  and a.batch_against=1 and c.load_unload_id=2  and c.entry_form=35 and c.service_company in($cbo_company_id)   and to_char(c.process_end_date,'MON-YYYY')='$year'  and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id ,c.process_end_date";
	$dying_prod_sql_res = sql_select($dying_prod_sql);
	foreach ($dying_prod_sql_res as $val) 
	{
		 
		
		$is_sales_id=$val['IS_SALES'];
		if($is_sales_id==1)
		{
			$sales_id_array[$val['POID']] = $val['POID'];
		}
		else
		{
			$po_id_array[$val['POID']] = $val['POID'];
		}
		$batch_array[$val['FLOOR_ID']][$val['MONTH_YEAR']]+= $val['BATCH_QNTY'];
		
	}
	 $sub_dying_prod_sql=" SELECT a.color_id as COLOR_ID,c.floor_id as FLOOR_ID,d.po_id as POID,c.entry_form as ENTRY_FORM,c.process_end_date as PROCESS_END_DATE,to_char(c.process_end_date,'DD-MON') as MONTH_YEAR,(d.batch_qnty) as BATCH_QNTY from pro_fab_subprocess c, pro_batch_create_mst a,pro_batch_create_dtls d where c.batch_id=a.id and a.id=d.mst_id  and c.batch_id=d.mst_id and a.batch_against=1 and c.load_unload_id=2  and c.entry_form=38 and c.service_company in($cbo_company_id) and to_char(c.process_end_date,'MON-YYYY')='$year' and a.status_active=1 and d.status_active=1 and c.status_active=1 order by c.floor_id ,c.process_end_date";
	$sub_dying_prod_sql_res = sql_select($sub_dying_prod_sql);
	foreach ($sub_dying_prod_sql_res as $val) 
	{
		//$sub_po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		$sub_po_id_array[$val['POID']] = $val['POID'];
	}
	
	/*$poIds = implode(",", array_unique($po_id_array));
	$salesIds = implode(",", array_unique($sales_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	if($salesIds !="")
	{
		$sales_cond="";
		if(count($sales_id_array)>999)
		{
			$chunk_arr=array_chunk($sales_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sales_cond=="") $sales_cond.=" and ( a.id in ($ids) ";
				else
					$sales_cond.=" or   a.id in ($ids) "; 
			}
			$sales_cond.=") ";

		}
		else
		{
			$sales_cond.=" and a.id in ($salesIds) ";
		}
	}*/ //Sales Order
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
			
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 1, $po_id_array, $empty_arr);//PO ID Ref from=1
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 2, $sales_id_array, $empty_arr);//Sales ID Ref from=2
	fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 3, 3, $sub_po_id_array, $empty_arr);//Subcon PO ID Ref from=3
	
	  // $sql_sales = "select a.id,a.job_no, a.within_group,b.color_id,b.determination_id as deter_id,b.process_id,b.process_seq,b.body_part_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sales_cond order by a.id desc";
	   $sql_sales = "select b.id as DTLS_ID,a.id as ID,a.job_no as JOB_NO, a.within_group as WITHING_GROUP,b.color_id as COLOR_ID,b.determination_id as DETER_ID,b.process_id as PROCESS_ID,b.process_seq as PROCESS_SEQ,b.body_part_id as BODY_PART_ID from fabric_sales_order_mst a,fabric_sales_order_dtls b,gbl_temp_engine g  where a.id=b.mst_id and a.id=g.ref_val and b.mst_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in (2) and g.entry_form=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by b.id desc";
		$sql_sales_result = sql_select($sql_sales);// and a.company_id in($cbo_company_id) 
		foreach ($sql_sales_result as $val) 
		{		
				 $process_id=array_unique(explode(",",$val['PROCESS_ID']));
				$process_seqArr=array_unique(explode(",",$val['PROCESS_SEQ']));
				foreach($process_id as $p_key)
				{
						if($val['PROCESS_SEQ']!="")
						{
							foreach($process_seqArr as $val_rate)
							{
								$process_Rate=explode("__",$val_rate);
								$process_Id=$process_Rate[0];
								$process_rate=$process_Rate[1];
								if($p_key==$process_Id)
								{
								$sales_data_array[$val['ID']][$val['DETER_ID']][$val['COLOR_ID']][$p_key]['process_rate'] = $process_rate;
								$sales_data_knit_array[$val['ID']][$val['DETER_ID']][$p_key]['process_rate'] = $process_rate;
								}
							}
						}
				}
		}
		
		
	/*$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po_sew="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_sew_result = sql_select($sql_po_sew);
	foreach ($sql_po_sew_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}*/
	 $sql_sew_po="SELECT b.id as POID,b.po_quantity as POQTY,b.pub_shipment_date as PUBSHIPDATE,b.shipment_date as SHIPDATE,b.job_no_mst as JOB_NO from wo_po_break_down b,gbl_temp_engine g where  b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and  b.status_active=1 and b.is_deleted=0 "; 
	$sql_po_sew_result = sql_select($sql_sew_po);
	foreach ($sql_po_sew_result as $val) 
	{
		 
		$po_qty_array[$val['POID']] = $val['POQTY'];
		$po_job_array[$val['POID']]= $val['JOB_NO'];
		$po_date_array[$val['JOB_NO']]['ship_date'].= $val['SHIPDATE'].',';
		$po_date_array[$val['JOB_NO']]['pub_date'].= $val['PUBSHIPDATE'].',';
	}
	unset($sql_po_sew_result);
	
	
	
	 // $sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.id as conv_id,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond order by f.id,b.id asc";
	  $sql_po="SELECT b.id as POID,c.color_number_id as COLOR_ID,d.color_size_sensitive as COLOR_SIZE_SENSITIVE,d.lib_yarn_count_deter_id as DETER_ID,f.cons_process as CONS_PROCESS,f.id as CONV_ID,f.charge_unit as CHARGE_UNIT,f.color_break_down as COLOR_BREAK_DOWN from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f,gbl_temp_engine g where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and b.id=g.ref_val and g.ref_val=c.po_break_down_id and g.ref_val=e.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  order by f.id,b.id asc"; 
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		
		if($val['COLOR_SIZE_SENSITIVE']==3)
		{
		$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'] = $val['COLOR_SIZE_SENSITIVE'];
		}
		if($val['COLOR_BREAK_DOWN']!="")
		{
		$color_break_down=$val['COLOR_BREAK_DOWN'];
		}
		if($val['CONS_PROCESS']==31 && $color_break_down!='')
		{
			if($val['COLOR_SIZE_SENSITIVE']==3) //Contrst
			{
			$po_color_brk_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];
			}
			else
			{
			$po_color_brk_fab_array[$val['POID']][$val['COLOR_ID']][$val['DETER_ID']][$val['CONS_PROCESS']]['color_break_down'] = $val['COLOR_BREAK_DOWN'];

			}
			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val['POID']][$arr_2[3]][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$arr_2[1];
			//$po_color_fab_array2[$val[csf('id')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			}
		}
		else if($val['CONS_PROCESS']==33) //Heatset
		{
			$po_color_fab_array2[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] =$val['CHARGE_UNIT'];
		}
		else
		{
			$po_color_fab_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['rate'] = $val['CHARGE_UNIT'];
		}
		$po_color_knit_array[$val['POID']][$val['DETER_ID']][$val['CONS_PROCESS']]['knit_rate'] = $val['CHARGE_UNIT'];
	}
	//print_r($po_color_knit_array);
	
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	/* $cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	}*/
	
	$cm_sql = "SELECT c.costing_date as COSTING_DATE,c.costing_per as COSTING_PER,c.sew_smv as SEW_SMV,a.cm_cost as CM_COST,b.id as POID,d.smv_set as SMV_SET,d.gmts_item_id as GMTS_ID from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d,gbl_temp_engine g where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and d.job_id=b.job_id and d.job_id=c.job_id  and g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3 and a.status_active=1 and b.status_active=1 and c.status_active=1  ";
	$cm_sql_res = sql_select($cm_sql);
	
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val['POID']] = $val['CM_COST'];
		$pre_cost_smv_array[$val['POID']][$val['GMTS_ID']]['sew_smv'] = $val['SMV_SET'];
		$pre_cost_array[$val['POID']]['costing_date'] = $val['COSTING_DATE'];
		$costing_per_arr[$val['POID']]= $val['COSTING_PER'];
	}
	
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);
	//$conversion= new conversion($condition);
		//echo $conversion->getQuery();die;
	//$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	//$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
//echo "<pre>";print_r($emblishment_costing_arr_wash);die();
$sql_pre_wash="SELECT  b.emb_name as EMB_NAME,d.id as AVG_ID,d.color_number_id as COLOR_ID,d.po_break_down_id as POID,c.id as COLOR_SIZE_ID,d.REQUIRMENT as REQUIRMENT,d.rate as RATE from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b,gbl_temp_engine g where d.job_id=a.id   and c.job_no_mst=a.job_no and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id  and b.id=d.pre_cost_emb_cost_dtls_id and  b.job_id=a.id  and  b.job_id=c.job_id and g.ref_val=c.po_break_down_id  and g.ref_val=d.po_break_down_id  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=3  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.requirment>0 and d.rate>0   order by d.id asc";

	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val)  //embel_name
	{
		$po_color_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['rate'] = $val['RATE'];
		$po_color_array[$val['COLOR_SIZE_ID']]['color_id'] = $val['COLOR_ID'];
		
		$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_rate']+= $val['RATE'];
		$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_req']+= $val['REQUIRMENT'];
	}
	unset($sql_wash_result);
	
	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id) 
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2 order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//==================================== subcon order data =============================
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	$sql_subcon_po="SELECT b.id as ID,b.rate as RATE from subcon_ord_dtls b,gbl_temp_engine g where  g.ref_val=b.id  and g.user_id = ".$user_id." and g.ref_from=3 and g.entry_form=3 and b.status_active=1 and b.is_deleted=0";
	$sql_subcon_po_res = sql_select($sql_subcon_po);
	foreach($sql_subcon_po_res as $row)
	{
		$order_wise_rate[$row['ID']]=$row['RATE'];
	}
	unset($sql_subcon_po_res);
	// print_r($order_wise_rate);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut="SELECT a.order_id as ORDER_ID,a.location_id  as LOCATION_ID,to_char(a.production_date,'DD-MON') as MONTH_YEAR,a.production_qnty as PRODUCTION_QNTY from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.production_type=2 and a.location_id>0 order by a.location_id ";
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== subcon kniting =============================
	   $sql_sub_knit=" SELECT a.location_id as LOCATION_ID,b.order_id as ORDER_ID,to_char(a.product_date,'DD-MON') as MONTH_YEAR,
	  (CASE WHEN a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS SUB_KNITTING_PROD,
	  (CASE WHEN a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id)  THEN b.product_qnty END) AS FIN_PROD
	  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)   and to_char(a.product_date,'MON-YYYY')='$year'  and a.product_type in(2,4) and a.location_id>0 order by a.location_id ";
	//entry_form=292, product_type=4
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// =================================== subcon dyeing =============================
	$main_array = array();
	$locationArray = array();
	$year_prod_cost_arr=array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	//echo $cm_cost_method_based_on.'DD';
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
			$fyear=$val["MONTH_YEAR"];
			$main_array[$fyear]['qty']+=$val['MSEW_OUT'];//msew_out
			$main_array[$fyear]['location'] = $val['LOCATION'];
			/*$print_cost=$emblishment_costing_arr_name[$val['po_break_down_id']][1];
			$print_qty=$emblishment_qty_arr_name[$val['po_break_down_id']][1];
			
			$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			
			$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];*/
			$dzn_qnty=0;
			$costing_per_id=$costing_per_arr[$val['POID']];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$color_id=$po_color_array[$val['COLOR_SIZE_BREAK_DOWN_ID']]['color_id'];
			if($color_id=='') $color_id=0;//$po_color_avg_rate_array[$val['EMB_NAME']][$val['POID']][$val['COLOR_ID']]['avg_rate']
			//$po_color_rate=$po_color_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['rate'];
			$avg_color_rate=$po_color_avg_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['avg_rate'];
			$avg_color_req=$po_color_avg_rate_array[$val['EMBEL_NAME']][$val['POID']][$color_id]['avg_req'];
			$po_color_rate=$avg_color_rate/$avg_color_req;
			//echo $print_cost.'D';
			$print_avg_rate=$po_color_rate/$dzn_qnty;
			if($val['MPRINT_RECV']>0 && $print_avg_rate>0)
			{
			//$print_avg_rate=($print_cost/$print_qty)/$dzn_qnty;
			
			$print_amount=$val['MPRINT_RECV']*$print_avg_rate;
			$year_prod_cost_arr[$fyear]['print_recv'] += $print_amount;
			}
			$embro_avg_rate=$po_color_rate/$dzn_qnty;
			if($val['MEMBRO_RECV']>0 && $embro_avg_rate>0)
			{
			//$embro_avg_rate=($embro_cost/$embro_qty)/$dzn_qnty;
			
			$embro_amount=$val['MEMBRO_RECV']*$embro_avg_rate;
			$year_prod_cost_arr[$fyear]['embo_recv'] += $embro_amount;
			}
			$wash_avg_rate=$po_color_rate/$dzn_qnty;
			if($val['MWASH_RECV']>0 && $wash_avg_rate>0)
			{
			//$wash_avg_rate=($wash_cost/$wash_qty)/$dzn_qnty;
			
			$wash_amount=$val['MWASH_RECV']*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val['POID']][$val['ITEM_ID']]['sew_smv'];
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val['POID']]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val['POID']]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
				//echo $sew_smv.'='.$val[csf($myear)].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
				$finish_cost=($sew_smv*$val['MSEW_OUT']*$cost_per_minute)/$exch_rate;
				$year_location_qty_array[$fyear][$val['LOCATION']]['finishing'] += $finish_cost;
			}
		$locationArray[$val['LOCATION']] = $val['LOCATION'];
	}
	// print_r($year_prod_cost_arr);die();
	// ======================== calcutate kniting amount ====================
	$dyeing_kniting_qty_array = array();
	$knit_location_arr=array();
	foreach ($sql_kniting_dyeing_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];
			$is_sales_id=$val['IS_SALES'];
			//$kniting_cost=0;
			//$kniting_qty=0;		//grey_receive_qnty			
			if($val['POID']>0)
			{
				/*$kniting_cost = array_sum($conversion_costing_arr[$val[csf('po_breakdown_id')]][1]);
				$kniting_qty = array_sum($conversion_qty_arr[$val[csf('po_breakdown_id')]][1]);	
				if($kniting_cost>0)
				{
				$avg_kniting_rate = $kniting_cost/$kniting_qty;
				}*/
			//	echo $is_sales_id.'='.$is_sales_id.', ';
				if($is_sales_id!=1)
				{
					$knit_rate=$po_color_knit_array[$val['POID']][$val['DETER_ID']][1]['knit_rate'];
					//$po_color_knit_array[$val[csf('id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['knit_rate'] 
					//echo $knit_rate.'A,';
					//$po_color_knit_array[$val[csf('id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['knit_rate'] 
				}
				else
				{
					
					$knit_rate=$sales_data_knit_array[$val['POID']][$val['DETER_ID']][1]['process_rate'];
					
				}

				//echo $knit_rate.'DD';
			}	
			if($knit_rate>0)
			{
			$knitingCost =$knit_rate*$val['GREY_RECEIVE_QNTY'];	
			$main_array[$myear][$val['KNIT_LOCTION_ID']]['kniting'] += $knitingCost;
			}
			$knit_location_arr[$val['KNIT_LOCTION_ID']]=$val['KNIT_LOCTION_ID'];
	}
	// print_r($main_array);die();
	// ======================== calcutate dyeing amount ====================
	 $process_array=array(1,30,35);
	//$dying_prod_sql_res = sql_select($dying_prod_sql);
	$dying_prod_qty_array=array();
	foreach ($dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$entry_formId=$val['ENTRY_FORM'];
			$po_id=$val['POID'];
			$is_sales_id=$val['IS_SALES'];
			//$dyeing_qty=0;
			if($is_sales_id!=1)
			{
			$sensitive_id=$pre_fab_array[$val['POID']][$val['DETER_ID']]['sensitive'];
			}
		//	echo $sensitive_id.'='.$is_sales_id;
			foreach ($conversion_cost_head_array as $key => $value) 
			{
				if($val['POID']>0)
				{
					if(!in_array($key, $process_array ))
					{
						if($is_sales_id!=1)
						{
							if($key==31) //Fabric Dyeing
							{
								
								$conv_rate=$po_color_fab_array[$po_id][$val['COLOR_ID']][$val['DETER_ID']][$key]['rate'];
								 $dyeing_cost=$conv_rate*$val['BATCH_QNTY'];	
							}
							else if($key==33) //Heatset
							{
								$conv_rate=$po_color_fab_array2[$po_id][$val['DETER_ID']][$key]['rate'];
								$dyeing_cost=$conv_rate*$val['BATCH_QNTY'];
							}
							else
							{
								$conv_rate=$po_color_fab_array[$po_id][$val['DETER_ID']][$key]['rate'];
								$dyeing_cost =$conv_rate*$val['BATCH_QNTY'];	
								//echo $conv_rate.'b,';
							}
						}
						else
						{
							//$conv_rate=$sales_data_array[$po_id][$val[csf('deter_id')]][$val[csf('color_id')]][$key]['process_rate'];
							$conv_rate=$sales_data_array[$po_id][$val['DETER_ID']][$val['COLOR_ID']][$key]['process_rate'];
							$dyeing_cost =$conv_rate*$val['BATCH_QNTY'];	
						}
						
						//echo $dyeing_cost.'='.$po_id.'<br>';
						if($po_id==14404)
						{
							//echo  $dyeing_cost.',';
						}
						//else echo " X";
						if($dyeing_cost>0)
						{
						$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['dyeing_prod']+=$dyeing_cost;
						//$main_array[$fyear]['dyeing'] += $dyeing_cost;
						}
					}
				}
			}
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	
	foreach ($sub_dying_prod_sql_res as $val) 
	{
			$myear=$val["MONTH_YEAR"];	
			$entry_formId=$val['ENTRY_FORM'];
			
			//echo $val[csf('po_breakdown_id')].'='.$dyeing_cost.'='.$dyeing_qty.'<br> ';
		
		  if($val['BATCH_QNTY']>0)
			{
				$dying_prod_qty_array[$myear][$val['FLOOR_ID']]['sub_dyeing_prod']+=($val['BATCH_QNTY']*$order_wise_rate[$val['POID']])/$exch_rate;
				$main_array[$fyear]['dyeing'] += ($val['BATCH_QNTY']*$order_wise_rate[$val['POID']])/$exch_rate;;
			}
		$floor_id=$floor_library[$val['FLOOR_ID']]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	ksort($floorArray);
	
	foreach ($sql_sub_knit_res as $val) 
	{	
			$myear=$val["MONTH_YEAR"];								
			$subKnit_cost =$order_wise_rate[$val['ORDER_ID']]*$val["SUB_KNITTING_PROD"];	
			$subFinish_cost =$order_wise_rate[$val['ORDER_ID']]*$val["FIN_PROD"];	
			//echo $subFinish_cost.'='.$subFinish_cost.', ';
			if($subKnit_cost>0)
			{
				$subKnit_costUSD = $subKnit_cost/$rate;
				$main_array[$myear][$val['LOCATION_ID']]['subKnit'] += $subKnit_costUSD;
			}
			if($subFinish_cost>0)
			{
			$subFin_costUSD = $subFinish_cost/$rate;
			$main_array[$myear][$val['LOCATION_ID']]['subFinish'] += $subFin_costUSD;
			}
		//	$knit_location_arr[$val[csf('location_id')]]=$val[csf('location_id')];
	}
	//print_r($main_array2);
	ksort($knit_location_arr);
	foreach ($sql_sub_dye_res as $val) 
	{			
			$myear=$val["MONTH_YEAR"];				
			$subDye_cost =$order_wise_rate[$val['ORDER_ID']]*$val["KNITTING_PROD"];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$subDye_costUSD = $subDye_cost/$rate;	
			$fiscalDyeingYear=$val["year"].'-'.($val["year"]+1);
			$main_array[$myear]['subDye'] += $subDye_costUSD;	
	}
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			$myear=$val["MONTH_YEAR"];	//		
			$subsewOut_cost =$order_wise_rate[$val['ORDER_ID']]*$val['PRODUCTION_QNTY'];	
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val["YEAR"].'-'.($val["YEAR"]+1);//
			$main_array[$myear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_location_sewOut_array[$myear][$val["LOCATION_ID"]]['subSew'] += $subSewOut_costUSD;
			}
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_kniting_dyeing_res);
	unset($sql_dyeing_res);
	unset($sql_sub_dye_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=3");
	oci_commit($con);
	disconnect($con);
	
	$location_width=count($knit_location_arr)*80;
	$floor_width=count($floorArray)*80;
	$tbl_width = 460+(count($locationArray)*80)+$floor_width+$location_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <!--<table width="<? //echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
          <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Daily Revenue Report <? echo date('F-Y',strtotime($year)); ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Date</th>
	            <?
				$gmt_year='';
				$gmt_year=date('Y',strtotime($year));
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th width="80" title="<? echo $loc_id;?>"><?  echo ucfirst($location_library[$loc_id]);?> gmt</th>
	            	<?
	            }
	            ?>
	            <th width="80">Total gmt</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?> </th>
	            	<?
	            }
	            ?>
                <th width="80">Total Dyeing</th>
                 <?
	            foreach ($knit_location_arr as $knit_loc_id => $val) 
	            {
	            	?>
	            	<th width="80" title="<? echo $knit_loc_id;?>"><?  echo ucfirst($location_library[$knit_loc_id]);?> Knit</th>
	            	<?
	            }
	            ?>
                
	            <th width="80">Total Knitting </th>
	            <th width="80">Printing</th>
				<th width="80">Embroidery</th>
	            <th width="80" title="Total Gmt+Dying+Kniting">Total</th>
	        </thead>
		        <tbody>   
		        <?
		        	$total_gmts_array 	= array();
		        	$gr_fiscal_total 	= 0;
		        	$gr_dyeing_total 	= 0;
		        	$gr_kniting_total 	= 0;
		        	$gr_year_total 		= 0;$subConKni=$gr_total_gmtst=0;
					$total_textile=$total_knit_asia=0;
					$total_finish=0;$total_print_cost=0;$total_embro_cost=0;$total_wash_cost=0;
		        //	foreach ($fiscal_year_arr as $year => $val) 
				$i=1;
					foreach ($days_arr as $year => $val) 
		        	{
						$gmt_date=date('d-M',strtotime($year)).'-'.$gmt_year;
						
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        		$fiscal_total = 0;		        		
			        	?>     
				      <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trd_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trd_<? echo $i; ?>" style="font-size:11px">
				          <td><? echo date('d-F',strtotime($year));?></td>
				            <?
							$i++;
				            $total_gmts = 0;
				            foreach ($locationArray as $loc_id => $location_name) 
				            {
				            	//  echo $year_location_qty_array[$year][$loc_id]['finishing'].', ';
								 $tot_jm_gmt=$year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing Out*SMV*CPM/Exchange Rate+SubCon SewOut*SubCon Order Rate(<? echo $subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];?>)"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$loc_id; ?>','gmt_location_kal',7,'850');" ><?  echo number_format($tot_jm_gmt,0);?></a></td>
				            	<?
				            	$total_gmts += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$gr_fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$total_gmts_array[$loc_id] += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
				            	$fiscal_total += $year_location_qty_array[$year][$loc_id]['finishing']+$subCon_year_location_sewOut_array[$year][$loc_id]['subSew'];
								
								
				            }
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
				            ?>
				            <td align="right"><? echo number_format($total_gmts);?></td>
                            <?
							$dying_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$dying_prod_qty=$dying_prod_qty_array[$year][$floor_id]['dyeing_prod']+$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
								 
								  ?>
				            	<td align="right" title=",Without Knitting+YarnDying+AOP Rate from PreCost*Dying Prod Qty+SubConDyeing Prod*SubCOn Order Rate(<? echo $dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'].',ExchangeRate='.$exch_rate;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','dying_floor_kal',8,'820');" ><?  echo number_format($dying_prod_qty,0);?></a></td>
				            	<?
								$dying_floor_total[$floor_id]+=$dying_prod_qty;
								$dying_floor_tot+=$dying_prod_qty;
								$dying_floor_total_subcon[$floor_id] +=$dying_prod_qty_array[$year][$floor_id]['sub_dyeing_prod'];
				            }
							 
						
							?>
                             
                            <td align="right" title="All Dying Floor"><? echo number_format($dying_floor_tot,0); ?></td>
                             <?
							 $tot_knitting=0;
							foreach ($knit_location_arr as $knit_loc_id => $val) 
							{
								?>
								  <td align="right" title="Knitting Prod*Pre Cost Knit Avg Rate+SubCon Knit*SubCon Order Rate(<? echo $main_array[$year][$knit_loc_id]['subKnit'];?>)"><a href="##"  target="_self" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$knit_loc_id; ?>','knitting_prod_kal',8,'770');" ><? echo number_format($main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'],0); ?></a></td>
								<?
								$knitting_locatiion_arr[$knit_loc_id]+=$main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
								$tot_knitting+=$main_array[$year][$knit_loc_id]['kniting']+$main_array[$year][$knit_loc_id]['subKnit'];
							}
							?>
                            <td align="right" title="All Knitting"><? echo number_format($tot_knitting,0); ?></td>
				          
				            <td align="right" title="Print Recv*PreCost Print Avg Rate"><a href="##"  target="_self" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$knit_loc_id; ?>','print_prod_kal',9,'770');" ><? echo number_format($year_prod_cost_arr[$year]['print_recv'],0); ?></a></td>
							<td align="right"><a href="##" onclick="fnc_gmt_kal_popup('<? echo $gmt_date.'_'.$cbo_company_id.'_'.$floor_id; ?>','embro_prod_kal',6,'820');" ><? echo number_format($year_prod_cost_arr[$year]['embo_recv'],0); ?></a></td>
				            <td align="right" title="Total gmt+Total Dying+Knitting+Tot Print+Embrodiry"><? echo number_format($total_gmts+$dying_floor_tot+$tot_knitting+$year_prod_cost_arr[$year]['print_recv']+$year_prod_cost_arr[$year]['embo_recv'],0); ?></td>
				        </tr>
				        <?
				        $gr_dyeing_total += $dying_floor_tot ;//$main_array[$year]['dyeing']+$main_array[$year]['subDye'];
				        $gr_kniting_total += $main_array[$year]['kniting']+$main_array[$year]['subKnit'];
						$gr_total_gmts += $total_gmts;
						//$total_finish+=$main_array[$year]['subFinish'];
						$total_print_cost+=$year_prod_cost_arr[$year]['print_recv'];
						$total_embro_cost+=$year_prod_cost_arr[$year]['embo_recv'];
						$total_wash_cost+=$year_prod_cost_arr[$year]['wash_recv'];
						$subConKnit+=$main_array[$year]['subKnit'];
						
						//$total_textile+=$tot_textile;
						//$total_jm+=$total_gmts+$tot_textile;
								
						
				       // $gr_year_total += $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting']+$main_array[$year]['subDye']+$main_array[$year]['subKnit'];
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <?
	            foreach ($locationArray as $loc_id => $val) 
	            {
	            	?>
	            	<th title="SubCon=<? //echo $total_gmts_subcon_array[$loc_id];?>"><?  echo number_format($total_gmts_array[$loc_id],0);?></th>
	            	<?
	            }
	            ?>
	            <th><? echo number_format($gr_fiscal_total,0); ?></th>
	             <?
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th title="SubCon=<? echo $dying_floor_total_subcon[$floor_id];?>"><?  echo number_format($dying_floor_total[$floor_id],0);?></th>
	            	<?
	            }
	            ?>
	          
	            <th><? echo number_format($gr_dyeing_total,0); ?></th>
                 <?
				 $total_knitting=0;
	            foreach ($knit_location_arr as $knit_loc_id => $val) 
	            {
	            	?>
	            	 <th title=""><? echo number_format($knitting_locatiion_arr[$knit_loc_id],0); ?></th>
	            	<?
					$total_knitting+=$knitting_locatiion_arr[$knit_loc_id];
	            }
	            ?>
	           
	            <th><? echo number_format($total_knitting,0); ?></th>
                <th><? echo number_format($total_print_cost,0); ?></th>
				<th><? echo number_format($total_embro_cost,0); ?></th>
	            <th><? echo number_format($gr_total_gmts+$total_knitting+$gr_dyeing_total+$total_print_cost+$total_embro_cost,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
			
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls"; 
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}
if($action=="report_generate_jm_rmg_not") //Yearly JM RMG
{
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exlastYear[1];
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$fiscal_year_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$fiscal_year_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	// print_r($fiscal_year_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1"; //JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	$sql_fin_prod="SELECT a.floor_id,a.po_break_down_id,a.item_number_id as item_id,a.production_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS m$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS sew$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS pr$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS embr$fyear ";
		$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS wr$fyear ";
	}
	 $sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 order by a.floor_id";

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";

	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	}

	// ======================================= getting subcontact data =================================
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	// =================================== SubCon Sewout =============================
	$sql_sub_sewOut="SELECT a.order_id,a.floor_id,a.production_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS m$fyear ";
	}
	  $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2 order by a.floor_id";
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== subcon kniting =============================
	$sql_sub_knit="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS m$fyear ";
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id) THEN b.product_qnty END) AS fm$fyear ";
	}
	 $sql_sub_knit.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4) ";
	//entry_form=292, product_type=4

	// $sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	// echo $sql_sub_knit;die();
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// =================================== subcon dyeing =============================
	$sql_sub_dye="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_dye.=", (CASE WHEN a.product_date between '$exydata[0]' and '$exydata[1]' THEN b.product_qnty END) AS m$fyear ";
	}
	$sql_sub_dye.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			//$myear='m'.$nyear;
			$myear='sew'.$nyear;
			$pmyear='pr'.$nyear;
			$embrmyear='embr'.$nyear;
			$wrmyear='wr'.$nyear;
			$main_array[$fyear]['qty']+=$val[csf($myear)];
			$main_array[$fyear]['location'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
				$finish_cost=($sew_smv*$val[csf($myear)]*$cost_per_minute)/$exch_rate;
				$year_floor_array[$fyear][$val[csf('floor_id')]]['finishing'] += $finish_cost;
			}
		}
		$floor_id=$floor_library[$val[csf('floor_id')]]['floor_id'];
		//echo $floor_id.', ';
		if($floor_id)
		{
		$floorArray[$floor_id] = $floor_id;
		}
	}
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='m'.$nyear;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf($myear)];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			if($subsewOut_cost>0)
			{
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			
			$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
			$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_floor_sewOut_array[$fyear][$val[csf("floor_id")]]['subSew'] += $subSewOut_costUSD;
			}
		}	
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floorArray)*80;
	$tbl_width = 140+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Yearly Revenue Report <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="60">Fiscal Year</th>
                 <?
	            foreach ($floorArray as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
	            	?>
	            	<th width="80" title="FloorId=<? echo $floor_id;?>"><?  echo ucfirst($floor_library[$floor_id]['floor']);?></th>
	            	<?
	            }
	            ?>
	            <th width="80">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($fiscal_year_arr as $year => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";        		
			        	?>     
				         <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trdd_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trdd_<? echo $i; ?>" style="font-size:11px">
				            <td><a href="javascript:void()" onclick="report_generate_by_year('<? echo $year?>','5')"><? echo $year;?></a></td>
                            <?
							$i++;
							$rmg_floor_tot=0;
				            foreach ($floorArray as $floor_id => $floor_name) 
				            {
				            	$floor_rmg=$year_floor_array[$year][$floor_id]['finishing']+$subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];?>)"><?  echo number_format($floor_rmg,0);?></td>
				            	<?
								$rmg_floor_total[$floor_id]+=$floor_rmg;
								$rmg_floor_tot+=$floor_rmg;
				            }
							?>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($rmg_floor_tot,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floorArray as $floor_id => $val) 
	            {
	            	?>
	            	<th><?  echo number_format($rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$floor_rmg;
	            }
	            ?>
	            <th><? echo number_format($total_ashulia_rmg,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="report_generate_jm_rmg") //Yearly JM RMG
{
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$type 			= str_replace("'","",$type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exlastYear[1];
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$fiscal_year_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			//$fiscal_year='';
			if($firstYear<$lastYear)
			{
				$fiscal_year=$firstYear.'-'.($firstYear+1);
				$monthYr=''; $fstYr=$lstYr="";
				$fstYr=date("d-M-Y",strtotime(($firstYear.'-7-1')));
				$lstYr=date("d-M-Y",strtotime((($firstYear+1).'-6-30')));
				
				$monthYr=$fstYr.'_'.$lstYr;
				
				$fiscal_year_arr[$fiscal_year]=$monthYr;
				$i++;
			}
		}
	}
	// print_r($fiscal_year_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing ============================= 	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2"; //KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	unset($sql_floor);
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
			unset($sql_std_para);
		//print_r($financial_para_cpm);
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc"; 
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			if($val[csf('floor_name')]==7) //1st floor
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==9) //2nd 
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==12)//5th //test-Fid=49
			{
		 	$floor_group_gf5_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==11)//4th //test-Fid=49
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==57) //  Sample Cut
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==56) //Sample Intimate   
			{
			$floor_group_samf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==59) //Training Center  
			{
			$floor_group_training_arr[$flr_grp].=$val[csf('id')].',';
			}
			
		}
		unset($line_data);
		//print_r($floor_group_training_arr);
		if($prod_reso_allocation==1)
		{
			$sql_fin_prod="SELECT a.sewing_line,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,a.production_date,c.line_number";
			foreach($fiscal_year_arr as $fyear=>$ydata)
			{
				//$exydata=explode("_",$ydata);
				//$ffyear=str_replace("-","_",$fyear); //prod_resource_mst
				$exydata=explode("_",$ydata);
				$ffyear=str_replace("-","_",$fyear);
				
				//$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty  END) AS m$ffyear ";
				 $sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS sew$ffyear ";
			}//sewing_line
		    $sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty else 0 END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type in(5)  and a.production_source in(1)    and a.floor_id is not null and a.floor_id <> 0   order by a.floor_id"; //and a.po_break_down_id in(15337,5871) and a.floor_id=7
		}
		else 
		{
			$sql_fin_prod="SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,a.production_date";
			foreach($fiscal_year_arr as $fyear=>$ydata)
			{
				$exydata=explode("_",$ydata);
				$fyear=str_replace("-","_",$fyear); //prod_resource_mst 
				
				//$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=8 THEN b.production_qnty END) AS m$fyear ";
				$sql_fin_prod.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' and a.production_type=5 THEN b.production_qnty END) AS sew$fyear ";
			}//sewing_line
			
			 $sql_fin_prod.=" ,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type in(5)  and a.production_source in(1)    and a.floor_id is not null and a.floor_id <> 0  order by a.floor_id";
		}
	
	
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	//print_r($sql_fin_prod_res);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	 $sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0   $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	unset($sql_po_result);
	//print_r($po_job_array);
	
	// $cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1   $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond "; 
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();$pre_cost_smv_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		 
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		 
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$exchange_rate = $val[csf('exchange_rate')];
	}
unset($cm_sql_res);
	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	// =================================== SubCon Sewout =============================
	if($prod_reso_allocation==1)
	{
	$sql_sub_sewOut="SELECT c.line_number,a.order_id,a.floor_id,a.production_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS m$fyear ";
	}
	  $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a,prod_resource_mst c where  c.id=a.line_id and a.company_id in($cbo_company_id)  and a.production_date between '$startDate' and '$endDate' and a.production_type=2 order by a.floor_id";
	}
	else
	{
		$sql_sub_sewOut="SELECT a.line_id as line_number,a.order_id,a.floor_id,a.production_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_sewOut.=", (CASE WHEN a.production_date between '$exydata[0]' and '$exydata[1]' THEN a.production_qnty END) AS m$fyear ";
	}
	   $sql_sub_sewOut.=" from subcon_gmts_prod_dtls a where  a.company_id in($cbo_company_id)  and a.production_date between '$startDate' and '$endDate' and a.production_type=2 order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== subcon kniting =============================
	$sql_sub_knit="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS m$fyear ";
		$sql_sub_knit.=", (CASE WHEN a.product_date   between '$exydata[0]' and '$exydata[1]' and a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id) THEN b.product_qnty END) AS fm$fyear ";
	}
	 $sql_sub_knit.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(292,159)  and a.product_date between '$startDate' and '$endDate' and a.product_type in(2,4) and a.location_id=5 ";
	//entry_form=292, product_type=4

	// $sql_sub_knit = "SELECT b.order_id, to_char(a.product_date,'YYYY') as year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=159 and a.knitting_company=$cbo_company_id and a.product_date between '$startDate' and '$endDate' and a.product_type=2 and b.process='1'";
	// echo $sql_sub_knit;die();
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	// =================================== subcon dyeing =============================
	$sql_sub_dye="SELECT b.order_id,a.product_date";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_sub_dye.=", (CASE WHEN a.product_date between '$exydata[0]' and '$exydata[1]' THEN b.product_qnty END) AS m$fyear ";
	}
	//echo $cm_cost_method_based_on.'D';
	$sql_sub_dye.=" from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$year_floor_array=array();$year_floor_array2=array();$year_floor_array3=array();$year_floor_array4=array();$year_floor_arrayTraning=array();
	//print_r($sql_fin_prod_res);die;
	foreach ($sql_fin_prod_res as $val) 
	{	
		
		//$floor_idArr=$val[csf('floor_id')].'_'.$val[csf('floor_id')];
		//$floor_group=rtrim($floor_line_group_arr[$val[csf('floor_id')]],',');
		//echo $floor_line_group_arr[$val[csf('floor_id')]].', ';
	//	echo $val[csf('floor_id')].'='.$floor_group.'<br>';
	 //echo $floor_group.', ';  
	// print_r($floor_group_ff_arr);
	//echo $val[csf('floor_id')].'x,';
	 	
		$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			
			$nyear=str_replace("-","_",$fyear);
			//echo $fyear.'=='.$nyear.'<br>'; 
			//$myear='m'.$nyear;
			$myear='sew'.$nyear;
			/*$pmyear='pr'.$nyear;
			$embrmyear='embr'.$nyear;
			$wrmyear='wr'.$nyear;*/
		
			
			//echo $fyear.'=='.$sew_oput_prod.'<br>';  
			
			//$main_array[$fyear]['location'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $fyear.',';;
			//$line_group=rtrim($floor_line_group_arr[$val[csf('floor_id')]],",");
			$sew_oput_prod=$val[csf($myear)]; 
			// $floor_fyear=0;
			///$ff_floor_id=0;
			
			if($sew_smv>0)
			{
				
				// $floor_fyear= $fyear;
				//$main_array[$fyear]['qty']+=$sew_oput_prod;
				 $cm_cost_based_on_date="";
			// echo $val[csf('po_break_down_id')].'='.$cm_cost_method_based_on.', ';
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				//echo $shipment_date.', ';
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
			 
			  $line_number=array_unique(explode(",",$val[csf('line_number')]));
			  //echo $line_number.'d,';
			 // if($val[csf($myear)]=="") $prod_qty=0;else $prod_qty=$val[csf($myear)];
			 
			//  $floor_fyear=$fyear;
			  $ff_floor_id=$val[csf('floor_id')];
		 
			foreach($floor_group_ff_arr as $flr_grop1=>$val1) //1st Floor
			{
				
				$flr_grop_ex=explode("_",$flr_grop1);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.'='.$floor_fyear.', ';
			
				$flr_grop_exF=$flr_grop_ex[0];
				//echo $fyear.'='.$flr_grop_exF.'='.$val[csf('floor_id')].'<br>';
				if($flr_grop_exF==$ff_floor_id)  
				{
				//if($sew_smv>0)
				//{
					//$main_array[$fyear]['qty']+=$sew_oput_prod;
					
				//	if($cost_per_minute) $cost_per_minute=$cost_per_minute;else $cost_per_minute=0;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					//if ($fyear=='2020-2021') echo $fyear.'='.$prod_qty;die;
					//if($sew_oput_prod>0)
					//{
						if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
						$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
						//echo $fyear.'='.$exch_rate.'='.$flr_grop_exF.'='.$val[csf('floor_id')].'<br>';
							//$main_array[$fyear]['qty']+=$sew_oput_prod;
						//$finish_cost=$sew_smv;
						foreach( $line_number as $lineId)
						{
							
						$year_floor_array[$fyear][$flr_grop1][$lineId]['finishing'] += $finish_cost;
						}
						
					//}
				 //}  
				}
				 //Group end
			} //floor_group_sf_arr
			 foreach($floor_group_sf_arr as $flr_grop2=>$val2) //2st Floor
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				if($flr_grop_ex2[0]==$ff_floor_id) 
				{
				//if($sew_smv>0)
				//{
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array2[$fyear][$flr_grop2][$lineId]['finishing'] += $finish_cost;
					}
				 //}
				}
				 //Group end
			} //floor_group_sf_arr
			foreach($floor_group_gf_arr as $flr_grop3=>$val4) //4rd Floor
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//	echo $fyear.'='.$val[csf($myear)].', ';
				if($flr_grop_ex3[0]==$ff_floor_id) 
				{
				//if($sew_smv>0 && $val[csf($myear)]>0)
				//{
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					//echo $fyear.'='.$sew_oput_prod.'<br>';
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
						//echo $fyear.'=='.$finish_cost.'<br>'; 
					$year_floor_array3[$fyear][$flr_grop3][$lineId]['finishing'] += $finish_cost;
					}
				 //}
				}
				 //Group end
			} 
			foreach($floor_group_gf5_arr as $flr_grop5=>$val5) //5rd Floor
			{
				$flr_grop_ex5=explode("_",$flr_grop5);
			
				if($flr_grop_ex5[0]==$ff_floor_id) 
				{
				//if($sew_smv>0)
				//{
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
						//echo $fyear.'=='.$finish_cost.'<br>'; 
					$year_floor_array5[$fyear][$flr_grop5][$lineId]['finishing'] += $finish_cost;
					}
				 //}
				}
				 //Group end
			} 
			foreach($floor_group_tf_arr as $flr_grop4=>$val6) //4rd Floor
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				if($flr_grop_ex4[0]==$ff_floor_id) 
				{
				//if($sew_smv>0)
				//{
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array4[$fyear][$flr_grop4][$lineId]['finishing'] += $finish_cost;
					}
				 //}
				}
				 //Group end
			}//End
			foreach($floor_group_samf_arr as $flr_gropSM=>$val7) //Sample Intimate Floor
			{
				$flr_grop_exSM=explode("_",$flr_gropSM);
				if($flr_grop_exSM[0]==$ff_floor_id) 
				{
				//if($sew_smv>0)
				//{
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_array6[$fyear][$flr_gropSM][$lineId]['finishing'] += $finish_cost;
					}
				// }
				}
				 //Group end
			}  
			foreach($floor_group_training_arr as $flr_gropTraining=>$val8) //Training Center Floor
			{
				$flr_grop_exTR=explode("_",$flr_gropTraining);
				if($flr_grop_exTR[0]==$ff_floor_id) 
				{
				//if($sew_smv>0)
				//{
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
					//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
					//echo $sew_oput_prod.'D='.$cost_per_minute.',';
					$finish_cost=($sew_smv*$sew_oput_prod*$cost_per_minute)/$exch_rate;
					foreach( $line_number as $lineId)
					{
					$year_floor_arrayTraning[$fyear][$flr_gropTraining][$lineId]['finishing'] += $finish_cost;
					}
				// }
				}
				 //Group end
			}  
			
			
			}//smv end
		} 
	}
	
	//print_r($year_floor_arrayTraning);//die;
	//SubCon Sewing Out
$subCon_year_floor_sewOut_array=array();$subCon_year_floor_sewOut_array2=array();
$subCon_year_floor_sewOut_array3=array();
$subCon_year_floor_sewOut_array4=array();

	foreach ($sql_sub_sewOut_result as $val) 
	{			
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='m'.$nyear;		
			$sub_sew_prod=$val[csf($myear)];
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$sub_sew_prod;	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$sub_ff_floor_id=$val[csf('floor_id')];
		//	$line_number=$val[csf('line_number')];
			 $line_number=array_unique(explode(",",$val[csf('line_number')]));
			foreach($floor_group_ff_arr as $s_flr_grop=>$val1)//1st 1
			{
				$flr_grop_ex=explode("_",$s_flr_grop);
				
				if($flr_grop_ex[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					
					foreach( $line_number as $lineId)
					  {
						 // echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$subSewOut_costUSD.'T, ';
					  $subCon_year_floor_sewOut_array[$fyear][$s_flr_grop][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					 }
				}
			}//flr Group End
			foreach($floor_group_sf_arr as $flr_grop2=>$val2)//1st 1
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					foreach( $line_number as $lineId)
					  {	
					  $subCon_year_floor_sewOut_array2[$fyear][$flr_grop2][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}//flr Group End
			foreach($floor_group_gf_arr as $flr_grop3=>$val3)//1st 1
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					foreach( $line_number as $lineId)
					  {		
					$subCon_year_floor_sewOut_array3[$fyear][$flr_grop3][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}//flr Group End
			foreach($floor_group_tf_arr as $flr_grop4=>$val4)//1st 1
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$sub_ff_floor_id) 
				{
				if($subsewOut_cost>0)
				{
				$subSewOut_costUSD = $subsewOut_cost/$rate;	
				$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
				$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
				 foreach( $line_number as $lineId)
				 {	
				 $subCon_year_floor_sewOut_array6[$fyear][$flr_grop4][$lineId]['subSew'] += $subSewOut_costUSD;
				 }
				}
				}
			}//flr Group End
			
			
			
		}	
	}
	//print_r($subCon_year_floor_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);//floor_group_training_arr
	$floor_width=count($floor_group_ff_arr)*70+count($floor_group_sf_arr)*70+count($floor_group_gf_arr)*70+count($floor_group_tf_arr)*70+count($floor_group_samf_arr)*70+count($floor_group_gf5_arr)*70+count($floor_group_training_arr)*70;
	$tbl_width = 400+$floor_width;
	ob_start();	
	//echo count($floor_group_gf_arr).'AZAAAAAAAAAA';
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Yearly Revenue Report <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <th width="70">Fiscal Year</th>
                 <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_ff_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id); 
				
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-1</th>
                  <? 
				    ksort($floor_group_sf_arr);
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_sf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-2</th>
                 
                  <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	         
			    foreach ($floor_group_gf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_gf_arr>0))
				{ 
	            ?>
                 <th width="70">Unit-4</th>
                 
                  <? 
				}
				//$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf5_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_gf5_arr>0))
				{ 
	            ?>
                 <th width="70">Unit-5</th>
                 
                  <? 
				}
				
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_training_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_training_arr>0))
				{ 
	            ?>
                 <th width="70">Training Center</th>
                 
                  <? 
				}
				
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_tf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				
				
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">Sample CutSew</th>
                 <?
				}
				foreach ($floor_group_samf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', Line='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th width="70">Sample Intimate</th>
                 <?
				}
				
				 ?>
	        <th width="70">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($fiscal_year_arr as $year => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	        		
			        	?>     
				         <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
				            <td><a href="javascript:void()" onclick="report_generate_by_year('<? echo $year?>','5')"><? echo $year;?></a></td>
                            <?
							$i++;
							$ff_rmg_floor_tot=0;
				            foreach ($floor_group_ff_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$ff_floor_rmg_line=0;$sub_ff_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
									$ff_floor_rmg_line+=$year_floor_array[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										$sub_ff_floor_rmg_line+=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										//echo $subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'].'d ';
									}
								$ff_floor_rmg=$ff_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_ff_floor_rmg_line;?>)"><?  echo number_format($ff_floor_rmg,0);?></td>
				            	<?
								$ff_rmg_floor_total[$floor_id]+=$ff_floor_rmg;
								$ff_rmg_floor_tot+=$ff_floor_rmg;
				            }
							?>
                                 <td align="right" title="All 1st Floor"><? echo number_format($ff_rmg_floor_tot,0); ?></td>
                             <?
							$sf_rmg_floor_tot=0;
				            foreach ($floor_group_sf_arr as $floor_id => $val)
				            {
									$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$sf_floor_rmg_line=0;$sub_sf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$sf_floor_rmg_line+=$year_floor_array2[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									$sub_sf_floor_rmg_line+=$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									}
								$sf_floor_rmg=$sf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_sf_floor_rmg_line;?>)"><?  echo number_format($sf_floor_rmg,0);?></td>
				            	<?
								$sf_rmg_floor_total[$floor_id]+=$sf_floor_rmg;
								$sf_rmg_floor_tot+=$sf_floor_rmg;
				            }
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($sf_rmg_floor_tot,0); ?></td>
                             <?
							$gf_rmg_floor_tot=0;
				            foreach ($floor_group_gf_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$gf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$gf_floor_rmg_line+=$year_floor_array3[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
									}
								$gf_floor_rmg=$gf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];?>)"><?  echo number_format($gf_floor_rmg,0);?></td>
				            	<?
								$gf_rmg_floor_total[$floor_id]+=$gf_floor_rmg;
								$gf_rmg_floor_tot+=$gf_floor_rmg;
				            }
							if(count($floor_group_gf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($gf_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   //5
						   $gf5_rmg_floor_tot=0;
				            foreach ($floor_group_gf5_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$gf5_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$gf5_floor_rmg_line+=$year_floor_array5[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
									}
								$gf5_floor_rmg=$gf5_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array5[$year][$floor_id]['subSew'];?>)"><?  echo number_format($gf5_floor_rmg,0);?></td>
				            	<?
								$gf5_rmg_floor_total[$floor_id]+=$gf5_floor_rmg;
								$gf5_rmg_floor_tot+=$gf5_floor_rmg;
				            }
							if(count($floor_group_gf5_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($gf5_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   //5th end
						   
						    //Training Center
						   $tc_rmg_floor_tot=0;
				            foreach ($floor_group_training_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$tc_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$tc_floor_rmg_line+=$year_floor_arrayTraning[$year][$floor_id][$lId]['finishing'];
										//+$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew']
									}
								//$gf5_floor_rmg=$tc_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? //echo $subCon_year_floor_sewOut_array5[$year][$floor_id]['subSew'];?>)"><?  echo number_format($tc_floor_rmg_line,0);?></td>
				            	<?
								$tc_rmg_floor_total[$floor_id]+=$tc_floor_rmg_line;
								$tc_rmg_floor_tot+=$tc_floor_rmg_line;
				            }
							if(count($floor_group_training_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($tc_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   //Training Center Floor th end
						   
							$tf_rmg_floor_tot=0;
				            foreach ($floor_group_tf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tf_floor_rmg_line+=$year_floor_array4[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
								}
								$tf_floor_rmg=$tf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];?>)"><?  echo number_format($tf_floor_rmg,0);?></td>
				            	<?
								$tf_rmg_floor_total[$floor_id]+=$tf_floor_rmg;
								$tf_rmg_floor_tot+=$tf_floor_rmg;
				            }
							if(count($floor_group_tf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($tf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$samf_rmg_floor_tot=0;
				            foreach ($floor_group_samf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$samf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$samf_floor_rmg_line+=$year_floor_array6[$year][$floor_id][$lId]['finishing'];
								}
								$samf_floor_rmg=$samf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? //echo $subCon_year_floor_sewOut_array6[$year][$floor_id]['subSew'];?>)"><?  echo number_format($samf_floor_rmg,0);?></td>
				            	<?
								$samf_rmg_floor_total[$floor_id]+=$samf_floor_rmg;
								$samf_rmg_floor_tot+=$samf_floor_rmg;
				            }
							if(count($floor_group_samf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($samf_rmg_floor_tot,0); ?></td>
                            <?
							}
							
							$total_floor_ratanpur=$tf_rmg_floor_tot+$gf_rmg_floor_tot+$sf_rmg_floor_tot+$ff_rmg_floor_tot+$samf_rmg_floor_tot+$gf5_rmg_floor_tot+$tc_rmg_floor_tot;
							?>
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($total_floor_ratanpur,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody> 
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floor_group_ff_arr as $floor_id => $val)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><?  echo number_format($ff_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$ff_rmg_floor_total[$floor_id]; 
	            }
	            ?>
  				<th><? echo number_format($gr_rmg_floor_tot,0); ?></th>
				 <?
				 $sf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_sf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($sf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$sf_gr_rmg_floor_tot+=$sf_rmg_floor_total[$floor_id]; 
	            }
	            ?>
                <th><? echo number_format($sf_gr_rmg_floor_tot,0); ?></th>
                 <?
				 $gf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf_gr_rmg_floor_tot+=$gf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf_arr>0))
				{
	            ?>
                <th><? echo number_format($gf_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				//5
				 $gf5_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf5_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf5_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf5_gr_rmg_floor_tot+=$gf5_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf5_arr>0))
				{
	            ?>
                <th><? echo number_format($gf5_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				//5th end
				//Traininig Center 
				 $tc_gr_rmg_floor_tot=0;
	            foreach ($floor_group_training_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tc_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$tc_gr_rmg_floor_tot+=$tc_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_training_arr>0))
				{
	            ?>
                <th><? echo number_format($tc_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				//Traininig Center  end
				
				 $gr_tf_floor_tot=0;
	            foreach ($floor_group_tf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_tf_floor_tot+=$tf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_tf_floor_tot,0); ?></th>
                 <?
				}
				 $gr_samf_floor_tot=0;
	            foreach ($floor_group_samf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($samf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_samf_floor_tot+=$samf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_samf_floor_tot,0); ?></th>
                 <?
				}
	            ?>
                <th><? echo number_format($gr_tf_floor_tot+$gf_gr_rmg_floor_tot+$sf_gr_rmg_floor_tot+$gr_rmg_floor_tot+$gr_samf_floor_tot+$gf5_gr_rmg_floor_tot+$tc_gr_rmg_floor_tot,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit(); 
}

 
if($action=="report_generate_by_year_jm_rmg") //Monthly JM of RMG
{ 
	
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	// $from_year 		= str_replace("'","",$cbo_from_year);
	// $to_year 		= str_replace("'","",$cbo_to_year);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2";//KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		$floor_group_training_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==7) //1st floor
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==9) //2nd 
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==12)//5th //test-Fid=49
			{
		 	$floor_group_gf5_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==11)//4th //test-Fid=49
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==57) //  Sample Cut
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==56) //Sample Intimate   
			{
			$floor_group_samf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==59) //Training Center  
			{
			$floor_group_training_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	//print_r($floor_group_ff_arr);
//	echo $prod_reso_allocation.'d';;
	if($prod_reso_allocation==1)
	{
	  $sql_fin_prod=" SELECT c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and    '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id  and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.floor_id is not null and a.floor_id <> 0     order by a.floor_id";
	}
	else
	{
		 $sql_fin_prod=" SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.floor_id is not null and a.floor_id <> 0   order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	 
	
	//$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	if($prod_reso_allocation==1)
	{
	  $sql_sub_sewOut=" SELECT c.line_number,a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a ,prod_resource_mst c where c.id=a.line_id and a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2   order by a.floor_id";
	}
	else 
	{
		$sql_sub_sewOut=" SELECT a.line_id as line_number,a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a ,prod_resource_mst c where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2  and a.status_active=1 order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$year_floor_array=array();$year_floor_array2=array();$year_floor_array3=array();$year_floor_array4=array();
	foreach ($sql_fin_prod_res as $val) 
	{	
		
			$fyear=$val[csf("month_year")];
			$sew_out_prod=$val[csf("sew_out")];
			$main_array[$fyear]['qty']+=$val[csf("sew_out")];//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			
		//	echo $sew_smv.'='.$sew_smv.'X';
		 $ff_floor_id=$val[csf('floor_id')];
		 $sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		 $cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				
				 $line_number=array_unique(explode(",",$val[csf('line_number')]));
				 $tot_sew_out_qty2=0;
			foreach($floor_group_ff_arr as $flr_grop1=>$val1) //1st Floor
			{
				$flr_grop_ex=explode("_",$flr_grop1);
				if($flr_grop_ex[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
					//	echo $sew_smv.'='.$sew_out_prod.'='.$cost_per_minute.'='.$exch_rate.'<br>';
					
						foreach( $line_number as $lineId)
					    {
						$year_floor_array[$fyear][$flr_grop1][$lineId]['finishing'] += $finish_cost;
						 $tot_sew_out_qty2+=$exch_rate;;
						}
					}
				}
			}
		
			foreach($floor_group_sf_arr as $flr_grop2=>$val2) //2nd Floor
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				if($flr_grop_ex2[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array2[$fyear][$flr_grop2][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
			foreach($floor_group_gf_arr as $flr_grop3=>$val3) //3rd Floor
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				if($flr_grop_ex3[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array3[$fyear][$flr_grop3][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
			foreach($floor_group_gf5_arr as $flr_grop5=>$val5) //5rd Floor
			{
				$flr_grop_ex5=explode("_",$flr_grop5);
				if($flr_grop_ex5[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array5[$fyear][$flr_grop5][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}//Training Center
			foreach($floor_group_training_arr as $flr_grop9=>$val5) //5rd Floor
			{
				$flr_grop_ex9=explode("_",$flr_grop9);
				if($flr_grop_ex9[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_arrayTraning[$fyear][$flr_grop9][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}//floor_group_training_arr
			foreach($floor_group_tf_arr as $flr_grop4=>$val4) //4th Floor
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				if($flr_grop_ex4[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array4[$fyear][$flr_grop4][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
			
			foreach($floor_group_samf_arr as $flr_gropSm=>$val6) //Sample Floor
			{
				$flr_grop_exSM=explode("_",$flr_gropSm);
				if($flr_grop_exSM[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						
						 //echo $cost_per_minute."<br>";
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
					    {
						$year_floor_array6[$fyear][$flr_gropSm][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			}
		
	}
		//echo $tot_sew_out_qty2.'=A';;
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	$subCon_year_floor_sewOut_array=array();$subCon_year_floor_sewOut_array2=array();
	$subCon_year_floor_sewOut_array3=array();
	$subCon_year_floor_sewOut_array4=array();

	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$myear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$sub_ff_floor_id=$val[csf('floor_id')];
			 $line_number=array_unique(explode(",",$val[csf('line_number')]));
			foreach($floor_group_ff_arr as $s_flr_grop=>$val1)//1st 1
			{
				$flr_grop_ex=explode("_",$s_flr_grop);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					foreach( $line_number as $lineId)
					  {	
					$subCon_year_floor_sewOut_array[$myear][$s_flr_grop][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			foreach($floor_group_sf_arr as $s_flr_grop2=>$val2)//2nd
			{
				$flr_grop_ex2=explode("_",$s_flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					  {	
					 // echo $subSewOut_costUSD.',';
					$subCon_year_floor_sewOut_array2[$myear][$s_flr_grop2][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			foreach($floor_group_gf_arr as $s_flr_grop3=>$val3)//2nd
			{
				$flr_grop_ex3=explode("_",$s_flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					  {	
					$subCon_year_floor_sewOut_array3[$myear][$s_flr_grop3][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			foreach($floor_group_tf_arr as $s_flr_grop4=>$val4)//2nd
			{
				$flr_grop_ex4=explode("_",$s_flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					  {	
					$subCon_year_floor_sewOut_array4[$myear][$s_flr_grop4][$lineId]['subSew'] += $subSewOut_costUSD;
					  }
					}
				}
			}
			
	}
	//print_r($subCon_year_location_sewOut_array);
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floor_group_ff_arr)*70+count($floor_group_sf_arr)*70+count($floor_group_gf_arr)*70+count($floor_group_tf_arr)*70+count($floor_group_samf_arr)*70+count($floor_group_gf5_arr)*70+count($floor_group_training_arr)*70;//
	$tbl_width = 400+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	  <!--  <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
        <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Monthly Revenue Report <? echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	             <th width="60">Month Year</th>
                 <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_ff_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id); 
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-1</th>
                  <? 
				  
				 //$floor_group_arr[$val[csf('floor_name')]]
				  ksort($floor_group_sf_arr);
	            foreach ($floor_group_sf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-2</th>
                 
                  <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_gf_arr>0))
				{ 
	            ?>
                 <th width="70">Unit-4</th>
                 
                  <? 
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf5_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.', LineId='.$val;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-5</th>
                 
                 
                 <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_training_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				?>
                 <th width="70">Training Center</th>
                 
                 <?
                
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_tf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				 
				
				//year_floor_arrayTraining
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">Sampe CutSew</th>
                 <?
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_samf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id;?>"><?  echo  ucfirst($ex_floor[1]);;?></th>
	            	<?
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th width="70">Sample Intimate</th>
                 <?
				}
				
				 ?>
	        <th width="100">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
		        	foreach ($fiscalMonth_arr as $year => $val) 
		        	{
		        		 $year_ex = explode("-", $year);
		        		$fiscal_total = 0;		      	        		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>">
				              <td><a href="javascript:void()" onclick="report_generate_by_month('<? echo $year?>',5)"><? echo date('F-y',strtotime($year));?></a></td>
                            <?
							$i++;
							$ff_rmg_floor_tot=0;
				            foreach ($floor_group_ff_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$ff_floor_rmg_line=0;$sub_ff_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$sub_ff_floor_rmg_line+=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										$subSew=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										if($subSew>0) $subSew_m=$subSew;else $subSew_m=0;
										if($year_floor_array[$year][$floor_id][$lId]['finishing']>0)
										{
											//echo $lId.'='.$year_floor_array[$year][$floor_id][$lId]['finishing'].'<br>';
										$ff_floor_rmg_line+=$year_floor_array[$year][$floor_id][$lId]['finishing']+$subSew_m;		
										}
									}
									
								$ff_floor_rmg=$ff_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_ff_floor_rmg_line;?>)"><?  echo number_format($ff_floor_rmg,0);?></td>
				            	<?
								$ff_rmg_floor_total[$floor_id]+=$ff_floor_rmg;
								$ff_rmg_floor_tot+=$ff_floor_rmg;
				            }
							?>
                                 <td align="right" title="All 1st Floor"><? echo number_format($ff_rmg_floor_tot,0); ?></td>
                             <?
							$sf_rmg_floor_tot=0;
				            foreach ($floor_group_sf_arr as $floor_id => $val)
				            {
									$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$sf_floor_rmg_line=0;$sub_sf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										//echo $subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'].'D, ';
										$sub_ff_floor_rmg_line+=$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
										$sf_floor_rmg_line+=$year_floor_array2[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									}
				            	
								$sf_floor_rmg=$sf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_sf_floor_rmg_line;?>)"><?  echo number_format($sf_floor_rmg,0);?></td>
				            	<?
								$sf_rmg_floor_total[$floor_id]+=$sf_floor_rmg;
								$sf_rmg_floor_tot+=$sf_floor_rmg;
				            }
							?>
                            <td align="right" title=""><? echo number_format($sf_rmg_floor_tot,0); ?></td>
                             <?
							$gf_rmg_floor_tot=0;
				            foreach ($floor_group_gf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$gf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$gf_floor_rmg_line+=$year_floor_array3[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
								}
									
								$gf_floor_rmg=$gf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];?>)"><?  echo number_format($gf_floor_rmg,0);?></td>
				            	<?
								$gf_rmg_floor_total[$floor_id]+=$gf_floor_rmg;
								$gf_rmg_floor_tot+=$gf_floor_rmg;
				            }
							if(count($floor_group_gf_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($gf_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   $gf5_rmg_floor_tot=0;
				            foreach ($floor_group_gf5_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$gf5_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$gf5_floor_rmg_line+=$year_floor_array5[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
								}
									
								$gf5_floor_rmg=$gf5_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array5[$year][$floor_id]['subSew'];?>)"><?  echo number_format($gf5_floor_rmg,0);?></td>
				            	<?
								$gf5_rmg_floor_total[$floor_id]+=$gf5_floor_rmg;
								$gf5_rmg_floor_tot+=$gf5_floor_rmg;
				            }
							if(count($floor_group_gf5_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($gf5_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   //Training Center
						    $tc_rmg_floor_tot=0;
				            foreach ($floor_group_training_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$tc_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tc_floor_rmg_line+=$year_floor_arrayTraning[$year][$floor_id][$lId]['finishing'];
									//+$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew']
								}
									
								//$gf5_floor_rmg=$tc_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? //echo $subCon_year_floor_sewOut_array5[$year][$floor_id]['subSew'];?>)"><?  echo number_format($tc_floor_rmg_line,0);?></td>
				            	<?
								$tc_rmg_floor_total[$floor_id]+=$tc_floor_rmg_line;
								$tc_rmg_floor_tot+=$tc_floor_rmg_line;
				            }
							if(count($floor_group_training_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($tc_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   
							$tf_rmg_floor_tot=0;
				            foreach ($floor_group_tf_arr as $floor_id => $val)
				            {
				           		 $lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tf_floor_rmg_line+=$year_floor_array4[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
								}
								
								$tf_floor_rmg=$tf_floor_rmg_line+$subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array4[$year][$floor_id]['subSew'];?>)"><?  echo number_format($tf_floor_rmg,0);?></td>
				            	<?
								$tf_rmg_floor_total[$floor_id]+=$tf_floor_rmg;
								$tf_rmg_floor_tot+=$tf_floor_rmg;
				            }
							if(count($floor_group_tf_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($tf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$samf_rmg_floor_tot=0;
				            foreach ($floor_group_samf_arr as $floor_id => $val)
				            {
				           		 $lineId=rtrim($val,',');
								$lineIds=explode(",",$lineId);
								$samf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$samf_floor_rmg_line+=$year_floor_array6[$year][$floor_id][$lId]['finishing'];
								}
								$samf_floor_rmg=$samf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? //echo $subCon_year_floor_sewOut_array6[$year][$floor_id]['subSew'];?>)"><?  echo number_format($samf_floor_rmg,0);?></td>
				            	<?
								$samf_rmg_floor_total[$floor_id]+=$samf_floor_rmg;
								$samf_rmg_floor_tot+=$samf_floor_rmg;
				            }
							if(count($floor_group_samf_arr>0))
							{
							?>
                            <td align="right" title=""><? echo number_format($samf_rmg_floor_tot,0); ?></td>
                            <?
							}
							
							$total_floor_ratanpur=$tf_rmg_floor_tot+$gf_rmg_floor_tot+$sf_rmg_floor_tot+$ff_rmg_floor_tot+$samf_rmg_floor_tot+$gf5_rmg_floor_tot+$tc_rmg_floor_tot;
							?>
                            
				            <td align="right" title=""><? echo number_format($total_floor_ratanpur,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floor_group_ff_arr as $floor_id => $val)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><?  echo number_format($ff_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$ff_rmg_floor_total[$floor_id]; 
	            }
	            ?>
  				<th><? echo number_format($gr_rmg_floor_tot,0); ?></th>
				 <?
				 $sf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_sf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($sf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$sf_gr_rmg_floor_tot+=$sf_rmg_floor_total[$floor_id]; 
	            }
	            ?>
                <th><? echo number_format($sf_gr_rmg_floor_tot,0); ?></th>
                 <?
				 $gf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf_gr_rmg_floor_tot+=$gf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf_arr>0))
				{
	            ?>
                <th><? echo number_format($gf_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				 $gf5_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf5_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf5_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf5_gr_rmg_floor_tot+=$gf5_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf5_arr>0))
				{
	            ?>
                <th><? echo number_format($gf5_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				//floor_group_training_arr
				 $tc_gr_rmg_floor_tot=0;
	            foreach ($floor_group_training_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tc_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$tc_gr_rmg_floor_tot+=$tc_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_training_arr>0))
				{
	            ?>
                <th><? echo number_format($tc_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				//floor_group_training_arr
				
				 $gr_tf_floor_tot=0;
	            foreach ($floor_group_tf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_tf_floor_tot+=$tf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_tf_floor_tot,0); ?></th>
                 <?
				}
				 $gr_samf_floor_tot=0;
	            foreach ($floor_group_samf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($samf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_samf_floor_tot+=$samf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_samf_floor_tot,0); ?></th>
                 <?
				}
	            ?>
                <th><? 
				//echo $gr_tf_floor_tot.'='.$gf_gr_rmg_floor_tot.'='.$sf_gr_rmg_floor_tot.'='.$gr_rmg_floor_tot.'='.$gr_samf_floor_tot;
				echo number_format($gr_tf_floor_tot+$gf_gr_rmg_floor_tot+$sf_gr_rmg_floor_tot+$gr_rmg_floor_tot+$gr_samf_floor_tot+$gf5_gr_rmg_floor_tot+$tc_gr_rmg_floor_tot,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}

if($action=="report_generate_by_month_jm_rmg") //Daily JM of RMG
{ 
 
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$year 	= str_replace("'","",$month_year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	if($cbo_company_id==1 || $cbo_company_id==3 || $cbo_company_id==4)
	{
		$cbo_company_id="2"; //KAL
	}
	else if($cbo_company_id==2 || $cbo_company_id==5)
	{
		 $cbo_company_id="1";//JM
	}
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==7) //1st floor
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==9) //2nd 
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==12)//5th //test-Fid=49
			{
		 	$floor_group_gf5_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==11)//4th //test-Fid=49
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==57) //  Sample Cut
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==56) //Sample Intimate   
			{
			$floor_group_samf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==59) //Training Center   
			{
			$floor_group_training_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	if($prod_reso_allocation==1)
	{
	 		 $sql_fin_prod=" SELECT a.sewing_line,c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0    order by a.floor_id";
	}
	else
	{
			$sql_fin_prod=" SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)   and a.floor_id is not null and a.floor_id <> 0   order by a.floor_id";
	}
	//echo $prod_reso_allocation;

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	 /*$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('sew_smv')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}*/
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);


	if($prod_reso_allocation==1)
	{
	  $sql_sub_sewOut=" SELECT c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year'  and a.status_active=1 and a.production_type=2   order by a.floor_id";
	}
	else
	{
		$sql_sub_sewOut=" SELECT a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.production_type=2   order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$tot_sew_out_qty=0;
	foreach ($sql_fin_prod_res as $val) 
	{	
		 
			$fyear=$val[csf("month_year")];
			//echo $val[csf("sew_out")].'<br> ';
			$sew_out_qty=$val[csf("sew_out")];
		
			$main_array[$fyear]['qty']+=$val[csf("sew_out")];//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		//	echo $sew_smv.'='.$sew_smv.'X';
			$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				$line_number=array_unique(explode(",",$val[csf('line_number')]));
			  //echo $line_number.'d,';
			  $ff_floor_id=$val[csf('floor_id')];
			  $tot_sew_out_qty=0;
			foreach($floor_group_ff_arr as $flr_grop1=>$val1) //1st Floor
			{
				$flr_grop_ex=explode("_",$flr_grop1);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						//echo $fyear.'d';
						//echo $sew_smv.'='.$sew_out_qty.'='.$cost_per_minute.'='.$exch_rate.'<br>';
							
						foreach( $line_number as $lineId)
						{
							//echo $lineId.'='.$sew_smv.'='.$sew_out_qty.'='.$cost_per_minute.'/'.$exch_rate.'<br> ';
						$year_floor_array[$fyear][$flr_grop1][$lineId]['finishing'] += $finish_cost;
						$tot_sew_out_qty+=$exch_rate;
						}
						
					}
				}
			} //Floor End
			
			foreach($floor_group_sf_arr as $flr_grop2=>$val2) //1st Floor
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
					//if($sew_out_qty=="") $sew_out_qty=0;else $sew_out_qty=$sew_out_qty;
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array2[$fyear][$flr_grop2][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
			foreach($floor_group_gf_arr as $flr_grop3=>$val3) //1st Floor
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array3[$fyear][$flr_grop3][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
			//5th  floor_group_training_arr
			foreach($floor_group_gf5_arr as $flr_grop5=>$val5) //1st Floor
			{
				$flr_grop_ex5=explode("_",$flr_grop5);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex5[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array5[$fyear][$flr_grop5][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor 5th End
			
			//5th  floor_group_training_arr
			foreach($floor_group_training_arr as $flr_grop9=>$val9) //1st Floor
			{
				$flr_grop_ex9=explode("_",$flr_grop9);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex9[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_arrayTraining[$fyear][$flr_grop9][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor Traininig End
			
			foreach($floor_group_tf_arr as $flr_grop4=>$val4) //4th Floor
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array4[$fyear][$flr_grop4][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
			foreach($floor_group_samf_arr as $flr_grop6=>$val6) //Sample Floor
			{
				$flr_grop_ex6=explode("_",$flr_grop6);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex6[0]==$ff_floor_id) 
				{
					if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						$finish_cost=($sew_smv*$sew_out_qty*$cost_per_minute)/$exch_rate;
						foreach( $line_number as $lineId)
						{
						$year_floor_array6[$fyear][$flr_grop6][$lineId]['finishing'] += $finish_cost;
						}
					}
				}
			} //Floor End
		
	}
	//echo $tot_sew_out_qty.'=B';;
	// print_r($year_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$fyear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."=".$val[csf('production_qnty')].'/'.$rate."<br>";
			$sub_ff_floor_id=$val[csf('floor_id')];
		   $line_number=array_unique(explode(",",$val[csf('line_number')]));
			foreach($floor_group_ff_arr as $s_flr_grop=>$val1)//1st 1
			{
				$flr_grop_ex=explode("_",$s_flr_grop);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					 {
					$subCon_year_floor_sewOut_array[$fyear][$s_flr_grop][$lineId]['subSew'] += $subSewOut_costUSD;
					 }
					}
				}
			}//flr Group End
			foreach($floor_group_sf_arr as $flr_grop2=>$val2)//1st 1
			{
				$flr_grop_ex2=explode("_",$flr_grop2);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex2[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					 {
					$subCon_year_floor_sewOut_array2[$fyear][$flr_grop2][$lineId]['subSew'] += $subSewOut_costUSD;
					 }
					}
				}
			}//flr Group End
			foreach($floor_group_gf_arr as $flr_grop3=>$val3)//1st 1
			{
				$flr_grop_ex3=explode("_",$flr_grop3);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex3[0]==$sub_ff_floor_id) 
				{
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;	
					foreach( $line_number as $lineId)
					 {
					$subCon_year_floor_sewOut_array3[$fyear][$flr_grop3][$lineId]['subSew'] += $subSewOut_costUSD;
					 }
					}
				}
			}//flr Group End
			foreach($floor_group_tf_arr as $flr_grop4=>$val4)//1st 1
			{
				$flr_grop_ex4=explode("_",$flr_grop4);
				//echo $flr_grop_ex[0].'='.$ff_floor_id.'='.$flr_grop1.', ';
				if($flr_grop_ex4[0]==$sub_ff_floor_id) 
				{
				if($subsewOut_cost>0)
				{
				$subSewOut_costUSD = $subsewOut_cost/$rate;	
				$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
				$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
				foreach( $line_number as $lineId)
				 {	
				 $subCon_year_floor_sewOut_array4[$fyear][$flr_grop4][$lineId]['subSew'] += $subSewOut_costUSD;
				 }
				}
				}
			}//flr Group End
			
	}
//	print_r($subCon_year_floor_sewOut_array); //floor_group_samf_arr
	unset($sql_fin_prod_res);
	
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$floor_width=count($floor_group_ff_arr)*70+count($floor_group_sf_arr)*70+count($floor_group_gf_arr)*70+count($floor_group_tf_arr)*70+count($floor_group_samf_arr)*70+count($floor_group_gf5_arr)*70+count($floor_group_training_arr)*70;//floor_group_training_arr
	$tbl_width = 400+$floor_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->
<!--
	    <table width="<? //echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px">Revenue Report </strong></td>
	        </tr>
	    </table>-->
          <br/>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Daily Revenue Report <? echo $year; ?></b></h3>
	    <div id="daily_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	             <th width="60">Date</th>
                 <? 
				 $gmt_year='';
				$gmt_year=date('Y',strtotime($year));
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_ff_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id); 
				
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-1</th>
                  <? 
				   ksort($floor_group_sf_arr);
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_sf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
	            ?>
                 <th width="70">Unit-2</th>
                 
                  <? 
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_gf_arr>0))
				{ 
	            ?>
                 <th width="70">Unit-4</th>
                 
                  <? 
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_gf5_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_gf5_arr>0))
				{ 
	            ?>
                 <th width="70">Unit-5</th>
                 
                  <? 
				} //floor_group_training_arr
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_training_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
					
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_training_arr>0))
				{ 
	            ?>
                 <th width="70">Training Center</th>
                 
                  <? 
				} //floor_group_training_arr
				
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_tf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);;//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">Sample CutSew</th>
                 <?
				}
				 //$floor_group_arr[$val[csf('floor_name')]]
	            foreach ($floor_group_samf_arr as $floor_id => $val) 
	            { //$floor_library[$row[csf('id')]]['floor']
					$ex_floor=explode("_",$floor_id);
	            	?>
	            	<th width="70" title="FloorId=<? echo $floor_id.',LineId='.$val;?>"><?  echo ucfirst($ex_floor[1]);;//ucfirst($floor_library[$ex_floor[0]]['floor']).' '.$ex_floor[1];?></th>
	            	<?
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th width="70">Sample Intimate</th>
                 <?
				}
				 ?>
	        <th width="70">Total</th>
	        </thead>
		        <tbody>   
		        <?
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($days_arr as $year => $val) 
		        	{
		        		$gmt_date=date('d-M',strtotime($year)).'-'.$gmt_year;
						
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        		$fiscal_total = 0;		      	        		
			        	?>     
				         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('trd_<?=$i; ?>','<?=$bgcolor;?>')" id="trd_<?=$i; ?>" style="font-size:12px">
				               <td><? echo date('d-F',strtotime($year));?></td>
                            <?
							$i++;
							$ff_rmg_floor_tot=0;
				            foreach ($floor_group_ff_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=explode(",",$lineId);
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$ff_floor_rmg_line=0;	$sub_ff_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$subSew=$subCon_year_floor_sewOut_array[$year][$floor_id][$lId]['subSew'];
										if($subSew>0) $subSew_d=$subSew;else $subSew_d=0;
										if($year_floor_array[$year][$floor_id][$lId]['finishing']>0)
										{
										//echo $floor_id.',';
										$ff_floor_rmg_line+=$year_floor_array[$year][$floor_id][$lId]['finishing']+$subSew_d;
										}
									$sub_ff_floor_rmg_line+=$subSew_d;
									}
									//ratanpur_floor_kal
								$ff_floor_rmg=$ff_floor_rmg_line;//+$subCon_year_floor_sewOut_array[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_ff_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','rmg_floor_jm',9,'850');" ><?  echo number_format($ff_floor_rmg,0);?></a></td>
				            	<?
								$ff_rmg_floor_total[$floor_id]+=$ff_floor_rmg;
								$ff_rmg_floor_tot+=$ff_floor_rmg;
				            }
							?>
                                 <td align="right" title="All 1st Floor"><? echo number_format($ff_rmg_floor_tot,0); ?></td>
                             <?
							$sf_rmg_floor_tot=0;
				            foreach ($floor_group_sf_arr as $floor_id => $val)
				            {
				            	
									$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$sf_floor_rmg_line=0;$sub_sf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										$sf_floor_rmg_line+=$year_floor_array2[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];;
										$sub_sf_floor_rmg_line+=$subCon_year_floor_sewOut_array2[$year][$floor_id][$lId]['subSew'];
									}
									
								$sf_floor_rmg=$sf_floor_rmg_line;//+$subCon_year_floor_sewOut_array2[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_sf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','rmg_floor_jm',10,'850');" ><?  echo number_format($sf_floor_rmg,0);?></a></td>
				            	<?
								$sf_rmg_floor_total[$floor_id]+=$sf_floor_rmg;
								$sf_rmg_floor_tot+=$sf_floor_rmg;
				            }
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($sf_rmg_floor_tot,0); ?></td>
                             <?
							$gf_rmg_floor_tot=0;
				            foreach ($floor_group_gf_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$gf_floor_rmg_line=0;$sub_gf_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										
										$gf_floor_rmg_line+=$year_floor_array3[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
										$sub_gf_floor_rmg_line+=$subCon_year_floor_sewOut_array3[$year][$floor_id][$lId]['subSew'];
									}
									
								$gf_floor_rmg=$gf_floor_rmg_line;//+$subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_gf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','rmg_floor_jm',9,'850');" ><?  echo number_format($gf_floor_rmg,0);?></a></td>
				            	<?
								$gf_rmg_floor_total[$floor_id]+=$gf_floor_rmg;
								$gf_rmg_floor_tot+=$gf_floor_rmg;
				            }
							if(count($floor_group_gf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($gf_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   $gf5_rmg_floor_tot=0;
				            foreach ($floor_group_gf5_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$gf5_floor_rmg_line=0;$sub_gf5_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										
										$gf5_floor_rmg_line+=$year_floor_array5[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
										$sub_gf5_floor_rmg_line+=$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
									}
									
								$gf5_floor_rmg=$gf5_floor_rmg_line;//+$subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_gf5_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','rmg_floor_jm',9,'850');" ><?  echo number_format($gf5_floor_rmg,0);?></a></td>
				            	<?
								$gf5_rmg_floor_total[$floor_id]+=$gf5_floor_rmg;
								$gf5_rmg_floor_tot+=$gf5_floor_rmg;
				            }
							if(count($floor_group_gf5_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($gf5_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   //Training 
						    $tc_rmg_floor_tot=0;
				            foreach ($floor_group_training_arr as $floor_id => $val)
				            {
				            		$lineId=rtrim($val,',');
									$lineIds=array_unique(explode(",",$lineId));
									$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
									$tc_floor_rmg_line=0;$sub_gf5_floor_rmg_line=0;
									foreach($lineIds as $lId)
									{
										
										$tc_floor_rmg_line+=$year_floor_arrayTraining[$year][$floor_id][$lId]['finishing'];
										//+$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew']
										//$sub_gf5_floor_rmg_line+=$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
									}
									
								//$gf5_floor_rmg=$tc_floor_rmg_line;//+$subCon_year_floor_sewOut_array3[$year][$floor_id]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_gf5_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$lineIds_all; ?>','rmg_floor_jm',9,'850');" ><?  echo number_format($tc_floor_rmg_line,0);?></a></td>
				            	<?
								$tc_rmg_floor_total[$floor_id]+=$tc_floor_rmg_line;
								$tc_rmg_floor_tot+=$tc_floor_rmg_line;
				            }
							if(count($floor_group_training_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($tc_rmg_floor_tot,0); ?></td>
                             <?
					       }
						   //5th end
						   
							$tf_rmg_floor_tot=0;
				            foreach ($floor_group_tf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
								$tf_floor_rmg_line=0;$sub_tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$tf_floor_rmg_line+=$year_floor_array4[$year][$floor_id][$lId]['finishing']+$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
									$sub_tf_floor_rmg_line+=$subCon_year_floor_sewOut_array4[$year][$floor_id][$lId]['subSew'];
								}
									
								$tf_floor_rmg=$tf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_tf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$$lineIds_all=implode(",",array_unique(explode(",",$lineId)));; ?>','rmg_floor_jm',9,'850');" ><?  echo number_format($tf_floor_rmg,0);?></a></td>
				            	<?
								$tf_rmg_floor_total[$floor_id]+=$tf_floor_rmg;
								$tf_rmg_floor_tot+=$tf_floor_rmg;
				            }
							if(count($floor_group_tf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($tf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$samf_rmg_floor_tot=0;
				            foreach ($floor_group_samf_arr as $floor_id => $val)
				            {
				            	$lineId=rtrim($val,',');
								$lineIds=array_unique(explode(",",$lineId));
								$lineIds_all=implode(",",array_unique(explode(",",$lineId)));
								$samf_floor_rmg_line=0;$sub_tf_floor_rmg_line=0;
								foreach($lineIds as $lId)
								{
									$samf_floor_rmg_line+=$year_floor_array6[$year][$floor_id][$lId]['finishing'];
									//$sub_tf_floor_rmg_line+=$subCon_year_floor_sewOut_array5[$year][$floor_id][$lId]['subSew'];
								}
									
								$samf_floor_rmg=$samf_floor_rmg_line;
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod*SubCOn Order Rate(<? echo $sub_tf_floor_rmg_line;?>)"><a href="##" onClick="fnc_gmt_kal_popup('<? echo $gmt_date.'__'.$cbo_company_id.'__'.$floor_id.'__'.$$lineIds_all=implode(",",array_unique(explode(",",$lineId)));; ?>','rmg_floor_jm',9,'850');" ><?  echo number_format($samf_floor_rmg,0);?></a></td>
				            	<?
								$samf_rmg_floor_total[$floor_id]+=$samf_floor_rmg;
								$samf_rmg_floor_tot+=$samf_floor_rmg;
				            }
							if(count($floor_group_samf_arr>0))
							{
							?>
                            <td align="right" title="Total gmt+Total Textile"><? echo number_format($samf_rmg_floor_tot,0); ?></td>
                            <?
							}
							$total_floor_ratanpur=$tf_rmg_floor_tot+$gf_rmg_floor_tot+$sf_rmg_floor_tot+$ff_rmg_floor_tot+$samf_rmg_floor_tot+$gf5_rmg_floor_tot+$tc_rmg_floor_tot;
							?>
                            
				            <td align="right" title="Total gmt+Total Textile"><? echo number_format($total_floor_ratanpur,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	           <th align="right">Total</th>
	             <?
				 $gr_rmg_floor_tot=0;
	            foreach ($floor_group_ff_arr as $floor_id => $val)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><?  echo number_format($ff_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_rmg_floor_tot+=$ff_rmg_floor_total[$floor_id]; 
	            }
	            ?>
  				<th><? echo number_format($gr_rmg_floor_tot,0); ?></th>
				 <?
				 $sf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_sf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($sf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$sf_gr_rmg_floor_tot+=$sf_rmg_floor_total[$floor_id]; 
	            }
	            ?>
                <th><? echo number_format($sf_gr_rmg_floor_tot,0); ?></th>
                 <?
				 $gf_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf_gr_rmg_floor_tot+=$gf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf_arr>0))
				{
	            ?>
                <th><? echo number_format($gf_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}
				 $gf5_gr_rmg_floor_tot=0;
	            foreach ($floor_group_gf5_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($gf5_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gf5_gr_rmg_floor_tot+=$gf5_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_gf5_arr>0))
				{
	            ?>
                <th><? echo number_format($gf5_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}//
				 $tc_gr_rmg_floor_tot=0;
	            foreach ($floor_group_training_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tc_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$tc_gr_rmg_floor_tot+=$tc_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_training_arr>0))
				{
	            ?>
                <th><? echo number_format($tc_gr_rmg_floor_tot,0); ?></th>
                
                 <?
				}//floor_group_training_arr
				
				 $gr_tf_floor_tot=0;
	            foreach ($floor_group_tf_arr as $floor_id => $val)
	            {
	            	?>
	            	<th><?  echo number_format($tf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_tf_floor_tot+=$tf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_tf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_tf_floor_tot,0); ?></th>
                 <?
				}
				 $gr_samf_floor_tot=0;
	            foreach ($floor_group_samf_arr as $floor_id => $val)
	            {
	            	?>

	            	<th><?  echo number_format($samf_rmg_floor_total[$floor_id],0);?></th>
	            	<?
					$gr_samf_floor_tot+=$samf_rmg_floor_total[$floor_id]; 
	            }
				if(count($floor_group_samf_arr>0))
				{
	            ?>
                 <th><? echo number_format($gr_samf_floor_tot,0); ?></th>
                 <?
				}
				
	            ?>
              <th><? echo number_format($gr_tf_floor_tot+$gf_gr_rmg_floor_tot+$sf_gr_rmg_floor_tot+$gr_rmg_floor_tot+$gr_samf_floor_tot+$gf5_gr_rmg_floor_tot+$tc_gr_rmg_floor_tot,0); ?></th>
                
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}
//POpUp Start here
if($action=="rmg_floor_jm") // JM RMG
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("__",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_data=$ex_dara[2];
		$line_id=$ex_dara[3];
		$floor_ex_data=explode("_",$floor_data);
		$floor_id=$floor_ex_data[0];
		$floor_grp=$floor_ex_data[1];
		$type_id=$type;
		//echo $line_id.'='.$floor_grp;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
		foreach($sql_floor as $row )
		{
			$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
			$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
		}
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null and a.floor_name=$floor_id and a.sewing_group='$floor_grp' and a.id in($line_id) order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null and a.floor_name=$floor_id and a.sewing_group='$floor_grp' and a.id in($line_id) order by  a.id asc";
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$line_name_arr[$val[csf('id')]]=$val[csf('line_name')];
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==7)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==9)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==41)
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==12)
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	if($prod_reso_allocation==1)
	{
	   $sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line,c.line_number,a.item_number_id as item_id,a.floor_id,a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id   order by c.line_number";
	}
	else
	{
			$sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line as line_number,a.item_number_id as item_id,a.floor_id,a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)    and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.grouping as ref_no,b.po_quantity,b.pub_shipment_date,b.shipment_date,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		$po_ref_array[$val[csf('id')]]['ref_no']= $val[csf('ref_no')];
		$po_ref_array[$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	} 
	$line_chk=rtrim($line_id,',');
	$line_chk_arr=explode(",",$line_chk);
	foreach ($sql_fin_prod_res as $val)  //Main Query
	{
			$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('msew_out')];
			$po_buyer=$po_buyer_array[$val[csf('po_break_down_id')]];
			$ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			//$line_number=$val[csf('line_number')];
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			//echo $cm_cost_method_based_on.'d';
			//print_r($line_numberArr);
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				//echo $sew_smv.'='.$val[csf('sew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
					$sew_out_cost=($sew_smv*$val[csf('sew_out')]*$cost_per_minute)/$exch_rate;
					//$year_location_qty_array[$fyear][$val[csf('location')]]['finishing'] += $finish_cost;
					foreach($line_numberArr as $lId)
					{
						//echo $lId.',A ';
						if(in_array($lId,$line_chk_arr))
						{
							//echo $lId.'B,';
						//echo $sew_out_cost.'d';
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_out']+= $val[csf('sew_out')];
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['revenue']+= $sew_out_cost;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['style_ref_no']= $style_ref_no;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_smv']= $sew_smv;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
						}
					}
			}
	}
	ksort($line_wise_array);
	//print_r($line_wise_array);
	
	// =================================== subcon kniting =============================
	  $width_td="780";
	   ?>
       <div style="margin-left:20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Garments Revenue-Sub Floor Wise	 </b> </caption>
       <tr>
        
           <td  width="100"> <b>Floor</b> </td>
           <td  width="70"><?  echo $floor_grp;?></td>
           <td width="390" colspan="6"><b style="float:right"> Date: </b></td>
           <td width="100"> &nbsp; <? echo $gmt_date;?></td>
            
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Own Job </b> </caption>
		<thead>
			<th width="70">Line no</th>
            <th width="100">Buyer</th>
			<th width="70">Ref No</th>
			<th width="150">Style Name</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">SMV</th>
			<th width="70">Produce Minute</th>
            <th width="40">CPM($)</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($line_wise_array as $line_id=>$line_data)
			{
				$line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $ref_no=>$ref_data)
			  {
				  foreach ($ref_data as $item_id=>$row)
				  {
					  $line_row_span++;
				  }
			 	 $line_row_arr[$line_id]=$line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$buyer_tot_gmt_revenue=$total_gmt_prod_min=$total_gmt_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($line_wise_array as $line_id=>$line_data)
			{
			 $b=1;
			 foreach ($line_data  as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data  as $ref_no=>$ref_data)
			  {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                   if($b==1)
					{
					?>
					<td width="70" rowspan="<? echo  $line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                    }?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
					<td width="70"><p>&nbsp;<? echo $ref_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['sew_out']; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row['sew_smv']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*SMV"><? $produce_min=$row['sew_out']*$row['sew_smv'];echo $produce_min;; ?></td>
                    <td width="40"><p><? echo number_format($row['cost_per_minute']/$exch_rate,2); ?>&nbsp;</p></td>
                    <td width="" align="right" title="Produce Min*CPM/Exchange Rae"><p><? 
					$tot_revenue=$row['revenue'];//($produce_min*$row['revenue'])/$exch_rate;
					echo number_format($tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$buyer_tot_gmt_revenue+=$tot_revenue;
				$buyer_tot_gmt_prod+=$row['sew_out'];
				$buyer_tot_gmt_prod_min+=$produce_min;
				
				$total_gmt_revenue+=$tot_revenue;
				$total_gmt_prod+=$row['sew_out'];
				$total_gmt_prod_min+=$produce_min;
			  }
			 }
			 }
			}
			?>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"></td>
                <td width="100"><p>Grand Total </p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod_min,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        <?
      if($prod_reso_allocation==1)
		{
		  $sql_sub_sewOut=" SELECT a.gmts_item_id,a.order_id,c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2    and a.floor_id=$floor_id order by a.floor_id";
		  // $sql_sub_sewOut="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=$location_id and a.status_active=1 ";
		}
		else
		{
			$sql_sub_sewOut=" SELECT a.gmts_item_id,a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2   order by a.floor_id";
		}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut2="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name,b.rate from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2  and a.status_active=1  and a.floor_id=$floor_id ";
	$sql_sub_sewOut_result2 = sql_select($sql_sub_sewOut2);
	foreach($sql_sub_sewOut_result2 as $val)//subcon_job,job_no_mst 
	{
		$sub_po_arr[$val[csf("order_id")]]['order_no']=$val[csf("order_no")];
		$sub_po_arr[$val[csf("order_id")]]['buyer_name']=$val[csf("buyer_name")];
		$sub_po_arr[$val[csf("order_id")]]['style_ref_no']=$val[csf("style_ref_no")];
		$sub_po_arr[$val[csf("order_id")]]['rate']=$val[csf("rate")];
	}
	//print_r($sub_po_arr);
	foreach($sql_sub_sewOut_result as $val)//subcon_job,job_no_mst
	{
			
			$fyear=$val[csf("month_year")];
			$order_no=$sub_po_arr[$val[csf("order_id")]]['order_no'];
			$style_ref_no=$sub_po_arr[$val[csf("style_ref_no")]]['style_ref_no'];
			$buyer_id=$sub_po_arr[$val[csf("order_id")]]['buyer_name'];
			$order_rate=$sub_po_arr[$val[csf("order_id")]]['rate'];	
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			//echo $order_rate.', ';		
			$subsewOut_cost =$order_rate*$val[csf('production_qnty')];
			if($subsewOut_cost>0)
			{
				foreach($line_numberArr as $lId)
					{
					if(in_array($lId,$line_chk_arr))
					{
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['production_qnty']+= $val[csf('production_qnty')];
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['revenue']+= $subsewOut_cost/$exch_rate;
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['style_ref_no']= $style_ref_no;
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['rate']= $order_rate;
					}
				 }
			}
	 }
	 ksort($sub_line_wise_array);
	// print_r($sub_line_wise_array);
	
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Subcontract Job </b> </caption>
		<thead>
			<th width="70">Line No</th>
            <th width="100">Factory Name</th>
			<th width="70">Order No</th>
			<th width="150">Customer Style</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">Pcs Rate<br>(Taka)</th>
			<th width="70">Total Taka</th>
            <th width="40">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
				$sub_line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				  foreach ($po_data as $item_id=>$row)
				  {
					  $sub_line_row_span++;
				  }
				  $sub_line_row_arr[$line_id]=$sub_line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$sub_total_gmt_revenue=$sub_total_gmt_prod=$sub_total_gmt_tk=0;
            $i=1;$k=1;$subcon_group_by_arr=array();
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
			$sb=1;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				 foreach ($po_data as $item_id=>$row)
			     {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	//line_name_arr
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 <?
                  if($sb==1)
					{
					?>
					<td width="70" rowspan="<? echo  $sub_line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                   }
					?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                   
					<td width="70"><p>&nbsp;<? echo $po_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row['production_qnty'],0); ?>&nbsp;</p></td>
					<td width="40" align="right"><p><? echo $row['rate']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*Order Rate"><? $tot_tk=$row['production_qnty']*$row['rate'];echo number_format($tot_tk,0); ?></td>
                    <td width="40" align="right"><p><? echo $exch_rate; ?>&nbsp;</p></td>
                    <td width="" align="right" title="Tot Tk/Exchange Rae"><p><? 
					$sub_tot_revenue=$row['revenue'];//$tot_tk/$exch_rate;// $sub_tot_revenue=$tot_tk/$exch_rate;
					echo number_format($sub_tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$sb++;
				
				$sub_buyer_tot_gmt_revenue+=$sub_tot_revenue;
				$sub_buyer_tot_gmt_tk+=$tot_tk;
				$sub_buyer_tot_prod+=$row['production_qnty'];
				
				$sub_total_gmt_revenue+=$sub_tot_revenue;
				$sub_total_gmt_prod+=$row['production_qnty'];
				$sub_total_gmt_tk+=$tot_tk;
			   }
			  }
			  
			 }
			}
			?>
            
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p> </p></td>
                <td width="100"><p>Grand Total</td>
                <td width="70"><p><? echo number_format($sub_total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo  number_format($sub_total_gmt_tk,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
    </div>
       <?
	
exit();
}



if($action=="gmt_location_kal") // Gmt Kal Sewing
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$location_id=$ex_dara[2];
		$type_id=$type;
		//echo $location_id;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	//$sql_fin_prod="SELECT a.location,a.po_break_down_id";
	
	   $sql_fin_prod="SELECT a.location,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,c.grouping as ref_no,d.buyer_name,d.style_ref_no,
	 (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS msew_out
	  from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and c.id=a.po_break_down_id and d.job_no=c.job_no_mst and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.production_type in(5) and a.production_source in(1)  and a.location <> 0 and a.location=$location_id  and a.status_active=1  and b.status_active=1 order by d.buyer_name asc";
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	 $cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv']= $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	} 
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	
	foreach ($sql_fin_prod_res as $val)  //Main Query
	{
		
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('msew_out')];
		
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			//echo $cm_cost_method_based_on.'d';
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				 //echo $cost_per_minute."<br>";
				//if($val[csf($myear)]=="") $val[csf($myear)]=0;else $val[csf($myear)]=$val[csf($myear)];
				//echo $sew_smv.'='.$val[csf('msew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
					$sew_out_cost=($sew_smv*$val[csf('msew_out')]*$cost_per_minute)/$exch_rate;
					//$year_location_qty_array[$fyear][$val[csf('location')]]['finishing'] += $finish_cost;
				
			}
			$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['msew_out']+= $val[csf('msew_out')];
			$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['style_ref_no']= $val[csf('style_ref_no')];
			$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['fin_revenue']= $sew_out_cost;
			$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['sew_smv']= $sew_smv;
			$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
		
	}
	//print_r($sewing_qty_array);
	//asort($sewing_qty_array);
	
	
	// =================================== subcon kniting =============================
	  $width_td="710";
	   ?>
       <div style="margin-left:50px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Garments Revenue - Location wise </b> </caption>
       <tr>
        
           <td  width="100"> <b>Location</b> </td>
           <td  width="70"><?  echo $location_arr[$location_id]?></td>
           <td width="390" colspan="6"><b style="float:right"> Date: </b></td>
           <td width="100"> &nbsp; <? echo $gmt_date;?></td>
            
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Own Job </b> </caption>
		<thead>
			<th width="100">Buyer</th>
			<th width="70">Ref No</th>
			<th width="150">Style Name</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">SMV</th>
			<th width="70">Produce Minute</th>
            <th width="40">CPM($)</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($sewing_qty_array as $buyer_id=>$buyer_data)
			{
				$buyer_row_span=0;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  $buyer_row_span++;
			  }
			  $buyer_row_arr[$buyer_id]=$buyer_row_span;
			 }
			}
			//print_r($buyer_row_arr);
			$buyer_tot_gmt_revenue=$total_gmt_prod_min=$total_gmt_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($sewing_qty_array as $buyer_id=>$buyer_data)
			{
				$b=1;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
				  if (!in_array($buyer_id,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="320" colspan="3"  align="right"> Buyer Total</td>
                              
                                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo number_format($buyer_tot_gmt_prod,0); ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo number_format($buyer_tot_gmt_prod_min,0);; ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width=""><p><? echo number_format($buyer_tot_gmt_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($buyer_tot_gmt_revenue);unset($buyer_tot_gmt_prod);unset($buyer_tot_gmt_prod_min);
							}
							$sub_group_by_arr[]=$buyer_id;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                  if($b==1)
					{
					?>
					<td width="100" valign="middle" rowspan="<? echo  $buyer_row_arr[$buyer_id];?>" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                    <?
                   }?>
					<td width="70" title="<? echo $buyer_id;?>"><p>&nbsp;<? echo $ref_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['msew_out']; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row['sew_smv']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*SMV"><? $produce_min=$row['msew_out']*$row['sew_smv'];echo $produce_min;; ?></td>
                    <td width="40"><p><? echo number_format($row['cost_per_minute']/$exch_rate,2); ?>&nbsp;</p></td>
                    <td width="" align="right" title="Produce Min*CPM/Exchange Rae"><p><? 
					$tot_revenue=($produce_min*$row['cost_per_minute'])/$exch_rate;
					echo number_format($tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$buyer_tot_gmt_revenue+=$tot_revenue;
				$buyer_tot_gmt_prod+=$row['msew_out'];
				$buyer_tot_gmt_prod_min+=$produce_min;
				
				$total_gmt_revenue+=$tot_revenue;
				$total_gmt_prod+=$row['msew_out'];
				$total_gmt_prod_min+=$produce_min;
				
				
			  }
			  
			 }
			
            
			 
			}
			?>
             <tr class="tbl_bottom">
            <td width="320" colspan="3"  align="right"> Buyer Total</td>
          
            <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
            <td width="70"><p><? echo number_format($buyer_tot_gmt_prod,0); ?>&nbsp;</p></td>
            <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
            <td width="70"><p><? echo number_format($buyer_tot_gmt_prod_min,0); ?>&nbsp;</p></td>
            <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
            <td width=""><p><? echo number_format($buyer_tot_gmt_revenue,0); ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p>Grand Total </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod_min,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        <?
       // $order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=$location_id and a.status_active=1 order by c.party_id asc";
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	foreach($sql_sub_sewOut_result as $val)//subcon_job,job_no_mst
	{
			//$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			$sub_sewing_qty_array[$val[csf('buyer_name')]][$val[csf('order_id')]][$val[csf('item_id')]]['production_qnty']+= $val[csf('production_qnty')];
			$sub_sewing_qty_array[$val[csf('buyer_name')]][$val[csf('order_id')]][$val[csf('item_id')]]['style_ref_no']= $val[csf('style_ref_no')];
			$sub_sewing_qty_array[$val[csf('buyer_name')]][$val[csf('order_id')]][$val[csf('item_id')]]['rate']= $val[csf('rate')];
			$sub_sewing_qty_array[$val[csf('buyer_name')]][$val[csf('order_id')]][$val[csf('item_id')]]['order_no']= $val[csf('order_no')];
			$sub_sewing_qty_array[$val[csf('buyer_name')]][$val[csf('order_id')]][$val[csf('item_id')]]['subcon']=100;
			//$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['sew_smv']= $sew_smv;
			//$sewing_qty_array[$val[csf('buyer_name')]][$val[csf('ref_no')]][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
	}
//	asort($sub_sewing_qty_array);
	
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Subcontract Job </b> </caption>
		<thead>
			<th width="100">Factory Name</th>
			<th width="70">Order No</th>
			<th width="150">Customer Style</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">Pcs Rate<br>(Taka)</th>
			<th width="70">Total Taka</th>
            <th width="40">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($sub_sewing_qty_array as $buyer_id=>$buyer_data)
			{
				$buyer_row_span=0;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  $buyer_row_span++;
			  }
			  $buyer_row_arr[$buyer_id]=$buyer_row_span;
			 }
			}
			//print_r($buyer_row_arr);
			$sub_total_gmt_revenue=$sub_total_gmt_prod=$sub_total_gmt_tk=0;
            $i=1;$k=1;$subcon_group_by_arr=array();
			foreach ($sub_sewing_qty_array as $buyer_id=>$buyer_data)
			{
				$sb=1;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
				  if (!in_array($buyer_id,$subcon_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="320" colspan="3"  align="right"> Buyer Total</td>
                              
                                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo number_format($sub_buyer_tot_prod,0); ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="70"><p><? echo $number_format($sub_buyer_tot_gmt_tk,0); ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width=""><p><? echo number_format($sub_buyer_tot_gmt_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_buyer_tot_gmt_revenue);unset($sub_buyer_tot_prod);unset($sub_buyer_tot_gmt_tk);
							}
							$subcon_group_by_arr[]=$buyer_id;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                  if($sb==1)
					{
						
					?>
					<td width="100" valign="middle" rowspan="<? echo  $buyer_row_arr[$buyer_id];?>" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                    <?
                   }?>
					<td width="70"><p>&nbsp;<? echo $row['order_no']; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row['production_qnty'],0); ?>&nbsp;</p></td>
					<td width="40" align="right"><p><? echo $row['rate']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*Order Rate"><? $tot_tk=$row['production_qnty']*$row['rate'];echo number_format($tot_tk,0); ?></td>
                    <td width="40" align="right"><p><? echo $exch_rate; ?>&nbsp;</p></td>
                    <td width="" align="right" title="Tot Tk/Exchange Rae"><p><? 
					$sub_tot_revenue=$tot_tk/$exch_rate;
					echo number_format($sub_tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$sb++;
				
				$sub_buyer_tot_gmt_revenue+=$sub_tot_revenue;
				$sub_buyer_tot_gmt_tk+=$tot_tk;
				$sub_buyer_tot_prod+=$row['production_qnty'];
				
				$sub_total_gmt_revenue+=$sub_tot_revenue;
				$sub_total_gmt_prod+=$row['production_qnty'];
				$sub_total_gmt_tk+=$tot_tk;
				
			  }
			  
			 }
			
            
			 
			}
			?>
             <tr class="tbl_bottom">
            <td width="320" colspan="3"  align="right"> Buyer Total</td>
          
            <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
            <td width="70"><p><? echo number_format($sub_buyer_tot_prod,0); ?>&nbsp;</p></td>
            <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
            <td width="70"><p><? echo number_format($sub_buyer_tot_gmt_tk,0); ?>&nbsp;</p></td>
            <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
            <td width=""><p><? echo number_format($sub_buyer_tot_gmt_revenue,0); ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p>Grand Total </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($sub_total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo  number_format($sub_total_gmt_tk,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
    </div>
       <?
	
exit();
}
if($action=="dying_floor_kal") //
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		$type_id=$type;
		//echo $location_id;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
		
		$sql_mc="select id, machine_no,machine_group,dia_width,prod_capacity from  lib_machine_name where floor_id=$floor_id and status_active=1";
		$sql_mc_res = sql_select($sql_mc);
		foreach ($sql_mc_res as $val) 
		{
			$machine_no_arr[$val[csf('id')]]['mc_no'] = $val[csf('machine_no')];
			$machine_no_arr[$val[csf('id')]]['mc_group'] = $val[csf('machine_group')];
			$machine_no_arr[$val[csf('id')]]['dia'] = $val[csf('dia_width')];
			$machine_no_arr[$val[csf('id')]]['prod_capacity'] = $val[csf('prod_capacity')];
		}
	//product_details_master
	   $dying_prod_sql=" SELECT a.id,a.is_sales,a.batch_no,a.process_id,a.color_id,c.floor_id,d.po_id as po_breakdown_id,c.machine_id,c.entry_form,to_char(c.process_end_date,'DD-MON') as month_year,(d.batch_qnty) as batch_qnty,e.prod_capacity,f.detarmination_id as deter_id from  pro_batch_create_mst a,pro_batch_create_dtls d,pro_fab_subprocess c,lib_machine_name e,product_details_master f where a.id=d.mst_id and a.id=c.batch_id and c.batch_id=d.mst_id and e.id=c.machine_id and f.id=d.prod_id   and c.load_unload_id=2 and a.batch_against=1 and c.service_company in($cbo_company_id)  and c.floor_id=$floor_id and c.process_end_date='$gmt_date' and a.status_active=1 and d.status_active=1 and c.status_active=1   order by c.floor_id,c.process_end_date";
	$dying_prod_sql_res = sql_select($dying_prod_sql);
	foreach ($dying_prod_sql_res as $val) 
	{
		$is_sales_id=$val[csf('is_sales')];
		if($is_sales_id==1)
		{
			$sales_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		}
		else
		{
			$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		}
	}
	 $sub_dying_prod_sql=" SELECT a.id,a.batch_no,a.color_id,c.floor_id,d.po_id as po_breakdown_id,c.machine_id,c.entry_form,to_char(c.process_end_date,'DD-MON') as month_year,(d.batch_qnty) as batch_qnty,e.prod_capacity  from  pro_batch_create_mst a,pro_batch_create_dtls d,pro_fab_subprocess c,lib_machine_name e where a.id=d.mst_id and a.id=c.batch_id and c.batch_id=d.mst_id and e.id=c.machine_id and c.load_unload_id=2 and a.batch_against=1 and c.service_company in($cbo_company_id)  and c.floor_id=$floor_id and c.process_end_date='$gmt_date' and a.status_active=1 and d.status_active=1 and c.status_active=1  order by c.floor_id,c.process_end_date";
	$sub_dying_prod_sql_res = sql_select($sub_dying_prod_sql);
	foreach ($sub_dying_prod_sql_res as $val) 
	{
		$sub_po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
	}
	 
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$salesIds = implode(",", array_unique($sales_id_array));
	if($salesIds !="")
	{
		$sales_cond="";
		if(count($sales_id_array)>999)
		{
			$chunk_arr=array_chunk($sales_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sales_cond=="") $sales_cond.=" and ( a.id in ($ids) ";
				else
					$sales_cond.=" or   a.id in ($ids) "; 
			}
			$sales_cond.=") ";

		}
		else
		{
			$sales_cond.=" and a.id in ($salesIds) ";
		}
	} //and a.company_id in($cbo_company_id)
	  $sql_sales = "select a.id,b.id as dtls_id,a.job_no, a.within_group,a.buyer_id,b.color_id,b.determination_id as deter_id,b.process_id,b.process_seq,b.body_part_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $sales_cond order by b.id desc";
		$sql_sales_result = sql_select($sql_sales);
		foreach ($sql_sales_result as $val) 
		{		
				if ($val[csf('within_group')] == 1)
					$buyer = $val[csf('buyer_id')];
				else
					$buyer = $val[csf('buyer_id')];
				$sales_buyer_array[$val[csf('id')]]['buyer'] = $buyer;
				$sales_buyer_array[$val[csf('id')]]['within_group'] = $val[csf('within_group')];
				$sales_job_no_array[$val[csf('id')]]['job_no'] = $val[csf('job_no')];
				if($val[csf('process_seq')])
				{
					$process_id=array_unique(explode(",",$val[csf('process_id')]));
					$process_seqArr=array_unique(explode(",",$val[csf('process_seq')]));
					foreach($process_id as $p_key)
					{
							foreach($process_seqArr as $val_rate)
							{
								$process_Rate=explode("__",$val_rate);
								$process_Id=$process_Rate[0];
								$process_rate=$process_Rate[1];
								if($p_key==$process_Id && $process_rate>0)
								{
								$sales_data_array[$val[csf('id')]][$val[csf('deter_id')]][$val[csf('color_id')]][$p_key]['process_rate'] = $process_rate;
								}
							}
					}
				}
		}
	
	$cm_po_cond = str_replace("id", "e.po_break_down_id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$cm_po_cond3 = str_replace("id", "b.id", $po_cond);
	// $sql_po="SELECT a.buyer_name,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and b.status_active=1 and b.is_deleted=0 $cm_po_cond"; 
	 $sql_po="SELECT a.buyer_name,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no from wo_po_break_down b,wo_po_details_master a where b.job_id=a.id and b.status_active=1 and b.is_deleted=0 $cm_po_cond3"; 
	 $sql_po_result = sql_select($sql_po);
	 foreach ($sql_po_result as $val) 
	{
		$po_array[$val[csf('id')]]['buyer'] = $val[csf('buyer_name')];
		$po_array[$val[csf('id')]]['ref_no'] = $val[csf('ref_no')];
	}
	 
	 /*$sql_po="SELECT a.buyer_name,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no,c.color_number_id as color_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,f.id as conv_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond order by f.id, b.id asc";*/
	 
	   $sql_conv="SELECT e.po_break_down_id as poid ,e.color_number_id as color_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,f.id as conv_id,f.cons_process,f.charge_unit,f.color_break_down from wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where 
	   d.id=e.pre_cost_fabric_cost_dtls_id and d.job_id=e.job_id and d.job_id=f.job_id  and d.id=f.fabric_description  and e.cons !=0  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond order by f.id, e.po_break_down_id asc";
	$sql_conv_result = sql_select($sql_conv);
	foreach ($sql_conv_result as $val) 
	{
		//$po_array[$val[csf('poid')]]['buyer'] = $val[csf('buyer_name')];
		//$po_array[$val[csf('poid')]]['ref_no'] = $val[csf('ref_no')];
		if($val[csf('color_size_sensitive')]==3)
		{
		$pre_fab_array[$val[csf('poid')]][$val[csf('deter_id')]]['sensitive'] = $val[csf('color_size_sensitive')];
		}
		if($val[csf('color_break_down')]!="")
		{
		$color_break_down=$val[csf('color_break_down')];
		}
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			//echo $color_break_down.'='.$val[csf('color_break_down')].', ';
			if($val[csf('color_size_sensitive')]==3) //Contrst
			{
				$po_color_fab_array[$val[csf('poid')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];
				 
			}
			else
			{
				$po_color_fab_array[$val[csf('poid')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];
			}

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			//if($arr_2[3])
			//{
			//echo $val[csf('deter_id')].'='.$arr_2[3].',';
			if($arr_2[1])
			{
			$po_color_fab_array[$val[csf('poid')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			}
			
			//}
			}
		}
		else if($val[csf('cons_process')]==33) //Heatset
		{
			$po_color_fab_array2[$val[csf('poid')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$val[csf('charge_unit')];
		}
		else
		{
			$po_color_fab_array[$val[csf('poid')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			//echo $val[csf('cons_process')].'='.$val[csf('color_id')].'='.$val[csf('deter_id')].'='.$val[csf('charge_unit')].'<br>';
		}
		
	}
	//print_r($po_color_fab_array);
	/*$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);
	$conversion= new conversion($condition);
	//echo $conversion->getQuery();die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	*/
	
	
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	 $sql_sub_order="SELECT b.id as order_id,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_ord_dtls b,subcon_ord_mst c  where   b.job_no_mst=c.subcon_job  and c.status_active=1 and b.status_active=1 ";
	 $sql_sub_order_res = sql_select($sql_sub_order);
	 foreach ($sql_sub_order_res as $val)  //Main Query
	{
		$sub_po_no_arr[$val[csf('order_id')]]['style_ref_no']=$val[csf('style_ref_no')];
		$sub_po_no_arr[$val[csf('order_id')]]['order_no']=$val[csf('order_no')];
		$sub_po_no_arr[$val[csf('order_id')]]['buyer_name']=$val[csf('buyer_name')];
		$sub_po_no_arr[$val[csf('order_id')]]['rate']=$val[csf('rate')];
	}
	 
	
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	 $process_array=array(1,30,35);
	$conv_dyeing_cost_tot=0;$tot_salesAmt=0;
	$dying_prod_array=array();
	foreach ($dying_prod_sql_res as $val)  //Main Query
	{
			$entry_formId=$val[csf('entry_form')];
			$po_id=$val[csf('po_breakdown_id')];
			$color_id=$val[csf('color_id')];
			$is_sales_id=$val[csf('is_sales')];
			$process_arr=explode(",",$val[csf('process_id')]);
			
			$mc_group=$machine_no_arr[$val[csf('machine_id')]]['mc_group'];
			$mc_no=$machine_no_arr[$val[csf('machine_id')]]['mc_no'];
			$prod_capacity=$machine_no_arr[$val[csf('machine_id')]]['prod_capacity'];
			
			$within_group=$sales_buyer_array[$po_id]['within_group'];
			if($is_sales_id==1)
			{
			$sales_buyer=$sales_buyer_array[$po_id]['buyer'];
			//echo 	$within_group.'dd'.$sales_buyer;
			$ref_no=$sales_job_no_array[$po_id]['job_no'];
			$buyerId=$sales_buyer;
			}
			else
			{
				$buyerId=$po_array[$val[csf('po_breakdown_id')]]['buyer'];
				$ref_no=$po_array[$val[csf('po_breakdown_id')]]['ref_no'];
				
			}
			if($is_sales_id!=1)
			{
				//$sensitive_id=$pre_fab_array[$val[csf('po_breakdown_id')]][$val[csf('deter_id')]]['sensitive'];
			}
		//	echo $sensitive_id.'='.$is_sales_id.',';
			
			$dyeing_cost=0;$dyeing_qty=0;
			foreach ($conversion_cost_head_array as $key => $value) 
			{
				if($val[csf('po_breakdown_id')]>0)
				{
					if(!in_array($key, $process_array ))
					{
						
						if($is_sales_id!=1)
						{
							if($key==31)
							{
								 
							
								//$conv_rate=$po_color_fab_array2[$po_id][$val[csf('color_id')]][$val[csf('deter_id')]][$key]['rate'];
							
								/*$arr_1=explode("__",$color_break_down);$amount_conv=0;
								for($ci=0;$ci<count($arr_1);$ci++)
								{
								$arr_2=explode("_",$arr_1[$ci]);
								*/
									
								$conv_rate=$po_color_fab_array[$po_id][$val[csf('color_id')]][$val[csf('deter_id')]][$key]['rate'];
								//echo $conv_rate.'D';
								$amount_conv=$conv_rate*$val[csf('batch_qnty')];
								//echo $arr_2[1].'='.$arr_2[3].'='.$conv_rate.'A,';
								//}
								//echo $conv_rate."=A,";
								
							}
							else if($key==33) //Heatset
							{
								$conv_rate=$po_color_fab_array2[$po_id][$val[csf('deter_id')]][$key]['rate'];
								$amount_conv=$conv_rate*$val[csf('batch_qnty')];
							}
							else
							{
							$conv_rate=$po_color_fab_array[$po_id][$val[csf('deter_id')]][$key]['rate'];
							$amount_conv=$conv_rate*$val[csf('batch_qnty')];
							//echo "B".$conv_rate.',';
							}
							
						}
						else
						{
							//if(in_array($key,$process_arr))
							//{
							$conv_rate=$sales_data_array[$po_id][$val[csf('deter_id')]][$val[csf('color_id')]][$key]['process_rate'];
							$amount_conv=$conv_rate*$val[csf('batch_qnty')];
							$tot_salesAmt+=$amount_conv;
							//if($key==33) echo $val[csf('deter_id')].'='.$val[csf('color_id')];else echo " ";
							//}
							 //echo $conv_rate.'B'.$amount_conv.', ';
						}
						
						 
							//echo $amount_conv.'='.$conv_rate.'<br/>';
						//$dyeing_cost += $conversion_costing_arr[$val[csf('po_breakdown_id')]][$key][12];
						//$dyeing_qty = $conversion_qty_arr[$val[csf('po_breakdown_id')]][$key][12];	
							if($amount_conv>0)
							{
							$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['dying_revenue']+=$amount_conv;// $conv_rate*$val[csf('batch_qnty')];
							}
						 
					}
				}
			
			}
			
			
			if($entry_formId!=38)
			{
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['batch_qnty']+= $val[csf('batch_qnty')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['batch_no']= $val[csf('batch_no')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['color_id']= $val[csf('color_id')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['within_group']= $within_group;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['is_sales_id']= $is_sales_id;
			
			//$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['avg_dyeing_rate']= $avg_dyeing_rate;
			
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['entry_formId']= $entry_formId;
			
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['mc_no']= $mc_no;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['prod_capacity']= $prod_capacity;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['buyer']= $buyerId;
		   // $conv_dyeing_cost_tot+=$conv_dyeing_cost;
			 $po_ids.=$val[csf('po_breakdown_id')].',';
			}
			
	}
	//echo $dyeing_cost.'='.$tot_salesAmt.'<br>';
	foreach ($sub_dying_prod_sql_res as $val)  //subcon Main Query
	{
			$entry_formId=$val[csf('entry_form')];
			$po_id=$val[csf('po_breakdown_id')];
			$color_id=$val[csf('color_id')];
			$mc_group=$machine_no_arr[$val[csf('machine_id')]]['mc_group'];
			$mc_no=$machine_no_arr[$val[csf('machine_id')]]['mc_no'];
			$prod_capacity=$machine_no_arr[$val[csf('machine_id')]]['prod_capacity'];
			//echo 	$mc_no.'dd';
			$ref_no=$po_array[$val[csf('po_breakdown_id')]]['ref_no'];
			$buyerId=$po_array[$val[csf('po_breakdown_id')]]['buyer'];
			//echo $dyeing_cost.'='.$dyeing_qty.'<br>';
			if($entry_formId==38)
			{
			$buyerId=	$sub_po_no_arr[$val[csf('po_breakdown_id')]]['buyer_name'];
			$ref_no=	$sub_po_no_arr[$val[csf('po_breakdown_id')]]['order_no'];
			$rate=	$sub_po_no_arr[$val[csf('po_breakdown_id')]]['rate'];
			$conv_dyeing_cost=($val[csf('batch_qnty')]*$rate)/$exch_rate;
			//$sub_po_no_arr[$val[csf('order_id')]]['buyer_name']	
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['batch_qnty']+= $val[csf('batch_qnty')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['batch_no']= $val[csf('batch_no')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['color_id']= $val[csf('color_id')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['avg_dyeing_rate']= $avg_dyeing_rate;
			
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['entry_formId']= $entry_formId;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['dying_revenue']+= $conv_dyeing_cost;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['mc_no']= $mc_no;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['prod_capacity']= $prod_capacity;
			$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['buyer']= $buyerId;
			}
		 // echo $entry_formId.'='.$exch_rate.'d';
			//$dying_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$val[csf('id')]]['cost_per_minute']= $cost_per_minute;
	}
	
	//asort($dying_prod_array);
	//echo $po_ids;
	
	
	// =================================== subcon kniting =============================
	  $width_td="740";
	   ?>
       <div style="margin-left:50px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Revenue Dyeing </b> </caption>
       <tr>
           <td  width="100"> <b>Floor</b> </td>
           <td  width="70"><?  echo $floor_arr[$floor_id]?></td>
           <td width="460" colspan="7"><b style="float:right"> Date: </b></td>
           <td width="70"> &nbsp; <? echo $gmt_date;?></td>
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
			<th width="50">M/C Type</th>
			<th width="70">M/C Capacity</th>
			<th width="70">M/C No</th>
			<th width="100">Buyer / Party</th>
			<th width="100">Ref No</th>
			<th width="100">Color</th>
			<th width="70">Batch No</th>
            <th width="70">Qty kg</th>
            <th width="40">Rate</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($dying_prod_array as $mc_grp=>$grp_data)
			{
				$mc_row_span=0;
			 foreach ($grp_data as $mc_id=>$mc_data)
			 {
			   foreach ($mc_data as $ref_no=>$ref_data)
			   {
				  foreach ($ref_data as $batch_id=>$row)
				  {
					  $mc_row_span++;
				  }
			    $mc_grp_row_arr[$mc_grp]=$mc_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$total_dying_revenue=$total_batch_prod=$total_batch_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($dying_prod_array as $mc_grp=>$grp_data)
			{
				$b=1;
			 foreach ($grp_data as $mc_id=>$mc_data)
			 {
			  foreach ($mc_data as $ref_no=>$ref_data)
			  {
				foreach ($ref_data as $batch_id=>$row)
			    {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
						if (!in_array($mc_grp,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="550" colspan="7"  align="right"> Buyer Total</td>
                              
                                <td width="70" align="right"><p><? echo number_format($sub_tot_batch_prod,0); ?>&nbsp;</p></td>
                                
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="" align="right"><p><? echo number_format($sub_tot_dying_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_tot_batch_prod);unset($sub_tot_dying_revenue);
							}
							$sub_group_by_arr[]=$mc_grp;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                 if($b==1)
					{
					?>
                    <td width="50"  rowspan="<? echo  $mc_grp_row_arr[$mc_grp];?>"><p>&nbsp;<? echo $mc_grp; ?></p></td>
					
                    <?
					
                   }
				   $is_sales_id=$row['is_sales_id'];
				    $within_group=$row['within_group'];
					if( $is_sales_id==1)
					{
				  		 if ($within_group == 1)
						$buyer = $company_name_arr[$row[('buyer')]];
						else
						$buyer = $buyer_arr[$row[('buyer')]];
						$buyer_name=$buyer;
					}
					else
					{
						$buyer_name=$buyer_arr[$row['buyer']];
					}
					
				   ?>
					<td width="70" title="<? echo $buyer_id;?>"><? echo $row['prod_capacity']; ?></td>
					<td width="70" style="word-break:break-all"><p>&nbsp;<? echo $row['mc_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $buyer_name; ?></p></td>
					<td width="100" align="center"><p><? echo $ref_no; ?>&nbsp;</p></td>
                    <td width="100" title="ColorId=<? echo $row['color_id'];?>"><p><? echo $color_arr[$row['color_id']]; ?>&nbsp;</p></td>
					<td width="70" align="center" title="<? echo $row['entry_formId'];?>"><? echo $row['batch_no'];; ?></td>
                    
                    <td width="70" align="right" title="Sales=<? echo $is_sales_id;?>"><p><? echo $row['batch_qnty']; ?>&nbsp;</p></td>
                     <td width="40" align="right"><p><? echo number_format($row['dying_revenue']/$row['batch_qnty'],4); ?>&nbsp;</p></td>
                     <td width="" align="right"><p><? $dying_revenue=$row['dying_revenue'];echo number_format($dying_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$sub_tot_dying_revenue+=$row['dying_revenue'];
				$sub_tot_batch_prod+=$row['batch_qnty'];
				$total_dying_revenue+=$dying_revenue;
				$total_batch_prod+=$row['batch_qnty'];
				
			    }
			   }
			 }
			}
			?>
             <tr class="tbl_bottom">
                <td width="550" colspan="7"  align="right"> Buyer Total</td>
                <td width="70"><p><? echo $sub_tot_batch_prod; ?>&nbsp;</p></td>
                <td width="40"><p><? //echo number_format($sub_tot_batch_prod,0);; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_tot_dying_revenue,0);; ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="50" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p> </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="100"><p><? //echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p>Grand Total</p></td>
                 <td width="70"><p><? echo number_format($total_batch_prod,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_dying_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        
    </div>
       <?
	
exit();
}
if($action=="finish_prod_kal") //
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		$type_id=$type;
		//echo $location_id;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		//$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	 $sql_sub_order="SELECT b.id as order_id,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_ord_dtls b,subcon_ord_mst c  where   b.job_no_mst=c.subcon_job  and c.status_active=1 and b.status_active=1 ";
	 $sql_sub_order_res = sql_select($sql_sub_order);
	 foreach ($sql_sub_order_res as $val)  //Main Query
	{
		$sub_po_no_arr[$val[csf('order_id')]]['style_ref_no']=$val[csf('style_ref_no')];
		$sub_po_no_arr[$val[csf('order_id')]]['order_no']=$val[csf('order_no')];
		$sub_po_no_arr[$val[csf('order_id')]]['buyer_name']=$val[csf('buyer_name')];
		$sub_po_no_arr[$val[csf('order_id')]]['rate']=$val[csf('rate')];
		
	}
	unset($sql_sub_order_res);
	    $sql_sub_knit=" SELECT a.party_id,b.order_id,b.batch_id,b.process,to_char(a.product_date,'DD-MON') as month_year,c.rate,
	  (CASE WHEN a.entry_form=292 and a.product_type=4  and a.company_id in($cbo_company_id)  THEN b.product_qnty END) AS fin_prod
	  from subcon_production_mst a, subcon_production_dtls b,subcon_ord_dtls c where a.id=b.mst_id and b.order_id=c.id  and a.entry_form in(292)   and  a.product_date='$gmt_date'  and a.product_type in(4)   and a.status_active=1 and b.status_active=1 and c.status_active=1";
	//entry_form=292, product_type=4
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	
	foreach ($sql_sub_knit_res as $val)  //Main Query
	{
		$poId_arr[$val[csf('order_id')]]=$val[csf('order_id')]; 
	
	}
	$sql_subBatch="select b.po_id,a.id,a.batch_no from pro_batch_create_mst a,pro_batch_create_dtls b,subcon_ord_dtls c where a.id=b.mst_id and c.id=b.po_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.po_id in(".implode(",",$poId_arr).")";
	$sql_res_subBatch = sql_select($sql_subBatch);
	foreach ($sql_res_subBatch as $val)  //Main Query
	{
		$batch_arr[$val[csf('id')]]=$val[csf('batch_no')];
	}
	unset($sql_subBatch);
		
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	 $fab_dyeing_chk_key=31;
	$fin_prod_array=array();
	foreach ($sql_sub_knit_res as $val)  //Main Query
	{
			$process=$val[csf('process')];
			$process_arr=array_unique(explode(",",$val[csf('process')]));
			
			if(!(in_array($fab_dyeing_chk_key, $process_arr )))
			{
				//echo $fab_dyeing_chk_key.'=A'.$process;
			$fin_prod=$val[csf('fin_prod')];
		  $batch_no= $batch_arr[$val[csf('batch_id')]];
			$buyerId=$val[csf('party_id')];
			//$sub_po_no=$sub_po_no_arr[$val[csf('order_id')]]['order_no'];
			$rate=	$sub_po_no_arr[$val[csf('order_id')]]['rate'];
			$finish_cost=($fin_prod*$rate);
			//$sub_po_no_arr[$val[csf('order_id')]]['buyer_name']	
			$fin_prod_array[$process][$buyerId]['fin_qty']+= $fin_prod;
			$fin_prod_array[$process][$buyerId]['avg_dyeing_rate']= $avg_dyeing_rate;
			$fin_prod_array[$process][$buyerId]['finish_revenue']+= $finish_cost;
			$fin_prod_array[$process][$buyerId]['buyer']= $buyerId;
			//$fin_prod_array[$process][$buyerId]['buyer']= $buyerId;
			$fin_prod_array[$process][$buyerId]['batch_no']= $batch_no;
			$fin_prod_array[$process][$buyerId]['po_no']= $sub_po_no;
			}
	}
	asort($fin_prod_array);
	//print_r($fin_prod_array);
	//echo $po_ids;
	
	
	// =================================== subcon kniting =============================
	  $width_td="600";
	   ?>
       <div style="margin-left:5px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Revenue - Finishing </b> </caption>
       <tr>
           <td  width="100"> <b></b> </td>
           <td  width="70"></td>
           <td width="460" colspan="7"><b style="float:right"> Date: </b></td>
           <td width="70"> &nbsp; <? echo $gmt_date;?></td>
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
			<th width="100">Process </th>
			<th width="100">Party name </th>
            <th width="70">Batch No</th>
			<th width="70">Production Qty</th>
			<th width="70">Rate(Taka)</th>
			<th width="70">Total Amount</th>
			<th width="70">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($fin_prod_array as $processId=>$proc_data)
			{
				$fin_row_span=0;
			 
				  foreach ($proc_data as $buyer_id=>$row)
				  {
					  $fin_row_span++;
				  }
			    $fin_row_arr[$processId]=$fin_row_span;
			  
			}
			//print_r($buyer_row_arr);
			$total_fin_prod=$total_fin_revenue=$total_fin_prod_revenue=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($fin_prod_array as $processId=>$proc_data)
			{
				$b=1;
			 foreach ($proc_data as $buyer_id=>$row)
			 {
			   
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
						if (!in_array($processId,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="200" colspan="3"  align="right"> Buyer Total</td>
                                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="70" align="right"><p><? echo number_format($sub_tot_fin_prod,0); ?>&nbsp;</p></td>
                                
                                <td width="70"><p><? echo number_format($sub_tot_fin_revenue,0); ?>&nbsp;</p></td>
                                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="" align="right"><p><? echo number_format($sub_tot_fin_prod_rev,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_tot_fin_prod);unset($sub_tot_fin_prod_rev);unset($sub_tot_fin_revenue);
							}
							$sub_group_by_arr[]=$processId;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                 if($b==1)
					{
					?>
                    <td width="100"  rowspan="<? echo  $fin_row_arr[$processId];?>"><p>&nbsp;<? 
					
					$exp_pro=array_unique(explode(",",$processId));
					$process_name="";
					foreach($exp_pro as $proc_id)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$proc_id];else $process_name.=".".$conversion_cost_head_array[$proc_id];
					}
					echo $process_name; ?></p></td>
					
                    <?
                   }?>
					<td width="100" title="<? echo $buyer_id;?>" style="word-break:break-all"><? echo $buyer_arr[$row['buyer']]; ?></td>
                    <td width="70" align="right"><p>&nbsp;<? echo $row['batch_no']; ?></p></td>
					<td width="70" align="right"><p>&nbsp;<? echo $row['fin_qty']; ?></p></td>
					<td width="70" align="center"><p><? echo $row['finish_revenue']/$row['fin_qty'];//$buyer_arr[$row['buyer']]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['finish_revenue']; ?>&nbsp;</p></td>
					<td width="70" align="right" title=""><? echo $exch_rate; ?></td>
                    <td width="" align="right"><p><? $fin_revenue=$row['finish_revenue']/$exch_rate;echo number_format($fin_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$sub_tot_fin_revenue+=$row['finish_revenue'];
				$sub_tot_fin_prod+=$row['fin_qty'];
				$sub_tot_fin_prod_rev+=$fin_revenue;
				$total_fin_revenue+=$fin_revenue;
				$total_fin_prod+=$row['fin_qty'];
				$total_fin_prod_revenue+=$row['finish_revenue'];
			 }
			}
			?>
             <tr class="tbl_bottom">
                <td width="200" colspan="3"  align="right"> Buyer Total</td>
                <td width="70"><p><? echo $sub_tot_fin_prod; ?>&nbsp;</p></td>
                 <td width="70"><p><? //echo $sub_tot_batch_prod; ?>&nbsp;</p></td>
                 <td width="70"><p><? echo $sub_tot_fin_revenue; ?>&nbsp;</p></td>
                 <td width="70"><p><? //echo $sub_tot_batch_prod; ?>&nbsp;</p></td>
               
                <td width=""><p><? echo number_format($sub_tot_fin_prod_rev,0);; ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="100" ></td>
                <td width="100"><p> Grand Total</p></td>
                <td width="80" ></td>
                <td width="80"><? echo number_format($total_fin_prod,0); ?></td>
                <td width="70"><p><? //echo number_format($sub_tot_fin_revenue,0); ?>&nbsp;</p></td>
                <td width="80"><p><? echo number_format($sub_tot_fin_revenue,0); ?>&nbsp;</p></td>
               
                <td width=""><p><? echo number_format($total_fin_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        
    </div>
       <?
	
exit();
}
if($action=="knitting_prod_kal") //
{
		echo load_html_head_contents("Knitting Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$location_id=$ex_dara[2];
		$type_id=$type;
		$knit_loc_con="";$sub_knit_loc_con="";
		if($type_id==8) //Jm Locatrion knitting
		{
		
		$knit_loc_con="and c.knitting_location_id=$location_id"; 
		$sub_knit_loc_con="and a.location_id=$location_id";
		}
		//echo $knit_loc_con;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$desc_arr=return_library_array( "select id, item_description from  product_details_master",'id','item_description');
		
		$sql_mc="select id, machine_no,machine_group,dia_width,prod_capacity from  lib_machine_name where   status_active=1";
		$sql_mc_res = sql_select($sql_mc);
		foreach ($sql_mc_res as $val) 
		{
			$machine_no_arr[$val[csf('id')]]['mc_no'] = $val[csf('machine_no')];
			$machine_no_arr[$val[csf('id')]]['mc_group'] = $val[csf('machine_group')];
			$machine_no_arr[$val[csf('id')]]['dia'] = $val[csf('dia_width')];
			$machine_no_arr[$val[csf('id')]]['prod_capacity'] = $val[csf('prod_capacity')];
		}
	
	
	
	  $sql_kniting_dyeing="SELECT a.machine_no_id,b.is_sales,a.febric_description_id as deter_id,a.prod_id,b.po_breakdown_id,c.buyer_id,to_char(c.receive_date,'DD-MON') as month_year,(b.quantity) as grey_receive_qnty,c.recv_number from pro_grey_prod_entry_dtls a,order_wise_pro_details b, inv_receive_master c where a.mst_id = c.id and a.id = b.dtls_id and c.knitting_company in($cbo_company_id) and c.receive_date='$gmt_date' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2) $knit_loc_con order by b.po_breakdown_id asc";
//MON-YYYY
	$sql_kniting_dyeing_res = sql_select($sql_kniting_dyeing);
	foreach ($sql_kniting_dyeing_res as $val) 
	{
		$is_sales_id=$val[csf('is_sales')];
		if($is_sales_id==1)
		{
			$sales_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			$sales_buyer_array[$val[csf('po_breakdown_id')]] = $val[csf('buyer_id')];
		}
		else
		{
			$po_id_array[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
		}
	}
	
	 
	$poIds = implode(",", array_unique($po_id_array));
	$salesIds = implode(",", array_unique($sales_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	if($salesIds !="")
	{
		$sales_cond="";
		if(count($sales_id_array)>999)
		{
			$chunk_arr=array_chunk($sales_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sales_cond=="") $sales_cond.=" and ( a.id in ($ids) ";
				else
					$sales_cond.=" or   a.id in ($ids) "; 
			}
			$sales_cond.=") ";

		}
		else
		{
			$sales_cond.=" and a.id in ($salesIds) ";
		}
	}
	  $sql_sales = "select a.id,a.job_no, a.within_group,a.buyer_id,b.color_id,b.determination_id as deter_id,b.process_id,b.process_seq,b.body_part_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $sales_cond order by a.id desc";
		$sql_sales_result = sql_select($sql_sales);//and a.company_id in($cbo_company_id)
		foreach ($sql_sales_result as $val) 
		{		
					
				$buyer = $val[csf('buyer_id')];
				$sales_buyer_array[$val[csf('id')]]['buyer'] = $buyer;
				$sales_no_array[$val[csf('id')]]['sales_no'] =$val[csf('job_no')];
				//echo $val[csf('job_no')].' ,';
				$sales_buyer_array[$val[csf('id')]]['within_group'] = $val[csf('within_group')];
				
				$process_id=array_unique(explode(",",$val[csf('process_id')]));
				$process_seqArr=array_unique(explode(",",$val[csf('process_seq')]));
				foreach($process_id as $p_key)
				{
						foreach($process_seqArr as $val_rate)
						{
							$process_Rate=explode("__",$val_rate);
							$process_Id=$process_Rate[0];
							$process_rate=$process_Rate[1];
							if($p_key==$process_Id && $process_rate>0)
							{
							//$sales_data_array[$val[csf('id')]][$val[csf('deter_id')]][$val[csf('color_id')]][$p_key]['process_rate'] = $process_rate;
							$sales_data_knit_array[$val[csf('id')]][$val[csf('deter_id')]][$p_key]['process_rate'] = $process_rate;
							}
						}
				}
		}
	
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	// $sql_po="SELECT a.buyer_name,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and b.status_active=1 and b.is_deleted=0 $cm_po_cond"; 
	 $sql_po="SELECT a.buyer_name,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.id as conv_id,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $cm_po_cond order by f.id,b.id asc";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_array[$val[csf('id')]]['buyer'] = $val[csf('buyer_name')];
		$po_array[$val[csf('id')]]['ref_no'] = $val[csf('ref_no')];
		$color_break_down=$val[csf('color_break_down')];
		if($val[csf('charge_unit')]>0)
		{
		$po_color_knit_array[$val[csf('id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['knit_rate'] = $val[csf('charge_unit')];
		}
		
	}
	

	 $sql_po="SELECT a.buyer_name,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and b.status_active=1 and b.is_deleted=0 $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_array[$val[csf('id')]]['buyer'] = $val[csf('buyer_name')];
		$po_array[$val[csf('id')]]['ref_no'] = $val[csf('ref_no')];
	}
	$condition= new condition();
	//$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);
	$conversion= new conversion($condition);
	//echo $conversion->getQuery();die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//echo "<pre>";print_r($conversion_costing_arr);die();
	$conversion_qty_arr=$conversion->getQtyArray_by_orderAndProcess();
	
	
	//$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	 $sql_sub_order="SELECT b.id as order_id,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_ord_dtls b,subcon_ord_mst c  where   b.job_no_mst=c.subcon_job  and c.status_active=1 and b.status_active=1 ";
	 $sql_sub_order_res = sql_select($sql_sub_order);
	 foreach ($sql_sub_order_res as $val)  //Main Query
	{
		$sub_po_no_arr[$val[csf('order_id')]]['style_ref_no']=$val[csf('style_ref_no')];
		$sub_po_no_arr[$val[csf('order_id')]]['order_no']=$val[csf('order_no')];
		$sub_po_no_arr[$val[csf('order_id')]]['buyer_name']=$val[csf('buyer_name')];
		$sub_po_no_arr[$val[csf('order_id')]]['rate']=$val[csf('rate')];
	}
	 
	
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	// $process_array=array(1,30,35);
	$conv_dyeing_cost_tot=0;
	$dying_prod_array=array();
	foreach ($sql_kniting_dyeing_res as $val)  //Main Query
	{
		$mc_group=$machine_no_arr[$val[csf('machine_no_id')]]['mc_group'];
		$mc_no=$machine_no_arr[$val[csf('machine_no_id')]]['mc_no'];
		$is_sales_id=$val[csf('is_sales')];
		$within_group=$sales_buyer_array[$val[csf('po_breakdown_id')]]['within_group'];
		$ref_no=$po_array[$val[csf('po_breakdown_id')]]['ref_no'];
	
			if($is_sales_id==1)
			{ 
			$ref_no=$sales_no_array[$val[csf('po_breakdown_id')]]['sales_no'];
			$sales_buyer=$sales_buyer_array[$val[csf('po_breakdown_id')]]['buyer'];
			$buyerId=$val[csf('buyer_id')];//$sales_buyer;
			}
			else
			{
				$buyerId=$val[csf('buyer_id')];
			}
		$dia=$machine_no_arr[$val[csf('machine_no_id')]]['dia'];
		
		
			$fab_desc=explode(",",$desc_arr[$val[csf('prod_id')]]);
			$fab_cons=$fab_desc[0];
		 // echo $entry_formId.'='.$exch_rate.'d';
			 $kniting_cost=0;
			$kniting_qty=0;		//grey_receive_qnty			
			if($val[csf('po_breakdown_id')]>0)
			{
				
				if($is_sales_id!=1)
				{
					$kniting_rate=$po_color_knit_array[$val[csf('po_breakdown_id')]][$val[csf('deter_id')]][1]['knit_rate'];
					//$ref_no=$ref_no;
				}
				else
				{
					//if($val[csf('po_breakdown_id')]==116) echo "Y".$val[csf('deter_id')];else echo " ";
					$kniting_rate=$sales_data_knit_array[$val[csf('po_breakdown_id')]][$val[csf('deter_id')]][1]['process_rate'];
					
					//$ref_no=$sales_no;

				}
				//echo $avg_kniting_rate.'DD';
			}	
			//if($kniting_rate >0)
			//{
			$knitingCost =$kniting_rate*$val[csf('grey_receive_qnty')];	
			
			//echo $kniting_cost.'='.$kniting_qty.',';
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['buyer']= $buyerId;
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['sales_id']= $is_sales_id;
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['recv_number'].=$val[csf('recv_number')].',';
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['within_group']= $within_group;
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['is_sales_id']= $is_sales_id;
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['grey_receive_qnty']+=$val[csf('grey_receive_qnty')];
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['knit_cost']+=$knitingCost;
		//	$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['grey_receive_qnty']=$val[csf('grey_receive_qnty')];
			$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['mc_dia']= $dia;
			//}
	}
	ksort($knit_prod_array);
	//echo $po_ids;
	 $sql_sub_knit=" SELECT a.party_id,b.order_id,to_char(a.product_date,'DD-MON') as month_year,b.machine_id,b.cons_comp_id,b.fabric_description,
	  (CASE WHEN a.entry_form=159 and a.product_type=2 and b.process='1' and a.knitting_company in($cbo_company_id)  THEN b.product_qnty END) AS sub_knitting_prod
	  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form in(159)   and a.product_date='$gmt_date'  and a.product_type in(2)   and a.status_active=1 and b.status_active=1 $sub_knit_loc_con order by b.machine_id asc";
	//entry_form=292, product_type=4
	$sql_sub_knit_res = sql_select($sql_sub_knit);
	foreach ($sql_sub_knit_res as $val)  //Main Query
	{
			$subKnit_cost =$sub_po_no_arr[$val[csf('order_id')]]['rate']*$val[csf("sub_knitting_prod")];
			
			$mc_group=$machine_no_arr[$val[csf('machine_id')]]['mc_group'];
			$mc_no=$machine_no_arr[$val[csf('machine_id')]]['mc_no'];
			$dia=$machine_no_arr[$val[csf('machine_id')]]['dia'];
			$ref_no=$po_array[$val[csf('po_breakdown_id')]]['ref_no'];
			$fab_desc=explode(",",$val[csf('fabric_description')]);
			$fab_cons=$fab_desc[0];
			$ref_no=$sub_po_no_arr[$val[csf('order_id')]]['order_no'];
			
		 	//echo $sub_po_no_arr[$val[csf('order_id')]]['rate'].'='.$val[csf("sub_knitting_prod")].', ';
			if($subKnit_cost>0)
			{
				$subKnit_costUSD = $subKnit_cost/$rate;
				//echo $subKnit_cost.'='.$rate;
			$knit_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$fab_cons]['buyer']= $val[csf('party_id')]; 
			$knit_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$fab_cons]['grey_receive_qnty']+=$val[csf('sub_knitting_prod')];
			$knit_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$fab_cons]['knit_cost']+=$subKnit_costUSD;
		//	$knit_prod_array[$mc_group][$val[csf('machine_no_id')]][$ref_no][$fab_cons]['grey_receive_qnty']=$val[csf('grey_receive_qnty')];
			$knit_prod_array[$mc_group][$val[csf('machine_id')]][$ref_no][$fab_cons]['mc_dia']= $dia;
			}
		
	}
	ksort($knit_prod_array);
	
	
	// =================================== subcon kniting =============================
	  $width_td="700";
	   ?>
       <div style="margin-left:20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Revenue Knitting </b> </caption>
       <tr>
           <td  width="100"> <b> </b> </td>
           <td  width="70"><? // echo $floor_arr[$floor_id]?></td>
           <td width="460" colspan="7"><b style="float:right"> Date: </b></td>
           <td width="70"> &nbsp; <? echo $gmt_date;?></td>
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
			<th width="50">M/C Type</th>
			<th width="70">M/C Dia</th>
			<th width="70">M/C No</th>
			<th width="100">Buyer / Party</th>
			<th width="100">Ref No</th>
			<th width="100">Fabric Structure</th>
            <th width="70">Qty kg</th>
            <th width="40">Rate</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($knit_prod_array as $mc_grp=>$grp_data)
			{
				$knit_row_span=0;
			 foreach ($grp_data as $mc_id=>$mc_data)
			 {
			   foreach ($mc_data as $ref_no=>$ref_data)
			   {
				  foreach ($ref_data as $fab_cons=>$row)
				  {
					  $knit_row_span++;
				  }
			    $knit_grp_row_arr[$mc_grp]=$knit_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$total_knit_prod=$total_knit_revenue=$total_batch_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($knit_prod_array as $mc_grp=>$grp_data)
			{
				$b=1;
			 foreach ($grp_data as $mc_id=>$mc_data)
			 {
			  foreach ($mc_data as $ref_no=>$ref_data)
			  {
				foreach ($ref_data as $fab_cons=>$row)
			    {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
						if (!in_array($mc_grp,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="490" colspan="6"  align="right"> Buyer Total</td>
                                <td width="70"><p><? echo $sub_tot_knit_prod; ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="" align="right"><p><? echo number_format($sub_tot_knit_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_tot_knit_prod);unset($sub_tot_knit_revenue);
							}
							$sub_group_by_arr[]=$mc_grp;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                 if($b==1)
					{
					?>
                    <td width="50"  rowspan="<? echo  $knit_grp_row_arr[$mc_grp];?>"><p>&nbsp;<? echo $mc_grp; ?></p></td>
					
                    <?
                   }
				    $is_sales_id=$row['is_sales_id'];
					$recv_number=$row['recv_number'];
				    $within_group=$row['within_group'];
					if( $is_sales_id==1)
					{
				  		 if ($within_group == 1)
						$buyer = $company_name_arr[$row[('buyer')]];
						else
						$buyer = $buyer_arr[$row[('buyer')]];
						$buyer_name=$buyer;
					}
					else
					{
						$buyer_name=$buyer_arr[$row['buyer']];
					}
				   ?>
					<td width="70" title="<? echo $buyer_id;?>"><? echo $row['mc_dia']; ?></td>
					<td width="70" style="word-break:break-all"><p>&nbsp;<? echo $machine_no_arr[$mc_id]['mc_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $buyer_name; ?></p></td>
					<td width="100" align="center"><p><? echo $ref_no; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $fab_cons; ?>&nbsp;</p></td>
				
                    <td width="70" align="right" title="RecvNo=<? echo $recv_number.', Sales Id='.$is_sales_id;?>"><p><? echo $row['grey_receive_qnty']; ?>&nbsp;</p></td>
                     <td width="40" align="right"><p><? echo number_format($row['knit_cost']/$row['grey_receive_qnty'],2); ?>&nbsp;</p></td>
                     <td width="" align="right"><p><? $knit_revenue=$row['knit_cost'];echo number_format($knit_revenue,2); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$sub_tot_knit_revenue+=$knit_revenue;
				$sub_tot_knit_prod+=$row['grey_receive_qnty'];
				$total_knit_revenue+=$knit_revenue;
				$total_knit_prod+=$row['grey_receive_qnty'];
				
			    }
			   }
			 }
			}
			?>
             <tr class="tbl_bottom">
                <td width="490" colspan="6"  align="right"> Buyer Total</td>
                <td width="70"><p><? echo $sub_tot_knit_prod; ?>&nbsp;</p></td>
                <td width="40"><p><? //echo number_format($sub_tot_batch_prod,0);; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_tot_knit_revenue,0);; ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="50" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                
                <td width="70"><p> </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="100"><p><? //echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                
                <td width="100"><p>Grand Total&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_knit_prod,0); ?></p></td>
              
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_knit_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        
    </div>
       <?
	
exit();
}

if($action=="print_prod_kal") //
{
		echo load_html_head_contents("Print Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		$type_id=$type;
	//echo $cbo_company_id;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
		
	   $sql_fin_prod="SELECT a.embel_name,a.location,a.po_break_down_id,a.item_number_id,to_char(a.production_date,'DD-MON') as month_year,b.color_size_break_down_id,
	 (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS msew_out,
	 (CASE WHEN  a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS print_recv,
	 (CASE WHEN  a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS embro_recv,
	 (CASE WHEN a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS wash_recv
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type in(3) and a.production_source in(1)  and a.location <> 0 and a.embel_name=1  and a.status_active=1  and b.status_active=1 order by a.location";
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}
	
	 
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no,c.id as color_size_id,c.color_number_id from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where b.job_id=a.id and c.po_break_down_id=b.id and c.job_id=a.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_array[$val[csf('id')]]['buyer'] = $val[csf('buyer_name')];
		$po_array[$val[csf('id')]]['ref_no'] = $val[csf('ref_no')];
		$po_array[$val[csf('id')]]['style_ref_no'] = $val[csf('style_ref_no')];
		//$po_color_array[$val[csf('color_size_id')]]['color'] = $val[csf('color_number_id')];
	}
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_id=b.job_id and c.job_id=a.job_id and  a.job_id=b.job_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_array[$val[csf('id')]]['sew_smv'] = $val[csf('sew_smv')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$pre_cost_array[$val[csf('id')]]['cost_per']=$val[csf('costing_per')];
	}  
	$cm_po_cond3 = str_replace("id", "c.po_break_down_id", $po_cond);
	
    $sql_pre_wash="SELECT b.emb_name, d.id,d.color_number_id,d.po_break_down_id as po_id,c.id as color_size_id,d.requirment,d.rate from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b where d.job_id=a.id   and c.job_id=a.id and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id and  b.job_id=a.id  and  b.job_id=c.job_id   and b.id=d.pre_cost_emb_cost_dtls_id   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and d.requirment>0 and d.rate>0 and b.emb_name=1 $cm_po_cond3 order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val) 
	{
		$po_color_rate_array[$val[csf('emb_name')]][$val[csf('po_id')]][$val[csf('color_number_id')]]['rate'] = $val[csf('rate')];
		
		$po_color_avg_rate_array[$val[csf('emb_name')]][$val[csf('po_id')]][$val[csf('color_number_id')]]['avg_rate']+= $val[csf('rate')];
		$po_color_avg_rate_array[$val[csf('emb_name')]][$val[csf('po_id')]][$val[csf('color_number_id')]]['avg_req']+= $val[csf('requirment')];
		
		 $po_color_array[$val[csf('color_size_id')]]['color'] = $val[csf('color_number_id')];
	}
	unset($sql_wash_result);
	
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);
	$emblishment= new emblishment($condition);
	//echo $emblishment->getQuery();die;
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
	
	//echo "<pre>";print_r($emblishment_costing_arr_name);die();
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	// $process_array=array(1,30,35);
	
	foreach ($sql_fin_prod_res as $val)   //Main Query
	{
		
			$buyerId=$po_array[$val[csf('po_break_down_id')]]['buyer'];
			//echo $buyerId.'d,';
			$ref_no=$po_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			$color_id=$po_color_array[$val[csf('color_size_break_down_id')]]['color'];
			$item_id=$val[csf('item_number_id')];
		 //echo $color_id.'='.$item_id.',';
			/*$print_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][1];
			$print_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][1];
			
			$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			
			$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];*/
			
			$dzn_qnty=0;//
			
			$costing_per_id=$pre_cost_array[$val[csf('po_break_down_id')]]['cost_per'];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			if($color_id=='') $color_id=0;
			 
			//$po_color_rate=$po_color_rate_array[$val[csf('embel_name')]][$val[csf('po_break_down_id')]][$color_id]['rate'];
			$avg_rate=$po_color_avg_rate_array[$val[csf('embel_name')]][$val[csf('po_break_down_id')]][$color_id]['avg_rate'];
			$avg_req=$po_color_avg_rate_array[$val[csf('embel_name')]][$val[csf('po_break_down_id')]][$color_id]['avg_req'];
			$po_color_rate=$avg_rate/$avg_req;
			
			 
			
			if($val[csf('print_recv')]>0)
			{
				// echo $avg_po_color_rate.'='.$dzn_qnty.'<br>';
				//$print_avg_rate=$po_color_rate;
				//echo $print_avg_rate.'*'.$val[csf('print_recv')].'<br>';
			$print_avg_rate=$po_color_rate/$dzn_qnty;
			$print_amount=$val[csf('print_recv')]*$print_avg_rate;
			//$year_prod_cost_arr[$fyear]['print_recv'] += $print_amount;
			}
			//  echo $print_amount.'='.$val[csf('print_recv')].'='.$print_avg_rate.'<br>';
			
			if($val[csf('embro_recv')]>0)
			{
			//$embro_avg_rate=($embro_cost/$embro_qty)/$dzn_qnty;;
			$embro_avg_rate=$po_color_rate/$dzn_qnty;
			$embro_amount=$val[csf('embro_recv')]*$embro_avg_rate;
			$year_prod_cost_arr[$fyear]['embo_recv'] += $embro_amount;
			}
			if($val[csf('wash_recv')]>0)
			{
			//$wash_avg_rate=($wash_cost/$wash_qty)/$dzn_qnty;;
			$wash_avg_rate=$po_color_rate/$dzn_qnty;
			$wash_amount=$val[csf('wash_recv')]*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			if($print_amount>0)
			{
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['buyer']= $val[csf('buyer_id')];
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['style_ref_no']= $style_ref_no;
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['print_recv']+=$val[csf('print_recv')];
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['print_revenue']+=$print_amount;
			}
			
	}
	//print_r($print_prod_array);
	//echo $po_ids;
	  $width_td="660";
	   ?>
       <div style="margin-left:50px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Revenue Print </b> </caption>
       <tr>
           <td  width="100"> <b> </b> </td>
           <td  width="70"><? // echo $floor_arr[$floor_id]?></td>
           <td width="420" colspan="5"><b style="float:right"> Date: </b></td>
           <td width="70"> &nbsp; <? echo $gmt_date;?></td>
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
			<th width="100">Buyer</th>
			<th width="70">Ref no</th>
			<th width="100">Style</th>
			<th width="100">Gmts Item</th>
			<th width="70">Color/combo </th>
			<th width="100">Rcvd Qty Pcs </th>
            <th width="40">Rate</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($print_prod_array as $buyer_id=>$buyer_data)
			{
				$print_row_span=0;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			   foreach ($ref_data as $item_id=>$item_data)
			   {
				  foreach ($item_data as $color_id=>$row)
				  {
					  $print_row_span++;
				  }
			    $print_grp_row_arr[$buyer_id]=$print_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$total_print_revenue=$total_print_prod=$total_batch_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($print_prod_array as $buyer_id=>$buyer_data)
			{
				$b=1;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			   foreach ($ref_data as $item_id=>$item_data)
			   {
				foreach ($item_data as $color_id=>$row)
			    {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
						if (!in_array($buyer_id,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="440" colspan="5"  align="right"> Buyer Total</td>
                                <td width="100"><p><? echo number_format($sub_tot_print_prod,0); ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="" align="right"><p><? echo number_format($sub_tot_print_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_tot_print_prod);unset($sub_tot_print_revenue);
							}
							$sub_group_by_arr[]=$buyer_id;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                if($b==1)
					{
					?>
                    <td width="100"  rowspan="<? echo  $print_grp_row_arr[$buyer_id];?>"><p>&nbsp;<? echo $buyer_arr[$buyer_id]; ?></p></td>
					
                    <?
                  }?>
					<td width="70" title="<? echo $buyer_id;?>"><? echo $ref_no; ?></td>
					<td width="100" style="word-break:break-all"><p>&nbsp;<? echo $row[('style_ref_no')] ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $color_arr[$color_id]; ?>&nbsp;</p></td>
					<td width="100" align="right"><p><? echo number_format($row['print_recv'],0); ?>&nbsp;</p></td>
                   
                     <td width="40" align="right"><p><? echo number_format(($row['print_revenue']/$row['print_recv']),2); ?>&nbsp;</p></td>
                     <td width="" align="right"><p><? $print_revenue=$row['print_revenue'];echo number_format($print_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$sub_tot_print_revenue+=$print_revenue;
				$sub_tot_print_prod+=$row['print_recv'];
				$total_print_revenue+=$print_revenue;
				$total_print_prod+=$row['print_recv'];
				
			    }
			   }
			 }
			}
			?>
             <tr class="tbl_bottom">
                <td width="440" colspan="5"  align="right"> Buyer Total</td>
                <td width="100"><p><? echo number_format($sub_tot_print_prod,0);; ?>&nbsp;</p></td>
                <td width="40"><p><? //echo number_format($sub_tot_batch_prod,0);; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_tot_print_revenue,0);; ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="100"><p> </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p>Grand Total&nbsp;</p></td>
                <td width="100"><p><? echo number_format($total_print_prod,0); ?></p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_print_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        
    </div>
       <?
	
exit();
}
if($action=="embro_prod_kal") //
{
		echo load_html_head_contents("Embroidery Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		$type_id=$type;
	//echo $cbo_company_id;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
		
	  $sql_fin_prod="SELECT a.embel_name,a.location,a.po_break_down_id,a.item_number_id,to_char(a.production_date,'DD-MON') as month_year,b.color_size_break_down_id,
	 (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS msew_out,
	 (CASE WHEN  a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS print_recv,
	 (CASE WHEN  a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS embro_recv,
	 (CASE WHEN a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS wash_recv
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(3) and a.embel_name=2 and a.production_source in(1)  and a.location <> 0  and a.status_active=1  and b.status_active=1 order by a.location";
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}
	$cm_po_cond3 = str_replace("id", "c.po_break_down_id", $po_cond);
	
    $sql_pre_embro="SELECT b.emb_name, d.id,d.color_number_id,d.po_break_down_id as po_id,c.id as color_size_id,d.rate from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b where d.job_id=a.id   and c.job_id=a.id and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id and  b.job_id=a.id  and  b.job_id=c.job_id   and b.id=d.pre_cost_emb_cost_dtls_id   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.requirment>0 and d.rate>0 and b.emb_name=2 $cm_po_cond3 order by d.id asc";
	$sql_embro_result = sql_select($sql_pre_embro);
	foreach ($sql_embro_result as $val) 
	{
		$po_color_rate_array[$val[csf('emb_name')]][$val[csf('po_id')]][$val[csf('color_number_id')]]['rate'] = $val[csf('rate')];
		$po_color_array[$val[csf('color_size_id')]]['color'] = $val[csf('color_number_id')];
	}
	unset($sql_embro_result);
	
	
	 
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no,c.id as color_size_id,c.color_number_id from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and c.po_break_down_id=b.id and c.job_no_mst=a.job_no and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_array[$val[csf('id')]]['buyer'] = $val[csf('buyer_name')];
		$po_array[$val[csf('id')]]['ref_no'] = $val[csf('ref_no')];
		$po_array[$val[csf('id')]]['style_ref_no'] = $val[csf('style_ref_no')];
		//$po_color_array[$val[csf('color_size_id')]]['color'] = $val[csf('color_number_id')];
	}
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_array[$val[csf('id')]]['sew_smv'] = $val[csf('sew_smv')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$pre_cost_array[$val[csf('id')]]['cost_per']=$val[csf('costing_per')];
	}  
	
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);
	$emblishment= new emblishment($condition);
	//echo $emblishment->getQuery();die;
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
	
	//echo "<pre>";print_r($emblishment_costing_arr_name);die();
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	// $process_array=array(1,30,35);
	
	foreach ($sql_fin_prod_res as $val)   //Main Query
	{
		
			$buyerId=$po_array[$val[csf('po_break_down_id')]]['buyer'];
			//echo $buyerId.'d,';
			$ref_no=$po_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			$color_id=$po_color_array[$val[csf('color_size_break_down_id')]]['color'];
			$item_id=$val[csf('item_number_id')];
		//echo $color_id.'='.$item_id.',';
		//	$print_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][1];
			//$print_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][1];
			
			//$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			//$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			
			//$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			//$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];
			$po_color_rate=$po_color_rate_array[$val[csf('embel_name')]][$val[csf('po_break_down_id')]][$color_id]['rate'];
			
			//echo $po_color_rate.'d';
			$dzn_qnty=0;//
			
			$costing_per_id=$pre_cost_array[$val[csf('po_break_down_id')]]['cost_per'];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			//echo $print_cost.'='.$costing_per_id.'<br>';
			if($print_cost>0)
			{
			$print_avg_rate=($print_cost/$print_qty)/$dzn_qnty;
			$print_amount=$val[csf('print_recv')]*$print_avg_rate;
			//$year_prod_cost_arr[$fyear]['print_recv'] += $print_amount;
			}
			//echo $print_cost.'='.$print_qty.'<br>';
			
			if($val[csf('embro_recv')]>0)
			{
			$embro_avg_rate=$po_color_rate/$dzn_qnty;
			$embro_amount=$val[csf('embro_recv')]*$embro_avg_rate;
			//$year_prod_cost_arr[$fyear]['embro_recv'] += $embro_amount;
			}
			if($wash_cost>0)
			{
			$wash_avg_rate=($wash_cost/$wash_qty)/$dzn_qnty;;
			$wash_amount=$val[csf('wash_recv')]*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			if($embro_amount>0)
			{
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['buyer']= $val[csf('buyer_id')];
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['style_ref_no']= $style_ref_no;
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['embro_recv']+=$val[csf('embro_recv')];
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['embro_revenue']+=$embro_amount;
			}
			
	}
	//print_r($print_prod_array);
	//echo $po_ids;
	  $width_td="660";
	   ?>
       <div style="margin-left:50px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Revenue Embroidery </b> </caption>
       <tr>
           <td  width="100"> <b> </b> </td>
           <td  width="70"><? // echo $floor_arr[$floor_id]?></td>
           <td width="420" colspan="5"><b style="float:right"> Date: </b></td>
           <td width="70"> &nbsp; <? echo $gmt_date;?></td>
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
			<th width="100">Buyer</th>
			<th width="70">Ref no</th>
			<th width="100">Style</th>
			<th width="100">Gmts Item</th>
			<th width="70">Color/combo </th>
			<th width="100">Rcvd Qty Pcs </th>
            <th width="40">Rate</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($print_prod_array as $buyer_id=>$buyer_data)
			{
				$print_row_span=0;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			   foreach ($ref_data as $item_id=>$item_data)
			   {
				  foreach ($item_data as $color_id=>$row)
				  {
					  $print_row_span++;
				  }
			    $print_grp_row_arr[$buyer_id]=$print_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$total_print_revenue=$total_print_prod=$total_batch_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($print_prod_array as $buyer_id=>$buyer_data)
			{
				$b=1;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			   foreach ($ref_data as $item_id=>$item_data)
			   {
				foreach ($item_data as $color_id=>$row)
			    {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
						if (!in_array($buyer_id,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="440" colspan="5"  align="right"> Buyer Total</td>
                                <td width="100"><p><? echo number_format($sub_tot_print_prod,0); ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="" align="right"><p><? echo number_format($sub_tot_print_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_tot_print_prod);unset($sub_tot_print_revenue);
							}
							$sub_group_by_arr[]=$buyer_id;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                if($b==1)
					{
					?>
                    <td width="100"  rowspan="<? echo  $print_grp_row_arr[$buyer_id];?>"><p>&nbsp;<? echo $buyer_arr[$buyer_id]; ?></p></td>
					
                    <?
                  }?>
					<td width="70" title="<? echo $buyer_id;?>"><? echo $ref_no; ?></td>
					<td width="100" style="word-break:break-all"><p>&nbsp;<? echo $row[('style_ref_no')] ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $color_arr[$color_id]; ?>&nbsp;</p></td>
					<td width="100" align="right"><p><? echo number_format($row['embro_recv'],0); ?>&nbsp;</p></td>
                   
                     <td width="40" align="right"><p><? echo number_format(($row['embro_revenue']/$row['embro_recv']),2); ?>&nbsp;</p></td>
                     <td width="" align="right"><p><? $embro_revenue=$row['embro_revenue'];echo number_format($embro_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$sub_tot_print_revenue+=$embro_revenue;
				$sub_tot_print_prod+=$row['embro_recv'];
				$total_print_revenue+=$embro_revenue;
				$total_print_prod+=$row['embro_recv'];
				
			    }
			   }
			 }
			}
			?>
             <tr class="tbl_bottom">
                <td width="440" colspan="5"  align="right"> Buyer Total</td>
                <td width="100"><p><? echo number_format($sub_tot_print_prod,0);; ?>&nbsp;</p></td>
                <td width="40"><p><? //echo number_format($sub_tot_batch_prod,0);; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_tot_print_revenue,0);; ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="100"><p> </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p>Grand Total&nbsp;</p></td>
                <td width="100"><p><? echo number_format($total_print_prod,0); ?></p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_print_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        
    </div>
       <?
	
exit();
}
if($action=="wash_prod_kal") //
{
		echo load_html_head_contents("Wash Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		$type_id=$type;
	//echo $cbo_company_id;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
		
	    $sql_fin_prod="SELECT a.embel_name,a.location,a.po_break_down_id,a.item_number_id,to_char(a.production_date,'DD-MON') as month_year,b.color_size_break_down_id,
	 (CASE WHEN   a.production_type=5 THEN b.production_qnty END) AS msew_out,
	 (CASE WHEN  a.embel_name=1 and a.production_type=3 THEN b.production_qnty END) AS print_recv,
	 (CASE WHEN  a.embel_name=2 and a.production_type=3 THEN b.production_qnty END) AS embro_recv,
	 (CASE WHEN a.embel_name=3 and a.production_type=3 THEN b.production_qnty END) AS wash_recv
	  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(3) and a.embel_name=3 and a.production_source in(1) and b.production_qnty>0  and a.location <> 0  and a.status_active=1  and b.status_active=1 order by a.location";
	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
	}
	
	 
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.po_quantity,b.pub_shipment_date,b.job_no_mst,b.grouping as ref_no,c.id as color_size_id,c.color_number_id from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where b.job_no_mst=a.job_no and c.po_break_down_id=b.id and c.job_no_mst=a.job_no and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_array[$val[csf('id')]]['buyer'] = $val[csf('buyer_name')];
		$po_array[$val[csf('id')]]['ref_no'] = $val[csf('ref_no')];
		$po_array[$val[csf('id')]]['style_ref_no'] = $val[csf('style_ref_no')];
	//	$po_color_array[$val[csf('color_size_id')]]['color'] = $val[csf('color_number_id')];
		
	}
	$cm_po_cond3 = str_replace("id", "c.po_break_down_id", $po_cond);
	
   $sql_pre_wash="SELECT b.emb_name, d.id,d.color_number_id,d.po_break_down_id as po_id,c.id as color_size_id,d.rate from wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cos_emb_co_avg_con_dtls d,wo_pre_cost_embe_cost_dtls b where d.job_id=a.id   and c.job_id=a.id and c.id=d.color_size_table_id and c.po_break_down_id=d.po_break_down_id and  b.job_id=a.id  and  b.job_id=c.job_id   and b.id=d.pre_cost_emb_cost_dtls_id   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.requirment>0 and d.rate>0 and b.emb_name=3 $cm_po_cond3 order by d.id asc";
	$sql_wash_result = sql_select($sql_pre_wash);
	foreach ($sql_wash_result as $val) 
	{
		$po_color_rate_array[$val[csf('emb_name')]][$val[csf('po_id')]][$val[csf('color_number_id')]]['rate'] = $val[csf('rate')];
		$po_color_array[$val[csf('color_size_id')]]['color'] = $val[csf('color_number_id')];
	}
	unset($sql_wash_result);
	
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_array[$val[csf('id')]]['sew_smv'] = $val[csf('sew_smv')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$pre_cost_array[$val[csf('id')]]['cost_per']=$val[csf('costing_per')];
	}  
	
	/*$condition= new condition();
	//$condition->company_name("=$cbo_company_id");
	if(isset($poIds))
	{
		$condition->po_id_in($poIds);
	}
	$condition->init();
	// var_dump($condition);
	$emblishment= new emblishment($condition);
	//echo $emblishment->getQuery();die;
//	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	//$emblishment_qty_arr_name=$emblishment->getQtyArray_by_orderAndEmbname();
	$wash= new wash($condition);
	$emblishment_costing_arr_wash=$wash->getAmountArray_by_orderAndEmbname();
	$emblishment_qty_arr_wash=$wash->getQtyArray_by_orderAndEmbname();*/
	
	//echo "<pre>";print_r($emblishment_costing_arr_name);die();
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	// $process_array=array(1,30,35);
	
	foreach ($sql_fin_prod_res as $val)   //Main Query
	{
		
			$buyerId=$po_array[$val[csf('po_break_down_id')]]['buyer'];
			//echo $buyerId.'d,';
			$ref_no=$po_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			$color_id=$po_color_array[$val[csf('color_size_break_down_id')]]['color'];
			$item_id=$val[csf('item_number_id')];//embel_name
			$po_color_rate=$po_color_rate_array[$val[csf('embel_name')]][$val[csf('po_break_down_id')]][$color_id]['rate'];
		 //echo $color_id.'='.$po_color_rate.',';
		//	$print_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][1];
			//$print_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][1];
			
			//$embro_cost=$emblishment_costing_arr_name[$val[csf('po_break_down_id')]][2];
			//$embro_qty=$emblishment_qty_arr_name[$val[csf('po_break_down_id')]][2];
			
			//$wash_cost=$emblishment_costing_arr_wash[$val[csf('po_break_down_id')]][3];
			//$wash_qty=$emblishment_qty_arr_wash[$val[csf('po_break_down_id')]][3];
			
			$dzn_qnty=0;//
			
			$costing_per_id=$pre_cost_array[$val[csf('po_break_down_id')]]['cost_per'];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			//echo $wash_cost.'='.$wash_qty.'='.$costing_per_id.'<br>';
			$wash_avg_rate=$po_color_rate/$dzn_qnty;
			$wash_amount=$val[csf('wash_recv')]*$wash_avg_rate;
			
			if($wash_cost>0)
			{
			//$wash_avg_rate=($wash_cost/$wash_qty)/$dzn_qnty;;
			//$wash_amount=$val[csf('wash_recv')]*$wash_avg_rate;
			$year_prod_cost_arr[$fyear]['wash_recv'] += $wash_amount;
			}
			if($wash_amount>0)
			{
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['buyer']= $val[csf('buyer_id')];
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['style_ref_no']= $style_ref_no;
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['wash_recv']+=$val[csf('wash_recv')];
			$print_prod_array[$buyerId][$ref_no][$item_id][$color_id]['wash_revenue']+=$wash_amount;
			}
			
	}
	//print_r($print_prod_array);
	//echo $po_ids;
	  $width_td="660";
	   ?>
       <div style="margin-left:50px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Revenue Wash.. </b> </caption>
       <tr>
           <td  width="100"> <b> </b> </td>
           <td  width="70"><? // echo $floor_arr[$floor_id]?></td>
           <td width="420" colspan="5"><b style="float:right"> Date: </b></td>
           <td width="70"> &nbsp; <? echo $gmt_date;?></td>
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
		<thead>
			<th width="100">Buyer</th>
			<th width="70">Ref no</th>
			<th width="100">Style</th>
			<th width="100">Gmts Item</th>
			<th width="70">Color/combo </th>
			<th width="100">Rcvd Qty Pcs </th>
            <th width="40">Rate</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($print_prod_array as $buyer_id=>$buyer_data)
			{
				$print_row_span=0;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			   foreach ($ref_data as $item_id=>$item_data)
			   {
				  foreach ($item_data as $color_id=>$row)
				  {
					  $print_row_span++;
				  }
			    $print_grp_row_arr[$buyer_id]=$print_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$total_print_revenue=$total_print_prod=$total_batch_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($print_prod_array as $buyer_id=>$buyer_data)
			{
				$b=1;
			 foreach ($buyer_data as $ref_no=>$ref_data)
			 {
			   foreach ($ref_data as $item_id=>$item_data)
			   {
				foreach ($item_data as $color_id=>$row)
			    {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				  
						if (!in_array($buyer_id,$sub_group_by_arr) )
						{
							if($k!=1)
							{
								 ?>
                              <tr class="tbl_bottom">
                                <td width="440" colspan="5"  align="right"> Buyer Total</td>
                                <td width="100"><p><? echo number_format($sub_tot_print_prod,0); ?>&nbsp;</p></td>
                                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                                <td width="" align="right"><p><? echo number_format($sub_tot_print_revenue,0); ?>&nbsp;</p></td>
                            </tr>
              <?			unset($sub_tot_print_prod);unset($sub_tot_print_revenue);
							}
							$sub_group_by_arr[]=$buyer_id;
							$k++;
						}
						
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                if($b==1)
					{
					?>
                    <td width="100"  rowspan="<? echo  $print_grp_row_arr[$buyer_id];?>"><p>&nbsp;<? echo $buyer_arr[$buyer_id]; ?></p></td>
					
                    <?
                  }?> 
					<td width="70" title="<? echo $buyer_id;?>"><? echo $ref_no; ?></td>
					<td width="100" style="word-break:break-all"><p>&nbsp;<? echo $row[('style_ref_no')] ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $color_arr[$color_id]; ?>&nbsp;</p></td>
					<td width="100" align="right"><p><? echo number_format($row['wash_recv'],0); ?>&nbsp;</p></td>
                   
                     <td width="40" title="Wash Amount/Rcvd Qty Pcs" align="right"><p><? echo number_format(($row['wash_revenue']/$row['wash_recv']),2); ?>&nbsp;</p></td>
                     <td width="" align="right"><p><? $embro_revenue=$row['wash_revenue'];echo number_format($embro_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$sub_tot_print_revenue+=$embro_revenue;
				$sub_tot_print_prod+=$row['wash_recv'];
				$total_print_revenue+=$embro_revenue;
				$total_print_prod+=$row['wash_recv'];
				
			    }
			   }
			 }
			}
			?>
             <tr class="tbl_bottom">
                <td width="440" colspan="5"  align="right"> Buyer Total</td>
                <td width="100"><p><? echo number_format($sub_tot_print_prod,0);; ?>&nbsp;</p></td>
                <td width="40"><p><? //echo number_format($sub_tot_batch_prod,0);; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_tot_print_revenue,0);; ?>&nbsp;</p></td>
          </tr>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="100"><p> </p></td>
                <td width="100"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p>Grand Total&nbsp;</p></td>
                <td width="100"><p><? echo number_format($total_print_prod,0); ?></p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_print_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        
    </div>
       <?
	
exit();
}

if($action=="ratanpur_floor_kal") // Ratanpur KAL Sewing
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("__",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_data=$ex_dara[2];
		$line_id=$ex_dara[3];
		$floor_ex_data=explode("_",$floor_data);
		$floor_id=$floor_ex_data[0];
		$floor_grp=$floor_ex_data[1];
		$type_id=$type;
		//echo $line_id.'='.$floor_grp;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
		foreach($sql_floor as $row )
		{
			$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
			$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
		}
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null and a.floor_name=$floor_id and a.sewing_group='$floor_grp' and a.id in($line_id) order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null and a.floor_name=$floor_id and a.sewing_group='$floor_grp' and a.id in($line_id) order by  a.id asc";
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$line_name_arr[$val[csf('id')]]=$val[csf('line_name')];
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==16)
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17)
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	if($prod_reso_allocation==1)
	{
	  $sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line,c.line_number,a.item_number_id as item_id,a.floor_id,a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id  and a.location=5 order by c.line_number";
	}
	else
	{
			$sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line as line_number,a.item_number_id as item_id,a.floor_id,a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.location=5 and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.grouping as ref_no,b.po_quantity,b.pub_shipment_date,b.shipment_date,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		$po_ref_array[$val[csf('id')]]['ref_no']= $val[csf('ref_no')];
		$po_ref_array[$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	} 
	$line_chk=rtrim($line_id,',');
	$line_chk_arr=explode(",",$line_chk);
	foreach ($sql_fin_prod_res as $val)  //Main Query
	{
			$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('msew_out')];
			$po_buyer=$po_buyer_array[$val[csf('po_break_down_id')]];
			$ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			//$line_number=$val[csf('line_number')];
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			//echo $cm_cost_method_based_on.'d';
			//print_r($line_numberArr);
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				//echo $sew_smv.'='.$val[csf('sew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
					$sew_out_cost=($sew_smv*$val[csf('sew_out')]*$cost_per_minute)/$exch_rate;
					//$year_location_qty_array[$fyear][$val[csf('location')]]['finishing'] += $finish_cost;
					foreach($line_numberArr as $lId)
					{
						//echo $lId.',A ';
						if(in_array($lId,$line_chk_arr))
						{
							//echo $lId.'B,';
						//echo $sew_out_cost.'d';
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_out']+= $val[csf('sew_out')];
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['revenue']+= $sew_out_cost;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['style_ref_no']= $style_ref_no;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_smv']= $sew_smv;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
						}
					}
			}
	}
	ksort($line_wise_array);
	//print_r($line_wise_array);
	
	// =================================== subcon kniting =============================
	  $width_td="780";
	   ?>
       <div style="margin-left:20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Garments Revenue-Sub Floor Wise	 </b> </caption>
       <tr>
        
           <td  width="100"> <b>Floor</b> </td>
           <td  width="70"><?  echo $floor_grp;?></td>
           <td width="390" colspan="6"><b style="float:right"> Date: </b></td>
           <td width="100"> &nbsp; <? echo $gmt_date;?></td>
            
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Own Job </b> </caption>
		<thead>
			<th width="70">Line no</th>
            <th width="100">Buyer</th>
			<th width="70">Ref No</th>
			<th width="150">Style Name</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">SMV</th>
			<th width="70">Produce Minute</th>
            <th width="40">CPM($)</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($line_wise_array as $line_id=>$line_data)
			{
				$line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $ref_no=>$ref_data)
			  {
				  foreach ($ref_data as $item_id=>$row)
				  {
					  $line_row_span++;
				  }
			 	 $line_row_arr[$line_id]=$line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$buyer_tot_gmt_revenue=$total_gmt_prod_min=$total_gmt_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($line_wise_array as $line_id=>$line_data)
			{
			 $b=1;
			 foreach ($line_data  as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data  as $ref_no=>$ref_data)
			  {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                   if($b==1)
					{
					?>
					<td width="70" rowspan="<? echo  $line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                    }?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
					<td width="70"><p>&nbsp;<? echo $ref_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['sew_out']; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row['sew_smv']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*SMV"><? $produce_min=$row['sew_out']*$row['sew_smv'];echo $produce_min;; ?></td>
                    <td width="40"><p><? echo number_format($row['cost_per_minute']/$exch_rate,2); ?>&nbsp;</p></td>
                    <td width="" align="right" title="Produce Min*CPM/Exchange Rae"><p><? 
					$tot_revenue=$row['revenue'];//($produce_min*$row['revenue'])/$exch_rate;
					echo number_format($tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$buyer_tot_gmt_revenue+=$tot_revenue;
				$buyer_tot_gmt_prod+=$row['sew_out'];
				$buyer_tot_gmt_prod_min+=$produce_min;
				
				$total_gmt_revenue+=$tot_revenue;
				$total_gmt_prod+=$row['sew_out'];
				$total_gmt_prod_min+=$produce_min;
			  }
			 }
			 }
			}
			?>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"></td>
                <td width="100"><p>Grand Total </p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod_min,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        <?
      if($prod_reso_allocation==1)
		{
		  $sql_sub_sewOut=" SELECT a.gmts_item_id,a.order_id,c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2  and a.location_id=5 and a.floor_id=$floor_id order by a.floor_id";
		  // $sql_sub_sewOut="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=$location_id and a.status_active=1 ";
		}
		else
		{
			$sql_sub_sewOut=" SELECT a.gmts_item_id,a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2  and a.location_id=5 order by a.floor_id";
		}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut2="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name,b.rate from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=5 and a.status_active=1  and a.floor_id=$floor_id ";
	$sql_sub_sewOut_result2 = sql_select($sql_sub_sewOut2);
	foreach($sql_sub_sewOut_result2 as $val)//subcon_job,job_no_mst
	{
		$sub_po_arr[$val[csf("order_id")]]['order_no']=$val[csf("order_no")];
		$sub_po_arr[$val[csf("order_id")]]['buyer_name']=$val[csf("buyer_name")];
		$sub_po_arr[$val[csf("order_id")]]['style_ref_no']=$val[csf("style_ref_no")];
		$sub_po_arr[$val[csf("order_id")]]['rate']=$val[csf("rate")];
	}
	//print_r($sub_po_arr);
	foreach($sql_sub_sewOut_result as $val)//subcon_job,job_no_mst
	{
			
			$fyear=$val[csf("month_year")];
			$order_no=$sub_po_arr[$val[csf("order_id")]]['order_no'];
			$style_ref_no=$sub_po_arr[$val[csf("style_ref_no")]]['style_ref_no'];
			$buyer_id=$sub_po_arr[$val[csf("order_id")]]['buyer_name'];
			$order_rate=$sub_po_arr[$val[csf("order_id")]]['rate'];	
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			//echo $order_rate.', ';		
			$subsewOut_cost =$order_rate*$val[csf('production_qnty')];
			if($subsewOut_cost>0)
			{
				foreach($line_numberArr as $lId)
					{
					if(in_array($lId,$line_chk_arr))
					{
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['production_qnty']+= $val[csf('production_qnty')];
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['revenue']+= $subsewOut_cost/$exch_rate;
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['style_ref_no']= $style_ref_no;
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['rate']= $order_rate;
					}
				 }
			}
	 }
	 ksort($sub_line_wise_array);
	// print_r($sub_line_wise_array);
	
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Subcontract Job </b> </caption>
		<thead>
			<th width="70">Line No</th>
            <th width="100">Factory Name</th>
			<th width="70">Order No</th>
			<th width="150">Customer Style</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">Pcs Rate<br>(Taka)</th>
			<th width="70">Total Taka</th>
            <th width="40">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
				$sub_line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				  foreach ($po_data as $item_id=>$row)
				  {
					  $sub_line_row_span++;
				  }
				  $sub_line_row_arr[$line_id]=$sub_line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$sub_total_gmt_revenue=$sub_total_gmt_prod=$sub_total_gmt_tk=0;
            $i=1;$k=1;$subcon_group_by_arr=array();
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
			$sb=1;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				 foreach ($po_data as $item_id=>$row)
			     {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	//line_name_arr
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 <?
                  if($sb==1)
					{
					?>
					<td width="70" rowspan="<? echo  $sub_line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                   }
					?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                   
					<td width="70"><p>&nbsp;<? echo $po_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row['production_qnty'],0); ?>&nbsp;</p></td>
					<td width="40" align="right"><p><? echo $row['rate']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*Order Rate"><? $tot_tk=$row['production_qnty']*$row['rate'];echo number_format($tot_tk,0); ?></td>
                    <td width="40" align="right"><p><? echo $exch_rate; ?>&nbsp;</p></td>
                    <td width="" align="right" title="Tot Tk/Exchange Rae"><p><? 
					$sub_tot_revenue=$row['revenue'];//$tot_tk/$exch_rate;// $sub_tot_revenue=$tot_tk/$exch_rate;
					echo number_format($sub_tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$sb++;
				
				$sub_buyer_tot_gmt_revenue+=$sub_tot_revenue;
				$sub_buyer_tot_gmt_tk+=$tot_tk;
				$sub_buyer_tot_prod+=$row['production_qnty'];
				
				$sub_total_gmt_revenue+=$sub_tot_revenue;
				$sub_total_gmt_prod+=$row['production_qnty'];
				$sub_total_gmt_tk+=$tot_tk;
			   }
			  }
			  
			 }
			}
			?>
            
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p> </p></td>
                <td width="100"><p>Grand Total</td>
                <td width="70"><p><? echo number_format($sub_total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo  number_format($sub_total_gmt_tk,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
    </div>
       <?
	
exit();
}

if($action=="ashulia_floor_kal") // Ashulia_floor_kal KAL Sewing
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("__",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		//echo $floor_id.'dDDDDD';
		//$floor_grp=$floor_ex_data[1]; 
		$type_id=$type;
		//echo $line_id.'='.$floor_grp;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
		foreach($sql_floor as $row )
		{
			$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
			$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
		}
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.location_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.floor_name=$floor_id   order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null and a.floor_name=$floor_id and a.sewing_group='$floor_grp' and a.id in($line_id) order by  a.id asc";
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$line_name_arr[$val[csf('id')]]=$val[csf('line_name')];
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==16)
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17)
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}
		
	if($prod_reso_allocation==1)
	{
	   $sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line,c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id  and a.location=3 order by a.floor_id";
	}
	else
	{
			$sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.location=3 and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.grouping as ref_no,b.po_quantity,b.pub_shipment_date,b.shipment_date,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		$po_ref_array[$val[csf('id')]]['ref_no']= $val[csf('ref_no')];
		$po_ref_array[$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";

	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	} 
	$line_chk=rtrim($line_id,',');
	$line_chk_arr=explode(",",$line_chk);
	foreach ($sql_fin_prod_res as $val)  //Main Query
	{
			$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('msew_out')];
			$po_buyer=$po_buyer_array[$val[csf('po_break_down_id')]];
			$ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			//$line_number=$val[csf('line_number')];
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			//echo $cm_cost_method_based_on.'d';
			//print_r($line_numberArr);
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				//echo $sew_smv.'='.$val[csf('sew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
					$sew_out_cost=($sew_smv*$val[csf('sew_out')]*$cost_per_minute)/$exch_rate;
					//$year_location_qty_array[$fyear][$val[csf('location')]]['finishing'] += $finish_cost;
					foreach($line_numberArr as $lId)
					{
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_out']+= $val[csf('sew_out')];
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['revenue']+= $sew_out_cost;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['style_ref_no']= $style_ref_no;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_smv']= $sew_smv;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
					}
			}
	}
	 ksort($line_wise_array);
	//print_r($line_wise_array);
	
	// =================================== subcon kniting =============================
	  $width_td="780";
	   ?>
       <div style="margin-left:20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Garments Revenue-Sub Floor Wise </b> </caption>
       <tr>
        
           <td  width="100"> <b>Floor</b> </td>
           <td  width="70"><?  echo $floor_arr[$floor_id];?></td>
           <td width="390" colspan="6"><b style="float:right"> Date: </b></td>
           <td width="100"> &nbsp; <? echo $gmt_date;?></td>
            
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Own Job </b> </caption>
		<thead>
			<th width="70">Line no</th>
            <th width="100">Buyer</th>
			<th width="70">Ref No</th>
			<th width="150">Style Name</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">SMV</th>
			<th width="70">Produce Minute</th>
            <th width="40">CPM($)</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($line_wise_array as $line_id=>$line_data)
			{
				$line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $ref_no=>$ref_data)
			  {
				  foreach ($ref_data as $item_id=>$row)
				  {
					  $line_row_span++;
				  }
			 	 $line_row_arr[$line_id]=$line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$buyer_tot_gmt_revenue=$total_gmt_prod_min=$total_gmt_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($line_wise_array as $line_id=>$line_data)
			{
			 $b=1;
			 foreach ($line_data  as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data  as $ref_no=>$ref_data)
			  {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                   if($b==1)
					{
					?>
					<td width="70" rowspan="<? echo  $line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                    }?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
					<td width="70"><p>&nbsp;<? echo $ref_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['sew_out']; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row['sew_smv']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*SMV"><? $produce_min=$row['sew_out']*$row['sew_smv'];echo $produce_min;; ?></td>
                    <td width="40"><p><? echo number_format($row['cost_per_minute']/$exch_rate,2); ?>&nbsp;</p></td>
                    <td width="" align="right" title="Produce Min*CPM/Exchange Rae"><p><? 
					$tot_revenue=$row['revenue'];//($produce_min*$row['revenue'])/$exch_rate;
					echo number_format($tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$buyer_tot_gmt_revenue+=$tot_revenue;
				$buyer_tot_gmt_prod+=$row['sew_out'];
				$buyer_tot_gmt_prod_min+=$produce_min;
				
				$total_gmt_revenue+=$tot_revenue;
				$total_gmt_prod+=$row['sew_out'];
				$total_gmt_prod_min+=$produce_min;
			  }
			 }
			 }
			}
			?>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"></td>
                <td width="100"><p>Grand Total </p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod_min,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        <?
      if($prod_reso_allocation==1)
		{
		  $sql_sub_sewOut=" SELECT a.gmts_item_id,a.order_id,c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2  and a.location_id=3 and a.floor_id=$floor_id order by a.floor_id";
		  // $sql_sub_sewOut="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=$location_id and a.status_active=1 ";
		}
		else
		{
			$sql_sub_sewOut=" SELECT a.gmts_item_id,a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2  and a.location_id=3 order by a.floor_id";
		}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut2="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name,b.rate from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=3 and a.status_active=1  and a.floor_id=$floor_id ";
	$sql_sub_sewOut_result2 = sql_select($sql_sub_sewOut2);
	foreach($sql_sub_sewOut_result2 as $val)//subcon_job,job_no_mst
	{
		$sub_po_arr[$val[csf("order_id")]]['order_no']=$val[csf("order_no")];
		$sub_po_arr[$val[csf("order_id")]]['buyer_name']=$val[csf("buyer_name")];
		$sub_po_arr[$val[csf("order_id")]]['style_ref_no']=$val[csf("style_ref_no")];
		$sub_po_arr[$val[csf("order_id")]]['rate']=$val[csf("rate")];
	}
	//print_r($sub_po_arr);
	foreach($sql_sub_sewOut_result as $val)//subcon_job,job_no_mst
	{
			
			$fyear=$val[csf("month_year")];
			$order_no=$sub_po_arr[$val[csf("order_id")]]['order_no'];
			$style_ref_no=$sub_po_arr[$val[csf("style_ref_no")]]['style_ref_no'];
			$buyer_name=$sub_po_arr[$val[csf("order_id")]]['buyer_name'];
			$order_rate=$sub_po_arr[$val[csf("order_id")]]['rate'];	
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			//echo $order_rate.', ';		
			$subsewOut_cost =$order_rate*$val[csf('production_qnty')];
			if($subsewOut_cost>0)
			{
				foreach($line_numberArr as $lId)
					{
					
					$sub_line_wise_array[$lId][$buyer_name][$order_no][$val[csf('gmts_item_id')]]['production_qnty']+= $val[csf('production_qnty')];
					$sub_line_wise_array[$lId][$buyer_name][$order_no][$val[csf('gmts_item_id')]]['revenue']+= $subsewOut_cost/$exch_rate;
					$sub_line_wise_array[$lId][$buyer_name][$order_no][$val[csf('gmts_item_id')]]['style_ref_no']= $style_ref_no;
					$sub_line_wise_array[$lId][$buyer_name][$order_no][$val[csf('gmts_item_id')]]['rate']= $order_rate;
					
				 }
			}
	 }
	  ksort($sub_line_wise_array);
	// print_r($sub_line_wise_array);
	
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Subcontract Job </b> </caption>
		<thead>
			<th width="70">Line No</th>
            <th width="100">Factory Name</th>
			<th width="70">Order No</th>
			<th width="150">Customer Style</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">Pcs Rate<br>(Taka)</th>
			<th width="70">Total Taka</th>
            <th width="40">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
				$sub_line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				  foreach ($po_data as $item_id=>$row)
				  {
					  $sub_line_row_span++;
				  }
				  $sub_line_row_arr[$line_id]=$sub_line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$sub_total_gmt_revenue=$sub_total_gmt_prod=$sub_total_gmt_tk=0;
            $i=1;$k=1;$subcon_group_by_arr=array();
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
			$sb=1;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				 foreach ($po_data as $item_id=>$row)
			     {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	//line_name_arr
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trsub_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trsub_<? echo $i; ?>" style="font-size:11px">
                 <?
                  if($sb==1)
					{
					?>
					<td width="70" rowspan="<? echo  $sub_line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                   }
					?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                   
					<td width="70"><p>&nbsp;<? echo $po_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row['production_qnty'],0); ?>&nbsp;</p></td>
					<td width="40" align="right"><p><? echo $row['rate']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*Order Rate"><? $tot_tk=$row['production_qnty']*$row['rate'];echo number_format($tot_tk,0); ?></td>
                    <td width="40" align="right"><p><? echo $exch_rate; ?>&nbsp;</p></td>
                    <td width="" align="right" title="Tot Tk/Exchange Rae"><p><? 
					$sub_tot_revenue=$row['revenue'];//$tot_tk/$exch_rate;// $sub_tot_revenue=$tot_tk/$exch_rate;
					echo number_format($sub_tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$sb++;
				
				$sub_buyer_tot_gmt_revenue+=$sub_tot_revenue;
				$sub_buyer_tot_gmt_tk+=$tot_tk;
				$sub_buyer_tot_prod+=$row['production_qnty'];
				
				$sub_total_gmt_revenue+=$sub_tot_revenue;
				$sub_total_gmt_prod+=$row['production_qnty'];
				$sub_total_gmt_tk+=$tot_tk;
			   }
			  }
			  
			 }
			}
			?>
            
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p> </p></td>
                <td width="100"><p>Grand Total</td>
                <td width="70"><p><? echo number_format($sub_total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo  number_format($sub_total_gmt_tk,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
    </div>
       <?
	
exit();
}


if($action=="gmt_floor_jm") // JM RMG
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$floor_id=$ex_dara[2];
		//echo $floor_id.'dDDDDD';
		//$floor_grp=$floor_ex_data[1]; 
		$type_id=$type;
		//echo $line_id.'='.$floor_grp;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
		foreach($sql_floor as $row )
		{
			$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
			$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
		}
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.floor_name=$floor_id   order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null and a.floor_name=$floor_id and a.sewing_group='$floor_grp' and a.id in($line_id) order by  a.id asc";
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$line_name_arr[$val[csf('id')]]=$val[csf('line_name')];
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			
		}
		
	if($prod_reso_allocation==1)
	{
	   $sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line,c.line_number,a.floor_id,a.item_number_id as item_id, a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id   order by a.floor_id";
	}
	else
	{
			$sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line as line_number,a.floor_id,a.item_number_id as item_id,a.po_break_down_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date='$gmt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)   and a.floor_id is not null and a.floor_id <> 0 and a.floor_id=$floor_id order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.grouping as ref_no,b.po_quantity,b.pub_shipment_date,b.shipment_date,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		$po_ref_array[$val[csf('id')]]['ref_no']= $val[csf('ref_no')];
		$po_ref_array[$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	} 
	$line_chk=rtrim($line_id,',');
	$line_chk_arr=explode(",",$line_chk);
	foreach ($sql_fin_prod_res as $val)  //Main Query
	{
			$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('msew_out')];
			$po_buyer=$po_buyer_array[$val[csf('po_break_down_id')]];
			$ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			//$line_number=$val[csf('line_number')];
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			//echo $cm_cost_method_based_on.'d';
			//print_r($line_numberArr);
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				//echo $sew_smv.'='.$val[csf('sew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
					$sew_out_cost=($sew_smv*$val[csf('sew_out')]*$cost_per_minute)/$exch_rate;
					//$year_location_qty_array[$fyear][$val[csf('location')]]['finishing'] += $finish_cost;
					foreach($line_numberArr as $lId)
					{
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_out']+= $val[csf('sew_out')];
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['revenue']+= $sew_out_cost;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['style_ref_no']= $style_ref_no;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_smv']= $sew_smv;
						$line_wise_array[$lId][$po_buyer][$ref_no][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
					}
			}
	}
	ksort($line_wise_array);
//	print_r($line_wise_array);
	
	// =================================== subcon kniting =============================
	  $width_td="780";
	   ?>
       <div style="margin-left:20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Garments Revenue-Sub Floor Wise </b> </caption>
       <tr>
        
           <td  width="100"> <b>Floor</b> </td>
           <td  width="70"><?  echo $floor_arr[$floor_id];?></td>
           <td width="390" colspan="6"><b style="float:right"> Date: </b></td>
           <td width="100"> &nbsp; <? echo $gmt_date;?></td>
            
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Own Job </b> </caption>
		<thead>
			<th width="70">Line no</th>
            <th width="100">Buyer</th>
			<th width="70">Ref No</th>
			<th width="150">Style Name</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">SMV</th>
			<th width="70">Produce Minute</th>
            <th width="40">CPM($)</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($line_wise_array as $line_id=>$line_data)
			{
				$line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $ref_no=>$ref_data)
			  {
				  foreach ($ref_data as $item_id=>$row)
				  {
					  $line_row_span++;
				  }
			 	 $line_row_arr[$line_id]=$line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$buyer_tot_gmt_revenue=$total_gmt_prod_min=$total_gmt_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($line_wise_array as $line_id=>$line_data)
			{
			 $b=1;
			 foreach ($line_data  as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data  as $ref_no=>$ref_data)
			  {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                   if($b==1)
					{
					?>
					<td width="70" rowspan="<? echo  $line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                    }?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
					<td width="70"><p>&nbsp;<? echo $ref_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['sew_out']; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row['sew_smv']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*SMV"><? $produce_min=$row['sew_out']*$row['sew_smv'];echo $produce_min;; ?></td>
                    <td width="40"><p><? echo number_format($row['cost_per_minute']/$exch_rate,2); ?>&nbsp;</p></td>
                    <td width="" align="right" title="Produce Min*CPM/Exchange Rae"><p><? 
					$tot_revenue=$row['revenue'];//($produce_min*$row['revenue'])/$exch_rate;
					echo number_format($tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$buyer_tot_gmt_revenue+=$tot_revenue;
				$buyer_tot_gmt_prod+=$row['sew_out'];
				$buyer_tot_gmt_prod_min+=$produce_min;
				
				$total_gmt_revenue+=$tot_revenue;
				$total_gmt_prod+=$row['sew_out'];
				$total_gmt_prod_min+=$produce_min;
			  }
			 }
			 }
			}
			?>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"></td>
                <td width="100"><p>Grand Total </p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod_min,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        <?
      if($prod_reso_allocation==1)
		{
		    $sql_sub_sewOut=" SELECT a.gmts_item_id,a.order_id,c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2   and a.floor_id=$floor_id order by a.floor_id";
		  // $sql_sub_sewOut="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=$location_id and a.status_active=1 ";
		}
		else
		{
			$sql_sub_sewOut=" SELECT a.gmts_item_id,a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and a.floor_id=$floor_id and a.production_date='$gmt_date'  and a.status_active=1 and a.production_type=2   order by a.floor_id";
		}
		//echo $sql_sub_sewOut.'XD';
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut2="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name,b.rate from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2  and a.status_active=1  ";
	$sql_sub_sewOut_result2 = sql_select($sql_sub_sewOut2);
	foreach($sql_sub_sewOut_result2 as $val)//subcon_job,job_no_mst
	{
		$sub_po_arr[$val[csf("order_id")]]['order_no']=$val[csf("order_no")];
		$sub_po_arr[$val[csf("order_id")]]['buyer_name']=$val[csf("buyer_name")];
		$sub_po_arr[$val[csf("order_id")]]['style_ref_no']=$val[csf("style_ref_no")];
		$sub_po_arr[$val[csf("order_id")]]['rate']=$val[csf("rate")];
	}
//	print_r($sql_sub_sewOut_result);
	foreach($sql_sub_sewOut_result as $val)//subcon_job,job_no_mst
	{
			
		
			$fyear=$val[csf("month_year")];
			$order_no=$sub_po_arr[$val[csf("order_id")]]['order_no'];
			$style_ref_no=$sub_po_arr[$val[csf("style_ref_no")]]['style_ref_no'];
			$buyer_id=$sub_po_arr[$val[csf("order_id")]]['buyer_name'];
			$order_rate=$sub_po_arr[$val[csf("order_id")]]['rate'];	
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			//echo $val[csf('line_number')].', ';		
			$subsewOut_cost =$order_rate*$val[csf('production_qnty')];
			if($subsewOut_cost>0)
			{
				foreach($line_numberArr as $lId)
					{
					
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['production_qnty']+= $val[csf('production_qnty')];
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['revenue']+= $subsewOut_cost/$exch_rate;
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['style_ref_no']= $style_ref_no;
					$sub_line_wise_array[$lId][$buyer_id][$order_no][$val[csf('gmts_item_id')]]['rate']= $order_rate;
					
				 }
			}
	 }
	 	ksort($sub_line_wise_array);
	// print_r($sub_line_wise_array);
	
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Subcontract Job </b> </caption>
		<thead>
			<th width="70">Line No</th>
            <th width="100">Factory Name</th>
			<th width="70">Order No</th>
			<th width="150">Customer Style</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">Pcs Rate<br>(Taka)</th>
			<th width="70">Total Taka</th>
            <th width="40">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
				$sub_line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				  foreach ($po_data as $item_id=>$row)
				  {
					  $sub_line_row_span++;
				  }
				  $sub_line_row_arr[$line_id]=$sub_line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$sub_total_gmt_revenue=$sub_total_gmt_prod=$sub_total_gmt_tk=0;
            $i=1;$k=1;$subcon_group_by_arr=array();
			foreach ($sub_line_wise_array as $line_id=>$line_data)
			{
			$sb=1;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				 foreach ($po_data as $item_id=>$row)
			     {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	//line_name_arr
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trD_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trD_<? echo $i; ?>" style="font-size:11px">
                 <?
                  if($sb==1)
					{
					?>
					<td width="70" rowspan="<? echo  $sub_line_row_arr[$line_id];?>" title="<? echo $line_id;?>"><? echo $line_name_arr[$line_id]; ?></td>
                    <?
                   }
					?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                   
					<td width="70"><p>&nbsp;<? echo $po_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row['production_qnty'],0); ?>&nbsp;</p></td>
					<td width="40" align="right"><p><? echo $row['rate']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*Order Rate"><? $tot_tk=$row['production_qnty']*$row['rate'];echo number_format($tot_tk,0); ?></td>
                    <td width="40" align="right"><p><? echo $exch_rate; ?>&nbsp;</p></td>
                    <td width="" align="right" title="Tot Tk/Exchange Rae"><p><? 
					$sub_tot_revenue=$row['revenue'];//$tot_tk/$exch_rate;// $sub_tot_revenue=$tot_tk/$exch_rate;
					echo number_format($sub_tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$sb++;
				
				$sub_buyer_tot_gmt_revenue+=$sub_tot_revenue;
				$sub_buyer_tot_gmt_tk+=$tot_tk;
				$sub_buyer_tot_prod+=$row['production_qnty'];
				
				$sub_total_gmt_revenue+=$sub_tot_revenue;
				$sub_total_gmt_prod+=$row['production_qnty'];
				$sub_total_gmt_tk+=$tot_tk;
			   }
			  }
			  
			 }
			}
			?>
            
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p> </p></td>
                <td width="100"><p>Grand Total</td>
                <td width="70"><p><? echo number_format($sub_total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo  number_format($sub_total_gmt_tk,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
    </div>
       <?
	
exit();
}
?>