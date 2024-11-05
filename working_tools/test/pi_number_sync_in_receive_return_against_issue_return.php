<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$empty_pi_id_sql="select a.id,a.issue_number,a.received_id,a.pi_id,c.prod_id,b.issue_id,b.id receive_id, b.recv_number, b.entry_form,c.transaction_type from inv_issue_master a,inv_transaction c, inv_receive_master b where a.id=c.mst_id and a.received_id=b.id and a.entry_form=8 and (a.pi_id=0 or a.pi_id is null) and a.status_active=1 and b.entry_form=9 and a.item_category=1 and b.item_category=1 and b.issue_id is not null and b.status_active=1 and c.item_category=1 and c.transaction_type=3 group by a.id,a.issue_number,a.received_id,a.pi_id,c.prod_id,b.issue_id,b.id, b.recv_number, b.entry_form,c.transaction_type order by a.id desc";
$empty_pi_id_data = sql_select($empty_pi_id_sql);
$transfered_barcode_po=array();
foreach ($empty_pi_id_data as $row) {
	$issue_id[] = $row[csf("issue_id")];
}

$yarn_receive_sql = "select b.mst_id issue_id,b.prod_id,d.id,d.recv_number,c.pi_wo_batch_no,d.booking_id,a.entry_form
from inv_transaction b,inv_mrr_wise_issue_details a 
left join inv_transaction c on a.recv_trans_id=c.id and c.transaction_type=1 and c.status_active=1 and c.item_category=1
left join inv_receive_master d on c.mst_id=d.id and d.item_category=1 and d.entry_form=1 and d.receive_basis=1 and d.status_active=1
where b.id=a.issue_trans_id and b.mst_id in(".implode(",",array_unique($issue_id)).")
and a.status_active=1 and b.status_active=1 and b.transaction_type=2 and c.item_category=1 and a.entry_form=3 and c.pi_wo_batch_no  is not null and a.status_active=1 and b.status_active=1";
$yarn_receive_data=sql_select($yarn_receive_sql);
foreach ($yarn_receive_data as $yarn_receive_row) {
	$receive_arr[$yarn_receive_row[csf("issue_id")]][$yarn_receive_row[csf("prod_id")]] = $yarn_receive_row[csf("booking_id")];
}

foreach ($empty_pi_id_data as $row) {
	$pi_id = $receive_arr[$row[csf("issue_id")]][$row[csf("prod_id")]];
	$rec_return_id = $row[csf("id")];
	if($pi_id!=""){
		//echo "update inv_issue_master set pi_id=$pi_id,updated_by=999 where id=$rec_return_id <br />";
		//$update_allocation_dtls=execute_query("update inv_issue_master set pi_id=$pi_id,updated_by=999 where id=$rec_return_id");
	}
}
die;
//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con); 
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";

?>