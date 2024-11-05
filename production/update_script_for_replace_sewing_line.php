<?
/*
Created by 	: Shafiq
Date 		: 18-04-2022
Comments 	: This script is applicable for replace in-active color size breakdown id by active color size breakdown in gmts production(for knit asia)
*/
die();
include('../includes/common.php');
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



$sql = "SELECT a.id as resource_id,b.id as mst_id,b.delivery_mst_id from PROD_RESOURCE_MST a, pro_garments_production_mst b where a.line_number=b.sewing_line and a.is_deleted=0 and a.line_marge=2 and b.prod_reso_allo=2 and b.status_active=1";
// echo $sql;die();
$res = sql_select($sql);
$data_arr = array();
$delv_data_arr = array();
foreach ($res as $v) 
{
	$data_arr[$v['MST_ID']] = $v['RESOURCE_ID'];
	$delv_data_arr[$v['DELIVERY_MST_ID']] = $v['RESOURCE_ID'];
}

// echo "<pre>";print_r($data_arr);die;
$id_arr = array();
$update_array_dtls="sewing_line*prod_reso_allo*updated_by";
$update_array_delv="sewing_line*updated_by";

foreach ($data_arr as $line_id => $resource_id) 
{
	$updateID_array_dtls[]=$line_id;
	$update_data_dtls[$line_id]=explode("*",$resource_id."*1*99999");
	$all_update_id_arr[$line_id]=$resource_id;
}

foreach ($delv_data_arr as $delv_id => $resource_id) 
{
	$updateID_array_delv[]=$delv_id;
	$update_data_dlv[$delv_id]=explode("*",$resource_id."*99999");
	$delv_update_id_arr[$delv_id]=$resource_id;
}


	/*$updateID_array_dtls_arr = array_chunk($updateID_array_dtls, 999);
	foreach ($updateID_array_dtls_arr as  $value) 
	{
		echo bulk_update_sql_statement2("PRO_GARMENTS_PRODUCTION_MST","ID",$update_array_dtls,$update_data_dtls,$value);
	}
	die();*/
// echo "<pre>";print_r($id_arr);die;

// echo bulk_update_sql_statement2("PRO_GARMENTS_PRODUCTION_MST","ID",$update_array_dtls,$update_data_dtls,$updateID_array_dtls);die();
// die('kakku');
if($db_type==2)
{	
	$updateID_array_dtls_arr = array_chunk($updateID_array_dtls, 999);
	foreach ($updateID_array_dtls_arr as  $value) 
	{
		$rID=execute_query(bulk_update_sql_statement2("PRO_GARMENTS_PRODUCTION_MST","ID",$update_array_dtls,$update_data_dtls,$value));
		if(!$rID)
		{
			echo "something wrong";die();
		}
	}

	$updateID_array_delv_arr = array_chunk($updateID_array_delv, 999);
	foreach ($updateID_array_delv_arr as  $value2) 
	{
		$rID2=execute_query(bulk_update_sql_statement2("PRO_GMTS_DELIVERY_MST","ID",$update_array_delv,$update_data_dlv,$value2));
		if(!$rID2)
		{
			echo "something wrong";die();
		}
	}
	// echo "$rID ** $rID2";die();
	if($rID && $rID2)
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


echo "<pre>";print_r($all_update_id_arr);
echo "<br>";
echo "<pre>";print_r($all_update_id_arr);
 


?>