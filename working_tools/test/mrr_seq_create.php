<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sql_company=sql_select("select id, company_name from lib_company where status_active=1");
$field_arr="table_name,next_id,company_id,entry_form,year";
$table='INV_RECEIVE_MASTER';
//$table='INV_ISSUE_MASTER';
$next_id='1';
$entry_form='366';
//$entry_form='367';
//$entry_form='368';
//$entry_form='369';
$year='2020';
 
//PLATFORM_SEQUENCE_MRR  for mysql
//PLATFORM_SEQUENCE_PK  for oracle
foreach($sql_company as $val)
{
	$data_array .="('".$table."',".$next_id.",".$val[csf('id')].",".$entry_form.",".$year.")";
}
$mrr_seq_insert = sql_insert("PLATFORM_SEQUENCE_PK",$field_arr,$data_array,1);
if($mrr_seq_insert!=1)
{
	echo "10**INSERT INTO PLATFORM_SEQUENCE_PK (".$field_arr.") VALUES ".$data_array; die;
}

if($db_type==0)
{
	if($mrr_seq_insert)
	{
		mysql_query("COMMIT");  
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "Success";
	}
}
if($db_type==2 || $db_type==1 )
{
	if($mrr_seq_insert)
	{
		oci_commit($con);  
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Success";
	}
}
?>