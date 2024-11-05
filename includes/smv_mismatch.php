<?
include('common.php');
$sql=sql_select("select a.id,a.job_no, a.set_smv, sum(b.smv_set) as smv_set  FROM wo_po_details_master a,wo_po_details_mas_set_details b WHERE a.job_no=b.job_no   GROUP BY a.id,a.job_no,a.set_smv  order by a.id");
$job=array();
foreach($sql as $row){
	if($row[csf('set_smv')] != $row[csf('smv_set')]){
		$job[$row[csf('job_no')]]=$row[csf('job_no')];
	}
}
echo implode(",",$job);
?>