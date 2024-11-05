<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];
include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

// get buyer condition according to priviledge
//if ($_SESSION['logic_erp']["data_level_secured"]==1){
if ($_SESSION['logic_erp']["buyer_id"] != "")
{
	$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
}
else
{
	$buyer_id_cond = "";
}

if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function () {
			set_field_level_access( <? echo $companyID; ?> );
		}
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_booking = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());
				selected_booking.push($('#txt_booking_no' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_booking.splice(i, 1);
			}
			var id = '';
			var name = '';
			var booking = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				booking += selected_booking[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			booking = booking.substr(0, booking.length - 1);
			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			$('#hide_booking_no').val(booking);
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>PO Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Sales Order No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
						<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
						<input type="hidden" name="hide_booking_no" id="hide_booking_no" value=""/>
					</thead>
					<tbody>
						<tr>
							<td id="buyer_td">
								<?
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:90px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:05px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "get_program_by_booking_for_req")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function () {
			set_field_level_access( <? echo $companyID; ?> );
		}
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_booking = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_booking_no' + str).val(), selected_booking) == -1) {
				selected_booking.push($('#txt_booking_no' + str).val());
			} else {
				for (var i = 0; i < selected_booking.length; i++) {
					if (selected_booking[i] == $('#txt_booking_no' + str).val()) break;
				}
				selected_booking.splice(i, 1);
			}
			var booking = '';
			for (var i = 0; i < selected_booking.length; i++) {
				booking += selected_booking[i] + '*';
			}

			booking = booking.substr(0, booking.length - 1);
			$('#hide_booking_no').val(booking);
		}

	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Cust. Buyer</th>
						<th id="search_by_td_up" width="170">Sales Job/ Booking No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
						<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
						<input type="hidden" name="hide_booking_no" id="hide_booking_no" value=""/>
						<input type="hidden" name="cbo_search_by" id="cbo_search_by" value="3"/>
					</thead>
					<tbody>
						<tr>
							<td id="buyer_td">
								<?
								//echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_buyer_brand', 'cust_buyer_brand_td' );load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_season', 'season_td' )", 0);
								
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$companyID." $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:90px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:05px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_search_list_view")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "%'";
		} else if ($search_by == 3) {
			$search_field_cond = " and a.sales_booking_no like '%" . $search_string . "%'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and a.within_group=$within_group";
	if ($buyer_id == 0) $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id=$buyer_id";

	if ($po_buyer_id == 0) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$po_buyer_id_cond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$po_buyer_id_cond = "";
		}
	} else {
		$po_buyer_id_cond = " and b.buyer_id=$po_buyer_id";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later


	if($db_type == 0){
		$booking_year = " b.booking_year";
		$booking_year2 = "YEAR(c.booking_date) as booking_year";
	}else{
		$booking_year = " cast(b.booking_year as varchar(4000)) as booking_year";
		$booking_year2 = "to_char(c.booking_date,'YYYY') as booking_year";
	}

	if ($within_group == 1) {	
		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num,$booking_year from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $buyer_id_cond $po_buyer_id_cond
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no_prefix_num,$booking_year2  from fabric_sales_order_mst a inner join wo_non_ord_samp_booking_mst c on a.sales_booking_no = c.booking_no where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond
		order by id";
	} else {
		$sql = "select a.id, $year_field,a.job_no_prefix_num,a.job_no,a.within_group,a.sales_booking_no booking_no_prefix_num,a.booking_date,a.buyer_id,a.style_ref_no,a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond and within_group=2 order by a.id";
	}
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Sales Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th width="60">Booking Year</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:770px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			if ($row[csf('within_group')] == 1)
				$buyer = $company_arr[$row[csf('buyer_id')]];
			else
				$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
				<td width="40" align="center"><? echo $i; ?>
				<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>"
				value="<? echo $row[csf('id')]; ?>"/>
				<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>"
				value="<? echo $row[csf('job_no')]; ?>"/>
				<?php
				if ($within_group == 1) {
					$booking = $row[csf('booking_no_prefix_num')];
				} else {
					$booking = $row[csf('sales_booking_no')];
				}
				?>
				<input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>"
				value="<? echo $row[csf('booking_no_prefix_num')]; ?>"/>
			</td>
			<td width="70" align="center"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
			<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
			<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('booking_year')]; ?></p></td>
			<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
		</tr>
		<?
		$i++;
	}
	?>
	</table>
	</div>
	<table width="760" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');
	$company_arr = return_library_array("select id, company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');

	$type = str_replace("'", "", $cbo_type);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$cbo_within_group = str_replace("'", "", $cbo_within_group);
	$company_name = $cbo_company_name;
	$barcode = str_replace("'", "", trim($txt_barcode));

	$job_no_cond = "";
	if (str_replace("'", "", $hide_job_id) != "")
	{
		$hideJobId = str_replace("'", "", $hide_job_id);
		$expHideJobId = explode(',', $hideJobId);
		$hideJobIdArr = array();
		foreach($expHideJobId as $key=>$val)
		{
			$hideJobIdArr[$val] = $val;
		}
		
		$job_no_cond = where_con_using_array($hideJobIdArr, '0', 'c.po_id');
	}

	$within_group_cond = " and a.within_group=$cbo_within_group";
	if ($buyer_name == 0)
		$buyer_id_cond = "";
	else
		$buyer_id_cond = " and a.buyer_id=$buyer_name";
		
	if (str_replace("'", "", $txt_machine_dia) == "")
		$machine_dia = "%%";
	else
		$machine_dia = "%" . str_replace("'", "", $txt_machine_dia) . "%";
		
	if (str_replace("'", "", $txt_booking_no) != "")
	{
		$booking_no = " and a.booking_no = $txt_booking_no";
	}
	else
	{
		$booking_no = "";
	}

	if ($barcode != "")
	{
		$expBarcode = explode(',', $barcode);
		$barcodeArr = array();
		foreach($expBarcode as $key=>$val)
		{
			$barcodeArr[$val] = $val;
		}
		
		$barcode_cond = where_con_using_array($barcodeArr, '0', 'b.barcode_no');
		$sales_dtls_result = sql_select("select b.id from fabric_sales_order_dtls b where b.status_active=1 and b.is_deleted=0 $barcode_cond");
		foreach ($sales_dtls_result as $srow)
		{
			$salseDetailsIdArr[$srow[csf('id')]] = $srow[csf('id')];
		}
		
		if(!empty($salseDetailsIdArr))
		{
			$salse_detls_id = chop($salse_detls_id," , ");
			$barcodeSalse_detlsCond = where_con_using_array($salseDetailsIdArr, '0', 'sales_order_dtls_ids');
		}
	}

	$i = 1;
	$k = 1;
	$tot_program_qnty = 0;
	$tot_yarn_req_qnty = 0;
	$machine_dia_gg_array = array();
	$program_no_arr = array();
	$sales_order_arr = array();
	$sql = "SELECT a.company_id, a.within_group, a.booking_no, a.buyer_id, a.customer_buyer, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,c.po_id, c.after_wash_gsm
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_name and b.knitting_source=$type and machine_dia like '$machine_dia' and (a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 or c.is_issued=1) and b.is_sales=1 $job_no_cond $booking_no $buyer_id_cond $within_group_cond $barcodeSalse_detlsCond 
	group by a.company_id, a.buyer_id, a.customer_buyer, a.within_group, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,c.po_id, c.after_wash_gsm
	order by b.machine_dia,b.machine_gg";
	//echo $sql;
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row)
	{
		$program_no_arr[$row[csf('id')]] = $row[csf('id')];
		$sales_order_arr[$row[csf('po_id')]] = $row[csf('po_id')];
	}

	$reqs_array = array();
	$program_cond = '';
	if(!empty($sales_order_arr))
	{
		$program_cond = where_con_using_array($program_no_arr, '0', 'knit_id');
	}

	$reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond group by knit_id, requisition_no");
	foreach ($reqs_sql as $row)
	{
		$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
		$reqs_array[$row[csf('knit_id')]]['qnty'] = $row[csf('yarn_req_qnty')];
	}

	$job_no_array = array();
	$booking_no_arr = array();
	$sales_cond = '';
	if(!empty($sales_order_arr))
	{
		$sales_cond = where_con_using_array($sales_order_arr, '0', 'a.id');
	}

	$jobData = sql_select("select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $sales_cond");
	foreach ($jobData as $row)
	{
		$job_no_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		$job_no_array[$row[csf('id')]]['job_id'] = $row[csf('id')];
		$job_no_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
		$job_no_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$booking_no_arr[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
	}

	if ($within_group == 0)
	{
		$fld_cap = "Booking";
		$booking_cond = '';
		if(!empty($booking_no_arr))
		{
			$booking_cond = where_con_using_array($booking_no_arr, '1', 'booking_no');
		}
		
		$sql_data=sql_select("select buyer_id, booking_no from wo_booking_mst where item_category in(2,13) and status_active=1 and is_deleted=0 $booking_cond
		union all
		select buyer_id,booking_no from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $booking_cond");
		$booking_buyer_array = array();
		foreach ($sql_data as $row)
		{
			$booking_buyer_array[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		}
	}
	else
		$fld_cap = "Sale";
	?>
	<fieldset style="width:1770px; margin-top:10px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1770" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="70">Program No</th>
				<th width="80">Program Date</th>
				<th width="70">Customers</th>
				<th width="110">Sales Order No</th>
				<th width="110">Sales Job/<? echo $fld_cap; ?> No</th>
				<th width="70">Customers Buyer</th>
				<th width="110">Style</th>
				<th width="80">Dia / GG</th>
				<th width="145">Fabric Description</th>
				<th width="70">Fabric Gsm</th>
				<th width="70">After Wash GSM</th>
				<th width="60">Fabric Dia</th>
				<th width="80">Width/Dia Type</th>
				<th width="90">Color Range</th>
				<th width="90">Program Qnty</th>
				<th width="90">Yarn Req. Qnty</th>
				<th width="70">Req. No</th>
				<th width="75">Start Date</th>
				<th width="75">T.O.D</th>
				<th>Status</th>
			</thead>
		</table>
		<div style="width:1770px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1750" class="rpt_table"
			id="tbl_list_search">
			<tbody>
				<?php
	            foreach ($nameArray as $row)
				{
	                $machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];
	                $yarn_req_qnty = $reqs_array[$row[csf('id')]]['qnty'];
	                $reqs_no = $reqs_array[$row[csf('id')]]['reqs_no'];
	                $buyer_nam=$job_no_array[$row[csf('po_id')]]['buyer_id'];
	                $balance_qnty = $row[csf('program_qnty')] - $yarn_req_qnty;

	                if (($planning_status == 3 && $balance_qnty <= 0) || ($planning_status == 1 && $balance_qnty > 0))
					{
	                    if ($i % 2 == 0)
	                        $bgcolor = "#E9F3FF";
	                    else
	                        $bgcolor = "#FFFFFF";

	                    if (!in_array($machine_dia_gg, $machine_dia_gg_array))
						{
	                        if ($k != 1)
							{
	                            ?>
	                            <tr bgcolor="#CCCCCC">
	                                <td colspan="15" align="right"><b>Sub Total</b></td>
	                                <td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
	                                <td align="right"><b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b></td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                            </tr>
	                            <?
	                            $sub_tot_program_qnty = 0;
	                            $sub_tot_yarn_req_qnty = 0;
	                        }

	                        ?>
	                        <tr bgcolor="#EFEFEF">
	                            <td colspan="21"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
	                        </tr>
	                        <?
	                        $machine_dia_gg_array[] = $machine_dia_gg;
	                        $k++;
	                    }

						$style_ref = $job_no_array[$row[csf('po_id')]]['style_ref'];
						$job_no = $job_no_array[$row[csf('po_id')]]['job_no'];
						$job_ids = $job_no_array[$row[csf('po_id')]]['job_id'];

						if ($row[csf('within_group')] == 1)
						{
							$buyer = $company_arr[$row[csf('buyer_id')]];
							//$customers_buyer = $buyer_arr[$booking_buyer_array[$row[csf('booking_no')]]];
						}
						else
						{
							$buyer = $buyer_arr[$buyer_nam];
							//$customers_buyer = $buyer_arr[$buyer_nam];
						}
						
						//for customer_buyer
						$customers_buyer = $buyer_arr[$row[csf('customer_buyer')]];
						$cons_comps = explode(",", $row[csf('fabric_desc')]);
						$comps = $cons_comps[1];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40" align="center"><? echo $i; ?></td>
							<td width="70" align="center">&nbsp;&nbsp;<? echo $row[csf('id')]; ?>&nbsp;</td>
							<td width="80" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
							<td width="70" align="center"><p><? echo $buyer; ?></p></td>
							<td width="110" align="center"><p><? echo $job_no; ?></p></td>
							<td width="110" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
							<td width="70" align="center"><p><? echo $customers_buyer; ?></p></td>
							<td width="110" align="center"><p><? echo $style_ref; ?></p></td>
							<td width="80" align="center"><p><? echo $machine_dia_gg; ?></p></td>
							<td width="145" align="center"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="70" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="70" align="center"><p><? echo $row[csf('after_wash_gsm')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
							<td width="80" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
							<td width="90" align="center"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
							<td align="right" width="90"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
							<td align="center" valign="middle" width="90">
								<input type="text" name="txt_yarn_req_qnty[]" id="txt_yarn_req_qnty_<? echo $i; ?>"
								style="width:70px" class="text_boxes_numeric" readonly
								value="<? if ($yarn_req_qnty > 0) echo number_format($yarn_req_qnty, 2); ?>"
								placeholder="Single Click"
								onClick="openmypage_yarnReq(<? echo $i; ?>,'<? echo $row[csf('id')]; ?>',<? echo $company_name; ?>,'<? echo $comps; ?>','<? echo $job_no; ?>','<? echo $reqs_no; ?>','<? echo $job_ids; ?>','<? echo $cbo_within_group; ?>','<? echo $row[csf('booking_no')]; ?>')"/>
							</td>
							<td align="center" width="70"><? echo "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $row[csf('id')] . "," . $row[csf('within_group')] . ")\">$reqs_no</a>"; ?> &nbsp;
	                        </td>
	                        <td width="75" align="center">&nbsp;<? if ($row[csf('start_date')] != "" && $row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
	                        <td width="75" align="center">
	                        &nbsp;<? if ($row[csf('end_date')] != "" && $row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
	                        <td align="center"><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p></td>
							</tr>
							<?
							$sub_tot_program_qnty += $row[csf('program_qnty')];
							$sub_tot_yarn_req_qnty += $yarn_req_qnty;
							$tot_program_qnty += $row[csf('program_qnty')];
							$tot_yarn_req_qnty += $yarn_req_qnty;
							$i++;
					}
				}
				if ($i > 1)
				{
					?>
					<tr bgcolor="#CCCCCC">
						<td colspan="15" align="right"><b>Sub Total</b></td>
						<td align="right"><b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
						<td align="right"><b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<?
				}
				?>
			</tbody>
			<tfoot>
				<th colspan="15" align="right">Grand Total</th>
				<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
				<th align="right"><? echo number_format($tot_yarn_req_qnty, 2, '.', ''); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</table>
	</div>
	</fieldset>
	<?
	exit();
}

if ($action == "print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $path;die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');
	//echo $company_id;die;

	$company_details = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", "id", "machine_no");
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$sales_info = sql_select("select a.job_no, a.style_ref_no,location_id  from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b where a.id=b.po_id and b.dtls_id = $program_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	?>
	<div style="width:860px">
		<div style="margin-left:20px; width:850px">
			<div style="width:100px;float:left;position:relative;margin-top:10px">
				<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
				<img src="<? echo $path . $image_location; ?>" height='100%' width='100%'/>
			</div>
			<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
			<div style="float:left;position:relative;">
				<table width="100%" style="margin-top:10px; font-family: tahoma;">
					<tr>
						<td align="center" style="font-size:16px;">
							<? echo $company_details[$company_id]; ?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">
							<?
							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result) {
								?>
								Plot No: <? echo $result['plot_no']; ?>
								Level No: <? echo $result['level_no'] ?>
								Road No: <? echo $result['road_no']; ?>
								Block No: <? echo $result['block_no']; ?>
								City No: <? echo $result['city']; ?>
								Zip Code: <? echo $result['zip_code']; ?>
								Country: <? echo $country_arr[$result['country_id']]; ?><br>
								Email Address: <? echo $result['email']; ?>
								Website No: <? echo $result['website'];
							}
							?>
						</td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin-left:10px;float:left; width:850px;">
			<?
			$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,fabric_dia from ppl_planning_info_entry_dtls where id=$program_id and status_active=1 and is_deleted=0");


			if ($dataArray[0][csf('knitting_source')] == 1) {

				$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
			}
			else if($dataArray[0][csf('knitting_source')] == 3)
			{
				$location = return_field_value("location_name", "lib_location", "id='" . $sales_info[0][csf('location_id')] . "'");
			}

			$advice = $dataArray[0][csf('advice')];

			$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia, within_group from ppl_planning_info_entry_mst where status_active=1 and is_deleted=0 and id=" . $dataArray[0][csf('mst_id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('dia')];
			$within_group = $mst_dataArray[0][csf('within_group')];

			?>
			&nbsp;&nbsp;<b>Attention- Knitting Manager</b>
			<table width="100%" style="margin-top:5px; font-family: tahoma;" cellspacing="7">
				<tr>
					<td width="140"><b>Program No:</b></td>
					<td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="170"><b>Program Date:</b></td>
					<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
				</tr>
				<tr>
					<td><b>Factory:</b></td>
					<td>
						<?
						if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
						else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
						?>
					</td>
					<td><b>Fabrication & FGSM:</b></td>
					<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3">
						<?
						$address = '';
						if ($dataArray[0][csf('knitting_source')] == 1) {
							$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result) {
								?>
								Plot No: <? echo $result[csf('plot_no')]; ?>
								Road No: <? echo $result[csf('road_no')]; ?>
								Block No: <? echo $result[csf('block_no')]; ?>
								City No: <? echo $result[csf('city')];
							}
						} else if ($dataArray[0][csf('knitting_source')] == 3) {
							$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
							echo $address;
						}

						$machine_no = '';
						$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
						foreach ($machine_id as $val) {
							if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
						}

						if ($within_group == 1) {
							$buyer = $company_details[$buyer_id];
							$booking_buyer = return_field_value("buyer_id", "wo_booking_mst", "booking_no='" . $booking_no . "'");
							$customers_buyer = $buyer_arr[$booking_buyer];
						} else {
							$buyer = $buyer_arr[$buyer_id];
							$customers_buyer = $buyer_arr[$buyer_id];
						}
						?>
					</td>
				</tr>
				<tr>
					<td><b>PO Company:</b></td>
					<td><b><? echo $buyer; ?></b></td>
					<td><b>PO Buyer:</b></td>
					<td><b><? echo $customers_buyer; ?></b></td>
				</tr>
				<tr>
					<td><b>Sales Order No:</b></td>
					<td><b><? echo $sales_info[0]["JOB_NO"]; ?></b></td>
					<td><b>Fabric/Booking No:</b></td>
					<td><b><? echo $booking_no; ?></b></td>
				</tr>
				<tr>
					<td><b>Style Ref.:</b></td>
					<td><b><? echo $sales_info[0]["STYLE_REF_NO"]; ?></b></td>
					<td><b>Location:</b></td>
					<td><b><? echo $location; ?></b></td>
				</tr>
				<tr>
					<td><b>Machine No:</b></td>
					<td><b><? echo $machine_no; ?></b></td>
				</tr>
			</table>

			<table style="margin-top:10px; font-family: tahoma;" width="850" border="1" rules="all" cellpadding="0"
			cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="70">Requisition No</th>
				<th width="70">Lot No</th>
				<th width="225">Yarn Description</th>
				<th width="110">Brand</th>
				<th width="80">Requisition Qnty</th>
				<th width="120">Yarn Color</th>
				<th>Remarks</th>
			</thead>
			<?
			$i = 1;
			$tot_reqsn_qnty = 0;
			$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
				&nbsp;</p></td>
				<td>
					<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
				&nbsp;</p></td>
				<td><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p></td>
				<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
				<td><p>
					&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
				</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
			$i++;
		}
		?>
		<tfoot>
			<th colspan="5" align="right"><b>Total</b></th>
			<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tfoot>
	</table>
	<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all"
	style="margin-top:20px; font-family: tahoma;" class="rpt_table">
	<tr>
		<td width="120"><b>Colour Range:</b></td>
		<td width="150"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
		<td width="120"><b>GGSM OR S/L:</b></td>
		<td width="150"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
		<td width="120"><b>FGSM:</b></td>
		<td><p><? echo $gsm_weight; ?>&nbsp;</p></td>
	</tr>
	<tr>
		<td><b>Finish Dia</b></td>
		<td>
			<p><? echo $dataArray[0][csf('fabric_dia')] . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
		&nbsp;</p></td>
		<td><b>Machine Dia & Gauge:</b></td>
		<td><p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
	&nbsp;</p></td>
	<td><b>Program Qnty:</b></td>
	<td><p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p></td>
	</tr>
	<tr>
	<td><b>Feeder:</b></td>
	<td><p>
		<?
		$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
		echo $feeder_array[$dataArray[0][csf('feeder')]];
		?>&nbsp;</p></td>
		<td><b>Garments Color</b></td>
		<td><p>
			<?
			$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= $color_library[$color_id] . ",";
			}
			$all_color = chop($all_color, ",");
			echo $all_color;

			?>&nbsp;</p></td>
			<td><b>Remarks</b></td>
			<td><p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p></td>
		</tr>
	</table>
	<?
	$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder order by a.id");
	if (count($sql_fedder) > 0) {
		?>
		<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
		class="rpt_table">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="200">Color</th>
				<th width="200">Stripe Color</th>
				<th width="120">Measurement</th>
				<th width="120">UOM</th>
				<th>No Of Feeder</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			$total_feeder = 0;
			foreach ($sql_fedder as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr>
					<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
					<td><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
					<td><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf('no_of_feeder')], 0);
					$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th align="right">Total:</th>
				<th align="right"><? echo number_format($total_feeder, 0); ?></th>
			</tr>
		</tfoot>
	</table>
	<?
}
?>

<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table">
<tr>
	<td colspan="4" style="word-wrap:break-word"><b>Advice:</b> <strong><? echo $advice; ?></strong>
	</td>
</tr>
</table>

<table width="850" style="display:none">
<tr>
	<td width="100%" height="90" colspan="4"></td>
</tr>
<tr>
	<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
	<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
	<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
	</td>
	<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
</tr>
</table>
<br>
<? echo signature_table(100, $company_id, "850px"); ?>
</div>
</div>
<?
exit();
}

if ($action == "print_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	//echo $company_id;die;

	$company_details = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", "id", "machine_no");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$sales_info = sql_select("select a.job_no, a.style_ref_no, a.buyer_id, a.booking_without_order from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b where a.id=b.po_id and b.dtls_id = $program_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	?>
	<style type="text/css">
	table tr td, table tr th {
		font-size: 13px;
	}
</style>
<div style="width:860px">
	<div style="margin-left:20px; width:850px">
		<div style="width:100px;float:left;position:relative;margin-top:10px">
			<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
			<img src="../../<? echo $image_location; ?>" height='100%' width='100%'/>
		</div>
		<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
		<div style="float:left;position:relative;">
			<table width="100%" style="margin-top:10px">
				<tr>
					<td align="center" style="font-size:16px;">
						<? echo $company_details[$company_id]; ?>
					</td>
				</tr>
				<tr>
					<td align="center" style="font-size:14px">
						<?
						$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id and status_active=1 and is_deleted=0");
						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result['plot_no']; ?>
							Level No: <? echo $result['level_no'] ?>
							Road No: <? echo $result['road_no']; ?>
							Block No: <? echo $result['block_no']; ?>
							City No: <? echo $result['city']; ?>
							Zip Code: <? echo $result['zip_code']; ?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email']; ?>
							Website No: <? echo $result['website'];
						}
						?>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
				</tr>
			</table>
		</div>
	</div>
	<div style="margin-left:20px;float:left; width:850px;">
		<?
		$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length, machine_dia, machine_gg, program_qnty, machine_id, remarks, location_id, advice, feeder, width_dia_type, color_id,fabric_dia from ppl_planning_info_entry_dtls where id=$program_id and status_active=1 and is_deleted=0");

		$location = return_field_value("location_name", "lib_location", "id='" . $dataArray[0][csf('location_id')] . "'");
		$advice = $dataArray[0][csf('advice')];

		$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia, within_group from ppl_planning_info_entry_mst where status_active=1 and is_deleted=0 and id=" . $dataArray[0][csf('mst_id')]);
		$booking_no = $mst_dataArray[0][csf('booking_no')];
		$buyer_id = $mst_dataArray[0][csf('buyer_id')];
		$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
		$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
		$dia = $mst_dataArray[0][csf('dia')];
		$within_group = $mst_dataArray[0][csf('within_group')];

		?>
		&nbsp;&nbsp;<b>Attention- Knitting Manager</b>
		<table width="100%" style="margin-top:20px" cellspacing="7">
			<tr>
				<td width="140"><b>Program No:</b></td>
				<td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="170"><b>Program Date:</b></td>
				<td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
			</tr>
			<tr>
				<td><b>Factory:</b></td>
				<td>
					<?

					if ($dataArray[0][csf('knitting_source')] == 1) echo $company_details[$dataArray[0][csf('knitting_party')]];
					else if ($dataArray[0][csf('knitting_source')] == 3) echo $supllier_arr[$dataArray[0][csf('knitting_party')]];
					?>
				</td>
				<td><b>Fabrication & FGSM:</b></td>
				<td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
			</tr>
			<tr>
				<td><b>Address:</b></td>
				<td colspan="3">
					<?
					$address = '';
					if ($dataArray[0][csf('knitting_source')] == 1) {
						$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id and status_active=1 and is_deleted=0");
						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							City No: <? echo $result[csf('city')];
						}
					} else if ($dataArray[0][csf('knitting_source')] == 3) {
						$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
						echo $address;
					}

					$machine_no = '';
					$machine_id = explode(",", $dataArray[0][csf("machine_id")]);
					foreach ($machine_id as $val) {
						if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
					}

					if ($within_group == 1)
					{
						$buyer = $company_details[$buyer_id];
						if($sales_info[0][csf('booking_without_order')] != 1)
						{
							$booking_buyer = return_field_value("buyer_id", "wo_booking_mst", "booking_no='" . $booking_no . "'");
						}
						else
						{
							//for sample without order
							$booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
						}
						$customers_buyer = $buyer_arr[$booking_buyer];
					}
					else
					{
						$buyer = '';
						$customers_buyer = $buyer_arr[$sales_info[0]["BUYER_ID"]];
					}
					?>
				</td>
			</tr>
			<tr>
				<td><b>PO Company:</b></td>
				<td><b><? echo $buyer; ?></b></td>
				<td><b>Buyer Name:</b></td>
				<td><b><? echo $customers_buyer; ?></b></td>
			</tr>
			<tr>
				<td><b>Sales Order No:</b></td>
				<td><b><? echo $sales_info[0]["JOB_NO"]; ?></b></td>
				<td><b>Sales / Booking No:</b></td>
				<td><b><? echo $booking_no; ?></b></td>
			</tr>
			<tr>
				<td><b>Style Ref.:</b></td>
				<td><b><? echo $sales_info[0]["STYLE_REF_NO"]; ?></b></td>
				<td><b>Location:</b></td>
				<td><b><? echo $location; ?></b></td>
			</tr>
			<tr>
				<td><b>Machine No:</b></td>
				<td><b><? echo $machine_no; ?></b></td>
			</tr>
		</table>

		<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
		class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="70">Requisition No</th>
			<th width="70">Lot No</th>
			<th width="220">Yarn Description</th>
			<th width="110">Brand</th>
			<th width="80">Requisition Qnty</th>
			<th width="120">Yarn Color</th>
			<th>Remarks</th>
		</thead>
		<?
		$i = 1;
		$tot_reqsn_qnty = 0;
		$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
		$nameArray = sql_select($sql);
		foreach ($nameArray as $selectResult) {
			?>
			<tr>
				<td align="center"><? echo $i; ?></td>
				<td align="center"><p><? echo $selectResult[csf('requisition_no')]; ?>&nbsp;</p></td>
				<td align="center"><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?>
			&nbsp;</p></td>
			<td>
				<p><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?>
			&nbsp;</p></td>
			<td><p><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?>&nbsp;</p></td>
			<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
			<td><p>
				&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></p>
			</td>
			<td>&nbsp;</td>
		</tr>
		<?
		$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
		$i++;
	}
	?>
	<tfoot>
		<th colspan="5" align="right"><b>Total</b></th>
		<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tfoot>
</table>
<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;"
class="rpt_table">
<tr>
	<td width="120"><b>Colour Range:</b></td>
	<td width="150"><p><? echo $color_range[$dataArray[0][csf('color_range')]]; ?>&nbsp;</p></td>
	<td width="120"><b>GGSM OR S/L:</b></td>
	<td width="150"><p><? echo $dataArray[0][csf('stitch_length')]; ?>&nbsp;</p></td>
	<td width="120"><b>FGSM:</b></td>
	<td><p><? echo $gsm_weight; ?>&nbsp;</p></td>
</tr>
<tr>
	<td><b>Finish Dia</b></td>
	<td>
		<p><? echo $dataArray[0][csf('fabric_dia')] . "  (" . $fabric_typee[$dataArray[0][csf('width_dia_type')]] . ")"; ?>
	&nbsp;</p></td>
	<td><b>Machine Dia & Gauge:</b></td>
	<td><p><? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?>
&nbsp;</p></td>
<td><b>Program Qnty:</b></td>
<td><p><? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;</p></td>
</tr>
<tr>
	<td><b>Feeder:</b></td>
	<td><p>
		<?
		$feeder_array = array(1 => "Full Feeder", 2 => "Half Feeder");
		echo $feeder_array[$dataArray[0][csf('feeder')]];
		?>&nbsp;</p></td>
		<td><b>Garments Color</b></td>
		<td><p>
			<?
			$color_id_arr = array_unique(explode(",", $dataArray[0][csf('color_id')]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= $color_library[$color_id] . ",";
			}
			$all_color = chop($all_color, ",");
			echo $all_color;

			?>&nbsp;</p></td>
			<td><b>Remarks</b></td>
			<td><p><? echo $dataArray[0][csf('remarks')]; ?>&nbsp;</p></td>
		</tr>
	</table>
	<?

	if($program_id!="")
	{
		$sql_fedder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder");
	}
	
	if (count($sql_fedder) > 0) {
		?>
		<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
		class="rpt_table">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="200">Color</th>
				<th width="200">Stripe Color</th>
				<th width="120">Measurement</th>
				<th width="120">UOM</th>
				<th>No Of Feeder</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			$total_feeder = 0;
			foreach ($sql_fedder as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				?>
				<tr>
					<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
					<td><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
					<td><p><? echo $color_library[$row[csf('stripe_color_id')]]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf('measurement')], 2); ?>&nbsp;</p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf('no_of_feeder')], 0);
					$total_feeder += $row[csf('no_of_feeder')]; ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th align="right">Total:</th>
				<th align="right"><? echo number_format($total_feeder, 0); ?></th>
			</tr>
		</tfoot>
	</table>
	<?
}
?>
<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
class="rpt_table">
<tr>
	<td colspan="4" style="word-wrap:break-word; font-size: 14px;"><b>Advice:</b>
		<strong><? echo $advice; ?></strong></td>
	</tr>
</table>

<table width="850" style="display:none">
	<tr>
		<td width="100%" height="90" colspan="4"></td>
	</tr>
	<tr>
		<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
		<td width="25%" align="center"><strong style="text-decoration:overline">Receive By</strong></td>
		<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong>
		</td>
		<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
	</tr>
</table>
<br>
<? echo signature_table(100, $company_id, "850px"); ?>
</div>
</div>
<?
exit();
}

if ($action == "yarn_req_qnty_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$is_auto_allocate_from_requisition = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name=".$companyID." and variable_list=6 and status_active=1 and is_deleted=0", "auto_allocate_yarn_from_requis");
	?>
	<script>
		var permission = '<? echo $permission; ?>';
		function calculate(field_id)
		{
			var txt_no_of_cone = $('#txt_no_of_cone').val() * 1;
			var txt_weight_per_cone = $('#txt_weight_per_cone').val() * 1;
			var txt_yarn_qnty = $('#txt_yarn_qnty').val() * 1;

			if (field_id == "txt_yarn_qnty") {
				if (txt_no_of_cone > 0) {
					var weightPerCone = txt_yarn_qnty / txt_no_of_cone;
					$('#txt_weight_per_cone').val(weightPerCone.toFixed(2));
				}
				else {
					$('#txt_weight_per_cone').val('');
				}
			}
			else {
				if (txt_weight_per_cone == "" && txt_yarn_qnty != "") {
					if (txt_no_of_cone > 0) {
						var weightPerCone = txt_yarn_qnty / txt_no_of_cone;
						$('#txt_weight_per_cone').val(weightPerCone.toFixed(2));
					}
					else {
						$('#txt_weight_per_cone').val('');
					}
				}
				else {
					var yarnQnty = txt_no_of_cone * txt_weight_per_cone;
					$('#txt_yarn_qnty').val(yarnQnty);
				}
			}
		}

		function openpage_lot()
		{
			var is_auto_allocation_from_requisition = $("#is_auto_allocation_from_requisition").val();
			var page_link = "yarn_requisition_entry_sales_v2_controller.php?action=lot_info_popup&companyID=<? echo $companyID; ?>" + "&knit_dtlsId=<? echo $knit_dtlsId; ?>" + "&comps=" + '<? echo $comps; ?>' + "&job_no=" + '<? echo $job_no; ?>' + "&selected_booking_no=" + '<? echo $booking_no; ?>' + '&is_auto_allocation_from_requisition=' + is_auto_allocation_from_requisition;
			var title = 'Lot Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=350px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function ()
			{
				var theform = this.contentDoc.forms[0];
				var prod_id = this.contentDoc.getElementById("hidden_prod_id").value;
				var data = this.contentDoc.getElementById("hidden_data").value.split("**");
				/*$('#prod_id').val(prod_id);
				$('#txt_lot').val(data[0]);
				$('#cbo_yarn_count').val(data[1]);
				$('#cbo_yarn_type').val(data[2]);
				$('#txt_color').val(data[3]);
				$('#txt_composition').val(data[4]);
				$('#txt_available_qty').val(data[5]);
				$('#hidden_lot_available_qnty').val(data[5]);*/
				
//$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**2**" . $bal_alloc_qnty."** **".number_format($allocationQty, 2, '.', '');

//$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**".$row[csf('dyed_type')]."**" . $available_qnty."**".$cbo_dyed_yarn_qty_from;
				
				$('#prod_id').val(prod_id);
				$('#txt_lot').val(data[0]);
				$('#cbo_yarn_count').val(data[1]);
				$('#cbo_yarn_type').val(data[2]);
				$('#txt_color').val(data[3]);
				$('#txt_composition').val(data[4]);
				$('#is_dyed_yarn').val(data[5]);
				$('#txt_available_qty').val(data[6]);
				
				/*if(is_auto_allocation_from_requisition==1) // yes
				{
					$('#available_qnty').val(data[6]);
				}
				else
				{
					if (data[6] != "" || data[6] != 0)
					{
						$('#hidden_yarn_req_qnty').val(data[6]);
					}
				}*/
				//$('#hidden_dyed_type').val(data[6]);
			}
		}

		function fnc_yarn_req_entry(operation)
		{
			var hidden_lot_available_qnty = parseFloat($('#hidden_lot_available_qnty').val());
			var txt_yarn_qnty = parseFloat($('#txt_yarn_qnty').val());
			
			if (txt_yarn_qnty > hidden_lot_available_qnty)
			{
				alert("Requisition Quantity is not Available.");
				return;
			}

			if (form_validation('txt_lot*txt_yarn_qnty*txt_reqs_date', 'Lot*Yarn Qnty*Requisition Date') == false) {
				return;
			}

			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('prod_id*txt_reqs_date*txt_no_of_cone*txt_yarn_qnty*updateId*update_dtls_id*txt_requisition_no*companyID*sale_order_id*txt_within_group*job_no*original_prod_id*original_prod_qnty*is_dyed_yarn*hdn_booking_no', "../../");
			//is_dyed_yarn
			freeze_window(operation);

			http.open("POST", "yarn_requisition_entry_sales_v2_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_req_entry_Reply_info;
		}

		function fnc_yarn_req_entry_Reply_info()
		{
			if (http.readyState == 4)
			{
				var reponse = trim(http.responseText).split('**');

				if (reponse[0] == 11)
				{
					alert("Duplicate Item Not Allowed");
				}
				else if (reponse[0] == 17)
				{
					alert(reponse[1]);
				}
				else if (reponse[0] == 18)
				{
					alert(reponse[1]);
				}
				else
				{
					show_msg(reponse[0]);
					if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2))
					{
						reset_form('yarnReqQnty_1', '', '', '', '', 'updateId*companyID*sale_order_id*txt_within_group*job_no*hdn_booking_no');
						$('#txt_requisition_no').val(reponse[3]);
						$('#hide_req_no').val(reponse[3]);
						show_list_view(reponse[1], 'requisition_info_details', 'list_view', 'yarn_requisition_entry_sales_v2_controller', '');
					}
				}

				set_button_status(reponse[2], permission, 'fnc_yarn_req_entry', 1);
				release_freezing();
			}
		}

		function fnc_close()
		{
            //$yarn_req_qnty=return_field_value( "sum(yarn_qnty) as yarn_qnty","ppl_yarn_requisition_entry", "knit_id=$knit_dtlsId","yarn_qnty");
            //$('#hidden_yarn_req_qnty').val( //echo $yarn_req_qnty; );
            parent.emailwindow.hide();
        }

        //generate_report2(".$row[csf('company_id')].",".$row[csf('id')].")
        function generate_report_print2(company_id, program_id, within_group)
		{
        	var req_no = $('#hide_req_no').val();
        	if (req_no != "")
			{
                var path = '';
                print_report(program_id + '**0**' + path + '**' + within_group, "requisition_print_two", "yarn_requisition_entry_sales_v2_controller");
            }
            else
			{
            	alert("Save Data First");
            	return;
            }
        }
		
        function generate_report_print(company_id, program_id)
		{
        	var req_no = $('#hide_req_no').val();
        	if (req_no != "") {
        		print_report(company_id + '*' + program_id, "print_popup", "yarn_requisition_entry_sales_v2_controller");
        	} else {
        		alert("Save Data First");
        		return;
        	}
        }
    </script>
</head>
<body>
	<div align="center">
		<div><? echo load_freeze_divs("../../", $permission, 1); ?></div>
		<form name="yarnReqQnty_1" id="yarnReqQnty_1">
			<fieldset style="width:950px !important; margin-top:10px">
				<legend>New Entry</legend>
				<table width="900" align="center" border="0">
					<tr>
						<td colspan="4" align="right"><strong>Requisition No</strong></td>
						<td colspan="4" align="left">
							<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes"
							style="width:130px;" placeholder="Display" disabled/>
							<input type="hidden" name="hide_req_no" id="hide_req_no" class="text_boxes" value="<? echo $reqs_no; ?>"/>
							<input type="hidden" id="is_auto_allocation_from_requisition" value="<? echo $is_auto_allocate_from_requisition; ?>"
                            readonly />
						</td>
					</tr>
					<tr>
						<td class="must_entry_caption">Lot</td>
						<td>
							<input type="text" name="txt_lot" id="txt_lot" class="text_boxes" placeholder="Double Click" style="width:130px;" 
                            onDblClick="openpage_lot();" readonly/>
							<input type="hidden" name="prod_id" id="prod_id" class="text_boxes" readonly/>
							<input type="hidden" name="is_dyed_yarn" id="is_dyed_yarn" class="text_boxes" readonly/>
                            <input type="hidden" name="original_prod_id" id="original_prod_id" class="text_boxes" readonly/>
							<input type="hidden" name="original_prod_qnty" id="original_prod_qnty" class="text_boxes" readonly/>
							<input type="hidden" name="hidden_yarn_req_qnty" id="hidden_yarn_req_qnty" class="text_boxes" readonly/>
							<input type="hidden" name="hidden_lot_available_qnty" id="hidden_lot_available_qnty" readonly/>
							<input type="hidden" name="companyID" id="companyID" value="<?php echo $companyID; ?>" readonly/>
							<input type="hidden" name="hdn_booking_no" id="hdn_booking_no" value="<?php echo $booking_no; ?>" readonly/>
						</td>
						<td>Yarn Count</td>
						<td>
							<?
							echo create_drop_down("cbo_yarn_count", 142, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id,yarn_count", 1, "Display", 0, "", 1);
							?>
						</td>
						<td>Yarn Type</td>
						<td>
							<?
							echo create_drop_down("cbo_yarn_type", 142, $yarn_type, "", 1, "Display", 0, "", 1);
							?>
						</td>
						<td>Color</td>
						<td>
							<input type="text" name="txt_color" id="txt_color" class="text_boxes" placeholder="Display"
							style="width:130px;" disabled/>
						</td>
					</tr>
					<tr>
						<td>Composition</td>
						<td colspan="3">
							<input type="text" name="txt_composition" id="txt_composition" class="text_boxes"
							placeholder="Display" style="width:372px;" disabled/>
						</td>
						<td class="must_entry_caption">Requisition Date</td>
						<td>
							<input type="text" name="txt_reqs_date" id="txt_reqs_date" class="datepicker" style="width:130px;" 
                            value="<? echo date("d-m-Y");?>" />
						</td>
						<td>No of Cone</td>
						<td>
							<input type="text" name="txt_no_of_cone" id="txt_no_of_cone" class="text_boxes_numeric" style="width:130px;"/>
						</td>
					</tr>
					<tr>
						<td>Available Qty</td>
						<td>
							<input type="text" name="txt_available_qty" id="txt_available_qty" class="text_boxes_numeric" style="width:130px;"
                             disabled readonly />
						</td>
						<td class="must_entry_caption">Yarn Reqs. Qnty</td>
						<td>
							<input type="text" name="txt_yarn_qnty" id="txt_yarn_qnty" class="text_boxes_numeric" style="width:130px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="8" align="center" class="button_container">
							<?
							echo load_submit_buttons($permission, "fnc_yarn_req_entry", 0, 0, "reset_form('yarnReqQnty_1','','','','','updateId');", 1);
							?>
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px"/>
							<input type="button" name="btn_print" class="formbutton" value="Print" id="btn_print"
							onClick="generate_report_print2('<? echo str_replace("'", '', $companyID); ?>','<? echo str_replace("'", '', $knit_dtlsId); ?>','<? echo str_replace("'", '', $cbo_within_group); ?>')"
							style="width:100px"/>
							<input type="hidden" name="updateId" id="updateId" class="text_boxes" value="<? echo str_replace("'", '', $knit_dtlsId); ?>">
							<input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
							<input type="hidden" name="sale_order_id" id="sale_order_id" value="<? echo str_replace("'", '', $sale_order_id); ?>">
							<input type="hidden" name="txt_within_group" id="txt_within_group" value="<? echo str_replace("'",'',$cbo_within_group); ?>">
							<input type="hidden" name="job_no" id="job_no" value="<? echo str_replace("'", '', $job_no); ?>">
						</td>
					</tr>
				</table>
			</fieldset>
			<div id="list_view" style="margin-top:10px">
				<?
				if (str_replace("'", '', $knit_dtlsId) != "")
				{
					?>
					<script>
						show_list_view('<? echo str_replace("'", '', $knit_dtlsId); ?>', 'requisition_info_details', 'list_view', 'yarn_requisition_entry_sales_v2_controller', '');
					</script>
					<?
				}
				?>
			</div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "lot_info_popup")
{
	echo load_html_head_contents("Lot Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function js_set_value(id, data)
		{
			$('#hidden_prod_id').val(id);
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<?php
	$company_arr = get_company_array();
	$supplier_arr = get_supplier_array();
	$count_arr = get_yarn_count_array();
	$brand_arr = get_brand_array();

	if($is_auto_allocation_from_requisition == 1) // Yes
	{
		$prod_data = sql_select("select id, supplier_id, yarn_count_id, yarn_type, is_within_group from product_details_master where item_category_id=1 and company_id=$companyID and status_active=1 and is_deleted=0");
		foreach ($prod_data as $row)
		{
			$supplierArr[$row[csf('supplier_id')]] = ($row[csf('is_within_group')]==1?$company_arr[$row[csf('supplier_id')]]['name']:$supplier_arr[$row[csf('supplier_id')]]);
			$countArr[$row[csf('yarn_count_id')]] = $count_arr[$row[csf('yarn_count_id')]];
			$yarn_type_arr[$row[csf('yarn_type')]] = $yarn_type[$row[csf('yarn_type')]];
		}
		?>
		<div align="center" style="">
			<form name="searchfrm" id="searchfrm">
				<fieldset>
					<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
					<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
					<div><b><? echo $comps; ?></b></div>
					<table width="1000" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th width="150">Supplier</th>
                            <th width="150">Count</th>
                            <th width="150">Yarn Description</th>
                            <th width="150">Type</th>
                            <th width="150">Lot</th>
                            <th width="150">Dyed Yarn Qty From </th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;"></th>
                        </thead>
                        <tbody>
                            <tr align="center">
                                <td><? echo create_drop_down("cbo_supplier", 150, $supplierArr, "", 1, "-- Select --", '', "", 0); ?></td>
                                <td><? echo create_drop_down("cbo_count", 150, $countArr, "", 1, "-- Select --", '', "", 0); ?></td>
                                <td><input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:140px"></td>
                                <td><? echo create_drop_down("cbo_type", 150, $yarn_type_arr, "", 1, "-- Select --", '', "", 0); ?></td>
                                <td><input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:140px"></td>
                                <td>
                                    <?
                                    $cbo_dyed_yarn_qty_arr = array("1"=>"Allocated","2"=>"Available");
                                    echo create_drop_down("cbo_dyed_yarn_qty", 150, $cbo_dyed_yarn_qty_arr, "","0","--Select--","1");
                                    ?>
                                </td>
                                <td>
                                    <input type="button" name="button" class="formbutton" value="Show"
                                    onClick="show_list_view (document.getElementById('cbo_supplier').value+'**'+document.getElementById('cbo_count').value+'**'+document.getElementById('txt_desc').value+'**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_lot_no').value+'**'+<? echo $companyID; ?>+'**'+document.getElementById('cbo_dyed_yarn_qty').value , 'create_product_search_list_view', 'search_div', 'yarn_requisition_entry_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
                                    style="width:70px;"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:20px; width:100%;" id="search_div"></div>
                </fieldset>
            </form>
        </div>
        <?
	}
	else
	{
		$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
		?>
		<div align="center" style="width:900px;">
			<form name="searchfrm" id="searchfrm">
				<fieldset style="width:890px;">
					<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
					<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
					<input type="hidden" name="available_qnty" id="available_qnty"  value=""  readonly="" />
					<div><b><? echo $comps; ?></b></div>
					<div style="float:left"><b><u>Allocated Grey Yarn</u></b></div>
					<table width="100%" border="1" rules="all" class="rpt_table">
						<thead>
							<th width="40">Sl No</th>
							<th width="120">Supplier</th>
							<th width="100">Brand</th>
							<th width="60">Count</th>
							<th width="230">Composition</th>
							<th width="80">Type</th>
							<th width="80">Color</th>
							<th width="80">Lot No</th>
							<th>Allocated Bl Qnty</th>
						</thead>
					</table>
					<div style="width:100%; overflow-y:scroll; max-height:140px;" id="scroll_body" align="left">
						<table class="rpt_table" rules="all" border="1" width="870" id="tbl_list_search">
						<?
                        $job_nos = explode(",", rtrim($job_no,", "));
                        foreach ($job_nos as $job)
                        {
                            $jobs .= "'".$job."',";
                        }
                        //$testprodcond = "and b.item_id in(52404)";
                        $sql_allo = "select b.booking_no, b.job_no, b.item_id as prod_id, b.po_break_down_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.dyed_type, b.qnty as allocated_qnty from inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c where b.booking_no = '".$selected_booking_no."' and b.job_no in(".rtrim($jobs,", ").") and a.id=b.mst_id and b.item_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=1 and c.status_active=1 and c.is_deleted=0 $testprodcond";
                        //echo $sql_allo;
                        $data_array = sql_select($sql_allo);
                        $all_prod_id = '';
                        $allocationQty = 0;
                        foreach ($data_array as $row_allo)
                        {
                            $booking_no = $row_allo[csf('booking_no')];
                            $job_no = $row_allo[csf('job_no')];
                            $yarn_count_id = $row_allo[csf('yarn_count_id')];
                            $yarn_comp_type1st = $row_allo[csf('yarn_comp_type1st')];
                            $yarn_comp_percent1st = $row_allo[csf('yarn_comp_percent1st')];
                            $yarn_type_id = $row_allo[csf('yarn_type')];
                            $product_type_arr[$row_allo[csf('prod_id')]] = $row_allo[csf('dyed_type')];
                            //$product_type_arr[$row_allo[csf('booking_no')]][$row_allo[csf('prod_id')]] = $row_allo[csf('dyed_type')];

                            if($row_allo[csf('dyed_type')]!=1)
                            {
                                if ($all_prod_id == '')
                                    $all_prod_id = $row_allo[csf('prod_id')];
                                else
                                    $all_prod_id .= "," . $row_allo[csf('prod_id')];

                                if($row_allo[csf('dyed_type')]!=1)
                                {
                                    $job_total_allocation_arr[$job_no][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];

                                    if($row_allo[csf('booking_no')]!="")
                                    {
                                        $booking_alocation_arr[$row_allo[csf('booking_no')]][$row_allo[csf('prod_id')]] += $row_allo[csf('allocated_qnty')];
                                    }
                                }
                            }
                            
                            //25.06.2020
                            $expPoId = explode(',',$poId);
                            for($zs = 0; $zs <= count($expPoId); $zs++)
                            {
                                if($row_allo[csf('po_break_down_id')] == $expPoId[$zs])
                                {
                                    $allocationQty += $row_allo[csf('allocated_qnty')];
                                }
                            }
                        }
                        unset($data_array);
                        //echo "<pre>";
                        //print_r($booking_alocation_arr);
                        //die();
						
                        $all_prod_id = implode(",",array_unique(explode(",", $all_prod_id)));
                        if($all_prod_id!=""){ $prod_id_cond = "and b.product_id in($all_prod_id)";}
                        if($yarn_count_id!=""){$count_id_cond = "and b.count=$yarn_count_id";}
                        if($yarn_comp_type1st!=""){$yarn_comp_type1st_cond = "and b.yarn_comp_type1st=$yarn_comp_type1st";}
                        if($yarn_comp_percent1st!=""){$yarn_comp_percent1st_cond = "and b.yarn_comp_percent1st=$yarn_comp_percent1st";}

                        $ydsw_sql="select x.job_no, x.product_id, sum(x.yarn_wo_qty) yarn_wo_qty from(select b.job_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(41,42,114,135,94) and b.entry_form in(41,42,114,135,94) and b.job_no='".$job_no."' $prod_id_cond group by b.job_no,b.product_id
                        union all
                        select b.job_no, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form in(125,340) and b.entry_form in(125,340) and b.job_no='$job_no' $count_id_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond group by b.job_no,b.product_id )x group by x.job_no,x.product_id";
                        //echo $ydsw_sql;
                        $check_ydsw = sql_select($ydsw_sql);
                        $prod_wise_ydsw=array();
                        foreach ($check_ydsw as $row)
                        {
                            $prod_wise_ydsw[$row[csf("job_no")]][$row[csf("product_id")]] = $row[csf("yarn_wo_qty")];
                        }
                        unset($check_ydsw);
                        //echo "<pre>";
                        //print_r($prod_wise_ydsw);

						/*
						|--------------------------------------------------------------------------
						| for sales_booking_no
						|--------------------------------------------------------------------------
						|
						*/
						$sql_sales = "SELECT a.sales_booking_no AS BOOKING_NO FROM fabric_sales_order_mst a WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.job_no = '".$job_no."'";
						$sql_sales_rslt = sql_select($sql_sales);
						$booking_no_arr = array();
                        foreach ($sql_sales_rslt as $row)
                        {
                            $booking_no_arr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
                        }
                        unset($sql_sales_rslt);
                        $booking_nos = "'".implode("','",$booking_no_arr)."'";
                        //echo "<pre>";
                        //print_r($all_booking_no);

						/*
						|--------------------------------------------------------------------------
						| for program no
						|--------------------------------------------------------------------------
						|
						*/
                        $sql_program = "SELECT b.id AS ID FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no in(".$booking_nos.") and a.status_active=1 and a.is_deleted=0 "; // and b.status_active=1 and b.is_deleted=0 : ommit cause program can be deleted even after issue
						$sql_program_rslt = sql_select($sql_program);
						$program_no_arr = array();
                        foreach ($sql_program_rslt as $row)
                        {
                            $program_no_arr[$row['ID']] = $row['ID'];
                        }
                        unset($sql_program_rslt);
						$all_knit_id = implode(",", $program_no_arr);
                        //echo "<pre>";
                        //print_r($program_no_arr);

                        if ($all_prod_id != "")
                        {
                            $req_sql = "SELECT a.booking_no AS BOOKING_NO, c.knit_id AS KNIT_ID, c.prod_id AS PROD_ID, c.requisition_no AS REQUISITION_NO, c.yarn_qnty AS YARN_QNTY FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.id=c.knit_id AND b.id in (".$all_knit_id.") AND c.prod_id in(".$all_prod_id.") AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0"; // AND b.status_active=1 AND b.is_deleted=0 : ommit cause program can be deleted even after issue
                            //echo $req_sql;
                            $req_result = sql_select($req_sql);
                            foreach($req_result as $row)
                            {
                                $product_type = $product_type_arr[$row['PROD_ID']];
                                if($product_type != 1)
                                {
                                    $booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']] += $row['YARN_QNTY'];
                                }
                            }
                            unset($req_result);
                            //echo "<pre>";
                            //print_r($booking_requsition_arr);

                            $sql = "SELECT id AS ID, supplier_id SUPPLIER_ID, lot AS LOT, current_stock AS CURRENT_STOCK, yarn_comp_type1st AS YARN_COMP_TYPE1ST, yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, yarn_comp_type2nd AS YARN_COMP_TYPE2ND, yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND, yarn_count_id AS YARN_COUNT_ID, yarn_type AS YARN_TYPE, color AS COLOR, is_within_group AS IS_WITHIN_GROUP, brand as BRAND FROM product_details_master WHERE company_id = ".$companyID." AND current_stock>0 AND id in($all_prod_id) AND item_category_id=1 AND status_active=1 AND is_deleted=0 ORDER BY id";
                            //echo $sql;
							$result = sql_select($sql);
                            $i = 1;
                            $ydw_qty = 0;
                            $job_total_allocation_qty = 0;
                            $booking_total_allocation_qty = 0;
                            $existing_requsition_qty = 0;
                            $booking_issue_rtn_qty = 0;
                            $balance = 0;
                            $bal_alloc_qnty = 0;
                            foreach ($result as $row)
                            {
                                if ($i % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";

                                $compos = '';
                                if ($row['YARN_COMP_PERCENT2ND'] != 0)
                                {
                                    $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
                                }
                                else
                                {
                                    $compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
                                }
                                //echo $selected_booking_requsition_qty."-".$selected_booking_issue_rtn_qty."<br>";

                                $ydsw_qty = $prod_wise_ydsw[$job_no][$row['ID']]*1;
                                $job_total_allocation_qty = $job_total_allocation_arr[$job_no][$row['ID']]*1;
                                $existing_requsition_qty = $booking_requsition_arr[$selected_booking_no][$row['ID']]*1;
                                $booking_total_allocation_qty = ($booking_alocation_arr[$selected_booking_no][$row['ID']]-$existing_requsition_qty);

                                //echo job_total_allocation_qty."-".$ydsw_qty."+".$existing_requsition_qty; die();
                                $balance = ( $job_total_allocation_qty - ($ydsw_qty + $existing_requsition_qty) );
                                if($balance>$booking_total_allocation_qty)
                                {
                                    $cumalative_balance = $booking_total_allocation_qty;
                                }
                                else
                                {
                                    if($balance>0)
                                    {
                                        $cumalative_balance = $balance;
                                    }
                                    else
                                    {
                                        $cumalative_balance = 0;
                                    }
                                }
                                
                                //company supplier checking here
                                $company_supplier_name = ($row['IS_WITHIN_GROUP']==1?$company_arr[$row['SUPPLIER_ID']]['name']:$supplier_arr[$row['SUPPLIER_ID']]);

                                $balance_title = "PROD ID->".$row['ID']." WYDS QTY->".$ydsw_qty." JOB TALL QTY->".$job_total_allocation_qty." PREV RQTY->".$existing_requsition_qty." Balance->".$balance ." BALL QTY->(This Booking Allocation-This Booking Previous Requisition)=".$booking_total_allocation_qty ."\nBalance Formula (" .$job_total_allocation_qty."-(".$ydsw_qty."+".$existing_requsition_qty."))" ;

                                $bal_alloc_qnty = $cumalative_balance;
                                //$data = $row['LOT'] . "**" . $row['YARN_COUNT_ID'] . "**" . $row['YARN_TYPE'] . "**" . $color_library[$row['COLOR']] . "**" . $compos . "**2**" . $bal_alloc_qnty."** **".number_format($allocationQty, 2, '.', '');
                                $data = $row['LOT'] . "**" . $row['YARN_COUNT_ID'] . "**" . $row['YARN_TYPE'] . "**" . $color_library[$row['COLOR']] . "**" . $compos . "**2**" . $bal_alloc_qnty . "**" . number_format($allocationQty, 2, '.', '');
                                //$data = $row['LOT'] . "**" . $row['YARN_COUNT_ID'] . "**" . $row['YARN_TYPE'] . "**" . $color_library[$row['COLOR']] . "**" . $compos . "**".$row[csf('dyed_type')]."**" . $available_qnty."**".$cbo_dyed_yarn_qty_from;

                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
                                    onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $data; ?>');">
                                    <td width="40" align="center"><? echo $i; ?></td>
                                    <td width="120" align="center"><p><? echo $company_supplier_name; ?></p></td>
                                    <td width="100" align="center"><p><? echo $brand_arr[$row['BRAND']]; ?></p></td>
                                    <td width="60" align="center"><p><? echo $count_arr[$row['YARN_COUNT_ID']]; ?></p></td>
                                    <td width="230" align="center"><p><? echo $compos; ?></p></td>
                                    <td width="80" align="center"><p><? echo $yarn_type[$row['YARN_TYPE']]; ?></p></td>
                                    <td width="80" align="center"><p><? echo $color_library[$row['COLOR']]; ?></p></td>
                                    <td width="80" align="center"><p><? echo $row['LOT']; ?></p></td>
                                    <td align="right" title="<? echo $balance_title;?>"><? echo number_format($bal_alloc_qnty, 2); ?></td>
                                </tr>
                                <?
                                $i++;
                            }
                            unset($result);
                        }
                        else
                            echo "<tr><td colspan='9' align='center' style ='color:#F00;'><strong>No Item Found</strong></td></tr>";
                        ?>
                        </table>
                    </div>
                    <div style="float:left"><b><u>Dyed Yarn</u></b></div>
                    <table width="100%" border="1" rules="all" class="rpt_table">
                        <thead>
                            <th width="30">Sl</th>
                            <th width="100">Supplier</th>
                            <th width="100">Brand</th>
                            <th width="60">Count</th>
                            <th width="200">Composition</th>
                            <th width="70">Type</th>
                            <th width="70">Color</th>
                            <th width="80">Lot No</th>
                            <th width="70">Allocation Qty</th>
                            <th>Cu.Bal.Qty</th>
                        </thead>
                    </table>
                    <div style="width:100%; overflow-y:scroll; max-height:140px;" id="scroll_body" align="left">
                        <table class="rpt_table" rules="all" border="1" width="870" id="tbl_list_search_dyied">
                        <?
                        $req_qnty_array = array();
                        $sql_requs = "select c.prod_id, sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b, ppl_yarn_requisition_entry c where b.id=a.mst_id and a.id=c.knit_id and a.id in (".$all_knit_id.") and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no = '".$selected_booking_no."' group by c.prod_id"; // and a.status_active=1 and a.is_deleted=0 : ommit cause program can be deleted even after issue
                        //$product_id = "";
                        $sql_requs_result = sql_select($sql_requs);
                        foreach ($sql_requs_result as $row)
                        {
                            $req_qnty_array[$row[csf('prod_id')]]['req'] = $row[csf('yarn_qnty')];
                        }
                        unset($sql_requs_result);

                        $check_ysw=sql_select("select x.wo_num, x.product_id, sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.yarn_dyeing_prefix_num, ',') WITHIN GROUP (ORDER BY b.id) as wo_num, b.product_id, sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(94,340) and b.entry_form in(94,340) and a.service_type not in(7) and b.fab_booking_no = '".$selected_booking_no."' and b.job_no='".$job_no."' group by b.product_id)x group by x.wo_num,x.product_id");
                        $ysw_qnty_arr = array();
                        foreach ($check_ysw as $row)
                        {
                            $ysw_qnty_arr[$row[csf('product_id')]] = $row[csf('yarn_wo_qty')];
                        }
                        unset($check_ysw);

                        $i = 1;
                        $bal_alloc_qnty = 0;
                        $sql_dyied_yarn_sql = "select a.job_no, a.po_break_down_id, a.item_id,sum(a.qnty) as qnty, sum(c.allocated_qnty) as allo_qty, c.id prod_id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_percent1st, c.yarn_comp_percent2nd, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.color, c.supplier_id, c.is_within_group, c.brand from inv_material_allocation_mst a, product_details_master c where a.booking_no = '".$selected_booking_no."' and a.job_no='".$job_no."' and a.item_id=c.id and a.status_active=1 and a.is_deleted=0 and a.is_dyied_yarn=1 and a.booking_without_order = 0 group by a.job_no, a.po_break_down_id, a.item_id, c.id, c.lot, c.yarn_count_id, c.yarn_type, c.yarn_comp_percent1st, c.yarn_comp_percent2nd, c.yarn_comp_type1st, c.yarn_comp_type2nd, c.color, c.supplier_id, c.is_within_group, c.brand";
                        //echo $sql_dyied_yarn_sql;
                        $dyedYarnData = sql_select($sql_dyied_yarn_sql);
                        foreach ($dyedYarnData as $row)
                        {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $compos = '';
                            if ($row[csf('yarn_comp_percent2nd')] != 0)
                            {
                                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
                            }
                            else
                            {
                                $compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
                            }
                            
                            //company supplier checking here
                            $company_supplier_name = ($row[csf('is_within_group')]==1?$company_arr[$row[csf('supplier_id')]]['name']:$supplier_arr[$row[csf('supplier_id')]]);

                            $allocation_qty = $row[csf('qnty')];
                            $requstion_qty = $req_qnty_array[$row[csf('prod_id')]]['req'];
                            $ysw_qnty = $ysw_qnty_arr[$row[csf('prod_id')]];
                            $cu_balance_qty = ($allocation_qty - ($requstion_qty+$ysw_qnty));

                            $balance_title = "PROD ID->".$row[csf('prod_id')]." YSW QTY->".$ysw_qnty.", PREV RQTY->".$requstion_qty.", Balance=($allocation_qty - ($requstion_qty+$ysw_qnty))";

                            $data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**1**" . $cu_balance_qty;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="searchf<? echo $i; ?>" style="cursor:pointer"
                                onClick="js_set_value(<? echo $row[csf('prod_id')]; ?>,'<? echo $data; ?>');">
                                <td width="30" align="center"><? echo $i; ?></td>
                                <td width="100" align="center"><? echo $company_supplier_name; ?></td>
                                <td width="100" align="center"><? echo $brand_arr[$row[csf('brand')]]; ?></td>
                                <td width="60" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
                                <td width="200" align="center"><? echo $compos; ?></td>
                                <td width="70" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
                                <td width="70" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
                                <td width="80" align="center" title="<? echo $row[csf('prod_id')]; ?>" ><? echo $row[csf('lot')]; ?></td>
                                <td width="70" align="right"><? echo number_format($allocation_qty, 2); ?></td>
                                <td align="right" title="<? echo $balance_title; ?>"><? echo number_format($cu_balance_qty, 2); ?></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </table>
                </div>
            </fieldset>
        </form>
    </div>
    <?php
	}
	?>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_product_search_list_view")
{
	$data = explode('**', $data);

	if ($data[0] == 0) $supp_cond = ""; else $supp_cond = " and a.supplier_id='" . trim($data[0]) . "' ";
	if ($data[1] == 0) $yarn_count_cond = ""; else $yarn_count_cond = " and a.yarn_count_id='" . trim($data[1]) . "' ";
	if ($data[3] == 0) $yarn_type_cond = ""; else $yarn_type_cond = " and a.yarn_type='" . trim($data[3]) . "' ";

	$yarn_desc_cond = " and a.supplier_id like '%" . trim($data[2]) . "%'";
	$lot_no_cond = " and lot like '%" . trim($data[4]) . "%'";
	$companyID = $data[5];
	$cbo_dyed_yarn_qty_from = $data[6];


	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	?>
	<table width="1150" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="40">Sl No.</th>
			<th width="70">Buyer ID</th>
			<th width="70">Count</th>
			<th width="150">Composition</th>
			<th width="80">Type</th>
			<th width="80">Color</th>
			<th width="80">Lot No</th>
			<th width="120">Supplier</th>
			<th width="80">Wgt. Bag/Cone</th>
			<th width="100">Current Stock</th>
			<th width="80">Allocated Qnty</th>
			<th width="80">Available For Req.</th>
			<th width="60">Age (Days)</th>
			<th>DOH</th>
		</thead>
	</table>
	<table width="1150" class="rpt_table" rules="all" border="1" id="tbl_list_search">
		<?
		$date_array = array();
		if($db_type==0)
		{
			$buyer_id_list = "group_concat(buyer_id) as buyer_id";
			$weight_per_bag_list = "group_concat(weight_per_bag) as weight_per_bag";

		}
		else
		{
			$buyer_id_list = "listagg(buyer_id, ',') within group (order by buyer_id) as buyer_id";
			$weight_per_bag_list = "listagg(weight_per_bag, ',') within group (order by weight_per_bag) as weight_per_bag";
		}

		$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date,$buyer_id_list,$weight_per_bag_list from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and receive_basis in(1,2,4) group by prod_id";

		$result_returnRes_date = sql_select($returnRes_date);
		foreach ($result_returnRes_date as $row)
		{
			$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
			$trans_info_arr[$row[csf("prod_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$trans_info_arr[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
			$trans_info_arr[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
		}

		$sql = "select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type, sum(b.yarn_qnty) yarn_qnty
		from product_details_master a left join ppl_yarn_requisition_entry b on a.id=b.prod_id
		where a.company_id=$companyID and a.current_stock>0 and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type order by a.lot";
		$result = sql_select($sql);
		$i = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
			$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

			$buyer = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));
			$weight_per_bag = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));
			$weight_per_cone = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));

			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			}
			else
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}

			if( $row[csf('dyed_type')]==1)
			{
				if($cbo_dyed_yarn_qty_from==1)
				{
					$available_qnty = $row[csf('allocated_qnty')];
				}
				else
				{
					$available_qnty = $row[csf('available_qnty')];
				}
			}
			else
			{
				$available_qnty = $row[csf('available_qnty')];
			}
			//$available_qnty = ($row[csf('dyed_type')]==1)?$row[csf('allocated_qnty')]:$row[csf('available_qnty')];
			$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**".$row[csf('dyed_type')]."**" . $available_qnty."**".$cbo_dyed_yarn_qty_from;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>');">
				<td width="40" align="center"><? echo $i; ?></td>
				<td width="70"><p><? echo $buyer_arr[$buyer]; ?></p></td>
				<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
				<td width="150"><p><? echo $compos; ?></p></td>
				<td width="80" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
				<td width="80" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
				<td width="80" align="center"><? echo $row[csf('lot')]; ?></td>
				<td width="120"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
				<td width="80" align="center"><? echo 'Bg:' .$weight_per_bag . '; ' .'<br>'. 'Cn:' . $weight_per_cone; ?></td>
				<td width="100" align="right"><? echo number_format($row[csf('current_stock')], 2); ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('allocated_qnty')], 2); ?></td>
				<td width="80" align="right"><? echo number_format($available_qnty, 2); ?></td>
				<td width="60" align="center"><? echo $ageOfDays; ?></td>
				<td align="center"><? echo $daysOnHand; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if ($action == "create_product_search_list_view-old")
{
	$data = explode('**', $data);

	if ($data[0] == 0) $supp_cond = ""; else $supp_cond = " and a.supplier_id='" . trim($data[0]) . "' ";
	if ($data[1] == 0) $yarn_count_cond = ""; else $yarn_count_cond = " and a.yarn_count_id='" . trim($data[1]) . "' ";
	if ($data[3] == 0) $yarn_type_cond = ""; else $yarn_type_cond = " and a.yarn_type='" . trim($data[3]) . "' ";

	$yarn_desc_cond = " and a.supplier_id like '%" . trim($data[2]) . "%'";
	$lot_no_cond = " and lot like '%" . trim($data[4]) . "%'";
	$companyID = $data[5];

	$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

	?>
	<table width="1210" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="40">Sl No</th>
			<th width="70">Buyer ID</th>
			<th width="70">Count</th>
			<th width="170">Composition</th>
			<th width="80">Type</th>
			<th width="80">Color</th>
			<th width="80">Lot No</th>
			<th width="140">Supplier</th>
			<th width="80">Wgt. Bag/Cone</th>
			<th width="80">Current Stock</th>
			<th width="80">Allocated Qnty</th>
			<th width="80">Available For Req.</th>
			<th width="80">Age (Days)</th>
			<th>DOH</th>
		</thead>
	
	
		<tbody class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
			$date_array = array();
			if($db_type==0)
			{
				$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date,group_concat(buyer_id) as buyer_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and receive_basis in(1,2,4) group by prod_id";
			}else {
				$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date,listagg(buyer_id, ',') within group (order by buyer_id) as buyer_id, max(weight_per_bag) as weight_per_bag, max(weight_per_cone) as weight_per_cone from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and receive_basis in(1,2,4) group by prod_id";
			}

			$result_returnRes_date = sql_select($returnRes_date);
			foreach ($result_returnRes_date as $row) {
				$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
				$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
				$trans_info_arr[$row[csf("prod_id")]]['buyer_id'] = $row[csf("buyer_id")];
				$trans_info_arr[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
				$trans_info_arr[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
			}

			$sql = "select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,a.dyed_type 
			from product_details_master a where a.company_id=$companyID and a.current_stock>0 and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd,a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,a.dyed_type order by a.lot";

			//echo $sql;
			// left join inv_transaction d on (a.id=d.prod_id and d.receive_basis in(1,2,4))
			//,sum(b.yarn_qnty) yarn_qnty
			//left join ppl_yarn_requisition_entry b on a.id=b.prod_id
			
			$result = sql_select($sql);
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
				$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));

				$buyer = implode(",",array_unique(explode(",",$trans_info_arr[$row[csf("id")]]['buyer_id'])));
				$weight_per_bag = $trans_info_arr[$row[csf("id")]]['weight_per_bag'];
				$weight_per_cone = $trans_info_arr[$row[csf("id")]]['weight_per_cone'];

				$compos = '';
				if ($row[csf('yarn_comp_percent2nd')] != 0) {
					$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
				} else {
					$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
				}
				//$available_qnty = ($row[csf('current_stock')] - $row[csf('yarn_qnty')]);
				$available_qnty = $row[csf('available_qnty')];			
				$data = $row[csf('lot')] . "**" . $row[csf('yarn_count_id')] . "**" . $row[csf('yarn_type')] . "**" . $color_library[$row[csf('color')]] . "**" . $compos . "**" . $available_qnty. "**" . $row[csf('dyed_type')];
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
					onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="70" align="center"><? echo $buyer_arr[$buyer]; ?></td>
					<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
					<td width="170" align="center"><p><? echo $compos; ?></p></td>
					<td width="80" align="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
					<td width="80" align="center"><? echo $color_library[$row[csf('color')]]; ?></td>
					<td width="80" align="center"><? echo $row[csf('lot')]; ?></td>
					<td width="140" align="center"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
					<td width="80" align="center"><? echo 'Bg:' .$weight_per_bag . '; ' .'<br>'. 'Cn:' . $weight_per_cone; ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('current_stock')], 2); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('allocated_qnty')], 2); ?></td>
					<td width="80" align="right"><? echo number_format($available_qnty, 2); ?></td>
					<td width="80" align="center"><? echo $ageOfDays; ?></td>
					<td align="center"><? echo $daysOnHand; ?></td>
				</tr>
				
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}

if ($action == "requisition_info_details")
{
	?>
	<table width="890" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="80">Lot No </th>
			<th width="100">Brand </th>
			<th width="70">Count</th>
			<th width="80">Type</th>
			<th width="150">Composition</th>
			<th width="90">Color</th>
			<th width="90">No of Cone</th>
			<th width="90">Requisition Date</th>
			<th>Yarn Reqs. Qnty</th>
		</thead>
	</table>
	<div style="width:890px; overflow-y:scroll; max-height:300px;" id="scroll_body" align="left">
		<table class="rpt_table" rules="all" border="1" width="873" id="tbl_list_search">
			<?
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0",'id','yarn_count');
			$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
			$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0","id","brand_name");

			if($data!="")
			{
				$query = sql_select("select id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty from ppl_yarn_requisition_entry where knit_id=$data and status_active = '1' and is_deleted = '0'");
			}
			
			$i = 1;
			$tot_yarn_qnty = 0;
			foreach ($query as $selectResult)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where status_active=1 and is_deleted=0 and id=" . $selectResult[csf('prod_id')] . "");

				$compos = '';
				if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0)
				{
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
				}
				else
				{
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
				}

				$tot_yarn_qnty += $selectResult[csf('yarn_qnty')];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
					onClick="get_php_form_data('<? echo $selectResult[csf('id')]; ?>', 'populate_requisition_data', 'yarn_requisition_entry_sales_v2_controller' );">
					<td width="80"><p><? echo $dataArray[0][csf('lot')]; ?></p></td>
					<td width="100"><p><? echo $brand_arr[$dataArray[0][csf('brand')]]; ?></p></td>
					<td width="70"><p><? echo $count_arr[$dataArray[0][csf('yarn_count_id')]]; ?></p></td>
					<td width="80"><p><? echo $yarn_type[$dataArray[0][csf('yarn_type')]]; ?></p></td>
					<td width="150"><p><? echo $compos; ?></p></td>
					<td width="90"><p><? echo $color_library[$dataArray[0][csf('color')]]; ?></p></td>
					<td align="right" width="90"><? echo number_format($selectResult[csf('no_of_cone')], 0); ?></td>
					<td align="center"
					width="90"><? echo change_date_format($selectResult[csf('requisition_date')]); ?></td>
					<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			<tfoot>
				<th colspan="8">Total</th>
				<th><? echo number_format($tot_yarn_qnty, 2); ?></th>
			</tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action == "populate_requisition_data")
{
	$expData = explode('_', $data);
	//$poId = $expData[1];
	if($data!="")
	{
		$sql = "select a.id, a.knit_id, a.requisition_no, a.prod_id, a.no_of_cone, a.requisition_date, a.yarn_qnty, b.program_qnty, c.company_id, c.booking_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c where a.knit_id=b.id and b.mst_id=c.id and a.id=".$expData[0]."";
	}
	
	//echo $sql;
	$data_array = sql_select($sql);
	foreach ($data_array as $row)
	{
		$bookingNo = $row[csf('booking_no')];
		$productId = $row[csf('prod_id')];
		$requisitionQty = $row[csf('yarn_qnty')];
		$programQty = $row[csf('program_qnty')];
		
		/*
		|--------------------------------------------------------------------------
		| variable settings Planning
		| if variable list is auto allocation yarn form requisition and
		| auto allocation yarn form requisition is yes then
		| available_qty = allocation_qty - (requsition_qty + yarn_dyeing_qty + yarn_service_qty)
		| otherwise available qty = product_details_mst table's available_qty
		|--------------------------------------------------------------------------
		|
		*/
		$is_auto_allocation = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name=".$row[csf('company_id')]." and variable_list=6 and status_active=1 and is_deleted=0");
		if($is_auto_allocation != 1)
		{
			/*
			|--------------------------------------------------------------------------
			| for allocation balance check
			|--------------------------------------------------------------------------
			|
			*/
			$sql_fabric_sales_order = "SELECT id AS ID, job_no AS JOB_NO FROM fabric_sales_order_mst WHERE sales_booking_no = '".$bookingNo."'";
			$sql_fabric_sales_order_rslt = sql_select($sql_fabric_sales_order);
			foreach($sql_fabric_sales_order_rslt as $sales_row)
			{
				$job_no = $sales_row['JOB_NO'];
				$po_id = $sales_row['ID'];
			}

			$arr = array();
			$arr['product_id'] = $productId;
			$arr['is_auto_allocation'] = 0;
			$arr['job_no'] = $job_no;
			$arr['booking_no'] = $bookingNo;
			$arr['po_id'] = $po_id;
			$allocation_arr = get_allocation_balance($arr);
			$booking_requisition_qty = $allocation_arr['booking_requisition'][$bookingNo][$productId]['qty']*1;
			$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$job_no][$productId]['qty']*1;
			$booking_allocation_qty = $allocation_arr['booking_allocation'][$bookingNo][$productId]*1;
		}

		$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, available_qnty from product_details_master where id =" . $row[csf('prod_id')]);
		$compos = '';
		if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0)
		{
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
		}
		
		//is_auto_allocation
		if($is_auto_allocation != 1)
		{
			$availableQty = ($booking_allocation_qty+$requisitionQty)-($booking_requisition_qty+$yarn_dyeing_service_qty);
		}
		else
		{
			$availableQty = $requisitionQty + $dataArray[0][csf("available_qnty")];
		}

		$color = return_field_value("color_name", "lib_color", "id='" . $dataArray[0][csf("color")] . "'");

		echo "document.getElementById('txt_requisition_no').value 			= '" . $row[csf("requisition_no")] . "';\n";
		echo "document.getElementById('txt_lot').value 						= '" . $dataArray[0][csf("lot")] . "';\n";
		echo "document.getElementById('cbo_yarn_count').value 				= '" . $dataArray[0][csf("yarn_count_id")] . "';\n";
		echo "document.getElementById('cbo_yarn_type').value 				= '" . $dataArray[0][csf("yarn_type")] . "';\n";
		echo "document.getElementById('txt_composition').value 				= '" . $compos . "';\n";
		echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
		echo "document.getElementById('txt_no_of_cone').value 				= '" . $row[csf("no_of_cone")] . "';\n";
		echo "document.getElementById('txt_reqs_date').value 				= '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('txt_yarn_qnty').value 				= '" . $requisitionQty . "';\n";
		echo "document.getElementById('prod_id').value 						= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_id').value 			= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_qnty').value 			= '" . $requisitionQty . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('companyID').value 				    = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('hidden_lot_available_qnty').value 	= '" . $availableQty . "';\n";
		echo "document.getElementById('txt_available_qty').value 			= '" . $availableQty . "';\n";
		echo "document.getElementById('hdn_booking_no').value 				= '" . $row[csf('booking_no')] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_yarn_req_entry',1);\n";
		exit();
	}
}

if ($action == "barcode_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		<?
		$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][120]);
		echo "var field_level_data= " . $data_arr . ";\n";
		?>
		window.onload = function () {
			set_field_level_access( <? echo $companyID; ?> );
		}
		var selected_id = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_barcode_no' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_barcode_no' + str).val());
			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_barcode_no' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}

			id = id.substr(0, id.length - 1);

			$('#hidden_barcode_nos').val(id);
		}

	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>PO Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Sales Order No</th>
						<th>Barcode No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
						<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos" value=""/>
					</thead>
					<tbody>
						<tr>
							<td id="buyer_td">
								<?
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>

							<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td>

							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value +'**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'yarn_requisition_entry_sales_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:90px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:05px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_barcode_search_list_view")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'short_name');

	$company_id = $data[0];
	$po_buyer_id = $data[1];
	$within_group = $data[2];
	$search_by = $data[3];
	$search_string = trim($data[4]);
	$barcode_no = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "%'";
		} else if ($search_by == 3) {
			$search_field_cond = " and a.sales_booking_no like '%" . $search_string . "%'";
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($barcode_no != "") {
		$search_field_cond = " and b.barcode_no = '" . $barcode_no . "'";
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and a.within_group=$within_group";

	if ($po_buyer_id == 0)
	{
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$po_buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$po_buyer_id_cond = "";
		}
	} else {
		$po_buyer_id_cond = " and a.buyer_id=$po_buyer_id";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later


	if($db_type == 0){
		$booking_year = " c.booking_year";
		$booking_year2 = "YEAR(c.booking_date) as booking_year";
	}else{
		$booking_year = " cast(c.booking_year as varchar(4000)) as booking_year";
		$booking_year2 = "to_char(c.booking_date,'YYYY') as booking_year";
	}

	if ($within_group == 1) {
		$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no_prefix_num,$booking_year,b.barcode_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no = c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $po_buyer_id_cond
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, c.buyer_id po_buyer,c.booking_no_prefix_num,$booking_year2,b.barcode_no  from fabric_sales_order_mst a , fabric_sales_order_dtls b , wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.sales_booking_no = c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond order by id";
	} else {
		$sql = "select a.id, $year_field,a.job_no_prefix_num,a.job_no,a.within_group,a.sales_booking_no booking_no_prefix_num,a.booking_date,a.buyer_id,a.style_ref_no,a.location_id,b.barcode_no from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond and within_group=2 order by a.id";
	}
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Sales Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th width="60">Booking Year</th>
			<th width="100">Barcode</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:870px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			if ($row[csf('within_group')] == 1)
				$buyer = $company_arr[$row[csf('buyer_id')]];
			else
				$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
				<td width="40" align="center"><? echo $i; ?>
				<input type="hidden" name="txt_barcode_no" id="txt_barcode_no<?php echo $i ?>"
						value="<? echo $row[csf('barcode_no')]; ?>"/>
				<?php
				if ($within_group == 1) {
					$booking = $row[csf('booking_no_prefix_num')];
				} else {
					$booking = $row[csf('sales_booking_no')];
				}
				?>
				<input type="hidden" name="txt_booking_no" id="txt_booking_no<?php echo $i ?>"
				value="<? echo $row[csf('booking_no_prefix_num')]; ?>"/>
			</td>
			<td width="70" align="center"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
			<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
			<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('booking_year')]; ?></p></td>
			<td width="100" align="center"><p><? echo $row[csf('barcode_no')]; ?></p></td>
			<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
		</tr>
		<?
		$i++;
	}
	?>
	</table>
	</div>
	<table width="760" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	
	/*
	|--------------------------------------------------------------------------
	| variable settings Planning
	| if variable list is auto allocation yarn form requisition and
	| Auto Allocate Yarn From Requisition is yes then
	| available_qnty comes from product_details_master table otherwise
	| available_qnty will be calculative
	|--------------------------------------------------------------------------
	|
	*/
	$is_auto_allocation_variable_settings = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name=".$companyID." and variable_list=6 and status_active=1 and is_deleted=0");
	/*echo "17**";
	disconnect($con);
	exit();*/

	/*
	|--------------------------------------------------------------------------
	| for insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation == 0)
	{
		$con = connect();
		$job_no = str_replace("'", "", $job_no);
		$prod_id = str_replace("'", "", $prod_id);
		$is_dyed_yarn = str_replace("'", "", $is_dyed_yarn);
		$poId = str_replace("'", "", $sale_order_id);
		$selected_booking_no = str_replace("'", "", $hdn_booking_no);
		
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		/*
		|--------------------------------------------------------------------------
		| duplicate product requisition checking
		|--------------------------------------------------------------------------
		|
		*/
		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=".$updateId." and prod_id=".$prod_id." and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**" . str_replace("'", "", $updateId) . "**0";
			disconnect($con);
			exit();
		}
		
		/*
		|--------------------------------------------------------------------------
		| for allocation balance check
		|--------------------------------------------------------------------------
		|
		*/
		$arr = array();
		$arr['product_id'] = $prod_id;
		$arr['is_auto_allocation'] = $is_auto_allocation_variable_settings;
		$arr['job_no'] = $job_no;
		$arr['booking_no'] = $selected_booking_no;
		$arr['po_id'] = $poId;

		$allocation_arr = get_allocation_balance($arr);
		$booking_requisition_qty = $allocation_arr['booking_requisition'][$selected_booking_no][$prod_id]['qty']*1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$job_no][$prod_id]['qty']*1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$selected_booking_no][$prod_id]*1;
		$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);
		
		if($is_auto_allocation_variable_settings == 1)
		{
			$available_qty = $allocation_arr['available_qnty'];
			if($allocation_arr['current_stock'] < 0.01)
			{
				echo "17**Stock Quantity is not Available\nStock quantity = ".$allocation_arr['current_stock']."";
				disconnect($con);
				exit();
			}
		}
		
		if (str_replace("'", "", $txt_yarn_qnty) > $available_qty)
		{
			echo "17**Quantity is not available for Requisition.\nAvailable quantity = ".$available_qty;
			disconnect($con);
			exit();
		}

		$requisition_no = return_field_value("requisition_no", "ppl_yarn_requisition_entry", "knit_id=$updateId","requisition_no");
		if ($requisition_no == "")
		{
			$requisition_no = return_next_id("requisition_no", "ppl_yarn_requisition_entry", 1);
		}
		else
			$requisition_no = $requisition_no;

		/*
		|--------------------------------------------------------------------------
		| prepare data to update allocation
		|--------------------------------------------------------------------------
		|
		*/
		if($updateId!="")
		{
			$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=".$updateId." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id";
		}
		
		$planning_array = sql_select($sql);
		$po_id = $planning_array[0][csf('po_id')];
		$booking_no = $planning_array[0][csf('booking_no')];
		if($is_dyed_yarn==2 || $is_dyed_yarn==0 || $is_dyed_yarn=="")
		{
			$is_dyed_yarn = 0;
		}
		else
		{
			$is_dyed_yarn = $is_dyed_yarn;
		}
		
		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_entry
		| data preparing
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id("id", "ppl_yarn_requisition_entry", 1);
		$field_array = "id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty, inserted_by, insert_date";
		$data_array = "(" . $id . "," . $updateId . "," . $requisition_no . "," . $prod_id . "," . $txt_no_of_cone . "," . $txt_reqs_date . "," . $txt_yarn_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		if ($is_auto_allocation_variable_settings == 1) 
		{
			$rID2 = false;
			$rID3 = false;
			$rID4 = false;

			/*
			|--------------------------------------------------------------------------
			| allocation checking
			|--------------------------------------------------------------------------
			|
			*/
			$sql_allocation = "select id, qnty from inv_material_allocation_mst a where a.po_break_down_id='".$po_id."' and a.item_id=" . str_replace("'", "", $prod_id) . " and a.job_no='".$job_no."' and a.booking_no='".$booking_no."' and a.status_active=1 and a.is_deleted=0";
			$check_allocation_array = sql_select($sql_allocation);
			if (!empty($check_allocation_array)) 
			{
				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_mst
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty)*1;
				$mst_id = $check_allocation_array[0][csf('id')];
				$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty+$txt_yarn_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);

				/*
				|--------------------------------------------------------------------------
				| inv_material_allocation_dtls
				| data preparing and updating
				|--------------------------------------------------------------------------
				|
				*/
				$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty+$txt_yarn_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=".$mst_id." and job_no='".$job_no."' and po_break_down_id='".$po_id."' and item_id = ".$prod_id."", 0);
			} 
			else 
			{
				if($job_no!="")
				{
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
					$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
					$data_array_allocation_mst = "(" . $id_allocation . ",".$id.",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $is_dyed_yarn . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
					
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
					$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $is_dyed_yarn . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
				}
			}
			
			/*
			|--------------------------------------------------------------------------
			| if the query result for
			| table inv_material_allocation_mst and
			| table inv_material_allocation_dtls is true then
			| the query for table product_details_master will be execute
			|--------------------------------------------------------------------------
			|
			*/
			if($rID2 && $rID3)
			{
				$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_yarn_qnty),available_qnty=(current_stock-(allocated_qnty+$txt_yarn_qnty)),update_date='" . $pc_date_time . "' where id=$prod_id", 0);	
			}						
		}
		else
		{
			$rID2 = true;
			$rID3 = true;
			$rID4 = true;
		}
		
		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_entry
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$rID1 = sql_insert("ppl_yarn_requisition_entry", $field_array, $data_array, 0);
		
		/*
		|--------------------------------------------------------------------------
		| if the query result for
		| table ppl_yarn_requisition_entry and
		| table inv_material_allocation_mst and
		| table inv_material_allocation_dtls is true then
		| the query for table product_details_master will be execute
		|--------------------------------------------------------------------------
		|
		*/
		if ($is_auto_allocation_variable_settings == 1) 
		{
			if($rID1 && $rID2 && $rID3)
			{
				$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_yarn_qnty),available_qnty=(current_stock-(allocated_qnty+$txt_yarn_qnty)),update_date='" . $pc_date_time . "' where id=$prod_id", 0);	
			}						
		}
		
		/*oci_rollback($con);
		echo "10**$rID1 && $rID2 && $rID3 && $rID4";
		disconnect($con);
		die;*/

		if ($db_type == 0)
		{
			if ($rID1 && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**" . str_replace("'", "", $updateId) . "**0";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($rID1 && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
			}
			else
			{
				oci_rollback($con);
				echo "5**" . str_replace("'", "", $updateId) . "**0";
			}
		}
		disconnect($con);
		die;
	} 

	/*
	|--------------------------------------------------------------------------
	| for update
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation == 1)
	{
		$con = connect();
		$original_prod_id = str_replace("'", "", $original_prod_id);
		
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}
		
		/*
		|--------------------------------------------------------------------------
		| duplicate product requisition checking
		|--------------------------------------------------------------------------
		|
		*/
		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=".$updateId." and prod_id=".$prod_id." and id<>".$update_dtls_id." and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**" . str_replace("'", "", $updateId) . "**1";
			disconnect($con);
			exit();
		}
		
		/*
		|--------------------------------------------------------------------------
		| for allocation balance check
		|--------------------------------------------------------------------------
		|
		*/
		$job_no_zs = str_replace("'", "", $job_no);
		$prod_id_zs = str_replace("'", "", $prod_id);
		$booking_no_za = str_replace("'", "", $hdn_booking_no);
		$arr = array();
		$arr['product_id'] = $prod_id_zs;
		$arr['is_auto_allocation'] = $is_auto_allocation_variable_settings;
		$arr['job_no'] = $job_no_zs;
		$arr['booking_no'] = $booking_no_za;
		$arr['po_id'] = $poId;
		$allocation_arr = get_allocation_balance($arr);
		$booking_requisition_qty = $allocation_arr['booking_requisition'][$booking_no_za][$prod_id_zs]['qty']*1;
		$yarn_dyeing_service_qty = $allocation_arr['yarn_dyeing_service'][$job_no_zs][$prod_id_zs]['qty']*1;
		$booking_allocation_qty = $allocation_arr['booking_allocation'][$booking_no_za][$prod_id_zs]*1;
		$available_qty = $booking_allocation_qty - ($booking_requisition_qty + $yarn_dyeing_service_qty);
		
		if($is_auto_allocation_variable_settings == 1)
		{
			$available_qty = $allocation_arr['available_qnty'];
			if($allocation_arr['current_stock'] < 0.01)
			{
				echo "17**Stock Quantity is not Available\nStock quantity = ".$allocation_arr['current_stock']."";
				disconnect($con);
				exit();
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| product checking
		| if original product and update product is same then
		|--------------------------------------------------------------------------
		|
		*/
		if(str_replace("'", "", $prod_id) == str_replace("'", "", $original_prod_id))
		{
			$qnty_limit = (str_replace("'", "", $original_prod_qnty)+$available_qty);
			if (str_replace("'", "", $txt_yarn_qnty) > $qnty_limit)
			{
				echo "17**Quantity is not available for Requisition.\nAvailable quantity = ".$qnty_limit;
				disconnect($con);
				exit();
			}
		}
		else
		{
			if (str_replace("'", "", $txt_yarn_qnty) > $available_qty)
			{
				echo "17**Quantity is not available for Requisition.\nAvailable quantity = ".$available_qty;
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| for daily demand check
		| if daily demand found then
		| lot can not be changed and
		| requisition qty can not be less than daily demand
		|--------------------------------------------------------------------------
		|
		*/
		$check_demand_entry = sql_select("select sum(yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls where requisition_no=".$txt_requisition_no." and prod_id=".$original_prod_id." and status_active=1 and is_deleted=0");
		if($original_prod_id != str_replace("'", "", $prod_id))
		{
			if(!empty($check_demand_entry) && $check_demand_entry[0][csf("yarn_demand_qnty")] > 0)
			{
				echo "17**Demand found. Lot can not be changed.";
				disconnect($con);
				exit();
			}
		}
		else
		{
			if(($check_demand_entry[0][csf("yarn_demand_qnty")] != "") && (str_replace("'", "", $txt_yarn_qnty) < $check_demand_entry[0][csf("yarn_demand_qnty")]))
			{
				echo "17**Requisition quantity can not be less than daily yarn demand entry.";
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| for requisition check
		| if issue found against requisition then
		| requisition quantity can not be less than issue quantity
		|--------------------------------------------------------------------------
		|
		*/
		$check_issue = sql_select("select sum(a.cons_quantity) issue_qnty from inv_transaction a where a.transaction_type = 2 and a.requisition_no=$txt_requisition_no and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$original_prod_id");

		$check_issue_return = sql_select("select sum(b.cons_quantity) issue_return_qnty from inv_transaction b where issue_id in(select a.mst_id from inv_transaction a where a.transaction_type in(2) and a.requisition_no=$txt_requisition_no and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$original_prod_id) and b.transaction_type=4 and b.receive_basis in (3,8) and b.prod_id=$original_prod_id and b.requisition_no=$txt_requisition_no");

		//echo (str_replace("'", "", $txt_yarn_qnty) ."<". ($check_issue[0][csf('issue_qnty')]."-".$check_issue_return[0][csf('issue_return_qnty')])); die;

		$issueQty = $check_issue[0][csf('issue_qnty')]; 
		$issueReturnQty = $check_issue_return[0][csf('issue_return_qnty')];		
		$balanceQnty = ($issueQty - $issueReturnQty);
		$requisitionQty = str_replace("'", "", $txt_yarn_qnty)*1;

		//echo "17**".$requisitionQty ."<". $balanceQnty ; die;
		//echo "17**".$check_issue[0][csf('issue_qnty')];

		if($original_prod_id != str_replace("'", "", $prod_id))
		{
			if ( (!empty($check_issue)) && ($check_issue[0][csf('issue_qnty')] !=null) )
			{
				echo "17**Issue found.You can not change this lot.";
				disconnect($con);
				exit();
			}
		}
		else
		{
			//echo "10**";
			//echo (str_replace("'", "", $txt_yarn_qnty) ."<". ($check_issue[0][csf('issue_qnty')]-$check_issue_return[0][csf('issue_return_qnty')])); die;

			$issueQty = $check_issue[0][csf('issue_qnty')]; 
			$issueReturnQty = $check_issue_return[0][csf('issue_return_qnty')];		
			$balanceQnty = number_format(($issueQty - $issueReturnQty), 2, '.', '');
			$requisitionQty = str_replace("'", "", $txt_yarn_qnty)*1;
			
			if ( !empty($check_issue) && ( ($requisitionQty < $balanceQnty) || ( 0>$balanceQnty ) ) )
			{
				echo "17**Issue Found.\nIssue Quantity =$issueQty\nIssue Return quantity =$issueReturnQty\nUpto Reduce Balance =$balanceQnty";
				disconnect($con);
				exit();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| variable settings Planning
		| if variable list is auto allocation yarn form requisition and
		| Auto Allocate Yarn From Requisition is yes
		|--------------------------------------------------------------------------
		|
		*/
		if ($is_auto_allocation_variable_settings == 1) 
		{
			$rID2 = false;
			$rID3 = false;
			$rID4 = false;
			$rID5 = false;
			$rID6 = false;
			
			if($updateId!="")
			{
				$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=".$updateId." group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc,c.po_id";
			}
			
			$planning_array = sql_select($sql);
			$po_id = $planning_array[0][csf('po_id')];
			$booking_no = $planning_array[0][csf('booking_no')];
			$job_no = str_replace("'", "", $job_no);
			$prod_id = str_replace("'", "", $prod_id);
			$is_dyed_yarn = str_replace("'", "", $is_dyed_yarn);
			
			if($is_dyed_yarn==2 || $is_dyed_yarn==0 || $is_dyed_yarn=="")
			{
				$is_dyed_yarn = 0;
			}
			else
			{
				$is_dyed_yarn = $is_dyed_yarn;
			}
			
			/*
			|--------------------------------------------------------------------------
			| product checking
			| if original product and update product is same then
			|--------------------------------------------------------------------------
			|
			*/
			if(str_replace("'", "", $prod_id) == str_replace("'", "", $original_prod_id))
			{
				$rID5 = true;
				$rID6 = true;
				$rID7 = true;
				
				/*
				|--------------------------------------------------------------------------
				| allocation checking
				|--------------------------------------------------------------------------
				|
				*/
				$sql_allocation = "select id, qnty from inv_material_allocation_mst a where a.po_break_down_id='".$po_id."' and a.item_id=".str_replace("'", "", $prod_id)." and a.job_no='".$job_no."' and a.booking_no='".$booking_no."' and a.status_active=1 and a.is_deleted=0";
				$check_allocation_array = sql_select($sql_allocation);
				if (!empty($check_allocation_array)) 
				{
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$mst_id = $check_allocation_array[0][csf('id')];
					$pro_allocate_data = (str_replace("'", "", $txt_yarn_qnty)-str_replace("'", "", $original_prod_qnty));
					$allocation_qnty = ($check_allocation_array[0][csf('qnty')]-str_replace("'", "", $original_prod_qnty)) + str_replace("'", "", $txt_yarn_qnty);	
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty, updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=".$mst_id." and item_id=".$prod_id."", 0);
					
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id=".$mst_id." and item_id=".$prod_id."", 0);
				} 
				else 
				{
					if($job_no!="")
					{
						/*
						|--------------------------------------------------------------------------
						| inv_material_allocation_mst
						| data preparing
						|--------------------------------------------------------------------------
						|
						*/
						$pro_allocate_data = str_replace("'", "", $txt_yarn_qnty);						
						$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_mst = "(" . $id_allocation . ",".$id.",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $is_dyed_yarn . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
	
						/*
						|--------------------------------------------------------------------------
						| inv_material_allocation_dtls
						| data preparing
						|--------------------------------------------------------------------------
						|
						*/
						$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $is_dyed_yarn . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
					}
				}
			}
			
			/*
			|--------------------------------------------------------------------------
			| product checking
			| if original product and update product is not same then
			|--------------------------------------------------------------------------
			|
			*/
			else
			{
				/*
				|--------------------------------------------------------------------------
				| yarn issue checking
				|--------------------------------------------------------------------------
				|
				*/
				$check_issue_against_requisition_lot = sql_select("select sum(cons_quantity) cons_quantity from inv_transaction where receive_basis=3 and transaction_type=2 and item_category=1 and requisition_no=".$txt_requisition_no." and prod_id=".$original_prod_id." and status_active=1 and is_deleted=0");
				if($check_issue_against_requisition_lot[0][csf("cons_quantity")] != "" || $check_issue_against_requisition_lot[0][csf("cons_quantity")] != null)
				{
					echo "17**Issue found.You can not change this lot.";
					disconnect($con);
					exit();
				}

				/*
				|--------------------------------------------------------------------------
				| yarn demand checking
				|--------------------------------------------------------------------------
				|
				*/
				$check_demand_entry = return_field_value("sum(yarn_demand_qnty)yarn_demand_qnty", "ppl_yarn_demand_reqsn_dtls", "requisition_no=".$txt_requisition_no." and prod_id=".$original_prod_id." and status_active=1 and is_deleted=0");
				if($check_demand_entry[0][csf("yarn_demand_qnty")] != "" || $check_demand_entry[0][csf("yarn_demand_qnty")] != null)
				{
					echo "17**Daily yarn demand found.You can not change this lot.";
					disconnect($con);
					exit();
				}
				
				/*
				|--------------------------------------------------------------------------
				| allocation checking
				| for original product
				|--------------------------------------------------------------------------
				|
				*/
				$new_allocate_data = str_replace("'", "", $original_prod_qnty);
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='".$po_id."' and a.item_id=".$original_prod_id." and a.job_no='".$job_no."' and a.booking_no='".$booking_no."' and a.status_active=1 and a.is_deleted=0";
				$check_allocation_array = sql_select($sql_allocation);
				$mst_id = $check_allocation_array[0][csf('id')];				
				if (!empty($check_allocation_array)) 
				{
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty-$new_allocate_data),updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id=".$mst_id." and item_id=".$original_prod_id."", 0);								

					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty-$new_allocate_data),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=".$mst_id." and job_no='".$job_no."' and po_break_down_id='".$po_id."' and item_id=".$original_prod_id."", 0);
				}
				
				/*
				|--------------------------------------------------------------------------
				| allocation checking
				| for new product
				|--------------------------------------------------------------------------
				|
				*/
				$new_prod_id = str_replace("'", "", $prod_id);
				$pro_allocate_data = str_replace("'", "", $txt_yarn_qnty);
				$sql_new_prod_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='".$po_id."' and a.item_id=".$new_prod_id." and a.job_no='".$job_no."' and a.booking_no='".$booking_no."' and status_active=1 and is_deleted=0";
				$check_new_allocation_array = sql_select($sql_new_prod_allocation);
				if (!empty($check_new_allocation_array)) 
				{
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_mst
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$mst_id = $check_new_allocation_array[0][csf('id')];
					$allocation_qnty = $check_new_allocation_array[0][csf('qnty')] + str_replace("'", "", $txt_yarn_qnty);
					$rID5 = execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=".$mst_id." and item_id=".$prod_id."", 0);				
					
					/*
					|--------------------------------------------------------------------------
					| inv_material_allocation_dtls
					| data preparing
					|--------------------------------------------------------------------------
					|
					*/
					$rID6 = execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and item_id=$prod_id", 0);
				}
				else
				{
					if($job_no!="")
					{
						/*
						|--------------------------------------------------------------------------
						| inv_material_allocation_mst
						| data preparing
						|--------------------------------------------------------------------------
						|
						*/
						$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_mst = "(" . $id_allocation . ",".$update_dtls_id.",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $is_dyed_yarn . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$rID5 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);

						/*
						|--------------------------------------------------------------------------
						| inv_material_allocation_dtls
						| data preparing
						|--------------------------------------------------------------------------
						|
						*/
						$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $is_dyed_yarn . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$rID6 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
					}
					else
					{
						$rID5 = false;
						$rID6 = false;
					}
				}
			}
		}
		else
		{
			$rID2 = true;
			$rID3 = true;
			$rID4 = true;
			$rID5 = true;
			$rID6 = true;
			$rID7 = true;
		}
		
		/*
		|--------------------------------------------------------------------------
		| ppl_yarn_requisition_entry
		| data preparing and updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_update = "prod_id*no_of_cone*requisition_date*yarn_qnty*updated_by*update_date";
		$data_array_update = $prod_id . "*" . $txt_no_of_cone . "*" . $txt_reqs_date . "*" . $txt_yarn_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID1 = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		
		/*
		|--------------------------------------------------------------------------
		| variable settings Planning
		| if variable list is auto allocation yarn form requisition and
		| Auto Allocate Yarn From Requisition is yes then
		|--------------------------------------------------------------------------
		|
		*/
		if ($is_auto_allocation_variable_settings == 1) 
		{
			/*
			|--------------------------------------------------------------------------
			| product checking
			| if original product and update product is same then
			|--------------------------------------------------------------------------
			|
			*/
			if(str_replace("'", "", $prod_id) == str_replace("'", "", $original_prod_id))
			{
				/*
				|--------------------------------------------------------------------------
				| if the query result for
				| table inv_material_allocation_mst and
				| table inv_material_allocation_dtls is true then
				| the query for table product_details_master will be execute
				|--------------------------------------------------------------------------
				|
				*/
				if($rID1 && $rID2 && $rID3)
				{
					$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),available_qnty=(current_stock-(allocated_qnty+$pro_allocate_data)),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);
				}
			}
			
			/*
			|--------------------------------------------------------------------------
			| product checking
			| if original product and update product is not same then
			|--------------------------------------------------------------------------
			|
			*/
			else
			{
				/*
				|--------------------------------------------------------------------------
				| for original product
				| if the query result for
				| table ppl_yarn_requisition_entry and
				| table inv_material_allocation_mst and
				| table inv_material_allocation_dtls is true then
				| the query for table product_details_master will be execute
				|--------------------------------------------------------------------------
				|
				*/
				if($rID1 && $rID2 && $rID3)
				{
					$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$new_allocate_data),available_qnty=(current_stock-(allocated_qnty-$new_allocate_data)),update_date='" . $pc_date_time . "' where id=".$original_prod_id."", 0);
				}

				/*
				|--------------------------------------------------------------------------
				| if the query result for
				| table inv_material_allocation_mst and
				| table inv_material_allocation_dtls is true then
				| the query for table product_details_master will be execute
				|--------------------------------------------------------------------------
				|
				*/
				if($rID1 && $rID5 && $rID6)
				{
					$rID7 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data), available_qnty=(current_stock-(allocated_qnty+$pro_allocate_data)), update_date='" . $pc_date_time . "' where id=".$new_prod_id."", 0);
				}
			}
		}

		/*oci_rollback($con);
		echo "10**$rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7";
		disconnect($con);
		die;*/

		if ($db_type == 0)
		{
			if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 )
			{
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 )
			{
				oci_commit($con);
				echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	} 

	/*
	|--------------------------------------------------------------------------
	| for delete
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation == 2)
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			// check if demand found against requisition
			$check_demand_entry = sql_select("select group_concat(b.demand_system_no) as demand_system_no,sum(a.yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls a,ppl_yarn_demand_entry_mst b,ppl_yarn_demand_entry_dtls c where a.dtls_id=c.id and a.mst_id=b.id and a.requisition_no=$txt_requisition_no and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		}
		else 
		{
			// check if demand found against requisition
			$check_demand_entry = sql_select("select listagg(cast(b.demand_system_no as varchar2(4000)), ',') within group (order by b.demand_system_no) as demand_system_no,sum(a.yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls a,ppl_yarn_demand_entry_mst b,ppl_yarn_demand_entry_dtls c where a.dtls_id=c.id and a.mst_id=b.id and a.requisition_no=$txt_requisition_no and a.prod_id=$original_prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		}

		if($check_demand_entry[0][csf("yarn_demand_qnty")] != "" || $check_demand_entry[0][csf("yarn_demand_qnty")] != null)
		{
			echo "18**Daily yarn demand found.Requisition can not be deleted.\nDemand Id=".$check_demand_entry[0][csf("demand_system_no")];
			disconnect($con);
			exit();
		}

		// CHECK IF ISSUE FOUND
		$check_issue = sql_select("select sum(cons_quantity) cons_quantity from inv_transaction where receive_basis=3 and transaction_type=2 and item_category=1 and requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");
		if($check_issue[0][csf("cons_quantity")] > 0 || $check_issue[0][csf("cons_quantity")] != null)
		{
			echo "17**Issue found.You can not change this lot.";
			disconnect($con);
			exit();
		}
		
		//for requisition
		$rID2 = true;
		$rID3 = true;
		$rID4 = true;
		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty);
		if ($is_auto_allocation_variable_settings == 1) 
		{
			if($updateId!="")
			{
				$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=$updateId group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc,c.po_id";
			}
			
			$planning_array = sql_select($sql);
			$po_id = $planning_array[0][csf('po_id')];
			$booking_no = $planning_array[0][csf('booking_no')];
			$job_no = str_replace("'", "", $job_no);
			$prod_id = str_replace("'", "", $prod_id);

			$sql_allocation = "select a.id,a.qnty from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=$prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";
			$check_allocation_array = sql_select($sql_allocation);

			if (!empty($check_allocation_array)) 
			{
				$mst_id = $check_allocation_array[0][csf('id')];
				$current_allocation = $check_allocation_array[0][csf('qnty')];
				
				if($txt_yarn_qnty < $current_allocation)
				{
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=qnty-$txt_yarn_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
				}
				else
				{
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=qnty-$txt_yarn_qnty,status_active=0,is_deleted=1,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
				}

				$rID3 = false;
				if ($rID2) 
				{
					if($txt_yarn_qnty < $current_allocation)
					{
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=qnty-$txt_yarn_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id", 0);
					}
					else
					{
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=qnty-$txt_yarn_qnty,status_active=0,is_deleted=1,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id", 0);
					}
				}
			}
			
			$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$txt_yarn_qnty),available_qnty=(current_stock-(allocated_qnty-$txt_yarn_qnty)),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);

		} 
		
		/*echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4;
		oci_rollback($con);
		die;*/
		
		if ($db_type == 0) 
		{
			if ($rID && $rID2 && $rID3 && $rID4) 
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "7**0**1";
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID && $rID2 && $rID3 && $rID4) 
			{
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} 
			else 
			{
				oci_rollback($con);
				echo "7**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "requisition_print_two")
{
	extract($_REQUEST);
	$data = explode('**', $data);
	if ($data[2])
	{
		echo load_html_head_contents("Program Qnty Info", "../", 1, 1, '', '', '');
	}
	else
	{
		echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	}

	$typeForAttention = $data[1];
	$program_ids = $data[0];
	$within_group = $data[3];
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name'); 
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active=1 and is_deleted=0", "id", "location_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	//for requsition information
	$knit_id_array = array();
	$prod_id_array = array();
	$product_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no,requisition_date,prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in(".$program_ids.") and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no,requisition_date");
	foreach ($reqsn_dataArray as $row)
	{
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsd'] .= $row[csf('requisition_date')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
		
		$product_id_array[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$rqsn_array2[$row[csf('knit_id')]] = $row[csf('requisition_no')];
	}
	
	//for product information
	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0".where_con_using_array($product_id_array, '1', 'id');
	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}
		
		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}
	//echo "<pre>";
	//print_r($product_details_array);
	//echo "</pre>";

	$sales_order_no = '';
	$buyer_name = '';
	$customer_buyer = '';
	$knitting_factory = '';
    $booking_no = '';
	$wg_yes_booking = '';
	$company = '';
	$order_buyer = '';
	$style_ref_no = '';
	$location_name = '';
	$attention = '';
	if ($db_type == 0)
	{
		$dataArray = sql_select("SELECT a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id, b.within_group, b.after_wash_gsm 
		from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b 
		where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.within_group, b.after_wash_gsm");
	}
	else
	{
		$dataArray = sql_select("SELECT a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id, b.within_group, b.after_wash_gsm
		from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b 
		where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.within_group, b.after_wash_gsm");
	}

	$k_source = "";
	$sales_ids = "";
	$sales_id = array();
	foreach ($dataArray as $row)
	{
		if ($row[csf('knitting_source')] == 1)
		{
			//for knitting party
			$knitting_factory_arr[$company_details[$row[csf('knitting_party')]]] = $company_details[$row[csf('knitting_party')]];
		
			//for location
			$location_name = $location_arr[$row[csf('location_id')]];
		}
		else if ($row[csf('knitting_source')] == 3)
		{
			//for knitting party
			$knitting_factory_arr[$supplier_details[$row[csf('knitting_party')]]] = $supplier_details[$row[csf('knitting_party')]];

			//for location
			$ComArray = sql_select("select id, contact_person, address_1 from lib_supplier where id=".$row[csf('knitting_party')]."");
			foreach ($ComArray as $loc)
			{
				$attention = $loc[csf('contact_person')];
				$location_name = $loc[csf('address_1')];
			}
		}
        $knitting_factory=implode(", ", $knitting_factory_arr);

		//for buyer
		if ($row[csf('buyer_id')]*1 != 0)
		{
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		}

		//for cust. buyer
		if ($row[csf('customer_buyer')]*1 != 0)
		{
			$customer_buyer = $buyer_arr[$row[csf('customer_buyer')]];
		}
		
		if ($booking_no != '')
		{
			$booking_no .= "," . $row[csf('booking_no')];
		}
		else
		{
			$booking_no = $row[csf('booking_no')];
		}

		if ($company == "")
		{
			$company = $company_details[$row[csf('company_id')]];
		}
		if ($company_id == "")
		{
			$company_id = $row[csf('company_id')];
		}

		$is_sales = $row[csf('is_sales')];
		$sales_ids .= $row[csf('po_id')] . ",";
		$k_source = $row[csf('knitting_source')];
		$after_wash_gsm_arr[$row[csf('id')]] =  $row[csf('after_wash_gsm')];
	}
	
	$sales_ids = substr($sales_ids, 0, -1);
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));
	//echo "<pre>";
	//print_r($booking_nos);
	//echo "</pre>"; die;
	
	//for sales information
	$sales_array = array();
	$sales_booking_no = array();
	if(!empty($sales_id))
	{
		$po_dataArray = sql_select("select id, job_no, buyer_id, style_ref_no, within_group, sales_booking_no, booking_without_order from fabric_sales_order_mst where status_active=1 and is_deleted=0".where_con_using_array($sales_id, '0', 'id'));
		foreach ($po_dataArray as $row)
		{
			$sales_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
			$sales_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
			$sales_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			$sales_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$sales_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
			$sales_array[$row[csf('id')]]['booking_without_order'] = $row[csf('booking_without_order')];
			$sales_booking_no[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
		}
	}
	//echo "<pre>";
	//print_r($sales_array);
	//echo "</pre>"; die;

	//for booking information
	$booking_array = array();
	if(!empty($booking_nos))
	{
		$book_dataArray = sql_select("select a.buyer_id, b.booking_no, b.po_break_down_id as po_id, b.job_no, c.po_number, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0".where_con_using_array($sales_booking_no, '1', 'b.booking_no'));
		foreach ($book_dataArray as $row)
		{
			$booking_array[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
			$booking_array[$row[csf('booking_no')]]['po_id'] = $row[csf('po_id')];
			$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$booking_array[$row[csf('booking_no')]]['po_no'] = $row[csf('po_number')];
			$booking_array[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
			$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		}
	}
	//echo "<pre>";
	//print_r($booking_array);
	//echo "</pre>"; die;

	$style_ref_no = "";
	$job_no = "";
	foreach ($sales_id as $pid)
	{
		$sales_order_no .= $sales_array[$pid]['no'] . ","; 
        if ($sales_array[$pid]['within_group'] == 2)
		{
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
        }
		else
		{
			if($sales_array[$pid]['booking_without_order'] != 1)
			{			
            	$order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
			}
			else
			{
				//for sample without order
				$booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='".$sales_array[$pid]['sales_booking_no']."'");
            	$order_buyer .= $buyer_arr[$booking_buyer].",";
			}
			
            $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
        }
	}

    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
	$order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
	$style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
	$job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
	$booking_noss = implode(",", $booking_nos);

    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT dtls_id, seq_no, count_id, feeding_id FROM ppl_planning_count_feed_dtls WHERE dtls_id in($program_ids) and status_active=1 and is_deleted=0");
        $feedingDataArr = array();
        foreach ($feedingResult as $row)
		{
            $feedingSequence[$row[csf('seq_no')]] =  $row[csf('seq_no')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['count_id'] = $row[csf('count_id')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['feeding_id'] = $row[csf('feeding_id')];  
        }
    }
	?>
	<style type="text/css">
		td.rotate > div {
			transform:
			/*translate(0px, 25px)*/
			rotate(270deg);
			/*width: auto;*/
			font-weight:bold;
		}
    </style>
    <div style="width:1200px; margin-left:5px;">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr class="">
				<td  align="center" style="font-size:14px"><?
				$nameArray_com=sql_select( "SELECT PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, PROVINCE, CITY, ZIP_CODE, CONTACT_NO, EMAIL, WEBSITE, VAT_NUMBER FROM LIB_COMPANY WHERE ID=$company_id AND STATUS_ACTIVE=1 AND IS_DELETED=0");
				$loc = '';
				foreach ($nameArray_com as $result)
				{
					if($result['PLOT_NO'] != '')
					{
						$loc .= $result['PLOT_NO'];
					}
					
					if($result['LEVEL_NO'] != '')
					{
						if($loc != '')
						{
							$loc .= ', '.$result['PLOT_NO'];
						}
						else
						{
							$loc .= $result['PLOT_NO'];
						}
					}
					
					if($result['ROAD_NO'] != '')
					{
						if($loc != '')
						{
							$loc .= ', '.$result['ROAD_NO'];
						}
						else
						{
							$loc .= $result['ROAD_NO'];
						}
					}
					
					if($result['BLOCK_NO'] != '')
					{
						if($loc != '')
						{
							$loc .= ', '.$result['BLOCK_NO'];
						}
						else
						{
							$loc .= $result['BLOCK_NO'];
						}
					}
					
					if($result['CITY'] != '')
					{
						if($loc != '')
						{
							$loc .= ', '.$result['CITY'];
						}
						else
						{
							$loc .= $result['CITY'];
						}
					}
				}
				echo $loc;
				?></td>
			</tr>
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
            </tr>
        </table>
        <div style="margin-top:10px; width:950px">
            <table width="100%" cellpadding="2" cellspacing="5">
                <tr>
                    <td width="140"><b style="font-size:18px">Knitting Factory </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><b><? echo $knitting_factory; ?></b></td>
                </tr>
                <tr>
                    <td><b>Location</b></td>
                    <td>:</td>
                    <td><b><? echo $location_name; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Attention </b></td>
                    <td>:</td>
                    <?
                    if ($typeForAttention == 1)
                    {
                        echo "<td style=\"font-size:18px; font-weight:bold;\">Knitting Manager</td>";
                    }
                    else
                    {
                        ?>
                        <td style="font-size:18px; font-weight:bold;"><b><? echo $attention; ?></b></td>
                        <?
                    }
                    ?>
             </tr>
                <tr>
                    <td><b>Sales job/Booking No </b></td>
                    <td>:</td>
                    <td><? echo $booking_noss; ?></td>
                </tr>
                <tr>
                    <td><b>Customer Name </b></td>
                    <td>:</td>
                    <td><? echo $order_buyers; ?></td>
                </tr>
                <tr>
                    <td><b>Cust. Buyer Name </b></td>
                    <td>:</td>
                    <td><? echo $customer_buyer; ?></td>
                </tr>
                <tr>
                    <td><b>Sales Order No </b></td>
                    <td>:</td>
                    <td><? echo $sales_nos; ?></td>
                </tr>
            </table>
        </div>
        <table width="1050" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100">Requisition No</th>
                <th width="100">Requisition Date</th>
                <th width="100">Brand</th>
                <th width="100">Lot No</th>
                <th width="200">Yarn Description</th>
                <th width="100">Color</th>
                <th width="100">Requisition Qty.</th>
                <th>No Of Cone</th>
            </thead>
            <?
            $j = 1;
            $tot_reqsn_qty = 0;
            foreach ($rqsn_array as $prod_id => $data)
            {
                if ($j % 2 == 0)
                    $bgcolor = "#E9F3FF";
                else
                    $bgcolor = "#FFFFFF";
                 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>" style=" font-size:12px;">
                    <td width="30"><? echo $j; ?></td>
                    <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
                    <td width="100"><? echo substr($data['reqsd'], 0, -1); ?></td>
                    <td width="100" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</td>
                    <td width="100" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['lot']; ?></td>
                    <td width="200" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['desc']; ?></td>
                    <td width="100" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</td>
                    <td width="100" align="right" style=" word-wrap:break-word;"><? echo number_format($data['qnty'], 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
                </tr>
                <?
                $tot_reqsn_qty += $data['qnty'];
                $tot_no_of_cone += $data['no_of_cone'];
                $j++;
            }
            ?>
            <tfoot>
            	<tr style="font-size:12px;">
                    <th colspan="7" align="right">Total</th>
                    <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
                    <th><? echo number_format($tot_no_of_cone); ?></th>
                </tr>
            </tfoot>
        </table>
        <table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
            <thead align="center">
                <th width="25">SL</th>
                <th width="50">Program No</th>
                <th width="50">Requisition No</th>
                <th width="100">Fabric Composition</th>
                <th width="120">Cons. & Yarn Com Details</th>
                <th width="50">GSM</th>
                <th width="40">F. Dia</th>
                <th width="60">Dia Type</th>
                <th width="45">Floor</th>
                <th width="45">M/c. No</th>
                <th width="50">M/c. Dia & GG</th>
                <th width="100">Color</th>
                <th width="60">Color Range</th>
                <th width="50">Dye Type</th>
                <th width="50">S/L</th>
                <th width="50">Spandex S/L</th>
                <th width="50">Feeder</th>
                <th width="100">Count Feeding</th>
                <th width="70">Knit Start</th>
                <th width="70">Knit End</th>
                <th width="70">Prpgram Qty.</th>
                <th width="110">Yarn Description</th>
                <th width="50">Lot</th>
                <th width="70">Yarn Qty.(KG)</th>
                <th>Remarks</th>
            </thead>
            <?
            $i = 1;
            $s = 1;
            $tot_program_qnty = 0;
            $tot_yarn_reqsn_qnty = 0;
            $company_id = '';
            $sql = "SELECT a.company_id, a.determination_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks, b.advice, b.dye_type 
            from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b 
            where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            // echo $sql;
            $nameArray = sql_select($sql);
            foreach ($nameArray as $key => $rows) 
            {
            	$all_determination_id .= $rows[csf('determination_id')].",";
            }

            $all_determination_id = chop($all_determination_id,",");            
            $fab_desc_arr = return_library_array("SELECT a.id, (b.fabric_composition_name) as fab_desc  from lib_yarn_count_determina_mst a left join lib_fabric_composition b on a.fabric_composition_id=b.id where  a.fab_nature_id=2 and a.status_active=1 and a.is_deleted=0 and a.id in (". $all_determination_id.")","id","fab_desc");
            $advice = "";
            foreach ($nameArray as $row)
            {
                if ($i % 2 == 0)
                 $bgcolor = "#E9F3FF";
                else
                 $bgcolor = "#FFFFFF";
            	$after_wash_gsm=$after_wash_gsm_arr[$row[csf('program_id')]];
                $color = '';
                $color_id = explode(",", $row[csf('color_id')]);
            
                foreach ($color_id as $val)
                {
                    if ($color == '')
                      $color = $color_library[$val];
                    else
                      $color .= "," . $color_library[$val];
                }
            
                if ($company_id == '')
                $company_id = $row[csf('company_id')];
            
                $machine_no = '';
                $machine_id = explode(",", $row[csf('machine_id')]);
            
                foreach ($machine_id as $val)
                {
                    if ($machine_no == '')
                        $machine_no = $machine_arr[$val];
                    else
                        $machine_no .= "," . $machine_arr[$val];
                }
                
                if ($machine_id[0] != "")
                {
                    $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
                }
            
                $count_feeding = "";
                foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
                {
                    if($count_feeding =="")
                    {   
                        $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                    } 
                    else 
                    {
                        $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                    }
                }

                if ($knit_id_array[$row[csf('program_id')]] != "")
                {
                    $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
                    $row_span = count($all_prod_id);
                    $z = 0;
                
                    foreach ($all_prod_id as $prod_id) 
                    {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style=" font-size:13px;" valign="middle">
                    <?
                    if ($z == 0) 
                    {
                        ?>
                        <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" align="center"><b><? echo $rqsn_array2[$row[csf('program_id')]]; ?></b></td>
                        <td width="100" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo chop($fab_desc_arr[$row[csf('determination_id')]],", "); ?></td>
                        <td width="120" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('fabric_desc')]; ?></td>
                        <td width="50" align="center" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><b><? echo 'B/W '.$row[csf('gsm_weight')].'<br>A/W '.$after_wash_gsm; ?></b></th>
                        <td width="50" align="center" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('fabric_dia')]; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $fabric_typee[$row[csf('diatype')]]; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></td>
                        <td width="60" align="center" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $machine_no; ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $color; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $color_range[$row[csf('color_range')]]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;" align="center"><? echo $row[csf('dye_type')]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word; font-size:14px;" align="center" class="rotate"><div><span><? echo $row[csf('stitch_length')]; ?></span></div></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('spandex_stitch_length')]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $feeder[$row[csf('feeder')]]; ?></td>
                        <td width="100" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $count_feeding; ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('start_date')]); ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('end_date')]); ?></td>
                        <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
                        <?
                        $tot_program_qnty += $row[csf('program_qnty')];
                        $i++;
                    }
                    ?>
                        <td width="110" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</td>
                        <td width="50" align="center" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</td>
                        <td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
                        <?
                        if ($z == 0)
						{
                            ?>
                            <td rowspan="<? echo $row_span; ?>"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                            <?
                        }
                        ?>
                    </tr>
                        <?
                        $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
                        $z++;
                    }
                }
                else
                {
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style=" font-size:13px;" valign="middle">
						<td width="25"><? echo $i; ?></td>
						<td width="60" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
						<td width="60" align="center"><b><? echo $rqsn_array2[$row[csf('program_id')]]; ?></b></td>
						<td width="100" style=" word-wrap:break-word;"><? echo chop($fab_desc_arr[$row[csf('determination_id')]],", "); ?></td>
						<td width="120" style=" word-wrap:break-word;"><? echo $row[csf('fabric_desc')]; ?></td>
						<td width="50" align="center" style=" word-wrap:break-word;"><b><? echo 'B/W '.$row[csf('gsm_weight')].'<br>A/W '.$after_wash_gsm;; ?></b></td>
						<td width="50" align="center" style=" word-wrap:break-word;"><? echo $row[csf('fabric_dia')]; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $fabric_typee[$row[csf('diatype')]]; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></td>
						<td width="60" align="center" style=" word-wrap:break-word;"><? echo $machine_no; ?></td>
						<td width="70" style=" word-wrap:break-word;"><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $color; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $color_range[$row[csf('color_range')]]; ?></td>
						<td width="50" style=" word-wrap:break-word;" align="center"><? echo $row[csf('dye_type')]; ?></td>
						<td width="50" style=" word-wrap:break-word; font-size:14px;" align="center" class="rotate"><div><span><? echo $row[csf('stitch_length')]; ?></span></div></td>
						<td width="50" style=" word-wrap:break-word;"><? echo $row[csf('spandex_stitch_length')]; ?></td>
						<td width="50" style=" word-wrap:break-word;"><? echo $feeder[$row[csf('feeder')]]; ?></td>
						<td width="100" style=" word-wrap:break-word;" rowspan="<? echo $row_span; ?>"><? echo $count_feeding; ?></td>
						<td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('start_date')]); ?></td>
						<td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('end_date')]); ?></td>
						<td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
						<td width="110">&nbsp;</td>
						<td width="50">&nbsp;</td>
						<td width="70" align="right">&nbsp;</td>
						<td><? echo $row[csf('remarks')]; ?>&nbsp;</td>
					</tr>
					<?
					$tot_program_qnty += $row[csf('program_qnty')];
					$i++;
                }
                $advice = $row[csf('advice')];
            }
            ?>
            <tfoot>
                <th colspan="20" align="right"><b>Total</b></th>
                <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <br>
        <?
        $sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, grey_size_1, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
        if (count($sql_collarCuff) > 0)
		{
           ?>
           <table style="margin-top:10px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0"
           class="rpt_table">
           <thead>
            <tr>
                <th width="20">SL</th>
                <th width="100">Body Part</th>
                <th width="100">Gmt. Size</th>
                <th width="100">Finish Size</th>
                <th width="100">Grey Size</th>
                <th>Quantity Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?
            $i = 1;
            $total_qty_pcs = 0;
            foreach ($sql_collarCuff as $row)
			{
             if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
             ?>
             <tr>
                <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('grey_size_1')]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
                    $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th align="right">Total</th>
                <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
            </tr>
        </tfoot>
        </table>
        <?
        }
        ?>
        <br>
		<?
        $sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";
        $result_stripe = sql_select($sql_strip);
        if (count($result_stripe) > 0)
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
				<thead>
					<tr>
						<th colspan="7">Stripe Measurement</th>
						</tr>
						<tr>
						<th width="30">SL</th>
						<th width="60">Prog. no</th>
						<th width="140">Color</th>
						<th width="130">Stripe Color</th>
						<th width="70">Measurement</th>
						<th width="50">UOM</th>
						<th>No Of Feeder</th>
					</tr>
				</thead>
				<?
				$i = 1;
				$tot_feeder = 0;
				foreach ($result_stripe as $row)
				{
					if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
					else
					$bgcolor = "#FFFFFF";
					$tot_feeder += $row[csf('no_of_feeder')];
					?>
					<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td>
					<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
					<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
					<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>
					<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
					</tr>
					<?
					$tot_masurement += $row[csf('measurement')];
					$i++;
				}
				?>
				<tfoot>
					<th colspan="4">Total</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
				</tfoot>
			</table>
			<?
        }
        ?>
        <table border="1" rules="all" class="rpt_table">
            <tr>
                <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
                <td style="font-size:20px; width:100%;"><? echo $advice; ?></td>
            </tr>
        </table>
        <div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div>
        <br/>
        <div style="float:left; border:1px solid #000;">
            <table border="1" rules="all" class="rpt_table" width="400" height="200">
                <thead>
                    <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
                <thead>
                <tbody>
                    <tr>
                        <td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
                        <td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 2.</b> Loop</td>
                        <td style="font-size:14px;"><b> 9.</b> Needle mark</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 3.</b> Hole</td>
                        <td style="font-size:14px;"><b> 10.</b> Oil mark</td>
                    </tr>
                    <tr>
                        <td><b> 4.</b> Star marks</td>
                        <td><b> 11.</b> Dia mark/Crease Mark</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 5.</b> Barre</td>
                        <td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
                        <td style="font-size:14px;"><b> 13.</b> Slub</td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
                        <td style="font-size:14px;"><b> 14.</b> Other contamination</td>
                    </tr>
                </tbody>
            </table>
		</div>
        <div style="float:right; border:1px solid #000;">
            <table border="1" rules="all" class="rpt_table" width="400" height="150">
                <thead>
                    <th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as
                        Follows
                    </th>
                    <thead>
                        <tr>
                            <td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
                            <td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 2.</b> Prog. Company Name</td>
                            <td style="font-size:14px;"><b> 7.</b> Finished Dia</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
                            <td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
                            <td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
                            <td style="font-size:14px;"><b> 10.</b> Knit Program No</td>

                        </table>
                    </div>
        <?
		echo signature_table(213, $company_id, "1180px");
		?>
    </div>
    <?
    exit();
}

if ($action == "requisition_print_two_21112021")
{
	extract($_REQUEST);
	$data = explode('**', $data);
	if ($data[2])
	{
		echo load_html_head_contents("Program Qnty Info", "../", 1, 1, '', '', '');
	}
	else
	{
		echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	}

	$typeForAttention = $data[1];
	$program_ids = $data[0];
	$within_group = $data[3];
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name'); 
    $yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by id, yarn_count","id","yarn_count");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active=1 and is_deleted=0", "id", "location_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	//for requsition information
	$knit_id_array = array();
	$prod_id_array = array();
	$product_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no,requisition_date,prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in(".$program_ids.") and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no,requisition_date");
	foreach ($reqsn_dataArray as $row)
	{
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsd'] .= $row[csf('requisition_date')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
		
		$product_id_array[$row[csf('prod_id')]] = $row[csf('prod_id')];
	}
	
	//for product information
	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0".where_con_using_array($product_id_array, '1', 'id');
	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}
		
		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}
	//echo "<pre>";
	//print_r($product_details_array);
	//echo "</pre>";

	$sales_order_no = '';
	$buyer_name = '';
	$customer_buyer = '';
	$knitting_factory = '';
    $booking_no = '';
	$wg_yes_booking = '';
	$company = '';
	$order_buyer = '';
	$style_ref_no = '';
	$location_name = '';
	$attention = '';
	if ($db_type == 0)
	{
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id, b.within_group from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.within_group");
	}
	else
	{
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id, b.within_group from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in (".$program_ids.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, a.location_id, a.is_sales, b.buyer_id, b.customer_buyer, b.booking_no, b.company_id, b.within_group");
	}

	$k_source = "";
	$sales_ids = "";
	$sales_id = array();
	foreach ($dataArray as $row)
	{
		if ($row[csf('knitting_source')] == 1)
		{
			//for knitting party
			$knitting_factory_arr[$company_details[$row[csf('knitting_party')]]] = $company_details[$row[csf('knitting_party')]];
		
			//for location
			$location_name = $location_arr[$row[csf('location_id')]];
		}
		else if ($row[csf('knitting_source')] == 3)
		{
			//for knitting party
			$knitting_factory_arr[$supplier_details[$row[csf('knitting_party')]]] = $supplier_details[$row[csf('knitting_party')]];

			//for location
			$ComArray = sql_select("select id, contact_person, address_1 from lib_supplier where id=".$row[csf('knitting_party')]."");
			foreach ($ComArray as $loc)
			{
				$attention = $loc[csf('contact_person')];
				$location_name = $loc[csf('address_1')];
			}
		}
        $knitting_factory=implode(", ", $knitting_factory_arr);

		//for buyer
		if ($row[csf('buyer_id')]*1 != 0)
		{
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		}

		//for cust. buyer
		if ($row[csf('customer_buyer')]*1 != 0)
		{
			$customer_buyer = $buyer_arr[$row[csf('customer_buyer')]];
		}
		
		if ($booking_no != '')
		{
			$booking_no .= "," . $row[csf('booking_no')];
		}
		else
		{
			$booking_no = $row[csf('booking_no')];
		}

		if ($company == "")
		{
			$company = $company_details[$row[csf('company_id')]];
		}
		if ($company_id == "")
		{
			$company_id = $row[csf('company_id')];
		}

		$is_sales = $row[csf('is_sales')];
		$sales_ids .= $row[csf('po_id')] . ",";
		$k_source = $row[csf('knitting_source')];
	}
	
	$sales_ids = substr($sales_ids, 0, -1);
    $sales_id = array_unique(explode(",", $sales_ids));
    $booking_nos = array_unique(explode(",", $booking_no));
	//echo "<pre>";
	//print_r($booking_nos);
	//echo "</pre>"; die;
	
	//for sales information
	$sales_array = array();
	$sales_booking_no = array();
	if(!empty($sales_id))
	{
		$po_dataArray = sql_select("select id, job_no, buyer_id, style_ref_no, within_group, sales_booking_no, booking_without_order from fabric_sales_order_mst where status_active=1 and is_deleted=0".where_con_using_array($sales_id, '0', 'id'));
		foreach ($po_dataArray as $row)
		{
			$sales_array[$row[csf('id')]]['no'] = $row[csf('job_no')];
			$sales_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
			$sales_array[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			$sales_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$sales_array[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
			$sales_array[$row[csf('id')]]['booking_without_order'] = $row[csf('booking_without_order')];
			$sales_booking_no[$row[csf('sales_booking_no')]] = $row[csf('sales_booking_no')];
		}
	}
	//echo "<pre>";
	//print_r($sales_array);
	//echo "</pre>"; die;

	//for booking information
	$booking_array = array();
	if(!empty($booking_nos))
	{
		$book_dataArray = sql_select("select a.buyer_id, b.booking_no, b.po_break_down_id as po_id, b.job_no, c.po_number, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0".where_con_using_array($sales_booking_no, '1', 'b.booking_no'));
		foreach ($book_dataArray as $row)
		{
			$booking_array[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
			$booking_array[$row[csf('booking_no')]]['po_id'] = $row[csf('po_id')];
			$booking_array[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$booking_array[$row[csf('booking_no')]]['po_no'] = $row[csf('po_number')];
			$booking_array[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
			$booking_array[$row[csf('booking_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
		}
	}
	//echo "<pre>";
	//print_r($booking_array);
	//echo "</pre>"; die;

	$style_ref_no = "";
	$job_no = "";
	foreach ($sales_id as $pid)
	{
		$sales_order_no .= $sales_array[$pid]['no'] . ","; 
        if ($sales_array[$pid]['within_group'] == 2)
		{
            $order_buyer .= $buyer_arr[$sales_array[$pid]['buyer_id']] . ",";
            $style_ref_no .= "," . $sales_array[$pid]['style_ref_no'];
            $job_no .= "";
            $order_ids .= "";
        }
		else
		{
			if($sales_array[$pid]['booking_without_order'] != 1)
			{			
            	$order_buyer .= $buyer_arr[$booking_array[$sales_array[$pid]['sales_booking_no']]['buyer_id']] . ",";
			}
			else
			{
				//for sample without order
				$booking_buyer = return_field_value("buyer_id", "wo_non_ord_samp_booking_mst", "booking_no='".$sales_array[$pid]['sales_booking_no']."'");
            	$order_buyer .= $buyer_arr[$booking_buyer].",";
			}
			
            $style_ref_no .= "," . $booking_array[$sales_array[$pid]['sales_booking_no']]['style_ref_no'];
            $job_no .= $booking_array[$sales_array[$pid]['sales_booking_no']]['job_no'] . ",";
            $order_ids .= $booking_array[$sales_array[$pid]['sales_booking_no']]['po_no'] . ",";
        }
	}

    $sales_nos = rtrim(implode(",", array_unique(explode(",", $sales_order_no))), ",");
	$order_buyers = rtrim(implode(",", array_unique(explode(",", $order_buyer))), ",");
	$style_ref_nos = ltrim(implode(",", array_unique(explode(",", $style_ref_no))), ",");
	$job_nos = implode(",", array_unique(explode(",", rtrim($job_no,","))));
	$booking_noss = implode(",", $booking_nos);

    if($program_ids!="")
    {
        $feedingResult =  sql_select("SELECT dtls_id, seq_no, count_id, feeding_id FROM ppl_planning_count_feed_dtls WHERE dtls_id in($program_ids) and status_active=1 and is_deleted=0");
        $feedingDataArr = array();
        foreach ($feedingResult as $row)
		{
            $feedingSequence[$row[csf('seq_no')]] =  $row[csf('seq_no')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['count_id'] = $row[csf('count_id')];
            $feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['feeding_id'] = $row[csf('feeding_id')];  
        }
    }
	?>
    <div style="width:1200px; margin-left:5px;">
        <table width="100%" style="margin-top:10px">
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
            </tr>
            <tr>
                <td align="center" style="font-size:14px">
                <?
                //echo show_company($company_id, '', '');
				echo $location_name;
                ?>
                </td>
            </tr>
            <tr>
                <td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
            </tr>
        </table>
        <div style="margin-top:10px; width:950px">
            <table width="100%" cellpadding="2" cellspacing="5">
                <tr>
                    <td width="140"><b style="font-size:18px">Knitting Factory </b></td>
                    <td>:</td>
                    <td style="font-size:16px"><b><? echo $knitting_factory; ?></b></td>
                </tr>
                <tr>
                    <td><b>Location</b></td>
                    <td>:</td>
                    <td><b><? echo $location_name; ?></b></td>
                </tr>
                <tr>
                    <td style="font-size:16px"><b>Attention </b></td>
                    <td>:</td>
                    <?
                    if ($typeForAttention == 1)
                    {
                        echo "<td style=\"font-size:18px; font-weight:bold;\">Knitting Manager</td>";
                    }
                    else
                    {
                        ?>
                        <td style="font-size:18px; font-weight:bold;"><b><? echo $attention; ?></b></td>
                        <?
                    }
                    ?>
             </tr>
                <tr>
                    <td><b>Sales job/Booking No </b></td>
                    <td>:</td>
                    <td><? echo $booking_noss; ?></td>
                </tr>
                <tr>
                    <td><b>Customer Name </b></td>
                    <td>:</td>
                    <td><? echo $order_buyers; ?></td>
                </tr>
                <tr>
                    <td><b>Cust. Buyer Name </b></td>
                    <td>:</td>
                    <td><? echo $customer_buyer; ?></td>
                </tr>
                <tr>
                    <td><b>Sales Order No </b></td>
                    <td>:</td>
                    <td><? echo $sales_nos; ?></td>
                </tr>
            </table>
        </div>
        <table width="1050" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100">Requisition No</th>
                <th width="100">Requisition Date</th>
                <th width="100">Brand</th>
                <th width="100">Lot No</th>
                <th width="200">Yarn Description</th>
                <th width="100">Color</th>
                <th width="100">Requisition Qty.</th>
                <th>No Of Cone</th>
            </thead>
            <?
            $j = 1;
            $tot_reqsn_qty = 0;
            foreach ($rqsn_array as $prod_id => $data)
            {
                if ($j % 2 == 0)
                    $bgcolor = "#E9F3FF";
                else
                    $bgcolor = "#FFFFFF";
                 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>" style=" font-size:12px;">
                    <td width="30"><? echo $j; ?></td>
                    <td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
                    <td width="100"><? echo substr($data['reqsd'], 0, -1); ?></td>
                    <td width="100" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</td>
                    <td width="100" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['lot']; ?></td>
                    <td width="200" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['desc']; ?></td>
                    <td width="100" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</td>
                    <td width="100" align="right" style=" word-wrap:break-word;"><? echo number_format($data['qnty'], 2, '.', ''); ?></td>
                    <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
                </tr>
                <?
                $tot_reqsn_qty += $data['qnty'];
                $tot_no_of_cone += $data['no_of_cone'];
                $j++;
            }
            ?>
            <tfoot>
            	<tr style="font-size:12px;">
                    <th colspan="7" align="right">Total</th>
                    <th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
                    <th><? echo number_format($tot_no_of_cone); ?></th>
                </tr>
            </tfoot>
        </table>
        <table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
            <thead align="center">
                <th width="25">SL</th>
                <th width="50">Program No</th>
                <th width="120">Fabrication</th>
                <th width="50">GSM</th>
                <th width="40">F. Dia</th>
                <th width="60">Dia Type</th>
                <th width="45">Floor</th>
                <th width="45">M/c. No</th>
                <th width="50">M/c. Dia & GG</th>
                <th width="100">Color</th>
                <th width="60">Color Range</th>
                <th width="50">Dye Type</th>
                <th width="50">S/L</th>
                <th width="50">Spandex S/L</th>
                <th width="50">Feeder</th>
                <th width="100">Count Feeding</th>
                <th width="70">Knit Start</th>
                <th width="70">Knit End</th>
                <th width="70">Prpgram Qty.</th>
                <th width="110">Yarn Description</th>
                <th width="50">Lot</th>
                <th width="70">Yarn Qty.(KG)</th>
                <th>Remarks</th>
            </thead>
            <?
            $i = 1;
            $s = 1;
            $tot_program_qnty = 0;
            $tot_yarn_reqsn_qnty = 0;
            $company_id = '';
            $sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks, b.advice, b.dye_type from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            $nameArray = sql_select($sql);
            $advice = "";
            foreach ($nameArray as $row)
            {
                if ($i % 2 == 0)
                 $bgcolor = "#E9F3FF";
                else
                 $bgcolor = "#FFFFFF";
            
                $color = '';
                $color_id = explode(",", $row[csf('color_id')]);
            
                foreach ($color_id as $val)
                {
                    if ($color == '')
                      $color = $color_library[$val];
                    else
                      $color .= "," . $color_library[$val];
                }
            
                if ($company_id == '')
                $company_id = $row[csf('company_id')];
            
                $machine_no = '';
                $machine_id = explode(",", $row[csf('machine_id')]);
            
                foreach ($machine_id as $val)
                {
                    if ($machine_no == '')
                        $machine_no = $machine_arr[$val];
                    else
                        $machine_no .= "," . $machine_arr[$val];
                }
                
                if ($machine_id[0] != "")
                {
                    $sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
                }
            
                $count_feeding = "";
                foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
                {
                    if($count_feeding =="")
                    {   
                        $count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                    } 
                    else 
                    {
                        $count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
                    }
                }

                if ($knit_id_array[$row[csf('program_id')]] != "")
                {
                    $all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
                    $row_span = count($all_prod_id);
                    $z = 0;
                
                    foreach ($all_prod_id as $prod_id) 
                    {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style=" font-size:12px;">
                    <?
                    if ($z == 0) 
                    {
                        ?>
                        <td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
                        <td width="120" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('fabric_desc')]; ?></td>
                        <td width="50" align="center" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('gsm_weight')]; ?></th>
                        <td width="50" align="center" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('fabric_dia')]; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $fabric_typee[$row[csf('diatype')]]; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></td>
                        <td width="60" align="center" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $machine_no; ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $color; ?></td>
                        <td width="60" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $color_range[$row[csf('color_range')]]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;" align="center"><? echo $row[csf('dye_type')]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $row[csf('spandex_stitch_length')]; ?></td>
                        <td width="50" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $feeder[$row[csf('feeder')]]; ?></td>
                        <td width="100" rowspan="<? echo $row_span; ?>" style=" word-wrap:break-word;"><? echo $count_feeding; ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('start_date')]); ?></td>
                        <td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('end_date')]); ?></td>
                        <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
                        <?
                        $tot_program_qnty += $row[csf('program_qnty')];
                        $i++;
                    }
                    ?>
                        <td width="110" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</td>
                        <td width="50" align="center" style=" word-wrap:break-word;"><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</td>
                        <td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
                        <?
                        if ($z == 0)
						{
                            ?>
                            <td rowspan="<? echo $row_span; ?>"><? echo $row[csf('remarks')]; ?>&nbsp;</td>
                            <?
                        }
                        ?>
                    </tr>
                        <?
                        $tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
                        $z++;
                    }
                }
                else
                {
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style=" font-size:12px;">
						<td width="25"><? echo $i; ?></td>
						<td width="60" align="center" style="font-size:16px;"><b><? echo $row[csf('program_id')]; ?></b></td>
						<td width="120" style=" word-wrap:break-word;"><? echo $row[csf('fabric_desc')]; ?></td>
						<td width="50" align="center" style=" word-wrap:break-word;"><? echo $row[csf('gsm_weight')]; ?></td>
						<td width="50" align="center" style=" word-wrap:break-word;"><? echo $row[csf('fabric_dia')]; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $fabric_typee[$row[csf('diatype')]]; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></td>
						<td width="60" align="center" style=" word-wrap:break-word;"><? echo $machine_no; ?></td>
						<td width="70" style=" word-wrap:break-word;"><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $color; ?></td>
						<td width="60" style=" word-wrap:break-word;"><? echo $color_range[$row[csf('color_range')]]; ?></td>
						<td width="50" style=" word-wrap:break-word;" align="center"><? echo $row[csf('dye_type')]; ?></td>
						<td width="50" style=" word-wrap:break-word;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
						<td width="50" style=" word-wrap:break-word;"><? echo $row[csf('spandex_stitch_length')]; ?></td>
						<td width="50" style=" word-wrap:break-word;"><? echo $feeder[$row[csf('feeder')]]; ?></td>
						<td width="100" style=" word-wrap:break-word;" rowspan="<? echo $row_span; ?>"><? echo $count_feeding; ?></td>
						<td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('start_date')]); ?></td>
						<td width="70" rowspan="<? echo $row_span; ?>" align="center"><? echo change_date_format($row[csf('end_date')]); ?></td>
						<td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?></td>
						<td width="110">&nbsp;</td>
						<td width="50">&nbsp;</td>
						<td width="70" align="right">&nbsp;</td>
						<td><? echo $row[csf('remarks')]; ?>&nbsp;</td>
					</tr>
					<?
					$tot_program_qnty += $row[csf('program_qnty')];
					$i++;
                }
                $advice = $row[csf('advice')];
            }
            ?>
            <tfoot>
                <th colspan="18" align="right"><b>Total</b></th>
                <th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <br>
        <?
        $sql_collarCuff = sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
        if (count($sql_collarCuff) > 0)
		{
           ?>
           <table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0"
           class="rpt_table">
           <thead>
            <tr>
                <th width="50">SL</th>
                <th width="200">Body Part</th>
                <th width="200">Grey Size</th>
                <th width="200">Finish Size</th>
                <th>Quantity Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?
            $i = 1;
            $total_qty_pcs = 0;
            foreach ($sql_collarCuff as $row)
			{
             if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
             ?>
             <tr>
                <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
                <td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf('qty_pcs')], 0);
                    $total_qty_pcs += $row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th align="right">Total</th>
                <th align="right"><? echo number_format($total_qty_pcs, 0); ?>&nbsp;</th>
            </tr>
        </tfoot>
        </table>
        <?
        }
        ?>
        <br>
		<?
        $sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";
        $result_stripe = sql_select($sql_strip);
        if (count($result_stripe) > 0)
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
				<thead>
					<tr>
						<th colspan="7">Stripe Measurement</th>
						</tr>
						<tr>
						<th width="30">SL</th>
						<th width="60">Prog. no</th>
						<th width="140">Color</th>
						<th width="130">Stripe Color</th>
						<th width="70">Measurement</th>
						<th width="50">UOM</th>
						<th>No Of Feeder</th>
					</tr>
				</thead>
				<?
				$i = 1;
				$tot_feeder = 0;
				foreach ($result_stripe as $row)
				{
					if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
					else
					$bgcolor = "#FFFFFF";
					$tot_feeder += $row[csf('no_of_feeder')];
					?>
					<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td>
					<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
					<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
					<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>
					<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
					</tr>
					<?
					$tot_masurement += $row[csf('measurement')];
					$i++;
				}
				?>
				<tfoot>
					<th colspan="4">Total</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th>
				</tfoot>
			</table>
			<?
        }
        ?>
        <table border="1" rules="all" class="rpt_table">
            <tr>
                <td style="font-size:24px; font-weight:bold; width:20px;">ADVICE:</td>
                <td style="font-size:20px; width:100%;"><? echo $advice; ?></td>
            </tr>
        </table>
        <div style="margin-top:60px; text-align: left;"><strong>Rate/Kg =</strong></div>
        <br/>
        <div style="float:left; border:1px solid #000;">
            <table border="1" rules="all" class="rpt_table" width="400" height="200">
                <thead>
                    <th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….
                    </th>
                    <thead>
                        <tbody>
                            <tr>
                                <td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
                                <td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
                            </tr>
                            <tr>
                                <td style="font-size:14px;"><b> 2.</b> Loop</td>
                                <td style="font-size:14px;"><b> 9.</b> Needle mark</td>
                            </tr>
                            <tr>
                                <td style="font-size:14px;"><b> 3.</b> Hole</td>
                                <td style="font-size:14px;"><b> 10.</b> Oil mark</td>
                            </tr>
                            <tr>
                                <td><b> 4.</b> Star marks</td>
                                <td><b> 11.</b> Dia mark/Crease Mark</td>
                            </tr>
                            <tr>
                                <td style="font-size:14px;"><b> 5.</b> Barre</td>
                                <td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
                            </tr>
                            <tr>
                                <td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
                                <td style="font-size:14px;"><b> 13.</b> Slub</td>
                            </tr>
                            <tr>
                                <td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
                                <td style="font-size:14px;"><b> 14.</b> Other contamination</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        <div style="float:right; border:1px solid #000;">
            <table border="1" rules="all" class="rpt_table" width="400" height="150">
                <thead>
                    <th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as
                        Follows
                    </th>
                    <thead>
                        <tr>
                            <td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
                            <td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 2.</b> Prog. Company Name</td>
                            <td style="font-size:14px;"><b> 7.</b> Finished Dia</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
                            <td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
                            <td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
                        </tr>
                        <tr>
                            <td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
                            <td style="font-size:14px;"><b> 10.</b> Knit Program No</td>

                        </table>
                    </div>
        <?
		echo signature_table(213, $company_id, "1180px");
		?>
    </div>
    <?
    exit();
}

function get_allocation_balance_zs($arr)
{
	$data_arr = array();
	$prod_id = $arr['product_id'];
	$variable_set_allocation = $arr['is_auto_allocation'];
	$job_no = $arr['job_no'];
	$selected_booking_no = $arr['booking_no'];
	$poId = $arr['po_id'];

	if($variable_set_allocation == 1)
	{
		$prodStockAvailavle = sql_select("SELECT current_stock AS CURRENT_STOCK,(current_stock-allocated_qnty) AS AVAILABLE_QNTY FROM product_details_master WHERE status_active=1 AND is_deleted=0 AND id=".$prod_id."");
		foreach($prodStockAvailavle as $row)
		{
			$data_arr['available_qnty'] = $row['AVAILABLE_QNTY'];
			$data_arr['current_stock'] = $row['CURRENT_STOCK'];
		}
	}
	else
	{
		/*
		|--------------------------------------------------------------------------
		| for allocation information
		|--------------------------------------------------------------------------
		|
		*/
		$sql_allo = "SELECT b.booking_no AS BOOKING_NO, b.job_no AS JOB_NO, b.item_id AS PROD_ID, b.po_break_down_id AS PO_BREAK_DOWN_ID, c.yarn_count_id AS YARN_COUNT_ID, c.yarn_comp_type1st AS YARN_COMP_TYPE1ST, c.yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, c.yarn_type AS YARN_TYPE, c.dyed_type AS DYED_TYPE, b.qnty AS ALLOCATED_QNTY FROM inv_material_allocation_mst a, inv_material_allocation_dtls b, product_details_master c WHERE a.id=b.mst_id AND b.item_id=c.id AND b.booking_no = '".$selected_booking_no."' AND b.job_no = '".$job_no."' AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND b.item_category=1 AND c.status_active=1 AND c.is_deleted=0";
		//return $sql_allo;
		$data_array = sql_select($sql_allo);
		$product_id_arr = array();
		$allocationQty = 0;
		foreach ($data_array as $row_allo)
		{
			$booking_no = $row_allo['BOOKING_NO'];
			$job_no = $row_allo['JOB_NO'];
			$yarn_count_id = $row_allo['YARN_COUNT_ID'];
			$yarn_comp_type1st = $row_allo['YARN_COMP_TYPE1ST'];
			$yarn_comp_percent1st = $row_allo['YARN_COMP_PERCENT1ST'];
			$yarn_type_id = $row_allo['YARN_TYPE'];
			$product_type_arr[$row_allo['PROD_ID']] = $row_allo['DYED_TYPE'];
	
			$product_id_arr[$row_allo['PROD_ID']] = $row_allo['PROD_ID'];
			$job_total_allocation_arr[$job_no][$row_allo['PROD_ID']] += $row_allo['ALLOCATED_QNTY'];

			if($row_allo['BOOKING_NO']!="")
			{
				$booking_alocation_arr[$row_allo['BOOKING_NO']][$row_allo['PROD_ID']] += $row_allo['ALLOCATED_QNTY'];
			}
			
			/*if($row_allo['DYED_TYPE']!=1)
			{
				$product_id_arr[$row_allo['PROD_ID']] = $row_allo['PROD_ID'];
	
				if($row_allo['DYED_TYPE']!=1)
				{
					$job_total_allocation_arr[$job_no][$row_allo['PROD_ID']] += $row_allo['ALLOCATED_QNTY'];
	
					if($row_allo['BOOKING_NO']!="")
					{
						$booking_alocation_arr[$row_allo['BOOKING_NO']][$row_allo['PROD_ID']] += $row_allo['ALLOCATED_QNTY'];
					}
				}
			}*/
			
			/*$expPoId = explode(',',$poId);
			for($zs = 0; $zs <= count($expPoId); $zs++)
			{
				if($row_allo['PO_BREAK_DOWN_ID'] == $expPoId[$zs])
				{
					$allocationQty += $row_allo['ALLOCATED_QNTY'];
				}
			}*/
		}
		unset($data_array);
		/*echo "<pre>";
		print_r($booking_alocation_arr);
		echo "</pre>";
		die();*/
		
		/*
		|--------------------------------------------------------------------------
		| for yarn dyeing and service information
		|--------------------------------------------------------------------------
		|
		*/
		$prod_id_cond = '';
		if(!empty($product_id_arr))
		{
			$prod_id_cond = "AND b.product_id IN(".implode(",", $product_id_arr).")";
		}
		
		if($yarn_count_id != '')
		{
			$count_id_cond = "AND b.count = ".$yarn_count_id."";
		}
		
		if($yarn_comp_type1st != '')
		{
			$yarn_comp_type1st_cond = "AND b.yarn_comp_type1st = ".$yarn_comp_type1st."";
		}
		
		if($yarn_comp_percent1st!="")
		{
			$yarn_comp_percent1st_cond = "AND b.yarn_comp_percent1st = ".$yarn_comp_percent1st."";
		}
	
		$ydsw_sql="SELECT x.job_no AS JOB_NO, x.product_id AS PRODUCT_ID, sum(x.yarn_wo_qty) AS YARN_WO_QTY, x.yarn_dyeing_prefix_num AS YARN_DYEING_PREFIX_NUM FROM(SELECT b.job_no, b.product_id, SUM(b.yarn_wo_qty) yarn_wo_qty, a.yarn_dyeing_prefix_num FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE a.id=b.mst_id AND b.status_active=1 AND b.is_deleted=0 AND a.entry_form IN(41,42,94,114,135) AND b.entry_form IN(41,42,94,114,135) AND b.job_no='".$job_no."' ".$prod_id_cond." GROUP BY b.job_no, b.product_id, a.yarn_dyeing_prefix_num
		UNION ALL
		SELECT b.job_no, b.product_id, SUM(b.yarn_wo_qty) yarn_wo_qty, a.yarn_dyeing_prefix_num from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b WHERE a.id=b.mst_id AND b.status_active=1 AND b.is_deleted=0 AND a.entry_form IN(125,340) AND b.entry_form IN(125,340) AND b.job_no='".$job_no."' ".$count_id_cond." ".$yarn_comp_type1st_cond." ".$yarn_comp_percent1st_cond." GROUP BY b.job_no, b.product_id, a.yarn_dyeing_prefix_num )x GROUP BY x.job_no, x.product_id, x.yarn_dyeing_prefix_num";
		//echo $ydsw_sql;
		$check_ydsw = sql_select($ydsw_sql);
		$prod_wise_ydsw=array();
		foreach ($check_ydsw as $row)
		{
			$prod_wise_ydsw[$row['JOB_NO']][$row['PRODUCT_ID']]['qty'] += $row['YARN_WO_QTY'];
			$prod_wise_ydsw[$row['JOB_NO']][$row['PRODUCT_ID']]['yarn_dyeing_prefix_num'][$row['YARN_DYEING_PREFIX_NUM']] = $row['YARN_DYEING_PREFIX_NUM'];
		}
		unset($check_ydsw);
		/*echo "<pre>";
		print_r($prod_wise_ydsw);
		echo "</pre>";
		*/
		
		/*
		|--------------------------------------------------------------------------
		| for requisition information
		|--------------------------------------------------------------------------
		|
		*/
		if (!empty($product_id_arr) != "")
		{
			$req_sql = "SELECT a.booking_no AS BOOKING_NO, c.knit_id AS KNIT_ID, c.prod_id AS PROD_ID, c.requisition_no AS REQUISITION_NO, c.yarn_qnty AS YARN_QNTY FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c WHERE a.id=b.mst_id AND b.id=c.knit_id AND c.prod_id in(".implode(",", $product_id_arr).") AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND b.id in (SELECT b.id AS ID FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no in(SELECT a.sales_booking_no AS BOOKING_NO FROM fabric_sales_order_mst a WHERE a.status_active = 1 AND a.is_deleted = 0 AND a.job_no = '".$job_no."') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0)";
			//return $req_sql;
			$req_result = sql_select($req_sql);
			foreach($req_result as $row)
			{
				$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['qty'] += $row['YARN_QNTY'];
				$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['requisition_no'][$row['REQUISITION_NO']] += $row['REQUISITION_NO'];
				
				/*$product_type = $product_type_arr[$row['PROD_ID']];
				if($product_type != 1)
				{
					$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['qty'] += $row['YARN_QNTY'];
					$booking_requsition_arr[$row['BOOKING_NO']][$row['PROD_ID']]['requisition_no'][$row['REQUISITION_NO']] += $row['REQUISITION_NO'];
				}*/
			}
			unset($req_result);
			//echo "<pre>";
			//print_r($booking_requsition_arr);
			//echo "</pre>";
		}

		$data_arr['job_allocation'] = $job_total_allocation_arr;
		$data_arr['booking_allocation'] = $booking_alocation_arr;
		$data_arr['booking_requisition'] = $booking_requsition_arr;
		$data_arr['yarn_dyeing_service'] = $prod_wise_ydsw;
	}
	return $data_arr;
}
?>