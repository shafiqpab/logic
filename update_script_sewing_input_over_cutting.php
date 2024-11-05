<?
include('includes/common.php');
$con = connect();
function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
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
		$sql_up.=" where production_type=4 and status_active=1 and is_deleted=0 and ";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where  production_type=4 and status_active=1 and is_deleted=0  and $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}

$production_sql="SELECT  SUM(CASE WHEN production_type =1 THEN production_qnty+replace_qty ELSE 0 END) AS cutqc,SUM(CASE WHEN production_type =4 THEN production_qnty ELSE 0 END) AS swinqc,bundle_no from pro_garments_production_dtls where  status_active=1 and is_deleted=0 group by bundle_no having SUM(CASE WHEN production_type =1 THEN production_qnty+replace_qty ELSE 0 END)< SUM(CASE WHEN production_type =4 THEN production_qnty ELSE 0 END) order by bundle_no";

$result=sql_select($production_sql);
//$rID="";
foreach($result as $val)
{
 	$bundle_wise_cutting[$val[csf("bundle_no")]]=$val[csf("cutqc")];
 	//$rID=execute_query("update pro_garments_production_dtls set production_qnty='".$val[csf("cutqc")]."' ,updated_by='1001',update_date='".$pc_date_time."' where status_active=1 and is_deleted=0 and production_type=4 and bundle_no='".$val[csf("bundle_no")]."'");
}
$update_array_tr="production_qnty";
foreach($bundle_wise_cutting as $key=>$val)
{
 	$production_qnty=$val;	
	$updateID_array_tr[]="'$key'";
	$update_data_tr["'$key'"]=explode("*",("'".$production_qnty."'"));
}

if($db_type==2)
{
	//$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr));
echo $rID=bulk_update_sql_statement2("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr);die;
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
	$rID=execute_query(bulk_update_sql_statement("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr));
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

/*
 
//sewing output over then sewing input
include('includes/common.php');
$con = connect();
function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	$field_array=explode("*",$update_column);
	$id_count_arr=array_chunk($id_count,'999');
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
		$sql_up.=" where production_type=5 and ";
		$p=1;
		foreach($id_count_arr as $id_arr)
		{
			if($p==1) $sql_up .=" $id_column in(".implode(',',$id_arr).")"; else $sql_up .=" or $id_column in(".implode(',',$id_arr).")";
			$p++;
		}
	 }
	 else
	 {
		$sql_up.=" where production_type=5 and  $id_column in (".implode(",",$id_count).")";
	 }
	 
	 return $sql_up;     
}

$production_sql="select SUM(CASE WHEN production_type =5 THEN production_qnty ELSE 0 END) AS cutqc,
              SUM(CASE WHEN production_type =4 THEN production_qnty ELSE 0 END) AS swinqc,bundle_no
            from pro_garments_production_dtls
           where  status_active=1 and is_deleted=0  
           group by bundle_no having SUM(CASE WHEN production_type =5 THEN production_qnty ELSE 0 END) > SUM(CASE WHEN production_type =4 THEN production_qnty ELSE 0 END) 
           order by bundle_no";

$result=sql_select($production_sql);
foreach($result as $val)
{
	$bundle_wise_cutting[$val[csf("bundle_no")]]=$val[csf("cutqc")];
}
$update_array_tr="production_qnty*updated_by*update_date";
foreach($bundle_wise_cutting as $key=>$val)
{
 	$production_qnty=$val;	
	$updateID_array_tr[]=$key;
	$update_data_tr[$key]=explode("*",("'".$production_qnty."'*'101'*'".$pc_date_time."'"));
}

if($db_type==2)
{
	$rID=execute_query(bulk_update_sql_statement2("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr));
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
	$rID=execute_query(bulk_update_sql_statement("pro_garments_production_dtls","bundle_no",$update_array_tr,$update_data_tr,$updateID_array_tr));
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

 


?>