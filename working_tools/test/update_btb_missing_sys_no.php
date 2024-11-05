<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 

/*$sqls="select id, sys_no, chalan_no from sub_material_mst  
where entry_form is null and company_id = 2 and trans_type=1 and substr(sys_no,6,5)='AOPMR' 
order by id asc";
--AND BTB_PREFIX  LIKE  'BPKW-BTB%'  

$sqls="SELECT ID, BTB_SYSTEM_ID AS BTB_SYSTEM_ID, TO_CHAR(INSERT_DATE,'YY') AS BTB_YEAR, LC_TYPE_ID FROM COM_BTB_LC_MASTER_DETAILS 
WHERE IMPORTER_ID =2 and LC_TYPE_ID=3 AND STATUS_ACTIVE=1 
ORDER BY ID, BTB_YEAR ASC";
*/
$sqls="SELECT ID, BTB_SYSTEM_ID AS BTB_SYSTEM_ID, TO_CHAR(INSERT_DATE,'YY') AS BTB_YEAR, LC_TYPE_ID FROM COM_BTB_LC_MASTER_DETAILS 
WHERE IMPORTER_ID =2 and LC_TYPE_ID=3 AND STATUS_ACTIVE=1 
ORDER BY ID, BTB_YEAR ASC";



$row_data=sql_select($sqls);
$i=8;
foreach($row_data as $val)
{
	if($val["LC_TYPE_ID"]==1)
		$prefix="BTB";
	else if($val["LC_TYPE_ID"]==2)
		$prefix="MRGN";
	else if($val["LC_TYPE_ID"]==3)
		$prefix="FUND";
	
	if($i==1) $prev_year=$val["BTB_YEAR"];
	if($val["BTB_YEAR"]!=$prev_year)
	{
		$i=1;
		$prev_year=$val["BTB_YEAR"];
	}
	$sys_number_prefix_num=$i;
	$sys_number_prefix="BPKW-".$prefix."-".$val["BTB_YEAR"]."-";
	$sys_number=$sys_number_prefix.str_pad($i,5,"0",STR_PAD_LEFT);
	
	
	//echo $test_data=$sys_number."==".$val[csf("sys_no")]."==".$val[csf("chalan_no")]."<br>";
	//echo "UPDATE sub_material_mst set prefix_no='AOPL-RECV-19-', prefix_no_num='$sys_number_prefix_num',sys_no='$sys_number' where  id=$mst_id"."<br>";
	$update_issue_prefix=execute_query("UPDATE COM_BTB_LC_MASTER_DETAILS set btb_prefix='".$sys_number_prefix."', btb_prefix_number='".$i."',btb_system_id='".$sys_number."' where id=$mst_id");
	//if($update_issue_prefix){ $update_issue_prefix=1; } else {echo "UPDATE sub_material_mst set prefix_no='AOPL-DCIR-19-', prefix_no_num='$sys_number_prefix_num',sys_no='$sys_number' where  id=$mst_id";oci_rollback($con);die;}
	$i++;
	 
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