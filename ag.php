<?php
require_once('includes/common.php');
$years="2019";
if($years != ""){
	$year_cond = " and to_char(d.insert_date,'YYYY') = '$years'";
}

echo "select d.company_id,sum(b.qnty) as receive_qty, d.id as fso_id from inv_receive_master a,pro_roll_details b,pro_grey_prod_entry_dtls c, fabric_sales_order_mst d where a.id=b.mst_id and b.dtls_id=c.id  and b.entry_form in(58,2) and c.trans_id>0 and b.po_breakdown_id  = d.id  $year_cond and a.receive_basis in(2,4,10) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date='17-Jan-19' group by d.company_id, d.id";


$rcv_sql =  sql_select("select d.company_id,sum(b.qnty) as receive_qty, d.id as fso_id from inv_receive_master a,pro_roll_details b,pro_grey_prod_entry_dtls c, fabric_sales_order_mst d where a.id=b.mst_id and b.dtls_id=c.id  and b.entry_form in(58,2) and c.trans_id>0 and b.po_breakdown_id  = d.id  $year_cond and a.receive_basis in(2,4,10) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_date='17-Jan-19' group by d.company_id, d.id");

foreach ($rcv_sql as  $val) 
{
	$fso_arr[$val[csf("fso_id")]] =  $val[csf("fso_id")];
	$grey_stock_arr[$val[csf("company_id")]]["rcv"] +=  $val[csf("receive_qty")];
	$grey_stock_arr[$val[csf("company_id")]]["stock"] +=  $val[csf("receive_qty")];
}

$fso_arr = array_filter(array_unique($fso_arr));
if(count($fso_arr)>0)
{
	$fso_nos = implode(",", $fso_arr);
	$fsoCond = $all_fso_no_cond = $toCond = $all_tr_To_fso_no_cond =$fromCond= $all_tr_From_fso_no_cond =$issRetCond = $all_iss_ret_fso_no_cond= "";

	if($db_type==2 && count($fso_arr)>999)
	{
		$fso_arr_chunk=array_chunk($fso_arr,999) ;
		foreach($fso_arr_chunk as $chunk_arr)
		{
			$fsoCond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			$toCond.=" a.to_order_id in(".implode(",",$chunk_arr).") or ";
			$fromCond.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			$issRetCond.=" d.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
		}

		$all_fso_no_cond.=" and (".chop($fsoCond,'or ').")";
		$all_tr_To_fso_no_cond.=" and (".chop($toCond,'or ').")";
		$all_tr_From_fso_no_cond.=" and (".chop($fromCond,'or ').")";
		$all_iss_ret_fso_no_cond.=" and (".chop($issRetCond,'or ').")";
	}
	else
	{
		$all_fso_no_cond=" and c.po_breakdown_id in($fso_nos)";
		$all_tr_To_fso_no_cond=" and a.to_order_id in($fso_nos)";
		$all_tr_From_fso_no_cond=" and a.from_order_id in($fso_nos)";
		$all_iss_ret_fso_no_cond=" and d.po_breakdown_id in($fso_nos)";
	}
}
	$iss_sql =  sql_select("select a.company_id,d.qnty as issue_qty from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c, pro_roll_details d where a.id=b.mst_id and b.id=c.dtls_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and c.trans_type=2 and c.entry_form=61 and d.entry_form=61 and a.issue_purpose=11 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $all_fso_no_cond and d.is_returned<>1 ");
	
	foreach ($iss_sql as  $val) 
	{
		$grey_stock_arr[$val[csf("company_id")]]["issue"] += $val[csf("issue_qty")];
		$grey_stock_arr[$val[csf("company_id")]]["stock"] -= $val[csf("issue_qty")];
	}

	$sql_issue_return = sql_select("select a.company_id,sum(d.qnty) as issue_return_qty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d where a.id=b.mst_id and c.dtls_id=b.id and b.id=d.dtls_id and a.item_category=13 and a.entry_form =84 and c.entry_form=84 and d.entry_form =84 and a.receive_basis in(0) and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $all_iss_ret_fso_no_cond group by a.company_id ");

	foreach($sql_issue_return as $row)
	{
		$grey_stock_arr[$row[csf("company_id")]]["iss_return"] +=  $row[csf("issue_return_qty")];
		$grey_stock_arr[$row[csf("company_id")]]["stock"] +=  $row[csf("issue_return_qty")];
	}

	$trans_out_data = sql_select("select a.company_id,sum(d.qnty) as transfer_out_qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b, pro_roll_details d where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id = d.mst_id and d.entry_form = 133 and b.id = d.dtls_id and d.status_active=1 and d.is_deleted=0 $all_tr_From_fso_no_cond group by a.company_id");

	foreach($trans_out_data as $row)
	{
		$grey_stock_arr[$row[csf("company_id")]]["tr_out"] +=  $row[csf("transfer_out_qnty")];
		$grey_stock_arr[$row[csf("company_id")]]["stock"] -=  $row[csf("transfer_out_qnty")];
	}

	$trans_in_data = sql_select("select a.company_id,sum(d.qnty) as transfer_in_qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b, pro_roll_details d where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.id = d.mst_id and d.entry_form = 133 and b.id = d.dtls_id and d.status_active=1 and d.is_deleted=0 $all_tr_To_fso_no_cond group by a.company_id");
	foreach($trans_in_data as $row)
	{
		$grey_stock_arr[$row[csf("company_id")]]["tr_in"] +=  $row[csf("transfer_in_qnty")];
		$grey_stock_arr[$row[csf("company_id")]]["stock"] +=  $row[csf("transfer_in_qnty")];
	}

	echo "<pre>";
	 print_r($grey_stock_arr);
	$gray_stock=0;
	foreach($grey_stock_arr as $row){
		$gray_stock+=$row[stock];
	}
	echo $gray_stock;
	
?> 