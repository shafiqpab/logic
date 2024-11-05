<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) {
			mysql_query("BEGIN");
		}
	die;	
$load_dyeing_compamy_array=array();
$load_main_dyeing_sql="select b.batch_id,b.batch_no,b.load_unload_id,a.company_id from pro_fab_subprocess b, pro_batch_create_mst a where a.id=b.batch_id and b.entry_form=35  and b.load_unload_id=1 and b.process_end_date between '01-Feb-2018' and '31-Aug-2018' ";
$load_main_dyeing=sql_select($load_main_dyeing_sql);
foreach($load_main_dyeing as $row)
{
	
	$load_dyeing_compamy_array[$row[csf("batch_id")]]=$row[csf("company_id")];
}
 $m=0;
foreach($load_dyeing_compamy_array as  $batch_id=>$company_id)
{
	$m++;
$up_mst=execute_query("UPDATE pro_fab_subprocess set company_id=$company_id where entry_form=35 and load_unload_id=1 and batch_id=$batch_id ");
 
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
echo "Total Load Batch No=".count($load_main_dyeing);
 echo "<pre>";
echo "Total Load Dyeing Batch No=".$m;
echo "<pre>";
//print_r($load_dyeing_compamy_array);
//After run Complete; Plz stop this page- use die top of page;
?>