<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$issue_return_sql = sql_select("select c.id,  c.pi_wo_batch_no , c.prod_id,  c.issue_id from  inv_transaction c where  c.status_active = 1 and c.issue_id >0 and c.transaction_type= 4 and c.item_category=2");

foreach($issue_return_sql as $val)
{
	$issue_id_arr[$val[csf("issue_id")]] = $val[csf("issue_id")];
}

$issue_id_arr = array_filter($issue_id_arr);
$all_issue_id_cond="";
$issueId="";
if($db_type==2 && count($issue_id_arr)>999)
{
	$issue_id_arr_chunk=array_chunk($issue_id_arr,999) ;
	foreach($issue_id_arr_chunk as $chunk_arr)
	{
		$chunk_arr_value=implode(",",$chunk_arr);
		$issueId.=" b.id in($chunk_arr_value) or ";
	}

	$all_issue_id_cond.=" and (".chop($issueId,'or ').")";
}
else
{
	$all_issue_id_cond=" and b.id in(".implode(",", $issue_id_arr).")";
}





$issue_sql=sql_select("select a.body_part_id, a.batch_id, a.prod_id, b.id as issue_id
from inv_finish_fabric_issue_dtls a, inv_issue_master b, inv_transaction c
where  a.mst_id =b.id and b.entry_form = 18  and a.status_active = 1 and b.status_active =1
and a.trans_id = c.id and c.transaction_type= 2 and c.item_category=2 and b.id in (".implode(',', $issue_id_arr).")
group by a.body_part_id, a.batch_id, a.prod_id, b.id");

if(empty($issue_sql))
{
	echo "Data Not Found";
	die;
}

foreach($issue_sql as $val)
{
	$dtlsBatch[$val[csf("issue_id")]][$val[csf("batch_id")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
}


foreach ($issue_return_sql as  $val) 
{
	$body_part_id = $dtlsBatch[$val[csf("issue_id")]][$val[csf("pi_wo_batch_no")]][$val[csf("prod_id")]]["body_part_id"];
	echo "update inv_transaction set body_part_id = '".$body_part_id."' where id = ".$val[csf("id")]." <br>";
	//execute_query("update inv_transaction set body_part_id = '".$body_part_id. "' where id =  ".$val[csf("id")],0);
	//execute_query("update pro_finish_fabric_rcv_dtls set body_part_id = '".$body_part_id. "' where trans_id =  ".$val[csf("id")],0);
}

/*
oci_commit($con);
echo "Success"; 
die;*/


?>