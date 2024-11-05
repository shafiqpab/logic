<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die;
/*$colormstarr=sql_select( "select min(id) as rid, po_break_down_id, color_number_id from wo_po_color_size_breakdown where 1=1 and status_active=1 and is_deleted=0 group by po_break_down_id, color_number_id ");//
//print_r($job_smv_arr);die;and a.job_no='FAL-20-00285'
$c=1;
foreach($colormstarr as $val)
{
	//echo "update wo_po_color_size_breakdown set color_mst_id=".$val[csf("rid")]." where po_break_down_id='".$val[csf("po_break_down_id")]."' and color_number_id='".$val[csf("color_number_id")]."'<br>";
	
	$query=execute_query("update wo_po_color_size_breakdown set color_mst_id=".$val[csf("rid")]." where po_break_down_id='".$val[csf("po_break_down_id")]."' and color_number_id='".$val[csf("color_number_id")]."' and status_active=1 and is_deleted=0",1);
	$c++;
}*/


$sizemstarr=sql_select( "select min(id) as rid, po_break_down_id, size_number_id from wo_po_color_size_breakdown where 1=1 and size_mst_id=0 group by po_break_down_id, size_number_id ");//status_active=1 and is_deleted=0
//print_r($job_smv_arr);die;and a.job_no='FAL-20-00285'
$s=1;
foreach($sizemstarr as $val)
{
	//echo "update wo_po_color_size_breakdown set color_mst_id=".$val[csf("rid")]." where po_break_down_id='".$val[csf("po_break_down_id")]." and color_number_id='".$val[csf("color_number_id")]."'<br>";
	
	$query=execute_query("update wo_po_color_size_breakdown set size_mst_id=".$val[csf("rid")]." where po_break_down_id='".$val[csf("po_break_down_id")]."' and size_number_id='".$val[csf("size_number_id")]."'",1);
	$s++;
}
/*
$itemmstarr=sql_select( "select min(id) as rid, po_break_down_id, item_number_id from wo_po_color_size_breakdown where 1=1 group by po_break_down_id, item_number_id ");//status_active=1 and is_deleted=0
//print_r($job_smv_arr);die;and a.job_no='FAL-20-00285'
$i=1;
foreach($itemmstarr as $val)
{
	//echo "update wo_po_color_size_breakdown set color_mst_id=".$val[csf("rid")]." where po_break_down_id='".$val[csf("po_break_down_id")]." and color_number_id='".$val[csf("color_number_id")]."'<br>";
	
	$query=execute_query("update wo_po_color_size_breakdown set item_mst_id=".$val[csf("rid")]." where po_break_down_id='".$val[csf("po_break_down_id")]."' and item_number_id='".$val[csf("item_number_id")]."'",1);
	$i++;
}*/


oci_commit($con); 
echo "Success=".$c.'='.$s.'='.$i;

 
?>