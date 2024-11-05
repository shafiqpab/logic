<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$delivery_sql=sql_select("select a.id, a.po_breakdown_id, b.po_breakdown_id as roll_po, a.insert_date 
from order_wise_pro_details a, pro_roll_details b
where a.entry_form =2 and b.entry_form =2 and a.dtls_id = b.dtls_id  and a.po_breakdown_id is null  
order by a.insert_date 
");


if(empty($delivery_sql))
{
	echo "Data Not Found";
	die;
}


foreach ($delivery_sql as  $row) 
{

	echo "update order_wise_pro_details set po_breakdown_id = '".$row[csf('roll_po')]."' where id = ".$row[csf('id')]." <br>";
	//die;
	//execute_query("update inv_transaction set fabric_shade = '".$issue_dtls_fabric_shade_arr[$tId]["fabric_shade"]."',  updated_by = 999 where id = ".$tId,0);
}


/*oci_commit($con);
echo "Success"; 
die;*/



?>