<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
die;
$po_item_arr=return_library_array( "SELECT a.id as id ,b.gmts_item_id as item  from  wo_po_break_down a,wo_po_details_mas_set_details b where a.job_no_mst=b.job_no   ",'id','item');
//print_r($po_item_arr);die;
$sql="SELECT po_break_down_id from PPL_SEWING_PLAN_BOARD where item_number_id is null or item_number_id=0";
foreach(sql_select($sql) as $val)
{
	$po=$val[csf("po_break_down_id")];
	$item=$po_item_arr[$po];
	$query=execute_query("update ppl_sewing_plan_board set item_number_id='$item' where po_break_down_id='$po'",1); 
} 
 
$sql2="SELECT po_break_down_id from ppl_sewing_plan_board_powise where item_number_id is null or item_number_id=0";
foreach(sql_select($sql2) as $val)
{
	$po=$val[csf("po_break_down_id")];
	$item=$po_item_arr[$po];
	$query=execute_query("update ppl_sewing_plan_board_powise set item_number_id='$item' where po_break_down_id='$po'",1); 
}


oci_commit($con); 
echo "Success";

 
?>