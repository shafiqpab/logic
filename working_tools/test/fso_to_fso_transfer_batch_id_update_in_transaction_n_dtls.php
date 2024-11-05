<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$fso_2_fso_sql=sql_select("select b.id as dtls_id, b.batch_id, b.to_batch_id, b.trans_id, b.to_trans_id, b.from_store, b.to_store from inv_item_transfer_mst a,inv_item_transfer_dtls b, inv_transaction c
where a.id = b.mst_id and a.entry_form = 230 and b.status_active = 1  and b.trans_id=c.id and c.transaction_type=6 and c.pi_wo_batch_no=0");

if(empty($fso_2_fso_sql))
{
	echo "Data Not Found";
	die;
}

foreach($fso_2_fso_sql as $val)
{

	$trans_batch_arr[$val[csf("trans_id")]] = $val[csf("batch_id")];
	$trans_batch_arr[$val[csf("to_trans_id")]] = $val[csf("batch_id")];

	$trans_arr[$val[csf("trans_id")]] = $val[csf("trans_id")];
	$trans_arr[$val[csf("to_trans_id")]] = $val[csf("to_trans_id")];

	$dtlsBatch[$val[csf("dtls_id")]] = $val[csf("batch_id")];
	$dtls_arr[$val[csf("dtls_id")]] = $val[csf("dtls_id")];

}

$batch_no= "";
$trans_arr = array_unique(array_filter($trans_arr));
foreach ($trans_arr as  $trans_id) 
{
	$batch_no = $trans_batch_arr[$trans_id];
	//echo "update inv_transaction set pi_wo_batch_no = '".$batch_no. "', updated_by = 999 where id = ".$trans_id." <br>";
	execute_query("update inv_transaction set pi_wo_batch_no = '".$batch_no. "', updated_by = 999 where pi_wo_batch_no=0 and id = ".$trans_id,0);
}


/* $batch_no= "";
$dtls_arr = array_unique(array_filter($dtls_arr));
foreach ($dtls_arr as  $dtls_id) 
{
	$batch_no = $dtlsBatch[$dtls_id];
	//echo "update inv_item_transfer_dtls set to_batch_id = '".$batch_no. "', updated_by = 999 where id = ".$dtls_id." <br>";
	execute_query("update inv_item_transfer_dtls set to_batch_id = '".$batch_no. "', updated_by = 999 where id = ".$dtls_id,0);
} */



oci_commit($con);
echo "Success"; 
die;


?>