<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$job_sql="select id, job_no from wo_po_details_master";
$job_sql_res=sql_select($job_sql); $i=0;
foreach($job_sql_res as $row)
{
	$i++;
	$job_no=$row[csf("job_no")];
	$id=$row[csf("id")];
	//echo "update wo_po_break_down set job_id='$id' where job_no_mst='$job_no' and job_id=0".'<br>';
	//$up=execute_query("update wo_po_break_down set job_id='$id' where job_no_mst='$job_no'");
	//$up=execute_query("update wo_po_color_size_breakdown set job_id='$id' where job_no_mst='$job_no'");
	//$up=execute_query("update wo_po_details_mas_set_details set job_id='$id' where job_no='$job_no'");
	//$up=execute_query("update wo_pre_cos_emb_co_avg_con_dtls set job_id='$id' where job_no='$job_no'");
	//$up=execute_query("update wo_pre_cos_fab_co_avg_con_dtls set job_id='$id' where job_no='$job_no'");
	//$up=execute_query("update wo_pre_cos_fab_co_color_dtls set job_id='$id' where job_no='$job_no'");
	//$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	/*$up=execute_query("update wo_pre_cost_commiss_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_embe_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_fab_conv_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_fab_yarn_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_fab_yarnbreakdown set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_fabric_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_lab_test_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_mst set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_sum_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_trim_co_cons_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_trim_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_stripe_color set job_id='$id' where job_no='$job_no'");*/
	
	/*$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");
	$up=execute_query("update wo_pre_cost_comarci_cost_dtls set job_id='$id' where job_no='$job_no'");*/
	//echo "update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1";
}

oci_commit($con); 
echo "Success".$i;