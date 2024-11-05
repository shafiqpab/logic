<?
include('../includes/common.php');
$con = connect();


$data_array=sql_select("select b.ID, a.CONVERSION_FACTOR from lib_item_group a, product_details_master b where a.id=b.item_group_id and a.item_category=4 and b.item_category_id=4 and b.entry_form=24 and b.status_active=1 and b.id in(399972,399973,403135,409443,409444,409445,409446,411881,411882,411883,411884,412292,412293,412294,412295,412296,412297,412298,412411,412412,412413,412414,412415,413756,413757,413758,413764,414572,414573,414575,414655,416709,416710,420224,421016,421843,424419,433265,447853,450567,455283,457778,465366,467142,471636,161572)");
$conversion_factor_arr =array();
foreach($data_array as $row)
{
	$conversion_factor_arr[$row["ID"]]=$row["CONVERSION_FACTOR"];
}
unset($data_array);

$rcv_issue_rtn_sql="select TRANS_ID, PO_BREAKDOWN_ID as ORDER_ID from ORDER_WISE_PRO_DETAILS where status_active=1 and ENTRY_FORM in (24,25,49,73,78,112) and prod_id in(399972,399973,403135,409443,409444,409445,409446,411881,411882,411883,411884,412292,412293,412294,412295,412296,412297,412298,412411,412412,412413,412414,412415,413756,413757,413758,413764,414572,414573,414575,414655,416709,416710,420224,421016,421843,424419,433265,447853,450567,455283,457778,465366,467142,471636,161572)";
$rcv_issue_rtn_result=sql_select($rcv_issue_rtn_sql);
$rcv_issue_rtn_data=array();
foreach($rcv_issue_rtn_result as $val)
{
	$rcv_issue_rtn_data[$val["TRANS_ID"]].=$val["ORDER_ID"].",";
}
unset($rcv_issue_rtn_result);


$sql_order_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE, a.ID as PROP_ID, a.PO_BREAKDOWN_ID, a.QUANTITY, a.ORDER_RATE, a.ORDER_AMOUNT, b.STORE_ID 
from ORDER_WISE_PRO_DETAILS a, inv_transaction b, PRODUCT_DETAILS_MASTER c 
where a.trans_id=b.id and a.prod_id=b.prod_id and b.prod_id=c.id and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.prod_id in(399972,399973,403135,409443,409444,409445,409446,411881,411882,411883,411884,412292,412293,412294,412295,412296,412297,412298,412411,412412,412413,412414,412415,413756,413757,413758,413764,414572,414573,414575,414655,416709,416710,420224,421016,421843,424419,433265,447853,450567,455283,457778,465366,467142,471636,161572)
order by b.PROD_ID, b.STORE_ID, b.ID";
$order_trans_result=sql_select($sql_order_trans);
//echo count($order_trans_result);die;
$i=1;$k=1;
$prod_ord_data=array();$prod_ord_data2=array();
$upTransID=$upTransID=$upOrdTransID=true;
foreach($order_trans_result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$rcv_data[$row["PROD_ID"]]["qnty"]=0;
		$rcv_data[$row["PROD_ID"]]["amt"]=0;
		$runtime_rate=0;$runtime_ord_rate=0;
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		if($trans_id_check[$row["TRANS_ID"]]=="")
		{
			$trans_id_check[$row["TRANS_ID"]]=$row["TRANS_ID"];
			if($row["TRANSACTION_TYPE"]==4)
			{
				$rcv_rtn_ord_id_arr=array_unique(explode(",",chop($rcv_issue_rtn_data[$row["TRANS_ID"]],",")));
				$runtime_qnty=$runtime_amt=0;
				foreach($rcv_rtn_ord_id_arr as $ord_id)
				{
					$runtime_qnty+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["qnty"];
					$runtime_amt+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["amt"];
				}
				
				if(number_format($runtime_qnty,8,'.','') > 0 && number_format($runtime_amt,8,'.','') > 0)
				{
					$runtime_rate=number_format(($runtime_amt/$runtime_qnty),8,'.','');
				}
				
				//if($row["TRANS_ID"]==102901) echo number_format($runtime_qnty,8,'.','')."=".number_format($runtime_amt,8,'.','')."=".$runtime_rate;oci_rollback($con);die;
				$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
				$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
				if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				
				$upIssID=execute_query("update INV_TRIMS_ENTRY_DTLS set CONS_RATE='".$runtime_rate."', BOOK_KEEPING_CURR='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]." ");
				if($upIssID){ $upIssID=1; } else {echo"update INV_TRIMS_ENTRY_DTLS set CONS_RATE='".$runtime_rate."', BOOK_KEEPING_CURR='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				
				$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"]+=$issue_amount;
				
				
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
			}
			else if($row["TRANSACTION_TYPE"]==5)
			{
				$rcv_rtn_ord_id_arr=array_unique(explode(",",chop($rcv_issue_rtn_data[$row["TRANS_ID"]],",")));
				$runtime_qnty=$runtime_amt=0;
				foreach($rcv_rtn_ord_id_arr as $ord_id)
				{
					$runtime_qnty+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["qnty"];
					$runtime_amt+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["amt"];
				}
				
				if(number_format($runtime_qnty,8,'.','') > 0 && number_format($runtime_amt,8,'.','') > 0)
				{
					$runtime_rate=number_format(($runtime_amt/$runtime_qnty),8,'.','');
				}
				
				$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
				$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
				if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				
				$upIssID=execute_query("update INV_ITEM_TRANSFER_DTLS set RATE='".$runtime_rate."', TRANSFER_VALUE='".$issue_amount."' where TO_TRANS_ID=".$row["TRANS_ID"]." ");
				if($upIssID){ $upIssID=1; } else {echo"update INV_ITEM_TRANSFER_DTLS set RATE='".$runtime_rate."', TRANSFER_VALUE='".$issue_amount."' where TO_TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}

				
				$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"]+=$issue_amount;
				
				
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
				
			}
			else
			{
				$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
				
				
				$cons_rate=$row["CONS_AMOUNT"]/$row["CONS_QUANTITY"];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
				$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$cons_rate);
			}
		}
		else
		{
			$cons_rate=$row["CONS_AMOUNT"]/$row["CONS_QUANTITY"];
			$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
			$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$cons_rate);
		}
		
		if($row["TRANSACTION_TYPE"]==1)
		{
			$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"];
			$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=$row["ORDER_AMOUNT"];
		}
		else
		{
			if(number_format($prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"],8,'.','') > 0 && number_format($prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"],8,'.','') > 0)
			{
				$runtime_ord_rate=number_format(($prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]/$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]),8,'.','');
			}
			
			$runtime_ord_amt=number_format($row["QUANTITY"]*$runtime_ord_rate,8,'.','');
			
			$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]+=$row["QUANTITY"];
			$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]+=$runtime_ord_amt;
		
			$upOrdTransID=execute_query("update order_wise_pro_details set ORDER_RATE='".$runtime_ord_rate."', ORDER_AMOUNT='".$runtime_ord_amt."' where id=".$row["PROP_ID"]." ");
			if($upOrdTransID){ $upOrdTransID=1; } else {echo "update order_wise_pro_details set ORDER_RATE='".$runtime_ord_rate."', ORDER_AMOUNT='".$runtime_ord_amt."' where id=".$row["PROP_ID"]." ";oci_rollback($con);die;}
		}
		
		$k=0;
	}
	else
	{
		if($trans_id_check[$row["TRANS_ID"]]=="")
		{
			$trans_id_check[$row["TRANS_ID"]]=$row["TRANS_ID"];
			
			$rcv_rtn_ord_id_arr=array_unique(explode(",",chop($rcv_issue_rtn_data[$row["TRANS_ID"]],",")));;
			$runtime_qnty=$runtime_amt=0;
			foreach($rcv_rtn_ord_id_arr as $ord_id)
			{
				$runtime_qnty+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["qnty"];
				$runtime_amt+=$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$ord_id]["amt"];
			}
			
			if(number_format($runtime_qnty,8,'.','') > 0 && number_format($runtime_amt,8,'.','') > 0)
			{
				$runtime_rate=number_format(($runtime_amt/$runtime_qnty),8,'.','');
			}
			
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
			
			
			
			if($row["TRANSACTION_TYPE"]==2 || $row["TRANSACTION_TYPE"]==3)
			{
				$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
				if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				
				$upIssID=execute_query("update INV_TRIMS_ISSUE_DTLS set RATE='".$runtime_rate."', AMOUNT='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]." ");
				if($upIssID){ $upIssID=1; } else {echo"update INV_TRIMS_ISSUE_DTLS set RATE='".$runtime_rate."', AMOUNT='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			}
			else
			{
				$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
				if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
			}
			
			$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
			
			
			$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]-=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
			$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]-=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
			
			$k++;
		}
		else
		{
			$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]-=$row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]];
			$prod_ord_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]-=(($row["QUANTITY"]*$conversion_factor_arr[$row["PROD_ID"]])*$runtime_rate);
		}
		
		if(number_format($prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"],8,'.','') > 0 && number_format($prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"],8,'.','') > 0)
		{
			$runtime_ord_rate=number_format(($prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]/$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]),8,'.','');
		}
		
		$runtime_ord_amt=number_format($row["QUANTITY"]*$runtime_ord_rate,8,'.','');
		
		$upOrdTransID=execute_query("update order_wise_pro_details set ORDER_RATE='".$runtime_ord_rate."', ORDER_AMOUNT='".$runtime_ord_amt."' where id=".$row["PROP_ID"]." ");
		if($upOrdTransID){ $upOrdTransID=1; } else {echo "update order_wise_pro_details set ORDER_RATE='".$runtime_ord_rate."', ORDER_AMOUNT='".$runtime_ord_amt."' where id=".$row["PROP_ID"]." ";oci_rollback($con);die;}
		
		$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["qnty"]-=$row["QUANTITY"];
		$prod_ord_data2[$row["PROD_ID"]][$row["STORE_ID"]][$row["PO_BREAKDOWN_ID"]]["amt"]-=$runtime_ord_amt;
	}
}

/* ##### difine Porduct ID Product Part update  */
$upProdID=true;
foreach($rcv_data as $prod_id=>$prod_val)
{
	$prod_agv_rate=0;
	if(number_format($prod_val["qnty"],8,'.','')>0 && number_format($prod_val["amt"],8,'.','')>0) 
	{
		$prod_agv_rate=number_format($prod_val["amt"],8,'.','')/number_format($prod_val["qnty"],8,'.','');
	}
	$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
	if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
}


if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID && $upProdID && $upIssID && $upOrdTransID)
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