<?php

	include('common.php');
	
	
	$EditSql="delete from com_info_setup";
	$ExeEditSql=mysql_db_query($DB,$EditSql);
	
	$txt_fin_yr=$txt_fin_yr_from."-".$txt_fin_yr_to;
	echo "asd $txt_half_hr_ot_fraction";
	//$mdate=date('Y-m-d');
	//$mentrydate=time();
$AddSql="Insert into com_info_setup values('1','$txt_fin_yr','$txt_proxy_id_card_length','$txt_comp_id_card_length','$cbo_punch_style','$txt_last_punch_time','$txt_in_time_acceptance_before','$txt_total_working_hr','$cbo_adjust_working_hr_with_ot','$cbo_round_salary','$cbo_deduction_from','$cbo_add_ot_with_salary','$txt_ot_multiply','$txt_pf_deduction_amnt','$cbo_income_tax_contribution','$txt_one_hr_ot_fraction','$txt_half_hr_ot_fraction','$cbo_apply_late_deduction','$cbo_apply_absent_deduction','$cbo_apply_early_dep_deduction','$cbo_keep_log_access_control','$txt_fin_yr_from','$txt_fin_yr_to')";
	$ExeAddSql=mysql_db_query($DB,$AddSql);
	
		$district_sql= mysql_db_query($DB, "select max(id) from com_info_setup_backup");
		$r_district=mysql_fetch_array($district_sql);
		$p_id=$r_district[0]+1;
		
	$AddSql1="Insert into com_info_setup_backup values('$p_id','$txt_fin_yr','$txt_proxy_id_card_length','$txt_comp_id_card_length','$cbo_punch_style','$txt_last_punch_time','$txt_in_time_acceptance_before','$txt_total_working_hr','$cbo_adjust_working_hr_with_ot','$cbo_round_salary','$cbo_deduction_from','$cbo_add_ot_with_salary','$txt_ot_multiply','$txt_pf_deduction_amnt','$cbo_income_tax_contribution','$txt_one_hr_ot_fraction','$txt_half_hr_ot_fraction','$cbo_apply_late_deduction','$cbo_apply_absent_deduction','$cbo_apply_early_dep_deduction','$cbo_keep_log_access_control','$txt_fin_yr_from','$txt_fin_yr_to')";
	$ExeAddSql=mysql_db_query($DB,$AddSql1);
	
	header("location: index.php?mid=$mid&fid=$fid&msg=2");
	exit();

?>