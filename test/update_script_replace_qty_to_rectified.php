<?
die;
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
	 $sql_up .= " and production_type=5";
	 return $sql_up;     
}

$replace_sql ="SELECT A.ID,B.ID as DTLS_ID,A.PRODUCTION_QUANTITY,B.PRODUCTION_QNTY,B.REPLACE_QTY
	from pro_garments_production_mst a,pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=5 and a.status_active=1 and b.production_type=5 and b.status_active=1
	and a.is_tab=1 and b.replace_qty=1"; // and a.id=2400975
// echo $replace_sql;die();   

$result=sql_select($replace_sql);
foreach($result as $val)
{ 
	$data_arr[$val["ID"]]['DTLS_ID']=$val['DTLS_ID'];
	$data_arr[$val["ID"]]['PRODUCTION_QUANTITY']=$val['REPLACE_QTY'];
	$data_arr[$val["ID"]]['PRODUCTION_QNTY']=$val['REPLACE_QTY'];
	$data_arr[$val["ID"]]['REPLACE_QTY']=$val['REPLACE_QTY'];
   		
}
// echo"<pre>"; print_r($data_arr);die();
$all_update_id_arr = array();
$update_array_dtls="PRODUCTION_QNTY*REPLACE_QTY*RECTIFIED_QTY*updated_by";
$update_array_mst="PRODUCTION_QUANTITY*updated_by";
foreach($data_arr as $mst_id=>$v)
{
	$prod_qty = $v['PRODUCTION_QUANTITY'];
 	$updateID_array_tr[]=$mst_id;
	$update_data_dtls[$mst_id]=explode("*",("'".$prod_qty."'*'0'*'1'*'99999'"));
	$update_data_mst[$mst_id]=explode("*",("'".$prod_qty."'*99999"));
	$all_update_id_arr[mst_id] .= $mst_id.",";
}


// echo bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr);die();
// echo bulk_update_sql_statement2("pro_garments_production_dtls","mst_id",$update_array_dtls,$update_data_dtls,$updateID_array_tr);die();

die;

if($db_type==2)
{	
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","mst_id",$update_array_dtls,$update_data_dtls,$updateID_array_tr));
	$rID2=execute_query(bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_tr));
	echo "$rID ** $rID2";//die();
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
 


?>