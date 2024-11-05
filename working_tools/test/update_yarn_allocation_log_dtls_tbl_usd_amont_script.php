<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$con = connect();

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

$permitted_user_id_arr = array(165);

if (!in_array($user_id, $permitted_user_id_arr)) {
	die('You are not authenticated');
	disconnect($con);
}

$sql_main = "SELECT a.ID, a.ITEM_ID,a.JOB_NO,a.PO_BREAK_DOWN_ID, a.QNTY,c.EXCHANGE_RATE,d.AVG_RATE_PER_UNIT FROM inv_mat_allocation_dtls_log a,fabric_sales_order_mst b, wo_pre_cost_mst c,product_details_master d WHERE a.po_break_down_id=b.id AND b.po_job_no=c.job_no AND a.item_id=d.id AND  a.item_category=1 AND a.is_sales=1 AND a.is_dyied_yarn<>1 AND a.avg_usd_rate=0 AND b.within_group=1 AND d.item_category_id=1 AND d.dyed_type!=1 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND d.status_active=1 AND d.is_deleted=0  AND d.avg_rate_per_unit>0";
//echo $sql_main;
//die;
$main_sql_result = sql_select($sql_main);

//echo "<pre>";
//print_r($main_sql_result); 

if (!empty($main_sql_result)) {

	foreach ($main_sql_result as $row) {
		$avg_usd_rate = number_format(($row['AVG_RATE_PER_UNIT'] / $row['EXCHANGE_RATE']), 2, ".", "");
		$avg_usd_amount = number_format(($row['QNTY'] * $avg_usd_rate), 4, ".", "");
		$avg_tk_rate = number_format($row['AVG_RATE_PER_UNIT'], 2, ".", "");
		$avg_tk_amount = number_format(($row['QNTY'] * $avg_tk_rate), 4, ".", "");

		//echo $row["ID"] . "==" . $row['AVG_RATE_PER_UNIT'] . "==" . $row['EXCHANGE_RATE'] . "==" . $row['QNTY'] . "==" . $avg_usd_rate . "<br>";
		$update_dtls_log = execute_query("UPDATE inv_mat_allocation_dtls_log SET avg_usd_rate=$avg_usd_rate, avg_usd_amount=$avg_usd_amount ,exchange_rate=" . $row['EXCHANGE_RATE'] . ", avg_tk_rate=$avg_tk_rate, avg_tk_amount=$avg_tk_amount WHERE id=" . $row["ID"] . " ");

		if (!$update_dtls_log) {
			echo "UPDATE inv_mat_allocation_dtls_log SET avg_usd_rate=$avg_usd_rate, avg_usd_amount=$avg_usd_amount ,exchange_rate=" . $row['EXCHANGE_RATE'] . ", avg_tk_rate=$avg_tk_rate, avg_tk_amount=$avg_tk_amount WHERE id=" . $row["ID"] . " ";
			oci_rollback($con);
			die;
		}
	}
} else {
	die("Data not found");
}


echo "10**" . $update_dtls_log;
die;

if ($update_dtls_log) {
	oci_commit($con);
	echo "1**Success";
	die;
} else {
	oci_rollback($con);
	echo "0**Failed";
	die;
}
