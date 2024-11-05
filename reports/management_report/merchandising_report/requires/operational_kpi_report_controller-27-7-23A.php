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
	  $sql_po_plan="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id=48   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0    and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
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
							
						} 
						//Days loop end
				   } 
				   
				   
			} 
			//Month Loop end here //Month Loop
			
			
		}
		 //unit
		//  echo "<pre>";
		// print_r($po_alloQty_arr);
		 //  print_r($po_planQty_arr);
		//print_r($num_of_plan_days);
		 // echo "</pre>";
	}
	 //Company
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
					$num_of_plan_days[$comp_id][1][$yr_month]++;
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
					$comp_avg_perArr[$comp_id][$unit_id]+=$per/$num_of_plan_days[$comp_id][1][$monYr];
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
				        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trskpi_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trskpi_<? echo $i; ?>"> 
						<?
						if($j==1)
						{
							?>
								 <td rowspan="<? echo $companySpanArr[$com_id];?>"><? echo $company_arr[$com_id];?></td>
						<? }
						
				            
				           // $year_total = $fiscal_total+$main_array[$year]['dyeing']+$main_array[$year]['kniting'];
						   $unit_val_name="";
						   if($unit_id!=6)
						   {
							if($unit_id==1)
							{
								$unit_val_name= 'Tejgaon Yarn';
							}
							  if($com_id==2 && $unit_id==2)
							{
								$unit_val_name= 'Shafipur';
							}
							  if($com_id==2 && $unit_id==3)
							{
								$unit_val_name= 'Ashulia';
							}
							  if($com_id==2 && $unit_id==4)
							{
								$unit_val_name= 'Ratanpur';
							}

							  if($com_id==1 && $unit_id==2)
							{
								$unit_val_name= 'Nayapara Fabric';
							}
							  if($com_id==1 && $unit_id==3)
							{
								$unit_val_name= 'Nayapara Cut & Sew';
							}
							  if($com_id==1 && $unit_id==4)
							{
								$unit_val_name= 'Nayapara Seamless';
							}
							if($unit_id==5)
							{
								$unit_val_name= 'Merchandising';
							}
							
							
							//else $unit_val= $unit_val;
							?>
							  <td align="center" title="<?=$unit_id.'='.$com_key;?>"><a href="javascript:void()" onclick="report_generate_by_unit('<? echo $com_id.'_'.$unit_id.'_'.$year_mon;?>','1')"><? echo $unit_val_name;?></a></td>
							<?
						   }
						   else
						   { 
							  if($unit_id==6)
							{
								$unit_val_name= 'Average';
							}
							?>
								 <td align="center" bgcolor="#CCCCCC" > <? echo $unit_val_name;?></td>
						  <? }

							if($unit_id!=6)  
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
								 
								
								//Tejgoan Yarn
								$yarn_month_wise_kpi=0;
								if($unit_id==1)
								{
									$diff_days=$num_of_plan_days[$com_id][$unit_id][$year_mon];
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
	//unset($main_array);
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
$unitArray=array(1=>"Tejgaon Yarn",2=>"Shafipur",3=>"Ashulia",4=>"Ratanpur",5=>"Merchandising",6=>"Average");

if($action=="report_generate_by_year_date_wise") //Unit wise and Monthly Date Wise KPI PER%  
{
	$cbo_company_id = str_replace("'","",$company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$cbo_templete_id 	= str_replace("'","",$cbo_templete_id);
	$unit_id 			= str_replace("'","",$report_type);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	//$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	 //echo $cbo_company_id.'='.$cbo_templete_id.'='.$unit_type;die;
	 
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
	// echo $startDate.'='.$endDate;die(); 
	 $company_conds="";
	 if($cbo_company_id>0)
	 {
		$company_conds=" and a.company_name =$cbo_company_id";
	 }
	//$location_library = return_library_array("select id, location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	// ========================= for Plan ======================
	  $sql_po_plan="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id=48   and c.task_type=1 and c.plan_qty>0  and  b.status_active=1 and b.is_deleted=0  and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
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
		$plan_qty_array[$unit_id][$plandate][$val['POID']][$val['COMPANY_ID']]['planQty']+=$val['PLAN_QTY'];
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
	}
	unset($sql_po_plan_result_current);
   
	$sql_po_allocate="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.QNTY,c.allocation_date as ALLOC_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, inv_material_allocat_hystory c where a.id=b.job_id and b.id=c.po_break_down_id and  b.status_active=1 and b.is_deleted=0  and c.allocation_date between '$startDate' and '$endDate' $company_conds order by c.allocation_date asc"; 
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
		$plan_qty_array[$unit_id][$allocatedate][$val['POID']][$val['COMPANY_ID']]['alloQty']+=$val['QNTY'];
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

	
	$avg_company_kip_perArr=array();$tot_avg_kpiArr=array();//$avg_allo_plan_Day_wise_calArr=array();
	foreach ($company_wise_arr as   $com_id) 
	{
		//foreach ($unitArray[$unit_type] as $unit_id) 
		//{	
			
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
					//if($unit_id==1)
					//{
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
									$planQtycal=$plan_qty_array[$unit_id][$newdate][$poid][$com_id]['planQty'];//Plan
									
									if($plan_qty_array[$unit_id][$newdate][$poid][$com_id]['alloQty']!="" || $planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										
										$po_alloQty_arr[$newdate][$poid]=$alloQty+$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
										
									}
									
								}
								
							} 
							
						} 
						//Days loop end
				  // } 
				   
				   
			} 
			//Month Loop end here //Month Loop
			
			
		//}
		 //unit
		//  echo "<pre>";
		// print_r($po_alloQty_arr);
		 //  print_r($po_planQty_arr);
		//print_r($num_of_plan_days);
		 // echo "</pre>";
	}
	 //Company
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
								$planQtycal=$plan_qty_array[$unit_id][$day_all][$poid][$comp_id]['planQty'];//alloQty
								//$kom_kpi_per=$po_alloQty_arr[$day_all][$poid]/$po_planQty_arr[$day_all][$poid]*100;
								//$kom_kpi_per = ($kom_kpi_per>100)?100:$kom_kpi_per;
								if($planQtycal>0)
								{
									//$mon_wiseAlloPlanQty_tillCalculateArr[$day_all]['alloc_qty']+=$po_alloQty_arr[$day_all][$poid];
									$mon_alloc_qty=$po_alloQty_arr[$day_all][$poid];
									$month_wise_kpiArr[$comp_id][$unit_id][$day_all]['allo']+=$mon_alloc_qty;
								}
								$month_wise_kpiArr[$comp_id][$unit_id][$day_all]['plan']+=$po_planQty_arr[$day_all][$poid];
							//	$mon_wise_allocateQty_tillCalculateArr[$day_all]+=$po_alloQty_arr[$day_all][$poid];
							 
						 }
					 //}
					
				}
		 }
	   }
	   //echo "<pre>";
	  // print_r($month_wise_kpiArr);

	   foreach($month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
		foreach($comData as $unit_id=>$unitData) //Month Wise KPI Percentage summary part
	    {
		   foreach($unitData as $day_key=>$row)
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
				$yarn_month_wise_kpiArr[$comp_id][$unit_id][$yr_month]+=$month_kpi_per;
				}
				if($month_plan>0)
				{
					$num_of_plan_daysArr[$comp_id][$unit_id][$yr_month]++;
				}
				
		   }
		}
			   
	   }
	 // print_r($num_of_plan_daysArr);
	   $comp_avg_perArr=array();
	   foreach($yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
			foreach($comData as $unit_id=>$monData) //Month Wise KPI Percentage summary part
			{
				foreach($monData as $monYr=>$per) //Month Wise KPI Percentage summary part
				{
					$comp_avg_perArr[$comp_id][$unit_id]+=$per/$num_of_plan_days[$comp_id][$unit_id][$monYr];
				}
			}

	   }
	    // echo "<pre>";
	  // print_r($comp_avg_perArr);
	  //print_r($mon_wise_allocateQty_tillCalculateArr);
	    //  echo "<pre>"; 
//die;

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

	   $tbl_width_unit = 80+(count($fiscalMonth_arr)*80);
		?>
          <table width="<? echo $tbl_width_unit;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title.'<br>'.$company_arr[$cbo_company_id];?> </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width_unit;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'kpi_unit_report', '')"> -<b>KPI Unit  <?=$unitArray[$unit_id]?>[<? echo $from_year; ?>]</b></h3>
	    <div id="kpi_unit_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width_unit;?>" cellpadding="0" cellspacing="0">
		 
	        <thead>
	            
	            <th width="80">Date </th>
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
				 
					 //$total_avg_kpi=0;$tot_avg_kpiArr=array();$tot_mon_kpiPerArr=array();$tot_overall_kpiPerArr=array();$tot_avg_kpiArr=array();
		        	 
						$i=1;$tot_days=31;
						$date_wise_KPIArr=array();
						for($j=1;$j<=$tot_days;$j++)	 
						{	 
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$day=str_pad($j, 2, '0', STR_PAD_LEFT);       		
			        	?>     
				        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trskpiunit_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trskpiunit_<? echo $i; ?>">
						 
						<td align="center"> <? echo $day; ?> </td>
						<?
						 foreach ($fiscalMonth_arr as $year_mon => $val) 
						 {
						  $mon_yr=date('M-Y',strtotime($year_mon));
						 $date=$day.'-'.$mon_yr;
						 $day_allocat_qty= $month_wise_kpiArr[$cbo_company_id][$unit_id][$date]['allo'];
						 $day_plan_qty= $month_wise_kpiArr[$cbo_company_id][$unit_id][$date]['plan'];

						
						 $monthYr=date("M-Y",strtotime($day_key));
					 // $yr_month=strtoupper($monthYr);
					  
					  //$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
					  $month_kpi_per=0;
					  if($day_allocat_qty>0 && $day_plan_qty>0)
					  {
						$month_kpi_per=$day_allocat_qty/$day_plan_qty*100;  
						
					  }
					  if($month_kpi_per>100)  $month_kpi_per=100;

						?>
						<td width="80" align="center" title="<?=$day_allocat_qty.'/'.$day_plan_qty.'*100';?>"><?  echo fn_number_format($month_kpi_per,2).'%';?></td>
						<?
							if($month_kpi_per>0)
							{
							$date_wise_KPIArr[$unit_id][$year_mon]+=$month_kpi_per;
							}
						 }
						?>
						
				        </tr> 
				        <?
						 $i++;
						 
						
				       } //Month Loop end here
					 
				    
				 
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Average</th>

				<?
				//$num_of_plan_daysArr[$comp_id][$unit_id][$yr_month]
				//echo "<pre>";
				//print_r($date_wise_KPIArr);
				
				foreach ($fiscalMonth_arr as $year_mon => $val) 
	            {
					$tot_mon_days=0;
					if($num_of_plan_daysArr[$cbo_company_id][$unit_id][$year_mon]>0)
					{
						$tot_mon_days=$num_of_plan_daysArr[$cbo_company_id][$unit_id][$year_mon];
						//echo $tot_days.', ';
					}
					
					$tot_kpi_per=0;$tot_avg_kpi_per=0;
					$tot_kpi_per=$date_wise_KPIArr[$unit_id][$year_mon];
					if($tot_kpi_per>0)
					{
						$tot_avg_kpi_per=$tot_kpi_per/$tot_mon_days;
					}
					
					?>
                <th width="80" align="center" title="tot Days="<?=$tot_mon_days;?>><? echo fn_number_format($tot_avg_kpi_per,2).'%';?></th>
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

?>