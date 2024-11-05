<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();



$details_sql=sql_select("select a.floor_id, a.room, a.rack, a.self, a.transaction_type, a.transaction_date, b.id
from inv_transaction a, pro_finish_fabric_rcv_dtls b
where a.id= b.trans_id and a.status_active=1 and b.status_active=1 and a.floor_id != b.floor and a.floor_id !=0 ");

if(empty($details_sql))
{
	echo "Data Not Found";
	die;
}

foreach($details_sql as $val)
{
	$floor_id = $val[csf("floor_id")];
	$room = $val[csf("room")];
	$rack = $val[csf("rack")];
	$self = $val[csf("self")];

	//b.floor, b.room, b.rack_no, b.shelf_no,

	echo "update pro_finish_fabric_rcv_dtls set floor = $floor_id, room = $room, rack_no = $rack, shelf_no = $self where id=".$val[csf("id")]." <br />";
	//execute_query("update pro_finish_fabric_rcv_dtls set floor = $floor_id, room = $room, rack_no = $rack, shelf_no = $self where id=".$val[csf("id")],0);
	
}

//oci_commit($con);
//mysql_query("COMMIT");
//echo "Success";
die;


?>