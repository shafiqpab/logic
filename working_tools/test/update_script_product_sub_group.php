<?
include('../includes/common.php');
$con = connect();
//$user_id=$_SESSION['logic_erp']['user_id'];
//echo $con."=".$user_id;die;
/*
for conversion factor update
update product_details_master a set a.conversion_factor=(select b.conversion_factor from lib_item_group b where a.item_group_id=b.id) 
where a.status_active=1 and (a.ITEM_CATEGORY_ID not in(1,2,3,12,13,14,24,25) or a.entry_form <>24)
*/
$sql_sub_group="select b.ID, b.ITEM_CATEGORY_ID, b.ITEM_GROUP_ID, b.SUB_GROUP_CODE, b.SUB_GROUP_NAME from LIB_ITEM_SUB_GROUP b";
$sql_sub_group_result=sql_select($sql_sub_group);
$sub_group_data=array();
foreach($sql_sub_group_result as $val)
{
	$sub_group_data[$val["ITEM_CATEGORY_ID"]][$val["ITEM_GROUP_ID"]][$val["SUB_GROUP_CODE"]][$val["SUB_GROUP_NAME"]]=$val["ID"];
}
//echo count($sub_group_data)."<pre>";print_r($sub_group_data);die;
$sql_prod="select ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_CODE, SUB_GROUP_NAME from product_details_master where status_active=1 and (ITEM_CATEGORY_ID not in(1,2,3,12,13,14,24,25) or entry_form <>24)";
$sql_prod_result=sql_select($sql_prod);
$upTransID=true;
$i=1;
foreach($sql_prod_result as $row)
{
	$sub_group_id=$sub_group_data[$row["ITEM_CATEGORY_ID"]][$row["ITEM_GROUP_ID"]][$row["SUB_GROUP_CODE"]][$row["SUB_GROUP_NAME"]];
	$prod_id=$row["ID"];
	if($sub_group_id!="")
	{
		$tst_data[$i]["SUB_GROUP_CODE"]=$row["SUB_GROUP_CODE"];
		$tst_data[$i]["SUB_GROUP_NAME"]=$row["SUB_GROUP_NAME"];
		$i++;
		$upTransID=execute_query("update product_details_master set ITEM_SUB_GROUP_ID=$sub_group_id where id=$prod_id");
	
		if($upTransID){ $upTransID=1; } else {echo "update product_details_master set ITEM_SUB_GROUP_ID=$sub_group_id where id=$prod_id";oci_rollback($con);die;}
	}
	
}
//echo count($tst_data)."<pre>";print_r($tst_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
	{
		oci_commit($con); 
		echo "Product Sub Group Id Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Product Sub Group Id Update Failed";
		die;
	}
}
?>