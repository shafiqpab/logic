<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();




$delivery_sql=sql_select("select  ID, GREY_RECEIVE_QNTY, ORDER_ID, PROD_ID, COLOR_ID ,UPDATED_BY, STATUS_ACTIVE, IS_DELETED

from PRO_GREY_PROD_ENTRY_DTLS
where id in (494805,495082,495112,495222,495224,495246,495259,495260,495262,495264,495265,495280,495283,495286,495291,495294,495295,495297,495298,495300,495304,495306)
");
/*print_r($delivery_sql);
die;*/

if(empty($delivery_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($delivery_sql as  $row) 
{
	//echo "update order_wise_pro_details set po_breakdown_id = ".$row['ORDER_ID'].", prod_id = ".$row['PROD_ID'].", quantity=".$row['GREY_RECEIVE_QNTY'].", color_id='".$row['COLOR_ID']."', updated_by='".$row['UPDATED_BY']."', status_active=".$row['STATUS_ACTIVE'].", is_deleted=".$row['IS_DELETED']." where entry_form=2 and dtls_id=".$row['ID']."  <br>";
	//die;
	
	$color = explode(",",$row['COLOR_ID']);
	if(count($color)>1)
	{
		$color_id = 0;
	}else{
		$color_id = $row['COLOR_ID'];
	}


	execute_query("update order_wise_pro_details set po_breakdown_id = ".$row['ORDER_ID'].", prod_id = ".$row['PROD_ID'].", quantity=".$row['GREY_RECEIVE_QNTY'].", color_id='".$color_id."', updated_by='".$row['UPDATED_BY']."', status_active=".$row['STATUS_ACTIVE'].", is_deleted=".$row['IS_DELETED']." where entry_form=2 and dtls_id=".$row['ID'],0);

}


oci_commit($con);
echo "Success"; 
die;



?>