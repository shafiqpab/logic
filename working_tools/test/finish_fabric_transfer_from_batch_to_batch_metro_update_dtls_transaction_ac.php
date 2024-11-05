<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$details_sql=sql_select("select b.id,b.batch_id,b.to_batch_id, b.mst_id, b.trans_id, b.to_trans_id
from inv_item_transfer_mst a, inv_item_transfer_dtls b
where  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
and b.is_deleted=0 and a.entry_form = 14 and a.transfer_criteria = 2 and b.batch_id != b.to_batch_id and b.status_active =1");

if(empty($details_sql))
{
	echo "Data Not Found";
	die;
}

foreach($details_sql as $val)
{
	$batch_id = $val[csf("batch_id")];
	echo "update inv_item_transfer_dtls set to_batch_id=$batch_id where id = ".$val[csf("id")]." <br />";
	//execute_query("update inv_item_transfer_dtls set to_batch_id=$body_part_id where id = ".$val[csf("id")],0);
}

$trans_sql=sql_select("select b.batch_id, c.id as mst_id , a.id as trans_id
from  inv_transaction a, inv_item_transfer_dtls b, inv_item_transfer_mst c 
where a.id = b.to_trans_id and b.mst_id = c.id and c.entry_form = 14 and c.transfer_criteria = 2 
and b.item_category =2 and b.batch_id != a.pi_wo_batch_no and a.transaction_type=5 and a.status_active =1 and b.status_active =1");

foreach($trans_sql as $val)
{
	$batch_id = $val[csf("batch_id")];
	echo "update inv_transaction set pi_wo_batch_no=$batch_id where id = ".$val[csf("trans_id")]." <br />";
	//execute_query("update inv_transaction set body_part_id=$body_part_id where id=".$val[csf("id")],0);
}





//oci_commit($con);
//mysql_query("COMMIT");
//echo "Success";
die;


?>