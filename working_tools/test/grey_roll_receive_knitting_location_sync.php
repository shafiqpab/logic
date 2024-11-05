<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$receive_id_sql =  sql_select("select a.location_id, c.knitting_location_id,c.recv_number, c.id from pro_grey_prod_delivery_mst a, pro_roll_details b , inv_receive_master c,  pro_roll_details d where a.id = b.mst_id and b.entry_form =56 and b.status_active =1 and c.id = d.mst_id and d.entry_form =58 and c.knitting_source=1 and d.status_active =1 and b.barcode_no = d.barcode_no and a.location_id != c.knitting_location_id group by a.location_id, c.knitting_location_id, c.recv_number, c.id");
if(empty($receive_id_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($receive_id_sql as $val) 
{
	//execute_query("update inv_receive_master set knitting_location_id='".$val[csf("location_id")]."',updated_by=999 where id=".$val[csf("id")],0);
	echo "update inv_receive_master set knitting_location_id='".$val[csf("location_id")]."',updated_by=999 where id=".$val[csf("id")]."<br>";
}

/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>