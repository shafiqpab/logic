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


$accep_mis_sql="select import_invoice_id, pi_id, count(pi_id) as tot_row from com_import_invoice_dtls where status_active=1 and is_deleted=0 
group by import_invoice_id, pi_id
having count(pi_id)>1
order by import_invoice_id, pi_id";

$pi_mis_result=sql_select($accep_mis_sql);
$mismass_data=array();
foreach($pi_mis_result as $row)
{
	$mismass_data[$row[csf("import_invoice_id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
	$all_invoice_id[$row[csf("import_invoice_id")]]=$row[csf("import_invoice_id")];
}


$all_invoice_id_chunk=array_chunk($all_invoice_id,999);
$chunk_cond=" and (";
foreach($all_invoice_id_chunk as $inv_id_arr)
{
	$chunk_cond.=" import_invoice_id in(".implode(",",$inv_id_arr).") or";
}
$chunk_cond=chop($chunk_cond,"or");
$chunk_cond.=")";
//$chunk_cond=" and import_invoice_id in(2965)";
$sql_accp="select id, import_invoice_id, pi_id from com_import_invoice_dtls where status_active=1 and is_deleted=0 $chunk_cond order by import_invoice_id, pi_id, id";
//echo $sql_accp;die;
$sql_accp_result=sql_select($sql_accp);
$update_array = "status_active*is_deleted*updated_by*update_date";
foreach($sql_accp_result as $row)
{
	if($dup_check[$row[csf("import_invoice_id")]][$row[csf("pi_id")]]=="")
	{
		$dup_check[$row[csf("import_invoice_id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
	}
	else
	{
		$updateID_array[]=$row[csf("id")];
		$update_data[$row[csf("id")]]=explode("*",("0*1*'1'*'".$pc_date_time."'"));
	}
}
//execute_query
$rID=execute_query(bulk_update_sql_statement2("com_import_invoice_dtls","id",$update_array,$update_data,$updateID_array));
//echo $rID;die;
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