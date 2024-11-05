<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

// RECEIVE ================================================================
$receive_sql=sql_select("select a.id,a.store_id,b.id trans_id,b.store_id trans_store,b.item_category
	from inv_receive_master a, inv_transaction b
	where a.id=b.mst_id and a.entry_form in(37,52) and a.status_active=1 and b.status_active=1 and a.item_category=2 and b.item_category=2 and b.transaction_type in(1,4) and a.company_id=4");

foreach($receive_sql as $val)
{
	$receive_id[$val[csf("id")]] 	= $val[csf("store_id")];
	$prod_id[$val[csf("prod_id")]] 	= $val[csf("store_id")];

	$trans_id = $val[csf("trans_id")];
	//echo "update inv_transaction set store_id=57 where id = ".$trans_id . "==" . $val[csf("store_id")] ."<br />";
	execute_query("update inv_transaction set store_id=69 where id = ".$trans_id,0);

}

if(!empty($receive_id))
{
	foreach ($receive_id as $id => $store) {
		//echo "update inv_receive_master set store_id=57 where id = ".$id ."<br />";
		execute_query("update inv_receive_master set store_id=69 where id = ".$id,0);
	}
}

if(!empty($prod_id))
{
	foreach ($prod_id as $id => $store) {
		execute_query("update product_details_master set store_id=69 where id = ".$id,0);
	}
}
// ISSUE ================================================================
$issue_sql=sql_select("select c.id dtls_id,c.store_id,b.id trans_id,b.store_id trans_store
	from inv_issue_master a, inv_transaction b,inv_finish_fabric_issue_dtls c
	where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=18 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.item_category=2 and b.item_category=2 and b.transaction_type in(2) and a.company_id=4");

foreach($issue_sql as $val)
{
	$trans_id = $val[csf("trans_id")];
	$dtls_id = $val[csf("dtls_id")];
	//echo "update inv_transaction set store_id=57 where id = ".$trans_id ."<br />";
	execute_query("update inv_transaction set store_id=69 where id = ".$trans_id,0);
	execute_query("update inv_finish_fabric_issue_dtls set store_id=69 where id = ".$dtls_id,0);

}

$issue_sql=sql_select("select b.id trans_id,b.store_id trans_store
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and a.entry_form=46 and a.status_active=1 and b.status_active=1 and a.item_category=2 and b.item_category=2 and b.transaction_type in(3) and a.company_id=4");

foreach($issue_sql as $val)
{
	$trans_id = $val[csf("trans_id")];
	//$dtls_id = $val[csf("dtls_id")];
	//echo "update inv_transaction set store_id=57 where id = ".$trans_id ."<br />";
	execute_query("update inv_transaction set store_id=69 where id = ".$trans_id,0);
	//execute_query("update inv_finish_fabric_issue_dtls set store_id=84 where id = ".$dtls_id,0);

}

// TRANSFER ================================================================
$transfer_sql=sql_select("select a.id,a.transfer_system_id, b.id dtls_id,b.trans_id,b.to_trans_id, b.from_store,b.to_store
	from inv_item_transfer_mst a,inv_item_transfer_dtls b
	where a.company_id=4 and a.entry_form=14 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.item_category=2 and b.item_category=2");

foreach($transfer_sql as $val)
{
	$trans_id = $val[csf("trans_id")];
	$to_trans_id = $val[csf("to_trans_id")];
	$dtls_id = $val[csf("dtls_id")];

	if($trans_id>0)
		execute_query("update inv_transaction set store_id=69 where id = ".$trans_id,0);

	if($to_trans_id>0)
		execute_query("update inv_transaction set store_id=69 where id = ".$to_trans_id,0);

	execute_query("update inv_item_transfer_dtls set from_store=69,to_store=69 where id = ".$dtls_id,0);

}

// ================================================================

oci_commit($con);
echo "Success";
die;


?>