<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
//require_once('../../../../includes/class4/class.conditions.php');
//require_once('../../../../includes/class4/class.reports.php');
//require_once('../../../../includes/class4/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name","id,location_name", 1, "-All-", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-All-", $selected, "buyerConfig(this.value)" );
	exit();
}

if ($action=="load_drop_down_client")
{
	$sql="select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))   group by a.id, a.buyer_name order by a.buyer_name";
	//echo $sql;
	echo create_drop_down( "cbo_client", 120, $sql,"id,buyer_name", 1, "--ALL--", $selected, "clientConfig(this.value)" );

	exit();
}
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 group by id, season_name order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}


$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$client_id=str_replace("'","",$cbo_client);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$orderStatus=str_replace("'","",$cbo_order_status);
	$fiscal_year=str_replace("'","",$cbo_fiscal_year);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	//echo $reporttype.'=kkk'; die;
	
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond="and a.buyer_name='$buyer_id'";
	
	$exfirstYear=explode('-',$fiscal_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	
	//var_dump($yearMonth_arr); die;
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	
	$tnaTaskSql="select task_name, task_short_name from lib_tna_task where status_active=1 and is_deleted=0 and task_name in (9,10,48,52,60,61,63,73,84,267,268,90,88) order by task_sequence_no ASC";
	$tnaTaskArr=array();
	$tnaTaskRes=sql_select($tnaTaskSql);
	foreach($tnaTaskRes as $trow)
	{
		$tnaTaskArr[$trow[csf("task_name")]]=$trow[csf("task_short_name")];
	}
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($cbo_season_id!=0) $jobSeasonCond="and a.season_buyer_wise='$cbo_season_id'"; else $jobSeasonCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
	
	$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	
	$sql_po="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number in (9,10,48,52,60,61,63,73,84,267,268,86,90,88) $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond $jobSeasonCond"; //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315') and a.job_no in ('FAL-21-00240') 
	//echo $sql_po; //die; //and a.job_no='FAL-21-00239' $dateCond
	
	$sqlPoRes=sql_select($sql_po); $poidstr=""; $jobId=""; $dateDataArr=array(); 
	$job_no_arr=array(); 
	$buyer_against_po = array();
	$powisejobarr = array();
	foreach($sqlPoRes as $row)
	{
		array_push($job_no_arr, $row['JOB_NO'])	;
		$poidstr.=$row['ID'].',';
		if($jobId=="") $jobId="'".$row["JOBID"]."'"; else $jobId.=",'".$row["JOBID"]."'";
		
		$total_date=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
		for($k=0; $k<$total_date; $k++)
		{
			$newdate=add_date(date("Y-m-d",strtotime($row["TASK_START_DATE"])),$k);
			$monthDay=date("Y-m",strtotime($newdate));
			$dateDataArr[$row['ID']][$row["TASK_NUMBER"]][$newdate]=$monthDay;
		}

		$buyer_against_po[$row['ID']]=$row['BUYER_NAME'];
		$powisejobarr[$row['ID']] = $row['JOB_NO'];
	}
	
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $taskno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$taskno][$sdate]+=1;
			}
		}
	}
	/*echo "<pre>";*/
	//print_r($powiseNoofDaysArr); die;
	
	$po_ids=array_filter(array_unique(explode(",",$poidstr)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	//$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	//$jobidRatioCond=where_con_using_array($jobIds,0,"job_id"); 
	$idCond=where_con_using_array($jobIds,0,"a.id"); 
	
	$job_no_cond=where_con_using_array(array_unique($job_no_arr),1,"job_no");
	unset($job_no_arr);

	
	

	$actualPoSql="select a.id,a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,d.unit_price as UNIT_PRICE from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id  and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond  $jobSeasonCond";

	//echo $actualPoSql;

	$actualPoSqlRes=sql_select($actualPoSql);

	$job_id_new_arr=array();
	foreach ($actualPoSqlRes as $row) {
		array_push($job_id_new_arr, $row[csf('id')]);
	}

	$jobIdArr=array_merge($jobIds,$job_id_new_arr);
	$jobidRatioCond=where_con_using_array($jobIdArr,0,"job_id"); 

	$jobidColCond=where_con_using_array($jobIdArr,0,"a.id");


	$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobidRatioCond";
	//echo $gmtsitemRatioSql; die;
	$job_no_cond=str_replace("job_no", "a.job_no", $job_no_cond);
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	$job_wise_set_item=array();
	$jobwisesmvarr = array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
		$job_wise_set_item[$row['JOB_ID']]++;
		$jobwisesmvarr[$row['JOBNO']] = $row['SMV_PCS'];
	}
	unset($gmtsitemRatioSqlRes);

	

	$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");
	$tna_plan_target_sql = "select TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE,UOM_ID,SOURCE_ID  from TNA_PLAN_TARGET where  task_type=1 and PLAN_DATE between '$startDate' and '$endDate' $po_id_cond";

	// $tna_plan_target_sql = "select d.TNA_MST_ID,d.TASK_ID,d.TASK_TYPE, d.PO_ID,d.PLAN_QTY,d.PLAN_DATE,d.UOM_ID,d.SOURCE_ID
	//  from wo_po_details_master a, wo_po_break_down b, tna_process_mst c,TNA_PLAN_TARGET d  where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number in (9,10,48,52,60,61,63,73,84,267,268,86,90,88) and d.po_id = b.id and c.id = d.TNA_MST_ID and c.task_number = d.TASK_ID and d.task_type=1 and d.PLAN_DATE between '$startDate' and '$endDate'  $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond $jobSeasonCond";

   // if($_SESSION['logic_erp']['user_id'] == 1)
   // {
   // 	 echo $tna_plan_target_sql;
   // }
	//echo $tna_plan_target_sql;
	$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
	$tna_plan_target_arr = array();
	foreach( $tna_plan_target_sql_res as $row ) 
	{
		$dmyKey = date('m-Y', strtotime($row['PLAN_DATE']));
		if(!empty($row['PLAN_QTY']))
		{
			$index = $row['TASK_ID'];
			if($row['TASK_ID'] == 73)
			{
				$uom = $row['UOM_ID'];
				if($uom == 27)
				{
					$uom = 23;
				}
				$index = $row['TASK_ID'] . "*". $uom . "*". $row['SOURCE_ID'];
			}
			if($row['TASK_ID'] == 86)
			{
				
				 
				$tna_plan_target_arr[$index][$dmyKey]+= $row['PLAN_QTY']; 
				$index_min = 902;
				$tna_plan_target_arr[$index_min][$dmyKey]+= ($row['PLAN_QTY'] * $jobwisesmvarr[$powisejobarr[$row['PO_ID']]]);
				//echo "<pre>".$powisejobarr[$row['PO_ID']]."=>".$jobwisesmvarr[$powisejobarr[$row['PO_ID']]]."=>".($row['PLAN_QTY'] * $jobwisesmvarr[$powisejobarr[$row['PO_ID']]])."</pre>";
			}
			else
			{
				$tna_plan_target_arr[$index][$dmyKey]+= $row['PLAN_QTY']; 
			}
			
		}
		
		//echo "<pre>tna ".$index."=>".$row['PLAN_QTY']."</pre>";
	}

	$poAvgRateArr=array();
	//$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.company_name ='$company_id' $jobidColCond";

	$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a join  wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on b.id=c.po_break_down_id left join  wo_pre_cost_dtls d on a.id=d.job_id and d.is_deleted=0 and d.status_active=1 where  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name ='$company_id' $jobidColCond";
	//echo $sqlpoarr; die; //and a.job_no='$job_no'

	$sqlpodata = sql_select($sqlpoarr);
	//print_r($sqlpodata);
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
	foreach($sqlpodata as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		$poAvgRateArr[$row["ID"]]=$row["UNIT_PRICE"];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		$smvmin=0;
		$smvmin=$row['ORDER_QUANTITY']*$smvJobArr[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
		$smvArr[$row['JOB_ID']][$row['ID']]+=$smvmin;
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	}
	unset($sqlpodata);
	
	$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlContrast; die;
	$sqlContrastRes = sql_select($sqlContrast);
	$sqlContrastArr=array(); $colorContrastArr=array();
	foreach($sqlContrastRes as $row)
	{
		$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		$colorContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['contras'][$row['CONTRAST_COLOR_ID']]=$row['CONTRAST_COLOR_ID'];
	}
	unset($sqlContrastRes);
	//print_r($colorNoArr);
	
	//Stripe Details
	$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlStripe; die;
	$sqlStripeRes = sql_select($sqlStripe);
	$sqlStripeArr=array();
	foreach($sqlStripeRes as $row)
	{
		$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		//$colorNoArr[$row['JOB_ID']][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
	}
	unset($sqlStripeRes);
	
	//Fabric Details
	$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, a.color_break_down as COLOR_BREAK_DOWN, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobidCond";
	//echo $sqlfab; die;
	$sqlfabRes = sql_select($sqlfab);
	$fabIdWiseGmtsDataArr=array(); $reqFabArr=array(); $colorNoArr=array();
	foreach($sqlfabRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
		
		$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
		$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
		$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
		$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
		$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
		$colorTypeId=$row['COLOR_TYPE_ID'];
		$jobcolorstr=$row['JOB_ID'];
		$stripe_color=$sqlStripeArr[$row['JOB_ID']]['strip'];
		/*if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
		{
			if (!in_array($jobcolorstr,$jobcolorArr) )
			{
				$noofStripeColor=count($stripe_color);
				$noofColorArr[$row['JOB_ID']][$row['POID']]+=$noofStripeColor;
				$jobcolorArr[]=$jobcolorstr; 
			}
			//$colorNoArr[$row['JOB_ID']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
		}
		else
		{*/
			if($row['COLOR_SIZE_SENSITIVE']==3)
			{
				foreach($colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras'] as $contrast)
				{
					$colorNoArr[$row['JOB_ID']][$contrast]=$contrast;//$colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras']);
				}
			}
			else
			{
				$colorNoArr[$row['JOB_ID']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
				/*if (!in_array($jobcolorstr,$jobcolorArr) )
				{
					$noofColorArr[$row['JOB_ID']][$row['POID']]+=1;
					$jobcolorArr[]=$jobcolorstr; 
				}*/
			}
		//}
		
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
		$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
		//echo $greyReq.'='.$planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'<br>';
		
		$finAmt=$finReq*$row['RATE'];
		$greyAmt=$greyReq*$row['RATE'];
		
		//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
		if($row['FABRIC_SOURCE']==1)
		{
			$reqFabArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
			if($row['UOM']==12)
			{

				$reqFabArr[$row['JOB_ID']][$row['POID']]['f_fabric_rcvd_mfg_kg']+=$finReq;
			}
		}
		else if($row['FABRIC_SOURCE']==2)
		{
			if($row['UOM']==12)
			{

				$reqFabArr[$row['JOB_ID']][$row['POID']]['f_fabric_rcvd_pur_kg']+=$finReq;
			}
			else if($row['UOM']==1)
			{
				$reqFabArr[$row['JOB_ID']][$row['POID']]['f_fabric_rcvd_pur_pcs']+=$finReq;
			}
			else if($row['UOM']==23 || $row['UOM']==27)
			{
				$reqFabArr[$row['JOB_ID']][$row['POID']]['f_fabric_rcvd_pur_yds_meter']+=$finReq;
			}
		}
		$reqFabArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
	}
	unset($sqlfabRes);
	
	$noofColorArr=array(); 
	foreach($colorNoArr as $jobid=>$colordata)
	{
		$c=0;
		foreach($colordata as $colorid)
		{
			$c++;
			//echo $jobid.'='.$colorid.'<br>';
		}
		$noofColorArr[$jobid]=$c;
	}
	//print_r($noofColorArr); //die;
	$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 

	from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
	//echo $sqlYarn;
	$sqlYarnRes = sql_select($sqlYarn);
	foreach($sqlYarnRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
		
		$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
		
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
		
		$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
		
		$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
		
		$yarnAmt=$yarnReq*$row['RATE'];
		
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
		//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
	}
	unset($sqlYarnRes); 
	
	//Convaersion Details
	$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
	from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
	//echo $sqlConv; die;
	$sqlConvRes = sql_select($sqlConv);
	$convConsRateArr=array(); $convReqArr=array();
	foreach($sqlConvRes as $row)
	{
		$id=$row['CONVERTION_ID'];
		$colorBreakDown=$row['COLOR_BREAK_DOWN'];
		if($colorBreakDown !="")
		{
			$arr_1=explode("__",$colorBreakDown);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
				$arr_2=explode("_",$arr_1[$ci]);
				$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
				$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
			}
		}
	}
	//echo "ff"; die; 
	//$cv=0;
	foreach($sqlConvRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
		$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
		
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
		
		$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
		$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
		$consProcessId=$row['CONS_PROCESS'];
		$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
		//echo $colorTypeId.'='.count($stripe_color).'<br>';
		if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
		{
			$qnty=0; $convrate=0;
			foreach($stripe_color as $stripe_color_id)
			{
				$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
				$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
				
				$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
				
				//$cv++;
				$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
				//echo "==".$planQty.'=='.$requirment.'=='.$qnty.'=='.$convrate.'<br>';
	
				if($convrate>0){
					$reqqnty+=$qnty;
					$convAmt+=$qnty*$convrate;
				}
			}
		}
		else
		{
			$convrate=$requirment=$reqqnty=0;
			$rateColorId=$row['COLOR_NUMBER_ID'];
			if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
	
			if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
			//echo $row['COLOR_NUMBER_ID'].'=='.$rateColorId.'=='.$convrate.'<br>';
			$r=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['cons'];;
			if($convrate>0){
				$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
				//echo $planQty.'=='.$itemRatio.'=='.$requirment.'=='.$costingPer.'<br>';
				$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
				$reqqnty+=$qnty;
				$convAmt+=$qnty*$convrate;
			}
		}
		
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_qty']+=$reqqnty;
		//$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_amt']+=$convAmt;
	}
	unset($sqlConvRes); 
	//echo $cv."<pre>"; 
	//print_r($convReqArr[4700][5502][30]);
	
	$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
	from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
	where 1=1 and a.cons_dzn_gmts>0 and
	a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
	//echo $sqlEmb; die;
	$sqlEmbRes = sql_select($sqlEmb);
	$embReqArr=array();
	foreach($sqlEmbRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
		
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		$budget_on=$row['BUDGET_ON'];
		
		$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
		//print_r($poCountryId);
		$calPoPlanQty=0;
		
		if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
		{
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
			if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
			$consQty=0;
			$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
			$consQnty+=$consQty;
			
			$consAmt=$consQty*$row['RATE'];
		}
		else
		{
			$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
			$consQnty=$consAmt=0;
			foreach($poCountryId as $countryId)
			{
				if(in_array($countryId, $countryIdArr))
				{
					$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					
					if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
					$consQty=0;
					$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
					$consQnty+=$consQty;
					//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
					$consAmt+=$consQty*$row['RATE'];
				}
			}
		}
		
		//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
		$embReqArr[$row['JOB_ID']][$row['POID']][$row['EMB_NAME']]['embqty']+=$consQnty;
	}
	unset($sqlEmbRes); 
	$monthDataArr=array(); $monthDtlsArr=array();
	foreach($sqlPoRes as $row)
	{
		$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
		foreach($powiseNoofDaysArr[$row["ID"]][$row["TASK_NUMBER"]] as $sdate=>$monthinDays)
		{
			//echo $sdate.'='.$monthinDays.'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'<br>';
			$qty=0;
			if($row["TASK_NUMBER"]==9)//Labdip Submission
			{
				$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
				/*if($sdate=='2020-09')
				{
					echo $row['JOB_NO'].'<br>';
				}*/
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==10)//Labdip Approval
			{
				$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'<br>';
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==48)//Yarn Allocation
			{
				$qty=(($reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty']/$noofDays)*$monthinDays);
				/*if($sdate=='2020-08')
				{
				echo $row['JOB_NO'].'='.$row['ID'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty'].'='.$sdate.'='.$monthinDays.'<br>';
				}*/
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==52)//Dyed Yarn Receive
			{
				$qty=(($convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty']/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$row['ID'].'='.$convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty'].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==60)//Grey Production
			{
				//echo $row['JOB_NO'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'='.$noofDays.'='.$monthinDays.'<br>';
				$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodgrey_qty']/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==61)//Dyeing
			{
				$qty=(($convReqArr[$row['JOBID']][$row['ID']][31]['conv_qty']/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==63)//AOP Receive
			{
				$qty=(($convReqArr[$row['JOBID']][$row['ID']][35]['conv_qty']/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==73)//F. Fabric Recvd
			{
				$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodfin_qty']/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;

				$f_fabric_rcvd_mfg_kg=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_mfg_kg']/$noofDays)*$monthinDays);
				$monthDataArr['f_fabric_rcvd_mfg_kg'][$sdate]['qty']+=$f_fabric_rcvd_mfg_kg;

				$f_fabric_rcvd_pur_kg=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_pur_kg']/$noofDays)*$monthinDays);
				$monthDataArr['f_fabric_rcvd_pur_kg'][$sdate]['qty']+=$f_fabric_rcvd_pur_kg;

				$f_fabric_rcvd_pur_pcs=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_pur_pcs']/$noofDays)*$monthinDays);
				$monthDataArr['f_fabric_rcvd_pur_pcs'][$sdate]['qty']+=$f_fabric_rcvd_pur_pcs;

				$f_fabric_rcvd_pur_yds_meter=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_pur_yds_meter']/$noofDays)*$monthinDays);
				$monthDataArr['f_fabric_rcvd_pur_yds_meter'][$sdate]['qty']+=$f_fabric_rcvd_pur_yds_meter;

			}
			if($row["TASK_NUMBER"]==84)//Cutting QC
			{
				$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==267)//Printing Receive
			{
				$qty=(($embReqArr[$row['JOBID']][$row['ID']][1]['embqty']/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$embReqArr[$row['JOBID']][$row['ID']][1]['embqty'].'='.$noofDays.'='.$monthinDays.'<br>';
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==268)//Embroidery Receive
			{
				$qty=(($embReqArr[$row['JOBID']][$row['ID']][2]['embqty']/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==86)//Sewing[Pcs]
			{
				$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$row["PO_QUANTITY"].'='.$noofDays.'='.$monthinDays.'<br>';
				$monthDataArr[86][$sdate]['qty']+=$qty;
				
				$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				$monthDataArr[902][$sdate]['qty']+=$sewMin;
			}
			if($row["TASK_NUMBER"]==90)//Garments Wash Rcv
			{
				$qty=(($embReqArr[$row['JOBID']][$row['ID']][3]['embqty']/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==88)//Garments Finishing
			{
				$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);
				$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
			}
		}
	}
	

	// cut from here
    $actualPoArr=array();
	$task_wise_total=array();
	foreach($actualPoSqlRes as $arow)
	{
		$monthDay="";
		$monthDay=date("Y-m",strtotime($arow["ACC_SHIP_DATE"]));//Shipment [Pcs]
		$monthDataArr[903][$monthDay]['qty']+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[904][$monthDay]['qty']+=$shipmentMin;
		//$shpmentVal=$arow["ACC_PO_QTY"]*$poAvgRateArr[$arow["PO_BREAK_DOWN_ID"]];//Shipment [Value]
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[905][$monthDay]['qty']+=$shpmentVal;
		$monthDataArr[905][$monthDay]['JOBNO'].=$arow['JOBNO'].",";
		$task_wise_total[903]+=$arow["ACC_PO_QTY"];
		$task_wise_total[904]+=$shipmentMin;
		$task_wise_total[905]+=$shpmentVal;
	}
	//echo $monthDataArr[905]['2021-10']['JOBNO'];
	unset($actualPoSqlRes);
	/*echo "<pre>";
	print_r($monthDataArr[903]);*/
	//die;
	$capacity_data=array();
	/*if(empty($cbo_season_id))
	{

		if(empty($buyer_id))
		{
			if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
			if($client_id!=0) $ClientCond=" and c.client_id='$client_id'"; else $ClientCond="";
			
			
			$dateCond="and (b.from_date between '".$startDate."' and '".$endDate."' or b.to_date between '".$startDate."' and '".$endDate."')";
			//$dateCond="and (b.from_date >= '".$startDate."' and  b.to_date <= '".$endDate."')";

			$sql="SELECT  a.company_id,a.location_id,a.floor_id,a.line_number,b.id, b.mst_id, b.from_date, b.to_date, b.man_power, b.operator, b.helper, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity,b.target_efficiency FROM prod_resource_mst a,prod_resource_dtls_mast b,lib_prod_floor c where a.id=b.mst_id and a.floor_id=c.id and a.company_id=$cbo_company_id  and b.is_deleted=0 and a.is_deleted=0 and c.is_deleted=0 $LocationCond $ClientCond $dateCond";
			//echo $sql;
			$res=sql_select($sql);
			foreach ($res as $row) 
			{
				$from_year_month=date('Y-m',strtotime($row[csf('from_date')]));
				$to_year_month=date('Y-m',strtotime($row[csf('to_date')]));
				$monthDay=date("Y-m",strtotime($row[csf('from_date')]));
				//echo "<pre>".$from_year_month."=>".$to_year_month."</pre>";
				if(date('Y-m-d',strtotime($row[csf('from_date')]))==date('Y-m-d',strtotime($row[csf('to_date')])))
				{
					$monthDataArr[2000][$monthDay]['qty']+=($row[csf('working_hour')]*$row[csf('man_power')]*60);
				}
				else if($from_year_month==$to_year_month)
				{
					
					$datediff=date_diff(date_create(date('Y-m-d',strtotime($row[csf('from_date')]))),date_create(date('Y-m-d',strtotime($row[csf('to_date')]))));
					$totalday=$datediff->format("%a");
					$monthDataArr[2000][$monthDay]['qty']+=(($totalday+1)*$row[csf('working_hour')]*$row[csf('man_power')]*60);
					$qnty=(($totalday+1)*$row[csf('working_hour')]*$row[csf('man_power')]*60);
					//echo date_diff(date_create(date('Y-m-d',strtotime($row[csf('from_date')]))),date_create(date('Y-m-d',strtotime($row[csf('to_date')]))));
					//echo $totalday;
				}
				else
				{
					$from_year=date('Y',strtotime($row[csf('from_date')]));
					$to_year=date('Y',strtotime($row[csf('to_date')]));
					$from_month=date('m',strtotime($row[csf('from_date')]));
					$to_month=date('m',strtotime($row[csf('to_date')]));

					
					//echo "<pre>2</pre>";
					for($year_i=$from_year;$year_i<=$to_year;$year_i++)
					{
						if($year_i==$to_year)
						{
							$last_month=$to_month;
						}
						else{
							$last_month=12;
						}

						if($year_i==$from_year)
						{
							$first_month=$from_month;
						}
						else
						{
							$first_month=1;
						}

						for($month_i=$first_month;$month_i<=$last_month;$month_i++)
						{
							$monthDay=$year_i."-".str_pad($month_i,2,"0",STR_PAD_LEFT);
							$firstDay='';
							if($year_i==$from_year && $month_i==$from_month)
							{
								$firstDay=date_create(date('Y-m-d',strtotime($row[csf('from_date')])));
							}
							else
							{
								$firstDay=date_create(date('Y-m-d',strtotime($year_i."-".str_pad($month_i,2,"0",STR_PAD_LEFT)."-01")));
							}

							if($year_i==$to_year && $month_i==$to_month)
							{
								$lastDay=date_create(date('Y-m-d',strtotime($row[csf('to_date')])));
							}
							else
							{
								$day=cal_days_in_month(CAL_GREGORIAN,str_pad($month_i,2,"0",STR_PAD_LEFT),$year_i);
								$lastDay=date_create(date('Y-m-d',strtotime($year_i."-".str_pad($month_i,2,"0",STR_PAD_LEFT)."-".$day)));
							}
							$qty=0;
							if($firstDay==$lastDay)
							{
								$qty=($row[csf('working_hour')]*$row[csf('man_power')]*60);
							}
							else
							{
								$datediff=date_diff($firstDay,$lastDay);
								$totalday=$datediff->format("%a");
								$qty=(($totalday+1)*$row[csf('working_hour')]*$row[csf('man_power')]*60);
							}

							$monthDataArr[2000][$monthDay]['qty']+=$qty;
							
							//echo "<pre>$monthDay=$qty</pre>";
						}
					}
				}
				
			}
		}
		else
		{
			if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
			if($buyer_id!=0) $BuyerCond="and b.buyer_id='$buyer_id'"; else $BuyerCond="";
			//$dateCond=" and (b.date_calc between '".$startDate."' and '".$endDate."')";
			$exfirstYear=explode('-',$fiscal_year);
			$firstYear=$exfirstYear[0];
			$lastYear=$exfirstYear[1];
			$sql="SELECT b.buyer_id, (b.allocation_percentage) as per,a.month_id,a.year_id,a.location_id
				  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
				  WHERE a.id = b.mst_id
				  		and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
				        and a.company_id=$company_id
				        $LocationCond
				        $BuyerCond
				  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
		    //echo $sql;
			$res=sql_select($sql);
			$buyer_percentage=array();
			foreach ($res as $row) 
			{
				//$monthDay=date("Y-m",strtotime($row[csf('year_id')].'-'.$row[csf('month_id')].'-01'));
				$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
				$buyer_percentage[2000][$monthDay][$row[csf('location_id')]]+=$row[csf('per')];
			}
			//print_r($buyer_percentage[2000]);
			$sql_s="SELECT a.id,
				         b.id AS bid,
				         b.month_id,
				         b.date_calc,
				         b.day_status,
				         b.no_of_line,
				         b.capacity_min,
				         b.capacity_pcs,
				         a.year,
				         a.location_id
				        
				    FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b
				    WHERE     a.comapny_id = $company_id
				    	 AND ((a.year=$firstYear and b.month_id>6) or (a.year=$lastYear and b.month_id<7))
				         AND a.status_active = 1
				         AND a.is_deleted = 0
				         AND a.id = b.mst_id
				         $LocationCond 
				    ";
		    //echo $sql_s;
			$res_s=sql_select($sql_s);

			foreach ($res_s as $row) 
			{
				//$monthDay=date("Y-m",strtotime($row[csf('date_calc')]));
				//$monthDay=date("Y-m",strtotime($row[csf('year')].'-'.$row[csf('month_id')].'-01'));
				$monthDay=$row[csf('year')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
				$monthDataArr[2000][$monthDay]['qty']+=(($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay][$row[csf('location_id')]])/100);
				//echo "<pre>";
				//echo $row[csf('capacity_min')]." ";
				//echo (($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay])/100);
				//echo "</pre>";
			}
		}
	}*/
	$exfirstYear=explode('-',$fiscal_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
	if($buyer_id!=0) $BuyerCond="and b.buyer_id='$buyer_id'"; else $BuyerCond="";
	if($buyer_id!=0)
	{
		$sql="SELECT b.buyer_id, (b.allocation_percentage) as per, a.month_id,a.year_id,a.location_id
		  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
		  WHERE a.id = b.mst_id
				and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
				and a.company_id=$company_id $LocationCond $BuyerCond
		  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
		//echo $sql;
		$res=sql_select($sql);
		$buyer_percentage=array();
		foreach ($res as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('year_id')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			$buyer_percentage[2000][$monthDay][$row[csf('location_id')]]=$row[csf('per')];
			
			//$monthDataArr[2000][$monthDay]['qty']+=($capAllowcationArr[2000][$monthDay]['qty']*$row[csf('per')])/100;
		}
	}
	
	
	//$dateCond=" and (b.date_calc between '".$startDate."' and '".$endDate."')";
	
	
	$sql_s="SELECT a.id, b.id AS bid, b.month_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs, a.year, a.location_id
			FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b
			WHERE a.comapny_id = $company_id
				 AND ((a.year=$firstYear and b.month_id>6) or (a.year=$lastYear and b.month_id<7))
				 AND a.status_active = 1 AND a.is_deleted = 0 AND a.id = b.mst_id $LocationCond";
	//echo $sql_s;
	$res_s=sql_select($sql_s);
	$capAllowcationArr=array();
	foreach ($res_s as $row) 
	{
		//$monthDay=date("Y-m",strtotime($row[csf('date_calc')]));
		//$monthDay=date("Y-m",strtotime($row[csf('year')].'-'.$row[csf('month_id')].'-01'));
		$monthDay=$row[csf('year')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
		//$monthDataArr[2000][$monthDay]['qty']+=(($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay][$row[csf('location_id')]])/100);
		
		if($buyer_id!=0)
		{
			$monthDataArr[2000][$monthDay]['qty']+=($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay][$row[csf('location_id')]])/100;
		}
		else
		{
			$monthDataArr[2000][$monthDay]['qty']+=$row[csf('capacity_min')];
		}
		
		
		//$buyer_percentage[2000][$monthDay][$row[csf('location_id')]]
		//echo "<pre>";
		//echo $row[csf('capacity_min')]." ";
		//echo (($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay])/100);
		//echo "</pre>";
	}
	
	$sql="SELECT b.buyer_id, (b.allocation_percentage) as per, a.month_id,a.year_id,a.location_id
		  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
		  WHERE a.id = b.mst_id
				and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
				and a.company_id=$company_id $LocationCond $BuyerCond
		  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
	//echo $sql;
	$res=sql_select($sql);
	$buyer_percentage=array();
	foreach ($res as $row) 
	{
		//$monthDay=date("Y-m",strtotime($row[csf('year_id')].'-'.$row[csf('month_id')].'-01'));
		$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
		//$buyer_percentage[2000][$monthDay][$row[csf('location_id')]]+=$row[csf('per')];
		
		//$monthDataArr[2000][$monthDay]['qty']+=($capAllowcationArr[2000][$monthDay]['qty']*$row[csf('per')])/100;
	}
	//print_r($buyer_percentage[2000]);
	
	

	if($location_id!=0) $LocationCond=" and location_name='$location_id'"; else $LocationCond="";
	if($buyer_id!=0) $BuyerCond="and buyer_id='$buyer_id'"; else $BuyerCond="";
	if($cbo_season_id!=0) $sessionCond="and season_buyer_wise='$cbo_season_id'"; else $sessionCond="";
	$dateCond=" and (est_ship_date between '".$startDate."' and '".$endDate."')";
	$sql_projection="SELECT company_id,
					       buyer_id,
					       offer_qty,
					       est_ship_date,
					       buyer_submit_price,
					       set_smv,
					       location_name,
					       season_buyer_wise
					FROM wo_quotation_inquery
					WHERE is_deleted=0 and company_id=$company_id $LocationCond $BuyerCond $sessionCond $dateCond";
	//echo $sql_projection;
	//print_r($monthDataArr[2000]);
	$res_projection=sql_select($sql_projection);
	foreach ($res_projection as $row) 
	{
		$monthDay=date("Y-m",strtotime($row[csf('est_ship_date')]));
		$monthDataArr[2001][$monthDay]['qty']+=($row[csf('offer_qty')]*$row[csf('set_smv')]);
		$monthDataArr[2002][$monthDay]['qty']+=($row[csf('offer_qty')]*$row[csf('buyer_submit_price')]);
		$monthDataArr[2006][$monthDay]['qty']+=$row[csf('offer_qty')];
	}
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");   
	ob_start();
	if($reporttype==3) $trDisplay="style='display:none'"; else $trDisplay="";
	?>
	
	<fieldset style="width:1340px;" id="content_tna_master_plan">
	    <div style="width:1320px; margin:0px 5px 5px 15px;">
        
	        <div style="width:1320px; font-size:20px; font-weight:bold" align="center">
			<input type="button" id="show_button" class="formbutton" style="width:100px;" value="With PP" onClick="fn_report_generated(1);" />
            <input type="button" id="show_button" class="formbutton" style="width:100px;" value="Without PP" onClick="fn_report_generated(3);" />
            <br />
			<?=$companyArr[str_replace("'","",$company_id)]; ?></div>
	        <div style="width:1320px; font-size:14px; font-weight:bold" align="center"><?=$report_title.':-'.$fiscal_year; ?></div>
	        <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1320px; margin-top:5px" rules="all">
	            <tr style="font-weight:bold; background-color:#9F9">
	                <td colspan="15" align="center">Fiscal Year Details:<?="Client : ".$buyerArr[str_replace("'","",$client_id)]."; Buyer : ".$buyerArr[str_replace("'","",$buyer_id)]; ?></td>
	            </tr>
	            <tr align="center" style="font-weight:bold; font-size:14px; background-color:#CCC">
	            	<td width="75">Group</td>
	                <td width="170">Operation Name/Month</td>
	                <? foreach($yearMonth_arr as $monthindex=>$monthval) { ?>
	                    <td width="80"><?=$monthval; ?></td>
	                <? } ?>
	                <td>Total</td>
	            </tr>
	            <? $i=1;
	           	$capacity_analysis=array(2000=> "Capacity/Allocation(Min)",2006=> "Last Projection (Pcs)",2001=> "Last Projection (Min)",2002=> "Last Projection (Value)",2003=> "Capacity Vs Projection(Min)%",2004=> "Capacity VS Confirm Booking(Min)%",2005=> "Capacity VS sewing Plan(Min)% ");
	           	$row_total_task=array();
	           	$row_task_arr=array(2000=> "Capacity/Allocation(Min)",2001=> "Last Projection (Min)",902=> "Sewing[Minutes]",904=> "Shipment [Minutes]");
	           	foreach($row_task_arr as $taskid=>$taskName)
	            {
	            	foreach($yearMonth_arr as $monthindex=>$monthval) 
	                { 
	                   $row_total_task[$taskid]+=$monthDataArr[$taskid][$monthindex]['qty'];
	                }
	            }
	            foreach($capacity_analysis as $taskid=>$taskName)
	            {
	                $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
					
					if($taskid==2000) $capacityCapPop="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','2000.5','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','buyer_wise_capacity_popup')\"> ".$taskName." <a/>";
					else $capacityCapPop=$taskName;
	                ?>
	                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
	                	<?php if ($taskid==2000): ?>
	                		<td rowspan="<?=count($capacity_analysis)?>" style="justify-content: center;vertical-align: middle;text-align: center;">Capacity & Analysis</td>
	                	<?php endif ?>
	                    <td title="<?=$taskid; ?>"><?=$capacityCapPop; ?></td>
	                    <?
	                    $rowTotal=0; $tdColor="";
	                    foreach($yearMonth_arr as $monthindex=>$monthval) 
	                    { 
	                        //echo $monthindex.'<br>';
	                        $monthWiseQty=$cellEchoQty=$hlinkData=0;
	                        $monthWiseQty=$monthDataArr[$taskid][$monthindex]['qty'];
	                        if($taskid==2003)
	                        {
	                        	$monthWiseQty=fn_number_format(((fn_number_format($monthDataArr[2001][$monthindex]['qty'],4,".","")/fn_number_format($monthDataArr[2000][$monthindex]['qty'],4,".",""))*100),4,".","");
								$rowTotal+=$monthWiseQty;
	                        	if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty,2);
								$hlinkData=fn_number_format($cellEchoQty,2);
	                        }
	                        else if($taskid==2004)
	                        {
	                        	$monthWiseQty=fn_number_format(((fn_number_format($monthDataArr[904][$monthindex]['qty'],4,".","")/fn_number_format($monthDataArr[2000][$monthindex]['qty'],4,".",""))*100),4,".","");
								$rowTotal+=$monthWiseQty;
	                        	if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty,2);
								$hlinkData=fn_number_format($cellEchoQty,2);
								$tdColor='style="color:#FF0000"';
	                        }
	                        else if($taskid==2005)
	                        {
	                        	$monthWiseQty=fn_number_format(((fn_number_format($monthDataArr[902][$monthindex]['qty'],4,".","")/fn_number_format($monthDataArr[2000][$monthindex]['qty'],4,".",""))*100),4,".","");
								$rowTotal+=$monthWiseQty;
	                        	if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty,2);
								$hlinkData=fn_number_format($cellEchoQty,2);
	                        }
							else
							{
								$rowTotal+=$monthWiseQty;
	                        	if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty);
								$hlinkData=$cellEchoQty;
							}
	                        
	                        //$hlinkData=$cellEchoQty;
	                       
	                        ?>
	                        <td align="right" title="<?=$monthWiseQty; ?>" <?=$tdColor;?> ><?=$hlinkData; ?></td>
	                    <? }

	                    	if($taskid==2003)
	                        {
	                        	$rowTotal=fn_number_format(((fn_number_format($row_total_task[2001],4,".","")/fn_number_format($row_total_task[2000],4,".",""))*100),4,".","");
	                        }
	                        else if($taskid==2004)
	                        {
	                        	$rowTotal=fn_number_format(((fn_number_format($row_total_task[904],4,".","")/fn_number_format($row_total_task[2000],4,".",""))*100),4,".","");
	                        }
	                        else if($taskid==2005)
	                        {
	                        	$rowTotal=fn_number_format(((fn_number_format($row_total_task[902],4,".","")/fn_number_format($row_total_task[2000],4,".",""))*100),4,".","");
	                        }
	                    	if($taskid==2000 || $taskid==2001 || $taskid==2002 || $taskid==2003 || $taskid==2004 || $taskid==2005 || $taskid==2006) $capacitytotalpop="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','buyer_wise_capacity_popup')\"> ".number_format($rowTotal,2)." <a/>";
							else $capacitytotalpop=number_format($rowTotal,2);
	                     ?>
	                    <td align="right" <?=$tdColor;?> ><?=$capacitytotalpop; ?></td>
	                </tr>
	                <?
	                $i++;
	            }

	            $confirm_booking=array(903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	            foreach($confirm_booking as $taskid=>$taskName)
	            {
	                $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
	                $client_wise_total="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','client_wise_total_order_quantity_popup')\"> ".$taskName." <a/>";
	                ?>
	                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
	                	<?php if ($taskid==903): ?>
	                		<td rowspan="5" style="justify-content: center;vertical-align: middle;text-align: center;">Confirm Booking</td>
	                	<?php endif ?>
	                    <td title="<?=$taskid; ?>"><?=$client_wise_total; ?></td>
	                    <?
	                    $rowTotal=0;
	                    foreach($yearMonth_arr as $monthindex=>$monthval) 
	                    { 
	                        //echo $monthindex.'<br>';
	                        $monthWiseQty=$cellEchoQty=0;
	                        $monthWiseQty=$monthDataArr[$taskid][$monthindex]['qty'];
	                        $rowTotal+=$monthWiseQty;
	                        if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty);
	                        if($taskid==902 )
	                        {
	                            $hlinkData=$cellEchoQty;
	                        }
	                        else if($taskid==905)
	                        {
	                        	$query_date = $monthindex.'-01';
	                        	$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".date('01-M-Y', strtotime($query_date))."','".date('t-M-Y', strtotime($query_date))."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','shipment_details_by_date')\"> ".$cellEchoQty." <a/>";
	                        }
	                        else if($taskid==904)
	                        {
	                        	$query_date = $monthindex.'-01';
	                        	$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".date('01-M-Y', strtotime($query_date))."','".date('t-M-Y', strtotime($query_date))."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','shipment_summery_by_date')\"> ".$cellEchoQty." <a/>";
	                        }
	                        else if($taskid==903)
	                        {
	                        	$query_date = $monthindex.'-01';
	                        	$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".date('01-M-Y', strtotime($query_date))."','".date('t-M-Y', strtotime($query_date))."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','order_quantity_popup')\"> ".$cellEchoQty." <a/>";
	                        }
	                        else 
	                        {
								$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$monthindex."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','podetails_popup')\"> ".$cellEchoQty." <a/>";
	                        }
	                       
	                        ?>
	                        <td align="right" title="<?=$monthWiseQty; ?>"><?=$hlinkData; ?></td>
	                    <? }
	                    	$hlinkData='';
	                    	/*if($taskid==902 )
	                        {
	                            $hlinkData=$rowTotal;
	                        }
	                        else */if($taskid==903 || $taskid==904 || $taskid==905)
	                        {
	                        	$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','total_order_quantity_popup')\"> ".number_format($rowTotal)." <a/>";
	                        }
	                        else 
	                        {
								$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','total_podetails_popup')\"> ".number_format($rowTotal)." <a/>";
								 //$hlinkData=$rowTotal;
	                        }
	                     ?>
	                    <td align="right"><?=$hlinkData; ?></td>
	                </tr>
	                <?
	                $i++;
	            }
	            ?>
	            <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" >
	                <td title="Avg. SMV=Minutes/Order Qty">Avg. SMV</td>
	                <?
	                $rowTotal=0;
	                foreach($yearMonth_arr as $monthindex=>$monthval) 
	                { 
	                    //echo $monthindex.'<br>';
	                    $monthWiseQty=$cellEchoQty=0;
	                   	$dev=1;
	                   	if($monthDataArr[903][$monthindex]['qty']>0)
	                   	{
	                   		$dev=$monthDataArr[903][$monthindex]['qty'];
	                   	}
	                    $monthWiseQty=($monthDataArr[904][$monthindex]['qty'])/$dev;
	                   // $rowTotal+=$monthWiseQty;
	                    if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty,2);
	                    
	                    ?>
	                    <td align="right" title="<?=number_format($monthWiseQty,2); ?>"><?=$cellEchoQty; ?></td>
	                <? } 

	                	
						$dev=1;
	                   	if($task_wise_total[903]>0)
	                   	{
	                   		$dev=$task_wise_total[903];
	                   	}
	                    $rowTotal=($task_wise_total[904])/$dev;

	                ?>
	                <td align="right"><?=number_format($rowTotal,2); ?></td>
	            </tr>
	            <?  $i++; $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";?>
	            <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
	                <td title="Avg. FOB=Amount/Order Qty">Avg. FOB</td>
	                <?
	                $rowTotal=0;
	                foreach($yearMonth_arr as $monthindex=>$monthval) 
	                { 
	                    //echo $monthindex.'<br>';
	                    $monthWiseQty=$cellEchoQty=0;
	                    $dev=1;
	                   	if($monthDataArr[903][$monthindex]['qty']>0)
	                   	{
	                   		$dev=$monthDataArr[903][$monthindex]['qty'];
	                   	}
	                    $monthWiseQty=($monthDataArr[905][$monthindex]['qty'])/$dev;
	                   // $rowTotal+=$monthWiseQty;
	                    if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty,2)
	                    
	                    ?>
	                    <td align="right" title="<?=number_format($monthWiseQty,2); ?>"><?=$cellEchoQty; ?></td>
	                <? } ?>
	                <td align="right">
	                	
	                	<?
		                	$dev=1;
		                   	if($task_wise_total[903]>0)
		                   	{
		                   		$dev=$task_wise_total[903];
		                   	}
		                    $rowTotal=($task_wise_total[905])/$dev;

	                	?>
	                	<?=number_format($rowTotal,2); ?>	
	                </td>
	            </tr>
	            <?
	            //$production_planning =array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing");
	            $production_planning1 =array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive");

	            $production_planning2 =array('f_fabric_rcvd_mfg_kg'=> "F. Fabric Rcvd ( MFG ) ( Kg )",'f_fabric_rcvd_pur_kg'=> "F. Fabric Rcvd ( Pur ) ( Kg )",'f_fabric_rcvd_pur_pcs'=> "F. Fabric Rcvd ( Pur ) ( Pcs )",'f_fabric_rcvd_pur_yds_meter'=> "F. Fabric Rcvd ( Pur ) ( yds/meter )");
	            $production_planning3 =array(84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing");
	            $row_span=count($production_planning1)+count($production_planning2)+count($production_planning3);
	            foreach($production_planning1 as $taskid=>$taskName)
	            {
	                $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
	                $client_wise_total="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','client_wise_total')\"> ".$taskName." <a/>";
	                ?>
	                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" <?=$trDisplay; ?>>
	                	<?php if ($taskid==9): ?>
	                		<td rowspan="<?=$row_span;?>" style="justify-content: center;vertical-align: middle;text-align: center;">Production Planning </td>
	                	<?php endif ?>
	                    <td title="<?=$taskid; ?>"><?=$client_wise_total; ?></td>
	                    <?
	                    $rowTotal=0;
	                    foreach($yearMonth_arr as $monthindex=>$monthval) 
	                    { 
	                        //echo $monthindex.'<br>';
	                        $monthWiseQty=$cellEchoQty=0;
	                        //$monthWiseQty=$monthDataArr[$taskid][$monthindex]['qty'];
	                        if($taskid > 10)
	                        {
	                        	$monthWiseQty=$tna_plan_target_arr[$taskid][date('m-Y',strtotime($monthval))];
	                        }
	                        else
	                        {
	                        	$monthWiseQty=$monthDataArr[$taskid][$monthindex]['qty'];
	                        }
	                        

	                        $rowTotal+=$monthWiseQty;
	                        if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty);
	                        if($taskid==902 )
	                        {
	                            $hlinkData=$cellEchoQty;
	                        }
	                        else 
	                        {
								$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$monthindex."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','podetails_popup')\"> ".$cellEchoQty." <a/>";
	                        }
	                       
	                        ?>
	                        <td align="right" title="<?=$monthWiseQty; ?>"><?=$hlinkData; ?></td>
	                    <? }
	                    	$hlinkData='';
	                    	
						    $hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','total_podetails_popup')\"> ".number_format($rowTotal)." <a/>";
	                        
	                     ?>
	                    <td align="right"><?=$hlinkData; ?></td>
	                </tr>
	                <?
	                $i++;
	            }
	            foreach($production_planning2 as $taskid=>$taskName)
	            {
	                $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";

	                if($taskid == 'f_fabric_rcvd_mfg_kg')
	                {
	                	$index = "73*12*1";
	                }
	                else if($taskid == 'f_fabric_rcvd_pur_kg')
	                {
	                	$index = "73*12*2";
	                }
	                else if($taskid == 'f_fabric_rcvd_pur_pcs')
	                {
	                	$index = "73*1*2";
	                }
	                else if($taskid == 'f_fabric_rcvd_pur_yds_meter')
	                {
	                	$index = "73*23*2";
	                }
	                
	                $client_wise_total="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','73','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','client_wise_total','".$taskid."')\"> ".$taskName." <a/>";
	                ?>
	                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" <?=$trDisplay; ?>>
	                    <td title="<?=$taskid; ?>"><?=$client_wise_total; ?></td>
	                    <?
	                    $rowTotal=0;
	                    foreach($yearMonth_arr as $monthindex=>$monthval) 
	                    { 
	                        //echo $monthindex.'<br>';
	                        $monthWiseQty=$cellEchoQty=0;
	                        //$monthWiseQty=$monthDataArr[$taskid][$monthindex]['qty'];
	                        $monthWiseQty=$tna_plan_target_arr[$index][date('m-Y',strtotime($monthval))];
	                        $rowTotal+=$monthWiseQty;
	                        if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty);
	                        if($taskid==902 )
	                        {
	                            $hlinkData=$cellEchoQty;
	                        }
	                        else 
	                        {
								$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$monthindex."','73','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','podetails_popup','".$taskid."')\"> ".$cellEchoQty." <a/>";
	                        }
	                       
	                        ?>
	                        <td align="right" title="<?=$monthWiseQty; ?>"><?=$hlinkData; ?></td>
	                    <? }
	                    	$hlinkData='';
	                    	
						    $hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','73','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','total_podetails_popup','".$taskid."')\"> ".number_format($rowTotal)." <a/>";
	                        
	                     ?>
	                    <td align="right"><?=$hlinkData; ?></td>
	                </tr>
	                <?
	                $i++;
	            }
	            foreach($production_planning3 as $taskid=>$taskName)
	            {
	                $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
	                $client_wise_total="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','client_wise_total')\"> ".$taskName." <a/>";
	                ?>
	                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" <?=$trDisplay; ?>>
	                    <td title="<?=$taskid; ?>"><?=$client_wise_total; ?></td>
	                    <?
	                    $rowTotal=0;
	                    foreach($yearMonth_arr as $monthindex=>$monthval) 
	                    { 
	                        //echo $monthindex.'<br>';
	                        $monthWiseQty=$cellEchoQty=0;
	                        //$monthWiseQty=$monthDataArr[$taskid][$monthindex]['qty'];
	                        $monthWiseQty=$tna_plan_target_arr[$taskid][date('m-Y',strtotime($monthval))];
	                        $rowTotal+=$monthWiseQty;
	                        if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty);
	                        
							$hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$monthindex."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','podetails_popup')\"> ".$cellEchoQty." <a/>";
	                        
	                       
	                        ?>
	                        <td align="right" title="<?=$monthWiseQty; ?>"><?=$hlinkData; ?></td>
	                    <? }
	                    	$hlinkData='';
	                    	
						    $hlinkData="<a href='#' onClick=\"generate_report('".$company_id."','".$fiscal_year."','".$taskid."','".$location_id."','".$buyer_id."','".$orderStatus."','".$client_id."','total_podetails_popup')\"> ".number_format($rowTotal)." <a/>";
	                        
	                     ?>
	                    <td align="right"><?=$hlinkData; ?></td>
	                </tr>
	                <?
	                $i++;
	            }
	             $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
	            ?>
	        </table>
	    </div>
    </fieldset>
    <br>
   
    <?

	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

if($action=="report_generate_master")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_id=str_replace("'","",$cbo_company_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$client_id=str_replace("'","",$cbo_client);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$orderStatus=str_replace("'","",$cbo_order_status);
	$fiscal_year=str_replace("'","",$cbo_fiscal_year);
	
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond="and a.buyer_name='$buyer_id'";
	
	$exfirstYear=explode('-',$fiscal_year);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	
	//var_dump($yearMonth_arr); die;
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

	
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
	
	$dateCond="and (a.insert_date between '".$startDate."' and '".$endDate."' or b.insert_date between '".$startDate."' and '".$endDate."')";
	
	$sql_po="select a.id as JOBID, a.job_no as JOB_NO,  b.id as ID
	 from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond"; //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315') and a.job_no in ('FAL-21-00240') 
	//echo $sql_po; die; //and a.job_no='FAL-21-00239' $dateCond
	 //
	$sqlPoRes=sql_select($sql_po); $poidstr=""; $jobId=""; $dateDataArr=array(); 
	$job_no_arr=array();
	foreach($sqlPoRes as $row)
	{
		array_push($job_no_arr, $row['JOB_NO'])	;
		$poidstr.=$row['ID'].',';
		if($jobId=="") $jobId="'".$row["JOBID"]."'"; else $jobId.=",'".$row["JOBID"]."'";
	}
	
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $taskno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$taskno][$sdate]+=1;
			}
		}
	}
	/*echo "<pre>";*/
	//print_r($powiseNoofDaysArr); die;
	
	$po_ids=array_filter(array_unique(explode(",",$poidstr)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	$jobidRatioCond=where_con_using_array($jobIds,0,"job_id"); 
	$idCond=where_con_using_array($jobIds,0,"a.id"); 
	
	$job_no_cond=where_con_using_array(array_unique($job_no_arr),1,"job_no");
	unset($job_no_arr);
	
	$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobidRatioCond";
	//echo $gmtsitemRatioSql; die;
	$job_no_cond=str_replace("job_no", "a.job_no", $job_no_cond);
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	$job_wise_set_item=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
		$job_wise_set_item[$row['JOB_ID']]++;
	}
	unset($gmtsitemRatioSqlRes);
	$poAvgRateArr=array();
	$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.company_name ='$company_id' $jobidColCond";
	//echo $sqlpoarr; die; //and a.job_no='$job_no'
	$sqlpodata = sql_select($sqlpoarr);
	//print_r($sqlpodata);
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
	foreach($sqlpodata as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		$poAvgRateArr[$row["ID"]]=$row["UNIT_PRICE"];
		
		
	}
	unset($sqlpodata);
	
	



	// planning master start here by helal

	$dateCond="and (c.submitted_to_buyer between '".$startDate."' and '".$endDate."' or c.approval_status_date between '".$startDate."' and '".$endDate."')";
	$job_no_cond=str_replace("a.job_no", "c.job_no_mst", $job_no_cond);
	$sql_po="SELECT 
	   				c.color_name_id as COLOR_NAME_ID, 
	   				c.submitted_to_buyer as SUBMITTED_TO_BUYER, 
	   				c.approval_status_date as APPROVAL_STATUS_DATE,
	   				c.approval_status as APPROVAL_STATUS
	 		from  wo_po_lapdip_approval_info c 
	 		where 	
	 				c.status_active=1 and 
	 				c.is_deleted=0  
	 				$dateCond $job_no_cond 

			  group by 
		       c.color_name_id ,
		       c.submitted_to_buyer ,
		       c.approval_status_date ,
		       c.approval_status 
	 ";
	// echo $sql_po;die;
	$res_lap=sql_select($sql_po);

	$master_plan_data=array();

	foreach ($res_lap as $val) 
	{
		$monthDay="";
		if(!empty($val['SUBMITTED_TO_BUYER']))
		{
			
			$monthDay=date("Y-m",strtotime($val["SUBMITTED_TO_BUYER"]));
			$master_plan_data['labdip_submission'][$monthDay]+=1;
		}
		if(!empty($val['APPROVAL_STATUS_DATE']) && $val['APPROVAL_STATUS']==3)
		{
			
			$monthDay=date("Y-m",strtotime($val["APPROVAL_STATUS_DATE"]));
			$master_plan_data['labdip_approval'][$monthDay]+=1;
		}
	}
	unset($sql_po);
	unset($res_lap);


	
	$dateCond="and (c.allocation_date between '".$startDate."' and '".$endDate."')";
	$job_no_cond=str_replace("c.job_no_mst", "c.job_no", $job_no_cond);
	$sql_allocation="SELECT  
	   				c.allocation_date as ALLOCATION_DATE, 
	   				sum(c.qnty) as QNTY
	 		from  inv_material_allocation_mst c 
	 		where 	
	 				c.status_active=1 and 
	 				c.is_deleted=0  and
	 				(c.is_dyied_yarn!=1 or c.is_dyied_yarn is null)
	 				$dateCond $job_no_cond

			  group by 
		       c.allocation_date 
	 ";
	// echo $sql_allocation;die;
	$res_allocation=sql_select($sql_allocation);
	foreach ($res_allocation as $val) 
	{
		$monthDay="";
		if(!empty($val['ALLOCATION_DATE']))
		{
			
			$monthDay=date("Y-m",strtotime($val["ALLOCATION_DATE"]));
			$master_plan_data['yarn_allocation'][$monthDay]+=$val['QNTY'];
		}
		
	}
	unset($sql_allocation);
	unset($res_allocation);
	//$job_no_cond=str_replace("c.job_no_mst", "c.job_no", $job_no_cond);
	$poidCond=str_replace("b.po_break_down_id", "f.po_breakdown_id", $poidCond);
	$dateCond="and (d.receive_date between '".$startDate."' and '".$endDate."')";
	$sqlRcv = "SELECT
				
	  			 d.receive_date as RECEIVE_DATE, 
				 sum(f.quantity) as QNTY
			FROM
				inv_receive_master d
				INNER JOIN inv_transaction e ON d.id = e.mst_id
				INNER JOIN order_wise_pro_details f ON e.id = f.trans_id
			WHERE
					
				 	d.status_active = 1 and
				 	d.is_deleted = 0 and
					d.receive_basis=2 and 
				    d.receive_purpose=2 and
				    e.transaction_type = 1 and
				    e.status_active = 1 and
				    e.is_deleted = 0 and
				    f.trans_type = 1 and
				    f.status_active = 1 and
				    f.is_deleted = 0
				    $dateCond $poidCond 

		    group by 
		       		 d.receive_date 
		";
	//echo $sqlRcv;die;
	$res_rcv=sql_select($sqlRcv);
	foreach($res_rcv as $val) 
	{
		$monthDay="";
		if(!empty($val['RECEIVE_DATE']))
		{
			
			$monthDay=date("Y-m",strtotime($val["RECEIVE_DATE"]));
			$master_plan_data['dyed_yarn_receive'][$monthDay]+=$val['QNTY'];
		}
		
	}
	unset($sqlRcv);
	unset($res_rcv);
	$poidCond=str_replace("f.po_breakdown_id", "e.po_id", $poidCond);
	$dateCond="and (c.receive_date between '".$startDate."' and '".$endDate."')";
	$sqlgrey = "SELECT 
	  			 c.receive_date as RECEIVE_DATE, 
	  			 sum(d.grey_receive_qnty) as QNTY
			  FROM 
			       inv_receive_master c,
			       pro_grey_prod_entry_dtls d,
			       ppl_planning_entry_plan_dtls e
			 WHERE  c.id = d.mst_id
                   AND c.booking_id = e.dtls_id
           			and  c.status_active = 1   
			       AND c.is_deleted = 0
			       AND d.status_active = 1
			       AND d.is_deleted = 0
			       AND e.status_active = 1
			       AND e.is_deleted = 0
			      
			       $dateCond $poidCond

			 group by 
		       		 c.receive_date 
			    ";
	//echo $sqlgrey;die;
	$res_grey=sql_select($sqlgrey);
	foreach($res_grey as $val) 
	{
		$monthDay="";
		if(!empty($val['RECEIVE_DATE']))
		{
			
			$monthDay=date("Y-m",strtotime($val["RECEIVE_DATE"]));
			$master_plan_data['grey_production'][$monthDay]+=$val['QNTY'];
		}
		
	}
	unset($res_grey);
	unset($sqlgrey);
	$poidCond=str_replace("e.po_id", "f.po_id", $poidCond);
	/*$sql_batch=sql_select("SELECT 
         e.id 
    FROM 
         pro_batch_create_mst e,
         pro_batch_create_dtls f
   WHERE     
         
         e.entry_form IN (0, 36)
         AND e.id = f.mst_id
         AND f.status_active = 1
         AND f.is_deleted = 0
         AND e.status_active = 1
         AND e.is_deleted = 0
         AND e.extention_no is null
         $poidCond
	GROUP BY 
         e.id ");

	$batch_id_arr=array();

	foreach ($sql_batch as $row) 
	{
		$batch_id_arr[$row[csf('id')]]=$row[csf('id')];
	}

	$batch_cond=where_con_using_array(array_unique($batch_id_arr),0,"c.batch_id");
	*/

	//$dateCond="and (c.process_end_date between '".$startDate."' and '".$endDate."')";
	$dateCond="and (c.production_date between '".$startDate."' and '".$endDate."')";
	$sql_dye="SELECT 
         SUM (d.batch_qty) AS batch_qty,
         SUM (d.production_qty) AS QNTY,
         c.production_date as PROCESS_END_DATE
    FROM 
         pro_fab_subprocess c,
         pro_fab_subprocess_dtls d
         
   WHERE     
          c.id = d.mst_id
         AND c.entry_form = 35
         AND c.is_deleted = 0
         AND c.status_active = 1
         AND d.is_deleted = 0
         AND d.status_active = 1
         AND c.load_unload_id IN (2)
         $dateCond and c.batch_id in (SELECT 
         e.id 
    FROM 
         pro_batch_create_mst e,
         pro_batch_create_dtls f
   WHERE     
         
         e.entry_form IN (0, 36)
         AND e.id = f.mst_id
         AND f.status_active = 1
         AND f.is_deleted = 0
         AND e.status_active = 1
         AND e.is_deleted = 0
         AND e.extention_no is null
         $poidCond
	GROUP BY 
         e.id )
	GROUP BY 
         c.production_date";
    //echo $sql_dye;die;
    $res_dye=sql_select($sql_dye);
	foreach($res_dye as $val) 
	{
		$monthDay="";
		if(!empty($val['PROCESS_END_DATE']))
		{
			
			$monthDay=date("Y-m",strtotime($val["PROCESS_END_DATE"]));
			$master_plan_data['dyeing'][$monthDay]+=$val['QNTY'];
		}
		
	}
	unset($res_dye);
	unset($sql_dye);
	
	$dateCond="and (d.receive_date between '".$startDate."' and '".$endDate."')";
	$sql_aop="SELECT 
			       d.receive_date as RECEIVE_DATE,
			       sum(c.batch_issue_qty) as QNTY
			  FROM 
			       pro_grey_batch_dtls c,
			       inv_receive_mas_batchroll d
			 WHERE     c.mst_id = d.id
			       AND d.entry_form = 92
			       
			       AND c.is_deleted = 0
			       AND c.status_active = 1
			       AND d.is_deleted = 0
			       AND d.status_active = 1 
			       AND c.process_id=35
			       $dateCond $job_no_cond
			GROUP BY 
			         d.receive_date
			        ";
	//echo $sql_aop;die;
	 $res_aop=sql_select($sql_aop);
	foreach($res_aop as $val) 
	{
		$monthDay="";
		if(!empty($val['RECEIVE_DATE']))
		{
			
			$monthDay=date("Y-m",strtotime($val["RECEIVE_DATE"]));
			$master_plan_data['aop_receive'][$monthDay]+=$val['QNTY'];
		}
		
	}
	unset($res_aop);
	unset($sql_aop);
  $dateCond="and (c.receive_date between '".$startDate."' and '".$endDate."')";
  $poidCond=str_replace("f.po_id", "e.po_breakdown_id", $poidCond);
	$recv_sql="SELECT 
         sum (e.quantity) AS QNTY,
         c.receive_date AS RECEIVE_DATE,
         d.uom as UOM,
         c.receive_basis as RECEIVE_BASIS,
         c.entry_form as ENTRY_FORM
  FROM 
       
         pro_finish_fabric_rcv_dtls d,
        inv_receive_master c,
       order_wise_pro_details e
        
    WHERE     
          c.id = d.mst_id
          and e.dtls_id=d.id
        
        and d.status_active = 1
       AND d.is_deleted = 0
        and c.status_active = 1
       AND c.is_deleted = 0
       AND c.id = d.mst_id
       AND c.entry_form in ( 37,7)
       AND e.entry_form in ( 37,7)
       AND c.item_category in ( 2,3)
       $dateCond  $poidCond
       
  group by   
         c.receive_date,
         d.uom,
         c.receive_basis,
         c.entry_form ";

   //echo $recv_sql;die;
    //$res_recv=array();
    $res_recv=sql_select($recv_sql);
	foreach($res_recv as $val) 
	{
		$monthDay="";
		if(!empty($val['RECEIVE_DATE']))
		{
			
			$monthDay=date("Y-m",strtotime($val["RECEIVE_DATE"]));
			
			if($val['ENTRY_FORM']==7 && $val['RECEIVE_BASIS']==5)
			{
				
				$master_plan_data['fin_fab_production_kg'][$monthDay]+=$val['QNTY'];
			}
			else if($val['ENTRY_FORM']==37 && $val['RECEIVE_BASIS']==11)
			{
				
				$master_plan_data['fin_fab_production_kg'][$monthDay]+=$val['QNTY'];
			}
			else if($val['ENTRY_FORM']==37 && ($val['RECEIVE_BASIS']!=11) && ($val['UOM']==27 || $val['UOM']==23))
			{
				
				$master_plan_data['fin_fab_purchase_yrd_mtr'][$monthDay]+=$val['QNTY'];
			}
			else if($val['ENTRY_FORM']==37 && ($val['RECEIVE_BASIS']!=11) && $val['UOM']==12)
			{
				
				$master_plan_data['fin_fab_purchase_kg'][$monthDay]+=$val['QNTY'];
			}
			else if($val['ENTRY_FORM']==37 && ($val['RECEIVE_BASIS']!=11) && $val['UOM']==1)
			{
				
				$master_plan_data['fin_fab_purchase_pcs'][$monthDay]+=$val['QNTY'];
			}

		}
		
	}
	//echo $recv_sql;
	unset($recv_sql);
	unset($res_recv);
	
	$dateCond="and (c.production_date between'".$startDate."' and '".$endDate."')";
	$sql_print_rcv="
				  SELECT  a.id AS JOBID,
			         a.job_no AS JOB_NO,
			         a.buyer_name AS BUYER_NAME,
			         C.PRODUCTION_DATE AS PROD_DATE,
			         C.ITEM_NUMBER_ID AS ITEM_NUMBER_ID,
			         SUM (
			            CASE WHEN C.PRODUCTION_TYPE = 1 THEN D.PRODUCTION_QNTY ELSE 0 END)
			            AS CUT_QTY,
			         SUM (
			            CASE
			               WHEN C.PRODUCTION_TYPE = 3 AND C.EMBEL_NAME = 1
			               THEN
			                  D.PRODUCTION_QNTY
			               ELSE
			                  0
			            END)
			            AS PRINTING_QTY,
			         SUM (
			            CASE
			               WHEN C.PRODUCTION_TYPE = 3 AND C.EMBEL_NAME = 2
			               THEN
			                  D.PRODUCTION_QNTY
			               ELSE
			                  0
			            END)
			            AS EMB_QTY,
			         SUM (
			            CASE
			               WHEN C.PRODUCTION_TYPE = 3 AND C.EMBEL_NAME = 3
			               THEN
			                  D.PRODUCTION_QNTY
			               ELSE
			                  0
			            END)
			            AS WASH_QTY,
			         SUM (
			            CASE WHEN C.PRODUCTION_TYPE = 4 THEN D.PRODUCTION_QNTY ELSE 0 END)
			            AS S_INPUT_QTY,
			         SUM (
			            CASE WHEN C.PRODUCTION_TYPE = 5 THEN D.PRODUCTION_QNTY ELSE 0 END)
			            AS S_OUT_QTY,
			         SUM (
			            CASE WHEN C.PRODUCTION_TYPE = 8 THEN D.PRODUCTION_QNTY ELSE 0 END)
			            AS G_FINISH_QTY
			    FROM wo_po_details_master a,
			         wo_po_break_down b,PRO_GARMENTS_PRODUCTION_MST C, PRO_GARMENTS_PRODUCTION_DTLS D
			   WHERE     
			             a.job_no = b.job_no_mst
			          AND  C.ID = D.MST_ID
			          AND b.id=C.PO_BREAK_DOWN_ID
			          AND a.company_name = '$company_id'
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND C.STATUS_ACTIVE = 1
			         AND D.STATUS_ACTIVE = 1
			         AND C.PRODUCTION_TYPE IN (1, 3, 4, 5, 8)
			         $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond $poidColCond
			GROUP BY  a.id,
			         a.job_no,
			         a.buyer_name, C.PRODUCTION_DATE,
			         C.ITEM_NUMBER_ID
					";
		//echo $sql_print_rcv;
		$res_print_rcv=sql_select($sql_print_rcv);
		foreach($res_print_rcv as $val) 
		{
			$monthDay="";
			if(!empty($val['PROD_DATE']))
			{
				
				$monthDay=date("Y-m",strtotime($val["PROD_DATE"]));
				$master_plan_data['cutting_qc'][$monthDay]+=$val['CUT_QTY'];
				$master_plan_data['print_receive'][$monthDay]+=$val['PRINTING_QTY'];
				$master_plan_data['embroidery_receive'][$monthDay]+=$val['EMB_QTY'];
				$shipmentMin=0;
				$shipmentMin=$val['S_OUT_QTY']*$smvJobArr[$val['JOB_NO']][$val['ITEM_NUMBER_ID']];
				$master_plan_data['sewing_output'][$monthDay]+=$val['S_OUT_QTY'];
				$master_plan_data['sewing_min'][$monthDay]+=$shipmentMin;
				$master_plan_data['garments_wash_rcv'][$monthDay]+=$val['WASH_QTY'];
				$master_plan_data['garments_finishing'][$monthDay]+=$val['G_FINISH_QTY'];
				
			}
			
		}
		
  
		unset($sql_print_rcv);
		unset($res_print_rcv);

		$dateCond="and (c.delivery_date between'".$startDate."' and '".$endDate."')";
		/*
		$sql_ship="
				  SELECT a.id as JOB_ID,
				  		 b.id AS PO_ID,
				         a.job_no AS JOB_NO,
				         a.buyer_name AS BUYER_NAME,
				         c.delivery_date AS DELIVERY_DATE,
				         SUM (d.ex_factory_qnty) AS QNTY,
				         d.ITEM_NUMBER_ID AS ITEM_NUMBER_ID
				    FROM wo_po_details_master a,
				         wo_po_break_down b,
				         pro_ex_factory_delivery_mst c,
				         pro_ex_factory_mst d
				   WHERE     c.id = d.delivery_mst_id
				         AND A.JOB_NO = b.job_no_mst
				         AND d.po_break_down_id = b.id
				          AND a.company_name = '$company_id'
				         AND a.is_deleted = 0
				         AND a.status_active = 1
				         AND b.is_deleted = 0
				         AND b.status_active = 1
				         AND c.status_active = 1
				         AND c.is_deleted = 0
				         AND d.status_active = 1
				         AND d.is_deleted = 0
				         AND c.entry_form != 85
				         
				          $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond $poidColCond
				GROUP BY b.id,
						 a.id,
				         a.job_no,
				         a.buyer_name,
				         c.delivery_date,
				         d.ITEM_NUMBER_ID";
				         */
		$sql_ship="
				  SELECT a.id as JOB_ID,
				  		 b.id AS PO_ID,
				         a.job_no AS JOB_NO,
				         a.buyer_name AS BUYER_NAME,
				         c.delivery_date AS DELIVERY_DATE,
				         sum(e.ex_fact_qty)          as QNTY,
				         d.item_number_id            as ITEM_NUMBER_ID,
				         sum( f.unit_price * e.ex_fact_qty )  as SHIP_VALUE
				    from wo_po_details_master       a,
				         wo_po_break_down           b,
				         pro_ex_factory_delivery_mst c,
				         pro_ex_factory_mst         d,
				         pro_ex_factory_actual_po_details e ,
				         wo_po_acc_po_info_dtls f
				   where     c.id = d.delivery_mst_id
				         and a.job_no = b.job_no_mst
				         and d.po_break_down_id = b.id
				         and e.mst_id = d.id
				         and e.actual_po_dtls_id = f.id
				       
				         and a.company_name = '2'
				         and a.is_deleted = 0
				         and a.status_active = 1
				         and b.is_deleted = 0
				         and b.status_active = 1
				         and c.status_active = 1
				         and c.is_deleted = 0
				         and d.status_active = 1
				         and d.is_deleted = 0
				         and e.status_active = 1
				         and e.is_deleted = 0
				         and c.entry_form != 85
				    
				         
				          $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond $poidColCond
				GROUP BY b.id,
						 a.id,
				         a.job_no,
				         a.buyer_name,
				         c.delivery_date,
				         d.ITEM_NUMBER_ID";
		//echo $sql_ship;
		$res_ship=sql_select($sql_ship);
		foreach($res_ship as $val) 
		{
			$monthDay="";
			if(!empty($val['DELIVERY_DATE']))
			{
				
				$monthDay=date("Y-m",strtotime($val["DELIVERY_DATE"]));
				$master_plan_data['shipment_pcs'][$monthDay]+=$val['QNTY'];
				$shipmentMin=$shpmentVal=0;
				$shipmentMin=$val['QNTY']*$smvJobArr[$val['JOB_NO']][$val['ITEM_NUMBER_ID']];
				$set_item=1;
				if(!empty($job_wise_set_item[$val['JOB_ID']]))
				{
					$set_item=$job_wise_set_item[$val['JOB_ID']];
				}
				
				$shpmentVal=($val['QNTY']*$poAvgRateArr[$val["PO_ID"]])/$set_item;//Shipment [Value];
				$master_plan_data['shipment_min'][$monthDay]+=$shipmentMin;
				//$master_plan_data['shipment_val'][$monthDay]+=$shpmentVal;
				$master_plan_data['shipment_val'][$monthDay]+=$val['SHIP_VALUE'];
			}
			
		}
		
  
		unset($sql_ship);
		unset($res_ship); 
	$pro_operation_arr=array(
		"labdip_submission"=>"Labdip Submission",
		"labdip_approval"=>"Labdip Approval",
		"yarn_allocation"=>"Yarn Allocation",
		"dyed_yarn_receive"=>"Dyed Yarn Receive",
		"grey_production"=>"Grey Production",
		"dyeing"=>"Dyeing",
		"aop_receive"=>"AOP Receive",
		"fin_fab_production_kg"=>"F.Fab rcv ( mfg , kg )",
		"fin_fab_purchase_yrd_mtr"=>"F.Fab rcv ( pur , yds/meter )",
		"fin_fab_purchase_kg"=>"F.Fab rcv ( pur , kg )",
		"fin_fab_purchase_pcs"=>"F.Fab rcv ( pur , pcs )",
		"cutting_qc"=>"Cutting QC",
		"print_receive"=>"Printing Receive",
		"embroidery_receive"=>"Embroidery Receive",
		"sewing_output"=>"Sewing(Pcs)",
		"sewing_min"=>"Sewing(Minutes)",
		"garments_wash_rcv"=>"Garments Wash Rcv",
		"garments_finishing"=>"Garments Finishing",
		"shipment_pcs"=>"Shipment (Pcs)",
		"shipment_min"=>"Shipment (Minutes)",
		"shipment_val"=>"Shipment (Value)"
	);      
			         
	ob_start();
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");  
	?>
	<fieldset style="width:1240px;" id="content_product_master_plan">
	    <div style="width:1220px; margin:0px 5px 5px 15px;">
	       	<div style="width:1220px; font-size:20px; font-weight:bold" align="center"><?=$companyArr[str_replace("'","",$company_id)]; ?></div>
	        <table class="rpt_table" border="1" cellpadding="2" cellspacing="2" style="width:1220px; margin-top:5px" rules="all">
	            <tr style="font-weight:bold; background-color:#9F9">
	                <td colspan="14" align="center">Production:<?=$fiscal_year; ?> <?="Client : ".$buyerArr[str_replace("'","",$client_id)]."; Buyer : ".$buyerArr[str_replace("'","",$buyer_id)]; ?></td>
	            </tr>
	            <tr align="center" style="font-weight:bold; font-size:14px; background-color:#CCC">
	                <td width="120">Operation Name/Month</td>
	                <? foreach($yearMonth_arr as $monthindex=>$monthval) { ?>
	                    <td width="80"><?=$monthval; ?></td>
	                <? } ?>
	                <td>Total</td>
	            </tr>
	            <? $i++;
	            
	            foreach($pro_operation_arr as $task_id=>$taskName)
	            {
	                $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
	                ?>
	                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
	                    <td title="<?=$taskName; ?>"><?=$taskName; ?></td>
	                    <?
	                    $rowTotal=0;
	                    foreach($yearMonth_arr as $monthindex=>$monthval) 
	                    { 
	                        //echo $monthindex.'<br>';
	                        $monthWiseQty=$cellEchoQty=0;
	                        $monthWiseQty=$master_plan_data[$task_id][$monthindex];
	                        $rowTotal+=$monthWiseQty;


	                        if($monthWiseQty==0 || $monthWiseQty=="") $cellEchoQty=""; else $cellEchoQty=number_format($monthWiseQty);
	                       
	                        ?>
	                        <td align="right" title="<?=$monthWiseQty; ?>"><?=$cellEchoQty; ?></td>
	                    <? }
	                    	if($rowTotal==0 || $rowTotal=="") $celltotalQty=""; else $celltotalQty=number_format($rowTotal);
	                     ?>
	                    <td align="right" title="<?=$rowTotal;?>"><?=$celltotalQty; ?></td>
	                </tr>
	                <?
	                $i++;
	            }
	             $bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
	            ?>
	           
	        </table>
	    </div>
	</fieldset>
    <?

	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

if($action=="podetails_popup_previous")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	//company_id,monthindex,taskid,location_id,buyer_id,orderStatus
	//echo $company_id.'='.$taskid; die;
	$exmonthindex=explode('-',$monthindex);
	$reptMonth=$monthindex;
	$yearno=$exmonthindex[0];
	$monthid=($exmonthindex[1]*1);
	$monthDateArr=array(); $j=cal_days_in_month(CAL_GREGORIAN, $monthid, $yearno);
	//echo $j; die;
	$i=1;
	for($k=1; $k <= $j; $k++)
	{
		$newdate=add_date(date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i))));
		$monthDateArr[$newdate]=date("d-M",strtotime(($newdate)));
		$i++;
	}
	$nodays=$i-1;
	//echo $i;
	//var_dump($monthDateArr); die;
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($yearno.'-'.$monthid.'-1')));
	$endDate=date("d-M-Y",strtotime(($yearno.'-'.$monthid.'-'.$nodays)));
	//echo $startDate.'=='.$endDate; die;
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
	
	//$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	$dateCond="and ( '".$startDate."' between c.task_start_date and c.task_finish_date or '".$endDate."' between c.task_start_date and c.task_finish_date or c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	
	$sqlOrder="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, b.grouping as INTREF, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number='$taskid' $dateCond $jobLocationCond $jobBuyerCond $orderStatusCond order by a.buyer_name, b.grouping asc"; //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315')
	//echo $sqlOrder; //die; //and a.job_no='FAL-21-00239'  $dateCond
	$sqlMstRes=sql_select( $sqlOrder ); $ponoid=""; $jobId=""; $dateDataArr=array(); $poAvgRateArr=array();
	//print_r($sqlMstRes); die;
	foreach($sqlMstRes as $prow)
	{
		//$poid.=$prow["ID"].",";
		//echo $prow['ID'].'<br>';
		if($ponoid=="") $ponoid=$prow["ID"]; else $ponoid.=",".$prow["ID"];
		if($jobId=="") $jobId="'".$prow["JOBID"]."'"; else $jobId.=",'".$prow["JOBID"]."'";
		
		$total_date=datediff(d, $prow["TASK_START_DATE"], $prow["TASK_FINISH_DATE"]);
		for($k=0; $k<$total_date; $k++)
		{
			$newdate=add_date(date("Y-m-d",strtotime($prow["TASK_START_DATE"])),$k);
			//echo $newdate.'<br>';
			$monthDay=date("Y-m",strtotime($newdate));
			$dateDataArr[$prow['ID']][$prow["INTREF"]][$newdate]=$monthDay;
		}
		$poAvgRateArr[$prow["ID"]]=$prow["UNIT_PRICE"];
	}
	//die;
	//echo $jobId; die;
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $refno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$refno][$fdate]+=1;
			}
		}
	}
	//print_r($ponoid); die;
	/*echo "<pre>";
	print_r($powiseNoofDaysArr); die;*/
	
	$po_ids=array_filter(array_unique(explode(",",$ponoid)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	$jobidRatioCond=where_con_using_array($jobIds,0,"job_id"); 
	
	$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}
	unset($gmtsitemRatioSqlRes);
	
	$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1";
	//echo $sqlpoarr; die; //and a.job_no='$job_no'
	$sqlpodata = sql_select($sqlpoarr);
	//print_r($sqlpodata);
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
	foreach($sqlpodata as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		$smvmin=0;
		$smvmin=$row['ORDER_QUANTITY']*$smvJobArr[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
		$smvArr[$row['JOB_ID']][$row['ID']]+=$smvmin;
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	}
	unset($sqlpodata);
	
	$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlContrast; die;
	$sqlContrastRes = sql_select($sqlContrast);
	$sqlContrastArr=array(); $colorContrastArr=array();
	foreach($sqlContrastRes as $row)
	{
		$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		$colorContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['contras'][$row['CONTRAST_COLOR_ID']]=$row['CONTRAST_COLOR_ID'];
	}
	unset($sqlContrastRes);
	//print_r($sqlContrastArr);
	
	//Stripe Details
	$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlStripe; die;
	$sqlStripeRes = sql_select($sqlStripe);
	$sqlStripeArr=array();
	foreach($sqlStripeRes as $row)
	{
		$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		//$colorNoArr[$row['JOB_ID']][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
	}
	unset($sqlStripeRes);
	
	//Fabric Details
	$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
	from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
	where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poidCond";
	//echo $sqlfab; die;
	$sqlfabRes = sql_select($sqlfab);
	$fabIdWiseGmtsDataArr=array(); $reqFabArr=array(); $colorNoArr=array();
	foreach($sqlfabRes as $row)
	{
		$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
		
		$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
		$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
		$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
		$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
		$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
		$colorTypeId=$row['COLOR_TYPE_ID'];
		$jobcolorstr=$row['JOB_ID'];
		$stripe_color=$sqlStripeArr[$row['JOB_ID']]['strip'];
		/*if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
		{
			if (!in_array($jobcolorstr,$jobcolorArr) )
			{
				$noofStripeColor=count($stripe_color);
				$noofColorArr[$row['JOB_ID']][$row['POID']]+=$noofStripeColor;
				$jobcolorArr[]=$jobcolorstr; 
			}
		}
		else
		{*/
			if($row['COLOR_SIZE_SENSITIVE']==3)
			{
				foreach($colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras'] as $contrast)
				{
					$colorNoArr[$row['JOB_ID']][$contrast]=$contrast;//$colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras']);
				}
			}
			else
			{
				$colorNoArr[$row['JOB_ID']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
				/*if (!in_array($jobcolorstr,$jobcolorArr) )
				{
					$noofColorArr[$row['JOB_ID']][$row['POID']]+=1;
					$jobcolorArr[]=$jobcolorstr; 
				}*/
			}
		//}
		
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		$costingPer=$costingPerArr[$row['JOB_ID']];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		
		$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
		$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
		//echo $greyReq.'='.$planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'<br>';
		
		$finAmt=$finReq*$row['RATE'];
		$greyAmt=$greyReq*$row['RATE'];
		
		//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
		if($row['FABRIC_SOURCE']==1)
		{
			$reqFabArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
		}
		$reqFabArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
	}
	unset($sqlfabRes);
	//print_r($reqFabArr); die;
	
	$noofColorArr=array();
	foreach($colorNoArr as $jobid=>$colordata)
	{
		$c=0;
		foreach($colordata as $colorid)
		{
			$c++;
			//$noofColorArr[$row['JOB_ID']]+=1;
		}
		$noofColorArr[$jobid]=$c;
	}
	//print_r($noofColorArr); die;
	if($taskid==48)
	{
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
	
	from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlYarn;
		$sqlYarnRes = sql_select($sqlYarn);
		foreach($sqlYarnRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
			
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
			
			$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
			
			$yarnAmt=$yarnReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
		}
		unset($sqlYarnRes);
	}
	if($taskid==61 || $taskid==63 || $taskid==52)
	{
		//Convaersion Details
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convReqArr=array();
		foreach($sqlConvRes as $row)
		{
			$id=$row['CONVERTION_ID'];
			$colorBreakDown=$row['COLOR_BREAK_DOWN'];
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
					$arr_2=explode("_",$arr_1[$ci]);
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
				}
			}
		}
		//echo "ff"; die;
		foreach($sqlConvRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
			$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				$qnty=0; $convrate=0;
				foreach($stripe_color as $stripe_color_id)
				{
					$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
					$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
					
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
		
					if($convrate>0){
						$reqqnty+=$qnty;
						$convAmt+=$qnty*$convrate;
					}
				}
			}
			else
			{
				$convrate=$requirment=$reqqnty=0;
				$rateColorId=$row['COLOR_NUMBER_ID'];
				if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
		
				if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
				
				if($convrate>0){
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty+=$qnty;
					$convAmt+=$qnty*$convrate;
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_qty']+=$reqqnty;
			//$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes); 
	}
	if($taskid==90 || $taskid==267 || $taskid==268)
	{
		$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
	from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
	where 1=1 and a.cons_dzn_gmts>0 and
	a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlEmb; die;
		$sqlEmbRes = sql_select($sqlEmb);
		$embReqArr=array();
		foreach($sqlEmbRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			$budget_on=$row['BUDGET_ON'];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			$calPoPlanQty=0;
			
			if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
				$consQty=0;
				$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
				$consQnty+=$consQty;
				
				$consAmt=$consQty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
				$consQnty=$consAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
						$consQty=0;
						$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
						$consQnty+=$consQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$embReqArr[$row['JOB_ID']][$row['POID']][$row['EMB_NAME']]['embqty']+=$consQnty;
		}
		unset($sqlEmbRes); 
	}
	$monthDtlsArr=array();
	foreach($sqlMstRes as $row)
	{
		foreach($dateDataArr[$row['ID']][$row["INTREF"]] as $tnadate=>$shortdate)
		{
			$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
			if($shortdate==$reptMonth)
			{
				//echo $shortdate.'=='.$reptMonth.'<br>';
				//$exday=explode("-",change_date_format($row["TASK_START_DATE"]));
				//print_r($exday);
				//echo $row["TASK_START_DATE"].'='. $row["TASK_FINISH_DATE"].'<br>';
				//$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
				//for($k=($exday[0]*1); $k <= $nodays; $k++)
				//{
					$fdate=$tnadate;//add_date(date("Y-m-d",strtotime(($yearno.'-'.($exday[1]*1).'-'.$k))));
					//echo $fdate.'='.$row["JOB_NO"].'<br>';
					$qty=0;
					if($row["TASK_NUMBER"]==9)//Labdip Submission
					{
						$qty=(($noofColorArr[$row['JOBID']]/$noofDays));//*$monthinDays
						//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==10)//Labdip Approval
					{
						$qty=(($noofColorArr[$row['JOBID']]/$noofDays));//*$monthinDays
						//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==48)//Yarn Allocation
					{
						$qty=(($reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==52)//Dyed Yarn Receive
					{
						$qty=(($convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==60)//Grey Production
					{
						//echo $row['JOB_NO'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'='.$noofDays.'='.$monthinDays.'<br>';
						$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodgrey_qty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==61)//Dyeing
					{
						$qty=(($convReqArr[$row['JOBID']][$row['ID']][31]['conv_qty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==63)//AOP Receive
					{
						$qty=(($convReqArr[$row['JOBID']][$row['ID']][35]['conv_qty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==73)//F. Fabric Recvd
					{
						$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodfin_qty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==84)//Cutting QC
					{
						$qty=(($row["PO_QUANTITY"]/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==86)//Sewing[Pcs]
					{
						$qty=(($row["PO_QUANTITY"]/$noofDays));//*$monthinDays
						//echo $row['JOB_NO'].'='.$row["PO_QUANTITY"].'='.$noofDays.'='.$monthinDays.'<br>';
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						
						$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$sewMin;
					}
					if($row["TASK_NUMBER"]==267)//Printing Receive
					{
						$qty=(($embReqArr[$row['JOBID']][$row['ID']][1]['embqty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==268)//Embroidery Receive
					{
						$qty=(($embReqArr[$row['JOBID']][$row['ID']][2]['embqty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==90)//Garments Wash Rcv
					{
						$qty=(($embReqArr[$row['JOBID']][$row['ID']][3]['embqty']/$noofDays));//*$monthinDays
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
					if($row["TASK_NUMBER"]==88)//Garments Finishing
					{
						$qty=(($row["PO_QUANTITY"]/$noofDays));//Garments Finishing
						$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
					}
				}
			}
		//}
	}
	$lastday="";
	$rspanArr=array(); $jobrefWiseTotArr=array(); $qtyDataArr=array();
	foreach($monthDtlsArr as $buid=>$bdata)
	{
		$binc=0;
		foreach($bdata as $jobn=>$jobdata)
		{
			foreach($jobdata as $iref=>$irefdata)
			{
				$binc++;
				foreach($irefdata as $flldate=>$qdata)
				{
					$jobrefWiseTotArr[$buid][$jobn][$iref]+=$qdata['qty'];
					$qtyDataArr[$buid][$jobn][$iref][$flldate]+=$qdata['qty'];
					//echo $qdata['qty'];
					$lastday=$flldate;
				}
			}
		}
		$rspanArr[$buid]=$binc;
	}
	//print_r($jobrefWiseTotArr);
		
	$tblwidth=($nodays*40)+180;
	ob_start();
	
	
	?>
    <script>
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth; ?>px;">
    	<div id="report_container2" ></div>
    	<input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px; margin-left:200px; display:none"/><input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/> <input type="hidden" value="" name="hiddfilename" style="width:100px;"/> 
        <div style="100%" id="report_container">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr style="font-weight:bold; background-color:#FFC">
            	<td width="30" rowspan="2" valign="middle" >Buyer</td>
                <td align="center" style="word-break:break-all"><?=$taskArr[$taskid]; ?></td>
                <td colspan="<?=$nodays; ?>" align="center">For The Month of <?=$months[$monthid].'-'.$yearno; ?> Plan Details As Per TNA:</td>
                <td rowspan="2" valign="middle" style="word-break:break-all" align="center">Total</td>
            </tr>
            <tr align="center" style="font-weight:bold; background-color:#CCC">
                <td width="60">Ref. No</td>
                <? foreach($monthDateArr as $monthindex=>$monthval) { 
					?>
                    <td width="40"><?=$monthval; ?></td>
                <? } ?>
            </tr>
        </table>
        <div style="width:<?=$tblwidth; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth-20; ?>px;" rules="all" id="table_body">
            <? $i=1; $dayTotatArr=array();
            foreach($monthDtlsArr as $buyid=>$buydata)
            {
				$buy=1; $btotal=0; $dayWiseBtotalArr=array();
				foreach($buydata as $jobno=>$jobdata)
				{
					foreach($jobdata as $intref=>$intrefdata)
					{
						//print_r($intrefdata);
						$rtotQty=0;
						$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
						
						?>
						<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
							<?  if($buy==1) { ?>
							<td width="30" rowspan="<?=$rspanArr[$buyid]; ?>" align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
							<?
							} ?>
							<td width="60" style="word-break:break-all" title="<?=$jobno; ?>"><?=$intref; ?></td>
							<? foreach($monthDateArr as $monthindex=>$monthval) { 
								$dayWiseQty=0;
								//echo $intrefdata[$monthindex]['qty'];
								$dayWiseQty=$qtyDataArr[$buyid][$jobno][$intref][$monthindex];//$intrefdata[$monthindex]['qty'];
								if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
								$dayWiseBtotalArr[$buyid][$monthindex]+=$dayWiseQty;
								$dayTotatArr[$monthindex]+=$dayWiseQty;
								$rtotQty+=$dayWiseQty;
							?>
								<td width="40" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
							<? } ?>
                            <td align="right"><?=number_format($rtotQty); ?></td>
						</tr>
						<?
						$btotal+=$rtotQty;
						$gtotal+=$rtotQty;
						$buy++;
						$i++;
					}
				}
				?>
                <tr bgcolor="#CCCCCC">
                    <td width="90" colspan="2" align="right"><b><?=$buyerArr[$buyid].'-Total :'; ?></b></td>
                    <? foreach($monthDateArr as $monthindex=>$monthval) { 
                        $dayWiseBuyerQty=0;
                        $dayWiseBuyerQty=$dayWiseBtotalArr[$buyid][$monthindex];
                    ?>
                        <td width="40" align="right" style="word-break:break-all"><?=number_format($dayWiseBuyerQty); ?></td>
                    <? } ?>
                    <td align="right" style="word-break:break-all"><?=number_format($btotal); ?></td>
                </tr>
                <?
            }
            ?>
        </table>
        </div>
        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr>
                <td width="90" align="right"><b>Grand Total :</b></td>
                <? foreach($monthDateArr as $monthindex=>$monthval) { 
                    $dayTotalQty=0;
                    $dayTotalQty=$dayTotatArr[$monthindex];
                ?>
                    <td width="40" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
                <? } ?>
                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
            </tr>
        </table>
    	</div>
    </div> 
    
    <?
	
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	//echo "$html**$filename";
	?>
	<script>
    	$('#hiddfilename').val(<?=$filename; ?>);
	</script>
    <?
	exit();
}


if($action=="podetails_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	//company_id,monthindex,taskid,location_id,buyer_id,orderStatus
	//echo $company_id.'='.$taskid; die;
	//$task_type
	
	$exmonthindex=explode('-',$monthindex);
	$reptMonth=$monthindex;
	$yearno=$exmonthindex[0];
	$monthid=($exmonthindex[1]*1);
	$monthDateArr=array(); $j=cal_days_in_month(CAL_GREGORIAN, $monthid, $yearno);
	//echo $j; die;
	$i=1;
	for($k=1; $k <= $j; $k++)
	{
		//echo strtotime(($yearno.'-'.$monthid.'-'.$i));
		//$newdate=add_date(date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i))));
		$newdate=date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i)));
		//echo $newdate.'<br>';
		$monthDateArr[$newdate]=date("d-M",strtotime(($newdate)));
		$i++;
	}
	$nodays=$i-1;
	//echo $i;
	//echo "<pre>";
	//print_r($monthDateArr);
	//var_dump($monthDateArr); die;
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($yearno.'-'.$monthid.'-1')));
	$endDate=date("d-M-Y",strtotime(($yearno.'-'.$monthid.'-'.$nodays)));
	//echo $startDate.'=='.$endDate; die;
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";

	//$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	$dateCond="and ( '".$startDate."' between c.task_start_date and c.task_finish_date or '".$endDate."' between c.task_start_date and c.task_finish_date or c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";

	if($taskid == 902)
	{
		$sqlOrder="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, b.grouping as INTREF, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number='86' $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by a.buyer_name, b.grouping asc";
	}
	else{
		$sqlOrder="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, b.grouping as INTREF, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number='$taskid' $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by a.buyer_name, b.grouping asc";
	}
	
	 //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315')
	//echo $sqlOrder; //die; //and a.job_no='FAL-21-00239'  $dateCond
	$sqlMstRes=sql_select( $sqlOrder ); $ponoid=""; $jobId=""; $dateDataArr=array(); $poAvgRateArr=array();
	//print_r($sqlMstRes); die;
	$powisejobarr = array();
	foreach($sqlMstRes as $prow)
	{
		//$poid.=$prow["ID"].",";
		//echo $prow['ID'].'<br>';
		if($ponoid=="") $ponoid=$prow["ID"]; else $ponoid.=",".$prow["ID"];
		if($jobId=="") $jobId="'".$prow["JOBID"]."'"; else $jobId.=",'".$prow["JOBID"]."'";
		
		$total_date=datediff(d, $prow["TASK_START_DATE"], $prow["TASK_FINISH_DATE"]);
		for($k=0; $k<$total_date; $k++)
		{
			$newdate=add_date(date("Y-m-d",strtotime($prow["TASK_START_DATE"])),$k);
			//echo $newdate.'<br>';
			$monthDay=date("Y-m",strtotime($newdate));
			$dateDataArr[$prow['ID']][$prow["INTREF"]][$newdate]=$monthDay;
		}
		$poAvgRateArr[$prow["ID"]]=$prow["UNIT_PRICE"];
		$powisejobarr[$prow["ID"]] = $prow['JOB_NO'];
	}


	//die;
	//echo $jobId; die;
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $refno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$refno][$fdate]+=1;
			}
		}
	}
	//print_r($ponoid); die;
	/*echo "<pre>";
	print_r($powiseNoofDaysArr); die;*/
	
	$po_ids=array_filter(array_unique(explode(",",$ponoid)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	$jobidRatioCond=where_con_using_array($jobIds,0,"job_id"); 
	$idCond=where_con_using_array($jobIds,0,"a.id"); 

	if($taskid == 60) // Grey Production 
	{
		
		$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");
		$tna_plan_target_sql = "select a.PO_ID,a.PLAN_QTY,a.PLAN_DATE,b.job_no as JOB_NO, b.buyer_name as BUYER_NAME,c.grouping as INTREF,a.UOM_ID,a.SOURCE_ID,a.YARN_COUNT_DETER_ID  from TNA_PLAN_TARGET a , wo_po_details_master b, wo_po_break_down c where b.id = c.job_id and c.id = a.po_id and a.TASK_ID=$taskid and a.task_type=1 and a.PLAN_DATE between '$startDate' and '$endDate' $po_id_cond ";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		$deter_id_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$deter_id_arr[$row['YARN_COUNT_DETER_ID']] = $row['YARN_COUNT_DETER_ID'];
		}

		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

		$deter_cond = where_con_using_array($deter_id_arr,0,"a.id");

		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 $deter_cond order by a.id,b.id";
		
		$data_array=sql_select($sql);
		$composition_arr=array();
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				$compo_per="";
				if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
				if(!empty($composition_arr[$row[csf('id')]]['com']))
				{
					$composition_arr[$row[csf('id')]]['com']=$composition_arr[$row[csf('id')]]['com']." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]['com']=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
					$composition_arr[$row[csf('id')]]['cons'] = $row[csf('construction')];
				}
			}
		}

		// echo "<pre>";
		// print_r($composition_arr);
		// echo "</pre>";

		$plan_data = array();

		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('d-m-Y', strtotime($row['PLAN_DATE']));
			if(!empty($row['PLAN_QTY']))
			{
				$comp = trim($composition_arr[$row['YARN_COUNT_DETER_ID']]['com']);
				$cons = trim($composition_arr[$row['YARN_COUNT_DETER_ID']]['cons']);
				$index = $cons ."*".$comp;
				$plan_data[$row['BUYER_NAME']][$row['INTREF']][$index][$dmyKey]+=$row['PLAN_QTY'];
				//echo "<pre>".$row['BUYER_NAME']."=>".$row['INTREF']."=>".$cons."=>".$comp."=>".$dmyKey."=>".$row['PLAN_QTY']."</pre>";
			}

			//echo "<pre>tna ".$row[csf('BUYER_NAME')]." , ".$row[csf('JOB_NO')]." , ".$row[csf('INTREF')]." , $dmyKey = ".$row['PLAN_QTY']." </pre>";
			
			//echo "<pre>tna ".$index."=>".$row['PLAN_QTY']."</pre>";
		}

		// echo "<pre>";
		// print_r($plan_data);
		// echo "</pre>";

		$tblwidth=($nodays*40)+180+270;
		ob_start();
		?>
		<script type="text/javascript">
			function print_rpt()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				//$("#table_body tr:first").hide();
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
				d.close();
				
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="4000px";
			}
			
			function print_excel()
			{
				var filename=$('#hiddfilename').val();
				alert(filename);
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
				d.close();
			}
		</script>
	    <div style="width:<?=$tblwidth+20; ?>px;">

	    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
	    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="<?=$nodays+5; ?>" align="center"><?=$months[$monthid].' '.$yearno; ?> : <?=$taskArr[$taskid]; ?></td>
	                
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="30"  valign="middle" >Buyer</td>
	                <td width="60">Ref. No</td>
	                <td width="100">Fabric Structure</td>
	                <td width="170">Fb. Composition</td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
						?>
	                    <td width="40"><?=$monthval; ?></td>
	                <? } ?>
	                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <? $i=1; $dayTotatArr=array();
	            $buyer_row_tatal = array();
	            $buyerSpan = array();
	            $intrefSpan = array();
	            $consSpan = array();
	            $gtotal = 0;
	            foreach($plan_data as $buyid=>$buydata)
	            {
					$buy=1; $btotal=0; $dayWiseBtotalArr=array();
					foreach($buydata as $intref=>$intrefdata)
					{
						foreach($intrefdata as $cons_comp=>$row)
						{
							foreach($monthDateArr as $monthindex=>$monthval)
							{ 
								$date_key = date("d-m-Y",strtotime($monthindex));
							} 
							$buyerSpan[$buyid]++;
							$intrefSpan[$buyid][$intref]++;
						}
					}
				}
	            foreach($plan_data as $buyid=>$buydata)
	            {
					$buy=0; $btotal=0; $dayWiseBtotalArr=array();

					foreach($buydata as $intref=>$intrefdata)
					{
						$int_ref_span = 0;
						foreach($intrefdata as $cons_comp=>$row)
						{
							$com_exp = array();
							$com_exp = explode("*",$cons_comp);
							$cons = $com_exp[0];
							$comp = $com_exp[1];

							$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								
								<?php if ($buy == 0): ?>
									<td width="30" rowspan="<?=$buyerSpan[$buyid];?>"  align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
								<?php endif ?>
								
								<?php if ($int_ref_span == 0 ): ?>
									<td width="60" align="center" valign="middle"  rowspan="<?=$intrefSpan[$buyid][$intref];?>" style="word-break:break-all" title="<?=$jobno; ?>"><?=$intref; ?></td>
								<?php endif ?>
								
								
									<td width="100"  align="center" valign="middle" style="word-break:break-all" title="<?=$cons; ?>"><?=$cons; ?></td>
								

								
								<td width="170" style="word-break:break-all" title="<?=$comp; ?>"><?=$comp; ?></td>
								<? 
								$rtotQty = 0;
								foreach($monthDateArr as $monthindex=>$monthval)
								{ 

									$dayWiseQty=0;
									$date_key = date('d-m-Y',strtotime($monthindex));
									
									$dayWiseQty=$row[$date_key];

									//echo "<pre>".$buyid."=>".$intref."=>".$cons."=>".$comp."=>".$date_key."=>".$row[$date_key]."</pre>";

									if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
									$rtotQty+=fn_number_format($dayWiseQty,2,".","");
									$dayWiseBtotalArr[$buyid][$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$dayTotatArr[$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$gtotal+=fn_number_format($dayWiseQty,2,".","");


									?>
									<td width="40" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
									<? 
								} 
								?>
	                            <td align="right"><?=number_format($rtotQty); ?></td>
							</tr>
							<?
							
							$i++;
							$buy++;
							$int_ref_span++;
						}
					}
					
					?>
	                <tr bgcolor="#CCCCCC">
	                    <td width="360" colspan="4" align="right"><b><?=$buyerArr[$buyid].'-Total :'; ?></b></td>
	                    <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                        $dayWiseBuyerQty=0;
	                        $dayWiseBuyerQty=$dayWiseBtotalArr[$buyid][$monthindex];
	                    ?>
	                        <td width="40" align="right" style="word-break:break-all"><?=number_format($dayWiseBuyerQty); ?></td>
	                    <? } ?>
	                    <td align="right" style="word-break:break-all"><?=number_format($btotal); ?></td>
	                </tr>
	                <?
	            }
	            ?>
	        </table>
	        </div>
	        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr>
	            	<td width="30"  ></td>
	                <td width="60"></td>
	                <td width="100"></td>
	               
	                <td width="170" align="right" ><b>Grand Total :</b></td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                    $dayTotalQty=0;
	                    $dayTotalQty=$dayTotatArr[$monthindex];
	                ?>
	                    <td width="40" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
	                <? } ?>
	                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
	            </tr>
	        </table>
	    	</div>
	    </div>
	    <?
	}
	else if($taskid == 52) // Dyed Yarn Receive 
	{
		$lib_color = return_library_array( "select id, color_name from lib_color", "id", "color_name");
		$po_id_cond = where_con_using_array($po_ids,0,"PO_ID");
		$tna_plan_target_sql = "select a.PO_ID,a.PLAN_QTY,a.PLAN_DATE,b.job_no as JOB_NO, b.buyer_name as BUYER_NAME,c.grouping as INTREF,a.UOM_ID,a.SOURCE_ID,a.COLOUR_ID  from TNA_PLAN_TARGET a , wo_po_details_master b, wo_po_break_down c where b.id = c.job_id and c.id = a.po_id and a.TASK_ID=$taskid and a.task_type=1 and a.PLAN_DATE between '$startDate' and '$endDate' $po_id_cond ";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		
		// echo "<pre>";
		// print_r($composition_arr);
		// echo "</pre>";

		$plan_data = array();

		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('d-m-Y', strtotime($row['PLAN_DATE']));
			if(!empty($row['PLAN_QTY']))
			{
				
				$plan_data[$row['BUYER_NAME']][$row['INTREF']][$row['COLOUR_ID']][$dmyKey]+=$row['PLAN_QTY'];
			}

			//echo "<pre>tna ".$row[csf('BUYER_NAME')]." , ".$row[csf('JOB_NO')]." , ".$row[csf('INTREF')]." , $dmyKey = ".$row['PLAN_QTY']." </pre>";
			
			//echo "<pre>tna ".$index."=>".$row['PLAN_QTY']."</pre>";
		}

		// echo "<pre>";
		// print_r($plan_data);
		// echo "</pre>";

		$tblwidth=($nodays*40)+180+220;
		ob_start();
		?>
		<script type="text/javascript">
			function print_rpt()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				//$("#table_body tr:first").hide();
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
				d.close();
				
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="4000px";
			}
			
			function print_excel()
			{
				var filename=$('#hiddfilename').val();
				alert(filename);
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
				d.close();
			}
		</script>
	    <div style="width:<?=$tblwidth+20; ?>px;">

	    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
	    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="<?=$nodays+5; ?>" align="center"><?=$months[$monthid].' '.$yearno; ?> : <?=$taskArr[$taskid]; ?></td>
	                
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100"  valign="middle" >Buyer</td>
	                <td width="60">Ref. No</td>
	                <td width="150">Color</td>
	                
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
						?>
	                    <td width="40"><?=$monthval; ?></td>
	                <? } ?>
	                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <? $i=1; $dayTotatArr=array();
	            $buyer_row_tatal = array();
	            $buyerSpan = array();
	            $intrefSpan = array();
	            $consSpan = array();
	            $gtotal = 0;
	            foreach($plan_data as $buyid=>$buydata)
	            {
					$buy=1; $btotal=0; $dayWiseBtotalArr=array();
					foreach($buydata as $intref=>$intrefdata)
					{
						foreach($intrefdata as $cons_comp=>$row)
						{
							foreach($monthDateArr as $monthindex=>$monthval)
							{ 
								$date_key = date("d-m-Y",strtotime($monthindex));
							} 
							$buyerSpan[$buyid]++;
							$intrefSpan[$buyid][$intref]++;
						}
					}
				}
	            foreach($plan_data as $buyid=>$buydata)
	            {
					$buy=0; $btotal=0; $dayWiseBtotalArr=array();

					foreach($buydata as $intref=>$intrefdata)
					{
						$int_ref_span = 0;
						foreach($intrefdata as $colour_id=>$row)
						{
							

							$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								
								<?php if ($buy == 0): ?>
									<td width="100" rowspan="<?=$buyerSpan[$buyid];?>"  align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
								<?php endif ?>
								
								<?php if ($int_ref_span == 0 ): ?>
									<td width="60" align="center" valign="middle"  rowspan="<?=$intrefSpan[$buyid][$intref];?>" style="word-break:break-all" title="<?=$jobno; ?>"><?=$intref; ?></td>
								<?php endif ?>
								
								
								<td width="150"  align="center" valign="middle" style="word-break:break-all" title="<?=$lib_color[$colour_id]; ?>"><?=$lib_color[$colour_id]; ?></td>
								

								
								
								<? 
								$rtotQty = 0;
								foreach($monthDateArr as $monthindex=>$monthval)
								{ 

									$dayWiseQty=0;
									$date_key = date('d-m-Y',strtotime($monthindex));
									
									$dayWiseQty=$row[$date_key];

									//echo "<pre>".$buyid."=>".$intref."=>".$cons."=>".$comp."=>".$date_key."=>".$row[$date_key]."</pre>";

									if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
									$rtotQty+=fn_number_format($dayWiseQty,2,".","");
									$dayWiseBtotalArr[$buyid][$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$dayTotatArr[$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$gtotal+=fn_number_format($dayWiseQty,2,".","");


									?>
									<td width="40" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
									<? 
								} 
								?>
	                            <td align="right"><?=number_format($rtotQty); ?></td>
							</tr>
							<?
							
							$i++;
							$buy++;
							$int_ref_span++;
						}
					}
					
					?>
	                <tr bgcolor="#CCCCCC">
	                    <td  colspan="3" align="right"><b><?=$buyerArr[$buyid].'-Total :'; ?></b></td>
	                    <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                        $dayWiseBuyerQty=0;
	                        $dayWiseBuyerQty=$dayWiseBtotalArr[$buyid][$monthindex];
	                    ?>
	                        <td width="40" align="right" style="word-break:break-all"><?=number_format($dayWiseBuyerQty); ?></td>
	                    <? } ?>
	                    <td align="right" style="word-break:break-all"><?=number_format($btotal); ?></td>
	                </tr>
	                <?
	            }
	            ?>
	        </table>
	        </div>
	        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr>
	            	<td width="100"  ></td>
	                <td width="60"></td>
	               
	                <td width="150" align="right" ><b>Grand Total :</b></td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                    $dayTotalQty=0;
	                    $dayTotalQty=$dayTotatArr[$monthindex];
	                ?>
	                    <td width="40" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
	                <? } ?>
	                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
	            </tr>
	        </table>
	    	</div>
	    </div>
	    <?
	}
	else if($taskid == 48) // Yarn Allocation 
	{
		$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");
		$tna_plan_target_sql = "select a.PO_ID,a.PLAN_QTY,a.PLAN_DATE,b.job_no as JOB_NO, b.buyer_name as BUYER_NAME,c.grouping as INTREF,a.UOM_ID,a.COMPOSITION_ID,a.COUNT_ID  from TNA_PLAN_TARGET a , wo_po_details_master b, wo_po_break_down c where b.id = c.job_id and c.id = a.po_id and a.TASK_ID=$taskid and a.task_type=1 and a.PLAN_DATE between '$startDate' and '$endDate' $po_id_cond ";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		

		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

		

		$plan_data = array();

		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('d-m-Y', strtotime($row['PLAN_DATE']));
			if(!empty($row['PLAN_QTY']))
			{
				$comp = $composition[$row['COMPOSITION_ID']];
				$count = $lib_yarn_count[$row['COUNT_ID']];
				$index = $comp ."*".$count;
				$plan_data[$row['BUYER_NAME']][$row['INTREF']][$index][$dmyKey]+=$row['PLAN_QTY'];
				
			}
		}

		// echo "<pre>";
		// print_r($plan_data);
		// echo "</pre>";

		$tblwidth=($nodays*40)+180+340;
		ob_start();
		?>
		<script type="text/javascript">
			function print_rpt()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				//$("#table_body tr:first").hide();
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
				d.close();
				
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="4000px";
			}
			
			function print_excel()
			{
				var filename=$('#hiddfilename').val();
				alert(filename);
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
				d.close();
			}

			function generate_report_popup()
			{
				
				var param='width=1080px,height=400px,center=1,resize=1,scrolling=1';
				var titel="Yarn Allocation";
				
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe',"planning_tna_report_controller.php?company_id=<?=$company_id;?>&monthindex=<?=$monthindex;?>&taskid=<?=$taskid;?>&location_id=<?=$location_id;?>&buyer_id=<?=$buyer_id;?>&orderStatus=<?=$orderStatus;?>&client_id=<?=$client_id;?>&action=allocation_count_popup&task_type=<?=$task_type;?>",titel,param ,'../../../');
		        emailwindow.onclose=function()
		        {
		            
		        }
			}
		</script>
	    <div style="width:<?=$tblwidth+20; ?>px;">

	    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
	    	<input type="button" onclick="generate_report_popup();" value="Count Wise" name="allocation_count" class="formbutton" style="width:100px; margin-left:200px;"/>
	    	
	    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="<?=$nodays+5; ?>" align="center"><?=$months[$monthid].' '.$yearno; ?> : <?=$taskArr[$taskid]; ?></td>
	                
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100"  valign="middle" >Buyer</td>
	                <td width="60">Ref. No</td>
	                <td width="170">Yarn Comp.</td>
	                <td width="100">Count</td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
						?>
	                    <td width="40"><?=$monthval; ?></td>
	                <? } ?>
	                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <? $i=1; $dayTotatArr=array();
	            $buyer_row_tatal = array();
	            $buyerSpan = array();
	            $intrefSpan = array();
	            $consSpan = array();
	            $gtotal = 0;
	            foreach($plan_data as $buyid=>$buydata)
	            {
					$buy=1; $btotal=0; $dayWiseBtotalArr=array();
					foreach($buydata as $intref=>$intrefdata)
					{
						foreach($intrefdata as $cons_comp=>$row)
						{
							foreach($monthDateArr as $monthindex=>$monthval)
							{ 
								$date_key = date("d-m-Y",strtotime($monthindex));
							} 
							$buyerSpan[$buyid]++;
							$intrefSpan[$buyid][$intref]++;
						}
					}
				}
	            foreach($plan_data as $buyid=>$buydata)
	            {
					$buy=0; $btotal=0; $dayWiseBtotalArr=array();

					foreach($buydata as $intref=>$intrefdata)
					{
						$int_ref_span = 0;
						foreach($intrefdata as $cons_comp=>$row)
						{
							$com_exp = array();
							$com_exp = explode("*",$cons_comp);
							$compo = $com_exp[0];
							$count = $com_exp[1];

							$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								
								<?php if ($buy == 0): ?>
									<td width="100" rowspan="<?=$buyerSpan[$buyid];?>"  align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
								<?php endif ?>
								
								<?php if ($int_ref_span == 0 ): ?>
									<td width="60" align="center" valign="middle"  rowspan="<?=$intrefSpan[$buyid][$intref];?>" style="word-break:break-all" title="<?=$jobno; ?>"><?=$intref; ?></td>
								<?php endif ?>
								
								
								<td width="170"  align="center" valign="middle" style="word-break:break-all" title="<?=$compo; ?>"><?=$compo; ?></td>
								

								
								<td width="100" style="word-break:break-all" title="<?=$count; ?>"><?=$count; ?></td>
								<? 
								$rtotQty = 0;
								foreach($monthDateArr as $monthindex=>$monthval)
								{ 

									$dayWiseQty=0;
									$date_key = date('d-m-Y',strtotime($monthindex));
									
									$dayWiseQty=$row[$date_key];

									//echo "<pre>".$buyid."=>".$intref."=>".$cons."=>".$comp."=>".$date_key."=>".$row[$date_key]."</pre>";

									if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
									$rtotQty+=fn_number_format($dayWiseQty,2,".","");
									$dayWiseBtotalArr[$buyid][$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$dayTotatArr[$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$gtotal+=fn_number_format($dayWiseQty,2,".","");


									?>
									<td width="40" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
									<? 
								} 
								?>
	                            <td align="right"><?=number_format($rtotQty); ?></td>
							</tr>
							<?
							
							$i++;
							$buy++;
							$int_ref_span++;
						}
					}
					
					?>
	                <tr bgcolor="#CCCCCC">
	                    <td  colspan="4" align="right"><b><?=$buyerArr[$buyid].'-Total :'; ?></b></td>
	                    <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                        $dayWiseBuyerQty=0;
	                        $dayWiseBuyerQty=$dayWiseBtotalArr[$buyid][$monthindex];
	                    ?>
	                        <td width="40" align="right" style="word-break:break-all"><?=number_format($dayWiseBuyerQty); ?></td>
	                    <? } ?>
	                    <td align="right" style="word-break:break-all"><?=number_format($btotal); ?></td>
	                </tr>
	                <?
	            }
	            ?>
	        </table>
	        </div>
	        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr>
	            	<td width="100"  ></td>
	                <td width="60"></td>
	                <td width="170"></td>
	               
	                <td width="100" align="right" ><b>Grand Total :</b></td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                    $dayTotalQty=0;
	                    $dayTotalQty=$dayTotatArr[$monthindex];
	                ?>
	                    <td width="40" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
	                <? } ?>
	                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
	            </tr>
	        </table>
	    	</div>
	    </div>
	    <?
	}
	else
	{
		$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array(); $smvJobArr=array();
		$jobSmv= array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
			$jobSmv[$row['JOBNO']]=$row['SMV_PCS'];
		}
		unset($gmtsitemRatioSqlRes);

		if($taskid == 902) $ntaskid = 86;
		else $ntaskid = $taskid;
		$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");
		$tna_plan_target_sql = "select a.PO_ID,a.PLAN_QTY,a.PLAN_DATE,b.job_no as JOB_NO, b.buyer_name as BUYER_NAME,c.grouping as INTREF,a.UOM_ID,a.SOURCE_ID  from TNA_PLAN_TARGET a , wo_po_details_master b, wo_po_break_down c where b.id = c.job_id and c.id = a.po_id and a.TASK_ID=$ntaskid and a.task_type=1 and a.PLAN_DATE between '$startDate' and '$endDate' $po_id_cond";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('d-m-Y', strtotime($row['PLAN_DATE']));
			if(!empty($row['PLAN_QTY']))
			{
				//echo "<pre>".$task_type  ."&&". $row[csf('UOM_ID')]."*".$row[csf('SOURCE_ID')]."</pre>";
				if($taskid == 73)
				{
					//f_fabric_rcvd_pur_yds_meter
					if($task_type == 'f_fabric_rcvd_mfg_kg' && $row[csf('UOM_ID')]==12 && $row[csf('SOURCE_ID')]==1)
		            {
		            	$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= $row['PLAN_QTY']; 
		            	//echo "<pre>".$row['PLAN_QTY']."</pre>";
		            }
		            else if($task_type == 'f_fabric_rcvd_pur_kg' &&$row[csf('UOM_ID')]==12 && $row[csf('SOURCE_ID')]==2)
		            {
		            	$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= $row['PLAN_QTY'];
		            }
		            else if($task_type == 'f_fabric_rcvd_pur_pcs' && $row[csf('UOM_ID')]==1 && $row[csf('SOURCE_ID')]==2)
		            {
		            	$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= $row['PLAN_QTY'];
		            }
		            else if($task_type == 'f_fabric_rcvd_pur_yds_meter' && ( $row[csf('UOM_ID')]==23 || $row[csf('UOM_ID')]==27 ) && $row[csf('SOURCE_ID')]==2)
		            {
		            	$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= $row['PLAN_QTY'];
		            }
				}
				else if ($taskid == 902)
				{
					$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= ($row['PLAN_QTY'] * $jobSmv[$powisejobarr[$row['PO_ID']]]); 
				}
				else
				{
					$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= $row['PLAN_QTY']; 
				}
			}

			//echo "<pre>tna ".$row[csf('BUYER_NAME')]." , ".$row[csf('JOB_NO')]." , ".$row[csf('INTREF')]." , $dmyKey = ".$row['PLAN_QTY']." </pre>";
			
			//echo "<pre>tna ".$index."=>".$row['PLAN_QTY']."</pre>";
		}
		// echo "<pre>";
		// print_r($tna_plan_target_arr);
		// echo "</pre>";
		
		
		
		$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 ";
		//echo $sqlpoarr; die; //and a.job_no='$job_no'
		$sqlpodata = sql_select($sqlpoarr);
		//print_r($sqlpodata);
		$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
		foreach($sqlpodata as $row)
		{
			$costingPerQty=0;
			if($row['COSTING_PER']==1) $costingPerQty=12;
			elseif($row['COSTING_PER']==2) $costingPerQty=1;	
			elseif($row['COSTING_PER']==3) $costingPerQty=24;
			elseif($row['COSTING_PER']==4) $costingPerQty=36;
			elseif($row['COSTING_PER']==5) $costingPerQty=48;
			else $costingPerQty=0;
			
			$costingPerArr[$row['JOB_ID']]=$costingPerQty;
			
			$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			
			$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
			
			$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			$smvmin=0;
			$smvmin=$row['ORDER_QUANTITY']*$smvJobArr[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
			$smvArr[$row['JOB_ID']][$row['ID']]+=$smvmin;
			
			$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		}
		unset($sqlpodata);
		
		$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array(); $colorContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
			$colorContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['contras'][$row['CONTRAST_COLOR_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		//print_r($sqlContrastArr);
		
		//Stripe Details
		$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			//$colorNoArr[$row['JOB_ID']][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		}
		unset($sqlStripeRes);
		
		//Fabric Details
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poidCond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $reqFabArr=array(); $colorNoArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$colorTypeId=$row['COLOR_TYPE_ID'];
			$jobcolorstr=$row['JOB_ID'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']]['strip'];
			/*if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				if (!in_array($jobcolorstr,$jobcolorArr) )
				{
					$noofStripeColor=count($stripe_color);
					$noofColorArr[$row['JOB_ID']][$row['POID']]+=$noofStripeColor;
					$jobcolorArr[]=$jobcolorstr; 
				}
			}
			else
			{*/
				if($row['COLOR_SIZE_SENSITIVE']==3)
				{
					foreach($colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras'] as $contrast)
					{
						$colorNoArr[$row['JOB_ID']][$contrast]=$contrast;//$colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras']);
					}
				}
				else
				{
					$colorNoArr[$row['JOB_ID']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
					/*if (!in_array($jobcolorstr,$jobcolorArr) )
					{
						$noofColorArr[$row['JOB_ID']][$row['POID']]+=1;
						$jobcolorArr[]=$jobcolorstr; 
					}*/
				}
			//}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			//echo $greyReq.'='.$planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'<br>';
			
			$finAmt=$finReq*$row['RATE'];
			$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			if($row['FABRIC_SOURCE']==1)
			{
				$reqFabArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
				if($row['UOM']==12)
				{

					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_mfg_kg]+=$finReq;
				}
			}
			else if($row['FABRIC_SOURCE']==2)
			{
				if($row['UOM']==12)
				{

					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_kg]+=$finReq;
				}
				else if($row['UOM']==1)
				{
					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_pcs]+=$finReq;
				}
				else if($row['UOM']==23 || $row['UOM']==27)
				{
					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_yds_meter]+=$finReq;
				}
			}
			$reqFabArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
		}
		unset($sqlfabRes);
		//print_r($reqFabArr); die;
		
		$noofColorArr=array();
		foreach($colorNoArr as $jobid=>$colordata)
		{
			$c=0;
			foreach($colordata as $colorid)
			{
				$c++;
				//$noofColorArr[$row['JOB_ID']]+=1;
			}
			$noofColorArr[$jobid]=$c;
		}
		//print_r($noofColorArr); die;
		if($taskid==48)
		{
			$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
		
			from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
			//echo $sqlYarn;
			$sqlYarnRes = sql_select($sqlYarn);
			foreach($sqlYarnRes as $row)
			{
				$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
				
				$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
				
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
				
				$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
				
				$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
				
				$yarnAmt=$yarnReq*$row['RATE'];
				
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
				//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
			}
			unset($sqlYarnRes);
		}
		if($taskid==61 || $taskid==63 || $taskid==52)
		{
			//Convaersion Details
			$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
			from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
			//echo $sqlConv; die;
			$sqlConvRes = sql_select($sqlConv);
			$convConsRateArr=array(); $convReqArr=array();
			foreach($sqlConvRes as $row)
			{
				$id=$row['CONVERTION_ID'];
				$colorBreakDown=$row['COLOR_BREAK_DOWN'];
				if($colorBreakDown !="")
				{
					$arr_1=explode("__",$colorBreakDown);
					for($ci=0;$ci<count($arr_1);$ci++)
					{
						$arr_2=explode("_",$arr_1[$ci]);
						$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
						$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
					}
				}
			}
			//echo "ff"; die;
			foreach($sqlConvRes as $row)
			{
				$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
				$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
				
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
				
				$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
				$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
				$consProcessId=$row['CONS_PROCESS'];
				$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
				
				if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
				{
					$qnty=0; $convrate=0;
					foreach($stripe_color as $stripe_color_id)
					{
						$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
						$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
						
						$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
						$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
			
						if($convrate>0){
							$reqqnty+=$qnty;
							$convAmt+=$qnty*$convrate;
						}
					}
				}
				else
				{
					$convrate=$requirment=$reqqnty=0;
					$rateColorId=$row['COLOR_NUMBER_ID'];
					if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
			
					if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
					
					if($convrate>0){
						$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
						$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
						$reqqnty+=$qnty;
						$convAmt+=$qnty*$convrate;
					}
				}
				
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_qty']+=$reqqnty;
				//$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_amt']+=$convAmt;
			}
			unset($sqlConvRes); 
		}
		if($taskid==90 || $taskid==267 || $taskid==268)
		{
			$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
		from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
		where 1=1 and a.cons_dzn_gmts>0 and
		a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
			//echo $sqlEmb; die;
			$sqlEmbRes = sql_select($sqlEmb);
			$embReqArr=array();
			foreach($sqlEmbRes as $row)
			{
				$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				
				$costingPer=$costingPerArr[$row['JOB_ID']];
				$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
				$budget_on=$row['BUDGET_ON'];
				
				$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
				//print_r($poCountryId);
				$calPoPlanQty=0;
				
				if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
				{
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					
					if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
					$consQty=0;
					$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
					$consQnty+=$consQty;
					
					$consAmt=$consQty*$row['RATE'];
				}
				else
				{
					$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
					$consQnty=$consAmt=0;
					foreach($poCountryId as $countryId)
					{
						if(in_array($countryId, $countryIdArr))
						{
							$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
							$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
							
							if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
							$consQty=0;
							$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
							$consQnty+=$consQty;
							//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
							$consAmt+=$consQty*$row['RATE'];
						}
					}
				}
				
				//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
				$embReqArr[$row['JOB_ID']][$row['POID']][$row['EMB_NAME']]['embqty']+=$consQnty;
			}
			unset($sqlEmbRes); 
		}
		$monthDtlsArr=array();
		$monthTnaQntArr=array();
		$task_id_data_not_comes_from_tna = explode(",","2000,903,904,905,9,10");
		foreach($sqlMstRes as $row)
		{
			foreach($dateDataArr[$row['ID']][$row["INTREF"]] as $tnadate=>$shortdate)
			{
				$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
				if($shortdate==$reptMonth)
				{
					//echo $shortdate.'=='.$reptMonth.'<br>';
					//$exday=explode("-",change_date_format($row["TASK_START_DATE"]));
					//print_r($exday);
					//echo $row["TASK_START_DATE"].'='. $row["TASK_FINISH_DATE"].'<br>';
					//$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
					//for($k=($exday[0]*1); $k <= $nodays; $k++)
					//{
						$fdate=$tnadate;//add_date(date("Y-m-d",strtotime(($yearno.'-'.($exday[1]*1).'-'.$k))));
						//echo $fdate.'='.$row["JOB_NO"].'<br>';
						$qty=0;
						

						$tna_plan_target_arr[$row[csf('PO_ID')]][$dmyKey]+= $row['PLAN_QTY'];

						if(!in_array($taskid,$task_id_data_not_comes_from_tna))
						{
							$qty = $tna_plan_target_arr[$row['ID']][date('d-m-Y',strtotime($fdate))];
						}
						


						if($row["TASK_NUMBER"]==9)//Labdip Submission
						{
							$qty=(($noofColorArr[$row['JOBID']]/$noofDays));//*$monthinDays
							//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==10)//Labdip Approval
						{
							$qty=(($noofColorArr[$row['JOBID']]/$noofDays));//*$monthinDays
							//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==48)//Yarn Allocation
						{
							//$qty=(($reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty']/$noofDays));//*$monthinDays
							$index = $row['PO_ID']."*".$dmyKey;

							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==52)//Dyed Yarn Receive
						{
							//$qty=(($convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==60)//Grey Production
						{
							//echo $row['JOB_NO'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'='.$noofDays.'='.$monthinDays.'<br>';
							//$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodgrey_qty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==61)//Dyeing
						{
							//$qty=(($convReqArr[$row['JOBID']][$row['ID']][31]['conv_qty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==63)//AOP Receive
						{
							//$qty=(($convReqArr[$row['JOBID']][$row['ID']][35]['conv_qty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==73)//F. Fabric Recvd
						{
							//$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodfin_qty']/$noofDays));//*$monthinDays
							//$qty=(($reqFabArr[$row['JOBID']][$row['ID']][$task_type]/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==84)//Cutting QC
						{
							//$qty=(($row["PO_QUANTITY"]/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==86)//Sewing[Pcs]
						{
							//$qty=(($row["PO_QUANTITY"]/$noofDays));//*$monthinDays
							//echo $row['JOB_NO'].'='.$row["PO_QUANTITY"].'='.$noofDays.'='.$monthinDays.'<br>';
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
							
							//$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
							//$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$sewMin;
						}
						if($row["TASK_NUMBER"]==902)//Sewing[Minutes]
						{
							
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==267)//Printing Receive
						{
							//$qty=(($embReqArr[$row['JOBID']][$row['ID']][1]['embqty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==268)//Embroidery Receive
						{
							//$qty=(($embReqArr[$row['JOBID']][$row['ID']][2]['embqty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==90)//Garments Wash Rcv
						{
							//$qty=(($embReqArr[$row['JOBID']][$row['ID']][3]['embqty']/$noofDays));//*$monthinDays
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
						if($row["TASK_NUMBER"]==88)//Garments Finishing
						{
							//$qty=(($row["PO_QUANTITY"]/$noofDays));//Garments Finishing
							$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
						}
					}
				}
			//}
		}
		
		$lastday="";
		$rspanArr=array(); $jobrefWiseTotArr=array(); $qtyDataArr=array();
		foreach($monthDtlsArr as $buid=>$bdata)
		{
			$binc=0;

			foreach($bdata as $jobn=>$jobdata)
			{
				foreach($jobdata as $iref=>$irefdata)
				{
					$binc++;
					$row_total = 0;
					foreach($irefdata as $flldate=>$qdata)
					{
						
						$jobrefWiseTotArr[$buid][$jobn][$iref]+=$qdata['qty'];
						$qtyDataArr[$buid][$jobn][$iref][$flldate]+=$qdata['qty'];
						$row_total+=fn_number_format($qdata['qty'],2,".","");
						//echo $qdata['qty'];
						$lastday=$flldate;		
					}
					if($row_total > 0) $rspanArr[$buid]++;
				}
			}
			//$rspanArr[$buid]=$binc;
		}
		//print_r($jobrefWiseTotArr);
			
		$tblwidth=($nodays*40)+180;
		ob_start();
		?>
		<script type="text/javascript">
			function print_rpt()
			{
				document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
				//$("#table_body tr:first").hide();
				
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
				d.close();
				
				document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="4000px";
			}
			
			function print_excel()
			{
				var filename=$('#hiddfilename').val();
				alert(filename);
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
				d.close();
			}
		</script>
	    <div style="width:<?=$tblwidth+20; ?>px;">

	    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
	    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="<?=$nodays+3; ?>" align="center"><?=$months[$monthid].' '.$yearno; ?> : <?=$taskArr[$taskid]; ?></td>
	                
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="30"  valign="middle" >Buyer</td>
	                <td width="60">Ref. No</td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
						?>
	                    <td width="40"><?=$monthval; ?></td>
	                <? } ?>
	                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <? $i=1; $dayTotatArr=array();
	            $buyer_row_tatal = array();
	            foreach($monthDtlsArr as $buyid=>$buydata)
	            {
					$buy=1; $btotal=0; $dayWiseBtotalArr=array();
					foreach($buydata as $jobno=>$jobdata)
					{
						foreach($jobdata as $intref=>$intrefdata)
						{
							//print_r($intrefdata);
							$rtotQty=0;
							
							 $row_total = 0;
							 foreach($monthDateArr as $monthindex=>$monthval)
							 { 
							 	

								$row_total+=fn_number_format($qtyDataArr[$buyid][$jobno][$intref][$monthindex],2,".","");
								 $buyer_row_tatal[$buyid]+=fn_number_format($qtyDataArr[$buyid][$jobno][$intref][$monthindex],2,".","");
							 	
									
							 }
							 if($row_total > 0)
							 {
							 	$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";

							
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								<?  if($buy==1) { ?>
								<td width="30" rowspan="<?=$rspanArr[$buyid]; ?>" align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
								<?
								} ?>
								<td width="60" style="word-break:break-all" title="<?=$jobno; ?>"><?=$intref; ?></td>
								<? foreach($monthDateArr as $monthindex=>$monthval) { 

									$dayWiseQty=0;
									//echo $intrefdata[$monthindex]['qty'];
									//

									$dayWiseQty=$qtyDataArr[$buyid][$jobno][$intref][$monthindex];

									
									
									//echo "<pre>$buyid , $jobno , $intref , $new_date = $dayWiseQty </pre>";
									if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
									$dayWiseBtotalArr[$buyid][$monthindex]+=$dayWiseQty;
									$dayTotatArr[$monthindex]+=$dayWiseQty;
									$rtotQty+=$dayWiseQty;
								?>
									<td width="40" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
								<? } ?>
	                            <td align="right"><?=number_format($rtotQty); ?></td>
							</tr>
							<?
							$btotal+=$rtotQty;
							$gtotal+=$rtotQty;
							$buy++;
							$i++;
							}
						}
					}
					
					if($buyer_row_tatal[$buyid] > 0)
					{
						?>
		                <tr bgcolor="#CCCCCC">
		                    <td width="90" colspan="2" align="right"><b><?=$buyerArr[$buyid].'-Total :'; ?></b></td>
		                    <? foreach($monthDateArr as $monthindex=>$monthval) { 
		                        $dayWiseBuyerQty=0;
		                        $dayWiseBuyerQty=$dayWiseBtotalArr[$buyid][$monthindex];
		                    ?>
		                        <td width="40" align="right" style="word-break:break-all"><?=number_format($dayWiseBuyerQty); ?></td>
		                    <? } ?>
		                    <td align="right" style="word-break:break-all"><?=number_format($btotal); ?></td>
		                </tr>
		                <?
		            }
	            }
	            ?>
	        </table>
	        </div>
	        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr>
	                <td width="90" align="right"><b>Grand Total :</b></td>
	                <? foreach($monthDateArr as $monthindex=>$monthval) { 
	                    $dayTotalQty=0;
	                    $dayTotalQty=$dayTotatArr[$monthindex];
	                ?>
	                    <td width="40" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
	                <? } ?>
	                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
	            </tr>
	        </table>
	    	</div>
	    </div>
	    <?
	}
	exit();
}

if($action =="allocation_count_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	//company_id,monthindex,taskid,location_id,buyer_id,orderStatus
	//echo $company_id.'='.$taskid; die;
	//$task_type
	
	$exmonthindex=explode('-',$monthindex);
	$reptMonth=$monthindex;
	$yearno=$exmonthindex[0];
	$monthid=($exmonthindex[1]*1);
	$monthDateArr=array(); $j=cal_days_in_month(CAL_GREGORIAN, $monthid, $yearno);
	//echo $j; die;
	$i=1;
	for($k=1; $k <= $j; $k++)
	{
		//echo strtotime(($yearno.'-'.$monthid.'-'.$i));
		//$newdate=add_date(date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i))));
		$newdate=date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i)));
		//echo $newdate.'<br>';
		$monthDateArr[$newdate]=date("d-M",strtotime(($newdate)));
		$i++;
	}
	$nodays=$i-1;
	//echo $i;
	//echo "<pre>";
	//print_r($monthDateArr);
	//var_dump($monthDateArr); die;
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($yearno.'-'.$monthid.'-1')));
	$endDate=date("d-M-Y",strtotime(($yearno.'-'.$monthid.'-'.$nodays)));
	//echo $startDate.'=='.$endDate; die;
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";

	//$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	$dateCond="and ( '".$startDate."' between c.task_start_date and c.task_finish_date or '".$endDate."' between c.task_start_date and c.task_finish_date or c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	
	$sqlOrder="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, b.grouping as INTREF, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number='$taskid' $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by a.buyer_name, b.grouping asc"; //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315')
	//echo $sqlOrder; //die; //and a.job_no='FAL-21-00239'  $dateCond
	$sqlMstRes=sql_select( $sqlOrder ); $ponoid=""; $jobId=""; $dateDataArr=array(); $poAvgRateArr=array();
	//print_r($sqlMstRes); die;
	foreach($sqlMstRes as $prow)
	{
		//$poid.=$prow["ID"].",";
		//echo $prow['ID'].'<br>';
		if($ponoid=="") $ponoid=$prow["ID"]; else $ponoid.=",".$prow["ID"];
		if($jobId=="") $jobId="'".$prow["JOBID"]."'"; else $jobId.=",'".$prow["JOBID"]."'";
		
		$total_date=datediff(d, $prow["TASK_START_DATE"], $prow["TASK_FINISH_DATE"]);
		for($k=0; $k<$total_date; $k++)
		{
			$newdate=add_date(date("Y-m-d",strtotime($prow["TASK_START_DATE"])),$k);
			//echo $newdate.'<br>';
			$monthDay=date("Y-m",strtotime($newdate));
			$dateDataArr[$prow['ID']][$prow["INTREF"]][$newdate]=$monthDay;
		}
		$poAvgRateArr[$prow["ID"]]=$prow["UNIT_PRICE"];
	}


	//die;
	//echo $jobId; die;
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $refno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$refno][$fdate]+=1;
			}
		}
	}
	//print_r($ponoid); die;
	/*echo "<pre>";
	print_r($powiseNoofDaysArr); die;*/
	
	$po_ids=array_filter(array_unique(explode(",",$ponoid)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	$jobidRatioCond=where_con_using_array($jobIds,0,"job_id"); 
	$idCond=where_con_using_array($jobIds,0,"a.id"); 

	$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");
	$tna_plan_target_sql = "select a.PO_ID,a.PLAN_QTY,a.PLAN_DATE,b.job_no as JOB_NO, b.buyer_name as BUYER_NAME,c.grouping as INTREF,a.UOM_ID,a.COMPOSITION_ID,a.COUNT_ID  from TNA_PLAN_TARGET a , wo_po_details_master b, wo_po_break_down c where b.id = c.job_id and c.id = a.po_id and a.TASK_ID=$taskid and a.task_type=1 and a.PLAN_DATE between '$startDate' and '$endDate' $po_id_cond ";
	//echo $tna_plan_target_sql;
	$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
	$tna_plan_target_arr = array();
	

	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");

	

	$plan_data = array();

	foreach( $tna_plan_target_sql_res as $row ) 
	{
		$dmyKey = date('d-m-Y', strtotime($row['PLAN_DATE']));
		if(!empty($row['PLAN_QTY']))
		{
			$comp = $composition[$row['COMPOSITION_ID']];
			$count = $lib_yarn_count[$row['COUNT_ID']];
			$plan_data[$comp][$count][$row['BUYER_NAME']][$row['INTREF']][$dmyKey]+=$row['PLAN_QTY'];
		}
	}

	// echo "<pre>";
	// print_r($plan_data);
	// echo "</pre>";

	$tblwidth=($nodays*40)+180+390;
	ob_start();
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}

		
	</script>
    <div style="width:<?=$tblwidth+20; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr style="font-weight:bold; background-color:#FFC">
            	
                <td colspan="<?=$nodays+5; ?>" align="center"><?=$months[$monthid].' '.$yearno; ?> : <?=$taskArr[$taskid]; ?></td>
                
            </tr>
            <tr align="center" style="font-weight:bold; background-color:#CCC">
            	<td width="170"  valign="middle" >Yarn Comp.</td>
                <td width="100">Count</td>
                <td width="100">Buyer</td>
                <td width="100">Ref. No</td>
                <? foreach($monthDateArr as $monthindex=>$monthval) { 
					?>
                    <td width="40"><?=$monthval; ?></td>
                <? } ?>
                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
            </tr>
        </table>
        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <? $i=1; 
            $dayTotatArr=array();
            $comp_row_tatal = array();
            $compSpan = array();
            $countSpan = array();
            $buyerSpan = array();
            $gtotal = 0;
            foreach($plan_data as $comp=>$comp_data)
            {
				$buy=1; $btotal=0; $dayWiseBtotalArr=array();
				foreach($comp_data as $count=>$count_data)
				{
					foreach($count_data as $buyer=>$buyer_data)
					{
						foreach($buyer_data as $intref=>$row)
						{
							foreach($monthDateArr as $monthindex=>$monthval)
							{ 
								$date_key = date("d-m-Y",strtotime($monthindex));
							}
							$compSpan[$comp]++;
							$countSpan[$comp][$count]++;
							$buyerSpan[$comp][$count][$buyer]++;
						} 
						
					}
				}
			}
            foreach($plan_data as $comp=>$comp_data)
            {
				$comp_span=0; $btotal=0; $dayWiseBtotalArr=array();

				foreach($comp_data as $count=>$count_data)
				{
					$count_span = 0;
					foreach($count_data as $buyer=>$buyer_data)
					{
						$buyer_span = 0;
						foreach($buyer_data as $intref=>$row)
						{
							$com_exp = array();
							

							$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								
								<?php if ($comp_span == 0): ?>
									<td width="170" rowspan="<?=$compSpan[$comp];?>"  align="center" valign="middle" style="word-break:break-all"><?=$comp;?></td>
								<?php endif ?>
								
								<?php if ($count_span == 0 ): ?>
									<td width="100" align="center" valign="middle"  rowspan="<?=$countSpan[$comp][$count];?>" style="word-break:break-all" title="<?=$count;?>"><?=$count;?></td>
								<?php endif ?>
								
								<?php if ($buyer_span == 0 ): ?>
									<td width="100"  align="center" valign="middle"  rowspan="<?=$buyerSpan[$comp][$count][$buyer];?>" style="word-break:break-all" title="<?=$buyer; ?>"><?=$buyerArr[$buyer]; ?></td>
								<?php endif ?>
								
								

								
								<td width="100" style="word-break:break-all" title="<?=$intref; ?>"><?=$intref; ?></td>
								<? 
								$rtotQty = 0;
								foreach($monthDateArr as $monthindex=>$monthval)
								{ 

									$dayWiseQty=0;
									$date_key = date('d-m-Y',strtotime($monthindex));
									
									$dayWiseQty=$row[$date_key];

								

									if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);

									$rtotQty+=fn_number_format($dayWiseQty,2,".","");
									$dayWiseBtotalArr[$comp][$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$dayTotatArr[$monthindex]+=fn_number_format($dayWiseQty,2,".","");
									$gtotal+=fn_number_format($dayWiseQty,2,".","");


									?>
									<td width="40" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
									<? 
								} 
								?>
	                            <td align="right"><?=number_format($rtotQty); ?></td>
							</tr>
							<?
							
							$i++;
							$buyer_span++;
							$count_span++;
							$comp_span++;
						}
					}
				}
				
				?>
                <tr bgcolor="#CCCCCC">
                    <td  colspan="4" align="right"><b><?=$buyerArr[$buyid].'-Total :'; ?></b></td>
                    <? foreach($monthDateArr as $monthindex=>$monthval) { 
                        $dayWiseBuyerQty=0;
                        $dayWiseBuyerQty=$dayWiseBtotalArr[$comp][$monthindex];
                    ?>
                        <td width="40" align="right" style="word-break:break-all"><?=number_format($dayWiseBuyerQty); ?></td>
                    <? } ?>
                    <td align="right" style="word-break:break-all"><?=number_format($btotal); ?></td>
                </tr>
                <?
            }
            ?>
        </table>
        </div>
        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr>
            	<td width="170"  ></td>
                <td width="100"></td>
                <td width="100"></td>
               
                <td width="100" align="right" ><b>Grand Total :</b></td>
                <? foreach($monthDateArr as $monthindex=>$monthval)
                { 
                    $dayTotalQty=0;
                    $dayTotalQty=$dayTotatArr[$monthindex];
                	?>
                    <td width="40" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
                	<? 
            	} ?>
                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
            </tr>
        </table>
    	</div>
    </div>
    <?
}

if($action=="total_podetails_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	//company_id,monthindex,taskid,location_id,buyer_id,orderStatus
	//echo $company_id.'='.$taskid; die;
	
	$exfirstYear=explode('-',$monthindex);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	/*
	$exmonthindex=explode('-',$monthindex);
	$reptMonth=$monthindex;
	$yearno=$exmonthindex[0];
	$monthid=($exmonthindex[1]*1);
	$monthDateArr=array(); $j=cal_days_in_month(CAL_GREGORIAN, $monthid, $yearno);
	//echo $j; die;
	$i=1;
	for($k=1; $k <= $j; $k++)
	{
		$newdate=add_date(date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i))));
		$monthDateArr[$newdate]=date("d-M",strtotime(($newdate)));
		$i++;
	}
	*/
	$nodays=$i-1;
	//echo $i;
	//var_dump($monthDateArr); die;
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	//echo $startDate.'=='.$endDate; die;
	
	if($taskid==902) $ntaskid=86; else $ntaskid=$taskid;
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
	
	//$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	$dateCond="and ( '".$startDate."' between c.task_start_date and c.task_finish_date or '".$endDate."' between c.task_start_date and c.task_finish_date or c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	
	$sqlOrder="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, b.grouping as INTREF, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number='$ntaskid' $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by a.buyer_name, b.grouping asc"; //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315')
	//echo $sqlOrder; die; //and a.job_no='FAL-21-00239'  $dateCond
	$sqlMstRes=sql_select( $sqlOrder ); $ponoid=""; $jobId=""; 
	$dateDataArr=array(); 
	
	$poAvgRateArr=array();
	//print_r($sqlMstRes); die;
	foreach($sqlMstRes as $prow)
	{
		//$poid.=$prow["ID"].",";
		//echo $prow['ID'].'<br>';
		if($ponoid=="") $ponoid=$prow["ID"]; else $ponoid.=",".$prow["ID"];
		if($jobId=="") $jobId="'".$prow["JOBID"]."'"; else $jobId.=",'".$prow["JOBID"]."'";
		
		$total_date=datediff(d, $prow["TASK_START_DATE"], $prow["TASK_FINISH_DATE"]);
		for($k=0; $k<$total_date; $k++)
		{
			$newdate=add_date(date("Y-m-d",strtotime($prow["TASK_START_DATE"])),$k);
			//echo $newdate.'<br>';
			$monthDay=date("Y-m",strtotime($newdate));
			$dateDataArr[$prow['ID']][$prow["INTREF"]][$newdate]=$monthDay;
		}
		$poAvgRateArr[$prow["ID"]]=$prow["UNIT_PRICE"];
	}
	//die;
	//echo $jobId; die;
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $refno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$refno][$sdate]+=1;
			}
		}
	}
	//print_r($ponoid); die;
	//echo "<pre>";
	//print_r($dateDataArr); die;
	
	$po_ids=array_filter(array_unique(explode(",",$ponoid)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	$idCond=where_con_using_array($jobIds,0,"a.id");
	$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	$jobidRatioCond=where_con_using_array($jobIds,0,"job_id");



	
	$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobidRatioCond";
	//echo $gmtsitemRatioSql; //die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();

	$job_wise_smv = array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
		$job_wise_smv[$row['JOBNO']] = $row['SMV_PCS'];
	}
	// echo "<pre>";
	// print_r($jobItemRatioArr);
	// echo "</pre>";
	// unset($gmtsitemRatioSqlRes);


	$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");

	
	$tna_plan_target_sql = "select a.PO_ID,a.PLAN_QTY,a.PLAN_DATE,b.job_no as JOB_NO, b.buyer_name as BUYER_NAME,c.grouping as INTREF,a.UOM_ID,a.SOURCE_ID , a.TASK_ID from TNA_PLAN_TARGET a , wo_po_details_master b, wo_po_break_down c where b.id = c.job_id and c.id = a.po_id and a.TASK_ID=$ntaskid and a.task_type=1 and a.PLAN_DATE between '$startDate' and '$endDate' $po_id_cond";
	//echo $tna_plan_target_sql;
	$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
	$tna_plan_target_arr = array();
	foreach( $tna_plan_target_sql_res as $row ) 
	{
		$dmyKey = date('m-Y', strtotime($row['PLAN_DATE']));
		if(!empty($row['PLAN_QTY']))
		{
			//echo "<pre>".$row[csf('TASK_ID')] ."== 86 && ".$ntaskid ."== 902 </pre>";
			if($ntaskid == 73)
			{
				if($task_type == 'f_fabric_rcvd_mfg_kg' && $row['UOM_ID']==12 && $row['SOURCE_ID']==1)
	            {
	            	$tna_plan_target_arr[$row[csf('BUYER_NAME')]][$row[csf('JOB_NO')]][$row[csf('INTREF')]][$dmyKey]+= $row['PLAN_QTY']; 
	            }
	            else if($task_type == 'f_fabric_rcvd_pur_kg' && $row['UOM_ID']==12 && $row['SOURCE_ID']==2)
	            {
	            	$tna_plan_target_arr[$row[csf('BUYER_NAME')]][$row[csf('JOB_NO')]][$row[csf('INTREF')]][$dmyKey]+= $row['PLAN_QTY'];
	            }
	            else if($task_type == 'f_fabric_rcvd_pur_pcs' && $row['UOM_ID']==1 && $row['SOURCE_ID']==2)
	            {
	            	$tna_plan_target_arr[$row[csf('BUYER_NAME')]][$row[csf('JOB_NO')]][$row[csf('INTREF')]][$dmyKey]+= $row['PLAN_QTY'];
	            }
	            else if($task_type == 'f_fabric_rcvd_pur_yds_meter' && ( $row['UOM_ID']==23 || $row['UOM_ID']==27 ) && $row['SOURCE_ID']==2)
	            {
	            	$tna_plan_target_arr[$row[csf('BUYER_NAME')]][$row[csf('JOB_NO')]][$row[csf('INTREF')]][$dmyKey]+= $row['PLAN_QTY'];
	            }
			}
			else if($row[csf('TASK_ID')] == 86 && $taskid == 902)
			{
				$tna_plan_target_arr[$row[csf('BUYER_NAME')]][$row[csf('JOB_NO')]][$row[csf('INTREF')]][$dmyKey]+= ( $row['PLAN_QTY'] *  $job_wise_smv[$row[csf('JOB_NO')]] );
				//echo "<pre>".$row[csf('JOB_NO')]."=>".$job_wise_smv[$row[csf('JOB_NO')]]."=>".( $row['PLAN_QTY'] *  $job_wise_smv[$row[csf('JOB_NO')]] )."</pre>";
			}
			else
			{
				$tna_plan_target_arr[$row[csf('BUYER_NAME')]][$row[csf('JOB_NO')]][$row[csf('INTREF')]][$dmyKey]+= $row['PLAN_QTY'];
			}
		}

		//echo "<pre>tna ".$row[csf('BUYER_NAME')]." , ".$row[csf('JOB_NO')]." , ".$row[csf('INTREF')]." , $dmyKey = ".$row['PLAN_QTY']." </pre>";
		
		//echo "<pre>tna ".$index."=>".$row['PLAN_QTY']."</pre>";
	}
	
	$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $jobidColCond";
	//echo $sqlpoarr; die; //and a.job_no='$job_no'
	$sqlpodata = sql_select($sqlpoarr);
	//print_r($sqlpodata);
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
	foreach($sqlpodata as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		$smvmin=0;
		$smvmin=$row['ORDER_QUANTITY']*$smvJobArr[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
		$smvArr[$row['JOB_ID']][$row['ID']]+=$smvmin;
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	}
	// unset($sqlpodata);
	// echo "<pre>";
	// print_r($costingPerArr);
	// echo "</pre>";
	$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlContrast; die;
	$sqlContrastRes = sql_select($sqlContrast);
	$sqlContrastArr=array(); $colorContrastArr=array();
	foreach($sqlContrastRes as $row)
	{
		$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		$colorContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['contras'][$row['CONTRAST_COLOR_ID']]=$row['CONTRAST_COLOR_ID'];
	}
	unset($sqlContrastRes);
	//print_r($sqlContrastArr);
	
	//Stripe Details
	$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlStripe; die;
	$sqlStripeRes = sql_select($sqlStripe);
	$sqlStripeArr=array();
	foreach($sqlStripeRes as $row)
	{
		$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		//$colorNoArr[$row['JOB_ID']][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
	}
	unset($sqlStripeRes);
	
		//Fabric Details
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poidCond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $reqFabArr=array(); $colorNoArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$colorTypeId=$row['COLOR_TYPE_ID'];
			$jobcolorstr=$row['JOB_ID'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']]['strip'];
			/*if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				if (!in_array($jobcolorstr,$jobcolorArr) )
				{
					$noofStripeColor=count($stripe_color);
					$noofColorArr[$row['JOB_ID']][$row['POID']]+=$noofStripeColor;
					$jobcolorArr[]=$jobcolorstr; 
				}
			}
			else
			{*/
				if($row['COLOR_SIZE_SENSITIVE']==3)
				{
					foreach($colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras'] as $contrast)
					{
						$colorNoArr[$row['JOB_ID']][$contrast]=$contrast;//$colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras']);
					}
				}
				else
				{
					$colorNoArr[$row['JOB_ID']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
					/*if (!in_array($jobcolorstr,$jobcolorArr) )
					{
						$noofColorArr[$row['JOB_ID']][$row['POID']]+=1;
						$jobcolorArr[]=$jobcolorstr; 
					}*/
				}
			//}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			//echo $greyReq.'='.$planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'<br>';
			
			$finAmt=$finReq*$row['RATE'];
			$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			if($row['FABRIC_SOURCE']==1)
			{
				$reqFabArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
				if($row['UOM']==12)
				{

					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_mfg_kg]+=$finReq;
				}
			}
			else if($row['FABRIC_SOURCE']==2)
			{
				if($row['UOM']==12)
				{

					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_kg]+=$finReq;
				}
				else if($row['UOM']==1)
				{
					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_pcs]+=$finReq;
				}
				else if($row['UOM']==23 || $row['UOM']==27)
				{
					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_yds_meter]+=$finReq;
				}
			}
			$reqFabArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
		}
		unset($sqlfabRes);
	
	//print_r($reqFabArr); die;
	
	$noofColorArr=array();
	foreach($colorNoArr as $jobid=>$colordata)
	{
		$c=0;
		foreach($colordata as $colorid)
		{
			$c++;
			//$noofColorArr[$row['JOB_ID']]+=1;
		}
		$noofColorArr[$jobid]=$c;
	}
	//print_r($noofColorArr); die;
	if($taskid==48)
	{
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
	
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlYarn;
		$sqlYarnRes = sql_select($sqlYarn);
		foreach($sqlYarnRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
			
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
			
			$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
			
			$yarnAmt=$yarnReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
		}
		unset($sqlYarnRes);
	}
	if($taskid==61 || $taskid==63 || $taskid==52)
	{
		//Convaersion Details
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convReqArr=array();
		foreach($sqlConvRes as $row)
		{
			$id=$row['CONVERTION_ID'];
			$colorBreakDown=$row['COLOR_BREAK_DOWN'];
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
					$arr_2=explode("_",$arr_1[$ci]);
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
				}
			}
		}
		//echo "ff"; die;
		foreach($sqlConvRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
			$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				$qnty=0; $convrate=0;
				foreach($stripe_color as $stripe_color_id)
				{
					$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
					$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
					
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
		
					if($convrate>0){
						$reqqnty+=$qnty;
						$convAmt+=$qnty*$convrate;
					}
				}
			}
			else
			{
				$convrate=$requirment=$reqqnty=0;
				$rateColorId=$row['COLOR_NUMBER_ID'];
				if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
		
				if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
				
				if($convrate>0){
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty+=$qnty;
					$convAmt+=$qnty*$convrate;
				}
			}
			
			//echo $row['JOB_ID'].'='.$gmtsItem.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_qty']+=$reqqnty;
			//$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes); 
		//die;
	}

	if($taskid==90 || $taskid==267 || $taskid==268)
	{
		$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
		from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
		where 1=1 and a.cons_dzn_gmts>0 and
		a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlEmb; die;
		$sqlEmbRes = sql_select($sqlEmb);
		$embReqArr=array();
		foreach($sqlEmbRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			$budget_on=$row['BUDGET_ON'];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			$calPoPlanQty=0;
			
			if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
				$consQty=0;
				$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
				$consQnty+=$consQty;
				
				$consAmt=$consQty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
				$consQnty=$consAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
						$consQty=0;
						$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
						$consQnty+=$consQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$embReqArr[$row['JOB_ID']][$row['POID']][$row['EMB_NAME']]['embqty']+=$consQnty;
		}
		unset($sqlEmbRes); 
	}
	$monthDtlsArr=array();
	foreach($sqlMstRes as $row)
	{
		foreach($powiseNoofDaysArr[$row['ID']][$row["INTREF"]] as $tnadate=>$monthinDays)
		{
			//echo $shortdate;die;
			$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
			if($noofDays==0)
			{
				$noofDays=1;
			}
			
				
			$fdate=$tnadate;//add_date(date("Y-m-d",strtotime(($yearno.'-'.($exday[1]*1).'-'.$k))));
			//echo $fdate.'='.$row["JOB_NO"].'<br>';
			$qty=0;
			if($row["TASK_NUMBER"]==9)//Labdip Submission
			{
				$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);//*$monthinDays
				//$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==10)//Labdip Approval
			{
				$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);//*$monthinDays
				//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==48)//Yarn Allocation
			{
				$qty=(($reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==52)//Dyed Yarn Receive
			{
				$qty=(($convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==60)//Grey Production
			{
				//echo $row['JOB_NO'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'='.$noofDays.'='.$monthinDays.'<br>';
				$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodgrey_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==61)//Dyeing
			{
				$qty=(($convReqArr[$row['JOBID']][$row['ID']][31]['conv_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==63)//AOP Receive
			{
				$qty=(($convReqArr[$row['JOBID']][$row['ID']][35]['conv_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==73)//F. Fabric Recvd
			{
				//$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodfin_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$qty=(($reqFabArr[$row['JOBID']][$row['ID']][$task_type]/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==84)//Cutting QC
			{
				$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==86 && $taskid==86)//Sewing[Pcs]
			{
				$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);//*$monthinDays
				//echo $row['JOB_NO'].'='.$row["PO_QUANTITY"].'='.$noofDays.'='.$monthinDays.'<br>';
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
				
				//$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				//$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$sewMin;
				
				//$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				//$monthDataArr[902][$sdate]['qty']+=$sewMin;
			}
			if($row["TASK_NUMBER"]==86 && $taskid==902)//Sewing[Minutes]
			{
				$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$sewMin;
			}
			if($row["TASK_NUMBER"]==267)//Printing Receive
			{
				$qty=(($embReqArr[$row['JOBID']][$row['ID']][1]['embqty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==268)//Embroidery Receive
			{
				$qty=(($embReqArr[$row['JOBID']][$row['ID']][2]['embqty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==90)//Garments Wash Rcv
			{
				$qty=(($embReqArr[$row['JOBID']][$row['ID']][3]['embqty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==88)//Garments Finishing
			{
				$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);//Garments Finishing
				$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
		}
	}
	$lastday="";
	$rspanArr=array(); $jobrefWiseTotArr=array(); $qtyDataArr=array();
	foreach($monthDtlsArr as $buid=>$bdata)
	{
		$binc=0;
		foreach($bdata as $jobn=>$jobdata)
		{
			foreach($jobdata as $iref=>$irefdata)
			{
				$binc++;
				foreach($irefdata as $flldate=>$qdata)
				{
					$jobrefWiseTotArr[$buid][$jobn][$iref]+=$qdata['qty'];
					$qtyDataArr[$buid][$jobn][$iref][$flldate]+=$qdata['qty'];
					//echo $qdata['qty'];
					$lastday=$flldate;
				}
			}
		}
		$rspanArr[$buid]=$binc;
	}
	//print_r($jobrefWiseTotArr);
		
	$tblwidth=(count($yearMonth_arr)*80)+180;
	ob_start();
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+20; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr style="font-weight:bold; background-color:#FFC">
            	
                <td colspan="<?=count($yearMonth_arr)+2; ?>" align="center"> <?=$taskArr[$taskid]; ?> : <?=$monthindex;?></td>
                
            </tr>
            <tr align="center" style="font-weight:bold; background-color:#CCC">
            	<td width="100"  valign="middle" >Buyer</td>
               
                <? foreach($yearMonth_arr as $monthindex=>$monthval) { 
					?>
                    <td width="80"><?=$monthval; ?></td>
                <? } ?>
                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
            </tr>
        </table>
        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <? $i=1; $dayTotatArr=array();
   //          echo "<pre>";
			// print_r($monthDtlsArr);
			// echo "<pre>";
			$task_id_data_not_comes_from_tna = explode(",","2000,903,904,905,9,10");
            foreach($monthDtlsArr as $buyid=>$buydata)
            {
				$buy=1; $btotal=0; $dayWiseBtotalArr=array();
				$buyer_wise=array();
				$row_total_check = 0;
				foreach($buydata as $jobno=>$jobdata)
				{
					foreach($jobdata as $intref=>$intrefdata)
					{
						foreach($yearMonth_arr as $monthindex=>$monthval) 
						{ 
							if(in_array($taskid,$task_id_data_not_comes_from_tna))
							{
								$dayWiseQty=$qtyDataArr[$buyid][$jobno][$intref][$monthindex];
							}
							else
							{
								$dayWiseQty=$tna_plan_target_arr[$buyid][$jobno][$intref][date('m-Y',strtotime($monthval))];
							}
							$buyer_wise[$buyid][$monthindex]+=$dayWiseQty;
							$row_total_check+=$dayWiseQty;
						}
					}
				}
				// echo "<pre>";
				// print_r($qtyDataArr[$buyid][$jobno][$intref]);
				// echo "<pre>";
				if($row_total_check > 0 )
				{
					$rtotQty=0;
					$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
					
					?>
					<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
						
						<td width="100"  align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
						
						
						<? foreach($yearMonth_arr as $monthindex=>$monthval) { 
							$dayWiseQty=0;
							//echo $intrefdata[$monthindex]['qty'];
							
							$dayWiseQty=$buyer_wise[$buyid][$monthindex];
							if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
							$dayWiseBtotalArr[$buyid][$monthindex]+=$dayWiseQty;
							$dayTotatArr[$monthindex]+=$dayWiseQty;
							$rtotQty+=$dayWiseQty;
						?>
							<td width="80" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
						<? } ?>
						<td align="right"><?=number_format($rtotQty); ?></td>
					</tr>
					<?
					$btotal+=$rtotQty;
					$gtotal+=$rtotQty;
					$buy++;
					$i++;
				}
			}
            ?>
        </table>
        </div>
        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr>
                <td width="100" align="right"><b>Grand Total :</b></td>
                <? foreach($yearMonth_arr as $monthindex=>$monthval) { 
                    $dayTotalQty=0;
                    $dayTotalQty=$dayTotatArr[$monthindex];
                ?>
                    <td width="80" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
                <? } ?>
                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
            </tr>
        </table>
    	</div>
    </div>
    <?
	exit();
}

if($action=="client_wise_total")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	
	//company_id,monthindex,taskid,location_id,buyer_id,orderStatus
	//echo $company_id.'='.$taskid; die;
	
	$exfirstYear=explode('-',$monthindex);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	/*
	$exmonthindex=explode('-',$monthindex);
	$reptMonth=$monthindex;
	$yearno=$exmonthindex[0];
	$monthid=($exmonthindex[1]*1);
	$monthDateArr=array(); $j=cal_days_in_month(CAL_GREGORIAN, $monthid, $yearno);
	//echo $j; die;
	$i=1;
	for($k=1; $k <= $j; $k++)
	{
		$newdate=add_date(date("Y-m-d",strtotime(($yearno.'-'.$monthid.'-'.$i))));
		$monthDateArr[$newdate]=date("d-M",strtotime(($newdate)));
		$i++;
	}
	*/
	$nodays=$i-1;
	//echo $i;
	//var_dump($monthDateArr); die;
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	//echo $startDate.'=='.$endDate; die;
	
	if($taskid==902) $ntaskid=86; else $ntaskid=$taskid;
	
	//$tnataskprintid_array=array(9,10,48,52,60,61,63,73,84,267,268,90,88);
	//$newTaskAddArr=array(901=> "Sewing[Pcs]",902=> "Sewing[Minutes]",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
	
	//$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	$dateCond="and ( '".$startDate."' between c.task_start_date and c.task_finish_date or '".$endDate."' between c.task_start_date and c.task_finish_date or c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
	
	$sqlOrder="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, b.grouping as INTREF, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE,a.client_id as CLIENT_ID
	 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number='$ntaskid'  $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by a.buyer_name, b.grouping asc"; //and a.job_no in ('FAL-21-00239','FAL-21-00240','FAL-21-00315')
	//echo $sqlOrder; //die; //and a.job_no='FAL-21-00239'  $dateCond
	$sqlMstRes=sql_select( $sqlOrder ); $ponoid=""; $jobId=""; 
	$dateDataArr=array(); 
	
	$poAvgRateArr=array();
	//print_r($sqlMstRes); die;
	$poWiseJob = array();
	foreach($sqlMstRes as $prow)
	{
		//$poid.=$prow["ID"].",";
		//echo $prow['ID'].'<br>';
		if($ponoid=="") $ponoid=$prow["ID"]; else $ponoid.=",".$prow["ID"];
		if($jobId=="") $jobId="'".$prow["JOBID"]."'"; else $jobId.=",'".$prow["JOBID"]."'";
		
		$total_date=datediff(d, $prow["TASK_START_DATE"], $prow["TASK_FINISH_DATE"]);
		for($k=0; $k<$total_date; $k++)
		{
			$newdate=add_date(date("Y-m-d",strtotime($prow["TASK_START_DATE"])),$k);
			//echo $newdate.'<br>';
			$monthDay=date("Y-m",strtotime($newdate));
			$dateDataArr[$prow['ID']][$prow["INTREF"]][$newdate]=$monthDay;
		}
		$poAvgRateArr[$prow["ID"]]=$prow["UNIT_PRICE"];

		$poWiseJob[$prow['ID']] = $prow['JOB_NO'];
	}
	//print_r($poWiseJob);


	//die;
	//echo $jobId; die;
	$powiseNoofDaysArr=array();
	foreach($dateDataArr as $poid=>$podata)
	{
		foreach($podata as $refno=>$tasknodata)
		{
			foreach($tasknodata as $fdate=>$sdate)
			{
				//print_r($sdate);
				//if($taskno==9)
				$powiseNoofDaysArr[$poid][$refno][$sdate]+=1;
			}
		}
	}
	//print_r($ponoid); die;
	//echo "<pre>";
	//print_r($dateDataArr); die;
	
	$po_ids=array_filter(array_unique(explode(",",$ponoid)));
	$jobIds=array_filter(array_unique(explode(",",$jobId)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_break_down_id");
	$poidColCond=where_con_using_array($po_ids,0,"b.id");
	$jobidCond=where_con_using_array($jobIds,0,"a.job_id");
	$idCond=where_con_using_array($jobIds,0,"a.id");
	$jobidColCond=where_con_using_array($jobIds,0,"a.id"); 
	$jobidRatioCond=where_con_using_array($jobIds,0,"job_id"); 


	
	$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobidRatioCond";
	//echo $gmtsitemRatioSql; //die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	$job_wise_smv = array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
		$job_wise_smv[$row['JOBNO']] = $row['SMV_PCS'];;
	}

	$po_id_cond =where_con_using_array($po_ids,0,"PO_ID");
	if($taskid == 902 ) $taskIdForSewingMin = 86;
	else $taskIdForSewingMin = $taskid;
	$tna_plan_target_sql = "SELECT c.TNA_MST_ID,c.TASK_ID,c.TASK_TYPE, c.PO_ID,c.PLAN_QTY,c.PLAN_DATE,c.UOM_ID,c.SOURCE_ID,C.COMPOSITION_ID,C.COUNT_ID,C.COLOUR_ID,C.YARN_COUNT_DETER_ID
		FROM   wo_po_details_master a, wo_po_break_down b, TNA_PLAN_TARGET c
		WHERE      a.job_no = b.job_no_mst
		       AND b.id = C.PO_ID
		       and a.company_name ='$company_id'
		       AND a.is_deleted = 0
		       AND a.status_active = 1
		       AND b.is_deleted = 0
		       AND b.status_active = 1
		       AND c.status_active = 1
		       AND c.is_deleted = 0 and   c.TASK_ID=$taskIdForSewingMin and c.task_type=1 and c.PLAN_DATE between '$startDate' and '$endDate' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond ";
	//echo $tna_plan_target_sql;
	$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
	$tna_plan_target_arr = array();
	foreach( $tna_plan_target_sql_res as $row ) 
	{
		$dmyKey = date('m-Y', strtotime($row['PLAN_DATE']));
		$index =  $row['PO_ID']."*".$dmyKey;
		if(!empty($row['PLAN_QTY']))
		{
			if($row['TASK_ID'] == 73)
			{
				$uom = $row['UOM_ID'];
				if($uom == 27)
				{
					$uom = 23;
				}
				$index = $row['PO_ID'] . "*". $uom . "*". $row['SOURCE_ID'] ."*".$dmyKey;
				$tna_plan_target_arr[$index]+= $row['PLAN_QTY']; 
			}
			else if($row['TASK_ID'] == 86)
			{
				if($taskid == 902 )
				{
					$tna_plan_target_arr[$index]+= ($row['PLAN_QTY'] * $job_wise_smv[$poWiseJob[$row['PO_ID']]]); 
					//echo "<pre>".$poWiseJob[$row['PO_ID']]."=>".$job_wise_smv[$poWiseJob[$row['PO_ID']]]."=>".($row['PLAN_QTY'] * $job_wise_smv[$poWiseJob[$row['PO_ID']]])."</pre>";
				}
				else
				{
					$tna_plan_target_arr[$index]+= $row['PLAN_QTY'];
				}
			}
			else
			{
				$tna_plan_target_arr[$index]+= $row['PLAN_QTY']; 
			}
		}
		
		//echo "<pre>tna ".$index."=>".$row['PLAN_QTY']."</pre>";
	}
	// echo "<pre>";
	// print_r($jobItemRatioArr);
	// echo "</pre>";
	// unset($gmtsitemRatioSqlRes);
	
	$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $jobidColCond";
	//echo $sqlpoarr; die; //and a.job_no='$job_no'
	$sqlpodata = sql_select($sqlpoarr);
	//print_r($sqlpodata);
	$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
	foreach($sqlpodata as $row)
	{
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		$smvmin=0;
		$smvmin=$row['ORDER_QUANTITY']*$smvJobArr[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
		$smvArr[$row['JOB_ID']][$row['ID']]+=$smvmin;
		
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
	}
	// unset($sqlpodata);
	// echo "<pre>";
	// print_r($costingPerArr);
	// echo "</pre>";
	$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlContrast; die;
	$sqlContrastRes = sql_select($sqlContrast);
	$sqlContrastArr=array(); $colorContrastArr=array();
	foreach($sqlContrastRes as $row)
	{
		$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		$colorContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['contras'][$row['CONTRAST_COLOR_ID']]=$row['CONTRAST_COLOR_ID'];
	}
	unset($sqlContrastRes);
	//print_r($sqlContrastArr);
	
	//Stripe Details
	$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0 $jobidCond";
	//echo $sqlStripe; die;
	$sqlStripeRes = sql_select($sqlStripe);
	$sqlStripeArr=array();
	foreach($sqlStripeRes as $row)
	{
		$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
		//$colorNoArr[$row['JOB_ID']][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
	}
	unset($sqlStripeRes);
	
		//Fabric Details
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poidCond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array(); $reqFabArr=array(); $colorNoArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
			$colorTypeId=$row['COLOR_TYPE_ID'];
			$jobcolorstr=$row['JOB_ID'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']]['strip'];
			/*if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				if (!in_array($jobcolorstr,$jobcolorArr) )
				{
					$noofStripeColor=count($stripe_color);
					$noofColorArr[$row['JOB_ID']][$row['POID']]+=$noofStripeColor;
					$jobcolorArr[]=$jobcolorstr; 
				}
			}
			else
			{*/
				if($row['COLOR_SIZE_SENSITIVE']==3)
				{
					foreach($colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras'] as $contrast)
					{
						$colorNoArr[$row['JOB_ID']][$contrast]=$contrast;//$colorContrastArr[$row['JOB_ID']][$row['ID']][$row['COLOR_NUMBER_ID']]['contras']);
					}
				}
				else
				{
					$colorNoArr[$row['JOB_ID']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
					/*if (!in_array($jobcolorstr,$jobcolorArr) )
					{
						$noofColorArr[$row['JOB_ID']][$row['POID']]+=1;
						$jobcolorArr[]=$jobcolorstr; 
					}*/
				}
			//}
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
			$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			//echo $greyReq.'='.$planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'<br>';
			
			$finAmt=$finReq*$row['RATE'];
			$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			if($row['FABRIC_SOURCE']==1)
			{
				$reqFabArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
				if($row['UOM']==12)
				{

					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_mfg_kg]+=$finReq;
				}
			}
			else if($row['FABRIC_SOURCE']==2)
			{
				if($row['UOM']==12)
				{

					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_kg]+=$finReq;
				}
				else if($row['UOM']==1)
				{
					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_pcs]+=$finReq;
				}
				else if($row['UOM']==23 || $row['UOM']==27)
				{
					$reqFabArr[$row['JOB_ID']][$row['POID']][f_fabric_rcvd_pur_yds_meter]+=$finReq;
				}
			}
			$reqFabArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
		}
		unset($sqlfabRes);
	
	//print_r($reqFabArr); die;
	
	$noofColorArr=array();
	foreach($colorNoArr as $jobid=>$colordata)
	{
		$c=0;
		foreach($colordata as $colorid)
		{
			$c++;
			//$noofColorArr[$row['JOB_ID']]+=1;
		}
		$noofColorArr[$jobid]=$c;
	}
	//print_r($noofColorArr); die;
	if($taskid==48)
	{
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
	
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlYarn;
		$sqlYarnRes = sql_select($sqlYarn);
		foreach($sqlYarnRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
			
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
			
			$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
			
			$yarnAmt=$yarnReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_qty']+=$yarnReq;
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['yarn_amt']+=$yarnAmt;
		}
		unset($sqlYarnRes);
	}
	if($taskid==61 || $taskid==63 || $taskid==52)
	{
		//Convaersion Details
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array(); $convReqArr=array();
		foreach($sqlConvRes as $row)
		{
			$id=$row['CONVERTION_ID'];
			$colorBreakDown=$row['COLOR_BREAK_DOWN'];
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
					$arr_2=explode("_",$arr_1[$ci]);
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
				}
			}
		}
		//echo "ff"; die;
		foreach($sqlConvRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
			$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				$qnty=0; $convrate=0;
				foreach($stripe_color as $stripe_color_id)
				{
					$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
					$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
					
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
		
					if($convrate>0){
						$reqqnty+=$qnty;
						$convAmt+=$qnty*$convrate;
					}
				}
			}
			else
			{
				$convrate=$requirment=$reqqnty=0;
				$rateColorId=$row['COLOR_NUMBER_ID'];
				if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
		
				if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
				
				if($convrate>0){
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
					$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
					$reqqnty+=$qnty;
					$convAmt+=$qnty*$convrate;
				}
			}
			
			//echo $row['JOB_ID'].'='.$gmtsItem.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_qty']+=$reqqnty;
			//$convReqArr[$row['JOB_ID']][$row['POID']][$row['CONS_PROCESS']]['conv_amt']+=$convAmt;
		}
		unset($sqlConvRes); 
		//die;
	}

	if($taskid==90 || $taskid==267 || $taskid==268)
	{
		$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
		from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b 
		where 1=1 and a.cons_dzn_gmts>0 and
		a.job_id=b.job_id and a.id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $jobidCond";
		//echo $sqlEmb; die;
		$sqlEmbRes = sql_select($sqlEmb);
		$embReqArr=array();
		foreach($sqlEmbRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			$budget_on=$row['BUDGET_ON'];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			$calPoPlanQty=0;
			
			if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
				$consQty=0;
				$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
				$consQnty+=$consQty;
				
				$consAmt=$consQty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
				$consQnty=$consAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
						$consQty=0;
						$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer)*12;
						$consQnty+=$consQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$embReqArr[$row['JOB_ID']][$row['POID']][$row['EMB_NAME']]['embqty']+=$consQnty;
		}
		unset($sqlEmbRes); 
	}
	$monthDtlsArr=array();
	foreach($sqlMstRes as $row)
	{
		foreach($powiseNoofDaysArr[$row['ID']][$row["INTREF"]] as $tnadate=>$monthinDays)
		{
			//echo $shortdate;die;
			$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
			if($noofDays==0)
			{
				$noofDays=1;
			}
			
			//$row["TASK_NUMBER"]==86 && $taskid
				
			$fdate=$tnadate;//add_date(date("Y-m-d",strtotime(($yearno.'-'.($exday[1]*1).'-'.$k))));
			//echo $fdate.'='.$row["JOB_NO"].'<br>';
			$qty=0;
			$dmyKey = date('m-Y', strtotime($fdate));
			$index = $row['ID']."*".$dmyKey;
			$qty = $tna_plan_target_arr[$index];
			if($row["TASK_NUMBER"]==9)//Labdip Submission
			{
				//$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);//*$monthinDays
				$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);
				//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==10)//Labdip Approval
			{
				$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);//*$monthinDays
				//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==48)//Yarn Allocation
			{
				//$qty=(($reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==52)//Dyed Yarn Receive
			{
				//$qty=(($convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==60)//Grey Production
			{
				//echo $row['JOB_NO'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'='.$noofDays.'='.$monthinDays.'<br>';
				//$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodgrey_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==61)//Dyeing
			{
				//$qty=(($convReqArr[$row['JOBID']][$row['ID']][31]['conv_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==63)//AOP Receive
			{
				//$qty=(($convReqArr[$row['JOBID']][$row['ID']][35]['conv_qty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==73)//F. Fabric Recvd
			{
				//$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodfin_qty']/$noofDays)*$monthinDays);//*$monthinDays
				//$qty=(($reqFabArr[$row['JOBID']][$row['ID']][$task_type]/$noofDays)*$monthinDays);//*$monthinDays

				$dmyKey = date("m-Y",strtotime($fdate));
				
				if($task_type == 'f_fabric_rcvd_mfg_kg')
                {
                	$index = $row['ID']."*12*1*".$dmyKey;
                }
                else if($task_type == 'f_fabric_rcvd_pur_kg')
                {
                	$index =$row['ID']."*12*2*".$dmyKey;
                }
                else if($task_type == 'f_fabric_rcvd_pur_pcs')
                {
                	$index = $row['ID']."*1*2*".$dmyKey;
                }
                else if($task_type == 'f_fabric_rcvd_pur_yds_meter')
                {
                	$index = $row['ID']."*23*2*".$dmyKey;
                }
                //echo "<pre>$index = $qty</pre>";
                $qty = $tna_plan_target_arr[$index];
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==84)//Cutting QC
			{
				//$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==86 && $taskid==86)//Sewing[Pcs]
			{
				//$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);//*$monthinDays
				//echo $row['JOB_NO'].'='.$row["PO_QUANTITY"].'='.$noofDays.'='.$monthinDays.'<br>';
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
				
				//$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				//$monthDtlsArr[$row["BUYER_NAME"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$sewMin;
				
				//$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				//$monthDataArr[902][$sdate]['qty']+=$sewMin;
			}
			if($row["TASK_NUMBER"]==86 && $taskid==902)//Sewing[Minutes]
			{

				//$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==267)//Printing Receive
			{
				//$qty=(($embReqArr[$row['JOBID']][$row['ID']][1]['embqty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==268)//Embroidery Receive
			{
				//$qty=(($embReqArr[$row['JOBID']][$row['ID']][2]['embqty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==90)//Garments Wash Rcv
			{
				//$qty=(($embReqArr[$row['JOBID']][$row['ID']][3]['embqty']/$noofDays)*$monthinDays);//*$monthinDays
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
			if($row["TASK_NUMBER"]==88)//Garments Finishing
			{
				//$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);//Garments Finishing
				
				//echo "<pre>".$index."=>".$qty."</pre>";
				$monthDtlsArr[$row["CLIENT_ID"]][$row["JOB_NO"]][$row["INTREF"]][$fdate]['qty']+=$qty;
			}
		}
	}
	$lastday="";
	$rspanArr=array(); $jobrefWiseTotArr=array(); $qtyDataArr=array();
	foreach($monthDtlsArr as $buid=>$bdata)
	{
		$binc=0;
		foreach($bdata as $jobn=>$jobdata)
		{
			foreach($jobdata as $iref=>$irefdata)
			{
				$binc++;
				foreach($irefdata as $flldate=>$qdata)
				{
					
					$jobrefWiseTotArr[$buid][$jobn][$iref]+=$qdata['qty'];
					$qtyDataArr[$buid][$jobn][$iref][$flldate]+=$qdata['qty'];
					//echo $qdata['qty'];
					$lastday=$flldate;
				}
			}
		}
		$rspanArr[$buid]=$binc;
	}
	//print_r($jobrefWiseTotArr);
		
	$tblwidth=(count($yearMonth_arr)*80)+180;
	ob_start();
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+20; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr style="font-weight:bold; background-color:#FFC">
            	
                <td colspan="<?=count($yearMonth_arr)+2; ?>" align="center"> <?=$taskArr[$taskid]; ?> : <?=$monthindex;?></td>
                
            </tr>
            <tr align="center" style="font-weight:bold; background-color:#CCC">
            	<td width="100"  valign="middle" >Client</td>
               
                <? foreach($yearMonth_arr as $monthindex=>$monthval) { 
					?>
                    <td width="80"><?=$monthval; ?></td>
                <? } ?>
                <td  valign="middle" style="word-break:break-all" align="center">Total</td>
            </tr>
        </table>
        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <? $i=1; $dayTotatArr=array();
   //          echo "<pre>";
			// print_r($monthDtlsArr);
			// echo "<pre>";
			$task_id_data_not_comes_from_tna = explode(',',"2000,903,904,905,9,10");
            foreach($monthDtlsArr as $buyid=>$buydata)
            {
				$buy=1; $btotal=0; $dayWiseBtotalArr=array();
				$buyer_wise=array();
				$row_total_check = 0;
				foreach($buydata as $jobno=>$jobdata)
				{
					foreach($jobdata as $intref=>$intrefdata)
					{
						foreach($yearMonth_arr as $monthindex=>$monthval) 
						{ 
							$dayWiseQty=$qtyDataArr[$buyid][$jobno][$intref][$monthindex];
							$buyer_wise[$buyid][$monthindex]+=$dayWiseQty;
							$row_total_check+=fn_number_format($dayWiseQty,2,".","");
						}
					}
				}

				// echo "<pre>";
				// print_r($qtyDataArr[$buyid][$jobno][$intref]);
				// echo "<pre>";
				if($row_total_check > 0 )
				{


					$rtotQty=0;
					$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
					
					?>
					<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
						
						<td width="100"  align="center" valign="middle" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
						
						
						<? foreach($yearMonth_arr as $monthindex=>$monthval) { 
							$dayWiseQty=0;
							//echo $intrefdata[$monthindex]['qty'];

							//$dayWiseQty=$buyer_wise[$buyid][$monthindex];
							
							$dayWiseQty=$buyer_wise[$buyid][$monthindex];
							
							
			                
							
							
							if($dayWiseQty=="" || $dayWiseQty==0) $rqtyshow=""; else $rqtyshow=number_format($dayWiseQty);
							$dayWiseBtotalArr[$buyid][$monthindex]+=$dayWiseQty;
							$dayTotatArr[$monthindex]+=$dayWiseQty;
							$rtotQty+=$dayWiseQty;
						?>
							<td width="80" title="<?=$dayWiseQty; ?>" align="right" style="word-break:break-all"><?=$rqtyshow; ?></td>
						<? } ?>
						<td align="right"><?=number_format($rtotQty); ?></td>
					</tr>
					<?
					$btotal+=$rtotQty;
					$gtotal+=$rtotQty;
					$buy++;
					$i++;
				}
			}
            ?>
        </table>
        </div>
        <table class="tbl_bottom" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
            <tr>
                <td width="100" align="right"><b>Grand Total :</b></td>
                <? foreach($yearMonth_arr as $monthindex=>$monthval) { 
                    $dayTotalQty=0;
                    $dayTotalQty=$dayTotatArr[$monthindex];
                ?>
                    <td width="80" align="right" style="word-break:break-all"><?=number_format($dayTotalQty); ?></td>
                <? } ?>
                <td align="center" style="word-break:break-all"><?=number_format($gtotal); ?></td>
            </tr>
        </table>
    	</div>
    </div>
    <?
	exit();
}

if($action=="order_quantity_popup_backup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$clientArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $startDate=$monthindex;
    $endDate=$taskid;
    $start_d=explode("-", $monthindex);
    $start_e=explode("-", $taskid);
    $order_summary_name='';
    if($start_d[2]==$start_e[2])
    {
    	$order_summary_name=$start_d[1]." ".$start_d[2];
    }
    else
    {
    	$order_summary_name=min($start_d[2],$start_e[2])."-".max($start_d[2],$start_e[2]);
    }
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	
	$actualPoSql="SELECT a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,(b.unit_price/a.total_set_qnty) as UNIT_PRICE,c.acc_po_no AS ACC_PO_NO,c.id as ACC_PO_ID,d.country_id AS COUNTRY_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;
	
	$actualPoSqlRes=sql_select($actualPoSql); $actualPoArr=array();
	$monthDateArr=array();
	$uniqe_acc_po=array();
	$po_id_arr=array();
	$acc_po_id_arr=array();
	$acc_county_no_arr = array();
	foreach($actualPoSqlRes as $arow)
	{
		//array_push($po_id_arr, $arow['PO_BREAK_DOWN_ID']);
		$acc_po_buyer_arr[$arow['ACC_PO_ID']]=$arow['BUYER_NAME'];
		if(empty($acc_county_no_arr[$arow['ACC_PO_ID']])) $acc_county_no_arr[$arow['ACC_PO_ID']]=$arow['COUNTRY_ID'];
		$acc_po_id_arr[$arow['ACC_PO_ID']] = $arow['ACC_PO_ID'];
	}
	$user_id = $_SESSION['logic_erp']['user_id'];
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from = 22 and ENTRY_FORM=888");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 888, 22, $acc_po_id_arr, $empty_arr);//PO ID

	$sql_carton=sql_select("SELECT a.id,b.carton_qty,b.cbm,a.acc_ship_date FROM wo_po_acc_po_info a,wo_po_act_pack_finish_info b,gbl_temp_engine g  where a.id=b.act_po_id and a.is_deleted=0 and b.is_deleted=0 and g.ref_val = a.id and  g.user_id = $user_id and g.ref_from = 22 and g.entry_form=888");

	$actual_po_carton_date_arr = array();
	foreach ($sql_carton as $row) {
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['carton_qty']+=$row[csf('carton_qty')];
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['cbm']+=$row[csf('cbm')];
	}
	$sql_pre_final=sql_select("SELECT b.actual_po_id,a.inspected_by, b.current_inspection_qnty, a.inspection_level, b.actual_po_ship_date,b.country_id FROM pro_buyer_inspection a, pro_buyer_inspection_dtls b,gbl_temp_engine g WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 and b.inspection_status=1 and g.ref_val = b.actual_po_id and  g.user_id = $user_id and g.ref_from = 22 and g.entry_form=888 ");

	$actual_po_pre_final_arr = array();
	foreach ($sql_pre_final as $row)
	{
		
		if($row[csf('inspection_level')] == 1 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$row[csf('actual_po_ship_date')]][$row[csf('country_id')]]['pre_final']+=$row[csf('current_inspection_qnty')];
		}
		else if($row[csf('inspection_level')] == 2 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$row[csf('actual_po_ship_date')]][$row[csf('country_id')]]['final']+=$row[csf('current_inspection_qnty')];
		}
	}
	$duplicate_check = array();
	$acc_avg_rate = array();
	foreach($actualPoSqlRes as $arow)
	{
		$monthDataArr[$arow["BUYER_NAME"]][903]+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow["BUYER_NAME"]][904]+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow["BUYER_NAME"]][905]+=$shpmentVal;
		$acc_avg_rate[$arow["ACC_PO_ID"]."*".$arow['ACC_SHIP_DATE']] = fn_number_format($shpmentVal/$arow["ACC_PO_QTY"],3,".","");
		$acc_po_and_ship_date = $arow["ACC_PO_ID"] . "*".$arow['ACC_SHIP_DATE']."*".$arow["COUNTRY_ID"];
		if(empty($duplicate_check[$acc_po_and_ship_date]))
		{
			$monthDataArr[$arow["BUYER_NAME"]]['carton_qty']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['carton_qty'];
			$monthDataArr[$arow["BUYER_NAME"]]['cbm']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['cbm'];

			$monthDataArr[$arow["BUYER_NAME"]]['pre_final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['pre_final'];
			$monthDataArr[$arow["BUYER_NAME"]]['final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['final'];
			$duplicate_check[$acc_po_and_ship_date] = $acc_po_and_ship_date ;
		}
		

		$key=$arow['PO_BREAK_DOWN_ID'].'***'.$arow['ACC_PO_NO'];
		if(!in_array($key, $uniqe_acc_po))
		{
			$monthDataArr[$arow["BUYER_NAME"]]['no_of_acc']=$monthDataArr[$arow["BUYER_NAME"]]['no_of_acc']+1;
			array_push($uniqe_acc_po, $key);
		}
	}
	
	$sql_fin ="SELECT a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_garments_prod_actual_po_details c,
	       pro_garments_production_mst d,
	       gbl_temp_engine g
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and c.mst_id = d.id
	       and c.production_type = 8
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       and d.is_deleted = 0
	       and g.ref_val = a.id 
	       and g.user_id = $user_id 
	       and g.ref_from = 22 
	       and g.entry_form = 888";
	
	// echo "<pre>";
	// echo $sql_fin;
	// echo "</pre>";
	$uniqe_cartoon = array(); 
	$res_fin = sql_select($sql_fin);
	foreach ($res_fin as $row) 
	{
		$index = $row[csf('actual_po_id')] ."*". $row[csf('acc_ship_date')] ."*". $row[csf('country_id')] ."*". $row[csf('id')];
		$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['fin']+=$row[csf('prod_qty')];
		if(empty($uniqe_cartoon[$index]))
		{
			$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['ok_carton']+=$row[csf('carton_qty')];
			$uniqe_cartoon[$index] = $index;
		}
	}

	$sql_delivery ="SELECT a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_ex_factory_actual_po_details c,
	       gbl_temp_engine g
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       and g.ref_val = a.id 
	       and g.user_id = $user_id 
	       and g.ref_from = 22 
	       and g.entry_form = 888";
	//echo $sql_delivery;
	$res_delivery = sql_select($sql_delivery);      
	foreach ($res_delivery as $row) 
	{
		$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['QNTY']+=$row[csf('ex_fact_qty')];
		$avg_rate = $acc_avg_rate[$row[csf('actual_po_id')]."*".$row[csf('acc_ship_date')]];
		$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['value']+=($row[csf('ex_fact_qty')]*$avg_rate);
	}

	unset($actualPoSqlRes);
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from = 22 and ENTRY_FORM=888");
	oci_commit($con);
	disconnect($con);
	$tblwidth=220+12*60+70*6;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="22" align="center">Shipment Summery by Buyer: <?=$order_summary_name; ?> <?php 
	                 if(!empty($client_id))
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: <?=$clientArr[$client_id];?></span>
	                 	<?
	                 }
	                 else
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: All</span>
	                 	<?
	                 }
	                 	?></td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="80" >Buyer</td>
	                <td width="40">No of PO</td>
	                <td width="70">Order Qty</td>
	               
	                <td width="70">Amount</td>
	                <td width="50">Avg. SMV</td>
	                <td width="50">Avg FOB</td>


	                <td style="word-break: break-all;" width="60">Req CBM</td>
	                <td style="word-break: break-all;" width="60" title="Ok CBM=(Req CBM/Req Carton) x (Ok Carton)">Ok CBM</td>
	                <td style="word-break: break-all;" width="60">Bal CBM</td>
	                <td style="word-break: break-all;" width="60">Req Carton</td>
	                <td style="word-break: break-all;" width="60">Ok Carton</td>
	                <td style="word-break: break-all;" width="60">Bal Carton</td>
	                <td style="word-break: break-all;" width="60">Finish Qty</td>
	                <td style="word-break: break-all;" width="60">Finish Bal</td>
	                <td style="word-break: break-all;" width="60">Pre final</td>
	                <td style="word-break: break-all;" width="60">Prefinal<br>Bal</td>
	                <td style="word-break: break-all;" width="60">Final</td>
	                <td style="word-break: break-all;" width="60">Final Bal</td>

	                <td style="word-break: break-all;" width="70">Exfactory</td>
	                <td style="word-break: break-all;" width="70">Exfactory Bal</td>
	                <td style="word-break: break-all;" width="70">Value</td>
	                <td style="word-break: break-all;" width="70">Value Bal</td>
	              


	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();

		            foreach($monthDataArr as $buyid=>$buydata)
		            {
						$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
								//print_r($intrefdata);
								$rtotQty=0;
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									<td width="80"   style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
									<?  
											$dev=1;
						                   	if($buydata[903]>0)
						                   	{
						                   		$dev=$buydata[903];
						                   	}
						                    $avg_smv=($buydata[904])/$dev;
						                    $avg_fob=($buydata[905])/$dev;
						                    $summary_total['order_qty']+=$buydata[903];
						                    $summary_total['minute']+=$buydata[904];
						                    $summary_total['amount']+=$buydata[905];
						                    $summary_total['avg_smv']+=$avg_smv;
						                    $summary_total['avg_fob']+=$avg_fob;
						                    $summary_total['no_of_acc']+=$buydata['no_of_acc'];
						                    $summary_total['carton_qty']+=$buydata['carton_qty'];
						                    $summary_total['ok_carton']+=$buydata['ok_carton'];
						                    $summary_total['fin']+=$buydata['fin'];
						                    $bal_carton = $buydata['carton_qty'] - $buydata['ok_carton'];
						                    $summary_total['bal_carton']+=$bal_carton;
						                    $summary_total['cbm']+=$buydata['cbm'];
						                    $summary_total['pre_final']+=$buydata['pre_final'];
						                    $summary_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);
						                    $summary_total['final']+=$buydata['final'];
						                    $summary_total['final_balance']+=($buydata[903]-$buydata['final']);
						                   
						                $ok_cbm=fn_number_format(fn_number_format($buydata['cbm'],2,".","")/fn_number_format($buydata['carton_qty'],2,".",""),2,".","") * (fn_number_format($buydata['ok_carton'],2,".","")) ; 
						                $summary_total['ok_cbm']+=$ok_cbm;
						                $balance_cbm=($buydata['cbm']-$ok_cbm);
						                $summary_total['balance_cbm']+= $balance_cbm; 

						                $fin_balance=($buydata[903]-$buydata['fin']);
						                $summary_total['fin_balance']+= $fin_balance; 
										
									?>
										<td width="40" title="<?=number_format($buydata['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$buydata['no_of_acc']; ?></td>
										<td width="70" title="<?=number_format($buydata[903]); ?>" align="right" style="word-break:break-all"><?=$buydata[903]; ?></td>
										
										<td width="70" title="<?=number_format($buydata[905]); ?>" align="right" style="word-break:break-all"><?=number_format($buydata[905]); ?></td>
										<td width="50" title="Avg. SMV=Minutes/Order Qty" align="right" style="word-break:break-all"><?=number_format($avg_smv,2); ?></td>
										<td width="50" title="Avg. FOB=Amount/Order Qty" align="right" style="word-break:break-all"><?=number_format($avg_fob,2); ?></td>



										<td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['cbm'],2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($ok_cbm,2);?></td>
						               <td align="right" style="word-break: break-all;" width="60"><?=number_format($balance_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['carton_qty']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['ok_carton']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($bal_carton);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['fin']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($fin_balance);?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata['pre_final']); ?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata[903]-$buydata['pre_final']); ?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata['final']); ?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata[903]-$buydata['final']); ?></td>

						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[903]-$buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70" title="<?=$avg_fob;?>"><?=number_format($buydata['value']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[905]-$buydata['value']);?></td>

								</tr>
								<?
								$summary_total['QNTY']+=$buydata['QNTY'];
						        $summary_total['ex_balance']+=($buydata['value']);
								$i++;
						}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
                    <tr align="center" style="font-weight:bold; background-color:#CCC">
                        <td width="80" >Total</td>
                        <td width="40" title="<?=number_format($summary_total['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$summary_total['no_of_acc']; ?></td>
                        <td width="70" title="<?=number_format($summary_total['order_qty']); ?>" align="right" style="word-break:break-all"><?=$summary_total['order_qty']; ?></td>
                       
                        <td width="70" title="<?=number_format($summary_total['amount']); ?>" align="right" style="word-break:break-all"><?=number_format($summary_total['amount']); ?></td>
                        <td width="50" title="Avg. SMV=Minutes/Order Qty" align="right" style="word-break:break-all"><?=number_format($summary_total['minute']/($summary_total['order_qty']==0 ? 1:$summary_total['order_qty']  ),2); ?></td>
                        <td width="50" title="Avg. FOB=Amount/Order Qty" align="right" style="word-break:break-all"><?=number_format($summary_total['amount']/($summary_total['order_qty']==0 ? 1:$summary_total['order_qty']  ),2); ?></td>

                        <?php 
                        	$summary_total_ok_cbm = fn_number_format(($summary_total['cbm']/$summary_total['carton_qty'])*$summary_total['ok_carton'],2,".","");

                        	$summary_total_balance_cbm = $summary_total_ok_cbm - $summary_total['cbm'];
                         ?>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['cbm'],2);?></td>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_ok_cbm,2);?></td>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_balance_cbm,2);?></td>
		                
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['carton_qty']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['ok_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['bal_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin_balance']);?></td>
		                <td  style="word-break: break-all;" align="right"  width="60"><?=number_format($summary_total['pre_final']);?></td>
		                <td style="word-break: break-all;" align="right" width="60"><?=number_format($summary_total['pre_final_balance']);?></td>
		                <td style="word-break: break-all;" align="right" width="60"><?=number_format($summary_total['final']);?></td>
		                <td style="word-break: break-all;" align="right" width="60"><?=number_format($summary_total['final_balance']);?></td>

		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['order_qty']-$summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['ex_balance']);?></td>
		               <td  align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['amount']-$summary_total['ex_balance']);?></td>
                        
                    </tr>
                </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

if($action=="order_quantity_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$clientArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $startDate=$monthindex;
    $endDate=$taskid;
    $start_d=explode("-", $monthindex);
    $start_e=explode("-", $taskid);
    $order_summary_name='';
    if($start_d[2]==$start_e[2])
    {
    	$order_summary_name=$start_d[1]." ".$start_d[2];
    }
    else
    {
    	$order_summary_name=min($start_d[2],$start_e[2])."-".max($start_d[2],$start_e[2]);
    }
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	
	$actualPoSql="SELECT a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,d.unit_price as UNIT_PRICE,c.acc_po_no AS ACC_PO_NO,c.id as ACC_PO_ID,d.country_id AS COUNTRY_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;
	
	$actualPoSqlRes=sql_select($actualPoSql); $actualPoArr=array();
	$monthDateArr=array();
	$uniqe_acc_po=array();
	$po_id_arr=array();
	$acc_po_id_arr=array();
	$acc_county_no_arr = array();
	$acc_po_ship_date_arr = array();
	foreach($actualPoSqlRes as $arow)
	{
		//array_push($po_id_arr, $arow['PO_BREAK_DOWN_ID']);
		$acc_po_buyer_arr[$arow['ACC_PO_ID']]=$arow['BUYER_NAME'];
		if(empty($acc_county_no_arr[$arow['ACC_PO_ID']])) $acc_county_no_arr[$arow['ACC_PO_ID']]=$arow['COUNTRY_ID'];
		$acc_po_id_arr[$arow['ACC_PO_ID']] = $arow['ACC_PO_ID'];
		$acc_po_ship_date_arr[$arow['ACC_PO_ID']] = $arow['ACC_SHIP_DATE'];
	}
	$user_id = $_SESSION['logic_erp']['user_id'];
	

	$acc_po_id_cond = where_con_using_array($acc_po_id_arr,0,"a.id");

	
	$sql_carton=sql_select("SELECT a.id,b.carton_qty,b.cbm,a.acc_ship_date FROM wo_po_acc_po_info a,wo_po_act_pack_finish_info b  where a.id=b.act_po_id and a.is_deleted=0 and b.is_deleted=0 $acc_po_id_cond");

	$actual_po_carton_date_arr = array();
	foreach ($sql_carton as $row) {
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['carton_qty']+=$row[csf('carton_qty')];
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['cbm']+=$row[csf('cbm')];
	}
	
	$acc_po_id_cond = where_con_using_array($acc_po_id_arr,0,"b.actual_po_id");
	$sql_pre_final=sql_select("SELECT b.actual_po_id,a.inspected_by, b.current_inspection_qnty, a.inspection_level, b.actual_po_ship_date,b.country_id FROM pro_buyer_inspection a, pro_buyer_inspection_dtls b WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 and b.inspection_status=1 $acc_po_id_cond ");

	$actual_po_pre_final_arr = array();
	foreach ($sql_pre_final as $row)
	{
		$acc_ship_date = $acc_po_ship_date_arr[$row[csf('actual_po_id')]];
		if($row[csf('inspection_level')] == 1 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$acc_ship_date][$row[csf('country_id')]]['pre_final']+=$row[csf('current_inspection_qnty')];
		}
		else if($row[csf('inspection_level')] == 2 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$acc_ship_date][$row[csf('country_id')]]['final']+=$row[csf('current_inspection_qnty')];
		}
	}
	$duplicate_check = array();
	$acc_avg_rate = array();
	foreach($actualPoSqlRes as $arow)
	{
		$monthDataArr[$arow["BUYER_NAME"]][903]+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow["BUYER_NAME"]][904]+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow["BUYER_NAME"]][905]+=$shpmentVal;
		//$acc_avg_rate[$arow["ACC_PO_ID"]."*".$arow['ACC_SHIP_DATE']] = fn_number_format($shpmentVal/$arow["ACC_PO_QTY"],3,".","");
		$acc_avg_rate[$arow["ACC_PO_ID"]."*".$arow['ACC_SHIP_DATE']] = $arow["UNIT_PRICE"];
		$acc_po_and_ship_date = $arow["ACC_PO_ID"] . "*".$arow['ACC_SHIP_DATE']."*".$arow["COUNTRY_ID"];
		if(empty($duplicate_check[$acc_po_and_ship_date]))
		{
			$monthDataArr[$arow["BUYER_NAME"]]['carton_qty']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['carton_qty'];
			$monthDataArr[$arow["BUYER_NAME"]]['cbm']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['cbm'];

			$monthDataArr[$arow["BUYER_NAME"]]['pre_final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['pre_final'];
			$monthDataArr[$arow["BUYER_NAME"]]['final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['final'];
			$duplicate_check[$acc_po_and_ship_date] = $acc_po_and_ship_date ;
		}
		

		$key=$arow['PO_BREAK_DOWN_ID'].'***'.$arow['ACC_PO_NO'];
		if(!in_array($key, $uniqe_acc_po))
		{
			$monthDataArr[$arow["BUYER_NAME"]]['no_of_acc']=$monthDataArr[$arow["BUYER_NAME"]]['no_of_acc']+1;
			array_push($uniqe_acc_po, $key);
		}
	}


	$acc_po_id_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	$sql_fin ="SELECT a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_garments_prod_actual_po_details c,
	       pro_garments_production_mst d
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and c.mst_id = d.id
	       and c.production_type = 8
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       and d.is_deleted = 0
	       $acc_po_id_cond";
	
	// echo "<pre>";
	// echo $sql_fin;
	// echo "</pre>";
	$uniqe_cartoon = array(); 
	$res_fin = sql_select($sql_fin);
	foreach ($res_fin as $row) 
	{
		$index = $row[csf('actual_po_id')] ."*". $row[csf('acc_ship_date')] ."*". $row[csf('country_id')] ."*". $row[csf('id')];
		$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['fin']+=$row[csf('prod_qty')];
		if(empty($uniqe_cartoon[$index]))
		{
			$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['ok_carton']+=$row[csf('carton_qty')];
			$uniqe_cartoon[$index] = $index;
		}
	}

	$acc_po_id_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	$sql_delivery ="SELECT a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id,b.unit_price
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_ex_factory_actual_po_details c
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 $acc_po_id_cond
	       ";
	//echo $sql_delivery;
	$res_delivery = sql_select($sql_delivery);      
	foreach ($res_delivery as $row) 
	{
		$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['QNTY']+=$row[csf('ex_fact_qty')];
		$avg_rate = $acc_avg_rate[$row[csf('actual_po_id')]."*".$row[csf('acc_ship_date')]];
		$monthDataArr[$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['value']+=($row[csf('ex_fact_qty')]*$row[csf('unit_price')]);
	}

	unset($actualPoSqlRes);
	
	$tblwidth=220+12*60+70*6;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="22" align="center">Shipment Summery by Buyer: <?=$order_summary_name; ?> <?php 
	                 if(!empty($client_id))
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: <?=$clientArr[$client_id];?></span>
	                 	<?
	                 }
	                 else
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: All</span>
	                 	<?
	                 }
	                 	?></td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="80" >Buyer</td>
	                <td width="40">No of PO</td>
	                <td width="70">Order Qty</td>
	               
	                <td width="70">Amount</td>
	                <td width="50">Avg. SMV</td>
	                <td width="50">Avg FOB</td>


	                <td style="word-break: break-all;" width="60">Req CBM</td>
	                <td style="word-break: break-all;" width="60" title="Ok CBM=(Req CBM/Req Carton) x (Ok Carton)">Ok CBM</td>
	                <td style="word-break: break-all;" width="60">Bal CBM</td>
	                <td style="word-break: break-all;" width="60">Req Carton</td>
	                <td style="word-break: break-all;" width="60">Ok Carton</td>
	                <td style="word-break: break-all;" width="60">Bal Carton</td>
	                <td style="word-break: break-all;" width="60">Finish Qty</td>
	                <td style="word-break: break-all;" width="60">Finish Bal</td>
	                <td style="word-break: break-all;" width="60">Pre final</td>
	                <td style="word-break: break-all;" width="60">Prefinal<br>Bal</td>
	                <td style="word-break: break-all;" width="60">Final</td>
	                <td style="word-break: break-all;" width="60">Final Bal</td>

	                <td style="word-break: break-all;" width="70">Exfactory</td>
	                <td style="word-break: break-all;" width="70">Exfactory Bal</td>
	                <td style="word-break: break-all;" width="70">Value</td>
	                <td style="word-break: break-all;" width="70">Value Bal</td>
	              


	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();

		            foreach($monthDataArr as $buyid=>$buydata)
		            {
						$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
								//print_r($intrefdata);
								$rtotQty=0;
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									<td width="80"   style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
									<?  
											$dev=1;
						                   	if($buydata[903]>0)
						                   	{
						                   		$dev=$buydata[903];
						                   	}
						                    $avg_smv=($buydata[904])/$dev;
						                    $avg_fob=($buydata[905])/$dev;
						                    $summary_total['order_qty']+=$buydata[903];
						                    $summary_total['minute']+=$buydata[904];
						                    $summary_total['amount']+=$buydata[905];
						                    $summary_total['avg_smv']+=$avg_smv;
						                    $summary_total['avg_fob']+=$avg_fob;
						                    $summary_total['no_of_acc']+=$buydata['no_of_acc'];
						                    $summary_total['carton_qty']+=$buydata['carton_qty'];
						                    $summary_total['ok_carton']+=$buydata['ok_carton'];
						                    $summary_total['fin']+=$buydata['fin'];
						                    $bal_carton = $buydata['carton_qty'] - $buydata['ok_carton'];
						                    $summary_total['bal_carton']+=$bal_carton;
						                    $summary_total['cbm']+=$buydata['cbm'];
						                    $summary_total['pre_final']+=$buydata['pre_final'];
						                    $summary_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);
						                    $summary_total['final']+=$buydata['final'];
						                    $summary_total['final_balance']+=($buydata[903]-$buydata['final']);
						                   
						                $ok_cbm=fn_number_format(fn_number_format($buydata['cbm'],8,".","")/fn_number_format($buydata['carton_qty'],8,".",""),8,".","") * (fn_number_format($buydata['ok_carton'],8,".","")) ; 
						                $summary_total['ok_cbm']+=$ok_cbm;
						                $balance_cbm=($buydata['cbm']-$ok_cbm);
						                $summary_total['balance_cbm']+= $balance_cbm; 

						                $fin_balance=($buydata[903]-$buydata['fin']);
						                $summary_total['fin_balance']+= $fin_balance; 
										
									?>
										<td width="40" title="<?=number_format($buydata['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$buydata['no_of_acc']; ?></td>
										<td width="70" title="<?=number_format($buydata[903]); ?>" align="right" style="word-break:break-all"><?=$buydata[903]; ?></td>
										
										<td width="70" title="<?=number_format($buydata[905]); ?>" align="right" style="word-break:break-all"><?=number_format($buydata[905]); ?></td>
										<td width="50" title="Avg. SMV=Minutes/Order Qty" align="right" style="word-break:break-all"><?=number_format($avg_smv,2); ?></td>
										<td width="50" title="Avg. FOB=Amount/Order Qty" align="right" style="word-break:break-all"><?=number_format($avg_fob,2); ?></td>



										<td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['cbm'],2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($ok_cbm,2);?></td>
						               <td align="right" style="word-break: break-all;" width="60"><?=number_format($balance_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['carton_qty']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['ok_carton']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($bal_carton);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['fin']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($fin_balance);?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata['pre_final']); ?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata[903]-$buydata['pre_final']); ?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata['final']); ?></td>
						                <td style="word-break: break-all;" align="right" width="60"><?=number_format($buydata[903]-$buydata['final']); ?></td>

						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[903]-$buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70" title="<?=$avg_fob;?>"><?=number_format($buydata['value']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[905]-$buydata['value']);?></td>

								</tr>
								<?
								$summary_total['QNTY']+=$buydata['QNTY'];
						        $summary_total['ex_balance']+=($buydata['value']);
								$i++;
						}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
                    <tr align="center" style="font-weight:bold; background-color:#CCC">
                        <td width="80" >Total</td>
                        <td width="40" title="<?=number_format($summary_total['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$summary_total['no_of_acc']; ?></td>
                        <td width="70" title="<?=number_format($summary_total['order_qty']); ?>" align="right" style="word-break:break-all"><?=$summary_total['order_qty']; ?></td>
                       
                        <td width="70" title="<?=number_format($summary_total['amount']); ?>" align="right" style="word-break:break-all"><?=number_format($summary_total['amount']); ?></td>
                        <td width="50" title="Avg. SMV=Minutes/Order Qty" align="right" style="word-break:break-all"><?=number_format($summary_total['minute']/($summary_total['order_qty']==0 ? 1:$summary_total['order_qty']  ),2); ?></td>
                        <td width="50" title="Avg. FOB=Amount/Order Qty" align="right" style="word-break:break-all"><?=number_format($summary_total['amount']/($summary_total['order_qty']==0 ? 1:$summary_total['order_qty']  ),2); ?></td>

                        <?php 
                        	$summary_total_ok_cbm = fn_number_format(($summary_total['cbm']/$summary_total['carton_qty'])*$summary_total['ok_carton'],8,".","");

                        	$summary_total_balance_cbm = $summary_total['cbm'] - $summary_total_ok_cbm;
                         ?>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['cbm'],2);?></td>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_ok_cbm,2);?></td>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_balance_cbm,2);?></td>
		                
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['carton_qty']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['ok_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['bal_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin_balance']);?></td>
		                <td  style="word-break: break-all;" align="right"  width="60"><?=number_format($summary_total['pre_final']);?></td>
		                <td style="word-break: break-all;" align="right" width="60"><?=number_format($summary_total['pre_final_balance']);?></td>
		                <td style="word-break: break-all;" align="right" width="60"><?=number_format($summary_total['final']);?></td>
		                <td style="word-break: break-all;" align="right" width="60"><?=number_format($summary_total['final_balance']);?></td>

		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['order_qty']-$summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['ex_balance']);?></td>
		               <td  align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['amount']-$summary_total['ex_balance']);?></td>
                        
                    </tr>
                </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}


if($action=="shipment_summery_by_date_backup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$clientArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $startDate=$monthindex;
    $endDate=$taskid;
    $start_d=explode("-", $monthindex);
    $start_e=explode("-", $taskid);
    $order_summary_name='';
    if($start_d[2]==$start_e[2])
    {
    	$order_summary_name=$start_d[1]." ".$start_d[2];
    }
    else
    {
    	$order_summary_name=min($start_d[2],$start_e[2])."-".max($start_d[2],$start_e[2]);
    }
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	
	$actualPoSql="SELECT a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,(b.unit_price/a.total_set_qnty) as UNIT_PRICE,c.acc_po_no AS ACC_PO_NO,c.id as ACC_PO_ID,d.country_id AS COUNTRY_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;
	
	$actualPoSqlRes=sql_select($actualPoSql); $actualPoArr=array();
	$monthDateArr=array();
	$uniqe_acc_po=array();
	$acc_po_id_arr=array();
	$acc_po_buyer_arr=array();
	$acc_county_no_arr = array();
	foreach($actualPoSqlRes as $arow)
	{
		$acc_po_id_arr[$arow['ACC_PO_ID']] = $arow['ACC_PO_ID'];
		$acc_po_buyer_arr[$arow['ACC_PO_ID']]=$arow['BUYER_NAME'];
		if(empty($acc_county_no_arr[$arow['ACC_PO_ID']])) $acc_county_no_arr[$arow['ACC_PO_ID']]=$arow['COUNTRY_ID'];
	}
	$user_id = $_SESSION['logic_erp']['user_id'];
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from = 23 and ENTRY_FORM=888");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 888, 23, $acc_po_id_arr, $empty_arr);//PO ID

	$sql_carton=sql_select("SELECT a.id,b.carton_qty,b.cbm,a.acc_ship_date FROM wo_po_acc_po_info a,wo_po_act_pack_finish_info b,gbl_temp_engine g  where a.id=b.act_po_id and a.is_deleted=0 and b.is_deleted=0 and g.ref_val = a.id and  g.user_id = $user_id and g.ref_from = 23 and g.entry_form=888");

	$actual_po_carton_date_arr = array();
	foreach ($sql_carton as $row) {
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['carton_qty']+=$row[csf('carton_qty')];
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['cbm']+=$row[csf('cbm')];
	}
	$sql_pre_final=sql_select("SELECT b.actual_po_id,a.inspected_by, b.current_inspection_qnty, a.inspection_level,b.actual_po_ship_date,b.country_id FROM pro_buyer_inspection a, pro_buyer_inspection_dtls b,gbl_temp_engine g WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 and b.inspection_status=1 and g.ref_val = b.actual_po_id and  g.user_id = $user_id and g.ref_from =23 and g.entry_form=888");

	$actual_po_pre_final_arr = array();
	foreach ($sql_pre_final as $row)
	{
		if($row[csf('inspection_level')] == 1 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$row[csf('actual_po_ship_date')]][$row[csf('country_id')]]['pre_final']+=$row[csf('current_inspection_qnty')];
		}
		else if($row[csf('inspection_level')] == 2 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$row[csf('actual_po_ship_date')]][$row[csf('country_id')]]['final']+=$row[csf('current_inspection_qnty')];
		}
	}

	$duplicate_check = array();
	$acc_avg_rate  = array();
	foreach($actualPoSqlRes as $arow)
	{
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][903]+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][904]+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][905]+=$shpmentVal;
		//$acc_po_and_ship_date = $arow["ACC_PO_ID"] ."*".$arow['ACC_SHIP_DATE'];
		$acc_avg_rate[$arow["ACC_PO_ID"]."*".$arow['ACC_SHIP_DATE']] = fn_number_format($shpmentVal/$arow["ACC_PO_QTY"],3,".","");
		$acc_po_and_ship_date = $arow["ACC_PO_ID"] . "*".$arow['ACC_SHIP_DATE']."*".$arow["COUNTRY_ID"];
		if(empty($duplicate_check[$acc_po_and_ship_date]))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['carton_qty']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['carton_qty'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['cbm']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['cbm'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['pre_final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['pre_final'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['final'];
			$duplicate_check[$acc_po_and_ship_date] = $acc_po_and_ship_date;
		}
		
		$key=$arow['PO_BREAK_DOWN_ID'].'***'.$arow['ACC_PO_NO'];
		if(!in_array($key, $uniqe_acc_po))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['no_of_acc']++;
			array_push($uniqe_acc_po, $key);
		}
	}
	

	$sql_fin ="SELECT a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_garments_prod_actual_po_details c,
	       pro_garments_production_mst d,
	       gbl_temp_engine g
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and c.mst_id = d.id
	       and c.production_type = 8
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       and d.is_deleted = 0
	       and g.ref_val = a.id 
	       and g.user_id = $user_id 
	       and g.ref_from = 23 
	       and g.entry_form=888";
	
	// echo "<pre>";
	// echo $sql_fin;
	// echo "</pre>";
	$uniqe_cartoon = array(); 
	$res_fin = sql_select($sql_fin);
	foreach ($res_fin as $row) 
	{
		$index = $row[csf('actual_po_id')] ."*". $row[csf('acc_ship_date')] ."*". $row[csf('country_id')] ."*". $row[csf('id')];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['fin']+=$row[csf('prod_qty')];
		if(empty($uniqe_cartoon[$index]))
		{
			$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['ok_carton']+=$row[csf('carton_qty')];
			$uniqe_cartoon[$index] = $index;
		}
	}

	$sql_delivery ="SELECT a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_ex_factory_actual_po_details c,
	       gbl_temp_engine g
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       and g.ref_val = a.id 
	       and g.user_id = $user_id 
	       and g.ref_from = 23 
	       and g.entry_form=888";
	//echo $sql_delivery;
	$res_delivery = sql_select($sql_delivery);      
	foreach ($res_delivery as $row) 
	{
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['QNTY']+=$row[csf('ex_fact_qty')];
		$avg_rate = $acc_avg_rate[$row[csf('actual_po_id')]."*".$row[csf('acc_ship_date')]];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['value']+=($row[csf('ex_fact_qty')]*$avg_rate);
	}

	unset($actualPoSqlRes);
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from = 23 and ENTRY_FORM=888");
	oci_commit($con);
	disconnect($con);

	$date_span=array();
	foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
	{
		$date_sl=0;
		foreach ($date_wise_data as $buyer_id => $buyer_data) 
		{
			$date_sl++;
		}
		$date_span[$ACC_SHIP_DATE]=$date_sl;
	}
	
	unset($actualPoSqlRes);
	$tblwidth=400+16*60;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="23" align="center">Shipment Summery by date: <?=$order_summary_name; ?> <?php 
	                 if(!empty($client_id))
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: <?=$clientArr[$client_id];?></span>
	                 	<?
	                 }
	                 else
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: All</span>
	                 	<?
	                 }
	                 	?> </td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="65">Ship Date</td>
	            	<td width="80" >Buyer</td>
	                <td width="60">No of PO</td>
	                <td width="80">Order Qty</td>
	               
	                <td width="80">Amount</td>
	               


	               <td style="word-break: break-all;" width="60">Req CBM</td>
	                <td style="word-break: break-all;" width="60">Ok CBM</td>
	                <td style="word-break: break-all;" width="60">Bal CBM</td>
	                <td style="word-break: break-all;" width="60">Req Carton</td>
	                <td style="word-break: break-all;" width="60">Ok Carton</td>
	                <td style="word-break: break-all;" width="60">Bal Carton</td>
	                <td style="word-break: break-all;" width="60">Finish Qty</td>
	                <td style="word-break: break-all;" width="60">Finish Bal</td>
	                <td style="word-break: break-all;" width="60">Pre final</td>
	                <td style="word-break: break-all;" width="60">Prefinal<br>Bal</td>
	                <td style="word-break: break-all;" width="60">Final</td>
	                <td style="word-break: break-all;" width="60">Final Bal</td>

	                <td style="word-break: break-all;" width="70">Exfactory</td>
	                <td style="word-break: break-all;" width="70">Exfactory Bal</td>
	                <td style="word-break: break-all;" width="70">Value</td>
	                <td style="word-break: break-all;" width="70">Value Bal</td>
	              


	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
					{
						$date_sl=0;
						$date_wise_total=array();
						foreach ($date_wise_data as $buyer_id => $buydata) 
						{
							
						
								$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
								//print_r($intrefdata);
								$rtotQty=0;
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									<?
										if($date_sl==0)
										{
											?>
												<td width="65" rowspan="<?=$date_span[$ACC_SHIP_DATE];?>"><?=date('d-M-Y',strtotime($ACC_SHIP_DATE));?></td>
											<?
										}
									?>
									
									<td width="80"   style="word-break:break-all"><?=$buyerArr[$buyer_id]; ?></td>
									<?  
											$dev=1;
						                   	if($buydata[903]>0)
						                   	{
						                   		$dev=$buydata[903];
						                   	}
						                    $avg_smv=($buydata[904])/$dev;
						                    $avg_fob=($buydata[905])/$dev;
						                    $summary_total['order_qty']+=$buydata[903];
						                    $summary_total['minute']+=$buydata[904];
						                    $summary_total['amount']+=$buydata[905];
						                    $summary_total['avg_smv']+=$avg_smv;
						                    $summary_total['avg_fob']+=$avg_fob;
						                    $summary_total['no_of_acc']+=$buydata['no_of_acc'];
						                    $summary_total['carton_qty']+=$buydata['carton_qty'];
						                    $summary_total['ok_carton']+=$buydata['ok_carton'];
						                    $bal_carton = $buydata['carton_qty'] - $buydata['ok_carton'];
						                    $summary_total['bal_carton']+=$bal_carton;
						                    $summary_total['cbm']+=$buydata['cbm'];

						                    $ok_cbm=fn_number_format(fn_number_format($buydata['cbm'],2,".","")/fn_number_format($buydata['carton_qty'],2,".",""),2,".","") * (fn_number_format($buydata['ok_carton'],2,".","")) ; 
							                $balance_cbm=($buydata['cbm']-$ok_cbm);
							                $fin_balance=($buydata[903]-$buydata['fin']);

							                $summary_total['ok_cbm']+=$ok_cbm;
							                $summary_total['balance_cbm']+= $balance_cbm;
							                $summary_total['fin_balance']+= $fin_balance; 
							                $summary_total['fin']+=$buydata['fin']; 


						                    $date_wise_total['order_qty']+=$buydata[903];
						                    $date_wise_total['minute']+=$buydata[904];
						                    $date_wise_total['amount']+=$buydata[905];
						                    $date_wise_total['avg_smv']+=$avg_smv;
						                    $date_wise_total['avg_fob']+=$avg_fob;
						                    $date_wise_total['no_of_acc']+=$buydata['no_of_acc'];
						                    $date_wise_total['carton_qty']+=$buydata['carton_qty'];
						                    $date_wise_total['ok_carton']+=$buydata['ok_carton'];
						                    $date_wise_total['bal_carton']+=$bal_carton;
						                    $date_wise_total['cbm']+=$buydata['cbm'];
						                    $date_wise_total['ok_cbm']+=$ok_cbm;
							                $date_wise_total['balance_cbm']+= $balance_cbm; 
							                $date_wise_total['fin_balance']+= $fin_balance; 
							                $date_wise_total['fin']+=$buydata['fin']; 
						                    
										
									?>
										<td width="60" title="<?=number_format($buydata['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$buydata['no_of_acc']; ?></td>
										<td width="80" title="<?=number_format($buydata[903]); ?>" align="right" style="word-break:break-all"><?=$buydata[903]; ?></td>
										
										<td width="80" title="<?=number_format($buydata[905]); ?>" align="right" style="word-break:break-all"><?=number_format($buydata[905]); ?></td>
										



										<td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['cbm'],2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($ok_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($balance_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['carton_qty']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['ok_carton']);?></td>
						               <td align="right" style="word-break: break-all;" width="60"><?=number_format($bal_carton);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['fin']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($fin_balance);?></td>
						                <td align="right"  style="word-break: break-all;" width="60"><?=number_format($buydata['pre_final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($buydata[903]-$buydata['pre_final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($buydata['final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($buydata[903]-$buydata['final']);?></td>

						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[903]-$buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70" title=""><?=number_format($buydata['value']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[905]-$buydata['value']);?></td>

								</tr>
								<?
								$summary_total['QNTY']+=$buydata['QNTY'];
						        //$summary_total['ex_balance']+=($buydata['QNTY']*$avg_fob);
						        $summary_total['ex_balance']+=($buydata['value']);

						        $summary_total['pre_final']+=$buydata['pre_final'];
						        $summary_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);

						        $summary_total['final']+=$buydata['final'];
						        $summary_total['final_balance']+=($buydata[903]-$buydata['final']);

						        $date_wise_total['QNTY']+=$buydata['QNTY'];
						        //$date_wise_total['ex_balance']+=($buydata['QNTY']*$avg_fob);
						        $date_wise_total['ex_balance']+=($buydata['value']);

						        $date_wise_total['pre_final']+=$buydata['pre_final'];
						        $date_wise_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);

						        $date_wise_total['final']+=$buydata['final'];
						        $date_wise_total['final_balance']+=($buydata[903]-$buydata['final']);

								$i++;
								$date_sl++;
						}

						?>
				                    <tr align="center" style="font-weight:bold; background-color:#CCC">
				                    	<td width="65"></td>
				                        <td width="80"  >Date Total</td>
				                        <td width="60" title="<?=number_format($date_wise_total['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$date_wise_total['no_of_acc']; ?></td>
				                        <td width="80" title="<?=number_format($date_wise_total['order_qty']); ?>" align="right" style="word-break:break-all"><?=$date_wise_total['order_qty']; ?></td>
				                       
				                        <td width="80" title="<?=number_format($date_wise_total['amount']); ?>" align="right" style="word-break:break-all"><?=number_format($date_wise_total['amount']); ?></td>
				                       
				                        <?
				                        $date_wise_ok_cbm = fn_number_format(($date_wise_total['cbm']/$date_wise_total['carton_qty'])*$date_wise_total['ok_carton'],2,".","");

                        				$date_wise_balance_cbm = $summary_total_ok_cbm - $date_wise_total['cbm'];
				                        ?>

				                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['cbm'],2);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_ok_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_balance_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['carton_qty']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['ok_carton']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['bal_carton']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['fin']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['fin_balance']);?></td>
						                <td align="right"  style="word-break: break-all;" width="60"><?=number_format($date_wise_total['pre_final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['pre_final_balance']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['final_balance']);?></td>

						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['QNTY']);?></td>
						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['order_qty']-$date_wise_total['QNTY']);?></td>
						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['ex_balance']);?></td>
						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['amount']-$date_wise_total['ex_balance']);?></td>
				                        
				                    </tr>	
						<?
						$i++;
					}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
                    <tr align="center" style="font-weight:bold; background-color:#CCC">
                    	<td width="65"></td>
                        <td width="80"  >Total</td>
                        <td width="60" title="<?=number_format($summary_total['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$summary_total['no_of_acc']; ?></td>
                        <td width="80" title="<?=number_format($summary_total['order_qty']); ?>" align="right" style="word-break:break-all"><?=$summary_total['order_qty']; ?></td>
                       
                        <td width="80" title="<?=number_format($summary_total['amount']); ?>" align="right" style="word-break:break-all"><?=number_format($summary_total['amount']); ?></td>
                       
                        <?

							$summary_total_ok_cbm = fn_number_format(($summary_total['cbm']/$summary_total['carton_qty'])*$summary_total['ok_carton'],2,".","");

                        	$summary_total_balance_cbm = $summary_total_ok_cbm - $summary_total['cbm'];
                        ?>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['cbm'],2);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_ok_cbm,2);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_balance_cbm,2);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['carton_qty']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['ok_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['bal_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin_balance']);?></td>
		                <td align="right"  style="word-break: break-all;" width="60"><?=number_format($summary_total['pre_final']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['pre_final_balance']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['final']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['final_balance']);?></td>

		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['order_qty']-$summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['ex_balance']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['amount']-$summary_total['ex_balance']);?></td>
                        
                    </tr>
                </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

if($action=="shipment_summery_by_date")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$clientArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $startDate=$monthindex;
    $endDate=$taskid;
    $start_d=explode("-", $monthindex);
    $start_e=explode("-", $taskid);
    $order_summary_name='';
    if($start_d[2]==$start_e[2])
    {
    	$order_summary_name=$start_d[1]." ".$start_d[2];
    }
    else
    {
    	$order_summary_name=min($start_d[2],$start_e[2])."-".max($start_d[2],$start_e[2]);
    }
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	
	$actualPoSql="SELECT a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,d.unit_price as UNIT_PRICE,c.acc_po_no AS ACC_PO_NO,c.id as ACC_PO_ID,d.country_id AS COUNTRY_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;
	
	$actualPoSqlRes=sql_select($actualPoSql); $actualPoArr=array();
	$monthDateArr=array();
	$uniqe_acc_po=array();
	$acc_po_id_arr=array();
	$acc_po_buyer_arr=array();
	$acc_county_no_arr = array();
	$acc_po_ship_date_arr = array();
	foreach($actualPoSqlRes as $arow)
	{
		$acc_po_id_arr[$arow['ACC_PO_ID']] = $arow['ACC_PO_ID'];
		$acc_po_buyer_arr[$arow['ACC_PO_ID']]=$arow['BUYER_NAME'];
		if(empty($acc_county_no_arr[$arow['ACC_PO_ID']])) $acc_county_no_arr[$arow['ACC_PO_ID']]=$arow['COUNTRY_ID'];
		$acc_po_ship_date_arr[$arow['ACC_PO_ID']] = $arow['ACC_SHIP_DATE'];
	}
	$user_id = $_SESSION['logic_erp']['user_id'];
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"a.id");

	$sql_carton=sql_select("SELECT a.id,b.carton_qty,b.cbm,a.acc_ship_date FROM wo_po_acc_po_info a,wo_po_act_pack_finish_info b  where a.id=b.act_po_id and a.is_deleted=0 and b.is_deleted=0 $acc_po_cond");

	$actual_po_carton_date_arr = array();
	foreach ($sql_carton as $row) {
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['carton_qty']+=$row[csf('carton_qty')];
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['cbm']+=$row[csf('cbm')];
	}
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"b.actual_po_id");
	$sql_pre_final=sql_select("SELECT b.actual_po_id,a.inspected_by, b.current_inspection_qnty, a.inspection_level,b.actual_po_ship_date,b.country_id FROM pro_buyer_inspection a, pro_buyer_inspection_dtls b WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 and b.inspection_status=1 $acc_po_cond");

	$actual_po_pre_final_arr = array();
	foreach ($sql_pre_final as $row)
	{
		$acc_ship_date = $acc_po_ship_date_arr[$row[csf('actual_po_id')]];
		if($row[csf('inspection_level')] == 1 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$acc_ship_date][$row[csf('country_id')]]['pre_final']+=$row[csf('current_inspection_qnty')];
		}
		else if($row[csf('inspection_level')] == 2 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$acc_ship_date][$row[csf('country_id')]]['final']+=$row[csf('current_inspection_qnty')];
		}
	}

	$duplicate_check = array();
	$acc_avg_rate  = array();
	foreach($actualPoSqlRes as $arow)
	{
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][903]+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][904]+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][905]+=$shpmentVal;
		//$acc_po_and_ship_date = $arow["ACC_PO_ID"] ."*".$arow['ACC_SHIP_DATE'];
		$acc_avg_rate[$arow["ACC_PO_ID"]."*".$arow['ACC_SHIP_DATE']] = fn_number_format($shpmentVal/$arow["ACC_PO_QTY"],3,".","");
		$acc_po_and_ship_date = $arow["ACC_PO_ID"] . "*".$arow['ACC_SHIP_DATE']."*".$arow["COUNTRY_ID"];
		if(empty($duplicate_check[$acc_po_and_ship_date]))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['carton_qty']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['carton_qty'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['cbm']+=$actual_po_carton_date_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['cbm'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['pre_final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['pre_final'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['final']+=$actual_po_pre_final_arr[$arow["ACC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['final'];
			$duplicate_check[$acc_po_and_ship_date] = $acc_po_and_ship_date;
		}
		
		$key=$arow['PO_BREAK_DOWN_ID'].'***'.$arow['ACC_PO_NO'];
		if(!in_array($key, $uniqe_acc_po))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]]['no_of_acc']++;
			array_push($uniqe_acc_po, $key);
		}
	}
	
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	$sql_fin ="SELECT a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_garments_prod_actual_po_details c,
	       pro_garments_production_mst d
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and c.mst_id = d.id
	       and c.production_type = 8
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       and d.is_deleted = 0
	       $acc_po_cond";
	
	// echo "<pre>";
	// echo $sql_fin;
	// echo "</pre>";
	$uniqe_cartoon = array(); 
	$res_fin = sql_select($sql_fin);
	foreach ($res_fin as $row) 
	{
		$index = $row[csf('actual_po_id')] ."*". $row[csf('acc_ship_date')] ."*". $row[csf('country_id')] ."*". $row[csf('id')];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['fin']+=$row[csf('prod_qty')];
		if(empty($uniqe_cartoon[$index]))
		{
			$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['ok_carton']+=$row[csf('carton_qty')];
			$uniqe_cartoon[$index] = $index;
		}
	}
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	$sql_delivery ="SELECT a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id,b.unit_price
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_ex_factory_actual_po_details c
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0 
	       $acc_po_cond";
	//echo $sql_delivery;
	$res_delivery = sql_select($sql_delivery);      
	foreach ($res_delivery as $row) 
	{
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['QNTY']+=$row[csf('ex_fact_qty')];
		$avg_rate = $row[csf('unit_price')];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]]['value']+=($row[csf('ex_fact_qty')]*$avg_rate);
	}

	unset($actualPoSqlRes);
	

	$date_span=array();
	foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
	{
		$date_sl=0;
		foreach ($date_wise_data as $buyer_id => $buyer_data) 
		{
			$date_sl++;
		}
		$date_span[$ACC_SHIP_DATE]=$date_sl;
	}
	
	unset($actualPoSqlRes);
	$tblwidth=400+16*60;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="23" align="center">Shipment Summery by date: <?=$order_summary_name; ?> <?php 
	                 if(!empty($client_id))
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: <?=$clientArr[$client_id];?></span>
	                 	<?
	                 }
	                 else
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: All</span>
	                 	<?
	                 }
	                 	?> </td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="65">Ship Date</td>
	            	<td width="80" >Buyer</td>
	                <td width="60">No of PO</td>
	                <td width="80">Order Qty</td>
	               
	                <td width="80">Amount</td>
	               


	               <td style="word-break: break-all;" width="60">Req CBM</td>
	                <td style="word-break: break-all;" width="60">Ok CBM</td>
	                <td style="word-break: break-all;" width="60">Bal CBM</td>
	                <td style="word-break: break-all;" width="60">Req Carton</td>
	                <td style="word-break: break-all;" width="60">Ok Carton</td>
	                <td style="word-break: break-all;" width="60">Bal Carton</td>
	                <td style="word-break: break-all;" width="60">Finish Qty</td>
	                <td style="word-break: break-all;" width="60">Finish Bal</td>
	                <td style="word-break: break-all;" width="60">Pre final</td>
	                <td style="word-break: break-all;" width="60">Prefinal<br>Bal</td>
	                <td style="word-break: break-all;" width="60">Final</td>
	                <td style="word-break: break-all;" width="60">Final Bal</td>

	                <td style="word-break: break-all;" width="70">Exfactory</td>
	                <td style="word-break: break-all;" width="70">Exfactory Bal</td>
	                <td style="word-break: break-all;" width="70">Value</td>
	                <td style="word-break: break-all;" width="70">Value Bal</td>
	              


	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
					{
						$date_sl=0;
						$date_wise_total=array();
						foreach ($date_wise_data as $buyer_id => $buydata) 
						{
							
						
								$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
								//print_r($intrefdata);
								$rtotQty=0;
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									<?
										if($date_sl==0)
										{
											?>
												<td width="65" rowspan="<?=$date_span[$ACC_SHIP_DATE];?>"><?=date('d-M-Y',strtotime($ACC_SHIP_DATE));?></td>
											<?
										}
									?>
									
									<td width="80"   style="word-break:break-all"><?=$buyerArr[$buyer_id]; ?></td>
									<?  
											$dev=1;
						                   	if($buydata[903]>0)
						                   	{
						                   		$dev=$buydata[903];
						                   	}
						                    $avg_smv=($buydata[904])/$dev;
						                    $avg_fob=($buydata[905])/$dev;
						                    $summary_total['order_qty']+=$buydata[903];
						                    $summary_total['minute']+=$buydata[904];
						                    $summary_total['amount']+=$buydata[905];
						                    $summary_total['avg_smv']+=$avg_smv;
						                    $summary_total['avg_fob']+=$avg_fob;
						                    $summary_total['no_of_acc']+=$buydata['no_of_acc'];
						                    $summary_total['carton_qty']+=$buydata['carton_qty'];
						                    $summary_total['ok_carton']+=$buydata['ok_carton'];
						                    $bal_carton = $buydata['carton_qty'] - $buydata['ok_carton'];
						                    $summary_total['bal_carton']+=$bal_carton;
						                    $summary_total['cbm']+=$buydata['cbm'];

						                    $ok_cbm=fn_number_format(fn_number_format($buydata['cbm'],8,".","")/fn_number_format($buydata['carton_qty'],8,".",""),8,".","") * (fn_number_format($buydata['ok_carton'],8,".","")) ; 
							                $balance_cbm=($buydata['cbm']-$ok_cbm);
							                $fin_balance=($buydata[903]-$buydata['fin']);

							                $summary_total['ok_cbm']+=$ok_cbm;
							                $summary_total['balance_cbm']+= $balance_cbm;
							                $summary_total['fin_balance']+= $fin_balance; 
							                $summary_total['fin']+=$buydata['fin']; 


						                    $date_wise_total['order_qty']+=$buydata[903];
						                    $date_wise_total['minute']+=$buydata[904];
						                    $date_wise_total['amount']+=$buydata[905];
						                    $date_wise_total['avg_smv']+=$avg_smv;
						                    $date_wise_total['avg_fob']+=$avg_fob;
						                    $date_wise_total['no_of_acc']+=$buydata['no_of_acc'];
						                    $date_wise_total['carton_qty']+=$buydata['carton_qty'];
						                    $date_wise_total['ok_carton']+=$buydata['ok_carton'];
						                    $date_wise_total['bal_carton']+=$bal_carton;
						                    $date_wise_total['cbm']+=$buydata['cbm'];
						                    $date_wise_total['ok_cbm']+=$ok_cbm;
							                $date_wise_total['balance_cbm']+= $balance_cbm; 
							                $date_wise_total['fin_balance']+= $fin_balance; 
							                $date_wise_total['fin']+=$buydata['fin']; 
						                    
										
									?>
										<td width="60" title="<?=number_format($buydata['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$buydata['no_of_acc']; ?></td>
										<td width="80" title="<?=number_format($buydata[903]); ?>" align="right" style="word-break:break-all"><?=$buydata[903]; ?></td>
										
										<td width="80" title="<?=number_format($buydata[905]); ?>" align="right" style="word-break:break-all"><?=number_format($buydata[905]); ?></td>
										



										<td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['cbm'],2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($ok_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($balance_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['carton_qty']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['ok_carton']);?></td>
						               <td align="right" style="word-break: break-all;" width="60"><?=number_format($bal_carton);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($buydata['fin']);?></td>
						                <td align="right" style="word-break: break-all;" width="60"><?=number_format($fin_balance);?></td>
						                <td align="right"  style="word-break: break-all;" width="60"><?=number_format($buydata['pre_final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($buydata[903]-$buydata['pre_final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($buydata['final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($buydata[903]-$buydata['final']);?></td>

						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[903]-$buydata['QNTY']);?></td>
						                <td align="right" style="word-break: break-all;" width="70" title=""><?=number_format($buydata['value']);?></td>
						                <td align="right" style="word-break: break-all;" width="70"><?=number_format($buydata[905]-$buydata['value']);?></td>

								</tr>
								<?
								$summary_total['QNTY']+=$buydata['QNTY'];
						        //$summary_total['ex_balance']+=($buydata['QNTY']*$avg_fob);
						        $summary_total['ex_balance']+=($buydata['value']);

						        $summary_total['pre_final']+=$buydata['pre_final'];
						        $summary_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);

						        $summary_total['final']+=$buydata['final'];
						        $summary_total['final_balance']+=($buydata[903]-$buydata['final']);

						        $date_wise_total['QNTY']+=$buydata['QNTY'];
						        //$date_wise_total['ex_balance']+=($buydata['QNTY']*$avg_fob);
						        $date_wise_total['ex_balance']+=($buydata['value']);

						        $date_wise_total['pre_final']+=$buydata['pre_final'];
						        $date_wise_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);

						        $date_wise_total['final']+=$buydata['final'];
						        $date_wise_total['final_balance']+=($buydata[903]-$buydata['final']);

								$i++;
								$date_sl++;
						}

						?>
				                    <tr align="center" style="font-weight:bold; background-color:#CCC">
				                    	<td width="65"></td>
				                        <td width="80"  >Date Total</td>
				                        <td width="60" title="<?=number_format($date_wise_total['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$date_wise_total['no_of_acc']; ?></td>
				                        <td width="80" title="<?=number_format($date_wise_total['order_qty']); ?>" align="right" style="word-break:break-all"><?=$date_wise_total['order_qty']; ?></td>
				                       
				                        <td width="80" title="<?=number_format($date_wise_total['amount']); ?>" align="right" style="word-break:break-all"><?=number_format($date_wise_total['amount']); ?></td>
				                       
				                        <?
				                        $date_wise_ok_cbm = fn_number_format(($date_wise_total['cbm']/$date_wise_total['carton_qty'])*$date_wise_total['ok_carton'],8,".","");

                        				$date_wise_balance_cbm = $date_wise_total['cbm'] - $summary_total_ok_cbm ;
				                        ?>

				                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['cbm'],2);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_ok_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_balance_cbm,2);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['carton_qty']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['ok_carton']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['bal_carton']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['fin']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['fin_balance']);?></td>
						                <td align="right"  style="word-break: break-all;" width="60"><?=number_format($date_wise_total['pre_final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['pre_final_balance']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['final']);?></td>
						                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($date_wise_total['final_balance']);?></td>

						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['QNTY']);?></td>
						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['order_qty']-$date_wise_total['QNTY']);?></td>
						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['ex_balance']);?></td>
						               <td align="right" style="word-break: break-all;" width="70"><?=number_format($date_wise_total['amount']-$date_wise_total['ex_balance']);?></td>
				                        
				                    </tr>	
						<?
						$i++;
					}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
                    <tr align="center" style="font-weight:bold; background-color:#CCC">
                    	<td width="65"></td>
                        <td width="80"  >Total</td>
                        <td width="60" title="<?=number_format($summary_total['no_of_acc']); ?>" align="right" style="word-break:break-all"><?=$summary_total['no_of_acc']; ?></td>
                        <td width="80" title="<?=number_format($summary_total['order_qty']); ?>" align="right" style="word-break:break-all"><?=$summary_total['order_qty']; ?></td>
                       
                        <td width="80" title="<?=number_format($summary_total['amount']); ?>" align="right" style="word-break:break-all"><?=number_format($summary_total['amount']); ?></td>
                       
                        <?

							$summary_total_ok_cbm = fn_number_format(($summary_total['cbm']/$summary_total['carton_qty'])*$summary_total['ok_carton'],8,".","");

                        	$summary_total_balance_cbm = $summary_total['cbm'] - $summary_total_ok_cbm ;
                        ?>
                        <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['cbm'],2);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_ok_cbm,2);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total_balance_cbm,2);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['carton_qty']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['ok_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['bal_carton']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['fin_balance']);?></td>
		                <td align="right"  style="word-break: break-all;" width="60"><?=number_format($summary_total['pre_final']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['pre_final_balance']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['final']);?></td>
		                <td align="right" style="word-break: break-all;"  width="60"><?=number_format($summary_total['final_balance']);?></td>

		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['order_qty']-$summary_total['QNTY']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['ex_balance']);?></td>
		               <td align="right" style="word-break: break-all;" width="70"><?=number_format($summary_total['amount']-$summary_total['ex_balance']);?></td>
                        
                    </tr>
                </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}
if($action=="shipment_details_by_date_backup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$clientArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$seasonArr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$countryArr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $startDate=$monthindex;
    $endDate=$taskid;
    $start_d=explode("-", $monthindex);
    $start_e=explode("-", $taskid);
    $order_summary_name='';
    if($start_d[2]==$start_e[2])
    {
    	$order_summary_name=$start_d[1]." ".$start_d[2];
    }
    else
    {
    	$order_summary_name=min($start_d[2],$start_e[2])."-".max($start_d[2],$start_e[2]);
    }
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	
	$actualPoSql="SELECT a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,(b.unit_price/a.total_set_qnty) as UNIT_PRICE,c.acc_po_no AS ACC_PO_NO,a.season_buyer_wise as SEASON,a.style_ref_no AS STYLE_REF,b.grouping AS INT_REF,d.country_id AS COUNTRY_ID,c.id as AC_PO_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;
	
	$actualPoSqlRes=sql_select($actualPoSql); $actualPoArr=array();
	$monthDateArr=array();
	$uniqe_acc_po=array();
	$acc_po_id_arr=array();
	$acc_po_buyer_arr=array();
	$acc_county_no_arr=array();
	foreach($actualPoSqlRes as $arow)
	{
		//array_push($po_id_arr, $arow['PO_BREAK_DOWN_ID']);
		$acc_po_buyer_arr[$arow['AC_PO_ID']]=$arow['BUYER_NAME'];
		$acc_po_id_arr[$arow['AC_PO_ID']] = $arow['AC_PO_ID'];
		if(empty($acc_county_no_arr[$arow['AC_PO_ID']])) $acc_county_no_arr[$arow['AC_PO_ID']]=$arow['COUNTRY_ID'];
	}
	$user_id = $_SESSION['logic_erp']['user_id'];
	$start_time = time();
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from = 24 and ENTRY_FORM=888");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 888, 24, $acc_po_id_arr, $empty_arr);//PO ID
	$end_time = time();
	$diff = $end_time-$start_time;
	//echo $diff ."=". $end_time ."-". $start_time;
	$sql_carton=sql_select("SELECT a.id,b.carton_qty,b.cbm,a.acc_ship_date FROM wo_po_acc_po_info a,wo_po_act_pack_finish_info b,gbl_temp_engine g where a.id=b.act_po_id and b.is_deleted=0 and a.is_deleted=0 and g.ref_val = a.id and  g.user_id = $user_id and g.ref_from = 24 and g.entry_form=888");

	$actual_po_carton_date_arr = array();
	foreach ($sql_carton as $row) {
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['carton_qty']+=$row[csf('carton_qty')];
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['cbm']+=$row[csf('cbm')];
	}
	$sql_pre_final=sql_select("SELECT b.actual_po_id,a.inspected_by, b.current_inspection_qnty, a.inspection_level,b.actual_po_ship_date,b.country_id FROM pro_buyer_inspection a, pro_buyer_inspection_dtls b,gbl_temp_engine g WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 and b.inspection_status=1 and g.ref_val = b.actual_po_id and  g.user_id = $user_id and g.ref_from =24 and g.entry_form=888");


	$actual_po_pre_final_arr = array();
	foreach ($sql_pre_final as $row)
	{
		if($row[csf('inspection_level')] == 1 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$row[csf('actual_po_ship_date')]][$row[csf('country_id')]]['pre_final']+=$row[csf('current_inspection_qnty')];
		}
		else if($row[csf('inspection_level')] == 2 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$row[csf('actual_po_ship_date')]][$row[csf('country_id')]]['final']+=$row[csf('current_inspection_qnty')];
		}
	}

	$duplicate_check = array();

	foreach($actualPoSqlRes as $arow)
	{
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]][903]+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]][904]+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]][905]+=$shpmentVal;
		$acc_po_ship_country = $arow["AC_PO_ID"] . "*".$arow['ACC_SHIP_DATE']."*".$arow["COUNTRY_ID"];

		if(empty($duplicate_check[$acc_po_ship_country]))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['carton_qty']+=$actual_po_carton_date_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['carton_qty'];

			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['cbm']+=$actual_po_carton_date_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['cbm'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['pre_final']+=$actual_po_pre_final_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['pre_final'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['final']+=$actual_po_pre_final_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['final'];
			$duplicate_check[$acc_po_ship_country] = $acc_po_ship_country;
		}
		

		$key=$arow['PO_BREAK_DOWN_ID'].'***'.$arow['ACC_PO_NO'];
		if(!in_array($key, $uniqe_acc_po))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['no_of_acc']=$monthDataArr[$arow["BUYER_NAME"]]['no_of_acc']+1;
			array_push($uniqe_acc_po, $key);
		}
	}

	
	
    $sql_fin ="SELECT a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id as pc_id
      from wo_po_acc_po_info a,
           wo_po_acc_po_info_dtls b,
           pro_garments_prod_actual_po_details c,
           pro_garments_production_mst d,
           wo_po_details_master e,
           wo_po_break_down  f,
           gbl_temp_engine g
     where     a.id = b.mst_id
           and a.id = c.actual_po_id
           and b.id = c.actual_po_dtls_id
           and c.mst_id = d.id
           and a.job_id = e.id
           and b.po_break_down_id = f.id
           and e.id = f.job_id
           and c.production_type = 8
           and a.is_deleted = 0 
           and b.is_deleted = 0
           and c.is_deleted = 0 
           and d.is_deleted = 0
           and e.is_deleted = 0 
           and f.is_deleted = 0
           and g.ref_val = a.id 
           and g.user_id = $user_id 
           and g.ref_from = 24
           and g.entry_form = 888
           group by a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id";
	
    $res_fin = sql_select($sql_fin);
	$uniqe_cartoon = array(); 
	foreach ($res_fin as $row) 
	{
		$index = $row[csf('actual_po_id')] ."*". $row[csf('acc_ship_date')] ."*". $row[csf('country_id')] ."*". $row[csf('id')];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['fin']+=$row[csf('prod_qty')];
		if(empty($uniqe_cartoon[$index]))
		{
			$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['ok_carton']+=$row[csf('carton_qty')];
			$uniqe_cartoon[$index] = $index;
		}
	}

	$sql_delivery ="SELECT a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_ex_factory_actual_po_details c,
	       wo_po_details_master e,
           wo_po_break_down  f,
	       gbl_temp_engine g
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and a.job_id = e.id
           and b.po_break_down_id = f.id
           and e.id = f.job_id
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0
	       and e.is_deleted = 0 
           and f.is_deleted = 0 
	       and g.ref_val = a.id 
	       and g.user_id = $user_id 
	       and g.ref_from = 24 
	       and g.entry_form = 888 
	       group by a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id";
	//echo $sql_delivery;
	$res_delivery = sql_select($sql_delivery);      
	foreach ($res_delivery as $row) 
	{
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['QNTY']+=$row[csf('ex_fact_qty')];
	}

	$date_span=array();
	$buyer_span=array();
	$season_span=array();
	$intref_span=array();
	$style_span=array();
	$po_span=array();
	foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
	{
		$date_sl=0;
		foreach ($date_wise_data as $buyer_id => $buyer_data) 
		{
			$buyer_sl=0;
			foreach ($buyer_data as $season => $season_buyer_wise) 
			{
				$season_sl=0;
				foreach ($season_buyer_wise as $int_ref => $int_ref_wise) 
				{
					$intref_sl=0;
					foreach ($int_ref_wise as $style_ref_no => $style_ref_wise) 
					{
						$style_sl=0;
						foreach ($style_ref_wise as $acc_po_no => $acc_po_no_wise) 
						{
							$po_sl=0;
							foreach ($acc_po_no_wise as $country_id => $country_wise) 
							{
								$buyer_sl++;
								$date_sl++;
								$season_sl++;
								$intref_sl++;
								$style_sl++;
								$po_sl++;
							}
							$po_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no][$acc_po_no]=$po_sl;
						}
						$style_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no]=$style_sl;

					}
					$intref_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref]=$intref_sl;
				}
				$season_span[$ACC_SHIP_DATE][$buyer_id][$season]=$season_sl;
			}
			$buyer_span[$ACC_SHIP_DATE][$buyer_id]=$buyer_sl;
			
		}
		$date_span[$ACC_SHIP_DATE]=$date_sl;
	}
	// $con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from = 24 and ENTRY_FORM=888");
	oci_commit($con);
	disconnect($con);
	$tblwidth=65+80+12*60+8*65;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="23" align="center" >Shipment Details by date: <?=$order_summary_name; ?>
	                 <?php 
	                 if(!empty($client_id))
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: <?=$clientArr[$client_id];?></span>
	                 	<?
	                 }
	                 else
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: All</span>
	                 	<?
	                 }
	                 	?>
	                 

	                  </td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td style="word-break: break-all;" width="65"><p>Ship Date</p></td>
	            	<td style="word-break: break-all;" width="80" ><p>Buyer</p></td>

	                <td style="word-break: break-all;" width="65"><p>Season</p></td>
	                <td style="word-break: break-all;" width="65"><p>Ref no </p></td>
	                <td style="word-break: break-all;" width="65"><p>Style Name</p> </td>
	                <td style="word-break: break-all;" width="65"><p>PO No</p></td>
	                <td style="word-break: break-all;" width="65"><p>Country</p></td>
	                <td style="word-break: break-all;" width="65"><p>PO Qty</p></td>
	               
	               

	                <td style="word-break: break-all;" width="60"><p>Req CBM</p></td>
	                <td style="word-break: break-all;" width="60"><p>Ok CBM</p></td>
	                <td style="word-break: break-all;" width="60"><p>Bal CBM</p></td>
	                <td style="word-break: break-all;" width="60"><p>Req Carton</p></td>
	                <td style="word-break: break-all;" width="60"><p>Ok Carton</p></td>
	                <td style="word-break: break-all;" width="60"><p>Bal Carton</p></td>
	                <td style="word-break: break-all;" width="60"><p>Finish Qty</p></td>
	                <td style="word-break: break-all;" width="60"><p>Finish Bal</p></td>
	                <td style="word-break: break-all;" width="60"><p>Pre final</p></td>
	                <td style="word-break: break-all;" width="60"><p>Prefinal<br>Bal</p></td>
	                <td style="word-break: break-all;" width="60"><p>Final</p></td>
	                <td style="word-break: break-all;" width="60"><p>Final Bal</p></td>

	                <td style="word-break: break-all;" width="65"><p>Exfactory</p></td>
	                <td style="word-break: break-all;" width="65"><p>Exfactory Bal</p></td>
	                
	              


	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
					{
						$date_sl=0;
						$date_wise_total=array();
						foreach ($date_wise_data as $buyer_id => $buyer_data) 
						{
							$buyer_sl=0;
							foreach ($buyer_data as $season => $season_buyer_wise) 
							{
								$season_sl=0;
								foreach ($season_buyer_wise as $int_ref => $int_ref_wise) 
								{
									$intref_sl=0;
									foreach ($int_ref_wise as $style_ref_no => $style_ref_wise) 
									{
										$style_sl=0;
										foreach ($style_ref_wise as $acc_po_no => $acc_po_no_wise) 
										{
											$po_sl=0;
											foreach ($acc_po_no_wise as $country_id => $buydata) 
											{
						
														$buy=1; $btotal=0; $dayWiseBtotalArr=array();
												
														//print_r($intrefdata);
														$rtotQty=0;
														$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
														
														?>
														<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
															<?
																if($date_sl==0)
																{
																	?>
																		<td  style="vertical-align: middle;" width="65" rowspan="<?=$date_span[$ACC_SHIP_DATE];?>"><p><?=date('d-M-Y',strtotime($ACC_SHIP_DATE));?></p></td>
																	<?
																}
															
															if($buyer_sl==0)
															{
																?>
																<td width="80" style="vertical-align: middle;"  rowspan="<?=$buyer_span[$ACC_SHIP_DATE][$buyer_id];?>"   style="word-break:break-all"><p><?=$buyerArr[$buyer_id]; ?></p></td>

															<?  
															}
																	$dev=1;
												                   	if($buydata[903]>0)
												                   	{
												                   		$dev=$buydata[903];
												                   	}
												                    $avg_smv=($buydata[904])/$dev;
												                    $avg_fob=($buydata[905])/$dev;
												                    $summary_total['order_qty']+=$buydata[903];
												                    $summary_total['minute']+=$buydata[904];
												                    $summary_total['amount']+=$buydata[905];
												                    $summary_total['avg_smv']+=$avg_smv;
												                    $summary_total['avg_fob']+=$avg_fob;
												                    $summary_total['no_of_acc']+=$buydata['no_of_acc'];

						                    						$bal_carton = $buydata['carton_qty'] - $buydata['ok_carton'];
						                    						$ok_cbm=fn_number_format(fn_number_format($buydata['cbm'],2,".","")/fn_number_format($buydata['carton_qty'],2,".",""),2,".","") * (fn_number_format($buydata['ok_carton'],2,".","")) ; 

						                							$balance_cbm=($buydata['cbm']-$ok_cbm);
						                							$fin_balance=($buydata[903]-$buydata['fin']);

						                							$summary_total['ok_cbm']+=$ok_cbm;
						                							$summary_total['ok_carton']+=$buydata['ok_carton'];
						                    						$summary_total['bal_carton']+=$bal_carton;
						                    						$summary_total['fin']+=$buydata['fin'];
						                							$summary_total['balance_cbm']+= $balance_cbm; 
						                							$summary_total['fin_balance']+= $fin_balance; 


						                							$date_wise_total['ok_cbm']+=$ok_cbm;
						                							$date_wise_total['ok_carton']+=$buydata['ok_carton'];
						                    						$date_wise_total['bal_carton']+=$bal_carton;
						                    						$date_wise_total['fin']+=$buydata['fin'];
						                							$date_wise_total['balance_cbm']+= $balance_cbm; 
						                							$date_wise_total['fin_balance']+= $fin_balance;


												                    $date_wise_total['order_qty']+=$buydata[903];
												                    $date_wise_total['minute']+=$buydata[904];
												                    $date_wise_total['amount']+=$buydata[905];
												                    $date_wise_total['avg_smv']+=$avg_smv;
												                    $date_wise_total['avg_fob']+=$avg_fob;
												                    $date_wise_total['no_of_acc']+=$buydata['no_of_acc'];
												                    
																
															
																if($season_sl==0)
																{
																	?>
																	<td style="vertical-align: middle;"  rowspan="<?=$season_span[$ACC_SHIP_DATE][$buyer_id][$season];?>" style="word-break: break-all;" width="65"><p><?=$seasonArr[$season];?></p></td>
																	<?
																}
																if($intref_sl==0)
																{
																	?>

												                	<td style="vertical-align: middle;"  rowspan="<?=$intref_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref];?>" style="word-break: break-all;" width="65"><p><?=$int_ref;?></p> </td>
												                	<?
												                }
												                if($style_sl==0)
																{
																	?>
												                	<td style="vertical-align: middle;"  rowspan="<?=$style_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no];?>" style="word-break: break-all;" width="65"><p><?=$style_ref_no;?></p> </td>
												                	<?
												                }

												                if($po_sl==0)
																{
																	?>
													                <td style="vertical-align: middle;"  rowspan="<?=$po_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no][$acc_po_no];?>" style="word-break: break-all;" width="65"><p><?=$acc_po_no;?></p></td>
													                <?
													            }
													            ?>
												                <td style="word-break: break-all;" width="65"><p><?=$countryArr[$country_id];?></p></td>
												               
																<td width="65" title="<?=number_format($buydata[903]); ?>" align="right" style="word-break:break-all"><p><?=$buydata[903]; ?></p></td>
																
																



																<td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['cbm'],2);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($ok_cbm,2);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($balance_cbm,2);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['carton_qty']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['ok_carton']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($bal_carton);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['fin']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($fin_balance);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['pre_final']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata[903]-$buydata['pre_final']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['final']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata[903]-$buydata['final']);?></p></td>

												                <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($buydata['QNTY']);?></p></td>
												                <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($buydata[903]-$buydata['QNTY']);?></p></td>
												               

														</tr>
														<?
														$summary_total['QNTY']+=$buydata['QNTY'];
														$summary_total['carton_qty']+=$buydata['carton_qty'];
												        $summary_total['ex_balance']+=($buydata['QNTY']*$avg_fob);
														$summary_total['cbm']+=$buydata['cbm'];

														$summary_total['pre_final']+=$buydata['pre_final'];
														$summary_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);
														$summary_total['final']+=$buydata['final'];
														$summary_total['final_balance']+=($buydata[903]-$buydata['final']);

												        $date_wise_total['carton_qty']+=$buydata['carton_qty'];
												        $date_wise_total['cbm']+=$buydata['cbm'];

												        $date_wise_total['QNTY']+=$buydata['QNTY'];
												        $date_wise_total['ex_balance']+=($buydata['QNTY']*$avg_fob);

												        $date_wise_total['pre_final']+=$buydata['pre_final'];
														$date_wise_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);
														$date_wise_total['final']+=$buydata['final'];
														$date_wise_total['final_balance']+=($buydata[903]-$buydata['final']);

														$i++;
														$buyer_sl++;
														$date_sl++;
														$season_sl++;
														$intref_sl++;
														$style_sl++;
														$po_sl++;
											}
										}
									}
								}
							}
						}


						?>
				                    <tr align="center" style="font-weight:bold; background-color:#CCC">
				                    	<td width="65"></td>
				                        <td width="80"  ></td>
				                        <td style="word-break: break-all;" width="65"></td>
						                <td style="word-break: break-all;" width="65"> </td>
						                <td style="word-break: break-all;" width="65"> </td>
						                <td style="word-break: break-all;" width="65"></td>
						                <td style="word-break: break-all;" width="65"><p>Date Total</p></td>
				                        <td width="65" title="<?=number_format($date_wise_total['order_qty']); ?>" align="right" style="word-break:break-all"><p><?=$date_wise_total['order_qty']; ?></p></td>
				                       
				                       
				                        <?

				                        $date_wise_ok_cbm = fn_number_format(($date_wise_total['cbm']/$date_wise_total['carton_qty'])*$date_wise_total['ok_carton'],2,".","");

                        				$date_wise_balance_cbm = $date_wise_ok_cbm - $date_wise_total['cbm'];
				                        ?>

				                        <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['cbm'],2);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_ok_cbm,2);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_balance_cbm,2);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['carton_qty']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['ok_carton']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['bal_carton']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['fin']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['fin_balance']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['pre_final']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['pre_final_balance']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['final']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['final_balance']);?></p></td>

						               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($date_wise_total['QNTY']);?></p></td>
						               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($date_wise_total['order_qty']-$date_wise_total['QNTY']);?></p></td>
						               
				                        
				                    </tr>	
						<?
						$i++;
					}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
                    <tr align="center" style="font-weight:bold; background-color:#CCC">
                    	<td width="65"></td>
                        <td width="80"  ></td>
                        <td style="word-break: break-all;" width="65"></td>
		                <td style="word-break: break-all;" width="65"> </td>
		                <td style="word-break: break-all;" width="65"> </td>
		                <td style="word-break: break-all;" width="65"></td>
		                <td style="word-break: break-all;" width="65"></td>
                       <?
                       		$summary_total_ok_cbm = fn_number_format(($summary_total['cbm']/$summary_total['carton_qty'])*$summary_total['ok_carton'],2,".","");

                        	$summary_total_balance_cbm = $summary_total_ok_cbm - $summary_total['cbm'];
                       ?>
                        <td width="65" title="<?=number_format($summary_total['order_qty']); ?>" align="right" style="word-break:break-all"><p><?=$summary_total['order_qty']; ?></p></td>
                        <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['cbm'],2);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total_ok_cbm,2);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total_balance_cbm,2);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['carton_qty']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['ok_carton']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['bal_carton']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['fin']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['fin_balance']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['pre_final']);?></p></td>
						<td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['pre_final_balance']);?></p></td>
						<td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['final']);?></p></td>
						<td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['final_balance']);?></p></td>

		               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($summary_total['QNTY']);?></p></td>
		               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($summary_total['order_qty']-$summary_total['QNTY']);?></p></td>
		              
                        
                    </tr>
                </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

if($action=="shipment_details_by_date")
{
	extract($_REQUEST);
	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	echo load_html_head_contents("Popup Info", "../../../../", 1, 1,'','','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$clientArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$seasonArr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$countryArr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $startDate=$monthindex;
    $endDate=$taskid;
    $start_d=explode("-", $monthindex);
    $start_e=explode("-", $taskid);
    $order_summary_name='';
    if($start_d[2]==$start_e[2])
    {
    	$order_summary_name=$start_d[1]." ".$start_d[2];
    }
    else
    {
    	$order_summary_name=min($start_d[2],$start_e[2])."-".max($start_d[2],$start_e[2]);
    }
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	
	$actualPoSql="SELECT a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,d.unit_price as UNIT_PRICE,c.acc_po_no AS ACC_PO_NO,a.season_buyer_wise as SEASON,a.style_ref_no AS STYLE_REF,b.grouping AS INT_REF,d.country_id AS COUNTRY_ID,c.id as AC_PO_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;
	
	$actualPoSqlRes=sql_select($actualPoSql); $actualPoArr=array();
	$monthDateArr=array();
	$uniqe_acc_po=array();
	$acc_po_id_arr=array();
	$acc_po_buyer_arr=array();
	$acc_county_no_arr=array();
	$acc_po_ship_date_arr=array();
	foreach($actualPoSqlRes as $arow)
	{
		//array_push($po_id_arr, $arow['PO_BREAK_DOWN_ID']);
		$acc_po_buyer_arr[$arow['AC_PO_ID']]=$arow['BUYER_NAME'];
		$acc_po_id_arr[$arow['AC_PO_ID']] = $arow['AC_PO_ID'];
		if(empty($acc_county_no_arr[$arow['AC_PO_ID']])) $acc_county_no_arr[$arow['AC_PO_ID']]=$arow['COUNTRY_ID'];
		$acc_po_ship_date_arr[$arow['AC_PO_ID']] = $arow['ACC_SHIP_DATE'];
	}
	$user_id = $_SESSION['logic_erp']['user_id'];
	$start_time = time();
	
	$end_time = time();
	$diff = $end_time-$start_time;
	//echo $diff ."=". $end_time ."-". $start_time;
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	$sql_carton=sql_select("SELECT a.id,b.carton_qty,b.cbm,a.acc_ship_date FROM wo_po_acc_po_info a,wo_po_act_pack_finish_info b where a.id=b.act_po_id and b.is_deleted=0 and a.is_deleted=0 $acc_po_cond");

	$actual_po_carton_date_arr = array();
	foreach ($sql_carton as $row) {
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['carton_qty']+=$row[csf('carton_qty')];
		$actual_po_carton_date_arr[$row[csf('id')]][$row[csf('acc_ship_date')]][$acc_county_no_arr[$row[csf('id')]]]['cbm']+=$row[csf('cbm')];
	}
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"b.actual_po_id");
	$sql_pre_final=sql_select("SELECT b.actual_po_id,a.inspected_by, b.current_inspection_qnty, a.inspection_level,b.actual_po_ship_date,b.country_id FROM pro_buyer_inspection a, pro_buyer_inspection_dtls b WHERE a.id = b.mst_id AND a.status_active = 1 AND a.is_deleted = 0 and b.inspection_status=1 $acc_po_cond");


	$actual_po_pre_final_arr = array();
	foreach ($sql_pre_final as $row)
	{
		$acc_ship_date = $acc_po_ship_date_arr[$row[csf('actual_po_id')]];
		if($row[csf('inspection_level')] == 1 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$acc_ship_date][$row[csf('country_id')]]['pre_final']+=$row[csf('current_inspection_qnty')];
		}
		else if($row[csf('inspection_level')] == 2 )
		{
			$actual_po_pre_final_arr[$row[csf('actual_po_id')]][$acc_ship_date][$row[csf('country_id')]]['final']+=$row[csf('current_inspection_qnty')];
		}
	}

	$duplicate_check = array();

	foreach($actualPoSqlRes as $arow)
	{
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]][903]+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]][904]+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]][905]+=$shpmentVal;
		$acc_po_ship_country = $arow["AC_PO_ID"] . "*".$arow['ACC_SHIP_DATE']."*".$arow["COUNTRY_ID"];

		if(empty($duplicate_check[$acc_po_ship_country]))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['carton_qty']+=$actual_po_carton_date_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['carton_qty'];

			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['cbm']+=$actual_po_carton_date_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['cbm'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['pre_final']+=$actual_po_pre_final_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['pre_final'];
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['final']+=$actual_po_pre_final_arr[$arow["AC_PO_ID"]][$arow['ACC_SHIP_DATE']][$arow["COUNTRY_ID"]]['final'];
			$duplicate_check[$acc_po_ship_country] = $acc_po_ship_country;
		}
		

		$key=$arow['PO_BREAK_DOWN_ID'].'***'.$arow['ACC_PO_NO'];
		if(!in_array($key, $uniqe_acc_po))
		{
			$monthDataArr[$arow['ACC_SHIP_DATE']][$arow["BUYER_NAME"]][$arow["SEASON"]][$arow["INT_REF"]][$arow["STYLE_REF"]][$arow["ACC_PO_NO"]][$arow["COUNTRY_ID"]]['no_of_acc']=$monthDataArr[$arow["BUYER_NAME"]]['no_of_acc']+1;
			array_push($uniqe_acc_po, $key);
		}
	}

	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	
    $sql_fin ="SELECT a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id as pc_id
      from wo_po_acc_po_info a,
           wo_po_acc_po_info_dtls b,
           pro_garments_prod_actual_po_details c,
           pro_garments_production_mst d,
           wo_po_details_master e,
           wo_po_break_down  f
     where     a.id = b.mst_id
           and a.id = c.actual_po_id
           and b.id = c.actual_po_dtls_id
           and c.mst_id = d.id
           and a.job_id = e.id
           and b.po_break_down_id = f.id
           and e.id = f.job_id
           and c.production_type = 8
           and a.is_deleted = 0 
           and b.is_deleted = 0
           and c.is_deleted = 0 
           and d.is_deleted = 0
           and e.is_deleted = 0 
           and f.is_deleted = 0
           $acc_po_cond
           group by a.acc_po_no,a.acc_ship_date,c.prod_qty,b.country_id,c.reject_qty,c.actual_po_id,d.carton_qty,d.id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id";
	
    $res_fin = sql_select($sql_fin);
	$uniqe_cartoon = array(); 
	foreach ($res_fin as $row) 
	{
		$index = $row[csf('actual_po_id')] ."*". $row[csf('acc_ship_date')] ."*". $row[csf('country_id')] ."*". $row[csf('id')];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['fin']+=$row[csf('prod_qty')];
		if(empty($uniqe_cartoon[$index]))
		{
			$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['ok_carton']+=$row[csf('carton_qty')];
			$uniqe_cartoon[$index] = $index;
		}
	}
	$acc_po_cond = where_con_using_array($acc_po_id_arr,0,"a.id");
	$sql_delivery ="SELECT a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id,b.unit_price
	  from wo_po_acc_po_info a,
	       wo_po_acc_po_info_dtls b,
	       pro_ex_factory_actual_po_details c,
	       wo_po_details_master e,
           wo_po_break_down  f
	 where     a.id = b.mst_id
	       and a.id = c.actual_po_id
	       and b.id = c.actual_po_dtls_id
	       and a.job_id = e.id
           and b.po_break_down_id = f.id
           and e.id = f.job_id
	       and a.is_deleted = 0 
	       and b.is_deleted = 0
	       and c.is_deleted = 0
	       and e.is_deleted = 0 
           and f.is_deleted = 0 
	       $acc_po_cond 
	       group by a.acc_po_no,a.acc_ship_date,c.ex_fact_qty,b.country_id,c.actual_po_id,f.grouping,e.season_buyer_wise,e.style_ref_no,c.id,b.unit_price";
	//echo $sql_delivery;
	$res_delivery = sql_select($sql_delivery);      
	foreach ($res_delivery as $row) 
	{
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['QNTY']+=$row[csf('ex_fact_qty')];
		$monthDataArr[$row[csf('acc_ship_date')]][$acc_po_buyer_arr[$row[csf('actual_po_id')]]][$row[csf('season_buyer_wise')]][$row[csf('grouping')]][$row[csf('style_ref_no')]][$row[csf('acc_po_no')]][$row[csf('country_id')]]['value']+=($row[csf('ex_fact_qty')] * $row[csf('unit_price')]);
	}

	$date_span=array();
	$buyer_span=array();
	$season_span=array();
	$intref_span=array();
	$style_span=array();
	$po_span=array();
	foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
	{
		$date_sl=0;
		foreach ($date_wise_data as $buyer_id => $buyer_data) 
		{
			$buyer_sl=0;
			foreach ($buyer_data as $season => $season_buyer_wise) 
			{
				$season_sl=0;
				foreach ($season_buyer_wise as $int_ref => $int_ref_wise) 
				{
					$intref_sl=0;
					foreach ($int_ref_wise as $style_ref_no => $style_ref_wise) 
					{
						$style_sl=0;
						foreach ($style_ref_wise as $acc_po_no => $acc_po_no_wise) 
						{
							$po_sl=0;
							foreach ($acc_po_no_wise as $country_id => $country_wise) 
							{
								$buyer_sl++;
								$date_sl++;
								$season_sl++;
								$intref_sl++;
								$style_sl++;
								$po_sl++;
							}
							$po_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no][$acc_po_no]=$po_sl;
						}
						$style_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no]=$style_sl;

					}
					$intref_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref]=$intref_sl;
				}
				$season_span[$ACC_SHIP_DATE][$buyer_id][$season]=$season_sl;
			}
			$buyer_span[$ACC_SHIP_DATE][$buyer_id]=$buyer_sl;
			
		}
		$date_span[$ACC_SHIP_DATE]=$date_sl;
	}

	$tblwidth=65+80+12*60+10*65;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			$(".short_name").hide();
			$(".full_name").show();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$(".short_name").show();
			$(".full_name").hide();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="25" align="center" >Shipment Details by date: <?=$order_summary_name; ?>
	                 <?php 
	                 if(!empty($client_id))
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: <?=$clientArr[$client_id];?></span>
	                 	<?
	                 }
	                 else
	                 {
	                 	?>
	                 	; <span style="color:red;">Client: All</span>
	                 	<?
	                 }
	                 	?>
	                 

	                  </td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td style="word-break: break-all;" width="65"><p>Ship Date</p></td>
	            	<td style="word-break: break-all;" width="80" ><p>Buyer</p></td>

	                <td style="word-break: break-all;" width="65"><p>Season</p></td>
	                <td style="word-break: break-all;" width="65"><p>Ref no </p></td>
	                <td style="word-break: break-all;" width="65"><p>Style Name</p> </td>
	                <td style="word-break: break-all;" width="65"><p>PO No</p></td>
	                <td style="word-break: break-all;" width="65"><p>Country</p></td>
	                <td style="word-break: break-all;" width="65"><p>PO Qty</p></td>
	               
	               

	                <td style="word-break: break-all;" width="60"><p>Req CBM</p></td>
	                <td style="word-break: break-all;" width="60"><p>Ok CBM</p></td>
	                <td style="word-break: break-all;" width="60"><p>Bal CBM</p></td>
	                <td style="word-break: break-all;" width="60"><p>Req Carton</p></td>
	                <td style="word-break: break-all;" width="60"><p>Ok Carton</p></td>
	                <td style="word-break: break-all;" width="60"><p>Bal Carton</p></td>
	                <td style="word-break: break-all;" width="60"><p>Finish Qty</p></td>
	                <td style="word-break: break-all;" width="60"><p>Finish Bal</p></td>
	                <td style="word-break: break-all;" width="60"><p>Pre final</p></td>
	                <td style="word-break: break-all;" width="60"><p>Prefinal<br>Bal</p></td>
	                <td style="word-break: break-all;" width="60"><p>Final</p></td>
	                <td style="word-break: break-all;" width="60"><p>Final Bal</p></td>

	                <td style="word-break: break-all;" width="65"><p>Exfactory</p></td>
	                <td style="word-break: break-all;" width="65"><p>Exfactory Bal</p></td>

	                <td style="word-break: break-all;" width="65"><p>Value</p></td>
	                <td style="word-break: break-all;" width="65"><p>Value Bal</p></td>
	                
	              


	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            foreach ($monthDataArr as $ACC_SHIP_DATE => $date_wise_data) 
					{
						$date_sl=0;
						$date_wise_total=array();
						foreach ($date_wise_data as $buyer_id => $buyer_data) 
						{
							$buyer_sl=0;
							foreach ($buyer_data as $season => $season_buyer_wise) 
							{
								$season_sl=0;
								foreach ($season_buyer_wise as $int_ref => $int_ref_wise) 
								{
									$intref_sl=0;
									foreach ($int_ref_wise as $style_ref_no => $style_ref_wise) 
									{
										$style_sl=0;
										foreach ($style_ref_wise as $acc_po_no => $acc_po_no_wise) 
										{
											$po_sl=0;
											foreach ($acc_po_no_wise as $country_id => $buydata) 
											{
						
														$buy=1; $btotal=0; $dayWiseBtotalArr=array();
												
														//print_r($intrefdata);
														$rtotQty=0;
														$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
														
														?>
														<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
															<?
																if($date_sl==0)
																{
																	?>
																		<td  style="vertical-align: middle;" width="65" rowspan="<?=$date_span[$ACC_SHIP_DATE];?>"><p><?=date('d-M-Y',strtotime($ACC_SHIP_DATE));?></p></td>
																	<?
																}
															
															if($buyer_sl==0)
															{
																?>
																<td width="80" style="vertical-align: middle;"  rowspan="<?=$buyer_span[$ACC_SHIP_DATE][$buyer_id];?>"   style="word-break:break-all"><p><?=$buyerArr[$buyer_id]; ?></p></td>

															<?  
															}
																	$dev=1;
												                   	if($buydata[903]>0)
												                   	{
												                   		$dev=$buydata[903];
												                   	}
												                    $avg_smv=($buydata[904])/$dev;
												                    $avg_fob=($buydata[905])/$dev;
												                    $summary_total['order_qty']+=$buydata[903];
												                    $summary_total['minute']+=$buydata[904];
												                    $summary_total['amount']+=$buydata[905];
												                    $summary_total['avg_smv']+=$avg_smv;
												                    $summary_total['avg_fob']+=$avg_fob;
												                    $summary_total['no_of_acc']+=$buydata['no_of_acc'];
												                    $summary_total['value']+=$buydata['value'];

						                    						$bal_carton = $buydata['carton_qty'] - $buydata['ok_carton'];
						                    						$ok_cbm=fn_number_format(fn_number_format($buydata['cbm'],8,".","")/fn_number_format($buydata['carton_qty'],8,".",""),8,".","") * (fn_number_format($buydata['ok_carton'],8,".","")) ; 

						                							$balance_cbm=($buydata['cbm']-$ok_cbm);
						                							$fin_balance=($buydata[903]-$buydata['fin']);

						                							$summary_total['ok_cbm']+=$ok_cbm;
						                							$summary_total['ok_carton']+=$buydata['ok_carton'];
						                    						$summary_total['bal_carton']+=$bal_carton;
						                    						$summary_total['fin']+=$buydata['fin'];
						                							$summary_total['balance_cbm']+= $balance_cbm; 
						                							$summary_total['fin_balance']+= $fin_balance; 


						                							$date_wise_total['ok_cbm']+=$ok_cbm;
						                							$date_wise_total['ok_carton']+=$buydata['ok_carton'];
						                    						$date_wise_total['bal_carton']+=$bal_carton;
						                    						$date_wise_total['fin']+=$buydata['fin'];
						                							$date_wise_total['balance_cbm']+= $balance_cbm; 
						                							$date_wise_total['fin_balance']+= $fin_balance;


												                    $date_wise_total['order_qty']+=$buydata[903];
												                    $date_wise_total['minute']+=$buydata[904];
												                    $date_wise_total['amount']+=$buydata[905];
												                    $date_wise_total['avg_smv']+=$avg_smv;
												                    $date_wise_total['avg_fob']+=$avg_fob;
												                    $date_wise_total['no_of_acc']+=$buydata['no_of_acc'];
												                    $date_wise_total['value']+=$buydata['value'];
												                    
																
															
																if($season_sl==0)
																{
																	?>
																	<td style="vertical-align: middle;"  rowspan="<?=$season_span[$ACC_SHIP_DATE][$buyer_id][$season];?>" style="word-break: break-all;" width="65"><p><?=$seasonArr[$season];?></p></td>
																	<?
																}
																if($intref_sl==0)
																{
																	?>

												                	<td style="vertical-align: middle;"  rowspan="<?=$intref_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref];?>" style="word-break: break-all;" width="65"><p><?=$int_ref;?></p> </td>
												                	<?
												                }
												                if($style_sl==0)
																{
																	if(strlen($style_ref_no) <= 10)
																	{
																		$style_ref = $style_ref_no;
																	}
																	else
																	{
																		$style_ref = substr($style_ref_no, 0,8)."..";
																	}
																	?>
												                	<td style="vertical-align: middle;"  rowspan="<?=$style_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no];?>" style="word-break: break-all;" width="65" title="<?=$style_ref_no;?>"><p class="short_name"><?=$style_ref;?></p> <p class="full_name" style="display:none;"><?=$style_ref_no;?></p> </td>
												                	<?
												                }

												                if($po_sl==0)
																{
																	if(strlen($acc_po_no) <= 10)
																	{
																		$acc_po = $acc_po_no;
																	}
																	else
																	{
																		$acc_po = substr($acc_po_no, 0,8)."..";
																	}
																	
																	
																	?>
													                <td title="<?=$acc_po_no;?>" style="vertical-align: middle;"  rowspan="<?=$po_span[$ACC_SHIP_DATE][$buyer_id][$season][$int_ref][$style_ref_no][$acc_po_no];?>" style="word-break: break-all;" width="65"><p class="short_name"><?=$acc_po;?></p><p class="full_name" style="display:none;"><?=$acc_po_no;?></p></td>
													                <?
													            }

													            if(strlen($countryArr[$country_id]) <= 10)
																{
																	$country_name =$countryArr[$country_id];
																}
																else
																{
																	$country_name = substr($countryArr[$country_id], 0,8)."..";
																}
													            ?>
												                <td style="word-break: break-all;" width="65" title="<?=$countryArr[$country_id]?>"><p class="short_name"><?=$country_name;?></p><p class="full_name" style="display:none;"><?=$countryArr[$country_id];?></p></td>
												               
																<td width="65" title="<?=number_format($buydata[903]); ?>" align="right" style="word-break:break-all"><p><?=$buydata[903]; ?></p></td>
																
																



																<td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['cbm'],2);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($ok_cbm,2);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($balance_cbm,2);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['carton_qty']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['ok_carton']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($bal_carton);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['fin']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($fin_balance);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['pre_final']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata[903]-$buydata['pre_final']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata['final']);?></p></td>
												                <td style="word-break: break-all;" width="60" align="right"><p><?=number_format($buydata[903]-$buydata['final']);?></p></td>

												                <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($buydata['QNTY']);?></p></td>
												                <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($buydata[903]-$buydata['QNTY']);?></p></td>

												                <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($buydata['value'],0,".",",");?></p></td>
												                <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($buydata[905]-$buydata['value'],0,".",",");?></p></td>
												               

														</tr>
														<?
														$summary_total['QNTY']+=$buydata['QNTY'];
														$summary_total['carton_qty']+=$buydata['carton_qty'];
												        $summary_total['ex_balance']+=($buydata['QNTY']*$avg_fob);
														$summary_total['cbm']+=$buydata['cbm'];

														$summary_total['pre_final']+=$buydata['pre_final'];
														$summary_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);
														$summary_total['final']+=$buydata['final'];
														$summary_total['final_balance']+=($buydata[903]-$buydata['final']);

												        $date_wise_total['carton_qty']+=$buydata['carton_qty'];
												        $date_wise_total['cbm']+=$buydata['cbm'];

												        $date_wise_total['QNTY']+=$buydata['QNTY'];
												        $date_wise_total['ex_balance']+=($buydata['QNTY']*$avg_fob);

												        $date_wise_total['pre_final']+=$buydata['pre_final'];
														$date_wise_total['pre_final_balance']+=($buydata[903]-$buydata['pre_final']);
														$date_wise_total['final']+=$buydata['final'];
														$date_wise_total['final_balance']+=($buydata[903]-$buydata['final']);

														$i++;
														$buyer_sl++;
														$date_sl++;
														$season_sl++;
														$intref_sl++;
														$style_sl++;
														$po_sl++;
											}
										}
									}
								}
							}
						}


						?>
				                    <tr align="center" style="font-weight:bold; background-color:#CCC">
				                    	<td width="65"></td>
				                        <td width="80"  ></td>
				                        <td style="word-break: break-all;" width="65"></td>
						                <td style="word-break: break-all;" width="65"> </td>
						                <td style="word-break: break-all;" width="65"> </td>
						                <td style="word-break: break-all;" width="65"></td>
						                <td style="word-break: break-all;" width="65"><p>Date Total</p></td>
				                        <td width="65" title="<?=number_format($date_wise_total['order_qty']); ?>" align="right" style="word-break:break-all"><p><?=$date_wise_total['order_qty']; ?></p></td>
				                       
				                       
				                        <?

				                        $date_wise_ok_cbm = fn_number_format(($date_wise_total['cbm']/$date_wise_total['carton_qty'])*$date_wise_total['ok_carton'],8,".","");

                        				$date_wise_balance_cbm = $date_wise_total['cbm'] - $date_wise_ok_cbm ;
				                        ?>

				                        <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['cbm'],2);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_ok_cbm,2);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_balance_cbm,2);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['carton_qty']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['ok_carton']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['bal_carton']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['fin']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['fin_balance']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['pre_final']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['pre_final_balance']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['final']);?></p></td>
						                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($date_wise_total['final_balance']);?></p></td>

						               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($date_wise_total['QNTY']);?></p></td>
						               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($date_wise_total['order_qty']-$date_wise_total['QNTY']);?></p></td>
						               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($date_wise_total['value'],0,".",",");?></p></td>
						               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($date_wise_total['amount']-$date_wise_total['value'],0,".",",");?></p></td>
						               
				                        
				                    </tr>	
						<?
						$i++;
					}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
                    <tr align="center" style="font-weight:bold; background-color:#CCC">
                    	<td width="65"></td>
                        <td width="80"  ></td>
                        <td style="word-break: break-all;" width="65"></td>
		                <td style="word-break: break-all;" width="65"> </td>
		                <td style="word-break: break-all;" width="65"> </td>
		                <td style="word-break: break-all;" width="65"></td>
		                <td style="word-break: break-all;" width="65"></td>
                       <?
                       		$summary_total_ok_cbm = fn_number_format(($summary_total['cbm']/$summary_total['carton_qty'])*$summary_total['ok_carton'],8,".","");

                        	$summary_total_balance_cbm = $summary_total['cbm'] - $summary_total_ok_cbm ;
                       ?>
                        <td width="65" title="<?=number_format($summary_total['order_qty']); ?>" align="right" style="word-break:break-all"><p><?=$summary_total['order_qty']; ?></p></td>
                        <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['cbm'],2);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total_ok_cbm,2);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total_balance_cbm,2);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['carton_qty']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['ok_carton']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['bal_carton']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['fin']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['fin_balance']);?></p></td>
		                <td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['pre_final']);?></p></td>
						<td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['pre_final_balance']);?></p></td>
						<td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['final']);?></p></td>
						<td style="word-break: break-all;"  width="60" align="right"><p><?=number_format($summary_total['final_balance']);?></p></td>

		               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($summary_total['QNTY']);?></p></td>
		               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($summary_total['order_qty']-$summary_total['QNTY']);?></p></td>

		               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($summary_total['value'],0,".",",");?></p></td>
		               <td align="right" style="word-break: break-all;" width="65"><p><?=number_format($summary_total['amount']-$summary_total['value'],0,".",",");?></p></td>
		              
                        
                    </tr>
                </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

if($action=="total_order_quantity_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $exfirstYear=explode('-',$monthindex);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	
	//var_dump($yearMonth_arr); die;
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

    $order_summary_name=$monthindex;
	
    $gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$jobItemRatioArr=array(); $smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}

	

	$actualPoSql="select a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,d.unit_price as UNIT_PRICE from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id = d.mst_id and b.id = d.po_break_down_id and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;die;
	
	$actualPoSqlRes=sql_select($actualPoSql); 
	$task_wise_total=array();
	$monthDateArr=array();
	foreach($actualPoSqlRes as $arow)
	{
		// $monthDataArr[$arow["BUYER_NAME"]][903]+=$arow["ACC_PO_QTY"];
		// $shipmentMin=$shpmentVal=0;
		// $shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		// $monthDataArr[$arow["BUYER_NAME"]][904]+=$shipmentMin;
		// $shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		// $monthDataArr[$arow["BUYER_NAME"]][905]+=$shpmentVal;


		$monthDay="";
		$monthDay=date("Y-m",strtotime($arow["ACC_SHIP_DATE"]));//Shipment [Pcs]
		$monthDataArr[$arow["BUYER_NAME"]][903][$monthDay]['qty']+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=$shpmentVal2=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow["BUYER_NAME"]][904][$monthDay]['qty']+=$shipmentMin;
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$shpmentVal2=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow["BUYER_NAME"]][905][$monthDay]['qty']+=$shpmentVal;
		//$monthDataArr[905][$monthDay]['val']+=$shpmentVal2;
		$task_wise_total[903]+=$arow["ACC_PO_QTY"];
		$task_wise_total[904]+=$shipmentMin;
		$task_wise_total[905]+=$shpmentVal;
	}
	
	unset($actualPoSqlRes);
	$tblwidth=count($yearMonth_arr)*80+160;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="<?=count($yearMonth_arr)+2;?>" align="center"><?=$taskArr[$taskid]?>: <?=$order_summary_name; ?> </td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100" >Buyer</td>
	            	<? foreach($yearMonth_arr as $monthindex=>$monthval) 
                     { 

                     ?>
                     <td width="80"><?=$monthval;?></td>
                 <? }?>
	               
	                <td width="80">Total</td>
	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            $month_wise_total=array();
		            foreach($monthDataArr as $buyid=>$buydata)
		            {
						$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
								//print_r($intrefdata);
								$rtotQty=0;
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									
									<td width="100"   style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
									
									
									<?  
										$total=0;
										foreach($yearMonth_arr as $monthindex=>$monthval) 
                    					{ 
                    						$total+=$buydata[$taskid][$monthindex]['qty'];
                    						$month_wise_total[$monthindex]+=$buydata[$taskid][$monthindex]['qty'];
										
									?>
										<td width="80" title="<?=$buydata[$taskid][$monthindex]['qty']; ?>" align="right" style="word-break:break-all"><?=number_format($buydata[$taskid][$monthindex]['qty']); ?></td>
										<?}?>
										<td width="80" title="<?=$total;?>" align="right" style="word-break:break-all"><?=number_format($total); ?></td>
									
		                           
								</tr>
								<?
								
								$i++;
							
						}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100" >Total</td>
	            	<?
	            	$grand_total=0;
	            	foreach($yearMonth_arr as $monthindex=>$monthval) 
                    { 
                    	$grand_total+=$month_wise_total[$monthindex];
	            	?>
		                <td width="80" title="<?=$month_wise_total[$monthindex]; ?>" align="right" style="word-break:break-all"><?=number_format($month_wise_total[$monthindex]); ?></td>
		            <? } ?>
					<td width="80" title="<?=$grand_total; ?>" align="right" style="word-break:break-all"><?=number_format($grand_total); ?></td>
					
	                
	            </tr>
	        </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

if($action=="client_wise_total_order_quantity_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$taskArr=array(9=> "Labdip Submission",10=> "Labdip Approval",48=> "Yarn Allocation",52=> "Dyed Yarn Receive",60=> "Grey Production",61=> "Dyeing",63=> "AOP Receive",73=> "F. Fabric Recvd",84=> "Cutting QC",267=> "Printing Receive",268=> "Embroidery Receive",86=> "Sewing[Pcs]",902=> "Sewing[Minutes]",90=> "Garments Wash Rcv",88=> "Garments Finishing",903=> "Shipment [Pcs]",904=> "Shipment [Minutes]",905=> "Shipment [Value]");
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $exfirstYear=explode('-',$monthindex);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	
	//var_dump($yearMonth_arr); die;
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	$startDate=''; $endDate="";
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

    $order_summary_name=$monthindex;
	
   

	

	$actualPoSql="select a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,d.unit_price as UNIT_PRICE,a.client_id as CLIENT_ID from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
	//echo $actualPoSql;die;
	
	$actualPoSqlRes=sql_select($actualPoSql); 
	$task_wise_total=array();
	$monthDateArr=array();
	$jobNoArr=array();
	foreach($actualPoSqlRes as $arow)
	{
		array_push($jobNoArr, $arow['JOBNO']);
	}
	$job_cond1=where_con_using_array(array_filter(array_unique($jobNoArr)),1,"job_no");
	$job_cond2=where_con_using_array(array_filter(array_unique($jobNoArr)),1,"a.job_no");
	$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $job_cond1";
	//echo $gmtsitemRatioSql; die;
	$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
	$smvJobArr=array();
	foreach($gmtsitemRatioSqlRes as $row)
	{
		$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
	}



	foreach($actualPoSqlRes as $arow)
	{
		// $monthDataArr[$arow["BUYER_NAME"]][903]+=$arow["ACC_PO_QTY"];
		// $shipmentMin=$shpmentVal=0;
		// $shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		// $monthDataArr[$arow["BUYER_NAME"]][904]+=$shipmentMin;
		// $shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		// $monthDataArr[$arow["BUYER_NAME"]][905]+=$shpmentVal;


		$monthDay="";
		$monthDay=date("Y-m",strtotime($arow["ACC_SHIP_DATE"]));//Shipment [Pcs]
		$monthDataArr[$arow["CLIENT_ID"]][903][$monthDay]['qty']+=$arow["ACC_PO_QTY"];
		$shipmentMin=$shpmentVal=$shpmentVal2=0;
		$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
		$monthDataArr[$arow["CLIENT_ID"]][904][$monthDay]['qty']+=$shipmentMin;
		//$shpmentVal=$arow["ACC_PO_QTY"]*$poAvgRateArr[$arow["PO_BREAK_DOWN_ID"]];//Shipment [Value]
		$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$shpmentVal2=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
		$monthDataArr[$arow["CLIENT_ID"]][905][$monthDay]['qty']+=$shpmentVal;
		//$monthDataArr[905][$monthDay]['val']+=$shpmentVal2;
		$task_wise_total[903]+=$arow["ACC_PO_QTY"];
		$task_wise_total[904]+=$shipmentMin;
		$task_wise_total[905]+=$shpmentVal;
	}
	
	unset($actualPoSqlRes);
	$tblwidth=count($yearMonth_arr)*80+160;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">

    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px;"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	            	
	                <td colspan="<?=count($yearMonth_arr)+2;?>" align="center"><?=$taskArr[$taskid]?>: <?=$order_summary_name; ?> </td>
	               
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100" >Client</td>
	            	<? foreach($yearMonth_arr as $monthindex=>$monthval) 
                     { 

                     ?>
                     <td width="80"><?=$monthval;?></td>
                 <? }?>
	               
	                <td width="80">Total</td>
	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            $month_wise_total=array();
		            foreach($monthDataArr as $buyid=>$buydata)
		            {
						$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
								//print_r($intrefdata);
								$rtotQty=0;
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
									
									<td width="100"   style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
									
									
									<?  
										$total=0;
										foreach($yearMonth_arr as $monthindex=>$monthval) 
                    					{ 
                    						$total+=$buydata[$taskid][$monthindex]['qty'];
                    						$month_wise_total[$monthindex]+=$buydata[$taskid][$monthindex]['qty'];
										
									?>
										<td width="80" title="<?=$buydata[$taskid][$monthindex]['qty']; ?>" align="right" style="word-break:break-all"><?=number_format($buydata[$taskid][$monthindex]['qty']); ?></td>
										<?}?>
										<td width="80" title="<?=$total;?>" align="right" style="word-break:break-all"><?=number_format($total); ?></td>
									
		                           
								</tr>
								<?
								
								$i++;
							
						}
						?>
		                
		        </table>
		         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100" >Total</td>
	            	<?
	            	$grand_total=0;
	            	foreach($yearMonth_arr as $monthindex=>$monthval) 
                    { 
                    	$grand_total+=$month_wise_total[$monthindex];
	            	?>
		                <td width="80" title="<?=$month_wise_total[$monthindex]; ?>" align="right" style="word-break:break-all"><?=number_format($month_wise_total[$monthindex]); ?></td>
		            <? } ?>
					<td width="80" title="<?=$grand_total; ?>" align="right" style="word-break:break-all"><?=number_format($grand_total); ?></td>
					
	                
	            </tr>
	        </table>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

if($action=="buyer_wise_capacity_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	
	$buyerArr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	//if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
	//if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
	//if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
	//if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
    $exfirstYear=explode('-',$monthindex);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	//echo $firstYear.'-'.$lastYear;
	$yearMonth_arr=array(); $monthIndexArr=array(); $j=12; $i=1;
	
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
			else if ($i!=1 && $k<7)
			{
				$monthShort=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime(($firstYear.'-'.$k)));
				$yearMonth_arr[$monthShort]=$fiscal_month;
			}
		}
		$i++;
	}
	
	//var_dump($yearMonth_arr); die;
	//echo date("d-M-Y",strtotime($startDate)).'='.date("d-M-Y",strtotime($endDate)).'<br>';
	//$startDate=''; $endDate="";
	//$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	//$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

   // $order_summary_name=$monthindex;
   $exfirstYear=explode('-',$monthindex);
	$firstYear=$exfirstYear[0];
	$lastYear=$exfirstYear[1];
	if($taskid==2000 || $taskid==2000.5)
	{
		if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and b.buyer_id='$buyer_id'"; else $BuyerCond="";
		//$dateCond=" and (b.date_calc between '".$startDate."' and '".$endDate."')";
		
		
		$sql_s="SELECT a.id,
					 b.id AS bid,
					 b.month_id,
					 b.date_calc,
					 b.day_status,
					 b.no_of_line,
					 b.capacity_min,
					 b.capacity_pcs,
					 a.year,
					 a.location_id
					
				FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b
				WHERE     a.comapny_id = $company_id
					 AND ((a.year=$firstYear and b.month_id>6) or (a.year=$lastYear and b.month_id<7))
					 AND a.status_active = 1
					 AND a.is_deleted = 0
					 AND a.id = b.mst_id
					 $LocationCond 
				";
		//echo $sql_s;
		$res_s=sql_select($sql_s);
		$capacityMinArr=array();
		foreach ($res_s as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('date_calc')]));
			//$monthDay=date("Y-m",strtotime($row[csf('year')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			$capacityMinArr[$monthDay][$row[csf('location_id')]]+=$row[csf('capacity_min')];
			//echo "<pre>";
			//echo $row[csf('capacity_min')]." ";
			//echo (($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay])/100);
			//echo "</pre>";
		}
		
		$sql="SELECT b.buyer_id, (b.allocation_percentage) as per, a.month_id, a.year_id, a.location_id
			  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
			  WHERE a.id = b.mst_id
					and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
					and a.company_id=$company_id
					$LocationCond
					$BuyerCond
			  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
		//echo $sql;
		$res=sql_select($sql);
		$monthDataArr=array(); $d=array();
		foreach ($res as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('year_id')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			if(($row[csf('per')]*1)>0)
			{
				if($taskid==2000)
				{
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]+=((($capacityMinArr[$monthDay][$row[csf('location_id')]]*$row[csf('per')])/100)/array_sum($capacityMinArr[$monthDay]))*100;
				}
				else
				{
					$monthDataArr[$row[csf('buyer_id')]][$monthDay]+=($capacityMinArr[$monthDay][$row[csf('location_id')]]*$row[csf('per')])/100;
				}
			}
			/*$d[$row[csf('buyer_id')]][$monthDay]['min']=$capacityMinArr[$monthDay][$row[csf('location_id')]];
			$d[$row[csf('buyer_id')]][$monthDay]['per']=$row[csf('per')];
			$d[$row[csf('buyer_id')]][$monthDay]['t']=array_sum($capacityMinArr[$monthDay]);*/
		}
		//print_r($d[26]); die;
	}
	else if($taskid==2001 || $taskid==2002)
	{
		
		$startDate=''; $endDate="";
		$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
		$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
		if($location_id!=0) $LocationCond=" and location_name='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and buyer_id='$buyer_id'"; else $BuyerCond="";
		if($cbo_season_id!=0) $sessionCond="and season_buyer_wise='$cbo_season_id'"; else $sessionCond="";
		$dateCond=" and (est_ship_date between '".$startDate."' and '".$endDate."')";
		$sql_projection="SELECT company_id,
							   buyer_id,
							   offer_qty,
							   est_ship_date,
							   buyer_submit_price,
							   set_smv,
							   location_name,
							   season_buyer_wise
						FROM wo_quotation_inquery
						WHERE is_deleted=0 and company_id=$company_id $LocationCond $BuyerCond $sessionCond $dateCond";
		//echo $sql_projection;
		//print_r($monthDataArr[2000]);
		$res_projection=sql_select($sql_projection);
		foreach ($res_projection as $row) 
		{
			if($row[csf('offer_qty')]>0)
			{
				$monthDay=date("Y-m",strtotime($row[csf('est_ship_date')]));
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['min']+=($row[csf('offer_qty')]*$row[csf('set_smv')]);
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['val']+=($row[csf('offer_qty')]*$row[csf('buyer_submit_price')]);
			}
		}
	}
	else if($taskid==2003)
	{
		
		if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and b.buyer_id='$buyer_id'"; else $BuyerCond="";
		//$dateCond=" and (b.date_calc between '".$startDate."' and '".$endDate."')";
		
		
		$sql_s="SELECT a.id,
					 b.id AS bid,
					 b.month_id,
					 b.date_calc,
					 b.day_status,
					 b.no_of_line,
					 b.capacity_min,
					 b.capacity_pcs,
					 a.year,
					 a.location_id
					
				FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b
				WHERE     a.comapny_id = $company_id
					 AND ((a.year=$firstYear and b.month_id>6) or (a.year=$lastYear and b.month_id<7))
					 AND a.status_active = 1
					 AND a.is_deleted = 0
					 AND a.id = b.mst_id
					 $LocationCond 
				";
		//echo $sql_s;
		$res_s=sql_select($sql_s);
		$capacityMinArr=array();
		foreach ($res_s as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('date_calc')]));
			//$monthDay=date("Y-m",strtotime($row[csf('year')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			$capacityMinArr[$monthDay][$row[csf('location_id')]]+=$row[csf('capacity_min')];
			//echo "<pre>";
			//echo $row[csf('capacity_min')]." ";
			//echo (($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay])/100);
			//echo "</pre>";
		}
		
		$sql="SELECT b.buyer_id, (b.allocation_percentage) as per, a.month_id, a.year_id, a.location_id
			  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
			  WHERE a.id = b.mst_id
					and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
					and a.company_id=$company_id
					$LocationCond
					$BuyerCond
			  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
		//echo $sql;
		$res=sql_select($sql);
		$monthDataArr=array(); $d=array();
		foreach ($res as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('year_id')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			if(($row[csf('per')]*1)>0)
			{
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['capqty']+=(($capacityMinArr[$monthDay][$row[csf('location_id')]]*$row[csf('per')])/100);
			}
			/*$d[$row[csf('buyer_id')]][$monthDay]['min']=$capacityMinArr[$monthDay][$row[csf('location_id')]];
			$d[$row[csf('buyer_id')]][$monthDay]['per']=$row[csf('per')];
			$d[$row[csf('buyer_id')]][$monthDay]['t']=array_sum($capacityMinArr[$monthDay]);*/
		}
		//print_r($d[26]); die;
	
	
		$startDate=''; $endDate="";
		$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
		$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
		if($location_id!=0) $LocationCond=" and location_name='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and buyer_id='$buyer_id'"; else $BuyerCond="";
		if($cbo_season_id!=0) $sessionCond="and season_buyer_wise='$cbo_season_id'"; else $sessionCond="";
		$dateCond=" and (est_ship_date between '".$startDate."' and '".$endDate."')";
		$sql_projection="SELECT company_id,
							   buyer_id,
							   offer_qty,
							   est_ship_date,
							   buyer_submit_price,
							   set_smv,
							   location_name,
							   season_buyer_wise
						FROM wo_quotation_inquery
						WHERE is_deleted=0 and company_id=$company_id $LocationCond $BuyerCond $sessionCond $dateCond";
		//echo $sql_projection;
		//print_r($monthDataArr[2000]);
		$res_projection=sql_select($sql_projection);
		foreach ($res_projection as $row) 
		{
			if($row[csf('offer_qty')]>0)
			{
				$monthDay=date("Y-m",strtotime($row[csf('est_ship_date')]));
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['min']+=($row[csf('offer_qty')]*$row[csf('set_smv')]);
				//$monthDataArr[$row[csf('buyer_id')]][$monthDay]['val']+=($row[csf('offer_qty')]*$row[csf('buyer_submit_price')]);
			}
		}
	}
	else if($taskid==2004)
	{
		if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and b.buyer_id='$buyer_id'"; else $BuyerCond="";
		//$dateCond=" and (b.date_calc between '".$startDate."' and '".$endDate."')";
		
		
		$sql_s="SELECT a.id,
					 b.id AS bid,
					 b.month_id,
					 b.date_calc,
					 b.day_status,
					 b.no_of_line,
					 b.capacity_min,
					 b.capacity_pcs,
					 a.year,
					 a.location_id
					
				FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b
				WHERE     a.comapny_id = $company_id
					 AND ((a.year=$firstYear and b.month_id>6) or (a.year=$lastYear and b.month_id<7))
					 AND a.status_active = 1
					 AND a.is_deleted = 0
					 AND a.id = b.mst_id
					 $LocationCond 
				";
		//echo $sql_s;
		$res_s=sql_select($sql_s);
		$capacityMinArr=array();
		foreach ($res_s as $row) 
		{
			$monthDay=$row[csf('year')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			$capacityMinArr[$monthDay][$row[csf('location_id')]]+=$row[csf('capacity_min')];
		}
		
		$sql="SELECT b.buyer_id, (b.allocation_percentage) as per, a.month_id, a.year_id, a.location_id
			  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
			  WHERE a.id = b.mst_id
					and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
					and a.company_id=$company_id
					$LocationCond
					$BuyerCond
			  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
		//echo $sql;
		$res=sql_select($sql);
		$monthDataArr=array(); $d=array();
		foreach ($res as $row) 
		{
			$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			if(($row[csf('per')]*1)>0)
			{
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['capqty']+=($capacityMinArr[$monthDay][$row[csf('location_id')]]*$row[csf('per')])/100;
			}
		}
		// if($_SESSION['logic_erp']['user_id'] == 1)
		// {
		// 	echo "<pre>";
		// 	print_r($monthDataArrAlocation);
		// 	echo "</pre>";
		// }
		


		$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 ";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array(); $smvJobArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
		}

		$startDate=''; $endDate="";
		$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
		$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
		if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
		if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
		if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
		//if($cbo_season_id!=0) $jobSeasonCond="and a.season_buyer_wise='$cbo_season_id'"; else $jobSeasonCond="";
		if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
		

		$actualPoSql="select a.job_no as JOBNO, b.id as PO_BREAK_DOWN_ID, d.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY,a.buyer_name as BUYER_NAME,d.unit_price as UNIT_PRICE from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.id = d.mst_id and b.id = d.po_break_down_id and d.status_active = 1 and d.is_deleted = 0 and d.gmts_item <> 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond order by c.acc_ship_date";
		//echo $actualPoSql;die;
		
		$actualPoSqlRes=sql_select($actualPoSql); 
		$monthDataArrShipment=array();
		$monthDateArr=array();
		foreach($actualPoSqlRes as $arow)
		{
			$monthDay="";
			$monthDay=date("Y-m",strtotime($arow["ACC_SHIP_DATE"]));//Shipment [Pcs]
			$shipmentMin=0;
			$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
			$monthDataArr[$arow["BUYER_NAME"]][$monthDay]['shipmin']+=$shipmentMin; 
		}
		foreach($monthDataArr as $buyid=>$buydata)
		{
			$noOfMonthWithZero = 0;
			foreach($yearMonth_arr as $monthindex=>$monthval) 
			{
				$qtyValPer=number_format(fn_number_format(((fn_number_format($buydata[$monthindex]['shipmin'],4,".","")/fn_number_format($buydata[$monthindex]['capqty'],4,".",""))*100),4,".",""),2,".","");
				if($qtyValPer == 0 || $qtyValPer == "") $noOfMonthWithZero++;
			}
			if($noOfMonthWithZero == 12)
			{
				unset($monthDataArr[$buyid]);
			}	
		}
	}
	else if($taskid==2005)
	{
		if($location_id!=0) $LocationCond=" and a.location_id='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and b.buyer_id='$buyer_id'"; else $BuyerCond="";
		//$dateCond=" and (b.date_calc between '".$startDate."' and '".$endDate."')";
		
		$monthDataArr=array();
		$sql_s="SELECT a.id,
					 b.id AS bid,
					 b.month_id,
					 b.date_calc,
					 b.day_status,
					 b.no_of_line,
					 b.capacity_min,

					 b.capacity_pcs,
					 a.year,
					 a.location_id
					
				FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b
				WHERE     a.comapny_id = $company_id
					 AND ((a.year=$firstYear and b.month_id>6) or (a.year=$lastYear and b.month_id<7))
					 AND a.status_active = 1
					 AND a.is_deleted = 0
					 AND a.id = b.mst_id
					 $LocationCond 
				";
		//echo $sql_s;
		$res_s=sql_select($sql_s);
		$capacityMinArr=array();
		foreach ($res_s as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('date_calc')]));
			//$monthDay=date("Y-m",strtotime($row[csf('year')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			$capacityMinArr[$monthDay][$row[csf('location_id')]]+=$row[csf('capacity_min')];
			//echo "<pre>";
			//echo $row[csf('capacity_min')]." ";
			//echo (($row[csf('capacity_min')]*$buyer_percentage[2000][$monthDay])/100);
			//echo "</pre>";
		}
		
		$sql="SELECT b.buyer_id, (b.allocation_percentage) as per, a.month_id, a.year_id, a.location_id
			  FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b
			  WHERE a.id = b.mst_id
					and ((a.year_id=$firstYear and a.month_id>6) or (a.year_id=$lastYear and a.month_id<7))
					and a.company_id=$company_id
					$LocationCond
					$BuyerCond
			  Group by b.buyer_id,a.month_id,a.year_id,b.allocation_percentage,a.location_id";
		//echo $sql;
		$res=sql_select($sql);
		$monthDataArr=array(); $d=array();
		foreach ($res as $row) 
		{
			//$monthDay=date("Y-m",strtotime($row[csf('year_id')].'-'.$row[csf('month_id')].'-01'));
			$monthDay=$row[csf('year_id')]."-".str_pad($row[csf('month_id')],2,"0",STR_PAD_LEFT);
			if(($row[csf('per')]*1)>0)
			{
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['capqty']+=(($capacityMinArr[$monthDay][$row[csf('location_id')]]*$row[csf('per')])/100);
			}
			/*$d[$row[csf('buyer_id')]][$monthDay]['min']=$capacityMinArr[$monthDay][$row[csf('location_id')]];
			$d[$row[csf('buyer_id')]][$monthDay]['per']=$row[csf('per')];
			$d[$row[csf('buyer_id')]][$monthDay]['t']=array_sum($capacityMinArr[$monthDay]);*/
		}//print_r($monthDataArr[26]); die;
		
		$startDate=''; $endDate="";
		$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
		$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
		if($location_id!=0) $jobLocationCond="and a.location_name='$location_id'"; else $jobLocationCond="";
		if($client_id!=0) $jobClientCond="and a.client_id='$client_id'"; else $jobClientCond="";
		if($buyer_id!=0) $jobBuyerCond="and a.buyer_name='$buyer_id'"; else $jobBuyerCond="";
		//if($cbo_season_id!=0) $jobSeasonCond="and a.season_buyer_wise='$cbo_season_id'"; else $jobSeasonCond="";
		if($orderStatus==1) $orderStatusCond=" and b.shiping_status in (1,2)"; else if($orderStatus==2) $orderStatusCond=" and b.shiping_status in (3)"; else $orderStatusCond="";
		
		$dateCond="and (c.task_start_date between '".$startDate."' and '".$endDate."' or c.task_finish_date between '".$startDate."' and '".$endDate."')";
		
		$sql_po="select a.id as JOBID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.total_set_qnty as TOTAL_SET_QNTY, (b.po_quantity*a.set_smv) as SET_SMV, b.id as ID, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, b.shipment_date as SHIPMENT_DATE, (b.po_quantity*a.total_set_qnty) as PO_QUANTITY, b.po_total_price as PO_TOTAL_PRICE, c.task_number as TASK_NUMBER, c.task_start_date as TASK_START_DATE, c.task_finish_date as TASK_FINISH_DATE
		 from wo_po_details_master a, wo_po_break_down b, tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name ='$company_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.task_number in (86) $dateCond $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond $jobSeasonCond";
		 
		$sqlPoRes=sql_select($sql_po); $poidstr=""; $jobIdArr=array(); $dateDataArr=array(); 
		$job_no_arr=array(); 
		foreach($sqlPoRes as $row)
		{
			array_push($job_no_arr, $row['JOB_NO']);
			array_push($jobIdArr, $row['JOBID']);
			$poidstr.=$row['ID'].',';
			if($jobId=="") $jobId="'".$row["JOBID"]."'"; else $jobId.=",'".$row["JOBID"]."'";
			
			$total_date=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
			for($k=0; $k<$total_date; $k++)
			{
				$newdate=add_date(date("Y-m-d",strtotime($row["TASK_START_DATE"])),$k);
				$monthDay=date("Y-m",strtotime($newdate));
				$dateDataArr[$row['ID']][$row["TASK_NUMBER"]][$newdate]=$monthDay;
			}
		}
		
		$powiseNoofDaysArr=array();
		foreach($dateDataArr as $poid=>$podata)
		{
			foreach($podata as $taskno=>$tasknodata)
			{
				foreach($tasknodata as $fdate=>$sdate)
				{
					//print_r($sdate);
					//if($taskno==9)
					$powiseNoofDaysArr[$poid][$taskno][$sdate]+=1;
				}
			}
		}
		
		$actualPoSql="select a.id as JOBID, a.job_no as JOBNO, a.buyer_name as BUYER_NAME, b.id as PO_BREAK_DOWN_ID, c.gmts_item as GMTSITEM, c.acc_ship_date as ACC_SHIP_DATE, d.po_qty as ACC_PO_QTY ,d.unit_price as UNIT_PRICE from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c,wo_po_acc_po_info_dtls d where a.company_name ='$company_id' and a.id=b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and b.id = d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.gmts_item <>0 and d.status_active = 1 and d.is_deleted = 0 and c.acc_ship_date between '".$startDate."' and '".$endDate."' $jobLocationCond $jobClientCond $jobBuyerCond $orderStatusCond";
		
		$actualPoSqlRes=sql_select($actualPoSql);
		foreach ($actualPoSqlRes as $row) {
			array_push($jobIdArr, $row['JOBID']);
		}
		
		$jobidRatioCond=where_con_using_array($jobIdArr,0,"job_id"); 
		$jobidColCond=where_con_using_array($jobIdArr,0,"a.id");
		
		$gmtsitemRatioSql="select job_id AS JOB_ID, job_no as JOBNO, smv_pcs as SMV_PCS, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1 $jobidRatioCond";
		//echo $gmtsitemRatioSql; die;
		//$job_no_cond=str_replace("job_no", "a.job_no", $job_no_cond);
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array(); $smvJobArr=array();
		$job_wise_set_item=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			$smvJobArr[$row['JOBNO']][$row['GMTS_ITEM_ID']]=$row['SMV_PCS'];
			$job_wise_set_item[$row['JOB_ID']]++;
		}
		unset($gmtsitemRatioSqlRes);
		
		$sqlpoarr="select a.id as JOB_ID, a.job_no AS JOB_NO, b.id AS ID, (b.unit_price/a.total_set_qnty) as UNIT_PRICE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_details_master a join  wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on b.id=c.po_break_down_id left join  wo_pre_cost_dtls d on a.id=d.job_id and d.is_deleted=0 and d.status_active=1 where  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name ='$company_id' $jobidColCond";
		//echo $sqlpoarr; die; //and a.job_no='$job_no'
	
		$sqlpodata = sql_select($sqlpoarr);
		//print_r($sqlpodata);
		$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $smvArr=array();
		foreach($sqlpodata as $row)
		{
			$costingPerQty=0;
			if($row['COSTING_PER']==1) $costingPerQty=12;
			elseif($row['COSTING_PER']==2) $costingPerQty=1;	
			elseif($row['COSTING_PER']==3) $costingPerQty=24;
			elseif($row['COSTING_PER']==4) $costingPerQty=36;
			elseif($row['COSTING_PER']==5) $costingPerQty=48;
			else $costingPerQty=0;
			
			$costingPerArr[$row['JOB_ID']]=$costingPerQty;
			$poAvgRateArr[$row["ID"]]=$row["UNIT_PRICE"];
			
			$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			
			$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
			
			$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			$smvmin=0;
			$smvmin=$row['ORDER_QUANTITY']*$smvJobArr[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
			$smvArr[$row['JOB_ID']][$row['ID']]+=$smvmin;
			
			$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		}
		unset($sqlpodata);
		
		//$jobIdArr=array_merge($jobIds,$jobIdArr);
		
		$monthDtlsArr=array();
		foreach($sqlPoRes as $row)
		{
			$noofDays=datediff(d, $row["TASK_START_DATE"], $row["TASK_FINISH_DATE"]);
			foreach($powiseNoofDaysArr[$row["ID"]][$row["TASK_NUMBER"]] as $sdate=>$monthinDays)
			{
				//echo $sdate.'='.$monthinDays.'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'<br>';
				$qty=0;
				if($row["TASK_NUMBER"]==9)//Labdip Submission
				{
					$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);
					//echo $row['JOB_NO'].'='.$row['ID'].'='.$noofColorArr[$row['JOBID']].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
					/*if($sdate=='2020-09')
					{
						echo $row['JOB_NO'].'<br>';
					}*/
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==10)//Labdip Approval
				{
					$qty=(($noofColorArr[$row['JOBID']]/$noofDays)*$monthinDays);
					//echo $row['JOB_NO'].'='.$noofColorArr[$row['JOBID']][$row['ID']].'='.$noofDays.'<br>';
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==48)//Yarn Allocation
				{
					$qty=(($reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty']/$noofDays)*$monthinDays);
					/*if($sdate=='2020-08')
					{
					echo $row['JOB_NO'].'='.$row['ID'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['yarn_qty'].'='.$sdate.'='.$monthinDays.'<br>';
					}*/
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==52)//Dyed Yarn Receive
				{
					$qty=(($convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty']/$noofDays)*$monthinDays);
					//echo $row['JOB_NO'].'='.$row['ID'].'='.$convReqArr[$row['JOBID']][$row['ID']][30]['conv_qty'].'='.$noofDays.'='.$monthinDays.'='.$qty.'<br>';
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==60)//Grey Production
				{
					//echo $row['JOB_NO'].'='.$reqQtyAmtArr[$row['JOBID']][$row['ID']]['prodgrey_qty'].'='.$noofDays.'='.$monthinDays.'<br>';
					$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodgrey_qty']/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==61)//Dyeing
				{
					$qty=(($convReqArr[$row['JOBID']][$row['ID']][31]['conv_qty']/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==63)//AOP Receive
				{
					$qty=(($convReqArr[$row['JOBID']][$row['ID']][35]['conv_qty']/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==73)//F. Fabric Recvd
				{
					$qty=(($reqFabArr[$row['JOBID']][$row['ID']]['prodfin_qty']/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
	
					$f_fabric_rcvd_mfg_kg=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_mfg_kg']/$noofDays)*$monthinDays);
					//$monthDataArr['f_fabric_rcvd_mfg_kg'][$sdate]['qty']+=$f_fabric_rcvd_mfg_kg;
	
					$f_fabric_rcvd_pur_kg=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_pur_kg']/$noofDays)*$monthinDays);
					//$monthDataArr['f_fabric_rcvd_pur_kg'][$sdate]['qty']+=$f_fabric_rcvd_pur_kg;
	
					$f_fabric_rcvd_pur_pcs=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_pur_pcs']/$noofDays)*$monthinDays);
					//$monthDataArr['f_fabric_rcvd_pur_pcs'][$sdate]['qty']+=$f_fabric_rcvd_pur_pcs;
	
					$f_fabric_rcvd_pur_yds_meter=(($reqFabArr[$row['JOBID']][$row['ID']]['f_fabric_rcvd_pur_yds_meter']/$noofDays)*$monthinDays);
					//$monthDataArr['f_fabric_rcvd_pur_yds_meter'][$sdate]['qty']+=$f_fabric_rcvd_pur_yds_meter;
	
				}
				if($row["TASK_NUMBER"]==84)//Cutting QC
				{
					$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==267)//Printing Receive
				{
					$qty=(($embReqArr[$row['JOBID']][$row['ID']][1]['embqty']/$noofDays)*$monthinDays);
					//echo $row['JOB_NO'].'='.$embReqArr[$row['JOBID']][$row['ID']][1]['embqty'].'='.$noofDays.'='.$monthinDays.'<br>';
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==268)//Embroidery Receive
				{
					$qty=(($embReqArr[$row['JOBID']][$row['ID']][2]['embqty']/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==86)//Sewing[Pcs]
				{
					$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);
					
					//$monthDataArr[86][$sdate]['qty']+=$qty;
					
					$sewMin=(($smvArr[$row['JOBID']][$row['ID']]/$noofDays)*$monthinDays);//Sewing[Minutes]
					//echo $sdate.'='.$smvArr[$row['JOBID']][$row['ID']].'='.$noofDays.'='.$monthinDays.'<br>';
					$monthDataArr[$row['BUYER_NAME']][$sdate]['sewmin']+=$sewMin;
				}
				if($row["TASK_NUMBER"]==90)//Garments Wash Rcv
				{
					$qty=(($embReqArr[$row['JOBID']][$row['ID']][3]['embqty']/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
				if($row["TASK_NUMBER"]==88)//Garments Finishing
				{
					$qty=(($row["PO_QUANTITY"]/$noofDays)*$monthinDays);
					//$monthDataArr[$row["TASK_NUMBER"]][$sdate]['qty']+=$qty;
				}
			}
		}
		
		$actualPoArr=array();
		$task_wise_total=array();
		foreach($actualPoSqlRes as $arow)
		{
			$monthDay="";
			$monthDay=date("Y-m",strtotime($arow["ACC_SHIP_DATE"]));//Shipment [Pcs]
			//$monthDataArr[903][$monthDay]['qty']+=$arow["ACC_PO_QTY"];
			$shipmentMin=$shpmentVal=0;
			//echo $arow['JOBNO'].'='.$arow["ACC_PO_QTY"].'='.$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']].'<br>';
			$shipmentMin=$arow["ACC_PO_QTY"]*$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']];//Shipment [Minutes]
			//echo $arow['JOBNO'].'='.$arow["ACC_PO_QTY"].'='.$smvJobArr[$arow['JOBNO']][$arow['GMTSITEM']].'<br>';
			$monthDataArr[$arow["BUYER_NAME"]][$monthDay]['shipmin']+=$shipmentMin;
			$shpmentVal=$arow["ACC_PO_QTY"]*$arow["UNIT_PRICE"];//Shipment [Value]
			//$monthDataArr[905][$monthDay]['qty']+=$shpmentVal;
			//$monthDataArr[905][$monthDay]['JOBNO'].=$arow['JOBNO'].",";
			//$task_wise_total[903]+=$arow["ACC_PO_QTY"];
			//$task_wise_total[904]+=$shipmentMin;
			//$task_wise_total[905]+=$shpmentVal;
		}
		//echo $monthDataArr[905]['2021-10']['JOBNO'];
		unset($actualPoSqlRes);
	}
	else if($taskid==2006)
	{
		$startDate=''; $endDate="";
		$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
		$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
		if($location_id!=0) $LocationCond=" and location_name='$location_id'"; else $LocationCond="";
		if($buyer_id!=0) $BuyerCond="and buyer_id='$buyer_id'"; else $BuyerCond="";
		if($cbo_season_id!=0) $sessionCond="and season_buyer_wise='$cbo_season_id'"; else $sessionCond="";
		$dateCond=" and (est_ship_date between '".$startDate."' and '".$endDate."')";
		$sql_projection="SELECT company_id,
							   buyer_id,
							   offer_qty,
							   est_ship_date,
							   buyer_submit_price,
							   set_smv,
							   location_name,
							   season_buyer_wise
						FROM wo_quotation_inquery
						WHERE is_deleted=0 and company_id=$company_id $LocationCond $BuyerCond $sessionCond $dateCond";
		//echo $sql_projection;
		//print_r($monthDataArr[2000]);
		$res_projection=sql_select($sql_projection);
		foreach ($res_projection as $row) 
		{
			if($row[csf('offer_qty')]>0)
			{
				$monthDay=date("Y-m",strtotime($row[csf('est_ship_date')]));
				$monthDataArr[$row[csf('buyer_id')]][$monthDay]['pcs']+=$row[csf('offer_qty')];
			}
		}
	}
	
	
	
	//unset($actualPoSqlRes);
	$tblwidth=count($yearMonth_arr)*80+180;
	?>
	<script type="text/javascript">
		function print_rpt()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			//$("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="4000px";
		}
		
		function print_excel()
		{
			var filename=$('#hiddfilename').val();
			alert(filename);
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			document.getElementById('report_container').innerHTML='<a href="'+filename+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;'; 
			d.close();
		}
	</script>
    <div style="width:<?=$tblwidth+30; ?>px;">
    	<input type="button" onclick="print_rpt();" value="Print Preview" name="Print" class="formbutton" style="width:100px; margin-left:200px; display:none"/>
    	<div style="100%" id="report_container">
	        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            <tr style="font-weight:bold; background-color:#FFC">
	                <td colspan="<?=count($yearMonth_arr)+2;?>" align="center"><?=$taskArr[$taskid]?>: <?=$order_summary_name; ?> </td>
	            </tr>
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100" >Buyer</td>
	            	<? foreach($yearMonth_arr as $monthindex=>$monthval) 
                     { 
                     ?>
                     <td width="80"><?=$monthval;?></td>
                 <? } if($taskid==2001 || $taskid==2002 || $taskid==2000.5 || $taskid==2006) { ?>
	               
	                <td width="80">Total</td>
                    <? } ?>
	                
	            </tr>
	        </table>
	        <div style="width:<?=$tblwidth+20; ?>px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
		            <? $i=1; 
		            $summary_total=array();
		            $month_wise_total=array();
					foreach($monthDataArr as $buyid=>$buydata)
					{
						$buy=1; $btotal=0; $dayWiseBtotalArr=array();
						
						//print_r($intrefdata);
						$rtotQty=0;
						$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";
						
						?>
						<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
							<td width="100" title="<?=$buyid; ?>" style="word-break:break-all"><?=$buyerArr[$buyid]; ?></td>
							<?  
								$total=0;
								foreach($yearMonth_arr as $monthindex=>$monthval) 
								{
									$qtyValPer=0;
									if($taskid==2000 || $taskid==2000.5)
									{
										$total+=$buydata[$monthindex];
										$month_wise_total[$monthindex]+=$buydata[$monthindex];
										if($taskid==2000.5) $qtyValPer=number_format($buydata[$monthindex],0,".","");
										else if($taskid==2000) $qtyValPer=number_format($buydata[$monthindex],2,".","");
									}
									else if($taskid==2001)
									{
										$total+=$buydata[$monthindex]['min'];
										$month_wise_total[$monthindex]+=$buydata[$monthindex]['min'];
										$qtyValPer=fn_number_format($buydata[$monthindex]['min'],0,".","");
									}
									else if($taskid==2002)
									{
										$total+=$buydata[$monthindex]['val'];
										$month_wise_total[$monthindex]+=$buydata[$monthindex]['val'];
										$qtyValPer=number_format($buydata[$monthindex]['val'],2,".","");
									}
									else if($taskid==2003)
									{
										$qtyValPer=number_format(fn_number_format(((fn_number_format($buydata[$monthindex]['min'],4,".","")/fn_number_format($buydata[$monthindex]['capqty'],4,".",""))*100),4,".",""),2,".","");
										$total+=$qtyValPer;
										$month_wise_total[$monthindex]+=$qtyValPer;
									}
									else if($taskid==2004)
									{
										//echo $buydata[$monthindex]['shipmin'].'='.$buydata[$monthindex]['capqty'].'<br>';
										$qtyValPer=number_format(fn_number_format(((fn_number_format($buydata[$monthindex]['shipmin'],4,".","")/fn_number_format($buydata[$monthindex]['capqty'],4,".",""))*100),4,".",""),2,".","");
										$total+=$qtyValPer;
										$month_wise_total[$monthindex]+=$qtyValPer;
									}
									else if($taskid==2005)
									{
										//echo $buydata[$monthindex]['shipmin'].'='.$buydata[$monthindex]['capqty'].'<br>';
										$qtyValPer=number_format(fn_number_format(((fn_number_format($buydata[$monthindex]['sewmin'],4,".","")/fn_number_format($buydata[$monthindex]['capqty'],4,".",""))*100),4,".",""),2,".","");
										$total+=$qtyValPer;
										$month_wise_total[$monthindex]+=$qtyValPer;
									}
									else if($taskid==2006)
									{
										$qtyValPer=number_format($buydata[$monthindex]['pcs'],0,".","");
										$total+=$qtyValPer;
										$month_wise_total[$monthindex]+=$qtyValPer;
									}
								?>
								<td width="80" title="<?=$buydata[$monthindex]['shipmin'].'='.$buydata[$monthindex]['capqty']; ?>" align="right" style="word-break:break-all"><? if($qtyValPer!=0) echo number_format($qtyValPer,0,".",","); else echo ""; ?></td>
								<? } if($taskid==2001 || $taskid==2002 || $taskid==2000.5 || $taskid==2006) {  ?>
								<td width="80" title="<?=$total;?>" align="right" style="word-break:break-all"><?=number_format($total,0,".",","); ?></td>
								<? } ?>
							</tr>
							<?
							$i++;
						}
						?>
		        	</table>
                <? if($taskid==2000 ||$taskid==2001 || $taskid==2002 || $taskid==2000.5 || $taskid==2006) {  ?>
		        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<?=$tblwidth; ?>px;" rules="all">
	            
	            <tr align="center" style="font-weight:bold; background-color:#CCC">
	            	<td width="100" >Total</td>
	            	<?
	            	$grand_total=0;
	            	foreach($yearMonth_arr as $monthindex=>$monthval) 
                    { 
                    	$grand_total+=$month_wise_total[$monthindex];
	            	?>
		                <td width="80" title="<?=$month_wise_total[$monthindex]; ?>" align="right" style="word-break:break-all"><?=number_format($month_wise_total[$monthindex],0,".",","); ?></td>
		           <? } if($taskid!=2000) { ?>
					<td width="80" title="<?=$grand_total; ?>" align="right" style="word-break:break-all"><?=number_format($grand_total,0,".",","); ?></td>
                    <? } ?>
	            </tr>
	        </table>
            <? } ?>
	        </div>
       
    	</div>
    </div>
    <?
	exit();
}

?>