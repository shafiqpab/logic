<?
die;
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
$production_sql="SELECT count(b.bundle_no),b.BUNDLE_NO,min(b.mst_id) as MAX_MSTID from pro_garments_production_mst
 a, pro_garments_production_dtls b where a.id=b.mst_id and b.production_type =5 and   b.status_active=1 and b.is_deleted=0 and b.is_rescan=0  and b.bundle_no in('JMF-22-25611-66',
'JMF-23-4201-72',
'JMF-22-26407-48',
'JMF-22-26407-50',
'JMF-23-4201-111',
'JMF-23-4201-107',
'JMF-23-4201-80',
'JMF-23-3497-43',
'JMF-22-26407-54',
'JMF-22-26407-86',
'JMF-22-26407-93',
'JMF-22-26407-74',
'JMF-22-26407-51',
'JMF-22-26407-72',
'JMF-22-25611-129',
'JMF-22-25611-118',
'JMF-23-4201-124',
'JMF-23-4201-119',
'JMF-23-3955-54',
'JMF-23-3497-22',
'JMF-22-25611-86',
'JMF-22-26407-92',
'JMF-22-25611-114',
'JMF-23-4201-106',
'JMF-23-4201-123',
'JMF-22-25611-85',
'JMF-22-26407-75',
'JMF-22-26407-71',
'JMF-23-4201-82',
'JMF-23-4201-117',
'JMF-23-4201-81',
'JMF-23-4201-92',
'JMF-22-25611-67',
'JMF-22-26407-63',
'JMF-22-26407-81',
'JMF-22-25611-126',
'JMF-23-4201-98',
'JMF-23-4201-116',
'JMF-23-4060-4',
'JMF-23-4201-108',
'JMF-23-4201-120',
'JMF-23-4201-112'
)  having  count(b.bundle_no)>1
           group by b.bundle_no";
// echo $production_sql;die();           
//and a.production_date BETWEEN '01-Nov-2019' AND '30-Nov-2019'

$result=sql_select($production_sql);
foreach($result as $val)
{
	// 1st mst id will not update or delete
	$prod_sql="SELECT BUNDLE_NO, MST_ID,DELIVERY_MST_ID from pro_garments_production_dtls where production_type =5 and   status_active=1 and is_deleted=0 and is_rescan=0 and mst_id not in($val[MAX_MSTID]) and bundle_no='$val[BUNDLE_NO]'
           group by bundle_no,mst_id, delivery_mst_id";
    $res=sql_select($prod_sql);       
   	foreach($res as $vals)
   	{
   		$bundle_wise_duplicate_id[$vals["MST_ID"]]=$vals['BUNDLE_NO'];
   		$bundle_wise_duplicate_delv_id[$vals["DELIVERY_MST_ID"]]=$vals['DELIVERY_MST_ID'];
   	}	
}
// echo $prod_sql;die();
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
	/*if($rID && $rID2 && $rID3)
	{
		mysql_query("COMMIT");
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "Failed";
	}*/
}

echo "<pre>";print_r($all_update_id_arr);
 


?>