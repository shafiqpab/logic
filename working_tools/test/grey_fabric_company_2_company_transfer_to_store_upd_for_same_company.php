<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$barcode_data=sql_select("select b.id as dtls_id, a.transfer_criteria, c.barcode_no,b.from_store, b.to_store , b.to_trans_id
from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 
and c.booking_without_order =0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.re_transfer=0 and (b.to_store = 0 or b.to_store is null) and a.transfer_criteria=1 and a.company_id = a.to_company");

foreach ($barcode_data as $row)
{
	$from_store_id = $row[csf("from_store")];
	//echo "update inv_item_transfer_dtls set to_store='$from_store_id' where id = ".$row[csf("dtls_id")] ."<br>";
	//echo "update inv_transaction set store_id = '$from_store_id' where id = ".$row[csf("to_trans_id")]."<br>";

	execute_query("update inv_item_transfer_dtls set to_store='$from_store_id' where id = ".$row[csf("dtls_id")],0);
	execute_query("update inv_transaction set store_id = '$from_store_id' where id = ".$row[csf("to_trans_id")],0);
}


oci_commit($con);
echo "Success";
die;
?>