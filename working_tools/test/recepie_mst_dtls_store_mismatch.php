<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$sqls="SELECT a.id,a.store_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b
 where a.id=b.mst_id and a.entry_form=220 and a.store_id is not null and b.store_id=0 and a.status_active=1 and b.status_active=1
  group by a.id,a.store_id ";
$row_data=sql_select($sqls);
$kk=0;
foreach($row_data as $k=>$val)
{
	
	$store_id=$val[csf("store_id")];
	$mst_id=$val[csf("id")];
	//$aa.="UPDATE pro_recipe_entry_dtls set store_id=$store_id where mst_id=$mst_id and status_active=1";
	$up=execute_query("UPDATE pro_recipe_entry_dtls set store_id=$store_id where mst_id=$mst_id and status_active=1 ");
	$kk++;
	 
}
//echo "10**".$aa; die;
if($kk==1804)
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