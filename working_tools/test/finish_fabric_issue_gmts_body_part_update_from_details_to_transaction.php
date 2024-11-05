<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();



$details_sql=sql_select("select a.body_part_id ,b.id from inv_finish_fabric_issue_dtls a, inv_transaction b,inv_issue_master c  where c.id=a.mst_id and a.trans_id = b.id  and c.entry_form=18 and a.status_active =1 and b.status_active =1 and b.transaction_type= 2 and b.item_category=2 and b.body_part_id =0 and b.body_part_id != a.body_part_id");

if(empty($details_sql))
{
	echo "Data Not Found";
	die;
}

foreach($details_sql as $val)
{
	$body_part_id = $val[csf("body_part_id")];
	//echo "update inv_transaction set body_part_id=$body_part_id where id = ".$val[csf("id")]." <br />";
	execute_query("update inv_transaction set body_part_id=$body_part_id where id=".$val[csf("id")],0);
	
}

oci_commit($con);
//mysql_query("COMMIT");
echo "Success";
die;


?>