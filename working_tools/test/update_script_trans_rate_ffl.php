<?
include('../includes/common.php');
$con = connect();


$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.prod_id in(291044) and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";

//echo $sql_dyes_trans;die;
$result=sql_select($sql_dyes_trans);
echo count($result);die;
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
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		if($row["TRANSACTION_TYPE"]==1)
		{
			$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
		}
		else
		{
			//$runtime_rate=0;
			if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
			$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]]["qnty"] += $row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"] += $issue_amount;
		}
		
		$k=0;
	}
	else
	{
		if($k==0)
		{
			$runtime_rate=0;
			if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
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
	if($prod_val["qnty"]>0 && $prod_val["amt"]>0) 
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