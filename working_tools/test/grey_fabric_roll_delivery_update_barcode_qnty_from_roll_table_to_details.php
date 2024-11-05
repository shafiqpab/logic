<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$sql =  sql_select("select b.id, c.grey_used_qty, b.grey_used_qnty
from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_finish_fabric_rcv_dtls c
where a.entry_form =54 and a.id = b.mst_id and b.sys_dtls_id = c.id and b.grey_used_qnty is  null");*/

$sql =  sql_select("select a.barcode_no,a.qnty, b.id from pro_roll_details a, pro_grey_prod_delivery_dtls  b
  where b.id=a.dtls_id and a.entry_form =56 and b.current_delivery != a.qnty");


if(empty($sql))
{
	echo "Data Not Found";
	die;
}

foreach ($sql as $val) 
{
	echo "update pro_grey_prod_delivery_dtls set current_delivery='".$val[csf('qnty')]."' where id=".$val[csf("id")]."<br>";
	//execute_query("update pro_grey_prod_delivery_dtls set current_delivery='".$val[csf('qnty')]."' where id=".$val[csf("id")],0);
}

/*oci_commit($con); 
echo "Success";
disconnect($con);*/
die;
 
?>