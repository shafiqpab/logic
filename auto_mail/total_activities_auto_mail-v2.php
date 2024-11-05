<?php
date_default_timezone_set("Asia/Dhaka");

include('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
include('setting/mail_setting.php');


$company_lib =return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_lib =return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$supplier_library 	=return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");


	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	if($_REQUEST['view_date']){
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
	}
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	$is_insert_date=0;
	
	
 	//$previous_date= $current_date='01-Apr-2022';

	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
	}

 $company_exchange_rate_arr =return_library_array( "select COMPANY_ID,CONVERSION_RATE from CURRENCY_CONVERSION_RATE where id in(select max(id) from CURRENCY_CONVERSION_RATE where CURRENCY=2 and STATUS_ACTIVE=1 and IS_DELETED=0 group by COMPANY_ID)", "COMPANY_ID", "CONVERSION_RATE");
//print_r($company_exchange_rate_arr);die;

	
	
//Order Received [Bulk]......................................	
	if($is_insert_date==1){
		$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con =" and b.po_received_date between '$previous_date' and '$previous_date'";
	}

	$company_id_str=implode(',',array_keys($company_lib));
	
 

	$order_sql="select a.COMPANY_NAME,a.BUYER_NAME, sum(b.SHIPMENT_DATE-b.PO_RECEIVED_DATE) as LEAD_TIME,count(b.id) as TOTAL_PO,
	  sum(CASE WHEN b.IS_CONFIRMED=1 THEN  (a.total_set_qnty*b.po_quantity) END) AS CON_PO_QTY	,
	  sum(CASE WHEN b.IS_CONFIRMED=1 THEN  b.po_total_price END) AS CON_PO_VAL	,
	  sum(CASE WHEN b.IS_CONFIRMED=2 THEN  (a.total_set_qnty*b.po_quantity) END) AS PROJ_PO_QTY,	
	  sum(CASE WHEN b.IS_CONFIRMED=2 THEN  b.po_total_price END) AS PROJ_PO_VAL	
	 from
	 wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 and a.COMPANY_NAME in($company_id_str) $date_con group by a.company_name,a.buyer_name";
	  //echo $order_sql;die;

	$order_sql_result=sql_select($order_sql);
	$order_data_arr=array();
	foreach($order_sql_result as $row)
	{
		$order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]]=array(
			BUYER_NAME=>$row[BUYER_NAME],
			LEAD_TIME=>$row[LEAD_TIME],
			AVG_LEAD_TIME=>$row[LEAD_TIME]/$row[TOTAL_PO],
			TOTAL_PO=>$row[TOTAL_PO],
			CON_PO_QTY=>$row[CON_PO_QTY],
			CON_PO_VAL=>$row[CON_PO_VAL],
			PROJ_PO_QTY=>$row[PROJ_PO_QTY],
			PROJ_PO_VAL=>$row[PROJ_PO_VAL],
		);
	}
	
//Order Received [Sample].................................
	if($is_insert_date==1){
		$date_con =" and c.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con =" and c.po_received_date between '$previous_date' and '$previous_date'";
	}

	$smp_order_sql="select a.COMPANY_NAME,a.BUYER_NAME,c.PO_BREAK_DOWN_ID, 
	(b.SHIPMENT_DATE-b.PO_RECEIVED_DATE) as LEAD_TIME,
	(a.total_set_qnty*b.po_quantity) AS QTY,
	(b.po_total_price) AS AMOUNT	
	 from wo_po_details_master a, wo_po_break_down b,WO_BOOKING_DTLS c where a.job_no=b.job_no_mst  and b.id=c.PO_BREAK_DOWN_ID and c.BOOKING_TYPE=4 and c.IS_SHORT=2 and c.SAMPLE_TYPE=23 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.COMPANY_NAME in($company_id_str) $date_con";
	  //echo $smp_order_sql;
	
	/*$smp_order_sql___="select a.COMPANY_ID,a.BUYER_ID,sum(b.FIN_FAB_QNTY) as FIN_FAB_QNTY,sum(b.AMOUNT) as AMOUNT,sum(a.DELIVERY_DATE-a.BOOKING_DATE) as LEAD_TIME from WO_BOOKING_MST a,WO_BOOKING_DTLS b,wo_po_break_down c where a.BOOKING_NO = b.BOOKING_NO and c.id=b.PO_BREAK_DOWN_ID  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.COMPANY_ID in($company_id_str) $date_con  group by a.COMPANY_ID,a.BUYER_ID";*/	
	
	$smp_order_sql_result=sql_select($smp_order_sql);
	$smp_order_data_arr=array();
	foreach($smp_order_sql_result as $row)
	{
		
		$smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][QTY][$row[PO_BREAK_DOWN_ID]]=$row[QTY];
		$smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][VAL][$row[PO_BREAK_DOWN_ID]]=$row[AMOUNT];
		$smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][LEAD_TIME][$row[PO_BREAK_DOWN_ID]]=$row[LEAD_TIME];
		
		$smp_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]]=array(
			BUYER_ID=>$row[BUYER_NAME],
			LEAD_TIME=>array_sum($smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][LEAD_TIME]),
			AVG_LEAD_TIME=>array_sum($smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][LEAD_TIME]),
			TOTAL_PO=>count($smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][LEAD_TIME]),
			QTY=>array_sum($smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][QTY]),
			VAL=>array_sum($smp_with_order_data_arr[$row[COMPANY_NAME]][$row[BUYER_NAME]][VAL]),
		);
		
		
	}
	

	if($is_insert_date==1){
		$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con =" and b.po_received_date between '$previous_date' and '$previous_date'";
	}
	$smp_without_order_sql="select a.COMPANY_ID,a.BUYER_NAME,sum(b.SUBMISSION_QTY) as SUBMISSION_QTY,sum(b.SUBMISSION_QTY*b.SAMPLE_CHARGE) as SUBMISSION_VAL,sum(b.DELV_END_DATE-b.DELV_START_DATE) as LEAD_TIME  from SAMPLE_DEVELOPMENT_MST a,SAMPLE_DEVELOPMENT_DTLS b where a.id=b.SAMPLE_MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.COMPANY_ID in($company_id_str) $date_con  group by a.COMPANY_ID,a.BUYER_NAME";	
	//echo $smp_without_order_sql;
	$smp_without_order_sql_result=sql_select($smp_without_order_sql);
	foreach($smp_without_order_sql_result as $row)
	{
		$smp_order_data_arr[$row[COMPANY_ID]][$row[BUYER_NAME]][WITHOUT_ORDER_QTY]+=$row[SUBMISSION_QTY];
		$smp_order_data_arr[$row[COMPANY_ID]][$row[BUYER_NAME]][WITHOUT_ORDER_VAL]+=$row[SUBMISSION_VAL];
	}


//LC SC.....................................................
	if($is_insert_date==1){
		$date_con =" and insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$str_cond_lc_date=" and lc_date between '$previous_date' and '$current_date'";
		$str_cond_sc_date=" and contract_date between '$previous_date' and '$current_date'";
	}


	$sql_lc_sc="SELECT beneficiary_name as COMPANY_ID, BUYER_NAME,sum(lc_value) as LC_SC_VALUE, 1 as TYPE, export_lc_no as NO from com_export_lc where status_active=1 and is_deleted=0 $date_con $str_cond_lc_date group by beneficiary_name,buyer_name,export_lc_no
	union all
	SELECT beneficiary_name as COMPANY_ID, BUYER_NAME,sum(contract_value) as LC_SC_VALUE, 2 as TYPE, contract_no as NO from com_sales_contract where status_active=1 and is_deleted=0 $date_con $str_cond_sc_date group by beneficiary_name,buyer_name,contract_no order by buyer_name";
	// echo $sql_lc_sc;
	$sql_lc_sc_result=sql_select($sql_lc_sc);
	$lc_sc_data_arr=array();
	foreach($sql_lc_sc_result as $row)
	{
		
		$lc_sc_data_arr[$row[COMPANY_ID]][$row[BUYER_NAME]][$row[TYPE]]=array(
			BUYER_NAME=>$row[BUYER_NAME],
			TYPE=>$row[TYPE],
			LC_SC_VALUE=>$row[LC_SC_VALUE],
			NO=>$row[NO],
		);
	}
	
//BTB........................................................
    if($is_insert_date==1){
			$date_con =" and insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
        }
		else{
			$date_con=" and lc_date between '$previous_date' and '$current_date'";
		}
		
        $backToBackArr=array();
		$catTotal=array();
		$supTotal=array();
		
		$sql_back_back="Select IMPORTER_ID,SUPPLIER_ID, sum(lc_value) as LC_VALUE, ITEM_CATEGORY_ID, LC_NUMBER from com_btb_lc_master_details where status_active=1 and is_deleted=0 $date_con group by IMPORTER_ID,supplier_id,item_category_id,lc_number";
		//echo $sql_back_back;
		$nameArray_back_back=sql_select($sql_back_back);
		foreach($nameArray_back_back as $row)
		{
			$backToBackArr[$row[IMPORTER_ID]][$item_category[$row[ITEM_CATEGORY_ID]]][$supplier_library[$row[SUPPLIER_ID]]][$row[LC_NUMBER]]=$row;
		
			$catTotal[$row[IMPORTER_ID]][$item_category[$row[ITEM_CATEGORY_ID]]][]=$row[LC_VALUE];
			$supTotal[$row[IMPORTER_ID]][$item_category[$row[ITEM_CATEGORY_ID]]][$supplier_library[$row[SUPPLIER_ID]]][]=$row[LC_VALUE];
		
		}


	
//Fabric Booking Status........................
    if($is_insert_date==1){
			$date_con =" and A.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
        }
		else{
			$date_con=" and A.BOOKING_DATE between '$previous_date' and '$current_date'";	
		}
	$booking_sql = "select a.COMPANY_ID,a.FABRIC_SOURCE,a.BOOKING_TYPE,a.IS_SHORT,a.BOOKING_NO,b.JOB_NO,b.FIN_FAB_QNTY,b.GREY_FAB_QNTY from WO_BOOKING_MST a,WO_BOOKING_DTLS b where a.id=b.BOOKING_MST_ID and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $date_con";
	$booking_sql_res=sql_select($booking_sql);
	foreach($booking_sql_res as $row)
	{
		if($row[FABRIC_SOURCE]==2 && $row[BOOKING_TYPE]==1){
			$booking_data[$row[COMPANY_ID]][PURCH_JOB_NO][$row[JOB_NO]]=$row[JOB_NO];	
			$booking_data[$row[COMPANY_ID]][PURCH_BOOKING_NO][$row[BOOKING_NO]]=$row[BOOKING_NO];	
			$booking_data[$row[COMPANY_ID]][PURCH_FIN_FAB_QNTY]+=$row[FIN_FAB_QNTY];	
			$booking_data[$row[COMPANY_ID]][PURCH_GREY_FAB_QNTY]+=$row[GREY_FAB_QNTY];
		}
		else if($row[FABRIC_SOURCE]==1 && $row[BOOKING_TYPE]==4){
			$booking_data[$row[COMPANY_ID]][WITHORDER_JOB_NO][$row[JOB_NO]]=$row[JOB_NO];	
			$booking_data[$row[COMPANY_ID]][WITHORDER_BOOKING_NO][$row[BOOKING_NO]]=$row[BOOKING_NO];	
			$booking_data[$row[COMPANY_ID]][WITHORDER_FIN_FAB_QNTY]+=$row[FIN_FAB_QNTY];	
			$booking_data[$row[COMPANY_ID]][WITHORDER_GREY_FAB_QNTY]+=$row[GREY_FAB_QNTY];
		}
		else{
			$booking_data[$row[COMPANY_ID]][JOB_NO][$row[IS_SHORT]][$row[JOB_NO]]=$row[JOB_NO];	
			$booking_data[$row[COMPANY_ID]][BOOKING_NO][$row[IS_SHORT]][$row[BOOKING_NO]]=$row[BOOKING_NO];	
			$booking_data[$row[COMPANY_ID]][FIN_FAB_QNTY][$row[IS_SHORT]]+=$row[FIN_FAB_QNTY];	
			$booking_data[$row[COMPANY_ID]][GREY_FAB_QNTY][$row[IS_SHORT]]+=$row[GREY_FAB_QNTY];
		}
	}
	unset($booking_sql_res);

        if($is_insert_date==1){
			$date_con =" and A.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
        }
		else{
			$date_con=" and A.REQUISITION_DATE between '$previous_date' and '$current_date'";	
		}
$sample_without_order_sql="SELECT a.COMPANY_ID,d.BOOKING_NO, sum(c.FIN_FAB_QNTY) as FINISH_QTY,SUM(c.GREY_FAB_QNTY) AS GRAY_QTY from SAMPLE_DEVELOPMENT_MST a,SAMPLE_DEVELOPMENT_DTLS b,sample_development_fabric_acc c,SAMPLE_DEVELOPMENT_YARN_DTLS d where a.id=b.SAMPLE_MST_ID and b.SAMPLE_MST_ID=c.SAMPLE_MST_ID and b.SAMPLE_NAME = c.SAMPLE_NAME and c.id=d.REQ_FAB_DTLS_ID and a.id=d.MST_ID and c.FORM_TYPE=1 and  c.is_deleted=0  and c.status_active=1 and a.ENTRY_FORM_ID=203 $date_con GROUP BY a.COMPANY_ID,d.BOOKING_NO ";	
//echo $sample_without_order_sql;
	$sample_without_order_sql_res=sql_select($sample_without_order_sql);
	foreach($sample_without_order_sql_res as $row)
	{
		$sample_without_order_data[$row[COMPANY_ID]][BOOKING_NO][$row[BOOKING_NO]]=$row[BOOKING_NO];	
		$sample_without_order_data[$row[COMPANY_ID]][FINISH_QTY]+=$row[FINISH_QTY];	
		$sample_without_order_data[$row[COMPANY_ID]][GRAY_QTY]+=$row[GRAY_QTY];	
	}
	unset($sample_without_order_sql_res);
 
        
//Yarn Received..............................
    if($is_insert_date==1){
			$date_con =" and A.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
        }
		else{
			$date_con=" and a.transaction_date between '$previous_date' and '$current_date'";
		}
	$yarn_rec_sql="select c.COMPANY_ID,c.RECEIVE_PURPOSE,c.currency_id, d.supplier_name,
	b.product_name_details, sum(a.cons_quantity) as cons_quantity, sum(b.avg_rate_per_unit) as avg_rate_per_unit, sum(a.cons_amount/c.exchange_rate) as cons_amount , sum(a.cons_amount) as cons_amount_tk 
	from inv_transaction a, product_details_master b,inv_receive_master c ,lib_supplier d
	where d.id=a.supplier_id and c.id=a.mst_id and b.id=a.prod_id and c.entry_form=1 and a.item_category=1 and a.transaction_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 AND c.RECEIVE_PURPOSE in(16,2) $date_con 
	group by c.COMPANY_ID,c.RECEIVE_PURPOSE,c.currency_id,d.supplier_name,a.prod_id,b.product_name_details				
	order by d.supplier_name,b.product_name_details asc";				
	 //echo $yarn_rec_sql;
	$yarn_rec_sql_res=sql_select($yarn_rec_sql);
	foreach($yarn_rec_sql_res as $row)
	{
		$yarn_rec_data_arr[$row[COMPANY_ID]][$row[RECEIVE_PURPOSE]][]=$row;
	}
	unset($yarn_rec_sql_res);
		
//Yarn Allocation..............................
	
	$prod_data_arr = array();
	$prod_data = sql_select("select id, product_name_details, supplier_id, lot,AVG_RATE_PER_UNIT from product_details_master where item_category_id=1");
	foreach ($prod_data as $row) {
		$prod_data_arr[$row[csf('id')]]['prod_details'] = $row[csf('product_name_details')];
		$prod_data_arr[$row[csf('id')]]['supp'] = $row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$prod_data_arr[$row[csf('id')]]['AVG_RATE_PER_UNIT'] = $row[csf('AVG_RATE_PER_UNIT')];
	}
	unset($prod_data);
	
	
        if($is_insert_date==1){
			$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
        }
		else{
			$date_con=" and b.ALLOCATION_DATE between '$previous_date' and '$current_date'";
		}
	
	$yarn_allocation_sql="SELECT c.COMPANY_NAME,c.JOB_NO, b.booking_no,b.ITEM_ID,sum(b.qnty) as QTY FROM inv_material_allocation_dtls b, wo_po_details_master c, wo_po_break_down d where b.job_no=c.job_no and b.po_break_down_id = d.id and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 $date_con group by  c.company_name,c.job_no, b.booking_no,b.item_id";
	$yarn_allocation_sql_res=sql_select($yarn_allocation_sql);
	foreach($yarn_allocation_sql_res as $row)
	{
		$yarn_allocation_data_arr[$row[COMPANY_NAME]][]=$row;
	}
	unset($yarn_allocation_sql_res);


//--------------------------------------

	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con=" and a.transaction_date between '$previous_date' and '$current_date'";
	}
	
	
	$yarn_issue_sql="select a.COMPANY_ID,b.DYED_TYPE,c.issue_purpose, b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit,sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id  and a.item_category=1 and a.transaction_type=2 and c.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.DYED_TYPE in(1,2) $date_con  and a.COMPANY_ID in($company_id_str) group by a.company_id,b.DYED_TYPE,c.issue_purpose,a.prod_id,b.product_name_details";	
	 // echo $yarn_issue_sql;			
	$yarn_issue_sql_res=sql_select($yarn_issue_sql);
	foreach($yarn_issue_sql_res as $row)
	{
		$yarn_issue_data_arr[$row[COMPANY_ID]][$row[DYED_TYPE]][]=$row;
	}
	unset($yarn_issue_sql_res);

//Fabric Issue to Cutting.....................

	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.issue_date between '$previous_date' and '$current_date'";
	}
	$sql_fab_issue_to_cut_sql="select a.COMPANY_ID,a.BUYER_ID,a.KNIT_DYE_SOURCE,sum(b.issue_qnty) as ISSUE_QNTY from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id  and a.entry_form in(71,18) and a.knit_dye_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $date_con group by a.COMPANY_ID,a.buyer_id,a.knit_dye_source";
	// echo $sql_fab_issue_to_cut_sql;		
	$sql_fab_issue_to_cut_sql_res=sql_select($sql_fab_issue_to_cut_sql);
	$fbIssueDataArr=array();
	foreach($sql_fab_issue_to_cut_sql_res as $row)
	{
		$fbIssueDataArr[$row[COMPANY_ID]][$row[BUYER_ID]][$row[KNIT_DYE_SOURCE]]+=$row[ISSUE_QNTY];
	}
	unset($sql_fab_issue_to_cut_sql_res);


//Cutting Production.........................
	
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.production_date between '$previous_date' and '$current_date'";
	}
	$sql_cutting = "select a.COMPANY_ID,a.production_source,c.buyer_name, a.production_quantity, a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id in($company_id_str) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $date_con";
	//echo $sql_cutting;	
	$nameArray_cuttign=sql_select($sql_cutting);
	$rejectQtyArr=array();$cuttingDataArr=array();
	foreach($nameArray_cuttign as $row)
	{
		$cuttingDataArr[$row[COMPANY_ID]][$row[csf('buyer_name')]][$row[csf('production_source')]]+=$row[csf('production_quantity')];
		$rejectQtyArr[$row[COMPANY_ID]][$row[csf('buyer_name')]][$row[csf('production_source')]]+=$row[csf('reject_qnty')];
	}
	unset($nameArray_cuttign);
	
	
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.production_date between '$previous_date' and '$current_date'";
	}
	 
	$sql_sub="select  c.COMPANY_ID,a.production_qnty, a.reject_qnty,c.party_id, 2 as production_source from  subcon_gmts_prod_dtls a, subcon_ord_dtls b,subcon_ord_mst c  where a.order_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.production_type = 1 $date_con";
	$sqlResult =sql_select($sql_sub);					
	foreach($sqlResult as $row)
	{
		$cuttingDataArrr[$row[COMPANY_ID]][$row[csf('party_id')]][$row[csf('production_source')]]+=$row[csf('production_qnty')];
		$rejectQtyArrr[$row[COMPANY_ID]][$row[csf('party_id')]][$row[csf('production_source')]]+=$row[csf('reject_qnty')];
	
	}
	unset($sqlResult);
	
	
//print Production....................................
	if($is_insert_date==1){
		$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and b.PRODUCTION_DATE between '$previous_date' and '$current_date'";
	}
	$print_sql="select a.COMPANY_ID,c.BUYER_ID,c.WITHIN_GROUP,b.QCPASS_QTY,b.REJE_QTY from SUBCON_EMBEL_PRODUCTION_MST a,SUBCON_EMBEL_PRODUCTION_DTLS b,PRO_RECIPE_ENTRY_MST c where a.id=b.mst_id and a.JOB_NO=c.JOB_NO and a.ENTRY_FORM in(223,222) and c.ENTRY_FORM=220 and c.EMBL_NAME=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 $date_con";
	//echo $print_sql;	
	$print_sql_res =sql_select($print_sql);					
	foreach($print_sql_res as $row)
	{
		$productionDataArr[$row[COMPANY_ID]][QCPASS_QTY][$row[BUYER_ID].'**'.$row[WITHIN_GROUP]][$row[WITHIN_GROUP]]+=$row[QCPASS_QTY];
		$productionDataArr[$row[COMPANY_ID]][REJE_QTY][$row[BUYER_ID].'**'.$row[WITHIN_GROUP]]+=$row[REJE_QTY];
	
	}
	unset($print_sql_res);
	
	
//emblish Production...............................
	if($is_insert_date==1){
		$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and b.PRODUCTION_DATE between '$previous_date' and '$current_date'";
	}
	$emb_sql="select a.COMPANY_ID,c.PARTY_ID as BUYER_ID,c.WITHIN_GROUP,b.QCPASS_QTY,b.REJE_QTY from SUBCON_EMBEL_PRODUCTION_MST a,SUBCON_EMBEL_PRODUCTION_DTLS b,SUBCON_ORD_MST c where a.id=b.mst_id and a.JOB_NO=c.EMBELLISHMENT_JOB and a.ENTRY_FORM in(315,324) and c.ENTRY_FORM=311 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 $date_con";
	 //echo $emb_sql;	
	$emb_sql_res =sql_select($emb_sql);					
	foreach($emb_sql_res as $row)
	{
		$embDataArr[$row[COMPANY_ID]][QCPASS_QTY][$row[BUYER_ID].'**'.$row[WITHIN_GROUP]][$row[WITHIN_GROUP]]+=$row[QCPASS_QTY];
		$embDataArr[$row[COMPANY_ID]][REJE_QTY][$row[BUYER_ID].'**'.$row[WITHIN_GROUP]]+=$row[REJE_QTY];
	
	}
	unset($emb_sql_res);
	
 		
	if($is_insert_date==1){
		$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.production_date between '$previous_date' and '$current_date'";
	}
		
	$sewing_sql = "select a.SERVING_COMPANY,c.BUYER_NAME, a.PRODUCTION_QUANTITY, a.REJECT_QNTY, a.ALTER_QNTY, a.SPOT_QNTY, b.UNIT_PRICE,d.CM_COST from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c, wo_pre_cost_dtls d where c.job_no=d.job_no and a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.serving_company in($company_id_str) and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status in(1,2,3) $date_con"; 
	 //echo $sewing_sql;

		$sewing_sql_res = sql_select($sewing_sql);
		foreach($sewing_sql_res as $rows)
		{
			$pro_qnty[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=$rows[PRODUCTION_QUANTITY];
			$rej_qnty[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=$rows[REJECT_QNTY];
			$alter_qnty[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=$rows[ALTER_QNTY];
			$spot_qnty[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=$rows[SPOT_QNTY];
			$total_qnty[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=$rows[PRODUCTION_QUANTITY]+$rows[REJECT_QNTY]+$rows[ALTER_QNTY]+$rows[SPOT_QNTY];
			
			$fob_val[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=($rows[PRODUCTION_QUANTITY]+$rows[REJECT_QNTY]+$rows[ALTER_QNTY]+$rows[SPOT_QNTY])*$rows[UNIT_PRICE];
			
			$earningCM_arr[$rows[SERVING_COMPANY]][$rows[BUYER_NAME]]+=($rows[PRODUCTION_QUANTITY]+$rows[REJECT_QNTY]+$rows[ALTER_QNTY]+$rows[SPOT_QNTY])*$rows[CM_COST];
		}


	
//Garments Finishing..............................
            
    if($is_insert_date==1){
				$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
            }
			else{
                $date_con="and a.production_date between '$previous_date' and '$current_date'";
			}
            
            $sql_garments_pro="select a.COMPANY_ID,c.BUYER_NAME,a.PRODUCTION_TYPE, sum(a.production_quantity) as PRODUCTION_QUANTITY, sum(a.carton_qty) as CARTON_QTY from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id in($company_id_str) and a.production_type in(7,8,11,15) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $date_con group by a.COMPANY_ID,a.PRODUCTION_TYPE,c.buyer_name";	//and b.shiping_status=1
			//echo $sql_garments_pro;
			$sql_garments_pro_res=sql_select($sql_garments_pro);
			foreach($sql_garments_pro_res as $rows)
			{
				$pro_data_arr[PRO_QTY][$rows[COMPANY_ID]][$rows[BUYER_NAME]][$rows[PRODUCTION_TYPE]]=$rows[PRODUCTION_QUANTITY];
				$pro_data_arr[CRT_QTY][$rows[COMPANY_ID]][$rows[BUYER_NAME]][$rows[PRODUCTION_TYPE]]=$rows[CARTON_QTY];
				
			}
	
	
	
//Actual Production Resource Entry...................
			
    if($is_insert_date==1){
				$date_con =" and b.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
            }
			else{
                $date_con="and b.pr_date between '$previous_date' and '$current_date'";
			}
            
			$actual_production_resource_sql="select a.COMPANY_ID,b.MAN_POWER,b.OPERATOR,b.HELPER,b.TARGET_PER_HOUR,b.WORKING_HOUR from PROD_RESOURCE_MST a ,PROD_RESOURCE_DTLS b where a.id=b.mst_id and a.IS_DELETED=0 and b.IS_DELETED=0  and a.COMPANY_ID in($company_id_str) $date_con ";	
			$actual_production_resource_sql_res=sql_select($actual_production_resource_sql);
			foreach($actual_production_resource_sql_res as $rows)
			{
				$actual_manpawer_data_arr[$rows[COMPANY_ID]][MAN_POWER]+=$rows[MAN_POWER];
				$actual_manpawer_data_arr[$rows[COMPANY_ID]][OPERATOR]+=$rows[OPERATOR];
				$actual_manpawer_data_arr[$rows[COMPANY_ID]][HELPER]+=$rows[HELPER];
				$actual_manpawer_data_arr[$rows[COMPANY_ID]][DAY_TARGET]+=($rows[TARGET_PER_HOUR]*$rows[WORKING_HOUR]);
			}
	
	
	
	
//Final Inspection............................
	
		
		if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.inspection_date between '$previous_date' and '$current_date'";
		}


		$pro_buyer_inspection_sql="select C.COMPANY_NAME,C.JOB_NO,C.BUYER_NAME,B.PO_NUMBER, A.INSPECTION_STATUS,A.INSPECTION_QNTY,B.SHIPMENT_DATE from pro_buyer_inspection a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.id in(SELECT MAX(id) FROM pro_buyer_inspection where inspection_status in(1,2,3) $date_con GROUP BY po_break_down_id) and c.company_name in($company_id_str) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
		//echo $pro_buyer_inspection_sql;	
		
		$pro_buyer_inspection_sql_res=sql_select($pro_buyer_inspection_sql);
		foreach($pro_buyer_inspection_sql_res as $rows)
		{
			$pro_buyer_inspection_data_arr[$rows[COMPANY_NAME]][]=$rows;
 		}
	
	
//Ex-factory Done.............................
	if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.ex_factory_date between '$previous_date' and '$current_date'";
		}
			
		$ex_sql="select C.COMPANY_NAME,C.BUYER_NAME,A.EX_FACTORY_QNTY,B.UNIT_PRICE from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name  in($company_id_str) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $date_con";	
		//echo $ex_sql;
			
		$ex_sql_res = sql_select($ex_sql);
		foreach($ex_sql_res as $rows)
		{
			$ex_fac_data_arr[ex_fac_qty][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]+=$rows[EX_FACTORY_QNTY];
			$ex_fac_data_arr[ex_fac_val][$rows[COMPANY_NAME]][$rows[BUYER_NAME]]+=$rows[EX_FACTORY_QNTY]*$rows[UNIT_PRICE];
		}
		
	
//Full Shipment............................
 	if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.EX_FACTORY_DATE between '$previous_date' and '$current_date'";
		}
			
            
        $sql_full_ship="select C.COMPANY_NAME,B.ID,B.PLAN_CUT,B.PO_QUANTITY,B.PO_TOTAL_PRICE,B.JOB_NO_MST,B.PO_NUMBER,C.BUYER_NAME,sum(a.EX_FACTORY_QNTY) as EXFACTORY_QTY from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.shiping_status = 3 and b.shiping_status=3 and c.company_name in($company_id_str) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_con group by C.COMPANY_NAME,b.job_no_mst, b.id,b.plan_cut,b.po_quantity,b.po_total_price,b.po_number,c.buyer_name";
		   //echo $sql_full_ship;
        $sql_full_ship_res=sql_select($sql_full_ship);
		foreach($sql_full_ship_res as $rows)
		{
			$full_ship_data_arr[$rows[COMPANY_NAME]][]=$rows;
		}
			
			
//Leftover Garments After Shipment............................

	  		
		if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.EX_FACTORY_DATE between '$previous_date' and '$current_date'";
		}
		
		$sql_leftover_sql="select C.COMPANY_NAME,SUM(A.EX_FACTORY_QNTY) AS EX_FACTORY_QNTY,B.JOB_NO_MST,B.PO_NUMBER,C.BUYER_NAME,C.STYLE_REF_NO,SUM(D.PRODUCTION_QUANTITY) AS FINISH_QUANTITY from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c,pro_garments_production_mst d where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.po_break_down_id=d.po_break_down_id and a.shiping_status = 3 and d.production_type=5 and b.shiping_status=3 and c.company_name in($company_id_str) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=3 and c.is_deleted=0 and c.status_active=1 $date_con group by C.COMPANY_NAME,b.job_no_mst,b.po_number,c.buyer_name,style_ref_no";
		// echo $sql_leftover_sql;
		$sql_leftover_sql_res=sql_select($sql_leftover_sql);
		foreach($sql_leftover_sql_res as $rows)
		{
			$leftover_data_arr[$rows[COMPANY_NAME]][]=$rows;
		}
 	
 	
//Export Proceed Realized..................................
  	if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.received_date between '$previous_date' and '$current_date'";
		}


         $sql_realization_invoice="select A.BENIFICIARY_ID,A.BUYER_ID,C.IS_LC,C.LC_SC_ID,B.TYPE,SUM(B.DOCUMENT_CURRENCY) AS TOT_DOCUMENT_CURRENCY from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_invoice_ship_mst c where a.id=b.mst_id and a.invoice_bill_id=c.id and a.benificiary_id in($company_id_str) and a.is_invoice_bill=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $date_con group by a.benificiary_id,a.buyer_id,c.is_lc,c.lc_sc_id,b.type";
            
        $sql_realization_invoice_res=sql_select($sql_realization_invoice);
		foreach($sql_realization_invoice_res as $rows)
		{
			$realization_invoice_data_arr[$rows[BENIFICIARY_ID]][]=$rows;
		}
	
	
	
 		if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.received_date between '$previous_date' and '$current_date'";
		}


            $sql_realization_bill="select A.BENIFICIARY_ID,A.BUYER_ID,C.IS_LC,C.LC_SC_ID,B.TYPE,SUM(B.DOCUMENT_CURRENCY) AS TOT_DOCUMENT_CURRENCY from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id and a.benificiary_id  in($company_id_str) and a.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $date_con  group by a.benificiary_id,a.buyer_id,c.is_lc,c.lc_sc_id,b.type";
            
        $sql_realization_bill_res=sql_select($sql_realization_bill);
		foreach($sql_realization_bill_res as $rows)
		{
			$realization_bill_data_arr[$rows[BENIFICIARY_ID]][]=$rows;
		}
	
	
	
//Knitting Production [Sample & bulk]........................


	
 	if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.RECEIVE_DATE between '$previous_date' and '$current_date'";
		}
	//Note: production->Knit Composite->Knitting Production
	$sql_knit_pro_sample ="select a.BOOKING_ID,a.KNITTING_COMPANY,a.KNITTING_SOURCE,a.BOOKING_WITHOUT_ORDER, a.RECEIVE_DATE,a.BUYER_ID,b.GREY_RECEIVE_QNTY,b.REJECT_FABRIC_RECEIVE from INV_RECEIVE_MASTER a,PRO_GREY_PROD_ENTRY_DTLS b where a.id=b.mst_id  AND a.RECEIVE_BASIS = 2 and a.ENTRY_FORM=2 AND a.KNITTING_COMPANY in($company_id_str)  and a.STATUS_ACTIVE=1 and a.IS_AUDITED=0 $date_con"; 
	  //echo $sql_knit_pro_sample;
	$sql_knit_pro_sample_res=sql_select($sql_knit_pro_sample);
	$booking_id_arr=array();
	foreach($sql_knit_pro_sample_res as $rows)
	{
		$booking_id_arr[$rows[BOOKING_ID]]=$rows[BOOKING_ID];
	}
 	
	//Note: Plan->Knitting Plan->In-House Knitting Plan->Planning Info Entry For Sample Without Order
	$pp_planning_sql ="select b.ID,a.BOOKING_NO from PPL_PLANNING_INFO_ENTRY_MST a,PPL_PLANNING_INFO_ENTRY_DTLS b where a.id=b.mst_id ".where_con_using_array($booking_id_arr,0,'b.id').""; 
	// echo $pp_planning_sql;
	$pp_planning_sql_res=sql_select($pp_planning_sql);
	$booking_no_arr=array();
	foreach($pp_planning_sql_res as $rows)
	{
		$bpa=explode('-',$rows[BOOKING_NO]);
		$booking_no_arr[$rows[ID]]=$bpa[1];
	}
	///print_r($booking_no_arr);
	
	$knit_pro_sample_data_arr=array();
	$knit_pro_bulk_data_arr=array();
	foreach($sql_knit_pro_sample_res as $rows)
	{
		//echo $booking_no_arr[$rows[BOOKING_ID]].'='.$rows[BOOKING_ID].',';
		
		if($booking_no_arr[$rows[BOOKING_ID]]=='SMN'){
			$knit_pro_sample_data_arr[GREY_RECEIVE_QNTY][$rows[KNITTING_COMPANY]][$rows[BUYER_ID]][$rows[KNITTING_SOURCE]][$rows[BOOKING_WITHOUT_ORDER]]+=$rows[GREY_RECEIVE_QNTY];
			$knit_pro_sample_data_arr[REJECT_FABRIC_RECEIVE][$rows[KNITTING_COMPANY]][$rows[BUYER_ID]]+=$rows[REJECT_FABRIC_RECEIVE];
			
			$knit_pro_sample_data_arr[KNITTING_SOURCE][$rows[KNITTING_COMPANY]][$rows[BUYER_ID]]=$rows[KNITTING_SOURCE];
		}
		else{
			$knit_pro_bulk_data_arr[GREY_RECEIVE_QNTY][$rows[KNITTING_COMPANY]][$rows[BUYER_ID]][$rows[KNITTING_SOURCE]][$rows[BOOKING_WITHOUT_ORDER]]+=$rows[GREY_RECEIVE_QNTY];
			$knit_pro_bulk_data_arr[REJECT_FABRIC_RECEIVE][$rows[KNITTING_COMPANY]][$rows[BUYER_ID]]+=$rows[REJECT_FABRIC_RECEIVE];
			
			$knit_pro_bulk_data_arr[KNITTING_SOURCE][$rows[KNITTING_COMPANY]][$rows[BUYER_ID]]=$rows[KNITTING_SOURCE];
		}
		
	 }
	 
	
//Dyes and Chemical Received / Loan Received...............

	
 	if($is_insert_date==1){
			$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
		}
		else{
			$date_con="and a.RECEIVE_DATE between '$previous_date' and '$current_date'";
		}
		$sql_dyes_chem_rec="select a.COMPANY_ID,a.RECEIVE_PURPOSE,a.CURRENCY_ID,a.SUPPLIER_ID,b.ITEM_CATEGORY,c.ITEM_DESCRIPTION ,b.ORDER_UOM,sum(b.ORDER_QNTY) as ORDER_QNTY,sum(b.ORDER_AMOUNT) as CONS_AMOUNT from inv_receive_master a,INV_TRANSACTION b,PRODUCT_DETAILS_MASTER c  where a.id=b.mst_id and b.PROD_ID=c.id and a.ENTRY_FORM=4 and b.transaction_type=1 and a.COMPANY_ID in($company_id_str) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 $date_con group by a.COMPANY_ID,a.RECEIVE_PURPOSE,a.CURRENCY_ID,a.SUPPLIER_ID,b.ITEM_CATEGORY,c.ITEM_DESCRIPTION ,b.ORDER_UOM";
		//echo $sql_dyes_chem_rec;	
        $sql_dyes_chem_rec_res=sql_select($sql_dyes_chem_rec);
		foreach($sql_dyes_chem_rec_res as $rows)
		{
			if($rows[RECEIVE_PURPOSE]==5){
				$dyes_chem_loan_data_arr[$rows[COMPANY_ID]][]=$rows;
			}
			else{
				$dyes_chem_data_arr[$rows[COMPANY_ID]][]=$rows;
			}
		}
		unset($sql_dyes_chem_rec_res);
	
	
 		$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);
	
 	
	
//Dyes and Chemical Issued/ Loan Issued..............................

 	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.ISSUE_DATE between '$previous_date' and '$current_date'";
	}
	$sql_dyes_chem_issue = "select  a.COMPANY_ID,a.ISSUE_PURPOSE,b.ITEM_CATEGORY,b.SUPPLIER_ID,C.ITEM_DESCRIPTION,b.CONS_UOM,sum(b.CONS_QUANTITY) as CONS_QUANTITY,sum(b.CONS_AMOUNT) as CONS_AMOUNT from  INV_ISSUE_MASTER a,INV_TRANSACTION b,PRODUCT_DETAILS_MASTER c where a.id=b.mst_id and b.PROD_ID=c.id  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and a.ENTRY_FORM=5 and a.COMPANY_ID in($company_id_str) $date_con group by a.COMPANY_ID,a.ISSUE_PURPOSE,b.ITEM_CATEGORY,b.SUPPLIER_ID,b.CONS_UOM,C.ITEM_DESCRIPTION";
		 //echo $sql_dyes_chem_issue;	
        $sql_dyes_chem_issue_res=sql_select($sql_dyes_chem_issue);
		$dyes_chem_issue_data_arr=array();
		foreach($sql_dyes_chem_issue_res as $rows)
		{
			if($rows[ISSUE_PURPOSE]==5){
				$dyes_chem_loan_issue_data_arr[$rows[COMPANY_ID]][]=$rows;
			}
			else{
				$dyes_chem_issue_data_arr[$rows[COMPANY_ID]][]=$rows;
			}
		}
		unset($sql_dyes_chem_issue_res);
	
	
//Dyeing Machine ................................	
	
 	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.process_end_date between '$previous_date' and '$current_date'";
	}
	
	$dyeing_sql ="select a.COMPANY_ID,a.ENTRY_FORM,a.LOAD_UNLOAD_ID,c.BATCH_AGAINST,d.BUYER_ID, sum(b.BATCH_QTY) as BATCH_QTY from PRO_FAB_SUBPROCESS a,PRO_FAB_SUBPROCESS_DTLS b,PRO_BATCH_CREATE_MST c,WO_BOOKING_MST d where a.id=b.mst_id and c.id=a.BATCH_ID and c.BOOKING_NO_ID=d.id $date_con and a.ENTRY_FORM in(35,38) and b.ENTRY_PAGE in(35,38) and a.load_unload_id in(1,2) and a.company_id  in($company_id_str)  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 group by a.COMPANY_ID,a.ENTRY_FORM,a.LOAD_UNLOAD_ID,c.BATCH_AGAINST,d.BUYER_ID";
	  //echo $dyeing_sql;die;	
	$dyeing_sql_res=sql_select($dyeing_sql);
	$dyeing_data_arr=array();
	foreach($dyeing_sql_res as $rows)
	{
		if($rows[BATCH_AGAINST]==3){
			$dyeing_data_arr[sample][$rows[LOAD_UNLOAD_ID]][$rows[COMPANY_ID]][]=$rows;
		}
		else{
			$dyeing_data_arr[bulk][$rows[LOAD_UNLOAD_ID]][$rows[COMPANY_ID]][]=$rows;
		}
	}
	unset($dyeing_sql_res);

	//print_r($dyeing_data_arr[sample][3]);die;
	
//Finish Fabric Production .......................................	
 	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.receive_date between '$previous_date' and '$current_date'";
	}
 $ff_pro_sql = "select a.COMPANY_ID,a.KNITTING_SOURCE,c.BATCH_AGAINST,sum(b.receive_qnty) as QTY from inv_receive_master a, pro_finish_fabric_rcv_dtls b,PRO_BATCH_CREATE_MST c where a.id=b.mst_id and b.BATCH_ID=c.id  and a.knitting_source in(1,3) $date_con and a.COMPANY_ID in($company_id_str)  and a.entry_form=66 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.COMPANY_ID,a.KNITTING_SOURCE,c.BATCH_AGAINST";//
 //echo  $ff_pro_sql;	
	$ff_pro_sql_res=sql_select($ff_pro_sql);
	$ff_data_arr=array();
	foreach($ff_pro_sql_res as $rows)
	{
		if($rows[BATCH_AGAINST]==3){
			$ff_data_arr[sample][$rows[COMPANY_ID]][]=$rows;
		}
		else{
			$ff_data_arr[bulk][$rows[COMPANY_ID]][]=$rows;
		}
	}
	unset($ff_pro_sql_res);

	
//Finish Fabric Received [Normal] .......................................	
 	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id group by a.id, a.construction, b.copmposition_id, b.percent";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
 	
	//print_r($composition_arr);die;
	
	
	
	
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.RECEIVE_DATE between '$previous_date' and '$current_date'";
	}
  
  $ffr_sql ="select a.KNITTING_COMPANY,b.FABRIC_DESCRIPTION_ID,b.UOM,c.PO_BREAKDOWN_ID,c.QUANTITY from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id AND a.ENTRY_FORM = 68  AND c.ENTRY_FORM = 68  and a.KNITTING_COMPANY in($company_id_str)  $date_con";
    //echo $ffr_sql;
  	$ffr_sql_res=sql_select($ffr_sql);
	$order_id_arr=array();
	foreach($ffr_sql_res as $rows)
	{
		$order_id_arr[$rows[PO_BREAKDOWN_ID]]=$rows[PO_BREAKDOWN_ID];
	}
	
	$order_sql="select ID,JOB_NO_MST from WO_PO_BREAK_DOWN where IS_DELETED=0 ".where_con_using_array($order_id_arr,0,'id')."";
  	$order_sql_res=sql_select($order_sql);
	$job_by_po_arr=array();
	foreach($order_sql_res as $rows)
	{
		$job_by_po_arr[$rows[ID]]=$rows[JOB_NO_MST];
	}
	unset($order_sql_res);


	$ffr_data_arr=array();
	foreach($ffr_sql_res as $row)
	{
		$row[JOB_NO]=$job_by_po_arr[$row[PO_BREAKDOWN_ID]];
		$key = $row[JOB_NO].'**'.$row[FABRIC_DESCRIPTION_ID].'**'.$row[UOM];
		$ffr_data_arr[$row[KNITTING_COMPANY]][$key]+=$row[QUANTITY];
	}
	unset($ffr_sql_res);
	
	
//Finish Fabric Issued [Normal]..........................
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.ISSUE_DATE between '$previous_date' and '$current_date'";
	}
  
  //$ffi_sql ="select a.COMPANY_ID,b.FABRIC_DESCRIPTION_ID,b.UOM,c.PO_BREAKDOWN_ID,c.QUANTITY from inv_issue_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id AND a.ENTRY_FORM = 71  AND c.ENTRY_FORM = 71  and a.COMPANY_ID in($company_id_str)  $date_con";
  
  
  $ffi_sql ="select a.COMPANY_ID,d.DETARMINATION_ID as FABRIC_DESCRIPTION_ID,b.UOM,c.PO_BREAKDOWN_ID,c.QUANTITY from inv_issue_master a,INV_FINISH_FABRIC_ISSUE_DTLS b,order_wise_pro_details c,PRODUCT_DETAILS_MASTER d where a.id=b.mst_id and b.PROD_ID=d.id and b.id=c.dtls_id AND a.ENTRY_FORM = 71  AND c.ENTRY_FORM = 71  and a.COMPANY_ID in($company_id_str)  $date_con";  
        //echo $ffi_sql;
  	$ffi_sql_res=sql_select($ffi_sql);
	$order_id_arr=array();
	foreach($ffi_sql_res as $rows)
	{
		$order_id_arr[$rows[PO_BREAKDOWN_ID]]=$rows[PO_BREAKDOWN_ID];
	}
	
	$order_sql="select ID,JOB_NO_MST from WO_PO_BREAK_DOWN where IS_DELETED=0 ".where_con_using_array($order_id_arr,0,'id')."";
  	$order_sql_res=sql_select($order_sql);
	$job_by_po_arr=array();
	foreach($order_sql_res as $rows)
	{
		$job_by_po_arr[$rows[ID]]=$rows[JOB_NO_MST];
	}
	unset($order_sql_res);


	$ffi_data_arr=array();
	foreach($ffi_sql_res as $row)
	{
		$row[JOB_NO]=$job_by_po_arr[$row[PO_BREAKDOWN_ID]];
		$key = $row[JOB_NO].'**'.$row[FABRIC_DESCRIPTION_ID].'**'.$row[UOM];
		$ffi_data_arr[$row[COMPANY_ID]][$key]+=$row[QUANTITY];
	}
	unset($ffr_sql_res);
	
	//print_r($ffi_data_arr);die;
	
	
	
//Finish Fabric Received/Issue [Transfer]...............................
	
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.TRANSFER_DATE between '$previous_date' and '$current_date'";
	}
	$ffrit_sql = "select A.COMPANY_ID,A.TO_COMPANY,b.FEB_DESCRIPTION_ID,b.FROM_ORDER_ID,b.TO_ORDER_ID,b.UOM,b.TRANSFER_QNTY from INV_ITEM_TRANSFER_MST a,INV_ITEM_TRANSFER_DTLS b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 AND (A.COMPANY_ID IN ($company_id_str) or A.TO_COMPANY IN ($company_id_str)) $date_con";
	//echo $ffrit_sql;
  	$ffrit_sql_res=sql_select($ffrit_sql);
	$order_id_arr=array();
	foreach($ffrit_sql_res as $rows)
	{
		$order_id_arr[$rows[FROM_ORDER_ID]]=$rows[FROM_ORDER_ID];
		$order_id_arr[$rows[TO_ORDER_ID]]=$rows[TO_ORDER_ID];
	}
	$order_sql="select ID,JOB_NO_MST from WO_PO_BREAK_DOWN where IS_DELETED=0 ".where_con_using_array($order_id_arr,0,'id')."";
  	$order_sql_res=sql_select($order_sql);
	$job_by_po_arr=array();
	foreach($order_sql_res as $rows)
	{
		$job_by_po_arr[$rows[ID]]=$rows[JOB_NO_MST];
	}
	unset($order_sql_res);

	$ffrit_data_arr=array();
	foreach($ffrit_sql_res as $row)
	{
		$row[FROM_JOB_NO]=$job_by_po_arr[$row[FROM_ORDER_ID]];
		$key_form = $row[FROM_JOB_NO].'**'.$row[FEB_DESCRIPTION_ID].'**'.$row[UOM];
		$ffrit_data_arr[form][$row[COMPANY_ID]][$key_form]+=$row[TRANSFER_QNTY];
		
		$row[TO_JOB_NO]=$job_by_po_arr[$row[TO_ORDER_ID]];
		$key_to = $row[TO_JOB_NO].'**'.$row[FEB_DESCRIPTION_ID].'**'.$row[UOM];
		$ffrit_data_arr[to][$row[TO_COMPANY]][$key_to]+=$row[TRANSFER_QNTY];
	}
	unset($ffrit_sql_res);


//Accessories Received........................
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.RECEIVE_DATE between '$previous_date' and '$current_date'";
	}
	$acc_rec_sql="select a.COMPANY_ID,a.CURRENCY_ID,a.SUPPLIER_ID,b.ORDER_UOM,b.ITEM_GROUP_ID,b.ITEM_DESCRIPTION,sum(b.RECEIVE_QNTY) as QTY,sum(b.AMOUNT) as VAL from INV_RECEIVE_MASTER a, INV_TRIMS_ENTRY_DTLS b where a.id=b.mst_id and a.ITEM_CATEGORY=4 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and A.COMPANY_ID IN ($company_id_str) $date_con group by a.COMPANY_ID,a.SUPPLIER_ID,a.CURRENCY_ID,b.ORDER_UOM,b.ITEM_GROUP_ID,b.ITEM_DESCRIPTION";
	//echo $acc_rec_sql;
  	$acc_rec_sql_res=sql_select($acc_rec_sql);
	$acc_data_arr=array();
	foreach($acc_rec_sql_res as $rows)
	{
		$acc_data_arr[$rows[COMPANY_ID]][]=$rows;
	}

$item_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	
	
//Accessories Issued............................................
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.ISSUE_DATE between '$previous_date' and '$current_date'";
	}
	
	$acc_issue_sql="select a.COMPANY_ID, a.SUPPLIER_ID,b.UOM,b.ITEM_GROUP_ID,b.ITEM_DESCRIPTION, (b.ISSUE_QNTY) as QTY, (b.AMOUNT) as VAL from inv_issue_master a, inv_trims_issue_dtls b where a.id=b.mst_id and a.ITEM_CATEGORY=4 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and A.COMPANY_ID IN ($company_id_str) $date_con";	
	//echo $acc_issue_sql;
  	$acc_issue_sql_res=sql_select($acc_issue_sql);
	$acc_issue_data_arr=array();
	foreach($acc_issue_sql_res as $rows)
	{
		$acc_issue_data_arr[$rows[COMPANY_ID]][]=$rows;
	}

 	
//Greige Fabric Received [Normal]..........................
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.RECEIVE_DATE between '$previous_date' and '$current_date'";
	}
	
	$gray_fab_rec_sql ="SELECT a.COMPANY_ID,b.FEBRIC_DESCRIPTION_ID,B.UOM,c.PO_BREAKDOWN_ID,b.GREY_RECEIVE_QNTY as QUANTITY FROM INV_RECEIVE_MASTER A,PRO_GREY_PROD_ENTRY_DTLS B left join order_wise_pro_details c on b.id=c.dtls_id  AND c.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 WHERE a.id=b.mst_id  and a.ENTRY_FORM in(58) and a.ITEM_CATEGORY=13  and A.COMPANY_ID IN ($company_id_str) $date_con and A.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and B.IS_DELETED=0 ";	
	  // echo $gray_fab_rec_sql;
  	$gray_fab_rec_sql_res=sql_select($gray_fab_rec_sql);
	$tmp_po_id_arr=array();
	foreach($gray_fab_rec_sql_res as $rows)
	{
		if($rows[PO_BREAKDOWN_ID]){$tmp_po_id_arr[$rows[PO_BREAKDOWN_ID]]=$rows[PO_BREAKDOWN_ID];}
	}
	
	
	$order_sql="select ID,JOB_NO_MST from WO_PO_BREAK_DOWN where IS_DELETED=0 ".where_con_using_array($tmp_po_id_arr,0,'id')."";
  	$order_sql_res=sql_select($order_sql);
	$job_by_po_arr=array();
	foreach($order_sql_res as $rows)
	{
		$job_by_po_arr[$rows[ID]]=$rows[JOB_NO_MST];
	}
	unset($order_sql_res);
	unset($tmp_po_id_arr);
	
	
	$gray_fab_rec_data_arr=array();
	foreach($gray_fab_rec_sql_res as $rows)
	{
		$rows[JOB_NO]=$job_by_po_arr[$rows[PO_BREAKDOWN_ID]];
		$rows[UOM]=12;
		$key =$rows[JOB_NO].'**'.$rows[FEBRIC_DESCRIPTION_ID].'**'.$rows[UOM];
		$gray_fab_rec_data_arr[$rows[COMPANY_ID]][$key]+=$rows[QUANTITY];
	}
	unset($gray_fab_rec_sql_res);
	
//Greige Fabric Issue [Normal]..........................
	if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.ISSUE_DATE between '$previous_date' and '$current_date'";
	}
	
	$gray_fab_issue_sql ="SELECT a.COMPANY_ID, d.DETARMINATION_ID,c.PO_BREAKDOWN_ID, c.QUANTITY,d.ORDER_UOM FROM INV_ISSUE_MASTER A, INV_GREY_FABRIC_ISSUE_DTLS  B, order_wise_pro_details c, product_details_master d WHERE a.id = b.mst_id AND b.id = c.DTLS_ID AND c.PROD_ID = d.id AND a.ENTRY_FORM = 61 AND a.ITEM_CATEGORY = 13 AND A.COMPANY_ID IN ($company_id_str) $date_con and A.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and B.IS_DELETED=0  and c.STATUS_ACTIVE=1 and c.IS_DELETED=0  and d.STATUS_ACTIVE=1 and d.IS_DELETED=0";	
	 //echo $gray_fab_issue_sql;
  	$gray_fab_issue_sql_res=sql_select($gray_fab_issue_sql);
	$tmp_po_id_arr=array();
	foreach($gray_fab_issue_sql_res as $rows)
	{
		if($rows[PO_BREAKDOWN_ID]){$tmp_po_id_arr[$rows[PO_BREAKDOWN_ID]]=$rows[PO_BREAKDOWN_ID];}
	}
	
	
	$order_sql="select ID,JOB_NO_MST from WO_PO_BREAK_DOWN where IS_DELETED=0 ".where_con_using_array($tmp_po_id_arr,0,'id')."";
  	$order_sql_res=sql_select($order_sql);
	$job_by_po_arr=array();
	foreach($order_sql_res as $rows)
	{
		$job_by_po_arr[$rows[ID]]=$rows[JOB_NO_MST];
	}
	unset($order_sql_res);
	unset($tmp_po_id_arr);
	
	
	$gray_fab_issue_data_arr=array();
	foreach($gray_fab_issue_sql_res as $rows)
	{	$rows[ORDER_UOM]=12;
		$rows[JOB_NO]=$job_by_po_arr[$rows[PO_BREAKDOWN_ID]];
		
		$key =$rows[JOB_NO].'**'.$rows[DETARMINATION_ID].'**'.$rows[ORDER_UOM];
		$gray_fab_issue_data_arr[$rows[COMPANY_ID]][$key]+=$rows[QUANTITY];
	}
	unset($gray_fab_rec_sql_res);
	
	
	
//Greige Fabric Received/Issue [Transfer]................
if($is_insert_date==1){
		$date_con =" and a.insert_date between '".$previous_date."' and '".$previous_date." 11:59:59 PM'";
	}
	else{
		$date_con="and a.TRANSFER_DATE between '$previous_date' and '$current_date'";
	}
	$gray_rit_sql = "select A.COMPANY_ID,A.TO_COMPANY,b.FEB_DESCRIPTION_ID,b.FROM_ORDER_ID,b.TO_ORDER_ID,b.UOM,b.TRANSFER_QNTY from INV_ITEM_TRANSFER_MST a,INV_ITEM_TRANSFER_DTLS b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and b.ITEM_CATEGORY=13 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 AND (A.COMPANY_ID IN ($company_id_str) or A.TO_COMPANY IN ($company_id_str)) $date_con";
	 //echo $gray_rit_sql;
  	$gray_rit_sql_res=sql_select($gray_rit_sql);
	$order_id_arr=array();
	foreach($gray_rit_sql_res as $rows)
	{
		$order_id_arr[$rows[FROM_ORDER_ID]]=$rows[FROM_ORDER_ID];
		$order_id_arr[$rows[TO_ORDER_ID]]=$rows[TO_ORDER_ID];
	}
	$order_sql="select ID,JOB_NO_MST from WO_PO_BREAK_DOWN where IS_DELETED=0 ".where_con_using_array($order_id_arr,0,'id')."";
  	$order_sql_res=sql_select($order_sql);
	$job_by_po_arr=array();
	foreach($order_sql_res as $rows)
	{
		$job_by_po_arr[$rows[ID]]=$rows[JOB_NO_MST];
	}
	unset($order_sql_res);

	$gray_rit_data_arr=array();
	foreach($gray_rit_sql_res as $row)
	{	$row[UOM]=12;
		$row[FROM_JOB_NO]=$job_by_po_arr[$row[FROM_ORDER_ID]];
		$key_form = $row[FROM_JOB_NO].'**'.$row[FEB_DESCRIPTION_ID].'**'.$row[UOM];
		$gray_rit_data_arr[form][$row[COMPANY_ID]][$key_form]+=$row[TRANSFER_QNTY];
		
		$row[TO_JOB_NO]=$job_by_po_arr[$row[TO_ORDER_ID]];
		$key_to = $row[TO_JOB_NO].'**'.$row[FEB_DESCRIPTION_ID].'**'.$row[UOM];
		$gray_rit_data_arr[to][$row[TO_COMPANY]][$key_to]+=$row[TRANSFER_QNTY];
	}
	unset($gray_rit_sql_res);	
	
	
 


foreach($company_lib as $company_id=>$compname)
{
	ob_start();
?>

<h2>Total Activities of ( Date : <?=$previous_date;?>)</h2>
<p><?=$company_lib[$company_id];?></p>

 <table border="1" rules="all">
	<thead>
    <tr>
    	<th colspan="11">Order Received [Sample]</th>
    </tr>
	<tr bgcolor="#EEE">
    	<th rowspan="2">Buyer</th>
    	<th rowspan="2">Avg. Lead Time</th>
    	<th colspan="3">Sample Without Order</th>
    	<th colspan="3">Sample With Order</th>
    	<th colspan="3">Total</th>
    </tr>
	<tr>
    	<th>Qty.(Pcs)</th>
    	<th>Value</th>
    	<th>Avg. Rate</th>

    	<th>Qty.(Pcs)</th>
    	<th>Value</th>
    	<th>Avg. Rate</th>

    	<th>Qty.(Pcs)</th>
    	<th>Value</th>
    	<th>Avg. Rate</th>
    </tr>
    </thead>
    <tbody>
		<?
            $i=1;
            $total_data_arr=array();
            foreach($smp_order_data_arr[$company_id] as $buyer_id=>$rows){
        ?>
        <tr>
        	<td><?=$buyer_lib[$buyer_id];?></td>
        	<td align="right"><?=number_format($rows[AVG_LEAD_TIME],2);?></td>
        	<td align="right"><?=number_format($rows[WITHOUT_ORDER_QTY]);?></td>
        	<td align="right"><?=number_format($rows[WITHOUT_ORDER_VAL],2);?></td>
        	<td align="right"><?=number_format($rows[WITHOUT_ORDER_VAL]/$rows[WITHOUT_ORDER_QTY],2);?></td>
        	
            
        	<td align="right"><?=number_format($rows[QTY]);?></td>
        	<td align="right"><?=number_format($rows[VAL],2);?></td>
        	<td align="right"><?=number_format($rows[VAL]/$rows[QTY],2);?></td>
        	<td align="right"><?=number_format($TOT_QTY = $rows[WITHOUT_ORDER_QTY]+$rows[QTY],2);?></td>
        	<td align="right"><?=number_format($TOT_VAL = $rows[WITHOUT_ORDER_VAL]+$rows[VAL],2);?></td>
        	<td align="right"><?=number_format($TOT_VAL/$TOT_QTY,2);?></td>
        </tr>
			<?
            $total_data_arr[QTY]+=$rows[QTY];
            $total_data_arr[VAL]+=$rows[VAL];
            $total_data_arr[WITHOUT_ORDER_QTY]+=$rows[WITHOUT_ORDER_QTY];
            $total_data_arr[WITHOUT_ORDER_VAL]+=$rows[WITHOUT_ORDER_VAL];
            
            $total_data_arr[TOT_QTY]+=$TOT_QTY;
            $total_data_arr[TOT_VAL]+=$TOT_VAL;
			
			$i++;
            }
            ?>
    </tbody>
    <tfoot>
    	<tr bgcolor="#EEE">
        	<td colspan="2">Total</td>
        	<td align="right"><?=number_format($total_data_arr[WITHOUT_ORDER_QTY]);?></td>
        	<td align="right"><?=number_format($total_data_arr[WITHOUT_ORDER_VAL],2);?></td>
        	<td align="right"><?=number_format($total_data_arr[WITHOUT_ORDER_VAL]/$total_data_arr[WITHOUT_ORDER_QTY],2);?></td>
        	<td align="right"><?=number_format($total_data_arr[QTY]);?></td>
        	<td align="right"><?=number_format($total_data_arr[VAL],2);?></td>
        	<td align="right"><?=number_format($total_data_arr[VAL]/$total_data_arr[QTY],2);?></td>
            
            
            <td align="right"><?=number_format($total_data_arr[TOT_QTY]);?></td>
        	<td align="right"><?=number_format($total_data_arr[TOT_VAL],2);?></td>
        	<td align="right"><?=number_format($total_data_arr[TOT_VAL]/$total_data_arr[TOT_QTY],2);?></td>
        </tr>
    </tfoot>
</table>
<br />

 <table border="1" rules="all">
	<thead bgcolor="#EEE">
    <tr>
    	<th colspan="11">Order Received [Bulk]</th>
    </tr>
	<tr>
    	<th rowspan="2">Buyer</th>
    	<th rowspan="2">Avg. Lead Time</th>
    	<th colspan="3">Confirm Order</th>
    	<th colspan="3">Projected Order</th>
    	<th colspan="3">Total</th>
    </tr>
	<tr>
    	<th>Qty.(Pcs)</th>
    	<th>Value</th>
    	<th>Avg. Rate</th>

    	<th>Qty.(Pcs)</th>
    	<th>Value</th>
    	<th>Avg. Rate</th>

    	<th>Qty.(Pcs)</th>
    	<th>Value</th>
    	<th>Avg. Rate</th>
    </tr>
    </thead>
    <tbody>
    <?
		$i=1;
		$total_data_arr=array();
		foreach($order_data_arr[$company_id] as $buyer_id=>$rows){
	
	?>
    	<tr>
        	<td><?=$buyer_lib[$buyer_id];?></td>
        	<td align="right"><?=number_format($rows[AVG_LEAD_TIME],2);?></td>
        	<td align="right"><?=$rows[CON_PO_QTY];?></td>
        	<td align="right"><?=$rows[CON_PO_VAL];?></td>
        	<td align="right"><?=number_format($rows[CON_PO_VAL]/$rows[CON_PO_QTY],2);?></td>
        	<td align="right"><?=$rows[PROJ_PO_QTY];?></td>
        	<td align="right"><?=$rows[PROJ_PO_VAL];?></td>
        	<td align="right"><?=number_format($rows[PROJ_PO_VAL]/$rows[PROJ_PO_QTY],2);?></td>
        	<td align="right"><?=$TOTAL_PO_QTY = ($rows[CON_PO_QTY]+$rows[PROJ_PO_QTY]);?></td>
        	<td align="right"><?=$TOTAL_PO_VAL = ($rows[CON_PO_VAL]+$rows[PROJ_PO_VAL]);?></td>
        	<td align="right"><?=number_format($TOTAL_PO_VAL/$TOTAL_PO_QTY,2);?></td>
        </tr>
        <?
		$total_data_arr[CON_PO_QTY]+=$rows[CON_PO_QTY];
		$total_data_arr[CON_PO_VAL]+=$rows[CON_PO_VAL];
		$total_data_arr[PROJ_PO_QTY]+=$rows[PROJ_PO_QTY];
		$total_data_arr[PROJ_PO_VAL]+=$rows[PROJ_PO_VAL];
		$total_data_arr[TOTAL_PO_QTY]+=$TOTAL_PO_QTY;
		$total_data_arr[TOTAL_PO_VAL]+=$TOTAL_PO_VAL;
		
		$i++;
		}
		?>
    </tbody>
    <tfoot>
    	<tr bgcolor="#EEE">
        	<td colspan="2">Total</td>
        	<td align="right"><?=$total_data_arr[CON_PO_QTY];?></td>
        	<td align="right"><?=$total_data_arr[CON_PO_VAL];?></td>
        	<td align="right"><?=number_format($total_data_arr[CON_PO_VAL]/$total_data_arr[CON_PO_QTY],2);?></td>
        	<td align="right"><?=number_format($total_data_arr[PROJ_PO_QTY]);?></td>
        	<td align="right"><?=$total_data_arr[PROJ_PO_VAL];?></td>
        	<td align="right"><?=number_format($total_data_arr[PROJ_PO_VAL]/$total_data_arr[PROJ_PO_QTY],2);?></td>
        	<td align="right"><?=$total_data_arr[TOTAL_PO_QTY];?></td>
        	<td align="right"><?=$total_data_arr[TOTAL_PO_VAL];?></td>
        	<td align="right"><?=number_format($total_data_arr[TOTAL_PO_VAL]/$total_data_arr[TOTAL_PO_QTY],2);?></td>
        </tr>
    </tfoot>
</table>
<br />

 <table border="1" rules="all">
	<thead bgcolor="#EEE">
        <tr>
            <th colspan="5">Export LC/Sales Contract Receive</th>
        </tr>
        <tr>
            <th>SL</th>
            <th>Buyer</th>
            <th>LC/SC</th>
            <th>LC/SC No</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
    <?
		$i=1;
		$total_data_arr=array();
		foreach($lc_sc_data_arr[$company_id] as $buyer_id=>$rowsArr){
			foreach($rowsArr as $rows){
		
		?>
			<tr>
				<td><?=$i;?></td>
				<td><?=$buyer_lib[$buyer_id];?></td>
				<td><?=($rows[TYPE]==1)?"LC":"SC"; ?></td>
				<td align="right"><?=$rows[NO];?></td>
				<td align="right"><?=$rows[LC_SC_VALUE];?></td>
			</tr>
			<?
			$total_data_arr[LC_SC_VALUE]+=$rows[LC_SC_VALUE];
			$i++;
			}
		}
		?>
    </tbody>
    <tfoot>
    	<tr bgcolor="#EEE">
        	<td colspan="4">Total</td>
        	<td align="right"><?=$total_data_arr[LC_SC_VALUE];?></td>
        </tr>
    </tfoot>
</table>
<br />


 <table rules="all" border="1">
    <thead>
    <tr>
        <th colspan="7">Back to Back Open</th>
    </tr>
    <tr bgcolor="#EEE">
        <th width="30">SL</td>
        <th width="150">Item Category</th>
        <th width="180">Supplier</th>
        <th width="150">Value</th>
        <th width="180">LC Number </th>
        <th width="100">Catg Total</th>
        <th>Supplier Total</th>
    </tr>
    </thead>
    <tbody>
    <?
            
	$i=0;$tot_bb_value=0;
	ksort($backToBackArr[$company_id]);
	foreach($backToBackArr[$company_id] as $item_name=>$suppliyerData)
	{	ksort($suppliyerData);$itemFlag=0;
		foreach($suppliyerData as $upplyer_name=>$lc_data)
		{	$supFlag=0;
			foreach($lc_data as $row)
			{
                
                $i++;
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><? echo $item_name; ?></td>
            <td><? echo $upplyer_name; ?></td>
            <td align="right">
                <?
                    $value= $row[csf('lc_value')];
                    echo number_format($value,2); 
                    $tot_bb_value += $value;  
                ?>
            </td>
            <td><? echo $row[csf('lc_number')]; ?></td>
            <? 
            if($itemFlag==0){
                echo '
                <td align="right" rowspan="'.count($catTotal[$company_id][$item_name]).'">'.number_format(array_sum($catTotal[$company_id][$item_name]),2).'</td>
                <td align="right" rowspan="'.count($supTotal[$company_id][$item_name][$upplyer_name]).'">'.number_format(array_sum($supTotal[$company_id][$item_name][$upplyer_name]),2).'</td>
                </tr>
                ';
            }
            else if($supFlag==0){
                echo '
                <td align="right" rowspan="'.count($supTotal[$company_id][$item_name][$upplyer_name]).'">'.number_format(array_sum($supTotal[$company_id][$item_name][$upplyer_name]),2).'</td>
                </tr>
                ';
            }
            else
            {
                echo'</tr>';	
            }
            
        
			$itemFlag=1;
			$supFlag=1;
			$flag=1;
				}
			}
			
		}
        
        ?> 
    <tr>
    </tbody>
        <tfoot bgcolor="#EEE">
            <th>&nbsp;</th>
            <th>Total</th>
            <th>&nbsp;</th>
            <th align="right"><?  echo  number_format($tot_bb_value,2);  ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tfoot>
    </tr>
 </table>
<br />

 <table border="1" rules="all">
    <thead>
        <tr>
        	<th colspan="6">Fabric Booking Status</th>
        </tr>
        <tr>
        	<th>Booking Type</th>
            <th>Job No</th>
            <th>Booking No</th>
            <th>Finish Qty</th>
            <th>Process Loss %</th>
            <th>Greige Qty</th>
        </tr>
    </thead>
    <tr>
    	<td>Main Fabric</td>
        <td><?= implode(',',$booking_data[$company_id][JOB_NO][2]);?></td>
        <td><?= implode(',',$booking_data[$company_id][BOOKING_NO][2]);?></td>
        <td align="right"><?=number_format($booking_data[$company_id][FIN_FAB_QNTY][2],2);?></td>
        <td align="right">
        	<?
        	$processLoss=$booking_data[$company_id][GREY_FAB_QNTY][2]-$booking_data[$company_id][FIN_FAB_QNTY][2];
			echo number_format(($processLoss/$booking_data[$company_id][FIN_FAB_QNTY][2])*100,2);
			?>
        </td>
        <td align="right"><?=number_format($booking_data[$company_id][GREY_FAB_QNTY][2],2);?></td>
    </tr>
    <tr>
    	<td>Purchase</td>
        <td><?= implode(',',$booking_data[$company_id][PURCH_JOB_NO]);?></td>
        <td><?= implode(',',$booking_data[$company_id][PURCH_BOOKING_NO]);?></td>
        <td align="right"><?=number_format($booking_data[$company_id][PURCH_FIN_FAB_QNTY],2);?></td>
        <td align="right">
        	<?
        	$processLoss=$booking_data[$company_id][PURCH_GREY_FAB_QNTY]-$booking_data[$company_id][PURCH_FIN_FAB_QNTY];
			echo number_format(($processLoss/$booking_data[$company_id][PURCH_FIN_FAB_QNTY])*100,2);
			?>
        </td>
        <td align="right"><?=number_format($booking_data[$company_id][PURCH_GREY_FAB_QNTY],2);?></td>
    </tr>
    <tr>
    	<td>Short</td>
        <td><?= implode(',',$booking_data[$company_id][JOB_NO][1]);?></td>
        <td><?= implode(',',$booking_data[$company_id][BOOKING_NO][1]);?></td>
        <td align="right"><?=number_format($booking_data[$company_id][FIN_FAB_QNTY][1],2);?></td>
        <td align="right">
        	<?
        	$processLoss=$booking_data[$company_id][GREY_FAB_QNTY][1]-$booking_data[$company_id][FIN_FAB_QNTY][1];
			echo number_format(($processLoss/$booking_data[$company_id][FIN_FAB_QNTY][1])*100,2);
			?>
        </td>
        <td align="right"><?=number_format($booking_data[$company_id][GREY_FAB_QNTY][1],2);?></td>
    </tr>
    <tr>
    	<td>Sample With Order</td>
        <td><?= implode(',',$booking_data[$company_id][WITHORDER_JOB_NO]);?></td>
        <td><?= implode(',',$booking_data[$company_id][WITHORDER_BOOKING_NO]);?></td>
        <td align="right"><?=number_format($booking_data[$company_id][WITHORDER_FIN_FAB_QNTY],2);?></td>
        <td align="right">
        	<?
        	$processLoss=$booking_data[$company_id][WITHORDER_GREY_FAB_QNTY]-$booking_data[$company_id][WITHORDER_FIN_FAB_QNTY];
			echo number_format(($processLoss/$booking_data[$company_id][WITHORDER_FIN_FAB_QNTY])*100,2);
			?>
        </td>
        <td align="right"><?=number_format($booking_data[$company_id][WITHORDER_GREY_FAB_QNTY],2);?></td>
    </tr>
    <tr>
    	<td>Sample Without Order</td>
        <td></td>
        <td><?= implode(',',$sample_without_order_data[$company_id][BOOKING_NO]);?></td>
        <td align="right"><?=number_format($sample_without_order_data[$company_id][FINISH_QTY],2);?></td>
        <td align="right">
        	<?
        	$processLoss=$sample_without_order_data[$company_id][GRAY_QTY]-$sample_without_order_data[$company_id][FINISH_QTY];
			echo number_format(($processLoss/$sample_without_order_data[$company_id][FINISH_QTY])*100,2);
			?>
        </td>
        <td align="right"><?=number_format($sample_without_order_data[$company_id][GRAY_QTY],2);?></td>
    </tr>
</table>
<br />
 

 <table cellpadding="0" cellspacing="0" border="1" rules="all">
    <tr>
        <td colspan="9" height="30" align="center"><strong>Yarn Received [Grey Yarn]</strong></td>
    </tr>
    
    <tr bgcolor="#EEE">
        <td width="30"><strong>SL</strong></td>
        <td width="130" align="center"><strong>Supplier Name</strong></td>
        <td width="220" align="center"><strong>Yarn Description</strong></td>
        <td width="100" align="center"><strong>Qty. (Kg)</strong></td>
        <td width="60" align="center"><strong>Receive Currency</strong></td>
        <td width="80" align="center"><strong>Avg. Rate</strong></td>
        <td width="110" align="center"><strong>Amount</strong></td>
        <td width="80" align="center"><strong>Avg. Rate(Tk.)</strong></td>
        <td align="center"><strong>Amount(Tk.)</strong></td>
    </tr>
    <?
        $i=1; $tot_quantity=0; $tot_value=0;$tot_value_tk=0;
        foreach($yarn_rec_data_arr[$company_id][16] as $row)
        {
		?>
		<tr>
			<td align="center"><? echo $i; ?></td>
			<td><? echo $row[csf('supplier_name')]; ?></td>
			<td><? echo $row[csf('product_name_details')]; ?></td>
			
			<td align="right">
			<?
			   $tot_quantity += $row[csf('cons_quantity')]; 
				echo number_format($row[csf('cons_quantity')],2); 
			?>
			</td>
			<td align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>                        
			<td align="right">
				<?
					$rate= $row[csf('cons_amount')]/$row[csf('cons_quantity')];
					echo number_format($rate,2);
				?>
			</td>                        
			<td align="right">
				<? 
					$value= $row[csf('cons_amount')];
					echo number_format($value,2); 
					$tot_value += $value;  
				?>
			</td>
			<td align="right">
				<?
					$rate= $row[csf('cons_amount_tk')]/$row[csf('cons_quantity')];
					echo number_format($rate,2);
				?>
			</td>
			<td align="right">
				<? 
					$value_tk= $row[csf('cons_amount_tk')];
					echo number_format($value_tk,2); 
					$tot_value_tk += $value_tk;  
				?>
			
			</td>                        
		</tr>
		<?	
        $i++;
       }
    ?> 
    <tr>
        <tfoot bgcolor="#EEE">
            <th>&nbsp;</th>
            <th>Total</th>
            <th>&nbsp;</th>
            <th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
            <th align="right">&nbsp;</th>
            <th align="right">&nbsp;</th>
            <th align="right"><?  echo  number_format($tot_value,2);  ?></th>
            <th align="right">&nbsp;</th>
            <th align="right"><?  echo  number_format($tot_value_tk,2);  ?></th>
        </tfoot>
    </tr>
 </table>
<br />

 <table cellpadding="0" cellspacing="0" border="1" rules="all">
    <tr>
        <td colspan="9" height="30" align="center"><strong>Yarn Received [Dyed Yarn]</strong></td>
    </tr>
    
    <tr bgcolor="#EEE">
        <td width="30"><strong>SL</strong></td>
        <td width="130" align="center"><strong>Supplier Name</strong></td>
        <td width="220" align="center"><strong>Yarn Description</strong></td>
        <td width="100" align="center"><strong>Qty. (Kg)</strong></td>
        <td width="60" align="center"><strong>Receive Currency</strong></td>
        <td width="80" align="center"><strong>Avg. Rate</strong></td>
        <td width="110" align="center"><strong>Amount</strong></td>
        <td width="80" align="center"><strong>Avg. Rate(Tk.)</strong></td>
        <td align="center"><strong>Amount(Tk.)</strong></td>
    </tr>
    <?
        $i=1; $tot_quantity=0; $tot_value=0;$tot_value_tk=0;
        foreach($yarn_rec_data_arr[$company_id][2] as $row)
        {
		?>
		<tr>
			<td align="center"><? echo $i; ?></td>
			<td><? echo $row[csf('supplier_name')]; ?></td>
			<td><? echo $row[csf('product_name_details')]; ?></td>
			
			<td align="right">
			<?
			   $tot_quantity += $row[csf('cons_quantity')]; 
				echo number_format($row[csf('cons_quantity')],2); 
			?>
			</td>
			<td align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>                        
			<td align="right">
				<?
					$rate= $row[csf('cons_amount')]/$row[csf('cons_quantity')];
					echo number_format($rate,2);
				?>
			</td>                        
			<td align="right">
				<? 
					$value= $row[csf('cons_amount')];
					echo number_format($value,2); 
					$tot_value += $value;  
				?>
			</td>
			<td align="right">
				<?
					$rate= $row[csf('cons_amount_tk')]/$row[csf('cons_quantity')];
					echo number_format($rate,2);
				?>
			</td>
			<td align="right">
				<? 
					$value_tk= $row[csf('cons_amount_tk')];
					echo number_format($value_tk,2); 
					$tot_value_tk += $value_tk;  
				?>
			
			</td>                        
		</tr>
		<?	
        $i++;
       }
    ?> 
    <tr>
        <tfoot bgcolor="#EEE">
            <th>&nbsp;</th>
            <th>Total</th>
            <th>&nbsp;</th>
            <th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
            <th align="right">&nbsp;</th>
            <th align="right">&nbsp;</th>
            <th align="right"><?  echo  number_format($tot_value,2);  ?></th>
            <th align="right">&nbsp;</th>
            <th align="right"><?  echo  number_format($tot_value_tk,2);  ?></th>
        </tfoot>
    </tr>
 </table>
<br />


 <table border="1" rules="all">
	<thead>
    <tr>
        <th colspan="8">Yarn Allocation</th>
    </tr>
    <tr>
        <th>SL</th>
        <th>Job No</th>
        <th>Yarn Description</th>
        <th>Alloction Qty. (Kg)</th>
        <th>Avg. Rate</th>
        <th>Amount</th>
        <th>Avg. Rate(Tk.)</th>
        <th>Amount(Tk.)</th>
    </tr>
    </thead>
    <?
	$i=1;
	foreach($yarn_allocation_data_arr[$company_id] as $orw){
		?>
      <tr>
        <td><?=$i;?></td>
        <td><?=$orw[JOB_NO];?></td>
        <td><?=$prod_data_arr[$orw[ITEM_ID]]['prod_details'];?></td>
        <td align="right"><?=number_format($orw[QTY],2);?></td>
        <td align="center"><?=number_format($prod_data_arr[$orw[ITEM_ID]]['AVG_RATE_PER_UNIT'],2);?></td>
        <td align="right"><?=number_format($prod_data_arr[$orw[ITEM_ID]]['AVG_RATE_PER_UNIT']*$orw[QTY],2);?></td>
        <td align="center"><?=number_format($prod_data_arr[$orw[ITEM_ID]]['AVG_RATE_PER_UNIT']*$company_exchange_rate_arr[$company_id],2);?></td>
        <td align="right"><?=number_format(($prod_data_arr[$orw[ITEM_ID]]['AVG_RATE_PER_UNIT']*$orw[QTY])*$company_exchange_rate_arr[$company_id],2);?></td>
    </tr>

	<?
	$i++;
	}
	?>
    
    
</table>
<br />

 
 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="6" height="30" align="center"><strong>Yarn Issued [Grey Yarn]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <td width="30"><strong>SL</strong></td>
            <td align="center"><strong>Yarn Description</strong></td>
            <td width="200" align="center"><strong>Purpose</strong></td>
            <td width="125" align="center"><strong>Qty. (Kg)</strong></td>
            <td width="125" align="center"><strong>Value</strong></td>
            <td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
        </tr>
        <?
		$i=1;$tot_quantity=0;$tot_value=0;
		foreach($yarn_issue_data_arr[$company_id][2] as $row)
		{
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><? echo $row[csf('product_name_details')]; ?></td>
            <td><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
            
            <td align="right">
            <?
               $tot_quantity += $row[csf('cons_quantity')]; 
               echo number_format($row[csf('cons_quantity')],2); 
            ?>
            </td>                        
            <td align="right">
                <? 
                    $value= $row[csf('cons_amount')];
                    echo number_format($value,2); 
                    $tot_value += $value;  
                ?>
            </td>
            <td align="right">
                <?
                    $rate= $value/$row[csf('cons_quantity')];
                    echo number_format($rate,2);
                ?>
            </td>
        </tr>
        <?	
            $i++;
            }
		?>
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
                <th align="right"><?  echo  number_format($tot_value,2);  ?></th>
                <th align="right">&nbsp;</th>
            </tfoot>
        </tr>
     </table>
<br />

 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="6" height="30" align="center"><strong>Yarn Issued [Dyed Yarn]</strong></td>
        </tr>
        
        <tr bgcolor="#EEE">
            <td width="30"><strong>SL</strong></td>
            <td align="center"><strong>Yarn Description</strong></td>
            <td width="200" align="center"><strong>Purpose</strong></td>
            <td width="125" align="center"><strong>Qty. (Kg)</strong></td>
            <td width="125" align="center"><strong>Value</strong></td>
            <td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
        </tr>
        <?
		$i=1;$tot_quantity=0;$tot_value=0;
		foreach($yarn_issue_data_arr[$company_id][1] as $row)
		{
        ?>
        <tr>
            <td align="center"><?= $i; ?></td>
            <td><?= $row[csf('product_name_details')]; ?></td>
            <td><?= $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
            
            <td align="right">
            <?
               $tot_quantity += $row[csf('cons_quantity')]; 
               echo number_format($row[csf('cons_quantity')],2); 
            ?>
            </td>                        
            <td align="right">
                <? 
                    $value= $row[csf('cons_amount')];
                    echo number_format($value,2); 
                    $tot_value += $value;  
                ?>
            </td>
            <td align="right">
                <?
                    $rate= $value/$row[csf('cons_quantity')];
                    echo number_format($rate,2);
                ?>
            </td>
        </tr>
        <?	
            $i++;
            }
		?>
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th>&nbsp;</th>
                <th align="right"><?= number_format($tot_quantity ,2); ?></th>
                <th align="right"><?=  number_format($tot_value,2); ?></th>
                <th align="right">&nbsp;</th>
            </tfoot>
        </tr>
     </table>
<br />

    
  <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
        <tr>
            <td colspan="9" align="center"><strong>Knitting Production [Sample]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Buyer</th>
            <th>In-house Without Order</th>
            <th>Out Side Without Order</th>
            <th>In-house With Order</th>
            <th>Outside With Order</th>
            <th>Reject Qty.</th>
            <th>Total Production</th>
            <th>Reject %</th>
        </tr>
        </thead>
        <?
		$i=1;
		$grand_total=array();
		
	 
		
		
		foreach($knit_pro_sample_data_arr[GREY_RECEIVE_QNTY][$company_id] as $buyer_id=>$dataArr){
			 
			$row[InhouseWithoutOrder]=$dataArr[1][1];
			$row[InhouseWithOrder]=$dataArr[1][0];
			$row[OutSideWithoutOrder]=$dataArr[3][1];
			$row[OutSideWithOrder]=$dataArr[3][0];
			$row[RejectQty]=$knit_pro_sample_data_arr[REJECT_FABRIC_RECEIVE][$company_id][$buyer_id];
			$row[TotalProduction]=($row[InhouseWithoutOrder]+$row[OutSideWithoutOrder]+$row[InhouseWithOrder]+$row[OutSideWithOrder]);
			$row[RejectPercent]=($row[RejectQty]/$row[TotalProduction])*100;
			//Grand total.........................................................
 			$grand_total[InhouseWithoutOrder]+=$row[InhouseWithoutOrder];
			$grand_total[InhouseWithOrder]+=$row[InhouseWithOrder];
			$grand_total[OutSideWithoutOrder]+=$row[OutSideWithoutOrder];
			$grand_total[OutSideWithOrder]+=$row[OutSideWithOrder];
			$grand_total[RejectQty]+=$row[RejectQty];
			$grand_total[TotalProduction]+=$row[TotalProduction];
			
			
			$row[KNITTING_SOURCE]=$knit_pro_sample_data_arr[KNITTING_SOURCE][$company_id][$buyer_id];
			$buyerArr=($row[KNITTING_SOURCE]==0)?$company_lib:$buyer_lib;
		 
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$buyerArr[$buyer_id];?></td>
            <td align="right"><?=number_format($row[InhouseWithoutOrder],2);?></td>
            <td align="right"><?=number_format($row[OutSideWithoutOrder],2);?></td>
            <td align="right"><?=number_format($row[InhouseWithOrder],2);?></td>
            <td align="right"><?=number_format($row[OutSideWithOrder],2);?></td>
            <td align="right"><?=number_format($row[RejectQty],2);?></td>
            <td align="right"><?=number_format($row[TotalProduction],2);?></td>
            <td align="right"><?=number_format($row[RejectPercent],2);?></td>
        </tr>
        <?
		$i++;
		}
		
		$grand_total[RejectPercent]=($grand_total[RejectQty]/$grand_total[TotalProduction])*100;
		?>
        
        <tfoot bgcolor="#EEE">
            <th colspan="2">Total</th>
            <th align="right"><?=number_format($grand_total[InhouseWithoutOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[OutSideWithoutOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[InhouseWithOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[OutSideWithOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[RejectQty],2);?></th>
            <th align="right"><?=number_format($grand_total[TotalProduction],2);?></th>
            <th align="right"><?=number_format($grand_total[RejectPercent],2);?></th>
        </tfoot>
        
    </table>
    
    <br />
  <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
        <tr>
            <td colspan="9" align="center"><strong>Knitting Production [Bulk]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Buyer</th>
            <th>In-house Without Order</th>
            <th>Out Side Without Order</th>
            <th>In-house With Order</th>
            <th>Outside With Order</th>
            <th>Reject Qty.</th>
            <th>Total Production</th>
            <th>Reject %</th>
        </tr>
        </thead>
        <?
		$i=1;
		$grand_total=array();
		foreach($knit_pro_bulk_data_arr[GREY_RECEIVE_QNTY][$company_id] as $buyer_id=>$dataArr){
			$row[InhouseWithoutOrder]=$dataArr[1][1];
			$row[InhouseWithOrder]=$dataArr[1][0];
			$row[OutSideWithoutOrder]=$dataArr[3][1];
			$row[OutSideWithOrder]=$dataArr[3][0];
			$row[RejectQty]=$knit_pro_bulk_data_arr[REJECT_FABRIC_RECEIVE][$company_id][$buyer_id];
			$row[TotalProduction]=($row[InhouseWithoutOrder]+$row[OutSideWithoutOrder]+$row[InhouseWithOrder]+$row[OutSideWithOrder]);
			$row[RejectPercent]=($row[RejectQty]/$row[TotalProduction])*100;
			//Grand total.........................................................
 			$grand_total[InhouseWithoutOrder]+=$row[InhouseWithoutOrder];
			$grand_total[InhouseWithOrder]+=$row[InhouseWithOrder];
			$grand_total[OutSideWithoutOrder]+=$row[OutSideWithoutOrder];
			$grand_total[OutSideWithOrder]+=$row[OutSideWithOrder];
			$grand_total[RejectQty]+=$row[RejectQty];
			$grand_total[TotalProduction]+=$row[TotalProduction];
			
			
			$row[KNITTING_SOURCE]=$knit_pro_bulk_data_arr[KNITTING_SOURCE][$company_id][$buyer_id];
			$buyerArr=($row[KNITTING_SOURCE]==0)?$company_lib:$buyer_lib;
		 
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$buyerArr[$buyer_id];?></td>
            <td align="right"><?=number_format($row[InhouseWithoutOrder],2);?></td>
            <td align="right"><?=number_format($row[OutSideWithoutOrder],2);?></td>
            <td align="right"><?=number_format($row[InhouseWithOrder],2);?></td>
            <td align="right"><?=number_format($row[OutSideWithOrder],2);?></td>
            <td align="right"><?=number_format($row[RejectQty],2);?></td>
            <td align="right"><?=number_format($row[TotalProduction],2);?></td>
            <td align="right"><?=number_format($row[RejectPercent],2);?></td>
        </tr>
        <?
		$i++;
		}
		
		$grand_total[RejectPercent]=($grand_total[RejectQty]/$grand_total[TotalProduction])*100;
		?>
        
        <tfoot bgcolor="#EEE">
            <th colspan="2">Total</th>
            <th align="right"><?=number_format($grand_total[InhouseWithoutOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[OutSideWithoutOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[InhouseWithOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[OutSideWithOrder],2);?></th>
            <th align="right"><?=number_format($grand_total[RejectQty],2);?></th>
            <th align="right"><?=number_format($grand_total[TotalProduction],2);?></th>
            <th align="right"><?=number_format($grand_total[RejectPercent],2);?></th>
        </tfoot>
        
    </table>
    
<br />    
    
    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <th colspan="5" align="center">Greige Fabric Received [Normal]</th>
        </tr>
        <tr>
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type  Construction and Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <?
		$i=1;
		foreach($gray_fab_rec_data_arr[$company_id] as $key=>$qty){
			list($JOB_NO,$FEBRIC_DESCRIPTION_ID,$UOM)=explode('**',$key);	
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$FEBRIC_DESCRIPTION_ID];?></td>
            <td><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table>
    
   
      
  
 <br />
    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <th colspan="5" align="center">Greige Fabric Received [Transfer]</th>
        </tr>
        <tr>
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type  Construction and Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <?
		$i=1;
		foreach($gray_rit_data_arr[from][$company_id] as $key=>$qty){
			list($JOB_NO,$DESCRIPTION_ID,$UOM)=explode('**',$key);	
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$DESCRIPTION_ID];?></td>
            <td><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table>
   
    
<br />
    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <th colspan="5" align="center">Greige Fabric Issued [Normal]</th>
        </tr>
        <tr>
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type  Construction and Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <?
		$i=1;
		foreach($gray_fab_issue_data_arr[$company_id] as $key=>$qty){
			list($JOB_NO,$DESCRIPTION_ID,$UOM)=explode('**',$key);	
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$DESCRIPTION_ID];?></td>
            <td><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table>
    
<br />
    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <th colspan="5" align="center">Greige Fabric Issued [Transfer]</th>
        </tr>
        <tr>
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type  Construction and Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <?
		$i=1;
		foreach($gray_rit_data_arr[to][$company_id] as $key=>$qty){
			list($JOB_NO,$DESCRIPTION_ID,$UOM)=explode('**',$key);	
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$DESCRIPTION_ID];?></td>
            <td><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table>
    
    
    
    <br />

  <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="11" align="center"><strong>Dyes and Chemical Received</strong></td>
            </tr>
            <tr bgcolor="#EEE">
                <th>SL</th>
                <th>Supplier Name</th>
                <th>Item Category</th>
                <th>Item Description</th>
                <th>UOM</th>
                <th>Qty</th>
                <th>Received Currency</th>
                <th>Avg. Rate</th>
                <th>Amount</th>
                <th>Avg. Rate (Tk.)</th>
                <th>Amount (Tk.)</th>
            </tr>
        </thead>
		<?
		$i=1;
		foreach($dyes_chem_data_arr[$company_id] as $rows){
			
			
			$rows[CONS_AMOUNT_TK]=($rows[CURRENCY_ID]!=1)?$rows[CONS_AMOUNT]*$conversion_rate:$rows[CONS_AMOUNT];
			$rows[AVG_RATE_TK]=$rows[CONS_AMOUNT_TK]/$rows[ORDER_QNTY];
			



			$total_dat_arr[ORDER_QNTY]+=$rows[ORDER_QNTY];
			$total_dat_arr[CONS_AMOUNT]+=$rows[CONS_AMOUNT];
			$total_dat_arr[CONS_AMOUNT_TK]+=$rows[CONS_AMOUNT_TK];
			
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$supplier_library[$rows[SUPPLIER_ID]];?></td>
            <td><?=$garments_item[$rows[ITEM_CATEGORY]];?></td>
            <td><?=$rows[ITEM_DESCRIPTION];?></td>
            <td><?=$unit_of_measurement[$rows[ORDER_UOM]];?></td>
            <td><?=$rows[ORDER_QNTY];?></td>
            <td align="right"><?=$currency[$rows[CURRENCY_ID]];?></td>
            <td align="center"><?=number_format(($rows[CONS_AMOUNT]/$rows[ORDER_QNTY]),2);?></td>
            <td align="right"><?=$rows[CONS_AMOUNT];?></td>
            <td align="center"><?=number_format($rows[AVG_RATE_TK],2);?></td>
            <td align="right"><?=$rows[CONS_AMOUNT_TK];?></td>
         </tr>
         <?
		 $i++;
		 }
		 ?>
         
        <tfoot bgcolor="#EEE">
            <td colspan="5" align="right">Total</td>
            <td><?=$total_dat_arr[ORDER_QNTY];?></td>
            <td></td>
            <td align="center"><?=number_format(($total_dat_arr[CONS_AMOUNT]/$total_dat_arr[ORDER_QNTY]),2);?></td>
            <td align="right"><?=$total_dat_arr[CONS_AMOUNT];?></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT_TK]/$total_dat_arr[ORDER_QNTY],2);?></td>
            <td align="right"><?=$total_dat_arr[CONS_AMOUNT_TK];?></td>
         </tfoot>
         
     </table>

    <br />

  <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="11" align="center"><strong>Dyes and Chemical Issued</strong></td>
            </tr>
            <tr bgcolor="#EEE">
                <th>SL</th>
                <th>Supplier Name</th>
                <th>Item Category</th>
                <th>Item Description</th>
                <th>UOM</th>
                <th>Qty</th>
                <th>Avg. Rate</th>
                <th>Amount</th>
                <th>Avg. Rate (Tk.)</th>
                <th>Amount (Tk.)</th>
            </tr>
        </thead>
		<?
		$i=1;
		foreach($dyes_chem_issue_data_arr[$company_id] as $rows){
			
			
			$rows[CONS_AMOUNT_USD]=$rows[CONS_AMOUNT]/$conversion_rate;
			$rows[AVG_RATE_USD]=($rows[CONS_AMOUNT_USD]/$rows[CONS_QUANTITY]);
			
			$total_dat_arr[CONS_QUANTITY]+=$rows[CONS_QUANTITY];
			$total_dat_arr[CONS_AMOUNT]+=$rows[CONS_AMOUNT];
			$total_dat_arr[CONS_AMOUNT_USD]+=$rows[CONS_AMOUNT_USD];
			
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$supplier_library[$rows[SUPPLIER_ID]];?></td>
            <td><?=$garments_item[$rows[ITEM_CATEGORY]];?></td>
            <td><?=$rows[ITEM_DESCRIPTION];?></td>
            <td><?=$unit_of_measurement[$rows[ORDER_UOM]];?></td>
            <td align="right"><?=$rows[CONS_QUANTITY];?></td>
            <td align="center"><?=number_format($rows[AVG_RATE_USD],2);?></td>
            <td align="right"><?=number_format($rows[CONS_AMOUNT_USD],2);?></td>
            <td align="center"><?=number_format($rows[CONS_AMOUNT]/$rows[CONS_QUANTITY],2);?></td>
            <td align="right"><?=number_format($rows[CONS_AMOUNT],2);?></td>
         </tr>
         <?
		 $i++;
		 }
		 ?>
         
        <tfoot bgcolor="#EEE">
            <td colspan="5" align="right">Total</td>
            <td><?=$total_dat_arr[CONS_QUANTITY];?></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT_USD]/$total_dat_arr[CONS_QUANTITY],2);?></td>
            <td align="right"><?=number_format($total_dat_arr[CONS_AMOUNT_USD]);?></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT]/$total_dat_arr[CONS_QUANTITY],2);?></td>
            <td align="right"><?=number_format($total_dat_arr[CONS_AMOUNT],2);?></td>
         </tfoot>
         
     </table>
    <br />  
     
  <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="11" align="center"><strong>Dyes and Chemical Loan Received</strong></td>
            </tr>
            <tr bgcolor="#EEE">
                <th>SL</th>
                <th>Supplier Name</th>
                <th>Item Category</th>
                <th>Item Description</th>
                <th>UOM</th>
                <th>Qty</th>
                <th>Received Currency</th>
                <th>Avg. Rate</th>
                <th>Amount</th>
                <th>Avg. Rate (Tk.)</th>
                <th>Amount (Tk.)</th>
            </tr>
        </thead>
		<?
		$i=1;
		$total_dat_arr=array();
		foreach($dyes_chem_loan_data_arr[$company_id] as $rows){
			
			$rows[CONS_AMOUNT_TK]=($rows[CURRENCY_ID]!=1)?$rows[CONS_AMOUNT]*$conversion_rate:$rows[CONS_AMOUNT];
			$rows[AVG_RATE_TK]=$rows[CONS_AMOUNT_TK]/$rows[ORDER_QNTY];
			
			$total_dat_arr[ORDER_QNTY]+=$rows[ORDER_QNTY];
			$total_dat_arr[CONS_AMOUNT]+=$rows[CONS_AMOUNT];
			$total_dat_arr[CONS_AMOUNT_TK]+=$rows[CONS_AMOUNT_TK];
			
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$supplier_library[$rows[SUPPLIER_ID]];?></td>
            <td><?=$garments_item[$rows[ITEM_CATEGORY]];?></td>
            <td><?=$rows[ITEM_DESCRIPTION];?></td>
            <td><?=$unit_of_measurement[$rows[ORDER_UOM]];?></td>
            <td><?=$rows[ORDER_QNTY];?></td>
            <td align="right"><?=$currency[$rows[CURRENCY_ID]];?></td>
            <td align="center"><?=number_format(($rows[CONS_AMOUNT]/$rows[ORDER_QNTY]),2);?></td>
            <td align="right"><?=$rows[CONS_AMOUNT];?></td>
            <td align="center"><?=number_format($rows[AVG_RATE_TK],2);?></td>
            <td align="right"><?=$rows[CONS_AMOUNT_TK];?></td>
         </tr>
         <?
		 $i++;
		 }
		 ?>
         
        <tfoot bgcolor="#EEE">
            <td colspan="5" align="right">Total</td>
            <td><?=$total_dat_arr[ORDER_QNTY];?></td>
            <td></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT]/$total_dat_arr[ORDER_QNTY],2);?></td>
            <td align="right"><?=$total_dat_arr[CONS_AMOUNT];?></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT_TK]/$total_dat_arr[ORDER_QNTY],2);?></td>
            <td align="right"><?=$total_dat_arr[CONS_AMOUNT_TK];?></td>
         </tfoot>
         
     </table>
    <br />  
     

  <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="11" align="center"><strong>Dyes and Chemical Loan Issued</strong></td>
            </tr>
            <tr bgcolor="#EEE">
                <th>SL</th>
                <th>Supplier Name</th>
                <th>Item Category</th>
                <th>Item Description</th>
                <th>UOM</th>
                <th>Qty</th>
                <th>Avg. Rate</th>
                <th>Amount</th>
                <th>Avg. Rate (Tk.)</th>
                <th>Amount (Tk.)</th>
            </tr>
        </thead>
		<?
		$i=1;
		foreach($dyes_chem_loan_issue_data_arr[$company_id] as $rows){
			
			
			$rows[CONS_AMOUNT_USD]=$rows[CONS_AMOUNT]/$conversion_rate;
			$rows[AVG_RATE_USD]=($rows[CONS_AMOUNT_USD]/$rows[CONS_QUANTITY]);
			
			$total_dat_arr[CONS_QUANTITY]+=$rows[CONS_QUANTITY];
			$total_dat_arr[CONS_AMOUNT]+=$rows[CONS_AMOUNT];
			$total_dat_arr[CONS_AMOUNT_USD]+=$rows[CONS_AMOUNT_USD];
			
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$supplier_library[$rows[SUPPLIER_ID]];?></td>
            <td><?=$garments_item[$rows[ITEM_CATEGORY]];?></td>
            <td><?=$rows[ITEM_DESCRIPTION];?></td>
            <td><?=$unit_of_measurement[$rows[CONS_UOM]];?></td>
            <td align="right"><?=$rows[CONS_QUANTITY];?></td>
            <td align="center"><?=number_format($rows[AVG_RATE_USD],2);?></td>
            <td align="right"><?=number_format($rows[CONS_AMOUNT_USD],2);?></td>
            <td align="center"><?=number_format($rows[CONS_AMOUNT]/$rows[CONS_QUANTITY],2);?></td>
            <td align="right"><?=number_format($rows[CONS_AMOUNT],2);?></td>
         </tr>
         <?
		 $i++;
		 }
		 ?>
         
        <tfoot bgcolor="#EEE">
            <td colspan="5" align="right">Total</td>
            <td align="right"><?=$total_dat_arr[CONS_QUANTITY];?></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT_USD]/$total_dat_arr[CONS_QUANTITY],2);?></td>
            <td align="right"><?=number_format($total_dat_arr[CONS_AMOUNT_USD]);?></td>
            <td align="center"><?=number_format($total_dat_arr[CONS_AMOUNT]/$total_dat_arr[CONS_QUANTITY],2);?></td>
            <td align="right"><?=number_format($total_dat_arr[CONS_AMOUNT],2);?></td>
         </tfoot>
         
     </table>

	<br />

    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="4" align="center"><strong>Dyeing Machine Loaded [Sample]</strong></td>
            </tr>
            <tr>
                <th>SL</th>
                <th>Buyer</th>
                <th>In-house</th>
                <th>In-house Self Order</th>
            </tr>
        </thead>
        <?
		$i=1;
		$total_data=array();
		foreach($dyeing_data_arr[sample][1][$company_id] as $row)
		{
			if($row[ENTRY_FORM]==35){$row[IN_HOUSE_QTY]=$row[BATCH_QTY];}
			else if($row[ENTRY_FORM]==38){$row[SUB_CON_QTY]=$row[BATCH_QTY];}
			
			$total_data[IN_HOUSE_QTY]+=$row[IN_HOUSE_QTY];
			$total_data[SUB_CON_QTY]+=$row[SUB_CON_QTY];
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$buyer_lib[$row[BUYER_ID]];?></td>
            <td align="right"><?=$row[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$row[SUB_CON_QTY];?></td>
        </tr>
		<?
		$i++;
		}
		?>
        <tr>
            <td colspan="2" align="right">Total</td>
            <td align="right"><?=$total_data[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$total_data[SUB_CON_QTY];?></td>
        </tr>
         
    </table>



	<br />

    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="4" align="center"><strong>Dyeing Machine Un-Loaded [Sample]</strong></td>
            </tr>
            <tr>
                <th>SL</th>
                <th>Buyer</th>
                <th>In-house</th>
                <th>In-house Self Order</th>
            </tr>
        </thead>
        <?
		$i=1;
		$total_data=array();
		foreach($dyeing_data_arr[sample][2][$company_id] as $row)
		{
			if($row[ENTRY_FORM]==35){$row[IN_HOUSE_QTY]=$row[BATCH_QTY];}
			else if($row[ENTRY_FORM]==38){$row[SUB_CON_QTY]=$row[BATCH_QTY];}
			
			$total_data[IN_HOUSE_QTY]+=$row[IN_HOUSE_QTY];
			$total_data[SUB_CON_QTY]+=$row[SUB_CON_QTY];
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$buyer_lib[$row[BUYER_ID]];?></td>
            <td align="right"><?=$row[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$row[SUB_CON_QTY];?></td>
        </tr>
		<?
		$i++;
		}
		?>
        <tr>
            <td colspan="2" align="right">Total</td>
            <td align="right"><?=$total_data[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$total_data[SUB_CON_QTY];?></td>
        </tr>
         
    </table>

	<br />

    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="4" align="center"><strong>Dyeing Machine Loaded [Bulk]</strong></td>
            </tr>
            <tr>
                <th>SL</th>
                <th>Buyer</th>
                <th>In-house</th>
                <th>In-house Self Order</th>
            </tr>
        </thead>
        <?
		$i=1;
		$total_data=array();
		foreach($dyeing_data_arr[bulk][1][$company_id] as $row)
		{
			if($row[ENTRY_FORM]==35){$row[IN_HOUSE_QTY]=$row[BATCH_QTY];}
			else if($row[ENTRY_FORM]==38){$row[SUB_CON_QTY]=$row[BATCH_QTY];}
			
			$total_data[IN_HOUSE_QTY]+=$row[IN_HOUSE_QTY];
			$total_data[SUB_CON_QTY]+=$row[SUB_CON_QTY];
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$buyer_lib[$row[BUYER_ID]];?></td>
            <td align="right"><?=$row[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$row[SUB_CON_QTY];?></td>
        </tr>
		<?
		$i++;
		}
		?>
        <tr>
            <td colspan="2" align="right">Total</td>
            <td align="right"><?=$total_data[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$total_data[SUB_CON_QTY];?></td>
        </tr>
         
    </table>

	<br />

    <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <td colspan="4" align="center"><strong>Dyeing Machine Un-Loaded [bulk]</strong></td>
            </tr>
            <tr>
                <th>SL</th>
                <th>Buyer</th>
                <th>In-house</th>
                <th>In-house Self Order</th>
            </tr>
        </thead>
        <?
		$i=1;
		$total_data=array();
		foreach($dyeing_data_arr[bulk][2][$company_id] as $row)
		{
			if($row[ENTRY_FORM]==35){$row[IN_HOUSE_QTY]=$row[BATCH_QTY];}
			else if($row[ENTRY_FORM]==38){$row[SUB_CON_QTY]=$row[BATCH_QTY];}
			
			$total_data[IN_HOUSE_QTY]+=$row[IN_HOUSE_QTY];
			$total_data[SUB_CON_QTY]+=$row[SUB_CON_QTY];
		?>
        <tr>
            <td><?=$i;?></td>
            <td><?=$buyer_lib[$row[BUYER_ID]];?></td>
            <td align="right"><?=$row[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$row[SUB_CON_QTY];?></td>
        </tr>
		<?
		$i++;
		}
		?>
        <tr>
            <td colspan="2" align="right">Total</td>
            <td align="right"><?=$total_data[IN_HOUSE_QTY];?></td>
            <td align="right"><?=$total_data[SUB_CON_QTY];?></td>
        </tr>
         
    </table>
    
	<br />    
   
     <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="3" align="center"><strong>Finish Fabric Production [Sample]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Source</th>
            <th>Total Prod.</th>
         </tr>
         <?
		 $i=1;
		 $total_data_arr=array();
		 foreach($ff_data_arr[sample][$company_id] as $row)
		 {
			 $total_data_arr[QTY]+=$row[QTY];
		 ?>	 
        <tr>
            <td><?=$i;?></td>
            <td><?=$knitting_source[$row[KNITTING_SOURCE]];?></td>
            <td align="right"><?=$row[QTY];?></td>
         </tr>
         <?
		 $i++;
		 }
		 ?>
        <tr bgcolor="#EEE">
            <td colspan="2" align="right">Total</td>
            <td align="right"><?=$total_data_arr[QTY];?></td>
         </tr>
    </table>
    <br />
    
     <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="3" align="center"><strong>Finish Fabric Production [Bulk]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Source</th>
            <th>Total Prod.</th>
         </tr>
         <?
		 $i=1;
		 $total_data_arr=array();
		 foreach($ff_data_arr[bulk][$company_id] as $row)
		 {
			 $total_data_arr[QTY]+=$row[QTY];
		 ?>	 
        <tr>
            <td><?=$i;?></td>
            <td><?=$knitting_source[$row[KNITTING_SOURCE]];?></td>
            <td align="right"><?=$row[QTY];?></td>
         </tr>
         <?
		 $i++;
		 }
		 ?>
        <tr bgcolor="#EEE">
            <td colspan="2" align="right">Total</td>
            <td align="right"><?=$total_data_arr[QTY];?></td>
         </tr>
    </table>
    <br /> 
      <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="5" align="center"><strong>Finish Fabric Received [Normal]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type Construction & Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <? 
		$i=1;
		foreach($ffr_data_arr[$company_id] as $key=>$qty)
		{
		list($JOB_NO,$FABRIC_DESCRIPTION_ID,$UOM)=explode('**',$key);
		$UOM=12;
		?>
        <tr>  
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$FABRIC_DESCRIPTION_ID];?></td>
            <td align="center"><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
        
    </table>  
    <br />  
    
	<table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="5" align="center"><strong>Finish Fabric Received [Transfer]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type Construction & Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <? 
		$i=1;
		foreach($ffrit_data_arr[form][$company_id] as $key=>$qty)
		{
		list($JOB_NO,$FABRIC_DESCRIPTION_ID,$UOM)=explode('**',$key);
		$UOM=12;
		?>
        <tr>  
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$FABRIC_DESCRIPTION_ID];?></td>
            <td align="center"><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
        
    </table>        
    <br /> 
     
      <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="5" align="center"><strong>Finish Fabric Issued [Normal]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type Construction & Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <?
		$i=1;
		foreach($ffi_data_arr[$company_id] as $key=>$qty)
		{
		list($JOB_NO,$FABRIC_DESCRIPTION_ID,$UOM)=explode('**',$key);
		$UOM=12;
		
		?>
        <tr>  
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$FABRIC_DESCRIPTION_ID];?></td>
            <td align="center"><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table> 
    <br />     
    
       <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="5" align="center"><strong>Finish Fabric Issued [Transfer]</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <th>SL</th>
            <th>Job No</th>
            <th>Fabric Type Construction & Composition</th>
            <th>UOM</th>
            <th>Qty</th>
        </tr>
        <? 
		$i=1;
		foreach($ffrit_data_arr[to][$company_id] as $key=>$qty)
		{
		list($JOB_NO,$FABRIC_DESCRIPTION_ID,$UOM)=explode('**',$key);
		$UOM=12;
		
		?>
        <tr>  
            <td><?=$i;?></td>
            <td><?=$JOB_NO;?></td>
            <td><?=$composition_arr[$FABRIC_DESCRIPTION_ID];?></td>
            <td align="center"><?=$unit_of_measurement[$UOM];?></td>
            <td align="right"><?=$qty;?></td>
        </tr>
        <?
		$i++;
		}
		?>
    </table>  
     
   
     
      <br /> 
   <table cellpadding="0" cellspacing="0" rules="all" border="1">
    <tr>
        <td colspan="11" align="center"><strong>Accessories Received</strong></td>
    </tr>
    <tr bgcolor="#EEE">
         <th>SL</th>
         <th>Supplier Name</th>
         <th>Item Group</th>
         <th>Item Description</th>
         <th>Qty</th>
         <th>UOM</th>
         <td>Received Currency</td>
         <th>Avg. Rate</th>
         <th>Amount</th>
         <th>Avg. Rate (Tk.)</th>
         <th>Amount (Tk.)</th>
    </tr>
    <?
	$i=1;
	foreach($acc_data_arr[$company_id] as $row){
		$row[VAL_TK]=($row[CURRENCY_ID]==1)?$row[VAL]:$row[VAL]*$company_exchange_rate_arr[$company_id];	
		$row[VAL_USD]=($row[CURRENCY_ID]==1)?($row[VAL]/$company_exchange_rate_arr[$company_id]):$row[VAL];	
	?>
    <tr>
         <td><?=$i;?></td>
         <td><?=$supplier_library[$row[SUPPLIER_ID]];?></td>
         <td><?=$item_arr[$row[ITEM_GROUP_ID]];?></td>
         <td><?=$row[ITEM_DESCRIPTION];?></td>
         <td align="right"><?=$row[QTY];?></td>
         <td><?=$unit_of_measurement[$row[ORDER_UOM]];?></td>
         <td><?=$currency[$row[CURRENCY_ID]];?></td>
         <td><?=number_format($row[VAL_USD]/$row[QTY],2);?></td>
         <td align="right"><?=$row[VAL_USD];?></td>
         <td align="right"><?=number_format($row[VAL_TK]/$row[QTY],2);?></td>
         <td align="right"><?=$row[VAL_TK];?></td>
    </tr>
    <?
	$i++;
	}
	?>

</table>  
   
   <br />
   
   <table cellpadding="0" cellspacing="0" rules="all" border="1">
    <tr>
        <td colspan="11" align="center"><strong>Accessories Issued</strong></td>
    </tr>
    <tr bgcolor="#EEE">
         <th>SL</th>
         <th>Supplier Name</th>
         <th>Item Group</th>
         <th>Item Description</th>
         <th>Qty</th>
         <th>UOM</th>
         <td>Received Currency</td>
         <th>Avg. Rate</th>
         <th>Amount</th>
         <th>Avg. Rate (Tk.)</th>
         <th>Amount (Tk.)</th>
    </tr>
    <?
	$i=1;
	foreach($acc_data_arr[$company_id] as $row){
		$row[VAL_TK]=$row[VAL];	
		$row[VAL_USD]=($row[VAL]/$company_exchange_rate_arr[$company_id]);	
	?>
    <tr>
         <td><?=$i;?></td>
         <td><?=$supplier_library[$row[SUPPLIER_ID]];?></td>
         <td><?=$item_arr[$row[ITEM_GROUP_ID]];?></td>
         <td><?=$row[ITEM_DESCRIPTION];?></td>
         <td align="right"><?=$row[QTY];?></td>
         <td align="center"><?=$unit_of_measurement[$row[ORDER_UOM]];?></td>
         <td align="center"><?=$currency[$row[CURRENCY_ID]];?></td>
         <td align="right"><?=number_format($row[VAL_USD]/$row[QTY],2);?></td>
         <td align="right"><?=number_format($row[VAL_USD],2);?></td>
         <td align="right"><?=number_format($row[VAL_TK]/$row[QTY],2);?></td>
         <td align="right"><?=$row[VAL_TK];?></td>
    </tr>
    <?
	$i++;
	}
	?>

</table>  
   
   <br />     



 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="5" align="center"><strong>Fabric Issue to Cutting</strong></td>
        </tr>
        
        <tr>
            <td align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer</strong></td>
            <td align="center"><strong>In house</strong></td>
            <td align="center"><strong>Sub Con</strong></td>
            <td align="center"><strong>Total</strong></td>
        </tr>
        <?
            
            $i=0;
            
            $in_house_qty=$sub_con_qty=$grand_total_qty=0;
            foreach($fbIssueDataArr[$company_id] as $buyer_id=>$row)
            {
              $in_house_qty += $row[1];
              $sub_con_qty += $row[3];   
              $grand_total_qty += $row[1]+$row[3];   
              
              $i++;
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><? echo $buyer_lib[$buyer_id]; ?></td>
            <td align="right"><? echo $row[1]; ?></td>
            <td align="right"><? echo $row[3]; ?></td>
            <td align="right"><? echo $row[1]+$row[3]; ?></td>
        </tr>
        <?	
            $flag=1;
            }
        ?> 
        <tr>
            <tfoot>
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><? echo number_format($in_house_qty ,2)  ?></th>
                <th align="right"><? echo number_format($sub_con_qty,2)  ?></th>
                <th align="right"><? echo number_format($grand_total_qty ,2)  ?></th>
            </tfoot>
        </tr>
     </table>
 <br />    
     
 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="11" height="30" align="center"><strong>Cutting Production</strong></td>
        </tr>
        
        <tr bgcolor="#EEE">
            <td width="30" align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer</strong></td>
            <td width="90" align="center"><strong>In-house Self Order</strong></td>
            <td width="90" align="center"><strong>In-house Sub-con Order</strong></td>
            <td width="90" align="center"><strong>Out Side Self Order</strong></td>
            <td width="90" align="center"><strong>Total In-house</strong></td>
            <td width="90" align="center"><strong>Total Self Order</strong></td>
            <td width="70" align="center"><strong>Reject Qty.</strong></td>
            <td width="90" align="center"><strong>Total Cutting </strong></td>
            <td width="50" align="center"><strong>Reject %</strong></td>
        </tr>
        <?
        

        $i=1;
        $grand_inhouseSelfOrder=0;$grand_inhouseSubconOrder=0;$grand_outSideSelfOrder=0;		
        $grand_totalInhouse=0;$grand_totalSelfOrder=0;$grand_totalCutting=0;$grand_totalReject=0;
        foreach($cuttingDataArr[$company_id] as $buyer_id=>$row)
        {
            $totalInhouse=$row[1]+$row[2];	
            $totalSelfOrder=$row[1]+$row[3];
            $totalCutting=$row[1]+$row[2]+$row[3];
            $totalReject=$rejectQtyArr[$company_id][$buyer_id][1]+$rejectQtyArr[$buyer_id][2]+$rejectQtyArr[$buyer_id][3];
            
            
            $grand_inhouseSelfOrder+=$row[1];	
            $grand_inhouseSubconOrder+=$row[2];	
            $grand_outSideSelfOrder+=$row[3];	
            $grand_totalInhouse+=$totalInhouse;	
            $grand_totalSelfOrder+=$totalSelfOrder;
            $grand_totalCutting+=$totalCutting;
            $grand_totalReject+=$totalReject;
            
            
            
            
            
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><? echo $buyer_lib[$buyer_id]; ?></td>
            <td align="right"><? echo $row[1];?></td>                 
            <td align="right"><? echo $row[2];?></td>                 
            <td align="right"><? echo $row[3];?></td>                 
            <td align="right"><? echo $totalInhouse;?></td>                 
            <td align="right"><? echo $totalSelfOrder;?></td>                 
            <td align="right"><? echo $totalReject;?></td>                 
            <td align="right"><? echo $totalCutting;?></td>                 
            <td align="right"><? echo number_format(($totalReject/$totalCutting)*100,4); ?></td>                 
        </tr>
        <?	
        $i++;
        }
        
        ?> 
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><? echo $grand_inhouseSelfOrder;?></th>
                <th align="right"><? echo $grand_inhouseSubconOrder; ?></th>
                <th align="right"><? echo $grand_outSideSelfOrder; ?></th>
                <th align="right"><? echo $grand_totalInhouse; ?></th>
                <th align="right"><? echo $grand_totalSelfOrder; ?></th>
                <th align="right"><? echo $grand_totalReject; ?></th>
                <th align="right"><? echo $grand_totalCutting; ?></th>
                <th align="right"><? echo number_format(($grand_totalReject/$grand_totalCutting)*100,4);?></th>
            </tfoot>
        </tr>
     </table>
 <br />

 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="10" height="30" align="center"><strong>Print Production</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <td width="30" align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer</strong></td>
            <td width="90" align="center"><strong>In-house Self Order</strong></td>
            <td width="90" align="center"><strong>In-house Sub-con Order</strong></td>
            <td width="90" align="center"><strong>Total In-house</strong></td>
            <td width="90" align="center"><strong>Total Self Order</strong></td>
            <td width="70" align="center"><strong>Reject Qty.</strong></td>
            <td width="90" align="center"><strong>Total Print Production</strong></td>
            <td width="50" align="center"><strong>Reject %</strong></td>
        </tr>
        <?
        $i=1;
		foreach($productionDataArr[$company_id][QCPASS_QTY] as $datStr=>$data_row)
        {
			list($buyer_id,$within_group)=explode('**',$datStr);
			$buyerArr=($within_group==1)?$company_lib:$buyer_lib;
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><?= $buyerArr[$buyer_id]; ?></td>
            <td align="right"><?=$data_row[1];?></td>                 
            <td align="right"><?=$data_row[2];?></td>                 
            <td align="right"><? ?></td>                 
            <td align="right"><? ?></td>                 
            <td align="right"><?=$productionDataArr[$company_id][REJE_QTY][$datStr];?></td>                 
            <td align="right"><?= $total_production = ($data_row[1]+$data_row[2]);?></td>                 
            <td align="right"><?= number_format(($productionDataArr[$company_id][REJE_QTY][$datStr]/$total_production)*100,4); ?></td>
        </tr>
        <?	
        $i++;
        }
        
        ?> 
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><? echo $grand_inhouseSelfOrder;?></th>
                <th align="right"><? echo $grand_outSideSelfOrder; ?></th>
                <th align="right"><? echo $grand_totalInhouse; ?></th>
                <th align="right"><? echo $grand_totalSelfOrder; ?></th>
                <th align="right"><? echo $grand_totalReject; ?></th>
                <th align="right"><? echo $grand_totalCutting; ?></th>
                <th align="right"><? echo number_format(($grand_totalReject/$grand_totalCutting)*100,4);?></th>
            </tfoot>
        </tr>
     </table>
 <br />

 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <tr>
            <td colspan="10" height="30" align="center"><strong>Embroidery Production</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <td width="30" align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer</strong></td>
            <td width="90" align="center"><strong>In-house Self Order</strong></td>
            <td width="90" align="center"><strong>In-house Sub-con Order</strong></td>
            <td width="90" align="center"><strong>Total In-house</strong></td>
            <td width="90" align="center"><strong>Total Self Order</strong></td>
            <td width="70" align="center"><strong>Reject Qty.</strong></td>
            <td width="90" align="center"><strong>Total Print Production</strong></td>
            <td width="50" align="center"><strong>Reject %</strong></td>
        </tr>
        <?
        $i=1;
		foreach($embDataArr[$company_id][QCPASS_QTY] as $datStr=>$data_row)
        {
			list($buyer_id,$within_group)=explode('**',$datStr);
			$buyerArr=($within_group==1)?$company_lib:$buyer_lib;
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><?= $buyerArr[$buyer_id]; ?></td>
            <td align="right"><?=$data_row[1];?></td>                 
            <td align="right"><?=$data_row[2];?></td>                 
            <td align="right"><? ?></td>                 
            <td align="right"><? ?></td>                 
            <td align="right"><?=$embDataArr[$company_id][REJE_QTY][$datStr];?></td>                 
            <td align="right"><?= $total_production = ($data_row[1]+$data_row[2]);?></td>                 
            <td align="right"><?= number_format(($embDataArr[$company_id][REJE_QTY][$datStr]/$total_production)*100,4); ?></td>
        </tr>
        <?	
        $i++;
        }
        
        ?> 
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><? echo $grand_inhouseSelfOrder;?></th>
                <th align="right"><? echo $grand_outSideSelfOrder; ?></th>
                <th align="right"><? echo $grand_totalInhouse; ?></th>
                <th align="right"><? echo $grand_totalSelfOrder; ?></th>
                <th align="right"><? echo $grand_totalReject; ?></th>
                <th align="right"><? echo $grand_totalCutting; ?></th>
                <th align="right"><? echo number_format(($grand_totalReject/$grand_totalCutting)*100,4);?></th>
            </tfoot>
        </tr>
     </table>
 <br />

 <table cellpadding="0" cellspacing="0" rules="all" border="1">
        <thead>
            <tr>
                <th colspan="7">Sewing</th>
            </tr>
            <tr>
                <th>Operator</th>
                <th>Helper</th>
                <th>Total Man Power</th>
                <th>Day Target</th>
                <th>Total Production</th>
                <th>Achieve %</th>
                <th>Sewing Efficiency</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="right"><?=$actual_manpawer_data_arr[$company_id][OPERATOR];?></td>
                <td align="right"><?=$actual_manpawer_data_arr[$company_id][HELPER] ;?></td>
                <td align="right"><?=$actual_manpawer_data_arr[$company_id][MAN_POWER] ;?></td>
                <td align="right"><?=$actual_manpawer_data_arr[$company_id][DAY_TARGET] ;?></td>
                <td align="right"><?=array_sum($pro_qnty[$company_id]);?></td>
                <td><? ;?></td>
                <td><? ;?></td>
            </tr>
        </tbody>
    </table>
 <br />


 <table cellpadding="0" cellspacing="0" rules="all" border="1">
            <tr>
                <td colspan="10" align="center"><strong>Sewing Completed</strong></td>
            </tr>
            <tr bgcolor="#EEE">
                <td width="30">SL</td>
                <td width="130">Buyer</td>
                <td width="100">Good Qty. (Pcs)</td>
                <td width="70">Reject Qty.</td>
                <td width="70">Alter Qty.</td>
                <td width="70">Spot Qty.</td>
                <td width="100">Total</td>
                <td width="100">FOB Value</td>
            </tr>
            <?
               
				//$rej_qnty=array(); $alter_qnty=array(); $pro_qnty=array(); 
//				$spot_qnty=array(); $total_qnty=array(); $fob_val=array();
				
				$tot_production_quantity=0; $tot_reject_qnty=0;
				$tot_alter_qnty=0; $tot_spot_qnty=0; $tot_all=0; $tot_fob_val=0; $tot_earningCM=0;
			   
			    $i=0;
                foreach($pro_qnty[$company_id] as $buyer_id=>$row)
                {
                    
                    $i++;
            ?>
                <tr>
                    <td align="center"><?= $i; ?></td>
                    <td><?= $buyer_lib[$buyer_id]; ?></td>
                    <td align="right">
                        <?
                            echo fn_remove_zero($pro_qnty[$company_id][$buyer_id],2);
                           $tot_production_quantity += $pro_qnty[$company_id][$buyer_id]; 
                        ?>
                    </td>
                    <td align="right">
                        <?
                           echo fn_remove_zero($rej_qnty[$company_id][$buyer_id],2);
                           $tot_reject_qnty += $rej_qnty[$company_id][$buyer_id]; 
                        ?>
                    </td>                 
                    <td align="right">
                        <?
                            echo fn_remove_zero($alter_qnty[$company_id][$buyer_id],2);
                            $tot_alter_qnty += $alter_qnty[$company_id][$buyer_id]; 
                        ?>
                    </td>
                    <td align="right">
                        <?
                            echo fn_remove_zero($spot_qnty[$company_id][$buyer_id],2);
                            $tot_spot_qnty += $spot_qnty[$company_id][$buyer_id]; 
                        ?>
                    </td>
                    <td align="right">
                        <?
                            $total= $total_qnty[$company_id][$buyer_id];
                            echo fn_remove_zero($total,2);
                            $tot_all += $total; 
                        ?>
                    </td>
                    <td align="right">
                        <?
                            $fob= $fob_val[$company_id][$buyer_id];
                            echo fn_remove_zero($fob,2);
                            $tot_fob_val += $fob; 
                        ?>
                    </td>
                </tr>
                <?	
                    $flag=1;
                }
            ?> 
            <tr bgcolor="#EEE">
                <td>&nbsp;</td>
                <td align="center"><b>Total</b></td>
                <td align="right"><b><? echo number_format($tot_production_quantity,2)  ?></b></td>
                <td align="right"><b><? echo number_format($tot_reject_qnty,2);  ?></b></td>
                <td align="right"><b><? echo number_format($tot_alter_qnty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_spot_qnty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_all,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_fob_val,2); ?></b></td>
            </tr>
            
         </table>
 <br />
   
 <table cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
            <td colspan="7" align="center"><strong>Garments Finishing</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <td align="center">SL</td>
            <td>Buyer</td>
            <td align="right">Iron Qty. (Pcs)</td>
            <td align="right">Hangtag Qty. (Pcs)</td>
            <td align="right">Poly Qty. (Pcs)</td>
            <td align="right">Packing and Finishing Qty. (Pcs)</td>
            <td align="center">Number of Carton</td>
        </tr>
        <?
            $i=0;
            $totalArr=array();
            foreach($pro_data_arr[PRO_QTY][$company_id] as $buyer_id=>$row)
            {
                $i++;
				
				$row['IRON']=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][7];
				$row['POLY']=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][11];
				$row['HANG_TAG']=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][18];
				$row['PACKING_FINISH']=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][8];
				$row['PACKING_CRT']=$pro_data_arr[CRT_QTY][$company_id][$buyer_id][8];
				
				
				$totalArr['IRON']+=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][7];
				$totalArr['POLY']+=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][11];
				$totalArr['HANG_TAG']+=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][18];
				$totalArr['PACKING_FINISH']+=$pro_data_arr[PRO_QTY][$company_id][$buyer_id][8];
				$totalArr['PACKING_CRT']+=$pro_data_arr[CRT_QTY][$company_id][$buyer_id][8];
				
				
				
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><?= $buyer_lib[$buyer_id]; ?></td>
            <td align="right"><?=number_format($row['IRON'],2);?></td>
            <td align="right"><?=number_format($row['HANG_TAG'],2);?></td>
            <td align="right"><?=number_format($row[POLY],2);?></td>
            <td align="right"><?=number_format($row[PACKING_FINISH],2);?></td>
            <td align="right"><?=number_format($row[PACKING_CRT],2); ?></td>
        </tr>
        <?	
            $flag=1;
            }
        ?> 
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><?=number_format($totalArr['IRON'],2);?></th>
                <th align="right"><?=number_format($totalArr['HANG_TAG'],2);?></th>
                <th align="right"><?=number_format($totalArr[POLY],2);?></th>
                <th align="right"><?=number_format($totalArr[PACKING_FINISH],2);?></th>
                <th align="right"><?=number_format($totalArr[PACKING_CRT],2); ?></th>
            </tfoot>
        </tr>
     </table>
 <br />


 <table cellpadding="0" cellspacing="0" border="1" rules="all">
    <tr>
        <th colspan="7" align="center"><strong>Final Inspection</strong></th>
    </tr>
    
    <tr bgcolor="#EEE">
        <th>SL</th>
        <th>Job No</th>
        <th>Buyer</th>
        <th>Order No</th> 
        <th>Inspection Qty</th> 
        <th>Shipment Date</th>     
        <th>Inspection Status</th>                        
    </tr>
    <?
    $i=0;
    foreach($pro_buyer_inspection_data_arr[$company_id] as $row)
    {
    ?>
    <tr>
        <td align="center"><? echo $i; ?></td>
        <td><? echo $row[csf('job_no')]; ?></td>
        <td><? echo $buyer_lib[$row[csf('buyer_name')]]; ?></td>
        <td><? echo $row[csf('po_number')]; ?></td>
        <td align="right"><? echo $row[csf('inspection_qnty')]; ?></td>
        <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
        <td align="center"><? echo $inspection_status[$row[csf('inspection_status')]]; ?></td>
    </tr>
    <?	
    }
    ?> 
 </table>
 <br />

 <table cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
            <td colspan="4" align="center"><strong>Ex-factory Done</strong></td>
        </tr>
        
        <tr bgcolor="#EEE">
            <td align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer</strong></td>
            <td align="center"><strong>Delv. Qty. (Pcs)</strong></td>
            <td align="center"><strong>FOB Value</strong></td>
        </tr>
        <?
             $tot_ex_factory_val=0;$tot_ex_factory_qnty=0;
            foreach($ex_fac_data_arr[ex_fac_qty][$company_id] as $buyer_id=>$row)
            {
                
                $i++;
				$qty = $ex_fac_data_arr[ex_fac_qty][$company_id][$buyer_id];
				$val = $ex_fac_data_arr[ex_fac_val][$company_id][$buyer_id];
					
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td><? echo $buyer_lib[$buyer_id]; ?></td>
					<td align="right">
						<?
							echo number_format($qty,2);
						   $tot_ex_factory_qnty += $qty; 
						?>
					</td>
					<td align="right">
						<?
							echo number_format($val,2);
						   $tot_ex_factory_val += $val; 
						?>
					</td>
				</tr>
				<?	
            }
        ?> 
        <tr>
            <tfoot bgcolor="#EEE">
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><? echo number_format($tot_ex_factory_qnty,2);  ?></th>
                <th align="right"><? echo number_format($tot_ex_factory_val,2);  ?></th>
            </tfoot>
        </tr>
     </table>
 <br />

    <table cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
            <td colspan="7" height="30" align="center"><strong>Full Shipment</strong></td>
        </tr>
        
        <tr bgcolor="#EEE">
            <td align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer Name</strong></td>
            <td align="center"><strong>Job No</strong></td>
            <td align="center"><strong>PO No</strong></td>
            <td align="center"><strong>Plan Ship Qty</strong></td>
            <td align="center"><strong>Actual Ship Qty</strong></td>
            <td align="right"><strong>Value</strong></td>
        </tr>
        <?  
            $tot_plan_cut=0; $tot_po_quantity=0; $tot_po_total_price=0; $tot_exf_quantity=0;
            foreach($full_ship_data_arr[$company_id] as $row){
				$row[csf('po_total_price')]=($row[csf('po_total_price')]/$row[csf('po_quantity')])*$row[EXFACTORY_QTY];
            $i++;
            $tot_plan_cut+=$row[csf('plan_cut')];
            $tot_po_quantity+=$row[csf('po_quantity')];
            $tot_exf_quantity+=$row[EXFACTORY_QTY];
            $tot_po_total_price+=$row[csf('po_total_price')];
			
			
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><? echo $buyer_lib[$row[csf('buyer_name')]]; ?></td>
            <td><?= $row[JOB_NO_MST]; ?></td>
            <td><? echo $row[csf('po_number')]; ?></td>
            <td align="right"><? echo $row[csf('plan_cut')]; ?></td>
            <td align="right"><? echo $row[EXFACTORY_QTY]; ?></td>
            <td align="right"><? echo number_format($row[csf('po_total_price')],2); ?></td>
        </tr>
        <?
            }
        ?>
        
        <tr>
            <tfoot bgcolor="#EEE">
                <th colspan="4">Total</th>
                <th align="right"><? echo number_format($tot_plan_cut);  ?></th>
                <th align="right"><? echo number_format($tot_exf_quantity);  ?></th>
                <th align="right"><? echo number_format($tot_po_total_price,2);  ?></th>
            </tfoot>
        </tr>
        
    </table>
 <br />

    <table cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
            <td colspan="8" align="center"><strong>Leftover Garments After Shipment</strong></td>
        </tr>
        
        <tr bgcolor="#EEE">
            <td width="30" align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer Name</strong></td>
            <td align="center"><strong>Job No</strong></td>
            <td align="center"><strong>Style</strong></td>
            <td align="center"><strong>PO No</strong></td>
            <td align="center"><strong>Fin Qty</strong></td>
            <td align="center"><strong>Ex-Fac Qty</strong></td>
            <td align="center"><strong>Leftover Qty</strong></td>
        </tr>
        <?
            $i=1;
            foreach($leftover_data_arr[$company_id] as $row){
            
            $leftover_qty=($row[csf('finish_quantity')]-$row[csf('ex_factory_qnty')]);
            if($leftover_qty){
                ?>
                <tr>
                    <td align="center"><? echo $i; ?></td>
                    <td><? echo $buyer_lib[$row[csf('buyer_name')]]; ?></td>
                    <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                    <td><? echo $row[csf('style_ref_no')]; ?></td>
                    <td><? echo $row[csf('po_number')]; ?></td>
                    <td align="right"><? echo $row[csf('finish_quantity')]; ?></td>
                    <td align="right"><? echo $row[csf('ex_factory_qnty')]; ?></td>
                    <td align="right"><? echo $leftover_qty; ?></td>
                </tr>
                <?
                    $i++;
                }
            }
    
        ?>
        
    </table>
 <br />

     
    <table cellpadding="0" cellspacing="0" border="1" rules="all">
        <tr>
            <td colspan="6" align="center"><strong>Export Proceed Realized</strong></td>
        </tr>
        <tr bgcolor="#EEE">
            <td align="center"><strong>SL</strong></td>
            <td align="center"><strong>Buyer</strong></td>
            <td align="center"><strong>LC/SC</strong></td>
            <td align="center"><strong>LC/SC No</strong></td>
            <td align="center"><strong>Realized</strong></td>
            <td align="center"><strong>Short Realized</strong></td>
        </tr>
        <?
            $i=0; $tot_realized=0; $tot_short_realized=0;
            foreach($realization_invoice_data_arr[$company_id] as $row_invoice)
            {
               $i++;
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><?= $buyer_lib[$row_invoice[csf('buyer_id')]]; ?></td>
            <td align="center"><? if($row_invoice[csf('is_lc')] == 1) echo "LC"; else echo "SC"; ?></td>
            <td>
                <? 
                    if($row_invoice[csf('is_lc')] == 1) 
                    {
                        $lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row_invoice[lc_sc_id]' and status_active=1 and is_deleted=0");
                        echo $lc_no;
                    }
                    else
                    {
                        $sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row_invoice[lc_sc_id]' and status_active=1 and is_deleted=0"); 
                        echo $sales_cont_no;
                    }
                ?>
            </td>
            <td align="right">
                <? 
                    if($row_invoice[csf('type')] == 1) 
                    {
                        echo number_format($row_invoice[csf('tot_document_currency')],2);
                        $tot_realized+= $row_invoice[csf('tot_document_currency')];
                    }
                ?>
            </td>
            <td align="right">
                <? 
                    if($row_invoice[csf('type')] == 0) 
                    {
                        echo number_format($row_invoice[csf('tot_document_currency')],2);
                        $tot_short_realized+= $row_invoice[csf('tot_document_currency')];
                    }
                ?>
            </td>
        </tr>
        <?	
            $flag=1;
            }
            
            
            foreach($realization_bill_data_arr[$company_id] as $row_bill)
            {
                $i++;
        ?>
        <tr>
            <td align="center"><? echo $i; ?></td>
            <td><?   echo $buyer_lib[$row_bill[csf('buyer_id')]]; ?></td>
            <td align="center"><? if($row_bill[csf('is_lc')] == 1) echo "LC"; else echo "SC"; ?></td>
            <td>
                <? 
                    if($row_bill[csf('is_lc')] == 1) 
                    {
                        $lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row_bill[lc_sc_id]' and status_active=1 and is_deleted=0");
                        echo $lc_no;
                    }
                    else
                    {
                        $sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row_bill[lc_sc_id]' and status_active=1 and is_deleted=0"); 
                        echo $sales_cont_no;
                    }
                ?>
            </td>
            <td align="right">
                <? 
                    if($row_bill[csf('type')] == 1) 
                    {
                        echo number_format($row_bill[csf('tot_document_currency')],2);
                        $tot_realized+= $row_bill[csf('tot_document_currency')];
                    }
                ?>
            </td>
            <td align="right">
                <? 
                    if($row_bill[csf('type')] == 0) 
                    {
                        echo number_format($row_bill[csf('tot_document_currency')],2); 
                        $tot_short_realized+= $row_bill[csf('tot_document_currency')];
                    }
                ?>
            </td>
        </tr>
        <?	
        $flag=1;
        }
        ?> 
        <tr>
            <tfoot bgcolor="#EEE">
                <th colspan="4" align="center">Total :</th>
                <th align="right"><?  echo number_format($tot_realized,2);   ?></th>
                <th align="right"><?  echo number_format($tot_short_realized,2);   ?></th>
            </tfoot>
        </tr>
     </table>     
     
     

<?

    	
	$message=ob_get_contents();
	ob_clean();


	$sql = "SELECT c.email_address as MAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=2 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$company_id";
	
	$mailArr=array();
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$mailArr[$row[MAIL_ADDRESS]]=$row[MAIL_ADDRESS];
	}
	
	$to=implode(',',$mailArr);
	$subject="Total Activities of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
	$header=mailHeader();

	if($_REQUEST['isview']==1){
		echo $to.$message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}





}

?>



 