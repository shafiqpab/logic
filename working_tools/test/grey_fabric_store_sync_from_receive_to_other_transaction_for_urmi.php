<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$grey_issue_sql = sql_select("select a.id as trans_id, b.id as dtls_id, c.barcode_no from inv_transaction a, inv_grey_fabric_issue_dtls b, pro_roll_details c where a.id = b.trans_id and b.id = c.dtls_id and b.mst_id = c.mst_id and c.entry_form = 61 and a.item_category = 13 and (a.store_id = 0 or a.store_id is null) and a.status_active =1 and b.status_active =1 and c.status_active =1 and c.is_returned = 0");

foreach ($grey_issue_sql as $val) 
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

$grey_receive_sql= sql_select("select c.barcode_no, z.store_id from inv_transaction z,pro_grey_prod_entry_dtls b, pro_roll_details c where z.id=b.trans_id and b.id=c.dtls_id and c.entry_form in (2,22,58) and c.status_active=1 and c.is_deleted=0 and z.status_active=1 and z.is_deleted=0 and z.transaction_type=1 and b.status_active=1 and c.status_active=1 $all_barcode_cond");

foreach ($grey_receive_sql as $val)
{
	$store_arr[$val[csf("barcode_no")]] = $val[csf("store_id")];
}

$i=1;
foreach ($grey_issue_sql as $val)
{
	$store_id = $store_arr[$val[csf("barcode_no")]];
	if($store_id)
	{
		$trans_id = $val[csf("trans_id")];
		$dtls_id = $val[csf("dtls_id")];

		$update_query =  execute_query("update inv_transaction set store_id = $store_id where id = $trans_id",0);
		$update_query =  execute_query("update inv_grey_fabric_issue_dtls set store_name = $store_id where id = $dtls_id",0);
		$i++;
	}
	else
	{
		$without_store_source[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}
}

if(!empty($without_store_source))
{
	echo "Not Updated Barcode Nos are Below : <br><br>".implode(",", $without_store_source)."<br>";
}

/*oci_commit($con);
echo "Success";
die;*/

?>