<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"]; 
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$action="report_generate";
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$str_cond="";
	//$field_arr['lib_season']="id, buyer_id, season_name, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	if($db_type==0)
	{
		$insert_cond="DATE_FORMAT(insert_date, '%d-%b-%Y %h:%i:%s %p')";
		$update_cond="DATE_FORMAT(update_date, '%d-%b-%Y %h:%i:%s %p')";
		$approvedDate_cond="DATE_FORMAT(approved_date, '%d-%b-%Y %h:%i:%s %p')";
		$restore_time_change_db_cond="DATE_FORMAT(max(restore_time), '%d-%b-%Y %h:%i:%s %p')";
	}
	else if($db_type==2)
	{
		$insert_cond="to_char(insert_date,'yyyy-mm-dd HH24:MI:SS')";
		$update_cond="to_char(update_date,'yyyy-mm-dd HH24:MI:SS')";
		$approvedDate_cond="to_char(approved_date,'yyyy-mm-dd HH24:MI:SS')";
		$restore_time_change_db_cond="to_char(max(restore_time),'yyyy-mm-dd HH24:MI:SS')";
	}
	//echo $cbo_buyer_id; die;
	
	if($db_type==2)
	{
		$sql_userpass=sql_select("select id, employee_id, user_name, password, user_full_name, designation, created_on, created_by, access_ip, access_proxy_ip, expire_on, user_level, buyer_id, unit_id, is_data_level_secured, valid, department_id, supplier_id, item_cate_id, company_location_id, store_location_id, user_email, is_fst_time, reset_code, graph_id, row_status from user_passwd where 1=1 order by id ASC");
		foreach($sql_userpass as $row)
		{
			if($db_type==0)
			{
				if($row[csf("created_on")]!='0000-00-00') $create_date=date("d-M-Y",strtotime($row[csf("created_on")]));
				else $create_date='';
				
				if($row[csf("expire_on")]!='0000-00-00') $expire_date=date("d-M-Y",strtotime($row[csf("expire_on")]));
				else $expire_date='';
			}
			else if($db_type==2)
			{
				$create_date=date("Y-m-d",strtotime($row[csf("created_on")]));//change_date_format($row[csf("created_on")],'yyyy-mm-dd');
				$expire_date=date("Y-m-d",strtotime($row[csf("expire_on")]));
			}
			$data_arr['lib_userpass'][]="('".$row[csf("id")]."','".$row[csf("employee_id")]."','".$row[csf("user_name")]."','".$row[csf("password")]."','".$row[csf("user_full_name")]."','".$row[csf("designation")]."','".$create_date."','".$row[csf("created_by")]."','".$row[csf("access_ip")]."','".$row[csf("access_proxy_ip")]."','".$expire_date."','".$row[csf("user_level")]."','".$row[csf("buyer_id")]."','".$row[csf("unit_id")]."','".$row[csf("is_data_level_secured")]."','".$row[csf("valid")]."','".$row[csf("department_id")]."','".$row[csf("supplier_id")]."','".$row[csf("item_cate_id")]."','".$row[csf("company_location_id")]."','".$row[csf("store_location_id")]."','".$row[csf("user_email")]."','".$row[csf("is_fst_time")]."','".$row[csf("reset_code")]."','".$row[csf("graph_id")]."','".$row[csf("row_status")]."')";
		}
		unset($sql_userpass);
		
		$sql_buyer=sql_select("select id, buyer_name, short_name, contact_person, exporters_reference, party_type, designation, tag_company, country_id, web_site, buyer_email, address_1, address_2, address_3, address_4, remark, supllier, credit_limit_days, credit_limit_amount, credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted, sewing_effi_mkt_percent, sewing_effi_plaing_per, marketing_team_id, control_delivery, vat_reg_no, cut_off_used, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from lib_buyer where 1=1 order by id ASC");
		foreach($sql_buyer as $row)
		{
			$data_arr['lib_buyer'][]="('".$row[csf("id")]."','".$row[csf("buyer_name")]."','".$row[csf("short_name")]."','".$row[csf("contact_person")]."','".$row[csf("exporters_reference")]."','".$row[csf("party_type")]."','".$row[csf("designation")]."','".$row[csf("tag_company")]."','".$row[csf("country_id")]."','".$row[csf("web_site")]."','".$row[csf("buyer_email")]."','".$row[csf("address_1")]."','".$row[csf("address_2")]."','".$row[csf("address_3")]."','".$row[csf("address_4")]."','".$row[csf("remark")]."','".$row[csf("supllier")]."','".$row[csf("credit_limit_days")]."','".$row[csf("credit_limit_amount")]."','".$row[csf("credit_limit_amount_currency")]."','".$row[csf("discount_method")]."','".$row[csf("securitye_deducted")]."','".$row[csf("vat_to_be_deducted")]."','".$row[csf("ait_to_be_deducted")]."','".$row[csf("sewing_effi_mkt_percent")]."','".$row[csf("sewing_effi_plaing_per")]."','".$row[csf("marketing_team_id")]."','".$row[csf("control_delivery")]."','".$row[csf("vat_reg_no")]."','".$row[csf("cut_off_used")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_buyer);
		
		$sql_bParty=sql_select("select id, buyer_id, party_type from lib_buyer_party_type where 1=1 order by id ASC");
		foreach($sql_bParty as $row)
		{
			$data_arr['lib_bParty'][]="('".$row[csf("id")]."','".$row[csf("buyer_id")]."','".$row[csf("party_type")]."')";
		}
		unset($sql_bParty);
		
		$sql_btagCom=sql_select("select id, buyer_id, tag_company from lib_buyer_tag_company where 1=1 order by id ASC");
		foreach($sql_btagCom as $row)
		{
			$data_arr['lib_btagCom'][]="('".$row[csf("id")]."','".$row[csf("buyer_id")]."','".$row[csf("tag_company")]."')";
		}
		unset($sql_btagCom);
		
		$sql_designation=sql_select("select id, level_des, system_designation, custom_designation, custom_designation_local, allowance_rate, allowance_treatment, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, is_locked from lib_designation where 1=1 order by id ASC");
		foreach($sql_designation as $row)
		{
			$data_arr['lib_designation'][]="('".$row[csf("id")]."','".$row[csf("level_des")]."','".$row[csf("system_designation")]."','".$row[csf("custom_designation")]."','".$row[csf("custom_designation_local")]."','".$row[csf("allowance_rate")]."','".$row[csf("allowance_treatment")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("is_locked")]."')";
		}
		unset($sql_designation);
		
		$sql_department=sql_select("select id, department_name, division_id, contact_person, contact_no, country_id, website, email, short_name, address, remark, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, is_locked from lib_department where 1=1 order by id ASC");
		foreach($sql_department as $row)
		{
			$data_arr['lib_department'][]="('".$row[csf("id")]."','".$row[csf("department_name")]."','".$row[csf("division_id")]."','".$row[csf("contact_person")]."','".$row[csf("contact_no")]."','".$row[csf("country_id")]."','".$row[csf("website")]."','".$row[csf("email")]."','".$row[csf("short_name")]."','".$row[csf("address")]."','".$row[csf("remark")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("is_locked")]."')";
		}
		unset($sql_department);
		//======================
		$sql_mktmst=sql_select("select id, team_name, team_leader_name, team_leader_desig, team_leader_email, lib_mkt_team_member_info_id, total_member, capacity_smv, capacity_basic, team_contact_no, user_tag_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from lib_marketing_team where 1=1 order by id ASC");
		foreach($sql_mktmst as $row)
		{
			$data_arr['lib_mktmst'][]="('".$row[csf("id")]."','".$row[csf("team_name")]."','".$row[csf("team_leader_name")]."','".$row[csf("team_leader_desig")]."','".$row[csf("team_leader_email")]."','".$row[csf("lib_mkt_team_member_info_id")]."','".$row[csf("total_member")]."','".$row[csf("capacity_smv")]."','".$row[csf("capacity_basic")]."','".$row[csf("team_contact_no")]."','".$row[csf("user_tag_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_mktmst);
		
		$sql_mktdtls=sql_select("select id, team_id, designation, team_member_name, team_member_email, capacity_smv_member, capacity_basic_member, member_contact_no, user_tag_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from lib_mkt_team_member_info where 1=1 order by id ASC");
		foreach($sql_mktdtls as $row)
		{
			$data_arr['lib_mktdtls'][]="('".$row[csf("id")]."','".$row[csf("team_id")]."','".$row[csf("designation")]."','".$row[csf("team_member_name")]."','".$row[csf("team_member_email")]."','".$row[csf("capacity_smv_member")]."','".$row[csf("capacity_basic_member")]."','".$row[csf("member_contact_no")]."','".$row[csf("user_tag_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_mktdtls);
		
		$sql_subDept=sql_select("select id, sub_department_name, department_id, buyer_id, inserted_by, $insert_cond as insert_date, update_by, $update_cond as update_date, status_active, is_deleted from lib_pro_sub_deparatment where 1=1 order by id ASC");
		foreach($sql_subDept as $row)
		{
			$data_arr['lib_subDept'][]="('".$row[csf("id")]."','".$row[csf("sub_department_name")]."','".$row[csf("department_id")]."','".$row[csf("buyer_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("update_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_subDept);
		
		$sql_stdcm=sql_select("select id, company_id, applying_period_date, applying_period_to_date, bep_cm, asking_profit, asking_cm, monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, asking_avg_rate, actual_cm, max_profit, depreciation_amorti, interest_expense, income_tax, operating_expn, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, is_locked from lib_standard_cm_entry where 1=1 order by id ASC");
		foreach($sql_stdcm as $row)
		{
			if($db_type==0)
			{
				if($row[csf("applying_period_date")]!='0000-00-00') $app_from_date=date("d-M-Y",strtotime($row[csf("applying_period_date")]));
				else $app_from_date='';
				
				if($row[csf("applying_period_to_date")]!='0000-00-00') $app_to_date=date("d-M-Y",strtotime($row[csf("applying_period_to_date")]));
				else $app_to_date='';
			}
			else if($db_type==2)
			{
				$app_from_date=date("Y-m-d",strtotime($row[csf("applying_period_date")]));//change_date_format($row[csf("created_on")],'yyyy-mm-dd');
				$app_to_date=date("Y-m-d",strtotime($row[csf("applying_period_to_date")]));
			}
			
			$data_arr['lib_stdcm'][]="('".$row[csf("id")]."','".$row[csf("company_id")]."','".$app_from_date."','".$app_to_date."','".$row[csf("bep_cm")]."','".$row[csf("asking_profit")]."','".$row[csf("asking_cm")]."','".$row[csf("monthly_cm_expense")]."','".$row[csf("no_factory_machine")]."','".$row[csf("working_hour")]."','".$row[csf("cost_per_minute")]."','".$row[csf("asking_avg_rate")]."','".$row[csf("actual_cm")]."','".$row[csf("max_profit")]."','".$row[csf("depreciation_amorti")]."','".$row[csf("interest_expense")]."','".$row[csf("income_tax")]."','".$row[csf("operating_expn")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("is_locked")]."')";
		}
		unset($sql_stdcm);
		
		$sql_season=sql_select("select id, buyer_id, season_name, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from lib_buyer_season where 1=1 order by id ASC");
		foreach($sql_season as $row)
		{
			$data_arr['lib_season'][]="('".$row[csf("id")]."','".$row[csf("buyer_id")]."','".$row[csf("season_name")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_season);
		
		$sql_item_group=sql_select("select id, item_name, trim_type, remark, order_uom, trim_uom, item_category, item_group_code, conversion_factor, fancy_item, cal_parameter, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from lib_item_group where 1=1 order by id ASC");
		foreach($sql_item_group as $row)
		{
			$data_arr['lib_item_group'][]="('".$row[csf("id")]."','".$row[csf("item_name")]."','".$row[csf("trim_type")]."','".$row[csf("remark")]."','".$row[csf("order_uom")]."','".$row[csf("trim_uom")]."','".$row[csf("item_category")]."','".$row[csf("item_group_code")]."','".$row[csf("conversion_factor")]."','".$row[csf("fancy_item")]."','".$row[csf("cal_parameter")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_item_group);
		
		//$field_arr['lib_template']="id, item_id, item_name, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_tmp=sql_select("select id, item_id, item_name, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from lib_qc_template where 1=1 order by id ASC");
		foreach($sql_tmp as $row)
		{
			$data_arr['lib_template'][]="('".$row[csf("id")]."','".$row[csf("item_id")]."','".$row[csf("item_name")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_tmp);
	
		//print_r($data_arr['lib_template']);
		//$field_arr['qc_template']="id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_qc_temp=sql_select("select id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from qc_template where 1=1 order by id ASC");
		foreach($sql_qc_temp as $row)
		{
			$data_arr['qc_template'][]="('".$row[csf("id")]."','".$row[csf("temp_id")]."','".$row[csf("item_id1")]."','".$row[csf("ratio1")]."','".$row[csf("item_id2")]."','".$row[csf("ratio2")]."','".$row[csf("lib_item_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("tuid")]."')";
		}
		unset($sql_qc_temp);
		
		//$field_arr['qc_stage']="id, stage_name, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_qc_stage=sql_select("select id, stage_name, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from lib_stage_name where 1=1 order by id ASC");
		foreach($sql_qc_stage as $row)
		{
			$data_arr['qc_stage'][]="('".$row[csf("id")]."','".$row[csf("stage_name")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("tuid")]."')"; 
		}
		unset($sql_qc_stage);
		
		$sql_qc_lib_agent_loc=sql_select("select id, type, agent_location, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from lib_agent_location where 1=1 order by id ASC");
		foreach($sql_qc_lib_agent_loc as $row)
		{
			$data_arr['qc_lib_agent_loc'][]="('".$row[csf("id")]."','".$row[csf("type")]."','".$row[csf("agent_location")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("tuid")]."')"; 
		}
		unset($sql_qc_lib_agent_loc);
		//print_r($data_arr['qc_lib_agent_loc']); 
		//$field_arr['qc_mst']="id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref,  department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, lib_item_id, pre_cost_sheet_id, revise_no, option_id, buyer_remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$buyer_cond=""; $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
		if($cbo_buyer_id=='') $buyer_cond=""; else $buyer_cond="and buyer_id in ($cbo_buyer_id)";
		
		$form_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
		$costingdate_cond="";
		if($db_type==0)
		{
			if ($form_date!="" &&  $to_date!="") $costingdate_cond="and costing_date between '".change_date_format($form_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'"; else $costingdate_cond ="";
		}
		else if($db_type==2)
		{
			if ($form_date!="" && $to_date!="") $costingdate_cond="and costing_date between '".change_date_format($form_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($to_date, "yyyy-mm-dd", "-",1)."'"; else $costingdate_cond ="";
		}
		
		$user_id_cond=""; $cbo_user_id=str_replace("'","",$cbo_user_id);
		if($cbo_user_id=='') $user_id_cond=""; else $user_id_cond="and inserted_by in ($cbo_user_id)";
		
		$sql_mst=sql_select("Select id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, lib_item_id, pre_cost_sheet_id, revise_no, option_id, option_remarks, buyer_remarks, meeting_no, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, qc_no, uom, approved, approved_by, $approvedDate_cond as approved_date from qc_mst where 1=1 $costingdate_cond $buyer_cond order $user_id_cond by id ASC");// or die(mysql_error());
		
		$add_comma=0; $mstIds=''; $tot_rows=0;
		foreach($sql_mst as $row)
		{
			if($db_type==0)
			{
				if($row[csf("costing_date")]!='0000-00-00') $costing_date=date("d-M-Y",strtotime($row[csf("costing_date")]));
				else $costing_date='';
				
				if($row[csf("delivery_date")]!='0000-00-00') $delivery_date=date("d-M-Y",strtotime($row[csf("delivery_date")]));
				else $delivery_date='';
			}
			else if($db_type==2)
			{
				$costing_date=date("Y-m-d",strtotime($row[csf("costing_date")]));//change_date_format($row[csf("costing_date")],'yyyy-mm-dd');
				$delivery_date=date("Y-m-d",strtotime($row[csf("delivery_date")]));
			}
			$tot_rows++;
			$mstIds.=$row[csf("qc_no")].",";
			$data_arr['qc_mst'][]="('".$row[csf("id")]."','".$row[csf("cost_sheet_id")]."','".$row[csf("cost_sheet_no")]."','".$row[csf("temp_id")]."','".$row[csf("style_des")]."','".$row[csf("buyer_id")]."','".$row[csf("cons_basis")]."','".$row[csf("season_id")]."','".$row[csf("style_ref")]."','".$row[csf("department_id")]."','".$delivery_date."','".$row[csf("exchange_rate")]."','".$row[csf("offer_qty")]."','".$row[csf("quoted_price")]."','".$row[csf("tgt_price")]."','".$row[csf("stage_id")]."','".$costing_date."','".trim($row[csf("lib_item_id")])."','".$row[csf("pre_cost_sheet_id")]."','".$row[csf("revise_no")]."','".$row[csf("option_id")]."','".$row[csf("option_remarks")]."','".$row[csf("buyer_remarks")]."','".$row[csf("meeting_no")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("qc_no")]."','".$row[csf("uom")]."','".$row[csf("approved")]."','".$row[csf("approved_by")]."','".$row[csf("approved_date")]."','0')"; 
		}
		//print_r($data_arr['qc_mst']);
		unset($sql_mst);
		$mstIds=chop($mstIds,','); $mst_id_cond=''; $confirm_qc_no_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$mst_id_cond=" and (";
			$confirm_qc_no_cond=" and (";
			$mstIdsArr=array_chunk(explode(",",$mstIds),999);
			foreach($mstIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$mst_id_cond.=" mst_id in ($ids) or ";
				$confirm_qc_no_cond.=" cost_sheet_id in ($ids) or ";
			}
			$mst_id_cond=chop($mst_id_cond,'or ');
			$mst_id_cond.=")";
			
			$confirm_qc_no_cond=chop($confirm_qc_no_cond,'or ');
			$confirm_qc_no_cond.=")";
		}
		else
		{
			$mst_id_cond=" and mst_id in ($mstIds)";
			$confirm_qc_no_cond=" and cost_sheet_id in ($mstIds)";
		}
		
		//$field_arr['qc_fab_cost']="id, mst_id, item_id, body_part, des, value, alw, uniq_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_qc_fab_cost=sql_select("select id, mst_id, item_id, body_part, des, value, alw, uniq_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_fabric_dtls where 1=1 $mst_id_cond order by id ASC");//$mst_id_cond
		
		foreach($sql_qc_fab_cost as $row)
		{
			$data_arr['qc_fab_cost'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("body_part")]."','".$row[csf("des")]."','".$row[csf("value")]."','".$row[csf("alw")]."','".$row[csf("uniq_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_qc_fab_cost);
		
		//$field_arr['qc_cons_rate']="id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_cons_rate=sql_select("select id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, ex_percent, tot_cons, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_cons_rate_dtls where 1=1 $mst_id_cond order by id ASC");//$mst_id_cond
		
		foreach($sql_cons_rate as $row)
		{
			$data_arr['qc_cons_rate'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("type")]."','".$row[csf("particular_type_id")]."','".$row[csf("formula")]."','".$row[csf("consumption")]."','".$row[csf("unit")]."','".$row[csf("is_calculation")]."','".$row[csf("rate")]."','".$row[csf("rate_data")]."','".$row[csf("value")]."','".$row[csf("ex_percent")]."','".$row[csf("tot_cons")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_cons_rate);
		//print_r($data_arr['qc_cons_rate']);
		$sql_fabstrdata=sql_select("select id, mst_id, item_id, body_part_data, des_val_alw_data, fab_cons_rate_data, special_operation_data, accessories_data, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_fabric_string_data where 1=1 $mst_id_cond order by id ASC"); //$mst_id_cond
		foreach($sql_fabstrdata as $row)
		{
			$data_arr['qc_fabstrdata'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("body_part_data")]."','".$row[csf("des_val_alw_data")]."','".$row[csf("fab_cons_rate_data")]."','".$row[csf("special_operation_data")]."','".$row[csf("accessories_data")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		//print_r($data_arr['qc_fabstrdata']); die;
		unset($sql_fabstrdata);
		
		//$field_arr['qc_summary']="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_summary=sql_select("select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_tot_cost_summary where 1=1 $mst_id_cond order by id ASC");// $mst_id_cond
		
		foreach($sql_summary as $row)
		{
			$data_arr['qc_summary'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("buyer_agent_id")]."','".$row[csf("location_id")]."','".$row[csf("no_of_pack")]."','".$row[csf("is_confirm")]."','".$row[csf("is_cm_calculative")]."','".$row[csf("mis_lumsum_cost")]."','".$row[csf("commision_per")]."','".$row[csf("tot_fab_cost")]."','".$row[csf("tot_sp_operation_cost")]."','".$row[csf("tot_accessories_cost")]."','".$row[csf("tot_cm_cost")]."','".$row[csf("tot_fright_cost")]."','".$row[csf("tot_lab_test_cost")]."','".$row[csf("tot_miscellaneous_cost")]."','".$row[csf("tot_other_cost")]."','".$row[csf("tot_commission_cost")]."','".$row[csf("tot_cost")]."','".$row[csf("tot_fob_cost")]."','".$row[csf("tot_rmg_ratio")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_summary);
		
		//$field_arr['qc_item_summ']="id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
		
		$sql_item_summ=sql_select("select id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, cpm, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_item_cost_summary where 1=1 $mst_id_cond order by id ASC");//$mst_id_cond
		
		foreach($sql_item_summ as $row)
		{
			$data_arr['qc_item_summ'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("fabric_cost")]."','".$row[csf("sp_operation_cost")]."','".$row[csf("accessories_cost")]."','".$row[csf("smv")]."','".$row[csf("efficiency")]."','".$row[csf("cm_cost")]."','".$row[csf("frieght_cost")]."','".$row[csf("lab_test_cost")]."','".$row[csf("miscellaneous_cost")]."','".$row[csf("offer_qty")]."','".$row[csf("commission_cost")]."','".$row[csf("fob_pcs")]."','".$row[csf("rmg_ratio")]."','".$row[csf("cpm")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_item_summ);
		
		$sql_confirmmst=sql_select("select id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, job_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, approved, approved_by, $approvedDate_cond as approved_date, status_active, is_deleted from qc_confirm_mst where 1=1 $confirm_qc_no_cond order by id ASC");//$confirm_qc_no_cond
		
		foreach($sql_confirmmst as $row)
		{
			if($db_type==0)
			{
				if($row[csf("ship_date")]!='0000-00-00') $ship_date=date("d-M-Y",strtotime($row[csf("ship_date")]));
				else $ship_date='';
			}
			else if($db_type==2)
			{
				$ship_date=date("Y-m-d",strtotime($row[csf("ship_date")]));
			}
			
			$data_arr['qc_confirmmst'][]="('".$row[csf("id")]."','".$row[csf("cost_sheet_id")]."','".$row[csf("lib_item_id")]."','".$row[csf("confirm_style")]."','".$row[csf("confirm_order_qty")]."','".$row[csf("confirm_fob")]."','".$row[csf("deal_merchant")]."','".$ship_date."','".$row[csf("job_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("approved")]."','".$row[csf("approved_by")]."','".$row[csf("approved_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_confirmmst);
		
		$sql_confirmdtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio, fab_cons_mtr, cppm_amount, smv_amount, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_confirm_dtls where 1=1 $confirm_qc_no_cond order by id ASC");// $confirm_qc_no_cond
		
		foreach($sql_confirmdtls as $row)
		{
			$data_arr['qc_confirmdtls'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("cost_sheet_id")]."','".$row[csf("item_id")]."','".$row[csf("fab_cons_kg")]."','".$row[csf("fab_cons_yds")]."','".$row[csf("fab_amount")]."','".$row[csf("sp_oparation_amount")]."','".$row[csf("acc_amount")]."','".$row[csf("fright_amount")]."','".$row[csf("lab_amount")]."','".$row[csf("misce_amount")]."','".$row[csf("other_amount")]."','".$row[csf("comm_amount")]."','".$row[csf("fob_amount")]."','".$row[csf("cm_amount")]."','".$row[csf("rmg_ratio")]."','".$row[csf("fab_cons_mtr")]."','".$row[csf("cppm_amount")]."','".$row[csf("smv_amount")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_confirmdtls);
		
		if($db_type==0) $meeting_time="TIME_FORMAT( meeting_time, '%H:%i')";
		else if($db_type==2) $meeting_time="TO_CHAR(meeting_time,'HH24:MI')";
		
		$sql_meeting_mst=sql_select("select id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, $meeting_time as meeting_time, remarks, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_meeting_mst where 1=1 $mst_id_cond order by id ASC");// $mst_id_cond
		
		foreach($sql_meeting_mst as $row)
		{
			if($db_type==0)
			{
				if($row[csf("meeting_date")]!='0000-00-00') $meeting_date=date("d-M-Y",strtotime($row[csf("meeting_date")]));
				else $meeting_date='';
				
			}
			else if($db_type==2)
			{
				$meeting_date=date("Y-m-d",strtotime($row[csf("meeting_date")]));
			}
			
			$data_arr['qc_meetingmst'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("meeting_no")]."','".$row[csf("buyer_agent_id")]."','".$row[csf("location_id")]."','".$meeting_date."','".$row[csf("meeting_time")]."','".$row[csf("remarks")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_meeting_mst);
	}
	else if ($db_type==0)
	{
		$restore_time=return_field_value("max(restore_time) as restore_time", "qc_restore_history", "status_active=1 and is_deleted=0","restore_time");
		$restore_time_change_db=return_field_value("$restore_time_change_db_cond as restore_time", "qc_restore_history", "status_active=1 and is_deleted=0","restore_time");
		
		$date_cond="and (insert_date>='$restore_time') or update_date>='$restore_time'";
		//and (b.booking_no in ($returnBooking) $reqRet_cond)
		//echo "select id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from qc_template where 1=1 $date_cond order by id ASC"; //die;
		$sql_qc_temp=sql_select("select id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from qc_template where 1=1 $date_cond order by id ASC");
		foreach($sql_qc_temp as $row)
		{
			
			$data_arr['qc_template'][]="('','".$row[csf("temp_id")]."','".$row[csf("item_id1")]."','".$row[csf("ratio1")]."','".$row[csf("item_id2")]."','".$row[csf("ratio2")]."','".$row[csf("lib_item_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("tuid")]."')";
		}
		//print_r($data_arr['qc_template']); die;
		unset($sql_qc_temp);
		
		$sql_qc_stage=sql_select("select id, stage_name, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from lib_stage_name where 1=1 $date_cond order by id ASC");
		foreach($sql_qc_stage as $row)
		{
			$data_arr['qc_stage'][]="('','".$row[csf("stage_name")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("tuid")]."')"; 
		}
		unset($sql_qc_stage);
		
		$sql_qc_lib_agent_loc=sql_select("select id, type, agent_location, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, tuid from lib_agent_location where 1=1 $date_cond order by id ASC");
		foreach($sql_qc_lib_agent_loc as $row)
		{
			$data_arr['qc_lib_agent_loc'][]="('','".$row[csf("type")]."','".$row[csf("agent_location")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("tuid")]."')"; 
		}
		unset($sql_qc_lib_agent_loc);
		//print_r($data_arr['qc_lib_agent_loc']); 
		
		$sql_mst=sql_select("Select id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref,  department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, lib_item_id, pre_cost_sheet_id, revise_no, option_id, option_remarks, buyer_remarks, meeting_no, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted, qc_no, uom, approved, approved_by, $approvedDate_cond as approved_date from qc_mst where 1=1 $date_cond order by id ASC");// or die(mysql_error());
		
		$add_comma=0; $mstIds=''; $tot_rows=0;
		foreach($sql_mst as $row)
		{
			if($db_type==0)
			{
				if($row[csf("costing_date")]!='0000-00-00') $costing_date=date("d-M-Y",strtotime($row[csf("costing_date")]));
				else $costing_date='';
				
				if($row[csf("delivery_date")]!='0000-00-00') $delivery_date=date("d-M-Y",strtotime($row[csf("delivery_date")]));
				else $delivery_date='';
			}
			else if($db_type==2)
			{
				$costing_date=date("Y-m-d",strtotime($row[csf("costing_date")]));//change_date_format($row[csf("costing_date")],'yyyy-mm-dd');
				$delivery_date=date("Y-m-d",strtotime($row[csf("delivery_date")]));
			}
			$tot_rows++;
			$mstIds.=$row[csf("qc_no")].",";
			if(strtotime($row[csf("insert_date")])>strtotime($restore_time_change_db))
			{
				$data_arr['qc_mst'][]="('','".$row[csf("cost_sheet_id")]."','".$row[csf("cost_sheet_no")]."','".$row[csf("temp_id")]."','".$row[csf("style_des")]."','".$row[csf("buyer_id")]."','".$row[csf("cons_basis")]."','".$row[csf("season_id")]."','".$row[csf("style_ref")]."','".$row[csf("department_id")]."','".$delivery_date."','".$row[csf("exchange_rate")]."','".$row[csf("offer_qty")]."','".$row[csf("quoted_price")]."','".$row[csf("tgt_price")]."','".$row[csf("stage_id")]."','".$costing_date."','".trim($row[csf("lib_item_id")])."','".$row[csf("pre_cost_sheet_id")]."','".$row[csf("revise_no")]."','".$row[csf("option_id")]."','".$row[csf("option_remarks")]."','".$row[csf("buyer_remarks")]."','".$row[csf("meeting_no")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."','".$row[csf("qc_no")]."','".$row[csf("uom")]."','".$row[csf("approved")]."','".$row[csf("approved_by")]."','".$row[csf("approved_date")]."','1')";
			}
			else
			{
				$data_arr['qc_mstid'][]=$row[csf("qc_no")];
				$data_arr['qc_mstup'][$row[csf("qc_no")]]=explode("*",("'".$row[csf("temp_id")]."'*'".trim($row[csf("lib_item_id")])."'*'".strtoupper($row[csf("style_ref")])."'*'".$row[csf("buyer_id")]."'*'".$row[csf("cons_basis")]."'*'".$row[csf("season_id")]."'*'".$row[csf("style_des")]."'*'".$row[csf("department_id")]."'*'".$delivery_date."'*'".$row[csf("exchange_rate")]."'*'".$row[csf("offer_qty")]."'*'".$row[csf("quoted_price")]."'*'".$row[csf("tgt_price")]."'*'".$row[csf("stage_id")]."'*'".$costing_date."'*'".$row[csf("buyer_remarks")]."'*'".$row[csf("option_remarks")]."'*'".$row[csf("meeting_no")]."'*'".$row[csf("updated_by")]."'*'".$row[csf("update_date")]."'*'".$row[csf("status_active")]."'*'".$row[csf("is_deleted")]."'*'".$row[csf("uom")]."'*'".$row[csf("approved")]."'*'".$row[csf("approved_by")]."'*'".$row[csf("approved_date")]."'"));
			} 
		}
		//print_r($data_arr['qc_mst']);
		unset($sql_mst);
		$mstIds=chop($mstIds,',');
		if($db_type==2 && $tot_rows>1000)
		{
			$mst_id_cond=" and (";
			$mstIdsArr=array_chunk(explode(",",$mst_Ids),999);
			foreach($mstIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$mst_id_cond.=" mst_id in($ids) or ";
			}
			$mst_id_cond=chop($mst_id_cond,'or ');
			$mst_id_cond.=")";
		}
		else
		{
			$mst_id_cond=" and mst_id in ($mstIds)";
		}
		
		$sql_qc_fab_cost=sql_select("select id, mst_id, item_id, body_part, des, value, alw, uniq_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_fabric_dtls where 1=1 $date_cond order by id ASC");//$mst_id_cond
		
		foreach($sql_qc_fab_cost as $row)
		{
			if(strtotime($row[csf("insert_date")])>strtotime($restore_time_change_db))
			{
				$data_arr['qc_fab_cost'][]="('','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("body_part")]."','".$row[csf("des")]."','".$row[csf("value")]."','".$row[csf("alw")]."','".$row[csf("uniq_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
			}
			else
			{
				$data_arr['qc_fab_cost_id'][]=$row[csf("id")];
				$data_arr['qc_fab_cost_up'][$row[csf("id")]]=explode("*",("'".$row[csf("item_id")]."'*'".$row[csf("uniq_id")]."'*'".$row[csf("body_part")]."'*'".$row[csf("des")]."'*'".$row[csf("value")]."'*'".$row[csf("alw")]."'*'".$row[csf("updated_by")]."'*'".$row[csf("update_date")]."'*'".$row[csf("status_active")]."'*'".$row[csf("is_deleted")]."'"));
			}
		}
		unset($sql_qc_fab_cost);
		
		$sql_cons_rate=sql_select("select id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, ex_percent, tot_cons, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_cons_rate_dtls where 1=1 $date_cond order by id ASC");//$mst_id_cond
		
		foreach($sql_cons_rate as $row)
		{
			$data_arr['qc_cons_rate'][]="('','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("type")]."','".$row[csf("particular_type_id")]."','".$row[csf("formula")]."','".$row[csf("consumption")]."','".$row[csf("unit")]."','".$row[csf("is_calculation")]."','".$row[csf("rate")]."','".$row[csf("rate_data")]."','".$row[csf("value")]."','".$row[csf("ex_percent")]."','".$row[csf("tot_cons")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_cons_rate);
		//print_r($data_arr['qc_cons_rate']);
		//echo "10**";
		$sql_fabstrdata=sql_select("select id, mst_id, item_id, body_part_data, des_val_alw_data, fab_cons_rate_data, special_operation_data, accessories_data, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_fabric_string_data where 1=1 $date_cond order by id ASC");
		foreach($sql_fabstrdata as $row)
		{
			$data_arr['qc_fabstrdata'][]="('','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("body_part_data")]."','".$row[csf("des_val_alw_data")]."','".$row[csf("fab_cons_rate_data")]."','".$row[csf("special_operation_data")]."','".$row[csf("accessories_data")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		//print_r($data_arr['qc_fabstrdata']); die;
		unset($sql_fabstrdata);
		
		$sql_summary=sql_select("select id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_tot_cost_summary where 1=1 $date_cond order by id ASC");
		
		foreach($sql_summary as $row)
		{
			$data_arr['qc_summary'][]="('','".$row[csf("mst_id")]."','".$row[csf("buyer_agent_id")]."','".$row[csf("location_id")]."','".$row[csf("no_of_pack")]."','".$row[csf("is_confirm")]."','".$row[csf("is_cm_calculative")]."','".$row[csf("mis_lumsum_cost")]."','".$row[csf("commision_per")]."','".$row[csf("tot_fab_cost")]."','".$row[csf("tot_sp_operation_cost")]."','".$row[csf("tot_accessories_cost")]."','".$row[csf("tot_cm_cost")]."','".$row[csf("tot_fright_cost")]."','".$row[csf("tot_lab_test_cost")]."','".$row[csf("tot_miscellaneous_cost")]."','".$row[csf("tot_other_cost")]."','".$row[csf("tot_commission_cost")]."','".$row[csf("tot_cost")]."','".$row[csf("tot_fob_cost")]."','".$row[csf("tot_rmg_ratio")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
		}
		unset($sql_summary);
		
		$sql_item_summ=sql_select("select id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, cpm, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_item_cost_summary where 1=1 $date_cond order by id ASC");
		
		foreach($sql_item_summ as $row)
		{
			$data_arr['qc_item_summ'][]="('','".$row[csf("mst_id")]."','".$row[csf("item_id")]."','".$row[csf("fabric_cost")]."','".$row[csf("sp_operation_cost")]."','".$row[csf("accessories_cost")]."','".$row[csf("smv")]."','".$row[csf("efficiency")]."','".$row[csf("cm_cost")]."','".$row[csf("frieght_cost")]."','".$row[csf("lab_test_cost")]."','".$row[csf("miscellaneous_cost")]."','".$row[csf("offer_qty")]."','".$row[csf("commission_cost")]."','".$row[csf("fob_pcs")]."','".$row[csf("rmg_ratio")]."','".$row[csf("cpm")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
		}
		unset($sql_item_summ);
		
		$sql_confirmmst=sql_select("select id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, job_id, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, approved, approved_by, $approvedDate_cond as approved_date, status_active, is_deleted from qc_confirm_mst where 1=1 $date_cond order by id ASC");
		
		foreach($sql_confirmmst as $row)
		{
			if($db_type==0)
			{
				if($row[csf("ship_date")]!='0000-00-00') $ship_date=date("d-M-Y",strtotime($row[csf("ship_date")]));
				else $ship_date='';
			}
			else if($db_type==2)
			{
				$ship_date=date("Y-m-d",strtotime($row[csf("ship_date")]));
			}
			if(strtotime($row[csf("insert_date")])>strtotime($restore_time_change_db))
			{
				$data_arr['qc_confirmmst'][]="('','".$row[csf("cost_sheet_id")]."','".$row[csf("lib_item_id")]."','".$row[csf("confirm_style")]."','".$row[csf("confirm_order_qty")]."','".$row[csf("confirm_fob")]."','".$row[csf("deal_merchant")]."','".$ship_date."','".$row[csf("job_id")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("approved")]."','".$row[csf("approved_by")]."','".$row[csf("approved_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
			}
			else
			{
				$data_arr['qc_confirmmst_id'][]=$row[csf("id")];
				$data_arr['qc_confirmmst_up'][$row[csf("id")]]=explode("*",("'".$row[csf("confirm_style")]."'*'".$row[csf("confirm_order_qty")]."'*'".$row[csf("confirm_fob")]."'*".$ship_date."*'".$row[csf("job_id")]."'*'".$row[csf("updated_by")]."'*'".$row[csf("update_date")]."'*'".$row[csf("approved")]."'*'".$row[csf("approved_by")]."'*'".$row[csf("approved_date")]."'*'".$row[csf("status_active")]."'*'".$row[csf("is_deleted")]."'"));
			}
		}
		unset($sql_confirmmst);
		
		$sql_confirmdtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio, fab_cons_mtr, cppm_amount, smv_amount, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_confirm_dtls where 1=1 $date_cond order by id ASC");
		
		foreach($sql_confirmdtls as $row)
		{
			if(strtotime($row[csf("insert_date")])>strtotime($restore_time_change_db))
			{
				$data_arr['qc_confirmdtls'][]="('','".$row[csf("mst_id")]."','".$row[csf("cost_sheet_id")]."','".$row[csf("item_id")]."','".$row[csf("fab_cons_kg")]."','".$row[csf("fab_cons_yds")]."','".$row[csf("fab_amount")]."','".$row[csf("sp_oparation_amount")]."','".$row[csf("acc_amount")]."','".$row[csf("fright_amount")]."','".$row[csf("lab_amount")]."','".$row[csf("misce_amount")]."','".$row[csf("other_amount")]."','".$row[csf("comm_amount")]."','".$row[csf("fob_amount")]."','".$row[csf("cm_amount")]."','".$row[csf("rmg_ratio")]."','".$row[csf("fab_cons_mtr")]."','".$row[csf("cppm_amount")]."','".$row[csf("smv_amount")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')";
			}
			else
			{
				$data_arr['qc_confirmdtls_id'][]=$row[csf("id")];
				$data_arr['qc_confirmdtls_up'][$row[csf("id")]] =explode("*",("'".$row[csf("fab_cons_kg")]."'*'".$row[csf("fab_cons_yds")]."'*'".$row[csf("fab_amount")]."'*'".$row[csf("sp_oparation_amount")]."'*'".$row[csf("acc_amount")]."'*'".$row[csf("fright_amount")]."'*'".$row[csf("lab_amount")]."'*'".$row[csf("misce_amount")]."'*'".$row[csf("other_amount")]."'*'".$row[csf("comm_amount")]."'*'".$row[csf("fob_amount")]."'*'".$row[csf("cm_amount")]."'*'".$row[csf("rmg_ratio")]."'*'".$row[csf("fab_cons_mtr")]."'*'".$row[csf("cppm_amount")]."'*'".$row[csf("smv_amount")]."'*'".$row[csf("updated_by")]."'*'".$row[csf("update_date")]."'*'".$row[csf("status_active")]."'*'".$row[csf("is_deleted")]."'"));
			}
		}
		unset($sql_confirmdtls);
		
		if($db_type==0) $meeting_time="TIME_FORMAT( meeting_time, '%H:%i')";
		else if($db_type==2) $meeting_time="TO_CHAR(meeting_time,'HH24:MI')";
		
		$sql_meeting_mst=sql_select("select id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, $meeting_time as meeting_time, remarks, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_meeting_mst where 1=1 $date_cond order by id ASC");
		
		foreach($sql_meeting_mst as $row)
		{
			if($db_type==0)
			{
				if($row[csf("meeting_date")]!='0000-00-00') $meeting_date=date("d-M-Y",strtotime($row[csf("meeting_date")]));
				else $meeting_date='';
				$meeting_time=$meeting_date." ".$row[csf("meeting_time")];
				$meeting_time="to_date('".$meeting_time."','DD MONTH YYYY HH24:MI:SS')";
			}
			else if($db_type==2)
			{
				$meeting_date=date("Y-m-d",strtotime($row[csf("meeting_date")]));
				$meeting_time=date("%H:%i",$row[csf("meeting_time")]); 
			}
			if(strtotime($row[csf("insert_date")])>strtotime($restore_time_change_db))
			{
				$data_arr['qc_meetingmst'][]="('','".$row[csf("mst_id")]."','".$row[csf("meeting_no")]."','".$row[csf("buyer_agent_id")]."','".$row[csf("location_id")]."','".$meeting_date."',".$meeting_time.",'".$row[csf("remarks")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
			}
			else
			{
				$data_arr['qc_meetingmst_id'][]=$row[csf("id")];
				$data_arr['qc_meetingmst_up'][$row[csf("id")]]=explode("*",("'".$row[csf("buyer_agent_id")]."'*'".$row[csf("location_id")]."'*'".$meeting_date."'*".$meeting_time."*'".$row[csf("remarks")]."'*'".$row[csf("updated_by")]."'*'".$row[csf("update_date")]."'*'".$row[csf("status_active")]."'*'".$row[csf("is_deleted")]."'"));
			}
		}
		//print_r($data_arr['qc_meetingmst']); die;
		unset($sql_meeting_mst);
	}
	//print_r($data_arr['qc_meetingmst']);
	
	/*$sql_meeting_person=sql_select("select id, mst_id, dtls_id, name, organization, designation, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_meeting_person where 1=1 order by id ASC");
	
	foreach($sql_meeting_person as $row)
	{
		$data_arr['qc_meetingperson'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("dtls_id")]."','".$row[csf("name")]."','".$row[csf("organization")]."','".$row[csf("designation")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
	}
	
	$sql_meeting_dtls=sql_select("select id, mst_id, dtls_id, particulars, inserted_by, $insert_cond as insert_date, updated_by, $update_cond as update_date, status_active, is_deleted from qc_meeting_dtls where 1=1 order by id ASC");
	
	foreach($sql_meeting_dtls as $row)
	{
		$data_arr['qc_meetingdtls'][]="('".$row[csf("id")]."','".$row[csf("mst_id")]."','".$row[csf("dtls_id")]."','".$row[csf("name")]."','".$row[csf("organization")]."','".$row[csf("designation")]."','".$row[csf("inserted_by")]."','".$row[csf("insert_date")]."','".$row[csf("updated_by")]."','".$row[csf("update_date")]."','".$row[csf("status_active")]."','".$row[csf("is_deleted")]."')"; 
	}*/
	
	
	//print_r($data_arr['qc_confirmdtls']); die;
	$file_name="qc_".date("Y-m-d_H-i-s").".txt";
	foreach (glob("../../Database/"."*.txt") as $deleteFile)
	{			
		@unlink($deleteFile);
	}
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$idrehist=return_next_id( "id", "qc_restore_history", 1) ;
	
	$field_arr_hist="id, backup_user, backup_time, file_name, status_active, is_deleted, is_lock";
	$data_arr_hist="(".$idrehist.",".$user_id.",'".$pc_date_time."','".$file_name."',1,0,0)"; 
	 
	$rID=sql_insert("qc_restore_history",$field_arr_hist,$data_arr_hist,1);
	if($db_type==0)
	{
		if($rID ){
			mysql_query("COMMIT");  
			echo "0**";
		}
		else{
			mysql_query("ROLLBACK"); 
			echo "10**";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID ){
			oci_commit($con);
			echo "0**";
		}
		else{
			oci_rollback($con);
			echo "10**";
		}
	}
	
	$fileName= "../Database/".$file_name;
	$file_folder = "../".$fileName;
	$myfile = fopen($file_folder, "w") or die("Unable to open file!");
	$txt = json_encode($data_arr);

	fwrite($myfile, $txt);
	fclose($myfile);
	echo "3****".$fileName;
	disconnect($con);
    exit();
}
?>
 
    