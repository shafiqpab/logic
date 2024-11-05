<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$sqls="SELECT a.id as mst_id,b.id as dtls_id,b.production_qnty,a.production_type as type1 ,b.production_type as type2,a.po_break_down_id ,b.color_size_break_down_id  from pro_garments_production_mst a ,pro_garments_production_dtls b 
where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_qnty>0
and b.color_size_break_down_id in (select id from wo_po_color_size_breakdown where status_active=0 and is_deleted=1)
group by a.id,b.id,b.production_qnty,a.production_type,b.production_type,a.po_break_down_id,b.color_size_break_down_id order by a.production_type ";
$row_data=sql_select($sqls);
$kk=0;
foreach($row_data as $k=>$val)
{
	
	$type1=$val[csf("type1")];
	$po_id=$val[csf("po_break_down_id")];
	$color_size_id=$val[csf("color_size_break_down_id")];
	$mst_id=$val[csf("mst_id")];
	$dtls_id=$val[csf("dtls_id")];
	$qnty=$val[csf("production_qnty")];
	$up=execute_query("UPDATE pro_garments_production_mst set production_quantity=production_quantity-$qnty where po_break_down_id=$po_id and id=$mst_id and status_active=1 and production_type=$type1 ");
	$up_dtls=execute_query("UPDATE pro_garments_production_dtls set status_active=0,is_deleted=1 where mst_id=$mst_id and id=$dtls_id and status_active=1 and production_type=$type1 ");
	$kk++;
	 
}

if($kk==1714)
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