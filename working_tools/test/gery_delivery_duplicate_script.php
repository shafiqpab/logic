<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$duplicate_sql="select id, grey_sys_id, product_id, order_id from pro_grey_prod_delivery_dtls where grey_sys_id in(select grey_sys_id
from pro_grey_prod_delivery_dtls where entry_form=53 and status_active=1 and is_deleted=0
group by grey_sys_id, product_id, order_id
having count(product_id)>1)
order by id";

$duplicate_sql_result=sql_select($duplicate_sql);
$dup_data_check=array();$upDtls=true;
foreach ($duplicate_sql_result as $row) 
{
	
	if($dup_data_check[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]]=="")
	{
		$dup_data_check[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]]=$row[csf("order_id")];
	}
	else
	{
		$grey_delivery_dtls=execute_query("update pro_grey_prod_delivery_dtls set status_active=0,is_deleted=1,updated_by=9999 where id=".$row[csf("id")]);
		if($grey_delivery_dtls) $upDtls=true;
		else
		{
			echo "update pro_grey_prod_delivery_dtls set status_active=0,is_deleted=1,updated_by=9999 where id=".$row[csf("id")];oci_rollback($con); die;
		}
	}
}
if($upDtls)
{
	oci_commit($con); 
	echo "Success";
	die;
}

?>