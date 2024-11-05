<?
	// rehan for chaity
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../includes/common.php');
	$con=connect();
	die;die;die;die;

	$sqls="SELECT a.id  ,c.country_id as org, a.country_id as wrong,b.color_size_break_down_id
	from pro_garments_production_mst a,pro_garments_production_dtls b, wo_po_color_size_breakdown c  where a.id=b.mst_id and a.status_active=1  and b.status_active=1 and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and c.status_active=1 and   a.country_id<>c.country_id and a.id=230181 group by  a.id, a.country_id,c.country_id,b.color_size_break_down_id ";
	$kk=0;
	foreach(sql_select($sqls) as $key=>$val)
	{
		$po_country=$val[csf("org")];
 		$mst_id=$val[csf("id")];
		$up=execute_query("UPDATE pro_garments_production_mst set country_id=$po_country where  id=$mst_id and status_active=1 ");
		$kk++;
	}

	if($kk==15383331)
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