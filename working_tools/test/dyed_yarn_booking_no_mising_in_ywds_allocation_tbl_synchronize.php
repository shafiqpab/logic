<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}

$wo_sql = "SELECT b.id, b.job_no, b.entry_form FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE     a.id = b.mst_id AND b.job_no is not null AND (b.booking_no IS NULL OR b.fab_booking_no IS NULL) AND b.status_active = 1 AND a.status_active = 1 AND a.insert_date BETWEEN '01-Sep-2022' AND '19-feb-2023'";

//echo $wo_sql; die();

$wo_sql = sql_select($wo_sql);
foreach($wo_sql as $row)
{
	$job_no_arr[]  = "'".$row[csf('job_no')]."'";
	$job_no_string = implode(',',array_unique($job_no_arr));
}

if($job_no_string!="")
{
	$booking_sql = "select job_no,po_break_down_id,booking_no,booking_type from wo_booking_dtls where booking_type = 1 and status_active = 1 and job_no in ($job_no_string)";
	$booking_result = sql_select($booking_sql);
	$job_booking_arr = array();
	$job_booking_po_arr = array();
	foreach($booking_result as $row)
	{
		$job_booking_arr[$row[csf("job_no")]]=$row[csf("booking_no")];
		$job_booking_po_arr[$row[csf("job_no")]][$row[csf("booking_no")]]=$row[csf("po_break_down_id")];
	}

	//echo "<pre>";
	//print_r($job_booking_po_arr);
	//die($booking_sql);
}

foreach($wo_sql as $row)
{	
	$job_no = $row[csf("job_no")];
	$booking_no  = $job_booking_arr[$job_no];

	if($row[csf("entry_form")]==42)
	{
		$update_wo_dtls = execute_query("update wo_yarn_dyeing_dtls set booking_no='$booking_no' where  job_no='$job_no'");
	}
	else
	{
		$update_wo_dtls = execute_query("update wo_yarn_dyeing_dtls set fab_booking_no='$booking_no' where  job_no='$job_no'");
	}

	if($update_wo_dtls) $update_wo_dtls=1; else { echo "update wo_yarn_dyeing_dtls set fab_booking_no = booking_no='$booking_no' where  job_no='$job_no'"; oci_rollback($con);die;}
}

$dyed_alc_sql="select a.id as alc_mst_id, a.job_no, a.item_id, b.id alc_dtls_id,a.qnty from inv_material_allocation_mst a, inv_material_allocation_dtls b where  a.id=b.mst_id and a.item_id=b.item_id and a.job_no=b.job_no and a.item_category = 1 and a.booking_no is null and b.job_no in($job_no_string) and a.insert_date between '01-Sep-2022' and '26-Nov-2022' and a.is_dyied_yarn = 1 and a.entry_form = 1";
//echo $dyed_alc_sql;die();
 
$alc_result = sql_select($dyed_alc_sql);

if(!empty($alc_result))
{
	foreach ($alc_result as $row) 
	{
		$booking_no = $job_booking_arr[$row[csf("job_no")]];		
		$po_id = $job_booking_po_arr[$row[csf("job_no")]][$booking_no];
		;
		$qntity_brek_down_str = $row[csf("qnty")]."_".$po_id."_".$row[csf("job_no")];

		$update_mst_alc =execute_query("update inv_material_allocation_mst set booking_no='".$booking_no."', qnty_break_down='$qntity_brek_down_str' WHERE id =".$row[csf("alc_mst_id")]." and job_no ='".$row[csf("job_no")]."' and is_dyied_yarn=1 and booking_no is null ");

		if($update_mst_alc) $update_mst_alc=1; else {echo "update inv_material_allocation_mst set booking_no='".$booking_no."', qnty_break_down='$qntity_brek_down_str' WHERE id =".$row[csf("alc_mst_id")]." and job_no ='".$row[csf("job_no")]."' and is_dyied_yarn=1 and booking_no is null ";oci_rollback($con);die;}

		$update_dtls_alc =execute_query("update inv_material_allocation_dtls set booking_no='".$booking_no."',po_break_down_id=$po_id  where id=".$row[csf("alc_dtls_id")]." and mst_id =".$row[csf("alc_mst_id")]." and item_id=".$row[csf("item_id")]." and job_no ='".$row[csf("job_no")]."' and is_dyied_yarn =1 and booking_no is null ");

        if($update_dtls_alc) $update_dtls_alc=1; else { echo "pdate inv_material_allocation_dtls set booking_no='".$booking_no."',po_break_down_id=$po_id  where id=".$row[csf("alc_dtls_id")]." and mst_id =".$row[csf("alc_mst_id")]." and item_id=".$row[csf("item_id")]." and job_no ='".$row[csf("job_no")]."' and is_dyied_yarn =1 and booking_no is null ";oci_rollback($con);die;}	
	} 
}

  
/*125=fabric_b
41= fabric_b
94= fabric b
42=booking_no*/
//echo "10**".$update_wo_dtls ."&&". $update_mst_alc ."&&". $update_dtls_alc; die;
if( $update_wo_dtls && $update_mst_alc && $update_dtls_alc ) 
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