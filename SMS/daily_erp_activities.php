 <?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require_once('setting/sms_setting.php');


$current_date = change_date_format(date("Y-m-d H:i:s",time()),'','',1);
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);


$company_lib =return_library_array( "select id, COMPANY_SHORT_NAME from lib_company where status_active=1 and is_deleted=0", "id", "COMPANY_SHORT_NAME");

$company_ids = implode(',',array_keys($company_lib));

//Order part start...............................................................
	//$str_cond_b	=" and  ((b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM') or  ( b.UPDATE_DATE between '".$previous_date."' and '".$current_date." 11:59:59 PM'))";
	
	$order_sql="select b.INSERT_DATE,a.COMPANY_NAME,b.IS_CONFIRMED,B.SHIPING_STATUS,sum(a.total_set_qnty*b.po_quantity) as PO_QUANTITY, sum(b.po_total_price) as PO_TOTAL_PRICE from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name in($company_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  group by a.company_name,b.is_confirmed,b.shiping_status,b.insert_date";
	 //echo $order_sql;die;					
						
	$order_sql_res=sql_select($order_sql);
	$poDataArr=array();
	foreach($order_sql_res as $row)
	{
		if($row[SHIPING_STATUS]<3){
			$poDataArr[PO_QTY][$row[COMPANY_NAME]][$row[IS_CONFIRMED]]+=$row[PO_QUANTITY];
			$poDataArr[PO_VAL][$row[COMPANY_NAME]][$row[IS_CONFIRMED]]+=$row[PO_TOTAL_PRICE];
		}
		
		$poDataArr[SHIP_STATUS_QTY][$row[COMPANY_NAME]][$row[SHIPING_STATUS]]+=$row[PO_QUANTITY];
		
		if(date('d-m-Y',strtotime($row[INSERT_DATE]))==date('d-m-Y',strtotime($previous_date))){
			$poDataArr[NEW_ORDER_QTY][$row[COMPANY_NAME]]+=$row[PO_QUANTITY];
		}
		
		
	}


//............................................................Order Part end;


//LC SC start...............................................................
	$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";

	$sql_lc_sc="SELECT BENEFICIARY_NAME,sum(lc_value) as LC_SC_VALUE from com_export_lc where beneficiary_name in($company_ids) and status_active=1 and is_deleted=0 $str_cond group by BENEFICIARY_NAME
	union all
	SELECT BENEFICIARY_NAME,sum(contract_value) as LC_SC_VALUE from com_sales_contract where beneficiary_name in($company_ids) and status_active=1 and is_deleted=0 $str_cond group by BENEFICIARY_NAME";
	// echo $sql_lc_sc;die;
	
	$sql_lc_sc_res=sql_select($sql_lc_sc);
	foreach($sql_lc_sc_res as $row)
	{
		$lcScDataArr[LC_SC_VALUE][$row[BENEFICIARY_NAME]]+=$row[LC_SC_VALUE];
	}
	
//............................................................LC SC end;



//BACK TO BACK start...............................................................
     $back_to_back_sql="Select IMPORTER_ID,sum(lc_value) as LC_VALUE from com_btb_lc_master_details where importer_id in($company_ids) and status_active=1 and is_deleted=0 $str_cond group by IMPORTER_ID";

	$back_to_back_sql_res=sql_select($back_to_back_sql);
	foreach($back_to_back_sql_res as $row)
	{
		$btbLcDataArr[LC_VALUE][$row[IMPORTER_ID]]+=$row[LC_VALUE];
	}
//............................................................BACK TO BACK end;







//Fabric Booking Revised start...............................................................
	$bookingHistorySql="SELECT a.COMPANY_ID,a.UOM,b.BOOKING_NO,b.GREY_FAB_QNTY FROM WO_BOOKING_MST a,WO_BOOKING_DTLS b WHERE   a.BOOKING_NO= b.BOOKING_NO and  b.BOOKING_NO in(select BOOKING_NO from WO_BOOKING_MST_HSTRY where revised_date='$previous_date' and a.COMPANY_ID in($company_ids)) and a.COMPANY_ID in($company_ids) and a.UOM in(12,27) AND b.status_active = 1 AND b.is_deleted = 0 ";
	//echo $bookingHistorySql;die;// PO_BREAK_DOWN_ID,
	$bookingHistorySqlRes=sql_select($bookingHistorySql);
	$jobArr=array();
	foreach($bookingHistorySqlRes as $row)
	{
		$fabRevisedQty[$row[COMPANY_ID]][$row[UOM]]+=$row[GREY_FAB_QNTY];
	}
//............................................................Fabric Booking Revised end;


//Budget start...............................................................
	
	$str_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	$budgetSql="select  A.APPROVED,c.COMPANY_NAME,a.INSERT_DATE, (c.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QTY_PCS  from wo_pre_cost_mst a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c where a.job_no=b.job_no_mst and  a.job_no=c.job_no $str_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0  and c.COMPANY_NAME in($company_ids) ";
	 // echo $budgetSql;die;// PO_BREAK_DOWN_ID,
	$budgetSqlRes=sql_select($budgetSql);
	$jobArr=array();
	foreach($budgetSqlRes as $row)
	{
		$precostDataArr[NEW_PRECOST_QTY][$row[COMPANY_NAME]]+=$row[PO_QTY_PCS];
		if($row[APPROVED]==1){
			$precostDataArr[PRECOST_APP_QTY][$row[COMPANY_NAME]]+=$row[PO_QTY_PCS];
		}
	}


//............................................................Budget end;


$recipient_number_arr=getSMSRecipient(array(item=>1));


foreach($company_lib as $company_id=>$company_name){
	
	$smsBody="SMS Generated On :".date('d-M-Y h:i:s A',time())."\n";	
	$smsBody.="".$company_name." Order"."\n";	
		
	$smsBody.="Projection Order [Pcs] : ".round($poDataArr[PO_QTY][$company_id][2])."\n";	
	$smsBody.="Confirm Order [Pcs] :  ".round($poDataArr[PO_QTY][$company_id][1])."\n";	
	$smsBody.="Export LC/Sales Contract Received : ".number_format($lcScDataArr[LC_SC_VALUE][$company_id])."\n";
	$smsBody.="Back to Back LC Open : ".number_format($btbLcDataArr[LC_VALUE][$company_id])."\n";
	
	
	$smsBody.="Fabric Booking Revised [Kg] : ".number_format($fabRevisedQty[$company_id][12])."\n";
	$smsBody.="Fabric Booking Revised [Yds] : ".number_format($fabRevisedQty[$company_id][27])."\n";
	
	$smsBody.="Inactive Order [Pcs] : ".number_format($poDataArr[SHIP_STATUS_QTY][$company_id][2])."\n";
	$smsBody.="Cancel Order [Pcs] : ".number_format($poDataArr[SHIP_STATUS_QTY][$company_id][3])."\n";
	$smsBody.="New Order Receive [Pcs] : ".number_format($poDataArr[NEW_ORDER_QTY][$company_id])."\n";
	
	
	$smsBody.="New Budget $ : ".number_format($precostDataArr[NEW_PRECOST_QTY][$company_id],2)."\n";
	$smsBody.="Budget Approved : ".number_format($precostDataArr[PRECOST_APP_QTY][$company_id],2)."\n";
	
	//echo implode(',',$recipient_number_arr[$company_id]);
	$mobile_number_arr=array();
	foreach($recipient_number_arr[$company_id] as $buyerRows){
		foreach($buyerRows as $brandRows){
			foreach($brandRows as $number){
				$mobile_number_arr[]=$number;
			}
		}
	}
	
	if(count($mobile_number_arr)){
		sendSMS($mobile_number_arr,$smsBody);//array('01511100004,01709632668,01975643095')
	}



}



?>
