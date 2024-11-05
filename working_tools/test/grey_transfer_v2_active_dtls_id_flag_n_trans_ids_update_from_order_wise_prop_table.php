<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_sql=sql_select("select a.trans_id, a.trans_type, b.id from order_wise_pro_details a, inv_item_transfer_dtls b
where a.dtls_id = b.id and b.item_category=13 and a.entry_form = 13 and b.trans_id=0");

if(empty($mismatch_sql))
{
    echo "Mismatch Not Found";
    die;
}

foreach($mismatch_sql as $val)
{
	if($val[csf("trans_type")] == 6)
	{
    	echo "update inv_item_transfer_dtls set trans_id ='".$val[csf("trans_id")]. "', active_dtls_id_in_transfer=1 where id = ".$val[csf("id")]." <br>";
	}
	else
	{
		echo "update inv_item_transfer_dtls set to_trans_id ='".$val[csf("trans_id")]. "' where id = ".$val[csf("id")]." <br>";
	}

    //execute_query("update pro_grey_prod_entry_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id = ".$rcv_dtls_id,0);
}

/*oci_commit($con);
echo "Success"; 
die;*/


?>