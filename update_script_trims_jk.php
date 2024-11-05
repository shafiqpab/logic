<?
include('includes/common.php');
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

$field_array_trans="cons_rate*cons_amount*updated_by*update_date"; 
$field_array_prod="avg_rate_per_unit*stock_value*updated_by*update_date"; 
$field_array_propotion="order_rate*order_amount*updated_by*update_date";

$sql_trim = sql_select("select a.id as prod_id, a.company_id, a.item_category_id, b.id as trans_id, b.transaction_type, b.cons_quantity, b.cons_rate, b.cons_amount 
from product_details_master a, inv_transaction b
where a.id=b.prod_id and a.entry_form=24 and b.item_category=4 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
order by b.prod_id, b.id");
$item_wise_data=array();$item_moving_avg_rate=array();
$i=1;
$upTransID=1;
foreach($sql_trim as $row)
{
	if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 ||  $row[csf("transaction_type")]==5)
	{
		$item_wise_data[$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		$item_wise_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
	}
	if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 ||  $row[csf("transaction_type")]==6)
	{
		
		if($item_wise_data[$row[csf("prod_id")]]["cons_amount"]>0 && $item_wise_data[$row[csf("prod_id")]]["cons_quantity"]>0)
		{
			$cons_rate=number_format(($item_wise_data[$row[csf("prod_id")]]["cons_amount"]/$item_wise_data[$row[csf("prod_id")]]["cons_quantity"]),5,".","");
			$cons_amount=number_format(($item_wise_data[$row[csf("cons_quantity")]]["cons_amount"]*$cons_rate),5,".","");
			if($db_type==0)
			{
				$update_idTrans_arr[]=$row[csf("trans_id")];
				$update_dataTrans_arr[$row[csf("trans_id")]]=explode("*",("'".$cons_rate."'*'".$cons_amount."'*1*'".$pc_date_time."'"));
			}
			else
			{
				if($upTransID)
				{
					$upTransID=execute_query("update inv_transaction set cons_rate='".$cons_rate."', cons_amount='".$cons_amount."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("trans_id")]);
					if($upTransID) $upTransID=1; else $upTransID=0;
				}
				
			}
			
			
		}
		
	}
}

if($db_type==2)
{
	if($upTransID)
	{
		oci_commit($con); 
		echo "Transaction Data Update Successfully. <br>";
	}
	else
	{
		oci_rollback($con);
		echo "Transaction Data Update Failed";
		die;
	}
}

unset($sql_trim);
$upProdID=1;
$prod_sql=sql_select("select id, current_stock from product_details_master where status_active=1 and item_category_id=4 and entry_form=24");
foreach($prod_sql as $row)
{
	if($item_wise_data[$row[csf("id")]]["cons_amount"]>0 && $item_wise_data[$row[csf("id")]]["cons_quantity"]>0)
	{
		$avg_rate=number_format(($item_wise_data[$row[csf("id")]]["cons_amount"]/$item_wise_data[$row[csf("id")]]["cons_quantity"]),5,".","");
		$stock_value=number_format(($row[csf("current_stock")]/$avg_rate),5,".","");
		if($db_type==0)
		{
			$update_idProd_arr[]=$row[csf("id")];
			$update_dataProd_arr[$row[csf("id")]]=explode("*",("'".$avg_rate."'*'".$stock_value."'*1*'".$pc_date_time."'"));
		}
		else
		{
			if($upProdID)
			{
				$upProdID=execute_query("update product_details_master set avg_rate_per_unit='".$avg_rate."', stock_value='".$stock_value."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("id")]);
				if($upProdID) $upProdID=1; else $upProdID=0;
			}
			
		}
		
	}
}

if($db_type==2)
{
	if($upProdID)
	{
		oci_commit($con); 
		echo "Product Data Update Successfully. <br>";
	}
	else
	{
		oci_rollback($con);
		echo "Product Data Update Failed";
		die;
	}
}

unset($prod_sql);

$order_item_wise_sql=sql_select("select prod_id, po_breakdown_id, avg_rate from order_wise_stock where status_active=1 order by prod_id, po_breakdown_id");
$order_item_data=array();
foreach($order_item_wise_sql as $row)
{
	$order_item_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]=$row[csf("avg_rate")];
}
unset($order_item_wise_sql);

$sql_propotionate = sql_select("select a.id as prod_id, b.po_breakdown_id, b.quantity , b.id as propotion_id
from product_details_master a, order_wise_pro_details b
where a.id=b.prod_id and a.entry_form=24 and a.item_category_id=4 and b.order_rate=0 and b.order_amount=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id, b.po_breakdown_id");
$upPropotionID=1;
foreach($sql_propotionate as $row)
{
	if($order_item_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]>0 && $row[csf("quantity")]>0)
	{
		$avg_ord_rate=number_format($order_item_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]],5,".","");
		$stock_ord_value=number_format(($row[csf("quantity")]/$avg_ord_rate),5,".","");
		
		if($db_type==0)
		{
			$update_idPropotion_arr[]=$row[csf("propotion_id")];
			$update_dataPropotion_arr[$row[csf("propotion_id")]]=explode("*",("'".$avg_ord_rate."'*'".$stock_ord_value."'*1*'".$pc_date_time."'"));
		}
		else
		{
			if($upPropotionID)
			{
				$upPropotionID=execute_query("update order_wise_pro_details set order_rate='".$avg_ord_rate."', order_amount='".$stock_ord_value."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("propotion_id")]);
				if($upPropotionID) $upPropotionID=1 ; else $upPropotionID=0 ;
			}
			
		}
	}
}
unset($sql_propotionate);

if($db_type==2)
{
	if($upPropotionID)
	{
		oci_commit($con); 
		echo "Propotionate Data Update Successfully";
	}
	else
	{
		oci_rollback($con);
		echo "Propotionate Data Update Failed";
		die;
	}
}

if($db_type==0)
{
	if(count($update_idTrans_arr)>0)
	{
		$upTransID=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$update_dataTrans_arr,$update_idTrans_arr));
	}
	
	if(count($update_idProd_arr)>0)
	{
		$upProdID=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod,$update_dataProd_arr,$update_idProd_arr));
	}
	
	if(count($update_idPropotion_arr)>0)
	{
		$upPropotionID=execute_query(bulk_update_sql_statement("order_wise_pro_details","id",$field_array_propotion,$update_dataPropotion_arr,$update_idPropotion_arr));
	}
	
	if($upTransID && $upProdID && $upPropotionID)
	{
		mysql_query("COMMIT");
		echo "Data Update Successfully";
	}
	else
	{
		mysql_query("ROLLBACK");
		echo "Data Update Failed";
	}
}
else
{
	/*if(count($update_idTrans_arr)>0)
	{
		$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$field_array_trans,$update_dataTrans_arr,$update_idTrans_arr));
	}
	
	if(count($update_idProd_arr)>0)
	{
		$upProdID=execute_query(bulk_update_sql_statement2("product_details_master","id",$field_array_prod,$update_dataProd_arr,$update_idProd_arr));
	}
	
	if(count($update_idPropotion_arr)>0)
	{
		$upPropotionID=execute_query(bulk_update_sql_statement2("order_wise_pro_details","id",$field_array_propotion,$update_dataPropotion_arr,$update_idPropotion_arr));
	}
	//echo "$upPropotionID";die;
	if($upTransID && $upProdID && $upPropotionID)
	{
		oci_commit($con); 
		echo "Data Update Successfully";
	}
	else
	{
		oci_rollback($con);
		echo "Data Update Failed";
	}*/
}


		




?>