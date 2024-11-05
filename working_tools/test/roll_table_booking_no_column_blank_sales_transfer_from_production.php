<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$roll_transfed_sql=sql_select("select a.barcode_no, a.booking_no, b.booking_no as prod_booking
from pro_roll_details a, pro_roll_details b
where a.entry_form = 133 and a.booking_no is null and a.status_active =1 and a.is_deleted =0 and b.entry_form = 2 and a.barcode_no=b.barcode_no and b.status_active =1 and b.is_deleted =0");

if(empty($roll_transfed_sql))
{
	echo "Data Not Found";
}

foreach ($roll_transfed_sql as $row) 
{	
	$barcode_no = $row[csf('barcode_no')];
	$prod_booking = $row[csf('prod_booking')];
	execute_query("update pro_roll_details set booking_no = '".$prod_booking."' where entry_form = 133 and barcode_no = $barcode_no",0);
}*/

$roll_dtls_sql=sql_select("select a.barcode_no, a.booking_no, b.id, b.to_program, b.from_program
from pro_roll_details a, inv_item_transfer_dtls b
where a.dtls_id = b.id and a.entry_form = 133 and a.status_active =1 and a.is_deleted =0  and b.status_active =1 and b.is_deleted =0 and (b.to_program is null or b.from_program is null )");

if(empty($roll_dtls_sql))
{
	echo "Data Not Found";
}

foreach ($roll_dtls_sql as $row) 
{
	$prod_booking = $row[csf('booking_no')];
	$id = $row[csf('id')];
	execute_query("update inv_item_transfer_dtls set to_program = '".$prod_booking."',from_program = '".$prod_booking."' where id = $id",0);
}


oci_commit($con);
echo "Success"; 
die;


?>