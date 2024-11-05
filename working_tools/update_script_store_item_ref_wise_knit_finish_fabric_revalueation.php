<?
include('../includes/common.php');
$con = connect();


/*$sql_dyes_trans="select b.PROD_ID, b.STORE_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_RATE, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.item_category in(select category_id from lib_item_category_list where category_type=1) and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";*/
$sql_dyes_trans="select b.PROD_ID, b.STORE_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_RATE, b.CONS_AMOUNT, b.TRANSACTION_TYPE,
B.BATCH_ID, B.PI_WO_BATCH_NO, B.BODY_PART_ID, B.FLOOR_ID,B.RACK,B.ROOM,B.SELF,B.BIN_BOX 
from inv_transaction b where b.prod_id in(9621) and b.status_active=1 and b.is_deleted=0 and b.item_category=2
order by b.PROD_ID, b.STORE_ID, b.ID";

$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($result as $row)
{

	$batch_id = $row["PI_WO_BATCH_NO"];

	if($prod_store_check[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]=="")
	{
		$prod_store_check[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]=$row["PROD_ID"];
		//$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]=0;
		//$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]=0;
		//$runtime_rate=0;

		$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"]=0;
		$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"]=0;
		$runtime_rate=0;
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		if($row["TRANSACTION_TYPE"]==4)
		{
			if(number_format($rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"],12,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"],12,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"]/$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"]),12,'.','');
			}
			//echo $row["TRANSACTION_TYPE"]."=".$runtime_rate."=";print_r($rcv_data);echo "<br>";
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),12,'.','');
			$upTransID=execute_query("update inv_transaction set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo "update inv_transaction set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$row["TRANS_ID"]." ";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"]+=$issue_amount;
		}
		else
		{
			
			$upTransID=execute_query("update inv_transaction set STORE_RATE='".$row["CONS_RATE"]."', STORE_AMOUNT='".$row["CONS_AMOUNT"]."' where id=".$row["TRANS_ID"]." ");
			if($upTransID){ $upTransID=1; } else {echo "update inv_transaction set STORE_RATE='".$row["CONS_RATE"]."', STORE_AMOUNT='".$row["CONS_AMOUNT"]."' where id=".$row["TRANS_ID"]." ";oci_rollback($con);die;}
			$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"]+=$row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"]+=$row["CONS_AMOUNT"];
			//echo $row["TRANSACTION_TYPE"]."=".$runtime_rate."=";print_r($rcv_data);echo "<br>";
		}
		$k=0;
	}
	else
	{
		if(number_format($rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"],12,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"],12,'.','') > 0)
		{
			$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"]/$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"]),12,'.','');
		}
		//echo $row["TRANSACTION_TYPE"]."=".$runtime_rate."=";print_r($rcv_data);echo "<br>";
		$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),12,'.','');
		
		$upTransID=execute_query("update inv_transaction set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTransID){ $upTransID=1; } else {echo "update inv_transaction set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$row["TRANS_ID"]." ";oci_rollback($con);die;}
		$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"] -= $row["CONS_QUANTITY"];
		$rcv_data[$row["PROD_ID"]][$row["STORE_ID"]][$batch_id][$row["BODY_PART_ID"]][$row["FLOOR_ID"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["amt"] -= $issue_amount;
		$k++;
	}
}

/* ##### difine Porduct ID Product Part update  */


if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
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