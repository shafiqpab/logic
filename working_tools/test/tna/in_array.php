<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$con = connect();
 
 
function tmp_data_process($dataArr=array()){
	$moduleWiseTmpTableArr=array(1=>'TMP_TNA');
	$activeTable=$moduleWiseTmpTableArr[$dataArr[module]];
	$activeUserId=$_SESSION['logic_erp']['user_id'];
	$activeField = ($dataArr[data_type]==0)?'NUMBER_DATA':'VARCHAR_DATA';
	
 	global $con ;

	if($dataArr[process_type]==2){//Insert
		$strQuery = "INSERT INTO $activeTable (USERID,$activeField,TYPE) VALUES($activeUserId,'{$dataArr[data]}',{$dataArr[type]})";
		$resultss=oci_parse($con, $strQuery);
<<<<<<< HEAD
		$response = oci_execute($resultss);//,OCI_COMMIT_ON_SUCCESS
=======
		$response = oci_execute($resultss,OCI_NO_AUTO_COMMIT);
>>>>>>> 8feb83d0abae376a412865eea1b854c981ca1106
		return $response;
	}
	else if($dataArr[process_type]==1){//Delete
		$strQuery = "delete FROM $activeTable where USERID=$activeUserId";
		$resultss=oci_parse($con, $strQuery);
<<<<<<< HEAD
		$response = oci_execute($resultss);//,OCI_COMMIT_ON_SUCCESS
=======
		$response = oci_execute($resultss,OCI_NO_AUTO_COMMIT);//OCI_COMMIT_ON_SUCCESS
>>>>>>> 8feb83d0abae376a412865eea1b854c981ca1106
		return $response;
	}
	else if($dataArr[process_type]==3){//Join Query
		$responseArr[table] = "$activeTable tmp";
		$responseArr[where] = "tmp.$activeField=$dataArr[join] and tmp.type=$dataArr[type]";
		return $responseArr;
	}
}



tmp_data_process(array('process_type'=>1,'module'=>1)); //Delete
//tmp_data_process(array('process_type'=>2,'module'=>1,'data_type'=>0,'data'=>'1111','type'=>2)); //Insert
$retDataArr = tmp_data_process(array('process_type'=>3,'module'=>1,'data_type'=>0,'type'=>2,'join'=>'PO_NUMBER_ID'));//Join
print_r($retDataArr);


	
			
	
	
	
	
	
	
	
	$sql = "SELECT a.JOB_NO, b.ID,b.PO_QUANTITY
		FROM  wo_po_details_master a,  wo_po_break_down b 
<<<<<<< HEAD
		WHERE a.job_no=b.job_no_mst and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 AND  b.PUB_SHIPMENT_DATE > '01-MAR-2021'"; 
=======
		WHERE a.job_no=b.job_no_mst and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 AND  b.PUB_SHIPMENT_DATE > '01-JAN-2022'"; 
>>>>>>> 8feb83d0abae376a412865eea1b854c981ca1106
	$sql_res=sql_select($sql);
	foreach( $sql_res as  $row ) 
	{	
		tmp_data_process(array('process_type'=>2,'module'=>1,'data_type'=>0,'data'=>$row[ID],'type'=>2));
	}
	oci_commit($con); 
 

	$sql = "SELECT PO_NUMBER_ID, JOB_NO,PO_RECEIVE_DATE FROM  tna_process_mst, $retDataArr[table] WHERE is_deleted = 0  and status_active=1 and $retDataArr[where]"; 
	
<<<<<<< HEAD
	//echo $sql;die;
=======
echo $sql;die;

	$time_elapsed_secs = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
	//echo $time_elapsed_secs;die;
	
>>>>>>> 8feb83d0abae376a412865eea1b854c981ca1106
	
	$sql_res=sql_select($sql);
	foreach( $sql_res as  $row ) 
	{	
		
		
	}



//



?>

