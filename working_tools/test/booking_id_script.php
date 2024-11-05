<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$booking_sql="select a.id, a.booking_no from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3  and a.company_id in(1,2,3,4,5,6) and   (b.booking_mst_id is null or  booking_mst_id=0)";//   company_id=3  and to_char(insert_date,'YYYY')=2020 and to_char(a.insert_date,'YYYY')=2023
//$booking_sql="select id, booking_no from wo_non_ord_embl_booking_mst";
//$booking_sql="select id, booking_no from wo_non_ord_samp_booking_mst";
$book_sql_res=sql_select($booking_sql); $i=0;
foreach($book_sql_res as $row)
{
	$i++;
	$booking_no=$row[csf("booking_no")];
	$booking_noArr[$row[csf("booking_no")]]=$row[csf("booking_no")];
	$id=$row[csf("id")];
	//echo "update wo_po_break_down set job_id='$id' where job_no_mst='$job_no' and job_id=0".'<br>';
	$up=execute_query("update wo_booking_dtls set booking_mst_id='$id' where booking_no='$booking_no'  and booking_type=3");
	//$up=execute_query("update wo_non_ord_embl_booking_dtls set booking_mst_id='$id' where booking_no='$booking_no' and booking_mst_id=0");//1
	
	//$up=execute_query("update wo_non_ord_samp_booking_dtls set booking_mst_id='$id' where booking_no='$booking_no' and booking_mst_id=0");//2
	//$up=execute_query("update wo_non_ord_samp_yarn_dtls set booking_mst_id='$id' where booking_no='$booking_no' and booking_mst_id=0");//2
	
}
//print_r($booking_noArr);die;
if($up)
{
	oci_commit($con); 
	echo "Success=".$i;
}
else
{
	oci_rollback($con);
	echo "Not Success=".$i;
}