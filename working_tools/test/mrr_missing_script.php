<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 
// company 3 will be start 4336
// company 4 will be st 2279

//3459
/*$sqls="select id, requ_no from dyes_chem_issue_requ_mst  
where  entry_form = 156 and company_id = 2  and  substr(requ_no,6,4)='EDCR'
order by id asc";
$row_data=sql_select($sqls);
$kk=0;
$inc=6771;
foreach($row_data as $k=>$val)
{
	
	$delivery_mst_id=$val[csf("id")];
	$sys_number_prefix_num=$inc;
	$sys_number='AOPL-DCIR-19-'.str_pad($inc,5,"0",STR_PAD_LEFT);
	//$test_data.=$sys_number."==".$val[csf("requ_no")]."<br>";
	$update_delivery_mst=execute_query("UPDATE dyes_chem_issue_requ_mst set requ_no_prefix='AOPL-DCIR-19-', requ_prefix_num='$sys_number_prefix_num',requ_no='$sys_number' where  id=$delivery_mst_id");
	if($update_delivery_mst){ $update_delivery_mst=1; } else {echo "UPDATE dyes_chem_issue_requ_mst set requ_no_prefix='AOPL-DCIR-19-', requ_prefix_num='$sys_number_prefix_num',requ_no='$sys_number' where  id=$delivery_mst_id";oci_rollback($con);die;}
	$inc++;
	 
}*/

$sqls="select id, trims_del from trims_delivery_mst where  id>3456 order by id asc";
$row_data=sql_select($sqls);
$kk=0;
$inc=3455;
foreach($row_data as $k=>$val)
{
	
	$delivery_mst_id=$val[csf("id")];
	$sys_number_prefix_num=$inc;
	$sys_number='MTL-TD-20-'.str_pad($inc,5,"0",STR_PAD_LEFT);
	$test_data.=$sys_number."==".$val[csf("trims_del")]."<br>";
	$update_delivery_mst=execute_query("UPDATE trims_delivery_mst set del_no_prefix_num='$sys_number_prefix_num',trims_del='$sys_number' where  id=$delivery_mst_id");
	if($update_delivery_mst){ $update_delivery_mst=1; } else {echo "UPDATE trims_delivery_mst set del_no_prefix_num='$sys_number_prefix_num',trims_del='$sys_number' where  id=$delivery_mst_id";oci_rollback($con);die;}
	$inc++;
	 
}

//echo $test_data;die;
if($update_delivery_mst)
{
	oci_commit($con); 
	echo "Success".$test_data;

}
else
{
	oci_rollback($con);
	echo "failed";
}



 
?>