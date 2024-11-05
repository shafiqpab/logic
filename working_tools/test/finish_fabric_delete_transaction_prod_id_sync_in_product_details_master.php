<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$sql=sql_select("select prod_id, sum(case when transaction_type in (1,4,5) then cons_quantity else 0 end) as rcv, sum(case when transaction_type in (2,3,6) then cons_quantity else 0 end) as iss, sum(case when transaction_type in (1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in (2,3,6) then cons_quantity else 0 end) as bal
from inv_transaction a, pro_batch_create_mst b, wo_booking_mst c
where a.item_category = 2 and a.store_id = 20 and a.status_active =1 and a.insert_date >= to_timestamp('2020-03-01', 'YYYY-MM-DD') 
and a.pi_wo_batch_no= b.id and b.booking_no= c.booking_no and c.fabric_source=2
group by prod_id
order by prod_id");

foreach($sql as $val)
{
	$prod_id_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
}

$product_sql	= sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in (".implode(",", $prod_id_arr).")");
foreach ($product_sql as  $val) 
{
	$stock_arr[$val[csf("id")]]["current_stock"] = $val[csf("current_stock")];
	$stock_arr[$val[csf("id")]]["avg_rate_per_unit"] = $val[csf("avg_rate_per_unit")];
}

foreach ($sql as  $val) 
{
	$stock_qnty  = $stock_arr[$val[csf("prod_id")]]["current_stock"] - $val[csf("bal")];
	if($stock_qnty<0)
	{
		$stock_qnty = 0;
	}
	$stock_value  = $stock_arr[$val[csf("prod_id")]]["avg_rate_per_unit"] * $stock_qnty;

	echo "update product_details_master set current_stock = '".$stock_qnty."', stock_value='".$stock_value."' where id = ".$val[csf("prod_id")]." <br>";

	//die;
	//execute_query("update inv_transaction set fabric_shade = '".$issue_dtls_fabric_shade_arr[$tId]["fabric_shade"]."',  updated_by = 999 where id = ".$tId,0);
}


/*oci_commit($con);
echo "Success"; 
die;*/



?>