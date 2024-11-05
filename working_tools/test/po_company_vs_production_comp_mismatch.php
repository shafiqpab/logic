<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$sqls="SELECT c.id,a.company_name,c.company_id,c.po_break_down_id  from wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c
 where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 
  and a.company_name <> c.company_id
  group by a.company_name,c.company_id,c.po_break_down_id ,c.id ";
$row_data=sql_select($sqls);
$kk=0;
foreach($row_data as $k=>$val)
{
	
	$po_comp=$val[csf("company_name")];
	$po_id=$val[csf("po_break_down_id")];
	$mst_id=$val[csf("id")];
	$up=execute_query("UPDATE pro_garments_production_mst set company_id=$po_comp where po_break_down_id=$po_id and id=$mst_id and status_active=1 ");
	$kk++;
	 
}

if($kk==29)
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