<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) {
			mysql_query("BEGIN");
		}
	die;	
$dyeing_sys_no_array=array();
$sub_dyeing_sql="select batch_id,batch_no,system_no,load_unload_id from pro_fab_subprocess where entry_form=38 and system_no>0 and load_unload_id=1 order by batch_no";
foreach(sql_select($sub_dyeing_sql) as $v)
{
	
	$dyeing_sys_no_array[$v[csf("batch_id")]]=$v[csf("system_no")];
}
 
foreach($dyeing_sys_no_array as  $batch_id=>$sys_no)
{
	
$up_mst=execute_query("UPDATE pro_fab_subprocess set system_no=$sys_no where entry_form=38 and load_unload_id=2 and batch_id=$batch_id and ( system_no is null or system_no=0)");
	 
}
 	if($db_type==0)
		{
			if($up_mst){
				mysql_query("COMMIT");  
				echo "<b>Success</b>";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "Failed";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($up_mst){
				oci_commit($con);
				echo "<b>Success</b>"; 
			}
			else{
				oci_rollback($con); 
				echo "<b>Failed</b>";
			}
		}
echo "<pre>";
echo "Total Batch No=".count($dyeing_sys_no_array);
echo "<pre>";
print_r($dyeing_sys_no_array);
//After run Complete; Plz stop this page- use die top of page;
?>