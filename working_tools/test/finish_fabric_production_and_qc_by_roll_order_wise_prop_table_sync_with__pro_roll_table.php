<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sql =  sql_select("select b.id, b.dtls_id
from pro_roll_details a, order_wise_pro_details b
where a.dtls_id =b.dtls_id and a.entry_form = 66 and b.entry_form =66 and a.booking_without_order =1 and b.status_active =1 and a.status_active =1");


if(empty($sql))
{
	echo "Data Not Found";
	die;
}

$flag=1;
foreach ($sql as $val)
{
	$delete_dtls=execute_query("update order_wise_pro_details set status_active='0', is_deleted='1' where entry_form=66 and id = ".$val[csf("id")],0);

	if($flag==1)
	{
		if($delete_dtls) $flag=1; else $flag=0;
	}
	else
	{
		oci_rollback($con);
		echo "update order_wise_pro_details set status_active='0', is_deleted='1' where entry_form=66 and id = ".$val[csf("id")];
		disconnect($con);
		die;
	}
}

oci_commit($con); 
echo "Success";
disconnect($con);
die;
 
?>