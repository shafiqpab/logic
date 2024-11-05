<?

session_start();
error_reporting(-1);
ini_set('display_errors', 1);
ini_set("allow_url_fopen","On");
include('../../includes/common.php');


$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
 
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_variable_list=str_replace("'","",$cbo_variable_list);
	$fromDate=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
	$toDate=date("Y-m-d",strtotime(str_replace("'","",$txt_date_to)));
	$URL="";
	$IP=$_SERVER[HTTP_HOST];
	$Url_path=explode("/",$_SERVER[REQUEST_URI]);
	$URL='http://'.$IP."/".$Url_path[1];
	
	if($cbo_variable_list==1)
	{
		$knitting_plan_data=file_get_contents('http://192.168.100.29/erp_test/logic-api/index.php/api/salesorder/plan/form_date/'.$fromDate.'/to_date/'.$toDate);
		//$knitting_plan_data=file_get_contents($URL.'/logic-api/index.php/api/salesorder/plan/form_date/'.$fromDate.'/to_date/'.$toDate);
	}
	else
	{
		//$knitting_plan_data=file_get_contents($URL.'/logic-api/index.php/api/salesorder/delevery_to_store/form_date/'.$fromDate.'/to_date/'.$toDate);
		$knitting_plan_data=file_get_contents('http://192.168.100.29/erp_test/logic-api/index.php/api/salesorder/delevery_to_store/form_date/'.$fromDate.'/to_date/'.$toDate);
	//print_r($knitting_plan_data);die;
	}
	echo $knitting_plan_data;die;
}

die;
//************************************ Start *************************************************









?>