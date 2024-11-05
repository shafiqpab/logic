<?
include('../includes/common.php');
$con = connect();

$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}

$recv_sql="select a.ID, a.ORDER_RATE, a.CONS_QUANTITY, p.EXCHANGE_RATE, b.ITEM_GROUP_ID 
from INV_RECEIVE_MASTER p, INV_TRANSACTION a, PRODUCT_DETAILS_MASTER b
where p.id=a.mst_id and a.prod_id=b.id and b.entry_form<>24 and p.entry_form=20 and p.exchange_rate>1 and p.status_active=1 and a.status_active=1 and a.transaction_type=1 and a.item_category in(select category_id from lib_item_category_list where category_type=1)";

$recv_sql_result=sql_select($recv_sql);
//echo count($recv_sql_result);die;
$trans_rid=true;
foreach($recv_sql_result as $row)
{
	$trans_id=$row["ID"];
	$cons_rate=(($row["ORDER_RATE"]/$trim_group_arr[$row['ITEM_GROUP_ID']]['conversion_factor'])*$row['EXCHANGE_RATE']);
	$cons_amount=$cons_rate*$row['CONS_QUANTITY'];
	$trans_rid=execute_query("update INV_TRANSACTION set cons_rate='$cons_rate', CONS_AMOUNT='$cons_amount' where id=$trans_id");
	if($trans_rid==false)
	{
		echo "update INV_TRANSACTION set cons_rate='$cons_rate', CONS_AMOUNT='$cons_amount' where id=$trans_id";oci_rollback($con);disconnec($con);die;
	}
	
}

//echo $rID;die;
//echo $rID."<br>".$rID2;die;

if($trans_rid)
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