<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$issue_sql=sql_select("select a.id, a.issue_number, d.id as fso_id, d.job_no,d.sales_booking_no, d.booking_id
from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,fabric_sales_order_mst d 
where a.entry_form=224 and a.id=b.mst_id and b.batch_id=c.id and b.order_id=d.id and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by a.id, a.issue_number, d.id, d.job_no,d.sales_booking_no, d.booking_id ");

if(empty($issue_sql))
{
	echo "Data Not Found";
	die;
}

foreach($issue_sql as $val)
{
	$issue_id_arr[$val[csf("id")]] = $val[csf("id")];
	$issue_ref[$val[csf("id")]]["fso_id"] = $val[csf("fso_id")];
	$issue_ref[$val[csf("id")]]["job_no"] = $val[csf("job_no")];
	$issue_ref[$val[csf("id")]]["sales_booking_no"] = $val[csf("sales_booking_no")];
	$issue_ref[$val[csf("id")]]["booking_id"] = $val[csf("booking_id")];
}

$issue_id_arr = array_filter($issue_id_arr);

foreach ($issue_id_arr as  $issue_id) 
{

	$fso_id = $issue_ref[$issue_id]["fso_id"];
	$job_no = $issue_ref[$issue_id]["job_no"];
	$sales_booking_no = $issue_ref[$issue_id]["sales_booking_no"];
	$booking_id = $issue_ref[$issue_id]["booking_id"];

	//echo "update inv_issue_master set booking_id = '".$booking_id. "', booking_no = '".$sales_booking_no. "', fso_id = '".$fso_id. "', fso_no = '".$job_no. "', updated_by = 999 where entry_form=224 and id = ".$issue_id." <br>";
	execute_query("update inv_issue_master set booking_id = '".$booking_id. "', booking_no = '".$sales_booking_no. "', fso_id = '".$fso_id. "', fso_no = '".$job_no. "', updated_by = 999 where entry_form=224 and id = ".$issue_id,0);
}

oci_commit($con);
echo "Success"; 
die;


?>