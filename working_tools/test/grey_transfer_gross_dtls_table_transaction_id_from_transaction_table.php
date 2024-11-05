<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_sql=sql_select("select c.entry_form, a.id dtls_id, b.id tran_table_id, b.transaction_type, a.transfer_qnty, b.cons_quantity
from inv_item_transfer_dtls a, inv_transaction b, inv_item_transfer_mst c
where a.mst_id=c.id and a.mst_id = b.mst_id and b.transaction_type in (5,6) and b.item_category=13 and (a.trans_id is null or a.trans_id =0) and b.status_active=1 and a.status_active=1
and a.transfer_qnty=b.cons_quantity and b.prod_id= a.from_prod_id 
order by a.id, b.id");

if(empty($mismatch_sql))
{
    echo "Mismatch Not Found";
    die;
}

foreach($mismatch_sql as $val)
{
	if($val[csf("transaction_type")] == 6)
	{
    	echo "update inv_item_transfer_dtls set trans_id ='".$val[csf("tran_table_id")]. "', insert_reason='trans_id and to_trans_id scripted' where id = ".$val[csf("dtls_id")]." <br>";

    	//execute_query("update inv_item_transfer_dtls set trans_id ='".$val[csf("tran_table_id")]. "' where id = ".$val[csf("dtls_id")],0);
	}
	else
	{
		echo "update inv_item_transfer_dtls set to_trans_id ='".$val[csf("tran_table_id")]. "', insert_reason='trans_id and to_trans_id scripted' where id = ".$val[csf("dtls_id")]." <br>";

		//execute_query("update inv_item_transfer_dtls set to_trans_id ='".$val[csf("tran_table_id")]. "' where id = ".$val[csf("dtls_id")],0);
	}

    //execute_query("update pro_grey_prod_entry_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id = ".$rcv_dtls_id,0);
}

/*oci_commit($con);
echo "Success"; 
die;*/


?>