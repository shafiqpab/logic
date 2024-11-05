<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_roll_sql=" select a.po_breakdown_id , b.po_breakdown_id as roll_po, b.insert_date ,a.id
 from order_wise_pro_details a, pro_roll_details b
 where a.dtls_id = b.dtls_id and a.entry_form = 61 and b.entry_form =61 and a.status_active=1 and b.status_active=1 
 and a.po_breakdown_id!= b.po_breakdown_id ";

$mismatch_roll_data = sql_select($mismatch_roll_sql);
if(empty($mismatch_roll_data))
{
	echo "No Mismatch Found"; die;
}
$i=1;
foreach ($mismatch_roll_data as $row) 
{
	echo "$i . UPDATE order_wise_pro_details set po_breakdown_id=".$row[csf('roll_po')]." where id=".$row[csf('id')] ."<br />";
	$i++;
	//$update_allocation_dtls=execute_query("UPDATE pro_roll_details set po_breakdown_id=$transfer_po,updated_by=999 where id=$roll_id");
}


//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con);
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>