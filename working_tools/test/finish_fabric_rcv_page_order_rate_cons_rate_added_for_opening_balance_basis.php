<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("SELECT a.id as mst_id,  b.id as dtls_id, c.id as trans_id, a.currency_id, a.exchange_rate,  c.cons_quantity, c.order_rate, c.order_amount, c.cons_rate, c.cons_amount from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c where a.entry_form=37 and a.receive_basis=6  and c.order_amount != 0 and c.order_rate=0 and a.id=b.mst_id and b.trans_id= c.id and c.transaction_type=1 and c.item_category=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_posted_account=0 order by a.id asc");

//and c.id= a.sales_order_id 

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($mis_match_sql as  $row) 
{
	$order_amount 	= number_format($row[csf("order_amount")],4,".","");
	$order_rate 	= $order_amount/$row[csf("cons_quantity")];
	$order_rate 	= number_format($order_rate,4,".","");

	$cons_rate 		= $row[csf("exchange_rate")]*$order_rate;
	$cons_rate 		= number_format($cons_rate,4,".","");

	$cons_amount 	= $row[csf("cons_quantity")]*$cons_rate;
	$cons_amount 	= number_format($cons_amount,4,".","");
	

	//echo "update inv_receive_master set currency_id = '".$row[csf("currency_id")]. "', exchange_rate = '".$row[csf("exchange_rate")]. "' where id = ".$row[csf("mst_id")]." <br>";
	//echo "update pro_finish_fabric_rcv_dtls set rate = '".$cons_rate. "', amount = '".$cons_amount. "' where id = ".$row[csf("dtls_id")]." <br>";
	//echo "update inv_transaction set cons_rate = '".$cons_rate. "', cons_amount = '".$cons_amount. "', order_rate = '".$order_rate. "', order_amount = '".$row[csf("order_amount")]. "', reason_of_change='opening basis rate added from amount' where id = ".$row[csf("trans_id")]." <br>";
	
	//execute_query("update inv_receive_master set currency_id = '".$row[csf("currency_id")]. "', exchange_rate = '".$row[csf("exchange_rate")]. "' where id = ".$row[csf("mst_id")],0);

	execute_query("update pro_finish_fabric_rcv_dtls set rate = '".$cons_rate. "', amount = '".$cons_amount. "' where id = ".$row[csf("dtls_id")],0);

	execute_query("update inv_transaction set cons_rate = '".$cons_rate. "', cons_amount = '".$cons_amount. "', order_rate = '".$order_rate. "', order_amount = '".$order_amount. "', reason_of_change='opening basis rate added from amount' where id = ".$row[csf("trans_id")],0);
}

oci_commit($con);
echo "Success";
disconnect($con);
die;


?>