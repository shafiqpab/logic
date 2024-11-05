<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();


$sql_result=sql_select("select b.po_id,a.id,a.buyer_id as recv_buyer,c.po_buyer,c.within_group,c.buyer_id 
from inv_receive_master a, ppl_planning_entry_plan_dtls b, fabric_sales_order_mst c 
where a.booking_id=b.dtls_id and b.po_id=c.id and a.entry_form=2 and a.receive_basis=2 and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and c.is_deleted=0");
$recv_id="";
foreach($sql_result as $row)
{
	$recv_id.=$row[csf("id")].",";
	if($row[csf("within_group")]==1 && ($row[csf("recv_buyer")]!=$row[csf("po_buyer")])){
		$recv_buyer_arr[$row[csf("id")]]=$row[csf("po_buyer")];
	}
	if($row[csf("within_group")]==2 && ($row[csf("recv_buyer")]!=$row[csf("buyer_id")])){
		$recv_buyer_arr[$row[csf("id")]]=$row[csf("buyer_id")];
	}
	
}
// $recvID=explode(",", $recv_id);
// $recvID=implode(",", array_unique($recvID));
// $reciveID=chop($recvID,",");
// //echo $reciveID;


foreach($recv_buyer_arr as $key=> $val)
{
	execute_query("update inv_receive_master set buyer_id=$val where id=$key",0) ;
	//echo "update inv_receive_master set buyer_id=$val where id=$key<br/>" ;
}
oci_commit($con);
echo "Success"; 
disconnet($con);
die;

?>