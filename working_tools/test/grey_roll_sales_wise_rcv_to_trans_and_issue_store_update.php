<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$grey_issue_sql = sql_select("select e.id, e.trans_id, d.barcode_no, e.store_name
from pro_roll_details d, inv_grey_fabric_issue_dtls e
where d.dtls_id = e.id and d.entry_form = 61 and d.status_active=1 and e.store_name =0
and d.barcode_no in ( select  c.barcode_no from pro_grey_prod_entry_dtls a, inv_transaction b, pro_roll_details c where a.trans_id = b.id and a.id = c.dtls_id and c.entry_form = 58
and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.store_id =50 )");


$grey_transfer_sql = sql_select("select e.id, d.barcode_no,e.from_store,e.to_store, e.trans_id, e.to_trans_id
from pro_roll_details d,  inv_item_transfer_dtls e
where d.dtls_id = e.id and d.entry_form = 133 and d.status_active=1 and (e.from_store =0 or e.to_store=0 or e.to_store is null)
and d.barcode_no in ( select  c.barcode_no from pro_grey_prod_entry_dtls a, inv_transaction b, pro_roll_details c where a.trans_id = b.id and a.id = c.dtls_id and c.entry_form = 58
and a.status_active =1 and b.status_active=1 and c.status_active=1 and b.store_id =50 )");

/*foreach ($grey_issue_sql as $val) 
{
	$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
}

if(empty($barcode_arr)){
	echo "Data not found";
	die;
}

$all_barcode_nos = implode(",", $barcode_arr);
$all_barcode_cond=""; $barCond="";
if($db_type==2 && count($barcode_arr)>999)
{
	$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
	foreach($barcode_arr_chunk as $chunk_arr)
	{
		$chunk_arr_value=implode(",",$chunk_arr);
		$barCond.=" c.barcode_no in($chunk_arr_value) or ";
	}

	$all_barcode_cond.=" and (".chop($barCond,'or ').")";
}
else
{
	$all_barcode_cond=" and c.barcode_no in($all_barcode_nos)";
}

$grey_receive_sql= sql_select("select c.barcode_no, z.store_id from inv_transaction z,pro_grey_prod_entry_dtls b, pro_roll_details c where z.id=b.trans_id and b.id=c.dtls_id and c.entry_form in (2,22,58) and c.status_active=1 and c.is_deleted=0 and z.status_active=1 and z.is_deleted=0 and z.transaction_type=1 and b.status_active=1 and c.status_active=1 and c.re_transfer=0 $all_barcode_cond");

foreach ($grey_receive_sql as $val)
{
	$store_arr[$val[csf("barcode_no")]] = $val[csf("store_id")];
}*/


$i=1;
foreach ($grey_issue_sql as $val)
{

	$trans_id = $val[csf("trans_id")];
	$dtls_id = $val[csf("id")];
	//$update_query =  execute_query("update inv_transaction set store_id = 50 where id = $trans_id",0);
	//$update_query =  execute_query("update inv_grey_fabric_issue_dtls set store_name = 50 where id = $dtls_id",0);


	echo "$i update inv_transaction set store_id = 50 where id = $trans_id";
	echo "update inv_grey_fabric_issue_dtls set store_name = 50 where id = $dtls_id";
	echo "<br>";

	$i++;
	
}

echo "======================================== =================================== ";

foreach ($grey_transfer_sql as $val)
{

	$trans_id = $val[csf("trans_id")];
	$to_trans_id = $val[csf("to_trans_id")];
	$dtls_id = $val[csf("id")];
	//$update_query =  execute_query("update inv_transaction set store_id = 50 where id = $trans_id",0);
	//$update_query =  execute_query("update inv_grey_fabric_issue_dtls set store_name = 50 where id = $dtls_id",0);


	echo "$i update inv_transaction set store_id = 50 where id = $trans_id";
	echo "$i update inv_transaction set store_id = 50 where id = $to_trans_id";
	echo "update inv_item_transfer_dtls set from_store = 50, to_store=50 where id = $dtls_id";
	echo "<br>";

	$i++;
	
}

//oci_commit($con);
//echo "Success";
//die;

?>