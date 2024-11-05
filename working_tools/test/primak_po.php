<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$apply_sql="select b.id, b.po_number, b.po_quantity, b.shipment_date, c.id as color_size_table_id, c.color_number_id, c.size_number_id from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.buyer_name=49 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and b.pub_shipment_date between '01-Dec-2018' and '28-Feb-2019' ";
$apply_sql_res=sql_select($apply_sql); $i=0; $po_id_arr=array(); $color_size_arr=array();
foreach($apply_sql_res as $row)
{
	$po_id_arr[$row[csf("id")]]=$row[csf("id")];
	$color_size_arr[$row[csf("color_size_table_id")]]=$row[csf("color_size_table_id")];
	//$i++;
	//$booking_no="'".$row[csf("sales_booking_no")]."'";
	//$up=execute_query("update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1");
	//echo "update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1";
}
//echo $i;
//echo count($po_id_arr);
//print_r($po_id_arr);
//echo implode(",",$po_id_arr);

/*foreach($po_id_arr as $po_id)
{
	$up=execute_query("update pro_garments_production_mst set status_active=0, is_deleted=11 where po_break_down_id='$po_id' and status_active=1 and is_deleted=0 and production_type in (4,5,7,8)");
	$i++;
}*/

foreach($color_size_arr as $copo_id)
{
	$up=execute_query("update pro_garments_production_dtls set status_active=0, is_deleted=11 where color_size_break_down_id='$copo_id' and status_active=1 and is_deleted=0 and production_type in (4,5,7,8)");
	$i++;
}

/*echo $mst_prod="select id, garments_nature, company_id, 
   challan_no, po_break_down_id, item_number_id, 
   country_id, production_source, serving_company, 
   location, embel_name, embel_type, 
   production_date, production_quantity, production_type, 
   entry_break_down_type, production_hour_prev, sewing_line, 
   supervisor, carton_qty, remarks, 
   floor_id, alter_qnty, reject_qnty, 
   total_produced, yet_to_produced, receive_status, 
   inserted_by, insert_date, updated_by, 
   update_date, status_active, is_deleted, 
   is_locked, re_production_qty, prod_reso_allo, 
   spot_qnty, cut_no, batch_no, 
   production_hour, produced_by, break_down_type_rej, 
   delivery_mst_id, replace_qty, entry_form, 
   trans_type, dtls_id, wo_order_id, 
   currency_id, exchange_rate, rate, 
   amount, is_posted_account, wo_order_no, 
   pack_type, man_cutt_no, sending_location, 
   sending_company, finish_floor_id, is_tab
from c##buyerdb.pro_garments_production_mst
where
po_break_down_id in (27739,26262,27071,26249,27228,27067,27233,27312,27313,27029,27314,26425,27948,27740,28531,26430,28530,28528,28532,28527,28529)
and production_type in (4,5,7,8)";*/

oci_commit($con); 
	echo "Success".$i;