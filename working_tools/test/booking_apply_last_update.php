<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$apply_sql="select b.sales_booking_no from wo_booking_mst a, fabric_sales_order_mst b where a.booking_no=b.sales_booking_no and a.is_apply_last_update=1 and b.revise_no=0";
$apply_sql_res=sql_select($apply_sql); $i=0;
foreach($apply_sql_res as $row)
{
	$i++;
	$booking_no="'".$row[csf("sales_booking_no")]."'";
	$up=execute_query("update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1");
	//echo "update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1";
}

oci_commit($con); 
	echo "Success".$i;