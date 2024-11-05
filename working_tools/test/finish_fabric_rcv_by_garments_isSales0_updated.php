<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$invRecvMst=sql_select("select id from INV_RECEIVE_MASTER  where entry_form = 37");

if(empty($invRecvMst))
{
	echo "Data Not Found";
	die;
}

foreach($invRecvMst as $val)
{

	$recvIDs[$val[csf("id")]] = $val[csf("id")];

}


foreach ($recvIDs as  $recvID) 
{
	//echo "update PRO_FINISH_FABRIC_RCV_DTLS set is_sales = 0, updated_by = 999 where id = ".$recvID." <br>";
	execute_query("update PRO_FINISH_FABRIC_RCV_DTLS set is_sales = 0, updated_by = 999 where mst_id = ".$recvID,0);
}


oci_commit($con);
echo "Success"; 
die;


?>