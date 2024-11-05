<?
include('../includes/common.php');
$con = connect();

$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}

$sql="select COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE, count(id) as TOT_ROW,
min(id) as MIN_PROD_ID, listagg(cast(id as varchar(4000)),',') within group(order by id) as ALL_PROD_ID 
from product_details_master
where status_active=1 and is_deleted=0 and entry_form <> 24 
and item_category_id in(select category_id from lib_item_category_list where category_type=1)
group by company_id, item_category_id, item_group_id, sub_group_name, item_description, item_size, model, item_number, item_code
having count(id) >1
order by tot_row desc";

$sql_result=sql_select($sql);
echo count($sql_result);die;
$prod_rid=$req_rid=$wo_rid=$pi_rid=$tr_rid=$prod_rid1=$prod_rid2=$prod_rid3=$prod_rid4=true;
foreach($sql_result as $row)
{
	$all_prod_arr=explode(",",$row["ALL_PROD_ID"]);
	$min_prod_id_arr=explode(",",$row["MIN_PROD_ID"]);
	$all_prod_arr_without_min=array_diff($all_prod_arr,$min_prod_id_arr);
	//print_r($all_prod_arr_without_min);die;
	
	$prod_rid=execute_query("update product_details_master set status_active=4, is_deleted=5 where id in(".implode(",",$all_prod_arr_without_min).")");
	if($prod_rid==false)
	{
		echo "update product_details_master set status_active=4, is_deleted=5 where id in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$req_rid=execute_query("update inv_purchase_requisition_dtls set product_id='".$row["MIN_PROD_ID"]."' where product_id in(".implode(",",$all_prod_arr_without_min).")");
	if($req_rid==false)
	{
		echo "update inv_purchase_requisition_dtls set product_id='".$row["MIN_PROD_ID"]."' where product_id in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$wo_rid=execute_query("update wo_non_order_info_dtls set item_id='".$row["MIN_PROD_ID"]."' where item_id in(".implode(",",$all_prod_arr_without_min).")");
	if($wo_rid==false)
	{
		echo "update wo_non_order_info_dtls set item_id='".$row["MIN_PROD_ID"]."' where item_id in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$pi_rid=execute_query("update com_pi_item_details set item_prod_id='".$row["MIN_PROD_ID"]."' where item_prod_id in(".implode(",",$all_prod_arr_without_min).")");
	if($pi_rid==false)
	{
		echo "update com_pi_item_details set item_prod_id='".$row["MIN_PROD_ID"]."' where item_prod_id in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$tr_rid=execute_query("update inv_transaction set prod_id='".$row["MIN_PROD_ID"]."' where prod_id in(".implode(",",$all_prod_arr_without_min).")");
	if($tr_rid==false)
	{
		echo "update inv_transaction set prod_id='".$row["MIN_PROD_ID"]."' where prod_id in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	$all_active_prod_id_arr[$row["MIN_PROD_ID"]]=$row["MIN_PROD_ID"];
	
	$prod_rid1=execute_query("update INV_ITEM_TRANSFER_DTLS set FROM_PROD_ID='".$row["MIN_PROD_ID"]."' where FROM_PROD_ID in(".implode(",",$all_prod_arr_without_min).")");
	if($prod_rid1==false)
	{
		echo "update INV_ITEM_TRANSFER_DTLS set FROM_PROD_ID='".$row["MIN_PROD_ID"]."' where FROM_PROD_ID in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$prod_rid2=execute_query("update INV_ITEM_TRANSFER_DTLS_AC set FROM_PROD_ID='".$row["MIN_PROD_ID"]."' where FROM_PROD_ID in(".implode(",",$all_prod_arr_without_min).")");
	if($prod_rid2==false)
	{
		echo "update INV_ITEM_TRANSFER_DTLS_AC set FROM_PROD_ID='".$row["MIN_PROD_ID"]."' where FROM_PROD_ID in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$prod_rid3=execute_query("update INV_ITEM_TRANSFER_DTLS set TO_PROD_ID='".$row["MIN_PROD_ID"]."' where TO_PROD_ID in(".implode(",",$all_prod_arr_without_min).")");
	if($prod_rid3==false)
	{
		echo "update INV_ITEM_TRANSFER_DTLS set TO_PROD_ID='".$row["MIN_PROD_ID"]."' where TO_PROD_ID in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
	
	$prod_rid4=execute_query("update INV_ITEM_TRANSFER_DTLS_AC set TO_PROD_ID='".$row["MIN_PROD_ID"]."' where TO_PROD_ID  in(".implode(",",$all_prod_arr_without_min).")");
	if($prod_rid4==false)
	{
		echo "update INV_ITEM_TRANSFER_DTLS_AC set TO_PROD_ID='".$row["MIN_PROD_ID"]."' where TO_PROD_ID  in(".implode(",",$all_prod_arr_without_min).")";oci_rollback($con);disconnec($con);die;
	}
}

//echo $rID;die;
//echo $rID."<br>".$rID2;die;

if($prod_rid && $req_rid && $wo_rid && $pi_rid && $tr_rid && $prod_rid1 && $prod_rid2 && $prod_rid3 && $prod_rid4)
{
	oci_commit($con); 
	echo "Success <br>";
}
else
{
	oci_rollback($con); 
	echo "Failed <br>";
}


$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.prod_id in(".implode(",",$all_active_prod_id_arr).") and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";

//echo $sql_dyes_trans;die;
$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$rcv_data[$row["PROD_ID"]]["qnty"]=0;
		$rcv_data[$row["PROD_ID"]]["amt"]=0;
		$runtime_rate=0;
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		if($row["TRANSACTION_TYPE"]==4)
		{
			if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],8,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],8,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
			$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$issue_amount;
		}
		else
		{
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
		}
		
		$k=0;
	}
	else
	{
		if($k==0)
		{
			if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],8,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],8,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
			}
		}
		$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
		
		$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
		$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
		$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
		$k++;
	}
}

/* ##### difine Porduct ID Product Part update  */
$upProdID=true;
foreach($rcv_data as $prod_id=>$prod_val)
{
	$prod_agv_rate=0;
	if(number_format($prod_val["qnty"],8,'.','') > 0 && number_format($prod_val["amt"],8,'.','') > 0) 
	{
		$prod_agv_rate=number_format($prod_val["amt"],8,'.','')/number_format($prod_val["qnty"],8,'.','');
	}
	$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
	if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
}


if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID && $upProdID)
	{
		oci_commit($con); 
		echo "Transaction Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Transaction Data Update Failed";
		die;
	}
}

?>