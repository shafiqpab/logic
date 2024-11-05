<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$apply_sql="select b.style_id from inv_receive_master a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
where a.booking_no=b.booking_no and a.entry_form=2 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 and b.style_id=c.id and c.req_ready_to_approved!=1 and c.entry_form_id=117 group by b.style_id order by b.style_id asc";
$apply_sql_res=sql_select($apply_sql); $i=0;
foreach($apply_sql_res as $row)
{
	$i++;
	$id=$row[csf("style_id")];
	//$up=execute_query("update sample_development_mst set req_ready_to_approved=1 where id=$id and req_ready_to_approved!=1 and entry_form_id=117");
	echo "update sample_development_mst set req_ready_to_approved=1 where id=$id and req_ready_to_approved!=1 and entry_form_id=117".'<br>';
}

/*$apply_sql="select id, cons_comp_id, width_dia_type from subcon_production_dtls where product_type in (3,4) and width_dia_type not in ('3','2','1','0')";
$apply_sql_res=sql_select($apply_sql); $i=0;
foreach($apply_sql_res as $row)
{
	$i++;
	$booking_no="'".$gsm_arr[$row[csf("cons_comp_id")]]."'";
	$id=$row[csf("id")];
	$up=execute_query("update subcon_production_dtls set width_dia_type=$booking_no where id=$id and product_type in (3,4) and width_dia_type not in ('3','2','1','0')");
	//echo "update subcon_production_dtls set width_dia_type=$booking_no where id=$id and product_type in (3,4) and width_dia_type not in ('3','2','1','0')".'<br>';
}*/

//oci_commit($con); 
	echo "Success".$i;