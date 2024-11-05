<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$prop_sql=sql_select("select trans_type, trans_id, dtls_id
from order_wise_pro_details
where entry_form  = 15 and status_active = 1 ");

if(empty($prop_sql))
{
	echo "Data Not Found";
	die;
}

foreach($prop_sql as $val)
{
	$dtls_arr[$val[csf("dtls_id")]] = $val[csf("dtls_id")];
	if($val[csf("trans_type")] == 5){
		$dtlsBatch[$val[csf("dtls_id")]][5] = $val[csf("trans_id")];
	}else{
		$dtlsBatch[$val[csf("dtls_id")]][6] = $val[csf("trans_id")];
	}
	
}

$dtls_arr = array_filter($dtls_arr);

$batch_id = "";
foreach ($dtls_arr as  $dtls_id) 
{
	$trans_id =  $dtlsBatch[$dtls_id][6];
	$to_trans_id =  $dtlsBatch[$dtls_id][5];
	echo "update inv_item_transfer_dtls set trans_id = '".$trans_id. "', to_trans_id= '".$to_trans_id."',  updated_by = 999 where id = ".$dtls_id." <br>";
	//execute_query("update inv_item_transfer_dtls set trans_id = '".$trans_id. "', to_trans_id= '".$to_trans_id."'  updated_by = 999 where id = ".$dtls_id,0);
}

/*
oci_commit($con);
echo "Success"; 
die;*/


?>