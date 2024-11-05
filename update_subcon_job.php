<?php
date_default_timezone_set("Asia/Dhaka");
require_once('includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

/*$sql="select id, subcon_job from subcon_ord_mst where entry_form=238";

$sqlpoRes = sql_select($sql);
$i=1;
foreach ($sqlpoRes as $row)
{
	//echo "update subcon_ord_dtls set mst_id=".$row[csf('id')]." where job_no_mst='".$row[csf('subcon_job')]."' and mst_id=0 ".'<br>';
	
	$rID=execute_query("update subcon_ord_dtls set mst_id=".$row[csf('id')]." where job_no_mst='".$row[csf('subcon_job')]."' and mst_id=0 ");
	$i++;
}
echo $i;*/

/*$sql="select a.id, a.job_no_mst from subcon_ord_dtls a, subcon_ord_mst b where b.subcon_job=a.job_no_mst and  b.entry_form=238 group by a.id, a.job_no_mst";

$sqlpoRes = sql_select($sql);
$i=1;
foreach ($sqlpoRes as $row)
{
	//echo "update subcon_ord_breakdown set job_no_mst='".$row[csf('job_no_mst')]."' where order_id='".$row[csf('id')]."' and job_no_mst=''".'<br>';
	
	$rID=execute_query("update subcon_ord_breakdown set job_no_mst='".$row[csf('job_no_mst')]."' where order_id='".$row[csf('id')]."' and job_no_mst='' ");
	$i++;
}
echo $i;*/