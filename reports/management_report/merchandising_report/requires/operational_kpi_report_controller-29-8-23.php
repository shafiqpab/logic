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

	// ========================= for Plan ======================
	     $sql_po_plan="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, c.SOURCE_ID,c.UOM_ID,b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(48,84,122,86,88,52,60,61,63,267,268,73,90,70,71)   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337)   and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_po_plan_result = sql_select($sql_po_plan);
	foreach ($sql_po_plan_result as $val) 
	{
		if($val['TASK_ID']==48) // for Yarn
		{
			$plan_date_mon=date('m-Y', strtotime($val['PLAN_DATE']));
			$plandate=strtotime($val['PLAN_DATE']);
			 $plan_poIdArr[$val['POID']]=$val['POID'];
		 	$com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];

			$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
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

		if($val['COMPANY_ID']==2 && $val['TASK_ID']==52) //Yarn Recv
		{
				//$plandate=strtotime($val['PLAN_DATE']);
				$plan_yarn_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==60) //Knitting/Grey Recv
		{ 
				$plan_knit_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==61) //Dyeing Prod
		{
			 
				$plan_dyeing_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==63) //AOP Recv 
		{
				 
				$plan_aop_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==267) //Print Recv 
		{
				 
				$plan_print_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==268) //Embro Recv 
		{
				 
				$plan_embro_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //MFG Fin Recv 
		{
				 
				$plan_fin_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==90) //Wash Fin Recv 
		{
				 
				$plan_wash_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		// ===========Jm Nayapara Fabric====================
		if($val['COMPANY_ID']==1  && $val['LOCATION_NAME']==6  && $val['TASK_ID']==52) //Yarn Recv
		{
				//$plandate=strtotime($val['PLAN_DATE']);
				$plan_yarn_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==1  && $val['LOCATION_NAME']==6 && $val['TASK_ID']==60) //Knitting/Grey Recv
		{ 
				$plan_knit_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6 && $val['TASK_ID']==61) //Dyeing Prod
		{
			 
				$plan_dyeing_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==1  && $val['LOCATION_NAME']==6 && $val['TASK_ID']==63) //AOP Recv 
		{
				 
				$plan_aop_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		if($val['COMPANY_ID']==1  && $val['LOCATION_NAME']==6 && $val['TASK_ID']==267) //Print Recv 
		{
				 
				$plan_print_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		if($val['COMPANY_ID']==1  && $val['LOCATION_NAME']==6 && $val['TASK_ID']==268) //Embro Recv 
		{
				 
				$plan_embro_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		if($val['COMPANY_ID']==1  && $val['LOCATION_NAME']==6 && $val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //MFG Fin Recv 
		{
				 
				$plan_fin_shafipur_poIdArr[$val['POID']]=$val['POID'];
				$shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		

		////============***********Marchandising ********===============
		if($val['COMPANY_ID']==1 && $val['TASK_ID']==73 && $val['SOURCE_ID']==2 ) //MFG Fin Recv purchase JM
		{
				 
				$plan_fin_recv_mar_poIdArr[$val['POID']]=$val['POID'];
				$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 

		if($val['COMPANY_ID']==2 && $val['TASK_ID']==73 && $val['SOURCE_ID']==2 ) //MFG Fin Recv purchase Kal
		{
				 
				$plan_fin_recv_mar_poIdArr[$val['POID']]=$val['POID'];
				$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		////============***********Trims Sewing ********===============
		if($val['COMPANY_ID']==1 && $val['TASK_ID']==70 ) //  Sewing JM
		{
				$plan_trim_sew_mar_poIdArr[$val['POID']]=$val['POID'];
				$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 

		if($val['COMPANY_ID']==2 && $val['TASK_ID']==70 ) //  Sewing Kal
		{
				$plan_trim_sew_mar_poIdArr[$val['POID']]=$val['POID'];
				$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		////============***********Trims Fin ********===============
		if($val['COMPANY_ID']==1 && $val['TASK_ID']==71 ) //  Fin JM
		{
				$plan_trim_fin_mar_poIdArr[$val['POID']]=$val['POID'];
				$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 

		if($val['COMPANY_ID']==2 && $val['TASK_ID']==71 ) //  Fin Kal
		{
				$plan_trim_fin_mar_poIdArr[$val['POID']]=$val['POID'];
				$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		} 
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
	}
	// print_r($plan_knit_shafipur_poIdArr);
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
		$mar_plan_qty_array[5][$plandate][$val['POID']][$val['COMPANY_ID']]['planQty']+=$val['PLAN_QTY'];
	}
	unset($sql_po_plan_result_current);
	//$allo_date_cond=" and c.insert_date between '".$startDate."' and '".$endDate." 11:59:59 PM'";
	//$allo_as_on_date="and  TRUNC(c.insert_date)<=TO_DATE('".$endDate."')";
	//to_char(c.insert_date,'DD/Mon/YY') as ALLOC_DATE

     $sql_po_allocate="SELECT a.company_name as COMPANY_ID,b.id as POID,b.GROUPING, c.QNTY,c.allocation_date as ALLOC_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, inv_material_allocat_hystory c where a.id=b.job_id and b.id=c.po_break_down_id and b.id in(20328,20329,20330,20334,20335,20337)  and  b.status_active=1 and b.is_deleted=0   and c.allocation_date between '$startDate' and '$endDate'   $company_conds order by c.allocation_date asc"; 
	$sql_po_allocate_result = sql_select($sql_po_allocate); //and b.id in(20325,20326,20327,20328,20329,20330)
	foreach ($sql_po_allocate_result as $val) 
	{
		$allocat_poIdArr[$val['POID']]=$val['POID'];
		$mar_allocat_poIdArr[$val['POID']]=$val['POID'];
		$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		$com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 
	} 
	 fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 2, $allocat_poIdArr, $empty_arr);//PO ID Ref from=2

	  fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 44, $mar_allocat_poIdArr, $empty_arr);//PO ID Ref from=44

    $curr_sql_po_allocate="SELECT a.company_name as COMPANY_ID,b.id as POID, c.QNTY,c.allocation_date as ALLOC_DATE, b.job_no_mst as JOB_NO,g.REF_FROM from wo_po_details_master a,wo_po_break_down b, inv_material_allocat_hystory c,gbl_temp_engine g  where a.id=b.job_id and b.id=c.po_break_down_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(2,44) and g.entry_form=89  and c.allocation_date <= '$endDate'  and  b.status_active=1 and b.is_deleted=0    $company_conds order by c.allocation_date,c.id asc";
	
  $sql_po_allocate_result_curr = sql_select($curr_sql_po_allocate);  
	foreach ($sql_po_allocate_result_curr as $val) 
	{
		$allocatedate=date('d-M-Y',strtotime($val['ALLOC_DATE']));
		if($val['REF_FROM']==2)
		{
			$plan_qty_array[1][$allocatedate][$val['POID']][$val['COMPANY_ID']]['alloQty']+=$val['QNTY'];
		}
		if($val['REF_FROM']==44)
		{
			$mar_plan_qty_array[5][$allocatedate][$val['POID']][$val['COMPANY_ID']]['alloQty']+=$val['QNTY'];
		}
		
	}
	unset($sql_po_allocate_result_curr);
	//print_r($mar_plan_qty_array);
	
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

	  //======Start Actual PO for Ashulia nad Ratabpur======================

	    $sql_actual_po_plan_kal="SELECT c.id as acul_poid,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name in(3,5)    and c.acc_po_qty>0 and  b.status_active=1 and b.is_deleted=0  and b.id in(20328,20329,20330) and c.acc_ship_date between '$startDate' and '$endDate' $company_conds order by acul_poid, PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_plan_result_kal = sql_select($sql_actual_po_plan_kal);
	foreach($sql_actual_po_plan_result_kal  as $val)
	{
		$kal_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		//$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 17, $kal_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=17
	$sql_actual_qty_po_plan_kal="SELECT c.id as ACUL_POID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.ACC_PO_NO, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c, gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name in(3,5)  and c.acc_po_qty>0 and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(17) and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0  and b.id in(20328,20329,20330)  and c.ACC_SHIP_DATE <= '$endDate' $company_conds  order by  PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_qty_plan_result_kal = sql_select($sql_actual_qty_po_plan_kal);
	foreach($sql_actual_po_qty_plan_result_kal  as $val)
	{
		$plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		$loc_id=$val['LOCATION_NAME']; 
		if($loc_id==3)
		{
			$ash_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
			$ash_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];

			$ash_acl_delivery_qty_array[$loc_id][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
			$rat_acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
			$rat_acl_kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
			$loc_acl_po_noArr[$val['POID']]=$loc_id;
		}
		else{
			$rat_actual_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
			$rat_actual_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
			
			$rat_acl_delivery_qty_array[4][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
			$rat_acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
			$rat_acl_kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
			$loc_acl_po_noArr[$val['POID']]=$loc_id;
		}
		 
	}
  $sql_actual_po_delivery_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.ex_fact_qty, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 pro_ex_factory_mst c,pro_ex_factory_actual_po_details d  where a.id=b.job_id and b.id=c.po_break_down_id
  and c.id=d.mst_id  and a.location_name in(3,5) and d.ex_fact_qty>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
   and b.id in(20328,20329,20330) and c.ex_factory_date between '$startDate' and '$endDate' $company_conds order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_result_kal = sql_select($sql_actual_po_delivery_kal);
	foreach($sql_actual_po_delivery_result_kal  as $val)
	{
		$kal_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		$kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 18, $kal_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=18

	  $sql_actual_po_qty_delivery_jm="SELECT d.actual_po_id as ACTUAL_PO_ID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, d.ex_fact_qty as DEL_QTY,c.ex_factory_date as EX_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, pro_ex_factory_mst c,pro_ex_factory_actual_po_details d,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id  and c.id=d.mst_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(18) and g.entry_form=89  and a.location_name in(3,5) and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and d.ex_fact_qty>0 and  b.status_active=1 and b.is_deleted=0   and b.id in(20328,20329,20330) and c.ex_factory_date<= '$endDate'  $company_conds order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_qty_result_jm = sql_select($sql_actual_po_qty_delivery_jm);
	foreach ($sql_actual_po_delivery_qty_result_jm as $val) 
	{
		$ex_date=date('d-M-Y',strtotime($val['EX_DATE']));
	  	$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		 $loc_id=$val['LOCATION_NAME'];
		 if($loc_id==3) //
		 {
			$ash_acl_delivery_qty_array[$loc_id][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$ash_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$ash_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
		 }
		 else{
			$rat_acl_delivery_qty_array[4][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$rat_actual_plan_qty_array[4][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$rat_actual_plan_qty_array[4][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
		 }
	
		
	}
	//print_r($ash_acl_delivery_qty_array);

	//======Actual PO for Ashulia N Ratabpur======================

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
		 
	   $sql_po_plan_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(84,122,86,88,90) and a.location_name=6  and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0   and b.id in(20334,20335,20337) and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds  order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
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
	$sql_actual_po_plan_jm="SELECT c.id as acul_poid,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c where a.id=b.job_id and b.id=c.po_break_down_id    and c.acc_po_qty>0 and  b.status_active=1 and b.is_deleted=0   and b.id in(20328,20329,20330,20334,20335,20337) and c.acc_ship_date between '$startDate' and '$endDate' $company_conds  order by acul_poid, PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_plan_result_jm = sql_select($sql_actual_po_plan_jm);
	foreach($sql_actual_po_plan_result_jm  as $val)
	{
	if($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6)
	{
	$jm_main_plan_wash_poIdArr[$val['POID']]=$val['POID'];
	$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}
	
	$plan_acl_mar_poIdArr[$val['POID']]=$val['POID'];
	$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}
	
	//============================Jm Plan============================
	//==========================******************Nayapara*************************************==================
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 9, $jm_plan_cut_Qc_poIdArr, $empty_arr);//PO ID Ref from=9
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 10, $jm_plan_input_poIdArr, $empty_arr);//PO ID Ref from=10
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 11, $jm_plan_output_poIdArr, $empty_arr);//PO ID Ref from=11
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 12, $jm_plan_fin_poIdArr, $empty_arr);//PO ID Ref from=12
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 13, $jm_plan_wash_poIdArr, $empty_arr);//PO ID Ref from=13
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 15, $jm_main_plan_wash_poIdArr, $empty_arr);//PO ID Ref from=15
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 40, $plan_acl_mar_poIdArr, $empty_arr);//PO ID Ref from=40// For Marchandising

	// for actual po ship qty
	 $sql_actual_qty_po_plan_jm="SELECT c.id as ACUL_POID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.ACC_PO_NO, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO,g.REF_FROM from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c, gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id and c.acc_po_qty>0 and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(15,40) and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0  and b.id in(20328,20329,20330,20334,20335,20337)  and c.ACC_SHIP_DATE <= '$endDate' $company_conds order by  PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_qty_plan_result_jm = sql_select($sql_actual_qty_po_plan_jm);
	foreach($sql_actual_po_qty_plan_result_jm  as $val)
	{
		$plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		if($val['LOCATION_NAME']==6  && $val['COMPANY_ID']==1  && $val['REF_FROM']==15)
		{
		$loc_id=$val['LOCATION_NAME']; 
		//$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['qc_planQty']+=$val['PLAN_QTY'];
		$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
		$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
		$nay_acl_delivery_qty_array[$loc_id][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
		$acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
		$acl_jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
		}
		//===============Marchandising=========================
		if($val['REF_FROM']==40)
		{
		$mar_acl_delivery_qty_array[5][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
		$mar_acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
		$mar_acl_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
		}

	}
	//echo "<pre>";
 //print_r($acl_jm_com_poIdArr);
 $sql_actual_po_delivery_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 pro_ex_factory_mst c,pro_ex_factory_actual_po_details d  where a.id=b.job_id and b.id=c.po_break_down_id
  and c.id=d.mst_id   and d.ex_fact_qty>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
  and b.id in(20328,20329,20330,20334,20335,20337) and c.ex_factory_date between '$startDate' and '$endDate' $company_conds  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330) // and a.location_name=6  and a.company_name=1
	$sql_actual_po_delivery_result_jm = sql_select($sql_actual_po_delivery_jm);
	foreach($sql_actual_po_delivery_result_jm  as $val)
	{
		if($val['LOCATION_NAME']==6 && $val['COMPANY_ID']==1)
		{
		$jm_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		}
		//===============Marchandising=========================
		$mar_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		$mar_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 16, $jm_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=16
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 41, $mar_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=16

	 $sql_actual_po_qty_delivery_jm="SELECT d.actual_po_id as ACTUAL_PO_ID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, d.ex_fact_qty as DEL_QTY,c.ex_factory_date as EX_DATE, b.job_no_mst as JOB_NO,g.REF_FROM from wo_po_details_master a,wo_po_break_down b, pro_ex_factory_mst c,pro_ex_factory_actual_po_details d,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id  and c.id=d.mst_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(16,41) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and d.ex_fact_qty>0 and  b.status_active=1 and b.is_deleted=0   and b.id in(20328,20329,20330,20334,20335,20337) and c.ex_factory_date<= '$endDate' $company_conds  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330) //and a.location_name=6 
	$sql_actual_po_delivery_qty_result_jm = sql_select($sql_actual_po_qty_delivery_jm);
	foreach ($sql_actual_po_delivery_qty_result_jm as $val) 
	{
		$ex_date=date('d-M-Y',strtotime($val['EX_DATE']));
	  	$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID']; 
		if($val['LOCATION_NAME']==6 && $val['COMPANY_ID']==1 && $val['REF_FROM']==16)
		{
		 $loc_id=$val['LOCATION_NAME'];
		// $nay_actual_poIdplan_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['acul_poid'].',';
		 $nay_acl_delivery_qty_array[$loc_id][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		 $nay_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		 $nay_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
		}
		//===============Marchandising=========================
		if($val['REF_FROM']==41)
		{
		 $mar_acl_delivery_qty_array[5][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
		}
	}
	// print_r($mar_acl_delivery_qty_array);

//=======Delivery===============
	
   $sql_po_plan_jm="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
	(case when  c.task_id=84  and g.ref_from=9  then c.PLAN_QTY else 0 end) as QC_PLAN_QTY,
	(case when  c.task_id=122  and g.ref_from=10  then c.PLAN_QTY else 0 end) as INPUT_PLAN_QTY,
	(case when  c.task_id=86  and g.ref_from=11  then c.PLAN_QTY else 0 end) as OUT_PLAN_QTY,
	(case when  c.task_id=88  and g.ref_from=12  then c.PLAN_QTY else 0 end) as FIN_PLAN_QTY,
	(case when  c.task_id=90  and g.ref_from=13  then c.PLAN_QTY else 0 end) as WASH_PLAN_QTY 
	from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id and a.company_name=1 and a.location_name in(6)   and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(9,10,11,12,13) and  c.task_id in(84,122,86,88,90) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  and c.PLAN_DATE <= '$endDate' $company_conds order by c.PLAN_DATE asc";
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
	  from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(1,3,4,5,8) and a.location_name in(6)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20334,20335,20337)   and c.production_date between '$startDate' and '$endDate' and c.production_type in(1,4,5,8,3)  $company_conds order by c.PRODUCTION_DATE asc"; //and a.location_name=3
	    
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
	   and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0 and c.production_type in(1,4,5,8,3) and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' $company_conds order by c.PRODUCTION_DATE asc";// and a.location_name=3
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

//===============Yarn Recv Start for Shafipur==============

fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 20, $plan_yarn_shafipur_poIdArr, $empty_arr);//PO ID Ref from=20
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 21, $plan_knit_shafipur_poIdArr, $empty_arr);//PO ID Ref from=21
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 22, $plan_dyeing_shafipur_poIdArr, $empty_arr);//PO ID Ref from=22
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 23, $plan_aop_shafipur_poIdArr, $empty_arr);//PO ID Ref from=23
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 24, $plan_print_shafipur_poIdArr, $empty_arr);//PO ID Ref from=24
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 25, $plan_embro_shafipur_poIdArr, $empty_arr);//PO ID Ref from=25
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 26, $plan_fin_shafipur_poIdArr, $empty_arr);//PO ID Ref from=26
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 27, $plan_wash_shafipur_poIdArr, $empty_arr);//PO ID Ref from=27
//============Marchandising========================
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 34, $plan_fin_recv_mar_poIdArr, $empty_arr);//PO ID Ref from=34
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 35, $plan_trim_sew_mar_poIdArr, $empty_arr);//PO ID Ref from=35
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 36, $plan_trim_fin_mar_poIdArr, $empty_arr);//PO ID Ref from=26


$sql_po_plan_shafipur="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,c.SOURCE_ID,c.UOM_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
  
 (case when  c.task_id=52  and g.ref_from=20  then c.PLAN_QTY else 0 end) as SHAFI_YARN_PLAN_QTY,
 (case when  c.task_id=60  and g.ref_from=21  then c.PLAN_QTY else 0 end) as SHAFI_KNIT_PLAN_QTY,
 (case when  c.task_id=61 and g.ref_from=22  then c.PLAN_QTY else 0 end) as SHAFI_DYING_PLAN_QTY,
 (case when  c.task_id=63  and g.ref_from=23  then c.PLAN_QTY else 0 end) as SHAFI_AOP_PLAN_QTY,
 (case when  c.task_id=267  and g.ref_from=24  then c.PLAN_QTY else 0 end) as SHAFI_PRINT_PLAN_QTY,
 (case when  c.task_id=268  and g.ref_from=25  then c.PLAN_QTY else 0 end) as SHAFI_EMBR_PLAN_QTY,
 (case when  c.task_id=73 and c.source_id=1 and g.ref_from=26  then c.PLAN_QTY else 0 end) as SHAFI_FIN_PLAN_QTY,
 (case when  c.task_id=90  and g.ref_from=27  then c.PLAN_QTY else 0 end) as SHAFI_WASH_PLAN_QTY,

 (case when  c.task_id=73 and c.source_id=2 and g.ref_from=34  then c.PLAN_QTY else 0 end) as MAR_FIN_PLAN_QTY,
 (case when  c.task_id=70   and g.ref_from=35  then c.PLAN_QTY else 0 end) as MAR_TRIM_SEW_PLAN_QTY,
 (case when  c.task_id=71  and g.ref_from=36  then c.PLAN_QTY else 0 end) as MAR_TRIM_FIN_PLAN_QTY

from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id    and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(20,21,22,23,24,25,26,27,34,35,36) and c.task_id in(52,60,61,63,267,268,73,90,70,71) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $company_conds and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc";
 //and b.id in(20325,20326,20327,20328,20329,20330)
  $sql_po_plan_result_shafipur = sql_select($sql_po_plan_shafipur);
  foreach ($sql_po_plan_result_shafipur as $val) 
  {
	  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
	 
	  if($val['COMPANY_ID']==2) //Kal 
	  {

		if($val['TASK_ID']==52) //Yarn
		{
			$shafipur_plan_yarn_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_YARN_PLAN_QTY'];
		}
		if($val['TASK_ID']==60) //Knit
		{
			$shafipur_plan_knit_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_KNIT_PLAN_QTY'];
		}
		if($val['TASK_ID']==61) //Dyeing
		{
			$shafipur_plan_dyeing_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_DYING_PLAN_QTY'];
		}
		if($val['TASK_ID']==63) //AOP
		{
			$shafipur_plan_aop_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_AOP_PLAN_QTY'];
		}
		if($val['TASK_ID']==267) //Print
		{
			$shafipur_plan_print_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_PRINT_PLAN_QTY'];
		}
		if($val['TASK_ID']==268) //Embro
		{
			$shafipur_plan_embr_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_EMBR_PLAN_QTY'];
		}
		if($val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //Fin Gmt/Production,KG
		{
			$shafipur_plan_fin_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_FIN_PLAN_QTY'];
		}
		if($val['TASK_ID']==90) //Wash
		{
			$shafipur_plan_wash_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_WASH_PLAN_QTY'];
		}

	  }
	  else if ($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6)  //=========Jm Nayapara Fabric
	  {
		if($val['TASK_ID']==52) //Yarn
		{
			$shafipur_plan_yarn_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_YARN_PLAN_QTY'];
		}
		if($val['TASK_ID']==60) //Knit
		{
			$shafipur_plan_knit_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_KNIT_PLAN_QTY'];
		}
		if($val['TASK_ID']==61) //Dyeing
		{
			$shafipur_plan_dyeing_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_DYING_PLAN_QTY'];
		}
		if($val['TASK_ID']==63) //AOP
		{
			$shafipur_plan_aop_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_AOP_PLAN_QTY'];
		}
		if($val['TASK_ID']==267) //Print
		{
			$shafipur_plan_print_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_PRINT_PLAN_QTY'];
		}
		if($val['TASK_ID']==268) //Embro
		{
			$shafipur_plan_embr_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_EMBR_PLAN_QTY'];
		}
		if($val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //Fin Gmt/Production,KG
		{
			$shafipur_plan_fin_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_FIN_PLAN_QTY'];
		}
		 
	  }
//	=======JM Marchandising================
	  if($val['COMPANY_ID']==1 && $val['TASK_ID']==73 && $val['SOURCE_ID']==2) //Purchase Gmt/Production,KG
		{
			$mar_fin_plan_recv_qty_array[5][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['MAR_FIN_PLAN_QTY'];
		}

		////////////Kal===============
		if($val['COMPANY_ID']==2 && $val['TASK_ID']==73 && $val['SOURCE_ID']==2) //Purchase Gmt/Production
		{
			$mar_fin_plan_recv_qty_array[5][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['MAR_FIN_PLAN_QTY'];
		}
		
		if($val['TASK_ID']==70) //Trim Sewing
		{
			$mar_trim_sew_plan_recv_qty_array[5][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['MAR_TRIM_SEW_PLAN_QTY'];
		}
		////////////Kal===============
		if($val['TASK_ID']==71 ) //Trim Sewing
		{
			$mar_trim_fin_plan_recv_qty_array[5][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['MAR_TRIM_FIN_PLAN_QTY'];
		}
  }
     //print_r($mar_trim_sew_plan_recv_qty_array);

     $sql_yarn_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
 and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
   and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1 and d.entry_form=1 and  c.BOOKING_NO like '%YDW%'  and c.transaction_date between '$startDate' and '$endDate' $company_conds order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_yarn_recv_kal_result = sql_select($sql_yarn_recv_kal); 
	foreach($sql_yarn_recv_kal_result  as $val)
	{
		$kal_shafipur_poIdArr[$val['POID']]=$val['POID'];
		$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 28, $kal_shafipur_poIdArr, $empty_arr);//PO ID Ref from=27
	//print_r($kal_shafipur_poIdArr);

	    $sql_yarn_recv_kal_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
	inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(28) and g.entry_form=89 and d.entry_form=1
	and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
	  and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and  c.BOOKING_NO like '%YDW%'   and c.transaction_date <= '$endDate'  $company_conds order by c.transaction_date asc";
	  //and b.id in(20325,20326,20327,20328,20329,20330)
	  
	   $sql_yarn_recv_kal_qty_result = sql_select($sql_yarn_recv_kal_qty);
	   foreach($sql_yarn_recv_kal_qty_result  as $val)
	   {
		$trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
		//echo $val['POID'].'='.$trans_date.'='.$val['QUANTITY'].'<br>';
		if($val['COMPANY_ID']==2) //Kal Shafipur
		{
			$shafipur_plan_yarn_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['y_recv']+=$val['QUANTITY'];
		}
		if($val['COMPANY_ID']==1 &&  $val['LOCATION_NAME']==6) //JM Nayapara Fabric
		{
			$shafipur_plan_yarn_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['y_recv']+=$val['QUANTITY'];
		}
		   
	   }
	   // print_r($shafipur_plan_yarn_recv_qty_array2);
// ======================Kntting/Grey Recv production=================
	     $sql_knit_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
	   inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
	   and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
		 and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(2,22) and c.transaction_date between '$startDate' and '$endDate' $company_conds order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
		 
		  $sql_knit_recv_kal_result = sql_select($sql_knit_recv_kal); 
		  foreach($sql_knit_recv_kal_result  as $val)
		  {
			  $kal_knit_poIdArr[$val['POID']]=$val['POID'];
			  $kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			  
		  }
		  fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 29, $kal_knit_poIdArr, $empty_arr);//PO ID Ref from=29
		  //print_r($kal_shafipur_poIdArr);
	  
			$sql_knit_recv_kal_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
		  inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(29) and g.entry_form=89
		  and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
			and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(2,22)    and c.transaction_date <= '$endDate'  $company_conds order by c.transaction_date asc"; 
			//and b.id in(20325,20326,20327,20328,20329,20330)
			
			 $sql_sql_knit_recv_kal_qty_result = sql_select($sql_knit_recv_kal_qty);
			 foreach($sql_sql_knit_recv_kal_qty_result  as $val)
			 {
			  $trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
			  if($val['COMPANY_ID']==2) //Kal Shafipur
			  {
			  	$shafipur_plan_knit_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['knit_recv']+=$val['QUANTITY'];
			  }
			  if($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6) //Jm Nayapara
			  {
			  	$shafipur_plan_knit_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['knit_recv']+=$val['QUANTITY'];
			  }
			 }

			 //////=================Dyeing Prod=======================
			 
			    $sql_dyeing_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.BATCH_QNTY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			 pro_batch_create_dtls c,pro_fab_subprocess d  where a.id=b.job_id  and b.id=c.po_id and c.mst_id=d.batch_id
			 and c.BATCH_QNTY>0 and b.status_active=1 and d.load_unload_id=2 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
			   and b.id in(20328,20329,20330,20334,20335,20337) and d.batch_ext_no is null and d.entry_form in(35) and d.process_end_date between '$startDate' and '$endDate' $company_conds order by d.process_end_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
			   
				$sql_dyeing_kal_result = sql_select($sql_dyeing_kal); 
				foreach($sql_dyeing_kal_result  as $val)
				{
					$kal_dyeing_poIdArr[$val['POID']]=$val['POID'];
					$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				}
				 
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 30, $kal_dyeing_poIdArr, $empty_arr);//PO ID Ref from=30

				$sql_dyeing_qty_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.BATCH_QNTY,d.PROCESS_END_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				pro_batch_create_dtls c,pro_fab_subprocess d,gbl_temp_engine g   where a.id=b.job_id  and b.id=c.po_id and c.mst_id=d.batch_id and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(30) and g.entry_form=89
				and c.BATCH_QNTY>0 and b.status_active=1 and d.load_unload_id=2 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
				  and b.id in(20328,20329,20330,20334,20335,20337) and d.batch_ext_no is null and d.entry_form in(35)   and d.process_end_date <= '$endDate'  $company_conds order by d.process_end_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
				  
				$sql_dyeing_kal_qty_result = sql_select($sql_dyeing_qty_kal); 
				foreach($sql_dyeing_kal_qty_result  as $val)
				{
					$dying_date=date('d-M-Y',strtotime($val['PROCESS_END_DATE']));
					if($val['COMPANY_ID']==2) //Kal Shafipur
					{
					$shafipur_plan_dyeing_recv_qty_array[2][$dying_date][$val['POID']][$val['COMPANY_ID']]['dyeing']+=$val['BATCH_QNTY'];
					}
					if($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6) //JM  Nayapara Fabric
					{
					$shafipur_plan_dyeing_recv_qty_array[2][$dying_date][$val['POID']][$val['COMPANY_ID']]['dyeing']+=$val['BATCH_QNTY'];
					}
				}
				//inv_receive_mas_batchroll
				 //////=================Fabric Service Recv=======================

				$sql_fab_aop_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.BATCH_ISSUE_QTY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				inv_receive_mas_batchroll c,pro_grey_batch_dtls d  where a.id=b.job_id  and b.id=d.order_id and c.id=d.mst_id
				and d.BATCH_ISSUE_QTY>0 and b.status_active=1 and c.entry_form=92 and d.process_id=35 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
				and b.id in(20328,20329,20330,20334,20335,20337)   and c.receive_date between '$startDate' and '$endDate' $company_conds order by c.receive_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
				
				$sql_fab_aop_kal_result = sql_select($sql_fab_aop_kal); 
				foreach($sql_fab_aop_kal_result  as $val)
				{
					$kal_fab_aop_poIdArr[$val['POID']]=$val['POID'];
					$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				}
					
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 31, $kal_fab_aop_poIdArr, $empty_arr);//PO ID Ref from=30

				$sql_fab_aop_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.BATCH_ISSUE_QTY,c.RECEIVE_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				inv_receive_mas_batchroll c,pro_grey_batch_dtls d,gbl_temp_engine g where a.id=b.job_id  and b.id=d.order_id and c.id=d.mst_id  and b.id=g.ref_val and d.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(31) and g.entry_form=89
				and d.BATCH_ISSUE_QTY>0 and b.status_active=1 and c.entry_form=92 and d.process_id=35 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
					and b.id in(20328,20329,20330,20334,20335,20337)   and c.receive_date between '$startDate' and '$endDate' $company_conds order by c.receive_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
					
					$sql_fab_aop_kal_result = sql_select($sql_fab_aop_kal); 
					foreach($sql_fab_aop_kal_result  as $val)
					{
						$aop_date=date('d-M-Y',strtotime($val['RECEIVE_DATE']));
						if($val['COMPANY_ID']==2) //Kal Shafipur
						{		
							$shafipur_plan_aop_recv_qty_array[2][$aop_date][$val['POID']][$val['COMPANY_ID']]['aop_recv']+=$val['BATCH_ISSUE_QTY'];
						}
						else if($val['COMPANY_ID']==2 || $val['LOCATION_NAME']==6  ) // Jm Nayapara Fabric
						{
							$shafipur_plan_aop_recv_qty_array[2][$aop_date][$val['POID']][$val['COMPANY_ID']]['aop_recv']+=$val['BATCH_ISSUE_QTY'];
						}	
					}
			//=============Print Recv=======================
			 $sql_po_prod_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(3)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337)   and c.production_date between '$startDate' and '$endDate' and c.production_type in(3)  $company_conds order by c.PRODUCTION_DATE asc"; //and a.location_name=3
			
			$sql_po_result_prod_kal = sql_select($sql_po_prod_kal);
			foreach ($sql_po_result_prod_kal as $val) 
			{
				$kal_print_poIdArr[$val['POID']]=$val['POID'];
				$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			}
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 32, $kal_print_poIdArr, $empty_arr);//PO ID Ref from=14

			 $sql_po_prod_kal_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.EMBEL_NAME,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
			(case when  c.production_type=3 and c.embel_name=1  then c.PRODUCTION_QUANTITY else 0 end) as PRINT_PROD,
			(case when  c.production_type=3 and c.embel_name=2  then c.PRODUCTION_QUANTITY else 0 end) as EMBR_PROD,
			(case when  c.production_type=3 and c.embel_name=3  then c.PRODUCTION_QUANTITY else 0 end) as WASH_PROD
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
			and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(32) and g.entry_form=89 
			and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0 and c.production_type in(3) and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' $company_conds order by c.PRODUCTION_DATE asc"; 
			$sql_po_result_prod_kal_curr = sql_select($sql_po_prod_kal_curr);
			foreach ($sql_po_result_prod_kal_curr as $val) 
			{
				
				$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
				$loc_id=$val['LOCATION_NAME'];
				$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
				if($val['COMPANY_ID']==2)  //Kal Shafipur
				{
					if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==1) // Print
					{
						$shafipur_plan_print_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_print']+=$val['PRINT_PROD'];
					}
					if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==2)// Embro
					{
						$shafipur_plan_embr_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_embr']+=$val['EMBR_PROD'];
					}
					if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==3)// Wash
					{
						$shafipur_plan_wash_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_wash']+=$val['WASH_PROD'];
					}
				}	 
				else if($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6) //Jm Nayapara Fabric
				{
					if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==1) // Print
					{
						$shafipur_plan_print_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_print']+=$val['PRINT_PROD'];
					}
					if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==2)// Embro
					{
						$shafipur_plan_embr_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_embr']+=$val['EMBR_PROD'];
					}
					if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==3)// Wash
					{
						//$shafipur_plan_wash_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_wash']+=$val['WASH_PROD'];
					}
				}	
				
			}
			unset($sql_po_result_prod_kal_curr); 
			// Knit Fin Fab Transfer Ack===========
			// ======================Knit Fin Fab Transfer Ack=================
			if($cbo_company_id==2) $store_id_cond="8,20";
			else  $store_id_cond="32,52";
			  $sql_knit_fin_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.CONS_QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			inv_transaction c,inv_item_trans_acknowledgement d  where a.id=b.job_id and b.id=c.order_id   and c.mst_id=d.challan_id and d.store_id in($store_id_cond) and d.transfer_criteria = 2 and c.item_category = 2 and c.transaction_type = 5 and c.cons_quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337) and d.entry_form in(247) 
			and d.acknowledg_date between '$startDate' and '$endDate' $company_conds  order by d.acknowledg_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
			  
			   $sql_knit_fin_recv_kal_result = sql_select($sql_knit_fin_recv_kal);  //jm 32,52
			   foreach($sql_knit_fin_recv_kal_result  as $val)
			   {
				   $kal_fin_knit_poIdArr[$val['POID']]=$val['POID'];
				   $kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				   
			   }
			   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 33, $kal_fin_knit_poIdArr, $empty_arr);//PO ID Ref from=27
			   //print_r($kal_shafipur_poIdArr);
		   
			$sql_knit_fin_recv_qty_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.CONS_QUANTITY, d.ACKNOWLEDG_DATE,b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			inv_transaction c,inv_item_trans_acknowledgement d,gbl_temp_engine g where a.id=b.job_id and b.id=c.order_id   and c.mst_id=d.challan_id and b.id=g.ref_val and c.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(33) and g.entry_form=89  and d.store_id in($store_id_cond) and d.transfer_criteria = 2 and c.item_category = 2 and c.transaction_type = 5 and c.cons_quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337) and d.entry_form in(247) 
			and d.acknowledg_date <= '$endDate' $company_conds order by d.acknowledg_date asc";

			$sql_sql_knit_recv_kal_qty_result = sql_select($sql_knit_fin_recv_qty_kal);
			foreach($sql_sql_knit_recv_kal_qty_result  as $val) 
			{
				$trans_date=date('d-M-Y',strtotime($val['ACKNOWLEDG_DATE']));
				if($val['COMPANY_ID']==2)  //Kal Shafipur
				{
					$shafipur_plan_fin_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['fin_recv']+=$val['CONS_QUANTITY'];
				}
				else if($val['COMPANY_ID']==1 && $val['LOCATION_NAME']==6) //Jm Nayapara Fabric
				{
					$shafipur_plan_fin_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['fin_recv']+=$val['CONS_QUANTITY'];
				}
			
			}
	
//===================sql JM Nayapara end ======================

// ========================************Marchandising**************============
// ======================Gmts Fin  Recv production=================
  $sql_mar_fin_gmts_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
  and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(37) and c.transaction_date between '$startDate' and '$endDate' $company_conds order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
  
   $sql_mar_fin_gmts_kal_result = sql_select($sql_mar_fin_gmts_kal); 
   foreach($sql_mar_fin_gmts_kal_result  as $val)
   {
	   $mar_fin_recvpoIdArr[$val['POID']]=$val['POID'];
	   $mar_po_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	   
   }
   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 37, $mar_fin_recvpoIdArr, $empty_arr);//PO ID Ref from=34
   //print_r($kal_shafipur_poIdArr);

	 $sql_mar_fin_gmts_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
   inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(37) and g.entry_form=89
   and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
	 and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(37)    and c.transaction_date <= '$endDate'  $company_conds order by c.transaction_date asc"; 
	 //and b.id in(20325,20326,20327,20328,20329,20330)
	 
	  $sql_mar_fin_gmts_qty_result = sql_select($sql_mar_fin_gmts_qty);
	  foreach($sql_mar_fin_gmts_qty_result  as $val)
	  {
	   $trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
	   if($val['COMPANY_ID']==2) //Kal  Fin
	   {
		   $mar_fin_plan_recv_qty_array[5][$trans_date][$val['POID']][$val['COMPANY_ID']]['fin_recv']+=$val['QUANTITY'];
	   }
	   if($val['COMPANY_ID']==1) //Jm Fin
	   {
		   $mar_fin_plan_recv_qty_array[5][$trans_date][$val['POID']][$val['COMPANY_ID']]['fin_recv']+=$val['QUANTITY'];
	   }
	  }
	  ////================Trims Sewing n Fin===================inv_trims_entry_dtls lib_item_group
   $sql_mar_trim_recv="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO,f.trim_type as TRIM_TYPE from wo_po_details_master a,wo_po_break_down b,
inv_transaction c,order_wise_pro_details d,inv_trims_entry_dtls e,lib_item_group f  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and c.id=e.trans_id and e.id=d.dtls_id  and f.id=e.item_group_id
and d.quantity>0 and f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and  e.status_active=1 and e.is_deleted=0  and  d.status_active=1 and d.is_deleted=0 
  and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(24) and c.transaction_date between '$startDate' and '$endDate' $company_conds order by c.transaction_date asc";  //and b.id in(20325,20326,20327,20328,20329,20330)
  
   $sql_mar_trim_recv_result = sql_select($sql_mar_trim_recv); 
   foreach($sql_mar_trim_recv_result  as $val)
   {
	   $mar_trim_recvpoIdArr[$val['POID']]=$val['POID'];
	   $mar_po_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	   
   }
   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 38, $mar_trim_recvpoIdArr, $empty_arr);//PO ID Ref from=38
   
     $sql_mar_trim_recv_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO,c.TRANSACTION_DATE,f.trim_type as TRIM_TYPE from wo_po_details_master a,wo_po_break_down b,
inv_transaction c,order_wise_pro_details d,inv_trims_entry_dtls e,lib_item_group f,gbl_temp_engine g  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and c.id=e.trans_id and e.id=d.dtls_id  and f.id=e.item_group_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(38) and g.entry_form=89
and d.quantity>0 and f.status_active=1 and f.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and  e.status_active=1 and e.is_deleted=0  and  d.status_active=1 and d.is_deleted=0 
  and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(24) and c.transaction_date <= '$endDate' $company_conds order by c.transaction_date asc";
   
	 
	  $sql_mar_trim_recv_qty_result = sql_select($sql_mar_trim_recv_qty);
	  foreach($sql_mar_trim_recv_qty_result  as $val)
	  {
	   $trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
	   if($val['TRIM_TYPE']==1) //Sew  Trim
	   {
		   $mar_trim_sew_plan_recv_qty_array[5][$trans_date][$val['POID']][$val['COMPANY_ID']]['trim_recv']+=$val['QUANTITY'];
	   }
	   if($val['TRIM_TYPE']==2) // Fin Trim
	   {
		   $mar_trim_fin_plan_recv_qty_array[5][$trans_date][$val['POID']][$val['COMPANY_ID']]['trim_recv']+=$val['QUANTITY'];
	   }
	  }
   

     //echo "<pre>";
    //  print_r($mar_trim_sew_plan_recv_qty_array);
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
	ksort($shafipur_plan_knit_recv_qty_array);
	ksort($shafipur_plan_yarn_recv_qty_array);
	ksort($mar_plan_qty_array);
	ksort($mar_trim_fin_plan_recv_qty_array);
	ksort($mar_trim_sew_plan_recv_qty_array);
	ksort($mar_fin_plan_recv_qty_array);
	ksort($mar_acl_delivery_qty_array);
 

	unset($sql_po_plan_result);
	unset($sql_po_allocate_result);
	unset($sql_sql_knit_recv_kal_qty_result);
	unset($sql_yarn_recv_kal_qty_result);
	 
	
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
							 //****************============ashulia Acl Plan Qty===============***************//
							 foreach($ash_acl_delivery_qty_array[3][$newdate_ash] as $poid_del=>$poDataArr)
							 {
								foreach($poDataArr as $aclpoid=>$poData)
							    {
								 $nay_planQty_acl=$nay_prod_DelQty=0;	
								 //$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']
								 $nay_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
								
								 $nay_prod_DelQty=$poData[$com_id]['prod_del'];
								  $acul_poid=rtrim($poData[$com_id]['acul_poid'],',');
								  //echo $newdate_ash.'='.$nay_prod_DelQty.'<br>';
								 // $acul_poidArr=array_unique(explode(",",$acul_poid));
								 // asort($acul_poidArr);
								 if($nay_planQty_acl>0)
								 {
									 $ash_actual_po_planQty_arr[$newdate_ash][$poid_del]=$nay_planQty_acl;
								 }
								 
									$nay_prod_DelQty=$nay_prod_DelQty;//$nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del'];
									 if($nay_prod_DelQty=='') $nay_prod_DelQty=0;
									 if($nay_planQty_acl>0 || $nay_prod_DelQty>0)
									 {
									 	if($nay_planQty_acl>0 && $nay_prod_DelQty==0)
										{
											
										 $ash_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$ash_po_prodDelQty_arr[$ash_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $ash_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										}
										if($nay_planQty_acl==0 && $nay_prod_DelQty==0)
										{
										  $ash_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$ash_po_prodDelQty_arr[$ash_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $ash_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										   // echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
										}
										
										if($ash_acl_delivery_qty_array[3][$newdate_ash][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $nay_planQty_acl>0)
										{
										  $ash_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$nay_prod_DelQty+$ash_po_prodDelQty_arr[$ash_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $ash_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										}
									}
								 }
							}
							
							
						} 
						//===========Fin End
						//Days loop end
				    }
					//echo "<pre>";
					//print_r($ash_po_prodDelQty_arr);

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
							 //****************============Rat Acl Plan Qty===============***************//
							 foreach($rat_acl_delivery_qty_array[4][$newdate_ash_rat] as $poid_del=>$poDataArr)
							 {
								foreach($poDataArr as $aclpoid=>$poData)
							    {
								 $nay_planQty_acl=$nay_prod_DelQty=0;	
								 //$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']
								 $nay_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
								
								 $nay_prod_DelQty=$poData[$com_id]['prod_del'];
								  $acul_poid=rtrim($poData[$com_id]['acul_poid'],',');
								  //echo $newdate_ash_rat.'='.$nay_prod_DelQty.'<br>';
								  $acul_poidArr=array_unique(explode(",",$acul_poid));
								 // asort($acul_poidArr);
								 if($nay_planQty_acl>0)
								 {
									 $rat_actual_po_planQty_arr[$newdate_ash_rat][$poid_del]=$nay_planQty_acl;
								 }
								 
									$nay_prod_DelQty=$nay_prod_DelQty;//$nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del'];
									 if($nay_prod_DelQty=='') $nay_prod_DelQty=0;
									 if($nay_planQty_acl>0 || $nay_prod_DelQty>0)
									 {
									 	if($nay_planQty_acl>0 && $nay_prod_DelQty==0)
										{
											
										 $rat_po_prodDelQty_arr[$newdate_ash_rat][$poid_del][$aclpoid]=$rat_po_prodDelQty_arr[$rat_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $rat_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash_rat;
										}
										if($nay_planQty_acl==0 && $nay_prod_DelQty==0)
										{
										  $rat_po_prodDelQty_arr[$newdate_ash_rat][$poid_del][$aclpoid]=$rat_po_prodDelQty_arr[$rat_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $rat_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash_rat;
										   // echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
										}
										
										if($rat_acl_delivery_qty_array[4][$newdate_ash_rat][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $nay_planQty_acl>0)
										{
										  $rat_po_prodDelQty_arr[$newdate_ash_rat][$poid_del][$aclpoid]=$nay_prod_DelQty+$rat_po_prodDelQty_arr[$rat_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $rat_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash_rat;
										}
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
					if($unit_id==2 && $com_id==2) //Kal  Shafipur
					{
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_shafi = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_shafi = date('d-M-Y', strtotime("+1 day", strtotime($newdate_shafi)));
							}
							//===========Yarn Recv============
							foreach($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi] as $poid_rec=>$poData)
							{
								$sha_planQty=$yarn_sha_recv_Qty=0;
								$sha_planQty=$poData[$com_id]['plan'];
								$yarn_sha_recv_Qty=$poData[$com_id]['y_recv'];
								if($yarn_sha_recv_Qty=='') $yarn_sha_recv_Qty=0;
								//  echo $newdate_shafi.'='.$yarn_sha_recv_Qty.'<br>';
								if($sha_planQty>0)
								{ 
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planYarnQty_arr[$newdate_shafi][$poid_rec]=$sha_planQty+$shafi_po_planYarnQty_arr[$sha_prev_date_planYarn[$poid_rec]][$poid_rec];
									$sha_prev_date_planYarn[$poid_rec] = $newdate_shafi;
								}
								if($sha_planQty>0 || $yarn_sha_recv_Qty>0)
								{
									if($sha_planQty>0 && $yarn_sha_recv_Qty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planQty==0 && $yarn_sha_recv_Qty>0) 
									{
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$yarn_shafi_planQtycal=0;  
									$yarn_shafi_planQtycal=$shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['plan'];//Plan
									if($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['y_recv']!="" || $yarn_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$yarn_sha_recv_Qty+$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									}
								}
							} 
							//==========Knitting=========================
							foreach($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi] as $poid_k=>$poData)
							{
								$sha_planKnitQty=$sha_prodKnitQty=0;
								$sha_planKnitQty=$poData[$com_id]['plan'];
								$sha_prodKnitQty=$poData[$com_id]['knit_recv'];
								if($sha_prodKnitQty=='') $sha_prodKnitQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planKnitQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planKnitQty_arr[$newdate_shafi][$poid_k]=$sha_planKnitQty+$shafi_po_planKnitQty_arr[$sha_prev_date_planKnit[$poid_k]][$poid_k];
									$sha_prev_date_planKnit[$poid_k] = $newdate_shafi;
								}
								if($sha_planKnitQty>0 || $sha_prodKnitQty>0)
								{
									if($sha_planKnitQty>0 && $sha_prodKnitQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planKnitQty==0 && $sha_prodKnitQty>0) 
									{
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$shafi_planQtycal=0;
									$shafi_planQtycal=$shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['plan'];//Plan
									if($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['knit_recv']!="" || $shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$sha_prodKnitQty+$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									}
								}
							}
							//==========Dyeing=========================
							foreach($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi] as $poid_d=>$poData)
							{
								$sha_planDyingQty=$sha_prodDyeingQty=0;
								$sha_planDyingQty=$poData[$com_id]['plan'];
								$sha_prodDyeingQty=$poData[$com_id]['dyeing'];
								if($sha_prodDyeingQty=='') $sha_prodDyeingQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planDyingQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_planDyingQty+$shafi_po_planDyeingQty_arr[$sha_prev_date_planDye[$poid_d]][$poid_d];
									$sha_prev_date_planDye[$poid_d] = $newdate_shafi;
								}
								if($sha_planDyingQty>0 || $sha_prodDyeingQty>0)
								{
									if($sha_planDyingQty>0 && $sha_prodDyeingQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planDyingQty==0 && $sha_prodDyeingQty>0) 
									{
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$dye_shafi_planQtycal=0;
									$dye_shafi_planQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['plan'];//Plan
									if($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['dyeing']!="" || $dye_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_prodDyeingQty+$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									}
								}
							}
							//==========Aop Recv=========================
							foreach($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi] as $poid_a=>$poData)
							{
								$sha_planAopQty=$sha_prodAopQty=0;
								$sha_planAopQty=$poData[$com_id]['plan'];
								$sha_prodAopQty=$poData[$com_id]['aop_recv'];
								if($sha_prodAopQty=='') $sha_prodAopQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planAopQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planAopQty_arr[$newdate_shafi][$poid_a]=$sha_planAopQty+$shafi_po_planAopQty_arr[$sha_prev_date_planAop[$poid_a]][$poid_a];
									$sha_prev_date_planAop[$poid_a] = $newdate_shafi;
								}
								if($sha_planAopQty>0 || $sha_prodAopQty>0)
								{
									if($sha_planAopQty>0 && $sha_prodAopQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planAopQty==0 && $sha_prodAopQty>0) 
									{
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$aop_shafi_planQtycal=0;
									$aop_shafi_planQtycal=$shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['plan'];//Plan
									if($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['aop_recv']!="" || $aop_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$sha_prodAopQty+$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									}
								}
							}

							//==========Print Recv=========================
							foreach($shafipur_plan_print_recv_qty_array[2][$newdate_shafi] as $poid_pr=>$poData)
							{
								$sha_planPrintQty=$sha_prodPrintQty=0;
								$sha_planPrintQty=$poData[$com_id]['plan'];
								$sha_prodPrintQty=$poData[$com_id]['prod_print'];
								if($sha_prodPrintQty=='') $sha_prodPrintQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planPrintQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_planPrintQty+$shafi_po_planPrintQty_arr[$sha_prev_date_planPrint[$poid_pr]][$poid_pr];
									$sha_prev_date_planPrint[$poid_pr] = $newdate_shafi;
								}
								if($sha_planPrintQty>0 || $sha_prodPrintQty>0)
								{
									if($sha_planPrintQty>0 && $sha_prodPrintQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planPrintQty==0 && $sha_prodPrintQty>0) 
									{
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$print_shafi_planQtycal=0;
									$print_shafi_planQtycal=$shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['plan'];//Plan
									if($shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['prod_print']!="" || $print_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_prodPrintQty+$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									}
								}
							}
							//==========Embro Recv=========================
							foreach($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi] as $poid_er=>$poData)
							{
								$sha_planEmbQty=$sha_prodEmbQty=0;
								$sha_planEmbQty=$poData[$com_id]['plan'];
								$sha_prodEmbQty=$poData[$com_id]['prod_embr'];
								if($sha_prodEmbQty=='') $sha_prodEmbQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planEmbQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planEmbQty_arr[$newdate_shafi][$poid_er]=$sha_planEmbQty+$shafi_po_planEmbQty_arr[$sha_prev_date_planEmb[$poid_er]][$poid_er];
									$sha_prev_date_planEmb[$poid_er] = $newdate_shafi;
								}
								if($sha_planEmbQty>0 || $sha_prodEmbQty>0)
								{
									if($sha_planEmbQty>0 && $sha_prodEmbQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planEmbQty==0 && $sha_prodEmbQty>0) 
									{
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$emb_shafi_planQtycal=0;
									$emb_shafi_planQtycal=$shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['plan'];//Plan
									if($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['prod_embr']!="" || $emb_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$sha_prodEmbQty+$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									}
								}
							}
							//==========Wash Recv=========================
							foreach($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi] as $poid_wr=>$poData)
							{
								$sha_planWashQty=$sha_prodWashQty=0;
								$sha_planWashQty=$poData[$com_id]['plan'];
								$sha_prodWashQty=$poData[$com_id]['prod_wash'];
								if($sha_prodWashQty=='') $sha_prodWashQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planWashQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planWashQty_arr[$newdate_shafi][$poid_wr]=$sha_planWashQty+$shafi_po_planWashQty_arr[$sha_prev_date_planWash[$poid_wr]][$poid_wr];
									$sha_prev_date_planWash[$poid_wr] = $newdate_shafi;
								}
								if($sha_planWashQty>0 || $sha_prodWashQty>0)
								{
									if($sha_planWashQty>0 && $sha_prodWashQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planWashQty==0 && $sha_prodWashQty>0) 
									{
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$wash_shafi_planQtycal=0;
									$wash_shafi_planQtycal=$shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['plan'];//Plan
									if($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['prod_wash']!="" || $wash_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$sha_prodWashQty+$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
									}
								}
							}

							//==========Fin Fab Transfer Acknowlege Recv=========================
							foreach($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi] as $poid_ft=>$poData)
							{
								$sha_planFinQty=$sha_prodFinQty=0;
								$sha_planFinQty=$poData[$com_id]['plan'];
								$sha_prodFinQty=$poData[$com_id]['fin_recv'];
								if($sha_prodFinQty=='') $sha_prodFinQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planFinQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planFinQty_arr[$newdate_shafi][$poid_ft]=$sha_planFinQty+$shafi_po_planFinQty_arr[$sha_prev_date_planFin[$poid_ft]][$poid_ft];
									$sha_prev_date_planFin[$poid_ft] = $newdate_shafi;
								}
								if($sha_planFinQty>0 || $sha_prodFinQty>0)
								{
									if($sha_planFinQty>0 && $sha_prodFinQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planFinQty==0 && $sha_prodFinQty>0) 
									{
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$fin_shafi_planQtycal=0;
									$fin_shafi_planQtycal=$shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['plan'];//Plan
									if($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['fin_recv']!="" || $fin_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$sha_prodFinQty+$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
									}
								}
							}

						}
					}
					if($unit_id==2 && $com_id==1) //Jm  Nayapara Fabric
					{
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_shafi = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_shafi = date('d-M-Y', strtotime("+1 day", strtotime($newdate_shafi)));
							}
							//===========Yarn Recv============
							foreach($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi] as $poid_rec=>$poData)
							{
								$sha_planQty=$yarn_sha_recv_Qty=0;
								$sha_planQty=$poData[$com_id]['plan'];
								$yarn_sha_recv_Qty=$poData[$com_id]['y_recv'];
								if($yarn_sha_recv_Qty=='') $yarn_sha_recv_Qty=0;
								//  echo $newdate_shafi.'='.$yarn_sha_recv_Qty.'<br>';
								if($sha_planQty>0)
								{ 
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planYarnQty_arr[$newdate_shafi][$poid_rec]=$sha_planQty+$shafi_po_planYarnQty_arr[$sha_prev_date_planYarn[$poid_rec]][$poid_rec];
									$sha_prev_date_planYarn[$poid_rec] = $newdate_shafi;
								}
								if($sha_planQty>0 || $yarn_sha_recv_Qty>0)
								{
									if($sha_planQty>0 && $yarn_sha_recv_Qty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planQty==0 && $yarn_sha_recv_Qty>0) 
									{
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$yarn_shafi_planQtycal=0;  
									$yarn_shafi_planQtycal=$shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['plan'];//Plan
									if($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['y_recv']!="" || $yarn_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$yarn_sha_recv_Qty+$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									}
								}
							} 
							//==========Knitting=========================
							foreach($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi] as $poid_k=>$poData)
							{
								$sha_planKnitQty=$sha_prodKnitQty=0;
								$sha_planKnitQty=$poData[$com_id]['plan'];
								$sha_prodKnitQty=$poData[$com_id]['knit_recv'];
								if($sha_prodKnitQty=='') $sha_prodKnitQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planKnitQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planKnitQty_arr[$newdate_shafi][$poid_k]=$sha_planKnitQty+$shafi_po_planKnitQty_arr[$sha_prev_date_planKnit[$poid_k]][$poid_k];
									$sha_prev_date_planKnit[$poid_k] = $newdate_shafi;
								}
								if($sha_planKnitQty>0 || $sha_prodKnitQty>0)
								{
									if($sha_planKnitQty>0 && $sha_prodKnitQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planKnitQty==0 && $sha_prodKnitQty>0) 
									{
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$shafi_planQtycal=0;
									$shafi_planQtycal=$shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['plan'];//Plan
									if($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['knit_recv']!="" || $shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$sha_prodKnitQty+$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									}
								}
							}
							//==========Dyeing=========================
							foreach($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi] as $poid_d=>$poData)
							{
								$sha_planDyingQty=$sha_prodDyeingQty=0;
								$sha_planDyingQty=$poData[$com_id]['plan'];
								$sha_prodDyeingQty=$poData[$com_id]['dyeing'];
								if($sha_prodDyeingQty=='') $sha_prodDyeingQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planDyingQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_planDyingQty+$shafi_po_planDyeingQty_arr[$sha_prev_date_planDye[$poid_d]][$poid_d];
									$sha_prev_date_planDye[$poid_d] = $newdate_shafi;
								}
								if($sha_planDyingQty>0 || $sha_prodDyeingQty>0)
								{
									if($sha_planDyingQty>0 && $sha_prodDyeingQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planDyingQty==0 && $sha_prodDyeingQty>0) 
									{
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$dye_shafi_planQtycal=0;
									$dye_shafi_planQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['plan'];//Plan
									if($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['dyeing']!="" || $dye_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_prodDyeingQty+$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									}
								}
							}
							//==========Aop Recv=========================
							foreach($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi] as $poid_a=>$poData)
							{
								$sha_planAopQty=$sha_prodAopQty=0;
								$sha_planAopQty=$poData[$com_id]['plan'];
								$sha_prodAopQty=$poData[$com_id]['aop_recv'];
								if($sha_prodAopQty=='') $sha_prodAopQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planAopQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planAopQty_arr[$newdate_shafi][$poid_a]=$sha_planAopQty+$shafi_po_planAopQty_arr[$sha_prev_date_planAop[$poid_a]][$poid_a];
									$sha_prev_date_planAop[$poid_a] = $newdate_shafi;
								}
								if($sha_planAopQty>0 || $sha_prodAopQty>0)
								{
									if($sha_planAopQty>0 && $sha_prodAopQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planAopQty==0 && $sha_prodAopQty>0) 
									{
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$aop_shafi_planQtycal=0;
									$aop_shafi_planQtycal=$shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['plan'];//Plan
									if($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['aop_recv']!="" || $aop_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$sha_prodAopQty+$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									}
								}
							}

							//==========Print Recv=========================
							foreach($shafipur_plan_print_recv_qty_array[2][$newdate_shafi] as $poid_pr=>$poData)
							{
								$sha_planPrintQty=$sha_prodPrintQty=0;
								$sha_planPrintQty=$poData[$com_id]['plan'];
								$sha_prodPrintQty=$poData[$com_id]['prod_print'];
								if($sha_prodPrintQty=='') $sha_prodPrintQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planPrintQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_planPrintQty+$shafi_po_planPrintQty_arr[$sha_prev_date_planPrint[$poid_pr]][$poid_pr];
									$sha_prev_date_planPrint[$poid_pr] = $newdate_shafi;
								}
								if($sha_planPrintQty>0 || $sha_prodPrintQty>0)
								{
									if($sha_planPrintQty>0 && $sha_prodPrintQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planPrintQty==0 && $sha_prodPrintQty>0) 
									{
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$print_shafi_planQtycal=0;
									$print_shafi_planQtycal=$shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['plan'];//Plan
									if($shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['prod_print']!="" || $print_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_prodPrintQty+$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									}
								}
							}
							//==========Embro Recv=========================
							foreach($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi] as $poid_er=>$poData)
							{
								$sha_planEmbQty=$sha_prodEmbQty=0;
								$sha_planEmbQty=$poData[$com_id]['plan'];
								$sha_prodEmbQty=$poData[$com_id]['prod_embr'];
								if($sha_prodEmbQty=='') $sha_prodEmbQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planEmbQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planEmbQty_arr[$newdate_shafi][$poid_er]=$sha_planEmbQty+$shafi_po_planEmbQty_arr[$sha_prev_date_planEmb[$poid_er]][$poid_er];
									$sha_prev_date_planEmb[$poid_er] = $newdate_shafi;
								}
								if($sha_planEmbQty>0 || $sha_prodEmbQty>0)
								{
									if($sha_planEmbQty>0 && $sha_prodEmbQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planEmbQty==0 && $sha_prodEmbQty>0) 
									{
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$emb_shafi_planQtycal=0;
									$emb_shafi_planQtycal=$shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['plan'];//Plan
									if($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['prod_embr']!="" || $emb_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$sha_prodEmbQty+$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									}
								}
							}
							//==========Wash Recv=========================
							// foreach($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi] as $poid_wr=>$poData)
							// {
							// 	$sha_planWashQty=$sha_prodWashQty=0;
							// 	$sha_planWashQty=$poData[$com_id]['plan'];
							// 	$sha_prodWashQty=$poData[$com_id]['prod_wash'];
							// 	if($sha_prodWashQty=='') $sha_prodWashQty=0;
							// 	 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
							// 	if($sha_planWashQty>0)
							// 	{
							// 		//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
							// 		$shafi_po_planWashQty_arr[$newdate_shafi][$poid_wr]=$sha_planWashQty+$shafi_po_planWashQty_arr[$sha_prev_date_planWash[$poid_wr]][$poid_wr];
							// 		$sha_prev_date_planWash[$poid_wr] = $newdate_shafi;
							// 	}
							// 	if($sha_planWashQty>0 || $sha_prodWashQty>0)
							// 	{
							// 		if($sha_planWashQty>0 && $sha_prodWashQty==0)
							// 		{
							// 		//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
							// 			$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
							// 			$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
							// 			//echo "A=".$poid;
							// 		}
							// 		if($sha_planWashQty==0 && $sha_prodWashQty>0) 
							// 		{
							// 			$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
							// 			$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
							// 		// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
							// 		}
							// 		$wash_shafi_planQtycal=0;
							// 		$wash_shafi_planQtycal=$shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['plan'];//Plan
							// 		if($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['prod_wash']!="" || $wash_shafi_planQtycal>0)
							// 		{
							// 			//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
							// 			$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$sha_prodWashQty+$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
							// 			$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
							// 		}
							// 	}
							// }

							//==========Fin Fab Transfer Acknowlege Recv=========================
							foreach($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi] as $poid_ft=>$poData)
							{
								$sha_planFinQty=$sha_prodFinQty=0;
								$sha_planFinQty=$poData[$com_id]['plan'];
								$sha_prodFinQty=$poData[$com_id]['fin_recv'];
								if($sha_prodFinQty=='') $sha_prodFinQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planFinQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planFinQty_arr[$newdate_shafi][$poid_ft]=$sha_planFinQty+$shafi_po_planFinQty_arr[$sha_prev_date_planFin[$poid_ft]][$poid_ft];
									$sha_prev_date_planFin[$poid_ft] = $newdate_shafi;
								}
								if($sha_planFinQty>0 || $sha_prodFinQty>0)
								{
									if($sha_planFinQty>0 && $sha_prodFinQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planFinQty==0 && $sha_prodFinQty>0) 
									{
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$fin_shafi_planQtycal=0;
									$fin_shafi_planQtycal=$shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['plan'];//Plan
									if($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['fin_recv']!="" || $fin_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$sha_prodFinQty+$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
									}
								}
							}

						}
					}
					
					//=======Marchandising=============== 
					if($unit_id==5)
					{
						$diff_days=datediff('d',$from_date,$to_date);
						 
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$mar_newdate = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$mar_newdate = date('d-M-Y', strtotime("+1 day", strtotime($mar_newdate)));
							}
							
							//echo $new_date = date('d-M-Y', strtotime($from_date) + (3600*24));
							//echo "<br />";
							//$newdate =change_date_format($from_date,'','',1);
							$kpi_dates=strtoupper(date('d-m-Y',$mar_newdate)); //previ_plan_cumulative_cal
							 //===========Yarn Allocation n plan============
							foreach($mar_plan_qty_array[5][$mar_newdate] as $poid_y=>$poData)
							{
								$mar_planQty=$mar_alloQty=0;
								$mar_planQty=$poData[$com_id]['planQty'];
								$mar_alloQty=$poData[$com_id]['alloQty'];
								if($mar_alloQty=='') $mar_alloQty=0;
								if($mar_planQty>0)
								{
									$mar_po_planQty_arr[$mar_newdate][$poid_y]=$mar_planQty+$mar_po_planQty_arr[$mar_prev_date[$poid_y]][$poid_y];
									$mar_prev_date[$poid_y] = $mar_newdate;
								}
								if($mar_planQty>0 || $mar_alloQty>0)
								{
									if($mar_planQty>0 && $mar_alloQty==0)
									{
										$mar_po_alloQty_arr[$mar_newdate][$poid_y]=$mar_po_alloQty_arr[$mar_prev_allocation_date[$poid_y]][$poid_y];
										$mar_prev_allocation_date[$poid_y] = $mar_newdate;
										//echo "A=".$poid;
									}
									if($mar_planQty==0 && $mar_alloQty>0) 
									{
										$mar_po_alloQty_arr[$mar_newdate][$poid_y]=$mar_po_alloQty_arr[$mar_prev_allocation_date[$poid_y]][$poid_y];
										$mar_prev_allocation_date[$poid_y] = $mar_newdate;
									}
									$mar_planQtycal=0;
									$mar_planQtycal=$mar_plan_qty_array[5][$mar_newdate][$poid_y][$com_id]['planQty'];//Plan
									
									if($mar_plan_qty_array[5][$mar_newdate][$poid_y][$com_id]['alloQty']!="" || $mar_planQtycal>0)
									{
										$mar_po_alloQty_arr[$mar_newdate][$poid_y]=$mar_alloQty+$mar_po_alloQty_arr[$mar_prev_allocation_date[$poid_y]][$poid_y];
										$mar_prev_allocation_date[$poid_y] = $mar_newdate;
									}
								}
							} 
							 //===========Fin Fab Production purchase n plan============
							foreach($mar_fin_plan_recv_qty_array[5][$mar_newdate] as $poid_ff=>$poData)
							{
								$fin_fab_mar_planQty=$fin_fab_mar_alloQty=0;
								$fin_fab_mar_planQty=$poData[$com_id]['plan'];
								$fin_fab_mar_alloQty=$poData[$com_id]['fin_recv'];
								if($fin_fab_mar_alloQty=='') $fin_fab_mar_alloQty=0;
								if($fin_fab_mar_planQty>0)
								{
									$mar_fin_po_planQty_arr[$mar_newdate][$poid_ff]=$fin_fab_mar_planQty+$mar_fin_po_planQty_arr[$ff_mar_prev_date[$poid_ff]][$poid_ff];
									$ff_mar_prev_date[$poid_ff] = $mar_newdate;
								}
								if($fin_fab_mar_planQty>0 || $fin_fab_mar_alloQty>0)
								{
									if($fin_fab_mar_planQty>0 && $fin_fab_mar_alloQty==0)
									{
										$mar_fin_fab_po_Qty_arr[$mar_newdate][$poid_ff]=$mar_fin_fab_po_Qty_arr[$mar_fin_prev_allocation_date[$poid_ff]][$poid_ff];
										$mar_fin_prev_allocation_date[$poid_ff] = $mar_newdate;
										//echo "A=".$poid;
									}
									 
									if($fin_fab_mar_planQty==0 && $fin_fab_mar_alloQty>0) 
									{
										$mar_fin_fab_po_Qty_arr[$mar_newdate][$poid_ff]=$mar_fin_fab_po_Qty_arr[$mar_fin_prev_allocation_date[$poid_ff]][$poid_ff];
										$mar_fin_prev_allocation_date[$poid_ff] = $mar_newdate;
									}
									$ff_mar_planQtycal=0;
									$ff_mar_planQtycal=$mar_fin_plan_recv_qty_array[5][$mar_newdate][$poid_ff][$com_id]['plan'];//Plan
									
									if($mar_fin_plan_recv_qty_array[5][$mar_newdate][$poid_ff][$com_id]['fin_recv']!="" || $ff_mar_planQtycal>0)
									{
										$mar_fin_fab_po_Qty_arr[$mar_newdate][$poid_ff]=$fin_fab_mar_alloQty+$mar_fin_fab_po_Qty_arr[$mar_fin_prev_allocation_date[$poid_ff]][$poid_ff];
										$mar_fin_prev_allocation_date[$poid_ff] = $mar_newdate;
									}
								}
							}
							///////////===============Marchandising================
							 //=============######### Actual PO and Gmts  Delivery ****########=========================//
							 //nay_actual_plan_qty_array
					 foreach($mar_acl_delivery_qty_array[5][$mar_newdate] as $poid_del=>$poDataArr)
					 {
						foreach($poDataArr as $aclpoid=>$poData)
						{
							$mar_del_planQty_acl=$deli_prod_DelQty=0;	
							$mar_del_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
							$deli_prod_DelQty=$poData[$com_id]['prod_del'];
							if($mar_del_planQty_acl>0)
							{
							$mar_actual_po_planQty_arr[$mar_newdate][$poid_del]=$mar_del_planQty_acl;
							}
							if($deli_prod_DelQty=='') $deli_prod_DelQty=0;
							if($mar_del_planQty_acl>0 || $deli_prod_DelQty>0)
							{
								if($mar_del_planQty_acl>0 && $deli_prod_DelQty==0)
								{
								
								$mar_po_prodDelQty_arr[$mar_newdate][$poid_del][$aclpoid]=$mar_po_prodDelQty_arr[$mar_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
								$mar_prev_prodDel_date[$poid_del][$aclpoid] = $mar_newdate;
								}
								if($mar_del_planQty_acl==0 && $deli_prod_DelQty==0)
								{
								$mar_po_prodDelQty_arr[$mar_newdate][$poid_del][$aclpoid]=$mar_po_prodDelQty_arr[$mar_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
								$mar_prev_prodDel_date[$poid_del][$aclpoid] = $mar_newdate;
								// echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
								}
								
								if($mar_acl_delivery_qty_array[5][$mar_newdate][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $mar_del_planQty_acl>0)
								{
								$mar_po_prodDelQty_arr[$mar_newdate][$poid_del][$aclpoid]=$deli_prod_DelQty+$mar_po_prodDelQty_arr[$mar_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
								$mar_prev_prodDel_date[$poid_del][$aclpoid] = $mar_newdate;
								}
						     }
						   }
					     } 
						 //===============Trim Sew Recv n Plan===================
						 	foreach($mar_trim_sew_plan_recv_qty_array[5][$mar_newdate] as $poid_t=>$poData)
							{
								$trim_sew_mar_planQty=$trim_sew_mar_recvQty=0;
								$trim_sew_mar_planQty=$poData[$com_id]['plan'];
								$trim_sew_mar_recvQty=$poData[$com_id]['trim_recv'];
								if($trim_sew_mar_recvQty=='') $trim_sew_mar_recvQty=0;
								if($trim_sew_mar_planQty>0)
								{
								$mar_trim_sew_po_planQty_arr[$mar_newdate][$poid_t]=$trim_sew_mar_planQty+$mar_trim_sew_po_planQty_arr[$trim_mar_sew_prev_date[$poid_t]][$poid_t];
								$trim_mar_sew_prev_date[$poid_t] = $mar_newdate;
								}
								if($trim_sew_mar_planQty>0 || $trim_sew_mar_recvQty>0)
								{
									if($trim_sew_mar_planQty>0 && $trim_sew_mar_recvQty==0)
									{
										$mar_trim_sew_po_Qty_arr[$mar_newdate][$poid_t]=$mar_trim_sew_po_Qty_arr[$mar_trim_sew_prev_date[$poid_t]][$poid_t];
										$mar_trim_sew_prev_date[$poid_t] = $mar_newdate;
										//echo "A=".$poid;
									}
									 
									if($trim_sew_mar_planQty==0 && $trim_sew_mar_recvQty>0) 
									{
										$mar_trim_sew_po_Qty_arr[$mar_newdate][$poid_t]=$mar_trim_sew_po_Qty_arr[$mar_trim_sew_prev_date[$poid_t]][$poid_t];
										$mar_trim_sew_prev_date[$poid_t] = $mar_newdate;
									}
									$trim_mar_planQtycal=0;
									$trim_mar_planQtycal=$mar_trim_sew_plan_recv_qty_array[5][$mar_newdate][$poid_t][$com_id]['plan'];//Plan
									
									if($mar_trim_sew_plan_recv_qty_array[5][$mar_newdate][$poid_t][$com_id]['trim_recv']!="" || $trim_mar_planQtycal>0)
									{
										$mar_trim_sew_po_Qty_arr[$mar_newdate][$poid_t]=$trim_sew_mar_recvQty+$mar_trim_sew_po_Qty_arr[$mar_trim_sew_prev_date[$poid_t]][$poid_t];
										$mar_trim_sew_prev_date[$poid_t] = $mar_newdate;
									}
								}
							}
						  //===============Trim Fin Recv n Plan===================
						  foreach($mar_trim_fin_plan_recv_qty_array[5][$mar_newdate] as $poid_tf=>$poData)
							{
								$trim_fin_mar_planQty=$trim_fin_mar_recvQty=0;
								$trim_fin_mar_planQty=$poData[$com_id]['plan'];
								$trim_fin_mar_recvQty=$poData[$com_id]['trim_recv'];
								if($trim_fin_mar_recvQty=='') $trim_fin_mar_recvQty=0;
								if($trim_fin_mar_planQty>0)
								{
									$mar_trim_fin_po_planQty_arr[$mar_newdate][$poid_tf]=$trim_fin_mar_planQty+$mar_trim_fin_po_planQty_arr[$trim_mar_prev_date[$poid_tf]][$poid_tf];
									$trim_mar_prev_date[$poid_tf] = $mar_newdate;
								}
								if($trim_fin_mar_planQty>0 || $trim_fin_mar_recvQty>0)
								{
									if($trim_fin_mar_planQty>0 && $trim_fin_mar_recvQty==0)
									{
										$mar_trim_fin_po_Qty_arr[$mar_newdate][$poid_tf]=$mar_trim_fin_po_Qty_arr[$mar_trim_fin_prev_date[$poid_tf]][$poid_tf];
										$mar_trim_fin_prev_date[$poid_tf] = $mar_newdate;
										//echo "A=".$poid;
									}
									 
									if($trim_fin_mar_planQty==0 && $trim_fin_mar_recvQty>0) 
									{
										$mar_trim_fin_po_Qty_arr[$mar_newdate][$poid_tf]=$mar_trim_fin_po_Qty_arr[$mar_trim_fin_prev_date[$poid_tf]][$poid_tf];
										$mar_trim_fin_prev_date[$poid_tf] = $mar_newdate;
									}
									$trim_fin_mar_planQtycal=0;
									$trim_fin_mar_planQtycal=$mar_trim_fin_plan_recv_qty_array[5][$mar_newdate][$poid_tf][$com_id]['plan'];//Plan
									
									if($mar_trim_fin_plan_recv_qty_array[5][$mar_newdate][$poid_tf][$com_id]['trim_recv']!="" || $trim_fin_mar_planQtycal>0)
									{
										$mar_trim_fin_po_Qty_arr[$mar_newdate][$poid_tf]=$trim_fin_mar_recvQty+$mar_trim_fin_po_Qty_arr[$mar_trim_fin_prev_date[$poid_tf]][$poid_tf];
										$mar_trim_fin_prev_date[$poid_tf] = $mar_newdate;
									}
								}
							}
						 
						} 
						//Days loop end
				    } 
			 } 
		//Month Loop end here //Month Loop
		}
          		// echo "<pre>";
		         //  print_r($mar_trim_sew_po_Qty_arr); 
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
	   //Actual PO & Delivery for Kal
	   //$rat_acl_kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
			//$loc_acl_po_noArr[$val['POID']]=$loc_id; 
	   foreach($rat_acl_kal_com_poIdArr as $comp_id=>$comData)
	   {
		foreach($comData as $poid=>$IR_Data)
		{
		   foreach($IR_Data as $acl_poid=>$IR)
		  {
				$loaction=0;
			   $loaction=$loc_acl_po_noArr[$poid];
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
						   if($loaction==3) // Ashulia===========  Start===================
						   {
								
							   //==========GMTS Delivery Qty Prod and Actual Plan Qty==============
								
							   $ash_planAclQtycal=0;//
							   $ash_planAclQtycal=$ash_acl_delivery_qty_array[$loaction][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
							   $ash_mon_delivery_qty=$ash_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
							   //echo $day_all.'='.$mon_delivery_qty.'<br>';
							   if($ash_planAclQtycal>0 && $ash_mon_delivery_qty>0)
							   {
								  // $mon_del_qty=$mon_delivery_qty;
								   $ash_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['del_prod']+=$ash_mon_delivery_qty;
							   }
							   $ash_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['acl_plan']+=$ash_planAclQtycal;
						   } 
							//***********=========== Ashulia End===================********//
							if($loaction==5) //========== Ratanpur===========  Start===================
							{
								 
								//==========GMTS Delivery Qty Prod and Actual Plan Qty==============
								$loaction_rat=4;
								$rat_planAclQtycal=0;//
								$rat_planAclQtycal=$rat_acl_delivery_qty_array[$loaction_rat][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
								$rat_mon_delivery_qty=$rat_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
								//echo 
								$day_all.'='.$mon_delivery_qty.'<br>';
								if($rat_planAclQtycal>0 && $rat_mon_delivery_qty>0)
								{
									//$mon_del_qty=$mon_delivery_qty;
									$rat_delivery_month_wise_kpiArr[$comp_id][$loaction_rat][$day_all]['del_prod']+=$rat_mon_delivery_qty;
								}
								$rat_delivery_month_wise_kpiArr[$comp_id][$loaction_rat][$day_all]['acl_plan']+=$rat_planAclQtycal;
							} 
							 //***********=========== Ratanpur End===================********//
						}
			   }
		   }
		 }
	  }
	   //=========For Ashulia Location================
	 
	 //Month Wise KPI Percentage summary part
	   foreach($qc_month_wise_kpiArr as $comp_id=>$comData) 
	   {
	   foreach($comData as $loc_id=>$LocData) 
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

		//=========For Ashulia **Gmts Delivery** Location================//
		foreach($ash_delivery_month_wise_kpiArr as $comp_id=>$comData)  
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
					$ash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['deli']+=$del_month_kpi_per;
					}
					$out_plan_day_chk= $out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
					$qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					$in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
					$fin_plan_day_chk= $fin_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin_plan'];
					
					if($del_month_plan>0)
					{
						$ash_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						$mon_grand_del_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						
					}
					if($del_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk))
					{
						$gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
			}
			}
		}
		//=========For Ratanpur **Gmts Delivery** Location================//
		//print_r($rat_delivery_month_wise_kpiArr);
		foreach($rat_delivery_month_wise_kpiArr as $comp_id=>$comData)  
		{
		 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
		 {
			foreach($LocData as $day_key=>$row)
			{
				$del_month_prod=$del_month_plan=0;
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
					 $ash_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['deli']+=$del_month_kpi_per;
					 }
					 $out_plan_day_chk= $out_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['out_plan'];
					 $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['qc_plan'];
					 $in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['in_plan'];
					 $fin_plan_day_chk= $fin_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin_plan'];
					 
					 if($del_month_plan>0)
					 {
						$ash_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						$mon_grand_del_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						
					 }
					if($del_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk))
					{
						$gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				 }
			  }
			}
		}
 		//print_r($rat_delivery_month_wise_kpiArr);
		 
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
					$event_del_count=$mon_grand_del_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_mon_count= $event_qc_count+$event_in_count+$event_out_count+$event_fin_count+$event_del_count;
					$all_kpi_per=$per['qc']+$per['in']+$per['out']+$per['fin']+$per['deli'];
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


////************ */===============================Shafipur============================
	foreach($kal_po_shafipur_poIdArr as $comp_id=>$comData)
	   {
		 foreach($comData as $poid=>$IR)
	     {
				// $loaction=0;
				//$loaction=$poId_loaction_Arr[$comp_id][$poid];
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
								//==========Shafipur Location Prod and  Plan==============
								//==========Shafipur Yarn Recv   and  Plan==============
								$planYarnQtycal=0;
								$planYarnQtycal=$shafipur_plan_yarn_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
								if($planYarnQtycal>0)//shafi_po_prodYarnQty_arr
								{
									$mon_yarn_qty=$shafi_po_prodYarnQty_arr[$day_all][$poid];
									//  echo $day_all.'='.$mon_yarn_qty.'<br>';
									$shafi_yarn_month_wise_kpiArr[$comp_id][2][$day_all]['yarn_prod']+=$mon_yarn_qty;
								}
								$shafi_yarn_month_wise_kpiArr[$comp_id][2][$day_all]['yarn_plan']+=$shafi_po_planYarnQty_arr[$day_all][$poid];

								//==========Knit Prod and Knit Plan==============
								$planKnitQtycal=0;
								$planKnitQtycal=$shafipur_plan_knit_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
								if($planKnitQtycal>0)
								{
									$mon_knit_qty=$shafi_po_prodKnitQty_arr[$day_all][$poid];
									$shafi_knit_month_wise_kpiArr[$comp_id][2][$day_all]['knit_prod']+=$mon_knit_qty;
								}
								$shafi_knit_month_wise_kpiArr[$comp_id][2][$day_all]['knit_plan']+=$shafi_po_planKnitQty_arr[$day_all][$poid];
								//==========Dyeing Qty Prod and Dyeing Plan Qty==============
								$planDyeingQtycal=0;
								$planDyeingQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
								if($planDyeingQtycal>0)
								{
									$mon_dyeing_qty=$shafi_po_prodDyeingQty_arr[$day_all][$poid];
									$shafi_dyeing_month_wise_kpiArr[$comp_id][2][$day_all]['dyeing_prod']+=$mon_dyeing_qty;
								}
								$shafi_dyeing_month_wise_kpiArr[$comp_id][2][$day_all]['dyeing_plan']+=$shafi_po_planDyeingQty_arr[$day_all][$poid];
								//==========GMTS Aop Rec Qty Prod and Aop Plan Qty==============
								$planAopQtycal=0; 
								$planAopQtycal=$shafipur_plan_aop_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
								if($planAopQtycal>0)
								{
									$mon_aop_qty=$shafi_po_prodAopQty_arr[$day_all][$poid];
									$shafi_aop_month_wise_kpiArr[$comp_id][2][$day_all]['aop_prod']+=$mon_aop_qty;
								}
								$shafi_aop_month_wise_kpiArr[$comp_id][2][$day_all]['aop_plan']+=$shafi_po_planAopQty_arr[$day_all][$poid];
								//==========GMTS Print Rec Qty Prod and Print Plan Qty==============
								$planPrintQtycal=0; 
								$planPrintQtycal=$shafipur_plan_print_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
								if($planPrintQtycal>0)
								{
									$mon_print_qty=$shafi_po_prodPrintQty_arr[$day_all][$poid];
									$shafi_print_month_wise_kpiArr[$comp_id][2][$day_all]['print_prod']+=$mon_print_qty;
								}
								$shafi_print_month_wise_kpiArr[$comp_id][2][$day_all]['print_plan']+=$shafi_po_planPrintQty_arr[$day_all][$poid];
								//==========GMTS Embro Rec Qty Prod and Embro Plan Qty==============
								$planEmbQtycal=0; 
								$planEmbQtycal=$shafipur_plan_embr_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
								 
								if($planEmbQtycal>0)
								{
									$mon_embr_qty=$shafi_po_prodEmbQty_arr[$day_all][$poid];
									$shafi_embr_month_wise_kpiArr[$comp_id][2][$day_all]['emb_prod']+=$mon_embr_qty;
								}
								$shafi_embr_month_wise_kpiArr[$comp_id][2][$day_all]['emb_plan']+=$shafi_po_planEmbQty_arr[$day_all][$poid];
								//==========GMTS Wash Rec Qty Prod and Wash Plan Qty==============
								$planWashQtycal=0; 
								$planWashQtycal=$shafipur_plan_wash_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
								if($planWashQtycal>0)
								{
									$mon_wash_qty=$shafi_po_prodWashQty_arr[$day_all][$poid];
									$shafi_wash_month_wise_kpiArr[$comp_id][2][$day_all]['wash_prod']+=$mon_wash_qty;
								}
								$shafi_wash_month_wise_kpiArr[$comp_id][2][$day_all]['wash_plan']+=$shafi_po_planWashQty_arr[$day_all][$poid];
								//==========GMTS Fin Transfer ackl Rec Qty Prod and Fin Plan Qty==============
								$planFinQtycal=0; 
								$planFinQtycal=$shafipur_plan_fin_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
								if($planFinQtycal>0)
								{
									$mon_fin_qty=$shafi_po_prodFinQty_arr[$day_all][$poid];
									$shafi_fin_month_wise_kpiArr[$comp_id][2][$day_all]['fin_prod']+=$mon_fin_qty;
								}
								$shafi_fin_month_wise_kpiArr[$comp_id][2][$day_all]['fin_plan']+=$shafi_po_planFinQty_arr[$day_all][$poid];
							 
							 //***********===========Shafipur End===================********//shafi_po_prodFinQty_arr
						 }
					 //}
					
				}
		 }
	   }
	   //********************=================Month Wise yarn Start================**************** */
	   $shafi_month_wise_kpiArr=array();
	   foreach($shafi_yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $yarn_month_prod=$row['yarn_prod']; 
			  $yarn_month_plan=$row['yarn_plan'];
			   $yarn_month_kpi_per=$yarn_month_prod/$yarn_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($yarn_month_prod>0 && $yarn_month_plan>0)
					{
						if($yarn_month_kpi_per>100)  $yarn_month_kpi_per=100;
					$shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn']+=$yarn_month_kpi_per;
					}
					if($yarn_month_plan>0) 
					{
						$shafi_mon_grand_yarn_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
						$shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	   //****************Month wise Knitting======================================= */
	   foreach($shafi_knit_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $knit_month_prod=$row['knit_prod']; 
			  $knit_month_plan=$row['knit_plan'];
			   $knit_month_kpi_per=$knit_month_prod/$knit_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($knit_month_prod>0 && $knit_month_plan>0)
					{
						if($knit_month_kpi_per>100)  $knit_month_kpi_per=100;
					$shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit']+=$knit_month_kpi_per;
					}
					$yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];

					if($knit_month_plan>0)
					{
						$shafi_mon_grand_knit_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}

					if($knit_month_plan>0 && !$yarn_plan_day_chk)
					{
						$shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	   //****************Month wise Dyeing======================================= */
	   foreach($shafi_dyeing_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $dyeing_month_prod=$row['dyeing_prod']; 
			  $dyeing_month_plan=$row['dyeing_plan'];
			   $dyeing_month_kpi_per=$dyeing_month_prod/$dyeing_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($dyeing_month_plan>0 && $dyeing_month_prod>0)
					{
						if($dyeing_month_kpi_per>100)  $dyeing_month_kpi_per=100;
					$shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing']+=$dyeing_month_kpi_per;
					}
					$yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
					$knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];

					if($dyeing_month_plan>0)
					{
						$shafi_mon_grand_dyeing_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}

					if($dyeing_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk) )
					{
						
						$shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	   //****************Month wise Aop======================================= */
	   foreach($shafi_aop_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $aop_month_prod=$row['aop_prod']; 
			  $aop_month_plan=$row['aop_plan'];
			   $aop_month_kpi_per=$aop_month_prod/$aop_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($aop_month_plan>0 && $aop_month_prod>0)
					{
						if($aop_month_kpi_per>100)  $aop_month_kpi_per=100;
					$shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop']+=$aop_month_kpi_per;
					}
					$yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
					$knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
					$dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
					
					if($aop_month_plan>0)
					{
						$shafi_mon_grand_aop_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}
					if($aop_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk) )
					{
						
						$shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	   //****************Month wise Print======================================= */
	   foreach($shafi_print_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	   {
	   foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	   {
		   foreach($LocData as $day_key=>$row)
		   {
			  $print_month_prod=$row['print_prod']; 
			  $print_month_plan=$row['print_plan'];
			   $print_month_kpi_per=$print_month_prod/$print_month_plan*100; 
			   	$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				$today=strtotime($today_date);
				$day_chk=strtotime($day_key);
				if($day_chk<=$today)// as on today
				{
					if($print_month_plan>0 && $print_month_prod>0)
					{
						if($print_month_kpi_per>100)  $print_month_kpi_per=100;
					$shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print']+=$print_month_kpi_per;
					}
					$yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
					$knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
					$dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
					$aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];

					if($print_month_plan>0)
					{
						$shafi_mon_grand_print_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}
					if($print_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk))
					{
						
						$shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					}
				}
		   	  }
	   		}
	   }
	    //****************Month wise Embrodiory======================================= */
		foreach($shafi_embr_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
		foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
		{
			foreach($LocData as $day_key=>$row)
			{
			   $emb_month_prod=$row['emb_prod']; 
			   $emb_month_plan=$row['emb_plan'];
				$emb_month_kpi_per=$emb_month_prod/$emb_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 if($day_chk<=$today)// as on today
				 {
					 if($emb_month_plan>0 && $emb_month_prod>0)
					 {
						 if($emb_month_kpi_per>100)  $emb_month_kpi_per=100;
					 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb']+=$emb_month_kpi_per;
					 }
					$yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
					$knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
					$dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
					$aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
					$print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];

					if($emb_month_plan>0)
					{
						$shafi_mon_grand_emb_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}

					 if($emb_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk ))
					 {
						
						 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				   }
				  }
				}
		}
		//****************Month wise Wash======================================= */
		foreach($shafi_wash_month_wise_kpiArr as $comp_id=>$comData)  
		{
		foreach($comData as $loc_id=>$LocData)  
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
				 if($day_chk<=$today)// as on today
				 {
					 if($wash_month_plan>0 && $wash_month_prod>0)
					 {
						 if($wash_month_kpi_per>100)  $wash_month_kpi_per=100;
					 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash']+=$wash_month_kpi_per;
					 }
					 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
					$knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
					$dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
					$aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
					$print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];
					$emb_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb_plan'];

					if($wash_month_plan>0)
					{
						$shafi_mon_grand_wash_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}

					 if($wash_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk  && !$emb_plan_day_chk) )
					 {
						
						 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				   }
				  }
				}
		}
		//****************Month wise Fin Transfer Acknoledgement======================================= */
		foreach($shafi_fin_month_wise_kpiArr as $comp_id=>$comData)  
		{
		foreach($comData as $loc_id=>$LocData)  
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
				 if($day_chk<=$today)// as on today
				 {
					 if($fin_month_plan>0 && $fin_month_prod>0)
					 {
						 if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
					 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin']+=$fin_month_kpi_per;
					 }
					 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
					 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
					 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
					 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
					 $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];
					 $emb_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb_plan'];
					 $wash_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash_plan'];

					 if($fin_month_plan>0)
					{
						$shafi_mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					}

					 if($fin_month_plan>0  && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk  && !$emb_plan_day_chk && !$wash_plan_day_chk) )
					 {
						
						 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
					 }
				   }
				  }
				}
		}
           // echo "<pre>";
	     //  print_r($shafi_month_wise_kpiArr);

		 //=========Month wise all event summation====================
		 asort($shafi_month_wise_kpiArr);
		foreach($shafi_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			foreach($comData as $unit_id=>$monData)  
			{
				foreach($monData as $day_key=>$pers)  
				{
					$event_yarn_count=0;$event_knit_count=0;$event_dyeing_count=0;$event_aop_count=0;$event_print_count=0;$event_emb_count=0;$event_wash_count=0;
					$event_yarn_count=$shafi_mon_grand_yarn_event_kpi_perArr[$comp_id][$unit_id][$day_key];//$mon_grand_qc_event_kpi_perArr
					$event_knit_count=$shafi_mon_grand_knit_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_dyeing_count=$shafi_mon_grand_dyeing_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_aop_count=$shafi_mon_grand_aop_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_print_count=$shafi_mon_grand_print_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_emb_count=$shafi_mon_grand_emb_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_fin_count=$shafi_mon_grand_fin_event_kpi_perArr[$comp_id][$unit_id][$day_key];
					$event_wash_count=$shafi_mon_grand_wash_event_kpi_perArr[$comp_id][$unit_id][$day_key];

					$shafi_event_mon_count= $event_yarn_count+$event_knit_count+$event_dyeing_count+$event_aop_count+$event_print_count+$event_fin_count+$event_wash_count+$event_emb_count;
					$shafi_all_kpi_per=$pers['yarn']+$pers['knit']+$pers['dyeing']+$pers['aop']+$pers['print']+$pers['emb']+$pers['fin']+$pers['wash'];
					if($day_key=='29-May-2023' )
					{
					    //  echo $day_key.'='.$pers['yarn'].'='.$pers['knit'].'='.$pers['dyeing'].'='.$pers['aop'].'='.$pers['print'].'='.$pers['emb'].'='.$pers['fin'].'='.$pers['wash'].'/'.$shafi_event_mon_count.'<br>';
					}
					// echo $day_key.'='.$event_del_count.'='.$per['deli'].'/'.$nay_event_mon_count.'<br>';
					$shafi_gbl_comp_avg_perArr[$comp_id][$unit_id][$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
					//$all_avg_perArr[$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
				}
			}
		}


		foreach($shafi_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
		foreach($comData as $unitid=>$monData)  
		{
			foreach($monData as $day_key=>$val)  
			{
				$monthYr=date("M-Y",strtotime($day_key));
				$yr_month=strtoupper($monthYr);
				 // echo $day_key.'='.$val.'<br>';
				$shafi_gbl_comp_mon_avg_perArr[$comp_id][$unitid][$yr_month]+=$val;
			}
		}
		}
		$shafi_comp_avg_perArr=array();
		foreach($shafi_gbl_comp_mon_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			foreach($comData as $unit_id=>$monData) 
			{
				foreach($monData as $monYr=>$per) 
				{
					$mon_dayscount=$shafi_gbl_num_of_plan_daysArr[$comp_id][$unit_id][$monYr]; 
					$avgKpi=$per/$mon_dayscount;
					$shafi_comp_avg_perArr[$comp_id][$unit_id]+=$avgKpi;
					//echo $avgKpi.'<br>';
				}
			}

		}
		//======================Marchandisiong Yarn Month Wise KPI===============
		 
	  	foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								//===========================Yarn allocation and Plan=============
								$yarn_planQtycal=0;
								$yarn_planQtycal=$mar_plan_qty_array[5][$day_all][$poid][$comp_id]['planQty'];//alloQty
								if($yarn_planQtycal>0)
								{
									$yarn_mon_alloc_qty=$mar_po_alloQty_arr[$day_all][$poid];
									$mar_yarn_month_wise_kpiArr[$comp_id][$day_all]['yarn_allo']+=$yarn_mon_alloc_qty;
								}
								$mar_yarn_month_wise_kpiArr[$comp_id][$day_all]['plan']+=$mar_po_planQty_arr[$day_all][$poid];
								//===========================Fin Fabric Production  and Plan=============
								$fin_fab_planQtycal=0;
								$fin_fab_planQtycal=$mar_fin_plan_recv_qty_array[5][$day_all][$poid][$comp_id]['plan'];//alloQty
								if($fin_fab_planQtycal>0)
								{
									$fin_mon_prod_qty=$mar_fin_fab_po_Qty_arr[$day_all][$poid];
									//echo $day_all.'X'.$fin_mon_prod_qty.'<br>';
									$mar_fin_month_wise_kpiArr[$comp_id][$day_all]['fin_recv']+=$fin_mon_prod_qty;
								}
								$mar_fin_month_wise_kpiArr[$comp_id][$day_all]['plan']+=$mar_fin_po_planQty_arr[$day_all][$poid];
								
								//=========================== Trim Sew Recv and Plan=============
								$trim_sew_planQtycal=0;
								$trim_sew_planQtycal=$mar_trim_sew_plan_recv_qty_array[5][$day_all][$poid][$comp_id]['plan'];
								if($trim_sew_planQtycal>0)
								{
									$sew_mon_prod_qty=$mar_trim_sew_po_Qty_arr[$day_all][$poid];
									$mar_trim_sew_month_wise_kpiArr[$comp_id][$day_all]['trim_recv']+=$sew_mon_prod_qty;
								}
								$mar_trim_sew_month_wise_kpiArr[$comp_id][$day_all]['plan']+=$mar_trim_sew_po_planQty_arr[$day_all][$poid];
								
								//=========================== Trim Fin Recv and Plan=============
								$trim_fin_planQtycal=0;
								$trim_fin_planQtycal=$mar_trim_fin_plan_recv_qty_array[5][$day_all][$poid][$comp_id]['plan'];
								if($trim_fin_planQtycal>0)
								{
									$fin_mon_prod_qty=$mar_trim_fin_po_Qty_arr[$day_all][$poid];
									$mar_trim_fin_month_wise_kpiArr[$comp_id][$day_all]['trim_recv']+=$fin_mon_prod_qty;
								}
								$mar_trim_fin_month_wise_kpiArr[$comp_id][$day_all]['plan']+=$mar_trim_fin_po_planQty_arr[$day_all][$poid];
							
						 }
					 //}
					
				}
		  }
	    }
		//echo "<pre>";
		//print_r($mar_fin_fab_po_Qty_arr);
		//****************Month wise Marchandising Yarn n allocation======================================= */
		foreach($mar_yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
		 
			foreach($comData as $day_key=>$row)
			{
			   $mar_yarn_month_prod=$row['yarn_allo']; 
			   $mar_yarn_month_plan=$row['plan'];
			   $yarn_month_kpi_per=0;
				$yarn_month_kpi_per=$mar_yarn_month_prod/$mar_yarn_month_plan*100; 
					$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 if($day_chk<=$today)// as on today
				 {
					 if($mar_yarn_month_prod>0 && $mar_yarn_month_plan>0)
					 {
						 if($yarn_month_kpi_per>100)  $yarn_month_kpi_per=100;
					 $mar_month_wise_kpiArr[$comp_id][$day_key]['yarn']+=$yarn_month_kpi_per;
					 }
				 
					 if($mar_yarn_month_plan>0)
					 {
						 $mar_mon_grand_yarn_event_kpi_perArr[$comp_id][$day_key]=1;
					 }
 
					 //if($knit_month_plan>0 && !$yarn_plan_day_chk)
					 if($mar_yarn_month_plan>0)
					 {
						 $mar_gbl_num_of_plan_daysArr[$comp_id][$yr_month]++;
					 }
				 }
			  }
			}
			//****************Month wise Marchandising Fin Fabric Prod and Plan======================================= */
		foreach($mar_fin_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			foreach($comData as $day_key=>$row)
			{
			   $mar_fin_fab_month_prod=$row['fin_recv']; 
			  // echo $day_key.'=='.$mar_fin_fab_month_prod.'<br>';
			   $mar_fin_fab_month_plan=$row['plan'];
			   $fin_fab_month_kpi_per=0;
				$fin_fab_month_kpi_per=$mar_fin_fab_month_prod/$mar_fin_fab_month_plan*100; 
					$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 if($day_chk<=$today)// as on today
				 {
					 if($mar_fin_fab_month_prod>0 && $mar_fin_fab_month_plan>0)
					 {
						 if($fin_fab_month_kpi_per>100)  $fin_fab_month_kpi_per=100;
					 $mar_month_wise_kpiArr[$comp_id][$day_key]['fin']+=$fin_fab_month_kpi_per;
					 }
					$mar_yarn_plan_day_chk= $mar_yarn_month_wise_kpiArr[$comp_id][$day_key]['plan'];
 
					 if($mar_fin_fab_month_plan>0)
					 {
						 $mar_mon_grand_fin_fab_event_kpi_perArr[$comp_id][$day_key]=1;
					 }
 
					 //if($knit_month_plan>0 && !$yarn_plan_day_chk)
					 if($mar_fin_fab_month_plan>0 && !$mar_yarn_plan_day_chk)
					 {
						 $mar_gbl_num_of_plan_daysArr[$comp_id][$yr_month]++;
					 }
				 }
			  }
			}
			//****************Month wise Marchandising Actual Po  and Plan======================================= */
		//  echo "<pre>";
 //print_r($mar_fin_month_wise_kpiArr);
		//==============Delivery Marchandising===========
		foreach($mar_acl_com_poIdArr as $comp_id=>$comData)
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
								//==========Marchandisiing GMTS Delivery Qty Prod and Actual Plan Qty==============
								$planAclQtycal=0;//
								$planAclQtycal=$mar_acl_delivery_qty_array[5][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
								$mon_delivery_qty=$mar_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
								//echo $day_all.'='.$mon_delivery_qty.'<br>';
								if($planAclQtycal>0 && $mon_delivery_qty>0)
								{
									$mon_del_qty=$mon_delivery_qty;
									$mar_delivery_month_wise_kpiArr[$comp_id][$day_all]['del_prod']+=$mon_delivery_qty;
								}
								$mar_delivery_month_wise_kpiArr[$comp_id][$day_all]['acl_plan']+=$planAclQtycal;
							} 
							 //***********=========== Marchandisiing End===================********//
				}
			}
		  }
	    }
		// ==============Marchandising Actual PO and Plan=========
		foreach($mar_delivery_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			foreach($comData as $day_key=>$row)
			{
			   $mar_del_month_prod=$row['del_prod']; 
			   $mar_del_month_plan=$row['acl_plan'];
			   $fin_fab_month_kpi_per=0;
				$del_month_kpi_per=$mar_del_month_prod/$mar_del_month_plan*100; 
					$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 if($day_chk<=$today)// as on today
				 {
					 if($mar_del_month_prod>0 && $mar_del_month_plan>0)
					 {
						 if($del_month_kpi_per>100)  $del_month_kpi_per=100;
					 $mar_month_wise_kpiArr[$comp_id][$day_key]['deli']+=$del_month_kpi_per;
					 }
					 $mar_yarn_plan_day_chk= $mar_yarn_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					$mar_fin_plan_day_chk= $mar_fin_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					 if($mar_del_month_plan>0)
					 {
						 $mar_mon_grand_deli_event_kpi_perArr[$comp_id][$day_key]=1;
					 }
					 //if($knit_month_plan>0 && !$yarn_plan_day_chk)
					 if($mar_del_month_plan>0 && !$mar_yarn_plan_day_chk && !$mar_fin_plan_day_chk)
					 {
						 $mar_gbl_num_of_plan_daysArr[$comp_id][$yr_month]++;
					 }
				 }
			  }
			}
			// ==============Marchandising Trim Sew PO and Plan=========
		foreach($mar_trim_sew_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			foreach($comData as $day_key=>$row)
			{
			   $mar_trim_month_prod=$row['trim_recv']; 
			   $mar_trim_month_plan=$row['plan'];
			   $sew_month_kpi_per=0;
				$sew_month_kpi_per=$mar_trim_month_prod/$mar_trim_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 if($day_chk<=$today)// as on today
				 {
					 if($mar_trim_month_prod>0 && $mar_trim_month_plan>0)
					 {
						 if($sew_month_kpi_per>100)  $sew_month_kpi_per=100;
					 $mar_month_wise_kpiArr[$comp_id][$day_key]['trim_sew']+=$sew_month_kpi_per;
					 }
					 $mar_yarn_plan_day_chk= $mar_yarn_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					$mar_fin_plan_day_chk= $mar_fin_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					$mar_del_plan_day_chk= $mar_delivery_month_wise_kpiArr[$comp_id][$day_key]['acl_plan'];
					 if($mar_trim_month_plan>0)
					 {
						 $mar_mon_grand_trim_sew_event_kpi_perArr[$comp_id][$day_key]=1;
					 }
					 if($mar_trim_month_plan>0 && !$mar_yarn_plan_day_chk && !$mar_fin_plan_day_chk  && !$mar_del_plan_day_chk)
					 {
						 $mar_gbl_num_of_plan_daysArr[$comp_id][$yr_month]++;
					 }
				 }
			  }
			}
			// ==============Marchandising Trim Fin PO and Plan=========
		foreach($mar_trim_fin_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
		{
			foreach($comData as $day_key=>$row)
			{
			   $mar_trim_fin_month_prod=$row['trim_recv']; 
			   $mar_trim_fin_month_plan=$row['plan'];
			   $fin_month_kpi_per=0;
				$fin_month_kpi_per=$mar_trim_fin_month_prod/$mar_trim_fin_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
				 $yr_month=strtoupper($monthYr);
				 $today=strtotime($today_date);
				 $day_chk=strtotime($day_key);
				 if($day_chk<=$today)// as on today
				 {
					 if($mar_trim_fin_month_prod>0 && $mar_trim_fin_month_plan>0)
					 {
						 if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
					 $mar_month_wise_kpiArr[$comp_id][$day_key]['trim_fin']+=$fin_month_kpi_per;
					 }
					 $mar_yarn_plan_day_chk= $mar_yarn_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					$mar_fin_plan_day_chk= $mar_fin_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					$mar_del_plan_day_chk= $mar_delivery_month_wise_kpiArr[$comp_id][$day_key]['acl_plan'];
					$mar_sew_plan_day_chk= $mar_trim_sew_month_wise_kpiArr[$comp_id][$day_key]['plan'];
					 if($mar_trim_fin_month_plan>0)
					 {
						 $mar_mon_grand_trim_fin_event_kpi_perArr[$comp_id][$day_key]=1;
					 }
					 if($mar_trim_fin_month_plan>0 && !$mar_yarn_plan_day_chk && !$mar_fin_plan_day_chk  && !$mar_del_plan_day_chk  && !$mar_sew_plan_day_chk)
					 {
						 $mar_gbl_num_of_plan_daysArr[$comp_id][$yr_month]++;
					 }
				 }
			  }
			}
			 //=========Month wise all event summation====================
			 asort($mar_month_wise_kpiArr);
			 foreach($mar_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
			 {
				foreach($comData as $day_key=>$pers)  
				{
					$event_yarn_count=0;$event_fin_fab_count=0;$event_deli_count=0;$event_trim_sew_count=0;$event_trim_fin_count=0;
					$event_yarn_count=$mar_mon_grand_yarn_event_kpi_perArr[$comp_id][$day_key];
					$event_fin_fab_count=$mar_mon_grand_fin_fab_event_kpi_perArr[$comp_id][$day_key];
					$event_deli_count=$mar_mon_grand_deli_event_kpi_perArr[$comp_id][$day_key];
					$event_trim_sew_count=$mar_mon_grand_trim_sew_event_kpi_perArr[$comp_id][$day_key];
					$event_trim_fin_count=$mar_mon_grand_trim_fin_event_kpi_perArr[$comp_id][$day_key];
					
					$mar_event_mon_count= $event_yarn_count+$event_fin_fab_count+$event_deli_count+$event_trim_sew_count+$event_trim_fin_count;
					$mar_all_kpi_per=$pers['yarn']+$pers['fin']+$pers['deli']+$pers['trim_sew']+$pers['trim_fin'];
					if($day_key=='29-May-2023' )
					{
						//  echo $day_key.'='.$pers['yarn'].'='.$pers['knit'].'='.$pers['dyeing'].'='.$pers['aop'].'='.$pers['print'].'='.$pers['emb'].'='.$pers['fin'].'='.$pers['wash'].'/'.$shafi_event_mon_count.'<br>';
					}
					// echo $day_key.'='.$event_del_count.'='.$per['deli'].'/'.$nay_event_mon_count.'<br>';
					$mar_gbl_comp_avg_perArr[$comp_id][$day_key]+=$mar_all_kpi_per/$mar_event_mon_count;
					//$all_avg_perArr[$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
				}
			 }
			 foreach($mar_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
			 {
				 foreach($comData as $day_key=>$val)  
				 {
					 $monthYr=date("M-Y",strtotime($day_key));
					 $yr_month=strtoupper($monthYr);
					  // echo $day_key.'='.$val.'<br>';
					 $mar_gbl_comp_mon_avg_perArr[$comp_id][$yr_month]+=$val;
				 }
			 }
			 $mar_comp_avg_perArr=array();
			 foreach($mar_gbl_comp_mon_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
			 {
					 foreach($comData as $monYr=>$per) 
					 {
						 $mon_dayscount=$mar_gbl_num_of_plan_daysArr[$comp_id][$monYr]; 
						 $avgKpi=$per/$mon_dayscount;
						 $mar_comp_avg_perArr[$comp_id]+=$avgKpi;
						 //echo $avgKpi.'<br>';
					 }
				  
			 }
		
		//  echo "<pre>";
   //print_r($mar_gbl_comp_mon_avg_perArr);

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
       <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" cellspacing="0">
	        
	        <!--<tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title;?> </strong></td>
	        </tr>-->
            <tr>
            <thead>
             <th width="70">IR </th>
             <?	
			 ksort($mar_com_poIdArr);
			 ksort($com_poIdArr); ksort($Ash_com_poIdArr); ksort($jm_com_poIdArr);ksort($acl_jm_com_poIdArr);ksort($kal_po_shafipur_poIdArr);//
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
				foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								$po_planQty_tillCalculteArr[$day_count]+=$mar_po_planQty_arr[$day_count][$poid];    
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$mar_po_planQty_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till Yarn Plan</b></td>
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
			  foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								//$planQtycal=0;
								//$planQtycal=$plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								//$alloQty=0;
								//$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								
								$alloQty_cal=0;
								if($mar_po_planQty_arr[$day_count][$poid]!=0)   
								{
									$alloQty_cal=$mar_po_alloQty_arr[$day_count][$poid];
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
            <td><b>  Till Yarn</b></td>
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
            <tr bgcolor="#C99">
            <td> Yarn KPI Per</td>
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
									$Grand_yarn_event_kpi_perArr[$day_count]=1;
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
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								$po_planQty_tillCalculteArr[$day_count]+=$mar_fin_po_planQty_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$mar_fin_po_planQty_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till Fin.Fab Plan</b></td>
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
			  foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								if($mar_fin_po_planQty_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$mar_fin_fab_po_Qty_arr[$day_count][$poid];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$alloQty;  ?>">  <?=$alloQty_cal;  ?>  </td>
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
            <td> <b> Till Fin.Fab Prod</b></td>
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
					  <td width="80" title="">  <?=$po_alloQty_tillCalculteArr[$day_count]; ?> </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#C99">
            <td> Fin.Fab KPI Per</td>
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
									$Grand_fin_fab_event_kpi_perArr[$day_count]=1;
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
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($mar_acl_com_poIdArr as $comp_id=>$comData)
				{
				foreach($comData as $poid=>$poidData)
				  {
			     foreach($poidData as $aclpo_id=>$IR)
			     {
					$po_Str=explode("**",$IR);
					$IR_name=$po_Str[0];
						$acl_po_no=$po_Str[1];
					// $mar_acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
				if($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
               <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trplan_<? echo $p; ?>','<? echo $bgcolor; ?>')" id="trplan_<? echo $p; ?>"> 
            
             <td width="70" title="<?=$poid;?>"><?=$IR_name.'<br>'.$acl_po_no;?> </td>
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
								$po_planQty_tillCalculteArr[$day_count]+=$mar_acl_delivery_qty_array[5][$day_count][$poid][$aclpo_id][$comp_id]['acc_planQty'];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$mar_acl_delivery_qty_array[5][$day_count][$poid][$aclpo_id][$comp_id]['acc_planQty']; ?> </td>
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
				}
			//PO end
			?>
            <tr bgcolor="#999966">
            <td><b> Till Deli Plan</b></td>
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
			 foreach($mar_acl_com_poIdArr as $comp_id=>$comData)
				{
				foreach($comData as $poid=>$poidData)
				  {
			     foreach($poidData as $aclpo_id=>$IR)
			     {
					$po_Str=explode("**",$IR);
					$IR_name=$po_Str[0];
						$acl_po_no=$po_Str[1];
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
              
             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trallo_<? echo $a; ?>','<? echo $bgcolor; ?>')" id="trallo_<? echo $a; ?>"> 
            <td width="70" title="<?=$poid;?>"><?=$IR_name.'<br>'.$acl_po_no;?> </td>
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
								$planQtycal=0;$alloQty_cal=0;
								$planQtycal=$plan_qty_array[1][$day_count][$poid][$comp_id]['planQty'];//alloQty
								$alloQty=0;$acc_planQty_cal=0;
								$alloQty=$plan_qty_array[1][$day_count][$poid][$comp_id]['alloQty'];
								
								$acc_planQty_cal=$mar_acl_delivery_qty_array[5][$day_count][$poid][$aclpo_id][$comp_id]['acc_planQty'];
								if($acc_planQty_cal!=0)
								{
									$alloQty_cal=$mar_po_prodDelQty_arr[$day_count][$poid][$aclpo_id];
								}
								
								$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
								 
				 ?>
					  <td width="80" title="<?=$acc_planQty_cal;  ?>">  <?=$alloQty_cal;  ?>  </td>
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
					  <td width="80" title="">  <?=$po_alloQty_tillCalculteArr[$day_count]; ?>  </td>
             <?
						 }
					 }
				}
			 ?>
            
            </tr>
            <tr bgcolor="#C99">
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
									$Grand_deli_event_kpi_perArr[$day_count]=1;
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
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								$po_planQty_tillCalculteArr[$day_count]+=$mar_trim_sew_po_planQty_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$mar_trim_sew_po_planQty_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till TrimSew Plan</b></td>
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
					  <td width="80" title="">  <?=$po_planQty_tillCalculteArr[$day_count];  ?>  </td>
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
			  foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								if($mar_trim_sew_po_planQty_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$mar_trim_sew_po_Qty_arr[$day_count][$poid];
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
            <td> <b> Till TrimSew</b></td>
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
            <tr bgcolor="#C99">
           
			<td> Sew KPI Per</td>
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
									
									$Grand_sew_event_kpi_perArr[$day_count]=1;
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
        <table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" cellspacing="0">
	    
            
            <? 
				$po_planQty_tillCalculteArr=array();
				$p=1;
				foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								$po_planQty_tillCalculteArr[$day_count]+=$mar_trim_fin_po_planQty_arr[$day_count][$poid];
				 ?>
					 		 <td width="80"  title=" <?=$planQty_cal;  ?>"> <?=$mar_trim_fin_po_planQty_arr[$day_count][$poid]; ?> </td>
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
            <td><b> Till TrimFin Plan</b></td>
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
			  foreach($mar_com_poIdArr as $comp_id=>$comData)
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
								if($mar_trim_fin_po_planQty_arr[$day_count][$poid]!=0)
								{
									$alloQty_cal=$mar_trim_fin_po_Qty_arr[$day_count][$poid];
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
            <td> <b> Till TrimFin Prod</b></td>
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
            <tr bgcolor="#C99">
           
			<td> TrimFin KPI Per</td>
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
									$Grand_fin_event_kpi_perArr[$day_count]=1;
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
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width2;?>" cellpadding="0" cellspacing="0">    
		<? 
			$po_planQty_tillCalculteArr=array();
			$p=1;
			foreach($mar_com_poIdArr as $comp_id=>$comData)
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
							$po_planQty_tillCalculteArr[$day_count]+=$shafi_po_planEmbQty_arr[$day_count][$poid];
			 ?>
						  <td width="80"  title=" <?=$planQty_cal;  ?>"> <? //$shafi_po_planEmbQty_arr[$day_count][$poid]; ?> </td>
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
		<td><b> Till PP Plan</b></td>
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
				  <td width="80" title=""> <b><? //$po_planQty_tillCalculteArr[$day_count];  ?> </b></td>
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
		  foreach($mar_com_poIdArr as $comp_id=>$comData)
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
							if($shafi_po_planEmbQty_arr[$day_count][$poid]!=0)
							{
								//$alloQty_cal=$shafi_po_prodEmbQty_arr[$day_count][$poid];
							}
							
							//$po_alloQty_tillCalculteArr[$day_count]+=$alloQty_cal;
							 
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
		<td> <b> Till PP Prod</b></td>
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
				  <td width="80" title=""> <b><?//$po_alloQty_tillCalculteArr[$day_count]; ?></b> </td>
		 <?
					 }
				 }
			}
		 ?>
		
		</tr>
		<tr bgcolor="#C99">
	   
		<td> PP KPI Per</td>
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
								//$Grand_company_kpi_perArr[$day_count]+=$company_kpi_per;
								
							}
							if($po_planQty_tillCalculteArr[$day_count]>0)
							{
								
								//$global_plan_day_chkArr[$day_count]+=1;
								//$Grand_sample_event_kpi_perArr[$day_count]=1; 
							}

							 
			 ?>
				  <td width="80" title="Wash Prod/Plan*100"> <b><?//fn_number_format($company_kpi_per,2); ?> </b></td>
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
							   $tot_yarn= $Grand_yarn_event_kpi_perArr[$day_count];
							   $tot_fin_fab= $Grand_fin_fab_event_kpi_perArr[$day_count];
							   $tot_deli= $Grand_deli_event_kpi_perArr[$day_count];
							   $tot_sew= $Grand_sew_event_kpi_perArr[$day_count];
							   $tot_fin= $Grand_fin_event_kpi_perArr[$day_count];
							   $tot_pp= $Grand_sample_event_kpi_perArr[$day_count]; 
							  
							   $tot_grnd=$tot_yarn+$tot_fin_fab+$tot_deli+$tot_sew+$tot_fin+$tot_pp;
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
	 	
	<br>
	 
          
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
								$tot_ash_com_avg_per=$yarn_com_avg_per=$Ash_com_avg_per=$rat_Ash_com_avg_per=0;$tot_ash_com_avg_per=0;
								$avg_month_wise_kpi_Tejgaon_count=0;$tot_avg_month_wise_kpi_per=0;$ash_avg_month_wise_kpi_count=$ash_tot_avg_month_wise_kpi_per=0;
								$rat_avg_month_wise_kpi_count=$rat_tot_avg_month_wise_kpi_per=0;$jm_nay_avg_month_wise_kpi_count=$jm_nay_tot_avg_month_wise_kpi_per=0;
								$kal_shafi_avg_month_wise_kpi_count=$kal_shafi_tot_avg_month_wise_kpi_per=0;$march_tot_avg_month_wise_kpi_per=$march_avg_month_wise_kpi_count=0;
								$jm_nay_fab_tot_avg_month_wise_kpi_per=$kal_shafi_avg_month_wise_kpi_count=0;
							foreach ($fiscalMonth_arr as $year_mon => $val) 
							{
								if($unit_id==1)
								{
									 
									$diff_days=$num_of_plan_days[$com_id][$unit_id][$year_mon];
									$avg_month_wise_kpi_per =$yarn_month_wise_kpiArr[$com_id][$unit_id][$year_mon]/$diff_days;
									if($avg_month_wise_kpi_per>0)
									{
										$avg_month_wise_kpi_Tejgaon_count+=1;
										$tot_avg_month_wise_kpi_per+=$avg_month_wise_kpi_per;
									
									}
									
								}
								 
								if($com_id==2 && $unit_id==3) //Ashulia Kal Location
								{
								//$Ash_com_avg_per=0;
							//	$Ash_com_avg_per=$ash_comp_avg_perArr[$com_id][3];	
								$mon_days_count=$gbl_num_of_plan_daysArr[$com_id][3][$year_mon];
								$ash_month_kpi_per =$ash_gbl_comp_mon_avg_perArr[$com_id][3][$year_mon]/$mon_days_count;
									if($ash_month_kpi_per>0)
									{
										$ash_avg_month_wise_kpi_count+=1;
										$ash_tot_avg_month_wise_kpi_per+=$ash_month_kpi_per;
									}
								
								}
								if($com_id==2 && $unit_id==4) //Ratanpur Kal Location
								{
									$rat_mon_days_count=$gbl_num_of_plan_daysArr[$com_id][4][$year_mon];
									$rat_month_kpi_per =$ash_gbl_comp_mon_avg_perArr[$com_id][4][$year_mon];
									//$diff_days=$qc_month_wise_kpi_per.'+'.$in_month_wise_kpi_per.'+'.$out_month_wise_kpi_per.'+'.$fin_month_wise_kpi_per;
									$rat_month_wise_kpi_per =$rat_month_kpi_per/$rat_mon_days_count;
									if($rat_month_wise_kpi_per>0)
									{
										$rat_avg_month_wise_kpi_count+=1;
										$rat_tot_avg_month_wise_kpi_per+=$rat_month_wise_kpi_per;
									}
								}
								if($com_id==1 && $unit_id==3) //JM Nayapara Location
								{
									$nay_mon_days_count=$nay_gbl_num_of_plan_daysArr[$com_id][6][$year_mon];
									$month_kpi_per =$nay_gbl_comp_mon_avg_perArr[$com_id][6][$year_mon];
									//echo $month_kpi_per.'='.$nay_mon_days_count.'<br>';
									$diff_days=$month_kpi_per.'/'.$nay_mon_days_count;
									$jm_nay_month_wise_kpi_per =$month_kpi_per/$nay_mon_days_count;

									if($jm_nay_month_wise_kpi_per>0)
									{
										$jm_nay_avg_month_wise_kpi_count+=1;
										$jm_nay_tot_avg_month_wise_kpi_per+=$jm_nay_month_wise_kpi_per;
									}

								}
								if($com_id==2 && $unit_id==2) //Shafipur Kal Location
								{
									$shafi_mon_days_count=$shafi_gbl_num_of_plan_daysArr[$com_id][2][$year_mon];
									$month_kpi_per =$shafi_gbl_comp_mon_avg_perArr[$com_id][2][$year_mon];
									// echo $month_kpi_per.'='.$shafi_mon_days_count.'<br>';
								//	$diff_days=$month_kpi_per.'/'.$shafi_mon_days_count;
									$shafi_month_wise_kpi_per =$month_kpi_per/$shafi_mon_days_count;

									if($shafi_month_wise_kpi_per>0)
									{
										$kal_shafi_avg_month_wise_kpi_count+=1;
										$kal_shafi_tot_avg_month_wise_kpi_per+=$shafi_month_wise_kpi_per;
									}

								}
								if($com_id==1 && $unit_id==2) //JM Nayaparar Fabric Location
								{
									$jm_nay_fab_mon_days_count=$shafi_gbl_num_of_plan_daysArr[$com_id][2][$year_mon];
									$month_kpi_per =$shafi_gbl_comp_mon_avg_perArr[$com_id][2][$year_mon];
									// echo $month_kpi_per.'='.$shafi_mon_days_count.'<br>';
									//$diff_days=$month_kpi_per.'/'.$shafi_mon_days_count;
									$jm_nfab_month_wise_kpi_per =$month_kpi_per/$jm_nay_fab_mon_days_count;

									if($jm_nfab_month_wise_kpi_per>0)
									{
										$jm_nay_fab_avg_month_wise_kpi_count+=1;
										$jm_nay_fab_tot_avg_month_wise_kpi_per+=$jm_nfab_month_wise_kpi_per;
									}
								}
								if($unit_id==5) //=======Marchandising========
								{
									$mar_mon_days_count=$mar_gbl_num_of_plan_daysArr[$com_id][$year_mon];
									$month_kpi_per =$mar_gbl_comp_mon_avg_perArr[$com_id][$year_mon];
								 
									//$diff_days=$month_kpi_per.'/'.$mar_mon_days_count;
									$mar_month_wise_kpi_per =$month_kpi_per/$mar_mon_days_count;

									if($mar_month_wise_kpi_per>0)
									{
										$march_avg_month_wise_kpi_count+=1;
										$march_tot_avg_month_wise_kpi_per+=$mar_month_wise_kpi_per;
									}

							    } 
								//===========Avg kpi end====

							}
							 
							
								if($unit_id==1)
								{
									$tejgaon_yarn_com_avg_per=$tot_avg_month_wise_kpi_per/$avg_month_wise_kpi_Tejgaon_count;
									// echo $tejgaon_yarn_com_avg_per.'='.$tot_avg_month_wise_kpi_per.'='.$avg_month_wise_kpi_Tejgaon_count.'=';
									$gbl_com_avg_per=$tot_avg_month_wise_kpi_per;
									$mon_count=$avg_month_wise_kpi_Tejgaon_count;
									$tot_ash_com_avg_per=$tejgaon_yarn_com_avg_per;
								}
								if($com_id==2 && $unit_id==3) //Ashulia Kal Location
								{
									$ash_yarn_com_avg_per=$ash_tot_avg_month_wise_kpi_per/$ash_avg_month_wise_kpi_count;
									// echo $tejgaon_yarn_com_avg_per.'='.$tot_avg_month_wise_kpi_per.'='.$avg_month_wise_kpi_Tejgaon_count.'=';
									$gbl_com_avg_per=$ash_tot_avg_month_wise_kpi_per;
									$mon_count=$ash_avg_month_wise_kpi_count;
									$tot_ash_com_avg_per=$ash_yarn_com_avg_per;
								}

								if($com_id==2 && $unit_id==4) //Ratanpur Kal Location
								{
									$rat_yarn_com_avg_per=$rat_tot_avg_month_wise_kpi_per/$rat_avg_month_wise_kpi_count;
									// echo $tejgaon_yarn_com_avg_per.'='.$tot_avg_month_wise_kpi_per.'='.$avg_month_wise_kpi_Tejgaon_count.'=';
									$gbl_com_avg_per=$rat_tot_avg_month_wise_kpi_per;
									$mon_count=$rat_avg_month_wise_kpi_count;
									$tot_ash_com_avg_per=$rat_yarn_com_avg_per;
								}
								if($com_id==1 && $unit_id==3) //JM Nayapara Location
								{
									$jm_nay_yarn_com_avg_per=$jm_nay_tot_avg_month_wise_kpi_per/$jm_nay_avg_month_wise_kpi_count;
									// echo $tejgaon_yarn_com_avg_per.'='.$tot_avg_month_wise_kpi_per.'='.$avg_month_wise_kpi_Tejgaon_count.'=';
									$gbl_com_avg_per=$jm_nay_tot_avg_month_wise_kpi_per;
									$mon_count=$jm_nay_avg_month_wise_kpi_count;
									$tot_ash_com_avg_per=$jm_nay_yarn_com_avg_per;
								}

								if($com_id==2 && $unit_id==2) //Shafipur Kal Location
								{
									$kal_shafi_yarn_com_avg_per=$kal_shafi_tot_avg_month_wise_kpi_per/$kal_shafi_avg_month_wise_kpi_count;
									$gbl_com_avg_per=$kal_shafi_tot_avg_month_wise_kpi_per;
									$mon_count=$kal_shafi_avg_month_wise_kpi_count;
									$tot_ash_com_avg_per=$kal_shafi_yarn_com_avg_per;
								}
								if($com_id==1 && $unit_id==2) //JM Nayaparar Fabric Location
								{
									$jmNayaFab_com_avg_per=$jm_nay_fab_tot_avg_month_wise_kpi_per/$jm_nay_fab_avg_month_wise_kpi_count;
									$gbl_com_avg_per=$jm_nay_fab_tot_avg_month_wise_kpi_per;
									$mon_count=$jm_nay_fab_avg_month_wise_kpi_count;
									$tot_ash_com_avg_per=$jmNayaFab_com_avg_per;
								}
								if($unit_id==5) //=======Marchandising========march_fab_tot_avg_month_wise_kpi_per
								{
									$march_com_avg_per=$march_tot_avg_month_wise_kpi_per/$march_avg_month_wise_kpi_count;
									$gbl_com_avg_per=$march_tot_avg_month_wise_kpi_per;
									$mon_count=$march_avg_month_wise_kpi_count;
									$tot_ash_com_avg_per=$march_com_avg_per;
								}
								 
								
								
								?>
								<td align="center" title="Tot KPI(<?=$gbl_com_avg_per;?>)/<?=$mon_count;?>"><?  if($tot_ash_com_avg_per>0) echo fn_number_format($tot_ash_com_avg_per,2).'%';else echo ""; ?></td>
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
									$month_wise_kpi_per =$yarn_month_wise_kpiArr[$com_id][$unit_id][$year_mon]/$diff_days;
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
								if($com_id==2 && $unit_id==2) //=======Kal Shafipur========
								{
									$shafi_mon_days_count=$shafi_gbl_num_of_plan_daysArr[$com_id][2][$year_mon];
									$month_kpi_per =$shafi_gbl_comp_mon_avg_perArr[$com_id][2][$year_mon];
									// echo $month_kpi_per.'='.$shafi_mon_days_count.'<br>';
									$diff_days=$month_kpi_per.'/'.$shafi_mon_days_count;
									$month_wise_kpi_per =$month_kpi_per/$shafi_mon_days_count;
							    } 
								if($com_id==1 && $unit_id==2) //=======Jm Nayapara Fabric========
								{
									$shafi_mon_days_count=$shafi_gbl_num_of_plan_daysArr[$com_id][2][$year_mon];
									$month_kpi_per =$shafi_gbl_comp_mon_avg_perArr[$com_id][2][$year_mon];
									// echo $month_kpi_per.'='.$shafi_mon_days_count.'<br>';
									$diff_days=$month_kpi_per.'/'.$shafi_mon_days_count;
									$month_wise_kpi_per =$month_kpi_per/$shafi_mon_days_count;
							    } 

								if($unit_id==5) //=======Marchandising========
								{
									$mar_mon_days_count=$mar_gbl_num_of_plan_daysArr[$com_id][$year_mon];
									$month_kpi_per =$mar_gbl_comp_mon_avg_perArr[$com_id][$year_mon];
									 // echo $month_kpi_per.'='.$mar_mon_days_count.'<br>';
									$diff_days=$month_kpi_per.'/'.$mar_mon_days_count;
									$month_wise_kpi_per =$month_kpi_per/$mar_mon_days_count;
							    } 


								// $mar_gbl_comp_mon_avg_perArr[$comp_id][$yr_month]+=$val;

								//shafi_gbl_comp_mon_avg_perArr
								 
							   
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

	 $sql_actual_po_plan_kal="SELECT c.id as acul_poid,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c where a.id=b.job_id and b.id=c.po_break_down_id   and a.location_name=$unitid    and c.acc_po_qty>0 and  b.status_active=1 and b.is_deleted=0   and b.id in(20328,20329,20330) and c.acc_ship_date between '$startDate' and '$endDate' $company_conds  order by acul_poid, PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_plan_result_kal = sql_select($sql_actual_po_plan_kal);
	foreach($sql_actual_po_plan_result_kal  as $val)
	{
		$kal_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		//$jm_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
	}

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 17, $kal_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=17
	$sql_actual_qty_po_plan_kal="SELECT c.id as ACUL_POID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.ACC_PO_NO, c.acc_po_qty as PLAN_QTY,c.ACC_SHIP_DATE as PLAN_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, wo_po_acc_po_info c, gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id  and a.location_name=$unitid  and c.acc_po_qty>0 and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(17) and g.entry_form=89 and  b.status_active=1 and b.is_deleted=0  and b.id in(20328,20329,20330)  and c.ACC_SHIP_DATE <= '$endDate' $company_conds order by  PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_qty_plan_result_kal = sql_select($sql_actual_qty_po_plan_kal);
	foreach($sql_actual_po_qty_plan_result_kal  as $val)
	{
		$plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
		$loc_id=$val['LOCATION_NAME']; 
		if($loc_id==3)
		{
			$ash_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
			$ash_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];

			$ash_acl_delivery_qty_array[$loc_id][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
			$rat_acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
			$rat_acl_kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
			$loc_acl_po_noArr[$val['POID']]=$loc_id;
		}
		else{
			$rat_actual_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACUL_POID'].',';
			$rat_actual_plan_qty_array[4][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
			
			$rat_acl_delivery_qty_array[4][$plandate][$val['POID']][$val['ACUL_POID']][$val['COMPANY_ID']]['acc_planQty']+=$val['PLAN_QTY'];
			$rat_acl_po_noArr[$val['ACUL_POID']]=$val['ACC_PO_NO'];
			$rat_acl_kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
			$loc_acl_po_noArr[$val['POID']]=$loc_id;
		}
		 
	}
  $sql_actual_po_delivery_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.ex_fact_qty, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 pro_ex_factory_mst c,pro_ex_factory_actual_po_details d  where a.id=b.job_id and b.id=c.po_break_down_id
  and c.id=d.mst_id   and a.location_name=$unitid  and d.ex_fact_qty>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
   and b.id in(20328,20329,20330) and c.ex_factory_date between '$startDate' and '$endDate'  $company_conds order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_result_kal = sql_select($sql_actual_po_delivery_kal);
	foreach($sql_actual_po_delivery_result_kal  as $val)
	{
		$kal_main_plan_acl_poIdArr[$val['POID']]=$val['POID'];
		$kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 18, $kal_main_plan_acl_poIdArr, $empty_arr);//PO ID Ref from=18

	  $sql_actual_po_qty_delivery_jm="SELECT d.actual_po_id as ACTUAL_PO_ID,a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING, d.ex_fact_qty as DEL_QTY,c.ex_factory_date as EX_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, pro_ex_factory_mst c,pro_ex_factory_actual_po_details d,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id  and c.id=d.mst_id and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(18) and g.entry_form=89  and a.location_name=$unitid  and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  and d.ex_fact_qty>0 and  b.status_active=1 and b.is_deleted=0   and b.id in(20328,20329,20330) and c.ex_factory_date<= '$endDate' $company_conds  order by c.ex_factory_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_actual_po_delivery_qty_result_jm = sql_select($sql_actual_po_qty_delivery_jm);
	foreach ($sql_actual_po_delivery_qty_result_jm as $val) 
	{
		$ex_date=date('d-M-Y',strtotime($val['EX_DATE']));
	  	$company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		 $loc_id=$val['LOCATION_NAME'];
		 if($loc_id==3) //
		 {
			$ash_acl_delivery_qty_array[$loc_id][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$ash_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$ash_actual_plan_qty_array[$loc_id][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
		 }
		 else{
			$rat_acl_delivery_qty_array[4][$ex_date][$val['POID']][$val['ACTUAL_PO_ID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$rat_actual_plan_qty_array[4][$ex_date][$val['POID']][$val['COMPANY_ID']]['prod_del']+=$val['DEL_QTY'];
			$rat_actual_plan_qty_array[4][$ex_date][$val['POID']][$val['COMPANY_ID']]['acul_poid'].=$val['ACTUAL_PO_ID'].',';
		 }
	
		
	}
	
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
	ksort($ash_acl_delivery_qty_array);
	
	
 
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
							 //****************============ashulia Acl Plan Qty===============***************//
							 foreach($ash_acl_delivery_qty_array[$unitid][$newdate_ash] as $poid_del=>$poDataArr)
							 {
								foreach($poDataArr as $aclpoid=>$poData)
							    {
								 $nay_planQty_acl=$nay_prod_DelQty=0;	
								 //$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']
								 $nay_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
								
								 $nay_prod_DelQty=$poData[$com_id]['prod_del'];
								  $acul_poid=rtrim($poData[$com_id]['acul_poid'],',');
								  //echo $newdate_ash.'='.$nay_prod_DelQty.'<br>';
								 // $acul_poidArr=array_unique(explode(",",$acul_poid));
								 // asort($acul_poidArr);
								 if($nay_planQty_acl>0)
								 {
									 $ash_actual_po_planQty_arr[$newdate_ash][$poid_del]=$nay_planQty_acl;
								 }
								 
									$nay_prod_DelQty=$nay_prod_DelQty;//$nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del'];
									 if($nay_prod_DelQty=='') $nay_prod_DelQty=0;
									 if($nay_planQty_acl>0 || $nay_prod_DelQty>0)
									 {
									 	if($nay_planQty_acl>0 && $nay_prod_DelQty==0)
										{
											
										 $ash_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$ash_po_prodDelQty_arr[$ash_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $ash_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										}
										if($nay_planQty_acl==0 && $nay_prod_DelQty==0)
										{
										  $ash_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$ash_po_prodDelQty_arr[$ash_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $ash_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										   // echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
										}
										
										if($ash_acl_delivery_qty_array[3][$newdate_ash][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $nay_planQty_acl>0)
										{
										  $ash_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$nay_prod_DelQty+$ash_po_prodDelQty_arr[$ash_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										   $ash_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										}
									}
								 }
							}
							//****************============Rat Acl Plan Qty===============***************//
							foreach($rat_acl_delivery_qty_array[4][$newdate_ash] as $poid_del=>$poDataArr)
							{
							   foreach($poDataArr as $aclpoid=>$poData)
							   {
								$nay_planQty_acl=$nay_prod_DelQty=0;	
								//$nay_actual_plan_qty_array[$loc_id][$plandate][$val['POID']][$val['COMPANY_ID']]['acc_planQty']
								$nay_planQty_acl=$poData[$com_id]['acc_planQty'];//fin_planQty
							   
								$nay_prod_DelQty=$poData[$com_id]['prod_del'];
								 $acul_poid=rtrim($poData[$com_id]['acul_poid'],',');
								 //echo $newdate_ash_rat.'='.$nay_prod_DelQty.'<br>';
								 $acul_poidArr=array_unique(explode(",",$acul_poid));
								// asort($acul_poidArr);
								if($nay_planQty_acl>0)
								{
									$rat_actual_po_planQty_arr[$newdate_ash][$poid_del]=$nay_planQty_acl;
								}
								
								   $nay_prod_DelQty=$nay_prod_DelQty;//$nay_acl_delivery_qty_array[6][$newdate_nay][$poid_del][$aclpoid][$com_id]['prod_del'];
									if($nay_prod_DelQty=='') $nay_prod_DelQty=0;
									if($nay_planQty_acl>0 || $nay_prod_DelQty>0)
									{
										if($nay_planQty_acl>0 && $nay_prod_DelQty==0)
									   {
										   
										$rat_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$rat_po_prodDelQty_arr[$rat_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										 $rat_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
									   }
									   if($nay_planQty_acl==0 && $nay_prod_DelQty==0)
									   {
										 $rat_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$rat_po_prodDelQty_arr[$rat_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $rat_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
										  // echo $newdate_nay."=".$aclpoid."=". $nay_po_prodDelQty_arr[$newdate_nay][$poid_del][$aclpoid].'=A <br>';
									   }
									   
									   if($rat_acl_delivery_qty_array[4][$newdate_ash][$poid_del][$aclpoid][$com_id]['prod_del']!=0 || $nay_planQty_acl>0)
									   {
										 $rat_po_prodDelQty_arr[$newdate_ash][$poid_del][$aclpoid]=$nay_prod_DelQty+$rat_po_prodDelQty_arr[$rat_prev_prodDel_date[$poid_del][$aclpoid]][$poid_del][$aclpoid];
										  $rat_prev_prodDel_date[$poid_del][$aclpoid] = $newdate_ash;
									   }
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
	// print_r($ash_po_prodDelQty_arr);

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
	 //Actual PO & Delivery for Kal
	   //$rat_acl_kal_com_poIdArr[$val['COMPANY_ID']][$val['POID']][$val['ACUL_POID']].=$val['GROUPING'].'**'.$val['ACC_PO_NO'];
			//$loc_acl_po_noArr[$val['POID']]=$loc_id; 
			foreach($rat_acl_kal_com_poIdArr as $comp_id=>$comData)
			{
			 foreach($comData as $poid=>$IR_Data)
			 {
				foreach($IR_Data as $acl_poid=>$IR)
			   {
					 $loaction=0;
					$loaction=$loc_acl_po_noArr[$poid];
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
								if($loaction==3) // Ashulia===========  Start===================
								{
									 
									//==========GMTS Delivery Qty Prod and Actual Plan Qty==============
									 
									$ash_planAclQtycal=0;//
									$ash_planAclQtycal=$ash_acl_delivery_qty_array[$loaction][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
									$ash_mon_delivery_qty=$ash_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
									//echo $day_all.'='.$mon_delivery_qty.'<br>';
									if($ash_planAclQtycal>0 && $ash_mon_delivery_qty>0)
									{
									   // $mon_del_qty=$mon_delivery_qty;
										$ash_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['del_prod']+=$ash_mon_delivery_qty;
									}
									$ash_delivery_month_wise_kpiArr[$comp_id][$loaction][$day_all]['acl_plan']+=$ash_planAclQtycal;
								} 
								 //***********=========== Ashulia End===================********//
								 if($loaction==5) //========== Ratanpur===========  Start===================
								 {
									  
									 //==========GMTS Delivery Qty Prod and Actual Plan Qty==============
									 $loaction_rat=4;
									 $rat_planAclQtycal=0;//
									 $rat_planAclQtycal=$rat_acl_delivery_qty_array[$loaction_rat][$day_all][$poid][$acl_poid][$comp_id]['acc_planQty'];
									 $rat_mon_delivery_qty=$rat_po_prodDelQty_arr[$day_all][$poid][$acl_poid];
									 //echo 
									// $day_all.'='.$mon_delivery_qty.'<br>';
									 if($rat_planAclQtycal>0 && $rat_mon_delivery_qty>0)
									 {
										 //$mon_del_qty=$mon_delivery_qty;
										 $rat_delivery_month_wise_kpiArr[$comp_id][$loaction_rat][$day_all]['del_prod']+=$rat_mon_delivery_qty;
									 }
									 $rat_delivery_month_wise_kpiArr[$comp_id][$loaction_rat][$day_all]['acl_plan']+=$rat_planAclQtycal;
								 } 
								  //***********=========== Ratanpur End===================********//
							 }
					}
				}
			  }
		   }

	 //=========For Ashulia Location================
	// echo "<pre>";
	//  print_r($ash_delivery_month_wise_kpiArr);
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


		//=========For Ashulia **Gmts Delivery** Location================//
		foreach($ash_delivery_month_wise_kpiArr as $comp_id=>$comData)  
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
					 $ash_month_wise_kpiArr[$comp_id][$unitid][$day_key]['deli']+=$del_month_kpi_per;
					 }
					 $out_plan_day_chk= $out_month_wise_kpiArr[$comp_id][$day_key]['out_plan'];
					 $qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$day_key]['qc_plan'];
					 $in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$day_key]['in_plan'];
					 $fin_plan_day_chk= $fin_month_wise_kpiArr[$comp_id][$day_key]['fin_plan'];
					 
					 if($del_month_plan>0)
					 {
						//$ash_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
						$mon_grand_del_event_kpi_perArr[$comp_id][$unitid][$day_key]=1;
						
					 }
					if($del_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk))
					{
						 
						$gbl_num_of_plan_daysArr[$comp_id][$unitid][$yr_month]++;
					}
				 }
			  }
			}
		}
	//=========For Ratanpur **Gmts Delivery** Location================//
	//print_r($rat_delivery_month_wise_kpiArr);
	foreach($rat_delivery_month_wise_kpiArr as $comp_id=>$comData)  
	{
		foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
		{
		foreach($LocData as $day_key=>$row)
		{
			$del_month_prod=$del_month_plan=0;
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
					$ash_month_wise_kpiArr[$comp_id][$unitid][$day_key]['deli']+=$del_month_kpi_per;
					}
					$out_plan_day_chk= $out_month_wise_kpiArr[$comp_id][$day_key]['out_plan'];
					$qc_plan_day_chk= $qc_month_wise_kpiArr[$comp_id][$day_key]['qc_plan'];
					$in_plan_day_chk= $in_month_wise_kpiArr[$comp_id][$day_key]['in_plan'];
					$fin_plan_day_chk= $fin_month_wise_kpiArr[$comp_id][$day_key]['fin_plan'];
					
					if($del_month_plan>0)
					{
					//$ash_fin_num_of_plan_days[$comp_id][$loc_id][$yr_month]++;
					$mon_grand_del_event_kpi_perArr[$comp_id][$unitid][$day_key]=1;
					
					}
				if($del_month_plan>0 && (!$out_plan_day_chk && !$in_plan_day_chk && !$qc_plan_day_chk && !$fin_plan_day_chk))
				{
					$gbl_num_of_plan_daysArr[$comp_id][$unitid][$yr_month]++;
				}
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
		  $event_del_count=$mon_grand_del_event_kpi_perArr[$comp_id][$unitid][$day_key];
		  $event_mon_count= $event_qc_count+$event_in_count+$event_out_count+$event_fin_count+$event_del_count;
		  
		  $all_kpi_per=$per['qc']+$per['in']+$per['out']+$per['fin']+$per['deli'];
		   //  echo $day_key.'='.$all_kpi_per.'='.$per['deli'].'='.$event_mon_count.'<br>';
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
if($action=="report_generate_by_year_shafipur_kal")
{
	//Unit Shafipur/Jm Nayapara Fabric //Kal and Monthly Date Wise KPI PER%  
	$cbo_company_id = str_replace("'","",$company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$cbo_templete_id 	= str_replace("'","",$cbo_templete_id);
	$unit_id 			= str_replace("'","",$report_type);
	 
	$unitid=$unit_id;
	
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
	 if($cbo_company_id==1) $jm_loc_id_cond=" and a.LOCATION_NAME=6";
	 else  $jm_loc_id_cond="";
	
	// ========================= for Plan ======================
     //============******** Shafipur/Nayapara Fabric Start here*****===========================
		 //============************************JM *******************************************
		
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=89");
		 
	    $sql_po_plan="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, c.SOURCE_ID,c.UOM_ID,b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(52,60,61,63,267,268,73,90)   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337)   and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	 $sql_po_plan_result = sql_select($sql_po_plan);
	 foreach ($sql_po_plan_result as $val) 
	 {
		  
		 if($val['TASK_ID']==52) //Yarn Recv
		 {
				 //$plandate=strtotime($val['PLAN_DATE']);
				 $plan_yarn_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==60) //Knitting/Grey Recv
		 { 
				 $plan_knit_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==61) //Dyeing Prod
		 {
				 $plan_dyeing_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==63) //AOP Recv 
		 {
				 $plan_aop_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==267) //Print Recv 
		 {
				 $plan_print_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
		 if($val['TASK_ID']==268) //Embro Recv 
		 {
				 $plan_embro_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
		 if($val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //MFG Fin Recv 
		 {
				  
				 $plan_fin_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
		 if($val['COMPANY_ID']==2 && $val['TASK_ID']==90) //Wash Fin Recv 
		 {
				 $plan_wash_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
 
		 $company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		 
	 }
	 //print_r($plan_wash_shafipur_poIdArr);die;
	 //===============Yarn Recv Start for Shafipur==============

fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 20, $plan_yarn_shafipur_poIdArr, $empty_arr);//PO ID Ref from=20
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 21, $plan_knit_shafipur_poIdArr, $empty_arr);//PO ID Ref from=21
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 22, $plan_dyeing_shafipur_poIdArr, $empty_arr);//PO ID Ref from=22
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 23, $plan_aop_shafipur_poIdArr, $empty_arr);//PO ID Ref from=23
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 24, $plan_print_shafipur_poIdArr, $empty_arr);//PO ID Ref from=24
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 25, $plan_embro_shafipur_poIdArr, $empty_arr);//PO ID Ref from=25
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 26, $plan_fin_shafipur_poIdArr, $empty_arr);//PO ID Ref from=26
if($cbo_company_id==2)
{
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 27, $plan_wash_shafipur_poIdArr, $empty_arr);//PO ID Ref from=27
}




   $sql_po_plan_shafipur="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,c.SOURCE_ID,c.UOM_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
  
 (case when  c.task_id=52  and g.ref_from=20  then c.PLAN_QTY else 0 end) as SHAFI_YARN_PLAN_QTY,
 (case when  c.task_id=60  and g.ref_from=21  then c.PLAN_QTY else 0 end) as SHAFI_KNIT_PLAN_QTY,
 (case when  c.task_id=61 and g.ref_from=22  then c.PLAN_QTY else 0 end) as SHAFI_DYING_PLAN_QTY,
 (case when  c.task_id=63  and g.ref_from=23  then c.PLAN_QTY else 0 end) as SHAFI_AOP_PLAN_QTY,
 (case when  c.task_id=267  and g.ref_from=24  then c.PLAN_QTY else 0 end) as SHAFI_PRINT_PLAN_QTY,
 (case when  c.task_id=268  and g.ref_from=25  then c.PLAN_QTY else 0 end) as SHAFI_EMBR_PLAN_QTY,
 (case when  c.task_id=73 and c.source_id=1 and g.ref_from=26  then c.PLAN_QTY else 0 end) as SHAFI_FIN_PLAN_QTY,
 (case when  c.task_id=90  and g.ref_from=27  then c.PLAN_QTY else 0 end) as SHAFI_WASH_PLAN_QTY

from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id    and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(20,21,22,23,24,25,26,27) and c.task_id in(52,60,61,63,267,268,73,90) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $company_conds $jm_loc_id_cond and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc";
 //and b.id in(20325,20326,20327,20328,20329,20330)
  $sql_po_plan_result_shafipur = sql_select($sql_po_plan_shafipur);
  foreach ($sql_po_plan_result_shafipur as $val) 
  {
	  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
	  if($val['TASK_ID']==52) //Yarn
	  {
		$shafipur_plan_yarn_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_YARN_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==60) //Knit
	  {
		$shafipur_plan_knit_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_KNIT_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==61) //Dyeing
	  {
		$shafipur_plan_dyeing_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_DYING_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==63) //AOP
	  {
		$shafipur_plan_aop_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_AOP_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==267) //Print
	  {
		$shafipur_plan_print_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_PRINT_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==268) //Embro
	  {
		$shafipur_plan_embr_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_EMBR_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //Fin Gmt/Production,KG
	  {
		$shafipur_plan_fin_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_FIN_PLAN_QTY'];
	  }
	  if($val['COMPANY_ID']==2  && $val['TASK_ID']==90) //Wash
	  {
		$shafipur_plan_wash_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_WASH_PLAN_QTY'];
	  }
	
  }
  // print_r($shafipur_plan_fin_recv_qty_array);

     $sql_yarn_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
 and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
   and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1 and d.entry_form=1 and  c.BOOKING_NO like '%YDW%'  and c.transaction_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_yarn_recv_kal_result = sql_select($sql_yarn_recv_kal); 
	foreach($sql_yarn_recv_kal_result  as $val)
	{
		$kal_shafipur_poIdArr[$val['POID']]=$val['POID'];
		$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 28, $kal_shafipur_poIdArr, $empty_arr);//PO ID Ref from=27
	//print_r($kal_shafipur_poIdArr);

	    $sql_yarn_recv_kal_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
	inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(28) and g.entry_form=89 and d.entry_form=1
	and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
	  and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and  c.BOOKING_NO like '%YDW%'   and c.transaction_date <= '$endDate' $jm_loc_id_cond  $company_conds order by c.transaction_date asc";
	  //and b.id in(20325,20326,20327,20328,20329,20330)
	  
	   $sql_yarn_recv_kal_qty_result = sql_select($sql_yarn_recv_kal_qty);
	   foreach($sql_yarn_recv_kal_qty_result  as $val)
	   {
		$trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
		//echo $val['POID'].'='.$trans_date.'='.$val['QUANTITY'].'<br>';
		$shafipur_plan_yarn_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['y_recv']+=$val['QUANTITY'];
		//$shafipur_plan_yarn_recv_qty_array2[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['y_recv']+=$val['QUANTITY'];
		   
	   }
	   // print_r($shafipur_plan_yarn_recv_qty_array2);
// ======================Kntting/Grey Recv production=================
	     $sql_knit_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
	   inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
	   and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
		 and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(2,22) and c.transaction_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
		 
		  $sql_knit_recv_kal_result = sql_select($sql_knit_recv_kal); 
		  foreach($sql_knit_recv_kal_result  as $val)
		  {
			  $kal_knit_poIdArr[$val['POID']]=$val['POID'];
			  $kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			  
		  }
		  fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 29, $kal_knit_poIdArr, $empty_arr);//PO ID Ref from=29
		  //print_r($kal_shafipur_poIdArr);
	  
			$sql_knit_recv_kal_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
		  inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(29) and g.entry_form=89
		  and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
			and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(2,22)    and c.transaction_date <= '$endDate' $jm_loc_id_cond  $company_conds order by c.transaction_date asc"; 
			//and b.id in(20325,20326,20327,20328,20329,20330)
			
			 $sql_sql_knit_recv_kal_qty_result = sql_select($sql_knit_recv_kal_qty);
			 foreach($sql_sql_knit_recv_kal_qty_result  as $val)
			 {
			  $trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
			  $shafipur_plan_knit_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['knit_recv']+=$val['QUANTITY'];
			 }

			 //////=================Dyeing Prod=======================
			 
			   $sql_dyeing_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.BATCH_QNTY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			 pro_batch_create_dtls c,pro_fab_subprocess d  where a.id=b.job_id  and b.id=c.po_id and c.mst_id=d.batch_id
			 and c.BATCH_QNTY>0 and b.status_active=1 and d.load_unload_id=2 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
			   and b.id in(20328,20329,20330,20334,20335,20337) and d.batch_ext_no is null and d.entry_form in(35) and d.process_end_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by d.process_end_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
			   
				$sql_dyeing_kal_result = sql_select($sql_dyeing_kal); 
				foreach($sql_dyeing_kal_result  as $val)
				{
					$kal_dyeing_poIdArr[$val['POID']]=$val['POID'];
					$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				}
				 
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 30, $kal_dyeing_poIdArr, $empty_arr);//PO ID Ref from=30

				$sql_dyeing_qty_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.BATCH_QNTY,d.PROCESS_END_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				pro_batch_create_dtls c,pro_fab_subprocess d,gbl_temp_engine g   where a.id=b.job_id  and b.id=c.po_id and c.mst_id=d.batch_id and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(30) and g.entry_form=89
				and c.BATCH_QNTY>0 and b.status_active=1 and d.load_unload_id=2 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
				  and b.id in(20328,20329,20330,20334,20335,20337) and d.batch_ext_no is null and d.entry_form in(35)   and d.process_end_date <= '$endDate'  $company_conds $jm_loc_id_cond order by d.process_end_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
				  
				$sql_dyeing_kal_qty_result = sql_select($sql_dyeing_qty_kal); 
				foreach($sql_dyeing_kal_qty_result  as $val)
				{
				$dying_date=date('d-M-Y',strtotime($val['PROCESS_END_DATE']));
				$shafipur_plan_dyeing_recv_qty_array[2][$dying_date][$val['POID']][$val['COMPANY_ID']]['dyeing']+=$val['BATCH_QNTY'];
				}
				//inv_receive_mas_batchroll
				 //////=================Fabric Service Recv=======================

				$sql_fab_aop_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.BATCH_ISSUE_QTY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				inv_receive_mas_batchroll c,pro_grey_batch_dtls d  where a.id=b.job_id  and b.id=d.order_id and c.id=d.mst_id
				and d.BATCH_ISSUE_QTY>0 and b.status_active=1 and c.entry_form=92 and d.process_id=35 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
				and b.id in(20328,20329,20330,20334,20335,20337)   and c.receive_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.receive_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
				
				$sql_fab_aop_kal_result = sql_select($sql_fab_aop_kal); 
				foreach($sql_fab_aop_kal_result  as $val)
				{
					$kal_fab_aop_poIdArr[$val['POID']]=$val['POID'];
					$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				}
					
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 31, $kal_fab_aop_poIdArr, $empty_arr);//PO ID Ref from=30

				$sql_fab_aop_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.BATCH_ISSUE_QTY,c.RECEIVE_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				inv_receive_mas_batchroll c,pro_grey_batch_dtls d,gbl_temp_engine g where a.id=b.job_id  and b.id=d.order_id and c.id=d.mst_id  and b.id=g.ref_val and d.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(31) and g.entry_form=89
				and d.BATCH_ISSUE_QTY>0 and b.status_active=1 and c.entry_form=92 and d.process_id=35 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
					and b.id in(20328,20329,20330,20334,20335,20337)   and c.receive_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.receive_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
					
					$sql_fab_aop_kal_result = sql_select($sql_fab_aop_kal); 
					foreach($sql_fab_aop_kal_result  as $val)
					{
					$aop_date=date('d-M-Y',strtotime($val['RECEIVE_DATE']));
					$shafipur_plan_aop_recv_qty_array[2][$aop_date][$val['POID']][$val['COMPANY_ID']]['aop_recv']+=$val['BATCH_ISSUE_QTY'];
					}
			//=============Print Recv=======================
			  $sql_po_prod_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(3)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337)   and c.production_date between '$startDate' and '$endDate' and c.production_type in(3)  $company_conds $jm_loc_id_cond order by c.PRODUCTION_DATE asc"; //and a.location_name=3
			
			$sql_po_result_prod_kal = sql_select($sql_po_prod_kal);
			foreach ($sql_po_result_prod_kal as $val) 
			{
				//if($val['COMPANY_ID']==2) //Kal
				//{
				$kal_print_poIdArr[$val['POID']]=$val['POID'];
				$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				//}
			}
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 32, $kal_print_poIdArr, $empty_arr);//PO ID Ref from=14

			  $sql_po_prod_kal_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.EMBEL_NAME,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
			
			(case when  c.production_type=3 and c.embel_name=1  then c.PRODUCTION_QUANTITY else 0 end) as PRINT_PROD,
			(case when  c.production_type=3 and c.embel_name=2  then c.PRODUCTION_QUANTITY else 0 end) as EMBR_PROD,
			(case when  c.production_type=3 and c.embel_name=3  then c.PRODUCTION_QUANTITY else 0 end) as WASH_PROD
			
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
			and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(32) and g.entry_form=89 
			and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0 and c.production_type in(3) and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' $company_conds $jm_loc_id_cond order by c.PRODUCTION_DATE asc";
			$sql_po_result_prod_kal_curr = sql_select($sql_po_prod_kal_curr);
			foreach ($sql_po_result_prod_kal_curr as $val) 
			{
				
				$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
				$loc_id=$val['LOCATION_NAME'];
				$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
				 
				
				if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==1) // Print
				{
					$shafipur_plan_print_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_print']+=$val['PRINT_PROD'];

					//echo $val['PRINT_PROD'].'A=<br>';
				}
				if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==2)// Embro
				{
					$shafipur_plan_embr_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_embr']+=$val['EMBR_PROD'];
				}
			   
				if($val['COMPANY_ID']==2 && $val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==3)// Wash
				{
					$shafipur_plan_wash_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_wash']+=$val['WASH_PROD'];
				}
			}
			unset($sql_po_result_prod_kal_curr); 
		//	print_r($shafipur_plan_print_recv_qty_array);

			// Knit Fin Fab Transfer Ack===========
			// ======================Knit Fin Fab Transfer Ack=================
			if($cbo_company_id==2) $store_id_cond="8,20";
			else  $store_id_cond="32,52";

			 $sql_knit_fin_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.CONS_QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			inv_transaction c,inv_item_trans_acknowledgement d  where a.id=b.job_id and b.id=c.order_id   and c.mst_id=d.challan_id and d.store_id in($store_id_cond) and d.transfer_criteria = 2 and c.item_category = 2 and c.transaction_type = 5 and c.cons_quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337) and d.entry_form in(247) 
			and d.acknowledg_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by d.acknowledg_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
			  
			   $sql_knit_fin_recv_kal_result = sql_select($sql_knit_fin_recv_kal); 
			   foreach($sql_knit_fin_recv_kal_result  as $val)
			   {
				   $kal_fin_knit_poIdArr[$val['POID']]=$val['POID'];
				   $kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];   
			   }
			   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 33, $kal_fin_knit_poIdArr, $empty_arr);//PO ID Ref from=27
			   //print_r($kal_shafipur_poIdArr);
			$sql_knit_fin_recv_qty_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.CONS_QUANTITY, d.ACKNOWLEDG_DATE,b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			inv_transaction c,inv_item_trans_acknowledgement d,gbl_temp_engine g where a.id=b.job_id and b.id=c.order_id   and c.mst_id=d.challan_id and b.id=g.ref_val and c.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(33) and g.entry_form=89  and d.store_id in($store_id_cond) and d.transfer_criteria = 2 and c.item_category = 2 and c.transaction_type = 5 and c.cons_quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337) and d.entry_form in(247) 
			and d.acknowledg_date <= '$endDate' $company_conds $jm_loc_id_cond order by d.acknowledg_date asc";

			$sql_sql_knit_recv_kal_qty_result = sql_select($sql_knit_fin_recv_qty_kal);
			foreach($sql_sql_knit_recv_kal_qty_result  as $val) 
			{
			$trans_date=date('d-M-Y',strtotime($val['ACKNOWLEDG_DATE']));
			$shafipur_plan_fin_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['fin_recv']+=$val['CONS_QUANTITY'];
			}

	
	// echo "<pre>";
	// print_r($plan_qty_array);
	
	//===================Allocation Wise Calculation=======
	
	 
  // echo "<pre>";
//print_r($till_today_planArr);
 //ksort($till_today_AllQtyArr);
 // =============================Calculation of KPI============= //plan asy  allocatio ni  day dorbo
		$company_kip_cal_Arr=array();//$company_wise_arr=array();
		ksort($shafipur_plan_yarn_recv_qty_array);
		ksort($shafipur_plan_knit_recv_qty_array);
		ksort($shafipur_plan_dyeing_recv_qty_array);
		ksort($shafipur_plan_aop_recv_qty_array);
		ksort($shafipur_plan_print_recv_qty_array);
		ksort($shafipur_plan_embr_recv_qty_array);
		ksort($shafipur_plan_wash_recv_qty_array);
		ksort($shafipur_plan_fin_recv_qty_array);
		
		
	
		//unset($sql_po_plan_result);
		//unset($sql_po_allocate_result);
	 
	
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
					
						 
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_shafi = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_shafi = date('d-M-Y', strtotime("+1 day", strtotime($newdate_shafi)));
							}
							//===========Yarn Recv============
							foreach($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi] as $poid_rec=>$poData)
							{
								$sha_planQty=$yarn_sha_recv_Qty=0;
								$sha_planQty=$poData[$com_id]['plan'];
								$yarn_sha_recv_Qty=$poData[$com_id]['y_recv'];
								if($yarn_sha_recv_Qty=='') $yarn_sha_recv_Qty=0;
								//  echo $newdate_shafi.'='.$yarn_sha_recv_Qty.'<br>';
								if($sha_planQty>0)
								{ 
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planYarnQty_arr[$newdate_shafi][$poid_rec]=$sha_planQty+$shafi_po_planYarnQty_arr[$sha_prev_date_planYarn[$poid_rec]][$poid_rec];
									$sha_prev_date_planYarn[$poid_rec] = $newdate_shafi;
								}
								if($sha_planQty>0 || $yarn_sha_recv_Qty>0)
								{
									if($sha_planQty>0 && $yarn_sha_recv_Qty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planQty==0 && $yarn_sha_recv_Qty>0) 
									{
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$yarn_shafi_planQtycal=0;  
									$yarn_shafi_planQtycal=$shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['plan'];//Plan
									if($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['y_recv']!="" || $yarn_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$yarn_sha_recv_Qty+$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									}
								}
							} 
							//==========Knitting=========================
							foreach($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi] as $poid_k=>$poData)
							{
								$sha_planKnitQty=$sha_prodKnitQty=0;
								$sha_planKnitQty=$poData[$com_id]['plan'];
								$sha_prodKnitQty=$poData[$com_id]['knit_recv'];
								if($sha_prodKnitQty=='') $sha_prodKnitQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planKnitQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planKnitQty_arr[$newdate_shafi][$poid_k]=$sha_planKnitQty+$shafi_po_planKnitQty_arr[$sha_prev_date_planKnit[$poid_k]][$poid_k];
									$sha_prev_date_planKnit[$poid_k] = $newdate_shafi;
								}
								if($sha_planKnitQty>0 || $sha_prodKnitQty>0)
								{
									if($sha_planKnitQty>0 && $sha_prodKnitQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planKnitQty==0 && $sha_prodKnitQty>0) 
									{
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$shafi_planQtycal=0;
									$shafi_planQtycal=$shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['plan'];//Plan
									if($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['knit_recv']!="" || $shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$sha_prodKnitQty+$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									}
								}
							}
							//==========Dyeing=========================
							foreach($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi] as $poid_d=>$poData)
							{
								$sha_planDyingQty=$sha_prodDyeingQty=0;
								$sha_planDyingQty=$poData[$com_id]['plan'];
								$sha_prodDyeingQty=$poData[$com_id]['dyeing'];
								if($sha_prodDyeingQty=='') $sha_prodDyeingQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planDyingQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_planDyingQty+$shafi_po_planDyeingQty_arr[$sha_prev_date_planDye[$poid_d]][$poid_d];
									$sha_prev_date_planDye[$poid_d] = $newdate_shafi;
								}
								if($sha_planDyingQty>0 || $sha_prodDyeingQty>0)
								{
									if($sha_planDyingQty>0 && $sha_prodDyeingQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planDyingQty==0 && $sha_prodDyeingQty>0) 
									{
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$dye_shafi_planQtycal=0;
									$dye_shafi_planQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['plan'];//Plan
									if($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['dyeing']!="" || $dye_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_prodDyeingQty+$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									}
								}
							}
							//==========Aop Recv=========================
							foreach($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi] as $poid_a=>$poData)
							{
								$sha_planAopQty=$sha_prodAopQty=0;
								$sha_planAopQty=$poData[$com_id]['plan'];
								$sha_prodAopQty=$poData[$com_id]['aop_recv'];
								if($sha_prodAopQty=='') $sha_prodAopQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planAopQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planAopQty_arr[$newdate_shafi][$poid_a]=$sha_planAopQty+$shafi_po_planAopQty_arr[$sha_prev_date_planAop[$poid_a]][$poid_a];
									$sha_prev_date_planAop[$poid_a] = $newdate_shafi;
								}
								if($sha_planAopQty>0 || $sha_prodAopQty>0)
								{
									if($sha_planAopQty>0 && $sha_prodAopQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planAopQty==0 && $sha_prodAopQty>0) 
									{
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$aop_shafi_planQtycal=0;
									$aop_shafi_planQtycal=$shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['plan'];//Plan
									if($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['aop_recv']!="" || $aop_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$sha_prodAopQty+$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									}
								}
							}

							//==========Print Recv=========================
							foreach($shafipur_plan_print_recv_qty_array[2][$newdate_shafi] as $poid_pr=>$poData)
							{
								$sha_planPrintQty=$sha_prodPrintQty=0;
								$sha_planPrintQty=$poData[$com_id]['plan'];
								$sha_prodPrintQty=$poData[$com_id]['prod_print'];
								if($sha_prodPrintQty=='') $sha_prodPrintQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planPrintQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_planPrintQty+$shafi_po_planPrintQty_arr[$sha_prev_date_planPrint[$poid_pr]][$poid_pr];
									$sha_prev_date_planPrint[$poid_pr] = $newdate_shafi;
								}
								if($sha_planPrintQty>0 || $sha_prodPrintQty>0)
								{
									if($sha_planPrintQty>0 && $sha_prodPrintQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planPrintQty==0 && $sha_prodPrintQty>0) 
									{
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$print_shafi_planQtycal=0;
									$print_shafi_planQtycal=$shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['plan'];//Plan
									if($shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['prod_print']!="" || $print_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_prodPrintQty+$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									}
								}
							}
							//==========Embro Recv=========================
							foreach($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi] as $poid_er=>$poData)
							{
								$sha_planEmbQty=$sha_prodEmbQty=0;
								$sha_planEmbQty=$poData[$com_id]['plan'];
								$sha_prodEmbQty=$poData[$com_id]['prod_embr'];
								if($sha_prodEmbQty=='') $sha_prodEmbQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planEmbQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planEmbQty_arr[$newdate_shafi][$poid_er]=$sha_planEmbQty+$shafi_po_planEmbQty_arr[$sha_prev_date_planEmb[$poid_er]][$poid_er];
									$sha_prev_date_planEmb[$poid_er] = $newdate_shafi;
								}
								if($sha_planEmbQty>0 || $sha_prodEmbQty>0)
								{
									if($sha_planEmbQty>0 && $sha_prodEmbQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planEmbQty==0 && $sha_prodEmbQty>0) 
									{
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$emb_shafi_planQtycal=0;
									$emb_shafi_planQtycal=$shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['plan'];//Plan
									if($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['prod_embr']!="" || $emb_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$sha_prodEmbQty+$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									}
								}
							}
							//==========Wash Recv=========================
							foreach($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi] as $poid_wr=>$poData)
							{
								$sha_planWashQty=$sha_prodWashQty=0;
								$sha_planWashQty=$poData[$com_id]['plan'];
								$sha_prodWashQty=$poData[$com_id]['prod_wash'];
								if($sha_prodWashQty=='') $sha_prodWashQty=0;

								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planWashQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planWashQty_arr[$newdate_shafi][$poid_wr]=$sha_planWashQty+$shafi_po_planWashQty_arr[$sha_prev_date_planWash[$poid_wr]][$poid_wr];
									$sha_prev_date_planWash[$poid_wr] = $newdate_shafi;
								}
								if($sha_planWashQty>0 || $sha_prodWashQty>0)
								{
									if($sha_planWashQty>0 && $sha_prodWashQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planWashQty==0 && $sha_prodWashQty>0) 
									{
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$wash_shafi_planQtycal=0;
									$wash_shafi_planQtycal=$shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['plan'];//Plan
									if($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['prod_wash']!="" || $wash_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$sha_prodWashQty+$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
									}
								}
							}

							//==========Fin Fab Transfer Acknowlege Recv=========================
							foreach($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi] as $poid_ft=>$poData)
							{
								$sha_planFinQty=$sha_prodFinQty=0;
								$sha_planFinQty=$poData[$com_id]['plan'];
								$sha_prodFinQty=$poData[$com_id]['fin_recv'];
								if($sha_prodFinQty=='') $sha_prodFinQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planFinQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planFinQty_arr[$newdate_shafi][$poid_ft]=$sha_planFinQty+$shafi_po_planFinQty_arr[$sha_prev_date_planFin[$poid_ft]][$poid_ft];
									$sha_prev_date_planFin[$poid_ft] = $newdate_shafi;
								}
								if($sha_planFinQty>0 || $sha_prodFinQty>0)
								{
									if($sha_planFinQty>0 && $sha_prodFinQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planFinQty==0 && $sha_prodFinQty>0) 
									{
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$fin_shafi_planQtycal=0;
									$fin_shafi_planQtycal=$shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['plan'];//Plan
									if($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['fin_recv']!="" || $fin_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$sha_prodFinQty+$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
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
	  //====================Kal Shafipur Location=========================
	 ///************ */===============================Shafipur============================
	foreach($kal_po_shafipur_poIdArr as $comp_id=>$comData)
	{
	  foreach($comData as $poid=>$IR)
	  {
			 // $loaction=0;
			 //$loaction=$poId_loaction_Arr[$comp_id][$poid];
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
							 //==========Shafipur Location Prod and  Plan==============
							 //==========Shafipur Yarn Recv   and  Plan==============
							 $planYarnQtycal=0;
							 $planYarnQtycal=$shafipur_plan_yarn_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
							 if($planYarnQtycal>0)//shafi_po_prodYarnQty_arr
							 {
								 $mon_yarn_qty=$shafi_po_prodYarnQty_arr[$day_all][$poid];
								 //  echo $day_all.'='.$mon_yarn_qty.'<br>';
								 $shafi_yarn_month_wise_kpiArr[$comp_id][2][$day_all]['yarn_prod']+=$mon_yarn_qty;
							 }
							 $shafi_yarn_month_wise_kpiArr[$comp_id][2][$day_all]['yarn_plan']+=$shafi_po_planYarnQty_arr[$day_all][$poid];

							 //==========Knit Prod and Knit Plan==============
							 $planKnitQtycal=0;
							 $planKnitQtycal=$shafipur_plan_knit_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
							 if($planKnitQtycal>0)
							 {
								 $mon_knit_qty=$shafi_po_prodKnitQty_arr[$day_all][$poid];
								 $shafi_knit_month_wise_kpiArr[$comp_id][2][$day_all]['knit_prod']+=$mon_knit_qty;
							 }
							 $shafi_knit_month_wise_kpiArr[$comp_id][2][$day_all]['knit_plan']+=$shafi_po_planKnitQty_arr[$day_all][$poid];
							 //==========Dyeing Qty Prod and Dyeing Plan Qty==============
							 $planDyeingQtycal=0;
							 $planDyeingQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
							 if($planDyeingQtycal>0)
							 {
								 $mon_dyeing_qty=$shafi_po_prodDyeingQty_arr[$day_all][$poid];
								 $shafi_dyeing_month_wise_kpiArr[$comp_id][2][$day_all]['dyeing_prod']+=$mon_dyeing_qty;
							 }
							 $shafi_dyeing_month_wise_kpiArr[$comp_id][2][$day_all]['dyeing_plan']+=$shafi_po_planDyeingQty_arr[$day_all][$poid];
							 //==========GMTS Aop Rec Qty Prod and Aop Plan Qty==============
							 $planAopQtycal=0; 
							 $planAopQtycal=$shafipur_plan_aop_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planAopQtycal>0)
							 {
								 $mon_aop_qty=$shafi_po_prodAopQty_arr[$day_all][$poid];
								 $shafi_aop_month_wise_kpiArr[$comp_id][2][$day_all]['aop_prod']+=$mon_aop_qty;
							 }
							 $shafi_aop_month_wise_kpiArr[$comp_id][2][$day_all]['aop_plan']+=$shafi_po_planAopQty_arr[$day_all][$poid];
							 //==========GMTS Print Rec Qty Prod and Print Plan Qty==============
							 $planPrintQtycal=0; 
							 $planPrintQtycal=$shafipur_plan_print_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planPrintQtycal>0)
							 {
								 $mon_print_qty=$shafi_po_prodPrintQty_arr[$day_all][$poid];
								 $shafi_print_month_wise_kpiArr[$comp_id][2][$day_all]['print_prod']+=$mon_print_qty;
							 }
							 $shafi_print_month_wise_kpiArr[$comp_id][2][$day_all]['print_plan']+=$shafi_po_planPrintQty_arr[$day_all][$poid];
							 //==========GMTS Embro Rec Qty Prod and Embro Plan Qty==============
							 $planEmbQtycal=0; 
							 $planEmbQtycal=$shafipur_plan_embr_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							  
							 if($planEmbQtycal>0)
							 {
								 $mon_embr_qty=$shafi_po_prodEmbQty_arr[$day_all][$poid];
								 $shafi_embr_month_wise_kpiArr[$comp_id][2][$day_all]['emb_prod']+=$mon_embr_qty;
							 }
							 $shafi_embr_month_wise_kpiArr[$comp_id][2][$day_all]['emb_plan']+=$shafi_po_planEmbQty_arr[$day_all][$poid];
							 //==========GMTS Wash Rec Qty Prod and Wash Plan Qty==============
							 $planWashQtycal=0; 
							 $planWashQtycal=$shafipur_plan_wash_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planWashQtycal>0)
							 {
								 $mon_wash_qty=$shafi_po_prodWashQty_arr[$day_all][$poid];
								 $shafi_wash_month_wise_kpiArr[$comp_id][2][$day_all]['wash_prod']+=$mon_wash_qty;
							 }
							 $shafi_wash_month_wise_kpiArr[$comp_id][2][$day_all]['wash_plan']+=$shafi_po_planWashQty_arr[$day_all][$poid];
							 //==========GMTS Fin Transfer ackl Rec Qty Prod and Fin Plan Qty==============
							 $planFinQtycal=0; 
							 $planFinQtycal=$shafipur_plan_fin_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planFinQtycal>0)
							 {
								 $mon_fin_qty=$shafi_po_prodFinQty_arr[$day_all][$poid];
								 $shafi_fin_month_wise_kpiArr[$comp_id][2][$day_all]['fin_prod']+=$mon_fin_qty;
							 }
							 $shafi_fin_month_wise_kpiArr[$comp_id][2][$day_all]['fin_plan']+=$shafi_po_planFinQty_arr[$day_all][$poid];
						  
						  //***********===========Shafipur End===================********//shafi_po_prodFinQty_arr
					  }
				  //}
				 
			 }
	  }
	}
	//********************=================Month Wise yarn Start================**************** */
	$shafi_month_wise_kpiArr=array();
	foreach($shafi_yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $yarn_month_prod=$row['yarn_prod']; 
		   $yarn_month_plan=$row['yarn_plan'];
			$yarn_month_kpi_per=$yarn_month_prod/$yarn_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($yarn_month_prod>0 && $yarn_month_plan>0)
				 {
					 if($yarn_month_kpi_per>100)  $yarn_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn']+=$yarn_month_kpi_per;
				 }
				 if($yarn_month_plan>0) 
				 {
					 $shafi_mon_grand_yarn_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Knitting======================================= */
	foreach($shafi_knit_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $knit_month_prod=$row['knit_prod']; 
		   $knit_month_plan=$row['knit_plan'];
			$knit_month_kpi_per=$knit_month_prod/$knit_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($knit_month_prod>0 && $knit_month_plan>0)
				 {
					 if($knit_month_kpi_per>100)  $knit_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit']+=$knit_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];

				 if($knit_month_plan>0)
				 {
					 $shafi_mon_grand_knit_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				 if($knit_month_plan>0 && !$yarn_plan_day_chk)
				 {
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Dyeing======================================= */
	foreach($shafi_dyeing_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $dyeing_month_prod=$row['dyeing_prod']; 
		   $dyeing_month_plan=$row['dyeing_plan'];
			$dyeing_month_kpi_per=$dyeing_month_prod/$dyeing_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($dyeing_month_plan>0 && $dyeing_month_prod>0)
				 {
					 if($dyeing_month_kpi_per>100)  $dyeing_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing']+=$dyeing_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];

				 if($dyeing_month_plan>0)
				 {
					 $shafi_mon_grand_dyeing_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				 if($dyeing_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk) )
				 {
					 
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Aop======================================= */
	foreach($shafi_aop_month_wise_kpiArr as $comp_id=>$comData)  
	{
	foreach($comData as $loc_id=>$LocData)  
	{
		foreach($LocData as $day_key=>$row)
		{
		   $aop_month_prod=$row['aop_prod']; 
		   $aop_month_plan=$row['aop_plan'];
			$aop_month_kpi_per=$aop_month_prod/$aop_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($aop_month_plan>0 && $aop_month_prod>0)
				 {
					 if($aop_month_kpi_per>100)  $aop_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop']+=$aop_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 
				 if($aop_month_plan>0)
				 {
					 $shafi_mon_grand_aop_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }
				 if($aop_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk) )
				 {
					 
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Print======================================= */
	foreach($shafi_print_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $print_month_prod=$row['print_prod']; 
		   $print_month_plan=$row['print_plan'];
			$print_month_kpi_per=$print_month_prod/$print_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($print_month_plan>0 && $print_month_prod>0)
				 {
					 if($print_month_kpi_per>100)  $print_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print']+=$print_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];

				 if($print_month_plan>0)
				 {
					 $shafi_mon_grand_print_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }
				 if($print_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk))
				 {
					 
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	 //****************Month wise Embrodiory======================================= */
	 foreach($shafi_embr_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	 {
	 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	 {
		 foreach($LocData as $day_key=>$row)
		 {
			$emb_month_prod=$row['emb_prod']; 
			$emb_month_plan=$row['emb_plan'];
			 $emb_month_kpi_per=$emb_month_prod/$emb_month_plan*100; 
			 $monthYr=date("M-Y",strtotime($day_key));
			  $yr_month=strtoupper($monthYr);
			  $today=strtotime($today_date);
			  $day_chk=strtotime($day_key);
			  if($day_chk<=$today)// as on today
			  {
				  if($emb_month_plan>0 && $emb_month_prod>0)
				  {
					  if($emb_month_kpi_per>100)  $emb_month_kpi_per=100;
				  $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb']+=$emb_month_kpi_per;
				  }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
				 $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];

				 if($emb_month_plan>0)
				 {
					 $shafi_mon_grand_emb_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				  if($emb_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk ))
				  {
					 
					  $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				  }
			  }
			   }
			 }
	 }
	 //****************Month wise Wash======================================= */
	 foreach($shafi_wash_month_wise_kpiArr as $comp_id=>$comData)  
	 {
	 foreach($comData as $loc_id=>$LocData)  
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
			  if($day_chk<=$today)// as on today
			  {
				  if($wash_month_plan>0 && $wash_month_prod>0)
				  {
					  if($wash_month_kpi_per>100)  $wash_month_kpi_per=100;
				  $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash']+=$wash_month_kpi_per;
				  }
				  $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
				 $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];
				 $emb_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb_plan'];

				 if($wash_month_plan>0)
				 {
					 $shafi_mon_grand_wash_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				  if($wash_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk  && !$emb_plan_day_chk) )
				  {
					 
					  $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				  }
			  }
			   }
			 }
	 }
	 //****************Month wise Fin Transfer Acknoledgement======================================= */
	 foreach($shafi_fin_month_wise_kpiArr as $comp_id=>$comData)  
	 {
	 foreach($comData as $loc_id=>$LocData)  
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
			  if($day_chk<=$today)// as on today
			  {
				  if($fin_month_plan>0 && $fin_month_prod>0)
				  {
					  if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
				  $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin']+=$fin_month_kpi_per;
				  }
				  $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				  $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				  $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				  $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
				  $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];
				  $emb_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb_plan'];
				  $wash_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash_plan'];

				  if($fin_month_plan>0)
				 {
					 $shafi_mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				  if($fin_month_plan>0  && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk  && !$emb_plan_day_chk && !$wash_plan_day_chk) )
				  {
					 
					  $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				  }
			  }
			   }
			 }
	 }
		// echo "<pre>";
	  //  print_r($shafi_month_wise_kpiArr);

	  //=========Month wise all event summation====================
	  asort($shafi_month_wise_kpiArr);
	 foreach($shafi_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	 {
		 foreach($comData as $unit_id=>$monData)  
		 {
			 foreach($monData as $day_key=>$pers)  
			 {
				 $event_yarn_count=0;$event_knit_count=0;$event_dyeing_count=0;$event_aop_count=0;$event_print_count=0;$event_emb_count=0;$event_wash_count=0;
				 $event_yarn_count=$shafi_mon_grand_yarn_event_kpi_perArr[$comp_id][$unit_id][$day_key];//$mon_grand_qc_event_kpi_perArr
				 $event_knit_count=$shafi_mon_grand_knit_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_dyeing_count=$shafi_mon_grand_dyeing_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_aop_count=$shafi_mon_grand_aop_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_print_count=$shafi_mon_grand_print_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_emb_count=$shafi_mon_grand_emb_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_fin_count=$shafi_mon_grand_fin_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_wash_count=$shafi_mon_grand_wash_event_kpi_perArr[$comp_id][$unit_id][$day_key];

				 $shafi_event_mon_count= $event_yarn_count+$event_knit_count+$event_dyeing_count+$event_aop_count+$event_print_count+$event_fin_count+$event_wash_count+$event_emb_count;
				 $shafi_all_kpi_per=$pers['yarn']+$pers['knit']+$pers['dyeing']+$pers['aop']+$pers['print']+$pers['emb']+$pers['fin']+$pers['wash'];
				  if($day_key=='29-May-2023')
				  {
					 // echo $day_key.'='.$pers['yarn'].'='.$pers['knit'].'='.$pers['dyeing'].'='.$pers['aop'].'='.$pers['print'].'='.$pers['emb'].'='.$pers['fin'].'='.$pers['wash'].'/'.$shafi_event_mon_count.'=Wash='. $event_wash_count.'<br>';
				  }
				 // echo $day_key.'='.$event_del_count.'='.$per['deli'].'/'.$nay_event_mon_count.'<br>';
				 $shafi_gbl_comp_avg_perArr[$comp_id][$unit_id][$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
				 //$all_avg_perArr[$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
			 }
		 }
	 }
	 
	   
// echo "<pre>";
 //print_r($all_avg_perArr);
//echo "<pre>";
foreach($shafi_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
   foreach($comData as $unitid=>$monData)  
   {
	   foreach($monData as $day_key=>$val)  
	   {
		  $monthYr=date("M-Y",strtotime($day_key));
		  $yr_month=strtoupper($monthYr);
	   	// echo $day_key.'='.$val.'<br>';
		  $shafi_gbl_comp_mon_avg_perArr[$comp_id][$unitid][$yr_month]+=$val;
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
	   if($cbo_company_id==2) $com_ttl="Shafipur"; 
	   else $com_ttl="Nayapara Fabric";  
		?>
          <table width="<? echo $tbl_width_unit;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title.'<br>'.$company_arr[$cbo_company_id];?> </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width_unit;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'kpi_unit_report', '')"> -<b>KPI Unit  <?=$com_ttl;?>[<? echo $from_year; ?>]</b></h3>
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
							$shafi_gbl_comp_avg_kip=$shafi_gbl_comp_avg_perArr[$cbo_company_id][$unitid][$date];//shafi_gbl_comp_avg_perArr
							$month_kpi_per=$shafi_gbl_comp_avg_kip;
							
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
					$tot_mon_days=$shafi_gbl_num_of_plan_daysArr[$cbo_company_id][$unitid][$year_mon];
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
if($action=="report_generate_by_year_marchandising")
{
	//Unit Shafipur/Jm Nayapara Fabric //Kal and Monthly Date Wise KPI PER%  
	$cbo_company_id = str_replace("'","",$company_id);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$cbo_templete_id 	= str_replace("'","",$cbo_templete_id);
	$unit_id 			= str_replace("'","",$report_type);
	 
	$unitid=$unit_id;
	
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
	 if($cbo_company_id==1) $jm_loc_id_cond=" and a.LOCATION_NAME=6";
	 else  $jm_loc_id_cond="";
	
	// ========================= for Plan ======================
     //============******** Shafipur/Nayapara Fabric Start here*****===========================
		 //============************************JM *******************************************
		
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=89");
		 
	    $sql_po_plan="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.TASK_ID, c.PLAN_QTY,c.PLAN_DATE, c.SOURCE_ID,c.UOM_ID,b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b, tna_plan_target c where a.id=b.job_id and b.id=c.po_id and c.task_id in(52,60,61,63,267,268,73,90)   and c.task_type=1 and c.plan_qty>0 and  b.status_active=1 and b.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337)   and c.PLAN_DATE between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.PLAN_DATE asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	 $sql_po_plan_result = sql_select($sql_po_plan);
	 foreach ($sql_po_plan_result as $val) 
	 {
		  
		 if($val['TASK_ID']==52) //Yarn Recv
		 {
				 //$plandate=strtotime($val['PLAN_DATE']);
				 $plan_yarn_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==60) //Knitting/Grey Recv
		 { 
				 $plan_knit_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==61) //Dyeing Prod
		 {
				 $plan_dyeing_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==63) //AOP Recv 
		 {
				 $plan_aop_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 }
		 if($val['TASK_ID']==267) //Print Recv 
		 {
				 $plan_print_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
		 if($val['TASK_ID']==268) //Embro Recv 
		 {
				 $plan_embro_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
		 if($val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //MFG Fin Recv 
		 {
				  
				 $plan_fin_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
		 if($val['COMPANY_ID']==2 && $val['TASK_ID']==90) //Wash Fin Recv 
		 {
				 $plan_wash_shafipur_poIdArr[$val['POID']]=$val['POID'];
				 $shafipur_com_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		 } 
 
		 $company_wise_arr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		 
	 }
	 //print_r($plan_wash_shafipur_poIdArr);die;
	 //===============Yarn Recv Start for Shafipur==============

fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 20, $plan_yarn_shafipur_poIdArr, $empty_arr);//PO ID Ref from=20
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 21, $plan_knit_shafipur_poIdArr, $empty_arr);//PO ID Ref from=21
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 22, $plan_dyeing_shafipur_poIdArr, $empty_arr);//PO ID Ref from=22
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 23, $plan_aop_shafipur_poIdArr, $empty_arr);//PO ID Ref from=23
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 24, $plan_print_shafipur_poIdArr, $empty_arr);//PO ID Ref from=24
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 25, $plan_embro_shafipur_poIdArr, $empty_arr);//PO ID Ref from=25
fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 26, $plan_fin_shafipur_poIdArr, $empty_arr);//PO ID Ref from=26
if($cbo_company_id==2)
{
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 27, $plan_wash_shafipur_poIdArr, $empty_arr);//PO ID Ref from=27
}

   $sql_po_plan_shafipur="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PLAN_DATE,c.TASK_ID,c.SOURCE_ID,c.UOM_ID,g.REF_FROM, b.job_no_mst as JOB_NO,
  
 (case when  c.task_id=52  and g.ref_from=20  then c.PLAN_QTY else 0 end) as SHAFI_YARN_PLAN_QTY,
 (case when  c.task_id=60  and g.ref_from=21  then c.PLAN_QTY else 0 end) as SHAFI_KNIT_PLAN_QTY,
 (case when  c.task_id=61 and g.ref_from=22  then c.PLAN_QTY else 0 end) as SHAFI_DYING_PLAN_QTY,
 (case when  c.task_id=63  and g.ref_from=23  then c.PLAN_QTY else 0 end) as SHAFI_AOP_PLAN_QTY,
 (case when  c.task_id=267  and g.ref_from=24  then c.PLAN_QTY else 0 end) as SHAFI_PRINT_PLAN_QTY,
 (case when  c.task_id=268  and g.ref_from=25  then c.PLAN_QTY else 0 end) as SHAFI_EMBR_PLAN_QTY,
 (case when  c.task_id=73 and c.source_id=1 and g.ref_from=26  then c.PLAN_QTY else 0 end) as SHAFI_FIN_PLAN_QTY,
 (case when  c.task_id=90  and g.ref_from=27  then c.PLAN_QTY else 0 end) as SHAFI_WASH_PLAN_QTY

from wo_po_details_master a,wo_po_break_down b, tna_plan_target c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_id    and c.task_type=1 and c.plan_qty>0 and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(20,21,22,23,24,25,26,27) and c.task_id in(52,60,61,63,267,268,73,90) and g.entry_form=89  and  c.status_active=1 and c.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $company_conds $jm_loc_id_cond and c.PLAN_DATE <= '$endDate' order by c.PLAN_DATE asc";
 //and b.id in(20325,20326,20327,20328,20329,20330)
  $sql_po_plan_result_shafipur = sql_select($sql_po_plan_shafipur);
  foreach ($sql_po_plan_result_shafipur as $val) 
  {
	  $plandate=date('d-M-Y',strtotime($val['PLAN_DATE']));
	  if($val['TASK_ID']==52) //Yarn
	  {
		$shafipur_plan_yarn_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_YARN_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==60) //Knit
	  {
		$shafipur_plan_knit_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_KNIT_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==61) //Dyeing
	  {
		$shafipur_plan_dyeing_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_DYING_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==63) //AOP
	  {
		$shafipur_plan_aop_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_AOP_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==267) //Print
	  {
		$shafipur_plan_print_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_PRINT_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==268) //Embro
	  {
		$shafipur_plan_embr_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_EMBR_PLAN_QTY'];
	  }
	  if($val['TASK_ID']==73 && $val['SOURCE_ID']==1 && $val['UOM_ID']==12) //Fin Gmt/Production,KG
	  {
		$shafipur_plan_fin_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_FIN_PLAN_QTY'];
	  }
	  if($val['COMPANY_ID']==2  && $val['TASK_ID']==90) //Wash
	  {
		$shafipur_plan_wash_recv_qty_array[2][$plandate][$val['POID']][$val['COMPANY_ID']]['plan']+=$val['SHAFI_WASH_PLAN_QTY'];
	  }
	
  }
  // print_r($shafipur_plan_fin_recv_qty_array);

     $sql_yarn_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
 inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
 and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
   and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1 and d.entry_form=1 and  c.BOOKING_NO like '%YDW%'  and c.transaction_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
	$sql_yarn_recv_kal_result = sql_select($sql_yarn_recv_kal); 
	foreach($sql_yarn_recv_kal_result  as $val)
	{
		$kal_shafipur_poIdArr[$val['POID']]=$val['POID'];
		$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
		
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 28, $kal_shafipur_poIdArr, $empty_arr);//PO ID Ref from=27
	//print_r($kal_shafipur_poIdArr);

	    $sql_yarn_recv_kal_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
	inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(28) and g.entry_form=89 and d.entry_form=1
	and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
	  and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and  c.BOOKING_NO like '%YDW%'   and c.transaction_date <= '$endDate' $jm_loc_id_cond  $company_conds order by c.transaction_date asc";
	  //and b.id in(20325,20326,20327,20328,20329,20330)
	  
	   $sql_yarn_recv_kal_qty_result = sql_select($sql_yarn_recv_kal_qty);
	   foreach($sql_yarn_recv_kal_qty_result  as $val)
	   {
		$trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
		//echo $val['POID'].'='.$trans_date.'='.$val['QUANTITY'].'<br>';
		$shafipur_plan_yarn_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['y_recv']+=$val['QUANTITY'];
		//$shafipur_plan_yarn_recv_qty_array2[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['y_recv']+=$val['QUANTITY'];
		   
	   }
	   // print_r($shafipur_plan_yarn_recv_qty_array2);
// ======================Kntting/Grey Recv production=================
	     $sql_knit_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
	   inv_transaction c,order_wise_pro_details d  where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id
	   and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
		 and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(2,22) and c.transaction_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.transaction_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
		 
		  $sql_knit_recv_kal_result = sql_select($sql_knit_recv_kal); 
		  foreach($sql_knit_recv_kal_result  as $val)
		  {
			  $kal_knit_poIdArr[$val['POID']]=$val['POID'];
			  $kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
			  
		  }
		  fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 29, $kal_knit_poIdArr, $empty_arr);//PO ID Ref from=29
		  //print_r($kal_shafipur_poIdArr);
	  
			$sql_knit_recv_kal_qty="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.QUANTITY,c.TRANSACTION_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
		  inv_transaction c,order_wise_pro_details d,gbl_temp_engine g   where a.id=b.job_id  and c.id=d.trans_id and b.id=d.po_breakdown_id and b.id=g.ref_val and d.po_breakdown_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(29) and g.entry_form=89
		  and d.quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
			and b.id in(20328,20329,20330,20334,20335,20337) and c.transaction_type=1   and d.entry_form in(2,22)    and c.transaction_date <= '$endDate' $jm_loc_id_cond  $company_conds order by c.transaction_date asc"; 
			//and b.id in(20325,20326,20327,20328,20329,20330)
			
			 $sql_sql_knit_recv_kal_qty_result = sql_select($sql_knit_recv_kal_qty);
			 foreach($sql_sql_knit_recv_kal_qty_result  as $val)
			 {
			  $trans_date=date('d-M-Y',strtotime($val['TRANSACTION_DATE']));
			  $shafipur_plan_knit_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['knit_recv']+=$val['QUANTITY'];
			 }

			 //////=================Dyeing Prod=======================
			 
			   $sql_dyeing_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.BATCH_QNTY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			 pro_batch_create_dtls c,pro_fab_subprocess d  where a.id=b.job_id  and b.id=c.po_id and c.mst_id=d.batch_id
			 and c.BATCH_QNTY>0 and b.status_active=1 and d.load_unload_id=2 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
			   and b.id in(20328,20329,20330,20334,20335,20337) and d.batch_ext_no is null and d.entry_form in(35) and d.process_end_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by d.process_end_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
			   
				$sql_dyeing_kal_result = sql_select($sql_dyeing_kal); 
				foreach($sql_dyeing_kal_result  as $val)
				{
					$kal_dyeing_poIdArr[$val['POID']]=$val['POID'];
					$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				}
				 
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 30, $kal_dyeing_poIdArr, $empty_arr);//PO ID Ref from=30

				$sql_dyeing_qty_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.BATCH_QNTY,d.PROCESS_END_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				pro_batch_create_dtls c,pro_fab_subprocess d,gbl_temp_engine g   where a.id=b.job_id  and b.id=c.po_id and c.mst_id=d.batch_id and b.id=g.ref_val and c.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(30) and g.entry_form=89
				and c.BATCH_QNTY>0 and b.status_active=1 and d.load_unload_id=2 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
				  and b.id in(20328,20329,20330,20334,20335,20337) and d.batch_ext_no is null and d.entry_form in(35)   and d.process_end_date <= '$endDate'  $company_conds $jm_loc_id_cond order by d.process_end_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
				  
				$sql_dyeing_kal_qty_result = sql_select($sql_dyeing_qty_kal); 
				foreach($sql_dyeing_kal_qty_result  as $val)
				{
				$dying_date=date('d-M-Y',strtotime($val['PROCESS_END_DATE']));
				$shafipur_plan_dyeing_recv_qty_array[2][$dying_date][$val['POID']][$val['COMPANY_ID']]['dyeing']+=$val['BATCH_QNTY'];
				}
				//inv_receive_mas_batchroll
				 //////=================Fabric Service Recv=======================

				$sql_fab_aop_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.BATCH_ISSUE_QTY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				inv_receive_mas_batchroll c,pro_grey_batch_dtls d  where a.id=b.job_id  and b.id=d.order_id and c.id=d.mst_id
				and d.BATCH_ISSUE_QTY>0 and b.status_active=1 and c.entry_form=92 and d.process_id=35 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
				and b.id in(20328,20329,20330,20334,20335,20337)   and c.receive_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.receive_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
				
				$sql_fab_aop_kal_result = sql_select($sql_fab_aop_kal); 
				foreach($sql_fab_aop_kal_result  as $val)
				{
					$kal_fab_aop_poIdArr[$val['POID']]=$val['POID'];
					$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				}
					
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 31, $kal_fab_aop_poIdArr, $empty_arr);//PO ID Ref from=30

				$sql_fab_aop_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,d.BATCH_ISSUE_QTY,c.RECEIVE_DATE, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
				inv_receive_mas_batchroll c,pro_grey_batch_dtls d,gbl_temp_engine g where a.id=b.job_id  and b.id=d.order_id and c.id=d.mst_id  and b.id=g.ref_val and d.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(31) and g.entry_form=89
				and d.BATCH_ISSUE_QTY>0 and b.status_active=1 and c.entry_form=92 and d.process_id=35 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 
					and b.id in(20328,20329,20330,20334,20335,20337)   and c.receive_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by c.receive_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
					
					$sql_fab_aop_kal_result = sql_select($sql_fab_aop_kal); 
					foreach($sql_fab_aop_kal_result  as $val)
					{
					$aop_date=date('d-M-Y',strtotime($val['RECEIVE_DATE']));
					$shafipur_plan_aop_recv_qty_array[2][$aop_date][$val['POID']][$val['COMPANY_ID']]['aop_recv']+=$val['BATCH_ISSUE_QTY'];
					}
			//=============Print Recv=======================
			  $sql_po_prod_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.PRODUCTION_TYPE,c.PRODUCTION_DATE, b.job_no_mst as JOB_NO
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id  and c.production_type in(3)  and c.PRODUCTION_QUANTITY>0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337)   and c.production_date between '$startDate' and '$endDate' and c.production_type in(3)  $company_conds $jm_loc_id_cond order by c.PRODUCTION_DATE asc"; //and a.location_name=3
			
			$sql_po_result_prod_kal = sql_select($sql_po_prod_kal);
			foreach ($sql_po_result_prod_kal as $val) 
			{
				//if($val['COMPANY_ID']==2) //Kal
				//{
				$kal_print_poIdArr[$val['POID']]=$val['POID'];
				$kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];
				//}
			}
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 32, $kal_print_poIdArr, $empty_arr);//PO ID Ref from=14

			  $sql_po_prod_kal_curr="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,c.PRODUCTION_DATE,c.EMBEL_NAME,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
			
			(case when  c.production_type=3 and c.embel_name=1  then c.PRODUCTION_QUANTITY else 0 end) as PRINT_PROD,
			(case when  c.production_type=3 and c.embel_name=2  then c.PRODUCTION_QUANTITY else 0 end) as EMBR_PROD,
			(case when  c.production_type=3 and c.embel_name=3  then c.PRODUCTION_QUANTITY else 0 end) as WASH_PROD
			
			from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
			and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(32) and g.entry_form=89 
			and c.PRODUCTION_QUANTITY>0  and  b.status_active=1 and b.is_deleted=0 and c.production_type in(3) and  c.status_active=1 and c.is_deleted=0 and c.production_date <= '$endDate' $company_conds $jm_loc_id_cond order by c.PRODUCTION_DATE asc";
			$sql_po_result_prod_kal_curr = sql_select($sql_po_prod_kal_curr);
			foreach ($sql_po_result_prod_kal_curr as $val) 
			{
				
				$jm_poId_loaction_Arr[$val['COMPANY_ID']][$val['POID']]=$val['LOCATION_NAME'];
				$loc_id=$val['LOCATION_NAME'];
				$propddate=date('d-M-Y',strtotime($val['PRODUCTION_DATE']));
				 
				
				if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==1) // Print
				{
					$shafipur_plan_print_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_print']+=$val['PRINT_PROD'];

					//echo $val['PRINT_PROD'].'A=<br>';
				}
				if($val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==2)// Embro
				{
					$shafipur_plan_embr_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_embr']+=$val['EMBR_PROD'];
				}
			   
				if($val['COMPANY_ID']==2 && $val['PRODUCTION_TYPE']==3 && $val['EMBEL_NAME']==3)// Wash
				{
					$shafipur_plan_wash_recv_qty_array[2][$propddate][$val['POID']][$val['COMPANY_ID']]['prod_wash']+=$val['WASH_PROD'];
				}
			}
			unset($sql_po_result_prod_kal_curr); 
		//	print_r($shafipur_plan_print_recv_qty_array);

			// Knit Fin Fab Transfer Ack===========
			// ======================Knit Fin Fab Transfer Ack=================
			if($cbo_company_id==2) $store_id_cond="8,20";
			else  $store_id_cond="32,52";

			 $sql_knit_fin_recv_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.CONS_QUANTITY, b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			inv_transaction c,inv_item_trans_acknowledgement d  where a.id=b.job_id and b.id=c.order_id   and c.mst_id=d.challan_id and d.store_id in($store_id_cond) and d.transfer_criteria = 2 and c.item_category = 2 and c.transaction_type = 5 and c.cons_quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337) and d.entry_form in(247) 
			and d.acknowledg_date between '$startDate' and '$endDate' $company_conds $jm_loc_id_cond order by d.acknowledg_date asc"; //and b.id in(20325,20326,20327,20328,20329,20330)
			  
			   $sql_knit_fin_recv_kal_result = sql_select($sql_knit_fin_recv_kal); 
			   foreach($sql_knit_fin_recv_kal_result  as $val)
			   {
				   $kal_fin_knit_poIdArr[$val['POID']]=$val['POID'];
				   $kal_po_shafipur_poIdArr[$val['COMPANY_ID']][$val['POID']]=$val['GROUPING'];   
			   }
			   fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 89, 33, $kal_fin_knit_poIdArr, $empty_arr);//PO ID Ref from=27
			   //print_r($kal_shafipur_poIdArr);
			$sql_knit_fin_recv_qty_kal="SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.GROUPING,c.CONS_QUANTITY, d.ACKNOWLEDG_DATE,b.job_no_mst as JOB_NO from wo_po_details_master a,wo_po_break_down b,
			inv_transaction c,inv_item_trans_acknowledgement d,gbl_temp_engine g where a.id=b.job_id and b.id=c.order_id   and c.mst_id=d.challan_id and b.id=g.ref_val and c.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(33) and g.entry_form=89  and d.store_id in($store_id_cond) and d.transfer_criteria = 2 and c.item_category = 2 and c.transaction_type = 5 and c.cons_quantity>0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and b.id in(20328,20329,20330,20334,20335,20337) and d.entry_form in(247) 
			and d.acknowledg_date <= '$endDate' $company_conds $jm_loc_id_cond order by d.acknowledg_date asc";

			$sql_sql_knit_recv_kal_qty_result = sql_select($sql_knit_fin_recv_qty_kal);
			foreach($sql_sql_knit_recv_kal_qty_result  as $val) 
			{
			$trans_date=date('d-M-Y',strtotime($val['ACKNOWLEDG_DATE']));
			$shafipur_plan_fin_recv_qty_array[2][$trans_date][$val['POID']][$val['COMPANY_ID']]['fin_recv']+=$val['CONS_QUANTITY'];
			}

	
	// echo "<pre>";
	// print_r($plan_qty_array);
	
	//===================Allocation Wise Calculation=======
	
	 
  // echo "<pre>";
//print_r($till_today_planArr);
 //ksort($till_today_AllQtyArr);
 // =============================Calculation of KPI============= //plan asy  allocatio ni  day dorbo
		$company_kip_cal_Arr=array();//$company_wise_arr=array();
		ksort($shafipur_plan_yarn_recv_qty_array);
		ksort($shafipur_plan_knit_recv_qty_array);
		ksort($shafipur_plan_dyeing_recv_qty_array);
		ksort($shafipur_plan_aop_recv_qty_array);
		ksort($shafipur_plan_print_recv_qty_array);
		ksort($shafipur_plan_embr_recv_qty_array);
		ksort($shafipur_plan_wash_recv_qty_array);
		ksort($shafipur_plan_fin_recv_qty_array);
		
		
	
		//unset($sql_po_plan_result);
		//unset($sql_po_allocate_result);
	 
	
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
					
						 
						$diff_days=datediff('d',$from_date,$to_date);
					
						for($j=0;$j<$diff_days;$j++)
						{
							if($j==0)
							{
								$newdate_shafi = date('d-M-Y', strtotime($from_date));
							}
							else
							{
								$newdate_shafi = date('d-M-Y', strtotime("+1 day", strtotime($newdate_shafi)));
							}
							//===========Yarn Recv============
							foreach($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi] as $poid_rec=>$poData)
							{
								$sha_planQty=$yarn_sha_recv_Qty=0;
								$sha_planQty=$poData[$com_id]['plan'];
								$yarn_sha_recv_Qty=$poData[$com_id]['y_recv'];
								if($yarn_sha_recv_Qty=='') $yarn_sha_recv_Qty=0;
								//  echo $newdate_shafi.'='.$yarn_sha_recv_Qty.'<br>';
								if($sha_planQty>0)
								{ 
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planYarnQty_arr[$newdate_shafi][$poid_rec]=$sha_planQty+$shafi_po_planYarnQty_arr[$sha_prev_date_planYarn[$poid_rec]][$poid_rec];
									$sha_prev_date_planYarn[$poid_rec] = $newdate_shafi;
								}
								if($sha_planQty>0 || $yarn_sha_recv_Qty>0)
								{
									if($sha_planQty>0 && $yarn_sha_recv_Qty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planQty==0 && $yarn_sha_recv_Qty>0) 
									{
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$yarn_shafi_planQtycal=0;  
									$yarn_shafi_planQtycal=$shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['plan'];//Plan
									if($shafipur_plan_yarn_recv_qty_array[2][$newdate_shafi][$poid_rec][$com_id]['y_recv']!="" || $yarn_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodYarnQty_arr[$newdate_shafi][$poid_rec]=$yarn_sha_recv_Qty+$shafi_po_prodYarnQty_arr[$sha_prev_prodYarn_date[$poid_rec]][$poid_rec];
										$sha_prev_prodYarn_date[$poid_rec] = $newdate_shafi;
									}
								}
							} 
							//==========Knitting=========================
							foreach($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi] as $poid_k=>$poData)
							{
								$sha_planKnitQty=$sha_prodKnitQty=0;
								$sha_planKnitQty=$poData[$com_id]['plan'];
								$sha_prodKnitQty=$poData[$com_id]['knit_recv'];
								if($sha_prodKnitQty=='') $sha_prodKnitQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planKnitQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planKnitQty_arr[$newdate_shafi][$poid_k]=$sha_planKnitQty+$shafi_po_planKnitQty_arr[$sha_prev_date_planKnit[$poid_k]][$poid_k];
									$sha_prev_date_planKnit[$poid_k] = $newdate_shafi;
								}
								if($sha_planKnitQty>0 || $sha_prodKnitQty>0)
								{
									if($sha_planKnitQty>0 && $sha_prodKnitQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planKnitQty==0 && $sha_prodKnitQty>0) 
									{
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$shafi_planQtycal=0;
									$shafi_planQtycal=$shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['plan'];//Plan
									if($shafipur_plan_knit_recv_qty_array[2][$newdate_shafi][$poid_k][$com_id]['knit_recv']!="" || $shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodKnitQty_arr[$newdate_shafi][$poid_k]=$sha_prodKnitQty+$shafi_po_prodKnitQty_arr[$sha_prev_prodKnit_date[$poid_k]][$poid_k];
										$sha_prev_prodKnit_date[$poid_k] = $newdate_shafi;
									}
								}
							}
							//==========Dyeing=========================
							foreach($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi] as $poid_d=>$poData)
							{
								$sha_planDyingQty=$sha_prodDyeingQty=0;
								$sha_planDyingQty=$poData[$com_id]['plan'];
								$sha_prodDyeingQty=$poData[$com_id]['dyeing'];
								if($sha_prodDyeingQty=='') $sha_prodDyeingQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planDyingQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_planDyingQty+$shafi_po_planDyeingQty_arr[$sha_prev_date_planDye[$poid_d]][$poid_d];
									$sha_prev_date_planDye[$poid_d] = $newdate_shafi;
								}
								if($sha_planDyingQty>0 || $sha_prodDyeingQty>0)
								{
									if($sha_planDyingQty>0 && $sha_prodDyeingQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planDyingQty==0 && $sha_prodDyeingQty>0) 
									{
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$dye_shafi_planQtycal=0;
									$dye_shafi_planQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['plan'];//Plan
									if($shafipur_plan_dyeing_recv_qty_array[2][$newdate_shafi][$poid_d][$com_id]['dyeing']!="" || $dye_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodDyeingQty_arr[$newdate_shafi][$poid_d]=$sha_prodDyeingQty+$shafi_po_prodDyeingQty_arr[$sha_prev_prodDye_date[$poid_d]][$poid_d];
										$sha_prev_prodDye_date[$poid_d] = $newdate_shafi;
									}
								}
							}
							//==========Aop Recv=========================
							foreach($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi] as $poid_a=>$poData)
							{
								$sha_planAopQty=$sha_prodAopQty=0;
								$sha_planAopQty=$poData[$com_id]['plan'];
								$sha_prodAopQty=$poData[$com_id]['aop_recv'];
								if($sha_prodAopQty=='') $sha_prodAopQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planAopQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planAopQty_arr[$newdate_shafi][$poid_a]=$sha_planAopQty+$shafi_po_planAopQty_arr[$sha_prev_date_planAop[$poid_a]][$poid_a];
									$sha_prev_date_planAop[$poid_a] = $newdate_shafi;
								}
								if($sha_planAopQty>0 || $sha_prodAopQty>0)
								{
									if($sha_planAopQty>0 && $sha_prodAopQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planAopQty==0 && $sha_prodAopQty>0) 
									{
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$aop_shafi_planQtycal=0;
									$aop_shafi_planQtycal=$shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['plan'];//Plan
									if($shafipur_plan_aop_recv_qty_array[2][$newdate_shafi][$poid_a][$com_id]['aop_recv']!="" || $aop_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodAopQty_arr[$newdate_shafi][$poid_a]=$sha_prodAopQty+$shafi_po_prodAopQty_arr[$sha_prev_prodAop_date[$poid_a]][$poid_a];
										$sha_prev_prodAop_date[$poid_a] = $newdate_shafi;
									}
								}
							}

							//==========Print Recv=========================
							foreach($shafipur_plan_print_recv_qty_array[2][$newdate_shafi] as $poid_pr=>$poData)
							{
								$sha_planPrintQty=$sha_prodPrintQty=0;
								$sha_planPrintQty=$poData[$com_id]['plan'];
								$sha_prodPrintQty=$poData[$com_id]['prod_print'];
								if($sha_prodPrintQty=='') $sha_prodPrintQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planPrintQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_planPrintQty+$shafi_po_planPrintQty_arr[$sha_prev_date_planPrint[$poid_pr]][$poid_pr];
									$sha_prev_date_planPrint[$poid_pr] = $newdate_shafi;
								}
								if($sha_planPrintQty>0 || $sha_prodPrintQty>0)
								{
									if($sha_planPrintQty>0 && $sha_prodPrintQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planPrintQty==0 && $sha_prodPrintQty>0) 
									{
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$print_shafi_planQtycal=0;
									$print_shafi_planQtycal=$shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['plan'];//Plan
									if($shafipur_plan_print_recv_qty_array[2][$newdate_shafi][$poid_pr][$com_id]['prod_print']!="" || $print_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodPrintQty_arr[$newdate_shafi][$poid_pr]=$sha_prodPrintQty+$shafi_po_prodPrintQty_arr[$sha_prev_prodPrint_date[$poid_pr]][$poid_pr];
										$sha_prev_prodPrint_date[$poid_pr] = $newdate_shafi;
									}
								}
							}
							//==========Embro Recv=========================
							foreach($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi] as $poid_er=>$poData)
							{
								$sha_planEmbQty=$sha_prodEmbQty=0;
								$sha_planEmbQty=$poData[$com_id]['plan'];
								$sha_prodEmbQty=$poData[$com_id]['prod_embr'];
								if($sha_prodEmbQty=='') $sha_prodEmbQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planEmbQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planEmbQty_arr[$newdate_shafi][$poid_er]=$sha_planEmbQty+$shafi_po_planEmbQty_arr[$sha_prev_date_planEmb[$poid_er]][$poid_er];
									$sha_prev_date_planEmb[$poid_er] = $newdate_shafi;
								}
								if($sha_planEmbQty>0 || $sha_prodEmbQty>0)
								{
									if($sha_planEmbQty>0 && $sha_prodEmbQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planEmbQty==0 && $sha_prodEmbQty>0) 
									{
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$emb_shafi_planQtycal=0;
									$emb_shafi_planQtycal=$shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['plan'];//Plan
									if($shafipur_plan_embr_recv_qty_array[2][$newdate_shafi][$poid_er][$com_id]['prod_embr']!="" || $emb_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodEmbQty_arr[$newdate_shafi][$poid_er]=$sha_prodEmbQty+$shafi_po_prodEmbQty_arr[$sha_prev_prodEmb_date[$poid_er]][$poid_er];
										$sha_prev_prodEmb_date[$poid_er] = $newdate_shafi;
									}
								}
							}
							//==========Wash Recv=========================
							foreach($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi] as $poid_wr=>$poData)
							{
								$sha_planWashQty=$sha_prodWashQty=0;
								$sha_planWashQty=$poData[$com_id]['plan'];
								$sha_prodWashQty=$poData[$com_id]['prod_wash'];
								if($sha_prodWashQty=='') $sha_prodWashQty=0;

								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planWashQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planWashQty_arr[$newdate_shafi][$poid_wr]=$sha_planWashQty+$shafi_po_planWashQty_arr[$sha_prev_date_planWash[$poid_wr]][$poid_wr];
									$sha_prev_date_planWash[$poid_wr] = $newdate_shafi;
								}
								if($sha_planWashQty>0 || $sha_prodWashQty>0)
								{
									if($sha_planWashQty>0 && $sha_prodWashQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planWashQty==0 && $sha_prodWashQty>0) 
									{
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$wash_shafi_planQtycal=0;
									$wash_shafi_planQtycal=$shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['plan'];//Plan
									if($shafipur_plan_wash_recv_qty_array[2][$newdate_shafi][$poid_wr][$com_id]['prod_wash']!="" || $wash_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodWashQty_arr[$newdate_shafi][$poid_wr]=$sha_prodWashQty+$shafi_po_prodWashQty_arr[$sha_prev_prodWash_date[$poid_wr]][$poid_wr];
										$sha_prev_prodWash_date[$poid_wr] = $newdate_shafi;
									}
								}
							}

							//==========Fin Fab Transfer Acknowlege Recv=========================
							foreach($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi] as $poid_ft=>$poData)
							{
								$sha_planFinQty=$sha_prodFinQty=0;
								$sha_planFinQty=$poData[$com_id]['plan'];
								$sha_prodFinQty=$poData[$com_id]['fin_recv'];
								if($sha_prodFinQty=='') $sha_prodFinQty=0;
								 //echo $newdate_nay.'='.$nay_planQty_qc.'='.$nay_prod_qcQty.'<br>';
								if($sha_planFinQty>0)
								{
									//  echo $newdate_ash."=".$poid."=".$planQty_qc."+".$po_planQtyQc_arr[$prev_date_planQc[$poid]][$poid]."<br />";
									$shafi_po_planFinQty_arr[$newdate_shafi][$poid_ft]=$sha_planFinQty+$shafi_po_planFinQty_arr[$sha_prev_date_planFin[$poid_ft]][$poid_ft];
									$sha_prev_date_planFin[$poid_ft] = $newdate_shafi;
								}
								if($sha_planFinQty>0 || $sha_prodFinQty>0)
								{
									if($sha_planFinQty>0 && $sha_prodFinQty==0)
									{
									//	if($poid==20326)  echo $newdate."=".$poid."=".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=A <br>';
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
										//echo "A=".$poid;
									}
									if($sha_planFinQty==0 && $sha_prodFinQty>0) 
									{
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
									// if($poid==20326)  echo $po_alloQty_arr[$prev_allocation_date[$poid]][$poid].'=B <br>';
									}
									$fin_shafi_planQtycal=0;
									$fin_shafi_planQtycal=$shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['plan'];//Plan
									if($shafipur_plan_fin_recv_qty_array[2][$newdate_shafi][$poid_ft][$com_id]['fin_recv']!="" || $fin_shafi_planQtycal>0)
									{
										//if($poid==20326)  echo $newdate."=".$poid."=".$alloQty."+".$po_alloQty_arr[$prev_allocation_date[$poid]][$poid]."=C <br />";
										$shafi_po_prodFinQty_arr[$newdate_shafi][$poid_ft]=$sha_prodFinQty+$shafi_po_prodFinQty_arr[$sha_prev_prodFin_date[$poid_ft]][$poid_ft];
										$sha_prev_prodFin_date[$poid_ft] = $newdate_shafi;
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
	  //====================Kal Shafipur Location=========================
	 ///************ */===============================Shafipur============================
	foreach($kal_po_shafipur_poIdArr as $comp_id=>$comData)
	{
	  foreach($comData as $poid=>$IR)
	  {
			 // $loaction=0;
			 //$loaction=$poId_loaction_Arr[$comp_id][$poid];
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
							 //==========Shafipur Location Prod and  Plan==============
							 //==========Shafipur Yarn Recv   and  Plan==============
							 $planYarnQtycal=0;
							 $planYarnQtycal=$shafipur_plan_yarn_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
							 if($planYarnQtycal>0)//shafi_po_prodYarnQty_arr
							 {
								 $mon_yarn_qty=$shafi_po_prodYarnQty_arr[$day_all][$poid];
								 //  echo $day_all.'='.$mon_yarn_qty.'<br>';
								 $shafi_yarn_month_wise_kpiArr[$comp_id][2][$day_all]['yarn_prod']+=$mon_yarn_qty;
							 }
							 $shafi_yarn_month_wise_kpiArr[$comp_id][2][$day_all]['yarn_plan']+=$shafi_po_planYarnQty_arr[$day_all][$poid];

							 //==========Knit Prod and Knit Plan==============
							 $planKnitQtycal=0;
							 $planKnitQtycal=$shafipur_plan_knit_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
							 if($planKnitQtycal>0)
							 {
								 $mon_knit_qty=$shafi_po_prodKnitQty_arr[$day_all][$poid];
								 $shafi_knit_month_wise_kpiArr[$comp_id][2][$day_all]['knit_prod']+=$mon_knit_qty;
							 }
							 $shafi_knit_month_wise_kpiArr[$comp_id][2][$day_all]['knit_plan']+=$shafi_po_planKnitQty_arr[$day_all][$poid];
							 //==========Dyeing Qty Prod and Dyeing Plan Qty==============
							 $planDyeingQtycal=0;
							 $planDyeingQtycal=$shafipur_plan_dyeing_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];//alloQty
							 if($planDyeingQtycal>0)
							 {
								 $mon_dyeing_qty=$shafi_po_prodDyeingQty_arr[$day_all][$poid];
								 $shafi_dyeing_month_wise_kpiArr[$comp_id][2][$day_all]['dyeing_prod']+=$mon_dyeing_qty;
							 }
							 $shafi_dyeing_month_wise_kpiArr[$comp_id][2][$day_all]['dyeing_plan']+=$shafi_po_planDyeingQty_arr[$day_all][$poid];
							 //==========GMTS Aop Rec Qty Prod and Aop Plan Qty==============
							 $planAopQtycal=0; 
							 $planAopQtycal=$shafipur_plan_aop_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planAopQtycal>0)
							 {
								 $mon_aop_qty=$shafi_po_prodAopQty_arr[$day_all][$poid];
								 $shafi_aop_month_wise_kpiArr[$comp_id][2][$day_all]['aop_prod']+=$mon_aop_qty;
							 }
							 $shafi_aop_month_wise_kpiArr[$comp_id][2][$day_all]['aop_plan']+=$shafi_po_planAopQty_arr[$day_all][$poid];
							 //==========GMTS Print Rec Qty Prod and Print Plan Qty==============
							 $planPrintQtycal=0; 
							 $planPrintQtycal=$shafipur_plan_print_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planPrintQtycal>0)
							 {
								 $mon_print_qty=$shafi_po_prodPrintQty_arr[$day_all][$poid];
								 $shafi_print_month_wise_kpiArr[$comp_id][2][$day_all]['print_prod']+=$mon_print_qty;
							 }
							 $shafi_print_month_wise_kpiArr[$comp_id][2][$day_all]['print_plan']+=$shafi_po_planPrintQty_arr[$day_all][$poid];
							 //==========GMTS Embro Rec Qty Prod and Embro Plan Qty==============
							 $planEmbQtycal=0; 
							 $planEmbQtycal=$shafipur_plan_embr_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							  
							 if($planEmbQtycal>0)
							 {
								 $mon_embr_qty=$shafi_po_prodEmbQty_arr[$day_all][$poid];
								 $shafi_embr_month_wise_kpiArr[$comp_id][2][$day_all]['emb_prod']+=$mon_embr_qty;
							 }
							 $shafi_embr_month_wise_kpiArr[$comp_id][2][$day_all]['emb_plan']+=$shafi_po_planEmbQty_arr[$day_all][$poid];
							 //==========GMTS Wash Rec Qty Prod and Wash Plan Qty==============
							 $planWashQtycal=0; 
							 $planWashQtycal=$shafipur_plan_wash_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planWashQtycal>0)
							 {
								 $mon_wash_qty=$shafi_po_prodWashQty_arr[$day_all][$poid];
								 $shafi_wash_month_wise_kpiArr[$comp_id][2][$day_all]['wash_prod']+=$mon_wash_qty;
							 }
							 $shafi_wash_month_wise_kpiArr[$comp_id][2][$day_all]['wash_plan']+=$shafi_po_planWashQty_arr[$day_all][$poid];
							 //==========GMTS Fin Transfer ackl Rec Qty Prod and Fin Plan Qty==============
							 $planFinQtycal=0; 
							 $planFinQtycal=$shafipur_plan_fin_recv_qty_array[2][$day_all][$poid][$comp_id]['plan'];
							 if($planFinQtycal>0)
							 {
								 $mon_fin_qty=$shafi_po_prodFinQty_arr[$day_all][$poid];
								 $shafi_fin_month_wise_kpiArr[$comp_id][2][$day_all]['fin_prod']+=$mon_fin_qty;
							 }
							 $shafi_fin_month_wise_kpiArr[$comp_id][2][$day_all]['fin_plan']+=$shafi_po_planFinQty_arr[$day_all][$poid];
						  
						  //***********===========Shafipur End===================********//shafi_po_prodFinQty_arr
					  }
				  //}
				 
			 }
	  }
	}
	//********************=================Month Wise yarn Start================**************** */
	$shafi_month_wise_kpiArr=array();
	foreach($shafi_yarn_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $yarn_month_prod=$row['yarn_prod']; 
		   $yarn_month_plan=$row['yarn_plan'];
			$yarn_month_kpi_per=$yarn_month_prod/$yarn_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($yarn_month_prod>0 && $yarn_month_plan>0)
				 {
					 if($yarn_month_kpi_per>100)  $yarn_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn']+=$yarn_month_kpi_per;
				 }
				 if($yarn_month_plan>0) 
				 {
					 $shafi_mon_grand_yarn_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Knitting======================================= */
	foreach($shafi_knit_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $knit_month_prod=$row['knit_prod']; 
		   $knit_month_plan=$row['knit_plan'];
			$knit_month_kpi_per=$knit_month_prod/$knit_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($knit_month_prod>0 && $knit_month_plan>0)
				 {
					 if($knit_month_kpi_per>100)  $knit_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit']+=$knit_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];

				 if($knit_month_plan>0)
				 {
					 $shafi_mon_grand_knit_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				 if($knit_month_plan>0 && !$yarn_plan_day_chk)
				 {
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Dyeing======================================= */
	foreach($shafi_dyeing_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $dyeing_month_prod=$row['dyeing_prod']; 
		   $dyeing_month_plan=$row['dyeing_plan'];
			$dyeing_month_kpi_per=$dyeing_month_prod/$dyeing_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($dyeing_month_plan>0 && $dyeing_month_prod>0)
				 {
					 if($dyeing_month_kpi_per>100)  $dyeing_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing']+=$dyeing_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];

				 if($dyeing_month_plan>0)
				 {
					 $shafi_mon_grand_dyeing_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				 if($dyeing_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk) )
				 {
					 
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Aop======================================= */
	foreach($shafi_aop_month_wise_kpiArr as $comp_id=>$comData)  
	{
	foreach($comData as $loc_id=>$LocData)  
	{
		foreach($LocData as $day_key=>$row)
		{
		   $aop_month_prod=$row['aop_prod']; 
		   $aop_month_plan=$row['aop_plan'];
			$aop_month_kpi_per=$aop_month_prod/$aop_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($aop_month_plan>0 && $aop_month_prod>0)
				 {
					 if($aop_month_kpi_per>100)  $aop_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop']+=$aop_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 
				 if($aop_month_plan>0)
				 {
					 $shafi_mon_grand_aop_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }
				 if($aop_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk) )
				 {
					 
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	//****************Month wise Print======================================= */
	foreach($shafi_print_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	{
	foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	{
		foreach($LocData as $day_key=>$row)
		{
		   $print_month_prod=$row['print_prod']; 
		   $print_month_plan=$row['print_plan'];
			$print_month_kpi_per=$print_month_prod/$print_month_plan*100; 
				$monthYr=date("M-Y",strtotime($day_key));
			 $yr_month=strtoupper($monthYr);
			 $today=strtotime($today_date);
			 $day_chk=strtotime($day_key);
			 if($day_chk<=$today)// as on today
			 {
				 if($print_month_plan>0 && $print_month_prod>0)
				 {
					 if($print_month_kpi_per>100)  $print_month_kpi_per=100;
				 $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print']+=$print_month_kpi_per;
				 }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];

				 if($print_month_plan>0)
				 {
					 $shafi_mon_grand_print_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }
				 if($print_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk))
				 {
					 
					 $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				 }
			 }
			  }
			}
	}
	 //****************Month wise Embrodiory======================================= */
	 foreach($shafi_embr_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	 {
	 foreach($comData as $loc_id=>$LocData) //Month Wise KPI Percentage summary part
	 {
		 foreach($LocData as $day_key=>$row)
		 {
			$emb_month_prod=$row['emb_prod']; 
			$emb_month_plan=$row['emb_plan'];
			 $emb_month_kpi_per=$emb_month_prod/$emb_month_plan*100; 
			 $monthYr=date("M-Y",strtotime($day_key));
			  $yr_month=strtoupper($monthYr);
			  $today=strtotime($today_date);
			  $day_chk=strtotime($day_key);
			  if($day_chk<=$today)// as on today
			  {
				  if($emb_month_plan>0 && $emb_month_prod>0)
				  {
					  if($emb_month_kpi_per>100)  $emb_month_kpi_per=100;
				  $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb']+=$emb_month_kpi_per;
				  }
				 $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
				 $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];

				 if($emb_month_plan>0)
				 {
					 $shafi_mon_grand_emb_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				  if($emb_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk ))
				  {
					 
					  $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				  }
			  }
			   }
			 }
	 }
	 //****************Month wise Wash======================================= */
	 foreach($shafi_wash_month_wise_kpiArr as $comp_id=>$comData)  
	 {
	 foreach($comData as $loc_id=>$LocData)  
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
			  if($day_chk<=$today)// as on today
			  {
				  if($wash_month_plan>0 && $wash_month_prod>0)
				  {
					  if($wash_month_kpi_per>100)  $wash_month_kpi_per=100;
				  $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash']+=$wash_month_kpi_per;
				  }
				  $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				 $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				 $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				 $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
				 $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];
				 $emb_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb_plan'];

				 if($wash_month_plan>0)
				 {
					 $shafi_mon_grand_wash_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				  if($wash_month_plan>0 && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk  && !$emb_plan_day_chk) )
				  {
					 
					  $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				  }
			  }
			   }
			 }
	 }
	 //****************Month wise Fin Transfer Acknoledgement======================================= */
	 foreach($shafi_fin_month_wise_kpiArr as $comp_id=>$comData)  
	 {
	 foreach($comData as $loc_id=>$LocData)  
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
			  if($day_chk<=$today)// as on today
			  {
				  if($fin_month_plan>0 && $fin_month_prod>0)
				  {
					  if($fin_month_kpi_per>100)  $fin_month_kpi_per=100;
				  $shafi_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['fin']+=$fin_month_kpi_per;
				  }
				  $yarn_plan_day_chk= $shafi_yarn_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['yarn_plan'];
				  $knit_plan_day_chk= $shafi_knit_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['knit_plan'];
				  $dyeing_plan_day_chk= $shafi_dyeing_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['dyeing_plan'];
				  $aop_plan_day_chk= $shafi_aop_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['aop_plan'];
				  $print_plan_day_chk= $shafi_print_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['print_plan'];
				  $emb_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['emb_plan'];
				  $wash_plan_day_chk= $shafi_embr_month_wise_kpiArr[$comp_id][$loc_id][$day_key]['wash_plan'];

				  if($fin_month_plan>0)
				 {
					 $shafi_mon_grand_fin_event_kpi_perArr[$comp_id][$loc_id][$day_key]=1;
				 }

				  if($fin_month_plan>0  && (!$yarn_plan_day_chk && !$knit_plan_day_chk && !$dyeing_plan_day_chk && !$aop_plan_day_chk && !$print_plan_day_chk  && !$emb_plan_day_chk && !$wash_plan_day_chk) )
				  {
					 
					  $shafi_gbl_num_of_plan_daysArr[$comp_id][$loc_id][$yr_month]++;
				  }
			  }
			   }
			 }
	 }
		// echo "<pre>";
	  //  print_r($shafi_month_wise_kpiArr);

	  //=========Month wise all event summation====================
	  asort($shafi_month_wise_kpiArr);
	 foreach($shafi_month_wise_kpiArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
	 {
		 foreach($comData as $unit_id=>$monData)  
		 {
			 foreach($monData as $day_key=>$pers)  
			 {
				 $event_yarn_count=0;$event_knit_count=0;$event_dyeing_count=0;$event_aop_count=0;$event_print_count=0;$event_emb_count=0;$event_wash_count=0;
				 $event_yarn_count=$shafi_mon_grand_yarn_event_kpi_perArr[$comp_id][$unit_id][$day_key];//$mon_grand_qc_event_kpi_perArr
				 $event_knit_count=$shafi_mon_grand_knit_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_dyeing_count=$shafi_mon_grand_dyeing_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_aop_count=$shafi_mon_grand_aop_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_print_count=$shafi_mon_grand_print_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_emb_count=$shafi_mon_grand_emb_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_fin_count=$shafi_mon_grand_fin_event_kpi_perArr[$comp_id][$unit_id][$day_key];
				 $event_wash_count=$shafi_mon_grand_wash_event_kpi_perArr[$comp_id][$unit_id][$day_key];

				 $shafi_event_mon_count= $event_yarn_count+$event_knit_count+$event_dyeing_count+$event_aop_count+$event_print_count+$event_fin_count+$event_wash_count+$event_emb_count;
				 $shafi_all_kpi_per=$pers['yarn']+$pers['knit']+$pers['dyeing']+$pers['aop']+$pers['print']+$pers['emb']+$pers['fin']+$pers['wash'];
				  if($day_key=='29-May-2023')
				  {
					 // echo $day_key.'='.$pers['yarn'].'='.$pers['knit'].'='.$pers['dyeing'].'='.$pers['aop'].'='.$pers['print'].'='.$pers['emb'].'='.$pers['fin'].'='.$pers['wash'].'/'.$shafi_event_mon_count.'=Wash='. $event_wash_count.'<br>';
				  }
				 // echo $day_key.'='.$event_del_count.'='.$per['deli'].'/'.$nay_event_mon_count.'<br>';
				 $shafi_gbl_comp_avg_perArr[$comp_id][$unit_id][$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
				 //$all_avg_perArr[$day_key]+=$shafi_all_kpi_per/$shafi_event_mon_count;
			 }
		 }
	 }
	 
	   
// echo "<pre>";
 //print_r($all_avg_perArr);
//echo "<pre>";
foreach($shafi_gbl_comp_avg_perArr as $comp_id=>$comData) //Month Wise KPI Percentage summary part
{
   foreach($comData as $unitid=>$monData)  
   {
	   foreach($monData as $day_key=>$val)  
	   {
		  $monthYr=date("M-Y",strtotime($day_key));
		  $yr_month=strtoupper($monthYr);
	   	// echo $day_key.'='.$val.'<br>';
		  $shafi_gbl_comp_mon_avg_perArr[$comp_id][$unitid][$yr_month]+=$val;
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
	   if($cbo_company_id==2) $com_ttl="Shafipur"; 
	   else $com_ttl="Nayapara Fabric";  
		?>
          <table width="<? echo $tbl_width_unit;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="11" align="center" ><strong style="font-size:19px"><?php //echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="11" align="center"><strong style="font-size:14px"><?=$report_title.'<br>'.$company_arr[$cbo_company_id];?> </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width_unit;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'kpi_unit_report', '')"> -<b>KPI Unit  <?=$com_ttl;?>[<? echo $from_year; ?>]</b></h3>
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
							$shafi_gbl_comp_avg_kip=$shafi_gbl_comp_avg_perArr[$cbo_company_id][$unitid][$date];//shafi_gbl_comp_avg_perArr
							$month_kpi_per=$shafi_gbl_comp_avg_kip;
							
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
					$tot_mon_days=$shafi_gbl_num_of_plan_daysArr[$cbo_company_id][$unitid][$year_mon];
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
    
 