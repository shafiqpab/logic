<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

 $sql_receive=sql_select("select a.recv_trans_id, a.issue_trans_id, b.fabric_shade from inv_mrr_wise_issue_details a , inv_transaction b where a.entry_form = 18 and a.recv_trans_id = b.id  and b.status_active =1 and a.status_active =1 ");


if(empty($sql_receive))
{
	echo "Data Not Found";
	die;
}

foreach($sql_receive as $row)
{
	$recv_shade_array[$row[csf('issue_trans_id')]]=$row[csf('fabric_shade')];
}

foreach ($recv_shade_array as  $issue_id => $fabric_shade ) 
{
	//echo "update inv_finish_fabric_issue_dtls set fabric_shade = '".$fabric_shade."',  updated_by = 777 where trans_id=$issue_id  <br>";
	//echo "update inv_transaction set fabric_shade = '".$fabric_shade."',  updated_by = 777 where id=$issue_id  <br>";

	execute_query("update inv_finish_fabric_issue_dtls set fabric_shade = '".$fabric_shade."',  updated_by = 777 where trans_id=".$issue_id,0);
	execute_query("update inv_transaction set fabric_shade = '".$fabric_shade."',  updated_by = 777 where id=".$issue_id,0);
}


mysql_query("COMMIT");
echo "Success"; 
die;



?>