<?
/*
Created by 	: Shafiq
Date 		: 10-06-2021
Comments 	: This script is applicable for duplicate bundle with different challan without rescan
*/
include('includes/common.php');
$con = connect();
function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
	$sql_up.= "UPDATE $table SET ";
	
	 for ($len=0; $len<count($field_array); $len++)
	 {
		 $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
		 for ($id=0; $id<count($id_count); $id++)
		 {
			 if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
			 else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
		 }
		 if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
	 }
	 if(count($id_count)>999)
	 {
		$sql_up.=" where";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}

// ========================= get first mst id ============================= (JMF-20-2273-143)
$production_sql="SELECT bundle_no,
SUM (CASE WHEN b.production_type = 4 THEN production_qnty ELSE 0 END)
	AS in_qty,
SUM (CASE WHEN b.production_type = 1 THEN production_qnty ELSE 0 END)
	AS cut_qty
FROM PRO_GARMENTS_PRODUCTION_DTLS a, PRO_GARMENTS_PRODUCTION_mst b
WHERE     a.mst_id = b.id
AND a.status_active = 1
AND b.production_date > '01-Apr-2023'
AND b.COMPANY_ID = 1
AND b.production_type IN (4, 4)
AND b.PO_BREAK_DOWN_ID IN (10125)
GROUP BY bundle_no
HAVING (SUM (
              CASE WHEN b.production_type = 4 THEN production_qnty ELSE 0 END) >
          (SUM (
               CASE
                   WHEN b.production_type = 1 THEN a.production_qnty
                   ELSE 0
               END)))";
// echo $production_sql;die();           

$result=sql_select($production_sql);
$bundle_array = array();
foreach($result as $v)
{
	$bundle_array[$v['BUNDLE_NO']] = $v['CUT_QTY'];
	
}
echo "<pre>"; print_r($bundle_array);die;
$all_update_id_arr = array();
$update_array_dtls="status_active*is_deleted";
$update_array_mst="status_active*is_deleted*updated_by*update_date";
foreach($bundle_wise_duplicate_id as $mst_id=>$bundle_no)
{
 	$updateID_array_tr[]=$mst_id;
	$update_data_dtls[$mst_id]=explode("*",("'0'*'1'"));
	$update_data_mst[$mst_id]=explode("*",("'0'*'1'*99999*'".$pc_date_time."'"));
	$all_update_id_arr[mst_id] .= $mst_id.",";
}

// =====for delivery ==============
foreach($bundle_wise_duplicate_delv_id as $delv_id=>$delv_id_val)
{
 	$updateID_array_delv_id[]=$delv_id;
	$update_data_delv[$delv_id]=explode("*",("'0'*'1'*99999*'".$pc_date_time."'"));
	$all_update_id_arr[delivery_mst_id] .= $delv_id.",";
}

// echo bulk_update_sql_statement2("pro_garments_production_dtls","mst_id",$update_array_dtls,$update_data_dtls,$updateID_array_tr);die();
// echo bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr);die();
// echo bulk_update_sql_statement2("pro_gmts_delivery_mst","id",$update_array_mst,$update_data_delv,$updateID_array_delv_id);die();


if($db_type==2)
{	
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","mst_id",$update_array_dtls,$update_data_dtls,$updateID_array_tr));
	$rID2=execute_query(bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr));
	$rID3=execute_query(bulk_update_sql_statement2("pro_gmts_delivery_mst","id",$update_array_mst,$update_data_delv,$updateID_array_delv_id));
	echo "$rID ** $rID2 ** $rID3";//die();
	if($rID && $rID2 && $rID3)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}
else
{
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","mst_id",$update_array_dtls,$update_data_dtls,$updateID_array_tr));
	$rID2=execute_query(bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr));
	$rID3=execute_query(bulk_update_sql_statement2("pro_gmts_delivery_mst","id",$update_array_mst,$update_data_delv,$updateID_array_delv_id));
	// echo "$rID ** $rID2 ** $rID3";die();
	if($rID && $rID2 && $rID3)
	{
		mysql_query("COMMIT");
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "Failed";
	}
}

echo "<pre>";print_r($all_update_id_arr);
 


?>