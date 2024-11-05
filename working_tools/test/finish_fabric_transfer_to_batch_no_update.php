<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("select a.transfer_system_id,a.transfer_date, a.transfer_criteria,b.id, b.batch_id, b.to_batch_id
from inv_item_transfer_mst a, inv_item_transfer_dtls b
where a.id = b.mst_id
and a.entry_form in (14,15) and b.to_batch_id =0 and a.transfer_criteria =2 ");

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach($mis_match_sql as $val)
{
	$dtls_arr[$val[csf("id")]] = $val[csf("id")];
	$dtlsBatch[$val[csf("id")]] = $val[csf("batch_id")];
}

$dtls_arr = array_filter($dtls_arr);

$batch_id = "";
foreach ($dtls_arr as  $dtls_id) 
{
	$batch_id =  $dtlsBatch[$dtls_id];
	echo "update inv_item_transfer_dtls set to_batch_id = '".$batch_id. "', updated_by = 999 where id = ".$dtls_id." <br>";
	//execute_query("update inv_item_transfer_dtls set to_batch_id = '".$batch_id. "', updated_by = 999 where id = ".$dtls_id,0);
}

/*
oci_commit($con);
echo "Success"; 
die;*/


?>