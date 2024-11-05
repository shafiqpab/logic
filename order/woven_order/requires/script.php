<?
//echo "monzu";
$conMy = mysql_connect( 'localhost', 'root', '' );
$DBMy =  mysql_select_db('logic_erp_3rd_version', $conMy);
//====================================inv_material_allocation_mst
$dataMy=mysql_query("select id,job_no,po_break_down_id,item_category,allocation_date,fabric_des,pre_cost_fabric_cost_id,booking_no,color_id,dia,item_id,qnty,qnty_break_down,inserted_by,insert_date,updated_by, update_date,status_active,is_deleted from inv_material_allocation_mst");
$field_array="id,job_no,po_break_down_id,item_category,allocation_date,fabric_des,pre_cost_fabric_cost_id,booking_no,color_id,dia,item_id,qnty,qnty_break_down,inserted_by,insert_date,updated_by, update_date,status_active,is_deleted";
//include('../../../includes/common.php');
$i=0;
while($row = mysql_fetch_array($dataMy))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	if ($i!=0) $data_array .=",";
	$data_array .="(".$row['id'].",'".$row['job_no']."','".$row['po_break_down_id']."','".$row['item_category']."','".change_date_format($row['allocation_date'],'yyyy-mm-dd','-',1)."','".$row['fabric_des']."','".$row['pre_cost_fabric_cost_id']."','".$row['booking_no']."','".$row['color_id']."','".$row['dia']."','".$row['item_id']."','".$row['qnty']."','".$row['qnty_break_down']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."')";
$i++;	
}
$rID=sql_insert("inv_material_allocation_mst",$field_array,$data_array,0);

//======================================================sub_fab_finish_dtls
$dataMy1=mysql_query("select id,system_id,challen_no,delivery_date,order_no,style_name,buyer_name,number_roll,process,fabric_dts,fabric_qty,amount,rate,remarks, inserted_by, insert_date,updated_by ,update_date,	status_active,is_deleted,is_locked from sub_fab_finish_dtls");
$field_array1="id,system_id,challen_no,delivery_date,order_no,style_name,buyer_name,number_roll,process,fabric_dts,fabric_qty,amount,rate,remarks, inserted_by, insert_date,updated_by ,update_date,status_active,is_deleted,is_locked";
$i=0;
while($row = mysql_fetch_array($dataMy1))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	if ($i!=0) $data_array1 .=",";
	$data_array1 .="(".$row['id'].",'".$row['system_id']."','".$row['challen_no']."','".change_date_format($row['delivery_date'],'yyyy-mm-dd','-',1)."','".$row['order_no']."','".$row['style_name']."','".$row['buyer_name']."','".$row['number_roll']."','".$row['process']."','".$row['fabric_dts']."','".$row['fabric_qty']."','".$row['amount']."','".$row['rate']."','".$row['remarks']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."','".$row['is_locked']."')";
$i++;	
}
$rID1=sql_insert("sub_fab_finish_dtls",$field_array1,$data_array1,0);
//============================

//======================================================wo_booking_mst_hstry
$dataMy2=mysql_query("select id,approved_no,booking_id,booking_type,is_short,booking_no_prefix,	booking_no_prefix_num,booking_no,company_id,buyer_id,job_no,po_break_down_id, 	item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,booking_percent, colar_excess_percent,cuff_excess_percent,is_approved,is_deleted,status_active,inserted_by,insert_date,updated_by,update_date from wo_booking_mst_hstry");

$field_array2="id,approved_no,booking_id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no,po_break_down_id, 	item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,booking_percent, colar_excess_percent,cuff_excess_percent,is_approved,is_deleted,status_active,inserted_by,insert_date,updated_by,update_date";
$i=0;
while($row = mysql_fetch_array($dataMy2))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	if ($i!=0) $data_array2 .=",";
	$data_array2 .="(".$row['id'].",'".$row['approved_no']."','".$row['booking_id']."','".$row['booking_type']."','".$row['is_short']."','".$row['booking_no_prefix']."','".$row['booking_no_prefix_num']."','".$row['booking_no']."','".$row['company_id']."','".$row['buyer_id']."','".$row['job_no']."','".$row['po_break_down_id']."','".$row['item_category']."','".$row['fabric_source']."','".$row['currency_id']."','".$row['exchange_rate']."','".$row['pay_mode']."','".$row['source']."','".change_date_format($row['booking_date'],'yyyy-mm-dd','-',1)."','".change_date_format($row['delivery_date'],'yyyy-mm-dd','-',1)."','".$row['booking_month']."','".$row['booking_year']."','".$row['supplier_id']."','".$row['attention']."','".$row['booking_percent']."','".$row['colar_excess_percent']."','".$row['cuff_excess_percent']."','".$row['is_approved']."','".$row['is_deleted']."','".$row['status_active']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."')";
$i++;	
}
$rID2=sql_insert("wo_booking_mst_hstry",$field_array2,$data_array2,0);
//============================


//======================================================wo_nonor_sambo_dtl_hstry
$dataMy3=mysql_query("select id,approved_no,booking_dtls_id,booking_no,style_id,sample_type,body_part,color_type_id,lib_yarn_count_determination_id,construction,composition,fabric_description,gsm_weight,	fabric_color,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,yarn_breack_down,process_loss_method,inserted_by,insert_date,updated_by,update_date ,status_active,is_deleted from wo_nonor_sambo_dtl_hstry");

$field_array3="id,approved_no,booking_dtls_id,booking_no,style_id,sample_type,body_part,color_type_id,lib_yarn_count_deter_id,construction,composition,fabric_description,gsm_weight,	fabric_color,item_size,dia_width,finish_fabric,process_loss,grey_fabric,rate,amount,yarn_breack_down,process_loss_method,inserted_by,insert_date,updated_by,update_date ,status_active,is_deleted";
$i=0;
while($row = mysql_fetch_array($dataMy3))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	if ($i!=0) $data_array3 .=",";
	$data_array3 .="(".$row['id'].",'".$row['approved_no']."','".$row['booking_dtls_id']."','".$row['booking_no']."','".$row['style_id']."','".$row['sample_type']."','".$row['body_part']."','".$row['color_type_id']."','".$row['lib_yarn_count_determination_id']."','".$row['construction']."','".$row['composition']."','".$row['fabric_description']."','".$row['gsm_weight']."','".$row['fabric_color']."','".$row['item_size']."','".$row['dia_width']."','".$row['finish_fabric']."','".$row['process_loss']."','".$row['grey_fabric']."','".$row['rate']."','".$row['amount']."','".$row['yarn_breack_down']."','".$row['process_loss_method']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."')";
$i++;	
}
$rID3=sql_insert("wo_nonor_sambo_dtl_hstry",$field_array3,$data_array3,0);
//============================

//======================================================wo_pre_cost_fabric_cost_dtls
$dataMy4=mysql_query("select id,job_no,item_number_id,body_part_id,fab_nature_id,color_type_id,lib_yarn_count_determination_id,	construction,composition,fabric_description,	gsm_weight,	color_size_sensitive,color,	avg_cons,fabric_source,rate,amount,avg_finish_cons,avg_process_loss,inserted_by,insert_date,updated_by,update_date,	status_active,	is_deleted,	company_id,	costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls");

$field_array4="id,job_no,item_number_id,body_part_id,fab_nature_id,color_type_id,lib_yarn_count_deter_id,	construction,composition,fabric_description,	gsm_weight,	color_size_sensitive,color,	avg_cons,fabric_source,rate,amount,avg_finish_cons,avg_process_loss,inserted_by,insert_date,updated_by,update_date,	status_active,	is_deleted,	company_id,	costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type";
$i=0;
while($row = mysql_fetch_array($dataMy4))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	if ($i!=0) $data_array4 .=",";
	$data_array4 .="(".$row['id'].",'".$row['job_no']."','".$row['item_number_id']."','".$row['body_part_id']."','".$row['fab_nature_id']."','".$row['color_type_id']."','".$row['lib_yarn_count_determination_id']."','".$row['construction']."','".$row['composition']."','".$row['fabric_description']."','".$row['gsm_weight']."','".$row['color_size_sensitive']."','".$row['color']."','".$row['avg_cons']."','".$row['fabric_source']."','".$row['rate']."','".$row['amount']."','".$row['avg_finish_cons']."','".$row['avg_process_loss']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."','".$row['company_id']."','".$row['costing_per']."','".$row['consumption_basis']."','".$row['process_loss_method']."','".$row['cons_breack_down']."','".$row['msmnt_break_down']."','".$row['color_break_down']."','".$row['yarn_breack_down']."','".$row['marker_break_down']."','".$row['width_dia_type']."')";
$i++;	
}
$rID4=sql_insert("wo_pre_cost_fabric_cost_dtls",$field_array4,$data_array4,0);
//============================

//======================================================wo_pre_cost_fab_conv_cost_dtls
$dataMy5=mysql_query("select id,job_no,fabric_description, cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,	updated_by,	update_date, 	status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls");

$field_array5="id,job_no,fabric_description, cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, 	status_active,is_deleted";
$i=0;
while($row = mysql_fetch_array($dataMy5))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	if ($i!=0) $data_array5 .=",";
	$data_array5 .="(".$row['id'].",'".$row['job_no']."','".$row['fabric_description']."','".$row['cons_process']."','".$row['req_qnty']."','".$row['charge_unit']."','".$row['amount']."','".$row['color_break_down']."','".$row['charge_lib_id']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."')";
$i++;	
}
$rID5=sql_insert("wo_pre_cost_fab_conv_cost_dtls",$field_array5,$data_array5,0);
//============================

//======================================================sample_development_dtls
$dataMy6=mysql_query("select id,sample_mst_id,sample_name,sample_color,	working_factory,sent_to_factory_date,factory_dead_line,recieve_date_from_buyer,receive_date_from_factory,	fabrication,sent_to_buyer_date,key_point,approval_status,department,status_date,tf_receive_date,buyer_meeting_date,sample_charge,sample_curency,buyer_dead_line,buyer_req_no, 	comments,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from sample_development_dtls");

$field_array6="id,sample_mst_id,sample_name,sample_color,working_factory,sent_to_factory_date,factory_dead_line,recieve_date_from_buyer,receive_date_from_factory,	fabrication,sent_to_buyer_date,key_point,approval_status,department,status_date,tf_receive_date,buyer_meeting_date,sample_charge,sample_curency,buyer_dead_line,buyer_req_no, 	comments,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted";
$i=0;
while($row = mysql_fetch_array($dataMy6))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	
	if ($i!=0) $data_array6 .=",";
	$data_array6 .="(".$row['id'].",'".$row['sample_mst_id']."','".$row['sample_name']."','".$row['sample_color']."','".$row['working_factory']."','".change_date_format($row['sent_to_factory_date'],'yyyy-mm-dd','-',1)."','".change_date_format($row['factory_dead_line'],'yyyy-mm-dd','-',1)."','".change_date_format($row['recieve_date_from_buyer'],'yyyy-mm-dd','-',1)."','".change_date_format($row['receive_date_from_factory'],'yyyy-mm-dd','-',1)."','".$row['fabrication']."','".change_date_format($row['sent_to_buyer_date'],'yyyy-mm-dd','-',1)."','".$row['key_point']."','".$row['approval_status']."','".$row['department']."','".change_date_format($row['status_date'],'yyyy-mm-dd','-',1)."','".change_date_format($row['tf_receive_date'],'yyyy-mm-dd','-',1)."','".change_date_format($row['buyer_meeting_date'],'yyyy-mm-dd','-',1)."','".$row['sample_charge']."','".$row['sample_curency']."','".change_date_format($row['buyer_dead_line'],'yyyy-mm-dd','-',1)."','".$row['buyer_req_no']."','".$row['comments']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."')";
$i++;	
}
$rID6=sql_insert("sample_development_dtls",$field_array6,$data_array6,0);
//============================

//====================================================== wo_booking_dtls
$dataMy7=mysql_query("select id,job_no,	po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type,is_short,	fabric_color_id,gmts_color_id,item_size, 	gmts_size,fin_fab_qnty,grey_fab_qnty,rate,amount,color_type,construction,copmposition,gsm_weight,dia_width,process_loss_percent,trim_group,description,brand_supplier,uom ,process, 	sensitivity,wo_qnty,delivery_date,cons_break_down,rmg_qty, gmt_item,responsible_dept,responsible_person, reason, country_id_string,inserted_by,insert_date,	updated_by, 	update_date,status_active,is_deleted from  wo_booking_dtls");

$field_array7="id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type,is_short,	fabric_color_id,gmts_color_id,item_size, 	gmts_size,fin_fab_qnty,grey_fab_qnty,rate,amount,color_type,construction,copmposition,gsm_weight,dia_width,process_loss_percent,trim_group,description,brand_supplier,uom ,process, 	sensitivity,wo_qnty,delivery_date,cons_break_down,rmg_qty, gmt_item,responsible_dept,responsible_person, reason, country_id_string,inserted_by,insert_date,	updated_by, 	update_date,status_active,is_deleted";
$i=0;
while($row = mysql_fetch_array($dataMy7))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	
	if ($i!=0) $data_array7 .=",";
	$data_array7 .="(".$row['id'].",'".$row['job_no']."','".$row['po_break_down_id']."','".$row['pre_cost_fabric_cost_dtls_id']."','".$row['color_size_table_id']."','".$row['booking_no']."','".$row['booking_type']."','".$row['is_short']."','".$row['fabric_color_id']."','".$row['gmts_color_id']."','".$row['item_size']."','".$row['gmts_size']."','".$row['fin_fab_qnty']."','".$row['grey_fab_qnty']."','".$row['rate']."','".$row['amount']."','".$row['color_type']."','".$row['construction']."','".$row['copmposition']."','".$row['gsm_weight']."','".$row['dia_width']."','".$row['process_loss_percent']."','".$row['trim_group']."','".$row['description']."','".$row['brand_supplier']."','".$row['uom']."','".$row['process']."','".$row['sensitivity']."','".$row['wo_qnty']."','".change_date_format($row['delivery_date'],'yyyy-mm-dd','-',1)."','".$row['cons_break_down']."','".$row['rmg_qty']."','".$row['gmt_item']."','".$row['responsible_dept']."','".$row['responsible_person']."','".$row['reason']."','".$row['country_id_string']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."')";
$i++;	
}
$rID7=sql_insert(" wo_booking_dtls",$field_array7,$data_array7,0);
//============================

//====================================================== wo_po_embell_approval
$dataMy8=mysql_query("select id,job_no_mst,po_break_down_id,embellishment_id,embellishment_type_id,color_name_id,target_approval_date,sent_to_supplier,submitted_to_buyer,	approval_status,approval_status_date,supplier_name,embellishment_comments,current_status,is_deleted,status_active,inserted_by,insert_date,updated_by,update_date,garments_nature from wo_po_embell_approval");

$field_array8="id,job_no_mst,po_break_down_id,embellishment_id,embellishment_type_id,color_name_id,target_approval_date,sent_to_supplier,submitted_to_buyer,	approval_status,approval_status_date,supplier_name,embellishment_comments,current_status,is_deleted,status_active,inserted_by,insert_date,updated_by,update_date,garments_nature";
$i=0;
while($row = mysql_fetch_array($dataMy8))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	
	if ($i!=0) $data_array8 .=",";
	$data_array8 .="(".$row['id'].",'".$row['job_no_mst']."','".$row['po_break_down_id']."','".$row['embellishment_id']."','".$row['embellishment_type_id']."','".$row['color_name_id']."','".change_date_format($row['target_approval_date'],'yyyy-mm-dd','-',1)."','".change_date_format($row['sent_to_supplier'],'yyyy-mm-dd','-',1)."','".change_date_format($row['submitted_to_buyer'],'yyyy-mm-dd','-',1)."','".$row['approval_status']."','".change_date_format($row['approval_status_date'],'yyyy-mm-dd','-',1)."','".$row['supplier_name']."','".$row['embellishment_comments']."','".$row['current_status']."','".$row['is_deleted']."','".$row['status_active']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['garments_nature']."')";
$i++;	
}
$rID8=sql_insert(" wo_po_embell_approval",$field_array8,$data_array8,0);
//============================

//====================================================== wo_pre_cost_trim_cost_dtls
$dataMy9=mysql_query("select id,job_no,trim_group,description,brand_sup_ref,cons_uom,cons_dzn_gmts,	rate,amount,apvl_req,nominated_supp,cons_breack_down,inserted_by,insert_date, 	updated_by,update_date,status_active,is_deleted from wo_pre_cost_trim_cost_dtls");

$field_array9="id,job_no_mst,po_break_down_id,embellishment_id,embellishment_type_id,color_name_id,target_approval_date,sent_to_supplier,submitted_to_buyer,	approval_status,approval_status_date,supplier_name,embellishment_comments,current_status,is_deleted,status_active,inserted_by,insert_date,updated_by,update_date,garments_nature";
$i=0;
while($row = mysql_fetch_array($dataMy8))
{
	$insert_date = date("d-M-Y h:i:s",strtotime($row['insert_date'])); 
	$update_date = date("d-M-Y h:i:s",strtotime($row['update_date'])); 
	
	if ($i!=0) $data_array9 .=",";
	$data_array9 .="(".$row['id'].",'".$row['job_no']."','".$row['trim_group']."','".$row['description']."','".$row['brand_sup_ref']."','".$row['cons_uom']."','".$row['cons_dzn_gmts']."','".$row['rate']."','".$row['amount']."','".$row['apvl_req']."','".$row['nominated_supp']."','".$row['cons_breack_down']."','".$row['inserted_by']."','".$insert_date."','".$row['updated_by']."','".$update_date."','".$row['status_active']."','".$row['is_deleted']."')";
$i++;	
}
$rID9=sql_insert("wo_pre_cost_trim_cost_dtls",$field_array9,$data_array9,0);
//============================



$con = connect();
if($rID )
{
	oci_commit($con);  
	echo "0";
}
else
{
	oci_rollback($con);
	echo "10";
}
disconnect($con);
?>