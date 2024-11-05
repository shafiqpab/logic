<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die;
/*$job_smv_arr=sql_select( "SELECT a.id as id, a.job_no, a.set_smv, sum(b.smv_set) as item_set_smv from  wo_po_details_master a, wo_po_details_mas_set_details b where a.job_no=b.job_no and a.job_no like 'FFL--%' group by a.id, a.job_no, a.set_smv order by a.id DESC");
//print_r($job_smv_arr);die;and a.job_no='FAL-20-00285'
$i=1;
foreach($job_smv_arr as $val)
{
	if(number_format($val[csf("set_smv")],2)!=number_format($val[csf("item_set_smv")],2))
	{
		//echo $val[csf("job_no")].'=='.number_format($val[csf("set_smv")],2).'=='.number_format($val[csf("item_set_smv")],2).'<br>';
		//echo "update wo_po_details_master set set_smv=".number_format($val[csf("item_set_smv")],2)." where job_no='".$val[csf("job_no")]."'<br>";
		
		//$query=execute_query("update wo_po_details_master set set_smv='".number_format($val[csf("item_set_smv")],2)."' where job_no='".$val[csf("job_no")]."'",1);
		$i++;
	}
}*/

$job_smv_arr=sql_select( "select a.id as id, a.job_no, a.set_smv, b.sew_smv from  wo_po_details_master a, wo_pre_cost_mst b where a.job_no=b.job_no  group by a.id, a.job_no, a.set_smv, b.sew_smv order by a.id desc");
//print_r($job_smv_arr);die;and a.job_no='FAL-20-00285' and a.job_no like 'FFL-20-%'
$i=1;
foreach($job_smv_arr as $val)
{
	if(number_format($val[csf("set_smv")],2)!=number_format($val[csf("sew_smv")],2))
	{
		//echo $val[csf("job_no")].'=='.number_format($val[csf("set_smv")],2).'=='.number_format($val[csf("sew_smv")],2).'<br>';
		//echo "update wo_po_details_master set set_smv=".number_format($val[csf("item_set_smv")],2)." where job_no='".$val[csf("job_no")]."'<br>";
		
		$query=execute_query("update wo_po_details_master set set_smv='".number_format($val[csf("item_set_smv")],2)."' where job_no='".$val[csf("job_no")]."'",1);
		$i++;
	}
}
echo $i; die;


oci_commit($con); 
echo "Success".$i;

 
?>