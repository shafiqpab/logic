<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$transfer_roll_sql="select barcode_no,entry_form,po_breakdown_id from pro_roll_details where po_breakdown_id=0 and entry_form=82 and status_active=1";
$transfer_roll_data = sql_select($transfer_roll_sql);
$transfered_barcode_po=array();
foreach ($transfer_roll_data as $row) {
	$roll_barcodes[$row[csf("barcode_no")]] = $row[csf("barcode_no")];	
}

$all_barcodes=array_unique($roll_barcodes);
print_r($all_barcodes);die;
if($db_type==2 && count($all_barcodes)>999)
{
	$barcode_cond=" and (";
	$barcodeArr=array_chunk($all_barcodes,999);
	foreach($barcodeArr as $ids)
	{
		$ids=rtrim(implode(",",$ids),", ");
		$barcode_cond.=" barcode_no in($ids) or"; 
	}
	$barcode_cond=chop($barcode_cond,'or ');
	$barcode_cond.=")";
}
else
{
	$barcodes=rtrim(implode(",",$all_barcodes),", ");
	$barcode_cond=" and barcode_no in($barcodes)";
}

if(!empty($all_barcodes)){

}

echo $receive_barcode_sql="select barcode_no,entry_form,po_breakdown_id from pro_roll_details where entry_form=58 and status_active=1 $barcode_cond";die;
$mismatch_batch_roll_data = sql_select($receive_barcode_sql);
foreach ($mismatch_batch_roll_data as $row) {
	$dtls_id = $row[csf("id")];
	$po_breakdown_id = $row[csf("po_breakdown_id")];
	//echo "UPDATE pro_roll_details set po_breakdown_id_bk=$transfer_po where id=$roll_id <br />";
	//$update_allocation_dtls=execute_query("UPDATE pro_batch_create_dtls set po_id=$po_breakdown_id where id=$dtls_id");
}
//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con); 
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>