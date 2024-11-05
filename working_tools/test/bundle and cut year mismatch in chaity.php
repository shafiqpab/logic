<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
die;die;
$con=connect();
$sqls=" SELECT a.id, a.cutting_no,bundle_no ,bundle_num_prefix from ppl_cut_lay_mst a ,ppl_cut_lay_bundle b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and 
a.cutting_no in('CCL-17-009744','CCL-17-009818')

and to_char(a.insert_date,'YYYY')=2017 and    to_char(b.insert_date,'YYYY')=2018
group by   a.id, a.cutting_no,bundle_no ,bundle_num_prefix ";
$row_data=sql_select($sqls);
$kk=0;
foreach($row_data as $k=>$val)
{
	
	$bundle_no=$val[csf("bundle_no")];
	$bundle_num_prefix=$val[csf("bundle_num_prefix")];

	$id=$val[csf("id")];
	$cut_no=$val[csf("cut_no")];
	$new_bundle_ex=explode("-", $bundle_no);
	$new_bundle_pre_ex=explode("-", $bundle_num_prefix);
	$new_bundle=$new_bundle_ex[0]."-"."17"."-".$new_bundle_ex[2]."-".$new_bundle_ex[3];
	$new_bundle_num_prefix=$new_bundle_pre_ex[0]."-"."17"."-".$new_bundle_pre_ex[2];
	//echo "re   $new_bundle <br>";
	 
	 
	$up_dtls=execute_query("UPDATE ppl_cut_lay_bundle set bundle_no='$new_bundle',bundle_num_prefix='$new_bundle_num_prefix' where bundle_no='$bundle_no' and mst_id='$id' ");
	$kk++;
	 
}

if($kk==182)
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