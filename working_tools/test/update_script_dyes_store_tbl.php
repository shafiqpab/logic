<?
include('../includes/common.php');
$con = connect();

//,6,7,23 and b.prod_id <> 15883
$sql_dyes_trans="select prod_id, store_id,
 sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty,
 sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt
 from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(27686,27687,27689,27690,27821,27823,27824,27825,27832,27836,28695,28696,28698,28721,28722,28724,28732,28733,43737,45528)
 group by prod_id, store_id
 order by prod_id";
$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($result as $row)
{
	$prod_rate=0;
	if($row[csf("bal_amt")] > 0 && $row[csf("bal_qnty")] > 0)
	{
		$prod_rate=number_format(($row[csf("bal_amt")]/$row[csf("bal_qnty")]),6,'.','');
	}
	
	$upTransID=execute_query("update inv_store_wise_qty_dtls set rate='".$prod_rate."', cons_qty='".$row[csf("bal_qnty")]."', amount='".$row[csf("bal_amt")]."' where prod_id=".$row[csf("prod_id")]." and store_id=".$row[csf("store_id")]." ");
	if($upTransID){ $upTransID=1; } else {echo "update inv_store_wise_qty_dtls set rate='".$prod_rate."', cons_qty='".$row[csf("bal_qnty")]."', amount='".$row[csf("bal_amt")]."' where prod_id=".$row[csf("prod_id")]." and store_id=".$row[csf("store_id")]." ";oci_rollback($con);die;}
}
//echo "<pre>";print_r($rcv_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
	{
		oci_commit($con); 
		echo "Porduct Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Porduct Data Update Failed";
		die;
	}
}
?>