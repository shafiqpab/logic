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
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'batch_follow_up_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'batch_follow_up_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'batch_follow_up_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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

	if($cbo_within_group != 0)
	{
		$within_group_cond = " and a.within_group = $cbo_within_group ";
	}

	if($db_type==0)
	{
		$year_cond=" and YEAR(b.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year_selection";
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
			$str_cond_insert = " and b.batch_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$str_cond_insert = " and b.batch_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} else {
		$str_cond_insert = "";
	}
	if($hide_job_id == ""){
		$sales_order_cond = ($sales_job_no != "") ? " and a.job_no_prefix_num=$sales_job_no" : "";
	}else{
		$sales_order_cond = " and a.id in($hide_job_id)";
	}
	if($hide_booking_id == ""){
		$sales_booking_cond = ($sales_booking_no != "") ? " and a.sales_booking_no like '%$sales_booking_no%'" : "";
	}else{
		$sales_booking_cond = " and a.sales_booking_no='$sales_booking_no'";
	}
	if($hdn_batch_no == ""){
		$batch_cond = ($txt_batch_no != "") ? " and b.batch_no='$txt_batch_no'" : "";
	}else{
		$batch_cond = " and b.id in($hdn_batch_no)";
	}
	if($hdn_batch_no == ""){
		$batch_cond = ($txt_batch_no != "") ? " and b.batch_no='$txt_batch_no'" : "";
	}else{
		$batch_cond = " and b.id in($hdn_batch_no)";
	}
	$buyer_cond = ($buyer_name != 0) ? " and a.po_buyer=$buyer_name" : "";

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$con = connect();
    $r_id=execute_query("delete from tmp_booking_id where userid=$user_name");
    oci_commit($con);

	$main_sql="SELECT A.ID AS FSO_ID, A.WITHIN_GROUP, A.BUYER_ID, A.JOB_NO AS FSO_NO, A.STYLE_REF_NO, A.PO_BUYER, A.SALES_BOOKING_NO, A.BOOKING_ID, A.BOOKING_ENTRY_FORM, B.ID AS BATCH_ID, B.BATCH_NO, B.WORKING_COMPANY_ID, B.BATCH_SL_NO, B.BATCH_DATE, B.INSERT_DATE, B.EXTENTION_NO, B.COLOR_RANGE_ID, B.COLOR_ID, C.BODY_PART_ID, C.PROD_ID, C.ITEM_DESCRIPTION, SUM(C.BATCH_QNTY) AS BATCH_QNTY, D.DETARMINATION_ID, D.PRODUCT_NAME_DETAILS
	FROM fabric_sales_order_mst a, pro_batch_create_mst b, pro_batch_create_dtls c, PRODUCT_DETAILS_MASTER d
	WHERE a.id=b.sales_order_id and b.id=c.mst_id and a.id=c.po_id and C.PROD_ID=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_sales=1 and a.company_id = $company_name
	$sales_order_cond $year_cond $batch_cond $within_group_cond $sales_booking_cond $buyer_cond $str_cond_insert
	GROUP BY A.ID, A.WITHIN_GROUP, A.BUYER_ID, A.PO_BUYER, A.JOB_NO, A.STYLE_REF_NO, A.SALES_BOOKING_NO, A.BOOKING_ID, A.BOOKING_ENTRY_FORM, B.ID, B.BATCH_NO, B.WORKING_COMPANY_ID, B.BATCH_SL_NO, B.BATCH_DATE, B.INSERT_DATE, B.EXTENTION_NO, B.COLOR_RANGE_ID, B.COLOR_ID, C.BODY_PART_ID, C.PROD_ID, C.ITEM_DESCRIPTION, D.DETARMINATION_ID, D.PRODUCT_NAME_DETAILS ORDER BY B.ID, C.PROD_ID ASC";
	// echo $main_sql;
	$result_data = sql_select($main_sql);
	$data_arr = array();$booking_id_check =array();
	foreach ($result_data as $key => $row)
	{
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['FSO_ID']=$row['FSO_ID'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['FSO_NO']=$row['FSO_NO'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['PO_BUYER']=$row['PO_BUYER'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['SALES_BOOKING_NO']=$row['SALES_BOOKING_NO'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['WITHIN_GROUP']=$row['WITHIN_GROUP'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BOOKING_ID']=$row['BOOKING_ID'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BOOKING_ENTRY_FORM']=$row['BOOKING_ENTRY_FORM'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BATCH_NO']=$row['BATCH_NO'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['WORKING_COMPANY_ID']=$row['WORKING_COMPANY_ID'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BATCH_SL_NO']=$row['BATCH_SL_NO'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['EXTENTION_NO']=$row['EXTENTION_NO'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BATCH_DATE']=$row['BATCH_DATE'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['INSERT_DATE']=$row['INSERT_DATE'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['COLOR_RANGE_ID']=$row['COLOR_RANGE_ID'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BODY_PART_ID']=$row['BODY_PART_ID'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['COLOR_ID']=$row['COLOR_ID'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['ITEM_DESCRIPTION']=$row['ITEM_DESCRIPTION'];
		$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['BATCH_QNTY']+=$row['BATCH_QNTY'];
		if ($row['WITHIN_GROUP']==1)
		{
			$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['PARTY']=$company_arr[$row['BUYER_ID']];
		}
		else
		{
			$data_arr[$row['BATCH_ID']][$row['DETARMINATION_ID']]['PARTY']=$buyer_arr[$row['BUYER_ID']];
		}
		$batch_id_arr[$row['BATCH_ID']]=$row['BATCH_ID'];

        if( $booking_id_check[$row[csf('BOOKING_ID')]] == "" && $row['WITHIN_GROUP']==1)
        {
        	$booking_id_check[$row['BOOKING_ID']]=$row['BOOKING_ID'];
            $booking_id = $row['BOOKING_ID'];
            // echo "insert into tmp_booking_id (userid, booking_id) values ($user_name,$booking_id)";
            $r_id=execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_name,$booking_id)");
        }
	}
	// echo "<pre>";print_r($booking_idArr);die;
	oci_commit($con);

	$booking_sql="SELECT a.booking_no, a.company_id, a.po_break_down_id, a.item_category, a.fabric_source, a.job_no, a.entry_form, a.is_approved
	from wo_booking_mst a, tmp_booking_id b where a.id = b.booking_id and b.userid=$user_name  and a.booking_type in(1,4) and a.is_short in(1,2) and a.status_active=1 and a.is_deleted=0
	UNION ALL
	SELECT a.booking_no, a.company_id, null as po_break_down_id, a.item_category, a.fabric_source, a.job_no, null as entry_form, a.is_approved
	from wo_non_ord_samp_booking_mst a, tmp_booking_id b where a.id = b.booking_id and b.userid=$user_name
	and a.booking_type in(4) and a.status_active=1 and a.is_deleted=0";
	// echo $booking_sql; //and a.entry_form in(86,88,118)
    $booking_sql_dataArr = sql_select($booking_sql);
    $booking_Arr=array();
    foreach($booking_sql_dataArr as $row)
    {
        $booking_Arr[$row[csf('booking_no')]]['booking_company_id'] = $row[csf('company_id')];
        $booking_Arr[$row[csf('booking_no')]]['booking_entry_form'] = $row[csf('entry_form')];
        $booking_Arr[$row[csf('booking_no')]]['booking_order_id'] = $row[csf('po_break_down_id')];
        $booking_Arr[$row[csf('booking_no')]]['booking_fabric_natu'] = $row[csf('item_category')];
        $booking_Arr[$row[csf('booking_no')]]['booking_fabric_source'] = $row[csf('fabric_source')];
        $booking_Arr[$row[csf('booking_no')]]['booking_job_no'] = $row[csf('job_no')];
        $booking_Arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
    }

	$all_batch_ids = implode(",",array_filter(array_unique($batch_id_arr)));
	if($all_batch_ids=="") $all_batch_ids=0;
 	$batchCond = $all_batch_id_cond = $batchCond2 = $all_batch_id_cond2 = "";
 	$all_batch_id_arr=explode(",",$all_batch_ids);
	if($db_type==2 && count($all_batch_id_arr)>999)
	{
		$all_batch_id_chunk_arr=array_chunk($all_batch_id_arr,999) ;
		foreach($all_batch_id_chunk_arr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$batchCond.=" a.batch_id in($chunk_arr_value) or ";
			$batchCond2.=" b.batch_id in($chunk_arr_value) or ";
		}
		$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
		$all_batch_id_cond2.=" and (".chop($batchCond2,'or ').")";
	}
	else
	{
		$all_batch_id_cond=" and a.batch_id in($all_batch_ids)";
		$all_batch_id_cond2=" and b.batch_id in($all_batch_ids)";
	}
	// echo $all_batch_id_cond;die;

	// Finishing process
	$heat_setting_data_arr=array();$sliting_start_data_arr=array();$stenter_start_data_arr=array();
	$dry_start_data_arr=array();$special_end_data_arr=array();$compact_end_data_arr=array();
	$sql_data = sql_select("SELECT a.entry_form, a.load_unload_id, a.batch_ext_no, a.batch_no, a.batch_id, a.process_end_date as production_date, a.insert_date from pro_fab_subprocess a, pro_fab_subprocess_dtls b
	where a.id=b.mst_id and a.company_id=$company_name $all_batch_id_cond and a.status_active=1 and a.is_deleted=0 ");
	foreach ($sql_data as $row)
	{
		if ($row[csf("entry_form")] == 32) // Heat Setting
		{
			$heat_setting_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		}
		else if ($row[csf("entry_form")] == 35) // Dyeing Production
		{
			if ($row[csf("load_unload_id")] == 2)
			{
				$deying_start_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
			}
		}
		if ($row[csf("entry_form")] == 30) // Slitting/Squeezing
		{
			$sliting_start_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		}
		else if ($row[csf("entry_form")] == 48) // Stentering
		{
			$stenter_start_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		}
		else if ($row[csf("entry_form")] == 31) // Drying
		{
			$dry_start_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		}
		else if ($row[csf("entry_form")] == 34) // Special Finish
		{
			$special_end_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		}
		else if ($row[csf("entry_form")] == 33) // Compacting
		{
			$compact_end_data_arr[$row[csf("batch_id")]]["insert_date"] = $row[csf("insert_date")];
		}
	}
	// echo "<pre>";print_r($heat_setting_data_arr);die;

	// FINISH PRODUCTION
	$finish_sql="SELECT a.receive_date, b.batch_id, c.detarmination_id, c.product_name_details, a.entry_form, a.insert_date , sum(case when a.entry_form=66 then b.receive_qnty else 0 end) as qc_pass_qty
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, product_details_master c
	where a.id=b.mst_id and B.PROD_ID=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(66,317) and a.company_id=$company_name $all_batch_id_cond2
	group by a.receive_date, b.batch_id, a.entry_form, a.insert_date, c.detarmination_id, c.product_name_details";
	// echo $finish_sql;
	$finish_data = sql_select($finish_sql);
	$finish_data_arr = array();
	foreach ($finish_data as $key => $val)
	{
		if ($val[csf("entry_form")]==66)
		{
			$finish_data_arr[$val[csf("batch_id")]][$val[csf("detarmination_id")]]["receive_date"] = $val[csf("insert_date")];
			$finish_data_arr[$val[csf("batch_id")]][$val[csf("detarmination_id")]]["qc_pass_qty"] += $val[csf("qc_pass_qty")];
		}
		else
		{
			$finish_data_arr[$val[csf("batch_id")]][$val[csf("detarmination_id")]]["textile_recv_date"] = $val[csf("insert_date")];
		}

	}

	//Delv. To Store
	$fin_feb = sql_select("SELECT a.delevery_date, a.insert_date, b.batch_id from pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst a where a.company_id=$company_name and a.id=b.mst_id and a.entry_form=67 and b.status_active=1 and b.is_deleted=0 and b.batch_id!=0");
	$fin_feb_del_store_arr = array();
	foreach ($fin_feb as $row)
	{
		$fin_feb_del_store_arr[$row[csf("batch_id")]]["delevery_date"] = $row[csf("insert_date")];
	}

	ob_start();
	$width=1898;
	?>
	<style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}
    </style>
	<table cellspacing="0"  width="<?= $width;?>">
		<tr class="form_caption">
	        <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"><? echo $company_arr[$company_name]; ?></td>
	    </tr>
	    <tr class="form_caption">
	        <td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">Batch Follow Up Report<br>
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
            <th width="110">Batch No</th>
            <th width="110">Party</th>
            <th width="110">Buyer</th>
            <th width="60">Extention No</th>
            <th width="100">FSO No & Sty Ref</th>
            <th width="100">Booking No</th>
            <th width="100">Color Range</th>
            <th width="60">Body Part</th>
            <th width="60">Color</th>
            <th width="200">Fabric Description</th>
            <th width="80">Batch  Wgt.</th>

            <th width="62">Batch Create</th>
            <th width="62">Heat Setting</th>
            <th width="62">Dyeing</th>
            <th width="62">Slitting Squeezing</th>
            <th width="62">Stentering</th>
            <th width="62">Drying</th>
            <th width="62">Special Finish</th>
            <th width="70">Compacting</th>
            <th width="62">Finish</th>
            <th width="60">Finish Wgt.</th>
            <th width="62">Delv. To Store</th>
            <th>Recv. By Textile</th>
        </tr>
    </thead>
    </table>
    </div>
    <div style="width:<?= $width+18;?>px; max-height:350px; overflow-y:scroll; clear:both" id="scroll_body">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" align="left" id="tbl_list_dtls">
		<?
		// =========================Booking, color row_span start====================
		foreach ($data_arr as $batch_id_key => $batch_id_value)
		{
			// $batch_row_span=0;
			foreach ($batch_id_value as $deter_id_key => $rows)
			{
				// $batch_row_span++;
				$batch_row_span_arr[$batch_id_key]++;
				// $color_row_span_arr[$batch_id_key][$rows['SALES_BOOKING_NO']]++;
			}
			// $batch_row_span_arr[$batch_id_key]=$batch_row_span;
		}
		// print_r($body_part_row_span_arr);
        // Data show start-----------------------------------------------
        $i=1;
        $grand_tot_batch_qnty=$grand_tot_finish_wgt=0;
        //for rpt template
		$sql_rpt_tmplt = sql_select("select format_id, template_name, report_id from lib_report_template where module_id=2 and status_active=1 and is_deleted=0 and report_id in(1,2,3,4)");
		$rpt_tmplt_arr = array();
		foreach($sql_rpt_tmplt as $trow)
		{
			$exp_frmt = array();
			$exp_frmt = explode(",", $trow[csf('format_id')]);
			if($trow[csf('report_id')] == 1)
			{
				$rpt_tmplt_arr[$trow[csf('template_name')]][1] = $exp_frmt[0];
			}
			elseif($trow[csf('report_id')] == 2)
			{
				$rpt_tmplt_arr[$trow[csf('template_name')]][2] = $exp_frmt[0];
			}
			elseif ($trow[csf('report_id')] == 3)
			{
				$rpt_tmplt_arr[$trow[csf('template_name')]][3] = $exp_frmt[0];
			}
			elseif ($trow[csf('report_id')] == 4)
			{
				$rpt_tmplt_arr[$trow[csf('template_name')]][4] = $exp_frmt[0];
			}
		}
		foreach ($data_arr as $batch_id_key => $batch_id_value)
		{
			$b=1;
			$tot_batch_qnty=$tot_finish_wgt=0;
			foreach ($batch_id_value as $deter_id_key => $rows)
			{
				$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
				$batch_rowspan=$batch_row_span_arr[$batch_id_key];
				// $booking_rowspan=$booking_row_span_arr[$batch_id_key][$rows['SALES_BOOKING_NO']];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">

					<?
	                if($b==1)
					{
                        // $booking_entry_form = $rows["BOOKING_ENTRY_FORM"];
                        $sale_booking_no=$rows["SALES_BOOKING_NO"];
                        $booking=explode('-', $sale_booking_no);

                        $booking_company=$booking_Arr[$sale_booking_no]['booking_company_id'];
                        $booking_entry_form=$booking_Arr[$sale_booking_no]['booking_entry_form'];
                        $booking_order_id=$booking_Arr[$sale_booking_no]['booking_order_id'];
                        $booking_fabric_natu=$booking_Arr[$sale_booking_no]['booking_fabric_natu'];
                        $booking_fabric_source=$booking_Arr[$sale_booking_no]['booking_fabric_source'];
                        $booking_job_no=$booking_Arr[$sale_booking_no]['booking_job_no'];
                        $is_approved_id=$booking_Arr[$sale_booking_no]['is_approved'];

                        // Budget Wise Fabric Booking and Main Fabric Booking V2
                        $fReportId2 = $rpt_tmplt_arr[$booking_company][1];

                        // Short Fabric Booking
						$fReportId3 = $rpt_tmplt_arr[$booking_company][2];

						// Sample with order Booking
						$fReportId4 = $rpt_tmplt_arr[$booking_company][3];

						// Sample without order Booking
						$fReportId5 = $rpt_tmplt_arr[$booking_company][4];

                        if ($booking_entry_form==86 || $booking_entry_form==118)
                        {// Budget Wise Fabric Booking and Main Fabric Booking V2
                            $fbReportId=$fReportId2;
                        }
                        else if($booking_entry_form==88)
                        {
                            $fbReportId=$fReportId3;// Short Fabric Booking
                        }
                        else if($booking_entry_form=="" && $booking[1]=="SM")
                        {
                        	$booking_entry_form="SM";
                            $fbReportId=$fReportId4;// Sample with order Booking
                        }
                        else if($booking_entry_form=="" && $booking[1]=="SMN")
                        {
                        	$booking_entry_form="SMN";
                            $fbReportId=$fReportId5;// Sample without order Booking
                        }

						?>
						<td width="40" class="wrd_brk" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><?= $i;?></td>

						<?
						$report_title='BATCH CARD';
						$data=$company_name.'*'.$batch_id_key.'*'.$txt_batch_sl_no.'*'.$rows['BATCH_NO'].'*'.$rows['EXTENTION_NO'].'*'.$report_title.'*'.$rows['BOOKING_ID'].'*'.$rows['WORKING_COMPANY_ID'].'*1';
	                    $fso_and_style=$rows["FSO_NO"].'<br>'.$rows['STYLE_REF_NO'];

		                echo "<td width='110' class='wrd_brk' style='vertical-align: middle;' align='center' title='<? echo $batch_id_key;?>' rowspan='$batch_rowspan'><p><a href='../../production/requires/batch_creation_controller.php?data=".$data."&action=batch_card_prog_wise' target='_blank'> ".$rows['BATCH_NO']." </a></p></td>";
		                ?>

		                <td width="110" class="wrd_brk" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><?= $rows['PARTY'];?></td>
		                <td width="110" class="wrd_brk" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><?= $buyer_arr[$rows['PO_BUYER']];?></td>
		                <td width="60" class="wrd_brk" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><p><?= $rows['EXTENTION_NO'];?></p></td>

	                    <td width="100" class="wrd_brk" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><p><? echo "<a href='##' onclick=\"generate_fso_report($company_name, '".$rows["BOOKING_ID"]."','".$rows['SALES_BOOKING_NO']."','".$rows["FSO_NO"]."', '".$rows["FSO_ID"]."' )\">$fso_and_style</a>"; ?>&nbsp;</p></td>

	                    <? if ($rows['WITHIN_GROUP']==1)
	                    {
	                    	?>
		                    <td width="100" class="wrd_brk" title="Booking Entry Form:<? echo $booking_entry_form;?>" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>
		                    <?
	                    }
		            	else
		            	{
		            		?>
		            		<td width="100" class="wrd_brk" title="Within Group No" style="vertical-align: middle;" align="center" rowspan="<? echo $batch_rowspan;?>"><p><? echo $sale_booking_no; ?>&nbsp;</p></td>
		            		<?
		            	}
		                $i++;
	            	}
	                ?>
	                <td width="100" class="wrd_brk" style="vertical-align: middle;" align="center"><?= $color_range[$rows['COLOR_RANGE_ID']];?></td>
	                <td width="60" class="wrd_brk" style="vertical-align: middle;" align="center"><?= $body_part[$rows['BODY_PART_ID']];?></td>
	                <td width="60" class="wrd_brk" style="vertical-align: middle;" align="center"><?= $color_library[$rows['COLOR_ID']];?></td>
	                <td width="200" class="wrd_brk" style="vertical-align: middle;" align="center"><?= $rows['ITEM_DESCRIPTION'];?></td>
	                <td width="80" class="wrd_brk" style="vertical-align: middle;" align="right"><?= number_format($rows['BATCH_QNTY'],2,'.','');?></td>
	                <td width="60" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $rows['INSERT_DATE'];?></p></td>

	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $heat_setting_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $deying_start_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $sliting_start_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $stenter_start_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $dry_start_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $special_end_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="70" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $compact_end_data_arr[$batch_id_key]["insert_date"];?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $finish_data_arr[$batch_id_key][$deter_id_key]["receive_date"];?></p></td>
	                <td width="60" class="wrd_brk" style="vertical-align: middle;" align="right"><p><?= number_format($finish_data_arr[$batch_id_key][$deter_id_key]["qc_pass_qty"],2,'.','');?></p></td>
	                <td width="62" class="wrd_brk" style="vertical-align: middle;" align="center"><p><?= $fin_feb_del_store_arr[$batch_id_key]["delevery_date"];?></p></td>
	                <td class="wrd_brk" style="vertical-align: middle;" align="center"><?= $finish_data_arr[$batch_id_key][$deter_id_key]["textile_recv_date"];?></td>
	            </tr>
				<?
				$tot_batch_qnty+=$rows['BATCH_QNTY'];
				$tot_finish_wgt+=$finish_data_arr[$batch_id_key][$deter_id_key]["qc_pass_qty"];
			    $b++;
			}
			?>
			<tr class="tbl_bottom">
	            <td colspan="11" align="right">Total</td>
	            <td width="80" align="right"><strong><?= number_format($tot_batch_qnty,2,'.',''); ?></strong></td>
	            <td width="62"></td>
	            <td width="62"></td>
	            <td width="62"></td>
	            <td width="62"></td>
	            <td width="62"></td>
	            <td width="62"></td>
	            <td width="62"></td>
	            <td width="70"></td>
	            <td width="62"></td>
	            <td width="60" align="right"><strong><?= number_format($tot_finish_wgt,2,'.',''); ?></strong></td>
	            <td width="62"></td>
	            <td></th>
			</tr>
			<?
			$grand_tot_batch_qnty+=$tot_batch_qnty;
			$grand_tot_finish_wgt+=$tot_finish_wgt;
		}
        ?>
    </table>

    <!-- foot start -->
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="report_table_footer">
		<tfoot>
			<th width="40"></th>
			<th width="110"></th>
			<th width="110"></th>
            <th width="110"></th>
            <th width="60"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="60"></th>
            <th width="60"></th>
            <th width="200" align="right">Grand Total</th>
            <th width="80" align="right"><strong><?= number_format($grand_tot_batch_qnty,2,'.',''); ?></strong></th>
            <th width="62"></th>
            <th width="62"></th>
            <th width="62"></th>
            <th width="62"></th>
            <th width="62"></th>
            <th width="62"></th>
            <th width="62"></th>
            <th width="70"></th>
            <th width="62"></th>
            <th width="60" align="right"><strong><?= number_format($grand_tot_finish_wgt,2,'.',''); ?></strong></th>
            <th width="62"></th>
            <th></th>
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

?>
