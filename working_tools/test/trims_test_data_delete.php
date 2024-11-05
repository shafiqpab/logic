<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 

$trims_sql="select id,order_id,order_no,subcon_job from subcon_ord_mst where entry_form=255";
$apply_sql_res=sql_select($trims_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	
	$booking_no="'".$row[csf("order_no")]."'";
	$trims_job_no="'".$row[csf("subcon_job")]."'";
	$id="'".$row[csf("id")]."'";
	
	$booking_update=execute_query("update wo_booking_mst set lock_another_process=0 where booking_no=$booking_no and lock_another_process=1");
	if($booking_update==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con);
		echo "failed";
	} 
	//echo "delete from subcon_ord_breakdown  where job_no_mst= $trims_job_no";
	$breakdown_delete = execute_query("DELETE FROM subcon_ord_breakdown WHERE job_no_mst=$trims_job_no");
	//$breakdown_delete=execute_query("delete from subcon_ord_breakdown  where job_no_mst= $trims_job_no");
	if($breakdown_delete==1)
	{
		$flag=1;
	} 
	else
	{
		$flag=0;
		oci_rollback($con);
		echo "failed";
	} 
	
	$dtls_delete = execute_query("DELETE FROM subcon_ord_dtls WHERE mst_id=$id and job_no_mst=$trims_job_no");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
	$mst_delete = execute_query("DELETE FROM subcon_ord_mst WHERE id=$id and entry_form=255");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
}
//oci_rollback($con); 
//echo "kkf"; die;

//echo $test_data;die;
if($flag)
{
	oci_commit($con); 
	echo "Success";

}
else
{
	oci_rollback($con);
	echo "failed";
}



 
?>