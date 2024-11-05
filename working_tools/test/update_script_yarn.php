<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();



$issue_trans_sql = "select max(id) as id, prod_id from inv_transaction where status_active=1 and item_category=1 and transaction_type=2 and prod_id in(select b.prod_id
from inv_transaction b
where b.status_active=1 and item_category=1
group by b.prod_id
having sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) < 1 
and round(sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)),5) >0)
group by prod_id";
$issue_trans_sql_result=sql_select($issue_trans_sql);
$issue_data=array();
foreach($issue_trans_sql_result as $row)
{
	$issue_data[$row[csf("prod_id")]]=$row[csf("id")];
}
unset($issue_trans_sql_result);
echo "<pre>";print_r($issue_data);//die;
$propotion_sql = "select max(id) as id, prod_id from order_wise_pro_details where status_active=1 and entry_form=3 and trans_type=2 and prod_id in(select b.prod_id
from inv_transaction b
where b.status_active=1 and item_category=1
group by b.prod_id
having sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) < 1 
and round(sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)),5) >0)
group by prod_id";

$propotion_sql_result=sql_select($propotion_sql);
$propotion_data=array();
foreach($propotion_sql_result as $row)
{
	$propotion_data[$row[csf("prod_id")]]=$row[csf("id")];
}
unset($issue_trans_sql_result);
echo "<pre>";print_r($propotion_data);die;


$yarn_sql = "select b.prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty, 
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt
from inv_transaction b
where b.status_active=1 and item_category=1
group by b.prod_id
having sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) < 1 
and round(sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)),5) >0
order by prod_id";
$yarn_sql_result = sql_select($yarn_sql);
echo count($yarn_sql_result);die;
$upTransID=$upPropo=$upProd=true;
foreach ($yarn_sql_result as $row)
{
	$bal_qnty=$row[csf("bal_qnty")];
	if($issue_data[$row[csf("prod_id")]])
	{
		$upTransID=execute_query("update inv_transaction set cons_quantity=(cons_quantity+$bal_qnty), cons_amount=(cons_quantity+$bal_qnty)*cons_rate where id=".$issue_data[$row[csf("prod_id")]]." ");
		if($upTransID){ $upTransID=1; } else {echo "update inv_transaction set cons_quantity=(cons_quantity+$bal_qnty), cons_amount=(cons_quantity+$bal_qnty)*cons_rate where id=".$issue_data[$row[csf("prod_id")]]." ";oci_rollback($con);die;}
	}
	
	
	if($propotion_data[$row[csf("prod_id")]])
	{
		$upPropo=execute_query("update order_wise_pro_details set quantity=(quantity+$bal_qnty) where id=".$propotion_data[$row[csf("prod_id")]]." ");
		if($upPropo){ $upPropo=1; } else {echo "update order_wise_pro_details set quantity=(quantity+$bal_qnty) where id=".$propotion_data[$row[csf("prod_id")]]." ";oci_rollback($con);die;}
	}
	
	
	$upProd=execute_query("update product_details_master set avg_rate_per_unit=0, current_stock=0, stock_value=0 where id=".$row[csf("prod_id")]." ");
	if($upProd){ $upProd=1; } else {echo "update product_details_master set avg_rate_per_unit=0, current_stock=0, stock_value=0 where id=".$row[csf("prod_id")]." ";oci_rollback($con);die;}
}



//echo count(array_unique($maintable_id));

//die();
//echo $upTransID ."&& ". $upPropo ."&& ". $upProd; die();
//echo $db_type;die;
if ($db_type == 0) {
	if ($upTransID && $upPropo &&  $upProd) {
		mysql_query("COMMIT");
		echo "0**" . "success";
	} else {
		mysql_query("ROLLBACK");
		echo "10**"."fail";
	}
} else if ($db_type == 2 || $db_type == 1) {

	if ($upTransID && $upPropo &&  $upProd) 
	{
		oci_commit($con);
		echo "0**"."success";
	} else {
		oci_rollback($con);
		echo "10**fail";
	}
}
disconnect($con);
die();
?>
