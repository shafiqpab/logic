<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');

$con=connect();
if ($db_type == 0) {
			mysql_query("BEGIN");
		}
	 	die;
	$unload_dyeing_mc_array=array();
	$unload_main_dyeing_sql="select b.batch_id,b.machine_id,b.batch_no,b.load_unload_id,a.company_id from pro_fab_subprocess b, pro_batch_create_mst a where a.id=b.batch_id and b.entry_form=35  and b.load_unload_id=2 and a.status_active=1 and b.status_active=1  and b.process_end_date between '01-Jan-2018' and '31-Dec-2018'";
//and b.process_end_date between '01-Feb-2018' and '31-Aug-2018'
$unload_main_dyeing=sql_select($unload_main_dyeing_sql);
foreach($unload_main_dyeing as $row)
{
	
	$unload_dyeing_mc_array[$row[csf("batch_id")]]=$row[csf("machine_id")];
	$unload_batch_array[$row[csf("batch_id")]]=$row[csf("machine_id")];
}
$batch_id_cond_in=where_con_using_array($unload_batch_array,0,'b.batch_id');

$load_dyeing_array=array();
 $load_main_dyeing_sql="select b.batch_id,b.machine_id,b.batch_no,b.load_unload_id,a.company_id from pro_fab_subprocess b, pro_batch_create_mst a where a.id=b.batch_id and b.entry_form=35  and b.load_unload_id=1 and a.status_active=1 and b.status_active=1  and b.process_end_date between '01-Jan-2018' and '31-Dec-2018' order by b.batch_id desc"; 
//and b.process_end_date between '01-Feb-2018' and '31-Aug-2018'
$load_main_dyeing=sql_select($load_main_dyeing_sql);
foreach($load_main_dyeing as $row)
{
	
	//$load_machine_id=$row[csf("machine_id")];
	//$unload_machine_id=$unload_dyeing_mc_array[$row[csf("machine_id")]];
	$load_dyeing_mc_array[$row[csf("batch_id")]]=$row[csf("machine_id")];
	$load_dyeing_batch_array[$row[csf("batch_id")]]=$row[csf("batch_id")];
	if($load_machine_id!=$unload_machine_id)
	{
	//	$load_dyeing_array[$row[csf("batch_id")]]=$row[csf("batch_id")];
	}
	
	
}

 $m=0;
foreach($unload_dyeing_mc_array as  $batch_id=>$mc_id)
{
	//$load_machine_id=$row[csf("machine_id")];
	//$unload_machine_id=$mc_id;
	$load_machine_id=$load_dyeing_mc_array[$batch_id];
	$load_batch_id=$load_dyeing_batch_array[$batch_id];
	if($load_batch_id==$batch_id)
	{
		if($mc_id!=$load_machine_id)
		{
			$load_bat_dyeing_array[$batch_id]=$batch_id;
			$up_mst=execute_query("UPDATE pro_fab_subprocess set machine_id=$mc_id where entry_form=35 and load_unload_id=1 and batch_id=$batch_id ");
		$m++;
		}
	}
	
	

 
}
//echo implode(", ",$load_bat_dyeing_array);
die;

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
echo "Total Load Batch No=".count($load_bat_dyeing_array);
 echo "<pre>";
echo "Total Load Dyeing Batch No=".$m;
echo "<pre>";
//print_r($load_dyeing_compamy_array);
//After run Complete; Plz stop this page- use die top of page;
?>