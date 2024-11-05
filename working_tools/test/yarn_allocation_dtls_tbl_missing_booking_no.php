<?
include('../../includes/common.php');
$con = connect();

$sql_alc_dtls="select b.id as DTLS_ID, a.booking_no as BOOKING_NO from inv_material_allocation_mst a, inv_material_allocation_dtls b where a.id=b.mst_id and a.item_id=b.item_id and a.job_no=b.job_no and b.booking_no is null and a.booking_no is not null";
$dtls_result=sql_select($sql_alc_dtls);

foreach($dtls_result as $row)
{
	$booking_no = $row["booking_no"];
	
	if($booking_no!="")
	{
		$upAlcID=execute_query("update inv_material_allocation_dtls set booking_no='".$booking_no."' where id=".$row["DTLS_ID"]." ");
		if($upAlcID){ $upAlcID=1;} else {echo "update inv_material_allocation_dtls set booking_no='".$booking_no."' where id=".$row["DTLS_ID"]." "; oci_rollback($con);die;}
	}
}

if($upAlcID)
{
	oci_commit($con); 
	echo "Transaction Data Update Successfully. <br>";die;
}
else
{
	oci_rollback($con);
	echo "Transaction Data Update Failed";
	die;
}
?>