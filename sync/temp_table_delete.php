<?php die;
$start = microtime(true);

require_once('../includes/common.php');

$con = connect();

$ridDel[TMP_BARCODE_NO]		=execute_query("delete from TMP_BARCODE_NO",0);
$ridDel[TMP_BATCH_ID]		=execute_query("delete from TMP_BATCH_ID",0);
$ridDel[TMP_BATCH_OR_ISS]	=execute_query("delete from TMP_BATCH_OR_ISS",0);
$ridDel[TMP_BOOKING_ID]		=execute_query("delete from TMP_BOOKING_ID",0);
$ridDel[TMP_BTB_LC_ID]		=execute_query("delete from TMP_BTB_LC_ID",0);
$ridDel[TMP_COLOR_ID]		=execute_query("delete from TMP_COLOR_ID",0);
$ridDel[TMP_COL_PO_ID]		=execute_query("delete from TMP_COL_PO_ID",0);
$ridDel[TMP_ISSUE_ID]		=execute_query("delete from TMP_ISSUE_ID",0);
$ridDel[TMP_JOB_NO]			=execute_query("delete from TMP_JOB_NO",0);
$ridDel[TMP_LCIDS]			=execute_query("delete from TMP_LCIDS",0);
$ridDel[TMP_MRR_NO]			=execute_query("delete from TMP_MRR_NO",0);
$ridDel[TMP_POID]			=execute_query("delete from TMP_POID",0);
$ridDel[TMP_PO_ID]			=execute_query("delete from TMP_PO_ID",0);
$ridDel[TMP_PROD_ID]		=execute_query("delete from TMP_PROD_ID",0);
$ridDel[TMP_PROG_NO]		=execute_query("delete from TMP_PROG_NO",0);
$ridDel[TMP_RECV_DTLS]		=execute_query("delete from TMP_RECV_DTLS",0);
$ridDel[TMP_RECV_MST_ID]	=execute_query("delete from TMP_RECV_MST_ID",0);
$ridDel[TMP_REQS_NO]		=execute_query("delete from TMP_REQS_NO",0);
$ridDel[TMP_SALES_ORDER_DTLS_ID]=execute_query("delete from TMP_SALES_ORDER_DTLS_ID",0);
$ridDel[TMP_SCIDS]			=execute_query("delete from TMP_SCIDS",0);
$ridDel[TMP_TNA]			=execute_query("delete from TMP_TNA",0);
$ridDel[TMP_TRANS_ID]		=execute_query("delete from TMP_TRANS_ID",0);
$ridDel[TMP_TRIM_COST_DTLS_ID]=execute_query("delete from TMP_TRIM_COST_DTLS_ID",0);

$ridDel[GBL_TEMP_ENGINE]=execute_query("delete from GBL_TEMP_ENGINE",0);


oci_commit($con);
disconnect($con); 

 

echo http_build_query($ridDel,'',"<br>");
 
 
//echo "Delete Execute Time: ".(microtime(true) - $start);



?> 