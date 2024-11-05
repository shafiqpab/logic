<?
include('../includes/common.php');
$con = connect();

$prod_sql="select ID, CURRENT_STOCK from product_details_master where item_category_id in(1) order by ID";

$prod_sql_result=sql_select($prod_sql);
$prod_data_arr=array();
foreach($prod_sql_result as $row)
{
	$prod_data_arr[$row["ID"]]=$row["CURRENT_STOCK"];
}
//echo "<pre>";print_r($prod_data_arr);die;

$trans_sql=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt
from inv_transaction where status_active=1 and is_deleted=0 and item_category in(1)
group by prod_id
order by prod_id");
$trans_data_arr=array();
foreach($trans_sql as $row)
{
	if($row[csf("bal")]==0) $bal_amt=0; else $bal_amt=$row[csf("bal_amt")];
	$trans_data_arr[$row[csf("prod_id")]]["bal"]=$row[csf("bal")];
	$trans_data_arr[$row[csf("prod_id")]]["bal_amt"]=$bal_amt;
}
//echo "<pre>";print_r($trans_data_arr);
$upProdId=true;
foreach($trans_data_arr as $prod_id=>$val)
{
	$prod_stock=$prod_data_arr[$prod_id];
	if($prod_stock != $val["bal"])
	{
		if($val["bal"]>0 && $val["bal_amt"]>0) $avg_rate=$val["bal_amt"]/$val["bal"]; else $avg_rate=0;
		$upProdId=execute_query("update product_details_master set avg_rate_per_unit=$avg_rate, current_stock='".$val["bal"]."', stock_value='".$val["bal_amt"]."', updated_by=1, update_date='".$pc_date_time."' where id=$prod_id");
		if($upProdId)
		{
			$upProdId=1;
		}
		else
		{
			$upProdId=0;
			echo "update product_details_master set avg_rate_per_unit=$avg_rate, current_stock='".$val["bal"]."', stock_value='".$val["bal_amt"]."', updated_by=1, update_date='".$pc_date_time."' where id=$prod_id";oci_rollback($con);die;
		}
	}
}

if($db_type==2)
{
	if($upProdId)
	{
		oci_commit($con); 
		echo "Product Data Update Successfully. <br>";
	}
	else
	{
		oci_rollback($con);
		echo "Product Data Update Failed";
		die;
	}
}
die;
?>