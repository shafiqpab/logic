<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
 die;
$barcode_sql="SELECT  barcode_no ,count(barcode_no)  from pro_qc_result_mst where   is_tab=1 and   status_active=1 and is_deleted=0 and ( barcode_no<>0  or  barcode_no<>'')  group by barcode_no having  count(barcode_no)>1    "; 
foreach(sql_select($barcode_sql) as $v)
{
	$barcode_no_arr[$v[csf("barcode_no")]]=$v[csf("barcode_no")];
}
 $all_barcodes=implode(",", $barcode_no_arr);
 $sels="SELECT  id,barcode_no    from pro_qc_result_mst where   is_tab=1 and   status_active=1 and is_deleted=0 and barcode_no in($all_barcodes) ";
foreach(sql_select($sels) as $v)
{
	 if(!in_array($v[csf("barcode_no")], $in_array_barcode))
	 {
	 	$barcode_wise_max_id[$v[csf("barcode_no")]]=0;
	 	$in_array_barcode[$v[csf("barcode_no")]]=$v[csf("barcode_no")];
	 }
	if($barcode_wise_max_id[$v[csf("barcode_no")]]<$v[csf("id")])
	$barcode_wise_max_id[$v[csf("barcode_no")]]=$v[csf("id")];
}


foreach(sql_select($sels) as $v)
{
	$max_id=$barcode_wise_max_id[$v[csf("barcode_no")]];
	$barcode_no= $v[csf("barcode_no")] ;
	$mst_id= $v[csf("id")] ;
	if($max_id==$mst_id)
	{

	}
	else
	{
		$up=execute_query("UPDATE pro_qc_result_mst set status_active=0,is_deleted=0 where barcode_no =$barcode_no and id not in($max_id) ",1);
		$up_dtls=execute_query("UPDATE pro_qc_result_dtls set status_active=0,is_deleted=0 where mst_id =$mst_id and mst_id not in($max_id) ",1);
	}
	
}
if($db_type==0)
{
	mysql_query("COMMIT");
	echo "success mysql";
}
else
{
	oci_commit($con);
	echo "success in oracle";
}

//$up=execute_query("UPDATE pro_qc_result_mst set status_active=0,is_deleted=11 where barcode_no in($all_barcodes) and ");
//echo "<pre>";print_r($barcode_wise_max_id);die;
?>