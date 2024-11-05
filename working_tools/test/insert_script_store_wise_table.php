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
//,6,7,23 and b.prod_id <> 15883
$sql_dyes_trans="select company_id, 0 as location_id, store_id, item_category, prod_id,
sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt, 
1 as inserted_by, sysdate
from inv_transaction where item_category in(5,6,7,23) and status_active=1
group by company_id,  store_id, item_category, prod_id
order by prod_id";
$result=sql_select($sql_dyes_trans);
//echo count($result);die;
$i=1;$k=1;
//$field_array="id, company_id, location_id, store_id, category_id, prod_id, cons_qty, rate, amount, inserted_by, insert_date";
$upTransID=true;
foreach($result as $row)
{
	$prod_rate=0;
	if($row[csf("bal_amt")] > 0 && $row[csf("bal_qnty")] > 0)
	{
		$prod_rate=number_format(($row[csf("bal_amt")]/$row[csf("bal_qnty")]),6,'.','');
	}
	
	$upTransID=execute_query("insert into inv_store_wise_qty_dtls (id, company_id, location_id, store_id, category_id, prod_id, cons_qty, rate, amount, inserted_by, insert_date) values (".$i.",'".$row[csf("company_id")]."','".$row[csf("location_id")]."','".$row[csf("store_id")]."','".$row[csf("item_category")]."','".$row[csf("prod_id")]."','".$row[csf("bal_qnty")]."','".$prod_rate."','".$row[csf("bal_amt")]."','".$row[csf("inserted_by")]."','".$row[csf("sysdate")]."' ) ");
	if($upTransID){ $upTransID=1; } else {echo "insert into inv_store_wise_qty_dtls (id, company_id, location_id, store_id, category_id, prod_id, cons_qty, rate, amount, inserted_by, insert_date) values (".$i.",'".$row[csf("company_id")]."','".$row[csf("location_id")]."','".$row[csf("store_id")]."','".$row[csf("item_category")]."','".$row[csf("prod_id")]."','".$row[csf("bal_qnty")]."','".$prod_rate."','".$row[csf("bal_amt")]."','".$row[csf("inserted_by")]."','".$row[csf("sysdate")]."' ) ";oci_rollback($con);die;}
	$i++;
}
//echo "<pre>";print_r($rcv_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
	{
		oci_commit($con); 
		echo "Store Wise Data Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Store Wise Data Insert Failed";
		die;
	}
}
?>