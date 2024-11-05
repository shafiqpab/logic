<?
/*
Created by 	: Shafiq
Date 		: 07-12-2021
Comments 	: This script is applicable for duplicate bundle (for urmi)
*/
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

// ========================= get duplicate bunle =============================
$sql="SELECT b.bundle_no,count(CASE WHEN b.production_type = 5 THEN b.bundle_no end) as tot, SUM (CASE WHEN b.production_type = 4 THEN b.production_qnty ELSE 0 END) AS in_qty, SUM (CASE WHEN b.production_type = 5 THEN b.production_qnty ELSE 0 END) AS out_qty FROM PRO_GARMENTS_PRODUCTION_mst a, PRO_GARMENTS_PRODUCTION_DTLS b WHERE a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 AND a.production_date >= '01-Aug-2021'AND a.production_type IN (4, 5)  HAVING SUM (CASE WHEN b.production_type = 4 THEN b.production_qnty ELSE 0 END) < SUM (CASE WHEN b.production_type = 5 THEN b.production_qnty ELSE 0 END) AND SUM (CASE WHEN b.production_type = 4 THEN b.production_qnty ELSE 0 END) > 0 GROUP BY b.bundle_no";//AND A.PO_BREAK_DOWN_ID = 101537 
// echo $sql;die();
$res = sql_select($sql);
$bundle_arr = array();
$bundle_qty_arr = array();
foreach ($res as $val) 
{
	$bundle_arr[$val['BUNDLE_NO']] = $val['BUNDLE_NO'];
	$bundle_qty_arr[$val['BUNDLE_NO']]['in_qty'] = $val['IN_QTY'];
	$bundle_qty_arr[$val['BUNDLE_NO']]['out_qty'] = $val['OUT_QTY'];
}

$bundle_nos = where_con_using_array($bundle_arr,1,"bundle_no");
// echo $bundle_nos;die;
// ========================= get first mst id =============================
$production_sql="SELECT BUNDLE_NO,MAX(mst_id) as MAX_MSTID from pro_garments_production_dtls where production_type =5 and   status_active=1 and is_deleted=0 $bundle_nos
           group by bundle_no";
// echo $production_sql;die();           

$result=sql_select($production_sql);
$mst_id_arr = array();
foreach($result as $val)
{
   	$bundle_wise_mst_id[$val["BUNDLE_NO"]]=$val['MAX_MSTID'];
   	$mst_id_arr[$val['MAX_MSTID']] = $val['MAX_MSTID'];
   		
}

// ========================= get dtls id =============================
$mst_id_cond = where_con_using_array($mst_id_arr,0,"mst_id");
$production_sql="SELECT ID,bundle_no from pro_garments_production_dtls where production_type =5 and status_active=1 and is_deleted=0 $mst_id_cond $bundle_nos";
// echo $production_sql;die();           

$result=sql_select($production_sql);
foreach($result as $val)
{
   	$dtls_id_arr[$val["ID"]]=$val['BUNDLE_NO'];
   		
}
// print_r($dtls_id_arr);die;
// ========================= get mst id wise tot qty =============================
$mst_id_cond = where_con_using_array($mst_id_arr,0,"id");
$production_sql="SELECT ID,PRODUCTION_QUANTITY from pro_garments_production_mst where production_type =5 and status_active=1 and is_deleted=0 $mst_id_cond";
// echo $production_sql;die();           

$result=sql_select($production_sql);
foreach($result as $val)
{
   	$mst_prod_qty[$val["ID"]]=$val['PRODUCTION_QUANTITY'];
   		
}

// print_r($bundle_wise_mst_id);die;
$all_update_id_arr = array();
$update_array_dtls="status_active*is_deleted";
$update_array_mst="production_quantity*updated_by*update_date";
foreach($dtls_id_arr as $dtls_id=>$bundle_no)
{
 	$updateID_array_dtls[]=$dtls_id;
	$update_data_dtls[$dtls_id]=explode("*",("'0'*'1'"));


	$prod_qty = $mst_prod_qty[$bundle_wise_mst_id[$bundle_no]] - $bundle_qty_arr[$bundle_no]['in_qty'];
	$updateID_array_mst[]=$bundle_wise_mst_id[$bundle_no];
	$update_data_mst[$bundle_wise_mst_id[$bundle_no]]=explode("*",($prod_qty."*99999*'".$pc_date_time."'"));
	$all_update_id_arr[$bundle_no] = "mst_id=".$bundle_wise_mst_id[$bundle_no].", Mst Qty was=".$mst_prod_qty[$bundle_wise_mst_id[$bundle_no]].", Dtls Id=".$dtls_id.", Deduct Qty=".$bundle_qty_arr[$bundle_no]['in_qty'];
}

// echo bulk_update_sql_statement2("pro_garments_production_dtls","id",$update_array_dtls,$update_data_dtls,$updateID_array_dtls);die();
// echo bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_mst);die();

die('kakku');
if($db_type==2)
{	
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","id",$update_array_dtls,$update_data_dtls,$updateID_array_dtls));
	$rID2=execute_query(bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_mst));
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
else
{
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","mst_id",$update_array_dtls,$update_data_dtls,$updateID_array_dtls));
	$rID2=execute_query(bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_mst));
	// echo "$rID ** $rID2";die();
	if($rID && $rID2)
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