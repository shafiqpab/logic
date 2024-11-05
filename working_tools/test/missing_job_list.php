<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//echo "I am ahere"; die;
$fabric_data = sql_select("SELECT id, job_no from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 and job_no like '%FAL-20%' ");
$fabric_data_arr=array();
foreach ($fabric_data as $row) {
	$fabric_data_arr[$row[csf('id')]] = $row[csf('job_no')];
}
$fabric_cons_data = sql_select("SELECT id, job_no,pre_cost_fabric_cost_dtls_id from WO_PRE_COS_FAB_CO_AVG_CON_DTLS where job_no like '%FAL-20%'");
$fabric_cons_data_arr=array();
foreach ($fabric_cons_data as $row) {
	$fabric_cons_data_arr[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];
}

foreach ($fabric_data_arr as $key => $value) {
	if($fabric_cons_data_arr[$value][$key]=='')
	{
		echo __LINE__.$key.'--'.$value.'<br>';
	}
}
