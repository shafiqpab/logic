<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$transfer_sql=sql_select("select a.company_id, a.transfer_system_id,a.transfer_date, a.transfer_criteria,b.id dtls_id,d.id trans_id, b.body_part_id,b.to_body_part,b.from_prod_id,b.color_id,b.from_store,b.batch_id,b.from_order_id as po_breakdown_id,b.uom
    from inv_item_transfer_mst a, inv_item_transfer_dtls b,inv_transaction d
    where a.id = b.mst_id and b.trans_id=d.id and a.entry_form in (306) and a.transfer_criteria in (7,8)  and d.transaction_type=6");

if(empty($transfer_sql))
{
	echo "Data Not Found";
	die;
}
if(!empty($transfer_sql))
{


	$sql = "select b.company_id, b.pi_wo_batch_no as batch_id, c.buyer_id, c.id as order_id, b.prod_id, b.store_id, a.batch_no, a.booking_no_id as booking_id, a.booking_no, b.fabric_shade, b.body_part_id, b.cons_uom, a.color_id 
	from pro_batch_create_mst a, inv_transaction b, wo_non_ord_samp_booking_mst c 
	where a.id=b.pi_wo_batch_no and a.booking_no_id = c.id and a.booking_no = c.booking_no and a.booking_without_order=1 and b.item_category=2 and b.transaction_type in (1,4,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 
	group by b.company_id, b.pi_wo_batch_no, c.buyer_id, c.id , b.prod_id, b.store_id, a.batch_no, a.booking_no_id , a.booking_no, b.fabric_shade, b.body_part_id, b.cons_uom, a.color_id ";
	//echo $sql;//die;
	$result = sql_select($sql);
	$order_data_array=array();
	foreach($result as $row )
	{
		$bodypartArr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('store_id')]][$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('cons_uom')]]=$row[csf('body_part_id')];
	}
	foreach($transfer_sql as $val)
	{

		$dtls_id  = $val[csf("dtls_id")];
		$trans_id  = $val[csf("trans_id")];
		$body_part_id = $bodypartArr[$val[csf('company_id')]][$val[csf('from_prod_id')]][$val[csf('color_id')]][$val[csf('from_store')]][$val[csf('batch_id')]][$val[csf('po_breakdown_id')]][$val[csf('uom')]];

		if($body_part_id!=""){
			echo "update inv_item_transfer_dtls set body_part_id=$body_part_id,to_body_part=$body_part_id, updated_by=999 where id = ".$dtls_id." <br />";
			//execute_query("update inv_item_transfer_dtls set body_part_id=$body_part_id,to_body_part=$body_part_id,updated_by=888 where id=$dtls_id",0);
			echo "update inv_transaction set body_part_id=$body_part_id, updated_by=999 where id = ".$trans_id." <br />";
			//execute_query("update inv_transaction set body_part_id=$body_part_id, updated_by=888 where id=$trans_id",0);
		}
	}
}

//oci_commit($con);
//mysql_query("COMMIT");
//echo "Success";
die;


?>