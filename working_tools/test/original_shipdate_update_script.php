<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
die;
$job_sql="select A.ID, B.JOB_ID,B.ID AS PO_ID,B.SHIPMENT_DATE from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.job_no_prefix like '%EKL-22-%' and a.copy_from is not null and a.status_active=1 and b.status_active!=0 ";
$job_sql_res=sql_select($job_sql); $i=0;
foreach($job_sql_res as $row)
{
	$i++;
	$po_id=$row["PO_ID"];
	$job_id=$row["ID"];
	$shipment_date=$row["SHIPMENT_DATE"];
	//echo "update wo_po_color_size_breakdown set country_ship_date='$shipment_date' where job_id='$job_id' and po_break_down_id='$po_id' and status_active=1".'<br>';
	//$up=execute_query("update wo_po_color_size_breakdown set country_ship_date='$shipment_date' where job_id='$job_id' and po_break_down_id='$po_id' and status_active=1");
	
}
//die;
oci_commit($con); 
echo "Success=".$i;