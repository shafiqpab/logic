<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$ord2ord_sql=sql_select("select a.barcode_no , b.to_trans_id, a.dtls_id
	from pro_roll_details a, inv_item_transfer_dtls b,inv_transaction c
	where a.entry_form in (82,83,183,110,180,133) and a.status_active =1 and a.re_transfer=0
	and a.dtls_id = b.id and b.to_trans_id=c.id and c.store_id=0");

if(empty($ord2ord_sql))
{
	echo "Data Not Found";
	die;
}

foreach($ord2ord_sql as $val)
{
	$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	/*$trans_in_arr[$val[csf("to_trans_id")]] = $val[csf("to_trans_id")];
	$dtls_arr[$val[csf("dtls_id")]] = $val[csf("dtls_id")];

	$transBarcode[$val[csf("to_trans_id")]] = $val[csf("barcode_no")];
	$dtlsBarcode[$val[csf("dtls_id")]] = $val[csf("barcode_no")];*/
}


$barcode_arr = array_filter($barcode_arr);
$barcode_nos = implode(",", $barcode_arr);
$barCond = $all_barcode_no_cond = "";
if($db_type==2 && count($barcode_arr)>999)
{
	$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
	foreach($barcode_arr_chunk as $chunk_arr)
	{
		$barCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
	}
	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
}
else
{
	$all_barcode_no_cond=" and a.barcode_no in($barcode_nos)";
}


$store_ref_sql = sql_select("select a.barcode_no, a.entry_form , b.store_id, c.to_store
	from pro_roll_details a left join inv_receive_master b on a.mst_id = b.id and b.entry_form in (2,22,58) and a.entry_form in (2,22,58) and b.status_active = 1
	left join  inv_item_transfer_dtls c on a.dtls_id = c.id and a.entry_form = 82 and c.status_active = 1
	where a.entry_form in (2,22,58,82) and a.status_active =1 and a.barcode_no > 0
	$all_barcode_no_cond
	order by a.barcode_no,a.entry_form");

foreach ($store_ref_sql as $val)
{
	if($val[csf("entry_form")] == 2 ||$val[csf("entry_form")] == 22 || $val[csf("entry_form")] == 58){
		$barcode_store_ref[$val[csf("barcode_no")]] = $val[csf("store_id")];
	}
	else if($val[csf("entry_form")] == 82 && $val[csf("to_store")] !=0)
	{
		$barcode_store_ref[$val[csf("barcode_no")]] = $val[csf("to_store")];
	}
}

/*$trans_in_arr = array_filter($trans_in_arr);
$BARCODE_NO = "";
foreach ($trans_in_arr as  $trans_id)
{
	$BARCODE_NO =  $transBarcode[$trans_id];
	//echo "update inv_transaction set store_id = '".$barcode_store_ref[$BARCODE_NO]. "', updated_by = 999 where id = ".$trans_id." <br>";
	execute_query("update inv_transaction set store_id = '".$barcode_store_ref[$BARCODE_NO]. "', updated_by = 999 where id = ".$trans_id,0);
}


$dtls_arr = array_filter($dtls_arr);
$BARCODE_NO = "";
foreach ($dtls_arr as  $dtls_id)
{
	$BARCODE_NO =  $dtlsBarcode[$dtls_id];
	//echo "update inv_item_transfer_dtls set to_store = '".$barcode_store_ref[$BARCODE_NO]. "', updated_by = 999 where id = ".$dtls_id." <br>";
	execute_query("update inv_item_transfer_dtls set to_store = '".$barcode_store_ref[$BARCODE_NO]. "', updated_by = 999 where id = ".$dtls_id,0);
}*/

foreach($ord2ord_sql as $val)
{
	$trans_id = $val[csf("to_trans_id")];
	$dtls_id = $val[csf("dtls_id")];
	$barcode_no = $val[csf("barcode_no")];
	echo "update inv_transaction set store_id = '".$barcode_store_ref[$barcode_no]. "', updated_by = 999 where id = ".$trans_id;
	//execute_query("update inv_transaction set store_id = '".$barcode_store_ref[$barcode_no]. "', updated_by = 999 where id = ".$trans_id,0);
	//execute_query("update inv_item_transfer_dtls set to_store = '".$barcode_store_ref[$barcode_no]. "', updated_by = 999 where id = ".$dtls_id,0);
}


//$update_query =  execute_query("update pro_roll_details set booking_no = '$booking_from_id_arr[$roll_id]',updated_by=999 where barcode_no = $barcode_no",0);


oci_commit($con);
echo "Success";
die;


?>