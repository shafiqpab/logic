<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$booking_sql="select a.id,a.booking_no,b.job_no from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1  and a.company_id=3 and a.entry_form=118 and a.job_no is null   group by a.id,a.booking_no,b.job_no";//   company_id=3  and to_char(insert_date,'YYYY')=2020
//$booking_sql="select id, booking_no from wo_non_ord_embl_booking_mst";
//$booking_sql="select id, booking_no from wo_non_ord_samp_booking_mst";
$book_sql_res=sql_select($booking_sql); $i=0;
foreach($book_sql_res as $row)
{
	$i++;
	$booking_no=$row[csf("booking_no")];
	$job_no=$row[csf("job_no")];
	$booking_noArr[$row[csf("booking_no")]]=$row[csf("booking_no")];
	$id=$row[csf("id")];
	// echo "update wo_booking_mst set job_no='$job_no' where booking_no='$booking_no' and entry_form=118 and booking_type=1";
	//$up=execute_query("update wo_booking_mst set job_no='$job_no' where booking_no='$booking_no' and entry_form=118 and booking_type=1");
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