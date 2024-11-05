<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$issue_sql=sql_select("select a.id, a.issue_number, c.booking_no, c.booking_no_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id = b.mst_id and b.batch_id = c.id and a.entry_form = 18 and b.status_active =1 and c.booking_no is not null and a.booking_no is null");

if(empty($issue_sql))
{
	echo "Data Not Found";
	die;
}

foreach($issue_sql as $val)
{
	$issue_arr[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
	$issue_arr[$val[csf("id")]]["booking_id"] = $val[csf("booking_no_id")];
}

/*echo "<pre>";
print_r($issue_arr);
die;*/

foreach($issue_arr as $issue_id=>$val)
{
	$booking_no = $val["booking_no"];
	$booking_id = $val["booking_id"];
	//echo "update inv_issue_master set booking_no = '".$booking_no. "', booking_id = '".$booking_id."', updated_by = 888 where id = ".$issue_id."<br>";
	execute_query("update inv_issue_master set booking_no = '".$booking_no. "', booking_id = '".$booking_id."', updated_by = 888 where id = ".$issue_id,0);
}


oci_commit($con);
echo "Success";
die;


?>