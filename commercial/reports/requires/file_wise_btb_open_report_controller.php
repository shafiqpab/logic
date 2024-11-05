<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_lc_year")
{
	$sql="select distinct(lc_year) as lc_sc_year from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  
	union select distinct(sc_year) as lc_sc_year from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0";
	echo create_drop_down( "hide_year", 100,$sql,"lc_sc_year,lc_sc_year", 1, "-- Select --", 1,"");
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank ",'id','bank_name');
	$buyer_arr=return_library_array( "select buyer_name,id from lib_buyer ",'id','buyer_name');
	$con = connect();
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_company_name=str_replace("'","",$cbo_company_name); $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); $cbo_lein_bank=str_replace("'","",$cbo_lein_bank); $txt_file_no=str_replace("'","",$txt_file_no);$hide_year=str_replace("'","",$hide_year);
	$file_cond="";
	if($txt_file_no!="") $file_cond="and a.internal_file_no='$txt_file_no'";
	$attach_order_sql="select b.wo_po_break_down_id, c.job_no_mst from com_sales_contract a, com_sales_contract_order_info b, wo_po_break_down c where a.id=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and a.sc_year='$hide_year' $file_cond 
	union all 
	select b.wo_po_break_down_id, c.job_no_mst from com_export_lc a, com_export_lc_order_info b, wo_po_break_down c where a.id=b.com_export_lc_id and b.wo_po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and a.lc_year='$hide_year' $file_cond";
	//echo $attach_order_sql; die;
	$attach_order_sql_result=sql_select($attach_order_sql);
	$attach_order_id=array();
	foreach($attach_order_sql_result as $row)
	{
		$attach_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		$powiseJobNoArr[$row[csf("wo_po_break_down_id")]]=$row[csf("job_no_mst")];
	}
	
	$buyer_cond="";
	if($cbo_buyer_name >0) $buyer_cond=" and d.buyer_name=$cbo_buyer_name";
	
	if(count($attach_order_id)>0)
	{
		
		/*$budge_btb_open_sql="SELECT a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, e.budget_on, a.costing_per, e.amount, e.id as dtls_id, 0 as rate, 0 as measurement, 0 as type,0 as cons_dzn_gmts, 0 as set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls e  
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and e.job_no=d.job_no and e.job_no=c.job_no_mst and e.job_no=a.job_no and a.status_active=1 and c.status_active=1 and e.status_active=1 and e.amount > 0 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).") 
		union all
		select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, f.order_quantity as po_quantity, f.plan_cut_qnty as plan_cut_quantity, e.budget_on, a.costing_per, b.amount, b.id as dtls_id, b.rate as rate, g.measurement, 1 as type,0 as cons_dzn_gmts, 0 as set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fab_yarn_cost_dtls b, wo_pre_cost_fabric_cost_dtls e, wo_po_color_size_breakdown f ,wo_pre_stripe_color g 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and b.fabric_cost_dtls_id=e.id and e.job_no=d.job_no and e.job_no=c.job_no_mst and c.id=f.po_break_down_id and e.item_number_id=f.item_number_id and g.color_number_id=f.color_number_id and e.id=g.pre_cost_fabric_cost_dtls_id and b.color=g.stripe_color and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and b.amount > 0 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).") 
		union all 
		select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, 0 as budget_on, a.costing_per, b.amount, b.id as dtls_id, 0 as rate, 0 as measurement, 2 as type,0 as cons_dzn_gmts, 0 as set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).")
		union all 
		select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, b.budget_on, a.costing_per, b.amount, b.id as dtls_id, h.rate as rate, 0 as measurement, 3 as type,b.cons_dzn_gmts, i.set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_embe_cost_dtls b ,wo_po_color_size_breakdown f, wo_pre_cos_emb_co_avg_con_dtls h, wo_po_details_mas_set_details i
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and
		d.id=h.job_id  and c.id=f.po_break_down_id and c.id=h.po_break_down_id and f.item_number_id= h.item_number_id and f.color_number_id=h.color_number_id and f.size_number_id=h.size_number_id and b.id=h.pre_cost_emb_cost_dtls_id and d.id=i.job_id and f.item_number_id=i.gmts_item_id and
		 a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and b.emb_name <>2 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).")
		 group by a.job_no, d.job_quantity, d.total_set_qnty,d.job_quantity,d.total_set_qnty, d.job_quantity,c.excess_cut, d.total_set_qnty,c.po_quantity,d.total_set_qnty, c.plan_cut,d.total_set_qnty, b.budget_on, a.costing_per, b.amount, b.id , h.rate , b.cons_dzn_gmts,i.set_item_ratio";
		echo $budge_btb_open_sql;die;
		
		$budge_btb_open_result=sql_select($budge_btb_open_sql);
	
		foreach($budge_btb_open_result as $row)
		{
			$dzn_qnty=0;
			$costing_per_id=$row[csf('costing_per')];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$amount=0;
			if($row[csf('type')]==1)
			{
				if($row[csf('budget_on')]==1) 
				{
					$yarn_req_kg=($row[csf('measurement')]/$dzn_qnty)*$row[csf("po_quantity")];
					$yarn_req_lbs=$yarn_req_kg*2.20462;
					$amount=$yarn_req_lbs*$row[csf('rate')];
					
				}
				else 
				{
					$yarn_req_kg=($row[csf('measurement')]/$dzn_qnty)*$row[csf("plan_cut_quantity")];
					$yarn_req_lbs=$yarn_req_kg*2.20462;
					$amount=$yarn_req_lbs*$row[csf('rate')];				
				}
			}
			else if($row[csf('type')]==2)
			{
				if($row[csf('budget_on')]==1) 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")];
				}
				else if($row[csf('budget_on')]==2) 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("plan_cut_quantity")]; 
				}
				else 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")]; 
				}
				
			}
			else if($row[csf('type')]==3)
			{
				if($row[csf('budget_on')]==1) 
				{
					$amount=($row[csf("cons_dzn_gmts")]/$dzn_qnty)*($row[csf("po_quantity")]/$row[csf("set_item_ratio")])*$row[csf("rate")];
				}
				else if($row[csf('budget_on')]==2) 
				{
					$amount=($row[csf("cons_dzn_gmts")]/$dzn_qnty)*($row[csf("plan_cut_quantity")]/$row[csf("set_item_ratio")])*$row[csf("rate")]; 
				}
				else 
				{
					$amount=($row[csf("cons_dzn_gmts")]/$dzn_qnty)*($row[csf("plan_cut_quantity")]/$row[csf("set_item_ratio")])*$row[csf("rate")]; 
				}
			}
			else
			{
				if($row[csf('budget_on')]==1) 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")];
				}
				else 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("plan_cut_quantity")]; 
				}
			}
			//$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("job_quantity")];
			//if($job_check[$row[csf("dtls_id")]]=="")
			//{
				//$job_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				//$all_job_no[$row[csf('job_no')]]=$row[csf('job_no')];
				//$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
			//}
			$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
			
		}
		*/
		
		$condition= new condition();
		
		if(implode(",",$attach_order_id)!='')
		{
			$condition->po_id_in("".implode(",",$attach_order_id)."");
		}
		
		$condition->init();
		$fabric= new fabric($condition);
		$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
		
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_order();
		//echo "tppps";die;
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
		
		$wash= new wash($condition);
		$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

		$conversion = new conversion($condition);
		$conversion_costing_arr = $conversion->getAmountArray_by_order();
		
		foreach($attach_order_id as $bompoid)
		{
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['0']+=array_sum($fabric_costing_arr['sweater']['grey'][$bompoid])+array_sum($fabric_costing_arr['knit']['grey'][$bompoid])+array_sum($fabric_costing_arr['woven']['grey'][$bompoid]);
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['1']+=$yarn_costing_arr[$bompoid];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['2']+=$trims_costing_arr[$bompoid];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['3']+=$emblishment_costing_arr[$bompoid][1]+$emblishment_costing_arr[$bompoid][2]+$emblishment_costing_arr_name_wash[$bompoid][3]+$emblishment_costing_arr[$bompoid][4]+$emblishment_costing_arr[$bompoid][5];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['4']+=array_sum($conversion_costing_arr[$bompoid]);			
		}
		//$budge_btb_open_amt+=array_sum($job_wise_budge_amt);
	}
	
	
	// echo "<pre>";print_r($job_wise_budge_amt['SSL-21-00192']);die;
	//echo "<pre>";print_r($job_wise_budge_amt['SSL-21-00102']);die;
	
	if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
	if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
	if($cbo_lein_bank == 0) $cbo_lein_bank="%%"; else $cbo_lein_bank = $cbo_lein_bank;
	if(trim($txt_file_no)!="") $txt_file_no =$txt_file_no; else $txt_file_no="%%";
	if(trim($hide_year)!="") $hide_year =$hide_year; else $hide_year="%%";
	$lc_sc_sql= "SELECT a.ID, a.BENEFICIARY_NAME, a.BUYER_NAME, a.CONTRACT_NO AS LC_SC_NO, a.CONTRACT_VALUE AS LC_SC_VAL, a.SC_YEAR as LC_SC_YEAR, a.INTERNAL_FILE_NO, a.LIEN_BANK, a.EXPIRY_DATE, a.CONVERTIBLE_TO_LC AS REPL_CONV, a.SC_YEAR as SC_LC_YEAR, a.CONVERTED_FROM, 2 as TYPE, sum(attached_qnty) as ATTACHED_QNTY, c.id as PO_ID, c.job_no_mst as JOB_NO_MST, c.po_quantity as PO_QNTY, c.po_total_price as PO_VALUE
	from com_sales_contract a
	left join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and b.is_deleted= 0 and b.status_active=1
	left join wo_po_break_down c on b.wo_po_break_down_id=c.id and c.is_deleted= 0 and c.status_active=1
	where  a.beneficiary_name like '$cbo_company_name' and  a.buyer_name like '$cbo_buyer_name' and  a.internal_file_no like '$txt_file_no' and a.sc_year like '$hide_year' and a.is_deleted= 0 and a.status_active=1 
	group by a.id, a.beneficiary_name, a.buyer_name, a.contract_no, a.contract_value, a.sc_year , a.internal_file_no, a.lien_bank, a.expiry_date, a.convertible_to_lc, a.sc_year, a.converted_from, c.id,c.job_no_mst,c.po_quantity, c.po_total_price
	union all 
	select a.ID, a.BENEFICIARY_NAME, a.BUYER_NAME, a.EXPORT_LC_NO AS LC_SC_NO, a.LC_VALUE AS LC_SC_VAL, a.LC_YEAR as LC_SC_YEAR, a.INTERNAL_FILE_NO, a.LIEN_BANK, a.EXPIRY_DATE, a.REPLACEMENT_LC AS REPL_CONV, a.LC_YEAR as SC_LC_YEAR, 0 as CONVERTED_FROM, 1 as TYPE, sum(attached_qnty) as ATTACHED_QNTY, c.id as PO_ID, c.job_no_mst as JOB_NO_MST, c.po_quantity as PO_QNTY, c.po_total_price as PO_VALUE
	from com_export_lc a
	left join com_export_lc_order_info b on a.id=b.com_export_lc_id and b.is_deleted= 0 and b.status_active=1
	left join wo_po_break_down c on b.wo_po_break_down_id=c.id and c.is_deleted= 0 and c.status_active=1
	where a.beneficiary_name like '$cbo_company_name' and  a.buyer_name like '$cbo_buyer_name' and  a.internal_file_no like '$txt_file_no' and a.lc_year like '$hide_year' and a.is_deleted= 0 and a.status_active=1 
	group by a.id, a.beneficiary_name, a.buyer_name, a.export_lc_no, a.lc_value, a.lc_year, a.internal_file_no, a.lien_bank, a.expiry_date, a.replacement_lc, a.lc_year, c.id, c.job_no_mst, c.po_quantity, c.po_total_price
	order by INTERNAL_FILE_NO asc, EXPIRY_DATE desc";
	// echo $lc_sc_sql;die;

	$lc_sc_result=sql_select($lc_sc_sql);
	$report_data=array();
	$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
	if($temp_table_id=="") $temp_table_id=1;$lc_sc_file_arr=array();
	//echo "<pre>";print_r($job_wise_budge_amt);
	$p=0;
	foreach($lc_sc_result as $val)
	{
		if($lc_sc_id_check1[$val["ID"]."*".$val["TYPE"]."*".$val["JOB_NO_MST"]]=="")
		{
			$lc_sc_id_check1[$val["ID"]."*".$val["TYPE"]."*".$val["JOB_NO_MST"]]=$val["ID"]."*".$val["TYPE"]."*".$val["JOB_NO_MST"];
			$file_ref=$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"];
			$report_data[$file_ref]["BTB_TOBE_OPEN"]+=$job_wise_budge_amt[$val["JOB_NO_MST"]][0]+$job_wise_budge_amt[$val["JOB_NO_MST"]][1]+$job_wise_budge_amt[$val["JOB_NO_MST"]][2]+$job_wise_budge_amt[$val["JOB_NO_MST"]][3];
			$tot_fab_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][0];
			$tot_yarn_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][1];
			$tot_trims_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][2];
			$tot_emb_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][3];
			$tot_conv_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][4];
		}
		
		if($lc_sc_id_check[$val["ID"]."*".$val["TYPE"]]=="")
		{
			$lc_sc_id_check[$val["ID"]."*".$val["TYPE"]]=$val["ID"]."*".$val["TYPE"];
			$file_ref=$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"];
			$report_data[$file_ref]["BENEFICIARY_NAME"]=$val["BENEFICIARY_NAME"];
			$report_data[$file_ref]["BUYER_NAME"]=$val["BUYER_NAME"];
			$report_data[$file_ref]["SC_LC_YEAR"]=$val["SC_LC_YEAR"];
			$report_data[$file_ref]["INTERNAL_FILE_NO"]=$val["INTERNAL_FILE_NO"];
			$report_data[$file_ref]["LC_SC_NO"].=$val["LC_SC_NO"].",";
			$report_data[$file_ref]["LC_SC_ID"].=$val["ID"].",";
			$report_data[$file_ref]["LIEN_BANK"]=$val["LIEN_BANK"];
			$report_data[$file_ref]["EXPIRY_DATE"]=$val["EXPIRY_DATE"];
			//$report_data[$file_ref]["BTB_TOBE_OPEN"]=$job_wise_budge_amt[$val["JOB_NO_MST"]][0]+$job_wise_budge_amt[$val["JOB_NO_MST"]][1]+$job_wise_budge_amt[$val["JOB_NO_MST"]][2]+$job_wise_budge_amt[$val["JOB_NO_MST"]][3];
			
			$lc_sc_file_arr[$val["ID"]."*".$val["TYPE"]]=$val["INTERNAL_FILE_NO"];
			if($val["TYPE"]==2)
			{
				if($val["REPL_CONV"]!=2 && $val["CONVERTED_FROM"] <=0)
				{
					$report_data[$file_ref]["FILE_VALUE"]+=$val["LC_SC_VAL"];
				}
				if($val["REPL_CONV"]==2)
				{
					$report_data[$file_ref]["FILE_VALUE"]+=$val["LC_SC_VAL"];
				}
				$refrID1=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")";oci_rollback($con);die;
				}
			}
			else
			{
				if($val["REPL_CONV"]!=1)
				{
					$report_data[$file_ref]["FILE_VALUE"]+=$val["LC_SC_VAL"];
				}
				$refrID1=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")";oci_rollback($con);die;
				}
			}
			$temp_table_id++;
		}

		$file_ref=$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"];
		if($po_id_check[$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"]."*".$val["PO_ID"]]=="")
		{
			$po_id_check[$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"]."*".$val["PO_ID"]]=$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"];
			$report_data[$file_ref]["LC_SC_PO_QNTY"]+=$val["PO_QNTY"];
			$report_data[$file_ref]["LC_SC_PO_VALUE"]+=$val["PO_VALUE"];
		}

		if($att_qnt_check[$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"]]=="")
		{
			$att_qnt_check[$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"]]=$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"];
			$report_data[$file_ref]["LC_SC_ATTACHED_QNTY"]+=$val["ATTACHED_QNTY"];
		}
	}
	unset($lc_sc_result);
	unset($po_id_check);
	unset($att_qnt_check);
	if($refrID1)
	{
		oci_commit($con);
	}
	//echo "tres";die;
	//echo $p."<pre>";print_r($report_data);die;
	$btb_sql="SELECT c.IMPORTER_ID, b.IMPORT_MST_ID, b.CURRENT_DISTRIBUTION, b.LC_SC_ID, c.LC_VALUE, 1 as TYPE 
	from GBL_TEMP_REPORT_ID a, COM_BTB_EXPORT_LC_ATTACHMENT b, COM_BTB_LC_MASTER_DETAILS c 
	where a.REF_VAL=b.LC_SC_ID and b.IMPORT_MST_ID=c.ID and a.REF_FROM=1 and b.IS_LC_SC=0 and c.LC_NUMBER IS NOT NULL  and b.status_active=1 and b.is_deleted=0
	union all
	select c.IMPORTER_ID, b.IMPORT_MST_ID, b.CURRENT_DISTRIBUTION, b.LC_SC_ID, c.LC_VALUE, 2 as TYPE 
	from GBL_TEMP_REPORT_ID a, COM_BTB_EXPORT_LC_ATTACHMENT b, COM_BTB_LC_MASTER_DETAILS c 
	where a.REF_VAL=b.LC_SC_ID and b.IMPORT_MST_ID=c.ID and a.REF_FROM=2 and b.IS_LC_SC=1 and c.LC_NUMBER IS NOT NULL  and b.status_active=1 and b.is_deleted=0";
	//echo $btb_sql;die;
	$btb_sql_result=sql_select($btb_sql);
	foreach($btb_sql_result as $row)
	{
		$file_no=$lc_sc_file_arr[$row["LC_SC_ID"]."*".$row["TYPE"]]."*".$row["IMPORTER_ID"];
		if($btb_check[$row["LC_SC_ID"]."*".$row["IMPORT_MST_ID"]."*".$row["TYPE"]]=="")
		{
			$btb_check[$row["LC_SC_ID"]."*".$row["IMPORT_MST_ID"]."*".$row["TYPE"]]=$row["LC_SC_ID"]."*".$row["IMPORT_MST_ID"]."*".$row["TYPE"];
			$report_data[$file_no]["BTB_OPEN"]+=$row["CURRENT_DISTRIBUTION"];
			$report_data[$file_no]["IMPORT_MST_ID"].=$row["IMPORT_MST_ID"].",";
		}
	}
	
	unset($btb_sql_result);
	
	$inv_sql="select b.ID, b.BENIFICIARY_ID, b.INVOICE_QUANTITY, b.INVOICE_VALUE, b.NET_INVO_VALUE, b.LC_SC_ID, b.IS_LC as TYPE from GBL_TEMP_REPORT_ID a, COM_EXPORT_INVOICE_SHIP_MST b where a.REF_VAL=b.LC_SC_ID and a.REF_FROM=b.IS_LC and b.status_active=1 and b.is_deleted=0";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	foreach($inv_sql_result as $row)
	{
		$file_no=$lc_sc_file_arr[$row["LC_SC_ID"]."*".$row["TYPE"]]."*".$row["BENIFICIARY_ID"];
		
		if($inv_check[$row["ID"]]=="")
		{
			$inv_check[$row["ID"]]=$row["ID"];
			$report_data[$file_no]["INV_ID"].=$row["ID"].",";
			$report_data[$file_no]["SHIP_QNTY"]+=$row["INVOICE_QUANTITY"];
			$report_data[$file_no]["SHIP_VALUE"]+=$row["INVOICE_VALUE"];
			$report_data[$file_no]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
		}
	}
	unset($inv_sql_result);
	
	
	$gblDel=execute_query("delete from GBL_TEMP_REPORT_ID where USER_ID=$user_id");
	if($gblDel)
	{
		oci_commit($con);
	}
	// echo "<pre>";print_r($report_data);die;
	
	ob_start();
	?>
    <div style="width:2250px;" id="scroll_body">
        <fieldset style="width:100%">
            <table width="2250" cellpadding="0" cellspacing="0" id="caption" align="left">
                <tr>
                    <td align="center" width="100%" colspan="23" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="2250" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                <thead>
                    <tr>
                        <th rowspan="2" width="30">Sl</th>
                        <th width="130" rowspan="2">Company</th>
                        <th width="150" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">File Year</th>
                        <th width="80" rowspan="2">Int. File No.</th>
                        <th width="200" rowspan="2">SC/LC No.</th>
                        <th width="100" rowspan="2">File Value</th>
                        <th width="130" rowspan="2">Lien Bank</th>
                        <th width="80" rowspan="2">Expairy Date</th>
                        <th colspan="7">BTB Value Open Status</th>
                        <th colspan="7">Export Status</th>
                    </tr>
                    <tr>
                        <th width="100">BTB Need To Open</th>
                        <th width="100">Opened</th>
                        <th width="80">(%)</th>
                        <th width="100">BTB To Be Open</th>
                        <th width="80">BTB To Be Open %</th>
                        <th width="100">Excess BTB Opened</th>
                        <th width="80">Excess %</th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="80">Order Value</th>
                        <th width="100">Shipped Qty. (Pcs)</th>
                        <th width="100">Shipped Value ($)</th>
                        <th width="80">Value %</th>
                        <th width="80">Bal. Qty Pcs</th>
                        <th>Bal. Value($)</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach ($report_data as $key=>$val)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$btb_to_be_open=($val['BTB_TOBE_OPEN']/($val['LC_SC_PO_QNTY']*1))*($val['LC_SC_ATTACHED_QNTY']*1);
					// if($val['BTB_OPEN']!=0 && $val['FILE_VALUE']!=0) $btb_open_percent=(($val['BTB_OPEN']/$val['FILE_VALUE'])*100); $btb_open_percent=0;
					if($val['BTB_OPEN']!=0 && $val['FILE_VALUE']!=0){ $btb_open_percent=(($val['BTB_OPEN']/$val['FILE_VALUE'])*100); }else{$btb_open_percent=0;}
					if($val['FILE_VALUE']!=0){ $file_60=(($val['FILE_VALUE']/100)*50); }else{ $file_60=0; }
					// $file_space=$val['BTB_TOBE_OPEN']-$val['BTB_OPEN'];
					$file_space=$btb_to_be_open-$val['BTB_OPEN'];
					//$file_space_percent=(($file_space/$file_60)*100);
					// if($file_space!=0 && $val['BTB_OPEN']!=0){ $file_space_percent=(($val['BTB_OPEN']/$file_space)*100); }else{ $file_space_percent=0;}
					// if($val['SHIP_VALUE']!=0 && $val['FILE_VALUE']!=0){ $ship_percent=(($val['SHIP_VALUE']/$val['FILE_VALUE'])*100); }else{ $ship_percent=0;}
					if($file_space!=0 && $btb_to_be_open!=0){ $file_space_percent=(($file_space/$btb_to_be_open)*100); }else{ $file_space_percent=0;}
					if($val['SHIP_VALUE']!=0 && $val['LC_SC_PO_VALUE']!=0){ $ship_percent=(($val['SHIP_VALUE']/$val['LC_SC_PO_VALUE'])*100); }else{ $ship_percent=0;}
					$balance_qnty=$val['LC_SC_PO_QNTY']-$val['SHIP_QNTY'];
					$balance_value=$val['LC_SC_PO_VALUE']-$val['SHIP_VALUE'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i;?></td>
						<td style="word-break:break-all;"><? echo $company_arr[$val['BENEFICIARY_NAME']];?></td>
						<td style="word-break:break-all;"><? echo $buyer_arr[$val['BUYER_NAME']];?></td>
						<td align="center" style="word-break:break-all;"><? echo $val['SC_LC_YEAR'];?></td>
						<td align="center" style="word-break:break-all;"><? echo $val['INTERNAL_FILE_NO'];?></td>
						<td style="word-break:break-all;"><? echo chop($val['LC_SC_NO'],','); ?></td>
                        <td align="right"><? echo number_format($val['FILE_VALUE'],2);?></td>
                        <td style="padding-left:10px; word-break:break-all;"><? echo $lein_bank_arr[$val['LIEN_BANK']];?></td>
						<td align="center" style="word-break:break-all;"><? echo change_date_format($val[('EXPIRY_DATE')]);?></td>
                        <td align="right" title="( ( Job wise budget( fabric cost = <?= $tot_fab_cost;?> + yarn cost=<?= $tot_yarn_cost; ?> + trim cost=<?=$tot_trims_cost; ?> + emblishment= <?=$tot_emb_cost; ?> + conversion= <?=$tot_conv_cost; ?> ) / PO Qnty<?=$val['LC_SC_PO_QNTY']?> ) * Att Qnty<?=$val['LC_SC_ATTACHED_QNTY']?> )"><? echo number_format($btb_to_be_open,2);?></td>
						<td align="right"><a href='#report_details' style='color:#000' onClick="openmypage_submission('<? echo chop($val['IMPORT_MST_ID'],',')?>','<?=$key;?>');"><? echo number_format($val['BTB_OPEN'],2);?></a></td>
						<td align="right"><? echo number_format($btb_open_percent,2);?></td>
						<td align="right"><? if($file_space>=0) { echo number_format($file_space,2); $total_file_space+=$file_space;} else echo "0.00";?></td>
						<td align="right"><? if($file_space>=0) echo number_format($file_space_percent,2); else echo "0.00";?></td>
                        <td align="right"><? if($file_space<0) echo number_format($file_space,2);  else echo "0.00";?></td>
						<td align="right"><? if($file_space<0) echo number_format($file_space_percent,2);  else echo "0.00";?></td>
                        <td align="right"><? echo number_format($val['LC_SC_PO_QNTY'],2);?></td>
                        <td align="right"><? echo number_format($val['LC_SC_PO_VALUE'],2);?></td>
                        <td align="right"><? echo number_format($val['SHIP_QNTY'],2);?></td>
                        <td align="right"><a href='#report_details' style='color:#000' onClick="openmypage_invoice('<? echo chop($val['INV_ID'],',')?>');"><? echo number_format($val['SHIP_VALUE'],2);?></a></td>
                        <td align="right"><? echo number_format($ship_percent,2); ?></td>
                        <td align="right"><? if($balance_qnty>0){ echo number_format($balance_qnty,2);$total_balance_po_qnty+=$balance_qnty;}else{echo "0.00";} ?></td>
                        <td align="right"><? if($balance_value>0){ echo number_format($balance_value,2);$total_balance_po_value+=$balance_value;}else{echo "0.00";} ?></td>
					</tr>
					<?
					$i++;
					$total_file_value+=$val['FILE_VALUE'];
					$total_btb_tobe_open+=$val['BTB_TOBE_OPEN'];
					$total_btb_open+=$val['BTB_OPEN'];
					$total_ship_qnty+=$val['SHIP_QNTY'];
					$total_ship_value+=$val['SHIP_VALUE'];
					$total_po_qnty+=$val['LC_SC_PO_QNTY'];
					$total_po_value+=$val['LC_SC_PO_VALUE'];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th align="right">Total:</th>
                        <th align="right"><? echo number_format($total_file_value,2)?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_btb_tobe_open,2)?></th>
                        <th align="right"><? echo number_format($total_btb_open,2)?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_file_space,2)?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_po_qnty,2)?></th>
                        <th align="right"><? echo number_format($total_ship_value,2)?></th>
                        <th align="right"><? echo number_format($total_ship_qnty,2)?></th>
                        <th align="right"><? echo number_format($total_ship_value,2)?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_balance_po_qnty,2)?></th>
                        <th align="right"><? echo number_format($total_balance_po_value,2)?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
			}
// 			}
// 			//$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("job_quantity")];
// 			//if($job_check[$row[csf("dtls_id")]]=="")
// 			//{
// 				//$job_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
// 				//$all_job_no[$row[csf('job_no')]]=$row[csf('job_no')];
// 				//$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
// 			//}
// 			$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
// }
// 			//$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("job_quantity")];
// 			//if($job_check[$row[csf("dtls_id")]]=="")
// 			//{
// 				//$job_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
// 				//$all_job_no[$row[csf('job_no')]]=$row[csf('job_no')];
// 				//$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
// 			//}
// 			$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;

if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank ",'id','bank_name');
	$buyer_arr=return_library_array( "select buyer_name,id from lib_buyer ",'id','buyer_name');
	$con = connect();
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_company_name=str_replace("'","",$cbo_company_name); $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); $cbo_lein_bank=str_replace("'","",$cbo_lein_bank); $txt_file_no=str_replace("'","",$txt_file_no);$hide_year=str_replace("'","",$hide_year);
	$file_cond="";
	if($txt_file_no!="") $file_cond="and a.internal_file_no='$txt_file_no'";
	$attach_order_sql="select b.wo_po_break_down_id, c.job_no_mst from com_sales_contract a, com_sales_contract_order_info b, wo_po_break_down c where a.id=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and a.sc_year='$hide_year' $file_cond 
	union all 
	select b.wo_po_break_down_id, c.job_no_mst from com_export_lc a, com_export_lc_order_info b, wo_po_break_down c where a.id=b.com_export_lc_id and b.wo_po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and a.lc_year='$hide_year' $file_cond";
	//echo $attach_order_sql; die;
	$attach_order_sql_result=sql_select($attach_order_sql);
	$attach_order_id=array();
	foreach($attach_order_sql_result as $row)
	{
		$attach_order_id[$row[csf("wo_po_break_down_id")]]=$row[csf("wo_po_break_down_id")];
		$powiseJobNoArr[$row[csf("wo_po_break_down_id")]]=$row[csf("job_no_mst")];
	}
	
	$buyer_cond="";
	if($cbo_buyer_name >0) $buyer_cond=" and d.buyer_name=$cbo_buyer_name";
	
	if(count($attach_order_id)>0)
	{
		
		/*$budge_btb_open_sql="SELECT a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, e.budget_on, a.costing_per, e.amount, e.id as dtls_id, 0 as rate, 0 as measurement, 0 as type,0 as cons_dzn_gmts, 0 as set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls e  
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and e.job_no=d.job_no and e.job_no=c.job_no_mst and e.job_no=a.job_no and a.status_active=1 and c.status_active=1 and e.status_active=1 and e.amount > 0 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).") 
		union all
		select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, f.order_quantity as po_quantity, f.plan_cut_qnty as plan_cut_quantity, e.budget_on, a.costing_per, b.amount, b.id as dtls_id, b.rate as rate, g.measurement, 1 as type,0 as cons_dzn_gmts, 0 as set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fab_yarn_cost_dtls b, wo_pre_cost_fabric_cost_dtls e, wo_po_color_size_breakdown f ,wo_pre_stripe_color g 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and b.fabric_cost_dtls_id=e.id and e.job_no=d.job_no and e.job_no=c.job_no_mst and c.id=f.po_break_down_id and e.item_number_id=f.item_number_id and g.color_number_id=f.color_number_id and e.id=g.pre_cost_fabric_cost_dtls_id and b.color=g.stripe_color and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and b.amount > 0 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).") 
		union all 
		select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, 0 as budget_on, a.costing_per, b.amount, b.id as dtls_id, 0 as rate, 0 as measurement, 2 as type,0 as cons_dzn_gmts, 0 as set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).")
		union all 
		select a.job_no, (d.job_quantity*d.total_set_qnty) as job_quantity, (d.job_quantity*d.total_set_qnty)+((d.job_quantity*(c.excess_cut/100))*d.total_set_qnty) as job_quantity_plan, (c.po_quantity*d.total_set_qnty) as po_quantity, (c.plan_cut*d.total_set_qnty) as plan_cut_quantity, b.budget_on, a.costing_per, b.amount, b.id as dtls_id, h.rate as rate, 0 as measurement, 3 as type,b.cons_dzn_gmts, i.set_item_ratio
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_embe_cost_dtls b ,wo_po_color_size_breakdown f, wo_pre_cos_emb_co_avg_con_dtls h, wo_po_details_mas_set_details i
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and
		d.id=h.job_id  and c.id=f.po_break_down_id and c.id=h.po_break_down_id and f.item_number_id= h.item_number_id and f.color_number_id=h.color_number_id and f.size_number_id=h.size_number_id and b.id=h.pre_cost_emb_cost_dtls_id and d.id=i.job_id and f.item_number_id=i.gmts_item_id and
		 a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and b.emb_name <>2 and d.company_name=$cbo_company_name $buyer_cond and c.id in(".implode(",",$attach_order_id).")
		 group by a.job_no, d.job_quantity, d.total_set_qnty,d.job_quantity,d.total_set_qnty, d.job_quantity,c.excess_cut, d.total_set_qnty,c.po_quantity,d.total_set_qnty, c.plan_cut,d.total_set_qnty, b.budget_on, a.costing_per, b.amount, b.id , h.rate , b.cons_dzn_gmts,i.set_item_ratio";
		echo $budge_btb_open_sql;die;
		
		$budge_btb_open_result=sql_select($budge_btb_open_sql);
	
		foreach($budge_btb_open_result as $row)
		{
			$dzn_qnty=0;
			$costing_per_id=$row[csf('costing_per')];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$amount=0;
			if($row[csf('type')]==1)
			{
				if($row[csf('budget_on')]==1) 
				{
					$yarn_req_kg=($row[csf('measurement')]/$dzn_qnty)*$row[csf("po_quantity")];
					$yarn_req_lbs=$yarn_req_kg*2.20462;
					$amount=$yarn_req_lbs*$row[csf('rate')];
					
				}
				else 
				{
					$yarn_req_kg=($row[csf('measurement')]/$dzn_qnty)*$row[csf("plan_cut_quantity")];
					$yarn_req_lbs=$yarn_req_kg*2.20462;
					$amount=$yarn_req_lbs*$row[csf('rate')];				
				}
			}
			else if($row[csf('type')]==2)
			{
				if($row[csf('budget_on')]==1) 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")];
				}
				else if($row[csf('budget_on')]==2) 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("plan_cut_quantity")]; 
				}
				else 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")]; 
				}
				
			}
			else if($row[csf('type')]==3)
			{
				if($row[csf('budget_on')]==1) 
				{
					$amount=($row[csf("cons_dzn_gmts")]/$dzn_qnty)*($row[csf("po_quantity")]/$row[csf("set_item_ratio")])*$row[csf("rate")];
				}
				else if($row[csf('budget_on')]==2) 
				{
					$amount=($row[csf("cons_dzn_gmts")]/$dzn_qnty)*($row[csf("plan_cut_quantity")]/$row[csf("set_item_ratio")])*$row[csf("rate")]; 
				}
				else 
				{
					$amount=($row[csf("cons_dzn_gmts")]/$dzn_qnty)*($row[csf("plan_cut_quantity")]/$row[csf("set_item_ratio")])*$row[csf("rate")]; 
				}
			}
			else
			{
				if($row[csf('budget_on')]==1) 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("po_quantity")];
				}
				else 
				{
					$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("plan_cut_quantity")]; 
				}
			}
			//$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("job_quantity")];
			//if($job_check[$row[csf("dtls_id")]]=="")
			//{
				//$job_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				//$all_job_no[$row[csf('job_no')]]=$row[csf('job_no')];
				//$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
			//}
			$job_wise_budge_amt[$row[csf('job_no')]][$row[csf('type')]]+=$amount;
			
		}
		*/
		
		$condition= new condition();
		
		if(implode(",",$attach_order_id)!='')
		{
			$condition->po_id_in("".implode(",",$attach_order_id)."");
		}
		
		$condition->init();
		$fabric= new fabric($condition);
		$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
		
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_order();
		//echo "tppps";die;
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_orderAndEmbname();
		
		$wash= new wash($condition);
		$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

		$conversion = new conversion($condition);
		$conversion_costing_arr = $conversion->getAmountArray_by_order();
		
		foreach($attach_order_id as $bompoid)
		{
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['0']+=array_sum($fabric_costing_arr['sweater']['grey'][$bompoid])+array_sum($fabric_costing_arr['knit']['grey'][$bompoid])+array_sum($fabric_costing_arr['woven']['grey'][$bompoid]);
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['1']+=$yarn_costing_arr[$bompoid];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['2']+=$trims_costing_arr[$bompoid];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['3']+=$emblishment_costing_arr[$bompoid][1]+$emblishment_costing_arr[$bompoid][2]+$emblishment_costing_arr_name_wash[$bompoid][3]+$emblishment_costing_arr[$bompoid][4]+$emblishment_costing_arr[$bompoid][5];
			$job_wise_budge_amt[$powiseJobNoArr[$bompoid]]['4']+=array_sum($conversion_costing_arr[$bompoid]);			
		}
		//$budge_btb_open_amt+=array_sum($job_wise_budge_amt);
	}
	
	
	// echo "<pre>";print_r($job_wise_budge_amt['SSL-21-00192']);die;
	//echo "<pre>";print_r($job_wise_budge_amt['SSL-21-00102']);die;
	
	if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
	if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
	if($cbo_lein_bank == 0) $cbo_lein_bank="%%"; else $cbo_lein_bank = $cbo_lein_bank;
	if(trim($txt_file_no)!="") $txt_file_no =$txt_file_no; else $txt_file_no="%%";
	if(trim($hide_year)!="") $hide_year =$hide_year; else $hide_year="%%";
	
	$lc_sc_sql= " SELECT a.ID, a.BENEFICIARY_NAME, a.BUYER_NAME, a.CONTRACT_NO AS LC_SC_NO, a.CONTRACT_VALUE AS LC_SC_VAL, a.SC_YEAR AS LC_SC_YEAR, a.INTERNAL_FILE_NO, a.LIEN_BANK, a.EXPIRY_DATE, a.CONVERTIBLE_TO_LC AS REPL_CONV, a.SC_YEAR AS SC_LC_YEAR, a.CONVERTED_FROM, a.MAX_BTB_LIMIT, 2 AS TYPE, SUM (attached_qnty) AS ATTACHED_QNTY, c.id AS PO_ID, c.job_no_mst AS JOB_NO_MST, c.po_quantity AS PO_QNTY, c.po_total_price AS PO_VALUE, d.EQUIVALENT_FC FROM com_sales_contract a LEFT JOIN com_sales_contract_order_info b ON a.id = b.com_sales_contract_id AND b.is_deleted = 0 AND b.status_active = 1 LEFT JOIN wo_po_break_down c ON b.wo_po_break_down_id = c.id AND c.is_deleted = 0 AND c.status_active = 1 LEFT JOIN com_pre_export_lc_wise_dtls d ON d.LC_SC_ID = a.id  and d.EXPORT_TYPE=2 WHERE a.beneficiary_name LIKE '$cbo_company_name'  AND a.buyer_name LIKE '$cbo_buyer_name' AND a.internal_file_no LIKE '$txt_file_no' AND a.sc_year LIKE '$hide_year'AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.id, a.beneficiary_name, a.buyer_name, a.contract_no, a.contract_value, a.sc_year, a.internal_file_no, a.lien_bank, a.expiry_date, a.convertible_to_lc, a.sc_year, a.converted_from,a.MAX_BTB_LIMIT, c.id, c.job_no_mst, c.po_quantity, c.po_total_price, d.EQUIVALENT_FC 
    UNION ALL SELECT a.ID, a.BENEFICIARY_NAME, a.BUYER_NAME, a.EXPORT_LC_NO AS LC_SC_NO, a.LC_VALUE AS LC_SC_VAL, a.LC_YEAR AS LC_SC_YEAR, a.INTERNAL_FILE_NO, a.LIEN_BANK, a.EXPIRY_DATE, a.REPLACEMENT_LC AS REPL_CONV, a.LC_YEAR AS SC_LC_YEAR,a.MAX_BTB_LIMIT, 0 AS CONVERTED_FROM, 1 AS TYPE, SUM (attached_qnty) AS ATTACHED_QNTY, c.id AS PO_ID, c.job_no_mst AS JOB_NO_MST, c.po_quantity AS PO_QNTY, c.po_total_price AS PO_VALUE, d.EQUIVALENT_FC FROM com_export_lc a LEFT JOIN com_export_lc_order_info b ON a.id = b.com_export_lc_id AND b.is_deleted = 0 AND b.status_active = 1 LEFT JOIN wo_po_break_down c ON b.wo_po_break_down_id = c.id AND c.is_deleted = 0 AND c.status_active = 1 LEFT JOIN com_pre_export_lc_wise_dtls d ON d.LC_SC_ID = a.id  and d.EXPORT_TYPE=1 WHERE a.beneficiary_name LIKE '$cbo_company_name' AND a.buyer_name LIKE '$cbo_buyer_name'AND a.internal_file_no LIKE '$txt_file_no'AND a.lc_year LIKE '$hide_year' AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.id, a.beneficiary_name,a.MAX_BTB_LIMIT, a.buyer_name, a.export_lc_no, a.lc_value, a.lc_year, a.internal_file_no, a.lien_bank, a.expiry_date, a.replacement_lc, a.lc_year, c.id, c.job_no_mst, c.po_quantity, c.po_total_price, d.EQUIVALENT_FC ORDER BY INTERNAL_FILE_NO ASC, EXPIRY_DATE DESC";
	//echo $lc_sc_sql;die;

		$lc_sc_result=sql_select($lc_sc_sql);
		$total_row_count= count($lc_sc_result);

		$report_data2=array();
		foreach($lc_sc_result as $val)
		{
			if(!$report_data2[$val["INTERNAL_FILE_NO"]][$val["LC_SC_NO"]]["MAX_BTB_LIMIT"]){
				$report_data2[$val["INTERNAL_FILE_NO"]][$val["LC_SC_NO"]]["MAX_BTB_LIMIT"]+=$val["MAX_BTB_LIMIT"];
			}
			if(!$report_data2[$val["INTERNAL_FILE_NO"]][$val["LC_SC_NO"]]["CONVERTED_FROM"]){
				$report_data2[$val["INTERNAL_FILE_NO"]][$val["LC_SC_NO"]]["CONVERTED_FROM"]+=$val["CONVERTED_FROM"];
			}
			$report_data2_EqAr[$val["INTERNAL_FILE_NO"]][$val["LC_SC_NO"]]["EQUIVALENT_FC"]   +=$val["EQUIVALENT_FC"];
		}
		// echo "<pre>";
		// print_r($report_data2_EqAr);
		$report_data=array();
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;$lc_sc_file_arr=array();
		//echo "<pre>";print_r($job_wise_budge_amt);
		$p=0; 
		foreach($lc_sc_result as $val)
		{
	
		if($lc_sc_id_check1[$val["ID"]."*".$val["TYPE"]."*".$val["JOB_NO_MST"]]=="")
		{
			$lc_sc_id_check1[$val["ID"]."*".$val["TYPE"]."*".$val["JOB_NO_MST"]]=$val["ID"]."*".$val["TYPE"]."*".$val["JOB_NO_MST"];
			$file_ref=$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"];
			$report_data[$file_ref]["BTB_TOBE_OPEN"]+=$job_wise_budge_amt[$val["JOB_NO_MST"]][0]+$job_wise_budge_amt[$val["JOB_NO_MST"]][1]+$job_wise_budge_amt[$val["JOB_NO_MST"]][2]+$job_wise_budge_amt[$val["JOB_NO_MST"]][3];
			$tot_fab_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][0];
			$tot_yarn_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][1];
			$tot_trims_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][2];
			$tot_emb_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][3];
			$tot_conv_cost+=$job_wise_budge_amt[$val["JOB_NO_MST"]][4];
	
		}
		
		if($lc_sc_id_check[$val["ID"]."*".$val["TYPE"]]=="")
		{
			$lc_sc_id_check[$val["ID"]."*".$val["TYPE"]]=$val["ID"]."*".$val["TYPE"];
			$file_ref=$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"];
			$report_data[$file_ref]["BENEFICIARY_NAME"]=$val["BENEFICIARY_NAME"];
			$report_data[$file_ref]["BUYER_NAME"]=$val["BUYER_NAME"];
			$report_data[$file_ref]["SC_LC_YEAR"]=$val["SC_LC_YEAR"];
			$report_data[$file_ref]["INTERNAL_FILE_NO"]=$val["INTERNAL_FILE_NO"];
			$report_data[$file_ref]["LC_SC_NO"].=$val["LC_SC_NO"].",";
			$report_data[$file_ref]["LC_SC_ID"].=$val["ID"].",";
			$report_data[$file_ref]["LIEN_BANK"]=$val["LIEN_BANK"];
			$report_data[$file_ref]["EXPIRY_DATE"]=$val["EXPIRY_DATE"];
			//$report_data[$file_ref]["EQUIVALENT_FC"]+=$val["EQUIVALENT_FC"];
		
			
			//$report_data[$file_ref]["BTB_TOBE_OPEN"]=$job_wise_budge_amt[$val["JOB_NO_MST"]][0]+$job_wise_budge_amt[$val["JOB_NO_MST"]][1]+$job_wise_budge_amt[$val["JOB_NO_MST"]][2]+$job_wise_budge_amt[$val["JOB_NO_MST"]][3];
			
			$lc_sc_file_arr[$val["ID"]."*".$val["TYPE"]]=$val["INTERNAL_FILE_NO"];
			if($val["TYPE"]==2)
			{
				if($val["REPL_CONV"]!=2 && $val["CONVERTED_FROM"] <=0)
				{
					$report_data[$file_ref]["FILE_VALUE"]+=$val["LC_SC_VAL"];	
				}
				if($val["REPL_CONV"]==2)
				{
					$report_data[$file_ref]["FILE_VALUE"]+=$val["LC_SC_VAL"];
						
				}
				$refrID1=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")";oci_rollback($con);die;
				}
			}
			else
			{
				if($val["REPL_CONV"]!=1)
				{
				$report_data[$file_ref]["FILE_VALUE"]+=$val["LC_SC_VAL"];
					
			}
				
			$refrID1=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")");
			if(!$refrID1)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val["ID"].",".$val["TYPE"].",".$user_id.")";oci_rollback($con);die;
			}
			  }
			$temp_table_id++;
		 }
	
		
		$file_ref=$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"];
		if($po_id_check[$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"]."*".$val["PO_ID"]]=="")
		{
			$po_id_check[$val["INTERNAL_FILE_NO"]."*".$val["BENEFICIARY_NAME"]."*".$val["PO_ID"]]=$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"];
			$report_data[$file_ref]["LC_SC_PO_QNTY"]+=$val["PO_QNTY"];
			$report_data[$file_ref]["LC_SC_PO_VALUE"]+=$val["PO_VALUE"];
		}

		if($att_qnt_check[$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"]]=="")
		{
			$att_qnt_check[$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"]]=$val["ID"]."*".$val["TYPE"]."*".$val["PO_ID"];
			$report_data[$file_ref]["LC_SC_ATTACHED_QNTY"]+=$val["ATTACHED_QNTY"];
		}
	}
	// echo "<pre>";
	// 	print_r($report_data2);die;
	
	unset($lc_sc_result);
	unset($po_id_check);
	unset($att_qnt_check);
	if($refrID1)
	{
		oci_commit($con);
	}
	//echo "tres";die;
	//echo $p."<pre>";print_r($report_data);die;
	$btb_sql="SELECT c.IMPORTER_ID, b.IMPORT_MST_ID, b.CURRENT_DISTRIBUTION, b.LC_SC_ID, c.LC_VALUE, 1 as TYPE 
	from GBL_TEMP_REPORT_ID a, COM_BTB_EXPORT_LC_ATTACHMENT b, COM_BTB_LC_MASTER_DETAILS c 
	where a.REF_VAL=b.LC_SC_ID and b.IMPORT_MST_ID=c.ID and a.REF_FROM=1 and b.IS_LC_SC=0 and c.LC_NUMBER IS NOT NULL  and b.status_active=1 and b.is_deleted=0
	union all
	select c.IMPORTER_ID, b.IMPORT_MST_ID, b.CURRENT_DISTRIBUTION, b.LC_SC_ID, c.LC_VALUE, 2 as TYPE 
	from GBL_TEMP_REPORT_ID a, COM_BTB_EXPORT_LC_ATTACHMENT b, COM_BTB_LC_MASTER_DETAILS c 
	where a.REF_VAL=b.LC_SC_ID and b.IMPORT_MST_ID=c.ID and a.REF_FROM=2 and b.IS_LC_SC=1 and c.LC_NUMBER IS NOT NULL  and b.status_active=1 and b.is_deleted=0";
	// echo $btb_sql;die;
	$btb_sql_result=sql_select($btb_sql);
	foreach($btb_sql_result as $row)
	{
		$file_no=$lc_sc_file_arr[$row["LC_SC_ID"]."*".$row["TYPE"]]."*".$row["IMPORTER_ID"];
		if($btb_check[$row["LC_SC_ID"]."*".$row["IMPORT_MST_ID"]."*".$row["TYPE"]]=="")
		{
			$btb_check[$row["LC_SC_ID"]."*".$row["IMPORT_MST_ID"]."*".$row["TYPE"]]=$row["LC_SC_ID"]."*".$row["IMPORT_MST_ID"]."*".$row["TYPE"];
			$report_data[$file_no]["BTB_OPEN"]+=$row["CURRENT_DISTRIBUTION"];
			$report_data[$file_no]["IMPORT_MST_ID"].=$row["IMPORT_MST_ID"].",";
		}
	}
	
	unset($btb_sql_result);
	
	$inv_sql="select b.ID, b.BENIFICIARY_ID, b.INVOICE_QUANTITY, b.INVOICE_VALUE, b.NET_INVO_VALUE, b.LC_SC_ID, b.IS_LC as TYPE from GBL_TEMP_REPORT_ID a, COM_EXPORT_INVOICE_SHIP_MST b where a.REF_VAL=b.LC_SC_ID and a.REF_FROM=b.IS_LC and b.status_active=1 and b.is_deleted=0";
	//echo $inv_sql;die;
	$inv_sql_result=sql_select($inv_sql);
	foreach($inv_sql_result as $row)
	{
		$file_no=$lc_sc_file_arr[$row["LC_SC_ID"]."*".$row["TYPE"]]."*".$row["BENIFICIARY_ID"];
		
		if($inv_check[$row["ID"]]=="")
		{
			$inv_check[$row["ID"]]=$row["ID"];
			$report_data[$file_no]["INV_ID"].=$row["ID"].",";
			$report_data[$file_no]["SHIP_QNTY"]+=$row["INVOICE_QUANTITY"];
			$report_data[$file_no]["SHIP_VALUE"]+=$row["INVOICE_VALUE"];
			$report_data[$file_no]["NET_INVO_VALUE"]+=$row["NET_INVO_VALUE"];
		}
	}
	unset($inv_sql_result);
	
	
	$gblDel=execute_query("delete from GBL_TEMP_REPORT_ID where USER_ID=$user_id");
	if($gblDel)
	{
		oci_commit($con);
	}
	// echo "<pre>";print_r($report_data);die;
	
	ob_start();
	?>
    <div style="width:2250px;" id="scroll_body">
        <fieldset style="width:100%">
            <table width="2250" cellpadding="0" cellspacing="0" id="caption" align="left">
                <tr>
                    <td align="center" width="100%" colspan="23" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="2250" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                <thead>
                    <tr>
                        <th rowspan="2" width="30">Sl</th>
                        <th width="130" rowspan="2">Company</th>
                        <th width="150" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">File Year</th>
                        <th width="80" rowspan="2">Int. File No.</th>
                        <th width="200" rowspan="2">SC/LC No.</th>
                        <th width="100" rowspan="2">File Value</th>
                        <th width="130" rowspan="2">Lien Bank</th>
                        <th width="80" rowspan="2">Expairy Date</th>
                        <th colspan="6">BTB Value Open Status</th>
						<th width="100"></th>
                        <th colspan="7">Export Status</th>
                    </tr>
                    <tr>
						<th width="100"> % Of BTB To Be Open </th>
                        <th width="100">BTB Need To Open</th>
                        <th width="100">Opened</th>
                        <th width="80">(%) Of Opend</th>
                        <th width="100">Excess /(Balance)  BTB Opened</th>
                        <th width="80">Excess/ ( Short)  %</th>
                        <th width="100">PC RECEIVED </th>
                        <th width="80">Order Qty (Pcs)</th>
                        <th width="80">Order Value</th>
                        <th width="100">Shipped Qty. (Pcs)</th>
                        <th width="100">Shipped Value ($)</th>
                        <th width="80">Value %</th>
                        <th width="80">Bal. Qty Pcs</th>
                        <th>Bal. Value($)</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach ($report_data as $key=>$val)
				{	
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$btb_to_be_open=($val['BTB_TOBE_OPEN']/($val['LC_SC_PO_QNTY']*1))*($val['LC_SC_ATTACHED_QNTY']*1);
					// if($val['BTB_OPEN']!=0 && $val['FILE_VALUE']!=0) $btb_open_percent=(($val['BTB_OPEN']/$val['FILE_VALUE'])*100); $btb_open_percent=0;
					if($val['BTB_OPEN']!=0 && $val['FILE_VALUE']!=0){ $btb_open_percent=(($val['BTB_OPEN']/$val['FILE_VALUE'])*100); }else{$btb_open_percent=0;}
					if($val['FILE_VALUE']!=0){ $file_60=(($val['FILE_VALUE']/100)*50); }else{ $file_60=0; }
					// $file_space=$val['BTB_TOBE_OPEN']-$val['BTB_OPEN'];
					$file_space=$btb_to_be_open-$val['BTB_OPEN'];
					//$file_space_percent=(($file_space/$file_60)*100);
					// if($file_space!=0 && $val['BTB_OPEN']!=0){ $file_space_percent=(($val['BTB_OPEN']/$file_space)*100); }else{ $file_space_percent=0;}
					// if($val['SHIP_VALUE']!=0 && $val['FILE_VALUE']!=0){ $ship_percent=(($val['SHIP_VALUE']/$val['FILE_VALUE'])*100); }else{ $ship_percent=0;}
					if($file_space!=0 && $btb_to_be_open!=0){ $file_space_percent=(($file_space/$btb_to_be_open)*100); }else{ $file_space_percent=0;}
					if($val['SHIP_VALUE']!=0 && $val['LC_SC_PO_VALUE']!=0){ $ship_percent=(($val['SHIP_VALUE']/$val['LC_SC_PO_VALUE'])*100); }else{ $ship_percent=0;}
					$balance_qnty=$val['LC_SC_PO_QNTY']-$val['SHIP_QNTY'];
					$balance_value=$val['LC_SC_PO_VALUE']-$val['SHIP_VALUE'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i;?></td>
						<td style="word-break:break-all;"><? echo $company_arr[$val['BENEFICIARY_NAME']];?></td>
						<td style="word-break:break-all;"><? echo $buyer_arr[$val['BUYER_NAME']];?></td>
						<td align="center" style="word-break:break-all;"><? echo $val['SC_LC_YEAR'];?></td>
						<td align="center" style="word-break:break-all;"><? echo $val['INTERNAL_FILE_NO'];?></td>
						<td style="word-break:break-all;"><? echo chop($val['LC_SC_NO'],','); ?></td>
                        <td align="right"><? echo number_format($val['FILE_VALUE'],2);?></td>
                        <td style="padding-left:10px; word-break:break-all;"><? echo $lein_bank_arr[$val['LIEN_BANK']];?></td>
						<td align="center" style="word-break:break-all;"><? echo change_date_format($val[('EXPIRY_DATE')]);?></td>
                        <td align="right" ><?
						
						$lc_arr = array_unique(explode(",", $val["LC_SC_NO"]));
						// print_r($lc_arr);
						// echo $count; // This will output the count of '00977010603866OC' in the array
						$count=count(array_filter($lc_arr));
						$val1=0;$val2=0;$tottalVal=0;
						foreach($lc_arr as $lcsc){
						//echo "u_". $val["INTERNAL_FILE_NO"];
						//print_r($report_data2[$val["INTERNAL_FILE_NO"]][$lcsc]["CONVERTED_FROM"]);
						$val1+=$report_data2[$val["INTERNAL_FILE_NO"]][$lcsc]["CONVERTED_FROM"];
						$val2+=$report_data2[$val["INTERNAL_FILE_NO"]][$lcsc]["MAX_BTB_LIMIT"];
						$tottalVal = $val1+$val2;
						}
						echo number_format(($tottalVal)/$count,2)." %"?></td>
						<td align="right" title="File Value* % OF BTB TO BE OPEN"><? echo ($val['FILE_VALUE']*number_format(($tottalVal)/$count,2))/100?></td>
						<td align="right" ><a href='#report_details' style='color:#000' onClick="openmypage_submission('<? echo chop($val['IMPORT_MST_ID'],',')?>','<?=$key;?>');"><? echo number_format($val['BTB_OPEN'],2);?></a></td>
						<td align="right" title="(Opened /File Value )*100"><? echo number_format(($val['BTB_OPEN']/$val['FILE_VALUE'])*100,2)."%"?></td>
						<td align="right" title="(BTB Need To Open-Opened"><?echo ($val['FILE_VALUE']*number_format(($tottalVal)/$count,2))/100-$val['BTB_OPEN']?></td>
						<td align="right" title="( % OF BTB TO BE OPEN -(%) of Opend"><? echo  number_format(($tottalVal)/$count,2)-number_format(($val['BTB_OPEN']/$val['FILE_VALUE'])*100,2)?></td>
						
						<td align="right"><?  
						 $lc_arr = array_unique(explode(",", $val["LC_SC_NO"]));
						//  echo "<pre>";
						//  print_r($lc_arr);
						// echo "LC = ".$lcNo;
						$totalCl = 0;
						foreach($lc_arr as $key=>$lcNo)
						{
							$totalCl +=  $report_data2_EqAr[$val["INTERNAL_FILE_NO"]][$lcNo]["EQUIVALENT_FC"];
						}
					   echo $totalCl  ;
						//echo $total ; ?></td>
                        <td align="right"><? echo number_format($val['LC_SC_PO_QNTY'],2);?></td>
                        <td align="right"><? echo number_format($val['LC_SC_PO_VALUE'],2);?></td>
                        <td align="right"><? echo number_format($val['SHIP_QNTY'],2);?></td>
                        <td align="right"><a href='#report_details' style='color:#000' onClick="openmypage_invoice('<? echo chop($val['INV_ID'],',')?>');"><? echo number_format($val['SHIP_VALUE'],2);?></a></td>
                        <td align="right"><? echo number_format($ship_percent,2); ?></td>
                        <td align="right"><? if($balance_qnty>0){ echo number_format($balance_qnty,2);$total_balance_po_qnty+=$balance_qnty;}else{echo "0.00";} ?></td>
                        <td align="right"><? if($balance_value>0){ echo number_format($balance_value,2);$total_balance_po_value+=$balance_value;}else{echo "0.00";} ?></td>
					</tr>
					<?
					$i++;
					$total_file_value+=$val['FILE_VALUE'];
					// $total_btb_tobe_open+=$val['BTB_TOBE_OPEN'];
					$total_btb_open+=$val['BTB_OPEN'];
					$total_ship_qnty+=$val['SHIP_QNTY'];
					$total_ship_value+=$val['SHIP_VALUE'];
					$total_po_qnty+=$val['LC_SC_PO_QNTY'];
					$total_po_value+=$val['LC_SC_PO_VALUE'];
					$total_btb+=number_format(($tottalVal)/$count,2);
					$total_btb_tobe_open+= ($val['FILE_VALUE']*number_format(($tottalVal)/$count,2))/100;
					$total_open+=($val['BTB_OPEN']/$val['FILE_VALUE'])*100;
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th align="right">Total:</th>
                        <th align="right"><? echo number_format($total_file_value,2)?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_btb,2)."%"?></th>
                        <th align="right"><? echo number_format($total_btb_tobe_open,2)?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_open,2)."%"?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_po_qnty,2)?></th>
                        <th align="right"><? echo number_format($total_po_value,2)?></th>
                        <th align="right"><? echo number_format($total_ship_qnty,2)?></th>
                        <th align="right"><? echo number_format($total_ship_value,2)?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_balance_po_qnty,2)?></th>
                        <th align="right"><? echo number_format($total_balance_po_value,2)?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
	<?
    foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}




if ($action=="btb_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$btb_id=str_replace("'","",$btb_id);
	$data=explode('*',$file_no);
	$int_file_no=$data[0];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank ",'id','bank_name');
	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier ",'id','supplier_name');
	if($db_type==0)
	{
		$select_pi_num=", group_concat(c.PI_NUMBER) as PI_NUMBER";
	}
	else
	{
		$select_pi_num=", listagg(cast(c.PI_NUMBER as varchar(4000)),', ') within group(order by c.id) as PI_NUMBER";
	}
	
	/* $sql_btb="select a.ID as BTB_ID, a.LC_NUMBER, a.IMPORTER_ID, a.SUPPLIER_ID, a.ISSUING_BANK_ID, a.LC_DATE, a.LC_EXPIRY_DATE, a.LC_VALUE $select_pi_num 
	from COM_BTB_LC_MASTER_DETAILS a, COM_BTB_LC_PI b, COM_PI_MASTER_DETAILS c 
	where a.ID=b.COM_BTB_LC_MASTER_DETAILS_ID and b.PI_ID=c.ID and a.id in($btb_id) and a.LC_NUMBER IS NOT NULL and b.status_active=1 and b.is_deleted=0
	group by a.ID, a.LC_NUMBER, a.IMPORTER_ID, a.SUPPLIER_ID, a.ISSUING_BANK_ID, a.LC_DATE, a.LC_EXPIRY_DATE, a.LC_VALUE
	order by a.LC_DATE ASC"; */
	$sql_btb_pi="select a.ID as BTB_ID $select_pi_num 
	from com_btb_lc_master_details a, COM_BTB_LC_PI b, COM_PI_MASTER_DETAILS c 
	where a.ID=b.com_btb_lc_master_details_id and b.PI_ID=c.ID and a.id in($btb_id) and a.LC_NUMBER IS NOT NULL and b.status_active=1 and b.is_deleted=0
	group by a.ID";
	// echo $sql_btb_pi;
	$sql_re_pi=sql_select($sql_btb_pi);
	$pi_data_arr=array();
	foreach($sql_re_pi as $row)
	{
		$pi_data_arr[$row['BTB_ID']]=$row["PI_NUMBER"];
	}


	$sql_btb="SELECT a.ID as BTB_ID, a.LC_NUMBER, a.IMPORTER_ID, a.SUPPLIER_ID, a.ISSUING_BANK_ID, a.LC_DATE, a.LC_EXPIRY_DATE, sum(b.current_distribution) as LC_VALUE 
	from com_btb_lc_master_details a, com_btb_export_lc_attachment b, com_sales_contract c 
	where a.id in($btb_id) and a.LC_NUMBER IS NOT NULL and b.status_active=1 and c.internal_file_no like '$int_file_no'and a.id=b.import_mst_id and b.is_lc_sc=1 and b.lc_sc_id=c.id
	group by a.ID, a.LC_NUMBER, a.IMPORTER_ID, a.SUPPLIER_ID, a.ISSUING_BANK_ID, a.LC_DATE, a.LC_EXPIRY_DATE
	union all 
	SELECT a.ID as BTB_ID, a.LC_NUMBER, a.IMPORTER_ID, a.SUPPLIER_ID, a.ISSUING_BANK_ID, a.LC_DATE, a.LC_EXPIRY_DATE, sum(b.current_distribution) as LC_VALUE
	from COM_BTB_LC_MASTER_DETAILS a, com_btb_export_lc_attachment b, com_export_lc c 
	where a.id in($btb_id) and a.LC_NUMBER IS NOT NULL and b.status_active=1 and c.internal_file_no like '$int_file_no' and a.id=b.import_mst_id and b.is_lc_sc=0 and b.lc_sc_id=c.id
	group by a.ID, a.LC_NUMBER, a.IMPORTER_ID, a.SUPPLIER_ID, a.ISSUING_BANK_ID, a.LC_DATE, a.LC_EXPIRY_DATE
	order by LC_DATE ASC";
	// echo $sql_btb;die;

	$i=1;
	$sql_re=sql_select($sql_btb);
	
	?>
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

	</script>
    <?
	ob_start();
	$html='<div id="report_container" align="center" style="width:950px">
    <fieldset style="width:950px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" style="overflow-x:auto;">
            <thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">BTB LC No</th>
                    <th width="160">PI No</th>
                    <th width="130">Applicant</th>
                    <th width="130">Bank</th>
                    <th width="130">Benficiary</th>
                    <th width="70">LC Date</th>
                    <th width="70">LC Expiry Date</th>
                    <th>LC Amount (USD)</th>
                </tr>
            </thead>
            <tbody>';
			?>
	<div id="report_container" align="center" style="width:950px">
    <fieldset style="width:950px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" style="overflow-x:auto;">
            <thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="110">BTB LC No</th>
                    <th width="160">PI No</th>
                    <th width="130">Applicant</th>
                    <th width="130">Bank</th>
                    <th width="130">Benficiary</th>
                    <th width="80">LC Date</th>
                    <th width="80">LC Expiry Date</th>
                    <th width="100">LC Amount (USD)</th>
                </tr>
            </thead>
            <tbody>
            <?					
			foreach($sql_re as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				$html.='<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">
                	<td align="center" title="'.$row["BTB_ID"].'">'. $i .'</td>
                    <td><p>'.$row["LC_NUMBER"].'&nbsp;</p></td>
                    <td><p>'.$pi_data_arr[$row['BTB_ID']].'&nbsp;</p></td>
                    <td><p>'.$supplier_arr[$row["SUPPLIER_ID"]].'&nbsp;</p></td>
                    <td><p>'.$lein_bank_arr[$row["ISSUING_BANK_ID"]].'&nbsp;</p></td>
                    <td><p>'.$company_arr[$row["IMPORTER_ID"]].'&nbsp;</p></td>
                    <td align="center"><p>'.change_date_format($row["LC_DATE"]).'&nbsp;</p></td>
                    <td align="center"><p>'.change_date_format($row["LC_EXPIRY_DATE"]).'&nbsp;</p></td>
                    <td align="right">'.number_format($row["LC_VALUE"],2).'</td>
                </tr>';
				
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $row["LC_NUMBER"]; ?>&nbsp;</p></td>
                    <td><p><? echo $row["PI_NUMBER"]; ?>&nbsp;</p></td>
                    <td><p><? echo $supplier_arr[$row["SUPPLIER_ID"]]; ?>&nbsp;</p></td>
                    <td><p><? echo $lein_bank_arr[$row["ISSUING_BANK_ID"]]; ?>&nbsp;</p></td>
                    <td><p><? echo $company_arr[$row["IMPORTER_ID"]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row["LC_DATE"]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row["LC_EXPIRY_DATE"]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row["LC_VALUE"],2)?></td>
                </tr>
                <?
				
				$total_btb_value+=$row["LC_VALUE"];
				$i++;
			}
			$html.='</tbody>
            <tfoot>
            	<tr>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right">'.number_format($total_btb_value,2).'</th>
                </tr>
            </tfoot>
        </table>
    </fieldset>';
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_btb_value,2);?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
		//if( @filemtime($filename) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	ob_end_clean();
	?>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    &nbsp; <a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>
    <?
	echo $html; 
    exit();
}

if ($action=="invoice_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$invoice_id=str_replace("'","",$invoice_id);
	if($invoice_id=="" && $invoice_id ==0) {echo "No Data Found";die;}
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank ",'id','bank_name');
	//$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier ",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$sql_inv="select a.CONTRACT_NO as LC_NUMBER, a.BUYER_NAME, a.PAY_TERM, a.CURRENCY_NAME, b.ID as INV_ID, b.INVOICE_NO, b.INVOICE_DATE, b.IS_LC, b.INVOICE_QUANTITY, b.INVOICE_VALUE, b.NET_INVO_VALUE, b.EX_FACTORY_DATE 
	from COM_SALES_CONTRACT a, COM_EXPORT_INVOICE_SHIP_MST b
	where a.ID=b.LC_SC_ID and b.IS_LC=2 and b.id in($invoice_id) and b.status_active=1 and b.is_deleted=0
	union all
	select a.EXPORT_LC_NO as LC_NUMBER, a.BUYER_NAME, a.PAY_TERM, a.CURRENCY_NAME, b.ID as INV_ID, b.INVOICE_NO, b.INVOICE_DATE, b.IS_LC, b.INVOICE_QUANTITY, b.INVOICE_VALUE, b.NET_INVO_VALUE, b.EX_FACTORY_DATE 
	from COM_EXPORT_LC a, COM_EXPORT_INVOICE_SHIP_MST b
	where a.ID=b.LC_SC_ID and b.IS_LC=1 and b.id in($invoice_id) and b.status_active=1 and b.is_deleted=0
	order by INVOICE_DATE";
	//echo $sql_inv;die;
	$i=1;
	$sql_re=sql_select($sql_inv);
	
	$bill_sql="select a.ID as BILL_ID, a.BANK_REF_NO, a.BANK_REF_DATE, b.INVOICE_ID from COM_EXPORT_DOC_SUBMISSION_MST a, COM_EXPORT_DOC_SUBMISSION_INVO b where a.id=b.DOC_SUBMISSION_MST_ID and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.INVOICE_ID in($invoice_id)";
	$bill_result=sql_select($bill_sql);
	$bill_data=array();
	foreach($bill_result as $row)
	{
		$bill_data[$row["INVOICE_ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
		$bill_data[$row["INVOICE_ID"]]["BANK_REF_DATE"]=$row["BANK_REF_DATE"];
	}
	unset($bill_result);
	$rlz_sql="select a.INVOICE_ID, b.RECEIVED_DATE from COM_EXPORT_DOC_SUBMISSION_INVO a, COM_EXPORT_PROCEED_REALIZATION b where a.DOC_SUBMISSION_MST_ID=b.INVOICE_BILL_ID and b.INVOICE_BILL_ID=1 and a.INVOICE_ID in($invoice_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$rlz_result=sql_select($rlz_sql);
	$realized_data=array();
	foreach($rlz_result as $row)
	{
		$realized_data[$row["INVOICE_ID"]]=$row["BANK_REF_NO"];
	}
	unset($rlz_result);
	
	$exfact_sql="select a.INVOICE_NO, a.EX_FACTORY_QNTY from PRO_EX_FACTORY_MST a where a.status_active=1 and a.is_deleted=0 and a.INVOICE_NO in($invoice_id)";
	$exfact_sql_result=sql_select($exfact_sql);
	$ex_factory_data=array();
	foreach($exfact_sql_result as $row)
	{
		$ex_factory_data[$row["INVOICE_NO"]]+=$row["EX_FACTORY_QNTY"];
	}
	unset($exfact_sql_result);
	
	$lc_sc_type_arr=array(1=>"LC",2=>"SC");
	?>
    <script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}

	</script>
    <?
	ob_start();
	$html='<div id="report_container" align="center" style="width:1490px">
    <fieldset style="width:1490px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" style="overflow-x:auto;">
            <thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">Invoice No.</th>
                    <th width="80">Invoice Date</th>
                    <th width="40">SC/LC</th>
                    <th width="120">SC/LC No.</th>
                    <th width="70">Pay Term</th>
                    <th width="130">Buyer Name</th>
                    <th width="90">Ex-factory Qnty</th>
                    <th width="90">Invoice Qnty.</th>
                    <th width="100">Invoice value</th>
                    <th width="100">Net Invoice Amount</th>
                    <th width="70">Currency</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="100">Bank Bill No.</th>
                    <th width="80">Bank Bill Date</th>
                    <th width="80">Actual Realized Date</th>
                    <th>Realization Amount</th>
                </tr>
            </thead>
            <tbody>';
	?>
    <div id="report_container" align="center" style="width:1490px">
    <fieldset style="width:1490px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" style="overflow-x:auto;">
            <thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">Invoice No.</th>
                    <th width="80">Invoice Date</th>
                    <th width="40">SC/LC</th>
                    <th width="120">SC/LC No.</th>
                    <th width="70">Pay Term</th>
                    <th width="130">Buyer Name</th>
                    <th width="90">Ex-factory Qnty</th>
                    <th width="90">Invoice Qnty.</th>
                    <th width="100">Invoice value</th>
                    <th width="100">Net Invoice Amount</th>
                    <th width="70">Currency</th>
                    <th width="80">Ex-Factory Date</th>
                    <th width="100">Bank Bill No.</th>
                    <th width="80">Bank Bill Date</th>
                    <th width="80">Actual Realized Date</th>
                    <th>Realization Amount</th>
                </tr>
            </thead>
            <tbody>
            <?
			foreach($sql_re as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				$net_inv_val="";
				if($realized_data[$row["INV_ID"]]!="" && $realized_data[$row["INV_ID"]]!="0000-00-00") $net_inv_val=$row["NET_INVO_VALUE"];
				$html.='<tr bgcolor="'. $bgcolor.'" onClick="change_color(\'tr_'. $i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">
                	<td align="center">'. $i.'</td>
                    <td><p>'.$row["INVOICE_NO"].'&nbsp;</p></td>
                    <td align="center"><p>'.change_date_format($row["INVOICE_DATE"]).'&nbsp;</p></td>
                    <td align="center"><p>'.$lc_sc_type_arr[$row["IS_LC"]].'&nbsp;</p></td>
                    <td><p>'. $row["LC_NUMBER"].'&nbsp;</p></td>
                    <td><p>'.$pay_term[$row["PAY_TERM"]].'&nbsp;</p></td>
                    <td><p>'.$buyer_arr[$row["BUYER_NAME"]].'&nbsp;</p></td>
                    <td align="right">'.number_format($ex_factory_data[$row["INV_ID"]],2).'</td>
                    <td align="right">'.number_format($row["INVOICE_QUANTITY"],2).'</td>
                    <td align="right">'.number_format($row["INVOICE_VALUE"],2).'</td>
                    <td align="right">'.number_format($row["NET_INVO_VALUE"],2).'</td>
                    <td align="center"><p>'.$currency[$row["CURRENCY_NAME"]].'&nbsp;</p></td>
                    <td align="center"><p>'.change_date_format($row["EX_FACTORY_DATE"]).'&nbsp;</p></td>
                    <td><p>'.$bill_data[$row["INV_ID"]]["BANK_REF_NO"].'&nbsp;</p></td>
                    <td align="center"><p>'. change_date_format($bill_data[$row["INV_ID"]]["BANK_REF_DATE"]).'&nbsp;</p></td>
                    <td align="center"><p>'. change_date_format($realized_data[$row["INV_ID"]]).'&nbsp;</p></td>
                    <td align="center"><p>'.$net_inv_val.'&nbsp;</p></td>
                </tr>';
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $row["INVOICE_NO"]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row["INVOICE_DATE"]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $lc_sc_type_arr[$row["IS_LC"]]; ?>&nbsp;</p></td>
                    <td><p><? echo $row["LC_NUMBER"]; ?>&nbsp;</p></td>
                    <td><p><? echo $pay_term[$row["PAY_TERM"]]; ?>&nbsp;</p></td>
                    <td><p><? echo $buyer_arr[$row["BUYER_NAME"]]; ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($ex_factory_data[$row["INV_ID"]],2); ?></td>
                    <td align="right"><? echo number_format($row["INVOICE_QUANTITY"],2); ?></td>
                    <td align="right"><? echo number_format($row["INVOICE_VALUE"],2); ?></td>
                    <td align="right"><? echo number_format($row["NET_INVO_VALUE"],2); ?></td>
                    <td align="center"><p><? echo $currency[$row["CURRENCY_NAME"]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($row["EX_FACTORY_DATE"]!="" && $row["EX_FACTORY_DATE"]!="0000-00-00") echo change_date_format($row["EX_FACTORY_DATE"]); ?>&nbsp;</p></td>
                    <td><p><? echo $bill_data[$row["INV_ID"]]["BANK_REF_NO"]; ?>&nbsp;</p></td>
                    <td align="center"><p><? if($bill_data[$row["INV_ID"]]["BANK_REF_DATE"]!="" && $bill_data[$row["INV_ID"]]["BANK_REF_DATE"]!="0000-00-00") echo change_date_format($bill_data[$row["INV_ID"]]["BANK_REF_DATE"]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($realized_data[$row["INV_ID"]]!="" && $realized_data[$row["INV_ID"]]!="0000-00-00") echo change_date_format($realized_data[$row["INV_ID"]]); ?>&nbsp;</p></td>
                    <td align="center"><p><? if($realized_data[$row["INV_ID"]]!="" && $realized_data[$row["INV_ID"]]!="0000-00-00") echo number_format($row["NET_INVO_VALUE"],2); else echo "0.00"; ?>&nbsp;</p></td>
                </tr>
                <?
				$i++;
				$total_exFact_qnty+=$ex_factory_data[$row["INV_ID"]];
				$total_invoice_qnty+=$row["INVOICE_QUANTITY"];
				$total_invoice_value+=$row["INVOICE_VALUE"];
				$total_net_invoice_value+=$row["NET_INVO_VALUE"];
			}
			$html.='</tbody>
            <tfoot>
            	<tr>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right">'.number_format($total_exFact_qnty,2).'</th>
                    <th align="right">'.number_format($total_invoice_qnty,2).'</th>
                    <th align="right">'.number_format($total_invoice_value,2).'</th>
                    <th align="right">'.number_format($total_net_invoice_value,2).'</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>';
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_exFact_qnty,2); ?></th>
                    <th align="right"><? echo number_format($total_invoice_qnty,2); ?></th>
                    <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                    <th align="right"><? echo number_format($total_net_invoice_value,2); ?></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
		//if( @filemtime($filename) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	ob_end_clean();
	?>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    &nbsp; <a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>
    <?
	echo $html; 
    exit();
}

if ($action=="load_drop_down_search")
{
	$data=explode('_',$data);
	if($data[1]==1) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	
	if($data[1]==2) echo create_drop_down( "txt_search_common", 170, "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lein Bank --", $selected, "",0,"" );
	if($data[1]==3) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	exit();
}

if ($action=="file_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;  
?>
<script>
	
	function js_set_value(str)
	{
		$("#hide_file_no").val(str);
		parent.emailwindow.hide();
	}
	function set_caption(id)
	{
	if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
	if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Lein Bank';
	if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter  SC/LC';
	}
</script>
</head>
<body>
    <div style="width:530px">
    <form name="search_order_frm"  id="search_order_frm">
    <fieldset style="width:530px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
			    <th>Year</th>
                <th>Search By</th>
                <th id="search_by_td_up">Enter File No</th>
                <th> 
                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/> 
               
                <input type="hidden" name="txt_lien_bank_id" id="txt_lien_bank_id" value="<?  echo $lien_bank; ?>"/> 
                <input type="hidden" name="txt_selected_file" id="txt_selected_file" value=""/> 
                </th>
            </thead>
            <tbody>
            
                <tr class="general">
				<td>
	                    <?
						$com_cond="";
						if($company_id>0) $com_cond=" and beneficiary_name='$company_id'";
						$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where status_active=1 and is_deleted=0 $com_cond  union all select sc_year as lc_sc_year from com_sales_contract where status_active=1 and is_deleted=0 $com_cond");
						foreach($sql as $row)
						{
							$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
						}
						echo create_drop_down( "cbo_year", 100,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
						?>
	                    </td>
                    <td>
                    <?
					$sarch_by_arr=array(1=>"File No",2=>"Lien Bank",3=>"SC/LC"); 
					echo create_drop_down( "cbo_search_by", 170,$sarch_by_arr,"", 1, "-- Select Search --", 1,"load_drop_down( 'file_wise_btb_open_report_controller',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
					?>
                    </td>
                    <td align="center" id="search_by_td">
                    <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />
                    </td>
                    <td>
                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?  echo $company_id; ?>+'_'+<?  echo $buyer_id; ?>+'_'+<?  echo $lien_bank;?>,'search_file_info','search_div_file','file_wise_btb_open_report_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="100%">
            <tr>
                <td>
                <div style="width:560px; margin-top:5px" id="search_div_file" align="left"></div>
                </td>
            </tr>
        </table>
    </fieldset>
    </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

	exit();
}


if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	// print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	 $txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	$buyer_id = $ex_data[3];
	$lien_bank_id = $ex_data[4];
	$cbo_year = $ex_data[5];
	//echo $cbo_year; die;
	if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	$com_cond="";
	if($company_id>0) $com_cond=" and beneficiary_name='$company_id'";
	if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else
	{
		$year_cond_sc="";
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'";
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	//if($txt_search_common==0)$txt_search_common="";

    $txt_search_common = trim($txt_search_common);
    $search_cond ="";$search_cond_lc="";$search_cond_sc="";
    if($txt_search_common!="")
    {
        if($cbo_search_by==1)
        {
            $search_cond .= " and internal_file_no like '%$txt_search_common%'";
        }
        // else if($cbo_search_by==2)
        // {
        //     $search_cond .= " and buyer_name='$txt_search_common'";
        // }
        else if($cbo_search_by==2)
        {
            $search_cond .= " and lien_bank='$txt_search_common'";
        }
        else if($cbo_search_by==3)
        {
            $search_cond_lc .= " and export_lc_no='$txt_search_common'";
            $search_cond_sc .= " and contract_no='$txt_search_common'";
        }
    }
    //echo $cbo_search_by."**".$txt_search_common; die;
    //echo $cbo_search_by."**".$search_cond_lc."**".$search_cond_sc; die;
    if($db_type == 0)
    {
        $sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , group_concat(a.export_lc_no) as export_lc_no
        from (
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, group_concat(export_lc_no) as export_lc_no, 'export' as type
              from com_export_lc
             where status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc $com_cond
             group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
             union all
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,group_concat(contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where  status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc $com_cond
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
          group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";
    }
    else
    {
        $sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , listagg(cast(a.export_lc_no as varchar(4000)),',') within group(order by a.export_lc_no) as export_lc_no
        from (
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, listagg(cast(export_lc_no as varchar(4000)),',') within group(order by export_lc_no) as export_lc_no, 'export' as type
              from com_export_lc
             where status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc $com_cond
             group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
             union all
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,listagg(cast(contract_no as varchar(4000)),',') within group(order by contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc $com_cond
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
          group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";

		  //echo $sql;
    }
	//echo $sql;
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank ",'id','bank_name');
	$buyer_arr=return_library_array( "select buyer_name,id from lib_buyer ",'id','buyer_name');
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sll_result=sql_select($sql);
			$i=1;
			foreach($sll_result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
			?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("lc_sc_year")];?>,<? echo $row[csf("buyer_name")];?>,<? echo $row[csf("lien_bank")];?>,<? echo $row[csf("id")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><? echo $row[csf("internal_file_no")];  ?></td>
                    <td align="center" width="80"><? echo $row[csf("lc_sc_year")];  ?></td>
                    <td width="130"><? echo $buyer_arr[$row[csf("buyer_name")]];  ?></td>
                    <td width="100"><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
			<input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>

        <?
}


disconnect($con);
?>
