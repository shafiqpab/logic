<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$details_sql=sql_select("SELECT b.id, b.barcode_no, b.entry_form, a.po_breakdown_id as batch_po_breakdown_id, b.po_breakdown_id 
from pro_roll_details a, pro_roll_details b
where a.barcode_no=b.barcode_no and a.entry_form=64 and a.booking_without_order=1 
and b.entry_form in (66,67,68,71) and b.po_breakdown_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

if(empty($details_sql))
{
	echo "Data Not Found";
	die;
}

foreach($details_sql as $val)
{
	$batch_po_breakdown_id = $val[csf("batch_po_breakdown_id")];
	if ($batch_po_breakdown_id) 
	{
		// echo "update pro_roll_details set po_breakdown_id=$batch_po_breakdown_id where id = ".$val[csf("id")]." <br />";
		execute_query("update pro_roll_details set po_breakdown_id=$batch_po_breakdown_id where id=".$val[csf("id")],0);
	}
	
	
}

oci_commit($con);
//mysql_query("COMMIT");
echo "Success";
die;


?>