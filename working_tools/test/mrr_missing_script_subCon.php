<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 

$sqls="select id, sys_no, chalan_no from sub_material_mst  
where  entry_form is null and company_id = 2 and trans_type=1 and substr(sys_no,6,5)='AOPMR' 
order by id asc";
$row_data=sql_select($sqls);
$kk=1;
$inc=968;
foreach($row_data as $val)
{
	$test_data="";
	$mst_id=$val[csf("id")];
	$sys_number_prefix_num=$inc;
	$sys_number='AOPL-RECV-19-'.str_pad($inc,5,"0",STR_PAD_LEFT);
	//echo $test_data=$sys_number."==".$val[csf("sys_no")]."==".$val[csf("chalan_no")]."<br>";
	//echo "UPDATE sub_material_mst set prefix_no='AOPL-RECV-19-', prefix_no_num='$sys_number_prefix_num',sys_no='$sys_number' where  id=$mst_id"."<br>";
	$update_issue_prefix=execute_query("UPDATE sub_material_mst set prefix_no='AOPL-RECV-19-', prefix_no_num='$sys_number_prefix_num',sys_no='$sys_number' where  id=$mst_id");
	//if($update_issue_prefix){ $update_issue_prefix=1; } else {echo "UPDATE sub_material_mst set prefix_no='AOPL-DCIR-19-', prefix_no_num='$sys_number_prefix_num',sys_no='$sys_number' where  id=$mst_id";oci_rollback($con);die;}
	$inc++;
	$kk++;
	 
}
echo $kk; //die;

if($update_issue_prefix)
{
	oci_commit($con); 
	echo "Success";

}
else
{
	oci_rollback($con);
	echo "failed";
}



 
?>