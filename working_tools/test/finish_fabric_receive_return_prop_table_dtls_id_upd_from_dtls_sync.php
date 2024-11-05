<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$transaction_sql=sql_select("select b.id as prop_id, a.id as dtls_id, a.trans_id from inv_finish_fabric_issue_dtls a, order_wise_pro_details b where a.trans_id = b.trans_id and b.entry_form=46 and b.status_active=1 and a.status_active=1");

if(empty($transaction_sql))
{
	echo "Data Not Found";
	die;
}

foreach($transaction_sql as $val)
{
	$dtls_id = $val[csf("dtls_id")];
	$prop_id = $val[csf("prop_id")];
	echo "update order_wise_pro_details set dtls_id = '".$dtls_id. "' where entry_form = 46 and id = ".$prop_id." <br>";

	//execute_query("update order_wise_pro_details set dtls_id = '".$dtls_id. "' where entry_form = 46 and id = ".$prop_id,0);
}


//oci_commit($con);
//die;


?>