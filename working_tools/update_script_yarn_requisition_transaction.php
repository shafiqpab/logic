<?
include('../includes/common.php');
$con = connect();

$sql_trans="select A.MST_ID, A.PROD_ID, A.REQUISITION_NO
from inv_transaction a where a.receive_basis = 3 and a.transaction_type = 2 and a.item_category = 1 and a.status_active=1
 group by a.mst_id, a.prod_id, a.requisition_no";
$trans_result=sql_select($sql_trans);
$issue_trans_data=array();
foreach($trans_result as $row)
{
	$issue_trans_data[$row["MST_ID"]][$row["PROD_ID"]]=$row["REQUISITION_NO"];
}

$sql_issue_rtn="select ID, ISSUE_ID, PROD_ID, REQUISITION_NO from inv_transaction 
where status_active=1 and item_category = 1 and transaction_type = 4 and receive_basis = 3 and (requisition_no is null or requisition_no=0)";
$sql_issue_rtn_result=sql_select($sql_issue_rtn);
//echo count($result);die;
$i=1;
$upTransID=true;
foreach($sql_issue_rtn_result as $row)
{
	$requision_no=$issue_trans_data[$row["ISSUE_ID"]][$row["PROD_ID"]];
	if($requision_no>0)
	{
		$upTransID=execute_query("update inv_transaction set REQUISITION_NO='".$requision_no."' where id=".$row["ID"]." ");
		if($upTransID){ $upTransID=1;} else {echo "update inv_transaction set REQUISITION_NO='".$requision_no."' where id=".$row["ID"]." ";oci_rollback($con);die;}
	}
}

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
?>