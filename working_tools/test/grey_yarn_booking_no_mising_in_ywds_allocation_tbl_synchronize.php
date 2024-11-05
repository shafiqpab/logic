<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}

$alc_sql = "SELECT job_no,booking_no,item_id from INV_MATERIAL_ALLOCATION_DTLS where ITEM_CATEGORY=1 and IS_DYIED_YARN!=1 and status_active=1 and is_deleted=0 and job_no is not null and booking_no is not null";
//echo $alc_sql; die;

$alcation_result = sql_select($alc_sql);

$allocation_booking_no_array = array();
foreach($alcation_result as $row)
{
	$allocation_booking_no_array[$row[csf("item_id")]][$row[csf("job_no")]]['booking_no']= $row[csf("booking_no")];
	$allocation_booking_no_array[$row[csf("item_id")]][$row[csf("job_no")]]['product_id']= $row[csf("item_id")];
	$allocation_booking_no_array[$row[csf("item_id")]][$row[csf("job_no")]]['job_no']= $row[csf("job_no")];
}

$wo_sql = "select a.id,job_no,product_id from wo_yarn_dyeing_dtls a, product_details_master b  where a.product_id=b.id and a.fab_booking_no is null and a.status_active=1 and a.is_deleted=0 and b.dyed_type!=1 and job_no is not null ";

//echo $wo_sql; die;

$wo_result = sql_select($wo_sql);

if(!empty($wo_result))
{
	foreach($wo_result as $row)
	{
		$job_no = $allocation_booking_no_array[$row[csf("product_id")]][$row[csf("job_no")]]['job_no'];
		$booking_no = $allocation_booking_no_array[$row[csf("product_id")]][$row[csf("job_no")]]['booking_no'];
		$prod_id = $allocation_booking_no_array[$row[csf("product_id")]][$row[csf("job_no")]]['product_id'];

		if($prod_id==$row[csf("product_id")] && $job_no==$row[csf("job_no")])
		{
			$update_wo_dtls = execute_query("update wo_yarn_dyeing_dtls set fab_booking_no='".$booking_no."' where id=".$row[csf("id")]." and  job_no='".$row[csf("job_no")]."' and  fab_booking_no is null");

			/* echo "update wo_yarn_dyeing_dtls set fab_booking_no='".$booking_no."' where id=".$row[csf("id")]." and  job_no='".$row[csf("job_no")]."' and  fab_booking_no is null"."<br>"; die; */

			if(!$update_wo_dtls) 
			{
				echo "update wo_yarn_dyeing_dtls set fab_booking_no".$booking_no." where id=".$row[csf("product_id")]." and  job_no='".$row[csf("job_no")]."' "; oci_rollback($con);die;
			}

		}
		
	}
	 
}

  
/*135=fabric_b
41= fabric_b
94= fabric b
42=booking_no*/
//echo "10**".$update_wo_dtls; die;
if( $update_wo_dtls ) 
{
	oci_commit($con); 
    echo "1**Success";
    die; 
}else{
	oci_rollback($con);
	echo "0**Failed";
    die;
}
?>