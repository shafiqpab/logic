<?
	include('includes/common.php');
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
		 //echo $sql_up; die();
		 return $sql_up;      
	}
	
	$con = connect();


	$sql_twisting_rcv =sql_select("select b.prod_id 
from inv_receive_master a,inv_transaction b, product_details_master c
where a.id=b.mst_id and b.prod_id=c.id and a.receive_purpose=15 and a.entry_form=1 
and a.item_category=1 and b.item_category=1 and b.transaction_type=1 and c.dyed_type=2");

	$update_field="dyed_type*is_twisted";
	$update_id_arr=array();
	$i=1;
	foreach($sql_twisting_rcv as $row)
	{
		$update_id_arr[]=$row[csf("prod_id")];
		$update_data_arr[$row[csf("prod_id")]]=explode("*",("1*1"));
	}
	if(count($update_id_arr)>0)
	{
		$rID=execute_query(bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data_arr,$update_id_arr));
	}

	if($db_type==0)
	{
		if($rID)
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
	else
	{
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

	
	die;
?>
	