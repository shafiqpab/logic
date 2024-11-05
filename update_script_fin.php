<?
include('includes/common.php');
$con = connect();

/*$id=return_next_id( "id", "product_details_master", 1 ) ;
$only_gi_arr=array(); $only_trims_arr=array(); $both_arr=array();

$sql="select b.prod_id from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=24 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by prod_id order by prod_id";
$result=sql_select($sql);
foreach($result as $row)
{
	$only_trims_arr[]=$row[csf('prod_id')];
}

$sql="select order_rate, order_amount, cons_rate, cons_amount from inv_transaction
where status_active=1 and transaction_type in(1) and mst_id in(300,302) 
and prod_id in (751,769,750,760,763,752,563,667,719,678,753,765,767,772,776,777,780,786,791,794,748,790,800,756,757,773,726,779,787,795,671,691,694,711,716,682,685,739,
319,561,770,727,781,797,669,754,322,799,674,693,718,747,553,687,695,680,743,758,762,796,331,672,689,690,697,715,720,723,679,684,755,761,764,793,320,563,
673,713,686,746,766,782,785,788,789,759,771,774,775,778,733,783,784,792,798,321
)";
$result=sql_select($sql);
foreach($result as $row)
{
	if(in_array($row[csf('prod_id')],$only_trims_arr))
	{
		$both_arr[$id]=$row[csf('prod_id')];
		$id++;
	}
	else
	{
		$only_gi_arr[]=$row[csf('prod_id')];
	}
}

//echo implode(",",$both_arr)."<br>";
//echo count($both_arr)."<br>";
//var_dump($both_arr);
//var_dump($only_gi_arr);
if(count($both_arr)>0)
{
	$id_string=""; $previds=''; $newids='';
	foreach($both_arr as $newId=>$previd)
	{
		$id_string.=" WHEN $previd THEN $newId";
		$previds.=$previd.",";
		$newids.=$newId.",";
	}
	
	$id_string_prod="CASE id ".$id_string." END";
	$id_string_trans="CASE b.prod_id ".$id_string." END";
	$id_string_nonOrder="CASE item_id ".$id_string." END";
	$id_string_mrr="CASE prod_id ".$id_string." END";
	
	$sql_insert="insert into product_details_master(id, company_id,supplier_id,store_id, item_category_id,entry_form, detarmination_id,sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
					$id_string_prod, company_id,supplier_id,store_id, item_category_id,20,detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code,unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from product_details_master where id in (".substr($previds,0,-1).")";
	
	$trans_sql="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_receive_master a where a.id=b.mst_id and a.entry_form=20 
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type=1 and b.item_category=4)";

	$trans_sql2="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_issue_master a where a.id=b.mst_id and a.entry_form in (21,26)
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type in (2,3) and b.item_category=4)";

	$purc_sql="update inv_purchase_requisition_dtls set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";
	$nonOrder_sql="update wo_non_order_info_dtls set item_id=".$id_string_nonOrder." where item_id in(".substr($previds,0,-1).")";
	$mrr_sql="update inv_mrr_wise_issue_details set prod_id=".$id_string_mrr." where prod_id in(".substr($previds,0,-1).") and entry_form=21";
	//$serial_sql="update inv_serial_no_details set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";$serial=execute_query($serial_sql,0);

	$both_ids=$previds.substr($newids,0,-1);
	
	$sql_trans=sql_select("SELECT prod_id, (sum(case when transaction_type in(1,4) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3) then cons_quantity else 0 end)) as current_stock from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".$both_ids.") and item_category=4 group by prod_id");
	$update_field="current_stock";
	foreach($sql_trans as $row)
	{
		$update_id_arr[]=$row[csf('prod_id')];
		$update_data_arr[$row[csf('prod_id')]]=explode("*",($row[csf('current_stock')]));
	}
	$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	
	$rID=sql_multirow_update("product_details_master","entry_form","24","id",implode(",",$only_trims_arr),0);
	$rID2=sql_multirow_update("product_details_master","entry_form","20","id",implode(",",$only_gi_arr),0);
	$rID3=execute_query($sql_insert,0);
	
	$inv_trans=execute_query($trans_sql,0);
	$inv_trans2=execute_query($trans_sql2,0);
	$inv_purc=execute_query($purc_sql,0);
	$non_order=execute_query($nonOrder_sql,0);
	$mrr=execute_query($mrr_sql,0);
	$rID4=execute_query($upsubDtlsID);
	
	if($rID && $rID2 && $rID3 && $inv_trans && $inv_trans2 && $inv_purc && $non_order && $mrr && $rID4)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}*/

function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
	
	//echo "<pre>";print_r($id_count_arr);die;
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	
	$sql_up.= "UPDATE $table SET ";
	
	 for ($len=0; $len<count($field_array); $len++)
	 {
		 $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
		 for ($id=0; $id<count($id_count); $id++)
		 {
			 if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
			 else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
		 }
		 if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
	 }
	 if(count($id_count)>999)
	 {
		$sql_up.=" where";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}



$sql=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_quantity,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt  from inv_transaction
where status_active=1 and transaction_type in(1) and mst_id in(300,302) 
and prod_id in (751,769,750,760,763,752,563,667,719,678,753,765,767,772,776,777,780,786,791,794,748,790,800,756,757,773,726,779,787,795,671,691,694,711,716,682,685,739,
319,561,770,727,781,797,669,754,322,799,674,693,718,747,553,687,695,680,743,758,762,796,331,672,689,690,697,715,720,723,679,684,755,761,764,793,320,563,
673,713,686,746,766,782,785,788,789,759,771,774,775,778,733,783,784,792,798,321 ) group by prod_id order by prod_id");
$prod_data_arr=array();
foreach($sql as $row)
{
	$prod_data_arr[$row[csf('prod_id')]]["bal_quantity"]=$row[csf('bal_quantity')];
	$prod_data_arr[$row[csf('prod_id')]]["bal_quantity"]=$row[csf('bal_quantity')];
}
$all_trans_id=chop($all_trans_id,",");



$sql=sql_select("select a.id, a.cons_rate, a.cons_amount from inv_transaction a, inv_mrr_wise_issue_details b
where a.id=b.issue_trans_id and a.status_active=1 and a.transaction_type in(2) 
and b.recv_trans_id in ($all_trans_id)
group by  a.id, a.cons_rate, a.cons_amount");

$update_field="cons_rate*cons_amount";
foreach($sql as $row)
{
	$rate=$row[csf('cons_rate')]/77;
	$amount=$row[csf('cons_amount')]/77;
	$rate=number_format($rate,6,".","");
	$amount=number_format($amount,4,".","");
	
	
	$update_id_arr[]=$row[csf('id')];
	$update_data_arr[$row[csf('id')]]=explode("*",($rate."*".$amount));
	
}



$sql=sql_select("select b.id, b.rate as cons_rate, b.amount as cons_amount from inv_transaction a, inv_mrr_wise_issue_details b
where a.id=b.issue_trans_id and a.status_active=1 and a.transaction_type in(2) 
and b.recv_trans_id in ($all_trans_id)");

$update_field_mrr="rate*amount";
foreach($sql as $row)
{
	$rate=$row[csf('cons_rate')]/77;
	$amount=$row[csf('cons_amount')]/77;
	$rate=number_format($rate,6,".","");
	$amount=number_format($amount,4,".","");
	
	
	$update_id_arr_mrr[]=$row[csf('id')];
	$update_data_arr_mrr[$row[csf('id')]]=explode("*",($rate."*".$amount));
}
//$rID=(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data_arr,$update_id_arr));
$rID2=(bulk_update_sql_statement2(" inv_mrr_wise_issue_details","id",$update_field_mrr,$update_data_arr_mrr,$update_id_arr_mrr));
echo $rID2;die;
if($rID && $rID2) echo "Success"; else "Invalid";

// entry_form, item_category,
?>