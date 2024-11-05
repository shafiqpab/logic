<?
include('includes/common.php');
$con = connect();

$mrr_data_arr=array(); $issue_data_arr=array();

$sql_issue="select b.prod_id, b.id as trans_id, b.cons_quantity, b.transaction_type  from inv_transaction b where b.item_category in(1) and b.transaction_type in(2,3,6) and b.status_active=1 and b.is_deleted=0";
$result_issue=sql_select($sql_issue);
foreach($result_issue as $row)
{
	$issue_data_arr[$row[csf('prod_id')]]+=$row[csf('cons_quantity')];
}
unset($result_issue);

$mrr_issue="select a.prod_id,  b.issue_qnty from inv_transaction a, inv_mrr_wise_issue_details b
where a.id=b.issue_trans_id and a.item_category=1 and a.status_active=1 and b.status_active=1
 and  a.transaction_type in(2,3,6) and a.prod_id=b.prod_id";
$result_receive=sql_select($mrr_issue);
foreach($result_receive as $row)
{
	$mrr_data_arr[$row[csf('prod_id')]]+=$row[csf('issue_qnty')];
}


unset($result_receive);

$mismass_data=array();


/*foreach($mrr_data_arr as $prod_id=>$qnty)
{
	if(number_format($issue_data_arr[$prod_id],2)!=number_format($qnty,2))
	{
		$mismass_data[$prod_id]=$issue_data_arr[$prod_id]."##".$qnty;
	}
}*/

foreach($issue_data_arr as $prod_id=>$qnty)
{
	if(number_format($qnty,2)!=number_format($mrr_data_arr[$prod_id],2))
	{
		$mismass_data[$prod_id]=$mrr_data_arr[$prod_id]."##".$qnty;
	}
}

echo count($mismass_data);
echo "<pre>";print_r($mismass_data);die;

?>