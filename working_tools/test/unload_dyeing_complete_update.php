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
/*$un_load_main_dyeing_sql="select b.batch_id,b.batch_no,b.entry_form,b.result from pro_fab_subprocess b  where  b.entry_form not in(38,35) and b.result not in(11)  and b.status_active=1 and 
 b.batch_id not in(select a.batch_id  from pro_fab_subprocess a where  a.entry_form in(38,35)  and a.load_unload_id=2
 and a.process_end_date between '01-Jan-2020' and '27-Sep-2020' and a.status_active=1 ) group by b.batch_id,b.batch_no,b.entry_form,b.result) ";*/
 $un_load_main_dyeing_sql="select a.batch_id  from pro_fab_subprocess a,pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in(38,35)  and a.load_unload_id=2
 and a.process_end_date between '01-Jan-2020' and '27-Sep-2020' and a.result in(1) and a.company_id=6 and b.is_sales=1 and a.status_active=1 and b.status_active=1 ";
$un_load_main_dyeing=sql_select($un_load_main_dyeing_sql);
$m=0;
foreach($un_load_main_dyeing as $row)
{
	$m++;
	$un_load_dyeing_batch_array[$row[csf("batch_id")]]=$row[csf("batch_id")];
}
 
  //echo $dyeing_batch_id.','; and a.entry_form not in(38,35)
  // ".where_con_using_array($poIdArr,0,'b.inv_pur_req_mst_id')." 
 $complete_dyeing_sql="select a.batch_id,a.batch_no,a.entry_form,a.result from pro_fab_subprocess a,pro_batch_create_mst b  where   b.id=a.batch_id  and a.result not in(11)  ".where_con_using_array($un_load_dyeing_batch_array,0,'a.batch_id')."  and b.is_sales=1 and a.status_active=1";
$complete_dyeing_result=sql_select($complete_dyeing_sql);
$k=0;
foreach($complete_dyeing_result as $row)
{
	$k++;
	$complete_not_dyeing_batch_array[$row[csf("batch_id")]]=$row[csf("batch_no")];
}
 $complete_not_batch_id=implode(",",$complete_not_dyeing_batch_array);
foreach($complete_not_dyeing_batch_array as  $batch_id=>$batch_no)
{
$not_complete_batch=execute_query("UPDATE pro_fab_subprocess set result=11 where  batch_id=$batch_id and status_active=1");
}
 	if($db_type==0)
		{
			if($not_complete_batch){
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
			if($not_complete_batch){
				oci_commit($con);
				echo "<b>Success</b>"; 
			}
			else{
				oci_rollback($con); 
				echo "<b>Failed</b>";
			}
		}
echo "<pre>";
echo "Total complete  Batch No=".$complete_not_batch_id;//count($un_load_dyeing_compamy_array);
echo "<pre>";
echo "Total Dyeing Unload Batch No=".count($complete_not_dyeing_batch_array);
//print_r($un_load_dyeing_compamy_array);
//After run Complete; Plz stop this page- use die top of page;
?>