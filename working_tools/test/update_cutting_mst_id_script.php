<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

	//die;
//$sql = "SELECT b.ID as MST_ID,A.ID as COL_SIZE_BREAK_DOWN_ID FROM WO_PO_COLOR_SIZE_BREAKDOWN a, PRO_GARMENTS_PRODUCTION_MST b WHERE a.po_break_down_id= b.po_break_down_id AND a.item_number_id = b.item_number_id AND a.country_id = b.country_id and B.PRODUCTION_TYPE=1 and b.CUT_NO = 'OG-20-000074' and b.cut_no is not null AND b.status_active = 1 and b.is_deleted=0"; 
$sql = "SELECT b.ID as MST_ID,A.ID as COL_SIZE_BREAK_DOWN_ID, c.DELIVERY_MST_ID FROM WO_PO_COLOR_SIZE_BREAKDOWN a, PRO_GARMENTS_PRODUCTION_MST b, pro_garments_production_dtls c WHERE a.po_break_down_id= b.po_break_down_id AND a.item_number_id = b.item_number_id AND a.country_id = b.country_id and c.color_size_break_down_id=a.id and c.DELIVERY_MST_ID=b.DELIVERY_MST_ID and B.PRODUCTION_TYPE=1 and b.po_break_down_id=26659 and b.cut_no is not null AND b.status_active = 1 and b.is_deleted=0 AND c.status_active = 1 and c.is_deleted=0
and b.id not in (3400679,3400680,3400681, 3161333,3161339,3161993,3161999,3161334,3161340,3161332,3161335)";
//and b.CUT_NO in ('FFL-20-027532','')
$sl_res = sql_select($sql);
$mst_id_array = array();
foreach ($sl_res as $val) 
{
	$mst_id_array[$val['DELIVERY_MST_ID']][$val['COL_SIZE_BREAK_DOWN_ID']] = $val['MST_ID'];
}
$i=1;
foreach ($mst_id_array as $delId => $deldata) 
{
	foreach ($deldata as $col_size_id => $mst_id) 
	{
		$up_sql= "UPDATE pro_garments_production_dtls set mst_id='$mst_id' where color_size_break_down_id='$col_size_id' and production_type=1 AND status_active=1 and is_deleted=0 and DELIVERY_MST_ID='$delId'";
		//echo $up_sql.'<br>';
		//$up_res = execute_query($up_sql,0);
		$i++;
	}
}
oci_commit($con); 
echo "Success".$i;

//oci_commit($con);

/*select count(id) as mst_id,  po_break_down_id, item_number_id, country_id,cut_no, DELIVERY_MST_ID from PRO_GARMENTS_PRODUCTION_MST where PRODUCTION_TYPE=1 
and cut_no is not null AND DELIVERY_MST_ID!=0 and  status_active = 1 and is_deleted=0
group by  po_break_down_id, item_number_id, country_id, cut_no, DELIVERY_MST_ID having count(id)>1 order by mst_id DESC;

--278706

update PRO_GARMENTS_PRODUCTION_MST set cut_no='' where PRODUCTION_TYPE=1 and cut_no='0'
*/
?>