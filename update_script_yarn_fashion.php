<?
include('includes/common.php');
$con = connect();

/*
$file = fopen("fashion.csv","r");
$all_data=array();
while(! feof($file))
{
	$ref_data=fgetcsv($file);
	if($ref_data[0]!="")
	{
		$all_data[$ref_data[0]]=number_format($ref_data[1],2,".","");
		$all_prod_id[$ref_data[0]]=$ref_data[0];
	}
	
}
fclose($file);
*/


$prod_sql=sql_select("select prod_id,rate from fashion_prod_list");
foreach($prod_sql as $row)
{
	$all_data[$row[csf("prod_id")]]=$row[csf("rate")];
	$all_data_ids[$row[csf("prod_id")]]=$row[csf("prod_id")];
}



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


$sql_prod="select id, current_stock from product_details_master where item_category_id=1 and id in (".implode(",",$all_data_ids).")";
//echo $sql_prod;die;
$result_prod=sql_select($sql_prod);
$prod_stock=array();
foreach($result_prod as $row)
{
	//$prod_stock[$row[csf("id")]]=$row[csf("current_stock")];
	
	$prod_value=$row[csf("current_stock")]*$all_data[$row[csf("id")]];
	$updateID_prod_array[]=$row[csf("id")];
	$update_prod_data[$row[csf("id")]]=explode("*",("'".$all_data[$row[csf("id")]]."'*'".$prod_value."'*'1'*'".$pc_date_time."'"));
}

$sql_transac="select id, cons_quantity,prod_id from inv_transaction where item_category=1 and prod_id in (".implode(",",$all_data_ids).")";
//echo $sql_transac;die;
$result_transac=sql_select($sql_transac);
$prod_transac=array();
foreach($result_transac as $row)
{
	$trans_value=$row[csf("cons_quantity")]*$all_data[$row[csf("prod_id")]];
	$updateID_trans_array[]=$row[csf("id")];
	$update_trans_data[$row[csf("id")]]=explode("*",("'".$all_data[$row[csf("prod_id")]]."'*'".$trans_value."'*'1'*'".$pc_date_time."'"));
}
	
$update_array_prod = "avg_rate_per_unit*stock_value*updated_by*update_date";
$update_array_trans = "cons_rate*cons_amount*updated_by*update_date";
//execute_query
$rID =$rID2=true;
if(count($updateID_prod_array)>0)
{
	$rID = execute_query(bulk_update_sql_statement2("product_details_master","id",$update_array_prod,$update_prod_data,$updateID_prod_array));
}
if(count($updateID_trans_array)>0)
{
	$rID2=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array_trans,$update_trans_data,$updateID_trans_array));
}



//echo $rID;die;
if($db_type==0)
{
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
else
{
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

	
die;








?>