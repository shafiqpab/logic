<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");


$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),2))),'','',1);
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 

	  
//$prev_date="1-Jan-2016";	
//$company_library=array(1=>$company_library[1]);

$flag=0;
foreach($company_library as $compid=>$compname)
{
ob_start();	
	
	$composition=array();
	$date_from = str_replace("'", "", trim($prev_date));
	$date_to = str_replace("'", "", trim($current_date));
	
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$booking_date = "and b.UPDATE_DATE between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$booking_date = "and b.UPDATE_DATE between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

/*	$cbo_booking_type_short = explode("_",str_replace("'", "", trim($cbo_booking_type)));
	$cbo_booking_type = $cbo_booking_type_short[0];
	$is_short = $cbo_booking_type_short[1];

	if($cbo_booking_type>0)
	{
		$booking_type_cond = "and a.booking_type=$cbo_booking_type and a.is_short=$is_short";
	}else{
		$booking_type_cond = "";
	}
*/		

		//$booking_type_cond = "and a.booking_no='HGL-Fb-19-01144'";
		
		if ($db_type == 0) {

			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$compid and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_cond $booking_date $booking_type_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'
		} else {

			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$compid and a.item_category=2 and a.fabric_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 $booking_cond $booking_date $booking_type_cond group by a.id, a.company_id, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, c.style_ref_no order by b.dia_width,a.booking_no";

		}
	
	
		    //echo $sql ;die;
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) {
		$pre_cost_id_arr[] = $row[csf("pre_cost_fabric_cost_dtls_id")];
		$po_id_arr[] = $row[csf("po_break_down_id")];
		$booking_no_arr[] = $row[csf("booking_no")];
	}

	$all_booking_nos="'".implode("','", array_filter(array_unique($booking_no_arr)))."'";
	$bookId = $all_booking_no_cond = "";
	$all_booking_arr=explode(",",$all_booking_nos);
	if($db_type==2 && count($all_booking_arr)>999)
	{
		$all_booking_chunk=array_chunk($all_booking_arr,999) ;
		foreach($all_booking_chunk as $chunk_arr)
		{
			$bookId.=" booking_no in(".implode(",",$chunk_arr).") or ";
		}
		$all_booking_no_cond.=" and (".chop($bookId,'or ').")";
	}
	else
	{
		$all_booking_no_cond=" and booking_no in($all_booking_nos)";
	}


	$pre_cost_array = array();
	if(!empty($pre_cost_id_arr)){
		$pre_cost_cond = " and id in(".implode(",", array_unique($pre_cost_id_arr)).")";
		if ($db_type == 0) {
			$costing_sql = sql_select("SELECT id, body_part_id, color_type_id, width_dia_type, gsm_weight, concat_ws(', ',construction,composition) as fab_desc, lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where status_active=1 $pre_cost_cond");
		} else {
			$costing_sql = sql_select("SELECT id, body_part_id, color_type_id, width_dia_type, gsm_weight, construction || ',' || composition as fab_desc, lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where status_active=1 $pre_cost_cond");
		}

		foreach ($costing_sql as $row) {
			$costing_per_id_library[$row[csf('id')]]['body_part'] = $row[csf('body_part_id')];
			$costing_per_id_library[$row[csf('id')]]['color_type'] = $row[csf('color_type_id')];
			$costing_per_id_library[$row[csf('id')]]['width_dia_type'] = $row[csf('width_dia_type')];
			$costing_per_id_library[$row[csf('id')]]['gsm'] = $row[csf('gsm_weight')];
			$costing_per_id_library[$row[csf('id')]]['desc'] = $row[csf('fab_desc')];
			$costing_per_id_library[$row[csf('id')]]['determination_id'] = $row[csf('lib_yarn_count_deter_id')];
		}
	}

	$tna_array = array();
	if(!empty($pre_cost_id_arr)){
		$po_cond = " and po_number_id in(".implode(",",array_unique($po_id_arr)).")";
		$tna_sql = sql_select("SELECT id, po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=60 and is_deleted=0 and status_active=1 $po_cond");
		foreach ($tna_sql as $row) {
			$tna_array[$row[csf('po_number_id')]]['start_d'] = $row[csf('task_start_date')];
			$tna_array[$row[csf('po_number_id')]]['finish_d'] = $row[csf('task_finish_date')];
		}
	}
	
	$yarn_desc_array = array();
	$prod_sql = "SELECT id, lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type from product_details_master where item_category_id=1 and status_active=1";
	$result = sql_select($prod_sql);
	foreach ($result as $row) {
		$compostion = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$yarn_desc = $row[csf('lot')] . " " . $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion . " " . $yarn_type[$row[csf('yarn_type')]];
		$yarn_desc_array[$row[csf('id')]] = $yarn_desc;
	}

	$booking_item_array = array();
	if ($db_type == 0) {
		$booking_item_array = return_library_array("SELECT a.booking_no, group_concat(distinct(b.item_id)) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_cond group by a.booking_no", 'booking_no', 'prod_id');
	} else {
		$booking_item_array = return_library_array("SELECT a.booking_no, LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_cond group by a.booking_no", 'booking_no', 'prod_id');
	}

	$program_data_array = array();
	$booking_program_arr = array();
	if ($db_type == 0) {
		$sql_plan = "SELECT mst_id, booking_no, po_id, yarn_desc as pre_cost_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, group_concat(distinct(dtls_id)) as prog_no, sum(program_qnty) as program_qnty, min(id) as id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1  $all_booking_no_cond group by mst_id, booking_no, po_id, yarn_desc, body_part_id, fabric_desc, gsm_weight, dia, color_type_id";
	} else {
		$sql_plan = "SELECT mst_id, booking_no, po_id, yarn_desc as pre_cost_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as prog_no, sum(program_qnty) as program_qnty, min(id) as id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1  $all_booking_no_cond group by mst_id, booking_no, po_id, yarn_desc, body_part_id, fabric_desc, gsm_weight, dia, color_type_id";//, yarn_desc
	}
	
	//echo $all_booking_no_cond;die;
	  //echo $sql_plan;die;
	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan) {
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['mst_id'] = $rowPlan[csf('mst_id')];
		
		/*$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['prog_no'] .= ",". $rowPlan[csf('prog_no')];*/
		
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['prog_no'][$rowPlan[csf('prog_no')]]=$rowPlan[csf('prog_no')];
		
		
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['program_qnty'] = $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['id'] = $rowPlan[csf('id')];

		$booking_program_arr[$rowPlan[csf('booking_no')]] .= $rowPlan[csf('prog_no')] . ",";
	}

	//Print Button Permission
	$print_report = return_field_value("format_id", "lib_report_template", "template_name=" . $compid . "  and module_id=2 and report_id in(1) and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report);
	$print_report2 = return_field_value("format_id", "lib_report_template", "template_name=" . $compid . "  and module_id=2 and report_id in(2) and is_deleted=0 and status_active=1");
	$format_ids2 = explode(",", $print_report2);
	$print_report3 = return_field_value("format_id", "lib_report_template", "template_name=" . $compid . "  and module_id=2 and report_id in(3) and is_deleted=0 and status_active=1");
	$format_ids3 = explode(",", $print_report3);
	if (str_replace("'", "", $txt_booking_date) == "") $booking_date = ""; else $booking_date = " and a.booking_date>=" . $txt_booking_date . "";

		
		$knit_qnty_array = return_library_array("SELECT a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 group by a.booking_id", "booking_id", "knitting_qnty");

		$found_prog_no = '';
		$booking_no = '';
		$not_found_prog_array = array();
		$bookingType = array();

		foreach ($nameArray as $row) {
			$plan_id = '';
			$gsm = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['gsm'];
			$dia = $row[csf('dia_width')];
			$desc = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['desc'];
			$determination_id = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['determination_id'];
			$color_type_id = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type'];

			$update_id = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['id'];
			$program_qnty = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['program_qnty'];
			$plan_id = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['mst_id'];
			$prog_no = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['prog_no'];

			$found_prog_no[]= implode(',',$prog_no);


			$booking_no = $row[csf('booking_no')];
			$bookingType[$row[csf('booking_no')]][1] = $row[csf('booking_type')];
			$bookingType[$row[csf('booking_no')]][2] = $row[csf('is_short')];
			$bookingType[$row[csf('booking_no')]][3] = $row[csf('item_category')];
			$bookingType[$row[csf('booking_no')]][4] = $row[csf('fabric_source')];
			$bookingType[$row[csf('booking_no')]][5] = $row[csf('is_approved')];
			
			$booking_program_no_arr[] = substr($booking_program_arr[$booking_no], 0, -1);
		}
		$booking_program_no=array_unique(explode(",", implode(',',$booking_program_no_arr)));
		
		$not_found_prog_array = array_diff($booking_program_no, $found_prog_no);
		
		 //print_r($found_prog_no);die;
		
		if (count($not_found_prog_array) > 0) {
			
			
			if($db_type==2 && count($not_found_prog_array)>999)
			{
				$all_booking_chunk=array_chunk($not_found_prog_array,999) ;
				foreach($all_booking_chunk as $chunk_arr)
				{
					$bookId.=" dtls_id in(".implode(",",$chunk_arr).") or ";
					$bookId2.=" b.id in(".implode(",",$chunk_arr).") or ";
				}
		
				$dtls_id_cond=" and (".chop($bookId,'or ').")";
				$dtls_id_cond2=" and (".chop($bookId2,'or ').")";
		
			}
			else
			{
		
				$dtls_id_cond=" and dtls_id in(".implode(",",$not_found_prog_array).")";
				$dtls_id_cond2=" and b.id in(".implode(",",$not_found_prog_array).")";
			}
			
			
			
			
			if ($db_type == 0) {
				$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where is_sales!=1 $dtls_id_cond group by dtls_id", "dtls_id", "po_id");
			} else {
				$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where is_sales!=1 $dtls_id_cond group by dtls_id", "dtls_id", "po_id");
			}

			$po_array = array();
			$costing_sql = sql_select("select a.job_no, a.style_ref_no,a.dealing_marchant, b.id, b.po_number,b.update_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".(implode(',',$plan_details_array)).") and a.company_name=$compid");
			foreach ($costing_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				
				$update_date_arr[$row[csf('update_date')]] = $row[csf('update_date')];
				$dealing_marchant_arr[$row[csf('dealing_marchant')]] = $supplier_library[$row[csf('dealing_marchant')]];
			}
			
			$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id $dtls_id_cond2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_sales!=1 group by b.id, a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks order by b.machine_dia, b.machine_gg, b.id";
			$nameArray = sql_select($sql);			
			
			//echo $sql;die;
			?>
                <b>Dear Service Provider,</b><br> Merchandiser (<b><? echo implode(', ',$dealing_marchant_arr);?></b>) has changed the below just right now(<b><? echo implode(', ',$update_date_arr);?></b>)   From: <? echo date('Y-m-d',strtotime($date_to));?> to <? echo date('Y-m-d',strtotime($date_from));?>
                
                <br>
                
                <b><? echo $company_library[$compid];?></b>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2600" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="100">Party Name</th>
						<th width="60">Program No</th>
						<th width="80">Program Date</th>
						<th width="80">Start Date</th>
						<th width="80">T.O.D</th>
						<th width="70">Buyer</th>
						<th width="110">Booking No</th>
						<th width="90">Job No</th>
						<th width="130">Order No</th>
						<th width="110">Style</th>
						<th width="80">Dia / GG</th>
						<th width="100">Distribution Qnty</th>
						<th width="80">M/C no</th>
						<th width="70">Status</th>
						<th width="140">Fabric Desc.</th>
						<th width="100">Color Range</th>
						<th width="100">Color Type</th>
						<th width="80">Stitch Length</th>
						<th width="80">Sp. Stitch Length</th>
						<th width="80">Draft Ratio</th>
						<th width="70">Fabric Gsm</th>
						<th width="70">Fabric Dia</th>
						<th width="80">Width/Dia Type</th>
						<th width="100">Program Qnty</th>
						<th width="100">Knitting Qnty</th>
						<th>Remarks</th>
					</thead>

					
					<?
					$i = 1;
					
					foreach ($nameArray as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];

						$machine_no = '';
						$machine_id = explode(",", $row[csf("machine_id")]);
						foreach ($machine_id as $val) {
							if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
						}

						$po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
						$po_no = '';
						$style_ref = '';
						$job_no = '';

						foreach ($po_id as $val) {
							if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= "," . $po_array[$val]['no'];
							if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
							if ($job_no == '') $job_no = $po_array[$val]['job_no'];
						}

						$item_category = $bookingType[$row[csf('booking_no')]][3];
						$fabric_source = $bookingType[$row[csf('booking_no')]][4];
						$is_approve = $bookingType[$row[csf('booking_no')]][5];

						$knitting_qnty = $knit_qnty_array[$row[csf('id')]];
						if ($knitting_qnty > 0) $disabled = "disabled='disabled'"; else $disabled = "";

						if ($row[csf('knitting_source')] == 1) $knitting_source = $company_library[$row[csf('knitting_party')]];
						else if ($row[csf('knitting_source')] == 3) $knitting_source = $supplier_library[$row[csf('knitting_party')]];
						else $knitting_source = "&nbsp;";

						if (!in_array($machine_dia_gg, $machine_dia_gg_array)) {
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="29"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
							</tr>
							<?
							$machine_dia_gg_array[] = $machine_dia_gg;
						}

						$pre = '';
						if ($bookingType[$row[csf('booking_no')]][1] != 4) {
							if ($bookingType[$row[csf('booking_no')]][2] == 1) {
								$pre = "(S)";
							} else {
								$pre = "(M)";
							}
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"  id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="100"><p><? echo $knitting_source; ?></p></td>
							<td width="60" align="center"><a href='##' onClick="generate_report2(<? echo $row[csf('company_id')] . "," . $row[csf('id')]; ?>)"><? echo $row[csf('id')]; ?></a>&nbsp;
							</td>
							<td width="80" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
							<td width="80" align="center">
								<? if ($row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?>
							</td>
							<td width="80" align="center">
								<? if ($row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?>
							</td>
							<td width="70"><p><? echo $buyer_library[$row[csf('buyer_id')]]; ?></p></td>
							<td width="110"><p><? echo $row[csf('booking_no')] . $pre; ?></p></td>
							<td width="90"><p><? echo $job_no; ?></p></td>
							<td width="130"><div style="word-wrap:break-word; width:129px"><? echo $po_no; ?></div></td>
							<td width="110"><p><? echo $style_ref; ?></p></td>
							<td width="80"><p><? echo $machine_dia_gg; ?></p></td>
							<td align="right" width="100"><? echo number_format($row[csf('distribution_qnty')], 2); ?></td>
							<td width="80"><p><? echo $machine_no; ?></p></td>
							<td width="70"><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							<td width="140"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="100"><p><? echo $color_range[$row[csf('color_range')]] ?>&nbsp;</p></td>
							<td width="100"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
							<td align="right" width="80"><? echo number_format($row[csf('draft_ratio')], 2); ?></td>
							<td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
							<td width="80"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</td>
							<td align="right" width="100"><? echo $row[csf('program_qnty')]; ?></td>
							<td align="right" width="100"><? echo number_format($knitting_qnty, 2); ?></td>
							<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
							<?
							$i++;
						}
						?>
					</table>
			<?
		} else {
			echo "<div style='width:1100px' align='center'><font style='color:#F00; font-size:17px; font-weight:bold'>No Program Found.</font></div>";
		}
	
	$mail_item=23;
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=23 and b.mail_user_setup_id=c.id and a.company_id=$compid and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$toArr[$row[csf('email_address')]]=$row[csf('email_address')];
	}
	$to=implode(',',$toArr);
 	$subject = "Fabric Booking Revised";
	
	$message="";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	//if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);

  	}


	//echo $message;

}
	
	





?> 