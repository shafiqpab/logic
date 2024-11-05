<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$transaction_id_sql =  sql_select("select b.id, b.trans_type
from 
    (select b.id, b.transaction_type
    from inv_item_transfer_mst a, inv_transaction b 
    where a.id = b.mst_id and a.entry_form = 82 and a.transfer_criteria=1 and a.item_category = 13 
    and b.item_category = 13 and b.transaction_type in (5,6) and b.status_active = 1 and a.status_active = 1
    ) a, order_wise_pro_details b
    where a.id = b.trans_id and b.entry_form = 82
    and a.transaction_type != b.trans_type ");

foreach ($transaction_id_sql as $val) 
{
	if($val[csf("trans_type")] == 5){
		execute_query("update order_wise_pro_details set trans_type='6',updated_by=999 where id=".$val[csf("id")],0);
	}else{
		execute_query("update order_wise_pro_details set trans_type='5',updated_by=999 where id=".$val[csf("id")],0);
	}
	
}

/*foreach ($transaction_id_sql as $val) 
{	
	echo "update inv_transaction set transaction_type='".$trans_id_type_arr[$val[csf("id")]]."',updated_by=999 where id=".$val[csf("id")]."<br>";
	//execute_query("update inv_transaction set transaction_type='".$trans_id_type_arr[$val[csf("id")]]."',updated_by=999 where id=".$val[csf("id")],0);
}*/

oci_commit($con); 
echo "Success";
disconnect($con);
die;
 
?>