<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

//$cum_issu_return_sql = " select a.po_breakdown_id, a.prod_id,b.issue_id,sum(b.cons_quantity) return_qnty from order_wise_pro_details a,inv_transaction b where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and a.issue_purpose!=2 and a.entry_form ='9' and a.trans_type=4 group by a.po_breakdown_id, a.prod_id,b.issue_id";
$requisition_plan_qnty_sql = sql_select("select y.po_id,x.requisition_no,x.knit_id,x.prod_id,y.program_qnty program_qnty,y.is_sales from(select distinct(a.requisition_no),a.knit_id,a.prod_id from ppl_yarn_requisition_entry a where a.status_active=1 and a.is_deleted=0) x, ppl_planning_entry_plan_dtls y where x.knit_id = y.dtls_id and y.status_active=1 and y.is_deleted=0");//and a.requisition_no=37189 and a.prod_id=174581
$program_qnty_arr = array();
foreach($requisition_plan_qnty_sql as $req_row){
	$program_qnty_arr[$req_row[csf('requisition_no')]][$req_row[csf('prod_id')]] += number_format($req_row[csf('program_qnty')], 2, '.', '');
}

$cum_issu_return_sql = "select b.issue_id issue_id, a.prod_id,sum(b.cons_quantity) return_qnty 
from order_wise_pro_details a,inv_transaction b 
where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1
and a.entry_form ='9' and a.trans_type=4 and b.item_category=1
group by b.issue_id, a.prod_id";// and b.prod_id=21154
$cum_issu_return_res = sql_select($cum_issu_return_sql);
$cum_issue_return_array=array();
foreach ($cum_issu_return_res as $row) {
	$cum_issue_return_array[$row[csf('issue_id')]][$row[csf('prod_id')]] = $row[csf('return_qnty')];
}

$cum_issu_sql = "select a.mst_id issue_id,a.id trans_id,a.requisition_no, b.prod_id, 
sum(CASE WHEN b.entry_form ='3' and b.trans_type=2 THEN quantity ELSE 0 END) AS issue_qnty,
sum(CASE WHEN b.entry_form ='11' and b.trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
sum(CASE WHEN b.entry_form ='11' and b.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn
from inv_transaction a,order_wise_pro_details b
where a.id=b.trans_id and trans_type in(2,5,6) and b.status_active=1 and b.is_deleted=0 and a.item_category=1 
group by a.mst_id,a.id,a.requisition_no, b.prod_id";//and a.mst_id=434042 and a.prod_id=21154
$cum_issu_sql_res = sql_select($cum_issu_sql);

$issue_return_qnty=0;
foreach ($cum_issu_sql_res as $row) {
	$issue_return_qnty = $cum_issue_return_array[$row[csf('issue_id')]][$row[csf('prod_id')]];
	$cum_issue_array[$row[csf('issue_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] += $row[csf('issue_qnty')] + $row[csf('transfer_in_qnty_yarn')] - $issue_return_qnty - $row[csf('transfer_out_qnty_yarn')];
	$req_issue_ids[$row[csf('requisition_no')]][$row[csf('prod_id')]][] = $row[csf('issue_id')];
}


$has_returnable_row_transaction_sql = "select a.id issue_id,b.id trans_id,b.requisition_no,b.prod_id,sum(b.cons_quantity) issue from inv_issue_master a,inv_transaction b 
where a.id=b.mst_id and b.transaction_type=2 and b.item_category=1 group by a.id,b.id,b.requisition_no,b.prod_id order by b.id";//and a.id=434042 and b.prod_id=174581
$has_returnable_row_transaction_res = sql_select($has_returnable_row_transaction_sql);
$already_returnable = array();
foreach ($has_returnable_row_transaction_res as $row) {
	$returnable_qnty =  $cum_issue_array[$row[csf('issue_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] - $program_qnty_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]];	
	$trans_id = $row[csf('trans_id')];
	$returnable_qnty = ($returnable_qnty > 0)?$returnable_qnty:0;
	echo "UPDATE inv_transaction set return_qnty=$returnable_qnty where id=$trans_id <br />";
	//$update_inv_transaction=execute_query("UPDATE inv_transaction set return_qnty=$returnable_qnty where id=$trans_id");
	//$update_proportion=execute_query("UPDATE order_wise_pro_details set returnable_qnty=$returnable_qnty where trans_id=$trans_id and trans_type=2 and entry_form=3");
	/*if($update_proportion){
		echo "success. ".$trans_id."=".$returnable_qnty."<br />";
	}else{
		echo "Failled ".$trans_id;
		die;
	}*/
}



//oci_commit($con); 
echo "Success";
die;
?>