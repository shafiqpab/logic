<?php
error_reporting(0);
date_default_timezone_set("Asia/Dhaka");
//manual precision settings here
//ini_set('precision',8);

$db_type=2; //$db_type=(0=Mysql,1=mssql,2=oracle);

$tna_process_type=1; //1 for Regular Process, 2 for Percentage based new process
$select_job_year_all=0;
//$tna_process_start_date="2014-12-01";

if($db_type==0)
{
	$tna_process_start_date="2014-12-01";
}
if($db_type==2)
{
	$tna_process_start_date="01-Dec-2014";
}

 //require_once('db_functions_mysql.php');
 //require_once('db_functions_mssql.php');

require_once('db_functions_oracle.php');
include('common_functions.php');
include('array_function.php');

$pc_time= date("H:i:s",time());

if($db_type==0) $pc_date_time = date("Y-m-d H:i:s",time());
	else $pc_date_time = date("d-M-Y h:i:s A",time());

		if($db_type==0) $pc_date = date("Y-m-d",time());
		else $pc_date = date("d-M-Y",time());





/*
echo "<script>  var select_job_year_all='$select_job_year_all'; </script>"; // Added for FAL Request 09-08-2015*/

/*

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

function sql_insertss( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}

		if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	   //return $strQuery ;
	}
	else
	{
		$tmpv=explode(")",$arrValues);

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0";
		}
		return "1";

	}
    //return  $strQuery; die;
	echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";
	die;

	if ( $commit==1 )
	{
		if (!oci_error($exestd))
		{
			$pc_time= add_time(date("H:i:s",time()),360);
			$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
			$pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')";
			$resultss=oci_parse($con, $strQuery);
			oci_execute($resultss);
			$_SESSION['last_query']="";
			//oci_commit($con);
			return "0";
		}
		else
		{
			//oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	die;
}

function sql_updatess($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	echo $strQuery;die;


	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}


function base_path($file){
    $path = str_replace('\\', '/', __DIR__);
    $path = str_replace('includes', $file,$path );
    return $path;
 }



 function base_url($file=''){
    //return 'http://localhost/platform-v3.5:8080/'.$file;
	$dir_arr=explode('/',$_SERVER['REQUEST_URI']);
	$port = '';
	if (!in_array($_SERVER['SERVER_PORT'], [80, 443])) {
        $port = ":$_SERVER[SERVER_PORT]";
    }
	$http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? "https://" : "http://";
	$base_url=$http .$_SERVER['SERVER_NAME'].$port.'/'.$dir_arr[1].'/'.$file;
	return $base_url;
 }



?>

