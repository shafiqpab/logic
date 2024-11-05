<?php
date_default_timezone_set("Asia/Dhaka");
$db_type=0; //$db_type=(0=Mysql,1=mssql,2=oracle);

$tna_process_type=1; //1 for Regular Process, 2 for Percentage based new process
$select_job_year_all=0; 

//$tna_process_start_date="2014-12-01";

if($db_type==0)
{
	$tna_process_start_date="2015-11-01";
}
if($db_type==2)
{
	$tna_process_start_date="01-Dec-2014";
}

require_once('db_functions_mysql.php');
 //require_once('db_functions_mssql.php');
 // require_once('db_functions_oracle.php');
include('common_functions.php');
include('array_function.php');
 
 	/*$pc_time= add_time(date("H:i:s",time()),360);  
	$pc_date_time = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));*/
	
	

	$pc_time= date("H:i:s",time());  
	 
	if($db_type==0) $pc_date_time = date("Y-m-d H:i:s",time()); 
	else $pc_date_time = date("d-M-Y h:i:s",time()); 
	
	if($db_type==0) $pc_date = date("Y-m-d",time());
	else $pc_date = date("d-M-Y",time());
	
/*	echo "<script>  var select_job_year_all=$select_job_year_all; </script>"; // Added for FAL Request 09-08-2015



/// template_id 	template_name 	buyer_id 	bank_id 	report_id 	format_id 	bank_specific 	buyer_specific 	status_active
$nameArray=sql_select( "select template_id,buyer_id,bank_id,report_id,format_id,bank_specific,buyer_specific from  lib_report_template where status_active=1 and template_id=0" );
foreach ( $nameArray as $result )
{
	if ($result[csf("buyer_specific")]!=0)
	{
		$lib_report_template_array[$result[csf("report_id")]][$result[csf("buyer_id")]]=$result[csf("format_id")];
	}
	else if ($result[csf("bank_specific")]!=0)
	{
		$lib_report_template_array[$result[csf("report_id")]][$result[csf("bank_id")]]=$result[csf("format_id")];
	}
	else $lib_report_template_array[$result[csf("report_id")]][0]=$result[csf("format_id")];
}
 */
 
?>