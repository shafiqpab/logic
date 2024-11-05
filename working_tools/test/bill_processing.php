<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$apply_sql="select count(id) as cid, min(id) as mid, delivery_id, delivery_dtls_id from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 and process_id = 16 having count(id)>1 group by delivery_id, delivery_dtls_id order by cid desc";
$apply_sql_res=sql_select($apply_sql); $i=0;
foreach($apply_sql_res as $row)
{
	$i++;
	$mid=$row[csf("mid")];
	$booking_no="'".$row[csf("delivery_id")]."'";
	$delivery_dtls_id="'".$row[csf("delivery_dtls_id")]."'";
	//$up=execute_query("update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1");
	echo "update subcon_inbound_bill_dtls set status_active=2, is_deleted=1 where delivery_id=$booking_no and delivery_dtls_id=$delivery_dtls_id and process_id = 16 and id!=$mid".'<br>';
}

//oci_commit($con); 
	echo "Success".$i;