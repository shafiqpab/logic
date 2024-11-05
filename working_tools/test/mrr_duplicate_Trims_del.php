<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 

$sqls="select id, trims_del from trims_delivery_mst  
where  entry_form=208 and company_id = 2 and substr(trims_del,6,2)='TD' and TO_CHAR(insert_date,'YYYY')='2021' and  DELIVERY_DATE > TO_DATE('05/23/2021 00:00:00', 'MM/DD/YYYY HH24:MI:SS')
order by id asc";

//select max(DEL_NO_PREFIX_NUM) from trims_delivery_mst where TO_CHAR(insert_date,'YYYY')='2021' group by company_id
$row_data=sql_select($sqls);
$kk=1;
$inc=639;
foreach($row_data as $val)
{
	//AOPL-TD-21-00203
	$test_data="";
	$mst_id=$val[csf("id")];
	$sys_number_prefix_num=$inc;
	$sys_number='AOPL-TD-21-'.str_pad($inc,5,"0",STR_PAD_LEFT);

	//echo $test_data=$sys_number."==".$val[csf("trims_del")]."<br>";
	//echo "UPDATE trims_delivery_mst set del_no_prefix='AOPL-TD-21-', del_no_prefix_num='$sys_number_prefix_num',trims_del='$sys_number' where  id=$mst_id"."<br>";
	$update_issue_prefix=execute_query("UPDATE trims_delivery_mst set del_no_prefix='AOPL-TD-21-', del_no_prefix_num='$sys_number_prefix_num',trims_del='$sys_number' where  id=$mst_id");
	//if($update_issue_prefix){ $update_issue_prefix=1; } else {echo "UPDATE trims_delivery_mst set del_no_prefix='AOPL-TD-21-', del_no_prefix_num='$sys_number_prefix_num',trims_del='$sys_number' where  id=$mst_id";oci_rollback($con);die;}
	$inc++;
	$kk++;
}
//echo $kk; //die;

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