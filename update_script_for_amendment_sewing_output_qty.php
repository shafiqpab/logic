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
	 $sql_up .= " and production_type=5";
	 return $sql_up;     
}

// ========================= get first mst id ============================= (JMF-20-2273-143)
$production_sql="SELECT bundle_no,
SUM (CASE WHEN b.production_type = 4 THEN production_qnty ELSE 0 END)
	AS in_qty,
SUM (CASE WHEN b.production_type = 5 THEN production_qnty ELSE 0 END)
	AS out_qty,
SUM (CASE WHEN b.production_type = 5 THEN a.alter_qty ELSE 0 END)
	AS alter_qty,
SUM (CASE WHEN b.production_type = 5 THEN a.spot_qty ELSE 0 END)
	AS spot_qty,
SUM (CASE WHEN b.production_type = 5 THEN a.replace_qty ELSE 0 END)
	AS replace_qty,
SUM (CASE WHEN b.production_type = 5 THEN a.reject_qty ELSE 0 END)
	AS reject_qty
FROM PRO_GARMENTS_PRODUCTION_DTLS a, PRO_GARMENTS_PRODUCTION_mst b
WHERE     a.mst_id = b.id
AND a.status_active = 1
and b.production_date between '01-Jan-2023' and '31-Jul-2023'

AND a.replace_qty IS NOT NULL
AND b.production_type IN (4, 5)
GROUP BY bundle_no
HAVING (SUM (
	 CASE WHEN b.production_type = 4 THEN production_qnty ELSE 0 END) >
 (  SUM (
		CASE
			WHEN b.production_type = 5
			THEN
				a.production_qnty + a.replace_qty
			ELSE
				0
		END)
  - SUM (
		CASE
			WHEN b.production_type = 5 THEN a.alter_qty + a.spot_qty
			ELSE 0
		END)))";
// echo $production_sql;die();

$result=sql_select($production_sql);
$bundle_array = array();
$bundle_array2 = array();
foreach($result as $v)
{	
	// echo $v['BUNDLE_NO']."=".$v['IN_QTY'] ."> (".$v['OUT_QTY']."+".$v['REPLACE_QTY'].") - (".$v['ALTER_QTY']."+".$v['SPOT_QTY']."+".$v['REJECT_QTY'].")";
	if($v['OUT_QTY']>0)
	{
		if($v['IN_QTY'] > ($v['OUT_QTY']+$v['REJECT_QTY']))//+$v['REPLACE_QTY']
		{
			$qty = ($v['IN_QTY']+$v['REPLACE_QTY']) - ($v['ALTER_QTY']+$v['SPOT_QTY']+$v['REJECT_QTY']);
			$bundle_array[$v['BUNDLE_NO']] = $qty;
			$bundle_array2[$v['BUNDLE_NO']]['in'] = $v['IN_QTY'];
			$bundle_array2[$v['BUNDLE_NO']]['out'] = $v['OUT_QTY'];
		}
	}
	
}
// echo "<pre>"; print_r($bundle_array);die;
// echo count($bundle_array);die;
$update_array_tr="production_qnty";
foreach($bundle_array as $key=>$val)
{
 	$production_qnty=$val;	
	$updateID_array_tr[]="'$key'";
	$update_data_tr["'$key'"]=explode("*",("'".$production_qnty."'"));
}

echo bulk_update_sql_statement2("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr);die;

die();
if($db_type==2)
{	
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr));
	// echo "$rID";//die();
	if($rID)
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

echo "<pre>";print_r($bundle_array2);
 


?>