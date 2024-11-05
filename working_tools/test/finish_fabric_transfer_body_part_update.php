<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$transfer_sql=sql_select("select a.id as mst_id, a.company_id, a.transfer_system_id,a.transfer_date, a.transfer_criteria,b.id as dtls_id, d.id as trans_id, b.to_trans_id, b.body_part_id,b.to_body_part,b.from_prod_id,b.color_id,b.from_store,b.batch_id,c.po_breakdown_id, d.cons_uom
	from inv_item_transfer_mst a, inv_item_transfer_dtls b,order_wise_pro_details c,inv_transaction d
	where a.id = b.mst_id and b.id=c.dtls_id and c.trans_id=d.id
	and a.entry_form in (14,15) and c.entry_form in(14,15) and c.trans_type=6 and d.transaction_type=6 and b.to_body_part=0");

if(empty($transfer_sql))
{
	echo "Data Not Found";
	die;
}

$sql = "select id, product_name_details, color, unit_of_measure, current_stock, avg_rate_per_unit, detarmination_id, company_id, store_id, floor_id, fabric_shade, body_part_id, room, rack_no, shelf_no, sum(qnty) as qnty,prod_id, po_number, order_id,buyer_id, booking_no, batch_no, batch_id
from
(
select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, c.company_id,c.store_id, c.floor_id, b.fabric_shade, b.body_part_id, (case when c.room is null or c.room=0 then 0 else c.room end) room,(case when c.rack is null or c.rack=0 then 0 else c.rack end) rack_no,(case when c.self is null or c.self=0 then 0 else c.self end) shelf_no,sum(d.quantity) as qnty,c.prod_id, f.po_number, d.po_breakdown_id as order_id, g.buyer_name as buyer_id,e.booking_no,e.batch_no,  c.pi_wo_batch_no as batch_id
from product_details_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details d, inv_transaction c, pro_batch_create_mst e, wo_po_break_down f, wo_po_details_master g
where d.entry_form in (7,37) and a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=2 and c.item_category=2 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.trans_id = d.trans_id  and d.trans_id = c.id and d.po_breakdown_id = f.id and f.job_no_mst = g.job_no and c.pi_wo_batch_no = e.id and b.body_part_id > 0
group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, c.company_id, c.store_id, c.floor_id, b.fabric_shade, b.body_part_id, c.room, c.rack, c.self, c.prod_id, f.po_number, d.po_breakdown_id,g.buyer_name,e.booking_no, e.batch_no, c.pi_wo_batch_no
union all
select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, c.company_id,c.store_id, c.floor_id, b.fabric_shade, c.body_part_id, (case when c.room is null or c.room=0 then 0 else c.room end) room,(case when c.rack is null or c.rack=0 then 0 else c.rack end) rack_no,(case when c.self is null or c.self=0 then 0 else c.self end) shelf_no, sum(d.quantity) as qnty,  c.prod_id, f.po_number , d.po_breakdown_id as order_id, g.buyer_name as buyer_id, e.booking_no, e.batch_no, c.pi_wo_batch_no as batch_id
from product_details_master a, inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details d, pro_batch_create_mst e, wo_po_break_down f, wo_po_details_master g
where a.id = b.to_prod_id and b.to_trans_id = c.id and c.transaction_type = 5 and c.item_category = 2  and c.status_active =1 and c.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and a.status_active=1 and a.is_deleted=0 and d.entry_form in (14,15,306) and d.trans_type = 5 and c.id = d.trans_id and c.pi_wo_batch_no = e.id and d.po_breakdown_id = f.id and f.job_no_mst = g.job_no and d.status_active =1 and f.status_active =1 and c.body_part_id > 0
group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.detarmination_id, a.company_id,c.store_id, c.company_id, c.floor_id, b.fabric_shade, c.body_part_id, c.room,c.rack,c.self,c.prod_id, f.po_number, d.po_breakdown_id,g.buyer_name,e.booking_no, e.batch_no, c.pi_wo_batch_no
) x
group by id, product_name_details, color, unit_of_measure, current_stock, avg_rate_per_unit, detarmination_id, company_id,store_id, company_id, floor_id,fabric_shade, body_part_id, room, rack_no, shelf_no, prod_id, po_number, order_id, buyer_id, booking_no, batch_no, batch_id";
//echo $sql;die;
$result = sql_select($sql);
$bodypartArr=array();
foreach($result as $row )
{
	$bodypartArr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('color')]][$row[csf('store_id')]][$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('unit_of_measure')]]=$row[csf('body_part_id')];  
}
foreach($transfer_sql as $val)
{
	$mst_id  = $val[csf("mst_id")];
	$dtls_id  = $val[csf("dtls_id")];
	$trans_id  = $val[csf("trans_id")];
	$to_trans_id  = $val[csf("to_trans_id")];
	$body_part_id = $bodypartArr[$val[csf('company_id')]][$val[csf('from_prod_id')]][$val[csf('color_id')]][$val[csf('from_store')]][$val[csf('batch_id')]][$val[csf('po_breakdown_id')]][$val[csf('cons_uom')]];  

	if($body_part_id!=""){
		//echo "update inv_item_transfer_dtls set body_part_id=$body_part_id,to_body_part=$body_part_id where id=$dtls_id"." <br />";
		
		execute_query("update inv_item_transfer_dtls set body_part_id=$body_part_id,to_body_part=$body_part_id where id=$dtls_id",0);
		
		//echo "update inv_transaction set body_part_id=$body_part_id, updated_by=999 where transaction_type in (5,6) and item_category=2 and  id in ($to_trans_id,$trans_id)  <br />";
		execute_query("update inv_transaction set body_part_id=$body_part_id where transaction_type in (5,6) and item_category=2 and id in ($to_trans_id,$trans_id)",0);
	}
}


oci_commit($con);
//mysql_query("COMMIT");
echo "Success";
die;


?>