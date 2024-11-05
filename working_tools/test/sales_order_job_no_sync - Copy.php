<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


  $booking_dtls_sql =  sql_select("select job_no,booking_no from wo_booking_dtls where status_active!=0 group by job_no,booking_no");

  foreach ($booking_dtls_sql as $val) 
  {
  		$job_arr[$val[csf("booking_no")]] = $val[csf("job_no")].",";
  }


   $sales_order_arr =  sql_select(" select a.id, a.sales_booking_no from  fabric_sales_order_mst a where a.booking_without_order <> 1 and a.within_group = 1 and a.status_active = 1 and a.is_deleted=0 and po_job_no is null");



   foreach ($sales_order_arr as  $val) 
   {
   		$job_no = "'".implode("','",array_filter(array_unique(explode(",",chop($job_arr[$val[csf("sales_booking_no")]],",")))))."'"; 
   		//echo  " update fabric_sales_order_mst a set po_job_no= ".$job_no." where a.id = ". $val[csf("id")] ." and a.status_active=1 "."<br>";
   		$sales_mst_up=execute_query("update fabric_sales_order_mst a set po_job_no= ".$job_no." where a.id = ". $val[csf("id")] ." and a.status_active=1");
   }



oci_commit($con); 

echo "Success";
die;
?>