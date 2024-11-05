<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sql =  sql_select("select a.barcode_no, a.qnty rcv_qnty, b.qnty as trans_qnty, b.id as roll_table_id, c.id as dtls_id, c.trans_id, c.to_trans_id
from pro_roll_details a, pro_roll_details b , inv_item_transfer_dtls c
where a.entry_form=58 and b.entry_form =133 and  a.barcode_no = b.barcode_no and round(a.qnty,2) != round(b.qnty,2) and a.status_active =1 and b.status_active=1
and b.dtls_id = c.id and round(b.qnty,2) = round(c.transfer_qnty,2)
group by a.barcode_no, a.qnty, b.qnty, b.id, c.id, c.trans_id, c.to_trans_id order by a.barcode_no");


if(empty($sql))
{
	echo "Data Not Found";
	die;
}

foreach ($sql as $val) 
{
	echo "update pro_roll_details set qnty='".$val[csf('rcv_qnty')]."' where id=".$val[csf("roll_table_id")]."<br>";
	echo "update inv_item_transfer_dtls set transfer_qnty='".$val[csf('rcv_qnty')]."' where id=".$val[csf("dtls_id")]."<br>";
	echo "update inv_transaction set cons_quantity='".$val[csf('rcv_qnty')]."' where id in (".$val[csf("trans_id")].",".$val[csf("to_trans_id")].")<br>";
	echo "update order_wise_pro_details set quantity='".$val[csf('rcv_qnty')]."' where entry_form=133 and trans_id in (".$val[csf("trans_id")].",".$val[csf("to_trans_id")].")<br>";
	//execute_query("update pro_grey_prod_delivery_dtls set current_delivery='".$val[csf('qnty')]."' where id=".$val[csf("id")],0);
}

/*oci_commit($con); 
echo "Success";
disconnect($con);*/
die;
 
?>