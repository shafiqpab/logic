<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("select a.booking_no, a.id, b.id as book_id , b.currency_id, b.exchange_rate
from inv_receive_master a, wo_non_ord_knitdye_booking_mst b
where a.booking_no = b.booking_no and a.entry_form = 37 and a.receive_basis = 11 and a.booking_without_order = 1 and a.status_active = 1");

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}


$batch_sql = sql_select("select a.booking_no, a.id as  book_id, b.id as batch_id, entry_form
from wo_non_ord_knitdye_booking_mst a, pro_batch_create_mst b
where a.booking_no = b.booking_no and b.status_active =1 and b.entry_form = 37 and a.id != b.booking_no_id");

foreach ($batch_sql as $row ) 
{
	//echo "update pro_batch_create_mst set booking_no_id = '".$row[csf("book_id")]. "', updated_by = 666 where id = ".$row[csf("batch_id")]." <br>"; 
	execute_query("update pro_batch_create_mst set booking_no_id = '".$row[csf("book_id")]. "', updated_by = 666 where id = ".$row[csf("batch_id")],0);
}


foreach($mis_match_sql as $val)
{
	//echo "update inv_receive_master set booking_id = '".$val[csf("book_id")]. "', currency_id = '".$val[csf("currency_id")]."', exchange_rate ='".$val[csf("exchange_rate")]."', updated_by = 666 where id = ".$val[csf("id")]." <br>";

	execute_query("update inv_receive_master set booking_id = '".$val[csf("book_id")]. "', currency_id = '".$val[csf("currency_id")]."', exchange_rate ='".$val[csf("exchange_rate")]."', updated_by = 666 where id = ".$val[csf("id")],0);
}

oci_commit($con);
echo "Success"; 
die;


?>