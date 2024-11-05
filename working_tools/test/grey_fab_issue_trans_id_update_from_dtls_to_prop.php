<?php
include('../includes/common.php');
error_reporting(1);
$con = connect();
$sql = "SELECT a.TRANS_ID,b.ID FROM INV_GREY_FABRIC_ISSUE_DTLS a, ORDER_WISE_PRO_DETAILS b WHERE a.id = b.dtls_id AND b.entry_form = 16 AND a.trans_id <> b.trans_id AND a.status_active = 1 AND b.status_active = 1"; 
$sl_res = sql_select($sql);
$trans_id_array = array();
if(empty($sl_res))
{
	echo "Data not found";
	disconnect($con);

	die();
}
foreach ($sl_res as $val) 
{
	// echo "update ORDER_WISE_PRO_DETAILS set trans_id = '".$val['TRANS_ID']."' where id=".$val['ID']."<br>";
	execute_query("update ORDER_WISE_PRO_DETAILS set trans_id = '".$val['TRANS_ID']."' where id=".$val['ID'],0);
}

oci_commit($con);  
echo "Success";
disconnect($con);

die();

?>