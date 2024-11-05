<?
	include('includes/common.php');
	function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
	{
		$field_array=explode("*",$update_column);
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
		 $sql_up.=" where";
		 $id_count_arr=array_chunk(array_unique($id_count),999);
		 $p=1;
		foreach($id_count_arr as $id_count)
		{
			if($p==1) $sql_up .="($id_column in(".implode(',',$id_count).")"; else  $sql_up .=" or $id_column in(".implode(',',$id_count).")";
			
			$p++;
		}
		 $sql_up.=")";
		 return $sql_up;     
	}
	
	$con = connect();
	
	$sql_bal=sql_select("select id from inv_transaction a where a.status_active=1 and a.item_category in (8,9,10,11,15,16,17,18,19,20,21,22) and a.prod_id>0  group by a.prod_id");
	$update_field="avg_rate_per_unit*last_purchased_qnty*current_stock*last_issued_qnty*stock_value";
	foreach($sql_bal as $row)
	{
		$i++;
		$update_id_arr[]=$row[csf("prod_id")];
		$update_data_arr[$row[csf("prod_id")]]=explode("*",("0*0*0*0*0"));
	}
	
	
	
	$upsubDtlsID=bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	//echo $upsubDtlsID;die;
	$rID=execute_query($upsubDtlsID);
	
	
	if($rID) echo "Success"; else {echo "Failed";echo $upsubDtlsID;die;}
	
	//die;
	
	/*$delete_receive="delete a, b from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=23137 and b.transaction_type=1";
	$delete_issue="delete  a, b, c from inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c where a.id=b.mst_id and b.id=c.issue_trans_id and b.prod_id=18058 and b.transaction_type=2";*/
	?>
	