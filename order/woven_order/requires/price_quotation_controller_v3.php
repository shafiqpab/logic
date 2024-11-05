<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];
$type		= $_REQUEST['type'];
$permission	= $_SESSION['page_permission'];

$color_library	=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library	=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");

//----------------------------------------------------Start---------------------------------------------------------

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$data' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
} 


if($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --",$selected, "",0 );      	 
}



if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
		
	if($operation==0)	/*For Save*/
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
				
		 $id=return_next_id( "id", "wo_price_quotation_v3_mst", 1 ) ;
		 
		 $sys_no_arr=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'PQS', date("Y",time()), 5, "select id,sys_no_prefix,sys_prefix_num from  wo_price_quotation_v3_mst where company_id=$cbo_company_id  $year_cond=".date('Y',time())." order by id desc ", "sys_no_prefix", "sys_prefix_num" ));
		 
		 $field_array="id, sys_no_prefix, sys_prefix_num, system_id, company_id, team_member, buyer_id, quot_date, agent, style_ref, gmts_item, fabrication, color, yarn_count, cons_size, order_qty, measurment_basis, yarn_cons, yarn_unit_price, yarn_total, knit_fab_purc_cons, knit_fab_purc_price, knit_fab_purc_total, woven_fab_purc_cons, woven_fab_purc_price, woven_fab_purc_total, yarn_dye_crg_cons, yarn_dye_crg_price, yarn_dye_crg_total, knit_crg_cons, knit_crg_unit_price, knit_crg_total, dye_crg_cons, dye_crg_unit_price, dye_crg_total, spandex_amt, spandex_cons, spandex_unit_price, spandex_total, aop_cons, aop_price, aop_total, collar_cuff_cons, collar_cuff_unit_price, collar_cuff_total, print_cons, print_unit_price, print_total, gmts_wash_dye_cons, gmts_wash_dye_price, gmts_wash_dye_total, access_price_cons, access_price_unit_price, access_price_total, zipper_cons, zipper_unit_price, zipper_total, button_cons, button_unit_price, button_total, test_cons, test_unit_price, test_total, cm_cons, cm_unit_price, cm_total, inspec_cost_cons, inspec_cost_unit_price, inspec_cost_total, freight_cons, freight_unit_price, freight_total, carrier_cost_cons, carrier_cost_unit_price, carrier_cost_total, others_column_caption, others_cost_cons, others_cost_unit_price, others_cost_total, others_column_caption2, others_cost_cons2, others_cost_unit_price2, others_cost_total2,others_column_caption3, others_cost_cons3, others_cost_unit_price3, others_cost_total3, comm_cost_cons, comm_cost_price, comm_cost_total, remarks, fact_u_price, agnt_comm, agnt_comm_tot, local_comm, local_comm_tot, final_offer_price, order_conf_price, order_conf_date, embro_cons, embro_unit_price, embro_total, uom_yarn, uom_knit_fab_purc, uom_woven_fab_purc, uom_yarn_dye_crg, uom_knit_crg, uom_dye_crg, uom_spandex, uom_aop, uom_collar_cuff, uom_print, uom_embro, uom_wash_gmts_dye, uom_access_price, uom_zipper, uom_button, uom_test, uom_cm, uom_inspec_cost, uom_freight, uom_carrier_cost, uom_others, uom_others2, uom_others3, size_range,ready_to_approved, inserted_by, insert_date, status_active, is_deleted";
		 $data_array ="(".$id.",'".$sys_no_arr[1]."','".$sys_no_arr[2]."','".$sys_no_arr[0]."',".$cbo_company_id.",".$txt_team_member.",".$cbo_buyer_name.",".$txt_price_quota_date.",".$cbo_agent.",".$txt_style_ref.",".$txt_gmts_item.",".$txt_fabrication.",".$txt_color.",".$txt_yarn_count.",".$txt_consumption_size.",".$txt_order_qty.",".$cbo_measurement_basis.",".$txt_yarn__1.",".$txt_yarn__2.",".$txt_yarn__3.",".$txt_knit_fab_purc__1.",".$txt_knit_fab_purc__2.",".$txt_knit_fab_purc__3.",".$txt_woven_fab_purc__1.",".$txt_woven_fab_purc__2.",".$txt_woven_fab_purc__3.",".$txt_yarn_dye_crg__1.",".$txt_yarn_dye_crg__2.",".$txt_yarn_dye_crg__3.",".$txt_knit_crg__1.",".$txt_knit_crg__2.",".$txt_knit_crg__3.",".$txt_dye_crg__1.",".$txt_dye_crg__2.",".$txt_dye_crg__3.",".$txt_spandex_amt__1.",".$txt_spandex__1.",".$txt_spandex__2.",".$txt_spandex__3.",".$txt_aop__1.",".$txt_aop__2.",".$txt_aop__3.",".$txt_knit_collar_cuff__1.",".$txt_knit_collar_cuff__2.",".$txt_knit_collar_cuff__3.",".$txt_print__1.",".$txt_print__2.",".$txt_print__3.",".$txt_wash_gmts_dye__1.",".$txt_wash_gmts_dye__2.",".$txt_wash_gmts_dye__3.",".$txt_access_price__1.",".$txt_access_price__2.",".$txt_access_price__3.",".$txt_zipper__1.",".$txt_zipper__2.",".$txt_zipper__3.",".$txt_button__1.",".$txt_button__2.",".$txt_button__3.",".$txt_test__1.",".$txt_test__2.",".$txt_test__3.",".$txt_cm__1.",".$txt_cm__2.",".$txt_cm__3.",".$txt_inspec_cost__1.",".$txt_inspec_cost__2.",".$txt_inspec_cost__3.",".$txt_freight__1.",".$txt_freight__2.",".$txt_freight__3.",".$txt_carrier_cost__1.",".$txt_carrier_cost__2.",".$txt_carrier_cost__3.",".$txt_others_caption.",".$txt_others__1.",".$txt_others__2.",".$txt_others__3.","		 .$txt_others_caption2.",".$txt_others2__1.",".$txt_others2__2.",".$txt_others2__3.",".$txt_others_caption3.",".$txt_others3__1.",".$txt_others3__2.",".$txt_others3__3.",".$txt_comm_cost__1.",".$txt_comm_cost__2.",".$txt_comm_cost__3.",".$txt_remarks.",".$txt_fact_u_price.",".$txt_agnt_comm.",".$txt_agnt_comm_tot.",".$txt_local_comm.",".$txt_local_comm_tot.",".$txt_final_offer_price.",".$txt_order_conf_price.",".$txt_order_conf_date.",".$txt_embro__1.",".$txt_embro__2.",".$txt_embro__3.",".$cbo_uom_yarn.",".$cbo_uom_knit_fab_purc.",".$cbo_uom_woven_fab_purc.",".$cbo_uom_yarn_dye_crg.",".$cbo_uom_knit_crg.",".$cbo_uom_dye_crg.",".$cbo_uom_spandex.",".$cbo_uom_aop.",".$cbo_uom_collar_cuff.",".$cbo_uom_print.",".$cbo_uom_embro.",".$cbo_uom_wash_gmts_dye.",".$cbo_uom_access_price.",".$cbo_uom_zipper.",".$cbo_uom_button.",".$cbo_uom_test.",".$cbo_uom_cm.",".$cbo_uom_inspec_cost.",".$cbo_uom_freight.",".$cbo_uom_carrier_cost.",".$cbo_uom_others.",".$cbo_uom_others2.",".$cbo_uom_others3.",".$txt_size_range.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 
		 
		 
		 //echo "10** insert into wo_price_quotation_v3_mst ($field_array) values $data_array "; die;
		 
		 
		 $dtls_id=return_next_id( "id", "wo_price_quotation_v3_dtls", 1 ) ;
		 $field_array_dtls="id, mst_id, garments_type, fabric_source, fabric_natu, body_length, sleeve_length, inseam_length, front_back_rise, sleev_rise_allow, chest, thigh, chest_thigh_allow, gsm, body_fabric, wastage, net_body_fabric, rib, ttl_top_bottom_cons, inserted_by, insert_date, status_active, is_deleted";
		 
		$dtlsDataArr = explode("**",$dtlsDataString);
		$data_array_dtls="";
		foreach($dtlsDataArr as $dataRow ){
			$dataArr = explode("_",$dataRow);
			if($dataArr[0] == "")
			{
				if($data_array_dtls !="")
				{
					$data_array_dtls .=",('".$dtls_id."','".$id."','".$dataArr[1]."','".$dataArr[2]."','".$dataArr[3]."','".$dataArr[4]."','".$dataArr[5]."','".$dataArr[6]."','".$dataArr[7]."','".$dataArr[8]."','".$dataArr[9]."','".$dataArr[10]."','".$dataArr[11]."','".$dataArr[12]."','".$dataArr[13]."','".$dataArr[14]."','".$dataArr[15]."','".$dataArr[16]."','".$dataArr[17]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}else{
					$data_array_dtls .="('".$dtls_id."','".$id."','".$dataArr[1]."','".$dataArr[2]."','".$dataArr[3]."','".$dataArr[4]."','".$dataArr[5]."','".$dataArr[6]."','".$dataArr[7]."','".$dataArr[8]."','".$dataArr[9]."','".$dataArr[10]."','".$dataArr[11]."','".$dataArr[12]."','".$dataArr[13]."','".$dataArr[14]."','".$dataArr[15]."','".$dataArr[16]."','".$dataArr[17]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}
				
				$dtls_id++;
			}
			
			
		}
		 
		 //echo "10** insert into wo_price_quotation_v3_mst ($field_array) values $data_array "; die;
		 //echo "10** insert into wo_price_quotation_v3_dtls ($field_array_dtls) values $data_array_dtls "; die;
		 
		 $rID=sql_insert("wo_price_quotation_v3_mst",$field_array,$data_array,0);
		 $rID1=sql_insert("wo_price_quotation_v3_dtls",$field_array_dtls,$data_array_dtls,0);
		 
		 
		//echo "10**".$rID."**".$rID1;die;
		
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");  
				echo "0**".$sys_no_arr[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$sys_no_arr[0]."**".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);  
				echo "0**".$sys_no_arr[0]."**".$id;
			}
			else{
				oci_rollback($con);  
				echo "10**".$sys_no_arr[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==1)	/*For Update*/
	{
		$insert_by_id	=return_field_value("inserted_by","wo_price_quotation_v3_mst"," id=$txt_update_id and status_active=1 and is_deleted=0 ","inserted_by");
		$insert_by_name	=return_field_value("user_name","USER_PASSWD"," id=".$insert_by_id,"user_name");
		if($insert_by_id != $_SESSION['logic_erp']['user_id']){ echo "14**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$txt_update_id)."**This Price Quotation is create by - $insert_by_name.";disconnect($con);die;}
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_update="company_id*team_member*buyer_id*quot_date*agent*style_ref*gmts_item*fabrication*color*yarn_count*cons_size*order_qty*measurment_basis*yarn_cons*yarn_unit_price*yarn_total*knit_fab_purc_cons*knit_fab_purc_price*knit_fab_purc_total*woven_fab_purc_cons*woven_fab_purc_price*woven_fab_purc_total*yarn_dye_crg_cons*yarn_dye_crg_price*yarn_dye_crg_total*knit_crg_cons*knit_crg_unit_price*knit_crg_total*dye_crg_cons*dye_crg_unit_price*dye_crg_total*spandex_amt*spandex_cons*spandex_unit_price*spandex_total*aop_cons*aop_price*aop_total*collar_cuff_cons*collar_cuff_unit_price*collar_cuff_total*print_cons*print_unit_price*print_total*gmts_wash_dye_cons*gmts_wash_dye_price*gmts_wash_dye_total*access_price_cons*access_price_unit_price*access_price_total*zipper_cons*zipper_unit_price*zipper_total*button_cons*button_unit_price*button_total*test_cons*test_unit_price*test_total*cm_cons*cm_unit_price*cm_total*inspec_cost_cons*inspec_cost_unit_price*inspec_cost_total*freight_cons*freight_unit_price*freight_total*carrier_cost_cons*carrier_cost_unit_price*carrier_cost_total*others_column_caption*others_cost_cons*others_cost_unit_price*others_cost_total*others_column_caption2*others_cost_cons2*others_cost_unit_price2*others_cost_total2*others_column_caption3*others_cost_cons3*others_cost_unit_price3*others_cost_total3*comm_cost_cons*comm_cost_price*comm_cost_total*remarks*fact_u_price*agnt_comm*agnt_comm_tot*local_comm*local_comm_tot*final_offer_price*order_conf_price*order_conf_date*embro_cons*embro_unit_price*embro_total*uom_yarn*uom_knit_fab_purc*uom_woven_fab_purc*uom_yarn_dye_crg*uom_knit_crg*uom_dye_crg*uom_spandex*uom_aop*uom_collar_cuff*uom_print*uom_embro*uom_wash_gmts_dye*uom_access_price*uom_zipper*uom_button*uom_test*uom_cm*uom_inspec_cost*uom_freight*uom_carrier_cost*uom_others*uom_others2*uom_others3*size_range*ready_to_approved*updated_by*update_date*status_active*is_deleted";
		
		 $data_array_update ="".$cbo_company_id."*".$txt_team_member."*".$cbo_buyer_name."*".$txt_price_quota_date."*".$cbo_agent."*".$txt_style_ref."*".$txt_gmts_item."*".$txt_fabrication."*".$txt_color."*".$txt_yarn_count."*".$txt_consumption_size."*".$txt_order_qty."*".$cbo_measurement_basis."*".$txt_yarn__1."*".$txt_yarn__2."*".$txt_yarn__3."*".$txt_knit_fab_purc__1."*".$txt_knit_fab_purc__2."*".$txt_knit_fab_purc__3."*".$txt_woven_fab_purc__1."*".$txt_woven_fab_purc__2."*".$txt_woven_fab_purc__3."*".$txt_yarn_dye_crg__1."*".$txt_yarn_dye_crg__2."*".$txt_yarn_dye_crg__3."*".$txt_knit_crg__1."*".$txt_knit_crg__2."*".$txt_knit_crg__3."*".$txt_dye_crg__1."*".$txt_dye_crg__2."*".$txt_dye_crg__3."*".$txt_spandex_amt__1."*".$txt_spandex__1."*".$txt_spandex__2."*".$txt_spandex__3."*".$txt_aop__1."*".$txt_aop__2."*".$txt_aop__3."*".$txt_knit_collar_cuff__1."*".$txt_knit_collar_cuff__2."*".$txt_knit_collar_cuff__3."*".$txt_print__1."*".$txt_print__2."*".$txt_print__3."*".$txt_wash_gmts_dye__1."*".$txt_wash_gmts_dye__2."*".$txt_wash_gmts_dye__3."*".$txt_access_price__1."*".$txt_access_price__2."*".$txt_access_price__3."*".$txt_zipper__1."*".$txt_zipper__2."*".$txt_zipper__3."*".$txt_button__1."*".$txt_button__2."*".$txt_button__3."*".$txt_test__1."*".$txt_test__2."*".$txt_test__3."*".$txt_cm__1."*".$txt_cm__2."*".$txt_cm__3."*".$txt_inspec_cost__1."*".$txt_inspec_cost__2."*".$txt_inspec_cost__3."*".$txt_freight__1."*".$txt_freight__2."*".$txt_freight__3."*".$txt_carrier_cost__1."*".$txt_carrier_cost__2."*".$txt_carrier_cost__3."*".$txt_others_caption."*".$txt_others__1."*".$txt_others__2."*".$txt_others__3."*".$txt_others_caption2."*".$txt_others2__1."*".$txt_others2__2."*".$txt_others2__3."*".$txt_others_caption3."*".$txt_others3__1."*".$txt_others3__2."*".$txt_others3__3."*".$txt_comm_cost__1."*".$txt_comm_cost__2."*".$txt_comm_cost__3."*".$txt_remarks."*".$txt_fact_u_price."*".$txt_agnt_comm."*".$txt_agnt_comm_tot."*".$txt_local_comm."*".$txt_local_comm_tot."*".$txt_final_offer_price."*".$txt_order_conf_price."*".$txt_order_conf_date."*".$txt_embro__1."*".$txt_embro__2."*".$txt_embro__3."*".$cbo_uom_yarn."*".$cbo_uom_knit_fab_purc."*".$cbo_uom_woven_fab_purc."*".$cbo_uom_yarn_dye_crg."*".$cbo_uom_knit_crg."*".$cbo_uom_dye_crg."*".$cbo_uom_spandex."*".$cbo_uom_aop."*".$cbo_uom_collar_cuff."*".$cbo_uom_print."*".$cbo_uom_embro."*".$cbo_uom_wash_gmts_dye."*".$cbo_uom_access_price."*".$cbo_uom_zipper."*".$cbo_uom_button."*".$cbo_uom_test."*".$cbo_uom_cm."*".$cbo_uom_inspec_cost."*".$cbo_uom_freight."*".$cbo_uom_carrier_cost."*".$cbo_uom_others."*".$cbo_uom_others2."*".$cbo_uom_others3."*".$txt_size_range."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
		 
		 
		 $dtls_id=return_next_id( "id", "wo_price_quotation_v3_dtls", 1 ) ;
		 $field_array_dtls="id, mst_id, garments_type, fabric_source, fabric_natu, body_length, sleeve_length, inseam_length, front_back_rise, sleev_rise_allow, chest, thigh, chest_thigh_allow, gsm, body_fabric, wastage, net_body_fabric, rib, ttl_top_bottom_cons, inserted_by, insert_date, status_active, is_deleted";
		 
		  $field_array_dtls_update="garments_type*fabric_source*fabric_natu*body_length*sleeve_length*inseam_length*front_back_rise*sleev_rise_allow*chest*thigh*chest_thigh_allow*gsm*body_fabric*wastage*net_body_fabric*rib*ttl_top_bottom_cons*updated_by*update_date*status_active*is_deleted";
		 
		
		$dtlsDataArr = explode("**",$dtlsDataString);
		
		$data_array_dtls="";
		
		foreach($dtlsDataArr as $dataRow ){
			$dataArr = explode("_",$dataRow);
			if($dataArr[0] != "")
			{
				$update_id_array[]=$dataArr[0];
				$data_array_dtls_update[$dataArr[0]]=explode("*",("'".$dataArr[1]."'*'".$dataArr[2]."'*'".$dataArr[3]."'*'".$dataArr[4]."'*'".$dataArr[5]."'*'".$dataArr[6]."'*'".$dataArr[7]."'*'".$dataArr[8]."'*'".$dataArr[9]."'*'".$dataArr[10]."'*'".$dataArr[11]."'*'".$dataArr[12]."'*'".$dataArr[13]."'*'".$dataArr[14]."'*'".$dataArr[15]."'*'".$dataArr[16]."'*'".$dataArr[17]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'"));
				
			}
			else
			{
				if($data_array_dtls !="")
				{
					$data_array_dtls .=",('".$dtls_id."',".$txt_update_id.",'".$dataArr[1]."','".$dataArr[2]."','".$dataArr[3]."','".$dataArr[4]."','".$dataArr[5]."','".$dataArr[6]."','".$dataArr[7]."','".$dataArr[8]."','".$dataArr[9]."','".$dataArr[10]."','".$dataArr[11]."','".$dataArr[12]."','".$dataArr[13]."','".$dataArr[14]."','".$dataArr[15]."','".$dataArr[16]."','".$dataArr[17]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}else{
					$data_array_dtls .="('".$dtls_id."',".$txt_update_id.",'".$dataArr[1]."','".$dataArr[2]."','".$dataArr[3]."','".$dataArr[4]."','".$dataArr[5]."','".$dataArr[6]."','".$dataArr[7]."','".$dataArr[8]."','".$dataArr[9]."','".$dataArr[10]."','".$dataArr[11]."','".$dataArr[12]."','".$dataArr[13]."','".$dataArr[14]."','".$dataArr[15]."','".$dataArr[16]."','".$dataArr[17]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				}
				
				$dtls_id++;
			}
		}
		
		
		//echo "10**<pre>";
		//print_r($data_array_dtls_update);die;

		$update_id=str_replace("'","",$txt_update_id);
		$rID=$rID1=$rID2=$rID3=1;
		$rID=sql_update("wo_price_quotation_v3_mst",$field_array_update,$data_array_update,"id","".$update_id."",1);		
		
		$rID1=execute_query(bulk_update_sql_statement( "wo_price_quotation_v3_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $update_id_array ));
		
		
		if($data_array_dtls != "")
		{
			$rID2=sql_insert("wo_price_quotation_v3_dtls", $field_array_dtls, $data_array_dtls,0);
		}
		
		$field_array_dtls_delete="status_active*is_deleted*updated_by*update_date";
		$data_array_dtls_delete="'2'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$deleted_id = str_replace("'","",$deleted_id_dtls);
		if($deleted_id != '')
		{
	  		$rID3=sql_multirow_update("wo_price_quotation_v3_dtls",$field_array_dtls_delete,$data_array_dtls_delete,"id","".$deleted_id."",1);
		}
		$measurement_basis = str_replace("'","",$cbo_measurement_basis);
		//echo "10**#".$rID."#**".$rID1."**".$rID2."**".$rID3;die;
		
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_id)."**".$update_id."**".$measurement_basis;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id)."**".$update_id."**".$measurement_basis;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_system_id)."**".$update_id."**".$measurement_basis;
			}else{
				oci_rollback($con);   
				echo "10**".str_replace("'","",$txt_system_id)."**".$update_id."**".$measurement_basis;
			}
		}
		disconnect($con);
		die;
	}
	
	if($operation==2)	/*For Delete*/
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_up="updated_by*update_date*status_active*is_deleted";
		$data_array_up ="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'2'*'1'";
		
		$update_id=str_replace("'","",$txt_update_id);
		$rID=sql_update("wo_price_quotation_v3_mst",$field_array_up,$data_array_up,"id","".$update_id."",1);
		$rID1=sql_update("wo_price_quotation_v3_dtls",$field_array_up,$data_array_up,"mst_id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_system_id)."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id)."**".$update_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 ){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_system_id)."**".$update_id;
			}else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_system_id)."**".$update_id;
			}
		}
		disconnect($con);
		die;
	}
}


if($action == "browse_system_number") 
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode, 1);
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(id) {
            //alert(id);
            document.getElementById('hidden_system_number').value = id;
            parent.emailwindow.hide();
        }
		function show_data() 
		{
			show_list_view(document.getElementById('cbo_company_id').value + '_' + document.getElementById('cbo_gmts_type').value + '_' + document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('cbo_agent').value + '_'+ document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value+ '_' + document.getElementById('txt_system_id').value+ '_' + document.getElementById('txt_style_ref').value+ '_' + document.getElementById('txt_team_member').value, 'show_searh_active_listview', 'searh_list_view', 'price_quotation_controller_v3', 'setFilterGrid(\'list_view\',-1)');
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">
                <table width="1020" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th style="display:none">Garment Type</th>
                            <th>Buyer</th>
                            <th>Agent</th>
                            <th>Team Member</th>
                            <th>Style Ref</th>
                            <th>System No</th>
                            <th width="210" align="center" >Date Range</th>
                            <th width="80"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                 echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) order by comp.company_name","id,company_name", 1, "-- Select --",$cbo_company_name,"load_drop_down( 'price_quotation_controller_v3', this.value,'load_drop_down_buyer','buyer_td' );load_drop_down( 'price_quotation_controller_v3', this.value,'load_drop_down_agent','agent_td' );", 0 );
                                ?>
                            </td>
                            <td style="display:none">
							<? 
                            	echo create_drop_down( "cbo_gmts_type", 100,$body_part_type,"", 1, "-- Select --",$selected,"","","1,20" );
                            ?>
                            </td>
                            <td id="buyer_td">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select --",$selected,"", 0 );
                            ?>
                            </td>
                            <td id="agent_td">
							<?	
                            	echo create_drop_down( "cbo_agent", 100, $blank_array,"", 1, "-- Select --",$selected,"", 0 );
                            ?>
                            </td>
                            <td>
                                <input name="txt_team_member" id="txt_team_member" class="text_boxes" style="width:100px" placeholder="Team Member" />
                            </td> 
                            <td>
                                <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:80px" placeholder="Style Ref" />
                            </td> 
                            <td>
                                <input name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:100px" placeholder="System No" />
                            </td> 
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:66px" placeholder="From" readonly/>-
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:66px" placeholder="To" readonly/>
                            </td>  

                            <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_data()" style="width:70px" />		
                            </td>
                        </tr>
                        <tr>                  
                            <td align="center" height="24" valign="middle" colspan="8">
                                <?php echo load_month_buttons(1); ?>
                                <!-- Hidden field here-------->
                                <input type="hidden" id="hidden_system_number" value="" />
                                <!-- ---------END------------->
                            </td>
                        </tr>  
                    </tbody>
                </table> 
            </form>
            <div align="center" valign="top" id="searh_list_view"> </div>
        </div>

    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    
	<script type="text/javascript">
		/*this is for Enter key */
		$('#searchorderfrm_2').keydown(function(event){ 
			var keyCode = (event.keyCode ? event.keyCode : event.which);   
			if (keyCode == 13) {
				show_data();
			}
		});
    </script>
    
    </html>
    <?php
}

if($action == "show_searh_active_listview") 
{
    $ex_data = explode("_", $data);
	 
	 if ($ex_data[0] == 0) {
        echo "Please Company first";
        die;
    }
	
    $buyer_name_arr = return_library_array("select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c  where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$ex_data[0]' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name", "id", "buyer_name");
    $agent_name_arr = return_library_array("select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$ex_data[0]'  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name", "id", "buyer_name");
	
	
    if ($ex_data[0] == 0) $company_id = ""; 	else $company_id 	= " and a.company_id='" . $ex_data[0] . "'";
    if ($ex_data[1] == 0) $gmts_type = ""; 		else $gmts_type 	= " and b.garments_type='" . $ex_data[1] . "'";
    if ($ex_data[2] == 0) $buyer_id = ""; 		else $buyer_id 		= " and a.buyer_id='" . $ex_data[2] . "'";
    if ($ex_data[3] == 0) $agent = ""; 			else $agent 		= " and a.agent='" . $ex_data[3] . "'";
	
    $txt_date_from 	= $ex_data[4];
    $txt_date_to 	= $ex_data[5];
	
	if ($ex_data[6] == "") $system_id_cond = ""; else $system_id_cond = " and a.system_id LIKE '%" . $ex_data[6] . "'";
	if ($ex_data[7] == "") $StyleRef = ""; 		 else $StyleRef 	  = " and a.style_ref LIKE '%" . $ex_data[7] . "%'";
    if ($ex_data[8] == "") $teamMemberCond = ""; else $teamMemberCond 	  = " and a.team_member LIKE '%" . $ex_data[8] . "%'";

    if ($db_type == 0) {/*for mysql*/
        if ($txt_date_from != "" || $txt_date_to != "") {
            $tran_date = " and a.quot_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        }
    }

    if ($db_type == 2) {/*for oracal*/
        if ($txt_date_from != "" && $txt_date_to != "") {
            $tran_date = " and a.quot_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
        }
    }
	
	$sql = "SELECT a.id, a.system_id, a.company_id, a.buyer_id, a.quot_date, a.agent, a.team_member, a.style_ref, a.gmts_item from wo_price_quotation_v3_mst a WHERE a.status_active=1 AND a.is_deleted=0 $company_id $StyleRef $buyer_id $agent $tran_date $system_id_cond $teamMemberCond  ORDER BY a.id";
	

    $arr = array(2 => $body_part_type,4 => $buyer_name_arr, 5 => $agent_name_arr);
	
    echo create_list_view("list_view","System No,Date,Style Ref,Team Member,Buyer Name,Agent Name", "150,80,120,100,120,120", "800", "300", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,buyer_id,agent", $arr, "system_id,quot_date,style_ref,team_member,buyer_id,agent", "price_quotation_controller_v3", '', '0,3,0,0,0,0');
	die;
}


if($action == "populate_information_form_data") 
{
	
	$data=explode("_",$data);
	$data_array = sql_select("SELECT id, sys_no_prefix, sys_prefix_num, system_id, company_id, team_member, buyer_id, quot_date, agent, style_ref, gmts_item, fabrication, color, yarn_count, cons_size, order_qty, measurment_basis, yarn_cons, yarn_unit_price, yarn_total, knit_fab_purc_cons, knit_fab_purc_price, knit_fab_purc_total, woven_fab_purc_cons, woven_fab_purc_price, woven_fab_purc_total, yarn_dye_crg_cons, yarn_dye_crg_price, yarn_dye_crg_total, knit_crg_cons, knit_crg_unit_price, knit_crg_total, dye_crg_cons, dye_crg_unit_price, dye_crg_total, spandex_amt, spandex_cons, spandex_unit_price, spandex_total, aop_cons, aop_price, aop_total, collar_cuff_cons, collar_cuff_unit_price, collar_cuff_total, print_cons, print_unit_price, print_total, gmts_wash_dye_cons, gmts_wash_dye_price, gmts_wash_dye_total, access_price_cons, access_price_unit_price, access_price_total, zipper_cons, zipper_unit_price, zipper_total, button_cons, button_unit_price, button_total, test_cons, test_unit_price, test_total, cm_cons, cm_unit_price, cm_total, inspec_cost_cons, inspec_cost_unit_price, inspec_cost_total, freight_cons, freight_unit_price, freight_total, carrier_cost_cons, carrier_cost_unit_price, carrier_cost_total, others_column_caption, others_cost_cons, others_cost_unit_price, others_cost_total, comm_cost_cons, comm_cost_price, comm_cost_total, remarks, fact_u_price, agnt_comm, agnt_comm_tot, local_comm, local_comm_tot, final_offer_price, order_conf_price, order_conf_date, embro_cons, embro_unit_price, embro_total, uom_yarn, uom_knit_fab_purc, uom_woven_fab_purc, uom_yarn_dye_crg, uom_knit_crg, uom_dye_crg, uom_spandex, uom_aop, uom_collar_cuff, uom_print, uom_embro, uom_wash_gmts_dye, uom_access_price, uom_zipper, uom_button, uom_test, uom_cm, uom_inspec_cost, uom_freight, uom_carrier_cost, uom_others, uom_others2, uom_others3, size_range,ready_to_approved,is_approved, others_column_caption2, others_cost_cons2, others_cost_unit_price2, others_cost_total2, others_column_caption3, others_cost_cons3, others_cost_unit_price3, others_cost_total3 FROM wo_price_quotation_v3_mst WHERE id='$data[0]' AND status_active = 1 AND is_deleted = 0");
	 
	
	if(count($data_array)== 0) die;
	foreach ($data_array as $row) 
	{
		
		$sub_total = $row[csf("yarn_total")]+$row[csf("knit_fab_purc_total")]+$row[csf("woven_fab_purc_total")]+$row[csf("yarn_dye_crg_total")]+$row[csf("knit_crg_total")]+$row[csf("dye_crg_total")]+$row[csf("spandex_total")]+$row[csf("aop_total")]+$row[csf("collar_cuff_total")]+$row[csf("print_total")]+$row[csf("embro_total")]+$row[csf("gmts_wash_dye_total")]+$row[csf("access_price_total")]+$row[csf("zipper_total")]+$row[csf("button_total")]+$row[csf("test_total")]+$row[csf("cm_total")]+$row[csf("inspec_cost_total")]+$row[csf("freight_total")]+$row[csf("carrier_cost_total")]+$row[csf("others_cost_total")]+$row[csf("others_cost_total2")]+$row[csf("others_cost_total3")];
		
		//$factory_cost_total=$sub_total+$row[csf("comm_cost_total")];
		
		echo "load_drop_down('requires/price_quotation_controller_v3','" . $row[csf("company_id")] . "','load_drop_down_buyer','buyer_td' );\n";
		echo "load_drop_down('requires/price_quotation_controller_v3','" . $row[csf("company_id")] . "','load_drop_down_agent','agent_td' );\n";
		
		echo "document.getElementById('txt_system_id').value 		= '" . $row[csf("system_id")] . "';\n";
		echo "document.getElementById('txt_update_id').value 		= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 		= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('txt_team_member').value 		= '" . $row[csf("team_member")] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 		= '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('cbo_agent').value 			= '" . $row[csf("agent")] . "';\n";
		echo "document.getElementById('txt_price_quota_date').value = '" .change_date_format($row[csf("quot_date")], 'dd-mm-yyyy'). "';\n";
		echo "document.getElementById('txt_style_ref').value 		= '" . $row[csf("style_ref")] . "';\n";
		echo "document.getElementById('txt_gmts_item').value 		= '" . $row[csf("gmts_item")] . "';\n";
		echo "document.getElementById('txt_fabrication').value 		= '" . $row[csf("fabrication")] . "';\n";
		echo "document.getElementById('txt_color').value 			= '" . $row[csf("color")] . "';\n";
		echo "document.getElementById('txt_yarn_count').value 		= '" . $row[csf("yarn_count")] . "';\n";
		echo "document.getElementById('txt_consumption_size').value = '" . $row[csf("cons_size")] . "';\n";
		echo "document.getElementById('txt_order_qty').value 		= '" . $row[csf("order_qty")] . "';\n";
		echo "document.getElementById('cbo_measurement_basis').value 		= '" . $row[csf("measurment_basis")] . "';\n";
		echo "document.getElementById('txt_remarks').value 				= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 				= '" . $row[csf("ready_to_approved")] . "';\n";
		
		echo "document.getElementById('txt_size_range').value 			= '" . $row[csf("size_range")] . "';\n";
		
		echo "document.getElementById('txt_yarn__1').value 			= '" . $row[csf("yarn_cons")] . "';\n";
		echo "document.getElementById('txt_yarn__2').value 			= '" . $row[csf("yarn_unit_price")] . "';\n";
		echo "document.getElementById('txt_yarn__3').value 			= '" . number_format($row[csf("yarn_total")],4) . "';\n";
		
		echo "document.getElementById('txt_knit_fab_purc__1').value 		= '" . $row[csf("knit_fab_purc_cons")] . "';\n";
		echo "document.getElementById('txt_knit_fab_purc__2').value 		= '" . $row[csf("knit_fab_purc_price")] . "';\n";
		echo "document.getElementById('txt_knit_fab_purc__3').value 		= '" . number_format($row[csf("knit_fab_purc_total")],4) . "';\n";
		
		echo "document.getElementById('txt_woven_fab_purc__1').value 		= '" . $row[csf("woven_fab_purc_cons")] . "';\n";
		echo "document.getElementById('txt_woven_fab_purc__2').value 		= '" . $row[csf("woven_fab_purc_price")] . "';\n";
		echo "document.getElementById('txt_woven_fab_purc__3').value 		= '" . number_format($row[csf("woven_fab_purc_total")],4) . "';\n";
		
		echo "document.getElementById('txt_yarn_dye_crg__1').value 		= '" . $row[csf("yarn_dye_crg_cons")] . "';\n";
		echo "document.getElementById('txt_yarn_dye_crg__2').value 		= '" . $row[csf("yarn_dye_crg_price")] . "';\n";
		echo "document.getElementById('txt_yarn_dye_crg__3').value 		= '" . number_format($row[csf("yarn_dye_crg_total")],4) . "';\n";
		
		echo "document.getElementById('txt_knit_crg__1').value 		= '" . $row[csf("knit_crg_cons")] . "';\n";
		echo "document.getElementById('txt_knit_crg__2').value 		= '" . $row[csf("knit_crg_unit_price")] . "';\n";
		echo "document.getElementById('txt_knit_crg__3').value 		= '" . number_format($row[csf("knit_crg_total")],4) . "';\n";
		
		echo "document.getElementById('txt_dye_crg__1').value 		= '" . $row[csf("dye_crg_cons")] . "';\n";
		echo "document.getElementById('txt_dye_crg__2').value 		= '" . $row[csf("dye_crg_unit_price")] . "';\n";
		echo "document.getElementById('txt_dye_crg__3').value 		= '" . number_format($row[csf("dye_crg_total")],4) . "';\n";
		
		echo "document.getElementById('txt_spandex__1').value 		= '" . number_format($row[csf("spandex_cons")],2) . "';\n";
		echo "document.getElementById('txt_spandex__2').value 		= '" . $row[csf("spandex_unit_price")] . "';\n";
		echo "document.getElementById('txt_spandex__3').value 		= '" . number_format($row[csf("spandex_total")],4) . "';\n";
		echo "document.getElementById('txt_spandex_amt__1').value 		= '" . $row[csf("spandex_amt")] . "';\n";
		
		echo "document.getElementById('txt_aop__1').value 		= '" . $row[csf("aop_cons")] . "';\n";
		echo "document.getElementById('txt_aop__2').value 		= '" . $row[csf("aop_price")] . "';\n";
		echo "document.getElementById('txt_aop__3').value 		= '" . number_format($row[csf("aop_total")],4) . "';\n";
		
		echo "document.getElementById('txt_knit_collar_cuff__1').value 		= '" . $row[csf("collar_cuff_cons")] . "';\n";
		echo "document.getElementById('txt_knit_collar_cuff__2').value 		= '" . $row[csf("collar_cuff_unit_price")] . "';\n";
		echo "document.getElementById('txt_knit_collar_cuff__3').value 		= '" . number_format($row[csf("collar_cuff_total")],4) . "';\n";
		
		echo "document.getElementById('txt_print__1').value 		= '" . $row[csf("print_cons")] . "';\n";
		echo "document.getElementById('txt_print__2').value 		= '" . $row[csf("print_unit_price")] . "';\n";
		echo "document.getElementById('txt_print__3').value 		= '" . number_format($row[csf("print_total")],4) . "';\n";
		
		echo "document.getElementById('txt_embro__1').value 		= '" . $row[csf("embro_cons")] . "';\n";
		echo "document.getElementById('txt_embro__2').value 		= '" . $row[csf("embro_unit_price")] . "';\n";
		echo "document.getElementById('txt_embro__3').value 		= '" . number_format($row[csf("embro_total")],4) . "';\n";
				
		echo "document.getElementById('txt_wash_gmts_dye__1').value 		= '" . $row[csf("gmts_wash_dye_cons")] . "';\n";
		echo "document.getElementById('txt_wash_gmts_dye__2').value 		= '" . $row[csf("gmts_wash_dye_price")] . "';\n";
		echo "document.getElementById('txt_wash_gmts_dye__3').value 		= '" . number_format($row[csf("gmts_wash_dye_total")],4) . "';\n";
						
		echo "document.getElementById('txt_access_price__1').value 		= '" . $row[csf("access_price_cons")] . "';\n";
		echo "document.getElementById('txt_access_price__2').value 		= '" . $row[csf("access_price_unit_price")] . "';\n";
		echo "document.getElementById('txt_access_price__3').value 		= '" . number_format($row[csf("access_price_total")],4) . "';\n";
		
		echo "document.getElementById('txt_zipper__1').value 		= '" . $row[csf("zipper_cons")] . "';\n";
		echo "document.getElementById('txt_zipper__2').value 		= '" . $row[csf("zipper_unit_price")] . "';\n";
		echo "document.getElementById('txt_zipper__3').value 		= '" . number_format($row[csf("zipper_total")],4) . "';\n";
		
		echo "document.getElementById('txt_button__1').value 		= '" . $row[csf("button_cons")] . "';\n";
		echo "document.getElementById('txt_button__2').value 		= '" . $row[csf("button_unit_price")] . "';\n";
		echo "document.getElementById('txt_button__3').value 		= '" . number_format($row[csf("button_total")],4) . "';\n";
		
		
		echo "document.getElementById('txt_test__1').value 		= '" . $row[csf("test_cons")] . "';\n";
		echo "document.getElementById('txt_test__2').value 		= '" . $row[csf("test_unit_price")] . "';\n";
		echo "document.getElementById('txt_test__3').value 		= '" . number_format($row[csf("test_total")],4) . "';\n";
		
		echo "document.getElementById('txt_cm__1').value 		= '" . $row[csf("cm_cons")] . "';\n";
		echo "document.getElementById('txt_cm__2').value 		= '" . $row[csf("cm_unit_price")] . "';\n";
		echo "document.getElementById('txt_cm__3').value 		= '" . number_format($row[csf("cm_total")],4) . "';\n";
		
		echo "document.getElementById('txt_inspec_cost__1').value 		= '" . $row[csf("inspec_cost_cons")] . "';\n";
		echo "document.getElementById('txt_inspec_cost__2').value 		= '" . $row[csf("inspec_cost_unit_price")] . "';\n";
		echo "document.getElementById('txt_inspec_cost__3').value 		= '" . number_format($row[csf("inspec_cost_total")],4) . "';\n";
		
		echo "document.getElementById('txt_freight__1').value 		= '" . $row[csf("freight_cons")] . "';\n";
		echo "document.getElementById('txt_freight__2').value 		= '" . $row[csf("freight_unit_price")] . "';\n";
		echo "document.getElementById('txt_freight__3').value 		= '" . number_format($row[csf("freight_total")],4) . "';\n";
		
		echo "document.getElementById('txt_carrier_cost__1').value 		= '" . $row[csf("carrier_cost_cons")] . "';\n";
		echo "document.getElementById('txt_carrier_cost__2').value 		= '" . $row[csf("carrier_cost_unit_price")] . "';\n";
		echo "document.getElementById('txt_carrier_cost__3').value 		= '" . number_format($row[csf("carrier_cost_total")],4) . "';\n";
		
		echo "document.getElementById('txt_others_caption').value 		= '" . $row[csf("others_column_caption")] . "';\n";
		echo "document.getElementById('txt_others__1').value 		= '" . $row[csf("others_cost_cons")] . "';\n";
		echo "document.getElementById('txt_others__2').value 		= '" . $row[csf("others_cost_unit_price")] . "';\n";
		echo "document.getElementById('txt_others__3').value 		= '" . number_format($row[csf("others_cost_total")],4) . "';\n";
		
		echo "document.getElementById('txt_others_caption2').value 		= '" . $row[csf("others_column_caption2")] . "';\n";
		echo "document.getElementById('txt_others2__1').value 		= '" . $row[csf("others_cost_cons2")] . "';\n";
		echo "document.getElementById('txt_others2__2').value 		= '" . $row[csf("others_cost_unit_price2")] . "';\n";
		echo "document.getElementById('txt_others2__3').value 		= '" . number_format($row[csf("others_cost_total2")],4) . "';\n";
		
		
		echo "document.getElementById('txt_others_caption3').value 		= '" . $row[csf("others_column_caption3")] . "';\n";
		echo "document.getElementById('txt_others3__1').value 		= '" . $row[csf("others_cost_cons3")] . "';\n";
		echo "document.getElementById('txt_others3__2').value 		= '" . $row[csf("others_cost_unit_price3")] . "';\n";
		echo "document.getElementById('txt_others3__3').value 		= '" . number_format($row[csf("others_cost_total3")],4) . "';\n";
		
		
		echo "document.getElementById('txt_sub_total').value 		= '" . number_format($sub_total,4) . "';\n";
		
		echo "document.getElementById('txt_comm_cost__1').value 		= '" . number_format($row[csf("comm_cost_cons")],2) . "';\n";
		echo "document.getElementById('txt_comm_cost__2').value 		= '" . $row[csf("comm_cost_price")] . "';\n";
		echo "document.getElementById('txt_comm_cost__3').value 		= '" . number_format($row[csf("comm_cost_total")],4) . "';\n";
				
		echo "document.getElementById('txt_fact_u_price').value 		= '" .number_format($row[csf("fact_u_price")],4) . "';\n";

		echo "document.getElementById('txt_agnt_comm').value 			= '" . number_format($row[csf("agnt_comm")],2) . "';\n";
		echo "document.getElementById('txt_local_comm').value 			= '" . number_format($row[csf("local_comm")],2) . "';\n";
		echo "document.getElementById('txt_agnt_comm_tot').value 			= '" . number_format($row[csf("agnt_comm_tot")],4) . "';\n";
		echo "document.getElementById('txt_local_comm_tot').value 			= '" . number_format($row[csf("local_comm_tot")],4) . "';\n";
		
		echo "document.getElementById('txt_final_offer_price').value 	= '" . $row[csf("final_offer_price")] . "';\n";
		echo "document.getElementById('txt_order_conf_price').value 	= '" . $row[csf("order_conf_price")] . "';\n";
		echo "document.getElementById('txt_order_conf_date').value 		= '" . change_date_format($row[csf("order_conf_date")], 'dd-mm-yyyy') . "';\n";
		echo "document.getElementById('is_approved').value 				= '" . $row[csf("is_approved")] . "';\n";
		
		
		echo "document.getElementById('cbo_uom_yarn').value 		= '" . $row[csf("uom_yarn")] . "';\n";
		echo "document.getElementById('cbo_uom_knit_fab_purc').value 		= '" . $row[csf("uom_knit_fab_purc")] . "';\n";
		echo "document.getElementById('cbo_uom_woven_fab_purc').value 		= '" . $row[csf("uom_woven_fab_purc")] . "';\n";
		echo "document.getElementById('cbo_uom_yarn_dye_crg').value 		= '" . $row[csf("uom_yarn_dye_crg")] . "';\n";
		echo "document.getElementById('cbo_uom_knit_crg').value 		= '" . $row[csf("uom_knit_crg")] . "';\n";
		echo "document.getElementById('cbo_uom_dye_crg').value 		= '" . $row[csf("uom_dye_crg")] . "';\n";
		echo "document.getElementById('cbo_uom_spandex').value 		= '" . $row[csf("uom_spandex")] . "';\n";
		echo "document.getElementById('cbo_uom_aop').value 		= '" . $row[csf("uom_aop")] . "';\n";
		echo "document.getElementById('cbo_uom_collar_cuff').value 		= '" . $row[csf("uom_collar_cuff")] . "';\n";
		echo "document.getElementById('cbo_uom_print').value 		= '" . $row[csf("uom_print")] . "';\n";
		echo "document.getElementById('cbo_uom_embro').value 		= '" . $row[csf("uom_embro")] . "';\n";
		echo "document.getElementById('cbo_uom_wash_gmts_dye').value 		= '" . $row[csf("uom_wash_gmts_dye")] . "';\n";
		echo "document.getElementById('cbo_uom_access_price').value 		= '" . $row[csf("uom_access_price")] . "';\n";
		echo "document.getElementById('cbo_uom_zipper').value 		= '" . $row[csf("uom_zipper")] . "';\n";
		echo "document.getElementById('cbo_uom_button').value 		= '" . $row[csf("uom_button")] . "';\n";
		echo "document.getElementById('cbo_uom_test').value 		= '" . $row[csf("uom_test")] . "';\n";
		echo "document.getElementById('cbo_uom_cm').value 		= '" . $row[csf("uom_cm")] . "';\n";
		echo "document.getElementById('cbo_uom_inspec_cost').value 		= '" . $row[csf("uom_inspec_cost")] . "';\n";
		echo "document.getElementById('cbo_uom_freight').value 		= '" . $row[csf("uom_freight")] . "';\n";
		echo "document.getElementById('cbo_uom_carrier_cost').value 		= '" . $row[csf("uom_carrier_cost")] . "';\n";
		echo "document.getElementById('cbo_uom_others').value 		= '" . $row[csf("uom_others")] . "';\n";
		echo "document.getElementById('cbo_uom_others2').value 		= '" . $row[csf("uom_others2")] . "';\n";
		echo "document.getElementById('cbo_uom_others3').value 		= '" . $row[csf("uom_others3")] . "';\n";
		
		echo "document.getElementById('txt_factory_cost').value 		= '" . number_format(($sub_total + $row[csf("comm_cost_total")]),4) . "';\n";
		
		
		
		
		echo "set_button_status(1, permission, 'fnc_price_quotation_entry',1,0);\n";
	}
}


if($action == "fab_cons_dtls_data") 
{
	
	$data=explode("_",$data);
	$data_array = sql_select("SELECT id, mst_id, garments_type, fabric_source, fabric_natu, body_length, sleeve_length, inseam_length, front_back_rise, sleev_rise_allow, chest, thigh, chest_thigh_allow, gsm, body_fabric, wastage, net_body_fabric, rib, ttl_top_bottom_cons FROM wo_price_quotation_v3_dtls WHERE mst_id='$data[0]' AND status_active = 1 AND is_deleted = 0");
	 
	if(count($data_array)== 0) die;
	$dtlsDataArr = array();
	foreach ($data_array as $row) 
	{
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['mst_id']=$row[csf('mst_id')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['garments_type']=$row[csf('garments_type')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['fabric_source']=$row[csf('fabric_source')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['fabric_natu']=$row[csf('fabric_natu')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['body_length']=$row[csf('body_length')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['sleeve_length']=$row[csf('sleeve_length')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['inseam_length']=$row[csf('inseam_length')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['front_back_rise']=$row[csf('front_back_rise')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['sleev_rise_allow']=$row[csf('sleev_rise_allow')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['chest']=$row[csf('chest')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['thigh']=$row[csf('thigh')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['chest_thigh_allow']=$row[csf('chest_thigh_allow')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['body_fabric']=$row[csf('body_fabric')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['wastage']=$row[csf('wastage')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['net_body_fabric']=$row[csf('net_body_fabric')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['rib']=$row[csf('rib')];
		$dtlsDataArr[$row[csf('garments_type')]][$row[csf('id')]]['ttl_top_bottom_cons']=$row[csf('ttl_top_bottom_cons')];
	}
	
	$tble_body_top="";
	$tble_body_bottom="";
	
	$subTotTop = 0;
	$subTotBottom = 0;
	
	$i=1;
	$j=1;
	foreach($dtlsDataArr as $gmtsType => $gmtsTypeData)
	{
		if($gmtsType == 1)
		{
			
			foreach($gmtsTypeData as $dtlsId => $dtlsDataArr)
			{
				$subTotTop += $dtlsDataArr["ttl_top_bottom_cons"];
				
				$tble_body_top .='<tr id="fabTr_'.$i.'"><td>'.create_drop_down( "cboGmtsType_$i", 70, $body_part_type,"",0, "-- Select --",$dtlsDataArr["garments_type"],"","","1","","","","","","cboGmtsType[]" ).'
				</td>
				<td>'.create_drop_down( "cboFabricSource_$i",100,$fabric_source,"",2,"-- Select --",$dtlsDataArr["fabric_source"],"", "", "1,2","","","","","","cboFabricSource[]").'</td>
				<td>'.create_drop_down( "cboFabricNatu_$i",130,$item_category,"",2,"-- Select --",$dtlsDataArr["fabric_natu"],"","", "2,3","","","","","","cboFabricNatu[]").'</td>
				<td>
				<input type="text" name="txtBodyLength[]" id="txtBodyLength_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr["body_length"].'" style="width:45px"  placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtSleeveLength[]" id="txtSleeveLength_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr["sleeve_length"].'" style="width:45px" placeholder="Write" />
				</td>
				<td style="display:none"><input type="text" name="txtInseamLength[]" id="txtInseamLength_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr["inseam_length"].'" style="width:45px" placeholder="Write" /></td>
				<td style="display:none"><input type="text" name="txtFrontBackRise[]" id="txtFrontBackRise_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr["front_back_rise"].'" style="width:45px" placeholder="Write" /></td>
				<td>
				<input type="text" name="txtSleevAllow[]" id="txtSleevAllow_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr["sleev_rise_allow"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtChest[]" id="txtChest_'.$i.'"  onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )"class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr["chest"].'" style="width: 45px;" placeholder="Write" />
				</td>
				<td style="display:none">
				<input type="text" name="txtThigh[]" id="txtThigh_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr["thigh"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtChestAllow[]" id="txtChestAllow_'.$i.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr["chest_thigh_allow"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtGsm[]" id="txtGsm_'.$i.'" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" value="'.$dtlsDataArr["gsm"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtBodyFabric[]" id="txtBodyFabric_'.$i.'" class="text_boxes_numeric is_cad_basis_enable"  onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )"  disabled  value="'.$dtlsDataArr["body_fabric"].'" style="width:65px" />
				</td>
				<td>
				<input type="text" name="txtWastage[]" id="txtWastage_'.$i.'" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" value="'.$dtlsDataArr["wastage"].'" style="width:45px"  placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtNetBodyFabric[]" id="txtNetBodyFabric_'.$i.'" class="text_boxes_numeric"  disabled value="'.number_format($dtlsDataArr["net_body_fabric"],4).'" style="width:65px"/>
				</td>
				<td>
				<input type="text" name="txtRib[]" id="txtRib_'.$i.'" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$i.' )" value="'.$dtlsDataArr["rib"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtTtlTopCons[]" id="txtTtlTopCons_'.$i.'" class="text_boxes_numeric" disabled value="'.number_format($dtlsDataArr["ttl_top_bottom_cons"],4).'" style="width:65px"  />
				</td>
				<td>
				<input type="button" name="btnadd[]" id="btnadd_'.$i.'" value="+" class="formbutton" onClick="add_break_down_tr('.$i.')" style="width:35px"/>
				<input type="button" name="decrease[]" id="decrease_'.$i.'" value="-" class="formbutton" onClick="fn_deleteRow('.$i.')" style="width:35px"/>       
				<input type="hidden" name="UpdateIdDtls[]" id="UpdateIdDtls_'.$i.'" value="'.$dtlsId.'" class="text_boxes" style="width:200px" />
				</td></tr>';
				$i++;
			}
		}
		else
		{
			foreach($gmtsTypeData as $dtlsId2 => $dtlsDataArr2)
			{
				
				$subTotBottom += $dtlsDataArr2["ttl_top_bottom_cons"];
				$tble_body_bottom .='<tr id="fabBottomTr_'.$j.'"><td>'.create_drop_down( "cboGmtsType_$j", 70, $body_part_type,"",0, "-- Select --",$dtlsDataArr2["garments_type"],"","","20","","","","","","cboGmtsType[]" ).'
				</td>
				<td>'.create_drop_down( "cboFabricSource_$j",100,$fabric_source,"",2,"-- Select --",$dtlsDataArr2["fabric_source"],"", "", "1,2","","","","","","cboFabricSource[]").'</td>
				<td>'.create_drop_down( "cboFabricNatu_$j",130,$item_category,"",2,"-- Select --",$dtlsDataArr2["fabric_natu"],"","", "2,3","","","","","","cboFabricNatu[]").'</td>
				<td style="display:none">
				<input type="text" name="txtBodyLength[]" id="txtBodyLength_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr2["body_length"].'" style="width:45px"  placeholder="Write" />
				</td>
				<td style="display:none">
				<input type="text" name="txtSleeveLength[]" id="txtSleeveLength_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr2["sleeve_length"].'" style="width:45px" placeholder="Write" />
				</td>
				<td><input type="text" name="txtInseamLength[]" id="txtInseamLength_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr2["inseam_length"].'" style="width:45px" placeholder="Write" /></td>
				<td><input type="text" name="txtFrontBackRise[]" id="txtFrontBackRise_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr2["front_back_rise"].'" style="width:45px" placeholder="Write" /></td>
				<td>
				<input type="text" name="txtSleevAllow[]" id="txtSleevAllow_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr2["sleev_rise_allow"].'" style="width:45px" placeholder="Write" />
				</td>
				<td style="display:none">
				<input type="text" name="txtChest[]" id="txtChest_'.$j.'"  onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )"class="text_boxes_numeric is_cad_basis" value="'.$dtlsDataArr2["chest"].'" style="width: 45px;" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtThigh[]" id="txtThigh_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr2["thigh"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtChestAllow[]" id="txtChestAllow_'.$j.'" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" class="text_boxes_numeric is_cad_basis"  value="'.$dtlsDataArr2["chest_thigh_allow"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtGsm[]" id="txtGsm_'.$j.'" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" value="'.$dtlsDataArr2["gsm"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtBodyFabric[]" id="txtBodyFabric_'.$j.'" class="text_boxes_numeric is_cad_basis_enable"  onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )"  disabled  value="'.$dtlsDataArr2["body_fabric"].'" style="width:65px" />
				</td>
				<td>
				<input type="text" name="txtWastage[]" id="txtWastage_'.$j.'" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" value="'.$dtlsDataArr2["wastage"].'" style="width:45px"  placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtNetBodyFabric[]" id="txtNetBodyFabric_'.$j.'" class="text_boxes_numeric" readonly disabled value="'.number_format($dtlsDataArr2["net_body_fabric"],4).'" style="width:65px"/>
				</td>
				<td>
				<input type="text" name="txtRib[]" id="txtRib_'.$j.'" class="text_boxes_numeric" onBlur="calculate_fabric_cons( $(this).closest(\'table\').attr(\'id\'),'.$j.' )" value="'.$dtlsDataArr2["rib"].'" style="width:45px" placeholder="Write" />
				</td>
				<td>
				<input type="text" name="txtTtlTopCons[]" id="txtTtlTopCons_'.$j.'" class="text_boxes_numeric" disabled value="'.number_format($dtlsDataArr2["ttl_top_bottom_cons"],4).'" style="width:65px"  />
				</td>
				<td>
				<input type="button" name="btnaddBottom[]" id="btnaddBottom_'.$j.'" value="+" class="formbutton" onClick="add_break_down_tr_bottom('.$j.')" style="width:35px"/>
				<input type="button" name="decreaseBottom[]" id="decreaseBottom_'.$j.'" value="-" class="formbutton" onClick="fn_deleteRow_bottom('.$j.')" style="width:35px"/>       
				<input type="hidden" name="UpdateIdDtls[]" id="UpdateIdDtls_'.$j.'" value="'.$dtlsId2.'"   class="text_boxes" style="width:200px" />
				</td></tr>';
				
				$j++;
			}
		}
	}
	
	
	echo $tble_body_top."__".$tble_body_bottom."__".$subTotTop."__".$subTotBottom."__".($subTotTop+$subTotBottom);
	die;
	
	
}


if($action == "top_botton_report")
{
	extract($_REQUEST);
	echo load_html_head_contents("Price Quotation", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
    $buyer_name_arr = return_library_array("select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c  where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$data[0]' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name", "id", "buyer_name");
	
    $agent_name_arr = return_library_array("select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name", "id", "buyer_name");
	
	$company_library = return_library_array("select id,company_name  from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	
	
	$sql = "SELECT a.id, a.sys_no_prefix, a.sys_prefix_num, a.system_id, a.company_id, a.team_member, a.buyer_id, a.quot_date, a.agent, a.style_ref, a.gmts_item, a.fabrication, a.color, a.yarn_count, a.cons_size, a.order_qty, a.measurment_basis, a.yarn_cons, a.yarn_unit_price, a.yarn_total, a.knit_fab_purc_cons, a.knit_fab_purc_price, a.knit_fab_purc_total, a.woven_fab_purc_cons, a.woven_fab_purc_price, a.woven_fab_purc_total, a.yarn_dye_crg_cons, a.yarn_dye_crg_price, a.yarn_dye_crg_total, a.knit_crg_cons, a.knit_crg_unit_price, a.knit_crg_total, a.dye_crg_cons, a.dye_crg_unit_price, a.dye_crg_total, a.spandex_amt, a.spandex_cons, a.spandex_unit_price, a.spandex_total, a.aop_cons, a.aop_price, a.aop_total, a.collar_cuff_cons, a.collar_cuff_unit_price, a.collar_cuff_total, a.print_cons, a.print_unit_price, a.print_total, a.gmts_wash_dye_cons, a.gmts_wash_dye_price, a.gmts_wash_dye_total, a.access_price_cons, a.access_price_unit_price, a.access_price_total, a.zipper_cons, a.zipper_unit_price, a.zipper_total, a.button_cons, a.button_unit_price, a.button_total, a.test_cons, a.test_unit_price, a.test_total, a.cm_cons, a.cm_unit_price, a.cm_total, a.inspec_cost_cons, a.inspec_cost_unit_price, a.inspec_cost_total, a.freight_cons, a.freight_unit_price, a.freight_total, a.carrier_cost_cons, a.carrier_cost_unit_price, a.carrier_cost_total, a.others_column_caption, a.others_cost_cons, a.others_cost_unit_price, a.others_cost_total, a.comm_cost_cons, a.comm_cost_price, a.comm_cost_total, a.remarks, a.fact_u_price, a.agnt_comm, a.agnt_comm_tot, a.local_comm, a.local_comm_tot, a.final_offer_price, a.order_conf_price, a.order_conf_date, a.embro_cons, a.embro_unit_price, a.embro_total, a.uom_yarn, a.uom_knit_fab_purc, a.uom_woven_fab_purc, a.uom_yarn_dye_crg, a.uom_knit_crg, a.uom_dye_crg, a.uom_spandex, a.uom_aop, a.uom_collar_cuff, a.uom_print, a.uom_embro, a.uom_wash_gmts_dye, a.uom_access_price, a.uom_zipper, a.uom_button, a.uom_test, a.uom_cm, a.uom_inspec_cost, a.uom_freight, a.uom_carrier_cost, a.uom_others, a.uom_others2, a.uom_others3, a.size_range, a.others_column_caption2, a.others_cost_cons2, a.others_cost_unit_price2, a.others_cost_total2, a.others_column_caption3, a.others_cost_cons3, a.others_cost_unit_price3, a.others_cost_total3, 
	b.id as dtls_id,a.is_approved,  b.garments_type, b.fabric_source, b.fabric_natu, b.body_length, b.sleeve_length, b.inseam_length, b.front_back_rise, b.sleev_rise_allow, b.chest, b.thigh, b.chest_thigh_allow, b.gsm, b.body_fabric, b.wastage, b.net_body_fabric, b.rib, b.ttl_top_bottom_cons
	FROM wo_price_quotation_v3_mst a, wo_price_quotation_v3_dtls b where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	
	 
	
	
	//echo $sql;die;
	$result = sql_select($sql);
	$dtlsDataArray =array();
	foreach($result as $row)
	{
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_source']=$row[csf('fabric_source')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_natu']=$row[csf('fabric_natu')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_length']=$row[csf('body_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleeve_length']=$row[csf('sleeve_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['front_back_rise']=$row[csf('front_back_rise')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleev_rise_allow']=$row[csf('sleev_rise_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest']=$row[csf('chest')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['thigh']=$row[csf('thigh')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest_thigh_allow']=$row[csf('chest_thigh_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['gsm']=$row[csf('gsm')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_fabric']=$row[csf('body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['wastage']=$row[csf('wastage')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['net_body_fabric']=$row[csf('net_body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['rib']=$row[csf('rib')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['ttl_top_bottom_cons']=$row[csf('ttl_top_bottom_cons')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['inseam_length']=$row[csf('inseam_length')];
	}
	
	
	
	
	

	$sub_total = $result[0][csf('yarn_total')]+$result[0][csf('knit_fab_purc_total')]+$result[0][csf('woven_fab_purc_total')]+$result[0][csf('yarn_dye_crg_total')]+$result[0][csf('knit_crg_total')]+$result[0][csf('dye_crg_total')]+$result[0][csf('spandex_total')]+$result[0][csf('aop_total')]+$result[0][csf('collar_cuff_total')]+$result[0][csf('print_total')]+$result[0][csf('embro_total')]+$result[0][csf('gmts_wash_dye_total')]+$result[0][csf('access_price_total')]+$result[0][csf('zipper_total')]+$result[0][csf('button_total')]+$result[0][csf('test_total')]+$result[0][csf('cm_total')]+$result[0][csf('inspec_cost_total')]+$result[0][csf('freight_total')]+$result[0][csf('carrier_cost_total')]+$result[0][csf('others_cost_total')]+$result[0][csf('others_cost_total2')]+$result[0][csf('others_cost_total3')];
	
	$tot_factory_cost =$sub_total+$result[0][csf('comm_cost_total')];
		
	$measurement_basis_arr = array(1=>"Cad Bassis", 2=>"Measurement Basis");	
		
        ?> 
	<div align="left">
        <div style="width:210mm;">
            <table cellspacing="0" border="0" style="width:210mm; margin-right:-10px;">
                <tr class="form_caption">
                    <?
                    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                    ?>
                    <td rowspan="2" align="left" width="50">
					<?
                    foreach ($data_array as $img_row) 
                    {
                        ?>
                        <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle"/>
                        <?
                    }
					
                    ?>
                    </td>
                    <td colspan="8" align="center" style="font-size:25px;">
                        <strong> <? echo $company_library[$data[0]]; ?></strong>
                        
                        
                    </td>
                    <td rowspan="2" align="left" width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="8" align="center" style="font-size:18px">
                   		 <? echo show_company($data[0], '', array('city')); ?>
                         <br>
                    	<strong><u> Price Quotation</u></strong>
                    </td>
                </tr>
                <?
                $msg = "";
                if($result[0][csf('is_approved')] != 0)
                {
                	$msg = ($result[0][csf('is_approved')] == 1) ? "This Quotation is approved!" : "This Quotation is partial approved!";
	                ?>
	                <tr>
	                	<td colspan="10" align="center" style="font-size:16px;color: red;"> <? echo $msg;?> </td>
	                </tr>
	                <?
            	}
                ?>
            </table>
        </div>
        <br>
		<div style="width:210mm;">
            <table style="width:210mm; text-align:center; font-size:13px;" cellspacing="0" border="1" rules="all" class="rpt_table">
            	<tr>
                	<td colspan="14" bgcolor="#dddddd" align="left"><b>System No: <? echo $result[0][csf('system_id')]; ?></b></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Buyer</td>
                    <td align="left"><? echo  $buyer_name_arr[$result[0][csf('buyer_id')]]; ?></td>
                    <td colspan="2" align="left">Consumption Basis</td>
                    <td colspan="4" align="left"><? echo $measurement_basis_arr[$result[0][csf('measurment_basis')]]; ?></td>
                    <td colspan="2" align="left">Date</td>
                    <td colspan="3" align="left"><? echo change_date_format($result[0][csf('quot_date')],'dd-mm-yyyy'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Agent</td>
                    <td align="left"><? echo $agent_name_arr[$result[0][csf('agent')]]; ?></td>
                    <td colspan="2" align="left">Size Range</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('size_range')]; ?></td>
                    <td colspan="2" align="left">Style Ref</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('style_ref')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Description</td>
                    <td align="left"><? echo $result[0][csf('gmts_item')]; ?></td>
                    <td colspan="2" align="left">Team Member</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('team_member')]; ?></td>
                    <td colspan="2" align="left">Fabrication</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('fabrication')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">GSM</td>
                    <td align="left"><? echo $result[0][csf('gsm')]; ?></td>
                    <td colspan="2" align="left">Consumption Size</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('cons_size')]; ?></td>
                    <td colspan="2" align="left">Color</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('color')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Require Yarn Count</td>
                    <td colspan="7" align="left"><? echo $result[0][csf('yarn_count')]; ?></td>
                    
                    <td colspan="2" align="left">Order Qty</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('order_qty')]; ?></td>
                </tr>
                <tr>
                    <td colspan="14" bgcolor="#dddddd" align="left"><b>Fabric Consumption / Dz</b></td>
                </tr>
                
                <?
				$subTotalCons = 0;
				$subTotalBottom = 0;
				$grandTota =0;
				
				foreach($dtlsDataArray as $gmtsType => $gmtsData)
				{
					// Top == 1
					// Bottom = 20
					if($gmtsType==1)
					{
						?>
						<tr style="font-weight:bold;">
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>Body Length</td>
							<td>Sleeve Length</td>
							<td>Allow</td>
							<td>1/2 Chest</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>
                        <? foreach($gmtsData as $row)
						{ 
							$subTotalCons += $row['ttl_top_bottom_cons'];
							$grandTota += $row['ttl_top_bottom_cons'];
				
						?>
						<tr  valign="middle">
                        	<td><? echo $body_part_type[$gmtsType]; ?></td>
							<td><? echo $fabric_source[$row['fabric_source']]; ?></td>
							<td><? echo $item_category[$row['fabric_natu']]; ?></td>
							<td><? echo $row['body_length']; ?></td>
							<td><? echo $row['sleeve_length']; ?></td>
							<td><? echo $row['sleev_rise_allow']; ?></td>
							<td><? echo $row['chest']; ?></td>
							<td><? echo $row['chest_thigh_allow']; ?></td>
							<td><? echo $row['gsm']; ?></td>
							<td><? echo $row['body_fabric']; ?></td>
							<td><? echo $row['wastage']; ?></td>
							<td><? echo number_format($row['net_body_fabric'],4);  ?></td>
							<td><? echo $row['rib']; ?></td>
							<td align="right"><? echo $row['ttl_top_bottom_cons']; ?></td>
						</tr>
                         <? 
						 }
						 ?>
                         <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Subtotal Top Consumption </strong></td>
							<td align="right"><strong><?  echo number_format($subTotalCons,4);  ?></strong></td>
						</tr>
                         <?
						 
					}
					else
					{
						
				
						?>
						<tr style="font-weight:bold;">
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>TTL Side/<br>Inseam Length</td>
							<td>Front/ <br>Back Rise</td>
							<td>Allow</td>
							<td>1/2 Thigh</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric<br>Cons</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>
                        <? foreach($gmtsData as $row)
						{ 
						$subTotalBottom += $row['ttl_top_bottom_cons'];
						$grandTota += $row['ttl_top_bottom_cons'];
						?>
						<tr valign="middle">
                        	<td><? echo $body_part_type[$gmtsType]; ?></td>
							<td><? echo $fabric_source[$row['fabric_source']]; ?></td>
							<td><? echo $item_category[$row['fabric_natu']]; ?></td>
							
							<td><? echo $row['inseam_length']; ?></td>
							<td><? echo $row['front_back_rise']; ?></td>
							<td><? echo $row['sleev_rise_allow']; ?></td>
							<td><? echo $row['thigh']; ?></td>
							<td><? echo $row['chest_thigh_allow']; ?></td>
							<td><? echo $row['gsm']; ?></td>
							<td><? echo $row['body_fabric']; ?></td>
							<td><? echo $row['wastage']; ?></td>
							<td><?  echo number_format($row['net_body_fabric'],4);  ?></td>
							<td><? echo $row['rib']; ?></td>
							<td align="right"><? echo $row['ttl_top_bottom_cons']; ?></td>
						</tr>
						<?
						}
						?>
                         <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Subtotal Bottom Consumption </strong></td>
							<td align="right"><strong><? echo number_format($subTotalBottom,4); ?></strong></td>
						</tr>
                        <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Grand Total Consumption </strong></td>
							<td align="right"><strong><? echo number_format($grandTota,4); ?></strong></td>
						</tr>
                         <?
					}
				}
				
				?>
            </table>
		</div>
         <br/>
         <div style="width:210mm;">
			<div style='width:65%;float:left;padding-right:10px;'>
			<table cellspacing='0' border='1' class='rpt_table' rules='all' align='left' style=' text-align:center; font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
                    <th width="">Costing Head</th>
                    <th width="">UOM</th>
                    <th width="100">Consumption</th>
                    <th width="100">Unit Price</th>
                    <th width="100">Total Price</th>
                </thead>
                <tbody class="" id="costing_dtls">
                	<? 
					 if($result[0][csf('yarn_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Yarn Price</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_yarn')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('yarn_total')],4); 
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('knit_fab_purc_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Knit Fabric Purchase</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_knit_fab_purc')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_fab_purc_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_fab_purc_price')],2); ?></td>
                        <td align="right"><? 
						 echo number_format($result[0][csf('knit_fab_purc_total')],4); 
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('woven_fab_purc_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Woven Fabric Purchase</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_woven_fab_purc')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('woven_fab_purc_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('woven_fab_purc_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('woven_fab_purc_total')],4); 
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('yarn_dye_crg_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Yarn Dyeing Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_yarn_dye_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_dye_crg_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_dye_crg_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('yarn_dye_crg_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('knit_crg_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Knitting Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_knit_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_crg_cons')],4);  ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_crg_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('knit_crg_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('dye_crg_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Dyeing Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_dye_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('dye_crg_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('dye_crg_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('dye_crg_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('spandex_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Spandex</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_spandex')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('spandex_amt')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('spandex_unit_price')],2); ?></td>
                        <td align="right"><? 
						 echo number_format($result[0][csf('spandex_total')],4);
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('aop_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">AOP</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_aop')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('aop_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('aop_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('aop_total')],4);
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('collar_cuff_unit_price')]*1 > 0){ 
					 ?> 
                    <tr>
                        <td align="left">Flat Knit Collar & Cuff</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_collar_cuff')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('collar_cuff_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('collar_cuff_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('collar_cuff_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('print_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Print</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_print')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('print_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('print_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('print_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('embro_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Embroidery</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_embro')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('embro_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('embro_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('embro_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('gmts_wash_dye_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Wash/Gmts Dyeing</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_wash_gmts_dye')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('gmts_wash_dye_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('gmts_wash_dye_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('gmts_wash_dye_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('access_price_unit_price')]*1 > 0){ 
					 ?> 
                    <tr>
                        <td align="left">Accessories Price</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_access_price')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('access_price_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('access_price_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('access_price_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('zipper_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Zipper</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_zipper')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('zipper_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('zipper_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('zipper_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('button_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Button</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_button')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('button_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('button_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('button_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('test_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Test</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_test')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('test_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('test_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('test_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('cm_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">CM</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_cm')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('cm_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('cm_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('cm_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('inspec_cost_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Inspection Cost</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_inspec_cost')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_total')],4); ?></td>
                    </tr>
                     <? 
					 }
					 if($result[0][csf('freight_unit_price')]*1 > 0){ 
					 ?>
                   
                    <tr>
                        <td align="left">Freight</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_freight')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_total')],4); ?></td>
                    </tr>
                     <? 
					 }
					 if($result[0][csf('carrier_cost_unit_price')]*1 > 0){ 
					 ?>
                    
                    <tr>
                        <td align="left">Currier Cost</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_carrier_cost')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_total')],4); ?></td>
                    </tr> 
					<? 
					 }
					if($result[0][csf('others_cost_unit_price')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total')],4);  ?></td>
                    </tr>
                     <? }if($result[0][csf('others_cost_unit_price2')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption2')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others2')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons2')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price2')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total2')],4);  ?></td>
                    </tr>
                     <? }if($result[0][csf('others_cost_unit_price3')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption3')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others3')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons3')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price3')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total3')],4);  ?></td>
                    </tr>
                     <? }?>
                    
                    <tr>
                        <td align="left" colspan="4"> <strong>Sub Total</strong> </td>
                        <td align="right"><strong><? echo number_format($sub_total,4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td align="left">Commercial Cost</td>
                        <td colspan="3"><? echo number_format($result[0][csf('comm_cost_cons')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('comm_cost_total')],4);  
						?></td>
                    </tr> 
                    <tr>
                        <td align="left" colspan="4"><strong>Total Factory Cost/ Dz </strong></td>
                        <td align="right"><strong><? echo number_format($tot_factory_cost,4); ?></strong></td>
                    </tr> 
                </tbody>
            </table>
            </div>
            <div style="width:30%;float:right;">
			<table cellspacing='0' border='1' class='rpt_table' id='' rules='all' align='left' style='width:64mm; text-align:center;font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
                    <th colspan="2">Offer Price/ Unit (FOB)</th>
                </thead>
                <tbody class="" id="costing_dtls">
                    <tr>
                        <td align="left"width="150">Factory Unit Price</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('fact_u_price')],4); 
						?></td>
                    </tr> 
                    <tr>
                        <td align="left"width="150">Agent Commission</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('agnt_comm_tot')],4); 
						?></td>
                    </tr> 
                    <tr>
                        <td align="left"width="150">Local Commission</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('local_comm_tot')],4); 
						?></td>
                    </tr> 
                    
                    <tr>
                        <td align="left" >Final Offer Price</td>
                        <td align="right"><strong><? echo number_format($result[0][csf('final_offer_price')],4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong>Order Confirmed Price</strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2" align="right"><strong><? echo number_format($result[0][csf('order_conf_price')],4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong>Order Confirmed Date</strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong><? echo change_date_format($result[0][csf('order_conf_date')],'dd-mm-yyyy'); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:left; height:60px;  text-align: justify; text-justify: inter-word; " valign="top"><strong>Remarks : </strong><? echo $result[0][csf('remarks')]; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
		</div>
        <br>
        <div style="padding-top:500px; width:210mm;">
            <table cellspacing="0" style="width:210mm;" border="0">
                <tr align="center">
                    <td colspan="4" align="left" style="padding-left:40px;">Prepared By</td>
                    <td colspan="2" align="right" style="padding-right:40px;">Approved By</td>
                </tr>
            </table>
		</div>
     </div>   
	<?
}

function sql_update_a($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{
	
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);	
	
	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}
	
	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";
	
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);	
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	 echo $strQuery; die;
	  return $strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con); 
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}


if($action=="create_pdf_pages**********")
{
	require_once("mpdf_new/mpdf.php");
	$mpdf=new mPDF();
	$html ="<table width=\"100%\" style=\"table-layout: fixed;overflow:auto; font-size:40px;\">
					<tr>
						<td colspan=\"3\">hello</td>
					</tr>
					<tr>
						<td colspan=\"3\" align=\"center\">world</td>
					</tr>
			</table>";
			
				
	
	$mpdf->WriteHTML($html);
	foreach (glob("requires/"."*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'price_quotation_' . date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output($name, 'F');
	
	echo "1###$name";
			
	exit();
}

if($action=="create_pdf_pages")
{
	//echo "10****".$data;die;
	require_once("mpdf_new/mpdf.php");
	$mpdf=new mPDF();
	
	//===========================================================================================================================
	//$("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();
	$data = explode('__', $data);
	$cbo_company_id=$data[0];
	$txt_update_id=$data[1];
	$txt_system_id=$data[2];
	
    $buyer_name_arr = return_library_array("select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c  where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$cbo_company_id' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name", "id", "buyer_name");
	
    $agent_name_arr = return_library_array("select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name", "id", "buyer_name");
	
	$company_library = return_library_array("select id,company_name  from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	
	$sql = "select a.id, a.sys_no_prefix, a.sys_prefix_num, a.system_id, a.company_id, a.team_member, a.buyer_id, a.quot_date, a.agent, a.style_ref, a.gmts_item, a.fabrication, a.color, a.yarn_count, a.cons_size, a.order_qty, a.measurment_basis, a.yarn_cons, a.yarn_unit_price, a.yarn_total, a.knit_fab_purc_cons, a.knit_fab_purc_price, a.knit_fab_purc_total, a.woven_fab_purc_cons, a.woven_fab_purc_price, a.woven_fab_purc_total, a.yarn_dye_crg_cons, a.yarn_dye_crg_price, a.yarn_dye_crg_total, a.knit_crg_cons, a.knit_crg_unit_price, a.knit_crg_total, a.dye_crg_cons, a.dye_crg_unit_price, a.dye_crg_total, a.spandex_amt, a.spandex_cons, a.spandex_unit_price, a.spandex_total, a.aop_cons, a.aop_price, a.aop_total, a.collar_cuff_cons, a.collar_cuff_unit_price, a.collar_cuff_total, a.print_cons, a.print_unit_price, a.print_total, a.gmts_wash_dye_cons, a.gmts_wash_dye_price, a.gmts_wash_dye_total, a.access_price_cons, a.access_price_unit_price, a.access_price_total, a.zipper_cons, a.zipper_unit_price, a.zipper_total, a.button_cons, a.button_unit_price, a.button_total, a.test_cons, a.test_unit_price, a.test_total, a.cm_cons, a.cm_unit_price, a.cm_total, a.inspec_cost_cons, a.inspec_cost_unit_price, a.inspec_cost_total, a.freight_cons, a.freight_unit_price, a.freight_total, a.carrier_cost_cons, a.carrier_cost_unit_price, a.carrier_cost_total, a.others_column_caption, a.others_cost_cons, a.others_cost_unit_price, a.others_cost_total, a.comm_cost_cons, a.comm_cost_price, a.comm_cost_total, a.remarks, a.fact_u_price, a.agnt_comm, a.agnt_comm_tot, a.local_comm, a.local_comm_tot, a.final_offer_price, a.order_conf_price, a.order_conf_date, a.embro_cons, a.embro_unit_price, a.embro_total, a.uom_yarn, a.uom_knit_fab_purc, a.uom_woven_fab_purc, a.uom_yarn_dye_crg, a.uom_knit_crg, a.uom_dye_crg, a.uom_spandex, a.uom_aop, a.uom_collar_cuff, a.uom_print, a.uom_embro, a.uom_wash_gmts_dye, a.uom_access_price, a.uom_zipper, a.uom_button, a.uom_test, a.uom_cm, a.uom_inspec_cost, a.uom_freight, a.uom_carrier_cost, a.uom_others, a.uom_others2, a.uom_others3, a.size_range, a.others_column_caption2, a.others_cost_cons2, a.others_cost_unit_price2, a.others_cost_total2, a.others_column_caption3, a.others_cost_cons3, a.others_cost_unit_price3, a.others_cost_total3, 
	b.id as dtls_id, b.garments_type, b.fabric_source, b.fabric_natu, b.body_length, b.sleeve_length, b.inseam_length, b.front_back_rise, b.sleev_rise_allow, b.chest, b.thigh, b.chest_thigh_allow, b.gsm, b.body_fabric, b.wastage, b.net_body_fabric, b.rib, b.ttl_top_bottom_cons
	FROM wo_price_quotation_v3_mst a, wo_price_quotation_v3_dtls b where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	
	 
	
	
	//echo $sql;die;
	$result = sql_select($sql);
	$dtlsDataArray =array();
	foreach($result as $row)
	{
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_source']=$row[csf('fabric_source')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_natu']=$row[csf('fabric_natu')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_length']=$row[csf('body_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleeve_length']=$row[csf('sleeve_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['front_back_rise']=$row[csf('front_back_rise')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleev_rise_allow']=$row[csf('sleev_rise_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest']=$row[csf('chest')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['thigh']=$row[csf('thigh')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest_thigh_allow']=$row[csf('chest_thigh_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['gsm']=$row[csf('gsm')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_fabric']=$row[csf('body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['wastage']=$row[csf('wastage')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['net_body_fabric']=$row[csf('net_body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['rib']=$row[csf('rib')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['ttl_top_bottom_cons']=$row[csf('ttl_top_bottom_cons')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['inseam_length']=$row[csf('inseam_length')];
	}
	
	
	$sub_total = $result[0][csf('yarn_total')]+$result[0][csf('knit_fab_purc_total')]+$result[0][csf('woven_fab_purc_total')]+$result[0][csf('yarn_dye_crg_total')]+$result[0][csf('knit_crg_total')]+$result[0][csf('dye_crg_total')]+$result[0][csf('spandex_total')]+$result[0][csf('aop_total')]+$result[0][csf('collar_cuff_total')]+$result[0][csf('print_total')]+$result[0][csf('embro_total')]+$result[0][csf('gmts_wash_dye_total')]+$result[0][csf('access_price_total')]+$result[0][csf('zipper_total')]+$result[0][csf('button_total')]+$result[0][csf('test_total')]+$result[0][csf('cm_total')]+$result[0][csf('inspec_cost_total')]+$result[0][csf('freight_total')]+$result[0][csf('carrier_cost_total')]+$result[0][csf('others_cost_total')]+$result[0][csf('others_cost_total2')]+$result[0][csf('others_cost_total3')];
	
	$tot_factory_cost =$sub_total+$result[0][csf('comm_cost_total')];
		
	$measurement_basis_arr = array(1=>"Cad Bassis", 2=>"Measurement Basis");	
	//===========================================================================================================================
	
 $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");

//foreach ($data_array as $img_row) 
//{
//	$imagPath = "<img src='../../../'".$img_row[csf('image_location')]."' height='50' width='50' align='middle'/>";
//}

$html ="";
	$html .= "<div align='left'>
        <div>
            <table cellspacing='0' border='0' style='width:210mm; margin-right:-10px;'>
                <tr class='form_caption'>
                    <td rowspan='2' align='left' width='50'>".$imagPath ."</td>
                    <td colspan='8' align='center' style='font-size:25px;'>
                        <strong>".$company_library[$data[0]]."</strong>
                    </td>
                    <td rowspan='2' align='left' width='50'>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan='8' align='center' style='font-size:18px'>".show_company($data[0], '', array('city'))."<br>
                    	<strong><u> Price Quotation</u></strong>
                    </td>
                </tr>
            </table>
        </div>
        <br>
		<div>
            <table style='width:210mm; text-align:center; font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' cellspacing='0' border='1' rules='all' class='rpt_table'>
            	<tr>
                	<td colspan='14' bgcolor='#dddddd' align='left'><b>System No: ".$result[0][csf('system_id')]."</b></td>
                </tr>
                <tr>
                    <td colspan='2' align='left'>Buyer</td>
                    <td align='left'>".$buyer_name_arr[$result[0][csf('buyer_id')]]."</td>
                    <td colspan='2' align='left'>Consumption Basis</td>
                    <td colspan='4' align='left'>".$measurement_basis_arr[$result[0][csf('measurment_basis')]]."</td>
                    <td colspan='2' align='left'>Date</td>
                    <td colspan='3' align='left'>".change_date_format($result[0][csf('quot_date')],'dd-mm-yyyy')."</td>
                </tr>
                <tr>
                    <td colspan='2' align='left'>Agent</td>
                    <td align='left'>".$agent_name_arr[$result[0][csf('agent')]]."</td>
                    <td colspan='2' align='left'>Size Range</td>
                    <td colspan='4' align='left'>".$result[0][csf('size_range')]."</td>
                    <td colspan='2' align='left'>Style Ref</td>
                    <td colspan='3' align='left'>".$result[0][csf('style_ref')]."</td>
                </tr>
                <tr>
                    <td colspan='2' align='left'>Description</td>
                    <td align='left'>".$result[0][csf('gmts_item')]."</td>
                    <td colspan='2' align='left'>Team Member</td>
                    <td colspan='4' align='left'>".$result[0][csf('team_member')]."</td>
                    <td colspan='2' align='left'>Fabrication</td>
                    <td colspan='3' align='left'>".$result[0][csf('fabrication')]."</td>
                </tr>
                <tr>
                    <td colspan='2' align='left'>GSM</td>
                    <td align='left'>".$result[0][csf('gsm')]."</td>
                    <td colspan='2' align='left'>Consumption Size</td>
                    <td colspan='4' align='left'>".$result[0][csf('cons_size')]."</td>
                    <td colspan='2' align='left'>Color</td>
                    <td colspan='3' align='left'>".$result[0][csf('color')]."</td>
                </tr>
                <tr>
                    <td colspan='2' align='left'>Require Yarn Count</td>

                    <td colspan='7' align='left'>".$result[0][csf('yarn_count')]."</td>
                    
                    <td colspan='2' align='left'>Order Qty</td>
                    <td colspan='3' align='left'>".$result[0][csf('order_qty')]."</td>
                </tr>
                <tr>
                    <td colspan='14' bgcolor='#dddddd' align='left'><b>Fabric Consumption / Dz</b></td>
                </tr>".
				$subTotalCons = 0;
				$subTotalBottom = 0;
				$grandTota =0;
				
				foreach($dtlsDataArray as $gmtsType => $gmtsData)
				{
					if($gmtsType==1)
					{
						$html .= "<tr style='font-weight:bold'>
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>Body Length</td>
							<td>Sleeve Length</td>
							<td>Allow</td>
							<td>1/2 Chest</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
						</tr>";
						foreach($gmtsData as $row)
						{ 
						 	$subTotalCons += $row['ttl_top_bottom_cons'];
							$grandTota += $row['ttl_top_bottom_cons'];
							
							$html .= "<tr  valign='middle'>
                        	<td>".$body_part_type[$gmtsType]."</td>
							<td>".$fabric_source[$row['fabric_source']]."</td>
							<td>".$item_category[$row['fabric_natu']]."</td>
							<td>".$row['body_length']."</td>
							<td>".$row['sleeve_length']."</td>
							<td>".$row['sleev_rise_allow']."</td>
							<td>".$row['chest']."</td>
							<td>".$row['chest_thigh_allow']."</td>
							<td>".$row['gsm']."</td>
							<td>".$row['body_fabric']."</td>
							<td>".$row['wastage']."</td>
							<td>".number_format($row['net_body_fabric'],4)."</td>
							<td>".$row['rib']."</td>
							<td align='right'>".$row['ttl_top_bottom_cons']."</td>
						</tr>"; 
						} 
						$html .= "<tr valign='middle'>
                        	<td colspan='13' align='right'><strong>Subtotal Top Consumption </strong></td>
							<td align='right'><strong>".number_format($subTotalCons,4)."</strong></td>
						</tr>"; 
					}
					else
					{
					$html .= "<tr style='font-weight:bold;'>
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>TTL Side/<br>Inseam Length</td>
							<td>Front/ <br>Back Rise</td>
							<td>Allow</td>
							<td>1/2 Thigh</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric<br>Cons</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>";
						foreach($gmtsData as $row){ 
						$subTotalBottom += $row['ttl_top_bottom_cons'];
						$grandTota += $row['ttl_top_bottom_cons'];
						$html .= "<tr valign='middle'>
                        	<td>".$body_part_type[$gmtsType]."</td>
							<td>".$fabric_source[$row['fabric_source']]."</td>
							<td>".$item_category[$row['fabric_natu']]."</td>
							
							<td>".$row['inseam_length']."</td>
							<td>".$row['front_back_rise']."</td>
							<td>".$row['sleev_rise_allow']."</td>
							<td>".$row['thigh']."</td>
							<td>".$row['chest_thigh_allow']."</td>
							<td>".$row['gsm']."</td>
							<td>".$row['body_fabric']."</td>
							<td>".$row['wastage']."</td>
							<td>".number_format($row['net_body_fabric'],4)."</td>
							<td>".$row['rib']."</td>
							<td align='right'>".$row['ttl_top_bottom_cons']."</td>
						</tr>";
						}
						$html .= "<tr valign='middle'>
                        	<td colspan='13' align='right'><strong>Subtotal Bottom Consumption </strong></td>
							<td align='right'><strong>".number_format($subTotalBottom,4)."</strong></td>
						</tr>
                        <tr valign='middle'>
                        	<td colspan='13' align='right'><strong>Grand Total Consumption </strong></td>
							<td align='right'><strong>".number_format($grandTota,4)."</strong></td>
						</tr>";
					}
				}
				$html .= "</table>
		</div>
         <br/>
        <div style=\"width:100%;\">
			<div style='width:70%;float:left;padding-right:10px;'>
			<table cellspacing='0' border='1' class='rpt_table' rules='all' align='left' style=' text-align:center; font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
					<tr>
                    <th width=''>Costing Head</th>
                    <th width='20'>UOM</th>
                    <th width='80'>Consumption</th>
                    <th width='80'>Unit Price</th>
                    <th width='80'>Total Price</th>
					</tr>
                </thead>
                <tbody class='' id='costing_dtls'>";
				if($result[0][csf('yarn_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Yarn Price</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_yarn')]]."</td>
                        <td align='right'>".number_format($result[0][csf('yarn_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('yarn_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('yarn_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('knit_fab_purc_price')]*1 > 0){ 
					$html .= "<tr>
                        <td align='left'>Knit Fabric Purchase</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_knit_fab_purc')]]."</td>
                        <td align='right'>".number_format($result[0][csf('knit_fab_purc_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('knit_fab_purc_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('knit_fab_purc_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('woven_fab_purc_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Woven Fabric Purchase</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_woven_fab_purc')]]."</td>
                        <td align='right'>".number_format($result[0][csf('woven_fab_purc_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('woven_fab_purc_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('woven_fab_purc_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('yarn_dye_crg_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Yarn Dyeing Charge</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_yarn_dye_crg')]]."</td>
                        <td align='right'>".number_format($result[0][csf('yarn_dye_crg_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('yarn_dye_crg_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('yarn_dye_crg_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('knit_crg_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Knitting Charge</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_knit_crg')]]."</td>
                        <td align='right'>".number_format($result[0][csf('knit_crg_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('knit_crg_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('knit_crg_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('dye_crg_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Dyeing Charge</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_dye_crg')]]."</td>
                        <td align='right'>".number_format($result[0][csf('dye_crg_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('dye_crg_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('dye_crg_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('spandex_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Spandex</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_spandex')]]."</td>
                        <td align='right'>".number_format($result[0][csf('spandex_amt')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('spandex_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('spandex_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('aop_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>AOP</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_aop')]]."</td>
                        <td align='right'>".number_format($result[0][csf('aop_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('aop_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('aop_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('collar_cuff_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Flat Knit Collar & Cuff</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_collar_cuff')]]."</td>
                        <td align='right'>".number_format($result[0][csf('collar_cuff_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('collar_cuff_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('collar_cuff_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('print_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Print</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_print')]]."</td>
                        <td align='right'>".number_format($result[0][csf('print_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('print_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('print_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('embro_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Embroidery</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_embro')]]."</td>
                        <td align='right'>".number_format($result[0][csf('embro_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('embro_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('embro_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('gmts_wash_dye_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Wash/Gmts Dyeing</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_wash_gmts_dye')]]."</td>
                        <td align='right'>".number_format($result[0][csf('gmts_wash_dye_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('gmts_wash_dye_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('gmts_wash_dye_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('access_price_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Accessories Price</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_access_price')]]."</td>
                        <td align='right'>".number_format($result[0][csf('access_price_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('access_price_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('access_price_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('zipper_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Zipper</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_zipper')]]."</td>
                        <td align='right'>".number_format($result[0][csf('zipper_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('zipper_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('zipper_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('button_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Button</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_button')]]."</td>
                        <td align='right'>".number_format($result[0][csf('button_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('button_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('button_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('test_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Test</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_test')]]."</td>
                        <td align='right'>".number_format($result[0][csf('test_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('test_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('test_total')],4)."</td>
                    </tr> ";
					}
					 if($result[0][csf('cm_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>CM</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_cm')]]."</td>
                        <td align='right'>".number_format($result[0][csf('cm_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('cm_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('cm_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('inspec_cost_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Inspection Cost</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_inspec_cost')]]."</td>
                        <td align='right'>".number_format($result[0][csf('inspec_cost_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('inspec_cost_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('inspec_cost_total')],4)."</td>
                    </tr>";
					}
					 if($result[0][csf('freight_unit_price')]*1 > 0){
						$html .= "<tr>
                        <td align='left'>Freight</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_freight')]]."</td>
                        <td align='right'>".number_format($result[0][csf('freight_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('freight_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('freight_total')],4)."</td>
                    </tr>";
					 }
					 if($result[0][csf('carrier_cost_unit_price')]*1 > 0){ 
					 $html .= "<tr>
                        <td align='left'>Currier Cost</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_carrier_cost')]]."</td>
                        <td align='right'>".number_format($result[0][csf('carrier_cost_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('carrier_cost_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('carrier_cost_total')],4)."</td>
                    </tr>"; 
					 }
					if($result[0][csf('others_cost_unit_price')]*1 > 0){ 
					$html .= "<tr>
                        <td align='left'>".$result[0][csf('others_column_caption')]."</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_others')]]."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_cons')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_unit_price')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_total')],4)."</td>
                    </tr>";
                    }
					if($result[0][csf('others_cost_unit_price2')]*1 > 0){ 
					$html .= "<tr>
                        <td align='left'>".$result[0][csf('others_column_caption2')]."</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_others2')]]."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_cons2')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_unit_price2')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_total2')],4)."</td>
                    </tr>";
                     }
					 if($result[0][csf('others_cost_unit_price3')]*1 > 0){ 
					$html .= "<tr>
                        <td align='left'>".$result[0][csf('others_column_caption3')]."</td>
                        <td align='center'>".$unit_of_measurement[$result[0][csf('uom_others3')]]."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_cons3')],4)."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_unit_price3')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('others_cost_total3')],4)."</td>
                    </tr>";
					}
					 $html .= "<tr>
                        <td align='left' colspan='4'> <strong>Sub Total</strong> </td>
                        <td align='right'><strong>".number_format($sub_total,4)."</strong></td>
                    </tr> 
                    <tr>
                        <td align='left'>Commercial Cost</td>
                        <td colspan='3'>".number_format($result[0][csf('comm_cost_cons')],2)."</td>
                        <td align='right'>".number_format($result[0][csf('comm_cost_total')],4)."</td>
                    </tr> 
                    <tr>
                        <td align='left' colspan='4'><strong>Total Factory Cost/ Dz </strong></td>
                        <td align='right'><strong>".number_format($tot_factory_cost,4)."</strong></td>
                    </tr> 
                </tbody>
            </table>
			</div>
			<div style=\"width:28%;float:right;\">
			<table cellspacing='0' border='1' class='rpt_table' id='' rules='all' align='left' style='width:64mm; text-align:center;font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
					<tr>
                    <th colspan='2'>Offer Price/ Unit (FOB)</th>
					</tr>
                </thead>
                <tbody class='' id='costing_dtls'>
                    <tr>
                        <td align='left'width='150'>Factory Unit Price</td>
                        <td align='right'>".number_format($result[0][csf('fact_u_price')],4)."</td>
                    </tr> 
                    <tr>
                        <td align='left'width='150'>Agent Commission</td>
                        <td align='right'>".number_format($result[0][csf('agnt_comm_tot')],4)."</td>
                    </tr> 
                    <tr>
                        <td align='left'width='150'>Local Commission</td>
                        <td align='right'>".number_format($result[0][csf('local_comm_tot')],4)."</td>
                    </tr> 
                    
                    <tr>
                        <td align='left' >Final Offer Price</td>
                        <td align='right'><strong>".number_format($result[0][csf('final_offer_price')],4)."</strong></td>
                    </tr> 
                    <tr>
                        <td colspan='2'><strong>Order Confirmed Price</strong></td>
                    </tr> 
                    <tr>
                        <td colspan='2' align='right'><strong>".number_format($result[0][csf('order_conf_price')],4)."</strong></td>
                    </tr> 
                    <tr>
                        <td colspan='2'><strong>Order Confirmed Date</strong></td>
                    </tr> 
                    <tr>
                        <td colspan='2'><strong>".change_date_format($result[0][csf('order_conf_date')],'dd-mm-yyyy')."</strong></td>
                    </tr>
                    <tr>
                        <td colspan='2' style='text-align:left; height:60px;  text-align: justify; text-justify: inter-word; ' valign='top'><strong>Remarks : </strong>".$result[0][csf('remarks')]."</td>
                    </tr>
                </tbody>
            </table>
			</div>
		</div>
        <div style='padding-top:70px;'>
            <table cellspacing='0' style='width:210mm; font-size:11px; font-family:Arial Narrow,Arial,sans-serif;' border='0'>
                <tr align='center'>
                    <td colspan='4' align='left' style='padding-left:40px;'>Prepared By</td>
                    <td colspan='2' align='right' style='padding-right:40px;'>Approved By</td>
                </tr>
            </table>
		</div>
     </div>";
				
	
	$mpdf->WriteHTML($html);
	foreach (glob("requires/"."*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'price_quotation_' . date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output($name, 'F');
	
	echo "1###$name";
			
	exit();
}

