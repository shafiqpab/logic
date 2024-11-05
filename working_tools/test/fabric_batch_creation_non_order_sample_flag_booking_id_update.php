<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mis_match_sql=sql_select("SELECT a.batch_no, a.id,   a.booking_no, b.booking_id as fso_booking_id, b.sales_booking_no, b.booking_without_order as fso_booking_without_order 
from pro_batch_create_mst a, fabric_sales_order_mst b
where a.sales_order_no= b.job_no and a.status_active=1 and b.booking_without_order=1 and a.booking_without_order !=1 and b.within_group=1 order by a.id desc");

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach($mis_match_sql as $val)
{
	$batch_arr[$val[csf("id")]] = $val[csf("id")];
	$booking_data[$val[csf("id")]]['booking_no'] = $val[csf("sales_booking_no")];
	$booking_data[$val[csf("id")]]['booking_id'] = $val[csf("fso_booking_id")];
	$booking_data[$val[csf("id")]]['booking_without_order'] = $val[csf("fso_booking_without_order")];
}

$batch_arr = array_filter($batch_arr);

foreach ($batch_arr as  $batch_id) 
{
	$booking_no = $booking_data[$batch_id]['booking_no'];
	$booking_id = $booking_data[$batch_id]['booking_id'];
	$booking_without_order = $booking_data[$batch_id]['booking_without_order'];

	//echo "update pro_batch_create_mst set booking_no_id = '".$booking_id. "', booking_without_order = '".$booking_without_order. "' where id = ".$batch_id." <br>";
	execute_query("update pro_batch_create_mst set booking_no_id = '".$booking_id. "', booking_without_order = '".$booking_without_order. "' where id = ".$batch_id,0);
}

oci_commit($con);
echo "Success"; 
disconnect($con);
die;


?>