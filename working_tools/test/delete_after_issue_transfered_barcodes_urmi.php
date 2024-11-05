<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$transfered_barcodes_after_batch_sql="select a.id transfer_roll_id,b.id batch_roll_id,a.entry_form,a.barcode_no,b.entry_form batch_entry_form,a.qnty,c.id dtls_id,c.trans_id,c.to_trans_id
from pro_roll_details a
left join pro_roll_details b on b.barcode_no=a.barcode_no and b.entry_form=61 and b.status_active=1,inv_item_transfer_dtls c
where a.dtls_id=c.id and a.entry_form=133 and a.status_active=1 and c.status_active=1
and a.id > b.id";
$transfered_barcodes_after_batch = sql_select($transfered_barcodes_after_batch_sql);

$transfered_barcode_po=array();
$i=1;
foreach ($transfered_barcodes_after_batch as $row) {
	$transfer_roll_id = $row[csf("transfer_roll_id")];
	$transfer_dtls_id = $row[csf("dtls_id")];
	$from_trans_id 	  = $row[csf("trans_id")];
	$to_trans_id 	  = $row[csf("to_trans_id")];

	echo "UPDATE pro_roll_details set status_active=0,is_deleted=1,updated_by=333 where id=$transfer_roll_id; <br />";
	//echo "$i UPDATE inv_item_transfer_dtls set status_active=0,is_deleted=1,updated_by=333 where id=$transfer_dtls_id <br />";
	/*echo "UPDATE inv_transaction set status_active=0,is_deleted=1,updated_by=333 where id=$from_trans_id <br />";
	echo "UPDATE inv_transaction set status_active=0,is_deleted=1,updated_by=333 where id=$to_trans_id <br />";
	echo "UPDATE order_wise_pro_details set status_active=0,is_deleted=1,updated_by=333 where trans_id=$from_trans_id <br />";
	echo "UPDATE order_wise_pro_details set status_active=0,is_deleted=1,updated_by=333 where trans_id=$to_trans_id <br />";*/
/*
	$update_allocation_dtls=execute_query("UPDATE pro_roll_details set status_active=0,is_deleted=1,updated_by=333 where id=$transfer_roll_id");
	$update_allocation_dtls=execute_query("UPDATE inv_item_transfer_dtls set status_active=0,is_deleted=1,updated_by=333 where id=$transfer_dtls_id");
	$update_allocation_dtls=execute_query("UPDATE inv_transaction set status_active=0,is_deleted=1,updated_by=333 where id=$from_trans_id");
	$update_allocation_dtls=execute_query("UPDATE inv_transaction set status_active=0,is_deleted=1,updated_by=333 where id=$to_trans_id");
	$update_allocation_dtls=execute_query("UPDATE order_wise_pro_details set status_active=0,is_deleted=1,updated_by=333 where trans_id=$from_trans_id");
	$update_allocation_dtls=execute_query("UPDATE order_wise_pro_details set status_active=0,is_deleted=1,updated_by=333 where trans_id=$to_trans_id");
*/
	$i++;
}
//echo $i;
//echo "Success";
die;
?>