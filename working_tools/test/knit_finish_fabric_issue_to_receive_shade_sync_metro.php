<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

 $sql_receive=sql_select("select a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,b.floor,b.fabric_shade, b.body_part_id, b.batch_id,
(case when b.room is null or b.room=0 then 0 else b.room end) room,(case when b.rack_no is null or b.rack_no=0 then 0 else b.rack_no end) rack_no,
(case when b.shelf_no is null or b.shelf_no=0 then 0 else b.shelf_no end) shelf_no,sum(b.receive_qnty) as qnty,d.gmt_item_id,b.prod_id,(case when d.cons_rate is null or d.cons_rate=0 then 0 else d.cons_rate end) cons_rate 
from product_details_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, inv_receive_master c 
where a.id=b.prod_id and b.trans_id=d.id and d.mst_id=c.id and b.batch_id in(31989) and c.company_id=2 and d.store_id =53 and c.entry_form in (7,37) and a.item_category_id=2 and b.trans_id!=0 
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 
group by a.id, a.product_name_details, a.color, a.unit_of_measure, a.current_stock,a.company_id,c.store_id,b.floor,b.fabric_shade,b.body_part_id, b.batch_id,b.room,b.rack_no,b.shelf_no,d.cons_rate,
d.gmt_item_id,b.prod_id");


if(empty($sql_receive))
{
	echo "Data Not Found";
	die;
}

foreach($sql_receive as $row)
{
	$recv_shade_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('floor')]][$row[csf('room')]][$row[csf('rack_no')]][$row[csf('shelf_no')]]=$row[csf('fabric_shade')];
}

/*echo "<pre>";
print_r($recv_shade_array);
die;
*/

 $issue_shade_sql=sql_select("select a.prod_id, a.pi_wo_batch_no as batch_id,c.fabric_shade, a.floor_id,(case when a.rack is null or a.rack=0 then 0 else a.rack end) rack,(case when a.room is null or a.room=0 then 0 else a.room end) room,(case when a.self is null or a.self=0 then 0 else a.self end) self, sum(case when a.transaction_type=2 then cons_quantity end) as issue_qnty from inv_issue_master b,inv_transaction a,inv_finish_fabric_issue_dtls c where b.entry_form=18 and b.id=a.mst_id and a.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 and a.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and a.pi_wo_batch_no in(31989) and b.status_active=1 and b.is_deleted=0 and b.company_id =2 and a.store_id in(53) group by a.prod_id, a.pi_wo_batch_no,c.fabric_shade,a.floor_id,a.room,a.rack, a.self");

foreach ($issue_shade_sql as  $row) 
{

	$issue_shade=$recv_shade_array[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]];

	echo "update inv_finish_fabric_issue_dtls set fabric_shade = '".$issue_shade."',  updated_by = 999  <br>";
	//die;
	//execute_query("update inv_finish_fabric_issue_dtls set fabric_shade = '".$issue_shade."',  updated_by = 999,0);
}

/*echo "<pre>";
print_r($recv_shade_array);
die;*/

//oci_commit($con);
echo "Success"; 
die;



?>