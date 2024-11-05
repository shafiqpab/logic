<?
/*
Created by 	: Shafiq
Date 		: 18-04-2022
Comments 	: This script is applicable for replace in-active color size breakdown id by active color size breakdown in gmts production(for knit asia)
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
$sql="SELECT PO_BREAK_DOWN_ID,item_number_id,country_id,color_number_id,size_number_id,status_active,id FROM WO_PO_COLOR_SIZE_BREAKDOWN where PO_BREAK_DOWN_ID=60155 and is_deleted=0 and status_active in(1,3) and to_char(insert_date,'YYYY')=2022";//AND A.PO_BREAK_DOWN_ID = 60155 
// echo $sql;die();
$res = sql_select($sql);
$active_id_arr = array();
$inactive_id_arr = array();
foreach ($res as $val) 
{
	if($val['STATUS_ACTIVE']==1)
	{
		$active_id_arr[$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COUNTRY_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']] = $val['ID'];
	}
	else
	{
		$inactive_id_arr[$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COUNTRY_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']] = $val['ID'];
	}
}
// echo "<pre>";print_r($active_id_arr);die;
$id_arr = array();
$update_array_dtls="color_size_break_down_id";
foreach ($inactive_id_arr as $po_key => $po_value) 
{
	foreach ($po_value as $item_key => $item_value) 
	{
		foreach ($item_value as $coun_key => $coun_value) 
		{
			foreach ($coun_value as $col_key => $col_value) 
			{
				foreach ($col_value as $s_key => $row) 
				{
					// echo $row."sdfd";
					// $id_arr[$po_key][$item_key][$coun_key][$col_key][$s_key][$row] = $active_id_arr[$po_key][$item_key][$coun_key][$col_key][$s_key];

					
					if($row!="" && $active_id_arr[$po_key][$item_key][$coun_key][$col_key][$s_key] !="")
					{
						$updateID_array_dtls[]=$row;
						$update_data_dtls[$row]=explode("*",$active_id_arr[$po_key][$item_key][$coun_key][$col_key][$s_key]);
						$all_update_id_arr[$row]=$active_id_arr[$po_key][$item_key][$coun_key][$col_key][$s_key];
					}
				}
			}
		}
	}
}
// echo "<pre>";print_r($id_arr);die;

// echo bulk_update_sql_statement2("pro_garments_production_dtls","color_size_break_down_id",$update_array_dtls,$update_data_dtls,$updateID_array_dtls);die();
// echo bulk_update_sql_statement2("pro_garments_production_mst","id",$update_array_mst,$update_data_mst,$updateID_array_mst);die();

// die('kakku');
/*if($db_type==2)
{	
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","color_size_break_down_id",$update_array_dtls,$update_data_dtls,$updateID_array_dtls));
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
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","color_size_break_down_id",$update_array_dtls,$update_data_dtls,$updateID_array_dtls));
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
}*/


echo "<pre>";print_r($all_update_id_arr);
 


?>