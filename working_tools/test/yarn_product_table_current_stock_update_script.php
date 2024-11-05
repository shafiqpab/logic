<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}

$mis_match_stock_sql = " SELECT b.prod_id,round (c.current_stock,2) as current_stock, SUM ( CASE WHEN b.transaction_type IN (1, 4, 5) THEN b.cons_quantity ELSE 0 END) AS rcv, SUM ( CASE WHEN b.transaction_type IN (2, 3, 6) THEN b.cons_quantity ELSE 0 END) AS issue, SUM ( (CASE WHEN b.transaction_type IN (1, 4, 5) THEN b.cons_quantity ELSE 0 END) - (CASE WHEN b.transaction_type IN (2, 3, 6) THEN b.cons_quantity ELSE 0 END)) AS bal_qnty, SUM ( (CASE WHEN b.transaction_type IN (1, 4, 5) THEN b.cons_amount ELSE 0 END) - (CASE WHEN b.transaction_type IN (2, 3, 6) THEN b.cons_amount ELSE 0 END)) AS bal_amount FROM inv_transaction b,product_details_master c
WHERE b.prod_id=c.id and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0  and b.item_category =1  and c.item_category_id=1  GROUP BY b.prod_id,c.current_stock  having  round (( SUM ( (CASE WHEN b.transaction_type IN (1, 4, 5) THEN b.cons_quantity ELSE 0 END) - (CASE WHEN b.transaction_type IN (2, 3, 6) THEN b.cons_quantity ELSE 0 END)) ) ,2) != round(c.current_stock,2)  
";

//echo $mis_match_stock_sql; die();and b.prod_id=12111


$mis_match_stock_result = sql_select($mis_match_stock_sql);
if(!empty($mis_match_stock_result))
{
	$delete_id_arr = array(); 
	foreach ($mis_match_stock_result as $row) 
	{
		$prod_id 					= $row[csf("prod_id")];
		$new_current_stock 			= $row[csf("bal_qnty")];
		$new_current_stock_value	= $row[csf("bal_amount")];		
		
		if($new_current_stock>0 && $new_current_stock_value>0)
		{
			$new_avg_rate = $new_current_stock_value/$new_current_stock;
		}else{
			$new_avg_rate = 0;
		}
        
		$update_product_sql =execute_query("update product_details_master set current_stock=$new_current_stock,stock_value='$new_current_stock_value',avg_rate_per_unit=$new_avg_rate WHERE id = $prod_id");

		if($update_product_sql) $update_product_sql=1; else {echo "update product_details_master set current_stock=$new_current_stock,stock_value='$new_current_stock_value',avg_rate_per_unit=$new_avg_rate WHERE id = $prod_id";oci_rollback($con);die;}
		$update_product_sql_2 =execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) WHERE id = $prod_id");
		if($update_product_sql_2) $update_product_sql_2=1; else {echo "update product_details_master set available_qnty=(current_stock-allocated_qnty) WHERE id = $prod_id";oci_rollback($con);die;}
	}
}

if ($update_product_sql && $update_product_sql_2) {
	oci_commit($con); 
    echo "Success";
    die; 
}
?>