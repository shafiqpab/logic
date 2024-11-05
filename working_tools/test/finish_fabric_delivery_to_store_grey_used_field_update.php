<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sql =  sql_select("select b.id, c.grey_used_qty, b.grey_used_qnty
from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_finish_fabric_rcv_dtls c
where a.entry_form =54 and a.id = b.mst_id and b.sys_dtls_id = c.id and b.grey_used_qnty is  null");
if(empty($sql))
{
	echo "Data Not Found";
	die;
}

foreach ($sql as $val) 
{
	//echo "update pro_grey_prod_delivery_dtls set grey_used_qnty='".number_format($val[csf('grey_used_qty')],4,'.','')."' where id=".$val[csf("id")]."<br>";
	execute_query("update pro_grey_prod_delivery_dtls set grey_used_qnty='".number_format($val[csf('grey_used_qty')],4,'.','')."' where id=".$val[csf("id")],0);
}

oci_commit($con); 
echo "Success";
disconnect($con);
die;
 
?>