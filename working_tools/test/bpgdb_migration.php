<?php
header( 'Content-type:text/html; charset=utf-8' );
session_start();
if( $_SESSION['logic_erp']['user_id']=='' ) header( 'location:login.php' );
include('../includes/common.php');
//include('../includes/excel_reader.php');
$data = $_REQUEST['data'];
$user_login_id = $_SESSION['logic_erp']['user_id'];
extract( $_REQUEST );
//$con=connect();
//disconnect($con);

//for mysql
function connect_zs()
{
	$DB_SERVER		= "43.240.103.173";		// Database Server ID
	$DB_LOGIN		= "Logic";			// Database UserName
	$DB_PASSWORD	= "asro@123";		// Database Password
	$DB				= "bpg";		//fal_hrm, noman_hrm140714 ,noman_hrm140714, fal_hrm_170115, logic_hrm //badar_hrm //fariha_hrm120515// 
	$HTTP_HOST		= "43.240.103.173";		// HTTP Host
	
	$con_=mysql_pconnect( $DB_SERVER, $DB_LOGIN, $DB_PASSWORD );
	mysql_select_db( $DB );
	if(!$con_)
	{
		trigger_error("Problem connecting to server");
		//echo "1";
	}

	if(!$DB)
	{
		trigger_error("Problem selecting database");
		//echo "2";
	}
	//echo $con_;
	return $con_;
}

function disconnect_zs($m_zs) 
{
	$discdb = mysql_close($m_zs);
	if(!$discdb)
	{
		trigger_error("Problem disconnecting database");
	}	
}

//form mysql
$zs_con=connect_zs();
//print_r($zs_con); die;

/*$result=mysql_query("SELECT id, master_tble_id, details_tble_id, form_name, image_location, pic_size, is_deleted, file_type, real_file_name FROM common_photo_library");
$tcons_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$tcons_arr[$row['id']]['a']=$row['master_tble_id'];
	$tcons_arr[$row['id']]['b']=$row['details_tble_id'];
	$tcons_arr[$row['id']]['c']=$row['form_name'];
	$tcons_arr[$row['id']]['d']=$row['image_location'];
	$tcons_arr[$row['id']]['e']=$row['pic_size'];
	$tcons_arr[$row['id']]['f']=$row['is_deleted'];
	$tcons_arr[$row['id']]['g']=$row['file_type'];
	$tcons_arr[$row['id']]['h']=$row['real_file_name'];
}

disconnect_zs($zs_con);
//print_r($tcons_arr); die;
$con=connect();
//echo $con;die;
foreach ($tcons_arr as $id=>$data)
{
	//echo "insert into common_photo_library (id, master_tble_id, details_tble_id, form_name, image_location, pic_size, is_deleted, file_type, real_file_name) values ('".$id."','".$data['a']."','".$data['b']."','".$data['b']."','".$data['d']."','".$data['e']."','".$data['f']."','".$data['g']."','".$data['h']."')<br>";
	$up=execute_query("insert into common_photo_library (id, master_tble_id, details_tble_id, form_name, image_location, pic_size, is_deleted, file_type, real_file_name) values ('".$id."','".$data['a']."','".$data['b']."','".$data['b']."','".$data['d']."','".$data['e']."','".$data['f']."','".$data['g']."','".$data['h']."')");
}
oci_commit($con);
disconnect($con);
echo "Success".$i;
//echo "<pre>";
//print_r($tcons_arr); 
die;*/



/*$result=mysql_query("SELECT id, qnty_break_down, fabric_des, po_break_down_id FROM inv_material_allocation_mst");
$tcons_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$tcons_arr[$row['id']]['a']=$row['qnty_break_down'];
	$tcons_arr[$row['id']]['b']=$row['fabric_des'];
	$tcons_arr[$row['id']]['c']=$row['po_break_down_id'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($tcons_arr as $id=>$data)
{
	$up=execute_query("update inv_material_allocation_mst set qnty_break_down='".$data['a']."',fabric_des='".$data['b']."',po_break_down_id='".$data['c']."' where id='".$id."'");
}
oci_commit($con);
disconnect($con);
//echo "Success".$i;
//echo "<pre>";
//print_r($tcons_arr); 
die;
*/

/*$result=mysql_query("SELECT id, cons_breack_down FROM wo_pre_cost_trim_cost_dtls ");
$tcons_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$tcons_arr[$row['id']]=$row['cons_breack_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($tcons_arr as $id=>$data)
{
	$up=execute_query("update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'");
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/
//echo "<pre>";
//print_r($tcons_arr); die;

/*$result=mysql_query("SELECT id, comments, mer_comments FROM tna_progress_comments ");
$tnacomm_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$tnacomm_arr[$row['id']]['c']=$row['comments'];
	$tnacomm_arr[$row['id']]['mc']=$row['mer_comments'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($tnacomm_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update tna_progress_comments set comments='".$data['c']."', mer_comments='".$data['mc']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, color_size_rate_data, actual_po_infos FROM com_export_invoice_ship_dtls ");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['c']=$row['color_size_rate_data'];
	$sdtls_arr[$row['id']]['m']=$row['actual_po_infos'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update com_export_invoice_ship_dtls set color_size_rate_data='".$data['c']."', actual_po_infos='".$data['m']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/


/*$result=mysql_query("SELECT id, local_comn, foreign_comn, bl_clause, reimbursement_clauses, discount_clauses FROM com_export_lc ");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['local_comn'];
	$sdtls_arr[$row['id']]['b']=$row['foreign_comn'];
	$sdtls_arr[$row['id']]['c']=$row['bl_clause'];
	$sdtls_arr[$row['id']]['d']=$row['reimbursement_clauses'];
	$sdtls_arr[$row['id']]['e']=$row['discount_clauses'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update com_export_lc set local_comn='".$data['a']."', foreign_comn='".$data['b']."',bl_clause='".$data['c']."', reimbursement_clauses='".$data['d']."',discount_clauses='".$data['e']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, item_description FROM com_pi_item_details ");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['item_description'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update com_pi_item_details set item_description='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, buyer_job_no, order_id  FROM inv_issue_master ");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['buyer_job_no'];
	$sdtls_arr[$row['id']]['b']=$row['order_id'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update inv_issue_master set buyer_job_no='".$data['a']."', order_id='".$data['b']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/


/*$result=mysql_query("SELECT id, save_string FROM inv_trims_entry_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['save_string'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update inv_trims_entry_dtls set save_string='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i; die;*/

/*$result=mysql_query("SELECT id, entry_page_id FROM lib_body_part");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['entry_page_id'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update lib_body_part set entry_page_id='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, tag_buyer FROM lib_supplier");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['tag_buyer'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update lib_supplier set tag_buyer='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, advice, save_data, no_fo_feeder_data, collar_cuff_data FROM ppl_planning_info_entry_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['advice'];
	$sdtls_arr[$row['id']]['b']=$row['save_data'];
	$sdtls_arr[$row['id']]['c']=$row['no_fo_feeder_data'];
	$sdtls_arr[$row['id']]['d']=$row['collar_cuff_data'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update ppl_planning_info_entry_dtls set advice='".$data['a']."',save_data='".$data['b']."',no_fo_feeder_data='".$data['c']."',collar_cuff_data='".$data['d']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, remarks FROM pro_finish_fabric_rcv_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['remarks'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update pro_finish_fabric_rcv_dtls set remarks='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, production_date, production_hour FROM pro_garments_production_mst where production_hour!='00:00:00' ");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$txt_reporting_hour=date('d-M-Y', strtotime($row['production_date']))." ".$row['production_hour'];
	$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
	$sdtls_arr[$row['id']]['a']=$txt_reporting_hour;
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo 'update pro_garments_production_mst set production_hour='.$data["a"].' where id='.$id.'<br>';
	$d="update pro_garments_production_mst set production_hour=".$data['a']." where id=".$id;
	$up=execute_query($d);
	//echo $up.'<br>';
	//if($id>100) exit();
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, remarks FROM pro_grey_prod_delivery_mst");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['remarks'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update pro_grey_prod_delivery_mst set remarks='".$data['a']."' where id='".$id."'");
	echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, meeting_date, meeting_time FROM qc_meeting_mst");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$txt_reporting_hour=date('d-M-Y', strtotime($row['meeting_date']))." ".$row['meeting_time'];
	$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
	$sdtls_arr[$row['id']]['a']=$txt_reporting_hour;
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo 'update pro_garments_production_mst set production_hour='.$data["a"].' where id='.$id.'<br>';
	$d="update qc_meeting_mst set meeting_time=".$data['a']." where id=".$id;
	$up=execute_query($d);
	//echo $up.'<br>';
	//if($id>100) exit();
}
oci_commit($con);
disconnect($con);
echo "Success".$i;
*/

/*$result=mysql_query("SELECT id, prod_start_time FROM prod_resource_dtls_time where prod_start_time!='00:00:00'");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	//$txt_reporting_hour=date('d-M-Y', strtotime($row['meeting_date']))." ".$row['prod_start_time'];
	$txt_reporting_hour="to_date('".$row['prod_start_time']."','HH24:MI:SS')";
	$sdtls_arr[$row['id']]['a']=$txt_reporting_hour;
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo 'update prod_resource_dtls_time set prod_start_time='.$data["a"].' where id='.$id.'<br>';
	$d="update prod_resource_dtls_time set prod_start_time=".$data['a']." where id=".$id;
	$up=execute_query($d);
	//echo $up.'<br>';
	//if($id>100) exit();
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, lunch_start_time FROM prod_resource_dtls_time where lunch_start_time!='00:00:00'");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	//$txt_reporting_hour=date('d-M-Y', strtotime($row['meeting_date']))." ".$row['prod_start_time'];
	$txt_reporting_hour="to_date('".$row['lunch_start_time']."','HH24:MI:SS')";
	$sdtls_arr[$row['id']]['a']=$txt_reporting_hour;
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo 'update prod_resource_dtls_time set prod_start_time='.$data["a"].' where id='.$id.'<br>';
	$d="update prod_resource_dtls_time set lunch_start_time=".$data['a']." where id=".$id;
	$up=execute_query($d);
	//echo $up.'<br>';
	//if($id>100) exit();
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/


/*$result=mysql_query("SELECT id, sewing_date, reporting_hour FROM sample_sewing_output_dtls where reporting_hour!='00:00:00'");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$txt_reporting_hour=date('d-M-Y', strtotime($row['sewing_date']))." ".$row['reporting_hour'];
	$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
	$sdtls_arr[$row['id']]['a']=$txt_reporting_hour;
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo 'update pro_garments_production_mst set production_hour='.$data["a"].' where id='.$id.'<br>';
	$d="update sample_sewing_output_dtls set reporting_hour=".$data['a']." where id=".$id;
	$up=execute_query($d);
	//echo $up.'<br>';
	//if($id>100) exit();
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, production_date, hour FROM subcon_gmts_prod_dtls where hour!='00:00:00'");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$txt_reporting_hour=date('d-M-Y', strtotime($row['production_date']))." ".$row['hour'];
	$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
	$sdtls_arr[$row['id']]['a']=$txt_reporting_hour;
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo 'update pro_garments_production_mst set production_hour='.$data["a"].' where id='.$id.'<br>';
	$d="update subcon_gmts_prod_dtls set hour=".$data['a']." where id=".$id;
	$up=execute_query($d);
	//echo $up.'<br>';
	//if($id>100) exit();
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, order_id FROM subcon_outbound_bill_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['order_id'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update subcon_outbound_bill_dtls set order_id='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_break_down FROM wo_booking_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_booking_dtls set cons_break_down='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_break_down FROM wo_booking_dtls_hstry");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_booking_dtls_hstry set cons_break_down='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/


/*$result=mysql_query("SELECT id, job_no, po_break_down_id FROM wo_booking_mst");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['job_no'];
	$sdtls_arr[$row['id']]['b']=$row['po_break_down_id'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_booking_mst set job_no='".$data['a']."',po_break_down_id='".$data['b']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, terms FROM wo_booking_terms_condition");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['terms'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_booking_terms_condition set terms='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, terms_and_condition FROM wo_non_order_info_mst");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['terms_and_condition'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_non_order_info_mst set terms_and_condition='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, terms_and_condition FROM wo_non_order_info_mst_history");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['terms_and_condition'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_non_order_info_mst_history set terms_and_condition='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, fabric_description, yarn_breack_down FROM wo_non_ord_samp_booking_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['fabric_description'];
	$sdtls_arr[$row['id']]['b']=$row['yarn_breack_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_non_ord_samp_booking_dtls set fabric_description='".$data['a']."', yarn_breack_down='".$data['b']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, country_remarks FROM wo_po_color_size_breakdown");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['country_remarks'];
	//$sdtls_arr[$row['id']]['b']=$row['yarn_breack_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_po_color_size_breakdown set country_remarks='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, lapdip_comments FROM wo_po_lapdip_approval_info");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['lapdip_comments'];
	//$sdtls_arr[$row['id']]['b']=$row['yarn_breack_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_po_lapdip_approval_info set lapdip_comments='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, sample_comments FROM wo_po_sample_approval_info");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['sample_comments'];
	$sdtls_arr[$row['id']]['b']=$row['yarn_breack_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_po_sample_approval_info set sample_comments='".$data['a']."' where id='".$id."'");
	echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, marker_break_down, cons_breack_down, msmnt_break_down, color_break_down FROM wo_pre_cost_fabric_cost_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['marker_break_down'];
	$sdtls_arr[$row['id']]['b']=$row['cons_breack_down'];
	$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_fabric_cost_dtls set marker_break_down='".$data['a']."',cons_breack_down='".$data['b']."',msmnt_break_down='".$data['c']."',color_break_down='".$data['d']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, marker_break_down, cons_breack_down, msmnt_break_down, color_break_down FROM wo_pre_cost_fabric_cost_dtls_h");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['marker_break_down'];
	$sdtls_arr[$row['id']]['b']=$row['cons_breack_down'];
	$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_fabric_cost_dtls_h set marker_break_down='".$data['a']."',cons_breack_down='".$data['b']."',msmnt_break_down='".$data['c']."',color_break_down='".$data['d']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;
*/
/*$result=mysql_query("SELECT id, color_break_down, charge_lib_id FROM wo_pre_cost_fab_conv_cost_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['color_break_down'];
	$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_fab_conv_cost_dtls set color_break_down='".$data['a']."',charge_lib_id='".$data['b']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, color_break_down, charge_lib_id FROM wo_pre_cost_fab_con_cst_dtls_h");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['color_break_down'];
	$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_fab_con_cst_dtls_h set color_break_down='".$data['a']."',charge_lib_id='".$data['b']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_breack_down FROM wo_pre_cost_trim_cost_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_breack_down'];
	//$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_breack_down FROM wo_pre_cost_trim_cost_dtls_his");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_breack_down'];
	//$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_trim_cost_dtls_his set cons_breack_down='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, country_id FROM wo_pre_cost_trim_co_cons_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['country_id'];
	//$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_trim_co_cons_dtls set country_id='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, country_id FROM wo_pre_cost_trim_co_cons_dtl_h");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['country_id'];
	//$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cost_trim_co_cons_dtl_h set country_id='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;
*/
/*$result=mysql_query("SELECT id, country_id FROM wo_pre_cos_emb_co_avg_con_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['country_id'];
	//$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cos_emb_co_avg_con_dtls set country_id='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;
*/

/*$result=mysql_query("SELECT id, remarks FROM wo_pre_cos_fab_co_avg_con_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['remarks'];
	//$sdtls_arr[$row['id']]['b']=$row['charge_lib_id'];
	//$sdtls_arr[$row['id']]['c']=$row['msmnt_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pre_cos_fab_co_avg_con_dtls set remarks='".$data['a']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_breack_down, msmnt_break_down, marker_break_down FROM wo_pri_quo_fabric_cost_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_breack_down'];
	$sdtls_arr[$row['id']]['b']=$row['msmnt_break_down'];
	$sdtls_arr[$row['id']]['c']=$row['marker_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pri_quo_fabric_cost_dtls set cons_breack_down='".$data['a']."',msmnt_break_down='".$data['b']."',marker_break_down='".$data['c']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_breack_down, msmnt_break_down, marker_break_down FROM wo_pri_quo_fabric_cost_dtls");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_breack_down'];
	$sdtls_arr[$row['id']]['b']=$row['msmnt_break_down'];
	$sdtls_arr[$row['id']]['c']=$row['marker_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pri_quo_fabric_cost_dtls set cons_breack_down='".$data['a']."',msmnt_break_down='".$data['b']."',marker_break_down='".$data['c']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/

/*$result=mysql_query("SELECT id, cons_breack_down, msmnt_break_down, marker_break_down FROM wo_pri_quo_fab_cost_dtls_his");
$sdtls_arr=array();
while($row=mysql_fetch_assoc($result))
{
	$i++;
	$sdtls_arr[$row['id']]['a']=$row['cons_breack_down'];
	$sdtls_arr[$row['id']]['b']=$row['msmnt_break_down'];
	$sdtls_arr[$row['id']]['c']=$row['marker_break_down'];
	//$sdtls_arr[$row['id']]['d']=$row['color_break_down'];
}

disconnect_zs($zs_con);
$con=connect();
foreach ($sdtls_arr as $id=>$data)
{
	//echo "update wo_pre_cost_trim_cost_dtls set cons_breack_down='".$data."' where id='".$id."'";
	$up=execute_query("update wo_pri_quo_fab_cost_dtls_his set cons_breack_down='".$data['a']."',msmnt_break_down='".$data['b']."',marker_break_down='".$data['c']."' where id='".$id."'");
	//echo $up.'<br>';
}
oci_commit($con);
disconnect($con);
echo "Success".$i;*/
die;

?>