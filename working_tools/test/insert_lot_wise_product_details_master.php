<?
include('../includes/common.php');
$con = connect();
//echo $con;die;
$sql_prod="Select PROD_ID, BATCH_LOT
 from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)
 group by  PROD_ID, BATCH_LOT
 order by PROD_ID";
 //echo $sql_prod;die;
$sql_prod_result=sql_select($sql_prod);
$upProd=true;
$prod_check=array();
foreach($sql_prod_result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$previous_prod_id=$row["PROD_ID"];
		$prod_scrtipt=execute_query("update product_details_master set lot='".$row["BATCH_LOT"]."' where id=$previous_prod_id");
		if($upProd){ $upProd=1; } else { echo "update product_details_master set lot='".$row["BATCH_LOT"]."' where id=$previous_prod_id"; oci_rollback($con);disconnect($con);die;}
	}
	else
	{
		$txt_product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		$prod_scrtipt=execute_query("insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date) 
		select	
		'$txt_product_id', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), '".$row["BATCH_LOT"]."', item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date from product_details_master where id=$previous_prod_id");
		if($upProd){ $upProd=1; } else { echo "insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date) 
		select	
		'$txt_product_id', company_id, supplier_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, trim(product_name_details), '".$row["BATCH_LOT"]."', item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, order_uom, conversion_factor, origin, inserted_by, insert_date from product_details_master where id=$previous_prod_id"; oci_rollback($con);disconnect($con);die;}
					
	}
	$i++;
}
//echo "<pre>";print_r($rcv_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upProd)
	{
		oci_commit($con); 
		echo "Lot Wise Product Data Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Lot Wise Product Data Insert Failed";
		die;
	}
}
?>