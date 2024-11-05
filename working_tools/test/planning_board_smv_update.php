<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$smv_arr=array();
$smv_sql="SELECT   b.id ,a.GMTS_ITEM_ID,  a.SMV_PCS FROM wo_po_details_mas_set_details a,WO_PO_BREAK_DOWN b where a.job_no=b.job_no_mst and b.status_active=1 group by b.id ,a.GMTS_ITEM_ID,  a.SMV_PCS ";
foreach(sql_select($smv_sql) as $v)
{
	$smv_arr[$v[csf("ID")]][$v[csf("GMTS_ITEM_ID")]]=$v[csf("SMV_PCS")];
}
//print_r($smv_arr);die;

$update_sql="SELECT   PLAN_ID, PO_BREAK_DOWN_ID,  ITEM_NUMBER_ID FROM PPL_SEWING_PLAN_BOARD_POWISE where smv=0 or  smv is null  ";
foreach(sql_select($update_sql) as  $val)
{
	
	$PLAN_ID=$val[csf("PLAN_ID")];	 
	$PO_BREAK_DOWN_ID=$val[csf("PO_BREAK_DOWN_ID")];
	$ITEM_NUMBER_ID=$val[csf("ITEM_NUMBER_ID")];
	$SMV=$smv_arr[$PO_BREAK_DOWN_ID][$ITEM_NUMBER_ID];

	 
	 
	$up_mst=execute_query("UPDATE ppl_sewing_plan_board_powise set smv='$SMV' where PLAN_ID='$PLAN_ID'  and PO_BREAK_DOWN_ID='$PO_BREAK_DOWN_ID' and ITEM_NUMBER_ID='$ITEM_NUMBER_ID' ");
	 
	 
	 
}

 
	oci_commit($con); 
	echo "Success";

 



 
?>