<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("SELECT a.id as mst_id,  b.id as dtls_id, c.id as trans_id, d.booking_no, c.cons_quantity, c.order_rate, c.cons_rate, e.currency_id, e.exchange_rate from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c, inv_issue_master d, wo_non_ord_samp_booking_mst e where a.entry_form=37 and a.receive_basis=10 and a.booking_without_order=1 and a.currency_id=0 and c.order_rate !=0 and a.id=b.mst_id and b.trans_id= c.id and c.transaction_type=1 and c.item_category=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no= d.issue_number and d.booking_no=e.booking_no and a.is_posted_account=0");

//and c.id= a.sales_order_id 

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($mis_match_sql as  $row) 
{
	$cons_rate 		= $row[csf("exchange_rate")]*$row[csf("order_rate")];
	$cons_amount 	= $row[csf("cons_quantity")]*$cons_rate;
	$order_amount 	= $row[csf("cons_quantity")]*$row[csf("order_rate")];

	//echo "update inv_receive_master set currency_id = '".$row[csf("currency_id")]. "', exchange_rate = '".$row[csf("exchange_rate")]. "' where id = ".$row[csf("mst_id")]." <br>";
	//echo "update pro_finish_fabric_rcv_dtls set rate = '".$cons_rate. "', amount = '".$cons_amount. "' where id = ".$row[csf("dtls_id")]." <br>";
	//echo "update inv_transaction set cons_rate = '".$cons_rate. "', cons_amount = '".$cons_amount. "', order_rate = '".$row[csf("order_rate")]. "', order_amount = '".$order_amount. "' where id = ".$row[csf("trans_id")]." <br>";
	
	execute_query("update inv_receive_master set currency_id = '".$row[csf("currency_id")]. "', exchange_rate = '".$row[csf("exchange_rate")]. "' where id = ".$row[csf("mst_id")],0);

	execute_query("update pro_finish_fabric_rcv_dtls set rate = '".$cons_rate. "', amount = '".$cons_amount. "' where id = ".$row[csf("dtls_id")],0);

	execute_query("update inv_transaction set cons_rate = '".$cons_rate. "', cons_amount = '".$cons_amount. "', order_rate = '".$row[csf("order_rate")]. "', order_amount = '".$order_amount. "' where id = ".$row[csf("trans_id")],0);
}

oci_commit($con);
echo "Success";
disconnect($con);
die;


?>