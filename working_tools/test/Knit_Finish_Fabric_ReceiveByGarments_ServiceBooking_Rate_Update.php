<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("select a.currency_id, a.exchange_rate,b.id as dtls_id,b.rate as b_rate, b.amount as b_amount,b.dyeing_charge as b_dyeing_charge, b.grey_fabric_rate as b_grey_fabric_rate,b.grey_used_qnty as b_grey_used_qnty,c.id as trans_id, c.order_qnty as c_order_qnty,c.order_rate as c_order_rate, c.order_amount as c_order_amount,c.cons_rate as c_cons_rate,c.cons_amount as c_cons_amount
    from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c
    where a.id = b.mst_id and b.trans_id=c.id and b.mst_id=c.mst_id and a.entry_form = 37 and a.receive_basis=11 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.receive_basis=11 and c.status_active =1 and c.is_deleted=0 
    group by a.currency_id, a.exchange_rate,b.id,b.rate, b.amount,b.dyeing_charge, b.grey_fabric_rate,b.grey_used_qnty,c.id, c.order_qnty,c.order_rate, c.order_amount,c.cons_rate,c.cons_amount
    order by b.id,c.id desc");

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}


 $sql_charge="SELECT c.detarmination_id ,c.id,c.lot,c.brand, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,sum(b.cons_quantity) issue_qty,sum(b.cons_amount) cons_amount from order_wise_pro_details d, product_details_master c, inv_transaction b , inv_issue_master a where d.po_breakdown_id in(37457) and d.trans_type = 2 AND d.entry_form IN (16, 61) and d.prod_id=c.id and c.detarmination_id=5 AND b.mst_id = a.id AND b.id = d.trans_id AND b.prod_id = d.prod_id and a.entry_form in (16,61) and a.item_category=13 and b.id=d.trans_id and a.status_active=1 and b.item_category=13 and b.status_active=1 and c.detarmination_id=5 group by c.id,c.lot,c.brand,c.supplier_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type,c.detarmination_id";




die;
foreach ($mis_match_sql as  $row) 
{
	$row[csf("booking_no")];
	$row[csf("booking_id")];

	//echo "update pro_batch_create_mst set booking_no = '".$row[csf("booking_no")]. "', booking_no_id = '".$row[csf("booking_id")]. "'  where id = ".$row[csf("id")]." <br>";
	//execute_query("update inv_item_transfer_dtls set to_batch_id = '".$batch_id. "', updated_by = 999 where id = ".$dtls_id,0);
}

/*
oci_commit($con);
echo "Success"; 
die;*/


?>