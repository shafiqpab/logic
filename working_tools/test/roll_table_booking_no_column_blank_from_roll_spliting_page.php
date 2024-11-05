<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$roll_splited_sql=sql_select("select a.roll_split_from , a.barcode_no from pro_roll_details a where a.entry_form = 2 and a.roll_split_from >0 and a.status_active =1 and a.is_deleted =0");

foreach($roll_splited_sql as $val)
{
	$roll_split_from_id_arr[$val[csf("roll_split_from")]] = $val[csf("roll_split_from")];
	//$split_from_id_barcode_arr[$val[csf("roll_split_from")]] .= $val[csf("barcode_no")].",";
	$split_from_id_barcode_arr[$val[csf("barcode_no")]] = $val[csf("roll_split_from")];
}

if($db_type !=0)
{
	if(count($roll_split_from_id_arr)>999)
	{
		$roll_split_from_id_chunk=array_chunk($roll_split_from_id_arr, 999);
		$arr_roll_split_from_id_cond=" and (";
		foreach ($roll_split_from_id_chunk as $value) 
		{
			$arr_roll_split_from_id_cond .="id in (".implode(",", $value).") or ";
		}
		$arr_roll_split_from_id_cond=chop($arr_roll_split_from_id_cond,"or ");
		$arr_roll_split_from_id_cond.=")";
	}
	else
	{
		$arr_roll_split_from_id_cond=" and id in (".implode(",", $roll_split_from_id_arr).")";
	}
}
else
{
	$arr_roll_split_from_id_cond=" and id in (".implode(",", $roll_split_from_id_arr).")";
}
//echo $arr_roll_split_from_id_cond;

$sql_roll_split_from_id =  sql_select("select id, booking_no, barcode_no from pro_roll_details where status_active =1 and is_deleted =0 $arr_roll_split_from_id_cond");

foreach ($sql_roll_split_from_id as $val) 
{
	$booking_from_id_arr[$val[csf("id")]] = $val[csf("booking_no")];
}


$field_array_trans_update="booking_no*updated_by";

$flag = 1;
foreach ($split_from_id_barcode_arr as $barcode_no => $roll_id) 
{	
	/*$barcode_all[] = $barcode_no;
	$data_array_trans_update[$barcode_no]=explode("*",($booking_from_id_arr[$roll_id]."*999"));	*/
	//echo "update pro_roll_details set booking_no_new = '$booking_from_id_arr[$roll_id]' where barcode_no = $barcode_no;<br>";

	//$update_query =  execute_query("update pro_roll_details set booking_no_bk = '$booking_from_id_arr[$roll_id]',updated_by=999 where barcode_no = $barcode_no",0);

	$update_query =  execute_query("update pro_roll_details set booking_no = '$booking_from_id_arr[$roll_id]',updated_by=999 where barcode_no = $barcode_no",0);


}
oci_commit($con);
echo "Success"; 
die;


?>