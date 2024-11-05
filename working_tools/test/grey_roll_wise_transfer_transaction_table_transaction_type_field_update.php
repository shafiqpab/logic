<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

//update inv_transaction set transaction_type_bk = transaction_type;

$transaction_id_sql =  sql_select("select b.id, b.transaction_type, c.id as prop_id, c.trans_type
from inv_item_transfer_mst a, inv_transaction b, order_wise_pro_details c 
where a.id = b.mst_id and b.id = c.trans_id and a.entry_form = 82 and a.transfer_criteria=1 
and a.item_category = 13 and b.item_category = 13 and b.transaction_type in (5,6) and b.status_active = 1 and a.status_active = 1 ");
if(empty($transaction_id_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($transaction_id_sql as $val) 
{
	if($val[csf("transaction_type")] == 5){
		execute_query("update inv_transaction set transaction_type='6',updated_by=999 where id=".$val[csf("id")],0);
	}else{
		execute_query("update inv_transaction set transaction_type='5',updated_by=999 where id=".$val[csf("id")],0);
	}

	if($val[csf("trans_type")] == 5){
		execute_query("update order_wise_pro_details set trans_type='6',updated_by=999 where id=".$val[csf("prop_id")],0);
	}else{
		execute_query("update order_wise_pro_details set trans_type='5',updated_by=999 where id=".$val[csf("prop_id")],0);
	}
	
}

/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>