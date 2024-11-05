<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();




$delivery_sql=sql_select("select  ID,  PO_BREAKDOWN_ID, PROD_ID, QUANTITY, COLOR_ID, UPDATED_BY, REJECT_QTY, STATUS_ACTIVE, IS_DELETED, IS_SALES
from order_wise_pro_details@BPGTEST1 
where TRANS_ID = 0
AND ENTRY_FORM  in (2,7,66,6)
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
	//echo "update order_wise_pro_details set po_breakdown_id = ".$row['PO_BREAKDOWN_ID'].", prod_id = ".$row['PROD_ID'].", quantity=".$row['QUANTITY'].", color_id=".$row['COLOR_ID'].", updated_by='".$row['UPDATED_BY']."', reject_qty=".$row['REJECT_QTY'].", status_active=".$row['STATUS_ACTIVE'].", is_deleted=".$row['IS_DELETED'].", is_sales=".$row['IS_SALES']." where id=".$row['ID']."  <br>";
	//die;

	execute_query("update order_wise_pro_details set po_breakdown_id = ".$row['PO_BREAKDOWN_ID'].", prod_id = ".$row['PROD_ID'].", quantity=".$row['QUANTITY'].", color_id='".$row['COLOR_ID']."', updated_by='".$row['UPDATED_BY']."', reject_qty='".$row['REJECT_QTY']."', status_active=".$row['STATUS_ACTIVE'].", is_deleted=".$row['IS_DELETED'].", is_sales='".$row['IS_SALES']."' where id=".$row['ID'],0);

}


oci_commit($con);
echo "Success"; 
die;



?>