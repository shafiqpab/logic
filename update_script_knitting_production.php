<?
include('includes/common.php');
$con = connect();

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




$job_sql=sql_select("select b.mst_id, c.job_no_mst from inv_receive_master a, pro_roll_details b,  wo_po_break_down c 
where a.id=b.mst_id and b.po_breakdown_id=c.id and b.entry_form=2 and a.entry_form=2 and a.roll_maintained>0 and a.booking_without_order<>1 and b.booking_without_order<>1 and a.receive_basis in(1,2) 
group by b.mst_id, c.job_no_mst");

$update_array = "job_no_ref";
foreach($job_sql as $row)
{
	$updateID_array[]=$row[csf("mst_id")];
	$update_data[$row[csf("mst_id")]]=explode("*",("'".$row[csf("job_no_mst")]."'"));
}


$rid=execute_query("update inv_receive_master a set a.booking_no_ref=(select c.booking_no from ppl_planning_info_entry_dtls b,  ppl_planning_info_entry_mst c where a.booking_id=b.id and b.mst_id=c.id)  
where a.entry_form=2 and a.receive_basis=2");

$rid2=execute_query("update inv_receive_master set booking_no_ref=booking_no  
where  entry_form=2 and  receive_basis=1");

$rID3=execute_query(bulk_update_sql_statement2("inv_receive_master","id",$update_array,$update_data,$updateID_array));

//echo $rid ."&& ".$rid2  ."&& ". $rID3;die;

/*$rid3=execute_query("update inv_receive_master a set a.job_no_ref=(select c.job_no_mst from pro_roll_details b,  wo_po_break_down c where a.id=b.mst_id and b.po_breakdown_id=c.id and b.entry_form=2 group by c.job_no_mst)  
where a.entry_form=2 and a.receive_basis in(1,2) and a.roll_maintained>0 and a.booking_without_order<>1");*/


//echo $rid3.nahid;die;

if($db_type==0)
{
	if($rid && $rid2 && $rid3)
	{
		mysql_query("COMMIT"); 
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "Failed";
	}
}
else
{
	if($rid && $rid2 && $rID3)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}







/*$sql="select b.prod_id from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by prod_id order by prod_id";
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
}*/

//echo implode(",",$both_arr)."<br>";
//echo count($both_arr)."<br>";
//var_dump($both_arr);
//var_dump($only_gi_arr);
/*if(count($both_arr)>0)
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


?>