<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select(" select  a.id, a.batch_no, a.color_id, a.booking_no, a.booking_no_id, a.booking_without_order, a.sales_order_no, a.sales_order_id, a.entry_form, c.sales_booking_no, c.booking_id as fso_booking_id, c.booking_without_order as fso_booking_without_order
from pro_batch_create_mst a,  inv_receive_master b, fabric_sales_order_mst c
where a.entry_form =225 and b.entry_form=225 and b.receive_basis=14 and a.sales_order_no=b.booking_no and c.id= a.sales_order_id and a.status_active=1 and b.status_active=1  and a.booking_no_id=0 and a.booking_no is null
group by a.id, a.batch_no, a.color_id, a.booking_no, a.booking_no_id, a.booking_without_order, a.sales_order_no, a.sales_order_id, a.entry_form,c.sales_booking_no, c.booking_id, c.booking_without_order");

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


	//echo "update pro_batch_create_mst set booking_no = '".$booking_no. "', booking_no_id = '".$booking_id. "', booking_without_order = '".$booking_without_order. "' where id = ".$batch_id." <br>";
	execute_query("update pro_batch_create_mst set booking_no = '".$booking_no. "', booking_no_id = '".$booking_id. "', booking_without_order = '".$booking_without_order. "' where id = ".$batch_id,0);
}


oci_commit($con);
echo "Success"; 
disconnect($con);
die;


?>