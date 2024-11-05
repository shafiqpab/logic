<?
include('../includes/common.php');
$con = connect();
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
					//return $strQuery;
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
    return  $strQuery; die;
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

$sql_product=sql_select("select id, company_id, supplier_id, store_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label, minimum_label, maximum_label, item_account, packing_type, avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value,  yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color, gmts_size, gsm, brand, brand_supplier, dia_width, item_size, weight, allocated_qnty, available_qnty, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, entry_form, item_return_qty, origin, model, capacity, otherinfo, brand_name, test_available, dyed_type, is_buyer_supplied 
from product_details_master where status_active=1 and is_deleted=0 and item_category_id in(5,6,7) and company_id=2");
$field_arr="id, company_id, supplier_id, store_id, item_category_id, detarmination_id, sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label, minimum_label, maximum_label, item_account, packing_type, avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value,  yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color, gmts_size, gsm, brand, brand_supplier, dia_width, item_size, weight, allocated_qnty, available_qnty, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, entry_form, item_return_qty, origin, model, capacity, otherinfo, brand_name, test_available, dyed_type, is_buyer_supplied";
if($db_type==0) $data_arr=""; else $data_arr=array();
$i=1;
$year_id=date("Y",time());
$roll_check=array();
foreach($sql_product as $row)
{
	$prodMSTID = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	if($db_type==0)
	{
		if($data_arr!="") $data_arr.=",";
		$data_arr.="('".$prodMSTID."','6','".$row[csf("supplier_id")]."','".$row[csf("store_id")]."','".$row[csf("item_category_id")]."','".$row[csf("detarmination_id")]."','".$row[csf("sub_group_code")]."','".$row[csf("sub_group_name")]."','".$row[csf("item_group_id")]."','".$row[csf("item_description")]."','".$row[csf("product_name_details")]."','".$row[csf("lot")]."','".$row[csf("item_code")]."','".$row[csf("unit_of_measure")]."','".$row[csf("re_order_label")]."','".$row[csf("minimum_label")]."','".$row[csf("maximum_label")]."','".$row[csf("item_account")]."','".$row[csf("packing_type")]."','0','0','0','0','0','".$row[csf("yarn_count_id")]."','".$row[csf("yarn_comp_type1st")]."','".$row[csf("yarn_comp_percent1st")]."','".$row[csf("yarn_comp_type2nd")]."','".$row[csf("yarn_comp_percent2nd")]."','".$row[csf("yarn_type")]."','".$row[csf("color")]."','".$row[csf("item_color")]."','".$row[csf("gmts_size")]."','".$row[csf("gsm")]."','".$row[csf("brand")]."','".$row[csf("brand_supplier")]."','".$row[csf("dia_width")]."','".$row[csf("item_size")]."','".$row[csf("weight")]."','".$row[csf("allocated_qnty")]."','".$row[csf("available_qnty")]."','1','".$pc_date_time."','','','1','0','".$row[csf("entry_form")]."','".$row[csf("item_return_qty")]."','".$row[csf("origin")]."','".$row[csf("model")]."','".$row[csf("capacity")]."','".$row[csf("otherinfo")]."','".$row[csf("brand_name")]."','".$row[csf("test_available")]."','".$row[csf("dyed_type")]."','".$row[csf("is_buyer_supplied")]."')";
	}
	else
	{
		$data_arr[]="('".$prodMSTID."','6','".$row[csf("supplier_id")]."','".$row[csf("store_id")]."','".$row[csf("item_category_id")]."','".$row[csf("detarmination_id")]."','".$row[csf("sub_group_code")]."','".$row[csf("sub_group_name")]."','".$row[csf("item_group_id")]."','".$row[csf("item_description")]."','".$row[csf("product_name_details")]."','".$row[csf("lot")]."','".$row[csf("item_code")]."','".$row[csf("unit_of_measure")]."','".$row[csf("re_order_label")]."','".$row[csf("minimum_label")]."','".$row[csf("maximum_label")]."','".$row[csf("item_account")]."','".$row[csf("packing_type")]."','0','0','0','0','0','".$row[csf("yarn_count_id")]."','".$row[csf("yarn_comp_type1st")]."','".$row[csf("yarn_comp_percent1st")]."','".$row[csf("yarn_comp_type2nd")]."','".$row[csf("yarn_comp_percent2nd")]."','".$row[csf("yarn_type")]."','".$row[csf("color")]."','".$row[csf("item_color")]."','".$row[csf("gmts_size")]."','".$row[csf("gsm")]."','".$row[csf("brand")]."','".$row[csf("brand_supplier")]."','".$row[csf("dia_width")]."','".$row[csf("item_size")]."','".$row[csf("weight")]."','".$row[csf("allocated_qnty")]."','".$row[csf("available_qnty")]."','1','".$pc_date_time."','','','1','0','".$row[csf("entry_form")]."','".$row[csf("item_return_qty")]."','".$row[csf("origin")]."','".$row[csf("model")]."','".$row[csf("capacity")]."','".$row[csf("otherinfo")]."','".$row[csf("brand_name")]."','".$row[csf("test_available")]."','".$row[csf("dyed_type")]."','".$row[csf("is_buyer_supplied")]."')";
	}
}
//die;
//print_r($data_arr);die;
//echo $mrrWiseSeq;die;
if ($db_type == 0)
{
	if ($data_arr == "") 
	{
		echo "No Data Found";die;	
	}
	$mrrWiseSeq= sql_insert("product_details_master", $field_arr, $data_arr, 1);
	if ($mrrWiseSeq) 
	{
		mysql_query("COMMIT");
		echo "Data Save Successfully";
	}
	else
	{
		mysql_query("ROLLBACK");
		echo "Data Does Not Save Successfully";
	}
}
else
{
	if (count($data_arr)<1) 
	{
		echo "No Data Found";die;	
	}
	$mrrWiseSeq= sql_insert2("product_details_master", $field_arr, $data_arr, 1);
	if ($mrrWiseSeq) 
	{
		oci_commit($con);
		echo "Data Save Successfully";
	}
	else
	{
		oci_rollback($con);
		echo "Data Does Not Save Successfully";
	}
}


 
?>