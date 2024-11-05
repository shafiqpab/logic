<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$grey_receive = sql_select("select a.job_no,a.booking_no,a.item_id from inv_material_allocation_mst a,inv_material_allocation_dtls b
where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.job_no is not null");
$allocated_booking=array();
if(!empty($grey_receive))
{
	foreach($grey_receive as $row)
	{
		$allocated_booking[$row[csf("job_no")]][$row[csf("item_id")]]=$row[csf("booking_no")];
	}
}


$ysw_data = sql_select("select a.ydw_no,b.id dtls_id,b.job_no,b.product_id,(select listagg(booking_no,',' ) within group (order by booking_no) booking_no 
from wo_booking_mst where wo_booking_mst.job_no=b.job_no and booking_type=1 and is_short=2) booking_no
from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b
where a.id=b.mst_id and a.entry_form = 94
and a.service_type = 12 and a.status_active=1 
and b.job_no is not null and b.fab_booking_no is null");
if(!empty($ysw_data))
{
	foreach($ysw_data as $row)
	{
		$dtl_id = $row[csf("dtls_id")];
		$job_no = $row[csf("job_no")];
		$booking_no = $allocated_booking[$row[csf("job_no")]][$row[csf("product_id")]];
		echo "UPDATE wo_yarn_dyeing_dtls set fab_booking_no=$booking_no where id=$dtl_id $job_no <br />";
		//$update_allocation_dtls=execute_query("UPDATE wo_yarn_dyeing_dtls set fab_booking_no=$booking_no where id=$dtl_id");
	}
}

//oci_commit($con);
echo "Success";
die;

?>