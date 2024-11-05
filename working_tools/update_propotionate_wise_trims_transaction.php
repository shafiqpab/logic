<?
include('../includes/common.php');
$con = connect();


/*$prop_sql="select prod_id, PO_BREAKDOWN_ID, listagg(cast(TRANS_ID as varchar(4000)),',') within group (order by TRANS_ID) as TRANS_ID,
sum(case when trans_type in(1,4,5) then quantity else 0 end) as rcv ,
sum(case when trans_type in(2,3,6) then quantity else 0 end) as issue,
sum((case when trans_type in(1,4,5) then quantity else 0 end)-(case when trans_type in(2,3,6) then quantity else 0 end)) as bal
from order_wise_pro_details
where status_active=1 and PO_BREAKDOWN_ID in(851,1580,1406,1396,851,851,1444,1736,1711,851,851,1423,1651)
and entry_form in(24)
AND PROD_ID in(7616,21652,32617,25224,2429,7617,25166,102083,102144,2423,7615,29919,103114)
AND QUANTITY = 0.1
group by prod_id, PO_BREAKDOWN_ID";
$prop_sql_result=sql_select($prop_sql);
$book_rid=$trans_rid=$propo_rid=true;
foreach($prop_sql_result as $row)
{
	$prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
	$trans_id=$row["TRANS_ID"];
	$book_rid=execute_query("update INV_TRIMS_ENTRY_DTLS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)");
	if($book_rid==false)
	{
		echo "update INV_TRIMS_ENTRY_DTLS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)";oci_rollback($con);disconnec($con);die;
	}
	
	$trans_rid=execute_query("update INV_TRANSACTION set status_active=7, is_deleted=8 where id in($trans_id)");
	if($trans_rid==false)
	{
		echo "update INV_TRANSACTION set status_active=7, is_deleted=8 where id in($trans_id)";oci_rollback($con);disconnec($con);die;
	}
	
	$propo_rid=execute_query("update ORDER_WISE_PRO_DETAILS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)");
	if($propo_rid==false)
	{
		echo "update ORDER_WISE_PRO_DETAILS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)";oci_rollback($con);disconnec($con);die;
	}
	
}


if($book_rid && $trans_rid && $propo_rid )
{
	oci_commit($con); 
	echo "Success <br>";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}

die;*/


$prop_sql="select PROD_ID, PO_BREAKDOWN_ID, listagg(cast(TRANS_ID as varchar(4000)),',') within group (order by TRANS_ID) as TRANS_ID,
sum(case when trans_type in(1,4,5) then quantity else 0 end) as rcv ,
sum(case when trans_type in(2,3,6) then quantity else 0 end) as issue,
sum((case when trans_type in(1,4,5) then quantity else 0 end)-(case when trans_type in(2,3,6) then quantity else 0 end)) as bal
from order_wise_pro_details 
where PO_BREAKDOWN_ID in(851,1580,1406,1396,851,851,1444,1736,1711,851,851,1423,1651)
and entry_form in(25) and trans_type=2 and status_active=1
AND PROD_ID in(7616,21652,32617,25224,2429,7617,25166,102083,102144,2423,7615,29919,103114)
group by prod_id, PO_BREAKDOWN_ID";
$prop_sql_result=sql_select($prop_sql);
$book_rid=$trans_rid=$propo_rid=true;
foreach($prop_sql_result as $row)
{
	$prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
	$trans_id=$row["TRANS_ID"];
	$book_rid=execute_query("update INV_TRIMS_ISSUE_DTLS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)");
	if($book_rid==false)
	{
		echo "update INV_TRIMS_ISSUE_DTLS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)";oci_rollback($con);disconnec($con);die;
	}
	
	$trans_rid=execute_query("update INV_TRANSACTION set status_active=7, is_deleted=8 where id in($trans_id)");
	if($trans_rid==false)
	{
		echo "update INV_TRANSACTION set status_active=7, is_deleted=8 where id in($trans_id)";oci_rollback($con);disconnec($con);die;
	}
	
	$propo_rid=execute_query("update ORDER_WISE_PRO_DETAILS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)");
	if($propo_rid==false)
	{
		echo "update ORDER_WISE_PRO_DETAILS set status_active=7, is_deleted=8 where TRANS_ID in($trans_id)";oci_rollback($con);disconnec($con);die;
	}
	
}


if($book_rid && $trans_rid && $propo_rid )
{
	oci_commit($con); 
	echo "Success <br>";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}

$sql_dyes_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE 
from inv_transaction b where b.prod_id in(".implode(",",$prod_id_arr).") and b.status_active=1 and b.is_deleted=0 
order by b.PROD_ID, b.ID";
$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($result as $row)
{
	if($prod_check[$row["PROD_ID"]]=="")
	{
		unset($prod_id_arr[$row["PROD_ID"]]);
		$prod_check[$row["PROD_ID"]]=$row["PROD_ID"];
		$rcv_data[$row["PROD_ID"]]["qnty"]=0;
		$rcv_data[$row["PROD_ID"]]["amt"]=0;
	}
	
	if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
	{
		$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
		$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
		$k=0;
	}
	else
	{
		if($k==0)
		{
			$runtime_rate=0;
			if(number_format($rcv_data[$row["PROD_ID"]]["qnty"],8,'.','') > 0 && number_format($rcv_data[$row["PROD_ID"]]["amt"],8,'.','') > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
			}
		}
		$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
		
		$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
		if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
		
		$upIssID=execute_query("update INV_TRIMS_ISSUE_DTLS set RATE='".$runtime_rate."', AMOUNT='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]." ");
		if($upIssID){ $upIssID=1; } else {echo"update INV_TRIMS_ISSUE_DTLS set RATE='".$runtime_rate."', AMOUNT='".$issue_amount."' where TRANS_ID=".$row["TRANS_ID"]."";oci_rollback($con);die;}
		
		
		$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
		$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
		$k++;
	}
}

/* ##### difine Porduct ID Product Part update  */
$upProdID=$upProdID2=true;
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

if(count($prod_id_arr)>0)
{
	foreach($prod_id_arr as $prod_idd)
	{
		$upProdID2=execute_query("update product_details_master set current_stock=0, stock_value=0, avg_rate_per_unit=0 where id=$prod_idd");
		if(!$upProdID2) { echo "update product_details_master set current_stock=0, stock_value=0, avg_rate_per_unit=0 where id=$prod_idd";oci_rollback($con); die;}
	}
}


if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID && $upProdID && $upIssID && $upProdID2)
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

die;

$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group where item_category=4");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}

$prop_sql="select a.TRANS_ID, a.PROD_ID, b.SAVE_STRING, b.ORDER_ID, b.ITEM_GROUP_ID, c.EXCHANGE_RATE, b.RATE as TRANS_RATE, b.CONS_RATE
, listagg(cast (a.PO_BREAKDOWN_ID as varchar(4000)),',') within group (order by a.id) as PROP_ORDER_ID, b.RECEIVE_QNTY, sum(a.QUANTITY) as ORD_QNTY, sum(a.ORDER_AMOUNT) as ORD_AMOUNT
from ORDER_WISE_PRO_DETAILS a, INV_TRIMS_ENTRY_DTLS b, INV_RECEIVE_MASTER c
where a.trans_id=b.trans_id and b.mst_id=c.id and a.dtls_id=b.id and a.status_active=1 and b.status_active=1 and a.trans_type=1 
and a.entry_form=24
group by a.trans_id, b.RECEIVE_QNTY, b.SAVE_STRING, b.ORDER_ID, a.PROD_ID, b.ITEM_GROUP_ID, c.EXCHANGE_RATE, b.RATE, b.CONS_RATE
having sum(a.QUANTITY)-b.RECEIVE_QNTY > 1 or sum(a.QUANTITY)-b.RECEIVE_QNTY < -1
order by a.PROD_ID, a.trans_id";


$prop_sql_result=sql_select($prop_sql);
$book_rid=$trans_rid=$propo_rid=true;
foreach($prop_sql_result as $row)
{
	$prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
	$trans_id=$row["TRANS_ID"];
	$ord_avg_rate=$ord_amount=0;
	if($row["ORD_AMOUNT"]>0) 
	{
		$ord_amount=$row["ORD_AMOUNT"];
		$ord_avg_rate=$row["ORD_AMOUNT"]/$row["ORD_QNTY"];
	}
	else
	{
		if($row["TRANS_RATE"]>0)
		{
			$ord_amount=$row["ORD_QNTY"]*$row["TRANS_RATE"];
			$ord_avg_rate=$row["TRANS_RATE"];
		}
	}
	
	$cons_qnty=$row["ORD_QNTY"]*$trim_group_arr[$row['ITEM_GROUP_ID']]['conversion_factor'];
	$cons_rate=$cons_amount=0;
	if($row["CONS_RATE"]==$row["TRANS_RATE"])
	{
		$cons_rate=$row["CONS_RATE"];
		$cons_amount=$row["CONS_RATE"]*$cons_qnty;
	}
	else
	{
		$cons_rate=(($ord_avg_rate/$trim_group_arr[$row['ITEM_GROUP_ID']]['conversion_factor'])*$row['EXCHANGE_RATE']);
		$cons_amount=$cons_rate*$cons_qnty;
	}
	$book_rid=execute_query("update INV_TRIMS_ENTRY_DTLS set RECEIVE_QNTY='".$row["ORD_QNTY"]."', RATE='$ord_avg_rate', AMOUNT='$ord_amount', CONS_QNTY='$cons_qnty', CONS_RATE='$cons_rate', BOOK_KEEPING_CURR='$cons_amount' where TRANS_ID=$trans_id");
	if($book_rid==false)
	{
		echo "update INV_TRIMS_ENTRY_DTLS set RECEIVE_QNTY='".$row["ORD_QNTY"]."', RATE='$ord_avg_rate', AMOUNT='$ord_amount', CONS_QNTY='$cons_qnty', CONS_RATE='$cons_rate', BOOK_KEEPING_CURR='$cons_amount' where TRANS_ID=$trans_id";oci_rollback($con);disconnec($con);die;
	}
	
	$trans_rid=execute_query("update INV_TRANSACTION set ORDER_QNTY='".$row["ORD_QNTY"]."', ORDER_RATE='$ord_avg_rate', ORDER_AMOUNT='$ord_amount', CONS_QUANTITY='$cons_qnty', CONS_RATE='$cons_rate', CONS_AMOUNT='$cons_amount' where ID=$trans_id");
	if($trans_rid==false)
	{
		echo "update INV_TRANSACTION set ORDER_QNTY='".$row["ORD_QNTY"]."', ORDER_RATE='$ord_avg_rate', ORDER_AMOUNT='$ord_amount', CONS_QUANTITY='$cons_qnty', CONS_RATE='$cons_rate', CONS_AMOUNT='$cons_amount' where ID=$trans_id";oci_rollback($con);disconnec($con);die;
	}
	
}

//echo $rID;die;
//echo $rID."<br>".$rID2;die;

if($book_rid && $trans_rid)
{
	oci_commit($con); 
	echo "Success <br>"; print_r($prod_id_arr);
}
else
{
	oci_rollback($con); 
	echo "Failed";
}

?>