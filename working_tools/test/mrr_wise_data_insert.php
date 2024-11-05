<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) 
{
	mysql_query("BEGIN");
}

$rcv_data_arr=array(); $issue_data_arr=array();
//--and b.prod_id < 226235
//and b.prod_id in(8,99713,83214)

/*
select min(b.prod_id) as min_prod_id, max(b.prod_id) as max_prod_id, count(id) as tot_row
 from inv_transaction b where b.item_category in(1) and b.status_active=1 and b.is_deleted=0 and b.company_id=1 and b.prod_id < 126235
 */

//update INV_MRR_WISE_ISSUE_DETAILS set status_active=7, is_deleted=8  where prod_id in(446639)

$sql_issue="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.transaction_type as TRANSACTION_TYPE, b.store_id as STORE_ID from inv_transaction b where b.item_category in(1) and b.transaction_type in(2,3,6) and b.status_active=1 and b.is_deleted=0 and b.store_id>0 and b.prod_id in(33837)
order by trans_id";
//echo $sql_issue;die;

/*$sql_issue="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.transaction_type as TRANSACTION_TYPE from inv_transaction b where b.item_category in(1) and b.transaction_type in(2,3,6) and b.status_active=1 and b.is_deleted=0 and b.company_id=3 and b.prod_id >=150000 and b.prod_id < 280000
order by trans_id";*/

/*$sql_issue="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.transaction_type as TRANSACTION_TYPE from inv_transaction b where b.item_category in(1) and b.transaction_type in(2,3,6) and b.status_active=1 and b.is_deleted=0 and b.company_id=3 and b.prod_id >= 280000 and b.prod_id < 430000
order by trans_id";*/
 
/*$sql_issue="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.transaction_type as TRANSACTION_TYPE from inv_transaction b where b.item_category in(1) and b.transaction_type in(2,3,6) and b.status_active=1 and b.is_deleted=0 and b.company_id=3 and b.prod_id >= 250000
order by trans_id";*/

//echo $sql_issue;die;
$result_issue=sql_select($sql_issue);
foreach($result_issue as $row)
{
	$issue_data_arr[$row["PROD_ID"]][$row["STORE_ID"]][$row["TRANS_ID"]]["cons_quantity"]=$row["CONS_QUANTITY"];
	$issue_data_arr[$row["PROD_ID"]][$row["STORE_ID"]][$row["TRANS_ID"]]["transaction_type"]=$row["TRANSACTION_TYPE"];
}
unset($result_issue);
// -- and b.prod_id < 226235
//and b.prod_id in(8,99713,83214)

$sql_receive="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.cons_rate as CONS_RATE, b.transaction_type as TRANSACTION_TYPE, b.store_id as STORE_ID from inv_transaction b where b.item_category in(1) and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 and b.store_id>0 and b.prod_id in(33837)
order by trans_id";


/*$sql_receive="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.cons_rate as CONS_RATE, b.transaction_type as TRANSACTION_TYPE from inv_transaction b where b.item_category in(1) and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 and b.company_id=3 and b.prod_id >=150000 and b.prod_id < 280000
order by trans_id";*/

/*$sql_receive="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.cons_rate as CONS_RATE, b.transaction_type as TRANSACTION_TYPE from inv_transaction b where b.item_category in(1) and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 and b.company_id=3 and b.prod_id >= 280000 and b.prod_id < 430000
order by trans_id";*/


/*$sql_receive="select b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as CONS_QUANTITY, b.cons_rate as CONS_RATE, b.transaction_type as TRANSACTION_TYPE from inv_transaction b where b.item_category in(1) and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 and b.company_id=3 and b.prod_id >= 250000
order by trans_id";*/

//echo $sql_receive;die;
$result_receive=sql_select($sql_receive);
foreach($result_receive as $row)
{
	$rcv_data_arr[$row["PROD_ID"]][$row["STORE_ID"]][$row["TRANS_ID"]]["cons_quantity"]=$row["CONS_QUANTITY"];
	$rcv_data_arr[$row["PROD_ID"]][$row["STORE_ID"]][$row["TRANS_ID"]]["cons_rate"]=$row["CONS_RATE"];
	$rcv_data_arr[$row["PROD_ID"]][$row["STORE_ID"]][$row["TRANS_ID"]]["transaction_type"]=$row["TRANSACTION_TYPE"];
}
unset($result_receive);
//echo "<pre>";print_r($issue_data_arr);echo "<pre>";print_r($rcv_data_arr);die;
//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
//$update_array = "balance_qnty*balance_amount*updated_by*update_date";
//$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
//echo "<pre>";print_r($rcv_data_arr);
$i=1;$k=1;$p=1;
$receive_balance_check=array();
foreach($issue_data_arr as $prod_id=>$prod_data)
{
	foreach($prod_data as $store_id=>$store_data)
	{
		foreach($store_data as $issue_trans_id=>$val)
		{
			$issue_qnty=$val["cons_quantity"];
			$issue_trans_type=$val["transaction_type"];
			$brk_loop=0;
			foreach($rcv_data_arr[$prod_id][$store_id] as $receive_trans_id=>$row)
			{
				$cons_rate = $row[("cons_rate")];
				if($issue_trans_type==2) $entry_form=3;
				else if($issue_trans_type==3) $entry_form=8;
				else $entry_form=10;
	
				if($receive_omite_check[$receive_trans_id]=="")
				{
					if($receive_balance_check[$receive_trans_id]=="")
					{
						$balance_qnty = $row[("cons_quantity")];
					}
					else
					{
						$balance_qnty = number_format($receive_balance_check[$receive_trans_id],6,'.','')*1;
					}
					
					$transQntyBalance = number_format(($balance_qnty*1)-(number_format($issue_qnty,6,'.','')*1),6,'.','');
					
					if($transQntyBalance>0)
					{
						$receive_balance_check[$receive_trans_id]=$transQntyBalance;
						$rcv_data_arr[$prod_id][$store_id][$receive_trans_id]["cons_quantity"]=$transQntyBalance;
					}
					else
					{
						$receive_omite_check[$receive_trans_id]=$receive_trans_id;
						unset($rcv_data_arr[$prod_id][$store_id][$receive_trans_id]);
					}
	
					if($transQntyBalance>=0)
					{
						//echo $receive_trans_id."**".$transQntyBalance."**".$balance_qnty."**".$issue_qnty."**".$row[("cons_quantity")];print_r($receive_balance_check);echo"<br>";
						if($transQntyBalance==0.0000)
						{
							$receive_balance_check[$receive_trans_id]="";
						}
						$transAmtBalance = $transQntyBalance*$cons_rate;
						$mrr_issue_qnty=$issue_qnty;
						$mrr_issue_amt=$mrr_issue_qnty*$cons_rate;
						$test_issue_qnty+=$mrr_issue_qnty;
						//if($data_array_mrr!="") $data_array_mrr .= ",";
						//$data_array[]  
						
						//echo $mrr_issue_qnty."**".$receive_balance_check[$receive_trans_id];echo"<br>";
						//echo $mrr_issue_qnty."**".$transQntyBalance;print_r($receive_balance_check);echo"<br>";
						$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
						//$data_array_mrr[]= "(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",".$mrr_issue_qnty.",".$cons_rate.",".$mrr_issue_amt.",'1','".$pc_date_time."')";
						$mrrId=execute_query("insert into inv_mrr_wise_issue_details (id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by) values(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",".$mrr_issue_qnty.",".$cons_rate.",".$mrr_issue_amt.",1)");
						if($mrrId==false)
						{
							echo "insert into inv_mrr_wise_issue_details (id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by) values(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",".$mrr_issue_qnty.",".$cons_rate.",".$mrr_issue_amt.",1)";
							oci_rollback($con); disconnect($con);die;
						}
						$mrrWiseIsID++;
						
						if($rcv_trans_check[$receive_trans_id]=="")
						{
							$rcv_trans_check[$receive_trans_id]=$receive_trans_id;
							$updateID_array[]=$receive_trans_id;
						}
						 
						//$update_data[$receive_trans_id]=explode("*",("".$transQntyBalance."*".$transAmtBalance."*'1'*'".$pc_date_time."'"));
						$transId=execute_query("update inv_transaction set balance_qnty=".$transQntyBalance.", balance_amount=".$transAmtBalance.", updated_by=1, update_date='".$pc_date_time."' where id=$receive_trans_id");
						if($transId==false)
						{
							echo "update inv_transaction set balance_qnty=".$transQntyBalance.", balance_amount=".$transAmtBalance.", updated_by=1, update_date='".$pc_date_time."' where id=$receive_trans_id";
							oci_rollback($con); disconnect($con);die;
						}
						$brk_loop=1;
						//break;
					}
					else
					{
						if($receive_balance_check[$receive_trans_id]!="")
						{
							$transferQntyBalance = $issue_qnty-$receive_balance_check[$receive_trans_id];
							$mrr_issue_qnty=$receive_balance_check[$receive_trans_id];
						}
						else
						{
							$transferQntyBalance = $issue_qnty-$balance_qnty;
							$mrr_issue_qnty=$balance_qnty;
						}
						$mrr_issue_amt=$mrr_issue_qnty*$cons_rate;
						$receive_balance_check[$receive_trans_id]="";
						$test_issue_qnty+=$mrr_issue_qnty;
						
						if($mrr_issue_qnty>0)
						{
							$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
							//$data_array_mrr[]= "(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",'".$mrr_issue_qnty."',".$cons_rate.",".$mrr_issue_amt.",'1','".$pc_date_time."')";
							$mrrId=execute_query("insert into inv_mrr_wise_issue_details (id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by) values(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",".$mrr_issue_qnty.",".$cons_rate.",".$mrr_issue_amt.",1)");
							if($mrrId==false)
							{
								echo "insert into inv_mrr_wise_issue_details (id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by) values(".$mrrWiseIsID.",".$receive_trans_id.",".$issue_trans_id.",".$entry_form.",".$prod_id.",".$mrr_issue_qnty.",".$cons_rate.",".$mrr_issue_amt.",1)";
								oci_rollback($con); disconnect($con);die;
							}
							$mrrWiseIsID++;
							//$updateID_array[]=$receive_trans_id; 
							if($rcv_trans_check[$receive_trans_id]=="")
							{
								$rcv_trans_check[$receive_trans_id]=$receive_trans_id;
								$updateID_array[]=$receive_trans_id;
							}
							//$update_data[$receive_trans_id]=explode("*",("0*0*'1'*'".$pc_date_time."'"));
							$transId=execute_query("update inv_transaction set balance_qnty=0, balance_amount=0, updated_by=1, update_date='".$pc_date_time."' where id=$receive_trans_id");
							if($transId==false)
							{
								echo "update inv_transaction set balance_qnty=0, balance_amount=0, updated_by=1, update_date='".$pc_date_time."' where id=$receive_trans_id";
								oci_rollback($con); disconnect($con);die;
							}
							$issue_qnty = $transferQntyBalance;
						}
						//$i++;
					}
					$p++;
				}
				
				if($brk_loop)
				{
					break;
				}
			}
		}
	}
}
//echo "<pre>";print_r($rcv_data_arr);oci_rollback($con);disconnect($con);die;
foreach($rcv_data_arr as $prod_id=>$prod_data)
{
	foreach($prod_data as $store_id=>$store_data)
	{
		foreach($store_data as $trans_id=>$row)
		{
			$transQntyBalance=$row["cons_quantity"];
			$transAmtBalance=$row["cons_quantity"]*$row["cons_rate"];
			$transId=execute_query("update inv_transaction set balance_qnty=".$transQntyBalance.", balance_amount=".$transAmtBalance.", updated_by=1, update_date='".$pc_date_time."' where id=$trans_id");
			if($transId==false)
			{
				echo "update inv_transaction set balance_qnty=".$transQntyBalance.", balance_amount=".$transAmtBalance.", updated_by=1, update_date='".$pc_date_time."' where id=$trans_id";
				oci_rollback($con); disconnect($con);die;
			}
		}
	}
}

//echo "<pre>";print_r($rcv_data_arr);oci_rollback($con);disconnect($con);die;
//echo $p;
//$rID = sql_insert2("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
//$rID2=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array),1);

//echo $rID;die;
echo $mrrId."<br>".$transId;die;

if($mrrId && $transId)
{
	oci_commit($con); 
	echo "Success";
}
else
{
	oci_rollback($con); 
	echo "Failed";
}
disconnect($con);
die;


?>