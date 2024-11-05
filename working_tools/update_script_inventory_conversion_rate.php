<?
include('../includes/common.php');
$con = connect();

//die;

$current_date=date("d-m-Y");
$p=1;
$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
$company_wise_data=array();
foreach($queryText as $row)
{
	$company_wise_data[$row["COMPANY_ID"]]++;
}
$conversion_data_arr=array();$previous_date="";$company_check_arr=array();
foreach($queryText as $val)
{
	if($company_check_arr[$val["COMPANY_ID"]]=="")
	{
		$company_check_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
		$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])]=$val["CONVERSION_RATE"];
		$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
		$sCurrentDate = $sStartDate;
		$sEndDate = $sStartDate;
		$previous_date=$sStartDate;
		$previous_rate=$val["CONVERSION_RATE"];
		//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
		
		$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
		$sEndDate = date("Y-m-d", strtotime($current_date));
		$sCurrentDate = $sStartDate;
		//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
		while ($sCurrentDate <= $sEndDate) {
			
			$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
			$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
		}
		$q=1;
	}
	else
	{
		$q++;
		$sStartDate = date("Y-m-d", strtotime($previous_date));
		if($company_wise_data[$val["COMPANY_ID"]]==$q)
		{
			$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
			$sCurrentDate = $sStartDate;
			//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
			while ($sCurrentDate <= $sEndDate) {
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
				$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
			}
			
			$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
			$sEndDate = date("Y-m-d", strtotime($current_date));
			$sCurrentDate = $sStartDate;
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
			while ($sCurrentDate <= $sEndDate) {
				
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
				$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
			}
			$previous_date=$val["CON_DATE"];
			$previous_rate=$val["CONVERSION_RATE"];
		}
		else
		{
			$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
			$sCurrentDate = $sStartDate;
			//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
			while ($sCurrentDate <= $sEndDate) {
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
				$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
			}
			$previous_date=$val["CON_DATE"];
			$previous_rate=$val["CONVERSION_RATE"];
		}
	}
	$p++;
}
unset($queryText);
//echo "<pre>";print_r($conversion_data_arr);die;

/*
$upBook=true;
$sql_booking_data=sql_select("select ID, COMPANY_ID, BOOKING_DATE, ECCHANGE_RATE from WO_YARN_DYEING_MST where CURRENCY=2 and status_active=1 and is_deleted=0 and BOOKING_DATE>'31-Dec-2021'");
foreach($sql_booking_data as $val)
{
	$lib_exchange_rate=$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["BOOKING_DATE"])];
	//echo $lib_exchange_rate."=".$val["COMPANY_ID"]."=".$val["BOOKING_DATE"];die;
	$entry_exchange_rate=$val["ECCHANGE_RATE"];
	if($lib_exchange_rate!=$entry_exchange_rate && $lib_exchange_rate >1)
	{
		$upBook=execute_query("update WO_YARN_DYEING_MST set ECCHANGE_RATE='".$lib_exchange_rate."' where id=".$val["ID"]." ");
		if($upBook==false){echo"update WO_YARN_DYEING_MST set ECCHANGE_RATE='".$lib_exchange_rate."' where id=".$val["ID"]."";oci_rollback($con);die;}
	}
}

if($upBook)
{
	oci_commit($con); 
	echo "Book Data Update Successfully. <br>";die;
}
else
{
	oci_rollback($con);
	echo "Book Data Update Failed. <br>";
	die;
}

die;

$sql_booking="select a.ID as MST_ID, a.CURRENCY, a.ECCHANGE_RATE, a.COMPANY_ID, a.BOOKING_DATE , b.product_id, b.DYEING_CHARGE, b.YARN_COLOR, c.YARN_COUNT_ID
, c.YARN_COMP_TYPE1ST as PROD_YARN_COMP_TYPE1ST, c.YARN_TYPE as PROD_YARN_TYPE, c.COLOR AS PROD_COLOR, c.LOT
from WO_YARN_DYEING_MST a, WO_YARN_DYEING_DTLS b, PRODUCT_DETAILS_MASTER c 
where a.id=b.mst_id and b.PRODUCT_ID=c.id and a.CURRENCY=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.DYEING_CHARGE>0";
$sql_booking_result=sql_select($sql_booking);
$booking_data=array();
foreach($sql_booking_result as $row)
{
	$booking_data[$row["MST_ID"]][$row["YARN_COUNT_ID"]][$row["PROD_YARN_COMP_TYPE1ST"]][$row["PROD_YARN_TYPE"]][$row["YARN_COLOR"]]=number_format(($row["ECCHANGE_RATE"]*$row["DYEING_CHARGE"]),6,'.','');
}

//echo "<pre>";print_r($booking_data);die;

$sql_rcv="select b.ID, b.PI_WO_BATCH_NO, b.DYE_CHARGE, b.ORDER_QNTY, b.ORDER_RATE, b.ORDER_AMOUNT, b.CONS_QUANTITY, b.CONS_RATE, b.CONS_AMOUNT, c.ID as PROD_ID, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST as PROD_YARN_COMP_TYPE1ST, c.YARN_TYPE as PROD_YARN_TYPE, c.COLOR AS PROD_COLOR, c.LOT, b.CONS_AVG_RATE 
from inv_receive_master a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c
where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 and a.entry_form=1 and b.ITEM_CATEGORY = 1 AND b.RECEIVE_BASIS = 2  AND b.TRANSACTION_TYPE = 1 AND b.DYE_CHARGE > 0 and b.status_active=1";
$sql_rcv_result=sql_select($sql_rcv);
//echo count($sql_rcv_result);die;
$upTrans=true;$up_prod_id_arr=array();
foreach($sql_rcv_result as $row)
{
	$grey_rate=$row["CONS_AVG_RATE"];
	$dying_charge=$booking_data[$row["PI_WO_BATCH_NO"]][$row["YARN_COUNT_ID"]][$row["PROD_YARN_COMP_TYPE1ST"]][$row["PROD_YARN_TYPE"]][$row["PROD_COLOR"]];
	//echo $dying_charge."=".$row["PI_WO_BATCH_NO"]."=".$row["YARN_COUNT_ID"]."=".$row["PROD_YARN_COMP_TYPE1ST"]."=".$row["PROD_YARN_TYPE"]."=".$row["PROD_COLOR"];die;
	if($dying_charge>0)
	{
		$cons_rate=number_format(($grey_rate+$dying_charge),6,'.','');
		$ord_amt=number_format(($row["ORDER_QNTY"]*$cons_rate),8,'.','');
		$cons_amt=number_format(($row["CONS_QUANTITY"]*$cons_rate),8,'.','');
		$upTrans=execute_query("update INV_TRANSACTION set ORDER_RATE='".$cons_rate."', ORDER_AMOUNT='".$ord_amt."',  CONS_RATE='".$cons_rate."', CONS_AMOUNT='".$cons_amt."' , DYE_CHARGE='".$dying_charge."' where id=".$row["ID"]." ");
		if($upTrans==false)
		{echo "update INV_TRANSACTION set ORDER_RATE='".$cons_rate."', ORDER_AMOUNT='".$ord_amt."',  CONS_RATE='".$cons_rate."', CONS_AMOUNT='".$cons_amt."' where id=".$row["ID"]." ";oci_rollback($con);die;}
		$up_prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
	}
}

if($upTrans)
{
	oci_commit($con); 
	echo "Trans Data Update Successfully. <br>".implode(",",$up_prod_id_arr);die;
}
else
{
	oci_rollback($con);
	echo "Trans Data Update Failed. <br>";
	die;
}

die;
*/
//echo "<pre>".count($conversion_data_arr[1]['15-10-2022'])."=";print_r($conversion_data_arr[1]['15-10-2022']);die; 

//and b.TRANSACTION_DATE>'31-Dec-2021'


//############# for Yarn synchronization   ############# /////

/*$sql_rcv="select a.ID as MST_ID, a.EXCHANGE_RATE, a.COMPANY_ID, a.RECV_NUMBER, b.PROD_ID, b.ID as TRANS_ID, b.TRANSACTION_DATE, b.ORDER_QNTY, b.ORDER_RATE, b.ORDER_AMOUNT, b.CONS_QUANTITY, b.CONS_RATE, b.CONS_AMOUNT 
from INV_RECEIVE_MASTER a, inv_transaction b 
where a.id=b.mst_id and a.ENTRY_FORM = 1 and a.CURRENCY_ID=2 and b.transaction_type=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.EXCHANGE_RATE < 85
order by b.ID";
//echo $sql_dyes_trans;die;
$sql_rcv_result=sql_select($sql_rcv);
//echo count($sql_rcv_result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upRcv=$upTrans=true;$change_prod_id=array();
foreach($sql_rcv_result as $row)
{
	$lib_exchange_rate=$conversion_data_arr[$row["COMPANY_ID"]][change_date_format($row["TRANSACTION_DATE"])];
	$entry_exchange_rate=$row["EXCHANGE_RATE"];
	if($lib_exchange_rate!=$entry_exchange_rate && $lib_exchange_rate>1)
	{
		$change_prod_id[$row["PROD_ID"]]=$row["PROD_ID"];
		$change_mst_id[$row["MST_ID"]]=$row["MST_ID"];
		$test_data[$row["TRANS_ID"]]=$lib_exchange_rate."=".$entry_exchange_rate."=".$row["RECV_NUMBER"];
		$upRcv=execute_query("update INV_RECEIVE_MASTER set EXCHANGE_RATE='".$lib_exchange_rate."' where id=".$row["MST_ID"]." ");
		if($upRcv==false){echo"update INV_RECEIVE_MASTER set EXCHANGE_RATE='".$lib_exchange_rate."' where id=".$row["MST_ID"]."";oci_rollback($con);die;}
		$cons_amount=number_format(($row["ORDER_AMOUNT"]*$lib_exchange_rate),8,'.','');
		$cons_rate=number_format(($cons_amount/$row["CONS_QUANTITY"]),8,'.','');
		$upTrans=execute_query("update INV_TRANSACTION set CONS_RATE='".$cons_rate."', cons_amount='".$cons_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTrans==false)
		{echo"update INV_TRANSACTION set CONS_RATE='".$cons_rate."', cons_amount='".$cons_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
	}
}
//echo "<pre>".count($change_mst_id)."=";print_r($test_data);die;

if($upRcv && $upTrans)
{
	oci_commit($con); 
	echo "Rcv Data Update Successfully. <br>";echo implode(",",$change_prod_id)."<br>";
}
else
{
	oci_rollback($con);
	echo "Rcv Data Update Failed. <br>";
	die;
}


if(count($change_prod_id)>0)
{
	$prod_id_cond="";
	$change_prod_id_arr=array_chunk($change_prod_id,999);
	$p=1;
	foreach($change_prod_id_arr as $prod_id)
	{
		if($p==1) $prod_id_cond .=" and (b.prod_id in(".implode(',',$prod_id).")"; else $prod_id_cond .=" or id in(".implode(',',$prod_id).")";
		$p++;
	}
	$prod_id_cond .=" )";
	
	$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
	from inv_transaction b where b.status_active=1 and b.is_deleted=0 $prod_id_cond
	order by b.PROD_ID, b.ID";
	//echo $sql_dyes_trans;die;
	$result=sql_select($sql_dyes_trans);
	//echo count($result);die;
	$i=1;$k=1;
	$upTransID=true;
	foreach($result as $row)
	{
		if($prod_check[$row["PROD_ID"]]=="")
		{
			$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
			$rcv_data[$row["PROD_ID"]]["qnty"]=0;
			$rcv_data[$row["PROD_ID"]]["amt"]=0;
			$runtime_rate=0;
		}
		
		if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
		{
			if($row["TRANSACTION_TYPE"]==4)
			{
				//$runtime_rate=0;
				if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],8,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],8,'.','') > 0)
				{
					$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
				}
				$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
				
				$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
				if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				$rcv_data[$row["PROD_ID"]]["qnty"] += $row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"] += $issue_amount;
			}
			else
			{
				$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
			}
			
			$k=0;
		}
		else
		{
			if($k==0)
			{
				if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],8,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],8,'.','') > 0)
				{
					$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
				}
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
			
			$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
			$k++;
		}
	}
	
	// ##### difine Porduct ID Product Part update
	$upProdID=true;
	foreach($rcv_data as $prod_id=>$prod_val)
	{
		$prod_agv_rate=0;
		if(number_format($prod_val["qnty"],8,'.','')>0 && number_format($prod_val["amt"],8,'.','')>0) 
		{
			$prod_agv_rate=number_format($prod_val["amt"],8,'.','')/number_format($prod_val["qnty"],8,'.','');
		}
		$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
		if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
	}
	
	
	if($db_type==2)
	{
		//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
		if($upTransID && $upProdID)
		{
			oci_commit($con); 
			echo "Transaction Data Update Successfully. <br>";die;
		}
		else
		{
			oci_rollback($con);
			echo "Transaction Data Update Failed";
			die;
		}
	}
}*/



//############# for Trims synchronization   ############# /////



$sql_rcv="select a.ID as MST_ID, a.EXCHANGE_RATE, a.COMPANY_ID, a.RECV_NUMBER, b.PROD_ID, b.ID as TRANS_ID, b.TRANSACTION_DATE, b.ORDER_QNTY, b.ORDER_RATE, b.ORDER_AMOUNT, b.CONS_QUANTITY, b.CONS_RATE, b.CONS_AMOUNT 
from INV_RECEIVE_MASTER a, inv_transaction b 
where a.id=b.mst_id and a.ENTRY_FORM in(24) and a.CURRENCY_ID=2 and b.transaction_type=1 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.EXCHANGE_RATE < 85
order by b.ID";
//echo $sql_dyes_trans;die;
$sql_rcv_result=sql_select($sql_rcv);
//echo count($sql_rcv_result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upRcv=$upTrans=true;$change_prod_id=array();
foreach($sql_rcv_result as $row)
{
	$lib_exchange_rate=$conversion_data_arr[$row["COMPANY_ID"]][change_date_format($row["TRANSACTION_DATE"])];
	$entry_exchange_rate=$row["EXCHANGE_RATE"];
	if($lib_exchange_rate!=$entry_exchange_rate && $lib_exchange_rate>1)
	{
		$change_prod_id[$row["PROD_ID"]]=$row["PROD_ID"];
		$change_mst_id[$row["MST_ID"]]=$row["MST_ID"];
		$test_data[$row["TRANS_ID"]]=$lib_exchange_rate."=".$entry_exchange_rate."=".$row["RECV_NUMBER"];
		$upRcv=execute_query("update INV_RECEIVE_MASTER set EXCHANGE_RATE='".$lib_exchange_rate."' where id=".$row["MST_ID"]." ");
		if($upRcv==false){echo"update INV_RECEIVE_MASTER set EXCHANGE_RATE='".$lib_exchange_rate."' where id=".$row["MST_ID"]."";oci_rollback($con);die;}
		$cons_amount=number_format(($row["ORDER_AMOUNT"]*$lib_exchange_rate),8,'.','');
		$cons_rate=number_format(($cons_amount/$row["CONS_QUANTITY"]),8,'.','');
		$upTrans=execute_query("update INV_TRANSACTION set CONS_RATE='".$cons_rate."', cons_amount='".$cons_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTrans==false)
		{echo"update INV_TRANSACTION set CONS_RATE='".$cons_rate."', cons_amount='".$cons_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
		
		$upDtls=execute_query("update INV_TRIMS_ENTRY_DTLS set CONS_RATE='".$cons_rate."', BOOK_KEEPING_CURR='".$cons_amount."' where TRANS_ID=".$row["TRANS_ID"]." ");
		if($upDtls==false)
		{echo"update INV_TRIMS_ENTRY_DTLS set CONS_RATE='".$cons_rate."', BOOK_KEEPING_CURR='".$cons_amount."' where TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
	}
}
//echo "<pre>".count($change_prod_id)."=";print_r($test_data);die;

if($upRcv && $upTrans && $upDtls)
{
	oci_commit($con); 
	echo "Rcv Data Update Successfully. <br>";echo implode(",",$change_prod_id)."<br>";
}
else
{
	oci_rollback($con);
	echo "Rcv Data Update Failed. <br>";
	die;
}


if(count($change_prod_id)>0)
{
	//$prod_id_cond="";
//	$change_prod_id_arr=array_chunk($change_prod_id,999);
//	$p=1;
//	foreach($change_prod_id_arr as $prod_id)
//	{
//		if($p==1) $prod_id_cond .=" and (b.prod_id in(".implode(',',$prod_id).")"; else $prod_id_cond .=" or id in(".implode(',',$prod_id).")";
//		$p++;
//	}
//	$prod_id_cond .=" )";
	
	$data_array=sql_select("select b.ID, a.CONVERSION_FACTOR from lib_item_group a, product_details_master b where a.id=b.item_group_id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and b.status_active=1 and b.id in(".implode(",",$change_prod_id).")");
	$conversion_factor_arr =array();
	foreach($data_array as $row)
	{
		$conversion_factor_arr[$row["ID"]]=$row["CONVERSION_FACTOR"];
	}
	unset($data_array);
	
	$rcv_issue_rtn_sql="select TRANS_ID, ORDER_ID from INV_TRIMS_ENTRY_DTLS where status_active=1 and prod_id in(".implode(",",$change_prod_id).")";
	$rcv_issue_rtn_result=sql_select($rcv_issue_rtn_sql);
	$rcv_issue_rtn_data=array();
	foreach($rcv_issue_rtn_result as $val)
	{
		$rcv_issue_rtn_data[$val["TRANS_ID"]]=$val["ORDER_ID"];
	}
	unset($rcv_issue_rtn_result);
	
	$issue_rcv_rtn_sql="select TRANS_ID, ORDER_ID from INV_TRIMS_ISSUE_DTLS where status_active=1 and prod_id in(".implode(",",$change_prod_id).")";
	$issue_rcv_rtn_result=sql_select($issue_rcv_rtn_sql);
	foreach($issue_rcv_rtn_result as $val)
	{
		$rcv_issue_rtn_data[$val["TRANS_ID"]]=$val["ORDER_ID"];
	}
	unset($issue_rcv_rtn_result);
	
	$sql_order_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE, a.ID as PROP_ID, a.PO_BREAKDOWN_ID, a.QUANTITY, a.ORDER_RATE, a.ORDER_AMOUNT, b.STORE_ID 
	from ORDER_WISE_PRO_DETAILS a, inv_transaction b, PRODUCT_DETAILS_MASTER c 
	where a.trans_id=b.id and a.prod_id=b.prod_id and b.prod_id=c.id and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.prod_id in(".implode(",",$change_prod_id).")
	order by b.PROD_ID, b.STORE_ID, b.ID";
	$order_trans_result=sql_select($sql_order_trans);
	//echo count($order_trans_result);die;
	$i=1;$k=1;
	$prod_ord_data=$rcv_data=array();
	$upTransID=$upTransID=true;
	foreach($order_trans_result as $row)
	{
		if($prod_check[$row["PROD_ID"]]=="")
		{
			$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
			$rcv_data[$row["PROD_ID"]]["qnty"]=0;
			$rcv_data[$row["PROD_ID"]]["amt"]=0;
			$runtime_rate=0;$runtime_ord_rate=0;
		}
		
		if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
		{
			if($trans_id_check[$row["TRANS_ID"]]=="")
			{
				$trans_id_check[$row["TRANS_ID"]]=$row["TRANS_ID"];
				if($row["TRANSACTION_TYPE"]==4)
				{
					$rcv_rtn_ord_id_arr=explode(",",$rcv_issue_rtn_data[$row["TRANS_ID"]]);
					$runtime_qnty=$runtime_amt=0;
					foreach($rcv_rtn_ord_id_arr as $ord_id)
					{
						$runtime_qnty+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["qnty"];
						$runtime_amt+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["amt"];
					}
					
					if(number_format($runtime_qnty,8,'.','') > 0 && number_format($runtime_amt,8,'.','') > 0)
					{
						$runtime_rate=number_format(($runtime_amt/$runtime_qnty),8,'.','');
					}
					$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
					$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
					
					$upIssID=execute_query("update INV_TRIMS_ENTRY_DTLS set CONS_RATE='".$runtime_rate."', BOOK_KEEPING_CURR='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]." ");
					if($upIssID){ $upIssID=1; } else {echo"update INV_TRIMS_ENTRY_DTLS set CONS_RATE='".$runtime_rate."', BOOK_KEEPING_CURR='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
					
					$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"]+=$issue_amount;
					
					
					$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
					$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
				}
				else
				{
					$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
					
					
					$cons_rate=$row["CONS_AMOUNT"]/$row["CONS_QUANTITY"];
					$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
					$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$cons_rate);
				}
			}
			else
			{
				$cons_rate=$row["CONS_AMOUNT"]/$row["CONS_QUANTITY"];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$cons_rate);
			}
			
			$k=0;
		}
		else
		{
			if($trans_id_check[$row["TRANS_ID"]]=="")
			{
				$trans_id_check[$row["TRANS_ID"]]=$row["TRANS_ID"];
				
				$rcv_rtn_ord_id_arr=explode(",",$rcv_issue_rtn_data[$row["TRANS_ID"]]);
				$runtime_qnty=$runtime_amt=0;
				foreach($rcv_rtn_ord_id_arr as $ord_id)
				{
					$runtime_qnty+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["qnty"];
					$runtime_amt+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["amt"];
				}
				
				if(number_format($runtime_qnty,8,'.','') > 0 && number_format($runtime_amt,8,'.','') > 0)
				{
					$runtime_rate=number_format(($runtime_amt/$runtime_qnty),8,'.','');
				}
				
				$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
				
				
				
				if($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3)
				{
					$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
					
					$upIssID=execute_query("update INV_TRIMS_ISSUE_DTLS set RATE='".$runtime_rate."', AMOUNT='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]." ");
					if($upIssID){ $upIssID=1; } else {echo"update INV_TRIMS_ISSUE_DTLS set RATE='".$runtime_rate."', AMOUNT='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				}
				else
				{
					$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				}
				
				$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
				
				
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]-=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]-=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
				
				$k++;
			}
			else
			{
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]-=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]-=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
			}
		}
	}
	
	/* ##### difine Porduct ID Product Part update  */
	$upProdID=true;
	foreach($rcv_data as $prod_id=>$prod_val)
	
	{
		$prod_agv_rate=0;
		if(number_format($prod_val["qnty"],8,'.','')>0 && number_format($prod_val["amt"],8,'.','')>0) 
		{
			$prod_agv_rate=number_format($prod_val["amt"],8,'.','')/number_format($prod_val["qnty"],8,'.','');
		}
		$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
		if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
	}
	
	
	if($db_type==2)
	{
		//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
		if($upTransID && $upProdID && $upIssID)
		{
			oci_commit($con); 
			echo "Transaction Data Update Successfully. <br>";die;
		}
		else
		{
			oci_rollback($con);
			echo "Transaction Data Update Failed";
			die;
		}
	}
}


?>