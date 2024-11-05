<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_sql=sql_select("select a.id dtls_id, c.from_order_id, c.to_order_id
from inv_item_transfer_dtls a, inv_item_transfer_mst c
where a.mst_id=c.id and (a.from_order_id is null or a.from_order_id =0) and a.status_active=1 and c.entry_form=83");

if(empty($mismatch_sql))
{
    echo "Mismatch Not Found";
    die;
}

foreach($mismatch_sql as $val)
{
	echo "update inv_item_transfer_dtls set from_order_id ='".$val[csf("from_order_id")]. "', to_order_id='".$val[csf("to_order_id")]. "' where id = ".$val[csf("dtls_id")]." <br>";

    //execute_query("update inv_item_transfer_dtls set from_order_id ='".$val[csf("from_order_id")]. "', to_order_id='".$val[csf("to_order_id")]. "' where id = ".$val[csf("dtls_id")],0);
}

/*oci_commit($con);
echo "Success"; 
die;*/


?>