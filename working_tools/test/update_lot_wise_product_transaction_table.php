<?
include('../includes/common.php');
$con = connect();
//echo $con;die;
$sql_prod="Select ID, COMPANY_ID, ITEM_GROUP_ID, ITEM_DESCRIPTION, LOT
 from product_details_master where status_active=1 and is_deleted=0 and item_category_id in(5,6,7,23)";
$sql_prod_result=sql_select($sql_prod);
$prod_data=array();
foreach($sql_prod_result as $row)
{
	$prod_data[$row["COMPANY_ID"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]][$row["LOT"]]=$row["ID"];
}

$sql_trans="Select a.ID, a.COMPANY_ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, b.ID as TRANS_ID, b.BATCH_LOT
 from product_details_master a, inv_transaction b where a.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id in(5,6,7,23) and b.item_category in(5,6,7,23)";
$sql_trans_result=sql_select($sql_trans);
$upTrans=true;
$prod_check=array();
foreach($sql_trans_result as $row)
{
	$prod_id=$prod_data[$row["COMPANY_ID"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]][$row["BATCH_LOT"]];
	if($prod_id)
	{
		$upTrans=execute_query("update inv_transaction set prod_id='$prod_id' where id='".$row["TRANS_ID"]."'");
		if($upTrans){ $upTrans=1; } else { echo "update inv_transaction set prod_id='$prod_id' where id='".$row["TRANS_ID"]."'"; oci_rollback($con);disconnect($con);die;}
	}
}

if($db_type==2)
{
	if($upTrans)
	{
		oci_commit($con); 
		echo "Transaction Data Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Transaction Data Insert Failed";
		die;
	}
}
?>