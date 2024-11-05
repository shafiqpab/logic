<?
include('../includes/common.php');
$con = connect();

//,6,7,23 and b.prod_id <> 15883 ,6,7,23
/*

$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b, product_details_master a where a.id=b.prod_id and a.entry_form=24 and a.item_category_id=4 and b.item_category=4 and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";
$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.item_category in(8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";*/

$prod_id_arrs=explode(",","15942,15943,15944,15942,15943,15944,16707");
foreach($prod_id_arrs as $prod_id)
{
	$prod_id_arr[$prod_id]=$prod_id;
}

$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.prod_id in(176565) and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";

echo $sql_dyes_trans;die;
$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		//$rcv_data=array();
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$rcv_data[$row["PROD_ID"]]["qnty"]=0;
		$rcv_data[$row["PROD_ID"]]["amt"]=0;
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		$rcv_data[$row["PROD_ID"]]["qnty"]=bcadd($rcv_data[$row["PROD_ID"]]["qnty"],$row["CONS_QUANTITY"],15);
		$rcv_data[$row["PROD_ID"]]["amt"]=bcadd($rcv_data[$row["PROD_ID"]]["amt"],$row["CONS_AMOUNT"],15);
		$k=0;
	}
	else
	{
		if($k==0)
		{
			$runtime_rate=0;
			if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
			{
				$runtime_rate=bcdiv($rcv_data[$row["PROD_ID"]]["amt"],$rcv_data[$row["PROD_ID"]]["qnty"],15);
			}
		}
		$issue_amount=bcmul($row["CONS_QUANTITY"],$runtime_rate,15);
		
		$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
		$rcv_data[$row["PROD_ID"]]["qnty"] = bcsub($rcv_data[$row["PROD_ID"]]["qnty"],$row["CONS_QUANTITY"],15);
		$rcv_data[$row["PROD_ID"]]["amt"] = bcsub($rcv_data[$row["PROD_ID"]]["amt"],$issue_amount,15);
		$k++;
	}
}

/* ##### difine Porduct ID Product Part update  */
$upProdID=true;
foreach($rcv_data as $prod_id=>$prod_val)
{
	$prod_agv_rate=0;
	if($prod_val["qnty"]>0 && $prod_val["amt"]>0) 
	{
		$prod_agv_rate=bcdiv($prod_val["amt"],$prod_val["qnty"],15);
	}
	$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
	if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
	unset($prod_id_arr[$prod_id]);
}

if(count($prod_id_arr)>0)
{
	$upProdID=execute_query("update product_details_master set current_stock='0', stock_value='0' where id in(".implode(",",$prod_id_arr).")");
	if(!$upProdID) { echo "update product_details_master set current_stock='0', stock_value='0' where id in(".implode(",",$prod_id_arr).")";oci_rollback($con); die;}
}
/*$prod_agv_rate=0;
if($rcv_data[30971]["amt"]>0 && $rcv_data[30971]["qnty"]>0)
{
	$prod_agv_rate=number_format($rcv_data[30971]["amt"],6,'.','')/number_format($rcv_data[30971]["qnty"],6,'.','');
}
//echo  $prod_agv_rate."=".number_format($rcv_data[30971]["amt"],6,'.','')."=".number_format($rcv_data[30971]["qnty"],6,'.','')."=".$rcv_data[30971]["qnty"];oci_rollback($con); die;
$upProdID=execute_query("update product_details_master set current_stock='".number_format($rcv_data[30971]["qnty"],6,'.','')."', stock_value='".number_format($rcv_data[30971]["amt"],6,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,6,'.','')."' where id=30971");
if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($rcv_data[30971]["qnty"],6,'.','')."', stock_value='".number_format($rcv_data[30971]["amt"],6,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,6,'.','')."' where id=30971";oci_rollback($con); die;}*/
//echo "<pre>";print_r($rcv_data);die;

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