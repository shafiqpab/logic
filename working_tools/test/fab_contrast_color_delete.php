<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

$fab_sql="select  c.id as fab_id,a.job_no,c.color_size_sensitive from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_mst a,wo_pre_cost_fabric_cost_dtls c where a.job_id=b.job_id and  a.job_id=c.job_id and c.id=b.pre_cost_fabric_cost_dtls_id  and to_char(a.insert_date,'YYYY')=2021 and c.color_size_sensitive!=3  and c.status_active=1 and b.status_active=1 and a.status_active=1";//   company_id=3  and to_char(insert_date,'YYYY')=2020

$fab_sql_res=sql_select($fab_sql); $i=0;
foreach($fab_sql_res as $row)
{
	$i++;
	$color_size_sensitive=$row[csf("color_size_sensitive")];
	$job_no=$row[csf("job_no")];
	$fab_id=$row[csf("fab_id")];
	//$id=$row[csf("id")];
	//echo "update wo_pre_cos_fab_co_color_dtls set status_active=0,is_deleted=1 where job_no='$job_no' and pre_cost_fabric_cost_dtls_id=$fab_id";die;
 	$up=execute_query("update wo_pre_cos_fab_co_color_dtls set status_active=0,is_deleted=1 where job_no='$job_no' and pre_cost_fabric_cost_dtls_id=$fab_id");//1
	
}

//print_r($booking_noArr);die;
if($up)
{
	oci_commit($con); 
	echo "Success=".$i;
}
else
{
	oci_rollback($con);
	echo "Not Success=".$i;
}