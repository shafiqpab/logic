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

/*
$conversion_factor=return_library_array("select a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.entry_form=24","id","conversion_factor");

$sql_booking  =sql_select("select a.id as book_id, a.company_id, b.trim_group as item_group, c.description, c.brand_supplier, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.rate, c.cons as wo_qnty 
from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c 
where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_type=2 and b.booking_type=2");

$trims_booking=array();
foreach($sql_booking as $row)
{
	$trims_booking[$row[csf("book_id")]][$row[csf("company_id")]][$row[csf("item_group")]][$row[csf("description")]][$row[csf("brand_supplier")]][$row[csf("color_number_id")]][$row[csf("item_color")]][$row[csf("gmts_sizes")]][$row[csf("item_size")]]["rate"]=$row[csf("rate")];
}


$sql_pi  =sql_select("select a.id as pi_id, a.importer_id as company_id, b.item_group as item_group, b.item_description as description, b.brand_supplier, b.color_id as color_number_id, b.item_color as item_color, b.size_id as gmts_sizes, b.item_size as item_size, b.rate, b.quantity
from com_pi_master_details a, com_pi_item_details b 
where a.id=b.pi_id and a.item_category_id=4");

$trims_pi=array();
foreach($sql_pi as $row)
{
	$trims_pi[$row[csf("pi_id")]][$row[csf("company_id")]][$row[csf("item_group")]][$row[csf("description")]][$row[csf("brand_supplier")]][$row[csf("color_number_id")]][$row[csf("item_color")]][$row[csf("gmts_sizes")]][$row[csf("item_size")]]["rate"]=$row[csf("rate")];
}

$transact_date_cond="";
if($db_type==0) $transact_date_cond=" and transaction_date>'2016-12-31'"; else $transact_date_cond="";

$sql_rcv="select a.id as prod_id, a.company_id, a.item_group_id, a.item_description, a.brand_supplier, a.color, a.item_color, a.gmts_size, a.item_size, b.id as trans_id, b.receive_basis, b.pi_wo_batch_no, b.order_qnty, b.cons_quantity, c.exchange_rate  
from  product_details_master a,  inv_transaction b,  inv_receive_master c
where a.id=b.prod_id and b.mst_id=c.id and a.entry_form=24 and c.entry_form=24 and b.transaction_type=1 and b.item_category=4 and a.item_category_id=4 and b.receive_basis in(1,2) and b.pi_wo_batch_no>0 $transact_date_cond";

//echo $sql_rcv;die;

$result_rcv=sql_select($sql_rcv);

//echo count($result_rcv);die;

$update_array="order_rate*order_amount*cons_rate*cons_amount*updated_by*update_date";
foreach($result_rcv as $row)
{
	$order_rate=$order_amt=$cons_rate=$cons_amt=0;
	if($row[csf("receive_basis")]==1)
	{
		$order_rate=$trims_pi[$row[csf("pi_wo_batch_no")]][$row[csf("company_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("brand_supplier")]][$row[csf("color")]][$row[csf("item_color")]][$row[csf("gmts_size")]][$row[csf("item_size")]]["rate"];
	}
	else
	{
		$order_rate=$trims_booking[$row[csf("pi_wo_batch_no")]][$row[csf("company_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("brand_supplier")]][$row[csf("color")]][$row[csf("item_color")]][$row[csf("gmts_size")]][$row[csf("item_size")]]["rate"];
	}
	$order_amt=$row[csf("order_qnty")]*$order_rate;
	$cons_rate=(($order_rate/$conversion_factor[$row[csf("prod_id")]])*$row[csf("exchange_rate")]);
	$cons_amt=$row[csf("cons_quantity")]*$cons_rate;
	
	$updateID_array[]=$row[csf("trans_id")];
	$update_data[$row[csf("trans_id")]]=explode("*",("'".$order_rate."'*'".$order_amt."'*'".$cons_rate."'*'".$cons_amt."'*'1'*'".$pc_date_time."'"));
}

if($db_type==2)
{
	$rID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array,$update_data,$updateID_array));
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
else
{
	//execute_query
	$rID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
	
	//echo $rID.jahid;die;
	
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


die;


$sql_product="select a.current_stock, b.prod_id, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt 
from product_details_master a,  inv_transaction b where a.id=b.prod_id and a.entry_form=24 and b.item_category=4 and b.transaction_type=1 and b.receive_basis in(1,2) and b.pi_wo_batch_no>0 group by a.current_stock, b.prod_id";

$result_product=sql_select($sql_product);

$update_array_prod="avg_rate_per_unit*stock_value*updated_by*update_date";
foreach($result_product as $row)
{
	$prod_rate=$prod_amt=0;
	
	$prod_rate=$row[csf("amt")]/$row[csf("qnty")];
	$prod_amt=$row[csf("current_stock")]*$prod_rate;
	
	$updateID_array_prod[]=$row[csf("prod_id")];
	$update_data_prod[$row[csf("prod_id")]]=explode("*",("'".$prod_rate."'*'".$prod_amt."'*'1'*'".$pc_date_time."'"));
}

if($db_type==2)
{
	$rID=execute_query(bulk_update_sql_statement2("product_details_master","id",$update_array_prod,$update_data_prod,$updateID_array_prod));
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
else
{
	//execute_query
	$rID=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_data_prod,$updateID_array_prod));
	
	//echo $rID.jahid;die;
	
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

die;



$transact_date_cond="";
if($db_type==0) $transact_date_cond=" and b.transaction_date>'2016-12-31'"; else $transact_date_cond="";

$sql_tr="select a.avg_rate_per_unit, b.id as trans_id, b.transaction_type, b.cons_quantity 
from product_details_master a, inv_transaction b where a.id=b.prod_id and a.entry_form=24 and b.item_category=4 and b.transaction_type<>1 and a.avg_rate_per_unit>0 $transact_date_cond";


$result_tr=sql_select($sql_tr);

$update_array_tr="cons_rate*cons_amount*updated_by*update_date";
foreach($result_tr as $row)
{
	$cons_rate=$cons_amt=0;
	
	$cons_rate=$row[csf("avg_rate_per_unit")];
	$cons_amt=$row[csf("cons_quantity")]*$cons_rate;
	
	$updateID_array_tr[]=$row[csf("trans_id")];
	$update_data_tr[$row[csf("trans_id")]]=explode("*",("'".$cons_rate."'*'".$cons_amt."'*'1'*'".$pc_date_time."'"));
}

if($db_type==2)
{
	$rID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_array_tr,$update_data_tr,$updateID_array_tr));
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
else
{
	//execute_query
	$rID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_tr,$update_data_tr,$updateID_array_tr));
	
	//echo $rID.jahid;die;
	
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

*/


//and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name and b.po_breakdown_id =$order_id 

$field_array_ord_prod="id, company_id, category_id, prod_id, po_breakdown_id, stock_quantity, last_rcv_qnty, avg_rate, stock_amount, inserted_by, insert_date"; 

$sql_trim = sql_select("select a.id as prod_id, a.company_id, a.item_category_id, b.po_breakdown_id, 
sum( case when b.entry_form in(24) and b.trans_type in(1) and c.transaction_type in(1) then b.quantity else 0 end) as rcv_qnty, 
sum( case when b.entry_form in(24) and b.trans_type in(1) and c.transaction_type in(1) then (b.quantity*c.order_rate) else 0 end) as rcv_amount, 
sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) and c.transaction_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) and c.transaction_type in(2,3,6) then b.quantity else 0 end)) as balance 
from product_details_master a, order_wise_pro_details b, inv_transaction c
where a.id=b.prod_id and b.trans_id=c.id and b.prod_id=c.prod_id and a.entry_form=24 and c.item_category=4 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0
group by a.id, a.company_id, a.item_category_id, b.po_breakdown_id");
foreach($sql_trim as $row)
{
	$ord_prod_id = return_next_id_by_sequence("ORDER_WISE_STOCK_PK_SEQ", "order_wise_stock", $con);
	
	if($row[csf("rcv_amount")]>0 && $row[csf("rcv_qnty")]>0)
	{
		$rate=number_format(($row[csf("rcv_amount")]/$row[csf("rcv_qnty")]),5,".","");
	}
	else
	{
		$rate=0;
	}
	$order_amount=number_format(($row[csf("balance")]*$rate),5,".","");
	
	$data_array_ord_prod[]= "(".$ord_prod_id.",".$row[csf("company_id")].",4,".$row[csf("prod_id")].",".$row[csf("po_breakdown_id")].",'".$row[csf("balance")]."','".$row[csf("balance")]."','".$rate."','".$order_amount."',1,'".$pc_date_time."')";
}

$rID = sql_insert2("order_wise_stock",$field_array_ord_prod,$data_array_ord_prod,1);

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