<?
include('../includes/common.php');
$con = connect();
$sql_trans="Select prod_id, store_id, batch_lot, min(transaction_date) as first_receive, max(transaction_date) as last_receive
 from inv_transaction where status_active=1 and is_deleted=0 and transaction_type in (1,4,5) and item_category in(5,6,7,23) group by prod_id, store_id, batch_lot";
 //echo $sql_trans;die;
$sql_trans_result=sql_select($sql_trans);
$chemical_dyes_arr=array();
foreach($sql_trans_result as $row)
{
	$chemical_dyes_arr[$row[csf("prod_id")]][$row[csf("store_id")]][trim($row[csf("batch_lot")])]["first_receive"]=change_date_format($row[csf("first_receive")],'','',1);
	$chemical_dyes_arr[$row[csf("prod_id")]][$row[csf("store_id")]][trim($row[csf("batch_lot")])]["last_receive"]=change_date_format($row[csf("last_receive")],'','',1);
}

//echo "<pre>";print_r($chemical_dyes_arr);die;

$store_sql=sql_select("select id, prod_id, store_id, lot, first_receive_date, last_receive_date from inv_store_wise_qty_dtls");
$upStoreProdId=true;
foreach($store_sql as $val)
{
	$trans_first_rcv_date=$chemical_dyes_arr[$val[csf("prod_id")]][$val[csf("store_id")]][trim($val[csf("lot")])]["first_receive"];
	$trans_last_rcv_date=$chemical_dyes_arr[$val[csf("prod_id")]][$val[csf("store_id")]][trim($val[csf("lot")])]["last_receive"];
	$store_tbl_id=$val[csf("id")];
	//echo "update inv_store_wise_qty_dtls set first_receive_date='$trans_first_rcv_date', last_receive_date='$trans_last_rcv_date' where id=$store_tbl_id";oci_rollback($con);die;
	$upStoreProdId=execute_query("update inv_store_wise_qty_dtls set first_receive_date='$trans_first_rcv_date', last_receive_date='$trans_last_rcv_date' where id=$store_tbl_id");
	if($upStoreProdId)
	{
		$upStoreProdId=1;
	}
	else
	{
		$upStoreProdId=0;
		echo "update inv_store_wise_qty_dtls set first_receive_date='$trans_first_rcv_date', last_receive_date='$trans_last_rcv_date' where id=$store_tbl_id";oci_rollback($con);die;
	}
}

if($db_type==2)
{
	if($upStoreProdId)
	{
		oci_commit($con); 
		echo "Store Data Update Successfully. <br>";
	}
	else
	{
		oci_rollback($con);
		echo "Store Data Update Failed";
		die;
	}
}
die;
?>