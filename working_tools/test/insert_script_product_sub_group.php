<?
include('../includes/common.php');
$con = connect();
//$user_id=$_SESSION['logic_erp']['user_id'];
//echo $con."=".$user_id;die;
/*
###########for conversion factor update#############

update product_details_master a set a.conversion_factor=(select b.conversion_factor from lib_item_group b where a.item_group_id=b.id) 
where a.status_active=1 and (a.ITEM_CATEGORY_ID not in(1,2,3,12,13,14,24,25) or a.entry_form <>24)

update product_details_master a set a.order_uom=(select b.order_uom from lib_item_group b where a.item_group_id=b.id) 
where a.status_active=1 and (a.ITEM_CATEGORY_ID not in(1,2,3,12,13,14,24,25) or a.entry_form <>24)

*/
$sql_prod="select ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_CODE, SUB_GROUP_NAME 
from product_details_master where status_active=1 and (ITEM_CATEGORY_ID not in(1,2,3,12,13,14,24,25) or entry_form <>24)
group by ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_CODE, SUB_GROUP_NAME";
$sql_prod_result=sql_select($sql_prod);
$upTransID=true;
$id=return_next_id("id","lib_item_sub_group",1);
$i=1;
foreach($sql_prod_result as $row)
{
	if($row["SUB_GROUP_CODE"]!="" || $row["SUB_GROUP_NAME"]!="")
	{
		$tst_data[$i]["SUB_GROUP_CODE"]=$row["SUB_GROUP_CODE"];
		$tst_data[$i]["SUB_GROUP_NAME"]=$row["SUB_GROUP_NAME"];
		$i++;
		$upTransID=execute_query("insert into lib_item_sub_group (ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, INSERTED_BY, INSERT_DATE) values (".$id.",'".$row["ITEM_CATEGORY_ID"]."','".$row["ITEM_GROUP_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','1','".$pc_date_time."')");
	
		if($upTransID){ $upTransID=1; } else {echo "insert into lib_item_sub_group (ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, INSERTED_BY, INSERT_DATE) values (".$id.",'".$row["ITEM_CATEGORY_ID"]."','".$row["ITEM_GROUP_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','1','".$pc_date_time."')";oci_rollback($con);die;}
		$id++;
	}
	
}
//echo count($tst_data)."<pre>";print_r($tst_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
	{
		oci_commit($con); 
		echo "Sub Group Wise Data Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Sub Group Wise Data Insert Failed";
		die;
	}
}
?>