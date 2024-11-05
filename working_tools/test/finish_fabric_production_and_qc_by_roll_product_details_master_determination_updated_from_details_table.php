<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sql =  sql_select("select a.prod_id, a.fabric_description_id
from inv_receive_master c, pro_finish_fabric_rcv_dtls a, product_details_master b
where c.id=a.mst_id and c.entry_form =66
and a.prod_id=b.id and a.fabric_description_id != b.detarmination_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
group by a.prod_id,a.fabric_description_id");


if(empty($sql))
{
	echo "Data Not Found";
	die;
}

$flag=1;
foreach ($sql as $val)
{
	echo "update product_details_master set detarmination_id='".$val[csf("fabric_description_id")]."' where id =".$val[csf("prod_id")]."<br>";
	/*$up_prod_table=execute_query("update product_details_master set detarmination_id='".$val[csf("fabric_description_id")]."' where id = ".$val[csf("prod_id")],0);

	if($flag==1)
	{
		if($up_prod_table) $flag=1; else $flag=0;
	}
	else
	{
		oci_rollback($con);
		echo "update product_details_master set detarmination_id='".$val[csf("fabric_description_id")]."' where id =".$val[csf("prod_id")];
		disconnect($con);
		die;
	}*/
}
disconnect($con);
		die;

/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>