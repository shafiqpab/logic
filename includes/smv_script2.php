<?
include('common.php');
/*$sql=sql_select("select a.id,a.job_no, a.set_smv, sum(b.smv_set) as smv_set  FROM wo_po_details_master a,wo_po_details_mas_set_details b WHERE a.job_no=b.job_no   GROUP BY a.id,a.job_no,a.set_smv  order by a.id");
$job=array();
foreach($sql as $row){
	if($row[csf('set_smv')] != $row[csf('smv_set')]){
		$job[$row[csf('job_no')]]=$row[csf('job_no')];
	}
}
echo implode(",",$job);*/


/*$sql=sql_select("select a.id,a.job_no, b.sew_smv as set_smv, sum(c.smv_set_precost) as smv_set  FROM wo_po_details_master a, wo_pre_cost_mst b, wo_po_details_mas_set_details c WHERE a.job_no=b.job_no and a.job_no=c.job_no and a.order_uom=1   GROUP BY a.id,a.job_no,b.sew_smv  order by a.id");
$job=array();
foreach($sql as $row){
	if($row[csf('set_smv')] != $row[csf('smv_set')]){
		$job[$row[csf('job_no')]]=$row[csf('job_no')];
	}
}
echo implode(",",$job);*/

$sql=sql_select("select a.id,a.job_no, b.sew_smv as set_smv, sum(c.smv_set) as smv_set  FROM wo_po_details_master a, wo_pre_cost_mst b, wo_po_details_mas_set_details c WHERE a.job_no=b.job_no and a.job_no=c.job_no and a.order_uom=1   GROUP BY a.id,a.job_no,b.sew_smv  order by a.id");
$field_array='smv_set';
$i=0;
foreach($sql as $row){
	if($row[csf('set_smv')] != $row[csf('smv_set')]){
		$data_array ="".$row[csf('set_smv')]."";
		$con = connect();
		 $rID=sql_update("wo_po_details_mas_set_details",$field_array,$data_array,"job_no","'".$row[csf('job_no')]."'",0);
		if($rID ){
			oci_commit($con);   
			echo $row[csf('job_no')]."==Inserted".$rID."<br/>";
		}
		else{
			oci_rollback($con);
			echo $row[csf('job_no')]."== Not Inserted".$rID;
		}
		disconnect($con);
		$i++;
	}
}
echo $i."OK";
?>