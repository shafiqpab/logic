<?
include('includes/common.php');
$con = connect();

/*
$sql = sql_select("delete from  inv_mrr_wise_issue_details  where entry_form in(3,8,10)");*/ 

// prod_id = 87982 by check

function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	
	if( $contain_lob==0)
	{
		$count=count($arrValues);
		 //return $count."ss"; 
		if( $count >1 ) // Multirow
		{
			$k=1;	
			foreach( $arrValues as $rows)
			{
				
				if($k==1)
				{
					$strQuery= "INSERT ALL \n";
				}
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
				if( $count==$k )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//return "=".$strQuery; 
					$stid =  oci_parse($con, $strQuery);
					//oci_execute("Character set is AL32UTF8");
					$exestd=oci_execute($stid, OCI_NO_AUTO_COMMIT);
					 if(!$exestd) return 0; //else return $exestd;
					$strQuery="";
					$k=0;
				}
				else if ( $k==50 )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//return $strQuery;
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
					if(!$exestd) return 0;
					$strQuery="";
					$k=0;
				}
				$k++;
			}
			return 1;
			 
			//return $strQuery; 
		}
		else // Single Row
		{
			$strQuery= "INSERT  \n";
			foreach( $arrValues as $rows)
			{
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
			}
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			//return $strQuery; 
			 return 1;
		}
	}
	else
	{
		$tmpv=explode(")",$arrValues);
		
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
 
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0"; 
		}
		return "1";

	}
	
    //return  $strQuery; die;
	//$strQuery .= "SELECT * FROM dual";
	//echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
	
}

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


$receive_sql="select id, receive_basis, booking_id, booking_no from inv_receive_master where entry_form=24 and status_active=1 and receive_basis in(1,2)";
$receive_result=sql_select($receive_sql);
$receive_data=array();
foreach($receive_result as $row)
{
	$receive_data[$row[csf("id")]]["receive_basis"]=$row[csf("receive_basis")];
	$receive_data[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
	$receive_data[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
}

$rcv_rtn_data_arr=array();

$sql_rcv_rtn="select b.id as trans_id, a.po_breakdown_id, a.quantity, c.received_id  
from  order_wise_pro_details a, inv_transaction b ,  inv_issue_master c
where a.trans_id=b.id and b.mst_id=c.id and a.entry_form=49 and c.entry_form=49 and a.trans_type=3 and b.transaction_type=3 and b.item_category=4";
$result_rcv_rtn=sql_select($sql_rcv_rtn);
foreach($result_rcv_rtn as $row)
{
	$rcv_rtn_data_arr[$row[csf('trans_id')]]["save_data"].=$row[csf('po_breakdown_id')]."_".$row[csf('quantity')].",";
	$rcv_rtn_data_arr[$row[csf('trans_id')]]["order_id"].=$row[csf('po_breakdown_id')].",";
	$rcv_rtn_data_arr[$row[csf('trans_id')]]["receive_basis"] =$receive_data[$row[csf("received_id")]]["receive_basis"];
	$rcv_rtn_data_arr[$row[csf('trans_id')]]["booking_id"] =$receive_data[$row[csf("received_id")]]["booking_id"];
	$rcv_rtn_data_arr[$row[csf('trans_id')]]["booking_no"] =$receive_data[$row[csf("received_id")]]["booking_no"];
}

$field_array_trans = "id,mst_id,company_id,store_id,prod_id,item_category,transaction_type,transaction_date,cons_uom,cons_quantity,cons_rate,cons_amount,remarks,inserted_by,insert_date";
$data_array_trans = "(".$id_trans.",".$id.",".$cbo_company_name.",".$cbo_store_name.",".$txt_prod_id.",4,3,".$txt_return_date.",".$cbo_uom.",".$txt_return_qnty.",".$txt_cons_rate.",".$txt_amount.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')"; 


$product_sql="select id, item_group_id, product_name_details, brand_supplier, item_color, item_size   
from  product_details_master 
where item_category_id=4 and status_active=1 and entry_form=24 ";
$product_result=sql_select($product_sql);
$prod_data_arr=array();
foreach($product_result as $row)
{
	$prod_data_arr[$row[csf('id')]]["item_group_id"]=$row[csf('item_group_id')];
	$prod_data_arr[$row[csf('id')]]["product_name_details"]=$row[csf('product_name_details')];
	$prod_data_arr[$row[csf('id')]]["brand_supplier"]=$row[csf('brand_supplier')];
	$prod_data_arr[$row[csf('id')]]["item_color"]=$row[csf('item_color')];
	$prod_data_arr[$row[csf('id')]]["item_size"]=$row[csf('item_size')];
}

$update_array="receive_basis*pi_wo_batch_no*updated_by*update_date";

$id_dtls=return_next_id( "id", "inv_trims_issue_dtls", 1 ) ;
$field_array_dtls="id, mst_id, trans_id, prod_id, item_group_id, item_description, brand_supplier, uom, issue_qnty, rate, amount, order_id, item_color_id, item_size, save_string, store_id, remarks, booking_id, booking_no, inserted_by, insert_date";

$sql_transaction="select b.id as trans_id, b.mst_id, b.prod_id, b.store_id, b.remarks, b.cons_uom, b.cons_quantity, b.cons_rate, b.cons_amount from inv_transaction b, inv_issue_master a where a.id=b.mst_id and a.entry_form=49 and b.item_category=4 and b.transaction_type=3 and b.status_active=1";
$sql_transaction_result=sql_select($sql_transaction);
 
if($db_type==0) $data_array_dtls=""; else $data_array_dtls=array();
foreach($sql_transaction_result as $row)
{
	if($db_type==0)
	{
		if($data_array_dtls!="") $data_array_dtls.=",";
		$data_array_dtls.="(".$id_dtls.",'".$row[csf("mst_id")]."','".$row[csf("trans_id")]."','".$row[csf("prod_id")]."','".$prod_data_arr[$row[csf("prod_id")]]["item_group_id"]."','".$prod_data_arr[$row[csf("prod_id")]]["product_name_details"]."','".$prod_data_arr[$row[csf("prod_id")]]["brand_supplier"]."','".$row[csf("cons_uom")]."','".$row[csf("cons_quantity")]."','".$row[csf("cons_rate")]."','".$row[csf("cons_amount")]."','".chop($rcv_rtn_data_arr[$row[csf("trans_id")]]["order_id"],",")."','".$prod_data_arr[$row[csf("prod_id")]]["item_color"]."','".$prod_data_arr[$row[csf("prod_id")]]["item_size"]."','".chop($rcv_rtn_data_arr[$row[csf("trans_id")]]["save_data"],",")."','".$row[csf("store_id")]."','".$row[csf("remarks")]."','".$rcv_rtn_data_arr[$row[csf("trans_id")]]["booking_id"]."','".$rcv_rtn_data_arr[$row[csf("trans_id")]]["booking_no"]."',1,'".$pc_date_time."')";
	
		$id_dtls++;
	}
	else
	{
		$data_array_dtls[]="(".$id_dtls.",'".$row[csf("mst_id")]."','".$row[csf("trans_id")]."','".$row[csf("prod_id")]."','".$prod_data_arr[$row[csf("prod_id")]]["item_group_id"]."','".$prod_data_arr[$row[csf("prod_id")]]["product_name_details"]."','".$prod_data_arr[$row[csf("prod_id")]]["brand_supplier"]."','".$row[csf("cons_uom")]."','".$row[csf("cons_quantity")]."','".$row[csf("cons_rate")]."','".$row[csf("cons_amount")]."','".chop($rcv_rtn_data_arr[$row[csf("trans_id")]]["order_id"],",")."','".$prod_data_arr[$row[csf("prod_id")]]["item_color"]."','".$prod_data_arr[$row[csf("prod_id")]]["item_size"]."','".chop($rcv_rtn_data_arr[$row[csf("trans_id")]]["save_data"],",")."','".$row[csf("store_id")]."','".$row[csf("remarks")]."','".$rcv_rtn_data_arr[$row[csf("trans_id")]]["booking_id"]."','".$rcv_rtn_data_arr[$row[csf("trans_id")]]["booking_no"]."',1,'".$pc_date_time."')";
	
		$id_dtls++;
	}
	
	
	$updateID_array[]=$row[csf("trans_id")];
	$update_data[$row[csf("trans_id")]]=explode("*",("'".$rcv_rtn_data_arr[$row[csf("trans_id")]]["receive_basis"]."'*'".$rcv_rtn_data_arr[$row[csf("trans_id")]]["booking_id"]."'*'1'*'".$pc_date_time."'"));
}


if($db_type==2)
{
	$rID = sql_insert2("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,1);
	$rID2=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
	if($rID && $rID2)
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
else
{
	$rID = sql_insert("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,1);
	$rID2=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
	if($rID && $rID2)
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