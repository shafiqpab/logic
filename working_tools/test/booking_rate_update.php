<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

//$booking_sql="select id, booking_no from wo_booking_mst where booking_type=1  and company_id=1 and to_char(insert_date,'YYYY')=2020";
$booking_sql="select booking_no, booking_date, currency_id, company_id from wo_booking_mst where exchange_rate is null and is_short=1 and to_char(insert_date,'YYYY')=2021 and booking_type=1 ";

$book_sql_res=sql_select($booking_sql); $i=0;
foreach($book_sql_res as $row)
{
	$i++;
	$booking_no=$row[csf("booking_no")];
	$booking_noArr[$row[csf("booking_no")]]=$row[csf("booking_no")];
	$id=$row[csf("id")];
	$cdate = change_date_format($row[csf("booking_date")], "d-M-y", "-", 1);
	$cid=$row[csf('currency_id')];
	$company=$row[csf('company_id')];
	$queryText = "select conversion_rate from currency_conversion_rate where con_date<='" . $cdate . "' and currency=$cid and status_active=1 and is_deleted=0 and company_id=$company order by con_date desc";
	//echo $queryText; die;
	$nameArray = sql_select($queryText);
	if (count($nameArray) > 0) {
		foreach ($nameArray as $result) {
			if ($result[csf('conversion_rate')] != "") {
				$conversion_rate= $result[csf("conversion_rate")];
			}
		}
	}
	$up=execute_query("update wo_booking_mst set exchange_rate='$conversion_rate' where booking_no='$booking_no' and booking_type=1");	
	//echo "update wo_booking_mst set exchange_rate='$conversion_rate' where booking_no='$booking_no' and booking_type=1"; die;
}
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