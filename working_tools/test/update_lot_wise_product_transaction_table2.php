<?
include('../includes/common.php');
$con = connect();
//echo $con;die;
$sql_prod="Select PROD_ID, BATCH_LOT
 from inv_transaction where status_active=1 and is_deleted=0 and item_category in(22) and transaction_type in(1,5) and BATCH_LOT is not null and prod_id in(84256,84256,84256,84256,84256,92504,378630,378630,378637,378637,378642,378642,378643,378643,378643,402382,402382,402382,402382,402383,402383,402383,402384,402384,402384,402386,402386,402388,402389,402392,402393,402393,402396,406007,406007,409011,409011,409011,479755,479755,479757,479757)
 order by ID";
$sql_prod_result=sql_select($sql_prod);
$prod_data=array(); $prod_check=array();
foreach($sql_prod_result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$prod_data[$row["PROD_ID"]]=$row["BATCH_LOT"];
	}
}

$sql_trans="Select b.ID, b.PROD_ID
 from inv_transaction b where b.status_active=1 and b.is_deleted=0 and b.PROD_ID  in(84256,84256,84256,84256,84256,92504,378630,378630,378637,378637,378642,378642,378643,378643,378643,402382,402382,402382,402382,402383,402383,402383,402384,402384,402384,402386,402386,402388,402389,402392,402393,402393,402396,406007,406007,409011,409011,409011,479755,479755,479757,479757)";
$sql_trans_result=sql_select($sql_trans);
$upTrans=true;
foreach($sql_trans_result as $row)
{
	$prod_lot=$prod_data[$row["PROD_ID"]];
	echo "update inv_transaction set batch_lot='$prod_lot' where id='".$row["ID"]."'"; oci_rollback($con);disconnect($con);die;
	$upTrans=execute_query("update inv_transaction set batch_lot='$prod_lot' where id='".$row["ID"]."'");
	if($upTrans){ $upTrans=1; } else { echo "update inv_transaction set batch_lot='$prod_lot' where id='".$row["ID"]."'"; oci_rollback($con);disconnect($con);die;}
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