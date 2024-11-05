<?

/*$server='localhost';
$user='root';
$db_name='logic_erp_3rd_version';
$passwd='';
$con = mysql_connect( $server, $user, $passwd );
if(!$con)
{
	trigger_error("Problem connecting to server");
}

$DB =  mysql_select_db($db_name, $con);
if(!$DB)
{
	trigger_error("Problem selecting database");
}*/

include('includes/common.php');
$con = connect();

$sql_prod_prev=sql_select("select item_category_id, item_group_id, sub_group_name, item_description, item_size
from product_details_master
where status_active=1 and is_deleted=0 and item_category_id not in(1,2,3,12,13,14,24,25) and entry_form <>24  and company_id=3
group by item_category_id, item_group_id, sub_group_name, item_description, item_size");

$prev_data=array();

foreach($sql_prod_prev as $row)
{
	$prev_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("sub_group_name")]][$row[csf("item_description")]][$row[csf("item_size")]]=$row[csf("item_description")];
}



$sql_prod=sql_select("select max(id) as prod_id, company_id , item_category_id, item_group_id, sub_group_name, item_description, item_size
from product_details_master
where status_active=1 and is_deleted=0 and item_category_id not in(1,2,3,12,13,14,24,25) and entry_form <>24  and company_id=1
group by company_id , item_category_id, item_group_id, sub_group_name, item_description, item_size");

$product_id=return_next_id( "id", "product_details_master", 1 ) ;


foreach($sql_prod as $row)
{
	if($prev_data[$row[csf("item_category_id")]][$row[csf("item_group_id")]][$row[csf("sub_group_name")]][$row[csf("item_description")]][$row[csf("item_size")]]=="")
	{
		$pord_id=$row[csf("prod_id")];
		$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date,entry_form) 
			select	
			'$product_id', 3, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, 0, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, entry_form from product_details_master where id=$pord_id";
		$prod=execute_query($sql_prod_insert,1);
		$product_id++;
	}
}


oci_commit($con);
die;



?>