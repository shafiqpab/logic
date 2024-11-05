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
		 
		 return $sql_up;      
	}
	
	$con = connect();
	
	
	
	/*$sql_transac=sql_select("select b.prod_id, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end) -(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as bal_qnty, sum((case when b.transaction_type in(1,4,5) then b.cons_amount else 0 end) -(case when b.transaction_type in(2,3,6) then b.cons_amount else 0 end)) as bal_amt from inv_transaction b where item_category=1 and b.status_active=1 and b.is_deleted=0 group by b.prod_id order by b.prod_id");
	
	$prod_trans_data=array();
	foreach($sql_transac as $row)
	{
		$prod_trans_data[$row[csf("prod_id")]]["bal_qnty"]=$row[csf("bal_qnty")];
		$prod_trans_data[$row[csf("prod_id")]]["bal_amt"]=$row[csf("bal_amt")];
	}
	
	
	 
	$prod_sql=sql_select("select id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0  order by id");
	
	$update_field="current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty";
	$update_id_arr=array();
	$i=1;
	foreach($prod_sql as $row)
	{
		if($prod_trans_data[$row[csf("id")]]["bal_qnty"]!="")
			{
				$avg_rate=0;
				$stock_qnty=$prod_trans_data[$row[csf("id")]]["bal_qnty"];
				$stock_value=$prod_trans_data[$row[csf("id")]]["bal_amt"];
				if($prod_trans_data[$row[csf("id")]]["bal_qnty"]>0)
				{
					$avg_rate=$stock_value/$stock_qnty;
					$avg_rate=number_format($avg_rate,5,'.','');
					
				}
				$stock_qnty=number_format($stock_qnty,5,'.','');
				$stock_value=number_format($stock_value,5,'.','');
				
				$update_id_arr[]=$row[csf("id")];
				$update_data_arr[$row[csf("id")]]=explode("*",("'".$stock_qnty."'*".$avg_rate."*'".$stock_value."'*0*'".$stock_qnty."'"));
			}
	}
	//echo count($update_id_arr)."<br>";
	$upsubDtlsID="";
	if(count($update_id_arr)>0)
	{
		$upsubDtlsID=bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	}
	
	echo $upsubDtlsID;die;
	
	$rID=execute_query($upsubDtlsID);
	
	
	if($rID)
	{
		echo "Success"; 
	}else 
	{
		echo "Failed";echo $upsubDtlsID;
	}
	die;*/
	
	$prev_prod_id="2459,4708,5118,5119,5121,5165,5522,5635,5645,5852,5892,5930,5963,6017,6110,6204,6317,6407,6776,7079,7092,7199,7368,7659,8818,8993,8994,8996,9135,9185,9528,19468";
	$crrunt_prod_id="26096,18725,26097,26098,26099,26102,26101,26123,26116,26112,26103,26114,26113,26100,19948,26124,26120,26107,26109,26118,26119,26115,26117,26121,26110,26104,26105,26106,18957,26108,26111,26122
";

	$prev_prod_id_arr=explode(",",$prev_prod_id);
	$crrunt_prod_id_arr=explode(",",$crrunt_prod_id);
	$new_prod_arr=array_combine($prev_prod_id_arr,$crrunt_prod_id_arr);
	$sql_transac=sql_select("select b.prod_id, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as bal_qnty,   sum((case when b.transaction_type in(1,4,5) then b.cons_amount else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_amount else 0 end)) as bal_amount  from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.item_category=4 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($crrunt_prod_id) group by b.prod_id");
	$update_field="avg_rate_per_unit*current_stock*stock_value";
	$update_id_arr=array();
	$i=1;
	foreach($sql_transac as $row)
	{
		$avg_rate=number_format(($row[csf("bal_amount")]/$row[csf("bal_qnty")]),6,".","");
		$update_id_arr[]=$row[csf("prod_id")];
		$update_data_arr[$row[csf("prod_id")]]=explode("*",("'".$avg_rate."'*".$row[csf("bal_qnty")]."*'".$row[csf("bal_amount")]."'"));
	}
	if(count($update_id_arr)>0)
	{
		$upsubDtlsID=bulk_update_sql_statement2("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	}
	echo $upsubDtlsID;die;
	
	?>
	