<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$duplicate_roll_in_issue_sql="select a.barcode_no, count(*)total_dup, 
								min(a.mst_id) as min_issue_id,listagg(a.mst_id, ',') within group (order by a.id asc) as issue_master_ids,
								min(a.id) as min_roll_id,listagg(a.id, ',') within group (order by a.id asc) as max_dup_roll_ids,
								min(a.dtls_id)min_issue_dtls_id,listagg(a.dtls_id, ',') within group (order by a.id asc) as issue_dtls_ids,
								min(b.trans_id)min_trans_id,listagg(b.trans_id, ',') within group (order by a.id asc) as trans_ids,
								min(b.issue_qnty) min_issue_qnty,sum(b.issue_qnty) total_issue_qnty,
								b.prod_id,c.current_stock 
								from pro_roll_details a,inv_grey_fabric_issue_dtls b,product_details_master c
								where a.dtls_id=b.id and b.prod_id=c.id and a.entry_form=61 and c.item_category_id=13 -- and a.barcode_no=18020025656
								and a.status_active=1 and b.status_active=1 and c.status_active=1
								group by a.barcode_no,b.prod_id,c.current_stock
								having (count(*) > 1)";

$duplicate_roll_in_issue_data=sql_select($duplicate_roll_in_issue_sql);
$issue_arr=array();
foreach ($duplicate_roll_in_issue_data as $row) {
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