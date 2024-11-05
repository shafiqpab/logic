<?
session_start();
include('../includes/common.php');
$con=connect();

execute_query("truncate table TMP_BARCODE_NO");
execute_query("truncate table TMP_BATCH_ID");
execute_query("truncate table TMP_BATCH_OR_ISS");
execute_query("truncate table TMP_BOOKING_ID");
execute_query("truncate table TMP_BTB_LC_ID");
execute_query("truncate table TMP_COLOR_ID");
execute_query("truncate table TMP_COL_PO_ID");
execute_query("truncate table TMP_ISSUE_ID");
execute_query("truncate table TMP_JOB_NO");
execute_query("truncate table TMP_LCIDS");
execute_query("truncate table TMP_MRR_NO");
execute_query("truncate table TMP_POID");
execute_query("truncate table TMP_PO_ID");
execute_query("truncate table TMP_PROD_ID");
execute_query("truncate table TMP_PROG_NO");
execute_query("truncate table TMP_RECV_DTLS");
execute_query("truncate table TMP_RECV_MST_ID");
execute_query("truncate table TMP_REQS_NO");
execute_query("truncate table TMP_SALES_ORDER_DTLS_ID");
execute_query("truncate table TMP_SCIDS");
execute_query("truncate table TMP_TNA");
execute_query("truncate table TMP_TRANS_ID");
execute_query("truncate table TMP_TRIM_COST_DTLS_ID");

oci_commit($con); 
echo "All Temporary tables truncated successfully";