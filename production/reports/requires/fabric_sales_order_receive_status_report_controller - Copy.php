<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_party_type")
{
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if ($data == 1) //Yes
	{
		echo create_drop_down("cbo_buyer_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active = 1 and comp.is_deleted = 0 ".$company_cond." order by comp.company_name", "id,company_name", 1, "-- Select Party--", "", "", 0, 0);
	}	
	else if ($data == 2) //No
	{
		echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$selected_company and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Party --", $selected, "");
	}
	//all
	else
	{
		echo create_drop_down("cbo_buyer_name", 120, $blank_array, "", 1, "-- Select Party--", $selected, "", 1);
	}
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
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'fabric_sales_order_receive_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'fabric_sales_order_receive_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
	$cbo_booking_type = str_replace("'", "", trim($cbo_booking_type));

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
			$date_cond = " and a.booking_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$date_cond = " and a.booking_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} else {
		$date_cond = "";
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

	if($cbo_within_group != 0)
	{
		$within_group_cond = " and a.within_group = $cbo_within_group ";
	}
	$buyer_cond = ($buyer_name != 0) ? " and a.buyer_id=$buyer_name" : "";

	$booking_type_cond='';
	if ($cbo_booking_type==1) // main
	{
		$booking_type_cond= " and a.booking_type=1 and a.booking_entry_form=118 and a.booking_without_order=0";
	}
	elseif ($cbo_booking_type==2) // Short
	{
		$booking_type_cond= " and a.booking_type=1 and a.booking_entry_form=88 and a.booking_without_order=0";
	}
	elseif ($cbo_booking_type==3) // Sample with order
	{
		$booking_type_cond= " and a.booking_type=4 and a.booking_without_order=0";
	}
	elseif ($cbo_booking_type==4)  // Sample without order
	{
		$booking_type_cond= " and a.booking_type=4 and a.booking_without_order=1";
	}

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$company_short_name_arr = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$main_sql="SELECT A.ID AS FSO_ID, A.JOB_NO AS FSO_NO, TO_CHAR(A.INSERT_DATE,'YYYY') AS YEAR, A.WITHIN_GROUP, A.SALES_ORDER_TYPE, A.IS_APPROVED, A.BOOKING_APPROVAL_DATE AS RECEIVE_DATE, A.BUYER_ID, A.PO_BUYER, A.SALES_BOOKING_NO, A.BOOKING_DATE, A.BOOKING_TYPE, A.STYLE_REF_NO, A.DELIVERY_DATE, A.BOOKING_ENTRY_FORM, SUM(B.GREY_QNTY_BY_UOM) AS BOOKING_QTY, B.CONS_UOM, SUM(B.GREY_QTY) AS GREY_QTY, SUM(B.FINISH_QTY) AS FINISH_QTY, B.PROCESS_LOSS, A.BOOKING_ID, A.BOOKING_WITHOUT_ORDER, A.IS_MASTER_PART_UPDATED, A.UPDATE_DATE
	FROM fabric_sales_order_mst a, fabric_sales_order_dtls b
	WHERE a.id=b.mst_id and a.entry_form=109 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id = $company_name $sales_order_cond $year_cond $within_group_cond $sales_booking_cond $buyer_cond $date_cond $booking_type_cond
	GROUP BY A.ID, A.JOB_NO, A.INSERT_DATE, a.WITHIN_GROUP, a.SALES_ORDER_TYPE, a.IS_APPROVED, a.BOOKING_APPROVAL_DATE, A.BUYER_ID, A.PO_BUYER, A.SALES_BOOKING_NO, A.BOOKING_DATE, A.BOOKING_TYPE, A.STYLE_REF_NO, A.DELIVERY_DATE, A.BOOKING_ENTRY_FORM, B.CONS_UOM, B.PROCESS_LOSS, a.BOOKING_ID, A.BOOKING_WITHOUT_ORDER, A.IS_MASTER_PART_UPDATED, A.UPDATE_DATE ORDER BY A.ID";
	// echo $main_sql;
	$result_data = sql_select($main_sql);
	$data_arr = array();
	foreach ($result_data as $key => $row) 
	{
		$data_arr[$row['FSO_ID']]['FSO_NO']=$row['FSO_NO'];
		$data_arr[$row['FSO_ID']]['BOOKING_WITHOUT_ORDER']=$row['BOOKING_WITHOUT_ORDER'];
		$data_arr[$row['FSO_ID']]['BOOKING_ENTRY_FORM']=$row['BOOKING_ENTRY_FORM'];
		$data_arr[$row['FSO_ID']]['YEAR']=$row['YEAR'];
		$data_arr[$row['FSO_ID']]['WITHIN_GROUP']=$row['WITHIN_GROUP'];
		$data_arr[$row['FSO_ID']]['SALES_ORDER_TYPE']=$row['SALES_ORDER_TYPE'];
		$data_arr[$row['FSO_ID']]['IS_APPROVED']=$row['IS_APPROVED'];
		$data_arr[$row['FSO_ID']]['RECEIVE_DATE']=$row['RECEIVE_DATE'];
		$data_arr[$row['FSO_ID']]['BUYER_ID']=$row['BUYER_ID'];
		$data_arr[$row['FSO_ID']]['PO_BUYER']=$row['PO_BUYER'];
		$data_arr[$row['FSO_ID']]['SALES_BOOKING_NO']=$row['SALES_BOOKING_NO'];
		$data_arr[$row['FSO_ID']]['BOOKING_DATE']=$row['BOOKING_DATE'];
		$data_arr[$row['FSO_ID']]['BOOKING_TYPE']=$row['BOOKING_TYPE'];
		$data_arr[$row['FSO_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$data_arr[$row['FSO_ID']]['DELIVERY_DATE']=$row['DELIVERY_DATE'];
		$data_arr[$row['FSO_ID']]['CONS_UOM']=$row['CONS_UOM'];
		$data_arr[$row['FSO_ID']]['IS_MASTER_PART_UPDATED']=$row['IS_MASTER_PART_UPDATED'];
		$data_arr[$row['FSO_ID']]['UPDATE_DATE']=$row['UPDATE_DATE'];
		$data_arr[$row['FSO_ID']]['BOOKING_QTY']+=$row['BOOKING_QTY'];
		$data_arr[$row['FSO_ID']]['GREY_QTY']+=$row['GREY_QTY'];
		$data_arr[$row['FSO_ID']]['FINISH_QTY']+=$row['FINISH_QTY'];

		if ($row['WITHIN_GROUP']==1) 
		{
			$data_arr[$row['FSO_ID']]['PARTY']=$company_short_name_arr[$row['BUYER_ID']];
		}
		else
		{
			$data_arr[$row['FSO_ID']]['PARTY']=$buyer_short_name_arr[$row['BUYER_ID']];
		}

		$fso_id_arr[$row['FSO_ID']]=$row['FSO_ID'];
		if ($row['WITHIN_GROUP']==1 && $row["BOOKING_WITHOUT_ORDER"]==0) 
		{
			$booking_id_arr[$row["BOOKING_ID"]]=$row["BOOKING_ID"];
		}
		if ($row['WITHIN_GROUP']==1 && $row["BOOKING_WITHOUT_ORDER"]==1) 
		{
			$non_order_booking_id_arr[$row["BOOKING_ID"]]=$row["BOOKING_ID"];
		}

		$booking_type='';
		/*if ($row["BOOKING_TYPE"]==1 && $row["BOOKING_ENTRY_FORM"]==118 && $row["BOOKING_WITHOUT_ORDER"]==0) // main
		{
			$booking_type= "Main";
		}*/
		if ($row["BOOKING_TYPE"]==1 && $row["BOOKING_ENTRY_FORM"]==88 && $row["BOOKING_WITHOUT_ORDER"]==0) // Short
		{
			$booking_type= "Short Fabric";
		}
		elseif ($row["BOOKING_TYPE"]==4 && $row["BOOKING_WITHOUT_ORDER"]==0) // Sample with order
		{
			$booking_type= "Sample With Order";
		}
		elseif ($row["BOOKING_TYPE"]==4 && $row["BOOKING_WITHOUT_ORDER"]==1)  // Sample without order
		{
			$booking_type= "Sample Without Order";
		}
		else
		{
			$booking_type= "Main Fabric";
		}

		$summary_data_arr[$booking_type]['BOOKING_QTY']+=$row['BOOKING_QTY'];
		$summary_data_arr[$booking_type]['GREY_QTY']+=$row['GREY_QTY'];
		$summary_data_arr[$booking_type]['FINISH_QTY']+=$row['FINISH_QTY'];
		$summary_data_arr[$booking_type]['CONS_UOM']=$row['CONS_UOM'];
		$summary_data_arr[$booking_type]['FSO_NO']=$row['FSO_NO'];
	}
	// echo "<pre>";print_r($data_arr);echo "</pre>";//die;

	$all_booking_ids= array_chunk($booking_id_arr, 999);
	$booking_ids_cond=" and(";
	foreach($all_booking_ids as $booking_ids)
	{
		if($booking_ids_cond==" and(") $booking_ids_cond.=" id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or id in(". implode(',', $booking_ids).")";
	}
	$booking_ids_cond.=")";

	$all_fso_ids = implode(",",array_filter(array_unique($fso_id_arr)));
	if($all_fso_ids=="") $all_fso_ids=0;
 	$fsoCond = $all_fso_id_cond = "";
 	$all_fso_id_arr=explode(",",$all_fso_ids);
	if($db_type==2 && count($all_fso_id_arr)>999)
	{
		$all_fso_id_chunk_arr=array_chunk($all_fso_id_arr,999) ;
		foreach($all_fso_id_chunk_arr as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$fsoCond.=" a.po_breakdown_id in($chunk_arr_value) or ";
		}
		$all_fso_id_cond.=" and (".chop($fsoCond,'or ').")";
	}
	else
	{
		$all_fso_id_cond=" and a.po_breakdown_id in($all_fso_ids)";
	}
	// echo $all_batch_id_cond;die;

	// Finish Fabric Roll Delivery To Garments
	$fin_deliv_sql="SELECT A.PO_BREAKDOWN_ID, SUM(A.QNTY) AS FIN_DELIV_QTY, C.BOOKING_TYPE, C.BOOKING_WITHOUT_ORDER
	from pro_roll_details a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c
	where a.dtls_id=b.id and a.po_breakdown_id=c.id and a.entry_form=318 $all_fso_id_cond and  a.is_sales = 1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_returned!=1 
	group by  A.PO_BREAKDOWN_ID, C.BOOKING_TYPE, C.BOOKING_WITHOUT_ORDER";
	// echo $fin_deliv_sql;
	$fin_deliv_data = sql_select($fin_deliv_sql);	
	$finish_data_arr = array(); $summary_fin_deliv_arr = array();
	foreach ($fin_deliv_data as $key => $val) 
	{
		$finish_data_arr[$val["PO_BREAKDOWN_ID"]]["FIN_DELIV_QTY"] += $val["FIN_DELIV_QTY"];
		
		$booking_type='';
		if ($val["BOOKING_TYPE"]==1 && $val["BOOKING_ENTRY_FORM"]==88 && $val["BOOKING_WITHOUT_ORDER"]==0) // Short
		{
			$booking_type= "Short Fabric";
		}
		elseif ($val["BOOKING_TYPE"]==4 && $val["BOOKING_WITHOUT_ORDER"]==0) // Sample with order
		{
			$booking_type= "Sample With Order";
		}
		elseif ($val["BOOKING_TYPE"]==4 && $val["BOOKING_WITHOUT_ORDER"]==1)  // Sample without order
		{
			$booking_type= "Sample Without Order";
		}
		else
		{
			$booking_type= "Main Fabric";
		}
		$summary_fin_deliv_arr[$booking_type]+= $val["FIN_DELIV_QTY"];
	}
	// echo "<pre>";print_r($summary_fin_deliv_arr);echo "</pre>";

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

	if(!empty($all_booking_ids))
	{
		//$sql_booking_query = "SELECT a.booking_type, a.booking_no, a.is_short from wo_booking_mst a where status_active=1 $booking_ids_cond ";
		$booking_sql="SELECT booking_no, booking_type, is_short, company_id, po_break_down_id, item_category, fabric_source, job_no ,entry_form, is_approved
		from wo_booking_mst where booking_type in(1,4) $booking_ids_cond and is_short in(1,2) and status_active=1 and is_deleted=0";
	}
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

	ob_start();
	$width=1450;
	$sWidth=590;
	?>

	<table cellspacing="0"  width="<?= $width;?>">
		<tr class="form_caption">
	        <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold"><? echo $company_arr[$company_name]; ?></td>
	    </tr>
	    <tr class="form_caption">
			<td align="center">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name"); 
				foreach ($nameArray as $result)
				{				 
					echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
					<? echo $result[csf('email')];?> <? echo $result[csf('website')];
				}
				?>
			</td>
		</tr>
	    <tr class="form_caption">
	        <td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">FSO ORDER [BOOKING] REPORT<br>
	        </b>
	        <?
			echo ($start_date == '0000-00-00' || $start_date == '' ? '' : ' Date: '.change_date_format($start_date));echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : ' TO: '.change_date_format($end_date));
	        ?> </b>
	        </td>
	    </tr>
    </table>

    <!-- Summary Start -->    
    <div id="summary_report_container" style="margin: 5px auto;">
	    <div style="width:<?= $sWidth+20;?>px;">
			<table width="<?= $sWidth;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
			        <tr style="font-size:12px;">
			        	<th width="40">Sl</th>
			            <th width="80">Booking Type</th>
			            <th width="80">Booking Qty.</th>
			            <th width="50">UOM</th>
			            <th width="80">Req. Grey Qty (Kg)</th>
			            <th width="80">Req. Finish Qty (Kg)</th>
			            <th width="80">Process loss %</th>
			            <th width="">Fin. Deliv. Gmts Qty.(Kg)</th>
			        </tr>
			    </thead>
			</table>
		</div>
		<div style="width:<?= $sWidth+18;?>px; max-height:250px; overflow-y:scroll; clear:both;" id="scroll_body2">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $sWidth;?>" class="rpt_table" align="left" id="tbl_list_dtls2">
			    <tbody>
			    	<?
			        $i=1;
			        $summary_tot_booking_qnty=$summary_tot_grey_qty=$summary_tot_finish_qty=$summary_tot_deliv_qty=0;
					foreach ($summary_data_arr as $booking_type_key => $rows) 
					{
						$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
						$process_loss=0;
						if ($rows['GREY_QTY']>0) {
							$process_loss=($rows['GREY_QTY']-$rows['FINISH_QTY'])/$rows['GREY_QTY']*100;
						}
						$summary_fin_deliv_gmts_qty=$summary_fin_deliv_arr[$booking_type_key];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr1_<?= $i; ?>" onClick="change_color('tr1_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
							<td width="40" align="center"><?= $i;?></td>
			                <td width="80" align="left"><p><?= $booking_type_key;?></p></td>
			                <td width="80" align="right"><p><?= number_format($rows['BOOKING_QTY'],2,'.','');?></p></td>
			                <td width="50" align="center"><p><?= $unit_of_measurement[$rows['CONS_UOM']];?></p></td>
			                <td width="80" align="right"><p><?= number_format($rows['GREY_QTY'],2,'.','');?></p></td>
			                <td width="80" align="right"><p><?= number_format($rows['FINISH_QTY'],2,'.','');?></p></td>
			                <td width="80" align="right"><p><?= number_format($process_loss,0,'.','').'%';?></p></td>
			                <td width="" align="right"><p><?= number_format($summary_fin_deliv_gmts_qty,2,'.','');?></p></td>
			            </tr>
						<?
						$summary_tot_booking_qnty+=$rows['BOOKING_QTY'];
						$summary_tot_grey_qty+=$rows['GREY_QTY'];
						$summary_tot_finish_qty+=$rows['FINISH_QTY'];
						$summary_tot_deliv_qty+=$summary_fin_deliv_gmts_qty;
					    $i++;
					}
	       	 		?>       	 		
			    </tbody>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $sWidth;?>" class="rpt_table" id="report_table_footer">
				<tfoot>
					<th width="40"></th>
					<th width="80" align="right">Total:</th>
		            <th width="80"><strong><?= number_format($summary_tot_booking_qnty,2,'.',''); ?></strong></th>
		            <th width="50"></th>
		            <th width="80"><strong><?= number_format($summary_tot_grey_qty,2,'.',''); ?></strong></th>
		            <th width="80"><strong><?= number_format($summary_tot_finish_qty,2,'.',''); ?></strong></th>
		            <th width="80"></th>
		            <th width=""><strong><?= number_format($summary_tot_deliv_qty,2,'.',''); ?></strong></th>
				</tfoot>
			</table>
		</div>
	</div>
	<br clear="all">		
	<!-- Summary End -->

    <!-- Details Part Start -->
    <div style="width:<?= $width+20;?>px;">
	    <table width="<?= $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">
		    <thead>
		        <tr style="font-size:12px;">
		        	<th width="40">Sl</th>
		        	<th width="60">Party</th>
		            <th width="100">Sales Order No</th>
		            <th width="50">Attached File</th>
		            <th width="40">Year</th>
		            <th width="50">Within Group</th>
		            <th width="60">Sales Order Type</th>
		            <th width="50">Approve Status</th>
		            <th width="60" title="Booking Date">Receive Date</th>
		            <th width="60">Last Update date</th>
		            <th width="80">Buyer</th>
		            <th width="100">Sales/ Booking No</th>
		            <th width="60" title="Receive Date">Sales Booking date</th>
		            <th width="60">Booking Type</th>
		            <th width="80">Style Ref.</th>
		            <th width="60">Delivery Date</th>
		            <th width="60">Booking Qty.</th>
		            <th width="40">UOM</th>
		            <th width="60">Req. Grey Qty (Kg)</th>
		            <th width="60">Req. Finish Qty (Kg)</th>
		            <th width="70">Process loss %</th>
		            <th width="60">Fin. Deliv. Gmts</th>
		            <th>Finish Fab. Available Stock</th>
		        </tr>
		    </thead>	
	    </table>   
    </div>	
    <div style="width:<?= $width+18;?>px; max-height:350px; overflow-y:scroll; clear:both" id="scroll_body">	
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" align="left" id="tbl_list_dtls">  
			<?
			// Fabric Sales Order Entry
		    $print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");
		    $fReportId=explode(",",$print_report_format);
		    $fReportId=$fReportId[0];
	        $i=1;
	        $tot_booking_qnty=$tot_grey_qty=$tot_finish_qty=$tot_deliv_qty=$tot_stock_qty=0;
			foreach ($data_arr as $fso_id_key => $rows) 
			{
				$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
				$approve_status = ($rows['IS_APPROVED']==1) ? "Yes" : "No";
				$booking_type_string='';
				if ($rows["BOOKING_WITHOUT_ORDER"] != 1) 
				{
					$booking_no = $rows['SALES_BOOKING_NO'];
					$booking_type_id=$booking_type_arr[$booking_no];
					if($booking_type_id==1)
					{
						$is_short=$booking_is_short_arr[$booking_no];
						if($is_short==1)  $booking_type_string="Short Fabric";
						else if($is_short==2)  $booking_type_string="Main Fabric";
					}				
					else if($booking_type_id==4) $booking_type_string="Sample Booking";
				}
				else if ($rows["BOOKING_WITHOUT_ORDER"] == 1) 
				{
					$booking_type_string="Sample Without Order";
				}
				$last_update_date="";
				if ($rows['IS_MASTER_PART_UPDATED']==1) 
				{
					$last_update_date=date('d-m-Y', strtotime($rows['UPDATE_DATE']));;
				}
				$process_loss=0;
				if ($rows['GREY_QTY']>0) {
					$process_loss=($rows['GREY_QTY']-$rows['FINISH_QTY'])/$rows['GREY_QTY']*100;
				}
				$fin_deliv_qty=$finish_data_arr[$fso_id_key]["FIN_DELIV_QTY"];
				$stock_qty=$rows['FINISH_QTY']-$fin_deliv_qty;

				$within_group=$rows['WITHIN_GROUP'];
				$sales_order_no=$rows["FSO_NO"];
				$sale_booking_no=$rows['SALES_BOOKING_NO'];
				$booking_entry_form = $rows[csf('BOOKING_ENTRY_FORM')];
				$sale_booking_no_sm_smn=explode('-', $sale_booking_no);
				$sale_booking_no_sm_smn[1];

				if ($rows['WITHIN_GROUP']==1 && $rows[csf('BOOKING_WITHOUT_ORDER')]==0) 
				{
					$booking_company=$booking_Arr[$sale_booking_no]['booking_company_id'];
	                $booking_order_id=$booking_Arr[$sale_booking_no]['booking_order_id'];
	                $booking_fabric_natu=$booking_Arr[$sale_booking_no]['booking_fabric_natu'];
	                $booking_fabric_source=$booking_Arr[$sale_booking_no]['booking_fabric_source'];
	                $booking_job_no=$booking_Arr[$sale_booking_no]['booking_job_no'];
	                $is_approved_id=$booking_Arr[$sale_booking_no]['is_approved'];
				}
				elseif ($rows['WITHIN_GROUP']==1 && $rows[csf('BOOKING_WITHOUT_ORDER')]==1) 
				{
					$booking_company=$non_order_booking_Arr[$sale_booking_no]['booking_company_id'];
	                $booking_order_id=$non_order_booking_Arr[$sale_booking_no]['booking_order_id'];
	                $booking_fabric_natu=$non_order_booking_Arr[$sale_booking_no]['booking_fabric_natu'];
	                $booking_fabric_source=$non_order_booking_Arr[$sale_booking_no]['booking_fabric_source'];
	                $booking_job_no=$non_order_booking_Arr[$sale_booking_no]['booking_job_no'];
	                $is_approved_id=$non_order_booking_Arr[$sale_booking_no]['is_approved'];
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

	                // Partial Fabric Booking
	                $print_report_format6=return_field_value("format_id"," lib_report_template","template_name =$booking_company  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	                $fReportId6=explode(",",$print_report_format6);
	                $fReportId6=$fReportId6[0];
	                // echo "SELECT format_id FROM lib_report_template WHERE template_name =$booking_company  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1";
            	}
            	$fbReportId=0;
                if ($booking_entry_form==86 || $booking_entry_form==118) 
                {// Budget Wise Fabric Booking and Main Fabric Booking V2
                    $fbReportId=$fReportId2;
                }
                else if($booking_entry_form==88)
                {
                    $fbReportId=$fReportId3;// Short Fabric Booking
                }
                else if($booking_entry_form==108)
                {
                    $fbReportId=$fReportId6;// Partial Fabric Booking
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
                $img_val =  return_field_value("master_tble_id","common_photo_library","form_name='fabric_sales_order_entry' and master_tble_id='$fso_id_key'","master_tble_id");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
					<td width="40" align="center"><?= $i;?></td>
					<td width="60" align="center"><p><?= $rows['PARTY'];?></p></td>
	                <td width="100">
					<? echo "<a href='##' onclick=\"generate_report(" . $company_name . ",'" . $sale_booking_no . "','" . $sale_booking_no . "','" . $sales_order_no . "','" . $fReportId . "',$within_group )\">$sales_order_no</a>"; ?></td>
					<td width="50"><a href="javascript:void()" onClick="downloiadFile('<? echo $fso_id_key; ?>','<? echo $company_name; ?>');"><? if ($img_val != '') echo 'View File'; ?></a></td>
	                <td width="40" align="center"><p><?= $rows['YEAR'];?></p></td>
	                <td width="50" align="center"><p><?= $yes_no[$rows['WITHIN_GROUP']];?></p></td>
	                <td width="60" align="center"><?= $sales_order_type_arr[$rows['SALES_ORDER_TYPE']];?></td>
	                <td width="50" align="center"><?= $approve_status;?></td>
	                <td width="60" align="center"><?= change_date_format($rows['BOOKING_DATE']);?></td>	
	                <td width="60" align="center"><?= $last_update_date;?></td>
	                <td width="80" align="center"><?= $buyer_arr[$rows['PO_BUYER']]	;?></td>

	                <td width="100" title="<? echo $booking_company.'=='.$booking_entry_form;?>"><p><? echo "<a href='##' onclick=\"generate_booking_report('".$sale_booking_no."',".$booking_company.",'".$booking_order_id."',".$booking_fabric_natu.",".$booking_fabric_source.",".$is_approved_id.",'".$booking_job_no."','".$booking_entry_form."','".$fbReportId."' )\">$sale_booking_no</a>"; ?>&nbsp;</p></td>

	                <td width="60" align="center"><?= change_date_format($rows['RECEIVE_DATE']);?></td>
	                <td width="60" align="center"><p><?= $booking_type_string;?></p></td>
	                <td width="80" align="center"><p><?= $rows['STYLE_REF_NO'];?></p></td>
	                <td width="60" align="center"><p><?= change_date_format($rows['DELIVERY_DATE']);?></p></td>
	                <td width="60" align="right"><p><?= number_format($rows['BOOKING_QTY'],2,'.','');?></p></td>
	                <td width="40" align="center"><p><?= $unit_of_measurement[$rows['CONS_UOM']];?></p></td>
	                <td width="60" align="right"><p><?= number_format($rows['GREY_QTY'],2,'.','');?></p></td>
	                <td width="60" align="right"><p><?= number_format($rows['FINISH_QTY'],2,'.','');?></p></td>
	                <td width="70" align="right" title="(Req. Grey Qty - Req. Finish Qty)/Req. Grey Qty"><p><? echo number_format($process_loss,0,'.','').'%'; ?></p></td>
	                <td width="60" align="right"><p><?= number_format($fin_deliv_qty,2,'.','');?></p></td>
	                <td align="right" title="Req. Finish Qty (Kg)-Fin. Deliv. Gmts"><p><?= number_format($stock_qty,2,'.','');?></p></td>
	            </tr>
				<?
				$tot_booking_qnty+=$rows['BOOKING_QTY'];
				$tot_grey_qty+=$rows['GREY_QTY'];
				$tot_finish_qty+=$rows['FINISH_QTY'];
				$tot_deliv_qty+=$rows['deliv_qty'];
				$tot_stock_qty+=$rows['stock_qty'];
			    $i++;
			}
	        ?>
	    </table>
	    <!-- foot start -->
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="report_table_footer">
			<tfoot>
				<th width="40"></th>
				<th width="60"></th>
				<th width="100"></th>
				<th width="50"></th>
	            <th width="40"></th>
	            <th width="50"></th>
	            <th width="60"></th>
	            <th width="50"></th>
	            <th width="60"></th>
	            <th width="60"></th>
	            <th width="80"></th>
	            <th width="100"></th>
	            <th width="60"></th>
	            <th width="60"></th>
	            <th width="80"></th>
	            <th width="60" align="right">Total</th>
	            <th width="60" align="right" id="value_total_booking_qty"><strong><?= number_format($tot_booking_qnty,2,'.',''); ?></strong></th>
	            <th width="40"></th>
	            <th width="60" align="right" id="value_total_grey_qty"><strong><?= number_format($tot_grey_qty,2,'.',''); ?></strong></th>
	            <th width="60" align="right" id="value_total_finish_qty"><strong><?= number_format($tot_finish_qty,2,'.',''); ?></strong></th>
	            <th width="70"></th>
	            <th width="60" align="right" id="value_total_deliv_qty"><strong><?= number_format($tot_deliv_qty,2,'.',''); ?></strong></th>
	            <th align="right" id="value_total_stock_qty"><strong><?= number_format($tot_stock_qty,2,'.',''); ?></strong></th>
			</tfoot>
		</table>
		<!-- foot End-->
    </div>
    <!-- Details Part End -->
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

if($action=="fabric_sales_order_file")
{
	echo load_html_head_contents("File View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='fabric_sales_order_entry' and master_tble_id='$id'";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a target="_blank" href="../../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}
?>
      
 