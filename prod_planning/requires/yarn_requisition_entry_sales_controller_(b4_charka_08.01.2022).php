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
if ($_SESSION['logic_erp']["buyer_id"] != "") {
	$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
} else {
	$buyer_id_cond = "";
}
// } else {
// 	$buyer_id_cond="";
// }

if ($action == "load_drop_down_buyer") {
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
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
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
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
					<th>PO Buyer</th>
					<th id="search_by_td_up" width="170">Booking No</th>
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
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$buyerID $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
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
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$company_name = $cbo_company_name;
	$barcode = str_replace("'", "", trim($txt_barcode));
	$txt_booking_no = str_replace("'","",$txt_booking_no);

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
		
	if ($txt_booking_no!='') $booking_no="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no="";	
	
	// if (str_replace("'", "", $txt_booking_no) != "")
	// {
	// 	$booking_no = " and a.booking_no = $txt_booking_no";
	// }
	// else
	// {
	// 	$booking_no = "";
	// }

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
	$sql = "select a.company_id, a.within_group, a.booking_no, a.buyer_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,c.po_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_name and b.knitting_source=$type and machine_dia like '$machine_dia' and (a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 or c.is_issued=1) and b.is_sales=1 $job_no_cond $booking_no $buyer_id_cond $within_group_cond $barcodeSalse_detlsCond group by a.company_id, a.buyer_id, a.within_group, a.booking_no, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.status, b.start_date, b.end_date,c.po_id order by b.machine_dia,b.machine_gg";
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
		select buyer_id,booking_no from wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0 $booking_cond
		");
		$booking_buyer_array = array();
		foreach ($sql_data as $row)
		{
			$booking_buyer_array[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		}
	}
	else
		$fld_cap = "Sale";
	 ?>
	 <fieldset style="width:1700px; margin-top:10px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1700" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="70">Program No</th>
				<th width="80">Program Date</th>
				<th width="70">Customers</th>
				<th width="110">Sales Order No</th>
				<th width="110"><? echo $fld_cap; ?> No</th>
				<th width="70">Customers Buyer</th>
				<th width="110">Style</th>
				<th width="80">Dia / GG</th>
				<th width="145">Fabric Description</th>
				<th width="70">Fabric Gsm</th>
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
		<div style="width:1700px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table"
			id="tbl_list_search">
			<tbody>
			<?
            foreach ($nameArray as $row) {
                $machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];
                $yarn_req_qnty = $reqs_array[$row[csf('id')]]['qnty'];
                $reqs_no = $reqs_array[$row[csf('id')]]['reqs_no'];
                $buyer_nam=$job_no_array[$row[csf('po_id')]]['buyer_id'];

                $balance_qnty = $row[csf('program_qnty')] - $yarn_req_qnty;

                if (($planning_status == 3 && $balance_qnty <= 0) || ($planning_status == 1 && $balance_qnty > 0)) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    if (!in_array($machine_dia_gg, $machine_dia_gg_array)) {
                        if ($k != 1) {
                            ?>
                            <tr bgcolor="#CCCCCC">
                                <td colspan="14" align="right"><b>Sub Total</b></td>
                                <td align="right">
                                    <b><? echo number_format($sub_tot_program_qnty, 2, '.', ''); ?></b></td>
                                    <td align="right">
                                        <b><? echo number_format($sub_tot_yarn_req_qnty, 2, '.', ''); ?></b></td>
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
                                    <td colspan="20"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
                                </tr>
                                <?
                                $machine_dia_gg_array[] = $machine_dia_gg;
                                $k++;
                            }

                            $style_ref = $job_no_array[$row[csf('po_id')]]['style_ref'];
                            $job_no = $job_no_array[$row[csf('po_id')]]['job_no'];
                            $job_ids = $job_no_array[$row[csf('po_id')]]['job_id'];

                            if ($row[csf('within_group')] == 1) {
                                $buyer = $company_arr[$row[csf('buyer_id')]];
                                $customers_buyer = $buyer_arr[$booking_buyer_array[$row[csf('booking_no')]]];
                            } else {
                                $buyer = $buyer_arr[$buyer_nam];
                                $customers_buyer = $buyer_arr[$buyer_nam];
                            }

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
                                <td width="60" align="center"><p><? echo $row[csf('dia')]; ?></p></td>
                                <td width="80" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
                                <td width="90" align="center"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
                                <td align="right" width="90"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
                                <td align="center" valign="middle" width="90">
                                    <input type="text" name="txt_yarn_req_qnty[]" id="txt_yarn_req_qnty_<? echo $i; ?>"
                                    style="width:70px" class="text_boxes_numeric" readonly
                                    value="<? if ($yarn_req_qnty > 0) echo number_format($yarn_req_qnty, 2); ?>"
                                    placeholder="Single Click"
                                    onClick="openmypage_yarnReq(<? echo $i; ?>,'<? echo $row[csf('id')]; ?>',<? echo $company_name; ?>,'<? echo $comps; ?>','<? echo $job_no; ?>','<? echo $reqs_no; ?>','<? echo $job_ids; ?>','<? echo $cbo_within_group; ?>')"/>
                                </td>
                                <td align="center"
                                width="70"><? echo "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $row[csf('id')] . "," . $row[csf('within_group')] . ")\">$reqs_no</a>"; ?>
                            &nbsp;</td>
                            <td width="75" align="center">
                                &nbsp;<? if ($row[csf('start_date')] != "" && $row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?></td>
                                <td width="75" align="center">
                                    &nbsp;<? if ($row[csf('end_date')] != "" && $row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?></td>
                                    <td align="center"><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p>
                                    </td>
                                </tr>
                                <?

                                $sub_tot_program_qnty += $row[csf('program_qnty')];
                                $sub_tot_yarn_req_qnty += $yarn_req_qnty;

                                $tot_program_qnty += $row[csf('program_qnty')];
                                $tot_yarn_req_qnty += $yarn_req_qnty;

                                $i++;
                            }
                        }
                        if ($i > 1) {
                            ?>
                            <tr bgcolor="#CCCCCC">
                                <td colspan="14" align="right"><b>Sub Total</b></td>
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
                        <th colspan="14" align="right">Grand Total</th>
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
	?>

	<script>
		var permission = '<? echo $permission; ?>';

		function calculate(field_id) {
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

		function openpage_lot() {
			var page_link = "yarn_requisition_entry_sales_controller.php?action=lot_info_popup&companyID=<? echo $companyID; ?>" +
			"&knit_dtlsId=<? echo $knit_dtlsId; ?>" + "&comps=" + '<? echo $comps; ?>' + "&job_no=" + '<? echo $job_no; ?>';
			var title = 'Lot Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var prod_id = this.contentDoc.getElementById("hidden_prod_id").value;
				var data = this.contentDoc.getElementById("hidden_data").value.split("**");
				$('#prod_id').val(prod_id);
				$('#txt_lot').val(data[0]);
				$('#cbo_yarn_count').val(data[1]);
				$('#cbo_yarn_type').val(data[2]);
				$('#txt_color').val(data[3]);
				$('#txt_composition').val(data[4]);
				$('#txt_available_qty').val(data[5]);
				$('#hidden_lot_available_qnty').val(data[5]);
				$('#hidden_dyed_type').val(data[6]);
			}
		}

		function fnc_yarn_req_entry(operation) {
			var hidden_lot_available_qnty = parseFloat($('#hidden_lot_available_qnty').val());
			var txt_yarn_qnty = parseFloat($('#txt_yarn_qnty').val());

			
			if (txt_yarn_qnty > hidden_lot_available_qnty) {
				alert("Requisition Quantity is not Available");
				return;
			}

			if (form_validation('txt_lot*txt_yarn_qnty*txt_reqs_date', 'Lot*Yarn Qnty*Requisition Date') == false) {
				return;
			}

			var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('prod_id*txt_no_of_cone*txt_reqs_date*txt_yarn_qnty*updateId*update_dtls_id*txt_requisition_no*companyID*sale_order_id*txt_within_group*job_no*original_prod_id*original_prod_qnty*hidden_dyed_type', "../../");

			freeze_window(operation);

			http.open("POST", "yarn_requisition_entry_sales_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_req_entry_Reply_info;
		}

		function fnc_yarn_req_entry_Reply_info() {
			if (http.readyState == 4) {
				var reponse = trim(http.responseText).split('**');

				if (reponse[0] == 11) {
					alert("Duplicate Item Not Allowed");
				}
				else if (reponse[0] == 17) {
					alert(reponse[1]);
				}
				else if (reponse[0] == 18) {
					alert(reponse[1]);
				}
				else {
					show_msg(reponse[0]);

					if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
						reset_form('yarnReqQnty_1', '', '', '', '', 'updateId*companyID*sale_order_id*txt_within_group*job_no');
						$('#txt_requisition_no').val(reponse[3]);
						$('#hide_req_no').val(reponse[3]);
						show_list_view(reponse[1], 'requisition_info_details', 'list_view', 'yarn_requisition_entry_sales_controller', '');
					}
				}

				set_button_status(reponse[2], permission, 'fnc_yarn_req_entry', 1);
				release_freezing();
			}
		}

		function fnc_close() {
            //$yarn_req_qnty=return_field_value( "sum(yarn_qnty) as yarn_qnty","ppl_yarn_requisition_entry", "knit_id=$knit_dtlsId","yarn_qnty");
            //$('#hidden_yarn_req_qnty').val( //echo $yarn_req_qnty; );
            parent.emailwindow.hide();
        }

        //generate_report2(".$row[csf('company_id')].",".$row[csf('id')].")

        function generate_report_print2(company_id, program_id, within_group) {
        	var req_no = $('#hide_req_no').val();
        	if (req_no != "") {
                //print_report(company_id + '*' + program_id, "print_popup", "yarn_requisition_entry_sales_controller");
                var path = '';
                print_report(program_id + '**0**' + path + '**' + within_group, "requisition_print_two", "../reports/requires/knitting_status_report_sales_controller");
            }
            else {
            	alert("Save Data First");
            	return;
            }
        }
        function generate_report_print(company_id, program_id) {
        	var req_no = $('#hide_req_no').val();
        	if (req_no != "") {
        		print_report(company_id + '*' + program_id, "print_popup", "yarn_requisition_entry_sales_controller");
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
							<input type="hidden" name="hide_req_no" id="hide_req_no" class="text_boxes"
							value="<? echo $reqs_no; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="must_entry_caption">Lot</td>
						<td>
							<input type="text" name="txt_lot" id="txt_lot" class="text_boxes" placeholder="Double Click" style="width:130px;" onDblClick="openpage_lot();" readonly/>
							<input type="hidden" name="prod_id" id="prod_id" class="text_boxes" readonly/>
							<input type="hidden" name="hidden_dyed_type" id="hidden_dyed_type" class="text_boxes" readonly/><input type="hidden" name="original_prod_id" id="original_prod_id" class="text_boxes" readonly/>
							<input type="hidden" name="original_prod_qnty" id="original_prod_qnty" class="text_boxes" readonly/>
							<input type="hidden" name="hidden_yarn_req_qnty" id="hidden_yarn_req_qnty"
							class="text_boxes" readonly/>
							<input type="hidden" name="hidden_lot_available_qnty" id="hidden_lot_available_qnty"
							readonly/>
							<input type="hidden" name="companyID" id="companyID" value="<?php echo $companyID; ?>"
							readonly/>
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
						<td class="must_entry_caption">Yarn Reqs. Qnty</td>
						<td>
							<input type="text" name="txt_yarn_qnty" id="txt_yarn_qnty" class="text_boxes_numeric" style="width:130px;"/>
						</td>

						<td>No of Cone</td>
						<td>
							<input type="text" name="txt_no_of_cone" id="txt_no_of_cone" class="text_boxes_numeric" style="width:130px;"/>
						</td>

					</tr>
					<tr>
						<td>Available Req.</td>
						<td>
							<input type="text" name="txt_available_qty" id="txt_available_qty" class="text_boxes_numeric" style="width:130px;" readonly />
						</td>
						<td class="must_entry_caption">Requisition Date</td>
						<td>
							<input type="text" name="txt_reqs_date" id="txt_reqs_date" class="datepicker"
							style="width:130px;" value="<? echo date("d-m-Y");?>" />
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
							<input type="button" name="btn_print" class="formbutton" value="Print" id="btn_print" onClick="generate_report_print('<? echo str_replace("'", '', $companyID); ?>','<? echo str_replace("'", '', $knit_dtlsId); ?>')" style="width:100px"/>
							<input type="button" name="btn_print" class="formbutton" value="Print 2" id="btn_print"
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
				if (str_replace("'", '', $knit_dtlsId) != "") {
					?>
					<script>
						show_list_view('<? echo str_replace("'", '', $knit_dtlsId); ?>', 'requisition_info_details', 'list_view', 'yarn_requisition_entry_sales_controller', '');
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

	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');

	$prod_data = sql_select("select id, supplier_id, yarn_count_id, yarn_type from product_details_master where item_category_id=1 and company_id=$companyID and status_active=1 and is_deleted=0");
	foreach ($prod_data as $row) {
		$supplierArr[$row[csf('supplier_id')]] = $supplier_arr[$row[csf('supplier_id')]];
		$countArr[$row[csf('yarn_count_id')]] = $count_arr[$row[csf('yarn_count_id')]];
		$yarn_type_arr[$row[csf('yarn_type')]] = $yarn_type[$row[csf('yarn_type')]];
	}
	//asort($supplierArr);
	//asort($countArr);
	//asort($yarn_type_arr);
	?>
	<script>
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function js_set_value(id, data) {
			$('#hidden_prod_id').val(id);
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="">
		<form name="searchfrm" id="searchfrm">
			<fieldset>
				<input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
				<div><b><? echo $comps; ?></b></div>
				<table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Supplier</th>
					<th>Count</th>
					<th>Yarn Description</th>
					<th>Type</th>
					<th>Lot</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;"></th>
				</thead>
				<tbody>
					<tr align="center">
						<td><? echo create_drop_down("cbo_supplier", 130, $supplierArr, "", 1, "-- Select --", '', "", 0); ?></td>
						<td><? echo create_drop_down("cbo_count", 80, $countArr, "", 1, "-- Select --", '', "", 0); ?></td>
						<td><input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:150px">
						</td>
						<td><? echo create_drop_down("cbo_type", 110, $yarn_type_arr, "", 1, "-- Select --", '', "", 0); ?></td>
						<td><input type="text" name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:70px">
						</td>
						<td>
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view (document.getElementById('cbo_supplier').value+'**'+document.getElementById('cbo_count').value+'**'+document.getElementById('txt_desc').value+'**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_lot_no').value+'**'+<? echo $companyID; ?>, 'create_product_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:70px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:02px" id="search_div"></div>
		</fieldset>
	</form>
</div>
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
	<table width="790" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="80">Lot No</th>
			<th width="70">Count</th>
			<th width="80">Type</th>
			<th width="150">Composition</th>
			<th width="90">Color</th>
			<th width="90">No of Cone</th>
			<th width="90">Requisition Date</th>
			<th>Yarn Reqs. Qnty</th>
		</thead>
	</table>
	<div style="width:790px; overflow-y:scroll; max-height:300px;" id="scroll_body" align="left">
		<table class="rpt_table" rules="all" border="1" width="773" id="tbl_list_search">
			<?
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
			$color_library = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

			if($data!="")
			{
				$query = sql_select("select id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty from ppl_yarn_requisition_entry where knit_id=$data and status_active = '1' and is_deleted = '0'");
			}
			
			$i = 1;
			$tot_yarn_qnty = 0;
			foreach ($query as $selectResult) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color from product_details_master where status_active=1 and is_deleted=0 and id=" . $selectResult[csf('prod_id')] . "");

				$compos = '';
				if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0) {
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
				} else {
					$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
				}

				$tot_yarn_qnty += $selectResult[csf('yarn_qnty')];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="cursor:pointer"
					onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>, 'populate_requisition_data', 'yarn_requisition_entry_sales_controller' );">
					<td width="80"><p><? echo $dataArray[0][csf('lot')]; ?></p></td>
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
				<th colspan="7">Total</th>
				<th><? echo number_format($tot_yarn_qnty, 2); ?></th>
			</tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action == "populate_requisition_data")
{
	if($data!="")
	{
		$sql = "select a.id, a.knit_id, a.requisition_no, a.prod_id, a.no_of_cone, a.requisition_date, a.yarn_qnty,c.company_id from ppl_yarn_requisition_entry a,ppl_planning_info_entry_dtls b ,ppl_planning_info_entry_mst c where a.knit_id=b.id and b.mst_id=c.id and a.id=$data";
	}
	
	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$dataArray = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color,available_qnty from product_details_master where id=" . $row[csf('prod_id')]);

		$compos = '';
		if ($dataArray[0][csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]] . " " . $dataArray[0][csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$dataArray[0][csf('yarn_comp_type1st')]] . " " . $dataArray[0][csf('yarn_comp_percent1st')] . "%" . " " . $composition[$dataArray[0][csf('yarn_comp_type2nd')]];
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
		echo "document.getElementById('txt_yarn_qnty').value 				= '" . $row[csf("yarn_qnty")] . "';\n";
		echo "document.getElementById('prod_id').value 						= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_id').value 			= '" . $row[csf("prod_id")] . "';\n";
		echo "document.getElementById('original_prod_qnty').value 			= '" . $row[csf("yarn_qnty")] . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('companyID').value 				    = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('hidden_lot_available_qnty').value 	= '" . ($row[csf("yarn_qnty")] + $dataArray[0][csf("available_qnty")]) . "';\n";
		echo "document.getElementById('txt_available_qty').value 	= '" . ($row[csf("yarn_qnty")] + $dataArray[0][csf("available_qnty")]) . "';\n";


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
								onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value +'**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'yarn_requisition_entry_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
	//echo $updateId; exit;
	if ($operation == 0)  // Insert Here
	{

		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=$updateId and prod_id=$prod_id and status_active=1 and is_deleted=0") == 1) {
			echo "11**" . str_replace("'", "", $updateId) . "**0";
			disconnect($con);
			exit();
		}

		//$check_product_available_qnty = return_field_value("(current_stock-allocated_qnty) as available_qnty", "product_details_master", "id=$prod_id","available_qnty");

		$prodStockAvailavle = sql_select("select current_stock,(current_stock-allocated_qnty) as available_qnty from product_details_master where status_active=1 and is_deleted=0 and id=$prod_id");

		$check_product_available_qnty = $prodStockAvailavle[0][csf('available_qnty')];
		$prodCurrentStock = $prodStockAvailavle[0][csf('current_stock')];
		
		//echo "17**$prodCurrentStock"; die();

		if($prodCurrentStock<0.01)
		{
			echo "17**Stock Quantity is not Available\nStock quantity = $prodCurrentStock";
			disconnect($con);
			exit();
		}

		if (str_replace("'", "", $txt_yarn_qnty) > $check_product_available_qnty)
		{
			echo "17**Quantity is not available for Requisition.\nAvailable quantity = $check_product_available_qnty";
			disconnect($con);
			exit();
		}

		$requisition_no = return_field_value("requisition_no", "ppl_yarn_requisition_entry", "knit_id=$updateId","requisition_no");
		if ($requisition_no == "") 
		{
			$requisition_no = return_next_id("requisition_no", "ppl_yarn_requisition_entry", 1);
			$rechk_requisition_no = return_field_value("requisition_no", "ppl_yarn_requisition_entry", "requisition_no=$requisition_no","requisition_no");//Recheck requsition no

			if($rechk_requisition_no!="") //system have 
			{
				$requisition_no = return_next_id("requisition_no", "ppl_yarn_requisition_entry", 1);
			}
			else
			{
				$requisition_no = $requisition_no;
			}

		}
		else
			$requisition_no = $requisition_no;

		// check variable settings if allocation is available or not
		$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$companyID and variable_list=18 and item_category_id = 1");

		if ($variable_set_allocation == 1) 
		{
			// prepare data to update allocation
			if($updateId!="")
			{
				$sql = "select a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc, c.po_id from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id=$updateId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.determination_id, a.dia, a.within_group, a.fabric_desc,c.po_id";
			}
			
			$planning_array = sql_select($sql);

			$po_id = $planning_array[0][csf('po_id')];
			$booking_no = $planning_array[0][csf('booking_no')];
			$job_no = str_replace("'", "", $job_no);
			$prod_id = str_replace("'", "", $prod_id);
			$hidden_dyed_type = str_replace("'", "", $hidden_dyed_type);
			
			if($hidden_dyed_type==2 || $hidden_dyed_type==0 || $hidden_dyed_type==""){
				$hidden_dyed_type = 0;
			}else{
				$hidden_dyed_type = $hidden_dyed_type;
			}

			// if allocation found
			$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $prod_id) . " and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";

			$check_allocation_array = sql_select($sql_allocation);

			$id = return_next_id("id", "ppl_yarn_requisition_entry", 1);
			$field_array = "id, knit_id, requisition_no, prod_id, no_of_cone, requisition_date, yarn_qnty, inserted_by, insert_date";

			$data_array = "(" . $id . "," . $updateId . "," . $requisition_no . "," . $prod_id . "," . $txt_no_of_cone . "," . $txt_reqs_date . "," . $txt_yarn_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			$rID1 = sql_insert("ppl_yarn_requisition_entry", $field_array, $data_array, 0);

			if (!empty($check_allocation_array)) 
			{
				$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty)*1;
				$mst_id = $check_allocation_array[0][csf('id')];

				if($rID1) //requsition data transection true then allocation
				{
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty+$txt_yarn_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
				}
				
				$rID3 = false;
				if ($rID1 && $rID2) // requsition and mst allocation data transection true then dtls allocation
				{
					$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty+$txt_yarn_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id='$po_id' and item_id = $prod_id", 0);
				}
			} 
			else 
			{
				if($job_no!="")
				{
					$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
					$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
					$data_array_allocation_mst = "(" . $id_allocation . ",".$id.",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
					$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
					$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					if($rID1) // Requsition true then 
					{
						$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
					}
					
					$rID3 = false;
					if ($data_array_allocation_dtls != '') {
						if( $rID1 && $rID2 ) // requsition and mst allocation data transection true then dtls allocation
						{
							$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
						}						
					}
				}
				else
				{
					$rID2 = $rID3 = false;
				}
			}
			// update product allocation,available details
			if($rID2 && $rID3) // mst allocation and dtls allocation data transection true then
			{
				$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$txt_yarn_qnty),available_qnty=(current_stock-(allocated_qnty+$txt_yarn_qnty)),update_date='" . $pc_date_time . "' where id=$prod_id", 0);	
			}						
		} 
		
		/*oci_rollback($con);
		echo "10**$rID1 && $rID2 && $rID3 && $rID4";
		disconnect($con);
		die();*/

		if ($db_type == 0) {
			if ($rID1 && $rID2 && $rID3 && $rID4) {
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
			} else {
				mysql_query("ROLLBACK");
				echo "5**" . str_replace("'", "", $updateId) . "**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2 && $rID3 && $rID4) {
				oci_commit($con);
				echo "0**" . str_replace("'", "", $updateId) . "**0**" . $requisition_no;
			} else {
				oci_rollback($con);
				echo "5**" . str_replace("'", "", $updateId) . "**0";
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1) // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$original_prod_id = str_replace("'", "", $original_prod_id);
		// check if item is already found against this requisition
		if (is_duplicate_field("prod_id", "ppl_yarn_requisition_entry", "knit_id=$updateId and prod_id=$prod_id and id<>$update_dtls_id and status_active=1 and is_deleted=0") == 1) {
			echo "11**" . str_replace("'", "", $updateId) . "**1";
			disconnect($con);
			exit();
		}

		//$check_product_available_qnty = return_field_value("(current_stock-allocated_qnty) as available_qnty", "product_details_master", "id=$prod_id","available_qnty");

		$prodStockAvailavle = sql_select("select current_stock,(current_stock-allocated_qnty) as available_qnty from product_details_master where status_active=1 and is_deleted=0 and id=$prod_id");

		$check_product_available_qnty = $prodStockAvailavle[0][csf('available_qnty')];
		$prodCurrentStock = $prodStockAvailavle[0][csf('current_stock')];
		
		//echo "17**$prodCurrentStock"; die();

		if($prodCurrentStock<0.01)
		{
			echo "17**Stock Quantity is not Available\nStock quantity = $prodCurrentStock";
			disconnect($con);
			exit();
		}
		
		if(str_replace("'", "", $prod_id) == str_replace("'", "", $original_prod_id))
		{
			$qnty_limit = (str_replace("'", "", $original_prod_qnty)+$check_product_available_qnty);
			if ( str_replace("'", "", $txt_yarn_qnty) > $qnty_limit ) 
			{
				echo "17**Requisition Quantity is not Available\nAvailable quantity = ".$qnty_limit;
				disconnect($con);
				exit();
			}
		}
		else
		{
			if ( str_replace("'", "", $txt_yarn_qnty) > $check_product_available_qnty ) 
			{
				echo "17**Requisition Quantity is not Available\nAvailable quantity = ".$check_product_available_qnty;
				disconnect($con);
				exit();
			}
		}
			
		// === Auto allocation missing start 07-22-2020 ==//
		/*
		$total_requis_result = sql_select("select sum(yarn_qnty) total_requisition_qnty from ppl_yarn_requisition_entry where prod_id=$original_prod_id and status_active=1 and is_deleted=0");
		$total_alocation_result = sql_select("select sum(qnty) total_allocation_qnty from inv_material_allocation_dtls where item_id=$original_prod_id and status_active=1 and is_deleted=0");

		$total_requisition_qnty = $total_requis_result[0][csf("total_requisition_qnty")];
		$total_allocation_qnty = $total_alocation_result[0][csf("total_allocation_qnty")];

		$requisition_balance_qty = ($check_product_available_qnty-$total_requisition_qnty);
		
		if( $total_requisition_qnty!=$total_allocation_qnty )
		{
			$allocation_missing_qty = ($total_requisition_qnty-$total_allocation_qnty);
			$realAvailableQty = ( ($check_product_available_qnty-$allocation_missing_qty)+str_replace("'", "", $original_prod_qnty));

			if(str_replace("'", "", $txt_yarn_qnty) > $realAvailableQty )
			{
				echo "17**Auto allocation missing across with requisiiton\nMissing quantity=$allocation_missing_qty\nTotal requisiiton quantity=$total_requisition_qnty\nTotal allocation quantity=$total_allocation_qnty";
				disconnect($con);
				exit();
			}			
		}
		*/
		// === Auto allocation missing End==//

		// check if issue found against daily demand
		$check_demand_entry = sql_select("select sum(yarn_demand_qnty) yarn_demand_qnty from ppl_yarn_demand_reqsn_dtls where requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");
		if($original_prod_id != str_replace("'", "", $prod_id)){
			if(!empty($check_demand_entry) && $check_demand_entry[0][csf("yarn_demand_qnty")] > 0){
				echo "17**Demand found. Lot can not be changed.";
				disconnect($con);
				exit();
			}
		}else{
			if(($check_demand_entry[0][csf("yarn_demand_qnty")] != "") && (str_replace("'", "", $txt_yarn_qnty) < $check_demand_entry[0][csf("yarn_demand_qnty")])){
				echo "17**Requisition quantity can not be less than daily yarn demand entry.";
				disconnect($con);
				exit();
			}
		}

		// check if issue found against Requisition and Requisition Quantity can not be less than Issue Quantity
		$check_issue = sql_select("select sum(case when a.transaction_type=2 then a.cons_quantity else 0 end) - sum(case when a.transaction_type=4 then a.cons_quantity else 0 end) issue_qnty from inv_transaction a where a.transaction_type in(2,4) and a.requisition_no=$txt_requisition_no and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$prod_id");

		$check_issue_return = sql_select("select sum(b.cons_quantity) issue_return_qnty from inv_transaction b where issue_id in(select a.mst_id from inv_transaction a where a.transaction_type in(2) and a.requisition_no=$txt_requisition_no and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.prod_id=$prod_id) and b.transaction_type=4 and b.receive_basis=3 and b.prod_id=$prod_id");
		
		//echo "10**";
		//echo (str_replace("'", "", $txt_yarn_qnty) ."<". ($check_issue[0][csf('issue_qnty')]-$check_issue_return[0][csf('issue_return_qnty')])); die;

		$issueQty = $check_issue[0][csf('issue_qnty')]; 
		$issueReturnQty = $check_issue_return[0][csf('issue_return_qnty')];		
		$balanceQnty = ($issueQty - $issueReturnQty);
		$requisitionQty = str_replace("'", "", $txt_yarn_qnty)*1;
		
		if ( !empty($check_issue) && ($requisitionQty < $balanceQnty ) ) {
			echo "17**Issue Found.\nIssue Quantity =$issueQty\nIssue Return quantity =$issueReturnQty\nUpto Reduce Balance =$balanceQnty";
			disconnect($con);
			exit();
		}

		// check variable settings if allocation is available or not
		$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$companyID and variable_list=18 and item_category_id = 1","allocation");

		if ($variable_set_allocation == 1) 
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
			$hidden_dyed_type = str_replace("'", "", $hidden_dyed_type);
			
			if($hidden_dyed_type==2 || $hidden_dyed_type==0 || $hidden_dyed_type==""){
				$hidden_dyed_type = 0;
			}else{
				$hidden_dyed_type = $hidden_dyed_type;
			}

			// IF USER CHANGE LOT WHILE UPDATE
			if(str_replace("'", "", $prod_id) != str_replace("'", "", $original_prod_id))
			{
				// CHECK IF ISSUE FOUND
				$check_issue_against_requisition_lot = sql_select("select sum(cons_quantity) cons_quantity from inv_transaction where receive_basis=3 and transaction_type=2 and item_category=1 and requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");
				if($check_issue_against_requisition_lot[0][csf("cons_quantity")] != "" || $check_issue_against_requisition_lot[0][csf("cons_quantity")] != null){
					echo "17**Issue found.You can not change this lot.";
					disconnect($con);
					exit();
				}
				// CHECK IF DAILY YARN DEMAND FOUND
				$check_demand_entry = return_field_value("sum(yarn_demand_qnty)yarn_demand_qnty", "ppl_yarn_demand_reqsn_dtls", "requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");
				if($check_demand_entry[0][csf("yarn_demand_qnty")] != "" || $check_demand_entry[0][csf("yarn_demand_qnty")] != null){
					echo "17**Daily yarn demand found.You can not change this lot.";
					disconnect($con);
					exit();
				}

				$new_allocate_data = str_replace("'", "", $original_prod_qnty);
				// CHECK PREVIOUS PRODUCT ALLOCATION
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=$original_prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";

				$check_allocation_array = sql_select($sql_allocation);
				$mst_id = $check_allocation_array[0][csf('id')];				

				if (!empty($check_allocation_array)) 
				{
					// UPDATE PREVIOUS PRODUCT ALLOCATION MST				
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=(qnty-$new_allocate_data),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and item_id=$original_prod_id", 0);								

					$rID3 = false;
					if ($rID2) {
					// UPDATE PREVIOUS PRODUCT ALLOCATION DTLS
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=(qnty-$new_allocate_data),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id='$po_id' and item_id=$original_prod_id", 0);

					}
					// UPDATE PREVIOUS PRODUCT DETAILS
					if($rID2 && $rID3) // Requsition  and allocation mst and allocation dtls data transection true then
					{
						$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$new_allocate_data),available_qnty=(current_stock-(allocated_qnty-$new_allocate_data)),update_date='" . $pc_date_time . "' where id=$original_prod_id  ", 0);
					}					
				}

				// NEW PRODUCT ALLOCATION
				$new_prod_id = str_replace("'", "", $prod_id);
				$pro_allocate_data = str_replace("'", "", $txt_yarn_qnty);
				// CHECK IF NEW PRODUCT ALLOCATION FOUND
				$sql_new_prod_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=$new_prod_id and a.job_no='$job_no' and a.booking_no='$booking_no' and status_active=1 and is_deleted=0";
				$check_new_allocation_array = sql_select($sql_new_prod_allocation);
				if (!empty($check_new_allocation_array)) 
				{
					$mst_id = $check_new_allocation_array[0][csf('id')];
					$allocation_qnty = $check_new_allocation_array[0][csf('qnty')] + str_replace("'", "", $txt_yarn_qnty);
					
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and item_id=$prod_id", 0);				
					
					$rID3 = false;
					if ($rID2) 
					{
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and item_id=$prod_id", 0);
					}
					$rID3=$rID5=$rID6=true;
				}
				else
				{
					if($job_no!="")
					{
						// INSERT NEW ALLOCATION WITH CHANGED PRODUCT
						$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_mst = "(" . $id_allocation . ",".$update_dtls_id.",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						
						$rID5 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
						$rID6 = false;
						if ($data_array_allocation_dtls != '') {
							$rID6 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
						}
										
					}
					else
					{
						$rID2 = $rID3 = false;
					}
				}

				// UPDATE CHANGED PRODUCT DETAILS
				if($rID2 && $rID3) // Requsition true then
				{
					$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),available_qnty=(current_stock-(allocated_qnty+$pro_allocate_data)),update_date='" . $pc_date_time . "' where id=$new_prod_id  ", 0);
				}
			}
			else
			{
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $prod_id) . " and a.job_no='$job_no' and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0";

				//echo "6**".$sql_allocation; die();

				$check_allocation_array = sql_select($sql_allocation);

				if (!empty($check_allocation_array)) 
				{
					$pro_allocate_data = (str_replace("'", "", $txt_yarn_qnty)-str_replace("'", "", $original_prod_qnty));
					$mst_id = $check_allocation_array[0][csf('id')];
					$allocation_qnty = ($check_allocation_array[0][csf('qnty')]-str_replace("'", "", $original_prod_qnty)) + str_replace("'", "", $txt_yarn_qnty);	
					
					$rID2 = execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id and item_id=$prod_id", 0);
					

					$rID3 = false;
					if ($rID2) 
					{
						$rID3 = execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and item_id=$prod_id", 0);
					}
				} 
				else 
				{
					if($job_no!="")
					{
						// NER YARN ALLOCATION TO SALES ORDER
						$pro_allocate_data = str_replace("'", "", $txt_yarn_qnty);
						$id_allocation = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_array_allocation_mst = "id,mst_id,entry_form,job_no,po_break_down_id,allocation_date,booking_no,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_mst = "(" . $id_allocation . ",".$update_dtls_id.",120,'" . $job_no . "','" . $po_id . "'," . $txt_reqs_date . ",'" . $booking_no . "'," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$id_allocation_dtls = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_array_allocation_dtls = "id,mst_id,job_no,po_break_down_id,booking_no,allocation_date,item_id,qnty,is_sales,is_dyied_yarn,inserted_by,insert_date";
						$data_array_allocation_dtls = "(" . $id_allocation_dtls . "," . $id_allocation . ",'" . $job_no . "','" . $po_id . "','" . $booking_no . "'," . $txt_reqs_date . "," . $prod_id . "," . $txt_yarn_qnty . ",1," . $hidden_dyed_type . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$rID2 = sql_insert("inv_material_allocation_mst", $field_array_allocation_mst, $data_array_allocation_mst, 0);
						

						$rID3 = false;
						if ($data_array_allocation_dtls != '') {
							$rID3 = sql_insert("inv_material_allocation_dtls", $field_array_allocation_dtls, $data_array_allocation_dtls, 0);
						}
					}else{
						$rID2 = $rID3 = false;
					}
				}
				// UPDATE PRODUCT DETAILS
				if($rID2 && $rID3) // Requsition and allocation mst, allocation dtls true then
				{
					$rID4 = execute_query("update product_details_master set allocated_qnty=(allocated_qnty+$pro_allocate_data),available_qnty=(current_stock-(allocated_qnty+$pro_allocate_data)),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);

				}
				
				$rID5=$rID6=1;
			}

			$field_array_update = "prod_id*no_of_cone*requisition_date*yarn_qnty*updated_by*update_date";
			$data_array_update = $prod_id . "*" . $txt_no_of_cone . "*" . $txt_reqs_date . "*" . $txt_yarn_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$rID1 = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		} 

		//echo $rID1 ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5."&&". $rID6; die();

		if ($db_type == 0) {
			if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 ) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 ) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $updateId) . "**0**" . str_replace("'", "", $txt_requisition_no);
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 2) // Deleted 
	{
		$con = connect();
		if ($db_type == 0) {
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

		if($check_demand_entry[0][csf("yarn_demand_qnty")] != "" || $check_demand_entry[0][csf("yarn_demand_qnty")] != null){
			echo "18**Daily yarn demand found.Requisition can not be deleted.\nDemand Id=".$check_demand_entry[0][csf("demand_system_no")];
			disconnect($con);
			exit();
		}

		// CHECK IF ISSUE FOUND
		$check_issue = sql_select("select sum(cons_quantity) cons_quantity from inv_transaction where receive_basis=3 and transaction_type=2 and item_category=1 and requisition_no=$txt_requisition_no and prod_id=$original_prod_id and status_active=1 and is_deleted=0");
		if($check_issue[0][csf("cons_quantity")] > 0 || $check_issue[0][csf("cons_quantity")] != null){
			echo "17**Issue found.You can not change this lot.";
			disconnect($con);
			exit();
		}

		$txt_yarn_qnty = str_replace("'", "", $txt_yarn_qnty);

		// check variable settings if allocation is available or not
		$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$companyID and variable_list=18 and item_category_id = 1");

		if ($variable_set_allocation == 1) 
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


			$field_array_update = "status_active*is_deleted*updated_by*update_date";
			$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$rID = sql_update("ppl_yarn_requisition_entry", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);

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
		
		//echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4;die;
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
?>