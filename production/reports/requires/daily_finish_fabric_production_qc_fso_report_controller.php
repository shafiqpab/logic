<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action == "load_drop_down_buyer") 
{
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

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

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:780px;">
					<table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer Name</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
						<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
					</thead>
					<tbody>
						<tr>
							<td id="buyer_td">
								<?
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $buyerID, "");
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref",3 => "Booking No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'daily_finish_fabric_production_qc_fso_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_search_list_view")
{
	$data = explode('**', $data);
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$buyer_id = $data[1];
	$po_buyer_id = $data[2];
	$within_group = $data[3];
	$search_by = $data[4];
	$search_string = trim($data[5]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		} else if($search_by == 2) {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
		else
		{
			$search_field_cond = " and a.sales_booking_no like '%$search_string%'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
	//echo "==".$_SESSION['logic_erp']["buyer_id"];die;
	if ($po_buyer_id == 0) {
		if ($_SESSION['logic_erp']["buyer_id"] != "")
		{
			if($within_group == 1)
			{
				$po_buyer_id_cond = " and a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			}
			else if($within_group == 2)
			{
				$po_buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			}
			else
			{
				$po_buyer_id_cond = " and (a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] .") or a.buyer_id in ( " .$_SESSION['logic_erp']["buyer_id"]. ") )";
			}
		}
		else
		{
			$po_buyer_id_cond = "";
		}
	}
	else
	{
		if($within_group == 1)
		{
			$po_buyer_id_cond = " and a.po_buyer=$po_buyer_id";
		}
		else if($within_group == 2)
		{
			$po_buyer_id_cond = " and a.buyer_id=$po_buyer_id";
		}
		else
		{
			$po_buyer_id_cond = " and (a.po_buyer=$po_buyer_id or a.buyer_id=$po_buyer_id )";
		}
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, a.po_buyer, a.po_company_id from fabric_sales_order_mst a
	where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $po_buyer_id_cond order by a.id desc";

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="115">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Sales Order Buyer</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:820px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if ($row[csf('within_group')] == 1)
					$sales_order_buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$sales_order_buyer = $buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40" align="center"><? echo $i; ?>
					<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
					<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>"/>
				</td>
				<td width="115" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
				<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
				<td width="70" align="center"><p><? echo $sales_order_buyer; ?>&nbsp;</p></td>
				<td width="70" align="center"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
				<td width="70" align="center"><p><? echo $company_arr[$row[csf('po_company_id')]]; ?>&nbsp;</p></td>
				<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
				<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
	</div>
	<table width="800" cellspacing="0" cellpadding="0" style="border:none" align="left">
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

if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_no,booking_num) {
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_booking_num').val(booking_num);
			parent.emailwindow.hide();
		}

	</script>
	</head>

	<body>
		<div align="center" style="width:730px;">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:100%;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="725" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Po Buyer</th>
							<th>Booking Date</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="150">Please Enter Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
								value="<? echo $companyID; ?>">
								<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
								value="<? echo $cbo_within_group; ?>">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_booking_num" id="hidden_booking_num" class="text_boxes" value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								readonly>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Booking No", 2 => "Job No");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'daily_finish_fabric_production_qc_fso_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:90px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_booking_search_list_view") 
{
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);


	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	$search_field_cond = "";
	if ($search_by == 1) {
		$search_field_cond .= " and sales_booking_no like '%$search_string%'";
	}else{
		$search_field_cond .= " and po_job_no like '%$search_string%'";
	}
	if ($buyer_id != 0) {
		$search_field_cond .= " and po_buyer=$buyer_id";
	}
	if ($cbo_within_group > 0) {
		$search_field_cond .= " and within_group=$cbo_within_group";
	}
	$sql = "select id, sales_booking_no booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no from fabric_sales_order_mst where company_id= $company_id and status_active =1 and is_deleted=0 $search_field_cond $date_cond group by id, sales_booking_no, booking_date,buyer_id, company_id,job_no, style_ref_no,po_job_no";

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Sales Order No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th>Job No.</th>
		</thead>
	</table>
	<div style="width:720px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "") {
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id) {
					if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><? echo $row[csf('po_job_no')]; ?></td>
			</tr>
			<?
			$i++;
		}

		$sql_partial = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, b.job_no,b.po_job_no,b.style_ref_no from wo_booking_mst a, wo_booking_dtls c,fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_no_prefix_num,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,b.job_no,b.po_job_no,b.style_ref_no";
		$result_partial = sql_select($sql_partial);
		foreach ($result_partial as $row) {
			if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "") {
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id) {
					if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_no_prefix_num')]; ?>')">
				<td width="40"><? echo $j; ?>p</td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $row[csf('po_job_no')]; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
	</div>
	<?
	exit();
}

if ($action == "batch_popup") 
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id) {

			var item_id = id.split("_");
			// alert(item_id[0]+'='+item_id[1]);
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
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'daily_finish_fabric_production_qc_fso_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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

if ($action == "create_batch_search_list_view") 
{
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

if ($action == "production_popup") 
{
	echo load_html_head_contents("Production Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

    <script>

		function js_set_value(id) 
		{
			//alert(id);
			var item_id = id.split("**");
			// alert(item_id[0]+'='+item_id[1]);
			document.getElementById('hidden_production_id').value = item_id[0];
			document.getElementById('hidden_production_no').value = item_id[1];
			parent.emailwindow.hide();
		}

    </script>

    </head>

    <body>
    <div align="center" style="width:760px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:760px; margin-left:2px">
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Production Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Production No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                               class="formbutton"/>
                        <input type="hidden" name="hidden_production_id" id="hidden_production_id">
                        <input type="hidden" name="hidden_production_no" id="hidden_production_no">
                    </th>
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                                   style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                                   readonly>
                        </td>
                        <td align="center">
							<?
							$search_by_arr = array(1 => "Production No", 2 => "Challan No", 3 => "Barcode No", 4 => "Batch No");
							$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/>
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view(document.getElementById('txt_search_common').value + '_' + document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' +<? echo $cbo_company_id; ?>+'_'+ document.getElementById('cbo_year_selection').value, 'create_production_search_list_view', 'search_div', 'daily_finish_fabric_production_qc_fso_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40"
                            valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}


if ($action == "create_production_search_list_view") 
{
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0])."%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$year_id=$data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.recv_number like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and a.challan_no like '$search_string'";
		else if ($search_by == 3)
			$search_field_cond = "and b.barcode_no like '$search_string'";
		else if ($search_by == 4)
			$search_field_cond = "and d.batch_no like '$search_string'";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year,";
	} else
		$year_field = ""; //defined Later


	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and YEAR(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}

	//$sql = "select id, $year_field recv_number_prefix_num, recv_number, knitting_source, knitting_company, receive_date, store_id, challan_no from inv_receive_master where entry_form=66 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond $year_cond order by id";

	/*	$sql = "select a.id,  $year_field a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no
	from inv_receive_master a,pro_roll_details b
	where a.id=b.mst_id and a.entry_form=66 and b.entry_form=66  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond
	group by  a.id,a.insert_date, a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no
	order by a.id";*/

	$sql = "SELECT a.id,  $year_field a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no, d.batch_no
	from inv_receive_master a,pro_roll_details b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst d 
	where a.id=b.mst_id and a.id=c.mst_id and c.batch_id=d.id and a.entry_form=66 and b.entry_form=66  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond
	group by  a.id,a.insert_date, a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no, d.batch_no
	order by a.id";

	//$barcode_nos = return_field_value("group_concat(barcode_no order by id desc) as barcode_nos", "pro_roll_details", "entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");

	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
        <thead>
        <th width="40">SL</th>
        <th width="70">Production No</th>
        <th width="70">Batch No</th>
        <th width="60">Year</th>
        <th width="140">Challan No</th>
        <th width="120">Service Source</th>
        <th width="140">Service Company</th>
        <th>Production date</th>
        </thead>
    </table>
    <div style="width:810px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table"
               id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$dye_comp = "&nbsp;";
				if ($row[csf('knitting_source')] == 1)
					$dye_comp = $company_arr[$row[csf('knitting_company')]];
				else
					$dye_comp = $supllier_arr[$row[csf('knitting_company')]];
					
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value('<? echo $row[csf('id')] . "**" . $row[csf('recv_number')]; ?>');">
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('batch_no')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="140"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                 	<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                </tr>
				<?
				$i++;
			}
			?>
        </table>
    </div>
	<?
	exit();
}




/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", trim($cbo_buyer_name));
	$sales_job_no = str_replace("'", "", $txt_sales_job_no);
	$hide_job_id = str_replace("'", "", $hide_job_id);
	$sales_booking_no = str_replace("'", "", $txt_booking_no);
	$hide_booking_id = str_replace("'", "", $hide_booking_id);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$cbo_year_selection = str_replace("'", "", trim($cbo_year_selection));
	$cbo_within_group = str_replace("'", "", trim($cbo_within_group));
	$txt_batch_no = str_replace("'", "", trim($txt_batch_no));
	$hdn_batch_no = str_replace("'", "", trim($hdn_batch_no));
	$Production_no = str_replace("'", "", trim($txt_Production_no));
	$Production_id = str_replace("'", "", trim($hdn_Production_id));

	if($cbo_within_group != 0)
	{
		$within_group_cond = " and d.within_group = $cbo_within_group ";
	}

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
	}
	else
	{
		$year_cond="";
	}

	if ($end_date == "") {
		$end_date = $start_date;
	} else {
		$end_date = $end_date;
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$str_cond_insert = " and a.receive_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$str_cond_insert = " and a.receive_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} else {
		$str_cond_insert = "";
	}
	if($hide_job_id == ""){
		$sales_order_cond = ($sales_job_no != "") ? " and d.job_no_prefix_num=$sales_job_no" : "";
	}else{
		$sales_order_cond = " and d.id in($hide_job_id)";
	}
	if($hide_booking_id == ""){
		$sales_booking_cond = ($sales_booking_no != "") ? " and d.sales_booking_no like '%$sales_booking_no%'" : "";
	}else{
		$sales_booking_cond = " and d.sales_booking_no='$sales_booking_no'";
	}
	if($hdn_batch_no == ""){
		$batch_cond = ($txt_batch_no != "") ? " and a.batch_no='$txt_batch_no'" : "";
	}else{
		$batch_cond = " and a.id in($hdn_batch_no)";
	}

	if($Production_id == ""){
		$Production_cond = ($Production_no != "") ? " and a.recv_number like '%$Production_no%'" : "";
	}else{
		$Production_cond = " and a.id in($Production_id)";
	}

	$buyer_cond = ($buyer_name != 0) ? " and e.buyer_id=$buyer_name" : "";

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$batch_arr = return_library_array("select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$batch_ext_arr = return_library_array("select id, extention_no from  pro_batch_create_mst", "id", "extention_no");
	$batch_wc_arr = return_library_array("select id, working_company_id from  pro_batch_create_mst", "id", "working_company_id");
	$roll_dtls_arr = return_library_array("select dtls_id, id from  pro_roll_details", "dtls_id", "id");
	$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent, a.id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ");
	foreach ($determination_sql as $val)
	{

		if($val[csf("construction")] != "")
		{
			$fabric_desc_arr[$val[csf("id")]] = $val[csf("construction")]. ", ";
		}
		$fabric_desc_arr[$val[csf("id")]] .= $composition[$val[csf("copmposition_id")]]. " " .$val[csf("percent")] . "% ";
	}

	if ($batch_cond != "") 
	{
		$batch_sql = "SELECT a.id as batch_id,a.batch_no from pro_batch_create_mst a
		where  a.company_id=$company_name and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 $batch_cond";
		// echo $batch_sql;
	}
	$batchResult = sql_select($batch_sql);
	if (!empty($batchResult)) 
	{
		foreach ($batchResult as $key => $row) 
		{
			$batch_ids_arr[] = $row[csf("batch_id")];

		}		
		$batch_ids = implode(",",$batch_ids_arr);
		if($batch_ids != "")
		{
			$batch_ids_arr = explode(",", $batch_ids);
			$batch_ids_Cond=""; $idCond_14="";

			if($db_type==2 && count($batch_ids_arr)>999)
			{
				$batch_ids_arr_chunk=array_chunk($batch_ids_arr,999) ;
				foreach($batch_ids_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$idCond_14.=" b.batch_id in($chunk_arr_value) or ";
				}

				$batch_ids_Cond.=" and (".chop($idCond_14,'or ').")";
			}
			else
			{
				$batch_ids_Cond=" and b.batch_id in ($batch_ids)";
			}
		}
	}

	// FINISH PRODUCTION
	

	// $finish_qc_sql="SELECT A.RECV_NUMBER, A.RECEIVE_DATE, D.SALES_BOOKING_NO, D.JOB_NO as FSO_NO, B.BATCH_ID, B.PROD_ID, B.GSM, B.WIDTH AS DIA, B.COLOR_ID, C.PO_BREAKDOWN_ID, B.FABRIC_DESCRIPTION_ID, B.COLOR_ID,B.PROD_ID, SUM(C.QUANTITY ) QC_PASS_QTY, D.BOOKING_ID 
	// from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, fabric_sales_order_mst d left join wo_booking_mst e on d.booking_id = e.id   
	// where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(66) and a.company_id = $company_name   $within_group_cond $sales_order_cond $sales_booking_cond $buyer_cond $year_cond $batch_ids_Cond $str_cond_insert $Production_id_cond 
	// group by A.RECV_NUMBER, A.RECEIVE_DATE, D.SALES_BOOKING_NO, D.JOB_NO, B.BATCH_ID, B.PROD_ID, B.GSM, B.WIDTH, B.COLOR_ID, C.PO_BREAKDOWN_ID, B.FABRIC_DESCRIPTION_ID, B.COLOR_ID,B.PROD_ID, D.BOOKING_ID ";

	$finish_qc_sql="SELECT A.RECV_NUMBER, A.RECEIVE_DATE, D.SALES_BOOKING_NO, D.JOB_NO as FSO_NO, B.BATCH_ID, B.PROD_ID, B.GSM, B.WIDTH AS DIA, B.COLOR_ID, C.PO_BREAKDOWN_ID, B.FABRIC_DESCRIPTION_ID, B.COLOR_ID,B.PROD_ID, SUM(C.QUANTITY ) QC_PASS_QTY, D.BOOKING_ID,D.BOOKING_ENTRY_FORM,D.WITHIN_GROUP,D.BOOKING_WITHOUT_ORDER as BOOKING_WITHOUT_ORDER_SALES,listagg(B.ID, ',') within group (order by B.ID) as DTLS_ID  
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, fabric_sales_order_mst d left join wo_booking_mst e on d.booking_id = e.id   
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(66) and a.company_id = $company_name   $within_group_cond $sales_order_cond $sales_booking_cond $buyer_cond $year_cond $batch_ids_Cond $str_cond_insert $Production_cond 
	group by A.RECV_NUMBER, A.RECEIVE_DATE, D.SALES_BOOKING_NO, D.JOB_NO, B.BATCH_ID, B.PROD_ID, B.GSM, B.WIDTH, B.COLOR_ID, C.PO_BREAKDOWN_ID, B.FABRIC_DESCRIPTION_ID, B.COLOR_ID,B.PROD_ID, D.BOOKING_ID, D.BOOKING_ENTRY_FORM, D.WITHIN_GROUP,D.BOOKING_WITHOUT_ORDER";

	//echo $finish_qc_sql;
	$finish_qc_data = sql_select($finish_qc_sql);	
	$data_arr = array();
	$batch_data_arr = array();
	foreach ($finish_qc_data as $key => $row) 
	{
		$pre_key=$row['RECV_NUMBER']."*".$row['RECEIVE_DATE']."*".$row['SALES_BOOKING_NO']."*".$row['FSO_NO']."*".$row['BATCH_ID']."*".$row['FABRIC_DESCRIPTION_ID']."*".$row['GSM']."*".$row['DIA']."*".$row['COLOR_ID']."*".$row['BOOKING_ID']."*".$row['BOOKING_ENTRY_FORM']."*".$row['WITHIN_GROUP']."*".$row['BOOKING_WITHOUT_ORDER_SALES']."*".$row['PROD_ID']."*".$row['DTLS_ID'];

		$data_arr[$pre_key]['QC_PASS_QTY']=$row['QC_PASS_QTY'];

		if ($row['WITHIN_GROUP']==1 && $row['BOOKING_WITHOUT_ORDER_SALES']==0) 
		{
			$booking_id_arr[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		}
		if ($row['WITHIN_GROUP']==1 && $row['BOOKING_WITHOUT_ORDER_SALES']==1) 
		{
			$non_order_booking_id_arr[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		}
		if($batchIdCheck[$row['BOOKING_ID']]=='')
		{
			$batchIdCheck[$row['BATCH_ID']]=$row['BATCH_ID'];
			array_push($batch_data_arr,$row['BATCH_ID']);
		}
		
			
	}
	// echo "<pre>";print_r($batch_data_arr);die;

	$all_booking_ids= array_chunk($booking_id_arr, 999);
	$booking_ids_cond=" and(";
	foreach($all_booking_ids as $booking_ids)
	{
		if($booking_ids_cond==" and(") $booking_ids_cond.=" id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or id in(". implode(',', $booking_ids).")";
	}
	$booking_ids_cond.=")";

	if(!empty($all_booking_ids))
	{
		//$sql_booking_query = "SELECT a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 $booking_ids_cond ";
		$booking_sql="SELECT booking_no, booking_type, is_short, company_id, po_break_down_id, item_category, fabric_source, job_no ,entry_form, is_approved
		from wo_booking_mst where booking_type in(1,4) $booking_ids_cond and is_short in(1,2) and status_active=1 and is_deleted=0";
	}
	//echo $booking_sql;
	$booking_type_result=sql_select($booking_sql);
	$booking_Arr=array();
	foreach($booking_type_result as $row) 
	{
		$booking_type_arr[$row[csf("booking_no")]]=$row[csf("booking_type")];
		$booking_is_short_arr[$row[csf("booking_no")]]=$row[csf("is_short")];

		$booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
        $booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
        $booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
        $booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
        $booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
        $booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
	}

	$noOfbooking = count($non_order_booking_id_arr);
    if ($noOfbooking>0) 
    {
        $bookingCondition = '';        
        if($db_type == 2 && $noOfbooking > 1000)
        {
            $bookingCondition = " and (";
            $bookingArrNew = array_chunk($non_order_booking_id_arr,999);
            foreach($bookingArrNew as $prod)
            {
                $bookingCondition.=" id in('".implode("','",$prod)."') or";
            }
            $bookingCondition = chop($bookingCondition,'or');
            $bookingCondition .= ")";
        }
        else
        {
            $bookingCondition=" and id in('".implode("','",$non_order_booking_id_arr)."')";
        }

		$booking_sql="SELECT booking_no, company_id, po_break_down_id, item_category, fabric_source, job_no , is_approved, is_short, booking_type
		from WO_NON_ORD_SAMP_BOOKING_MST where booking_type=4 $bookingCondition and status_active=1 and is_deleted=0";
    }
    // echo $booking_sql;die;
    $booking_sql_dataArr = sql_select($booking_sql);
    $non_order_booking_Arr=array();
    foreach($booking_sql_dataArr as $row)
    {
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
        $non_order_booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
    }

	
	// echo "SELECT a.company_id,a.id,a.booking_id, a.booking_no,b.batch_id,e.batch_no, b.body_part_id,d.detarmination_id,b.prod_id,d.gsm, d.dia_width, d.color as color_id,b.order_id ,sum(b.issue_qnty) as issue_qnty ,sum(b.no_of_roll) as no_of_roll,e.extention_no,e.working_company_id from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and a.entry_form=318 and b.batch_id = e.id and b.prod_id = d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name ".where_con_using_array($batch_data_arr,0,'b.batch_id')." group by a.company_id,a.id,a.booking_id,a.booking_no,b.batch_id,e.batch_no, b.body_part_id,d.detarmination_id,b.prod_id,d.gsm, d.dia_width, d.color,b.order_id,e.extention_no,e.working_company_id";
	$IssueQueryResult=sql_select("SELECT a.company_id,a.id,a.booking_id, a.booking_no,b.batch_id,e.batch_no, b.body_part_id,d.detarmination_id,b.prod_id,d.gsm, d.dia_width, d.color as color_id,b.order_id ,sum(b.issue_qnty) as issue_qnty ,sum(b.no_of_roll) as no_of_roll,e.extention_no,e.working_company_id from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master d, pro_batch_create_mst e where a.id=b.mst_id and a.entry_form=318 and b.batch_id = e.id and b.prod_id = d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name ".where_con_using_array($batch_data_arr,0,'b.batch_id')." group by a.company_id,a.id,a.booking_id,a.booking_no,b.batch_id,e.batch_no, b.body_part_id,d.detarmination_id,b.prod_id,d.gsm, d.dia_width, d.color,b.order_id,e.extention_no,e.working_company_id");

	$issuedataArr = array();
	foreach ($IssueQueryResult as $row) 
	{
		$pop_referance = $row[csf('company_id')]."__".$row[csf('id')]."__".$row[csf('order_id')]."__".$row[csf('batch_id')]."__".$row[csf('color_id')]."__".$row[csf('gsm')]."__".$row[csf('dia_width')]."__".$row[csf('detarmination_id')]."__".$row[csf('body_part_id')];

		$issuedataArr[$row[csf('booking_id')]][$row[csf('batch_id')]][$row[csf('prod_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
		$issuedataArr[$row[csf('booking_id')]][$row[csf('batch_id')]][$row[csf('prod_id')]]['no_of_roll']+=$row[csf('no_of_roll')];
		$issuedataArr[$row[csf('booking_id')]][$row[csf('batch_id')]][$row[csf('prod_id')]]['pop_ref']=$pop_referance;

	}

	ob_start();
	$width=1270;
	?>

	<table cellspacing="0"  width="<?= $width;?>">
		<tr class="form_caption">
	        <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold"><? echo $company_arr[$company_name]; ?></td>
	    </tr>
	    <tr class="form_caption">
	        <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">Daiy Finish Fabric Production QC Report FSO<br>
	        </b>
	        <?
			echo ($start_date == '0000-00-00' || $start_date == '' ? '' : ' From: '.change_date_format($start_date));echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : ' To: '.change_date_format($end_date));
	        ?> </b>
	        </td>
	    </tr>
    </table>

    <div style="width:<?= $width+20;?>px;">
    <table width="<?= $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">    
    <thead>  
        <tr style="font-size:12px;">
        	<th width="40">Sl</th>
            <th width="110">Company</th>
            <th width="110">Production ID</th>
            <th width="60">Production Date</th>
            <th width="100">Booking No</th>
            <th width="100">Sales Order No</th>
            <th width="100">Batch No</th>
            <th width="200">Fabric Description</th>
            <th width="60">F.GSM</th>
            <th width="60">Fabric Dia</th>
            <th width="80">Fabric Color</th>	
            <th  width="80">QC Pass Qty</th>
            <th  width="80">Roll Qty(Kg)</th>
            <th>No Of Roll</th>
        </tr>
    </thead>	
    </table>   
    </div>	
    <div style="width:<?= $width+18;?>px; max-height:350px; overflow-y:scroll; clear:both" id="scroll_body">	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" align="left"  id="tbl_list">  
		<?
			// Fabric Sales Order Entry
			$print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");
			$fReportId=explode(",",$print_report_format);
			$fReportId=$fReportId[0];
			// var_dump($fReportId);
        // Data show start-----------------------------------------------
        $i=1;$tot_pi_qty=0;
		foreach ($data_arr as $dtls_key => $rows) 
		{
			$data = explode("*", $dtls_key);
			$recv_number=$data[0];
			$receive_date=$data[1];
			$sales_booking_no=$data[2];
			$fso_no=$data[3];
			$batch_id=$data[4];
			$determination_id=$data[5];
			$gsm=$data[6];
			$dia=$data[7];
			$color_id=$data[8];
			$sales_booking_id=$data[9];
			$booking_entry_form=$data[10];
			$within_group=$data[11];
			$booking_without_order_sales=$data[12];
			$prod_id=$data[13];
			 $dtls_id=$data[14];
			//var_dump($roll_dtls_arr[$roll_id]);

			$dtls_id=array_unique(explode(",",$dtls_id));
			$dtls_ids = '';
			foreach($dtls_id as $val)
			{
				if($dtls_ids=='') $dtls_ids=$roll_dtls_arr[$val]; else $dtls_ids.=','.$roll_dtls_arr[$val];
			}

			
			$fabric_desc=$fabric_desc_arr[$determination_id];

			$issue_qnty = $issuedataArr[$sales_booking_id][$batch_id][$prod_id]['issue_qnty'];
			$no_of_roll = $issuedataArr[$sales_booking_id][$batch_id][$prod_id]['no_of_roll'];
			$pop_ref    = $issuedataArr[$sales_booking_id][$batch_id][$prod_id]['pop_ref'];
			 $roll_id    = $issuedataArr[$sales_booking_id][$batch_id][$prod_id]['roll_id'];
			

			if ($within_group==1 && $booking_without_order_sales==0) 
			{
				$booking_company=$booking_Arr[$sales_booking_no]['booking_company_id'];
				$booking_order_id=$booking_Arr[$sales_booking_no]['booking_order_id'];
				$booking_fabric_natu=$booking_Arr[$sales_booking_no]['booking_fabric_natu'];
				$booking_fabric_source=$booking_Arr[$sales_booking_no]['booking_fabric_source'];
				$booking_job_no=$booking_Arr[$sales_booking_no]['booking_job_no'];
				$is_approved_id=$booking_Arr[$sales_booking_no]['is_approved'];
			}
			elseif ($within_group==1 && $booking_without_order_sales==1) 
			{
				$booking_company=$non_order_booking_Arr[$sales_booking_no]['booking_company_id'];
				$booking_order_id=$non_order_booking_Arr[$sales_booking_no]['booking_order_id'];
				$booking_fabric_natu=$non_order_booking_Arr[$sales_booking_no]['booking_fabric_natu'];
				$booking_fabric_source=$non_order_booking_Arr[$sales_booking_no]['booking_fabric_source'];
				$booking_job_no=$non_order_booking_Arr[$sales_booking_no]['booking_job_no'];
				$is_approved_id=$non_order_booking_Arr[$sales_booking_no]['is_approved'];
			}

			if ($booking_company!="") 
			{
				// Budget Wise Fabric Booking and Main Fabric Booking V2
				$print_report_format2=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
				$fReportId2=explode(",",$print_report_format2);
				$fReportId2=$fReportId2[0];

				// Short Fabric Booking
				$print_report_format3=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
				$fReportId3=explode(",",$print_report_format3);
				$fReportId3=$fReportId3[0];

				// Sample with order
				$print_report_format4=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
				$fReportId4=explode(",",$print_report_format4);
				$fReportId4=$fReportId4[0];

				// Sample without order
				$print_report_format5=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
				$fReportId5=explode(",",$print_report_format5);
				$fReportId5=$fReportId5[0];
				// echo "SELECT format_id FROM lib_report_template WHERE template_name =$booking_company  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1";
			}

			$sale_booking_no_sm_smn=explode('-', $sales_booking_no);
			$sale_booking_no_sm_smn[1];
			
			$fbReportId=0;
			if ($booking_entry_form==86 || $booking_entry_form==118) 
			{// Budget Wise Fabric Booking and Main Fabric Booking V2
				$fbReportId=$fReportId2;
			}
			else if($booking_entry_form==88)
			{
				$fbReportId=$fReportId3;// Short Fabric Booking
			}
			else if($sale_booking_no_sm_smn[1]=='SM')
			{
				$fbReportId=$fReportId4;// Sample with order
				$booking_entry_form='SM';
			}
			else if($sale_booking_no_sm_smn[1]=='SMN')
			{
				$fbReportId=$fReportId5;// Sample without order
				$booking_entry_form='SMN';
			}


			$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
				<td width="40" style="vertical-align: middle;" align="center"><?= $i;?></td>
                <td width="110" style="vertical-align: middle;" align="center"><?= $company_arr[$company_name];?></td>
              
				<td width="100" style="vertical-align: middle;" align="center"><?
				
				
				echo "<a href='##' onclick=\"generate_report_production('" .$dtls_ids. "')\">$recv_number</a>"; 
				?></td>

                <td width="60" style="vertical-align: middle;" align="center"><p><?= change_date_format($receive_date);?></p></td>	

				<td width="100" style="vertical-align: middle;" align="center"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sales_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sales_booking_no</a>"; ?>&nbsp;</p>
			</td>
               
			

				<td width="100" style="vertical-align: middle;" align="center"><?= "<a href='##' onclick=\"generate_report(" . $company_name . ",'" . $sales_booking_no . "','" . $sales_booking_no . "','" . $fso_no . "','" . $fReportId . "' )\">$fso_no</a>"; ?></td>
               
				<? $report_title='BATCH CARD'; $data=$company_name.'*'.$batch_id.'*'.$txt_batch_sl_no.'*'.$batch_arr[$batch_id].'*'.$batch_ext_arr[$batch_id].'*'.$report_title.'*'.$sales_booking_id.'*'.$batch_wc_arr[$batch_id].'*1';

					echo "<td width='100' style='vertical-align: middle;' align='center'><p><a href='../../production/requires/batch_creation_controller.php?data=".$data."&action=batch_card_prog_wise' target='_blank'> ".$batch_arr[$batch_id]." </a></p></td>";
				?>
				
			
                <td width="200" style="vertical-align: middle;" align="center" title="<?=$prod_id;?>"><?= $fabric_desc;?></td>
                <td width="60" style="vertical-align: middle;" align="center"><?= $gsm;?></td>
                <td width="60" style="vertical-align: middle;" align="center"><?= $dia;?></td>
                <td width="80" style="vertical-align: middle;" align="center"><?= $color_library[$color_id];?></td>
                <td style="vertical-align: middle;" align="right"><?= number_format($rows['QC_PASS_QTY'],2,'.','');?></td>
                <td style="vertical-align: middle;" align="right"><?= number_format($issue_qnty,2,'.','');?></td>
				<td  style="vertical-align: middle;" align="right"><p><a href="##" onClick="openmypage_roll_qnty('<? echo $pop_ref;?>','roll_qnty_popup')"><?  echo number_format($no_of_roll,2,".","");?></a></p></td>
            </tr>
			<?
			$tot_qc_pass_qty+=$rows['QC_PASS_QTY'];						    
			$tot_issue_qnty+=$issue_qnty;						    
			$tot_no_of_roll+=$no_of_roll;						    
		    $i++;
		}
        ?>
    </table>

    <!-- foot start -->
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="report_table_footer">
		<tfoot>
			<th width="40"></th>
			<th width="110"></th>
            <th width="110"></th>
            <th width="60"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="200"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="80" align="right">Total:</th>	
            <th align="right"><strong><?= number_format($tot_qc_pass_qty,2,'.',''); ?></strong></th>
			<th width="80"><strong><?= number_format($tot_issue_qnty,2,'.',''); ?></strong></th>
			<th width="80"><strong><?= number_format($tot_no_of_roll,2,'.',''); ?></strong></th>
		</tfoot>
	</table>
	<!-- foot End-->

    </div>
    <!-- Data show End- -->
    <?
	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html####$filename";
	
	exit();	
}

if($action=="roll_qnty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$ref_data_arr =  explode("__", $ref_data);
	$company_id = $ref_data_arr[0];
	$issue_id = $ref_data_arr[1];
	$order_id = $ref_data_arr[2];
	$batch_id = $ref_data_arr[3];
	$color_id = $ref_data_arr[4];
	$gsm = $ref_data_arr[5];
	$dia_width = $ref_data_arr[6];
	$detarmination_id = $ref_data_arr[7];
	$body_part_id = $ref_data_arr[8];

	//$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			$(".flt").css("display","none");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
		}
		var tableFilters =
		{
			col_operation: {
				id: ["value_total_qty","value_total_rej","value_total_loss","value_total_gused"],
				col: [7,8.9,10],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}

	</script>
	<fieldset style="width:910px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Roll Wise Details Info</b>
				</caption>
				<thead>
					<th width="40">SL</th>
					<th width="120">Barcode No</th>
					<th width="60">Roll No</th>
					<th width="120">Batch No</th>
					<th width="100">Body Part</th>
					<th width="100">Construction</th>
					<th width="100">Composition</th>
					<th width="60">Roll Qty.</th>
					<th width="60">Reject Qty.</th>
					<th width="50">Process Loss</th>
					<th>Grey Wgt.</th>
				</thead>
			</table>
			<div style="width:918px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="table_body">
					<tbody>
						<?

						$composition_arr=array(); $constructtion_arr=array();
						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
						$data_array=sql_select($sql_deter);
						foreach( $data_array as $row )
						{
							$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
							$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
						}
			
						$sql_data=sql_select("SELECT a.id as roll_table_id, a.barcode_no,a.roll_no,b.batch_id,e.batch_no, b.body_part_id, d.detarmination_id, a.qnty ,a.reject_qnty , a.dtls_id, b.trans_id, a.roll_id, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.trans_id,b.prod_id,d.gsm, d.dia_width, d.color as color_id,b.floor, b.room, b.rack_no, b.shelf_no, b.width_type, c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, null as recv_number from inv_issue_master f, pro_roll_details a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e where f.id=b.mst_id and f.id=a.mst_id and a.dtls_id=b.id and a.entry_form=318 and a.po_breakdown_id = c.id and b.batch_id = e.id and b.prod_id = d.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$issue_id and f.company_id=$company_id and f.id=$issue_id and b.order_id=$order_id and b.batch_id=$batch_id and d.color=$color_id and d.gsm='$gsm' and d.dia_width='$dia_width' and d.detarmination_id=$detarmination_id and b.body_part_id=$body_part_id and a.is_returned!=1");	

						$barcode_NOs="";
						foreach($sql_data as $row)
						{
							$barcode_NOs.=$row[csf("barcode_no")].",";

						}
						$barcode_Nos_all=rtrim($barcode_NOs,","); 
						$barcode_Nos_alls=explode(",",$barcode_Nos_all);
						$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
						$barcode_no_conds=" and";
						foreach($barcode_Nos_alls as $dtls_id)
						{
							if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
						}
						$barcode_no_conds.=")";
						$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
						FROM pro_roll_details a 
						WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
						$production_data_arr=array();
						foreach($production_sql_data as $value)
						{
							$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
							$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
							$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
						}



						$i=1;
						foreach($sql_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$prod_qty=$production_data_arr[$row[csf('barcode_no')]]['prod_qty'];
							$qc_pass_qnty=$production_data_arr[$row[csf('barcode_no')]]['qc_pass_qnty'];
							$reject_qnty=$production_data_arr[$row[csf('barcode_no')]]['reject_qnty'];
							$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));
							$grey_used=$prod_qty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i;?></td>
								<td width="120"><p><? echo $row[csf('barcode_no')]; ?></p></td>
								<td width="60"><? echo $row[csf('roll_no')]; ?></td>
								<td width="120"><? echo $row[csf('batch_no')]; ?></td>
								<td width="100"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
								<td width="100"><? echo $constructtion_arr[$row[csf('detarmination_id')]];?></td>
								<td width="100"><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></td>
								<td width="60" align="right"><? echo  number_format($row[csf('qnty')],2,'.','');?></td>
								<td width="60" align="right"><? echo  number_format($reject_qnty,2,'.','');?></td>
								<td width="50" align="right"><? echo  number_format($processLoss,2,'.','');?></td>
								<td align="right"><? echo number_format($grey_used,2,'.','');?></td>
							</tr>

							<?
							$totalQnt+=$row[csf('qnty')];
							$totalRej+=$reject_qnty;
							$totalLoss+=$processLoss;
							$totalGused+=$grey_used;

							$i++;
						}
						?>
					</tbody>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40"></th>
					<th width="120"></th>
					<th width="60"></th>
					<th width="120"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="60" id="value_total_qty" align="right"><? echo  number_format($totalQnt,2,'.','');?></th>
					<th width="60" id="value_total_rej" align="right"><? echo  number_format($totalRej,2,'.','');?></th>
					<th width="50" id="value_total_loss" align="right"><? echo  number_format($totalLoss,2,'.','');?></th>
					<th id="value_total_gused" align="right"><? echo  number_format($totalGused,2,'.','');?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<!-- <script>setFilterGrid('table_body',-1,tableFilters);</script> -->
	<?
	exit();
}

?>
      
 