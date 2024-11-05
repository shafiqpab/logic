<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$delivery_sql=sql_select("select  b.id as trans_id, a.fabric_shade 
from  inv_finish_fabric_issue_dtls a, inv_transaction b, inv_issue_master c
where a.trans_id = b.id and b.mst_id = c.id and c.entry_form = 224 and c.status_active=1 
and b.transaction_type = 2 and b.item_category = 2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
");



if(empty($delivery_sql))
{
	echo "Data Not Found";
	die;
}

foreach($delivery_sql as $val)
{
	$trans_id_arr[$val[csf("id")]] = $val[csf("id")];
	$issue_dtls_fab_shade_arr[$val[csf("trans_id")]] = $val[csf("fabric_shade")];
}

$trans_ids = implode(",", $trans_id_arr);
$transCond = $all_trans_id_cond = ""; 
if($db_type==2 && count($trans_id_arr)>999)
{
	$trans_id_arr_chunk=array_chunk($trans_id_arr,999) ;
	foreach($trans_id_arr_chunk as $chunk_arr)
	{
		$transCond.=" a.trans_id in(".implode(",",$chunk_arr).") or ";	
	}
	$all_trans_id_cond.=" and (".chop($transCond,'or ').")";
}
else
{ 	
	$all_trans_id_cond=" and a.trans_id in($trans_ids)";  
}



$trans_id = "";

foreach ($trans_id_arr as  $tId) 
{
	$trans_id =  $issue_dtls_fab_shade_arr[$tId];

	echo "update inv_transaction set fabric_shade = '".$issue_dtls_fabric_shade_arr[3855503]["fabric_shade"]."',  updated_by = 999 where id = ".$tId." <br>";
	//die;
	//execute_query("update inv_transaction set fabric_shade = '".$issue_dtls_fabric_shade_arr[$tId]["fabric_shade"]."',  updated_by = 999 where id = ".$tId,0);
}


/*oci_commit($con);
echo "Success"; 
die;*/



?>