<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("select b.id, b.is_sales, c.is_sales as batch_is_sales
from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
where a.id=b.mst_id and a.entry_form=7 and b.batch_id=c.id and b.status_active=1 and b.is_deleted=0 and b.is_sales != c.is_sales");

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach($mis_match_sql as $val)
{
	$dtls_arr[$val[csf("id")]] = $val[csf("id")];
	$dtls_sales_data[$val[csf("id")]]['is_sales'] = $val[csf("batch_is_sales")];
}

$dtls_arr = array_filter($dtls_arr);

foreach ($dtls_arr as  $dtls_id) 
{
	$is_sales = $dtls_sales_data[$dtls_id]['is_sales'];

	//echo "update pro_finish_fabric_rcv_dtls set is_sales = '".$is_sales. "' where id = ".$dtls_id." <br>";
	//echo "update order_wise_pro_details set is_sales = '".$is_sales. "' where entry_form=7 and dtls_id = ".$dtls_id." <br>";


	execute_query("update pro_finish_fabric_rcv_dtls set is_sales = '".$is_sales. "' where id = ".$dtls_id,0);
	execute_query("update order_wise_pro_details set is_sales = '".$is_sales. "' where entry_form=7 and dtls_id = ".$dtls_id,0);
}


oci_commit($con);
echo "Success"; 
disconnect($con);
die;


?>