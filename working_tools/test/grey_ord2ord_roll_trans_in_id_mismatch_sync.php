<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$transfer_out_sql="select a.id,b.id as dtls_id, (c.id+1) trans_in_trans_id, c.cons_quantity, c.prod_id
from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c
where a.id=b.mst_id and b.trans_id=c.id and a.entry_form=83 and trans_id=to_trans_id and a.status_active=1 
and b.status_active=1 and c.status_active=1 and c.transaction_type=6 and c.item_category = 13";

$transfer_out_data = sql_select($transfer_out_sql);
$mst_ids_arr=array();
foreach ($transfer_out_data as $row) {
	$mst_ids_arr[$row[csf("id")]] = $row[csf("id")];

}

$mst_ids_arr=array_filter(array_unique($mst_ids_arr));
//print_r($mst_ids_arr);die;

if(empty($mst_ids_arr)) {echo "No Mismatch Found"; die;}


$mstCond = $mst_no_cond = ""; 
$all_mst_ids = implode(",", $mst_ids_arr);
if($db_type==2 && count($mst_ids_arr)>999)
{
	$mst_ids_arr_chunk=array_chunk($mst_ids_arr,999) ;
	foreach($mst_ids_arr_chunk as $chunk_arr)
	{
		$mstCond.=" mst_id in(".implode(",",$chunk_arr).") or ";	
	}
			
	$mst_no_cond.=" and (".chop($mstCond,'or ').")";			
	
}
else
{ 	
	
	$mst_no_cond=" and mst_id in($all_mst_ids)";
}

$trans_in_sql="select id, mst_id, prod_id, cons_quantity
from inv_transaction where item_category = 13 and status_active =1 and transaction_type = 5 $mst_no_cond";

$trans_in_sql_data = sql_select($trans_in_sql);
foreach ($trans_in_sql_data as $row) 
{
	$trans_in_id_ref_arr[$row[csf("id")]][$row[csf("mst_id")]][$row[csf("prod_id")]][$row[csf("cons_quantity")]] = $row[csf("id")];
}

foreach ($transfer_out_data as  $val) 
{
	$trans_in_id  = $trans_in_id_ref_arr[$val[csf("trans_in_trans_id")]][$val[csf("id")]][$val[csf("prod_id")]][$val[csf("cons_quantity")]];

	if($trans_in_id != "")
	{
		//echo "UPDATE inv_item_transfer_dtls set to_trans_id=$trans_in_id where id= ".$val[csf("dtls_id")]."  <br />";
		$update_trans_dtls=execute_query("UPDATE inv_item_transfer_dtls set to_trans_id=$trans_in_id where id= ".$val[csf("dtls_id")]);
		$update_prop_trans_id =execute_query("update order_wise_pro_details set trans_id =  $trans_in_id where entry_form = 83 and trans_type = 5 and  dtls_id = ".$val[csf('dtls_id')]." and status_active = 1 and is_deleted = 0 ");
	}
}

oci_commit($con); 
echo "Success";
die;
?>