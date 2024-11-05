<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) {
			mysql_query("BEGIN");
		}
	die;	

$un_load_dyeing_compamy_array=array();
$un_load_main_dyeing_sql="select b.batch_id,b.batch_no,b.load_unload_id,a.company_id from pro_fab_subprocess b, pro_batch_create_mst a where a.id=b.batch_id and b.entry_form=35  and b.load_unload_id=2 and b.production_date between '01-Feb-2018' and '31-Aug-2018'";
$un_load_main_dyeing=sql_select($un_load_main_dyeing_sql);
$m=0;
foreach($un_load_main_dyeing as $row)
{
	$m++;
	$un_load_dyeing_compamy_array[$row[csf("batch_id")]]=$row[csf("company_id")];
}
 
foreach($un_load_dyeing_compamy_array as  $batch_id=>$company_id)
{
$unload_mst=execute_query("UPDATE pro_fab_subprocess set company_id=$company_id where entry_form=35 and load_unload_id=2 and batch_id=$batch_id ");
}
 	if($db_type==0)
		{
			if($unload_mst){
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
			if($unload_mst){
				oci_commit($con);
				echo "<b>Success</b>"; 
			}
			else{
				oci_rollback($con); 
				echo "<b>Failed</b>";
			}
		}
echo "<pre>";
echo "Total Unload Batch No=".$m;//count($un_load_dyeing_compamy_array);
echo "<pre>";
echo "Total Dyeing Unload Batch No=".count($un_load_main_dyeing);
//print_r($un_load_dyeing_compamy_array);
//After run Complete; Plz stop this page- use die top of page;
?>