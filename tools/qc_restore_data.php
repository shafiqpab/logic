<?
 header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if( isset($_POST["submit"]) )
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	extract($_REQUEST);
	
	foreach (glob("../Database/"."*.txt") as $filename){			
		@unlink($filename);
	}
	
	$source = $_FILES['uploadfile']['tmp_name'];
	//echo str_replace('Database_','',$_FILES['uploadfile']['name']);
	$file_name=$_FILES['uploadfile']['name'];
	$targetdir ='../Database/';
	
	if($db_type==2)
	{
		if (strpos($file_name, '.._Database_') !== false) {
			$ins_file_name =str_replace(".._Database_","",$file_name);
		}
		else{
			$ins_file_name =str_replace("_Database_","",$file_name);
		}
		$restore_file=return_field_value("restore_time as restore_time", "qc_restore_history", "status_active=1 and is_deleted=0 and file_name='$ins_file_name'","restore_time");
		
		if($restore_file!="")
		{
			echo "10**"."This File already Uploaded. Upload Time & Date : ".$restore_file; die;
		}
	}
	
	if (strpos($file_name, '.._Database_') !== false) {
		$targetzip =$targetdir.str_replace(".._Database_","",$file_name);
	}
	else{
		$targetzip =$targetdir.str_replace("_Database_","",$file_name);
	}
	//$targetzip="../Database/qc_2017-10-10_11-29-56.txt";
	if (move_uploaded_file($source, $targetzip)) 
	{
		$myfile = fopen($targetzip, "r") or die("Unable to open file!");
	}
	else
	{
		echo "Unable to open file!"; die;
	}
	//echo fopen($targetzip, "r");
	$readfile =json_decode(fread($myfile,filesize($targetzip)), true);
	
	fclose($myfile); //echo implode(',',$readfile['qc_mst']); die;
	
	/*$targetzip ='../Database/qc_2017-10-10_11-29-56.txt';//$targetdir.str_replace("_Database_","",$file_name);
	$myfile = fopen($targetzip, "r") or die("Unable to open file!");
	$readfile =json_decode(fread($myfile,filesize($targetzip)), true);
	echo implode(',',$readfile['qc_fab_cost']); die;
	fclose($myfile);*/ 
	
	if($db_type==0)
	{
		$data_userpass=implode(',',$readfile['lib_userpass']);
		$data_buyer=implode(',',$readfile['lib_buyer']);
		$data_bParty=implode(',',$readfile['lib_bParty']);
		$data_btagCom=implode(',',$readfile['lib_btagCom']);
		$data_designation=implode(',',$readfile['lib_designation']);
		$data_department=implode(',',$readfile['lib_department']);
		$data_mktmst=implode(',',$readfile['lib_mktmst']);
		$data_mktdtls=implode(',',$readfile['lib_mktdtls']);
		$data_subDept=implode(',',$readfile['lib_subDept']);
		$data_stdcm=implode(',',$readfile['lib_stdcm']);
		$data_season=implode(',',$readfile['lib_season']);
		$data_item_group=implode(',',$readfile['lib_item_group']);
		$data_lib_template=implode(',',$readfile['lib_template']);
		$data_template=implode(',',$readfile['qc_template']);
		$data_stage=implode(',',$readfile['qc_stage']);
		$data_lib_agent_loc=implode(',',$readfile['qc_lib_agent_loc']);
		$data_mst=implode(',',$readfile['qc_mst']);
		$data_fab_cost=implode(',',$readfile['qc_fab_cost']);
		$data_cons_rate=implode(',',$readfile['qc_cons_rate']);
		$data_fabstrdata=implode(',',$readfile['qc_fabstrdata']);
		$data_summary=implode(',',$readfile['qc_summary']);
		$data_item_summ=implode(',',$readfile['qc_item_summ']);
		$data_confirmmst=implode(',',$readfile['qc_confirmmst']);
		$data_confirmdtls=implode(',',$readfile['qc_confirmdtls']);
		
		$data_meetingmst=implode(',',$readfile['qc_meetingmst']);
		//$data_meetingperson=implode(',',$readfile['qc_meetingperson']);
		//$data_meetingdtls=implode(',',$readfile['qc_meetingdtls']);
	}
	else if($db_type==2)
	{
		/*$data_userpass=array_chunk($readfile['lib_userpass'],20);
		$data_buyer=array_chunk($readfile['lib_buyer'],20);
		$data_bParty=array_chunk($readfile['lib_bParty'],20);
		$data_btagCom=array_chunk($readfile['lib_btagCom'],20);
		$data_designation=array_chunk($readfile['lib_designation'],20);
		$data_department=array_chunk($readfile['lib_department'],20);
		$data_mktmst=array_chunk($readfile['lib_mktmst'],20);
		$data_mktdtls=array_chunk($readfile['lib_mktdtls'],20);
		$data_subDept=array_chunk($readfile['lib_subDept'],20);
		$data_stdcm=array_chunk($readfile['lib_stdcm'],20);
		$data_season=array_chunk($readfile['lib_season'],20);
		$data_item_group=array_chunk($readfile['lib_item_group'],20);
		$data_lib_template=array_chunk($readfile['lib_template'],20);*/
		//insert data array
		$data_template=array_chunk($readfile['qc_template'],20);
		$data_stage=array_chunk($readfile['qc_stage'],20);
		$data_lib_agent_loc=array_chunk($readfile['qc_lib_agent_loc'],20);
		$data_mst=array_chunk($readfile['qc_mst'],20);
		$data_fab_cost=array_chunk($readfile['qc_fab_cost'],20);
		$data_cons_rate=array_chunk($readfile['qc_cons_rate'],20);
		$data_fabstrdata=array_chunk($readfile['qc_fabstrdata'],20);
		$data_summary=array_chunk($readfile['qc_summary'],20);
		$data_item_summ=array_chunk($readfile['qc_item_summ'],20);
		$data_confirmmst=array_chunk($readfile['qc_confirmmst'],20);
		$data_confirmdtls=array_chunk($readfile['qc_confirmdtls'],20);
		$data_meetingmst=array_chunk($readfile['qc_meetingmst'],1);
		//update data array
		$data_mst_up=$readfile['qc_mstup'];
		$data_mst_id=$readfile['qc_mstid'];
		$data_fab_cost_id=$readfile['qc_fab_cost_id'];
		$data_fab_cost_up=$readfile['qc_fab_cost_up'];
		$data_confirmmst_id=$readfile['qc_confirmmst_id'];
		$data_confirmmst_up=$readfile['qc_confirmmst_up'];
		$data_confirmdtls_id=$readfile['qc_confirmdtls'];
		$data_confirmdtls_up=$readfile['qc_confirmdtls'];
		$data_meetingmst_id=$readfile['qc_confirmdtls_id'];
		$data_meetingmst_up=$readfile['qc_confirmdtls_up'];
		
		//$data_meetingperson=array_chunk($readfile['qc_meetingperson'],20);
		//$data_meetingdtls=array_chunk($readfile['qc_meetingdtls'],20);
	}
	//echo $data_mst; die;
	
	$field_arr_user_pass="id, employee_id, user_name, password, user_full_name, designation, created_on, created_by, access_ip, access_proxy_ip, expire_on, user_level, buyer_id, unit_id, is_data_level_secured, valid, department_id, supplier_id, item_cate_id, company_location_id, store_location_id, user_email, is_fst_time, reset_code, graph_id, row_status";
	
	$field_arr_buyer="id, buyer_name, short_name, contact_person, exporters_reference, party_type, designation, tag_company, country_id, web_site, buyer_email, address_1, address_2, address_3, address_4, remark, supllier, credit_limit_days, credit_limit_amount, credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted, sewing_effi_mkt_percent, sewing_effi_plaing_per, marketing_team_id, control_delivery, vat_reg_no, cut_off_used, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_bParty="id, buyer_id, party_type";
	
	$field_arr_btagCom="id, buyer_id, tag_company";
	
	$field_arr_designation="id, level_des, system_designation, custom_designation, custom_designation_local, allowance_rate, allowance_treatment, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_locked";
	
	$field_arr_department="id, department_name, division_id, contact_person, contact_no, country_id, website, email, short_name, address, remark, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_locked";
	
	$field_arr_mktmst="id, team_name, team_leader_name, team_leader_desig, team_leader_email, lib_mkt_team_member_info_id, total_member, capacity_smv, capacity_basic, team_contact_no, user_tag_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_mktdtls="id, team_id, designation, team_member_name, team_member_email, capacity_smv_member, capacity_basic_member, member_contact_no, user_tag_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_subdept="id, sub_department_name, department_id, buyer_id, inserted_by, insert_date, update_by, update_date, status_active, is_deleted";
	
	$field_arr_cmstd="id, company_id, applying_period_date, applying_period_to_date, bep_cm, asking_profit, asking_cm, monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, asking_avg_rate, actual_cm, max_profit, depreciation_amorti, interest_expense, income_tax, operating_expn, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_locked ";
	
	$field_arr_season="id, buyer_id, season_name, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_item_group="id, item_name, trim_type, remark, order_uom, trim_uom, item_category, item_group_code, conversion_factor, fancy_item, cal_parameter, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_lib_template="id, item_id, item_name, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_template="id, temp_id, item_id1, ratio1, item_id2, ratio2, lib_item_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, tuid";
	
	$field_arr_stage="id, stage_name, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, tuid";
	
	$field_arr_lib_agent_loc="id, type, agent_location, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, tuid";
	
	$field_arr_mst="id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref,  department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, lib_item_id, pre_cost_sheet_id, revise_no, option_id, option_remarks, buyer_remarks, meeting_no, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, qc_no, uom, approved, approved_by, approved_date, from_client";
	
	$field_arr_fab_cost="id, mst_id, item_id, body_part, des, value, alw, uniq_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_cons_rate="id, mst_id, item_id, type, particular_type_id, formula, consumption, unit, is_calculation, rate, rate_data, value, ex_percent, tot_cons, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_fabstrdata="id, mst_id, item_id, body_part_data, des_val_alw_data, fab_cons_rate_data, special_operation_data, accessories_data, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_summary="id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_item_summ="id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, rmg_ratio, cpm, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_confirmmst="id, cost_sheet_id, lib_item_id, confirm_style, confirm_order_qty, confirm_fob, deal_merchant, ship_date, job_id, inserted_by, insert_date, updated_by, update_date, approved, approved_by, approved_date, status_active, is_deleted";
	
	$field_arr_confirmdtls="id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cm_amount, rmg_ratio, fab_cons_mtr, cppm_amount, smv_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_meetingmst="id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	//$field_arr_meetingperson="id, mst_id, dtls_id, name, organization, designation, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	//$field_arr_meetingdtls="id, mst_id, dtls_id, particulars, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted";
	
	$field_arr_mst_up="temp_id*lib_item_id*style_ref*buyer_id*cons_basis*season_id*style_des*department_id*delivery_date*exchange_rate*offer_qty*quoted_price*tgt_price*stage_id*costing_date*buyer_remarks*option_remarks*meeting_no*updated_by*update_date*status_active*is_deleted*uom*approved*approved_by*approved_date";
	
	$field_arr_fab_cost_up="item_id*uniq_id*body_part*des*value*alw*updated_by*update_date*status_active*is_deleted";
	
	$field_arr_confirmmst_up="confirm_style*confirm_order_qty*confirm_fob*ship_date*job_id*updated_by*update_date*approved*approved_by*approved_date*status_active*is_deleted";
	
	$field_arr_confirmdtls_up="fab_cons_kg*fab_cons_yds*fab_amount*sp_oparation_amount*acc_amount*fright_amount*lab_amount*misce_amount*other_amount*comm_amount*fob_amount*cm_amount*rmg_ratio*fab_cons_mtr*cppm_amount*smv_amount*updated_by*update_date*status_active*is_deleted";
	
	$field_arr_meetingmst_up="buyer_agent_id*location_id*meeting_date*meeting_time*remarks*updated_by*update_date*status_active*is_deleted";
	
	
	
	/*$qry_season="insert into qc_template ($field_arr_template) values".$data_template;
	echo $qry_season;
	//execute_query($qry_season,1);
	die;*/
	if($db_type==0)
	{
		execute_query("TRUNCATE TABLE user_passwd",1);
		execute_query("TRUNCATE TABLE lib_buyer",1);
		execute_query("TRUNCATE TABLE lib_buyer_party_type",1);
		execute_query("TRUNCATE TABLE lib_buyer_tag_company",1);
		execute_query("TRUNCATE TABLE lib_designation",1);
		execute_query("TRUNCATE TABLE lib_department",1);
		execute_query("TRUNCATE TABLE lib_marketing_team",1);
		execute_query("TRUNCATE TABLE lib_mkt_team_member_info",1);
		execute_query("TRUNCATE TABLE lib_pro_sub_deparatment",1);
		execute_query("TRUNCATE TABLE lib_standard_cm_entry",1);
		execute_query("TRUNCATE TABLE lib_item_group",1);
		execute_query("TRUNCATE TABLE lib_buyer_season",1);
		execute_query("TRUNCATE TABLE lib_stage_name",1);
		execute_query("TRUNCATE TABLE lib_qc_template",1);
		
		execute_query("TRUNCATE TABLE lib_agent_location",1);
		execute_query("TRUNCATE TABLE qc_template",1);
		execute_query("TRUNCATE TABLE qc_mst",1);
		execute_query("TRUNCATE TABLE qc_fabric_dtls",1);
		execute_query("TRUNCATE TABLE qc_cons_rate_dtls",1);
		execute_query("TRUNCATE TABLE qc_fabric_string_data",1);
		execute_query("TRUNCATE TABLE qc_tot_cost_summary",1);
		execute_query("TRUNCATE TABLE qc_item_cost_summary",1);
		execute_query("TRUNCATE TABLE qc_confirm_mst",1);
		execute_query("TRUNCATE TABLE qc_confirm_dtls",1);
		execute_query("TRUNCATE TABLE qc_meeting_mst",1);
	}
	
	//execute_query("TRUNCATE TABLE qc_meeting_person",1);
	//execute_query("TRUNCATE TABLE qc_meeting_dtls",1);
	$flag=1;
	$roll_back_msg="Data not save.";
	$commit_msg="Data Save Successfully.";
	if($db_type==0)
	{
		$rID=sql_insert("user_passwd", $field_arr_user_pass, $data_userpass,0);
		if($rID) $flag=1; else $flag=0;
		$rID1=sql_insert("lib_buyer", $field_arr_buyer, $data_buyer,0);
		if($rID1) $flag=1; else $flag=0;
		$rID2=sql_insert("lib_buyer_party_type", $field_arr_bParty, $data_bParty,0);
		if($rID2) $flag=1; else $flag=0;
		$rID3=sql_insert("lib_buyer_tag_company", $field_arr_btagCom, $data_btagCom,0);
		if($rID3) $flag=1; else $flag=0;
		$rID4=sql_insert("lib_designation", $field_arr_designation, $data_designation,0);
		if($rID4) $flag=1; else $flag=0;
		$rID5=sql_insert("lib_department", $field_arr_department, $data_department,0);
		if($rID5) $flag=1; else $flag=0;
		$rID6=sql_insert("lib_marketing_team", $field_arr_mktmst, $data_mktmst,0);
		if($rID6) $flag=1; else $flag=0;
		//echo $flag;
		$rID7=sql_insert("lib_mkt_team_member_info", $field_arr_mktdtls, $data_mktdtls,0);
		if($rID7) $flag=1; else $flag=0;
		$rID8=sql_insert("lib_pro_sub_deparatment", $field_arr_subdept, $data_subDept,0);
		if($rID8) $flag=1; else $flag=0;
		$rID9=sql_insert("lib_standard_cm_entry", $field_arr_cmstd, $data_stdcm,0);
		if($rID9) $flag=1; else $flag=0;
		$rID10=sql_insert("lib_item_group", $field_arr_item_group, $data_item_group,0);
		if($rID10) $flag=1; else $flag=0;
		$rID11=sql_insert("lib_buyer_season", $field_arr_season, $data_season,0);
		if($rID11) $flag=1; else $flag=0;
		$rID12=sql_insert("lib_stage_name", $field_arr_stage, $data_stage,0);
		if($rID12) $flag=1; else $flag=0;
		
		$rID24=sql_insert("lib_agent_location", $field_arr_lib_agent_loc, $data_lib_agent_loc,0);
		if($rID24) $flag=1; else $flag=0;
		
		$rID13=sql_insert("lib_qc_template", $field_arr_lib_template, $data_lib_template,0);
		if($rID13) $flag=1; else $flag=0;
		$rID14=sql_insert("qc_template", $field_arr_template, $data_template,1);
		if($rID14) $flag=1; else $flag=0;
		
		$rID15=sql_insert("qc_mst", $field_arr_mst, $data_mst,0);
		if($rID15) $flag=1; else $flag=0;
		//echo "INSERT INTO qc_mst (".$field_arr_mst.") VALUES ".$data_mst; die;
		//echo $flag.'kausar';
		$rID16=sql_insert("qc_fabric_dtls", $field_arr_fab_cost, $data_fab_cost,0);
		if($rID16) $flag=1; else $flag=0;
		
		$rID17=sql_insert("qc_cons_rate_dtls", $field_arr_cons_rate, $data_cons_rate,0);
		if($rID17) $flag=1; else $flag=0;
		
		$rID18=sql_insert("qc_fabric_string_data", $field_arr_fabstrdata, $data_fabstrdata,0);
		if($rID18) $flag=1; else $flag=0;
		$rID19=sql_insert("qc_tot_cost_summary", $field_arr_summary, $data_summary,0);
		if($rID19) $flag=1; else $flag=0;
		$rID20=sql_insert("qc_item_cost_summary", $field_arr_item_summ, $data_item_summ,0);
		if($rID20) $flag=1; else $flag=0;
		$rID21=sql_insert("qc_confirm_mst", $field_arr_confirmmst, $data_confirmmst,0);
		if($rID21) $flag=1; else $flag=0;
		$rID22=sql_insert("qc_confirm_dtls", $field_arr_confirmdtls, $data_confirmdtls,0);
		if($rID22) $flag=1; else $flag=0;
		
		$rID23=sql_insert("qc_meeting_mst", $field_arr_meetingmst, $data_meetingmst,0);
		if($rID23) $flag=1; else $flag=0;
		/*$rID24=sql_insert("qc_meeting_person", $field_arr_meetingperson, $data_meetingperson,0);
		if($rID24) $flag=1; else $flag=0;
		$rID25=sql_insert("qc_meeting_dtls", $field_arr_meetingdtls, $data_meetingdtls,0);
		if($rID25) $flag=1; else $flag=0;*/
	}
	else if ($db_type==2)
	{
		/*foreach( $data_userpass as $setRows)
		{
			$rID=sql_insert("user_passwd",$field_arr_user_pass,implode(",",$setRows),0);
			if($rID==1) $flag=1; //else $flag=0;
			else if($rID==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_buyer as $setRows)
		{
			$rID1=sql_insert("lib_buyer",$field_arr_buyer,implode(",",$setRows),0);
			if($rID1==1) $flag=1; //else $flag=0;
			else if($rID1==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_bParty as $setRows)
		{
			$rID2=sql_insert("lib_buyer_party_type",$field_arr_bParty,implode(",",$setRows),0);
			if($rID2==1) $flag=1; //else $flag=0;
			else if($rID2==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_btagCom as $setRows)
		{
			$rID3=sql_insert("lib_buyer_tag_company",$field_arr_btagCom,implode(",",$setRows),0);
			
			if($rID3==1) $flag=1; //else $flag=0;
			else if($rID3==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_designation as $setRows)
		{
			$rID4=sql_insert("lib_designation",$field_arr_designation,implode(",",$setRows),0);
			
			if($rID4==1) $flag=1; //else $flag=0;
			else if($rID4==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_department as $setRows)
		{
			$rID5=sql_insert("lib_department",$field_arr_department,implode(",",$setRows),0);
			
			if($rID5==1) $flag=1; //else $flag=0;
			else if($rID5==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_mktmst as $setRows)
		{
			$rID6=sql_insert("lib_marketing_team",$field_arr_mktmst,implode(",",$setRows),0);
			
			if($rID6==1) $flag=1; //else $flag=0;
			else if($rID6==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		foreach( $data_mktdtls as $setRows)
		{
			$rID7=sql_insert("lib_mkt_team_member_info",$field_arr_mktdtls,implode(",",$setRows),0);
			
			if($rID7==1) $flag=1; //else $flag=0;
			else if($rID7==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		foreach( $data_subDept as $setRows)
		{
			$rID8=sql_insert("lib_pro_sub_deparatment",$field_arr_subdept,implode(",",$setRows),0);
			
			if($rID8==1) $flag=1; //else $flag=0;
			else if($rID8==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		foreach( $data_stdcm as $setRows)
		{
			$rID9=sql_insert("lib_standard_cm_entry",$field_arr_cmstd,implode(",",$setRows),0);
			
			if($rID9==1) $flag=1; //else $flag=0;
			else if($rID9==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_item_group as $setRows)
		{
			$rID10=sql_insert("lib_item_group",$field_arr_item_group,implode(",",$setRows),0);
			
			if($rID10==1) $flag=1; //else $flag=0;
			else if($rID10==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_season as $setRows)
		{
			$rID11=sql_insert("lib_buyer_season",$field_arr_season,implode(",",$setRows),0);
			if($rID11==1) $flag=1; //else $flag=0;
			else if($rID11==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_lib_template as $setRows)
		{
			$rID13=sql_insert("lib_qc_template",$field_arr_lib_template,implode(",",$setRows),0);
			if($rID13==1) $flag=1; //else $flag=0;
			else if($rID13==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		*/
		//============================================
		foreach( $data_template as $setRows)
		{
			$rID14=sql_insert("qc_template",$field_arr_template,implode(",",$setRows),0);
			//echo "insert into qc_template ($field_arr_template) values".$setRows;
			if($rID14==1) $flag=1; //else $flag=0;
			else if($rID14==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		//echo $flag; die;
		//die;
		foreach( $data_stage as $setRows)
		{
			$rID12=sql_insert("lib_stage_name",$field_arr_stage,implode(",",$setRows),0);
			if($rID12==1) $flag=1; //else $flag=0;
			else if($rID12==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_lib_agent_loc as $setRows)
		{
			$rID24=sql_insert("lib_agent_location",$field_arr_lib_agent_loc,implode(",",$setRows),0);
			if($rID24==1) $flag=1; //else $flag=0;
			else if($rID24==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		//echo $flag; die;
		//die;
		foreach( $data_mst as $setRows)
		{
			$rID15=sql_insert("qc_mst",$field_arr_mst,implode(",",$setRows),0);
			//echo "insert into qc_mst ($field_arr_mst) values".implode(",",$setRows);
			if($rID15==1) $flag=1; //else $flag=0;
			else if($rID15==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_mst'; die;
			}
		}//oci_commit($con);  
		//echo $flag; die;
		//die;
		foreach( $data_fab_cost as $setRows)
		{
			$rID16=sql_insert("qc_fabric_dtls",$field_arr_fab_cost,implode(",",$setRows),0);
			if($rID16==1) $flag=1; //else $flag=0;
			else if($rID16==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_fabric_dtls'; die;
			}
		}
		//echo $flag; die;
		//die;
		$implode_mst_id=implode(",",$data_mst_id);
		if($flag==1)
		{
			if($implode_mst_id!="") 
			{
				$rID30=execute_query( "delete from qc_cons_rate_dtls where mst_id in ($implode_mst_id)",1);
				if($rID30==1) $flag=1; //else $flag=0;
				else if($rID30==0) 
				{
					$flag=0;
					oci_rollback($con); 
					echo "10**".$roll_back_msg.'-qc_cons_rate_dtls_del'; die;
				}
			}
		}
		//echo $flag; die;
		foreach( $data_cons_rate as $setRows)
		{
			$rID17=sql_insert("qc_cons_rate_dtls",$field_arr_cons_rate,implode(",",$setRows),0);
			if($rID17==1) $flag=1; //else $flag=0;
			else if($rID17==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_cons_rate_dtls'; die;
			}
		}
		//echo $flag; die;
		//die;
		
		if($flag==1)
		{
			if($implode_mst_id!="") 
			{
				$rID31=execute_query( "delete from qc_fabric_string_data where mst_id in ($implode_mst_id)",1);
				if($rID31==1) $flag=1; //else $flag=0;
				else if($rID31==0) 
				{
					$flag=0;
					oci_rollback($con); 
					echo "10**".$roll_back_msg.'-qc_fabric_string_data_del'; die;
				}
			}
		}
		//echo $flag; die;
		foreach( $data_fabstrdata as $setRows)
		{
			$rID18=sql_insert("qc_fabric_string_data",$field_arr_fabstrdata,implode(",",$setRows),0);
			if($rID18==1) $flag=1; //else $flag=0;
			else if($rID18==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_fabric_string_data'; die;
			}
		}
		//die;
		if($flag==1)
		{
			if($implode_mst_id!="") 
			{
				$rID32=execute_query( "delete from qc_tot_cost_summary where mst_id in ($implode_mst_id)",1);
				if($rID32==1) $flag=1; //else $flag=0;
				else if($rID32==0) 
				{
					$flag=0;
					oci_rollback($con); 
					echo "10**".$roll_back_msg.'-qc_tot_cost_summary_del'; die;
				}
			}
		}
		//echo $flag; die;
		foreach( $data_summary as $setRows)
		{
			$rID19=sql_insert("qc_tot_cost_summary",$field_arr_summary,implode(",",$setRows),0);
			if($rID19==1) $flag=1; //else $flag=0;
			else if($rID19==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_tot_cost_summary'; die;
			}
		}
		//die;
		if($flag==1)
		{
			if($implode_mst_id!="") 
			{
				$rID33=execute_query( "delete from qc_item_cost_summary where mst_id in ($implode_mst_id)",1);
				if($rID33==1) $flag=1; //else $flag=0;
				else if($rID33==0) 
				{
					$flag=0;
					oci_rollback($con); 
					echo "10**".$roll_back_msg.'-qc_item_cost_summary_del'; die;
				}
			}
		}
		
		foreach( $data_item_summ as $setRows)
		{
			$rID20=sql_insert("qc_item_cost_summary",$field_arr_item_summ,implode(",",$setRows),0);
			if($rID20==1) $flag=1; //else $flag=0;
			else if($rID20==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_item_cost_summary'; die;
			}
		}
		
		foreach( $data_confirmmst as $setRows)
		{
			$rID21=sql_insert("qc_confirm_mst",$field_arr_confirmmst,implode(",",$setRows),0);
			if($rID21==1) $flag=1; //else $flag=0;
			else if($rID21==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_confirm_mst'; die;
			}
		}
		
		foreach( $data_confirmdtls as $setRows)
		{
			$rID22=sql_insert("qc_confirm_dtls",$field_arr_confirmdtls,implode(",",$setRows),0);
			if($rID22==1) $flag=1; //else $flag=0;
			else if($rID22==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_confirm_dtls'; die;
			}
		}
		
		foreach( $data_meetingmst as $setRows)
		{
			//$rID23=sql_insert("qc_meeting_mst",$field_arr_meetingmst,implode(",",$setRows),0);
			$imp_meet=implode(',',$setRows);
			/*INSERT ALL
			   INTO qc_meeting_mst (".$field_arr_meetingmst.") VALUES $imp_meet
			   INTO qc_meeting_mst (".$field_arr_meetingmst.") VALUES $imp_meet
			   INTO qc_meeting_mst (".$field_arr_meetingmst.") VALUES $imp_meet
			SELECT * FROM DUAL;*/
			$rID23=execute_query("INSERT INTO qc_meeting_mst (".$field_arr_meetingmst.") VALUES  $imp_meet ",1);
			//echo $rID23."INSERT INTO qc_meeting_mst (".$field_arr_meetingmst.") VALUES  $imp_meet ";
			//echo "insert into qc_meeting_mst ($field_arr_meetingmst) values".implode(",",$setRows);
			if($rID23==1) $flag=1; //else $flag=0;
			else if($rID23==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_meeting_mst'; die;
			}
		}
		//echo $flag; die;
		if($data_mst_up!="")
		{
			$rID25=execute_query(bulk_update_sql_statement("qc_mst", "qc_no",$field_arr_mst_up,$data_mst_up,$data_mst_id ));
			//echo bulk_update_sql_statement("qc_mst", "qc_no",$field_arr_mst_up,$data_mst_up,$data_mst_id );
			if($rID25==1) $flag=1; //else $flag=0;
			else if($rID25==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_mst_up'; die;
			}
		}
		//echo $flag; die;
		foreach( $data_fab_cost_id as $setRows)
		{
			if(implode(",",$setRows)!="")
			{
				//echo bulk_update_sql_statement("qc_fabric_dtls", "id",$field_arr_fab_cost_up,$data_fab_cost_up[$setRows],$setRows ); die;
				$rID26=execute_query(bulk_update_sql_statement("qc_fabric_dtls", "id",$field_arr_fab_cost_up,$data_fab_cost_up[$setRows],$setRows ));
				if($rID26==1) $flag=1; //else $flag=0;
				else if($rID26==0) 
				{
					$flag=0;
					oci_rollback($con); 
					echo "10**".$roll_back_msg.'-qc_fabric_dtls_up'; die;
				}
			}
		}
		
		if($data_confirmmst_up!="")
		{
			$rID27=execute_query(bulk_update_sql_statement("qc_confirm_mst", "id",$field_arr_confirmmst_up,$data_confirmmst_up,$data_confirmmst_id ));
			if($rID27==1) $flag=1; //else $flag=0;
			else if($rID27==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_confirm_mst_up'; die;
			}
		}
		
		if($data_confirmdtls_up!="")
		{
			$rID28=execute_query(bulk_update_sql_statement("qc_confirm_dtls", "id",$field_arr_confirmdtls_up,$data_confirmdtls_up,$data_confirmdtls_id ));
			if($rID28==1) $flag=1; //else $flag=0;
			else if($rID28==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_confirm_dtls_up'; die;
			}
		}
		
		if($data_meetingmst_up!="")
		{
			$rID29=execute_query(bulk_update_sql_statement("qc_meeting_mst", "id",$field_arr_meetingmst_up,$data_meetingmst_up,$data_meetingmst_id ));
			if($rID29==1) $flag=1; //else $flag=0;
			else if($rID29==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg.'-qc_meeting_mst_up'; die;
			}
		}
		
		/*foreach( $data_meetingperson as $setRows)
		{
			$rID24=sql_insert("qc_meeting_person",$field_arr_meetingperson,implode(",",$setRows),0);
			if($rID24==1) $flag=1; //else $flag=0;
			else if($rID24==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}
		
		foreach( $data_meetingdtls as $setRows)
		{
			$rID25=sql_insert("qc_meeting_dtls",$field_arr_meetingdtls,implode(",",$setRows),0);
			if($rID25==1) $flag=1; //else $flag=0;
			else if($rID25==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**".$roll_back_msg; die;
			}
		}*/
	}
	
	$user_id = $_SESSION['logic_erp']["user_id"]; 
	//echo "10**";
	$idrehist=return_next_id( "id", "qc_restore_history", 1) ;
	
	if (strpos($file_name, '.._Database_') !== false) {
		$ins_file_name =str_replace(".._Database_","",$file_name);
	}
	else{
		$ins_file_name =str_replace("_Database_","",$file_name);
	}
	
	$field_arr_hist="id, backup_time, restore_user, restore_time, file_name, status_active, is_deleted, is_lock";
	$data_arr_hist="(".$idrehist.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."','".$ins_file_name."',1,0,0)"; 
	
	//echo "INSERT INTO qc_restore_history (".$field_arr_hist.") VALUES ".$data_arr_hist; die;
	$rID25=sql_insert("qc_restore_history",$field_arr_hist,$data_arr_hist,1);
	if($rID25) $flag=1; else $flag=0;
	
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo "0**".$commit_msg;//.$commit_msg;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "10**";//.$roll_back_msg;
		}
	}
	else if($db_type==2)
	{
		if($flag==1)
		{
			oci_commit($con);  
			echo "0**".$commit_msg;//.$commit_msg;
		}
		else
		{
			oci_rollback($con);
			echo "10**";//.$roll_back_msg;
		}	
	}
	disconnect($con);
	die;
}
?>    