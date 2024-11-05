<?
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.others.php');

session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 130, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", "", "" );
    exit();
}

if ($action=="load_drop_down_buyer_client")
{
    echo create_drop_down( "cbo_client", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );
    exit();
}

$date=date('Y-m-d');
$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');

if($action=="report_generate")
{
	$company_id 	= str_replace("'","",$cbo_company_id);
	$location_id 	= str_replace("'","",$cbo_location_id);
	$client_id 		= str_replace("'","",$cbo_client);
	$order_status	= str_replace("'","",$cbo_order_status);
	$from_year 		= str_replace("'","",$cbo_from_year);
	$to_year 		= str_replace("'","",$cbo_to_year);

	$sqlCond = "";
	$sqlCond .= ($company_id != 0) ? " and a.company_name=$company_id" : "";
	$sqlCond .= ($location_id != 0) ? " and a.location_name=$location_id" : "";
	$sqlCond .= ($client_id != 0) ? " and a.client_id=$client_id" : "";
	if($order_status !=0)
	{
		$sqlCond .= ($order_status == 1) ? " and b.shiping_status in(1,2)" : " and b.shiping_status in(3)";
	}
	// echo $sqlCond;die();
	$client_array = return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$company_id' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name", "id", "buyer_name");

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
	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/	
	$sql_gmt="SELECT a.JOB_NO,a.TOTAL_SET_QNTY,b.id as PO_ID";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql_gmt.=", SUM(CASE WHEN b.shipment_date between '$exydata[0]' and '$exydata[1]' THEN (b.po_quantity*a.total_set_qnty) ELSE 0 END) AS M$fyear ";
	}
	$sql_gmt.=" from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id $sqlCond and b.shipment_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no,a.total_set_qnty,b.id";//and c.approved=1
	// echo $sql_gmt;die();
	$sql_gmt_res = sql_select($sql_gmt);
	if(!count($sql_gmt_res))
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}

	$po_id_array = array();
	$gmt_array = array();
	foreach ($sql_gmt_res as $val) 
	{	
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$myear='M'.$nyear;
			$gmt_array[$fyear] += $val[$myear];
		}	
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];	
	}
	// print_r($gmt_array);die();
	unset($sql_gmt_res);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	/*==========================================================================================/
	/								yarn req qty from budget 									/
	/==========================================================================================*/
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);

    $sql = "SELECT a.job_no AS JOB_NO ,b.grouping as INT_REF,b.id AS id,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS country_id,c.color_number_id AS color_number_id,c.size_number_id AS size_number_id,c.order_quantity AS ORDER_QUANTITY,c.country_ship_date AS country_ship_date,d.id AS pre_cost_dtls_id,d.fab_nature_id AS fab_nature_id,d.construction AS construction, d.gsm_weight AS gsm_weight,e.cons AS cons,f.id AS yarn_id,f.count_id AS count_id,f.copm_one_id AS copm_one_id,f.percent_one AS percent_one,f.type_id AS type_id,f.color AS color,f.cons_qnty AS cons_qnty,f.avg_cons_qnty AS avg_cons_qnty,f.rate AS rate,f.amount AS amount";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sql.=", (CASE WHEN b.shipment_date between '$exydata[0]' and '$exydata[1]' THEN c.plan_cut_qnty ELSE 0 END) AS PLAN_CUT$fyear ";
		$sql.=", (CASE WHEN b.shipment_date between '$exydata[0]' and '$exydata[1]' THEN e.requirment ELSE 0 END) AS REQ$fyear ";
		$sql.=", (CASE WHEN b.shipment_date between '$exydata[0]' and '$exydata[1]' THEN f.cons_ratio ELSE 0 END) AS CONSRATIO$fyear ";
	}
    $sql.=" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.shipment_date between '$startDate' and '$endDate' and a.company_name=$company_id $poCond ";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$yarn_req_array = array();
	foreach ($sql_res as $key => $row) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$plan_cut_year 	= 'PLAN_CUT'.$nyear;
			$req_year 		= 'REQ'.$nyear;
			$consratio_year = 'CONSRATIO'.$nyear;

			$costingPer = $costing_per_arr[$row['JOB_NO']];
			if($costingPer==1) $pcs_value=1*12;
	        else if($costingPer==2) $pcs_value=1*1;
	        else if($costingPer==3) $pcs_value=2*12;
	        else if($costingPer==4) $pcs_value=3*12;
	        else if($costingPer==5) $pcs_value=4*12;

	        $gmtsitemRatio 	= $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
	        $consRatio 		= $row[$consratio_year];
	        $requirment 	= $row[$req_year];
	        $consQnty 		= $requirment*$consRatio/100;
	        $reqQty 		= ($row[$plan_cut_year]/$gmtsitemRatio)*($consQnty/$pcs_value);	
	        // echo $row['JOB_NO']."==".$reqQty 		."= (".$row[$plan_cut_year]."/".$gmtsitemRatio.")*(".$consQnty."/".$pcs_value.")<br>";

			
			($reqQty>0)?$yarn_req_array[$fyear] += $reqQty:"";
		}
	}
	unset($sql_res);
	// print_r($yarn_req_array);die();
	/*==========================================================================================/
	/									get req. and program id									/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sql_plan = "SELECT c.requisition_no as REQUISITION_ID, c.knit_id as  PROGRAM_ID from wo_booking_mst a, ppl_planning_entry_plan_dtls b, ppl_yarn_requisition_entry c where a.booking_no=b.booking_no and b.dtls_id=c.knit_id and a.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 $bookingPoCond";
	// echo $sql_plan;
	$plan_res = sql_select($sql_plan);
	$program_id_array = array();
	$requisition_id_array = array();
	foreach ($plan_res as $val) 
	{
		$program_id_array[$val['PROGRAM_ID']] = $val['PROGRAM_ID'];
		$requisition_id_array[$val['REQUISITION_ID']] = $val['REQUISITION_ID'];
	}
	$program_ids = implode(",", $program_id_array);
	$requisition_ids = implode(",", $requisition_id_array);
	// =======================================================
	$program_id_list_arr=array_chunk($program_id_array,999);
	$programCond = " and ";
	$p=1;
	foreach($program_id_list_arr as $programids)
    {
    	if($p==1) 
		{
			$programCond .="  ( a.booking_id in(".implode(',',$programids).")"; 
		}
        else
        {
          $programCond .=" or a.booking_id in(".implode(',',$programids).")";
      	}
        $p++;
    }
    $programCond .=")";
	// =======================================================
	$requisition_id_list_arr=array_chunk($requisition_id_array,999);
	$requisitionCond = " and ";
	$p=1;
	foreach($requisition_id_list_arr as $requisitionids)
    {
    	if($p==1) 
		{
			$requisitionCond .="  ( a.requisition_no in(".implode(',',$requisitionids).")"; 
		}
        else
        {
          $requisitionCond .=" or a.requisition_no in(".implode(',',$requisitionids).")";
      	}
        $p++;
    }
    $requisitionCond .=")";

	/*==========================================================================================/
	/										excess yarn											/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlExcessYarn = "SELECT c.job_no";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlExcessYarn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.grey_fab_qnty ELSE 0 END) AS GF$fyear ";
	} 
	$sqlExcessYarn .= " FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $bookingPoCond group by c.job_no";
    // echo $sqlExcessYarn;die();
    $exYarnRes = sql_select($sqlExcessYarn);
    $excess_yarn_array = array();
    foreach ($exYarnRes as $val) 
    {
    	foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$exyear 	= 'GF'.$nyear;
        	$excess_yarn_array[$fyear] += $val[$exyear];
        }
    }
    unset($exYarnRes);
    /*==========================================================================================/
	/										yarn receive										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArn = "SELECT a.TRANSACTION_TYPE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS Y$fyear ";
	}
	$sqlYArn .= " from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond group by a.transaction_type";
	// echo $sqlYArn;die();
	$yarnRes = sql_select($sqlYArn);
	$yarn_data_array = array();
	foreach ($yarnRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'Y'.$nyear;
        	$yarn_data_array[$val['TRANSACTION_TYPE']][$fyear] += $val[$yyear];
        }
	}
	// print_r($yarn_data_array);die();
    /*==========================================================================================/
	/										yarn issue											/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArn = "SELECT a.TRANSACTION_TYPE,d.ISSUE_PURPOSE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArn.=", SUM(CASE WHEN c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS Y$fyear ";
	}
	$sqlYArn .= " from inv_transaction a, order_wise_pro_details b,wo_po_break_down c,inv_issue_master d where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=b.po_breakdown_id and d.id=a.mst_id and a.item_category=1 and a.transaction_type in(2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and c.shipment_date between '$startDate' and '$endDate' $yarnPoCond $requisitionCond group by a.transaction_type,d.issue_purpose";
	// echo $sqlYArn;die();
	$yarnRes = sql_select($sqlYArn);
	$yarn_knit_data_array = array();
	foreach ($yarnRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'Y'.$nyear;
			if($val['ISSUE_PURPOSE']==1)
			{
	        	$yarn_knit_data_array[$val['TRANSACTION_TYPE']][$fyear] += $val[$yyear];
	        }
        	$yarn_data_array[$val['TRANSACTION_TYPE']][$fyear] += $val[$yyear];
        }
	}
	// print_r($yarn_knit_data_array);die();
    /*==========================================================================================/
	/						yarn issue for dyeing,twisting,recon....							/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArn = "SELECT c.ID,c.ISSUE_PURPOSE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS Y$fyear ";
	}
	$sqlYArn .= " from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond and c.issue_purpose in(2,12,15,38) group by c.id,c.issue_purpose";
	// echo $sqlYArn;die();
	$yarnRes = sql_select($sqlYArn);
	$yarn_dyeing_data_array = array();
	$issue_id_array = array();
	$issue_purpose_array = array();
	foreach ($yarnRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'Y'.$nyear;
        	$yarn_dyeing_data_array[$fyear][$val['ISSUE_PURPOSE']] += $val[$yyear];
        }
        $issue_id_array[$val['ID']] = $val['ID'];
        $issue_purpose_array[$val['ID']] = $val['ISSUE_PURPOSE'];
	}
	$issueIds = implode(",", $issue_id_array);

	// =======================================================
	$issue_id_list_arr=array_chunk($issue_id_array,999);
	$issueIdCond = " and ";
	$p=1;
	foreach($issue_id_list_arr as $issueids)
    {
    	if($p==1) 
		{
			$issueIdCond .="  ( a.issue_id in(".implode(',',$issueids).")"; 
		}
        else
        {
          $issueIdCond .=" or a.issue_id in(".implode(',',$issueids).")";
      	}
        $p++;
    }
    $issueIdCond .=")";
	// print_r($yarn_data_array);die();
    /*==========================================================================================/
	/						get yarn used qty for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,a.RECEIVE_PURPOSE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArnUsd.=", sum(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.grey_quantity ELSE 0 END) AS U$fyear ";
	}
	$sqlYArnUsd .= " from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2,12,15,38) and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond group by a.recv_number,a.RECEIVE_PURPOSE";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'U'.$nyear;
        	$yarn_dyeing_used_data_array[$fyear][$val['RECEIVE_PURPOSE']] += $val[$yyear];
        }
	}
	// print_r($yarn_dyeing_used_data_array);die();
    /*==========================================================================================/
	/					get yarn rej & rtn qty for dyeing,twisting,recon....					/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.ISSUE_ID";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArnUsd.=", (CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.cons_quantity ELSE 0 END) AS U$fyear ";
		$sqlYArnUsd.=", (CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.cons_reject_qnty ELSE 0 END) AS R$fyear ";
	}
	$sqlYArnUsd .= " from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id  and c.id=d.job_id and b.item_category=1  and a.entry_form=9 and b.transaction_type in(4) and e.trans_id=b.id and d.id=e.po_breakdown_id and b.transaction_type=e.trans_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose is null and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond  $issueIdCond";//and a.booking_id in($yd_wo_ids)
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_rtn_rej_data_array = array();
	$chk_rcv_no = array();
	// print_r($fiscal_year_arr);die();
	foreach ($yarnUsdRes as $val) 
	{		
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'U'.$nyear;
			$ryear 	= 'R'.$nyear;
        	$yarn_dyeing_rtn_rej_data_array[$fyear][$issue_purpose_array[$val['ISSUE_ID']]]['rtn_qty'] += $val[$yyear];
        	$yarn_dyeing_rtn_rej_data_array[$fyear][$issue_purpose_array[$val['ISSUE_ID']]]['rej_qty'] += $val[$ryear];
	    }
	}
	// print_r($yarn_dyeing_rtn_rej_data_array);die();
    /*==========================================================================================/
	/										get dyed yarn rcv qty								/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,a.RECEIVE_PURPOSE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArnUsd.=", sum(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN f.quantity ELSE 0 END) AS U$fyear ";
	}
	$sqlYArnUsd .= " from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e,order_wise_pro_details f where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and f.trans_id=b.id and d.id=f.po_breakdown_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond group by a.recv_number,a.RECEIVE_PURPOSE";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$dyed_yarn_rcv_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'U'.$nyear;
        	$dyed_yarn_rcv_array[$fyear] += $val[$yyear];
        }
	}
	// print_r($dyed_yarn_rcv_array);die();
    /*==========================================================================================/
	/									get yarn used qty										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnUsd = "SELECT a.id";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlYArnUsd.=", SUM(CASE WHEN f.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS U$fyear ";
		$sqlYArnUsd.=", SUM(CASE WHEN f.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.returnable_qnty ELSE 0 END) AS R$fyear ";
	}
	$sqlYArnUsd .= " from inv_receive_master a, order_wise_pro_details b,inv_transaction d,wo_po_details_master e,wo_po_break_down f where d.id=b.trans_id and d.prod_id=b.prod_id and e.id=f.job_id and f.id=b.po_breakdown_id and d.item_category=13 and b.entry_form=2 and d.transaction_type in(1) and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.shipment_date between '$startDate' and '$endDate' $yarnPoCond $programCond group by a.id";
	// echo $sqlYArnUsd;
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'U'.$nyear;
			$ryear 	= 'R'.$nyear;
        	$yarn_used_data_array[$fyear] += $val[$yyear] + $val[$ryear];
        }
	}
	// print_r($yarn_used_data_array);die();
    /*==========================================================================================/
	/										grey yarn issue										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArn = "SELECT a.TRANSACTION_TYPE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlGreyYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS Y$fyear ";
	}
	$sqlGreyYArn .= " from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond $requisitionCond group by a.transaction_type";
	// echo $sqlGreyYArn;die();
	$yarnRes = sql_select($sqlGreyYArn);
	$grey_yarn_data_array = array();
	foreach ($yarnRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'Y'.$nyear;
        	$grey_yarn_data_array[$val['TRANSACTION_TYPE']][$fyear] += $val[$yyear];
        }
	}
    /*==========================================================================================/
	/										grey yarn issue	rtn									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$reqCond = str_replace("a.requisition_no", "c.booking_id", $requisitionCond);
	$sqlGreyYArn = "SELECT a.TRANSACTION_TYPE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlGreyYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS Y$fyear ";
		$sqlGreyYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS R$fyear ";
	}
	$sqlGreyYArn .= " from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond $reqCond group by a.TRANSACTION_TYPE";
	// echo $sqlGreyYArn;die();
	$yarnRes = sql_select($sqlGreyYArn);
	$grey_yarn_rej_data_array = array();
	foreach ($yarnRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'Y'.$nyear;
			$ryear 	= 'R'.$nyear;
        	$grey_yarn_data_array[$val['TRANSACTION_TYPE']][$fyear] += $val[$yyear];
        	$grey_yarn_rej_data_array[$val['TRANSACTION_TYPE']][$fyear] += $val[$ryear];
        }
	}
	// print_r($grey_yarn_rej_data_array);die();
    /*==========================================================================================/
	/										dyed yarn issue	rtn									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArn = "SELECT a.TRANSACTION_TYPE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlGreyYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS Y$fyear ";
		$sqlGreyYArn.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS R$fyear ";
	}
	$sqlGreyYArn .= " from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(1) and f.item_category_id=1 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond $reqCond group by a.transaction_type";
	// echo $sqlGreyYArn;die();
	$yarnRes = sql_select($sqlGreyYArn);
	$dyed_yarn_rej_rtn_array = array();
	foreach ($yarnRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'Y'.$nyear;
			$ryear 	= 'R'.$nyear;
        	$dyed_yarn_rej_rtn_array[$val['TRANSACTION_TYPE']][$fyear]['rtn'] += $val[$yyear];
        	$dyed_yarn_rej_rtn_array[$val['TRANSACTION_TYPE']][$fyear]['rej'] += $val[$ryear];
        }
	}
    /*==========================================================================================/
	/									grey and fin fabric	rcv									/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlgreyfab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS FAB$fyear ";
		$sqlgreyfab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.RETURNABLE_QNTY ELSE 0 END) AS FABREJ$fyear ";
	}
	$sqlgreyfab .= " from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_break_down d,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and d.id=b.po_breakdown_id and a.transaction_type in(1,4) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $greyfabPoCond group by a.transaction_type,c.receive_purpose,a.item_category";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FAB'.$nyear;
			$yrejear 	= 'FABREJ'.$nyear;
			if($val['RECEIVE_PURPOSE']==31)
			{
				$fab_data_array[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['leftover_qnty'] += $val[$yyear];
			}
			else
			{
				$fab_data_array[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['receive_qnty'] += $val[$yyear];
				$fab_data_array[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['reject_qty'] += $val[$yrejear];
			}
        	
        }
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	issue								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT a.TRANSACTION_TYPE,a.ITEM_CATEGORY,c.ISSUE_PURPOSE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlgreyfab.=", SUM(CASE WHEN e.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS FAB$fyear ";
	}
	$sqlgreyfab .= " from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(2,3) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond  group by a.transaction_type,a.item_category, c.issue_purpose";//and c.issue_purpose != 31
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $fab_data_array = array();
	$scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FAB'.$nyear;

			$fab_data_array[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['issue_qnty'] += $val[$yyear];	
			if($val['ISSUE_PURPOSE']==31)
			{
				$fab_data_array[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['scrap_qty'] += $val[$yyear];
			}
        }
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/							grey and fin fabric	rcv	without sample							/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$progCond = str_replace("a.booking_id", "c.booking_id", $programCond);
	$sqlgreyfab = "SELECT a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlgreyfab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS FAB$fyear ";
		$sqlgreyfab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.returnable_qnty ELSE 0 END) AS RTN$fyear ";
	}
	$sqlgreyfab .= " from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_break_down d,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=c.booking_id and f.booking_no=h.booking_no and h.booking_type=1 and d.id=b.po_breakdown_id and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $greyfabPoCond  group by a.transaction_type,c.receive_purpose,a.item_category";//$progCond
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array_without_sample = array();
	foreach ($greyfabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FAB'.$nyear;
			$rtnyear 	= 'RTN'.$nyear;
			if($val['RECEIVE_PURPOSE']==31)
			{
				$fab_data_array_without_sample[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['leftover_qnty'] += $val[$yyear];
			}
			else
			{
				$fab_data_array_without_sample[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['receive_qnty'] += $val[$yyear];
				$fab_data_array_without_sample[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['rej_qty'] += $val[$rtnyear];
			}
        	
        }
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/							grey and fin fabric	issue without sample						/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$progCond = str_replace("a.booking_id", "a.requisition_no", $programCond);
	$sqlgreyfab = "SELECT a.TRANSACTION_TYPE,a.ITEM_CATEGORY,c.ISSUE_PURPOSE";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlgreyfab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS FAB$fyear ";
	}
	$sqlgreyfab .= " from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_break_down d,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=a.requisition_no and f.booking_no=h.booking_no and h.booking_type=1 and d.id=b.po_breakdown_id and a.transaction_type in(2,3) and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.issue_purpose != 31 and d.shipment_date between '$startDate' and '$endDate' $greyfabPoCond  group by a.transaction_type,a.item_category, c.issue_purpose";//and c.issue_purpose != 31 $progCond
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $fab_data_array = array();
	$scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FAB'.$nyear;

			if($val['ISSUE_PURPOSE']==31 && $val['TRANSACTION_TYPE']==2)
			{
				$scrap_data_array[$fyear][$val['ITEM_CATEGORY']] += $val[$yyear];
			}
			else
			{
				$fab_data_array_without_sample[$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']][$fyear]['issue_qnty'] += $val[$yyear];
			}			
        	
        }
	}
	// print_r($fab_data_array);die();
	/*==========================================================================================/
	/										batch data											/
	/==========================================================================================*/
	$batchPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sqlBatch = "SELECT a.batch_no";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlBatch.=", SUM(CASE WHEN c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.batch_qnty ELSE 0 END) AS B$fyear ";
	}
	$sqlBatch .= " from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.batch_against in(1) and c.shipment_date between '$startDate' and '$endDate' $batchPoCond group by a.batch_no";
	// echo $sqlBatch;die();
	$batchRes = sql_select($sqlBatch);
	$batch_data_array = array();
	foreach ($batchRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'B'.$nyear;
        	$batch_data_array[$fyear] += $val[$yyear];
        }
	}
	// print_r($batch_data_array);die();
	/*==========================================================================================/
	/										FF Rcv data											/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT a.recv_number";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlFinFab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN c.quantity ELSE 0 END) AS FF$fyear ";
		$sqlFinFab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN c.returnable_qnty ELSE 0 END) AS FFR$fyear ";
	}
    $sqlFinFab .= "from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,pro_batch_create_mst f where a.id=b.mst_id and b.id=c.trans_id and d.id=c.po_breakdown_id and b.transaction_type=1 $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 and f.id=b.pi_wo_batch_no and f.batch_against=1 and f.status_active=1 and d.shipment_date between '$startDate' and '$endDate' group by a.recv_number";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_data_array = array();
	foreach ($finFabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FF'.$nyear;
			$yryear 	= 'FFR'.$nyear;
        	$finfab_data_array[$fyear]['rcv_qty'] += $val[$yyear];
        	$finfab_data_array[$fyear]['rej_qty'] += $val[$yryear];
        }
	}
	// print_r($finfab_data_array);die();
	/*==========================================================================================/
	/									FF Purses Rcv data										/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT b.TRANSACTION_TYPE";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlFinFab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN c.quantity ELSE 0 END) AS FF$fyear ";
	}
    $sqlFinFab .= " from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(1,4) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(37,52) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and d.shipment_date between '$startDate' and '$endDate' group by b.transaction_type";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_purses_data_array = array();
	foreach ($finFabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FF'.$nyear;
        	$finfab_purses_data_array[$fyear][$val['TRANSACTION_TYPE']]['qty'] += $val[$yyear];
        }
	}
	// print_r($finfab_purses_data_array);die();
	/*==========================================================================================/
	/									FF Purses Issue data									/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT b.TRANSACTION_TYPE";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlFinFab.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN c.quantity ELSE 0 END) AS FF$fyear ";
	}
    $sqlFinFab .= " from inv_issue_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(2,3) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(18,46) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and d.shipment_date between '$startDate' and '$endDate' group by b.transaction_type";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	foreach ($finFabRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FF'.$nyear;
        	$finfab_purses_data_array[$fyear][$val['TRANSACTION_TYPE']]['qty'] += $val[$yyear];
        }
	}
	// print_r($finfab_purses_data_array);die();
	/*==========================================================================================/
	/										transfer data										/
	/==========================================================================================*/
	$transPoCond = str_replace("b.id", "a.to_order_id", $poCond);
	$transPoCond2 = str_replace("b.id", "a.from_order_id", $poCond);
    $sqltrnsfr = "SELECT a.ITEM_CATEGORY";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqltrnsfr.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN e.QUANTITY ELSE 0 END) AS FF$fyear ";
	}
    $sqltrnsfr .= "from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(5) and e.po_breakdown_id=d.id and d.id=a.to_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.shipment_date between '$startDate' and '$endDate' $transPoCond group by a.item_category";
    // echo $sqltrnsfr;die();
    $trnsfrRes = sql_select($sqltrnsfr);
    $transfer_in_data_array = array();
	foreach ($trnsfrRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FF'.$nyear;
        	$transfer_in_data_array[$fyear][$val['ITEM_CATEGORY']] += $val[$yyear];
        }
	}
	// print_r($transfer_in_data_array);die();
	//=====================================================================================	
    $sqltrnsfr = "SELECT a.ITEM_CATEGORY";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqltrnsfr.=", SUM(CASE WHEN d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN e.QUANTITY ELSE 0 END) AS FF$fyear ";
	}
    $sqltrnsfr .= "from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(6) and e.po_breakdown_id=d.id and d.id=a.from_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.shipment_date between '$startDate' and '$endDate' $transPoCond2 group by a.item_category";
    // echo $sqltrnsfr;die();
    $trnsfrRes = sql_select($sqltrnsfr);
    $transfer_out_data_array = array();
	foreach ($trnsfrRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$yyear 	= 'FF'.$nyear;
        	$transfer_out_data_array[$fyear][$val['ITEM_CATEGORY']] += $val[$yyear];
        }
	}
	// print_r($transfer_out_data_array);die();
	/*==========================================================================================/
	/										scrap data											/
	/==========================================================================================*/
	$scrapPoCond = str_replace("b.id", "e.id", $poCond);
	$sqlscrap = "SELECT c.ITEM_CATEGORY";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlscrap .=", (case when e.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.quantity ELSE 0 END) AS G$fyear ";
		
	}
	$sqlscrap .= "from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(13) and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $scrapPoCond and c.issue_purpose = 31";
	// echo $sqlscrap;die();
	$scrapRes = sql_select($sqlscrap);
	$scrap_data_array = array();
	foreach ($scrapRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$gcyear 	= 'G'.$nyear;
        	$scrap_data_array[$fyear][$val['ITEM_CATEGORY']]['grey'] += $val[$gcyear];
        }
	}
	// print_r($batch_data_array);die();
	// ======================================= FOR FIN FAB ====================================================	
	$scrapPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlscrap = "SELECT a.ITEM_CATEGORY_ID,a.RECEIVE_BASIS";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlscrap .=", (case when d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.RECEIVE_QNTY ELSE 0 END) AS G$fyear ";
		
	}
	$sqlscrap .= " from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, wo_po_details_master c,wo_po_break_down d,pro_batch_create_mst e,pro_batch_create_dtls f where a.id=b.mst_id and b.lot=e.batch_no and a.item_category_id in(2) and e.id=f.mst_id and e.batch_against=1 and c.id=d.job_id and d.id=f.po_id AND b.body_part_id=f.body_part_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $scrapPoCond";
	// echo $sqlscrap;die();
	$scrapRes = sql_select($sqlscrap);
	// $scrap_data_array = array();
	foreach ($scrapRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$gcyear 	= 'G'.$nyear;
			if($val['RECEIVE_BASIS'] !=1)
			{
				$scrap_data_array[$fyear][$val['ITEM_CATEGORY_ID']]['fin'] += $val[$gcyear];
			}
        	$scrap_data_array[$fyear][$val['ITEM_CATEGORY_ID']]['allfin'] += $val[$gcyear];
        }
	}
	/*==========================================================================================/
	/											AOP Data 										/
	/==========================================================================================*/
	$aopPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlAop = "select a.id";
	
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlAop .=", sum(case when c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.grey_used ELSE 0 END) AS G$fyear ";
		$sqlAop .=", sum(case when c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.batch_issue_qty ELSE 0 END) AS F$fyear ";
		$r++;
	}
	$sqlAop .= "from inv_receive_mas_batchroll a, pro_grey_batch_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.order_id and a.status_active=1 and b.status_active=1 and a.entry_form=92 and c.shipment_date between '$startDate' and '$endDate' $aopPoCond group by a.id";
	// echo $sqlAop;die();
	$aopRes = sql_select($sqlAop);
    $aop_data_array = array();
	foreach ($aopRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$gcyear 	= 'G'.$nyear;
			$fcyear 	= 'F'.$nyear;

        	$aop_data_array[$fyear]['grey'] 		+= $val[$gcyear];
        	$aop_data_array[$fyear]['finish'] 		+= $val[$fcyear];
        }
	}
	// print_r($aop_data_array);die();

	/*==========================================================================================/
	/										cutting qc Data 									/
	/==========================================================================================*/
	$cutQcPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlcutQc = "select a.cutting_qc_no";
	
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlcutQc .=", sum(case when c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.qc_pass_qty ELSE 0 END) AS QC$fyear ";
		$sqlcutQc .=", sum(case when c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS RJ$fyear ";
	}
	$sqlcutQc .= "from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.order_id and a.status_active=1 and b.status_active=1 and c.shipment_date between '$startDate' and '$endDate' $cutQcPoCond group by a.cutting_qc_no";
	// echo $sqlcutQc;die();
	$cutQcRes = sql_select($sqlcutQc);
    $cutqc_data_array = array();
	foreach ($cutQcRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear = str_replace("-","_",$fyear);
			$qcyear 	= 'QC'.$nyear;
			$rjyear 	= 'RJ'.$nyear;

        	$cutqc_data_array[$fyear]['qc_pass_qty']+= $val[$qcyear];
        	$cutqc_data_array[$fyear]['qc_rej_qty'] += $val[$rjyear];
        }
	}
	// print_r($cutqc_data_array);die();
	/*==========================================================================================/
	/											gmts prod data									/
	/==========================================================================================*/
	$prodPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
    $sqlProd = "SELECT a.id";
    foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		
		$sqlProd.=", sum(case when a.production_type=1 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS CUT$fyear ";
		$sqlProd.=", sum(case when a.production_type=1 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS CUTREJ$fyear ";
		$sqlProd.=", sum(case when a.production_type=2 and a.embel_name=1 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS PRI$fyear ";
		$sqlProd.=", sum(case when a.production_type=2 and a.embel_name=2 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS EMBI$fyear ";
		$sqlProd.=", sum(case when a.production_type=2 and a.embel_name=3 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS WHI$fyear ";
		$sqlProd.=", sum(case when a.production_type=3 and a.embel_name=1 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS PRR$fyear ";
		$sqlProd.=", sum(case when a.production_type=3 and a.embel_name=2 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS EMBR$fyear ";
		$sqlProd.=", sum(case when a.production_type=3 and a.embel_name=3 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS WHR$fyear ";
		$sqlProd.=", sum(case when a.production_type=3 and a.embel_name=3 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS WHRJ$fyear ";
		$sqlProd.=", sum(case when a.production_type=3 and a.embel_name=2 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS EMBRJ$fyear ";
		$sqlProd.=", sum(case when a.production_type=3 and a.embel_name=1 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS PRRJ$fyear ";
		$sqlProd.=", sum(case when a.production_type=4 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS SWI$fyear ";
		$sqlProd.=", sum(case when a.production_type=5 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS SWO$fyear ";
		$sqlProd.=", sum(case when a.production_type=5 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS SWR$fyear ";
		$sqlProd.=", sum(case when a.production_type=8 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS FIN$fyear ";
		$sqlProd.=", sum(case when a.production_type=8 and c.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.reject_qty ELSE 0 END) AS FINR$fyear ";
	}
    $sqlProd .= "from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=a.po_break_down_id and c.shipment_date between '$startDate' and '$endDate' $prodPoCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
    // echo $sqlProd;die();
    $prodRes = sql_select($sqlProd);
    $gmts_data_array = array();
	foreach ($prodRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$cutyear 	= 'CUT'.$nyear;
			$cutrejyear = 'CUTREJ'.$nyear;
			$priyear 	= 'PRI'.$nyear;
			$prryear 	= 'PRR'.$nyear;
			$prrjyear 	= 'PRRJ'.$nyear;
			$embiyear 	= 'EMBI'.$nyear;
			$embryear 	= 'EMBR'.$nyear;
			$embrjyear 	= 'EMBRJ'.$nyear;
			$whiyear 	= 'WHI'.$nyear;
			$whryear 	= 'WHR'.$nyear;
			$whrjyear 	= 'WHRJ'.$nyear;
			$swiyear 	= 'SWI'.$nyear;
			$swoyear 	= 'SWO'.$nyear;
			$swryear 	= 'SWR'.$nyear;
			$finyear 	= 'FIN'.$nyear;
			$finryear 	= 'FINR'.$nyear;

        	$gmts_data_array[$fyear]['cut_qty'] 		+= $val[$cutyear];
        	$gmts_data_array[$fyear]['cut_rej_qty'] 	+= $val[$cutrejyear];
        	$gmts_data_array[$fyear]['print_issue_qty'] += $val[$priyear];
        	$gmts_data_array[$fyear]['print_rcv_qty'] 	+= $val[$prryear];
        	$gmts_data_array[$fyear]['print_rej_qty'] 	+= $val[$prrjyear];

        	$gmts_data_array[$fyear]['emb_issue_qty'] 	+= $val[$embiyear];
        	$gmts_data_array[$fyear]['emb_rcv_qty'] 	+= $val[$embryear];
        	$gmts_data_array[$fyear]['emb_rej_qty'] 	+= $val[$embrjyear];

        	$gmts_data_array[$fyear]['wash_issue_qty'] 	+= $val[$whiyear];
        	$gmts_data_array[$fyear]['wash_rcv_qty'] 	+= $val[$whryear];
        	$gmts_data_array[$fyear]['wash_rej_qty'] 	+= $val[$whrjyear];
        	
        	$gmts_data_array[$fyear]['input_qty'] 		+= $val[$swiyear];
        	$gmts_data_array[$fyear]['output_qty'] 		+= $val[$swoyear];
        	$gmts_data_array[$fyear]['sew_rej_qty'] 	+= $val[$swryear];
        	$gmts_data_array[$fyear]['fin_qty'] 		+= $val[$finyear];
        	$gmts_data_array[$fyear]['fin_rej_qty'] 	+= $val[$finryear];
        }
	}
	// print_r($gmts_data_array);die();
	/*==========================================================================================/
	/										Ex-factory Data 									/
	/==========================================================================================*/
	$exfactPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
	$sqlExfact = "select ";
	$r = 1;
	$add_comma = "";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		$add_comma = ($r !=1 ) ? ", " : "";
		$sqlExfact .= $add_comma." sum(case when d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN b.production_qnty ELSE 0 END) AS EX$fyear ";
		$r++;
	}
	$sqlExfact .= "from pro_ex_factory_mst a, pro_ex_factory_dtls b, pro_ex_factory_delivery_mst c,wo_po_break_down d where a.id=b.mst_id and c.id=a.delivery_mst_id and d.id=a.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.shipment_date between '$startDate' and '$endDate' $exfactPoCond";
	// echo $sqlExfact;die();
	$exfactRes = sql_select($sqlExfact);
    $exfact_data_array = array();
	foreach ($exfactRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$exyear 	= 'EX'.$nyear;

        	$exfact_data_array[$fyear] 		+= $val[$exyear];
        }
	}
	// print_r($prod_data_array);die();
	/*==========================================================================================/
	/										gmt leftover Data 									/
	/==========================================================================================*/
	$leftoverPoCond = str_replace("b.id", "b.po_break_down_id", $poCond);
	$sqlLeftover = "select ";
	$r = 1;
	$add_comma = "";
	foreach($fiscal_year_arr as $fyear=>$ydata)
	{
		$exydata=explode("_",$ydata);
		$fyear=str_replace("-","_",$fyear);
		$add_comma = ($r !=1 ) ? ", " : "";
		$sqlLeftover .= $add_comma." sum(case when d.shipment_date between '$exydata[0]' and '$exydata[1]' THEN c.production_qnty ELSE 0 END) AS LFT$fyear ";
		$r++;
	}
	$sqlLeftover .= "from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c,wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and d.id=b.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.shipment_date between '$startDate' and '$endDate' $leftoverPoCond";
	// echo $sqlLeftover;die();
	$leftoverRes = sql_select($sqlLeftover);
    $gmts_leftover_data_array = array();
	foreach ($leftoverRes as $val) 
	{
		foreach($fiscal_year_arr as $fyear=>$ydata)
		{
			$nyear=str_replace("-","_",$fyear);
			$exyear 	= 'LFT'.$nyear;

        	$gmts_leftover_data_array[$fyear] 		+= $val[$exyear];
        }
	}
	// print_r($prod_data_array);die();

	$tbl_width = 1200;
	ob_start();	
	?>
	
	<!--=====================================Total Summary Start=====================================-->
	<fieldset class="first_part" style="width:<? echo $tbl_width+30;?>px">
		<div class="report_heading">
		    <table width="<? echo $tbl_width;?>"  cellspacing="0">
		        <tr class="form_caption">
		            <td colspan="25" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$company_id]; ?></strong></td>
		        </tr>
		        <tr class="form_caption">
		            <td colspan="25" align="center"><strong style="font-size:14px">Amolnama Report <span style="color: red;font-size: 15px;font-weight: bold;"> <?=($client_id) ? ": ".$client_array[$client_id] : ": All"; ?></span></strong></td>
		        </tr>
		    </table>
		</div>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'yearly_revenue_report', '')"> -<b>Fiscal Year : <? echo $from_year; ?> To <? echo $to_year; ?></b></h3>
	    <div id="yearly_revenue_report">
			<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
			
		        <thead>
		        	<tr>
		        		<th width="340" colspan="5"></th>
		        		<th width="180" colspan="3">Process Loss%</th>
		        		<th width="300" colspan="5">Lost</th>
		        		<th width="120" colspan="2">Rejection</th>
		        		<th width="180" colspan="3">Leftover</th>
		        		<th width="120" colspan="2">Ship Status</th>
		        	</tr>
		        	<tr>
			            <th width="60">Fiscal Year</th>
			            <th width="80">Total gmt</th>
			            <th width="80">Budget Yarn</th>
			            <th width="60">Extra Yarn </th>
			            <th width="60">Unauthorised Yarn</th>

			            <th width="60">YD</th>
			            <th width="60">Dyeing</th>
			            <th width="60">AOP</th>

			            <th width="60">Yarn</th>
			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Gmts</th>

			            <th width="60">Input to Ship</th>
			            <th width="60">Yarn to Ship</th>
		            </tr>
		        </thead>
			        <tbody>   
			        <?
			        	$i=1;			        	
			        	$gr_gmts 			= 0;
			        	$gr_budget_yarn 	= 0;
			        	$gr_extra_yarn 		= 0;
			        	$gr_unautho_yarn 	= 0;

			        	$gr_los_yd 			= 0;
			        	$gr_los_dyeing 		= 0;
			        	$gr_los_aop 		= 0;

			        	$gr_lost_yarn 		= 0;
			        	$gr_lost_grey_fab 	= 0;
			        	$gr_lost_fin_fab 	= 0;
			        	$gr_lost_gmts 		= 0;
			        	$gr_lost_cutpanel	= 0;

			        	$gr_left_grey_fab 	= 0;
			        	$gr_left_fin_fab 	= 0;
			        	$gr_left_gmts 		= 0;

			        	$gr_cut_for_fab 	= 0;
			        	$gr_cut_at_cutting 	= 0;
			        	$gr_cut_at_print 	= 0;
			        	$gr_cut_at_embro 	= 0;
			        	$gr_cut_at_sewing 	= 0;

			        	$gr_cut_panel_rej 	= 0;
			        	$gr_gmt_rej 	= 0;
			        	$gr_gmt_at_finish 	= 0;

			        	$gr_input_to_ship 	= 0;
			        	$gr_yarn_to_ship 	= 0;
			        	foreach ($fiscal_year_arr as $year => $val) 
			        	{
		        			$yarn_knit_issue= $yarn_knit_data_array[2][$year];		        		
		        			$yarn_knit_issue_rtn= $yarn_knit_data_array[4][$year];
			        		$yarn_issue 	= $yarn_data_array[2][$year];		        		
			        		$yarn_issue_rtn = $yarn_data_array[4][$year];

			        		$grey_yarn_issue 	= $grey_yarn_data_array[2][$year];		        		
			        		$grey_yarn_issue_rtn = $grey_yarn_data_array[4][$year];
		        			$grey_yarn_issue_rej_rtn= $grey_yarn_rej_data_array[4][$year];
		        			$dyed_yarn_rtn_qty = $dyed_yarn_rej_rtn_array[4][$year]['rtn'];
		        			$dyed_yarn_rej_qty = $dyed_yarn_rej_rtn_array[4][$year]['rej'];

				        	$yarn_issue_rtn_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$year][2]['rtn_qty'];
				        	$yarn_rej_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$year][2]['rej_qty'];

				        	$yarn_issue_rtn_for_others = $yarn_dyeing_rtn_rej_data_array[$year][12]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$year][15]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$year][38]['rtn_qty'];
				        	$yarn_rej_for_others = $yarn_dyeing_rtn_rej_data_array[$year][12]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$year][15]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$year][38]['rej_qty'];

				        	$yarn_issue_for_dyeing = $yarn_dyeing_data_array[$year][2];
				        	$yarn_dyeing_used_qty = $yarn_dyeing_used_data_array[$year][2];
			        		$yarn_dyed_lost = $yarn_issue_for_dyeing - ($yarn_dyeing_used_qty+$yarn_issue_rtn_for_dyeing+$yarn_rej_for_dyeing);
				        	// ========== get twisting, reconning,re-waxing data ===========
				        	$yarn_issue_for_others = $yarn_dyeing_data_array[$year][12]+$yarn_dyeing_data_array[$year][15]+$yarn_dyeing_data_array[$year][38];
				        	$yarn_others_used_qty = $yarn_dyeing_used_data_array[$year][12]+$yarn_dyeing_used_data_array[$year][15]+$yarn_dyeing_used_data_array[$year][38];
				        	$yarn_others_lost = $yarn_issue_for_others - ($yarn_others_used_qty+$yarn_issue_rtn_for_others+$yarn_rej_for_others);

			        		$unAuthoYarn 	= ($grey_yarn_issue+$yarn_issue_for_dyeing+$yarn_issue_for_others - $grey_yarn_issue_rtn - $grey_yarn_issue_rej_rtn - $yarn_issue_rtn_for_others - $yarn_rej_for_others - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing) - ($yarn_req_array[$year] + $excess_yarn_array[$year]);

			        		// echo $year."**".$grey_yarn_issue."+".$yarn_issue_for_dyeing."+".$yarn_issue_for_others ."-". $grey_yarn_issue_rtn ."-". $grey_yarn_issue_rej_rtn ."-". $yarn_issue_rtn_for_others ."-". $yarn_rej_for_others ."-". $yarn_issue_rtn_for_dyeing ."-". $yarn_rej_for_dyeing.") - (".$yarn_req_array[$year] ."+". $excess_yarn_array[$year].")<br>";

			        		$yarn_used_qty 	= $yarn_used_data_array[$year];
			        		$dyed_yarn_rcv_qty 	= $dyed_yarn_rcv_array[$year];


			        		$grey_yarn_lost = $yarn_knit_issue - ($yarn_used_qty + $grey_yarn_issue_rtn + $grey_yarn_issue_rej_rtn);
			        		// echo $yarn_knit_issue."-(".$yarn_used_qty ."+". $grey_yarn_issue_rtn."+". $grey_yarn_issue_rej_rtn.")";

			        		$yarn_lost_qty 	= $grey_yarn_lost +$yarn_dyed_lost+$yarn_others_lost-($dyed_yarn_rtn_qty+$dyed_yarn_rej_qty);
			        		// echo $grey_yarn_lost."+".$yarn_dyed_lost."+".$yarn_others_lost."-(".$dyed_yarn_rtn_qty ."+". $dyed_yarn_rej_qty.")";

			        		// $yarn_dyed_loss = ($yarn_issue_for_dyeing>0) ? (($yarn_issue_for_dyeing - $yarn_dyeing_used_qty - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing)/$yarn_issue_for_dyeing)*100 : 0;

			        		// new formula
		        			$yarn_dyed_loss = ($yarn_dyeing_used_qty>0) ? (($yarn_dyeing_used_qty - $dyed_yarn_rcv_qty)/$yarn_dyeing_used_qty)*100 : 0;

			        		// echo $intRef."==(".$yarn_issue_for_dyeing ."-". $yarn_dyeing_used_qty ."-". $grey_yarn_issue_rtn.")/".$yarn_issue_for_dyeing.")*100<br>";

			        		$grey_trnsf_in 	= $transfer_in_data_array[$year][13];
			        		$fin_trnsf_in 	= $transfer_in_data_array[$year][2];
			        		$grey_trnsf_out = $transfer_out_data_array[$year][13];
			        		$fin_trnsf_out 	= $transfer_out_data_array[$year][2];

			        		$grey_fab_rcv 	= $fab_data_array[13][1][$year]['receive_qnty'];
			        		$grey_fab_rcv_rtn= $fab_data_array[13][3][$year]['issue_qnty'];
			        		$grey_fab_issue = $fab_data_array[13][2][$year]['issue_qnty'];
			        		$grey_fab_iss_rtn= $fab_data_array[13][4][$year]['receive_qnty'];

			        		//
			        		$fin_fab_rcv_without_sample 	= $fab_data_array_without_sample[2][1][$year]['receive_qnty'];
			        		$fin_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[2][3][$year]['issue_qnty'];
			        		$fin_fab_issue_without_sample 	= $fab_data_array_without_sample[2][2][$year]['issue_qnty'];
			        		$fin_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[2][4][$year]['receive_qnty'];

			        		$grey_fab_rcv_without_sample 	= $fab_data_array_without_sample[13][1][$year]['receive_qnty'];
			        		$grey_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[13][3][$year]['issue_qnty'];
			        		$grey_fab_issue_without_sample 	= $fab_data_array_without_sample[13][2][$year]['issue_qnty'];
			        		$grey_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[13][4][$year]['receive_qnty'];
			        		$grey_fab_rej_without_sample	= $fab_data_array_without_sample[13][1][$year]['rej_qty'];
			        		$grey_lft_over 	= $scrap_data_array[$year][13]['grey'];

		        			$grey_fab_lost 	= ($grey_fab_rcv_without_sample+$grey_trnsf_in+$grey_fab_iss_rtn_without_sample) -($grey_fab_issue_without_sample+$grey_fab_rcv_rtn_without_sample + $grey_lft_over + $grey_trnsf_out);

			        		// $grey_fab_lost 	= ($grey_fab_rcv+$grey_trnsf_in+$grey_fab_iss_rtn) -($grey_fab_issue+$grey_fab_rcv_rtn+ $grey_trnsf_out+ $grey_lft_over);
			        		// echo $grey_fab_lost."==".$year."==(".$grey_fab_rcv ."+". $grey_trnsf_in."+". $grey_fab_iss_rtn.") - (".$grey_fab_issue ."+". $grey_trnsf_out."+". $grey_fab_rcv_rtn."+". $grey_lft_over.")<br>";
			        		$grey_lft_over 	= $scrap_data_array[$year][13]['grey']+$grey_fab_rej_without_sample;

			        		$fin_fab_rcv 	= $fab_data_array[2][1][$year]['receive_qnty'];
			        		$fin_fab_rcv_rtn= $fab_data_array[2][3][$year]['issue_qnty'];
			        		$fin_fab_issue 	= $fab_data_array[2][2][$year]['issue_qnty'];
			        		$fin_fab_iss_rtn= $fab_data_array[2][4][$year]['receive_qnty'];
			        		$fin_lft_over 	= $scrap_data_array[$year][2]['fin'];
			        		// $all_fin_lft_over = $scrap_data_array[$year][2]['allfin'];

			        		$fin_rcv_as_rej_qty = $fab_data_array[2][1][$year]['reject_qty'];
		        			$fin_iss_as_scrap_qty = $fab_data_array[2][2][$year]['scrap_qty'];
		        			$all_fin_lft_over = $fin_rcv_as_rej_qty + $fin_iss_as_scrap_qty;

			        		// fin fab purses data 
			        		$fin_fab_purses_rcv = $finfab_purses_data_array[$year][1]['qty'];
			        		$fin_fab_purses_issue = $finfab_purses_data_array[$year][2]['qty'];
			        		$fin_fab_purses_rcv_rtn = $finfab_purses_data_array[$year][3]['qty'];
			        		$fin_fab_purses_issue_rtn = $finfab_purses_data_array[$year][4]['qty'];

			        		$fin_fab_purses_lost = ($fin_fab_purses_rcv + $fin_fab_purses_issue_rtn) - ($fin_fab_purses_issue + $fin_fab_purses_rcv_rtn);

			        		$fin_fab_lost 	= ($fin_fab_rcv+$fin_trnsf_in+$fin_fab_iss_rtn) -($fin_fab_issue+$fin_trnsf_out+ $fin_fab_rcv_rtn) ;//+ $fin_lft_over + $fin_fab_purses_lost;
			        		// echo $fin_fab_rcv ."+". $fin_trnsf_in."+". $fin_fab_iss_rtn.") - (".$fin_fab_issue ."+". $fin_trnsf_out."+". $fin_fab_rcv_rtn."<br>";//."+". $fin_lft_over

			        		$batch_qnty 	= $batch_data_array[$year];
			        		$fin_fab_rcv 	= $finfab_data_array[$year]['rcv_qty'];
			        		$fin_fab_rej 	= $finfab_data_array[$year]['rej_qty'];
			        		$dyeing_pro_los = ($batch_qnty>0) ? (($batch_qnty - ($fin_fab_rcv + $fin_fab_rej))/$batch_qnty)*100 : 0;

			        		$aop_grey_fab 	= $aop_data_array[$year]['grey'];
			        		$aop_fin_fab 	= $aop_data_array[$year]['finish'];
			        		$aop_pro_loss 	= ($aop_grey_fab>0) ? (($aop_grey_fab - $aop_fin_fab)/$aop_grey_fab)*100 : 0;

			        		// $cutqc_pass_qty = $cutqc_data_array[$year]['qc_pass_qty'];
			        		// $cutqc_rej_qty 	= $cutqc_data_array[$year]['qc_rej_qty'];

			        		$cutqc_pass_qty = $gmts_data_array[$year]['cut_qty'];
			        		$cutqc_rej_qty 	= $gmts_data_array[$year]['cut_rej_qty'];

			        		$wash_issue_qty = $gmts_data_array[$year]['wash_issue_qty'];
			        		$wash_rcv_qty 	= $gmts_data_array[$year]['wash_rcv_qty'];
			        		$wash_rej_qty 	= $gmts_data_array[$year]['wash_rej_qty'];
			        		$wash_lost_qty 	= $wash_issue_qty - $wash_rcv_qty - $wash_rej_qty;
			        		$gmt_at_wash 	= $wash_issue_qty - $wash_rcv_qty;

			        		$emb_issue_qty 	= $gmts_data_array[$year]['emb_issue_qty'];
			        		$emb_rcv_qty 	= $gmts_data_array[$year]['emb_rcv_qty'];
			        		$emb_rej_qty 	= $gmts_data_array[$year]['emb_rej_qty'];
			        		$emb_lost_qty  	= $emb_issue_qty - $emb_rcv_qty - $emb_rej_qty;

			        		$print_issue_qty= $gmts_data_array[$year]['print_issue_qty'];
			        		$print_rcv_qty 	= $gmts_data_array[$year]['print_rcv_qty'];
			        		$print_rej_qty 	= $gmts_data_array[$year]['print_rej_qty'];
			        		$print_lost_qty = $print_issue_qty - $print_rcv_qty - $print_rej_qty;

			        		$sew_in_qty 	= $gmts_data_array[$year]['input_qty'];
			        		$sew_out_qty 	= $gmts_data_array[$year]['output_qty'];
			        		$sew_rej_qty 	= $gmts_data_array[$year]['sew_rej_qty'];
			        		$ok_sew_qty 	= $sew_out_qty - $sew_rej_qty;
		        			$sewing_lost 	= $sew_in_qty - $sew_out_qty - $sew_rej_qty;
		        			$fin_rej_qty 	= $gmts_data_array[$year]['fin_rej_qty'];

		        			$cutting_lost 	= ($cutqc_pass_qty - $print_issue_qty) + (($print_rcv_qty+$print_rej_qty) - $emb_issue_qty) + (($emb_rcv_qty+ $emb_rej_qty) - $sew_in_qty);
		        			$cutpanel_lost = $cutting_lost + $print_lost_qty + $sewing_lost + $emb_lost_qty;

		        			$cutpanel_reject = $cutqc_rej_qty + $print_rej_qty + $emb_rej_qty;

			        		$cut_at_cutting = $cutqc_pass_qty - ($print_lost_qty + $print_rej_qty + $emb_lost_qty + $emb_rej_qty+$sew_in_qty);
			        		$cut_at_sewing 	= $sew_in_qty - $sew_out_qty;
			        		$cut_at_embro 	= $emb_issue_qty - $emb_rcv_qty;
			        		$cut_at_print 	= $print_issue_qty - $print_rcv_qty;

			        		$ex_factory_qty = $exfact_data_array[$year];
			        		$gmt_lftovr_qty = $gmts_leftover_data_array[$year];
			        		$gmt_at_finish  = ($ok_sew_qty - ($gmt_lftovr_qty + $ex_factory_qty));

			        		$lost_gmts_qty 	= ($sew_out_qty + $sew_rej_qty) - ($gmt_lftovr_qty + $ex_factory_qty);
		        			$gmts_reject 	= $sew_rej_qty + $wash_rej_qty + $fin_rej_qty;

			        		$input_to_ship  = ($sew_in_qty>0) ? ($ex_factory_qty / $sew_in_qty)*100 : 0;

			        		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
				        	?>     
					        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					            <td>
					            	<a href="javascript:void()" onclick="report_generate_by_year('<? echo $year;?>','<?=$client_id;?>')">
						            	<? echo $year;?>
						            </a>
						        </td>
					            <td align="right"><? echo number_format($gmt_array[$year],0);?></td>
					            <td align="right"><? echo number_format($yarn_req_array[$year],0); ?></td>
					            <td align="right"><? echo number_format($excess_yarn_array[$year],0); ?></td>
					            <td align="right"><? echo number_format($unAuthoYarn,0); ?></td>

					            <td align="right"><? echo number_format($yarn_dyed_loss,2); ?></td>
					            <td align="right"><? echo number_format($dyeing_pro_los,2); ?></td>
					            <td align="right"><? echo number_format($aop_pro_loss,2); ?></td>

					            <td align="right"><? echo number_format($yarn_lost_qty,0); ?></td>
					            <td align="right"><? echo number_format($grey_fab_lost,0); ?></td>
					            <td align="right"><? echo number_format($fin_fab_lost,0); ?></td>
					            <td align="right" title="At Cutting=<? echo $cutting_lost;?>&#13;At Printing=<? echo $print_lost_qty;?>&#13;At Embroidery=<? echo $emb_lost_qty;?>&#13;At Sewing=<? echo $sewing_lost;?>">
					            	<? echo number_format($cutpanel_lost,0); ?>
					            </td>
					            <td align="right" title="At Wash = <? echo $wash_lost_qty;?>"><? echo number_format($lost_gmts_qty,0); ?></td>

					            <td align="right" title="At Cutting=<? echo $cutqc_rej_qty;?>&#13;At Printing=<? echo $print_rej_qty;?>&#13;At Embroidery=<? echo $emb_rej_qty;?>">
					            	<? echo number_format($cutpanel_reject,0); ?>
					            </td>
					            <td align="right" title="At Sewing=<? echo $sew_rej_qty;?>&#13;At Wash=<? echo $wash_rej_qty;?>&#13;At Finish=<? echo $fin_rej_qty;?>">
					            	<? echo number_format($gmts_reject,0); ?>
					            </td>

					            <td align="right"><? echo number_format($grey_lft_over,0); ?></td>
					            <td align="right"><? echo number_format($all_fin_lft_over,0); ?></td>
					            <td align="right"><? echo number_format($gmt_lftovr_qty,0); ?></td>

					            <td align="right"><? echo number_format($input_to_ship,2); ?></td>
					            <td align="right"><? echo number_format($a,2); ?></td>
					        </tr>
					        <?
					        $i++;
					        $gr_gmts 			+= $gmt_array[$year];
				        	$gr_budget_yarn 	+= $yarn_req_array[$year];
				        	$gr_extra_yarn 		+= $excess_yarn_array[$year];
				        	$gr_unautho_yarn 	+= $unAuthoYarn;

				        	$gr_los_yd 			+= $a;
				        	$gr_los_dyeing 		+= $dyeing_pro_los;
				        	$gr_los_aop 		+= $aop_pro_loss;

				        	$gr_lost_yarn 		+= $yarn_lost_qty;
				        	$gr_lost_grey_fab 	+= $grey_fab_lost;
				        	$gr_lost_fin_fab 	+= $fin_fab_lost;
				        	$gr_lost_gmts 		+= $lost_gmts_qty;
				        	$gr_lost_cutpanel 	+= $cutpanel_lost;

				        	$gr_left_grey_fab 	+= $grey_lft_over;
				        	$gr_left_fin_fab 	+= $all_fin_lft_over;
				        	$gr_left_gmts 		+= $gmt_lftovr_qty;

				        	$gr_cut_for_fab 	+= $cutqc_rej_qty;
				        	$gr_cut_at_cutting 	+= $cut_at_cutting;
				        	$gr_cut_at_print 	+= $cut_at_print;
				        	$gr_cut_at_embro 	+= $cut_at_embro;
				        	$gr_cut_at_sewing 	+= $cut_at_sewing;

				        	$gr_cut_panel_rej 	+= $cutpanel_reject;
				        	$gr_gmt_rej 		+= $gmts_reject;
				        	$gr_gmt_at_finish 	+= $gmt_at_finish;

				        	$gr_input_to_ship 	+= $input_to_ship;
				        	$gr_yarn_to_ship 	+= $a;
					    }
					    ?>
			        </tbody>
		        <tfoot>
		            <th align="right">Total</th>
		            <th><? echo number_format($gr_gmts,0); ?></th>
		            <th><? echo number_format($gr_budget_yarn,0); ?></th>
		            <th><? echo number_format($gr_extra_yarn,0); ?></th>
		            <th><? echo number_format($gr_unautho_yarn,0); ?></th>

		            <th><? //echo number_format($gr_los_yd,0); ?></th>
		            <th><? //echo number_format($gr_los_dyeing,2); ?></th>
		            <th><? //echo number_format($gr_los_aop,2); ?></th>

		            <th><? echo number_format($gr_lost_yarn,0); ?></th>
		            <th><? echo number_format($gr_lost_grey_fab,0); ?></th>
		            <th><? echo number_format($gr_lost_fin_fab,0); ?></th>
		            <th><? echo number_format($gr_lost_cutpanel,0); ?></th>
		            <th><? echo number_format($gr_lost_gmts,0); ?></th>

		            <th><? echo number_format($gr_cut_panel_rej,0); ?></th>
		            <th><? echo number_format($gr_gmt_rej,0); ?></th>


		            <th><? echo number_format($gr_left_grey_fab,0); ?></th>
		            <th><? echo number_format($gr_left_fin_fab,0); ?></th>
		            <th><? echo number_format($gr_left_gmts,0); ?></th>

		            <th><? //echo number_format($gr_input_to_ship,2); ?></th>
		            <th><? //echo number_format($gr_yarn_to_ship,2); ?></th>
		        </tfoot>
		    </table>     
	    </div>  
	</fieldset>  
			
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

if($action=="report_generate_by_year")
{
	$company_id 	= str_replace("'","",$cbo_company_id);
	$location_id 	= str_replace("'","",$cbo_location_id);
	$client_id 	= str_replace("'","",$cbo_client);
	$order_status	= str_replace("'","",$cbo_order_status);
	$year 	= str_replace("'","",$year);

	$sqlCond = "";
	$sqlCond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
	$sqlCond .= ($location_id !=0) ? " and a.location_name=$location_id" : "";
	$sqlCond .= ($client_id != 0) ? " and a.client_id=$client_id" : "";
	if($order_status !=0)
	{
		$sqlCond .= ($order_status == 1) ? " and b.shiping_status in(1,2)" : " and b.shiping_status in(3)";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$client_array = return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$company_id' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name", "id", "buyer_name");
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$fiscal_month_arr = array();
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
				$fiscal_month_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscal_month_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscal_month_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/	
	$sql_gmt="SELECT a.JOB_NO,a.BUYER_NAME,a.TOTAL_SET_QNTY,b.id as PO_ID,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(b.po_quantity*a.total_set_qnty) AS QTY
	 from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id  $sqlCond and b.shipment_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no,a.buyer_name,a.total_set_qnty,b.id,b.shipment_date";
	// echo $sql_gmt;die();
	$sql_gmt_res = sql_select($sql_gmt);
	if(!count($sql_gmt_res))
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}

	$po_id_array = array();
	$gmt_array = array();
	$buyer_data_array = array();
	foreach ($sql_gmt_res as $val) 
	{	
		$gmt_array[$val['MONTH_YEAR']] += $val['QTY'];			
		$buyer_data_array[$buyer_arr[$val['BUYER_NAME']]]['gmts_qty'] += $val['QTY'];			
		$buyer_data_array[$buyer_arr[$val['BUYER_NAME']]]['buyer_id'] = $val['BUYER_NAME'];			
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];	
	}
	// print_r($buyer_data_array);die();
	unset($sql_gmt_res);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	/*==========================================================================================/
	/								yarn req qty from budget									/
	/==========================================================================================*/
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);

    $sql = "SELECT a.job_no AS JOB_NO,a.BUYER_NAME ,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR,b.grouping as INT_REF,b.id AS id,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS country_id,c.color_number_id AS color_number_id,c.size_number_id AS size_number_id,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,c.country_ship_date AS country_ship_date,d.id AS pre_cost_dtls_id,d.fab_nature_id AS fab_nature_id,d.construction AS construction, d.gsm_weight AS gsm_weight,e.cons AS cons,e.requirment AS REQUIRMENT,f.id AS yarn_id,f.count_id AS count_id,f.copm_one_id AS copm_one_id,f.percent_one AS percent_one,f.type_id AS type_id,f.color AS color,f.cons_ratio AS CONS_RATIO,f.cons_qnty AS cons_qnty,f.avg_cons_qnty AS avg_cons_qnty,f.rate AS rate,f.amount AS amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_id $poCond and b.shipment_date between '$startDate' and '$endDate'";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$yarn_req_array = array();
	$buyer_yarn_req_array = array();
	foreach ($sql_res as $key => $row) 
	{		
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;

        $gmtsitemRatio 	= $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio 		= $row['CONS_RATIO'];
        $requirment 	= $row['REQUIRMENT'];
        $consQnty 		= $requirment*$consRatio/100;
        $reqQty 		= ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);	
        // echo $row['JOB_NO']."==".$reqQty 		."= (".$row[$plan_cut_year]."/".$gmtsitemRatio.")*(".$consQnty."/".$pcs_value.")<br>";
		
		($reqQty>0)?$yarn_req_array[$row['MONTH_YEAR']] += $reqQty:0;
		($reqQty>0)?$buyer_yarn_req_array[$row['BUYER_NAME']] += $reqQty:0;
		
	}
	unset($sql_res);
	// print_r($yarn_req_array);die();
	/*==========================================================================================/
	/									get req. and program id									/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sql_plan = "SELECT c.requisition_no as REQUISITION_ID, c.knit_id as  PROGRAM_ID from wo_booking_mst a, ppl_planning_entry_plan_dtls b, ppl_yarn_requisition_entry c where a.booking_no=b.booking_no and b.dtls_id=c.knit_id and a.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 $bookingPoCond";
	// echo $sql_plan;
	$plan_res = sql_select($sql_plan);
	$program_id_array = array();
	$requisition_id_array = array();
	foreach ($plan_res as $val) 
	{
		$program_id_array[$val['PROGRAM_ID']] = $val['PROGRAM_ID'];
		$requisition_id_array[$val['REQUISITION_ID']] = $val['REQUISITION_ID'];
	}
	$program_ids = implode(",", $program_id_array);
	$requisition_ids = implode(",", $requisition_id_array);
	// =======================================================
	$program_id_list_arr=array_chunk($program_id_array,999);
	$programCond = " and ";
	$p=1;
	foreach($program_id_list_arr as $programids)
    {
    	if($p==1) 
		{
			$programCond .="  ( a.booking_id in(".implode(',',$programids).")"; 
		}
        else
        {
          $programCond .=" or a.booking_id in(".implode(',',$programids).")";
      	}
        $p++;
    }
    $programCond .=")";
	// =======================================================
	$requisition_id_list_arr=array_chunk($requisition_id_array,999);
	$requisitionCond = " and ";
	$p=1;
	foreach($requisition_id_list_arr as $requisitionids)
    {
    	if($p==1) 
		{
			$requisitionCond .="  ( a.requisition_no in(".implode(',',$requisitionids).")"; 
		}
        else
        {
          $requisitionCond .=" or a.requisition_no in(".implode(',',$requisitionids).")";
      	}
        $p++;
    }
    $requisitionCond .=")";

	/*==========================================================================================/
	/										excess yarn											/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlExcessYarn = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(b.grey_fab_qnty) AS GREY_FAB_QNTY FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $bookingPoCond group by c.buyer_name,d.shipment_date order by d.shipment_date";
    // echo $sqlExcessYarn;die();
    $exYarnRes = sql_select($sqlExcessYarn);
    $excess_yarn_array = array();
    $buyer_excess_yarn_array = array();
    foreach ($exYarnRes as $val) 
    {			
        $excess_yarn_array[$val['MONTH_YEAR']] += $val['GREY_FAB_QNTY'];       
        $buyer_excess_yarn_array[$val['BUYER_NAME']] += $val['GREY_FAB_QNTY'];       
    }
    unset($exYarnRes);
    /*==========================================================================================/
	/										yarn receive										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnRcv = "SELECT a.TRANSACTION_TYPE,e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond";
	// echo $sqlYArnRcv;die();
	$yarnRcvRes = sql_select($sqlYArnRcv);
	$yarn_data_array = array();
	$buyer_yarn_data_array = array();
	foreach ($yarnRcvRes as $val) 
	{
        $yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_data_array);die();
    /*==========================================================================================/
	/										yarn issue											/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,c.ISSUE_PURPOSE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond $requisitionCond";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_knit_data_array = array();
	$buyer_yarn_knit_data_array = array();
	foreach ($yarnISsueRes as $val) 
	{
		if($val['ISSUE_PURPOSE']==1)
		{
        	$yarn_knit_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        	$buyer_yarn_knit_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        }
        $yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	//echo "<pre>"; print_r($yarn_data_array);die();



    /*==========================================================================================/
	/						yarn issue for dyeing,twisting,recon....							/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT c.ID,e.BUYER_NAME,c.ISSUE_PURPOSE,a.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond and c.issue_purpose in(2,12,15,38)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_dyeing_data_array = array();
	$buyer_yarn_dyeing_data_array = array();
	$issue_id_array = array();
	$issue_purpose_array = array();
	foreach ($yarnISsueRes as $val) 
	{
        $yarn_dyeing_data_array[$val['MONTH_YEAR']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $buyer_yarn_dyeing_data_array[$val['BUYER_NAME']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $issue_id_array[$val['ID']] = $val['ID'];
        $issue_purpose_array[$val['ID']] = $val['ISSUE_PURPOSE'];
	}
	$issueIds = implode(",", $issue_id_array);

	// =======================================================
	$issue_id_list_arr=array_chunk($issue_id_array,999);
	$issueIdCond = " and ";
	$p=1;
	foreach($issue_id_list_arr as $issueids)
    {
    	if($p==1) 
		{
			$issueIdCond .="  ( a.issue_id in(".implode(',',$issueids).")"; 
		}
        else
        {
          $issueIdCond .=" or a.issue_id in(".implode(',',$issueids).")";
      	}
        $p++;
    }
    $issueIdCond .=")";
	// print_r($yarn_dyeing_data_array);die(); 

	/*==========================================================================================/
	/						get yarn used qty for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,c.BUYER_NAME,a.RECEIVE_PURPOSE,b.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.GREY_QUANTITY as USED_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2,12,15,38) and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_used_data_array = array();
	$buyer_yarn_dyeing_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_used_data_array[$val['MONTH_YEAR']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
		$buyer_yarn_dyeing_used_data_array[$val['BUYER_NAME']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
	}
	// print_r($yarn_dyeing_used_data_array);die();

	/*==========================================================================================/
	/					get yarn rej & rtn qty for dyeing,twisting,recon....					/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.ISSUE_ID,a.RECV_NUMBER,c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.CONS_QUANTITY as RTN_QTY,b.cons_reject_qnty as REJ_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id  and c.id=d.job_id and b.item_category=1  and a.entry_form=9 and b.transaction_type in(4) and e.trans_id=b.id and d.id=e.po_breakdown_id and b.transaction_type=e.trans_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose is null and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond  $issueIdCond";//and a.booking_id in($yd_wo_ids)
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_rtn_rej_data_array = array();
	$buyer_yarn_dyeing_rtn_rej_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_rtn_rej_data_array[$val['MONTH_YEAR']][$issue_purpose_array[$val['ISSUE_ID']]]['rtn_qty'] += $val['RTN_QTY'];
		$yarn_dyeing_rtn_rej_data_array[$val['MONTH_YEAR']][$issue_purpose_array[$val['ISSUE_ID']]]['rej_qty'] += $val['REJ_QTY'];

		$buyer_yarn_dyeing_rtn_rej_data_array[$val['BUYER_NAME']][$issue_purpose_array[$val['ISSUE_ID']]]['rtn_qty'] += $val['RTN_QTY'];
		$buyer_yarn_dyeing_rtn_rej_data_array[$val['BUYER_NAME']][$issue_purpose_array[$val['ISSUE_ID']]]['rej_qty'] += $val['REJ_QTY'];				
	}
	// print_r($yarn_dyeing_rtn_rej_data_array);die();

	/*==========================================================================================/
	/										get dyed yarn rcv qty								/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,c.BUYER_NAME,a.RECEIVE_PURPOSE,b.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, f.QUANTITY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e,order_wise_pro_details f where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and f.trans_id=b.id and f.po_breakdown_id=d.id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$dyed_yarn_rcv_array = array();
	$buyer_dyed_yarn_rcv_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$dyed_yarn_rcv_array[$val['MONTH_YEAR']] += $val['QUANTITY'];
		$buyer_dyed_yarn_rcv_array[$val['BUYER_NAME']] += $val['QUANTITY'];
	}
	// print_r($yarn_dyeing_used_data_array);die();

    /*==========================================================================================/
	/									get yarn used qty										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnUsd = "SELECT e.BUYER_NAME,to_char(f.shipment_date,'MON-YYYY') as MONTH_YEAR, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master a, order_wise_pro_details b,inv_transaction d,wo_po_details_master e,wo_po_break_down f where d.id=b.trans_id and d.prod_id=b.prod_id and e.id=f.job_id and f.id=b.po_breakdown_id and d.item_category=13 and b.entry_form=2 and d.transaction_type in(1) and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.shipment_date between '$startDate' and '$endDate' $yarnPoCond $programCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_used_data_array[$val['MONTH_YEAR']] += $val['QUANTITY'] + $val['RETURNABLE_QNTY'];
        $buyer_yarn_used_data_array[$val['BUYER_NAME']] += $val['QUANTITY'] + $val['RETURNABLE_QNTY'];
	}
	// print_r($yarn_used_data_array);die();
    /*==========================================================================================/
	/										grey yarn issue										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssue = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond $requisitionCond";
	// echo $sqlGreyYArnIssue;die();
	$greyYarnISsueRes = sql_select($sqlGreyYArnIssue);
	$grey_yarn_data_array = array();
	$buyer_grey_yarn_data_array = array();
	foreach ($greyYarnISsueRes as $val) 
	{
        $grey_yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_grey_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
    }
    /*==========================================================================================/
	/										grey yarn issue	rtn									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$reqCond = str_replace("a.requisition_no", "c.booking_id", $requisitionCond);
	$sqlGreyYArnIssueRtn = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,f.DYED_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY,b.REJECT_QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2,1) and f.item_category_id=1 and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond $reqCond";
	// echo $sqlGreyYArnIssueRtn;die();
	$greyYarnIssueRtnRes = sql_select($sqlGreyYArnIssueRtn);
	$grey_yarn_rej_data_array = array();
	$buyer_grey_yarn_rej_data_array = array();
	$dyed_yarn_rej_rtn_array = array();
	$buyer_dyed_yarn_rej_rtn_array = array();
	foreach ($greyYarnIssueRtnRes as $val) 
	{
		if($val['DYED_TYPE']==1)
		{
			$dyed_yarn_rej_rtn_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['rtn'] += $val['QTY'];
			$buyer_dyed_yarn_rej_rtn_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['rtn'] += $val['QTY'];
			$dyed_yarn_rej_rtn_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['rej'] += $val['REJECT_QTY'];
			$buyer_dyed_yarn_rej_rtn_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['rej'] += $val['REJECT_QTY'];
		}
		else
		{
	        $grey_yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	        $buyer_grey_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];

	        $grey_yarn_rej_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
	        $buyer_grey_yarn_rej_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
	    }
    }
    // echo "<pre>"; print_r($grey_yarn_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	rcv									/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME, a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(1,4) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array = array();
	$buyer_fab_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['reject_qty'] += $val['RETURNABLE_QNTY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['reject_qty'] += $val['RETURNABLE_QNTY'];
		}
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/								grey and fin fabric	issue									/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR,  b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and a.transaction_type in(2,3) and f.booking_no=g.booking_no and g.booking_type=1 and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";// and c.issue_purpose != 31
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $fab_data_array = array();
	$scrap_data_array = array();
	$buyer_scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		if($val['ISSUE_PURPOSE']==31)
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['scrap_qty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['scrap_qty'] += $val['QUANTITY'];
		}
		
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/						grey and fin fabric	rcv	without sample								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$progCond = str_replace("a.booking_id", "c.booking_id", $programCond);
	$sqlgreyfab = "SELECT c.recv_number,d.BUYER_NAME, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR, b.QUANTITY,b.returnable_qnty as REJ_QTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=c.booking_id and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond ";//$progCond
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array_without_sample = array();
	$buyer_fab_data_array_without_sample = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
		$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['rej_qty'] += $val['REJ_QTY'];
		$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
		$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['rej_qty'] += $val['REJ_QTY'];
	}
	// print_r($fab_data_array_without_sample);die();
    /*==========================================================================================/
	/						grey and fin fabric	issue without sample							/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$progCond = str_replace("a.booking_id", "a.requisition_no", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=a.requisition_no and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond  and c.issue_purpose != 31";//and c.issue_purpose != 31 $progCond
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];		
	}
	// print_r($fab_data_array_without_sample);die();
	/*==========================================================================================/
	/										batch data											/
	/==========================================================================================*/
	$batchPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sqlBatch = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.batch_qnty AS BATCH_QNTY from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.batch_against in(1) and d.shipment_date between '$startDate' and '$endDate' $batchPoCond";
	// echo $sqlBatch;die();
	$batchRes = sql_select($sqlBatch);
	$batch_data_array = array();
	$buyer_batch_data_array = array();
	foreach ($batchRes as $val) 
	{
        $batch_data_array[$val['MONTH_YEAR']] += $val['BATCH_QNTY'];
        $buyer_batch_data_array[$val['BUYER_NAME']] += $val['BATCH_QNTY'];
	}
	// print_r($batch_data_array);die();
	/*==========================================================================================/
	/										FF Rcv data											/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, c.quantity AS RCV_QTY, c.returnable_qnty AS REJ_QTY 
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,pro_batch_create_mst f where a.id=b.mst_id and b.id=c.trans_id and d.id=c.po_breakdown_id and e.id=d.job_id and b.transaction_type=1 $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 and f.id=b.pi_wo_batch_no and f.batch_against=1 and f.status_active=1 and d.shipment_date between '$startDate' and '$endDate'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_data_array = array();
    $buyer_finfab_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_data_array[$val['MONTH_YEAR']]['rcv_qty'] += $val['RCV_QTY'];
        $finfab_data_array[$val['MONTH_YEAR']]['rej_qty'] += $val['REJ_QTY'];

        $buyer_finfab_data_array[$val['BUYER_NAME']]['rcv_qty'] += $val['RCV_QTY'];
        $buyer_finfab_data_array[$val['BUYER_NAME']]['rej_qty'] += $val['REJ_QTY'];
	}
	// print_r($finfab_data_array);die();

	/*==========================================================================================/
	/										FF Purses Rcv data									/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(1,4) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(37,52) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and d.shipment_date between '$startDate' and '$endDate'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_purses_data_array = array();
    $buyer_finfab_purses_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
        $buyer_finfab_purses_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();

	/*==========================================================================================/
	/										FF Purses Issue data								/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_issue_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(2,3) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(18,46) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and d.shipment_date between '$startDate' and '$endDate'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
        $buyer_finfab_purses_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();
	/*==========================================================================================/
	/									transfer data											/
	/==========================================================================================*/
	$transPoCond = str_replace("b.id", "a.to_order_id", $poCond);
	$transPoCond2 = str_replace("b.id", "a.from_order_id", $poCond);
	$sqlTrnsfrIn = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(5) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.to_order_id and d.shipment_date between '$startDate' and '$endDate' $transPoCond";
	// echo $sqlTrnsfr;die();
	$trnsfrResIn = sql_select($sqlTrnsfrIn);
	$transfer_in_data_array = array();
	$buyer_transfer_in_data_array = array();
	foreach ($trnsfrResIn as $val) 
	{
        $transfer_in_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
        $buyer_transfer_in_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_in_data_array);die();
	//======================================================================================
	$sqlTrnsfrOut = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(6) and e.po_breakdown_id=d.id and e.dtls_id=b.id and e.trans_type in(6) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.from_order_id and d.shipment_date between '$startDate' and '$endDate' $transPoCond2";
	// echo $sqlTrnsfrOut;die();
	$trnsfrResOut = sql_select($sqlTrnsfrOut);
	$transfer_out_data_array = array();
	$buyer_transfer_out_data_array = array();
	foreach ($trnsfrResOut as $val) 
	{
        $transfer_out_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
        $buyer_transfer_out_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_out_data_array);die();
	/*==========================================================================================/
	/										scrap data											/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(13) and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond and c.issue_purpose = 31";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$scrap_data_array = array();
	$buyer_scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];		
		$buyer_scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];		
	}
	// ======================================= FOR FIN FAB ====================================================	
	$greyfabPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlgreyfab = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY_ID,a.RECEIVE_BASIS,b.RECEIVE_QNTY  from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, wo_po_details_master c,wo_po_break_down d,pro_batch_create_mst e,pro_batch_create_dtls f where a.id=b.mst_id and b.lot=e.batch_no and a.item_category_id in(2) and e.id=f.mst_id and e.batch_against=1 and c.id=d.job_id and d.id=f.po_id AND b.body_part_id=f.body_part_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_BASIS'] !=1)
		{
			$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];
			$buyer_scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];
		}
		$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];
		$buyer_scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];	
	}
	/*==========================================================================================/
	/											AOP Data 										/
	/==========================================================================================*/
	$aopPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlAop = "SELECT d.BUYER_NAME,to_char(c.shipment_date,'MON-YYYY') as MONTH_YEAR,b.GREY_USED,b.BATCH_ISSUE_QTY from inv_receive_mas_batchroll a, pro_grey_batch_dtls b,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and c.id=b.order_id and d.id=c.job_id and a.status_active=1 and b.status_active=1 and a.entry_form=92 and c.shipment_date between '$startDate' and '$endDate' $aopPoCond";
	// echo $sqlAop;die();
	$aopRes = sql_select($sqlAop);
    $aop_data_array = array();
    $buyer_aop_data_array = array();
	foreach ($aopRes as $val) 
	{
    	$aop_data_array[$val['MONTH_YEAR']]['grey'] 		+= $val['GREY_USED'];
    	$aop_data_array[$val['MONTH_YEAR']]['finish'] 		+= $val['BATCH_ISSUE_QTY'];

    	$buyer_aop_data_array[$val['BUYER_NAME']]['grey'] 	+= $val['GREY_USED'];
    	$buyer_aop_data_array[$val['BUYER_NAME']]['finish'] 	+= $val['BATCH_ISSUE_QTY'];
	}
	// print_r($aop_data_array);die();



	/*==========================================================================================/
	/										cutting qc Data 									/
	/==========================================================================================*/
	$cutQcPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlcutQc = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR , b.QC_PASS_QTY , b.REJECT_QTY from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.order_id and a.status_active=1 and b.status_active=1 and d.shipment_date between '$startDate' and '$endDate' $cutQcPoCond";
	// echo $sqlcutQc;die();
	$cutQcRes = sql_select($sqlcutQc);
    $cutqc_data_array = array();
    $buyer_cutqc_data_array = array();
	foreach ($cutQcRes as $val) 
	{
        $cutqc_data_array[$val['MONTH_YEAR']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $cutqc_data_array[$val['MONTH_YEAR']]['qc_rej_qty'] += $val['REJECT_QTY'];

        $buyer_cutqc_data_array[$val['BUYER_NAME']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $buyer_cutqc_data_array[$val['BUYER_NAME']]['qc_rej_qty'] += $val['REJECT_QTY'];
        
	}
	// print_r($cutqc_data_array);die();

	/*==========================================================================================/
	/											gmts prod data									/
	/==========================================================================================*/
	$prodPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
    $sqlProd = "SELECT c.BUYER_NAME, to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,
		
		 sum(case when a.production_type=1 THEN b.production_qnty ELSE 0 END) AS CUT_QTY
		 ,sum(case when a.production_type=1  THEN b.reject_qty ELSE 0 END) AS CUT_REJ_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS PRI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS EMBI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS WHI_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS PRR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS EMBR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS WHR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS WHRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS EMBRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS PRRJ_QTY
		 ,sum(case when a.production_type=4 THEN b.production_qnty ELSE 0 END) AS SWI_QTY
		 ,sum(case when a.production_type=5 THEN b.production_qnty ELSE 0 END) AS SWO_QTY
		 ,sum(case when a.production_type=5 THEN b.reject_qty ELSE 0 END) AS SWR_QTY
		 ,sum(case when a.production_type=8 THEN b.production_qnty ELSE 0 END) AS FIN_QTY
		 ,sum(case when a.production_type=8  THEN b.reject_qty ELSE 0 END) AS FINR_QTY
	from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=a.po_break_down_id and d.shipment_date between '$startDate' and '$endDate' $prodPoCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by d.shipment_date,c.buyer_name";
    // echo $sqlProd;die();
    $prodRes = sql_select($sqlProd);
    $gmts_data_array = array();
    $buyer_gmts_data_array = array();
    $buyer_month_gmts_data_array = array();
	foreach ($prodRes as $val) 
	{
    	$gmts_data_array[$val['MONTH_YEAR']]['cut_qty'] 		+= $val['CUT_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['cut_rej_qty'] 	+= $val['CUT_REJ_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_issue_qty'] += $val['PRI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$gmts_data_array[$val['MONTH_YEAR']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['emb_rcv_qty'] 	+= $val['EMBR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['emb_rej_qty'] 	+= $val['EMBRJ_QTY'];

    	$gmts_data_array[$val['MONTH_YEAR']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$gmts_data_array[$val['MONTH_YEAR']]['input_qty'] 		+= $val['SWI_QTY'];
    	if($val['SWO_QTY']>0)
    	{
	    	$gmts_data_array[$val['MONTH_YEAR']]['output_qty'] 		+= $val['SWO_QTY'];
	    }
    	$gmts_data_array[$val['MONTH_YEAR']]['sew_rej_qty'] 	+= $val['SWR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['fin_qty'] 		+= $val['FIN_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['fin_rej_qty'] 	+= $val['FINR_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['cut_qty'] 			+= $val['CUT_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['cut_rej_qty'] 		+= $val['CUT_REJ_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_issue_qty'] 	+= $val['PRI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_rcv_qty'] 		+= $val['EMBR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_rej_qty'] 		+= $val['EMBRJ_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['input_qty'] 		+= $val['SWI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['output_qty'] 		+= $val['SWO_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['sew_rej_qty'] 		+= $val['SWR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['fin_qty'] 			+= $val['FIN_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['fin_rej_qty'] 		+= $val['FINR_QTY'];
    	$buyer_month_gmts_data_array[$val['MONTH_YEAR']][$val['BUYER_NAME']]['output_qty'] 			+= $val['SWO_QTY'];
	}
	// echo "<pre>";print_r($buyer_month_gmts_data_array);die();
	/*==========================================================================================/
	/										Ex-factory Data 									/
	/==========================================================================================*/
	$exfactPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
	$sqlExfact = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.production_qnty AS EX_QTY from pro_ex_factory_mst a, pro_ex_factory_dtls b, pro_ex_factory_delivery_mst c,wo_po_break_down d,wo_po_details_master e where a.id=b.mst_id and d.id=a.po_break_down_id and c.id=a.delivery_mst_id and e.id=d.job_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.shipment_date between '$startDate' and '$endDate' $exfactPoCond";
	 //echo $sqlExfact;die();
	$exfactRes = sql_select($sqlExfact);
    $exfact_data_array = array();
    $buyer_exfact_data_array = array();
	foreach ($exfactRes as $val) 
	{
	    $exfact_data_array[$val['MONTH_YEAR']] 			+= $val['EX_QTY'];	   
        $buyer_exfact_data_array[$val['BUYER_NAME']] 	+= $val['EX_QTY'];
	}
	// print_r($prod_data_array);die();
	/*==========================================================================================/
	/										gmt leftover Data 									/
	/==========================================================================================*/
	$leftoverPoCond = str_replace("b.id", "b.po_break_down_id", $poCond);
	$sqlLeftover = "SELECT a.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, c.PRODUCTION_QNTY from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c,wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and d.id=b.po_break_down_id and a.id=c.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.shipment_date between '$startDate' and '$endDate' $leftoverPoCond";
	// echo $sqlLeftover;die();
	$leftoverRes = sql_select($sqlLeftover);
    $gmts_leftover_data_array = array();
    $buyer_gmts_leftover_data_array = array();
	foreach ($leftoverRes as $val) 
	{
	    $gmts_leftover_data_array[$val['MONTH_YEAR']] 			+= $val['PRODUCTION_QNTY'];	    
        $buyer_gmts_leftover_data_array[$val['BUYER_NAME']] 	+= $val['PRODUCTION_QNTY'];
	}
	// print_r($prod_data_array);die();


	$tbl_width = 1200;
	ob_start();	
	?>
	<!-- =======================================================================================/
	/										month summary report								/
	/======================================================================================== --> 
    <fieldset class="first_part" style="width:<? echo $tbl_width+30;?>px;margin:15px 0;">
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Month Summary : <? echo $year; ?></b><span style="color: red;font-size: 15px;font-weight: bold;"> <?=($client_id) ? ": ".$client_array[$client_id] : ": All"; ?></span></h3>
	    <div id="monthly_revenue_report">
			<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		
	        <thead>
		        	<tr>
		        		<th width="340" colspan="5"></th>
		        		<th width="180" colspan="3">Process Loss%</th>
		        		<th width="300" colspan="5">Lost</th>
		        		<th width="120" colspan="2">Rejection</th>
		        		<th width="180" colspan="3">Leftover</th>
		        		<th width="120" colspan="2">Ship Status</th>
		        	</tr>
		        	<tr>
			            <th width="60">Month</th>
			            <th width="80">Total gmt</th>
			            <th width="80">Budget Yarn</th>
			            <th width="60">Extra Yarn </th>
			            <th width="60">Unauthorised Yarn</th>

			            <th width="60">YD</th>
			            <th width="60">Dyeing</th>
			            <th width="60">AOP</th>

			            <th width="60">Yarn</th>
			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Gmts</th>

			            <th width="60">Input to Ship</th>
			            <th width="60">Yarn to Ship</th>
		            </tr>
		        </thead>
		        <tbody>   
		        <?		
		        	$i=10;	        	
		        	$gr_gmts 			= 0;
		        	$gr_budget_yarn 	= 0;
		        	$gr_extra_yarn 		= 0;
		        	$gr_unautho_yarn 	= 0;

		        	$gr_los_yd 			= 0;
		        	$gr_los_dyeing 		= 0;
		        	$gr_los_aop 		= 0;

		        	$gr_lost_yarn 		= 0;
		        	$gr_lost_grey_fab 	= 0;
		        	$gr_lost_fin_fab 	= 0;
		        	$gr_lost_gmts 		= 0;
		        	$gr_lost_cutpanel	= 0;

		        	$gr_left_grey_fab 	= 0;
		        	$gr_left_fin_fab 	= 0;
		        	$gr_left_gmts 		= 0;

		        	$gr_cut_for_fab 	= 0;
		        	$gr_cut_at_cutting 	= 0;
		        	$gr_cut_at_print 	= 0;
		        	$gr_cut_at_embro 	= 0;
		        	$gr_cut_at_sewing 	= 0;

		        	$gr_cut_panel_rej 	= 0;
		        	$gr_gmt_rej 	= 0;
		        	$gr_gmt_at_finish 	= 0;

		        	$gr_input_to_ship 	= 0;
		        	$gr_yarn_to_ship 	= 0;
		        	foreach ($fiscal_month_arr as $monyear => $val) 
		        	{
		        		$yarn_knit_issue= $yarn_knit_data_array[$monyear][2];		        		
		        		$yarn_knit_issue_rtn= $yarn_knit_data_array[$monyear][4];	
		        		$yarn_issue 	= $yarn_data_array[$monyear][2];		        		
		        		$yarn_issue_rtn = $yarn_data_array[$monyear][4];

		        		$grey_yarn_issue 	= $grey_yarn_data_array[$monyear][2];		        		
		        		$grey_yarn_issue_rtn = $grey_yarn_data_array[$monyear][4];
		        		// echo $a += $grey_yarn_issue_rtn."<br>";
		        		$grey_yarn_issue_rej_rtn= $grey_yarn_rej_data_array[$monyear][4];
		        		$dyed_yarn_rtn_qty = $dyed_yarn_rej_rtn_array[$monyear][4]['rtn'];
		        		$dyed_yarn_rej_qty = $dyed_yarn_rej_rtn_array[$monyear][4]['rej'];

			        	$yarn_issue_rtn_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$monyear][2]['rtn_qty'];
			        	$yarn_rej_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$monyear][2]['rej_qty'];

			        	$yarn_issue_rtn_for_others = $yarn_dyeing_rtn_rej_data_array[$monyear][12]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][15]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][38]['rtn_qty'];
			        	$yarn_rej_for_others = $yarn_dyeing_rtn_rej_data_array[$monyear][12]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][15]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][38]['rej_qty'];

			        	$yarn_issue_for_dyeing = $yarn_dyeing_data_array[$monyear][2];
			        	$yarn_dyeing_used_qty = $yarn_dyeing_used_data_array[$monyear][2];
			        	$yarn_dyed_lost = $yarn_issue_for_dyeing - ($yarn_dyeing_used_qty+$yarn_issue_rtn_for_dyeing+$yarn_rej_for_dyeing);
			        	// ========== get twisting, reconning,re-waxing data ===========
			        	$yarn_issue_for_others = $yarn_dyeing_data_array[$monyear][12]+$yarn_dyeing_data_array[$monyear][15]+$yarn_dyeing_data_array[$monyear][38];
			        	$yarn_others_used_qty = $yarn_dyeing_used_data_array[$monyear][12]+$yarn_dyeing_used_data_array[$monyear][15]+$yarn_dyeing_used_data_array[$monyear][38];
			        	$yarn_others_lost = $yarn_issue_for_others - ($yarn_others_used_qty+$yarn_issue_rtn_for_others+$yarn_rej_for_others);

		        		$unAuthoYarn 	= ($grey_yarn_issue+$yarn_issue_for_dyeing+$yarn_issue_for_others - $grey_yarn_issue_rtn - $grey_yarn_issue_rej_rtn - $yarn_issue_rtn_for_others - $yarn_rej_for_others - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing) - ($yarn_req_array[$monyear] + $excess_yarn_array[$monyear]);
		        		
						// echo $monyear."**".$grey_yarn_issue."+".$yarn_issue_for_dyeing."+".$yarn_issue_for_others ."-". $grey_yarn_issue_rtn ."-". $grey_yarn_issue_rej_rtn ."-". $yarn_issue_rtn_for_others ."-". $yarn_rej_for_others ."-". $yarn_issue_rtn_for_dyeing ."-ss ". $yarn_rej_for_dyeing.") - (".$yarn_req_array[$monyear] ."+". $excess_yarn_array[$monyear].")<br>";
						// echo $a += $yarn_req_array[$monyear]."<br>";
						
			        	$yarn_used_qty 	= $yarn_used_data_array[$monyear];
			        	$dyed_yarn_rcv_qty 	= $dyed_yarn_rcv_array[$monyear];


			        	$grey_yarn_lost = $yarn_knit_issue - ($yarn_used_qty + $grey_yarn_issue_rtn + $grey_yarn_issue_rej_rtn);
			        	$yarn_lost_qty 	= $grey_yarn_lost+$yarn_dyed_lost+$yarn_others_lost-($dyed_yarn_rtn_qty+$dyed_yarn_rej_qty);
			        	// echo $monyear."**".$grey_yarn_lost."+".$yarn_dyed_lost."+".$yarn_others_lost."-(".$dyed_yarn_rtn_qty ."+". $dyed_yarn_rej_qty.")<br>";
			        	// echo $kakku += $dyed_yarn_rej_qty."<br>";

		        		// $yarn_dyed_loss = ($yarn_issue_for_dyeing>0) ? (($yarn_issue_for_dyeing - $yarn_dyeing_used_qty - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing)/$yarn_issue_for_dyeing)*100 : 0;

		        		// new formula
		        		$yarn_dyed_loss = ($yarn_dyeing_used_qty>0) ? (($yarn_dyeing_used_qty - $dyed_yarn_rcv_qty)/$yarn_dyeing_used_qty)*100 : 0;

		        		// echo $intRef."==(".$yarn_issue_for_dyeing ."-". $yarn_dyeing_used_qty ."-". $grey_yarn_issue_rtn.")/".$yarn_issue_for_dyeing.")*100<br>";

		        		$grey_trnsf_in 	= $transfer_in_data_array[$monyear][13];
		        		$fin_trnsf_in 	= $transfer_in_data_array[$monyear][2];
		        		$grey_trnsf_out = $transfer_out_data_array[$monyear][13];
		        		$fin_trnsf_out 	= $transfer_out_data_array[$monyear][2];

		        		$grey_fab_rcv 	= $fab_data_array[$monyear][13][1]['receive_qnty'];
		        		$grey_fab_rcv_rtn= $fab_data_array[$monyear][13][3]['issue_qnty'];
		        		$grey_fab_issue = $fab_data_array[$monyear][13][2]['issue_qnty'];
		        		$grey_fab_iss_rtn= $fab_data_array[$monyear][13][4]['receive_qnty'];
		        		//
		        		$fin_fab_rcv_without_sample 	= $fab_data_array_without_sample[$monyear][2][1]['receive_qnty'];
		        		$fin_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[$monyear][2][3]['issue_qnty'];
		        		$fin_fab_issue_without_sample 	= $fab_data_array_without_sample[$monyear][2][2]['issue_qnty'];
		        		$fin_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[$monyear][2][4]['receive_qnty'];

		        		$grey_fab_rcv_without_sample 	= $fab_data_array_without_sample[$monyear][13][1]['receive_qnty'];
		        		$grey_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[$monyear][13][3]['issue_qnty'];
		        		$grey_fab_issue_without_sample 	= $fab_data_array_without_sample[$monyear][13][2]['issue_qnty'];
		        		$grey_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[$monyear][13][4]['receive_qnty'];
		        		$grey_fab_rej_without_sample	= $fab_data_array_without_sample[$monyear][13][1]['rej_qty'];
		        		$grey_lft_over 	= $scrap_data_array[$monyear][13]['grey'];

		        		$grey_fab_lost 	= ($grey_fab_rcv_without_sample+$grey_trnsf_in+$grey_fab_iss_rtn_without_sample) -($grey_fab_issue_without_sample+$grey_fab_rcv_rtn_without_sample + $grey_lft_over + $grey_trnsf_out);

		        		// $grey_fab_lost 	= ($grey_fab_rcv+$grey_trnsf_in+$grey_fab_iss_rtn) -($grey_fab_issue+$grey_trnsf_out+ $grey_fab_rcv_rtn+ $grey_lft_over);
		        		// echo $grey_fab_lost."==".$monyear."==(".$grey_fab_rcv ."+". $grey_trnsf_in."+". $grey_fab_iss_rtn.") - (".$grey_fab_issue ."+". $grey_trnsf_out."+". $grey_fab_rcv_rtn."+". $grey_lft_over.")<br>";


		        		$grey_lft_over 	= $scrap_data_array[$monyear][13]['grey']+$grey_fab_rej_without_sample;

		        		$fin_fab_rcv 	= $fab_data_array[$monyear][2][1]['receive_qnty'];
		        		$fin_fab_rcv_rtn= $fab_data_array[$monyear][2][3]['issue_qnty'];
		        		$fin_fab_issue 	= $fab_data_array[$monyear][2][2]['issue_qnty'];
		        		$fin_fab_iss_rtn= $fab_data_array[$monyear][2][4]['receive_qnty'];
		        		$fin_lft_over 	= $scrap_data_array[$monyear][2]['fin'];
		        		// $all_fin_lft_over 	= $scrap_data_array[$monyear][2]['allfin'];

		        		$fin_rcv_as_rej_qty = $fab_data_array[$monyear][2][1]['reject_qty'];
		        		$fin_iss_as_scrap_qty = $fab_data_array[$monyear][2][2]['scrap_qty'];
		        		$all_fin_lft_over = $fin_rcv_as_rej_qty + $fin_iss_as_scrap_qty;

		        		// fin fab purses data 
		        		$fin_fab_purses_rcv = $finfab_purses_data_array[$monyear][1]['qty'];
		        		$fin_fab_purses_issue = $finfab_purses_data_array[$monyear][2]['qty'];
		        		$fin_fab_purses_rcv_rtn = $finfab_purses_data_array[$monyear][3]['qty'];
		        		$fin_fab_purses_issue_rtn = $finfab_purses_data_array[$monyear][4]['qty'];

		        		$fin_fab_purses_lost = ($fin_fab_purses_rcv + $fin_fab_purses_issue_rtn) - ($fin_fab_purses_issue + $fin_fab_purses_rcv_rtn);

		        		$fin_fab_lost 	= ($fin_fab_rcv+$fin_fab_iss_rtn+$fin_trnsf_in) -($fin_fab_issue+$fin_trnsf_out+$fin_fab_rcv_rtn) ;// + $fin_lft_over + $fin_fab_purses_lost;
		        		// echo $fin_fab_rcv ."+". $fin_fab_iss_rtn."+". $fin_trnsf_in.") - (".$fin_fab_issue ."+". $fin_trnsf_out."+". $fin_fab_rcv_rtn."<br>";//."+". $fin_lft_over

		        		$batch_qnty 	= $batch_data_array[$monyear];
		        		$fin_fab_rcv 	= $finfab_data_array[$monyear]['rcv_qty'];
		        		$fin_fab_rej 	= $finfab_data_array[$monyear]['rej_qty'];
		        		$dyeing_pro_los = ($batch_qnty>0) ? (($batch_qnty - ($fin_fab_rcv + $fin_fab_rej))/$batch_qnty)*100 : 0;
		        		// echo "((".$batch_qnty ."- (".$fin_fab_rcv ."+". $fin_fab_rej."))/".$batch_qnty.")*100<br>";

		        		$aop_grey_fab 	= $aop_data_array[$monyear]['grey'];
		        		$aop_fin_fab 	= $aop_data_array[$monyear]['finish'];
		        		$aop_pro_loss 	= ($aop_grey_fab>0) ? (($aop_grey_fab - $aop_fin_fab)/$aop_grey_fab)*100 : 0;

		        		// $cutqc_pass_qty = $cutqc_data_array[$monyear]['qc_pass_qty'];
		        		// $cutqc_rej_qty 	= $cutqc_data_array[$monyear]['qc_rej_qty'];

		        		$cutqc_pass_qty = $gmts_data_array[$monyear]['cut_qty'];
		        		$cutqc_rej_qty 	= $gmts_data_array[$monyear]['cut_rej_qty'];

		        		$wash_issue_qty = $gmts_data_array[$monyear]['wash_issue_qty'];
		        		$wash_rcv_qty 	= $gmts_data_array[$monyear]['wash_rcv_qty'];
		        		$wash_rej_qty 	= $gmts_data_array[$monyear]['wash_rej_qty'];
		        		$wash_lost_qty 	= $wash_issue_qty - $wash_rcv_qty - $wash_rej_qty;
		        		$gmt_at_wash 	= $wash_issue_qty - $wash_rcv_qty;

		        		$emb_issue_qty 	= $gmts_data_array[$monyear]['emb_issue_qty'];
		        		$emb_rcv_qty 	= $gmts_data_array[$monyear]['emb_rcv_qty'];
		        		$emb_rej_qty 	= $gmts_data_array[$monyear]['emb_rej_qty'];
		        		$emb_lost_qty  	= $emb_issue_qty - $emb_rcv_qty - $emb_rej_qty;

		        		$print_issue_qty= $gmts_data_array[$monyear]['print_issue_qty'];
		        		$print_rcv_qty 	= $gmts_data_array[$monyear]['print_rcv_qty'];
		        		$print_rej_qty 	= $gmts_data_array[$monyear]['print_rej_qty'];
		        		$print_lost_qty = $print_issue_qty - $print_rcv_qty - $print_rej_qty;

		        		$sew_in_qty 	= $gmts_data_array[$monyear]['input_qty'];
		        		$sew_out_qty 	= $gmts_data_array[$monyear]['output_qty'];
		        		$sew_rej_qty 	= $gmts_data_array[$monyear]['sew_rej_qty'];
		        		$ok_sew_qty 	= $sew_out_qty - $sew_rej_qty;
		        		$sewing_lost 	= $sew_in_qty - $sew_out_qty - $sew_rej_qty;
		        		// echo $intRef."==".$sew_in_qty ."-". $sew_out_qty ."-". $sew_rej_qty."<br>";

		        		$fin_rej_qty 	= $gmts_data_array[$monyear]['fin_rej_qty'];

		        		$cutting_lost 	= ($cutqc_pass_qty - $print_issue_qty) + (($print_rcv_qty+$print_rej_qty) - $emb_issue_qty) + (($emb_rcv_qty+ $emb_rej_qty) - $sew_in_qty);
		        		$cutpanel_lost = $cutting_lost + $print_lost_qty + $sewing_lost + $emb_lost_qty;

		        		$cutpanel_reject = $cutqc_rej_qty + $print_rej_qty + $emb_rej_qty;

		        		$cut_at_cutting = $cutqc_pass_qty - ($print_lost_qty + $print_rej_qty + $emb_lost_qty + $emb_rej_qty+$sew_in_qty);
		        		$cut_at_sewing 	= $sew_in_qty - $sew_out_qty;
		        		$cut_at_embro 	= $emb_issue_qty - $emb_rcv_qty;
		        		$cut_at_print 	= $print_issue_qty - $print_rcv_qty;

		        		$ex_factory_qty = $exfact_data_array[$monyear];
		        		$gmt_lftovr_qty = $gmts_leftover_data_array[$monyear];
		        		$gmt_at_finish  = ($ok_sew_qty - ($gmt_lftovr_qty + $ex_factory_qty));

		        		$lost_gmts_qty 	= ($sew_out_qty + $sew_rej_qty) - ($gmt_lftovr_qty + $ex_factory_qty);
		        		$gmts_reject 	= $sew_rej_qty + $wash_rej_qty + $fin_rej_qty;
		        		$input_to_ship  = ($sew_in_qty>0) ? ($ex_factory_qty / $sew_in_qty)*100 : 0;

		        		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
			        	?>     
				        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
				            <td>
				            	<a href="javascript:void()" onclick="report_generate_by_month_year('<? echo $monyear;?>','<?=$client_id;?>')">
					            	<? echo $monyear;?>
					            </a>
					        </td>
				            <td align="right"><? echo number_format($gmt_array[$monyear],0);?></td>
				            <td align="right"><? echo number_format($yarn_req_array[$monyear],0); ?></td>
				            <td align="right"><? echo number_format($excess_yarn_array[$monyear],0); ?></td>
				            <td align="right"><? echo number_format($unAuthoYarn,0); ?></td>

				            <td align="right"><? echo number_format($yarn_dyed_loss,2); ?></td>
				            <td align="right"><? echo number_format($dyeing_pro_los,2); ?></td>
				            <td align="right"><? echo number_format($aop_pro_loss,2); ?></td>

				            <td align="right"><? echo number_format($yarn_lost_qty,0); ?></td>
				            <td align="right"><? echo number_format($grey_fab_lost,0); ?></td>
				            <td align="right"><? echo number_format($fin_fab_lost,0); ?></td>
				            <td align="right" title="At Cutting=<? echo $cutting_lost;?>&#13;At Printing=<? echo $print_lost_qty;?>&#13;At Embroidery=<? echo $emb_lost_qty;?>&#13;At Sewing=<? echo $sewing_lost;?>">
				            	<? echo number_format($cutpanel_lost,0); ?>
				            </td>
				            <td align="right" title="At Wash = <? echo $wash_lost_qty;?>"><? echo number_format($lost_gmts_qty,0); ?></td>

				            <td align="right" title="At Cutting=<? echo $cutqc_rej_qty;?>&#13;At Printing=<? echo $print_rej_qty;?>&#13;At Embroidery=<? echo $emb_rej_qty;?>">
				            	<? echo number_format($cutpanel_reject,0); ?>
				            </td>
				            <td align="right" title="At Sewing=<? echo $sew_rej_qty;?>&#13;At Wash=<? echo $wash_rej_qty;?>&#13;At Finish=<? echo $fin_rej_qty;?>">
				            	<? echo number_format($gmts_reject,0); ?>
				            </td>

				            <td align="right"><? echo number_format($grey_lft_over,0); ?></td>
				            <td align="right"><? echo number_format($all_fin_lft_over,0); ?></td>
				            <td align="right"><? echo number_format($gmt_lftovr_qty,0); ?></td>

				            <td align="right"><? echo number_format($input_to_ship,2); ?></td>
				            <td align="right"><? echo number_format($a,2); ?></td>
				        </tr>
				        <?
				        $i++;
				        $gr_gmts 			+= $gmt_array[$monyear];
			        	$gr_budget_yarn 	+= $yarn_req_array[$monyear];
			        	$gr_extra_yarn 		+= $excess_yarn_array[$monyear];
			        	$gr_unautho_yarn 	+= $unAuthoYarn;

			        	$gr_los_yd 			+= $a;
			        	$gr_los_dyeing 		+= $dyeing_pro_los;
			        	$gr_los_aop 		+= $aop_pro_loss;

			        	$gr_lost_yarn 		+= $yarn_lost_qty;
			        	$gr_lost_grey_fab 	+= $grey_fab_lost;
			        	$gr_lost_fin_fab 	+= $fin_fab_lost;
			        	$gr_lost_gmts 		+= $lost_gmts_qty;
			        	$gr_lost_cutpanel	+= $cutpanel_lost;

			        	$gr_left_grey_fab 	+= $grey_lft_over;
			        	$gr_left_fin_fab 	+= $all_fin_lft_over;
			        	$gr_left_gmts 		+= $gmt_lftovr_qty;

			        	$gr_cut_for_fab 	+= $cutqc_rej_qty;
			        	$gr_cut_at_cutting 	+= $cut_at_cutting;
			        	$gr_cut_at_print 	+= $cut_at_print;
			        	$gr_cut_at_embro 	+= $cut_at_embro;
			        	$gr_cut_at_sewing 	+= $cut_at_sewing;

			        	$gr_cut_panel_rej 	+= $cutpanel_reject;
			        	$gr_gmt_rej	+= $gmts_reject;
			        	$gr_gmt_at_finish 	+= $gmt_at_finish;

			        	$gr_input_to_ship 	+= $input_to_ship;
			        	$gr_yarn_to_ship 	+= $a;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <th><? echo number_format($gr_gmts,0); ?></th>
	            <th><? echo number_format($gr_budget_yarn,0); ?></th>
	            <th><? echo number_format($gr_extra_yarn,0); ?></th>
	            <th><? echo number_format($gr_unautho_yarn,0); ?></th>

	            <th><? //echo number_format($gr_los_yd,0); ?></th>
	            <th><? //echo number_format($gr_los_dyeing,2); ?></th>
	            <th><? //echo number_format($gr_los_aop,2); ?></th>

	            <th><? echo number_format($gr_lost_yarn,0); ?></th>
	            <th><? echo number_format($gr_lost_grey_fab,0); ?></th>
	            <th><? echo number_format($gr_lost_fin_fab,0); ?></th>
	            <th><? echo number_format($gr_lost_cutpanel,0); ?></th>
	            <th><? echo number_format($gr_lost_gmts,0); ?></th>

	            <th><? echo number_format($gr_cut_panel_rej,0); ?></th>
	            <th><? echo number_format($gr_gmt_rej,0); ?></th>

	            <th><? echo number_format($gr_left_grey_fab,0); ?></th>
	            <th><? echo number_format($gr_left_fin_fab,0); ?></th>
	            <th><? echo number_format($gr_left_gmts,0); ?></th>

	            <th><? //echo number_format($gr_input_to_ship,2); ?></th>
	            <th><? //echo number_format($gr_yarn_to_ship,2); ?></th>
	        </tfoot>
	    </table>      
	    </div>    
	</fieldset>
	<!-- =======================================================================================/
	/										buyer summary report								/
	/======================================================================================== --> 
	<fieldset class="first_2nd" style="width:<? echo $tbl_width+30;?>px;margin:15px 0;">
		    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'buyer_summary_report', '')"> -<b>Buyer Summary : <? echo $year; ?></b><span style="color: red;font-size: 15px;font-weight: bold;"> <?=($client_id) ? ": ".$client_array[$client_id] : ": All"; ?></span></h3>
		    <div id="buyer_summary_report">
				<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">			
			        <thead>
		        	<tr>
		        		<th width="340" colspan="5"></th>
		        		<th width="180" colspan="3">Process Loss%</th>
		        		<th width="300" colspan="5">Lost</th>
		        		<th width="120" colspan="2">Rejection</th>
		        		<th width="180" colspan="3">Leftover</th>
		        		<th width="120" colspan="2">Ship Status</th>
		        	</tr>
		        	<tr>
			            <th width="60">Buyer</th>
			            <th width="80">Total gmt</th>
			            <th width="80">Budget Yarn</th>
			            <th width="60">Extra Yarn </th>
			            <th width="60">Unauthorised Yarn</th>

			            <th width="60">YD</th>
			            <th width="60">Dyeing</th>
			            <th width="60">AOP</th>

			            <th width="60">Yarn</th>
			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>
			            
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Gmts</th>

			            <th width="60">Input to Ship</th>
			            <th width="60">Yarn to Ship</th>
		            </tr>
		        </thead>
			        <tbody>   
			        	<?   	
			        	$i=30;
			        	$gr_gmts 			= 0;
			        	$gr_budget_yarn 	= 0;
			        	$gr_extra_yarn 		= 0;
			        	$gr_unautho_yarn 	= 0;

			        	$gr_los_yd 			= 0;
			        	$gr_los_dyeing 		= 0;
			        	$gr_los_aop 		= 0;

			        	$gr_lost_yarn 		= 0;
			        	$gr_lost_grey_fab 	= 0;
			        	$gr_lost_fin_fab 	= 0;
			        	$gr_lost_gmts 		= 0;
			        	$gr_lost_cutpanel	= 0;

			        	$gr_left_grey_fab 	= 0;
			        	$gr_left_fin_fab 	= 0;
			        	$gr_left_gmts 		= 0;

			        	$gr_cut_for_fab 	= 0;
			        	$gr_cut_at_cutting 	= 0;
			        	$gr_cut_at_print 	= 0;
			        	$gr_cut_at_embro 	= 0;
			        	$gr_cut_at_sewing 	= 0;

			        	$gr_cut_panel_rej 	= 0;
			        	$gr_gmt_rej 	= 0;
			        	$gr_gmt_at_finish 	= 0;

			        	$gr_input_to_ship 	= 0;
			        	$gr_yarn_to_ship 	= 0;
			        	ksort($buyer_data_array); 
			        	// print_r($buyer_data_array);
			        	foreach ($buyer_data_array as $buyer_name => $row) 
			        	{
			        		$buyer_id 		= $row['buyer_id'];
		        			$yarn_knit_issue= $buyer_yarn_knit_data_array[$buyer_id][2];		        		
		        			$yarn_knit_issue_rtn= $buyer_yarn_knit_data_array[$buyer_id][4];	
			        		$yarn_issue 	= $buyer_yarn_data_array[$buyer_id][2];		        		
			        		$yarn_issue_rtn = $buyer_yarn_data_array[$buyer_id][4];

			        		$grey_yarn_issue 	= $buyer_grey_yarn_data_array[$buyer_id][2];		        		
		        			$grey_yarn_issue_rtn = $buyer_grey_yarn_data_array[$buyer_id][4];
		        			$grey_yarn_issue_rej_rtn= $buyer_grey_yarn_rej_data_array[$buyer_id][4];
		        			$dyed_yarn_rtn_qty = $buyer_dyed_yarn_rej_rtn_array[$buyer_id][4]['rtn'];
		        			$dyed_yarn_rej_qty = $buyer_dyed_yarn_rej_rtn_array[$buyer_id][4]['rej'];

				        	$yarn_issue_rtn_for_dyeing = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][2]['rtn_qty'];
				        	$yarn_rej_for_dyeing = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][2]['rej_qty'];

				        	$yarn_issue_rtn_for_others = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][12]['rtn_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][15]['rtn_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][38]['rtn_qty'];
				        	$yarn_rej_for_others = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][12]['rej_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][15]['rej_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][38]['rej_qty'];

				        	$yarn_issue_for_dyeing = $buyer_yarn_dyeing_data_array[$buyer_id][2];
				        	$yarn_dyeing_used_qty = $buyer_yarn_dyeing_used_data_array[$buyer_id][2];
			        		$yarn_dyed_lost = $yarn_issue_for_dyeing - ($yarn_dyeing_used_qty+$yarn_issue_rtn_for_dyeing+$yarn_rej_for_dyeing);
				        	// ========== get twisting, reconning,re-waxing data ===========
				        	$yarn_issue_for_others = $buyer_yarn_dyeing_data_array[$buyer_id][12]+$buyer_yarn_dyeing_data_array[$buyer_id][15]+$buyer_yarn_dyeing_data_array[$buyer_id][38];
				        	$yarn_others_used_qty = $buyer_yarn_dyeing_used_data_array[$buyer_id][12]+$buyer_yarn_dyeing_used_data_array[$buyer_id][15]+$buyer_yarn_dyeing_used_data_array[$buyer_id][38];
				        	$yarn_others_lost = $yarn_issue_for_others - ($yarn_others_used_qty+$yarn_issue_rtn_for_others+$yarn_rej_for_others);

			        		$unAuthoYarn 	= ($grey_yarn_issue+$yarn_issue_for_dyeing+$yarn_issue_for_others - $grey_yarn_issue_rtn - $grey_yarn_issue_rej_rtn - $yarn_issue_rtn_for_others - $yarn_rej_for_others - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing) - ($buyer_yarn_req_array[$buyer_id] + $buyer_excess_yarn_array[$buyer_id]);

			        		// echo $buyer_id."**".$grey_yarn_issue."+".$yarn_issue_for_dyeing."+".$yarn_issue_for_others ."-". $grey_yarn_issue_rtn ."-". $grey_yarn_issue_rej_rtn ."-". $yarn_issue_rtn_for_others ."-". $yarn_rej_for_others ."-". $yarn_issue_rtn_for_dyeing ."-". $yarn_rej_for_dyeing.") - (".$buyer_yarn_req_array[$buyer_id] ."+". $buyer_excess_yarn_array[$buyer_id].")<br>";

			        		$yarn_used_qty 	= $buyer_yarn_used_data_array[$buyer_id];
			        		$dyed_yarn_rcv_qty 	= $buyer_dyed_yarn_rcv_array[$buyer_id];


			        		$grey_yarn_lost = $yarn_knit_issue - ($yarn_used_qty + $grey_yarn_issue_rtn + $grey_yarn_issue_rej_rtn);
			        		$yarn_lost_qty 	= $grey_yarn_lost+$yarn_dyed_lost+$yarn_others_lost-($dyed_yarn_rtn_qty+$dyed_yarn_rej_qty);
			        		// echo $yarn_issue ."- (".$yarn_issue_rtn ."+". $yarn_used_qty.")<br>";

			        		// $yarn_dyed_loss = ($yarn_issue_for_dyeing>0) ? (($yarn_issue_for_dyeing - $yarn_dyeing_used_qty - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing)/$yarn_issue_for_dyeing)*100 : 0;

		        			// new formula
		        			$yarn_dyed_loss = ($yarn_dyeing_used_qty>0) ? (($yarn_dyeing_used_qty - $dyed_yarn_rcv_qty)/$yarn_dyeing_used_qty)*100 : 0;

			        		// echo $intRef."==(".$yarn_issue_for_dyeing ."-". $yarn_dyeing_used_qty ."-". $grey_yarn_issue_rtn.")/".$yarn_issue_for_dyeing.")*100<br>";

			        		$grey_trnsf_in 	= $buyer_transfer_in_data_array[$buyer_id][13];
			        		$fin_trnsf_in 	= $buyer_transfer_in_data_array[$buyer_id][2];
			        		$grey_trnsf_out = $buyer_transfer_out_data_array[$buyer_id][13];
			        		$fin_trnsf_out 	= $buyer_transfer_out_data_array[$buyer_id][2];

			        		$grey_fab_rcv 	= $buyer_fab_data_array[$buyer_id][13][1]['receive_qnty'];
			        		$grey_fab_rcv_rtn= $buyer_fab_data_array[$buyer_id][13][3]['issue_qnty'];
			        		$grey_fab_issue = $buyer_fab_data_array[$buyer_id][13][2]['issue_qnty'];
			        		$grey_fab_iss_rtn= $buyer_fab_data_array[$buyer_id][13][4]['receive_qnty'];

			        		//
			        		$fin_fab_rcv_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][2][1]['receive_qnty'];
			        		$fin_fab_rcv_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][2][3]['issue_qnty'];
			        		$fin_fab_issue_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][2][2]['issue_qnty'];
			        		$fin_fab_iss_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][2][4]['receive_qnty'];

			        		$grey_fab_rcv_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][13][1]['receive_qnty'];
			        		$grey_fab_rcv_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][13][3]['issue_qnty'];
			        		$grey_fab_issue_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][13][2]['issue_qnty'];
			        		$grey_fab_iss_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][13][4]['receive_qnty'];
			        		$grey_fab_rej_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][13][1]['rej_qty'];
			        		$grey_lft_over 	= $buyer_scrap_data_array[$buyer_id][13]['grey'];
		        		
		        			$grey_fab_lost 	= ($grey_fab_rcv_without_sample+$grey_trnsf_in+$grey_fab_iss_rtn_without_sample) -($grey_fab_issue_without_sample+$grey_fab_rcv_rtn_without_sample + $grey_lft_over + $grey_trnsf_out);

			        		// $grey_fab_lost 	= ($grey_fab_rcv+$grey_trnsf_in+$grey_fab_iss_rtn) -($grey_fab_issue+$grey_trnsf_out+ $grey_fab_rcv_rtn+ $grey_lft_over);
			        		//echo $grey_fab_rcv ."-(".$grey_fab_issue ."+". $grey_lft_over.")<br>";
			        		$grey_lft_over 	= $buyer_scrap_data_array[$buyer_id][13]['grey']+$grey_fab_rej_without_sample;

			        		$fin_fab_rcv 	= $buyer_fab_data_array[$buyer_id][2][1]['receive_qnty'];
			        		$fin_fab_rcv_rtn= $buyer_fab_data_array[$buyer_id][2][3]['issue_qnty'];
			        		$fin_fab_issue 	= $buyer_fab_data_array[$buyer_id][2][2]['issue_qnty'];
			        		$fin_fab_iss_rtn= $buyer_fab_data_array[$buyer_id][2][4]['receive_qnty'];
			        		$fin_lft_over 	= $buyer_scrap_data_array[$buyer_id][2]['fin'];
			        		// $all_fin_lft_over 	= $buyer_scrap_data_array[$buyer_id][2]['allfin'];

			        		$fin_rcv_as_rej_qty = $buyer_fab_data_array[$buyer_id][2][1]['reject_qty'];
		        			$fin_iss_as_scrap_qty = $buyer_fab_data_array[$buyer_id][2][2]['scrap_qty'];
		        			$all_fin_lft_over = $fin_rcv_as_rej_qty + $fin_iss_as_scrap_qty;

			        		// fin fab purses data 
			        		$fin_fab_purses_rcv = $buyer_finfab_purses_data_array[$buyer_id][1]['qty'];
			        		$fin_fab_purses_issue = $buyer_finfab_purses_data_array[$buyer_id][2]['qty'];
			        		$fin_fab_purses_rcv_rtn = $buyer_finfab_purses_data_array[$buyer_id][3]['qty'];
			        		$fin_fab_purses_issue_rtn = $buyer_finfab_purses_data_array[$buyer_id][4]['qty'];

			        		$fin_fab_purses_lost = ($fin_fab_purses_rcv + $fin_fab_purses_issue_rtn) - ($fin_fab_purses_issue + $fin_fab_purses_rcv_rtn);

			        		$fin_fab_lost 	= ($fin_fab_rcv+$fin_fab_iss_rtn+$fin_trnsf_in) -($fin_fab_issue+$fin_trnsf_out+$fin_fab_rcv_rtn) ;// + $fin_lft_over + $fin_fab_purses_lost;

			        		$batch_qnty 	= $buyer_batch_data_array[$buyer_id];
			        		$fin_fab_rcv 	= $buyer_finfab_data_array[$buyer_id]['rcv_qty'];
			        		$fin_fab_rej 	= $buyer_finfab_data_array[$buyer_id]['rej_qty'];
			        		$dyeing_pro_los = ($batch_qnty>0) ? (($batch_qnty - ($fin_fab_rcv + $fin_fab_rej))/$batch_qnty)*100 : 0;
			        		// echo "((".$batch_qnty ."- (".$fin_fab_rcv ."+". $fin_fab_rej."))/".$batch_qnty.")*100<br>";

			        		$aop_grey_fab 	= $buyer_aop_data_array[$buyer_id]['grey'];
			        		$aop_fin_fab 	= $buyer_aop_data_array[$buyer_id]['finish'];
			        		$aop_pro_loss 	= ($aop_grey_fab>0) ? (($aop_grey_fab - $aop_fin_fab)/$aop_grey_fab)*100 : 0;

			        		// $cutqc_pass_qty = $buyer_cutqc_data_array[$buyer_id]['qc_pass_qty'];
			        		// $cutqc_rej_qty 	= $buyer_cutqc_data_array[$buyer_id]['qc_rej_qty'];

			        		$cutqc_pass_qty = $buyer_gmts_data_array[$buyer_id]['cut_qty'];
			        		$cutqc_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['cut_rej_qty'];

			        		$wash_issue_qty = $buyer_gmts_data_array[$buyer_id]['wash_issue_qty'];
			        		$wash_rcv_qty 	= $buyer_gmts_data_array[$buyer_id]['wash_rcv_qty'];
			        		$wash_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['wash_rej_qty'];
			        		$wash_lost_qty 	= $wash_issue_qty - $wash_rcv_qty - $wash_rej_qty;
			        		$gmt_at_wash 	= $wash_issue_qty - $wash_rcv_qty;

			        		$emb_issue_qty 	= $buyer_gmts_data_array[$buyer_id]['emb_issue_qty'];
			        		$emb_rcv_qty 	= $buyer_gmts_data_array[$buyer_id]['emb_rcv_qty'];
			        		$emb_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['emb_rej_qty'];
			        		$emb_lost_qty  	= $emb_issue_qty - $emb_rcv_qty - $emb_rej_qty;

			        		$print_issue_qty= $buyer_gmts_data_array[$buyer_id]['print_issue_qty'];
			        		$print_rcv_qty 	= $buyer_gmts_data_array[$buyer_id]['print_rcv_qty'];
			        		$print_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['print_rej_qty'];
			        		$print_lost_qty = $print_issue_qty - $print_rcv_qty - $print_rej_qty;

			        		$sew_in_qty 	= $buyer_gmts_data_array[$buyer_id]['input_qty'];
			        		$sew_out_qty 	= $buyer_gmts_data_array[$buyer_id]['output_qty'];
			        		$sew_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['sew_rej_qty'];
			        		$ok_sew_qty 	= $sew_out_qty - $sew_rej_qty;
			        		$sewing_lost 	= $sew_in_qty - $sew_out_qty - $sew_rej_qty;
			        		// echo $intRef."==".$sew_in_qty ."-". $sew_out_qty ."-". $sew_rej_qty."<br>";

			        		$fin_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['fin_rej_qty'];

			        		$cutting_lost 	= ($cutqc_pass_qty - $print_issue_qty) + (($print_rcv_qty+$print_rej_qty) - $emb_issue_qty) + (($emb_rcv_qty+ $emb_rej_qty) - $sew_in_qty);
			        		$cutpanel_lost = $cutting_lost + $print_lost_qty + $sewing_lost + $emb_lost_qty;

			        		$cutpanel_reject = $cutqc_rej_qty + $print_rej_qty + $emb_rej_qty;

			        		$cut_at_cutting = $cutqc_pass_qty - ($print_lost_qty + $print_rej_qty + $emb_lost_qty + $emb_rej_qty+$sew_in_qty);
			        		$cut_at_sewing 	= $sew_in_qty - $sew_out_qty;
			        		$cut_at_embro 	= $emb_issue_qty - $emb_rcv_qty;
			        		$cut_at_print 	= $print_issue_qty - $print_rcv_qty;

			        		$ex_factory_qty = $buyer_exfact_data_array[$buyer_id];
			        		$gmt_lftovr_qty = $buyer_gmts_leftover_data_array[$buyer_id];
			        		$gmt_at_finish  = ($ok_sew_qty - ($gmt_lftovr_qty + $ex_factory_qty));

			        		$lost_gmts_qty 	= ($sew_out_qty + $sew_rej_qty) - ($gmt_lftovr_qty + $ex_factory_qty);
			        		$gmts_reject 	= $sew_rej_qty + $wash_rej_qty + $fin_rej_qty;
			        		$input_to_ship  = ($sew_in_qty>0) ? ($ex_factory_qty / $sew_in_qty)*100 : 0;

			        		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
				        	?>     
					        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					            <td title="<? echo $buyer_id;?>">
					            	<a href="javascript:void()" onclick="report_generate_by_buyer_month_year('<? echo $buyer_id;?>','<? echo $year;?>','<?=$client_id;?>')">
						            	<? echo $buyer_name;?>
						            </a>
						        </td>
					            <td align="right"><? echo number_format($row['gmts_qty'],0);?></td>
					            <td align="right"><? echo number_format($buyer_yarn_req_array[$buyer_id],0); ?></td>
					            <td align="right"><? echo number_format($buyer_excess_yarn_array[$buyer_id],0); ?></td>
					            <td align="right"><? echo number_format($unAuthoYarn,0); ?></td>

					            <td align="right"><? echo number_format($yarn_dyed_loss,0); ?></td>
					            <td align="right"><? echo number_format($dyeing_pro_los,2); ?></td>
					            <td align="right"><? echo number_format($aop_pro_loss,2); ?></td>

					            <td align="right"><? echo number_format($yarn_lost_qty,0); ?></td>
					            <td align="right"><? echo number_format($grey_fab_lost,0); ?></td>
					            <td align="right"><? echo number_format($fin_fab_lost,0); ?></td>
					            <td align="right" title="At Cutting=<? echo $cutting_lost;?>&#13;At Printing=<? echo $print_lost_qty;?>&#13;At Embroidery=<? echo $emb_lost_qty;?>&#13;At Sewing=<? echo $sewing_lost;?>">
					            	<? echo number_format($cutpanel_lost,0); ?>
					            </td>
					            <td align="right" title="At Wash = <? echo $wash_lost_qty;?>"><? echo number_format($lost_gmts_qty,0); ?></td>

					            <td align="right" title="At Cutting=<? echo $cutqc_rej_qty;?>&#13;At Printing=<? echo $print_rej_qty;?>&#13;At Embroidery=<? echo $emb_rej_qty;?>">
					            	<? echo number_format($cutpanel_reject,0); ?>
					            </td>
					            <td align="right" title="At Sewing=<? echo $sew_rej_qty;?>&#13;At Wash=<? echo $wash_rej_qty;?>&#13;At Finish=<? echo $fin_rej_qty;?>">
					            	<? echo number_format($gmts_reject,0); ?>
					            </td>

					            <td align="right"><? echo number_format($grey_lft_over,0); ?></td>
					            <td align="right"><? echo number_format($all_fin_lft_over,0); ?></td>
					            <td align="right"><? echo number_format($gmt_lftovr_qty,0); ?></td>

					            <td align="right"><? echo number_format($input_to_ship,2); ?></td>
					            <td align="right"><? echo number_format($a,2); ?></td>
					        </tr>
					        <? 
					        $i++;
					        $gr_gmts 			+= $row['gmts_qty'];
				        	$gr_budget_yarn 	+= $buyer_yarn_req_array[$buyer_id];
				        	$gr_extra_yarn 		+= $buyer_excess_yarn_array[$buyer_id];
				        	$gr_unautho_yarn 	+= $unAuthoYarn;

				        	$gr_los_yd 			+= $a;
				        	$gr_los_dyeing 		+= $dyeing_pro_los;
				        	$gr_los_aop 		+= $aop_pro_loss;

				        	$gr_lost_yarn 		+= $yarn_lost_qty;
				        	$gr_lost_grey_fab 	+= $grey_fab_lost;
				        	$gr_lost_fin_fab 	+= $fin_fab_lost;
				        	$gr_lost_gmts 		+= $lost_gmts_qty;
				        	$gr_lost_cutpanel	+= $cutpanel_lost;

				        	$gr_left_grey_fab 	+= $grey_lft_over;
				        	$gr_left_fin_fab 	+= $all_fin_lft_over;
				        	$gr_left_gmts 		+= $gmt_lftovr_qty;

				        	$gr_cut_for_fab 	+= $cutqc_rej_qty;
				        	$gr_cut_at_cutting 	+= $cut_at_cutting;
				        	$gr_cut_at_print 	+= $cut_at_print;
				        	$gr_cut_at_embro 	+= $cut_at_embro;
				        	$gr_cut_at_sewing 	+= $cut_at_sewing;

				        	$gr_cut_panel_rej 	+= $cutpanel_reject;
				        	$gr_gmt_rej 		+= $gmts_reject;
				        	$gr_gmt_at_finish 	+= $gmt_at_finish;

				        	$gr_input_to_ship 	+= $input_to_ship;
				        	$gr_yarn_to_ship 	+= $a;
					    }
					    ?>
			        </tbody>
			        <tfoot>
			            <th align="right">Total</th>
			            <th><? echo number_format($gr_gmts,0); ?></th>
			            <th><? echo number_format($gr_budget_yarn,0); ?></th>
			            <th><? echo number_format($gr_extra_yarn,0); ?></th>
			            <th><? echo number_format($gr_unautho_yarn,0); ?></th>

			            <th><? //echo number_format($gr_los_yd,0); ?></th>
			            <th><? //echo number_format($gr_los_dyeing,2); ?></th>
			            <th><? //echo number_format($gr_los_aop,2); ?></th>

			            <th><? echo number_format($gr_lost_yarn,0); ?></th>
			            <th><? echo number_format($gr_lost_grey_fab,0); ?></th>
			            <th><? echo number_format($gr_lost_fin_fab,0); ?></th>
			            <th><? echo number_format($gr_lost_cutpanel,0); ?></th>
			            <th><? echo number_format($gr_lost_gmts,0); ?></th>

			            <th><? echo number_format($gr_cut_panel_rej,0); ?></th>
			            <th><? echo number_format($gr_gmt_rej,0); ?></th>

			            <th><? echo number_format($gr_left_grey_fab,0); ?></th>
			            <th><? echo number_format($gr_left_fin_fab,0); ?></th>
			            <th><? echo number_format($gr_left_gmts,0); ?></th>

			            <th><? //echo number_format($gr_input_to_ship,2); ?></th>
			            <th><? //echo number_format($gr_yarn_to_ship,2); ?></th>
			        </tfoot>
			    </table>      
		    </div>    
	</fieldset>
			
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

if($action=="report_generate_by_month_year")// report generate when month summary click
{
	$company_id 	= str_replace("'","",$cbo_company_id);
	$location_id 	= str_replace("'","",$cbo_location_id);
	$client_id 		= str_replace("'","",$cbo_client);
	$order_status	= str_replace("'","",$cbo_order_status);
	$year 			= str_replace("'","",$month_year);

	$sqlCond = "";
	$sqlCond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
	$sqlCond .= ($location_id !=0) ? " and a.location_name=$location_id" : "";
	$sqlCond .= ($client_id != 0) ? " and a.client_id=$client_id" : "";
	if($order_status !=0)
	{
		$sqlCond .= ($order_status == 1) ? " and b.shiping_status in(1,2)" : " and b.shiping_status in(3)";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$client_array = return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$company_id' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name", "id", "buyer_name");
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
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
	$startDate= date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();

	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/	
	$sql_gmt="SELECT a.JOB_NO,a.BUYER_NAME,a.TOTAL_SET_QNTY,b.id as PO_ID,to_char(b.shipment_date,'DD-MON') as MONTH_YEAR, SUM(b.po_quantity*a.total_set_qnty) AS QTY
	 from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id  $sqlCond and to_char(b.shipment_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no,a.buyer_name,a.total_set_qnty,b.id,b.shipment_date";
	// echo $sql_gmt;die();
	$sql_gmt_res = sql_select($sql_gmt);
	if(!count($sql_gmt_res))
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}

	$po_id_array = array();
	$gmt_array = array();
	$buyer_data_array = array();
	foreach ($sql_gmt_res as $val) 
	{	
		$gmt_array[$val['MONTH_YEAR']] += $val['QTY'];			
		$buyer_data_array[$buyer_arr[$val['BUYER_NAME']]]['gmts_qty'] += $val['QTY'];			
		$buyer_data_array[$buyer_arr[$val['BUYER_NAME']]]['buyer_id'] = $val['BUYER_NAME'];			
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];	
	}
	// print_r($buyer_data_array);die();
	unset($sql_gmt_res);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	/*==========================================================================================/
	/								yarn req qty from budget									/
	/==========================================================================================*/
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);

    $sql = "SELECT a.job_no AS JOB_NO,a.BUYER_NAME ,to_char(b.shipment_date,'DD-MON') as MONTH_YEAR,b.grouping as INT_REF,b.id AS id,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS country_id,c.color_number_id AS color_number_id,c.size_number_id AS size_number_id,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,c.country_ship_date AS country_ship_date,d.id AS pre_cost_dtls_id,d.fab_nature_id AS fab_nature_id,d.construction AS construction, d.gsm_weight AS gsm_weight,e.cons AS cons,e.requirment AS REQUIRMENT,f.id AS yarn_id,f.count_id AS count_id,f.copm_one_id AS copm_one_id,f.percent_one AS percent_one,f.type_id AS type_id,f.color AS color,f.cons_ratio AS CONS_RATIO,f.cons_qnty AS cons_qnty,f.avg_cons_qnty AS avg_cons_qnty,f.rate AS rate,f.amount AS amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_id $poCond and to_char(b.shipment_date,'MON-YYYY')='$year'";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$yarn_req_array = array();
	$buyer_yarn_req_array = array();
	foreach ($sql_res as $key => $row) 
	{		
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;

        $gmtsitemRatio 	= $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio 		= $row['CONS_RATIO'];
        $requirment 	= $row['REQUIRMENT'];
        $consQnty 		= $requirment*$consRatio/100;
        $reqQty 		= ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);	
        // echo $row['JOB_NO']."==".$reqQty 		."= (".$row[$plan_cut_year]."/".$gmtsitemRatio.")*(".$consQnty."/".$pcs_value.")<br>";
		
		($reqQty>0)?$yarn_req_array[$row['MONTH_YEAR']] += $reqQty:"";
		($reqQty>0)?$buyer_yarn_req_array[$row['BUYER_NAME']] += $reqQty:"";
		
	}
	unset($sql_res);
	// print_r($yarn_req_array);die();
	/*==========================================================================================/
	/									get req. and program id									/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sql_plan = "SELECT c.requisition_no as REQUISITION_ID, c.knit_id as  PROGRAM_ID from wo_booking_mst a, ppl_planning_entry_plan_dtls b, ppl_yarn_requisition_entry c where a.booking_no=b.booking_no and b.dtls_id=c.knit_id and a.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 $bookingPoCond";
	// echo $sql_plan;
	$plan_res = sql_select($sql_plan);
	$program_id_array = array();
	$requisition_id_array = array();
	foreach ($plan_res as $val) 
	{
		$program_id_array[$val['PROGRAM_ID']] = $val['PROGRAM_ID'];
		$requisition_id_array[$val['REQUISITION_ID']] = $val['REQUISITION_ID'];
	}
	$program_ids = implode(",", $program_id_array);
	$requisition_ids = implode(",", $requisition_id_array);

	/*==========================================================================================/
	/										excess yarn											/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlExcessYarn = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, SUM(b.grey_fab_qnty) AS GREY_FAB_QNTY FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' $bookingPoCond group by c.buyer_name,d.shipment_date order by d.shipment_date";
    // echo $sqlExcessYarn;die();
    $exYarnRes = sql_select($sqlExcessYarn);
    $excess_yarn_array = array();
    $buyer_excess_yarn_array = array();
    foreach ($exYarnRes as $val) 
    {			
        $excess_yarn_array[$val['MONTH_YEAR']] += $val['GREY_FAB_QNTY'];       
        $buyer_excess_yarn_array[$val['BUYER_NAME']] += $val['GREY_FAB_QNTY'];       
    }
    unset($exYarnRes);
    /*==========================================================================================/
	/										yarn receive										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnRcv = "SELECT a.TRANSACTION_TYPE,e.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and d.job_id=e.id and c.id=a.mst_id and a.item_category=1 and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond";
	// echo $sqlYArnRcv;die();
	$yarnRcvRes = sql_select($sqlYArnRcv);
	$yarn_data_array = array();
	$buyer_yarn_data_array = array();
	foreach ($yarnRcvRes as $val) 
	{
        $yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_data_array);die();
    /*==========================================================================================/
	/										yarn issue											/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,c.ISSUE_PURPOSE,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond and a.requisition_no in($requisition_ids)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_knit_data_array = array();
	foreach ($yarnISsueRes as $val) 
	{
		if($val['ISSUE_PURPOSE']==1)
		{
        	$yarn_knit_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        }
        $yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_data_array);die();

    /*==========================================================================================/
	/							yarn issue for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT c.ID,e.BUYER_NAME,c.ISSUE_PURPOSE,a.TRANSACTION_TYPE,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond and c.issue_purpose in(2,12,15,38)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_dyeing_data_array = array();
	$buyer_yarn_dyeing_data_array = array();
	$issue_id_array = array();
	$issue_purpose_array = array();
	foreach ($yarnISsueRes as $val) 
	{
        $yarn_dyeing_data_array[$val['MONTH_YEAR']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $buyer_yarn_dyeing_data_array[$val['BUYER_NAME']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $issue_id_array[$val['ID']] = $val['ID'];
        $issue_purpose_array[$val['ID']] = $val['ISSUE_PURPOSE'];
	}
	$issueIds = implode(",", $issue_id_array);
	// print_r($yarn_dyeing_data_array);die(); 

	/*==========================================================================================/
	/						get yarn used qty for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,c.BUYER_NAME,a.RECEIVE_PURPOSE,b.TRANSACTION_TYPE,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, b.GREY_QUANTITY as USED_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2,12,15,38) and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_used_data_array = array();
	$buyer_yarn_dyeing_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_used_data_array[$val['MONTH_YEAR']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
		$buyer_yarn_dyeing_used_data_array[$val['BUYER_NAME']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
	}
	// print_r($yarn_dyeing_used_data_array);die();

	/*==========================================================================================/
	/					get yarn rej & rtn qty for dyeing,twisting,recon....					/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.ISSUE_ID,a.RECV_NUMBER,c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, b.CONS_QUANTITY as RTN_QTY,b.cons_reject_qnty as REJ_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id  and c.id=d.job_id and b.item_category=1  and a.entry_form=9 and b.transaction_type in(4) and e.trans_id=b.id and d.id=e.po_breakdown_id and b.transaction_type=e.trans_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose is null and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond and a.issue_id in($issueIds)";//and a.booking_id in($yd_wo_ids) 
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$buyer_yarn_dyeing_rtn_rej_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$buyer_yarn_dyeing_rtn_rej_data_array[$val['BUYER_NAME']][$issue_purpose_array[$val['ISSUE_ID']]]['rtn_qty'] += $val['RTN_QTY'];
		$buyer_yarn_dyeing_rtn_rej_data_array[$val['BUYER_NAME']][$issue_purpose_array[$val['ISSUE_ID']]]['rej_qty'] += $val['REJ_QTY'];
			
	}
	// print_r($buyer_yarn_dyeing_rtn_rej_data_array);die();

	/*==========================================================================================/
	/										get dyed yarn rcv qty								/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,c.BUYER_NAME,a.RECEIVE_PURPOSE,b.TRANSACTION_TYPE,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, f.QUANTITY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e,order_wise_pro_details f where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and f.trans_id=b.id and d.id=f.po_breakdown_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_used_data_array = array();
	$buyer_dyed_yarn_rcv_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$buyer_dyed_yarn_rcv_array[$val['BUYER_NAME']] += $val['QUANTITY'];
	}
	// print_r($buyer_dyed_yarn_rcv_array);die();
    /*==========================================================================================/
	/									get yarn used qty										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnUsd = "SELECT e.BUYER_NAME,to_char(f.shipment_date,'DD-MON') as MONTH_YEAR, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master a, order_wise_pro_details b,inv_transaction d,wo_po_details_master e,wo_po_break_down f where d.id=b.trans_id and d.prod_id=b.prod_id and e.id=f.job_id and f.id=b.po_breakdown_id and d.item_category=13 and b.entry_form=2 and d.transaction_type in(1) and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(f.shipment_date,'MON-YYYY')='$year' $yarnPoCond and a.booking_id in($program_ids)";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_used_data_array[$val['MONTH_YEAR']] += $val['QUANTITY']+$val['RETURNABLE_QNTY'];
        $buyer_yarn_used_data_array[$val['BUYER_NAME']] += $val['QUANTITY']+$val['RETURNABLE_QNTY'];
	}
	// print_r($yarn_used_data_array);die();
    /*==========================================================================================/
	/										grey yarn issue										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssue = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond and a.requisition_no in($requisition_ids)";
	// echo $sqlGreyYArnIssue;die();
	$greyYarnISsueRes = sql_select($sqlGreyYArnIssue);
	$grey_yarn_data_array = array();
	$buyer_grey_yarn_data_array = array();
	foreach ($greyYarnISsueRes as $val) 
	{
        $grey_yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_grey_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
    }
    /*==========================================================================================/
	/										grey yarn issue	rtn									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssueRtn = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,f.DYED_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY,b.REJECT_QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2,1) and f.item_category_id=1 and to_char(d.shipment_date,'MON-YYYY')='$year' $yarnPoCond and c.booking_id in($requisition_ids)";
	// echo $sqlGreyYArnIssueRtn;die();
	$greyYarnIssueRtnRes = sql_select($sqlGreyYArnIssueRtn);
	$grey_yarn_rej_data_array = array();
	$buyer_grey_yarn_rej_data_array = array();
	$dyed_yarn_rej_rtn_array = array();
	foreach ($greyYarnIssueRtnRes as $val) 
	{
		if($val['DYED_TYPE']==1)
		{
			$dyed_yarn_rej_rtn_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['rtn'] += $val['QTY'];
			$dyed_yarn_rej_rtn_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['rej'] += $val['REJECT_QTY'];
		}
		else
		{
	        $grey_yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	        $buyer_grey_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];

	        $grey_yarn_rej_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
	        $buyer_grey_yarn_rej_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
	    }
    }
    // echo "<pre>"; print_r($grey_yarn_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	rcv									/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME, a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY,to_char(e.shipment_date,'DD-MON') as MONTH_YEAR, b.QUANTITY, b.RETURNABLE_QNTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(1,4) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' $greyfabPoCond";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array = array();
	$buyer_fab_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['reject_qty'] += $val['RETURNABLE_QNTY'];
		}
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	issue								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,c.ISSUE_PURPOSE,to_char(e.shipment_date,'DD-MON') as MONTH_YEAR,  b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(2,3) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' $greyfabPoCond";// and c.issue_purpose != 31
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $fab_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		if($val['ISSUE_PURPOSE']==31)
		{
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['scrap_qty'] += $val['QUANTITY'];
		}
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/						grey and fin fabric	rcv	without sample								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT c.recv_number,d.BUYER_NAME, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'DD-MON') as MONTH_YEAR,b.QUANTITY,b.returnable_qnty as REJ_QTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=c.booking_id and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' $greyfabPoCond";// and c.booking_id in($program_ids)
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array_without_sample = array();
	$buyer_fab_data_array_without_sample = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['rej_qty'] += $val['REJ_QTY'];
		}
	}
	// print_r($fab_data_array_without_sample);die();
    /*==========================================================================================/
	/						grey and fin fabric	issue without sample							/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'DD-MON') as MONTH_YEAR,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=a.requisition_no and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' $greyfabPoCond  and c.issue_purpose != 31";//and c.issue_purpose != 31
	// echo $sqlgreyfab;die();//and a.requisition_no in($program_ids)
	$greyfabRes = sql_select($sqlgreyfab);
	// $scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{	
		$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		$buyer_fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
	}
	// print_r($fab_data_array_without_sample);die();
	/*==========================================================================================/
	/										batch data											/
	/==========================================================================================*/
	$batchPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sqlBatch = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, b.batch_qnty AS BATCH_QNTY from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.batch_against in(1) and to_char(d.shipment_date,'MON-YYYY')='$year' $batchPoCond";
	// echo $sqlBatch;die();
	$batchRes = sql_select($sqlBatch);
	$batch_data_array = array();
	$buyer_batch_data_array = array();
	foreach ($batchRes as $val) 
	{
        $batch_data_array[$val['MONTH_YEAR']] += $val['BATCH_QNTY'];
        $buyer_batch_data_array[$val['BUYER_NAME']] += $val['BATCH_QNTY'];
	}
	// print_r($batch_data_array);die();
	/*==========================================================================================/
	/										FF Rcv data											/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, c.quantity AS RCV_QTY, c.returnable_qnty AS REJ_QTY 
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,pro_batch_create_mst f where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type=1 $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 and f.id=b.pi_wo_batch_no and f.batch_against=1 and f.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_data_array = array();
    $buyer_finfab_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_data_array[$val['MONTH_YEAR']]['rcv_qty'] += $val['RCV_QTY'];
        $finfab_data_array[$val['MONTH_YEAR']]['rej_qty'] += $val['REJ_QTY'];

        $buyer_finfab_data_array[$val['BUYER_NAME']]['rcv_qty'] += $val['RCV_QTY'];
        $buyer_finfab_data_array[$val['BUYER_NAME']]['rej_qty'] += $val['REJ_QTY'];
	}
	// print_r($finfab_data_array);die();

	/*==========================================================================================/
	/										FF Purses Rcv data									/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(1,4) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(37,52) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and to_char(d.shipment_date,'MON-YYYY')='$year'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $buyer_finfab_purses_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $buyer_finfab_purses_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();

	/*==========================================================================================/
	/										FF Purses Issue data								/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_issue_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(2,3) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(18,46) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and to_char(d.shipment_date,'MON-YYYY')='$year'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	foreach ($finFabRes as $val) 
	{
        $buyer_finfab_purses_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();
	/*==========================================================================================/
	/									transfer data											/
	/==========================================================================================*/
	$transPoCond = str_replace("b.id", "a.to_order_id", $poCond);
	$transPoCond2 = str_replace("b.id", "a.from_order_id", $poCond);
	$sqlTrnsfrIn = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,a.ITEM_CATEGORY,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(5) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.to_order_id and to_char(d.shipment_date,'MON-YYYY')='$year' $transPoCond";
	// echo $sqlTrnsfrIn;die();
	$trnsfrResIn = sql_select($sqlTrnsfrIn);
	$transfer_in_data_array = array();
	$buyer_transfer_in_data_array = array();
	foreach ($trnsfrResIn as $val) 
	{
        $transfer_in_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
        $buyer_transfer_in_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_in_data_array);die();
	//======================================================================================
	$sqlTrnsfrOut = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,a.ITEM_CATEGORY,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(6) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.from_order_id and to_char(d.shipment_date,'MON-YYYY')='$year' $transPoCond2";
	// echo $sqlTrnsfrOut;die();
	$trnsfrResOut = sql_select($sqlTrnsfrOut);
	$transfer_out_data_array = array();
	$buyer_transfer_out_data_array = array();
	foreach ($trnsfrResOut as $val) 
	{
        $transfer_out_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
        $buyer_transfer_out_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_out_data_array);die();
	/*==========================================================================================/
	/										scrap data											/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME,to_char(e.shipment_date,'DD-MON') as MONTH_YEAR,a.ITEM_CATEGORY,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(13) and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' $greyfabPoCond and c.issue_purpose = 31";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$scrap_data_array = array();
	$buyer_scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];
		$buyer_scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];
		
	}
	// ======================================= FOR FIN FAB ====================================================	
	$greyfabPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlgreyfab = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,a.ITEM_CATEGORY_ID,a.RECEIVE_BASIS,b.RECEIVE_QNTY  from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, wo_po_details_master c,wo_po_break_down d,pro_batch_create_mst e,pro_batch_create_dtls f where a.id=b.mst_id and b.lot=e.batch_no and a.item_category_id in(2) and e.id=f.mst_id and e.batch_against=1 and c.id=d.job_id and d.id=f.po_id AND b.body_part_id=f.body_part_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' $greyfabPoCond";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_BASIS'] !=1)
		{
			$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];
			$buyer_scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];
		}
		$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];
		$buyer_scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];	
	}
	/*==========================================================================================/
	/											AOP Data 										/
	/==========================================================================================*/
	$aopPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlAop = "SELECT d.BUYER_NAME,to_char(c.shipment_date,'DD-MON') as MONTH_YEAR,b.GREY_USED,b.BATCH_ISSUE_QTY from inv_receive_mas_batchroll a, pro_grey_batch_dtls b,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and c.id=b.order_id and d.id=c.job_id and a.status_active=1 and b.status_active=1 and a.entry_form=92 and to_char(c.shipment_date,'MON-YYYY')='$year' $aopPoCond";
	// echo $sqlAop;die();
	$aopRes = sql_select($sqlAop);
    $aop_data_array = array();
    $buyer_aop_data_array = array();
	foreach ($aopRes as $val) 
	{
    	$aop_data_array[$val['MONTH_YEAR']]['grey'] 		+= $val['GREY_USED'];
    	$aop_data_array[$val['MONTH_YEAR']]['finish'] 		+= $val['BATCH_ISSUE_QTY'];

    	$buyer_aop_data_array[$val['BUYER_NAME']]['grey'] 	+= $val['GREY_USED'];
    	$buyer_aop_data_array[$val['BUYER_NAME']]['finish'] += $val['BATCH_ISSUE_QTY'];
	}
	// print_r($aop_data_array);die();



	/*==========================================================================================/
	/										cutting qc Data 									/
	/==========================================================================================*/
	$cutQcPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlcutQc = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR , b.QC_PASS_QTY , b.REJECT_QTY from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.order_id and a.status_active=1 and b.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' $cutQcPoCond";
	// echo $sqlcutQc;die();
	$cutQcRes = sql_select($sqlcutQc);
    $cutqc_data_array = array();
    $buyer_cutqc_data_array = array();
	foreach ($cutQcRes as $val) 
	{
        $cutqc_data_array[$val['MONTH_YEAR']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $cutqc_data_array[$val['MONTH_YEAR']]['qc_rej_qty'] += $val['REJECT_QTY'];

        $buyer_cutqc_data_array[$val['BUYER_NAME']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $buyer_cutqc_data_array[$val['BUYER_NAME']]['qc_rej_qty'] += $val['REJECT_QTY'];
        
	}
	// print_r($cutqc_data_array);die();

	/*==========================================================================================/
	/											gmts prod data									/
	/==========================================================================================*/
	$prodPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
    $sqlProd = "SELECT c.BUYER_NAME, to_char(d.shipment_date,'DD-MON') as MONTH_YEAR,
		
		 sum(case when a.production_type=1 THEN b.production_qnty ELSE 0 END) AS CUT_QTY
		 ,sum(case when a.production_type=1  THEN b.reject_qty ELSE 0 END) AS CUT_REJ_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS PRI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS EMBI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS WHI_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS PRR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS EMBR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS WHR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS WHRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS EMBRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS PRRJ_QTY
		 ,sum(case when a.production_type=4 THEN b.production_qnty ELSE 0 END) AS SWI_QTY
		 ,sum(case when a.production_type=5 THEN b.production_qnty ELSE 0 END) AS SWO_QTY
		 ,sum(case when a.production_type=5 THEN b.reject_qty ELSE 0 END) AS SWR_QTY
		 ,sum(case when a.production_type=8 THEN b.production_qnty ELSE 0 END) AS FIN_QTY
		 ,sum(case when a.production_type=8  THEN b.reject_qty ELSE 0 END) AS FINR_QTY
	from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=a.po_break_down_id and to_char(d.shipment_date,'MON-YYYY')='$year' $prodPoCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by d.shipment_date,c.buyer_name";
    // echo $sqlProd;die();
    $prodRes = sql_select($sqlProd);
    $gmts_data_array = array();
    $buyer_gmts_data_array = array();
	foreach ($prodRes as $val) 
	{
    	$gmts_data_array[$val['MONTH_YEAR']]['cut_qty'] 		+= $val['CUT_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['cut_rej_qty'] 	+= $val['CUT_REJ_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_issue_qty'] += $val['PRI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$gmts_data_array[$val['MONTH_YEAR']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['emb_rcv_qty'] 	+= $val['EMBR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['emb_rej_qty'] 	+= $val['EMBRJ_QTY'];

    	$gmts_data_array[$val['MONTH_YEAR']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$gmts_data_array[$val['MONTH_YEAR']]['input_qty'] 		+= $val['SWI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['output_qty'] 		+= $val['SWO_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['sew_rej_qty'] 	+= $val['SWR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['fin_qty'] 		+= $val['FIN_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['fin_rej_qty'] 	+= $val['FINR_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['cut_qty'] 			+= $val['CUT_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['cut_rej_qty'] 		+= $val['CUT_REJ_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_issue_qty'] 	+= $val['PRI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_rcv_qty'] 		+= $val['EMBR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_rej_qty'] 		+= $val['EMBRJ_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['input_qty'] 		+= $val['SWI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['output_qty'] 		+= $val['SWO_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['sew_rej_qty'] 		+= $val['SWR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['fin_qty'] 			+= $val['FIN_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['fin_rej_qty'] 		+= $val['FINR_QTY'];
	}
	// print_r($gmts_data_array);die();
	/*==========================================================================================/
	/										Ex-factory Data 									/
	/==========================================================================================*/
	$exfactPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
	$sqlExfact = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'DD-MON') as MONTH_YEAR, b.production_qnty AS EX_QTY from pro_ex_factory_mst a, pro_ex_factory_dtls b, pro_ex_factory_delivery_mst c,wo_po_break_down d,wo_po_details_master e where a.id=b.mst_id and d.id=a.po_break_down_id and c.id=a.delivery_mst_id and e.id=d.job_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' $exfactPoCond";
	// echo $sqlExfact;die();
	$exfactRes = sql_select($sqlExfact);
    $exfact_data_array = array();
    $buyer_exfact_data_array = array();
	foreach ($exfactRes as $val) 
	{
        $exfact_data_array[$val['MONTH_YEAR']] 			+= $val['EX_QTY'];
        $buyer_exfact_data_array[$val['BUYER_NAME']] 	+= $val['EX_QTY'];
	}
	// print_r($prod_data_array);die();
	/*==========================================================================================/
	/										gmt leftover Data 									/
	/==========================================================================================*/
	$leftoverPoCond = str_replace("b.id", "b.po_break_down_id", $poCond);
	$sqlLeftover = "SELECT a.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, c.PRODUCTION_QNTY from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c,wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and d.id=b.po_break_down_id and a.id=c.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' $leftoverPoCond";
	// echo $sqlLeftover;die();
	$leftoverRes = sql_select($sqlLeftover);
    $gmts_leftover_data_array = array();
    $buyer_gmts_leftover_data_array = array();
	foreach ($leftoverRes as $val) 
	{
        $gmts_leftover_data_array[$val['MONTH_YEAR']] 			+= $val['PRODUCTION_QNTY'];
        $buyer_gmts_leftover_data_array[$val['BUYER_NAME']] 	+= $val['PRODUCTION_QNTY'];
	}
	// print_r($prod_data_array);die();


	$tbl_width = 1200;
	ob_start();	
	?>
	<!-- =======================================================================================/
	/										Month Details report								/
	/======================================================================================== --> 
	<fieldset class="first_part" style="width:<? echo $tbl_width+30;?>px;margin:15px 0;">
		    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'month_details_report', '')"> -<b>Month Details : <? echo $year; ?></b><span style="color: red;font-size: 15px;font-weight: bold;"> <?=($client_id) ? ": ".$client_array[$client_id] : ": All"; ?></span></h3>
		    <div id="month_details_report">
				<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">			
			        <thead>
		        	<tr>
		        		<th width="340" colspan="5"></th>
		        		<th width="180" colspan="3">Process Loss%</th>
		        		<th width="300" colspan="5">Lost</th>
		        		<th width="120" colspan="2">Rejection</th>
		        		<th width="180" colspan="3">Leftover</th>
		        		<th width="120" colspan="2">Ship Status</th>
		        	</tr>
		        	<tr>
			            <th width="60">Buyer</th>
			            <th width="80">Total gmt</th>
			            <th width="80">Budget Yarn</th>
			            <th width="60">Extra Yarn </th>
			            <th width="60">Unauthorised Yarn</th>

			            <th width="60">YD</th>
			            <th width="60">Dyeing</th>
			            <th width="60">AOP</th>

			            <th width="60">Yarn</th>
			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>
			            
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Gmts</th>

			            <th width="60">Input to Ship</th>
			            <th width="60">Yarn to Ship</th>
		            </tr>
		        </thead>
			        <tbody>   
			        	<?   	
			        	$i=50;
			        	$gr_gmts 			= 0;
			        	$gr_budget_yarn 	= 0;
			        	$gr_extra_yarn 		= 0;
			        	$gr_unautho_yarn 	= 0;

			        	$gr_los_yd 			= 0;
			        	$gr_los_dyeing 		= 0;
			        	$gr_los_aop 		= 0;

			        	$gr_lost_yarn 		= 0;
			        	$gr_lost_grey_fab 	= 0;
			        	$gr_lost_fin_fab 	= 0;
			        	$gr_lost_gmts 		= 0;
			        	$gr_lost_cutpanel	= 0;

			        	$gr_left_grey_fab 	= 0;
			        	$gr_left_fin_fab 	= 0;
			        	$gr_left_gmts 		= 0;

			        	$gr_cut_for_fab 	= 0;
			        	$gr_cut_at_cutting 	= 0;
			        	$gr_cut_at_print 	= 0;
			        	$gr_cut_at_embro 	= 0;
			        	$gr_cut_at_sewing 	= 0;

			        	$gr_cut_panel_rej 	= 0;
			        	$gr_gmt_rej 	= 0;
			        	$gr_gmt_at_finish 	= 0;

			        	$gr_input_to_ship 	= 0;
			        	$gr_yarn_to_ship 	= 0;
			        	ksort($buyer_data_array); 
			        	// print_r($buyer_data_array);
			        	foreach ($buyer_data_array as $buyer_name => $row) 
			        	{
							$yarn_issue = 0;
							$yarn_issue_rtn = 0;
			        		$buyer_id 		= $row['buyer_id'];
		        			$yarn_knit_issue= $yarn_knit_data_array[$buyer_id][2];		        		
		        			$yarn_knit_issue_rtn= $yarn_knit_data_array[$buyer_id][4];
			        		$yarn_issue 	= $buyer_yarn_data_array[$buyer_id][2];		        		
			        		$yarn_issue_rtn = $buyer_yarn_data_array[$buyer_id][4];

			        		$grey_yarn_issue 	= $buyer_grey_yarn_data_array[$buyer_id][2];		        		
			        		$grey_yarn_issue_rtn = $buyer_grey_yarn_data_array[$buyer_id][4];
		        			$grey_yarn_issue_rej_rtn= $buyer_grey_yarn_rej_data_array[$buyer_id][4];
		        			$dyed_yarn_rtn_qty = $dyed_yarn_rej_rtn_array[$buyer_id][4]['rtn'];
		        			$dyed_yarn_rej_qty = $dyed_yarn_rej_rtn_array[$buyer_id][4]['rej'];

				        	$yarn_issue_rtn_for_dyeing = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][2]['rtn_qty'];
				        	$yarn_rej_for_dyeing = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][2]['rej_qty'];

				        	$yarn_issue_rtn_for_others = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][12]['rtn_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][15]['rtn_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][38]['rtn_qty'];
				        	$yarn_rej_for_others = $buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][12]['rej_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][15]['rej_qty']+$buyer_yarn_dyeing_rtn_rej_data_array[$buyer_id][38]['rej_qty'];

				        	$yarn_issue_for_dyeing = $buyer_yarn_dyeing_data_array[$buyer_id][2];
				        	$yarn_dyeing_used_qty = $buyer_yarn_dyeing_used_data_array[$buyer_id][2];
			        		$yarn_dyed_lost = $yarn_issue_for_dyeing - ($yarn_dyeing_used_qty+$yarn_issue_rtn_for_dyeing+$yarn_rej_for_dyeing);
				        	// ========== get twisting, reconning,re-waxing data ===========
				        	$yarn_issue_for_others = $buyer_yarn_dyeing_data_array[$buyer_id][12]+$buyer_yarn_dyeing_data_array[$buyer_id][15]+$buyer_yarn_dyeing_data_array[$buyer_id][38];
				        	$yarn_others_used_qty = $buyer_yarn_dyeing_used_data_array[$buyer_id][12]+$buyer_yarn_dyeing_used_data_array[$buyer_id][15]+$buyer_yarn_dyeing_used_data_array[$buyer_id][38];
				        	$yarn_others_lost = $yarn_issue_for_others - ($yarn_others_used_qty+$yarn_issue_rtn_for_others+$yarn_rej_for_others);

			        		$unAuthoYarn 	= ($grey_yarn_issue+$yarn_issue_for_dyeing+$yarn_issue_for_others - $grey_yarn_issue_rtn - $grey_yarn_issue_rej_rtn - $yarn_issue_rtn_for_others - $yarn_rej_for_others - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing) - ($buyer_yarn_req_array[$buyer_id] + $buyer_excess_yarn_array[$buyer_id]);

							// echo "(".$grey_yarn_issue ."+". $yarn_issue_for_dyeing."+". $yarn_issue_for_others."-". $grey_yarn_issue_rtn."-". $grey_yarn_issue_rej_rtn."-". $yarn_issue_rtn_for_others."-". $yarn_rej_for_others."-". $yarn_issue_rtn_for_dyeing."-". $yarn_rej_for_dyeing.") - (".$buyer_yarn_req_array[$buyer_id] ."+". $buyer_excess_yarn_array[$buyer_id].")<br>";
							
			        		$yarn_used_qty 	= $buyer_yarn_used_data_array[$buyer_id];
			        		$dyed_yarn_rcv_qty 	= $buyer_dyed_yarn_rcv_array[$buyer_id];

			        		$grey_yarn_lost = $yarn_knit_issue - ($yarn_used_qty + $grey_yarn_issue_rtn + $grey_yarn_issue_rej_rtn);
			        		$yarn_lost_qty 	= $grey_yarn_lost+$yarn_dyed_lost+$yarn_others_lost-($dyed_yarn_rtn_qty+$dyed_yarn_rej_qty);
							//echo $yarn_lost_qty 	."=". $yarn_issue ."- (".$yarn_issue_rtn ."+". $yarn_used_qty.")<br>";

			        		// $yarn_dyed_loss = ($yarn_issue_for_dyeing>0) ? (($yarn_issue_for_dyeing - $yarn_dyeing_used_qty - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing)/$yarn_issue_for_dyeing)*100 : 0;

		        			// new formula
		        			$yarn_dyed_loss = ($yarn_dyeing_used_qty>0) ? (($yarn_dyeing_used_qty - $dyed_yarn_rcv_qty)/$yarn_dyeing_used_qty)*100 : 0;

			        		// echo $intRef."==(".$yarn_issue_for_dyeing ."-". $yarn_dyeing_used_qty ."-". $grey_yarn_issue_rtn.")/".$yarn_issue_for_dyeing.")*100<br>";

			        		$grey_trnsf_in 	= $buyer_transfer_in_data_array[$buyer_id][13];
			        		$fin_trnsf_in 	= $buyer_transfer_in_data_array[$buyer_id][2];
			        		$grey_trnsf_out = $buyer_transfer_out_data_array[$buyer_id][13];
			        		$fin_trnsf_out 	= $buyer_transfer_out_data_array[$buyer_id][2];

			        		$grey_fab_rcv 	= $buyer_fab_data_array[$buyer_id][13][1]['receive_qnty'];
			        		$grey_fab_rcv_rtn= $buyer_fab_data_array[$buyer_id][13][3]['issue_qnty'];
			        		$grey_fab_issue = $buyer_fab_data_array[$buyer_id][13][2]['issue_qnty'];
			        		$grey_fab_iss_rtn= $buyer_fab_data_array[$buyer_id][13][4]['receive_qnty'];
			        		//
			        		$fin_fab_rcv_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][2][1]['receive_qnty'];
			        		$fin_fab_rcv_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][2][3]['issue_qnty'];
			        		$fin_fab_issue_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][2][2]['issue_qnty'];
			        		$fin_fab_iss_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][2][4]['receive_qnty'];

			        		$grey_fab_rcv_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][13][1]['receive_qnty'];
			        		$grey_fab_rcv_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][13][3]['issue_qnty'];
			        		$grey_fab_issue_without_sample 	= $buyer_fab_data_array_without_sample[$buyer_id][13][2]['issue_qnty'];
			        		$grey_fab_iss_rtn_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][13][4]['receive_qnty'];
			        		$grey_fab_rej_without_sample	= $buyer_fab_data_array_without_sample[$buyer_id][13][1]['rej_qty'];

			        		$grey_lft_over 	= $buyer_scrap_data_array[$buyer_id][13]['grey'];

			        		$grey_fab_lost 	= ($grey_fab_rcv_without_sample+$grey_trnsf_in+$grey_fab_iss_rtn_without_sample) -($grey_fab_issue_without_sample+$grey_fab_rcv_rtn_without_sample + $grey_lft_over + $grey_trnsf_out);

			        		$grey_lft_over 	= $buyer_scrap_data_array[$buyer_id][13]['grey']+$grey_fab_rej_without_sample;

			        		// echo $buyer_id."==(".$grey_fab_rcv_without_sample."+".$grey_trnsf_in."+".$grey_fab_iss_rtn_without_sample.") -(".$grey_fab_issue_without_sample."+".$grey_fab_rcv_rtn_without_sample ."+". $grey_lft_over + $grey_trnsf_out.")<br>";

			        		// $grey_fab_lost 	= ($grey_fab_rcv+$grey_trnsf_in+$grey_fab_iss_rtn) -($grey_fab_issue+$grey_trnsf_out+ $grey_fab_rcv_rtn+ $grey_lft_over);

			        		$fin_fab_rcv 	= $buyer_fab_data_array[$buyer_id][2][1]['receive_qnty'];
			        		$fin_fab_rcv_rtn= $buyer_fab_data_array[$buyer_id][2][3]['issue_qnty'];
			        		$fin_fab_issue 	= $buyer_fab_data_array[$buyer_id][2][2]['issue_qnty'];
			        		$fin_fab_iss_rtn= $buyer_fab_data_array[$buyer_id][2][4]['receive_qnty'];
			        		$fin_lft_over 	= $buyer_scrap_data_array[$buyer_id][2]['fin'];
			        		// $all_fin_lft_over 	= $buyer_scrap_data_array[$buyer_id][2]['allfin'];

			        		$fin_rcv_as_rej_qty = $buyer_fab_data_array[$buyer_id][2][1]['reject_qty'];
		        			$fin_iss_as_scrap_qty = $buyer_fab_data_array[$buyer_id][2][2]['scrap_qty'];
		        			$all_fin_lft_over = $fin_rcv_as_rej_qty + $fin_iss_as_scrap_qty;

			        		// fin fab purses data 
			        		$fin_fab_purses_rcv = $buyer_finfab_purses_data_array[$buyer_id][1]['qty'];
			        		$fin_fab_purses_issue = $buyer_finfab_purses_data_array[$buyer_id][2]['qty'];
			        		$fin_fab_purses_rcv_rtn = $buyer_finfab_purses_data_array[$buyer_id][3]['qty'];
			        		$fin_fab_purses_issue_rtn = $buyer_finfab_purses_data_array[$buyer_id][4]['qty'];

			        		$fin_fab_purses_lost = ($fin_fab_purses_rcv + $fin_fab_purses_issue_rtn) - ($fin_fab_purses_issue + $fin_fab_purses_rcv_rtn);

			        		$fin_fab_lost 	= ($fin_fab_rcv+$fin_fab_iss_rtn+$fin_trnsf_in) -($fin_fab_issue+$fin_trnsf_out+ $fin_fab_rcv_rtn) ;//+ $fin_lft_over + $fin_fab_purses_lost;
			        		// echo $buyer_id."==".$fin_fab_lost 	."== (".$fin_fab_rcv."+".$fin_fab_iss_rtn."+".$fin_trnsf_in.") -(".$fin_fab_issue."+".$fin_trnsf_out."+".$fin_fab_rcv_rtn ."+". $fin_lft_over.")<br>";

			        		$batch_qnty 	= $buyer_batch_data_array[$buyer_id];
			        		$fin_fab_rcv 	= $buyer_finfab_data_array[$buyer_id]['rcv_qty'];
			        		$fin_fab_rej 	= $buyer_finfab_data_array[$buyer_id]['rej_qty'];
			        		$dyeing_pro_los 	= ($batch_qnty>0) ? (($batch_qnty - ($fin_fab_rcv + $fin_fab_rej))/$batch_qnty)*100 : 0;

			        		$aop_grey_fab 	= $buyer_aop_data_array[$buyer_id]['grey'];
			        		$aop_fin_fab 	= $buyer_aop_data_array[$buyer_id]['finish'];
			        		$aop_pro_loss 	= ($aop_grey_fab>0) ? (($aop_grey_fab - $aop_fin_fab)/$aop_grey_fab)*100 : 0;

			        		// $cutqc_pass_qty = $buyer_cutqc_data_array[$buyer_id]['qc_pass_qty'];
			        		// $cutqc_rej_qty 	= $buyer_cutqc_data_array[$buyer_id]['qc_rej_qty'];

			        		$cutqc_pass_qty = $buyer_gmts_data_array[$buyer_id]['cut_qty'];
			        		$cutqc_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['cut_rej_qty'];

			        		$wash_issue_qty = $buyer_gmts_data_array[$buyer_id]['wash_issue_qty'];
			        		$wash_rcv_qty 	= $buyer_gmts_data_array[$buyer_id]['wash_rcv_qty'];
			        		$wash_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['wash_rej_qty'];
			        		$wash_lost_qty 	= $wash_issue_qty - $wash_rcv_qty - $wash_rej_qty;
			        		$gmt_at_wash 	= $wash_issue_qty - $wash_rcv_qty;

			        		$emb_issue_qty 	= $buyer_gmts_data_array[$buyer_id]['emb_issue_qty'];
			        		$emb_rcv_qty 	= $buyer_gmts_data_array[$buyer_id]['emb_rcv_qty'];
			        		$emb_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['emb_rej_qty'];
			        		$emb_lost_qty  	= $emb_issue_qty - $emb_rcv_qty - $emb_rej_qty ;

			        		$print_issue_qty= $buyer_gmts_data_array[$buyer_id]['print_issue_qty'];
			        		$print_rcv_qty 	= $buyer_gmts_data_array[$buyer_id]['print_rcv_qty'];
			        		$print_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['print_rej_qty'];
			        		$print_lost_qty = $print_issue_qty - $print_rcv_qty - $print_rej_qty;

			        		$sew_in_qty 	= $buyer_gmts_data_array[$buyer_id]['input_qty'];
			        		$sew_out_qty 	= $buyer_gmts_data_array[$buyer_id]['output_qty'];
			        		$sew_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['sew_rej_qty'];
			        		$ok_sew_qty 	= $sew_out_qty - $sew_rej_qty;
			        		$sewing_lost 	= $sew_in_qty - $sew_out_qty - $sew_rej_qty;
			        		// echo $intRef."==".$sew_in_qty ."-". $sew_out_qty ."-". $sew_rej_qty."<br>";

			        		$fin_rej_qty 	= $buyer_gmts_data_array[$buyer_id]['fin_rej_qty'];

			        		$cutting_lost 	= ($cutqc_pass_qty - $print_issue_qty) + (($print_rcv_qty+$print_rej_qty) - $emb_issue_qty) + (($emb_rcv_qty+$emb_rej_qty) - $sew_in_qty);
			        		$cutpanel_lost = $cutting_lost + $print_lost_qty + $sewing_lost + $emb_lost_qty;

			        		$cutpanel_reject = $cutqc_rej_qty + $print_rej_qty + $emb_rej_qty;

			        		$cut_at_cutting = $cutqc_pass_qty - ($print_lost_qty + $print_rej_qty + $emb_lost_qty + $emb_rej_qty+$sew_in_qty);
			        		$cut_at_sewing 	= $sew_in_qty - $sew_out_qty;
			        		$cut_at_embro 	= $emb_issue_qty - $emb_rcv_qty;
			        		$cut_at_print 	= $print_issue_qty - $print_rcv_qty;

			        		$ex_factory_qty = $buyer_exfact_data_array[$buyer_id];
			        		$gmt_lftovr_qty = $buyer_gmts_leftover_data_array[$buyer_id];
			        		$gmt_at_finish  = ($ok_sew_qty - ($gmt_lftovr_qty + $ex_factory_qty));

			        		$lost_gmts_qty 	= ($sew_out_qty + $sew_rej_qty) - ($gmt_lftovr_qty + $ex_factory_qty);
			        		$gmts_reject 	= $sew_rej_qty + $wash_rej_qty + $fin_rej_qty;
			        		$input_to_ship  = ($sew_in_qty>0) ? ($ex_factory_qty / $sew_in_qty)*100 : 0;
			        		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
			        		
				        	?>     
					        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					            <td>
					            	<a href="javascript:void()" onclick="report_generate_by_intref_wise('<? echo $buyer_id;?>','<? echo $year;?>','<? echo $client_id;?>')">
						            	<? echo $buyer_name;?>
						            </a>
						        </td>
					            <td align="right"><? echo number_format($row['gmts_qty'],0);?></td>
					            <td align="right"><? echo number_format($buyer_yarn_req_array[$buyer_id],0); ?></td>
					            <td align="right"><? echo number_format($buyer_excess_yarn_array[$buyer_id],0); ?></td>
					            <td align="right"><? echo number_format($unAuthoYarn,0); ?></td>

					            <td align="right"><? echo number_format($yarn_dyed_loss,2); ?></td>
					            <td align="right"><? echo number_format($dyeing_pro_los,2); ?></td>
					            <td align="right"><? echo number_format($aop_pro_loss,2); ?></td>

					            <td align="right"><? echo number_format($yarn_lost_qty,0); ?></td>
					            <td align="right"><? echo number_format($grey_fab_lost,0); ?></td>
					            <td align="right"><? echo number_format($fin_fab_lost,0); ?></td>
					            <td align="right" title="At Cutting=<? echo $cutting_lost;?>&#13;At Printing=<? echo $print_lost_qty;?>&#13;At Embroidery=<? echo $emb_lost_qty;?>&#13;At Sewing=<? echo $sewing_lost;?>">
					            	<? echo number_format($cutpanel_lost,0); ?>
					            </td>
					            <td align="right" title="At Wash = <? echo $wash_lost_qty;?>"><? echo number_format($lost_gmts_qty,0); ?></td>

					            <td align="right" title="At Cutting=<? echo $cutqc_rej_qty;?>&#13;At Printing=<? echo $print_rej_qty;?>&#13;At Embroidery=<? echo $emb_rej_qty;?>">
					            	<? echo number_format($cutpanel_reject,0); ?>
					            </td>
					            <td align="right" title="At Sewing=<? echo $sew_rej_qty;?>&#13;At Wash=<? echo $wash_rej_qty;?>&#13;At Finish=<? echo $fin_rej_qty;?>">
					            	<? echo number_format($gmts_reject,0); ?>
					            </td>

					            <td align="right"><? echo number_format($grey_lft_over,0); ?></td>
					            <td align="right"><? echo number_format($all_fin_lft_over,0); ?></td>
					            <td align="right"><? echo number_format($gmt_lftovr_qty,0); ?></td>

					            <td align="right"><? echo number_format($input_to_ship,2); ?></td>
					            <td align="right"><? echo number_format($a,2); ?></td>
					        </tr>
					        <? 
					        $i++;
					        $gr_gmts 			+= $row['gmts_qty'];
				        	$gr_budget_yarn 	+= $buyer_yarn_req_array[$buyer_id];
				        	$gr_extra_yarn 		+= $buyer_excess_yarn_array[$buyer_id];
				        	$gr_unautho_yarn 	+= $unAuthoYarn;

				        	$gr_los_yd 			+= $a;
				        	$gr_los_dyeing 		+= $dyeing_pro_los;
				        	$gr_los_aop 		+= $aop_pro_loss;

				        	$gr_lost_yarn 		+= $yarn_lost_qty;
				        	$gr_lost_grey_fab 	+= $grey_fab_lost;
				        	$gr_lost_fin_fab 	+= $fin_fab_lost;
				        	$gr_lost_gmts 		+= $lost_gmts_qty;
				        	$gr_lost_cutpanel 	+= $cutpanel_lost;

				        	$gr_left_grey_fab 	+= $grey_lft_over;
				        	$gr_left_fin_fab 	+= $all_fin_lft_over;
				        	$gr_left_gmts 		+= $gmt_lftovr_qty;

				        	$gr_cut_for_fab 	+= $cutqc_rej_qty;
				        	$gr_cut_at_cutting 	+= $cut_at_cutting;
				        	$gr_cut_at_print 	+= $cut_at_print;
				        	$gr_cut_at_embro 	+= $cut_at_embro;
				        	$gr_cut_at_sewing 	+= $cut_at_sewing;

				        	$gr_cut_panel_rej 	+= $cutpanel_reject;
				        	$gr_gmt_rej 	+= $gmts_reject;
				        	$gr_gmt_at_finish 	+= $gmt_at_finish;

				        	$gr_input_to_ship 	+= $input_to_ship;
				        	$gr_yarn_to_ship 	+= $a;
					    }
					    ?>
			        </tbody>
			        <tfoot>
			            <th align="right">Total</th>
			            <th><? echo number_format($gr_gmts,0); ?></th>
			            <th><? echo number_format($gr_budget_yarn,0); ?></th>
			            <th><? echo number_format($gr_extra_yarn,0); ?></th>
			            <th><? echo number_format($gr_unautho_yarn,0); ?></th>

			            <th><? //echo number_format($gr_los_yd,0); ?></th>
			            <th><? //echo number_format($gr_los_dyeing,2); ?></th>
			            <th><? //echo number_format($gr_los_aop,2); ?></th>

			            <th><? echo number_format($gr_lost_yarn,0); ?></th>
			            <th><? echo number_format($gr_lost_grey_fab,0); ?></th>
			            <th><? echo number_format($gr_lost_fin_fab,0); ?></th>
			            <th><? echo number_format($gr_lost_cutpanel,0); ?></th>
			            <th><? echo number_format($gr_lost_gmts,0); ?></th>

			            <th><? echo number_format($gr_cut_panel_rej,0); ?></th>
			            <th><? echo number_format($gr_gmt_rej,0); ?></th>

			            <th><? echo number_format($gr_left_grey_fab,0); ?></th>
			            <th><? echo number_format($gr_left_fin_fab,0); ?></th>
			            <th><? echo number_format($gr_left_gmts,0); ?></th>

			            <th><? //echo number_format($gr_input_to_ship,2); ?></th>
			            <th><? //echo number_format($gr_yarn_to_ship,2); ?></th>
			        </tfoot>
			    </table>      
		    </div>    
		</fieldset>
			
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

if($action=="report_generate_by_buyer_month_year")// report generate when buyer click
{
	$company_id 	= str_replace("'","",$cbo_company_id);
	$location_id 	= str_replace("'","",$cbo_location_id);
	$client_id 		= str_replace("'","",$cbo_client);
	$order_status	= str_replace("'","",$cbo_order_status);
	$year 	= str_replace("'","",$month_year);
	$buyer_id 	= str_replace("'","",$buyer);

	$sqlCond = "";
	$sqlCond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
	$sqlCond .= ($location_id !=0) ? " and a.location_name=$location_id" : "";
	$sqlCond .= ($client_id != 0) ? " and a.client_id=$client_id" : "";
	if($order_status !=0)
	{
		$sqlCond .= ($order_status == 1) ? " and b.shiping_status in(1,2)" : " and b.shiping_status in(3)";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$client_array = return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$company_id' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name", "id", "buyer_name");
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$fiscal_month_arr = array();
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
				$fiscal_month_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscal_month_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscal_month_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/	
	$sql_gmt="SELECT a.JOB_NO,a.BUYER_NAME,a.TOTAL_SET_QNTY,b.id as PO_ID,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(b.po_quantity*a.total_set_qnty) AS QTY
	 from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id  $sqlCond and b.shipment_date between '$startDate' and '$endDate' and a.buyer_name=$buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no,a.buyer_name,a.total_set_qnty,b.id,b.shipment_date";
	// echo $sql_gmt;die();
	$sql_gmt_res = sql_select($sql_gmt);
	if(!count($sql_gmt_res))
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}

	$po_id_array = array();
	$gmt_array = array();
	$buyer_data_array = array();
	foreach ($sql_gmt_res as $val) 
	{	
		$gmt_array[$val['MONTH_YEAR']] += $val['QTY'];			
		$buyer_data_array[$buyer_arr[$val['BUYER_NAME']]]['gmts_qty'] += $val['QTY'];			
		$buyer_data_array[$buyer_arr[$val['BUYER_NAME']]]['buyer_id'] = $val['BUYER_NAME'];			
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];	
	}
	// print_r($buyer_data_array);die();
	unset($sql_gmt_res);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	/*==========================================================================================/
	/								yarn req qty from budget									/
	/==========================================================================================*/
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);

    $sql = "SELECT a.job_no AS JOB_NO,a.BUYER_NAME ,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR,b.grouping as INT_REF,b.id AS id,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS country_id,c.color_number_id AS color_number_id,c.size_number_id AS size_number_id,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,c.country_ship_date AS country_ship_date,d.id AS pre_cost_dtls_id,d.fab_nature_id AS fab_nature_id,d.construction AS construction, d.gsm_weight AS gsm_weight,e.cons AS cons,e.requirment AS REQUIRMENT,f.id AS yarn_id,f.count_id AS count_id,f.copm_one_id AS copm_one_id,f.percent_one AS percent_one,f.type_id AS type_id,f.color AS color,f.cons_ratio AS CONS_RATIO,f.cons_qnty AS cons_qnty,f.avg_cons_qnty AS avg_cons_qnty,f.rate AS rate,f.amount AS amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_id $poCond and b.shipment_date between '$startDate' and '$endDate' and a.buyer_name=$buyer_id";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$yarn_req_array = array();
	$buyer_yarn_req_array = array();
	foreach ($sql_res as $key => $row) 
	{		
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;

        $gmtsitemRatio 	= $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio 		= $row['CONS_RATIO'];
        $requirment 	= $row['REQUIRMENT'];
        $consQnty 		= $requirment*$consRatio/100;
        $reqQty 		= ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);	
        // echo $row['JOB_NO']."==".$reqQty 		."= (".$row[$plan_cut_year]."/".$gmtsitemRatio.")*(".$consQnty."/".$pcs_value.")<br>";
		
		($reqQty>0)?$yarn_req_array[$row['MONTH_YEAR']] += $reqQty:"";
		($reqQty>0)?$buyer_yarn_req_array[$row['BUYER_NAME']] += $reqQty:"";
		
	}
	unset($sql_res);
	// print_r($yarn_req_array);die();
	/*==========================================================================================/
	/									get req. and program id									/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sql_plan = "SELECT c.requisition_no as REQUISITION_ID, c.knit_id as  PROGRAM_ID from wo_booking_mst a, ppl_planning_entry_plan_dtls b, ppl_yarn_requisition_entry c where a.booking_no=b.booking_no and b.dtls_id=c.knit_id and a.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 $bookingPoCond";
	// echo $sql_plan;
	$plan_res = sql_select($sql_plan);
	$program_id_array = array();
	$requisition_id_array = array();
	foreach ($plan_res as $val) 
	{
		$program_id_array[$val['PROGRAM_ID']] = $val['PROGRAM_ID'];
		$requisition_id_array[$val['REQUISITION_ID']] = $val['REQUISITION_ID'];
	}
	$program_ids = implode(",", $program_id_array);
	$requisition_ids = implode(",", $requisition_id_array);

	/*==========================================================================================/
	/										excess yarn											/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlExcessYarn = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(b.grey_fab_qnty) AS GREY_FAB_QNTY FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $bookingPoCond and c.buyer_name=$buyer_id group by c.buyer_name,d.shipment_date order by d.shipment_date";
    // echo $sqlExcessYarn;die();
    $exYarnRes = sql_select($sqlExcessYarn);
    $excess_yarn_array = array();
    $buyer_excess_yarn_array = array();
    foreach ($exYarnRes as $val) 
    {			
        $excess_yarn_array[$val['MONTH_YEAR']] += $val['GREY_FAB_QNTY'];       
        $buyer_excess_yarn_array[$val['BUYER_NAME']] += $val['GREY_FAB_QNTY'];       
    }
    unset($exYarnRes);
    /*==========================================================================================/
	/										yarn receive										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnRcv = "SELECT a.TRANSACTION_TYPE,e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond";
	// echo $sqlYArnRcv;die();
	$yarnRcvRes = sql_select($sqlYArnRcv);
	$yarn_data_array = array();
	$buyer_yarn_data_array = array();
	foreach ($yarnRcvRes as $val) 
	{
        $yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_data_array);die();
    /*==========================================================================================/
	/										yarn issue											/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT e.BUYER_NAME,c.ISSUE_PURPOSE,a.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond and a.requisition_no in($requisition_ids)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_knit_data_array = array();
	foreach ($yarnISsueRes as $val) 
	{
		if($val['ISSUE_PURPOSE']==1)
		{
        	$yarn_knit_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        }
        $yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_data_array);die();

    /*==========================================================================================/
	/						yarn issue for dyeing,twisting,recon....							/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT c.ID,e.BUYER_NAME,a.TRANSACTION_TYPE,c.ISSUE_PURPOSE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' and e.buyer_name=$buyer_id $yarnPoCond and c.issue_purpose in(2,12,15,38)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_dyeing_data_array = array();
	$buyer_yarn_dyeing_data_array = array();
	$issue_id_array = array();
	$issue_purpose_array = array();
	foreach ($yarnISsueRes as $val) 
	{
        $yarn_dyeing_data_array[$val['MONTH_YEAR']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $buyer_yarn_dyeing_data_array[$val['BUYER_NAME']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $issue_id_array[$val['ID']] = $val['ID'];
        $issue_purpose_array[$val['ID']] = $val['ISSUE_PURPOSE'];
	}
	$issueIds = implode(",", $issue_id_array);
	// print_r($yarn_dyeing_data_array);die(); 

	/*==========================================================================================/
	/						get yarn used qty for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,c.BUYER_NAME,a.RECEIVE_PURPOSE,b.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.GREY_QUANTITY as USED_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2,12,15,38) and d.shipment_date between '$startDate' and '$endDate' and c.buyer_name=$buyer_id $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_used_data_array = array();
	$buyer_yarn_dyeing_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_used_data_array[$val['MONTH_YEAR']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
		$buyer_yarn_dyeing_used_data_array[$val['BUYER_NAME']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
	}
	// print_r($yarn_dyeing_used_data_array);die(); 

	/*==========================================================================================/
	/					get yarn rej & rtn qty for dyeing,twisting,recon....					/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.ISSUE_ID,a.RECV_NUMBER,c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.CONS_QUANTITY as RTN_QTY,b.cons_reject_qnty as REJ_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id  and c.id=d.job_id and b.item_category=1  and a.entry_form=9 and b.transaction_type in(4) and e.trans_id=b.id and d.id=e.po_breakdown_id and b.transaction_type=e.trans_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose is null and d.shipment_date between '$startDate' and '$endDate' and c.buyer_name=$buyer_id $yarnPoCond  and a.issue_id in($issueIds)";//and a.booking_id in($yd_wo_ids)
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_rtn_rej_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_rtn_rej_data_array[$val['MONTH_YEAR']][$issue_purpose_array[$val['ISSUE_ID']]]['rtn_qty'] += $val['RTN_QTY'];
		$yarn_dyeing_rtn_rej_data_array[$val['MONTH_YEAR']][$issue_purpose_array[$val['ISSUE_ID']]]['rej_qty'] += $val['REJ_QTY'];
	}
	// echo "<pre>";print_r($yarn_dyeing_rtn_rej_data_array);die();

	/*==========================================================================================/
	/									get dyed yarn rcv qty									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,c.BUYER_NAME,a.RECEIVE_PURPOSE,b.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, f.QUANTITY from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e,order_wise_pro_details f where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and b.item_category=1 and f.trans_id=b.id and d.id=f.po_breakdown_id and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) and d.shipment_date between '$startDate' and '$endDate' and c.buyer_name=$buyer_id $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$dyed_yarn_rcv_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$dyed_yarn_rcv_array[$val['MONTH_YEAR']] += $val['QUANTITY'];
	}
	// print_r($yarn_dyeing_used_data_array);die(); 
    /*==========================================================================================/
	/									get yarn used qty										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnUsd = "SELECT e.BUYER_NAME,to_char(f.shipment_date,'MON-YYYY') as MONTH_YEAR, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master a, order_wise_pro_details b,inv_transaction d,wo_po_details_master e,wo_po_break_down f where d.id=b.trans_id and d.prod_id=b.prod_id and e.id=f.job_id and f.id=b.po_breakdown_id and d.item_category=13 and b.entry_form=2 and d.transaction_type in(1) and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.buyer_name=$buyer_id and f.shipment_date between '$startDate' and '$endDate' $yarnPoCond and a.booking_id in($program_ids)";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_used_data_array[$val['MONTH_YEAR']] += $val['QUANTITY'] + $val['RETURNABLE_QNTY'];
        $buyer_yarn_used_data_array[$val['BUYER_NAME']] += $val['QUANTITY'] + $val['RETURNABLE_QNTY'];
	}
	// print_r($yarn_used_data_array);die();
    /*==========================================================================================/
	/										grey yarn issue										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssue = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond and a.requisition_no in($requisition_ids)";
	// echo $sqlGreyYArnIssue;die();
	$greyYarnISsueRes = sql_select($sqlGreyYArnIssue);
	$grey_yarn_data_array = array();
	$buyer_grey_yarn_data_array = array();
	foreach ($greyYarnISsueRes as $val) 
	{
        $grey_yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        $buyer_grey_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];
    }
    /*==========================================================================================/
	/										grey yarn issue	rtn									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssueRtn = "SELECT e.BUYER_NAME,a.TRANSACTION_TYPE,f.DYED_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY,b.REJECT_QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and d.id=b.po_breakdown_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2,1) and f.item_category_id=1 and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $yarnPoCond and c.booking_id in($requisition_ids)";
	// echo $sqlGreyYArnIssueRtn;die();
	$greyYarnIssueRtnRes = sql_select($sqlGreyYArnIssueRtn);
	$grey_yarn_rej_data_array = array();
	$buyer_grey_yarn_rej_data_array = array();
	$dyed_yarn_rej_rtn_array = array();
	foreach ($greyYarnIssueRtnRes as $val) 
	{
		if($val['DYED_TYPE']==1)
		{
			$dyed_yarn_rej_rtn_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['rtn'] += $val['QTY'];
			$dyed_yarn_rej_rtn_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['rej'] += $val['REJECT_QTY'];
		}
		else
		{
	        $grey_yarn_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	        $buyer_grey_yarn_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['QTY'];

	        $grey_yarn_rej_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
	        $buyer_grey_yarn_rej_data_array[$val['BUYER_NAME']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
	    }
    }
    // echo "<pre>"; print_r($grey_yarn_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	rcv 								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME, a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(1,4) and f.id=a.pi_wo_batch_no  and f.status_active=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.buyer_name=$buyer_id and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array = array();
	$buyer_fab_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['reject_qty'] += $val['RETURNABLE_QNTY'];
			$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
		}
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/								grey and fin fabric	issue									/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR,  b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(2,3) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.buyer_name=$buyer_id and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";// and c.issue_purpose != 31
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $fab_data_array = array();
	$scrap_data_array = array();
	$buyer_scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		$buyer_fab_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		if($val['ISSUE_PURPOSE']==31)
		{
			$fab_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['scrap_qty'] += $val['QUANTITY'];
		}
		
	}
	// print_r($fab_data_array);die();
	/*==========================================================================================/
	/						grey and fin fabric	rcv	without sample								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT c.recv_number,e.grouping as INT_REF, a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR, b.QUANTITY,b.returnable_qnty as REJ_QTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=c.booking_id and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.buyer_name=$buyer_id and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";// and c.booking_id in($program_ids)
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array_without_sample = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['rej_qty'] += $val['REJ_QTY'];
		}
	}
	// print_r($fab_data_array_without_sample);die();
    /*==========================================================================================/
	/						grey and fin fabric	issue without sample							/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT e.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=a.requisition_no and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.buyer_name=$buyer_id and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";//and c.issue_purpose != 31   and a.requisition_no in($program_ids)
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{	
		$fab_data_array_without_sample[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		$fab_data_array_without_sample[$val['BUYER_NAME']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
	}
	// print_r($fab_data_array_without_sample);die();
	/*==========================================================================================/
	/										batch data											/
	/==========================================================================================*/
	$batchPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sqlBatch = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.batch_qnty AS BATCH_QNTY from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.batch_against in(1) and c.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $batchPoCond";
	// echo $sqlBatch;die();
	$batchRes = sql_select($sqlBatch);
	$batch_data_array = array();
	$buyer_batch_data_array = array();
	foreach ($batchRes as $val) 
	{
        $batch_data_array[$val['MONTH_YEAR']] += $val['BATCH_QNTY'];
        $buyer_batch_data_array[$val['BUYER_NAME']] += $val['BATCH_QNTY'];
	}
	// print_r($batch_data_array);die();
	/*==========================================================================================/
	/										FF Rcv data											/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, c.quantity AS RCV_QTY, c.returnable_qnty AS REJ_QTY 
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,pro_batch_create_mst f where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type=1 $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 and f.id=b.pi_wo_batch_no and f.batch_against=1 and f.status_active=1 and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_data_array = array();
    $buyer_finfab_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_data_array[$val['MONTH_YEAR']]['rcv_qty'] += $val['RCV_QTY'];
        $finfab_data_array[$val['MONTH_YEAR']]['rej_qty'] += $val['REJ_QTY'];

        $buyer_finfab_data_array[$val['BUYER_NAME']]['rcv_qty'] += $val['RCV_QTY'];
        $buyer_finfab_data_array[$val['BUYER_NAME']]['rej_qty'] += $val['REJ_QTY'];
	}
	// print_r($finfab_data_array);die();

	/*==========================================================================================/
	/										FF Purses Rcv data									/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(1,4) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(37,52) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_purses_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();

	/*==========================================================================================/
	/										FF Purses Issue data								/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_issue_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(2,3) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(18,46) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate'";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['MONTH_YEAR']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();
	/*==========================================================================================/
	/									transfer data											/
	/==========================================================================================*/
	$transPoCond = str_replace("b.id", "a.to_order_id", $poCond);
	$transPoCond2 = str_replace("b.id", "a.from_order_id", $poCond);
	$sqlTrnsfrIn = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(5) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.to_order_id and c.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $transPoCond";
	// echo $sqlTrnsfr;die();
	$trnsfrResIn = sql_select($sqlTrnsfrIn);
	$transfer_in_data_array = array();
	$buyer_transfer_in_data_array = array();
	foreach ($trnsfrResIn as $val) 
	{
        $transfer_in_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
        $buyer_transfer_in_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_in_data_array);die();
	//======================================================================================
	$sqlTrnsfrOut = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(6) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.from_order_id and c.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $transPoCond2";
	// echo $sqlTrnsfrOut;die();
	$trnsfrResOut = sql_select($sqlTrnsfrOut);
	$transfer_out_data_array = array();
	$buyer_transfer_out_data_array = array();
	foreach ($trnsfrResOut as $val) 
	{
        $transfer_out_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
        $buyer_transfer_out_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_out_data_array);die();
	/*==========================================================================================/
	/										scrap data											/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT d.BUYER_NAME,to_char(e.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(13) and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.buyer_name=$buyer_id and e.shipment_date between '$startDate' and '$endDate' $greyfabPoCond and c.issue_purpose = 31";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];
        $scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];
		
	}
	// ======================================= FOR FIN FAB ====================================================	
	$greyfabPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlgreyfab = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,a.ITEM_CATEGORY_ID,a.RECEIVE_BASIS,b.RECEIVE_QNTY  from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, wo_po_details_master c,wo_po_break_down d,pro_batch_create_mst e,pro_batch_create_dtls f where a.id=b.mst_id and b.lot=e.batch_no and a.item_category_id in(2) and e.id=f.mst_id and e.batch_against=1 and c.id=d.job_id and d.id=f.po_id AND b.body_part_id=f.body_part_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.shipment_date between '$startDate' and '$endDate' $greyfabPoCond";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_BASIS'] !=1)
		{
			$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];
			$scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];
		}
		$scrap_data_array[$val['MONTH_YEAR']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];
		$scrap_data_array[$val['BUYER_NAME']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];	
	}
	/*==========================================================================================/
	/											AOP Data 										/
	/==========================================================================================*/
	$aopPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlAop = "SELECT d.BUYER_NAME,to_char(c.shipment_date,'MON-YYYY') as MONTH_YEAR,b.GREY_USED,b.BATCH_ISSUE_QTY from inv_receive_mas_batchroll a, pro_grey_batch_dtls b,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and c.id=b.order_id and d.id=c.job_id and a.status_active=1 and b.status_active=1 and a.entry_form=92 and d.buyer_name=$buyer_id and c.shipment_date between '$startDate' and '$endDate' $aopPoCond";
	// echo $sqlAop;die();
	$aopRes = sql_select($sqlAop);
    $aop_data_array = array();
    $buyer_aop_data_array = array();
	foreach ($aopRes as $val) 
	{
    	$aop_data_array[$val['MONTH_YEAR']]['grey'] 		+= $val['GREY_USED'];
    	$aop_data_array[$val['MONTH_YEAR']]['finish'] 		+= $val['BATCH_ISSUE_QTY'];

    	$buyer_aop_data_array[$val['BUYER_NAME']]['grey'] 	+= $val['GREY_USED'];
    	$buyer_aop_data_array[$val['BUYER_NAME']]['finish'] 	+= $val['BATCH_ISSUE_QTY'];
	}
	// print_r($aop_data_array);die();



	/*==========================================================================================/
	/										cutting qc Data 									/
	/==========================================================================================*/
	$cutQcPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlcutQc = "SELECT c.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR , b.QC_PASS_QTY , b.REJECT_QTY from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.order_id and a.status_active=1 and b.status_active=1 and c.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $cutQcPoCond";
	// echo $sqlcutQc;die();
	$cutQcRes = sql_select($sqlcutQc);
    $cutqc_data_array = array();
    $buyer_cutqc_data_array = array();
	foreach ($cutQcRes as $val) 
	{
        $cutqc_data_array[$val['MONTH_YEAR']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $cutqc_data_array[$val['MONTH_YEAR']]['qc_rej_qty'] += $val['REJECT_QTY'];

        $buyer_cutqc_data_array[$val['BUYER_NAME']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $buyer_cutqc_data_array[$val['BUYER_NAME']]['qc_rej_qty'] += $val['REJECT_QTY'];
        
	}
	// print_r($cutqc_data_array);die();

	/*==========================================================================================/
	/											gmts prod data									/
	/==========================================================================================*/
	$prodPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
    $sqlProd = "SELECT c.BUYER_NAME, to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,
		
		 sum(case when a.production_type=1 THEN b.production_qnty ELSE 0 END) AS CUT_QTY
		 ,sum(case when a.production_type=1  THEN b.reject_qty ELSE 0 END) AS CUT_REJ_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS PRI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS EMBI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS WHI_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS PRR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS EMBR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS WHR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS WHRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS EMBRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS PRRJ_QTY
		 ,sum(case when a.production_type=4 THEN b.production_qnty ELSE 0 END) AS SWI_QTY
		 ,sum(case when a.production_type=5 THEN b.production_qnty ELSE 0 END) AS SWO_QTY
		 ,sum(case when a.production_type=5 THEN b.reject_qty ELSE 0 END) AS SWR_QTY
		 ,sum(case when a.production_type=8 THEN b.production_qnty ELSE 0 END) AS FIN_QTY
		 ,sum(case when a.production_type=8  THEN b.reject_qty ELSE 0 END) AS FINR_QTY
	from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=a.po_break_down_id and c.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $prodPoCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by d.shipment_date,c.buyer_name";
    // echo $sqlProd;die();
    $prodRes = sql_select($sqlProd);
    $gmts_data_array = array();
    $buyer_gmts_data_array = array();
	foreach ($prodRes as $val) 
	{
    	$gmts_data_array[$val['MONTH_YEAR']]['cut_qty'] 		+= $val['CUT_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['cut_rej_qty'] 	+= $val['CUT_REJ_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_issue_qty'] += $val['PRI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$gmts_data_array[$val['MONTH_YEAR']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['emb_rcv_qty'] 	+= $val['EMBR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['emb_rej_qty'] 	+= $val['EMBRJ_QTY'];

    	$gmts_data_array[$val['MONTH_YEAR']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$gmts_data_array[$val['MONTH_YEAR']]['input_qty'] 		+= $val['SWI_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['output_qty'] 		+= $val['SWO_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['sew_rej_qty'] 	+= $val['SWR_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['fin_qty'] 		+= $val['FIN_QTY'];
    	$gmts_data_array[$val['MONTH_YEAR']]['fin_rej_qty'] 	+= $val['FINR_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['cut_qty'] 			+= $val['CUT_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['cut_rej_qty'] 		+= $val['CUT_REJ_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_issue_qty'] 	+= $val['PRI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_rcv_qty'] 		+= $val['EMBR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['emb_rej_qty'] 		+= $val['EMBRJ_QTY'];

    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['input_qty'] 		+= $val['SWI_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['output_qty'] 		+= $val['SWO_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['sew_rej_qty'] 		+= $val['SWR_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['fin_qty'] 			+= $val['FIN_QTY'];
    	$buyer_gmts_data_array[$val['BUYER_NAME']]['fin_rej_qty'] 		+= $val['FINR_QTY'];
	}
	// print_r($gmts_data_array);die();
	/*==========================================================================================/
	/										Ex-factory Data 									/
	/==========================================================================================*/
	$exfactPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
	$sqlExfact = "SELECT e.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, b.production_qnty AS EX_QTY from pro_ex_factory_mst a, pro_ex_factory_dtls b, pro_ex_factory_delivery_mst c,wo_po_break_down d,wo_po_details_master e where a.id=b.mst_id and d.id=a.po_break_down_id and c.id=a.delivery_mst_id and d.job_id=e.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $exfactPoCond";
	// echo $sqlExfact;die();
	$exfactRes = sql_select($sqlExfact);
    $exfact_data_array = array();
    $buyer_exfact_data_array = array();
	foreach ($exfactRes as $val) 
	{
        $exfact_data_array[$val['MONTH_YEAR']] 			+= $val['EX_QTY'];
        $buyer_exfact_data_array[$val['BUYER_NAME']] 		+= $val['EX_QTY'];
	}
	// print_r($prod_data_array);die();
	/*==========================================================================================/
	/										gmt leftover Data 									/
	/==========================================================================================*/
	$leftoverPoCond = str_replace("b.id", "b.po_break_down_id", $poCond);
	$sqlLeftover = "SELECT a.BUYER_NAME,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR, c.PRODUCTION_QNTY from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c,wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and d.id=b.po_break_down_id and a.id=c.mst_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.buyer_name=$buyer_id and d.shipment_date between '$startDate' and '$endDate' $leftoverPoCond";
	// echo $sqlLeftover;die();
	$leftoverRes = sql_select($sqlLeftover);
    $gmts_leftover_data_array = array();
    $buyer_gmts_leftover_data_array = array();
	foreach ($leftoverRes as $val) 
	{
        $gmts_leftover_data_array[$val['MONTH_YEAR']] 			+= $val['PRODUCTION_QNTY'];
        $buyer_gmts_leftover_data_array[$val['BUYER_NAME']] 	+= $val['PRODUCTION_QNTY'];
	}
	// print_r($prod_data_array);die();


	$tbl_width = 1200;
	ob_start();	
	?>
	<!-- =======================================================================================/
	/										buyer details report								/
	/======================================================================================== --> 
    <fieldset class="first_part" style="width:<? echo $tbl_width+30;?>px;margin:15px 0;">
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'buyer_details_report', '')"> -<b>Buyer Details: <? echo $buyer_arr[$buyer_id]; ?></b><span style="color: red;font-size: 15px;font-weight: bold;"> <?=($client_id) ? ": ".$client_array[$client_id] : ": All"; ?></span></h3>
	    <div id="buyer_details_report">
			<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		
	        <thead>
		        	<tr>
		        		<th width="340" colspan="5"></th>
		        		<th width="180" colspan="3">Process Loss%</th>
		        		<th width="300" colspan="5">Lost</th>
		        		<th width="120" colspan="2">Rejection</th>
		        		<th width="180" colspan="3">Leftover</th>
		        		<th width="120" colspan="2">Ship Status</th>
		        	</tr>
		        	<tr>
			            <th width="60">Month</th>
			            <th width="80">Total gmt</th>
			            <th width="80">Budget Yarn</th>
			            <th width="60">Extra Yarn </th>
			            <th width="60">Unauthorised Yarn</th>

			            <th width="60">YD</th>
			            <th width="60">Dyeing</th>
			            <th width="60">AOP</th>

			            <th width="60">Yarn</th>
			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Gmts</th>

			            <th width="60">Input to Ship</th>
			            <th width="60">Yarn to Ship</th>
		            </tr>
		        </thead>
		        <tbody>   
		        <?		
		        	$i=70;	        	
		        	$gr_gmts 			= 0;
		        	$gr_budget_yarn 	= 0;
		        	$gr_extra_yarn 		= 0;
		        	$gr_unautho_yarn 	= 0;

		        	$gr_los_yd 			= 0;
		        	$gr_los_dyeing 		= 0;
		        	$gr_los_aop 		= 0;

		        	$gr_lost_yarn 		= 0;
		        	$gr_lost_grey_fab 	= 0;
		        	$gr_lost_fin_fab 	= 0;
		        	$gr_lost_gmts 		= 0;
		        	$gr_lost_cutpanel	= 0;

		        	$gr_left_grey_fab 	= 0;
		        	$gr_left_fin_fab 	= 0;
		        	$gr_left_gmts 		= 0;

		        	$gr_cut_for_fab 	= 0;
		        	$gr_cut_at_cutting 	= 0;
		        	$gr_cut_at_print 	= 0;
		        	$gr_cut_at_embro 	= 0;
		        	$gr_cut_at_sewing 	= 0;

		        	$gr_cut_panel_rej 	= 0;
		        	$gr_gmt_rej 	= 0;
		        	$gr_gmt_at_finish 	= 0;

		        	$gr_input_to_ship 	= 0;
		        	$gr_yarn_to_ship 	= 0;
		        	foreach ($fiscal_month_arr as $monyear => $val) 
		        	{
		        		$yarn_knit_issue= $yarn_knit_data_array[$monyear][2];		        		
		        		$yarn_knit_issue_rtn= $yarn_knit_data_array[$monyear][4];
		        		$yarn_issue 	= $yarn_data_array[$monyear][2];		        		
		        		$yarn_issue_rtn = $yarn_data_array[$monyear][4];

		        		$grey_yarn_issue 	= $grey_yarn_data_array[$monyear][2];		        		
		        		$grey_yarn_issue_rtn = $grey_yarn_data_array[$monyear][4];
		        		$grey_yarn_issue_rej_rtn= $grey_yarn_rej_data_array[$monyear][4];
		        		$dyed_yarn_rtn_qty = $dyed_yarn_rej_rtn_array[$monyear][4]['rtn'];
		        		$dyed_yarn_rej_qty = $dyed_yarn_rej_rtn_array[$monyear][4]['rej'];

			        	$yarn_issue_rtn_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$monyear][2]['rtn_qty'];
			        	$yarn_rej_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$monyear][2]['rej_qty'];

			        	$yarn_issue_rtn_for_others = $yarn_dyeing_rtn_rej_data_array[$monyear][12]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][15]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][38]['rtn_qty'];
			        	$yarn_rej_for_others = $yarn_dyeing_rtn_rej_data_array[$monyear][12]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][15]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$monyear][38]['rej_qty'];

			        	$yarn_issue_for_dyeing = $yarn_dyeing_data_array[$monyear][2];
			        	$yarn_dyeing_used_qty = $yarn_dyeing_used_data_array[$monyear][2];
			        	$yarn_dyed_lost = $yarn_issue_for_dyeing - ($yarn_dyeing_used_qty+$yarn_issue_rtn_for_dyeing+$yarn_rej_for_dyeing);
			        	// echo $monyear."**".$yarn_issue_for_dyeing ."- (".$yarn_dyeing_used_qty."+".$yarn_issue_rtn_for_dyeing."+".$yarn_rej_for_dyeing.")<br>";
			        	// ========== get twisting, reconning,re-waxing data ===========
			        	$yarn_issue_for_others = $yarn_dyeing_data_array[$monyear][12]+$yarn_dyeing_data_array[$monyear][15]+$yarn_dyeing_data_array[$monyear][38];
			        	$yarn_others_used_qty = $yarn_dyeing_used_data_array[$monyear][12]+$yarn_dyeing_used_data_array[$monyear][15]+$yarn_dyeing_used_data_array[$monyear][38];
			        	$yarn_others_lost = $yarn_issue_for_others - ($yarn_others_used_qty+$yarn_issue_rtn_for_others+$yarn_rej_for_others);

		        		$unAuthoYarn 	= ($grey_yarn_issue+$yarn_issue_for_dyeing+$yarn_issue_for_others - $grey_yarn_issue_rtn - $grey_yarn_issue_rej_rtn - $yarn_issue_rtn_for_others - $yarn_rej_for_others - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing) - ($yarn_req_array[$monyear] + $excess_yarn_array[$monyear]);

		        		// echo $monyear."**".$grey_yarn_issue."+".$yarn_issue_for_dyeing."+".$yarn_issue_for_others ."-". $grey_yarn_issue_rtn ."-". $grey_yarn_issue_rej_rtn ."-". $yarn_issue_rtn_for_others ."-". $yarn_rej_for_others ."-". $yarn_issue_rtn_for_dyeing ."-". $yarn_rej_for_dyeing.") - (".$yarn_req_array[$monyear] ."+". $excess_yarn_array[$monyear].")<br>";

			        	$yarn_used_qty 	= $yarn_used_data_array[$monyear];
			        	$dyed_yarn_rcv_qty 	= $dyed_yarn_rcv_array[$monyear];

			        	$grey_yarn_lost = $yarn_knit_issue - ($yarn_used_qty + $grey_yarn_issue_rtn + $grey_yarn_issue_rej_rtn);
			        	// echo $monyear."==".$yarn_knit_issue ."- (".$yarn_used_qty ."+". $grey_yarn_issue_rtn ."+". $grey_yarn_issue_rej_rtn.")<br>";
			        	$yarn_lost_qty 	= $grey_yarn_lost+$yarn_dyed_lost+$yarn_others_lost-($dyed_yarn_rtn_qty+$dyed_yarn_rej_qty);
			        	// echo $monyear."==".$grey_yarn_lost ."+".$yarn_dyed_lost."+".$yarn_others_lost."-(".$dyed_yarn_rtn_qty."+".$dyed_yarn_rej_qty.")<br>";

		        		// $yarn_dyed_loss = ($yarn_issue_for_dyeing>0) ? (($yarn_issue_for_dyeing - $yarn_dyeing_used_qty - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing)/$yarn_issue_for_dyeing)*100 : 0;

		        		// new formula
		        		$yarn_dyed_loss = ($yarn_dyeing_used_qty>0) ? (($yarn_dyeing_used_qty - $dyed_yarn_rcv_qty)/$yarn_dyeing_used_qty)*100 : 0;

		        		// echo $monyear."==(".$yarn_issue_for_dyeing ."-". $yarn_dyeing_used_qty ."-". $grey_yarn_issue_rtn.")/".$yarn_issue_for_dyeing.")*100<br>";

		        		$grey_trnsf_in 	= $transfer_in_data_array[$monyear][13];
		        		$fin_trnsf_in 	= $transfer_in_data_array[$monyear][2];
		        		$grey_trnsf_out = $transfer_out_data_array[$monyear][13];
		        		$fin_trnsf_out 	= $transfer_out_data_array[$monyear][2];

		        		$grey_fab_rcv 	= $fab_data_array[$monyear][13][1]['receive_qnty'];
		        		$grey_fab_rcv_rtn= $fab_data_array[$monyear][13][3]['issue_qnty'];
		        		$grey_fab_issue = $fab_data_array[$monyear][13][2]['issue_qnty'];
		        		$grey_fab_iss_rtn= $fab_data_array[$monyear][13][4]['receive_qnty'];
		        		//
		        		$fin_fab_rcv_without_sample 	= $fab_data_array_without_sample[$monyear][2][1]['receive_qnty'];
		        		$fin_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[$monyear][2][3]['issue_qnty'];
		        		$fin_fab_issue_without_sample 	= $fab_data_array_without_sample[$monyear][2][2]['issue_qnty'];
		        		$fin_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[$monyear][2][4]['receive_qnty'];

		        		$grey_fab_rcv_without_sample 	= $fab_data_array_without_sample[$monyear][13][1]['receive_qnty'];
		        		$grey_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[$monyear][13][3]['issue_qnty'];
		        		$grey_fab_issue_without_sample 	= $fab_data_array_without_sample[$monyear][13][2]['issue_qnty'];
		        		$grey_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[$monyear][13][4]['receive_qnty'];
		        		$grey_fab_rej_without_sample	= $fab_data_array_without_sample[$monyear][13][1]['rej_qty'];
		        		$grey_lft_over 	= $scrap_data_array[$monyear][13]['grey'];

		        		$grey_fab_lost 	= ($grey_fab_rcv_without_sample+$grey_trnsf_in+$grey_fab_iss_rtn_without_sample) -($grey_fab_issue_without_sample+$grey_trnsf_out+ $grey_fab_rcv_rtn_without_sample+ $grey_lft_over);


		        		$grey_lft_over 	= $scrap_data_array[$monyear][13]['grey']+$grey_fab_rej_without_sample;

		        		$fin_fab_rcv 	= $fab_data_array[$monyear][2][1]['receive_qnty'];
		        		$fin_fab_rcv_rtn= $fab_data_array[$monyear][2][3]['issue_qnty'];
		        		$fin_fab_issue 	= $fab_data_array[$monyear][2][2]['issue_qnty'];
		        		$fin_fab_iss_rtn= $fab_data_array[$monyear][2][4]['receive_qnty'];
		        		$fin_lft_over 	= $scrap_data_array[$monyear][2]['fin'];
		        		// $all_fin_lft_over 	= $scrap_data_array[$monyear][2]['allfin'];

		        		$fin_rcv_as_rej_qty = $fab_data_array[$monyear][2][1]['reject_qty'];
		        		$fin_iss_as_scrap_qty = $fab_data_array[$monyear][2][2]['scrap_qty'];
		        		$all_fin_lft_over = $fin_rcv_as_rej_qty + $fin_iss_as_scrap_qty;

		        		// fin fab purses data 
		        		$fin_fab_purses_rcv = $finfab_purses_data_array[$monyear][1]['qty'];
		        		$fin_fab_purses_issue = $finfab_purses_data_array[$monyear][2]['qty'];
		        		$fin_fab_purses_rcv_rtn = $finfab_purses_data_array[$monyear][3]['qty'];
		        		$fin_fab_purses_issue_rtn = $finfab_purses_data_array[$monyear][4]['qty'];

		        		$fin_fab_purses_lost = ($fin_fab_purses_rcv + $fin_fab_purses_issue_rtn) - ($fin_fab_purses_issue + $fin_fab_purses_rcv_rtn);
		        		
		        		$fin_fab_lost 	= ($fin_fab_rcv+$fin_fab_iss_rtn+$fin_trnsf_in) -($fin_fab_issue+$fin_trnsf_out+$fin_fab_rcv_rtn) ;// + $fin_lft_over + $fin_fab_purses_lost;
		        		// echo $fin_fab_lost 	."== (".$fin_fab_rcv."+".$fin_fab_iss_rtn."+".$fin_trnsf_in.") -(".$fin_fab_issue."+".$fin_trnsf_out."+".$fin_fab_rcv_rtn ."+". $fin_lft_over.")<br>";

		        		$batch_qnty 	= $batch_data_array[$monyear];
		        		$fin_fab_rcv 	= $finfab_data_array[$monyear]['rcv_qty'];
		        		$fin_fab_rej 	= $finfab_data_array[$monyear]['rej_qty'];
		        		$dyeing_pro_los 	= ($batch_qnty>0) ? (($batch_qnty - ($fin_fab_rcv + $fin_fab_rej))/$batch_qnty)*100 : 0;

		        		$aop_grey_fab 	= $aop_data_array[$monyear]['grey'];
		        		$aop_fin_fab 	= $aop_data_array[$monyear]['finish'];
		        		$aop_pro_loss 	= ($aop_grey_fab>0) ? (($aop_grey_fab - $aop_fin_fab)/$aop_grey_fab)*100 : 0;

		        		// $cutqc_pass_qty = $cutqc_data_array[$monyear]['qc_pass_qty'];
		        		// $cutqc_rej_qty 	= $cutqc_data_array[$monyear]['qc_rej_qty'];

		        		$cutqc_pass_qty = $gmts_data_array[$monyear]['cut_qty'];
		        		$cutqc_rej_qty 	= $gmts_data_array[$monyear]['cut_rej_qty'];

		        		$wash_issue_qty = $gmts_data_array[$monyear]['wash_issue_qty'];
		        		$wash_rcv_qty 	= $gmts_data_array[$monyear]['wash_rcv_qty'];
		        		$wash_rej_qty 	= $gmts_data_array[$monyear]['wash_rej_qty'];
		        		$wash_lost_qty 	= $wash_issue_qty - $wash_rcv_qty - $wash_rej_qty;
		        		$gmt_at_wash 	= $wash_issue_qty - $wash_rcv_qty;

		        		$emb_issue_qty 	= $gmts_data_array[$monyear]['emb_issue_qty'];
		        		$emb_rcv_qty 	= $gmts_data_array[$monyear]['emb_rcv_qty'];
		        		$emb_rej_qty 	= $gmts_data_array[$monyear]['emb_rej_qty'];
		        		$emb_lost_qty  	= $emb_issue_qty - $emb_rcv_qty - $emb_rej_qty;

		        		$print_issue_qty= $gmts_data_array[$monyear]['print_issue_qty'];
		        		$print_rcv_qty 	= $gmts_data_array[$monyear]['print_rcv_qty'];
		        		$print_rej_qty 	= $gmts_data_array[$monyear]['print_rej_qty'];
		        		$print_lost_qty = $print_issue_qty - $print_rcv_qty - $print_rej_qty;

		        		$sew_in_qty 	= $gmts_data_array[$monyear]['input_qty'];
		        		$sew_out_qty 	= $gmts_data_array[$monyear]['output_qty'];
		        		$sew_rej_qty 	= $gmts_data_array[$monyear]['sew_rej_qty'];
		        		$ok_sew_qty 	= $sew_out_qty - $sew_rej_qty;
		        		$sewing_lost 	= $sew_in_qty - $sew_out_qty - $sew_rej_qty;
		        		// echo $intRef."==".$sew_in_qty ."-". $sew_out_qty ."-". $sew_rej_qty."<br>";

		        		$fin_rej_qty 	= $gmts_data_array[$monyear]['fin_rej_qty'];

		        		$cutting_lost 	= ($cutqc_pass_qty - $print_issue_qty) + (($print_rcv_qty+$print_rej_qty) - $emb_issue_qty) + (($emb_rcv_qty+$emb_rej_qty) - $sew_in_qty);
		        		$cutpanel_lost = $cutting_lost + $print_lost_qty + $sewing_lost + $emb_lost_qty;
		        		// echo  $monyear."==".$cutting_lost ."+". $print_lost_qty ."+". $sewing_lost ."+". $emb_lost_qty."<br>";

		        		$cutpanel_reject = $cutqc_rej_qty + $print_rej_qty + $emb_rej_qty;

		        		$cut_at_cutting = $cutqc_pass_qty - ($print_lost_qty + $print_rej_qty + $emb_lost_qty + $emb_rej_qty+$sew_in_qty);
		        		$cut_at_sewing 	= $sew_in_qty - $sew_out_qty;
		        		$cut_at_embro 	= $emb_issue_qty - $emb_rcv_qty;
		        		$cut_at_print 	= $print_issue_qty - $print_rcv_qty;

		        		$ex_factory_qty = $exfact_data_array[$monyear];
		        		$gmt_lftovr_qty = $gmts_leftover_data_array[$monyear];
		        		$gmt_at_finish  = ($ok_sew_qty - ($gmt_lftovr_qty + $ex_factory_qty));

		        		$lost_gmts_qty 	= ($sew_out_qty + $sew_rej_qty) - ($gmt_lftovr_qty + $ex_factory_qty);
		        		$gmts_reject 	= $sew_rej_qty + $wash_rej_qty + $fin_rej_qty;
		        		$input_to_ship  = ($sew_in_qty>0) ? ($ex_factory_qty / $sew_in_qty)*100 : 0;

		        		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
			        	?>     
				        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
				            <td>
				            	<a href="javascript:void()" onclick="report_generate_by_intref_wise('<? echo $buyer_id;?>','<? echo $monyear?>')">
					            	<? echo $monyear;?>
					            </a>
					        </td>
				            <td align="right"><? echo number_format($gmt_array[$monyear],0);?></td>
				            <td align="right"><? echo number_format($yarn_req_array[$monyear],0); ?></td>
				            <td align="right"><? echo number_format($excess_yarn_array[$monyear],0); ?></td>
				            <td align="right"><? echo number_format($unAuthoYarn,0); ?></td>

				            <td align="right"><? echo number_format($yarn_dyed_loss,2); ?></td>
				            <td align="right"><? echo number_format($dyeing_pro_los,2); ?></td>
				            <td align="right"><? echo number_format($aop_pro_loss,2); ?></td>

				            <td align="right"><? echo number_format($yarn_lost_qty,0); ?></td>
				            <td align="right"><? echo number_format($grey_fab_lost,0); ?></td>
				            <td align="right"><? echo number_format($fin_fab_lost,0); ?></td>
				            <td align="right" title="At Cutting=<? echo $cutting_lost;?>&#13;At Printing=<? echo $print_lost_qty;?>&#13;At Embroidery=<? echo $emb_lost_qty;?>&#13;At Sewing=<? echo $sewing_lost;?>">
				            	<? echo number_format($cutpanel_lost,0); ?>
				            </td>
				            <td align="right" title="At Wash = <? echo $wash_lost_qty;?>"><? echo number_format($lost_gmts_qty,0); ?></td>

				            <td align="right" title="At Cutting=<? echo $cutqc_rej_qty;?>&#13;At Printing=<? echo $print_rej_qty;?>&#13;At Embroidery=<? echo $emb_rej_qty;?>">
				            	<? echo number_format($cutpanel_reject,0); ?>
				            </td>
				            <td align="right" title="At Sewing=<? echo $sew_rej_qty;?>&#13;At Wash=<? echo $wash_rej_qty;?>&#13;At Finish=<? echo $fin_rej_qty;?>">
				            	<? echo number_format($gmts_reject,0); ?>
				            </td>

				            <td align="right"><? echo number_format($grey_lft_over,0); ?></td>
				            <td align="right"><? echo number_format($all_fin_lft_over,0); ?></td>
				            <td align="right"><? echo number_format($gmt_lftovr_qty,0); ?></td>

				            <td align="right"><? echo number_format($input_to_ship,2); ?></td>
				            <td align="right"><? echo number_format($a,2); ?></td>
				        </tr>
				        <?
				        $i++;
				        $gr_gmts 			+= $gmt_array[$monyear];
			        	$gr_budget_yarn 	+= $yarn_req_array[$monyear];
			        	$gr_extra_yarn 		+= $excess_yarn_array[$monyear];
			        	$gr_unautho_yarn 	+= $unAuthoYarn;

			        	$gr_los_yd 			+= $a;
			        	$gr_los_dyeing 		+= $dyeing_pro_los;
			        	$gr_los_aop 		+= $aop_pro_loss;

			        	$gr_lost_yarn 		+= $yarn_lost_qty;
			        	$gr_lost_grey_fab 	+= $grey_fab_lost;
			        	$gr_lost_fin_fab 	+= $fin_fab_lost;
			        	$gr_lost_gmts 		+= $lost_gmts_qty;
			        	$gr_lost_cutpanel	+= $cutpanel_lost;

			        	$gr_left_grey_fab 	+= $grey_lft_over;
			        	$gr_left_fin_fab 	+= $all_fin_lft_over;
			        	$gr_left_gmts 		+= $gmt_lftovr_qty;

			        	$gr_cut_for_fab 	+= $cutqc_rej_qty;
			        	$gr_cut_at_cutting 	+= $cut_at_cutting;
			        	$gr_cut_at_print 	+= $cut_at_print;
			        	$gr_cut_at_embro 	+= $cut_at_embro;
			        	$gr_cut_at_sewing 	+= $cut_at_sewing;

			        	$gr_cut_panel_rej 	+= $cutpanel_reject;
			        	$gr_gmt_rej 	+= $gmts_reject;
			        	$gr_gmt_at_finish 	+= $gmt_at_finish;

			        	$gr_input_to_ship 	+= $input_to_ship;
			        	$gr_yarn_to_ship 	+= $a;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <th><? echo number_format($gr_gmts,0); ?></th>
	            <th><? echo number_format($gr_budget_yarn,0); ?></th>
	            <th><? echo number_format($gr_extra_yarn,0); ?></th>
	            <th><? echo number_format($gr_unautho_yarn,0); ?></th>

	            <th><? //echo number_format($gr_los_yd,0); ?></th>
	            <th><? //echo number_format($gr_los_dyeing,2); ?></th>
	            <th><? //echo number_format($gr_los_aop,2); ?></th>

	            <th><? echo number_format($gr_lost_yarn,0); ?></th>
	            <th><? echo number_format($gr_lost_grey_fab,0); ?></th>
	            <th><? echo number_format($gr_lost_fin_fab,0); ?></th>
	            <th><? echo number_format($gr_lost_cutpanel,0); ?></th>
	            <th><? echo number_format($gr_lost_gmts,0); ?></th>

	            <th><? echo number_format($gr_cut_panel_rej,0); ?></th>
	            <th><? echo number_format($gr_gmt_rej,0); ?></th>

	            <th><? echo number_format($gr_left_grey_fab,0); ?></th>
	            <th><? echo number_format($gr_left_fin_fab,0); ?></th>
	            <th><? echo number_format($gr_left_gmts,0); ?></th>

	            <th><? //echo number_format($gr_input_to_ship,2); ?></th>
	            <th><? //echo number_format($gr_yarn_to_ship,2); ?></th>
	        </tfoot>
	    </table>      
	    </div>    
	</fieldset>
			
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

if($action=="report_generate_by_intref_wise")// report generate when month/buyer details click
{
	$company_id 	= str_replace("'","",$cbo_company_id);
	$location_id 	= str_replace("'","",$cbo_location_id);
	$client_id 		= str_replace("'","",$cbo_client);
	$order_status	= str_replace("'","",$cbo_order_status);
	$year 			= str_replace("'","",$month_year);
	$buyer_id 		= str_replace("'","",$buyer_id);

	$sqlCond = "";
	$sqlCond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
	$sqlCond .= ($location_id !=0) ? " and a.location_name=$location_id" : "";
	$sqlCond .= ($client_id != 0) ? " and a.client_id=$client_id" : "";
	if($order_status !=0)
	{
		$sqlCond .= ($order_status == 1) ? " and b.shiping_status in(1,2)" : " and b.shiping_status in(3)";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$client_array = return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$company_id' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name", "id", "buyer_name");
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	// echo $firstYear."==".$lastYear;die();
	$fiscal_month_arr = array();
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
				$fiscal_month_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscal_month_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscal_month_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));

	$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/	
	$sql_gmt="SELECT a.JOB_NO,b.grouping as INT_REF,a.TOTAL_SET_QNTY,b.id as PO_ID,SUM(b.po_quantity*a.total_set_qnty) AS QTY
	 from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id  $sqlCond and to_char(b.shipment_date,'MON-YYYY')='$year' and a.buyer_name=$buyer_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no,b.grouping,a.total_set_qnty,b.id,b.shipment_date";
	// echo $sql_gmt;die();
	$sql_gmt_res = sql_select($sql_gmt);
	if(!count($sql_gmt_res))
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}

	$po_id_array = array();
	$gmt_array = array();
	foreach ($sql_gmt_res as $val) 
	{	
		$gmt_array[$val['INT_REF']] += $val['QTY'];			
		$po_id_array[$val['PO_ID']] = $val['PO_ID'];	
	}
	// print_r($buyer_data_array);die();
	unset($sql_gmt_res);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
    // echo $poCond;
	/*==========================================================================================/
	/								yarn req qty from budget									/
	/==========================================================================================*/
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);

    $sql = "SELECT a.job_no AS JOB_NO ,b.grouping as INT_REF,b.id AS id,c.item_number_id AS ITEM_NUMBER_ID,c.country_id AS country_id,c.color_number_id AS color_number_id,c.size_number_id AS size_number_id,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,c.country_ship_date AS country_ship_date,d.id AS pre_cost_dtls_id,d.fab_nature_id AS fab_nature_id,d.construction AS construction, d.gsm_weight AS gsm_weight,e.cons AS cons,e.requirment AS REQUIRMENT,f.id AS yarn_id,f.count_id AS count_id,f.copm_one_id AS copm_one_id,f.percent_one AS percent_one,f.type_id AS type_id,f.color AS color,f.cons_ratio AS CONS_RATIO,f.cons_qnty AS cons_qnty,f.avg_cons_qnty AS avg_cons_qnty,f.rate AS rate,f.amount AS amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_id $poCond and to_char(b.shipment_date,'MON-YYYY')='$year' and a.buyer_name=$buyer_id";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$yarn_req_array = array();
	foreach ($sql_res as $key => $row) 
	{		
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;

        $gmtsitemRatio 	= $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio 		= $row['CONS_RATIO'];
        $requirment 	= $row['REQUIRMENT'];
        $consQnty 		= ($requirment*$consRatio)/100;
        $reqQty 		= ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);	
        // echo $row['JOB_NO']."==".$reqQty 		."= (".$row['PLAN_CUT_QNTY']."/".$gmtsitemRatio.")*(".$consQnty."/".$pcs_value.")<br>";
		
		($reqQty>0)?$yarn_req_array[$row['INT_REF']] += $reqQty:"";
		
	}
	unset($sql_res);
	// print_r($yarn_req_array);die();
	/*==========================================================================================/
	/									get req. and program id									/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sql_plan = "SELECT c.requisition_no as REQUISITION_ID, c.knit_id as  PROGRAM_ID from wo_booking_mst a, ppl_planning_entry_plan_dtls b, ppl_yarn_requisition_entry c where a.booking_no=b.booking_no and b.dtls_id=c.knit_id and a.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 $bookingPoCond";
	// echo $sql_plan;die();
	$plan_res = sql_select($sql_plan);
	$program_id_array = array();
	$requisition_id_array = array();
	foreach ($plan_res as $val) 
	{
		$program_id_array[$val['PROGRAM_ID']] = $val['PROGRAM_ID'];
		$requisition_id_array[$val['REQUISITION_ID']] = $val['REQUISITION_ID'];
	}
	$program_ids = implode(",", $program_id_array);
	$requisition_ids = implode(",", $requisition_id_array);

	/*==========================================================================================/
	/									get YD Work Order id									/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
	$sql_wo = "SELECT b.MST_ID from wo_booking_dtls a, wo_yarn_dyeing_dtls b where a.booking_no=b.fab_booking_no and a.job_no=b.job_no and a.booking_type=1 and a.status_active=1 and b.status_active=1 $bookingPoCond";
	// echo $sql_wo;
	$plan_res = sql_select($sql_wo);
	$yd_wo_id_array = array();
	foreach ($plan_res as $val) 
	{
		$yd_wo_id_array[$val['MST_ID']] = $val['MST_ID'];
	}
	$yd_wo_ids = implode(",", $yd_wo_id_array);


	/*==========================================================================================/
	/										excess yarn											/
	/==========================================================================================*/
	$bookingPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlExcessYarn = "SELECT d.grouping as INT_REF,SUM(b.grey_fab_qnty) AS GREY_FAB_QNTY FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $bookingPoCond group by d.grouping";
    // echo $sqlExcessYarn;die();
    $exYarnRes = sql_select($sqlExcessYarn);
    $excess_yarn_array = array();
    foreach ($exYarnRes as $val) 
    {			
        $excess_yarn_array[$val['INT_REF']] += $val['GREY_FAB_QNTY'];        
    }
    unset($exYarnRes);
    // print_r($excess_yarn_array);die();
    /*==========================================================================================/
	/										yarn receive										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnRcv = "SELECT d.grouping as INT_REF, a.TRANSACTION_TYPE,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and a.item_category=1 and e.id=d.job_id and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond";
	// echo $sqlYArnRcv;die();
	$yarnRcvRes = sql_select($sqlYArnRcv);
	$yarn_data_array = array();
	foreach ($yarnRcvRes as $val) 
	{
        $yarn_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_data_array);die();
    /*==========================================================================================/
	/										yarn issue											/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT d.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,to_char(c.issue_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond and a.requisition_no in($requisition_ids)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_knit_data_array = array();
	foreach ($yarnISsueRes as $val) 
	{
		if($val['ISSUE_PURPOSE']==1)
		{
        	$yarn_knit_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        }
        $yarn_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']] += $val['QTY'];
	}
	// print_r($yarn_dyeing_data_array);die();

    /*==========================================================================================/
	/							yarn issue for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlYArnIssue = "SELECT c.ID,d.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,to_char(c.issue_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e where a.id=b.trans_id and a.prod_id=b.prod_id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond and c.issue_purpose in(2,12,15,38)";
	// echo $sqlYArnIssue;die();
	$yarnISsueRes = sql_select($sqlYArnIssue);
	$yarn_dyeing_data_array = array();
	$issue_id_array = array();
	$issue_purpose_array = array();
	foreach ($yarnISsueRes as $val) 
	{
        $yarn_dyeing_data_array[$val['INT_REF']][$val['ISSUE_PURPOSE']] += $val['QTY'];
        $issue_id_array[$val['ID']] = $val['ID'];
        $issue_purpose_array[$val['ID']] = $val['ISSUE_PURPOSE'];
	}
	$issueIds = implode(",", $issue_id_array);
	// print_r($yarn_dyeing_data_array);die(); 

	/*==========================================================================================/
	/						get yarn used qty for dyeing,twisting,recon....						/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.recv_number,d.grouping as INT_REF,a.RECEIVE_PURPOSE, b.GREY_QUANTITY as USED_QTY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2,12,15,38) and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $yarnPoCond";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_used_data_array[$val['INT_REF']][$val['RECEIVE_PURPOSE']] += $val['USED_QTY'];
	}
	// print_r($yarn_dyeing_used_data_array);die(); 

	/*==========================================================================================/
	/					get yarn rej & rtn qty for dyeing,twisting,recon....					/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnUsd = "SELECT a.ISSUE_ID,a.RECV_NUMBER,d.grouping as INT_REF,b.CONS_QUANTITY as RTN_QTY,b.cons_reject_qnty as REJ_QTY from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id  and c.id=d.job_id and b.item_category=1  and a.entry_form=9 and b.transaction_type in(4) and e.trans_id=b.id and d.id=e.po_breakdown_id and b.transaction_type=e.trans_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose is null and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $yarnPoCond  and a.issue_id in($issueIds)";//and a.booking_id in($yd_wo_ids)
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_dyeing_rtn_rej_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_dyeing_rtn_rej_data_array[$val['INT_REF']][$issue_purpose_array[$val['ISSUE_ID']]]['rtn_qty'] += $val['RTN_QTY'];
		$yarn_dyeing_rtn_rej_data_array[$val['INT_REF']][$issue_purpose_array[$val['ISSUE_ID']]]['rej_qty'] += $val['REJ_QTY'];
				
	}
	// echo "<pre>";print_r($yarn_dyeing_rtn_rej_data_array);die();

	/*==========================================================================================/
	/									get dyed yarn rcv qty									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlYArnDyed = "SELECT d.grouping as INT_REF, f.QUANTITY  from inv_receive_master a, inv_transaction b,wo_po_details_master c,wo_po_break_down d,wo_yarn_dyeing_mst e,order_wise_pro_details f where a.id=b.mst_id and b.pi_wo_batch_no=e.id and b.job_no=c.job_no and c.id=d.job_id and e.id=a.booking_id and f.po_breakdown_id=d.id and f.trans_id=b.id and b.item_category=1  and a.entry_form=1 and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_purpose in(2) and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $yarnPoCond";
	// echo $sqlYArnDyed;die();
	$yarnUsdRes = sql_select($sqlYArnDyed);
	$dyed_yarn_rcv_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$dyed_yarn_rcv_array[$val['INT_REF']] += $val['QUANTITY'];
	}
	// print_r($dyed_yarn_rcv_array);die(); 


    /*==========================================================================================/
	/									get yarn used qty										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	// $sqlYArnUsd = "SELECT f.grouping as INT_REF, c.USED_QTY  from inv_receive_master a, order_wise_pro_details b,pro_material_used_dtls c,inv_transaction d,wo_po_details_master e,wo_po_break_down f where d.id=b.trans_id and d.prod_id=b.prod_id and e.id=f.job_id and f.id=b.po_breakdown_id and d.item_category=13 and c.item_category=1 and c.entry_form=2 and b.entry_form=2 and d.transaction_type in(1) and a.id=d.mst_id and a.id=c.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(f.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond and a.booking_id in($program_ids)";

	$sqlYArnUsd = "SELECT f.grouping as INT_REF, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master a, order_wise_pro_details b,inv_transaction d,wo_po_details_master e,wo_po_break_down f where d.id=b.trans_id and d.prod_id=b.prod_id and e.id=f.job_id and f.id=b.po_breakdown_id and d.item_category=13 and b.entry_form=2 and d.transaction_type in(1) and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(f.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond and a.booking_id in($program_ids)";
	// echo $sqlYArnUsd;die();
	$yarnUsdRes = sql_select($sqlYArnUsd);
	$yarn_used_data_array = array();
	foreach ($yarnUsdRes as $val) 
	{
		$yarn_used_data_array[$val['INT_REF']] += $val['QUANTITY'] + $val['RETURNABLE_QNTY'];
		// $yarn_used_data_array[$val['RETURNABLE_QNTY']] += $val['RETURNABLE_QNTY'];
	}
	// print_r($yarn_used_data_array);die();
    /*==========================================================================================/
	/										grey yarn issue										/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssue = "SELECT d.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,to_char(c.issue_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY from inv_transaction a, order_wise_pro_details b,inv_issue_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2) and f.item_category_id=1 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond and a.requisition_no in($requisition_ids)";
	// echo $sqlGreyYArnIssue;die();
	$greyYarnISsueRes = sql_select($sqlGreyYArnIssue);
	$grey_yarn_data_array = array();
	foreach ($greyYarnISsueRes as $val) 
	{
        $grey_yarn_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']] += $val['QTY'];
    }
    /*==========================================================================================/
	/										grey yarn issue	rtn									/
	/==========================================================================================*/
	$yarnPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlGreyYArnIssueRtn = "SELECT d.grouping as INT_REF,f.DYED_TYPE, a.TRANSACTION_TYPE,to_char(d.shipment_date,'MON-YYYY') as MONTH_YEAR,b.quantity AS QTY,b.REJECT_QTY from inv_transaction a, order_wise_pro_details b,inv_receive_master c,wo_po_break_down d,wo_po_details_master e,product_details_master f where a.id=b.trans_id and a.prod_id=b.prod_id and f.id=a.prod_id and b.prod_id=f.id and c.id=a.mst_id and d.id=b.po_breakdown_id and e.id=d.job_id and a.item_category=1 and a.transaction_type in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.dyed_type in(0,2,1) and f.item_category_id=1 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $yarnPoCond and c.booking_id in($requisition_ids)";
	// echo $sqlGreyYArnIssueRtn;die();
	$greyYarnIssueRtnRes = sql_select($sqlGreyYArnIssueRtn);
	$grey_yarn_rej_data_array = array();
	$dyed_yarn_rej_rtn_array = array();
	foreach ($greyYarnIssueRtnRes as $val) 
	{
		if($val['DYED_TYPE']==1)
		{
			$dyed_yarn_rej_rtn_array[$val['INT_REF']][$val['TRANSACTION_TYPE']]['rtn'] += $val['QTY'];
			$dyed_yarn_rej_rtn_array[$val['INT_REF']][$val['TRANSACTION_TYPE']]['rej'] += $val['REJECT_QTY'];
		}
		else
		{
        	$grey_yarn_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']] += $val['QTY'];
        	$grey_yarn_rej_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']] += $val['REJECT_QTY'];
        }
    }
    // echo "<pre>"; print_r($grey_yarn_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	rcv									/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT e.grouping as INT_REF, a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY, b.QUANTITY,b.RETURNABLE_QNTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(1,4) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' and d.buyer_name=$buyer_id $greyfabPoCond ";//and f.batch_against=1 
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$fab_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$fab_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
		}
		$fab_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['reject_qty'] += $val['RETURNABLE_QNTY'];
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/									grey and fin fabric	issue								/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT e.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,pro_batch_create_mst f,wo_booking_mst g where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.booking_no=g.booking_no and g.booking_type=1 and a.transaction_type in(2,3) and f.id=a.pi_wo_batch_no  and f.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' and d.buyer_name=$buyer_id $greyfabPoCond";// and c.issue_purpose != 31 and f.batch_against=1 
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];
		if($val['ISSUE_PURPOSE']==31)
		{
			$fab_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['scrap_qty'] += $val['QUANTITY'];
		}
		
	}
	// print_r($fab_data_array);die();
    /*==========================================================================================/
	/						grey and fin fabric	rcv	without sample								/
	/==========================================================================================*/

	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT c.recv_number,e.grouping as INT_REF, a.TRANSACTION_TYPE,c.RECEIVE_PURPOSE,a.ITEM_CATEGORY, b.QUANTITY,b.returnable_qnty as REJ_QTY  from inv_receive_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and f.id=g.mst_id and g.id=c.booking_id and f.booking_no=h.booking_no and h.booking_type=1 and a.transaction_type in(1,4)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' and d.buyer_name=$buyer_id $greyfabPoCond";// and c.booking_id in($program_ids)
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$fab_data_array_without_sample = array();
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_PURPOSE']==31)
		{
			$fab_data_array_without_sample[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['leftover_qnty'] += $val['QUANTITY'];
		}
		else
		{
			$fab_data_array_without_sample[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['receive_qnty'] += $val['QUANTITY'];
			$fab_data_array_without_sample[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['rej_qty'] += $val['REJ_QTY'];
		}
	}
	// print_r($fab_data_array_without_sample);die();
    /*==========================================================================================/
	/						grey and fin fabric	issue without sample							/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT e.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e,ppl_planning_info_entry_mst f, ppl_planning_info_entry_dtls g,wo_booking_mst h where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(2,13) and a.transaction_type in(2,3) and f.id=g.mst_id and g.id=a.requisition_no and f.booking_no=h.booking_no and h.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' and d.buyer_name=$buyer_id $greyfabPoCond and c.issue_purpose != 31";// and a.requisition_no in($program_ids)
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	// $scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$fab_data_array_without_sample[$val['INT_REF']][$val['ITEM_CATEGORY']][$val['TRANSACTION_TYPE']]['issue_qnty'] += $val['QUANTITY'];		
	}
	// print_r($fab_data_array_without_sample);die();
	/*==========================================================================================/
	/										batch data											/
	/==========================================================================================*/
	$batchPoCond = str_replace("b.id", "b.po_id", $poCond);
	$sqlBatch = "SELECT d.grouping as INT_REF,b.batch_qnty AS BATCH_QNTY from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.batch_against in(1) and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $batchPoCond";
	// echo $sqlBatch;die();
	$batchRes = sql_select($sqlBatch);
	$batch_data_array = array();
	foreach ($batchRes as $val) 
	{
        $batch_data_array[$val['INT_REF']] += $val['BATCH_QNTY'];
	}
	// print_r($batch_data_array);die();
	/*==========================================================================================/
	/										FF Rcv data											/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT d.grouping as INT_REF,to_char(a.receive_date,'MON-YYYY') as MONTH_YEAR, c.quantity AS RCV_QTY, c.returnable_qnty AS REJ_QTY 
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,pro_batch_create_mst f where a.id=b.mst_id and b.id=c.trans_id and d.id=c.po_breakdown_id and e.id=d.job_id and b.transaction_type=1 $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 and f.id=b.pi_wo_batch_no and f.batch_against=1 and f.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_data_array[$val['INT_REF']]['rcv_qty'] += $val['RCV_QTY'];
        $finfab_data_array[$val['INT_REF']]['rej_qty'] += $val['REJ_QTY'];
	}
	// print_r($finfab_data_array);die();

	/*==========================================================================================/
	/										FF Purses Rcv data									/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT d.grouping as INT_REF,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(1,4) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(37,52) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0)  and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_purses_data_array = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();

	/*==========================================================================================/
	/										FF Purses Issue data								/
	/==========================================================================================*/
	$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT d.grouping as INT_REF,b.TRANSACTION_TYPE,c.quantity AS QTY
	from inv_issue_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,wo_booking_mst f,pro_batch_create_mst g where a.id=b.mst_id and d.id=c.po_breakdown_id and b.id=c.trans_id and e.id=d.job_id and b.transaction_type in(2,3) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(18,46) and a.item_category=2 and f.booking_type=1 and f.fabric_source=2 and f.status_active=1 and f.id=g.booking_no_id and g.id=b.pi_wo_batch_no and g.batch_against in(0) and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
	foreach ($finFabRes as $val) 
	{
        $finfab_purses_data_array[$val['INT_REF']][$val['TRANSACTION_TYPE']]['qty'] += $val['QTY'];
	}
	// print_r($finfab_purses_data_array);die();
	/*==========================================================================================/
	/								FF Rcv data	without sample									/
	/==========================================================================================*/
	/*$poIdsCondff = str_replace("b.id", "c.po_breakdown_id", $poCond);
    $sqlFinFab = "SELECT d.grouping as INT_REF,to_char(a.receive_date,'MON-YYYY') as MONTH_YEAR, c.quantity AS RCV_QTY, c.returnable_qnty AS REJ_QTY 
	from inv_receive_master a,inv_transaction b,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e,pro_batch_create_mst f where a.id=b.mst_id and b.id=c.trans_id and d.id=c.po_breakdown_id and e.id=d.job_id and b.transaction_type=1 and f.id=b.pi_wo_batch_no and f.batch_against(1) $poIdsCondff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=7 and a.item_category=2 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id";
    // echo $sqlFinFab;die();
    $finFabRes = sql_select($sqlFinFab);
    $finfab_data_array_without_sample = array();
	foreach ($finFabRes as $val) 
	{
        $finfab_data_array_without_sample[$val['INT_REF']]['rcv_qty'] += $val['RCV_QTY'];
        $finfab_data_array_without_sample[$val['INT_REF']]['rej_qty'] += $val['REJ_QTY'];
	}*/
	// print_r($finfab_data_array_without_sample);die();
	/*==========================================================================================/
	/									transfer data											/
	/==========================================================================================*/
	$transPoCond = str_replace("b.id", "a.to_order_id", $poCond);
	$transPoCond2 = str_replace("b.id", "a.from_order_id", $poCond);
	$sqlTrnsfrIn = "SELECT  a.ITEM_CATEGORY,d.grouping as INT_REF,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(5) and e.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and d.id=a.to_order_id and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $transPoCond";
	// echo $sqlTrnsfrIn;die();
	$trnsfrResIn = sql_select($sqlTrnsfrIn);
	$transfer_in_data_array = array();
	foreach ($trnsfrResIn as $val) 
	{
		$transfer_in_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_in_data_array);die();
	//======================================================================================
	$sqlTrnsfrOut = "SELECT  a.ITEM_CATEGORY,d.grouping as INT_REF,e.QUANTITY from inv_item_transfer_mst a,inv_item_transfer_dtls b,wo_po_details_master c,wo_po_break_down d,order_wise_pro_details e where a.id=b.mst_id and c.id=d.job_id and a.item_category in(2,13) and e.dtls_id=b.id and e.trans_type in(6) and e.po_breakdown_id=d.id and d.id=a.from_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transfer_criteria=4 and a.entry_form in(13,14) and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $transPoCond2";
	// echo $sqlTrnsfrOut;die();
	$trnsfrResOut = sql_select($sqlTrnsfrOut);
	$transfer_out_data_array = array();
	foreach ($trnsfrResOut as $val) 
	{
        $transfer_out_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']] += $val['QUANTITY'];
	}
	// print_r($transfer_out_data_array);die();
	/*==========================================================================================/
	/										scrap data											/
	/==========================================================================================*/
	$greyfabPoCond = str_replace("b.id", "b.po_breakdown_id", $poCond);
	$sqlgreyfab = "SELECT e.grouping as INT_REF,c.ISSUE_PURPOSE, a.TRANSACTION_TYPE,a.ITEM_CATEGORY,b.QUANTITY  from inv_issue_master c, inv_transaction a, order_wise_pro_details b,wo_po_details_master d,wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and d.id=e.job_id and e.id=b.po_breakdown_id and a.item_category in(13) and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(e.shipment_date,'MON-YYYY')='$year' and d.buyer_name=$buyer_id $greyfabPoCond and c.issue_purpose = 31";
	// echo $sqlgreyfab;die();
	$greyfabRes = sql_select($sqlgreyfab);
	$scrap_data_array = array();
	foreach ($greyfabRes as $val) 
	{
		$scrap_data_array[$val['INT_REF']][$val['ITEM_CATEGORY']]['grey'] += $val['QUANTITY'];
		
	}
	// ============================================ Fin Fab ===============================================	
	$greyfabPoCond = str_replace("b.id", "d.id", $poCond);
	$sqlgreyfab = "SELECT d.grouping as INT_REF,a.ITEM_CATEGORY_ID,a.RECEIVE_BASIS,b.RECEIVE_QNTY  from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, wo_po_details_master c,wo_po_break_down d,pro_batch_create_mst e,pro_batch_create_dtls f where a.id=b.mst_id and b.lot=e.batch_no and a.item_category_id in(2) and e.id=f.mst_id and e.batch_against=1 and c.id=d.job_id and d.id=f.po_id AND b.body_part_id=f.body_part_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $greyfabPoCond";
	// echo $sqlgreyfab;die(); 
	$greyfabRes = sql_select($sqlgreyfab);
	foreach ($greyfabRes as $val) 
	{
		if($val['RECEIVE_BASIS'] !=1)
		{
			$scrap_data_array[$val['INT_REF']][$val['ITEM_CATEGORY_ID']]['fin'] += $val['RECEIVE_QNTY'];	
		}			
		$scrap_data_array[$val['INT_REF']][$val['ITEM_CATEGORY_ID']]['allfin'] += $val['RECEIVE_QNTY'];
		
	}

	/*==========================================================================================/
	/											AOP Data 										/
	/==========================================================================================*/
	$aopPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlAop = "SELECT c.grouping as INT_REF,to_char(a.receive_date,'MON-YYYY') as MONTH_YEAR,b.GREY_USED,b.BATCH_ISSUE_QTY from inv_receive_mas_batchroll a, pro_grey_batch_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.order_id and a.status_active=1 and b.status_active=1 and a.entry_form=92 and to_char(c.shipment_date,'MON-YYYY')='$year' and b.buyer_id=$buyer_id $aopPoCond";
	// echo $sqlAop;die();
	$aopRes = sql_select($sqlAop);
    $aop_data_array = array();
	foreach ($aopRes as $val) 
	{
    	$aop_data_array[$val['INT_REF']]['grey'] 		+= $val['GREY_USED'];
    	$aop_data_array[$val['INT_REF']]['finish'] 		+= $val['BATCH_ISSUE_QTY'];
	}
	// print_r($aop_data_array);die();



	/*==========================================================================================/
	/										cutting qc Data 									/
	/==========================================================================================*/
	$cutQcPoCond = str_replace("b.id", "b.order_id", $poCond);
	$sqlcutQc = "SELECT d.grouping as INT_REF, b.QC_PASS_QTY , b.REJECT_QTY from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.order_id and a.status_active=1 and b.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $cutQcPoCond";
	// echo $sqlcutQc;die();
	$cutQcRes = sql_select($sqlcutQc);
    $cutqc_data_array = array();
	foreach ($cutQcRes as $val) 
	{
        $cutqc_data_array[$val['INT_REF']]['qc_pass_qty']+= $val['QC_PASS_QTY'];
        $cutqc_data_array[$val['INT_REF']]['qc_rej_qty'] += $val['REJECT_QTY'];        
	}
	// print_r($cutqc_data_array);die();

	/*==========================================================================================/
	/											gmts prod data									/
	/==========================================================================================*/
	$prodPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
    $sqlProd = "SELECT d.grouping as INT_REF, 
		
		 sum(case when a.production_type=1  THEN b.production_qnty ELSE 0 END) AS CUT_QTY
		 ,sum(case when a.production_type=1  THEN b.reject_qty ELSE 0 END) AS CUT_REJ_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=1  THEN b.production_qnty ELSE 0 END) AS PRI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=2  THEN b.production_qnty ELSE 0 END) AS EMBI_QTY
		 ,sum(case when a.production_type=2 and a.embel_name=3  THEN b.production_qnty ELSE 0 END) AS WHI_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1  THEN b.production_qnty ELSE 0 END) AS PRR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2  THEN b.production_qnty ELSE 0 END) AS EMBR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3  THEN b.production_qnty ELSE 0 END) AS WHR_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=3  THEN b.reject_qty ELSE 0 END) AS WHRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=2  THEN b.reject_qty ELSE 0 END) AS EMBRJ_QTY
		 ,sum(case when a.production_type=3 and a.embel_name=1  THEN b.reject_qty ELSE 0 END) AS PRRJ_QTY
		 ,sum(case when a.production_type=4  THEN b.production_qnty ELSE 0 END) AS SWI_QTY
		 ,sum(case when a.production_type=5  THEN b.production_qnty ELSE 0 END) AS SWO_QTY
		 ,sum(case when a.production_type=5  THEN b.reject_qty ELSE 0 END) AS SWR_QTY
		 ,sum(case when a.production_type=8  THEN b.production_qnty ELSE 0 END) AS FIN_QTY
		 ,sum(case when a.production_type=8  THEN b.reject_qty ELSE 0 END) AS FINR_QTY
	from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=a.po_break_down_id and to_char(d.shipment_date,'MON-YYYY')='$year' and c.buyer_name=$buyer_id $prodPoCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by d.grouping";
    // echo $sqlProd;die();
    $prodRes = sql_select($sqlProd);
    $gmts_data_array = array();
	foreach ($prodRes as $val) 
	{
    	$gmts_data_array[$val['INT_REF']]['cut_qty'] 		+= $val['CUT_QTY'];
    	$gmts_data_array[$val['INT_REF']]['cut_rej_qty'] 	+= $val['CUT_REJ_QTY'];
    	$gmts_data_array[$val['INT_REF']]['print_issue_qty']+= $val['PRI_QTY'];
    	$gmts_data_array[$val['INT_REF']]['print_rcv_qty'] 	+= $val['PRR_QTY'];
    	$gmts_data_array[$val['INT_REF']]['print_rej_qty'] 	+= $val['PRRJ_QTY'];

    	$gmts_data_array[$val['INT_REF']]['emb_issue_qty'] 	+= $val['EMBI_QTY'];
    	$gmts_data_array[$val['INT_REF']]['emb_rcv_qty'] 	+= $val['EMBR_QTY'];
    	$gmts_data_array[$val['INT_REF']]['emb_rej_qty'] 	+= $val['EMBRJ_QTY'];

    	$gmts_data_array[$val['INT_REF']]['wash_issue_qty'] 	+= $val['WHI_QTY'];
    	$gmts_data_array[$val['INT_REF']]['wash_rcv_qty'] 	+= $val['WHR_QTY'];
    	$gmts_data_array[$val['INT_REF']]['wash_rej_qty'] 	+= $val['WHRJ_QTY'];
    	
    	$gmts_data_array[$val['INT_REF']]['input_qty'] 		+= $val['SWI_QTY'];
    	$gmts_data_array[$val['INT_REF']]['output_qty'] 		+= $val['SWO_QTY'];
    	$gmts_data_array[$val['INT_REF']]['sew_rej_qty'] 	+= $val['SWR_QTY'];
    	$gmts_data_array[$val['INT_REF']]['fin_qty'] 		+= $val['FIN_QTY'];
    	$gmts_data_array[$val['INT_REF']]['fin_rej_qty'] 	+= $val['FINR_QTY'];
	}
	// print_r($gmts_data_array);die();
	/*==========================================================================================/
	/										Ex-factory Data 									/
	/==========================================================================================*/
	$exfactPoCond = str_replace("b.id", "a.po_break_down_id", $poCond);
	$sqlExfact = "SELECT d.grouping as INT_REF, b.production_qnty AS EX_QTY from pro_ex_factory_mst a, pro_ex_factory_dtls b, pro_ex_factory_delivery_mst c,wo_po_break_down d,wo_po_details_master e where a.id=b.mst_id and c.id=a.delivery_mst_id and d.id=a.po_break_down_id and e.id=d.job_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' and e.buyer_name=$buyer_id $exfactPoCond";
	// echo $sqlExfact;die();
	$exfactRes = sql_select($sqlExfact);
    $exfact_data_array = array();
	foreach ($exfactRes as $val) 
	{
        $exfact_data_array[$val['INT_REF']] 			+= $val['EX_QTY'];
	}
	// print_r($prod_data_array);die();
	/*==========================================================================================/
	/										gmt leftover Data 									/
	/==========================================================================================*/
	$leftoverPoCond = str_replace("b.id", "b.po_break_down_id", $poCond);
	$sqlLeftover = "SELECT d.grouping as INT_REF,c.PRODUCTION_QNTY from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c,wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and d.id=b.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and to_char(d.shipment_date,'MON-YYYY')='$year' and a.buyer_name=$buyer_id $leftoverPoCond";
	// echo $sqlLeftover;die();
	$leftoverRes = sql_select($sqlLeftover);
    $gmts_leftover_data_array = array();
	foreach ($leftoverRes as $val) 
	{
        $gmts_leftover_data_array[$val['INT_REF']] += $val['PRODUCTION_QNTY'];
	}
	// print_r($prod_data_array);die();


	$tbl_width = 1200;
	ob_start();	
	?>
	<!-- =======================================================================================/
	/										internal ref. wise report								/
	/======================================================================================== --> 
    <fieldset class="first_part" style="width:<? echo $tbl_width+30;?>px;margin:15px 0;">
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'ir_wise_report', '')"> -<b>Style Details : Buyer <? echo $buyer_arr[$buyer_id]; ?> and Month <? echo $year; ?></b><span style="color: red;font-size: 15px;font-weight: bold;"> <?=($client_id) ? ": ".$client_array[$client_id] : ": All"; ?></span></h3>
	    <div id="ir_wise_report">
			<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
		
	        <thead>
		        	<tr>
		        		<th width="340" colspan="5"></th>
		        		<th width="180" colspan="3">Process Loss%</th>
		        		<th width="300" colspan="5">Lost</th>
		        		<th width="120" colspan="2">Rejection</th>
		        		<th width="180" colspan="3">Leftover</th>
		        		<th width="120" colspan="2">Ship Status</th>
		        	</tr>
		        	<tr>
			            <th width="60">Ref No</th>
			            <th width="80">Total gmt</th>
			            <th width="80">Budget Yarn</th>
			            <th width="60">Extra Yarn </th>
			            <th width="60">Unauthorised Yarn</th>

			            <th width="60">YD</th>
			            <th width="60">Dyeing</th>
			            <th width="60">AOP</th>

			            <th width="60">Yarn</th>
			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Cut Panel</th>
			            <th width="60">Gmts</th>

			            <th width="60">Grey Fab</th>
			            <th width="60">Fin Fab</th>
			            <th width="60">Gmts</th>

			            <th width="60">Input to Ship</th>
			            <th width="60">Yarn to Ship</th>
		            </tr>
		        </thead>
		        <tbody>   
		        <?			
		        	$i=100;        	
		        	$gr_gmts 			= 0;
		        	$gr_budget_yarn 	= 0;
		        	$gr_extra_yarn 		= 0;
		        	$gr_unautho_yarn 	= 0;

		        	$gr_los_yd 			= 0;
		        	$gr_los_dyeing 		= 0;
		        	$gr_los_aop 		= 0;

		        	$gr_lost_yarn 		= 0;
		        	$gr_lost_grey_fab 	= 0;
		        	$gr_lost_fin_fab 	= 0;
		        	$gr_lost_gmts 		= 0;
		        	$gr_lost_cutpanel	= 0;

		        	$gr_left_grey_fab 	= 0;
		        	$gr_left_fin_fab 	= 0;
		        	$gr_left_gmts 		= 0;

		        	$gr_cut_for_fab 	= 0;
		        	$gr_cut_at_cutting 	= 0;
		        	$gr_cut_at_print 	= 0;
		        	$gr_cut_at_embro 	= 0;
		        	$gr_cut_at_sewing 	= 0;

		        	$gr_cut_panel_rej 	= 0;
		        	$gr_gmt_rej 	= 0;
		        	$gr_gmt_at_finish 	= 0;

		        	$gr_input_to_ship 	= 0;
		        	$gr_yarn_to_ship 	= 0;
		        	ksort($gmt_array);
		        	foreach ($gmt_array as $intRef => $val) 
		        	{
		        		$yarn_knit_issue= $yarn_knit_data_array[$intRef][2];		        		
		        		$yarn_knit_issue_rtn= $yarn_knit_data_array[$intRef][4];		        		
		        		$yarn_issue 	= $yarn_data_array[$intRef][2];		        		
		        		$yarn_issue_rtn = $yarn_data_array[$intRef][4];

		        		$grey_yarn_issue 	= $grey_yarn_data_array[$intRef][2];		        		
		        		$grey_yarn_issue_rtn= $grey_yarn_data_array[$intRef][4];
		        		$grey_yarn_issue_rej_rtn= $grey_yarn_rej_data_array[$intRef][4];
		        		$dyed_yarn_rtn_qty = $dyed_yarn_rej_rtn_array[$intRef][4]['rtn'];
		        		$dyed_yarn_rej_qty = $dyed_yarn_rej_rtn_array[$intRef][4]['rej'];


			        	$yarn_issue_rtn_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$intRef][2]['rtn_qty'];
			        	$yarn_rej_for_dyeing = $yarn_dyeing_rtn_rej_data_array[$intRef][2]['rej_qty'];

			        	$yarn_issue_rtn_for_others = $yarn_dyeing_rtn_rej_data_array[$intRef][12]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$intRef][15]['rtn_qty']+$yarn_dyeing_rtn_rej_data_array[$intRef][38]['rtn_qty'];
			        	$yarn_rej_for_others = $yarn_dyeing_rtn_rej_data_array[$intRef][12]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$intRef][15]['rej_qty']+$yarn_dyeing_rtn_rej_data_array[$intRef][38]['rej_qty'];

			        	$yarn_issue_for_dyeing = $yarn_dyeing_data_array[$intRef][2];
			        	$yarn_dyeing_used_qty = $yarn_dyeing_used_data_array[$intRef][2];
			        	$yarn_dyed_lost = $yarn_issue_for_dyeing - ($yarn_dyeing_used_qty+$yarn_issue_rtn_for_dyeing+$yarn_rej_for_dyeing);
			        	// echo $intRef."==".$yarn_issue_for_dyeing ."- (".$yarn_dyeing_used_qty."+".$yarn_issue_rtn_for_dyeing."+".$yarn_rej_for_dyeing.")<br>";

			        	// ========== get twisting, reconning,re-waxing data ===========
			        	$yarn_issue_for_others = $yarn_dyeing_data_array[$intRef][12]+$yarn_dyeing_data_array[$intRef][15]+$yarn_dyeing_data_array[$intRef][38];
			        	$yarn_others_used_qty = $yarn_dyeing_used_data_array[$intRef][12]+$yarn_dyeing_used_data_array[$intRef][15]+$yarn_dyeing_used_data_array[$intRef][38];
			        	$yarn_others_lost = $yarn_issue_for_others - ($yarn_others_used_qty+$yarn_issue_rtn_for_others+$yarn_rej_for_others);
			        	// echo $yarn_issue_for_others ."-". $yarn_others_used_qty."+". $yarn_issue_rtn_for_others ."+". $yarn_rej_for_others."<br>";
		        		
		        		$unAuthoYarn 	= ($grey_yarn_issue+$yarn_issue_for_dyeing+$yarn_issue_for_others - $grey_yarn_issue_rtn - $grey_yarn_issue_rej_rtn - $yarn_issue_rtn_for_others - $yarn_rej_for_others- $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing) - ($yarn_req_array[$intRef] + $excess_yarn_array[$intRef]);
		        		// $unAuthoYarn 	= ($yarn_issue + $yarn_issue_rtn) - ($yarn_req_array[$intRef] + $excess_yarn_array[$intRef]);

			        	// echo $intRef."==".$grey_yarn_issue ."+".$yarn_issue_for_dyeing."+".$yarn_issue_for_others."-". $grey_yarn_issue_rtn."-". $grey_yarn_issue_rej_rtn."-". $yarn_issue_rtn_for_others."-". $yarn_rej_for_others.") - (".$yarn_req_array[$intRef] ."+". $excess_yarn_array[$intRef].")<br>";

			        	$yarn_used_qty 	= $yarn_used_data_array[$intRef];
			        	$dyed_yarn_rcv_qty 	= $dyed_yarn_rcv_array[$intRef];

			        	$grey_yarn_lost = $yarn_knit_issue - ($yarn_used_qty + $grey_yarn_issue_rtn + $grey_yarn_issue_rej_rtn);
			        	// echo $intRef."==(".$yarn_knit_issue ."- (".$yarn_used_qty ."+". $grey_yarn_issue_rtn ."+". $grey_yarn_issue_rej_rtn.")<br>";

			        	$yarn_lost_qty 	= $grey_yarn_lost+$yarn_dyed_lost+$yarn_others_lost-($dyed_yarn_rtn_qty+$dyed_yarn_rej_qty);
			        	// echo $intRef."==".$grey_yarn_lost ."+".$yarn_dyed_lost."+".$yarn_others_lost."-(".$dyed_yarn_rtn_qty."+".$dyed_yarn_rej_qty.")<br>";

			        	// old formula
		        		// $yarn_dyed_loss = ($yarn_issue_for_dyeing>0) ? (($yarn_issue_for_dyeing - $yarn_dyeing_used_qty - $yarn_issue_rtn_for_dyeing - $yarn_rej_for_dyeing)/$yarn_issue_for_dyeing)*100 : 0;

		        		// new formula
		        		$yarn_dyed_loss = ($yarn_dyeing_used_qty>0) ? (($yarn_dyeing_used_qty - $dyed_yarn_rcv_qty)/$yarn_dyeing_used_qty)*100 : 0;

		        		// echo $intRef."==(".$yarn_dyeing_used_qty ."-". $dyed_yarn_rcv_qty.")/".$yarn_dyeing_used_qty.")*100<br>";

		        		$grey_trnsf_in 	= $transfer_in_data_array[$intRef][13];
		        		$fin_trnsf_in 	= $transfer_in_data_array[$intRef][2];
		        		$grey_trnsf_out = $transfer_out_data_array[$intRef][13];
		        		$fin_trnsf_out 	= $transfer_out_data_array[$intRef][2];

		        		$grey_fab_rcv 	= $fab_data_array[$intRef][13][1]['receive_qnty'];
		        		$grey_fab_rcv_rtn= $fab_data_array[$intRef][13][3]['issue_qnty'];
		        		$grey_fab_issue = $fab_data_array[$intRef][13][2]['issue_qnty'];
		        		$grey_fab_iss_rtn= $fab_data_array[$intRef][13][4]['receive_qnty'];
		        		
		        		$grey_fab_rej_without_sample	= $fab_data_array_without_sample[$intRef][13][1]['rej_qty'];
		        		$grey_lft_over 	= $scrap_data_array[$intRef][13]['grey'];

		        		//
		        		$fin_fab_rcv_without_sample 	= $fab_data_array_without_sample[$intRef][2][1]['receive_qnty'];
		        		$fin_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[$intRef][2][3]['issue_qnty'];
		        		$fin_fab_issue_without_sample 	= $fab_data_array_without_sample[$intRef][2][2]['issue_qnty'];
		        		$fin_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[$intRef][2][4]['receive_qnty'];

		        		$grey_fab_rcv_without_sample 	= $fab_data_array_without_sample[$intRef][13][1]['receive_qnty'];
		        		$grey_fab_rcv_rtn_without_sample	= $fab_data_array_without_sample[$intRef][13][3]['issue_qnty'];
		        		$grey_fab_issue_without_sample 	= $fab_data_array_without_sample[$intRef][13][2]['issue_qnty'];
		        		$grey_fab_iss_rtn_without_sample	= $fab_data_array_without_sample[$intRef][13][4]['receive_qnty'];

		        		$grey_fab_lost 	= ($grey_fab_rcv_without_sample+$grey_trnsf_in+$grey_fab_iss_rtn_without_sample) -($grey_fab_issue_without_sample+$grey_fab_rcv_rtn_without_sample + $grey_lft_over + $grey_trnsf_out);

		        		// echo $intRef."==".$grey_fab_rcv_without_sample."+".$grey_trnsf_in."+".$grey_fab_iss_rtn_without_sample.") -(".$grey_fab_issue_without_sample ."+". $grey_fab_rcv_rtn_without_sample."+". $grey_lft_over ."+". $grey_trnsf_out .")<br>";
						

		        		$grey_lft_over 	= $scrap_data_array[$intRef][13]['grey']+$grey_fab_rej_without_sample;

		        		$fin_fab_rcv 	= $fab_data_array[$intRef][2][1]['receive_qnty'];
		        		$fin_fab_rcv_rtn= $fab_data_array[$intRef][2][3]['issue_qnty'];
		        		$fin_fab_issue 	= $fab_data_array[$intRef][2][2]['issue_qnty'];
		        		$fin_fab_iss_rtn= $fab_data_array[$intRef][2][4]['receive_qnty'];
		        		$fin_lft_over 	= $scrap_data_array[$intRef][2]['fin'];
		        		// $all_fin_lft_over 	= $scrap_data_array[$intRef][2]['allfin'];

		        		$fin_rcv_as_rej_qty = $fab_data_array[$intRef][2][1]['reject_qty'];
		        		$fin_iss_as_scrap_qty = $fab_data_array[$intRef][2][2]['scrap_qty'];
		        		$all_fin_lft_over = $fin_rcv_as_rej_qty + $fin_iss_as_scrap_qty;

		        		// fin fab purses data 
		        		$fin_fab_purses_rcv = $finfab_purses_data_array[$intRef][1]['qty'];
		        		$fin_fab_purses_issue = $finfab_purses_data_array[$intRef][2]['qty'];
		        		$fin_fab_purses_rcv_rtn = $finfab_purses_data_array[$intRef][3]['qty'];
		        		$fin_fab_purses_issue_rtn = $finfab_purses_data_array[$intRef][4]['qty'];

		        		$fin_fab_purses_lost = ($fin_fab_purses_rcv + $fin_fab_purses_issue_rtn) - ($fin_fab_purses_issue + $fin_fab_purses_rcv_rtn);

		        		
		        		$fin_fab_lost 	= ($fin_fab_rcv+$fin_trnsf_in+$fin_fab_iss_rtn) -($fin_fab_issue+$fin_trnsf_out+$fin_fab_rcv_rtn) ;// + $fin_lft_over + $fin_fab_purses_lost;
		        		// echo $intRef."==".$fin_fab_rcv."+". $fin_trnsf_in ."+".$fin_fab_iss_rtn .")-(".$fin_fab_issue ."+". $fin_trnsf_out ."+". $fin_fab_rcv_rtn ."+". $fin_lft_over."+".$fin_fab_purses_lost.")<br>";

		        		$batch_qnty 	= $batch_data_array[$intRef];
		        		// $fin_fab_rcv 	= $finfab_data_array[$intRef]['rcv_qty'];
		        		$fin_fab_rej 	= $finfab_data_array[$intRef]['rej_qty'];

		        		// $fin_fab_rcv_without_sample 	= $finfab_data_array_without_sample[$intRef]['rcv_qty'];
		        		// $fin_fab_rej_without_sample 	= $finfab_data_array_without_sample[$intRef]['rej_qty'];

		        		$dyeing_pro_los = ($batch_qnty>0) ? (($batch_qnty - ($fin_fab_rcv + $fin_fab_rej))/$batch_qnty)*100 : 0;
		        		// echo $intRef."== ((".$batch_qnty ."- (".$fin_fab_rcv ."+". $fin_fab_rej."))/".$batch_qnty.")*100<br>";

		        		$aop_grey_fab 	= $aop_data_array[$intRef]['grey'];
		        		$aop_fin_fab 	= $aop_data_array[$intRef]['finish'];
		        		$aop_pro_loss 	= ($aop_grey_fab>0) ? (($aop_grey_fab - $aop_fin_fab)/$aop_grey_fab)*100 : 0;

		        		// $cutqc_pass_qty = $cutqc_data_array[$intRef]['qc_pass_qty'];
		        		// $cutqc_rej_qty 	= $cutqc_data_array[$intRef]['qc_rej_qty'];

		        		$cutqc_pass_qty = $gmts_data_array[$intRef]['cut_qty'];
		        		$cutqc_rej_qty 	= $gmts_data_array[$intRef]['cut_rej_qty'];

		        		$wash_issue_qty = $gmts_data_array[$intRef]['wash_issue_qty'];
		        		$wash_rcv_qty 	= $gmts_data_array[$intRef]['wash_rcv_qty'];
		        		$wash_rej_qty 	= $gmts_data_array[$intRef]['wash_rej_qty'];
		        		$wash_lost_qty 	= $wash_issue_qty - $wash_rcv_qty - $wash_rej_qty;
		        		$gmt_at_wash 	= $wash_issue_qty - $wash_rcv_qty;

		        		$emb_issue_qty 	= $gmts_data_array[$intRef]['emb_issue_qty'];
		        		$emb_rcv_qty 	= $gmts_data_array[$intRef]['emb_rcv_qty'];
		        		$emb_rej_qty 	= $gmts_data_array[$intRef]['emb_rej_qty'];
		        		$emb_lost_qty  	= $emb_issue_qty - $emb_rcv_qty - $emb_rej_qty;

		        		$print_issue_qty= $gmts_data_array[$intRef]['print_issue_qty'];
		        		$print_rcv_qty 	= $gmts_data_array[$intRef]['print_rcv_qty'];
		        		$print_rej_qty 	= $gmts_data_array[$intRef]['print_rej_qty'];
		        		$print_lost_qty = $print_issue_qty - $print_rcv_qty - $print_rej_qty;

		        		$sew_in_qty 	= $gmts_data_array[$intRef]['input_qty'];
		        		$sew_out_qty 	= $gmts_data_array[$intRef]['output_qty'];
		        		$sew_rej_qty 	= $gmts_data_array[$intRef]['sew_rej_qty'];
		        		$ok_sew_qty 	= $sew_out_qty - $sew_rej_qty;
		        		$sewing_lost 	= $sew_in_qty - $sew_out_qty - $sew_rej_qty;
		        		// echo $intRef."==".$sew_in_qty ."-". $sew_out_qty ."-". $sew_rej_qty."<br>";

		        		$fin_rej_qty 	= $gmts_data_array[$intRef]['fin_rej_qty'];

		        		$cutting_lost 	= ($cutqc_pass_qty - $print_issue_qty) + (($print_rcv_qty+$print_rej_qty) - $emb_issue_qty) + (($emb_rcv_qty+$emb_rej_qty) - $sew_in_qty);
		        		$cutpanel_lost = $cutting_lost + $print_lost_qty + $sewing_lost + $emb_lost_qty;
		        		// echo  $intRef."==".$cutting_lost ."+". $print_lost_qty ."+". $sewing_lost ."+". $emb_lost_qty."<br>";

		        		$cutpanel_reject = $cutqc_rej_qty + $print_rej_qty + $emb_rej_qty;

		        		$cut_at_cutting = $cutqc_pass_qty - ($print_lost_qty + $print_rej_qty + $emb_lost_qty + $emb_rej_qty+$sew_in_qty);
		        		$cut_at_sewing 	= $sew_in_qty - $sew_out_qty;
		        		$cut_at_embro 	= $emb_issue_qty - $emb_rcv_qty;
		        		$cut_at_print 	= $print_issue_qty - $print_rcv_qty;

		        		$ex_factory_qty = $exfact_data_array[$intRef];
		        		$gmt_lftovr_qty = $gmts_leftover_data_array[$intRef];
		        		$gmt_at_finish  = ($ok_sew_qty - ($gmt_lftovr_qty + $ex_factory_qty));

		        		$lost_gmts_qty 	= ($sew_out_qty + $sew_rej_qty) - ($gmt_lftovr_qty + $ex_factory_qty);
		        		// echo $intRef."==(".$sew_out_qty ."+". $sew_rej_qty.") - (".$gmt_lftovr_qty ."+". $ex_factory_qty.")<br>";
		        		$gmts_reject 	= $sew_rej_qty + $wash_rej_qty + $fin_rej_qty;
		        		$input_to_ship  = ($sew_in_qty>0) ? ($ex_factory_qty / $sew_in_qty)*100 : 0;

		        		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
			        	?>     
				        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
				            <td><? echo $intRef;?></td>
				            <td align="right"><? echo number_format($gmt_array[$intRef],0);?></td>
				            <td align="right"><? echo number_format($yarn_req_array[$intRef],0); ?></td>
				            <td align="right"><? echo number_format($excess_yarn_array[$intRef],0); ?></td>
				            <td align="right"><? echo number_format($unAuthoYarn,0); ?></td>

				            <td align="right"><? echo number_format($yarn_dyed_loss,2); ?></td>
				            <td align="right"><? echo number_format($dyeing_pro_los,2); ?></td>
				            <td align="right"><? echo number_format($aop_pro_loss,2); ?></td>

				            <td align="right"><? echo number_format($yarn_lost_qty,0); ?></td>
				            <td align="right"><? echo number_format($grey_fab_lost,0); ?></td>
				            <td align="right"><? echo number_format($fin_fab_lost,0); ?></td>
				            <td align="right" title="At Cutting=<? echo $cutting_lost;?>&#13;At Printing=<? echo $print_lost_qty;?>&#13;At Embroidery=<? echo $emb_lost_qty;?>&#13;At Sewing=<? echo $sewing_lost;?>">
				            	<? echo number_format($cutpanel_lost,0); ?>
				            </td>
				            <td align="right" title="At Wash = <? echo $wash_lost_qty;?>"><? echo number_format($lost_gmts_qty,0); ?></td>

				            <td align="right" title="At Cutting=<? echo $cutqc_rej_qty;?>&#13;At Printing=<? echo $print_rej_qty;?>&#13;At Embroidery=<? echo $emb_rej_qty;?>">
				            	<? echo number_format($cutpanel_reject,0); ?>
				            </td>
				            <td align="right" title="At Sewing=<? echo $sew_rej_qty;?>&#13;At Wash=<? echo $wash_rej_qty;?>&#13;At Finish=<? echo $fin_rej_qty;?>">
				            	<? echo number_format($gmts_reject,0); ?>
				            </td>

				            <td align="right"><? echo number_format($grey_lft_over,0); ?></td>
				            <td align="right"><? echo number_format($all_fin_lft_over,0); ?></td>
				            <td align="right"><? echo number_format($gmt_lftovr_qty,0); ?></td>

				            <td align="right"><? echo number_format($input_to_ship,2); ?></td>
				            <td align="right"><? echo number_format($a,2); ?></td>
				        </tr>
				        <?
				        $i++;
				        $gr_gmts 			+= $gmt_array[$intRef];
			        	$gr_budget_yarn 	+= $yarn_req_array[$intRef];
			        	$gr_extra_yarn 		+= $excess_yarn_array[$intRef];
			        	$gr_unautho_yarn 	+= $unAuthoYarn;

			        	$gr_los_yd 			+= $a;
			        	$gr_los_dyeing 		+= $dyeing_pro_los;
			        	$gr_los_aop 		+= $aop_pro_loss;

			        	$gr_lost_yarn 		+= $yarn_lost_qty;
			        	$gr_lost_grey_fab 	+= $grey_fab_lost;
			        	$gr_lost_fin_fab 	+= $fin_fab_lost;
			        	$gr_lost_gmts 		+= $lost_gmts_qty;
			        	$gr_lost_cutpanel 	+= $cutpanel_lost;

			        	$gr_left_grey_fab 	+= $grey_lft_over;
			        	$gr_left_fin_fab 	+= $all_fin_lft_over;
			        	$gr_left_gmts 		+= $gmt_lftovr_qty;

			        	$gr_cut_for_fab 	+= $cutqc_rej_qty;
			        	$gr_cut_at_cutting 	+= $cut_at_cutting;
			        	$gr_cut_at_print 	+= $cut_at_print;
			        	$gr_cut_at_embro 	+= $cut_at_embro;
			        	$gr_cut_at_sewing 	+= $cut_at_sewing;

			        	$gr_cut_panel_rej 	+= $cutpanel_reject;
			        	$gr_gmt_rej 		+= $gmts_reject;
			        	$gr_gmt_at_finish 	+= $gmt_at_finish;

			        	$gr_input_to_ship 	+= $input_to_ship;
			        	$gr_yarn_to_ship 	+= $a;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Total</th>
	            <th><? echo number_format($gr_gmts,0); ?></th>
	            <th><? echo number_format($gr_budget_yarn,0); ?></th>
	            <th><? echo number_format($gr_extra_yarn,0); ?></th>
	            <th><? echo number_format($gr_unautho_yarn,0); ?></th>

	            <th><? //echo number_format($gr_los_yd,0); ?></th>
	            <th><? //echo number_format($gr_los_dyeing,2); ?></th>
	            <th><? //echo number_format($gr_los_aop,2); ?></th>

	            <th><? echo number_format($gr_lost_yarn,0); ?></th>
	            <th><? echo number_format($gr_lost_grey_fab,0); ?></th>
	            <th><? echo number_format($gr_lost_fin_fab,0); ?></th>
	            <th><? echo number_format($gr_lost_cutpanel,0); ?></th>
	            <th><? echo number_format($gr_lost_gmts,0); ?></th>

	            <th><? echo number_format($gr_cut_panel_rej,0); ?></th>
	            <th><? echo number_format($gr_gmt_rej,0); ?></th>

	            <th><? echo number_format($gr_left_grey_fab,0); ?></th>
	            <th><? echo number_format($gr_left_fin_fab,0); ?></th>
	            <th><? echo number_format($gr_left_gmts,0); ?></th>

	            <th><? //echo number_format($gr_input_to_ship,2); ?></th>
	            <th><? //echo number_format($gr_yarn_to_ship,2); ?></th>
	        </tfoot>
	    </table>      
	    </div>    
	</fieldset>
			
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