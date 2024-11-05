<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$sql=sql_select("select a.id, a.company_id, a.location_id, a.challan_id, a.acknowledg_date, a.remarks,c.mst_id transfer_id,
b.mst_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.order_id, b.prod_id, b.pi_wo_batch_no, d.batch_no, b.dyeing_color_id, 
sum(b.cons_quantity) cons_quantity, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, b.cons_rate, b.cons_amount, 
b.cons_uom, b.fabric_shade,c.id dtls_id,c.trans_id from_trans_id,c.to_trans_id, c.active_dtls_id_in_transfer
from inv_item_trans_acknowledgement a, inv_transaction b,inv_item_transfer_dtls c, pro_batch_create_mst d
where a.id=896 and a.challan_id=b.mst_id and b.id=c.to_trans_id and b.pi_wo_batch_no=d.id and a.entry_form=247 and b.transaction_type=5 and a.status_active=1 
and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.active_dtls_id_in_transfer=1
group by a.id, a.company_id, a.location_id, a.challan_id, a.acknowledg_date, a.remarks,c.mst_id, b.mst_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.order_id, 
b.prod_id, b.pi_wo_batch_no, d.batch_no, b.dyeing_color_id, b.cons_rate, b.cons_amount, b.cons_uom, b.fabric_shade,c.id,c.trans_id,c.to_trans_id,c.active_dtls_id_in_transfer");


foreach($sql as $val)
{
	$to_trans_id = $val[csf("to_trans_id")];
	$dtls_id = $val[csf("dtls_id")];

	echo "update inv_item_transfer_dtls set status_active=0 and is_deleted=1 where id = $dtls_id <br />";
	echo "update inv_transaction set status_active=0 and is_deleted=1 where id = $to_trans_id <br />";
	echo "update order_wise_pro_details set status_active=0 and is_deleted=1 where trans_id = $to_trans_id <br />";

	//execute_query("update inv_item_transfer_dtls set status_active=0 and is_deleted=1 where id = $dtls_id",0);
	//execute_query("update inv_transaction set status_active=0 and is_deleted=1 where id = $to_trans_id",0);
	//execute_query("update order_wise_pro_details set status_active=0 and is_deleted=1 where trans_id = $to_trans_id",0);
}


/*oci_commit($con);
echo "Success"; 
die;*/



?>