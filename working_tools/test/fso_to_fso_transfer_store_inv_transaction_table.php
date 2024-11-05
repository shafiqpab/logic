<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$fso_2_fso_sql=sql_select("select b.trans_id, b.to_trans_id, b.from_store, b.to_store from inv_item_transfer_mst a,inv_item_transfer_dtls b
where a.id = b.mst_id and a.entry_form = 230 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0");

if(empty($fso_2_fso_sql))
{
	echo "Data Not Found";
	die;
}

foreach($fso_2_fso_sql as $val)
{
	$trans_store_arr[$val[csf("trans_id")]] = $val[csf("from_store")];
	$trans_store_arr[$val[csf("to_trans_id")]] = $val[csf("to_store")];

	$trans_arr[$val[csf("trans_id")]] = $val[csf("trans_id")];
	$trans_arr[$val[csf("to_trans_id")]] = $val[csf("to_trans_id")];

}

$store_id= "";
$trans_arr = array_unique(array_filter($trans_arr));
foreach ($trans_arr as  $trans_id) 
{
	$store_id = $trans_store_arr[$trans_id];
	//echo "update inv_transaction set store_id = '".$store_id. "', updated_by = 999 where id = ".$trans_id." <br>";
	execute_query("update inv_transaction set store_id = '".$store_id. "', updated_by = 999 where id = ".$trans_id,0);
}


oci_commit($con);
echo "Success"; 
die;


?>