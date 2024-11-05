<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$gsm_arr=return_library_array( "select id, width_dia_type from pro_batch_create_dtls",'id','width_dia_type');

/*$apply_sql="select id, item_id, width_dia_type from subcon_delivery_dtls where process_id in (3,4) and width_dia_type not in ('3','2','1','0')";
$apply_sql_res=sql_select($apply_sql); $i=0;
foreach($apply_sql_res as $row)
{
	$i++;
	$booking_no="'".$gsm_arr[$row[csf("item_id")]]."'";
	$id=$row[csf("id")];
	$up=execute_query("update subcon_delivery_dtls set width_dia_type=$booking_no where id=$id and process_id in (3,4) and width_dia_type not in ('3','2','1','0')");
	//echo "update subcon_delivery_dtls set width_dia_type=$booking_no where id=$id and process_id in (3,4) and width_dia_type not in ('3','2','1','0')".'<br>';
}

$apply_sql="select id, cons_comp_id, width_dia_type from subcon_production_dtls where product_type in (3,4) and width_dia_type not in ('3','2','1','0')";
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