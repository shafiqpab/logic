<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];
$brand_arr = return_library_array("SELECT id, yarn_brand as brand_name from lib_yarn_brand where status_active=1 and is_deleted=0  ", 'id', 'brand_name');
if ($_SESSION['logic_erp']["buyer_id"] != "") {
	$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
} else {
	$buyer_id_cond = "";
}

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 162, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($data[0] == 1) {
			echo create_drop_down("cbo_buyer_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 1);
		} else if ($data[0] == 2) {
			echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "txt_season", 162, "select id,season_name from lib_buyer_season where buyer_id in($data)","id,season_name", 1, "-- Select Season --", $selected, "",0 );

	exit();
}

if ($action == "load_drop_down_cust_buyer") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) 
	{
		echo create_drop_down("cbo_cust_buyer_name", 162, $blank_array, "", 1, "--Select Buyer--", 0, "");
	}
	else  
	{
		echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_buyer_brand', 'cust_buyer_brand_td' );load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', this.value, 'load_drop_down_season', 'season_td' )", 0);
	}
	exit();
}

if ($action=="load_drop_down_buyer_brand")
{
	echo create_drop_down( "cbo_cust_buyer_brand", 162, "select id,brand_name from lib_buyer_brand where buyer_id in($data)","id,brand_name", 1, "-- Select Brand --", $selected, "",0 );

	exit();
}


if ($action == "load_drop_down_dealing_merchant") {
	$data = explode("_", $data);
	$team_leader = $data[1];

	if ($data[0] == 1) $disable = 1; else $disable = 0;

	echo create_drop_down("cbo_dealing_merchant", 162, "select id,team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and status_active =1 and is_deleted=0 order by team_member_name", "id,team_member_name", 1, "-- Select Team Member --", $selected, "", $disable);
	exit();
}
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=150 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#print1').hide();\n";
	echo "$('#print_2').hide();\n";
	echo "$('#print_5').hide();\n";
	echo "$('#print_6').hide();\n";
	echo "$('#print_3').hide();\n";



	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print1').show();\n";}
			if($id==116){echo "$('#print_2').show();\n";}
			if($id==137){echo "$('#print_5').show();\n";}
			if($id==72){echo "$('#print_6').show();\n";}
			if($id==85){echo "$('#print_3').show();\n";}
		}
	}
	else
	{
		echo "$('#print1').show();\n";
		echo "$('#print_2').show();\n";
		echo "$('#print_5').show();\n";
		echo "$('#print_6').show();\n";
		echo "$('#print_3').show();\n";
	}
	exit();
}
if ($action == "fabricDescription_popup") {
	echo load_html_head_contents("Fabric Description Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function js_set_value(id, comp, gsm, color_range) {
			$('#hidden_desc_id').val(id);
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			$('#hidden_color_range').val(color_range);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<form name="searchdescfrm" id="searchdescfrm">
		<fieldset style="width:720px;margin-left:10px">
			<?
			$composition_arr = array();
			$compositionData = sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
			foreach ($compositionData as $row) {
				$composition_arr[$row[csf('mst_id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
			}
			?>
			<input type="hidden" name="hidden_desc_id" id="hidden_desc_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">
			<input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value="">
			<input type="hidden" name="hidden_color_range" id="hidden_color_range" class="text_boxes" value="">

			<div style="margin-left:10px; margin-top:10px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
					<thead>
						<th width="50">SL</th>
						<th width="100">Fabric Nature</th>
						<th width="150">Construction</th>
						<th>Composition</th>
						<th width="100">GSM/Weight</th>
					</thead>
				</table>
				<div style="width:700px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680"
					id="tbl_list_search">
					<?
					$i = 1;
					$data_array = sql_select("select id, construction, fab_nature_id, gsm_weight, color_range_id from lib_yarn_count_determina_mst where fab_nature_id=2 and status_active=1 and is_deleted=0");
					foreach ($data_array as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						if ($row[csf('construction')] != "") {
							$comp = $row[csf('construction')] . ", ";
						}
						$comp .= $composition_arr[$row[csf('id')]];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $comp; ?>','<? echo $row[csf('gsm_weight')]; ?>','<? echo $row[csf('color_range_id')]; ?>')"
							style="cursor:pointer">
							<td width="50"><? echo $i; ?></td>
							<td width="100"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
							<td width="150"><p><? echo $row[csf('construction')]; ?></p></td>
							<td><p><? echo $comp; ?></p></td>
							<td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
	</fieldset>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "color_popup") {
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function js_set_value(id, color) {
			$('#hidden_color_id').val(id);
			$('#hidden_color_no').val(color);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<form name="searchdescfrm" id="searchdescfrm">
		<fieldset style="width:420px;margin-left:10px">
			<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_color_no" id="hidden_color_no" class="text_boxes" value="">

			<div style="margin-left:10px; margin-top:10px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="380">
					<thead>
						<th width="50">SL</th>
						<th>Color Name</th>
					</thead>
				</table>
				<div style="width:400px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="380"
					id="tbl_list_search">
					<?
					$i = 1;
					$data_array = sql_select("select id, color_name from lib_color where status_active=1 and is_deleted=0");
					foreach ($data_array as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('color_name')]; ?>')"
							style="cursor:pointer">
							<td width="50"><? echo $i; ?></td>
							<td><? echo $row[csf('color_name')]; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
	</fieldset>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "check_approvl_necessity_setup") {
	$data = explode("__", $data);
	$company_id=$data[2];
	$booking_type=$data[11];
	$is_short=$data[12];
	$booking_without_order=$data[19];
	if($booking_without_order==0)
	{
		if($booking_type==1 && $is_short==2){

			$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		}
		else if($booking_type==1 && $is_short==1)
		{
			$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		}

		else if($booking_type==4)
		{
			$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		}

		$allowed_partial=2;
		foreach($sql as $row){
			$allowed_partial=$row[csf('allow_partial')];
		}
	}
	else{

		$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");

		$allowed_partial=2;
		foreach($sql as $row){
			$allowed_partial=$row[csf('allow_partial')];
		}
	}
	echo $allowed_partial;
	exit();
}

if ($action == "check_approvl_necessity_setup_revised") {
	$data = explode("_", $data);
	$company_id=$data[0];
	$booking_type=$data[1];
	$is_short=$data[2];
	$booking_without_order=$data[3];

	if($booking_without_order==0)
	{

		if($booking_type==1 && $is_short==2){

			$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		}
		else if($booking_type==1 && $is_short==1)
		{
			$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		}

		else if($booking_type==4)
		{
			$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		}

		$allowed_partial=2;
		foreach($sql as $row){
			$allowed_partial=$row[csf('allow_partial')];
		}
	}
	else{

		$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$company_id and b.page_id=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");

		$allowed_partial=2;
		foreach($sql as $row){
			$allowed_partial=$row[csf('allow_partial')];
		}
	}
	echo $allowed_partial;
	exit();
}

if ($action == "fabricBooking_popup") {
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var tableFilters =
		{
			col_11: "select",
			display_all_text: 'Show All'
		}

		function js_set_value(data, is_approved) {
			if (is_approved == 1) {
				$('#hidden_booking_data').val(data);
				parent.emailwindow.hide();
			}
			if (is_approved == 3) {

				var response=return_global_ajax_value( data, 'check_approvl_necessity_setup', '', 'fabric_sales_order_entry_v2_controller');

				if(response==1){
					$('#hidden_booking_data').val(data);
					parent.emailwindow.hide();
				}
				else{
					alert("Approved Booking First.");
					return;
				}
			}
			else {
				alert("Approved Booking First.");
				return;
			}
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:98%;">
				<h3 align="left" id="accordion_h1" class="accordion_h"
				onClick="accordion_menu(this.id,'content_search_panel','')">-Enter search words</h3>
				<div id="content_search_panel">
					<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer</th>
							<th>Unit</th>
							<th>Booking Date</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="200">Please Enter Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
								value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" class="text_boxes"
								value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								$user_wise_buyer = $_SESSION['logic_erp']['buyer_id'];
								if($user_wise_buyer>0){$user_wise_buyer_cond="and buy.id in ($user_wise_buyer)";}else{$user_wise_buyer_cond="";}
								$buyer_sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $user_wise_buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
								echo create_drop_down("cbo_buyer", 150, $buyer_sql, "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- All Unit --", $selected, "", $data[0]);
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"
								style="width:70px" readonly>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Booking No", 2 => "Job No");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('cbo_year_selection').value, 'create_booking_search_list_view', 'search_div', 'fabric_sales_order_entry_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')"
								style="width:100px;"/>
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
							<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div"
							align="left"></div>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_booking_search_list_view") {
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$unit_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$buyer_id = $data[6];
	$year_selection = $data[7];


	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.booking_no_prefix_num=$search_string";
		} else if ($search_by == 2) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		}
	}

	$date_cond='';
	$year_cond="";
	if($date_from!="" && $date_to!="")
	{

		if($db_type==0)
		{
			$year_cond="";
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$year_cond="";
			$date_cond="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}else {

		if($db_type==0)
		{
			if($year_selection!="")
			{
				$year_cond=" and YEAR(a.booking_date)=$year_selection";
				$date_cond = "";
			}
		}
		else
		{
			if($year_selection!="")
			{
				$year_cond=" and to_char(a.booking_date,'YYYY')=$year_selection";
				$date_cond = "";
			}
		}
	}

	if($unit_id==0) $unit_id_cond=""; else $unit_id_cond=" and a.company_id=$unit_id";
	if($buyer_id==0) $buyer_id_cond= $buyer_id_cond; else $buyer_id_cond=" and a.buyer_id=$buyer_id";

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$apporved_date_arr = return_library_array("select mst_id,max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id", 'mst_id', 'approved_date');
	$cdate=date('d-M-Y');
	$allow_partial = return_field_value("b.allow_partial as allow_partial", "approval_setup_mst a,approval_setup_dtls b", "a.id=b.mst_id and b.status_active=1 and page_id=5 and a.company_id=$company_id and a.setup_date<='" . $cdate . "' order by a.setup_date DESC","allow_partial" );

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	if($db_type==0)
	{
		$po_break_down_id_cond=	"CAST(a.po_break_down_id as CHAR(4000)) as po_break_down_id,";
	}
	else
	{
		$po_break_down_id_cond=	"cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id,";
	}

	$sql = "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_cond b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise as season,a.remarks,a.attention,c.copmposition as composition,0 as booking_without_order
	FROM wo_booking_mst a, wo_po_details_master b, wo_booking_dtls c
	WHERE a.booking_no=c.booking_no and c.job_no = b.job_no and a.pay_mode=5 and a.fabric_source in (1,2,4) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond
	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise,a.remarks,a.attention,c.copmposition 
	union all
	select a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, null as entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_cond null as job_no,null as style_ref_no,null as team_leader,null as dealing_marchant, null as season,null as remarks,a.attention,b.composition,1 as booking_without_order from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.pay_mode=5 and (a.fabric_source in (1,2,4)  or b.fabric_source in(1,2,4) ) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id,a.attention,b.composition"; //and a.id not in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id and c.status_active=1 and c.is_deleted=0)
	//echo $sql;
	$result = sql_select($sql);
	$booking_id_arr=$po_id_arr=array();$bookingID="";$poID="";
	foreach ($result as $row) {
		$booking_id_arr[] = $row[csf("id")];
		$po_id_arr[] = $row[csf("po_break_down_id")];

		$bookingID.=$row[csf("id")].",";
		$poID.=$row[csf("po_break_down_id")].",";
	}

	$sales_booking=array();
	if(!empty($booking_id_arr)){
		$bookingID=rtrim($bookingID,",");
		$bookingID=explode(",",$bookingID);

		$bookingID=array_chunk($bookingID,999);
		$bookingID_cond=" and";$bookingID_cond2=" and";
		foreach($bookingID as $dtls_id)
		{
			if($bookingID_cond==" and")  $bookingID_cond.="(booking_id in(".implode(',',$dtls_id).")"; else $bookingID_cond.=" or booking_id in(".implode(',',$dtls_id).")";
			if($bookingID_cond2==" and")  $bookingID_cond2.="(b.id in(".implode(',',$dtls_id).")"; else $bookingID_cond2.=" or b.id in(".implode(',',$dtls_id).")";
		}
		$bookingID_cond.=")";
		$bookingID_cond2.=")";
		//echo $pi_qnty_cond;die;

		if ($db_type==2) {
			$check_booking_in_salesorder = sql_select("select id, booking_id,booking_without_order from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0 $bookingID_cond");
		}
		else
		{
			$check_booking_in_salesorder = sql_select("select id, booking_id,booking_without_order from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0 and booking_id in(".implode(",",$booking_id_arr).")");
		}
		foreach ($check_booking_in_salesorder as $sales_row) {
			$booking_without_order = ($sales_row[csf('booking_without_order')]==1)?1:0;
			$sales_booking[$sales_row[csf("booking_id")]][$booking_without_order] = $sales_row[csf("id")];
		}
	}

	if(!empty($po_id_arr)){
		$poID=rtrim($poID,",");
		$poID=explode(",",$poID);

		$poID=array_chunk($poID,999);
		$poID_cond=" and";
		foreach($poID as $dtls_id)
		{
			if($poID_cond==" and")  $poID_cond.="(id in(".implode(',',$dtls_id).")"; else $poID_cond.=" or id in(".implode(',',$dtls_id).")";
			if($bookingID_cond==" and")  $bookingID_cond.="(booking_id in(".implode(',',$dtls_id).")"; else $bookingID_cond.=" or booking_id in(".implode(',',$dtls_id).")";

		}
		$poID_cond.=")";
		//echo $pi_qnty_cond;die;

		if ($db_type==2) {
			$po_arr = return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 $poID_cond", 'id', 'po_number');
		}
		else
		{
			$po_arr = return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 and id in(".implode(",",$po_id_arr).")", 'id', 'po_number');
		}
	}

 $smn_booking="select distinct a.style_ref_no,b.booking_no from sample_development_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.id=c.style_id and b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and a.style_ref_no is not null $bookingID_cond2";
 $result_smn_booking = sql_select($smn_booking);
 foreach ($result_smn_booking as $row) {
	 $booking_style_arr[$row[csf('booking_no')]] = $row[csf('style_ref_no')];
 }
 //print_r($booking_style_arr);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="90">Job No</th>
			<th width="110">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th width="80">App. Date</th>
			<th width="80">Delivery Date</th>
			<th width="70">Currency</th>
			<th width="60">Approved</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:1080px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1045" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		//echo $sql;
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($sales_booking[$row[csf("id")]][$row[csf('booking_without_order')]]=="") {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('po_break_down_id')] != "") {
					$po_no = '';
					$po_ids = explode(",", $row[csf('po_break_down_id')]);
					foreach ($po_ids as $po_id) {
						if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
					}
				}
				if($row[csf('booking_without_order')]==1)
				{
				$row[csf('style_ref_no')]=$booking_style_arr[$row[csf('booking_no')]];
				}
				else $row[csf('style_ref_no')]=$row[csf('style_ref_no')];
		
				$data = $row[csf('id')] . '__' . $row[csf('booking_no')] . '__' . $row[csf('company_id')] . '__' . $row[csf('style_ref_no')] . '__' . change_date_format($row[csf('delivery_date')]) . '__' . $row[csf('currency_id')] . '__' . $row[csf('season')] . '__' . $row[csf('team_leader')] . '__' . $row[csf('dealing_marchant')] . '__' . $row[csf('remarks')] . '__' . $row[csf('is_approved')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('is_short')] . '__' . $row[csf('fabric_source')] . '__' . $row[csf('item_category')] . '__' . $row[csf('job_no')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('po_break_down_id')] . '__' . $row[csf('entry_form')] . '__' . $row[csf('booking_without_order')] . '__' . change_date_format($apporved_date_arr[$row[csf('id')]]). '__' . $row[csf('attention')] . '__' . $row[csf('composition')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80"
					align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')] == 1 || ($row[csf('is_approved')]==3 && $allow_partial==1)) ? "Yes" : "No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			}
		}
		if($db_type==0)
		{
			$po_break_down_id_str="group_concat(d.po_break_down_id)";
			
		}
		else if($db_type==2)
		{
			$po_break_down_id_str="listagg(d.po_break_down_id,',') within group (order by d.po_break_down_id) as po_break_down_id";
		}
		//partial booking...........................................................start;
		$partial_sql = "SELECT a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date,a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_str, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks,0 as booking_without_order FROM wo_booking_mst a, wo_po_details_master b,wo_booking_dtls d WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.entry_form=108 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond and a.id not in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id) group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.booking_date asc";
		$partial_result = sql_select($partial_sql);
		foreach ($partial_result as $row) {
			if (!in_array($row[csf('id')], $import_booking_id_arr)) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('po_break_down_id')] != "") {
					$po_no = '';
					$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
					foreach ($po_ids as $po_id) {
						if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
					}
				}
				$data = $row[csf('id')] . '__' . $row[csf('booking_no')] . '__' . $row[csf('company_id')] . '__' . $row[csf('style_ref_no')] . '__' . change_date_format($row[csf('delivery_date')]) . '__' . $row[csf('currency_id')] . '__' . $row[csf('season')] . '__' . $row[csf('team_leader')] . '__' . $row[csf('dealing_marchant')] . '__' . $row[csf('remarks')] . '__' . $row[csf('is_approved')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('is_short')] . '__' . $row[csf('fabric_source')] . '__' . $row[csf('item_category')] . '__' . $row[csf('job_no')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('po_break_down_id')] . '__' . $row[csf('entry_form')] . '__' . $row[csf('booking_without_order')] . '__' . change_date_format($apporved_date_arr[$row[csf('id')]]);
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center"><? echo ($row[csf('is_approved')] == 1 || ($row[csf('is_approved')]==3 && $allow_partial==1)) ? "Yes" : "No"; ?></td>
					<td style="word-break: break-all;"><? echo $po_no; ?></td>
				</tr>
				<?
				$i++;
			}
		}
		//partial booking...........................................................end;
		?>
	</table>
</div>
<?
exit();
}


if ($action == 'show_fabric_details') {
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$composition_arr = array();
	$colorRange_arr = array();
	$sql_deter = "select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$deter_array = sql_select($sql_deter);
	if (count($deter_array) > 0) {
		foreach ($deter_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
			$colorRange_arr[$row[csf('id')]] = $row[csf('color_range_id')];
		}
	}

	

	if($db_type==0)
	{
		$width_dia_type_cond="CAST(c.width_dia_type as CHAR(4000)) as width_dia_type,";
		$dia_width_cond = " and b.entry_form_id ='' and a.entry_form_id =''";
	}
	else
	{
		$width_dia_type_cond="cast(c.width_dia_type as varchar2(200)) width_dia_type,";
		$dia_width_cond = " and b.entry_form_id is null and a.entry_form_id is null";
	}

	/*$sql ="select c.id pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia_width,sum(b.adjust_qty) adjust_qty,c.body_part_id, c.color_type_id,c.width_dia_type, c.gsm_weight,b.pre_cost_remarks, c.lib_yarn_count_deter_id, b.fabric_color_id, c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty,sum(b.grey_fab_qnty*b.rate) as amnt, sum(b.fin_fab_qnty*b.rate) as partial_amnt,b.process_loss_percent,c.item_number_id,a.entry_form,null as  color_all_data from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0  group by c.id,a.booking_type,a.is_short, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.pre_cost_remarks,b.process_loss_percent,c.item_number_id,a.entry_form
	union all
	select 0 as pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia as dia_width,null as adjust_qty, b.body_part as body_part_id, b.color_type_id,b.width_dia_type as width_dia_type, b.gsm_weight,b.remarks as pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color as fabric_color_id, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty,sum(b.finish_fabric) as fqnty, sum(b.grey_fabric*b.rate) as amnt,sum(b.finish_fabric*b.rate) as partial_amnt,b.process_loss as process_loss_percent,b.gmts_item_id as item_number_id,b.entry_form_id as entry_form,b.color_all_data as color_all_data from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id,b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.dia_width,b.fabric_color,b.remarks,b.process_loss, b.gmts_item_id,b.entry_form_id,b.color_all_data ";*/

	$sql ="SELECT a.company_id, c.id pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia_width,sum(b.adjust_qty) adjust_qty,c.body_part_id, c.color_type_id,c.width_dia_type, c.gsm_weight,b.pre_cost_remarks, c.lib_yarn_count_deter_id, b.fabric_color_id, c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty,sum(b.grey_fab_qnty*b.rate) as amnt, sum(b.fin_fab_qnty*b.rate) as partial_amnt,b.process_loss_percent,c.item_number_id,a.entry_form,null as  color_all_data 
	from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c  
	where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0
	group by a.company_id, c.id,a.booking_type,a.is_short, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.pre_cost_remarks,b.process_loss_percent,c.item_number_id,a.entry_form
	union all
	select a.company_id, 0 as pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia_width,null as adjust_qty, b.body_part as body_part_id, b.color_type_id,b.width_dia_type as width_dia_type, b.gsm_weight,b.remarks as pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color as fabric_color_id, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty,sum(b.finish_fabric) as fqnty, sum(b.grey_fabric*b.rate) as amnt,sum(b.finish_fabric*b.rate) as partial_amnt,b.process_loss as process_loss_percent,b.gmts_item_id as item_number_id,b.entry_form_id as entry_form,b.color_all_data as color_all_data from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $dia_width_cond and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.company_id, b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id,b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.dia_width,b.fabric_color,b.remarks,b.process_loss, b.gmts_item_id,b.entry_form_id,b.color_all_data
	union all
	select a.company_id, 0 as pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia as dia_width,null as adjust_qty, b.body_part as body_part_id, b.color_type_id,b.width_dia_type as width_dia_type, b.gsm_weight,b.remarks as pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color as fabric_color_id, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty,sum(b.finish_fabric) as fqnty, sum(b.grey_fabric*b.rate) as amnt,sum(b.finish_fabric*b.rate) as partial_amnt,b.process_loss as process_loss_percent,b.gmts_item_id as item_number_id,b.entry_form_id as entry_form,b.color_all_data as color_all_data from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.entry_form_id=140 and b.entry_form_id=140 and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.company_id, b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id,b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.dia_width,b.fabric_color,b.remarks,b.process_loss, b.gmts_item_id,b.entry_form_id,b.color_all_data ";

	//echo $sql;//die;
	$data_array = sql_select($sql);

	/*echo "<pre>";
	print_r($data_array);die;*/
	if ($data_array[0][csf('entry_form')]==139) // Sample Requisition Fabric Booking -With order // For Dia
	{
		// echo "string";die;
		$sql ="SELECT a.company_id, c.id pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia as dia_width,sum(b.adjust_qty) adjust_qty,c.body_part_id, c.color_type_id,c.width_dia_type, c.gsm_weight,b.pre_cost_remarks, c.lib_yarn_count_deter_id, b.fabric_color_id, c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty,sum(b.grey_fab_qnty*b.rate) as amnt, sum(b.fin_fab_qnty*b.rate) as partial_amnt,b.process_loss_percent,c.item_number_id,a.entry_form,null as  color_all_data 
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c  
		where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.entry_form=139
		group by a.company_id, c.id,a.booking_type,a.is_short, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia, b.fabric_color_id,b.pre_cost_remarks,b.process_loss_percent,c.item_number_id,a.entry_form";
		$data_array = sql_select($sql);
	}
	
	$company_id = $data_array[0][csf('company_id')];

	$variable_set_marchendising = sql_select("select item_category_id,process_loss_method from variable_order_tracking where company_name=$company_id and variable_list=18 and item_category_id = 95 order by id");
	$process_loss_method = !empty($variable_set_marchendising) ? $variable_set_marchendising[0][csf('process_loss_method')] : 0;

	$booking_qnty_source = sql_select("select process_wise_rate_source from variable_settings_production where company_name=$company_id and variable_list=61 and status_active =1 ");
	$booking_qnty_source = ($booking_qnty_source[0][csf('process_wise_rate_source')] == 1) ? $booking_qnty_source[0][csf('process_wise_rate_source')] : 0;
	//for $booking_qnty_source 2,0 or null business as it is 
	//echo $booking_qnty_source."==="."select process_wise_rate_source from variable_settings_production where company_name=$company_id and variable_list=61 and status_active =1 ";die;
	if (!empty($data_array)) 
	{
		$i = 0;$total_amnt = 0;
		foreach ($data_array as $row) 
		{
			if ($row[csf('entry_form')] == 108) 
			{
				$po = $row[csf('partial_po_break_down_id')];
				if (($row[csf('adjust_qty')] != "") || ($row[csf('adjust_qty')] != 0)) 
				{
					if ($process_loss_method == 1) {
						// Markup Method
						$grey_qty = ($row[csf('qnty')] + (($row[csf('qnty')] / 100) * $row[csf('process_loss_percent')]) - $row[csf('adjust_qty')]);
					} else {
						// Margin Method
						$grey_qty = (($row[csf('qnty')]) / (1 - $row[csf('process_loss_percent')] / 100) - $row[csf('adjust_qty')]);
					}
				} else {
					$grey_qty = $row[csf('qnty')];
				}
				$avg_rate = $row[csf('partial_amnt')]/$grey_qty;
				$amount = $row[csf('partial_amnt')];
			} 
			else 
			{
				if($booking_qnty_source==1)
				{
					$grey_qty = $row[csf('fqnty')];
					$avg_rate = $row[csf('partial_amnt')]/$grey_qty;
					$amount = $row[csf('partial_amnt')];
				}
				else
				{
					$grey_qty = $row[csf('qnty')];
					$avg_rate = $row[csf('amnt')]/$grey_qty;
					$amount = $row[csf('amnt')];
				}

				$po = $row[csf('po_break_down_id')];

				
			}

			if($booking_qnty_source==1)
			{
				$process_loss_percent = $row[csf('process_loss_percent')]*1;
			}
			

			if($row[csf('entry_form')]==140)
			{
				$colorID=explode("_",$row[csf('color_all_data')]);
				$row[csf('fabric_color_id')]=$row[csf('fabric_color_id')];//$colorID[2];//fabric_color_id
			}
			$i++;
			?>
			<tr class="general" id="tr_<? echo $i; ?>">
				<td width="25" id="slTd_<? echo $i; ?>">
					<span><? echo $i; ?></span>
					<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboGarmItemId_" . $i, 80, $garments_item, "", 1, "- Select -", $row[csf('item_number_id')], "", "1", "", "", "", "", "", "", "cboGarmItemId[]");
					?>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboBodyPart_" . $i, 80, $body_part, "", 1, "- Select -", $row[csf('body_part_id')], "", "1", "", "", "", "", "", "", "cboBodyPart[]");
					?>
				</td>
				<td width="72">
					<?
					echo create_drop_down("cboColorType_" . $i, 70, $color_type, "", 1, "- Select -", $row[csf('color_type_id')], "", "1", "", "", "", "", "", "", "cboColorType[]");
					?>
				</td>
				<td width="152">
					<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes"
					style="width:140px" placeholder="Double Click To Search"
					onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" disabled="disabled"
					value="<? echo $composition_arr[$row[csf('lib_yarn_count_deter_id')]]; ?>" title="<? echo $composition_arr[$row[csf('lib_yarn_count_deter_id')]]; ?>" readonly/>
					<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('lib_yarn_count_deter_id')]; ?>" title="<? echo $row[csf('lib_yarn_count_deter_id')]; ?>">
				</td>
				<td width="57">
					<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_<? echo $i; ?>" class="text_boxes"
					style="width:45px" value="<? echo $row[csf('gsm_weight')]; ?>" title="<? echo $row[csf('gsm_weight')]; ?>" disabled="disabled"/>
				</td>
				<td width="52">
					<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
					style="width:40px" value="<? echo $row[csf('dia_width')]; ?>" title="<? echo $row[csf('dia_width')]; ?>" disabled="disabled"/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboDiaWidthType_" . $i, 80, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", "1", "", "", "", "", "", "", "cboDiaWidthType[]");
					?>
				</td>
				<td width="87">
					<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
					style="width:75px" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>" readonly
					title="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('fabric_color_id')]; ?>">
				</td>
				<td width="82">
					<?
					$color_range_id = $colorRange_arr[$row[csf('lib_yarn_count_deter_id')]];
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $color_range_id, "", "0", "", "", "", "", "", "", "cboColorRange[]");
					?>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $row[csf('uom')], "", "1", "1,12,23,27", "", "", "", "", "", "cboConsUom[]");
					?>
				</td>
				<td width="67">
					<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>"
					class="text_boxes_numeric"
					style="width:55px" value="<? echo number_format($grey_qty, 4, '.', ''); ?>" title="<? echo number_format($grey_qty, 4, '.', ''); ?>" readonly/>
				</td>
				<td width="57">
					<input type="text" name="txtAvgRate[]" id="txtAvgRate_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:45px"
					value="<? echo number_format($avg_rate, 4, '.', ''); ?>" title="<? echo number_format($avg_rate, 4, '.', ''); ?>"
					onKeyUp="calculate_amount(<? echo $i; ?>);" readonly/>
				</td>
				<td width="72">
					<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:60px" value="<? echo $amount; ?>" title="<? echo $amount; ?>" readonly/>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboUom_" . $i, 50, $unit_of_measurement, "", 0, "", 12, "consumtion_calculate($i);", "0", "1,12,23,27", "", "", "", "", "", "cboUom[]");
					?>
				</td>
				<td width="67">
					<?php
					$uom = $row[csf('uom')];
					$readonly = "readonly";
					if ($uom == 27) {
						$fin_qnty = ($grey_qty * 36 * $row[csf('dia_width')] * $row[csf('gsm_weight')]) / (1550 * 1000);
					}else if ($uom == 1) {
						$readonly = "";
						$fin_qnty = "";
					} else {
						$fin_qnty = $grey_qty * 1;
					}
					?>
					<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px"
					onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
					value="<? echo number_format($fin_qnty, 4, '.', ''); ?>" title="<? echo number_format($fin_qnty, 4, '.', ''); ?>" <?php echo $readonly;?>/>

					<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>"
					value="<? echo number_format($row[csf('rmg_qty')], 4, '.', ''); ?>" readonly/>
				</td>
				<td width="52">
					<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onKeyUp="calculate_grey_qty(<? echo $i; ?>);" value="<? echo $process_loss_percent;?>" />
				</td>
				<td width="67">
					<?
					if($booking_qnty_source==1)
					{
						$fin_qnty = number_format($fin_qnty, 4, '.', '');
					 	$txtProcessLoss = $row[csf('process_loss_percent')]*1;   
					 	$grey_quantity=0;
					 	if ($fin_qnty <= 0 || $txtProcessLoss <= 0) {
					 		$grey_quantity = $fin_qnty;
					 	}
					 	else 
					 	{
					 		if ($process_loss_method == 1) {
					 			$grey_quantity = $fin_qnty + (($fin_qnty / 100) * $txtProcessLoss);
					 		}
					 		else {
					 			$perc = 1 - ($txtProcessLoss / 100);
					 			$grey_quantity = $fin_qnty / $perc;
					 		}
					 		$grey_quantity = number_format($grey_quantity, 4, '.', '');
					 	}
					}
					?>

					<input type="text" name="txtGreyQty[]" id="txtGreyQty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px" value="<? echo $grey_quantity;//number_format($fin_qnty, 4, '.', ''); ?>" title="<? echo number_format($fin_qnty, 4, '.', ''); ?>" readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboWorkScope_" . $i, 80, $item_category, "", 1, "-- Select --", 2, "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
					?>
				</td>

				<td width="100">
	                <input type="text" name="txtProcessNameMain[]" id="txtProcessNameMain_<? echo $i; ?>" class="text_boxes" style="width:70px;" placeholder="Double Click To Search" onDblClick="openmypage_process_main(<? echo $i; ?>);" readonly />
	                <input type="hidden" name="txtProcessIdMain[]" id="txtProcessIdMain_<? echo $i; ?>" value="" />
	                <input type="hidden" name="txtProcessSeqMain[]" id="txtProcessSeqMain_<? echo $i; ?>" value="" />
	            </td>

				<td width="100">
					<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" readonly />
					<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="" />
					<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="" />
				</td>
				<td width="100">
					<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? echo $row[csf('pre_cost_remarks')]; ?>" title="<? echo $row[csf('pre_cost_remarks')]; ?>" readonly/>
				</td>
				<td width="100">
					<input type="text" name="barcode_no[]" id="barcode_no_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? //echo $row[csf('barcode_no')]; ?>"  readonly />
				</td>
				<td>
					<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
					class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes"
					readonly/>
					<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
					id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>"
					value="<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>" class="text_boxes" readonly/>
				</td>
			</tr>
			<?
			$total_amnt += $row[csf('amnt')];
		}
		?>

		<?
	} 
	else 
	{
		?>
		<tr>
			<td colspan="18" style="text-align: center; color: red; font-weight: bold; padding: 5px;">Only UOM in Kg is
				allowed.
			</td>
		</tr>
		<?
	}
	exit();
}

if ($action == 'show_fabric_details_update') {
	$data = explode("**", $data);
	$job_no = $data[0];
	$color_from_library = $data[1];
	$cbo_within_group = $data[2];

	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$bodypart_with_type_arr = return_library_array("select id, body_part_type from lib_body_part where status_active=1 and is_deleted=0", "id", "body_part_type");
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}

	$sql = "select a.id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.avg_rate, a.amount,a.grey_qty, a.work_scope, a.order_uom,a.pre_cost_fabric_cost_dtls_id,a.item_number_id, a.pre_cost_remarks, a.rmg_qty,a.process_loss,a.grey_qnty_by_uom,a.cons_uom,a.process_id_main,a.process_seq_main,a.process_id,a.process_seq,a.barcode_no, a.after_wash_gsm, a.cuttable_dia, a.pp_qnty, a.mtl_qnty, a.fpt_qnty, a.gpt_qnty, a.ccmp_data, a.coller_cuff_cons, a.ply, a.lab_dip_no from fabric_sales_order_dtls a left join fabric_sales_order_dtls b on (a.mst_id=b.mst_id and b.status_active=0 and b.is_deleted=1 and a.pre_cost_fabric_cost_dtls_id= b.pre_cost_fabric_cost_dtls_id and a.color_id=b.color_id) where a.mst_id='$job_no' and a.status_active=1 and a.is_deleted=0 group by a.id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.avg_rate, a.amount, a.grey_qty, a.work_scope, a.order_uom, a.pre_cost_fabric_cost_dtls_id,a.item_number_id,a.pre_cost_remarks, a.rmg_qty,a.process_loss,a.grey_qnty_by_uom,a.cons_uom,a.process_id_main,a.process_seq_main,a.process_id,a.process_seq,a.barcode_no, a.after_wash_gsm, a.cuttable_dia, a.pp_qnty, a.mtl_qnty, a.fpt_qnty, a.gpt_qnty, a.ccmp_data, a.coller_cuff_cons, a.ply, a.lab_dip_no order by a.body_part_id";
	$data_array = sql_select($sql);
	$i = 0;
	foreach ($data_array as $row) {
		$i++;
		$uom = $row[csf('cons_uom')];
		$readonly = "readonly";
		if ($cbo_within_group == 2) {
			$readonly = "";
			$disableCombo = 0;
			$disable = "";
		}else{
			$disableCombo = 1;
			$disable = 'disabled="disabled"';
		}

		if ($cbo_within_group == 1 && $uom == 1) {
			$readonly = '';
		}
		if ($cbo_within_group == 1 && $uom != 1) {
			$readonly = 'readonly="readonly"';
		}
		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}
		$process_name_main = '';
		$process_id_main_array = explode(",", $row[csf("process_id_main")]);
		foreach ($process_id_main_array as $val) {
			if ($process_name_main == "") $process_name_main = $conversion_cost_head_array[$val]; else $process_name_main .= "," . $conversion_cost_head_array[$val];
		}

		?>
		<tr class="general" id="tr_<? echo $i; ?>">
			<td width="25" id="slTd_<? echo $i; ?>">
				<span><? echo $i; ?></span>
				<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
			</td>
			<td width="82">
				<?
				echo create_drop_down("cboGarmItemId_" . $i, 80, $garments_item, "", 1, "- Select -", $row[csf('item_number_id')], "", "1", "", "", "", "", "", "", "cboGarmItemId[]");
				?>
			</td>
			<td width="82">
				<?
				echo create_drop_down("cboBodyPart_" . $i, 80, $body_part, "", 1, "- Select -", $row[csf('body_part_id')], "", $disableCombo, "", "", "", "", "", "", "cboBodyPart[]");
				?>
			</td>
			<td width="72">
				<?
				echo create_drop_down("cboColorType_" . $i, 70, $color_type, "", 1, "- Select -", $row[csf('color_type_id')], "", $disableCombo, "", "", "", "", "", "", "cboColorType[]");
				?>
			</td>
			<td width="152">
				<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes"
				style="width:140px" placeholder="Double Click To Search"
				onDblClick="openmypage_fabricDescription(<? echo $i; ?>)"
				value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" title="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" <? echo $disable; ?>
				readonly/>
				<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes"
				value="<? echo $row[csf('determination_id')]; ?>">
			</td>
			<td width="52">
				<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_<? echo $i; ?>" class="text_boxes"
				style="width:45px"
				value="<? echo $row[csf('gsm_weight')]; ?>"  title="<? echo $row[csf('gsm_weight')]; ?>" <?php echo ($cbo_within_group == 1) ? "disabled" : "" ?> />
			</td>

			<td width="57">
                <input type="text" name="txtAfterFabricGsm[]" id="txtAfterFabricGsm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $row[csf('after_wash_gsm')]; ?>"/>
            </td>

            <td width="52">
               <?
               if($bodypart_with_type_arr[$row[csf('body_part_id')]]==40 || $bodypart_with_type_arr[$row[csf('body_part_id')]]==50){
               	$ply_disabled=0;
               	$collar_cuff_wise_disable = 'disabled="disabled"';
               }else{
               	$ply_disabled=1;
               	$collar_cuff_wise_disable = "";
               }
                $plyArr = array(3=>3,4=>4,5=>5);
                echo create_drop_down("cboPly_".$i, 57, $plyArr, "", 1, "- Select -", $row[csf('ply')], "", $ply_disabled, "", "", "", "", "", "", "cboPly[]");
                ?>
            </td>

			<td width="52">
				<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
				style="width:40px" value="<? echo $row[csf('dia')]; ?>" title="<? echo $row[csf('dia')]; ?>" <? echo $disable; ?> />
			</td>
			<td width="52">
                <input type="text" name="txtFabricCuttableDia[]" id="txtFabricCuttableDia_<? echo $i; ?>" class="text_boxes" style="width:40px" value="<? echo $row[csf('cuttable_dia')]; ?>" /> 
            </td>
			<td width="82">
				<?
				echo create_drop_down("cboDiaWidthType_" . $i, 80, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", $disableCombo, "", "", "", "", "", "", "cboDiaWidthType[]");
				?>
			</td>
			<td width="87">
				<?
				if ($cbo_within_group == 1) {
					?>
					<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
					style="width:75px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>"
					placeholder="Display" readonly/>
					<?
				} else {
					if ($color_from_library == 2) {
						?>
						<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
						style="width:75px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>"
						placeholder="Write"/>
						<?
					} else {
						?>
						<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
						style="width:75px" onDblClick="openmypage_color(<? echo $i; ?>)"
						value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>" placeholder="Double Click"
						readonly/>
						<?
					}
				}
				?>
				<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes"
				value="<? echo $row[csf('color_id')]; ?>">
			</td>
			<td width="82">
				<?
				echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $row[csf('color_range_id')], "", "0", "", "", "", "", "", "", "cboColorRange[]");
				?>
			</td>
			<td width="52">
				<?
				echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $row[csf('cons_uom')], "", $disableCombo, "1,12,23,27", "", "", "", "", "", "cboConsUom[]");
				?>
			</td>
			<td width="67">
				<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:55px" value="<? echo number_format($row[csf('grey_qnty_by_uom')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qnty_by_uom')], 4, '.', ''); ?>" onKeyUp="calculate_amount(<?php echo $i;?>);calculate_fin_qty(<?php echo $i;?>);" onClick="openmypage_ccmp(<? echo $i; ?>);" <? echo $disable; ?> />
			</td>
			<td width="57">
				<input type="text" name="txtAvgRate[]" id="txtAvgRate_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:45px" value="<? echo $row[csf('avg_rate')]; ?>" title="<? echo $row[csf('avg_rate')]; ?>"
				onKeyUp="calculate_amount(<? echo $i; ?>);" <? echo $readonly; ?> />
			</td>
			<td width="72">
				<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:60px" value="<? echo $row[csf('amount')]; ?>" title="<? echo $row[csf('amount')]; ?>" readonly/>
			</td>
			<td width="52">
				<?
				echo create_drop_down("cboUom_" . $i, 50, $unit_of_measurement, "", 0, "", $row[csf('order_uom')], "consumtion_calculate($i);", "", "1,12,27,23", "", "", "", "", "", "cboUom[]");
				?>
			</td>
			<td width="67">
				<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);" value="<? echo $row[csf('finish_qty')]; ?>" title="<? echo $row[csf('finish_qty')]; ?>" readonly/>
				<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>" value="<? echo $row[csf('rmg_qty')]; ?>"
				readonly/>
			</td>

			<td width="67">
                <input type="text" name="txtPPQty[]" id="txtPPQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" value="<? echo $row[csf('pp_qnty')]; ?>" <? echo $collar_cuff_wise_disable;?>/>
            </td>
            <td width="67">
                <input type="text" name="txtMTLQty[]" id="txtMTLQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" value="<? echo $row[csf('mtl_qnty')]; ?>" <? echo $collar_cuff_wise_disable;?>/>
            </td>
            <td width="67">
                <input type="text" name="txtFPTQty[]" id="txtFPTQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" value="<? echo $row[csf('fpt_qnty')]; ?>" <? echo $collar_cuff_wise_disable;?>/>
            </td>
            <td width="67">
                <input type="text" name="txtGPTQty[]" id="txtGPTQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" value="<? echo $row[csf('gpt_qnty')]; ?>" <? echo $collar_cuff_wise_disable;?>/>
            </td>
            <td width="67">
            	<? 
            	$total_finish_qnty = $row[csf('finish_qty')] + $row[csf('pp_qnty')] + $row[csf('mtl_qnty')] + $row[csf('fpt_qnty')] + $row[csf('gpt_qnty')];?>

                <input type="text" name="txtTotalFinishQty[]" id="txtTotalFinishQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" readonly disabled  value="<? echo $total_finish_qnty; ?>" />
            </td>

			<td width="52">
				<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onKeyUp="calculate_grey_qty(<? echo $i; ?>);" value="<? echo $row[csf('process_loss')]; ?>" title="<? echo $row[csf('process_loss')]; ?>" <? echo $collar_cuff_wise_disable;?>/>
			</td>
			<td width="67">
				<input type="text" name="txtGreyQty[]" id="txtGreyQty_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:55px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>"
				readonly/>
			</td>
			<td width="82">
				<?
				echo create_drop_down("cboWorkScope_" . $i, 80, $item_category, "", 1, "-- Select --", $row[csf('work_scope')], "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
				?>
			</td>
			<td width="100">
                <input type="text" name="txtProcessNameMain[]" id="txtProcessNameMain_<? echo $i; ?>" class="text_boxes" style="width:70px;" placeholder="Double Click To Search" onDblClick="openmypage_process_main(<? echo $i; ?>);" value="<? echo $process_name_main;?>" readonly />
                <input type="hidden" name="txtProcessIdMain[]" id="txtProcessIdMain_<? echo $i; ?>" value="<? echo $row[csf('process_id_main')]; ?>" />
                <input type="hidden" name="txtProcessSeqMain[]" id="txtProcessSeqMain_<? echo $i; ?>" value="<? echo $row[csf('process_seq_main')]; ?>" />
            </td>
			<td width="100">
				<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" value="<? echo $process_name; ?>" readonly />
				<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>" />
				<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="<? echo $row[csf('process_seq')]; ?>" />
			</td>
			<td width="100">
				<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
				style="width:90px"
				value="<? echo $row[csf('pre_cost_remarks')]; ?>" title="<? echo $row[csf('pre_cost_remarks')]; ?>" <?php echo ($cbo_within_group == 2) ? "" : "readonly"; ?>/>
			</td>
			<td width="100">
				<input type="text" name="txtLabDipNo[]" id="txtLabDipNo_<? echo $i; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf('lab_dip_no')]; ?>" />
			</td>
			<td width="100">
				<input type="text" name="barcode_no[]" id="barcode_no_<? echo $i; ?>" class="text_boxes"
				style="width:90px"
				value="<? echo $row[csf('barcode_no')]; ?>" readonly />
			</td>
			<td width="70">
				<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
				class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
				class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
				<? 
					if($row[csf('body_part_id')] ==460 || $row[csf('body_part_id')] ==461)
					{
						$display_stat = "";
					}else{
						$display_stat = "display: none;";
					}
				?>
				
				<input type="hidden" name="ccmpSaveData[]" id="ccmpSaveData_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('ccmp_data')]; ?>" readonly/>
				<input type="hidden" name="hdnCollerCuffCon[]" id="hdnCollerCuffCon_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('coller_cuff_cons')]; ?>" readonly/>

				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" readonly/>
				<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]" id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>" value="<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>" class="text_boxes" readonly/>
			</td>
		</tr>
		<?
	}
	exit();
}

if ($action == 'show_fabric_details_last_update') {
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$data = explode("**", $data);
	$mst_id = $data[0];
	$booking_no = $data[1];

	$composition_arr = array();
	$colorRange_arr = array();
	$sql_deter = "select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id";
	$deter_array = sql_select($sql_deter);
	if (count($deter_array) > 0) {
		foreach ($deter_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
			$colorRange_arr[$row[csf('id')]] = $row[csf('color_range_id')];
		}
	}

	$salesData = sql_select("select e.sales_booking_no,d.mst_id,d.pre_cost_fabric_cost_dtls_id,d.color_id,d.dia,d.body_part_id,d.color_type_id,d.gsm_weight,d.determination_id,d.width_dia_type,d.item_number_id,d.color_range_id,d.process_loss from fabric_sales_order_dtls d, fabric_sales_order_mst e where e.id=d.mst_id and d.mst_id=$mst_id and d.status_active=1 and d.is_deleted=0");
	foreach ($salesData as $sales_row) {
		$sales_arr[$sales_row[csf("sales_booking_no")]][$sales_row[csf("pre_cost_fabric_cost_dtls_id")]][$sales_row[csf("body_part_id")]][$sales_row[csf("color_type_id")]][$sales_row[csf("gsm_weight")]][$sales_row[csf("dia")]][$sales_row[csf("width_dia_type")]][$sales_row[csf("determination_id")]][$sales_row[csf("color_id")]][$sales_row[csf("item_number_id")]] = $sales_row[csf("color_range_id")] . "_" . $sales_row[csf("process_loss")];
	}

	$data_arr = array();

	/*$sql = "select a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.dia_width, c.body_part_id, c.color_type_id, c.width_dia_type,c.item_number_id, c.gsm_weight,b.pre_cost_remarks,c.lib_yarn_count_deter_id, b.fabric_color_id, b.process_loss_percent,c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty, sum(b.fin_fab_qnty*b.rate) as amnt,sum(b.adjust_qty) as adjust_qty,a.entry_form from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.process_loss_percent,a.entry_form,b.pre_cost_remarks,c.item_number_id
	union all
	select a.booking_no,0 as pre_cost_fabric_cost_dtls_id, null as po_break_down_id, b.dia as dia_width, b.body_part, b.color_type_id, b.width_dia_type as width_dia_type,b.gmts_item_id,b.gsm_weight,b.remarks pre_cost_remarks, b.lib_yarn_count_deter_id,
	b.fabric_color fabric_color_id, b.process_loss, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty, sum(b.finish_fabric) as fqnty, sum(b.finish_fabric*b.rate) as amnt,null as adjust_qty,null as entry_form
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0
	group by a.booking_no,b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.fabric_color,b.remarks,b.process_loss, b.gmts_item_id";//and c.uom in(12,27)*/


	if($db_type==0)
	{
		$dia_width_cond = " and b.entry_form_id ='' and a.entry_form_id =''";
	}
	else
	{
		$dia_width_cond = " and b.entry_form_id is null and a.entry_form_id is null";
	}


	$sql = "select a.company_id, a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.dia_width, c.body_part_id, c.color_type_id, c.width_dia_type,c.item_number_id, c.gsm_weight,b.pre_cost_remarks,c.lib_yarn_count_deter_id, b.fabric_color_id, b.process_loss_percent as process_loss,c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty, sum(b.fin_fab_qnty*b.rate) as amnt,sum(b.adjust_qty) as adjust_qty,a.entry_form from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.company_id, a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.process_loss_percent,a.entry_form,b.pre_cost_remarks,c.item_number_id
	union all
	select a.company_id, a.booking_no,0 as pre_cost_fabric_cost_dtls_id, null as po_break_down_id, b.dia_width, b.body_part, b.color_type_id, b.width_dia_type as width_dia_type,b.gmts_item_id,b.gsm_weight,b.remarks pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color fabric_color_id, b.process_loss, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty, sum(b.finish_fabric) as fqnty, sum(b.finish_fabric*b.rate) as amnt,null as adjust_qty,null as entry_form
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no $dia_width_cond and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0
	group by a.company_id, a.booking_no,b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.fabric_color, b.remarks, b.process_loss, b.gmts_item_id,b.dia_width
	union all
	select a.company_id, a.booking_no,0 as pre_cost_fabric_cost_dtls_id, null as po_break_down_id, b.dia as dia_width, b.body_part, b.color_type_id, b.width_dia_type as width_dia_type,b.gmts_item_id,b.gsm_weight,b.remarks pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color fabric_color_id, b.process_loss, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty, sum(b.finish_fabric) as fqnty, sum(b.finish_fabric*b.rate) as amnt,null as adjust_qty,null as entry_form
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.entry_form_id=140 and b.entry_form_id=140 and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0
	group by a.company_id, a.booking_no,b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.fabric_color, b.remarks, b.process_loss, b.gmts_item_id";


	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$process_loss_percent = $sales_arr[$row[csf("booking_no")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("body_part_id")]][$row[csf("color_type_id")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]][$row[csf("width_dia_type")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]][$row[csf("item_number_id")]];

		$index = $row[csf('body_part_id')] . "__" . $row[csf('lib_yarn_count_deter_id')] . "__" . $row[csf('color_type_id')] . "__" . $row[csf('width_dia_type')] . "__" . $row[csf('gsm_weight')] . "__" . $row[csf('dia_width')] . "__" . $row[csf('fabric_color_id')] . "__" . $row[csf('uom')] . "__" . $row[csf('pre_cost_remarks')] . "__" . $row[csf('item_number_id')] . "__" . $row[csf('pre_cost_fabric_cost_dtls_id')] . "__" . $process_loss_percent;
		$data_arr[$index] = $row[csf('fqnty')] . "**" . $row[csf('amnt')] . "**" . $row[csf('pre_cost_remarks')] . "**" . $row[csf('qnty')] . "**" . $row[csf('rmg_qty')] . "**" . $process_loss_percent . "**" . $row[csf('adjust_qty')] . "**" . $row[csf('entry_form')]. "**" . $row[csf('process_loss')];

		$company_id = $row[csf('company_id')];
	}
	unset($data_array);


	$variable_set_marchendising = sql_select("select item_category_id,process_loss_method from variable_order_tracking where company_name=$company_id and variable_list=18 and item_category_id = 95 order by id");
	$process_loss_method = !empty($variable_set_marchendising) ? $variable_set_marchendising[0][csf('process_loss_method')] : 0;

	$booking_qnty_source = sql_select("select process_wise_rate_source from variable_settings_production where company_name=$company_id and variable_list=61 and status_active =1 ");
	$booking_qnty_source = ($booking_qnty_source[0][csf('process_wise_rate_source')] == 1) ? $booking_qnty_source[0][csf('process_wise_rate_source')] : 0;


	$sql = "select id, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_range_id, color_id, finish_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom, pre_cost_remarks, rmg_qty, item_number_id, pre_cost_fabric_cost_dtls_id,process_id, process_seq, process_id_main, process_seq_main from fabric_sales_order_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by body_part_id desc";
	$data_array = sql_select($sql);
	$i = 1;

	foreach ($data_array as $row) 
	{
		$index = $row[csf('body_part_id')] . "__" . $row[csf('determination_id')] . "__" . $row[csf('color_type_id')] . "__" . $row[csf('width_dia_type')] . "__" . $row[csf('gsm_weight')] . "__" . $row[csf('dia')] . "__" . $row[csf('color_id')] . "__" . $row[csf('order_uom')] . "__" . $row[csf('pre_cost_remarks')];

		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}

		$process_name_main = '';
		$process_id_main_array = explode(",", $row[csf("process_id_main")]);
		foreach ($process_id_main_array as $val) {
			if ($process_name_main == "") $process_name_main = $conversion_cost_head_array[$val]; else $process_name_main .= "," . $conversion_cost_head_array[$val];
		}


		if ($data_arr[$index]) 
		{
			$i++;
			$newData = explode("**", $data_arr[$index]);
			$row[csf('finish_qty')] = $newData[0];
			$row[csf('rmg_qty')] = $newData[4];
			$row[csf('amount')] = $newData[1];
			$row[csf('avg_rate')] = number_format($newData[1] / $newData[0], 4, '.', '');
			$row[csf('grey_qty')] = (($row[csf('finish_qty')] / 100) * $row[csf('process_loss')]) + $row[csf('finish_qty')];
			$row[csf('pre_cost_remarks')] = $newData[2];
			unset($data_arr[$index]);
			?>
			<tr class="general" id="tr_<? echo $i; ?>">
				<td width="25" id="slTd_<? echo $i; ?>">
					<span><? echo $i; ?></span>
					<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboGarmItemId_" . $i, 80, $garments_item, "", 1, "- Select -", $row[csf('item_number_id')], "", "1", "", "", "", "", "", "", "cboGarmItemId[]");
					?>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboBodyPart_" . $i, 80, $body_part, "", 1, "- Select -", $row[csf('body_part_id')], "", "1", "", "", "", "", "", "", "cboBodyPart[]");
					?>
				</td>
				<td width="72">
					<?
					echo create_drop_down("cboColorType_" . $i, 70, $color_type, "", 1, "- Select -", $row[csf('color_type_id')], "", "1", "", "", "", "", "", "", "cboColorType[]");
					?>
				</td>
				<td width="152">
					<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes"
					style="width:140px" placeholder="Double Click To Search"
					onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" disabled="disabled"
					value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" title="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" readonly/>
					<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('determination_id')]; ?>">
				</td>
				<td width="57">
					<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_<? echo $i; ?>" class="text_boxes"
					style="width:45px" value="<? echo $row[csf('gsm_weight')]; ?>" disabled="disabled" title="<? echo $row[csf('gsm_weight')]; ?>"/>
				</td>
				<td width="52">
					<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
					style="width:40px" value="<? echo $row[csf('dia')]; ?>" disabled="disabled" title="<? echo $row[csf('dia')]; ?>"/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboDiaWidthType_" . $i, 80, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", "1", "", "", "", "", "", "", "cboDiaWidthType[]");
					?>
				</td>
				<td width="87">
					<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
					style="width:75px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>" readonly/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('color_id')]; ?>">
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $row[csf('color_range_id')], "", "0", "", "", "", "", "", "", "cboColorRange[]");
					?>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $row[csf('order_uom')], "", "1", "12,23,27,1", "", "", "", "", "", "cboConsUom[]");
					?>
				</td>
				<td width="67">
					<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" readonly/>
				</td>
				<td width="57">
					<input type="text" name="txtAvgRate[]" id="txtAvgRate_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:45px" value="<? echo $row[csf('avg_rate')]; ?>" title="<? echo $row[csf('avg_rate')]; ?>"
					onKeyUp="calculate_amount(<? echo $i; ?>);" readonly/>
				</td>
				<td width="72">
					<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:60px" value="<? echo $amount; ?>" title="<? echo $amount; ?>" readonly/>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboUom_" . $i, 50, $unit_of_measurement, "", 0, "", 12, "", "1", "12,27,23,1", "", "", "", "", "", "cboUom[]");
					?>
				</td>
				<td width="67">
					<?php
					$uom = $row[csf('uom')];
					$readonly = "readonly";
					if ($uom == 27) {
						$fin_qnty = ($grey_qty * 36 * $row[csf('dia_width')] * $row[csf('gsm_weight')]) / (1550 * 1000);
					}else if ($uom == 1) {
						$readonly = "";
						$fin_qnty = "";
					} else {
						$fin_qnty = $grey_qty * 1;
					}
					?>
					<?php
					/*$uom = $row[csf('order_uom')];
					$readonly = "readonly";
					if ($uom == 1) {
						$readonly = "";
						$fin_qnty = "";
					} else {
						$fin_qnty = $row[csf('finish_qty')];;
					}*/
					?>
					<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px"
					onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
					value="<? echo $fin_qnty; ?>" <?php echo $readonly; ?> title="<? echo $fin_qnty; ?>"/>
					<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>"
					value="<? echo $row[csf('rmg_qty')]; ?>" readonly/>
				</td>
				<td width="52">
					<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onKeyUp="calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" value="<? echo $row[csf('process_loss')]; ?>" title="<? echo $row[csf('process_loss')]; ?>"/>
				</td>
				<td width="67">
					<input type="text" name="txtGreyQty[]" id="txtGreyQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>"
					readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboWorkScope_" . $i, 80, $item_category, "", 1, "-- Select --", $row[csf('work_scope')], "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
					?>
				</td>
				<td width="100">
					<input type="text" name="txtProcessNameMain[]" id="txtProcessNameMain_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process_main(<? echo $i; ?>);" value="===<? echo $process_name_main; ?>" readonly />
					<input type="hidden" name="txtProcessIdMain[]" id="txtProcessIdMain_<? echo $i; ?>" value="<? echo $row[csf('process_id_main')]; ?>" />
					<input type="hidden" name="txtProcessSeqMain[]" id="txtProcessSeqMain_<? echo $i; ?>" value="<? echo $row[csf('process_seq_main')]; ?>" />
				</td>
				<td width="100">
					<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" value="<? echo $process_name; ?>" readonly />
					<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>" />
					<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="<? echo $row[csf('process_seq')]; ?>" />
				</td>
				<td width="100">
					<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? echo $row[csf('pre_cost_remarks')]; ?>" title="<? echo $row[csf('pre_cost_remarks')]; ?>" readonly/>
				</td>
				<td width="100">
					<input type="text" name="barcode_no[]" id="barcode_no_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? //echo $row[csf('barcode_no')]; ?>"  readonly />
				</td>
				<td>
					<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
					class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('id')]; ?>" readonly/>
					<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
					id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>"
					value="<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>" class="text_boxes" readonly/>
				</td>
			</tr>
			<?
		}
	}

	if (count($data_arr) > 0) 
	{
		foreach ($data_arr as $key => $val) 
		{
			//echo $val."===";
			$newData = explode("**", $val);
			$finish_qty = $newData[0];
			$gray_qty = $newData[3];
			$amount = $newData[1];
			$rmg_qty = $newData[4];
			$booking_process_loss = $newData[8];

			$index_data = explode("__", $key);
			$body_part_id = $index_data[0];
			$lib_yarn_count_deter_id = $index_data[1];
			$color_type_id = $index_data[2];
			$width_dia_type = $index_data[3];
			$gsm_weight = $index_data[4];
			$dia_width = $index_data[5];
			$color_id = $index_data[6];
			$uom = $index_data[7];
			$pre_cost_remarks = $index_data[8];
			$item_number_id = $index_data[9];
			$pre_cost_fabric_cost_dtls_id = $index_data[10];
			$process_loss_per = explode('_', $index_data[11]);
			if ($newData[7] == 108) {
				if (($newData[6] != "") || ($newData[6] != 0)) {
					$gray_qty = ($gray_qty - $newData[6]);
				}
			}
			?>
			<tr class="general" id="tr_<? echo $i; ?>">
				<td width="25" id="slTd_<? echo $i; ?>">
					<span><? echo $i; ?></span>
					<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboGarmItemId_" . $i, 80, $garments_item, "", 1, "- Select -", $item_number_id, "", "1", "", "", "", "", "", "", "cboGarmItemId[]");
					?>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboBodyPart_" . $i, 80, $body_part, "", 1, "- Select -", $body_part_id, "", "1", "", "", "", "", "", "", "cboBodyPart[]");
					?>
				</td>
				<td width="72">
					<?
					echo create_drop_down("cboColorType_" . $i, 70, $color_type, "", 1, "- Select -", $color_type_id, "", "1", "", "", "", "", "", "", "cboColorType[]");
					?>
				</td>
				<td width="152">
					<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes"
					style="width:140px" placeholder="Double Click To Search"
					onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" disabled="disabled"
					value="<? echo $composition_arr[$lib_yarn_count_deter_id]; ?>" title="<? echo $composition_arr[$lib_yarn_count_deter_id]; ?>" readonly/>
					<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $lib_yarn_count_deter_id; ?>">
				</td>
				<td width="57">
					<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_<? echo $i; ?>" class="text_boxes"
					style="width:45px" value="<? echo $gsm_weight; ?>" disabled="disabled" title="<? echo $gsm_weight; ?>"/>

				</td>
				<td width="52">
					<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
					style="width:40px" value="<? echo $dia_width; ?>" disabled="disabled" title="<? echo $dia_width; ?>"/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboDiaWidthType_" . $i, 80, $fabric_typee, "", 1, "-- Select --", $width_dia_type, "", "1", "", "", "", "", "", "", "cboDiaWidthType[]");
					?>
				</td>
				<td width="87">
					<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
					style="width:75px" value="<? echo $color_library[$color_id]; ?>"  title="<? echo $color_library[$color_id]; ?>" readonly/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $color_id; ?>">
				</td>
				<td width="82">
					<?
					if($booking_qnty_source == 1)
					{
						$color_range_id = $colorRange_arr[$lib_yarn_count_deter_id];
						$booking_quantity = $finish_qty;
					}else{
						$color_range_id = $process_loss_per[0];
						$booking_quantity = $gray_qty;
					}
					
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $color_range_id, "", "0", "", "", "", "", "", "", "cboColorRange[]");
					?>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $uom, "", $disableCombo, "12,27,23,1", "", "", "", "", "", "cboConsUom[]");
					?>
				</td>
				<td width="67">
					<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($booking_quantity, 4, '.', ''); ?>" title="<? echo number_format($booking_quantity, 4, '.', ''); ?>" onKeyUp="calculate_amount(<?php echo $i;?>);calculate_fin_qty(<?php echo $i;?>);"/>
				</td>

				<td width="57">
					<input type="text" name="txtAvgRate[]" id="txtAvgRate_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:45px" value="<? echo number_format($amount / $finish_qty, 4, '.', ''); ?>"
					onKeyUp="calculate_amount(<? echo $i; ?>);" title="<? echo number_format($amount / $finish_qty, 4, '.', ''); ?>" readonly/>
				</td>
				<td width="72">
					<?php
					if ($row[csf('entry_form')] == 108) {
						$amnt = $row[csf('amount')];
					}else{
						$amnt = $newData[3]*($amount / $finish_qty);
					}
					?>
					<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $amnt; ?>"  title="<? echo $amnt; ?>" readonly/>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboUom_" . $i, 50, $unit_of_measurement, "", 0, "", 12, "", "1", "12,27,23,1", "", "", "", "", "", "cboUom[]");
					?>
				</td>
				<td width="67">
					<?php
					$readonly = "readonly";
					if ($uom == 27) {
						$fin_qnty = ($booking_quantity * 36 * $dia_width * $gsm_weight) / (1550 * 1000);
					}else if ($uom == 1) {
						$readonly = "";
						$fin_qnty = "";
					} else {
						$fin_qnty = $booking_quantity * 1;
					}

					if($booking_qnty_source == 1)
					{
						$gray_qty_with_process_loss = ($fin_qnty + (($fin_qnty / 100) * $booking_process_loss ));
						$process_loss_percent_show =  $booking_process_loss;

					}else{
						$gray_qty_with_process_loss = ($fin_qnty + (($fin_qnty / 100) * $process_loss_per[1]));
						$process_loss_percent_show =  $process_loss_per[1];
					}


					
					?>
					<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px"	onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);" value="<? echo number_format($fin_qnty, 4, '.', ''); ?>" title="<? echo number_format($fin_qnty, 4, '.', ''); ?>" <? echo $readonly;?> />
					<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>" value="<? echo $rmg_qty; ?>"
					readonly/>
				</td>
				<td width="52">
					<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<?php echo $process_loss_percent_show ; ?>" onKeyUp="calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" title="<?php echo $process_loss_percent_show ; ?>"/>
				</td>
				<td width="67">
					<input type="text" name="txtGreyQty[]" id="txtGreyQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($gray_qty_with_process_loss, 4, '.', ''); ?>" title="<? echo number_format($gray_qty_with_process_loss, 4, '.', ''); ?>"
					readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboWorkScope_" . $i, 80, $item_category, "", 1, "-- Select --", 2, "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
					?>
				</td>
				<td width="100">
					<input type="text" name="txtProcessNameMain[]" id="txtProcessNameMain_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process_main(<? echo $i; ?>);" value="<? //echo $process_name_main; ?>" readonly />
					<input type="hidden" name="txtProcessIdMain[]" id="txtProcessIdMain_<? echo $i; ?>" value="<? //echo $row[csf('process_id_main')]; ?>" />
					<input type="hidden" name="txtProcessSeqMain[]" id="txtProcessSeqMain_<? echo $i; ?>" value="<? //echo $row[csf('process_seq_main')]; ?>" />
				</td>
				<td width="100">
					<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" value="<? //echo $process_name; ?>" readonly />
					<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="<? //echo $row[csf('process_id')]; ?>" />
					<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="<? //echo $row[csf('process_seq')]; ?>" />
				</td>
				<td width="100">
					<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? echo $pre_cost_remarks; ?>" title="<? echo $pre_cost_remarks; ?>" readonly/>
				</td>
				<td width="100">
					<input type="text" name="barcode_no[]" id="barcode_no_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? //echo $row[csf('barcode_no')]; ?>"  readonly />
				</td>
				<td>
					<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
					class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes"
					readonly/>
					<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
					id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>"
					value="<? echo $pre_cost_fabric_cost_dtls_id; ?>" class="text_boxes" readonly/>
				</td>
			</tr>
			<?
			$i++;
		}
	}
	exit();
}

if ($action == 'show_fabric_details_last_update_pre') {
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$data = explode("**", $data);
	$mst_id = $data[0];
	$booking_no = $data[1];

	$composition_arr = array();
	$colorRange_arr = array();
	$sql_deter = "select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
			$colorRange_arr[$row[csf('id')]] = $row[csf('color_range_id')];
		}
	}

	$data_arr = array();
	$sql = "select b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.dia_width, c.body_part_id, c.color_type_id, c.width_dia_type,c.item_number_id, c.gsm_weight,b.pre_cost_remarks,c.lib_yarn_count_deter_id, b.fabric_color_id, b.process_loss_percent,c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty, sum(b.fin_fab_qnty*b.rate) as amnt,sum(b.adjust_qty) as adjust_qty, (select d.color_range_id || '_' ||  d.process_loss from fabric_sales_order_dtls d where b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.mst_id=$mst_id and b.fabric_color_id=d.color_id and d.status_active=1 and d.is_deleted=0 and b.dia_width=d.dia and  c.body_part_id= d.body_part_id and c.color_type_id= d.color_type_id and c.gsm_weight=d.gsm_weight and c.lib_yarn_count_deter_id=d.determination_id) process_loss_percent,a.entry_form from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and c.uom in(12,27) group by b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.process_loss_percent,a.entry_form,b.pre_cost_remarks,c.item_number_id
	union all
	select 0 as pre_cost_fabric_cost_dtls_id, null as po_break_down_id, b.dia_width, b.body_part, b.color_type_id, null as width_dia_type,b.gmts_item_id,b.gsm_weight,b.remarks pre_cost_remarks, b.lib_yarn_count_deter_id,
	b.fabric_color fabric_color_id, b.process_loss, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty, sum(b.finish_fabric) as fqnty, sum(b.grey_fabric*b.rate) as amnt,null as adjust_qty,
	(select d.color_range_id || '_' || d.process_loss from fabric_sales_order_dtls d where d.mst_id=$mst_id and b.fabric_color=d.color_id and d.status_active=1 and d.is_deleted=0 and b.dia_width=d.dia and b.body_part= d.body_part_id and b.color_type_id= d.color_type_id and b.gsm_weight=d.gsm_weight and b.lib_yarn_count_deter_id=d.determination_id) process_loss_percent,null as entry_form
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0
	group by b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.uom, b.gsm_weight, b.dia_width, b.fabric_color,b.remarks,b.process_loss, b.gmts_item_id
	";

	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$index = $row[csf('body_part_id')] . "__" . $row[csf('lib_yarn_count_deter_id')] . "__" . $row[csf('color_type_id')] . "__" . $row[csf('width_dia_type')] . "__" . $row[csf('gsm_weight')] . "__" . $row[csf('dia_width')] . "__" . $row[csf('fabric_color_id')] . "__" . $row[csf('uom')] . "__" . $row[csf('pre_cost_remarks')] . "__" . $row[csf('item_number_id')] . "__" . $row[csf('pre_cost_fabric_cost_dtls_id')] . "__" . $row[csf('process_loss_percent')];
		$data_arr[$index] = $row[csf('fqnty')] . "**" . $row[csf('amnt')] . "**" . $row[csf('pre_cost_remarks')] . "**" . $row[csf('qnty')] . "**" . $row[csf('rmg_qty')] . "**" . $row[csf('process_loss_percent')] . "**" . $row[csf('adjust_qty')] . "**" . $row[csf('entry_form')];
	}
	unset($data_array);

	$sql = "select id, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_range_id, color_id, finish_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom,pre_cost_remarks,rmg_qty,item_number_id,pre_cost_fabric_cost_dtls_id,process_id,process_seq from fabric_sales_order_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by body_part_id desc";
	$data_array = sql_select($sql);
	$i = 1;

	foreach ($data_array as $row) {

		$index = $row[csf('body_part_id')] . "__" . $row[csf('determination_id')] . "__" . $row[csf('color_type_id')] . "__" . $row[csf('width_dia_type')] . "__" . $row[csf('gsm_weight')] . "__" . $row[csf('dia')] . "__" . $row[csf('color_id')] . "__" . $row[csf('order_uom')] . "__" . $row[csf('pre_cost_remarks')];

		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}

		if ($data_arr[$index]) {
			$i++;
			$newData = explode("**", $data_arr[$index]);
			$row[csf('finish_qty')] = $newData[0];
			$row[csf('rmg_qty')] = $newData[4];
			$row[csf('amount')] = $newData[1];
			$row[csf('avg_rate')] = number_format($newData[1] / $newData[0], 4, '.', '');
			$row[csf('grey_qty')] = (($row[csf('finish_qty')] / 100) * $row[csf('process_loss')]) + $row[csf('finish_qty')];
			$row[csf('pre_cost_remarks')] = $newData[2];
			unset($data_arr[$index]);
			?>
			<tr class="general" id="tr_<? echo $i; ?>">
				<td width="25" id="slTd_<? echo $i; ?>">
					<span><? echo $i; ?></span>
					<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboGarmItemId_" . $i, 80, $garments_item, "", 1, "- Select -", $row[csf('item_number_id')], "", "1", "", "", "", "", "", "", "cboGarmItemId[]");
					?>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboBodyPart_" . $i, 80, $body_part, "", 1, "- Select -", $row[csf('body_part_id')], "", "1", "", "", "", "", "", "", "cboBodyPart[]");
					?>
				</td>
				<td width="72">
					<?
					echo create_drop_down("cboColorType_" . $i, 70, $color_type, "", 1, "- Select -", $row[csf('color_type_id')], "", "1", "", "", "", "", "", "", "cboColorType[]");
					?>
				</td>
				<td width="152">
					<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes"
					style="width:140px" placeholder="Double Click To Search"
					onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" disabled="disabled"
					value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" title="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" readonly/>
					<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('determination_id')]; ?>">
				</td>
				<td width="57">
					<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_<? echo $i; ?>" class="text_boxes"
					style="width:45px" value="<? echo $row[csf('gsm_weight')]; ?>" disabled="disabled" title="<? echo $row[csf('gsm_weight')]; ?>"/>
				</td>
				<td width="52">
					<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
					style="width:40px" value="<? echo $row[csf('dia')]; ?>" disabled="disabled" title="<? echo $row[csf('dia')]; ?>"/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboDiaWidthType_" . $i, 80, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", "1", "", "", "", "", "", "", "cboDiaWidthType[]");
					?>
				</td>
				<td width="87">
					<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
					style="width:75px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" title="<? echo $color_library[$row[csf('color_id')]]; ?>" readonly/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('color_id')]; ?>">
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $row[csf('color_range_id')], "", "0", "", "", "", "", "", "", "cboColorRange[]");
					?>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $row[csf('order_uom')], "", "1", "12,23,27,1", "", "", "", "", "", "cboConsUom[]");
					?>
				</td>
				<td width="67">
					<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" readonly/>
				</td>
				<td width="57">
					<input type="text" name="txtAvgRate[]" id="txtAvgRate_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:45px" value="<? echo $row[csf('avg_rate')]; ?>" title="<? echo $row[csf('avg_rate')]; ?>"
					onKeyUp="calculate_amount(<? echo $i; ?>);" readonly/>
				</td>
				<td width="72">
					<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:60px" value="<? echo $amount; ?>" title="<? echo $amount; ?>" readonly/>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboUom_" . $i, 50, $unit_of_measurement, "", 0, "", 12, "", "1", "12,23,27", "", "", "", "", "", "cboUom[]");
					?>
				</td>
				<td width="67">
					<?php
					$uom = $row[csf('order_uom')];
					$readonly = "readonly";
					if ($uom == 1) {
						$readonly = "";
						$fin_qnty = "";
					} else {
						$fin_qnty = $row[csf('finish_qty')];;
					}
					?>
					<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px"
					onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
					value="<? echo $fin_qnty; ?>" <?php echo $readonly; ?> title="<? echo $fin_qnty; ?>"/>
					<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>"
					value="<? echo $row[csf('rmg_qty')]; ?>" readonly/>
				</td>
				<td width="52">
					<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>"
					class="text_boxes_numeric" style="width:40px"
					onKeyUp="calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);"
					value="<? echo $row[csf('process_loss')]; ?>" title="<? echo $row[csf('process_loss')]; ?>"/>
				</td>
				<td width="67">
					<input type="text" name="txtGreyQty[]" id="txtGreyQty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>"
					readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboWorkScope_" . $i, 80, $item_category, "", 1, "-- Select --", $row[csf('work_scope')], "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
					?>
				</td>
				<td width="100">
					<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" value="<? echo $process_name; ?>" readonly />
					<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>" />
					<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="<? echo $row[csf('process_seq')]; ?>" />
				</td>
				<td width="100">
					<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? echo $row[csf('pre_cost_remarks')]; ?>" title="<? echo $row[csf('pre_cost_remarks')]; ?>" readonly/>
				</td>
				<td>
					<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
					class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes"
					value="<? echo $row[csf('id')]; ?>" readonly/>
					<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
					id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>"
					value="<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>" class="text_boxes" readonly/>
				</td>
			</tr>
			<?
		}
	}

	if (count($data_arr) > 0) {
		foreach ($data_arr as $key => $val) {
			//echo $val."===";
			$newData = explode("**", $val);
			$finish_qty = $newData[0];
			$gray_qty = $newData[3];
			$amount = $newData[1];
			$rmg_qty = $newData[4];

			$index_data = explode("__", $key);
			$body_part_id = $index_data[0];
			$lib_yarn_count_deter_id = $index_data[1];
			$color_type_id = $index_data[2];
			$width_dia_type = $index_data[3];
			$gsm_weight = $index_data[4];
			$dia_width = $index_data[5];
			$color_id = $index_data[6];
			$uom = $index_data[7];
			$pre_cost_remarks = $index_data[8];
			$item_number_id = $index_data[9];
			$pre_cost_fabric_cost_dtls_id = $index_data[10];
			$process_loss_per = explode('_', $index_data[11]);
			if ($newData[7] == 108) {
				if (($newData[6] != "") || ($newData[6] != 0)) {
					$gray_qty = ($gray_qty - $newData[6]);
				}
			}
			?>
			<tr class="general" id="tr_<? echo $i; ?>">
				<td width="25" id="slTd_<? echo $i; ?>">
					<span><? echo $i; ?></span>
					<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboGarmItemId_" . $i, 80, $garments_item, "", 1, "- Select -", $item_number_id, "", "1", "", "", "", "", "", "", "cboGarmItemId[]");
					?>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboBodyPart_" . $i, 80, $body_part, "", 1, "- Select -", $body_part_id, "", "1", "", "", "", "", "", "", "cboBodyPart[]");
					?>
				</td>
				<td width="72">
					<?
					echo create_drop_down("cboColorType_" . $i, 70, $color_type, "", 1, "- Select -", $color_type_id, "", "1", "", "", "", "", "", "", "cboColorType[]");
					?>
				</td>
				<td width="152">
					<input type="text" name="txtFabricDesc[]" id="txtFabricDesc_<? echo $i; ?>" class="text_boxes"
					style="width:140px" placeholder="Double Click To Search"
					onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" disabled="disabled"
					value="<? echo $composition_arr[$lib_yarn_count_deter_id]; ?>" title="<? echo $composition_arr[$lib_yarn_count_deter_id]; ?>" readonly/>
					<input type="hidden" name="fabricDescId[]" id="fabricDescId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $lib_yarn_count_deter_id; ?>">
				</td>
				<td width="57">
					<input type="text" name="txtFabricGsm[]" id="txtFabricGsm_<? echo $i; ?>" class="text_boxes"
					style="width:45px" value="<? echo $gsm_weight; ?>" disabled="disabled" title="<? echo $gsm_weight; ?>"/>

				</td>
				<td width="52">
					<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
					style="width:40px" value="<? echo $dia_width; ?>" disabled="disabled" title="<? echo $dia_width; ?>"/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboDiaWidthType_" . $i, 80, $fabric_typee, "", 1, "-- Select --", $width_dia_type, "", "1", "", "", "", "", "", "", "cboDiaWidthType[]");
					?>
				</td>
				<td width="87">
					<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>" class="text_boxes"
					style="width:75px" value="<? echo $color_library[$color_id]; ?>"  title="<? echo $color_library[$color_id]; ?>" readonly/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" class="text_boxes"
					value="<? echo $color_id; ?>">
				</td>
				<td width="82">
					<?
					$color_range_id = $colorRange_arr[$lib_yarn_count_deter_id];
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $process_loss_per[0], "", "0", "", "", "", "", "", "", "cboColorRange[]");
					?>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $uom, "", $disableCombo, "12,23,27,1", "", "", "", "", "", "cboConsUom[]");
					?>
				</td>
				<td width="67">
					<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px"
					value="<? echo number_format($gray_qty, 4, '.', ''); ?>" title="<? echo number_format($gray_qty, 4, '.', ''); ?>"
					onkeyup="calculate_amount(<?php echo $i;?>);calculate_fin_qty(<?php echo $i;?>);"/>
				</td>

				<td width="57">
					<input type="text" name="txtAvgRate[]" id="txtAvgRate_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:45px" value="<? echo number_format($amount / $finish_qty, 4, '.', ''); ?>"
					onKeyUp="calculate_amount(<? echo $i; ?>);" title="<? echo number_format($amount / $finish_qty, 4, '.', ''); ?>" readonly/>
				</td>
				<td width="72">
					<?php
					if ($row[csf('entry_form')] == 108) {
						$amnt = $row[csf('amount')];
					}else{
						$amnt = $newData[3]*($amount / $finish_qty);
					}
					?>
					<input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:60px" value="<? echo $amnt; ?>"  title="<? echo $amnt; ?>" readonly/>
				</td>
				<td width="52">
					<?
					echo create_drop_down("cboUom_" . $i, 50, $unit_of_measurement, "", 0, "", 1, "", "1", "12,23,27", "", "", "", "", "", "cboUom[]");
					?>
				</td>
				<td width="67">
					<?php
					if ($uom == 27) {
						$fin_qnty = ($gray_qty * 36 * $dia_width * $gsm_weight) / (1550 * 1000);
					} else {
						$fin_qnty = $gray_qty * 1;
					}
					$gray_qty_with_process_loss = ($fin_qnty + (($fin_qnty / 100) * $process_loss_per[1]));
					?>
					<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px"
					onKeyUp="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
					value="<? echo number_format($fin_qnty, 4, '.', ''); ?>" title="<? echo number_format($fin_qnty, 4, '.', ''); ?>" readonly/>
					<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>" value="<? echo $rmg_qty; ?>"
					readonly/>
				</td>
				<td width="52">
					<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>"
					class="text_boxes_numeric" style="width:40px" value="<?php echo $process_loss_per[1]; ?>"
					onKeyUp="calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" title="<?php echo $process_loss_per[1]; ?>"/>
				</td>
				<td width="67">
					<input type="text" name="txtGreyQty[]" id="txtGreyQty_<? echo $i; ?>" class="text_boxes_numeric"
					style="width:55px" value="<? echo number_format($gray_qty_with_process_loss, 4, '.', ''); ?>" title="<? echo number_format($gray_qty_with_process_loss, 4, '.', ''); ?>"
					readonly/>
				</td>
				<td width="82">
					<?
					echo create_drop_down("cboWorkScope_" . $i, 80, $item_category, "", 1, "-- Select --", 2, "", "0", "2,13", "", "", "", "", "", "cboWorkScope[]");
					?>
				</td>
				<td width="100">
					<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" value="<? echo $process_name; ?>" readonly />
					<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>" />
					<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="<? echo $row[csf('process_seq')]; ?>" />
				</td>
				<td width="100">
					<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
					style="width:90px" value="<? echo $pre_cost_remarks; ?>" title="<? echo $pre_cost_remarks; ?>" readonly/>
				</td>
				<td>
					<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
					class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
					<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes"
					readonly/>
					<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
					id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>"
					value="<? echo $pre_cost_fabric_cost_dtls_id; ?>" class="text_boxes" readonly/>
				</td>
			</tr>
			<?
			$i++;
		}
	}
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";
		$cbo_within_group_combo = str_replace("'", "", $cbo_within_group);
		$season_val="'".str_replace("'", "", $season_val)."'";

		$check_if_booking_already_used_in_fso = return_field_value("job_no", "fabric_sales_order_mst", "sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0","job_no");
		if($check_if_booking_already_used_in_fso !=""){
			echo "5**Sales Order found against this Booking NO.\nSales Order No = $check_if_booking_already_used_in_fso";
			die;
		}

		$id = return_next_id_by_sequence("FABRIC_SALES_ORDER_MST_PK_SEQ", "fabric_sales_order_mst", $con);
		$new_sales_order_system_id = explode("*", return_next_id_by_sequence("FABRIC_SALES_ORDER_MST_PK_SEQ", "fabric_sales_order_mst",$con,1,$cbo_company_id,'FSOE',472,date("Y",time()),0 ));


		if( str_replace("'", "", $cbo_within_group) ==1  && str_replace("'", "", $booking_without_order)== 0)
		{
			$po_ref_data = sql_select("select a.id,a.buyer_id,a.company_id,a.entry_form, a.booking_type, a.booking_no,b.job_no
				from wo_booking_mst a,wo_booking_dtls b
				where a.booking_no = b.booking_no and a.booking_no = $txt_booking_no and b.status_active=1 and b.is_deleted = 0
				group by a.id,a.buyer_id,a.company_id,a.entry_form, a.booking_type, a.booking_no,b.job_no ");
			foreach ($po_ref_data as $val)
			{
				$po_buyer = $val[csf("buyer_id")];
				$po_company_id = $val[csf("company_id")];
				$po_job_no_arr[$val[csf("job_no")]] = $val[csf("job_no")];
				$booking_type_id = $val[csf("booking_type")];
				$booking_entry_form = $val[csf("entry_form")];
			}
		}
		else if( str_replace("'", "", $cbo_within_group) ==1  && str_replace("'", "", $booking_without_order)== 1)
		{
			$po_ref_data = sql_select(" select c.id, c.buyer_id, c.company_id, c.entry_form_id as entry_form, c.booking_type, c.booking_no, null as job_no
				from wo_non_ord_samp_booking_mst c
				where  c.booking_no = $txt_booking_no");
			foreach ($po_ref_data as $val)
			{
				$po_buyer = $val[csf("buyer_id")];
				$po_company_id = $val[csf("company_id")];
				$po_job_no_arr[$val[csf("job_no")]] = $val[csf("job_no")];
				$booking_type_id = $val[csf("booking_type")];
				$booking_entry_form = $val[csf("entry_form")];
			}
		}

		$po_job_no = implode(",",array_filter($po_job_no_arr));
		$barcode_year = date("y");

		$field_array = "id, job_no_prefix, job_no_prefix_num, job_no, company_id, within_group, sales_booking_no, booking_id, booking_date, delivery_start_date, delivery_date, buyer_id, style_ref_no, location_id, ship_mode, team_leader, dealing_marchant, remarks, currency_id, season_id,season, inserted_by, insert_date,entry_form,booking_without_order, po_buyer, po_company_id, po_job_no, booking_entry_form, booking_type,booking_approval_date, ready_to_approved, attention, sales_order_type, customer_buyer, customer_buyer_brand, season_year, ship_to, order_nature, from_fso_no, from_fso_id";

		$data_array = "(" . $id . ",'" . $new_sales_order_system_id[1] . "'," . $new_sales_order_system_id[2] . ",'" . $new_sales_order_system_id[0] . "'," . $cbo_company_id . "," . $cbo_within_group . "," . $txt_booking_no . "," . $txt_booking_no_id . "," . $txt_booking_date . "," . $txt_delivery_start_date. "," . $txt_delivery_date . "," . $cbo_buyer_name . "," . $txt_style_ref . "," . $cbo_location_name . "," . $cbo_ship_mode . "," . $cbo_team_leader . "," . $cbo_dealing_merchant . "," . $txt_remarks . "," . $cbo_currency . "," . $txt_season . "," . $season_val . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',472,".$booking_without_order.",'".$po_buyer."','".$po_company_id."','".$po_job_no."','".$booking_entry_form."','".$booking_type_id."',".$booking_approval_date.",".$cbo_ready_to_approved."," . $txt_attention . "," . $cbo_sales_order_type . "," . $cbo_cust_buyer_name . "," . $cbo_cust_buyer_brand .",". $cbo_season_year .",". $txt_ship_to .",". $cbo_order_nature .",". $txt_fso_ref .",". $txt_fso_ref_id . ")";

		$job_no = $new_sales_order_system_id[0];
		$job_update_id = $id;

		//$id_dtls = return_next_id("id", "fabric_sales_order_dtls", 1);

		if ($cbo_within_group_combo == 1) {
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty,rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom,pre_cost_fabric_cost_dtls_id, item_number_id,pre_cost_remarks, inserted_by, insert_date,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no,after_wash_gsm, cuttable_dia, pp_qnty, mtl_qnty, fpt_qnty, gpt_qnty, ccmp_data, ply, coller_cuff_cons, lab_dip_no";
		} else {
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty,rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom,pre_cost_remarks, inserted_by, insert_date,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no,after_wash_gsm, cuttable_dia, pp_qnty, mtl_qnty, fpt_qnty, gpt_qnty, ccmp_data, ply, coller_cuff_cons, lab_dip_no";
		}

		$field_array_coll_cuff = "id, mst_id, job_no_mst, dtls_id, coller_size, coller_qnty, cuff_size, cuff_qnty, coller_gmt_size, coller_ex_percent, cuff_gmt_size, cuff_ex_percent, inserted_by, insert_date";

		for ($i = 1; $i <= $total_row; $i++) {
			$cboBodyPart = "cboBodyPart" . $i;
			$cboColorType = "cboColorType" . $i;
			$txtFabricDesc = "txtFabricDesc" . $i;
			$fabricDescId = "fabricDescId" . $i;
			$txtFabricGsm = "txtFabricGsm" . $i;
			$txtFabricDia = "txtFabricDia" . $i;
			$cboDiaWidthType = "cboDiaWidthType" . $i;
			$txt_color = "txtColor" . $i;
			$colorId = "colorId" . $i;
			$cboColorRange = "cboColorRange" . $i;
			$txtFinishQty = "txtFinishQty" . $i;
			$txtAvgRate = "txtAvgRate" . $i;
			$txtAmount = "txtAmount" . $i;
			$txtProcessLoss = "txtProcessLoss" . $i;
			$txtGreyQty = "txtGreyQty" . $i;
			$cboWorkScope = "cboWorkScope" . $i;
			$cboUom = "cboUom" . $i;
			$cboConsUom = "cboConsUom" . $i;
			$txtRemarks = "txtRemarks" . $i;
			$txtProcessId = "txtProcessId" . $i;
			$txtProcessSeq = "txtProcessSeq" . $i;
			$txtProcessIdMain = "txtProcessIdMain" . $i;
			$txtProcessSeqMain = "txtProcessSeqMain" . $i;
			$booking_qnty_by_uom = "booking_qnty_by_uom" . $i;

            $txtAfterFabricGsm = "txtAfterFabricGsm" . $i;
            $txtFabricCuttableDia = "txtFabricCuttableDia" . $i;
            $txtPPQty = "txtPPQty" . $i;
            $txtMTLQty = "txtMTLQty" . $i;
            $txtFPTQty = "txtFPTQty" . $i;
            $txtGPTQty = "txtGPTQty" . $i;
            $ccmpSaveData = "ccmpSaveData" . $i;
            $cboPly = "cboPly" . $i;
            $hdnCollerCuffCon = "hdnCollerCuffCon" . $i;
            $txtLabDipNo = "txtLabDipNo" . $i;



			if ($cbo_within_group_combo == 1) {
				$cboGarmItemId = "cboGarmItemId" . $i;
				$pre_cost_fabric_cost_dtls_id = "pre_cost_fabric_cost_dtls_id" . $i;
			}

			$rmgQty = "rmgQty" . $i;

			if (str_replace("'", '', $color_from_library) == 2 && str_replace("'", '', $cbo_within_group) == 2) 
			{				
				if(str_replace("'","",$$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txt_color), $color_library, "lib_color", "id,color_name","109");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txt_color);

					}
					else $color_id =  array_search(str_replace("'","",$$txt_color), $new_array_color);
				}
				else
				{
					$color_id=0;
				}
			} else {
				$color_id = $$colorId;
			}
			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "fabric_sales_order_dtls",$con,1,0,'',109,date("Y",time()),13 ));
			$barcode_no = $barcode_year . "109" . str_pad($barcode_suffix_no[2], 6, "0", STR_PAD_LEFT);

			if ($cbo_within_group_combo == 1) {
				$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);

				$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "'," . $$pre_cost_fabric_cost_dtls_id . "," . $$cboGarmItemId . ",'" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','". $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ",'" . $$txtAfterFabricGsm ."','". $$txtFabricCuttableDia . "','" . $$txtPPQty . "','" . $$txtMTLQty . "','" . $$txtFPTQty . "','". $$txtGPTQty . "','". $$ccmpSaveData ."','".$$cboPly."','".$$hdnCollerCuffCon ."','" .$$txtLabDipNo. "')";
			} else {
				$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);
				$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "','" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','". $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ",'" . $$txtAfterFabricGsm ."','". $$txtFabricCuttableDia . "','" . $$txtPPQty . "','" . $$txtMTLQty . "','" . $$txtFPTQty . "','". $$txtGPTQty . "','". $$ccmpSaveData ."','".$$cboPly."','".$$hdnCollerCuffCon ."','" .$$txtLabDipNo. "')";
			}

			if($$ccmpSaveData != "")
			{
				
				$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
				foreach ($coll_cuff_arr as $ccm) 
				{
					$ccm_arr = explode("_", $ccm);
					$coller_size = $ccm_arr[0];
					$coller_qnty = $ccm_arr[1];
					$cuff_size = $ccm_arr[2];
					$cuff_qnty = $ccm_arr[3];
					$coller_gmt_size = $ccm_arr[4];
					$coller_ex_percent = $ccm_arr[5];
					$cuff_gmt_size = $ccm_arr[6];
					$cuff_ex_percent = $ccm_arr[7];

					if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
					$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
					$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . $id_dtls . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "','" .$coller_gmt_size . "','" .$coller_ex_percent . "','" .$cuff_gmt_size . "','" .$cuff_ex_percent ."',". $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}
		}


		$rID3=1;
		$rID = sql_insert("fabric_sales_order_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("fabric_sales_order_dtls", $field_array_dtls, $data_array_dtls, 1);
		if($data_array_coll_cuff != ""){
			$rID3 = sql_insert("fabric_sales_order_coller_cuff", $field_array_coll_cuff, $data_array_coll_cuff, 1);
		}

		//echo "10**";
		//echo "10**insert into fabric_sales_order_mst (".$field_array.") values ".$data_array;"<br>";
		//echo "10**insert into fabric_sales_order_dtls (".$field_array_dtls.") values ".$data_array_dtls; 
		//echo "10**insert into fabric_sales_order_coller_cuff (".$field_array_coll_cuff.") values ".$data_array_coll_cuff; 
		//oci_rollback($con); die;


		//echo "5**"."$rID && $rID2 && $rID3"; oci_rollback($con);die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3) {
				mysql_query("COMMIT");
				echo "0**" . $job_update_id . "**" . $job_no;
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "0**" . $job_update_id . "**" . $job_no;
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$job_no = str_replace("'", "", $txt_job_no);
		$job_update_id = str_replace("'", "", $update_id);
		$cbo_within_group_combo = str_replace("'", "", $cbo_within_group);

		// for within group no: check if program found while changing sales order info
		if ($cbo_within_group_combo == 2) {
			if(str_replace("'", "", $txt_booking_no) != str_replace("'", "", $txt_hdn_booking_no)){
				$check_planning_info = return_field_value("b.booking_no booking_no", "ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_mst b", "a.mst_id=b.id and a.po_id=$job_update_id and b.booking_no=$txt_hdn_booking_no and a.within_group=2 and a.status_active=1 and a.is_deleted=0 group by b.booking_no","booking_no");
				if($check_planning_info !=""){
					echo "5**Program found. Sales/Booking No can not be changed";
					die;
				}
			}
		}

		// check if Receive found while changing sales order info
		$check_recv_info = sql_select("SELECT recv_number as RECV_NUMBER from inv_receive_master where entry_form=22 and receive_basis=14 and status_active=1 and booking_no = $txt_job_no");
		if(!empty($check_recv_info))
		{
			$grey_recv_number="";
			foreach ($check_recv_info as $val) 
			{
				($grey_recv_number =="") ? $grey_recv_number= $val["RECV_NUMBER"] : $grey_recv_number .= ", ".$val["RECV_NUMBER"];
			}
			echo "5**Grey receive found. Sales Order can not be changed\nGrey receive no : ".$grey_recv_number;
			die;
		}
		//echo "10**";die;
		// sales order table query for data preparation
		$sales_info = sql_select("select update_sl, is_master_part_updated, revise_no from fabric_sales_order_mst where id='" . $job_update_id . "'");
		$update_serial = $sales_info[0][csf("update_sl")];
		$revise_no = $sales_info[0][csf("revise_no")];

		$is_apply_last_update = str_replace("'", "", $is_apply_last_update);
		if ($is_apply_last_update == 1) {
			$is_master_part_updated = 1;
		} else {
			if ($sales_info[0][csf("is_master_part_updated")] != 1) {
				$is_master_part_updated = 0;
			} else {
				$is_master_part_updated = 1;
			}
		}

		if( str_replace("'", "", $cbo_within_group) ==1  && str_replace("'", "", $booking_without_order)== 0)
		{
			$po_ref_data = sql_select("select a.id,a.buyer_id,a.company_id,a.entry_form, a.booking_type, a.booking_no,b.job_no
				from wo_booking_mst a,wo_booking_dtls b
				where a.booking_no = b.booking_no and a.booking_no = $txt_booking_no and b.status_active=1 and b.is_deleted = 0
				group by a.id,a.buyer_id,a.company_id,a.entry_form, a.booking_type, a.booking_no,b.job_no ");
			foreach ($po_ref_data as $val)
			{
				$po_buyer = $val[csf("buyer_id")];
				$po_company_id = $val[csf("company_id")];
				$po_job_no_arr[$val[csf("job_no")]] = $val[csf("job_no")];
				$booking_type_id = $val[csf("booking_type")];
				$booking_entry_form = $val[csf("entry_form")];
			}
		}
		else if( str_replace("'", "", $cbo_within_group) ==1  && str_replace("'", "", $booking_without_order)== 1)
		{
			$po_ref_data = sql_select("select c.id, c.buyer_id, c.company_id, c.entry_form_id as entry_form, c.booking_type, c.booking_no, null as job_no
				from wo_non_ord_samp_booking_mst c where  c.booking_no = $txt_booking_no");
			foreach ($po_ref_data as $val)
			{
				$po_buyer = $val[csf("buyer_id")];
				$po_company_id = $val[csf("company_id")];
				$po_job_no_arr[$val[csf("job_no")]] = $val[csf("job_no")];
				$booking_type_id = $val[csf("booking_type")];
				$booking_entry_form = $val[csf("entry_form")];
			}
		}

		$po_job_no = implode(",",array_filter($po_job_no_arr));

		if ($is_apply_last_update == 1) {
			$field_array_update = "company_id*within_group*sales_booking_no*booking_id*booking_date*delivery_start_date*delivery_date*buyer_id*style_ref_no*location_id*ship_mode*team_leader*dealing_marchant*remarks*currency_id*season_id*season*updated_by*update_date*update_sl*is_master_part_updated*is_apply_last_update*po_buyer*po_company_id*po_job_no*booking_entry_form*booking_type*revise_no*booking_approval_date*ready_to_approved*attention*sales_order_type*customer_buyer*customer_buyer_brand*season_year*ship_to*order_nature*from_fso_no*from_fso_id";

			$data_array_update = $cbo_company_id . "*" . $cbo_within_group . "*" . $txt_booking_no . "*" . $txt_booking_no_id . "*" . $txt_booking_date . "*" .$txt_delivery_start_date. "*". $txt_delivery_date . "*" . $cbo_buyer_name . "*" . $txt_style_ref . "*" . $cbo_location_name . "*" . $cbo_ship_mode . "*" . $cbo_team_leader . "*" . $cbo_dealing_merchant . "*" . $txt_remarks . "*" . $cbo_currency . "*" . $txt_season . "*'" . $season_val . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . ($update_serial+1) . "*" . $is_master_part_updated."*0*'".$po_buyer."'*'".$po_company_id."'*'".$po_job_no."'*'".$booking_entry_form."'*'".$booking_type_id."'*".($revise_no+1)."*".$booking_approval_date."*".$cbo_ready_to_approved."*".$txt_attention."*".$cbo_sales_order_type."*".$cbo_cust_buyer_name . "*" . $cbo_cust_buyer_brand ."*". $cbo_season_year ."*". $txt_ship_to ."*". $cbo_order_nature ."*". $txt_fso_ref ."*". $txt_fso_ref_id;
		}else{
			$field_array_update = "company_id*within_group*sales_booking_no*booking_id*booking_date*delivery_start_date*delivery_date*buyer_id*style_ref_no*location_id*ship_mode*team_leader*dealing_marchant*remarks*currency_id*season_id*season*updated_by*update_date*update_sl*is_master_part_updated*po_buyer*po_company_id*po_job_no*booking_entry_form*booking_type*booking_approval_date*ready_to_approved*attention*sales_order_type*customer_buyer*customer_buyer_brand*season_year*ship_to*order_nature*from_fso_no*from_fso_id";

			$data_array_update = $cbo_company_id . "*" . $cbo_within_group . "*" . $txt_booking_no . "*" . $txt_booking_no_id . "*" . $txt_booking_date . "*" .$txt_delivery_start_date . "*" . $txt_delivery_date . "*" . $cbo_buyer_name . "*" . $txt_style_ref . "*" . $cbo_location_name . "*" . $cbo_ship_mode . "*" . $cbo_team_leader . "*" . $cbo_dealing_merchant . "*" . $txt_remarks . "*" . $cbo_currency . "*" . $txt_season . "*'" . $season_val . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . ($update_serial+1) . "*" . $is_master_part_updated."*'".$po_buyer."'*'".$po_company_id."'*'".$po_job_no."'*'".$booking_entry_form."'*'".$booking_type_id."'*".$booking_approval_date."*".$cbo_ready_to_approved."*".$txt_attention."*".$cbo_sales_order_type."*".$cbo_cust_buyer_name . "*" . $cbo_cust_buyer_brand ."*". $cbo_season_year ."*". $txt_ship_to ."*". $cbo_order_nature ."*". $txt_fso_ref ."*". $txt_fso_ref_id;
		}
		$barcode_year = date("y");

		if ($cbo_within_group_combo == 1) {
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty, rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom, pre_cost_fabric_cost_dtls_id, item_number_id, pre_cost_remarks, inserted_by, insert_date,status_active,is_deleted,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no,after_wash_gsm, cuttable_dia, pp_qnty, mtl_qnty, fpt_qnty, gpt_qnty, ccmp_data, ply, coller_cuff_cons, lab_dip_no";
		} else {
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty, rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom, pre_cost_remarks, inserted_by, insert_date,status_active,is_deleted,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no,after_wash_gsm, cuttable_dia, pp_qnty, mtl_qnty, fpt_qnty, gpt_qnty, ccmp_data, ply, coller_cuff_cons, lab_dip_no";
		}

		$field_array_coll_cuff = "id, mst_id, job_no_mst, dtls_id, coller_size, coller_qnty, cuff_size, cuff_qnty, coller_gmt_size, coller_ex_percent, cuff_gmt_size, cuff_ex_percent, inserted_by, insert_date";

		// prepare sales order data by sales order id
		$check_sales_dtls = sql_select("select a.*,b.within_group from fabric_sales_order_dtls a,fabric_sales_order_mst b where a.mst_id=b.id and a.mst_id=$job_update_id and a.status_active=1 and a.is_deleted=0");
		foreach ($check_sales_dtls as $orows) {
			if($orows[csf("within_group")] == 1){
				$old_data[$orows[csf("pre_cost_fabric_cost_dtls_id")]][$orows[csf('item_number_id')]][$orows[csf('body_part_id')]][$orows[csf('gsm_weight')]][$orows[csf('dia')]][$orows[csf('color_type_id')]][$orows[csf('fabric_desc')]][$orows[csf('width_dia_type')]][$orows[csf('color_id')]][$orows[csf('pre_cost_remarks')]]['id'] = $orows[csf('id')];
				$oldids[$orows[csf('id')]] = $orows[csf('id')];
			}else{
				$old_data[$orows[csf('body_part_id')]][$orows[csf('gsm_weight')]][$orows[csf('dia')]][$orows[csf('color_type_id')]][$orows[csf('fabric_desc')]][$orows[csf('width_dia_type')]][$orows[csf('color_id')]]['id'] = $orows[csf('id')];

				$old_id_desc[$orows[csf('id')]] = $orows[csf('body_part_id')]."*".$orows[csf('color_type_id')]."*".$orows[csf('determination_id')]."*".$orows[csf('gsm_weight')]."*".$orows[csf('dia')]."*".$orows[csf('fabric_desc')]."*".$orows[csf('width_dia_type')]."*".$orows[csf('color_id')]."*".$orows[csf('color_range_id')]."*".$orows[csf('finish_qty')]."*".$orows[csf('rmg_qty')]."*".$orows[csf('avg_rate')]."*".$orows[csf('amount')]."*".$orows[csf('process_loss')]."*".$orows[csf('grey_qty')]."*".$orows[csf('work_scope')]."*".$orows[csf('order_uom')]."*".$orows[csf('pre_cost_remarks')];
			}
		}

		if ($cbo_within_group_combo == 1) {
			$field_array_dtls_update = "body_part_id*color_type_id*fabric_desc*determination_id*gsm_weight*dia*width_dia_type*color_id*color_range_id*finish_qty*rmg_qty*avg_rate*amount*process_loss*grey_qty*work_scope*order_uom*pre_cost_fabric_cost_dtls_id*item_number_id*pre_cost_remarks*updated_by*update_date*status_active*is_deleted*grey_qnty_by_uom*cons_uom*process_id*process_seq*process_id_main*process_seq_main*after_wash_gsm*cuttable_dia*pp_qnty*mtl_qnty*fpt_qnty*gpt_qnty*ccmp_data*ply*coller_cuff_cons*lab_dip_no";
		} else {
			$field_array_dtls_update = "body_part_id*color_type_id*fabric_desc*determination_id*gsm_weight*dia*width_dia_type*color_id*color_range_id*finish_qty*rmg_qty*avg_rate*amount*process_loss*grey_qty*work_scope*order_uom*pre_cost_remarks*updated_by*update_date*status_active*is_deleted*grey_qnty_by_uom*cons_uom*process_id*process_seq*process_id_main*process_seq_main*after_wash_gsm*cuttable_dia*pp_qnty*mtl_qnty*fpt_qnty*gpt_qnty*ccmp_data*ply*coller_cuff_cons*lab_dip_no";
		}
		$is_new = 0;
		$dtls_ids_to_delete = "";
		$allocation_product_qnty_arr = $product_qnty_arr = array();
		for ($i = 1; $i <= $total_row; $i++) {
			$cboBodyPart = "cboBodyPart" . $i;
			$cboColorType = "cboColorType" . $i;
			$txtFabricDesc = "txtFabricDesc" . $i;
			$fabricDescId = "fabricDescId" . $i;
			$txtFabricGsm = "txtFabricGsm" . $i;
			$txtFabricDia = "txtFabricDia" . $i;
			$cboDiaWidthType = "cboDiaWidthType" . $i;
			$txt_color = "txtColor" . $i;
			$colorId = "colorId" . $i;
			$cboColorRange = "cboColorRange" . $i;
			$txtFinishQty = "txtFinishQty" . $i;
			$txtAvgRate = "txtAvgRate" . $i;
			$txtAmount = "txtAmount" . $i;
			$txtProcessLoss = "txtProcessLoss" . $i;
			$txtGreyQty = "txtGreyQty" . $i;
			$cboWorkScope = "cboWorkScope" . $i;
			$updateIdDtls = "updateIdDtls" . $i;
			$cboUom = "cboUom" . $i;
			$cboConsUom = "cboConsUom" . $i;
			$txtRemarks = "txtRemarks" . $i;
			$txtProcessId = "txtProcessId" . $i;
			$txtProcessSeq = "txtProcessSeq" . $i;
			$txtProcessIdMain = "txtProcessIdMain" . $i;
			$txtProcessSeqMain = "txtProcessSeqMain" . $i;
			$booking_qnty_by_uom = "booking_qnty_by_uom" . $i;
			$rmgQty = "rmgQty" . $i;
			if ($cbo_within_group_combo == 1) {
				$cboGarmItemId = "cboGarmItemId" . $i;
				$pre_cost_fabric_cost_dtls_id = "pre_cost_fabric_cost_dtls_id" . $i;
			}

			$txtAfterFabricGsm = "txtAfterFabricGsm" . $i;
            $txtFabricCuttableDia = "txtFabricCuttableDia" . $i;
            $txtPPQty = "txtPPQty" . $i;
            $txtMTLQty = "txtMTLQty" . $i;
            $txtFPTQty = "txtFPTQty" . $i;
            $txtGPTQty = "txtGPTQty" . $i;
            $ccmpSaveData = "ccmpSaveData" . $i;
            $cboPly = "cboPly" . $i;
            $hdnCollerCuffCon = "hdnCollerCuffCon" . $i;
            $txtLabDipNo = "txtLabDipNo" . $i;

			if (str_replace("'", '', $color_from_library) == 2 && str_replace("'", '', $cbo_within_group) == 2)
			{				
				if(str_replace("'","",$$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txt_color), $color_library, "lib_color", "id,color_name","109");
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txt_color);

					}
					else $color_id =  array_search(str_replace("'","",$$txt_color), $new_array_color);
				}
				else
				{
					$color_id=0;
				}
			} else {
				$color_id = $$colorId;
			}
			$dia = $$txtFabricDia;
			$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "fabric_sales_order_dtls",$con,1,0,'',109,date("Y",time()),13 ));
			$barcode_no = $barcode_year . "109" . str_pad($barcode_suffix_no[2], 6, "0", STR_PAD_LEFT);

			if ($cbo_within_group_combo == 1) 
			{
				if (str_replace("'", "", $$updateIdDtls) == "") 
				{
					$pre_cost_dtls_id = $$pre_cost_fabric_cost_dtls_id;
					$is_new_arr = array();
					$changed_precost_ids = '';
					$is_old_arr = array();
					// if fabric details is changed in pre-cost
					if ($old_data[$pre_cost_dtls_id][$$cboGarmItemId][$$cboBodyPart][$$txtFabricGsm][$$txtFabricDia][$$cboColorType][$$txtFabricDesc][$$cboDiaWidthType][$color_id][$$txtRemarks]['id'] == '') 
					{
						// get the changed pre-cost ids
						$changed_precost_ids = $pre_cost_dtls_id;
						// prepare new sales data for entry
						$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);

						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "'," . $$txtFabricGsm . ",'" . $dia . "','" . $$cboDiaWidthType . "','" . $color_id . "'," . $$cboColorRange . ",'" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "'," . $$pre_cost_fabric_cost_dtls_id . "," . $$cboGarmItemId . ",'" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','" . $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no .",'".$$txtAfterFabricGsm ."','". $$txtFabricCuttableDia . "','" . $$txtPPQty . "','" . $$txtMTLQty . "','" . $$txtFPTQty . "','". $$txtGPTQty . "','". $$ccmpSaveData."','" . $$cboPly . "','" . $$hdnCollerCuffCon . "','" . $$txtLabDipNo. "')";

						$dtlsId = $id_dtls;

						if($$ccmpSaveData != "")
						{
							$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
							foreach ($coll_cuff_arr as $ccm) 
							{
								$ccm_arr = explode("_", $ccm);
								$coller_size = $ccm_arr[0];
								$coller_qnty = $ccm_arr[1];
								$cuff_size = $ccm_arr[2];
								$cuff_qnty = $ccm_arr[3];
								$coller_gmt_size = $ccm_arr[4];
								$coller_ex_percent = $ccm_arr[5];
								$cuff_gmt_size = $ccm_arr[6];
								$cuff_ex_percent = $ccm_arr[7];

								if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
								$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
								$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . $id_dtls . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "','" .$coller_gmt_size."','" .$coller_ex_percent."','" .$cuff_gmt_size."','" .$cuff_ex_percent."',". $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							}
						}
					} 
					else 
					{
						$tid = $old_data[$pre_cost_dtls_id][$$cboGarmItemId][$$cboBodyPart][$$txtFabricGsm][$$txtFabricDia][$$cboColorType][$$txtFabricDesc][$$cboDiaWidthType][$color_id][$$txtRemarks]['id'];
						$is_old_arr[] = $tid;
						unset ($oldids[$tid]);

						execute_query("update fabric_sales_order_dtls set body_part_id='" . $$cboBodyPart . "',color_type_id='" . $$cboColorType . "',fabric_desc='" . $$txtFabricDesc . "',determination_id='" . $$fabricDescId . "',gsm_weight='" . $$txtFabricGsm . "',dia='" . $dia . "',width_dia_type='" . $$cboDiaWidthType . "', color_id='" . $color_id . "',color_range_id='" . $$cboColorRange . "', finish_qty='" . $$txtFinishQty . "', rmg_qty='" . $$rmgQty . "', avg_rate='" . $$txtAvgRate . "', amount='" . $$txtAmount . "', process_loss='" . $$txtProcessLoss . "', grey_qty='" . $$txtGreyQty . "',work_scope='" . $$cboWorkScope . "', updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "', grey_qnty_by_uom=" . $$booking_qnty_by_uom . ", cons_uom=" . $$cboConsUom . ", process_id='" . $$txtProcessId . "', process_seq='" . $$txtProcessSeq . "', process_id_main='". $$txtProcessIdMain ."', process_seq_main='" . $$txtProcessSeqMain . "', after_wash_gsm='".$$txtAfterFabricGsm."', cuttable_dia='".$$txtFabricCuttableDia."', pp_qnty='".$$txtPPQty."', mtl_qnty='".$$txtMTLQty."', fpt_qnty='".$$txtFPTQty."', gpt_qnty='".$$txtGPTQty."', ccmp_data='".$$ccmpSaveData."', ply='".$$cboPly."',coller_cuff_cons='".$$hdnCollerCuffCon."', lab_dip_no='".$$txtLabDipNo."' where id in (" . $tid . ")", 0);
						oci_commit($con);


						// Has some confusion about below add

						if($$ccmpSaveData != "")
						{
							$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
							foreach ($coll_cuff_arr as $ccm) 
							{
								$ccm_arr = explode("_", $ccm);
								$coller_size = $ccm_arr[0];
								$coller_qnty = $ccm_arr[1];
								$cuff_size = $ccm_arr[2];
								$cuff_qnty = $ccm_arr[3];

								if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
								$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
								$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . $tid . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							}
						}
					}

					if ($is_apply_last_update == 1) 
					{
						// get all programs by sales order,booking and pre-cost id
						$check_program_in_req_issue = sql_select("select a.id,a.dtls_id,listagg(b.id,',') within group (order by b.id) as  requisition_id, listagg(b.prod_id,',') within group (order by b.id) as  requisition_prod_id,listagg(b.yarn_qnty,',') within group (order by b.id) as  requisition_qnty,listagg(d.mst_id,',') within group (order by d.mst_id) as  issue_id, listagg(c.id,',') within group (order by c.id) as  production_id,a.pre_cost_fabric_cost_dtls_id  from ppl_planning_entry_plan_dtls a left join ppl_yarn_requisition_entry b on a.dtls_id=b.knit_id left join inv_transaction d on b.requisition_no=d.requisition_no left join inv_receive_master c on (a.dtls_id=c.booking_id and a.company_id=c.company_id and c.entry_form=2 and c.item_category=13 and c.receive_basis=2) where a.po_id=$update_id and a.booking_no=$txt_booking_no and a.pre_cost_fabric_cost_dtls_id=$pre_cost_dtls_id group by a.id,a.dtls_id, a.pre_cost_fabric_cost_dtls_id");

						$user_id = $_SESSION['logic_erp']['user_id'];
						if (!empty($check_program_in_req_issue)) {
							foreach ($check_program_in_req_issue as $row) {
								$dtls_id = $row[csf('dtls_id')];
								$req_ids = explode(",",$row[csf('requisition_id')]);
								$requisition_prod_id = explode(",",$row[csf('requisition_prod_id')]);
								$requisition_qnty = explode(",",$row[csf('requisition_qnty')]);

								if (($row[csf('dtls_id')] != '') && ($row[csf('requisition_id')] == '') && ($row[csf('issue_id')] == '') && ($row[csf('production_id')] == '')) {
									if ($changed_precost_ids == $row[csf("pre_cost_fabric_cost_dtls_id")]) {
										// program delete if only program
										execute_query("update ppl_planning_info_entry_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where dtls_id=$dtls_id", 0);
									}
								} else if (($row[csf('dtls_id')] != '') && ($row[csf('requisition_id')] != '') && ($row[csf('issue_id')] == '') && ($row[csf('production_id')] == '')) {
									// program & requisition delete if (Program + Requisition) found
									if ($changed_precost_ids == $row[csf("pre_cost_fabric_cost_dtls_id")]) {
										execute_query("update ppl_planning_info_entry_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where dtls_id=$dtls_id", 0);
										execute_query("update ppl_yarn_requisition_entry set updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$req_id", 0);
										execute_query("update ppl_yarn_requisition_entry set updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$req_id", 0);

										// get product ids to decrease allocation quantity of requisition lot
										$d=0;
										foreach ($req_ids as $req_id) {
											execute_query("update ppl_yarn_requisition_entry set updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$req_id", 0);
											if(!in_array($requisition_prod_id[$d],$allocation_prod_qnty_arr)){
												$allocation_product_qnty_arr[$requisition_prod_id[$d]] = $requisition_qnty[$d];
												$product_qnty_arr[$requisition_prod_id[$d]] = $requisition_qnty[$d];
											}
											$d++;
										}
									}
								} else if (($row[csf('dtls_id')] != '') && ($row[csf('requisition_id')] != '') && ($row[csf('issue_id')] != '') && ($row[csf('production_id')] == '')) {
									if ($changed_precost_ids == $row[csf("pre_cost_fabric_cost_dtls_id")]) {
										// "program delete if (Program + Requisition + Issue) found";
										execute_query("update ppl_planning_info_entry_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1,is_issued=1 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1,is_issued=1 where dtls_id=$dtls_id", 0);
									}
								} else if (($row[csf('dtls_id')] != '') && ($row[csf('requisition_id')] != '') && ($row[csf('issue_id')] == '') && ($row[csf('production_id')] != '')) {
									if ($changed_precost_ids == $row[csf("pre_cost_fabric_cost_dtls_id")]) {
										// "program revised if (Program + Requisition + Production) found";
										execute_query("update ppl_planning_info_entry_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where dtls_id=$dtls_id", 0);
									}
								} else if (($row[csf('dtls_id')] != '') && ($row[csf('requisition_id')] != '') && ($row[csf('issue_id')] != '') && ($row[csf('production_id')] != '')) {
									if ($changed_precost_ids == $row[csf("pre_cost_fabric_cost_dtls_id")]) {
										// "program revised if (Program + Requisition + Issue + Production) found";
										execute_query("update ppl_planning_info_entry_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where dtls_id=$dtls_id", 0);
									}
								} else if (($row[csf('dtls_id')] != '') && ($row[csf('requisition_id')] == '') && ($row[csf('issue_id')] == '') && ($row[csf('production_id')] != '')) {
									// "program revised if (Program + Production) found";
									if ($changed_precost_ids == $row[csf("pre_cost_fabric_cost_dtls_id")]) {
										execute_query("update ppl_planning_info_entry_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where dtls_id=$dtls_id", 0);
									}
								} else {
									$rID6 = 1;
									$rID7 = 1;
									$rID8 = 1;
								}
							}
						}
					} else {
						$rID6 = 1;
						$rID7 = 1;
					}
					$rID3=1;
				} 
				else 
				{
					$dtlsId = str_replace("'", '', $$updateIdDtls);
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("'" . $$cboBodyPart . "'*'" . $$cboColorType . "'*'" . $$txtFabricDesc . "'*'" . $$fabricDescId . "'*'" . $$txtFabricGsm . "'*'" . $$txtFabricDia . "'*'" . $$cboDiaWidthType . "'*'" . $color_id . "'*'" . $$cboColorRange . "'*'" . $$txtFinishQty . "'*'" . $$rmgQty . "'*'" . $$txtAvgRate . "'*'" . $$txtAmount . "'*'" . $$txtProcessLoss . "'*'" . $$txtGreyQty . "'*'" . $$cboWorkScope . "'*'" . $$cboUom . "'*" . $$pre_cost_fabric_cost_dtls_id . "*" . $$cboGarmItemId . "*'" . $$txtRemarks . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0*" . $$booking_qnty_by_uom . "*" . $$cboConsUom. "*'" . $$txtProcessId. "'*'" . $$txtProcessSeq. "'*'" .$$txtProcessIdMain ."'*'" . $$txtProcessSeqMain ."'*'" .$$txtAfterFabricGsm ."'*'". $$txtFabricCuttableDia . "'*'" . $$txtPPQty . "'*'" . $$txtMTLQty . "'*'" . $$txtFPTQty . "'*'". $$txtGPTQty . "'*'". $$ccmpSaveData ."'*'".$$cboPly."'*'" .$$hdnCollerCuffCon."'*'" .$$txtLabDipNo. "'"));
					$rID2 = 1;
					
					if($$ccmpSaveData != "")
					{
						$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
						foreach ($coll_cuff_arr as $ccm) 
						{
							$ccm_arr = explode("_", $ccm);
							$coller_size = $ccm_arr[0];
							$coller_qnty = $ccm_arr[1];
							$cuff_size = $ccm_arr[2];
							$cuff_qnty = $ccm_arr[3];
							$coller_gmt_size = $ccm_arr[4];
							$coller_ex_percent = $ccm_arr[5];
							$cuff_gmt_size = $ccm_arr[6];
							$cuff_ex_percent = $ccm_arr[7];

							if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
							$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
							$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . str_replace("'", '', $$updateIdDtls) . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "','" .$coller_gmt_size. "','" .$coller_ex_percent. "','" .$cuff_gmt_size. "','" .$cuff_ex_percent."',". $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						}
					}
				}
			} 
			else 
			{
                // WITHIN GROUP = NO
				if (str_replace("'", "", $$updateIdDtls) == "") 
				{
					$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "','" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','" . $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ",'" . $$txtAfterFabricGsm ."','". $$txtFabricCuttableDia . "','" . $$txtPPQty . "','" . $$txtMTLQty . "','" . $$txtFPTQty . "','". $$txtGPTQty . "','". $$ccmpSaveData ."','".$$cboPly."','".$$hdnCollerCuffCon."','".$$txtLabDipNo. "')";

					if($$ccmpSaveData != "")
					{
						$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
						foreach ($coll_cuff_arr as $ccm) 
						{
							$ccm_arr = explode("_", $ccm);
							$coller_size = $ccm_arr[0];
							$coller_qnty = $ccm_arr[1];
							$cuff_size = $ccm_arr[2];
							$cuff_qnty = $ccm_arr[3];
							$coller_gmt_size = $ccm_arr[4];
							$coller_ex_percent = $ccm_arr[5];
							$cuff_gmt_size = $ccm_arr[6];
							$cuff_ex_percent = $ccm_arr[7];

							if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
							$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
							$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . $id_dtls . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "','" .$coller_gmt_size. "','" .$coller_ex_percent. "','" .$cuff_gmt_size. "','" .$cuff_ex_percent. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						}
					}


					$id_dtls = $id_dtls + 1;
					$rID2 = 1;
				} 
				else 
				{
					$rID2 = 1;
					$get_existing_dtls_row = explode("*",$old_id_desc[str_replace("'", "", $$updateIdDtls)]);
					if(($get_existing_dtls_row[0] != $$cboBodyPart) || ($get_existing_dtls_row[1] != $$cboColorType) || ($get_existing_dtls_row[2] != $$fabricDescId) || ($get_existing_dtls_row[3] != $$txtFabricGsm) || ($get_existing_dtls_row[4] != $$txtFabricDia) ||($get_existing_dtls_row[6] != $$cboDiaWidthType))
					{
						$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);
						$dtls_ids_to_delete .= ($dtls_ids_to_delete == "") ? $$updateIdDtls : "," . $$updateIdDtls;
						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "','" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','" . $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ",'" . $$txtAfterFabricGsm ."','". $$txtFabricCuttableDia . "','" . $$txtPPQty . "','" . $$txtMTLQty . "','" . $$txtFPTQty . "','". $$txtGPTQty . "','". $$ccmpSaveData ."','".$$cboPly."','".$$hdnCollerCuffCon."','".$$txtLabDipNo. "')";

						if($$ccmpSaveData != "")
						{
							$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
							foreach ($coll_cuff_arr as $ccm) 
							{
								$ccm_arr = explode("_", $ccm);
								$coller_size = $ccm_arr[0];
								$coller_qnty = $ccm_arr[1];
								$cuff_size = $ccm_arr[2];
								$cuff_qnty = $ccm_arr[3];
								$coller_gmt_size = $ccm_arr[4];
								$coller_ex_percent = $ccm_arr[5];
								$cuff_gmt_size = $ccm_arr[6];
								$cuff_ex_percent = $ccm_arr[7];

								if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
								$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
								$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . $id_dtls . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "','" .$coller_gmt_size. "','" .$coller_ex_percent. "','" .$cuff_gmt_size. "','" .$cuff_ex_percent. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							}
						}

						//$id_dtls = $id_dtls + 1;
						$changed_dtls_desc = $get_existing_dtls_row[0]."*".$get_existing_dtls_row[3]."*".$get_existing_dtls_row[4]."*".$get_existing_dtls_row[1]."*".trim($get_existing_dtls_row[5]," ")."*".$get_existing_dtls_row[6];

						// get all programs by sales order,booking and pre-cost id
						$check_program_in_req_issue = sql_select("select a.id dtls_id,e.body_part_id,a.color_id,e.color_type_id,e.determination_id,e.dia, e.fabric_desc,e.gsm_weight,e.width_dia_type,listagg(b.id,',') within group (order by b.id) as requisition_id,listagg(b.prod_id,',') within group (order by b.id) as  requisition_prod_id,listagg(b.yarn_qnty,',') within group (order by b.id) as  requisition_qnty,listagg(d.mst_id,',') within group (order by d.mst_id) as  issue_id, listagg(c.id,',') within group (order by c.id) as  production_id from ppl_planning_info_entry_dtls a left join ppl_planning_info_entry_mst e on a.mst_id=e.id left join ppl_yarn_requisition_entry b on a.id=b.knit_id left join inv_transaction d on b.requisition_no=d.requisition_no left join inv_receive_master c on (a.id=c.booking_id and e.company_id=c.company_id and c.entry_form=2 and c.item_category=13 and c.receive_basis=2) where e.booking_no=$txt_booking_no group by a.id,e.body_part_id,a.color_id,e.color_type_id,e.determination_id,e.dia,e.fabric_desc, e.gsm_weight,e.width_dia_type");
						$user_id = $_SESSION['logic_erp']['user_id'];

						if (!empty($check_program_in_req_issue)) {
							foreach ($check_program_in_req_issue as $plan_row) {
								$dtls_id = $plan_row[csf('dtls_id')];
								$req_ids = explode(",",$plan_row[csf('requisition_id')]);
								$requisition_prod_id = explode(",",$plan_row[csf('requisition_prod_id')]);
								$requisition_qnty = explode(",",$plan_row[csf('requisition_qnty')]);
								$plan_desc = $plan_row[csf('body_part_id')]."*".$plan_row[csf('gsm_weight')]."*".$plan_row[csf('dia')]."*".$plan_row[csf('color_type_id')]."*".trim($plan_row[csf('fabric_desc')]," ")."*".$plan_row[csf('width_dia_type')];
								if (($plan_row[csf('dtls_id')] != '') && ($plan_row[csf('requisition_id')] == '') && ($plan_row[csf('issue_id')] == '') && ($plan_row[csf('production_id')] == '')) {
									if ($changed_dtls_desc == $plan_desc) {
                                    	// program delete if only program found
										execute_query("update ppl_planning_info_entry_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where dtls_id=$dtls_id", 0);
									}
								} else if (($plan_row[csf('dtls_id')] != '') && ($plan_row[csf('requisition_id')] != '') && ($plan_row[csf('issue_id')] == '') && ($plan_row[csf('production_id')] == '')) {
                                	// program & requisition delete if (Program + Requisition) found
									if ($changed_dtls_desc == $plan_desc) {
										execute_query("update ppl_planning_info_entry_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where dtls_id=$dtls_id", 0);

										// get product ids to decrease allocation quantity of requisition lot
										$d=0;
										foreach ($req_ids as $req_id) {
											execute_query("update ppl_yarn_requisition_entry set updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 where id=$req_id", 0);
											if(!in_array($requisition_prod_id[$d],$allocation_prod_qnty_arr)){
												$allocation_product_qnty_arr[$requisition_prod_id[$d]] = $requisition_qnty[$d];
												$product_qnty_arr[$requisition_prod_id[$d]] = $requisition_qnty[$d];
											}
											$d++;
										}
									}
								} else if (($plan_row[csf('dtls_id')] != '') && ($plan_row[csf('requisition_id')] != '') && ($plan_row[csf('issue_id')] != '') && ($plan_row[csf('production_id')] == '')) {
									if ($changed_dtls_desc == $plan_desc) {
                                    	// "program delete if (Program + Requisition + Issue) found";
										execute_query("update ppl_planning_info_entry_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1,is_issued=1 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=0, updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1,is_issued=1 where dtls_id=$dtls_id", 0);
									}
								} else if (($plan_row[csf('dtls_id')] != '') && ($plan_row[csf('requisition_id')] != '') && ($plan_row[csf('issue_id')] == '') && ($plan_row[csf('production_id')] != '')) {
									if ($changed_dtls_desc == $plan_desc) {
                                    	// "program revised if (Program + Requisition + Production) found";
										execute_query("update ppl_planning_info_entry_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where dtls_id=$dtls_id", 0);
									}
								} else if (($plan_row[csf('dtls_id')] != '') && ($plan_row[csf('requisition_id')] != '') && ($plan_row[csf('issue_id')] != '') && ($plan_row[csf('production_id')] != '')) {
									if ($changed_dtls_desc == $plan_desc) {
                                    	//echo "program revised if (Program + Requisition + Issue + Production) found";
										execute_query("update ppl_planning_info_entry_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where dtls_id=$dtls_id", 0);
									}
								} else if (($plan_row[csf('dtls_id')] != '') && ($plan_row[csf('requisition_id')] == '') && ($plan_row[csf('issue_id')] == '') && ($plan_row[csf('production_id')] != '')) {
                                	// "program revised if (Program + Production) found";
									if ($changed_dtls_desc == $plan_desc) {
										execute_query("update ppl_planning_info_entry_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where id=$dtls_id", 0);
										execute_query("update ppl_planning_entry_plan_dtls set is_revised=1, updated_by=$user_id, update_date='$pc_date_time', status_active=1, is_deleted=0 where dtls_id=$dtls_id", 0);
									}
								} else {
									$rID6 = 1;
									$rID7 = 1;
									$rID8 = 1;
								}
							}
						}
						$rID3=1;
					}
					else
					{
						$dtlsId = str_replace("'", '', $$updateIdDtls);
						$id_arr[] = str_replace("'", '', $$updateIdDtls);
						$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("'" . $$cboBodyPart . "'*'" . $$cboColorType . "'*'" . $$txtFabricDesc . "'*'" . $$fabricDescId . "'*'" . $$txtFabricGsm . "'*'" . $$txtFabricDia . "'*'" . $$cboDiaWidthType . "'*'" . $color_id . "'*'" . $$cboColorRange . "'*'" . $$txtFinishQty . "'*'" . $$rmgQty . "'*'" . $$txtAvgRate . "'*'" . $$txtAmount . "'*'" . $$txtProcessLoss . "'*'" . $$txtGreyQty . "'*'" . $$cboWorkScope . "'*'" . $$cboUom . "'*'" . $$txtRemarks . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0*" . $$booking_qnty_by_uom . "*" . $$cboConsUom. "*'" . $$txtProcessId. "'*'" . $$txtProcessSeq . "'*'" . $$txtProcessIdMain . "'*'" . $$txtProcessSeqMain . "'*'" .$$txtAfterFabricGsm ."'*'". $$txtFabricCuttableDia . "'*'" . $$txtPPQty . "'*'" . $$txtMTLQty . "'*'" . $$txtFPTQty . "'*'". $$txtGPTQty . "'*'". $$ccmpSaveData."'*'".$$cboPly."'*'".$$hdnCollerCuffCon."'*'".$$txtLabDipNo."'"));

						if($$ccmpSaveData != "")
						{
							$coll_cuff_arr = explode(",", str_replace("'", "", $$ccmpSaveData));
							foreach ($coll_cuff_arr as $ccm) 
							{
								$ccm_arr = explode("_", $ccm);
								$coller_size = $ccm_arr[0];
								$coller_qnty = $ccm_arr[1];
								$cuff_size = $ccm_arr[2];
								$cuff_qnty = $ccm_arr[3];
								$coller_gmt_size = $ccm_arr[4];
								$coller_ex_percent = $ccm_arr[5];
								$cuff_gmt_size = $ccm_arr[6];
								$cuff_ex_percent = $ccm_arr[7];

								if ($data_array_coll_cuff != "") $data_array_coll_cuff .= ",";
								$id_coll_cuff = return_next_id_by_sequence("FABRIC_SALES_ORDER_COLLER_CUFF_PK_SEQ", "fabric_sales_order_coller_cuff", $con);
								$data_array_coll_cuff .= "(" . $id_coll_cuff . "," . $job_update_id . ",'" . $job_no . "'," . str_replace("'", '', $$updateIdDtls) . ",'" . $coller_size . "','" . $coller_qnty . "','" . $cuff_size . "','" . $cuff_qnty . "','" .$coller_gmt_size. "','" .$coller_ex_percent. "','" .$cuff_gmt_size. "','" .$cuff_ex_percent. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							}
						}
						
					}
				}
			}
		}
		// decrease allocation quantity from allocation
		if(!empty($allocation_product_qnty_arr)){
			foreach ($allocation_product_qnty_arr as $prod_id => $all_qnty) {
				execute_query("update inv_material_allocation_mst set qnty=(qnty-$all_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where po_break_down_id='$job_update_id' and item_id=$prod_id and job_no='$job_no'", 0);

				execute_query("update inv_material_allocation_dtls set qnty=(qnty-$all_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] .",update_date='" . $pc_date_time ."' where po_break_down_id='$job_update_id' and item_id in($prod_id) and job_no='$job_no'", 0);

				execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$all_qnty) where id=$prod_id", 0);
				execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty),update_date='" . $pc_date_time . "' where id=$prod_id  ", 0);
			}
		}

		if ($cbo_within_group_combo == 1) {
			if (count($oldids) > 0) {
				$rID2 = execute_query("update fabric_sales_order_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in (" . implode(",", $oldids) . ")");
			} else {
				$rID2 = 1;
			}
		}
		if (!empty($data_array_dtls_update)) {
			$rID3 = execute_query(bulk_update_sql_statement("fabric_sales_order_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr));
		}

		$rID = sql_update("fabric_sales_order_mst", $field_array_update, $data_array_update, "id", $update_id, 1);

		$rID4 = true;
		if ($data_array_dtls != "") {
			//echo "10**insert into fabric_sales_order_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID4 = sql_insert("fabric_sales_order_dtls", $field_array_dtls, $data_array_dtls, 1);
		}
		$deletedDtlsIds = str_replace("'", '', $deletedDtlsIds);
		$deleted_ids = $dtls_ids_to_delete."".(($dtls_ids_to_delete != "")?(",".$deletedDtlsIds):$deletedDtlsIds);

		$deleted_ids = implode(",",array_filter(explode(",",chop($deleted_ids,","))));

		if($deleted_ids)
		{
			$test = execute_query("update fabric_sales_order_dtls set status_active=0,is_deleted=1 where mst_id=$update_id and id in($deleted_ids)", 0);
		}


		$del_coll_cuff = execute_query("update fabric_sales_order_coller_cuff set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where mst_id=$update_id", 0);

		$coll_cuff_insert=1;
		if($data_array_coll_cuff != "")
		{
			$coll_cuff_insert =sql_insert("fabric_sales_order_coller_cuff", $field_array_coll_cuff, $data_array_coll_cuff, 1);
		}

		//echo "10**".bulk_update_sql_statement("fabric_sales_order_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr);oci_rollback($con);die;
		//echo "10**"."$rID && $rID2 && $rID3 && $rID4 && $coll_cuff_insert && $del_coll_cuff";oci_rollback($con);die;
		//echo "10**" . $rID . "=" . $rID2 . "=" . $rID3 . "=" . $rID4. ">".$test; oci_rollback($con); die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $coll_cuff_insert) {
				mysql_query("COMMIT");
				echo "1**" . $job_update_id . "**" . $job_no;
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $coll_cuff_insert) {
				oci_commit($con);
				echo "1**" . $job_update_id . "**" . $job_no;
			} else {
				oci_rollback($con);
				echo "6**0**0";
			}
		}
		$_POST = array();
		disconnect($con);
		die;
	}
}

if ($action == "jobNo_popup") {
	echo load_html_head_contents("Job Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Sales Order Type</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cbo_sales_order_type", 150, $sales_order_type_arr, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_within_group').value + '_' + document.getElementById('cbo_sales_order_type').value, 'create_job_search_list_view', 'search_div', 'fabric_sales_order_entry_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_job_search_list_view") {
	$data = explode('_', $data);

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$within_group = $data[3];
	$sales_order_type = $data[4];

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and job_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and sales_booking_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and style_ref_no like '" . $search_string . "%'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
	if ($sales_order_type == 0) $sales_type_cond = ""; else $sales_type_cond = " and sales_order_type=$sales_order_type";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	$booking_arr = array();
	$booking_info = sql_select("select a.id,a.booking_no, a.booking_type, a.company_id, a.entry_form, a.fabric_source, a.item_category, a.job_no, a.po_break_down_id, a.is_approved, is_short from wo_booking_mst a where a.is_deleted = 0 and a.status_active=1 and a.supplier_id=$company_id");
	foreach ($booking_info as $row) {
		$booking_arr[$row[csf('booking_no')]]['id'] = $row[csf('id')];
		$booking_arr[$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];
		$booking_arr[$row[csf('booking_no')]]['booking_type'] = $row[csf('booking_type')];
		$booking_arr[$row[csf('booking_no')]]['company_id'] = $row[csf('company_id')];
		$booking_arr[$row[csf('booking_no')]]['entry_form'] = $row[csf('entry_form')];
		$booking_arr[$row[csf('booking_no')]]['fabric_source'] = $row[csf('fabric_source')];
		$booking_arr[$row[csf('booking_no')]]['item_category'] = $row[csf('item_category')];
		$booking_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		$booking_arr[$row[csf('booking_no')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$booking_arr[$row[csf('booking_no')]]['is_approved'] = $row[csf('is_approved')];
		$booking_arr[$row[csf('booking_no')]]['is_short'] = $row[csf('is_short')];
	}
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_order_type, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where entry_form=472 and status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $sales_type_cond $search_field_cond order by id desc";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="80">Sales Order Type</th>
			<th width="70">Buyer</th>
			<th width="100">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];

				$booking_data = $booking_arr[$row[csf('sales_booking_no')]]['id'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['booking_no'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['booking_type'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['entry_form'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['fabric_source'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['item_category'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['job_no'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['po_break_down_id'] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['is_approved'] . "**" . $row[csf('id')] . "**" . $booking_arr[$row[csf('sales_booking_no')]]['is_short'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $sales_order_type_arr[$row[csf('sales_order_type')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}else{

		}
		?>
	</table>
</div>
<?
exit();
}

if ($action == "from_fso_popup") 
{
	echo load_html_head_contents("Job Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data) {
			document.getElementById('hidden_booking_data').value = booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Sales Order Type</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cbo_sales_order_type", 150, $sales_order_type_arr, "", 1, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Sales Order No", 2 => "Sales / Booking No", 3 => "Style Ref.");
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_within_group').value + '_' + document.getElementById('cbo_sales_order_type').value + '_' + '<? echo $job_id; ?>', 'create_from_fso_search_list_view', 'search_div', 'fabric_sales_order_entry_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_from_fso_search_list_view") {
	$data = explode('_', $data);

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$within_group = $data[3];
	$sales_order_type = $data[4];
	$sales_order_id = $data[5];

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and job_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and sales_booking_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and style_ref_no like '" . $search_string . "%'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
	if ($sales_order_type == 0) $sales_type_cond = ""; else $sales_type_cond = " and sales_order_type=$sales_order_type";
	if ($sales_order_id) $sales_order_id_cond = " and id != $sales_order_id"; 

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_order_type, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where entry_form=472 and status_active=1 and is_deleted=0 $within_group_cond $sales_type_cond $search_field_cond $sales_order_id_cond order by id desc";
	//and company_id=$company_id and order_nature =11
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Sales Order No</th>
			<th width="40">Year</th>
			<th width="50">Within Group</th>
			<th width="80">Sales Order Type</th>
			<th width="70">Buyer</th>
			<th width="100">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		if(!empty($result)){
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];

				$booking_data = $row[csf('id')] . "**" . $row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="120"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="50"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $sales_order_type_arr[$row[csf('sales_order_type')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}
		else
		{
			echo "<br><p align='center' style='text-align:center; font-weight:bold; font-size: 15px;'>Data Not Found.</p>";
		}
		?>
	</table>
</div>
<?
exit();
}

if ($action == 'populate_data_from_sales_order') {

	$data = explode("_", $data);
	$fabric_sales_id = $data[0];
	$booking_no = $data[1];
	//$data_array = sql_select("select id, job_no, company_id, within_group, sales_booking_no, booking_id, booking_date, delivery_date, buyer_id, style_ref_no, location_id, ship_mode,season_id, team_leader, dealing_marchant, remarks, currency_id, season,booking_without_order,booking_type,booking_approval_date,ready_to_approved,is_approved,entry_form from fabric_sales_order_mst where id='$data'");

	if($booking_no!="")
	{
		$po_sql = sql_select("select po_break_down_id from wo_booking_dtls where booking_no = '".$booking_no."'");
		foreach ($po_sql as $val)
		{
			$po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}
		$po_arr = array_filter($po_arr);
		$po_ids = implode(",", $po_arr);
	}

	$data_array = sql_select("SELECT a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.delivery_start_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode,a.season_id, a.team_leader, a.dealing_marchant, a.remarks, a.currency_id, a.season,a.booking_without_order, a.booking_type, a.booking_approval_date,a.ready_to_approved,a.is_approved,a.booking_entry_form,a.po_job_no, b.fabric_source, b.is_approved booking_is_approved,b.item_category,a.attention,a.fabric_composition,a.sales_order_type, a.customer_buyer, a.customer_buyer_brand, a.season_year, a.ship_to, a.order_nature, a.from_fso_no, a.from_fso_id from fabric_sales_order_mst a left join wo_booking_mst b on a.booking_id=b.id where a.id=$fabric_sales_id group by a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.delivery_start_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode,a.season_id, a.team_leader, a.dealing_marchant, a.remarks, a.currency_id, a.season, a.booking_without_order, a.booking_type, a.booking_approval_date,a.ready_to_approved,a.is_approved,a.booking_entry_form,a.po_job_no, b.fabric_source, b.is_approved, b.item_category, a.attention,a.fabric_composition,a.sales_order_type, a.customer_buyer, a.customer_buyer_brand, a.season_year, a.ship_to, a.order_nature, a.from_fso_no, a.from_fso_id");
	foreach ($data_array as $row)
	{

		//$po_ids = implode(",",array_unique(explode(",",$row[csf("po_break_down_id")])));
		echo "document.getElementById('cbo_ready_to_approved').value 		= '" . $row[csf("ready_to_approved")] . "';\n";
		echo "document.getElementById('cbo_within_group').value 			= '" . $row[csf("within_group")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";

		echo "active_inactive();\n";

		if($row[csf("is_approved")]>0) {
			echo "$('#cbo_ready_to_approved').attr('disabled','true')" . ";\n";
		}
		else
		{
			echo "$('#cbo_ready_to_approved').removeAttr('disabled');" . ";\n";
		}
		echo "$('#cbo_within_group').attr('disabled','true')" . ";\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		if($row[csf("is_approved")]==1)  echo "$('#approval_msg_td').text('This Sales Order is Approved')" . ";\n";
		else if($row[csf("is_approved")]==3)  echo "$('#approval_msg_td').text('This Sales Order is Partially Approved')" . ";\n";
		else  echo "$('#approval_msg_td').text('')" . ";\n";

		//echo "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', ".$row[csf("within_group")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_buyer','buyer_td');\n";

		echo "document.getElementById('is_approved').value 					= '" . $row[csf("is_approved")] . "';\n";
		echo "document.getElementById('txt_job_no').value 					= '" . $row[csf("job_no")] . "';\n";
		echo "document.getElementById('txt_booking_no').value 				= '" . $row[csf("sales_booking_no")] . "';\n";
		echo "document.getElementById('txt_hdn_booking_no').value 			= '" . $row[csf("sales_booking_no")] . "';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '" . $row[csf("booking_id")] . "';\n";
		echo "document.getElementById('txt_booking_date').value 			= '" . change_date_format($row[csf("booking_date")]) . "';\n";

		echo "document.getElementById('txt_delivery_date').value 			= '" . change_date_format($row[csf("delivery_date")]) . "';\n";

		echo "document.getElementById('txt_delivery_start_date').value 		= '" . change_date_format($row[csf("delivery_start_date")]) . "';\n";

		echo "document.getElementById('booking_approval_date').value 		= '" . change_date_format($row[csf("booking_approval_date")]) . "';\n";

		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("buyer_id")] . "';\n";
		echo "document.getElementById('txt_style_ref').value 				= '" . $row[csf("style_ref_no")] . "';\n";
		echo "document.getElementById('cbo_currency').value 				= '" . $row[csf("currency_id")] . "';\n";
		echo "document.getElementById('cbo_team_leader').value 				= '" . $row[csf("team_leader")] . "';\n";
		echo "document.getElementById('booking_without_order').value 		= '" . $row[csf("booking_without_order")] . "';\n";
		echo "document.getElementById('txt_booking_type').value 			= '" . $row[csf("booking_type")] . "';\n";
		echo "document.getElementById('txt_booking_entry_form').value 		= '" . $row[csf("booking_entry_form")] . "';\n";
		echo "document.getElementById('txt_fabric_source').value 			= '" . $row[csf("fabric_source")] . "';\n";
		echo "document.getElementById('txt_hidden_job_no').value 			= '" . $row[csf("po_job_no")] . "';\n";
		echo "document.getElementById('txt_is_approved').value 				= '" . $row[csf("booking_is_approved")] . "';\n";
		echo "document.getElementById('txt_item_category').value 			= '" . $row[csf("item_category")] . "';\n";
		echo "document.getElementById('txt_order_id').value 				= '" . $po_ids . "';\n";

		echo "document.getElementById('cbo_cust_buyer_name').value 			= '" . $row[csf("customer_buyer")] . "';\n";

		echo "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', ". $row[csf("customer_buyer")]. ", 'load_drop_down_buyer_brand' , 'cust_buyer_brand_td' );\n";
		echo "load_drop_down( 'requires/fabric_sales_order_entry_v2_controller', ". $row[csf("customer_buyer")]. ", 'load_drop_down_season' , 'season_td' );\n";
		echo "document.getElementById('cbo_cust_buyer_brand').value 		= '" .$row[csf("customer_buyer_brand")]."';\n";
		echo "document.getElementById('cbo_season_year').value 				= '" . $row[csf("season_year")] . "';\n";
		echo "document.getElementById('txt_ship_to').value 					= '" . $row[csf("ship_to")] . "';\n";
		echo "document.getElementById('cbo_order_nature').value 			= '" . $row[csf("order_nature")] . "';\n";
		echo "document.getElementById('txt_fso_ref').value 					= '" . $row[csf("from_fso_no")] . "';\n";
		echo "document.getElementById('txt_fso_ref_id').value 				= '" . $row[csf("from_fso_id")] . "';\n";

		//echo "load_drop_down('requires/fabric_sales_order_entry_v2_controller', '".$row[csf("team_leader")]."', 'load_drop_down_dealing_merchant','team_td');\n";
		echo "load_drop_down('requires/fabric_sales_order_entry_v2_controller', " . $row[csf("within_group")] . "+'_'+" . $row[csf("team_leader")] . ", 'load_drop_down_dealing_merchant','team_td');\n";

		echo "document.getElementById('cbo_dealing_merchant').value 		= '" . $row[csf("dealing_marchant")] . "';\n";
		echo "document.getElementById('cbo_ship_mode').value 				= '" . $row[csf("ship_mode")] . "';\n";
		echo "document.getElementById('txt_attention').value 				= '" . $row[csf("attention")] . "';\n";
		//echo "document.getElementById('txt_fab_comp').value 				= '" . $row[csf("fabric_composition")] . "';\n";
		echo "document.getElementById('txt_season').value 					= '" . $row[csf("season_id")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('cbo_sales_order_type').value 		= '" . $row[csf("sales_order_type")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_fabric_sales_order_entry_v2',1);\n";

		exit();
	}
}

if ($action == "yarnDetails_popup") {
	echo load_html_head_contents("Yarn Details Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');

	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");

	if ($txtGreyQty == "") $txtGreyQty = 0;
	?>

	<script>
		var greyQty = '<? echo str_replace(",", "", $txtGreyQty); ?>';
		function add_break_down_tr(i) {
			var lastTrId = $('#tbl_list_search tbody tr:last').attr('id').split('_');
			var row_num = lastTrId[1];

			if (row_num != i) {
				return false;
			}
			else {
				i++;

				$("#tbl_list_search tbody tr:last").clone().find("input,select").each(function () {

					$(this).attr({
						'id': function (_, id) {
							var id = id.split("_");
							return id[0] + "_" + i
						},
						'name': function (_, name) {
							return name
						},
						'value': function (_, value) {
							return ''
						}
					});

				}).end().appendTo("#tbl_list_search");

				$("#tbl_list_search tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);

				$('#txtConsRatio_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate(" + i + ",1);");
				$('#txtConsQty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate(" + i + ",2);calculateConsAmount(" +i +");");
				$('#txtUnitRate_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculateConsAmount(" +i +");");
				$('#txtcompone_' + i).attr("onDblClick", "openmypage_comp(" + i + ",1);");
				//$('#txtcompone_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_comp("+i+",1);");

				$('#txtPerc_' + i).val('100');
				$('#cboYarnCount_' + i).val(0);
				$('#cboComposition_' + i).val(0);
				$('#cboYarnType_' + i).val(0);
				$('#cboSupplier_' + i).val(0);
				$('#cboBrand_' + i).val(0);
				$('#cboComposition_'+i).val("");
				$('#txtcompone_'+i).val("");

				$('#increase_' + i).removeAttr("value").attr("value", "+");
				$('#decrease_' + i).removeAttr("value").attr("value", "-");
				$('#increase_' + i).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + i + ");");
				$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
			}

			set_all_onclick();
		}

		function fn_deleteRow(rowNo) {
			var numRow = $('#tbl_list_search tbody tr').length;
			if (rowNo != 1) {
				$('#tr_' + rowNo).remove();
			}
			else {
				return false;
			}
		}

		function calculate(i, from) {
			var totRatio = 0;
			var numRow = $('#tbl_list_search tbody tr').length;
			for (var s = 1; s <= numRow; s++) {
				totRatio += $('#txtConsRatio_' + s).val() * 1;
			}
			if (totRatio > 100) {
				alert('Cons. Ratio Over 100% Not Allow.');
				$('#txtConsRatio_' + i).val('');
				return false;
			}

			if (from == 1) {
				var ratio = $('#txtConsRatio_' + i).val() * 1;
				var qty = (greyQty / 100) * ratio;
				$('#txtConsQty_' + i).val(qty.toFixed(2));
			}
			else {
				var qty = $('#txtConsQty_' + i).val() * 1;
				var ratio = (qty / greyQty) * 100;
				$('#txtConsRatio_' + i).val(ratio.toFixed(2));
			}

			calculate_grey_qty();
		}
		function calculateConsAmount(i) 
		{
			var qty = $('#txtConsQty_' + i).val() * 1;
			var rate = $('#txtUnitRate_' + i).val() * 1;
			var amnt = qty * rate;
			$('#txtConsAmount_' + i).val(amnt.toFixed(2));
		}

		function calculate_grey_qty() {
			var tot_qty = '';

			$("#tbl_list_search").find('tbody tr').each(function () {
				var txtConsQty = trim($(this).find('input[name="txtConsQty[]"]').val());
				tot_qty = tot_qty * 1 + txtConsQty * 1;
			});

			$('#txtTotGreyQty').val(tot_qty.toFixed(2));
		}

		function fnc_close() {
			var save_data = '';
			var tot_ratio = '';

			$("#tbl_list_search").find('tbody tr').each(function () {
				var cboYarnCount = $(this).find('select[name="cboYarnCount[]"]').val();
				var cboComposition = $(this).find('input[name="cboComposition[]"]').val();
				var txtPerc = $(this).find('input[name="txtPerc[]"]').val();
				var txtColor = trim($(this).find('input[name="txtColor[]"]').val());
				var cboYarnType = $(this).find('select[name="cboYarnType[]"]').val();
				var txtConsRatio = trim($(this).find('input[name="txtConsRatio[]"]').val());
				var txtConsQty = trim($(this).find('input[name="txtConsQty[]"]').val());
				var cboSupplier = $(this).find('select[name="cboSupplier[]"]').val();
				var cboBrand = $(this).find('select[name="cboBrand[]"]').val();
				var txtUnitRate = $(this).find('input[name="txtUnitRate[]"]').val();
				var txtConsAmount = $(this).find('input[name="txtConsAmount[]"]').val();

				if (txtConsRatio * 1 > 0) {
					if (save_data == "") {
						save_data = cboYarnCount + "_" + cboComposition + "_" + txtPerc + "_" + txtColor + "_" + cboYarnType + "_" + txtConsRatio + "_" + txtConsQty + "_" + cboSupplier+ "_" + cboBrand+ "_" + txtUnitRate+ "_" + txtConsAmount;
					}
					else {
						save_data += "|" + cboYarnCount + "_" + cboComposition + "_" + txtPerc + "_" + txtColor + "_" + cboYarnType + "_" + txtConsRatio + "_" + txtConsQty + "_" + cboSupplier + "_" + cboBrand+ "_" + txtUnitRate+ "_" + txtConsAmount;
					}

					tot_ratio = tot_ratio * 1 + txtConsRatio * 1;
				}
			});

			$('#hidden_yarn_data').val(save_data);
			$('#hidden_tot_ratio').val(tot_ratio);
			parent.emailwindow.hide();
		}
		function openmypage_comp(inc)
		{
			var page_link="fabric_sales_order_entry_v2_controller.php?action=composition_popup&inc="+inc;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var hidcompid=this.contentDoc.getElementById("hidcompid").value;
				var hidcompname=this.contentDoc.getElementById("hidcompname").value;
				$('#cboComposition_'+inc).val(hidcompid);
				$('#txtcompone_'+inc).val(hidcompname);
				check_duplicate(inc,1);
			}
		}
	</script>

</head>

<body>
	<form name="searchdescfrm" id="searchdescfrm">
		<fieldset style="width:1140px;margin-left:5px">
			<input type="hidden" name="hidden_yarn_data" id="hidden_yarn_data" class="text_boxes" value="">
			<input type="hidden" name="hidden_tot_ratio" id="hidden_tot_ratio" class="text_boxes" value="">
			<div>
				<b> Fabric Description : </b><? echo $txtFabricDesc; ?>
				<b>&nbsp;&nbsp; Grey Quantity : </b><? echo $txtGreyQty; ?>
			</div>
			<div style="margin-top:5px; margin-left:5px">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1120">
					<thead>
						<th width="80">Count</th>
						<th width="150">Composition</th>
						<th width="60">%</th>
						<th width="90">Color</th>
						<th width="90">Type</th>
						<th width="80">Cons. Ratio</th>
						<th width="80">Cons. Qty.</th>
						<th width="80">Rate/Unit</th>
						<th width="80">Amount</th>
						<th width="130">Supplier</th>
						<th width="100">Brand</th>
						<th></th>
					</thead>
				</table>
				<div style="width:1120px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1100"
					id="tbl_list_search">
					<tbody>
						<?
						$tot_grey_qty = 0;
						if (str_replace("'", '', $yarnData) != "") {
							$i = 1;
							$yarnDatas = explode("|", str_replace("'", '', $yarnData));
							foreach ($yarnDatas as $value) {
								$yarn_val = explode('_', $value);
								$cboYarnCount = $yarn_val[0];
								$cboComposition = $yarn_val[1];
								$txtPerc = $yarn_val[2];
								$txtColor = $yarn_val[3];
								$cboYarnType = $yarn_val[4];
								$txtConsRatio = $yarn_val[5];
								$txtConsQty = $yarn_val[6];
								$cboSupplier = $yarn_val[7];
								$cboBrand = $yarn_val[8];
								$txtUnitRate = $yarn_val[9];
								$txtConsAmount = $yarn_val[10];

								$tot_grey_qty += $txtConsQty;
								?>
								<tr align="center" id="tr_<? echo $i; ?>">
									<td width="80">
										<? echo create_drop_down("cboYarnCount_" . $i, 75, $yarnCount_arr, "", 1, "- Select -", $cboYarnCount, "", "0", "", "", "", "", "", "", "cboYarnCount[]"); ?>
									</td>
									<td width="150">
										<? //echo create_drop_down("cboComposition_" . $i, 145, $composition, "", 1, "- Select -", $cboComposition, "", "0", "", "", "", "", "", "", "cboComposition[]"); ?>
										<input type="text" id="txtcompone_<? echo $i; ?>"  name="txtcompone_<? echo $i; ?>"  class="text_boxes" style="width:140px" value="<? echo $composition[$cboComposition]; ?>" readonly placeholder="Browse" onDblClick="openmypage_comp(<? echo $i; ?>);" />
										<input type="hidden" id="cboComposition_<? echo $i; ?>"  name="cboComposition[]" class="text_boxes" style="width:50px" value="<? echo $cboComposition; ?>" />

									</td>
									<td width="60">
										<input type="text" name="txtPerc[]" id="txtPerc_<? echo $i; ?>"
										class="text_boxes_numeric" style="width:45px"
										value="<? echo $txtPerc; ?>" readonly/>
									</td>
									<td width="90">
										<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>"
										class="text_boxes" style="width:75px" value="<? echo $txtColor; ?>"/>
									</td>
									<td width="90">
										<? echo create_drop_down("cboYarnType_" . $i, 85, $yarn_type, "", 1, "- Select -", $cboYarnType, "", "0", "", "", "", "", "", "", "cboYarnType[]"); ?>
									</td>
									<td width="80">
										<input type="text" name="txtConsRatio[]" id="txtConsRatio_<? echo $i; ?>"
										class="text_boxes_numeric" style="width:65px"
										value="<? echo $txtConsRatio; ?>"
										onKeyUp="calculate(<? echo $i; ?>,1);"/>
									</td>
									<td width="80">
										<input type="text" name="txtConsQty[]" id="txtConsQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $txtConsQty; ?>" onKeyUp="calculate(<? echo $i; ?>,2);calculateConsAmount(<? echo $i; ?>);"/>
									</td>

									<td width="80">
										<input type="text" name="txtUnitRate[]" id="txtUnitRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $txtUnitRate; ?>" onKeyUp="calculateConsAmount(<? echo $i; ?>);"/>
									</td>
									<td width="80">
										<input type="text" name="txtConsAmount[]" id="txtConsAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $txtConsAmount; ?>" readonly/>
									</td>


									<td width="130">
										<? echo create_drop_down("cboSupplier_" . $i, 125, $supplier_arr, "", 1, "- Select -", $cboSupplier, "", "0", "", "", "", "", "", "", "cboSupplier[]"); ?>
									</td>

									<td width="100">
										<? echo create_drop_down("cboBrand_" . $i, 100, $brand_arr, "", 1, "- Select -", $cboBrand, "", "0", "", "", "", "", "", "", "cboBrand[]"); ?>
									</td>

									<td>
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
										style="width:30px" class="formbuttonplasminus" value="-"
										onClick="fn_deleteRow(<? echo $i; ?>);"/>
									</td>
								</tr>
								<?
								$i++;
							}
						} else {
							?>
							<tr align="center" id="tr_1">
								<td width="80">
									<? echo create_drop_down("cboYarnCount_1", 75, $yarnCount_arr, "", 1, "- Select -", 0, "", "0", "", "", "", "", "", "", "cboYarnCount[]"); ?>
								</td>
								<td width="150">
									<? //echo create_drop_down("cboComposition_1", 145, $composition, "", 1, "- Select -", 0, "", "0", "", "", "", "", "", "", "cboComposition[]"); ?>

									<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:140px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
									<input type="hidden" id="cboComposition_1"  name="cboComposition[]" class="text_boxes" style="width:50px" value="" />



								</td>
								<td width="60">
									<input type="text" name="txtPerc[]" id="txtPerc_1" class="text_boxes_numeric"
									style="width:45px" value="100" readonly/>
								</td>
								<td width="90">
									<input type="text" name="txtColor[]" id="txtColor_1" class="text_boxes"
									style="width:75px"/>
								</td>
								<td width="90">
									<? echo create_drop_down("cboYarnType_1", 85, $yarn_type, "", 1, "- Select -", 0, "", "0", "", "", "", "", "", "", "cboYarnType[]"); ?>
								</td>
								<td width="80">
									<input type="text" name="txtConsRatio[]" id="txtConsRatio_1"
									class="text_boxes_numeric" style="width:65px" onKeyUp="calculate(1,1);"/>
								</td>
								<td width="80">
									<input type="text" name="txtConsQty[]" id="txtConsQty_1" class="text_boxes_numeric" style="width:65px" onKeyUp="calculate(1,2);calculateConsAmount(1);"/>
								</td>

								<td width="80">
										<input type="text" name="txtUnitRate[]" id="txtUnitRate_1" class="text_boxes_numeric" style="width:65px" value="" onKeyUp="calculateConsAmount(1);"/>
								</td>
								<td width="80">
									<input type="text" name="txtConsAmount[]" id="txtConsAmount_1" class="text_boxes_numeric" style="width:65px" value=""  readonly/>
								</td>

								<td width="130">
									<? echo create_drop_down("cboSupplier_1", 125, $supplier_arr, "", 1, "- Select -", 0, "", "0", "", "", "", "", "", "", "cboSupplier[]"); ?>
								</td>
								<td width="100">
									<? echo create_drop_down("cboBrand_1", 100, $brand_arr, "", 1, "- Select -", 0, "", "0", "", "", "", "", "", "", "cboBrand[]"); ?>
								</td>

								<td>
									<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"/>
									<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
								</td>
							</tr>
							<?
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="6">Total</th>
						<th><input type="text" name="txtTotGreyQty" id="txtTotGreyQty"
							value="<? echo number_format($tot_grey_qty, 2, '.', ''); ?>"
							class="text_boxes_numeric" style="width:65px" readonly/></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</div>
			<table width="955">
				<tr>
					<td align="center">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close"
						onClick="fnc_close();" style="width:100px"/>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../", 1, 1, '','1','');
	extract($_REQUEST);

	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<fieldset style="width:430px;margin-left:10px">
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th>Composition
						<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
						<input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
					</th>
				</thead>
			</table>
			<table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
				<tbody>
					<? $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>')">
						<td width="30"><? echo $i; ?></td>
						<td><? echo $comp_name; ?> </td>
					</tr>
					<? $i++; } ?>
				</tbody>
			</table>
			<div id="search_div" style="margin-top:5px"></div>
		</form>
	</fieldset>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>setFilterGrid('comp_tbl',-1);</script>
</html>
<?
exit();
}
if ($action == 'yarn_details') {
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}

	$saveDataArr = array();
	$sql_yarn = "select deter_id, gsm, yarn_data from fabric_sales_order_yarn where mst_id='$data' and status_active=1 and is_deleted=0 ";
	$data_array_yarn = sql_select($sql_yarn);
	foreach ($data_array_yarn as $row) {
		$saveDataArr[$row[csf('deter_id')]][$row[csf('gsm')]] = $row[csf('yarn_data')];
	}

	if($db_type==0)
	{
		$dtls_ids="group_concat(id) as dtls_ids";
		
	}
	else if($db_type==2)
	{
		$dtls_ids="listagg(id,',') within group (order by id) as dtls_ids";
	}

	$sql = "select determination_id, gsm_weight, sum(grey_qty) as grey_qty, $dtls_ids from fabric_sales_order_dtls where mst_id='$data' and status_active=1 and is_deleted=0 group by determination_id, gsm_weight";
	$data_array = sql_select($sql);
	$i = 0;
	foreach ($data_array as $row) {
		$i++;

		?>
		<tr class="general" id="tr_<? echo $i; ?>">
			<td>
				<input type="text" name="txtFabricDescY[]" id="txtFabricDescY_<? echo $i; ?>" class="text_boxes"
				style="width:380px" value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" disabled
				readonly/>
				<input type="hidden" name="fabricDescIdY[]" id="fabricDescIdY_<? echo $i; ?>" class="text_boxes"
				value="<? echo $row[csf('determination_id')]; ?>">
			</td>
			<td>
				<input type="text" name="txtFabricGsmY[]" id="txtFabricGsmY_<? echo $i; ?>" class="text_boxes"
				style="width:90px" value="<? echo $row[csf('gsm_weight')]; ?>" disabled="disabled"/>
			</td>
			<td>
				<input type="text" name="txtGreyQtyY[]" id="txtGreyQtyY_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>" placeholder="Double Click" onDblClick="openmypage_yarnDetails(<? echo $i; ?>)" readonly/>
				<input type="hidden" name="yarnData[]" id="yarnData_<? echo $i; ?>" value="<? echo $saveDataArr[$row[csf('determination_id')]][$row[csf('gsm_weight')]]; ?>" class="text_boxes">
				<input type="hidden" name="fsoDtlsIds[]" id="fsoDtlsIds_<? echo $i; ?>" value="<? echo $row[csf('dtls_ids')]; ?>" class="text_boxes">
			</td>
		</tr>
		<?
	}
	echo "##" . count($data_array_yarn);
	exit();
}

if ($action == "save_update_delete_yarn") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	if ($operation == 0)  // Insert Here
	{
		$con = connect();

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$field_array_dtls = "id, mst_id, deter_id, gsm, grey_qty, yarn_data, fso_dtls_ids, inserted_by, insert_date";
		$field_array_yarn_dtls = "id,mst_id,yarn_dtls_id,deter_id,gsm,yarn_count_id,composition_id,composition_perc,color_id,yarn_type,cons_ratio,cons_qty,unit_rate,amount,supplier_id,brand_id,inserted_by,insert_date";

		for ($i = 1; $i <= $total_row; $i++) {
			$fabricDescIdY = "fabricDescIdY" . $i;
			$txtFabricGsmY = "txtFabricGsmY" . $i;
			$txtGreyQtyY = "txtGreyQtyY" . $i;
			$yarnData = "yarnData" . $i;
			$fsoDtlsIds = "fsoDtlsIds" . $i;
			$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_YARN_PK_SEQ", "fabric_sales_order_yarn", $con);
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $update_id . ",'" . $$fabricDescIdY . "','" . $$txtFabricGsmY . "','" . str_replace(",", '', $$txtGreyQtyY) . "','" . $$yarnData ."','".$$fsoDtlsIds. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			if (str_replace("'", '', $$yarnData) != "") {
				$yarnDatas = explode("|", str_replace("'", '', $$yarnData));
				foreach ($yarnDatas as $value) {
					$yarn_val = explode('_', $value);
					$cboYarnCount = $yarn_val[0];
					$cboComposition = $yarn_val[1];
					$txtPerc = $yarn_val[2];
					$txtColor = $yarn_val[3];
					$cboYarnType = $yarn_val[4];
					$txtConsRatio = $yarn_val[5];
					$txtConsQty = $yarn_val[6];
					$cboSupplier = $yarn_val[7];
					$cboBrand = $yarn_val[8];					
					$txtUnitRate = $yarn_val[9];					
					$txtAmount = $yarn_val[10];					

					if(str_replace("'","",$txtColor)!="")
					{
						if (!in_array(str_replace("'","",$txtColor),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$txtColor), $color_library, "lib_color", "id,color_name","109");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_color[$color_id]=str_replace("'","",$txtColor);

						}
						else $color_id =  array_search(str_replace("'","",$txtColor), $new_array_color);
					}
					else
					{
						$color_id=0;
					}

					$consQty = number_format(($$txtGreyQtyY / 100) * $txtConsRatio, 2, '.', '');
					$yarn_id = return_next_id_by_sequence("FABRIC_SALES_YARN_DTLS_PK_SEQ", "fabric_sales_order_yarn_dtls", $con);
					if ($data_array_yarn_dtls != "") $data_array_yarn_dtls .= ",";
					$data_array_yarn_dtls .= "(" . $yarn_id . "," . $update_id . "," . $id_dtls . "," . $$fabricDescIdY . "," . $$txtFabricGsmY . ",'" . $cboYarnCount . "','" . $cboComposition . "','" . $txtPerc . "','" . $color_id . "','" . $cboYarnType . "','" . $txtConsRatio . "','" . $consQty ."','" . $txtUnitRate . "','" . $txtAmount. "','" . $cboSupplier . "','" . $cboBrand . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}
		}

		//oci_rollback($con);
		//echo "10**insert into fabric_sales_order_yarn (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID = sql_insert("fabric_sales_order_yarn", $field_array_dtls, $data_array_dtls, 0);
		//echo "10**insert into fabric_sales_order_yarn_dtls (".$field_array_yarn_dtls.") values ".$data_array_yarn_dtls;oci_rollback($con);die;
		$rID2 = sql_insert("fabric_sales_order_yarn_dtls", $field_array_yarn_dtls, $data_array_yarn_dtls, 1);
		$rID3 = execute_query("update fabric_sales_order_mst set is_apply_last_update=0,is_master_part_updated=0 where id=$update_id", 0);

		//oci_rollback($con);
		// echo "10**".$rID."&&".$data_array_yarn_dtls;die;
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**$rID && $rID2 && $rID3";oci_rollback($con);die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3) {
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", "", $update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "0**" . str_replace("'", "", $update_id);
			} else {
				oci_rollback($con);
				echo "5**0";
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$yarn_update_id = str_replace("'", "", $update_id);
		$update_serial = (return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id=$yarn_update_id")) + 1;
		$field_array_dtls = "id, mst_id, deter_id, gsm, grey_qty, yarn_data, fso_dtls_ids, inserted_by, insert_date,update_sl";

		$field_array_yarn_dtls = "id,mst_id,yarn_dtls_id,deter_id,gsm,yarn_count_id,composition_id,composition_perc,color_id,yarn_type,cons_ratio,cons_qty,unit_rate,amount,supplier_id,brand_id,inserted_by,insert_date";

		for ($i = 1; $i <= $total_row; $i++) {
			$fabricDescIdY = "fabricDescIdY" . $i;
			$txtFabricGsmY = "txtFabricGsmY" . $i;
			$txtGreyQtyY = "txtGreyQtyY" . $i;
			$yarnData = "yarnData" . $i;
			$fsoDtlsIds = "fsoDtlsIds" . $i;

			$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_YARN_PK_SEQ", "fabric_sales_order_yarn", $con);
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $update_id . ",'" . $$fabricDescIdY . "','" . $$txtFabricGsmY . "','" . $$txtGreyQtyY . "','" . $$yarnData . "','" .$$fsoDtlsIds."',". $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',$update_serial)";

			if (str_replace("'", '', $$yarnData) != "") {
				$yarnDatas = explode("|", str_replace("'", '', $$yarnData));
				foreach ($yarnDatas as $value) {
					$yarn_val = explode('_', $value);
					$cboYarnCount = $yarn_val[0];
					$cboComposition = $yarn_val[1];
					$txtPerc = $yarn_val[2];
					$txtColor = $yarn_val[3];
					$cboYarnType = $yarn_val[4];
					$txtConsRatio = $yarn_val[5];
					$txtConsQty = $yarn_val[6];
					$cboSupplier = ($yarn_val[7]!="" || $yarn_val[7]!=null)?$yarn_val[7]:0;
					$cboBrand = ($yarn_val[8]!="" || $yarn_val[8]!=null)?$yarn_val[8]:0;
					$txtUnitRate = $yarn_val[9];
					$txtAmount = $yarn_val[10];

					if(str_replace("'","",$txtColor)!="")
					{
						if (!in_array(str_replace("'","",$txtColor),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$txtColor), $color_library, "lib_color", "id,color_name","109");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_color[$color_id]=str_replace("'","",$txtColor);

						}
						else $color_id =  array_search(str_replace("'","",$txtColor), $new_array_color);
					}
					else
					{
						$color_id=0;
					}

					$consQty = number_format(($$txtGreyQtyY / 100) * $txtConsRatio, 2, '.', '');
					$yarn_id = return_next_id_by_sequence("FABRIC_SALES_YARN_DTLS_PK_SEQ", "fabric_sales_order_mst", $con);
					if ($data_array_yarn_dtls != "") $data_array_yarn_dtls .= ",";
					$data_array_yarn_dtls .= "(" . $yarn_id . "," . $update_id . "," . $id_dtls . "," . $$fabricDescIdY . "," . $$txtFabricGsmY . ",'" . $cboYarnCount . "','" . $cboComposition . "','" . $txtPerc . "','" . $color_id . "','" . $cboYarnType . "','" . $txtConsRatio . "','" . $consQty ."','" .$txtUnitRate ."','" .$txtAmount. "','" . $cboSupplier . "','" . $cboBrand . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}
		}
		//echo "10**insert into fabric_sales_order_yarn_dtls (".$field_array_yarn_dtls.") values ".$data_array_yarn_dtls;die;
		//oci_rollback($con);
		$rID = execute_query("delete from fabric_sales_order_yarn where mst_id=$update_id", 0);
		$rID2 = execute_query("delete from fabric_sales_order_yarn_dtls where mst_id=$update_id", 0);
		//echo "10**insert into fabric_sales_order_yarn (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3 = sql_insert("fabric_sales_order_yarn", $field_array_dtls, $data_array_dtls, 0);

		$rID4 = sql_insert("fabric_sales_order_yarn_dtls", $field_array_yarn_dtls, $data_array_yarn_dtls, 1);
		//echo "10**$rID4 insert into fabric_sales_order_yarn_dtls (".$field_array_yarn_dtls.") values ".$data_array_yarn_dtls;die;
		$rID5 = execute_query("update fabric_sales_order_mst set is_apply_last_update=0,is_master_part_updated=0 where id=$update_id", 0);

		//oci_rollback($con);
		//echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5;die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "6**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $update_id);
			} else {
				oci_rollback($con);
				echo "6**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "process_loss_method") {
	$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name ='$data' and item_category_id=2 and variable_list=18 and is_deleted=0 and status_active=1");
	if ($process_loss_method == 2) $process_loss_method = $process_loss_method; else $process_loss_method = 1;

	$color_from_library = return_field_value("color_from_library", "variable_order_tracking", "company_name ='$data' and variable_list=23 and is_deleted=0 and status_active=1");
	if ($color_from_library == 2) $color_from_library = $color_from_library; else $color_from_library = 1;

	echo "document.getElementById('process_loss_method').value 				= '" . $process_loss_method . "';\n";
	echo "document.getElementById('color_from_library').value 				= '" . $color_from_library . "';\n";

	exit();
}

if ($action == "check_booking_approval") {
	$approved = sql_select("select is_approved,booking_type,is_short,0 as booking_without_order,company_id from wo_booking_mst where booking_no='" . trim($data) . "'
		union all
		select is_approved,booking_type,is_short,1 as booking_without_order,company_id from wo_non_ord_samp_booking_mst where booking_no='" . trim($data) . "'");

	echo $approved[0][csf("is_approved")]."**".$approved[0][csf("booking_type")]."**".$approved[0][csf("is_short")]."**".$approved[0][csf("booking_without_order")]."**".$approved[0][csf("company_id")];
	exit();
}

if ($action == 'show_change_bookings_old') {
	$sql = "select id, company_id, job_no, sales_booking_no,is_master_part_updated,booking_without_order from fabric_sales_order_mst where status_active=1 and is_deleted=0 and within_group=1 and is_apply_last_update=2";
	$data_array = sql_select($sql);

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="110">Sales Order No</th>
			<th width="100">Booking No.</th>
			<th>Revised No</th>
		</thead>
	</table>
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search_revised">
			<?
			$i = 1;
			foreach ($data_array as $row) {
				if ($row[csf("is_master_part_updated")] == 1) {
					$bgcolor = "#8FCF57";
				} else {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='set_form_data("<? echo $row[csf('id')] . "**" . $row[csf('company_id')] . "**" . $row[csf('sales_booking_no')]; ?>")'
					style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="110"><? echo $row[csf('job_no')]; ?></td>
					<td width="100"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td align="right">
						<?
						if ($row[csf("booking_without_order")] == 1) {
							$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='" . $row[csf('sales_booking_no')] . "' and b.entry_form=9");
						}else{
							$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='" . $row[csf('sales_booking_no')] . "'");
						}
						echo $nameArray_approved[0][csf('approved_no')] - 1;
						?>

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

if ($action == 'show_change_bookings') {
	$sql = "select id, company_id, job_no, sales_booking_no,booking_id,is_master_part_updated,booking_without_order from fabric_sales_order_mst where status_active=1 and is_deleted=0 and within_group=1 and is_apply_last_update=2";
	$data_array = sql_select($sql);
	$sales_po_booking_arr=$sales_wpo_booking_arr=array();
	foreach ($data_array as $row) {
		if($row[csf("booking_without_order")]==1){
			$sales_booking_arr[] = $row[csf("booking_id")];
		}else{
			$sales_booking_arr[] = $row[csf("booking_id")];
		}
	}

	$all_sales_po_booking_arr = array_filter($sales_booking_arr);
	$booking_cond="";
	if($db_type==2)
	{
		if(count($all_sales_po_booking_arr)>999)
		{
			$all_booking_chunk=array_chunk($all_sales_po_booking_arr,999) ;
			foreach($all_booking_chunk as $chunk_arr)
			{
				$bookCond .=" a.id in (".implode(",", $chunk_arr).") or ";
			}
			$booking_cond.=" and (".chop($bookCond,'or ').")";
		}else{
			$booking_cond=" and a.id in (".implode(",", $all_sales_po_booking_arr).")";
		}
	}
	else
	{
		$booking_cond=" and a.id in(".implode($sales_booking_arr).")";
	}

	$nameArray_approved = sql_select("select a.id,max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id $booking_cond group by a.id
		union all
		select a.id,max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.status_active=1 $booking_cond and a.ENTRY_FORM_ID=9 group by a.id");
	foreach ($nameArray_approved as $approve_row) {
		$approve_arr[$approve_row[csf("id")]] = $approve_row[csf("approved_no")];
	}

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="110">Sales Order No</th>
			<th width="100">Booking No.</th>
			<th>Revised No</th>
		</thead>
	</table>
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search_revised">
			<?
			$i = 1;
			foreach ($data_array as $row) {
				if ($row[csf("is_master_part_updated")] == 1) {
					$bgcolor = "#8FCF57";
				} else {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='set_form_data("<? echo $row[csf('id')] . "**" . $row[csf('company_id')] . "**" . $row[csf('sales_booking_no')]; ?>")'
					style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="110"><? echo $row[csf('job_no')]; ?></td>
					<td width="100"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td align="right">
						<? echo $approve_arr[$row[csf("booking_id")]] - 1; ?>
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

if ($action == 'fabric_sales_order_print') 
{
	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$departmentArr = return_library_array("select id,department_name from lib_department", 'id', 'department_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$update_serial = sql_select("select a.id, a.update_sl,a.booking_approval_date,a.revise_no from fabric_sales_order_mst a where a.sales_booking_no = '" . $bookingNo . "'");

	/*$sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,cast(a.fabric_composition as nvarchar2(200)) fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention
	union all
	select a.attention,null as job_no,null as order_repeat_no,c.id as sales_id,b.composition fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,null as style_description,null as dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id,null as style_ref_no,null as team_leader,null as dealing_marchant,null as season_matrix, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.attention,c.id,b.composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date";*/
	if ($db_type == 0)
	{
		$fabric_composition = "a.fabric_composition as fabric_composition";
		$po_break_down_id = "a.po_break_down_id as po_break_down_id";
		$group_concat = "group_concat(e.po_break_down_id) as po_break_down_id";
		
	}
	else
	{
		$fabric_composition = "cast(a.fabric_composition as nvarchar2(200)) as fabric_composition";
		$po_break_down_id = "cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id";
		$group_concat = "listagg(e.po_break_down_id, ',') within group (order by e.po_break_down_id) as po_break_down_id";
	}
	$sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id, $fabric_composition, a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.booking_approval_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.booking_approval_date,d.attention,d.fabric_composition
	union all
	select a.attention,null as job_no,null as order_repeat_no,c.id as sales_id, b.composition as fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, null as style_description,null as dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id,null as style_ref_no,null as team_leader,null as dealing_marchant,null as season_matrix, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention as sales_attention,c.fabric_composition as sales_fab_comp from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.attention,c.id,b.composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention,c.fabric_composition";


	$partial_sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $group_concat,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp FROM wo_booking_mst a, wo_po_details_master b,	wo_po_break_down c,	fabric_sales_order_mst d, wo_booking_dtls e
	WHERE
	a.booking_no=e.booking_no and e.po_break_down_id=c.id and a.id=d.booking_id and e.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' and a.entry_form=108
	group by a.attention,b.job_no,b.order_repeat_no,d.id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id,b.style_description,b.dealing_marchant,d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention,d.fabric_composition";

	$mst_data = sql_select($sql);
	$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
	extract($mst_data);

	if ($sales_id == '') {
		$mst_data = sql_select($partial_sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}
	$po_break_down_id = implode(",", array_unique(explode(",", $mst_data['po_break_down_id'])));
	if ($sales_id == '') {
		$mst_data = sql_select($partial_sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}
	$po_break_down_id = implode(",", array_unique(explode(",", $mst_data['po_break_down_id'])));
	if ($po_break_down_id != ""){
		$po_quantity = return_field_value("sum(po_quantity)", "wo_po_break_down", "id in($po_break_down_id)");
		$sql_po = "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in($po_break_down_id) group by po_number";
		$data_array_po = sql_select($sql_po);
		$po_no = '';
		foreach ($data_array_po as $row_po) {
			$po_no .= $row_po[csf('po_number')] . ", ";
		}
	}	
	if ($sales_id != ""){
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry_v2' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');
	}
	
	if (count($image_locationArr) == 0) {
		if ($wo_job_no != ""){
			$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='knit_order_entry' and master_tble_id='$wo_job_no' and file_type=1", 'id', 'image_location');
		}		
	}

	//$lead_time = datediff("d", $po_received_date, date('d-M-Y', time()));
	$lead_time = datediff("d", $booking_approval_date,$delivery_date);

	$gmts_item_id_arr = explode(',', $gmts_item_id);
	foreach ($gmts_item_id_arr as $item_id) {
		if ($item_string == '') {
			$item_string = $garments_item[$item_id];
		} else {
			$item_string .= ',' . $garments_item[$item_id];
		}
	}
	$max_shipment_date . 'wew';
	$update_serial_yarn = return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id='" . $update_serial[0][csf("id")] . "'");
	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0"
	style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
	<tr>
		<td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="center"><strong><? echo $addressArr[$companyId]; ?></strong></td>
	</tr>
	<tr>
		<td colspan="5" align="center"><strong><? echo $formCaption; ?></strong></td>
	</tr>
	</table>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
	<tr>
		<td width="135"><strong>Buyer/Agent Name</strong></td>
		<td><? echo $buyerArr[$buyer_name]; ?></td>
		<td width="135"><strong>Dept.</strong></td>
		<td><? echo $departmentArr[$product_dept]; ?></td>
		<td width="135"><strong>Garments Item</strong></td>
		<td><? echo $item_string; ?></td>
		<td><strong>Sales Order No: <? echo $salesOrderNo; ?></strong></td>
	</tr>
	<tr>
		<td><strong>Style Ref.</strong></td>
		<td><? echo $style_ref_no; ?></td>
		<td><strong>Season</strong></td>
		<td><? echo $season_arr[$season]; ?></td>
		<td><strong>Order Qnty</strong></td>
		<td><? echo $po_quantity; ?></td>
		<td rowspan="10" valign="top" width="205"><? foreach ($image_locationArr as $path) { ?><img
			src="../../<? echo $path; ?>" height="100" width="100"><? } ?></td>
		</tr>
		<tr>
			<td><strong>Style Des.</strong></td>
			<td><? echo $style_description; ?></td>
			<td><strong>Lead Time</strong></td>
			<td><? echo $lead_time; ?></td>
			<td><strong>Job No</strong></td>
			<td><? echo $wo_job_no; ?></td>
		</tr>
		<tr>
			<td><strong>Order No</strong></td>
			<td style="overflow:hidden;text-overflow: ellipsis;word-break: break-all;;"
			colspan="3"><? echo rtrim($po_no, ','); ?></td>
			<td><strong>Booking No</strong></td>
			<td><? echo $booking_no; ?></td>
		</tr>
		<tr>
			<td><strong>Repeat No</strong></td>
			<td><? echo $order_repeat_no; ?></td>
			<td><strong>Shipment Date</strong></td>
			<td><? echo 'First:' . change_date_format($shipment_date) . ',' . 'Last:' . change_date_format($max_shipment_date); ?></td>
			<td><strong>Booking Date</strong></td>
			<td><? echo change_date_format($booking_date); ?></td>
		</tr>
		<tr>
			<td><strong>Po Received Date</strong></td>
			<td><? echo change_date_format($po_received_date); ?></td>
			<td><strong>WO Prepared After</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Dealing Merchant</strong></td>
			<td><? echo $dealing_marArr[$dealing_marchant]; ?></td>
		</tr>
		<tr>
			<td><strong>Currency</strong></td>
			<td><? echo $currency[$currency_id]; ?></td>
			<td><strong>Quality Label</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Style Owner</strong></td>
			<td><? echo $companyArr[$style_owner]; ?></td>
		</tr>
		<tr>
			<td><strong>Attention</strong></td>
			<td colspan="3"><? echo $sales_attention; ?></td>
			<td><strong>Delivery Date</strong></td>
			<td><? echo change_date_format($delivery_date); ?></td>
		</tr>
		<tr>
			<td><strong>Fabric Composition</strong></td>
			<td colspan="3"><? echo $sales_fab_comp; ?></td>
			<td><strong>Revised No</strong></td>
			<td><?
			$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='" . $booking_no . "' and b.entry_form=7");
			echo ($update_serial[0][csf("revise_no")] > 0)?$update_serial[0][csf("revise_no")]:""; ?></td>
		</tr>
		<tr>
			<td rowspan="2"><strong>Remarks</strong></td>
			<td rowspan="2" colspan="3"><? echo $remarks; ?></td>
			<td><strong>Receive Date</strong></td>
			<td><? echo change_date_format($update_serial[0][csf("booking_approval_date")]); ?></td>
		</tr>
		<tr>
			<td><strong>Update Serial</strong></td>
			<td>
				Fabric: <? echo ($update_serial[0][csf("update_sl")] == "") ? 0 : $update_serial[0][csf("update_sl")]; ?>
				,
				Yarn: <? echo ($update_serial_yarn == "") ? 0 : $update_serial_yarn; ?>
			</td>
		</tr>
	</table>

	<br>

	<? 
	
	$uom_wise_sql="SELECT  b.cons_uom, sum(b.grey_qnty_by_uom) as grey_qnty_by_uom, sum(b.grey_qty) as grey_qty
 	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 group by b.cons_uom order by b.cons_uom";
	// echo $uom_wise_sql;die;
	$uom_wise_sql_data = sql_select($uom_wise_sql);
	$uom_wise_arr=array();
	foreach ($uom_wise_sql_data as $row) 
	{
		$uom_wise_arr[$row[csf('cons_uom')]]['cons_uom']=$row[csf('cons_uom')];
		$uom_wise_arr[$row[csf('cons_uom')]]['grey_qnty_by_uom']+=$row[csf('grey_qnty_by_uom')];
		$uom_wise_arr[$row[csf('cons_uom')]]['grey_qty']+=$row[csf('grey_qty')];
	}
	
	?>

		<div> <!-- UOM Wise Summary -->
			<strong style="font: bold 12px tahoma;">UOM Wise Summary</strong>
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
				<tr bgcolor="#CCCCCC">
					<td align="center"><strong>UOM</strong></td>
					<td align="center"><strong>Booking Qty.</strong></td>
					<td align="center"><strong>Grey Qty. (Kg)</strong></td>
				</tr>
				<?
				$i = 1;
				foreach ($uom_wise_arr as $row) 
				{
					?>
					<tr>
						<td><? echo $unit_of_measurement[$row['cons_uom']].' To Kg'; ?></td>
						<td><? echo number_format($row['grey_qnty_by_uom'],2); ?></td>
						<td><p><? echo number_format($row['grey_qty'],2); ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		
	<br/>

	
	<?
	//$dtls_sql = "select b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.grey_qty,sum(b.rmg_qty) as rmg_qty, c.item_number_id,b.pre_cost_fabric_cost_dtls_id,b.process_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_pre_cost_fabric_cost_dtls c where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.id=b.mst_id and b.pre_cost_fabric_cost_dtls_id=c.id and a.status_active=1 and b.is_deleted=0 group by b.body_part_id,b.color_type_id,b.fabric_desc, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.grey_qty,b.color_id,b.pre_cost_remarks, c.item_number_id, b.pre_cost_fabric_cost_dtls_id,b.process_id order by b.body_part_id";
	$dtls_sql = "select b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.grey_qty,sum(b.rmg_qty) as rmg_qty, c.item_number_id,b.pre_cost_fabric_cost_dtls_id,b.process_id from fabric_sales_order_mst a,fabric_sales_order_dtls b
	left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id
	where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 group by b.body_part_id,b.color_type_id,b.fabric_desc, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.grey_qty,b.color_id,b.pre_cost_remarks, c.item_number_id, b.pre_cost_fabric_cost_dtls_id,b.process_id order by b.body_part_id";

	$dtls_sql_data = sql_select($dtls_sql);
	foreach ($dtls_sql_data as $rows) {
		$key = $rows[csf('body_part_id')] . '_' . $rows[csf('color_type_id')] . '_' . $rows[csf('fabric_desc')] . '_' . $rows[csf('gsm_weight')] . '_' . $rows[csf('dia')] . '_' . $rows[csf('pre_cost_remarks')]. '_' . $rows[csf('item_number_id')];

		$colorArr[$rows[csf('color_id')]] = $rows[csf('color_id')];

		$finish_qty_arr[$key][$rows[csf('color_id')]] += $rows[csf('finish_qty')];
		$grey_qty_arr[$key][$rows[csf('color_id')]] += $rows[csf('grey_qty')];
		$process_loss_arr[$key][$rows[csf('color_id')]] += $rows[csf('process_loss')];

		$tot_finish_qty_arr += $rows[csf('finish_qty')];
		$tot_grey_qty_arr += $rows[csf('grey_qty')];

		list($construction, $compositions) = explode(',', $rows[csf('fabric_desc')]);
		$body_part_data[$key] = $rows[csf('body_part_id')];
		$item_number_id[$key] = $rows[csf('item_number_id')];
		$color_type_id_arr[$key] = $rows[csf('color_type_id')];
		$constructions_arr[$key] = $construction;
		$compositions_arr[$key] = $compositions;

		$gsm_weight_arr[$key] = $rows[csf('gsm_weight')];
		$dia_arr[$key] = $rows[csf('dia')] . ',' . $fabric_typee[$rows[csf('width_dia_type')]];
		$remarks_arr[$key] = $rows[csf('pre_cost_remarks')];
		$rmg_qty_arr[$key] += $rows[csf('rmg_qty')];
		$fin_qty_arr[$key] += $rows[csf('finish_qty')];

		if($rows[csf('process_id')]!=""){$process_count+=1;}
		$process_name = '';
		$process_id_array = explode(",", $rows[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}
		$process_name_arr[$key] = $process_name;
	}

	$costing_per = "";
	$costing_per_qnty = 0;
	$costing_per_id = return_field_value("costing_per", "wo_pre_cost_mst", "job_no ='$wo_job_no'");
	if ($costing_per_id == 1) {
		$costing_per = "1 Dzn";
		$costing_per_qnty = 12;
	} elseif ($costing_per_id == 2) {
		$costing_per = "1 Pcs";
		$costing_per_qnty = 1;
	} elseif ($costing_per_id == 3) {
		$costing_per = "2 Dzn";
		$costing_per_qnty = 24;
	} elseif ($costing_per_id == 4) {
		$costing_per = "3 Dzn";
		$costing_per_qnty = 36;
	} elseif ($costing_per_id == 5) {
		$costing_per = "4 Dzn";
		$costing_per_qnty = 48;
	}


	$gmt_color_data = sql_select("select gmts_color_id,contrast_color_id FROM wo_pre_cos_fab_co_color_dtls WHERE job_no ='$wo_job_no'");
	foreach ($gmt_color_data as $gmt_color_row) {
		$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]] = $color_library[$gmt_color_row[csf("gmts_color_id")]];
	}

	?>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td colspan="3"><strong>Item Name</strong></td>

			<?
			foreach ($item_number_id as $result_fabric_description) {
				if ($result_fabric_description == "")
					echo "<td colspan='3'>&nbsp</td>";
				else
					echo "<td colspan='3' align='center'>" . $garments_item[$result_fabric_description] . "</td>";
			}
			?>
			<td rowspan="10"><strong>Total Finish</strong></td>
			<td rowspan="10"><strong>Total Grey</strong></td>
		</tr>
		<tr>
			<td colspan="3"><strong>Body Part</strong></td>
			<? foreach ($body_part_data as $val) {
				echo '<td colspan="3" align="center">' . $body_part[$val] . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Color Type</strong></td>
			<? foreach ($color_type_id_arr as $val) {
				echo '<td colspan="3" align="center">' . $color_type[$val] . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Fabric Construction </strong></td>
			<? foreach ($constructions_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Yarn Composition</strong></td>
			<? foreach ($compositions_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>GSM </strong></td>
			<? foreach ($gsm_weight_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Dia/Width (Inch)</strong></td>
			<? foreach ($dia_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Consumption For <? echo $costing_per; ?></strong></td>
			<? foreach ($body_part_data as $key_ids => $val) {
				//$tot_consumption_arr+=$val;echo '<td colspan="3">'.number_format($val,2).'</td>';

				list($body_part_id, $color_type_id, $fabric_desc, $gsm_weight, $dia, $pre_cost_remarks) = explode('_', $key_ids);
				if ($pre_cost_remarks == "") {
					$pre_cost_remarks = 0;
				}
				if ($dia != "") {
					$dia_con = " and b.dia_width='$dia'";
				} else {
					$dia_con = " and b.dia_width is null";
				}
				list($constrac_str, $compo_str) = explode(',', $fabric_desc);

				$sql = "select avg(b.cons) as cons from
				wo_pre_cost_fabric_cost_dtls a,
				wo_po_color_size_breakdown c,
				wo_pre_cos_fab_co_avg_con_dtls b,
				wo_booking_dtls d
				where a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				b.po_break_down_id=d.po_break_down_id and
				c.id=b.color_size_table_id and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no ='$booking_no' and

				d.status_active=1 and
				d.is_deleted=0 and
				b.cons>0  and
				a.body_part_id='$body_part_id' and
				a.color_type_id='$color_type_id' and
				a.construction='$constrac_str' and
				a.composition='" . trim($compo_str) . "'  and

				a.gsm_weight='$gsm_weight' and
				b.remarks='$pre_cost_remarks'
				$dia_con
				";
				//if($rmg_qty_arr[$key_ids]==0){
				$con_sql_data = sql_select($sql);
				$conQTY = $con_sql_data[0][csf('cons')];
				//}
				//else
				//{
				//$conQTY=($fin_qty_arr[$key_ids]/$rmg_qty_arr[$key_ids])*$costing_per_qnty;
				//}


				$tot_consumption_arr += $conQTY;
				echo '<td colspan="3" align="center">' . number_format($conQTY, 2) . '</td>';
				//if($conQTY<1){echo $sql.'**';die;}
			}
			?>
		</tr>
		<?
		if($process_count>0){
			?>
			<tr>
				<td colspan="3"><strong>Process</strong></td>
				<? foreach ($process_name_arr as $val) {
					echo '<td colspan="3" align="center">' . $val . '</td>';
				} ?>
			</tr>

			<?
		}
		?>
		<tr>
			<td colspan="3"><strong>Remarks</strong></td>
			<? foreach ($remarks_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Fabric Color</strong></td>
			<td align="center"><strong>Body Color</strong></td>
			<td align="center"><strong>Lab Dip No</strong></td>
			<?
			foreach ($body_part_data as $val) {
				echo '<td align="center"><strong>Finish Fab. Qty</strong></td>';
				echo '<td align="center"><strong>Pro. Loss %</strong></td>';
				echo '<td align="center"><strong>Grey Fab. Qty</strong></td>';
			}
			?>
		</tr>
		<? foreach ($colorArr as $colorVal) {
			$i++;
			$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $color_library[$colorVal]; ?></td>
				<td align="center"><? echo implode(",", $gmt_color_library[$colorVal]);; ?></td>
				<td align="center"><? echo return_field_value("lapdip_no", "wo_po_lapdip_approval_info", "job_no_mst='" . $wo_job_no . "' and approval_status=3 and color_name_id=" . $colorVal . ""); ?></td>
				<? foreach ($body_part_data as $key_id => $val) {
					$color_wise_finish_tot_qty[$colorVal] += $finish_qty_arr[$key_id][$colorVal];
					$color_wise_grey_tot_qty[$colorVal] += $grey_qty_arr[$key_id][$colorVal];
					$all_color_finish_tot_qty[$key_id] += $finish_qty_arr[$key_id][$colorVal];
					$all_color_grey_tot_qty[$key_id] += $grey_qty_arr[$key_id][$colorVal];

					if (is_null($finish_qty_arr[$key_id][$colorVal]) || $finish_qty_arr[$key_id][$colorVal] == 0) {
						echo '<td align="right"></td>';
					} else {
						echo '<td align="right">' . number_format($finish_qty_arr[$key_id][$colorVal], 2) . '</td>';
					}
					if (is_null($process_loss_arr[$key_id][$colorVal]) || $process_loss_arr[$key_id][$colorVal] == 0) {
						echo '<td align="right"></td>';
					} else {
						echo '<td align="right">' . number_format($process_loss_arr[$key_id][$colorVal], 2) . '</td>';
					}
					if (is_null($grey_qty_arr[$key_id][$colorVal]) || $grey_qty_arr[$key_id][$colorVal] == 0) {
						echo '<td align="right"></td>';
					} else {
						echo '<td align="right">' . number_format($grey_qty_arr[$key_id][$colorVal], 2) . '</td>';
					}
				}
				?>
				<td align="right"><strong><? echo number_format($color_wise_finish_tot_qty[$colorVal], 2); ?></strong>
				</td>
				<td align="right"><strong><? echo number_format($color_wise_grey_tot_qty[$colorVal], 2); ?></strong>
				</td>
			</tr>
			<?
		}
		?>

		<tr bgcolor="#EEEEEE">
			<td align="center"></td>
			<td align="center">Total</td>
			<td align="center"></td>
			<? foreach ($body_part_data as $key_id => $val) {
				$grand_all_color_finish_tot_qty += $all_color_finish_tot_qty[$key_id];
				$grand_all_color_grey_tot_qty += $all_color_grey_tot_qty[$key_id];
				echo '<td align="right">' . number_format($all_color_finish_tot_qty[$key_id], 2) . '</td>';
				echo '<td></td>';
				echo '<td align="right">' . number_format($all_color_grey_tot_qty[$key_id], 2) . '</td>';
			}
			?>
			<td align="right"><strong><? echo number_format($grand_all_color_finish_tot_qty, 2); ?></strong></td>
			<td align="right"><strong><? echo number_format($grand_all_color_grey_tot_qty, 2); ?></strong></td>
		</tr>

		<tr>
			<td align="center"></td>
			<td align="center"><strong>Consumption For <? echo $costing_per; ?></strong></td>
			<td align="center"></td>
			<? foreach ($body_part_data as $val) {
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
			}
			?>
			<td align="right"><strong><? echo number_format($tot_consumption_arr, 2); ?></strong></td>
			<td align="right"><strong></strong></td>
		</tr>

	</table>
	<br>
	<?
	$yarn_dtls_sql = "select yarn_count_id,color_id,composition_id,composition_perc,yarn_type,sum(cons_qty) as cons_qty,brand_id, supplier_id from fabric_sales_order_yarn_dtls where mst_id=$sales_id and status_active=1 and is_deleted=0 group by yarn_count_id,color_id,composition_id,composition_perc,brand_id,yarn_type,supplier_id";
	$yarn_dtls_sql_data = sql_select($yarn_dtls_sql);
	
	
	/*echo '<pre>';
	print_r($uom_wise_arr);die;*/
	?>
	<div style="display: inline-flex;">
		<div style="float: left; margin-right: 20px;">
			<strong style="font: bold 12px tahoma;">Yarn Required Summary</strong>
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
				<tr bgcolor="#CCCCCC">
					<td align="center"><strong>Sl No</strong></td>
					<td align="center"><strong>Count</strong></td>
					<td align="center"><strong>Composition</strong></td>
					<td align="center"><strong>Color</strong></td>
					<td align="center"><strong>Type</strong></td>
					<td align="center"><strong>Req. Qty.</strong></td>
					<td align="center"><strong>Supplier</strong></td>
					<td align="center"><strong>Brand</strong></td>
				</tr>
				<?
				$i = 1;
				foreach ($yarn_dtls_sql_data as $rows) 
				{
					?>
					<tr>
						<td align="center"><? echo $i; ?></td>
						<td><? echo $yarnCount_arr[$rows[csf(yarn_count_id)]]; ?></td>
						<td><? echo $composition[$rows[csf(composition_id)]]; ?></td>
						<td><p><? echo $color_library[$rows[csf(color_id)]]; ?></p></td>
						<td><p><? echo $yarn_type[$rows[csf(yarn_type)]]; ?></p></td>
						<td align="right"><p><? echo $rows[csf(cons_qty)];
						$tot_cons_qty += $rows[csf(cons_qty)]; ?></p></td>
						<td><p><? echo $supplier_arr[$rows[csf(supplier_id)]]; ?></p></td>
						<td><p><? echo $brand_arr[$rows[csf(brand_id)]]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
				<tr>
					<td align="center"><strong></strong></td>
					<td align="center"><strong></strong></td>
					<td align="center"><strong>Total Grey</strong></td>
					<td align="center"><strong></strong></td>
					<td align="center"><strong></strong></td>
					<td align="right"><strong><? echo number_format($tot_cons_qty, 2); ?></strong></td>
					<td align="center"><strong></strong></td>
					<td align="center"><strong></strong></td>
				</tr>
			</table>
		</div>

		
	</div>
	<br>
	<br>

	<?
	echo get_spacial_instruction($salesOrderNo);

	// ============================== Approval Status Start =============
	$approved_sql=sql_select("SELECT mst_id, approved_by ,approved_date as approved_date
	from approval_history where entry_form=24 and mst_id=$sales_id and un_approved_by=0 order by id");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
	/*if(count($approved_sql)>0)
	{*/
		$sl=1;
		?>
		<div style="margin-top:15px">
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma; width:630px;" rules="all">
				<strong style="font: bold 12px tahoma;">Approval Status</strong>
				<thead>
					<tr bgcolor="#CCCCCC">
						<th width="20">SL</th>
						<th width="150">Name/Designation</th>
						<th width="80">Approval Date</th>
						<th width="30">Approval No</th>
					</tr>
				</thead>
				<? foreach ($approved_sql as $key => $value)
				{
					$approved_by=$user_lib_name[$value[csf("approved_by")]];
					$designation=$designation_lib[$user_lib_desg[$value[csf("approved_by")]]];
					$approved_date=explode(" ",$value[csf("approved_date")]);
					$am_pm=$approved_date[2];
					$approved_date=change_date_format($approved_date[0])." ".$approved_date[1];
					?>
					<tr>
						<td width="20"><? echo $sl; ?></td>
						<td width="150"><? echo $approved_by.'/'.$designation; ?></td>
						<td width="80"><? echo $approved_date ?></td>
						<td width="30"><? echo $sl; ?></td>
					</tr>
					<?
					$sl++;
				}
				?>
			</table>
		</div>
		<?
	//}
	// ============================== Approval Status End =============

	// ============================== Entry Status Start ==============
	$entry_status_sql=sql_select("SELECT  id, inserted_by, insert_date, updated_by, update_date  
		from fabric_sales_order_mst where entry_form=109 and id=$sales_id and sales_booking_no ='$booking_no' order by  inserted_by");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
	if(count($entry_status_sql)>0)
	{
		$sl=1;
		?>
		<div style="margin-top:15px">
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma; width:630px;" rules="all">
				<strong style="font: bold 12px tahoma;">Entry Status</strong>
				<thead>
					<tr bgcolor="#CCCCCC">
						<th width="20">SL</th>
						<th width="150">Name/Designation</th>
						<th width="80">Insert Date and Time</th>
						<th width="30">Insert and Update No</th>
					</tr>
				</thead>
				<? foreach ($entry_status_sql as $key => $value)
				{
					$inserted_by=$user_lib_name[$value[csf("inserted_by")]];
					$insert_designation=$designation_lib[$user_lib_desg[$value[csf("inserted_by")]]];
					$insert_date=explode(" ",$value[csf("insert_date")]);
					$insert_date=change_date_format($insert_date[0])." ".$insert_date[1];

					$updated_by=$user_lib_name[$value[csf("updated_by")]];
					$updated_designation=$designation_lib[$user_lib_desg[$value[csf("updated_by")]]];
					$update_date=explode(" ",$value[csf("update_date")]);
					$update_date=change_date_format($update_date[0])." ".$update_date[1];
					?>
					<tr>
						<td width="20"><? echo $sl; ?></td>
						<td width="150"><? echo $inserted_by.'/'.$insert_designation; ?></td>
						<td width="80"><? echo $insert_date ?></td>
						<td width="30"><? echo 1; ?></td>
					</tr>
					<?
					$sl++;
					if($value[csf("update_date")] !='0000-00-00 00:00:00' && $value[csf("update_date")] !='')
					{
						?>
						<tr>
							<td width="20"><? echo $sl; ?></td>
							<td width="150"><? echo $updated_by.'/'.$updated_designation; ?></td>
							<td width="80"><? echo $update_date ?></td>
							<td width="30"><? echo 2; ?></td>
						</tr>

						<?
						$sl++;
					}
				}
				?>
			</table>
		</div>
		<?
	}
	// ============================== Entry Status End ===============

	echo "<br><br><br><br><br>";
	echo signature_table(176, $data[0], "1000px","","10");

	exit();
}

if ($action == "fabric_sales_order_print2") {

	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$departmentArr = return_library_array("select id,department_name from lib_department", 'id', 'department_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$item_library = return_library_array("select id,item_name from lib_garment_item", "id", "item_name");
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');
	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');

	$update_serial = sql_select("select a.id, a.update_sl from fabric_sales_order_mst a where a.job_no = '" . $salesOrderNo . "'");

	$sql = "select d.id as sales_id,d.currency_id,d.delivery_date,d.within_group,d.job_no, d.buyer_id,d.booking_date,
	d.sales_booking_no,d.company_id,d.delivery_date,d.dealing_marchant,d.remarks,d.style_ref_no,d.season,d.attention,d.fabric_composition from fabric_sales_order_mst d
	where d.status_active =1 and d.is_deleted =0 and d.job_no='$salesOrderNo'";
	if ($sales_id == '') {
		$mst_data = sql_select($sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}

	$image_locationArr2 = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry_v2' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');

	$max_shipment_date . 'wew';
	$update_serial_yarn = return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id='" . $update_serial[0][csf("id")] . "'");
	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0"
	style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
	<tr>
		<td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="center"><strong><? echo $addressArr[$companyId]; ?></strong></td>
	</tr>
	<tr>
		<td colspan="5" align="center"><strong><? echo $formCaption; ?></strong></td>
	</tr>
</table>
<div align="center"><b>Grey Booking</b></div>
<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
	<tr>
	</tr>
	<tr>
		<td width="135"><strong>Buyer/Agent Name</strong></td>
		<td><? echo $buyerArr[$buyer_id]; ?></td>
		<td width="135"><strong>Dept.</strong></td>
		<td><? //echo $departmentArr[$product_dept]; ?></td>
		<td width="135"><strong>Garments Item</strong></td>
		<td><? //echo $item_string; ?></td>
		<td><strong>Sales Order No: <? echo $job_no; ?></strong></td>
	</tr>
	<tr>
		<td><strong>Style Ref.</strong></td>
		<td><? echo $style_ref_no; ?></td>
		<td><strong>Season</strong></td>
		<td><? echo $season; ?></td>
		<td><strong>Order Qnty</strong></td>
		<td><? //echo $po_quantity; ?></td>
		<td rowspan="9" valign="top" width="205"><? foreach ($image_locationArr2 as $path) { ?><img
			src="../../<? echo $path; ?>" height="100" width="100"><? } ?></td>
		</tr>
		<tr>
			<td><strong>Style Des.</strong></td>
			<td><? //echo $style_description; ?></td>
			<td><strong>Lead Time</strong></td>
			<td><? //echo $lead_time; ?></td>
			<td><strong>Job No</strong></td>
			<td><? //echo $job_no; ?></td>
		</tr>
		<tr>
			<td><strong>Order No</strong></td>
			<td style="overflow:hidden;text-overflow: ellipsis;word-break: break-all;;"
			colspan="3"><? //echo rtrim($po_no, ','); ?></td>
			<td><strong>Booking No</strong></td>
			<td><? echo $sales_booking_no; ?></td>
		</tr>
		<tr>
			<td><strong>Repeat No</strong></td>
			<td><? //echo $order_repeat_no; ?></td>
			<td><strong>Shipment Date</strong></td>
			<td><? echo 'First:' . change_date_format($shipment_date) . ',' . 'Last:' . change_date_format($max_shipment_date); ?></td>
			<td><strong>Booking Date</strong></td>
			<td><? echo change_date_format($booking_date); ?></td>
		</tr>
		<tr>
			<td><strong>Po Received Date</strong></td>
			<td><? echo change_date_format($po_received_date); ?></td>
			<td><strong>WO Prepared After</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Dealing Merchant</strong></td>
			<td><? echo $dealing_marArr[$dealing_marchant]; ?></td>
		</tr>
		<tr>
			<td><strong>Currency</strong></td>
			<td><? echo $currency[$currency_id]; ?></td>
			<td><strong>Quality Label</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Style Owner</strong></td>
			<td><? echo $companyArr[$style_owner]; ?></td>
		</tr>
		<tr>
			<td><strong>Attention</strong></td>
			<td colspan="3"><? echo $attention; ?></td>
			<td><strong>Delivery Date</strong></td>
			<td><? echo change_date_format($delivery_date); ?></td>
		</tr>
		<tr>
			<td><strong>Fabric Composition</strong></td>
			<td colspan="3"><? echo $fabric_composition; ?></td>
			<td><strong>Revised No</strong></td>
			<td><?
			$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='" . $bookingNo . "' and b.entry_form=7");
			echo $nameArray_approved[0][csf('approved_no')]; ?></td>
		</tr>
		<tr>
			<td><strong>Remarks</strong></td>
			<td colspan="3"><? echo $remarks; ?></td>
			<td><strong>Update Serial</strong></td>
			<td>
				Fabric: <? echo ($update_serial[0][csf("update_sl")] == "") ? 0 : $update_serial[0][csf("update_sl")]; ?>
				,
				Yarn: <? echo ($update_serial_yarn == "") ? 0 : $update_serial_yarn; ?>
			</td>
		</tr>
	</table>
	<br/>
	<?
	$dtls_sql = "select b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.work_scope, sum(b.grey_qty) grey_qty,sum(b.rmg_qty) as rmg_qty, b.pre_cost_fabric_cost_dtls_id,a.within_group,b.process_id
	from fabric_sales_order_mst a,fabric_sales_order_dtls b
	where a.id=$sales_id and a.job_no ='$salesOrderNo' and a.id=b.mst_id and a.status_active=1 and b.is_deleted=0
	group by b.body_part_id,b.color_type_id,b.fabric_desc,b.work_scope, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.color_id,b.pre_cost_remarks, b.pre_cost_fabric_cost_dtls_id,a.within_group,b.process_id order by b.body_part_id";

	$dtls_sql_data = sql_select($dtls_sql);
	$process_count=0;
	foreach ($dtls_sql_data as $rows) {
		$key = $rows[csf('body_part_id')] . '_' . $rows[csf('color_type_id')] . '_' . $rows[csf('fabric_desc')] . '_' . $rows[csf('gsm_weight')] . '_' . $rows[csf('dia')] . '_' . $rows[csf('pre_cost_remarks')];

		$colorArr[$rows[csf('color_id')]] = $rows[csf('color_id')];
		$finish_qty_arr[$key][$rows[csf('color_id')]] += $rows[csf('finish_qty')];
		$grey_qty_arr[$key][$rows[csf('color_id')]] += $rows[csf('grey_qty')];
		$process_loss_arr[$key][$rows[csf('color_id')]] += $rows[csf('process_loss')];

		$tot_finish_qty_arr += $rows[csf('finish_qty')];
		$tot_grey_qty_arr += $rows[csf('grey_qty')];

		list($construction, $compositions) = explode(',', $rows[csf('fabric_desc')]);
		$body_part_data[$key] = $rows[csf('body_part_id')];
		$color_type_id_arr[$key] = $rows[csf('color_type_id')];
		$constructions_arr[$key] = $construction;
		$compositions_arr[$key] = $compositions;

		$gsm_weight_arr[$key] = $rows[csf('gsm_weight')];
		$dia_arr[$key] = $rows[csf('dia')] . ',' . $fabric_typee[$rows[csf('width_dia_type')]];
		$work_scope_arr[$key] = $rows[csf('work_scope')];
		$remarks_arr[$key] = $rows[csf('pre_cost_remarks')];
		$rmg_qty_arr[$key] += $rows[csf('rmg_qty')];
		$fin_qty_arr[$key] += $rows[csf('finish_qty')];


		if($rows[csf('process_id')]!=""){$process_count+=1;}

		$process_name = '';
		$process_id_array = explode(",", $rows[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}
		$process_name_arr[$key] = $process_name;

	}

	$costing_per = "";
	$costing_per_qnty = 0;
	$costing_per_id = return_field_value("costing_per", "wo_pre_cost_mst", "job_no ='$wo_job_no'");
	if ($costing_per_id == 1) {
		$costing_per = "1 Dzn";
		$costing_per_qnty = 12;
	} elseif ($costing_per_id == 2) {
		$costing_per = "1 Pcs";
		$costing_per_qnty = 1;
	} elseif ($costing_per_id == 3) {
		$costing_per = "2 Dzn";
		$costing_per_qnty = 24;
	} elseif ($costing_per_id == 4) {
		$costing_per = "3 Dzn";
		$costing_per_qnty = 36;
	} elseif ($costing_per_id == 5) {
		$costing_per = "4 Dzn";
		$costing_per_qnty = 48;
	}


	$gmt_color_data = sql_select("select gmts_color_id,contrast_color_id FROM wo_pre_cos_fab_co_color_dtls WHERE job_no ='$wo_job_no'");
	foreach ($gmt_color_data as $gmt_color_row) {
		$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]] = $color_library[$gmt_color_row[csf("gmts_color_id")]];
	}

	?>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td colspan="3"><strong>Body Part</strong></td>
			<? foreach ($body_part_data as $val) {
				echo '<td colspan="3" align="center">' . $body_part[$val] . '</td>';
			} ?>
			<td rowspan="9"><strong>Total Finish</strong></td>
			<td rowspan="9"><strong>Total Grey</strong></td>
		</tr>
		<tr>
			<td colspan="3"><strong>Color Type</strong></td>
			<? foreach ($color_type_id_arr as $val) {
				echo '<td colspan="3" align="center">' . $color_type[$val] . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Fabric Construction </strong></td>
			<? foreach ($constructions_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Yarn Composition</strong></td>
			<? foreach ($compositions_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>GSM </strong></td>
			<? foreach ($gsm_weight_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Dia/Width (Inch)</strong></td>
			<? foreach ($dia_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="3"><strong>Consumption For 1 Dzn <? echo $costing_per; ?></strong></td>
			<? foreach ($body_part_data as $key_ids => $val) {
				//$tot_consumption_arr+=$val;echo '<td colspan="3">'.number_format($val,2).'</td>';

				list($body_part_id, $color_type_id, $fabric_desc, $gsm_weight, $dia, $pre_cost_remarks) = explode('_', $key_ids);
				if ($pre_cost_remarks == "") {
					$pre_cost_remarks = 0;
				}
				if ($dia != "") {
					$dia_con = " and b.dia_width='$dia'";
				} else {
					$dia_con = " and b.dia_width is null";
				}
				list($constrac_str, $compo_str) = explode(',', $fabric_desc);

				$sql = "select avg(b.cons) as cons from
				wo_pre_cost_fabric_cost_dtls a,
				wo_po_color_size_breakdown c,
				wo_pre_cos_fab_co_avg_con_dtls b,
				wo_booking_dtls d
				where a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				b.po_break_down_id=d.po_break_down_id and
				c.id=b.color_size_table_id and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no ='$bookingNo' and

				d.status_active=1 and
				d.is_deleted=0 and
				b.cons>0  and
				a.body_part_id='$body_part_id' and
				a.color_type_id='$color_type_id' and
				a.construction='$constrac_str' and
				a.composition='" . trim($compo_str) . "'  and

				a.gsm_weight='$gsm_weight' and
				b.remarks='$pre_cost_remarks'
				$dia_con
				";
				//if($rmg_qty_arr[$key_ids]==0){
				$con_sql_data = sql_select($sql);
				$conQTY = $con_sql_data[0][csf('cons')];
				//}
				//else
				//{
				//$conQTY=($fin_qty_arr[$key_ids]/$rmg_qty_arr[$key_ids])*$costing_per_qnty;
				//}

				$tot_consumption_arr += $conQTY;
				echo '<td colspan="3" align="center">' . number_format($conQTY, 2) . '</td>';
				//if($conQTY<1){echo $sql.'**';die;}
			}
			?>
		</tr>


		<tr>
			<td colspan="3"><strong>Work Scope</strong></td>
			<? foreach ($work_scope_arr as $val) {
				echo '<td colspan="3" align="center">' . $item_category[$val] . '</td>';
			} ?>
		</tr>
		<?
		if($process_count>0){
			?>
			<tr>
				<td colspan="3"><strong>Process</strong></td>
				<? foreach ($process_name_arr as $val) {
					echo '<td colspan="3" align="center">' . $val . '</td>';
				} ?>
			</tr>

			<?
		}
		?>

		<tr>
			<td colspan="3"><strong>Remarks</strong></td>
			<? foreach ($remarks_arr as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Fabric Color</strong></td>
			<td align="center"><strong>Body Color</strong></td>
			<td align="center"><strong>Lab Dip No</strong></td>
			<?
			foreach ($body_part_data as $val) {
				echo '<td align="center"><strong>Finish Fab. Qty</strong></td>';
				echo '<td align="center"><strong>Pro. Loss %</strong></td>';
				echo '<td align="center"><strong>Grey Fab. Qty</strong></td>';
			}
			?>
		</tr>
		<? foreach ($colorArr as $colorVal) {
			$i++;
			$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $color_library[$colorVal]; ?></td>
				<td align="center"><? echo implode(",", $gmt_color_library[$colorVal]);; ?></td>
				<td align="center"><? echo return_field_value("lapdip_no", "wo_po_lapdip_approval_info", "job_no_mst='" . $wo_job_no . "' and approval_status=3 and color_name_id=" . $colorVal . ""); ?></td>
				<? foreach ($body_part_data as $key_id => $val) {
					$color_wise_finish_tot_qty[$colorVal] += $finish_qty_arr[$key_id][$colorVal];
					$color_wise_grey_tot_qty[$colorVal] += $grey_qty_arr[$key_id][$colorVal];
					$all_color_finish_tot_qty[$key_id] += $finish_qty_arr[$key_id][$colorVal];
					$all_color_grey_tot_qty[$key_id] += $grey_qty_arr[$key_id][$colorVal];
					$process_loss_per = (($grey_qty_arr[$key_id][$colorVal] - $finish_qty_arr[$key_id][$colorVal])/$finish_qty_arr[$key_id][$colorVal])*100;

					if (is_null($finish_qty_arr[$key_id][$colorVal]) || $finish_qty_arr[$key_id][$colorVal] == 0) {
						echo '<td align="right"></td>';
					} else {
						echo '<td align="right">' . number_format($finish_qty_arr[$key_id][$colorVal], 2) . '</td>';
					}
					if (is_null($process_loss_arr[$key_id][$colorVal]) || $process_loss_arr[$key_id][$colorVal] == 0) {
						echo '<td align="right"></td>';
					} else {
						echo '<td align="right">' . number_format($process_loss_per, 2) . '</td>';
					}
					if (is_null($grey_qty_arr[$key_id][$colorVal]) || $grey_qty_arr[$key_id][$colorVal] == 0) {
						echo '<td align="right"></td>';
					} else {
						echo '<td align="right">' . number_format($grey_qty_arr[$key_id][$colorVal], 2) . '</td>';
					}
				}
				?>
				<td align="right"><strong><? echo number_format($color_wise_finish_tot_qty[$colorVal], 2); ?></strong>
				</td>
				<td align="right"><strong><? echo number_format($color_wise_grey_tot_qty[$colorVal], 2); ?></strong>
				</td>
			</tr>
			<?
		}
		?>

		<tr bgcolor="#EEEEEE">
			<td align="center"></td>
			<td align="center">Total</td>
			<td align="center"></td>
			<? foreach ($body_part_data as $key_id => $val) {
				$grand_all_color_finish_tot_qty += $all_color_finish_tot_qty[$key_id];
				$grand_all_color_grey_tot_qty += $all_color_grey_tot_qty[$key_id];
				echo '<td align="right">' . number_format($all_color_finish_tot_qty[$key_id], 2) . '</td>';
				echo '<td></td>';
				echo '<td align="right">' . number_format($all_color_grey_tot_qty[$key_id], 2) . '</td>';
			}
			?>
			<td align="right"><strong><? echo number_format($grand_all_color_finish_tot_qty, 2); ?></strong></td>
			<td align="right"><strong><? echo number_format($grand_all_color_grey_tot_qty, 2); ?></strong></td>
		</tr>

		<tr>
			<td align="center"></td>
			<td align="center"><strong>Consumption For <? echo $costing_per; ?></strong></td>
			<td align="center"></td>
			<? foreach ($body_part_data as $val) {
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
			}
			?>
			<td align="right"><strong><? echo number_format($tot_consumption_arr, 2); ?></strong></td>
			<td align="right"><strong></strong></td>
		</tr>

	</table>
	<br>
	<?
	$yarn_dtls_sql = "select yarn_count_id,color_id,composition_id,composition_perc,yarn_type,sum(cons_qty) as cons_qty,brand_id, supplier_id from fabric_sales_order_yarn_dtls where mst_id=$sales_id and status_active=1 and is_deleted=0 group by yarn_count_id,color_id,composition_id,brand_id,composition_perc,yarn_type,supplier_id";
	$yarn_dtls_sql_data = sql_select($yarn_dtls_sql);
	?>
	<strong style="font: bold 12px tahoma;">Yarn Required Summary</strong>
	<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Sl No</strong></td>
			<td align="center"><strong>Count</strong></td>
			<td align="center"><strong>Composition</strong></td>
			<td align="center"><strong>Color</strong></td>
			<td align="center"><strong>Type</strong></td>
			<td align="center"><strong>Req. Qty.</strong></td>
			<td align="center"><strong>Supplier</strong></td>
			<td align="center"><strong>Brand</strong></td>
		</tr>
		<?
		$i = 1;
		foreach ($yarn_dtls_sql_data as $rows) {
			?>
			<tr>
				<td align="center"><? echo $i; ?></td>
				<td><? echo $yarnCount_arr[$rows[csf(yarn_count_id)]]; ?></td>
				<td><? echo $composition[$rows[csf(composition_id)]]; ?></td>
				<td><p><? echo $color_library[$rows[csf(color_id)]]; ?></p></td>
				<td><p><? echo $yarn_type[$rows[csf(yarn_type)]]; ?></p></td>
				<td align="right"><p><? echo $rows[csf(cons_qty)];
				$tot_cons_qty += $rows[csf(cons_qty)]; ?></p></td>
				<td><p><? echo $supplier_arr[$rows[csf(supplier_id)]]; ?></p></td>
				<td><p><? echo $brand_arr[$rows[csf(brand_id)]]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<tr>
			<td align="center"><strong></strong></td>
			<td align="center"><strong></strong></td>
			<td align="center"><strong>Total Grey</strong></td>
			<td align="center"><strong></strong></td>
			<td align="center"><strong></strong></td>
			<td align="right"><strong><? echo number_format($tot_cons_qty, 2); ?></strong></td>
			<td align="center"><strong></strong></td>
			<td align="center"><strong></strong></td>
		</tr>
	</table>
	<br>
	<?
	?>
	<strong style="font: bold 12px tahoma;">Special Instruction</strong>
	<table border="1" rules="all" cellpadding="3" style="font-size: 12px;">
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Sl</strong></td>
			<td><strong>Terms</strong></td>
		</tr>
		<?
		$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$salesOrderNo'");
		if (count($data_array) > 0) {
			$i = 0;
			foreach ($data_array as $row) {
				$i++;
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td><? echo $row[csf('terms')]; ?></td>
				</tr>
				<?
			}
		}
		//		else {
		//			$data_array = sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
		//			if (count($data_array) > 0) {
		//				foreach ($data_array as $row) {
		//					$i++;
		//					?>
		<!--                    <tr>-->
			<!--                        <td align="center">--><?// echo $i; ?><!--</td>-->
			<!--                        <td>--><?// echo $row[csf('terms')]; ?><!--</td>-->
			<!--                    </tr>-->
        <!--					--><?//
		//				}
		//			}
		//		}
        ?>

    </tbody>
</table>
<?
exit();
}

if ($action == 'fabric_sales_order_print3')
{

	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$departmentArr = return_library_array("select id,department_name from lib_department", 'id', 'department_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$update_serial = sql_select("select a.id, a.update_sl from fabric_sales_order_mst a where a.sales_booking_no = '" . $bookingNo . "'");

	if ($db_type == 0)
	{
		$fabric_composition = "a.fabric_composition as fabric_composition";
		$group_concat = "group_concat(e.po_break_down_id) as po_break_down_id";
		
	}
	else
	{
		$fabric_composition = "cast(a.fabric_composition as nvarchar2(200)) fabric_composition";
		$group_concat = "listagg(e.po_break_down_id, ',') within group (order by e.po_break_down_id) as po_break_down_id";
	}


	$sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,$fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.attention,d.fabric_composition
	union all
	select a.attention,null as job_no,null as order_repeat_no,c.id as sales_id,b.composition as fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id,cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id,null as style_description,null as dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id,null as style_ref_no,null as team_leader,null as dealing_marchant,null as season_matrix, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date,c.attention as sales_attention,c.fabric_composition as sales_fab_comp from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.attention,c.id,b.composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date,a.attention,b.composition,c.attention,c.fabric_composition";


	$partial_sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id,$group_concat,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp FROM wo_booking_mst a,
	wo_po_details_master b,
	wo_po_break_down c,
	fabric_sales_order_mst d,
	wo_booking_dtls e
	WHERE
	a.booking_no=e.booking_no and
	e.po_break_down_id=c.id and
	a.id=d.booking_id and
	e.job_no=b.job_no and
	b.job_no=c.job_no_mst and
	a.pay_mode=5 and
	a.fabric_source in(1,2) and
	a.supplier_id=$companyId and
	a.status_active =1 and
	a.is_deleted =0 and
	a.item_category=2 and
	a.booking_no='$bookingNo' and
	a.entry_form=108
	group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.attention,d.fabric_composition";
	$mst_data = sql_select($sql);
	$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
	extract($mst_data);

	if ($sales_id == '') {
		$mst_data = sql_select($partial_sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}
	$po_break_down_id = implode(",", array_unique(explode(",", $mst_data['po_break_down_id'])));
	$po_quantity = return_field_value("sum(po_quantity)", "wo_po_break_down", "id in($po_break_down_id)");

	$sql_po = "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in($po_break_down_id) group by po_number";
	$data_array_po = sql_select($sql_po);
	$po_no = '';
	foreach ($data_array_po as $row_po) {
		$po_no .= $row_po[csf('po_number')] . ", ";
	}

	$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry_v2' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');
	if (count($image_locationArr) == 0) {
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='knit_order_entry' and master_tble_id='$wo_job_no' and file_type=1", 'id', 'image_location');
	}

	$lead_time = datediff("d", $po_received_date, date('d-M-Y', time()));

	$gmts_item_id_arr = explode(',', $gmts_item_id);
	foreach ($gmts_item_id_arr as $item_id) {
		if ($item_string == '') {
			$item_string = $garments_item[$item_id];
		} else {
			$item_string .= ',' . $garments_item[$item_id];
		}
	}
	$max_shipment_date . 'wew';
	$update_serial_yarn = return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id='" . $update_serial[0][csf("id")] . "'");
	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0" style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
		<tr>
			<td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="5" align="center"><strong><? echo $addressArr[$companyId]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="5" align="center"><strong><? echo $formCaption; ?></strong></td>
		</tr>
	</table>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td width="135"><strong>Buyer/Agent Name</strong></td>
			<td><? echo $buyerArr[$buyer_name]; ?></td>
			<td width="135"><strong>Dept.</strong></td>
			<td><? echo $departmentArr[$product_dept]; ?></td>
			<td width="135"><strong>Garments Item</strong></td>
			<td><? echo $item_string; ?></td>
			<td><strong>Sales Order No: <? echo $salesOrderNo; ?></strong></td>
		</tr>
		<tr>
			<td><strong>Style Ref.</strong></td>
			<td><? echo $style_ref_no; ?></td>
			<td><strong>Season</strong></td>
			<td><? echo $season_arr[$season]; ?></td>
			<td><strong>Order Qnty</strong></td>
			<td><? echo $po_quantity; ?></td>
			<td rowspan="9" valign="top" width="205">
				<? foreach ($image_locationArr as $path) { ?>
					<img src="../../<? echo $path; ?>" height="100" width="100">
				<? } ?>
			</td>
		</tr>
		<tr>
			<td><strong>Style Des.</strong></td>
			<td><? echo $style_description; ?></td>
			<td><strong>Lead Time</strong></td>
			<td><? echo $lead_time; ?></td>
			<td><strong>Job No</strong></td>
			<td><? echo $wo_job_no; ?></td>
		</tr>
		<tr>
			<td><strong>Order No</strong></td>
			<td style="overflow:hidden;text-overflow: ellipsis;word-break: break-all;" colspan="3"><? echo rtrim($po_no,','); ?></td>
			<td><strong>Booking No</strong></td>
			<td><? echo $booking_no; ?></td>
		</tr>
		<tr>
			<td><strong>Repeat No</strong></td>
			<td><? echo $order_repeat_no; ?></td>
			<td><strong>Shipment Date</strong></td>
			<td><? echo 'First:' . change_date_format($shipment_date) . ',' . 'Last:' . change_date_format($max_shipment_date); ?></td>
			<td><strong>Booking Date</strong></td>
			<td><? echo change_date_format($booking_date); ?></td>
		</tr>
		<tr>
			<td><strong>Po Received Date</strong></td>
			<td><? echo change_date_format($po_received_date); ?></td>
			<td><strong>WO Prepared After</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Dealing Merchant</strong></td>
			<td><? echo $dealing_marArr[$dealing_marchant]; ?></td>
		</tr>
		<tr>
			<td><strong>Currency</strong></td>
			<td><? echo $currency[$currency_id]; ?></td>
			<td><strong>Quality Label</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Style Owner</strong></td>
			<td><? echo $companyArr[$style_owner]; ?></td>
		</tr>
		<tr>
			<td><strong>Attention</strong></td>
			<td colspan="3"><? echo $sales_attention; ?></td>
			<td><strong>Delivery Date</strong></td>
			<td><? echo change_date_format($delivery_date); ?></td>
		</tr>
		<tr>
			<td><strong>Fabric Composition</strong></td>
			<td colspan="3"><? echo $sales_fab_comp; ?></td>
			<td><strong>Revised No</strong></td>
			<td>
				<?
				$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='" . $booking_no . "' and b.entry_form=7");
				echo $nameArray_approved[0][csf('approved_no')] - 1;; ?>
			</td>
		</tr>
		<tr>
			<td><strong>Remarks</strong></td>
			<td colspan="3"><? echo $remarks; ?></td>
			<td><strong>Update Serial</strong></td>
			<td>
				Fabric: <? echo ($update_serial[0][csf("update_sl")] == "") ? 0 : $update_serial[0][csf("update_sl")]; ?>
				,
				Yarn: <? echo ($update_serial_yarn == "") ? 0 : $update_serial_yarn; ?>
			</td>
		</tr>
	</table>
	<br/>
	<?php
	if ($booking_no != "" && $sales_id != "")
	{
		$dtls_sql = "select b.pre_cost_remarks,b.fabric_desc,b.gsm_weight,sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,sum(b.process_loss) process_loss,sum(b.rmg_qty) as rmg_qty,c.yarn_count_id,c.yarn_type,c.cons_ratio,c.deter_id,c.supplier_id,c.brand_id,c.cons_qty,c.composition_id from fabric_sales_order_mst a inner join fabric_sales_order_dtls b on a.id=b.mst_id left join fabric_sales_order_yarn_dtls c on (a.id=c.mst_id and b.determination_id=c.deter_id and b.gsm_weight=c.gsm) where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.status_active=1 and b.is_deleted=0 group by c.deter_id,b.gsm_weight,b.fabric_desc,b.pre_cost_remarks,c.yarn_count_id,c.yarn_type,c.cons_ratio,c.supplier_id,c.brand_id, c.cons_qty,c.composition_id order by c.deter_id, b.gsm_weight,b.fabric_desc";
		$dtls_sql_data = sql_select($dtls_sql);
		$sub_total_group_arr = array();
		foreach ($dtls_sql_data as $row) {
			$sub_total_group_arr[$row[csf("deter_id")]][$row[csf("fabric_desc")]][$row[csf("gsm_weight")]] ++;
		}
	}	
	
	?>
	<table width="100%" border="1" cellpadding="2" rules="all" style="font: 12px tahoma;">
		<tr style="background: #ccc;">
			<th>SL No</th>
			<th>Fabric Description</th>
			<th>Fabric GSM</th>
			<th>Finish Fabric QTY</th>
			<th>Process Loss %</th>
			<th>Grey Fabric QTY (Kg)</th>
			<th>Yarn Count</th>
			<th>Composition</th>
			<th>Yarn Type</th>
			<th>Cons Ratio</th>
			<th>Yarn QTY (Kg)</th>
			<th>Supplier</th>
			<th>Brand</th>
		</tr>
		<?php
		$i=$j=1;
		foreach ($dtls_sql_data as $row) {
			$row_span_count = $sub_total_group_arr[$row[csf("deter_id")]][$row[csf("fabric_desc")]][$row[csf("gsm_weight")]];
			$process_loss = ((number_format($row[csf("grey_qty")], 4, '.', '') - number_format($row[csf("finish_qty")], 4, '.', ''))/number_format($row[csf("finish_qty")], 4, '.', '')) * 100;
			?>
			<tr style="background: #f2f2f2;">
				<?php
				if($j > 1 && $j <= $row_span_count){
					?>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<?php
				}else{
					?>
					<td align="center"><?php echo $i;?></td>
					<td><?php echo $row[csf("fabric_desc")];?></td>
					<td align="center"><?php echo $row[csf("gsm_weight")];?></td>
					<td align="right"><?php echo number_format($row[csf("finish_qty")], 4, '.', '');?></td>
					<td align="center"><?php echo number_format($process_loss, 2, '.', '');?></td>
					<td align="right"><?php echo number_format($row[csf("grey_qty")], 4, '.', '');?></td>
					<?php
					$i++;
				}
				?>
				<td align="center"><?php echo $yarnCount_arr[$row[csf("yarn_count_id")]];?></td>
				<td align="center"><?php echo $composition[$row[csf("composition_id")]];?></td>
				<td align="center"><?php echo $yarn_type[$row[csf("yarn_type")]];?></td>
				<td align="right"><?php echo number_format($row[csf("cons_ratio")], 2, '.', '');?></td>
				<td align="right"><?php echo number_format($row[csf("cons_qty")], 2, '.', '');?></td>
				<td align="center"><?php echo $supplier_arr[$row[csf("supplier_id")]];?></td>
				<td align="center"><?php echo $brand_arr[$row[csf("brand_id")]];?></td>
			</tr>
			<?php
			$cons_ratio_total 	+= number_format($row[csf("cons_ratio")], 2, '.', '');
			$cons_qty_total 	+= number_format($row[csf("cons_qty")], 2, '.', '');

			if($j == $row_span_count){
				?>
				<tr>
					<th colspan="9" align="right">Sub Total</th>
					<th align="right"><?php echo number_format($cons_ratio_total, 2, '.', '');?></th>
					<th align="right"><?php echo number_format($cons_qty_total, 2, '.', '');?></th>
					<th></th>
				</tr>
				<?php
				$cons_ratio_total 	= 0.00;
				$cons_qty_total 	= 0.00;
				$j=0;
			}

			$cons_ratio_grand_total += number_format($row[csf("cons_ratio")], 2, '.', '');
			$cons_qty_grand_total 	+= number_format($row[csf("cons_qty")], 2, '.', '');
			$j++;
		}
		?>
		<tr style="border-top: 5px solid #777; background: #ccc;">
			<th colspan="9" align="right">Grand Total</th>
			<th align="right"><?php echo number_format($cons_ratio_grand_total, 2, '.', '');?></th>
			<th align="right"><?php echo number_format($cons_qty_grand_total, 2, '.', '');?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
	exit();
}

if ($action == 'fabric_sales_order_print4') // Print 3
{
	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption, $update_id) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$team_leaderArr = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", 'id', 'team_leader_name');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$update_serial = sql_select("select a.id, a.update_sl,a.booking_approval_date,a.revise_no from fabric_sales_order_mst a where a.sales_booking_no = '" . $bookingNo . "'");

	$fso_mst_sql = "SELECT a.booking_date as date_rel, a.dealing_marchant as prepared_by, a.team_leader as merchandiser, a.job_no as fso_job, a.po_job_no, a.style_ref_no, a.buyer_id as garment_unit, a.po_buyer as client, a.ship_mode, a.within_group, a.sales_booking_no
	FROM fabric_sales_order_mst a
	WHERE a.company_id=$companyId and a.job_no='$salesOrderNo' and a.status_active=1 and a.is_deleted=0";
	$fso_data = sql_select($fso_mst_sql);
	$job_nos_arr= explode(",",$fso_data[0][csf("po_job_no")]);
	foreach ($job_nos_arr as $job) {
		$job_nos .= "'".$job . "', ";
	}
	//echo $job_no;
	$job_nos = rtrim($job_nos, ", ");

	$sample_with_order_booking_arr= sql_select("select a.job_no, a.booking_no,b.booking_no as sample_with_order_booking from wo_booking_dtls a , wo_booking_dtls b where a.job_no=b.job_no and a.po_break_down_id=b.po_break_down_id and a.booking_no in('".$fso_data[0][csf('sales_booking_no')]."') and b.booking_type=4 group by a.job_no, a.booking_no,b.booking_no"); 

	$apvl_reqArr = return_library_array("SELECT job_no, apvl_req from wo_pre_cost_trim_cost_dtls where job_no in($job_nos) and trim_group=9 and status_active=1 and is_deleted=0", 'job_no', 'apvl_req');
	//echo $sql;
	if ($fso_data[0][csf("within_group")]==1)
	{
		$order_sql = "SELECT a.job_no, a.style_description, a.job_quantity, min(b.pub_shipment_date) as shipment_date,max(b.pub_shipment_date) as max_shipment_date, a.packing
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst  and a.job_no in($job_nos) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.style_description, a.job_quantity, a.packing, a.job_no";
		$order_data = sql_select($order_sql);
	}

	if ($fso_data[0][csf("within_group")]==1)
	{
		$buyerUnitArr = return_library_array("SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0", 'id', 'company_name');
	}
	else if ($fso_data[0][csf("within_group")] == 2)
	{
		$buyerUnitArr = return_library_array("SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", 'id', 'buyer_name');
	}

	// Highlight Color
	$aop_strip_sql="SELECT job_no, color_type_id FROM wo_pre_cost_fabric_cost_dtls WHERE job_no in($job_nos) and status_active=1 and is_deleted=0";
	$aop_strip_data = sql_select($aop_strip_sql);
	foreach ($aop_strip_data as $key => $value)
	{
		//$color_typeArr[$value[csf('job_no')]][$value[csf('color_type_id')]]=$value[csf('color_type_id')];
		$color_typeArr[$value[csf('job_no')]][$value[csf('color_type_id')]]++;
	}
	/*echo "<pre>";
	print_r($color_typeArr);*/
	$aopColor=$stripedColor="";
	foreach(explode(",", $job_nos) as $jobNo)
	{
		$jobNo=str_replace("'", "",$jobNo);
		if(($color_typeArr[$jobNo][5] || $color_typeArr[$jobNo][7])>0) $aopColor='background: green;';

		if(($color_typeArr[$jobNo][2] || $color_typeArr[$jobNo][3] || $color_typeArr[$jobNo][4] || $color_typeArr[$jobNo][6] || $color_typeArr[$jobNo][32] || $color_typeArr[$jobNo][33])>0) $stripedColor='background: green;';
	}
	$aop = $aopColor;
	$striped = $stripedColor;


	$wash_Emb_sql="SELECT job_no, emb_name FROM wo_pre_cost_embe_cost_dtls WHERE job_no in($job_nos) and status_active=1 and is_deleted=0";
	$wash_Emb_data = sql_select($wash_Emb_sql);
	foreach ($wash_Emb_data as $key => $value)
	{
		// $wash_EmbArr[$value[csf('job_no')]][$value[csf('emb_name')]]=$value[csf('emb_name')];
		$wash_EmbArr[$value[csf('job_no')]][$value[csf('emb_name')]]++;
	}


	$washColor=$embroideryColor=$printingColor="";
	foreach(explode(",", $job_nos) as $jobNo)
	{
		$jobNo=str_replace("'", "",$jobNo);
		if($wash_EmbArr[$jobNo][3]>0) $washColor='background: green;';
		if($wash_EmbArr[$jobNo][2]>0) $embroideryColor='background: green;';
		if($wash_EmbArr[$jobNo][1]>0) $printingColor='background: green;';
	}

	//$wash = ($wash_EmbArr[$job_no][3]) ? "background: green;" : "" ;
	$wash = $washColor;
	$embroidery = $embroideryColor;
	$printing = $printingColor;


	// REQUIRED INFORMATIONS
	$fso_dtls_sql="SELECT a.within_group,a.po_job_no, b.body_part_id, b.fabric_desc, b.width_dia_type, b.gsm_weight, b.dia, b.color_id, b.color_range_id, b.finish_qty, b.process_loss, b.grey_qty, b.determination_id, b.process_id, b.color_type_id
	FROM fabric_sales_order_mst a, fabric_sales_order_dtls b
	WHERE a.id=b.mst_id and a.company_id=$companyId and a.job_no='$salesOrderNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$dtls_data = sql_select($fso_dtls_sql);
	$thread_rospan=$processArr=array();$color_ids="";$po_job_no="";
	foreach ($dtls_data as $row)
	{
		$color_ids.=$row[csf('color_id')].",";
		$po_job_no.="'".$row[csf('po_job_no')]."',";
		$data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]]['finish_qty']=$row[csf('finish_qty')];
		$data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]]['process_loss']=$row[csf('process_loss')];
		$data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]]['grey_qty']=$row[csf('grey_qty')];
		$data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]]['process_id']=$row[csf('process_id')];

		$process_data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]][$row[csf('process_id')]]['process_id']=$row[csf('process_id')];

		foreach (explode(",",$row[csf('process_id')]) as  $p_id)
		{
			if($p_id!="")
			{
				$processArr[$row[csf('determination_id')]][$row[csf('color_id')]][$p_id]=$p_id;
			}
		}

		$deter_id_vs_Description[$row[csf('determination_id')]]=$row[csf('fabric_desc')];

		$proDaynamicHadArr[$row[csf('process_id')]]=$row[csf('process_id')];
		$thread_rospan[$row[csf('determination_id')]]++;
	}
	$color_ids=chop($color_ids,",");
	$po_job_no=chop($po_job_no,",");
	//echo $dtls_data[0][csf('color_type_id')].'Tipu';
	/*echo "<pre>";
	print_r($processArr);die;*/

	$maxProcess = 0;
	foreach($proDaynamicHadArr as $value)
	{
		$value = trim($value);
		if (!empty($value))
		{
			$arr = explode(",",$value);
			$maxProcess_count = count($arr);
			if($maxProcess_count>$maxProcess)
			{
				$maxProcess=$maxProcess_count;
			}
		}
	}
	//echo $maxProcess;

	$thread_Arr=$thread_dataArr=$thread_ratioArr=$thread_qtyArr=array();
	$thread_sql="SELECT b.deter_id, b.gsm, c.id as dtls_id, c.yarn_dtls_id, c.yarn_count_id, c.composition_id, c.yarn_type, c.cons_ratio, c.cons_qty,c.color_id FROM fabric_sales_order_mst a, fabric_sales_order_yarn b, fabric_sales_order_yarn_dtls c where a.id=b.mst_id and b.id=c.yarn_dtls_id and a.job_no='$salesOrderNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	// echo $thread_sql;

	$thread_data = sql_select($thread_sql);
	foreach ($thread_data as $key => $value)
	{

		$thread_dataArr[$value[csf('deter_id')]][$value[csf('gsm')]][$value[csf('dtls_id')]]['yarn_count_id']=$value[csf('yarn_count_id')];
		$thread_dataArr[$value[csf('deter_id')]][$value[csf('gsm')]][$value[csf('dtls_id')]]['composition_id']=$value[csf('composition_id')];
		$thread_dataArr[$value[csf('deter_id')]][$value[csf('gsm')]][$value[csf('dtls_id')]]['yarn_type']=$value[csf('yarn_type')];
		$thread_dataArr[$value[csf('deter_id')]][$value[csf('gsm')]][$value[csf('dtls_id')]]['cons_ratio']=$value[csf('cons_ratio')];
		$thread_dataArr[$value[csf('deter_id')]][$value[csf('gsm')]][$value[csf('dtls_id')]]['cons_qty']=$value[csf('cons_qty')];

		//$thread_Arr[$value[csf('dtls_id')]]=$value[csf('dtls_id')];
		//$thread_Arr[$value[csf('deter_id')]][$value[csf('color_id')]]=$value[csf('dtls_id')];
		$thread_Arr[$value[csf('deter_id')]][$value[csf('gsm')]][]=$value[csf('dtls_id')];
		$thread_max_Arr[$value[csf('yarn_dtls_id')]][]=$value[csf('dtls_id')];
	}
	/*echo '<pre>';
	print_r($thread_dataArr);
	echo "</pre><br>";
	echo '<pre>';
	print_r($thread_Arr);
	die;*/

	$maxThread_count = 0;
	foreach($thread_max_Arr as $value)
	{
		if(count($value)>0){
			if(count($value) > $maxThread_count){
				$maxThread_count = count($value);
			}
		}
	}
	//echo $maxThread_count;die;

	//$wo_job_no = $order_data[0][csf('job_no')];
	$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry_v2' and master_tble_id='$update_id' and file_type=1", 'id', 'image_location');
	if (count($image_locationArr) == 0) {
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='knit_order_entry' and master_tble_id=$job_nos and file_type=1", 'id', 'image_location');
	}
	$lapdip_sql_arr=sql_select("select lapdip_no,color_name_id,job_no_mst from wo_po_lapdip_approval_info where job_no_mst in($po_job_no) and color_name_id in($color_ids) and status_active=1 and is_deleted=0");
	foreach($lapdip_sql_arr as $row)
	{
		$lapdip_arr[$row[csf('color_name_id')]]["lapdip_no"]=$row[csf('lapdip_no')];
	}

	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0"
		style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
		<tr>
			<td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="5" align="center"><strong><? echo "KNITTING & DYEING SCHEDULE"; ?></strong></td>
		</tr>
	</table>

	<table width="1180" cellspacing="0" align="left" border="0">
		<tr>
			<td width="130"><strong>DATE REL: </strong></td><td width="175"><? echo change_date_format($fso_data[0][csf("date_rel")]); ?></td>
			<td width="130"><strong>PREPARED BY: </strong></td><td width="175px"> <? echo $dealing_marArr[$fso_data[0][csf("prepared_by")]]; ?></td>
			<td width="130"><strong>MERCHANDISER: </strong></td><td width="175"><? echo $team_leaderArr[$fso_data[0][csf("merchandiser")]]; ?></td>
		</tr>
		<tr>
			<td><strong>ORDER/FSOE NO: </strong></td><td><? echo $fso_data[0][csf("fso_job")]; ?></td>
			<td><strong>STYLE REFERENCE: </strong></td><td><? echo $fso_data[0][csf("style_ref_no")]; ?></td>
			<td><strong>GARMENT UNIT: </strong></td><td><? echo $buyerUnitArr[$fso_data[0][csf("garment_unit")]]; ?></td>
		</tr>
		<tr>
			<td><strong>CLIENT: </strong></td><td><? echo $buyerArr[$fso_data[0][csf("client")]]; ?></td>
			<td><strong>SHIPMENT MODE: </strong></td><td><? echo $shipment_mode[$fso_data[0][csf("ship_mode")]]; ?></td>
			<td><strong>STYLE: </strong></td><td><? echo $order_data[0][csf('style_description')]; ?></td>
		</tr>
		<tr>
			<td><strong>SHIPMENT DATE: </strong></td><td><? echo change_date_format($order_data[0][csf('shipment_date')]); ?></td>
			<td><strong>ORDERED QTY: </strong></td><td><? echo $order_data[0][csf('job_quantity')]; ?></td>
			<td></td><td></td>
		</tr>
		<tr>
			<td><strong>MAIN FABRIC BOOKING NO: </strong></td><td><? echo $fso_data[0][csf("sales_booking_no")]; ?></td>
			<td><strong>SAMPLE FABRIC BOOKING NO: </strong></td><td><? echo $sample_with_order_booking_arr[0][csf("sample_with_order_booking")]; ?></td>
			<td></td><td></td>
		</tr>
		<tr style=" height:20px">
			<td colspan="6">&nbsp;</td>
		</tr>
	</table>

	<table width="990" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td width="130"><strong>MAIN PROCESS</strong></td>
			<td width="130"><strong>NORMAL</strong></td>
			<td width="130" style="<? echo $aop; ?>"><strong>AOP/TYPE</strong></td>
			<td width="130" style="<? echo $wash; ?>"><strong>GMT WASH/TYPE</strong></td>
			<td width="130"><strong>SPECIAL FINISHING/TYPE</strong></td>
			<td width="130" style="<? echo $embroidery; ?>"><strong>EMBROIDERY</strong></td>
			<td width="150" style="<? echo $printing; ?>"><strong>PANEL PRINTING</strong></td>
			<td width="130" style="<? echo $striped; ?>"><strong>STRIPED</strong></td>
			<td width="130"><strong>CPB DYEING</strong></td>
		</tr>
	</table>

	<br>

	<table width="790" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td width="130"><strong>External lab test- required (y/n)</strong></td>
			<td width="50"></td>
			<td width="50"></strong></td>
			<td width="130"><strong>SOLID /ASSORTED</strong></td>
			<td width="100"><? echo $packing[$order_data[0][csf('packing')]]; ?></td>
			<td width="130"><strong>Availabel stock-Y/N</strong></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="60" rowspan="2"><strong>Lab Ref</strong></td>
			<td width="100" rowspan="2"></td>
		</tr>
		<tr>
			<td><strong>1st bulk approval required (y/n)</strong></td>
			<td></td>
			<td></td>
			<td><strong>CARE TEST - REQ (Y/N)</strong></td>
			<td><? echo $yes_no[$apvl_reqArr[$fso_data[0][csf("po_job_no")]]]; ?></td>
			<td><strong>Fabric Shrinkage valu</strong></td>
			<td></td>
			<td></td>
		</tr>
	</table>

	<br>

	<table width="750" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td width="150"><strong>FABRIC SIDE TO USE - RIGHT/WRONG</strong></td>
			<td width="150"><strong>BEST MARKER WIDTH</strong></td>
			<td width="150"><strong>CUT METHOD - PLAIN/STRIPE</strong></td>
			<td width="150"><strong>FABRIC ORIENTATION - 1WAY/2WAY</strong></td>
			<td width="150"><strong>PURCHASED IN DYED FORM - Y/N</strong></td>
		</tr>
		<tr>
			<td height="20"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>

	<br>
	<!-- REQUIRED INFORMATIONS -->
	<td align="center"><strong style="font: bold 12px tahoma; text-align: right;">REQUIRED INFORMATIONS</strong></td>
	<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Body part</strong></td>
			<td align="center"><strong>Fabric Type & Composition</strong></td>
			<td align="center"><strong>ROUTING - CAL/STEN</strong></td>
			<td align="center"><strong>REQ DENSITY</strong></td>
			<td align="center"><strong>REQ WIDTH</strong></td>
			<td align="center"><strong>COLOR</strong></td>
			<td align="center"><strong>LAPDIP NO</strong></td>
			<td align="center"><strong>SHADE TYPE</strong></td>
			<td align="center"><strong>DYE METHOD</strong></td>
			<td align="center"><strong>ENZYME</strong></td>
			<td align="center"><strong>FINISHED QTY</strong></td>
			<td align="center"><strong>WEIGHT LOSS</strong></td>
			<td align="center"><strong>GREIGE QTY</strong></td>
			<?
			for ($i=1; $maxThread_count>=$i  ; $i++) {
				?>
				<td align="center"><strong><? echo 'THREAD '.$i; ?></strong></td>
				<?
			}
			?>
		</tr>
		<?
		$i = 1;
		$chk = array();
		foreach ($data_arr as $body_part_id => $body_part_val)
		{
			foreach ($body_part_val as $determination_id => $determination_val)
			{
				foreach ($determination_val as $width_dia_type_id => $width_dia_type)
				{
					foreach ($width_dia_type as $gsm_weight_id => $gsm_weight)
					{
						foreach ($gsm_weight as $dia_id => $dia_val)
						{	$k=0;
							foreach ($dia_val as $color_id => $color_value)
							{
								foreach ($color_value as $color_range_id => $row)
								{
									?>
									<tr>
										<td align="center"><? echo $body_part[$body_part_id]; ?></td>
										<td><p><? echo $row['fabric_desc']; ?></p></td>
										<td><p><? echo $fabric_typee[$width_dia_type_id]; ?></p></td>
										<td><p><? echo $gsm_weight_id; ?></p></td>
										<td><p><? echo $dia_id; ?></p></td>
										<td><p><? echo $color_library[$color_id]; ?></p></td>
										<td><p><? echo $lapdip_arr[$color_id]["lapdip_no"]; ?></p></td>
										<td><p><? echo $color_range[$color_range_id]; ?></p></td>
										<td><p><? echo ""; ?></p></td>
										<td><p><? echo ""; ?></p></td>
										<td align="right"><p><? echo $row['finish_qty']; ?></p></td>
										<td><p><? echo $row['process_loss']; ?></p></td>
										<td align="right"><p><? echo $row['grey_qty']; ?></p></td>
										<?
										foreach ($thread_Arr[$determination_id][$gsm_weight_id] as $key => $value)
										{
											$yarn_count_id=$thread_dataArr[$determination_id][$gsm_weight_id][$value]['yarn_count_id'];
											$yarn_composition=$thread_dataArr[$determination_id][$gsm_weight_id][$value]['composition_id'];
											$yarn_type_id=$thread_dataArr[$determination_id][$gsm_weight_id][$value]['yarn_type'];
											if($k==0){
												?>
												<td align="center" rowspan="<? //echo $thread_rospan[$determination_id]; ?>" title="<? echo $value; ?>"><? echo $yarnCount_arr[$yarn_count_id].', '.$composition[$yarn_composition].', '.$yarn_type[$yarn_type_id]; ?></td>
												<?
											}
										}
										for($i=0;$i<($maxThread_count-count($thread_Arr[$determination_id][$gsm_weight_id]));$i++)
										{
											if($k==0){
											?>
											<td rowspan="<? //echo $thread_rospan[$determination_id]; ?>"><?php ''; ?></td>
											<?php
											}
										}

										$k++;
										?>
									</tr>
									<?
									$i++;
								}
							}
							?>
							<tr>
								<td align="right" colspan="13"><strong>Consumption Ratio</strong></td>
								<?
								foreach ($thread_Arr[$determination_id][$gsm_weight_id] as $key => $value)
								{
									$cons_ratio=$thread_dataArr[$determination_id][$gsm_weight_id][$value]['cons_ratio'];
									?>
									<td align="center" title="<? echo $value; ?>"><? echo $cons_ratio; ?></td>
									<?
								}
								for($i=0;$i<($maxThread_count-count($thread_Arr[$determination_id][$gsm_weight_id]));$i++)
								{
									?>
									<td><?php ''; ?></td>
									<?php
								}?>
							</tr>
							<tr>
								<td align="right" colspan="13"><strong>Count Wise Consumption Qty</strong></td>
								<?
								foreach ($thread_Arr[$determination_id][$gsm_weight_id] as $key => $value)
								{
									$cons_qty=$thread_dataArr[$determination_id][$gsm_weight_id][$value]['cons_qty'];
									?>
									<td align="center" title="<? echo $value; ?>"><? echo $cons_qty; ?></td>
									<?
								}
								for($i=0;$i<($maxThread_count-count($thread_Arr[$determination_id][$gsm_weight_id]));$i++)
								{
									?>
									<td><?php ''; ?></td>
									<?php
								}?>
							</tr>
							<?
						}
					}
				}
			}
		}
		?>
	</table>

	<br>

	<!-- PROCESS ROUTING -->
	<td align="center"><strong style="font: bold 12px tahoma; text-align: right;">PROCESS ROUTING</strong></td>
	<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
		<tr>
			<td align="center"><strong>Color</strong></td>
			<td align="center"><strong>Fabric Type & Composition</strong></td>
			<?php
			$j=1;
			for($i=0;$i<$maxProcess;$i++)
			{
			?>
			<td style="text-align: center;"><?php echo $j; ?></td>
			<?php
			$j++;
			}

			?>
		</tr>
		<?
		$i = 1;
		/*echo "<pre>";
		print_r($data_arr);die;*/
		foreach ($process_data_arr as $body_part_id => $body_part_val)
		{
			foreach ($body_part_val as $determination_id => $determination_val)
			{
				foreach ($determination_val as $width_dia_type_id => $width_dia_type)
				{
					foreach ($width_dia_type as $gsm_weight_id => $gsm_weight)
					{
						foreach ($gsm_weight as $dia_id => $dia_val)
						{
							foreach ($dia_val as $color_id => $color_value)
							{
								foreach ($color_value as $color_range_id => $color_range)
								{
									foreach ($color_range as $process_id => $row)
									{
										if ($row['process_id']!="")
										{
											if(!empty($processArr[$determination_id][$color_id]))
											{
												?>
												<tr>
													<td align="center" title="<? echo $determination_id.'='.$color_id; ?>"><? echo $color_library[$color_id]; ?></td>
													<td><p><? echo $deter_id_vs_Description[$determination_id]; ?></p></td>
													<?php
													foreach ($processArr[$determination_id][$color_id] as $key => $value)
													{
														?>
														<td style="text-align: center;"><?php echo $conversion_cost_head_array[$key]; ?></td>
														<?php
														$j++;
													}
													for($i=0;$i<($maxProcess-count($processArr[$determination_id][$color_id]));$i++)
													{
														?>
														<td><?php ''; ?></td>
														<?php
													}
													?>
												</tr>
												<?
												$i++;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		?>
	</table>

	<br>

	<!-- Show Picture and Spacial Instruction-->
	<style type="text/css">
		.parent {
		  display: flex;
		  flex-direction:row;
		}

		.column {
		  flex: 1 1 0px;
		}
	</style>
	<div style="width:1000px; margin-left:10px" class="parent">
		<div width="250">
			<table width="480" cellspacing="0" align="center">
				<tr>
					<td rowspan="3" width="70"><? echo get_spacial_instruction($fso_data[0][csf("sales_booking_no")]); ?></td>
				</tr>
			</table>
		</div>
		<div class="column">
			<table width="480" cellspacing="0" align="left" border="1" rules="all" cellpadding="3">
				<tr>
					<td align="center" width="470"><strong>TECHNICAL SKETCH/PICTURE</strong></td>
				</tr>
				<td valign="top" width="300" style="text-align: center;"><? foreach ($image_locationArr as $path) { ?><img src="../../<? echo $path; ?>" height="300" width="300"><? } ?></td>
			</table>
		</div>
  	</div>

	<!-- Signature -->
	<? echo signature_table(176, $data[0],"900px"); ?>

	<?
	exit();
}
if ($action == "fabric_sales_order_print5") 
{

	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$departmentArr = return_library_array("select id,department_name from lib_department", 'id', 'department_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$item_library = return_library_array("select id,item_name from lib_garment_item", "id", "item_name");
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');
	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');

	$update_serial = sql_select("select a.id, a.update_sl from fabric_sales_order_mst a where a.job_no = '" . $salesOrderNo . "'");

	$sql = "select d.id as sales_id,d.currency_id,d.delivery_date,d.within_group,d.job_no, d.buyer_id,d.booking_date,
	d.sales_booking_no,d.company_id,d.delivery_date,d.dealing_marchant,d.remarks,d.style_ref_no,d.season,d.attention,d.fabric_composition from fabric_sales_order_mst d
	where d.status_active =1 and d.is_deleted =0 and d.job_no='$salesOrderNo'";
	if ($sales_id == '') {
		$mst_data = sql_select($sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}

	$image_locationArr2 = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry_v2' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');

	$max_shipment_date . 'wew';
	$update_serial_yarn = return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id='" . $update_serial[0][csf("id")] . "'");
	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0"
	style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
	<tr>
		<td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong>
		</td>
	</tr>
	<tr>
		<td colspan="5" align="center"><strong><? echo $addressArr[$companyId]; ?></strong></td>
	</tr>
	<tr>
		<td colspan="5" align="center"><strong><? echo $formCaption; ?></strong></td>
	</tr>
	</table>
	<div align="center"><b>Grey Booking</b></div>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
		</tr>
		<tr>
			<td width="135"><strong>Buyer/Agent Name</strong></td>
			<td><? echo $buyerArr[$buyer_id]; ?></td>
			<td width="135"><strong>Dept.</strong></td>
			<td><? //echo $departmentArr[$product_dept]; ?></td>
			<td width="135"><strong>Garments Item</strong></td>
			<td><? //echo $item_string; ?></td>
			<td><strong>Sales Order No: <? echo $job_no; ?></strong></td>
		</tr>
		<tr>
			<td><strong>Style Ref.</strong></td>
			<td><? echo $style_ref_no; ?></td>
			<td><strong>Season</strong></td>
			<td><? echo $season; ?></td>
			<td><strong>Order Qnty</strong></td>
			<td><? //echo $po_quantity; ?></td>
			<td rowspan="9" valign="top" width="205"><? foreach ($image_locationArr2 as $path) { ?><img
				src="../../<? echo $path; ?>" height="100" width="100"><? } ?></td>
			</tr>
			<tr>
				<td><strong>Style Des.</strong></td>
				<td><? //echo $style_description; ?></td>
				<td><strong>Lead Time</strong></td>
				<td><? //echo $lead_time; ?></td>
				<td><strong>Job No</strong></td>
				<td><? //echo $job_no; ?></td>
			</tr>
			<tr>
				<td><strong>Order No</strong></td>
				<td style="overflow:hidden;text-overflow: ellipsis;word-break: break-all;;"
				colspan="3"><? //echo rtrim($po_no, ','); ?></td>
				<td><strong>Booking No</strong></td>
				<td><? echo $sales_booking_no; ?></td>
			</tr>
			<tr>
				<td><strong>Repeat No</strong></td>
				<td><? //echo $order_repeat_no; ?></td>
				<td><strong>Shipment Date</strong></td>
				<td><? echo 'First:' . change_date_format($shipment_date) . ',' . 'Last:' . change_date_format($max_shipment_date); ?></td>
				<td><strong>Booking Date</strong></td>
				<td><? echo change_date_format($booking_date); ?></td>
			</tr>
			<tr>
				<td><strong>Po Received Date</strong></td>
				<td><? echo change_date_format($po_received_date); ?></td>
				<td><strong>WO Prepared After</strong></td>
				<td><? echo $row[csf('')]; ?></td>
				<td><strong>Dealing Merchant</strong></td>
				<td><? echo $dealing_marArr[$dealing_marchant]; ?></td>
			</tr>
			<tr>
				<td><strong>Currency</strong></td>
				<td><? echo $currency[$currency_id]; ?></td>
				<td><strong>Quality Label</strong></td>
				<td><? echo $row[csf('')]; ?></td>
				<td><strong>Style Owner</strong></td>
				<td><? echo $companyArr[$style_owner]; ?></td>
			</tr>
			<tr>
				<td><strong>Attention</strong></td>
				<td colspan="3"><? echo $attention; ?></td>
				<td><strong>Delivery Date</strong></td>
				<td><? echo change_date_format($delivery_date); ?></td>
			</tr>
			<tr>
				<td><strong>Fabric Composition</strong></td>
				<td colspan="3"><? echo $fabric_composition; ?></td>
				<td><strong>Revised No</strong></td>
				<td><?
				$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='" . $bookingNo . "' and b.entry_form=7");
				echo $nameArray_approved[0][csf('approved_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td>
				<td colspan="3"><? echo $remarks; ?></td>
				<td><strong>Update Serial</strong></td>
				<td>
					Fabric: <? echo ($update_serial[0][csf("update_sl")] == "") ? 0 : $update_serial[0][csf("update_sl")]; ?>
					,
					Yarn: <? echo ($update_serial_yarn == "") ? 0 : $update_serial_yarn; ?>
				</td>
			</tr>
		</table>
		<br/>
		<?
		$dtls_sql = "select b.pre_cost_remarks,b.fabric_desc,b.gsm_weight,sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,sum(b.process_loss) process_loss,sum(b.rmg_qty) as rmg_qty,b.cons_uom, b.avg_rate,sum(b.amount) as amount,c.yarn_count_id,c.yarn_type,c.cons_ratio,c.deter_id,c.supplier_id,c.brand_id,c.cons_qty,c.composition_id from fabric_sales_order_mst a inner join fabric_sales_order_dtls b on a.id=b.mst_id left join fabric_sales_order_yarn_dtls c on (a.id=c.mst_id and b.determination_id=c.deter_id and b.gsm_weight=c.gsm) where a.id=$sales_id and a.job_no ='$salesOrderNo' and a.status_active=1 and b.is_deleted=0 group by c.deter_id,b.gsm_weight,b.fabric_desc,b.pre_cost_remarks,b.cons_uom, b.avg_rate,c.yarn_count_id,c.yarn_type,c.cons_ratio,c.supplier_id,c.brand_id, c.cons_qty,c.composition_id order by c.deter_id, b.gsm_weight,b.fabric_desc";

		$dtls_sql_data = sql_select($dtls_sql);
		$sub_total_group_arr = array();
		foreach ($dtls_sql_data as $row) {
			$sub_total_group_arr[$row[csf("deter_id")]][$row[csf("fabric_desc")]][$row[csf("gsm_weight")]] ++;
		}
		$process_count=0;
		
		?>
		<table width="100%" border="1" cellpadding="2" rules="all" style="font: 12px tahoma;">
		<tr style="background: #ccc;">
			<th>SL No</th>
			<th>Fabric Description</th>
			<th>Fabric GSM</th>
			<th>Cons. UOM</th>
			<th>Finish Fabric QTY</th>
			<th>Avg. Price</th>
			<th>Amount</th>
			<th>Process Loss %</th>
			<th>Grey Fabric QTY (Kg)</th>
			<th>Yarn Count</th>
			<th>Composition</th>
			<th>Yarn Type</th>
			<th>Cons Ratio</th>
			<th>Yarn QTY (Kg)</th>
			<th>Supplier</th>
			<th>Brand</th>
		</tr>
		<?php
		$i=$j=1;

		foreach ($dtls_sql_data as $row) {
			$row_span_count = $sub_total_group_arr[$row[csf("deter_id")]][$row[csf("fabric_desc")]][$row[csf("gsm_weight")]];
			$process_loss = ((number_format($row[csf("grey_qty")], 4, '.', '') - number_format($row[csf("finish_qty")], 4, '.', ''))/number_format($row[csf("finish_qty")], 4, '.', '')) * 100;
			?>
			<tr style="background: #f2f2f2;">
				<?php
				if($j > 1 && $j <= $row_span_count){
					?>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<?php
				}else{
					?>
					<td align="center"><?php echo $i; ?></td>
					<td><?php echo $row[csf("fabric_desc")];?></td>
					<td align="center"><?php echo $row[csf("gsm_weight")];?></td>
					<td align="center"><?php echo $unit_of_measurement[$row[csf("cons_uom")]];?></td>
					<td align="right"><?php echo number_format($row[csf("finish_qty")], 4, '.', '');?></td>
					<td align="right"><?php echo number_format($row[csf("avg_rate")], 4, '.', '');?></td>
					<td align="right"><?php echo number_format($row[csf("amount")], 4, '.', '');?></td>
					<td align="center"><?php echo number_format($process_loss, 2, '.', '');?></td>
					<td align="right"><?php echo number_format($row[csf("grey_qty")], 4, '.', '');?></td>
					<?php
					$i++;
				}
				?>
				<td align="center"><?php echo $yarnCount_arr[$row[csf("yarn_count_id")]];?></td>
				<td align="center"><?php echo $composition[$row[csf("composition_id")]];?></td>
				<td align="center"><?php echo $yarn_type[$row[csf("yarn_type")]];?></td>
				<td align="right"><?php echo number_format($row[csf("cons_ratio")], 2, '.', '');?></td>
				<td align="right"><?php echo number_format($row[csf("cons_qty")], 2, '.', '');?></td>
				<td align="center"><?php echo $supplier_arr[$row[csf("supplier_id")]];?></td>
				<td align="center"><?php echo $brand_arr[$row[csf("brand_id")]];?></td>
			</tr>
			<?php
			$cons_ratio_total 	+= number_format($row[csf("cons_ratio")], 2, '.', '');
			$cons_qty_total 	+= number_format($row[csf("cons_qty")], 2, '.', '');

			if($j == $row_span_count){
				?>
				<tr>
					<th colspan="12" align="right">Sub Total</th>
					<th align="right"><?php echo number_format($cons_ratio_total, 2, '.', '');?></th>
					<th align="right"><?php echo number_format($cons_qty_total, 2, '.', '');?></th>
					<th></th>
				</tr>
				<?php
				$cons_ratio_total 	= 0.00;
				$cons_qty_total 	= 0.00;
				$j=0;
			}

			$cons_ratio_grand_total += number_format($row[csf("cons_ratio")], 2, '.', '');
			$cons_qty_grand_total 	+= number_format($row[csf("cons_qty")], 2, '.', '');
			$j++;
		}
		?>
		<tr style="border-top: 5px solid #777; background: #ccc;">
			<th colspan="12" align="right">Grand Total</th>
			<th align="right"><?php echo number_format($cons_ratio_grand_total, 2, '.', '');?></th>
			<th align="right"><?php echo number_format($cons_qty_grand_total, 2, '.', '');?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?

	exit();
}
if ($action == "terms_condition_popup")
{
	echo load_html_head_contents("Order Search", "../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
	<script>
		function add_break_down_tr_clone(i) {
			var row_num = $('#tbl_termcondi_details tr').length - 1;
			if (row_num != i) {
				return false;
			}
			else {
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function () {
					$(this).attr({
						'id': function (_, id) {
							var id = id.split("_");
							return id[0] + "_" + i
						},
						'value': function (_, value) {
							return value
						}
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_' + i).removeAttr("onClick").attr("onClick", "add_break_down_tr_clone(" + i + ");");
				$('#decrease_' + i).removeAttr("onClick").attr("onClick", "fn_deletebreak_down_tr(" + i + ")");
				$('#termscondition_' + i).val("");
				$('#tbl_termcondi_details tr:last td:first').html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo) {
			$('#tbl_termcondi_details tbody tr#settr_' + rowNo).remove();

		}

		function fnc_sales_order_terms_condition(operation) {
			var formData = $("#termscondi_1").serialize();
			var data = "action=save_update_delete_sales_order_terms_condition&operation=" + operation + "&" + formData;
			freeze_window(operation);
			http.open("POST", "fabric_sales_order_entry_v2_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sales_order_terms_condition_reponse;
		}

		function fnc_sales_order_terms_condition_reponse() {

			if (http.readyState == 4) {
				var reponse = trim(http.responseText).split('**');
				if (reponse[0].length > 2) reponse[0] = 10;
				release_freezing();
				if (reponse[0] == 0 || reponse[0] == 1) {
					parent.emailwindow.hide();
				}
			}
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:100%;">
			<? echo load_freeze_divs("../../", $permission); ?>
			<fieldset>
				<form id="termscondi_1" autocomplete="off">
					<input type="hidden" id="txt_job_no" name="txt_job_no"
					value="<? echo str_replace("'", "", $txt_job_no) ?>"/>


					<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
						<thead>
							<tr>
								<th width="50">Sl</th>
								<th width="530">Terms</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?
						$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_job_no");// quotation_id='$data'
						if (count($data_array) > 0) {
							$i = 0;
							foreach ($data_array as $row) {
								$i++;
								?>
								<tr id="settr_<? echo $i; ?>" align="center">
									<td>
										<? echo $i; ?>
									</td>
									<td>
										<input type="text" id="termscondition_<? echo $i; ?>"
										name="termscondition[]" style="width:95%" class="text_boxes"
										value="<? echo $row[csf('terms')]; ?>"/>
									</td>
									<td>
										<input type="button" id="increase_<? echo $i; ?>" style="width:30px"
										class="formbutton" value="+"
										onClick="add_break_down_tr_clone(<? echo $i; ?> )"/>
										<input type="button" id="decrease_<? echo $i; ?>" style="width:30px"
										class="formbutton" value="-"
										onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);"/>
									</td>
								</tr>
								<?
							}
						} else {
							$data_array = sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
							if (count($data_array) > 0) {
								foreach ($data_array as $row) {
									$i++;
									?>
									<tr id="settr_<? echo $i; ?>" align="center">
										<td>
											<? echo $i; ?>
										</td>
										<td>
											<input type="text" id="termscondition_<? echo $i; ?>"
											name="termscondition[]" style="width:95%" class="text_boxes"
											value="<? echo $row[csf('terms')]; ?>"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" style="width:30px"
											class="formbutton" value="+"
											onClick="add_break_down_tr_clone(<? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" style="width:30px"
											class="formbutton" value="-"
											onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );"/>
										</td>
									</tr>
									<?
								}
							}
						}
						?>
					</tbody>
				</table>

				<table width="650" cellspacing="0" class="" border="0">
					<tr>
						<td align="center" height="15" width="100%"></td>
					</tr>
					<tr>
						<td align="center" width="100%" class="button_container">
							<?
							echo load_submit_buttons($permission, "fnc_sales_order_terms_condition", 0, 0, "reset_form('termscondi_1','','','','')", 1);
							?>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "save_update_delete_sales_order_terms_condition")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			die;
		}
		$id = return_next_id("id", "wo_booking_terms_condition", 1);
		$field_array = "id,booking_no,terms";
		for ($i = 0; $i < sizeof($termscondition); $i++) {
			if ($data_array != "") $data_array .= ",";
			$data_array .= "(" . $id . ",'" . $txt_job_no . "','" . trim($termscondition[$i]) . "')";
			$id = $id + 1;
		}
		$rID_de3 = execute_query("delete from wo_booking_terms_condition where  booking_no ='" . $txt_job_no . "'", 0);

		$rID = sql_insert("wo_booking_terms_condition", $field_array, $data_array, 1);
		check_table_status($_SESSION['menu_id'], 0);
		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "0**" . $txt_job_no;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $txt_job_no;
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "0**" . $new_booking_no[0];
			} else {
				oci_rollback($con);
				echo "10**" . $new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == 'sales_script') {
	$con = connect();
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$data = explode("**", $data);
	$mst_id = $data[0];
	$booking_no = $data[1];

	$booking_arr = array();
	$colorRange_arr = array();

	$data_arr = array();
	$sql = "select a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.dia_width, c.body_part_id, c.color_type_id, c.width_dia_type,c.item_number_id, c.gsm_weight,b.pre_cost_remarks,c.lib_yarn_count_deter_id, b.fabric_color_id, c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty, sum(b.fin_fab_qnty*b.rate) as amnt from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and c.uom=12 group by a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.pre_cost_remarks,c.item_number_id order by c.body_part_id";
	$data_array = sql_select($sql);
	foreach ($data_array as $row) {
		$booking_arr[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('color_type_id')]][$row[csf('fabric_color_id')]]['pre_cost_fabric_cost_dtls_id'] = $row[csf('pre_cost_fabric_cost_dtls_id')] . "*" . $row[csf('item_number_id')];
	}

	$sql_sales = "select c.sales_booking_no,a.pre_cost_fabric_cost_dtls_id,a.id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.avg_rate, a.amount,a.grey_qty, a.work_scope, a.order_uom,a.pre_cost_fabric_cost_dtls_id,a.item_number_id, a.pre_cost_remarks, a.rmg_qty,a.process_loss from fabric_sales_order_dtls a left join fabric_sales_order_mst c on a.mst_id=c.id where a.status_active=1 and a.is_deleted=0 and a.pre_cost_fabric_cost_dtls_id is null group by c.sales_booking_no,a.pre_cost_fabric_cost_dtls_id,a.id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.avg_rate, a.amount, a.grey_qty, a.work_scope, a.order_uom, a.pre_cost_fabric_cost_dtls_id,a.item_number_id,a.pre_cost_remarks, a.rmg_qty,a.process_loss order by a.body_part_id";
	$sales_array = sql_select($sql_sales);

	foreach ($sales_array as $row) {
		if ($booking_arr[$row[csf('sales_booking_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_type_id')]][$row[csf('color_id')]]['pre_cost_fabric_cost_dtls_id'] != '') {
			$precost_row = ($booking_arr[$row[csf('sales_booking_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_type_id')]][$row[csf('color_id')]]['pre_cost_fabric_cost_dtls_id']);
			$precost = explode("*", $precost_row);
			$sales_dtls_id = $row[csf('id')];
			execute_query("update fabric_sales_order_dtls set PRE_COST_FABRIC_COST_DTLS_ID=$precost[0], item_number_id=$precost[1], UPDATED_BY=9999 where id=$sales_dtls_id", 0);
			oci_commit($con);
		}
	}

//	foreach ($data_array as $row) {
//		$index = $row[csf('body_part_id')] . "__" . $row[csf('lib_yarn_count_deter_id')] . "__" . $row[csf('color_type_id')] . "__" . $row[csf('width_dia_type')] . "__" . $row[csf('gsm_weight')] . "__" . $row[csf('dia_width')] . "__" . $row[csf('fabric_color_id')] . "__" . $row[csf('uom')] . "__" . $row[csf('pre_cost_remarks')] . "__" . $row[csf('item_number_id')] . "__" . $row[csf('pre_cost_fabric_cost_dtls_id')] . "__" . $row[csf('process_loss_percent')];
//		$data_arr[$index] = $row[csf('fqnty')] . "**" . $row[csf('amnt')] . "**" . $row[csf('pre_cost_remarks')] . "**" . $row[csf('qnty')] . "**" . $row[csf('rmg_qty')];
//	}

	disconnect($con);
	die;
}

if($action == "is_booking_duplicate"){
	$req_data = explode("*", $data);
	// check booking no when insert
	if($req_data[3] == 0){
		$check_data = return_field_value("sales_booking_no", "fabric_sales_order_mst", "company_id=$req_data[0] and buyer_id=$req_data[2] and sales_booking_no='$req_data[1]' and status_active=1 and is_deleted=0", "sales_booking_no");
		if($check_data){
			echo "invalid";
		}else{
			echo "valid";
		}
	}else{
		// check booking no when Update
		$check_data = return_field_value("sales_booking_no", "fabric_sales_order_mst", "company_id=$req_data[0] and buyer_id=$req_data[2] and sales_booking_no='$req_data[1]' and id!=$req_data[4] and status_active=1 and is_deleted=0", "sales_booking_no");
		if($check_data){
			echo "invalid";
		}else{
			echo "valid";
		}
	}
	exit();
}

if($action == "is_booking_used_in_plan"){
	$req_data = explode("*", $data);
	// check booking no when insert
	$check_data = return_field_value("booking_no", "PPL_PLANNING_ENTRY_PLAN_DTLS", "company_id=$req_data[0] and buyer_id=$req_data[2] and booking_no='$req_data[1]' and status_active=1 and is_deleted=0 and is_sales=1 and within_group=2", "booking_no");
	if($check_data){
		echo "invalid";
	}else{
		echo "valid";
	}
	exit();
}

if ($action == 'btn_load_change_bookings') {
	$sql = "select id, company_id, job_no, sales_booking_no,is_master_part_updated from fabric_sales_order_mst where status_active=1 and is_deleted=0 and within_group=1 and is_apply_last_update=2";
	$data_array = sql_select($sql);
	echo count($data_array);
	exit();
}
if ($action == "process_name_popup") 
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

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

		function set_all() {
			var old_seq = document.getElementById('txt_process_seq').value;
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						$('#txt_process_rate'+idSeq[0]).val(idSeq[2]);
						//$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_process_id').value;
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) 
     		{
     			/*if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}*/
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val()+'_'+$('#txt_process_rate'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val()+'_'+$('#txt_process_rate'+old[k]).val();}
     		}
     	}
     	$('#hidden_process_seq').val(seq);
			//var oldArr = old_seq.split(",");


			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
		<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
		value="">
		<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
				<thead>
					<th width="50">SL</th>
					<th>Process Name</th>
					<th>Rate</th>
					<th width="82">Sequence</th>
				</thead>
			</table>
			<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
			align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			$process_row_id = '';
			$not_process_id_print_array = array(2, 3, 4, 101, 120, 121, 122, 123, 124); //$mandatory_subprocess_array=array(33,63,65,94);
			$hidden_process_id = explode(",", $txt_process_id);
			foreach ($conversion_cost_head_array as $id => $name) 
			{
				if (!in_array($id, $not_process_id_print_array)) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					if (in_array($id, $hidden_process_id)) {
						if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
					}
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="50" align="center"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
							<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
						</td>
						<td>
							<p><? echo $name; ?></p>
						</td>
						<td width="65" align="center"><input type="text" id="txt_process_rate<? echo $id ?>" name="txt_process_rate<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						<td width="65" align="center" onclick="event.stopPropagation()">
							<input type="text" id="txt_sequence<? echo $id ?>" name="txt_sequence<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;">
						</td>
					</tr>
					<?
					$i++;
				}
			}
		?>
		<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
		<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="<?php echo $process_seq; ?>"/>
			</table>
		</div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if ($action == "process_name_popup_main") 
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

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

		function set_all() {
			var old_seq = document.getElementById('txt_process_seq').value;
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						$('#txt_process_rate'+idSeq[0]).val(idSeq[2]);
						//$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) 
		{


         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_process_id').value;
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) 
     		{
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val()+'_'+$('#txt_process_rate'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val()+'_'+$('#txt_process_rate'+old[k]).val();}
     		}
     	}
     	$('#hidden_process_seq').val(seq);

			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
		<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
		value="">
		<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
				<thead>
					<th width="50">SL</th>
					<th>Process Name</th>
					<th>Rate</th>
					<th width="82">Sequence</th>
				</thead>
			</table>
			<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
			align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			$process_row_id = '';
			$not_process_id_print_array = array(1,31,35,265); //$mandatory_subprocess_array=array(33,63,65,94);
			$hidden_process_id = explode(",", $txt_process_id);
			foreach ($conversion_cost_head_array as $id => $name) 
			{
				if (in_array($id, $not_process_id_print_array)) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					if (in_array($id, $hidden_process_id)) {
						if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
					}
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="50" align="center"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
							<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
						</td>
						<td>
							<p><? echo $name; ?></p>
						</td>
						<td width="65" align="center"><input type="text" id="txt_process_rate<? echo $id ?>" name="txt_process_rate<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						<td width="65" align="center" onclick="event.stopPropagation()">
							<input type="text" id="txt_sequence<? echo $id ?>" name="txt_sequence<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;">
						</td>
					</tr>
					<?
					$i++;
				}
			}
		?>
		<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
		<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="<?php echo $process_seq; ?>"/>
			</table>
		</div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if ($action == "collar_cuff_masurement_popup") 
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function window_close()
		{
			var ccmp_save_data ="";

			$("#tbl_list_search").find('tbody tr').each(function () 
			{
				var txtCollarSize = $(this).find('input[name="txtCollarSize[]"]').val();
	            var txtCollarQnty = $(this).find('input[name="txtCollarQnty[]"]').val();
	            var txtCuffSize = $(this).find('input[name="txtCuffSize[]"]').val();
	            var txtCuffQnty = $(this).find('input[name="txtCuffQnty[]"]').val();

	            var txtCollarGmtSize = $(this).find('input[name="txtCollarGmtSize[]"]').val();
	            var txtCollarExP = $(this).find('input[name="txtCollarExP[]"]').val();
	            var txtCuffGmtSize = $(this).find('input[name="txtCuffGmtSize[]"]').val();
	            var txtCuffExP = $(this).find('input[name="txtCuffExP[]"]').val();

	            if((txtCollarSize !="" && txtCollarQnty !="") || (txtCuffSize !="" && txtCuffQnty !=""))
	            {
	            	if(txtCollarSize =="" || txtCollarQnty ==""){
	            		txtCollarGmtSize ="";
	            		txtCollarExP="";
	            		txtCollarSize="";
	            		txtCollarQnty="";
	            	}
	            	if(txtCuffSize =="" || txtCuffQnty ==""){
	            		txtCuffGmtSize ="";
	            		txtCuffExP="";
	            		txtCuffSize="";
	            		txtCuffQnty="";
	            	}


	            	if(ccmp_save_data=="")
	            	{
	            		ccmp_save_data = txtCollarSize + "_" + txtCollarQnty + "_" + txtCuffSize + "_" + txtCuffQnty + "_" + txtCollarGmtSize + "_" + txtCollarExP + "_" + txtCuffGmtSize + "_" + txtCuffExP;
	            	}
	            	else
	            	{
	            		ccmp_save_data += "," + txtCollarSize + "_" + txtCollarQnty + "_" + txtCuffSize + "_" + txtCuffQnty + "_" + txtCollarGmtSize + "_" + txtCollarExP + "_" + txtCuffGmtSize + "_" + txtCuffExP;
	            	}
	            }

			});
			

			$('#hidden_ccmp_save_data').val(ccmp_save_data);
			//alert(ccmp_save_data);
			//return;
			parent.emailwindow.hide();
		}

		function add_break_down_tr(i) 
		{
			var lastTrId = $('#tbl_list_search tbody tr:last').attr('id').split('_');
	    		var row_num = lastTrId[1];
	    		if (row_num != i) {
	    			return false;
	    		}
	    		else
	    		{
	    			i++;
	    			$("#tbl_list_search tbody tr:last").clone().find("input,select").each(function () {
	    				$(this).attr({
	    					'id': function (_, id) {var id = id.split("_");return id[0] + "_" + i},
	    					'name': function (_, name) {return name},
	    					'value': function (_, value) {return value}
	    				});
    				}).end().appendTo("#tbl_list_search");
				
    				$('#txtCollarQnty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_collar(" + i + ");");
    				$('#txtCollarExP_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_collar(" + i + ");");
    				$('#txtCuffQnty_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_cuff(" + i + ");");
    				$('#txtCuffExP_' + i).removeAttr("onKeyUp").attr("onKeyUp", "calculate_cuff(" + i + ");");
	    		}

		}

		 function fn_deleteRow(rowNo) 
		 {
    		var numRow = $('#tbl_list_search tbody tr').length;
    		if (numRow != 1) {
    			$('#search_' + rowNo).remove();
    		}
    		else {
    			return false;
    		}
	    	
	    }

	    function calculate_collar(i)
	    {
	    	var txtCollarQnty = $('#txtCollarQnty_' + i).val() * 1;
 			var txtCollarExP = $('#txtCollarExP_' + i).val() * 1;

 			var collarTotal =txtCollarQnty + ((txtCollarQnty * txtCollarExP)/100);
 			collarTotal = Math.round(collarTotal);
 			$('#txtCollarTotal_' + i).val(collarTotal);

 			var doubleCollarQnty = txtCollarQnty * 2;
 			var txtCuffExP = $('#txtCuffExP_' + i).val() * 1;
 			var cuffTotal =doubleCollarQnty + ((doubleCollarQnty * txtCuffExP)/100);
 			cuffTotal = Math.round(cuffTotal);
 			$('#txtCuffQnty_' + i).val(doubleCollarQnty);
 			$('#txtCuffTotal_' + i).val(cuffTotal);
 			calculate_total();
	    	
	    }

	    function calculate_cuff(i)
	    {
 			var txtCuffQnty = $('#txtCuffQnty_' + i).val() * 1;
 			var txtCuffExP = $('#txtCuffExP_' + i).val() * 1;

 			var totCuffQnty =txtCuffQnty + ((txtCuffQnty * txtCuffExP)/100);
 			totCuffQnty = Math.round(totCuffQnty);
 			$('#txtCuffTotal_' + i).val(totCuffQnty);
 			calculate_total();
	    }

	    function calculate_total()
	    {
	    	var total_collar=0;
	    	var total_cuff=0;
	    	var total_collar_with_perc=0;
	    	var total_cuff_with_perc=0;
	    	$("#tbl_list_search").find('tbody tr').each(function () {
		 		var txtCollarQnty = $(this).find('input[name="txtCollarQnty[]"]').val()*1;
		 		var txtCollarTotal = $(this).find('input[name="txtCollarTotal[]"]').val()*1;
		 		var txtCuffQnty = $(this).find('input[name="txtCuffQnty[]"]').val()*1;
		 		var txtCuffTotal = $(this).find('input[name="txtCuffTotal[]"]').val()*1;

		 		total_collar += Math.round(txtCollarQnty);
		 		total_collar_with_perc += Math.round(txtCollarTotal);
		 		total_cuff += Math.round(txtCuffQnty);
		 		total_cuff_with_perc += Math.round(txtCuffTotal);
		 	});


			$("#total_collar_qnty").text(total_collar);
			$("#total_collar_qnty_with_perc").text(total_collar_with_perc);
			$("#total_cuff_qnty").text(total_cuff);
			$("#total_cuff_qnty_with_perc").text(total_cuff_with_perc);

			$("#grand_collar_qnty").val(total_collar_with_perc);
			$("#hdn_total_collar_qnty_without_ex_percent").val(total_collar);

			var txt_collercuff_con = $("#txt_collercuff_con").val()*1;
			var grand_cons =(txt_collercuff_con/12)*total_collar_with_perc;
		 	grand_cons = Math.round(grand_cons);
		 	$("#grand_cons").val(grand_cons);
	    }


	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:490px;margin-left:10px">
		<input type="hidden" name="hidden_ccmp_save_data" id="hidden_ccmp_save_data" class="text_boxes" value="">
		<input type="hidden" name="hidden_ccmp_save_data" id="hidden_ccmp_save_data" class="text_boxes" value="">
		<input type="hidden" name="hidden_ccmp_save_data" id="hidden_ccmp_save_data" class="text_boxes" value="">
		<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" align="left">
				<thead>
					<tr>
						<th width="50" rowspan="2">SL</th>
						<th colspan="5">Collar</th>
						<th colspan="5">Cuff</th>
						<th width="65" rowspan="2">&nbsp;</th>
					</tr>
					<tr>
						<th width="65">Gmt. Size</th>
						<th width="65">Item Size</th>
						<th width="65">Qnty(Pcs)</th>
						<th width="65">Ex. %</th>
						<th width="65">Total Qty.</th>
						<th width="65">Gmt. Size</th>
						<th width="65">Size</th>
						<th width="65">Qnty(Pcs)</th>
						<th width="65">Ex. %</th>
						<th width="65">Total Qty.</th>
					</tr>
				</thead>
			</table>
			<div style="width:850px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
			align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table"
			id="tbl_list_search">
				<tbody>
					<?
					$i = 1;
					$ccmp_save_data_arr = array_filter(explode(",", $ccmp_save_data));
					//echo count($ccmp_save_data_arr);die;
					foreach ($ccmp_save_data_arr as $row) 
					{
						$coller_cuff = explode("_", $row);
						$coller_size = $coller_cuff[0];
						$coller_qnty = $coller_cuff[1];
						$cuff_size = $coller_cuff[2];
						$cuff_qnty = $coller_cuff[3];
						$coller_gmt_size = $coller_cuff[4];
						$coller_exp = $coller_cuff[5];
						$cuff_gmt_size = $coller_cuff[6];
						$cuff_exp = $coller_cuff[7];

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							id="search_<? echo $i; ?>" >
							<td width="50" align="center" id="sl_<? echo $i;?>">
								<?php echo "$i"; ?>
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCollarGmtSize_<? echo $i ?>" name="txtCollarGmtSize[]" value="<? echo $coller_gmt_size;?>" class="text_boxes" style=" width:50px;">
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCollarSize_<? echo $i ?>" name="txtCollarSize[]" value="<? echo $coller_size;?>" class="text_boxes" style=" width:50px;">
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCollarQnty_<? echo $i ?>" name="txtCollarQnty[]" value="<? echo $coller_qnty;?>" onKeyUp="calculate_collar(<? echo $i;?>);" class="text_boxes_numeric" style=" width:50px;">
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCollarExP_<? echo $i ?>" name="txtCollarExP[]" value="<? echo $coller_exp;?>" class="text_boxes_numeric" onKeyUp="calculate_collar(<? echo $i;?>);" style=" width:50px;">
							</td>
							<td width="65" align="center">
							<? 
								$coller_tot = ($coller_qnty + ($coller_qnty*$coller_exp)/100);
								$coller_tot = round($coller_tot);
							?>
								<input type="text" id="txtCollarTotal_<? echo $i ?>" name="txtCollarTotal[]" value="<? echo $coller_tot;?>" class="text_boxes_numeric" style=" width:50px;" readonly>
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCuffGmtSize_<? echo $i ?>" name="txtCuffGmtSize[]" value="<? echo $cuff_gmt_size;?>" class="text_boxes" style=" width:50px;">
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCuffSize_<? echo $i ?>" name="txtCuffSize[]" value="<? echo $cuff_size;?>" class="text_boxes" style=" width:50px;">
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCuffQnty_<? echo $i ?>" name="txtCuffQnty[]" value="<? echo $cuff_qnty;?>" class="text_boxes_numeric" onKeyUp="calculate_cuff(<? echo $i;?>)" style=" width:50px;">
							</td>
							<td width="65" align="center">
								<input type="text" id="txtCuffExP_<? echo $i ?>" name="txtCuffExP[]" value="<? echo $cuff_exp;?>" class="text_boxes_numeric" onKeyUp="calculate_cuff(<? echo $i;?>)" style=" width:50px;">
							</td>
							<td width="65" align="center">
							<? 
								$cuff_tot = ($cuff_qnty + ($cuff_qnty*$cuff_exp)/100);
								$cuff_tot = round($cuff_tot);
							?>
								<input type="text" id="txtCuffTotal_<? echo $i ?>" name="txtCuffTotal[]" value="<? echo $cuff_tot;?>" class="text_boxes_numeric" style=" width:50px;" readonly>
							</td>
							<td width="65" align="center">
								<input type="button" id="increase_<? echo $i ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i ?>)"/>
								<input type="button" id="decrease_<? echo $i ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i ?>);"/>
							</td>
						</tr>
						<?
						$i++;
					}

					if(count($ccmp_save_data_arr)<10)
					{
						$remain_row = 10-count($ccmp_save_data_arr);

						for($x=0; $x<$remain_row; $x++)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
								id="search_<? echo $i; ?>">
								<td width="50" align="center" id="sl_<? echo $i;?>">
								<?php echo "$i"; ?>
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCollarGmtSize_<? echo $i ?>" name="txtCollarGmtSize[]" value="" class="text_boxes" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCollarSize_<? echo $i ?>" name="txtCollarSize[]" value="" class="text_boxes" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCollarQnty_<? echo $i ?>" name="txtCollarQnty[]" value="" class="text_boxes_numeric" onKeyUp="calculate_collar(<? echo $i;?>);" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCollarExP_<? echo $i ?>" name="txtCollarExP[]" value="5" class="text_boxes_numeric" onKeyUp="calculate_collar(<? echo $i;?>);" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCollarTotal_<? echo $i ?>" name="txtCollarTotal[]" value="" class="text_boxes_numeric" style=" width:50px;" readonly>
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCuffGmtSize_<? echo $i ?>" name="txtCuffGmtSize[]" value="" class="text_boxes" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCuffSize_<? echo $i ?>" name="txtCuffSize[]" value="" class="text_boxes" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCuffQnty_<? echo $i ?>" name="txtCuffQnty[]" value="" class="text_boxes_numeric" onKeyUp="calculate_cuff(<? echo $i;?>)" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCuffExP_<? echo $i ?>" name="txtCuffExP[]" value="5" class="text_boxes_numeric" onKeyUp="calculate_cuff(<? echo $i;?>)" style=" width:50px;">
								</td>
								<td width="65" align="center">
									<input type="text" id="txtCuffTotal_<? echo $i ?>" name="txtCuffTotal[]" value="" class="text_boxes_numeric" style=" width:50px;" readonly>
								</td>
								<td width="65" align="center">
									<input type="button" id="increase_<? echo $i ?>" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i ?>)"/>
									<input type="button" id="decrease_<? echo $i ?>" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i ?>);"/>
								</td>
							</tr>
							<?
							$i++;
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" align="left">
			<tfoot>
				<tr>
					<th width="50">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="65">Total Qty.</th>
					<th width="65" id="total_collar_qnty">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="65" id="total_collar_qnty_with_perc">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="65" id="total_cuff_qnty">&nbsp;</th>
					<th width="65">&nbsp;</th>
					<th width="65" id="total_cuff_qnty_with_perc">&nbsp;</th>
					<th width="65">&nbsp;</th>
				</tr>
				<tr>
					<th width="" colspan="12">&nbsp;</th>
				</tr>
				<tr>
					<th width="" colspan="6">&nbsp;</th>
					<th colspan="2">Total Collar & Cuff Qty.</th>
					<th><input type="text" id="grand_collar_qnty" name="grand_collar_qnty" class="text_boxes_numeric" onKeyUp="" width="40" disabled readonly></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<th colspan="6">&nbsp;</th>
					<th colspan="2">Collar & Cuff Cons/ Dzn</th>
					<th>
						<input type="text" id="txt_collercuff_con" name="txt_collercuff_con" class="text_boxes_numeric" onKeyUp="calculate_total();" width="40" value="<? echo $hdnCollerCuffCon;?>">
						<input type="hidden" id="hdn_total_collar_qnty_without_ex_percent" name="hdn_total_collar_qnty_without_ex_percent" class="text_boxes_numeric" onKeyUp="calculate_total();" width="40" value="<? echo $hdnCollerCuffCon;?>"> 
					</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
				<tr>
					<th colspan="6">&nbsp;</th>
					<th colspan="2">Total Cons</th>
					<th><input type="text" id="grand_cons" name="grand_cons" class="text_boxes_numeric" width="40" disabled readonly></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
			</tfoot>
		</table>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script type="text/javascript">
	//alert();
	calculate_total();

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action == "is_booking_revised")
{
	$check_data = sql_select("select is_apply_last_update from fabric_sales_order_mst where job_no='$data' and status_active=1 and is_deleted=0");
	if($check_data[0][csf("is_apply_last_update")] == 2)
	{
		echo "invalid";
	}else{
		echo "valid";
	}
	exit();
}

if ($action == "fabric_sales_order_print6") {

	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$departmentArr = return_library_array("select id,department_name from lib_department", 'id', 'department_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$item_library = return_library_array("select id,item_name from lib_garment_item", "id", "item_name");
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');
	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');

	//$update_serial = sql_select("select a.id, a.update_sl from fabric_sales_order_mst a where a.job_no = '" . $salesOrderNo . "'");

	$sql = "select d.id as sales_id, d.currency_id, d.delivery_start_date, d.delivery_date, d.within_group, d.job_no, d.buyer_id, d.booking_date, d.booking_approval_date, d.sales_booking_no, d.company_id, d.delivery_date, d.dealing_marchant, d.remarks, d.style_ref_no, d.season, d.attention, d.customer_buyer, d.customer_buyer_brand, d.season_year, d.ship_to, d.order_nature, d.from_fso_no, d.update_sl from fabric_sales_order_mst d
	where d.status_active =1 and d.is_deleted =0 and d.job_no='$salesOrderNo' and entry_form=472";

	//d.customer_buyer, d.customer_buyer_brand, d.season_year, d.ship_to, d.order_nature, from_fso_no, from_fso_id


	if ($sales_id == '') {
		$mst_data = sql_select($sql);
		//print_r($mst_data);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		//print_r($mst_data);
		extract($mst_data);
	}

	$brand_arr = return_library_array("select id, brand_name from lib_buyer_brand where buyer_id=$customer_buyer", "id", "brand_name");
	$image_locationArr2 = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry_v2' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');

	$max_shipment_date . 'wew';
	$update_serial_yarn = return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id='" . $sales_id . "'");
	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0"
	style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
		<tr>
			<td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="5" align="center"><strong><? echo $addressArr[$companyId]; ?></strong></td>
		</tr>
		<tr>
			<td colspan="5" align="center"><strong><? echo $formCaption; ?></strong></td>
		</tr>
	</table>

<div align="right" style="color: red;font: 20px tahoma;"><b><? echo ($order_nature) ? $fbooking_order_nature[$order_nature]." Order" : "";?></b></div>
<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
	<tr>
	</tr>
	<tr>
		<td><strong>Customer Name</strong></td>
		<td colspan="3"><? echo $buyerArr[$buyer_id]; ?></td>
		<td><strong>Ship to</strong></td>
		<td colspan="2"><? echo $ship_to;?></td>
	</tr>
	<tr>
		<td colspan="7" rowspan="" align="center" ><b><h3>Grey Booking</h3></b></td>
	</tr>
	<tr>
		<td width="135"><strong>Cust. Buyer Name</strong></td>
		<td><? echo $buyerArr[$customer_buyer]; ?></td>
		<td width="135"><strong>Brand Name</strong></td>
		<td><? echo $brand_arr[$customer_buyer_brand]; ?></td>
		<td width="135"><strong>Style Ref.</strong></td>
		<td><? echo $style_ref_no; ?></td>
		<td><strong>Sales Job/Booking No: <? echo $bookingNo; ?></strong></td>
	</tr>
	<tr>
		<td><strong>Sales Order No.</strong></td>
		<td><? echo $job_no; ?></td>
		<td><strong>Season</strong></td>
		<td><? echo $season; ?></td>
		<td><strong>Season Year</strong></td>
		<td><? echo $season_year; ?></td>
		<td rowspan="6" valign="top" width="205"><? foreach ($image_locationArr2 as $path) { ?><img
			src="../../<? echo $path; ?>" height="100" width="100"><? } ?></td>
	</tr>
	<tr>
		<td><strong>Booking Date</strong></td>
		<td><? echo change_date_format($booking_date); ?></td>
		<td><strong>Attention</strong></td>
		<td><? echo $attention;; ?></td>
		<td><strong>Dealing Marchant</strong></td>
		<td><? echo $dealing_marArr[$dealing_marchant];; ?></td>
	</tr>
	<tr>
		<td><strong>Received Date</strong></td>
		<td><? echo change_date_format($booking_approval_date); ?></td>
		<td><strong>From FSO Ref.</strong></td>
		<td><? echo $from_fso_no; ?></td>
		<td><strong>Currency</strong></td>
		<td><? echo $currency[$currency_id]; ?></td>
	</tr>
	<tr>
		<td><strong>Delivery Start Date</strong></td>
		<td><? echo change_date_format($delivery_start_date); ?></td>
		<td><strong>Delivery End Date</strong></td>
		<td><? echo change_date_format($delivery_date); ?></td>
		<td><strong>Lead Time</strong></td>
		<td>
			<?
				echo datediff("d", $booking_date, $delivery_date)
			?>
		</td>
	</tr>
	<tr>
		<td><strong>Remarks</strong></td>
		<td colspan="3"><? echo $remarks; ?></td>
		<td><strong>Revised No</strong></td>
		<td><?
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from approval_history b where mst_id='" . $sales_id . "' and b.entry_form=24 and un_approved_date is not null");
		echo $nameArray_approved[0][csf('approved_no')]; ?></td>
	</tr>
	<tr>
		<td><strong>&nbsp;</strong></td>
		<td colspan="3">&nbsp;</td>
		<td><strong>Update Serial</strong></td>
		<td>
			Fabric: <? echo ($update_sl == "") ? 0 : $update_sl; ?>
			,
			Yarn: <? echo ($update_serial_yarn == "") ? 0 : $update_serial_yarn; ?>
		</td>
	</tr>
	</table>
	<br/>
	<?

	$collor_cuff_sql= sql_select("select coller_size, coller_qnty, cuff_size, cuff_qnty, coller_gmt_size, coller_ex_percent, cuff_gmt_size, cuff_ex_percent from fabric_sales_order_coller_cuff where mst_id= $sales_id and status_active=1 and is_deleted=0");


	$dtls_sql = "select b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, sum(b.pp_qnty) as pp_qnty, sum(b.mtl_qnty) as mtl_qnty, sum(b.fpt_qnty) as fpt_qnty, sum(b.gpt_qnty) as gpt_qnty, b.process_loss, b.work_scope, sum(b.grey_qty) grey_qty,sum(b.rmg_qty) as rmg_qty, b.pre_cost_fabric_cost_dtls_id,a.within_group,b.process_id, b.ply, b.lab_dip_no
	from fabric_sales_order_mst a,fabric_sales_order_dtls b
	where a.id=$sales_id and a.job_no ='$salesOrderNo' and a.id=b.mst_id and a.status_active=1 and b.is_deleted=0
	group by b.body_part_id,b.color_type_id,b.fabric_desc,b.work_scope, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.color_id,b.pre_cost_remarks, b.pre_cost_fabric_cost_dtls_id,a.within_group,b.process_id,b.ply, b.lab_dip_no order by b.body_part_id";

	$dtls_sql_data = sql_select($dtls_sql);
	$process_count=0;
	foreach ($dtls_sql_data as $rows) {
		$key = $rows[csf('body_part_id')] . '_' . $rows[csf('color_type_id')] . '_' . $rows[csf('fabric_desc')] . '_' . $rows[csf('gsm_weight')] . '_' . $rows[csf('dia')] . '_' . $rows[csf('pre_cost_remarks')]. '_' . $rows[csf('ply')];

		$colorArr[$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]] = $rows[csf('color_id')];
		$finish_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]] += $rows[csf('finish_qty')];

		$sample_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]]["pp_qnty"] += $rows[csf('pp_qnty')];
		$sample_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]]["mtl_qnty"] += $rows[csf('mtl_qnty')];
		$sample_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]]["fpt_qnty"] += $rows[csf('fpt_qnty')];
		$sample_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]]["gpt_qnty"] += $rows[csf('gpt_qnty')];
		$sample_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]]["total"] += $rows[csf('finish_qty')] + $rows[csf('pp_qnty')]+ $rows[csf('mtl_qnty')]+ $rows[csf('fpt_qnty')]+ $rows[csf('gpt_qnty')];

		$grey_qty_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]] += $rows[csf('grey_qty')];
		$process_loss_arr[$key][$rows[csf('color_id')]][$rows[csf('lab_dip_no')]][$rows[csf('process_loss')]] += $rows[csf('process_loss')];

		$tot_finish_qty_arr += $rows[csf('finish_qty')];
		$tot_grey_qty_arr += $rows[csf('grey_qty')];

		list($construction, $compositions) = explode(',', $rows[csf('fabric_desc')]);
		$body_part_data[$key] = $rows[csf('body_part_id')];
		$color_type_id_arr[$key] = $rows[csf('color_type_id')];
		$constructions_arr[$key] = $construction;
		$compositions_arr[$key] = $compositions;

		$gsm_weight_arr[$key] = $rows[csf('gsm_weight')];
		$ply_arr[$key] = $rows[csf('ply')];
		$dia_arr[$key] = $rows[csf('dia')] . ',' . $fabric_typee[$rows[csf('width_dia_type')]];
		$work_scope_arr[$key] = $rows[csf('work_scope')];
		$remarks_arr[$key] = $rows[csf('pre_cost_remarks')];
		$rmg_qty_arr[$key] += $rows[csf('rmg_qty')];
		$fin_qty_arr[$key] += $rows[csf('finish_qty')];


		if($rows[csf('process_id')]!=""){$process_count+=1;}

		$process_name = '';
		$process_id_array = explode(",", $rows[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
		}
		$process_name_arr[$key] = $process_name;

	}

	$costing_per = "";
	$costing_per_qnty = 0;
	$costing_per_id = return_field_value("costing_per", "wo_pre_cost_mst", "job_no ='$wo_job_no'");
	if ($costing_per_id == 1) {
		$costing_per = "1 Dzn";
		$costing_per_qnty = 12;
	} elseif ($costing_per_id == 2) {
		$costing_per = "1 Pcs";
		$costing_per_qnty = 1;
	} elseif ($costing_per_id == 3) {
		$costing_per = "2 Dzn";
		$costing_per_qnty = 24;
	} elseif ($costing_per_id == 4) {
		$costing_per = "3 Dzn";
		$costing_per_qnty = 36;
	} elseif ($costing_per_id == 5) {
		$costing_per = "4 Dzn";
		$costing_per_qnty = 48;
	}


	$gmt_color_data = sql_select("select gmts_color_id,contrast_color_id FROM wo_pre_cos_fab_co_color_dtls WHERE job_no ='$wo_job_no'");
	foreach ($gmt_color_data as $gmt_color_row) {
		$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]] = $color_library[$gmt_color_row[csf("gmts_color_id")]];
	}

	?>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td colspan="2"><strong>Body Part</strong></td>
			<? foreach ($body_part_data as $val) {
				echo '<td colspan="8" align="center">' . $body_part[$val] . '</td>';
			} ?>
			<td rowspan="9"><strong>Total Finish</strong></td>
			<td rowspan="9"><strong>Total Grey</strong></td>
		</tr>
		<tr>
			<td colspan="2"><strong>Color Type</strong></td>
			<? foreach ($color_type_id_arr as $val) {
				echo '<td colspan="8" align="center">' . $color_type[$val] . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="2"><strong>Fabric Construction </strong></td>
			<? foreach ($constructions_arr as $val) {
				echo '<td colspan="8" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="2"><strong>Yarn Composition</strong></td>
			<? foreach ($compositions_arr as $val) {
				echo '<td colspan="8" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="2"><strong>GSM </strong></td>
			<? foreach ($gsm_weight_arr as $val) {
				echo '<td colspan="8" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="2"><strong>Dia/Width (Inch)</strong></td>
			<? foreach ($dia_arr as $val) {
				echo '<td colspan="8" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr>
			<td colspan="2"><strong>Ply</strong></td>
			<? 
				foreach ($ply_arr as $val) {
					echo '<td colspan="8" align="center">' . $val . '</td>';
				}
			?>
		</tr>
		<tr>
			<td colspan="2"><strong>Work Scope</strong></td>
			<? foreach ($work_scope_arr as $val) {
				echo '<td colspan="8" align="center">' . $item_category[$val] . '</td>';
			} ?>
		</tr>
		<?
		if($process_count>0){
			?>
			<tr>
				<td colspan="2"><strong>Process</strong></td>
				<? foreach ($process_name_arr as $val) {
					echo '<td colspan="3" align="center">' . $val . '</td>';
				} ?>
			</tr>

			<?
		}
		?>

		<tr>
			<td colspan="2"><strong>Remarks</strong></td>
			<? foreach ($remarks_arr as $val) {
				echo '<td colspan="8" align="center">' . $val . '</td>';
			} ?>
		</tr>
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Fabric Color</strong></td>
			<td align="center"><strong>Lab Dip No</strong></td>
			<?
			foreach ($body_part_data as $val) {
				echo '<td align="center"><strong>Finish Fab. Qty</strong></td>';
				echo '<td align="center"><strong>PP</strong></td>';
				echo '<td align="center"><strong>MTL</strong></td>';
				echo '<td align="center"><strong>FPT</strong></td>';
				echo '<td align="center"><strong>GPT</strong></td>';
				echo '<td align="center"><strong>Total Finish Fab. Qty</strong></td>';
				echo '<td align="center"><strong>Pro. Loss %</strong></td>';
				echo '<td align="center"><strong>Grey Fab. Qty</strong></td>';
			}
			?>
		</tr>
		<? 
		//echo "<pre>";
		//print_r($finish_qty_arr);die;

		foreach ($colorArr as $colorValue=>$colorValData) 
		{
			foreach ($colorValData as $lab_dip_no=>$lab_dip_noData) 
			{
				foreach ($lab_dip_noData as $process_loss_per=>$colorVal) 
				{
					$i++;
					$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $color_library[$colorVal]; ?></td>
						<td align="center"><? echo $lab_dip_no; ?></td>
						<? foreach ($body_part_data as $key_id => $val) 
						{
							//$lab_dip_no = "'".$lab_dip_no."'";
							$color_wise_finish_tot_qty[$colorVal][$lab_dip_no][$process_loss_per] += $finish_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per];
							

							$color_wise_finish_tot_qty[$colorVal][$lab_dip_no][$process_loss_per] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["pp_qnty"];
							$color_wise_finish_tot_qty[$colorVal][$lab_dip_no][$process_loss_per] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["mtl_qnty"];
							$color_wise_finish_tot_qty[$colorVal][$lab_dip_no][$process_loss_per] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["fpt_qnty"];
							$color_wise_finish_tot_qty[$colorVal][$lab_dip_no][$process_loss_per] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["gpt_qnty"];

							$color_wise_grey_tot_qty[$colorVal][$lab_dip_no][$process_loss_per] += $grey_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per];

							$all_color_finish_tot_qty[$key_id] += $finish_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per];
							$all_color_pp_tot_qty[$key_id] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["pp_qnty"];
							$all_color_mtl_tot_qty[$key_id] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["mtl_qnty"];
							$all_color_fpt_tot_qty[$key_id] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["fpt_qnty"];
							$all_color_gpt_tot_qty[$key_id] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["gpt_qnty"];
							$all_color_total_tot_qty[$key_id] += $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["total"];
							$all_color_grey_tot_qty[$key_id] += $grey_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per];

							//$process_loss_per = (($grey_qty_arr[$key_id][$colorVal][$lab_dip_no] - $finish_qty_arr[$key_id][$colorVal][$lab_dip_no])/$finish_qty_arr[$key_id][$colorVal][$lab_dip_no])*100;

							if (is_null($finish_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]) || $finish_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($finish_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per], 2) . '</td>';
							}

							if (is_null($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["pp_qnty"]) || $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["pp_qnty"] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["pp_qnty"], 2) . '</td>';
							}
							if (is_null($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["mtl_qnty"]) || $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["mtl_qnty"] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["mtl_qnty"], 2) . '</td>';
							}
							if (is_null($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["fpt_qnty"]) || $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["fpt_qnty"] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["fpt_qnty"], 2) . '</td>';
							}
							if (is_null($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["gpt_qnty"]) || $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["gpt_qnty"] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["gpt_qnty"], 2) . '</td>';
							}
							if (is_null($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["total"]) || $sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["total"] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($sample_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]["total"], 2) . '</td>';
							}

							//echo '<td align="right">' . number_format($process_loss_per, 2) . '</td>';

							if (is_null($process_loss_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]) || $process_loss_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($process_loss_per, 2) . '</td>';
							}
							if (is_null($grey_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per]) || $grey_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per] == 0) {
								echo '<td align="right"></td>';
							} else {
								echo '<td align="right">' . number_format($grey_qty_arr[$key_id][$colorVal][$lab_dip_no][$process_loss_per], 2) . '</td>';
							}
						}
						?>
						<td align="right"><strong><? echo number_format($color_wise_finish_tot_qty[$colorVal][$lab_dip_no][$process_loss_per], 2); ?></strong>
						</td>
						<td align="right"><strong><? echo number_format($color_wise_grey_tot_qty[$colorVal][$lab_dip_no][$process_loss_per], 2); ?></strong>
						</td>
					</tr>
					<?
				}
			}
		}
		?>

		<tr bgcolor="#EEEEEE">
			<td align="center"></td>
			<td align="center">Total</td>
			<? foreach ($body_part_data as $key_id => $val) {
				$grand_all_color_finish_tot_qty += $all_color_finish_tot_qty[$key_id];
				$grand_all_color_grey_tot_qty += $all_color_grey_tot_qty[$key_id];

				$grand_all_color_finish_tot_qty += $all_color_pp_tot_qty[$key_id];
				$grand_all_color_finish_tot_qty += $all_color_mtl_tot_qty[$key_id];
				$grand_all_color_finish_tot_qty += $all_color_fpt_tot_qty[$key_id];
				$grand_all_color_finish_tot_qty += $all_color_gpt_tot_qty[$key_id];
				//$grand_all_color_total_tot_qty += $all_color_total_tot_qty[$key_id];


				echo '<td align="right">' . number_format($all_color_finish_tot_qty[$key_id], 2) . '</td>';

				echo '<td align="right">' . number_format($all_color_pp_tot_qty[$key_id], 2) . '</td>';
				echo '<td align="right">' . number_format($all_color_mtl_tot_qty[$key_id], 2) . '</td>';
				echo '<td align="right">' . number_format($all_color_fpt_tot_qty[$key_id], 2) . '</td>';
				echo '<td align="right">' . number_format($all_color_gpt_tot_qty[$key_id], 2) . '</td>';
				echo '<td align="right">' . number_format($all_color_total_tot_qty[$key_id], 2) . '</td>';
				echo '<td></td>';
				echo '<td align="right">' . number_format($all_color_grey_tot_qty[$key_id], 2) . '</td>';
			}
			?>
			<td align="right"><strong><? echo number_format($grand_all_color_finish_tot_qty, 2); ?></strong></td>
			<td align="right"><strong><? echo number_format($grand_all_color_grey_tot_qty, 2); ?></strong></td>
		</tr>
	</table>
	<br>
	<?
	$yarn_dtls_sql = "select yarn_count_id,color_id,composition_id,composition_perc,yarn_type,sum(cons_qty) as cons_qty,brand_id, supplier_id from fabric_sales_order_yarn_dtls where mst_id=$sales_id and status_active=1 and is_deleted=0 group by yarn_count_id,color_id,composition_id,brand_id,composition_perc,yarn_type,supplier_id";
	$yarn_dtls_sql_data = sql_select($yarn_dtls_sql);
	?>
	<strong style="font: bold 12px tahoma;">Yarn Required Summary</strong>
	<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Sl No</strong></td>
			<td align="center"><strong>Count</strong></td>
			<td align="center"><strong>Composition</strong></td>
			<td align="center"><strong>Color</strong></td>
			<td align="center"><strong>Type</strong></td>
			<td align="center"><strong>Req. Qty.</strong></td>
			<td align="center"><strong>Supplier</strong></td>
			<td align="center"><strong>Brand</strong></td>
		</tr>
		<?
		$i = 1;
		foreach ($yarn_dtls_sql_data as $rows) {
			?>
			<tr>
				<td align="center"><? echo $i; ?></td>
				<td><? echo $yarnCount_arr[$rows[csf(yarn_count_id)]]; ?></td>
				<td><? echo $composition[$rows[csf(composition_id)]]; ?></td>
				<td><p><? echo $color_library[$rows[csf(color_id)]]; ?></p></td>
				<td><p><? echo $yarn_type[$rows[csf(yarn_type)]]; ?></p></td>
				<td align="right"><p><? echo $rows[csf(cons_qty)];
				$tot_cons_qty += $rows[csf(cons_qty)]; ?></p></td>
				<td><p><? echo $supplier_arr[$rows[csf(supplier_id)]]; ?></p></td>
				<td><p><? echo $brand_arr[$rows[csf(brand_id)]]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<tr>
			<td align="center"><strong></strong></td>
			<td align="center"><strong></strong></td>
			<td align="center"><strong>Total Grey</strong></td>
			<td align="center"><strong></strong></td>
			<td align="center"><strong></strong></td>
			<td align="right"><strong><? echo number_format($tot_cons_qty, 2); ?></strong></td>
			<td align="center"><strong></strong></td>
			<td align="center"><strong></strong></td>
		</tr>
	</table>
	<br>
	<?
	?>

	<strong style="font: bold 12px tahoma;">TNA Information</strong>
	<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
		<tr bgcolor="#CCCCCC">
			<td align="center" colspan="2"><strong>Yarn Receive</strong></td>
			<td align="center" colspan="2"><strong>Knitting</strong></td>
			<td align="center" colspan="2"><strong>Dyeing</strong></td>
			<td align="center" colspan="2"><strong>Finishing Fabric</strong></td>
			<td align="center" colspan="2"><strong>Finishing Fabric Delivery</strong></td>
		</tr>
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Start Date</strong></td>
			<td align="center"><strong>End Date</strong></td>
			<td align="center"><strong>Start Date</strong></td>
			<td align="center"><strong>End Date</strong></td>
			<td align="center"><strong>Start Date</strong></td>
			<td align="center"><strong>End Date</strong></td>
			<td align="center"><strong>Start Date</strong></td>
			<td align="center"><strong>End Date</strong></td>
			<td align="center"><strong>Start Date</strong></td>
			<td align="center"><strong>End Date</strong></td>
		</tr>
		<tr>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
			<td align="center"><strong>&nbsp;</strong></td>
		</tr>
	</table>
	<br>
	<?
		$alloc_sql = sql_select("select b.product_name_details, c.brand_name, b.lot, sum(a.qnty) as alloc_qnty from inv_material_allocation_dtls a, product_details_master b left join lib_brand c on b.brand= c.id where a.item_id= b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id= $sales_id group by b.product_name_details, c.brand_name, b.lot");
	?>
	<div style="float: left;">
		<div style="float: left; padding-right: 50px;">
			<strong style="font: bold 12px tahoma;">Allocated Details</strong>
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
				<tr bgcolor="#CCCCCC">
					<td align="center"><strong>SL</strong></td>
					<td align="center"><strong>Yarn Description</strong></td>
					<td align="center"><strong>Brand</strong></td>
					<td align="center"><strong>Lot</strong></td>
					<td align="center"><strong>Allocated Qty (Kg)</strong></td>
				</tr>
				<?
				$i=1;
				foreach ($alloc_sql as $val) 
				{
					?>
					<tr>
					<td align="center"><strong><? echo $i;?></strong></td>
					<td align="center"><strong><? echo $val[csf("product_name_details")];?></strong></td>
					<td align="center"><strong><? echo $val[csf("brand_name")];?></strong></td>
					<td align="center"><strong><? echo $val[csf("lot")];?></strong></td>
					<td align="center"><strong><? echo number_format($val[csf("alloc_qnty")],2);?></strong></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		
	</div>
	<div style="float: left;">
		<?
	$color_measure_sql = sql_select("select a.sales_dtls_id, b.body_part_id, a.color_number_id, a.measurement, a.stripe_color, a.yarn_dyed, sum(a.fabreqtotkg) as fabreqtotkg, b.finish_qty
	from wo_pre_stripe_color a, fabric_sales_order_dtls b
	where a.sales_dtls_id= b.id and a.status_active=1 and a.is_deleted=0 and b.mst_id =$sales_id
	group by a.sales_dtls_id, b.body_part_id, a.color_number_id, a.measurement, a.stripe_color, a.yarn_dyed, b.finish_qty");

	foreach ($color_measure_sql as $val) 
	{
		if($salesDtlsArr[$val[csf("sales_dtls_id")]] =="")
		{
			$salesDtlsArr[$val[csf("sales_dtls_id")]] = $val[csf("sales_dtls_id")];
			$body_part_color_arr2[$val[csf("body_part_id")]][$val[csf("color_number_id")]]["finish_qty"] += $val[csf("finish_qty")];
		}

		$body_part_color_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]][$val[csf("stripe_color")]]['stripe_color'] = $val[csf("stripe_color")];
		$body_part_color_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]][$val[csf("stripe_color")]]['fabreqtotkg'] += $val[csf("fabreqtotkg")];
		$body_part_color_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]][$val[csf("stripe_color")]]['st_color'] = $val[csf("stripe_color")];
		$body_part_color_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]][$val[csf("stripe_color")]]['measurement'] += $val[csf("measurement")];
		$body_part_color_arr[$val[csf("body_part_id")]][$val[csf("color_number_id")]][$val[csf("stripe_color")]]['yarn_dyed'] = $val[csf("yarn_dyed")];
	}
	/*echo "==========================<pre>";
	print_r($color_measure_sql);die;*/
	?> 

					

		<div style="float: left; padding-right: 50px;">
			<strong style="font: bold 12px tahoma;">Stripe Details</strong>
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
				<tr bgcolor="#CCCCCC">
					<td align="center"><strong>SL</strong></td>
					<td align="center"><strong>Body Part</strong></td>
					<td align="center"><strong>Fabric Color</strong></td>
					<td align="center"><strong>Fabric Qty(KG)</strong></td>
					<td align="center"><strong>Stripe Color</strong></td>
					<td align="center"><strong>Stripe Measurement</strong></td>
					<td align="center"><strong>Qty.(KG)</strong></td>
					<td align="center"><strong>Y/D Req.</strong></td>
				</tr>
				<?
				$i=1;
				foreach ($body_part_color_arr as $bodyPartId=>$bodyPartIdData) 
				{
					foreach ($bodyPartIdData as $COLORID => $COLORIDDATA) 
					{
						$body_part_color_arr2[$bodyPartId][$COLORID]["finish_qty"];
						$row_no = count($COLORIDDATA);
						?>
						<tr>
							<td align="center" rowspan="<? echo $row_no;?>"><strong><? echo $i;?></strong></td>
							<td align="center" rowspan="<? echo $row_no;?>"><strong><? echo $body_part[$bodyPartId];?></strong></td>
							<td align="center" rowspan="<? echo $row_no;?>"><strong><? echo $color_library[$COLORID];?></strong></td>
							<td align="center" rowspan="<? echo $row_no;?>"><strong><? echo $body_part_color_arr2[$bodyPartId][$COLORID]["finish_qty"];?></strong></td>
						<?
						foreach ($COLORIDDATA as $ST_COLOR => $val) 
						{
							?>
							<td align="center"><strong><? echo $color_library[$ST_COLOR];?></strong></td>
								<td align="center"><strong><? echo number_format($val["measurement"],2);?></strong></td>
								<td align="center"><strong><? echo number_format($val["fabreqtotkg"],2);?></strong></td>
								<td align="center"><strong><? echo $yes_no[$val["yarn_dyed"]];?></strong></td>
							</tr>
							<?
							
						}
					}
					$i++;
				}
				?>
			</table>
		</div>
		
	</div>
		<div style="clear: both;"></div>										
	<div style="float: left; padding-right: 50px;">					
		<strong style="font: bold 12px tahoma;">Collar  Measurement Info</strong>
		<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
			<tr bgcolor="#CCCCCC">
				<td align="center" width="40" rowspan="2"><strong>SL</strong></td>
				<td align="center" colspan="5"><strong>Coller</strong></td>
				<td align="center" colspan="5"><strong>Cuff</strong></td>
			</tr>
			<tr>
				<td align="center"><strong>Gmt Size</strong></td>
				<td align="center"><strong>Item Size</strong></td>
				<td align="center"><strong>Qnty (Pcs)</strong></td>
				<td align="center"><strong>Ex %</strong></td>
				<td align="center"><strong>Total Qnty</strong></td>
				<td align="center"><strong>Gmt Size</strong></td>
				<td align="center"><strong>Item Size</strong></td>
				<td align="center"><strong>Qnty (Pcs)</strong></td>
				<td align="center"><strong>Ex %</strong></td>
				<td align="center"><strong>Total Qnty</strong></td>
			</tr>
			<?
			$i=1;
			foreach ($collor_cuff_sql as $row) 
			{
			?>
			<tr>
				<td align="center"><strong><? echo $i;?></strong></td>
				<td align="center"><strong><? echo $row[csf("coller_gmt_size")];?></strong></td>
				<td align="center"><strong><? echo $row[csf("coller_size")];?></strong></td>
				<td align="center"><strong><? echo round($row[csf("coller_qnty")]);?></strong></td>
				<td align="center"><strong><? echo $row[csf("coller_ex_percent")];?></strong></td>
				<td align="center">
				<strong>
				<? 
					echo round($row[csf("coller_qnty")] + ($row[csf("coller_qnty")]*$row[csf("coller_ex_percent")])/100);
				?>
				</strong>
				</td>
				<td align="center"><strong><? echo $row[csf("cuff_gmt_size")];?></strong></td>
				<td align="center"><strong><? echo $row[csf("cuff_size")];?></strong></td>
				<td align="center"><strong><? echo round($row[csf("cuff_qnty")]);?></strong></td>
				<td align="center"><strong><? echo $row[csf("cuff_ex_percent")];?></strong></td>
				<td align="center">
				<strong>
				<? 
					echo round($row[csf("cuff_qnty")] + ($row[csf("cuff_qnty")]*$row[csf("cuff_ex_percent")])/100);
				?>
				</strong>
				</td>
			</tr>
			<?
			$total_collar +=$row[csf("coller_qnty")];
			$total_collar_with_perc +=$row[csf("coller_qnty")] + ($row[csf("coller_qnty")]*$row[csf("coller_ex_percent")])/100;
			$total_cuff +=$row[csf("cuff_qnty")];
			$total_cuff_qnty_with_perc +=$row[csf("cuff_qnty")] + ($row[csf("cuff_qnty")]*$row[csf("cuff_ex_percent")])/100;
			$i++;
			}
			?>
			<tr>
				<td align="center" colspan="3"><strong>Total</strong></td>
				<td align="center"><strong><? echo $total_collar;?></strong></td>
				<td align="center"></td>
				<td align="center"><strong><? echo round($total_collar_with_perc);?></strong></td>
				<td align="center" colspan="2"></td>
				<td align="center"><strong><? echo $total_cuff;?></strong></td>
				<td align="center"></td>
				<td align="center"><strong><? echo round($total_cuff_qnty_with_perc);?></strong></td>
			</tr>
		</table>

	</div>
	<div style="clear: both;"></div>	
	<strong style="font: bold 12px tahoma;">Special Instruction</strong>
	<table border="1" rules="all" cellpadding="3" style="font-size: 12px;">
		<tr bgcolor="#CCCCCC">
			<td align="center"><strong>Sl</strong></td>
			<td><strong>Terms</strong></td>
		</tr>
		<?
		$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$salesOrderNo'");
		if (count($data_array) > 0) {
			$i = 0;
			foreach ($data_array as $row) {
				$i++;
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td><? echo $row[csf('terms')]; ?></td>
				</tr>
				<?
			}
		}
		
        ?>

    </tbody>
</table>
<?
exit();
}
?>