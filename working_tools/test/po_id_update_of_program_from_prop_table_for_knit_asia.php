<?php
/*
Created by : Shafiq
Date 	   : 19-01-2021	
*/
include('../includes/common.php');
error_reporting(1);
$con = connect();
$sql = "SELECT a.program_no,b.po_breakdown_id,c.booking_no FROM INV_GREY_FABRIC_ISSUE_DTLS a, ORDER_WISE_PRO_DETAILS b,PPL_PLANNING_ENTRY_PLAN_DTLS c WHERE a.id = b.dtls_id and c.dtls_id=a.program_no and c.po_id != b.po_breakdown_id AND b.entry_form = 16 AND a.trans_id = b.trans_id AND a.status_active = 1 AND b.status_active = 1 and c.status_active=1"; 
$sl_res = sql_select($sql);
$trans_id_array = array();
if(empty($sl_res))
{
	echo "Data not found";
	disconnect($con);

	die();
}
$booking_no_array = array();
foreach ($sl_res as $val) 
{
	$booking_no_array[$val['BOOKING_NO']] = $val['BOOKING_NO'];
	// echo "update ORDER_WISE_PRO_DETAILS set po_id = '".$val['PO_BREAKDOWN_ID']."' where id=".$dtls_id['PROGRAM_NO']."<br>";
	execute_query("update PPL_PLANNING_ENTRY_PLAN_DTLS set po_id = '".$val['PO_BREAKDOWN_ID']."' where dtls_id=".$val['PROGRAM_NO'],0);
}

if(count($booking_no_array))
{
 	// execute_query("update PPL_PLANNING_ENTRY_PLAN_DTLS set po_id = '".$val['PO_BREAKDOWN_ID']."' where dtls_id=".$val['PROGRAM_NO'],0);
}

oci_commit($con);  
echo "Success";
disconnect($con);

die();

?>