<?

header('Content-type:text/html; charset=utf-8');
session_start(); 

include('../includes/common.php');
$con=connect();
$trims_sql="select a.id, a.mst_id, a.product_id, a.unit, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.job_no_mst, a.book_con_dtls_id, b.job_quantity from trims_job_card_breakdown a , trims_job_card_dtls b where a.mst_id=b.id and trunc(b.insert_date) > to_date('14-OCT-19','DD-MM-YYYY' ) order by a.id desc ";
$apply_sql_res=sql_select($trims_sql); $flag=1; $jobArr=array();
foreach($apply_sql_res as $row)
{
	$jobArr[$row[csf("id")]]['id']=$row[csf("id")];
	$jobArr[$row[csf("id")]]['req_qty']=$row[csf("req_qty")]/$row[csf("job_quantity")];
	$jobArr[$row[csf("id")]]['ploss_qty']=$row[csf("process_loss_qty")]/$row[csf("job_quantity")];
}
$field_array="req_qty*process_loss_qty";
foreach($jobArr as $brkId=> $row)
{
	if($brkId!='' && $brkId!=0 )
	{
		$data_array[$brkId]=explode("*",("'".$row['req_qty']."'*'".$row['ploss_qty']."'"));
		$hdn_brkId_id_arr[]=$brkId;
	}
}
if($data_array!=""  && $flag==1)
{
	echo "10**".bulk_update_sql_statement( "trims_job_card_breakdown", "id",$field_array,$data_array,$hdn_brkId_id_arr); die;
	$rID=execute_query(bulk_update_sql_statement( "trims_job_card_breakdown", "id",$field_array,$data_array,$hdn_brkId_id_arr),1);
	if($rID) $flag=1; else $flag=0;
}
//print_r($jobArr);
die;

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