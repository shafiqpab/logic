<?
include('../includes/common.php');
$con = connect();


//E:\wamp64\www\platform-v3.5\working_tools\update_script_store_item_ref_wise_knit_finish_fabric_revalueation_with_transfer_adjust.php

$product_id = 79281;

$sql_transfer_ref = sql_select("SELECT b.id as FROM_TRANS_ID, c.to_trans_id as TO_TRANS_ID, c.id as DTLS_ID, c.transfer_qnty as TRANSFER_QNTY, d.transfer_criteria as TRANSFER_CRITERIA
FROM inv_transaction b, inv_item_transfer_dtls c, inv_item_transfer_mst d 
where c.from_prod_id in ($product_id) and b.id=c.trans_id and c.mst_id=d.id and b.status_active=1 and b.is_deleted=0 and b.item_category=2 and b.transaction_type in (6,5)
order by  b.id, b.prod_id, b.store_id");
//and c.to_trans_id!=0

foreach ($sql_transfer_ref as $row) 
{
	if($row['TRANSFER_CRITERIA']==1)
	{
		echo "This Product has 'Company To Company' transfer criteria.\nScript can not run.";
		die;
	}
	if($row['TO_TRANS_ID'] !=0)
	{
		$dtls_id_ref[$row['TO_TRANS_ID']]=$row['DTLS_ID'];
		$tranfer_id_ref[$row['FROM_TRANS_ID']][]=$row['TO_TRANS_ID'];
	}
	else
	{
		$dtls_id_ref[$row['FROM_TRANS_ID']]=$row['DTLS_ID'];
	}
}

$sql_dyes_trans="select b.PROD_ID, b.STORE_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_RATE, b.CONS_AMOUNT, b.TRANSACTION_TYPE,
B.BATCH_ID, B.PI_WO_BATCH_NO, B.BODY_PART_ID, B.FLOOR_ID,B.RACK,B.ROOM,B.SELF,B.BIN_BOX 
from inv_transaction b where b.prod_id in($product_id) and b.status_active=1 and b.is_deleted=0 and b.item_category=2
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
		else if($row["TRANSACTION_TYPE"]==5)
		{

			$runtime_rate=$to_trans_id_rate[$row["TRANS_ID"]]; // N. B. rate comes from transfer out transaction
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),12,'.','');

			//N.B. transaction table update for transfer in
			$upTransID=execute_query("update inv_transaction set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
			if($upTransID)
			{ 
				$upTransID=1; 
			} 
			else 
			{
				echo "update inv_transaction set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$row["TRANS_ID"]." ";
				oci_rollback($con);die;
			}

			//N.B. transfer dtls table update for transfer in

			$DTLS_ID = $dtls_id_ref[$row["TRANS_ID"]];
			$upTransferDtlsID=execute_query("update inv_item_transfer_dtls set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$DTLS_ID." ");
			if($upTransferDtlsID)
			{ 
				$upTransferDtlsID=1; 
			} 
			else 
			{
				echo "update inv_item_transfer_dtls set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$DTLS_ID." ";
				oci_rollback($con);die;
			}

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

		if($row["TRANSACTION_TYPE"]==6)
		{
			foreach ($tranfer_id_ref[$row['TRANS_ID']] as $key => $to_trans_id) 
			{
				$to_trans_id_rate[$to_trans_id] = $runtime_rate;
			}

			//N.B. transfer dtls table update for transfer in
			$DTLS_ID = $dtls_id_ref[$row["TRANS_ID"]];
			$upTransferDtlsID=execute_query("update inv_item_transfer_dtls set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$DTLS_ID." ");
			if($upTransferDtlsID)
			{ 
				$upTransferDtlsID=1;
			} 
			else 
			{
				echo "update inv_item_transfer_dtls set STORE_RATE='".$runtime_rate."', STORE_AMOUNT='".$issue_amount."' where id=".$DTLS_ID." ";
				oci_rollback($con);die;
			}

		}
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