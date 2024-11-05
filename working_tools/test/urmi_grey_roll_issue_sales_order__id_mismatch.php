<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_roll_sql="select a.id,a.mst_id,a.dtls_id,a.po_breakdown_id,b.po_breakdown_id receive_po,a.entry_form,a.roll_no,a.booking_no,a.receive_basis,a.is_sales ,a.barcode_no from pro_roll_details a inner join pro_roll_details b on a.roll_id=b.id where a.entry_form in(58,61,62,64) and a.is_sales =1 and a.is_transfer=0 and a.status_active=1 and a.is_deleted=0 and a.po_breakdown_id!=b.po_breakdown_id and a.is_transfer=0";
$mismatch_roll_data = sql_select($mismatch_roll_sql);

foreach ($mismatch_roll_data as $row) {
	$roll_id = $row[csf("id")];
	$po_breakdown_id = $row[csf("receive_po")];
	$inv_grey_fabric_issue_dtls=execute_query("update pro_roll_details set po_breakdown_id=$po_breakdown_id where id=$roll_id");
}
oci_commit($con); 
echo "Success";
die;
?>