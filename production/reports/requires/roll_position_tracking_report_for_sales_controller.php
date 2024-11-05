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
//---------------------------------------------------------------------------------------------------------

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
	$txt_internal_ref = str_replace("'", "", $txt_internal_ref);
	//txt_batch_no

	$roll_status = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');

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
		$trans_buyer = " and ((a.buyer_id=$cbo_buyer_name and a.within_group=2) or (a.po_buyer=$cbo_buyer_name and a.within_group=1))";
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

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=119");
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=119");
	oci_commit($con);

	if($txt_internal_ref!="")
	{
		/* echo "SELECT a.id, a.job_no, c.grouping from fabric_sales_order_mst a, wo_booking_dtls b left join wo_po_break_down c on b.po_break_down_id=c.id where a.sales_booking_no=b.booking_no and  c.grouping is not null and a.job_no_prefix_num like '%$txt_sales_order_no%' and c.grouping='$txt_internal_ref' group by a.id, a.job_no, c.grouping"; */
		$ref_sql = sql_select("SELECT a.id, a.job_no, c.grouping from fabric_sales_order_mst a, wo_booking_dtls b left join wo_po_break_down c on b.po_break_down_id=c.id where a.sales_booking_no=b.booking_no and  c.grouping is not null and a.job_no_prefix_num like '%$txt_sales_order_no%' and c.grouping='$txt_internal_ref' group by a.id, a.job_no, c.grouping");

		if(empty($ref_sql))
		{
			echo "Data Not Found.";
			die;
		}
		foreach ($ref_sql as $row) {
			$ref_fso_arr[$row[csf('id')]] .=$row[csf('grouping')].",";
			$serch_int_ref_fso[$row[csf('id')]]=$row[csf('id')];
		}
	}

	$sql_qc = sql_select("select id, pro_dtls_id, roll_status, barcode_no from pro_qc_result_mst where status_active=1 and is_deleted=0");
	$qc_status_arr = array();
	foreach($sql_qc as $row){
		$qc_status_arr[$row[csf('barcode_no')]]= $row[csf('roll_status')];
		echo $ro[csf('roll_status')];
	}
	unset($sql_qc);

	if(!empty($serch_int_ref_fso))
	{
		$sql="SELECT a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, c.determination_id as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, f.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		FROM fabric_sales_order_mst a, wo_booking_mst f,ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d, inv_receive_master b
		where a.sales_booking_no=f.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and d.mst_id=b.id and e.id=b.BOOKING_ID and c.DTLS_ID=b.BOOKING_ID and b.entry_form=2 and a.status_active=1 and a.is_deleted=0
		and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $booking_buyer and d.entry_form in(2) and a.id in (". implode(',',$serch_int_ref_fso) .")
		group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.determination_id, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, f.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		union all
		SELECT a.id,a.company_id,a.style_ref_no,a.sales_booking_no, a.job_no,c.body_part_id,c.febric_description_id as fabric_desc,null as dia, cast(c.width as varchar2(2000)) as fabric_dia,c.gsm as gsm_weight,d.roll_no, d.id as roll_id,d.roll_split_from,d.barcode_no,d.qnty as grey_qnty, (case when a.within_group = 2 then a.buyer_id when a.within_group =1 and f.id is not null then f.buyer_id when a.within_group = 1 and g.id is not null then g.buyer_id end) as buyer_id, c.machine_dia,cast(c.stitch_length as varchar2(2000)) as stitch_length,c.machine_gg,c.color_range_id,c.color_id,d.entry_form, 4 as basis, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number  
		FROM fabric_sales_order_mst a left join wo_booking_mst f on a.sales_booking_no =  f.booking_no $booking_buyer left join wo_non_ord_samp_booking_mst g on a.sales_booking_no =  g.booking_no $buyer_without_order , inv_receive_master b, pro_grey_prod_entry_dtls c,  pro_roll_details d 
		where a.job_no = b.booking_no and b.id = c.mst_id and b.id = d.mst_id and c.id  =  d.dtls_id and b.receive_basis = 4 and d.is_sales =1 and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond $sales_buyer and a.status_active = 1 and b.status_active=1 and c.status_active =1 and d.status_active =1 and a.id in (". implode(',',$serch_int_ref_fso) .")";

		$sql_po_transfer ="SELECT b.id as transfer_id, b.transfer_prefix_number,b.transfer_system_id,b.transfer_system_id as recv_number, b.insert_date, d.po_breakdown_id as po_id, d.roll_id, d.barcode_no, d.roll_no,d.qnty as grey_qnty,d.booking_without_order, a.buyer_id, a.po_buyer, a.within_group, a.job_no, a.sales_booking_no, c.to_body_part, c.body_part_id , d.qc_pass_qnty_pcs, d.coller_cuff_size, a.id, a.style_ref_no
		from inv_item_transfer_mst b, inv_item_transfer_dtls c, pro_roll_details d, fabric_sales_order_mst a
		where b.id = c.mst_id and c.id = d.dtls_id and b.id = d.mst_id and b.entry_form in (133) and d.entry_form in (133) and d.po_breakdown_id=a.id $bar_code_cond $style_ref_cond $trans_buyer $sql_cond and a.company_id=$company_name and d.status_active = 1 and d.is_deleted = 0 and d.re_transfer=0 and d.booking_without_order=0 and a.id in (". implode(',',$serch_int_ref_fso) .")";
	}
	else 
	{
		$sql="SELECT a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, c.determination_id as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, f.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		from fabric_sales_order_mst a, wo_booking_mst f,ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d, inv_receive_master b, pro_grey_prod_entry_dtls h
		where a.sales_booking_no=f.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and d.mst_id=b.id and e.id=b.BOOKING_ID and c.DTLS_ID=b.BOOKING_ID and b.id=h.MST_ID and d.dtls_id=h.id and c.body_part_id=h.BODY_PART_ID and a.status_active=1 and a.is_deleted=0 and b.entry_form=2
		and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $booking_buyer and d.entry_form in(2) and h.status_active=1 and h.is_deleted=0
		group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.determination_id, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, f.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number

		union all
		select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, c.determination_id as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, g.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		FROM fabric_sales_order_mst a, wo_non_ord_samp_booking_mst g, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d, inv_receive_master b, pro_grey_prod_entry_dtls h
		where a.sales_booking_no=g.booking_no and a.id=c.po_id and c.dtls_id=e.id and cast(c.dtls_id as varchar2(10))= d.booking_no and d.mst_id=b.id and e.id=b.BOOKING_ID and c.DTLS_ID=b.BOOKING_ID and b.id=h.MST_ID and d.dtls_id=h.id and c.body_part_id=h.BODY_PART_ID  and h.status_active=1 and h.is_deleted=0 and b.entry_form=2 and a.status_active=1 and a.is_deleted=0
		and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $buyer_without_order and d.entry_form in(2) group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.determination_id, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, g.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		union all
		select a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, c.determination_id as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as grey_qnty, a.buyer_id, e.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, e.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		FROM fabric_sales_order_mst a, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d ,inv_receive_master b, pro_grey_prod_entry_dtls h
		where  a.id=c.po_id and c.dtls_id=e.id and c.dtls_id = b.booking_id and d.mst_id = b.id and b.id=h.MST_ID and d.dtls_id=h.id and c.body_part_id=h.BODY_PART_ID  and h.status_active=1 and h.is_deleted=0 and b.entry_form =2 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond and d.is_sales=1 $sales_buyer and d.entry_form in(2) and a.within_group =2 group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.determination_id, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, a.buyer_id,e.machine_dia, e.stitch_length,e.machine_gg,e.color_range,e.color_id, d.entry_form, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number
		union all
		select a.id,a.company_id,a.style_ref_no,a.sales_booking_no, a.job_no,c.body_part_id,c.febric_description_id as fabric_desc,null as dia, cast(c.width as varchar2(2000)) as fabric_dia,c.gsm as gsm_weight,d.roll_no, d.id as roll_id,d.roll_split_from,d.barcode_no,d.qnty as grey_qnty,(case when a.within_group = 2 then a.buyer_id when a.within_group =1 and f.id is not null then f.buyer_id when a.within_group = 1 and g.id is not null then g.buyer_id end) as buyer_id, c.machine_dia,cast(c.stitch_length as varchar2(2000)) as stitch_length,c.machine_gg,c.color_range_id,c.color_id,d.entry_form, 4 as basis, d.qc_pass_qnty_pcs, d.coller_cuff_size, b.recv_number  
		FROM fabric_sales_order_mst a left join wo_booking_mst f on a.sales_booking_no =  f.booking_no $booking_buyer left join wo_non_ord_samp_booking_mst g on a.sales_booking_no =  g.booking_no $buyer_without_order , inv_receive_master b, pro_grey_prod_entry_dtls c,  pro_roll_details d 
		where a.job_no = b.booking_no and b.id = c.mst_id and b.id = d.mst_id and c.id  =  d.dtls_id and b.receive_basis = 4 and d.is_sales =1 and a.company_id=$company_name $sql_cond $year_cond $bar_code_cond $style_ref_cond $sales_buyer and a.status_active = 1 and b.status_active=1 and c.status_active =1 and d.status_active =1";

		$sql_po_transfer ="SELECT b.id as transfer_id, b.transfer_prefix_number,b.transfer_system_id,b.transfer_system_id as recv_number, b.insert_date, d.po_breakdown_id as po_id, d.roll_id, d.barcode_no, d.roll_no,d.qnty as grey_qnty,d.booking_without_order, a.buyer_id, a.po_buyer, a.within_group, a.job_no, a.sales_booking_no, c.to_body_part, c.body_part_id , d.qc_pass_qnty_pcs, d.coller_cuff_size , a.id, a.style_ref_no
		from inv_item_transfer_mst b, inv_item_transfer_dtls c, pro_roll_details d, fabric_sales_order_mst a
		where b.id = c.mst_id and c.id = d.dtls_id and b.id = d.mst_id and b.entry_form in (133) and d.entry_form in (133) and d.po_breakdown_id=a.id $bar_code_cond $style_ref_cond $trans_buyer $sql_cond and a.company_id=$company_name
		and d.status_active = 1 and d.is_deleted = 0 and d.re_transfer=0 and d.booking_without_order=0";
	}

 	// echo $sql_po_transfer;
 	$nameArray = sql_select($sql);

 	$transNameArray = sql_select($sql_po_transfer);

	if(empty($nameArray) && empty($transNameArray))
	{
		echo "data not found.";
		disconnect($con);
		die;
	}

	$all_barcode=array();

	foreach ($nameArray as $resval)
	{
		$all_barcode[$resval[csf("barcode_no")]] = $resval[csf("barcode_no")];
		$all_sales_order_arr[$resval[csf("id")]]=$resval[csf("id")];
	}

	foreach ($transNameArray as $resval)
	{
		$all_barcode[$resval[csf("barcode_no")]] = $resval[csf("barcode_no")];
		$all_sales_order_arr[$resval[csf("id")]]=$resval[csf("id")];
		$all_transfer_arr[$resval[csf("transfer_id")]]=$resval[csf("transfer_id")];
	}
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 119, 1, $all_sales_order_arr, $empty_arr);//FSO id Ref from=1

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 119, 2, $all_transfer_arr, $empty_arr);//FSO id Ref from=1

	$fso_ref_sql = sql_select("SELECT a.id, c.grouping from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c, GBL_TEMP_ENGINE d where a.sales_booking_no=b.booking_no and b.po_break_down_id=c.id and a.id=d.ref_val and d.user_id=$user_id and d.entry_form=119 and d.ref_from=1 group by a.id, c.grouping");

	foreach ($fso_ref_sql as $row) {
		$ref_fso_arr[$row[csf('id')]] .=$row[csf('grouping')].",";
	}

	$ack_transfer_sql = sql_select("SELECT b.challan_id, b.id as ack_id, b.insert_date from GBL_TEMP_ENGINE a, inv_item_trans_acknowledgement b where a.ref_val=b.challan_id and a.user_id=$user_id and a.entry_form=119 and a.ref_from=2 and b.status_active=1");

	foreach ($ack_transfer_sql as $row)
	{
		$transfer_ack_arr[$row[csf("challan_id")]]['ack_id']=$row[csf("ack_id")];
		$transfer_ack_arr[$row[csf("challan_id")]]['ack_date_time']=$row[csf("insert_date")];
	}

	$all_barcode_nos = implode(",",array_filter(array_unique($all_barcode)));


	foreach($all_barcode as $barcode)
	{
		$r_id=execute_query("insert into tmp_barcode_no (userid, entry_form, barcode_no) values ($user_id, 119,".$barcode.")");
		if($r_id)
		{
			$r_id=1;
		}
		else
		{
			echo "insert into tmp_barcode_no (userid, entry_form, barcode_no) values ($user_id, 119, ".$barcode.")";
			oci_rollback($con);
			die;
		}
	}

	oci_commit($con);

	$sql_po_transfer_out ="SELECT b.id as transfer_id, b.transfer_prefix_number,b.transfer_system_id, b.insert_date, d.po_breakdown_id as po_id, d.roll_id, d.barcode_no,
	d.roll_no,d.qnty as grey_qnty,d.booking_without_order
	from TMP_BARCODE_NO g, pro_roll_details d, inv_item_transfer_mst b, inv_item_transfer_dtls c
	where g.barcode_no=d.barcode_no and g.userid=$user_id and g.entry_form=119
	and b.id = c.mst_id and c.id = d.dtls_id and b.id = d.mst_id and b.entry_form in (133) and d.entry_form in (133) and c.TO_TRANS_ID=0 and d.status_active = 1 and d.is_deleted = 0 and d.re_transfer=1 and d.booking_without_order=0";
 	// echo $sql_po_transfer_out;die;
 	$transOutNameArray = sql_select($sql_po_transfer_out);
 	foreach ($transOutNameArray as $row)
	{
		$roll_transfer_id_and_date_arr[$row[csf("barcode_no")]] = $row[csf("transfer_prefix_number")].'<br>'.$row[csf("insert_date")];
	}
	// echo "<pre>";print_r($roll_transfer_id_and_date_arr);die;

	$barcode_ref_for_trans = sql_select("select p.color_range_id, p.body_part_id, a.barcode_no, p.febric_description_id, p.gsm, p.width as dia, p.stitch_length, p.machine_no_id, a.id as roll_id, a.roll_no, a.entry_form, a.insert_date, a.qnty as grey_qnty, p.color_id from pro_grey_prod_entry_dtls p, pro_roll_details a, tmp_barcode_no g where p.id=a.dtls_id and a.entry_form in(2,22) and a.roll_id=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no=g.barcode_no and g.userid=$user_id and g.entry_form=119 order by  a.id");

	foreach ($barcode_ref_for_trans as $val) {
		$transfered_barcode_ref[$val[csf("barcode_no")]]["color_range_id"] = $val[csf("color_range_id")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["body_part_id"] = $val[csf("body_part_id")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["febric_description_id"] = $val[csf("febric_description_id")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["gsm"] = $val[csf("gsm")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["dia"] = $val[csf("dia")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["stitch_length"] = $val[csf("stitch_length")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["machine_no_id"] = $val[csf("machine_no_id")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["roll_id"] = $val[csf("roll_id")];
		$transfered_barcode_ref[$val[csf("barcode_no")]]["color_id"] = $val[csf("color_id")];
	}


	/* if($all_barcode_nos=="") $all_barcode_nos=0;
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
	} */

	/* $barcode_cond_batch = "";
	if ($txt_barcode_no != "") $barcode_cond_batch = " and c.barcode_no='$txt_barcode_no'";
	$batch_sql = sql_select("select c.barcode_no, a.color_id from pro_batch_create_mst a, pro_roll_details c, tmp_barcode_no g where a.id=c.mst_id and a.entry_form=0 and c.entry_form=64 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.roll_id>0 $barcode_cond_batch and c.barcode_no=g.barcode_no and g.user_id=$user_id and g.entry_form=119");  //$all_barcode_cond_c
	$batch_color_data = array();
	foreach ($batch_sql as $row) {
		$batch_color_data[$row[csf("barcode_no")]] = $row[csf("color_id")];
	}
	unset($batch_sql); */

	$barcode_cond_prod = "";
	if ($txt_barcode_no != "") $barcode_cond_prod = " and barcode_no='$txt_barcode_no'";

	$position_sql = sql_select("SELECT a.barcode_no, a.po_breakdown_id,c.extention_no,
	max(case when a.entry_form=56 then a.barcode_no else 0 end) as grey_delivery,
	max(case when a.entry_form=56 then a.insert_date else null end) as grey_delivery_date,
	max(case when a.entry_form=58 then a.barcode_no else 0 end) as grey_rcv_store,
	max(case when a.entry_form=58 then a.insert_date else null end) as grey_rcv_store_date,
	max(case when a.entry_form=61 and a.is_returned=0 then  a.barcode_no else 0 end) as grey_issue_batch,
	sum(case when a.entry_form=61 and a.is_returned=0 then a.qnty else 0 end) as batch_issue_qnty,
	max(case when a.entry_form=61 and a.is_returned=0 then a.insert_date else null end) as grey_issue_batch_date,
	max(case when a.entry_form=62 then a.barcode_no else 0 end) as grey_rcv_batch,
	max(case when a.entry_form=62 then a.insert_date else null end) as grey_rcv_batch_date,
	max(case when a.entry_form=64 then a.barcode_no else 0 end) as batch_created,
	sum(case when a.entry_form=64 then a.qnty else 0 end) as batch_create_qnty,
	max(case when a.entry_form=64 then a.insert_date else null end) as batch_created_date,
	max(case when a.entry_form=66 then a.barcode_no else 0 end) as finishion,
	max(case when a.entry_form=66 then a.insert_date else null end) as finishion_date,
	sum(case when a.entry_form=66 then a.qc_pass_qnty else 0 end) as finishion_qnty,
	max(case when a.entry_form=67 then a.barcode_no else 0 end) as fin_delivery,
	max(case when a.entry_form=67 then a.insert_date else null end) as fin_delivery_date,
	max(case when a.entry_form=68 then a.barcode_no else 0 end) as fin_rcv_store,
	max(case when a.entry_form=68 then a.insert_date else null end) as fin_rcv_store_date,
	max(case when a.entry_form=71 then a.barcode_no else 0 end) as fin_issu_cut,
	max(case when a.entry_form=71 then a.insert_date else null end) as fin_issu_cut_date,
	max(case when a.entry_form=72 then a.barcode_no else 0 end) as fin_receive_cut,
	max(case when a.entry_form=72 then a.insert_date else null end) as fin_receive_cut_date
	from pro_roll_details a left join pro_batch_create_mst c on a.mst_id = c.id and a.entry_form = 64, tmp_barcode_no g
	where a.status_active=1 and a.is_deleted=0 and a.barcode_no>0 and a.barcode_no=g.barcode_no and g.userid=$user_id and g.entry_form=119
	group by a.barcode_no, a.po_breakdown_id,c.extention_no
	having c.extention_no is null");// and a.po_breakdown_id=1993 $all_barcode_cond

	$batch_issue_qtny_arr = $batch_creat_qnty_arr = $roll_data_arr = array();$roll_recv_data_arr = array();
	foreach ($position_sql as $row)
	{
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_delivery"] = $row[csf("grey_delivery")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_delivery_date"] = $row[csf("grey_delivery_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_store"] = $row[csf("grey_rcv_store")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_qnty"] = $row[csf("grey_rcv_qnty")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_store_date"] = $row[csf("grey_rcv_store_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_issue_batch"] = $row[csf("grey_issue_batch")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_issue_batch_date"] = $row[csf("grey_issue_batch_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch"] = $row[csf("grey_rcv_batch")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch_date"] = $row[csf("grey_rcv_batch_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["batch_created"] = $row[csf("batch_created")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["batch_created_date"] = $row[csf("batch_created_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["finishion"] = $row[csf("finishion")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["finishion_date"] = $row[csf("finishion_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["finishion_qnty"] = $row[csf("finishion_qnty")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["fin_delivery"] = $row[csf("fin_delivery")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["fin_delivery_date"] = $row[csf("fin_delivery_date")];

		$roll_recv_data_arr[$row[csf("barcode_no")]]["fin_rcv_store"] .= $row[csf("fin_rcv_store")].',';
		$roll_recv_data_arr[$row[csf("barcode_no")]]["fin_rcv_store_date"] .= $row[csf("fin_rcv_store_date")].',';

		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["fin_issu_cut"] = $row[csf("fin_issu_cut")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["fin_issu_cut_date"] = $row[csf("fin_issu_cut_date")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["fin_receive_cut"] = $row[csf("fin_receive_cut")];
		$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["fin_receive_cut_date"] = $row[csf("fin_receive_cut_date")];
		$batch_creat_qnty_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]] += $row[csf("batch_create_qnty")];

		if($row[csf("grey_issue_batch_date")] != "")
		{
			$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_issue_batch"] = $row[csf("grey_issue_batch")];
			$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_issue_batch_date"] = $row[csf("grey_issue_batch_date")];
			$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["batch_issue_qnty"] += $row[csf("batch_issue_qnty")];
			$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["batch_create_qnty"] += $row[csf("batch_create_qnty")];
		}

		if($row[csf("grey_rcv_batch_date")] != "")
		{
			$roll_data_arr_wo_fso_ref[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch"] = $row[csf("grey_rcv_batch")];
			$roll_data_arr_wo_fso_ref[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["grey_rcv_batch_date"] = $row[csf("grey_rcv_batch_date")];
		}

		if($row[csf("batch_created_date")] != "")
		{
			$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["batch_created"] = $row[csf("batch_created")];
			$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_breakdown_id")]]["batch_created_date"] = $row[csf("batch_created_date")];
		}

	}
	// echo "<pre>";print_r($roll_recv_data_arr);

	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	$machine_data = array();
	foreach ($machine_sql as $row) {
		$machine_data[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_data[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_data[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$sub_process_data = array();
	$dyeing_roll_sql = sql_select("select b.barcode_no from pro_fab_subprocess a, pro_batch_create_dtls b, tmp_barcode_no g where a.batch_id=b.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.roll_id>0 and a.status_active=1 and b.status_active=1 and b.barcode_no=g.barcode_no and g.userid=$user_id and g.entry_form=119");
	foreach ($dyeing_roll_sql as $row) {
		$sub_process_data[$row[csf("barcode_no")]][2] = $row[csf("barcode_no")];
	}
	//production_date as end_date end_hours,end_minutes
	$sub_process_sql = sql_select("select b.barcode_no,
		max(case when b.entry_page=30 then b.barcode_no else 0 end) as slitting_roll,
		max(case when b.entry_page=30 then a.production_date else null end) as slitting_roll_date,
		max(case when b.entry_page=30 then a.end_hours else null end) as slt_hours,
		max(case when b.entry_page=30 then a.end_minutes else null end) as slt_minutes,
		max(case when b.entry_page=31 then b.barcode_no else 0 end) as drying_roll,
		max(case when b.entry_page=31 then a.production_date else null end) as drying_roll_date,
		max(case when b.entry_page=31 then a.end_hours else null end) as dry_hours,
		max(case when b.entry_page=31 then a.end_minutes else null end) as dry_minutes,

		max(case when b.entry_page=32 then b.barcode_no else 0 end) as heat_roll,
		max(case when b.entry_page=32 then a.production_date else null end) as heat_roll_date,
		max(case when b.entry_page=32 then a.end_hours else null end) as heat_hours,
		max(case when b.entry_page=32 then a.end_minutes else null end) as heat_minutes,

		max(case when b.entry_page=33 then b.barcode_no else 0 end) as compaction_roll,
		max(case when b.entry_page=33 then a.production_date else null end) as compaction_roll_date,
		max(case when b.entry_page=33 then a.end_hours else null end) as com_hours,
		max(case when b.entry_page=33 then a.end_minutes else null end) as com_minutes,

		max(case when b.entry_page=34 then b.barcode_no else 0 end) as special_finish_roll,
		max(case when b.entry_page=34 then a.production_date else null end) as special_finish_roll_date,
		max(case when b.entry_page=34 then a.end_hours else null end) as sfin_hours,
		max(case when b.entry_page=34 then a.end_minutes else null end) as sfin_minutes,

		max(case when b.entry_page=35 and a.load_unload_id=2 then b.barcode_no else 0 end) as dyeing_roll,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.production_date else null end) as dyeing_roll_date,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.end_hours else null end) as dyeing_hours,
		max(case when b.entry_page=35 and a.load_unload_id=2 then a.end_minutes else null end) as dyeing_minutes,

		max(case when b.entry_page=48 then b.barcode_no else 0 end) as stentering_roll,
		max(case when b.entry_page=48 then a.production_date else null end) as stentering_roll_date,
		max(case when b.entry_page=48 then a.end_hours else null end) as sten_hours,
		max(case when b.entry_page=48 then a.end_minutes else null end) as sten_minutes

		from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and b.entry_page in(30,31,32,33,35,34,48) and b.roll_id>0 and b.status_active=1 and b.is_deleted=0 group by b.barcode_no");

	$sub_process_data = $sub_process_dateTime = array();
	foreach ($sub_process_sql as $row) {
		$sub_process_data[$row[csf("barcode_no")]][2] = $row[csf("dyeing_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][2] = $row[csf("dyeing_roll_date")] . '<br>' . $row[csf("dyeing_hours")] . ':' . $row[csf("dyeing_minutes")];

		$sub_process_data[$row[csf("barcode_no")]][1] = $row[csf("heat_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][1] = $row[csf("heat_roll_date")] . '<br>' . $row[csf("heat_hours")] . ':' . $row[csf("heat_minutes")];
		$sub_process_data[$row[csf("barcode_no")]][3] = $row[csf("slitting_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][3] = $row[csf("slitting_roll_date")] . '<br>' . $row[csf("slt_hours")] . ':' . $row[csf("slt_minutes")];
		$sub_process_data[$row[csf("barcode_no")]][4] = $row[csf("stentering_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][4] = $row[csf("stentering_roll_date")] . '<br>' . $row[csf("sten_hours")] . ':' . $row[csf("sten_minutes")];
		$sub_process_data[$row[csf("barcode_no")]][5] = $row[csf("drying_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][5] = $row[csf("drying_roll_date")] . '<br>' . $row[csf("dry_hours")] . ':' . $row[csf("dry_minutes")];
		$sub_process_data[$row[csf("barcode_no")]][6] = $row[csf("special_finish_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][6] = $row[csf("special_finish_roll_date")] . '<br>' . $row[csf("sfin_hours")] . ':' . $row[csf("sfin_hours")];
		$sub_process_data[$row[csf("barcode_no")]][7] = $row[csf("compaction_roll")];
		$sub_process_dateTime[$row[csf("barcode_no")]][7] = $row[csf("compaction_roll_date")] . '<br>' . $row[csf("com_hours")] . ':' . $row[csf("com_hours")];
	}

	$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent, a.id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1");
	foreach ($determination_sql as $val)
	{

		if($val[csf("construction")] != "")
		{
			$fabric_desc_arr[$val[csf("id")]] = $val[csf("construction")]. ", ";
		}
		$fabric_desc_arr[$val[csf("id")]] .= $composition[$val[csf("copmposition_id")]]. " " .$val[csf("percent")] . "% ";
	}

	foreach ($nameArray as $row)
	{
		$fabric_description =  $fabric_desc_arr[$row[csf("fabric_desc")]];

		$roll_id = ($row[csf("roll_split_from")]>0)?$row[csf("roll_split_from")]:$row[csf("roll_id")];

		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["grey_qnty"] += $row[csf("grey_qnty")];

		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_issue_qtny"] += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_issue_qnty"];

		$summary_data[$row[csf("color_range_id")]][$row[csf("body_part_id")]][$fabric_description][$row[csf("gsm_weight")]][$row[csf("fabric_dia")]]["batch_creat_qnty"] += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_create_qnty"];

		$garph_data[1] += $row[csf("grey_qnty")];
		$garph_caption[1] = "Grey Wgt";

		if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_delivery"] > 0) {
			$garph_data[2] += $row[csf("grey_qnty")];
			$garph_caption[2] = "Delv. To Store";
		} else {
			$garph_data[2] += 0;
			$garph_caption[2] = "Delv. To Store";
		}
		if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_store"] > 0) {
			//$garph_data[3] += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_qnty"];
			$garph_data[3] += $row[csf("grey_qnty")];
			$garph_caption[3] = "Recv. by Store";
		} else {
			$garph_data[3] += 0;
			$garph_caption[3] = "Recv. by Store";
		}

		if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_issue_batch"] > 0) {
			$garph_data[4] += $row[csf("grey_qnty")];
			$garph_caption[4] = "Issue to Batch";
		} else {
			$garph_data[4] += 0;
			$garph_caption[4] = "Issue to Batch";
		}

		if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
			if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_batch"] > 0) {
				$garph_data[5] += $row[csf("grey_qnty")];
				$garph_caption[5] = "Recv. by Batch";
			} else {
				$garph_data[5] += 0;
				$garph_caption[5] = "Recv. by Batch";
			}
			if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_created"] > 0) {
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
				if ($sub_process_data[$row[csf("barcode_no")]][$i] > 0) {
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

	foreach ($transNameArray as $row)
	{
		$color_range_id = $transfered_barcode_ref[$row[csf("barcode_no")]]["color_range_id"];
		$febric_description_id = $fabric_desc_arr[$transfered_barcode_ref[$row[csf("barcode_no")]]["febric_description_id"]];
		$gsm_weight = $transfered_barcode_ref[$row[csf("barcode_no")]]["gsm"];
		$fabric_dia = $transfered_barcode_ref[$row[csf("barcode_no")]]["dia"];
		$stitch_length = $transfered_barcode_ref[$row[csf("barcode_no")]]["stitch_length"];
		$machine_no_id = $transfered_barcode_ref[$row[csf("barcode_no")]]["machine_no_id"];
		$machine_dia_width = $machine_data[$machine_no_id]["dia_width"];
		$machine_gauge = $machine_data[$machine_no_id]["gauge"];

		$fabric_description = $fabric_desc_arr[$transfered_barcode_ref[$row[csf("barcode_no")]]["febric_description_id"]];

		$summary_data[$color_range_id][$row[csf("body_part_id")]][$fabric_description][$gsm_weight][$fabric_dia]["grey_qnty"] += $row[csf("grey_qnty")];
		$summary_data[$color_range_id][$row[csf("body_part_id")]][$fabric_description][$gsm_weight][$fabric_dia]["batch_issue_qtny"] += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_issue_qnty"];
		$summary_data[$color_range_id][$row[csf("body_part_id")]][$fabric_description][$gsm_weight][$fabric_dia]["batch_creat_qnty"] += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_create_qnty"];



		if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["grey_issue_batch"] > 0) {
			$garph_data[4] += $row[csf("grey_qnty")];
			$garph_caption[4] = "Issue to Batch";
		} else {
			$garph_data[4] += 0;
			$garph_caption[4] = "Issue to Batch";
		}

		if ($variable_data_arr[50]["fabric_roll_level"] == 1) {
			if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["grey_rcv_batch"] > 0) {
				$garph_data[5] += $row[csf("grey_qnty")];
				$garph_caption[5] = "Recv. by Batch";
			} else {
				$garph_data[5] += 0;
				$garph_caption[5] = "Recv. by Batch";
			}
			if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_created"] > 0) {
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
				if ($sub_process_data[$row[csf("barcode_no")]][$i] > 0) {
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

	/* echo "<pre>";
	print_r($summary_data);
	echo "</pre>"; */

	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=119");
	oci_commit($con);
	disconnect($con);

	$div_width = 2250;
	$table_width = 2220;
	$coll_span = 18;
	if ($variable_data_arr[50]["fabric_roll_level"] == 1)
	{
		$div_width = $div_width + 140;
		$table_width = $table_width + 140;
		$coll_span = $coll_span + 2;
	}
	if ($variable_data_arr[50]["page_upto_id"] > 0)
	{
		$div_width = $div_width + (70 * $variable_data_arr[50]["page_upto_id"]);
		$table_width = $table_width + (70 * $variable_data_arr[50]["page_upto_id"]);
		$coll_span = $coll_span + $variable_data_arr[50]["page_upto_id"];
	}
	if ($variable_data_arr[2]["fabric_roll_level"] == 1)
	{
		$div_width = $div_width + 570;//490
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

			<table cellspacing="0" cellpadding="0" border="1" rules="all"  width="<? echo $table_width; ?>"
				class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">PO Buyer</th>
						<th width="120">FSO No & Sty Ref</th>
						<th width="120">Booking No</th>
						<th width="100">Internal ref.</th>
						<th width="60">Roll No</th>
						<th width="100">Production ID</th>
						<th width="100">Barcode No</th>
						<th width="100">Color Range</th>
						<th width="100">Body Part</th>
						<th width="100">Color</th>
						<th width="170">Fabric Description</th>
						<th width="60">GSM</th>
						<th width="60">Dia</th>
						<th width="60">QC Pass</th>
						<th width="60">Stich Lenth</th>
						<th width="60">Machine Dia</th>
						<th width="60">Guage</th>
						<th width="60">Collar Cuff Size</th>
						<th width="80">Pcs Qty</th>
						<th width="80">Grey Wgt.</th>
						<th width="70">Delv. To Store</th>
						<th width="70">Recv. by Store</th>
						<th width="100"><p>Roll Transfer<br>ID and Date</p></th>
						<th width="100"><p>Roll Transfer<br>Acknowledge<br>ID and Date</p></th>
						<th width="70">Issue to Batch</th>
						<?
						if ($variable_data_arr[50]["fabric_roll_level"] == 1)
						{
							?>
							<th width="70">Recv. by Batch</th>
							<th width="70">Batch Create</th>
							<?
						}
						if ($variable_data_arr[50]["page_upto_id"] > 0)
						{
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++)
							{
								?>
								<th width="70"><p><? echo $upto_receive_batch[$i]; ?></p></th>
								<?
							}
						}
						if ($variable_data_arr[2]["fabric_roll_level"] == 1)
						{
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
			<div style="width:<? echo $table_width; ?>px; overflow-y:scroll; max-height:330px; margin-left: 0px; float: left;" id="scroll_body"  >
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" align="left" id="table_body"  >
					<?
					$m = 1;
					$tot_grey_delivery = $tot_grey_rcv_store = $tot_grey_issue_batch = $tot_grey_rcv_batch = $tot_batch_created = $total_finishing_qnty=$tot_fin_delivery = $tot_fin_rcv_store = $tot_fin_issu_cut = $total_qc_pass_qnty_pcs = 0;
					foreach ($nameArray as $row)
					{
						if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$grey_delivery_bgcolor = $grey_rcv_store_bgcolor = $grey_issue_batch_bgcolor = $grey_rcv_batch_bgcolor = $batch_created_bgcolor = $finishion_bgcolor = $fin_delivery_bgcolor = $fin_rcv_store_bgcolor = $fin_rcv_store_bgcolor = '';

						$grey_delivery_date = $grey_rcv_store_date = $grey_issue_batch_day = $grey_rcv_batch_day = $batch_created_day = $finishion_day = $fin_delivery_day = $fin_rcv_store_day = $fin_issu_cut_day = "";

						$roll_id = ($row[csf("roll_split_from")]>0)?$row[csf("roll_split_from")]:$row[csf("roll_id")];

						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_delivery"] > 0) {
							$grey_delivery_bgcolor = 'bgcolor="green"';
							$grey_delivery_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_delivery_date"];
							$tot_grey_delivery += $row[csf("grey_qnty")];
						}

						$transfer_no_and_date=$roll_transfer_id_and_date_arr[$row[csf("barcode_no")]];

						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_store"] > 0) {
							$grey_rcv_store_bgcolor = 'bgcolor="green"';
							$grey_rcv_store_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_store_date"];
							$tot_grey_rcv_store += $row[csf("grey_qnty")];
						}

						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion"] > 0) {
							$finishion_bgcolor = 'bgcolor="green"';
							$finishion_day = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion_date"];
							$total_finishing_qnty += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion_qnty"];
						}

						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["fin_delivery"] > 0) {
							$fin_delivery_bgcolor = 'bgcolor="green"';
							$fin_delivery_day = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["fin_delivery_date"];
						}

						$fin_rcv_store=implode(',', array_filter(explode(',', $roll_recv_data_arr[$row[csf("barcode_no")]]["fin_rcv_store"])));
						if ($fin_rcv_store > 0) {
							$fin_rcv_store_bgcolor = 'bgcolor="green"';
							$fin_rcv_store_day = implode(',', array_filter(explode(',', $roll_recv_data_arr[$row[csf("barcode_no")]]["fin_rcv_store_date"])));
						}


						$grey_issue_batch_date="";
						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_issue_batch"] > 0) {

							$grey_issue_batch_bgcolor = 'bgcolor="green"';
							$grey_issue_batch_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_issue_batch_date"];
							$tot_grey_issue_batch += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_issue_qnty"];
						}

						$grey_rcv_batch_date="";
						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_batch"] > 0) {
							$grey_rcv_batch_bgcolor = 'bgcolor="green"';
							$grey_rcv_batch_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_create_qnty"];
						}

						$batch_created_date="";
						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_created"] > 0) {
							$batch_created_bgcolor = 'bgcolor="green"';
							$batch_created_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["batch_created_date"];
							$tot_batch_created += $row[csf("grey_qnty")];
						}

						$sub_process_bgcolor = $sub_process_day = array();
						if ($variable_data_arr[50]["page_upto_id"] > 0) {
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								if ($sub_process_data[$row[csf("barcode_no")]][$i] > 0) {
									$sub_process_bgcolor[$i] = 'bgcolor="green"';
									$sub_process_day[$i] = $sub_process_dateTime[$row[csf("barcode_no")]][$i];
									$tot_sub_process[$i] += $row[csf("grey_qnty")];
								}
							}
						}
						$color_names = '';
						$colors = explode(",", $row[csf("color_id")]);
						foreach ($colors as $color) {
							$color_names .= $color_library[$color] . ",";
						}

						if($row[csf("basis")] == 4)
						{
							$fabric_description =  $fabric_desc_arr[$row[csf("fabric_desc")]];
						}else{
							$fabric_description =  $row[csf("fabric_desc")];
						}

						if ($row[csf("entry_form")] == 2) $roll_entry_form = "Production"; else $roll_entry_form = "Receive";

						$internalRef = implode(",",array_unique(explode(",",chop($ref_fso_arr[$row[csf("id")]],","))));

						?>
						<tr bgcolor="<? //echo $bgcolor; ?>"
							onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
							<td width="30" align="center"><? echo $m; ?></td>
							<td width="100" align="center"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $row[csf("job_no")] . "<br>" . $row[csf("style_ref_no")]; ?></p></td>
							<td width="120"><p><? echo $row[csf("sales_booking_no")]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $internalRef; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><a href="##" onClick="openmypage_popup('<? echo $row[csf("barcode_no")];//$roll_id;?>','roll_popup')"><? echo $row[csf("roll_no")]; ?></a></p></td>
							<td width="100" align="center"><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $row[csf("barcode_no")]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $color_range[$row[csf("color_range_id")]]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo rtrim($color_names, ","); ?>&nbsp;</p></td>
							<td width="170" align="center"><p><? echo $fabric_description;//$row[csf("fabric_desc")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $row[csf("gsm_weight")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $row[csf("fabric_dia")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $roll_status[$qc_status_arr[$row[csf("barcode_no")]]]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $row[csf("stitch_length")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $row[csf("machine_dia")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $row[csf("machine_gg")]; ?>&nbsp;</p></td>

							<td width="60" align="center"><p><? echo $row[csf("coller_cuff_size")]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row[csf("qc_pass_qnty_pcs")]; $total_qc_pass_qnty_pcs += $row[csf("qc_pass_qnty_pcs")];?>&nbsp;</p></td>

							<td width="80" align="right" title="<? echo $roll_entry_form . "**" . $row[csf("barcode_no")] . "**" . $row[csf("po_id")]; ?>"><a href="##" onclick="openmypage_sys_no('2','<? echo $row[csf("barcode_no")];?>')"><? echo number_format($row[csf("grey_qnty")], 2);
							$total_grey_qnty += $row[csf("grey_qnty")]; ?></a></td>

							<td width="70" align="center" style="word-break:break-all;"
							valign="middle" <? echo $grey_delivery_bgcolor; ?>><a href="##" class="font_yellow_color" onclick="openmypage_sys_no('56','<? echo $row[csf("barcode_no")];?>')"><? echo $grey_delivery_date; ?></a></td>

							<td width="70" align="center" style="word-break:break-all;"
							valign="middle" <? echo $grey_rcv_store_bgcolor; ?>>
							<a href="##" class="font_yellow_color" onclick="openmypage_sys_no('58','<? echo $row[csf("barcode_no")];?>')"><? echo $grey_rcv_store_date; ?></a>
							</td>

							<td width="100" align="center" style="word-break:break-all;" valign="middle">
							<p><a href="##" onclick="openmypage_sys_no('T133','<? echo $row[csf("barcode_no")];?>')"><? echo $transfer_no_and_date; ?></a></p>
							</td>
							<td width="100"></td>
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

							if ($variable_data_arr[2]["fabric_roll_level"] == 1)
							{
								?>
								<td width="70" title="<? echo $row[csf("id")]; ?>" style="word-break:break-all" align="center"
								valign="middle" <? echo $finishion_bgcolor; ?>><? echo $finishion_day; ?></td>
								<td width="70" title="<? echo $row[csf("id")]; ?>"
								align="right"><? if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion"] > 0) echo number_format($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion_qnty"], 2);
								$total_finishing_qnty += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion_qnty"]; ?></td>
								<td width="70" align="right" title="<? echo $row[csf("id")]; ?>">
									<?
									$processes_loss = 0;
									$processes_loss = $row[csf("grey_qnty")] - $roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion_qnty"];
									if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("id")]]["finishion_qnty"] > 0) {
										echo number_format($processes_loss, 2);
										$total_processes_loss += $processes_loss;
									}
									?>
								</td>
								<td width="70" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_delivery_bgcolor; ?>><? echo $fin_delivery_day; ?></td>
								<td width="70" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_rcv_store_bgcolor; ?>><? echo $fin_rcv_store_day; ?></td>
								<td width="70" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_issu_cut_bgcolor; ?>><? echo $fin_issu_cut_day; ?></td>
								<td width="" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_receive_cut_bgcolor; ?>><? echo $fin_receive_cut_day; ?></td>
								<?
							}
							?>
						</tr>
						<?
						$m++;
					}


					foreach ($transNameArray as $row)
					{
						if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$grey_issue_batch_bgcolor = $grey_rcv_batch_bgcolor = $batch_created_bgcolor = $finishion_bgcolor = $fin_delivery_bgcolor = $fin_rcv_store_bgcolor = $fin_rcv_store_bgcolor = '';

						$grey_delivery_date = $grey_rcv_store_date = $grey_issue_batch_day = $grey_rcv_batch_day = $batch_created_day = $finishion_day = $fin_delivery_day = $fin_rcv_store_day = $fin_issu_cut_day = "";

						$roll_id = ($row[csf("roll_split_from")]>0)?$row[csf("roll_split_from")]:$row[csf("roll_id")];


						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion"] > 0) {
							$finishion_bgcolor = 'bgcolor="green"';
							$finishion_day = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion_date"];
							$total_finishing_qnty += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion_qnty"];
						}

						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["fin_delivery"] > 0) {
							$fin_delivery_bgcolor = 'bgcolor="green"';
							$fin_delivery_day = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["fin_delivery_date"];
						}

						$fin_rcv_store=implode(',', array_filter(explode(',', $roll_recv_data_arr[$row[csf("barcode_no")]]["fin_rcv_store"])));
						if ($fin_rcv_store > 0) {
							$fin_rcv_store_bgcolor = 'bgcolor="green"';
							$fin_rcv_store_day = implode(',', array_filter(explode(',', $roll_recv_data_arr[$row[csf("barcode_no")]]["fin_rcv_store_date"])));
						}


						$grey_issue_batch_date="";
						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["grey_issue_batch"] > 0) {

							$grey_issue_batch_bgcolor = 'bgcolor="green"';
							$grey_issue_batch_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["grey_issue_batch_date"];
							$tot_grey_issue_batch += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_issue_qnty"];
						}

						$grey_rcv_batch_date="";
						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["grey_rcv_batch"] > 0) {
							$grey_rcv_batch_bgcolor = 'bgcolor="green"';
							$grey_rcv_batch_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["grey_rcv_batch_date"];
							$tot_grey_rcv_batch += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_create_qnty"];
						}

						$batch_created_date="";
						if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_created"] > 0) {
							$batch_created_bgcolor = 'bgcolor="green"';
							$batch_created_date = $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["batch_created_date"];
							$tot_batch_created += $row[csf("grey_qnty")];
						}

						$sub_process_bgcolor = $sub_process_day = array();
						if ($variable_data_arr[50]["page_upto_id"] > 0) {
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								if ($sub_process_data[$row[csf("barcode_no")]][$i] > 0) {
									$sub_process_bgcolor[$i] = 'bgcolor="green"';
									$sub_process_day[$i] = $sub_process_dateTime[$row[csf("barcode_no")]][$i];
									$tot_sub_process[$i] += $row[csf("grey_qnty")];
								}
							}
						}
						$color_names = '';
						$colors = explode(",", $transfered_barcode_ref[$row[csf("barcode_no")]]["color_id"]);
						foreach ($colors as $color) {
							$color_names .= $color_library[$color] . ",";
						}


						$roll_entry_form = "Transfer";

						$internalRef = implode(",",array_unique(explode(",",chop($ref_fso_arr[$row[csf("po_id")]],","))));

						if($row[csf("within_group")]==2)
						{
							$buyer_id=$row[csf("buyer_id")];
						}
						else
						{
							$buyer_id=$row[csf("po_buyer")];
						}

						if($row[csf("to_body_part")])
						{
							$body_part_id=$row[csf("to_body_part")];
						}
						else
						{
							$body_part_id=$row[csf("body_part_id")];
						}



						$color_range_id = $transfered_barcode_ref[$row[csf("barcode_no")]]["color_range_id"];
						$febric_description_id = $fabric_desc_arr[$transfered_barcode_ref[$row[csf("barcode_no")]]["febric_description_id"]];
						$gsm_weight = $transfered_barcode_ref[$row[csf("barcode_no")]]["gsm"];
						$fabric_dia = $transfered_barcode_ref[$row[csf("barcode_no")]]["dia"];
						$stitch_length = $transfered_barcode_ref[$row[csf("barcode_no")]]["stitch_length"];
						$machine_no_id = $transfered_barcode_ref[$row[csf("barcode_no")]]["machine_no_id"];
						$roll_id = $transfered_barcode_ref[$row[csf("barcode_no")]]["roll_id"];

						$machine_dia_width = $machine_data[$machine_no_id]["dia_width"];
						$machine_gauge = $machine_data[$machine_no_id]["gauge"];

						$ack_id=$transfer_ack_arr[$row[csf("transfer_id")]]['ack_id'];
						$ack_date_time=$transfer_ack_arr[$row[csf("transfer_id")]]['ack_date_time'];

						?>
						<tr bgcolor="<? //echo $bgcolor; ?>"
							onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
							<td width="30" align="center"><? echo $m; ?></td>
							<td width="100" align="center"><p><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $row[csf("job_no")] . "<br>" . $row[csf("style_ref_no")]; ?></p></td>
							<td width="120"><p><? echo $row[csf("sales_booking_no")]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $internalRef; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><a href="##" onClick="openmypage_popup('<? echo $row[csf("barcode_no")];//$roll_id;?>','roll_popup')"><? echo $row[csf("roll_no")]; ?></a></p></td>
							<td width="100" align="center"><p><? echo $row[csf("recv_number")]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $row[csf("barcode_no")].' (T)'; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $color_range[$color_range_id]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? echo rtrim($color_names, ","); ?>&nbsp;</p></td>
							<td width="170" align="center"><p><? echo $fabric_description;//$row[csf("fabric_desc")]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $gsm_weight; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $fabric_dia; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $roll_status[$qc_status_arr[$row[csf("barcode_no")]]]; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $machine_dia_width; ?>&nbsp;</p></td>
							<td width="60" align="center"><p><? echo $machine_gauge; ?>&nbsp;</p></td>

							<td width="60" align="center"><p><? echo $row[csf("coller_cuff_size")]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $row[csf("qc_pass_qnty_pcs")]; $total_qc_pass_qnty_pcs += $row[csf("qc_pass_qnty_pcs")];?>&nbsp;</p></td>

							<td width="80" align="right" title="<? echo $roll_entry_form . "**" . $row[csf("barcode_no")] . "**" . $row[csf("po_id")]; ?>" >
							<a href="##" onclick="openmypage_sys_no('133','<? echo $row[csf("barcode_no")];?>')">
							<?
								echo number_format($row[csf("grey_qnty")], 2); 	//$total_grey_qnty += $row[csf("grey_qnty")];
							?>
							</a>
							</td>

							<td width="70" align="center" style="word-break:break-all;"
							valign="middle" <? echo $grey_delivery_bgcolor; ?>><? //echo $grey_delivery_date; ?></td>

							<td width="70" align="center" style="word-break:break-all;"
							valign="middle" <? echo $grey_rcv_store_bgcolor; ?>>
							<a href="##" class="font_yellow_color"><? //echo $grey_rcv_store_date; ?></a>
							</td>
							<td width="100" align="center" style="word-break:break-all;"
							valign="middle" <? //echo $grey_rcv_store_bgcolor; ?>>
							<a href="##" onclick="openmypage_sys_no('133','<? echo $row[csf("barcode_no")];?>')"><? echo $row[csf("transfer_prefix_number")].'<br>'.$row[csf("insert_date")]; ?></a>
							</td>
							<td width="100" align="center" style="word-break:break-all;"
							valign="middle" <? //echo $grey_rcv_store_bgcolor; ?>>
							<a href="##" onclick="openmypage_sys_no('A133','<? echo $ack_id;?>')"><? echo $ack_id.'<br>'.$ack_date_time; ?></a>
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

							if ($variable_data_arr[2]["fabric_roll_level"] == 1)
							{
								?>
								<td width="70" title="<? echo $row[csf("id")]; ?>" style="word-break:break-all" align="center"
								valign="middle" <? echo $finishion_bgcolor; ?>><? echo $finishion_day; ?></td>
								<td width="70" title="<? echo $row[csf("id")]; ?>"
								align="right"><? if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion"] > 0) echo number_format($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion_qnty"], 2);
								$total_finishing_qnty += $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion_qnty"]; ?></td>
								<td width="70" align="right" title="<? echo $row[csf("po_id")]; ?>">
									<?
									$processes_loss = 0;
									$processes_loss = $row[csf("grey_qnty")] - $roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion_qnty"];
									if ($roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]]["finishion_qnty"] > 0) {
										echo number_format($processes_loss, 2);
										$total_processes_loss += $processes_loss;
									}
									?>
								</td>
								<td width="70" align="center" title="<? echo $row[csf("po_id")]; ?>"
								valign="right" <? echo $fin_delivery_bgcolor; ?>><? echo $fin_delivery_day; ?></td>
								<td width="70" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_rcv_store_bgcolor; ?>><? echo $fin_rcv_store_day; ?></td>
								<td width="70" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_issu_cut_bgcolor; ?>><? echo $fin_issu_cut_day; ?></td>
								<td width="" align="center" title="<? echo $row[csf("id")]; ?>"
								valign="right" <? echo $fin_receive_cut_bgcolor; ?>><? echo $fin_receive_cut_day; ?></td>
								<?
							}
							?>
						</tr>
						<?
						$m++;
					}
					?>
				</table>
			</div>
			<table  cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" id="rpt_table_footer" align="left">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="170">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60"><b>Total:</b></th>
						<th width="80" align="right"><? echo number_format($total_qc_pass_qnty_pcs, 2); ?></th>
						<th width="80" align="right"
						id="value_total_grey_qnty"><? echo number_format($total_grey_qnty, 2); ?></th>
						<th width="70" align="right"><? echo number_format($tot_grey_delivery, 2);
						$pending_grey_delivery = $total_grey_qnty - $tot_grey_delivery; ?></th>
						<th width="70" align="right"><? echo number_format($tot_grey_rcv_store, 2);
						$pending_grey_rcv_store = $tot_grey_delivery - $tot_grey_rcv_store; ?></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="70" align="right"><? echo number_format($tot_grey_issue_batch, 2);
						$pending_grey_issue_batch = $tot_grey_rcv_store - $tot_grey_issue_batch; ?></th>
						<?
						if ($variable_data_arr[50]["fabric_roll_level"] == 1)
						{
							?>
							<th width="70" align="right"><? echo number_format($tot_grey_rcv_batch, 2);
							$pending_grey_rcv_batch = $tot_grey_issue_batch - $tot_grey_rcv_batch; ?></th>
							<th width="70" align="right"><? echo number_format($tot_batch_created, 2);
							$pending_batch_created = $tot_grey_rcv_batch - $tot_batch_created; ?></th>
							<?
						}
						if ($variable_data_arr[50]["page_upto_id"] > 0)
						{
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

						if ($variable_data_arr[2]["fabric_roll_level"] == 1)
						{
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
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="170">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60" align="right">Pending:</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0, 2); ?></th>
						<th width="70" align="right"><? echo number_format($pending_grey_delivery, 2); ?></th>
						<th width="70" align="right"><? echo number_format($pending_grey_rcv_store, 2); ?></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="70" align="right"><? echo number_format($pending_grey_issue_batch, 2); ?></th>
						<?
						if ($variable_data_arr[50]["fabric_roll_level"] == 1)
						{
							?>
							<th width="70" align="right"><? echo number_format($pending_grey_rcv_batch, 2); ?></th>
							<th width="70" align="right"><? echo number_format($pending_batch_created, 2); ?></th>
							<?
						}
						if ($variable_data_arr[50]["page_upto_id"] > 0)
						{
							for ($i = 1; $i <= $variable_data_arr[50]["page_upto_id"]; $i++) {
								?>
								<th width="70" align="right"><? echo number_format($pending_sub_process[$i], 2); ?></th>
								<?
							}
						}

						if ($variable_data_arr[2]["fabric_roll_level"] == 1)
						{
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
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="170">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60" align="right"><p>Pending%:</p></th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right" id="value_total_grey_qnty"><? echo number_format(0, 2); ?></th>
						<th width="70"
						align="right"><? echo number_format(($pending_grey_delivery / $total_grey_qnty) * 100, 2); ?></th>
						<th width="70"
						align="right"><? echo number_format(($pending_grey_rcv_store / $tot_grey_delivery) * 100, 2); ?></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="70" align="right"><? echo number_format(($pending_grey_issue_batch / $tot_grey_rcv_store) * 100, 2); ?></th>

						<?
						if ($variable_data_arr[50]["fabric_roll_level"] == 1)
						{
							?>
							<th width="70"
							align="right"><? echo number_format(($pending_grey_rcv_batch / $tot_grey_issue_batch) * 100, 2); ?></th>
							<th width="70"
							align="right"><? echo number_format(($pending_batch_created / $tot_grey_rcv_batch) * 100, 2); ?></th>
							<?
						}
						if ($variable_data_arr[50]["page_upto_id"] > 0)
						{
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

						if ($variable_data_arr[2]["fabric_roll_level"] == 1)
						{
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
	$width="240px";
	 if ($entry_form == 61)
	 {
	 	$sql = sql_select("select a.issue_number as sys_number, a.issue_date as system_date from inv_issue_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 61 and a.entry_form = 61 and b.is_returned =0 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form == 2)
	 {
	 	$sql = sql_select("SELECT a.recv_number as sys_number, a.receive_date as system_date from inv_receive_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 2 and a.entry_form = 2 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
	 }
	 else if ($entry_form == 58)
	 {
	 	$sql = sql_select("SELECT a.recv_number as sys_number, a.receive_date as system_date from inv_receive_master a, pro_roll_details b where a.id = b.mst_id and b.entry_form = 58 and a.entry_form = 58 and b.barcode_no = '$barcode_no' and a.status_active =1 and b.status_active =1 order by b.insert_date desc");
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
	 else if ($entry_form==133)
	 {
		$sql = sql_select("SELECT b.transfer_system_id as sys_number, b.transfer_date as system_date, d.qnty, c.to_store, c.to_floor_id, c.to_room, c.to_rack, c.to_shelf, c.to_bin_box from inv_item_transfer_mst b, inv_item_transfer_dtls c, pro_roll_details d, fabric_sales_order_mst a where b.id = c.mst_id and c.id = d.dtls_id and b.id = d.mst_id and b.entry_form in (133) and d.entry_form in (133) and d.po_breakdown_id=a.id and a.company_id=1 and d.status_active = 1 and d.is_deleted = 0 and d.re_transfer=0 and d.booking_without_order=0 and d.barcode_no='$barcode_no'");
		$width="700px";
		$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
		$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
	 }
	 else if ($entry_form =='A133') // Acknowledgement
	 {
	 	$sql = sql_select("SELECT b.id as sys_number, b.insert_date as system_date from inv_item_trans_acknowledgement b where b.status_active=1 and b.entry_form =133 and b.id=$barcode_no order by b.insert_date desc");
	 }
	 else if ($entry_form =='T133') // Transfer out
	 {
	 	$sql = sql_select("SELECT b.transfer_system_id as sys_number, b.transfer_date as system_date, d.qnty, c.to_store, c.to_floor_id, c.to_room, c.to_rack, c.to_shelf, c.to_bin_box from inv_item_transfer_mst b, inv_item_transfer_dtls c, pro_roll_details d, fabric_sales_order_mst a where b.id = c.mst_id and c.id = d.dtls_id and b.id = d.mst_id and b.entry_form in (133) and d.entry_form in (133) and d.po_breakdown_id=a.id and a.company_id=1 and d.status_active = 1 and d.is_deleted = 0 and d.re_transfer=1 and c.TO_TRANS_ID=0 and d.booking_without_order=0 and d.barcode_no='$barcode_no'");
		$width="700px";
		$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
		$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
	 }


	?>
	<br>
	<fieldset style="width:<? echo $width;?>; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="<? echo $width;?>" cellpadding="0" cellspacing="0">
            <thead>
                <th width="120">System No</th>
                <th width="100">Date</th>
				<?
					if($entry_form==133 || $entry_form=='T133')
					{
					?>
					<th width="80">Store</th>
					<th width="80">Floor</th>
					<th width="80">Room</th>
					<th width="80">Rack</th>
					<th width="80">Self</th>
					<th width="80">Bin Box</th>
					<?
					}
				?>
            </thead>
            <tbody>
            	<?
				foreach($sql as $row)
				{
					?>
            	<tr>
            		<td align="center"><? echo $row[csf("sys_number")];?></td>
            		<td align="center"><? echo $row[csf("system_date")];?></td>
					<?
						if($entry_form==133 || $entry_form=='T133')
						{
						?>
						<td width="80"><? echo $store_name_arr[ $row[csf("to_store")]];?></td>
						<td width="80"><? echo $floor_room_rack_arr[ $row[csf("to_floor_id")]];?></td>
						<td width="80"><? echo $floor_room_rack_arr[ $row[csf("to_room")]];?></td>
						<td width="80"><? echo $floor_room_rack_arr[ $row[csf("to_rack")]];?></td>
						<td width="80"><? echo $floor_room_rack_arr[ $row[csf("to_shelf")]];?></td>
						<td width="80"><? echo $floor_room_rack_arr[ $row[csf("to_bin_box")]];?></td>
						<?
						}
					?>
            	</tr>
            	<?
				}
				?>
            </tbody>
        </table>
    </fieldset>
	<?
}
?>