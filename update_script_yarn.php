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
	
	$sql_issue=sql_select("select id from inv_issue_master  where entry_form=3 and item_category=1 and company_id=4 order by id");
	$update_field="issue_number_prefix*issue_number_prefix_num*issue_number";
	$i=1;$update_id_arr=array();
	foreach($sql_issue as $row)
	{
		$comp_prefix="MKL";
		$category="YIS";
		$year="17";
		$com_prefix_num=str_pad($i,5,"0",STR_PAD_LEFT);
		$mrr_prefix=$comp_prefix."-".$category."-".$year."-";
		$new_mrr=$comp_prefix."-".$category."-".$year."-".$com_prefix_num;
		
		$update_id_arr[]=$row[csf("id")];
		$update_data_arr[$row[csf("id")]]=explode("*",("'".$mrr_prefix."'*".$i."*'".$new_mrr."'"));
		$i++;
	}
	//echo count($update_id_arr)."<br>";
	$upsubDtlsID="";
	if(count($update_id_arr)>0)
	{
		$upsubDtlsID=bulk_update_sql_statement2("inv_issue_master","id",$update_field,$update_data_arr,$update_id_arr);
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
	die;
	
	?>
	