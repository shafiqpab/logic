<?php
include('../includes/common.php');
error_reporting(1);
$con = connect();
$sql = "select a.prod_id,
--sum(case when  a.transaction_type in (1,4,5) then cons_quantity else 0 end) rcv,sum(case when  a.transaction_type in (2,3,6) then cons_quantity else 0 end) iss,
sum(case when  a.transaction_type in (1,4,5) then cons_quantity else 0 end) - sum(case when  a.transaction_type in (2,3,6) then cons_quantity else 0 end) QNTY_BAL,
sum(case when  a.transaction_type in (1,4,5) then cons_amount else 0 end) - sum(case when  a.transaction_type in (2,3,6) then cons_amount else 0 end) amount_bal
from inv_transaction a
where a.status_active=1 and a.item_category=13
group by a.prod_id"; 
$sl_res = sql_select($sql);
$trans_id_array = array();
if(empty($sl_res))
{
	echo "Data not found";
	disconnect($con);

	die();
}
foreach ($sl_res as $val) 
{
	$rate=0;

	if($val['QNTY_BAL']>0)
	{
		$rate = ($val['AMOUNT_BAL']/$val['QNTY_BAL']);
	}
	echo "update product_details_master set current_stock = ".$val['QNTY_BAL'].", stock_value=".$val['AMOUNT_BAL'].", avg_rate_per_unit=".$rate." where id=".$val['ID']."<br>";
	
	//execute_query("update product_details_master set current_stock = ".$val['QNTY_BAL'].", stock_value=".$val['AMOUNT_BAL'].", avg_rate_per_unit=".$rate." where id=".$val['ID'],0);
}

/* oci_commit($con);  
echo "Success";
disconnect($con); */

die();

?>