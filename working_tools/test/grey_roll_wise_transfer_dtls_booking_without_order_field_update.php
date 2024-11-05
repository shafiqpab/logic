<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$from_receive_barcode_trans = sql_select("select a.barcode_no, b.id as dtls_id from pro_roll_details a, inv_item_transfer_dtls b where a.entry_form = 82 and a.status_active = 1 and a.dtls_id = b.id and b.status_active =1 and b.from_trans_entry_form =58 ");

foreach ($from_receive_barcode_trans as $val) 
{
	$dtls_id_arr[$val[csf("dtls_id")]] = $val[csf("dtls_id")];
	$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

	$barcode_dtls_id_arr[$val[csf("dtls_id")]] = $val[csf("barcode_no")];
}


$barcode_nos = implode(",", array_filter(array_unique($barcode_no_arr)));

if($barcode_nos=="") {echo "No Blank Challan Found"; die;}

$barCond = $barcode_no_cond = ""; 
$barcode_no_arr=explode(",",$barcode_nos);
if($db_type==2 && count($barcode_no_arr)>999)
{
	$barcode_no_chunk=array_chunk($barcode_no_arr,999) ;
	foreach($barcode_no_chunk as $chunk_arr)
	{
		$barCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";	
	}
			
	$barcode_no_cond.=" and (".chop($barCond,'or ').")";
}
else
{ 	
	
	$barcode_no_cond=" and a.barcode_no in($barcode_nos)";
}


	$ref_sql_from_issue = sql_select("select a.barcode_no, a.booking_without_order
	from pro_roll_details a
	where a.entry_form = 58 and a.status_active = 1 and a.is_deleted=0 $barcode_no_cond ");

	foreach ($ref_sql_from_issue as $row) 
	{
		$ref_data_arr[$row[csf("barcode_no")]]["booking_without_order"] = $row[csf("booking_without_order")];
	}

	//$ref_data_arr[$barcode_dtls_id_arr[$val[csf("dtls_id")]]]["booking_without_order"];

	$flag=1;
	foreach ($from_receive_barcode_trans as $val) 
	{	
		//echo "update inv_item_transfer_dtls set from_booking_without_order='".$ref_data_arr[$barcode_dtls_id_arr[$val[csf("dtls_id")]]]["booking_without_order"]."',updated_by=999 where id=".$val[csf("dtls_id")]."<br>";
		execute_query("update inv_item_transfer_dtls set from_booking_without_order='".$ref_data_arr[$barcode_dtls_id_arr[$val[csf("dtls_id")]]]["booking_without_order"]."',updated_by=999 where id=".$val[csf("dtls_id")],0);
	}

	oci_commit($con); 
	echo "Success";
	disconnect($con);
	die;
 
?>