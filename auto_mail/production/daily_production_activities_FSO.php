<?
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
 
 
$company_lib = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name",$con);
$buyer_lib = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$color_lib = return_library_array( "select id, COLOR_NAME from LIB_COLOR where  status_active=1 and is_deleted=0", "id", "COLOR_NAME");


$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
if($_REQUEST['view_date']){
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
}

$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);

$previous_date=$current_date;

//------------------------Fabric Sales Order Received Status 

$dateCon =" AND A.BOOKING_DATE BETWEEN '$previous_date' AND '$previous_date'";
$fsoSql = "select A.JOB_NO,A.WITHIN_GROUP,a.COMPANY_ID,A.BUYER_ID AS PARTY_ID ,A.SALES_BOOKING_NO, A.DELIVERY_DATE,A.BOOKING_APPROVAL_DATE as APPROVED_DATE, C.BUYER_ID,SUM(B.GREY_QNTY_BY_UOM) QTY from FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B, WO_BOOKING_MST C WHERE A.ID=B.MST_ID AND A.BOOKING_ID=C.ID AND A.WITHIN_GROUP=1 $dateCon GROUP BY A.JOB_NO,A.WITHIN_GROUP,a.COMPANY_ID,A.BUYER_ID ,A.SALES_BOOKING_NO, A.DELIVERY_DATE,A.BOOKING_APPROVAL_DATE, C.BUYER_ID
UNION ALL
select A.JOB_NO,A.WITHIN_GROUP,a.COMPANY_ID,A.BUYER_ID AS PARTY_ID ,A.SALES_BOOKING_NO, A.DELIVERY_DATE,A.BOOKING_APPROVAL_DATE as APPROVED_DATE, 0 AS BUYER_ID,SUM(B.GREY_QNTY_BY_UOM) QTY from FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B WHERE A.ID=B.MST_ID AND A.WITHIN_GROUP=2 $dateCon GROUP BY A.JOB_NO,A.WITHIN_GROUP,a.COMPANY_ID,A.BUYER_ID ,A.SALES_BOOKING_NO, A.DELIVERY_DATE,A.BOOKING_APPROVAL_DATE";
   //echo $fsoSql;die;
$fsoSqlRes=sql_select($fsoSql);
$fsoDataArr=array();
foreach($fsoSqlRes as $rows){
	
	$party_name=($rows[WITHIN_GROUP]==1)?$company_lib[$rows[PARTY_ID]]:$buyer_lib[$rows[PARTY_ID]];
	
	$fsoDataArr[$rows[COMPANY_ID]][DATA_ARR][]=array(
		BUYER_NAME => $rows[BUYER_ID],
		WITHIN_GROUP => $rows[WITHIN_GROUP],
		JOB_NO => $rows[JOB_NO],
		PARTY_NAME => $party_name,
		APPROVED_DATE => change_date_format($rows[APPROVED_DATE]),
		DELIVERY_DATE => change_date_format($rows[DELIVERY_DATE]),
		BOOKING_NO => $rows[SALES_BOOKING_NO],
		QTY => $rows[QTY],
	);
	$fsoDataArr[$rows[COMPANY_ID]][GRAND_TOTAL]	+=$rows[QTY];
	$salse_id_arr[1][$rows[JOB_NO]]=$rows[JOB_NO];
}

//var_dump($fsoDataArr);die;
//..........................Fabric Sales Order Received Status end;


//................................................Planning Activities
$dateCon =" AND A.BOOKING_DATE BETWEEN '$previous_date' AND '$previous_date'";
$dateCon =" AND b.PROGRAM_DATE BETWEEN '$previous_date' AND '$previous_date'";
$sql_sales_booking = "SELECT a.id, a.COMPANY_ID, a.WITHIN_GROUP, a.JOB_NO, a.SALES_BOOKING_NO, a.booking_id, a.BUYER_ID, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,  a.is_apply_last_update,a.is_master_part_updated, b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no=c.booking_no  and c.fabric_source in(1,2) and a.booking_without_order=0 $dateCon__ 

and a.SALES_BOOKING_NO in(select a.BOOKING_NO from PPL_PLANNING_INFO_ENTRY_MST a,PPL_PLANNING_INFO_ENTRY_DTLS b where a.id=b.mst_id  $dateCon)

group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id
union all
select a.id, a.COMPANY_ID, a.WITHIN_GROUP, a.JOB_NO, a.SALES_BOOKING_NO, a.booking_id, a.BUYER_ID, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, a.is_apply_last_update,a.is_master_part_updated, b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no  and   (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 $dateCon__ 

and a.SALES_BOOKING_NO in(select a.BOOKING_NO from PPL_PLANNING_INFO_ENTRY_MST a,PPL_PLANNING_INFO_ENTRY_DTLS b where a.id=b.mst_id  $dateCon)

group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id
union all
SELECT a.id, a.COMPANY_ID, a.within_group, a.JOB_NO, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,a.is_apply_last_update,a.is_master_part_updated, b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 $dateCon__ and a.within_group=2 

and a.SALES_BOOKING_NO in(select a.BOOKING_NO from PPL_PLANNING_INFO_ENTRY_MST a,PPL_PLANNING_INFO_ENTRY_DTLS b where a.id=b.mst_id  $dateCon)

group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id  order by dia 
";

//echo $sql_sales_booking;



$sql_sales_booking_res=sql_select($sql_sales_booking);

$planDataArr=array();
foreach($sql_sales_booking_res as $row){
	
	//$party_name=($rows[WITHIN_GROUP]==1)?$company_lib[$rows[BUYER_ID]]:$buyer_lib[$rows[BUYER_ID]];

	//$buyer_name=($row[WITHIN_GROUP]==1)?$company_lib[$row[BUYER_ID]]:$buyer_lib[$row[BUYER_ID]];
	
	$planDataArr[$row[COMPANY_ID]][DATA_ARR][]=array(
		COMPANY_ID => $row[COMPANY_ID],
		BOOKING_NO => $row[SALES_BOOKING_NO],
		BUYER_NAME => $row[BUYER_ID],
		JOB_NO => $row[JOB_NO],
		WITHIN_GROUP => $row[WITHIN_GROUP],
		QTY_KEY => $row[SALES_BOOKING_NO].$row[csf('id')].$row[csf('body_part_id')].trim($row[csf('fabric_desc')]).$row[csf('gsm_weight')].$row[csf('dia')].$row[csf('color_type_id')].$row[csf('pre_cost_fabric_cost_dtls_id')]
	);
	$bookingArr[$row[SALES_BOOKING_NO]]=$row[SALES_BOOKING_NO];
	$salse_id_arr[1][$row[JOB_NO]]=$row[JOB_NO];
	
}

//$bookingArr['5446546543521f']='5446546543521f';
//echo $program_qnty;

$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids, a.pre_cost_fabric_cost_dtls_id,a.status_active from ppl_planning_entry_plan_dtls a where a.is_sales=1 and a.is_revised=0 ".where_con_using_array($bookingArr,1,'a.booking_no')." group by a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,a.status_active";
 //echo $sql_plan;
	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan)
	{
		$KEY = $rowPlan[csf('booking_no')].$rowPlan[csf('po_id')].$rowPlan[csf('body_part_id')].$rowPlan[csf('fabric_desc')].$rowPlan[csf('gsm_weight')].$rowPlan[csf('dia')].$rowPlan[csf('color_type_id')].$rowPlan[csf('pre_cost_fabric_cost_dtls_id')];
		
		$program_data_array[$KEY][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
	}
	
	//print_r($program_data_array);
	
//................................................Planning Activities end;

//------------------------Yarn Received By Transfer

$dateCon =" AND A.TRANSFER_DATE BETWEEN '$previous_date' AND '$previous_date'";
$yarnTranSql = "select a.TRANSFER_DATE ,a.TO_COMPANY,a.COMPANY_ID,b.FROM_PROD_ID,c.PRODUCT_NAME_DETAILS,sum(b.TRANSFER_QNTY) as TRANSFER_QNTY,sum(b.TRANSFER_VALUE) as TRANSFER_VALUE,avg(b.RATE) as RATE from INV_ITEM_TRANSFER_MST a,INV_ITEM_TRANSFER_DTLS b,product_details_master c where a.id=b.mst_id and b.FROM_PROD_ID=c.id $dateCon and A.status_active=1 and B.status_active=1 AND a.IS_DELETED=0 AND B.IS_DELETED=0 group by a.TRANSFER_DATE ,a.TO_COMPANY,a.COMPANY_ID,b.FROM_PROD_ID,c.PRODUCT_NAME_DETAILS";
$yarnTranSqlRes=sql_select($yarnTranSql);
$yTransDataArr=array();
foreach($yarnTranSqlRes as $rows){
	
	$yTransDataArr[$rows[TO_COMPANY]][DATA_ARR][]=array(
		FROM_COMPANY_NAME => $company_lib[$rows[COMPANY_ID]],
		PRODUCT_NAME_DETAILS => $rows[PRODUCT_NAME_DETAILS],
		RATE => $rows[RATE],
		TRANSFER_VALUE => $rows[TRANSFER_VALUE],
		TRANSFER_QNTY => $rows[TRANSFER_QNTY],
	);
	$yTransDataArr[$rows[TO_COMPANY]][GRAND_TOTAL_VAL]	+=$rows[TRANSFER_VALUE];
	$yTransDataArr[$rows[TO_COMPANY]][GRAND_TOTAL_QTY]	+=$rows[TRANSFER_QNTY];
}
//..........................Yarn Received By Transfer end;



//------------------------Yarn Issued

$dateCon =" AND b.TRANSACTION_DATE BETWEEN '$previous_date' AND '$previous_date'";
$yarnIssueSql = "SELECT a.COMPANY_ID, a.ISSUE_PURPOSE, C.PRODUCT_NAME_DETAILS,b.TRANSACTION_DATE,sum(b.CONS_QUANTITY) as CONS_QUANTITY,sum(b.RETURN_QNTY) as RETURN_QNTY,avg(b.CONS_RATE) as CONS_RATE
	from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and b.item_category=1  and A.status_active=1 and B.status_active=1 AND a.IS_DELETED=0 AND B.IS_DELETED=0 AND c.status_active=1 AND c.IS_DELETED=0 $dateCon group by a.COMPANY_ID, a.ISSUE_PURPOSE, C.PRODUCT_NAME_DETAILS,b.TRANSACTION_DATE";
	 //echo $yarnIssueSql;
$yarnIssueSqlRes=sql_select($yarnIssueSql);
$yIssueDataArr=array();
foreach($yarnIssueSqlRes as $rows){
	
	$yIssueDataArr[$rows[COMPANY_ID]][DATA_ARR][]=array(
		PRODUCT_NAME_DETAILS => $rows[PRODUCT_NAME_DETAILS],
		ISSUE_PURPOSE => $rows[ISSUE_PURPOSE],
		RATE => $rows[CONS_RATE],
		VALUE => $rows[CONS_RATE]*$rows[CONS_QUANTITY],
		CONS_QUANTITY => $rows[CONS_QUANTITY],
	);
	$yIssueDataArr[$rows[COMPANY_ID]][GRAND_TOTAL_VAL]	+=0;
	$yIssueDataArr[$rows[COMPANY_ID]][GRAND_TOTAL_QTY]	+=$rows[CONS_QUANTITY];
	$yIssueDataArr[$rows[COMPANY_ID]][GRAND_TOTAL_VAL]	+=($rows[CONS_RATE]*$rows[CONS_QUANTITY]);
}
//..........................Yarn Issued end;



//------------------------Knitting Production

$dateCon =" AND a.RECEIVE_DATE BETWEEN '$previous_date' AND '$previous_date'";
$knitProSql = "select a.COMPANY_ID,a.KNITTING_SOURCE, sum(b.GREY_RECEIVE_QNTY) as GREY_RECEIVE_QNTY,sum(b.REJECT_FABRIC_RECEIVE) as REJECT_FABRIC_RECEIVE  from INV_RECEIVE_MASTER a,PRO_GREY_PROD_ENTRY_DTLS b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.entry_form=2 and a.knitting_source in(1,3) $dateCon group by a.COMPANY_ID,a.KNITTING_SOURCE";//,a.RECEIVE_DATE
	  // echo $knitProSql;
$knitProSqlRes=sql_select($knitProSql);
$knitProDataArr=array();
foreach($knitProSqlRes as $rows){
	
	$knitProDataArr[$rows[COMPANY_ID]][DATA_ARR][]=array(
		KNITTING_SOURCE => $rows[KNITTING_SOURCE],
		GREY_RECEIVE_QNTY => $rows[GREY_RECEIVE_QNTY],
		REJECT_FABRIC_RECEIVE => $rows[REJECT_FABRIC_RECEIVE],
	);
	$knitProDataArr[$rows[COMPANY_ID]][GRAND_TOTAL_REC]	+=$rows[GREY_RECEIVE_QNTY];
	$knitProDataArr[$rows[COMPANY_ID]][GRAND_TOTAL_REJ]	+=$rows[REJECT_FABRIC_RECEIVE];
}
//..........................Knitting Production end;





//------------------------Batch Preparation

$dateCon =" AND a.BATCH_DATE BETWEEN '$previous_date' AND '$previous_date'";
/*$batchSql = "select A.BOOKING_NO_ID,A.SALES_ORDER_NO,A.BATCH_AGAINST,A.IS_SALES,a.BOOKING_WITHOUT_ORDER,a.ID,a.COMPANY_ID,a.COLOR_ID,b.BATCH_QNTY,a.TOTAL_TRIMS_WEIGHT  from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and A.BATCH_AGAINST in(1,3) $dateCon";*/

$batchSql = "select c.BOOKING_WITHOUT_ORDER,c.WITHIN_GROUP,A.BOOKING_NO_ID,A.SALES_ORDER_NO,A.BATCH_AGAINST,A.IS_SALES,a.ID,a.COMPANY_ID,a.COLOR_ID,b.BATCH_QNTY,a.TOTAL_TRIMS_WEIGHT  from pro_batch_create_mst a,pro_batch_create_dtls b,fabric_sales_order_mst c where A.SALES_ORDER_NO=c.JOB_NO  and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and A.BATCH_AGAINST in(1,3) $dateCon";
	
	 // echo $batchSql;
	  
$batchSqlRes=sql_select($batchSql);
$batchDataArr=array();$booking_id_arr=array();$temp_batch_id=array();
foreach($batchSqlRes as $rows){
	if($temp_batch_id[$rows[ID]]==1){
		$rows[TOTAL_TRIMS_WEIGHT]=0;
	}
	$temp_batch_id[$rows[ID]]=1;
	
	
	$batchDataArr[$rows[COMPANY_ID]][BATCH_QNTY][$rows[COLOR_ID]]+=$rows[BATCH_QNTY];
	$batchDataArr[$rows[COMPANY_ID]][NUMBER_OF_BATCH][$rows[COLOR_ID]][$rows[ID]]=$rows[ID];
	$batchDataArr[$rows[COMPANY_ID]][GRAND_TOTAL] += ($rows[BATCH_QNTY]+$rows[TOTAL_TRIMS_WEIGHT]);
	//$booking_id_arr[$rows[BOOKING_WITHOUT_ORDER]][$rows[BOOKING_NO_ID]]=$rows[BOOKING_NO_ID];
	$salse_id_arr[$rows[IS_SALES]][$rows[SALES_ORDER_NO]]=$rows[SALES_ORDER_NO];
}
//..........................Batch Preparation end;


//..........................QC data;
	$dateCon =" AND a.RECEIVE_DATE BETWEEN '$previous_date' AND '$previous_date'";
	$ffQc="SELECT E.ID,e.BATCH_AGAINST,e.TOTAL_TRIMS_WEIGHT,d.BUYER_ID,a.RECEIVE_DATE,c.QC_PASS_QNTY as QTY,c.PO_BREAKDOWN_ID,a.COMPANY_ID,d.WITHIN_GROUP,d.JOB_NO,D.BOOKING_WITHOUT_ORDER
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b ,pro_roll_details c ,FABRIC_SALES_ORDER_MST d,pro_batch_create_mst e
		WHERE  e.id=b.batch_id and a.id=b.mst_id and b.id=c.dtls_id and c.PO_BREAKDOWN_ID=d.id and c.status_active=1 and b.status_active=1 and a.status_active=1 and a.entry_form in(66) $dateCon  and c.roll_no=b.roll_no";
	  //echo $ffQc;
	$ffQcRes=sql_select($ffQc);
    foreach($ffQcRes as $rows){
		$salse_id_arr[1][$rows[JOB_NO]]=$rows[JOB_NO];
    }
//..........................QC data end;


//----------------------------------Daying Pro
$dateCon =" AND a.PRODUCTION_DATE BETWEEN '$previous_date' AND '$previous_date'";
$daySql="SELECT a.ID,b.BATCH_AGAINST,b.TOTAL_TRIMS_WEIGHT,a.COMPANY_ID, a.PRODUCTION_DATE,b.SALES_ORDER_NO,b.BOOKING_WITHOUT_ORDER, sum(c.batch_qnty) as BATCH_QNTY
from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_sales=1 $dateCon 
group by a.ID,a.COMPANY_ID, a.PRODUCTION_DATE,b.SALES_ORDER_NO,b.BOOKING_WITHOUT_ORDER,b.BATCH_AGAINST,b.TOTAL_TRIMS_WEIGHT "; // and b.batch_against  in(1,2)
   //echo $daySql; 
$daySqlRes=sql_select($daySql);
    foreach($daySqlRes as $rows){
		$salse_id_arr[1][$rows[SALES_ORDER_NO]]=$rows[SALES_ORDER_NO];
    }
//..........................Daying end;
	$slsSql="select ID,COMPANY_ID,JOB_NO,SALES_BOOKING_NO,WITHIN_GROUP,BUYER_ID,STYLE_REF_NO, PO_BUYER,BOOKING_WITHOUT_ORDER from fabric_sales_order_mst where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($salse_id_arr[1],1,'JOB_NO')."";
	  //echo $slsSql;
	$sales_data_arr = sql_select($slsSql);
	
    foreach($sales_data_arr as $rows){
        $salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BOOKING_WITHOUT_ORDER']=$rows[BOOKING_WITHOUT_ORDER];
        $salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BUYER_ID']=$rows[BUYER_ID];
        $salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['WITHIN_GROUP']=$rows[WITHIN_GROUP];
		
		if($rows[WITHIN_GROUP]==1){
			$all_booking_arr['BOOKING_TO_JOB'][$rows[SALES_BOOKING_NO]]=$rows[JOB_NO];
			$all_booking_arr['SALES_BOOKING_NO'][$rows[SALES_BOOKING_NO]]=$rows[SALES_BOOKING_NO];
		}
		
    }
	
	
	//var_dump($all_booking_arr['BOOKING_TO_JOB']);die;
	
	$bookSql="select BOOKING_NO,BUYER_ID,COMPANY_ID from WO_BOOKING_MST where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($all_booking_arr['SALES_BOOKING_NO'],1,'BOOKING_NO')."";
	$bookSqlRes = sql_select($bookSql);
    foreach($bookSqlRes as $rows){
		$rows[JOB_NO]=$all_booking_arr['BOOKING_TO_JOB'][$rows[BOOKING_NO]];
		$all_booking_arr['JOB_TO_BUYER'][$rows[JOB_NO]]=$rows[BUYER_ID];
		$all_booking_arr['JOB_TO_COMPANY'][$rows[JOB_NO]]=$rows[COMPANY_ID];
    }
	
	
	
	$samBookSql="select BOOKING_NO,BUYER_ID,COMPANY_ID from WO_NON_ORD_SAMP_BOOKING_MST where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($all_booking_arr['SALES_BOOKING_NO'],1,'BOOKING_NO')."";
	$samBookSqlRes = sql_select($samBookSql);
    foreach($samBookSqlRes as $rows){
		$rows[JOB_NO]=$all_booking_arr['BOOKING_TO_JOB'][$rows[BOOKING_NO]];
		$all_booking_arr['JOB_TO_BUYER'][$rows[JOB_NO]]=$rows[BUYER_ID];
		$all_booking_arr['JOB_TO_COMPANY'][$rows[JOB_NO]]=$rows[COMPANY_ID];
    }
	
	


	$temp_batch_id=array();
    foreach($batchSqlRes as $rows){
        if($temp_batch_id[$rows[ID]]==1){
			$rows[TOTAL_TRIMS_WEIGHT]=0;
		}
		$temp_batch_id[$rows[ID]]=1;

		if($rows[BOOKING_WITHOUT_ORDER]==''){$rows[BOOKING_WITHOUT_ORDER]=0;}
		
		
		$rows[WITHIN_GROUP] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['WITHIN_GROUP'];
		$rows[BUYER_ID] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BUYER_ID'];
		$rows[PARTY_NAME] =($rows[WITHIN_GROUP]==1)?$company_lib[$rows[BUYER_ID]]:$buyer_lib[$rows[BUYER_ID]];
   
		
        if($rows[WITHIN_GROUP]==1){
			$rows[BUYER_ID] = $all_booking_arr['JOB_TO_BUYER'][$rows[SALES_ORDER_NO]];
		}
		else{
			$rows[BUYER_ID] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BUYER_ID'];
		}
		
		
		
		$key = $rows[PARTY_NAME].'**'.$rows[BUYER_ID];
        $batchDataArr[$rows[COMPANY_ID]][SALES_JOB_NO][$key]=$rows[JOB_NO];
		$batchDataArr[$rows[COMPANY_ID]][TRIMS_WEIGHT][$key]+=$rows[TOTAL_TRIMS_WEIGHT];
        $batchDataArr[$rows[COMPANY_ID]][BUYER_NAME][$key]=$rows[BUYER_NAME];
		
        if($rows[BATCH_AGAINST]==3){
			$batchDataArr[$rows[COMPANY_ID]][SB_WIDTH_ORDER_WITHOUT_ORDER][$key][$rows[BOOKING_WITHOUT_ORDER]]+=$rows[BATCH_QNTY];
		}
		else{
			$batchDataArr[$rows[COMPANY_ID]][WG_YES_NO][$key][$rows[WITHIN_GROUP]]+=$rows[BATCH_QNTY];
		}
		
		$batchDataArr[$rows[COMPANY_ID]][BUYER_TOTAL]+=$rows[BATCH_QNTY]+$rows[TOTAL_TRIMS_WEIGHT];
		
		
		
    }
    

	
//----------------------------------Daying Pro
	$temp_batch_id=array();
    foreach($daySqlRes as $rows){
        
        if($temp_batch_id[$rows[ID]]==1){
			$rows[TOTAL_TRIMS_WEIGHT]=0;
		}		
		$temp_batch_id[$rows[ID]]=1;
		
		$rows[BOOKING_WITHOUT_ORDER] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BOOKING_WITHOUT_ORDER'];
        $rows[WITHIN_GROUP] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['WITHIN_GROUP'];
        $rows[BUYER_ID] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BUYER_ID'];
        
	/*	$rows[PARTY_NAME] =($rows[WITHIN_GROUP]==1)?$company_lib[$rows[BUYER_ID]]:$buyer_lib[$rows[BUYER_ID]];
        if($rows[WITHIN_GROUP]==1){
			$rows[BUYER_ID] = $all_booking_arr['JOB_TO_BUYER'][$rows[SALES_ORDER_NO]];
		}
		else{
			$rows[BUYER_ID] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BUYER_ID'];
		}*/
		
		
        if($rows[WITHIN_GROUP]==1){
			$rows[BUYER_ID] = $all_booking_arr['JOB_TO_BUYER'][$rows[SALES_ORDER_NO]];
			$rows[PARTY_NAME] = $company_lib[$all_booking_arr['JOB_TO_COMPANY'][$rows[SALES_ORDER_NO]]];
		}
		else{
			//$rows[BUYER_ID] = $salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BUYER_ID'];
			$rows[PARTY_NAME] = $buyer_lib[$salesDataArr[$rows[COMPANY_ID]][$rows[SALES_ORDER_NO]]['BUYER_ID']];
		}
		
		
		
		$key = $rows[PARTY_NAME].'**'.$rows[BUYER_ID];
        $dayDataArr[$rows[COMPANY_ID]][SALES_JOB_NO][$key]=$rows[SALES_ORDER_NO];
        $dayDataArr[$rows[COMPANY_ID]][TRIMS_WEIGHT][$key]+=$rows[TOTAL_TRIMS_WEIGHT];
   
   
        if($rows[BATCH_AGAINST]==3){
			$dayDataArr[$rows[COMPANY_ID]][SB_WIDTH_ORDER_WITHOUT_ORDER][$key][$rows[BOOKING_WITHOUT_ORDER]]+=$rows[BATCH_QNTY];
		}
		else{
			$dayDataArr[$rows[COMPANY_ID]][WG_YES_NO][$key][$rows[WITHIN_GROUP]]+=$rows[BATCH_QNTY];
		}
		
		$dyeDataArr[$rows[COMPANY_ID]][BUYER_TOTAL]+=$rows[BATCH_QNTY]+$rows[TOTAL_TRIMS_WEIGHT];
	
	
	}
	
//print_r($dayDataArr[3][SB_WIDTH_ORDER_WITHOUT_ORDER]);	
//------------------------------------daying pro end;


$dateCon =" AND a.RECEIVE_DATE BETWEEN '$previous_date' AND '$previous_date'";
$ffpSql ="select a.COMPANY_ID,a.RECEIVE_DATE,b.BUYER_ID,b.PROCESS_ID,b.RECEIVE_QNTY from INV_RECEIVE_MASTER a,PRO_FINISH_FABRIC_RCV_DTLS b where a.id=b.mst_id $dateCon and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
    //echo $ffpSql;
$ffpSqlRes=sql_select($ffpSql);
$ffpDataArr=array();
foreach($ffpSqlRes as $rows){
	$key = $rows[PROCESS_ID].'**'.$rows[BUYER_ID];
	$ffpDataArr[$rows[COMPANY_ID]][QTY][$key]+=$rows[RECEIVE_QNTY];
}

 //var_dump($ffpDataArr[3][QTY]);die;

/*	$dateCon =" AND a.RECEIVE_DATE BETWEEN '$previous_date' AND '$previous_date'";
	$ffQc="SELECT E.ID,e.BATCH_AGAINST,e.TOTAL_TRIMS_WEIGHT,d.BUYER_ID,a.RECEIVE_DATE,c.qnty as QTY,c.PO_BREAKDOWN_ID,a.COMPANY_ID,d.WITHIN_GROUP,d.JOB_NO,D.BOOKING_WITHOUT_ORDER
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b ,pro_roll_details c ,FABRIC_SALES_ORDER_MST d,pro_batch_create_mst e
		WHERE  e.id=b.batch_id and a.id=b.mst_id and b.id=c.dtls_id and c.PO_BREAKDOWN_ID=d.id and c.status_active=1 and b.status_active=1 and a.status_active=1 and a.entry_form in(66) $dateCon  and c.roll_no=b.roll_no";
	  //echo $ffQc;
	$ffQcRes=sql_select($ffQc);
*/	
	
	$ffQcDataArr=array();$temp_batch_id=array();
    foreach($ffQcRes as $rows){
        
        if($temp_batch_id[$rows[ID]]==1){
			$rows[TOTAL_TRIMS_WEIGHT]=0;
		}		
		$temp_batch_id[$rows[ID]]=1;


        $rows[PARTY_NAME] =($rows[WITHIN_GROUP]==1)?$company_lib[$rows[BUYER_ID]]:$buyer_lib[$rows[BUYER_ID]];
        if($rows[WITHIN_GROUP]==1){
			$rows[BUYER_ID] = $all_booking_arr['JOB_TO_BUYER'][$rows[JOB_NO]];
		}
		else{
			$rows[BUYER_ID] = $salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BUYER_ID'];
		}
		
		
		$key = $rows[PARTY_NAME].'**'.$rows[BUYER_ID];
		
        $ffQcDataArr[$rows[COMPANY_ID]][JOB_NO][$key]=$rows[JOB_NO];
        
		if($rows[BATCH_AGAINST]==3){
			$ffQcDataArr[$rows[COMPANY_ID]][SB_WIDTH_ORDER_WITHOUT_ORDER][$key][$rows[BOOKING_WITHOUT_ORDER]]+=$rows[QTY];
		}
		else{
			$ffQcDataArr[$rows[COMPANY_ID]][WG_YES_NO][$key][$rows[WITHIN_GROUP]]+=$rows[QTY];
		}
		
		$ffDataArr[$rows[COMPANY_ID]][BUYER_TOTAL]+=$rows[QTY]+$rows[TOTAL_TRIMS_WEIGHT];
        $ffQcDataArr[$rows[COMPANY_ID]][TRIMS_WEIGHT][$key]+=$rows[TOTAL_TRIMS_WEIGHT];

		
    }


/*	$dateCon =" AND a.receive_date BETWEEN '$previous_date' AND '$previous_date'";
	
	$finish_pro_sql="select a.knitting_company,sum(b.qnty) QTY 
from inv_receive_master a ,pro_roll_details b ,fabric_sales_order_mst c ,fabric_sales_order_dtls d ,PRO_GREY_PROD_ENTRY_DTLS e
where a.id=b.mst_id and b.po_breakdown_id=c.id and a.receive_basis=2 and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.id=e.mst_id and d.GSM_WEIGHT=e.GSM  and b.DTLS_ID=e.id and e.BODY_PART_ID=d.BODY_PART_ID
and a.status_active=1 and d.status_active=1 and b.status_active=1 and c.status_active=1 and c.id=d.mst_id $dateCon 
";
	$finish_pro_sql_res=sql_select($finish_pro_sql);
	$finish_pro_sql_res=array();
	foreach($finish_pro_sql_res as $rows){
	
	
	}

*/

//print_r($all_booking_arr['JOB_TO_BUYER']);
$flag=0;
foreach($company_lib as $company_id=>$company_name){


 ob_start();	
?>

<table cellspacing="0" cellpadding="0" width="600">
    <tr><th><b style="font-size:22px;">Daily Production Activities FSO</b></th></tr>
    <tr><th><b style="font-size:18px;"><?=$company_lib[$company_id];?></b></th></tr>
    <tr><th><b>Date: <?=$previous_date;?></b></th></tr>
</table><br />
<table border="1" rules="all">
	<caption><b>Fabric Sales Order Received Status</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Party</th>
        <th>Buyer</th>
        <th>Booking No</th>
        <th>Approved Date</th>
        <th>Delivery Date</th>
        <th>Qty.(Kg)</th>
    </thead>
    <tbody>
    <? 
	
	$i=1;
	foreach($fsoDataArr[$company_id][DATA_ARR] as $rows){
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	$flag=1;
	//echo $rows[WITHIN_GROUP].'='.$all_booking_arr['JOB_TO_BUYER'][$rows[JOB_NO]].'**';
		
        if($rows[WITHIN_GROUP]==1){
			$rows[BUYER_NAME] = $buyer_lib[$all_booking_arr['JOB_TO_BUYER'][$rows[JOB_NO]]];
			$rows[COMPANY_ID] = $company_lib[$all_booking_arr['JOB_TO_COMPANY'][$rows[JOB_NO]]];
		}
		else{
			$rows[BUYER_NAME] = $buyer_lib[$salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BUYER_ID']];
			$rows[COMPANY_ID] = $buyer_lib[$salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BUYER_ID']];
		}
		
	
	
	?>
    <tr bgcolor="<? echo $bgcolor;?>">
        <td align="center"><?=$i;?></td>
        <td><?=$rows[PARTY_NAME];?></td>
        <td><?=$rows[BUYER_NAME];?></td>
        <td align="center"><?=$rows[BOOKING_NO];?></td>
        <td align="center"><?=$rows[APPROVED_DATE];?></td>
        <td align="center"><?=$rows[DELIVERY_DATE];?></td>
        <td align="right"><?=decimal_format($rows[QTY], 1, ",");?></td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
    <tfoot bgcolor="#CCCCCC">
        <th colspan="6" align="right">Total : </th>
        <th align="right"><?=decimal_format($fsoDataArr[$company_id][GRAND_TOTAL], 1, ",");?></th>
    </tfoot>
</table>

<table border="1" rules="all">
	<caption><b>Planning Activities</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Party</th>
        <th>Buyer</th>
        <th>Booking No</th>
        <th>Plan Qty[Kg]</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	foreach($planDataArr[$company_id][DATA_ARR] as $rows){

		if($program_data_array[$rows[QTY_KEY]][1]['program_qnty']){
			$planDataArr[$row[COMPANY_ID]][GRAND_TOTAL]+=$program_data_array[$rows[QTY_KEY]][1]['program_qnty'];
			
			if($rows[WITHIN_GROUP]==1){
				$rows[BUYER_NAME] = $buyer_lib[$all_booking_arr['JOB_TO_BUYER'][$rows[JOB_NO]]];
				$rows[COMPANY_ID] = $company_lib[$all_booking_arr['JOB_TO_COMPANY'][$rows[JOB_NO]]];
			}
			else{
				$rows[BUYER_NAME] = $buyer_lib[$salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BUYER_ID']];
				$rows[COMPANY_ID] = $buyer_lib[$salesDataArr[$rows[COMPANY_ID]][$rows[JOB_NO]]['BUYER_ID']];
			}
			
			$new_planDataArr[$company_id][DATA_ARR][$rows[COMPANY_ID].'**'.$rows[BUYER_NAME].'**'.$rows[BOOKING_NO]]+=$program_data_array[$rows[QTY_KEY]][1]['program_qnty'];
		}
		
	}
		
		
	foreach($new_planDataArr[$company_id][DATA_ARR] as $key_val=>$proQty){
		list($rows[COMPANY_ID],$rows[BUYER_NAME],$rows[BOOKING_NO])=explode('**',$key_val);
		$flag=1;
		?>
		<tr>
			<td><?=$i;?></td>
			<td title="<?=$rows[WITHIN_GROUP].','.$rows[JOB_NO];?>"><?=$rows[COMPANY_ID];?></td>
			<td><?=$rows[BUYER_NAME];?></td>
			<td><?=$rows[BOOKING_NO];?></td>
			<td align="right"><?=decimal_format($proQty, 1, ",");?></td>
		</tr>
		<? 
		$i++;
		}
	 ?>
   </tbody>
    <tfoot bgcolor="#CCCCCC">
        <th colspan="4" align="right">Total : </th>
        <th align="right"><?=decimal_format($planDataArr[$company_id][GRAND_TOTAL], 1, ",");?></th>
    </tfoot>
</table>

<table border="1" rules="all">
	<caption><b>Yarn Received (Transfer)</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Supplier Name</th>
        <th>Yarn Description</th>
        <th>Qty. (Kg)</th>
        <th>Value</th>
        <th>Avg. Rate(Tk.)</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	foreach($yTransDataArr[$company_id][DATA_ARR] as $rows){
	$flag=1;
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$rows[FROM_COMPANY_NAME];?></td>
        <td><?=$rows[PRODUCT_NAME_DETAILS];?></td>
        <td align="right"><?=decimal_format($rows[TRANSFER_QNTY], 1, ",");?></td>
        <td align="right"><?=decimal_format($rows[TRANSFER_VALUE], 4, ",");?></td>
        <td align="right"><?=decimal_format($rows[RATE], 3, ",");?></td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
    <tfoot bgcolor="#CCCCCC">
        <th colspan="3" align="right">Total : </th>
        <th align="right"><?=decimal_format($yTransDataArr[$company_id][GRAND_TOTAL_QTY], 1, ",");?></th>
        <th align="right"><?=decimal_format($yTransDataArr[$company_id][GRAND_TOTAL_VAL], 4, ",");?></th>
        <th align="right"></th>
    </tfoot>
</table>

<table border="1" rules="all">
	<caption><b>Yarn Issued</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Yarn Description</th>
        <th>Purpose</th>
        <th>Qty. (Kg)</th>
        <th>Value</th>
        <th>Avg. Rate(Tk.)</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	foreach($yIssueDataArr[$company_id][DATA_ARR] as $rows){
	$flag=1;
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$rows[PRODUCT_NAME_DETAILS];?></td>
        <td><?=$yarn_issue_purpose[$rows[ISSUE_PURPOSE]];?></td>
        <td align="right"><?=decimal_format($rows[CONS_QUANTITY], 1, ",");?></td>
        <td align="right"><?=decimal_format($rows[VALUE], 4, ",");?></td>
        <td align="right"><?=decimal_format($rows[RATE], 3, ",");?></td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
    <tfoot bgcolor="#CCCCCC">
        <th colspan="3" align="right">Total : </th>
        <th align="right"><?=decimal_format($yIssueDataArr[$company_id][GRAND_TOTAL_QTY], 1, ",");?></th>
        <th align="right"><?=decimal_format($yIssueDataArr[$company_id][GRAND_TOTAL_VAL], 4, ",");?></th>
        <th align="right"></th>
    </tfoot>
</table>

<table border="1" rules="all">
	<caption><b>Knitting Production</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Source</th>
        <th>QC Pass Qty.</th>
        <th>Reject Qty.</th>
        <th>Reject %</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	foreach($knitProDataArr[$company_id][DATA_ARR] as $rows){
	$flag=1;
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$knitting_source[$rows[KNITTING_SOURCE]];?></td>
        <td align="right"><?=decimal_format($rows[GREY_RECEIVE_QNTY],9,',');?></td>
        <td align="right"><?=decimal_format($rows[REJECT_FABRIC_RECEIVE],9,',');?></td>
        <td align="right"><?=decimal_format((($rows[GREY_RECEIVE_QNTY]/100)*$rows[REJECT_FABRIC_RECEIVE]),7,',');?></td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
    <tfoot bgcolor="#CCCCCC">
        <th colspan="2" align="right">Total : </th>
        <th align="right"><?=decimal_format($knitProDataArr[$company_id][GRAND_TOTAL_REC], 9, ",");?></th>
        <th align="right"><?=decimal_format($knitProDataArr[$company_id][GRAND_TOTAL_REJ], 9, ",");?></th>
        <th align="right"></th>
    </tfoot>
</table>

<table border="1" rules="all">
	<caption><b>Batch Preparation - 1</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>No of Batch</th>
        <th>Colour</th>
        <th>Batch Qty. (Kg)</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	foreach($batchDataArr[$company_id][BATCH_QNTY] as $COLOR_ID=>$BQTY){
	$flag=1;
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?= count($batchDataArr[$company_id][NUMBER_OF_BATCH][$COLOR_ID]);?></td>
        <td><?= $color_lib[$COLOR_ID];?></td>
        <td align="right"><?=$BQTY;?></td>
    </tr>
    <? 
	$i++;
	} 
	?>
   </tbody>
    <tfoot bgcolor="#CCCCCC">
        <th colspan="3" align="right">Total : </th>
        <th align="right"><?=decimal_format($batchDataArr[$company_id][GRAND_TOTAL],1, ",");?></th>
    </tfoot>
</table>

<table border="1" rules="all">
	<caption><b>Batch Preparation - 2</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Party</th>
        <th>Buyer</th>
        <th>W/G Yes</th>
        <th>Smpl. Batch With Order</th>
        <th>Smpl. Batch Without Order</th>
        <th>Trims Weight</th>
        <th>W/G No</th>
        <th>Buyer Total</th>
        <th>%</th>
    </thead>
    <tbody>
    <?
	$i=1;
	$batchGrandDataArr=array();
	$batchTotalDataArr=array();
	foreach($batchDataArr[$company_id][SALES_JOB_NO] as $party_buyer_id=>$rows){
	$flag=1;
        list($party,$buyer_id)=explode('**',$party_buyer_id);
		$batchGrandDataArr[WG_YES]+=$batchDataArr[$company_id][WG_YES_NO][$party_buyer_id][1];
		$batchGrandDataArr[SB_WIDTH_ORDER]+=$batchDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][0];
		$batchGrandDataArr[SB_WITHOUT_ORDER]+=$batchDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][1];
		$batchGrandDataArr[TRIMS_WEIGHT]+=$batchDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id];
		$batchGrandDataArr[WG_NO]+=$batchDataArr[$company_id][WG_YES_NO][$party_buyer_id][2];
		
		
		$batchTotalDataArr[$party_buyer_id]+=$batchDataArr[$company_id][WG_YES_NO][$party_buyer_id][1];
		$batchTotalDataArr[$party_buyer_id]+=$batchDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][0];
		$batchTotalDataArr[$party_buyer_id]+=$batchDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][1];
		$batchTotalDataArr[$party_buyer_id]+=$batchDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id];
		$batchTotalDataArr[$party_buyer_id]+=$batchDataArr[$company_id][WG_YES_NO][$party_buyer_id][2];
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$party;?></td>
        <td><?=$buyer_lib[$buyer_id];?></td>
        <td align="right"><?=$batchDataArr[$company_id][WG_YES_NO][$party_buyer_id][1];?></td>
        <td align="right"><?=$batchDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][0];?></td>
        <td align="right"><?=$batchDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][1];?></td>
        <td align="center"><?=$batchDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id];?></td>
        <td align="right"><?=$batchDataArr[$company_id][WG_YES_NO][$party_buyer_id][2];?></td>
        <td align="right"><?=$batchTotalDataArr[$party_buyer_id];?></td>
        <td align="right"><?=number_format(($batchTotalDataArr[$party_buyer_id]/$batchDataArr[$company_id][BUYER_TOTAL])*100,2);?> </td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
   <tfoot>
    <tr bgcolor="#CCCCCC">
        <th colspan="3" align="right">Total : </th>
        <th align="right"><?=$batchGrandDataArr[WG_YES];?></th>
        <th align="right"><?=$batchGrandDataArr[SB_WIDTH_ORDER];?></th>
        <th align="right"><?=$batchGrandDataArr[SB_WITHOUT_ORDER];?></th>
        <th align="center"><?=$batchGrandDataArr[TRIMS_WEIGHT];?></th>
        <th align="right"><?=$batchGrandDataArr[WG_NO];?></th>
        <th align="right"><?=array_sum($batchTotalDataArr);?></th>
        <th>100 </th>
    </tr>
   </tfoot>
   
</table>

<table border="1" rules="all">
	<caption><b>Dyeing Production</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Party</th>
        <th>Buyer</th>
        <th>W/G Yes</th>
        <th>Smpl. Batch With Order</th>
        <th>Smpl. Batch Without Order</th>
        <th>Trims Weight</th>
        <th>W/G No</th>
        <th>Buyer Total</th>
        <th>%</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	$dyeGrandDataArr=array();
	$dyeTotalDataArr=array();
	foreach($dayDataArr[$company_id][SALES_JOB_NO] as $party_buyer_id=>$rows){
	$flag=1;
        list($party,$buyer_id)=explode('**',$party_buyer_id);
		
		$dyeGrandDataArr[WG_YES]+=$dayDataArr[$company_id][WG_YES_NO][$party_buyer_id][1];
		$dyeGrandDataArr[SB_WIDTH_ORDER]+=$dayDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][0];
		$dyeGrandDataArr[SB_WITHOUT_ORDER]+=$dayDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][1];
		$dyeGrandDataArr[TRIMS_WEIGHT]+=$dayDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id];
		$dyeGrandDataArr[WG_NO]+=$dayDataArr[$company_id][WG_YES_NO][$party_buyer_id][2];
		
		
		$dyeTotalDataArr[$party_buyer_id]+=$dayDataArr[$company_id][WG_YES_NO][$party_buyer_id][1];
		$dyeTotalDataArr[$party_buyer_id]+=$dayDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][0];
		$dyeTotalDataArr[$party_buyer_id]+=$dayDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][1];
		$dyeTotalDataArr[$party_buyer_id]+=$dayDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id];
		$dyeTotalDataArr[$party_buyer_id]+=$dayDataArr[$company_id][WG_YES_NO][$party_buyer_id][2];
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$party;?></td>
        <td><?=$buyer_lib[$buyer_id];?></td>
        <td align="right"><?=$dayDataArr[$company_id][WG_YES_NO][$party_buyer_id][1];?></td>
        <td align="right"><?=$dayDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][0];?></td>
        <td align="right"><?=$dayDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id][1];?></td>
        <td align="center"><?=$dayDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id];?></td>
        <td align="right"><?=$dayDataArr[$company_id][WG_YES_NO][$party_buyer_id][2];?></td>
        <td align="right"><?=$dyeTotalDataArr[$party_buyer_id];?></td>
        <td align="right"><?=number_format(($dyeTotalDataArr[$party_buyer_id]/$dyeDataArr[$company_id][BUYER_TOTAL])*100,2);?> </td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
   <tfoot>
    <tr bgcolor="#CCCCCC">
        <th colspan="3" align="right">Total : </th>
        <th align="right"><?=$dyeGrandDataArr[WG_YES];?></th>
        <th align="right"><?=$dyeGrandDataArr[SB_WIDTH_ORDER];?></th>
        <th align="right"><?=$dyeGrandDataArr[SB_WITHOUT_ORDER];?></th>
        <th align="center"><?=$dyeGrandDataArr[TRIMS_WEIGHT];?></th>
        <th align="right"><?=$dyeGrandDataArr[WG_NO];?></th>
        <th align="right"><?=array_sum($dyeTotalDataArr);?></th>
        <th>100 </th>
    </tr>
   </tfoot>
</table>

 
<table border="1" rules="all" style="display:none;">
	<caption><b>Fabric Finishing Production</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Finishing Process</th>
        <th>Buyer</th>
        <th>Qty. (Kg)</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	foreach($ffpDataArr[$company_id][QTY] as $key => $val){
	$flag=1;
	list($fprocess_id,$buyer_id)=explode('**',$key);
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$conversion_cost_head_array[$fprocess_id];?></td>
        <td><?=$buyer_lib[$buyer_id];?></td>
        <td align="right"><?=$val;?></td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
</table>


<table border="1" rules="all">
	<caption><b>QC Pass Finish Fabric</b></caption>
    <thead bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Party</th>
        <th>Buyer</th>
        <th>W/G Yes</th>
        <th>Smpl. Batch With Order</th>
        <th>Smpl. Batch Without Order</th>
        <th>Trims Weight</th>
        <th>W/G No</th>
        <th>Buyer Total</th>
        <th>%</th>
    </thead>
    <tbody>
    <? 
	$i=1;
	$ffGrandDataArr=array();
	$ffTotalDataArr=array();
	
	foreach($ffQcDataArr[$company_id][JOB_NO] as $party_buyer_id_key=>$rows){
	$flag=1;
        list($party,$buyer_id)=explode('**',$party_buyer_id_key);
		
		$ffGrandDataArr[WG_YES]+=$ffQcDataArr[$company_id][WG_YES_NO][$party_buyer_id_key][1];
		$ffGrandDataArr[SB_WIDTH_ORDER]+=$ffQcDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id_key][0];
		$ffGrandDataArr[SB_WITHOUT_ORDER]+=$ffQcDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id_key][1];
		$ffGrandDataArr[TRIMS_WEIGHT]+=$ffQcDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id_key];
		$ffGrandDataArr[WG_NO]+=$ffQcDataArr[$company_id][WG_YES_NO][$party_buyer_id_key][2];
		
		
		$ffTotalDataArr[$party_buyer_id_key]+=$ffQcDataArr[$company_id][WG_YES_NO][$party_buyer_id_key][1];
		$ffTotalDataArr[$party_buyer_id_key]+=$ffQcDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id_key][0];
		$ffTotalDataArr[$party_buyer_id_key]+=$ffQcDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id_key][1];
		$ffTotalDataArr[$party_buyer_id_key]+=$ffQcDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id_key];
		$ffTotalDataArr[$party_buyer_id_key]+=$ffQcDataArr[$company_id][WG_YES_NO][$party_buyer_id_key][2];
		
	?>
    <tr>
        <td><?=$i;?></td>
        <td><?=$party;?></td>
        <td><?=$buyer_lib[$buyer_id];?></td>
        <td align="right"><?=$ffQcDataArr[$company_id][WG_YES_NO][$party_buyer_id_key][1];?></td>
        <td align="right"><?=$ffQcDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id_key][0];?></td>
        <td align="right"><?=$ffQcDataArr[$company_id][SB_WIDTH_ORDER_WITHOUT_ORDER][$party_buyer_id_key][1];?></td>
        <td align="center"><?=$ffQcDataArr[$company_id][TRIMS_WEIGHT][$party_buyer_id_key];?></td>
        <td align="right"><?=$ffQcDataArr[$company_id][WG_YES_NO][$party_buyer_id_key][2];?></td>
        <td align="right"><?=$ffTotalDataArr[$party_buyer_id_key];?></td>
        <td align="right"><?=number_format(($ffTotalDataArr[$party_buyer_id_key]/$ffDataArr[$company_id][BUYER_TOTAL])*100,2);?> </td>
    </tr>
    <? 
	$i++;
	} ?>
   </tbody>
   <tfoot>
    <tr bgcolor="#CCCCCC">
        <th colspan="3" align="right">Total : </th>
        <th align="right"><?=$ffGrandDataArr[WG_YES];?></th>
        <th align="right"><?=$ffGrandDataArr[SB_WIDTH_ORDER];?></th>
        <th align="right"><?=$ffGrandDataArr[SB_WITHOUT_ORDER];?></th>
        <th align="center"><?=$ffGrandDataArr[TRIMS_WEIGHT];?></th>
        <th align="right"><?=$ffGrandDataArr[WG_NO];?></th>
        <th align="right"><?=array_sum($ffTotalDataArr);?></th>
        <th>100 </th>
    </tr>
   </tfoot>
</table>



<?
		
	$message=ob_get_contents();
	ob_clean();
	$mail_item=83;
	$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=83 and b.mail_user_setup_id=c.id and a.company_id =$company_id AND a.MAIL_TYPE=1";
	$mail_sql_res=sql_select($sql);
	
	$mailArr=array();
	foreach($mail_sql_res as $row)
	{
		$mailArr[$row[EMAIL]]=$row[EMAIL]; 
	}
	$to=implode(',',$mailArr);

	$subject="Daily Production Activities FSO";
	$header=mailHeader();
	//if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $to.$message;
	}
	else{
		if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}
	 
}

?>


