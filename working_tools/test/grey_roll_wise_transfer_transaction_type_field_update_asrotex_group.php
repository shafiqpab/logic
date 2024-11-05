<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
//$con=connect();

$transaction_id_sql =  sql_select("select b.id as dtls_id, b.trans_id, b.to_trans_id, c.trans_type, c.status_active, c.po_breakdown_id
from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
where a.id = b.mst_id and b.id=c.dtls_id and a.entry_form =82 and c.entry_form =82 and a.transfer_criteria=1 and b.status_active =1 and c.status_active=1
and b.trans_id != c.trans_id and c.trans_type=6
order by b.mst_id ");

if(empty($transaction_id_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($transaction_id_sql as $val) 
{
	$transfer_in[$val[csf("to_trans_id")]] =$val[csf("to_trans_id")];
	$transfer_out[$val[csf("trans_id")]] =$val[csf("trans_id")];

	$trans_id_arr[$val[csf("trans_id")]] =$val[csf("trans_id")];
	$trans_id_arr[$val[csf("to_trans_id")]] =$val[csf("to_trans_id")];
	
}

/*echo "<pre>";
print_r($trans_id_arr);*/


foreach ($trans_id_arr as $val) 
{
	if($transfer_in[$val] !="")
	{
		//execute_query("update inv_transaction set transaction_type='5',updated_by=990 where id=".$val,0);
		//execute_query("update order_wise_pro_details set trans_type='5',updated_by=990 where entry_form=82 and trans_id=".$val,0);

		echo "update inv_transaction set transaction_type='5',updated_by=990 where id=".$val."<br>";
		echo "update order_wise_pro_details set trans_type='5',updated_by=990 where entry_form=82 and trans_id=".$val."<br>";
	}

	if($transfer_out[$val] !="")
	{
		//execute_query("update inv_transaction set transaction_type='6',updated_by=990 where id=".$val,0);
		//execute_query("update order_wise_pro_details set trans_type='6',updated_by=990 where entry_form=82 and trans_id=".$val,0);

		echo "update inv_transaction set transaction_type='6',updated_by=990 where id=".$val."<br>";
		echo "update order_wise_pro_details set trans_type='6',updated_by=990 where entry_form=82 and trans_id=".$val."<br>";
	}

	echo "<br>";
	
}
die;


/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>