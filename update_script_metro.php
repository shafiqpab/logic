<?
include('includes/common.php');
$con = connect();


//echo $con;die;



function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	
	if( $contain_lob==0)
	{
		$count=count($arrValues);
		 //return $count."ss"; 
		if( $count >1 ) // Multirow
		{
			$k=1;	
			foreach( $arrValues as $rows)
			{
				
				if($k==1)
				{
					$strQuery= "INSERT ALL \n";
				}
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
				if( $count==$k )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					 //return "=".$strQuery; 
					$stid =  oci_parse($con, $strQuery);
					//oci_execute("Character set is AL32UTF8");
					$exestd=oci_execute($stid, OCI_NO_AUTO_COMMIT);
					 if(!$exestd) return 0; //else return $exestd;
					$strQuery="";
					$k=0;
				}
				else if ( $k==50 )
				{
					$count=$count-$k;
					$strQuery .= "SELECT * FROM dual";
					//  return $strQuery;
					$stid =  oci_parse($con, $strQuery);
					$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
					if(!$exestd) return 0;
					$strQuery="";
					$k=0;
				}
				$k++;
			}
			return 1;
			 
			//return $strQuery; 
		}
		else // Single Row
		{
			$strQuery= "INSERT  \n";
			foreach( $arrValues as $rows)
			{
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
			}
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			//return $strQuery; 
			 return 1;
		}
	}
	else
	{
		$tmpv=explode(")",$arrValues);
		
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
 
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0"; 
		}
		return "1";

	}
    //return  $strQuery; die;
	//$strQuery .= "SELECT * FROM dual";
	//echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
	
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


	/*$id = return_next_id("id","product_details_master",1);

    $field_array_cost="id,company_id, supplier_id, store_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label, minimum_label, maximum_label, item_account, packing_type, avg_rate_per_unit, 
    last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color, gmts_size, gsm, brand, brand_supplier, dia_width, item_size, weight, allocated_qnty, available_qnty, inserted_by, 
    insert_date, updated_by, update_date, status_active, is_deleted, entry_form, item_return_qty"; 
    
    $sql = "Select * FROM product_details_master WHERE company_id = 1 AND ITEM_CATEGORY_ID in (8,9,10,11,15,16,17,18,19,20,21,22,32)";
    
    $nameArray=sql_select( $sql );
    
    $data_array=array(); 
	$i=1;  
    foreach ($nameArray as $row)
    {       
        $data_array[]="(".$id.",2,'".$row[csf('supplier_id')]."','".$row[csf('store_id')]."','".$row[csf('item_category_id')]."',
        '".$row[csf('detarmination_id')]."','".$row[csf('sub_group_code')]."','".$row[csf('sub_group_name')]."',
        '".$row[csf('item_group_id')]."','".$row[csf('item_description')]."','".$row[csf('product_name_details')]."',
        '".$row[csf('lot')]."','".$row[csf('item_code')]."','".$row[csf('unit_of_measure')]."','".$row[csf('re_order_label')]."',
        '".$row[csf('minimum_label')]."','".$row[csf('maximum_label')]."','".$row[csf('item_account')]."',
        '".$row[csf('packing_type')]."','".$row[csf('avg_rate_per_unit')]."','".$row[csf('last_purchased_qnty')]."',
        '".$row[csf('current_stock')]."','".$row[csf('last_issued_qnty')]."','".$row[csf('stock_value')]."',
        '".$row[csf('yarn_count_id')]."','".$row[csf('yarn_comp_type1st')]."','".$row[csf('yarn_comp_percent1st')]."',
        '".$row[csf('yarn_comp_type2nd')]."','".$row[csf('yarn_comp_percent2nd')]."','".$row[csf('yarn_type')]."',
        '".$row[csf('color')]."','".$row[csf('item_color')]."','".$row[csf('gmts_size')]."','".$row[csf('gsm')]."',
        '".$row[csf('brand')]."','".$row[csf('brand_supplier')]."','".$row[csf('dia_width')]."','".$row[csf('item_size')]."',
        '".$row[csf('weight')]."', '".$row[csf('allocated_qnty')]."', '".$row[csf('available_qnty')]."', 
        '".$row[csf('inserted_by')]."','".$row[csf('insert_date')]."','".$row[csf('updated_by')]."','".$row[csf('update_date')]."',
        '".$row[csf('status_active')]."','".$row[csf('is_deleted')]."','".$row[csf('entry_form')]."','".$row[csf('item_return_qty')]."')"; 
        $id++;
		$i++;
		//if($i==5) break; 
    }
	
	//echo "insert into product_details_master ($field_array_cost) values " .$data_array[0];die;
	//echo "<pre>";print_r($data_array);die;

    //$rID = sql_insert2("product_details_master",$field_array_cost,$data_array,1);
	
	//echo $rID;die;
	
	
	
	$sql_receive=sql_select("select  prod_id, store_id
	from inv_transaction 
	where status_active=1 and prod_id in(12528,12450,12530,12336,12673,11786,11172,11339,11563,11624,11625,12277,12320,11313,11315,11340,10972,10995,11150,11243,11436,11292,
	12273,12274,11027,10964,11176,11181,11232,11158,11126,11278,11282,11332,11056,11062,11007,11008,11079,11105,11106,10994,10980,11154) and transaction_type in(1,4,5)
	group by store_id, prod_id having count(store_id)<2");
	$receive_data_arr=array();
	foreach($sql_receive as $row)
	{
		$receive_data_arr[$row[csf("prod_id")]]=$row[csf("store_id")];
	}
	
	
	$sql_issue=sql_select("select id, prod_id, store_id
	from inv_transaction 
	where status_active=1 and prod_id in(12528,12450,12530,12336,12673,11786,11172,11339,11563,11624,11625,12277,12320,11313,11315,11340,10972,10995,11150,11243,11436,11292,
	12273,12274,11027,10964,11176,11181,11232,11158,11126,11278,11282,11332,11056,11062,11007,11008,11079,11105,11106,10994,10980,11154) and transaction_type in(2,3,6)");
	
	$update_field_transaction="store_id*status_active";
	$update_field_transfer="from_store*status_active";
	
	foreach($sql_issue as $row)
	{
		if($receive_data_arr[$row[csf("prod_id")]]!="")
		{
			if($receive_data_arr[$row[csf("prod_id")]]!=$row[csf("store_id")])
			{
				$updateID_array[]=$row[csf("id")];
				$update_data_transaction[$row[csf("id")]]=explode("*",("".$receive_data_arr[$row[csf("prod_id")]]."*1"));
			}
		}
	}
	
	$sql_transfer=sql_select("select id, from_prod_id, from_store
	from  inv_item_transfer_dtls 
	where status_active=1 and from_prod_id in(12528,12450,12530,12336,12673,11786,11172,11339,11563,11624,11625,12277,12320,11313,11315,11340,10972,10995,11150,11243,11436,11292,
	12273,12274,11027,10964,11176,11181,11232,11158,11126,11278,11282,11332,11056,11062,11007,11008,11079,11105,11106,10994,10980,11154)");
	
	foreach($sql_transfer as $row)
	{
		if($receive_data_arr[$row[csf("from_prod_id")]]!="")
		{
			if($receive_data_arr[$row[csf("from_prod_id")]]!=$row[csf("from_store")])
			{
				$updateID_transfer_array[]=$row[csf("id")];
				$update_data_transfer[$row[csf("id")]]=explode("*",("".$receive_data_arr[$row[csf("from_prod_id")]]."*1"));
			}
		}
	}
	*/
	
	$sql=sql_select("select a.id as trans_id, a.mst_id, a.store_id, a.company_id as trans_company, b.company_id as stroe_company
from inv_transaction a,  lib_store_location b where a.store_id=b.id and a.item_category=1 and a.status_active=1 and a.company_id<>b.company_id order by a.id");
	$update_field="store_id*status_active";
	foreach($sql as $row)
	{
		if($row[csf("store_id")]==2)
		{
			$store_id=11;
		}
		else if($row[csf("store_id")]==11)
		{
			$store_id=2;
		}
		else if($row[csf("store_id")]==7)
		{
			$store_id=10;
		}
		else if($row[csf("store_id")]==10)
		{
			$store_id=7;
		}
		else if($row[csf("store_id")]==8)
		{
			$store_id=9;
		}
		else if($row[csf("store_id")]==9)
		{
			$store_id=8;
		}
		
		$updateID_array[]=$row[csf("trans_id")];
		$update_data[$row[csf("trans_id")]]=explode("*",("".$store_id."*1"));
	}
	
	$rID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	
	//echo $rID;die;
	
	//$rID2=execute_query(bulk_update_sql_statement2("inv_item_transfer_dtls","id",$update_field_transfer,$update_data_transfer,$updateID_transfer_array),1);
	
	//$rID=bulk_update_sql_statement2("inv_transaction","id",$update_field_transaction,$update_data_transaction,$updateID_array);
	//$rID2=bulk_update_sql_statement2("inv_item_transfer_dtls","id",$update_field_transfer,$update_data_transfer,$updateID_transfer_array);
	//echo $rID2;die;
	
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