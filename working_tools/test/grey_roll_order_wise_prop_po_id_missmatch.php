<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_roll_sql="select c.id,d.barcode_no,c.po_breakdown_id,d.po_breakdown_id roll_po,d.qnty
from inv_issue_master a, inv_grey_fabric_issue_dtls b
left join order_wise_pro_details c on b.id=c.dtls_id and c.entry_form=61 and c.trans_type=2 and c.status_active=1 and c.is_deleted=0
left join pro_roll_details d on b.id=d.dtls_id and d.entry_form=61 and d.status_active=1 and d.is_deleted=0
where a.id=b.mst_id  and a.item_category=13 and a.entry_form=61 and a.issue_purpose=11
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
and d.is_returned<>1 and c.po_breakdown_id<>d.po_breakdown_id
group by c.id,d.barcode_no,c.po_breakdown_id,d.po_breakdown_id,d.qnty order by barcode_no";// and d.po_breakdown_id in(2726)
$mismatch_roll_data = sql_select($mismatch_roll_sql);
$transfered_barcode_po=array();
foreach ($mismatch_roll_data as $row) {
	$id = $row[csf("id")];
	$roll_po = $row[csf("roll_po")];
	//echo "UPDATE order_wise_pro_details set po_breakdown_id=$roll_po where id=$id <br />";
	//$update_allocation_dtls=execute_query("UPDATE order_wise_pro_details set po_breakdown_id=$roll_po where id=$id");
}

/*$mismatch_batch_roll_sql="select a.batch_against, a.batch_for, a.booking_no, a.re_dyeing_from, a.color_id, a.booking_without_order,a.is_sales, b.id, b.program_no,
b.po_id,c.po_breakdown_id, b.prod_id, b.item_description, b.body_part_id, b.width_dia_type, b.roll_no, b.roll_id, b.barcode_no, b.batch_qnty, b.po_batch_no
from pro_batch_create_mst a,pro_batch_create_dtls b,pro_roll_details c
where a.id=b.mst_id and b.barcode_no=c.barcode_no
and c.entry_form =64
and b.status_active=1 and b.is_deleted=0 and c.status_active=1
and b.po_id!=c.po_breakdown_id";
$mismatch_batch_roll_data = sql_select($mismatch_batch_roll_sql);
foreach ($mismatch_batch_roll_data as $row) {
	$dtls_id = $row[csf("id")];
	$po_breakdown_id = $row[csf("po_breakdown_id")];
	//echo "UPDATE pro_roll_details set po_breakdown_id_bk=$transfer_po where id=$roll_id <br />";
	//$update_allocation_dtls=execute_query("UPDATE pro_batch_create_dtls set po_id=$po_breakdown_id where id=$dtls_id");
}*/
//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con);
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>