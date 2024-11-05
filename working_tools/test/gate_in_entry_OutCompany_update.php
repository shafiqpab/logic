<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$gate_pass_sql=sql_select("SELECT b.sys_number,b.company_id FROM inv_gate_pass_mst b WHERE b.within_group=1");

if(empty($gate_pass_sql))
{
	echo "Data Not Found";
	die;
}
if(!empty($gate_pass_sql)){
//$gate_pass_no="";
foreach($gate_pass_sql as $row )
	{

		$gate_pass_no="'".$row[csf("sys_number")]."'";
		$company_id=$row[csf("company_id")];
		

		execute_query("update  inv_gate_in_mst  set sending_company=$company_id  where gate_pass_no=$gate_pass_no and  within_group =1");



	}
	//echo $gate_pass_no;
}
	
oci_commit($con);
//mysql_query("COMMIT");
echo "Success";
die;


?>