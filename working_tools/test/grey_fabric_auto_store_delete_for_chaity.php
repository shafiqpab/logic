<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$production_sql="select a.dtls_id, a.entry_form, b.barcode_no
from order_wise_pro_details a, pro_roll_details b
where a.entry_form = 2 and a.trans_id >0 and a.status_active =1 and a.dtls_id = b.dtls_id and b.entry_form = 2 and b.status_active =1";

$production_data = sql_select($production_sql);

foreach ($production_data as $row) {
	$production_barcode_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

}

$production_barcode_arr=array_filter(array_unique($production_barcode_arr));
//print_r($mst_ids_arr);die;

if(empty($production_barcode_arr)) {echo "No Mismatch Found"; die;}


$barcodeCond = $barcode_no_cond = ""; 
$all_production_barcode_ids = implode(",", $production_barcode_arr);
if($db_type==2 && count($production_barcode_arr)>999)
{
	$production_barcode_arr_chunk=array_chunk($production_barcode_arr,999) ;
	foreach($production_barcode_arr_chunk as $chunk_arr)
	{
		$barcodeCond.=" barcode_no in(".implode(",",$chunk_arr).") or ";	
	}
			
	$barcode_no_cond.=" and (".chop($barcodeCond,'or ').")";
}
else
{ 	
	
	$barcode_no_cond=" and barcode_no in($all_production_barcode_ids)";
}

//$rcv_sql=  "select barcode_no, qnty from pro_roll_details where status_active =1 and is_deleted =0 and entry_form =58 $barcode_no_cond";

$rcv_data=return_library_array( "select barcode_no, qnty from pro_roll_details where status_active =1 and is_deleted =0 and entry_form =58 $barcode_no_cond", "barcode_no", "barcode_no"  );


foreach ($production_data as $row) 
{
	if($rcv_data[$row[csf("barcode_no")]] == "")
	{
		$non_rcv_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}
	
}

echo "<pre>";
print_r($non_rcv_arr);
die;


	//echo "UPDATE inv_item_transfer_dtls set to_trans_id=$trans_in_id where id= ".$val[csf("dtls_id")]."  <br />";
	//$update_trans_dtls=execute_query("UPDATE inv_item_transfer_dtls set to_trans_id=$trans_in_id where id= ".$val[csf("dtls_id")]);
	//$update_prop_trans_id =execute_query("update order_wise_pro_details set trans_id =  $trans_in_id where entry_form = 83 and trans_type = 5 and  dtls_id = ".$val[csf('dtls_id')]." and status_active = 1 and is_deleted = 0 ");
	


/*oci_commit($con); 
echo "Success";
die;*/
?>