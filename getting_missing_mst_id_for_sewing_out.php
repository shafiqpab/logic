<?
/*
Created by 	: Shafiq
Date 		: 30-10-2021
Comments 	: Getting missing mst table id in details table
*/

// http://192.168.100.4/fakirfashion_erp/getting_missing_mst_id_for_sewing_out.php

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

// ========================= get id ============================= (JMF-20-2273-143)
$sql="SELECT id,po_break_down_id from pro_garments_production_mst where production_type =5 and status_active=1 and is_deleted=0 and production_date between '01-Sep-2021' and '05-Sep-2021'";
// echo $sql;die();           

$result=sql_select($sql);
$id_arr = array();
$po_id_arr = array();
foreach($result as $val)
{
	$id_arr[$val['ID']]=$val['ID'];
	$po_id_arr[$val['ID']]=$val['PO_BREAK_DOWN_ID'];
}
$id_cond = where_con_using_array($id_arr,0,"mst_id");

// ========================= get mst id ============================= (JMF-20-2273-143)
$sql="SELECT mst_id from pro_garments_production_dtls where production_type =5 and  status_active=1 and is_deleted=0 $id_cond";
// echo $sql;die();           

$result=sql_select($sql);
$mst_id_arr = array();
foreach($result as $val)
{
	$mst_id_arr[$val['MST_ID']]=$val['MST_ID'];
}
$po_id_array = array();
foreach ($id_arr as $key => $val) 
{
	if($mst_id_arr[$key]=="")
	{
		$ids .= ($ids=="") ? $key : ",".$key;
		$po_id_array[$po_id_arr[$key]] = $po_id_arr[$key];
	}
}
$po_cond = where_con_using_array($po_id_array,0,"id");

$sql = "SELECT po_number from wo_po_break_down where status_active=1 $po_cond";
$res = sql_select($sql);
foreach ($res as $val) 
{
	$po_number .= ($po_number=="") ? $val['PO_NUMBER'] : "<br>".$val['PO_NUMBER'];
}


echo $ids."<br>";
echo $po_number."<br>";

 


?>