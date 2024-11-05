<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
///require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where id in(1,2,3,4,5,6) and  status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$team_info_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
$agent_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$user_name_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),2))),'','',1);
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$prev_fifteen_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 		
$select_fill="to_char(b.update_date,'DD-MM-YYYY HH12:MI:SS')";
 

foreach($company_library as $compid=>$compname)
{
	  $buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	  $bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	  $company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	  $buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	  $company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
	  $company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
	  $imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
		
	  $sql_data="SELECT a.job_no_prefix_num, a.job_no, b.insert_date, a.company_name, a.working_company_id, a.buyer_name,a.set_smv, a.agent_name, a.style_ref_no, 
	  a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season_buyer_wise, b.id as po_id, 
	  b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date,b.unit_price, b.po_total_price, 
	  b.details_remarks, b.shiping_status,   sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date
	  from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id 
	  where  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 
	  AND  trunc(b.insert_date) between trunc(sysdate-1) and TRUNC(sysdate-1) AND a.company_name=a.working_company_id and a.company_name='$compid'
	  group by a.job_no_prefix_num, a.job_no, b.insert_date, a.company_name, a.working_company_id, a.buyer_name, a.agent_name, 
	  a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom,a.set_smv,a.team_leader, a.dealing_marchant, 
	  a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price,
	  b.details_remarks, b.shiping_status, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id";
	  $data_array=sql_select( $sql_data);
	  $data_count= count($data_array);
	  if($data_count>0)
	  {
	 // echo $sql_data.'<br><br>'; //  die;
	  $all_po_id_arr=array();
	$po_wise_arr = null;
	foreach($data_array as $row) 
	{
	  	$po_wise_arr[$row[csf('po_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$po_wise_arr[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['company_name']=$row[csf('company_name')];
		$po_wise_arr[$row[csf('po_id')]]['working_company_id']=$row[csf('working_company_id')];
		$po_wise_arr[$row[csf('po_id')]]['buyer_name']=$row[csf('buyer_name')];
		$po_wise_arr[$row[csf('po_id')]]['agent_name']=$row[csf('agent_name')];
		$po_wise_arr[$row[csf('po_id')]]['job_quantity']=$row[csf('job_quantity')];
		
		$po_wise_arr[$row[csf('po_id')]]['product_category']=$row[csf('product_category')];
		$po_wise_arr[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$po_wise_arr[$row[csf('po_id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
		$po_wise_arr[$row[csf('po_id')]]['order_uom']=$row[csf('order_uom')];
		$po_wise_arr[$row[csf('po_id')]]['team_leader']=$row[csf('team_leader')];
		$po_wise_arr[$row[csf('po_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$po_wise_arr[$row[csf('po_id')]]['season']=$row[csf('season')];
		$po_wise_arr[$row[csf('po_id')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
		$po_wise_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
		$po_wise_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];
		$po_wise_arr[$row[csf('po_id')]]['po_number']=$row[csf('po_number')];
		$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('is_confirmed')];
		//$po_wise_arr[$row[csf('po_id')]]['is_confirmed']=$row[csf('season_buyer_wise')];
		$po_wise_arr[$row[csf('po_id')]]['inserted_by']=$row[csf('inserted_by')];
		$po_wise_arr[$row[csf('po_id')]]['po_quantity']=$row[csf('po_quantity')];
		$po_wise_arr[$row[csf('po_id')]]['shipment_date']=$row[csf('shipment_date')];
		$po_wise_arr[$row[csf('po_id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
		$po_wise_arr[$row[csf('po_id')]]['po_received_date']=$row[csf('po_received_date')];
		$po_wise_arr[$row[csf('po_id')]]['unit_price']=$row[csf('unit_price')];
		$po_wise_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
		$po_wise_arr[$row[csf('po_id')]]['details_remarks']=$row[csf('details_remarks')];
		
		$po_wise_arr[$row[csf('po_id')]]['file_no']=$row[csf('file_no')];
		$po_wise_arr[$row[csf('po_id')]]['grouping']=$row[csf('grouping')];
		$po_wise_arr[$row[csf('po_id')]]['ex_factory_qnty']=$row[csf('ex_factory_qnty')];
		$po_wise_arr[$row[csf('po_id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
		$po_wise_arr[$row[csf('po_id')]]['date_diff_1']=$row[csf('date_diff_1')];
		$po_wise_arr[$row[csf('po_id')]]['date_diff_2']=$row[csf('date_diff_2')];
		$po_wise_arr[$row[csf('po_id')]]['date_diff_3']=$row[csf('date_diff_3')];
		$po_wise_arr[$row[csf('po_id')]]['date_diff_4']=$row[csf('date_diff_4')];
		$po_wise_arr[$row[csf('po_id')]]['set_smv']=$row[csf('set_smv')];
		$po_wise_arr[$row[csf('po_id')]]['year']=$row[csf('year')];
		$po_wise_arr[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		
		$all_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		//Company Buyer Wise
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];
		$pub_date_key=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
		
		//Sumary
		$month_wise_arr[$pub_date_key]=$pub_date_key;
		$summ_cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_total_price']+=$row[csf('po_total_price')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['po_quantity']+=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['smv_min']+=$row[csf('set_smv')]*($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		//echo $summ_cm_cost.'='.$row[csf('po_quantity')]*$row[csf('total_set_qnty')].',';
		$buyer_summary_mon_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$pub_date_key]['cm_value']+=$summ_cm_cost;
		$comp_buyer_wise_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('company_name')];
		
	}
	
	//unset($data_array);
	
		$poIds=implode(",", $all_po_id_arr); 
		$po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; 
		$po_ids=$all_po_id_arr;
		// print_r($all_po_id_arr);die();
			if($db_type==2 && count($all_po_id_arr)>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$po_cond_for_in3=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
					$po_cond_for_in2.=" b.id in($ids) or";
					$po_cond_for_in3.=" a.wo_po_break_down_id in($ids) or"; 
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
				$po_cond_for_in3=chop($po_cond_for_in3,'or ');
				$po_cond_for_in3.=")";
			}
			else
			{
				$po_cond_for_in=" and b.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and b.id in($poIds)";
				$po_cond_for_in3=" and a.wo_po_break_down_id in($poIds)";
			}
 
		$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id like '$company_name' $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id");

		/*echo "SELECT b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id like '$company_name' $buyer_id_cond2 $po_cond_for_in group by b.po_break_down_id";*/
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$company_name=$po_wise_arr[$row[csf('po_id')]]['company_name'];
			$buyer_name=$po_wise_arr[$row[csf('po_id')]]['buyer_name'];
			$shiping_status_id=$po_wise_arr[$row[csf('po_id')]]['shiping_status'];
			//echo $shiping_status_id.', ';
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty']=$row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('ex_factory_return_qnty')];
			
			//Buyer Wise
		//	$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			if($shiping_status_id==3)//Full shipped
			{
				//echo $row[csf('ex_factory_qnty')].'dd';
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['full_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			else if($shiping_status_id==2)//Partial shipped
			{
			$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['partial_del_qty']+=$row[csf('ex_factory_qnty')]-$row[csf('ex_factory_return_qnty')];
			}
			//$buyer_ex_factory_qty_arr[$company_name][$buyer_name]['return_qty']=$row[csf('ex_factory_return_qnty')];
		}
		
		if($db_type==0)
			{
				$fab_dec_cond="group_concat(c.fabric_description) as fabric_description";
			}
			else if($db_type==2)
			{
				$fab_dec_cond="listagg(cast(c.fabric_description as varchar2(4000)),',') within group (order by fabric_description) as fabric_description";
			}
			//echo "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no $po_cond_for_in2 ";
		//	echo  "select c.job_no,c.cm_for_sipment_sche as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ";die;
		//	$cm_for_shipment_schedule_arr=return_library_array( "select c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_dtls c,wo_po_break_down b where c.job_no=b.job_no_mst $po_cond_for_in2 ",'job_no','cm_for_sipment_sche');
		//	print_r($cm_for_shipment_schedule_arr);
		
		$sql_pre="SELECT a.costing_per,a.approved, c.job_no,c.cm_cost as cm_for_sipment_sche from  wo_pre_cost_mst a,wo_pre_cost_dtls c,wo_po_break_down b where a.job_no=b.job_no_mst and  c.job_no=b.job_no_mst  $po_cond_for_in2 ";
		 $data_budget_pre=sql_select($sql_pre);
			foreach ($data_budget_pre as $row)
			{
				$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
				$job_approved_arr[$row[csf('job_no')]]=$row[csf('approved')];
			}
		  $sql_budget="SELECT a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id,$fab_dec_cond from wo_pre_cost_mst a,wo_pre_cost_sum_dtls d,wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b where a.job_no=d.job_no and a.job_no=c.job_no and a.job_no=b.job_no_mst and d.job_no=c.job_no and c.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1  $po_cond_for_in2 $file_cond $date_cond $file_cond $ref_cond  group by a.job_no,d.yarn_cons_qnty,a.costing_per,c.item_number_id";
		   $data_budget_array=sql_select($sql_budget);
		
			$fabric_arr=array();
			foreach ($data_budget_array as $row)
			{
				$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]]=$row[csf('fabric_description')];
				if($row[csf('yarn_cons_qnty')]>0)
				{
				$job_yarn_cons_arr[$row[csf('job_no')]]['yarn_cons_qnty']=$row[csf('yarn_cons_qnty')];
			
				}
					//$job_yarn_cons_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];
				//$cm_for_shipment_schedule_arr[$row[csf('job_no')]]=$row[csf('cm_for_sipment_sche')];
			}
				//var_dump($fabric_arr);die;
				$actual_po_no_arr=array();
		if($db_type==0)
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, group_concat(b.acc_po_no) as acc_po_no from wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}
		else
		{
			$actual_po_sql=sql_select( "SELECT b.po_break_down_id, listagg(cast(b.acc_po_no as varchar(4000)),',') within group(order by b.acc_po_no) as acc_po_no from  wo_po_acc_po_info b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
		}

		foreach($actual_po_sql as $row)
		{
			$actual_po_no_arr[$row[csf('po_break_down_id')]]=$row[csf('acc_po_no')];
		}
		unset($actual_po_sql);
		//die;
		$sql_lc_result=sql_select("select a.wo_po_break_down_id, a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor ");
		$lc_po_id="";
		foreach ($sql_lc_result as $row)
		{
			$lc_id_arr[$row[csf('wo_po_break_down_id')]] = $row[csf('com_export_lc_id')];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['file_no']= $row[csf('internal_file_no')];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['pay_term']= $pay_term[$row[csf('pay_term')]];
			$export_lc_arr[$row[csf('wo_po_break_down_id')]]['tenor']= $row[csf('tenor')];
			
				if($lc_po_id=="") $lc_po_id=$row[csf('com_export_lc_id')];else $lc_po_id.=",".$row[csf('com_export_lc_id')];
		}
		unset($sql_lc_result);
		$sql_sc_result=sql_select("select a.wo_po_break_down_id, b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank  from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id,b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank ");
		foreach ($sql_sc_result as $row)
		{
			$sc_number_arr[$row[csf('wo_po_break_down_id')]].= $row[csf('contract_no')].',';
			$sc_bank_arr[$row[csf('wo_po_break_down_id')]].= $row[csf('lien_bank')].',';
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['file_no']= $row[csf('internal_file_no')];
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['pay_term']= $pay_term[$row[csf('pay_term')]];
			$export_sc_arr[$row[csf('wo_po_break_down_id')]]['tenor']= $row[csf('tenor')];
		}
		unset($sql_sc_result);
						
		if($db_type==0)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, group_concat(b.lien_bank) as lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		if($db_type==2)
		{
			$lc_number_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','export_lc_no');
			$lc_bank_arr=return_library_array( "select a.wo_po_break_down_id, LISTAGG(b.lien_bank,',') WITHIN GROUP (ORDER BY b.lien_bank)  lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in3 group by a.wo_po_break_down_id ",'wo_po_break_down_id','lien_bank');
		}
		$lcIds=chop($lc_po_id,','); $lc_cond_for_in=""; 
		$lc_ids=count(array_unique(explode(",",$lc_po_id)));
			if($db_type==2 && $lc_ids>1000)
			{
				$lc_cond_for_in=" and (";
				$lcIdsArr=array_chunk(explode(",",$lcIds),999);
				foreach($lcIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$lc_cond_for_in.=" export_lc_id in($ids) or"; 
				}
				$lc_cond_for_in=chop($lc_cond_for_in,'or ');
				$lc_cond_for_in.=")";
			}
			else
			{
				$lc_cond_for_in=" and export_lc_id in($lcIds)";
			}
		
		$lc_amendment_arr= array();
		$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 $lc_cond_for_in");
	
		foreach($last_amendment_arr as $data)
		{
			$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
		}
		
		
		
		$cut_qty_sql_res = sql_select("SELECT JOB_NO_MST,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID,COUNTRY_SHIP_DATE,PLAN_CUT_QNTY  FROM WO_PO_COLOR_SIZE_BREAKDOWN where status_active=1 and is_deleted=0 ".where_con_using_array($all_po_id_arr,0,'PO_BREAK_DOWN_ID')."");
		$cut_qty_arr=array();
		foreach($cut_qty_sql_res as $rows)
		{
			
			$key=date("M-Y",strtotime($rows[COUNTRY_SHIP_DATE]));
			$comapny_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['company_name'];
			$buyer_id=$po_wise_arr[$rows[PO_BREAK_DOWN_ID]]['buyer_name'];
			$cut_qty_by_month_arr[$comapny_id][$buyer_id][$key] += $rows[PLAN_CUT_QNTY];
			$cut_qty_dtls_arr[$rows[JOB_NO_MST]][$rows[PO_BREAK_DOWN_ID]] += $rows[PLAN_CUT_QNTY];
		}

	ob_start();	
	?>
			<table width="6380" id="table_header_1" border="1" class="rpt_table" rules="all">
				<tr>
					<td colspan="50" >New Order List of <? echo $compname;?> </td>
				</tr>
			</table>
			<table width="6380" id="table_header_1" border="1" class="rpt_table" rules="all">
				<thead>
					<tr bgcolor="#CCCCCC">
						<th width="50">SL</th>
						<th width="70" >Company</th>
						<th width="100" >WO Company</th>
						<th width="70">Job No</th>
						
                        <th width="70">Approve Status</th>
						<th width="50">Buyer</th>
						<th width="110">PO No</th>
                        <th width="100">Emblishment</th>
                        
						<th width="100">Season</th>
						<th width="70">Order Status</th>
						<th width="70">Prod. Catg</th>
						<th width="40">Img</th>
                        <th width="40">File</th>
						<th width="90">Style Ref</th>
						<th width="150">Item</th>
						<th width="200">Fab. Description</th>
						<th width="70">Ship Date</th>
						<th width="70">PO Rec. Date</th>
						<th width="100">Inhouse Date</th>
						<th width="50">Days in Hand</th>
						<th width="90">Order Qty(Pcs)</th>
						<th width="90">PO Breakdown Qty(pcs)</th>
						<th width="90">Order Qty</th>
						<th width="40">Uom</th>
						<th width="50">Per Unit Price</th>
						<th width="100">Order Value</th>
                        <th width="100">Lien Bank</th>
						<th width="100">LC/SC No</th>
						<th width="90">Ex. LC Amendment No(Last)</th>
						<th width="80"> Int.File No </th>
						<th width="80">Pay Term </th>
						<th width="80">Tenor </th>
						<th width="90">Ex-Fac Qnty </th>
						<th width="70">Last Ex-Fac Date</th>
						<th width="90">Short/Access Qnty</th>
						<th width="120">Short/Access Value</th>
						<th width="100">Yarn Req</th>
						<th width="100">CM </th>
						<th width="100">CM(Pcs)</th>
						<th width="100">SMV </th>
						<th width="100" >Shipping Status</th>
						<th width="150"> Team Member</th>
						<th width="150">Team Name</th>
						<th width="40">Id</th>
						<th width="100">Remarks</th>
						<th width="100">User Name</th>
					</tr>
				</thead>
						<tbody>
							<?
							$sql_emblishment = "SELECT    a.job_no,    a.gmts_item_id,    a.embelishment,    a.embro,    a.wash,    a.gmtsdying FROM     wo_po_details_mas_set_details a ";
							$sql_emblishment_result = sql_select($sql_emblishment);
							
							foreach($sql_emblishment_result as $rowss)
							{
													
								if($rowss[csf('embelishment')]==1)
								{
									$data_arr_emb[$rowss[csf('job_no')]]['embelishment']= $emblishment_name_array[$rowss[csf('embelishment')]];
								}
								else if ($rowss[csf('embro')]==1)
								{
									$data_arr_emb[$rowss[csf('job_no')]]['embro']= $emblishment_name_array[$rowss[csf('embro')]];					
								}
								else if ($rowss[csf('wash')]==1)
								{
									$data_arr_emb[$rowss[csf('job_no')]]['wash']= $emblishment_name_array[$rowss[csf('wash')]];					
								}
								elseif ($rowss[csf('gmtsdying')]==1)
								{
									$data_arr_emb[$rowss[csf('job_no')]]['gmtsdying']= $emblishment_name_array[$rowss[csf('gmtsdying')]];					
								}	
								else
								{
									$data_arr_emb[$rowss[csf('job_no')]]['N/A']= "N/A";
								}
							}
							//echo '<pre>'; print_r($data_arr_emb);die;

							$i=1; $order_qnty_pcs_tot=0; $order_qntytot=0; $oreder_value_tot=0; $total_ex_factory_qnty=0; $total_short_access_qnty=0; $total_short_access_value=0; $yarn_req_for_po_total=0;
							
							
							
								$gorder_qnty_pcs_tot=0; $gorder_qntytot=0; $goreder_value_tot=0; $gtotal_ex_factory_qnty=0; $gtotal_short_access_qnty=0; $gtotal_short_access_value=0; $gyarn_req_for_po_total=0;

								if($db_type==0)
								{
								//DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2,DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4
								//	$data_array=sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, b.id,b.inserted_by, b.is_confirmed, b.po_number, b.file_no, b.grouping, b.po_quantity, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='".$row_group[csf('grouping')]."' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond  $ref_cond $season_cond  group by b.id, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
								}
								if($db_type==2)
								{
									$date=date('d-m-Y');
									if($row_group[csf('grouping')]!="")
									{
										$grouping="and b.grouping='".$row_group[csf('grouping')]."'";
									}
									if($row_group[csf('grouping')]=="")
									{
										$grouping="and b.grouping IS NULL";
									}

									// (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2,(b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4
									/* $data_array=sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4 from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst and a.company_name like '$company_name'  $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond  $ref_cond $season_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.season_buyer_wise, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id");*/

								}
								$data_file=sql_select("select image_location, master_tble_id from common_photo_library where   form_name='knit_order_entry' and is_deleted=0 and file_type=2");
								$system_file_arr=array();
								foreach($data_file as $row)
								{
								$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
								}
								unset($data_file);
								
								foreach ($po_wise_arr as $po_id=>$row)
								{
							//echo $lc_id_arr[$row[csf('id')]];
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$cons=0;
							$costing_per_pcs=0;
							$yarn_cons_qnty=$job_yarn_cons_arr[$row[('job_no')]]['yarn_cons_qnty'];
							$costing_per=$job_yarn_cons_arr[$row[('job_no')]]['costing_per'];
							//echo $costing_per.'='.$yarn_cons_qnty.',';
							if($costing_per==1) $costing_per_pcs=1*12;
							else if($costing_per==2) $costing_per_pcs=1*1;
							else if($costing_per==3) $costing_per_pcs=2*12;
							else if($costing_per==4) $costing_per_pcs=3*12;
							else if($costing_per==5) $costing_per_pcs=4*12;

								$cons=$yarn_cons_qnty;
								$yarn_req_for_po=($yarn_cons_qnty/ $costing_per_pcs)*$row[('po_quantity')];
							//--Calculation Yarn Required-------
							//--Color Determination-------------
							//==================================
							$shipment_performance=0;
							if($row[('shiping_status')]==1 && $row[('date_diff_1')]>10 )
							{
								$color="";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}

							if($row[('shiping_status')]==1 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
							{
								$color="orange";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
							if($row[('shiping_status')]==1 &&  $row[('date_diff_1')]<0)
							{
								$color="red";
								$number_of_order['yet']+=1;
								$shipment_performance=0;
							}
									//=====================================
							if($row[('shiping_status')]==2 && $row[('date_diff_1')]>10 )
							{
								$color="";
							}
							if($row[('shiping_status')]==2 && ($row[('date_diff_1')]<=10 && $row[('date_diff_1')]>=0))
							{
								$color="orange";
							}
							if($row[('shiping_status')]==2 &&  $row[('date_diff_1')]<0)
							{
								$color="red";
							}
							if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]>=0)
							{
								$number_of_order['ontime']+=1;
								$shipment_performance=1;
							}
							if($row[('shiping_status')]==2 &&  $row[('date_diff_2')]<0)
							{
								$number_of_order['after']+=1;
								$shipment_performance=2;
							}
							//========================================
							if($row[('shiping_status')]==3 && $row[('date_diff_3')]>=0 )
							{
								$color="green";
							}
							if($row[('shiping_status')]==3 &&  $row[('date_diff_3')]<0)
							{
								$color="#2A9FFF";
							}
							if($row[('shiping_status')]==3 && $row[('date_diff_4')]>=0 )
							{
								$number_of_order['ontime']+=1;
								$shipment_performance=1;
							}
							if($row[('shiping_status')]==3 &&  $row[('date_diff_4')]<0)
							{
								$number_of_order['after']+=1;
								$shipment_performance=2;
							}
							$approved_id=$job_approved_arr[$row['job_no']];
							//echo  $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;
							if($approved_id==1)
							{
								$msg_app="Approved";
								$color_app_td="#00FF66";//Blue
							}
							else if($approved_id==3)
							{
								$msg_app="Approved";
								$color_app_td="#FF0000";//Red
							}
							else
							{
								$msg_app="UnApproved"; //Red
								$color_app_td="#FF0000";//Red
							}
							$job_id = $row[('job_no')];
							$item_id = $row[('gmts_item_id')];
							//echo $file_type_name.'DDDDD,';
							?>
							<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td   bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
								<td ><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('company_name')]];?></div></td>
								<td  ><div style="word-wrap:break-word; width:70px"><? echo $company_short_name_arr[$row[('working_company_id')]];?></div></td>
								<td  ><p><? echo $row[('job_no')]; ?></p></td>
								<td   bgcolor="<? echo $color_app_td;?>"><p><? echo $msg_app; ?></p></td>
								<td  ><div style="word-wrap:break-word; width:50px"><? echo $buyer_short_name_arr[$row[('buyer_name')]];?></div></td>
								<td  ><div style="word-wrap:break-word; width:110px"><? echo $row[('po_number')];?></div></td>
								<td  ><div style="word-wrap:break-word; width:100px">
									<? 									
										//echo $data_arr_emb[$job_id][$item_id]['embelishment'].", ".$data_arr_emb[$job_id][$item_id]['embro'].", ".$data_arr_emb[$job_id][$item_id]['wash'].", ".$data_arr_emb[$job_id][$item_id]['gmtsdying'];
										echo $data_arr_emb[$job_id]['embelishment'].", ".$data_arr_emb[$job_id]['embro'].", ".$data_arr_emb[$job_id]['wash'].", ".$data_arr_emb[$job_id]['gmtsdying'];
									?>
								</div></td>
								<td  ><div style="word-wrap:break-word; width:100px"><? echo $buyer_wise_season_arr[$row[('season_buyer_wise')]];?></div></td>
								<td  ><div style="word-wrap:break-word; width:70px"><? echo $order_status[$row[('is_confirmed')]];?></div></td>
								<td  ><div style="word-wrap:break-word; width:70px"><? echo $product_category[$row[('product_category')]];?></div></td>
								<td   onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[("job_no")] ?>','Image View')"><img  src='../../../<? echo $imge_arr[$row[('job_no')]]; ?>' height='25' width='30' /></td>
								<td  >
								 <? 
								 $file_type_name=$system_file_arr[$row[('job_no')]]['file'];
								 if($file_type_name!="")
									{
								 ?>
								 <input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_image('requires/shipment_schedule_controller.php?action=show_file&job_no=<? echo $row[("job_no")] ?>','File View'),2"/>
								 <?
								   }
								  else echo " ";
								 ?>
								</td>
								<td  ><div style="word-wrap:break-word; width:90px"><? echo $row[('style_ref_no')];?></div></td>
								<td  ><div style="word-wrap:break-word; width:150px">
								<? $gmts_item_id=explode(',',$row[('gmts_item_id')]);
									$fabric_description="";
									for($j=0; $j<=count($gmts_item_id); $j++)
									{
										if($fabric_description=="") $fabric_description=$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]]; else $fabric_description.=','.$fabric_arr[$row[('job_no')]][$gmts_item_id[$j]];
										echo $garments_item[$gmts_item_id[$j]];
									}
									?></div></td>
								<td  ><div style="word-wrap:break-word; width:200px">
									<?
									$fabric_des="";
									$fabric_des=implode(",",array_unique(explode(",",$fabric_description)));
									echo $fabric_des;//$fabric_des;?></div></td>
								<td  ><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('pub_shipment_date')],'dd-mm-yyyy','-');?></div></td>
								<td  ><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('po_received_date')],'dd-mm-yyyy','-');?></div></td>
								<td  >
									<div style="word-wrap:break-word; width:100px">
										<?
										$ship=$row[('pub_shipment_date')];
										$po_rcv_date=$row[('po_received_date')];
										$dt = new DateTime($ship);
										$date = $dt->format('m/d/Y'); $date1=date_create($date);	$date2=date_create($po_rcv_date);
										
										$diff=date_diff($date2,$date1); 	$print=$diff->format("%R%a");	$days_diff =  substr($print, 1);
													
										if($days_diff>120){$day=40;}else if($days_diff>90){$day=40;}else if($days_diff>75){$day=35;}else if($days_diff>60){$day=30;}else if($days_diff>45){$day=22;}else if($days_diff>30){$day=17;}else{$day=0;}			
										$dt3 = new DateTime($ship);
										$date_ship = $dt3->format('Y-m-d');
										$date3=date_create($date_ship);
										date_sub($date3,date_interval_create_from_date_string("$day days"));
										$m_inhouse_date = date_format($date3,"d-m-Y");
										echo  $m_inhouse_date;	
										?>
									</div>
								</td>
								<td   bgcolor="<? echo $color; ?>"><div style="word-wrap:break-word; width:50px">
									<?
									if($row[('shiping_status')]==1 || $row[('shiping_status')]==2)
									{
										echo $row[('date_diff_1')];
									}
									if($row[('shiping_status')]==3)
									{
										echo $row[('date_diff_3')];
									}
									?></div></td>
								<td   align="right"><p>
									<?
									echo number_format(($row[('po_quantity')]*$row[('total_set_qnty')]),0);
									$order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
									$gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[('po_quantity')]*$row[('total_set_qnty')]);
									?></p></td>
								<td   align="right"><?= $cut_qty_dtls_arr[$row[('job_no')]][$po_id];?></td>
								<td   align="right"><p>
									<?
									echo number_format( $row[('po_quantity')],0);
									$order_qntytot=$order_qntytot+$row[('po_quantity')];
									$gorder_qntytot=$gorder_qntytot+$row[('po_quantity')];
									
									$total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
									$grand_total_cut_qty_dtls+=$cut_qty_dtls_arr[$row[('job_no')]][$po_id];
									
									
									?></p></td>
								<td  ><p><? echo $unit_of_measurement[$row[('order_uom')]];?></p></td>
								<td   align="right"><p><? echo number_format($row[('unit_price')],2);?></p></td>
								<td   align="right"><p>
									<?
										echo number_format($row[('po_total_price')],2);
										$oreder_value_tot=$oreder_value_tot+$row[('po_total_price')];
										$goreder_value_tot=$goreder_value_tot+$row[('po_total_price')];
									?></p></td>
								<td   align="center"><div style="word-wrap:break-word; width:100px">
									<?
									unset($bank_id_arr);
									unset($bank_string_arr);
									if($lc_bank_arr[$po_id] !="")
									{
										$bank_id_arr=array_unique(explode(",",$lc_bank_arr[$po_id]));
										foreach($bank_id_arr as $bank_id)
										{
											$bank_string_arr[]=$bank_name_arr[$bank_id];
										}
										echo implode(",",$bank_string_arr);
									}
									$sc_bank=rtrim($sc_bank_arr[$po_id],',');
									if($sc_bank !="")
									{
										$bank_id_arr=array_unique(explode(",",$sc_bank));
										foreach($bank_id_arr as $bank_id)
										{
											$bank_string_arr[]=$bank_name_arr[$bank_id];
										}
										echo implode(",",$bank_string_arr);
									}
									?>

								</div>
								<td   align="center"><div style="word-wrap:break-word; width:100px">
									<?
									if($lc_number_arr[$po_id] !="")
									{
										echo "LC: ". $lc_number_arr[$po_id];
										$lc_no = $lc_number_arr[$po_id];
									}
									$sc_number=rtrim($sc_number_arr[$po_id],',');
									$sc_numbers=implode(",",array_unique(explode(",",$sc_number)));
									if($sc_numbers !="")
									{
										echo " SC: ".$sc_numbers;
									}
									?>
									</div></td>
								<td   align="center"><div style="word-wrap:break-word; width:90px">
									<? if($lc_number_arr[$po_id] !="")
										{
											 echo $lc_amendment_arr[$lc_id_arr[$po_id]];

										}
									?>
								</div></td>
								<td   align="center"><p>
								<?
								if($export_lc_arr[$po_id]['file_no']!='') echo $export_lc_arr[$po_id]['file_no'];
								if($export_sc_arr[$po_id]['file_no']!='') echo $export_sc_arr[$po_id]['file_no'];

								?>

								</p></td>
								<td   align="center"><p><?

								if($export_lc_arr[$po_id]['pay_term']!="") echo $export_lc_arr[$po_id]['pay_term'];
								if($export_sc_arr[$po_id]['pay_term']!="") echo $export_sc_arr[$po_id]['pay_term'];

								 ?></p></td>
								<td   align="center"><p><?

								if($export_lc_arr[$po_id]['tenor']!="" ) echo $export_lc_arr[$po_id]['tenor'];
								if($export_sc_arr[$po_id]['tenor']!="" ) echo $export_sc_arr[$po_id]['tenor'];

								 ?></p></td>

								<td   align="right"><p>
								<?
									$ex_factory_del_qty=$ex_factory_qty_arr[$po_id]['del_qty'];
									$ex_factory_return_qty=$ex_factory_qty_arr[$po_id]['return_qty'];
									$ex_factory_qnty=$ex_factory_del_qty-$ex_factory_return_qty;

									//$ex_factory_qnty=$ex_factory_qty_arr[$row[csf("id")]];
									?>
									<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo  number_format( $ex_factory_qnty,0); ?></div></a>
									<?

									$total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
									$gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
									if ($shipment_performance==0)
									{
										$po_qnty['yet']+=($row[('po_quantity')]*$row[('total_set_qnty')]);
										$po_value['yet']+=100;
									}
									else if ($shipment_performance==1)
									{
										$po_qnty['ontime']+=$ex_factory_qnty;
										$po_value['ontime']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
										$po_qnty['yet']+=(($row[csf('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									}
									else if ($shipment_performance==2)
									{
										$po_qnty['after']+=$ex_factory_qnty;
										$po_value['after']+=((100*$ex_factory_qnty)/($row[('po_quantity')]*$row[('total_set_qnty')]));
										$po_qnty['yet']+=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
									}
									?></p></td>
								<td  ><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[('job_no')];?>', '<? echo $po_id; ?>','750px')"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[('ex_factory_date')]); ?></div></a></td>
								<td    align="right"><p>
									<?
										$short_access_qnty=(($row[('po_quantity')]*$row[('total_set_qnty')])-$ex_factory_qnty);
										echo number_format($short_access_qnty,0);
										$total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
										$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;;
									?></p>
								</td>
								<td   align="right"><p>
									<?
										$short_access_value=$short_access_qnty*$row[('unit_price')];
										echo number_format($short_access_value,2);
										$total_short_access_value=$total_short_access_value+$short_access_value;
										$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
									?></p>
								</td>
								<td   align="right" title="<? echo "Cons:".$cons."Costing per:".$costing_per[('costing_per')];?>"><p>
									<a href='##' onClick="openmypage_yarn_req('<? echo $row[('job_no')]; ?>', 'yarn_req_popup')">
									<?
										echo number_format($yarn_req_for_po,2);
										$yarn_req_for_po_total=$yarn_req_for_po_total+$yarn_req_for_po;
										$gyarn_req_for_po_total=$gyarn_req_for_po_total+$yarn_req_for_po;
									?></p></a>
								</td>
								<td   align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>">
									<p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs)*$row[('po_quantity')],2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
								<td   align="right" title="<? echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs.'='.$row[('po_quantity')];?>">
									<p><? echo number_format(($cm_for_shipment_schedule_arr[$row[('job_no')]]/$costing_per_pcs),2); //echo $cm_for_shipment_schedule_arr[$row[('job_no')]].'='.$costing_per_pcs;?></p></td>
								<?
									
									if($row[('order_uom')]==58){									
										?>
								<td   align="right"><a href="##" onClick="smv_popup('smv_set_details','<? echo $row[('job_no')];?>', '<? echo $row[('id')]; ?>','500px')"><?echo number_format($row[('set_smv')],2);?></a></td>
								<?}else{?>
									<td   align="right"><p><?echo number_format($row[('set_smv')],2);?></p></td>	<?	}?>
								<td   align="center"><div style="word-wrap:break-word; width:100px"><? echo $shipment_status[$row[('shiping_status')]]; ?></div></td>
								<td   align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_member_name_arr[$row[('dealing_marchant')]];?></div></td>
								<td   align="center"><div style="word-wrap:break-word; width:150px"><? echo $company_team_name_arr[$row[('team_leader')]];?></div></td>
								
								<td  ><p><? echo $po_id; ?></p></td>
								<td  ><p><? echo $row[('details_remarks')]; ?></p></td>
								<td  ><p><? echo $user_name_arr[$row[('inserted_by')]]; ?></p></td>
							</tr>
							<?
							$i++;
						}
						?>
						</tbody>
						<tfoot>
							<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
							<td   align="center" >  Total: </td>							
							<td  ></td>							
						
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot,0); ?></td>							
							<td   align="right"><?=$total_cut_qty_dtls;?></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot,0); ?></td>
							<td  ></td>
							<td  ></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot,2); ?></td>
							<td  ></td>					
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty,0); ?></td>
							<td  ></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty,0); ?></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value,0); ?></td>
							<td   align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total,2); ?></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td   ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							<td  ></td>
							
							<td  ></td>
						 </tr>
						</tfoot>
				</table>		
	<?		
	
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=8 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		//if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
	$subject = "Daily New Order List of ".$company_arr[$compid];
	$mail_body = "Please see the attached file for Daily New Order List  of ".$company_arr[$compid];
	
	$message="";	
	$header=mailHeader();
	$message=ob_get_contents();
	
	ob_clean();			
	$att_file_arr=array();
	$filename="New_Order_List_".$company_arr[$compid].".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$message);
	$att_file_arr[]=$filename.'**'.$filename;
		
	//$to=$to.", ".'al-amin@team.com.bd, joy@team.com.bd, raihan.uddin@team.com.bd';
	$to='al-amin@team.com.bd';
	
	if($compid==1)
	{
		$to=$to.", ".'raihan.uddin@team.com.bd, minhajul.arefin@gramtechknit.com, ie.shahadat@gramtechknit.com, rasel@gramtechknit.com, uzzal.dakua@gramtechknit.com, noman.rejwan@gramtechknit.com, mizan.rahman@gramtechknit.com, shahriar@gramtechknit.com, tipu@team.com.bd, rupon@gramtechknit.com, azizul.haq@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==2){
		$to=$to.", ".'raihan.uddin@team.com.bd, ibrahim@team.com.bd, azmal.huda@team.com.bd, mainul.islam@team.com.bd, tuhin.Rasul@team.com.bd, shah.alam@marsstitchltd.com, ibrahim@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==3){
		$to=$to.", ".'raihan.uddin@team.com.bd, pavel@brothersfashion-bd.com, emdad@brothersfashion-bd.com, tuhin.Rasul@team.com.bd, bfl_scm@brothersfashion-bd.com, abir@brothersfashion-bd.com, mahamudul.hassan@brothersfashion-bd.com, tanveer.hasan@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
		//echo $message;
	}
	elseif($compid==4){
		$to=$to.", ".'raihan.uddin@team.com.bd, sohel@4ajacket.com, zillur.frp@4ajacket.com, ashraful@4ajacket.com, sajib@team.com.bd, zahedul@4ajacket.com, asad@4ajacket.com,kabir@4ajacket.com,masum@4ajacket.com,robin@4ajacket.com,sohel@4ajacket.com,tangib@4ajacket.com ,enamul@4ajacket.com,hasibul@4ajacket.com,
zonayet@4ajacket.com,s.mahamud@4ajacket.com,shaon@4ajacket.com,monir@4ajacket.com,sujon@4ajacket.com,mahfuz@4ajacket.com,sheikh.sohelrana@4ajacket.com, pranaya.shovon@4ajacket.com,
abu.jubair@4ajacket.com,aworongo.shahriar@4ajacket.com,rohan.hossain@4ajacket.com,neamot.hossen@4ajacket.com,pranaya.shovon@4ajacket.com,mizanur.rahman@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,anwar.hossain@4ajacket.com,abdur.rahim@4ajacket.com,store3@4ajacket.com,dider@4ajacket.com,
hafizur.rahman@team.com.bd,sajib@team.com.bd,badsha@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,hafizur.rahman@team.com.bd,enamul@4ajacket.com,abdur.rahim@4ajacket.com,store3@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,shandhi.rozario@4ajacket.com,anwar.hossain@4ajacket.com';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==5){
		$to=$to.", ".'raihan.uddin@team.com.bd, anwar@cbm-international.com, amir@cbm-international.com, nazmul@cbm-international.com, tuhin.Rasul@team.com.bd, joy@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	elseif($compid==6){
		$to=$to.", ".'md.alimujjaman@team.com.bd, tuhin.Rasul@team.com.bd, khairul@southendsweater.com, shakawat@southendsweater.com, hasan@southendsweater.com, shibly@southendsweater.com, abdullah.numan@southendsweater.com, md.musa@southendsweater.com, enam@southendsweater.com, md.alimujjaman@team.com.bd, rakib.hasan@southendsweater.com, shohel.ie@southendsweater.com';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
	else{
		$to=$to.", ".'al-amin@team.com.bd, tanveer.hasan@team.com.bd';
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	}
 	
		
	//if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail, $att_file_arr );}
	/*
	if($compid==3)
	{
		//$to=$to.", ".'al-amin@team.com.bd, tanveer.hasan@team.com.bd, joy@team.com.bd, sajib@team.com.bd';
		$to=$to.", ".'al-amin@team.com.bd, sajib@team.com.bd';
		//echo $message;
		if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
	}
	else{
			//$to=$to.", ".'al-amin@team.com.bd';
		}
	*/
	
	 //$message=ob_get_contents();
	 //echo $message;
	 unset($data_array);
 
	}
}


?> 