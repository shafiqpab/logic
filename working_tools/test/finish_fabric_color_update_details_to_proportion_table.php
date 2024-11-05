<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$details_sql=sql_select("SELECT a.id, b.color_id from order_wise_pro_details a, inv_item_transfer_dtls b
where  a.dtls_id=b.id and a.entry_form=134 and a.trans_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.color_id=0 and b.color_id!=0");

if(empty($details_sql))
{
	echo "Data Not Found";
	die;
}

foreach($details_sql as $val)
{
	$color_id = $val[csf("color_id")];
	if ($color_id) 
	{
		// echo "update order_wise_pro_details set color_id=$color_id where id = ".$val[csf("id")]." <br />";
		execute_query("update order_wise_pro_details set color_id=$color_id where id=".$val[csf("id")],0);
	}
	
	
}

oci_commit($con);
//mysql_query("COMMIT");
echo "Success";
die;


?>