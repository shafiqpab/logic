<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_sql=sql_select("select a.barcode_no, sum(a.qnty) as roll_qnty, b.grey_receive_qnty, b.trans_id, b.id as dtls_id from pro_roll_details a, pro_grey_prod_entry_dtls b where a.entry_form=58 and a.status_active=1 and a.is_deleted=0 and a.dtls_id=b.id and b.status_active=1 and b.is_deleted=0 group by a.barcode_no, b.grey_receive_qnty, b.trans_id, b.id having sum(a.qnty) != b.grey_receive_qnty ");

if(empty($mismatch_sql))
{
    echo "Mismatch Not Found";
    die;
}

foreach($mismatch_sql as $val)
{
    echo "update pro_grey_prod_entry_dtls set grey_receive_qnty = '".$val[csf("roll_qnty")]. "', updated_by = 999 where id = ".$val[csf("dtls_id")]." <br>";
    echo "update order_wise_pro_details set quantity = '".$val[csf("roll_qnty")]. "', updated_by = 999 where trans_id = ".$val[csf("trans_id")]." and entry_form=58 <br>";
    echo "update inv_transaction set cons_quantity = '".$val[csf("roll_qnty")]. "', updated_by = 999 where id = ".$val[csf("trans_id")]." <br>";

    //execute_query("update pro_grey_prod_entry_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id = ".$rcv_dtls_id,0);
}

/*oci_commit($con);
echo "Success"; 
die;*/


?>