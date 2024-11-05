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

if ($action == "print_report_setting_action") 
{
	$data = explode("_", $data);
	$company_id = $data[0];
	$booking_entry_form = $data[1];
	if ($booking_entry_form==86 || $booking_entry_form==118)
	{
		// Budget Wise Fabric Booking and Main Fabric Booking V2
		$print_report_id=return_field_value("format_id"," lib_report_template","template_name =$company_id and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
	}
	else if($booking_entry_form==88) // Short Fabric Booking
    {
        $print_report_id=return_field_value("format_id"," lib_report_template","template_name=$company_id  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
    }
	else if ($booking_entry_form == 108) // Partial Fabric Booking
	{
    	$print_report_id=return_field_value("format_id"," lib_report_template","template_name=$company_id and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
	}
	else if($booking_entry_form=='SM')// Sample with order
    {
    	$print_report_id=return_field_value("format_id"," lib_report_template","template_name=$company_id  and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
    }
	else if($booking_entry_form=='SMN')// Sample without order
    {
    	$print_report_id=return_field_value("format_id"," lib_report_template","template_name=$company_id  and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
    }
    else if($booking_entry_form==140)// Sample Requisition Fabric Booking -Without order
    {
    	$print_report_id=return_field_value("format_id"," lib_report_template","template_name=$company_id and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
    }   
	
	$format_ids=explode(",",$print_report_id);
	$print_report_id=$format_ids[0];		
	echo "document.getElementById('print_booking_details').value 	= '" . $print_report_id . "';\n";
}

if ($action == "load_drop_down_location") 
{
	$location_sql="select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name";
	$location_sql_result=sql_select($location_sql);
	$seletedId="";
	if (count($location_sql_result)==1) 
	{
		$seletedId=$location_sql_result[0][csf('id')];
	}
	echo create_drop_down("cbo_location_name", 162, $location_sql, "id,location_name", 1, "-- Select Location --", $seletedId, "");
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
			echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/fabric_sales_order_entry_controller', this.value, 'load_drop_down_season', 'season_td' )", 0);
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
		if ($data[0] == 1) 
		{
			echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
		else
		{
			//echo create_drop_down("cbo_cust_buyer_name", 162, $blank_array, "", 1, "--Select Buyer--", 0, "");

			echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
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
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=67 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#print1').hide();\n";
	echo "$('#print_2').hide();\n";
	echo "$('#print_3').hide();\n";
	echo "$('#print_4').hide();\n";
	echo "$('#print_6').hide();\n";
	echo "$('#print_7').hide();\n";



	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==115){echo "$('#print1').show();\n";}
			if($id==116){echo "$('#print_2').show();\n";}
			if($id==136){echo "$('#print_3').show();\n";}
			if($id==137){echo "$('#print_4').show();\n";}
			if($id==129){echo "$('#print_6').show();\n";}
			if($id==72){echo "$('#print_7').show();\n";}
		}
	}
	else
	{
		echo "$('#print1').show();\n";
		echo "$('#print_2').show();\n";
		echo "$('#print_3').show();\n";
		echo "$('#print_4').show();\n";
		echo "$('#print_6').show();\n";
		echo "$('#print_7').show();\n";
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

		function js_set_value(data, is_approved) 
		{
			// alert(is_approved);
			if (is_approved == 1) 
			{
				$('#hidden_booking_data').val(data);
				parent.emailwindow.hide();
			}
			else if (is_approved == 3) 
			{
				var response=return_global_ajax_value( data, 'check_approvl_necessity_setup', '', 'fabric_sales_order_entry_controller');
				if(response==1)
				{
					$('#hidden_booking_data').val(data);
					parent.emailwindow.hide();
				}
				else
				{
					alert("Approved Booking First.");
					return;
				}
			}
			else 
			{
				alert("Approved Booking First.");//FOUND
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
								$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name=$cbo_company_id and variable_list=66 and status_active=1");

								if($variable_textile_sales_maintain[0][csf('production_entry')] ==2)
								{
									$textile_sales_maintain = 1;
									$comp_cond = " and comp.id = $cbo_company_id";
								}else{
									$textile_sales_maintain = 0;
								}

								echo create_drop_down("cbo_buyer_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $comp_cond order by comp.company_name", "id,company_name", 1, "-- All Unit --", $selected, "", $textile_sales_maintain);
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
								$search_by_arr = array(1 => "Booking No", 2 => "Job No", 3 => "Sample Booking With Order", 4 => "Sample Booking Without Order", 5 => "IR/IB");
								$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value +'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $textile_sales_maintain;?>', 'create_booking_search_list_view', 'search_div', 'fabric_sales_order_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1, tableFilters);'); accordion_menu(accordion_h1.id,'content_search_panel','')"
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

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$unit_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$buyer_id = $data[6];
	$year_selection = $data[7];
	$textile_sales_maintain = $data[8];

	//Main Fabric Booking V2, Partial Fabric Booking > BOOKING_TYPE=1, IS_SHORT=2
	//Sample Fabric Booking -With order > BOOKING_TYPE=4, IS_SHORT=2
	//Sample Fabric Booking -Without order > BOOKING_TYPE=4, IS_SHORT=''
	$search_field_cond = '';
	if ($search_string != "") 
	{
		if ($search_by == 1) 
		{
			$search_field_cond = " and a.booking_no_prefix_num=$search_string and a.booking_type=1 and a.is_short in(1,2)";
		} 
		else if ($search_by == 2) 
		{
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		}
		else if ($search_by == 3) 
		{
			$search_field_cond = " and a.booking_no_prefix_num=$search_string and a.booking_type=4 and a.is_short=2";
		}
		else if ($search_by == 4) 
		{
			$search_field_cond = " and a.booking_no_prefix_num=$search_string and a.booking_type=4 and a.is_short is null";
		}
		else if ($search_by == 5) 
		{

			//for internal ref.
			if($year_selection!="")
			{
				$year_cond2 =" and to_char(a.insert_date,'YYYY') = ".$year_selection."";
			}
			$internalRef_cond = '';$booking_nos_cond = '';
			$internalRef_cond = " and a.grouping like '%".$search_string."%'";

			$sql_bookings=sql_select("select b.booking_no from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond $year_cond2");
			$booking_nos="";$bookingArrChk=array();
			foreach ($sql_bookings as $row) {
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_cond = "and a.booking_no in($booking_nos)";
			unset($sql_bookings);
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

	if($textile_sales_maintain ==1){
		$fabric_source_cond = " and a.fabric_source=1";
		$fabric_source_cond2 = " and (a.fabric_source in (1)  or b.fabric_source in(1) )";
	}

	$restiction_date = "";//"01-01-2021";
	if($restiction_date !="")
	{
		if($db_type==0)
		{
			$restiction_date_cond="and a.booking_date > '".change_date_format(trim($restiction_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$restiction_date_cond="and a.booking_date > '".change_date_format(trim($restiction_date),'','',1)."'";
		}
	}
	
	/*$sql = "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.fabric_composition, $po_break_down_id_cond b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise as season,a.remarks,a.attention,c.copmposition as composition,0 as booking_without_order
	FROM wo_booking_mst a, wo_po_details_master b, wo_booking_dtls c
	WHERE a.booking_no=c.booking_no and c.job_no = b.job_no and a.pay_mode=5 and a.fabric_source in (1,2,4) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond $fabric_source_cond $restiction_date_cond
	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, a.fabric_composition, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise,a.remarks,a.attention,c.copmposition 
	union all
	select a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, null as entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.fabric_composition, $po_break_down_id_cond null as job_no,null as style_ref_no,null as team_leader,null as dealing_marchant, null as season,null as remarks,a.attention,b.composition,1 as booking_without_order 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.pay_mode=5 and (a.fabric_source in (1,2,4)  or b.fabric_source in(1,2,4) ) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond $fabric_source_cond2 $restiction_date_cond 
	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.fabric_composition, a.po_break_down_id,a.attention,b.composition";*/ 

	$sql = "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_cond b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise as season,a.remarks,a.attention,a.fabric_composition as fabric_composition,0 as booking_without_order
	FROM wo_booking_mst a, wo_po_details_master b, wo_booking_dtls c
	WHERE a.booking_no=c.booking_no and c.job_no = b.job_no and a.pay_mode=5 and a.fabric_source in (1,2,4) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond $fabric_source_cond $restiction_date_cond $booking_nos_cond 
	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, a.fabric_composition, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise,a.remarks,a.attention
	union all
	select a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, null as entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_cond null as job_no,null as style_ref_no,null as team_leader,null as dealing_marchant, null as season,null as remarks,a.attention,cast(b.composition as varchar2(4000)) as fabric_composition,1 as booking_without_order 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.pay_mode=5 and (a.fabric_source in (1,2,4)  or b.fabric_source in(1,2,4) ) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond $fabric_source_cond2 $restiction_date_cond $booking_nos_cond  
	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id,a.attention,b.composition";//and a.id not in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id and c.status_active=1 and c.is_deleted=0)
	// echo $sql; // and a.po_break_down_id in(58695,58697)
	$result = sql_select($sql);
	$booking_id_arr=array();
	$po_id_arr=array();
	$style_ref_no_arr=array();
	$bookingID="";
	$poID="";
	$jobsArrChks = array();
	$jobs_nos="";
	foreach ($result as $row)
	{
		$booking_id_arr[] = $row[csf("id")];
		$po_id_arr[] = $row[csf("po_break_down_id")];
		$style_ref_no_arr[$row[csf("booking_no")]][$row[csf("style_ref_no")]] = $row[csf("style_ref_no")];

		$bookingID.=$row[csf("id")].",";
		$poID.=$row[csf("po_break_down_id")].",";

		if ($row[csf('job_no')] != "")
		{
			if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
			{
				$jobs_nos.="'".$row[csf('job_no')]."',";
				$jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
			}	
		}	
	}
	$jobs_nos=chop($jobs_nos,",");
	if ($search_string == "")
	{
		//for internal ref.
		$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst in($jobs_nos) group by b.booking_no,a.job_no_mst,a.grouping");
		$internalRefArr=array();
		foreach ($sql_bookings as $row) {
			$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];			
		}
		unset($sql_bookings);
	}
	



	$sales_booking=array();
	if(!empty($booking_id_arr))
	{
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

		if ($db_type==2)
		{
			$check_booking_in_salesorder = sql_select("select id, booking_id,booking_without_order from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0 $bookingID_cond");
		}
		else
		{
			$check_booking_in_salesorder = sql_select("select id, booking_id,booking_without_order from fabric_sales_order_mst where within_group=1 and status_active=1 and is_deleted=0 and booking_id in(".implode(",",$booking_id_arr).")");
		}
		
		foreach ($check_booking_in_salesorder as $sales_row)
		{
			$booking_without_order = ($sales_row[csf('booking_without_order')]==1)?1:0;
			$sales_booking[$sales_row[csf("booking_id")]][$booking_without_order] = $sales_row[csf("id")];
		}
	}

	if(!empty($po_id_arr))
	{
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

		/*if ($db_type==2) {
			$po_arr = return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 $poID_cond", 'id', 'po_number');
		}
		else
		{
			$po_arr = return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 and id in(".implode(",",$po_id_arr).")", 'id', 'po_number');
		}*/
		$po_sql="select id,po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in(".implode(",",$po_id_arr).")";
		$result_po_sql = sql_select($po_sql);
		foreach ($result_po_sql as $key => $row) 
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')];
			$po_int_ref_arr[$row[csf('id')]]=$row[csf('grouping')];
		}
	}

	$smn_booking="select distinct a.style_ref_no,b.booking_no from sample_development_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.id=c.style_id and b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and a.style_ref_no is not null $bookingID_cond2";
	$result_smn_booking = sql_select($smn_booking);
	foreach ($result_smn_booking as $row)
	{
		$booking_style_arr[$row[csf('booking_no')]][$row[csf('style_ref_no')]] = $row[csf('style_ref_no')];
	}
	//print_r($booking_style_arr);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1145" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Buyer</th>
			<th width="65">Unit</th>
			<th width="90">Booking No</th>
			<th width="50">Booking ID</th>
			<th width="90">Job No</th>
			<th width="110">Style Ref.</th>
			<th title="Internal Ref" width="100">IR/IB</th>
			<th width="80">Booking Date</th>
			<th width="80">App. Date</th>
			<th width="80">Delivery Date</th>
			<th width="70">Currency</th>
			<th width="60">Approved</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:1180px; max-height:265px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1145" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		//echo $sql;
		$result = sql_select($sql);
		foreach ($result as $row) 
		{
			if ($sales_booking[$row[csf("id")]][$row[csf('booking_without_order')]]=="")
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				// echo $row[csf('po_break_down_id')].'='.$row[csf('booking_no')].'<br>';
				if ($row[csf('po_break_down_id')] != "") 
				{
					$po_no = ''; $po_no_ref=""; $int_ref="";
					$po_ids = explode(",", $row[csf('po_break_down_id')]);
					foreach ($po_ids as $po_id) 
					{
						if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];

						if ($po_no_ref == "") $po_no_ref = $po_arr[$po_id].':'.$po_int_ref_arr[$po_id]; else $po_no_ref .= "," . $po_arr[$po_id].':'.$po_int_ref_arr[$po_id];
						if ($int_ref == "") $int_ref = $po_int_ref_arr[$po_id]; else $int_ref .= "," . $po_int_ref_arr[$po_id];
					}
				}
				if($row[csf('booking_without_order')]==1)
				{
					$row[csf('style_ref_no')]=implode(',', $booking_style_arr[$row[csf('booking_no')]]);
				}
				else
				{
					//$row[csf('style_ref_no')]=$row[csf('style_ref_no')];
					$row[csf('style_ref_no')]=implode(',', $style_ref_no_arr[$row[csf('booking_no')]]);
				}
		
				$data = $row[csf('id')] . '__' . $row[csf('booking_no')] . '__' . $row[csf('company_id')] . '__' . $row[csf('style_ref_no')] . '__' . change_date_format($row[csf('delivery_date')]) . '__' . $row[csf('currency_id')] . '__' . $row[csf('season')] . '__' . $row[csf('team_leader')] . '__' . $row[csf('dealing_marchant')] . '__' . $row[csf('remarks')] . '__' . $row[csf('is_approved')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('is_short')] . '__' . $row[csf('fabric_source')] . '__' . $row[csf('item_category')] . '__' . $row[csf('job_no')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('po_break_down_id')] . '__' . $row[csf('entry_form')] . '__' . $row[csf('booking_without_order')] . '__' . change_date_format($apporved_date_arr[$row[csf('id')]]). '__' . $row[csf('attention')] . '__' . $row[csf('fabric_composition')]. '__' . $row[csf('buyer_id')]. '__' . $int_ref. '__' . $po_no_ref;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center" style="word-break: break-all;"><? echo $i; ?></td>
					<td width="100" align="center" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center" style="word-break: break-all;"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center" style="word-break: break-all;"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center" style="word-break: break-all;"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center" style="word-break: break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="100" align="center" style="word-break: break-all;"><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></td>
					<td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80"
					align="center" style="word-break: break-all;"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center" style="word-break: break-all;"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center" style="word-break: break-all;"><? echo ($row[csf('is_approved')] == 1 || ($row[csf('is_approved')]==3 && $allow_partial==1)) ? "Yes" : "No"; ?></td>
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
		// echo "string";die;
		//partial booking...........................................................start;
		$partial_sql = "SELECT a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date,a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.fabric_composition, $po_break_down_id_str, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks,0 as booking_without_order, d.copmposition as composition, a.attention FROM wo_booking_mst a, wo_po_details_master b, wo_booking_dtls d 
		WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 and a.entry_form=108 $buyer_id_cond $unit_id_cond $search_field_cond $date_cond $year_cond $booking_nos_cond  and a.id not in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id) 
		group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.fabric_composition, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks, d.copmposition, a.attention order by a.booking_date asc";
		$partial_result = sql_select($partial_sql);
		$style_ref_no_arr=array();
		foreach ($partial_result as $row)
		{
			$style_ref_no_arr[$row[csf("booking_no")]][$row[csf("style_ref_no")]] = $row[csf("style_ref_no")];
			if ($row[csf('job_no')] != "")
			{
				if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
				{
					$jobs_nos.="'".$row[csf('job_no')]."',";
					$jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
				}	
			}	
		}
		$jobs_nos=chop($jobs_nos,",");
		if ($search_string == "")
		{
			//for internal ref.
			$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst in($jobs_nos) group by b.booking_no,a.job_no_mst,a.grouping");
			$internalRefArr=array();
			foreach ($sql_bookings as $row) {
				$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];			
			}
			unset($sql_bookings);
		}
		
		foreach ($partial_result as $row)
		{
			if (!in_array($row[csf('id')], $import_booking_id_arr)) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('po_break_down_id')] != "") 
				{
					$po_no = ''; $po_no_ref = ""; $int_ref = "";
					$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
					foreach ($po_ids as $po_id) 
					{
						if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];

						if ($po_no_ref == "") $po_no_ref = $po_arr[$po_id].':'.$po_int_ref_arr[$po_id]; else $po_no_ref .= "," . $po_arr[$po_id].':'.$po_int_ref_arr[$po_id];
						if ($int_ref == "") $int_ref = $po_int_ref_arr[$po_id]; else $int_ref .= "," . $po_int_ref_arr[$po_id];
					}
				}
				
				//for multiple job_no in a booking_no
				$row[csf('style_ref_no')]=implode(',', $style_ref_no_arr[$row[csf('booking_no')]]);
				
				$data = $row[csf('id')] . '__' . $row[csf('booking_no')] . '__' . $row[csf('company_id')] . '__' . $row[csf('style_ref_no')] . '__' . change_date_format($row[csf('delivery_date')]) . '__' . $row[csf('currency_id')] . '__' . $row[csf('season')] . '__' . $row[csf('team_leader')] . '__' . $row[csf('dealing_marchant')] . '__' . $row[csf('remarks')] . '__' . $row[csf('is_approved')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('is_short')] . '__' . $row[csf('fabric_source')] . '__' . $row[csf('item_category')] . '__' . $row[csf('job_no')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('po_break_down_id')] . '__' . $row[csf('entry_form')] . '__' . $row[csf('booking_without_order')] . '__' . change_date_format($apporved_date_arr[$row[csf('id')]]). '__' . $row[csf('attention')] . '__' . $row[csf('fabric_composition')]. '__' . $row[csf('buyer_id')]. '__' . $int_ref. '__' . $po_no_ref;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
					<td width="40" align="center" style="word-break: break-all;"><? echo $i; ?></td>
					<td width="100" align="center" style="word-break: break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="65" align="center" style="word-break: break-all;"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
					<td width="90" align="center" style="word-break: break-all;"><? echo $row[csf('booking_no')]; ?></td>
					<td width="50" align="center" style="word-break: break-all;"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
					<td width="90" align="center" style="word-break: break-all;"><? echo $row[csf('job_no')]; ?></td>
					<td width="110" align="center" style="word-break: break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="100" align="center" style="word-break: break-all;"><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></td>
					<td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($apporved_date_arr[$row[csf('id')]]); ?></td>
					<td width="80" align="center" style="word-break: break-all;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center" style="word-break: break-all;"><? echo $currency[$row[csf('currency_id')]]; ?></td>
					<td width="60" align="center" style="word-break: break-all;"><? echo ($row[csf('is_approved')] == 1 || ($row[csf('is_approved')]==3 && $allow_partial==1)) ? "Yes" : "No"; ?></td>
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
	$sql_deter = "select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id asc";
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
	select a.company_id, 0 as pre_cost_fabric_cost_dtls_id,a.booking_type,a.is_short, b.dia as dia_width,null as adjust_qty, b.body_part as body_part_id, b.color_type_id, CAST(b.dia_width AS NUMBER) AS width_dia_type, b.gsm_weight,b.remarks as pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color as fabric_color_id, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty,sum(b.finish_fabric) as fqnty, sum(b.grey_fabric*b.rate) as amnt,sum(b.finish_fabric*b.rate) as partial_amnt,b.process_loss as process_loss_percent,b.gmts_item_id as item_number_id,b.entry_form_id as entry_form,b.color_all_data as color_all_data from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.entry_form_id=140 and b.entry_form_id=140 and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.company_id, b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.uom, b.gsm_weight, b.dia, b.dia_width,b.fabric_color,b.remarks,b.process_loss, b.gmts_item_id,b.entry_form_id,b.color_all_data ";

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

	$variable_textile_sales_maintain = sql_select("select production_entry, process_loss_editable from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2 && $variable_textile_sales_maintain[0][csf('process_loss_editable')] ==1) $process_loss_editable_maintain = 1; else $process_loss_editable_maintain = 0;


	//for $booking_qnty_source 2,0 or null business as it is 
	//echo $booking_qnty_source."==="."select process_wise_rate_source from variable_settings_production where company_name=$company_id and variable_list=61 and status_active =1 ";die;
	if (!empty($data_array)) 
	{
		$i = 0;$total_amnt = 0;
		foreach ($data_array as $row) 
		{
			/*	
				if ($row[csf('entry_form')] == 108) 
				{
					if($textile_sales_maintain==1)
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
						if ($process_loss_method == 1) 
						{
							//echo "(".$row[csf('fqnty')]." + ((".$row[csf('fqnty')] ."/100) *". $row[csf('process_loss_percent')].") -". $row[csf('adjust_qty')].")" ."<br>";
							// Markup Method
							$grey_qty = ($row[csf('fqnty')] + (($row[csf('fqnty')] / 100) * $row[csf('process_loss_percent')]) - $row[csf('adjust_qty')]);
						} else {
							// Margin Method
							$grey_qty = (($row[csf('fqnty')]) / (1 - $row[csf('process_loss_percent')] / 100) - $row[csf('adjust_qty')]);
						}
					}
					else
					{
						$grey_qty = $row[csf('fqnty')];
					}

					if($booking_qnty_source==1)
					{
						$grey_qty = $row[csf('fqnty')];
					}
					else
					{
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
			*/


			if($process_loss_editable_maintain==0 && $textile_sales_maintain==1)
			{
				$disabled = " disabled";
			}


			if($textile_sales_maintain==1)
			{
				$grey_qty = $row[csf('fqnty')];
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
			}

			$process_loss_percent = $row[csf('process_loss_percent')]*1;

			if($row[csf('entry_form')]==140)
			{
				$colorID=explode("_",$row[csf('color_all_data')]);
				$row[csf('fabric_color_id')]=$row[csf('fabric_color_id')];//$colorID[2];//fabric_color_id
			}
			
			if ($grey_qty>0) {
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
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $color_range_id, "copy_process_loss($i);", "0", "", "", "", "", "", "", "cboColorRange[]");
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
					onblur="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
					value="<? echo number_format($fin_qnty, 4, '.', ''); ?>" title="<? echo number_format($fin_qnty, 4, '.', ''); ?>" <?php echo $readonly;?>/>

					<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>"
					value="<? echo number_format($row[csf('rmg_qty')], 4, '.', ''); ?>" readonly/>
				</td>
				<td width="52">
					<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" onKeyUp="calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i; ?>);" value="<? echo $process_loss_percent;?>" <? echo $disabled;?> />
				</td>
				<td width="67">
					<?
					if($textile_sales_maintain==1)
					{
						$grey_quantity = $row[csf('qnty')];
					}
					else
					{
						$fin_qnty = number_format($fin_qnty, 4, '.', '');
						$txtProcessLoss = $row[csf('process_loss_percent')]*1;   
						$grey_quantity=0;
						
						if($booking_qnty_source == 1)
						{
							
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
						else
						{
							$grey_quantity = $fin_qnty + (($fin_qnty / 100) * $txtProcessLoss);
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
	                <input type="hidden" name="txtProcessSeqSub[]" id="txtProcessSeqSub_<? echo $i; ?>" value="" />
	            </td>

				<td width="100">
					<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" disabled="" readonly />
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
	$company_id =  $data[3];

	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id asc";
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

	$sql = "SELECT a.id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.avg_rate, a.amount,a.grey_qty, a.work_scope, a.order_uom,a.pre_cost_fabric_cost_dtls_id,a.item_number_id, a.pre_cost_remarks, a.rmg_qty,a.process_loss,a.grey_qnty_by_uom,a.cons_uom,a.process_id_main,a.process_seq_main,a.process_id,a.process_seq,a.barcode_no from fabric_sales_order_dtls a left join fabric_sales_order_dtls b on (a.mst_id=b.mst_id and b.status_active=0 and b.is_deleted=1 and a.pre_cost_fabric_cost_dtls_id= b.pre_cost_fabric_cost_dtls_id and a.color_id=b.color_id) where a.mst_id='$job_no' and a.status_active=1 and a.is_deleted=0 group by a.id, a.body_part_id, a.color_type_id, a.fabric_desc, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_range_id, a.color_id, a.finish_qty, a.avg_rate, a.amount, a.grey_qty, a.work_scope, a.order_uom, a.pre_cost_fabric_cost_dtls_id,a.item_number_id,a.pre_cost_remarks, a.rmg_qty,a.process_loss,a.grey_qnty_by_uom,a.cons_uom,a.process_id_main,a.process_seq_main,a.process_id,a.process_seq,a.barcode_no order by a.body_part_id";
	// echo $sql;
	//var_dump($variable_rate_avg);die;
	$data_array = sql_select($sql);
	
	$sub_process_sql="SELECT fso_id, fso_dtls_id, main_process_id, main_process_rate, main_process_seq, sub_process_id, sub_process_seq from fabric_sales_order_subprocess where fso_id=$job_no and status_active=1 and is_deleted=0";
	$sub_process_data_array = sql_select($sub_process_sql);
	foreach ($sub_process_data_array as $key => $row) 
	{
		$sub_process_arr[$row[csf('fso_dtls_id')]][$row[csf('main_process_id')]].=$row[csf('sub_process_id')].'_'.$row[csf('sub_process_seq')].',';
		$main_process_arr[$row[csf('fso_dtls_id')]][$row[csf('main_process_id')]]=$row[csf('main_process_rate')].'*'.$row[csf('main_process_seq')];
	}
	// echo "<pre>";print_r($main_process_arr);die;

	$variable_textile_sales_maintain = sql_select("select production_entry, process_loss_editable from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2 && $variable_textile_sales_maintain[0][csf('process_loss_editable')] ==1) $process_loss_editable_maintain = 1; else $process_loss_editable_maintain = 0;
	
	$i = 0;
	foreach ($data_array as $row) {
		$i++;
		$uom = $row[csf('cons_uom')];
		$readonly = "readonly";
		if ($cbo_within_group == 2 ) {
			$readonly = "";
			$disableCombo = 0;
			$disable = "";
		}else{
			$disableCombo = 1;
			$disable = 'disabled="disabled"';
		}

		if ($cbo_within_group == 1 && $uom == 1 ) {
			$readonly = '';
		}
		if ($cbo_within_group == 1 && $uom != 1 ) {
			$readonly = 'readonly="readonly"';
		}

		$main_process_name_arr = array(1=>"Knitting",30=>"Yarn Dyeing",31=>"Fabric Dyeing",35=>"All Over Printing",1000=>"Additional");
		$process_name = ''; 
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val) {
			if ($process_name == "") $process_name = $main_process_name_arr[$val]; else $process_name .= "," . $main_process_name_arr[$val];
		}
		$process_name_main = '';$sub_process_str="";
		$process_id_main_array = explode(",", $row[csf("process_id_main")]);
		$process_seq_main_array = explode(",", $row[csf('process_seq_main')]);
		foreach ($process_id_main_array as $val) 
		{
			if ($process_name_main == "") $process_name_main = $main_process_name_arr[$val]; else $process_name_main .= "," . $main_process_name_arr[$val];

			$main_str=$val.'*'.$main_process_arr[$row[csf('id')]][$val];
			$sub_process=chop($sub_process_arr[$row[csf('id')]][$val],",");
			if ($sub_process!="") 
			{
				$sub_process_str.=$main_str.'*'.$sub_process.'@';
			}			
		}
		// echo $sub_process_str.'=<br>';


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
			<td width="52">
				<input type="text" name="txtFabricDia[]" id="txtFabricDia_<? echo $i; ?>" class="text_boxes"
				style="width:40px" value="<? echo $row[csf('dia')]; ?>" title="<? echo $row[csf('dia')]; ?>" <? echo $disable; ?> />
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
				echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $row[csf('color_range_id')], "copy_process_loss($i)", "0", "", "", "", "", "", "", "cboColorRange[]");
				?>
			</td>
			<td width="52">
				<?
				echo create_drop_down("cboConsUom_" . $i, 50, $unit_of_measurement, "", 0, "", $row[csf('cons_uom')], "", $disableCombo, "1,12,23,27", "", "", "", "", "", "cboConsUom[]");
				?>
			</td>
			<td width="67">
				<input type="text" name="txtBookingQnty[]" id="txtBookingQnty_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:55px" value="<? echo number_format($row[csf('grey_qnty_by_uom')], 4, '.', ''); ?>" title="<? echo number_format($row[csf('grey_qnty_by_uom')], 4, '.', ''); ?>" onKeyUp="calculate_amount(<?php echo $i;?>);calculate_fin_qty(<?php echo $i;?>);" <? echo $disable; ?> />
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
				<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:55px"
				onblur="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
				value="<? echo $row[csf('finish_qty')]; ?>" title="<? echo $row[csf('finish_qty')]; ?>" <?php echo $readonly; ?>/>
				<input type="hidden" name="rmgQty[]" id="rmgQty_<? echo $i; ?>" value="<? echo $row[csf('rmg_qty')]; ?>"
				readonly/>
			</td>
			<td width="52">
				<?
					if($textile_sales_maintain ==1 && $cbo_within_group ==1 && $process_loss_editable_maintain==0){
						$process_disabled = "disabled";
					}
				?>
				<input type="text" name="txtProcessLoss[]" id="txtProcessLoss_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:40px" onKeyUp="calculate_grey_qty(<? echo $i; ?>);copy_process_loss(<? echo $i;?>)"
				value="<? echo $row[csf('process_loss')]; ?>" title="<? echo $row[csf('process_loss')]; ?>" <? echo $process_disabled;?> />
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
                <input type="hidden" name="txtProcessSeqSub[]" id="txtProcessSeqSub_<? echo $i; ?>" value="<? echo $sub_process_str; ?>" />
            </td>
			<td width="100">
				<input type="text" name="txtProcessName[]" id="txtProcessName_<? echo $i; ?>" class="text_boxes" style="width:80px;" placeholder="Double Click To Search" onDblClick="openmypage_process(<? echo $i; ?>);" value="<? echo $process_name; ?>" disabled="" readonly />
				<input type="hidden" name="txtProcessId[]" id="txtProcessId_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>" />
				<input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq_<? echo $i; ?>" value="<? echo $row[csf('process_seq')]; ?>" />
			</td>
			<td width="100">
				<input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" class="text_boxes"
				style="width:90px"
				value="<? echo $row[csf('pre_cost_remarks')]; ?>" title="<? echo $row[csf('pre_cost_remarks')]; ?>" <?php echo ($cbo_within_group == 2) ? "" : "readonly"; ?>/>
			</td>
			<td width="100">
				<input type="text" name="barcode_no[]" id="barcode_no_<? echo $i; ?>" class="text_boxes"
				style="width:90px"
				value="<? echo $row[csf('barcode_no')]; ?>" readonly />
			</td>
			<td>
				<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:27px"
				class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:27px"
				class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" readonly/>
				<input type="hidden" name="pre_cost_fabric_cost_dtls_id[]"
				id="pre_cost_fabric_cost_dtls_id_<? echo $i; ?>"
				value="<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>" class="text_boxes" readonly/>
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
	$sql_deter = "select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id";
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

	if($db_type==0)
	{
		$dia_width_cond = " and b.entry_form_id ='' and a.entry_form_id =''";
	}
	else
	{
		$dia_width_cond = " and b.entry_form_id is null and a.entry_form_id is null";
	}


	/* $sql = "select a.company_id, a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.dia_width, c.body_part_id, c.color_type_id, c.width_dia_type,c.item_number_id, c.gsm_weight,b.pre_cost_remarks,c.lib_yarn_count_deter_id, b.fabric_color_id, b.process_loss_percent as process_loss,c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty, sum(b.fin_fab_qnty*b.rate) as amnt,sum(b.adjust_qty) as adjust_qty,a.entry_form from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.company_id, a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,b.process_loss_percent,a.entry_form,b.pre_cost_remarks,c.item_number_id
	union all
	select a.company_id, a.booking_no,0 as pre_cost_fabric_cost_dtls_id, null as po_break_down_id, b.dia_width, b.body_part, b.color_type_id, b.width_dia_type as width_dia_type,b.gmts_item_id,b.gsm_weight,b.remarks pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color fabric_color_id, b.process_loss, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty, sum(b.finish_fabric) as fqnty, sum(b.finish_fabric*b.rate) as amnt,null as adjust_qty,null as entry_form
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no $dia_width_cond and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0
	group by a.company_id, a.booking_no,b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.fabric_color, b.remarks, b.process_loss, b.gmts_item_id,b.dia_width
	union all
	select a.company_id, a.booking_no,0 as pre_cost_fabric_cost_dtls_id, null as po_break_down_id, b.dia as dia_width, b.body_part, b.color_type_id, b.width_dia_type as width_dia_type,b.gmts_item_id,b.gsm_weight,b.remarks pre_cost_remarks, b.lib_yarn_count_deter_id, b.fabric_color fabric_color_id, b.process_loss, b.uom, (sum(b.bh_qty)+sum(b.rf_qty)) as rmg_qty, sum(b.grey_fabric) as qnty, sum(b.finish_fabric) as fqnty, sum(b.finish_fabric*b.rate) as amnt,null as adjust_qty,null as entry_form
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	where a.booking_no=b.booking_no and a.entry_form_id=140 and b.entry_form_id=140 and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0
	group by a.company_id, a.booking_no,b.id,a.booking_type,a.is_short, b.lib_yarn_count_deter_id, b.body_part, b.color_type_id, b.width_dia_type, b.uom, b.gsm_weight, b.dia, b.fabric_color, b.remarks, b.process_loss, b.gmts_item_id"; */

	$sql = "select a.company_id, a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, b.dia_width, c.body_part_id, c.color_type_id, c.width_dia_type,c.item_number_id, c.gsm_weight,b.pre_cost_remarks,c.lib_yarn_count_deter_id, b.fabric_color_id, (sum(b.process_loss_percent) / count(b.process_loss_percent)) as  process_loss, c.uom, sum(b.rmg_qty) as rmg_qty, sum(b.grey_fab_qnty) as qnty, sum(b.fin_fab_qnty) as fqnty, sum(b.fin_fab_qnty*b.rate) as amnt,sum(b.adjust_qty) as adjust_qty,a.entry_form from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.company_id, a.booking_no,b.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, c.color_type_id, c.width_dia_type, c.uom, c.gsm_weight, b.dia_width, b.fabric_color_id,a.entry_form,b.pre_cost_remarks,c.item_number_id
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

	$variable_textile_sales_maintain = sql_select("select production_entry, process_loss_editable from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;
	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2 && $variable_textile_sales_maintain[0][csf('process_loss_editable')] ==1) $process_loss_editable_maintain = 1; else $process_loss_editable_maintain = 0;


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
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $row[csf('color_range_id')], "copy_process_loss($i)", "0", "", "", "", "", "", "", "cboColorRange[]");
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
						$fin_qnty = ($row[csf('grey_qty')] * 36 * $row[csf('dia_width')] * $row[csf('gsm_weight')]) / (1550 * 1000);
					}else if ($uom == 1) {
						$readonly = "";
						$fin_qnty = "";
					} else {
						$fin_qnty = $row[csf('grey_qty')] * 1;
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
					onblur="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
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

			if($textile_sales_maintain==1)
			{
				$booking_quantity = $finish_qty;
			}
			else
			{
				if($booking_qnty_source == 1)
				{
					$color_range_id = $colorRange_arr[$lib_yarn_count_deter_id];
					$booking_quantity = $finish_qty;
				}else{
					$color_range_id = $process_loss_per[0];
					$booking_quantity = $gray_qty;
				}
			}

			if($booking_quantity)
			{
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
					
					echo create_drop_down("cboColorRange_" . $i, 80, $color_range, "", 1, "-- Select --", $color_range_id, "copy_process_loss($i)", "0", "", "", "", "", "", "", "cboColorRange[]");
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
					$process_loss_percent_show=0;
					if($textile_sales_maintain==1)
					{
						$gray_qty_with_process_loss = $gray_qty;
						$process_loss_percent_show =  $booking_process_loss;
					}
					else
					{
						if($booking_qnty_source == 1)
						{
							$fin_qnty = number_format($fin_qnty, 4, '.', '');
							//$gray_qty_with_process_loss = ($fin_qnty + (($fin_qnty / 100) * $booking_process_loss ));
							$process_loss_percent_show =  $booking_process_loss;
							if ($process_loss_method == 1) 
							{
						 		$grey_quantity = $fin_qnty + (($fin_qnty / 100) * $booking_process_loss);
						 	}
						 	else
						 	{
						 		$perc = 1 - ($booking_process_loss / 100);
						 		$grey_quantity = $fin_qnty / $perc;
						 	}
						 	$gray_qty_with_process_loss = $grey_quantity;
						}
						else
						{
							$gray_qty_with_process_loss = ($fin_qnty + (($fin_qnty / 100) * $process_loss_per[1]));
							$process_loss_percent_show =  $process_loss_per[1];
						}
					}
					
					?>
					<input type="text" name="txtFinishQty[]" id="txtFinishQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px"	onblur="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);" value="<? echo number_format($fin_qnty, 4, '.', ''); ?>" title="<? echo number_format($fin_qnty, 4, '.', ''); ?>" <? echo $readonly;?> />
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
	$sql_deter = "select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id";
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
					onblur="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
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
					onblur="calculate_amount(<? echo $i; ?>); calculate_grey_qty(<? echo $i; ?>);"
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
		$new_sales_order_system_id = explode("*", return_next_id_by_sequence("FABRIC_SALES_ORDER_MST_PK_SEQ", "fabric_sales_order_mst",$con,1,$cbo_company_id,'FSOE',109,date("Y",time()),0 ));


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

		$field_array = "id, job_no_prefix, job_no_prefix_num, job_no, company_id, within_group, sales_booking_no, booking_id, booking_date, delivery_date, buyer_id, style_ref_no, location_id, ship_mode, team_leader, dealing_marchant, remarks, currency_id, season_id,season, inserted_by, insert_date,entry_form,booking_without_order, po_buyer, po_company_id, po_job_no, booking_entry_form,booking_type,booking_approval_date,ready_to_approved,attention,fabric_composition,sales_order_type,customer_buyer";

		$data_array = "(" . $id . ",'" . $new_sales_order_system_id[1] . "'," . $new_sales_order_system_id[2] . ",'" . $new_sales_order_system_id[0] . "'," . $cbo_company_id . "," . $cbo_within_group . "," . $txt_booking_no . "," . $txt_booking_no_id . "," . $txt_booking_date . "," . $txt_delivery_date . "," . $cbo_buyer_name . "," . $txt_style_ref . "," . $cbo_location_name . "," . $cbo_ship_mode . "," . $cbo_team_leader . "," . $cbo_dealing_merchant . "," . $txt_remarks . "," . $cbo_currency . "," . $txt_season . "," . $season_val . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',109,".$booking_without_order.",'".$po_buyer."','".$po_company_id."','".$po_job_no."','".$booking_entry_form."','".$booking_type_id."',".$booking_approval_date.",".$cbo_ready_to_approved."," . $txt_attention . "," . $txt_fab_comp . "," . $cbo_sales_order_type. "," . $cbo_cust_buyer_name . ")";

		$job_no = $new_sales_order_system_id[0];
		$job_update_id = $id;

		//$id_dtls = return_next_id("id", "fabric_sales_order_dtls", 1);

		if ($cbo_within_group_combo == 1) 
		{
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty,rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom,pre_cost_fabric_cost_dtls_id, item_number_id,pre_cost_remarks, inserted_by, insert_date,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no";
		} 
		else 
		{
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty,rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom,pre_cost_remarks, inserted_by, insert_date,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no";
		}

		$field_array_subprocess = "id, fso_id, fso_dtls_id, main_process_id, main_process_rate, main_process_seq, sub_process_id, sub_process_seq, inserted_by, insert_date, status_active, is_deleted";

		for ($i = 1; $i <= $total_row; $i++)
		{
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
			$txtProcessSeqSub = "txtProcessSeqSub" . $i;
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
						$color_id = return_id_lib_common( str_replace("'","",$$txt_color), $color_library, "lib_color", "id,color_name","109");
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

			if ($cbo_within_group_combo == 1) 
			{
				$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);

				$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "'," . $$pre_cost_fabric_cost_dtls_id . "," . $$cboGarmItemId . ",'" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','". $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ")";
			} 
			else 
			{
				$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);
				$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "','" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','". $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ")";
			}
			//$id_dtls = $id_dtls + 1;

			
			// ===================== Sub process Start =================================
			$txtProcessSeqSub=chop($$txtProcessSeqSub,"@");			
			// $txtProcessSeqSub="1*2*3*253_1";
			if ($txtProcessSeqSub!="") 
			{
				$sub_seq_str_arr=explode("@", $txtProcessSeqSub);
				// echo "<pre>"; print_r($sub_seq_str_arr); 
				foreach ($sub_seq_str_arr as $key => $value) //35*1*11*209_1,272_2
				{
					$sub_seq_str_arr2=explode("*", $value);
					// echo "<pre>";print_r($sub_seq_str_arr2);
					$sub_id_sub_seq_str_arr=explode(",", $sub_seq_str_arr2[3]); // 209_1,272_2
					// echo "<pre>";print_r($sub_id_sub_seq_str_arr);
					foreach ($sub_id_sub_seq_str_arr as $key2 => $val) 
					{
						$subp_id = return_next_id_by_sequence("FABRIC_SALES_ORDER_SUBPROCESS_PK_SEQ", "fabric_sales_order_subprocess", $con);
						// echo $key2.'<br>';
						$val2=explode("_", $val); // 209_1

						//$sub_seq_str_arr2[0].; // all main process id
						if ($data_array_subprocess != "") $data_array_subprocess .= ",";
						$data_array_subprocess .= "(" . $subp_id . "," . $job_update_id . "," . $id_dtls . "," . $sub_seq_str_arr2[0] . "," . $sub_seq_str_arr2[1] . "," . $sub_seq_str_arr2[2] . "," . $val2[0] . "," . $val2[1] . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
					}			
					// echo "<pre>"; print_r($data_array_subprocess).'<br>';	
					// echo $data_array_subprocess;		
				}
			}
			// ===================== Sub process End =================================


			//=============Process wise rate entry start =================================
			

			$txtProcessWiseRate=chop(str_replace("'","",$$txtProcessSeqMain));			
			if ($txtProcessWiseRate!="") 
			{
				$txtProcessWiseRateArr =explode("@@", $txtProcessWiseRate);
				$knitProcessRateArr = $txtProcessWiseRateArr[0];
				$dyeProcessRateArr = $txtProcessWiseRateArr[1];
				$aopProcessRateArr = $txtProcessWiseRateArr[2];
				$yarnDyeProcessRateArr = $txtProcessWiseRateArr[3];
				$addiProcessRateArr = $txtProcessWiseRateArr[4];

				$knitProcessRateArrDtls = explode("__",$knitProcessRateArr);
				$dyeProcessRateArrDtls = explode("__",$dyeProcessRateArr);
				$aopProcessRateArrDtls = explode("__",$aopProcessRateArr);
				$yarnDyeProcessRateArrDtls = explode("__",$yarnDyeProcessRateArr);
				$addiProcessRateArrDtls = explode("__",$addiProcessRateArr);

				$KNIT_FABRIC_TYPE = $knitProcessRateArrDtls[0];
				$KNIT_FROM_COUNT = $knitProcessRateArrDtls[1];
				$KNIT_TO_COUNT = $knitProcessRateArrDtls[2];
				$KNIT_CHARGE = $knitProcessRateArrDtls[3]; 

				$DYEING_COLOR_RANGE = $dyeProcessRateArrDtls[0];
				$DYEING_PART = $dyeProcessRateArrDtls[1];
				$DIA_WIDTH_TYPE = $dyeProcessRateArrDtls[2];
				$DYEING_UPTO = $dyeProcessRateArrDtls[3];
				$DYEING_SUBPROCESS = $dyeProcessRateArrDtls[4];
				$DYEING_CHARGE = $dyeProcessRateArrDtls[5];


				$AOP_TYPE = $aopProcessRateArrDtls[0];
				$AOP_NO_OF_COLOR = $aopProcessRateArrDtls[1];
				$AOP_COVERAGE_FROM = $aopProcessRateArrDtls[2];
				$AOP_COVERAGE_TO = $aopProcessRateArrDtls[3];
				$AOP_UPTO = $aopProcessRateArrDtls[4];
				$AOP_SUBPROCESS = $aopProcessRateArrDtls[5];
				$AOP_CHARGE = $aopProcessRateArrDtls[6];


				$YARN_DYEING_PART = $yarnDyeProcessRateArrDtls[0];
				$YARN_COLOR_RANGE = $yarnDyeProcessRateArrDtls[1];
				$YARN_DYEING_SUBPROCESS = $yarnDyeProcessRateArrDtls[2];
				$YARN_DYEING_CHARGE = $yarnDyeProcessRateArrDtls[3];

				$ADDITIONAL_PROCESS = $addiProcessRateArrDtls[0];
				$ADDITIONAL_CHARGE = $addiProcessRateArrDtls[1];


				$field_array_process_wise_rate = "id,mst_id,dtls_id,knit_fabric_type,knit_from_count,knit_to_count,knit_charge,dyeing_part,dia_width_type,dyeing_color_range,dyeing_upto,dyeing_subprocess,dyeing_charge,aop_type,aop_no_of_color,aop_coverage_from,aop_coverage_to,aop_upto,aop_subprocess,aop_charge,yarn_dyeing_part,yarn_color_range,yarn_dyeing_subprocess,yarn_dyeing_charge,additional_process,additional_charge,inserted_by, insert_date, status_active, is_deleted";

				$fpwr_id = return_next_id_by_sequence("fso_process_wise_rate_seq", "fabric_sales_order_prcs_rate", $con);

				if ($data_array_process_wise_rate != "") $data_array_process_wise_rate .= ",";
				$data_array_process_wise_rate .= "(".$fpwr_id.",".$job_update_id . "," . $id_dtls.",'".$KNIT_FABRIC_TYPE."','".$KNIT_FROM_COUNT."','".$KNIT_TO_COUNT."','".$KNIT_CHARGE . "','" . $DYEING_PART."','".$DIA_WIDTH_TYPE."','".$DYEING_COLOR_RANGE."','".$DYEING_UPTO."','".$DYEING_SUBPROCESS."','".$DYEING_CHARGE ."','". $AOP_TYPE."','".$AOP_NO_OF_COLOR."','".$AOP_COVERAGE_FROM."','".$AOP_COVERAGE_TO."','".$AOP_UPTO."','".$AOP_SUBPROCESS."','".$AOP_CHARGE."','". $YARN_DYEING_PART."','".$YARN_COLOR_RANGE."','".$YARN_DYEING_SUBPROCESS."','".$YARN_DYEING_CHARGE."','".$ADDITIONAL_PROCESS."','".$ADDITIONAL_CHARGE . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
			}
			//==============Process wise rate entry END ===================================
			
		}
		//echo $data_array_dtls;
		// echo "insert into fabric_sales_order_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID = sql_insert("fabric_sales_order_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("fabric_sales_order_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($data_array_subprocess!="") 
		{
			// echo "5**insert into fabric_sales_order_subprocess (".$field_array_subprocess.") values ".$data_array_subprocess;die;
			$rID3 = sql_insert("fabric_sales_order_subprocess", $field_array_subprocess, $data_array_subprocess, 1);
		}
		
		$rID4=1;
		if ($data_array_process_wise_rate!="") 
		{
			// echo "5**insert into fabric_sales_order_subprocess (".$field_array_subprocess.") values ".$data_array_subprocess;die;
			$rID4 = sql_insert("fabric_sales_order_prcs_rate", $field_array_process_wise_rate, $data_array_process_wise_rate, 1);
		}

		// echo "5**"."$rID && $rID2 && $rID3 && $rID4";die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID4) {
				mysql_query("COMMIT");
				echo "0**" . $job_update_id . "**" . $job_no;
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID4) {
				oci_commit($con);
				echo "0**" . $job_update_id . "**" . $job_no;
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}

		disconnect($con);
		die;
	} 
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$job_no = str_replace("'", "", $txt_job_no);
		$job_update_id = str_replace("'", "", $update_id);
		$cbo_within_group_combo = str_replace("'", "", $cbo_within_group);
		$check_txt_booking_no = str_replace("'", "", $txt_booking_no);

		// for within group Yes: check if program/production found FSO booking change not allow
		$variable_allocation = sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_id and variable_list=18 and item_category_id=13 and status_active=1");

		if($variable_allocation[0][csf('allocation')] =="" || $variable_allocation[0][csf('allocation')] ==0) $allocation = 2; $allocation=$variable_allocation[0][csf('allocation')];// Yes
		if ($cbo_within_group_combo == 1) //  for within group Yes
		{
			if ($allocation==1) // If program found
			{
				//echo "SELECT b.booking_no booking_no from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_mst b where  a.mst_id=b.id and a.po_id=$job_update_id and b.booking_no=$txt_hdn_booking_no and a.within_group=1 and a.status_active=1 and a.is_deleted=0 group by b.booking_no";die;
				$check_program_booking = return_field_value("b.booking_no booking_no", "ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_mst b", "a.mst_id=b.id and a.po_id=$job_update_id and a.within_group=1 and a.is_sales=1 and a.status_active=1 and a.is_deleted=0 group by b.booking_no","booking_no");
				// echo $check_program_booking .'!='. $check_txt_booking_no;
				if($check_program_booking){
					if($check_program_booking != $check_txt_booking_no)
					{
						echo "30**Program found. Sales/Booking No can not be changed";die;
					}
				}
			}
			else // if production found
			{
				// echo "SELECT a.sales_booking_no from fabric_sales_order_mst a, inv_receive_master b where a.id=b.booking_id and b.entry_form=2 and b.receive_basis=4 and b.status_active=1 and b.booking_no=$txt_job_n";die;
				$check_production_found = return_field_value("a.sales_booking_no sales_booking_no", "fabric_sales_order_mst a, inv_receive_master b", "a.id=b.booking_id and b.entry_form=2 and b.receive_basis=4 and b.status_active=1 and b.booking_no=$txt_job_no","sales_booking_no");
				// echo $check_production_found .'!='. $check_txt_booking_no;
				if($check_production_found){
					if($check_production_found != $check_txt_booking_no)
					{
						echo "30**Production found. Sales/Booking No can not be changed";die;
					}
				}			
			}
		}
		// echo "30**string";die;
		
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
		$sales_info = sql_select("select update_sl,is_master_part_updated,revise_no from fabric_sales_order_mst where id='" . $job_update_id . "'");
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
			$field_array_update = "company_id*within_group*sales_booking_no*booking_id*booking_date*delivery_date*buyer_id*style_ref_no*location_id*ship_mode*team_leader*dealing_marchant*remarks*currency_id*season_id*season*updated_by*update_date*update_sl*is_master_part_updated*is_apply_last_update*po_buyer*po_company_id*po_job_no*booking_entry_form*booking_type*revise_no*booking_approval_date*ready_to_approved*attention*fabric_composition*sales_order_type*customer_buyer";

			$data_array_update = $cbo_company_id . "*" . $cbo_within_group . "*" . $txt_booking_no . "*" . $txt_booking_no_id . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $cbo_buyer_name . "*" . $txt_style_ref . "*" . $cbo_location_name . "*" . $cbo_ship_mode . "*" . $cbo_team_leader . "*" . $cbo_dealing_merchant . "*" . $txt_remarks . "*" . $cbo_currency . "*" . $txt_season . "*'" . $season_val . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . ($update_serial+1) . "*" . $is_master_part_updated."*0*'".$po_buyer."'*'".$po_company_id."'*'".$po_job_no."'*'".$booking_entry_form."'*'".$booking_type_id."'*".($revise_no+1)."*".$booking_approval_date."*".$cbo_ready_to_approved."*".$txt_attention."*".$txt_fab_comp."*".$cbo_sales_order_type."*".$cbo_cust_buyer_name;
		}else{
			$field_array_update = "company_id*within_group*sales_booking_no*booking_id*booking_date*delivery_date*buyer_id*style_ref_no*location_id*ship_mode*team_leader*dealing_marchant*remarks*currency_id*season_id*season*updated_by*update_date*update_sl*is_master_part_updated*po_buyer*po_company_id*po_job_no*booking_entry_form*booking_type*booking_approval_date*ready_to_approved*attention*fabric_composition*sales_order_type*customer_buyer";

			$data_array_update = $cbo_company_id . "*" . $cbo_within_group . "*" . $txt_booking_no . "*" . $txt_booking_no_id . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $cbo_buyer_name . "*" . $txt_style_ref . "*" . $cbo_location_name . "*" . $cbo_ship_mode . "*" . $cbo_team_leader . "*" . $cbo_dealing_merchant . "*" . $txt_remarks . "*" . $cbo_currency . "*" . $txt_season . "*'" . $season_val . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . ($update_serial+1) . "*" . $is_master_part_updated."*'".$po_buyer."'*'".$po_company_id."'*'".$po_job_no."'*'".$booking_entry_form."'*'".$booking_type_id."'*".$booking_approval_date."*".$cbo_ready_to_approved."*".$txt_attention."*".$txt_fab_comp."*".$cbo_sales_order_type."*".$cbo_cust_buyer_name;
		}
		$barcode_year = date("y");
		if ($cbo_within_group_combo == 1) {
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty, rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom, pre_cost_fabric_cost_dtls_id, item_number_id, pre_cost_remarks, inserted_by, insert_date,status_active,is_deleted,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no";
		} else {
			$field_array_dtls = "id, mst_id, job_no_mst, body_part_id, color_type_id, fabric_desc, determination_id, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty, rmg_qty, avg_rate, amount, process_loss, grey_qty, work_scope, order_uom, pre_cost_remarks, inserted_by, insert_date,status_active,is_deleted,grey_qnty_by_uom,cons_uom,process_id,process_seq,process_id_main,process_seq_main,barcode_year,barcode_suffix_no,barcode_no";
		}

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
			$field_array_dtls_update = "body_part_id*color_type_id*fabric_desc*determination_id*gsm_weight*dia*width_dia_type*color_id*color_range_id*finish_qty*rmg_qty*avg_rate*amount*process_loss*grey_qty*work_scope*order_uom*pre_cost_fabric_cost_dtls_id*item_number_id*pre_cost_remarks*updated_by*update_date*status_active*is_deleted*grey_qnty_by_uom*cons_uom*process_id*process_seq*process_id_main*process_seq_main";
		} else {
			$field_array_dtls_update = "body_part_id*color_type_id*fabric_desc*determination_id*gsm_weight*dia*width_dia_type*color_id*color_range_id*finish_qty*rmg_qty*avg_rate*amount*process_loss*grey_qty*work_scope*order_uom*pre_cost_remarks*updated_by*update_date*status_active*is_deleted*grey_qnty_by_uom*cons_uom*process_id*process_seq*process_id_main*process_seq_main";
		}

		$field_array_subprocess = "id, fso_id, fso_dtls_id, main_process_id, main_process_rate, main_process_seq, sub_process_id, sub_process_seq, inserted_by, insert_date, status_active, is_deleted";

		$is_new = 0;
		$dtls_ids_to_delete = "";
		$allocation_product_qnty_arr = $product_qnty_arr = array();
		for ($i = 1; $i <= $total_row; $i++) 
		{
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
			$txtProcessSeqSub = "txtProcessSeqSub" . $i;
			$rmgQty = "rmgQty" . $i;
			if ($cbo_within_group_combo == 1) {
				$cboGarmItemId = "cboGarmItemId" . $i;
				$pre_cost_fabric_cost_dtls_id = "pre_cost_fabric_cost_dtls_id" . $i;
			}

			if (str_replace("'", '', $color_from_library) == 2 && str_replace("'", '', $cbo_within_group) == 2)
			{				
				if(str_replace("'","",$$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$$txt_color),$new_array_color))
					{
						$color_id = return_id_lib_common( str_replace("'","",$$txt_color), $color_library, "lib_color", "id,color_name","109");
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
					if ($old_data[$pre_cost_dtls_id][$$cboGarmItemId][$$cboBodyPart][$$txtFabricGsm][$$txtFabricDia][$$cboColorType][$$txtFabricDesc][$$cboDiaWidthType][$color_id][$$txtRemarks]['id'] == '') {
						// get the changed pre-cost ids
						$changed_precost_ids = $pre_cost_dtls_id;
						// prepare new sales data for entry
						$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);

						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "'," . $$txtFabricGsm . ",'" . $dia . "','" . $$cboDiaWidthType . "','" . $color_id . "'," . $$cboColorRange . ",'" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "'," . $$pre_cost_fabric_cost_dtls_id . "," . $$cboGarmItemId . ",'" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','" . $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ")";
						$dtlsId = $id_dtls;
					} else {
						$tid = $old_data[$pre_cost_dtls_id][$$cboGarmItemId][$$cboBodyPart][$$txtFabricGsm][$$txtFabricDia][$$cboColorType][$$txtFabricDesc][$$cboDiaWidthType][$color_id][$$txtRemarks]['id'];
						$is_old_arr[] = $tid;
						unset ($oldids[$tid]);
						execute_query("update fabric_sales_order_dtls set body_part_id='" . $$cboBodyPart . "',color_type_id='" . $$cboColorType . "',fabric_desc='" . $$txtFabricDesc . "',determination_id='" . $$fabricDescId . "',gsm_weight='" . $$txtFabricGsm . "',dia='" . $dia . "',width_dia_type='" . $$cboDiaWidthType . "', color_id='" . $color_id . "',color_range_id='" . $$cboColorRange . "', finish_qty='" . $$txtFinishQty . "', rmg_qty='" . $$rmgQty . "', avg_rate='" . $$txtAvgRate . "', amount='" . $$txtAmount . "', process_loss='" . $$txtProcessLoss . "', grey_qty='" . $$txtGreyQty . "',work_scope='" . $$cboWorkScope . "', updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "', grey_qnty_by_uom=" . $$booking_qnty_by_uom . ", cons_uom=" . $$cboConsUom . ", process_id='" . $$txtProcessId . "', process_seq='" . $$txtProcessSeq . "', process_id_main='". $$txtProcessIdMain ."', process_seq_main='" . $$txtProcessSeqMain . "' where id in (" . $tid . ")", 0);
						oci_commit($con);
					}

					if ($is_apply_last_update == 1) {
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
				} else {
					$dtlsId = str_replace("'", '', $$updateIdDtls);
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("'" . $$cboBodyPart . "'*'" . $$cboColorType . "'*'" . $$txtFabricDesc . "'*'" . $$fabricDescId . "'*'" . $$txtFabricGsm . "'*'" . $$txtFabricDia . "'*'" . $$cboDiaWidthType . "'*'" . $color_id . "'*'" . $$cboColorRange . "'*'" . $$txtFinishQty . "'*'" . $$rmgQty . "'*'" . $$txtAvgRate . "'*'" . $$txtAmount . "'*'" . $$txtProcessLoss . "'*'" . $$txtGreyQty . "'*'" . $$cboWorkScope . "'*'" . $$cboUom . "'*" . $$pre_cost_fabric_cost_dtls_id . "*" . $$cboGarmItemId . "*'" . $$txtRemarks . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0*" . $$booking_qnty_by_uom . "*" . $$cboConsUom. "*'" . $$txtProcessId. "'*'" . $$txtProcessSeq. "'*'" .$$txtProcessIdMain ."'*'" . $$txtProcessSeqMain . "'"));
					$rID2 = 1;
					//=================
				}
			} else {
                // WITHIN GROUP = NO
				if (str_replace("'", "", $$updateIdDtls) == "") {
					$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "','" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','" . $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ")";
					//$id_dtls = $id_dtls + 1;
					$dtlsId = $id_dtls;
					$rID2 = 1;
				} else {
					$rID2 = 1;
					$get_existing_dtls_row = explode("*",$old_id_desc[str_replace("'", "", $$updateIdDtls)]);
					if(($get_existing_dtls_row[0] != $$cboBodyPart) || ($get_existing_dtls_row[1] != $$cboColorType) || ($get_existing_dtls_row[2] != $$fabricDescId) || ($get_existing_dtls_row[3] != $$txtFabricGsm) || ($get_existing_dtls_row[4] != $$txtFabricDia) ||($get_existing_dtls_row[6] != $$cboDiaWidthType))
					{
						$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_DTLS_PK_SEQ", "fabric_sales_order_dtls", $con);
						$dtls_ids_to_delete .= ($dtls_ids_to_delete == "") ? $$updateIdDtls : "," . $$updateIdDtls;
						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$data_array_dtls .= "(" . $id_dtls . "," . $job_update_id . ",'" . $job_no . "','" . $$cboBodyPart . "','" . $$cboColorType . "','" . $$txtFabricDesc . "','" . $$fabricDescId . "','" . $$txtFabricGsm . "','" . $$txtFabricDia . "','" . $$cboDiaWidthType . "','" . $color_id . "','" . $$cboColorRange . "','" . $$txtFinishQty . "','" . $$rmgQty . "','" . $$txtAvgRate . "','" . $$txtAmount . "','" . $$txtProcessLoss . "','" . $$txtGreyQty . "','" . $$cboWorkScope . "','" . $$cboUom . "','" . $$txtRemarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0," . $$booking_qnty_by_uom . "," . $$cboConsUom . ",'" . $$txtProcessId . "','" . $$txtProcessSeq . "','" . $$txtProcessIdMain . "','" . $$txtProcessSeqMain . "'," . $barcode_year . "," . $barcode_suffix_no[2] . "," . $barcode_no . ")";
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
					}else{
						$dtlsId = str_replace("'", '', $$updateIdDtls);
						$id_arr[] = str_replace("'", '', $$updateIdDtls);
						$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("'" . $$cboBodyPart . "'*'" . $$cboColorType . "'*'" . $$txtFabricDesc . "'*'" . $$fabricDescId . "'*'" . $$txtFabricGsm . "'*'" . $$txtFabricDia . "'*'" . $$cboDiaWidthType . "'*'" . $color_id . "'*'" . $$cboColorRange . "'*'" . $$txtFinishQty . "'*'" . $$rmgQty . "'*'" . $$txtAvgRate . "'*'" . $$txtAmount . "'*'" . $$txtProcessLoss . "'*'" . $$txtGreyQty . "'*'" . $$cboWorkScope . "'*'" . $$cboUom . "'*'" . $$txtRemarks . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0*" . $$booking_qnty_by_uom . "*" . $$cboConsUom. "*'" . $$txtProcessId. "'*'" . $$txtProcessSeq . "'*'" . $$txtProcessIdMain . "'*'" . $$txtProcessSeqMain ."'"));
					}
				}
			}

			// ===================== Sub process Start =================================
			$txtProcessSeqSub=chop($$txtProcessSeqSub,"@");
			if ($txtProcessSeqSub!="") 
			{	
				// echo $txtProcessSeqSub.'=<br>';
				$sub_seq_str_arr=explode("@", $txtProcessSeqSub);
				// echo "<pre>"; print_r($sub_seq_str_arr); 
			
				foreach ($sub_seq_str_arr as $key => $value)
				{
					$sub_seq_str_arr2=explode("*", $value);
					// echo "<pre>";print_r($sub_seq_str_arr2);

					$sub_id_sub_seq_str_arr=explode(",", $sub_seq_str_arr2[3]);
					// echo "<pre>";print_r($sub_id_sub_seq_str_arr);				
					foreach ($sub_id_sub_seq_str_arr as $key2 => $val) 
					{
						$subp_id = return_next_id_by_sequence("FABRIC_SALES_ORDER_SUBPROCESS_PK_SEQ", "fabric_sales_order_subprocess", $con);
						// echo $key2.'<br>';
						$val2=explode("_", $val); // 209_1

						//$sub_seq_str_arr2[0].; // all main process id
						if ($data_array_subprocess != "") $data_array_subprocess .= ",";
						$data_array_subprocess .= "(" . $subp_id . "," . $job_update_id . "," . $dtlsId . "," . $sub_seq_str_arr2[0] . "," . $sub_seq_str_arr2[1] . "," . $sub_seq_str_arr2[2] . "," . $val2[0] . "," . $val2[1] . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
					}			
					// echo "<pre>"; print_r($data_array_subprocess).'<br>';	
					// echo $data_array_subprocess;		
				}
			}
			// ===================== Sub process end =================================

			//======================Process wise Rate Entry Starts=====================
			$txtProcessWiseRate=chop(str_replace("'","",$$txtProcessSeqMain));			
			if ($txtProcessWiseRate!="") 
			{
				$txtProcessWiseRateArr =explode("@@", $txtProcessWiseRate);
				$knitProcessRateArr = $txtProcessWiseRateArr[0];
				$dyeProcessRateArr = $txtProcessWiseRateArr[1];
				$aopProcessRateArr = $txtProcessWiseRateArr[2];
				$yarnDyeProcessRateArr = $txtProcessWiseRateArr[3];
				$addiProcessRateArr = $txtProcessWiseRateArr[4];

				$knitProcessRateArrDtls = explode("__",$knitProcessRateArr);
				$dyeProcessRateArrDtls = explode("__",$dyeProcessRateArr);
				$aopProcessRateArrDtls = explode("__",$aopProcessRateArr);
				$yarnDyeProcessRateArrDtls = explode("__",$yarnDyeProcessRateArr);
				$addiProcessRateArrDtls = explode("__",$addiProcessRateArr);

				$KNIT_FABRIC_TYPE = $knitProcessRateArrDtls[0];
				$KNIT_FROM_COUNT = $knitProcessRateArrDtls[1];
				$KNIT_TO_COUNT = $knitProcessRateArrDtls[2];
				$KNIT_CHARGE = $knitProcessRateArrDtls[3]; 

				$DYEING_COLOR_RANGE = $dyeProcessRateArrDtls[0];
				$DYEING_PART = $dyeProcessRateArrDtls[1];
				$DIA_WIDTH_TYPE = $dyeProcessRateArrDtls[2];
				$DYEING_UPTO = $dyeProcessRateArrDtls[3];
				$DYEING_SUBPROCESS = $dyeProcessRateArrDtls[4];
				$DYEING_CHARGE = $dyeProcessRateArrDtls[5];


				$AOP_TYPE = $aopProcessRateArrDtls[0];
				$AOP_NO_OF_COLOR = $aopProcessRateArrDtls[1];
				$AOP_COVERAGE_FROM = $aopProcessRateArrDtls[2];
				$AOP_COVERAGE_TO = $aopProcessRateArrDtls[3];
				$AOP_UPTO = $aopProcessRateArrDtls[4];
				$AOP_SUBPROCESS = $aopProcessRateArrDtls[5];
				$AOP_CHARGE = $aopProcessRateArrDtls[6];


				$YARN_DYEING_PART = $yarnDyeProcessRateArrDtls[0];
				$YARN_COLOR_RANGE = $yarnDyeProcessRateArrDtls[1];
				$YARN_DYEING_SUBPROCESS = $yarnDyeProcessRateArrDtls[2];
				$YARN_DYEING_CHARGE = $yarnDyeProcessRateArrDtls[3];

				$ADDITIONAL_PROCESS = $addiProcessRateArrDtls[0];
				$ADDITIONAL_CHARGE = $addiProcessRateArrDtls[1];


				$field_array_process_wise_rate = "id,mst_id,dtls_id,knit_fabric_type,knit_from_count,knit_to_count,knit_charge,dyeing_part,dia_width_type,dyeing_color_range,dyeing_upto,dyeing_subprocess,dyeing_charge,aop_type,aop_no_of_color,aop_coverage_from,aop_coverage_to,aop_upto,aop_subprocess,aop_charge,yarn_dyeing_part,yarn_color_range,yarn_dyeing_subprocess,yarn_dyeing_charge,additional_process,additional_charge,inserted_by, insert_date, status_active, is_deleted";

				$fpwr_id = return_next_id_by_sequence("fso_process_wise_rate_seq", "fabric_sales_order_prcs_rate", $con);
				if ($data_array_process_wise_rate != "") $data_array_process_wise_rate .= ",";
				$data_array_process_wise_rate .= "(".$fpwr_id.",".$job_update_id . "," . $dtlsId.",'".$KNIT_FABRIC_TYPE."','".$KNIT_FROM_COUNT."','".$KNIT_TO_COUNT."','".$KNIT_CHARGE . "','" . $DYEING_PART."','".$DIA_WIDTH_TYPE."','".$DYEING_COLOR_RANGE."','".$DYEING_UPTO."','".$DYEING_SUBPROCESS."','".$DYEING_CHARGE ."','". $AOP_TYPE."','".$AOP_NO_OF_COLOR."','".$AOP_COVERAGE_FROM."','".$AOP_COVERAGE_TO."','".$AOP_UPTO."','".$AOP_SUBPROCESS."','".$AOP_CHARGE."','". $YARN_DYEING_PART."','".$YARN_COLOR_RANGE."','".$YARN_DYEING_SUBPROCESS."','".$YARN_DYEING_CHARGE."','".$ADDITIONAL_PROCESS."','".$ADDITIONAL_CHARGE . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
			}

			//======================Process wise Rate Entry Ends=====================
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

			//echo "10**".bulk_update_sql_statement("fabric_sales_order_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr);oci_rollback($con);die;
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

		$rID5 = execute_query("delete from fabric_sales_order_subprocess where fso_id=$update_id", 0);
		if ($data_array_subprocess!="") 
		{
			// echo "5**insert into fabric_sales_order_subprocess (".$field_array_subprocess.") values ".$data_array_subprocess;die;
			$rID6 = sql_insert("fabric_sales_order_subprocess", $field_array_subprocess, $data_array_subprocess, 1);
		}

		$rID7=$rID8=1;
		$rID7 = execute_query("delete from fabric_sales_order_prcs_rate where mst_id=$update_id", 0);
		if ($data_array_process_wise_rate!="") 
		{
			echo "10**insert into fabric_sales_order_prcs_rate (".$field_array_process_wise_rate.") values ".$data_array_process_wise_rate;die;
			$rID8 = sql_insert("fabric_sales_order_prcs_rate", $field_array_process_wise_rate, $data_array_process_wise_rate, 1);
		}

		//echo "10**" . $rID . "=" . $rID2 . "=" . $rID3 . "=" . $rID4. ">".$test . "=" . $rID5 . "=" . $rID6. "=" . $rID7 . "=" . $rID8; oci_rollback($con); die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID7 && $rID8) {
				mysql_query("COMMIT");
				echo "1**" . $job_update_id . "**" . $job_no;
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID7 && $rID8) {
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
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_within_group').value + '_' + document.getElementById('cbo_sales_order_type').value, 'create_job_search_list_view', 'search_div', 'fabric_sales_order_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_order_type, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where entry_form=109 and status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $sales_type_cond $search_field_cond order by id desc";
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
	$po_sql="select id, po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)";
	$result_po_sql = sql_select($po_sql);
	foreach ($result_po_sql as $key => $row) 
	{
		$po_no_arr[$row[csf('id')]]=$row[csf('po_number')];
		$int_ref_arr[$row[csf('id')]]=$row[csf('grouping')];

		$po_int_ref_arr[$row[csf('grouping')]]=$row[csf('grouping')];
		$all_po_ids_arr[$row[csf('id')]]=$row[csf('id')];
		// $all_po_ids = explode(",", $row[csf('id')]);
		// echo $row[csf('id')].'<br>';
	}
	// echo "<pre>";print_r($all_po_ids_arr);

	$po_int_ref_arr = array_filter($po_int_ref_arr);
	$int_ref = implode(",", $po_int_ref_arr);
	$po_no_ref="";
	foreach ($all_po_ids_arr as $po_id) 
	{
		// echo $po_id.'<br>';
		if ($po_no_ref == "") $po_no_ref = $po_no_arr[$po_id].':'.$int_ref_arr[$po_id]; else $po_no_ref .= "," . $po_no_arr[$po_id].':'.$int_ref_arr[$po_id];
	}
	// echo $po_no_ref;die('test');

	$data_array = sql_select("SELECT a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode,a.season_id, a.team_leader, a.dealing_marchant, a.remarks, a.currency_id, a.season,a.booking_without_order,a.booking_type,a.booking_approval_date,a.ready_to_approved,a.is_approved,a.booking_entry_form,a.po_job_no, b.fabric_source,b.is_approved booking_is_approved,b.item_category,a.attention,a.fabric_composition,a.sales_order_type,a.customer_buyer from fabric_sales_order_mst a left join wo_booking_mst b on a.booking_id=b.id
	where a.id=$fabric_sales_id group by a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode,a.season_id, a.team_leader, a.dealing_marchant, a.remarks, a.currency_id, a.season,a.booking_without_order,a.booking_type,a.booking_approval_date,a.ready_to_approved,a.is_approved,a.booking_entry_form,a.po_job_no, b.fabric_source,b.is_approved,b.item_category,a.attention,a.fabric_composition,a.sales_order_type,a.customer_buyer");
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

		//echo "load_drop_down( 'requires/fabric_sales_order_entry_controller', ".$row[csf("within_group")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_buyer','buyer_td');\n";

		echo "document.getElementById('is_approved').value 					= '" . $row[csf("is_approved")] . "';\n";
		echo "document.getElementById('txt_job_no').value 					= '" . $row[csf("job_no")] . "';\n";
		echo "document.getElementById('txt_booking_no').value 				= '" . $row[csf("sales_booking_no")] . "';\n";
		echo "document.getElementById('txt_hdn_booking_no').value 			= '" . $row[csf("sales_booking_no")] . "';\n";
		echo "document.getElementById('txt_booking_no_id').value 			= '" . $row[csf("booking_id")] . "';\n";
		echo "document.getElementById('txt_booking_date').value 			= '" . change_date_format($row[csf("booking_date")]) . "';\n";
		echo "document.getElementById('txt_delivery_date').value 			= '" . change_date_format($row[csf("delivery_date")]) . "';\n";
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
		echo "document.getElementById('txt_internal_ref').value 			= '" . $int_ref . "';\n";
		echo "$('#txt_internal_ref').attr('title', '$po_no_ref');\n";
		echo "document.getElementById('cbo_cust_buyer_name').value 			= '" . $row[csf("customer_buyer")] . "';\n";

		//echo "load_drop_down('requires/fabric_sales_order_entry_controller', '".$row[csf("team_leader")]."', 'load_drop_down_dealing_merchant','team_td');\n";
		echo "load_drop_down('requires/fabric_sales_order_entry_controller', " . $row[csf("within_group")] . "+'_'+" . $row[csf("team_leader")] . ", 'load_drop_down_dealing_merchant','team_td');\n";

		echo "document.getElementById('cbo_dealing_merchant').value 		= '" . $row[csf("dealing_marchant")] . "';\n";
		echo "document.getElementById('cbo_ship_mode').value 				= '" . $row[csf("ship_mode")] . "';\n";
		echo "document.getElementById('txt_attention').value 				= '" . $row[csf("attention")] . "';\n";
		echo "document.getElementById('txt_fab_comp').value 				= '" . $row[csf("fabric_composition")] . "';\n";
		echo "document.getElementById('txt_season').value 					= '" . $row[csf("season_id")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('cbo_sales_order_type').value 		= '" . $row[csf("sales_order_type")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_fabric_sales_order_entry',1);\n";
		exit();
	}
}

if ($action == "yarnDetails_popup") {
	echo load_html_head_contents("Yarn Details Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');

	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");

	if($update_id!="")
	{
		//"select a.id from FABRIC_SALES_ORDER_YARN a,FABRIC_SALES_ORDER_YARN_DTLS b,FABRIC_SALES_ORDER_MST c, FABRIC_SALES_ORDER_dtls d where a.id=b.YARN_DTLS_ID and b.mst_id=c.id and c.id=d.mst_id and a.mst_id=3436";

		$sql_allocation=sql_select("select a.id as allocation_id,f.qnty as allocation_qnty 
		from fabric_sales_order_yarn a,fabric_sales_order_yarn_dtls b,
		fabric_sales_order_mst c, fabric_sales_order_dtls d ,
		inv_material_allocation_mst e, inv_material_allocation_dtls f
		where a.id=b.yarn_dtls_id 
		and b.mst_id=c.id and c.id=d.mst_id and b.mst_id=d.mst_id and b.mst_id=a.mst_id 

		and d.job_no_mst=e.job_no and e.id=f.mst_id and e.job_no=f.job_no

		and a.mst_id=$update_id and b.gsm=$txt_Fabric_GsmY and b.deter_id=$fabric_Desc_IdY and a.color_range_id=$cbo_Color_Range  
		and e.is_sales=1 
		and c.entry_form=109 and e.entry_form=475 

		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
		group by a.id,f.qnty");
		//and b.yarn_count_id=248 and b.composition_id=670 
	}
	foreach($sql_allocation as $row)
	{
		$allocation_id=$row[csf("allocation_id")];
	}
	unset($sql_allocation);


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
			var page_link="fabric_sales_order_entry_controller.php?action=composition_popup&inc="+inc;
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
							if($allocation_id!="")
							{
								$disableVar=1;
								$disableVar_input="disabled";
							}
							else
							{
								$disableVar=0;
							}

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
										<? echo create_drop_down("cboYarnCount_" . $i, 75, $yarnCount_arr, "", 1, "- Select -", $cboYarnCount, "", $disableVar, "", "", "", "", "", "", "cboYarnCount[]"); ?>
									</td>
									<td width="150">
										<? //echo create_drop_down("cboComposition_" . $i, 145, $composition, "", 1, "- Select -", $cboComposition, "", "0", "", "", "", "", "", "", "cboComposition[]"); ?>
										<input type="text" id="txtcompone_<? echo $i; ?>"  name="txtcompone_<? echo $i; ?>"  class="text_boxes" style="width:140px" value="<? echo $composition[$cboComposition]; ?>" readonly placeholder="Browse" onDblClick="openmypage_comp(<? echo $i; ?>);"  <? echo $disableVar_input; ?>  />
										<input type="hidden" id="cboComposition_<? echo $i; ?>"  name="cboComposition[]" class="text_boxes" style="width:50px" value="<? echo $cboComposition; ?>"/>

									</td>
									<td width="60">
										<input type="text" name="txtPerc[]" id="txtPerc_<? echo $i; ?>"
										class="text_boxes_numeric" style="width:45px"
										value="<? echo $txtPerc; ?>" readonly  <? echo $disableVar_input; ?>/>
									</td>
									<td width="90">
										<input type="text" name="txtColor[]" id="txtColor_<? echo $i; ?>"
										class="text_boxes" style="width:75px" value="<? echo $txtColor; ?>"  <? echo $disableVar_input; ?>/>
									</td>
									<td width="90">
										<? echo create_drop_down("cboYarnType_" . $i, 85, $yarn_type, "", 1, "- Select -", $cboYarnType, "", $disableVar, "", "", "", "", "", "", "cboYarnType[]"); ?>
									</td>
									<td width="80">
										<input type="text" name="txtConsRatio[]" id="txtConsRatio_<? echo $i; ?>"
										class="text_boxes_numeric" style="width:65px"
										value="<? echo $txtConsRatio; ?>"
										onKeyUp="calculate(<? echo $i; ?>,1);" />
									</td>
									<td width="80">
										<input type="text" name="txtConsQty[]" id="txtConsQty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $txtConsQty; ?>" onKeyUp="calculate(<? echo $i; ?>,2);calculateConsAmount(<? echo $i; ?>);"  />
									</td>

									<td width="80">
										<input type="text" name="txtUnitRate[]" id="txtUnitRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $txtUnitRate; ?>" onKeyUp="calculateConsAmount(<? echo $i; ?>);"  <? echo $disableVar_input; ?>/>
									</td>
									<td width="80">
										<input type="text" name="txtConsAmount[]" id="txtConsAmount_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $txtConsAmount; ?>" readonly  <? echo $disableVar_input; ?>/>
									</td>


									<td width="130">
										<? echo create_drop_down("cboSupplier_" . $i, 125, $supplier_arr, "", 1, "- Select -", $cboSupplier, "", $disableVar, "", "", "", "", "", "", "cboSupplier[]"); ?>
									</td>

									<td width="100">
										<? echo create_drop_down("cboBrand_" . $i, 100, $brand_arr, "", 1, "- Select -", $cboBrand, "", $disableVar, "", "", "", "", "", "", "cboBrand[]"); ?>
									</td>

									<td>
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)" <? echo $disableVar_input; ?>/>
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
										style="width:30px" class="formbuttonplasminus" value="-"
										onClick="fn_deleteRow(<? echo $i; ?>);" <? echo $disableVar_input; ?>/>
									</td>
								</tr>
								<?
								$i++;
							}
						} else {
							?>
							<tr align="center" id="tr_1">
								<td width="80">
									<? echo create_drop_down("cboYarnCount_1", 75, $yarnCount_arr, "", 1, "- Select -", 0, "", $disableVar, "", "", "", "", "", "", "cboYarnCount[]"); ?>
								</td>
								<td width="150">
									<? //echo create_drop_down("cboComposition_1", 145, $composition, "", 1, "- Select -", 0, "", "0", "", "", "", "", "", "", "cboComposition[]"); ?>

									<input type="text" id="txtcompone_1"  name="txtcompone_1"  class="text_boxes" style="width:140px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" <? echo $disableVar_input; ?> />
									<input type="hidden" id="cboComposition_1"  name="cboComposition[]" class="text_boxes" style="width:50px" value="" />



								</td>
								<td width="60">
									<input type="text" name="txtPerc[]" id="txtPerc_1" class="text_boxes_numeric"
									style="width:45px" value="100" readonly <? echo $disableVar_input; ?> />
								</td>
								<td width="90">
									<input type="text" name="txtColor[]" id="txtColor_1" class="text_boxes"
									style="width:75px" <? echo $disableVar_input; ?> />
								</td>
								<td width="90">
									<? echo create_drop_down("cboYarnType_1", 85, $yarn_type, "", 1, "- Select -", 0, "", $disableVar, "", "", "", "", "", "", "cboYarnType[]"); ?>
								</td>
								<td width="80">
									<input type="text" name="txtConsRatio[]" id="txtConsRatio_1"
									class="text_boxes_numeric" style="width:65px" onKeyUp="calculate(1,1);"/>
								</td>
								<td width="80">
									<input type="text" name="txtConsQty[]" id="txtConsQty_1" class="text_boxes_numeric" style="width:65px" onKeyUp="calculate(1,2);calculateConsAmount(1);"/>
								</td>

								<td width="80">
										<input type="text" name="txtUnitRate[]" id="txtUnitRate_1" class="text_boxes_numeric" style="width:65px" value="" onKeyUp="calculateConsAmount(1);" <? echo $disableVar_input; ?> />
								</td>
								<td width="80">
									<input type="text" name="txtConsAmount[]" id="txtConsAmount_1" class="text_boxes_numeric" style="width:65px" value=""  readonly <? echo $disableVar_input; ?> />
								</td>

								<td width="130">
									<? echo create_drop_down("cboSupplier_1", 125, $supplier_arr, "", 1, "- Select -", 0, "", $disableVar, "", "", "", "", "", "", "cboSupplier[]"); ?>
								</td>
								<td width="100">
									<? echo create_drop_down("cboBrand_1", 100, $brand_arr, "", 1, "- Select -", 0, "", $disableVar, "", "", "", "", "", "", "cboBrand[]"); ?>
								</td>

								<td>
									<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" <? echo $disableVar_input; ?> />
									<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" <? echo $disableVar_input; ?> />
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
			<br>
			<?
			$color_grey_arr = sql_select("select color_id, sum(grey_qty) as grey_qty from fabric_sales_order_dtls where mst_id=$update_id and determination_id=$fabric_Desc_IdY and gsm_weight=$txt_Fabric_GsmY and color_range_id=$cbo_Color_Range and status_active=1 and is_deleted=0 group by color_id");
			$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			?>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" >
					<thead>
						<th width="40">Sl.</th>
						<th width="350">Color Name</th>
						<th width="100">Grey Quantity</th>
					</thead>
					<tbody id="color_table">
					<? 
					$i=1;
					foreach ($color_grey_arr as $row) 
					{
						?>
						<tr>
							<td align="center"><? echo $i;?></td>
							<td align="left"><? echo $color_library[$row[csf("color_id")]];?></td>
							<td align="right"><? echo number_format($row[csf("grey_qty")],2);?></td>
						</tr>
						<? 
						$i++;
					} ?>
					</tbody>
				</table>
				<script type="text/javascript">
					setFilterGrid('color_table',-1);
				</script>
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

if ($action == 'yarn_details') 
{
	$composition_arr = array();
	$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id";
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
	$sql_yarn = "SELECT deter_id, gsm, color_range_id, yarn_data from fabric_sales_order_yarn where mst_id='$data' and status_active=1 and is_deleted=0 ";
	$data_array_yarn = sql_select($sql_yarn);
	foreach ($data_array_yarn as $row) {
		//$saveDataArr[$row[csf('deter_id')]][$row[csf('gsm')]] = $row[csf('yarn_data')];
		$saveDataArr[$row[csf('deter_id')]][$row[csf('gsm')]][$row[csf('color_range_id')]] = $row[csf('yarn_data')];
	}

	$sql = "SELECT determination_id, gsm_weight, color_range_id, sum(grey_qty) as grey_qty 
	from fabric_sales_order_dtls 
	where mst_id='$data' and status_active=1 and is_deleted=0 
	group by determination_id, gsm_weight, color_range_id";
	$data_array = sql_select($sql);

	$yarn_sql="SELECT deter_id, gsm, color_range_id, yarn_data from fabric_sales_order_yarn where mst_id=$data and yarn_data is not null and status_active=1 and is_deleted=0";
	// echo $yarn_sql;
	$yarn_sql_data = sql_select($yarn_sql);
	foreach ($yarn_sql_data as $key => $value) 
	{
		$saveYarnDataArr[$value[csf('deter_id')]][$value[csf('gsm')]][$value[csf('color_range_id')]] = $value[csf('yarn_data')];
		// echo $value[csf('deter_id')].'='.$value[csf('gsm')].'='.$value[csf('color_range_id')].'<br>';
	}
	// echo "<pre>";print_r($saveYarnDataArr);
	$i = 0;
	foreach ($data_array as $row) 
	{
		$i++;
		$bgcolor="";
		if($saveYarnDataArr[$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('color_range_id')]] !="")
		{
			$bgcolor="#008000";
		}
		// echo $row[csf('deter_id')].'='.$row[csf('gsm')].'='.$row[csf('color_range_id')].'<br>';
		?>
		<!-- <tr class="general" id="tr_<? //echo $i; ?>"> -->
		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>">	
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
				<?
				$color_range_id = $row[csf('color_range_id')];
					echo create_drop_down("cboColorRangeY_" . $i, 80, $color_range, "", 1, "-- Select --", $color_range_id, "", "1", "", "", "", "", "", "", "cboColorRangeY[]");

				?>
			</td>
			<td>
				<input type="text" name="txtGreyQtyY[]" id="txtGreyQtyY_<? echo $i; ?>" class="text_boxes_numeric"
				style="width:100px" value="<? echo number_format($row[csf('grey_qty')], 4, '.', ''); ?>"
				placeholder="Double Click" onDblClick="openmypage_yarnDetails(<? echo $i; ?>)" readonly/>
				<input type="hidden" name="yarnData[]" id="yarnData_<? echo $i; ?>"
				value="<? echo $saveDataArr[$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('color_range_id')]]; ?>"
				class="text_boxes">
			</td>
		</tr>
		<?
	}
	echo "##" . count($data_array_yarn);
	exit();
}

if ($action == "save_update_delete_yarn") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	if ($operation == 0)  // Insert Here
	{
		$con = connect();

		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$field_array_dtls = "id, mst_id, deter_id, gsm, color_range_id, grey_qty, yarn_data, inserted_by, insert_date";
		$field_array_yarn_dtls = "id,mst_id,yarn_dtls_id,deter_id,gsm,yarn_count_id,composition_id,composition_perc,color_id,yarn_type,cons_ratio,cons_qty,unit_rate,amount,supplier_id,brand_id,inserted_by,insert_date";  

		for ($i = 1; $i <= $total_row; $i++) {
			$fabricDescIdY = "fabricDescIdY" . $i;
			$txtFabricGsmY = "txtFabricGsmY" . $i;
			$cboColorRangeY = "cboColorRangeY" . $i;
			$txtGreyQtyY = "txtGreyQtyY" . $i;
			$yarnData = "yarnData" . $i;
			$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_YARN_PK_SEQ", "fabric_sales_order_yarn", $con);
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $update_id . ",'" . $$fabricDescIdY . "','" . $$txtFabricGsmY . "','" . $$cboColorRangeY . "','" . str_replace(",", '', $$txtGreyQtyY) . "','" . $$yarnData . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

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
							$color_id = return_id_lib_common( str_replace("'","",$txtColor), $color_library, "lib_color", "id,color_name","109");
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
	} 
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$yarn_update_id = str_replace("'", "", $update_id);
		$update_serial = (return_field_value("update_sl", "FABRIC_SALES_ORDER_YARN", "mst_id=$yarn_update_id")) + 1;
		$field_array_dtls = "id, mst_id, deter_id, gsm, color_range_id, grey_qty, yarn_data, inserted_by, insert_date,update_sl";

		$field_array_yarn_dtls = "id,mst_id,yarn_dtls_id,deter_id,gsm,yarn_count_id,composition_id,composition_perc,color_id,yarn_type,cons_ratio,cons_qty,unit_rate,amount,supplier_id,brand_id,inserted_by,insert_date";

		for ($i = 1; $i <= $total_row; $i++) {
			$fabricDescIdY = "fabricDescIdY" . $i;
			$txtFabricGsmY = "txtFabricGsmY" . $i;
			$cboColorRangeY = "cboColorRangeY" . $i;
			$txtGreyQtyY = "txtGreyQtyY" . $i;
			$yarnData = "yarnData" . $i;

			$id_dtls = return_next_id_by_sequence("FABRIC_SALES_ORDER_YARN_PK_SEQ", "fabric_sales_order_yarn", $con);
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $update_id . ",'" . $$fabricDescIdY . "','" . $$txtFabricGsmY . "','" . $$cboColorRangeY . "','" . $$txtGreyQtyY . "','" . $$yarnData . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',$update_serial)";

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
							$color_id = return_id_lib_common( str_replace("'","",$txtColor), $color_library, "lib_color", "id,color_name","109");
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
	$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name ='$data' and item_category_id=95 and variable_list=18 and is_deleted=0 and status_active=1");
	if ($process_loss_method == 2) $process_loss_method = $process_loss_method; else $process_loss_method = 1;

	$color_from_library = return_field_value("color_from_library", "variable_order_tracking", "company_name ='$data' and variable_list=23 and is_deleted=0 and status_active=1");
	if ($color_from_library == 2) $color_from_library = $color_from_library; else $color_from_library = 1;

	$variable_textile_sales_maintain = sql_select("select production_entry, process_loss_editable from variable_settings_production where company_name=$data and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2 && $variable_textile_sales_maintain[0][csf('process_loss_editable')] ==1) $process_loss_editable_maintain = 1; else $process_loss_editable_maintain = 0;

	echo "document.getElementById('process_loss_method').value 				= '" . $process_loss_method . "';\n";
	echo "document.getElementById('color_from_library').value 				= '" . $color_from_library . "';\n";
	echo "document.getElementById('textile_sales_maintain').value 			= '" . $textile_sales_maintain . "';\n";
	echo "document.getElementById('process_loss_editable_maintain').value 			= '" . $process_loss_editable_maintain . "';\n";

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

if ($action == 'show_change_bookings') 
{
	$company_id=$data;

	//$sql = "select id, company_id, job_no, sales_booking_no,booking_id,is_master_part_updated,booking_without_order from fabric_sales_order_mst where status_active=1 and is_deleted=0 and within_group=1 and is_apply_last_update=2";

	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2)
	{
		$textile_sales_maintain = 1;
		$comp_cond = " and b.company_id = $company_id";
		$fabric_source_cond = " and b.fabric_source=1";
		$fabric_source_cond2 = " and (b.fabric_source in (1) or c.fabric_source in(1) )";
	}else{
		$textile_sales_maintain = 0;
	}
	
	$sql = "select a.id, a.company_id, a.job_no, a.sales_booking_no,a.booking_id,a.is_master_part_updated,a.booking_without_order 
	from fabric_sales_order_mst a, wo_booking_mst b
	where a.sales_booking_no=b.booking_no $comp_cond $fabric_source_cond and a.status_active=1 and a.is_deleted=0 and a.within_group=1 and a.is_apply_last_update=2
	union all
	select a.id, a.company_id, a.job_no, a.sales_booking_no,a.booking_id,a.is_master_part_updated,a.booking_without_order 
	from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c
	where a.sales_booking_no=b.booking_no and b.booking_no=c.booking_no $comp_cond $fabric_source_cond2 and a.status_active=1 and a.is_deleted=0 and a.within_group=1 and a.is_apply_last_update=2
	group by a.id, a.company_id, a.job_no, a.sales_booking_no,a.booking_id,a.is_master_part_updated,a.booking_without_order";


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

// Pending Bookings List
if ($action == 'show_change_pending_bookings') 
{
	$company_id=$data;
	$buyer_id=0;
	if($buyer_id==0) $buyer_id_cond= $buyer_id_cond; else $buyer_id_cond=" and a.buyer_id=$buyer_id";
	if($db_type==0)
	{
		$po_break_down_id_cond=	"CAST(a.po_break_down_id as CHAR(4000)) as po_break_down_id,";
	}
	else
	{
		$po_break_down_id_cond=	"cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id,";
	}

	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");
	$apporved_date_arr = return_library_array("select mst_id,max(approved_date) as approved_date from approval_history where current_approval_status=1 group by mst_id", 'mst_id', 'approved_date');

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2)
	{
		$textile_sales_maintain = 1;
		$comp_cond = " and a.company_id = $company_id";
		$fabric_source_cond = " and a.fabric_source=1";
		$fabric_source_cond2 = " and (a.fabric_source in (1)  or b.fabric_source in(1) )";
	}else{
		$textile_sales_maintain = 0;
	}


	// echo $buyer_id_cond;die;

	/*$sql = "SELECT a.id, a.booking_no, a.booking_date, 0 as booking_without_order
	FROM wo_booking_mst a, wo_booking_dtls c
	WHERE a.booking_no=c.booking_no and a.pay_mode=5 and a.fabric_source in (1,2,4) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.item_category=2  $buyer_id_cond $comp_cond $fabric_source_cond
	and c.status_active=1 and c.is_deleted=0 and a.booking_year >2020 group by a.id, a.booking_no, a.booking_date
	union all
	select a.id, a.booking_no, a.booking_date, 1 as booking_without_order 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.pay_mode=5 and (a.fabric_source in (1,2,4) or b.fabric_source in(1,2,4) ) and a.supplier_id=$company_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2  $buyer_id_cond $comp_cond $fabric_source_cond2 and to_char(a.booking_date,'YYYY') >='2021' group by a.id, a.booking_no, a.booking_date";*/

	$present_year = date("Y");
	$previous_year = $present_year-1;
	$next_year = $present_year+1;
	
	$sql = "SELECT a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_cond b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise as season,a.remarks,a.attention,c.copmposition as composition,0 as booking_without_order
	FROM wo_booking_mst a, wo_po_details_master b, wo_booking_dtls c
	WHERE a.booking_no=c.booking_no and c.job_no = b.job_no and a.pay_mode=5 and a.fabric_source in (1,2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.booking_year >$previous_year

	$buyer_id_cond $comp_cond $date_cond $fabric_source_cond 

	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.entry_form, a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_buyer_wise,a.remarks,a.attention,c.copmposition 
	union all
	select a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, null as entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_cond null as job_no,null as style_ref_no,null as team_leader,null as dealing_marchant, null as season,null as remarks,a.attention,b.composition,1 as booking_without_order from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.pay_mode=5 and (a.fabric_source in (1,2,4)  or b.fabric_source in(1,2,4) ) and a.status_active=1 and a.is_deleted =0 and a.item_category=2 and to_char(a.booking_date,'YYYY') >='$present_year' 

	$buyer_id_cond $comp_cond $date_cond $fabric_source_cond2 

	group by a.booking_no_prefix_num,a.id, a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, a.po_break_down_id,a.attention,b.composition";

	//echo $sql;die;
	$result = sql_select($sql);
	$booking_id_arr=$po_id_arr=array();$bookingID="";$poID="";
	foreach ($result as $row) {
		$booking_id_arr[] = $row[csf("id")];

		$style_ref_no_arr[$row[csf("booking_no")]][$row[csf("style_ref_no")]] = $row[csf("style_ref_no")];

		$bookingID.=$row[csf("id")].",";

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
	// echo "<pre>"; print_r($sales_booking);die;

	$smn_booking="select distinct a.style_ref_no,b.booking_no from sample_development_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.id=c.style_id and b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and a.style_ref_no is not null $bookingID_cond2";
	$result_smn_booking = sql_select($smn_booking);
	foreach ($result_smn_booking as $row)
	{
		$booking_style_arr[$row[csf('booking_no')]][$row[csf('style_ref_no')]] = $row[csf('style_ref_no')];
	}

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="200">
		<thead>
			<th width="40" align="center">SL No</th>
			<th width="90">Booking No.</th>
			<th>Date</th>
		</thead>
	</table>
	<div style="width:200px; max-height:130px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="182" class="rpt_table" id="tbl_list_search_pending_booking">
			<?
			$i = 1;
			$result = sql_select($sql);
			foreach ($result as $row) 
			{
				if ($sales_booking[$row[csf("id")]][$row[csf('booking_without_order')]]=="") 
				{
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
						$row[csf('style_ref_no')]=implode(',', $booking_style_arr[$row[csf('booking_no')]]);
					}
					else
					{
						//$row[csf('style_ref_no')]=$row[csf('style_ref_no')];
						$row[csf('style_ref_no')]=implode(',', $style_ref_no_arr[$row[csf('booking_no')]]);
					}
			
					$data = $row[csf('id')] . '__' . $row[csf('booking_no')] . '__' . $row[csf('company_id')] . '__' . $row[csf('style_ref_no')] . '__' . change_date_format($row[csf('delivery_date')]) . '__' . $row[csf('currency_id')] . '__' . $row[csf('season')] . '__' . $row[csf('team_leader')] . '__' . $row[csf('dealing_marchant')] . '__' . $row[csf('remarks')] . '__' . $row[csf('is_approved')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('is_short')] . '__' . $row[csf('fabric_source')] . '__' . $row[csf('item_category')] . '__' . $row[csf('job_no')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('po_break_down_id')] . '__' . $row[csf('entry_form')] . '__' . $row[csf('booking_without_order')] . '__' . change_date_format($apporved_date_arr[$row[csf('id')]]). '__' . $row[csf('attention')] . '__' . $row[csf('composition')];


					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="pending_booking_data_dtls('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="90" title="booking_id: <? echo $row[csf("id")]; ?>"><? echo $row[csf('booking_no')]; ?></td>
						<td align="right"><? echo change_date_format($row[csf('booking_date')]); ?></td>
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
			$partial_sql = "SELECT a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date,a.entry_form, a.booking_type, a.is_short, a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, $po_break_down_id_str, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix as season,a.remarks,0 as booking_without_order 
			FROM wo_booking_mst a, wo_po_details_master b,wo_booking_dtls d 
			WHERE a.booking_no=d.booking_no and d.job_no=b.job_no and a.pay_mode=5 and a.fabric_source in (1,2) and a.supplier_id=$company_id and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.entry_form=108 $buyer_id_cond and a.id not in(select c.booking_id  from fabric_sales_order_mst c where a.id=c.booking_id) and b.status_active =1 and b.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and to_char(a.booking_date,'YYYY') >2020
			group by a.booking_no_prefix_num,a.id, d.booking_no, a.booking_date, a.entry_form,a.booking_type, a.is_short,a.company_id, a.fabric_source, a.item_category, a.buyer_id, a.delivery_date, a.currency_id, a.is_approved, b.job_no, b.style_ref_no, b.team_leader, b.dealing_marchant, b.season_matrix,a.remarks order by a.booking_date asc";
			// echo $partial_sql;die;
			$partial_result = sql_select($partial_sql);
			$style_ref_no_arr=array();
			foreach ($partial_result as $row)
			{
				$style_ref_no_arr[$row[csf("booking_no")]][$row[csf("style_ref_no")]] = $row[csf("style_ref_no")];
			}
			foreach ($partial_result as $row) 
			{
				if (!in_array($row[csf('id')], $import_booking_id_arr))
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					//for multiple job_no in a booking_no
					$row[csf('style_ref_no')]=implode(',', $style_ref_no_arr[$row[csf('booking_no')]]);
				
					$data = $row[csf('id')] . '__' . $row[csf('booking_no')] . '__' . $row[csf('company_id')] . '__' . $row[csf('style_ref_no')] . '__' . change_date_format($row[csf('delivery_date')]) . '__' . $row[csf('currency_id')] . '__' . $row[csf('season')] . '__' . $row[csf('team_leader')] . '__' . $row[csf('dealing_marchant')] . '__' . $row[csf('remarks')] . '__' . $row[csf('is_approved')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('is_short')] . '__' . $row[csf('fabric_source')] . '__' . $row[csf('item_category')] . '__' . $row[csf('job_no')] . '__' . $row[csf('booking_type')] . '__' . $row[csf('po_break_down_id')] . '__' . $row[csf('entry_form')] . '__' . $row[csf('booking_without_order')] . '__' . change_date_format($apporved_date_arr[$row[csf('id')]]);

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="pending_booking_data_dtls('<? echo $data; ?>','<? echo $row[csf('is_approved')]; ?>')">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="90"><? echo $row[csf('booking_no')]; ?></td>
						<td align="right"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="auto_mail_send")
{

	list($booking_no,$email_add,$mail_body)=explode('__seperate__',$data);

	$dealing_mar_arr = return_library_array("select id,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where status_active =1 and is_deleted=0", 'id', 'TEAM_MEMBER_EMAIL');
	$team_leader_arr = return_library_array("select id,TEAM_LEADER_EMAIL from lib_marketing_team where status_active =1 and is_deleted=0", 'id', 'TEAM_LEADER_EMAIL');
	$factory_mar_arr = return_library_array("select id,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where status_active =1 and is_deleted=0", 'id', 'TEAM_MEMBER_EMAIL');
	
	
	$sql="select a.BOOKING_NO,b.TEAM_LEADER,b.DEALING_MARCHANT,b.FACTORY_MARCHANT from WO_BOOKING_DTLS a,WO_PO_DETAILS_MASTER b where a.JOB_NO=b.JOB_NO and a.BOOKING_NO='$booking_no' and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 group by a.BOOKING_NO,b.TEAM_LEADER,b.DEALING_MARCHANT,b.FACTORY_MARCHANT";
	$sql_result = sql_select($sql);
	$mailArr=array();
	$mailArr[]=$email_add;
	foreach($sql_result as $rows){
		$TEAM_LEADER_EMAIL=$team_leader_arr[$rows[TEAM_LEADER]];
		$DEALING_MARCHANT_EMAIL=$dealing_mar_arr[$rows[DEALING_MARCHANT]];
		$FACTORY_MARCHANT_EMAIL=$factory_mar_arr[$rows[FACTORY_MARCHANT]];
		
		if($TEAM_LEADER_EMAIL)$mailArr[$TEAM_LEADER_EMAIL]=$TEAM_LEADER_EMAIL;
		if($DEALING_MARCHANT_EMAIL)$mailArr[$DEALING_MARCHANT_EMAIL]=$DEALING_MARCHANT_EMAIL;
		if($FACTORY_MARCHANT_EMAIL)$mailArr[$FACTORY_MARCHANT_EMAIL]=$FACTORY_MARCHANT_EMAIL;
		
	}
	
 	
	//Mail send------------------------------------------
		require_once('../../mailer/class.phpmailer.php');
		require_once('../../auto_mail/setting/mail_setting.php');
	 
		$to=implode(',',$mailArr);
		
		 //echo  $mail_body;die;
		
		$subject="Fabric Sales Order Notification";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mail_body, $from_mail );
		
	//------------------------------------End;
	
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
		$style_description = "b.style_des as style_description";
		$group_concat = "group_concat(e.po_break_down_id) as po_break_down_id";
		
	}
	else
	{
		$fabric_composition = "cast(a.fabric_composition as nvarchar2(200)) as fabric_composition";
		$po_break_down_id = "cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id";
		$style_description = "CAST (b.style_des AS NVARCHAR2 (200)) AS style_description";
		$group_concat = "listagg(e.po_break_down_id, ',') within group (order by e.po_break_down_id) as po_break_down_id";
	}
	$sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id, $fabric_composition, a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.booking_approval_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp, 0 as booking_without_order FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.booking_approval_date,d.attention,d.fabric_composition
	union all
	select a.attention,null as job_no,null as order_repeat_no,c.id as sales_id, b.composition as fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, $style_description,a.dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id, c.style_ref_no,null as team_leader,a.dealing_marchant,null as season_matrix, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention as sales_attention,c.fabric_composition as sales_fab_comp, 1 as booking_without_order from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.attention,a.dealing_marchant,c.id,b.composition, b.style_des,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention,c.fabric_composition, c.style_ref_no";


	$partial_sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $group_concat,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp FROM wo_booking_mst a, wo_po_details_master b,	wo_po_break_down c,	fabric_sales_order_mst d, wo_booking_dtls e
	WHERE
	a.booking_no=e.booking_no and e.po_break_down_id=c.id and a.id=d.booking_id and e.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' and a.entry_form in(108,271) 
	group by a.attention,b.job_no,b.order_repeat_no,d.id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id,b.style_description,b.dealing_marchant,d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention,d.fabric_composition";

	$mst_data = sql_select($sql);
	$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
	extract($mst_data);
	// echo $booking_no.'==';
	// echo "<br>"; print_r($mst_data);die;

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
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');
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
			<td><? echo ($booking_without_order==0) ? $dealing_marArr[$dealing_marchant] : $dealing_marArr[$dealing_marchant]; //echo $dealing_marArr[$dealing_marchant]; ?></td>
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

		<div style="float: left; margin-right: 20px;">
			<strong style="font: bold 12px tahoma;">Color Range Wise Grey Quantity</strong>
			<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
				<tr bgcolor="#CCCCCC">
					<td align="center"><strong>Fabric Description</strong></td>
					<td align="center"><strong>Fabric GSM</strong></td>
					<td align="center"><strong>Color Range</strong></td>
					<td align="center"><strong> Grey Quantity</strong></td>
				</tr>
				<?
				$composition_arr = array();
				$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id";
				$y_data_array = sql_select($sql_deter);
				if (count($y_data_array) > 0) {
					foreach ($y_data_array as $row) {
						if (array_key_exists($row[csf('id')], $composition_arr)) {
							$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
						} else {
							$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
						}
					}
				}

				$sql = "SELECT determination_id, gsm_weight, color_range_id, sum(grey_qty) as grey_qty 
				from fabric_sales_order_dtls 
				where mst_id='$sales_id' and status_active=1 and is_deleted=0 
				group by determination_id, gsm_weight, color_range_id";
				// echo $sql;
				$data_array = sql_select($sql);

				$i = 1;
				foreach ($data_array as $rows) 
				{
					?>
					<tr>
						<td><p><? echo $composition_arr[$rows[csf('determination_id')]]; ?></p></td>
						<td><p><? echo $rows[csf('gsm_weight')]; ?></p></td>
						<td><p><? echo $color_range[$rows[csf('color_range_id')]]; ?></p></td>
						<td align="right"><p><? echo number_format($rows[csf('grey_qty')], 4, '.', ''); ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
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

if ($action == "fabric_sales_order_print2") 
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

	$sql = "SELECT d.id as sales_id,d.currency_id,d.delivery_date,d.within_group,d.job_no, d.buyer_id,d.booking_date,
	d.sales_booking_no,d.company_id,d.delivery_date,d.dealing_marchant,d.remarks,d.style_ref_no,d.season,d.attention,d.fabric_composition from fabric_sales_order_mst d
	where d.status_active =1 and d.is_deleted =0 and d.job_no='$salesOrderNo'";
	if ($sales_id == '') {
		$mst_data = sql_select($sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}

	$image_locationArr2 = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');

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
		$dtls_sql = "SELECT b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.work_scope, sum(b.grey_qty) grey_qty,sum(b.rmg_qty) as rmg_qty, b.pre_cost_fabric_cost_dtls_id,a.within_group,b.process_id
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

if ($action == 'fabric_sales_order_print_yes_6') 
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
		$style_description = "b.style_des as style_description";
		$group_concat = "group_concat(e.po_break_down_id) as po_break_down_id";
		
	}
	else
	{
		$fabric_composition = "cast(a.fabric_composition as nvarchar2(200)) as fabric_composition";
		$po_break_down_id = "cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id";
		$style_description = "CAST (b.style_des AS NVARCHAR2 (200)) AS style_description";
		$group_concat = "listagg(e.po_break_down_id, ',') within group (order by e.po_break_down_id) as po_break_down_id";
	}
	$sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id, $fabric_composition, a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.booking_approval_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp, 0 as booking_without_order FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.booking_approval_date,d.attention,d.fabric_composition
	union all
	select a.attention,null as job_no,null as order_repeat_no,c.id as sales_id, b.composition as fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, $style_description,a.dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id, c.style_ref_no,null as team_leader,a.dealing_marchant,null as season_matrix, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention as sales_attention,c.fabric_composition as sales_fab_comp, 1 as booking_without_order from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.attention,a.dealing_marchant,c.id,b.composition, b.style_des,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention,c.fabric_composition, c.style_ref_no";


	$partial_sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $group_concat,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp FROM wo_booking_mst a, wo_po_details_master b,	wo_po_break_down c,	fabric_sales_order_mst d, wo_booking_dtls e
	WHERE
	a.booking_no=e.booking_no and e.po_break_down_id=c.id and a.id=d.booking_id and e.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' and a.entry_form in(108,271) 
	group by a.attention,b.job_no,b.order_repeat_no,d.id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id,b.style_description,b.dealing_marchant,d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention,d.fabric_composition";

	$mst_data = sql_select($sql);


	$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
	extract($mst_data);
	// echo $booking_no.'==';
	// echo "<br>"; print_r($mst_data);die;

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
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');
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
		<td width="135" rowspan="2"><strong>Buyer/Agent Name</strong></td>
		<td  rowspan="2"><? echo $buyerArr[$buyer_name]; ?></td>
		<td width="135"><strong>Dept.</strong></td>
		<td><? echo $departmentArr[$product_dept]; ?></td>
		<td width="135"><strong>Garments Item</strong></td>
		<td><? echo $item_string; ?></td>
		<td><strong>Sales Order No: <? echo $salesOrderNo; ?></strong></td>
	</tr>
	<tr>
		<td><strong>Season</strong></td>
		<td><? echo $season_arr[$season]; ?></td>
		<td><strong>Order Qnty</strong></td>
		<td><? echo $po_quantity; ?></td>
		<td rowspan="10" valign="top" width="205"><? foreach ($image_locationArr as $path) { ?><img
			src="../../<? echo $path; ?>" height="100" width="100"><? } ?></td>
		</tr>
		<tr>
			<td><strong>Style Ref.</strong></td>
			<td><? echo $style_ref_no; ?></td>
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
			<td><? echo ($booking_without_order==0) ? $dealing_marArr[$dealing_marchant] : $dealing_marArr[$dealing_marchant]; //echo $dealing_marArr[$dealing_marchant]; ?></td>
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

	$stl_des_sql = "SELECT id, booking_no, body_part, color_type_id, fabric_description, fabric_color, gsm_weight, dia_width, style_des from wo_non_ord_samp_booking_dtls where booking_no ='$booking_no' and status_active=1 and is_deleted=0 group by id, booking_no, body_part, color_type_id, fabric_description, fabric_color, gsm_weight, dia_width, style_des order by fabric_color";
	//echo $stl_des_sql;
	$stl_des_result = sql_select($stl_des_sql);
	$style_des_arr = array();
	foreach ($stl_des_result as $rows) 
	{
		$stl_key = $rows[csf('booking_no')] . '_' . $rows[csf('body_part')] . '_' . $rows[csf('color_type_id')] . '_' . $rows[csf('fabric_color')]  . '_' . $rows[csf('gsm_weight')]. '_' . $rows[csf('dia_width')];

		$style_des_arr[$stl_key]['style_des'] =$rows[csf('style_des')];
	}
	//var_dump($style_des_arr);
	//$dtls_sql = "select b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.grey_qty,sum(b.rmg_qty) as rmg_qty, c.item_number_id,b.pre_cost_fabric_cost_dtls_id,b.process_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_pre_cost_fabric_cost_dtls c where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.id=b.mst_id and b.pre_cost_fabric_cost_dtls_id=c.id and a.status_active=1 and b.is_deleted=0 group by b.body_part_id,b.color_type_id,b.fabric_desc, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.grey_qty,b.color_id,b.pre_cost_remarks, c.item_number_id, b.pre_cost_fabric_cost_dtls_id,b.process_id order by b.body_part_id";
	$dtls_sql = "select a.sales_booking_no,b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.grey_qty,sum(b.rmg_qty) as rmg_qty, c.item_number_id,b.pre_cost_fabric_cost_dtls_id,b.process_id from fabric_sales_order_mst a,fabric_sales_order_dtls b
	left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id
	where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 group by b.body_part_id,b.color_type_id,b.fabric_desc, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.grey_qty,b.color_id,b.pre_cost_remarks, c.item_number_id, b.pre_cost_fabric_cost_dtls_id,b.process_id, a.sales_booking_no order by b.color_id";
	//echo $dtls_sql;
	$dtls_sql_data = sql_select($dtls_sql);
	$style_des_data = array();
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
		
		$style_key = $rows[csf('sales_booking_no')] . '_' . $rows[csf('body_part_id')] . '_' . $rows[csf('color_type_id')]  . '_' . $rows[csf('color_id')]  . '_' . $rows[csf('gsm_weight')]. '_' . $rows[csf('dia')];

		
		$style_des_data[$key] = $style_des_arr[$style_key]['style_des'];

	}

	//var_dump($style_des_data);

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
			<td colspan="3"><strong>Style Description</strong></td>
			<? foreach ($style_des_data as $val) {
				echo '<td colspan="3" align="center">' . $val . '</td>';
			} ?>
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

if ($action == 'fabric_sales_order_print_kds2')  // Print 4 // KDS 2
{
	include('../../includes/class4/class.conditions.php');
	include('../../includes/class4/class.reports.php');
	include('../../includes/class4/class.yarns.php');

	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption, $withinGroup) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$departmentArr = return_library_array("select id,department_name from lib_department", 'id', 'department_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');
	$user_name_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$update_serial = sql_select("SELECT a.id, a.update_sl, a.booking_approval_date, a.insert_date, a.inserted_by, a.sales_order_type, a.is_approved, a.approved_date, a.revise_no, a.ready_to_approved, a.delivery_date from fabric_sales_order_mst a where a.sales_booking_no = '" . $bookingNo . "'");

	if ($withinGroup==1) 
	{
		if ($db_type == 0)
		{
			$fabric_composition = "a.fabric_composition as fabric_composition";
			$po_break_down_id = "a.po_break_down_id as po_break_down_id";
			$style_description = "b.style_des as style_description";
			$group_concat = "group_concat(e.po_break_down_id) as po_break_down_id";	
		}
		else
		{
			$fabric_composition = "cast(a.fabric_composition as nvarchar2(200)) as fabric_composition";
			$po_break_down_id = "cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id";
			$style_description = "CAST (b.style_des AS NVARCHAR2 (200)) AS style_description";
			$group_concat = "listagg(e.po_break_down_id, ',') within group (order by e.po_break_down_id) as po_break_down_id";
		}

		$sql = "SELECT a.booking_type, a.is_short, a.booking_percent, a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id, $fabric_composition, a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, d.season as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.booking_approval_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp, 0 as booking_without_order
		FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d 
		WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' 
		group by a.booking_type, a.is_short, a.booking_percent, a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, d.season, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.booking_approval_date,d.attention,d.fabric_composition
		union all
		select a.booking_type, a.is_short, 0 as booking_percent, a.attention,null as job_no,null as order_repeat_no,c.id as sales_id, b.composition as fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, $po_break_down_id, $style_description,a.dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id, c.style_ref_no,null as team_leader,a.dealing_marchant, c.season, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention as sales_attention,c.fabric_composition as sales_fab_comp, 1 as booking_without_order 
		from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no 
		where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 
		group by a.booking_type, a.is_short, a.attention,a.dealing_marchant,c.id,b.composition, b.style_des,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date,c.booking_approval_date,c.attention,c.fabric_composition, c.season, c.style_ref_no";


		$partial_sql = "SELECT a.booking_type, a.is_short, a.booking_percent, a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, $group_concat,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, d.season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp
		FROM wo_booking_mst a, wo_po_details_master b,	wo_po_break_down c,	fabric_sales_order_mst d, wo_booking_dtls e
		WHERE a.booking_no=e.booking_no and e.po_break_down_id=c.id and a.id=d.booking_id and e.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' and a.entry_form in(108,271) 
		group by a.booking_type, a.is_short, a.booking_percent, a.attention,b.job_no,b.order_repeat_no,d.id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id,b.style_description,b.dealing_marchant,d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, d.season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention,d.fabric_composition";
		//b.season_matrix as season,
	}
	else
	{
		$sql = "SELECT d.id as sales_id,d.currency_id,d.delivery_date,d.within_group,d.job_no, d.buyer_id,d.booking_date, d.sales_booking_no,d.company_id,d.delivery_date,d.dealing_marchant,d.remarks,d.style_ref_no,d.season,d.attention,d.fabric_composition from fabric_sales_order_mst d
		where d.status_active =1 and d.is_deleted =0 and d.job_no='$salesOrderNo'";
	}
	//echo $sql;
	$mst_data = sql_select($sql);
	$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
	extract($mst_data);
	// echo $booking_no.'==';
	// echo "<br>"; print_r($mst_data);die;

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
	} //echo "<br>"; print_r($mst_data);die;
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
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');
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

	$booking_type_name="";
	if($booking_type==1 && $is_short==2)
	{
		$booking_type_name="Main";
	}
	else if($booking_type==1 && $is_short==1)
	{
		$booking_type_name="Short";
	}
	else if($booking_type==4)
	{
		$booking_type_name="Sample";
	}

	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0" style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
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

	<div style="margin-right: 80px; float: left;">
		<table width="100%" cellspacing="0" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
			<tr>
				<td width="135">
					<img src='../../<? echo $imge_arr[$companyId]; ?>' height='100%' width='100%'/>
				</td>
			</tr>
		</table>
	</div>

	<div style="margin-bottom: 2px; float: right;">
		<table width="100%" cellpadding="4" cellspacing="4" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
			<tr class="header">
				<td width="150"><strong>Sales Order Type</strong></td>
				<td width="160"><? echo $sales_order_type_arr[$update_serial[0][csf('sales_order_type')]]; ?></td>
			</td>
			</tr>
			<tr>
				<td width="150"><strong>FSO Status</strong></td>
				<td width="160"><? if ($update_serial[0][csf('is_approved')]==1) 
				{
					echo "<span style='color:red;'>Approved</span>";
				}
				else if ($update_serial[0][csf('is_approved')]==3) 
				{
					echo "<span style='color:red;'>Partial Approved</span>";
				}
				?></td>
			</tr>
			<tr>
				<td width="150"><strong>Approved Date & Time</strong></td>
				<td width="160"><? echo $update_serial[0][csf('approved_date')]; ?></td>
			</tr>
			<tr>
				<td width="150"><strong>FSO Received By</strong></td>
				<td width="160">
				<? echo $user_name_arr[$update_serial[0][csf('inserted_by')]]; ?>
				</td>
			</tr>
			<tr>
				<td width="150"><strong>FSO Rvc. Date & Time</strong></td>
				<td width="160">
				<? echo change_date_format($update_serial[0][csf('insert_date')]); ?>
				</td>
			</tr>
			<!-- <tr>
				<td width="150"><strong>Revised No</strong></td>
				<td width="160">
				<? echo ($update_serial[0][csf("revise_no")] > 0)?$update_serial[0][csf("revise_no")]:""; ?>
				</td>
			</tr> -->
		</table>
	</div>

	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td width="135"><strong>Buyer/Agent Name</strong></td>
			<td width="150"><? echo $buyerArr[$buyer_name]; ?></td>
			<td width="135"><strong>W/G</strong></td>
			<td width="150"><? echo $yes_no[$withinGroup]; ?></td>
			<td width="135"><strong>Garments Item</strong></td>
			<td width="200"><? echo $item_string; ?></td>
			<td width="130"><strong>Sales Order No: <? echo $salesOrderNo; ?></strong></td>
		</tr>
		<tr>
			<td><strong>Style Ref.</strong></td>
			<td><? echo $style_ref_no; ?></td>
			<td><strong>Season</strong></td>
			<td><? echo $season_arr[$season].$season; ?></td>
			<td><strong>Order Qnty</strong></td>
			<td><? echo $po_quantity; ?></td>
			<td rowspan="10" valign="top" width="205"><? foreach ($image_locationArr as $path) { ?><img
				src="../../<? echo $path; ?>" height="100%" width="100%"><? } ?></td>
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
			<td style="overflow:hidden;text-overflow: ellipsis;word-break: break-all;"><? echo rtrim($po_no,','); ?></td>
			<td><strong>Booking Type</strong></td>
			<td><? echo $booking_type_name; ?></td>
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
			<td><? echo ($booking_without_order==0) ? $dealing_marArr[$dealing_marchant] : $dealing_marArr[$dealing_marchant]; //echo $dealing_marArr[$dealing_marchant]; ?></td>
		</tr>
		<tr>
			<td><strong>Currency</strong></td>
			<td><? echo $currency[$currency_id]; ?></td>
			<td><strong>Quality Label</strong></td>
			<td><? echo $row[csf('')]; ?></td>
			<td><strong>Party/Unit</strong></td>
			<td><? echo $companyArr[$style_owner]; ?></td>
		</tr>
		<tr>
			<td><strong>Dep.</strong></td>
			<td><? echo $departmentArr[$product_dept];; ?></td>
			<td><strong>Ready To Approved</strong></td>
			<td><? echo $yes_no[$update_serial[0][csf("ready_to_approved")]];  ?></td>
			<td><strong>Delivery Date</strong></td>
			<td><? echo change_date_format($update_serial[0][csf("delivery_date")]); ?></td>
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
			<td><strong>Attention</strong></td>
			<td colspan="3"><? echo $sales_attention; ?></td>
			<td><strong>Receive Date</strong></td>
			<td><? echo change_date_format($update_serial[0][csf("booking_approval_date")]); ?></td>
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
	<br>
	
	<?
	$dtls_sql = "SELECT b.width_dia_type,b.pre_cost_remarks,b.body_part_id, b.color_type_id,b.fabric_desc,b.gsm_weight, b.dia, b.color_id, sum(b.finish_qty) finish_qty, b.process_loss, b.grey_qty,sum(b.rmg_qty) as rmg_qty, c.item_number_id,b.pre_cost_fabric_cost_dtls_id,b.process_id, b.process_id_main
	from fabric_sales_order_mst a,fabric_sales_order_dtls b
	left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id
	where a.id=$sales_id and a.sales_booking_no='$bookingNo' and a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 
	group by b.body_part_id,b.color_type_id,b.fabric_desc, b.gsm_weight,b.width_dia_type, b.dia, b.process_loss, b.grey_qty,b.color_id,b.pre_cost_remarks, c.item_number_id, b.pre_cost_fabric_cost_dtls_id,b.process_id, b.process_id_main 
	order by b.body_part_id";
	// echo $dtls_sql;die;
	$dtls_sql_data = sql_select($dtls_sql);
	foreach ($dtls_sql_data as $rows)
	{
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

		if($rows[csf('process_id_main')]!=""){$main_process_count+=1;}
		$main_process_name = '';
		$main_process_id_array = explode(",", $rows[csf("process_id_main")]);
		foreach ($main_process_id_array as $val) {
			if ($main_process_name == "") $main_process_name = $conversion_cost_head_array[$val]; else $main_process_name .= "," . $conversion_cost_head_array[$val];
			$main_process_name_arr[ $conversion_cost_head_array[$val]] =  $conversion_cost_head_array[$val];
		}
		//$main_process_name_arr[$key] = $main_process_name;
	}

	$sub_process_sql="SELECT main_process_id, sub_process_id from fabric_sales_order_subprocess where fso_id=$sales_id";
	$sub_process_data = sql_select($sub_process_sql);
	foreach ($sub_process_data as $key => $rows) 
	{
		if($rows[csf('sub_process_id')]!=""){$sub_process_count+=1;}
		$sub_process_name = '';
		$main_process_id_array = explode(",", $rows[csf("sub_process_id")]);
		foreach ($main_process_id_array as $val) {
			if ($sub_process_name == "") $sub_process_name = $conversion_cost_head_array[$val]; else $sub_process_name .= "," . $conversion_cost_head_array[$val];
		}
		$sub_process_name_arr[$rows[csf("main_process_id")]] .= $sub_process_name.',';
		$main_process_arr[$rows[csf("main_process_id")]] = $rows[csf("main_process_id")];
	}
	// echo "<pre>";print_r($sub_process_name_arr);die;

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
	foreach ($gmt_color_data as $gmt_color_row) 
	{
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
			<? foreach ($body_part_data as $key_ids => $val) 
			{
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

				$sql = "SELECT avg(b.cons) as cons from wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d 
				where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and b.po_break_down_id=d.po_break_down_id and c.id=b.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no ='$booking_no' and d.status_active=1 and d.is_deleted=0 and b.cons>0  and a.body_part_id='$body_part_id' and a.color_type_id='$color_type_id' and a.construction='$constrac_str' and a.composition='" . trim($compo_str) . "'  and a.gsm_weight='$gsm_weight' and b.remarks='$pre_cost_remarks'$dia_con "; 
				//if($rmg_qty_arr[$key_ids]==0){$con_sql_data = sql_select($sql);
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
		<? foreach ($colorArr as $colorVal) 
		{
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
	<!-- Yarn Required Summary (Pre Cost) Start -->
	<?
	if ($withinGroup==1 && $po_break_down_id != 0) 
	{
		$condition= new condition();
		if($po_break_down_id !=''){
			$condition->po_id("in(".$po_break_down_id.")");
		}
		$condition->init();
		// print_r($condition);die;
		$cos_per_arr=$condition->getCostingPerArr();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		// echo "<pre>"; print_r($yarn_data_array);die;

		$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate  from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$wo_job_no' and b.booking_no='$booking_no'  and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate order by id");
		?>
		<div style="display: inline-flex;">
			<div style="float: left; margin-right: 20px;">
				<table border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
					<tr align="center">
	                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>
	                </tr>
	                <tr bgcolor="#CCCCCC" align="center">
		                <td>Sl</td>
		                <td>Yarn Description</td>
		                <td>Brand</td>
		                <td>Lot</td>
		                <td>Rate</td>
		                <td>Cons for <? echo $costing_per; ?> Gmts</td>
		                <td>Total (KG)</td>
		                <td>$ Amount</td>
	                </tr>
					<?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
	                {
						$i++;
						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];

						$rate=$rowcons_Amt/$rowcons_qnty;
						$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
						$amount=$rowcons_qnty*$rate;
						?>
		                <tr align="center">
		                    <td><? echo $i; ?></td>
		                    <td>
							<?
							$yarn_des=$yarnCount_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
							$yarn_des.=$color_library[$row[csf('color')]]." ";
							$yarn_des.=$yarn_type[$row[csf('type_id')]];
							echo $yarn_des;
							?>
		                    </td>
		                    <td></td>
		                    <td></td>
		                    <td><? echo number_format($row[csf('rate')],4); ?></td>
		                    <td><?  echo number_format(($rowcons_qnty/$po_quantity)*$cos_per_arr[$wo_job_no],4); ?></td>

		                    <td align="right"><? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
		                    <td align="right"><? echo number_format($amount,2); $total_amount+=$amount; ?></td>
		                </tr>
		                <?
					}
					?>
	                <tr align="center">
	                    <td>Total</td>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td></td>
	                    <td align="right"><? echo number_format($total_yarn,4); ?></td>
	                    <td align="right"><? echo number_format($total_amount,4); ?></td>
	                </tr>
				</table>
			</div>
		</div>
		<br>
		<!-- Yarn Required Summary (Pre Cost) End -->

		<!-- Color Size Brakedown in Pcs. Start -->
		<?
		$lab_dip_color_arr=array();
		$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$wo_job_no'");
		foreach($lab_dip_color_sql as $row)
		{
			$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
		}

		$collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

		$collar_cuff_sql="SELECT a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, d.colar_cuff_per, e.body_part_full_name, e.body_part_type
		FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e
		WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$booking_no' and a.body_part_id=e.id and e.body_part_type in (40,50) and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 order by  c.size_order";
		// echo $collar_cuff_sql;die;
		$collar_cuff_sql_res=sql_select($collar_cuff_sql);
		
		$itemIdArr=array();

		foreach($collar_cuff_sql_res as $collar_cuff_row)
		{
			$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
			$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
			$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
			$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
			$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
			
			$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
		}
		//print_r($collar_cuff_percent_arr[40]) ;
		unset($collar_cuff_sql_res);
		//$count_collar_cuff=count($collar_cuff_size_arr);
		
		$order_plan_qty_arr=array();
		$color_wise_wo_sql_qnty=sql_select( "SELECT item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($po_break_down_id) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id");//and item_number_id in (".implode(",",$itemIdArr).")
		foreach($color_wise_wo_sql_qnty as $row)
		{
			$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
			$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
		}
		unset($color_wise_wo_sql_qnty);
		
		foreach($collar_cuff_body_arr as $body_type=>$body_name)
		{
			$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
			foreach($body_name as $body_val)
			{
				$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
				$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

				?>
	            <div style="max-height:1330px; overflow:auto; padding-top:5px; position:relative;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	                    <tr>
	                    	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
	                    </tr>
	                    <tr>
	                        <td width="100">Size</td>
								<?
	                            foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
	                            {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
	                            }
	                            ?>
	                        <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
	                        <td rowspan="2" align="center"><strong>Extra %</strong></td>
	                    </tr>
	                    <tr>
	                        <td style="font-size:12px"><? echo $body_val; ?> Size</td>
	                        <?
	                        foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
	                        {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
	                        }

	                        $pre_size_total_arr=array();
	                        foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
	                        {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?>
										<tr>
											<td>
												<?
	                                            if( $color_size_sensitive==3)
	                                            {
	                                                echo strtoupper ($constrast_color_arr[$color_number_id]) ;
	                                                $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
	                                            }
	                                            else
	                                            {
	                                                echo $color_library[$color_number_id];
	                                                $lab_dip_color_id=$color_number_id;
	                                            }
	                                            ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													$plan_cut=0;
													foreach($gmtsItemId as $giid)
													{
														if($body_type==50) $plan_cut+=($order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'])*2;
														else $plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];
													}
													
	                                                //$ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];

	                                                $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
	                                                // echo $collar_ex_per.'=';

												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
	                                                else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
	                                               // $colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
	                                               $tot_exPer=($plan_cut*$collar_ex_per)/100;
													$colar_excess_per=$tot_exPer;
												    $collerqty=($plan_cut+$colar_excess_per);

	                                                //$collerqty=number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');

	                                                echo number_format($collerqty);
	                                                $pre_size_total_arr[$size_number_id]+=$collerqty;
	                                                $pre_color_total_collar+=$collerqty;
	                                                $pre_color_total_collar_order_qnty+=$plan_cut;

	                                                //$pre_grand_tot_collar_order_qty+=$plan_cut;
	                                                ?>
												</td>
												<?
											}
											$DataArr=$pre_color_total_collar.'-'.$pre_color_total_collar_order_qnty.'/'.$pre_color_total_collar_order_qnty.'*100';
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											<td align="center" title="<? echo $DataArr;?> &nbsp; =(SizeQty-PlanCut)/PlanCut*100"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									}
								}
							}
							?>
	                    </tr>
	                    <tr>
	                        <td>Size Total</td>
								<?
	                            foreach($pre_size_total_arr  as $size_qty)
	                            {
									?>
									<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
									<?
	                            }
	                            ?>
	                        <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
	                        <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
	                    </tr>
					</table>
	            </div>
	            <br/>
	            <?
	    	}
		}
	}
	?>
	<!-- Color Size Brakedown in Pcs. End -->

	<!-- Fabric Main Process Required Start -->
	<?
	if($main_process_count>0)
	{
		$main_process_name='';
		foreach ($main_process_name_arr as $val) 
		{
			$main_process_name.=$val.',';
		}
		?>
		<table class="rpt_table" rules="all" width="100%" cellspacing="0" cellpadding="0" border="1">
            <tbody>	
				<tr>
					<td width="230"><strong>Fabric Main Process Required</strong></td>					
					<td colspan="3"><?echo chop($main_process_name,',');?></td>					
				</tr>
			</tbody>
		</table>
		<?
	}
	?>
	<br>
	<!-- Fabric Main Process Required End -->

	<!-- Fabric Process (If Require) Start -->
	<?
	if($main_process_count>0)
	{
		?>
		<table width="100%" cellspacing="0" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
			<thead>
				<tr><td colspan="2" align="center"><strong>Fabric Process (If Require)</strong></td></tr>
				<tr>
					<td width="40"><strong>Process Type</strong></td>
					<td width="540"><strong>Sub Processes Name</strong></td>
				</tr>
			</thead>
			<tbody>				
				<?
				foreach ($main_process_arr as $main_id) 
				{
					$subProcessNames = implode(",",array_unique(explode(",",chop($sub_process_name_arr[$main_id],','))));
					?>
					<tr>
						<td width="40"><?echo $conversion_cost_head_array[$main_id];?></td>					
						<td width="540"><?echo $subProcessNames//chop($sub_process_name_arr[$main_id],',');?></td>	
					</tr>
					<?
				}
				?>
			</tbody>
		</table>
		<br>
		<?
	}
	?>
	<!-- Fabric Process (If Require) End -->

	<!-- Special Instraction's (Party) Start -->
	<div style="margin-right: 80px; float: left;">
		<table width="100%" cellspacing="0" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
			<tr><td colspan="2" align="center"><strong>Special Instraction's (Party)</strong></td></tr>
			<tr>
				<td width="580"><? echo get_spacial_instruction($booking_no, "580px"); ?></td>
			</tr>
		</table>
	</div>
	<!-- Special Instraction's (Party) End -->

	<!-- Special Instraction's (Party) Start -->
	<div style="margin-bottom: 2px; float: right;">
		<table width="100%" cellpadding="4" cellspacing="4" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
			<tr><td colspan="2" align="center"><strong>Special Instraction's (BPKCL)</strong></td></tr>
			<tr>
				<td width="650"><? echo get_spacial_instruction($salesOrderNo, "650px"); ?></td>
			</tr>
		</table>
	</div>
	<!-- Special Instraction's (Party) End -->
	<br>
	<?
	echo signature_table(176, $data[0], "1000px","","10"); // signature

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


	$sql = "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,$fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season, b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date,d.attention as sales_attention,d.fabric_composition as sales_fab_comp, c.grouping  FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$companyId and a.status_active =1 and a.is_deleted =0 and a.item_category=2 and a.booking_no='$bookingNo' group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention,d.attention,d.fabric_composition,c.grouping
	union all
	select a.attention,null as job_no,null as order_repeat_no,c.id as sales_id,b.composition as fabric_composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id,cast(a.po_break_down_id as varchar2(4000)) as po_break_down_id,null as style_description,null as dealing_marchant,null as pub_shipment_date,null as pub_shipment_date,null as po_received_date,null as po_quantity, c.job_no,a.buyer_id,null as gmts_item_id,null as style_ref_no,null as team_leader,null as dealing_marchant,null as season_matrix, null as product_dept,null as style_owner,c.currency_id,c.remarks,c.delivery_date,c.attention as sales_attention,c.fabric_composition as sales_fab_comp,null as grouping from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no left join fabric_sales_order_mst c on a.booking_no=c.sales_booking_no where a.booking_no='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fabric>0 group by a.attention,c.id,b.composition,a.booking_no, c.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,c.job_no,a.buyer_id,c.currency_id,c.remarks,c.delivery_date,a.attention,b.composition,c.attention,c.fabric_composition";


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
	//echo $sql;
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

	$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');
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

	$grouping_arr = explode(',', $grouping);
	foreach ($grouping_arr as $grouping_data) {
		if ($grouping_string == '') {
			$grouping_string = $grouping_data;
		} else {
			$grouping_string .= ',' . $grouping_data;
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
			<td rowspan="10" valign="top" width="205">
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
			<td><strong>Internal Ref.</strong></td>
			<td colspan="3"><? echo $grouping_string; ?></td>
			<td><strong>Update Serial</strong></td>
			<td>
				Fabric: <? echo ($update_serial[0][csf("update_sl")] == "") ? 0 : $update_serial[0][csf("update_sl")]; ?>
				,
				Yarn: <? echo ($update_serial_yarn == "") ? 0 : $update_serial_yarn; ?>
			</td>
		</tr>
		<tr>
			<td><strong>Remarks</strong></td>
			<td colspan="5"><? echo $remarks; ?></td>
		</tr>
	</table>
	<br/>
	<?php
	if ($booking_no != "" && $sales_id != "")
	{
		if($db_type==0)
		{
			$color_id_str="group_concat(b.color_id)";
			
		}
		else if($db_type==2)
		{
			$color_id_str="listagg(b.color_id,',') within group (order by b.color_id) as color_id";
		}
		$dtls_sql = "select b.pre_cost_remarks,b.fabric_desc,b.gsm_weight,sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,sum(b.process_loss) process_loss,sum(b.rmg_qty) as rmg_qty,c.yarn_count_id,c.yarn_type,c.cons_ratio,c.deter_id,c.supplier_id,c.brand_id,c.cons_qty,c.composition_id, $color_id_str from fabric_sales_order_mst a inner join fabric_sales_order_dtls b on a.id=b.mst_id left join fabric_sales_order_yarn_dtls c on (a.id=c.mst_id and b.determination_id=c.deter_id and b.gsm_weight=c.gsm) where a.id=$sales_id and a.sales_booking_no ='$booking_no' and a.status_active=1 and b.is_deleted=0 group by c.deter_id,b.gsm_weight,b.fabric_desc,b.pre_cost_remarks,c.yarn_count_id,c.yarn_type,c.cons_ratio,c.supplier_id,c.brand_id, c.cons_qty,c.composition_id order by c.deter_id, b.gsm_weight,b.fabric_desc";
		//echo $dtls_sql;
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
			<th>Fabric Description pro</th>
			<th>Fabric Color</th>
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

			if ($row[csf('color_id')] != "") 
			{
				$color_no = ''; 
				$color_ids = array_unique(explode(",", $row[csf('color_id')]));
				foreach ($color_ids as $color_id) 
				{
					if ($color_no == "") $color_no = $color_library[$color_id]; else $color_no .= "," . $color_library[$color_id];

				}
			}

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
					<?php
				}else{
					?>
					<td align="center"><?php echo $i;?></td>
					<td><?php echo $row[csf("fabric_desc")];?></td>
					<td><?php echo $color_no;?></td>
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
					<th colspan="10" align="right">Sub Total</th>
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
			<th colspan="10" align="right">Grand Total</th>
			<th align="right"><?php echo number_format($cons_ratio_grand_total, 2, '.', '');?></th>
			<th align="right"><?php echo number_format($cons_qty_grand_total, 2, '.', '');?></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	<?php
	exit();
}

if ($action == 'fabric_sales_order_print4') // Print 3 // KDS
{
	
	extract($_REQUEST);

	//list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption, $update_id) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$dealing_marArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$team_leaderArr = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name", 'id', 'team_leader_name');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$yarnCount_arr = return_library_array("select id, yarn_count from lib_yarn_count order by yarn_count", 'id', 'yarn_count');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$update_serial = sql_select("select a.id, a.update_sl,a.booking_approval_date,a.revise_no from fabric_sales_order_mst a where a.sales_booking_no = '" . $bookingNo . "'");

	$fso_mst_sql = "SELECT a.booking_date as date_rel, a.dealing_marchant as prepared_by, a.team_leader as merchandiser, a.job_no as fso_job, a.po_job_no, a.style_ref_no, a.buyer_id as garment_unit, a.po_buyer as client, a.ship_mode, a.within_group, a.sales_booking_no, a.delivery_date
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
	$fso_dtls_sql="SELECT a.within_group,a.po_job_no, b.body_part_id, b.fabric_desc, b.width_dia_type, b.gsm_weight, b.dia, b.color_id, b.color_range_id, b.finish_qty, b.process_loss, b.grey_qty, b.determination_id, b.process_id_main as process_id, b.process_seq_main, b.color_type_id
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
		$data_arr[$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('color_range_id')]]['process_id']=$row[csf('process_seq_main')];

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
	$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$update_id' and file_type=1", 'id', 'image_location');
	if (count($image_locationArr) == 0) {
		$image_locationArr = return_library_array("select id,image_location from common_photo_library where form_name ='knit_order_entry' and master_tble_id=$job_nos and file_type=1", 'id', 'image_location');
	}
	$lapdip_sql_arr=sql_select("select lapdip_no,color_name_id,job_no_mst from wo_po_lapdip_approval_info where job_no_mst in($po_job_no) and color_name_id in($color_ids) and status_active=1 and is_deleted=0");
	foreach($lapdip_sql_arr as $row)
	{
		$lapdip_arr[$row[csf('color_name_id')]]["lapdip_no"]=$row[csf('lapdip_no')];
	}
	ob_start();
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
			<td><strong>GARMENT UNIT: </strong></td>
			<td>
			<? if ($fso_data[0][csf("within_group")] == 1) 
			{ 
				echo $buyerUnitArr[$fso_data[0][csf("garment_unit")]]; 
			} 
			?>
			</td>
		</tr>
		<tr>
			<td><strong>CLIENT: </strong></td><td>
			
			<? 
			if ($fso_data[0][csf("within_group")] == 1) 
			{
				echo $buyerArr[$fso_data[0][csf("client")]]; 
			}else{
				echo $buyerArr[$fso_data[0][csf("garment_unit")]]; 
			}
			?>
				
			</td>
			<td><strong>SHIPMENT MODE: </strong></td><td><? echo $shipment_mode[$fso_data[0][csf("ship_mode")]]; ?></td>
			<td><strong>STYLE: </strong></td><td><? echo $order_data[0][csf('style_description')]; ?></td>
		</tr>
		<tr>
			<td><strong>SHIPMENT DATE: </strong></td><td><? echo change_date_format($fso_data[0][csf('delivery_date')]); ?></td>
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
										<td><p style="width:100px;word-wrap: break-word;">
										<? 
										$process_seq_array =array();
										$processSeqArray = explode(",", $row['process_id']);
										foreach ($processSeqArray as $processSeq) 
										{
											$processSeqArr= explode("_", $processSeq);
											$process_seq_array[$processSeqArr[1]] = $processSeqArr[0];
										}
										ksort($process_seq_array);
										//print_r($process_seq_array);

										$allProcess="";
										foreach ($process_seq_array as $seq=>$processval) 
										{
											if($allProcess ==""){
												$allProcess .= $conversion_cost_head_array[$processval];
											}else{
												$allProcess .= ", ".$conversion_cost_head_array[$processval];
											}
											
										}
										
										echo  $allProcess;
										?>
											
										</p></td>
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
					<td rowspan="3" width="70">

					
					<? 
					if ($fso_data[0][csf("within_group")]==1)
					{
						$entry_form = 118;
						
						$mst_id = $fso_data[0][csf("sales_booking_no")];
						
						$html = '
						<table  width=100% class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr style="border:1px solid black;">
								<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
							</tr>
						</thead>
						<tbody>';

						if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
						
						$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id");
						if (count($data_array) > 0) {
							$i = 0;
							foreach ($data_array as $row) {
								$i++;
								$html .= '
								<tr id="settr_1" align="" style="border:1px solid black;">
									<td style="border:1px solid black;">' . $i . '</td>
									<td style="border:1px solid black;">' . $row[csf('terms')] . '</td>
								</tr>';
							}
						}else{
							
							$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=$entry_form order by id asc");// quotation_id='$data'
							$i = 0;
							foreach ($data_array as $row) {
								$i++;
								$html .= '
								<tr id="settr_1" align="" style="border:1px solid black;">
									<td style="border:1px solid black;">' . $i . '</td>
									<td style="border:1px solid black;">' . $row[csf('terms')] . '</td>
								</tr>';
							}
						}

						$html .= '
					
						</tbody>
						</table>';
						echo $html;

					}else{

						$entry_form = 109;
					
						$mst_id = $fso_data[0][csf("fso_job")];
						
						$html = '
						<table  width=100% class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
						<thead>
							<tr style="border:1px solid black;">
								<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
							</tr>
						</thead>
						<tbody>';

						if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
						 
						$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id");
						if (count($data_array) > 0) {
							$i = 0;
							foreach ($data_array as $row) {
								$i++;
								$html .= '
								<tr id="settr_1" align="" style="border:1px solid black;">
									<td style="border:1px solid black;">' . $i . '</td>
									<td style="border:1px solid black;">' . $row[csf('terms')] . '</td>
								</tr>';
							}
						}else{
						
							$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=$entry_form order by id asc");// quotation_id='$data'
							$i = 0;
							foreach ($data_array as $row) {
								$i++;
								$html .= '
								<tr id="settr_1" align="" style="border:1px solid black;">
									<td style="border:1px solid black;">' . $i . '</td>
									<td style="border:1px solid black;">' . $row[csf('terms')] . '</td>
								</tr>';
							}
						}

						$html .= '
					
						</tbody>
						</table>';
						echo $html;
					}

					
					
					
					// if ($fso_data[0][csf("within_group")]==1)
					// {
					// 	$page_id = 108;
					// 	echo get_spacial_instruction($fso_data[0][csf("sales_booking_no")],"",$page_id); 

					// }else
					// {

					// 	$page_id = 109;
					// 	echo get_spacial_instruction($fso_data[0][csf("sales_booking_no")],"",$page_id); 
					// }
					
					?>
					</td>
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
	$report_cat=100;
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("tb*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="tb".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename****$html****$report_cat";
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

	$image_locationArr2 = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');

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
			http.open("POST", "fabric_sales_order_entry_controller.php", true);
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

if ($action == "fabric_sales_order_print6") 
{
	list($companyId, $bookingId, $bookingNo, $salesOrderNo, $formCaption) = explode('*', $data);
	$companyArr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$addressArr = return_library_array("select id,city from lib_company where id=$companyId", 'id', 'city');
	$sql_buyer = sql_select("select address_1,id,buyer_name,buyer_email from lib_buyer");
	$buyer_data = array();
	foreach($sql_buyer as $row)
	{
		$buyer_data[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
		$buyer_data[$row[csf('id')]]['address'] = $row[csf('address_1')];
		$buyer_data[$row[csf('id')]]['email'] = $row[csf('buyer_email')];
	}
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	
	$supplier_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyId' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");
	
	

	$sql = "SELECT d.id as sales_id,d.currency_id,d.delivery_date,d.within_group,d.job_no, d.buyer_id,d.booking_date,
	d.sales_booking_no,d.company_id,d.delivery_date,d.dealing_marchant,d.remarks,d.style_ref_no,d.season,d.attention,d.fabric_composition from fabric_sales_order_mst d
	where d.status_active =1 and d.is_deleted =0 and d.job_no='$salesOrderNo'";
	if ($sales_id == '') {
		$mst_data = sql_select($sql);
		$mst_data = array_change_key_case($mst_data[0], CASE_LOWER);
		extract($mst_data);
	}

	$image_locationArr2 = return_library_array("select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$sales_id' and file_type=1", 'id', 'image_location');

	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
	
	?>
	<table width="100%" border="0" cellpadding="3" cellspacing="0"
	style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
		<tr>
			<td  align="center" width="15%">
				<img src='../../<? echo $imge_arr[$companyId]; ?>' height='100px' width='100%'/>
			</td>
			<td align="center">
				<strong style="font-size:20px;"><? echo $companyArr[$companyId]; ?></strong><br>
				<strong><? echo $addressArr[$companyId]; ?></strong><br>
				<strong><? echo $formCaption; ?></strong>
			</td>
			<td  width="15%"></td>
		</tr>
		
	</table>
	<table width="100%" border="1" cellpadding="3" rules="all" style="font: 12px tahoma;">
		<tr>
			<td rowspan="4">
				<p><? echo trim($buyer_data[$buyer_id]['buyer_name']); ?>
				<? echo trim($buyer_data[$buyer_id]['address']); ?>
				<? echo trim($buyer_data[$buyer_id]['email']); ?></p>	
			</td>
			<td><strong>Textile Ref.: </strong></td>
			<td><? echo $job_no; ?></td>
			<td><strong>Booking No</strong></td>
			<td><? echo $sales_booking_no; ?></td>
		</tr>
		<tr>
			<td><strong>Style Ref.</strong></td>
			<td><? echo $style_ref_no; ?></td>
			<td><strong>Booking Date</strong></td>
			<td><? echo change_date_format($booking_date); ?></td>
		</tr>
		<tr>
			<td><strong>Delivery Date</strong></td>
			<td><? echo change_date_format($delivery_date); ?></td>
			<td><strong>Currency</strong></td>
			<td><? echo $currency[$currency_id]; ?></td>
		</tr>
		<tr>
			<td><strong>Attention</strong></td>
			<td ><? echo $attention; ?></td>
			<td><strong>Remarks</strong></td>
			<td ><? echo $remarks; ?></td>
		</tr>
	</table>
		<br/>
		<?
		
		$dtls_sql ="SELECT b.pre_cost_remarks,b.gsm_weight,b.finish_qty,b.grey_qty,b.cons_uom, b.avg_rate, b.amount,b.determination_id,b.color_id,b.color_type_id, b.dia FROM    fabric_sales_order_mst a INNER JOIN fabric_sales_order_dtls b ON a.id = b.mst_id WHERE     a.id = $sales_id AND a.job_no = '$salesOrderNo' AND a.status_active = 1 AND b.is_deleted = 0 ORDER BY b.id";
		//echo $dtls_sql;die;

		$dtls_sql_data = sql_select($dtls_sql);
		$deter_id_arr = array();
		foreach ($dtls_sql_data as $row)
		{
			$deter_id_arr[$row[csf("determination_id")]]=$row[csf("determination_id")];
		}
		$deter_cond = where_con_using_array($deter_id_arr,0,"id");
		$deter_mst_cond = where_con_using_array($deter_id_arr,0,"mst_id");
		$deter_data_arr = array();
		$data_array = sql_select("select id, construction, fab_nature_id, gsm_weight, color_range_id from lib_yarn_count_determina_mst where fab_nature_id=2 and status_active=1 and is_deleted=0 $deter_cond");

		$composition_arr = array();
		$compositionData = sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0 $deter_mst_cond");
		foreach ($compositionData as $row) {
			$composition_arr[$row[csf('mst_id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}
		
		foreach ($data_array as $row)
		{
			$comp = '';

			if ($row[csf('construction')] != "") {
				$comp = $row[csf('construction')] . ", ";
			}
			$comp .= $composition_arr[$row[csf('id')]];
			$deter_data_arr[$row[csf('id')]]['construction'] = $row[csf('construction')];
			$deter_data_arr[$row[csf('id')]]['copmposition'] = $comp;
		}

		//print_r($deter_data_arr);
		
		
		?>
		<table width="100%" border="1" cellpadding="2" rules="all" style="font: 12px tahoma;">
		<tr style="background: #ccc;">
			<th>SL</th>
			<th>COMPOSITION</th>
			<th>CONSTRUCTION</th>
			<th>GSM</th>
			<th>Finish Dia</th>
			<th>FABRIC TYPE</th>
			<th>COLOR NAME</th>
			<th>QUANTITY<br>(KG)</th>
			<th>UNIT PRICE</th>
			<th>TOTAL VALUE</th>
			<th>Remarks</th>
			
		</tr>
		<?php
		$i=$j=1;

		foreach ($dtls_sql_data as $row)
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			?>
			<tr style="background: <?=$bgcolor?>;">
				<td align="center"><?php echo $i; ?></td>
				<td><?php echo $deter_data_arr[$row[csf('determination_id')]]['copmposition'];?></td>
				<td align="center"><?php echo $deter_data_arr[$row[csf('determination_id')]]['construction'];?></td>
				<td align="center"><?php echo $row[csf("gsm_weight")];?></td>
				<td align="center"><?php echo $row[csf("dia")];?></td>
				<td align="center"><?php echo $color_type[$row[csf("color_type_id")]];?></td>
				<td align="center"><?php echo $color_library[$row[csf("color_id")]];?></td>
				<td align="right"><?php echo number_format($row[csf("finish_qty")], 4, '.', '');?></td>
				<td align="right"><?php echo number_format($row[csf("avg_rate")], 4, '.', '');?></td>
				<td align="right"><?php echo number_format($row[csf("amount")], 4, '.', '');?></td>
				<td><p><?php echo $row[csf("pre_cost_remarks")];?></p></td>
			</tr>
			<?php
			

			
			$finish_qty += number_format($row[csf("finish_qty")], 2, '.', '');
			$amount 	+= number_format($row[csf("amount")], 2, '.', '');
			$i++;
		}
		?>
		<tr style="border-top: 5px solid #777; background: #ccc;">
			<th colspan="7" align="right">Grand Total</th>
			<th align="right"><?php echo number_format($finish_qty, 2, '.', '');?></th>
			<th></th>
			<th align="right"><?php echo number_format($amount, 2, '.', '');?></th>
			<th></th>
		</tr>

	</table>
	<table width="100%" border="1" cellpadding="2" rules="all" style="font: 12px tahoma;">
		<thead>
			<tr>
				<th width="150"  align="right">VALUE IN WORDS : </th>
				<th  align="left"><? echo number_to_words($amount, $currency[$currency_id]);?></th>
			</tr>
		</thead>
	</table>
	<br><br>
	<div style="margin-bottom: 2px; float: left;">
		<table width="100%" cellpadding="4" cellspacing="4" border="1" rules="all" class="rpt_table" style="font: 14px tahoma;">
			<tr><td colspan="2" align="center"><strong>Terms & Conditions: </strong></td></tr>
			<tr>
				<td width="100%">
					<? //echo get_spacial_instruction($salesOrderNo, "100%"); ?>
					<table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 11px;">
						<thead>
							<tr style="border:1px solid black;">
								<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
							</tr>
						</thead>
						<tbody>
							<?
							

							$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $salesOrderNo) . "'   order by id");
							if (count($data_array) > 0) {
								$i = 0;
								foreach ($data_array as $row) {
									$i++;
								?>
									<tr id="settr_1" align="" style="border:1px solid black;">
										<td style="border:1px solid black;"><?=$i;?></td>
										<td style="border:1px solid black;"><?=$row[csf('terms')];?></td>
									</tr>
								<?
								}
							}
							?>
						
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<!-- Special Instraction's (Party) End -->
	<br>
	<br>
	<?
	echo signature_table(176, $data[0], "1000px","","10");
	exit();
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

if($action=="sub_process_name_popup")
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);	
	// echo $job_no.'=====';						
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
						// $('#txt_process_rate'+idSeq[0]).val(idSeq[2]);
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
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
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
			<fieldset style="width:250px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
			value="">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="290" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th>Process Name</th>
						<th width="82">Sequence</th>
					</thead>
				</table>
				<div style="width:290px; overflow-y:scroll; max-height:230px;" id="buyer_list_view"
				align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table"
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
							id="search<? echo $i; ?>" >
							<td width="50" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
								<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
							</td>
							<td onClick="js_set_value(<? echo $i; ?>)">
								<p><? echo $name; ?></p>
							</td>
							<td width="65" align="center">
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
			<table width="290" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
						id="search<? echo $i; ?>" >
						<td width="50" align="center"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
							<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
						</td>
						<td onClick="js_set_value(<? echo $i; ?>)">
							<p><? echo $name; ?></p>
						</td>
						<td width="65" align="center"><input type="text" id="txt_process_rate<? echo $id ?>" name="txt_process_rate<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						<td width="65" align="center">
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

if ($action == "process_name_popup_main___________________bk_______________") 
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>
		/*$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});*/

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data() 
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) 
		{
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all() 
		{
			var old_seq = document.getElementById('txt_process_seq').value;
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") 
			{
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) 
				{
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

	        if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) 
	        {
	         	selected_id.push($('#txt_individual_id' + str).val());
	         	selected_name.push($('#txt_individual' + str).val());
	        }
	        else 
	        {
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
	        // alert(id);return;
	        $('#hidden_process_id').val(id);
	        $('#hidden_process_name').val(name);
	    }

	    function window_close()
	    {
	     	var old = document.getElementById('hidden_process_id').value;
	     	if (old != "") 
	     	{
	     		old = old.split(",");
	     		var seq='';
	     		var subSeq='';
	     		for (var k = 0; k < old.length; k++) 
	     		{
	     			if(seq=='')
	     			{
	     				seq=old[k]+'_'+$('#txt_sequence'+old[k]).val()+'_'+$('#txt_process_rate'+old[k]).val();
	     			}
	     			else
	     			{
	     				seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val()+'_'+$('#txt_process_rate'+old[k]).val();
	     			}
	     		}
	     	}
	     	// alert(seq);return;

	     	$('#hidden_process_seq').val(seq);

	     	var data_str="";
	     	$("#tbl_list_search").find('tbody tr').each(function()
			{				

				var txtSubProcessId=$(this).find('input[name="txtProcessId[]"]').val(); //1,2
				var txtSubProcessSeq=$(this).find('input[name="txtProcessSeq[]"]').val();//1_11,2_22
				var main_process_id=$(this).find('input[name="txt_individual_id[]"]').val();
				var main_rate=$(this).find('input[name="txt_process_rate[]"]').val();
				var main_seq=$(this).find('input[name="txt_sequence[]"]').val();
				if (main_process_id!="" && main_rate!="" && txtSubProcessId!="")
				{
					data_str+=main_process_id+'*'+main_rate+'*'+main_seq+'*'+txtSubProcessSeq+'@';
				}
			});
			// alert(data_str);
	     	$('#hidden_sub_process_seq').val(data_str);
			parent.emailwindow.hide();
		}

		function openmypage_subProcess(incr_id) // Sub process popup
		{
			var txt_process_id = $('#txtProcessId'+incr_id).val();
			var txt_process_seq = $('#txtProcessSeq'+incr_id).val();

			var title = 'Process Name Selection Form';
			var page_link = 'fabric_sales_order_entry_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=sub_process_name_popup';

			var title="Search Item Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=340px,height=300px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
				var process_name=this.contentDoc.getElementById("hidden_process_name").value;
				var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;

				$('#txtProcessId'+incr_id).val(process_id);
				$('#txtProcessName'+incr_id).val(process_name);
		        $('#txtProcessSeq'+incr_id).val(process_seq);
			}
		}
	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
		<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
		<input type="hidden" name="hidden_sub_process_seq" id="hidden_sub_process_seq" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
		value="">
		<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="90">Process Name</th>
					<th width="65">Rate</th>
					<th width="65">Sequence</th>
					<th> Sub Process</th>
				</thead>
			</table>
			<div style="width:380px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
			align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="362" class="rpt_table"
			id="tbl_list_search">
			<tbody>
			<?
			$sub_seq_str_arr=explode("@", $txtProcessSeqSub);
			foreach ($sub_seq_str_arr as $key => $value) //35*1*11*209_1,272_2
			{
				$sub_seq_str_arr2=explode("*", $value);
				// echo "<pre>";print_r($sub_seq_str_arr2);
				$sub_id_sub_seq_arr[$sub_seq_str_arr2[0]] = $sub_seq_str_arr2[3];// 209_1,272_2		
				// echo "<br>";print_r($sub_id_sub_seq_arr);

				$sub_id_sub_seq_str_arr=explode(",", $sub_seq_str_arr2[3]);
				// echo "<pre>";print_r($sub_id_sub_seq_str_arr);
				foreach ($sub_id_sub_seq_str_arr as $key2 => $val) 
				{
					$val2=explode("_", $val);
					$sub_id_arr[$sub_seq_str_arr2[0]] .= $val2[0].',';
					$sub_name_arr[$sub_seq_str_arr2[0]] .= $conversion_cost_head_array[$val2[0]].',';
				}				
			}

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
					$sub_id_seq=$sub_id_sub_seq_arr[$id];
					$sub_id=chop($sub_id_arr[$id],",");
					$sub_name=chop($sub_name_arr[$id],",");
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						id="search<? echo $i; ?>" >
						<td width="40" align="center"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id[]" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
							<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
						</td>
						<td width="90" onClick="js_set_value(<? echo $i; ?>)"><p><? echo $name; ?></p></td>
						<td width="65" align="center"><input type="text" id="txt_process_rate<? echo $id ?>" name="txt_process_rate[]" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						<td width="65" align="center">
							<input type="text" id="txt_sequence<? echo $id ?>" name="txt_sequence[]" value="" class="text_boxes_numeric" style=" width:50px;">
						</td>
						<td align="center">
	                        <input type="text" name="txtProcessName[]" id="txtProcessName<? echo $id ?>" class="text_boxes" style="width:80px;" placeholder="Double Click" onDblClick="openmypage_subProcess(<? echo $id ?>);" value="<? echo $sub_name; ?>" readonly />
	                        <input type="hidden" name="txtProcessId[]" id="txtProcessId<? echo $id ?>" value="<? echo $sub_id; ?>" />
	                        <input type="hidden" name="txtProcessSeq[]" id="txtProcessSeq<? echo $id ?>" value="<? echo $sub_id_seq; ?>" />
	                    </td>
					</tr>
					<?
					$i++;
				}
			}
		?>
		<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
		<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="<?php echo $process_seq; ?>"/>
			</tbody>
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


if ($action == 'populate_finish_change') {

	$data = explode("_", $data);
	$main_process 		= $data[0];
	$cbo_within_group 	= $data[1];
	$cbo_buyer_name 	= $data[2];

	if($main_process ==1)
	{
		$cbo_fabric_type 		= $data[3];
		$cbo_count_range_from 	= $data[4];
		$cbo_count_range_to 	= $data[5];

		$knit_charge_sql="SELECT a.main_process, b.knit_fabric_type, b.knit_from_count, b.knit_to_count, c.service_company, c.company_type, c.bdt from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b, lib_prcs_finfab_rt_chrt_rate c
		where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.main_process=1 and b.knit_fabric_type=$cbo_fabric_type and b.knit_from_count=$cbo_count_range_from and b.knit_to_count=$cbo_count_range_to and c.service_company=$cbo_buyer_name and c.company_type=$cbo_within_group order by c.effective_date desc, c.insert_date desc";
		$knit_charge_data=sql_select($knit_charge_sql);

		$knit_dollar_rate = $knit_charge_data[0][csf("bdt")];
		if($knit_dollar_rate){
			echo "$('#knit_charge').text(".$knit_dollar_rate.");\n";
		}else{
			echo "$('#knit_charge').text('');\n";
		}
		//main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_fabric_type+'_'+cbo_count_range_from+'_'+cbo_count_range_to
	}
	else if($main_process ==35)
	{
		$cbo_aop_type 		= $data[3];
		$cbo_no_color 		= $data[4];
		$txt_coverage_from 	= $data[5];
		$txt_coverage_to 	= $data[6];
		$cbo_aop_upto 		= $data[7];

		$aop_charge_sql="SELECT a.main_process, b.aop_type, b.aop_no_of_color, b.aop_coverage_from, b.aop_coverage_to, b.aop_upto, c.service_company, c.company_type, c.bdt from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b, lib_prcs_finfab_rt_chrt_rate c
		where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.main_process=35 and c.service_company=$cbo_buyer_name and c.company_type=$cbo_within_group and b.aop_type=$cbo_aop_type and b.aop_no_of_color=$cbo_no_color and b.aop_coverage_from =$txt_coverage_from and b.aop_coverage_to=$txt_coverage_to and b.aop_upto=$cbo_aop_upto order by c.effective_date desc, c.insert_date desc";
		$aop_charge_data=sql_select($aop_charge_sql);

		$aop_taka_rate = $aop_charge_data[0][csf("bdt")];
		if($aop_taka_rate){
			echo "$('#aop_charge').text(".$aop_taka_rate.");\n";
		}else{
			echo "$('#aop_charge').text('');\n";
		}

		//main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_aop_type+'_'+cbo_no_color+'_'+txt_coverage_from+'_'+txt_coverage_to+'_'+cbo_aop_upto
	}
	else if($main_process ==31)
	{
		$cbo_color_range 	= $data[3];
		$cbo_dyeing_part 	= $data[4];
		$cbo_diawidthtype 	= $data[5];
		$cbo_dyeing_upto 	= $data[6];

		$dye_charge_sql="SELECT a.main_process, b.dyeing_part, b.dia_width_type, b.dyeing_color_range, b.dyeing_upto, c.service_company, c.company_type, c.bdt from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b, lib_prcs_finfab_rt_chrt_rate c
		where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.main_process=31 and c.service_company=$cbo_buyer_name and c.company_type=$cbo_within_group and b.dyeing_part=$cbo_dyeing_part and b.dia_width_type=$cbo_diawidthtype and b.dyeing_color_range=$cbo_color_range and b.dyeing_upto=$cbo_dyeing_upto order by c.effective_date desc, c.insert_date desc";
		$dye_charge_data=sql_select($dye_charge_sql);

		$dye_taka_rate = $dye_charge_data[0][csf("bdt")];
		if($dye_taka_rate){
			echo "$('#dye_charge').text(".$dye_taka_rate.");\n";
		}else{
			echo "$('#dye_charge').text('');\n";
		}

		//main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_color_range+'_'+cbo_dyeing_part+'_'+cbo_diawidthtype+'_'+cbo_dyeing_upto
	}
	else if($main_process ==30)
	{
		$cbo_yarn_dyeing_part 	= $data[3];
		$cbo_yarn_color_range 	= $data[4];

		$yd_charge_sql="SELECT a.main_process, b.dyeing_part, b.dia_width_type, b.dyeing_color_range, b.dyeing_upto, c.service_company, c.company_type, c.bdt from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b, lib_prcs_finfab_rt_chrt_rate c
		where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.main_process=30 and c.service_company=$cbo_buyer_name and c.company_type=$cbo_within_group and b.yarn_dyeing_part=$cbo_yarn_dyeing_part and b.yarn_color_range=$cbo_yarn_color_range order by c.effective_date desc, c.insert_date desc";
		$yd_charge_data=sql_select($yd_charge_sql);

		$yd_taka_rate = $yd_charge_data[0][csf("bdt")];
		if($yd_taka_rate){
			echo "$('#yd_charge').text(".$yd_taka_rate.");\n";
		}else{
			echo "$('#yd_charge').text('');\n";
		}

		//main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_yarn_dyeing_part+'_'+cbo_yarn_color_range
	}
	else if($main_process ==1000)
	{
		$cbo_addi_process 	= $data[3];

		$addi_charge_sql="SELECT a.main_process, b.additional_process, c.service_company, c.company_type, c.bdt from lib_prcs_finfab_rt_chrt_mst a, lib_prcs_finfab_rt_chrt_dtls b, lib_prcs_finfab_rt_chrt_rate c
		where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.main_process=1000 and c.service_company=$cbo_buyer_name and c.company_type=$cbo_within_group and b.additional_process=$cbo_addi_process order by c.effective_date desc, c.insert_date desc";
		$addi_charge_data=sql_select($addi_charge_sql);

		$addi_charge_taka_rate = $addi_charge_data[0][csf("bdt")];
		if($addi_charge_taka_rate){
			echo "$('#addi_charge').text(".$addi_charge_taka_rate.");\n";
		}else{
			echo "$('#addi_charge').text('');\n";
		}

		//main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_addi_process
	}
}

if ($action == "process_name_popup_main") 
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);

		$txtProcessWiseRateArr =explode("@@", $process_seq);
		$knitProcessRateArr = $txtProcessWiseRateArr[0];
		$dyeProcessRateArr = $txtProcessWiseRateArr[1];
		$aopProcessRateArr = $txtProcessWiseRateArr[2];
		$yarnDyeProcessRateArr = $txtProcessWiseRateArr[3];
		$addiProcessRateArr = $txtProcessWiseRateArr[4];

		$knitProcessRateArrDtls = explode("__",$knitProcessRateArr);
		$dyeProcessRateArrDtls = explode("__",$dyeProcessRateArr);
		$aopProcessRateArrDtls = explode("__",$aopProcessRateArr);
		$yarnDyeProcessRateArrDtls = explode("__",$yarnDyeProcessRateArr);
		$addiProcessRateArrDtls = explode("__",$addiProcessRateArr);

		$KNIT_FABRIC_TYPE = $knitProcessRateArrDtls[0];
		$KNIT_FROM_COUNT = $knitProcessRateArrDtls[1];
		$KNIT_TO_COUNT = $knitProcessRateArrDtls[2];
		$KNIT_CHARGE = $knitProcessRateArrDtls[3]; 

		$DYEING_COLOR_RANGE = $dyeProcessRateArrDtls[0];
		$DYEING_PART = $dyeProcessRateArrDtls[1];
		$DIA_WIDTH_TYPE = $dyeProcessRateArrDtls[2];
		$DYEING_UPTO = $dyeProcessRateArrDtls[3];
		$DYEING_SUBPROCESS = $dyeProcessRateArrDtls[4];
		$DYEING_CHARGE = $dyeProcessRateArrDtls[5]; 


		$AOP_TYPE = $aopProcessRateArrDtls[0];
		$AOP_NO_OF_COLOR = $aopProcessRateArrDtls[1];
		$AOP_COVERAGE_FROM = $aopProcessRateArrDtls[2];
		$AOP_COVERAGE_TO = $aopProcessRateArrDtls[3];
		$AOP_UPTO = $aopProcessRateArrDtls[4];
		$AOP_SUBPROCESS = $aopProcessRateArrDtls[5];
		$AOP_CHARGE = $aopProcessRateArrDtls[6]; 


		$YARN_DYEING_PART = $yarnDyeProcessRateArrDtls[0];
		$YARN_COLOR_RANGE = $yarnDyeProcessRateArrDtls[1];
		$YARN_DYEING_SUBPROCESS = $yarnDyeProcessRateArrDtls[2];
		$YARN_DYEING_CHARGE = $yarnDyeProcessRateArrDtls[3];

		$ADDITIONAL_PROCESS = $addiProcessRateArrDtls[0];
		$ADDITIONAL_CHARGE = $addiProcessRateArrDtls[1];

	?>
	<script>
		var cbo_within_group = '<? echo $cbo_within_group;?>';
		var cbo_buyer_name = '<? echo $cbo_buyer_name;?>';

	    function window_close()
	    {
	     	var data_str="";
			
			//1
			var knitting_parameter_str = $('#cbo_fabric_type').val()+'__'+$('#cbo_count_range_from').val()+'__'+$('#cbo_count_range_to').val()+'__'+$('#knit_charge').text()*1;

			//31
			var dyeing_parameter_str = $('#cbo_color_range').val()+'__'+$('#cbo_dyeing_part').val()+'__'+$('#cbo_diawidthtype').val()+'__'+$('#cbo_dyeing_upto').val()+'__'+$('#cbo_dyeing_subprocess').val()+'__'+$('#dye_charge').text()*1;  

			//35
			var aop_parameter_str = $('#cbo_aop_type').val()+'__'+$('#cbo_no_color').val()+'__'+$('#txt_coverage_from').val()+'__'+$('#txt_coverage_to').val()+'__'+$('#cbo_aop_upto').val()+'__'+$('#cbo_aop_subprocess').val()+'__'+$('#aop_charge').text()*1;

			//30
			var yarn_dye_parameter_str = $('#cbo_yarn_dyeing_part').val()+'__'+$('#cbo_yarn_color_range').val()+'__'+$('#cbo_yd_subprocess').val()+'__'+$('#yd_charge').text()*1;

			//1000
			var additional_parameter_str = $('#cbo_addi_process').val()+'__'+$('#addi_charge').text()*1;

			var process_names =""; var process_ids = "";
			if($('#knit_charge').text()*1)
			{
				process_names ="Knitting";
				process_ids ="1";
			}
			if($('#dye_charge').text()*1)
			{
				if(process_names==""){
					process_names ="Fabric Dyeing";
					process_ids ="31";
				}else{
					process_names +=", Fabric Dyeing";
					process_ids +=",31";
				}
			}
			if($('#aop_charge').text()*1)
			{
				if(process_names==""){
					process_names ="AOP";
					process_ids ="35";
				}else{
					process_names +=", AOP";
					process_ids +=",35";
				}
			}
			if($('#yd_charge').text()*1)
			{
				if(process_names==""){
					process_names ="Yarn Dyeing";
					process_ids ="30";
				}else{
					process_names +=", Yarn Dyeing";
					process_ids +=",30";
				}
			}
			if($('#addi_charge').text()*1)
			{
				if(process_names==""){
					process_names ="Additional";
					process_ids ="Additional";
				}else{
					process_names +=", Additional";
					process_ids +=",1000";
				}
			}


			var data_str = knitting_parameter_str + '@@' + dyeing_parameter_str + '@@' + aop_parameter_str + '@@' + yarn_dye_parameter_str + '@@' + additional_parameter_str;
			$('#hidden_process_rate_str').val(data_str);

			$('#hidden_process_name').val(process_names);
			$('#hidden_process_id').val(process_ids);

			// alert(data_str);
	     	
			parent.emailwindow.hide();
		}

		function fnc_get_charge(main_process)  
		{
			//1
			var cbo_fabric_type = $('#cbo_fabric_type').val();
			var cbo_count_range_from = $('#cbo_count_range_from').val();
			var cbo_count_range_to = $('#cbo_count_range_to').val();

			//31
			var cbo_color_range = $('#cbo_color_range').val();
			var cbo_dyeing_part = $('#cbo_dyeing_part').val();
			var cbo_diawidthtype = $('#cbo_diawidthtype').val();
			var cbo_dyeing_upto = $('#cbo_dyeing_upto').val();

			//35
			var cbo_aop_type = $('#cbo_aop_type').val();
			var cbo_no_color = $('#cbo_no_color').val();
			var txt_coverage_from = $('#txt_coverage_from').val();
			var txt_coverage_to = $('#txt_coverage_to').val();
			var cbo_aop_upto = $('#cbo_aop_upto').val();

			//30
			var cbo_yarn_dyeing_part = $('#cbo_yarn_dyeing_part').val();
			var cbo_yarn_color_range = $('#cbo_yarn_color_range').val();

			//1000
			var cbo_addi_process = $('#cbo_addi_process').val();
			

			if(main_process == 1)
			{
				get_php_form_data(main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_fabric_type+'_'+cbo_count_range_from+'_'+cbo_count_range_to, "populate_finish_change", "fabric_sales_order_entry_controller");
			}
			else if(main_process == 30)
			{
				get_php_form_data(main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_yarn_dyeing_part+'_'+cbo_yarn_color_range, "populate_finish_change", "fabric_sales_order_entry_controller");
			}
			else if(main_process == 31)
			{
				get_php_form_data(main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_color_range+'_'+cbo_dyeing_part+'_'+cbo_diawidthtype+'_'+cbo_dyeing_upto, "populate_finish_change", "fabric_sales_order_entry_controller");
			}
			else if(main_process == 35)
			{
				get_php_form_data(main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_aop_type+'_'+cbo_no_color+'_'+txt_coverage_from+'_'+txt_coverage_to+'_'+cbo_aop_upto, "populate_finish_change", "fabric_sales_order_entry_controller");
			}
			else if(main_process == 1000)
			{
				get_php_form_data(main_process+'_'+cbo_within_group+'_'+cbo_buyer_name+'_'+cbo_addi_process, "populate_finish_change", "fabric_sales_order_entry_controller");
			}

			var total_process_charge = $('#knit_charge').text()*1 + $('#dye_charge').text()*1 + $('#aop_charge').text()*1 + $('#yd_charge').text()*1 + $('#addi_charge').text()*1;

			$('#total_process_charge').val(number_format(total_process_charge,4,'.',''));
			

		}
	</script>
</head>

<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
		<input type="hidden" name="hidden_process_rate_str" id="hidden_process_rate_str" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
		<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
		<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
			<table cellspacing="0" cellpadding="0" border="0" rules="all" width="500" >
				<legend>Process wise Rate Entry</legend>
					<tr style="font-weight:bold; background-color:lightsteelblue; line-height: 1.8; ">
						<td width="100">Knitting Parameter</td>
						<td width="150">&nbsp;</td>
						<td width="100">Knitting Charge</td>
						<td width="150" id="knit_charge"><? echo $KNIT_CHARGE;?></td>
					</tr>
					<tr>
						<td>Fabric Types</td>
						<td>
						<? echo create_drop_down( "cbo_fabric_type", 130, "select id, fabric_construction_name from lib_fabric_construction where status_active =1 and is_deleted=0  order by fabric_construction_name","id,fabric_construction_name", 1, "-- Select --", $KNIT_FABRIC_TYPE, "fnc_get_charge(1)" ); ?>
					</td>
						<td>Count range</td>
						<td>
						<?
							echo create_drop_down( "cbo_count_range_from", 70, "select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0  order by yarn_count","id,yarn_count", 1, "-- Select --", $KNIT_FROM_COUNT, "fnc_get_charge(1)" );
							echo create_drop_down( "cbo_count_range_to", 70, "select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0  order by yarn_count","id,yarn_count", 1, "-- Select --", $KNIT_TO_COUNT, "fnc_get_charge(1)" );
						?>  
					</td>
					</tr>
					<tr style="font-weight:bold; background-color:lightsteelblue; line-height: 1.8; ">
						<td width="100">Dyeing Parameter</td>
						<td width="150">&nbsp;</td>
						<td width="100">Dyeing Charge</td>
						<td width="150" id="dye_charge"><? echo $DYEING_CHARGE;?></td>
					</tr>
					<tr>
						<td>Color Range</td>
						<td>
							<?
								echo create_drop_down("cbo_color_range", 130, $color_range, "", 1, "-- Select --", $DYEING_COLOR_RANGE, "fnc_get_charge(31)", "0", "", "", "", "", "", "", "");
							?>
						</td>
						<td>Dyeing Part</td>
						<td>
							<? 
							$dyeing_part_arr = array();
							//$dyeing_part_arr = array(1=>'Cotton Part Dyeing',2=>'Double Part Dyeing',3=>'Polyester Part Dyeing',4=>'Viscose Dyeing');
							echo create_drop_down("cbo_dyeing_part", 130, $fabric_dyeing_part_arr, "", 1, "-- Select --", $DYEING_PART, "fnc_get_charge(31)");
							?>
						</td>
					</tr>
					<tr>
						<td>Width/Dia type </td>
						<td>
							<?
							echo create_drop_down("cbo_diawidthtype", 130, $fabric_typee, "", 1, "-- Select --", $DIA_WIDTH_TYPE, "fnc_get_charge(31)", "", "", "", "", "", "", "", "");
							?>
						</td>
						<td>Dyeing Upto</td>
						<td>
							<?
								echo create_drop_down("cbo_dyeing_upto", 130, $dyeing_sub_process, "", 1, "-- Select --", $DYEING_UPTO, "fnc_get_charge(31)", "0", "", "", "", "", "", "", "");
							?>
						</td>
					</tr>
					<tr>
						<td>Sub-Process</td>
						<td>
							<? 
								echo create_drop_down("cbo_dyeing_subprocess", 130, $conversion_cost_head_array, "", 1, "-- Select --", $DYEING_SUBPROCESS, "fnc_get_charge(31)", "0", "", "", "", '2, 3, 4, 101, 120, 121, 122, 123, 124', "", "", "");
								//$not_process_id_print_array = array(2, 3, 4, 101, 120, 121, 122, 123, 124);
							?>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr style="font-weight:bold; background-color:lightsteelblue; line-height: 1.8; ">
						<td width="100">AOP Parameter</td>
						<td width="150">&nbsp;</td>
						<td width="100">AOP Charge</td>
						<td width="150" id="aop_charge"><? echo $AOP_CHARGE;?></td>
					</tr>
					<tr>
						<td>AOP Type</td>
						<td>
							<?
							echo create_drop_down( "cbo_aop_type", 130,  $print_type,0, 1, "-- Select --", $AOP_TYPE, "fnc_get_charge(35)","","" ); 
							?>
						</td>
						<td>No of Color</td>
						<td>
							<?
							$no_color_arr=array(1=>"1 - 3",2=>"1 - 5",3=>"4 - 6",4=>"7 - 12");
							echo create_drop_down( "cbo_no_color", 130, $no_color_arr,"", 1, "-- Select --", $AOP_NO_OF_COLOR, "fnc_get_charge(35)","","" );
							?>
						</td>
					</tr>
					<tr>
						<td>Coverage Percent</td>
						<td>
							<input type="text" id="txt_coverage_from"  name="txt_coverage_from" class="text_boxes" style="width:52px" value="<? echo $AOP_COVERAGE_FROM;?>" placeholder="From"/>&nbsp;
							<input type="text" id="txt_coverage_to"  name="txt_coverage_to" class="text_boxes" style="width:52px" value="<? echo $AOP_COVERAGE_TO;?>"  placeholder="To"/>
						</td>
						<td>AOP Process Upto</td>
						<td>
							<?
								echo create_drop_down("cbo_aop_upto", 130, $dyeing_sub_process, "", 1, "-- Select --", $AOP_UPTO, "fnc_get_charge(35)", "0", "", "", "", "", "", "", "");
							?>
						</td>
					</tr>
					<tr>
						<td>Sub-Process</td>
						<td>
							<? 
								echo create_drop_down("cbo_aop_subprocess", 130, $conversion_cost_head_array, "", 1, "-- Select --", $AOP_SUBPROCESS, "fnc_get_charge(35)", "0", "", "", "", '2, 3, 4, 101, 120, 121, 122, 123, 124', "", "", "");
								//$not_process_id_print_array = array(2, 3, 4, 101, 120, 121, 122, 123, 124);
							?>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr style="font-weight:bold; background-color:lightsteelblue; line-height: 1.8; ">
						<td width="100">YD Parameter</td>
						<td width="150">&nbsp;</td>
						<td width="100">YD Charge</td>
						<td width="150" id="yd_charge"><? echo $YARN_DYEING_CHARGE;?></td>
					</tr>
					<tr>
						<td>YD Part</td>
						<td>
							<?
								echo create_drop_down("cbo_yarn_dyeing_part", 130, $yarn_dyeing_part_arr, "", 1, "-- Select --", $YARN_DYEING_PART, "fnc_get_charge(30)");
							?>
						</td>
						<td>Color Range</td>
						<td>
							<?
							echo create_drop_down("cbo_yarn_color_range", 130, $color_range, "", 1, "-- Select --", $YARN_COLOR_RANGE, "fnc_get_charge(30)", "0", "", "", "", "", "", "", "");
							?>
						</td>
					</tr>
					<tr>
						<td>Sub-Process</td>
						<td>
							<? 
								echo create_drop_down("cbo_yd_subprocess", 130, $conversion_cost_head_array, "", 1, "-- Select --", $YARN_DYEING_SUBPROCESS, "fnc_get_charge(30)", "0", "", "", "", '2, 3, 4, 101, 120, 121, 122, 123, 124', "", "", "");
								//$not_process_id_print_array = array(2, 3, 4, 101, 120, 121, 122, 123, 124);
							?>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr style="font-weight:bold; background-color:lightsteelblue; line-height: 1.8; ">
						<td width="100">Additional Process</td>
						<td width="150">&nbsp;</td>
						<td width="100">Additional Charge</td>
						<td width="150" id="addi_charge"><? echo $ADDITIONAL_CHARGE;?></td>
					</tr>
					<tr>
						<td>Process Name</td>
						<td>
							<? 
								echo create_drop_down("cbo_addi_process", 130, $additional_part_arr, "", 1, "-- Select --", $ADDITIONAL_PROCESS, "fnc_get_charge(1000)");
							?>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td style="font-weight:bold;text-align:right;">Total Charge</td>
						<td align="right"><input type="text" id="total_process_charge" class="text_boxes" style="width: 100px;" disabled />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				
			</table>
			
		<table width="500" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:100%;" align="center">
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
	
</script>
</html>
<?
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
						id="search<? echo $i; ?>" >
						<td width="50" align="center"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
							<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
						</td>
						<td onClick="js_set_value(<? echo $i; ?>)">
							<p><? echo $name; ?></p>
						</td>
						<td width="65" align="center"><input type="text" id="txt_process_rate<? echo $id ?>" name="txt_process_rate<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
						<td width="65" align="center">
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

if($action == "is_validate")
{
	$variable_rate_edit = sql_select("SELECT production_entry FROM variable_settings_production where company_name='$data' and variable_list=66 and is_deleted = 0 AND status_active = 1");
	if ($variable_rate_edit[0][csf("production_entry")] == 2) 
	{
		echo "editable";
	}
	else{		
		echo "noeditable";
	}
	exit();
}

?>