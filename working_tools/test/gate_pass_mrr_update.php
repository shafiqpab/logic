<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();

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

if ($db_type == 0) {
	mysql_query("BEGIN");
}

if($db_type==2)
{
	$year_cond=" and TO_CHAR(insert_date,'YYYY')=2019";
	//$date_cond=" and out_date between '30-Jan-2019' and '30-Jan-2019' ";
}
else
{
	$year_cond=" and YEAR(insert_date)=2019";
	//$date_cond=" and out_date between '2019-01-01' and '2019-01-30' ";
}
$mrr_sys_no_array=array();
$gate_sql="select id, sys_number_prefix_num, sys_number_prefix, sys_number from inv_gate_pass_mst where company_id=2 $year_cond order by id";
//echo $gate_sql;die;
$gate_sql_result=sql_select($gate_sql);
$i=1;
$field_array="sys_number_prefix_num*sys_number_prefix*sys_number";
foreach($gate_sql_result as $row)
{
	$prefix='DGWL-GPE-19-';
	$sys_num=$prefix.str_pad($i,5,'0',STR_PAD_LEFT);
	$update_id_arr[]=$row[csf("id")];
	$update_data_arr[$row[csf("id")]]=explode("*",("'".$i."'*'".$prefix."'*'".$sys_num."'"));
	$i++;
}
if($db_type==2)
{
	$rId=execute_query(bulk_update_sql_statement2("inv_gate_pass_mst","id",$field_array,$update_data_arr,$update_id_arr));
}
else
{
	$rId=execute_query(bulk_update_sql_statement("inv_gate_pass_mst","id",$field_array,$update_data_arr,$update_id_arr));
}
echo $rId;die;
if($db_type==0)
{
	if($rId)
	{
		mysql_query("COMMIT");
		echo "<b>Success</b>";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "Failed";
	}
}
if($db_type==2 || $db_type==1 )
{
	if($rId){
		oci_commit($con);
		echo "<b>Success</b>"; 
	}
	else{
		oci_rollback($con); 
		echo "<b>Failed</b>";
	}
}
?>