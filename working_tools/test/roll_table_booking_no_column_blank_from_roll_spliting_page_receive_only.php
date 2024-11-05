<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

//entry_form = 58  || entry_form = 61 individually

$roll_splited_sql = sql_select("select a.booking_no as production_booking, b.booking_no as rcv_booking, b.id as rcv_table_id from pro_roll_details a, pro_roll_details b where a.barcode_no = b.barcode_no and a.entry_form = 2 and b.entry_form = 61 and b.booking_no is null and a.booking_no is not null");

foreach($roll_splited_sql as $val)
{
	$booking_ref_data[$val[csf("rcv_table_id")]] = $val[csf("production_booking")];

	$roll_table_id_arr[$val[csf("rcv_table_id")]] = $val[csf("rcv_table_id")];
}

$roll_table_id_arr = array_filter($roll_table_id_arr);
$all_roll_ids = implode(",", $roll_table_id_arr);
$roll_id_cond=""; $roll_cond="";
if($db_type==2 && count($roll_table_id_arr)>999)
{
	$roll_table_id_arr_chunk=array_chunk($roll_table_id_arr,999) ;
	foreach($roll_table_id_arr_chunk as $chunk_arr)
	{
		$chunk_arr_value=implode(",",$chunk_arr);
		$roll_cond.="  id in($chunk_arr_value) or ";
	}

	$roll_id_cond.=" and (".chop($roll_cond,'or ').")";
}
else
{
	$roll_id_cond=" and id in($all_roll_ids)";
}


$field_array_trans_update="booking_no*updated_by";

$flag = 1;
foreach ($roll_table_id_arr as $roll_id) 
{	
	//echo "update pro_roll_details set booking_no = '$booking_ref_data[$roll_id]',updated_by=777 where id = $roll_id <br>";

	/*$barcode_all[] = $barcode_no;
	$data_array_trans_update[$barcode_no]=explode("*",($booking_from_id_arr[$roll_id]."*999"));	*/
	//echo "update pro_roll_details set booking_no_new = '$booking_from_id_arr[$roll_id]' where barcode_no = $barcode_no;<br>";

	//$update_query =  execute_query("update pro_roll_details set booking_no_bk = '$booking_from_id_arr[$roll_id]',updated_by=999 where barcode_no = $barcode_no",0);

	$update_query =  execute_query("update pro_roll_details set booking_no = '$booking_ref_data[$roll_id]',updated_by=777 where id = $roll_id",0);


}
oci_commit($con);
echo "Success"; 
die;


?>