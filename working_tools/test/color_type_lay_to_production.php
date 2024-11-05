<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$sqls="SELECT bundle_no,color_type_id from ppl_cut_lay_bundle where  color_type_id is not null and  color_type_id >0 and status_active=1 and is_deleted=0 and to_char(insert_date,'YYYY')=2018 and   to_char(insert_date,'MM') =06  and bundle_num_prefix like '%ATTIR-%' ";
$row_data=sql_select($sqls);
foreach($row_data as $k=>$val)
{
	
	 
	$bundle_no=$val[csf("bundle_no")];
	$color_type_id=$val[csf("color_type_id")];
	$up=execute_query("UPDATE pro_garments_production_dtls set color_type_id=$color_type_id where bundle_no='$bundle_no'  and status_active=1 ");
	
	 
}

	oci_commit($con); 
	echo "Success";
	die;



 
?>