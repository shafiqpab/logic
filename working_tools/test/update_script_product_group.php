<?
include('../includes/common.php');
$con = connect();

$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
//echo "<pre>";print_r($item_group_arr);die;
$sql_dyes_trans="select ID, ITEM_GROUP_ID, ITEM_DESCRIPTION, ITEM_SIZE from product_details_master where status_active=1 and is_deleted=0 and ITEM_CATEGORY_ID in(select category_id from lib_item_category_list where category_type=1) and ITEM_CATEGORY_ID not in(1,2,3,5,6,7,13,14,23) AND entry_form<>24
order by ID";

//echo $sql_dyes_trans;die;
$result=sql_select($sql_dyes_trans);
echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upProdID=true;
foreach($result as $row)
{
	
	$productname = $item_group_arr[$row["ITEM_GROUP_ID"]]." ".$row["ITEM_DESCRIPTION"];
	if($row["ITEM_SIZE"]!="") $productname .=" ".$row["ITEM_SIZE"];
	
	$upProdID=execute_query("update product_details_master set PRODUCT_NAME_DETAILS='".$productname."' where id=".$row["ID"]." ");
	if($upProdID){ $upProdID=1; } else {echo"update product_details_master set PRODUCT_NAME_DETAILS='".$productname."' where id=".$row["ID"]."";oci_rollback($con);die;}
}

if($db_type==2)
{
	if($upProdID)
	{
		oci_commit($con); 
		echo "Product Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Product Data Update Failed";
		die;
	}
}
?>