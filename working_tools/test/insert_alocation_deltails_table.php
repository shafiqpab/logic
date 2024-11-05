<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
$field_array1 = "id,mst_id,po_break_down_id,job_no,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";

$insert_string= "137.50_32966_ASTL-19-00392,395.23_32967_ASTL-19-00392,50.27_32968_ASTL-19-00392";

$main_string_data_arr = explode(",", $insert_string);

foreach ($main_string_data_arr as $main_string) {
	
	$data_arr = explode("_", $main_string);
	$qty = $data_arr[0];
	$po_id = $data_arr[1];
	$job_no = $data_arr[2];
	$txt_booking_no = 'ASTL-Fb-19-00684';
	$cbo_item_category = 1;
	$txt_allocation_date = '03-Sep-2019';
	$txt_item_id = 424538;
	
	if ($data_array1 != "") $data_array1 .= ",";
	$data_array1 .= "(" . $id1 . ",55234," . $po_id . ",'" . $job_no . "','" . $txt_booking_no . "'," . $cbo_item_category . ",'" . $txt_allocation_date . "'," . $txt_item_id . "," . $qty . ",689,'" . $pc_date_time . "')";
	//die();
}

if ($data_array1 != '') {
	//echo "10**INSERT INTO inv_material_allocation_dtls (".$field_array1.") VALUES ".$data_array1.""; die;
	$rID1 = sql_insert("inv_material_allocation_dtls", $field_array1, $data_array1, 0);
	
}
if ($db_type == 2 || $db_type == 1) {
	if ($rID1) 
	{
		oci_commit($con);
		echo "0**" . $rID1;
	} else {
		oci_rollback($con);
		echo "10**" . $rID1;
	}
}
disconnect($con);
disconnect($con);
die;


?>