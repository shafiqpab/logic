<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

 $sql_receive=sql_select("select b.id, a.from_order_id,a.to_order_id from inv_item_transfer_mst a, inv_item_transfer_dtls b
where a.id = b.mst_id and (b.from_order_id =0 or a.to_order_id =0)
and a.entry_form =14 and a.status_active = 1  ");


if(empty($sql_receive))
{
	echo "Data Not Found";
	die;
}

foreach ($sql_receive as   $row ) 
{
	//echo "update inv_finish_fabric_issue_dtls set from_order_id = '".$row[csf('from_order_id')]."', to_order_id = '".$row[csf('to_order_id')]."', updated_by = 777 where id= ". $row[csf('id')]  ."<br>";

	execute_query("update inv_item_transfer_dtls set from_order_id = '".$row[csf('from_order_id')]."', to_order_id = '".$row[csf('to_order_id')]."', updated_by = 777 where id= ". $row[csf('id')],0);

}


mysql_query("COMMIT");
echo "Success"; 
die;



?>