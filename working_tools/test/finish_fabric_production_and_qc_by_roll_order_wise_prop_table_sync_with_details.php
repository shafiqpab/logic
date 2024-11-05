<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sql =  sql_select("select dtls_id, count(dtls_id), max(id) as max_id, listagg(id,',') within group (order by id) as ids
from order_wise_pro_details
where entry_form =66 and status_active =1
group by dtls_id
having count(dtls_id) >1");


$delete_sql = sql_select("select a.id, a.insert_date
from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b
where a.dtls_id=b.id and a.entry_form =66 
and a.status_active=1 and a.is_deleted =0 and b.status_active =0 and b.is_deleted=1");

if(empty($sql) && empty($delete_sql))
{
	echo "Data Not Found";
	die;
}

$flag=1;
foreach ($delete_sql as $val) 
{
	//echo "update order_wise_pro_details set status_active='0', is_deleted='1' where entry_form=66 and id=".$val[csf("id")]." <br>";

	$delete_dtls=execute_query( "update order_wise_pro_details set status_active='0', is_deleted='1' where entry_form=66 and id=".$val[csf("id")],0);
	if($flag==1)
	{
		if($delete_dtls) $flag=1; else $flag=0;
	}
	else
	{
		oci_rollback($con);
		echo "update order_wise_pro_details set status_active='0', is_deleted='1' where entry_form=66 and id=".$val[csf("id")];
		disconnect($con);
		die;
	}
}



foreach ($sql as $val)
{
	//echo "update order_wise_pro_details set status_active='0', is_deleted='1' where dtls_id=".$val[csf("dtls_id")]." and entry_form=66 and id != ".$val[csf("max_id")]." <br>";

	$delete_dtls_2=execute_query("update order_wise_pro_details set status_active='0', is_deleted='1' where dtls_id=".$val[csf("dtls_id")]." and entry_form=66 and id != ".$val[csf("max_id")],0);

	if($flag==1)
	{
		if($delete_dtls_2) $flag=1; else $flag=0;
	}
	else
	{
		oci_rollback($con);
		echo "update order_wise_pro_details set status_active='0', is_deleted='1' where dtls_id=".$val[csf("dtls_id")]." and entry_form=66 and id != ".$val[csf("max_id")];
		disconnect($con);
		die;
	}
}



oci_commit($con); 
echo "Success";
disconnect($con);
die;
 
?>