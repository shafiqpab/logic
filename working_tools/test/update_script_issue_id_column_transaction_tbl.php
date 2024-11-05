<?
include('../includes/common.php');
$con = connect();

//,6,7,23 and b.prod_id <> 15883
$issue_return_sql ="select b.id, a.booking_id as requisition_no,b.prod_id from inv_receive_master a, inv_transaction b  where a.id=b.mst_id and  a.receive_basis = 3 and b.transaction_type = 4 and b.item_category = 1and b.issue_id is null and b.status_active = 1";

$issue_return_result=sql_select($issue_return_sql);

foreach($issue_return_result as $row)
{
	$requisition_no .= $row[csf("requisition_no")].",";
}

$requisition_numbers = implode(",",array_unique(explode(",",chop($requisition_no,','))));

if($requisition_no!="")
{
	$issue_sql = "select b.mst_id,b.requisition_no,b.prod_id from inv_issue_master a ,inv_transaction b where a.id=b.mst_id and a.item_category=1 and a.issue_basis = 3 and b.transaction_type = 2 and b.item_category = 1 and b.requisition_no in($requisition_numbers) and b.status_active =1 group by b.mst_id,b.requisition_no,b.prod_id";

	$issue_result=sql_select($issue_sql);
} 

$requisition_issue_arr = array();
foreach($issue_result as $row)
{
	if($row[csf("requisition_no")]!="")
	{
		$requisition_issue_arr[$row[csf("requisition_no")]][$row[csf("prod_id")]]['issue_id'] = $row[csf("mst_id")];
	}
	
}

//echo "<pre>";
//print_r($requisition_issue_arr);
//die();
//echo count($result);die;
$i=1;
$k=1;
//$update_field="cons_rate*cons_amount*updated_by*update_date";
$upTransID=true;
foreach($issue_return_result as $row)
{	
	$issue_id = $requisition_issue_arr[$row[csf("requisition_no")]][$row[csf("prod_id")]]['issue_id']; 

	if($issue_id!="")
	{
		$upTransID=execute_query("update inv_transaction set issue_id='".$issue_id."' where prod_id=".$row[csf("prod_id")]." and id=".$row[csf("id")]." ");

		//if($upTransID){ $upTransID=1; } else {echo "update inv_transaction set issue_id='".$issue_id."'";oci_rollback($con);die;}
	}
	
}

die();
//echo "<pre>";print_r($rcv_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
	{
		oci_commit($con); 
		echo "Porduct Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Porduct Data Update Failed";
		die;
	}
}
?>