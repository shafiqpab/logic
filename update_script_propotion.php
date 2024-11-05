<?
include('includes/common.php');
$con = connect();

/*$mrr_tbl_data=array(); $mst_ids='';
$sql="SELECT receive_trans_id as RCV_ID, sum(issue_qnty) as ISSUE_QNTY  FROM  inv_mrr_wise_issue_details WHERE status_active=1 and is_deleted=0 group by receive_trans_id";
$result=sql_select($sql);
foreach($result as $row)
{
	$mrr_tbl_data[$row['RCV_ID']]=$row['ISSUE_QNTY'];
}
//echo "<pre>";print_r($mrr_tbl_data);die;

$sql_transac="SELECT id as RCV_ID, cons_quantity as RCV_QNTY  FROM  inv_transaction WHERE status_active=1 and is_deleted=0 and item_category=1 and transaction_type in(1,4,5)";
$sql_transac_result=sql_select($sql_transac);
$trans_deviance=array();
foreach($sql_transac_result as $row)
{
	if(round($mrr_tbl_data[$row['RCV_ID']])<round($row['RCV_QNTY']))
	{
		$trans_deviance[$row['RCV_ID']]=$row['RCV_ID']."**".$mrr_tbl_data[$row['RCV_ID']]."**".$row['RCV_QNTY']."##".round($mrr_tbl_data[$row['RCV_ID']])."##".round($row['RCV_QNTY']);
	}
}

echo count($trans_deviance);echo "<pre>";print_r($trans_deviance);die;*/


/*

"select prod_id, listagg(cast(company_id as varchar(4000)),',') within group (order by company_id) as company_id 
from inv_transaction where status_active=1 and is_deleted=0 and item_category=1 group by prod_id
having count(distinct(company_id))>1";

$sql = sql_select("delete  from  inv_mrr_wise_issue_details  
where issue_trans_id in(select  B.ISSUE_TRANS_ID from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.issue_trans_id and a.item_category=1)");*/ 

// prod_id = 87982 by check

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

$batch_sql=sql_select("select mst_id, po_id  from  pro_batch_create_dtls where status_active=1 and is_deleted=0");
$batch_data=array();
foreach($batch_sql as $row)
{
	if($row[csf("po_id")]!="" && $row[csf("po_id")]!=0)
	{
		$batch_data[$row[csf("mst_id")]]=$row[csf("po_id")];
	}
}

//echo $batch_data[9389];die;

$propotionate_sql="select trans_id, quantity  from order_wise_pro_details where entry_form=18 and trans_type=2 and status_active=1 and is_deleted=0";

$propotionate_sql_result=sql_select($propotionate_sql);
$propotionate_data=array();
foreach($propotionate_sql_result as $row)
{
	$propotionate_data[$row[csf("trans_id")]]+=$row[csf("quantity")];
}

$sql_issue="select b.prod_id, b.id as trans_id, b.pi_wo_batch_no, b.cons_quantity, a.id as dtls_id  from inv_transaction b, inv_finish_fabric_issue_dtls a where b.id=a.trans_id and b.item_category=2 and b.transaction_type in(2) and b.status_active=1 and b.is_deleted=0";
$result_issue=sql_select($sql_issue);


/*
$sql_receive="select b.prod_id, b.id as trans_id, b.cons_quantity, b.cons_rate,b.transaction_type  from inv_transaction b where b.item_category=1 and b.transaction_type in(1,4,5) and b.status_active=1 and b.is_deleted=0 and company_id=1 order by b.id";
$result_receive=sql_select($sql_receive);
foreach($result_receive as $row)
{
	$rcv_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["cons_quantity"]=$row[csf('cons_quantity')];
	$rcv_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["cons_rate"]=$row[csf('cons_rate')];
	$rcv_data_arr[$row[csf('prod_id')]][$row[csf('trans_id')]]["transaction_type"]=$row[csf('transaction_type')];
}*/


//echo count($issue_data_arr)."<br>";
//echo count($issue_data_arr)."<pre>";print_r($issue_data_arr);die;
//echo "<pre>";print_r($rcv_data_arr);die;


$prod_color=sql_select("select id, color from product_details_master where item_category_id=2");
$prod_color_data=array();
foreach($prod_color as $row)
{
	$prod_color_data[$row[csf("id")]]=$row[csf("color")];
}

		

$proptionate_id = return_next_id("id", "order_wise_pro_details", 1);
$field_array_prop= "id,trans_id,trans_type,entry_form,po_breakdown_id,dtls_id,prod_id,color_id,quantity,inserted_by,insert_date";


$i=1;$k=1;
$receive_balance_check=array();
$data_array_prop="";
foreach($result_issue as $row)
{
	//if($propotionate_data[$row[csf("trans_id")]]=="" && $batch_data[$row[csf("pi_wo_batch_no")]]!="" && ($batch_data[$row[csf("pi_wo_batch_no")]]='1529' || $batch_data[$row[csf("pi_wo_batch_no")]]=='1530') && $row[csf("prod_id")]=="9364")
	//$prod_color_data[$row[csf("id")]]
	if($propotionate_data[$row[csf("trans_id")]]=="" && $batch_data[$row[csf("pi_wo_batch_no")]]!="")
	{
		if($data_array_prop!="") $data_array_prop.=",";
		$data_array_prop.="(".$proptionate_id.",".$row[csf("trans_id")].",2,18,".$batch_data[$row[csf("pi_wo_batch_no")]].",".$row[csf("dtls_id")].",".$row[csf("prod_id")].",'".$prod_color_data[$row[csf("prod_id")]]."',".$row[csf("cons_quantity")].",'1','".$pc_date_time."')";
		//$data_array_prop.="(".$proptionate_id.",".$row[csf("trans_id")].",2,18,".$row[csf("pi_wo_batch_no")].",".$row[csf("prod_id")].",".$row[csf("cons_quantity")].",'1','".$pc_date_time."')";
		$proptionate_id=$proptionate_id+1;
		//$test_data.=$batch_data[$row[csf("pi_wo_batch_no")]].",";
	}
}

//echo $data_array_prop;die;
//echo count($trans_id_all);die;

$rID = sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,1);
//echo $rID;die;
//echo $rID."<br>".$rID2;die;
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




/*$sql="select b.prod_id from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by prod_id order by prod_id";
$result=sql_select($sql);
foreach($result as $row)
{
	if(in_array($row[csf('prod_id')],$only_trims_arr))
	{
		$both_arr[$id]=$row[csf('prod_id')];
		$id++;
	}
	else
	{
		$only_gi_arr[]=$row[csf('prod_id')];
	}
}*/

//echo implode(",",$both_arr)."<br>";
//echo count($both_arr)."<br>";
//var_dump($both_arr);
//var_dump($only_gi_arr);
/*if(count($both_arr)>0)
{
	$id_string=""; $previds=''; $newids='';
	foreach($both_arr as $newId=>$previd)
	{
		$id_string.=" WHEN $previd THEN $newId";
		$previds.=$previd.",";
		$newids.=$newId.",";
	}
	
	$id_string_prod="CASE id ".$id_string." END";
	$id_string_trans="CASE b.prod_id ".$id_string." END";
	$id_string_nonOrder="CASE item_id ".$id_string." END";
	$id_string_mrr="CASE prod_id ".$id_string." END";
	
	$sql_insert="insert into product_details_master(id, company_id,supplier_id,store_id, item_category_id,entry_form, detarmination_id,sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
					$id_string_prod, company_id,supplier_id,store_id, item_category_id,20,detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code,unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from product_details_master where id in (".substr($previds,0,-1).")";
	
	$trans_sql="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_receive_master a where a.id=b.mst_id and a.entry_form=20 
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type=1 and b.item_category=4)";

	$trans_sql2="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_issue_master a where a.id=b.mst_id and a.entry_form in (21,26)
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type in (2,3) and b.item_category=4)";

	$purc_sql="update inv_purchase_requisition_dtls set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";
	$nonOrder_sql="update wo_non_order_info_dtls set item_id=".$id_string_nonOrder." where item_id in(".substr($previds,0,-1).")";
	$mrr_sql="update inv_mrr_wise_issue_details set prod_id=".$id_string_mrr." where prod_id in(".substr($previds,0,-1).") and entry_form=21";
	//$serial_sql="update inv_serial_no_details set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";$serial=execute_query($serial_sql,0);

	$both_ids=$previds.substr($newids,0,-1);
	
	$sql_trans=sql_select("SELECT prod_id, (sum(case when transaction_type in(1,4) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3) then cons_quantity else 0 end)) as current_stock from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".$both_ids.") and item_category=4 group by prod_id");
	$update_field="current_stock";
	foreach($sql_trans as $row)
	{
		$update_id_arr[]=$row[csf('prod_id')];
		$update_data_arr[$row[csf('prod_id')]]=explode("*",($row[csf('current_stock')]));
	}
	
	
	
	$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	$rID=sql_multirow_update("product_details_master","entry_form","24","id",implode(",",$only_trims_arr),0);
	$rID2=sql_multirow_update("product_details_master","entry_form","20","id",implode(",",$only_gi_arr),0);
	$rID3=execute_query($sql_insert,0);
	
	$inv_trans=execute_query($trans_sql,0);
	$inv_trans2=execute_query($trans_sql2,0);
	$inv_purc=execute_query($purc_sql,0);
	$non_order=execute_query($nonOrder_sql,0);
	$mrr=execute_query($mrr_sql,0);
	$rID4=execute_query($upsubDtlsID);
	
	if($rID && $rID2 && $rID3 && $inv_trans && $inv_trans2 && $inv_purc && $non_order && $mrr && $rID4)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}*/


?>