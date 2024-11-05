<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$cut_qnty_array=array();
$cut_qnty_sql="select bundle_no,production_qnty from pro_garments_production_dtls where status_active=1 and production_type=1 and  bundle_no in('FTML-18-42899-1',
'FTML-18-42899-2',
'FTML-18-42899-3',
'FTML-18-42899-4',
'FTML-18-42899-5',
'FTML-18-42899-6',
'FTML-18-42899-7',
'FTML-18-42899-8',
'FTML-18-42899-9',
'FTML-18-42899-10',
'FTML-18-42899-11',
'FTML-18-42899-12',
'FTML-18-42899-13',
'FTML-18-42899-14',
'FTML-18-42899-15',
'FTML-18-42899-16',
'FTML-18-42899-17',
'FTML-18-42899-18',
'FTML-18-42899-19',
'FTML-18-42899-20',
'FTML-18-42899-21',
'FTML-18-42899-22',
'FTML-18-42899-23',
'FTML-18-42899-24',
'FTML-18-42899-25',
'FTML-18-42899-26',
'FTML-18-42899-27',
'FTML-18-42899-28',
'FTML-18-42899-29',
'FTML-18-42899-30',
'FTML-18-42899-31',
'FTML-18-42899-32',
'FTML-18-42899-33',
'FTML-18-42899-34',
'FTML-18-42899-35',
'FTML-18-42899-36',
'FTML-18-42899-37',
'FTML-18-42899-38',
'FTML-18-42899-39',
'FTML-18-42899-40',
'FTML-18-42899-41',
'FTML-18-42899-42',
'FTML-18-42899-43',
'FTML-18-42899-44',
'FTML-18-42899-45',
'FTML-18-42899-46',
'FTML-18-42899-47',
'FTML-18-42899-48')";
foreach(sql_select($cut_qnty_sql) as $v)
{
	$cut_qnty_array[$v[csf("bundle_no")]]=$v[csf("production_qnty")];
}
print_r($cut_qnty_array); 

$sew_sql=$cut_qnty_sql="select mst_id, bundle_no,production_qnty from pro_garments_production_dtls where status_active=1 and production_type=2  and  bundle_no in('FTML-18-42899-1',
'FTML-18-42899-2',
'FTML-18-42899-3',
'FTML-18-42899-4',
'FTML-18-42899-5',
'FTML-18-42899-6',
'FTML-18-42899-7',
'FTML-18-42899-8',
'FTML-18-42899-9',
'FTML-18-42899-10',
'FTML-18-42899-11',
'FTML-18-42899-12',
'FTML-18-42899-13',
'FTML-18-42899-14',
'FTML-18-42899-15',
'FTML-18-42899-16',
'FTML-18-42899-17',
'FTML-18-42899-18',
'FTML-18-42899-19',
'FTML-18-42899-20',
'FTML-18-42899-21',
'FTML-18-42899-22',
'FTML-18-42899-23',
'FTML-18-42899-24',
'FTML-18-42899-25',
'FTML-18-42899-26',
'FTML-18-42899-27',
'FTML-18-42899-28',
'FTML-18-42899-29',
'FTML-18-42899-30',
'FTML-18-42899-31',
'FTML-18-42899-32',
'FTML-18-42899-33',
'FTML-18-42899-34',
'FTML-18-42899-35',
'FTML-18-42899-36',
'FTML-18-42899-37',
'FTML-18-42899-38',
'FTML-18-42899-39',
'FTML-18-42899-40',
'FTML-18-42899-41',
'FTML-18-42899-42',
'FTML-18-42899-43',
'FTML-18-42899-44',
'FTML-18-42899-45',
'FTML-18-42899-46',
'FTML-18-42899-47',
'FTML-18-42899-48')";

foreach(sql_select($sew_sql) as  $val)
{
	
	$bundle_no=$val[csf("bundle_no")];
	$qnty=$cut_qnty_array[$val[csf("bundle_no")]];
	$mst_id=$val[csf("mst_id")];

	 
	 
	$up_mst=execute_query("UPDATE pro_garments_production_mst set production_quantity=production_quantity-'$qnty' where   production_type=2 and id='$mst_id' ");
	$up_dtls=execute_query("UPDATE pro_garments_production_dtls set production_qnty='$qnty' where bundle_no='$bundle_no' and mst_id='$mst_id' and production_type=2 ");
	 
	 
}

 
	oci_commit($con); 
	echo "Success";

 



 
?>