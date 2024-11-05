<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$delivery_sql=sql_select("SELECT a.barcode_no, c.po_breakdown_id as PROP_ORDER, b.po_breakdown_id as ACTUAL_ORDER, c.id as ID
from pro_roll_details a, pro_roll_details b, order_wise_pro_details c
where a.barcode_no= b.barcode_no and a.FROM_ROLL_ID = b.id and a.dtls_id= c.DTLS_ID and a.entry_form=133  
and c.entry_form=133 and a.status_active=1 and c.status_active=1 and b.PO_BREAKDOWN_ID != c.PO_BREAKDOWN_ID and c.trans_type=6 
and a.barcode_no like '2202%'
order by a.barcode_no desc");

/*print_r($delivery_sql);
die;*/

if(empty($delivery_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($delivery_sql as  $row) 
{
	if($row['ACTUAL_ORDER'] !="")
	{
		//echo "update order_wise_pro_details set po_breakdown_id = ".$row['ACTUAL_ORDER']." where entry_form=133 and id=".$row['ID']."  <br>";

		execute_query("update order_wise_pro_details set po_breakdown_id = ".$row['ACTUAL_ORDER']." where entry_form=133 and id=".$row['ID'],0);
	}
	

}

oci_commit($con);
echo "Success"; 
die; 



?>