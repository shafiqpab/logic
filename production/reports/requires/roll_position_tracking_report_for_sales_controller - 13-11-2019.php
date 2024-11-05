<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

	require_once('../../../includes/common.php');
	$user_id = $_SESSION['logic_erp']['user_id'];

	$data = $_REQUEST['data'];
	$action = $_REQUEST['action'];

	if ($action == "load_drop_down_buyer") {
		echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		exit();
	}

	if ($action == "batch_popup") {
		echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
		extract($_REQUEST);
		?>
		<script type="text/javascript">
			function js_set_value(id) {

				var item_id = id.split("_");
				document.getElementById('selected_batch_id').value = item_id[0];
				document.getElementById('selected_batch_no').value = item_id[1];
				parent.emailwindow.hide();
			}
		</script>

	</head>
	<body>
		<div align="center">
			<fieldset style="width:1000px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
						<thead>
							<tr>
								<th>Search By</th>
								<th>Search</th>
								<th>Batch Create Date Range</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
									class="formbutton"/>
									<input type="hidden" id="selected_batch_id" name="selected_batch_id"/>
									<input type="hidden" id="selected_batch_no" name="selected_batch_no"/>
								</th>
							</tr>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr = array(1 => "Batch No", 2 => "Booking No");
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								readonly>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'roll_position_tracking_report_for_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" height="40"
							valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_batch_search_list_view") {
	$data = explode('_', $data);
	$search_by = $data[1];
	$company_name = $data[2];
	$start_date = $data[3];
	$end_date = $data[4];

	if ($search_by == 1)
		$search_field = 'batch_no';
	else
		$search_field = 'booking_no';

	$search_condition = ($data[0] != "") ? " and $search_field like '%" . trim($data[0]) . "%'" : "";
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.batch_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$po_name_arr = array();


	if ($db_type == 2) $group_concat = "  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no";
	else if ($db_type == 0) $group_concat = " group_concat(b.po_number) as order_no";

	$sql_po = sql_select("select a.mst_id,$group_concat from pro_batch_create_dtls a, wo_po_break_down b where a.po_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.mst_id");
	$po_name_arr = array();
	foreach ($sql_po as $p_name) {
		$po_name_arr[$p_name[csf('mst_id')]] = implode(",", array_unique(explode(",", $p_name[csf('order_no')])));
	}
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$arr = array(2 => $po_name_arr, 9 => $color_arr);

	 $sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id from pro_batch_create_mst a
	inner join pro_batch_create_dtls b on a.id = b.mst_id
	where a.company_id=$company_name $search_condition $date_cond and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0
	group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id";
	echo create_list_view("tbl_list_search", "Batch No,Ext. No,Order No,Booking No,Batch Weight,Total Trims Weight, Batch Date,Batch Against,Batch For, Color", "100,70,150,105,80,80,80,80,85,80", "1000", "320", 0, $sql, "js_set_value", "id,batch_no", "", 1, "0,0,id,0,0,0,0,batch_against,batch_for,color_id", $arr, "batch_no,extention_no,id,booking_no,batch_weight,total_trims_weight,batch_date,batch_against,batch_for,color_id", "", '', '0,0,0,0,2,2,3,0,0');
	exit();
}
//--------------------------------------------------------------------------------------------------------------------


if ($action == "report_generate_old_21_03_2017") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_sales_order_no = str_replace("'", "", $txt_sales_order_no);
	$txt_style_ref_no = str_replace("'", "", $txt_style_ref_no);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$txt_barcode_no = str_replace("'", "", $txt_barcode_no);
	$cbo_year = str_replace("'", "", $cbo_year);
	if ($cbo_year != 0) {
		if ($db_type == 0) $year_cond = "and year(a.insert_date)='$cbo_year'";
		else if ($db_type == 2) $year_cond = "and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$sql_cond = "";
	if ($txt_sales_order_no != "") $sql_cond .= " and a.job_no_prefix_num like '%$txt_sales_order_no%'";
	if ($txt_booking_no != "") $sql_cond .= " and a.sales_booking_no like '%$txt_booking_no%'";
	$bar_code_cond = "";
	if ($txt_barcode_no != "") $bar_code_cond = " and d.barcode_no='$txt_barcode_no'";
	$style_ref_cond = "";
	if ($txt_style_ref_no != "") $style_ref_cond = " and a.style_ref_no='$txt_style_ref_no'";

	if ($cbo_buyer_name > 0)
	{
		$booking_buyer = " and f.buyer_id=$cbo_buyer_name";
		$sales_buyer = " and a.buyer_id=$cbo_buyer_name";
		$buyer_without_order = " and g.buyer_id=$cbo_buyer_name";
	}

	$variable_prod = sql_select("select item_category_id, fabric_roll_level, page_upto_id from variable_settings_production where company_name=$company_name and variable_list=3 and status_active=1 and is_deleted=0");
	$variable_data_arr = array();
	foreach ($variable_prod as $row) {
		$variable_data_arr[$row[csf("item_category_id")]]["fabric_roll_level"] = $row[csf("fabric_roll_level")];
		$variable_data_arr[$row[csf("item_category_id")]]["page_upto_id"] = $row[csf("page_upto_id")];
	}

	if ($variable_data_arr[13]["fabric_roll_level"] != 1) {
		echo '<span style=" font-size:18px; font-weight:bold; color:red;">Fabric In Roll Level Not Maintained</span>';
		die;
	}
	$barcode_cond_batch = "";
	if ($txt_barcode_no != "") $barcode_cond_batch = " and c.barcode_no='$txt_barcode_no'";
	$batch_sql = sql_select("select c.roll_id, a.color_id from pro_batch_create_mst a, pro_roll_details c where a.id=c.mst_id and a.entry_form=0 and c.entry_form=64 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_id>0 $barcode_cond_batch");
	$batch_color_data = array();
	foreach ($batch_sql as $row) {
		$batch_color_data[$row[csf("roll_id")]] = $row[csf("color_id")];
	}
	unset($batch_sql);

	$barcode_cond_prod = "";
	if ($txt_barcode_no != "") $barcode_cond_prod = " and barcode_no='$txt_barcode_no'";
	$position_sql = sql_select("select roll_id, po_breakdown_id,
		max(case when entry_form=56 then roll_id else 0 end) as grey_delivery,
		max(case when entry_form=56 then insert_date else null end) as grey_delivery_date,
		max(case when entry_form=58 then roll_id else 0 end) as grey_rcv_store,
		max(case when entry_form=58 then insert_date else null end) as grey_rcv_store_date,
		max(case when entry_form=61 and is_returned=0 then roll_id else 0 end) as grey_issue_batch,
		sum(case when entry_form=61 and is_returned=0 then qnty else 0 end) as batch_issue_qnty,
		max(case when entry_form=61 and is_returned=0 then insert_date else null end) as grey_issue_batch_date,
		max(case when entry_form=62 then roll_id else 0 end) as grey_rcv_batch,
		max(case when entry_form=62 then insert_date else null end) as grey_rcv_batch_date,
		max(case when entry_form=64 then roll_id else 0 end) as batch_created,
		sum(case when entry_form=64 then qnty else 0 end) as batch_create_qnty,
		max(case when entry_form=64 then insert_date else null end) as batch_created_date,
		max(case when entry_form=66 then roll_id else 0 end) as finishion,
		max(case when entry_form=66 then insert_date else null end) as finishion_date,
		sum(case when entry_form=66 then qc_pass_qnty else 0 end) as finishion_qnty,
		max(case when entry_form=67 then roll_id else 0 end) as fin_delivery,
		max(case when entry_form=67 then insert_date else null end) as fin_delivery_date,
		max(case when entry_form=68 then roll_id else 0 end) as fin_rcv_store,
		max(case when entry_form=68 then insert_date else null end) as fin_rcv_store_date,
		max(case when entry_form=71 then roll_id else 0 end) as fin_issu_cut,
		max(case when entry_form=71 then insert_date else null end) as fin_issu_cut_date,
		max(case when entry_form=72 then roll_id else 0 end) as fin_receive_cut,
		max(case when entry_form=72 then insert_date else null end) as fin_receive_cut_date
		from pro_roll_details where status_active=1 and is_deleted=0 and roll_id>0   group by roll_id, po_breakdown_id");

	$batch_issue_qtny_arr = $batch_creat_qnty_arr = $roll_data_arr = array();
	foreach ($position_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_delivery"] = $row[csf("grey_delivery")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_delivery_date"] = $row[csf("grey_delivery_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_store"] = $row[csf("grey_rcv_store")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_store_date"] = $row[csf("grey_rcv_store_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_issue_batch"] = $row[csf("grey_issue_batch")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_issue_batch_date"] = $row[csf("grey_issue_batch_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch"] = $row[csf("grey_rcv_batch")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch_date"] = $row[csf("grey_rcv_batch_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["batch_created"] = $row[csf("batch_created")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["batch_created_date"] = $row[csf("batch_created_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion"] = $row[csf("finishion")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion_date"] = $row[csf("finishion_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion_qnty"] = $row[csf("finishion_qnty")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_delivery"] = $row[csf("fin_delivery")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_delivery_date"] = $row[csf("fin_delivery_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_rcv_store"] = $row[csf("fin_rcv_store")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_rcv_store_date"] = $row[csf("fin_rcv_store_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_issu_cut"] = $row[csf("fin_issu_cut")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_issu_cut_date"] = $row[csf("fin_issu_cut_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_receive_cut"] = $row[csf("fin_receive_cut")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_receive_cut_date"] = $row[csf("fin_receive_cut_date")];
		$batch_issue_qtny_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]] += $row[csf("batch_issue_qnty")];
		$batch_creat_qnty_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]] += $row[csf("batch_create_qnty")];
	}

	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	$machine_data = array();
	foreach ($machine_sql as $row) {
		$machine_data[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_data[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_data[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}


	$sub_process_data = array();
	$dyeing_roll_sql = sql_select("select b.roll_id from pro_fab_subprocess a, pro_batch_create_dtls b where a.batch_id=b.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.roll_id>0 and a.status_active=1 and b.status_active=1");
	foreach ($dyeing_roll_sql as $row) {
		$sub_process_data[$row[csf("roll_id")]][2] = $row[csf("roll_id")];
	}
	//production_date as end_date end_hours,end_minutes
	$sub_process_sql = sql_select("select b.roll_id,
		max(case when b.entry_page=30 then b.roll_id else 0 end) as slitting_roll,
		max(case when b.entry_page=30 then a.production_date else null end) as slitting_roll_date,
		max(case when b.entry_page=30 then a.end_hours else null end) as slt_hours,
		max(case when b.entry_page=30 then a.end_minutes else null end) as slt_minutes,
		max(case when b.entry_page=31 then b.roll_id else 0 end) as drying_roll,
		max(case when b.entry_page=31 then a.production_date else null end) as drying_roll_date,
		max(case when b.entry_page=31 then a.end_hours else null end) as dry_hours,
		max(case when b.entry_page=31 then a.end_minutes else null end) as dry_minutes,

		max(case when b.entry_page=32 then b.roll_id else 0 end) as heat_roll,
		max(case when b.entry_page=32 then a.production_date else null end) as heat_roll_date,
		max(case when b.entry_page=32 then a.end_hours else null end) as heat_hours,
		max(case when b.entry_page=32 then a.end_minutes else null end) as heat_minutes,

		max(case when b.entry_page=33 then b.roll_id else 0 end) as compaction_roll,
		max(case when b.entry_page=33 then a.production_date else null end) as compaction_roll_date,
		max(case when b.entry_page=33 then a.end_hours else null end) as com_hours,
		max(case when b.entry_page=33 then a.end_minutes else null end) as com_minutes,

		max(case when b.entry_page=34 then b.roll_id else 0 end) as special_finish_roll,
		max(case when b.entry_page=34 then a.production_date else null end) as special_finish_roll_date,
		max(case when b.entry_page=34 then a.end_hours else null end) as sfin_hours,
		max(case when b.entry_page=34 then a.end_minutes else null end) as sfin_minutes,

		max(case when b.entry_page=35 and a.load_unload_id=2 then b.roll_id else 0 end) as dyeing_roll,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.production_date else null end) as dyeing_roll_date,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.end_hours else null end) as dyeing_hours,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.end_minutes else null end) as dyeing_minutes,

		max(case when b.entry_page=48 then b.roll_id else 0 end) as stentering_roll,
		max(case when b.entry_page=48 then a.production_date else null end) as stentering_roll_date,
		max(case when b.entry_page=48 then a.end_hours else null end) as sten_hours,
		max(case when b.entry_page=48 then a.end_minutes else null end) as sten_minutes

		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and b.entry_page in(30,31,32,33,35,34,48) and b.roll_id>0 and b.status_active=1 and b.is_deleted=0 group by b.roll_id");

	$sub_process_data = $sub_process_dateTime = array();
	foreach ($sub_process_sql as $row) {
		$sub_process_data[$row[csf("roll_id")]][2] = $row[csf("dyeing_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][2] = $row[csf("dyeing_roll_date")] . '<br>' . $row[csf("dyeing_hours")] . ':' . $row[csf("dyeing_minutes")];

		$sub_process_data[$row[csf("roll_id")]][1] = $row[csf("heat_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][1] = $row[csf("heat_roll_date")] . '<br>' . $row[csf("heat_hours")] . ':' . $row[csf("heat_minutes")];
		$sub_process_data[$row[csf("roll_id")]][3] = $row[csf("slitting_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][3] = $row[csf("slitting_roll_date")] . '<br>' . $row[csf("slt_hours")] . ':' . $row[csf("slt_minutes")];
		$sub_process_data[$row[csf("roll_id")]][4] = $row[csf("stentering_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][4] = $row[csf("stentering_roll_date")] . '<br>' . $row[csf("sten_hours")] . ':' . $row[csf("sten_minutes")];
		$sub_process_data[$row[csf("roll_id")]][5] = $row[csf("drying_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][5] = $row[csf("drying_roll_date")] . '<br>' . $row[csf("dry_hours")] . ':' . $row[csf("dry_minutes")];
		$sub_process_data[$row[csf("roll_id")]][6] = $row[csf("special_finish_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][6] = $row[csf("special_finish_roll_date")] . '<br>' . $row[csf("sfin_hours")] . ':' . $row[csf("sfin_hours")];
		$sub_process_data[$row[csf("roll_id")]][7] = $row[csf("compaction_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][7] = $row[csf("compaction_roll_date")] . '<br>' . $row[csf("com_hours")] . ':' . $row[csf("com_hours")];
	}


	$composition_arr = array();
	$compositionData = sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls");
	foreach ($compositionData as $row) {
		$composition_arr[$row[csf('mst_id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	/*$sql = "select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id, d.barcode_no,d.qnty as grey_qnty, f.buyer_id, e.machine_dia, e.stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id
		from fabric_sales_order_mst a, wo_booking_mst f,ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d
		where a.sales_booking_no=f.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and a.status_active=1 and a.is_deleted=0
 		and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1
 		group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id, d.barcode_no, d.qnty, f.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id";*/

	$sql="select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id, d.barcode_no,d.qnty as grey_qnty, f.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis
	from fabric_sales_order_mst a, wo_booking_mst f,ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d
	where a.sales_booking_no=f.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and a.status_active=1 and a.is_deleted=0
	and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $booking_buyer and d.entry_form in(2,22)
	group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id, d.barcode_no, d.qnty, f.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form

	union all
	select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id, d.barcode_no,d.qnty as grey_qnty, g.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis
	from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst g, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d
	where a.sales_booking_no=g.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and a.status_active=1 and a.is_deleted=0
	and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $buyer_without_order and d.entry_form in(2,22) group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id, d.barcode_no, d.qnty, g.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form
	union all
	select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id, d.barcode_no,d.qnty as grey_qnty, a.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis
	from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d
	where  a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and a.status_active=1 and a.is_deleted=0
	and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $sales_buyer and d.entry_form in(2,22) group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id, d.barcode_no, d.qnty, a.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form
	union all
	select a.id,a.company_id,a.style_ref_no,a.sales_booking_no, a.job_no,c.body_part_id,cast(c.febric_description_id as varchar2(2000)) as fabric_desc,null as dia, cast(c.width as varchar2(2000)) as fabric_dia,c.gsm as gsm_weight,d.roll_no, d.id as roll_id,d.barcode_no,d.qnty as grey_qnty,(case when a.within_group = 2 then a.buyer_id when a.within_group =1 and f.id is not null then f.buyer_id when a.within_group = 1 and g.id is not null then g.buyer_id end) as buyer_id, c.machine_dia,cast(c.stitch_length as varchar2(2000)) as stitch_length,c.machine_gg,c.color_range_id,c.color_id,d.entry_form, 4 as basis  from fabric_sales_order_mst a left join wo_booking_mst f on a.sales_booking_no =  f.booking_no $booking_buyer left join wo_non_ord_samp_booking_mst g on a.sales_booking_no =  g.booking_no $buyer_without_order , inv_receive_master b, pro_grey_prod_entry_dtls c,  pro_roll_details d where a.job_no = b.booking_no and b.id = c.mst_id and b.id = d.mst_id and c.id  =  d.dtls_id and b.receive_basis = 4 and d.is_sales =1 and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond $sales_buyer and a.status_active = 1 and b.status_active=1 and c.status_active =1 and d.status_active =1";


	$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent, a.id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ");
	foreach ($determination_sql as $val)
	{

		if($val[csf("construction")] != "")
		{
			$fabric_desc_arr[$val[csf("id")]] = $val[csf("construction")]. ", ";
		}
		$fabric_desc_arr[$val[csf("id")]] .= $composition[$val[csf("copmposition_id")]]. " " .$val[csf("percent")] . "% ";
	}

	$nameArray = sql_select($sql);

	foreach ($nameArray as $row) {
		if($row[csf("basis")] == 4)
		{
			$fabric_description =  $fabric_desc_arr[$row[csf("fabric_desc")]];
		}else{
			$fabric_description =  $row[csf("fabric_desc")];
		}
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["grey_qnty"] += $row[csf("grey_qnty")];
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_issue_qtny"] += $batch_issue_qtny_arr[$row[csf("roll_id")]][$row[csf("id")]];
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_creat_qnty"] += $batch_creat_qnty_arr[$row[csf("roll_id")]][$row[csf("id")]];


		$garph_data[1] += $row[csf("grey_qnty")];
		$garph_caption[1] = "Grey Wgt";

		if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_delivery"] > 0) {
			$garph_data[2] += $row[csf("grey_qnty")];
			$garph_caption[2] = "Delv. To Store";
		} else {
			$garph_data[2] += 0;
			$garph_caption[2] = "Delv. To Store";
		}
		if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_rcv_store"] > 0) {
			$garph_data[3] += $row[csf("grey_qnty")];
			$garph_caption[3] = "Recv. by Store";
		} else {
			$garph_data[3] += 0;
			$garph_caption[3] = "Recv. by Store";
		}

		if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_issue_batch"] > 0) {
			$garph_data[4] += $row[csf("grey_qnty")];
			$garph_caption[4] = "Issue to Batch";
		} else {
			$garph_data[4] += 0;
			$garph_caption[4] = "Issue to Batch";
		}

		if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
			if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_rcv_batch"] > 0) {
				$garph_data[5] += $row[csf("grey_qnty")];
				$garph_caption[5] = "Recv. by Batch";
			} else {
				$garph_data[5] += 0;
				$garph_caption[5] = "Recv. by Batch";
			}
			if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["batch_created"] > 0) {
				$garph_data[6] += $row[csf("grey_qnty")];
				$garph_caption[6] = "Batch Create";
			} else {
				$garph_data[6] += 0;
				$garph_caption[6] = "Batch Create";
			}
		}

		$p = 6;
		if ($variable_data_arr[50]["page_upto_id"] > 0) {
			for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
				if ($sub_process_data[$row[csf("roll_id")]][$i] > 0) {
					$p++;
					$garph_data[$p] += $row[csf("grey_qnty")];
					$garph_caption[$p] = "" . $upto_receive_batch[$i] . "";
				} else {
					$p++;
					$garph_data[$p] += 0;
					$garph_caption[$p] = "" . $upto_receive_batch[$i] . "";
				}

			}
		}

	}

	$div_width = 1568;
	$table_width = 1550;
	$coll_span = 15;
	if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
		$div_width = $div_width + 140;
		$table_width = $table_width + 140;
		$coll_span = $coll_span + 2;
	}
	if ($variable_data_arr[50]["page_upto_id"] > 0) {
		$div_width = $div_width + (70 * $variable_data_arr[50]["page_upto_id"]);
		$table_width = $table_width + (70 * $variable_data_arr[50]["page_upto_id"]);
		$coll_span = $coll_span + $variable_data_arr[50]["page_upto_id"];
	}
	if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
		$div_width = $div_width + 570;
		$table_width = $table_width + 570;
		$coll_span = $coll_span + 8;
	}

	ob_start();
	?>
	<div style="width:<? echo $div_width; ?>px;">
		<fieldset style="width:<? echo $div_width; ?>px;">
			<p style="color:red; font-size:18px; font-weight:bold; text-align:left; padding-left:10px;">Note : Column
			Total Will Not Recalculate With html Filter.</p>
			<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
				<tr>
					<td align="center" width="100%" colspan="<? echo $coll_span; ?>"
						class="form_caption"><? echo $report_title; ?></td>
					</tr>
				</table>
				<table border="0" width="<? echo $table_width; ?>" align="left">
					<tr>
						<td width="45%">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"
							align="left">
							<thead>
								<tr>
									<th width="16%">Color Range</th>
									<th width="16%">Body Part</th>
									<th width="17%">Fabric Description</th>
									<th width="10%">GSM</th>
									<th width="10%">Dia</th>
									<th width="10%">Produced</th>
									<th width="10%">Issued To Batch</th>
									<th>Batch Done</th>
								</tr>
							</thead>
							<tbody>
								<?
								$j = 1;
								foreach ($summary_data as $color_range_id => $color_range_val) {
									foreach ($color_range_val as $body_part_id => $body_part_val) {
										foreach ($body_part_val as $febric_des_id => $febric_des_val) {
											foreach ($febric_des_val as $gsm => $gsm_val) {
												foreach ($gsm_val as $dia => $dia_val) {
													if ($j % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>">
														<td><p><? echo $color_range[$color_range_id]; ?>&nbsp;</p></td>
														<td><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
														<td><p><? echo $febric_des_id; ?>&nbsp;</p></td>
														<td align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
														<td align="center"><p><? echo $dia; ?>&nbsp;</p></td>
														<td align="right"><? echo number_format($dia_val["grey_qnty"], 2); ?></td>
														<td align="right"><? echo number_format($dia_val["batch_issue_qtny"], 2); ?></td>
														<td align="right"><? echo number_format($dia_val["batch_creat_qnty"], 2); ?></td>
													</tr>
													<?
													$summ_tot_grey_qnty += $dia_val["grey_qnty"];
													$summ_tot_batch_issue_qtny += $dia_val["batch_issue_qtny"];
													$summ_tot_batch_creat_qnty += $dia_val["batch_creat_qnty"];
													$j++;
												}
											}
										}

									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="5" align="right">Summary Total:</th>
									<th align="right"><? echo number_format($summ_tot_grey_qnty, 2); ?></th>
									<th align="right"><? echo number_format($summ_tot_batch_issue_qtny, 2); ?></th>
									<th align="right"><? echo number_format($summ_tot_batch_creat_qnty, 2); ?></th>
								</tr>
								<tr>
									<th colspan="5" align="right">Pending:</th>
									<th align="right"><? $pending_issue_batch = $summ_tot_grey_qnty - $summ_tot_batch_issue_qtny; ?>
									&nbsp;</th>
									<th align="right"><? $pending_tot_batch_creat_qnty = $summ_tot_batch_issue_qtny - $summ_tot_batch_creat_qnty;
									echo number_format($pending_issue_batch, 2); ?></th>
									<th align="right"><? echo number_format($pending_tot_batch_creat_qnty, 2); ?></th>
								</tr>
								<tr>
									<th colspan="5" align="right">Pending%:</th>
									<th align="right"><? $pending_issue_batch_percent = (($pending_issue_batch / $summ_tot_grey_qnty) * 100); ?>
									&nbsp;</th>
									<th align="right"><? $summ_tot_batch_creat_percent = (($pending_tot_batch_creat_qnty / $summ_tot_batch_issue_qtny) * 100);
									echo number_format($pending_issue_batch_percent, 2); ?></th>
									<th align="right"><? echo number_format($summ_tot_batch_creat_percent, 2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
					<td width="5%"></td>
					<td valign="top" width="700">
						<canvas id="canvas3" height="350" width="700"></canvas>
					</td>
					<td></td>
				</tr>
			</table>

			<table border="0" width="<? echo $table_width; ?>" align="left">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">PO Buyer</th>
						<th width="120">FSO No & Sty Ref</th>
						<th width="120">Booking No</th>
						<th width="60">Roll No</th>
						<th width="100">Barcode No</th>
						<th width="100">Color Range</th>
						<th width="100">Body Part</th>
						<th width="100">Color</th>
						<th width="170">Fabric Description</th>
						<th width="60">GSM</th>
						<th width="60">Dia</th>
						<th width="60">Stich Lenth</th>
						<th width="60">Machine Dia</th>
						<th width="60">Guage</th>
						<th width="80">Grey Wgt.</th>
						<th width="70">Delv. To Store</th>
						<th width="70">Recv. by Store</th>
						<th width="70">Issue to Batch</th>
						<?
						if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
							?>
							<th width="70">Recv. by Batch</th>
							<th width="70">Batch Create</th>
							<?
						}
						if ($variable_data_arr[50]["page_upto_id"] > 0) {
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								?>
								<th width="70"><p><? echo $upto_receive_batch[$i]; ?></p></th>
								<?
							}
						}
						if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
							?>
							<th width="70">Finish</th>
							<th width="70">Finish Wgt.</th>
							<th width="70">Process Loss</th>
							<th width="70">Delv. To Store</th>
							<th width="70">Recv. by Store</th>
							<th width="70">Issue to Cut</th>
							<th>Recv. by Cut</th>

							<?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
					class="rpt_table" id="table_body">
					<?
					$m = 1;
					$tot_grey_delivery = $tot_grey_rcv_store = $tot_grey_issue_batch = $tot_grey_rcv_batch = $tot_batch_created = $tot_fin_delivery = $tot_fin_rcv_store = $tot_fin_issu_cut = 0;
					foreach ($nameArray as $row) {
						if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$grey_delivery_bgcolor = $grey_rcv_store_bgcolor = $grey_issue_batch_bgcolor = $grey_rcv_batch_bgcolor = $batch_created_bgcolor = $finishion_bgcolor = $fin_delivery_bgcolor = $fin_rcv_store_bgcolor = $fin_rcv_store_bgcolor = '';

						$grey_delivery_date = $grey_rcv_store_date = $grey_issue_batch_day = $grey_rcv_batch_day = $batch_created_day = $finishion_day = $fin_delivery_day = $fin_rcv_store_day = $fin_issu_cut_day = "";

						if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_delivery"] > 0) {
							$grey_delivery_bgcolor = 'bgcolor="green"';
							$grey_delivery_date = $roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_delivery_date"];
							$tot_grey_delivery += $row[csf("grey_qnty")];
						}

						if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_rcv_store"] > 0) {
							$grey_rcv_store_bgcolor = 'bgcolor="green"';
							$grey_rcv_store_date = $roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_rcv_store_date"];
							$tot_grey_rcv_store += $row[csf("grey_qnty")];
						}

						if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_issue_batch"] > 0) {
							$grey_issue_batch_bgcolor = 'bgcolor="green"';
							$grey_issue_batch_date = $roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_issue_batch_date"];
							$tot_grey_issue_batch += $row[csf("grey_qnty")];
						}

						if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_rcv_batch"] > 0) {
							$grey_rcv_batch_bgcolor = 'bgcolor="green"';
							$grey_rcv_batch_date = $roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch += $row[csf("grey_qnty")];
						}
						if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["batch_created"] > 0) {
							$batch_created_bgcolor = 'bgcolor="green"';
							$batch_created_date = $roll_data_arr[$row[csf("roll_id")]][$row[csf("id")]]["batch_created_date"];
							$tot_batch_created += $row[csf("grey_qnty")];
						}
						$sub_process_bgcolor = $sub_process_day = array();
						if ($variable_data_arr[50]["page_upto_id"] > 0) {
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								if ($sub_process_data[$row[csf("roll_id")]][$i] > 0) {
									$sub_process_bgcolor[$i] = 'bgcolor="green"';
									$sub_process_day[$i] = $sub_process_dateTime[$row[csf("roll_id")]][$i];
									$tot_sub_process[$i] += $row[csf("grey_qnty")];
								}

							}
						}
						$color_names = '';
						$colors = explode(",", $row[csf("color_id")]);
						foreach ($colors as $color) {
							$color_names .= $color_library[$color] . ",";
						}

						if ($row[csf("entry_form")] == 2) $roll_entry_form = "Production"; else $roll_entry_form = "Receive";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
							<td width="30" align="center"><? echo $m; ?></td>
							<td width="100" align="center"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $row[csf("job_no")] . "<br>" . $row[csf("style_ref_no")]; ?></p></td>
							<td width="120"><p><? echo $row[csf("sales_booking_no")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><a href="##"
								onClick="openmypage_popup('<? echo $row[csf("roll_id")]; ?>','roll_popup')"><? echo $row[csf("roll_no")]; ?></a></p></td>
								<td width="100" align="center"><p><? echo $row[csf("barcode_no")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $color_range[$row[csf("color_range_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo rtrim($color_names, ","); ?>&nbsp;</p></td>
								<td width="170" align="center"><p><? echo $fabric_description;//$row[csf("fabric_desc")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("gsm_weight")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("fabric_dia")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("stitch_length")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("machine_dia")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("machine_gg")]; ?>&nbsp;</p></td>
								<td width="80" align="right"
								title="<? echo $roll_entry_form . "**" . $row[csf("roll_id")] . "**" . $row[csf("po_id")]; ?>"><? echo number_format($row[csf("grey_qnty")], 2);
								$total_grey_qnty += $row[csf("grey_qnty")]; ?></td>
								<td width="70" align="center" style="word-break:break-all;"
								valign="middle" <? echo $grey_delivery_bgcolor; ?>><? echo $grey_delivery_date; ?></td>
								<td width="70" align="center" style="word-break:break-all;"
								valign="middle" <? echo $grey_rcv_store_bgcolor; ?>><? echo $grey_rcv_store_date; ?></td>
								<td width="70" align="center" style="word-break:break-all;"
								valign="middle" <? echo $grey_issue_batch_bgcolor; ?>><? echo $grey_issue_batch_date; ?></td>
								<?
								if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
									?>
									<td width="70" align="center" style="word-break:break-all;"
									valign="middle" <? echo $grey_rcv_batch_bgcolor; ?>><? echo $grey_rcv_batch_date; ?></td>
									<td width="70" align="center" style="word-break:break-all;"
									valign="middle" <? echo $batch_created_bgcolor; ?>><? echo $batch_created_date; ?></td>
									<?
								}

								if ($variable_data_arr[50]["page_upto_id"] > 0) {
									for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
										?>
										<td width="70" style="word-break:break-all"
										title="<? echo $row[csf("grey_qnty")]; ?>" align="center"
										valign="middle" <? echo $sub_process_bgcolor[$i]; ?> ><? echo $sub_process_day[$i]; ?></td>
										<?
									}
								}

								if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
									?>
									<td width="70" style="word-break:break-all" align="center"
									valign="middle" <? echo $finishion_bgcolor; ?>><? echo $finishion_day; ?></td>
									<td width="70"
									align="right"><? if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion"] > 0) echo number_format($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"], 2);
									$total_finishing_qnty += $roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"]; ?></td>
									<td width="70" align="right">
										<?
										$processes_loss = 0;
										$processes_loss = $row[csf("grey_qnty")] - $roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"];
										if ($roll_data_arr[$row[csf("roll_id")]][$row[csf("po_id")]]["finishion_qnty"] > 0) {
											echo number_format($processes_loss, 2);
											$total_processes_loss += $processes_loss;
										}
										?>
									</td>
									<td width="70" align="center"
									valign="right" <? echo $fin_delivery_bgcolor; ?>><? echo $fin_delivery_day; ?></td>
									<td width="70" align="center"
									valign="right" <? echo $fin_rcv_store_bgcolor; ?>><? echo $fin_rcv_store_day; ?></td>
									<td width="70" align="center"
									valign="right" <? echo $fin_issu_cut_bgcolor; ?>><? echo $fin_issu_cut_day; ?></td>
									<td width="" align="center"
									valign="right" <? echo $fin_receive_cut_bgcolor; ?>><? echo $fin_receive_cut_day; ?></td>
									<?
								}
								?>
								<!--							<td width="70" align="center" valign="middle" <?php echo ($finish_receive_dt != '') ? 'bgcolor="green"' : ''; ?> ><?php echo $finish_receive_dt; ?></td>-->

							</tr>
							<?
							$m++;
						}
						?>
					</table>
				</div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
					class="rpt_table" id="rpt_table_footer" align="left">
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="170">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60" align="right">Total:</th>
							<th width="80" align="right"
							id="value_total_grey_qnty"><? echo number_format($total_grey_qnty, 2); ?></th>
							<th width="60" align="right"><? echo number_format($tot_grey_delivery, 2);
							$pending_grey_delivery = $total_grey_qnty - $tot_grey_delivery; ?></th>
							<th width="70" align="right"><? echo number_format($tot_grey_rcv_store, 2);
							$pending_grey_rcv_store = $tot_grey_delivery - $tot_grey_rcv_store; ?></th>
							<th width="70" align="right"><? echo number_format($tot_grey_issue_batch, 2);
							$pending_grey_issue_batch = $tot_grey_rcv_store - $tot_grey_issue_batch; ?></th>
							<?
							if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right"><? echo number_format($tot_grey_rcv_batch, 2);
								$pending_grey_rcv_batch = $tot_grey_issue_batch - $tot_grey_rcv_batch; ?></th>
								<th width="70" align="right"><? echo number_format($tot_batch_created, 2);
								$pending_batch_created = $tot_grey_rcv_batch - $tot_batch_created; ?></th>
								<?
							}
							if ($variable_data_arr[50]["page_upto_id"] > 0) {
								for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
									?>
									<th width="70" align="right"><? echo number_format($tot_sub_process[$i], 2); ?></th>
									<?
									if ($i == 1) {
										$pending_sub_process[$i] = $tot_batch_created - $tot_sub_process[$i];
									} else {
										$pending_sub_process[$i] = $tot_sub_process[$i - 1];
										-$tot_sub_process[$i];
									}
								}
							}

							if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right"><? echo number_format($total_finishing_qnty, 2); ?></th>
								<th width="70" align="right"><? echo number_format($total_processes_loss, 2); ?></th>
								<th width="70" align="right"><? echo number_format($tot_fin_delivery, 2); ?></th>
								<th width="70" align="right"><? echo number_format($tot_fin_rcv_store, 2); ?></th>
								<th width="70" align="right"><? echo number_format($tot_fin_issu_cut, 2); ?></th>
								<th width="" align="right"><? echo number_format($tot_fin_receive_cut, 2); ?></th>

								<?

							}
							?>
						</tr>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="170">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60" align="right">Pending:</th>
							<th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_grey_delivery, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_grey_rcv_store, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_grey_issue_batch, 2); ?></th>
							<?
							if ($variable_data_arr[50]["fabric_roll_level"] == 1) {

								?>
								<th width="70" align="right"><? echo number_format($pending_grey_rcv_batch, 2); ?></th>
								<th width="70" align="right"><? echo number_format($pending_batch_created, 2); ?></th>
								<?
							}
							if ($variable_data_arr[50]["page_upto_id"] > 0) {
								for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
									?>
									<th width="70" align="right"><? echo number_format($pending_sub_process[$i], 2); ?></th>
									<?
								}
							}

							if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="" align="right">&nbsp;</th>

								<?
							}
							?>
						</tr>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="170">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60" align="right">Pending%:</th>
							<th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_delivery / $total_grey_qnty) * 100, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_rcv_store / $tot_grey_delivery) * 100, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_issue_batch / $tot_grey_rcv_store) * 100, 2); ?></th>
							<?
							if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
								?>
								<th width="70"
								align="right"><? echo number_format(($pending_grey_rcv_batch / $tot_grey_issue_batch) * 100, 2); ?></th>
								<th width="70"
								align="right"><? echo number_format(($pending_batch_created / $tot_grey_rcv_batch) * 100, 2); ?></th>
								<?
							}
							if ($variable_data_arr[50]["page_upto_id"] > 0) {
								for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
									if ($i == 1) {
										?>
										<th width="70"
										align="right"><? echo number_format(($pending_sub_process[$i] / $tot_batch_created) * 100, 2); ?></th>
										<?
									} else {
										?>
										<th width="70"
										align="right"><? echo number_format(($pending_sub_process[$i] / $tot_sub_process[$i - 1]) * 100, 2); ?></th>
										<?
									}

								}
							}

							if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="" align="right">&nbsp;</th>

								<?
							}
							?>
						</tr>
					</tfoot>
				</table>
			</fieldset>
		</div>
		<?

	//echo "<pre>";print_r($garph_caption);
	//echo "<pre>";print_r($garph_data);die;
		$garph_caption = json_encode($garph_caption);
		$garph_data = json_encode($garph_data);

		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
	//---------end------------//
		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		$filename = $user_id . "_" . $name . ".xls";
		echo "$total_data####$filename####$garph_caption####$garph_data";

		disconnect($con);
		exit();
}

if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_sales_order_no = str_replace("'", "", $txt_sales_order_no);
	$txt_style_ref_no = str_replace("'", "", $txt_style_ref_no);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$txt_barcode_no = str_replace("'", "", $txt_barcode_no);
	$cbo_year = str_replace("'", "", $cbo_year);
	if ($cbo_year != 0) {
		if ($db_type == 0) $year_cond = "and year(a.insert_date)='$cbo_year'";
		else if ($db_type == 2) $year_cond = "and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$sql_cond = "";
	if ($txt_sales_order_no != "") $sql_cond .= " and a.job_no_prefix_num like '%$txt_sales_order_no%'";
	if ($txt_booking_no != "") $sql_cond .= " and a.sales_booking_no like '%$txt_booking_no%'";
	$bar_code_cond = "";
	if ($txt_barcode_no != "") $bar_code_cond = " and d.barcode_no='$txt_barcode_no'";
	$style_ref_cond = "";
	if ($txt_style_ref_no != "") $style_ref_cond = " and a.style_ref_no='$txt_style_ref_no'";

	if ($cbo_buyer_name > 0)
	{
		$booking_buyer = " and f.buyer_id=$cbo_buyer_name";
		$sales_buyer = " and a.buyer_id=$cbo_buyer_name";
		$buyer_without_order = " and g.buyer_id=$cbo_buyer_name";
	}

	$variable_prod = sql_select("select item_category_id, fabric_roll_level, page_upto_id from variable_settings_production where company_name=$company_name and variable_list=3 and status_active=1 and is_deleted=0");
	$variable_data_arr = array();
	foreach ($variable_prod as $row) {
		$variable_data_arr[$row[csf("item_category_id")]]["fabric_roll_level"] = $row[csf("fabric_roll_level")];
		$variable_data_arr[$row[csf("item_category_id")]]["page_upto_id"] = $row[csf("page_upto_id")];
	}

	if ($variable_data_arr[13]["fabric_roll_level"] != 1) {
		echo '<span style=" font-size:18px; font-weight:bold; color:red;">Fabric In Roll Level Not Maintained</span>';
		die;
	}


	$sql="select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, f.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis
 	from fabric_sales_order_mst a, wo_booking_mst f,ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d
 	where a.sales_booking_no=f.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and a.status_active=1 and a.is_deleted=0
 	and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $booking_buyer and d.entry_form in(2)
 	group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, f.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form

 	union all
 	select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, g.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis
 	from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst g, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d
 	where a.sales_booking_no=g.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and a.status_active=1 and a.is_deleted=0
 	and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $buyer_without_order and d.entry_form in(2) group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, g.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form and d.booking_without_order=1
 	union all
 	select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, a.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis
 	from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d ,inv_receive_master b
 	where  a.id=c.po_id and c.dtls_id=e.id and c.dtls_id = b.booking_id and d.mst_id = b.id and b.entry_form =2 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $sales_buyer and d.entry_form in(2) and a.within_group =2 group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, a.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form
 	union all
 	select a.id,a.company_id,a.style_ref_no,a.sales_booking_no, a.job_no,c.body_part_id,cast(c.febric_description_id as varchar2(2000)) as fabric_desc,null as dia, cast(c.width as varchar2(2000)) as fabric_dia,c.gsm as gsm_weight,d.roll_no, d.id as roll_id,d.roll_split_from,d.barcode_no,d.qnty as grey_qnty,(case when a.within_group = 2 then a.buyer_id when a.within_group =1 and f.id is not null then f.buyer_id when a.within_group = 1 and g.id is not null then g.buyer_id end) as buyer_id, c.machine_dia,cast(c.stitch_length as varchar2(2000)) as stitch_length,c.machine_gg,c.color_range_id,c.color_id,d.entry_form, 4 as basis  from fabric_sales_order_mst a left join wo_booking_mst f on a.sales_booking_no =  f.booking_no $booking_buyer left join wo_non_ord_samp_booking_mst g on a.sales_booking_no =  g.booking_no $buyer_without_order , inv_receive_master b, pro_grey_prod_entry_dtls c,  pro_roll_details d where a.job_no = b.booking_no and b.id = c.mst_id and b.id = d.mst_id and c.id  =  d.dtls_id and b.receive_basis = 4 and d.is_sales =1 and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond $sales_buyer and a.status_active = 1 and b.status_active=1 and c.status_active =1 and d.status_active =1";

 	$nameArray = sql_select($sql);

	$all_barcode=array();

	foreach ($nameArray as $resval)
	{
		$all_barcode[$resval[csf("barcode_no")]] = $resval[csf("barcode_no")];
	}

	$all_barcode_nos = implode(",",array_filter(array_unique($all_barcode)));


	if($all_barcode_nos=="") $all_barcode_nos=0;
 	$barcodeCond_c = $all_barcode_cond_c = $barcodeCond = $all_barcode_cond = "";
 	$all_barcode_arr=explode(",",$all_barcode_nos);
	if($db_type==2 && count($all_barcode_arr)>999)
	{
		$all_barcode_chunk_arr=array_chunk($all_barcode_arr,999) ;
		foreach($all_barcode_chunk_arr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$barcodeCond_c.=" c.barcode_no in($chunk_arr_value) or ";
			$barcodeCond.=" barcode_no in($chunk_arr_value) or ";
		}

		$all_barcode_cond_c.=" and (".chop($barcodeCond_c,'or ').")";
		$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";

	}
	else
	{
		$all_barcode_cond_c=" and c.barcode_no in($all_barcode_nos)";
		$all_barcode_cond=" and barcode_no in($all_barcode_nos)";
	}

	$barcode_cond_batch = "";
	if ($txt_barcode_no != "") $barcode_cond_batch = " and c.barcode_no='$txt_barcode_no'";
	$batch_sql = sql_select("select c.roll_id, a.color_id from pro_batch_create_mst a, pro_roll_details c where a.id=c.mst_id and a.entry_form=0 and c.entry_form=64 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_id>0 $barcode_cond_batch $all_barcode_cond_c");
	$batch_color_data = array();
	foreach ($batch_sql as $row) {
		$batch_color_data[$row[csf("roll_id")]] = $row[csf("color_id")];
	}
	unset($batch_sql);

	$barcode_cond_prod = "";
	if ($txt_barcode_no != "") $barcode_cond_prod = " and barcode_no='$txt_barcode_no'";
	$position_sql = sql_select("select roll_id, po_breakdown_id,
		max(case when entry_form=56 then roll_id else 0 end) as grey_delivery,
		max(case when entry_form=56 then insert_date else null end) as grey_delivery_date,
		max(case when entry_form=58 then roll_id else 0 end) as grey_rcv_store,
		sum(case when entry_form=58 then qnty else 0 end) as grey_rcv_qnty,
		max(case when entry_form=58 then insert_date else null end) as grey_rcv_store_date,
		max(case when entry_form=61 and is_returned=0 then roll_id else 0 end) as grey_issue_batch,
		sum(case when entry_form=61 and is_returned=0 then qnty else 0 end) as batch_issue_qnty,
		max(case when entry_form=61 and is_returned=0 then insert_date else null end) as grey_issue_batch_date,
		max(case when entry_form=62 then roll_id else 0 end) as grey_rcv_batch,
		max(case when entry_form=62 then insert_date else null end) as grey_rcv_batch_date,
		max(case when entry_form=64 then roll_id else 0 end) as batch_created,
		sum(case when entry_form=64 then qnty else 0 end) as batch_create_qnty,
		max(case when entry_form=64 then insert_date else null end) as batch_created_date,
		max(case when entry_form=66 then roll_id else 0 end) as finishion,
		max(case when entry_form=66 then insert_date else null end) as finishion_date,
		sum(case when entry_form=66 then qc_pass_qnty else 0 end) as finishion_qnty,
		max(case when entry_form=67 then roll_id else 0 end) as fin_delivery,
		max(case when entry_form=67 then insert_date else null end) as fin_delivery_date,
		max(case when entry_form=68 then roll_id else 0 end) as fin_rcv_store,
		max(case when entry_form=68 then insert_date else null end) as fin_rcv_store_date,
		max(case when entry_form=71 then roll_id else 0 end) as fin_issu_cut,
		max(case when entry_form=71 then insert_date else null end) as fin_issu_cut_date,
		max(case when entry_form=72 then roll_id else 0 end) as fin_receive_cut,
		max(case when entry_form=72 then insert_date else null end) as fin_receive_cut_date
		from pro_roll_details where status_active=1 and is_deleted=0 and roll_id>0 $all_barcode_cond  group by roll_id, po_breakdown_id");

	$batch_issue_qtny_arr = $batch_creat_qnty_arr = $roll_data_arr = array();
	foreach ($position_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_delivery"] = $row[csf("grey_delivery")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_delivery_date"] = $row[csf("grey_delivery_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_store"] = $row[csf("grey_rcv_store")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_qnty"] = $row[csf("grey_rcv_qnty")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_store_date"] = $row[csf("grey_rcv_store_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_issue_batch"] = $row[csf("grey_issue_batch")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_issue_batch_date"] = $row[csf("grey_issue_batch_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch"] = $row[csf("grey_rcv_batch")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch_date"] = $row[csf("grey_rcv_batch_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["batch_created"] = $row[csf("batch_created")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["batch_created_date"] = $row[csf("batch_created_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion"] = $row[csf("finishion")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion_date"] = $row[csf("finishion_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["finishion_qnty"] = $row[csf("finishion_qnty")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_delivery"] = $row[csf("fin_delivery")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_delivery_date"] = $row[csf("fin_delivery_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_rcv_store"] = $row[csf("fin_rcv_store")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_rcv_store_date"] = $row[csf("fin_rcv_store_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_issu_cut"] = $row[csf("fin_issu_cut")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_issu_cut_date"] = $row[csf("fin_issu_cut_date")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_receive_cut"] = $row[csf("fin_receive_cut")];
		$roll_data_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]]["fin_receive_cut_date"] = $row[csf("fin_receive_cut_date")];
		$batch_issue_qtny_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]] += $row[csf("batch_issue_qnty")];
		$batch_creat_qnty_arr[$row[csf("roll_id")]][$row[csf("po_breakdown_id")]] += $row[csf("batch_create_qnty")];

		if($row[csf("grey_issue_batch_date")] != "")
		{
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["grey_issue_batch"] = $row[csf("grey_issue_batch")];
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["grey_issue_batch_date"] = $row[csf("grey_issue_batch_date")];
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["batch_issue_qnty"] += $row[csf("batch_issue_qnty")];
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["batch_create_qnty"] += $row[csf("batch_create_qnty")];
		}

		if($row[csf("grey_rcv_batch_date")] != "")
		{
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["grey_rcv_batch"] = $row[csf("grey_rcv_batch")];
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["grey_rcv_batch_date"] = $row[csf("grey_rcv_batch_date")];
		}

		if($row[csf("batch_created_date")] != "")
		{
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["batch_created"] = $row[csf("batch_created")];
			$roll_data_arr_wo_fso_ref[$row[csf("roll_id")]]["batch_created_date"] = $row[csf("batch_created_date")];
		}

	}

	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	$machine_data = array();
	foreach ($machine_sql as $row) {
		$machine_data[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_data[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_data[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$sub_process_data = array();
	$dyeing_roll_sql = sql_select("select b.roll_id from pro_fab_subprocess a, pro_batch_create_dtls b where a.batch_id=b.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.roll_id>0 and a.status_active=1 and b.status_active=1");
	foreach ($dyeing_roll_sql as $row) {
		$sub_process_data[$row[csf("roll_id")]][2] = $row[csf("roll_id")];
	}
	//production_date as end_date end_hours,end_minutes
	$sub_process_sql = sql_select("select b.roll_id,
		max(case when b.entry_page=30 then b.roll_id else 0 end) as slitting_roll,
		max(case when b.entry_page=30 then a.production_date else null end) as slitting_roll_date,
		max(case when b.entry_page=30 then a.end_hours else null end) as slt_hours,
		max(case when b.entry_page=30 then a.end_minutes else null end) as slt_minutes,
		max(case when b.entry_page=31 then b.roll_id else 0 end) as drying_roll,
		max(case when b.entry_page=31 then a.production_date else null end) as drying_roll_date,
		max(case when b.entry_page=31 then a.end_hours else null end) as dry_hours,
		max(case when b.entry_page=31 then a.end_minutes else null end) as dry_minutes,

		max(case when b.entry_page=32 then b.roll_id else 0 end) as heat_roll,
		max(case when b.entry_page=32 then a.production_date else null end) as heat_roll_date,
		max(case when b.entry_page=32 then a.end_hours else null end) as heat_hours,
		max(case when b.entry_page=32 then a.end_minutes else null end) as heat_minutes,

		max(case when b.entry_page=33 then b.roll_id else 0 end) as compaction_roll,
		max(case when b.entry_page=33 then a.production_date else null end) as compaction_roll_date,
		max(case when b.entry_page=33 then a.end_hours else null end) as com_hours,
		max(case when b.entry_page=33 then a.end_minutes else null end) as com_minutes,

		max(case when b.entry_page=34 then b.roll_id else 0 end) as special_finish_roll,
		max(case when b.entry_page=34 then a.production_date else null end) as special_finish_roll_date,
		max(case when b.entry_page=34 then a.end_hours else null end) as sfin_hours,
		max(case when b.entry_page=34 then a.end_minutes else null end) as sfin_minutes,

		max(case when b.entry_page=35 and a.load_unload_id=2 then b.roll_id else 0 end) as dyeing_roll,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.production_date else null end) as dyeing_roll_date,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.end_hours else null end) as dyeing_hours,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.end_minutes else null end) as dyeing_minutes,

		max(case when b.entry_page=48 then b.roll_id else 0 end) as stentering_roll,
		max(case when b.entry_page=48 then a.production_date else null end) as stentering_roll_date,
		max(case when b.entry_page=48 then a.end_hours else null end) as sten_hours,
		max(case when b.entry_page=48 then a.end_minutes else null end) as sten_minutes

		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and b.entry_page in(30,31,32,33,35,34,48) and b.roll_id>0 and b.status_active=1 and b.is_deleted=0 group by b.roll_id");

	$sub_process_data = $sub_process_dateTime = array();
	foreach ($sub_process_sql as $row) {
		$sub_process_data[$row[csf("roll_id")]][2] = $row[csf("dyeing_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][2] = $row[csf("dyeing_roll_date")] . '<br>' . $row[csf("dyeing_hours")] . ':' . $row[csf("dyeing_minutes")];

		$sub_process_data[$row[csf("roll_id")]][1] = $row[csf("heat_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][1] = $row[csf("heat_roll_date")] . '<br>' . $row[csf("heat_hours")] . ':' . $row[csf("heat_minutes")];
		$sub_process_data[$row[csf("roll_id")]][3] = $row[csf("slitting_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][3] = $row[csf("slitting_roll_date")] . '<br>' . $row[csf("slt_hours")] . ':' . $row[csf("slt_minutes")];
		$sub_process_data[$row[csf("roll_id")]][4] = $row[csf("stentering_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][4] = $row[csf("stentering_roll_date")] . '<br>' . $row[csf("sten_hours")] . ':' . $row[csf("sten_minutes")];
		$sub_process_data[$row[csf("roll_id")]][5] = $row[csf("drying_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][5] = $row[csf("drying_roll_date")] . '<br>' . $row[csf("dry_hours")] . ':' . $row[csf("dry_minutes")];
		$sub_process_data[$row[csf("roll_id")]][6] = $row[csf("special_finish_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][6] = $row[csf("special_finish_roll_date")] . '<br>' . $row[csf("sfin_hours")] . ':' . $row[csf("sfin_hours")];
		$sub_process_data[$row[csf("roll_id")]][7] = $row[csf("compaction_roll")];
		$sub_process_dateTime[$row[csf("roll_id")]][7] = $row[csf("compaction_roll_date")] . '<br>' . $row[csf("com_hours")] . ':' . $row[csf("com_hours")];
	}

	$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent, a.id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ");
	foreach ($determination_sql as $val)
	{

		if($val[csf("construction")] != "")
		{
			$fabric_desc_arr[$val[csf("id")]] = $val[csf("construction")]. ", ";
		}
		$fabric_desc_arr[$val[csf("id")]] .= $composition[$val[csf("copmposition_id")]]. " " .$val[csf("percent")] . "% ";
	}

	foreach ($nameArray as $row) {
		if($row[csf("basis")] == 4)
		{
			$fabric_description =  $fabric_desc_arr[$row[csf("fabric_desc")]];
		}else{
			$fabric_description =  $row[csf("fabric_desc")];
		}
		$roll_id = ($row[csf("roll_split_from")]>0)?$row[csf("roll_split_from")]:$row[csf("roll_id")];

		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["grey_qnty"] += $row[csf("grey_qnty")];
		//$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_issue_qtny"] += $batch_issue_qtny_arr[$roll_id][$row[csf("id")]];
		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_issue_qtny"] += $roll_data_arr_wo_fso_ref[$roll_id]["batch_issue_qnty"];

		//$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_creat_qnty"] += $batch_creat_qnty_arr[$roll_id][$row[csf("id")]];

		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_creat_qnty"] += $roll_data_arr_wo_fso_ref[$roll_id]["batch_create_qnty"];




		$garph_data[1] += $row[csf("grey_qnty")];
		$garph_caption[1] = "Grey Wgt";

		if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_delivery"] > 0) {
			$garph_data[2] += $row[csf("grey_qnty")];
			$garph_caption[2] = "Delv. To Store";
		} else {
			$garph_data[2] += 0;
			$garph_caption[2] = "Delv. To Store";
		}
		if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_store"] > 0) {
			$garph_data[3] += $roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_qnty"];
			//$garph_data[3] += $row[csf("grey_qnty")];
			$garph_caption[3] = "Recv. by Store";
		} else {
			$garph_data[3] += 0;
			$garph_caption[3] = "Recv. by Store";
		}

		/*if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_issue_batch"] > 0) {
			$garph_data[4] += $row[csf("grey_qnty")];
			$garph_caption[4] = "Issue to Batch";
		} else {
			$garph_data[4] += 0;
			$garph_caption[4] = "Issue to Batch";
		}*/

		if ($roll_data_arr_wo_fso_ref[$roll_id]["grey_issue_batch"] > 0) {
			$garph_data[4] += $row[csf("grey_qnty")];
			$garph_caption[4] = "Issue to Batch";
		} else {
			$garph_data[4] += 0;
			$garph_caption[4] = "Issue to Batch";
		}

		if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
			/*if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_batch"] > 0) {
				$garph_data[5] += $row[csf("grey_qnty")];
				$garph_caption[5] = "Recv. by Batch";
			} else {
				$garph_data[5] += 0;
				$garph_caption[5] = "Recv. by Batch";
			}
			if ($roll_data_arr[$roll_id][$row[csf("id")]]["batch_created"] > 0) {
				$garph_data[6] += $row[csf("grey_qnty")];
				$garph_caption[6] = "Batch Create";
			} else {
				$garph_data[6] += 0;
				$garph_caption[6] = "Batch Create";
			}
			*/
			if ($roll_data_arr_wo_fso_ref[$roll_id]["grey_rcv_batch"] > 0) {
				$garph_data[5] += $row[csf("grey_qnty")];
				$garph_caption[5] = "Recv. by Batch";
			} else {
				$garph_data[5] += 0;
				$garph_caption[5] = "Recv. by Batch";
			}
			if ($roll_data_arr_wo_fso_ref[$roll_id]["batch_created"] > 0) {
				$garph_data[6] += $row[csf("grey_qnty")];
				$garph_caption[6] = "Batch Create";
			} else {
				$garph_data[6] += 0;
				$garph_caption[6] = "Batch Create";
			}
		}

		$p = 6;
		if ($variable_data_arr[50]["page_upto_id"] > 0) {
			for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
				if ($sub_process_data[$roll_id][$i] > 0) {
					$p++;
					$garph_data[$p] += $row[csf("grey_qnty")];
					$garph_caption[$p] = "" . $upto_receive_batch[$i] . "";
				} else {
					$p++;
					$garph_data[$p] += 0;
					$garph_caption[$p] = "" . $upto_receive_batch[$i] . "";
				}

			}
		}

	}

	$div_width = 1568;
	$table_width = 1550;
	$coll_span = 15;
	if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
		$div_width = $div_width + 140;
		$table_width = $table_width + 140;
		$coll_span = $coll_span + 2;
	}
	if ($variable_data_arr[50]["page_upto_id"] > 0) {
		$div_width = $div_width + (70 * $variable_data_arr[50]["page_upto_id"]);
		$table_width = $table_width + (70 * $variable_data_arr[50]["page_upto_id"]);
		$coll_span = $coll_span + $variable_data_arr[50]["page_upto_id"];
	}
	if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
		$div_width = $div_width + 570;
		$table_width = $table_width + 570;
		$coll_span = $coll_span + 8;
	}

	ob_start();
	?>
	<style type="text/css">
		.font_yellow_color { color: #fff;}
	</style>
	<div style="width:<? echo $div_width; ?>px;">
		<fieldset style="width:<? echo $div_width; ?>px;">
			<p style="color:red; font-size:18px; font-weight:bold; text-align:left; padding-left:10px;">Note : Column
			Total Will Not Recalculate With html Filter.</p>
			<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
				<tr>
					<td align="center" width="100%" colspan="<? echo $coll_span; ?>"
						class="form_caption"><? echo $report_title; ?></td>
					</tr>
				</table>
				<table border="0" width="<? echo $table_width; ?>" align="left">
					<tr>
						<td width="45%">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"
							align="left">
							<thead>
								<tr>
									<th width="16%">Color Range</th>
									<th width="16%">Body Part</th>
									<th width="17%">Fabric Description</th>
									<th width="10%">GSM</th>
									<th width="10%">Dia</th>
									<th width="10%">Produced</th>
									<th width="10%">Issued To Batch</th>
									<th>Batch Done</th>
								</tr>
							</thead>
							<tbody>
								<?
								$j = 1;
								foreach ($summary_data as $color_range_id => $color_range_val) {
									foreach ($color_range_val as $body_part_id => $body_part_val) {
										foreach ($body_part_val as $febric_des_id => $febric_des_val) {
											foreach ($febric_des_val as $gsm => $gsm_val) {
												foreach ($gsm_val as $dia => $dia_val) {
													if ($j % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>">
														<td><p><? echo $color_range[$color_range_id]; ?>&nbsp;</p></td>
														<td><p><? echo $body_part[$body_part_id]; ?>&nbsp;</p></td>
														<td><p><? echo $febric_des_id; ?>&nbsp;</p></td>
														<td align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
														<td align="center"><p><? echo $dia; ?>&nbsp;</p></td>
														<td align="right"><? echo number_format($dia_val["grey_qnty"], 2); ?></td>
														<td align="right"><? echo number_format($dia_val["batch_issue_qtny"], 2); ?></td>
														<td align="right"><? echo number_format($dia_val["batch_creat_qnty"], 2); ?></td>
													</tr>
													<?
													$summ_tot_grey_qnty += $dia_val["grey_qnty"];
													$summ_tot_batch_issue_qtny += $dia_val["batch_issue_qtny"];
													$summ_tot_batch_creat_qnty += $dia_val["batch_creat_qnty"];
													$j++;
												}
											}
										}

									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="5" align="right">Summary Total:</th>
									<th align="right"><? echo number_format($summ_tot_grey_qnty, 2); ?></th>
									<th align="right"><? echo number_format($summ_tot_batch_issue_qtny, 2); ?></th>
									<th align="right"><? echo number_format($summ_tot_batch_creat_qnty, 2); ?></th>
								</tr>
								<tr>
									<th colspan="5" align="right">Pending:</th>
									<th align="right"><? $pending_issue_batch = $summ_tot_grey_qnty - $summ_tot_batch_issue_qtny; ?>
									&nbsp;</th>
									<th align="right"><? $pending_tot_batch_creat_qnty = $summ_tot_batch_issue_qtny - $summ_tot_batch_creat_qnty;
									echo number_format($pending_issue_batch, 2); ?></th>
									<th align="right"><? echo number_format($pending_tot_batch_creat_qnty, 2); ?></th>
								</tr>
								<tr>
									<th colspan="5" align="right">Pending%:</th>
									<th align="right"><? $pending_issue_batch_percent = (($pending_issue_batch / $summ_tot_grey_qnty) * 100); ?>
									&nbsp;</th>
									<th align="right"><? $summ_tot_batch_creat_percent = (($pending_tot_batch_creat_qnty / $summ_tot_batch_issue_qtny) * 100);
									echo number_format($pending_issue_batch_percent, 2); ?></th>
									<th align="right"><? echo number_format($summ_tot_batch_creat_percent, 2); ?></th>
								</tr>
							</tfoot>
						</table>
					</td>
					<td width="5%"></td>
					<td valign="top" width="700">
						<canvas id="canvas3" height="350" width="700"></canvas>
					</td>
					<td></td>
				</tr>
			</table>

			<table border="0" width="<? echo $table_width; ?>" align="left">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">PO Buyer</th>
						<th width="120">FSO No & Sty Ref</th>
						<th width="120">Booking No</th>
						<th width="60">Roll No</th>
						<th width="100">Barcode No</th>
						<th width="100">Color Range</th>
						<th width="100">Body Part</th>
						<th width="100">Color</th>
						<th width="170">Fabric Description</th>
						<th width="60">GSM</th>
						<th width="60">Dia</th>
						<th width="60">Stich Lenth</th>
						<th width="60">Machine Dia</th>
						<th width="60">Guage</th>
						<th width="80">Grey Wgt.</th>
						<th width="70">Delv. To Store</th>
						<th width="70">Recv. by Store</th>
						<th width="70">Issue to Batch</th>
						<?
						if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
							?>
							<th width="70">Recv. by Batch</th>
							<th width="70">Batch Create</th>
							<?
						}
						if ($variable_data_arr[50]["page_upto_id"] > 0) {
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								?>
								<th width="70"><p><? echo $upto_receive_batch[$i]; ?></p></th>
								<?
							}
						}
						if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
							?>
							<th width="70">Finish</th>
							<th width="70">Finish Wgt.</th>
							<th width="70">Process Loss</th>
							<th width="70">Delv. To Store</th>
							<th width="70">Recv. by Store</th>
							<th width="70">Issue to Cut</th>
							<th>Recv. by Cut</th>

							<?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
					class="rpt_table" id="table_body">
					<?
					$m = 1;
					$tot_grey_delivery = $tot_grey_rcv_store = $tot_grey_issue_batch = $tot_grey_rcv_batch = $tot_batch_created = $tot_fin_delivery = $tot_fin_rcv_store = $tot_fin_issu_cut = 0;
					foreach ($nameArray as $row) {
						if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$grey_delivery_bgcolor = $grey_rcv_store_bgcolor = $grey_issue_batch_bgcolor = $grey_rcv_batch_bgcolor = $batch_created_bgcolor = $finishion_bgcolor = $fin_delivery_bgcolor = $fin_rcv_store_bgcolor = $fin_rcv_store_bgcolor = '';

						$grey_delivery_date = $grey_rcv_store_date = $grey_issue_batch_day = $grey_rcv_batch_day = $batch_created_day = $finishion_day = $fin_delivery_day = $fin_rcv_store_day = $fin_issu_cut_day = "";

						$roll_id = ($row[csf("roll_split_from")]>0)?$row[csf("roll_split_from")]:$row[csf("roll_id")];

						if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_delivery"] > 0) {
							$grey_delivery_bgcolor = 'bgcolor="green"';
							$grey_delivery_date = $roll_data_arr[$roll_id][$row[csf("id")]]["grey_delivery_date"];
							$tot_grey_delivery += $row[csf("grey_qnty")];
						}

						if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_store"] > 0) {
							$grey_rcv_store_bgcolor = 'bgcolor="green"';
							$grey_rcv_store_date = $roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_store_date"];
							$tot_grey_rcv_store += $roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_qnty"];
							//$tot_grey_rcv_store += $row[csf("grey_qnty")];
						}

						/*if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_issue_batch"] > 0) {
							$grey_issue_batch_bgcolor = 'bgcolor="green"';
							$grey_issue_batch_date = $roll_data_arr[$roll_id][$row[csf("id")]]["grey_issue_batch_date"];
							$tot_grey_issue_batch += $row[csf("grey_qnty")];
						}*/
						$grey_issue_batch_date="";
						if ($roll_data_arr_wo_fso_ref[$roll_id]["grey_issue_batch"] > 0) {

							$grey_issue_batch_bgcolor = 'bgcolor="green"';
							$grey_issue_batch_date = $roll_data_arr_wo_fso_ref[$roll_id]["grey_issue_batch_date"];
							$tot_grey_issue_batch += $row[csf("grey_qnty")];
							//echo $grey_issue_batch_date."==".$row[csf("barcode_no")]."<br>";
						}

						/*if ($roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_batch"] > 0) {
							$grey_rcv_batch_bgcolor = 'bgcolor="green"';
							$grey_rcv_batch_date = $roll_data_arr[$roll_id][$row[csf("id")]]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch += $row[csf("grey_qnty")];
						}*/
						$grey_rcv_batch_date="";
						if ($roll_data_arr_wo_fso_ref[$roll_id]["grey_rcv_batch"] > 0) {
							$grey_rcv_batch_bgcolor = 'bgcolor="green"';
							$grey_rcv_batch_date = $roll_data_arr_wo_fso_ref[$roll_id]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch += $row[csf("grey_qnty")];
						}



						/*if ($roll_data_arr[$roll_id][$row[csf("id")]]["batch_created"] > 0) {
							$batch_created_bgcolor = 'bgcolor="green"';
							$batch_created_date = $roll_data_arr[$roll_id][$row[csf("id")]]["batch_created_date"];
							$tot_batch_created += $row[csf("grey_qnty")];
						}*/
						$batch_created_date="";
						if ($roll_data_arr_wo_fso_ref[$roll_id]["batch_created"] > 0) {
							$batch_created_bgcolor = 'bgcolor="green"';
							$batch_created_date = $roll_data_arr_wo_fso_ref[$roll_id]["batch_created_date"];
							$tot_batch_created += $row[csf("grey_qnty")];
						}

						$sub_process_bgcolor = $sub_process_day = array();
						if ($variable_data_arr[50]["page_upto_id"] > 0) {
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								if ($sub_process_data[$roll_id][$i] > 0) {
									$sub_process_bgcolor[$i] = 'bgcolor="green"';
									$sub_process_day[$i] = $sub_process_dateTime[$roll_id][$i];
									$tot_sub_process[$i] += $row[csf("grey_qnty")];
								}
							}
						}
						$color_names = '';
						$colors = explode(",", $row[csf("color_id")]);
						foreach ($colors as $color) {
							$color_names .= $color_library[$color] . ",";
						}

						if ($row[csf("entry_form")] == 2) $roll_entry_form = "Production"; else $roll_entry_form = "Receive";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
							<td width="30" align="center"><? echo $m; ?></td>
							<td width="100" align="center"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $row[csf("job_no")] . "<br>" . $row[csf("style_ref_no")]; ?></p></td>
							<td width="120"><p><? echo $row[csf("sales_booking_no")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><a href="##" onClick="openmypage_popup('<? echo $row[csf("barcode_no")];//$roll_id;?>','roll_popup')"><? echo $row[csf("roll_no")]; ?></a></p></td>
								<td width="100" align="center"><p><? echo $row[csf("barcode_no")]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $color_range[$row[csf("color_range_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo rtrim($color_names, ","); ?>&nbsp;</p></td>
								<td width="170" align="center"><p><? echo $fabric_description;//$row[csf("fabric_desc")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("gsm_weight")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("fabric_dia")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("stitch_length")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("machine_dia")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("machine_gg")]; ?>&nbsp;</p></td>
								<td width="80" align="right"
								title="<? echo $roll_entry_form . "**" . $roll_id . "**" . $row[csf("po_id")]; ?>"><? echo number_format($row[csf("grey_qnty")], 2);
								$total_grey_qnty += $row[csf("grey_qnty")]; ?></td>

								<td width="70" align="center" style="word-break:break-all;"
								valign="middle" <? echo $grey_delivery_bgcolor; ?>><a href="##" class="font_yellow_color" onclick="openmypage_sys_no('56','<? echo $row[csf("barcode_no")];?>')"><? echo $grey_delivery_date; ?></a></td>

								<td width="70" align="center" style="word-break:break-all;"
								valign="middle" <? echo $grey_rcv_store_bgcolor; ?>>
								<a href="##" class="font_yellow_color" onclick="openmypage_sys_no('58','<? echo $row[csf("barcode_no")];?>')"><? echo $grey_rcv_store_date; ?></a>
								</td>
								<td width="70" align="center" style="word-break:break-all;"
								valign="middle" <? echo $grey_issue_batch_bgcolor; ?>>
								<a href="##" class="font_yellow_color" onclick="openmypage_sys_no('61','<? echo $row[csf("barcode_no")];?>')"><? echo $grey_issue_batch_date; ?></a>
								</td>
								<?
								if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
									?>
									<td width="70" align="center" style="word-break:break-all;"
									valign="middle" <? echo $grey_rcv_batch_bgcolor; ?>>
									<a href="##" class="font_yellow_color" onclick="openmypage_sys_no('62','<? echo $row[csf("barcode_no")];?>')"><? echo $grey_rcv_batch_date; ?></a>
									</td>
									<td width="70" align="center" style="word-break:break-all;"
									valign="middle" <? echo $batch_created_bgcolor; ?>>
										<a href="##" class="font_yellow_color" onclick="openmypage_sys_no('64','<? echo $row[csf("barcode_no")];?>')"><? echo $batch_created_date; ?></a>
									</td>
									<?
								}

								if ($variable_data_arr[50]["page_upto_id"] > 0) {
									for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
										?>
										<td width="70" style="word-break:break-all"
										title="<? echo $row[csf("grey_qnty")]; ?>" align="center"
										valign="middle" <? echo $sub_process_bgcolor[$i]; ?> ><? echo $sub_process_day[$i]; ?></td>
										<?
									}
								}

								if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
									?>
									<td width="70" style="word-break:break-all" align="center"
									valign="middle" <? echo $finishion_bgcolor; ?>><? echo $finishion_day; ?></td>
									<td width="70"
									align="right"><? if ($roll_data_arr[$roll_id][$row[csf("po_id")]]["finishion"] > 0) echo number_format($roll_data_arr[$roll_id][$row[csf("po_id")]]["finishion_qnty"], 2);
									$total_finishing_qnty += $roll_data_arr[$roll_id][$row[csf("po_id")]]["finishion_qnty"]; ?></td>
									<td width="70" align="right">
										<?
										$processes_loss = 0;
										$processes_loss = $row[csf("grey_qnty")] - $roll_data_arr[$roll_id][$row[csf("po_id")]]["finishion_qnty"];
										if ($roll_data_arr[$roll_id][$row[csf("po_id")]]["finishion_qnty"] > 0) {
											echo number_format($processes_loss, 2);
											$total_processes_loss += $processes_loss;
										}
										?>
									</td>
									<td width="70" align="center"
									valign="right" <? echo $fin_delivery_bgcolor; ?>><? echo $fin_delivery_day; ?></td>
									<td width="70" align="center"
									valign="right" <? echo $fin_rcv_store_bgcolor; ?>><? echo $fin_rcv_store_day; ?></td>
									<td width="70" align="center"
									valign="right" <? echo $fin_issu_cut_bgcolor; ?>><? echo $fin_issu_cut_day; ?></td>
									<td width="" align="center"
									valign="right" <? echo $fin_receive_cut_bgcolor; ?>><? echo $fin_receive_cut_day; ?></td>
									<?
								}
								?>
								<!--							<td width="70" align="center" valign="middle" <?php //echo ($finish_receive_dt != '') ? 'bgcolor="green"' : ''; ?> ><?php //echo $finish_receive_dt; ?></td>-->

							</tr>
							<?
							$m++;
						}
						?>
					</table>
				</div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
					class="rpt_table" id="rpt_table_footer" align="left">
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="170">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60" align="right">Total:</th>
							<th width="80" align="right"
							id="value_total_grey_qnty"><? echo number_format($total_grey_qnty, 2); ?></th>
							<th width="60" align="right"><? echo number_format($tot_grey_delivery, 2);
							$pending_grey_delivery = $total_grey_qnty - $tot_grey_delivery; ?></th>
							<th width="70" align="right"><? echo number_format($tot_grey_rcv_store, 2);
							$pending_grey_rcv_store = $tot_grey_delivery - $tot_grey_rcv_store; ?></th>
							<th width="70" align="right"><? echo number_format($tot_grey_issue_batch, 2);
							$pending_grey_issue_batch = $tot_grey_rcv_store - $tot_grey_issue_batch; ?></th>
							<?
							if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right"><? echo number_format($tot_grey_rcv_batch, 2);
								$pending_grey_rcv_batch = $tot_grey_issue_batch - $tot_grey_rcv_batch; ?></th>
								<th width="70" align="right"><? echo number_format($tot_batch_created, 2);
								$pending_batch_created = $tot_grey_rcv_batch - $tot_batch_created; ?></th>
								<?
							}
							if ($variable_data_arr[50]["page_upto_id"] > 0) {
								for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
									?>
									<th width="70" align="right"><? echo number_format($tot_sub_process[$i], 2); ?></th>
									<?
									if ($i == 1) {
										$pending_sub_process[$i] = $tot_batch_created - $tot_sub_process[$i];
									} else {
										$pending_sub_process[$i] = $tot_sub_process[$i - 1];
										-$tot_sub_process[$i];
									}
								}
							}

							if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right"><? echo number_format($total_finishing_qnty, 2); ?></th>
								<th width="70" align="right"><? echo number_format($total_processes_loss, 2); ?></th>
								<th width="70" align="right"><? echo number_format($tot_fin_delivery, 2); ?></th>
								<th width="70" align="right"><? echo number_format($tot_fin_rcv_store, 2); ?></th>
								<th width="70" align="right"><? echo number_format($tot_fin_issu_cut, 2); ?></th>
								<th width="" align="right"><? echo number_format($tot_fin_receive_cut, 2); ?></th>
								<?
							}
							?>
						</tr>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="170">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60" align="right">Pending:</th>
							<th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_grey_delivery, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_grey_rcv_store, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_grey_issue_batch, 2); ?></th>
							<?
							if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right"><? echo number_format($pending_grey_rcv_batch, 2); ?></th>
								<th width="70" align="right"><? echo number_format($pending_batch_created, 2); ?></th>
								<?
							}
							if ($variable_data_arr[50]["page_upto_id"] > 0) {
								for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
									?>
									<th width="70" align="right"><? echo number_format($pending_sub_process[$i], 2); ?></th>
									<?
								}
							}

							if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="" align="right">&nbsp;</th>
								<?
							}
							?>
						</tr>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="170">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60" align="right">Pending%:</th>
							<th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_delivery / $total_grey_qnty) * 100, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_rcv_store / $tot_grey_delivery) * 100, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_issue_batch / $tot_grey_rcv_store) * 100, 2); ?></th>
							<?
							if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
								?>
								<th width="70"
								align="right"><? echo number_format(($pending_grey_rcv_batch / $tot_grey_issue_batch) * 100, 2); ?></th>
								<th width="70"
								align="right"><? echo number_format(($pending_batch_created / $tot_grey_rcv_batch) * 100, 2); ?></th>
								<?
							}
							if ($variable_data_arr[50]["page_upto_id"] > 0) {
								for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
									if ($i == 1) {
										?>
										<th width="70"
										align="right"><? echo number_format(($pending_sub_process[$i] / $tot_batch_created) * 100, 2); ?></th>
										<?
									} else {
										?>
										<th width="70"
										align="right"><? echo number_format(($pending_sub_process[$i] / $tot_sub_process[$i - 1]) * 100, 2); ?></th>
										<?
									}
								}
							}

							if ($variable_data_arr[2]["fabric_roll_level"] == 1) {
								?>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="70" align="right">&nbsp;</th>
								<th width="" align="right">&nbsp;</th>
								<?
							}
							?>
						</tr>
					</tfoot>
				</table>
			</fieldset>
		</div>
		<?
		$garph_caption = json_encode($garph_caption);
		$garph_data = json_encode($garph_data);

		foreach (glob("$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}

		$name = time();
		$filename = $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		$filename = $user_id . "_" . $name . ".xls";
		echo "$total_data####$filename####$garph_caption####$garph_data";

		disconnect($con);
		exit();
}

if ($action == "report_generate--2") //For Fakir Fashion/Kaiyum, not used
{
	//entry page: pro_batch_create_mst,pro_batch_create_dtls,pro_roll_details,pro_batch_trims_dtls
	//a.entry_form=0
	//a.id as batch_id,a.batch_no,a.company_id,b.po_id as job_no,b.barcode_no
	//buyer_not,file_not,style_ref not,order_no not,internal_ref not
	//PRO_BATCH_CREATE_MST a,PRO_BATCH_CREATE_DTLS


	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_file_no = str_replace("'", "", $txt_file_no);
	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_style_ref_no = str_replace("'", "", $txt_style_ref_no);
	$txt_order_no = str_replace("'", "", $txt_order_no);
	$txt_inter_ref = str_replace("'", "", $txt_inter_ref);
	$txt_barcode_no = str_replace("'", "", $txt_barcode_no);
	$hdn_batch_no = str_replace("'", "", $hdn_batch_no);
	$txt_batch_no = str_replace("'", "", $txt_batch_no);

	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$sql_cond = "";
	if ($txt_file_no != "") $sql_cond = " and b.file_no='$txt_file_no'";
	if ($txt_job_no != "") $sql_cond .= " and b.job_no_mst like '%$txt_job_no%'";
	if ($txt_order_no != "") $sql_cond .= " and b.po_number like '%$txt_order_no%'";
	if ($txt_inter_ref != "") $sql_cond .= " and b.grouping='$txt_inter_ref'";
	$bar_code_cond = "";
	if ($txt_barcode_no != "") $bar_code_cond = " and a.barcode_no='$txt_barcode_no'";
	$style_ref_cond = "";
	if ($txt_style_ref_no != "") $style_ref_cond = " and c.style_ref_no='$txt_style_ref_no'";


	$hdn_batch_id_cond = "";
	$txt_batch_no_cond = "";
	if ($hdn_batch_no != "") $hdn_batch_id_cond = " and a.id='$hdn_batch_no'";
	if ($txt_batch_no != "") $txt_batch_no_cond = " and a.batch_no like '%$txt_batch_no%'";

	//for not batch list
	if ($hdn_batch_no == "") {
		$batch_no_cond_not = "";
	} else {
		$batch_no_cond_not = " and a.id !='$hdn_batch_no'";
	}
	if ($txt_batch_no == "") {
		$txt_batch_no_cond_not = "";
	} else {
		$txt_batch_no_cond_not = " and a.batch_no !='$txt_batch_no'";
	}


	$machine_name = array();
	$machine_data = sql_select("select id,dia_width, gauge from lib_machine_name");
	foreach ($machine_data as $row) {
		$machine_name[$row[csf("id")]]["mac_dia"] = $row[csf("dia_width")];
		$machine_name[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}


	//,g.gsm,g.stitch_length,g.machine_no_id
	echo $sql = "select distinct a.batch_no,a.color_id,b.barcode_no,b.body_part_id,b.item_description,b.fin_dia,e.buyer_name,e.job_no,f.po_number
	from pro_batch_create_mst a,pro_batch_create_dtls b,pro_roll_details c,wo_po_details_master e,wo_po_break_down f,pro_grey_prod_entry_dtls g
	where a.id in(1421,1624) and   a.id=b.mst_id and a.id=c.mst_id  and a.entry_form=0 and b.barcode_no is not NULL and b.barcode_no != 0 and b.po_id=f.id and e.job_no=f.job_no_mst and c.dtls_id=g.id  and a.status_active=1 and a.is_deleted=0 $hdn_batch_id_cond  $txt_batch_no_cond
	group by a.batch_no,a.color_id,b.barcode_no,b.body_part_id,b.item_description,b.fin_dia,e.buyer_name,e.job_no,f.po_number order by a.batch_no ASC";

	/*$sql="select p.color_range_id, p.body_part_id, a.barcode_no, p.febric_description_id, p.gsm, p.width as dia, p.stitch_length, p.machine_no_id,a.batch_no, a.id as roll_id, a.roll_no, a.entry_form, a.insert_date, a.qnty as grey_qnty,b.id as po_id, b.po_number, b.file_no, b.grouping, c.id as job_id, c.buyer_name, c.job_no_prefix_num, c.job_no, c.style_ref_no, b.grouping,(select max(d.insert_date) from pro_fab_subprocess_dtls d where a.id = d.roll_id) finishing_start
	from pro_grey_prod_entry_dtls p, pro_roll_details a, wo_po_break_down b, wo_po_details_master c
	where p.id=a.dtls_id and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.entry_form in(2,22) and a.roll_id=0 and a.status_active=1 and a.is_deleted=0 and c.company_name=$company_name  $sql_cond $bar_code_cond $style_ref_cond $batch_no_cond order by c.id, b.id, a.id,p.body_part_id,p.febric_description_id,a.roll_no";*/
	$nameArray = sql_select($sql);

	foreach ($nameArray as $row) {
		$dataArr_1[] = $row;
		$key_1 = $row[csf("buyer_name")] . $row[csf("job_no")] . $row[csf("po_number")] . $row[csf("body_part_id")] . $row[csf("item_description")];
		$gData_1[$key_1] += 1;
	}
	foreach ($nameArray as $row) {
		$dataArr_2[] = $row;
		$key_2 = $row[csf("color_id")] . $row[csf("batch_no")];
		$gData_2[$key_2] += 1;
	}

	/*	foreach ($nameArray as $row)
	{
		$dataArr_3[]=$row;
		$key_3=$row[csf("gsm")].$row[csf("fin_dia")].$row[csf("stitch_length")];
		$gData_3[$key_3]+=1;
	}
		*/
	ob_start();
	?>


	<div style="float:left; margin-left:100px;">
		<table class="rpt_table" border="1" rules="all">
			<thead>
				<tr>
					<th colspan="3" align="center">Summary</th>
				</tr>
				<tr>
					<th>Total Roll</th>
					<th>Total Batch Created</th>
					<th>%</th>
				</tr>
			</thead>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<div style="width:2510px; float:left;">
		<fieldset>
			<legend>Batch Wise</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2500" class="rpt_table"
			style="float:left;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">Buyer</th>
					<th width="120">Job No</th>
					<th width="120">Order No</th>
					<th width="120">Body Part</th>
					<th width="200">Fabric Description</th>
					<th width="60">Color</th>
					<th width="60">Batch No</th>
					<th width="100">Barcode No</th>
					<th width="60">GSM</th>
					<th width="60">Finish Dia</th>
					<th width="60">Stich Lenth</th>
					<th width="60">Machine Dia</th>
					<th width="60">Guage</th>
					<th width="100">Batch Create</th>
					<th width="60">Dyeing Start</th>
					<th width="60">Dyeing End</th>
					<th width="60">Slitting Squeezing</th>
					<th width="60">Stentering</th>
					<th width="100">Drying</th>
					<th width="60">Special Finish</th>
					<th width="60">Compacting</th>
					<th width="60">Finish</th>
					<th width="60">Finish Wgt</th>
					<th width="100">Process Loss</th>
					<th width="100">Finish Fab.Delv. To Store</th>
					<th width="60">Recv. by Store</th>
					<th width="60">Issue to Cut</th>
					<th width="60">Recv. by Cut</th>
					<th>Cutted</th>
				</tr>
			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2500" class="rpt_table" id="table_body"
		style="float:left;">

		<?
				//echo $sql;

		$m = 1;
				//$tot_grey_delivery=$tot_grey_rcv_store=$tot_grey_issue_batch=$tot_grey_rcv_batch=$tot_batch_created=$tot_fin_delivery=$tot_fin_rcv_store=$tot_fin_issu_cut=0;
		foreach ($nameArray as $row) {

			?>

			<tr bgcolor="<? echo $bgcolor; ?>"
				onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">


				<?
				$groupData_1 = $row[csf("buyer_name")] . $row[csf("job_no")] . $row[csf("po_number")] . $row[csf("body_part_id")] . $row[csf("item_description")];
				if (!in_array($groupData_1, $date_array_1)) {
					?>
					<td width="30" rowspan="<? echo $gData_1[$groupData_1]; ?>"
						align="center"><? echo $m; ?></td>
						<td width="120"
						rowspan="<? echo $gData_1[$groupData_1]; ?>"><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
						<td width="120"
						rowspan="<? echo $gData_1[$groupData_1]; ?>"><? echo $row[csf("job_no")]; ?></td>
						<td width="120"
						rowspan="<? echo $gData_1[$groupData_1]; ?>"><? echo $row[csf("po_number")]; ?></td>
						<td width="120"
						rowspan="<? echo $gData_1[$groupData_1]; ?>"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
						<td width="200"
						rowspan="<? echo $gData_1[$groupData_1]; ?>"><? echo $row[csf("item_description")]; ?></td>

						<?
						$date_array_1[] = $groupData_1;
							//$m++;
					}
					?>

					<?
					$groupData_2 = $row[csf("color_id")] . $row[csf("batch_no")];
					if (!in_array($groupData_2, $date_array_2)) {
						?>

						<td width="60"
						rowspan="<? echo $gData_2[$groupData_2]; ?>"><? echo $color_library[$row[csf("color_id")]]; ?></td>
						<td width="60"
						rowspan="<? echo $gData_2[$groupData_2]; ?>"><? echo $row[csf("batch_no")]; ?></td>

						<?
						$date_array_2[] = $groupData_2;
					}
					?>

					<td width="100"><? echo $row[csf("barcode_no")]; ?></td>

					<td width="60"><? echo $row[csf("gsm")]; ?></td>
					<td width="60"><? echo $row[csf("fin_dia")]; ?></td>
					<td width="60"><? echo $row[csf("stitch_length")]; ?></td>

					<td width="60"><? echo $machine_name[$row[csf("machine_no_id")]]["mac_dia"]; ?>    </td>
					<td width="60"><? echo $machine_name[$row[csf("machine_no_id")]]["gauge"]; ?></td>

					<td width="100">Batch Create</td>
					<td width="60">Dyeing Start</td>
					<td width="60">Dyeing End</td>
					<td width="60">Slitting Squeezing</td>
					<td width="60">Stentering</td>
					<td width="100">Drying</td>
					<td width="60">Special Finish</td>
					<td width="60">Compacting</td>
					<td width="60">Finish</td>
					<td width="60">Finish Wgt</td>
					<td width="100">Process Loss</td>
					<td width="100">Finish Fab.Delv. To Store</td>
					<td width="60">Recv. by Store</td>
					<td width="60">Issue to Cut</td>
					<td width="60">Recv. by Cut</td>
					<td>Cutted</td>
				</tr>
				<?
				$m++;
			}
			?>

		</table>
	</fieldset>
</div>

<!-- No Batch -->
</fieldset>
</div>
<div style="width:1600px; float:left;">
	<fieldset>
		<legend>Not Batch</legend>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table"
		style="float:left;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="200">Buyer</th>
				<th width="150">Job</th>
				<th width="150">Order</th>
				<th width="150">Body Part</th>
				<th width="250">Fabric Description</th>
				<th width="150">Barcode No</th>
				<th width="100">GSM</th>
				<th width="100">Finish Dia</th>
				<th width="100">Stich Length</th>
				<th width="100">Machine Dia</th>
				<th width="100">Guage</th>
			</tr>
		</thead>
	</table>
	<?
			//wo_po_details_master a,wo_po_break_down b
			//a.buyer_name,a.style_ref_no,a.job_no,
			// and b.po_id=c.id $txt_batch_no_cond $batch_no_cond
	$po_batch_id = "";
	$batch_po_id = sql_select("select po_id from pro_batch_create_dtls where is_deleted=0 and status_active=1");
	foreach ($batch_po_id as $row) {
		$po_batch_id .= $row[csf("po_id")] . ',';
	}
	$po_batch_idd = rtrim($po_batch_id, ',');
			//echo $po_batch_idd;

	$po_id = array_chunk(array_unique(explode(",", $po_batch_idd)), 1000, true);
	$order_cond = "";
	$ji = 0;
	foreach ($po_id as $key => $value) {
		if ($ji == 0) {
			$order_cond = " and b.id  not in(" . implode(",", $value) . ")";

		} else {
			$order_cond .= " or b.id  not in(" . implode(",", $value) . ")";

		}
		$ji++;
	}

	$btch_crt_barcodes = "";
			//$btch_crt_po="";
	$btch_crt_barcodes_arr = array();
	$sql_btch_crt_barcode = sql_select("select b.po_id,b.barcode_no from  pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and a.entry_form=0 and b.barcode_no is not NULL and  b.barcode_no!=0 group by b.po_id,b.barcode_no");
	foreach ($sql_btch_crt_barcode as $row) {
				//if($row[csf("barcode_no")]!=0 || $row[csf("barcode_no")]==""){
		if ($btch_crt_barcodes == "") $btch_crt_barcodes = $row[csf("barcode_no")]; else  $btch_crt_barcodes .= "," . $row[csf("barcode_no")];
				//$btch_crt_barcodes_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
				//}
				//$btch_crt_po.=$row[csf("po_id")].',';
	}
			//echo $btch_crt_barcodes;
			//die;
			//$btch_crt_barcode=rtrim($btch_crt_barcode,',');
			//$btch_crt_po=rtrim($btch_crt_po,',');


	$roll_barcode = "";
	$roll_po = "";
	$sql_roll_barcode = sql_select("select a.po_breakdown_id,a.barcode_no from pro_roll_details a where a.is_deleted=0 and a.status_active=1 group by a.po_breakdown_id,a.barcode_no");
	foreach ($sql_roll_barcode as $row) {
		$roll_barcode .= $row[csf("barcode_no")] . ',';
		$roll_po .= $row[csf("po_breakdown_id")] . ',';
	}
	$roll_barcode = rtrim($roll_barcode, ',');
	$roll_po = rtrim($btch_crt_po, ',');

			//array chunk for batch barcode
	$barcode_chnk = array_chunk(array_unique(explode(",", $btch_crt_barcodes)), 1000, true);
	$barcode_cond = "";
	$ji = 0;
	foreach ($barcode_chnk as $key => $value) {
		if ($ji == 0) {
			$barcode_cond = " and a.barcode_no  not in(" . implode(",", $value) . ")";

		} else {
			$barcode_cond .= " or a.barcode_no  not in(" . implode(",", $value) . ")";

		}
		$ji++;
	}
	$sql_roll_barcode = "select a.po_breakdown_id as job_id,a.barcode_no,b.job_no_mst,b.po_number,c.buyer_name,d.body_part_id,d.febric_description_id,d.gsm,d.width as dia,d.stitch_length, d.machine_no_id from pro_roll_details a,wo_po_break_down b,wo_po_details_master c,pro_grey_prod_entry_dtls d where a.company_id=$company_name $barcode_cond and a.is_deleted=0 and a.status_active=1 and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.dtls_id=d.id group by a.barcode_no,a.po_breakdown_id,b.job_no_mst,b.po_number,c.buyer_name,d.body_part_id,d.gsm,d.width,d.stitch_length,d.machine_no_id,d.febric_description_id";

			/* $sql="select p.color_range_id, p.body_part_id, a.barcode_no, p.febric_description_id, p.gsm, p.width as dia, p.stitch_length, p.machine_no_id,a.batch_no, a.id as roll_id, a.roll_no, a.entry_form, a.insert_date, a.qnty as grey_qnty,b.id as po_id, b.po_number, b.file_no, b.grouping, c.id as job_id, c.buyer_name, c.job_no_prefix_num, c.job_no, c.style_ref_no, b.grouping,(select max(d.insert_date) from pro_fab_subprocess_dtls d where a.id = d.roll_id) finishing_start
	from pro_grey_prod_entry_dtls p, pro_roll_details a, wo_po_break_down b, wo_po_details_master c
	where p.id=a.dtls_id and a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.entry_form in(2,22) and a.roll_id=0 and a.status_active=1 and a.is_deleted=0 and c.company_name=$company_name  $sql_cond $bar_code_cond $style_ref_cond $batch_no_cond order by c.id, b.id, a.id,p.body_part_id,p.febric_description_id,a.roll_no";  */

			//die;
			//$sql_batch="select a.buyer_name,a.job_no,b.po_number,b.id from  wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $order_cond  and a.company_name=1 group by a.buyer_name,a.job_no,b.po_number,b.id";
			//die;
			//$sql_batch="select a.id as batch_id,a.batch_no,a.company_id,a.color_id,b.po_id as job_no,b.barcode_no,b.body_part_id,b.fin_dia,b.item_description,c.job_no_mst,c.po_number from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c where a.id=b.mst_id and b.po_id=c.id $txt_batch_no_cond $batch_no_cond";
	$sql_not_batch_arr = sql_select($sql_roll_barcode);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="table_body"
	style="float:left;">
	<?
	$i = 1;
	foreach ($sql_not_batch_arr as $roww) {
		if ($i % 2 == 0)
			$bgcolor = "#E9F3FF";
		else
			$bgcolor = "#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td width="30"><? echo $i; ?></td>
			<td width="200"><? echo $buyer_arr[$roww[csf("buyer_name")]]; ?>  </td>
			<td width="150"><? echo $roww[csf("job_no_mst")]; ?> </td>
			<td width="150"><? echo $roww[csf("po_number")]; ?> </td>
			<td width="150"><? echo $body_part[$roww[csf("body_part_id")]]; ?> </td>
			<td width="250"><? echo $composition_arr[$roww[csf("febric_description_id")]]; ?> </td>
			<td width="150" align="center"><? echo $roww[csf("barcode_no")]; ?>  </td>
			<td width="100"><? echo $roww[csf("gsm")]; ?> </td>
			<td width="100"><? echo $roww[csf("dia")]; ?> </td>
			<td width="100"><? echo $roww[csf("stitch_length")]; ?>  </td>
			<td width="100"><? echo $machine_data[$roww[csf("machine_no_id")]]["dia_width"]; ?> </td>
			<td width="100"><? echo $machine_data[$roww[csf("machine_no_id")]]["gauge"]; ?></td>
		</tr>
		<?
		$i++;
	}
	?>
</table>
</fieldset>
</div>
<?

	//echo "<pre>";print_r($garph_caption);
	//echo "<pre>";print_r($garph_data);die;


foreach (glob("$user_id*.xls") as $filename) {
	if (@filemtime($filename) < (time() - $seconds_old))
		@unlink($filename);
}
	//---------end------------//
$name = time();
$filename = $user_id . "_" . $name . ".xls";
$create_new_doc = fopen($filename, 'w');
$is_created = fwrite($create_new_doc, ob_get_contents());
$filename = $user_id . "_" . $name . ".xls";
echo "$total_data####$filename####$garph_caption####$garph_data";

disconnect($con);
exit();
}

if ($action == "report_generate2") //For Fakir Fashion //Aziz
{
//entry page: pro_batch_create_mst,pro_batch_create_dtls,pro_roll_details,pro_batch_trims_dtls
//a.entry_form=0
//a.id as batch_id,a.batch_no,a.company_id,b.po_id as job_no,b.barcode_no
//buyer_not,file_not,style_ref not,order_no not,internal_ref not
//PRO_BATCH_CREATE_MST a,PRO_BATCH_CREATE_DTLS

	extract($_REQUEST);
	$company_name = str_replace("'", "", $cbo_company_name);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	$txt_file_no = str_replace("'", "", $txt_file_no);
	$txt_job_no = str_replace("'", "", $txt_job_no);
	$txt_style_ref_no = str_replace("'", "", $txt_style_ref_no);
	$txt_order_no = str_replace("'", "", $txt_order_no);
	$txt_inter_ref = str_replace("'", "", $txt_inter_ref);
	$txt_barcode_no = str_replace("'", "", $txt_barcode_no);
	$hdn_batch_no = str_replace("'", "", $hdn_batch_no);
	$txt_batch_no = str_replace("'", "", $txt_batch_no);
	$cbo_year = str_replace("'", "", $cbo_year);
	if ($cbo_year != 0) {
		if ($db_type == 0) $year_cond = "and year(e.insert_date)='$cbo_year'";
		else if ($db_type == 2) $year_cond = "and to_char(e.insert_date,'YYYY')='$cbo_year'";
	}
	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$desc_library = return_library_array("select id, product_name_details from  product_details_master", "id", "product_name_details");

	$sql_cond = "";
	$sql_cond3 = "";
	if ($txt_file_no != "") $sql_cond = " and f.file_no='$txt_file_no'";
	if ($txt_file_no != "") $sql_cond3 = " and b.file_no='$txt_file_no'";
	if ($txt_job_no != "") $sql_cond .= " and e.job_no_prefix_num =$txt_job_no";
	if ($txt_job_no != "") $sql_cond3 .= " and e.job_no_prefix_num =$txt_job_no";
	if ($cbo_buyer_name != 0) $sql_cond .= " and e.buyer_name=$cbo_buyer_name";
	if ($cbo_buyer_name != 0) $sql_cond3 .= " and e.buyer_name=$cbo_buyer_name";
	if ($txt_order_no != "") $sql_cond .= " and f.po_number like '%$txt_order_no%'";
	if ($txt_inter_ref != "") $sql_cond .= " and f.grouping='$txt_inter_ref'";
	if ($txt_order_no != "") $sql_cond3 .= " and b.po_number like '%$txt_order_no%'";
	if ($txt_inter_ref != "") $sql_cond3 .= " and b.grouping='$txt_inter_ref'";

	$bar_code_cond = "";
	if ($txt_barcode_no != "") $bar_code_cond = " and b.barcode_no='$txt_barcode_no'";
	$style_ref_cond = "";
	if ($txt_style_ref_no != "") $style_ref_cond = " and e.style_ref_no='$txt_style_ref_no'";

	$hdn_batch_id_cond = "";
	$txt_batch_no_cond = "";
	if ($hdn_batch_no != "") $hdn_batch_id_cond = " and a.id='$hdn_batch_no'";
	if ($txt_batch_no != "") $txt_batch_no_cond = " and a.batch_no='$txt_batch_no'";

	if ($hdn_batch_no == "") {
		$batch_no_cond_not = "";
	} else {
		$batch_no_cond_not = " and a.id !='$hdn_batch_no'";
	}
	if ($txt_batch_no == "") {
		$txt_batch_no_cond_not = "";
	} else {
		$txt_batch_no_cond_not = " and a.batch_no !='$txt_batch_no'";
	}

	$machine_name = array();
	$machine_data = sql_select("select id,dia_width, gauge from lib_machine_name");
	foreach ($machine_data as $row) {
		$machine_name[$row[csf("id")]]["mac_dia"] = $row[csf("dia_width")];
		$machine_name[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$fin_wgt_arr = array();//inv_receive_master //pro_finish_fabric_rcv_dtls
	$fin_wgt = sql_select("select b.batch_id,b.receive_qnty,b.barcode_no from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.entry_form=66 and b.status_active=1 and b.is_deleted=0");
	foreach ($fin_wgt as $row) {
		$fin_wgt_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["batch_qnty"] = $row[csf("receive_qnty")];
		$fin_wgt_arr2[$row[csf("barcode_no")]]["fin_qnty"] = $row[csf("receive_qnty")];
	}

	$fin_feb_recv_by_store_arr = array();
	$fin_recv = sql_select("select a.receive_date,a.insert_date,b.barcode_no,b.batch_id from  pro_finish_fabric_rcv_dtls b,inv_receive_master a where  a.company_id=$company_name and a.id=b.mst_id  and a.entry_form=68 and b.status_active=1 and b.is_deleted=0");
	foreach ($fin_recv as $row) {
		$fin_feb_recv_by_store_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["receive_date"] = $row[csf("receive_date")];
		$fin_feb_recv_by_store_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["insert_date"] = $row[csf("insert_date")];
	}

	$fin_feb_issue_to_cut_arr = array();
	$fin_issue = sql_select("select a.issue_date,a.insert_date,b.pi_wo_batch_no as batch_id from  inv_transaction b,inv_issue_master a where a.company_id=$company_name and a.id=b.mst_id  and a.entry_form=71 and b.status_active=1 and b.is_deleted=0");
	foreach ($fin_issue as $row) {
		$fin_feb_issue_to_cut_arr[$row[csf("batch_id")]]["issue_date"] = $row[csf("issue_date")];
		$fin_feb_issue_to_cut_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
	}
	$fin_feb_recv_to_cut_arr = array();
	//select a.receive_date,a.insert_date,b.batch_id as batch_id from  pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details  c where a.company_id=1 and
// c.dtls_id=b.id and a.id=b.mst_id  and a.entry_form=72 and b.status_active=1 and b.is_deleted=0
	$fin_recv_to = sql_select("select a.receive_date,a.insert_date,c.barcode_no from  pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details  c where a.company_id=$company_name and c.dtls_id=b.id  and a.id=b.mst_id  and a.entry_form=72 and b.status_active=1 and b.is_deleted=0");
	foreach ($fin_recv_to as $row) {
		$fin_feb_recv_to_cut_arr[$row[csf("barcode_no")]]["receive_date"] = $row[csf("receive_date")];
		$fin_feb_recv_to_cut_arr[$row[csf("barcode_no")]]["insert_date"] = $row[csf("insert_date")];
	}

	//inv_issue_master
	$fin_feb_del_store_arr = array();
	$fin_feb = sql_select("select a.delevery_date,a.insert_date,b.batch_id from pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst a where a.company_id=$company_name and a.id=b.mst_id  and a.entry_form=67 and b.status_active=1 and b.is_deleted=0 and b.batch_id!=0");
	foreach ($fin_feb as $row) {
		$fin_feb_del_store_arr[$row[csf("batch_id")]]["delevery_date"] = $row[csf("delevery_date")];
		$fin_feb_del_store_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
	}

	$deying_end_data_arr = array();
	$deying_start_data_arr = array();
	$sliting_start_data_arr = array();
	$stenter_start_data_arr = array();
	$special_end_data_arr = array();
	$dry_start_data_arr = array();
	$compact_end_data_arr = array();
	$sliting_end_data_arr = array();
	$sql_data = sql_select("select  a.entry_form,a.load_unload_id,a.batch_id,a.process_end_date as process_end_date,a.production_date as end_date,a.process_start_date,a.start_hours,a.end_hours,a.start_minutes,a.end_minutes,c.barcode_no from  pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_roll_details c where b.roll_id=c.id and a.id=b.mst_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 ");
	foreach ($sql_data as $row) {
		if ($row[csf("entry_form")] == 35) {
			if ($row[csf("load_unload_id")] == 1) {
				$deying_start_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["start_date"] = $row[csf("process_end_date")];
				$deying_start_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
				$deying_start_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];
			} else {
				$deying_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["end_date"] = $row[csf("process_end_date")];
				$deying_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
				$deying_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];
			}
		} else if ($row[csf("entry_form")] == 30) {
			$sliting_start_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["prod_date"] = $row[csf("end_date")];
			$sliting_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
			$sliting_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];
		} else if ($row[csf("entry_form")] == 48) {
			$stenter_start_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["prod_date"] = $row[csf("end_date")];
			//$stenter_start_data_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
			$stenter_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
			$stenter_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];
		} else if ($row[csf("entry_form")] == 31) //Drying...
		{
			$dry_start_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["prod_date"] = $row[csf("end_date")];
			$dry_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
			$dry_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];
		} else if ($row[csf("entry_form")] == 34) //Special Finish...
		{
			$special_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["prod_date"] = $row[csf("end_date")];
			//$special_start_data_arr[$row[csf("batch_id")]]["end_date"]=$row[csf("end_date")];
			$special_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
			$special_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];
		} else if ($row[csf("entry_form")] == 33) //Compacting...
		{
			$compact_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["prod_date"] = $row[csf("end_date")];

			$compact_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["hours"] = $row[csf("end_hours")];
			$compact_end_data_arr[$row[csf("batch_id")]][$row[csf("barcode_no")]]["minutes"] = $row[csf("end_minutes")];

		}

	}
	$kniting_prod_arr = array();
	$knit_feb = sql_select("select b.barcode_no,c.stitch_length,c.gsm,c.width,c.machine_no_id,b.qnty from pro_roll_details b,pro_grey_prod_entry_dtls c where   b.dtls_id=c.id  and b.status_active=1 and b.is_deleted=0 and b.barcode_no!=0 and b.entry_form in(22,2)");
	foreach ($knit_feb as $row) {
		$kniting_prod_arr[$row[csf("barcode_no")]]["qnty"] = $row[csf("qnty")];
		$kniting_prod_arr[$row[csf("barcode_no")]]["gsm"] = $row[csf("gsm")];
		$kniting_prod_arr[$row[csf("barcode_no")]]["width"] = $row[csf("width")];
		$kniting_prod_arr[$row[csf("barcode_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$kniting_prod_arr[$row[csf("barcode_no")]]["machine_no_id"] = $row[csf("machine_no_id")];
	}
	$sql_po_transfer = sql_select("select b.po_number, b.id as po_id,d.barcode_no,c.trans_type from  wo_po_details_master e,wo_po_break_down b,order_wise_pro_details c,pro_roll_details d where e.company_name=$company_name and c.po_breakdown_id=b.id and d.dtls_id=c.dtls_id and c.entry_form in(83,110) and d.entry_form in(83,110) and c.trans_type in(5,6) and b.job_no_mst=e.job_no and b.is_deleted=0 and b.status_active=1 $sql_cond3 $style_ref_cond");
	$bcode_transfer = '';
	$barcode_no_arr = array();
	foreach ($sql_po_transfer as $row) {

		if ($row[csf("trans_type")] == 6) {
			if ($bcode_transfer == '') $bcode_transfer = $row[csf("barcode_no")]; else $bcode_transfer .= "," . $row[csf("barcode_no")];
		} else {
			$barcode_no_arr[$row[csf("barcode_no")]]['tt'] = 'T';
		}
		//$po_no_arr[$row[csf("po_id")]]['po']=$row[csf("po_number")];
	}
	//echo $bcode_transfer;
	//array chunk for batch barcode
	if ($db_type == 2) {

		if ($bcode_transfer != '') {
			$barc_chnk = array_chunk(array_unique(explode(",", $bcode_transfer)), 1000, true);
			$barc_cond_not = "";
			$barc_cond_not_up = "";
			$mm = 0;
			foreach ($barc_chnk as $key => $value) {
				if ($mm == 0) {
					$barc_cond_not = " and a.barcode_no not in(" . implode(",", $value) . ")";
					$barc_cond_not_up = " and b.barcode_no not in(" . implode(",", $value) . ")";

				} else {
					$barc_cond_not .= " or a.barcode_no  not in(" . implode(",", $value) . ")";
					$barc_cond_not_up .= " or b.barcode_no  not in(" . implode(",", $value) . ")";

				}
				$mm++;
			}
		}
	} else {
		if ($bcode_transfer != '') {
			$barc_cond_not = " and a.barcode_no not in(" . $bcode_transfer . ")";
		}
	}
	/*echo $sql="select a.id as batch_id,a.batch_no,a.batch_date 	,a.color_id,a.batch_against,b.barcode_no,b.body_part_id,b.item_description,b.prod_id,g.stitch_length,g.machine_dia,g.machine_gg,b.fin_dia,g.machine_no_id,g.gsm,e.buyer_name,e.job_no,f.po_number,f.id as po_id  from pro_batch_create_mst a,pro_batch_create_dtls b,pro_roll_details c,wo_po_details_master e,wo_po_break_down f,pro_grey_prod_entry_dtls g
	where  a.company_id=$company_name and a.id=b.mst_id and a.id=c.mst_id  and b.po_id=f.id and c.po_breakdown_id=f.id  and e.job_no=f.job_no_mst and c.dtls_id=g.id  and a.entry_form=0 and b.barcode_no is not NULL and b.barcode_no != 0 and b.body_part_id!=0 and a.color_id!=0  and a.status_active=1 and a.is_deleted=0 $hdn_batch_id_cond  $txt_batch_no_cond $sql_cond $bar_code_cond   order by e.job_no,a.batch_no,b.barcode_no ASC" ;*/

	$sql = "select a.id as batch_id,a.batch_no,a.batch_date ,a.insert_date	,a.color_id,a.dyeing_machine,a.batch_against,b.barcode_no,b.body_part_id,b.item_description,b.prod_id,b.fin_dia,e.buyer_name,e.job_no,f.po_number,f.id as po_id  from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_details_master e,wo_po_break_down f
	where  a.company_id=$company_name and a.id=b.mst_id  and b.po_id=f.id and e.job_no=f.job_no_mst  and a.entry_form=0 and b.barcode_no is not NULL and b.barcode_no != 0 and b.body_part_id!=0 and a.color_id!=0  and a.status_active=1 and a.is_deleted=0 $hdn_batch_id_cond  $txt_batch_no_cond $sql_cond $bar_code_cond $barc_cond_not_up $year_cond   order by e.job_no,a.batch_no,b.barcode_no ASC";
	$nameArray = sql_select($sql);
	$batch_wise_arr = array();
	$batch_no_arr = array();
	$barcode_details_array = array();
	$batch_color_array = array();
	$body_details_array = array();
	$color_details_array = array();
	$desc_details_array = array();
	$batch_barcode_color_array = array();
	foreach ($nameArray as $row) {
		$batch_wise_arr[$row[csf("batch_id")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_wise_arr[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
		$batch_wise_arr[$row[csf("batch_id")]]['job_no'] = $row[csf("job_no")];
		$batch_wise_arr[$row[csf("batch_id")]]['po_number'] .= $row[csf("po_number")] . ',';
		$batch_wise_arr[$row[csf("batch_id")]]['po_id'] .= $row[csf("po_id")] . ',';
		$batch_wise_arr[$row[csf("batch_id")]]['body_part'] = $row[csf("body_part_id")];
		$batch_wise_arr[$row[csf("batch_id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_wise_arr[$row[csf("batch_id")]]['desc'] = $row[csf("item_description")];
		//$batch_wise_arr[$row[csf("batch_id")]]['machine_id']=$kniting_prod_arr[$row[csf("barcode_no")]]["machine_no_id"];
		$batch_wise_arr[$row[csf("batch_id")]]['color_id'] = $row[csf("color_id")];
		$batch_wise_arr[$row[csf("batch_id")]]['fin_dia'] = $row[csf("fin_dia")];
		$batch_wise_arr[$row[csf("batch_id")]]['gsm'] = $row[csf("gsm")];
		$batch_wise_arr[$row[csf("batch_id")]]['batch_date'] = $row[csf("batch_date")];
		$batch_wise_arr[$row[csf("batch_id")]]['insert_date'] = $row[csf("insert_date")];
		$batch_wise_arr[$row[csf("batch_id")]]['slentgh'] = $row[csf("stitch_length")];
		$batch_wise_arr[$row[csf("batch_id")]]['dia'] = $row[csf("machine_dia")];
		$batch_wise_arr[$row[csf("batch_id")]]['gg'] = $row[csf("machine_gg")];
		$batch_wise_arr[$row[csf("batch_id")]]['barcode_no'] = $row[csf("barcode_no")];
		if ($row[csf("body_part_id")] == 0) $row[csf("body_part_id")] = 0; else  $row[csf("body_part_id")] = $row[csf("body_part_id")];
		$batch_no_arr[$row[csf("batch_id")]] = $row[csf("batch_no")];
		$batch_details_arr[$row[csf("job_no")]][$row[csf('barcode_no')]] = $row[csf("barcode_no")];
		$batch_barcode_color_array[$row[csf("job_no")]][$row[csf('body_part_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('barcode_no')]] = $row[csf('barcode_no')];

		$barcode_details_array[$row[csf("batch_id")]][$row[csf('barcode_no')]] = $row[csf("barcode_no")];
		$body_details_array[$row[csf("batch_id")]][$row[csf('body_part_id')]] = $row[csf("body_part_id")];
		$desc_details_array[$row[csf("batch_id")]][$row[csf('prod_id')]] = $row[csf("prod_id")];
		$color_details_array[$row[csf("batch_id")]][$row[csf('color_id')]] = $row[csf("color_id")];
		$batch_details_arr[$row[csf("batch_id")]][$row[csf('batch_id')]] = $row[csf("batch_id")];
		$batch_color_array[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	$body_rowspan_arr = array();
	$item_rowspan_arr = array();
	$barcode_rowspan_arr = array();
	$color_rowspan_arr = array();
	$batch_rowspan_arr = array();
	foreach ($batch_barcode_color_array as $job_id => $job_value) {
		foreach ($job_value as $body_id => $body_value) {
			$body_rowspan = 0;
			foreach ($body_value as $item_id => $item_value) {
				foreach ($item_value as $color_id => $color_id_value) {
					$color_rowspan = 0;
					foreach ($color_id_value as $batch_id => $batch_id_value) {
						$batch_rowspan = 0;
						foreach ($batch_id_value as $barcode_id => $bcode_value) {
							$batch_rowspan++;
							$color_rowspan++;
							$body_rowspan++;
						}
						//$body_rowspan++;
						$batch_rowspan_arr[$job_id][$body_id][$item_id][$color_id][$batch_id] = $batch_rowspan;
						$color_rowspan_arr[$job_id][$body_id][$item_id][$color_id] = $color_rowspan;
						$body_rowspan_arr[$job_id][$body_id] = $body_rowspan;
					}
				}
			}
		}
	}
	//print_r( $color_rowspan_arr);
	ob_start();
	?>

	<div>
		<script>
			function fnc_summary() {
				var total_barcode_up = document.getElementById('tot_barcode_td').value;
				var total_barcode_down = document.getElementById('tot_no_batch_td').value;

				document.getElementById('total_barcode_create').innerHTML = total_barcode_up * 1;
				var total_roll = (total_barcode_up * 1) + (total_barcode_down * 1);

				document.getElementById('total_roll_td').innerHTML = total_roll;
				var total_barcode_percent = ((total_barcode_up * 1) * 100) / total_roll;
				document.getElementById('total_percent_td').innerHTML = number_format(total_barcode_percent, 2);
			}
		</script>

		<div style="float:left; margin-left:100px;">
			<table class="rpt_table" border="1" width="250" rules="all">
				<thead>
					<tr>
						<th colspan="3" align="center">Summary</th>
					</tr>
					<tr>
						<th width="80">Total Roll</th>
						<th width="120">Total Batch Created Against Barcode</th>
						<th>%</th>
					</tr>
				</thead>
				<tr>
					<td align="center" id="total_roll_td">&nbsp;</td>
					<td align="center" id="total_barcode_create">&nbsp;</td>
					<td align="center" id="total_percent_td">&nbsp;</td>
				</tr>
			</table>
		</div>
		<div style="width:2310px; float:left;">
			<br>
			<fieldset>
				<legend>Batch Wise</legend>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2310" class="rpt_table"
				style="float:left;">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="120">Buyer</th>
						<th width="120">Job No</th>
						<th width="120">Order No</th>
						<th width="120">Body Part</th>
						<th width="240">Fabric Description</th>
						<th width="100">Color</th>
						<th width="60">Batch No</th>
						<th width="100">Barcode No</th>
						<th width="70">Grey Wgt.</th>
						<th width="60">GSM</th>
						<th width="60">Finish Dia</th>
						<th width="60">Stich Lenth</th>
						<th width="60">Machine Dia</th>
						<th width="60">Guage</th>
						<th width="60">Batch Create</th>
						<th width="60">Dyeing Start</th>
						<th width="60">Dyeing End</th>
						<th width="60">Slitting Squeezing</th>
						<th width="60">Stentering</th>
						<th width="60">Drying</th>
						<th width="60">Special Finish</th>
						<th width="60">Compact</th>
						<th width="60">Finish Wgt</th>
						<th width="60">Process Loss</th>
						<th width="60">Finish Fab.Delv. To Store</th>
						<th width="60">Recv. by Store</th>
						<th width="60">Issue to Cut</th>
						<th width="">Recv. by Cut</th>

					</tr>
				</thead>
			</table>
			<div style=" max-height:380px; width:2310px; overflow-y:scroll;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2290" class="rpt_table"
				id="table_body" style="float:left;">
				<?
				$m = 1;
				$mm = 1;
				$total_fin_wgt = 0;
				$tot_batch_against_barcode = '';
				$total_roll_qty = 0;
				$total_process_loss_qty = 0;
						//$tot_grey_delivery=$tot_grey_rcv_store=$tot_grey_issue_batch=$tot_grey_rcv_batch=$tot_batch_created=$tot_fin_delivery=$tot_fin_rcv_store=$tot_fin_issu_cut=0;
				foreach ($batch_barcode_color_array as $job_key => $job_val) {
					$x = 1;
					foreach ($job_val as $body_key => $body_val) {
						$y = 1;
						foreach ($body_val as $desc_key => $descval) {
							$z = 1;
							foreach ($descval as $color_key => $colorval) {
								$zz = 1;
								foreach ($colorval as $batch_key => $batchval) {
									$zzz = 1;
									foreach ($batchval as $bcode_key => $val) {

										if ($m % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

										$po_nos = rtrim($batch_wise_arr[$batch_key]['po_number'], ',');
										$po_nos = implode(",", array_unique(explode(',', $po_nos)));
										$job_no = $batch_wise_arr[$batch_key]['job_no'];
										$buyer_name = $batch_wise_arr[$batch_key]['buyer_name'];
												$fin_dia = $kniting_prod_arr[$bcode_key]["width"];//$batch_wise_arr[$batch_key]['fin_dia'];
												$gsm = $kniting_prod_arr[$bcode_key]["gsm"];
												$slentgh = $kniting_prod_arr[$bcode_key]["stitch_length"];

												$machine_id = $kniting_prod_arr[$bcode_key]["machine_no_id"];//$batch_wise_arr[$batch_key]['machine_id'];
												$m_gg = $machine_name[$machine_id]["gauge"];//$machine_name[$row[csf("id")]]["gauge"]
												$m_dia = $machine_name[$machine_id]["mac_dia"];
												$roll_qnty = $kniting_prod_arr[$bcode_key]["qnty"];

												$batch_against = $batch_wise_arr[$batch_key]['batch_against'];
												$batch_insert_date = $batch_wise_arr[$batch_key]['insert_date'];
												$batch_insert = explode(" ", $batch_insert_date);
												//$batch_insert_time=$batch_insert[1];

												$batch_time = explode(".", $batch_insert[1]);
												$batch_insert_time = $batch_time[0] . ':' . $batch_time[1];


												if ($batch_against == 2) {
													$batch_againsts = 'Re Dyeing';
												} else {
													$batch_againsts = '';
												}
												$batch_date = $batch_wise_arr[$batch_key]['batch_date'];

												$dyeing_start_date = $deying_start_data_arr[$batch_key][$bcode_key]["start_date"];
												$dyeing_end_date = $deying_end_data_arr[$batch_key][$bcode_key]["end_date"];

												$dyeing_start_hr = $deying_start_data_arr[$batch_key][$bcode_key]["hours"];
												$dyeing_start_min = $deying_start_data_arr[$batch_key][$bcode_key]["minutes"];

												$dyeing_end_hr = $deying_end_data_arr[$batch_key][$bcode_key]["hours"];
												$dyeing_end_min = $deying_end_data_arr[$batch_key][$bcode_key]["minutes"];
												if ($dyeing_start_date != 0 || $dyeing_end_date != 0) {
													$d_start_hour_min = $dyeing_start_hr . ':' . $dyeing_start_min;
													$d_end_hour_min = $dyeing_end_hr . ':' . $dyeing_end_min;
												} else {
													$d_start_hour_min = '';
													$d_end_hour_min = '';
												}


												$sliting_prod_date = $sliting_start_data_arr[$batch_key][$bcode_key]["prod_date"];
												//$sliting_end_date=$sliting_start_data_arr[$batch_key]["end_date"];

												$stenter_prod_date = $stenter_start_data_arr[$batch_key][$bcode_key]["prod_date"];
												$dry_prod_date = $dry_start_data_arr[$batch_key][$bcode_key]["prod_date"];
												$sfinish_prod_date = $special_end_data_arr[$batch_key][$bcode_key]["prod_date"];
												$compact_prod_date = $compact_end_data_arr[$batch_key][$bcode_key]["prod_date"];
												$comp_hours = $compact_end_data_arr[$batch_key][$bcode_key]["hours"];
												$comp_min = $compact_end_data_arr[$batch_key][$bcode_key]["minutes"];

												$dry_hours = $dry_end_data_arr[$batch_key][$bcode_key]["hours"];
												$dry_min = $dry_end_data_arr[$batch_key][$bcode_key]["minutes"];

												$spfinish_hours = $special_end_data_arr[$batch_key][$bcode_key]["hours"];
												$spfinish_min = $special_end_data_arr[$batch_key][$bcode_key]["minutes"];

												if ($compact_prod_date != 0 || $compact_prod_date != '') {
													$comp_hours_min = $comp_hours . ':' . $comp_min;
												} else {
													$comp_hours_min = '';
												}

												if ($dry_prod_date != 0 || $dry_prod_date != '') {
													$dry_start_hour_min = $dry_hours . ':' . $dry_min;
												} else {
													$dry_start_hour_min = '';
												}
												if ($sfinish_prod_date != 0 || $sfinish_prod_date != '') {
													$spfinisht_hour_min = $spfinish_hours . ':' . $spfinish_min;
												} else {
													$spfinisht_hour_min = '';
												}


												$stenter_end_hr = $stenter_end_data_arr[$batch_key][$bcode_key]["hours"];
												$stenter_end_min = $stenter_end_data_arr[$batch_key][$bcode_key]["minutes"];
												//$stenter_start_hr=$stenter_start_data_arr[$batch_key]["hours"];
												//$stenter_start_min=$stenter_end_data_arr[$batch_key]["minutes"];

												if ($stenter_prod_date != 0 || $stenter_prod_date != '') {
													$stenter_start_hour_min = $stenter_end_hr . ':' . $stenter_end_min;
												} else {
													$stenter_start_hour_min = '';
												}

												$sliting_start_hr = $sliting_end_data_arr[$batch_key][$bcode_key]["hours"];
												$sliting_start_min = $sliting_end_data_arr[$batch_key][$bcode_key]["minutes"];
												if ($sliting_prod_date != 0 || $sliting_prod_date != '') {
													$sliting_start_hour_min = $sliting_start_hr . ':' . $sliting_start_min;
												} else {
													$sliting_start_hour_min = '';
												}

												$fin_wgt = $fin_wgt_arr[$batch_key][$bcode_key]["batch_qnty"];
												$fin_del_store_date = $fin_feb_del_store_arr[$batch_key]["delevery_date"];
												$fin_del_store_datetime = $fin_feb_del_store_arr[$batch_key]["insert_date"];
												$fin_del_store_time = explode(" ", $fin_del_store_datetime);
												//$fin_del_store_time=$fin_del_store_time[1];

												$fin_del_time = explode(".", $fin_del_store_time[1]);
												$fin_del_store_time = $fin_del_time[0] . ':' . $fin_del_time[1];

												$fin_feb_recv_by_store_date = $fin_feb_recv_by_store_arr[$batch_key][$bcode_key]["receive_date"];
												$fin_feb_recv_by_store_datetime = $fin_feb_recv_by_store_arr[$batch_key][$bcode_key]["insert_date"];
												$fin_feb_recv_by_store_time = explode(" ", $fin_feb_recv_by_store_datetime);
												//$fin_feb_recv_by_store_time=$fin_feb_recv_by_store_time[1];

												$fin_feb_recv_by_time = explode(".", $fin_feb_recv_by_store_time[1]);
												$fin_feb_recv_by_store_time = $fin_feb_recv_by_time[0] . ':' . $fin_feb_recv_by_time[1];

												$fin_feb_issue_to_cut_date = $fin_feb_issue_to_cut_arr[$batch_key]["issue_date"];
												$fin_feb_recv_to_cut_date = $fin_feb_recv_to_cut_arr[$bcode_key]["receive_date"];
												$fin_feb_issue_to_cut_datetime = $fin_feb_issue_to_cut_arr[$batch_key]["insert_date"];
												$fin_feb_recv_to_cut_datetime = $fin_feb_recv_to_cut_arr[$bcode_key]["insert_date"];

												$fin_feb_issue_to_cut_time = explode(" ", $fin_feb_issue_to_cut_datetime);
												//$fin_feb_issue_to_cut_time=$fin_feb_issue_to_cut_time[1];

												$fin_issue_to_cut = explode(".", $fin_feb_issue_to_cut_time[1]);
												$fin_feb_issue_to_cut_time = $fin_issue_to_cut[0] . ':' . $fin_issue_to_cut[1];

												$fin_feb_recv_to_cut_time = explode(" ", $fin_feb_recv_to_cut_datetime);
												//$fin_feb_recv_to_cut_time=$fin_feb_recv_to_cut_time[1];

												$recv_to_cut_time = explode(".", $fin_feb_recv_to_cut_time[1]);
												$fin_feb_recv_to_cut_time = $recv_to_cut_time[0] . ':' . $recv_to_cut_time[1];

												$t_msg = $barcode_no_arr[$bcode_key]['tt'];
												if ($t_msg != '') $t_msg = $t_msg; else $t_msg = '';
												$process_loss_qty = $roll_qnty - $fin_wgt;
												$process_loss_per = ($fin_wgt / $roll_qnty) * 100;

												if ($tot_batch_against_barcode == '') $tot_batch_against_barcode = $bcode_key; else $tot_batch_against_barcode .= "," . $bcode_key;
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"
													onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')"
													id="tr_<? echo $m; ?>">
													<?

													if ($y == 1) {
														?>
														<td width="30"
														rowspan="<? echo $body_rowspan_arr[$job_key][$body_key]; ?>"
														align="center"><? echo $mm; ?></td>
														<td width="120"
														rowspan="<? echo $body_rowspan_arr[$job_key][$body_key]; ?>"><? echo $buyer_arr[$buyer_name]; ?></td>
														<td width="120"
														rowspan="<? echo $body_rowspan_arr[$job_key][$body_key]; ?>"><? echo $job_key; ?></td>
														<td width="120"
														rowspan="<? echo $body_rowspan_arr[$job_key][$body_key]; ?>"><? echo $po_nos; ?></td>
														<td width="120"
														rowspan="<? echo $body_rowspan_arr[$job_key][$body_key]; ?>"><? echo $body_part[$body_key]; ?></td>
														<td width="240"
														rowspan="<? echo $body_rowspan_arr[$job_key][$body_key]; ?>"><? echo $desc_library[$desc_key]; ?></td>
														<?
													}
													if ($zz == 1) {
														?>
														<td width="100"
														rowspan="<? echo $color_rowspan_arr[$job_key][$body_key][$desc_key][$color_key]; ?>"><? echo $color_library[$color_key]; ?></td>
														<?
													}
													if ($zzz == 1) {
														?>
														<td width="60" title="<? echo $batch_againsts; ?>"
															rowspan="<? echo $batch_rowspan_arr[$job_key][$body_key][$desc_key][$color_key][$batch_key]; ?>"><? echo $batch_no_arr[$batch_key]; ?></td>
															<?
														}
														?>

														<td width="100"><? echo $t_msg . ' ' . $bcode_key; ?></p></td>
														<td width="70" align="right"><p><? echo $roll_qnty; ?></p></td>
														<td width="60"><p><? echo $gsm; ?></p></td>
														<td width="60"><p><? echo $fin_dia; ?></p></td>
														<td width="60"><p><? echo $slentgh; ?></p></td>

														<td width="60"><p><? echo $m_dia; ?></p></td>
														<td width="60"><p><? echo $m_gg; ?></p></td>

														<td width="60"><? echo change_date_format($batch_date) . '<br>' . $batch_insert_time; ?></td>
														<td width="60"><? echo change_date_format($dyeing_start_date) . '<br>' . $d_start_hour_min; ?></td>
														<td width="60"><? echo change_date_format($dyeing_end_date) . '<br>' . $d_end_hour_min; ?></td>
														<td width="60"
														title="Prod Date Time"><? echo change_date_format($sliting_prod_date) . '<br>' . $sliting_start_hour_min; ?></td>
														<td width="60"><? echo change_date_format($stenter_prod_date) . '<br>' . $stenter_start_hour_min; ?> </td>
														<td width="60"><? echo change_date_format($dry_prod_date) . '<br>' . $dry_start_hour_min; ?></td>
														<td width="60"><? echo change_date_format($sfinish_prod_date) . '<br>' . $spfinisht_hour_min; ?></td>
														<td width="60"><? echo change_date_format($compact_prod_date) . '<br>' . $comp_hours_min;; ?></td>

														<td width="60"
														align="right"><? echo number_format($fin_wgt, 2); ?> </td>
														<td width="60"
														align="right"><? echo number_format($process_loss_qty, 2) . ' Kg' . '<br>' . number_format($process_loss_per, 2) . '%'; ?> </td>

														<td width="60"><? echo change_date_format($fin_del_store_date) . '<br>' . $fin_del_store_time; ?> </td>
														<td width="60"><? echo change_date_format($fin_feb_recv_by_store_date) . '<br>' . $fin_feb_recv_by_store_time;; ?> </td>
														<td width="60"><? echo change_date_format($fin_feb_issue_to_cut_date) . '<br>' . $fin_feb_issue_to_cut_time;; ?></td>
														<td width=""><? echo change_date_format($fin_feb_recv_to_cut_date) . '<br>' . $fin_feb_recv_to_cut_time;; ?> </td>

													</tr>
													<?

													$total_fin_wgt += $fin_wgt;
													$total_roll_qty += $roll_qnty;
													$total_process_loss_qty += $process_loss_qty;
													$m++;
													$x++;
													$y++;
													$z++;
													$zz++;
													$zzz++;

												}

											}
										}
									}
									$mm++;
								}

							}
							?>

							<tfoot>
								<tr>
									<th colspan="9" align="right">Total</th>
									<th> <? echo number_format($total_roll_qty, 2); ?>  </th>
									<th> <?
									if ($tot_batch_against_barcode != '') {
										$tot_batch = array_unique(explode(",", $tot_batch_against_barcode));
										$tot_batch_row = count($tot_batch);
									} else {
										$tot_batch_row = 0;
									}
									?>
									<input type="hidden" id="tot_barcode_td" class="text_boxes" style="width:30px"
									value="<? echo $tot_batch_row; ?> ">
								</th>

								<th colspan="12"></th>
                            <th> <? echo number_format($total_fin_wgt, 2); //	$process_loss_per=($fin_wgt/$roll_qnty)*100;
                            ?>  </th>
                            <th> <? $total_process_loss_per = ($total_fin_wgt / $total_roll_qty) * 100;
                            echo number_format($total_process_loss_qty, 2) . '<br>' . number_format($total_process_loss_per, 2) . '%'; ?>  </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>

    </div>
    <?
		//die;
    ?>
    <div style="width:1370px; float:left;">
    	<br>
    	<fieldset style="width:1370px;">
    		<legend>Not Batch</legend>
    		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1370" class="rpt_table"
    		style="float:left;">
    		<thead>
    			<tr>
    				<th width="30">SL</th>
    				<th width="150">Buyer</th>
    				<th width="100">Job</th>
    				<th width="100">Order</th>
    				<th width="120">Body Part</th>
    				<th width="200">Fabric Description</th>
    				<th width="100">Barcode No</th>
    				<th width="70">Grey Wgt.</th>
    				<th width="100">GSM</th>
    				<th width="100">Finish Dia</th>
    				<th width="100">Stich Length</th>
    				<th width="100">Machine Dia</th>
    				<th width="">Guage</th>
    			</tr>
    		</thead>
    	</table>
    	<?
    	$sql_cond2 = "";
    	if ($txt_file_no != "") $sql_cond2 = " and b.file_no='$txt_file_no'";
    	if ($txt_job_no != "") $sql_cond2 .= " and e.job_no_prefix_num =$txt_job_no";

    	if ($txt_order_no != "") $sql_cond2 .= " and b.po_number like '%$txt_order_no%'";
    	if ($txt_inter_ref != "") $sql_cond2 .= " and b.grouping='$txt_inter_ref'";

    	if ($cbo_buyer_name != 0) $buyer_cond = " and e.buyer_name=$cbo_buyer_name"; else $buyer_cond = "";
    	$bar_code_cond = "";
    	if ($txt_barcode_no != "") $bar_code_cond = " and a.barcode_no='$txt_barcode_no'";
    	$style_ref_cond = "";
    	if ($txt_style_ref_no != "") $style_ref_cond = " and e.style_ref_no='$txt_style_ref_no'";


    	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
    	$machine_data = array();
    	foreach ($machine_sql as $row) {
    		$machine_data[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
    		$machine_data[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
    		$machine_data[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
    	}

    	$btch_crt_barcodes = "";
				//$btch_crt_po="";
    	$btch_crt_barcodes_arr = array();
    	$sql_btch_crt_barcode = sql_select("select b.po_id,b.barcode_no from  pro_batch_create_mst a,pro_batch_create_dtls b where a.company_id=$company_name and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and a.entry_form=0 and b.barcode_no is not NULL and  b.barcode_no!=0  $txt_batch_no_cond group by b.po_id,b.barcode_no");
    	foreach ($sql_btch_crt_barcode as $row) {
    		if ($btch_crt_barcodes == "") $btch_crt_barcodes = $row[csf("barcode_no")]; else  $btch_crt_barcodes .= "," . $row[csf("barcode_no")];
    	}
				//echo $btch_crt_barcodes;
    	$sql_po = sql_select("select a.buyer_name,a.job_no,b.po_number, b.id as po_id from  wo_po_details_master a,wo_po_break_down b where a.company_name=$company_name and b.job_no_mst=a.job_no and b.is_deleted=0 and b.status_active=1 ");
    	foreach ($sql_po as $row) {
    		$po_no_arr[$row[csf("job_no")]]['buyer'] = $row[csf("buyer_name")];
    		$po_no_arr[$row[csf("po_id")]]['po'] = $row[csf("po_number")];
    	}
				/*$sql_po_transfer =sql_select("select b.po_number, b.id as po_id,d.barcode_no,c.trans_type from  wo_po_details_master e,wo_po_break_down b,order_wise_pro_details c,pro_roll_details d where e.company_name=$company_name and c.po_breakdown_id=b.id and d.dtls_id=c.dtls_id and c.entry_form in(83,110) and d.entry_form in(83,110) and c.trans_type in(5,6) and b.job_no_mst=e.job_no and b.is_deleted=0 and b.status_active=1 $sql_cond2  $buyer_cond $style_ref_cond");
			$bcode_transfer='';$barcode_no_arr=array();
			foreach($sql_po_transfer as $row)
			{

				if($row[csf("trans_type")]==6)
				{
					if($bcode_transfer=='') $bcode_transfer=$row[csf("barcode_no")];else $bcode_transfer.=",".$row[csf("barcode_no")];
				}
				else
				{
					$barcode_no_arr[$row[csf("barcode_no")]]['tt']='T';
				}
				//$po_no_arr[$row[csf("po_id")]]['po']=$row[csf("po_number")];
			}
			//echo $bcode_transfer;
			//array chunk for batch barcode
			 if($db_type==2)
			  {

					if($bcode_transfer!='')
					{
						 $barc_chnk=array_chunk(array_unique(explode(",",$bcode_transfer)),1000, true);
						 $barc_cond_not="";
						   $mm=0;
						   foreach($barc_chnk as $key=> $value)
						   {
							   if($mm==0)
							   {
									$barc_cond_not=" and a.barcode_no not in(".implode(",",$value).")";

							   }
							   else
							   {
									$barc_cond_not.=" or a.barcode_no  not in(".implode(",",$value).")";

							   }
						   $mm++;
						   }
					}
				}
			  else
			  {
				  	if($bcode_transfer!='')
					{
						$barc_cond_not=" and a.barcode_no not in(".$bcode_transfer.")";
					}
			  }
			   */
			  $barcode_chnk = array_chunk(array_unique(explode(",", $btch_crt_barcodes)), 1000, true);
			  $barcode_cond = "";
			  $x = 0;
			  foreach ($barcode_chnk as $key => $value) {
			  	if ($x == 0) {
			  		$barcode_cond = " and a.barcode_no  not in(" . implode(",", $value) . ")";

			  	} else {
			  		$barcode_cond .= " or a.barcode_no  not in(" . implode(",", $value) . ")";

			  	}
			  	$x++;
			  }
				// echo $barcode_cond; //$btch_crt_barcodes/$barcode_cond
			  if ($db_type == 0) {

			  	if ($btch_crt_barcodes == '') $btch_crt_barcodes = 0; else $btch_crt_barcodes = $btch_crt_barcodes;
			  }


			  if ($txt_batch_no == '') {
			  	if ($db_type == 0) {
			  		$sql_roll_barcode = "select b.id as po_id,a.barcode_no,a.qnty,b.job_no_mst as job_no,b.po_number,e.buyer_name as buyer_name,d.body_part_id,d.febric_description_id,d.gsm,d.width as dia,d.stitch_length, d.machine_no_id,d.machine_dia,d.machine_gg ,d.prod_id from pro_roll_details a,wo_po_break_down b,pro_grey_prod_entry_dtls d,wo_po_details_master e where  a.po_breakdown_id=b.id and a.dtls_id=d.id and b.job_no_mst=e.job_no and e.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and  a.barcode_no!=0 and  a.entry_form in(2,22,82,83,110) and a.barcode_no not in($btch_crt_barcodes)  $sql_cond2 $bar_code_cond $style_ref_cond $buyer_cond  $pos_cond $year_cond  order by  b.id,a.barcode_no ";
			  	} else {
			  		$sql_roll_barcode = "select b.id as po_id,a.barcode_no,a.qnty,b.job_no_mst as job_no,b.po_number,e.buyer_name as buyer_name,d.body_part_id,d.febric_description_id,d.gsm,d.width as dia,d.stitch_length, d.machine_no_id,d.machine_dia,d.machine_gg ,d.prod_id from pro_roll_details a,wo_po_break_down b,pro_grey_prod_entry_dtls d,wo_po_details_master e where  a.po_breakdown_id=b.id and a.dtls_id=d.id and b.job_no_mst=e.job_no and e.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and  a.barcode_no!=0 and a.entry_form in(2,22,82,83,110) $barcode_cond $sql_cond2 $bar_code_cond $style_ref_cond $buyer_cond  $barc_cond_not $year_cond  order by  b.id,a.barcode_no ";
			  	}
			  	$sql_not_batch_arr = sql_select($sql_roll_barcode);
			  }

			  $barcode_data_arr = array();
			  foreach ($sql_not_batch_arr as $row) {
			  	$prod_detail_arr[$row[csf("barcode_no")]]['buyer_name'] = $row[csf("buyer_name")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['job'] = $row[csf("job_no_mst")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['po'] = $row[csf("po_number")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['machine_id'] = $row[csf("machine_no_id")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['slength'] = $row[csf("stitch_length")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['dia'] = $row[csf("machine_dia")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['fin_dia'] = $row[csf("dia")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['gg'] = $row[csf("machine_gg")];
			  	$prod_detail_arr[$row[csf("barcode_no")]]['qnty'] = $row[csf("qnty")];
			  	$barcode_data_arr[$row[csf("job_no")]][$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]][$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			  }

			  $body_rowspan_arr2 = array();
			  $desc_rowspan_arr = array();
			  $po_rowspan_arr = array();
			  foreach ($barcode_data_arr as $job_id => $job_value) {
			  	foreach ($job_value as $po_id => $po_value) {
			  		$po_rowspan = 0;
			  		foreach ($po_value as $body_id => $body_value) {
			  			$body_rowspan = 0;
			  			foreach ($body_value as $item_id => $item_value) {
			  				$desc_rowspan = 0;
			  				foreach ($item_value as $barcode_id => $bcode_value) {
			  					$desc_rowspan++;
			  					$body_rowspan++;
			  					$po_rowspan++;
			  				}
			  				$desc_rowspan_arr[$po_id][$body_id][$item_id] = $desc_rowspan;
			  				$body_rowspan_arr2[$po_id][$body_id] = $body_rowspan;
			  				$po_rowspan_arr[$po_id] = $po_rowspan;
			  			}
			  		}
			  	}
			  }
				//print_r($desc_rowspan_arr);
			  ?>
			  <div style=" max-height:380px; width:1390px; overflow-y:scroll;" id="scroll_body">
			  	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1370" class="rpt_table"
			  	id="table_body" style="float:left;">
			  	<?
			  	$i = 1;
			  	$k = 1;
			  	$tot_barcode = '';
			  	$total_roll_qnty = 0;
			  	foreach ($barcode_data_arr as $job_key => $job_value) {
			  		$j = 1;
			  		foreach ($job_value as $po_key => $po_value) {
			  			$x = 1;
			  			foreach ($po_value as $body_key => $body_value) {
			  				$y = 1;
			  				foreach ($body_value as $desc_key => $desc_value) {
			  					$z = 1;
			  					foreach ($desc_value as $barcode_key => $bar_value) {
			  						if ($i % 2 == 0)
			  							$bgcolor = "#E9F3FF";
			  						else
			  							$bgcolor = "#FFFFFF";
											//echo $desc_key.'d';
			  						$roll_qnty = $prod_detail_arr[$barcode_key]['qnty'];
			  						$fin_wgt_qty = $fin_wgt_arr2[$barcode_key]["fin_qnty"];
											//echo  $fin_wgt_qty.'fffffs';
			  						$slength = $prod_detail_arr[$barcode_key]['slength'];
			  						$gsm = $prod_detail_arr[$barcode_key]['gsm'];
			  						$fdia = $prod_detail_arr[$barcode_key]['fin_dia'];
			  						$machine_id = $prod_detail_arr[$barcode_key]['machine_id'];
			  						$t_msg = $barcode_no_arr[$barcode_key]['tt'];
			  						if ($t_msg != '') $t_msg = $t_msg; else $t_msg = '';
			  						if ($tot_barcode == '') $tot_barcode = $barcode_key; else $tot_barcode .= "," . $barcode_key;
											//echo $tot_barcode.'sd';
			  						?>
			  						<tr bgcolor="<? echo $bgcolor; ?>"
			  							onClick="change_color('trb_<? echo $i; ?>','<? echo $bgcolor; ?>')"
			  							id="trb_<? echo $i; ?>">
			  							<?
			  							if ($x == 1) {
			  								?>
			  								<td width="30"
			  								rowspan="<? echo $po_rowspan_arr[$po_key]; ?>"><? echo $k; ?></td>
			  								<td width="150" rowspan="<? echo $po_rowspan_arr[$po_key]; ?>">
			  									<p><? echo $buyer_arr[$po_no_arr[$job_key]['buyer']]; ?> </p>
			  								</td>
			  								<td width="100" rowspan="<? echo $po_rowspan_arr[$po_key]; ?>">
			  									<p><? echo $job_key; ?> </p></td>
			  									<td width="100" rowspan="<? echo $po_rowspan_arr[$po_key]; ?>">
			  										<p><? echo $po_no_arr[$po_key]['po']; ?> </p></td>
			  										<?
			  									}

			  									if ($y == 1) {
			  										?>

			  										<td width="120"
			  										rowspan="<? echo $body_rowspan_arr2[$po_key][$body_key]; ?>">
			  										<p><? echo $body_part[$body_key]; ?></p></td>

			  										<?
			  									}
			  									if ($z == 1) {
			  										?>


			  										<td width="200"
			  										rowspan="<? echo $desc_rowspan_arr[$po_key][$body_key][$desc_key]; ?>">
			  										<p><? echo $desc_library[$desc_key]; ?> </p></td>
			  										<?
			  									}
			  									?>
			  									<td width="100" align="center" title="<? echo $t_msg; ?>">
			  										<p><? echo $t_msg . ' ' . $barcode_key; ?> </p></td>
			  										<td width="70" align="right"><p><? echo $roll_qnty; ?> </p></td>
			  										<td width="100"><p><? echo $gsm; ?> </p></td>
			  										<td width="100"><p><? echo $fdia; ?> </p></td>
			  										<td width="100"><p><? echo $slength; ?> </p></td>
			  										<td width="100">
			  											<p><? echo $machine_data[$machine_id]["dia_width"]; ?></p></td>
			  											<td width=""><p><? echo $machine_data[$machine_id]["gauge"]; ?></p></td>
			  										</tr>
			  										<?
			  										$total_roll_qnty += $roll_qnty;
			  										$i++;
			  										$j++;
			  										$x++;
			  										$y++;
			  										$z++;

			  									}
			  								}
			  							}
			  							$k++;
			  						}

			  					}
			  					?>
			  					<tfoot>
			  						<tr>
			  							<th colspan="7" align="right"> &nbsp;
			  								<th><? echo $total_roll_qnty; ?> </th>
			  								<th colspan="6">
			  									<?
			  									if ($tot_barcode != '') {
			  										$tot_barcode = array_unique(explode(",", $tot_barcode));
			  										$tot_barcodes = count($tot_barcode);
			  									} else {
			  										$tot_barcodes = 0;
			  									}
			  									?>
			  									<input type="hidden" id="tot_no_batch_td" class="text_boxes" style="width:30px"
			  									name="tot_no_batch_td" value="<? echo $tot_barcodes; ?> "></th>

			  								</tr>
			  							</tfoot>
			  						</table>
			  					</div>
			  				</fieldset>
			  			</div>
			  			<script>
			  				fnc_summary();
			  			</script>

			  		</div>

			  		<?

			  		foreach (glob("$user_id*.xls") as $filename) {
			  			if (@filemtime($filename) < (time() - $seconds_old))
			  				@unlink($filename);
			  		}
	//---------end------------//
			  		$name = time();
			  		$filename = $user_id . "_" . $name . ".xls";
			  		$create_new_doc = fopen($filename, 'w');
			  		$is_created = fwrite($create_new_doc, ob_get_contents());
			  		$filename = $user_id . "_" . $name . ".xls";
			  		echo "$total_data####$filename####$garph_caption####$garph_data";

			  		disconnect($con);
			  		exit();
			  	}

if ($action == "roll_popup") {
	echo load_html_head_contents("Roll Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$roll_id = str_replace("'", "", $roll_id);
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	?>
	<script>

	function js_set_val() {
	parent.emailwindow.hide();
	}

	</script>
	<fieldset style="width:1080px; margin-left:5px">
	<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0">
	<thead>
	<th width="30">SL</th>
	<th width="100">Program No/ Booing No</th>
	<th width="110">Production ID</th>
	<th width="80">Barcode NO</th>
	<th width="150">Knitting Party Name</th>
	<th width="70">Yarn Issue Ch. No</th>
	<th width="120">Body Part</th>
	<th width="70">Stitch Length</th>
	<th width="70">Yarn Count</th>
	<th width="70">Brand</th>
	<th width="70">Yarn Type</th>
	<th width="70">Lot</th>
	<th>Roll Qty</th>
	</thead>
	<?
	$i = 1;
	$total_qnty = 0;
	$sql = "SELECT a.recv_number, a.booking_no, a.knitting_source, a.knitting_company, a.yarn_issue_challan_no, b.body_part_id, b.stitch_length, c.barcode_no, c.roll_no, c.qnty as roll_qnty, d.id as prod_id, b.yarn_lot, b.yarn_count, b.brand_id, d.yarn_type
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id  and c.barcode_no = $roll_id and a.entry_form in(2) and c.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//and c.id=$roll_id
	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $row) {
	if ($i % 2 == 0)
		$bgcolor = "#E9F3FF";
	else
		$bgcolor = "#FFFFFF";

	$total_qnty += $row[csf('qnty')];
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"
		id="tr_<? echo $i; ?>">
		<td align="center"><? echo $i; ?></td>
		<td align="center"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
		<td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
		<td align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
		<td><p>
		<?
		if ($row[csf('knitting_source')] == 1) $knit_company = $company_arr[$row[csf('knitting_company')]];
		else   $knit_company = $supplier_arr[$row[csf('knitting_company')]];
		echo $knit_company;
		?>&nbsp;</p></td>
		<td><p><? echo $row[csf('yarn_issue_challan_no')]; ?>&nbsp;</p></td>
		<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</td>
		<td><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
		<td><p>
		<?
		$all_yarn_count_arr = array_unique(explode(",", $row[csf('yarn_count')]));
		$all_yarn_count = "";
		foreach ($all_yarn_count_arr as $y_cont_id) {
			$all_yarn_count .= $yarn_count_arr[$y_cont_id] . ",";
		}
		$all_yarn_count = chop($all_yarn_count, ",");
		echo $all_yarn_count;
		//echo $row[csf('yarn_count')];
		?>&nbsp;</p></td>
		<td align="center"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
		<td align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
		<td align="center"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
		<td align="right"><? echo number_format($row[csf('roll_qnty')], 2, '.', ''); ?>&nbsp;</td>
	</tr>
		<?
		$i++;
	}
	?>
	<tr>
		<td colspan="13" align="center"><input type="button" id="btn_close" class="formbutton"
			style="width:100px;" onClick="js_set_val()" value="Close"/></td>
	</tr>
	</table>
	</fieldset>
	<?
	exit();
}


if($action == "system_no_popup")
{
	echo load_html_head_contents("Roll Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	 if ($entry_form == 61)
	 {
	 	$sql = sql_select("select a.issue_number as sys_number, a.issue_date as system_date from inv_issue_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 61 and a.entry_form = 61 and b.is_returned =0 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form == 58)
	 {
	 	$sql = sql_select("select a.recv_number as sys_number, a.receive_date as system_date from inv_receive_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 58 and a.entry_form = 58 and b.is_returned =0 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form == 56){
	 	$sql = sql_select("select a.sys_number as sys_number, a.delevery_date as system_date from pro_grey_prod_delivery_mst a, pro_roll_details b where a.id = b.mst_id and b.entry_form =56 and a.entry_form = 56 and b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form ==62)
	 {
	 	$sql = sql_select("select  a.recv_number as sys_number, a.receive_date as system_date from inv_receive_mas_batchroll a , pro_roll_details b where a.id = b.mst_id and b.entry_form =62 and a.entry_form = 62 and b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form ==64)
	 {
	 	$sql = sql_select("select  a.batch_no as sys_number, a.batch_date as system_date from pro_batch_create_mst a , pro_roll_details b where a.id = b.mst_id and b.entry_form =64 and b.barcode_no = '$barcode_no' and a.status_active =1  and b.status_active =1 order by b.insert_date desc");
	 }

	?>
	<br>
	<fieldset style="width:240px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="240" cellpadding="0" cellspacing="0">
            <thead>
                <th width="120">System No</th>
                <th width="100">Date</th>
            </thead>
            <tbody>
            	<? foreach($sql as $row) {?>
            	<tr>
            		<td align="center"><? echo $row[csf("sys_number")];?></td>
            		<td align="center"><? echo $row[csf("system_date")];?></td>
            	</tr>
            	<?}?>
            </tbody>
        </table>
    </fieldset>
	<?
}
?>