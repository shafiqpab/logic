<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$duplicate_receive_by_batch_sql="select c.barcode_no,count(*) total_dup,
								min(b.id)min_dtls_id,listagg(b.id, ',') within group (order by a.id asc) as dtls_ids,
								min(c.id)min_roll_id,listagg(c.id, ',') within group (order by b.id asc) as roll_ids
								from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c 
								where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62
								and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 
								and c.is_deleted=0
								group by c.barcode_no
								having (count(*) > 1);";

$duplicate_receive_by_batch_data=sql_select($duplicate_receive_by_batch_sql);
$issue_arr=array();
foreach ($duplicate_receive_by_batch_data as $row) {
	$active_issue_id = $row[csf("min_issue_dtls_id")];
	$delete_issue_id = $row[csf("issue_dtls_ids")];

	$active_trans_id = $row[csf("min_trans_id")];
	$delete_trans_ids = $row[csf("trans_ids")];

	$active_roll_id = $row[csf("min_roll_id")];
	$delete_roll_id = $row[csf("max_dup_roll_ids")];

	$prod_id = $row[csf("prod_id")];
	$current_stock = $row[csf("total_issue_qnty")]-$row[csf("min_issue_qnty")];

	$inv_grey_fabric_issue_dtls=execute_query("update inv_grey_fabric_issue_dtls set status_active=0,is_deleted=1,updated_by=9999 where id in($delete_issue_id) and id !=$active_issue_id and prod_id=$prod_id");
	$pro_roll_details=execute_query("update pro_roll_details set status_active=0,is_deleted=1,updated_by=9999 where id in($delete_roll_id) and id !=$active_roll_id and entry_form=61");
	$product_details_master=execute_query("update product_details_master set current_stock=(current_stock+$current_stock),updated_by=9999 where id=$prod_id and item_category_id=13");
	$order_wise_pro_details=execute_query("update order_wise_pro_details set status_active=0,is_deleted=1,updated_by=9999 where dtls_id in($delete_issue_id) and dtls_id !=$active_issue_id and prod_id=$prod_id and entry_form=61 and trans_type=2");
	$inv_transaction=execute_query("update inv_transaction set status_active=0,is_deleted=1,updated_by=9999 where id in($delete_trans_ids) and id !=$active_trans_id and prod_id=$prod_id and transaction_type=2");
}
oci_commit($con); 
echo "Success";
die;
?>