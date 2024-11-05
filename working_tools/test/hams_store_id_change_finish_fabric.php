<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$receive_sql="select a.id,b.id trans_id,a.entry_form,a.recv_number,a.location_id,a.store_id,b.store_id from inv_receive_master a,inv_transaction b,pro_finish_fabric_rcv_dtls c
where a.id=b.mst_id and b.id=c.trans_id and a.item_category=2 and a.location_id=4 
and b.item_category=2
and a.status_active=1 and b.status_active=1 
and c.trans_id>0 and c.status_active=1";
$receive_data = sql_select($receive_sql);
$transfered_barcode_po=array();
foreach ($receive_data as $row) {
	$rec_id = $row[csf("id")];
	$trans_id = $row[csf("trans_id")];

	echo "UPDATE inv_receive_master set store_id=16 where id=$rec_id";
	echo "UPDATE inv_transaction set store_id=16 where id=$trans_id";
	//$update_allocation_dtls=execute_query("UPDATE pro_roll_details set po_breakdown_id=$transfer_po,updated_by=999 where id=$roll_id");
}

$receive_sql="select a.id,b.id trans_id,a.entry_form,a.recv_number,a.location_id,a.store_id,b.store_id from inv_receive_master a,inv_transaction b,pro_finish_fabric_rcv_dtls c
where a.id=b.mst_id and b.id=c.trans_id and a.item_category=2 and a.location_id=4 
and b.item_category=2
and a.status_active=1 and b.status_active=1 
and c.trans_id>0 and c.status_active=1";
$receive_data = sql_select($receive_sql);
$transfered_barcode_po=array();
foreach ($receive_data as $row) {
	$rec_id = $row[csf("id")];
	$trans_id = $row[csf("trans_id")];

	echo "UPDATE inv_receive_master set store_id=16 where id=$rec_id";
	echo "UPDATE inv_transaction set store_id=16 where id=$trans_id";
	//$update_allocation_dtls=execute_query("UPDATE pro_roll_details set po_breakdown_id=$transfer_po,updated_by=999 where id=$roll_id");
}

//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con);
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>