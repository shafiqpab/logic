<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con = connect();


$trim_sql=" select id,sourcing_nominated_supp ,job_no,job_id from wo_pre_cost_trim_cost_dtls where (sourcing_nominated_supp is not null or  sourcing_nominated_supp!=0) and status_active=1 order by job_id,id asc ";

$trim_sql_result=sql_select($trim_sql);
 $field_supp_trim="id, job_id, job_no, trimid, supplier_id, sourcing_inserted_by, status_active, is_deleted";
$trim_sup_id = return_next_id("id", "wo_pre_cost_trim_sup_sourcing", 1);
$add_comma=0;
$job_noArr=array();
foreach($trim_sql_result as $row)
{
	$trimid=$row[csf('id')];
	$job_no=$row[csf('job_no')];
	$job_id=$row[csf('job_id')];
	$nominated_supp=explode(",",$row[csf('sourcing_nominated_supp')]); 
	$data_array_trim="";
	for($c=0;$c < count($nominated_supp);$c++)
	{
		if ($c!=0) $data_array_trim .=",";
		$data_array_trim .="(".$trim_sup_id.",".$job_id.",'".$job_no."',".$trimid.",".$nominated_supp[$c].",'1',1,0)";
		$trim_sup_id=$trim_sup_id+1;
		$add_comma++;
	}

	$job_noArr[$job_id]=$job_id;
	$rID2=0;
	if($data_array_trim !=""){
		
				//echo "insert into wo_pre_cost_trim_sup_sourcing ($field_supp_trim) values $data_array_trim";die;
		$rID2=sql_insert("wo_pre_cost_trim_sup_sourcing",$field_supp_trim,$data_array_trim,1);
	}
}

if($rID2)
{
	oci_commit($con); 
	echo "Success=".count($job_noArr);
}
else
{
	oci_rollback($con); 
	echo "Failed";
}
disconnect($con);
die;





//$mrrWiseIsID = return_next_id("id", "wo_pre_cost_trim_sup_sourcing", 1);



 
//echo "<br>".$test_issue_qnty;
//die;
//echo count($data_array_mrr);die;
//echo "<pre>";print_r($data_array_mrr);die;
//echo $i."==".$k;die;
//echo count($updateID_array);echo "<pre>";print_r($updateID_array);die;execute_query

//echo count($data_array_mrr);die;

//echo bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array);die;

$rID = sql_insert2("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
//$rID2=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array),1);

//echo $rID;die;
//echo $rID."<br>".$rID2;die;

if($rID && $rID2)
{
	oci_commit($con); 
	echo "Success";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}

?>