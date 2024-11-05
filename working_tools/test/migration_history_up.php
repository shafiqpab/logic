<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$sql="select id, approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pre_cost_comarc_cost_dtls_h where 1=1
group by approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted
order by approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_comarc_cost_dtls_h",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_comarc_cost_dtls_h set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}
*/

/*$sql="select id, approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, status_active, is_deleted  from wo_pre_cost_commis_cost_dtls_h where 1=1
group by approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, status_active, is_deleted 
order by approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, status_active, is_deleted  asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_commis_cost_dtls_h",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_commis_cost_dtls_h set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_commiss_cost_dtls_id='".$row[csf('pre_cost_commiss_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and particulars_id='".$row[csf('particulars_id')]."' and commission_base_id='".$row[csf('commission_base_id')]."' and commision_rate='".$row[csf('commision_rate')]."' and commission_amount='".$row[csf('commission_amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}*/

/*$sql="select id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pre_cost_dtls_histry where 1=1

order by approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_dtls_histry",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_dtls_histry set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_dtls_id='".$row[csf('pre_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and costing_per_id='".$row[csf('costing_per_id')]."' and order_uom_id='".$row[csf('order_uom_id')]."' and fabric_cost='".$row[csf('fabric_cost')]."' and fabric_cost_percent='".$row[csf('fabric_cost_percent')]."' and trims_cost='".$row[csf('trims_cost')]."' and trims_cost_percent='".$row[csf('trims_cost_percent')]."' and embel_cost='".$row[csf('embel_cost')]."' and embel_cost_percent='".$row[csf('embel_cost_percent')]."' and wash_cost='".$row[csf('wash_cost')]."' and wash_cost_percent='".$row[csf('wash_cost_percent')]."' and comm_cost='".$row[csf('comm_cost')]."' and comm_cost_percent='".$row[csf('comm_cost_percent')]."' and commission='".$row[csf('commission')]."' and commission_percent='".$row[csf('commission_percent')]."' and lab_test='".$row[csf('lab_test')]."' and lab_test_percent='".$row[csf('lab_test_percent')]."' and inspection='".$row[csf('inspection')]."' and cm_cost='".$row[csf('cm_cost')]."' and cm_cost_percent='".$row[csf('cm_cost_percent')]."' and freight='".$row[csf('freight')]."' and freight_percent='".$row[csf('freight_percent')]."' and currier_pre_cost='".$row[csf('currier_pre_cost')]."' and currier_percent='".$row[csf('currier_percent')]."' and certificate_pre_cost='".$row[csf('certificate_pre_cost')]."' and certificate_percent='".$row[csf('certificate_percent')]."' and common_oh='".$row[csf('common_oh')]."' and common_oh_percent='".$row[csf('common_oh_percent')]."' and total_cost='".$row[csf('total_cost')]."' and total_cost_percent='".$row[csf('total_cost_percent')]."' and price_dzn='".$row[csf('price_dzn')]."' and price_dzn_percent='".$row[csf('price_dzn_percent')]."' and margin_dzn='".$row[csf('margin_dzn')]."' and margin_dzn_percent='".$row[csf('margin_dzn_percent')]."' and cost_pcs_set='".$row[csf('cost_pcs_set')]."' and cost_pcs_set_percent='".$row[csf('cost_pcs_set_percent')]."' and price_pcs_or_set='".$row[csf('price_pcs_or_set')]."' and price_pcs_or_set_percent='".$row[csf('price_pcs_or_set_percent')]."' and margin_pcs_set='".$row[csf('margin_pcs_set')]."' and margin_pcs_set_percent='".$row[csf('margin_pcs_set_percent')]."' and cm_for_sipment_sche='".$row[csf('cm_for_sipment_sche')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."'
	
	");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}*/

/*$sql="select id, approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted  from wo_pre_cost_embe_cost_dtls_his where 1=1
group by approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted 
order by approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted  asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_embe_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_embe_cost_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_embe_cost_dtls_id='".$row[csf('pre_cost_embe_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and emb_name='".$row[csf('emb_name')]."' and emb_type='".$row[csf('emb_type')]."' and cons_dzn_gmts='".$row[csf('cons_dzn_gmts')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and charge_lib_id='".$row[csf('charge_lib_id')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}*/

/*$sql="SELECT id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type FROM wo_pre_cost_fabric_cost_dtls_h where 1=1
group by id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type

order by id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type  asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_fab_con_cst_dtls_h",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_fabric_cost_dtls_h set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_fabric_cost_dtls_id='".$row[csf('pre_cost_fabric_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_number_id='".$row[csf('item_number_id')]."' and body_part_id='".$row[csf('body_part_id')]."' and fab_nature_id='".$row[csf('fab_nature_id')]."' and color_type_id='".$row[csf('color_type_id')]."' and lib_yarn_count_deter_id='".$row[csf('lib_yarn_count_deter_id')]."' and construction='".$row[csf('construction')]."' and composition='".$row[csf('composition')]."' and fabric_description='".$row[csf('fabric_description')]."' and gsm_weight='".$row[csf('gsm_weight')]."' and color_size_sensitive='".$row[csf('color_size_sensitive')]."' and color='".$row[csf('color')]."' and avg_cons='".$row[csf('avg_cons')]."' and fabric_source='".$row[csf('fabric_source')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and avg_finish_cons='".$row[csf('avg_finish_cons')]."' and avg_process_loss='".$row[csf('avg_process_loss')]."' and company_id='".$row[csf('company_id')]."' and costing_per='".$row[csf('costing_per')]."' and consumption_basis='".$row[csf('consumption_basis')]."' and process_loss_method='".$row[csf('process_loss_method')]."' and cons_breack_down='".$row[csf('cons_breack_down')]."' and msmnt_break_down='".$row[csf('msmnt_break_down')]."' and yarn_breack_down='".$row[csf('yarn_breack_down')]."' and marker_break_down='".$row[csf('marker_break_down')]."' and width_dia_type='".$row[csf('width_dia_type')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}*/

/*$sql="select id, approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, status_active, is_deleted FROM wo_pre_cost_fab_con_cst_dtls_h where 1=1
group by approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, status_active, is_deleted
order by approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, status_active, is_deleted  asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_fab_con_cst_dtls_h",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_fab_con_cst_dtls_h set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_fab_conv_cst_dtls_id='".$row[csf('pre_cost_fab_conv_cst_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and fabric_description='".$row[csf('fabric_description')]."' and cons_process='".$row[csf('cons_process')]."' and req_qnty='".$row[csf('req_qnty')]."' and charge_unit='".$row[csf('charge_unit')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}*/

/*$sql="SELECT id, approved_no, pre_cost_fab_yarnbreakdown_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pre_cost_fab_yarnbkdown_his WHERE 1=1
group by approved_no, pre_cost_fab_yarnbreakdown_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted
order by approved_no, pre_cost_fab_yarnbreakdown_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_fab_yarnbkdown_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_fab_yarnbkdown_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_fab_yarnbreakdown_id='".$row[csf('pre_cost_fab_yarnbreakdown_id')]."' and fabric_cost_dtls_id='".$row[csf('fabric_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and count_id='".$row[csf('count_id')]."' and copm_one_id='".$row[csf('copm_one_id')]."' and percent_one='".$row[csf('percent_one')]."' and copm_two_id='".$row[csf('copm_two_id')]."'  and percent_two='".$row[csf('percent_two')]."' and type_id='".$row[csf('type_id')]."' and cons_ratio='".$row[csf('cons_ratio')]."' and cons_qnty='".$row[csf('cons_qnty')]."' and rate='".$row[csf('rate')]."'  and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	$id1++;
}*/

/*$sql="SELECT id, approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pre_cost_fab_yarn_cst_dtl_h WHERE 1=1
group by approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted
order by approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_fab_yarn_cst_dtl_h",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_fab_yarn_cst_dtl_h set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_fab_yarn_cost_dtls_id='".$row[csf('pre_cost_fab_yarn_cost_dtls_id')]."' and fabric_cost_dtls_id='".$row[csf('fabric_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and count_id='".$row[csf('count_id')]."' and copm_one_id='".$row[csf('copm_one_id')]."' and percent_one='".$row[csf('percent_one')]."' and copm_two_id='".$row[csf('copm_two_id')]."'  and percent_two='".$row[csf('percent_two')]."' and type_id='".$row[csf('type_id')]."' and cons_ratio='".$row[csf('cons_ratio')]."' and cons_qnty='".$row[csf('cons_qnty')]."' and rate='".$row[csf('rate')]."'  and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	$id1++;
}
*/

/*$sql="SELECT id, approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, cons_breack_down, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pre_cost_trim_cost_dtls_his WHERE 1=1 and id=0
group by approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, cons_breack_down, inserted_by, insert_date, updated_by, status_active, is_deleted 
order by approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, cons_breack_down, inserted_by, insert_date, updated_by, status_active, is_deleted  asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_trim_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_trim_cost_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_trim_cost_dtls_id='".$row[csf('pre_cost_trim_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and trim_group='".$row[csf('trim_group')]."' and description='".$row[csf('description')]."' and brand_sup_ref='".$row[csf('brand_sup_ref')]."' and cons_uom='".$row[csf('cons_uom')]."'  and cons_dzn_gmts='".$row[csf('cons_dzn_gmts')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and apvl_req='".$row[csf('apvl_req')]."' and nominated_supp='".$row[csf('nominated_supp')]."'  and nominated_supp='".$row[csf('nominated_supp')]."' and cons_breack_down='".$row[csf('cons_breack_down')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'  and id=0");
	$id1++;
}
*/

/*$sql="SELECT id, approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id FROM wo_pre_cost_trim_co_cons_dtl_h WHERE 1=1 and id=0
group by approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id
order by approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pre_cost_trim_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pre_cost_trim_co_cons_dtl_h set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and pre_cost_trim_co_cons_dtls_id='".$row[csf('pre_cost_trim_co_cons_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and wo_pre_cost_trim_cost_dtls_id='".$row[csf('wo_pre_cost_trim_cost_dtls_id')]."' and po_break_down_id='".$row[csf('po_break_down_id')]."' and item_size='".$row[csf('item_size')]."' and cons='".$row[csf('cons')]."'  and place='".$row[csf('place')]."' and pcs='".$row[csf('pcs')]."' and country_id='".$row[csf('country_id')]."'");
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quot_mst_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, terget_qty, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_price_quot_costing_mst_his where 1=1
group by quotation_id, quot_mst_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, terget_qty, inserted_by, insert_date, updated_by, status_active, is_deleted


order by quotation_id, quot_mst_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, terget_qty, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_price_quot_costing_mst_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_price_quot_costing_mst_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quot_mst_id='".$row[csf('quot_mst_id')]."' and costing_per_id='".$row[csf('costing_per_id')]."' and order_uom_id='".$row[csf('order_uom_id')]."' and fabric_cost='".$row[csf('fabric_cost')]."' and fabric_cost_percent='".$row[csf('fabric_cost_percent')]."' and trims_cost='".$row[csf('trims_cost')]."' and trims_cost_percent='".$row[csf('trims_cost_percent')]."' and embel_cost='".$row[csf('embel_cost')]."' and embel_cost_percent='".$row[csf('embel_cost_percent')]."' and wash_cost='".$row[csf('wash_cost')]."' and wash_cost_percent='".$row[csf('wash_cost_percent')]."' and quotation_id='".$row[csf('quotation_id')]."' and quot_mst_id='".$row[csf('quot_mst_id')]."' and costing_per_id='".$row[csf('costing_per_id')]."' and order_uom_id='".$row[csf('order_uom_id')]."' and comm_cost='".$row[csf('comm_cost')]."' and comm_cost_percent='".$row[csf('comm_cost_percent')]."' and lab_test='".$row[csf('lab_test')]."' and lab_test_percent='".$row[csf('lab_test_percent')]."' and inspection='".$row[csf('inspection')]."' and inspection_percent='".$row[csf('inspection_percent')]."' and cm_cost='".$row[csf('cm_cost')]."' 
	
	
	
	
	and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'");
	//$data_array2.="(".$id1.")";
	//echo "update wo_pre_cost_comarc_cost_dtls_h set id=$id1 where approved_no='".$row[csf('approved_no')]."' and pre_cost_comarci_cost_dtls_id='".$row[csf('pre_cost_comarci_cost_dtls_id')]."' and job_no='".$row[csf('job_no')]."' and item_id='".$row[csf('item_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."' and updated_by='".$row[csf('updated_by')]."' and update_date='".$row[csf('update_date')]."' and status_active='".$row[csf('status_active')]."' and is_deleted='".$row[csf('is_deleted')]."'".'<br>';
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quot_set_dlts_id, approved_no, gmts_item_id, set_item_ratio FROM wo_price_quot_set_details_his where 1=1
group by quotation_id, quot_set_dlts_id, approved_no, gmts_item_id, set_item_ratio
order by quotation_id, quot_set_dlts_id, approved_no, gmts_item_id, set_item_ratio asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_price_quot_set_details_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_price_quot_set_details_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quot_set_dlts_id='".$row[csf('quot_set_dlts_id')]."' and gmts_item_id='".$row[csf('gmts_item_id')]."' and set_item_ratio='".$row[csf('set_item_ratio')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quo_commiss_dtls_id, approved_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pri_quo_commiss_dtls_his where 1=1
group by quotation_id, quo_commiss_dtls_id, approved_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, status_active, is_deleted
order by quotation_id, quo_commiss_dtls_id, approved_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_commiss_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_commiss_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quo_commiss_dtls_id='".$row[csf('quo_commiss_dtls_id')]."' and particulars_id='".$row[csf('particulars_id')]."' and commission_base_id='".$row[csf('commission_base_id')]."' and commision_rate='".$row[csf('commision_rate')]."' and commission_amount='".$row[csf('commission_amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quo_comm_dtls_id, approved_no, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pri_quo_comm_cost_dtls_his where 1=1
group by quotation_id, quo_comm_dtls_id, approved_no, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted
order by quotation_id, quo_comm_dtls_id, approved_no, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_comm_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_comm_cost_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quo_comm_dtls_id='".$row[csf('quo_comm_dtls_id')]."' and item_id='".$row[csf('item_id')]."' and base_id='".$row[csf('base_id')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quo_emb_dtls_id, approved_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pri_quo_embe_cost_dtls_his where 1=1
group by quotation_id, quo_emb_dtls_id, approved_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted
order by quotation_id, quo_emb_dtls_id, approved_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_embe_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_embe_cost_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quo_emb_dtls_id='".$row[csf('quo_emb_dtls_id')]."' and emb_name='".$row[csf('emb_name')]."' and emb_type='".$row[csf('emb_type')]."' and cons_dzn_gmts='".$row[csf('cons_dzn_gmts')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}
*/

/*$sql="SELECT id, quotation_id, quo_fab_conv_dtls_id, approved_no, cost_head, cons_type, process_loss, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pri_quo_fab_conv_dtls_his where 1=1
group by quotation_id, quo_fab_conv_dtls_id, approved_no, cost_head, cons_type, process_loss, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted
order by quotation_id, quo_fab_conv_dtls_id, approved_no, cost_head, cons_type, process_loss, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_fab_conv_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_fab_conv_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quo_fab_conv_dtls_id='".$row[csf('quo_fab_conv_dtls_id')]."' and cost_head='".$row[csf('cost_head')]."' and cons_type='".$row[csf('cons_type')]."' and req_qnty='".$row[csf('req_qnty')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quo_fab_dtls_id, approved_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, cons_breack_down, msmnt_break_down, yarn_breack_down, marker_break_down, width_dia_type FROM wo_pri_quo_fab_cost_dtls_his where 1=1
group by quotation_id, quo_fab_dtls_id, approved_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, cons_breack_down, msmnt_break_down, yarn_breack_down, marker_break_down, width_dia_type

order by quotation_id, quo_fab_dtls_id, approved_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, cons_breack_down, msmnt_break_down, yarn_breack_down, marker_break_down, width_dia_type asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_fab_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_fab_cost_dtls_his set id='".$id1."' where approved_no='".$row[csf('approved_no')]."' and quotation_id='".$row[csf('quotation_id')]."' and quo_fab_dtls_id='".$row[csf('quo_fab_dtls_id')]."' and construction='".$row[csf('construction')]."' and composition='".$row[csf('composition')]."' and avg_cons='".$row[csf('avg_cons')]."' and amount='".$row[csf('amount')]."' and inserted_by='".$row[csf('inserted_by')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, wo_pri_quo_fab_co_dtls_id, quotation_id, quo_fab_avg_co_dtls_id, approved_no, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons FROM wo_pri_quo_fab_co_avg_con_his where 1=1
group by id, wo_pri_quo_fab_co_dtls_id, quotation_id, quo_fab_avg_co_dtls_id, approved_no, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons

order by id, wo_pri_quo_fab_co_dtls_id, quotation_id, quo_fab_avg_co_dtls_id, approved_no, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_fab_yarn_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_fab_co_avg_con_his set id='".$id1."' where quotation_id='".$row[csf('quotation_id')]."' and wo_pri_quo_fab_co_dtls_id='".$row[csf('wo_pri_quo_fab_co_dtls_id')]."' and approved_no='".$row[csf('approved_no')]."' and quo_fab_avg_co_dtls_id='".$row[csf('quo_fab_avg_co_dtls_id')]."' and gmts_sizes='".$row[csf('gmts_sizes')]."' and dia_width='".$row[csf('dia_width')]."' and cons='".$row[csf('cons')]."' and requirment='".$row[csf('requirment')]."' and pcs='".$row[csf('pcs')]."' and total='".$row[csf('total')]."' and gmts_pcs='".$row[csf('gmts_pcs')]."' and net_fab_cons='".$row[csf('net_fab_cons')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quo_yarn_dtls_id, approved_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, supplier_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pri_quo_fab_yarn_dtls_his where 1=1
group by quotation_id, quo_yarn_dtls_id, approved_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, supplier_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted

order by quotation_id, quo_yarn_dtls_id, approved_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, supplier_id, rate, amount, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_fab_yarn_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_fab_yarn_dtls_his set id='".$id1."' where quotation_id='".$row[csf('quotation_id')]."' and quo_yarn_dtls_id='".$row[csf('quo_yarn_dtls_id')]."' and approved_no='".$row[csf('approved_no')]."' and count_id='".$row[csf('count_id')]."' and copm_one_id='".$row[csf('copm_one_id')]."' and type_id='".$row[csf('type_id')]."' and inserted_by='".$row[csf('inserted_by')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and cons_ratio='".$row[csf('cons_ratio')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, quotation_id, quo_trim_dtls_id, approved_no, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, status_active, is_deleted FROM wo_pri_quo_trim_cost_dtls_his where 1=1
group by quotation_id, quo_trim_dtls_id, approved_no, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, status_active, is_deleted

order by quotation_id, quo_trim_dtls_id, approved_no, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, status_active, is_deleted asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_trim_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_pri_quo_trim_cost_dtls_his set id='".$id1."' where quotation_id='".$row[csf('quotation_id')]."' and quo_trim_dtls_id='".$row[csf('quo_trim_dtls_id')]."' and approved_no='".$row[csf('approved_no')]."' and trim_group='".$row[csf('trim_group')]."' and cons_uom='".$row[csf('cons_uom')]."' and cons_dzn_gmts='".$row[csf('cons_dzn_gmts')]."' and inserted_by='".$row[csf('inserted_by')]."' and rate='".$row[csf('rate')]."' and amount='".$row[csf('amount')]."' and apvl_req='".$row[csf('apvl_req')]."' and insert_date='".$row[csf('insert_date')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/

/*$sql="SELECT id, approved_no, wo_trim_book_con_dtl_id, wo_trim_booking_dtls_id, booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id FROM wo_trim_book_con_dtls_hstry where 1=1
group by id, approved_no, wo_trim_book_con_dtl_id, wo_trim_booking_dtls_id, booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id

order by id, approved_no, wo_trim_book_con_dtl_id, wo_trim_booking_dtls_id, booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id asc";

$sql_res=sql_select($sql); $i=0;
$id1=1;//return_next_id("id","wo_pri_quo_trim_cost_dtls_his",1) ;
foreach($sql_res as $row)
{
	$i++;
	$up=0;
	$up=execute_query("update wo_trim_book_con_dtls_hstry set id='".$id1."' where wo_trim_book_con_dtl_id='".$row[csf('wo_trim_book_con_dtl_id')]."' and wo_trim_booking_dtls_id='".$row[csf('wo_trim_booking_dtls_id')]."' and approved_no='".$row[csf('approved_no')]."' and booking_no='".$row[csf('booking_no')]."' and job_no='".$row[csf('job_no')]."' and po_break_down_id='".$row[csf('po_break_down_id')]."' and color_number_id='".$row[csf('color_number_id')]."' and gmts_sizes='".$row[csf('gmts_sizes')]."' and item_color='".$row[csf('item_color')]."' and item_size='".$row[csf('item_size')]."' and process_loss_percent='".$row[csf('process_loss_percent')]."' and cons='".$row[csf('cons')]."' and requirment='".$row[csf('requirment')]."' and pcs='".$row[csf('pcs')]."' and color_size_table_id='".$row[csf('color_size_table_id')]."'");
	//$data_array2.="(".$id1.")";
	$id1++;
}*/


//print_r($data_array2);
mysql_query("COMMIT");  
//oci_commit($con); 
	echo "Success".$i;