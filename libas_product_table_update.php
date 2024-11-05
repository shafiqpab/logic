<?
include('includes/common.php');
$con = connect();

//echo $con;die;

function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
	
	//echo "<pre>";print_r($id_count_arr);die;
	
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	
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


	$sql=sql_select("select id, current_stock from product_details_master 
    where id in(6921)"); //and a.issue_trans_id in(1802,3143,15633)


	$update_field="available_qnty*allocated_qnty";
	foreach($sql as $row)
	{		
		$updateID_array[]=$row[csf("id")];
		$update_data[$row[csf("id")]]=explode("*",("'".number_format($row[csf("current_stock")],2)."'*'0'"));
	}

	//echo bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data,$updateID_array); die();
	
	$rID=execute_query(bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data,$updateID_array));

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
?>