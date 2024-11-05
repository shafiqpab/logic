<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$rcv_sql="select a.recv_number, a.id, b.id as dtls_id, c.id as trans_id, c.prod_id, c.cons_quantity, c.cons_amount
from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_transaction c
where a.id=b.mst_id and b.trans_id=c.id and a.entry_form =22 and c.transaction_type=1 and c.item_category=13 and a.status_active =1 and b.status_active =1 and c.status_active =1";

$rcv_data = sql_select($rcv_sql);
if(empty($rcv_data)) {echo "No Mismatch Found"; die;}

$mst_ids_arr=array();
foreach ($rcv_data as $row) 
{
	$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
	$prod_qnty_arr[$row[csf("prod_id")]]["qnty"] += $row[csf("cons_quantity")];
}
$prod_id_arr=array_filter(array_unique($prod_id_arr));

$prodCond = $prod_id_cond = ""; 
$all_prod_ids = implode(",", $prod_id_arr);
if($db_type==2 && count($prod_id_arr)>999)
{
	$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
	foreach($prod_id_arr_chunk as $chunk_arr)
	{
		$prodCond.=" id in(".implode(",",$chunk_arr).") or ";	
	}
			
	$prod_id_cond.=" and (".chop($prodCond,'or ').")";			
	
}
else
{
	
	$prod_id_cond=" and id in($all_prod_ids)";
}

$product_sql="select id, current_stock, stock_value, avg_rate_per_unit from product_details_master where item_category_id =13 and status_active =1 $prod_id_cond";
$product_result = sql_select($product_sql);
foreach ($product_result as $row) 
{
	$product_ref_data[$row[csf("id")]]["id"] = $row[csf("id")];
	$product_ref_data[$row[csf("id")]]["current_stock"] = $row[csf("current_stock")];
	$product_ref_data[$row[csf("id")]]["avg_rate_per_unit"] = $row[csf("avg_rate_per_unit")];
	$product_ref_data[$row[csf("id")]]["stock_value"] = $row[csf("stock_value")];
}


foreach ($product_result as $row) 
{
	$upd_qnty = $product_ref_data[$row[csf("id")]]["current_stock"] - $prod_qnty_arr[$row[csf("id")]]["qnty"];
	if($upd_qnty<0)
	{
		$upd_qnty =0;
	}

	$upd_amnt = $upd_qnty * $product_ref_data[$row[csf("id")]]["avg_rate_per_unit"];
	if($upd_amnt < 0){
		$upd_amnt = 0;
	}
	$upd_amnt = number_format($upd_amnt,4,".","");
	
	//echo "UPDATE product_details_master set current_stock=$upd_qnty, stock_value=$upd_amnt where id= ".$row[csf("id")]."  <br />";
	execute_query("UPDATE product_details_master set current_stock=$upd_qnty, stock_value=$upd_amnt where id= ".$row[csf("id")],0);
}

foreach ($rcv_data as  $val) 
{
	//echo "UPDATE inv_receive_master set status_active=0, is_deleted=1 where id= ".$row[csf("id")]."  <br />";
	//echo "UPDATE pro_grey_prod_entry_dtls set status_active=0, is_deleted=1 where id= ".$row[csf("dtls_id")]."  <br />";
	//echo "UPDATE inv_transaction set status_active=0, is_deleted=1 where id= ".$row[csf("trans_id")]."  <br />";

	execute_query("UPDATE inv_receive_master set status_active=0, is_deleted=1 where id= ".$row[csf("id")],0);
	execute_query("UPDATE pro_grey_prod_entry_dtls set status_active=0, is_deleted=1 where id= ".$row[csf("dtls_id")],0);
	execute_query("UPDATE inv_transaction set status_active=0, is_deleted=1 where id= ".$row[csf("trans_id")],0);
}

oci_commit($con); 
echo "Success";
die;
?>