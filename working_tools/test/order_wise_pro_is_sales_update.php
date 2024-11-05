<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$return_issue_ids_sql="select a.id trans_id,a.issue_id from inv_transaction a where a.transaction_type=4 and a.status_active=1 and prod_id=12008";
$row_data=sql_select($return_issue_ids_sql);
foreach ($row_data as $issue_row) {
	$return_issue_arr[$issue_row[csf("trans_id")]]=$issue_row[csf("issue_id")];
}*/

$issue_id_sql="select a.id issue_id,b.id,c.po_breakdown_id, c.is_sales from inv_issue_master a,inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.trans_type=2 and c.entry_form=3";// and a.id in(".implode(",",$return_issue_arr).") // and c.is_sales=1

$issue_data=sql_select($issue_id_sql);
$issue_arr=array();
foreach ($issue_data as $row) {
	$issue_arr[$row[csf("issue_id")]]=$row[csf("is_sales")];
}

$return_issue_id_sql2="select a.id trans_id,a.issue_id,a.prod_id from inv_transaction a where a.transaction_type=4 and a.status_active=1 and item_category=1"; // and prod_id=12008
$update_row_data=sql_select($return_issue_id_sql2);
foreach ($update_row_data as $issue_row) {
	$prod_id = $issue_row[csf("prod_id")];
	$trans_id = $issue_row[csf("trans_id")];
	$is_sales = $issue_arr[$issue_row[csf("issue_id")]];
	$update_delivery_mst=execute_query("UPDATE order_wise_pro_details set is_sales=$is_sales where trans_id=$trans_id and prod_id=$prod_id and status_active=1 AND trans_type=4 and entry_form=9");
	//echo "UPDATE order_wise_pro_details set is_sales=$is_sales where trans_id=$trans_id and prod_id=$prod_id and status_active=1 AND trans_type=4 and entry_form=9";
}



/*echo "<pre>";
print_r($issue_arr); die;*/

if($update_delivery_mst)
{
	oci_commit($con); 
	echo "Success";

}
else
{
	oci_rollback($con);
	echo "failed";
}




?>