<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$empty_program_sql="select a.id,a.po_id, a.barcode_no from pro_batch_create_dtls a,pro_roll_details b where a.id=b.dtls_id and (a.program_no=0 or a.program_no is null) and a.status_active=1 and b.entry_form=64 and b.status_active=1";
$empty_program_data = sql_select($empty_program_sql);
$transfered_barcode_po=array();
foreach ($empty_program_data as $row) {
	$barcode_no = $row[csf("barcode_no")];
	$po_id = $row[csf("po_id")];
	$id = $row[csf("id")];
	
	$get_issue_program_id = return_field_value("a.booking_no", "pro_batch_create_mst a,pro_batch_create_dtls b", "a.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id = $po_id and a.barcode_no in($barcode_no) order by a.barcode_no","a.booking_no");
	echo $barcode_no . "==" . $get_issue_program_id;

}

/*$yarn_receive_sql = "select b.mst_id issue_id,b.prod_id,d.id,d.recv_number,c.pi_wo_batch_no,d.booking_id,a.entry_form
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
		echo "update inv_issue_master set pi_id=$pi_id,updated_by=999 where id=$rec_return_id <br />";
		$update_allocation_dtls=execute_query("update inv_issue_master set pi_id=$pi_id,updated_by=999 where id=$rec_return_id");
	}
}
*/
//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con); 
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>