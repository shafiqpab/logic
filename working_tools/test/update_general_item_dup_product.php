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


$dup_sql="select item_group_id, product_name_details, count(id) as tot_row, listagg(cast(id as varchar(4000)), ',') within group (order by id) as ids from product_details_master
where company_id=2 and item_category_id not in(1,2,3,5,6,7,13,14,23) and entry_form <>24 and status_active=1 and is_deleted=0
group by item_group_id, product_name_details
having count(id)>1 and count(id)<3";

$dup_result=sql_select($dup_sql);
$dup_prod_arr=array();
foreach($dup_result as $row)
{
	$prod_id_arr=explode(",",$row[csf("ids")]);
	$dup_prod_arr[$prod_id_arr[1]]=$prod_id_arr[0];
	$dup_prod_id.=$prod_id_arr[1].",";
	$org_prod_id .=$prod_id_arr[0].",";
}
$dup_prod_id=chop($dup_prod_id,",");
$org_prod_id=chop($org_prod_id,",");

//echo $dup_prod_id."<pre>";print_r($dup_prod_arr);die;
/*$trans_sql=sql_select("select id as trans_id, prod_id from inv_transaction where prod_id in($dup_prod_id)");
$upTransID=true;
foreach($trans_sql as $row)
{
	
	if($dup_prod_arr[$row[csf("prod_id")]])
	{
		$upTransID=execute_query("update inv_transaction set prod_id='".$dup_prod_arr[$row[csf("prod_id")]]."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("trans_id")]);
		if($upTransID)
		{
			$upTransID=1;
		}
		else
		{
			$upTransID=0;
			
			echo "update inv_transaction set prod_id='".$dup_prod_arr[$row[csf("prod_id")]]."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("trans_id")];die;
		}
	}
}

$mrr_sql=sql_select("select id as mrr_id, prod_id from inv_mrr_wise_issue_details where prod_id in($dup_prod_id)");
$upMrrId=true;
foreach($mrr_sql as $row)
{
	if($dup_prod_arr[$row[csf("prod_id")]])
	{
		$upMrrId=execute_query("update inv_mrr_wise_issue_details set prod_id='".$dup_prod_arr[$row[csf("prod_id")]]."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("mrr_id")]);
		if($upMrrId)
		{
			$upMrrId=1;
		}
		else
		{
			$upMrrId=0;
			//echo $row[csf("mrr_id")]."test".$row[csf("prod_id")]."check".$dup_prod_arr[$row[csf("prod_id")]];die;
			echo "update inv_mrr_wise_issue_details set prod_id='".$dup_prod_arr[$row[csf("prod_id")]]."', updated_by=1, update_date='".$pc_date_time."' where id=".$row[csf("mrr_id")];die;
		}
	}
}

//echo "check3";die;

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

if($db_type==2)
{
	if($upMrrId)
	{
		oci_commit($con); 
		echo "MRR Data Update Successfully. <br>";
	}
	else
	{
		oci_rollback($con);
		echo "MRR Data Update Failed";
		die;
	}
}

die;*/

$trans_sql=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt
from inv_transaction where status_active=1 and is_deleted=0 and prod_id in($org_prod_id)
group by prod_id
order by prod_id");
$trans_data_arr=array();
foreach($trans_sql as $row)
{
	if($row[csf("bal")]==0) $bal_amt=0; else $bal_amt=$row[csf("bal_amt")];
	$trans_data_arr[$row[csf("prod_id")]]["bal"]=$row[csf("bal")];
	$trans_data_arr[$row[csf("prod_id")]]["bal_amt"]=$bal_amt;
}
//echo "<pre>";print_r($trans_data_arr);
$upProdId=true;
foreach($trans_data_arr as $prod_id=>$val)
{
	if($prod_id)
	{
		if($val["bal"]>0 && $val["bal_amt"]>0) $avg_rate=$val["bal_amt"]/$val["bal"]; else $avg_rate=0;
		$upProdId=execute_query("update product_details_master set avg_rate_per_unit=$avg_rate, current_stock='".$val["bal"]."', stock_value='".$val["bal_amt"]."', updated_by=1, update_date='".$pc_date_time."' where id=$prod_id");
		if($upProdId)
		{
			$upProdId=1;
		}
		else
		{
			$upProdId=0;
			//echo $row[csf("mrr_id")]."test".$row[csf("prod_id")]."check".$dup_prod_arr[$row[csf("prod_id")]];die;
			echo "update product_details_master set avg_rate_per_unit=$avg_rate, current_stock='".$val["bal"]."', stock_value='".$val["bal_amt"]."', updated_by=1, update_date='".$pc_date_time."' where id=$prod_id";die;
		}
	}
}
$upProd_id=execute_query("update product_details_master set status_active=0, is_deleted=1 where id in($dup_prod_id)");
if($db_type==2)
{
	if($upProdId && $upProd_id)
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
die;
?>