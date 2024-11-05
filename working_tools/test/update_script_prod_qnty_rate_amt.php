<?
include('../includes/common.php');
$con = connect();


//,6,7,23 and b.prod_id <> 15883
$sql_dyes_trans="select a.id as prod_id, a.lot, a.product_name_details, a.avg_rate_per_unit, a.current_stock, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty, sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt
 from product_details_master a, inv_transaction b
 where a.id=B.PROD_ID and a.status_active=1 and b.status_active=1 and a.item_category_id=1 and b.item_category=1
 group by a.id, a.lot, a.product_name_details, a.avg_rate_per_unit, a.current_stock
 having a.current_stock <> sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) 
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
	
	//echo "update product_details_master set avg_rate_per_unit='".$prod_rate."', current_stock='".$row[csf("bal_qnty")]."', stock_value='".$row[csf("bal_amt")]."' where id=".$row[csf("prod_id")]."  ";oci_rollback($con);die;
	
	$upTransID=execute_query("update product_details_master set avg_rate_per_unit='".$prod_rate."', current_stock='".$row[csf("bal_qnty")]."', stock_value='".$row[csf("bal_amt")]."' where id=".$row[csf("prod_id")]." ");
	if($upTransID){ $upTransID=1; } else {echo "update product_details_master set avg_rate_per_unit='".$prod_rate."', current_stock='".$row[csf("bal_qnty")]."', stock_value='".$row[csf("bal_amt")]."' where id=".$row[csf("prod_id")]."  ";oci_rollback($con);die;}
}
//echo "<pre>";print_r($rcv_data);die;
if($db_type==2)
{
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