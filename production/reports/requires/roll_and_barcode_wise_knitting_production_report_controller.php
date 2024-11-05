<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer") 
{
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}

if ($action == "sales_order_no_search_popup") 
{
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(job_no) {
			document.getElementById('hidden_job_no').value = job_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">

								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", $cbo_within_group, $dd, 0);
									?>
								</td>
								<td align="center">
									<?
									$serach_type_arr = array(1 => 'Sales Order No', 2 => 'Fab. Booking No');
									echo create_drop_down("cbo_serach_type", 150, $serach_type_arr, "", 0, "--Select--", "", "", 0);
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'roll_and_barcode_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
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

if ($action == "create_sales_order_no_search_list") 
{
	$data 			= explode('_', $data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($db_type == 0) {
		if ($yearID != 0) $year_cond = " and YEAR(a.insert_date)=$yearID";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($yearID != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$yearID";
		else $year_cond = "";
	}

	$within_group_cond  = ($within_group == 0) ? "" : " and a.within_group=$within_group";
	if ($serach_type == 1) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.job_no like '%$sales_order_no%'";
	} else if ($serach_type == 2) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.sales_booking_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2) ? "to_char(a.insert_date,'YYYY') as year" : "YEAR(a.insert_date) as year";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:950px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1) {
					$buyer = $company_arr[$row[csf('buyer_id')]];
				} else {
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="110" align="center">
						<p>&nbsp;<? echo $row[csf('job_no')]; ?></p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('sales_booking_no')]; ?></p>
					</td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center">
						<p><? echo $row[csf('style_ref_no')]; ?></p>
					</td>
					<td>
						<p><? echo $location_arr[$row[csf('location_id')]]; ?></p>
					</td>
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

if ($action == "booking_no_search_popup") 
{
	echo load_html_head_contents("Booking No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
	<script>
		var tableFilters = {
			col_11: "select",
			display_all_text: 'Show All'
		}

		function js_set_value(data) {
			$('#hidden_booking_data').val(data);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:98%;">
					<h3 align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
					<div id="content_search_panel">
						<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
							<thead>
								<th>Buyer</th>
								<th>Booking Date</th>
								<th>Search By</th>
								<th id="search_by_td_up" width="200">Please Enter Booking No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $companyID; ?>">
									<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" class="text_boxes" value="">
								</th>
							</thead>
							<tr>
								<td align="center">
									<?
									$user_wise_buyer = $_SESSION['logic_erp']['buyer_id'];
									$buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in ($user_wise_buyer) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
									echo create_drop_down("cbo_buyer", 150, $buyer_sql, "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
									?>
								</td>
								<td align="center">
									<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
									<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
								</td>
								<td align="center">
									<?
									$search_by_arr = array(1 => "Booking No", 2 => "Job No");
									$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value+'_'+'<? echo $cbo_booking_type; ?>', 'create_booking_search_list_view', 'search_div', 'roll_and_barcode_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
							</tr>
						</table>
					</div>
					<table width="100%" style="margin-top:5px">
						<tr>
							<td colspan="5">
								<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
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

if ($action == "create_booking_search_list_view") 
{
	$data = explode("_", $data);

	$search_string 	= "%" . trim($data[0]) . "%";
	$search_by 		= $data[1];
	$company_id 	= $data[2];
	$date_from 		= trim($data[3]);
	$date_to 		= trim($data[4]);
	$buyer_id 		= $data[5];
	$booking_type 		= $data[6];
	$buyer_arr 		= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	if ($unit_id == 0) {
		$unit_id_cond = "";
	} else {
		$unit_id_cond = " and a.company_id=$unit_id";
	}
	if ($buyer_id == 0) {
		$buyer_id_cond = $buyer_id_cond;
	} else {
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.booking_no like '$search_string'";
		else
			$search_field_cond = "and a.job_no like '$search_string'";
	}

	$date_cond = '';
	if ($date_from != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$import_booking_id_arr = return_library_array("select id, booking_id from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0", 'id', 'booking_id');

	$apporved_date_arr = return_library_array("select mst_id,max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id", 'mst_id', 'approved_date');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');

	$entry_form_cond = ($booking_type > 0) ? " and a.entry_form=$booking_type" : "";

	$sql = "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id,  a.remarks FROM wo_booking_mst a WHERE a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $entry_form_cond group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, a.remarks order by a.booking_date asc";
	//echo $sql;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="845" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="65">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="80">Booking Date</th>
			<th width="80">App. Date</th>
			<th width="80">Delivery Date</th>
			<th width="70">Currency</th>
			<th width="60">Approved</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:880px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="845" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$result = sql_select($sql);
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
				if ($row[csf('po_break_down_id')] != "") {
					$po_no = '';
					$po_ids = explode(",", $row[csf('po_break_down_id')]);
					foreach ($po_ids as $po_id) {
						if ($po_no == "") $po_no = $po_arr[$po_id];
						else $po_no .= "," . $po_arr[$po_id];
					}
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="65" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')] == 1) ? "Yes" : "No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
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

//--------------------------------------------------------------------------------------------------------------------
if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$txt_sales_order_no = str_replace("'", "", $txt_sales_order_no);
	$hdn_sales_order_no = str_replace("'", "", $hdn_sales_order_no);
	$txt_style_ref_no = str_replace("'", "", $txt_style_ref_no);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$txt_program_no = str_replace("'", "", $txt_program_no);
	$cbo_knitting_source = str_replace("'", "", $cbo_knitting_source);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);

    if($txt_date_from && $txt_date_to)
	{
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);

        $production_date_cond="and b.receive_date between '$date_from' and '$date_to'";
        $subConproduction_date_cond="and a.product_date between '$date_from' and '$date_to'";
      
    }
	

	if ($cbo_year != 0) {
		if ($db_type == 0) $year_cond = "and year(a.insert_date)='$cbo_year'";
		else if ($db_type == 2) $year_cond = "and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	//$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_details = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$operator_details = return_library_array("select id, first_name from lib_employee", "id", "first_name");
	
	$sql_cond = "";
	if($hdn_sales_order_no != "")
	{
		if ($txt_sales_order_no != "") $sql_cond .= " and a.job_no like '%$hdn_sales_order_no%'";
	}
	else
	{
		if ($txt_sales_order_no != "") $sql_cond .= " and a.job_no_prefix_num like '%$txt_sales_order_no%'";
	}	
	if ($txt_booking_no != "") $sql_cond .= " and a.sales_booking_no like '%$txt_booking_no%'";

	$program_no_cond = "";
	if ($txt_program_no != "") $program_no_cond = " and e.id='$txt_program_no'";
	if ($txt_program_no != "") $subConprogram_no_cond = " and a.program_no='$txt_program_no'";
	$style_ref_cond = "";
	if ($txt_style_ref_no != "") $style_ref_cond = " and a.style_ref_no='$txt_style_ref_no'";
	if ($txt_style_ref_no != "") $subConstyle_ref_cond = " and d.cust_style_ref='$txt_style_ref_no'";
	$knitting_source_cond = "";
	if ($cbo_knitting_source > 0) $knitting_source_cond = " and b.knitting_source='$cbo_knitting_source'";

     $sql="SELECT a.id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id, cast(c.fabric_desc as varchar2(2000)) as fabric_desc, cast(c.dia as varchar2(2000)) as dia , cast(e.fabric_dia as varchar2(2000)) as fabric_dia,c.gsm_weight, d.roll_no,d.id roll_id,d.roll_split_from, d.barcode_no,d.qnty as roll_weight, f.machine_dia, cast(e.stitch_length as varchar2(2000)) as stitch_length, f.machine_gg,e.color_range color_range_id,e.color_id, d.entry_form, 2 as basis, b.receive_date,b.company_id,b.knitting_company,b.knitting_source,b.buyer_id, f.yarn_lot,f.yarn_count,f.brand_id,f.shift_name,f.operator_name,e.id as program_no,e.machine_id,f.febric_description_id
 	from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_dtls e, pro_roll_details d ,inv_receive_master b,pro_grey_prod_entry_dtls f
 	where  a.id=c.po_id and c.dtls_id=e.id and c.dtls_id = b.booking_id and d.mst_id = b.id and b.id=f.mst_id and c.body_part_id=f.body_part_id and b.entry_form =2 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id in ($company_name) $sql_cond  $program_no_cond $style_ref_cond $production_date_cond $knitting_source_cond and d.is_sales=1 and d.entry_form in(2) 
 	group by a.id,a.company_id, a.style_ref_no, a.sales_booking_no, a.job_no, c.body_part_id,c.fabric_desc, c.dia,e.fabric_dia,c.gsm_weight, d.roll_no,d.id,d.roll_split_from, d.barcode_no, d.qnty, a.buyer_id,f.machine_dia, e.stitch_length,f.machine_gg,e.color_range,e.color_id, d.entry_form,b.receive_date,b.company_id,b.knitting_company,b.knitting_source,b.buyer_id, f.yarn_lot,f.yarn_count,f.brand_id,f.shift_name,f.operator_name,e.id,e.machine_id,f.febric_description_id";
 	//echo $sql;
 	$nameArray = sql_select($sql);

	$all_barcode=array();
	$programNoArr=array();
	$progNoChk=array();

	foreach ($nameArray as $row)
	{
		if($progNoChk[$row[csf('program_no')]] == "")
        {
            $progNoChk[$row[csf('program_no')]] = $row[csf('program_no')];
            array_push($programNoArr,$row[csf('program_no')]);
        }
	}

	// ======================In-Bound Subcontract sql Start =================================
	if(str_replace("'","",$cbo_knitting_source)==0 || str_replace("'","",$cbo_knitting_source)==2)
	{
		if($from_date!="" && $to_date!="") $date_con_sub=" and a.product_date between '$from_date' and '$to_date'";	else $date_con_sub="";
		$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
		//$yarn_const_comp_arr=return_library_array( "select id, yarn_description from lib_subcon_charge", "id", "yarn_description");
		//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst 
		$sql_inhouse_sub=" SELECT 999 as receive_basis, a.product_date as receive_date,a.program_no, null as booking_no, 999 as booking_type, 1 as is_order, null as entry_form,
		listagg((cast(b.cons_comp_id as varchar2(4000))),',') within group (order by b.cons_comp_id) as prod_id,
		listagg((cast(b.gsm as varchar2(4000))),',') within group (order by b.gsm) as gsm_weight,
		listagg((cast(b.dia_width as varchar2(4000))),',') within group (order by b.dia_width) as fabric_dia,
		listagg((cast(b.yarn_lot as varchar2(4000))),',') within group (order by b.yarn_lot) as yarn_lot,
		listagg((cast(b.yrn_count_id as varchar2(4000))),',') within group (order by b.yrn_count_id) as yarn_count,
		listagg((cast(b.stitch_len as varchar2(4000))),',') within group (order by b.stitch_len) as stitch_length,
		listagg((cast(b.brand as varchar2(4000))),',') within group (order by b.brand) as brand_id,
		listagg((cast(b.machine_dia as varchar2(4000))),',') within group (order by b.machine_dia) as machine_dia,
		listagg((cast(b.machine_gg as varchar2(4000))),',') within group (order by b.machine_gg) as machine_gg,
		b.machine_id,b.operator_name, 
		listagg((cast(nvl(b.color_id,0) as varchar2(4000))),',') within group (order by nvl(b.color_id,0)) as color_id,
		listagg((cast(b.color_range as varchar2(4000))),',') within group (order by b.color_range) as color_range_id, 
		listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as po_breakdown_id, 
		listagg((cast(d.order_no as varchar2(4000))),',') within group (order by d.order_no) as order_nos,d.cust_style_ref, d.job_no_mst as job_no, null as sales_booking_no,sum(b.reject_qnty) as reject_qty,0 as is_sales, a.party_id as unit_id,0 as within_group,  a.knitting_source, a.knitting_company,a.party_id as buyer_id,
		b.shift as shift_name,e.barcode_no,e.qnty as roll_weight,e.roll_no,a.company_id
		from subcon_production_mst a, subcon_production_dtls b, subcon_pro_roll_details e, lib_machine_name c, subcon_ord_dtls d 
		where a.id=b.mst_id and a.id=e.mst_id and b.id=e.dtls_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id  and a.product_type=2 and d.status_active=1 and d.is_deleted=0 
		and a.status_active=1 and a.is_deleted=0  and a.company_id in ($company_name) $sql_cond $subConprogram_no_cond $subConproduction_date_cond $subConstyle_ref_cond
		group by a.product_date,a.knitting_source,a.knitting_company,a.program_no,b.shift, b.machine_id,b.operator_name, d.job_no_mst,d.cust_style_ref, a.party_id,a.company_id,e.barcode_no,e.qnty,e.roll_no
		order by a.product_date, b.machine_id ";

		//echo $sql_inhouse_sub;die;
		$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
	}
	// ======================In-Bound Subcontract sql End =================================

	//====================================
	$req_sql = "SELECT a.id, a.knit_id, a.prod_id,b.lot, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_count_id, b.yarn_type, b.color from ppl_yarn_requisition_entry a, product_details_master b where a.prod_id=b.id and a.status_active = 1 and a.is_deleted = 0 ".where_con_using_array($programNoArr,0,'a.knit_id')." ";
	$req_rslt = sql_select($req_sql);

	$yarn_info_arr = array();
	if (count($req_rslt) > 0)
	{
		foreach ($req_rslt as $row) 
		{
			if ($row[csf('yarn_comp_percent2nd')] != 0) {
				$yarn_info_arr[$row[csf('knit_id')]]['yarn_composition'] = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} else {
				$yarn_info_arr[$row[csf('knit_id')]]['yarn_composition'] = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}
			$yarn_info_arr[$row[csf('knit_id')]]['yarn_type'] = $row[csf('yarn_type')];
		}
	}
	//echo "<pre>";print_r($yarn_info_arr);echo "</pre>";

	$composition_arr = $construction_arr = array();
	$sql_deter = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) 
	{
		foreach ($data_array as $row) 
		{
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}

			$construction_arr[$row[csf('id')]] = $row[csf('construction')];
			$yarn_type_arr[$row[csf('id')]] = $yarn_type[$row[csf('yarn_type')]];
		}
	}

	$div_width = 30+2700+20;
	$table_width = 30+2700;
	$coll_span = 15;

	ob_start();
	?>
	<style type="text/css">
		.font_yellow_color { color: #fff;}
	</style>
	<div style="width:<? echo $div_width; ?>px;">
		<fieldset style="width:<? echo $div_width; ?>px;">	
			<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
				<tr>
					<td align="center" width="100%" colspan="<? echo $coll_span; ?>" class="form_caption"><? echo $report_title; ?></td>
				</tr>
			</table>

			<table border="0" width="<? echo $table_width; ?>" align="left">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
			<!-- In-House Start -->
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Date</th>
						<th width="100">LC Company</th>
						<th width="100">Knitting Source</th>
						<th width="100">Knitting Company</th>
						<th width="100">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="100">Booking No</th>
						<th width="100">FSO No</th>
						<th width="100">Count</th>
						<th width="100">Yarn Composition</th>
						<th width="100">Yarn Type</th>
						<th width="100">Lot</th>
						<th width="100">Yarn Brand</th>
						<th width="100">Construction</th>
						<th width="100">Fabric Composition</th>
						<th width="100">Fabric Color</th>
						<th width="100">Program No.</th>
						<th width="100">Stitch Length</th>
						<th width="100">M/C No</th>
						<th width="100">M/C Dia & Guage</th>
						<th width="100">Shift</th>
						<th width="100">Fin. Dia</th>
						<th width="100">Fin. GSM</th>
						<th width="100">Roll No</th>
						<th width="100">Barcode</th>
						<th width="100">Roll Weight</th>
						<th >Operator</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table  cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
					class="rpt_table" id="table_body">
					<?
					$m = 1;
					$tot_roll_weight = 0;
					foreach ($nameArray as $row) 
					{
						if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						
						if ($row[csf('knitting_source')] == 1)
							$knitting_party = $company_arr[$row[csf('knitting_company')]];
						else if ($row[csf('knitting_source')] == 3)
							$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
						else
							$knitting_party = "&nbsp;";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
							<td width="30" align="center"><? echo $m; ?></td>
							<td width="100" align="center" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")])?>&nbsp;</td>
							<td width="100" style="word-break:break-all;"><? echo $company_arr[$row[csf("company_id")]]?>&nbsp;</td>
							<td width="100" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]];?>&nbsp;</td>
							<td width="100" align="center" style="word-break:break-all;"><? echo $knitting_party;?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $buyer_arr[$row[csf("buyer_id")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("style_ref_no")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("sales_booking_no")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("job_no")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $yarn_count_details[$row[csf("yarn_count")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $yarn_info_arr[$row[csf('program_no')]]['yarn_composition'];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $yarn_type[$yarn_info_arr[$row[csf('program_no')]]['yarn_type']];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("yarn_lot")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $construction_arr[$row[csf("febric_description_id")]];?> &nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $composition_arr[$row[csf("febric_description_id")]]?>&nbsp;</td>
                            <td width="100" align="right" style="word-break:break-all;"><? echo $color_details[$row[csf("color_id")]];?>&nbsp;</td>

                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("program_no")]?>&nbsp;</td>

                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("stitch_length")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $machine_details [$row[csf("machine_id")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("machine_dia")].'X'.$row[csf("machine_gg")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("fabric_dia")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("gsm_weight")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("roll_no")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("barcode_no")]?>&nbsp;</td>
                            <td width="100" align="right" style="word-break:break-all;"><? echo number_format($row[csf('roll_weight')],2); ?>&nbsp;</td>
                            <td  align="center" style="word-break:break-all;"><? echo $operator_details[$row[csf("operator_name")]];?>&nbsp;</td>
                        </tr>
                        <?
						$tot_roll_weight += $row[csf('roll_weight')];
                        $m++;
					}
					?>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" id="rpt_table_footer" align="left">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100"> &nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100"> &nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100"><b>Total : </b>&nbsp;</th>
					<th width="100" align="right"><? echo number_format($tot_roll_weight,2); ?>&nbsp;</th>
					<th >&nbsp;</th>
				</tfoot>
			</table>
			<!-- In-House End -->

			<!-- In-Bound Subcontract Start -->
			<table border="0" width="<? echo $table_width; ?>" align="left">
				<tr>
					<td><strong>In-Bound Subcontract</strong></td>
				</tr>
			</table>			
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Date</th>
						<th width="100">LC Company</th>
						<th width="100">Knitting Source</th>
						<th width="100">Knitting Company</th>
						<th width="100">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="100">Booking No</th>
						<th width="100">FSO No</th>
						<th width="100">Count</th>
						<th width="100">Yarn Composition</th>
						<th width="100">Yarn Type</th>
						<th width="100">Lot</th>
						<th width="100">Yarn Brand</th>
						<th width="100">Construction</th>
						<th width="100">Fabric Composition</th>
						<th width="100">Fabric Color</th>
						<th width="100">Program No.</th>
						<th width="100">Stitch Length</th>
						<th width="100">M/C No</th>
						<th width="100">M/C Dia & Guage</th>
						<th width="100">Shift</th>
						<th width="100">Fin. Dia</th>
						<th width="100">Fin. GSM</th>
						<th width="100">Roll No</th>
						<th width="100">Barcode</th>
						<th width="100">Roll Weight</th>
						<th >Operator</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $div_width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body2">
				<table  cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
					class="rpt_table" id="table_body2">
					<?
					$m = 1;
					$tot_roll_weight = 0;
					foreach ($nameArray_inhouse_subcon as $row) 
					{
						if ($m % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						
						if ($row[csf('knitting_source')] == 1)
							$knitting_party = $company_arr[$row[csf('knitting_company')]];
						else if ($row[csf('knitting_source')] == 3)
							$knitting_party = $supplier_arr[$row[csf('knitting_company')]];
						else
							$knitting_party = "&nbsp;";
						$order_nos=implode(',',array_unique(explode(",",$row[csf('order_nos')])));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="change_color('tr2_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $m; ?>">
							<td width="30" align="center"><? echo $m; ?></td>
							<td width="100" align="center" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")])?>&nbsp;</td>
							<td width="100" style="word-break:break-all;"><? echo $company_arr[$row[csf("company_id")]]?>&nbsp;</td>
							<td width="100" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]];?>&nbsp;</td>
							<td width="100" align="center" style="word-break:break-all;"><? echo $knitting_party;?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $buyer_arr[$row[csf("buyer_id")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("cust_style_ref")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $order_nos;?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("job_no")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $yarn_count_details[$row[csf("yarn_count")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;">&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;">&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("yarn_lot")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]];?>&nbsp;</td>
                            
                            <?
							$prod_id_arr=array_unique(explode(",",$row[csf('prod_id')]));
							$all_prod="";
							foreach($prod_id_arr as $id)
							{
								$all_prod.=$const_comp_arr[$id].", ";
							}
							$all_prod=chop($all_prod," , "); ?>

                            <td width="100" align="center" style="word-break:break-all;"><? echo $all_prod;?> &nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $all_prod;?>&nbsp;</td>

                            <td width="100" align="right" style="word-break:break-all;"><? echo $color_details[$row[csf("color_id")]];?>&nbsp;</td>

                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("program_no")]?>&nbsp;</td>

                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("stitch_length")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $machine_details [$row[csf("machine_id")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("machine_dia")].'X'.$row[csf("machine_gg")];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]];?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("fabric_dia")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("gsm_weight")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("roll_no")]?>&nbsp;</td>
                            <td width="100" align="center" style="word-break:break-all;"><? echo $row[csf("barcode_no")]?>&nbsp;</td>
                            <td width="100" align="right" style="word-break:break-all;"><? echo number_format($row[csf('roll_weight')],2); ?>&nbsp;</td>
                            <td  align="center" style="word-break:break-all;"><? echo $operator_details[$row[csf("operator_name")]];?>&nbsp;</td>
                        </tr>
                        <?
						$tot_roll_weight += $row[csf('roll_weight')];
                        $m++;
					}
					?>
				</table>
			</div>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width; ?>"
				class="rpt_table" id="rpt_table_footer" align="left">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100"> &nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100"> &nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100"><b>Total : </b>&nbsp;</th>
					<th width="100" align="right"><? echo number_format($tot_roll_weight,2); ?>&nbsp;</th>
					<th >&nbsp;</th>
				</tfoot>
			</table>
			<!-- In-Bound Subcontract End -->
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

?>