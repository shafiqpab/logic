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
$today_date=date('d-M-Y');

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
	 $sql_po_plan="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(48,84,122,86,88)   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and b.id in(20328,20329,20330)   and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result = sql_select($sql_po_plan);
	foreach ($sql_po_plan_result as $val) 
	{
		if($val['TASK_ID']==48) // for Yarn
		{
			$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
			$plandate=strtotime($val['PLAN_DATE']);
			 $plan_poIdArr[$val['POID']]=$val['POID'];
		 	$com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		//===========Tejgoan Yarn End=================
		if($val['COMPANY_ID']==2 && ($val['LOCATION_NAME']==3 || $val['LOCATION_NAME']==5) ) //Location Ashulia//Kal 
		{
			$poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			
			if($val['TASK_ID']==84) //Cutting qc for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_cut_Qc_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==122) //Input for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_input_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==86) //Output  for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_output_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==88) //Gmts Fin  for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_fin_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
	    }
		
		
	}
	
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=89");
	 //oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 1, $plan_poIdArr, $empty_arr);//PO ID Ref from=1
  $sql_po_plan_current="SELECT a.company_name as COMPANY_ID,b.id as POID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and c.task_id=48   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 $company_conds and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
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
   
	$sql_po_allocate="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.QNTY,c.allocation_date as ALLOC_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, inv_material_allocat_hystory c where a.id=b.job_id and b.id=c.po_break_down_id and b.id in(20328,20329,20330)  and  b.status_active=1 and b.is_deleted=0   and c.allocation_date between '$startDate' and '$endDate' $company_conds order by c.allocation_date asc"; 
	$sql_po_allocate_result = sql_select($sql_po_allocate); //and b.id in(20325,20326,20327,20328,20329,20330)
	foreach ($sql_po_allocate_result as $val) 
	{
		$allocat_poIdArr[$val['POID']]=$val['POID'];
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
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
	
	//===================Allocation Wise Calculation Sql End=======

	//==========================******************Ashulia*************************************==================
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 3, $plan_cut_Qc_poIdArr, $empty_arr);//PO ID Ref from=3
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 4, $plan_input_poIdArr, $empty_arr);//PO ID Ref from=4
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 5, $plan_output_poIdArr, $empty_arr);//PO ID Ref from=5
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 6, $plan_fin_poIdArr, $empty_arr);//PO ID Ref from=6
	
	  $sql_po_plan_ashulia="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
	(case when  c.task_id=84  and g.ref_from=3  then c.PLAN_QTY else 0 end) as QC_PLAN_QTY,
	(case when  c.task_id=122  and g.ref_from=4  then c.PLAN_QTY else 0 end) as INPUT_PLAN_QTY,
	(case when  c.task_id=86  and g.ref_from=5  then c.PLAN_QTY else 0 end) as OUT_PLAN_QTY,
	(case when  c.task_id=88  and g.ref_from=6  then c.PLAN_QTY else 0 end) as FIN_PLAN_QTY 
	from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and a.location_name in(3,5)   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(3,4,5,6) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $company_conds and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc";
	 //and b.id in(20325,20326,20327,20328,20329,20330)
	  $sql_po_plan_result_ashulia = sql_select($sql_po_plan_ashulia);
	 $ashulia_plan_qty_array=array(); $ashulia_in_plan_qty_array=array(); $ashulia_out_plan_qty_array=array(); $ashulia_gFin_plan_qty_array=array();
	  $rat_ashulia_plan_qty_array=array(); $rat_ashulia_in_plan_qty_array=array(); $rat_ashulia_out_plan_qty_array=array(); $rat_ashulia_gFin_plan_qty_array=array();
	  foreach ($sql_po_plan_result_ashulia as $val) 
	  {
		  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		
		  //echo $plandate.'<br>';;
		  if($val['COMPANY_ID']==2 ) //Kal Ashulia
		  {
			
			  if($val['LOCATION_NAME']==3 )
			  {
						$poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
						 $loc_id=$val['LOCATION_NAME']; 
						if($val['QC_PLAN_QTY']>0 && $val['TASK_ID']==84 && $val['REF_FROM']==3) //QC
						{
							$ashulia_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['QC_PLAN_QTY'];
						}
						if($val['INPUT_PLAN_QTY']>0 && $val['TASK_ID']==122 && $val['REF_FROM']==4) //Input
						{
							$ashulia_in_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['input_planQty']+=$val['INPUT_PLAN_QTY'];
						}
						if($val['OUT_PLAN_QTY']>0 && $val['TASK_ID']==86 && $val['REF_FROM']==5) //Output
						{
							$ashulia_out_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['out_planQty']+=$val['OUT_PLAN_QTY'];
						}
						if($val['FIN_PLAN_QTY']>0 && $val['TASK_ID']==88 && $val['REF_FROM']==6) //Fin
						{
							$ashulia_gFin_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['fin_planQty']+=$val['FIN_PLAN_QTY'];
						}
			  }
			  else //Ratanpur
			  {
				 
						$poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
						 $rat_loc_id=$val['LOCATION_NAME']; 
						if($val['QC_PLAN_QTY']>0 && $val['TASK_ID']==84 && $val['REF_FROM']==3) //QC
						{
							$rat_ashulia_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['QC_PLAN_QTY'];
						}
						if($val['INPUT_PLAN_QTY']>0 && $val['TASK_ID']==122 && $val['REF_FROM']==4) //Input
						{
							$rat_ashulia_in_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['input_planQty']+=$val['INPUT_PLAN_QTY'];
						}
						if($val['OUT_PLAN_QTY']>0 && $val['TASK_ID']==86 && $val['REF_FROM']==5) //Output
						{
							$rat_ashulia_out_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['out_planQty']+=$val['OUT_PLAN_QTY'];
						}
						if($val['FIN_PLAN_QTY']>0 && $val['TASK_ID']==88 && $val['REF_FROM']==6) //Fin
						{
							$rat_ashulia_gFin_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['fin_planQty']+=$val['FIN_PLAN_QTY'];
						}
				    
			    }
		 }
		 
		//  $company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
	  }
	  unset($sql_po_plan_result_ashulia);
	  // print_r($rat_ashulia_plan_qty_array);

	     $sql_po_prod_ashulia="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(1,4,5,8) and a.location_name in(3,5)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20328,20329,20330) and c.production_date between '$startDate' and '$endDate' $company_conds order by c.PRODUCTION_DATE asc"; //and a.location_name=3
	    
		$sql_po_result_prod_ash = sql_select($sql_po_prod_ashulia);
		foreach ($sql_po_result_prod_ash as $val) 
		{
			if($val['COMPANY_ID']==2) //Kal Ashulia
			{
			$prod_poIdArr[$val['POID']]=$val['POID'];
			$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 7, $prod_poIdArr, $empty_arr);//PO ID Ref from=7

		$sql_po_prod_ashulia_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
	  (case when  c.production_type=1    then c.PRODUCTION_QUANTITY else 0 end) as PROD_QC,
	  (case when  c.production_type=4   then c.PRODUCTION_QUANTITY else 0 end) as INPUT_PROD,
	  (case when  c.production_type=5     then c.PRODUCTION_QUANTITY else 0 end) as OUT_PROD,
	  (case when  c.production_type=8     then c.PRODUCTION_QUANTITY else 0 end) as FIN_PROD 
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
	  and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(7) and g.entry_form=89 and a.location_name in(3,5)
	   and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' $company_conds order by c.PRODUCTION_DATE asc";// and a.location_name=3
	   //and b.id in(20325,20326,20327,20328,20329,20330)
		$sql_po_result_prod_curr = sql_select($sql_po_prod_ashulia_curr);
		foreach ($sql_po_result_prod_curr as $val) 
		{
			if($val['COMPANY_ID']==2) //Kal Ashulia 
			{
				
				if($val['LOCATION_NAME']==3) 
				{
					$poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
					
					$loc_id=$val['LOCATION_NAME'];
					$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
					if($val['PRODUCTION_TYPE']==1)
					{
						//echo $loc_id.'='.$val['POID'].'='.$val['PROD_QC'].'<br>';
						$ashulia_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_qc']+=$val['PROD_QC'];
					}
					if($val['PRODUCTION_TYPE']==4)
					{
						$ashulia_in_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_in']+=$val['INPUT_PROD'];
					}
					if($val['PRODUCTION_TYPE']==5)
					{
						$ashulia_out_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_out']+=$val['OUT_PROD'];
					}
					if($val['PRODUCTION_TYPE']==8)
					{
						$ashulia_gFin_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_fin']+=$val['FIN_PROD'];
					}
		  	  }
			  else //Ratanpur
			  {
				  $poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
				
					$rat_loc_id=$val['LOCATION_NAME'];
					$propddate_cal=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
					if($val['PRODUCTION_TYPE']==1)
					{
						//echo $loc_id.'='.$val['POID'].'='.$val['PROD_QC'].'<br>';
						$rat_ashulia_plan_qty_array[4][$propddate_cal][$val['POID']][$val['COMPANY_ID']]['prod_qc']+=$val['PROD_QC'];
					}
					if($val['PRODUCTION_TYPE']==4)
					{
						$rat_ashulia_in_plan_qty_array[4][$propddate_cal][$val['POID']][$val['COMPANY_ID']]['prod_in']+=$val['INPUT_PROD'];
					}
					if($val['PRODUCTION_TYPE']==5)
					{
						$rat_ashulia_out_plan_qty_array[4][$propddate_cal][$val['POID']][$val['COMPANY_ID']]['prod_out']+=$val['OUT_PROD'];
					}
					if($val['PRODUCTION_TYPE']==8)
					{
						$rat_ashulia_gFin_plan_qty_array[4][$propddate_cal][$val['POID']][$val['COMPANY_ID']]['prod_fin']+=$val['FIN_PROD'];
					}
			  }
			}
		}
		 unset($sql_po_result_prod_curr);
		 
		 //============********JM Nayapara Start here*****===========================
		 //============************************JM *******************************************
		 
	   $sql_po_plan_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(84,122,86,88,90) and a.location_name=6  and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337) and c.PLAN_DATE between '$startDate' and '$endDate'  order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result_jm = sql_select($sql_po_plan_jm);
	foreach($sql_po_plan_result_jm  as $val)
	{
		if($val['COMPANY_ID']==1) //Location //JM 
		{
			$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
			$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			if($val['TASK_ID']==84) //Cutting qc for  Nayapara
			{
				$jm_plan_cut_Qc_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==122) //Input for  Nayapara
			{
				$jm_plan_input_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==86) //Output  for  Nayapara
			{
				$jm_plan_output_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==88) //Gmts Fin  for  Nayapara
			{
				
				$jm_plan_fin_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==90) //Gmts Wash  for  Nayapara
			{
				
				$jm_plan_wash_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
	    }
			
	}
 $sql_actual_po_plan_jm="SELECT c.id as acul_poid,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name=6    and c.acc_po_qty>0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337) and c.acc_ship_date between '$startDate' and '$endDate'  order by acul_poid, PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_plan_result_jm = sql_select($sql_actual_po_plan_jm);
	foreach($sql_actual_po_plan_result_jm  as $val)
	{
		$jm_main_plan_wash_poIdArr[$val['POID']]=$val['POID'];
		$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}
	
	//============================Jm Plan============================
	//==========================******************Nayapara*************************************==================
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 9, $jm_plan_cut_Qc_poIdArr, $empty_arr);//PO ID Ref from=9
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 10, $jm_plan_input_poIdArr, $empty_arr);//PO ID Ref from=10
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 11, $jm_plan_output_poIdArr, $empty_arr);//PO ID Ref from=11
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 12, $jm_plan_fin_poIdArr, $empty_arr);//PO ID Ref from=12
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 13, $jm_plan_wash_poIdArr, $empty_arr);//PO ID Ref from=13

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 15, $jm_main_plan_wash_poIdArr, $empty_arr);//PO ID Ref from=15

	// for actual po ship qty
	 $sql_actual_qty_po_plan_jm="SELECT c.id as ACUL_POID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.ACC_PO_NO, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c, gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name=6  and c.acc_po_qty>0 and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(15) and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337)  and c.ACC_SHIP_DATE <= '$endDate'  order by  PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_qty_plan_result_jm = sql_select($sql_actual_qty_po_plan_jm);
	foreach($sql_actual_po_qty_plan_result_jm  as $val)
	{
		$plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		$loc_id=$val['LOCATION_NAME']; 
		//$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['PLAN_QTY'];
		$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
		$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];

		$nay_acl_delivery_qty_array[$loc_id][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];

		$acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];

		$acl_jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];

	}
	//echo "<pre>";
 //print_r($acl_jm_com_poIdArr);
 $sql_actual_po_delivery_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 pro_ex_factory_mst c,pro_ex_factory_actual_po_details d  where a.id=b.job_id and b.id=c.po_break_down_id
  and c.id=d.mst_id and a.company_name=1  and a.location_name=6 and d.ex_fact_qty>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
 and a.company_name=1 and b.id in(20334,20335,20337) and c.ex_factory_date between '$startDate' and '$endDate'  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_result_jm = sql_select($sql_actual_po_delivery_jm);
	foreach($sql_actual_po_delivery_result_jm  as $val)
	{
		$jm_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 16, $jm_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=16

	 $sql_actual_po_qty_delivery_jm="SELECT d.actual_po_id as ACTUAL_PO_ID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, d.ex_fact_qty as DEL_QTY,c.ex_factory_date as EX_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, pro_ex_factory_mst c,pro_ex_factory_actual_po_details d,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id  and c.id=d.mst_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(16) and g.entry_form=89  and a.location_name=6 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and d.ex_fact_qty>0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337) and c.ex_factory_date<= '$endDate'  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_qty_result_jm = sql_select($sql_actual_po_qty_delivery_jm);
	foreach ($sql_actual_po_delivery_qty_result_jm as $val) 
	{
		$ex_date=date('d-M-Y',strtotime($val['EX_DATE']));
	  	$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		 $loc_id=$val['LOCATION_NAME'];
		// $nay_actual_poIdplan_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['acul_poid'].',';
		 $nay_acl_delivery_qty_array[$loc_id][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		 $nay_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		 $nay_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
	}
	//print_r($nay_actual_plan_qty_array);

//=======Delivery===============
	
   $sql_po_plan_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
	(case when  c.task_id=84  and g.ref_from=9  then c.PLAN_QTY else 0 end) as QC_PLAN_QTY,
	(case when  c.task_id=122  and g.ref_from=10  then c.PLAN_QTY else 0 end) as INPUT_PLAN_QTY,
	(case when  c.task_id=86  and g.ref_from=11  then c.PLAN_QTY else 0 end) as OUT_PLAN_QTY,
	(case when  c.task_id=88  and g.ref_from=12  then c.PLAN_QTY else 0 end) as FIN_PLAN_QTY,
	(case when  c.task_id=90  and g.ref_from=13  then c.PLAN_QTY else 0 end) as WASH_PLAN_QTY 
	from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and a.company_name=1 and a.location_name in(6)   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(9,10,11,12,13) and  c.task_id in(84,122,86,88,90) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc";
	 //and b.id in(20325,20326,20327,20328,20329,20330)
	  $sql_po_plan_result_jm = sql_select($sql_po_plan_jm);
	 $jm_plan_qty_array=array(); $jm_in_plan_qty_array=array(); $jm_out_plan_qty_array=array(); $jm_gFin_plan_qty_array=array();
	 
	  foreach ($sql_po_plan_result_jm as $val) 
	  {
		  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			
			 $loc_id=$val['LOCATION_NAME']; 
			if($val['QC_PLAN_QTY']>0 && $val['TASK_ID']==84 && $val['REF_FROM']==9) //QC
			{
				$nay_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['QC_PLAN_QTY'];
				//echo $plandate.'=='.$val['QC_PLAN_QTY'].'<br>';
			}
			if($val['INPUT_PLAN_QTY']>0 && $val['TASK_ID']==122 && $val['REF_FROM']==10) //Input
			{
				$nay_in_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['input_planQty']+=$val['INPUT_PLAN_QTY'];
			}
			if($val['OUT_PLAN_QTY']>0 && $val['TASK_ID']==86 && $val['REF_FROM']==11) //Output
			{
				$nay_out_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['out_planQty']+=$val['OUT_PLAN_QTY'];
			}
			if($val['FIN_PLAN_QTY']>0 && $val['TASK_ID']==88 && $val['REF_FROM']==12) //Fin
			{
				$nay_gFin_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['fin_planQty']+=$val['FIN_PLAN_QTY'];
			}
			if($val['WASH_PLAN_QTY']>0 && $val['TASK_ID']==90 && $val['REF_FROM']==13) //Wash
			{
				$nay_gWash_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['wash_planQty']+=$val['WASH_PLAN_QTY'];
			}

	  }
	  unset($sql_po_plan_result_jm);
	 //  print_r($company_wise_arr);
	  
	     $sql_po_prod_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(1,3,4,5,8) and a.location_name in(6)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20334,20335,20337) and a.company_name=1 and c.production_date between '$startDate' and '$endDate' and c.production_type in(1,4,5,8,3)  order by c.PRODUCTION_DATE asc"; //and a.location_name=3
	    
		$sql_po_result_prod_jm = sql_select($sql_po_prod_jm);
		foreach ($sql_po_result_prod_jm as $val) 
		{
			if($val['COMPANY_ID']==1) //JM
			{
			$jm_prod_poIdArr[$val['POID']]=$val['POID'];
			$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 14, $jm_prod_poIdArr, $empty_arr);//PO ID Ref from=14

	  $sql_po_prod_jm_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.EMBEL_NAME,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
	  (case when  c.production_type=1    then c.PRODUCTION_QUANTITY else 0 end) as PROD_QC,
	  (case when  c.production_type=4   then c.PRODUCTION_QUANTITY else 0 end) as INPUT_PROD,
	  (case when  c.production_type=5     then c.PRODUCTION_QUANTITY else 0 end) as OUT_PROD,
	  (case when  c.production_type=8     then c.PRODUCTION_QUANTITY else 0 end) as FIN_PROD ,
	  (case when  c.production_type=3 and c.embel_name=3  then c.PRODUCTION_QUANTITY else 0 end) as WASH_PROD
	   
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
	  and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(14) and g.entry_form=89 and a.location_name in(6)
	   and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0 and c.production_type in(1,4,5,8,3) and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' order by c.PRODUCTION_DATE asc";// and a.location_name=3
	   //and b.id in(20325,20326,20327,20328,20329,20330)
		$sql_po_result_prod_jm_curr = sql_select($sql_po_prod_jm_curr);
		foreach ($sql_po_result_prod_jm_curr as $val) 
		{
			 
			$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			$loc_id=$val['LOCATION_NAME'];
			$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
			if($val['PRODUCTION_TYPE']==1)
			{
				//echo $loc_id.'='.$val['POID'].'='.$val['PROD_QC'].'<br>';
				$nay_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_qc']+=$val['PROD_QC'];
			}
			if($val['PRODUCTION_TYPE']==4)
			{
				$nay_in_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_in']+=$val['INPUT_PROD'];
			}
			if($val['PRODUCTION_TYPE']==5)
			{
				$nay_out_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_out']+=$val['OUT_PROD'];
			}
			if($val['PRODUCTION_TYPE']==8)
			{
				$nay_gFin_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_fin']+=$val['FIN_PROD'];
			}
			if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==3)
			{
				$nay_gWash_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_wash']+=$val['WASH_PROD'];
			}
		}
		 unset($sql_po_result_prod_jm_curr);
	
//===================sql JM Nayapara end ======================
   // echo "<pre>";
   //  print_r($rat_ashulia_plan_qty_array);
  // die;
 //ksort($till_today_AllQtyArr);
 // =============================Calculation of KPI============= //plan asy  allocatio ni  day dorbo
 $company_kip_cal_Arr=array();
	ksort($plan_qty_array);
	
	ksort($ashulia_plan_qty_array);
	ksort($ashulia_in_plan_qty_array);
	ksort($ashulia_out_plan_qty_array);
	ksort($ashulia_gFin_plan_qty_array);
	
	ksort($rat_ashulia_plan_qty_array);
	ksort($rat_ashulia_in_plan_qty_array);
	ksort($rat_ashulia_out_plan_qty_array);
	ksort($rat_ashulia_gFin_plan_qty_array);
	
	ksort($nay_plan_qty_array);
	ksort($nay_in_plan_qty_array);
	ksort($nay_out_plan_qty_array);
	ksort($nay_gFin_plan_qty_array);
	ksort($nay_gWash_plan_qty_array);
	ksort($nay_actual_plan_qty_array);
 

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
									$po_planQty_arr[$newdate][$poid]=$planQty+$po_planQty_arr[$prev_date[$poid]][$poid];
									$prev_date[$poid] = $newdate;
								}
								if($planQty>0 || $alloQty>0)
								{
									if($planQty>0 && $alloQty==0)
									{
										$po_alloQty_arr[$newdate][$poid]=$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
										//echo "A=".$poid;
									}
									 
									if($planQty==0 && $alloQty>0) 
									{
										$po_alloQty_arr[$newdate][$poid]=$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
									}
									$planQtycal=0;
									$planQtycal=$plan_qty_array[1][$newdate][$poid][$com_id]['planQty'];//Plan
									
									if($plan_qty_array[$unit_id][$newdate][$poid][$com_id]['alloQty']!="" || $planQtycal>0)
									{
										$po_alloQty_arr[$newdate][$poid]=$alloQty+$po_alloQty_arr[$prev_allocation_date[$poid]][$poid];
										$prev_allocation_date[$poid] = $newdate;
										
									}
									
								}
								
							} 
							
						} 
						//Days loop end
				    } 
					//============Ashulia Location============
					//==================Qc Prod=======================
					if($unit_id==3 && $com_id==2) //Kal Ashulia
					{
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_ash = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_ash = date('d-M-Y', strtotime("+1 day", strtotime($newdate_ash)));
							}
							//===================QC Prod==================
							foreach($ashulia_plan_qty_array[$unit_id][$newdate_ash] as $poid=>$poData)
							{
								$planQty_qc=$prod_qcQty=0;
								$planQty_qc=$poData[$com_id]['qc_planQty'];
								$prod_qcQty=$poData[$com_id]['prod_qc'];
								if($prod_qcQty=='') $prod_qcQty=0;
								if($planQty_qc>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$po_planQtyQc_arr[$newdate_ash][$poid]=$planQty_qc+$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid];
									$prev_date_planQc[$poid] = $newdate_ash;
								}
								if($planQty_qc>0 || $prod_qcQty>0)
								{
									if($planQty_qc>0 && $prod_qcQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_prodQcQty_arr[$newdate_ash][$poid]=$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid];
										$prev_prodQc_date[$poid] = $newdate_ash;
										//echo "A=".$poid;
									}
									if($planQty_qc==0 && $prod_qcQty>0) 
									{
										$po_prodQcQty_arr[$newdate_ash][$poid]=$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid];
										$prev_prodQc_date[$poid] = $newdate_ash;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planQtycal=0;
									$planQtycal=$ashulia_plan_qty_array[3][$newdate_ash][$poid][$com_id]['qc_planQty'];//Plan
									if($ashulia_plan_qty_array[3][$newdate_ash][$poid][$com_id]['prod_qc']!="" || $planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$po_prodQcQty_arr[$newdate_ash][$poid]=$prod_qcQty+$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid];
										$prev_prodQc_date[$poid] = $newdate_ash;
									}
								}
							} 
							//======================Qc Prod End================//ashulia_in_plan_qty_array
							//=============Input=========================//ashulia_out_plan_qty_array
							 
							foreach($ashulia_in_plan_qty_array[$unit_id][$newdate_ash] as $poid=>$poData)
							{
								$planQty_in=$prod_InQty=0;
								$planQty_in=$poData[$com_id]['input_planQty'];
								$prod_InQty=$poData[$com_id]['prod_in'];
								if($prod_InQty=='') $prod_InQty=0;
								if($planQty_in>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$po_planQtyIn_arr[$newdate_ash][$poid]=$planQty_in+$po_planQtyIn_arr[$prev_date_planIn[$poid]][$poid];
									$prev_date_planIn[$poid] = $newdate_ash;
								}
								if($planQty_in>0 || $prod_InQty>0)
								{
									if($planQty_in>0 && $prod_InQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_prodInQty_arr[$newdate_ash][$poid]=$po_prodInQty_arr[$prev_prodIn_date[$poid]][$poid];
										$prev_prodIn_date[$poid] = $newdate_ash;
										//echo "A=".$poid;
									}
									if($planQty_in==0 && $prod_InQty>0) 
									{
										$po_prodInQty_arr[$newdate_ash][$poid]=$po_prodInQty_arr[$prev_prodIn_date[$poid]][$poid];
										$prev_prodIn_date[$poid] = $newdate_ash;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planInQtycal=0;
									$planInQtycal=$ashulia_in_plan_qty_array[3][$newdate_ash][$poid][$com_id]['input_planQty'];//Plan
									if($ashulia_in_plan_qty_array[3][$newdate_ash][$poid][$com_id]['prod_in']!="" || $planInQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$po_prodInQty_arr[$newdate_ash][$poid]=$prod_InQty+$po_prodInQty_arr[$prev_prodIn_date[$poid]][$poid];
										$prev_prodIn_date[$poid] = $newdate_ash;
									}
								}
							} 
							//=============Out=========================//ashulia_out_plan_qty_array
							 //ashulia_gFin_plan_qty_array
							foreach($ashulia_out_plan_qty_array[$unit_id][$newdate_ash] as $poid=>$poData)
							{
								$planQty_out=$prod_OutQty=0;
								$planQty_out=$poData[$com_id]['out_planQty'];
								$prod_OutQty=$poData[$com_id]['prod_out'];
								if($prod_OutQty=='') $prod_OutQty=0;
								if($planQty_out>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$po_planQtyOut_arr[$newdate_ash][$poid]=$planQty_out+$po_planQtyOut_arr[$prev_date_planOut[$poid]][$poid];
									$prev_date_planOut[$poid] = $newdate_ash;
								}
								if($planQty_out>0 || $prod_OutQty>0)
								{
									if($planQty_out>0 && $prod_OutQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_prodOutQty_arr[$newdate_ash][$poid]=$po_prodOutQty_arr[$prev_prodOut_date[$poid]][$poid];
										$prev_prodOut_date[$poid] = $newdate_ash;
										//echo "A=".$poid;
									}
									if($planQty_out==0 && $prod_OutQty>0) 
									{
										$po_prodOutQty_arr[$newdate_ash][$poid]=$po_prodOutQty_arr[$prev_prodOut_date[$poid]][$poid];
										$prev_prodOut_date[$poid] = $newdate_ash;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planOutQtycal=0;
									$planOutQtycal=$ashulia_out_plan_qty_array[3][$newdate_ash][$poid][$com_id]['out_planQty'];//Plan
									if($ashulia_out_plan_qty_array[3][$newdate_ash][$poid][$com_id]['prod_out']!="" || $planOutQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$po_prodOutQty_arr[$newdate_ash][$poid]=$prod_OutQty+$po_prodOutQty_arr[$prev_prodOut_date[$poid]][$poid];
										$prev_prodOut_date[$poid] = $newdate_ash;
									}
								}
							}
							//=============Gmts Fin=========================//ashulia_gFin_plan_qty_array
							 
							 foreach($ashulia_gFin_plan_qty_array[$unit_id][$newdate_ash] as $poid=>$poData)
							 {
								 $planQty_fin=$prod_FinQty=0;
								 $planQty_fin=$poData[$com_id]['fin_planQty'];//fin_planQty
								 $prod_FinQty=$poData[$com_id]['prod_fin'];
								 if($prod_FinQty=='') $prod_FinQty=0;
								 if($planQty_fin>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $po_planQtyFin_arr[$newdate_ash][$poid]=$planQty_fin+$po_planQtyFin_arr[$prev_date_planFin[$poid]][$poid];
									 $prev_date_planFin[$poid] = $newdate_ash; 
								 }
								 if($planQty_fin>0 || $prod_FinQty>0)
								 {
									 if($planQty_fin>0 && $prod_FinQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $po_prodFinQty_arr[$newdate_ash][$poid]=$po_prodFinQty_arr[$prev_prodFin_date[$poid]][$poid];
										 $prev_prodFin_date[$poid] = $newdate_ash;
										 //echo "A=".$poid;
									 }
									 if($planQty_fin==0 && $prod_FinQty>0) 
									 {
										 $po_prodFinQty_arr[$newdate_ash][$poid]=$po_prodFinQty_arr[$prev_prodFin_date[$poid]][$poid];
										 $prev_prodFin_date[$poid] = $newdate_ash;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $planFinQtycal=0;
									 $planFinQtycal=$ashulia_gFin_plan_qty_array[3][$newdate_ash][$poid][$com_id]['fin_planQty'];//Plan
									 if($ashulia_gFin_plan_qty_array[3][$newdate_ash][$poid][$com_id]['prod_fin']!="" || $planFinQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $po_prodFinQty_arr[$newdate_ash][$poid]=$prod_FinQty+$po_prodFinQty_arr[$prev_prodFin_date[$poid]][$poid];
										 $prev_prodFin_date[$poid] = $newdate_ash;
									 }
								 }
							 }
							
						} 
						//===========Fin End
						//Days loop end
				    }
					if($unit_id==4 && $com_id==2) // For Kal //Ratanpur
					{
						//$diff_days=0;
						$diff_days=datediff('d',$from_date,$to_date);
						// echo $year_mon.'='.$diff_days."<br>"; 
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_ash_rat = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_ash_rat = date('d-M-Y', strtotime("+1 day", strtotime($newdate_ash_rat)));
							}
							//===================QC Prod==================
							
							foreach($rat_ashulia_plan_qty_array[4][$newdate_ash_rat] as $poid_r=>$poData)
							{
								 $loaction_r=$poId_loaction_Arr[$com_id][$poid_r];
								// echo $loaction_r.'<br>';
								//if($loaction_r==5)
								//{
								$planQty_qc_rat=$prod_qcQty_rat=0;
								$planQty_qc_rat=$poData[$com_id]['qc_planQty'];
								$prod_qcQty_rat=$poData[$com_id]['prod_qc'];
								if($prod_qcQty_rat=='') $prod_qcQty_rat=0;
							//	echo $poid_r.'='.$newdate_ash_rat.'='.$planQty_qc_rat.'='.$prod_qcQty_rat.'<br>';
								//rat_po_planQtyQc_arr
								//if($planQty_qc=='') $planQty_qc=0;
								
								
								if($planQty_qc_rat>0)
								{
									 // echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$rat_po_planQtyQc_arr[$newdate_ash_rat][$poid_r]=$planQty_qc_rat+$rat_po_planQtyQc_arr[$rat_prev_date_planQc[$poid_r]][$poid_r];
									$rat_prev_date_planQc[$poid_r] = $newdate_ash_rat;
								}
								if($planQty_qc_rat>0 || $prod_qcQty_rat>0)
								{
									if($planQty_qc_rat>0 && $prod_qcQty_rat==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$rat_po_prodQcQty_arr[$newdate_ash_rat][$poid_r]=$rat_po_prodQcQty_arr[$rat_prev_prodQc_date[$poid_r]][$poid_r];
										$rat_prev_prodQc_date[$poid_r] = $newdate_ash_rat;
										//echo "A=".$poid;
										
									}
									if($planQty_qc_rat==0 && $prod_qcQty_rat>0) 
									{
										$rat_po_prodQcQty_arr[$newdate_ash_rat][$poid_r]=$rat_po_prodQcQty_arr[$rat_prev_prodQc_date[$poid_r]][$poid_r];
										$rat_prev_prodQc_date[$poid_r] = $newdate_ash_rat;
										
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$rat_planQtycal=0;
									$rat_planQtycal=$rat_ashulia_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_r][$com_id]['qc_planQty'];//Plan
									if($rat_ashulia_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_r][$com_id]['prod_qc']!="" || $rat_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										// echo $unit_id."=".$prod_qcQty.'<br>';
										//echo $newdate_ash.'='.$poid.'='.$planQty_qc.'='.$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid].'<br>';
										$rat_po_prodQcQty_arr[$newdate_ash_rat][$poid_r]=$prod_qcQty_rat+$rat_po_prodQcQty_arr[$rat_prev_prodQc_date[$poid_r]][$poid_r];
										$rat_prev_prodQc_date[$poid_r] = $newdate_ash_rat;
									}
								 }
								//}
							} 
							//======================Qc Prod End================//ashulia_in_plan_qty_array
							//=============Input=========================//ashulia_out_plan_qty_array
							 
							foreach($rat_ashulia_in_plan_qty_array[$unit_id][$newdate_ash_rat] as $poid_in=>$poData)
							{
								$rat_planQty_in=$rat_prod_InQty=0;
								$rat_planQty_in=$poData[$com_id]['input_planQty'];
								$rat_prod_InQty=$poData[$com_id]['prod_in'];
								if($rat_prod_InQty=='') $rat_prod_InQty=0;
								if($rat_planQty_in>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$rat_po_planQtyIn_arr[$newdate_ash_rat][$poid_in]=$rat_planQty_in+$rat_po_planQtyIn_arr[$rat_prev_date_planIn[$poid_in]][$poid_in];
									$rat_prev_date_planIn[$poid_in] = $newdate_ash_rat;
								}
								if($rat_planQty_in>0 || $rat_prod_InQty>0)
								{
									if($rat_planQty_in>0 && $rat_prod_InQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$rat_po_prodInQty_arr[$newdate_ash_rat][$poid_in]=$rat_po_prodInQty_arr[$rat_prev_prodIn_date[$poid_in]][$poid_in];
										$rat_prev_prodIn_date[$poid_in] = $newdate_ash_rat;
										//echo "A=".$poid;
									}
									if($rat_planQty_in==0 && $rat_prod_InQty>0) 
									{
										$rat_po_prodInQty_arr[$newdate_ash_rat][$poid_in]=$rat_po_prodInQty_arr[$rat_prev_prodIn_date[$poid_in]][$poid_in];
										$rat_prev_prodIn_date[$poid_in] = $newdate_ash_rat;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$rat_planInQtycal=0;
									$rat_planInQtycal=$rat_ashulia_in_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_in][$com_id]['input_planQty'];//Plan
									if($rat_ashulia_in_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_in][$com_id]['prod_in']!="" || $rat_planInQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$rat_po_prodInQty_arr[$newdate_ash_rat][$poid_in]=$rat_prod_InQty+$rat_po_prodInQty_arr[$rat_prev_prodIn_date[$poid_in]][$poid_in];
										$rat_prev_prodIn_date[$poid_in] = $newdate_ash_rat;
									}
								}
							} 
							//=============Out=========================//ashulia_out_plan_qty_array
							 //ashulia_gFin_plan_qty_array
							foreach($rat_ashulia_out_plan_qty_array[$unit_id][$newdate_ash_rat] as $poid_out=>$poData)
							{
								$rat_planQty_out=$rat_prod_OutQty=0;
								$rat_planQty_out=$poData[$com_id]['out_planQty'];
								$rat_prod_OutQty=$poData[$com_id]['prod_out'];
								if($rat_prod_OutQty=='') $rat_prod_OutQty=0;
								if($rat_planQty_out>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$rat_po_planQtyOut_arr[$newdate_ash_rat][$poid_out]=$rat_planQty_out+$rat_po_planQtyOut_arr[$rat_prev_date_planOut[$poid_out]][$poid_out];
									$rat_prev_date_planOut[$poid_out] = $newdate_ash_rat;
								}
								if($rat_planQty_out>0 || $rat_prod_OutQty>0)
								{
									if($rat_planQty_out>0 && $rat_prod_OutQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$rat_po_prodOutQty_arr[$newdate_ash_rat][$poid_out]=$rat_po_prodOutQty_arr[$rat_prev_prodOut_date[$poid_out]][$poid_out];
										$rat_prev_prodOut_date[$poid_out] = $newdate_ash_rat;
										//echo "A=".$poid;
									}
									if($rat_planQty_out==0 && $rat_prod_OutQty>0) 
									{
										$rat_po_prodOutQty_arr[$newdate_ash_rat][$poid_out]=$rat_po_prodOutQty_arr[$rat_prev_prodOut_date[$poid_out]][$poid_out];
										$rat_prev_prodOut_date[$poid_out] = $newdate_ash_rat;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$rat_planOutQtycal=0;
									$rat_planOutQtycal=$rat_ashulia_out_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_out][$com_id]['out_planQty'];//Plan
									if($rat_ashulia_out_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_out][$com_id]['prod_out']!="" || $rat_planOutQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$rat_po_prodOutQty_arr[$newdate_ash_rat][$poid_out]=$rat_prod_OutQty+$rat_po_prodOutQty_arr[$rat_prev_prodOut_date[$poid_out]][$poid_out];
										$rat_prev_prodOut_date[$poid_out] = $newdate_ash_rat;
									}
								}
							}
							//=============Gmts Fin=========================//ashulia_gFin_plan_qty_array
							 
							 foreach($rat_ashulia_gFin_plan_qty_array[$unit_id][$newdate_ash_rat] as $poid_f=>$poData)
							 {
								 $rat_planQty_fin=$rat_prod_FinQty=0;
								 $rat_planQty_fin=$poData[$com_id]['fin_planQty'];//fin_planQty
								 $rat_prod_FinQty=$poData[$com_id]['prod_fin'];
								 if($rat_prod_FinQty=='') $rat_prod_FinQty=0;
								 if($rat_planQty_fin>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $rat_po_planQtyFin_arr[$newdate_ash_rat][$poid_f]=$rat_planQty_fin+$rat_po_planQtyFin_arr[$rat_prev_date_planFin[$poid_f]][$poid_f];
									 $rat_prev_date_planFin[$poid_f] = $newdate_ash_rat; 
								 }
								 if($rat_planQty_fin>0 || $rat_prod_FinQty>0)
								 {
									 if($rat_planQty_fin>0 && $rat_prod_FinQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $rat_po_prodFinQty_arr[$newdate_ash_rat][$poid_f]=$rat_po_prodFinQty_arr[$rat_prev_prodFin_date[$poid_f]][$poid_f];
										 $rat_prev_prodFin_date[$poid_f] = $newdate_ash_rat;
										 //echo "A=".$poid;
									 }
									 if($planQty_fin==0 && $prod_FinQty>0) 
									 {
										 $rat_po_prodFinQty_arr[$newdate_ash_rat][$poid_f]=$rat_po_prodFinQty_arr[$rat_prev_prodFin_date[$poid_f]][$poid_f];
										 $rat_prev_prodFin_date[$poid_f] = $newdate_ash_rat;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $rat_planFinQtycal=0;
									 $rat_planFinQtycal=$rat_ashulia_gFin_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_f][$com_id]['fin_planQty'];//Plan
									 if($rat_ashulia_gFin_plan_qty_array[$unit_id][$newdate_ash_rat][$poid_f][$com_id]['prod_fin']!="" || $rat_planFinQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $rat_po_prodFinQty_arr[$newdate_ash_rat][$poid_f]=$rat_prod_FinQty+$rat_po_prodFinQty_arr[$rat_prev_prodFin_date[$poid_f]][$poid_f];
										 $rat_prev_prodFin_date[$poid_f] = $newdate_ash_rat;
									 }
								 }
							 }
							
						} 
						//===========Fin End
						//Days loop end
				    }
					//echo $unit_id.'='.$com_id.'<br>';
					if($unit_id==3 && $com_id==1) //JM  Nayapara
					{
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_nay = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_nay = date('d-M-Y', strtotime("+1 day", strtotime($newdate_nay)));
							}
							//===================QC Prod==================
							foreach($nay_plan_qty_array[6][$newdate_nay] as $poid_q=>$poData)
							{
								$nay_planQty_qc=$nay_prod_qcQty=0;
								$nay_planQty_qc=$poData[$com_id]['qc_planQty'];
								$nay_prod_qcQty=$poData[$com_id]['prod_qc'];
								if($nay_prod_qcQty=='') $nay_prod_qcQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($nay_planQty_qc>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$nay_po_planQtyQc_arr[$newdate_nay][$poid_q]=$nay_planQty_qc+$nay_po_planQtyQc_arr[$nay_prev_date_planQc[$poid_q]][$poid_q];
									$nay_prev_date_planQc[$poid_q] = $newdate_nay;
								}
								if($nay_planQty_qc>0 || $nay_prod_qcQty>0)
								{
									if($nay_planQty_qc>0 && $nay_prod_qcQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$nay_po_prodQcQty_arr[$newdate_nay][$poid_q]=$nay_po_prodQcQty_arr[$nay_prev_prodQc_date[$poid_q]][$poid_q];
										$nay_prev_prodQc_date[$poid_q] = $newdate_nay;
										//echo "A=".$poid;
									}
									if($nay_planQty_qc==0 && $nay_prod_qcQty>0) 
									{
										$nay_po_prodQcQty_arr[$newdate_nay][$poid_q]=$nay_po_prodQcQty_arr[$nay_prev_prodQc_date[$poid_q]][$poid_q];
										$nay_prev_prodQc_date[$poid_q] = $newdate_nay;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$nay_planQtycal=0;
									$nay_planQtycal=$nay_plan_qty_array[6][$newdate_nay][$poid_q][$com_id]['qc_planQty'];//Plan
									if($nay_plan_qty_array[6][$newdate_nay][$poid_q][$com_id]['prod_qc']!="" || $nay_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$nay_po_prodQcQty_arr[$newdate_nay][$poid_q]=$nay_prod_qcQty+$nay_po_prodQcQty_arr[$nay_prev_prodQc_date[$poid_q]][$poid_q];
										$nay_prev_prodQc_date[$poid_q] = $newdate_nay;
									}
								}
							} 
							//======================Qc Prod End================//ashulia_in_plan_qty_array
							//=============Input=========================//ashulia_out_plan_qty_array
							 
							foreach($nay_in_plan_qty_array[6][$newdate_nay] as $poid_in=>$poData)
							{
								$nay_planQty_in=$nay_prod_InQty=0;
								$nay_planQty_in=$poData[$com_id]['input_planQty'];
								$nay_prod_InQty=$poData[$com_id]['prod_in'];
								if($nay_prod_InQty=='') $nay_prod_InQty=0;
								if($nay_planQty_in>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$nay_po_planQtyIn_arr[$newdate_nay][$poid_in]=$nay_planQty_in+$nay_po_planQtyIn_arr[$nay_prev_date_planIn[$poid_in]][$poid_in];
									$nay_prev_date_planIn[$poid_in] = $newdate_nay;
								}
								if($nay_planQty_in>0 || $nay_prod_InQty>0)
								{
									if($nay_planQty_in>0 && $nay_prod_InQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$nay_po_prodInQty_arr[$newdate_nay][$poid_in]=$nay_po_prodInQty_arr[$nay_prev_prodIn_date[$poid_in]][$poid_in];
										$nay_prev_prodIn_date[$poid_in] = $newdate_nay;
										//echo "A=".$poid;
									}
									if($nay_planQty_in==0 && $nay_prod_InQty>0) 
									{
										$nay_po_prodInQty_arr[$newdate_nay][$poid_in]=$nay_po_prodInQty_arr[$nay_prev_prodIn_date[$poid_in]][$poid_in];
										$nay_prev_prodIn_date[$poid_in] = $newdate_nay;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$nay_planInQtycal=0;
									$nay_planInQtycal=$nay_in_plan_qty_array[6][$newdate_nay][$poid_in][$com_id]['input_planQty'];//Plan
									if($nay_in_plan_qty_array[6][$newdate_nay][$poid_in][$com_id]['prod_in']!="" || $nay_planInQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$nay_po_prodInQty_arr[$newdate_nay][$poid_in]=$nay_prod_InQty+$nay_po_prodInQty_arr[$nay_prev_prodIn_date[$poid_in]][$poid_in];
										$nay_prev_prodIn_date[$poid_in] = $newdate_nay;
									}
								}
							} 
							//=============Out=========================//ashulia_out_plan_qty_array
							 //ashulia_gFin_plan_qty_array
							foreach($nay_out_plan_qty_array[6][$newdate_nay] as $poid_out=>$poData)
							{
								$nay_planQty_out=$nay_prod_OutQty=0;
								$nay_planQty_out=$poData[$com_id]['out_planQty'];
								$nay_prod_OutQty=$poData[$com_id]['prod_out'];
								if($nay_prod_OutQty=='') $nay_prod_OutQty=0;
								if($nay_planQty_out>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$nay_po_planQtyOut_arr[$newdate_nay][$poid_out]=$nay_planQty_out+$nay_po_planQtyOut_arr[$nay_prev_date_planOut[$poid_out]][$poid_out];
									$nay_prev_date_planOut[$poid_out] = $newdate_nay;
								}
								if($nay_planQty_out>0 || $nay_prod_OutQty>0)
								{
									if($nay_planQty_out>0 && $nay_prod_OutQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$nay_po_prodOutQty_arr[$newdate_nay][$poid_out]=$nay_po_prodOutQty_arr[$nay_prev_prodOut_date[$poid_out]][$poid_out];
										$nay_prev_prodOut_date[$poid_out] = $newdate_nay;
										//echo "A=".$poid;
									}
									if($nay_planQty_out==0 && $nay_prod_OutQty>0) 
									{
										$nay_po_prodOutQty_arr[$newdate_nay][$poid_out]=$nay_po_prodOutQty_arr[$nay_prev_prodOut_date[$poid_out]][$poid_out];
										$nay_prev_prodOut_date[$poid_out] = $newdate_nay;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$nay_planOutQtycal=0;
									$nay_planOutQtycal=$nay_out_plan_qty_array[6][$newdate_nay][$poid_out][$com_id]['out_planQty'];//Plan
									if($nay_out_plan_qty_array[6][$newdate_nay][$poid_out][$com_id]['prod_out']!="" || $nay_planOutQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$nay_po_prodOutQty_arr[$newdate_nay][$poid_out]=$nay_prod_OutQty+$nay_po_prodOutQty_arr[$nay_prev_prodOut_date[$poid_out]][$poid_out];
										$nay_prev_prodOut_date[$poid_out] = $newdate_nay;
									}
								}
							}
							//=============Gmts Fin=========================//ashulia_gFin_plan_qty_array
							 
							 foreach($nay_gFin_plan_qty_array[6][$newdate_nay] as $poid_fin=>$poData)
							 {
								 $nay_planQty_fin=$nay_prod_FinQty=0;
								 $nay_planQty_fin=$poData[$com_id]['fin_planQty'];//fin_planQty
								 $nay_prod_FinQty=$poData[$com_id]['prod_fin'];
								 if($prod_FinQty=='') $prod_FinQty=0;
								 if($nay_planQty_fin>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $nay_po_planQtyFin_arr[$newdate_nay][$poid_fin]=$nay_planQty_fin+$nay_po_planQtyFin_arr[$nay_prev_date_planFin[$poid_fin]][$poid_fin];
									 $nay_prev_date_planFin[$poid_fin] = $newdate_nay; 
								 }
								 if($nay_planQty_fin>0 || $nay_prod_FinQty>0)
								 {
									 if($nay_planQty_fin>0 && $nay_prod_FinQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $nay_po_prodFinQty_arr[$newdate_nay][$poid_fin]=$nay_po_prodFinQty_arr[$nay_prev_prodFin_date[$poid_fin]][$poid_fin];
										 $nay_prev_prodFin_date[$poid_fin] = $newdate_nay;
										 //echo "A=".$poid;
									 }
									 if($nay_planQty_fin==0 && $prod_FinQty>0) 
									 {
										 $nay_po_prodFinQty_arr[$newdate_nay][$poid_fin]=$nay_po_prodFinQty_arr[$nay_prev_prodFin_date[$poid_fin]][$poid_fin];
										 $nay_prev_prodFin_date[$poid_fin] = $newdate_nay;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $nay_planFinQtycal=0;
									 $nay_planFinQtycal=$nay_gFin_plan_qty_array[6][$newdate_nay][$poid_fin][$com_id]['fin_planQty'];//Plan
									 if($nay_gFin_plan_qty_array[6][$newdate_nay][$poid_fin][$com_id]['prod_fin']!="" || $nay_planFinQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $nay_po_prodFinQty_arr[$newdate_nay][$poid_fin]=$nay_prod_FinQty+$nay_po_prodFinQty_arr[$nay_prev_prodFin_date[$poid_fin]][$poid_fin];
										 $nay_prev_prodFin_date[$poid_fin] = $newdate_nay;
									 }
								 }
							 }
							 //=============Gmts Wash Recv=========================//
							 
							 foreach($nay_gWash_plan_qty_array[6][$newdate_nay] as $poid_w=>$poData)
							 {
								 $nay_planQty_w=$nay_prod_WQty=0;
								 $nay_planQty_w=$poData[$com_id]['wash_planQty'];//fin_planQty
								 $nay_prod_WQty=$poData[$com_id]['prod_wash'];
								 if($nay_prod_WQty=='') $nay_prod_WQty=0;
								 if($nay_planQty_w>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $nay_po_planQtyWash_arr[$newdate_nay][$poid_w]=$nay_planQty_w+$nay_po_planQtyWash_arr[$prev_date_planW[$poid_w]][$poid_w];
									 $prev_date_planW[$poid_w] = $newdate_nay; 
								 }
								 if($nay_planQty_w>0 || $nay_prod_WQty>0)
								 {
									 if($nay_planQty_w>0 && $nay_prod_WQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $nay_po_prodWashQty_arr[$newdate_nay][$poid_w]=$nay_po_prodWashQty_arr[$prev_prodW_date[$poid_w]][$poid_w];
										 $prev_prodW_date[$poid_w] = $newdate_nay;
										 //echo "A=".$poid;
									 }
									 if($nay_planQty_fin==0 && $nay_prod_WQty>0) 
									 {
										 $nay_po_prodWashQty_arr[$newdate_nay][$poid_w]=$nay_po_prodWashQty_arr[$prev_prodW_date[$poid_w]][$poid_w];
										 $prev_prodW_date[$poid_w] = $newdate_nay;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $nay_planWashQtycal=0;
									 $nay_planWashQtycal=$nay_gWash_plan_qty_array[6][$newdate_nay][$poid_w][$com_id]['wash_planQty'];//Plan
									 if($nay_gWash_plan_qty_array[6][$newdate_nay][$poid_w][$com_id]['prod_wash']!="" || $nay_planWashQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $nay_po_prodWashQty_arr[$newdate_nay][$poid_w]=$nay_prod_WQty+$nay_po_prodWashQty_arr[$prev_prodW_date[$poid_w]][$poid_w];
										 $prev_prodW_date[$poid_w] = $newdate_nay;
									 }
								 }
							 }

							 //=============######### Actual PO and Gmts  Delivery ****########=========================//
							 //nay_actual_plan_qty_array
							 foreach($nay_acl_delivery_qty_array[6][$newdate_nay] as $poid_del=>$poDataArr)
							 {
								foreach($poDataArr as $aclpoid=>$poData)
							    {
								 $nay_planQty_acl=$nay_prod_DelQty=0;	
								 //$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']
								 $nay_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
								//  echo $newdate_nay.'='.$nay_planQty_del.'<br>';
								 $nay_prod_DelQty=$poData[$com_id]['prod_del'];
								  $acul_poid=rtrim($poData[$com_id]['acul_poid'],',');
								  $acul_poidArr=array_unique(explode(",",$acul_poid));
								 // asort($acul_poidArr);
								 if($nay_planQty_acl>0)
								 {
									 $nay_actual_po_planQty_arr[$newdate_nay][$poid_del]=$nay_planQty_acl;
								 }
								 
									$nay_prod_DelQty=$nay_prod_DelQty;//$nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del'];
									 if($nay_prod_DelQty=='') $nay_prod_DelQty=0;
									 if($nay_planQty_acl>0 || $nay_prod_DelQty>0)
									 {
									 	if($nay_planQty_acl>0 && $nay_prod_DelQty==0)
										{
											
										 $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid]=$nay_po_prodDelQty_arr[$prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $prev_prodDel_date[$poid_del][$aclpoid] = $newdate_nay;
										}
										if($nay_planQty_acl==0 && $nay_prod_DelQty==0)
										{
										  $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid]=$nay_po_prodDelQty_arr[$prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $prev_prodDel_date[$poid_del][$aclpoid] = $newdate_nay;
										   // echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
										}
										
										if($nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $nay_planQty_acl>0)
										{
										  $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid]=$nay_prod_DelQty+$nay_po_prodDelQty_arr[$prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $prev_prodDel_date[$poid_del][$aclpoid] = $newdate_nay;
										}
									}
								 }
							}
						} 
						//===========Fin End
						//Days loop end
				    }
			} 
		//Month Loop end here //Month Loop
		}
		 //unit
		   //  echo "<pre>";
		     //  print_r($nay_po_prodDelQty_arr);
		 //  print_r($po_planQty_arr);
		//print_r($num_of_plan_days);
		 // echo "</pre>";
	}
	 
	  //==================Tejgoan Yarn Month Wise KPI==================
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
									
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
									
								}

								//echo $day_all.'='.$po_alloQty_arr[$day_all][$poid].'/'.$po_planQty_arr[$day_all][$poid].'<br>';
								$planQtycal=0;
								$planQtycal=$plan_qty_array[1][$day_all][$poid][$comp_id]['planQty'];//alloQty
								
								if($planQtycal>0)
								{
									
									$mon_alloc_qty=$po_alloQty_arr[$day_all][$poid];
									$month_wise_kpiArr[$comp_id][$day_all]['allo']+=$mon_alloc_qty;
								}
								$month_wise_kpiArr[$comp_id][$day_all]['plan']+=$po_planQty_arr[$day_all][$poid];
							
							 
						 }
					 //}
					
				}
		 }
	    }
	   
	   //=========For Tejgoan Yarn================
	   foreach($month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
		   foreach($comData as $day_key=>$row)
		   {
			  $month_allo=$row['allo']; 
			   $month_plan=$row['plan'];
			 
			   $month_kpi_per=$month_allo/$month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				//echo $today.'='.$day_chk.'<br>';
				if($day_chk<=$today)// as on today
				{
					//echo $day_key.'='.$today_date.'<br>';
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
			   
	   }
	   $comp_avg_perArr=array();
	   foreach($yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
			foreach($comData as $unit_id=>$monData) 
			{
				foreach($monData as $monYr=>$per) 
				{
					$comp_avg_perArr[$comp_id][$unit_id]+=$per/$num_of_plan_days[$comp_id][1][$monYr];
				}
			}

	   }
	   //=======================Kal Ashulia=======================
	   $fin_month_wise_kpiArr=array(); $out_month_wise_kpiArr=array();$in_month_wise_kpiArr=array();$qc_month_wise_kpiArr=array(); 
	   
	   foreach($Ash_com_poIdArr as $comp_id=>$comData)
	   {
		 foreach($comData as $poid=>$IR)
	     {
				 $loaction=0;
				$loaction=$poId_loaction_Arr[$comp_id][$poid];
				/*if($loaction==3)
				{
					$poid=$poid;
				}
				else
				{
					$poid_r=$poid;
				}*/
				//echo $loaction.'D';
							
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
				//	$company_kip_cal_Arr=array();//$po_planQtyArr=array();
					 
						//if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
						//{
							for($j=0;$j<$diff_days;$j++)
							{
								//$day_all =0;
								if($j==0)
								{
									$day_all = date('d-M-Y', strtotime($from_date));
									
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
									
								}
							
								 // echo $loaction.'=<br>';
								//echo $day_all.'='.$po_alloQty_arr[$day_all][$poid].'/'.$po_planQty_arr[$day_all][$poid].'<br>';
							
							if($loaction==3) //===========Ashulia Start===================
							{
								//==========Qc Prod and Qc Plan==============
							
								$planQcQtycal=0;
								$planQcQtycal=$ashulia_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['qc_planQty'];//alloQty
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								
								if($planQcQtycal>0)
								{
									
									$mon_qc_qty=$po_prodQcQty_arr[$day_all][$poid];
									 //echo $loaction.'='.$mon_qc_qty.'<br>';
									$qc_month_wise_kpiArr[$comp_id][$loaction][$day_all]['qc_prod']+=$mon_qc_qty;
								}
								$qc_month_wise_kpiArr[$comp_id][$loaction][$day_all]['qc_plan']+=$po_planQtyQc_arr[$day_all][$poid];

								//==========In Prod and In Plan==============
								$planInQtycal=0;
								$planInQtycal=$ashulia_in_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['input_planQty'];//alloQty
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								
								if($planInQtycal>0)
								{
									$mon_in_qty=$po_prodInQty_arr[$day_all][$poid];
									$in_month_wise_kpiArr[$comp_id][$loaction][$day_all]['in_prod']+=$mon_in_qty;
								}
								$in_month_wise_kpiArr[$comp_id][$loaction][$day_all]['in_plan']+=$po_planQtyIn_arr[$day_all][$poid];

								//==========Out Qty Prod and Out Plan Qty==============
								$planOutQtycal=0;
								$planOutQtycal=$ashulia_out_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['out_planQty'];//alloQty
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								//po_prodOutQty_arr
								if($planOutQtycal>0)
								{
									$mon_out_qty=$po_prodOutQty_arr[$day_all][$poid];
									$out_month_wise_kpiArr[$comp_id][$loaction][$day_all]['out_prod']+=$mon_out_qty;
								}
								$out_month_wise_kpiArr[$comp_id][$loaction][$day_all]['out_plan']+=$po_planQtyOut_arr[$day_all][$poid];

								//==========GMTS Fin Qty Prod and Fin Plan Qty==============
								$planFinQtycal=0;//po_prodFinQty_arr
								$planFinQtycal=$ashulia_gFin_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['fin_planQty'];
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								//po_prodOutQty_arr
								if($planFinQtycal>0)
								{
									$mon_fin_qty=$po_prodFinQty_arr[$day_all][$poid];
									$fin_month_wise_kpiArr[$comp_id][$loaction][$day_all]['fin_prod']+=$mon_fin_qty;
								}
								$fin_month_wise_kpiArr[$comp_id][$loaction][$day_all]['fin_plan']+=$po_planQtyFin_arr[$day_all][$poid];
							} 
							 //***********===========Ashulia End===================********//
							 $loaction_rat=$poId_loaction_Arr[$comp_id][$poid];
							if($loaction_rat==5) //===========Ratanpur Start===================
							{
								//==========Qc Prod and Qc Plan==============
							 
								$loaction_r=4;
								$r_planQcQtycal=0;
								$r_planQcQtycal=$rat_ashulia_plan_qty_array[$loaction_r][$day_all][$poid][$comp_id]['qc_planQty'];//alloQty
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
									// echo $day_all.'='.$loaction.'='.$r_planQcQtycal.'<br>';
								if($r_planQcQtycal>0)
								{
									 
									$mon_qc_qty=$rat_po_prodQcQty_arr[$day_all][$poid];
							//	echo $poid_r.'='.$day_all.'='.$mon_qc_qty.'<br>';
									$qc_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['qc_prod']+=$mon_qc_qty;
								}
								$qc_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['qc_plan']+=$rat_po_planQtyQc_arr[$day_all][$poid];

								//==========In Prod and In Plan==============
								$r_planInQtycal=0;
								$r_planInQtycal=$rat_ashulia_in_plan_qty_array[$loaction_r][$day_all][$poid][$comp_id]['input_planQty'];//alloQty
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								
								if($r_planInQtycal>0)
								{
									$mon_in_qty=0;
									$mon_in_qty=$rat_po_prodInQty_arr[$day_all][$poid];
									$in_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['in_prod']+=$mon_in_qty;
								}
								$in_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['in_plan']+=$rat_po_planQtyIn_arr[$day_all][$poid];

								//==========Out Qty Prod and Out Plan Qty==============
								$r_planOutQtycal=0;
								$r_planOutQtycal=$rat_ashulia_out_plan_qty_array[$loaction_r][$day_all][$poid][$comp_id]['out_planQty'];//alloQty
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								//po_prodOutQty_arr
								if($r_planOutQtycal>0)
								{
									$mon_out_qty=0;
									$mon_out_qty=$rat_po_prodOutQty_arr[$day_all][$poid];
									$out_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['out_prod']+=$mon_out_qty;
								}
								$out_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['out_plan']+=$rat_po_planQtyOut_arr[$day_all][$poid];

								//==========GMTS Fin Qty Prod and Fin Plan Qty==============
								$r_planFinQtycal=0;//po_prodFinQty_arr
								$r_planFinQtycal=$rat_ashulia_gFin_plan_qty_array[$loaction_r][$day_all][$poid][$comp_id]['fin_planQty'];
								//$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
								//po_prodOutQty_arr
								if($r_planFinQtycal>0)
								{
									$mon_fin_qty=0;
									$mon_fin_qty=$rat_po_prodFinQty_arr[$day_all][$poid];
									$fin_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['fin_prod']+=$mon_fin_qty;
								}
								$fin_month_wise_kpiArr[$comp_id][$loaction_r][$day_all]['fin_plan']+=$rat_po_planQtyFin_arr[$day_all][$poid];
							} 
							//===========************Ratanpur end********==============
						 }
					 //}
					
				}
		 }
	   }
	   //=========For Ashulia Location================
	   // echo "<pre>";
	  //  print_r($qc_month_wise_kpiArr);
	 //  echo "<pre>";
	   foreach($qc_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $qc_month_prod=$row['qc_prod']; 
			   $qc_month_plan=$row['qc_plan'];
			 
			   $qc_month_kpi_per=$qc_month_prod/$qc_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				//echo $today.'='.$day_chk.'<br>';
				// echo  $loc_id.'='.$qc_month_prod.'='. $qc_month_plan.'*100<br>';
				if($day_chk<=$today)// as on today
				{
					//echo $day_key.'='.$today_date.'<br>';
					if($qc_month_prod>0 && $qc_month_plan>0)
					{
						if($qc_month_kpi_per>100)  $qc_month_kpi_per=100;
						
					$ash_qc_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$qc_month_kpi_per;
					$ash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc']+=$qc_month_kpi_per;
					}
					if($qc_month_plan>0)
					{
						$mon_grand_qc_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;

						$ash_num_of_plan_days[$comp_id][$loc_id][$day_key]++;
						$gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	    // print_r($mon_grand_qc_event_kpi_perArr);
	    // echo "<pre>";
	   foreach($ash_qc_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
			foreach($comData as $unit_id=>$monData) //Month Wise KPI Percentage summary part
			{
				foreach($monData as $monYr=>$per) //Month Wise KPI Percentage summary part
				{
					$ash_comp_avg_perArr[$comp_id][$unit_id]+=$per/$ash_num_of_plan_days[$comp_id][$unit_id][$monYr];
				}
			}
	   }

	   //========****************===============
	    //=========For Ashulia in Location================//in_month_wise_kpiArr
		foreach($in_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
		  foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	  	  {
			foreach($LocData as $day_key=>$row)
			{
			   $in_month_prod=$row['in_prod']; 
				$in_month_plan=$row['in_plan'];
			  
				$in_month_kpi_per=$in_month_prod/$in_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($in_month_prod>0 && $in_month_plan>0)
					 {
						 if($in_month_kpi_per>100)  $in_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
					 $ash_in_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$in_month_kpi_per;
					 $ash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in']+=$in_month_kpi_per;
					 }
					$qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 if($in_month_plan>0)
					 {
						 $ash_in_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $mon_grand_in_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					 }
					 if($in_month_plan>0 && !$qc_plan_day_chk)
					 {
						$gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
						
					 }
				 }
			  }
			}
		}
		   // print_r($mon_grand_qc_event_kpi_perArr);
		  // echo "<pre>";
		//=========For Ashulia **Out** Location================//out_month_wise_kpiArr
		foreach($out_month_wise_kpiArr as $comp_id=>$comData) //Month Wise Out KPI Percentage summary part
		{
		  foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	  	   {
			foreach($LocData as $day_key=>$row)
			{
			   $out_month_prod=$row['out_prod']; 
				$out_month_plan=$row['out_plan'];
			  
				$out_month_kpi_per=$out_month_prod/$out_month_plan*100; 
					$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($out_month_prod>0 && $out_month_plan>0)
					 {
						 if($out_month_kpi_per>100)  $out_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
					 $ash_out_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$out_month_kpi_per;

					 $ash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out']+=$out_month_kpi_per;
					 }
					 $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 $in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
					 
					 if($out_month_plan>0)
					 {
						 $ash_out_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $mon_grand_out_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					 }
					 if($out_month_plan>0 && (!$in_plan_day_chk && !$qc_plan_day_chk)) 
					 {
						$gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
						//$mon_grand_qc_event_kpi_perArr[$comp_id][$loc_id][$day_key]+=1;
					 }
				 }
			  }
			}
		}
		//=========For Ashulia **Gmts Fin** Location================//out_month_wise_kpiArr
		foreach($fin_month_wise_kpiArr as $comp_id=>$comData)  
		{
		 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
		 {
			foreach($LocData as $day_key=>$row)
			{
			   $fin_month_prod=$row['fin_prod']; 
				$fin_month_plan=$row['fin_plan'];
			  
				$fin_month_kpi_per=$fin_month_prod/$fin_month_plan*100; 
					$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($fin_month_prod>0 && $fin_month_plan>0)
					 {
						 if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
					 $ash_fin_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$fin_month_kpi_per;
					 $ash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin']+=$fin_month_kpi_per;
					 }
					 $out_plan_day_chk= $out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
					 $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 $in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
		
					 if($fin_month_plan>0)
					 {
						 $ash_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					 }
					if($fin_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk))
					{
						$gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
						//$mon_grand_qc_event_kpi_perArr[$yr_month]+=1;
					}
		
				 }
			  }
			}
		}
		 
		foreach($ash_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			 foreach($comData as $unit_id=>$monData)  
			 {
				 foreach($monData as $day_key=>$per)  
				 {
					$all_kpi_per=0;
					$event_qc_count=$mon_grand_qc_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_in_count=$mon_grand_in_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_out_count=$mon_grand_out_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_fin_count=$mon_grand_fin_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_mon_count= $event_qc_count+$event_in_count+$event_out_count+$event_fin_count;
					$all_kpi_per=$per['qc']+$per['in']+$per['out']+$per['fin'];
					  //  echo $day_key.'='.$all_kpi_per.'='.$event_mon_count.'<br>';
					$ash_gbl_comp_avg_perArr[$comp_id][$unit_id][$day_key]+=$all_kpi_per/$event_mon_count;
				 }
			 }
		}
		foreach($ash_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			 foreach($comData as $unit_id=>$monData)  
			 {
				 foreach($monData as $day_key=>$val)  
				 {
					$monthYr=date("M-Y",strtotime($day_key));
					$yr_month=strtoupper($monthYr);
					// echo $day_key.'='.$val.'='.$mon_days_count.'<br>';
					$ash_gbl_comp_mon_avg_perArr[$comp_id][$unit_id][$yr_month]+=$val;
				 }
			 }
		}
		$ash_comp_avg_perArr=array();
		foreach($ash_gbl_comp_mon_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			 foreach($comData as $unit_id=>$monData) 
			 {
				 foreach($monData as $monYr=>$per) 
				 {
					$mon_dayscount=$gbl_num_of_plan_daysArr[$comp_id][$unit_id][$monYr]; 
					$avgKpi=$per/$mon_dayscount;
					$ash_comp_avg_perArr[$comp_id][$unit_id]+=$avgKpi;
					 //echo $avgKpi.'<br>';
				 }
			 }
		}
		//==========**********Ashulia end=======************================||||||||||||||||||||+++++++++
		//====================JM Nayapara Location=========================
	//	print_r($jm_com_poIdArr);
		foreach($jm_com_poIdArr as $comp_id=>$comData)
		{
		 foreach($comData as $poid=>$IR)
	     {
				 $loaction=0;
				$loaction=$jm_poId_loaction_Arr[$comp_id][$poid];
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
				 
							for($j=0;$j<$diff_days;$j++)
							{
								//$day_all =0;
								if($j==0)
								{
									$day_all = date('d-M-Y', strtotime($from_date));
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
								}
							if($loaction==6) //===========  Start===================
							{
								//==========Qc Prod and Qc Plan==============
								$planQcQtycal=0;
								 $planQcQtycal=$nay_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['qc_planQty'];//alloQty
								if($planQcQtycal>0 && $nay_po_prodQcQty_arr[$day_all][$poid]>0)
								{
									$mon_qc_qty=$nay_po_prodQcQty_arr[$day_all][$poid];
									$nay_qc_month_wise_kpiArr[$comp_id][$loaction][$day_all]['qc_prod']+=$mon_qc_qty;
								}
								$nay_qc_month_wise_kpiArr[$comp_id][$loaction][$day_all]['qc_plan']+=$nay_po_planQtyQc_arr[$day_all][$poid];
								//==========In Prod and In Plan==============
								$planInQtycal=0;
								$planInQtycal=$nay_in_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['input_planQty'];//alloQty
								 
								if($planInQtycal>0 && $nay_po_prodInQty_arr[$day_all][$poid]>0)
								{
									
									$mon_in_qty=$nay_po_prodInQty_arr[$day_all][$poid];
									if($day_all=='28-May-2023' || $day_all=='29-May-2023' || $day_all=='30-May-2023')
									{
										//echo $poid.'='.$day_all.'='.$mon_in_qty.'<br>';
									}
									$nay_in_month_wise_kpiArr[$comp_id][$loaction][$day_all]['in_prod']+=$mon_in_qty;
								}
								$nay_in_month_wise_kpiArr[$comp_id][$loaction][$day_all]['in_plan']+=$nay_po_planQtyIn_arr[$day_all][$poid];
								//==========Out Qty Prod and Out Plan Qty==============
								$planOutQtycal=0;
								$planOutQtycal=$nay_out_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['out_planQty'];//alloQty
								//po_prodOutQty_arr
								if($planOutQtycal>0 && $nay_po_prodOutQty_arr[$day_all][$poid]>0)
								{
									$mon_out_qty=$nay_po_prodOutQty_arr[$day_all][$poid];
									$nay_out_month_wise_kpiArr[$comp_id][$loaction][$day_all]['out_prod']+=$mon_out_qty;
								}
								$nay_out_month_wise_kpiArr[$comp_id][$loaction][$day_all]['out_plan']+=$nay_po_planQtyOut_arr[$day_all][$poid];
								//==========GMTS Fin Qty Prod and Fin Plan Qty==============
								$planFinQtycal=0;//po_prodFinQty_arr
								$planFinQtycal=$nay_gFin_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['fin_planQty'];
								//po_prodOutQty_arr
								if($planFinQtycal>0 && $nay_po_prodFinQty_arr[$day_all][$poid]>0)
								{
									$mon_fin_qty=$nay_po_prodFinQty_arr[$day_all][$poid];
									$nay_fin_month_wise_kpiArr[$comp_id][$loaction][$day_all]['fin_prod']+=$mon_fin_qty;
								}
								$nay_fin_month_wise_kpiArr[$comp_id][$loaction][$day_all]['fin_plan']+=$nay_po_planQtyFin_arr[$day_all][$poid];
								//==========GMTS Wash Qty Prod and Wash Plan Qty==============
								$planWashQtycal=0;//
								$planWashQtycal=$nay_gWash_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['wash_planQty'];
								if($planWashQtycal>0 && $nay_po_prodWashQty_arr[$day_all][$poid]>0)
								{
									$mon_wash_qty=$nay_po_prodWashQty_arr[$day_all][$poid];
									$nay_wash_month_wise_kpiArr[$comp_id][$loaction][$day_all]['wash_prod']+=$mon_wash_qty;
								}
								$nay_wash_month_wise_kpiArr[$comp_id][$loaction][$day_all]['wash_plan']+=$nay_po_planQtyWash_arr[$day_all][$poid];
								
								//==========GMTS Delivery Qty Prod and Actual Plan Qty==============
								// $planAclQtycal=0;//
								// $planAclQtycal=$nay_actual_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['acc_planQty'];
								// $mon_delivery_qty=array_sum($nay_po_prodDelQty_arr[$day_all][$poid]);
								// //echo $day_all.'='.$mon_delivery_qty.'<br>';
								// if($planAclQtycal>0 && $mon_delivery_qty>0)
								// {
								// 	$mon_del_qty=$mon_delivery_qty;
								// 	$nay_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['del_prod']+=$mon_delivery_qty;
								// }
								// $nay_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['acl_plan']+=$planAclQtycal;
							} 
							 //***********=========== Nayapara End===================********//
						 }
				}
		  }
	   }
	  // echo "<pre>";
	 //  print_r($nay_po_prodInQty_arr);
	   foreach($acl_jm_com_poIdArr as $comp_id=>$comData)
		{
		 foreach($comData as $poid=>$IR_Data)
	     {
			foreach($IR_Data as $acl_poid=>$IR)
	       {
				 $loaction=0;
				$loaction=$jm_poId_loaction_Arr[$comp_id][$poid];
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
				 
							for($j=0;$j<$diff_days;$j++)
							{
								//$day_all =0;
								if($j==0)
								{
									$day_all = date('d-M-Y', strtotime($from_date));
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
								}
							if($loaction==6) //===========  Start===================
							{
								 
								//==========GMTS Delivery Qty Prod and Actual Plan Qty==============
								 
								$planAclQtycal=0;//
								$planAclQtycal=$nay_acl_delivery_qty_array[$loaction][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
								$mon_delivery_qty=$nay_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
								//echo $day_all.'='.$mon_delivery_qty.'<br>';
								if($planAclQtycal>0 && $mon_delivery_qty>0)
								{
									$mon_del_qty=$mon_delivery_qty;
									$nay_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['del_prod']+=$mon_delivery_qty;
								}
								$nay_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['acl_plan']+=$planAclQtycal;
							} 
							 //***********=========== Nayapara End===================********//
						 }
				}
			}
		  }
	   }
	     // echo "<pre>";
	    //  print_r($nay_delivery_month_wise_kpiArr);
		   // echo "<pre>";
	   //===================****Nayapara Qc*******==========
	   $nay_month_wise_kpiArr=array();
	   foreach($nay_qc_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $qc_month_prod=$row['qc_prod']; 
			   $qc_month_plan=$row['qc_plan'];
			 
			   $qc_month_kpi_per=$qc_month_prod/$qc_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($qc_month_prod>0 && $qc_month_plan>0)
					{
						if($qc_month_kpi_per>100)  $qc_month_kpi_per=100;
					//$nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$qc_month_kpi_per;
					if($day_key=='30-May-2023')
					{
						//echo $qc_month_kpi_per.'<br>';
					}
					$nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc']+=$qc_month_kpi_per;
					}
					 

					if($qc_month_plan>0)
					{
						$nay_mon_grand_qc_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						$nay_num_of_plan_days[$comp_id][$loc_id][$day_key]++;
						$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	   foreach($nay_in_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
		  foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	  	  {
			foreach($LocData as $day_key=>$row)
			{
			   $in_month_prod=$row['in_prod']; 
				$in_month_plan=$row['in_plan'];
				$in_month_kpi_per=$in_month_prod/$in_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($in_month_prod>0 && $in_month_plan>0)
					 {
					  if($in_month_kpi_per>100)  $in_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
						 if($day_key=='30-May-2023')
						{
							// echo $in_month_kpi_per.'<br>';
						}
					  $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in']+=$in_month_kpi_per;
					 }
						$qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 
					 if($in_month_plan>0)
					 {
						 $nay_in_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $nay_mon_grand_in_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						
					 }
					 if($in_month_plan>0 && !$qc_plan_day_chk)
					 {
						$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				 }
			  }
			}
		}
		   // print_r($mon_grand_qc_event_kpi_perArr);
		  // echo "<pre>";
		//=========For Out Nayapara **Out** Location================//out_month_wise_kpiArr
		$nay_mon_grand_out_event_kpi_perArr=array();
		foreach($nay_out_month_wise_kpiArr as $comp_id=>$comData) //Month Wise Out KPI Percentage summary part
		{
		  foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	  	   {
			foreach($LocData as $day_key=>$row)
			{
			   $out_month_prod=$row['out_prod']; 
				$out_month_plan=$row['out_plan'];
				$out_month_kpi_per=$out_month_prod/$out_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($out_month_prod>0 && $out_month_plan>0)
					 {
						 if($out_month_kpi_per>100)  $out_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
					 //$nay_out_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$out_month_kpi_per;
					 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out']+=$out_month_kpi_per;
					 }
					 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
					  
					 
					 if($out_month_plan>0)
					 {
						 $nay_out_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $nay_mon_grand_out_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						 
						// echo $day_key.'='.$out_month_plan.'<br>';
					 }
					 if($out_month_plan>0 && (!$in_plan_day_chk && !$qc_plan_day_chk)) 
					 {
						$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				 }
			  }
			}
		}
		 //   print_r($nay_mon_grand_out_event_kpi_perArr);
		//   echo "<pre>";
//=========For Nayapara **Gmts Fin** Location================//
foreach($nay_fin_month_wise_kpiArr as $comp_id=>$comData)  
{
 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
 {
	foreach($LocData as $day_key=>$row)
	{
	   $fin_month_prod=$row['fin_prod']; 
		$fin_month_plan=$row['fin_plan'];
	  
		$fin_month_kpi_per=$fin_month_prod/$fin_month_plan*100; 
		$monthYr=date("M-Y",strtotime($day_key));
		 $yr_month=strtoupper($monthYr);
		 $today=strtotime($today_date);
		 $day_chk=strtotime($day_key);
		 //echo $today.'='.$day_chk.'<br>';
		 if($day_chk<=$today)// as on today
		 {
			 //echo $day_key.'='.$today_date.'<br>';
			 if($fin_month_prod>0 && $fin_month_plan>0)
			 {
				 if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
				 //$nay_fin_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$fin_month_kpi_per;
				// echo $day_key.'='.$fin_month_kpi_per.'<br>';
				if($day_key=='30-May-2023')
					{
						// echo $fin_month_kpi_per.'<br>';
					}
				 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin']+=$fin_month_kpi_per;
			 }
				 $out_plan_day_chk= $nay_out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
			 	 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
				 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
				  if($fin_month_kpi_per>0)
				 {
					  // $nay_mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }
			 
			 if($fin_month_plan>0)
			 {
				 $nay_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
				 $nay_mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				
			 }
			if($fin_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk))
			{
				$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
			}
		 }
	  }
	}
}
//nay_wash_month_wise_kpiArr
//=========For Nayapara **Gmts Wash** Location================//
foreach($nay_wash_month_wise_kpiArr as $comp_id=>$comData)  //nay_delivery_month_wise_kpiArr
{
 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
 {
	foreach($LocData as $day_key=>$row)
	{
	   $wash_month_prod=$row['wash_prod']; 
		$wash_month_plan=$row['wash_plan'];
		$wash_month_kpi_per=$wash_month_prod/$wash_month_plan*100; 
		$monthYr=date("M-Y",strtotime($day_key));
		 $yr_month=strtoupper($monthYr);
		 $today=strtotime($today_date);
		 $day_chk=strtotime($day_key);
		 //echo $today.'='.$day_chk.'<br>';
		 if($day_chk<=$today)// as on today
		 {
			 //echo $day_key.'='.$today_date.'<br>';
			 if($wash_month_prod>0 && $wash_month_plan>0)
			 {
				 if($wash_month_kpi_per>100)  $wash_month_kpi_per=100;
				 //  echo $day_key.'='.$wash_month_prod.'/'.$wash_month_plan.'<br>';
			// $nay_wash_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$wash_month_kpi_per;
			 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash']+=$wash_month_kpi_per;
			 }
			 $out_plan_day_chk= $nay_out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
			 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
			 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
			 $fin_plan_day_chk= $nay_fin_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin_plan'];
			  
			 
			 if($wash_month_plan>0)
			 {
				 $nay_wash_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
				 $nay_mon_grand_wash_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 
			 }
			if($wash_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk))
			{
				$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
			}
		 }
	  }
	}
}
//=========For Nayapara **Gmts Delivery** Location================//
foreach($nay_delivery_month_wise_kpiArr as $comp_id=>$comData)  //nay_delivery_month_wise_kpiArr
{
 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
 {
	foreach($LocData as $day_key=>$row)
	{
	   $del_month_prod=$row['del_prod']; 
		$del_month_plan=$row['acl_plan'];
		$del_month_kpi_per=$del_month_prod/$del_month_plan*100; 
		$monthYr=date("M-Y",strtotime($day_key));
		 $yr_month=strtoupper($monthYr);
		 $today=strtotime($today_date);
		 $day_chk=strtotime($day_key);
		//  echo  $day_key.'='.$del_month_plan.'='.$del_month_prod.'<br>';
		 if($day_chk<=$today)// as on today
		 {
			 //echo $day_key.'='.$today_date.'<br>';
			 if($del_month_prod>0 && $del_month_plan>0)
			 {
				 if($del_month_kpi_per>100)  $del_month_kpi_per=100;
				   // echo $day_key.'='.$del_month_prod.'/'.$del_month_plan.'<br>';
			// $nay_wash_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$wash_month_kpi_per;
			 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['deli']+=$del_month_kpi_per;
			 }
			 $out_plan_day_chk= $nay_out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
			 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
			 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
			 $fin_plan_day_chk= $nay_fin_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin_plan'];
			 $wash_plan_day_chk= $nay_wash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash_plan'];
			 
			 if($del_month_plan>0)
			 {
				 $nay_del_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
				 $nay_mon_grand_del_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				
			 }
			if($del_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk && !$wash_plan_day_chk))
			{
				$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
			}
		 }
	  }
	}
}
asort($nay_month_wise_kpiArr);
foreach($nay_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
	 foreach($comData as $unit_id=>$monData)  
	 {
		 foreach($monData as $day_key=>$pers)  
		 {
			 $event_qc_count=0;$event_in_count=0;$event_out_count=0;$event_fin_count=0;$event_wash_count=0;$event_del_count=0;
			$event_qc_count=$nay_mon_grand_qc_event_kpi_perArr[$comp_id][$unit_id][$day_key];//$mon_grand_qc_event_kpi_perArr
			$event_in_count=$nay_mon_grand_in_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_out_count=$nay_mon_grand_out_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_fin_count=$nay_mon_grand_fin_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_wash_count=$nay_mon_grand_wash_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_del_count=$nay_mon_grand_del_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$nay_event_mon_count= $event_qc_count+$event_in_count+$event_out_count+$event_fin_count+$event_wash_count+$event_del_count;
			$nay_all_kpi_per=$pers['qc']+$pers['in']+$pers['out']+$pers['fin']+$pers['wash']+$pers['deli'];
			if($day_key=='30-May-2023')
			{
			  //  echo $day_key.'='.$pers['qc'].'='.$pers['in'].'='.$pers['out'].'='.$pers['fin'].'='.$pers['wash'].'='.$pers['deli'].'/'.$nay_event_mon_count.'<br>';
			}
			 // echo $day_key.'='.$event_del_count.'='.$per['deli'].'/'.$nay_event_mon_count.'<br>';
			$nay_gbl_comp_avg_perArr[$comp_id][$unit_id][$day_key]+=$nay_all_kpi_per/$nay_event_mon_count;
			$all_avg_perArr[$day_key]+=$nay_all_kpi_per/$nay_event_mon_count;
		 }
	 }
}
// echo "<pre>";
 //print_r($all_avg_perArr);
//echo "<pre>";
foreach($nay_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
   foreach($comData as $unitid=>$monData)  
   {
	   foreach($monData as $day_key=>$val)  
	   {
		  $monthYr=date("M-Y",strtotime($day_key));
		  $yr_month=strtoupper($monthYr);
	   	 // echo $day_key.'='.$val.'<br>';
		  $nay_gbl_comp_mon_avg_perArr[$comp_id][$unitid][$yr_month]+=$val;
	   }
   }
}
$nay_comp_avg_perArr=array();
foreach($nay_gbl_comp_mon_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
	 foreach($comData as $unit_id=>$monData) 
	 {
		 foreach($monData as $monYr=>$per) 
		 {
			$mon_dayscount=$nay_gbl_num_of_plan_daysArr[$comp_id][$unit_id][$monYr]; 
			$avgKpi=$per/$mon_dayscount;
			$nay_comp_avg_perArr[$comp_id][$unit_id]+=$avgKpi;
			 //echo $avgKpi.'<br>';
		 }
	 }

}
	   
       // echo "<pre>";
	     // print_r($nay_gbl_comp_mon_avg_perArr);
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
       <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" style="display:none" cellspacing="0">
	        
	        <!--<tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title;?> </strong></td>
	        </tr>-->
            <tr>
            <thead>
             <th width="70">IR </th>
             <?	
			 ksort($com_poIdArr); ksort($Ash_com_poIdArr); ksort($jm_com_poIdArr);ksort($acl_jm_com_poIdArr);//
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
				foreach($jm_com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$IR)
			     {
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
            // $comp_id=2;
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
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								
								 
								//$planQty_cal=$alloQty_cal=0;//$po_prodQcQty_arr[$newdate_ash][$poid]
								//$planQty_cal=$plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$nay_po_planQtyQc_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$nay_po_planQtyQc_arr[$day_count][$poid]; ?> </td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
			  foreach($jm_com_poIdArr as $comp_id=>$comData)
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								if($nay_po_planQtyQc_arr[$day_count][$poid]!=0)  
								{
									$alloQty_cal=$nay_po_prodQcQty_arr[$day_count][$poid];
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
            <td><b>  Till QC</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b> <?=$po_alloQty_tillCalculteArr[$day_count]; ?></b>  </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
            <td> QC KPI Per</td>
             <? $global_plan_day_chkArr=array();
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
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
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
								$company_kpi_per=0;
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
									$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
								}
								if($company_kpi_per>0)
								{
									$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
								
								}
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$Grand_qc_event_kpi_perArr[$day_count]=1;
									$global_plan_day_chkArr[$day_count]+=1;
								}
								 
				 ?>
					  <td width="80" title="QC/Plan*100"> <b><?=fn_number_format($company_kpi_per,2); ?> </b></td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
            
	    </table>
		<br>
        <?
       // die;

		?>
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" style="display:none" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($jm_com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$IR)
			     {
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
            // $comp_id=2;
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
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								
								 
								$planQty_cal=$alloQty_cal=0;//$po_prodQcQty_arr[$newdate_ash][$poid]
								//$planQty_cal=$qc_plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$nay_po_planQtyIn_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$nay_po_planQtyIn_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till In Plan</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_planQty_tillCalculteArr[$day_count];  ?> </b></td>
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
			  foreach($jm_com_poIdArr as $comp_id=>$comData)
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								//$planQtycal=$plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								$alloQty=0;
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								
								$alloQty_cal=0;
								if($nay_po_planQtyIn_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$nay_po_prodInQty_arr[$day_count][$poid];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$alloQty;  ?>"> <b><?=$alloQty_cal;  ?></b> </td>
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
            <td> <b> Till In Prod</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_alloQty_tillCalculteArr[$day_count]; ?></b> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
            <td> In KPI Per</td>
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
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
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
								$company_kpi_per=0;
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
									$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
								}
							
								if($company_kpi_per>0)
								{
									$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
									
								}
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									
									$global_plan_day_chkArr[$day_count]+=1;
									$Grand_in_event_kpi_perArr[$day_count]=1;
								}
				 ?>
					  <td width="80" title="Allocation/Plan*100"> <b><?=fn_number_format($company_kpi_per,2); ?> </b></td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
            
	    </table>
        <br>
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" style="display:none" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($jm_com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$IR)
			     {
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
            // $comp_id=2;
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
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								
								 
								$planQty_cal=$alloQty_cal=0;//$po_prodQcQty_arr[$newdate_ash][$poid]
								$planQty_cal=$qc_plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$nay_po_planQtyOut_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$nay_po_planQtyOut_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till Out Plan</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_planQty_tillCalculteArr[$day_count];  ?> </b></td>
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
			  foreach($jm_com_poIdArr as $comp_id=>$comData)
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								if($nay_po_planQtyOut_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$nay_po_prodOutQty_arr[$day_count][$poid];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$alloQty;  ?>"> <b><?=$alloQty_cal;  ?></b> </td>
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
            <td> <b> Till Out Prod</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_alloQty_tillCalculteArr[$day_count]; ?></b> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
            <td> Out KPI Per</td>
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
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
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
								$company_kpi_per=0;
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
									$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
								}
								if($company_kpi_per>0)
								{
									$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
									
								}
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$Grand_out_event_kpi_perArr[$day_count]=1;
									$global_plan_day_chkArr[$day_count]+=1;
								}
							
								 
				 ?>
					  <td width="80" title="Allocation/Plan*100"> <b><?=fn_number_format($company_kpi_per,2); ?> </b></td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
            
	    </table>
		<br>
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>"  style="display:none" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($jm_com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$IR)
			     {
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
            // $comp_id=2;
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
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								
								 
								$planQty_cal=$alloQty_cal=0;//$po_prodQcQty_arr[$newdate_ash][$poid]
								$planQty_cal=$qc_plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$nay_po_planQtyFin_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$nay_po_planQtyFin_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till Fin Plan</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_planQty_tillCalculteArr[$day_count];  ?> </b></td>
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
			  foreach($jm_com_poIdArr as $comp_id=>$comData)
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								if($nay_po_planQtyFin_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$nay_po_prodFinQty_arr[$day_count][$poid];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$alloQty;  ?>"> <b><?=$alloQty_cal;  ?></b> </td>
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
            <td> <b> Till Fin Prod</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_alloQty_tillCalculteArr[$day_count]; ?></b> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
           
			<td> Fin KPI Per</td>
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
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
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
								$company_kpi_per=0;
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
									$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
								}
								if($company_kpi_per>0)
								{
									$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
									
								}
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$Grand_fin_event_kpi_perArr[$day_count]=1;
									$global_plan_day_chkArr[$day_count]+=1;
								}
							
								
							
								 
				 ?>
					  <td width="80" title="Allocation/Plan*100"> <b><?=fn_number_format($company_kpi_per,2); ?> </b></td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
			
            
	    </table>
		<br>
        <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" style="display:none" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($jm_com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$IR)
			     {
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR;?> </td>
             <?
            // $comp_id=2;
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
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								
								 
								$planQty_cal=$alloQty_cal=0;//$po_prodQcQty_arr[$newdate_ash][$poid]
								$planQty_cal=$wash_plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$nay_po_planQtyWash_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$nay_po_planQtyWash_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till Wash Plan</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_planQty_tillCalculteArr[$day_count];  ?> </b></td>
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
			  foreach($jm_com_poIdArr as $comp_id=>$comData)
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
								if($nay_po_planQtyWash_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$nay_po_prodWashQty_arr[$day_count][$poid];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$alloQty;  ?>"> <b><?=$alloQty_cal;  ?></b> </td>
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
            <td> <b> Till Wash Prod</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_alloQty_tillCalculteArr[$day_count]; ?></b> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
           
			<td> Wash KPI Per</td>
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
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
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
								$company_kpi_per=0;
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
									$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
								}
								if($company_kpi_per>0)
								{
									$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
									
								}
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									
									$global_plan_day_chkArr[$day_count]+=1;
									$Grand_wash_event_kpi_perArr[$day_count]=1;
								}
							
								
							
								 
				 ?>
					  <td width="80" title="Wash Prod/Plan*100"> <b><?=fn_number_format($company_kpi_per,2); ?> </b></td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
            
            
			 
            
	    </table>
        <br>
         <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" style="display:none" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($acl_jm_com_poIdArr as $comp_id=>$comData)
				{
			     foreach($comData as $poid=>$DataIR)
			     {
					foreach($DataIR as $acl_id=>$IR)
					{
						$IRStr=explode("**",$IR);
					
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trdelplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trdelplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid.', Acl PoId='.$acl_id;?>"><?=$IRStr[0].'='.$IRStr[1];?> </td>
             <?
            // $comp_id=2;
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
				
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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

								
								$planAclQtycal=$nay_acl_delivery_qty_array[6][$day_count][$poid][$acl_id][$comp_id]['acc_planQty'];
								 
								//$planQty_cal=$alloQty_cal=0;//$po_prodQcQty_arr[$newdate_ash][$poid]
							//	$planQty_cal=$wash_plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								$po_planQty_tillCalculteArr[$day_count]+=$planAclQtycal;
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$planAclQtycal; ?> </td>
             <?
							 
						 }
					 }
					
				}
			 ?>
             
            </tr>
            <?
			 	$p++;
					}
				  } //actual PO
				}
			//PO end
			?>
            <tr bgcolor="#999966">
            <td><b> Till AclPo Plan</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_planQty_tillCalculteArr[$day_count];  ?> </b></td>
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
			  foreach($acl_jm_com_poIdArr as $comp_id=>$comData)
				{
					foreach($comData as $poid=>$DataIR)
					{
					   foreach($DataIR as $acl_id=>$IR)
					   {
						   $IRStr=explode("**",$IR);
						if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
              
             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trdel_<? echo $a; ?>','<? echo $bgcolor; ?>')" id="trdel_<? echo $a; ?>"> 
             <td width="70" title="<?=$poid.',Acl PoId='.$acl_id;;?>"><?=$IRStr[0].'='.$IRStr[1];?> </td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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

								// $acul_poid=$nay_actual_plan_qty_array[6][$day_count][$poid][$comp_id]['acul_poid'];
								// $acul_poid=rtrim($acul_poid,',');
								// $acul_poidArr=array_unique(explode(",",$acul_poid));
								$nay_acl_planQty=$nay_acl_delivery_qty_array[6][$day_count][$poid][$acl_id][$comp_id]['acc_planQty'];
								$alloQty_cal=0;
								 
									
								 
								if($nay_acl_planQty>0)
								{
									$alloQty_cal=$nay_po_prodDelQty_arr[$day_count][$poid][$acl_id];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=implode(",",$acul_poidArr).',Plan='.$nay_acl_planQty;  ?>"> <b><?=$alloQty_cal;  ?></b> </td>
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
			}
			//po id load end
			?>
            <tr bgcolor="#99CC99">
            <td> <b> Till Deli Prod</b></td>
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
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
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
					  <td width="80" title=""> <b><?=$po_alloQty_tillCalculteArr[$day_count]; ?></b> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#CCFFFF">
           
			<td> Deli KPI Per</td>
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
					 
						if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
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
								$company_kpi_per=0;
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									$company_kpi_per=$po_alloQty_tillCalculteArr[$day_count]/$po_planQty_tillCalculteArr[$day_count]*100;
									$company_kpi_per = ($company_kpi_per>100)?100:$company_kpi_per;
								}
								if($company_kpi_per>0)
								{
									$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
									
								}
								if($po_planQty_tillCalculteArr[$day_count]>0)
								{
									
									$global_plan_day_chkArr[$day_count]+=1;
									$Grand_del_event_kpi_perArr[$day_count]=1;
								}
							
								
							
								 
				 ?>
					  <td width="80" title="Del Prod/Plan*100"> <b><?=fn_number_format($company_kpi_per,2); ?> </b></td>
             <?
						 }
					 }
				}
			 ?>
            </tr>
            
            
			<tr bgcolor="#CC0">
           
		   <td> Grand KPI Per</td>
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
					
					   if($year_mon=='MAY-2023' || $year_mon=='JUN-2023' || $year_mon=='JUL-2023')
					   {
						$global_plan_day=1;  
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
							   $tot_grnd=0;
							   $tot_wash= $Grand_wash_event_kpi_perArr[$day_count];
							    $tot_del= $Grand_del_event_kpi_perArr[$day_count];
							    $tot_fin= $Grand_fin_event_kpi_perArr[$day_count];
							   $tot_out= $Grand_out_event_kpi_perArr[$day_count];
							   $tot_in= $Grand_in_event_kpi_perArr[$day_count];
							   $tot_qc= $Grand_qc_event_kpi_perArr[$day_count];
							   $tot_grnd=$tot_qc+$tot_in+$tot_out+$tot_fin+$tot_wash+$tot_del;
							  // $global_plan_day+=$global_plan_day_chkArr[$day_count];
							 // echo $day_count.'='.$Grand_company_kpi_perArr[$day_count].'/'.$tot_grnd.'<br>';
							   $Grand_company_per=($Grand_company_kpi_perArr[$day_count]/$tot_grnd);

						   
								
				?>
					 <td width="80" title="<?=$Grand_company_kpi_perArr[$day_count].'/'.$tot_grnd;?>"> <b><?=fn_number_format($Grand_company_per,2); ?> </b></td>
			<?
						}
					}
			   }
			?>
		   </tr>
            
	    </table>
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
							
							
							 
							?>
							  <td align="center" title="<?=$unit_id.'='.$com_id;?>"><a href="javascript:void()" onclick="report_generate_by_unit('<? echo $com_id.'_'.$unit_id.'_'.$year_mon;?>','1')"><? echo $unit_val_name;?></a></td>
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

							if($unit_id!=6)  //ash_comp_avg_perArr
							{
								$tot_ash_com_avg_per=$yarn_com_avg_per=$Ash_com_avg_per=0;$tot_ash_com_avg_per=0;
								if($unit_id==1)
								{
									$yarn_com_avg_per=0;
									$yarn_com_avg_per=	$comp_avg_perArr[$com_id][$unit_id];
								}
								if($com_id==2 && $unit_id==3) //Ashulia Kal Location
								{
									$Ash_com_avg_per=0;
								$Ash_com_avg_per=$ash_comp_avg_perArr[$com_id][3];	
								//+$ash_in_comp_avg_perArr[$com_id][3]+$ash_out_comp_avg_perArr[$com_id][3]+$ash_fin_comp_avg_perArr[$com_id][3]			
								}
								if($com_id==2 && $unit_id==4) //Ratanpur Kal Location
								{
									$rat_Ash_com_avg_per=0;
								$rat_Ash_com_avg_per=$ash_comp_avg_perArr[$com_id][4];
								
								}
								if($com_id==2 && $unit_id==5) //Marchan Kal Location
								{
									 
								$tot_ash_com_avg_per=0;
								
								}
								
								if($com_id==1 && $unit_id==3) //JM Nayapara Location
								{
									$rat_Ash_com_avg_per=0;
								$nay_Ash_com_avg_per=$nay_comp_avg_perArr[$com_id][6];
								
								}
								else
								{
									$nay_Ash_com_avg_per=0;
								}
								
								$tot_ash_com_avg_per=$yarn_com_avg_per+$Ash_com_avg_per+$rat_Ash_com_avg_per+$nay_Ash_com_avg_per;
								?>
								<td align="center" title="Tot KPI/12">   <?  if($tot_ash_com_avg_per>0) echo fn_number_format($tot_ash_com_avg_per/12,2).'%'; ?></td>
								<?
								$tot_avg_kpiArr[6][$com_id]+=$tot_ash_com_avg_per/12;
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
								$month_wise_kpi_per=0;$diff_days=0;$qc_diff_days=0;
								if($unit_id==1)
								{
									$diff_days=$num_of_plan_days[$com_id][$unit_id][$year_mon];
									// echo $year_mon . "==";
									$month_wise_kpi_per =$yarn_month_wise_kpiArr[$com_id][$unit_id][$year_mon]/$diff_days;//$month_wise_kpi[$unit_id][$year_mon]/$diff_days;
							    } 
								// ==============Ashulia****======= Kal=================
								if($com_id==2 && $unit_id==3)
								{
									$qc_diff_days=$ash_num_of_plan_days[$com_id][3][$year_mon];
									 // echo $ash_qc_month_wise_kpiArr[$com_id][3][$year_mon].'='.$qc_diff_days . "<br>";
									$in_diff_days=$ash_in_num_of_plan_days[$com_id][3][$year_mon];
									$out_diff_days=$ash_out_num_of_plan_days[$com_id][3][$year_mon];
									//ash_fin_month_wise_kpiArr
									$fin_diff_days=$ash_fin_num_of_plan_days[$com_id][3][$year_mon];
									//ash_fin_month_wise_kpiArr
									// echo $kpi_for_day.'=DDDDDDDDDDDD';//$gbl_num_of_plan_daysArr[$com_id][3][$year_mon]
									$mon_days_count=$gbl_num_of_plan_daysArr[$com_id][3][$year_mon];
									//$month_kpi_per =($qc_month_wise_kpi_per+$in_month_wise_kpi_per+$out_month_wise_kpi_per+$fin_month_wise_kpi_per)/$event_mon_count;
									$month_kpi_per =$ash_gbl_comp_mon_avg_perArr[$com_id][3][$year_mon];
									//echo $month_kpi_per.'='.$mon_days_count.'<br>';;
									$diff_days=$month_kpi_per.'/'.$mon_days_count;
									//ash_out_month_wise_kpiArr
									$month_wise_kpi_per =$month_kpi_per/$mon_days_count;

							    } 
								if($com_id==2 && $unit_id==4) //=======Ratanpur========
								{
									
									$mon_days_count=$gbl_num_of_plan_daysArr[$com_id][4][$year_mon];
									$month_kpi_per =$ash_gbl_comp_mon_avg_perArr[$com_id][4][$year_mon];
									$diff_days=$qc_month_wise_kpi_per.'+'.$in_month_wise_kpi_per.'+'.$out_month_wise_kpi_per.'+'.$fin_month_wise_kpi_per;
									$month_wise_kpi_per =$month_kpi_per/$mon_days_count;
							    } 
								if($com_id==1 && $unit_id==3) //=======JM Nayapara========
								{
									$nay_mon_days_count=$nay_gbl_num_of_plan_daysArr[$com_id][6][$year_mon];
									$month_kpi_per =$nay_gbl_comp_mon_avg_perArr[$com_id][6][$year_mon];
									//echo $month_kpi_per.'='.$nay_mon_days_count.'<br>';
									$diff_days=$month_kpi_per.'/'.$nay_mon_days_count;
									$month_wise_kpi_per =$month_kpi_per/$nay_mon_days_count;
							    } 
								 
							   
								if($unit_id!=6)
								{
									 
									if($month_wise_kpi_per>0)
									{
									$tot_mon_kpiPerArr[$com_id][$year_mon]+=$month_wise_kpi_per;
									
									}
								?>
								<td width="80" align="center" title="<?='Tot KpI='.$diff_days.',Days-'.$nay_mon_days_count;?>" ><?  if($month_wise_kpi_per>0) echo fn_number_format($month_wise_kpi_per,2).'%';else echo "";?></td>
								<?
								}
								else
								{ ?>
									<td width="80" align="center" title="Date Wise Avg(<?=$tot_mon_kpiPerArr[$com_id][$year_mon];?>)/5" bgcolor="#CCCCCC" > <?  if($tot_mon_kpiPerArr[$com_id][$year_mon]>0) echo fn_number_format($tot_mon_kpiPerArr[$com_id][$year_mon]/5,2).'%';?></td>
									<?
									if($tot_mon_kpiPerArr[$com_id][$year_mon]>0)
									{
									$tot_overall_kpiPerArr[$year_mon]+=$tot_mon_kpiPerArr[$com_id][$year_mon]/5;
									}
								}
							} 
							//Month Loop end here
							?>
                           
				            
				        </tr>
				        <?
				     
				      
						$j++;$i++;
				        
				    }
					 //Unit
				}
				 //Company
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
		$plandate=strtotime($val['PLAN_DATE']);
		 $plan_poIdArr[$val['POID']]=$val['POID'];
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
		
		$allocat_poIdArr[$val['POID']]=$val['POID'];
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
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
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				//echo $today.'='.$day_chk.'<br>';
				$month_kpi_per=0;
				if($day_chk<=$today)// as on today
				{ 
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
						$today=strtotime($today_date);
						$day_chk=strtotime($date);
						//echo $today.'='.$day_chk.'<br>';
						$month_kpi_per=0;
						if($day_chk<=$today)// as on today
						{
							$day_allocat_qty= $month_wise_kpiArr[$cbo_company_id][$unit_id][$date]['allo'];
							$day_plan_qty= $month_wise_kpiArr[$cbo_company_id][$unit_id][$date]['plan'];

						
							if($day_allocat_qty>0 && $day_plan_qty>0)
							{
							  $month_kpi_per=$day_allocat_qty/$day_plan_qty*100;  
							  
							}
							if($month_kpi_per>100)  $month_kpi_per=100;
						}
						

						
						// $monthYr=date("M-Y",strtotime($day_key));
					 // $yr_month=strtoupper($monthYr);
					 
					 

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

if($action=="report_generate_by_year_ashulia_kal") //Unit Ashulia//Kal and Monthly Date Wise KPI PER%  
{
	$cbo_company_id = str_replace("'","",$company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$cbo_templete_id 	= str_replace("'","",$cbo_templete_id);
	$unit_id 			= str_replace("'","",$report_type);
	if($unit_id==4) $unitid=5;
	else $unitid=3;
	
	$unit_id_name=$unit_id;
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
     $sql_po_plan="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and a.location_name=$unitid and c.task_id in(84,122,86,88)   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and b.id in(20328,20329,20330)   and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result = sql_select($sql_po_plan);
	foreach ($sql_po_plan_result as $val) 
	{
		 $company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		if(($val['LOCATION_NAME']==3 || $val['LOCATION_NAME']==5) && $val['COMPANY_ID']==2) //Location Ashulia//Kal 
		{
			if($val['TASK_ID']==84) //Cutting qc for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_cut_Qc_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==122) //Input for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_input_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==86) //Output  for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_output_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==88) //Gmts Fin  for  Ashulia
			{
				//$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
				$plandate=strtotime($val['PLAN_DATE']);
				$plan_fin_poIdArr[$val['POID']]=$val['POID'];
				$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
	 }
		
		
	}
	
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=89");
	 //oci_commit($con);
	 //==========================******************Ashulia*************************************==================
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 3, $plan_cut_Qc_poIdArr, $empty_arr);//PO ID Ref from=3
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 4, $plan_input_poIdArr, $empty_arr);//PO ID Ref from=4
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 5, $plan_output_poIdArr, $empty_arr);//PO ID Ref from=5
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 6, $plan_fin_poIdArr, $empty_arr);//PO ID Ref from=6
	
	 $sql_po_plan_ashulia="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
	(case when  c.task_id=84  and g.ref_from=3  then c.PLAN_QTY else 0 end) as QC_PLAN_QTY,
	(case when  c.task_id=122  and g.ref_from=4  then c.PLAN_QTY else 0 end) as INPUT_PLAN_QTY,
	(case when  c.task_id=86  and g.ref_from=5  then c.PLAN_QTY else 0 end) as OUT_PLAN_QTY,
	(case when  c.task_id=88  and g.ref_from=6  then c.PLAN_QTY else 0 end) as FIN_PLAN_QTY 
	from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and a.location_name=$unitid   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(3,4,5,6) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $company_conds and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc"; 
	 //and b.id in(20325,20326,20327,20328,20329,20330)
	  $sql_po_plan_result_ashulia = sql_select($sql_po_plan_ashulia);
	  $ashulia_plan_qty_array=array(); $ashulia_in_plan_qty_array=array(); $ashulia_out_plan_qty_array=array(); $ashulia_gFin_plan_qty_array=array();
	  foreach ($sql_po_plan_result_ashulia as $val) 
	  {
		  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		  $loca_id=$val['LOCATION_NAME'];
		  
		  //echo $plandate.'<br>';;
		 $poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			if($val['QC_PLAN_QTY']>0 && $val['TASK_ID']==84 && $val['REF_FROM']==3) //QC
			{
				$ashulia_plan_qty_array[$loca_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['QC_PLAN_QTY'];
			}
			if($val['INPUT_PLAN_QTY']>0 && $val['TASK_ID']==122 && $val['REF_FROM']==4) //Input
			{
				$ashulia_in_plan_qty_array[$loca_id][$plandate][$val['POID']][$val['COMPANY_ID']]['input_planQty']+=$val['INPUT_PLAN_QTY'];
			}
			if($val['OUT_PLAN_QTY']>0 && $val['TASK_ID']==86 && $val['REF_FROM']==5) //Output
			{
				$ashulia_out_plan_qty_array[$loca_id][$plandate][$val['POID']][$val['COMPANY_ID']]['out_planQty']+=$val['OUT_PLAN_QTY'];
			}
			if($val['FIN_PLAN_QTY']>0 && $val['TASK_ID']==88 && $val['REF_FROM']==6) //Fin
			{
				$ashulia_gFin_plan_qty_array[$loca_id][$plandate][$val['POID']][$val['COMPANY_ID']]['fin_planQty']+=$val['FIN_PLAN_QTY'];
			}
		 
		 
		//  $company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
	  }
	  unset($sql_po_plan_result_ashulia);

	     $sql_po_prod_ashulia="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and a.location_name=$unitid  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20328,20329,20330) and c.production_date between '$startDate' and '$endDate' $company_conds order by c.PRODUCTION_DATE asc"; 
	    
		$sql_po_result_prod_ash = sql_select($sql_po_prod_ashulia);
		foreach ($sql_po_result_prod_ash as $val) 
		{
			$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
			if($val['COMPANY_ID']==2) //Kal Ashulia
			{
			$prod_poIdArr[$val['POID']]=$val['POID'];
			$Ash_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 7, $prod_poIdArr, $empty_arr);//PO ID Ref from=7

		  $sql_po_prod_ashulia_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
	  (case when  c.production_type=1    then c.PRODUCTION_QUANTITY else 0 end) as PROD_QC,
	  (case when  c.production_type=4   then c.PRODUCTION_QUANTITY else 0 end) as INPUT_PROD,
	  (case when  c.production_type=5     then c.PRODUCTION_QUANTITY else 0 end) as OUT_PROD,
	  (case when  c.production_type=8     then c.PRODUCTION_QUANTITY else 0 end) as FIN_PROD 
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name=$unitid 
	  and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(7) and g.entry_form=89
	   and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' $company_conds order by c.PRODUCTION_DATE asc"; 
	   //and b.id in(20325,20326,20327,20328,20329,20330)
		$sql_po_result_prod_curr = sql_select($sql_po_prod_ashulia_curr);
		foreach ($sql_po_result_prod_curr as $val) 
		{
			   $loca_id=$val['LOCATION_NAME'];
			   $poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
				$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
				if($val['PRODUCTION_TYPE']==1)
				{
					$ashulia_plan_qty_array[$loca_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_qc']+=$val['PROD_QC'];
				}
				if($val['PRODUCTION_TYPE']==4)
				{
					$ashulia_in_plan_qty_array[$loca_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_in']+=$val['INPUT_PROD'];
				}
				if($val['PRODUCTION_TYPE']==5)
				{
					$ashulia_out_plan_qty_array[$loca_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_out']+=$val['OUT_PROD'];
				}
				if($val['PRODUCTION_TYPE']==8)
				{
					$ashulia_gFin_plan_qty_array[$loca_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_fin']+=$val['FIN_PROD'];
				}
		    

			
		}
		unset($sql_po_result_prod_curr);
	
	// echo "<pre>";
	// print_r($plan_qty_array);
	
	//===================Allocation Wise Calculation=======
	
	 
  // echo "<pre>";
//print_r($till_today_planArr);
 //ksort($till_today_AllQtyArr);
 // =============================Calculation of KPI============= //plan asy  allocatio ni  day dorbo
	$company_kip_cal_Arr=array();//$company_wise_arr=array();
	ksort($ashulia_plan_qty_array);
	ksort($ashulia_in_plan_qty_array);
	ksort($ashulia_out_plan_qty_array);
	ksort($ashulia_gFin_plan_qty_array);
	
	
 
	unset($sql_po_plan_result);
	unset($sql_po_allocate_result);
	 
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=89");
	oci_commit($con);
	disconnect($con); 

	
	$avg_company_kip_perArr=array();$tot_avg_kpiArr=array();//$avg_allo_plan_Day_wise_calArr=array();
	foreach ($company_wise_arr as   $com_id) 
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
							$po_planQty=$po_alloQty=array();  
							$plan_Day_wise_calArr=array();
							
							 
							//============Ashulia Location============
					//==================Qc Prod=======================
					
					 
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_ash = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_ash = date('d-M-Y', strtotime("+1 day", strtotime($newdate_ash)));
							}
							//===================QC Prod==================
							foreach($ashulia_plan_qty_array[$unitid][$newdate_ash] as $poid=>$poData)
							{
								$planQty_qc=$prod_qcQty=0;
								$planQty_qc=$poData[$com_id]['qc_planQty'];
								$prod_qcQty=$poData[$com_id]['prod_qc'];
								if($prod_qcQty=='') $prod_qcQty=0;
								if($planQty_qc>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$po_planQtyQc_arr[$newdate_ash][$poid]=$planQty_qc+$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid];
									$prev_date_planQc[$poid] = $newdate_ash;
								}
								if($planQty_qc>0 || $prod_qcQty>0)
								{
									if($planQty_qc>0 && $prod_qcQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_prodQcQty_arr[$newdate_ash][$poid]=$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid];
										$prev_prodQc_date[$poid] = $newdate_ash;
										//echo "A=".$poid;
									}
									if($planQty_qc==0 && $prod_qcQty>0) 
									{
										$po_prodQcQty_arr[$newdate_ash][$poid]=$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid];
										$prev_prodQc_date[$poid] = $newdate_ash;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planQtycal=0;
									$planQtycal=$ashulia_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['qc_planQty'];//Plan
									if($ashulia_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['prod_qc']!="" || $planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$po_prodQcQty_arr[$newdate_ash][$poid]=$prod_qcQty+$po_prodQcQty_arr[$prev_prodQc_date[$poid]][$poid];
										$prev_prodQc_date[$poid] = $newdate_ash;
									}
								}
							} 
							//======================Qc Prod End================//ashulia_in_plan_qty_array
							//=============Input=========================//ashulia_out_plan_qty_array
							 
							foreach($ashulia_in_plan_qty_array[$unitid][$newdate_ash] as $poid=>$poData)
							{
								$planQty_in=$prod_InQty=0;
								$planQty_in=$poData[$com_id]['input_planQty'];
								$prod_InQty=$poData[$com_id]['prod_in'];
								if($prod_InQty=='') $prod_InQty=0;
								if($planQty_in>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$po_planQtyIn_arr[$newdate_ash][$poid]=$planQty_in+$po_planQtyIn_arr[$prev_date_planIn[$poid]][$poid];
									$prev_date_planIn[$poid] = $newdate_ash;
								}
								if($planQty_in>0 || $prod_InQty>0)
								{
									if($planQty_in>0 && $prod_InQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_prodInQty_arr[$newdate_ash][$poid]=$po_prodInQty_arr[$prev_prodIn_date[$poid]][$poid];
										$prev_prodIn_date[$poid] = $newdate_ash;
										//echo "A=".$poid;
									}
									if($planQty_in==0 && $prod_InQty>0) 
									{
										$po_prodInQty_arr[$newdate_ash][$poid]=$po_prodInQty_arr[$prev_prodIn_date[$poid]][$poid];
										$prev_prodIn_date[$poid] = $newdate_ash;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planInQtycal=0;
									$planInQtycal=$ashulia_in_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['input_planQty'];//Plan
									if($ashulia_in_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['prod_in']!="" || $planInQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$po_prodInQty_arr[$newdate_ash][$poid]=$prod_InQty+$po_prodInQty_arr[$prev_prodIn_date[$poid]][$poid];
										$prev_prodIn_date[$poid] = $newdate_ash;
									}
								}
							} 
							// $poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']];
							//=============Out=========================//ashulia_out_plan_qty_array
							 //ashulia_gFin_plan_qty_array
							foreach($ashulia_out_plan_qty_array[$unitid][$newdate_ash] as $poid=>$poData)
							{
								$planQty_out=$prod_OutQty=0;
								$planQty_out=$poData[$com_id]['out_planQty'];
								$prod_OutQty=$poData[$com_id]['prod_out'];
								if($prod_OutQty=='') $prod_OutQty=0;
								if($planQty_out>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$po_planQtyOut_arr[$newdate_ash][$poid]=$planQty_out+$po_planQtyOut_arr[$prev_date_planOut[$poid]][$poid];
									$prev_date_planOut[$poid] = $newdate_ash;
								}
								if($planQty_out>0 || $prod_OutQty>0)
								{
									if($planQty_out>0 && $prod_OutQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$po_prodOutQty_arr[$newdate_ash][$poid]=$po_prodOutQty_arr[$prev_prodOut_date[$poid]][$poid];
										$prev_prodOut_date[$poid] = $newdate_ash;
										//echo "A=".$poid;
									}
									if($planQty_out==0 && $prod_OutQty>0) 
									{
										$po_prodOutQty_arr[$newdate_ash][$poid]=$po_prodOutQty_arr[$prev_prodOut_date[$poid]][$poid];
										$prev_prodOut_date[$poid] = $newdate_ash;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$planOutQtycal=0;
									$planOutQtycal=$ashulia_out_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['out_planQty'];//Plan
									if($ashulia_out_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['prod_out']!="" || $planOutQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$po_prodOutQty_arr[$newdate_ash][$poid]=$prod_OutQty+$po_prodOutQty_arr[$prev_prodOut_date[$poid]][$poid];
										$prev_prodOut_date[$poid] = $newdate_ash;
									}
								}
							}
							//=============Gmts Fin=========================//ashulia_gFin_plan_qty_array
							 
							 foreach($ashulia_gFin_plan_qty_array[$unitid][$newdate_ash] as $poid=>$poData)
							 {
								 $planQty_fin=$prod_FinQty=0;
								 $planQty_fin=$poData[$com_id]['fin_planQty'];//fin_planQty
								 $prod_FinQty=$poData[$com_id]['prod_fin'];
								 if($prod_FinQty=='') $prod_FinQty=0;
								 if($planQty_fin>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $po_planQtyFin_arr[$newdate_ash][$poid]=$planQty_fin+$po_planQtyFin_arr[$prev_date_planFin[$poid]][$poid];
									 $prev_date_planFin[$poid] = $newdate_ash; 
								 }
								 if($planQty_fin>0 || $prod_FinQty>0)
								 {
									 if($planQty_fin>0 && $prod_FinQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $po_prodFinQty_arr[$newdate_ash][$poid]=$po_prodFinQty_arr[$prev_prodFin_date[$poid]][$poid];
										 $prev_prodFin_date[$poid] = $newdate_ash;
										 //echo "A=".$poid;
									 }
									 if($planQty_fin==0 && $prod_FinQty>0) 
									 {
										 $po_prodFinQty_arr[$newdate_ash][$poid]=$po_prodFinQty_arr[$prev_prodFin_date[$poid]][$poid];
										 $prev_prodFin_date[$poid] = $newdate_ash;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $planFinQtycal=0;
									 $planFinQtycal=$ashulia_gFin_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['fin_planQty'];//Plan
									 if($ashulia_gFin_plan_qty_array[$unitid][$newdate_ash][$poid][$com_id]['prod_fin']!="" || $planFinQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $po_prodFinQty_arr[$newdate_ash][$poid]=$prod_FinQty+$po_prodFinQty_arr[$prev_prodFin_date[$poid]][$poid];
										 $prev_prodFin_date[$poid] = $newdate_ash;
									 }
								 }
							 }
							
						} 
						//===========Fin End
						//Days loop end
				    
						} 
						//Days loop end
			} 
			//Month Loop end here //Month Loop
		 //unit
	}
	// print_r($po_prodQcQty_arr);

	  //=======================Kal Ashulia=======================
	   
	  foreach($Ash_com_poIdArr as $comp_id=>$comData)
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
			  //	$company_kip_cal_Arr=array();//$po_planQtyArr=array();
				   
					  //if($year_mon=='MAY-2023' || $year_mon=='JUN-2023'  || $year_mon=='JUL-2023')
					  //{
						  for($j=0;$j<$diff_days;$j++)
						  {
							  if($j==0)
							  {
								  $day_all = date('d-M-Y', strtotime($from_date));
								  
							  }
							  else
							  {
								  $day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
								  
							  }

							  //echo $day_all.'='.$po_alloQty_arr[$day_all][$poid].'/'.$po_planQty_arr[$day_all][$poid].'<br>';
							  //==========Qc Prod and Qc Plan==============
							  $planQcQtycal=0;
							  $planQcQtycal=$ashulia_plan_qty_array[$unitid][$day_all][$poid][$comp_id]['qc_planQty'];//alloQty
							  //$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
							   // echo $day_all.'='.$unitid.'<br>';
							  if($planQcQtycal>0)
							  {
								 
								  $mon_qc_qty=$po_prodQcQty_arr[$day_all][$poid];
								 
								  $qc_month_wise_kpiArr[$comp_id][$day_all]['qc_prod']+=$mon_qc_qty;
							  }
							  $qc_month_wise_kpiArr[$comp_id][$day_all]['qc_plan']+=$po_planQtyQc_arr[$day_all][$poid];

							  //==========In Prod and In Plan==============
							  $planInQtycal=0;
							  $planInQtycal=$ashulia_in_plan_qty_array[$unitid][$day_all][$poid][$comp_id]['input_planQty'];//alloQty
							  //$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
							  
							  if($planInQtycal>0)
							  {
								  $mon_in_qty=$po_prodInQty_arr[$day_all][$poid];
								  $in_month_wise_kpiArr[$comp_id][$day_all]['in_prod']+=$mon_in_qty;
							  }
							  $in_month_wise_kpiArr[$comp_id][$day_all]['in_plan']+=$po_planQtyIn_arr[$day_all][$poid];

							  //==========Out Qty Prod and Out Plan Qty==============
							  $planOutQtycal=0;
							  $planOutQtycal=$ashulia_out_plan_qty_array[$unitid][$day_all][$poid][$comp_id]['out_planQty'];//alloQty
							  //$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
							  //po_prodOutQty_arr
							  if($planOutQtycal>0)
							  {
								  $mon_out_qty=$po_prodOutQty_arr[$day_all][$poid];
								  $out_month_wise_kpiArr[$comp_id][$day_all]['out_prod']+=$mon_out_qty;
							  }
							  $out_month_wise_kpiArr[$comp_id][$day_all]['out_plan']+=$po_planQtyOut_arr[$day_all][$poid];

							  //==========GMTS Fin Qty Prod and Fin Plan Qty==============
							  $planFinQtycal=0;//po_prodFinQty_arr
							  $planFinQtycal=$ashulia_gFin_plan_qty_array[$unitid][$day_all][$poid][$comp_id]['fin_planQty'];
							  //$ashulia_plan_qty_array[3][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']
							  //po_prodOutQty_arr
							  if($planFinQtycal>0)
							  {
								  $mon_fin_qty=$po_prodFinQty_arr[$day_all][$poid];
								  $fin_month_wise_kpiArr[$comp_id][$day_all]['fin_prod']+=$mon_fin_qty;
							  }
							  $fin_month_wise_kpiArr[$comp_id][$day_all]['fin_plan']+=$po_planQtyFin_arr[$day_all][$poid];
					   }
				   //}
				  
			  }
	   }
	 }
	 //=========For Ashulia Location================
	// print_r($qc_month_wise_kpiArr);
	 foreach($qc_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	 {
		 foreach($comData as $day_key=>$row)
		 {
			$qc_month_prod=$row['qc_prod']; 
			 $qc_month_plan=$row['qc_plan'];
		   
			 $qc_month_kpi_per=$qc_month_prod/$qc_month_plan*100; 
				 $monthYr=date("M-Y",strtotime($day_key));
			  $yr_month=strtoupper($monthYr);
			  $today=strtotime($today_date);
			  $day_chk=strtotime($day_key);
			  //echo $today.'='.$day_chk.'<br>';
			  if($day_chk<=$today)// as on today
			  {
				  //echo $day_key.'='.$today_date.'<br>';
				  if($qc_month_prod>0 && $qc_month_plan>0)
				  {
					  if($qc_month_kpi_per>100)  $qc_month_kpi_per=100;
					  // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
				  $ash_qc_month_wise_kpiArr[$comp_id][$unitid][$yr_month]+=$qc_month_kpi_per;
				  $ash_month_wise_kpiArr[$comp_id][$unitid][$day_key]['qc']+=$qc_month_kpi_per;
				  }
				  if($qc_month_plan>0)
				  {
					  $mon_grand_qc_event_kpi_perArr[$comp_id][$unitid][$day_key]=1;

					  $ash_num_of_plan_days[$comp_id][$unitid][$day_key]++;
					  $gbl_num_of_plan_daysArr[$comp_id][$unitid][$yr_month]++;
				  }
			  }
		 }
	 }
	  

	 //========****************===============
	  //=========For Ashulia in Location================//in_month_wise_kpiArr
	  foreach($in_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	  {
		  foreach($comData as $day_key=>$row)
		  {
			 $in_month_prod=$row['in_prod']; 
			  $in_month_plan=$row['in_plan'];
			
			  $in_month_kpi_per=$in_month_prod/$in_month_plan*100; 
			  $monthYr=date("M-Y",strtotime($day_key));
			   $yr_month=strtoupper($monthYr);
			   $today=strtotime($today_date);
			   $day_chk=strtotime($day_key);
			   //echo $today.'='.$day_chk.'<br>';
			   if($day_chk<=$today)// as on today
			   {
				   //echo $day_key.'='.$today_date.'<br>';
				   if($in_month_prod>0 && $in_month_plan>0)
				   {
					   if($in_month_kpi_per>100)  $in_month_kpi_per=100;
					   // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
				   $ash_in_month_wise_kpiArr[$comp_id][$unitid][$yr_month]+=$in_month_kpi_per;
				   $ash_month_wise_kpiArr[$comp_id][$unitid][$day_key]['in']+=$in_month_kpi_per;
				   }
				  $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$day_key]['qc_plan'];
				   if($in_month_plan>0)
				   {
					   $ash_in_num_of_plan_days[$comp_id][$unitid][$yr_month]++;
					   $mon_grand_in_event_kpi_perArr[$comp_id][$unitid][$day_key]=1;
				   }
				   if($in_month_plan>0 && !$qc_plan_day_chk)
				   {
					  $gbl_num_of_plan_daysArr[$comp_id][$unitid][$yr_month]++;
					  
				   }
			   }
		  }
	  }
		 // print_r($mon_grand_qc_event_kpi_perArr);
		// echo "<pre>";
	   
	  //=========For Ashulia **Out** Location================//out_month_wise_kpiArr
	  foreach($out_month_wise_kpiArr as $comp_id=>$comData) //Month Wise Out KPI Percentage summary part
	  {
		  foreach($comData as $day_key=>$row)
		  {
			 $out_month_prod=$row['out_prod']; 
			  $out_month_plan=$row['out_plan'];
			
			  $out_month_kpi_per=$out_month_prod/$out_month_plan*100; 
				  $monthYr=date("M-Y",strtotime($day_key));
			   $yr_month=strtoupper($monthYr);
			   $today=strtotime($today_date);
			   $day_chk=strtotime($day_key);
			   //echo $today.'='.$day_chk.'<br>';
			   if($day_chk<=$today)// as on today
			   {
				   //echo $day_key.'='.$today_date.'<br>';
				   if($out_month_prod>0 && $out_month_plan>0)
				   {
					   if($out_month_kpi_per>100)  $out_month_kpi_per=100;
					   // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
				   $ash_out_month_wise_kpiArr[$comp_id][$unitid][$yr_month]+=$out_month_kpi_per;

				   $ash_month_wise_kpiArr[$comp_id][$unitid][$day_key]['out']+=$out_month_kpi_per;
				   }
				   $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$day_key]['qc_plan'];
				   $in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$day_key]['in_plan'];
				   
				   if($out_month_plan>0)
				   {
					   $ash_out_num_of_plan_days[$comp_id][$unitid][$yr_month]++;
					   $mon_grand_out_event_kpi_perArr[$comp_id][$unitid][$day_key]=1;
				   }
				   if($out_month_plan>0 && (!$in_plan_day_chk && !$qc_plan_day_chk)) 
				   {
					  $gbl_num_of_plan_daysArr[$comp_id][$unitid][$yr_month]++;
					  $mon_grand_qc_event_kpi_perArr[$comp_id][$unitid][$day_key]+=1;
				   }
			   }
		  }
	  }
		//  print_r($mon_grand_qc_event_kpi_perArr);
	   
//=========For Ashulia **Gmts Fin** Location================//out_month_wise_kpiArr
foreach($fin_month_wise_kpiArr as $comp_id=>$comData)  
{
  foreach($comData as $day_key=>$row)
  {
	 $fin_month_prod=$row['fin_prod']; 
	  $fin_month_plan=$row['fin_plan'];
	
	  $fin_month_kpi_per=$fin_month_prod/$fin_month_plan*100; 
	  $monthYr=date("M-Y",strtotime($day_key));
	   $yr_month=strtoupper($monthYr);
	   $today=strtotime($today_date);
	   $day_chk=strtotime($day_key);
	   //echo $today.'='.$day_chk.'<br>';
	   if($day_chk<=$today)// as on today
	   {
		   //echo $day_key.'='.$today_date.'<br>';
		   if($fin_month_prod>0 && $fin_month_plan>0)
		   {
			   if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
			   // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
		   $ash_fin_month_wise_kpiArr[$comp_id][$unitid][$yr_month]+=$fin_month_kpi_per;
		   $ash_month_wise_kpiArr[$comp_id][$unitid][$day_key]['fin']+=$fin_month_kpi_per;
		   }
		   $out_plan_day_chk= $out_month_wise_kpiArr[$comp_id][$day_key]['out_plan'];
		   $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$day_key]['qc_plan'];
		   $in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$day_key]['in_plan'];

		   if($fin_month_plan>0)
		   {
			   $ash_fin_num_of_plan_days[$comp_id][$unitid][$yr_month]++;
			   $mon_grand_fin_event_kpi_perArr[$comp_id][$unitid][$day_key]=1;
		   }
		  if($fin_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk))
		  {
			  $gbl_num_of_plan_daysArr[$comp_id][$unitid][$yr_month]++;
			  //$mon_grand_qc_event_kpi_perArr[$yr_month]+=1;
		  }

	   }
  }
}
	//print_r($mon_grand_qc_event_kpi_perArr);
//echo "<pre>";
foreach($ash_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
   foreach($comData as $unitid=>$monData)  
   {
	   foreach($monData as $day_key=>$per)  
	   {
		   
		  $event_qc_count=$mon_grand_qc_event_kpi_perArr[$comp_id][$unitid][$day_key];//$mon_grand_qc_event_kpi_perArr
		  $event_in_count=$mon_grand_in_event_kpi_perArr[$comp_id][$unitid][$day_key];
		  $event_out_count=$mon_grand_out_event_kpi_perArr[$comp_id][$unitid][$day_key];
		  $event_fin_count=$mon_grand_fin_event_kpi_perArr[$comp_id][$unitid][$day_key];
		  $event_mon_count= $event_qc_count+$event_in_count+$event_out_count+$event_fin_count;
		  
		  $all_kpi_per=$per['qc']+$per['in']+$per['out']+$per['fin'];
		  //  echo $day_key.'='.$all_kpi_per.'='.$mon_days_count.'='.$event_mon_count.'<br>';
		  $ash_gbl_comp_avg_perArr[$comp_id][$unitid][$day_key]+=$all_kpi_per/$event_mon_count;
	   }
   }
}
 // echo "<pre>";
 //print_r($ash_gbl_comp_avg_perArr);
foreach($ash_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
   foreach($comData as $unitid=>$monData)  
   {
	   foreach($monData as $day_key=>$val)  
	   {
		  $monthYr=date("M-Y",strtotime($day_key));
		  $yr_month=strtoupper($monthYr);
	  //	 echo $day_key.'='.$val.'='.$mon_days_count.'<br>';
		  $ash_gbl_comp_mon_avg_perArr[$comp_id][$unitid][$yr_month]+=$val;
	   }
   }
}
	  //  echo "<pre>";
	  // print_r($ash_gbl_comp_mon_avg_perArr);
	//print_r($mon_wise_allocateQty_tillCalculateArr);
	  //  echo "<pre>"; 	
	  	 
  
	$tbl_width = 240+(count($fiscalMonth_arr)*80);
	ob_start();	
	
	$tbl_width2 = 5000;
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total PO wise dtls Start============================= style="display:none" =====================================-->

       
        <?

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
	            
	            <th width="80">Date S</th>
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
						$today=strtotime($today_date);
						$day_chk=strtotime($date);
						//echo $today.'='.$day_chk.'<br>';
						$month_kpi_per=0; 
						if($day_chk<=$today)// as on today
						{
							$ash_gbl_comp_avg_kip=$ash_gbl_comp_avg_perArr[$cbo_company_id][$unitid][$date];
							$month_kpi_per=$ash_gbl_comp_avg_kip;
							
						}
						

						?>
						<td width="80" align="center" title="<??>"><?  echo fn_number_format($month_kpi_per,2).'%';?></td>
						<?
							if($month_kpi_per>0)
							{
							$date_wise_KPIArr[$unitid][$year_mon]+=$month_kpi_per;
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
					if($num_of_plan_daysArr[$cbo_company_id][$unitid][$year_mon]>0)
					{
					//	$tot_mon_days=$num_of_plan_daysArr[$cbo_company_id][$unit_id][$year_mon];
						//echo $tot_days.', ';
					}
					$tot_mon_days=$gbl_num_of_plan_daysArr[$cbo_company_id][$unitid][$year_mon];
					
					$tot_kpi_per=0;$tot_avg_kpi_per=0;
					$tot_kpi_per=$date_wise_KPIArr[$unitid][$year_mon];
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
if($action=="report_generate_by_year_nayapara_sew_kal") //Unit Ashulia//Kal and Monthly Date Wise KPI PER%  
{
	$cbo_company_id = str_replace("'","",$company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$cbo_templete_id 	= str_replace("'","",$cbo_templete_id);
	$unit_id 			= str_replace("'","",$report_type);
	if($unit_id==3) $unitid=6;
	else $unitid=3;
	
	$unit_id_name=$unit_id;
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
     //============********JM Nayapara Start here*****===========================
		 //============************************JM *******************************************
		
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=89");
		 
	   $sql_po_plan_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(84,122,86,88,90) and a.location_name=6  and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337) and c.PLAN_DATE between '$startDate' and '$endDate'  order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result_jm = sql_select($sql_po_plan_jm);
	foreach($sql_po_plan_result_jm  as $val)
	{
		if($val['COMPANY_ID']==1) //Location //JM 
		{
			$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
			$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			if($val['TASK_ID']==84) //Cutting qc for  Nayapara
			{
				$jm_plan_cut_Qc_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==122) //Input for  Nayapara
			{
				$jm_plan_input_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==86) //Output  for  Nayapara
			{
				$jm_plan_output_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==88) //Gmts Fin  for  Nayapara
			{
				
				$jm_plan_fin_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			if($val['TASK_ID']==90) //Gmts Wash  for  Nayapara
			{
				
				$jm_plan_wash_poIdArr[$val['POID']]=$val['POID'];
				$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
	    }
			
	}
 $sql_actual_po_plan_jm="SELECT c.id as acul_poid,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name=6    and c.acc_po_qty>0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337) and c.acc_ship_date between '$startDate' and '$endDate'  order by acul_poid, PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_plan_result_jm = sql_select($sql_actual_po_plan_jm);
	foreach($sql_actual_po_plan_result_jm  as $val)
	{
		$jm_main_plan_wash_poIdArr[$val['POID']]=$val['POID'];
		$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}
	
	//============================Jm Plan============================
	//==========================******************Nayapara*************************************==================
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 9, $jm_plan_cut_Qc_poIdArr, $empty_arr);//PO ID Ref from=9
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 10, $jm_plan_input_poIdArr, $empty_arr);//PO ID Ref from=10
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 11, $jm_plan_output_poIdArr, $empty_arr);//PO ID Ref from=11
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 12, $jm_plan_fin_poIdArr, $empty_arr);//PO ID Ref from=12
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 13, $jm_plan_wash_poIdArr, $empty_arr);//PO ID Ref from=13

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 15, $jm_main_plan_wash_poIdArr, $empty_arr);//PO ID Ref from=15

	// for actual po ship qty
	 $sql_actual_qty_po_plan_jm="SELECT c.id as ACUL_POID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.ACC_PO_NO, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c, gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name=6  and c.acc_po_qty>0 and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(15) and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337)  and c.ACC_SHIP_DATE <= '$endDate'  order by  PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_qty_plan_result_jm = sql_select($sql_actual_qty_po_plan_jm);
	foreach($sql_actual_po_qty_plan_result_jm  as $val)
	{
		$plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		$loc_id=$val['LOCATION_NAME']; 
		//$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['PLAN_QTY'];
		$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
		$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
		$nay_acl_delivery_qty_array[$loc_id][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
		$acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
		$acl_jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];

	}
//print_r($nay_actual_plan_qty_array);pro_ex_factory_mst
 $sql_actual_po_delivery_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 pro_ex_factory_mst c,pro_ex_factory_actual_po_details d  where a.id=b.job_id and b.id=c.po_break_down_id
  and c.id=d.mst_id and a.company_name=1  and a.location_name=6 and d.ex_fact_qty>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
 and a.company_name=1 and b.id in(20334,20335,20337) and c.ex_factory_date between '$startDate' and '$endDate'  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_result_jm = sql_select($sql_actual_po_delivery_jm);
	foreach($sql_actual_po_delivery_result_jm  as $val)
	{
		$jm_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 16, $jm_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=16


	 $sql_actual_po_qty_delivery_jm="SELECT d.actual_po_id as ACTUAL_PO_ID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, d.ex_fact_qty as DEL_QTY,c.ex_factory_date as EX_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, pro_ex_factory_mst c,pro_ex_factory_actual_po_details d,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id  and c.id=d.mst_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(16) and g.entry_form=89  and a.location_name=6 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and d.ex_fact_qty>0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=1 and b.id in(20334,20335,20337) and c.ex_factory_date<= '$endDate'  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_qty_result_jm = sql_select($sql_actual_po_qty_delivery_jm);
	foreach ($sql_actual_po_delivery_qty_result_jm as $val) 
	{
		$ex_date=date('d-M-Y',strtotime($val['EX_DATE']));
	  	$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		 $loc_id=$val['LOCATION_NAME'];
		// $nay_actual_poIdplan_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['acul_poid'].',';
		 $nay_acl_delivery_qty_array[$loc_id][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		 $nay_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		 $nay_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
	}
	//print_r($nay_actual_plan_qty_array);

//=======Delivery===============
	
  //=======Delivery===============
	
   $sql_po_plan_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
	(case when  c.task_id=84  and g.ref_from=9  then c.PLAN_QTY else 0 end) as QC_PLAN_QTY,
	(case when  c.task_id=122  and g.ref_from=10  then c.PLAN_QTY else 0 end) as INPUT_PLAN_QTY,
	(case when  c.task_id=86  and g.ref_from=11  then c.PLAN_QTY else 0 end) as OUT_PLAN_QTY,
	(case when  c.task_id=88  and g.ref_from=12  then c.PLAN_QTY else 0 end) as FIN_PLAN_QTY,
	(case when  c.task_id=90  and g.ref_from=13  then c.PLAN_QTY else 0 end) as WASH_PLAN_QTY 
	from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and a.company_name=1 and a.location_name in(6)   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(9,10,11,12,13) and  c.task_id in(84,122,86,88,90) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc";
	 //and b.id in(20325,20326,20327,20328,20329,20330)
	  $sql_po_plan_result_jm = sql_select($sql_po_plan_jm);
	 $jm_plan_qty_array=array(); $jm_in_plan_qty_array=array(); $jm_out_plan_qty_array=array(); $jm_gFin_plan_qty_array=array();
	 
	  foreach ($sql_po_plan_result_jm as $val) 
	  {
		  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			
			 $loc_id=$val['LOCATION_NAME']; 
			if($val['QC_PLAN_QTY']>0 && $val['TASK_ID']==84 && $val['REF_FROM']==9) //QC
			{
				$nay_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['QC_PLAN_QTY'];
				//echo $plandate.'=='.$val['QC_PLAN_QTY'].'<br>';
			}
			if($val['INPUT_PLAN_QTY']>0 && $val['TASK_ID']==122 && $val['REF_FROM']==10) //Input
			{
				$nay_in_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['input_planQty']+=$val['INPUT_PLAN_QTY'];
			}
			if($val['OUT_PLAN_QTY']>0 && $val['TASK_ID']==86 && $val['REF_FROM']==11) //Output
			{
				$nay_out_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['out_planQty']+=$val['OUT_PLAN_QTY'];
			}
			if($val['FIN_PLAN_QTY']>0 && $val['TASK_ID']==88 && $val['REF_FROM']==12) //Fin
			{
				$nay_gFin_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['fin_planQty']+=$val['FIN_PLAN_QTY'];
			}
			if($val['WASH_PLAN_QTY']>0 && $val['TASK_ID']==90 && $val['REF_FROM']==13) //Wash
			{
				$nay_gWash_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['wash_planQty']+=$val['WASH_PLAN_QTY'];
			}

	  }
	  unset($sql_po_plan_result_jm);
	 //  print_r($company_wise_arr);
	  
	    $sql_po_prod_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(1,3,4,5,8) and a.location_name in(6)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20334,20335,20337) and a.company_name=1 and c.production_date between '$startDate' and '$endDate' and c.production_type in(1,4,5,8,3)  order by c.PRODUCTION_DATE asc"; //and a.location_name=3
	    
		$sql_po_result_prod_jm = sql_select($sql_po_prod_jm);
		foreach ($sql_po_result_prod_jm as $val) 
		{
			if($val['COMPANY_ID']==1) //JM
			{
			$jm_prod_poIdArr[$val['POID']]=$val['POID'];
			$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 14, $jm_prod_poIdArr, $empty_arr);//PO ID Ref from=14

	  $sql_po_prod_jm_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.EMBEL_NAME,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
	  (case when  c.production_type=1    then c.PRODUCTION_QUANTITY else 0 end) as PROD_QC,
	  (case when  c.production_type=4   then c.PRODUCTION_QUANTITY else 0 end) as INPUT_PROD,
	  (case when  c.production_type=5     then c.PRODUCTION_QUANTITY else 0 end) as OUT_PROD,
	  (case when  c.production_type=8     then c.PRODUCTION_QUANTITY else 0 end) as FIN_PROD ,
	  (case when  c.production_type=3 and c.embel_name=3  then c.PRODUCTION_QUANTITY else 0 end) as WASH_PROD
	   
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
	  and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(14) and g.entry_form=89 and a.location_name in(6)
	   and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0 and c.production_type in(1,4,5,8,3) and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' order by c.PRODUCTION_DATE asc";// and a.location_name=3
	   //and b.id in(20325,20326,20327,20328,20329,20330)
		$sql_po_result_prod_jm_curr = sql_select($sql_po_prod_jm_curr);
		foreach ($sql_po_result_prod_jm_curr as $val) 
		{
			 
			$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
			$loc_id=$val['LOCATION_NAME'];
			$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
			if($val['PRODUCTION_TYPE']==1)
			{
				//echo $loc_id.'='.$val['POID'].'='.$val['PROD_QC'].'<br>';
				$nay_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_qc']+=$val['PROD_QC'];
			}
			if($val['PRODUCTION_TYPE']==4)
			{
				$nay_in_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_in']+=$val['INPUT_PROD'];
			}
			if($val['PRODUCTION_TYPE']==5)
			{
				$nay_out_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_out']+=$val['OUT_PROD'];
			}
			if($val['PRODUCTION_TYPE']==8)
			{
				$nay_gFin_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_fin']+=$val['FIN_PROD'];
			}
			if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==3)
			{
				$nay_gWash_plan_qty_array[$loc_id][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_wash']+=$val['WASH_PROD'];
			}
		}
		 unset($sql_po_result_prod_jm_curr);
	
	// echo "<pre>";
	// print_r($plan_qty_array);
	
	//===================Allocation Wise Calculation=======
	
	 
  // echo "<pre>";
//print_r($till_today_planArr);
 //ksort($till_today_AllQtyArr);
 // =============================Calculation of KPI============= //plan asy  allocatio ni  day dorbo
	$company_kip_cal_Arr=array();//$company_wise_arr=array();
	ksort($nay_plan_qty_array);
	ksort($nay_in_plan_qty_array);
	ksort($nay_out_plan_qty_array);
	ksort($nay_gFin_plan_qty_array);
	ksort($nay_gWash_plan_qty_array);
	ksort($nay_actual_plan_qty_array);
	ksort($nay_acl_delivery_qty_array);
	
	
 
	unset($sql_po_plan_result);
	unset($sql_po_allocate_result);
	 
	
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and   ENTRY_FORM=89");
	oci_commit($con);
	disconnect($con); 

	
	$avg_company_kip_perArr=array();$tot_avg_kpiArr=array();//$avg_allo_plan_Day_wise_calArr=array();
	foreach ($company_wise_arr as   $com_id) 
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
						$plan_cumulative_cal=0;$allo_cumulative_cal=0;$tot_kpi_cal = 0;
						$allo_Day_wise_calArr=array();
						 
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_nay = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_nay = date('d-M-Y', strtotime("+1 day", strtotime($newdate_nay)));
							}
							//===================QC Prod==================
							foreach($nay_plan_qty_array[6][$newdate_nay] as $poid_q=>$poData)
							{
								$nay_planQty_qc=$nay_prod_qcQty=0;
								$nay_planQty_qc=$poData[$com_id]['qc_planQty'];
								$nay_prod_qcQty=$poData[$com_id]['prod_qc'];
								if($nay_prod_qcQty=='') $nay_prod_qcQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($nay_planQty_qc>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$nay_po_planQtyQc_arr[$newdate_nay][$poid_q]=$nay_planQty_qc+$nay_po_planQtyQc_arr[$nay_prev_date_planQc[$poid_q]][$poid_q];
									$nay_prev_date_planQc[$poid_q] = $newdate_nay;
								}
								if($nay_planQty_qc>0 || $nay_prod_qcQty>0)
								{
									if($nay_planQty_qc>0 && $nay_prod_qcQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$nay_po_prodQcQty_arr[$newdate_nay][$poid_q]=$nay_po_prodQcQty_arr[$nay_prev_prodQc_date[$poid_q]][$poid_q];
										$nay_prev_prodQc_date[$poid_q] = $newdate_nay;
										//echo "A=".$poid;
									}
									if($nay_planQty_qc==0 && $nay_prod_qcQty>0) 
									{
										$nay_po_prodQcQty_arr[$newdate_nay][$poid_q]=$nay_po_prodQcQty_arr[$nay_prev_prodQc_date[$poid_q]][$poid_q];
										$nay_prev_prodQc_date[$poid_q] = $newdate_nay;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$nay_planQtycal=0;
									$nay_planQtycal=$nay_plan_qty_array[6][$newdate_nay][$poid_q][$com_id]['qc_planQty'];//Plan
									if($nay_plan_qty_array[6][$newdate_nay][$poid_q][$com_id]['prod_qc']!="" || $nay_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$nay_po_prodQcQty_arr[$newdate_nay][$poid_q]=$nay_prod_qcQty+$nay_po_prodQcQty_arr[$nay_prev_prodQc_date[$poid_q]][$poid_q];
										$nay_prev_prodQc_date[$poid_q] = $newdate_nay;
									}
								}
							} 
							//======================Qc Prod End================//ashulia_in_plan_qty_array
							//=============Input=========================//ashulia_out_plan_qty_array
							 
							foreach($nay_in_plan_qty_array[6][$newdate_nay] as $poid_in=>$poData)
							{
								$nay_planQty_in=$nay_prod_InQty=0;
								$nay_planQty_in=$poData[$com_id]['input_planQty'];
								$nay_prod_InQty=$poData[$com_id]['prod_in'];
								if($nay_prod_InQty=='') $nay_prod_InQty=0;
								if($nay_planQty_in>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$nay_po_planQtyIn_arr[$newdate_nay][$poid_in]=$nay_planQty_in+$nay_po_planQtyIn_arr[$nay_prev_date_planIn[$poid_in]][$poid_in];
									$nay_prev_date_planIn[$poid_in] = $newdate_nay;
								}
								if($nay_planQty_in>0 || $nay_prod_InQty>0)
								{
									if($nay_planQty_in>0 && $nay_prod_InQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$nay_po_prodInQty_arr[$newdate_nay][$poid_in]=$nay_po_prodInQty_arr[$nay_prev_prodIn_date[$poid_in]][$poid_in];
										$nay_prev_prodIn_date[$poid_in] = $newdate_nay;
										//echo "A=".$poid;
									}
									if($nay_planQty_in==0 && $nay_prod_InQty>0) 
									{
										$nay_po_prodInQty_arr[$newdate_nay][$poid_in]=$nay_po_prodInQty_arr[$nay_prev_prodIn_date[$poid_in]][$poid_in];
										$nay_prev_prodIn_date[$poid_in] = $newdate_nay;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$nay_planInQtycal=0;
									$nay_planInQtycal=$nay_in_plan_qty_array[6][$newdate_nay][$poid_in][$com_id]['input_planQty'];//Plan
									if($nay_in_plan_qty_array[6][$newdate_nay][$poid_in][$com_id]['prod_in']!="" || $nay_planInQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$nay_po_prodInQty_arr[$newdate_nay][$poid_in]=$nay_prod_InQty+$nay_po_prodInQty_arr[$nay_prev_prodIn_date[$poid_in]][$poid_in];
										$nay_prev_prodIn_date[$poid_in] = $newdate_nay;
									}
								}
							} 
							//=============Out=========================//ashulia_out_plan_qty_array
							 //ashulia_gFin_plan_qty_array
							foreach($nay_out_plan_qty_array[6][$newdate_nay] as $poid_out=>$poData)
							{
								$nay_planQty_out=$nay_prod_OutQty=0;
								$nay_planQty_out=$poData[$com_id]['out_planQty'];
								$nay_prod_OutQty=$poData[$com_id]['prod_out'];
								if($nay_prod_OutQty=='') $nay_prod_OutQty=0;
								if($nay_planQty_out>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$nay_po_planQtyOut_arr[$newdate_nay][$poid_out]=$nay_planQty_out+$nay_po_planQtyOut_arr[$nay_prev_date_planOut[$poid_out]][$poid_out];
									$nay_prev_date_planOut[$poid_out] = $newdate_nay;
								}
								if($nay_planQty_out>0 || $nay_prod_OutQty>0)
								{
									if($nay_planQty_out>0 && $nay_prod_OutQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$nay_po_prodOutQty_arr[$newdate_nay][$poid_out]=$nay_po_prodOutQty_arr[$nay_prev_prodOut_date[$poid_out]][$poid_out];
										$nay_prev_prodOut_date[$poid_out] = $newdate_nay;
										//echo "A=".$poid;
									}
									if($nay_planQty_out==0 && $nay_prod_OutQty>0) 
									{
										$nay_po_prodOutQty_arr[$newdate_nay][$poid_out]=$nay_po_prodOutQty_arr[$nay_prev_prodOut_date[$poid_out]][$poid_out];
										$nay_prev_prodOut_date[$poid_out] = $newdate_nay;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$nay_planOutQtycal=0;
									$nay_planOutQtycal=$nay_out_plan_qty_array[6][$newdate_nay][$poid_out][$com_id]['out_planQty'];//Plan
									if($nay_out_plan_qty_array[6][$newdate_nay][$poid_out][$com_id]['prod_out']!="" || $nay_planOutQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$nay_po_prodOutQty_arr[$newdate_nay][$poid_out]=$nay_prod_OutQty+$nay_po_prodOutQty_arr[$nay_prev_prodOut_date[$poid_out]][$poid_out];
										$nay_prev_prodOut_date[$poid_out] = $newdate_nay;
									}
								}
							}
							//=============Gmts Fin=========================//ashulia_gFin_plan_qty_array
							 
							 foreach($nay_gFin_plan_qty_array[6][$newdate_nay] as $poid_fin=>$poData)
							 {
								 $nay_planQty_fin=$nay_prod_FinQty=0;
								 $nay_planQty_fin=$poData[$com_id]['fin_planQty'];//fin_planQty
								 $nay_prod_FinQty=$poData[$com_id]['prod_fin'];
								 if($prod_FinQty=='') $prod_FinQty=0;
								 if($nay_planQty_fin>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $nay_po_planQtyFin_arr[$newdate_nay][$poid_fin]=$nay_planQty_fin+$nay_po_planQtyFin_arr[$nay_prev_date_planFin[$poid_fin]][$poid_fin];
									 $nay_prev_date_planFin[$poid_fin] = $newdate_nay; 
								 }
								 if($nay_planQty_fin>0 || $nay_prod_FinQty>0)
								 {
									 if($nay_planQty_fin>0 && $nay_prod_FinQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $nay_po_prodFinQty_arr[$newdate_nay][$poid_fin]=$nay_po_prodFinQty_arr[$nay_prev_prodFin_date[$poid_fin]][$poid_fin];
										 $nay_prev_prodFin_date[$poid_fin] = $newdate_nay;
										 //echo "A=".$poid;
									 }
									 if($nay_planQty_fin==0 && $prod_FinQty>0) 
									 {
										 $nay_po_prodFinQty_arr[$newdate_nay][$poid_fin]=$nay_po_prodFinQty_arr[$nay_prev_prodFin_date[$poid_fin]][$poid_fin];
										 $nay_prev_prodFin_date[$poid_fin] = $newdate_nay;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $nay_planFinQtycal=0;
									 $nay_planFinQtycal=$nay_gFin_plan_qty_array[6][$newdate_nay][$poid_fin][$com_id]['fin_planQty'];//Plan
									 if($nay_gFin_plan_qty_array[6][$newdate_nay][$poid_fin][$com_id]['prod_fin']!="" || $nay_planFinQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $nay_po_prodFinQty_arr[$newdate_nay][$poid_fin]=$nay_prod_FinQty+$nay_po_prodFinQty_arr[$nay_prev_prodFin_date[$poid_fin]][$poid_fin];
										 $nay_prev_prodFin_date[$poid_fin] = $newdate_nay;
									 }
								 }
							 }
							 //=============Gmts Wash Recv=========================//
							 
							 foreach($nay_gWash_plan_qty_array[6][$newdate_nay] as $poid_w=>$poData)
							 {
								 $nay_planQty_w=$nay_prod_WQty=0;
								 $nay_planQty_w=$poData[$com_id]['wash_planQty'];//fin_planQty
								 $nay_prod_WQty=$poData[$com_id]['prod_wash'];
								 if($nay_prod_WQty=='') $nay_prod_WQty=0;
								 if($nay_planQty_w>0)
								 {
									 //  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									 $nay_po_planQtyWash_arr[$newdate_nay][$poid_w]=$nay_planQty_w+$nay_po_planQtyWash_arr[$prev_date_planW[$poid_w]][$poid_w];
									 $prev_date_planW[$poid_w] = $newdate_nay; 
								 }
								 if($nay_planQty_w>0 || $nay_prod_WQty>0)
								 {
									 if($nay_planQty_w>0 && $nay_prod_WQty==0)
									 {
									 //	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										 $nay_po_prodWashQty_arr[$newdate_nay][$poid_w]=$nay_po_prodWashQty_arr[$prev_prodW_date[$poid_w]][$poid_w];
										 $prev_prodW_date[$poid_w] = $newdate_nay;
										 //echo "A=".$poid;
									 }
									 if($nay_planQty_fin==0 && $nay_prod_WQty>0) 
									 {
										 $nay_po_prodWashQty_arr[$newdate_nay][$poid_w]=$nay_po_prodWashQty_arr[$prev_prodW_date[$poid_w]][$poid_w];
										 $prev_prodW_date[$poid_w] = $newdate_nay;
									 // if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									 }
									 $nay_planWashQtycal=0;
									 $nay_planWashQtycal=$nay_gWash_plan_qty_array[6][$newdate_nay][$poid_w][$com_id]['wash_planQty'];//Plan
									 if($nay_gWash_plan_qty_array[6][$newdate_nay][$poid_w][$com_id]['prod_wash']!="" || $nay_planWashQtycal>0)
									 {
										 //if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										 $nay_po_prodWashQty_arr[$newdate_nay][$poid_w]=$nay_prod_WQty+$nay_po_prodWashQty_arr[$prev_prodW_date[$poid_w]][$poid_w];
										 $prev_prodW_date[$poid_w] = $newdate_nay;
									 }
								 }
							 }

							 //=============######### Actual PO and Gmts  Delivery ****########=========================//
							 //nay_actual_plan_qty_array
							 foreach($nay_acl_delivery_qty_array[6][$newdate_nay] as $poid_del=>$poDataArr)
							 {
								foreach($poDataArr as $aclpoid=>$poData)
							    {
								 $nay_planQty_acl=$nay_prod_DelQty=0;	
								 //$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']
								 $nay_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
								//  echo $newdate_nay.'='.$nay_planQty_del.'<br>';
								 $nay_prod_DelQty=$poData[$com_id]['prod_del'];
								  $acul_poid=rtrim($poData[$com_id]['acul_poid'],',');
								  $acul_poidArr=array_unique(explode(",",$acul_poid));
								 // asort($acul_poidArr);
								 if($nay_planQty_acl>0)
								 {
									 $nay_actual_po_planQty_arr[$newdate_nay][$poid_del]=$nay_planQty_acl;
								 }
								 
									$nay_prod_DelQty=$nay_prod_DelQty;//$nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del'];
									 if($nay_prod_DelQty=='') $nay_prod_DelQty=0;
									 if($nay_planQty_acl>0 || $nay_prod_DelQty>0)
									 {
									 	if($nay_planQty_acl>0 && $nay_prod_DelQty==0)
										{
											
										 $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid]=$nay_po_prodDelQty_arr[$prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $prev_prodDel_date[$poid_del][$aclpoid] = $newdate_nay;
										}
										if($nay_planQty_acl==0 && $nay_prod_DelQty==0)
										{
										  $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid]=$nay_po_prodDelQty_arr[$prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $prev_prodDel_date[$poid_del][$aclpoid] = $newdate_nay;
										   // echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
										}
										
										if($nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $nay_planQty_acl>0)
										{
										  $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid]=$nay_prod_DelQty+$nay_po_prodDelQty_arr[$prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $prev_prodDel_date[$poid_del][$aclpoid] = $newdate_nay;
										}
									}
								 }
							}
						} 
						//===========Fin End
						//Days loop end
						//Days loop end
			} 
			//Month Loop end here //Month Loop
	}
	// print_r($po_prodQcQty_arr);

	 
	// print_r($qc_month_wise_kpiArr);
	  //====================JM Nayapara Location=========================
	//	print_r($jm_com_poIdArr);
		 foreach($jm_com_poIdArr as $comp_id=>$comData)
		{
		 foreach($comData as $poid=>$IR)
	     {
				 $loaction=0;
				$loaction=$jm_poId_loaction_Arr[$comp_id][$poid];

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
				 
							for($j=0;$j<$diff_days;$j++)
							{
								//$day_all =0;
								if($j==0)
								{
									$day_all = date('d-M-Y', strtotime($from_date));
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
								}
							if($loaction==6) //===========  Start===================
							{
								//==========Qc Prod and Qc Plan==============
								$planQcQtycal=0;
								 $planQcQtycal=$nay_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['qc_planQty'];//alloQty
								if($planQcQtycal>0 && $nay_po_prodQcQty_arr[$day_all][$poid]>0)
								{
									$mon_qc_qty=$nay_po_prodQcQty_arr[$day_all][$poid];
									$nay_qc_month_wise_kpiArr[$comp_id][$loaction][$day_all]['qc_prod']+=$mon_qc_qty;
								}
								$nay_qc_month_wise_kpiArr[$comp_id][$loaction][$day_all]['qc_plan']+=$nay_po_planQtyQc_arr[$day_all][$poid];
								//==========In Prod and In Plan==============
								$planInQtycal=0;
								$planInQtycal=$nay_in_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['input_planQty'];//alloQty
								 
								if($planInQtycal>0 && $nay_po_prodInQty_arr[$day_all][$poid]>0)
								{
									$mon_in_qty=$nay_po_prodInQty_arr[$day_all][$poid];
									$nay_in_month_wise_kpiArr[$comp_id][$loaction][$day_all]['in_prod']+=$mon_in_qty;
								}
								$nay_in_month_wise_kpiArr[$comp_id][$loaction][$day_all]['in_plan']+=$nay_po_planQtyIn_arr[$day_all][$poid];
								//==========Out Qty Prod and Out Plan Qty==============
								$planOutQtycal=0;
								$planOutQtycal=$nay_out_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['out_planQty'];//alloQty
								//po_prodOutQty_arr
								if($planOutQtycal>0 && $nay_po_prodOutQty_arr[$day_all][$poid]>0)
								{
									$mon_out_qty=$nay_po_prodOutQty_arr[$day_all][$poid];
									$nay_out_month_wise_kpiArr[$comp_id][$loaction][$day_all]['out_prod']+=$mon_out_qty;
								}
								$nay_out_month_wise_kpiArr[$comp_id][$loaction][$day_all]['out_plan']+=$nay_po_planQtyOut_arr[$day_all][$poid];
								//==========GMTS Fin Qty Prod and Fin Plan Qty==============
								$planFinQtycal=0;//po_prodFinQty_arr
								$planFinQtycal=$nay_gFin_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['fin_planQty'];
								//po_prodOutQty_arr
								if($planFinQtycal>0 && $nay_po_prodFinQty_arr[$day_all][$poid]>0)
								{
									$mon_fin_qty=$nay_po_prodFinQty_arr[$day_all][$poid];
									$nay_fin_month_wise_kpiArr[$comp_id][$loaction][$day_all]['fin_prod']+=$mon_fin_qty;
								}
								$nay_fin_month_wise_kpiArr[$comp_id][$loaction][$day_all]['fin_plan']+=$nay_po_planQtyFin_arr[$day_all][$poid];
								//==========GMTS Wash Qty Prod and Wash Plan Qty==============
								$planWashQtycal=0;//
								$planWashQtycal=$nay_gWash_plan_qty_array[$loaction][$day_all][$poid][$comp_id]['wash_planQty'];
								if($planWashQtycal>0 && $nay_po_prodWashQty_arr[$day_all][$poid]>0)
								{
									$mon_wash_qty=$nay_po_prodWashQty_arr[$day_all][$poid];
									$nay_wash_month_wise_kpiArr[$comp_id][$loaction][$day_all]['wash_prod']+=$mon_wash_qty;
								}
								$nay_wash_month_wise_kpiArr[$comp_id][$loaction][$day_all]['wash_plan']+=$nay_po_planQtyWash_arr[$day_all][$poid];
								
								 
							} 
							 //***********=========== Nayapara End===================********//
						 }
				}
		  }
	   }
	   foreach($acl_jm_com_poIdArr as $comp_id=>$comData)
		{
		 foreach($comData as $poid=>$IR_Data)
	     {
			foreach($IR_Data as $acl_poid=>$IR)
	       {
				 $loaction=0;
				$loaction=$jm_poId_loaction_Arr[$comp_id][$poid];
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
				 
							for($j=0;$j<$diff_days;$j++)
							{
								//$day_all =0;
								if($j==0)
								{
									$day_all = date('d-M-Y', strtotime($from_date));
								}
								else
								{
									$day_all = date('d-M-Y', strtotime("+1 day", strtotime($day_all)));
								}
							if($loaction==6) //===========  Start===================
							{
								 
								//==========GMTS Delivery Qty Prod and Actual Plan Qty==============
								 
								$planAclQtycal=0;//
								$planAclQtycal=$nay_acl_delivery_qty_array[$loaction][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
								$mon_delivery_qty=$nay_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
								//echo $day_all.'='.$mon_delivery_qty.'<br>';
								if($planAclQtycal>0 && $mon_delivery_qty>0)
								{
									$mon_del_qty=$mon_delivery_qty;
									$nay_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['del_prod']+=$mon_delivery_qty;
								}
								$nay_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['acl_plan']+=$planAclQtycal;
							} 
							 //***********=========== Nayapara End===================********//
						 }
				}
			}
		  }
	   }
	   
	  
	    //  echo "<pre>";
	     // print_r($nay_delivery_month_wise_kpiArr);
		   // echo "<pre>";
	   //===================****Nayapara Qc*******==========
	    //===================****Nayapara Qc*******==========
	   $nay_month_wise_kpiArr=array();
	   foreach($nay_qc_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $qc_month_prod=$row['qc_prod']; 
			   $qc_month_plan=$row['qc_plan'];
			 
			   $qc_month_kpi_per=$qc_month_prod/$qc_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($qc_month_prod>0 && $qc_month_plan>0)
					{
						if($qc_month_kpi_per>100)  $qc_month_kpi_per=100;
					//$nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$qc_month_kpi_per;
					if($day_key=='30-May-2023')
					{
						//echo $qc_month_kpi_per.'<br>';
					}
					$nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc']+=$qc_month_kpi_per;
					}
					   

					if($qc_month_plan>0)
					{
						$nay_mon_grand_qc_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						$nay_num_of_plan_days[$comp_id][$loc_id][$day_key]++;
						$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	   foreach($nay_in_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
		  foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	  	  {
			foreach($LocData as $day_key=>$row)
			{
			   $in_month_prod=$row['in_prod']; 
				$in_month_plan=$row['in_plan'];
				$in_month_kpi_per=$in_month_prod/$in_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($in_month_prod>0 && $in_month_plan>0)
					 {
					  if($in_month_kpi_per>100)  $in_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
						 if($day_key=='04-Jun-2023')
						{
							// echo $in_month_kpi_per.'<br>';
						}
					  $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in']+=$in_month_kpi_per;
					 }
						$qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 
					 if($in_month_plan>0)
					 {
						 $nay_in_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $nay_mon_grand_in_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						
					 }
					 if($in_month_plan>0 && !$qc_plan_day_chk)
					 {
						$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				 }
			  }
			}
		}
		   // print_r($mon_grand_qc_event_kpi_perArr);
		  // echo "<pre>";
		//=========For Out Nayapara **Out** Location================//out_month_wise_kpiArr
		$nay_mon_grand_out_event_kpi_perArr=array();
		foreach($nay_out_month_wise_kpiArr as $comp_id=>$comData) //Month Wise Out KPI Percentage summary part
		{
		  foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	  	   {
			foreach($LocData as $day_key=>$row)
			{
			   $out_month_prod=$row['out_prod']; 
				$out_month_plan=$row['out_plan'];
				$out_month_kpi_per=$out_month_prod/$out_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 //echo $today.'='.$day_chk.'<br>';
				 if($day_chk<=$today)// as on today
				 {
					 //echo $day_key.'='.$today_date.'<br>';
					 if($out_month_prod>0 && $out_month_plan>0)
					 {
						 if($out_month_kpi_per>100)  $out_month_kpi_per=100;
						 // echo  $monthYr.'='.$month_allo.'='. $month_plan.'*100<br>';
					 //$nay_out_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$out_month_kpi_per;
					 if( $day_key=='30-May-2023')
					 {
						// echo $day_key.'='.$out_month_kpi_per.'<br>';
					 }
					 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out']+=$out_month_kpi_per;
					 }
					 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
					 
					 
					 if($out_month_plan>0)
					 {
						 $nay_out_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						 $nay_mon_grand_out_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						// echo $day_key.'='.$out_month_plan.'<br>';
					 }
					 if($out_month_plan>0 && (!$in_plan_day_chk && !$qc_plan_day_chk)) 
					 {
						$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				 }
			  }
			}
		}
		   //  print_r($nay_month_wise_kpiArr);
		  // echo "<pre>";
//=========For Nayapara **Gmts Fin** Location================//
foreach($nay_fin_month_wise_kpiArr as $comp_id=>$comData)  
{
 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
 {
	foreach($LocData as $day_key=>$row)
	{
	   $fin_month_prod=$row['fin_prod']; 
		$fin_month_plan=$row['fin_plan'];
	  
		$fin_month_kpi_per=$fin_month_prod/$fin_month_plan*100; 
		$monthYr=date("M-Y",strtotime($day_key));
		 $yr_month=strtoupper($monthYr);
		 $today=strtotime($today_date);
		 $day_chk=strtotime($day_key);
		 //echo $today.'='.$day_chk.'<br>';
		 if($day_chk<=$today)// as on today
		 {
			 //echo $day_key.'='.$today_date.'<br>';
			 if($fin_month_prod>0 && $fin_month_plan>0)
			 {
				 if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
				 //$nay_fin_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$fin_month_kpi_per;
				// echo $day_key.'='.$fin_month_kpi_per.'<br>';
				if($day_key=='30-May-2023')
					{
						// echo $fin_month_kpi_per.'<br>';
					}
				 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin']+=$fin_month_kpi_per;
			 }
				 $out_plan_day_chk= $nay_out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
			 	 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
				 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
				  
			 
			 if($fin_month_plan>0)
			 {
				 $nay_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
				 $nay_mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				
			 }
			if($fin_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk))
			{
				$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
			}
		 }
	  }
	}
}
//nay_wash_month_wise_kpiArr
//=========For Nayapara **Gmts Wash** Location================//
foreach($nay_wash_month_wise_kpiArr as $comp_id=>$comData)  //nay_delivery_month_wise_kpiArr
{
 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
 {
	foreach($LocData as $day_key=>$row)
	{
	   $wash_month_prod=$row['wash_prod']; 
		$wash_month_plan=$row['wash_plan'];
		$wash_month_kpi_per=$wash_month_prod/$wash_month_plan*100; 
		$monthYr=date("M-Y",strtotime($day_key));
		 $yr_month=strtoupper($monthYr);
		 $today=strtotime($today_date);
		 $day_chk=strtotime($day_key);
		 //echo $today.'='.$day_chk.'<br>';
		 if($day_chk<=$today)// as on today
		 {
			 //echo $day_key.'='.$today_date.'<br>';
			 if($wash_month_prod>0 && $wash_month_plan>0)
			 {
				 if($wash_month_kpi_per>100)  $wash_month_kpi_per=100;
				 //  echo $day_key.'='.$wash_month_prod.'/'.$wash_month_plan.'<br>';
			// $nay_wash_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$wash_month_kpi_per;
			 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash']+=$wash_month_kpi_per;
			 }
			 $out_plan_day_chk= $nay_out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
			 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
			 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
			 $fin_plan_day_chk= $nay_fin_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin_plan'];
			  
			 
			 if($wash_month_plan>0)
			 {
				 $nay_wash_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
				 $nay_mon_grand_wash_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 
			 }
			if($wash_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk))
			{
				$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
			}
		 }
	  }
	}
}
//=========For Nayapara **Gmts Delivery** Location================//
foreach($nay_delivery_month_wise_kpiArr as $comp_id=>$comData)  //nay_delivery_month_wise_kpiArr
{
 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
 {
	foreach($LocData as $day_key=>$row)
	{
	   $del_month_prod=$row['del_prod']; 
		$del_month_plan=$row['acl_plan'];
		$del_month_kpi_per=$del_month_prod/$del_month_plan*100; 
		$monthYr=date("M-Y",strtotime($day_key));
		 $yr_month=strtoupper($monthYr);
		 $today=strtotime($today_date);
		 $day_chk=strtotime($day_key);
		//  echo  $day_key.'='.$del_month_plan.'='.$del_month_prod.'<br>';
		 if($day_chk<=$today)// as on today
		 {
			 //echo $day_key.'='.$today_date.'<br>';
			 if($del_month_prod>0 && $del_month_plan>0)
			 {
				 if($del_month_kpi_per>100)  $del_month_kpi_per=100;
				   // echo $day_key.'='.$del_month_prod.'/'.$del_month_plan.'<br>';
			// $nay_wash_month_wise_kpiArr[$comp_id][$loc_id][$yr_month]+=$wash_month_kpi_per;
			 $nay_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['deli']+=$del_month_kpi_per;
			 }
			 $out_plan_day_chk= $nay_out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
			 $qc_plan_day_chk= $nay_qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
			 $in_plan_day_chk= $nay_in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
			 $fin_plan_day_chk= $nay_fin_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin_plan'];
			 $wash_plan_day_chk= $nay_wash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash_plan'];
			 
			 if($del_month_plan>0)
			 {
				 $nay_del_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
				 $nay_mon_grand_del_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				
			 }
			if($del_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk && !$wash_plan_day_chk))
			{
				$nay_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
			}
		 }
	  }
	}
}
asort($nay_month_wise_kpiArr);
foreach($nay_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
	 foreach($comData as $unit_id=>$monData)  
	 {
		 foreach($monData as $day_key=>$pers)  
		 {
			 $event_qc_count=0;$event_in_count=0;$event_out_count=0;$event_fin_count=0;$event_wash_count=0;$event_del_count=0;
			$event_qc_count=$nay_mon_grand_qc_event_kpi_perArr[$comp_id][$unit_id][$day_key];//$mon_grand_qc_event_kpi_perArr
			$event_in_count=$nay_mon_grand_in_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_out_count=$nay_mon_grand_out_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_fin_count=$nay_mon_grand_fin_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_wash_count=$nay_mon_grand_wash_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$event_del_count=$nay_mon_grand_del_event_kpi_perArr[$comp_id][$unit_id][$day_key];
			$nay_event_mon_count= $event_qc_count+$event_in_count+$event_out_count+$event_fin_count+$event_wash_count+$event_del_count;
			$nay_all_kpi_per=$pers['qc']+$pers['in']+$pers['out']+$pers['fin']+$pers['wash']+$pers['deli'];
			if($day_key=='04-Jun-2023')
			{
			  // echo $day_key.'='.$pers['qc'].'+'.$pers['in'].'+'.$pers['out'].'+'.$pers['fin'].'+'.$pers['wash'].'+'.$pers['deli'].'/'.$nay_event_mon_count.'<br>';
			}
			 // echo $day_key.'='.$event_del_count.'='.$per['deli'].'/'.$nay_event_mon_count.'<br>';
			$nay_gbl_comp_avg_perArr[$comp_id][$unit_id][$day_key]+=$nay_all_kpi_per/$nay_event_mon_count;
			$all_avg_perArr[$day_key]+=$nay_all_kpi_per/$nay_event_mon_count;
		 }
	 }
}
// echo "<pre>";
 //print_r($all_avg_perArr);
//echo "<pre>";
foreach($nay_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
   foreach($comData as $unitid=>$monData)  
   {
	   foreach($monData as $day_key=>$val)  
	   {
		  $monthYr=date("M-Y",strtotime($day_key));
		  $yr_month=strtoupper($monthYr);
	   	// echo $day_key.'='.$val.'<br>';
		  $nay_gbl_comp_mon_avg_perArr[$comp_id][$unitid][$yr_month]+=$val;
	   }
   }
}
	  //  echo "<pre>";
	  // print_r($ash_gbl_comp_mon_avg_perArr);
	//print_r($mon_wise_allocateQty_tillCalculateArr);
	  //  echo "<pre>"; 	
	  	 
  
	$tbl_width = 240+(count($fiscalMonth_arr)*80);
	ob_start();	
	
	$tbl_width2 = 5000;
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total PO wise dtls Start============================= style="display:none" =====================================-->

       
        <?

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
	    <h3 style="width:<? echo $tbl_width_unit;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'kpi_unit_report', '')"> -<b>KPI Unit  <?='Nayapara Cut Sewing';?>[<? echo $from_year; ?>]</b></h3>
	    <div id="kpi_unit_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width_unit;?>" cellpadding="0" cellspacing="0">
		 
	        <thead>
	            
	            <th width="80">Date</th>
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
						$today=strtotime($today_date);
						$day_chk=strtotime($date);
						//echo $today.'='.$day_chk.'<br>';
						$month_kpi_per=0; 
						if($day_chk<=$today)// as on today
						{
							$ash_gbl_comp_avg_kip=$nay_gbl_comp_avg_perArr[$cbo_company_id][$unitid][$date];
							$month_kpi_per=$ash_gbl_comp_avg_kip;
							
						}
						

						?>
						<td width="80" align="center" title="<??>"><?  echo fn_number_format($month_kpi_per,2).'%';?></td>
						<?
							if($month_kpi_per>0)
							{
							$date_wise_KPIArr[$unitid][$year_mon]+=$month_kpi_per;
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
					if($num_of_plan_daysArr[$cbo_company_id][$unitid][$year_mon]>0)
					{
					//	$tot_mon_days=$num_of_plan_daysArr[$cbo_company_id][$unit_id][$year_mon];
						//echo $tot_days.', ';
					}
					$tot_mon_days=$nay_gbl_num_of_plan_daysArr[$cbo_company_id][$unitid][$year_mon];
					//echo $tot_mon_days.'f';
					$tot_kpi_per=0;$tot_avg_kpi_per=0;
					$tot_kpi_per=$date_wise_KPIArr[$unitid][$year_mon];
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
    
 