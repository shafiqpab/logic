<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$yarn_sql="select a.id,b.id dtls_id,c.id prod_id
from inv_material_allocation_mst a,inv_material_allocation_dtls b,product_details_master c
where a.id=b.mst_id and b.item_id=c.id and c.item_category_id=1 and a.status_active=1 and b.status_active=1
and c.status_active=1 and c.dyed_type=1  and (a.is_dyied_yarn is null or a.is_dyied_yarn=0)";
$yarn_data=sql_select($yarn_sql); $i=0;
foreach($yarn_data as $row)
{
	$mst_id = $row[csf("id")];
	$dtls_id = $row[csf("dtls_id")];
	execute_query("update inv_material_allocation_mst set is_dyied_yarn=1 where id=$mst_id");
	execute_query("update inv_material_allocation_dtls set is_dyied_yarn=1 where id=$dtls_id");
}

oci_commit($con);
echo "Success".$i;