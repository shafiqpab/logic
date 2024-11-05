<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$sqls="SELECT id, sub_group_name from product_details_master where status_active=1 and is_deleted=0 ";
$row_data=sql_select($sqls);
$product_arr=array();
foreach($row_data as $k=>$val)
{
	$product_arr[$val[csf("id")]]=$val[csf("sub_group_name")];
}

$sqls2="SELECT id, product_id from inv_itemissue_requisition_dtls where status_active=1 and is_deleted=0 ";
$row_data2=sql_select($sqls2);

foreach($row_data2 as $k=>$val)
{
	
	$id=$val[csf("id")];
	$product_id=$val[csf("product_id")];
	$sub_group_name=$product_arr[$val[csf("product_id")]];
	
	$up_dtls=execute_query("UPDATE inv_itemissue_requisition_dtls set item_sub_group='$sub_group_name' where id=$id and product_id='$product_id'");

	 
}


	oci_commit($con); 
	echo "Success";





 
?>