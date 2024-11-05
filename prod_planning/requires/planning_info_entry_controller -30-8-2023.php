<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_location")
{
	echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

// if ($action == "load_drop_down_machine_group")
// {
// 	echo create_drop_down("cbo_machine_group", 100, "select machine_group from lib_machine_name where company_id='$data' and machine_group is not null and category_id=1 and status_active =1 and is_deleted=0 group by machine_group order by machine_group", "machine_group,machine_group", 1, "-- Machine Group --", 1, "");
// 	exit();
// }

if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", 0);
	exit();
}

if ($action == "load_drop_down_knitting_party")
{
	$data = explode("**", $data);
	if ($data[0] == 1)
	{
		$users_working_unit_cond = '';
		$sql_user_rslt = sql_select("SELECT working_unit_id AS WORKING_UNIT_ID FROM user_passwd WHERE id = '".$_SESSION['logic_erp']['user_id']."' AND valid = 1");
		$users_working_unit = $sql_user_rslt[0]['WORKING_UNIT_ID'];
		if($users_working_unit != '')
		{
			echo create_drop_down("cbo_knitting_party", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  AND comp.id IN(".$users_working_unit.") order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'planning_info_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); getValutAttention(this.value);", "");
		}
		else
		{
			echo create_drop_down("cbo_knitting_party", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'planning_info_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); getValutAttention(this.value);", "");
		}
	}
	else if ($data[0] == 3)
	{
		if ($data[2] == 1) $selected_id = $data[1]; else $selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 152, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "load_drop_down( 'planning_info_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); getValutAttention(this.value);");
	}
	else
	{
		echo create_drop_down("cbo_knitting_party", 152, $blank_array, "", 1, "--Select Knit Party--", 0, "load_drop_down( 'planning_info_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); getValutAttention(this.value);");
	}
	exit();
}

if ($action == "approval_needed_or_not")
{
	$approval_needed_or_not = return_field_value("auto_update", "variable_settings_production", "company_name ='$data' and variable_list=31 and is_deleted=0 and status_active=1");
	if ($approval_needed_or_not == 1) $approval_needed_or_not = $approval_needed_or_not; else $approval_needed_or_not = 0;
	echo "document.getElementById('approval_needed_or_not').value 					= '" . $approval_needed_or_not . "';\n";

	exit();
}
if($action=="check_collar_cuff_variable")
{
	$sql_info ="select coller_cuf_size_planning from variable_settings_production where company_name='$data' and variable_list=53 and status_active=1 and is_deleted=0";
	//echo $sql_info;// die;
	$result_dtls = sql_select($sql_info);
	$collarCuff=$result_dtls[0]['COLLER_CUF_SIZE_PLANNING'];
	echo "1"."_".$collarCuff;
	exit();
}
if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1]) break;
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
			$('#hide_style_ref').val(name);
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Style Ref.</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_style_ref" id="hide_style_ref" value=""/>
					<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Style Ref", 2 => "Job No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_style_ref_search_list_view', 'search_div', 'planning_info_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");

if ($action == "create_style_ref_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];
	//if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0 )
				$buyer_id_cond = " and buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " and buyer_name=$data[1]";//.str_replace("'","",$cbo_buyer_name)
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1) $search_field = "style_ref_no"; else $search_field = "job_no";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$arr = array(0 => $company_arr, 1 => $buyer_arr);
	$sql = "select id, $year_field job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,120,60,70", "600", "240", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "", '', '0,0,0,0,0', '', 1);

	exit();
}

if ($action == "order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1]) break;
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

			$('#hide_order_id').val(id);
			$('#hide_order_no').val(name);
		}


		function fn_change_caption(str)
		{
			if(str==1)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('Shipment From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('Shipment To Date');
			}
			else if(str==2)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('TNA Start From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('TNA Start To Date');
			}
			else if(str==3)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('TNA Finish From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('TNA Finish To Date');
			}
			else
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('Shipment From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('Shipment To Date');

			}
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:980px;">
				<table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Order No</th>

					<th width="90" >Date Category</th>
					<th width="70"  id="from_date_html">Shipment From Date</th>
					<th id="to_date_html">Shipment To Date</th>

					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_order_no" id="hide_order_no" value=""/>
					<input type="hidden" name="hide_order_id" id="hide_order_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No", 4 => "Internal Ref", 5 => "File No");
							$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>

						<td align="center">
							<?
							$search_type=array(1=>'Shipment Date',2=>'Knit TNA Start Date',3=>'Knit TNA Finish');
							echo create_drop_down( "cbo_date_type", 90, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
							?>
						</td>
						<td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value="" placeholder="From Date" /></td>
						<td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value="" placeholder="To Date" /></td>

						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_date_type').value, 'create_order_no_search_list_view', 'search_div', 'planning_info_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="7" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_order_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] >0 ) $buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_cond = "";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else if ($search_by == 4)
		$search_field = "b.grouping";
	else if ($search_by == 5)
		$search_field = "b.file_no";
	else
		$search_field = "a.job_no";

	if (trim($data[3]) != "") {
		$search_field_cond = " and $search_field like '$search_string'";
	} else {
		$search_field_cond = "";
	}


	$start_date = trim($data[4]);
	$end_date = trim($data[5]);
	$cbo_date_category = str_replace("'", "", trim($data[6]));

	if($cbo_date_category==1)
	{
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
		} else {
			$date_cond = "";
		}
	}
	else if($cbo_date_category==2)
	{
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond2 = "and c.task_start_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond2 = "and c.task_start_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}

			$tnaTaskNameCond = "and c.task_number=60";
		} else {
			$date_cond2 = "";
			$tnaTaskNameCond = "";
		}
	}
	else if($cbo_date_category==3)
	{
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) {
				$date_cond2 = "and c.task_finish_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			} else {
				$date_cond2 = "and c.task_finish_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
			$tnaTaskNameCond = "and c.task_number=60";
		} else {
			$date_cond2 = "";
			$tnaTaskNameCond = "";
		}
	}



	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$arr = array(0 => $company_arr, 1 => $buyer_arr);

	//$sql = "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b,tna_process_mst c where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.company_name=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $buyer_id_cond $date_cond $tnaTaskNameCond order by b.id, b.pub_shipment_date";

	$sql = "select b.id, to_char(a.insert_date,'YYYY') as year, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b
		left join tna_process_mst c on b.id=c.po_number_id and c.status_active=1 and c.is_deleted=0 $date_cond2 $tnaTaskNameCond where a.job_no=b.job_no_mst and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Internal Ref, File No, Shipment Date", "70,70,50,60,130,130,90,90", "860", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,file_no,pub_shipment_date", "", '', '0,0,0,0,0,0,0,0,3', '', 1);

	exit();
}

if ($action == "yarn_desc_pop_up")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$desc = str_replace("'", "", $desc);
	?>
</head>
<body>
	<div align="center">
		<table width="410" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
		<thead>
			<th>Desc. Of Yarn</th>

		</thead>
		<tbody>
			<tr class="general">
				<td>
					<?
					echo $desc;
					?>
				</td>

			</tr>
		</tbody>

	</table>


</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>

	<script>

		function js_set_value(data) {
			var data = data.split("_");
			var id = data[0];
			var booking_no = data[1];
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:600px;">
				<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th>Year</th>
					<th id="search_by_td_up" width="160">Please Enter Booking No.</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value=""/>
					<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" value=""/>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No.", 2 => "Job No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td>
							<?
							echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+'<? echo $cbo_booking_type; ?>', 'create_booking_search_list_view', 'search_div', 'planning_info_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//for create_booking_search_list_view
if ($action == "create_booking_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] > 0)
				$buyer_id_cond = " AND BUYER_ID IN (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " AND BUYER_ID = ".$data[1];
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]);
	$cbo_year = $data[4];

	if (trim($cbo_year) != 0)
	{
		$year_cond = " AND TO_CHAR(INSERT_DATE,'YYYY') = ".$cbo_year;
	}
	else $year_cond = "";

	if ($search_by == 1)
		$search_cond = " AND BOOKING_NO LIKE '".$search_string."'";
	else
		$search_cond = " AND JOB_NO LIKE '%".$search_string."%'";

	$cbo_booking_type_short = explode("_",$data[5]);

	$cbo_booking_type = $cbo_booking_type_short[0];
	$is_short = $cbo_booking_type_short[1];

	if($cbo_booking_type>0)
	{
		$booking_type_cond = " AND BOOKING_TYPE = ".$cbo_booking_type." AND IS_SHORT = ".$is_short;
	}
	else
	{
		$booking_type_cond = "";
	}

	$sql = "SELECT ID, BOOKING_NO, BOOKING_DATE, BUYER_ID, JOB_NO, PAY_MODE, SUPPLIER_ID FROM wo_booking_mst WHERE ITEM_CATEGORY = 2 AND FABRIC_SOURCE != 2 AND PAY_MODE in(3,5) AND company_id = ".$company_id.$search_cond.$buyer_id_cond.$year_cond.$booking_type_cond."
	UNION ALL
	SELECT ID, BOOKING_NO, BOOKING_DATE, BUYER_ID, JOB_NO, PAY_MODE, SUPPLIER_ID FROM WO_NON_ORD_SAMP_BOOKING_MST WHERE ITEM_CATEGORY=2 AND FABRIC_SOURCE != 2 AND PAY_MODE = 3 AND COMPANY_ID = ".$company_id.$search_cond.$buyer_id_cond.$year_cond.$booking_type_cond." ORDER BY ID DESC";
	//echo $sql;
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Buyer Name</th>
				<th width="110">Booking No</th>
				<th width="70">Booking Date</th>
				<th width="100">Job No.</th>
				<th>Supplier</th>
			</thead>
		</table>
	</div>
	<div style="width:600px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" id="tbl_list_search">
			<?
			$sql_res=sql_select($sql);
			$i=1;
			foreach( $sql_res as $row )
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['ID'].'_'.$row['BOOKING_NO']; ?>');" >
					<td width="30"><?php echo $i; ?></td>
					<td width="100"><?php echo $buyer_arr[$row['BUYER_ID']]; ?></td>
					<td width="110"><?php echo $row['BOOKING_NO']; ?></td>
					<td width="70"><?php echo change_date_format($row['BOOKING_DATE']); ?></td>
					<td width="100"><?php echo $row['JOB_NO']; ?></td>
					<td><?php echo $company_arr[$row['SUPPLIER_ID']]; ?></td>
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

if ($action == "booking_item_details")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$approval_needed_or_not = str_replace("'", "", $approval_needed_or_not);
	$file_no = trim(str_replace("'", "", $txt_file_no));
	$internal_ref = trim(str_replace("'", "", $txt_internal_ref));
	//echo $type;
	if ($file_no == "")
		$file_no_cond = "";
	else
		$file_no_cond = " and file_no='" . $file_no . "'";

	if ($internal_ref == "")
		$internal_ref_cond = "";
	else
		$internal_ref_cond = " and grouping='" . $internal_ref . "'";

	if (str_replace("'", "", $cbo_buyer_name) == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0)
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " and a.buyer_id=$cbo_buyer_name";
	}

	if (str_replace("'", "", $hide_job_id) == "")
	{
		$job_no_cond = "";
	}
	else
	{
		$job_no_cond = "and c.id in(" . str_replace("'", "", $hide_job_id) . ")";
	}

	if (str_replace("'", "", trim($txt_order_no)) == "" && $internal_ref == "" && $file_no == "")
	{
		$po_id_cond = "";
	}
	else
	{
		if (str_replace("'", "", $hide_order_id) != "")
		{
			$po_id = str_replace("'", "", $hide_order_id);
		}
		else
		{
			$po_number = "%" . trim(str_replace("'", "", $txt_order_no)) . "%";
			if ($db_type == 0)
			{
				$po_id = return_field_value("group_concat(id) as po_id", "wo_po_break_down", "po_number like '$po_number' and status_active=1 and is_deleted=0 $file_no_cond $internal_ref_cond", "po_id");
			}
			else
			{
				$po_id = return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id", "wo_po_break_down", "po_number like '$po_number' and status_active=1 and is_deleted=0 $file_no_cond $internal_ref_cond", "po_id");
			}
			if ($po_id == "")
				$po_id = 0;
		}
		$po_id_cond = "and b.po_break_down_id in(" . $po_id . ")";
	}

	$txt_booking = "%" . str_replace("'", "", trim($txt_booking_no));
	if(str_replace("'","",trim($txt_booking_no))!="")
	{
		$booking_cond="and a.booking_no like '$txt_booking'";
		$pr_booking_cond="and booking_no like '$txt_booking'";
	}
	else
	{
		$booking_cond="";
		$pr_booking_cond="";
	}

	$booking_date = '';
	$date_from = str_replace("'", "", trim($txt_date_from));
	$date_to = str_replace("'", "", trim($txt_date_to));
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$booking_date = "and a.booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$booking_date = "and a.booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$cbo_booking_type_short = explode("_",str_replace("'", "", trim($cbo_booking_type)));
	$cbo_booking_type = $cbo_booking_type_short[0];
	$is_short = $cbo_booking_type_short[1];

	if($cbo_booking_type>0)
	{
		$booking_type_cond = "and a.booking_type=$cbo_booking_type and a.is_short=$is_short";
	}
	else
	{
		$booking_type_cond = "";
	}

	if ($type == 2)
	{
		if ($db_type == 0)
		{

			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode = 3 $booking_cond $booking_date $booking_type_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'

			$sqlColor = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, b.fabric_color_id, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode = 3 $booking_cond $booking_date $booking_type_cond group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width, b.fabric_color_id order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'
		}
		else
		{

			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode = 3 $booking_cond $booking_date $booking_type_cond group by a.id, a.company_id, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, c.style_ref_no order by b.dia_width,a.booking_no";

			$sqlColor = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, b.fabric_color_id, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode = 3 $booking_cond $booking_date $booking_type_cond group by a.id, a.company_id, a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, b.fabric_color_id, c.style_ref_no order by b.dia_width,a.booking_no";
		}
	}
	else
	{

		if ($db_type == 0)
		{

			$sql = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_no_prefix_num as booking_prefix,a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode in(3,5) $booking_cond $job_no_cond $buyer_id_cond $po_id_cond $po_data_file_ref $booking_date $booking_type_cond group by a.booking_no, a.booking_no_prefix_num,b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by cast(b.dia_width as unsigned),a.booking_no";// and a.buyer_id like '$buyer_name'

			$sqlColor = "SELECT a.id, a.company_id, a.item_category, a.fabric_source, a.booking_no_prefix_num as booking_prefix,a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, b.fabric_color_id, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode in(3,5) $booking_cond $job_no_cond $buyer_id_cond $po_id_cond $po_data_file_ref $booking_date $booking_type_cond group by a.booking_no, a.booking_no_prefix_num,b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width, b.fabric_color_id order by cast(b.dia_width as unsigned),a.booking_no";

		}
		else
		{
			$sql = "SELECT a.id, a.company_id, a.item_category, a.booking_no_prefix_num as booking_prefix,a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode in(3,5) $booking_cond $job_no_cond $buyer_id_cond $po_id_cond $po_data_file_ref $booking_date $booking_type_cond group by a.id, a.company_id, a.fabric_source, a.booking_no_prefix_num,a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, c.style_ref_no order by b.dia_width, a.booking_no";

			$sqlColor = "SELECT a.id, a.company_id, a.item_category, a.booking_no_prefix_num as booking_prefix,a.fabric_source, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, b.fabric_color_id, sum(b.grey_fab_qnty) as qnty, c.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_name and a.item_category=2  and (a.fabric_source=1 or b.fabric_source=1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 and a.pay_mode in(3,5) $booking_cond $job_no_cond $buyer_id_cond $po_id_cond $po_data_file_ref $booking_date $booking_type_cond group by a.id, a.company_id, a.fabric_source, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_no, a.booking_date, a.job_no, a.buyer_id, a.is_approved, a.item_category, b.construction, b.copmposition, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.gsm_weight, b.dia_width, b.fabric_color_id, c.style_ref_no order by b.dia_width, a.booking_no";
		}
	}
	//echo $sql; die;
	$nameArray = sql_select($sql);
	$booking_po_arr = array();
	foreach ($nameArray as $row)
	{
		$pre_cost_id_arr[] = $row[csf("pre_cost_fabric_cost_dtls_id")];
		$po_id_arr[] = $row[csf("po_break_down_id")];
		$booking_no_arr[] = $row[csf("booking_no")];
		$booking_po_arr[$row[csf("booking_no")]][$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
	}

	$all_booking_nos="'".implode("','", array_filter(array_unique($booking_no_arr)))."'";
	$bookId = $all_booking_no_cond = "";
	$all_booking_arr=explode(",",$all_booking_nos);
	if($db_type==2 && count($all_booking_arr)>999)
	{
		$all_booking_chunk=array_chunk($all_booking_arr,999) ;
		foreach($all_booking_chunk as $chunk_arr)
		{
			$bookId.=" booking_no in(".implode(",",$chunk_arr).") or ";
		}

		$all_booking_no_cond.=" and (".chop($bookId,'or ').")";
	}
	else
	{
		$all_booking_no_cond=" and booking_no in($all_booking_nos)";
	}

	$pre_cost_array = array();
	if(!empty($pre_cost_id_arr))
	{
		$pre_cost_cond = " and id in(".implode(",", array_unique($pre_cost_id_arr)).")";
		if ($db_type == 0)
		{
			$costing_sql = sql_select("SELECT id, body_part_id, color_type_id, width_dia_type, gsm_weight, concat_ws(', ',construction,composition) as fab_desc, lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where status_active=1 $pre_cost_cond");
		}
		else
		{
			$costing_sql = sql_select("SELECT id, body_part_id, color_type_id, width_dia_type, gsm_weight, construction || ',' || composition as fab_desc, lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where status_active=1 $pre_cost_cond");
		}

		foreach ($costing_sql as $row)
		{
			$costing_per_id_library[$row[csf('id')]]['body_part'] = $row[csf('body_part_id')];
			$costing_per_id_library[$row[csf('id')]]['color_type'] = $row[csf('color_type_id')];
			$costing_per_id_library[$row[csf('id')]]['width_dia_type'] = $row[csf('width_dia_type')];
			$costing_per_id_library[$row[csf('id')]]['gsm'] = $row[csf('gsm_weight')];
			$costing_per_id_library[$row[csf('id')]]['desc'] = str_replace("+", "", $row[csf('fab_desc')]);
			$costing_per_id_library[$row[csf('id')]]['determination_id'] = $row[csf('lib_yarn_count_deter_id')];
		}
	}

	$tna_array = array();
	if(!empty($pre_cost_id_arr))
	{
		$po_cond = " and po_number_id in(".implode(",",array_unique($po_id_arr)).")";
		$tna_sql = sql_select("SELECT id, po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=60 and is_deleted=0 and status_active=1 $po_cond");
		foreach ($tna_sql as $row)
		{
			$tna_array[$row[csf('po_number_id')]]['start_d'] = $row[csf('task_start_date')];
			$tna_array[$row[csf('po_number_id')]]['finish_d'] = $row[csf('task_finish_date')];
		}
	}

	$yarn_desc_array = array();
	$prod_sql = "SELECT id, lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type from product_details_master where item_category_id=1 and status_active=1";
	$result = sql_select($prod_sql);
	foreach ($result as $row)
	{
		$compostion = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		}
		else
		{
			$compostion = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$yarn_desc = $row[csf('lot')] . " " . $yarn_count_details[$row[csf('yarn_count_id')]] . " " . $compostion . " " . $yarn_type[$row[csf('yarn_type')]];
		$yarn_desc_array[$row[csf('id')]] = $yarn_desc;
	}

	$booking_item_array = array();
	if ($db_type == 0)
	{
		$booking_item_array = return_library_array("SELECT a.booking_no, group_concat(distinct(b.item_id)) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_cond group by a.booking_no", 'booking_no', 'prod_id');
	}
	else
	{
		$booking_item_array = return_library_array("SELECT a.booking_no, LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id) as prod_id from inv_material_allocation_mst a,inv_material_allocation_dtls b where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_cond group by a.booking_no", 'booking_no', 'prod_id');
	}

	$program_data_array = array();
	$booking_program_arr = array();
	$planQty = array();
	$pogramQty = array();

	if ($db_type == 0)
	{
		$sql_plan = "SELECT mst_id, booking_no, po_id, yarn_desc as pre_cost_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, group_concat(distinct(dtls_id)) as prog_no, sum(program_qnty) as program_qnty, min(id) as id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1 $pr_booking_cond $all_booking_no_cond group by mst_id, booking_no, po_id, yarn_desc, body_part_id, fabric_desc, gsm_weight, dia, color_type_id";
	}
	else
	{
		$sql_plan = "SELECT mst_id, booking_no, po_id, yarn_desc as pre_cost_id, body_part_id, fabric_desc, gsm_weight, dia, color_type_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as prog_no, sum(program_qnty) as program_qnty, min(id) as id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and is_sales!=1 $pr_booking_cond $all_booking_no_cond group by mst_id, booking_no, po_id, yarn_desc, body_part_id, fabric_desc, gsm_weight, dia, color_type_id";//, yarn_desc
	}

	//echo $sql_plan;
	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan)
	{
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['mst_id'] = $rowPlan[csf('mst_id')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['prog_no'] .= ",". $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['program_qnty'] = $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['id'] = $rowPlan[csf('id')];
		$booking_program_arr[$rowPlan[csf('booking_no')]] .= $rowPlan[csf('prog_no')] . ",";
		$planQty[$rowPlan[csf('mst_id')]][$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('pre_cost_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['program_qnty'] = $rowPlan[csf('program_qnty')];
	}
	//echo "<pre>";
	//print_r($colorWiseQty);

	//Print Button Permission
	$print_report = return_field_value("format_id", "lib_report_template", "template_name=" . $company_name . "  and module_id=2 and report_id in(1) and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report);
	$print_report2 = return_field_value("format_id", "lib_report_template", "template_name=" . $company_name . "  and module_id=2 and report_id in(2) and is_deleted=0 and status_active=1");
	$format_ids2 = explode(",", $print_report2);
	$print_report3 = return_field_value("format_id", "lib_report_template", "template_name=" . $company_name . "  and module_id=2 and report_id in(3) and is_deleted=0 and status_active=1");
	$format_ids3 = explode(",", $print_report3);

	if (str_replace("'", "", $txt_booking_date) == "")
		$booking_date = "";
	else
		$booking_date = " and a.booking_date>=" . $txt_booking_date . "";

	//21.06.2020
	$resultSet = sql_select($sqlColor);
	$colorWiseQty = array();
	foreach ($resultSet as $row)
	{
		$colorWiseQty[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['desc']][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['gsm']][$row[csf('dia_width')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type']][$row[csf('fabric_color_id')]]['booking_qty'] = $row[csf('qnty')];
	}
	//echo "<pre>";
	//print_r($colorWiseQty);

	$bookingDataArr = array();
	foreach ($nameArray as $row)
	{
		$bookingDataArr[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['desc']][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['gsm']][$row[csf('dia_width')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type']]['booking_qty'] += $row[csf('qnty')];
	}
	//echo "<pre>";
	//print_r($bookingDataArr);

	$dataPlanQty = array();
	foreach($planQty as $planId=>$planArr)
	{
		foreach($planArr as $bookingNo=>$bookingArr)
		{
			foreach($bookingArr as $poId=>$poArr)
			{
				foreach($poArr as $precostId=>$precostArr)
				{
					foreach($precostArr as $bodyPart=>$bodyPartArr)
					{
						foreach($bodyPartArr as $febricId=>$febricArr)
						{
							foreach($febricArr as $gsm=>$gsmArr)
							{
								foreach($gsmArr as $dia=>$diaArr)
								{
									foreach($diaArr as $colorType=>$row)
									{
										$dataPlanQty[$planId]['planQty'] += number_format($row['program_qnty'], 2, '.', '');
										$dataPlanQty[$planId]['bookingQty'] += number_format($bookingDataArr[$bookingNo][$poId][$precostId][$bodyPart][$febricId][$gsm][$dia][$colorType]['booking_qty'], 2, '.', '');
									}
								}
							}
						}
					}
				}
			}
		}
	}
	// echo "<pre>";
	// print_r($dataPlanQty);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=4 and report_id=261 and is_deleted=0 and status_active=1");
	$fReportId=explode(",",$print_report_format);
	$fReportId=$fReportId[0];

	if ($type == 2)
	{
		$knit_qnty_array = return_library_array("SELECT a.booking_id, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 group by a.booking_id", "booking_id", "knitting_qnty");

		$found_prog_no = '';
		$booking_no = '';
		$not_found_prog_array = array();
		$bookingType = array();

		foreach ($nameArray as $row) {
			$plan_id = '';
			$gsm = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['gsm'];
			$dia = $row[csf('dia_width')];
			$desc = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['desc'];
			$determination_id = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['determination_id'];
			$color_type_id = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type'];

			$update_id = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['id'];
			$program_qnty = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['program_qnty'];
			$plan_id = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['mst_id'];
			$prog_no = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['prog_no'];

			if ($prog_no != "") {
				$found_prog_no .= $prog_no . ",";
			}

			$booking_no = $row[csf('booking_no')];
			$bookingType[$row[csf('booking_no')]][1] = $row[csf('booking_type')];
			$bookingType[$row[csf('booking_no')]][2] = $row[csf('is_short')];
			$bookingType[$row[csf('booking_no')]][3] = $row[csf('item_category')];
			$bookingType[$row[csf('booking_no')]][4] = $row[csf('fabric_source')];
			$bookingType[$row[csf('booking_no')]][5] = $row[csf('is_approved')];
		}

		$found_prog_no = array_unique(explode(",", substr($found_prog_no, 0, -1)));
		$booking_program_no = array_unique(explode(",", substr($booking_program_arr[$booking_no], 0, -1)));
		//print_r($found_prog_no);die;


		$not_found_prog_array = array_diff($booking_program_no, $found_prog_no);
		if (count($not_found_prog_array) > 0) {
			if ($db_type == 0) {
				$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in(" . implode(",", $not_found_prog_array) . ") and is_sales!=1 group by dtls_id", "dtls_id", "po_id");
			} else {
				$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in(" . implode(",", $not_found_prog_array) . ") and is_sales!=1 group by dtls_id", "dtls_id", "po_id");
			}

			$po_array = array();
			$costing_sql = sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name");
			foreach ($costing_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			}
			?>
			<fieldset style="width:2410px;">
				<input type="button" value="Delete Program" name="generate" id="generate" class="formbutton"
				style="width:150px" onClick="delete_prog()"/>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2600" class="rpt_table">
					<thead>
						<th width="40">&nbsp;</th>
						<th width="40">SL</th>
						<th width="100">Party Name</th>
						<th width="60">Program No</th>
						<th width="80">Program Date</th>
						<th width="80">Start Date</th>
						<th width="80">T.O.D</th>
						<th width="70">Buyer</th>
						<th width="110">Booking No</th>
						<th width="90">Job No</th>
						<th width="130">Order No</th>
						<th width="110">Style</th>
						<th width="80">Dia / GG</th>
						<th width="100">Distribution Qnty</th>
						<th width="80">M/C no</th>
						<th width="70">Status</th>
						<th width="140">Fabric Desc.</th>
						<th width="100">Color Range</th>
						<th width="100">Color Type</th>
						<th width="80">Stitch Length</th>
						<th width="80">Sp. Stitch Length</th>
						<th width="80">Draft Ratio</th>
						<th width="70">Fabric Gsm</th>
						<th width="70">Fabric Dia</th>
						<th width="80">Width/Dia Type</th>
						<th width="100">Program Qnty</th>
						<th width="100">Knitting Qnty</th>
						<th width="130">Remarks</th>
						<th></th>
					</thead>
				</table>
				<div style="width:2600px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table"
					id="tbl_list_search">
					<?
					$i = 1;
					$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and b.id in(" . implode(",", $not_found_prog_array) . ") and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_sales!=1 group by b.id, a.company_id, a.buyer_id, a.booking_no, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks order by b.machine_dia, b.machine_gg, b.id";
						 //echo $sql;
					$nameArray = sql_select($sql);
					foreach ($nameArray as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];

						$machine_no = '';
						$machine_id = explode(",", $row[csf("machine_id")]);
						foreach ($machine_id as $val) {
							if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
						}

						$po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
						$po_no = '';
						$style_ref = '';
						$job_no = '';

						foreach ($po_id as $val) {
							if ($po_no == '') $po_no = $po_array[$val]['no']; else $po_no .= "," . $po_array[$val]['no'];
							if ($style_ref == '') $style_ref = $po_array[$val]['style_ref'];
							if ($job_no == '') $job_no = $po_array[$val]['job_no'];
						}

						$item_category = $bookingType[$row[csf('booking_no')]][3];
						$fabric_source = $bookingType[$row[csf('booking_no')]][4];
						$is_approve = $bookingType[$row[csf('booking_no')]][5];

						$knitting_qnty = $knit_qnty_array[$row[csf('id')]];
						if ($knitting_qnty > 0) $disabled = "disabled='disabled'"; else $disabled = "";

						if ($row[csf('knitting_source')] == 1) $knitting_source = $company_arr[$row[csf('knitting_party')]];
						else if ($row[csf('knitting_source')] == 3) $knitting_source = $supllier_arr[$row[csf('knitting_party')]];
						else $knitting_source = "&nbsp;";

						if (!in_array($machine_dia_gg, $machine_dia_gg_array)) {
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="29"><b>Machine Dia:- <?php echo $machine_dia_gg; ?></b></td>
							</tr>
							<?
							$machine_dia_gg_array[] = $machine_dia_gg;
						}

						$pre = '';
						if ($bookingType[$row[csf('booking_no')]][1] != 4) {
							if ($bookingType[$row[csf('booking_no')]][2] == 1) {
								$pre = "(S)";
							} else {
								$pre = "(M)";
							}
						}
			 			?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')"  id="tr_<? echo $i; ?>">
							<td width="40" align="center" valign="middle">
								<input type="checkbox" id="tbl_<? echo $i; ?>" name="check[]" <? echo $disabled; ?>/>
								<input id="promram_id_<? echo $i; ?>" name="promram_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>"/>
							</td>
							</td>
							<td width="40"><? echo $i; ?></td>
							<td width="100"><p><? echo $knitting_source; ?></p></td>
							<td width="60" align="center"><a href='##' onClick="generate_report2(<? echo $row[csf('company_id')] . "," . $row[csf('id')]. ",'" . $fReportId."'"; ?>)"><? echo $row[csf('id')]; ?></a>&nbsp;
							</td>
							<td width="80" align="center"><? echo change_date_format($row[csf('program_date')]); ?></td>
							<td width="80" align="center">
								<? if ($row[csf('start_date')] != "0000-00-00") echo change_date_format($row[csf('start_date')]); ?>
							</td>
							<td width="80" align="center">
								<? if ($row[csf('end_date')] != "0000-00-00") echo change_date_format($row[csf('end_date')]); ?>
							</td>
							<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
							<td width="110"><p><? echo $row[csf('booking_no')] . $pre; ?></p></td>
							<td width="90"><p><? echo $job_no; ?></p></td>
							<td width="130">
								<div style="word-wrap:break-word; width:129px"><? echo $po_no; ?></div>
							</td>
							<td width="110"><p><? echo $style_ref; ?></p></td>
							<td width="80"><p><? echo $machine_dia_gg; ?></p></td>
							<td align="right"
							width="100"><? echo number_format($row[csf('distribution_qnty')], 2); ?></td>
							<td width="80"><p><? echo $machine_no; ?></p></td>
							<td width="70"><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p>
							</td>
							<td width="140"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="100"><p><? echo $color_range[$row[csf('color_range')]] ?>&nbsp;</p></td>
							<td width="100"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
							<td align="right" width="80"><? echo number_format($row[csf('draft_ratio')], 2); ?></td>
							<td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
							<td width="80"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</td>
							<td align="right" width="100">
								<input type="text" class="text_boxes_numeric" name="prog_qty[]"
								id="prog_qty_<? echo $i; ?>" value="<? echo $row[csf('program_qnty')]; ?>"
								style="width:80px"/>
							</td>
							<td align="right" width="100"><? echo number_format($knitting_qnty, 2); ?></td>
							<td width="130"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
							<td align="center"><input type="button" value="Update"
								onClick="fnc_update(<? echo $i; ?>);" class="formbutton"
								style="width:80px"></td>
							</tr>
							<?
							$sub_tot_program_qnty += $row[csf('program_qnty')];
							$sub_tot_knitting_qnty += $knitting_qnty;

							$tot_program_qnty += $row[csf('program_qnty')];
							$tot_knitting_qnty += $knitting_qnty;

							$i++;
						}
						?>
					</table>
				</div>
			</fieldset>
			<?
		} else {
			echo "<div style='width:1100px' align='center'><font style='color:#F00; font-size:17px; font-weight:bold'>No Program Found.</font></div>";
		}
	}
	else // type 1
	{
		$po_array = array();
		$po_cond = " and id in(".implode(",",array_unique($po_id_arr)).")";
		$costing_sql = sql_select("select id, po_number, file_no, grouping from wo_po_break_down where status_active=1 $po_cond");
		foreach ($costing_sql as $row)
		{
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
		}
		//echo $approval_needed_or_not;die;
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset style="width:2100px;">
				<legend>Fabric Description Details</legend>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2080" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="70">Plan Id</th>
						<th width="70">Prog. No</th>
						<th width="115">Booking No</th>
						<th width="80">Booking Date</th>
						<th width="80">Start Date</th>
						<th width="80">T.O.D</th>
						<th width="70">Buyer</th>
						<th width="110">Order No</th>
						<th width="70"><? echo $company_arr[$company_name]; ?></th>
						<th width="120">Style</th>
						<th width="100">Internal Ref</th>
						<th width="100">File No</th>
						<th width="110">Body Part</th>
						<th width="100">Color Type</th>
						<th width="130">Fabric Desc.</th>
						<th width="70">Fabric Gsm</th>
						<th width="80">Fabric Dia</th>
						<th width="80">Width/Dia Type</th>
						<th width="90">Booking Qnty</th>
						<th width="110">Prog. Qnty</th>
						<th width="90">Balance Prog. Qnty</th>
						<th>Desc.Of Yarn</th>
					</thead>
				</table>
				<div style="width:2097px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2080" class="rpt_table" id="tbl_list_search">
						<tbody>
						<?
						$i = 1;
						$k = 1;
						$z = 1;
						$dia_array = array();
						foreach ($nameArray as $row)
						{
							$plan_id = '';
							$print_btn = '';
							$gsm = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['gsm'];
							$dia = $row[csf('dia_width')];
							$desc = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['desc'];
							$determination_id = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['determination_id'];
							$color_type_id = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type'];

							if ($row[csf('booking_type')] == 4)
							{
								$booking_type = 3;
								$pre = '';
							}
							else
							{
								$booking_type = $row[csf('is_short')];
								if ($row[csf('is_short')] == 1)
								{
									$pre = "(S)";
								}
								else
								{
									$pre = "(M)";
								}
							}

							if ($booking_type == 2) //Main Fabric booking
							{
								$row_id=$format_ids[0];

								if ($row_id == 1) {
									$print_btn = "<a href='#'   onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_gr','" . $i . "')\"> " . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 2) {
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 3) {
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report3','" . $i . "')\"> " . $row[csf('booking_no')] . $pre . " <a/>";
								}

								else if ($row_id == 4) {
									$print_btn = "<a href='#' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report1','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}

								else if ($row_id == 5) {
									$print_btn = "<a href='#' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report2','" . $i . "')\"> " . $row[csf('booking_no')] . $pre . " <a/>";

								}
								else if ($row_id == 6) {
									$print_btn = "<a href='#' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report4','" . $i . "')\">" . $row[csf('booking_no')] . $pre . "<a/>";
								}

								else if ($row_id == 7) {
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report5','" . $i . "')\"> " . $row[csf('booking_no')] . $pre . "<a/>";
								}

								else if ($row_id == 45) //Print Button 1
								{
									$print_btn = "<a href='#'  value='Urmi-" . $row[csf('booking_no')] . $pre . "' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_urmi','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 53) //JK
								{
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_jk','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 28) //AKH
								{
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_akh','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 85) //Print Button 3
								{
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','print_booking_3','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 73) //Print Booking MF
								{
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_mf','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 93) //PrintB9
								{
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_libas','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 269) //Print Booking Knit Asia
								{
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_knit','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 719)
								{
									//$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";

									$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $po_id_str . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
								else if ($row_id == 370)
								{
									//$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";

									$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
									$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $po_id_str . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_print19','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
								}
							}
							else if ($booking_type == 1) //Short Fabric booking
							{
								foreach ($format_ids2 as $row_id)
								{
									if ($row_id == 8) {
										$print_btn = "<a href='#' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report','" . $i . "')\"> " . $row[csf('booking_no')] . $pre . "<a/>";
									}
									if ($row_id == 9) {

										$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report3','" . $i . "')\">" . $row[csf('booking_no')] . $pre . "<a/>";
									}
									if ($row_id == 10) {
										$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report4','" . $i . "')\">" . $row[csf('booking_no')] . $pre . "<a/>";
									}

									if ($row_id == 46) {
										$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_urmi','" . $i . "')\">" . $row[csf('booking_no')] . $pre . "<a/>";
									}
                                    if ($row_id == 28) //AKH
                                    {
                                    	$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_jk','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
                                    }
                                }
                            }
							else if ($booking_type == 3) //SM Fabric booking
							{
								foreach ($format_ids3 as $row_id)
								{
									if ($row_id == 38) {
										$print_btn = "<a href='#'   onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report','" . $i . "')\">" . $row[csf('booking_no')] . $pre . "<a/>";
									}
									if ($row_id == 39) {
										$print_btn = "<a href='#' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report2','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
									}
									if ($row_id == 64) {
										$print_btn = "<a href='#' onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report3','" . $i . "')\">" . $row[csf('booking_no')] . $pre . "<a/>";
									}
                                    if ($row_id == 28) //AKH
                                    {
                                    	$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $row[csf('po_break_down_id')] . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report_jk','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
                                    }
                                }
                            }

							//for booking no
							if($print_btn == '')
							{
								$print_btn = $row[csf('booking_no')];
							}

                            $update_id = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['id'];
                            $program_qnty = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['program_qnty'];
                            $plan_id = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['mst_id'];
                            $prog_no = $program_data_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id]['prog_no'];

                            $prog_no = implode(",", array_unique(explode(",", $prog_no)));
                            $balance_qnty = number_format($row[csf('qnty')], 2, '.', '') - number_format($program_qnty, 2, '.', '');
                            $start_date = $tna_array[$row[csf('po_break_down_id')]]['start_d'];
                            $end_date = $tna_array[$row[csf('po_break_down_id')]]['finish_d'];

                            if ($approval_needed_or_not == 1)
							{
                            	if ($row[csf('is_approved')] == 1)
								{
                            		if ($row[csf('booking_type')] == 4)
									{
                            			$entry_form = 13;
                            		}
									else
									{
                            			if ($row[csf('is_short')] == 1)
											$entry_form = 12;
										else
											$entry_form = 7;
                            		}

                            		$approved_booking = 1;

                            	}
								else
								{
                            		$approved_booking = 0;
                            	}
                            }
							else
							{
                            	$approved_booking = 1;
                            }

							//if (($planning_status == 2 && $balance_qnty <= 0) || ($planning_status == 1 && $balance_qnty > 0))

							// First Condition was "==". I (tofael) changed it to "<=" with consults of Mr. rasel, Mr. Swarup vai in 16-07-2020
							if (($planning_status == 2 && $dataPlanQty[$plan_id]['bookingQty'] <= $dataPlanQty[$plan_id]['planQty'] &&  $plan_id != '') || ($planning_status == 1 && ($dataPlanQty[$plan_id]['bookingQty'] > $dataPlanQty[$plan_id]['planQty'] || $plan_id == '')))
							{
                            	if ($z % 2 == 0)
                            		$bgcolor = "#E9F3FF";
                            	else
                            		$bgcolor = "#FFFFFF";

                            	if (!in_array($row[csf('dia_width')], $dia_array))
								{
                            		if ($k != 1)
									{
                            			?>
                            			<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
                            				<td colspan="19" align="right"><b>Sub Total</b></td>
                            				<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
                            				<td align="right"> <b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
                            				<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>
                            				<td>&nbsp;<input type="hidden" name="check[]" id="check_<? echo $i; ?>" value="0"/></td>
                            			</tr>
                            			<?
                            			$total_dia_qnty = 0;
                            			$total_program_qnty = 0;
                            			$total_balance = 0;
                            			$i++;
                            		}

                            		?>
                            		<tr bgcolor="#EFEFEF" id="tr_<? echo $i; ?>">
                            			<td colspan="23">
                            				<b>Dia/Width:- <?php echo $row[csf('dia_width')]; ?></b>
                            				<input type="hidden" name="check[]" id="check_<? echo $i; ?>" value="0"/>
                            			</td>
                            		</tr>
                            		<?
                            		$dia_array[] = $row[csf('dia_width')];
                            		$k++;
                            		$i++;
                            	}

                            	$reqsn_found_or_not = 0;
                            	$yarn_desc = '';
                            	$prod_id = $booking_item_array[$row[csf('booking_no')]];

                            	$prod_id = array_unique(explode(",", $prod_id));
                            	foreach ($prod_id as $value)
								{
                            		if ($yarn_desc == '')
										$yarn_desc = $yarn_desc_array[$value];
									else
										$yarn_desc .= ",<br>" . $yarn_desc_array[$value];
                            	}

                            	$show_desc = "<a href='#'  onClick=\"generate_yarn_desc('" . $yarn_desc  . "')\"> View <a/>";


                            	$body_part_type=return_library_array("select id, body_part_type from lib_body_part where status_active=1",'id','body_part_type');
								if($body_part_type[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']] == 40 || $body_part_type[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']] == 50)
								{
									if($body_part_type[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']] == 40)
									{
										$collarCufValidation = 1;
									}
									else if($body_part_type[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']] == 50)
									{
										$collarCufValidation = 2;
									}
								}
								else
								{
									$collarCufValidation = 0;
								}

                            	?>
                            	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer"
                            		onClick="selected_row('<? echo $i; ?>')" id="tr_<? echo $i; ?>">
                            		<td width="40"><? echo $z; ?></td>
                            		<td width="70" id="plan_id_<? echo $i; ?>"><? echo trim($plan_id); ?></td>
                            		<td width="70" id="prog_no_<? echo $i; ?>" style="word-break: break-all;" align="center">
                            			<?
                            			$print_program_no = "";
                            			$prog_no_arr = array_filter(array_unique(explode(",", $prog_no)));
                            			foreach ($prog_no_arr as $prog_no) {
                            				$print_program_no .= "<a href='##' onclick=\"generate_report2(" . $row[csf('company_id')] . "," . $prog_no . ",'" . $fReportId . "')\">" . $prog_no . "</a>,";
                            			}
                            			$print_program_no = chop($print_program_no, ",");
                            			echo trim($print_program_no);
                            			?>
                            		</td>
                            		<td width="115" align="center" id="booking_no_<? echo $i; ?>"><p><? echo $print_btn;?></p></td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                                    <td width="80" align="center" id="start_date_<? echo $i; ?>"><? if ($start_date != "") echo change_date_format($start_date); ?></td>
                                    <td width="80" align="center" id="end_date_<? echo $i; ?>"><? if ($end_date != "") echo change_date_format($end_date); ?></td>
                                    <td width="70" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                                    <td width="110" align="center"><p><? echo $po_array[$row[csf('po_break_down_id')]]['no']; ?></p></td>
                                    <td width="70" align="center" id="po_id_<? echo $i; ?>"><? echo $row[csf('po_break_down_id')]; ?></td>
                                    <td width="120" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                    <td width="100"><? echo $po_array[$row[csf('po_break_down_id')]]['grouping']; ?></td>
                                    <td width="100"><? echo $po_array[$row[csf('po_break_down_id')]]['file_no']; ?></td>
                                    <td width="110"><? echo $body_part[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']];
                                    ?></td>
                                    <td width="100" align="center"><p><? echo $color_type[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type']]; ?></p>
                                    </td>
                                    <td width="130" id="desc_<? echo $i; ?>" align="center"><p><? echo $desc; ?></p></td>
                                    <td width="70" id="gsm_weight_<? echo $i; ?>" align="center"><p><? echo $gsm; ?></p></td>
                                    <td width="80" id="dia_width_<? echo $i; ?>" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                                    <td width="80" align="center"><? echo $fabric_typee[$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['width_dia_type']]; ?></td>
                                    <td align="right" id="booking_qnty_<? echo $i; ?>" width="90" title="<? echo $row[csf('qnty')];?>"><? echo number_format($row[csf('qnty')], 2, '.', ''); ?></td>
                                    <td align="right" width="110" title="<? echo $program_qnty;?>"><? if ($program_qnty > 0) echo number_format($program_qnty, 2, '.', ''); ?></td>
                                    <td align="right" id="ballance_qnty_<? echo $i; ?>" title="<? echo $balance_qnty;?>" width="90"><? echo number_format($balance_qnty, 2, '.', ''); ?></td>


                                    <!-- this is for operation -->
                                    <td id="yarn_desc_<? echo $i; ?>" style="display: none;"><? echo $yarn_desc; ?></td>


                                    <td style="justify-content: center;text-align: center;"><? echo !empty($yarn_desc) ? $show_desc : ''; ?></td>


                                    <input type="hidden" name="buyer_id[]" id="buyer_id_<? echo $i; ?>"
                                    value="<? echo $row[csf('buyer_id')]; ?>"/>

                                    <input type="hidden" name="po_wise_bodypartId[]" id="po_wise_bodypartId_<? echo $i; ?>"
                                    value="<? echo $row[csf('po_break_down_id')]."_".$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']; ?>"/>

                                    <input type="hidden" name="body_part_id[]" id="body_part_id_<? echo $i; ?>"
                                    value="<? echo $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']; ?>"/>

                                    <input type="hidden" name="body_part_type[]" id="body_part_type_<? echo $i; ?>"
                                    value="<? echo $collarCufValidation; ?>"/>

                                    <input type="hidden" name="color_type_id[]" id="color_type_id_<? echo $i; ?>"
                                    value="<? echo $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['color_type']; ?>"/>
                                    <input type="hidden" name="determination_id[]" id="determination_id_<? echo $i; ?>"
                                    value="<? echo $determination_id; ?>"/>
                                    <?
                                    if ($costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['width_dia_type'] == "")
                                        $width_dia_type = 0;
                                    else
                                        $width_dia_type = $costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['width_dia_type'];
                                    ?>
                                    <input type="hidden" name="fabric_typee[]" id="fabric_typee_<? echo $i; ?>"
                                    value="<? echo $width_dia_type; ?>"/>
                                    <input type="hidden" name="pre_cost_id[]" id="pre_cost_id_<? echo $i; ?>"
                                    value="<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>"/>
                                    <input type="hidden" name="updateId[]" id="updateId_<? echo $i; ?>"
                                    value="<? echo $update_id; ?>"/><!-- Not Used -->
                                    <input type="hidden" name="reqsn_found_or_not[]"
                                    id="reqsn_found_or_not_<? echo $i; ?>"
                                    value="<? echo $reqsn_found_or_not; ?>"/>
                                    <input type="hidden" name="check[]" id="check_<? echo $i; ?>" value="1"/>
                                    <input type="hidden" name="approved[]" id="approved_<? echo $i; ?>"
                                    value="<? echo $approved_booking; ?>"/>
                                    <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>"
                                    value="<? echo $row[csf('booking_no')]; ?>"/>
                                    <?php
                                    $colorQtyStr = '';
                                    foreach($colorWiseQty[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]][$costing_per_id_library[$row[csf('pre_cost_fabric_cost_dtls_id')]]['body_part']][$desc][$gsm][$row[csf('dia_width')]][$color_type_id] as $colorId=>$colorArr)
                                    {
                                        if($colorQtyStr != '')
                                            $colorQtyStr .= ',';

                                        $colorQtyStr .= $colorId.'-'.number_format($colorArr['booking_qty'],2, '.', '');
                                    }
                                    ?>
                                    <input type="hidden" name="hdnColorQty[]" id="hdnColorQty_<? echo $i; ?>"
                                    value="<?php echo $colorQtyStr; ?>"/>
                            		</tr>
                            		<?

                            		$total_dia_qnty += number_format($row[csf('qnty')], 2, '.', '')*1;
                            		$total_program_qnty += number_format($program_qnty, 2, '.', '')*1;
                            		$total_balance += number_format($balance_qnty, 2, '.', '');

                            		$total_qnty += number_format($row[csf('qnty')], 2, '.', '')*1;
                            		$grand_total_program_qnty += number_format($program_qnty, 2, '.', '')*1;
                            		$grand_total_balance += number_format($balance_qnty, 2, '.', '')*1;

                            		$i++;
                            		$z++;
                            	}
                            }

                            if ($i > 1)
							{
                            	?>
                            	<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
                            		<td colspan="19" align="right"><b>Sub Total</b></td>
                            		<td align="right"><b><? echo number_format($total_dia_qnty, 2, '.', ''); ?></b></td>
                            		<td align="right"><b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
                            		<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>
                            		<td>&nbsp;<input type="hidden" name="check[]" id="check_<? echo $i; ?>" value="0"/></td>
                            	</tr>
                            	<?
                            }
                            ?>
                        </tbody>
                        <tfoot>
                        	<th colspan="19" align="right">Grand Total</th>
                        	<th align="right"><? echo number_format($total_qnty, 2, '.', ''); ?></th>
                        	<th align="right"><? echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
                        	<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>
                        	<th><input type="hidden" name="company_id" id="company_id" value="<? echo $company_name; ?>"/>
                        	<th><input type="hidden" name="hiddenVariableCollarCuff" id="hiddenVariableCollarCuff" value="<? echo $txtVariableCollarCuff; ?>"/>
                        	</th>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </form>
        <?
    }
    exit();
}

if ($action == "prog_qnty_popup")
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$current_date = date("d-m-Y");
	$start_date = trim($start_date);
	$end_date = trim($end_date);
	$dataArray = sql_select("select id, machine_dia, machine_gg, fabric_dia, stitch_length from fabric_mapping where mst_id=$determination_id and status_active=1 and is_deleted=0");

	$color_mixing_in_knittingplan = return_field_value("color_mixing_in_knitting_plan", "variable_settings_production", "company_name=$companyID and variable_list=53");

	if($color_mixing_in_knittingplan==1)
	{
		$color_mixing_in_knittingplan_yes = 1;
	}else{
		$color_mixing_in_knittingplan_yes = 0;
	}

	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][429] );
	?>
	<script>

		<?php echo "var field_level_data= ". $data_arr . ";\n"; ?>
		
		var permission = '<? echo $permission; ?>';
		var color_mixing_in_knittingplan_yes = '<? echo $color_mixing_in_knittingplan_yes; ?>';

		function openpage_machine() {

			var save_string = $('#save_data').val();
			var txt_machine_dia = $('#txt_machine_dia').val();
			var update_dtls_id = $('#update_dtls_id').val();
			var companyID = $('#cbo_knitting_party').val();  <? //echo $companyID; ?>
			var allowed_date_qnty_string = $('#allowed_date_qnty_string').val();
			var page_link = 'planning_info_entry_controller.php?action=machine_info_popup&save_string=' + save_string + '&companyID=' + companyID + '&txt_machine_dia=' + txt_machine_dia + '&update_dtls_id=' + update_dtls_id + '&allowed_date_qnty_string=' +allowed_date_qnty_string;
			var title = 'Machine Info';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_machine_no = this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id = this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string = this.contentDoc.getElementById("save_string").value;
				var hidden_machine_capacity = this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty = this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date = this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date = this.contentDoc.getElementById("hidden_max_date").value;
				var allowed_date_qnty_string = this.contentDoc.getElementById("hidden_all_allowed_date_qnty").value;

				$('#txt_machine_no').val(hidden_machine_no);
				$('#machine_id').val(hidden_machine_id);
				$('#save_data').val(save_string);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#txt_distribution_qnty').val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				$('#allowed_date_qnty_string').val(allowed_date_qnty_string);

                //var days_req=hidden_distribute_qnty*1/hidden_machine_capacity*1;
                //$('#txt_days_req').val(days_req.toFixed(2));
                days_req();
            }
        }

        function days_req() {
        	txt_start_date = $('#txt_start_date').val();
        	txt_end_date = $('#txt_end_date').val();

        	if (txt_start_date != "" && txt_end_date != "") {
        		var days_req = date_diff('d', txt_start_date, txt_end_date);
        		$('#txt_days_req').val(days_req + 1);
        	}
        	else {
        		$('#txt_days_req').val('');
        	}
        }

        function openpage_color() {
        	var hidden_color_id = $('#hidden_color_id').val();
        	var prog_no = $('#update_dtls_id').val();
        	var save_color_wise_prog_data = $('#hidden_color_wise_prog_data').val();
        	var hdnVariableCollarCuff = $('#hdnVariableCollarCuff').val();

        	var page_link = "planning_info_entry_controller.php?action=color_info_popup&companyID="+<? echo $companyID; ?>+"&po_id="+"<? echo $po_id; ?>"+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&booking_no="+"<? echo trim($booking_no); ?>"+"&dia="+"<?php echo $dia; ?>"+"&hidden_color_id="+hidden_color_id +"&save_color_wise_prog_data="+save_color_wise_prog_data +"&color_mixing_in_knittingplan_yes="+color_mixing_in_knittingplan_yes+"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no+"&hdnVariableCollarCuff="+hdnVariableCollarCuff+"&body_part_id="+"<? echo $body_part_id; ?>"+"&po_wise_bodypartId="+"<? echo $po_wise_bodypartId; ?>"; 
        	var title = 'Color Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,"width=670px,height=300px,center=1,resize=1,scrolling=0", '../');

        	emailwindow.onclose = function ()
        	{
        		var theform = this.contentDoc.forms[0];
        		var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;
        		var hidden_size_wise_prog_string = this.contentDoc.getElementById("hidden_size_wise_prog_string").value;
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#hidden_size_wise_prog_string').val(hidden_size_wise_prog_string);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);

        	}
        }




        function fnc_program_entry(operation)
        {
        	var knit_source = $("#cbo_knitting_source").val();
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][429]);?>' && knit_source != 3){
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][429]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][429]);?>')==false)
				{
					return;
				}
			}

			if(knit_source == 1){


				if (form_validation('cbo_knitting_party*txt_color*cbo_color_range*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*cbo_location_name*cbo_dia_width_type','cbo_knitting_party*Color*Color Range*Machine Dia*Machine GG*Program Quantity*Stitch Length*Location*Dia Width')==false)
        		{
        			return;
        		}
        	}else{
        		if (form_validation('cbo_knitting_party*txt_color*cbo_color_range*txt_machine_dia*txt_machine_gg*txt_program_qnty*txt_stitch_length*cbo_dia_width_type','knitting party*Color*Color Range*Machine Dia*Machine GG*Program Quantity*Stitch Length*Dia Width')==false)
        		{
        			return;
        		}
        	}

    		var hiddenProgramQnty = $("#hiddenProgramQnty").val()*1;
    		var balanceProgramQnty = $("#balanceProgramQnty").val()*1;
    		var program_qnty = $("#txt_program_qnty").val()*1;

    		if(operation == 0)
    		{
    			if(balanceProgramQnty  <  program_qnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(balanceProgramQnty);
    				return;
    			}
    		}
    		else if(operation == 1)
    		{
				if((program_qnty - hiddenProgramQnty).toFixed(2) > balanceProgramQnty)
    			{
    				alert("Program Qnty Cann't exceed Balance Qnty");
    				$("#txt_program_qnty").val(hiddenProgramQnty);
    				return;
    			}
    		}

        	if(operation == 0 || operation == 1){
        		var hidden_color_wise_total = $('#hidden_color_wise_total').val()*1;
        		var txt_program_qnty = $('#txt_program_qnty').val()*1;

        		if( hidden_color_wise_total != txt_program_qnty  )
        		{
        			alert('Mismatch Program quantity and Color Wise program quantity');
        			$('#txt_program_qnty').focus();
        			return;
        		}
        	}

        	var booking_qnty = $("#hdn_booking_qnty").val() * 1;
			var dataString = $("#hdn_data_text_frontEnd").val();
        	var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('cbo_knitting_source*cbo_knitting_party*cbo_subcontract_party*txt_color*txt_machine_dia*txt_machine_gg*cbo_machine_group*txt_program_qnty*txt_stitch_length*txt_spandex_stitch_length*txt_draft_ratio*machine_id*txt_machine_capacity*txt_distribution_qnty*cbo_knitting_status*txt_start_date*txt_end_date*txt_program_date*cbo_feeder*txt_remarks*txt_attention*save_data*updateId*update_dtls_id*cbo_color_range*cbo_dia_width_type*hidden_color_id*txt_fabric_dia*hidden_no_of_feeder_data*cbo_location_name*hidden_advice_data*hidden_collarCuff_data*txt_grey_dia*hiddenProgramQnty*balanceProgramQnty*hidden_count_feeding_data*txt_co_efficient*hidden_bodypartID_data*allowed_date_qnty_string*pic_up_po_ids*upd_plan_po_ids*txt_batch_no*hidden_color_wise_prog_data*hidden_size_wise_prog_string*txt_knitting_pdo', "../../") + '&companyID='+<? echo $companyID; ?>+
        	'&gsm=' + '<? echo trim($gsm); ?>' + '&dia=' + '<? echo trim($dia); ?>' + '&desc=' + '<? echo trim($desc); ?>' + '&start_date=' + '<? echo $start_date; ?>' + '&end_date=' + '<? echo $end_date; ?>' + '&determination_id='+'<? echo $determination_id; ?>'+'&booking_no=' + '<? echo trim($booking_no); ?>' + '&data='+dataString+'&body_part_id='+'<? echo $body_part_id; ?>'+'&color_type_id='+'<? echo $color_type_id; ?>'+ '&fabric_typee='+'<? echo $fabric_type; ?>'+ '&tot_booking_qnty='+'<? echo trim($booking_qnty); ?>'+'&buyer_id=' +'<? echo $buyer_id; ?>' +'&hdn_booking_qnty=' + booking_qnty+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&po_id="+"<? echo $po_id; ?>"+"&prog_id="+"<? echo $prog_id; ?>";
			
        	freeze_window(operation);
        	http.open("POST", "planning_info_entry_controller.php", true);
        	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        	http.send(data);
        	http.onreadystatechange = fnc_program_entry_Reply_info;
        }

        function fnc_program_entry_Reply_info() {
        	if (http.readyState == 4) {
                //release_freezing();return;//alert(http.responseText);
                var reponse = trim(http.responseText).split('**');
                console.log(http.responseText);

                show_msg(reponse[0]);

                if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2)) {
                	var progBalance = 0;
                	var PreProgramQnty = $("#hiddenProgramQnty").val()*1;
                	if(reponse[0] == 0 ){
                		progBalance = $("#balanceProgramQnty").val()*1 - $("#txt_program_qnty").val()*1;
                	}
                	else if(reponse[0] == 1 )
                	{
                		progBalance = $("#balanceProgramQnty").val()*1 + PreProgramQnty - $("#txt_program_qnty").val()*1 ;
                	}

                	reset_form('programQnty_1', '', '', 'txt_start_date,<? echo $start_date; ?>*txt_end_date,<? echo $end_date;?>*txt_program_date,<? echo $current_date;?>', '', 'hdn_booking_qnty*txt_fabric_dia*hdnVariableCollarCuff*hidden_bodypartID_data');

                	$('#updateId').val(reponse[1]);
					var dtls_id = reponse[3];
					if($('#update_dtls_id').val() != '')
					{
						dtls_id = dtls_id+','+$('#update_dtls_id').val();
					}
					$('#update_dtls_id').val(dtls_id);

                	show_list_view(reponse[1]+'_'+'<?php echo trim($dia);?>'+'_'+dtls_id, 'planning_info_details', 'list_view', 'planning_info_entry_controller', '');
                	set_button_status(0, permission, 'fnc_program_entry', 1);

                    $("#txt_program_qnty").val(progBalance.toFixed(2));
                    $("#balanceProgramQnty").val(progBalance.toFixed(2));

                    $('#cbo_knitting_source').attr('disabled', false);
                    $('#cbo_knitting_party').attr('disabled', false);

                }
                else if (reponse[0] == 13 || reponse[0] == 14) {
              	alert(reponse[1]);
                }
				else if (reponse[0] == 23)
				 {
    			alert(reponse[1]);
				 release_freezing();
    			return;
    		    }
                release_freezing();
            }
        }

        function active_inactive() {
        	var knitting_source = document.getElementById('cbo_knitting_source').value;

        	reset_form('', '', 'txt_machine_no*machine_id*txt_machine_capacity*txt_distribution_qnty*txt_days_req*cbo_location_name', 'txt_start_date,<? echo $start_date; ?>*txt_end_date,<? echo $end_date; ?>*txt_program_date,<? echo $current_date; ?>', '', '');

        	if (knitting_source == 1) {
        		document.getElementById('txt_machine_no').disabled = false;
        		document.getElementById('cbo_location_name').disabled = false;
        	}
        	else {
        		document.getElementById('txt_machine_no').disabled = true;
        		document.getElementById('cbo_location_name').disabled = true;
        	}
        }

        function openpage_feeder() {
        	var no_of_feeder_data = $('#hidden_no_of_feeder_data').val();
        	var color_type_id ='<? echo $color_type_id; ?>';

        	if (!(color_type_id == 2 || color_type_id == 3 || color_type_id == 4 || color_type_id == 33)) {
        		alert("Only for Stripe");
        		return;
        	}

        	var page_link = 'planning_info_entry_controller.php?action=feeder_info_popup&no_of_feeder_data=' + no_of_feeder_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
        	var title = 'Stripe Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_no_of_feeder_data = this.contentDoc.getElementById("hidden_no_of_feeder_data").value;

        		$('#hidden_no_of_feeder_data').val(hidden_no_of_feeder_data);
        	}
        }

        function openpage_collarCuff() {
        	var collarCuff_data = $('#hidden_collarCuff_data').val();
        	var hidden_bodypartID_data = $('#hidden_bodypartID_data').val();
        	var update_dtls_id = $('#update_dtls_id').val();
        	if (update_dtls_id == "") {
        		alert("Save Data First");
        		return;
        	}
        	var page_link = 'planning_info_entry_controller.php?action=collarCuff_info_popup&collarCuff_data=' + collarCuff_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id +'&hidden_bodypartID_data='+hidden_bodypartID_data;
        	var title = 'Collar & Cuff Measurement Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=300px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var hidden_collarCuff_data = this.contentDoc.getElementById("hidden_collarCuff_data").value;

        		$('#hidden_collarCuff_data').val(hidden_collarCuff_data);
        	}
        }

        function openpage_advice() {
        	var hidden_advice_data = $('#hidden_advice_data').val();

        	var page_link = 'planning_info_entry_controller.php?action=advice_info_popup&hidden_advice_data=' + hidden_advice_data;
        	var title = 'Advice Info';

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=200px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var advice_data = this.contentDoc.getElementById("txt_advice").value;
        		$('#hidden_advice_data').val(advice_data);
        	}
        }

		function openpage_count_feeding(){
			var count_feeding_data = $('#hidden_count_feeding_data').val();
			var update_dtls_id = $('#update_dtls_id').val();
			if (update_dtls_id == "") {
				alert("Save Data First");
				return;
			}
			var page_link = 'planning_info_entry_controller.php?action=count_feeding_data_popup&count_feeding_data=' + count_feeding_data + '&pre_cost_id=' + '<? echo $pre_cost_id; ?>&update_dtls_id=' + update_dtls_id;
			var title = 'Count Feeding';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=300px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_count_feeding_data = this.contentDoc.getElementById("hidden_count_feeding_data").value;
				$('#hidden_count_feeding_data').val(hidden_count_feeding_data);
			}
		}

		function balance_cal()
		{
			$("#hiddenProgramQnty").val($("#txt_program_qnty").val()*1);
		}

		function getValutAttention(knit_source_id)
		{
				//alert(knit_source_id);  return;
				var knit_source_id=$("#cbo_knitting_source").val()*1;

				if(knit_source_id==1)
				{
					var inHouse_knit_party_id=$("#cbo_knitting_party").val()*1;
					get_php_form_data(knit_source_id+'*'+inHouse_knit_party_id, "check_last_attention_action", "planning_info_entry_controller" );
				}
				else
				{
					var outBound_knit_party_id=$("#cbo_knitting_party").val()*1;
					get_php_form_data(knit_source_id+'*'+outBound_knit_party_id, "check_last_attention_action", "planning_info_entry_controller" );

				}
				return;
		}
		function fn_knit_production(knit_source_id){
			if(knit_source_id==1){
				$("#location_caption").removeClass("change_color2");
				$("#location_caption").addClass("change_color");
			}else{
				$("#location_caption").removeClass("change_color");
				$("#location_caption").addClass("change_color2");
			}
			var knit_sys = $("#hidden_knit_sys").val();
			var hidden_knit_source = $("#hidden_knitting_source").val();
			var companyID = $('#hidden_company').val();
			if(knit_sys != ""){
				alert("Knitting Source Can't Change.Knitting Production Found! ID-"+knit_sys)
				$("#cbo_knitting_source").val(hidden_knit_source);
        	load_drop_down( 'planning_info_entry_controller', hidden_knit_source+'**'+companyID, 'load_drop_down_knitting_party','knitting_party');//class="must_entry_caption"
    		}
		    return;
		}

		$(document).ready(function () {
			$('#txt_stitch_length').keyup(function() {
	        	var th = $(this);
		        th.val( th.val().replace(/[^a-zA-Z0-9,. -]+$/, function(str) {
		        	return '';
	        	}));
	    	});

			//Disable Ctrl+V
	    	var ctrlDown = false,
	        ctrlKey = 17,
	        cmdKey = 91,
	        vKey = 86,
	        cKey = 67;

		    $(document).keydown(function(e) {
		        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
		    }).keyup(function(e) {
		        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
		    });

		    $("#txt_stitch_length").keydown(function(e) {
		        if (ctrlDown && (e.keyCode == vKey || e.keyCode == cKey)) return false;
		    });

		    //Disable part of page
		    $('#txt_stitch_length').bind('cut copy paste', function (e) {
		        e.preventDefault();
		    });
    	});

	</script>
	</head>
	<body>
		<div align="center">
			<? echo load_freeze_divs("../../", $permission, 1); ?>
			<form name="programQnty_1" id="programQnty_1">
				<fieldset style="width:900px;">
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="890"
					align="center">
					<thead>
						<th width="160">Fabric Description</th>
						<th width="60">GSM</th>
						<th width="60">Dia</th>
						<th width="80">Booking Qnty</th>
						<th width="80">TNA Start Date</th>
						<th width="80">TNA Finish Date</th>
						<th>Description Of Yarn</th>
					</thead>
					<tr bgcolor="#FFFFFF">
						<td><p><? echo $desc; ?></p></td>
						<td><? echo $gsm; ?></td>
						<td><? echo $dia; ?></td>
						<td align="right"><? echo number_format($booking_qnty, 2); ?></td>
						<td align="center"><? echo $start_date; ?></td>
						<td align="center"><? echo $end_date; ?></td>
						<td><p><? echo $desc_of_yarn; ?></p></td>
					</tr>
				</table>
			</fieldset>
			<fieldset style="width:900px; margin-top:5px;">
				<legend>New Entry</legend>
				<input type="hidden" id="hdn_booking_qnty" name="hdn_booking_qnty" value="<? echo $booking_qnty; ?>"/>
				<input type="hidden" id="hdn_data_text_frontEnd" name="hdn_data_text_frontEnd"/>
				<table width="900" align="center" border="0">
					<tr>
						<td>Knitting Source</td>
						<td>
							<?
							echo create_drop_down("cbo_knitting_source", 152, $knitting_source, "", 1, "-- Select --", 0, "active_inactive();load_drop_down( 'planning_info_entry_controller', this.value+'**'+$companyID, 'load_drop_down_knitting_party','knitting_party'); load_drop_down( 'planning_info_entry_controller',$companyID, 'load_drop_down_location', 'location_td' ); getValutAttention(this.value);fn_knit_production(this.value);", 0, '1,3');
							?>
							<input type="hidden" name="hidden_knitting_source" id="hidden_knitting_source" value="">
							<input type="hidden" name="hidden_company" id="hidden_company" value="<? echo $companyID ?>">
							<input type="hidden" name="hidden_knit_sys" id="hidden_knit_sys" value="">
							<input type="hidden" name="pic_up_po_ids" id="pic_up_po_ids" value="<? echo $po_id; ?>">
							<input type="hidden" name="upd_plan_po_ids" id="upd_plan_po_ids" value="">
							<input type="hidden" name="hdnVariableCollarCuff" id="hdnVariableCollarCuff" value="<? echo $hdnVariableCollarCuff; ?>">

						</td>
						<td class="must_entry_caption">Knitting Party</td>
						<td id="knitting_party">
							<?
							echo create_drop_down("cbo_knitting_party", 152, $blank_array, "", 1, '--Select Knit Party--', 0, "load_drop_down( 'planning_info_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); getValutAttention(this.value);");
							?>
						</td>
						<td>Sub-Subcontract</td>
						<td id="subContract">
							<?
							echo create_drop_down("cbo_subcontract_party", 175, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--","", "");
							?>
						</td>
					</tr>
					<tr>
						<td class="must_entry_caption">Color</td>
						<td>
							<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:140px;"
							placeholder="Browse" onClick="openpage_color();" readonly/>
							<input type="hidden" name="hidden_color_id" id="hidden_color_id" readonly/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data"
							readonly>
							<input type="hidden" name="hidden_size_wise_prog_string" id="hidden_size_wise_prog_string"
							readonly>

							<input type="hidden" name="hidden_color_wise_total" id="hidden_color_wise_total"
							readonly>
						</td>
						<td class="must_entry_caption">Color Range</td>
						<td>
							<?
							echo create_drop_down("cbo_color_range", 152, $color_range, "", 1, "-- Select --", 0, "");
							?>
						</td>
						<td class="must_entry_caption">Machine Dia</td>
						<td>
							<input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes_numeric"
							style="width:60px;" maxlength="3" title="Maximum 3 Character"
							value="<? echo $dataArray[0][csf('machine_dia')]; ?>"/>
							<?
							echo create_drop_down("cbo_dia_width_type", 100, $fabric_typee, "", 1, "-- Select --", $fabric_type, "");
							?>
						</td>
					</tr>
					<tr>
						<td class="must_entry_caption">Machine GG</td>
						<td>
							<input type="text" name="txt_machine_gg" id="txt_machine_gg" class="text_boxes_numeric"
							style="width:30px;" maxlength="3" title="Maximum 3 Character" value="<? echo $dataArray[0][csf('machine_gg')]; ?>"/>
							<span id="machine_group_td">
							<?
							// echo create_drop_down("cbo_subcontract_party", 175, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--","", "");
							echo create_drop_down("cbo_machine_group", 100, "select machine_group from lib_machine_name where machine_group is not null and category_id=1 and status_active =1 and is_deleted=0 group by machine_group order by machine_group", "machine_group,machine_group", 1, "-- Machine Group --", 1, "");
							//echo create_drop_down("cbo_machine_group", 100, $blank_array, 0, "-- Select M.G --", 0, "");

							?>
							</span>
						</td>
						<td>Finish Fabric Dia</td>
						<td>
							<!--<input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes"
							style="width:140px;" value="<? echo $dataArray[0][csf('fabric_dia')]; ?>"/>-->
							<input type="text" name="txt_fabric_dia" id="txt_fabric_dia" class="text_boxes"
							style="width:140px;" value="<? echo $dia; ?>"/>
						</td>
						<td class="must_entry_caption">Program Qnty</td>
						<td>
							<input type="hidden" value="" id="hiddenProgramQnty">
							<input type="hidden" value="<? echo number_format($balance_qnty, 2, '.', '');?>" id="balanceProgramQnty">
							<input type="text" name="txt_program_qnty" id="txt_program_qnty" value="<? echo number_format($balance_qnty, 2, '.', '');?>" class="text_boxes_numeric"
							style="width:165px;" readonly/>
						</td>
					</tr>
					<tr>
						<td>Program Date</td>
						<td>
							<input type="text" name="txt_program_date" id="txt_program_date" class="datepicker"
							style="width:140px" value="<? echo $current_date; ?>" readonly>
						</td>
						<td class="must_entry_caption">Stitch Length</td>
						<td>
							<input type="text" name="txt_stitch_length" id="txt_stitch_length" class="text_boxes"
							style="width:140px;" value="<? echo $dataArray[0][csf('stitch_length')]; ?>" />
						</td>
						<td>Spandex Stitch Length</td>
						<td>
							<input type="text" name="txt_spandex_stitch_length" id="txt_spandex_stitch_length"
							class="text_boxes" style="width:165px;"/>
						</td>
					</tr>
					<tr>
						<td>Draft Ratio</td>
						<td>
							<input type="text" name="txt_draft_ratio" id="txt_draft_ratio" class="text_boxes_numeric"
							style="width:140px;"/>
						</td>
						<td>Machine No</td>
						<td>
							<input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes"
							placeholder="Double Click For Search" style="width:140px;"
							onDblClick="openpage_machine();" disabled="disabled" readonly/>
							<input type="hidden" name="machine_id" id="machine_id" class="text_boxes" readonly/>
						</td>
						<td>Machine Capacity</td>
						<td>
							<input type="text" name="txt_machine_capacity" id="txt_machine_capacity"
							placeholder="Display" class="text_boxes_numeric" style="width:165px;"
							disabled="disabled"/>
						</td>
					</tr>
					<tr>
						<td>Distribution Qnty</td>
						<td>
							<input type="text" name="txt_distribution_qnty" id="txt_distribution_qnty"
							placeholder="Display" class="text_boxes_numeric" style="width:65px;"
							disabled="disabled"/>
							<input type="text" name="txt_days_req" id="txt_days_req" placeholder="Days Req."
							class="text_boxes_numeric" style="width:60px;" disabled="disabled"/>
						</td>
						<td>Start Date</td>
						<td>
							<input type="text" name="txt_start_date" id="txt_start_date" class="datepicker"
							style="width:140px" value="<? echo $start_date; ?>" readonly>
						</td>
						<td>End Date & Batch No</td>
						<td width="20">
							<input type="text" name="txt_end_date" id="txt_end_date" class="datepicker"
							style="width:75px" value="<? echo $end_date; ?>" readonly>
							<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:55px;"/>
						</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>
							<?
							echo create_drop_down("cbo_knitting_status", 152, $knitting_program_status, "", 1, "--Select Status--", 0, "");
							?>
						</td>

						<td>Feeder</td>
						<td>
							<?
							$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");
							echo create_drop_down("cbo_feeder", 152, $feeder, "", 1, "--Select Feeder--", 0, "");
							?>
						</td>
						<td colspan="2">
							<input type="button" name="feeder" class="formbuttonplasminus" value="No Of Feeder"
							onClick="openpage_feeder();" style="width:100px"/>
							<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data"
							class="text_boxes"><b> &emsp; Program No.</b>
							<input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes"
							placeholder="Display" disabled style="width:90px" >
						</td>
					</tr>
					<tr>
						<td>Remarks</td>
						<td>
							<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"
							style="width:140px">
						</td>
						<td id="location_caption">Location</td>
						<td id="location_td">
							<?
							echo create_drop_down("cbo_location_name", 152, $blank_array, 1, "-- Select Location --", 0, "");
							?>
						</td>
						<td>
							<input type="button" name="feeder" class="formbuttonplasminus" value="Advice"
							onClick="openpage_advice();" style="width:100px"/>
							<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes">
						</td>

						<td colspan="2">
							<span style="padding-right: 5px;"><b>Knitting Coefficient</b></span>
							<input type="text" name="txt_co_efficient" id="txt_co_efficient" class="text_boxes_numeric"style="width:78px" >
						</td>

					</tr>
					<tr>
						<td>Grey fabric Dia</td>
						<td>
							<input type="text" name="txt_grey_dia" id="txt_grey_dia" class="text_boxes"
							style="width:140px">
						</td>
						<td>Attention</td>
						<td>
							<input type="text" name="txt_attention" id="txt_attention" class="text_boxes"
							style="width:140px">
						</td>

						<td>
							<input type="button" name="feeder" class="formbuttonplasminus" value="Count Feeding"
							onClick="openpage_count_feeding();" style="width:100px"/>
							<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data"
							class="text_boxes">
						</td>

						<td>
							<input type="button" name="feeder" class="formbuttonplasminus" value="Collar & Cuff"
							onClick="openpage_collarCuff();" style="width:100px"/>
							<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data"
							class="text_boxes">
							<input type="hidden" name="hidden_bodypartID_data" id="hidden_bodypartID_data"
							class="text_boxes" value="<? echo $body_part_id; ?>">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no"
							class="text_boxes" value="<? echo $booking_no; ?>">

						</td>
					</tr>
					<tr>

						<td>Knitting PDO</td>
						<td colspan="3">
							<input type="text" name="txt_knitting_pdo" id="txt_knitting_pdo" class="text_boxes"
							style="width:405px">
						</td>
					</tr>
					<tr>
	                    <td><? include("../../terms_condition/terms_condition.php"); terms_condition(429,'hidden_booking_no','../../'); ?>
	                    </td>
	                </tr>
					<tr>
						<td colspan="4" align="right" class="button_container">
							<?
							echo load_submit_buttons($permission, "fnc_program_entry", 0, 0, "reset_form('programQnty_1','','','txt_start_date,$start_date*txt_end_date,$end_date*txt_program_date,$current_date','','updateId*txt_color');", 1);
							?>
						</td>
						<td colspan="2" align="left" valign="top" class="button_container">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="parent.emailwindow.hide();" style="width:100px;"/>
							<input type="hidden" name="save_data" id="save_data" class="text_boxes">
							<input type="hidden" name="allowed_date_qnty_string" id="allowed_date_qnty_string" class="text_boxes">
							<input type="hidden" name="updateId" id="updateId" class="text_boxes"
							value="<? echo str_replace("'", '', $plan_id); ?>">
							<input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
						</td>
					</tr>
				</table>
			</fieldset>
			<div id="list_view" style="margin-top:5px">
				<?
				if (str_replace("'", '', $plan_id) != "") {
					?>
					<script>
						show_list_view('<? echo str_replace("'", '', $plan_id)."_".trim($dia)."_".$prog_id; ?>', 'planning_info_details', 'list_view', 'planning_info_entry_controller', '');
					</script>
					<?
				}
				?>
			</div>
		</form>
	</div>
	<style type="text/css">
		.change_color{color: blue;}
		.change_color2{color: black;}
	</style>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>

		// Retrieving the object from browser localStorage
		var stored_dataString = JSON.parse(localStorage.getItem('dataString'));
		var datas=stored_dataString['data'];
		$('#hdn_data_text_frontEnd').val(datas);
		localStorage.removeItem('dataString');

		setFieldLevelAccess('<?php echo $companyID;?>');
	</script>
	</html>
	<?
	exit();
}

if($action=="check_last_attention_action")
{
	$data=explode("*",$data);
	$data_array_attention=sql_select("select id,attention from ppl_planning_info_entry_dtls where knitting_source=$data[0] and knitting_party=$data[1] and is_deleted=0 and status_active=1 order by id desc ");
	foreach ($data_array_attention as $row)
	{
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		exit();
	}
}

if ($action == "planning_info_details")
{
	$expData = explode('_',$data);
	$prog_id=implode(",", array_filter(explode(',', $expData[2])));

	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	//$sql = "select id, knitting_source, knitting_party,mst_id,color_range, machine_dia, machine_gg, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, status, program_date,color_id from ppl_planning_info_entry_dtls where mst_id=".$expData[0]." and id in(".$prog_id.") and status_active = '1' and is_deleted = '0'";
	$sql = "select a.id, a.knitting_source, a.knitting_party,a.mst_id,a.color_range, a.machine_dia, a.machine_gg, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.status, a.program_date,a.color_id ,listagg(b.body_part_id, ',') within group (order by b.body_part_id) as body_part_id ,listagg(b.po_id, ',') within group (order by b.po_id) as po_id , listagg(b.po_id || '-' || coalesce(b.body_part_id, 0), ',')
        within group ( order by a.id )  as po_wise_bodyPartId
           from ppl_planning_info_entry_dtls a,ppl_planning_entry_plan_dtls b 
	where a.id=b.dtls_id and a.mst_id=b.mst_id and a.mst_id=".$expData[0]." and a.id in(".$prog_id.") and a.status_active = '1' and a.is_deleted = '0' and b.status_active = '1' and b.is_deleted = '0'  group by a.id, a.knitting_source, a.knitting_party,a.mst_id,a.color_range, a.machine_dia, a.machine_gg, a.program_qnty, a.stitch_length, a.spandex_stitch_length, a.draft_ratio, a.status, a.program_date,a.color_id";

	//echo $sql;

	$result = sql_select($sql);
	$progranNo="";
	foreach ($result as $row) {
		$progranNo.= $row[csf('id')].",";
	}
	$progranNo=chop($progranNo,",");

	$sql_yarn_issue =sql_select("select a.knit_id , a.requisition_no,b.mst_id from ppl_yarn_requisition_entry a,inv_transaction b where a.requisition_no=b.requisition_no and b.item_category=1 and b.transaction_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.knit_id in($progranNo) group by a.knit_id , a.requisition_no,b.mst_id");
	foreach ($sql_yarn_issue as $row) {
		if($row[csf('mst_id')]!="")
		{
			$yarn_issue_arr[$row[csf('knit_id')]]["yarn_mst_id"]=$row[csf('mst_id')];
		}
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1082" class="rpt_table">
		<thead>
			<th width="90">Knitting Source</th>
			<th width="100">Knitting Company</th>
			<th width="90">Color</th>
			<th width="90">Color Range</th>
			<th width="70">Machine Dia</th>
			<th width="70">Machine GG</th>
			<th width="80">Program Qnty</th>
			<th width="75">Stitch Length</th>
			<th width="80">Span. Stitch Length</th>
			<th width="70">Draft Ratio</th>
			<th width="75">Program Date</th>
			<th width="75">Program No.</th>
			<th>Status</th>
		</thead>
	</table>
	<div style="width:1100px; max-height:140px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1082" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('knitting_source')] == 1)
				$knit_party = $company_arr[$row[csf('knitting_party')]];
			else
				$knit_party = $supllier_arr[$row[csf('knitting_party')]];

			$color_name=explode(",",$row[csf('color_id')]);
			$color_id="";
			foreach ($color_name as $val) {
				if($val>0) $color_id .= $color_library[$val].",";
			}
			$color=chop($color_id,",");
			$yarnIssueCheck=$yarn_issue_arr[$row[csf('id')]]["yarn_mst_id"];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data('<? echo $row[csf('id')]."_".$expData[1]."_".$yarnIssueCheck."_".$row[csf('body_part_id')]."_".$row[csf('po_id')]."_".$row[csf('po_wise_bodyPartId')]; ?>','populate_data_from_planning_info', 'planning_info_entry_controller' );balance_cal();">
				<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
				<td width="100"><p><? echo $knit_party; ?></p></td>
				<td width="90"><p><? echo $color; ?></p></td>
				<td width="90"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
				<td width="70"><p><? echo $row[csf('machine_dia')]; ?></p></td>
				<td width="70"><? echo $row[csf('machine_gg')]; ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('program_qnty')], 2); ?></td>
				<td width="75"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('spandex_stitch_length')]; ?>&nbsp;</p></td>
				<td width="70" align="right"><? echo number_format($row[csf('draft_ratio')], 2); ?></td>
				<td width="75" align="right"><? echo change_date_format($row[csf('program_date')]); ?></td>
				<td width="75" align="right"><? echo $row[csf('id')]; ?></td>
				<td><p><? echo $knitting_program_status[$row[csf('status')]]; ?>&nbsp;</p></td>
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

if ($action == "populate_data_from_planning_info")
{
	//echo $data;
	$expData = explode('_', $data);
	$data = $expData[0];
	$dia = $expData[1];
	$yarnIssueID = $expData[2];
	$bodyPartIds = $expData[3];
	$poIDS = $expData[4];
	$po_wise_bodyPartId = $expData[5];

	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$sql = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=$data order by seq_no";
	$data_array = sql_select($sql);
	foreach ($data_array as $row)
	{
		$count_feeding_data_arr[]=$row[csf('seq_no')].'_'.$row[csf('count_id')].'_'.$row[csf('feeding_id')];
	}
	$count_feeding_data_arr_str=implode(',',$count_feeding_data_arr);

	$knit_sys = return_field_value("a.recv_number", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$data and b.status_active=1 and b.is_deleted=0", "recv_number");

	$sql ="SELECT a.id,a.knitting_source,a.knitting_party,a.subcontract_party,a.color_id,a.color_range,a.machine_dia,a.width_dia_type,a.machine_gg,a.machine_group,a.fabric_dia,a.program_qnty,a.stitch_length,a.spandex_stitch_length,a.draft_ratio,a.machine_id,a.machine_capacity,a.distribution_qnty,a.status,a.start_date,a.end_date,a.program_date,a.feeder,a.remarks,a.attention,a.co_efficient,a.batch_no,a.save_data,a.no_fo_feeder_data, a.location_id, a.advice, a.collar_cuff_data, a.grey_dia, listagg(b.po_id, ',') within group (order by b.po_id) as po_id, listagg(b.yarn_desc, ',') within group (order by b.yarn_desc) as pre_cost_id, b.company_id, b.booking_no,a.knitting_pdo
	from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,wo_po_break_down c
	where a.id=b.dtls_id and b.po_id=c.id and a.id=$data and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c. is_deleted=0 group by a.id,a.knitting_source,a.knitting_party,a.subcontract_party,a.color_id,a.color_range,a.machine_dia,a.width_dia_type,a.machine_gg,a.machine_group,a.fabric_dia,a.program_qnty,a.stitch_length,a.spandex_stitch_length, a.draft_ratio,a.machine_id,a.machine_capacity,a.distribution_qnty,a.status,a.start_date,a.end_date,a.program_date,a.feeder,a.remarks,attention,a.co_efficient,a.batch_no,a.save_data,a.no_fo_feeder_data, a.location_id, a.advice, a.collar_cuff_data, a.grey_dia, b.company_id, b.booking_no,a.knitting_pdo";

	$data_array = sql_select($sql);

	foreach ($data_array as $row)
	{
		echo "document.getElementById('upd_plan_po_ids').value 				= '" . $row[csf("po_id")] . "';\n";
		echo "document.getElementById('cbo_knitting_source').value 			= '" . $row[csf("knitting_source")] . "';\n";

		echo "document.getElementById('hidden_knitting_source').value 		= '" . $row[csf("knitting_source")] . "';\n";
		echo "document.getElementById('hidden_knit_sys').value 				= '" . $knit_sys . "';\n";
		echo "load_drop_down('planning_info_entry_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_party")] . "+'**1', 'load_drop_down_knitting_party','knitting_party');\n";

		$color = '';
		$color_id = explode(",", $row[csf("color_id")]);
		foreach ($color_id as $val) {
			if ($color == "") $color = $color_library[$val]; else $color .= "," . $color_library[$val];
		}
		echo "document.getElementById('knitting_party').value 				= '" . $row[csf("knitting_party")] . "';\n";
		echo "load_drop_down('planning_info_entry_controller', " .$row[csf("knitting_party")] . ", 'load_drop_down_location','location_td');\n";
		//echo "load_drop_down('planning_info_entry_controller', " .$row[csf("knitting_party")] . ", 'load_drop_down_machine_group','machine_group_td');\n";
		echo "document.getElementById('cbo_subcontract_party').value        = '" . $row[csf("subcontract_party")] . "';\n";
		echo "document.getElementById('txt_color').value 					= '" . $color . "';\n";
		echo "document.getElementById('hidden_color_id').value 				= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('hidden_color_wise_total').value 		= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 				= '" . $row[csf("color_range")] . "';\n";
		echo "document.getElementById('txt_machine_dia').value 				= '" . $row[csf("machine_dia")] . "';\n";
		echo "document.getElementById('cbo_dia_width_type').value 			= '" . $row[csf("width_dia_type")] . "';\n";
		echo "document.getElementById('txt_machine_gg').value 				= '" . $row[csf("machine_gg")] . "';\n";
		echo "document.getElementById('cbo_machine_group').value 			= '" . $row[csf("machine_group")] . "';\n";
		echo "document.getElementById('txt_fabric_dia').value 				= '" . $row[csf("fabric_dia")] . "';\n";
		echo "document.getElementById('txt_program_qnty').value 			= '" . $row[csf("program_qnty")] . "';\n";
		echo "document.getElementById('txt_stitch_length').value 			= '" . $row[csf("stitch_length")] . "';\n";
		echo "document.getElementById('txt_spandex_stitch_length').value 	= '" . $row[csf("spandex_stitch_length")] . "';\n";
		echo "document.getElementById('txt_draft_ratio').value 				= '" . $row[csf("draft_ratio")] . "';\n";
		echo "document.getElementById('txt_attention').value 				= '" . $row[csf("attention")] . "';\n";
		echo "document.getElementById('txt_co_efficient').value 			= '" . $row[csf("co_efficient")] . "';\n";
		echo "document.getElementById('txt_batch_no').value 				= '" . $row[csf("batch_no")] . "';\n";
		echo "active_inactive();\n";
		echo "document.getElementById('machine_id').value 					= '" . $row[csf("machine_id")] . "';\n";
		$machine_no = '';
		$machine_id = explode(",", $row[csf("machine_id")]);
		foreach ($machine_id as $val) {
			if ($machine_no == '') $machine_no = $machine_arr[$val]; else $machine_no .= "," . $machine_arr[$val];
		}
		echo "document.getElementById('txt_machine_no').value 				= '" . $machine_no . "';\n";
		echo "document.getElementById('txt_machine_capacity').value 		= '" . $row[csf("machine_capacity")] . "';\n";
		echo "document.getElementById('txt_distribution_qnty').value 		= '" . $row[csf("distribution_qnty")] . "';\n";
		echo "document.getElementById('cbo_knitting_status').value 			= '" . $row[csf("status")] . "';\n";
		echo "document.getElementById('txt_start_date').value 				= '" . change_date_format($row[csf("start_date")]) . "';\n";
		echo "document.getElementById('txt_end_date').value 				= '" . change_date_format($row[csf("end_date")]) . "';\n";
		echo "document.getElementById('txt_program_date').value 			= '" . change_date_format($row[csf("program_date")]) . "';\n";
		echo "document.getElementById('cbo_feeder').value 					= '" . $row[csf("feeder")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_knitting_pdo').value 			= '" . $row[csf("knitting_pdo")] . "';\n";
		echo "document.getElementById('save_data').value 					= '" . $row[csf("save_data")] . "';\n";
		echo "document.getElementById('hidden_no_of_feeder_data').value 	= '" . $row[csf("no_fo_feeder_data")] . "';\n";
		echo "document.getElementById('hidden_collarCuff_data').value 		= '" . $row[csf("collar_cuff_data")] . "';\n";
		echo "document.getElementById('cbo_location_name').value 			= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('hidden_bodypartID_data').value 		= '" . $row[csf("collar_cuff_data")] . "';\n";
		echo "document.getElementById('hidden_booking_no').value 			= '" . $row[csf("booking_no")] . "';\n";

		//=========
		if($db_type ==0)
		{
			$date_wise_breakdown_cond = " and date_wise_breakdown =''";
		}
		else
		{
			$date_wise_breakdown_cond = " and date_wise_breakdown is not null";
		}
		$machine_sql = sql_select("select machine_id,date_wise_breakdown from ppl_planning_info_machine_dtls where status_active = 1 and dtls_id = $data $date_wise_breakdown_cond");
		if(!empty($machine_sql))
		{
			$machine_data_string = "";
			foreach ($machine_sql as  $val)
			{
				$machine_data_string .= $val[csf("machine_id")]."=".$val[csf("date_wise_breakdown")]."__";
			}
			$machine_data_string = chop($machine_data_string,"__");
			echo "document.getElementById('allowed_date_qnty_string').value 		= '" . $machine_data_string . "';\n";
		}

		$advice = str_replace("\n", ";", $row[csf("advice")]);
		echo "document.getElementById('hidden_advice_data').value 			= '" .$advice. "';\n";
		echo "document.getElementById('hidden_count_feeding_data').value	= '" .$count_feeding_data_arr_str. "';\n";
		echo "document.getElementById('txt_grey_dia').value					= '" . $row[csf("grey_dia")] . "';\n";
		echo "document.getElementById('update_dtls_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_program_no').value 				= '" . $row[csf("id")] . "';\n";
		echo "days_req();\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_program_entry',1);\n";


		$booking_no = $row[csf("booking_no")];
		$company_id = $row[csf("company_id")];

		$expPoId = explode(',', $row[csf("po_id")]);
		$poIdArr = array();
		for($zs = 0; $zs < count($expPoId); $zs ++)
		{
			$poIdArr[$expPoId[$zs]] = $expPoId[$zs];
		}
		$po_id = implode(',', $poIdArr);

		$expPrecostId = explode(',', $row[csf("pre_cost_id")]);
		$precostIdArr = array();
		for($zss = 0; $zss < count($expPrecostId); $zss ++)
		{
			$precostIdArr[$expPrecostId[$zss]] = $expPrecostId[$zss];
		}
		$pre_cost_id = implode(',', $precostIdArr);

		$booking_no = $row[csf("booking_no")];

	}

	//for booking qty
	if ($dia!= "" || $db_type == 0)
	{
		$dia_cond = "b.dia_width like '%$dia%'";
	}
	else
	{
		$dia_cond = "b.dia_width is null";
	}

	//$sqlBookingQty = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=".$company_id." and a.item_category=2 and a.booking_no='".$booking_no."' and ".$dia_cond." and b.po_break_down_id in (".$po_id.") and b.pre_cost_fabric_cost_dtls_id in (".$pre_cost_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";

	$po_wise_bodypartIdArr = explode(",", $po_wise_bodyPartId);
	foreach ($po_wise_bodypartIdArr as $key => $value) {
		$po_wise_bodypartIdArr[$value]=$value;
	}

	$sql_primary = sql_select("select b.po_break_down_id,c.body_part_id,b.fabric_color_id, sum(b.grey_fab_qnty) as qnty 
	from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c 
	where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.gsm_weight=c.gsm_weight and b.construction=c.construction and b.copmposition=c.composition   and c.body_part_id in($bodyPartIds) and a.company_id=$company_id and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.po_break_down_id,c.body_part_id,b.fabric_color_id");

	$po_color_bookingReqQnty=array();
	foreach ($sql_primary as $row)
	{
		if($po_wise_bodypartIdArr[$row[csf('po_break_down_id')]."-".$row[csf('body_part_id')]]==$row[csf('po_break_down_id')]."-".$row[csf('body_part_id')])
		{
			$po_color_bookingReqQnty[$row[csf('fabric_color_id')]]["qnty"] +=  $row[csf('qnty')];

		}
	}
	$sqlBookingQty = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.gsm_weight=c.gsm_weight and b.construction=c.construction and b.copmposition=c.composition   and c.body_part_id in($bodyPartIds) and a.company_id=".$company_id." and a.item_category=2 and a.booking_no='".$booking_no."' and ".$dia_cond." and b.po_break_down_id in (".$po_id.") and b.pre_cost_fabric_cost_dtls_id in (".$pre_cost_id.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.fabric_color_id";

	$dataBookingQty = sql_select($sqlBookingQty);
	$bookingQtyArr = array();
	foreach($dataBookingQty as $zasu)
	{
		//$bookingQtyArr[$zasu[csf("fabric_color_id")]] = $zasu[csf("qnty")];
		$bookingQtyArr[$zasu[csf("fabric_color_id")]] = $po_color_bookingReqQnty[$zasu[csf('fabric_color_id')]]["qnty"];
	}

	//for color
	$sql_color_prog = "select id, plan_id, program_no, color_id, color_prog_qty,size_wise_prog_string from ppl_color_wise_break_down where program_no = $data and status_active=1 and is_deleted=0";
	$color_prog_data = sql_select($sql_color_prog);
	$colorTableId="";
	foreach ($color_prog_data as $colorRow)
	{
		$colorTableId.= $colorRow[csf("color_id")].",";

		$sizeString=explode("##", $colorRow[csf("size_wise_prog_string")]);


	}
	$colorTableId=chop($colorTableId,",");
	$sql_size_prog = sql_select("select id from ppl_size_wise_break_down where program_no = $data and color_id in($colorTableId) and status_active=1 and is_deleted=0 order by id asc");
	$sizeIndex=0;
	$sizeStrings="";
	foreach ($sql_size_prog as $sizeRow)
	{
		//echo $sizeRow[csf("id")]."stringxxxxxxxxxx";
		$sizeStrings.=$sizeString[$sizeIndex]."_".$sizeRow[csf("id")]."##";
		$sizeIndex++;
	}
	$sizeStrings=chop($sizeStrings,"##");

	if(count($color_prog_data>0))
	{
		$saveString = "";
		$saveStringForSizeWise = "";
		$totalProgQty = 0;

		foreach ($color_prog_data as $colorRow)
		{
			if($saveString=="")
			{
				$saveString =  $colorRow[csf("color_id")] . "_" . $colorRow[csf("color_prog_qty")]. "_" . $colorRow[csf("id")]. "_" . $bookingQtyArr[$colorRow[csf("color_id")]];
				//$saveStringForSizeWise = $colorRow[csf("size_wise_prog_string")];
				$saveStringForSizeWise = $sizeStrings;
			}
			else
			{
				$saveString .= "," . $colorRow[csf("color_id")] . "_" . $colorRow[csf("color_prog_qty")]. "_" . $colorRow[csf("id")]. "_" . $bookingQtyArr[$colorRow[csf("color_id")]];
				//$saveStringForSizeWise .= "**" . $colorRow[csf("size_wise_prog_string")];
				$saveStringForSizeWise .= "**" . $sizeStrings;
			}

			$totalProgQty += $colorRow[csf("color_prog_qty")];
		}
		echo "document.getElementById('hidden_color_wise_prog_data').value 	= '" . $saveString . "';\n";
		echo "document.getElementById('hidden_size_wise_prog_string').value 	= '" . $saveStringForSizeWise . "';\n";
		//echo "document.getElementById('hidden_total_prog_qty').value 	= '" . $totalProgQty . "';\n";
	}
		//This section for kniting source and kniting party would be disable on update mode if found Yarn Issue against this program
		if($yarnIssueID>0)
		{
			echo "document.getElementById('cbo_knitting_source').setAttribute('disabled','disabled');\n";
			echo "document.getElementById('cbo_knitting_party').setAttribute('disabled','disabled');\n";
		}
		else
		{
			echo "document.getElementById('cbo_knitting_source').removeAttribute('disabled');\n";
			echo "document.getElementById('cbo_knitting_party').removeAttribute('disabled');\n";
		}
	//for size
	/*$sql_size_prog = "select b.id, a.plan_id, a.program_no, a.color_id,grey_size_id,finish_size_id, per_kg,kg_wise_total_qnty from ppl_color_wise_break_down a,ppl_size_wise_break_down b where a.id=b.color_wise_mst_id and a.plan_id=b.plan_id and a.program_no=b.program_no and a.color_id=b.color_id and b.program_no = $data  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$size_prog_data = sql_select($sql_size_prog);
	if(count($size_prog_data>0))
	{
		$saveString = "";
		$saveStringForSizeWise = "";
		$totalProgQty = 0;

		foreach ($size_prog_data as $colorRow)
		{
			if($saveString=="")
			{
				$saveString =  $colorRow[csf("color_id")] . "_" . $colorRow[csf("color_prog_qty")]. "_" . $colorRow[csf("id")]. "_" . $bookingQtyArr[$colorRow[csf("color_id")]];
				$saveStringForSizeWise = $colorRow[csf("size_wise_prog_string")];
			}
			else
			{
				$saveString .= "," . $colorRow[csf("color_id")] . "_" . $colorRow[csf("color_prog_qty")]. "_" . $colorRow[csf("id")]. "_" . $bookingQtyArr[$colorRow[csf("color_id")]];
				$saveStringForSizeWise .= "**" . $colorRow[csf("size_wise_prog_string")];
			}

			$totalProgQty += $colorRow[csf("color_prog_qty")];
		}
		echo "document.getElementById('hidden_color_wise_prog_data').value 	= '" . $saveString . "';\n";
		echo "document.getElementById('hidden_size_wise_prog_string').value 	= '" . $saveStringForSizeWise . "';\n";
	}*/



	exit();
}

if ($action == "machine_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function calculate_qnty(tr_id) {
			var distribution_qnty = $('#txt_distribution_qnty_' + tr_id).val() * 1;
			if (distribution_qnty > 0) {
				$('#search' + tr_id).css('background-color', 'yellow');
			}
			else {
				$('#search' + tr_id).css('background-color', '#FFFFCC');
			}

			calculate_total_qnty('txt_distribution_qnty_', 'txt_total_distribution_qnty');
		}

		function calculate_total_qnty(field_id, total_field_id) {
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			var ddd = {dec_type: 2, comma: 0, currency: ''}

			math_operation(total_field_id, field_id, "+", tot_row, ddd);

		}

		function fnc_close() {
			var save_string = '';
			var allMachineId = '';
			var allMachineNo = '';
			var tot_capacity = '';
			var tot_distribution_qnty = '';
			var min_date = '';
			var max_date = '';
			var tot_row = $("#tbl_list_search tbody tr").length - 1;
			var allowed_date_qnty_string = '';

			for (var i = 1; i <= tot_row; i++) {
				var machineId = $('#txt_individual_id' + i).val();
				var machineNo = $('#txt_individual' + i).val();
				var capacity = $('#txt_capacity_' + i).val();
				var distributionQnty = $('#txt_distribution_qnty_' + i).val();
				var noOfDays = $('#txt_noOfDays_' + i).val();
				var startDate = $('#txt_startDate_' + i).val();
				var endDate = $('#txt_endDate_' + i).val();
				var allowedDateQnty = $('#txt_allowedDateQnty_' + i).val();

				if (distributionQnty * 1 > 0) {
					if (save_string == "") {
						save_string = machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate;
						allMachineId = machineId;
						allMachineNo = machineNo;
						allowed_date_qnty_string  =  allowedDateQnty;
					}
					else {
						save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate;
						allMachineId += "," + machineId;
						allMachineNo += "," + machineNo;

						allowed_date_qnty_string += "__" + allowedDateQnty;
					}

					if (min_date == '') {
						min_date = startDate;
					}

					if (date_compare(min_date, startDate) == false) {
						min_date = startDate;
					}

					if (date_compare(min_date, endDate) == false) {
						min_date = endDate;
					}

					if (max_date == '') {
						max_date = startDate;
					}

					if (date_compare(max_date, startDate) == true) {
						max_date = startDate;
					}

					if (date_compare(max_date, endDate) == true) {
						max_date = endDate;
					}

					tot_capacity = tot_capacity * 1 + capacity * 1;
					tot_distribution_qnty = tot_distribution_qnty * 1 + distributionQnty * 1;
				}
			}

			$('#hidden_machine_id').val(allMachineId);
			$('#hidden_machine_no').val(allMachineNo);
			$('#save_string').val(save_string);
			$('#hidden_machine_capacity').val(tot_capacity);
			$('#hidden_distribute_qnty').val(tot_distribution_qnty);
			$('#hidden_min_date').val(min_date);
			$('#hidden_max_date').val(max_date);
			$('#hidden_all_allowed_date_qnty').val(allowed_date_qnty_string);


			parent.emailwindow.hide();
		}

		function fn_add_date_field(row_no) {
			var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val() * 1;

			if (distribute_qnty == 0 || distribute_qnty < 0) {
				alert("Please Insert Distribution Qnty First.");
				$('#txt_startDate_' + row_no).val('');
				$('#txt_distribution_qnty_' + row_no).focus();
				return;
			}

			if ($('#txt_startDate_' + row_no).val() != "") {

				var txt_startDate = $('#txt_startDate_' + row_no).val();
				var txt_endDate = $('#txt_endDate_' + row_no).val();
				var machine_id = $('#txt_individual_id' + row_no).val();
				var capacity_qnty = $('#txt_capacity_' + row_no).val();
				var no_of_days = $('#txt_noOfDays_' + row_no).val();

				var data = machine_id + "**" + txt_startDate + "**" + distribute_qnty + "**" + capacity_qnty + "**" + no_of_days + "**" + '<? echo $update_dtls_id; ?>';
				var response = return_global_ajax_value(data, 'date_duplication_check', '', 'planning_info_entry_controller');
				var response = response.split("=");

				var days_req = $('#txt_noOfDays_' + row_no).val();

				days_req = Math.ceil(days_req);
				if (days_req > 0) {
					days_req = days_req - 1;
					$("#txt_endDate_" + row_no).val(add_days($('#txt_startDate_' + row_no).val(), days_req));
				}

				var first_dates = response[0].split(",");
				var firstDate = first_dates[0];
				var firstDate = $.trim(firstDate);
				//alert(txt_startDate + firstDate);
				if(txt_startDate != firstDate)
				{
					alert("Date Overlaping for this machine.");
					$('#txt_startDate_' + row_no).val('');
					$('#txt_endDate_' + row_no).val('');
					return;
				}
				else
				{
					$("#txt_endDate_" + row_no).val(response[1]);
					$("#txt_allowedDateQnty_" + row_no).val( machine_id + "=" + response[0] );  //date, qnty, time , machine no
				}


			}
		}

		function calculate_noOfDays(row_no) {
			var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val();
			var machine_capacity = $('#txt_capacity_' + row_no).val();

			var days_req = distribute_qnty * 1 / machine_capacity * 1;
			$('#txt_noOfDays_' + row_no).val(days_req.toFixed(2));

			$('#txt_startDate_' + row_no).val('');
			$('#txt_endDate_' + row_no).val('');
			$('#txt_allowedDateQnty_' + row_no).val('');

        	/*if (distribute_qnty * 1 > 0) {
        		fn_add_date_field(row_no);
        	}
        	else {
        		$('#txt_noOfDays_' + row_no).val('');
        		$('#txt_startDate_' + row_no).val('');
        		$('#txt_endDate_' + row_no).val('');
        	}*/
        }

    </script>

</head>

<body>
	<div style="width:830px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:820px; margin-top:10px; margin-left:5px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_id" id="hidden_machine_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_no" id="hidden_machine_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes"
				value="">
				<input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes"
				value="">
				<input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_all_allowed_date_qnty" id="hidden_all_allowed_date_qnty" class="text_boxes" value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="80">Floor</th>
						<th width="60">Machine No</th>
						<th width="60">Dia</th>
						<th width="60">GG</th>
						<th width="80">Group</th>
						<th width="90">Capacity</th>
						<th width="90">Distribution Qnty</th>
						<th width="60">No. Of Days</th>
						<th width="80">Start Date</th>
						<th>End Date</th>
					</thead>
				</table>
				<div style="width:818px; overflow-y:scroll; max-height:220px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$qnty_array = array();
						$save_string = explode(",", $save_string);

						for ($i = 0; $i < count($save_string); $i++) {
							$machine_wise_data = explode("_", $save_string[$i]);
							$machine_id = $machine_wise_data[0];
							$capacity = $machine_wise_data[2];
							$distribution_qnty = $machine_wise_data[3];
							$noOfDays = $machine_wise_data[4];
							$startDate = $machine_wise_data[5];
							$endDate = $machine_wise_data[6];

							$qnty_array[$machine_id]['capacity'] = $capacity;
							$qnty_array[$machine_id]['distribution'] = $distribution_qnty;
							$qnty_array[$machine_id]['noOfDays'] = $noOfDays;
							$qnty_array[$machine_id]['startDate'] = $startDate;
							$qnty_array[$machine_id]['endDate'] = $endDate;
						}

						$allowed_date_qnty_string = explode("__", $allowed_date_qnty_string);
						for ($i = 0; $i < count($allowed_date_qnty_string); $i++)
						{
							$machine_date_qnty = explode("=", $allowed_date_qnty_string[$i]);
							$machine_id = $machine_date_qnty[0];
							$qnty_array[$machine_id]['date_qnty'] = $machine_date_qnty[0] ."=". $machine_date_qnty[1];
						}
						/*echo "<pre>";
						print_r($qnty_array);*/

						$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

						$sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and company_id=$companyID order by seq_no";
						// and dia_width='$txt_machine_dia'company_id=$companyID and
						$result = sql_select($sql);

						$i = 1;
						$tot_capacity = 0;
						$tot_distribution_qnty = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$capacity = $qnty_array[$row[csf('id')]]['capacity'];
							if ($capacity == "") {
								$capacity = $row[csf('prod_capacity')];
							}

							$distribution_qnty = $qnty_array[$row[csf('id')]]['distribution'];

							if ($distribution_qnty > 0) $bgcolor = "yellow"; else $bgcolor = $bgcolor;

							$noOfDays = $qnty_array[$row[csf('id')]]['noOfDays'];
							$startDate = $qnty_array[$row[csf('id')]]['startDate'];
							$endDate = $qnty_array[$row[csf('id')]]['endDate'];
							$allowed_date_qnty =  $qnty_array[$row[csf('id')]]['date_qnty'];

							$tot_capacity += $capacity;
							$tot_distribution_qnty += $distribution_qnty;

							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>"
								value="<? echo $row[csf('id')]; ?>"/>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>"
								value="<? echo $row[csf('machine_no')]; ?>"/>
							</td>
							<td width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('machine_no')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gauge')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('machine_group')]; ?></p></td>
							<td width="90" align="center">
								<input type="text" name="txt_capacity[]" id="txt_capacity_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:75px" value="<? echo $capacity; ?>"
								onKeyUp="calculate_total_qnty('txt_capacity_','txt_total_capacity');calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="90">
								<input type="text" name="txt_distribution_qnty[]"
								id="txt_distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:75px" value="<? echo $distribution_qnty; ?>"
								onKeyUp="calculate_qnty(<? echo $i; ?>);calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="60">
								<input type="text" name="txt_noOfDays[]" id="txt_noOfDays_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:45px" value="<? echo $noOfDays; ?>"
								onKeyUp="calculate_noOfDays(<? echo $i; ?>);" disabled="disabled"/>
							</td>
							<td align="center" width="80">
								<input type="text" name="txt_startDate[]" id="txt_startDate_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $startDate; ?>"
								onChange="fn_add_date_field(<? echo $i; ?>);"/>
							</td>
							<td align="center">
								<input type="text" name="txt_endDate[]" id="txt_endDate_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $endDate; ?>"
								disabled="disabled"/>

								<input type="hidden" name="txt_allowedDateQnty[]" id="txt_allowedDateQnty_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $allowed_date_qnty; ?>" />
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6" align="right"><b>Total</b></th>
						<th align="center"><input type="text" name="txt_total_capacity" id="txt_total_capacity"
							class="text_boxes_numeric" style="width:75px" readonly
							disabled="disabled" value="<? echo $tot_capacity; ?>"/></th>
							<th align="center"><input type="text" name="txt_total_distribution_qnty"
								id="txt_total_distribution_qnty" class="text_boxes_numeric"
								style="width:75px" readonly disabled="disabled"
								value="<? echo $tot_distribution_qnty; ?>"/></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<table width="700" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
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

if ($action == "date_duplication_check_old")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
		$endDate = change_date_format(trim($data[2]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
		$endDate = change_date_format(trim($data[2]), '', '', 1);
	}
	$update_dtls_id = $data[3];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = '';
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {
				if ($data == '') $data = change_date_format($row[csf('distribution_date')]); else $data .= "," . change_date_format($row[csf('distribution_date')]);
			}
		}

		if ($data == '') echo "0_"; else echo "1" . "_" . $data;
	} else {
		echo "0_";
	}

	exit();
}

if ($action == "date_duplication_check_old_2")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	$distribute_qnty = $data[2];
	$capacity_qnty = $data[3];
	$no_of_days = $data[4];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
	}


	$update_dtls_id = $data[5];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' group by distribution_date order by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' and dtls_id<>$update_dtls_id group by distribution_date order by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = ''; $end_date = "";
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row)
		{
			if(date("d-m-Y",strtotime($row[csf('distribution_date')])) == date('d-m-Y',strtotime($startDate)))
			{
				if ($row[csf('days_complete')] >= 1)
				{
					if ($data == '') $data = change_date_format($row[csf('distribution_date')]); else $data .= "," . change_date_format($row[csf('distribution_date')]);
					if ($data == '') echo "0_"; else echo "1" . "_" . $data; exit();
				}
				else
				{
					$curr_remain_day = 1-$row[csf('days_complete')];
					$curr_day_capacity_qnty =  $capacity_qnty * $curr_remain_day;
					$remain_dist =  $distribute_qnty - $curr_day_capacity_qnty;

					if($distribute_qnty >= $curr_day_capacity_qnty)
					{
						$end_date = $row[csf('distribution_date')];
						$distribute_qnty =  $distribute_qnty - $curr_day_capacity_qnty;
						$dateWise_qnty .= "".$row[csf('distribution_date')].",".$curr_day_capacity_qnty."*";
						$no_of_days -= $curr_remain_day;
					}

					if($distribute_qnty ==0)
					{
						$dateWise_qnty = chop($dateWise_qnty,"*");
						echo "1_".$dateWise_qnty."#".$end_date;
						exit();
					}
				}
			}
			else
			{
				if ($row[csf('days_complete')] < 1)
				{
					$curr_remain_day = 1-$row[csf('days_complete')];
					$curr_day_capacity_qnty =  $capacity_qnty * $curr_remain_day;
					$remain_dist =  $distribute_qnty - $curr_day_capacity_qnty;

					if($distribute_qnty >= $curr_day_capacity_qnty)
					{
						$end_date = $row[csf('distribution_date')];
						$distribute_qnty =  $distribute_qnty - $curr_day_capacity_qnty;
						$dateWise_qnty .= "".$row[csf('distribution_date')].",".$curr_day_capacity_qnty."*";
						$no_of_days =- $curr_remain_day;
					}
					if($distribute_qnty ==0)
					{
						$dateWise_qnty = chop($dateWise_qnty,"*");
						echo "1_".$dateWise_qnty."#".$end_date;
						exit();
					}
				}
			}

		}

		if($no_of_days > 0)
		{
			$ceiled_noOfDays =  ceil($no_of_days);
			for($i = 0; $i <$ceiled_noOfDays; $i++)
			{
				$nxt_day = date('d-m-Y',strtotime($end_date . "+1 days"));
				if($no_of_days >1)
				{
					$dateWise_qnty .= $nxt_day.",".$capacity_qnty."*";

				}else{

					$dateWise_qnty .= $nxt_day.",".$no_of_days*$capacity_qnty."*";
				}
				$no_of_days -= 1;
				$end_date =$nxt_day;
			}
			$dateWise_qnty = chop($dateWise_qnty,"*");

		}

		echo "1_".$dateWise_qnty."#".$end_date;
		exit();
		if ($data == '') echo "0_"; else echo "1" . "_" . $data;
	}

	exit();
}

if ($action == "date_duplication_check")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	$distribute_qnty = $data[2];
	$capacity_qnty = $data[3];
	$no_of_days = $data[4];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
	}

	$update_dtls_id = $data[5];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' group by distribution_date order by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$startDate' and dtls_id<>$update_dtls_id group by distribution_date order by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = ''; $end_date = "";
	if (count($data_array) > 0)
	{
		foreach ($data_array as $row)
		{
			$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
			$previous_occupied_arr[$distribution_date]["days_complete"] = $row[csf('days_complete')];
			$previous_occupied_arr[$distribution_date]["distribution_date"] = $distribution_date;
		}
	}

	$present_day = $startDate;
	$dateWise_qnty = ""; $end_date = "";
	while($distribute_qnty > 0)
	{
		$present_day = date("d-m-Y",strtotime($present_day));

		if($previous_occupied_arr[$present_day]["distribution_date"])
		{
			if($previous_occupied_arr[$present_day]["distribution_date"] == $present_day)
			{
				if ($previous_occupied_arr[$present_day]["days_complete"] >= 1)
				{
					$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
				}
				else
				{
					if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"])))
					{
						$dist_qnty = $capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"]);
						$dateWise_qnty .= $present_day.",".$dist_qnty.",".($dist_qnty/$capacity_qnty)."*";
					}
					else
					{
						$dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
					}

					$distribute_qnty = $distribute_qnty - ($capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"]));
					$end_date =  $present_day;
					$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
				}
			}
		}
		else
		{
			if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr[$present_day]["days_complete"])))
			{
				$dateWise_qnty .= $present_day.",".$capacity_qnty.",".($capacity_qnty/$capacity_qnty)."*";
			}
			else
			{
				$dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
			}

			$end_date =  $present_day;
			$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
			$distribute_qnty = $distribute_qnty - $capacity_qnty;

		}


	}

	$dateWise_qnty = chop($dateWise_qnty,"*");
	echo $dateWise_qnty."=".$end_date;

	exit();
}

if ($action == "color_info_popup")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

		var selected_id = new Array, selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
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
			var old = document.getElementById('txt_color_row_id').value;
			if (old != "") {
				old = old.split(",");
				for (var i = 0; i < old.length; i++) {
					js_set_value(old[i])
				}
			}
		}

		/*
		function js_set_value2(str)
		{
			var id = $('#txt_individual_id' + str).val()
			var name = $('#txt_individual' + str).val();
			var color_balance_qty = $('#txt_individual_color_blqty' + str).val();
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_color_bl_qty').val(color_balance_qty);
			//parent.emailwindow.hide();
		}*/

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

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}

		function fnc_close(colorMixing)
		{
			var save_string = "";
			var save_string_size_wise = "";
			var breakOut = true;
			var total_prog_qty = 0;
			var color_name_string = '';
			var color_id_string = '';
			var color_prog_qty_string = '';
			var allowed_qty = 0;
			var colorQtyArr = [];

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var coloProgUpdateId = $(this).find('input[name="colo_prog_update_id[]"]').val();
				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();
				var txtColorProgQty = $(this).find('input[name="text_color_prog_qty[]"]').val() * 1;
				var hidden_color_allowed_qty = $(this).find('input[name="hidden_color_allowed_qty[]"]').val() * 1;
				var hidden_color_prev_prog_qty = $(this).find('input[name="hidden_color_prev_prog_qty[]"]').val() * 1;
				var txt_individual_color_blqty = $(this).find('input[name="txt_individual_color_blqty[]"]').val() * 1;
				var updateQty = $(this).find('input[name="text_color_prog_qty[]"]').attr('data-update-qty')*1;


				var hdn_size_wise_save_string = $(this).find('input[name="hdn_size_wise_save_string[]"]').val();


				//if(txtColorProgQty>0 || coloProgUpdateId !=0)
				if(txtColorProgQty>0 || (coloProgUpdateId !=0 && updateQty > 0))
				{
					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtColorProgQty;

						save_string_size_wise = hdn_size_wise_save_string;

					}
					else
					{
						save_string += "," + txtColorId + "_" + txtColorProgQty+ "_" + coloProgUpdateId+ "_" + hidden_color_allowed_qty;
						color_name_string += "," + txtColorName;
						color_id_string += "," + txtColorId;
						//color_prog_qty_string += "," + txtColorProgQty;

						save_string_size_wise += "**" + hdn_size_wise_save_string;
					}

					if(txtColorProgQty>0)
					{
						colorQtyArr.push(txtColorProgQty);
					}

					total_prog_qty += txtColorProgQty;

					if(hidden_color_allowed_qty<(hidden_color_prev_prog_qty+txtColorProgQty))
					{
						alert("Program quantity can not be greater than Balance quantity");
						$(this).find('input[name="text_color_prog_qty[]"]').focus();
						return;
					}
				}
			});

			if (total_prog_qty <= 0)
			{
				alert("Program quantity zero is not allowed");
				$('#text_color_prog_qty_1').focus();
				return false;
			}

			if(colorMixing!=1)
			{
				if(colorQtyArr.length>1)
				{
					alert('Color Mixing is not allowed');
					return;
				}
			}

			$('#hidden_color_wise_prog_data').val(save_string);
			$('#hidden_size_wise_prog_string').val(save_string_size_wise);
			$('#hidden_total_prog_qty').val(total_prog_qty.toFixed(2));
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
			parent.emailwindow.hide();
		}

		//func_onkeyup_color_qty
		function func_onkeyup_color_qty(rowId)
		{
			var bookingQty = $('#hidden_color_allowed_qty'+rowId).val()*1;
			var previousQty = $('#hidden_color_prev_prog_qty_'+rowId).val()*1;
			var qty = $('#text_color_prog_qty_'+rowId).val()*1;
			var updateQty = $('#text_color_prog_qty_'+rowId).attr('data-update-qty');

			if(qty < 0)
			{
				alert("Program quantity can't be less than zero.");
				$('#text_color_prog_qty_'+rowId).val(updateQty);
				return;
			}

			if(bookingQty < (previousQty+qty))
			{
				alert("Program quantity can't exceed booking quantity");
				$('#text_color_prog_qty_'+rowId).val(updateQty);
				return;
			}
		}

		function openpage_color_and_size_wise(rowId,colorId) {
        	var hidden_color_id = $('#hidden_color_id').val();
        	//var prog_no = $('#update_dtls_id').val();
        	var save_color_wise_prog_data = $('#hidden_color_wise_prog_data').val();

        	var sizeWiseSaveStringData = $('#hdn_size_wise_save_string_'+rowId).val();
        	var color_wise_break_down_mst_id = $('#colo_prog_update_id_'+rowId).val();
        	var prog_no = $('#color_prog_no_'+rowId).val();



        	var page_link = "planning_info_entry_controller.php?action=color_and_sizewise_info_popup&companyID="+<? echo $companyID; ?>+"&po_id="+"<? echo $po_id; ?>"+"&pre_cost_id="+"<? echo $pre_cost_id; ?>"+"&booking_no="+"<? echo trim($booking_no); ?>"+"&dia="+"<?php echo $dia; ?>"+"&hidden_color_id="+hidden_color_id +"&save_color_wise_prog_data="+save_color_wise_prog_data +"&plan_id="+"<?php echo $plan_id; ?>"+ '&prog_no=' + prog_no +"&colorId="+colorId+"&rowId="+rowId+"&color_wise_break_down_mst_id="+color_wise_break_down_mst_id+"&sizeWiseSaveStringData="+encodeURIComponent(String(sizeWiseSaveStringData))+"&body_part_id="+"<? echo $body_part_id; ?>";
        	var title = 'Size Info';

        	//alert(colorId);return;

        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,"width=600px,height=250px,center=1,resize=1,scrolling=0", '../');

        	emailwindow.onclose = function ()
        	{
        		var theform = this.contentDoc.forms[0];
        		/*var hidden_color_no = this.contentDoc.getElementById("txt_selected").value;
        		var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_prog_blance = this.contentDoc.getElementById("txt_selected_color_bl_qty").value;
        		var hidden_color_wise_prog_data = this.contentDoc.getElementById("hidden_color_wise_prog_data").value;
        		var hidden_total_prog_qty = this.contentDoc.getElementById("hidden_total_prog_qty").value;

        		$('#txt_color').val(hidden_color_no);
        		$('#hidden_color_id').val(hidden_color_id);
        		$('#txt_program_qnty').val(hidden_color_prog_blance);
        		$('#hidden_color_wise_prog_data').val(hidden_color_wise_prog_data);
        		$('#txt_program_qnty').val(hidden_total_prog_qty);
        		$('#hidden_color_wise_total').val(hidden_total_prog_qty);*/
        		//var hidden_color_id = this.contentDoc.getElementById("txt_selected_id").value;
        		var hidden_color_row_id = this.contentDoc.getElementById("hidden_color_row_id").value;
				var hidden_grandtotal_kg_qty = this.contentDoc.getElementById("hidden_grandtotal_kg_qty").value;
				var hidden_size_wise_prog_data = this.contentDoc.getElementById("hidden_size_wise_prog_data").value;

				$('#text_color_prog_qty_'+hidden_color_row_id).val(hidden_grandtotal_kg_qty);
				$('#hdn_size_wise_save_string_'+hidden_color_row_id).val(hidden_size_wise_prog_data);

        	}
        }
        function fnc_update_revise_qnty(operation)
		{
			var updateQnty=$('#txt_revise_qnty_1').val()*1;
			var txt_color_wise_id=$('#txt_color_wise_id_1').val()*1;
			var txt_programID=$('#txt_programID_1').val()*1;
			var txt_plandID=$('#txt_plandID_1').val()*1;
			var txt_colorID=$('#txt_colorID_1').val()*1;


			//alert(updateQnty+"=="+txt_color_wise_id+"=="+txt_programID+"=="+txt_plandID+"=="+txt_colorID); return;
			var data = "action=update_revise_qnty&operation=" + operation  + '&txt_color_wise_id='+txt_color_wise_id+'&txt_programID=' + txt_programID  + '&txt_plandID=' + txt_plandID + '&txt_colorID='+txt_colorID+'&updateQnty=' + updateQnty;
			//alert(data);
			//freeze_window(operation);
			http.open("POST", "planning_info_entry_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_revise_qnty_Reply_info;
		}
		function fnc_revise_qnty_Reply_info() {
        	if (http.readyState == 4) {
                //release_freezing();return;//alert(http.responseText);
                var reponse = trim(http.responseText).split('**');
                console.log(http.responseText);

                show_msg(reponse[0]);

                if(reponse[0] == 1)
                {
                	alert("Updated Success");
                }
                else if (reponse[0] == 20)
                {
                	alert("Knitting quantiy can not less than program quantity. Knitting Qnty = "+ reponse[1]);
                }
                else
                {
                	alert("Not Updated");
                }


            }
        }
	</script>
</head>
<body>
	<div align="center" style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:630px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="160">Color</th>
							<th width="80">Qnty</th>
							<th width="80">Prog. Qty</th>
							<th width="80">Prev. Prog. Qty</th>
							<th>Balance</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="txt_selected_color_bl_qty" id="txt_selected_color_bl_qty" value=""/>
							<input type="hidden" name="hidden_color_wise_prog_data" id="hidden_color_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_size_wise_prog_string" id="hidden_size_wise_prog_string" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_prog_qty" id="hidden_total_prog_qty" class="text_boxes" value="">
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
						id="tbl_list_search">
						<tbody>
						<?
						$hidden_color_id_for_revise_qnty=$hidden_color_id;
						$hidden_color_id = explode(",", $hidden_color_id);
						$pre_cost_id = explode(",", $pre_cost_id);
						$pre_cost_id = implode(",", array_unique($pre_cost_id));

						$bodypartId = explode(",", $body_part_id);
						$bodypartId = implode(",", array_unique($bodypartId));


						$po_wise_bodypartIdArr = explode(",", $po_wise_bodypartId);
						foreach ($po_wise_bodypartIdArr as $key => $value) {
							$po_wise_bodypartIdArr[$value]=$value;
						}
						/*echo "<pre>";
						print_r($po_wise_bodypartIdArr);
						die;*/


						
						$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");


						if ($dia!= "" || $db_type == 0) {
							$dia_cond = "b.dia_width like '%$dia%'";
							//$dia_cond = "b.dia_width = '$dia'";
						} else $dia_cond = "b.dia_width is null";

						//$sql = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";


						
						$sql_primary = sql_select("select b.po_break_down_id,c.body_part_id,b.fabric_color_id, sum(b.grey_fab_qnty) as qnty 
						from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c 
						where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.gsm_weight=c.gsm_weight and b.construction=c.construction and b.copmposition=c.composition   and c.body_part_id in($bodypartId ) and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.po_break_down_id,c.body_part_id,b.fabric_color_id
						union all 
						select b.po_break_down_id,c.body_part_id,b.fabric_color_id, sum(b.grey_fab_qnty) as qnty 
						from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c 
						where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.booking_type=1 and b.is_short=1 and b.job_no=c.job_no and c.body_part_id in($bodypartId ) and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.po_break_down_id,c.body_part_id,b.fabric_color_id");


						$po_color_bookingReqQnty=array();
						foreach ($sql_primary as $row)
						{
							if($po_wise_bodypartIdArr[$row[csf('po_break_down_id')]."_".$row[csf('body_part_id')]]==$row[csf('po_break_down_id')]."_".$row[csf('body_part_id')])
							{
								$po_color_bookingReqQnty[$row[csf('fabric_color_id')]]["qnty"] +=  $row[csf('qnty')];
								
							}
						}

						/*echo "<pre>";
						print_r($po_color_bookingReqQnty);*/

						$sql = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty 
						from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c 
						where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.gsm_weight=c.gsm_weight and b.construction=c.construction and b.copmposition=c.composition   and c.body_part_id in($bodypartId ) and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id
						union all 
						select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty 
						from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c 
						where a.booking_no=b.booking_no and b.booking_type=1 and b.is_short=1 and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.body_part_id in($bodypartId ) and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";


						/*  select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty 
						from wo_booking_mst a, wo_booking_dtls b,WO_PRE_COST_FABRIC_COST_DTLS c 
						where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.gsm_weight=c.gsm_weight and b.construction=c.construction --and b.job_no=d.job_no and c.job_no=d.job_no 
						and b.copmposition=c.composition and c.body_part_id in(651,461 ) and a.company_id=1 and a.item_category=2 and a.booking_no='FAL-Fb-23-00001' and b.dia_width like '%ANY%' 
						and b.po_break_down_id in (71129,71131) and b.pre_cost_fabric_cost_dtls_id in (41616,41615) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (a.fabric_source=1 or b.fabric_source=1)  and a.pay_mode in(3,5) 
						group by b.fabric_color_id 
						*/

						//echo $sql;

						if($plan_id!="")
						{
							//$plan_sql = "select id, plan_id, program_no, color_id, color_prog_qty from ppl_color_wise_break_down where plan_id = $plan_id and status_active=1 and is_deleted=0";
							$plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b where a.dtls_id = b.program_no and a.mst_id = b.plan_id and a.po_id in(".$po_id.") and b.plan_id = ".$plan_id." and b.status_active=1 and b.is_deleted=0 GROUP BY b.id, b.plan_id,  b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string";
							$plan_data = sql_select($plan_sql);
							$color_prog_data = array();
							foreach ($plan_data as $row)
							{
								$color_plan_data[$row[csf('plan_id')]][$row[csf('color_id')]]['color_prog_qty_total'] += $row[csf('color_prog_qty')];
								$color_plan_data[$row[csf('plan_id')]][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];
								$color_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]]['colo_prog_update_id'] = $row[csf('id')];
								$color_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]]['color_prog_qty'] = $row[csf('color_prog_qty')];
								$color_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]]['size_wise_prog_string'] = $row[csf('size_wise_prog_string')];
							}
						}
						else
						{
							//$save_color_wise_prog_data = array();
							$save_color_wise_prog_data = explode(",", $save_color_wise_prog_data);								
							
							$colo_prog_update_id_arr = array();
							for ($k = 0; $k < count($save_color_wise_prog_data); $k++) 
							{
								if($save_color_wise_prog_data[$k] != '')
								{  
									$colorWiseProgData = explode("_", $save_color_wise_prog_data[$k]);
									foreach ($colorWiseProgData as $key => $value) 
									{
										$colo_prog_update_id_arr[$colorWiseProgData[0]] = $colorWiseProgData[2];
									}
								}
							}

							//print_r($colo_prog_update_id_arr);
								
						}
						$body_part_type=return_library_array("select id, body_part_type from lib_body_part where status_active=1",'id','body_part_type');
						//echo $cbo_body_part;
						//echo $body_part_type[$cbo_body_part];die;

						$bodyPart=explode(",",$body_part_id);
						if($body_part_type[$bodyPart[0]] == 40 || $body_part_type[$bodyPart[0]] == 50){
							$popupOnOff = 1;
						} else {
							$popupOnOff = 0;
						}

						$result = sql_select($sql);
						$i = 1;
						$tot_qnty = 0;
						foreach ($result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$row[csf('qnty')]=$po_color_bookingReqQnty[$row[csf('fabric_color_id')]]["qnty"];

							$tot_qnty += $row[csf('qnty')];

							if (in_array($row[csf('fabric_color_id')], $hidden_color_id)) {
								if ($color_row_id == "") $color_row_id = $i; else $color_row_id .= "," . $i;
							}
							//echo $plan_id."==".$row[csf('fabric_color_id')];

							if($plan_id!="")
							{
								$colo_prog_update_id = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
							}
							else
							{
								$colo_prog_update_id = $colo_prog_update_id_arr[$row[csf('fabric_color_id')]];
							}
								
							//$colo_prog_update_id = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
							
							$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['color_prog_qty'];
							$color_total_prog_qty = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['color_prog_qty_total'];
							$blance = ($row[csf('qnty')]-($color_total_prog_qty));
							$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);

							$sizeWiseSaveData=$color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['size_wise_prog_string'];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">

								<td width="40" align="center"><? echo $i; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
									<input type="hidden" name="color_prog_no[]" id="color_prog_no_<?php echo $i; ?>" value="<? echo  $update_id= ($colo_prog_update_id!="")?$prog_no:"0"; ?>"/>
									<input type="hidden" name="colo_prog_update_id[]" id="colo_prog_update_id_<?php echo $i; ?>" value="<? echo  $update_id= ($colo_prog_update_id!="")?$colo_prog_update_id:"0"; ?>"/>
									<input type="hidden" name="hdn_size_wise_save_string[]" id="hdn_size_wise_save_string_<?php echo $i; ?>" value="<? if($sizeWiseSaveData!=""){echo $sizeWiseSaveData;} ?>"/>
								</td>
								<td width="160">
									<p><? echo $color_library[$row[csf('fabric_color_id')]]; ?></p>
									<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
									<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
								</td>
								<td width="80" align="right">
									<? echo number_format($row[csf('qnty')], 2); ?>
									<input type="hidden" name="hidden_color_allowed_qty[]" id="hidden_color_allowed_qty<? echo $i;?>" value="<? echo number_format($row[csf('qnty')], 2, '.', ''); ?>"/>
								</td>

								<?
									if(str_replace("'", '', $hdnVariableCollarCuff)==1)
									{
										if($popupOnOff==1)
										{
											?>
												<td width="80" align="right">
													<input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" onClick="openpage_color_and_size_wise('<?php echo $i; ?>','<?php echo $row[csf('fabric_color_id')]; ?>')" readonly />
												</td>
											<?
										}
										else
										{
											?>
												<td width="80" align="right">
													<input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" />
												</td>

											<?
										}

									}
									else
									{
										?>
											<td width="80" align="right">
												<input type="text" class="text_boxes_numeric" name="text_color_prog_qty[]" id="text_color_prog_qty_<? echo $i;?>" value="<? echo  $text_color_prog_qty= ($color_prog_qty>0)?$color_prog_qty:""; ?>" style="max-width: 80px; text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_color_prog_qty; ?>" onKeyUp="func_onkeyup_color_qty('<?php echo $i; ?>')" onBlur="func_onkeyup_color_qty('<?php echo $i; ?>')" />
											</td>

										<?
									}

								?>

								<td width="80" align="right">
									<p><? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2):"0"; ?></p>
									<input type="hidden" name="hidden_color_prev_prog_qty[]" id="hidden_color_prev_prog_qty_<? echo $i;?>" value="<? echo $text_prev_qty = ($previous_color_prog_qty>0)?number_format(($previous_color_prog_qty),2, '.', ''):"0"; ?>"/>
								</td>
								<td align="right"><p><? echo $balanceQty = ($blance>0)?number_format($blance ,2):"0" ; ?></p>
									<input type="hidden" name="txt_individual_color_blqty[]" id="txt_individual_color_blqty<?php echo $i; ?>" value="<? echo $balanceQty = ($blance>0)?number_format($blance ,2, '.', ''):"0" ; ?>"/>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
						<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_id; ?>"/>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_qnty, 2); ?></th>
							<th align="right">&nbsp;</th>
							<th align="right">&nbsp;</th>
							<th align="right">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div style="width:100%; margin-left:10px; margin-top:5px">
			<div style="width:43%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/> Check /
				Uncheck All
			</div>
			<div style="width:57%; float:left" align="left">
				<input type="button" name="close" onClick="fnc_close(<? echo $color_mixing_in_knittingplan_yes;?>);" class="formbutton"
				value="Close" style="width:100px"/>
			</div>
		</div>

		<?
		//IMPORTANT NOTE: THIS PART ONLY FOR THAT COLOR WHO IS NOT EXISTS IN BOOKING. MEANS BOOKING CHANGED COLOR AFTER PROGRAM
		$sql_color_not_exists = sql_select("select b.fabric_color_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.fabric_color_id in($hidden_color_id_for_revise_qnty) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id");
		$color_not_exist="";
		foreach ($sql_color_not_exists as $rowData)
		{
			$color_not_exist.=$rowData[csf('fabric_color_id')].",";
		}
		$color_not_exist=chop($color_not_exist,",");

		if(empty($sql_color_not_exists)){$existingColorNotFoundCond=""; $sql_color_not_existsCond=1;}else{$existingColorNotFoundCond="and b.color_id not in($color_not_exist)";$sql_color_not_existsCond=1;}

		if($plan_id!="")
		{
			$plan_sql_exists_color = sql_select("select b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b where a.dtls_id = b.program_no and a.mst_id = b.plan_id and a.po_id in(".$po_id.") and b.plan_id = ".$plan_id." and b.status_active=1 and b.is_deleted=0  $existingColorNotFoundCond and b.color_id in($hidden_color_id_for_revise_qnty) GROUP BY b.id, b.plan_id,  b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string");
		}


		//if($hidden_color_id_for_revise_qnty!="" && !empty($plan_sql_exists_color) && empty($sql_color_not_exists))
		if($hidden_color_id_for_revise_qnty!="" && !empty($plan_sql_exists_color) && $sql_color_not_existsCond==1)
		{
			$plan_sql_existsColor = sql_select("select b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b where a.dtls_id = b.program_no and a.mst_id = b.plan_id and a.po_id in(".$po_id.") and b.plan_id = ".$plan_id." and b.program_no=".$prog_no." and b.status_active=1 and b.is_deleted=0 $existingColorNotFoundCond and b.color_id in($hidden_color_id_for_revise_qnty) GROUP BY b.id, b.plan_id,  b.program_no, b.color_id, b.color_prog_qty,b.size_wise_prog_string order by b.id desc");
			?>
			<div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="160">Color</th>
						<th>Knitting Qnty</th>
						<input type="hidden" name="txt_selected_idx" id="txt_selected_id" value=""/>

					</thead>

						<tbody>
						<?
						$ii = 1;
						foreach ($plan_sql_existsColor as $row)
						{
							if ($ii % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							?>
							<tr>
								<td width="40" align="center"><? echo $ii; ?>

									<input type="hidden" name="txt_color_wise_id[]" id="txt_color_wise_id_<?php echo $ii; ?>" value="<? echo $row[csf('id')]; ?>"/>
									<input type="hidden" name="txt_programID[]" id="txt_programID_<?php echo $ii; ?>" value="<? echo $row[csf('program_no')]; ?>"/>
									<input type="hidden" name="txt_plandID[]" id="txt_plandID_<?php echo $ii; ?>" value="<? echo $row[csf('plan_id')]; ?>"/>
									<input type="hidden" name="txt_colorID[]" id="txt_colorID_<?php echo $ii; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
								</td>
								<td align="center" width="160"><? echo $color_library[$row[csf('color_id')]]; ?></td>
								<td align="right"><input class="text_boxes_numeric" type="text" name="txt_revise_qnty[]" id="txt_revise_qnty_<?php echo $ii; ?>" value="<? echo $row[csf('color_prog_qty')]; ?>"/>
								</td>

							</tr>


							<?
							$ii++;
						}
						?>
						</tbody>
					</table>
					<div style="width:57%;" align="center">
						<input type="button" name="" onClick="fnc_update_revise_qnty(1);" class="formbutton" value="Revise" style="width:100px"/>
					</div>

			</div>
			<?
		}
		?>
	</fieldset>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if ($action == "color_and_sizewise_info_popup")
{
	echo load_html_head_contents("Size Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
			set_all();
		});

		var selected_id = new Array, selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
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
			var old = document.getElementById('txt_color_row_id').value;
			if (old != "") {
				old = old.split(",");
				for (var i = 0; i < old.length; i++) {
					js_set_value(old[i])
				}
			}
		}

		/*
		function js_set_value2(str)
		{
			var id = $('#txt_individual_id' + str).val()
			var name = $('#txt_individual' + str).val();
			var color_balance_qty = $('#txt_individual_color_blqty' + str).val();
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_color_bl_qty').val(color_balance_qty);
			//parent.emailwindow.hide();
		}*/

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

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}

		//func_onkeyup_color_size_qty
		function func_onkeyup_color_size_qty(rowId)
		{
			//var color_size_qty_pcs = $('#hidden_color_size_qty_pcs'+rowId).val()*1;
			var current_qty_pcs = $('#text_current_qty_'+rowId).val()*1;

			var per_pcs_qty = $('#text_per_pcs_qty_'+rowId).val()*1;
			var tot_kg=current_qty_pcs/per_pcs_qty;
			$('#txt_tot_kg_'+rowId).val(tot_kg.toFixed(2));
		}
		function func_onkeyup_sum_qnty(rowId)
		{
			var tbl_row_count = $('#tbl_list_search tbody tr').length;
			tbl_row_count = tbl_row_count - 1;
			var totalSum=0;
			for (var i = 1; i <= tbl_row_count; i++) {
				totalSum+= $('#txt_tot_kg_'+i).val()*1;
			}
			$('#txt_sumID').text(totalSum)*1;
		}

		function fnc_close()
		{
			var save_string = "";
			var breakOut = true;
			var total_per_kg_qty = 0;
			var total_kg_qty = 0;
			var color_name_string = '';
			var color_id_string = '';

			$("#tbl_list_search").find('tbody tr').not(":first").each(function ()
			{
				var sizeProgUpdatePrimaryIds="";
				var coloProgUpdateId = $(this).find('input[name="size_prog_update_id[]"]').val();
				var sizeProgUpdatePrimaryId = $(this).find('input[name="size_prog_update_primary_id[]"]').val();

				var txtColorId = $(this).find('input[name="text_colorid_[]"]').val();
				var txtColorName = $(this).find('input[name="text_color_name_[]"]').val().trim();

				var txtGreySizeId = $(this).find('input[name="text_greySizeid_[]"]').val().trim();
				var txtGreySizeName = $(this).find('input[name="text_grey_size_name_[]"]').val().trim();

				var txtFinishSizeId = $(this).find('input[name="text_finishSizeid_[]"]').val().trim();
				var txtBodyPartId = $(this).find('input[name="text_body_part_[]"]').val();

				var txtSizePerKgQty = $(this).find('input[name="text_per_pcs_qty[]"]').val() * 1;
				var textCurrentQty = $(this).find('input[name="text_current_qty[]"]').val() * 1;

				var hidden_size_tot_kg = $(this).find('input[name="txt_tot_kg[]"]').val() * 1;
				var hidden_grandtotal_kg_qty = $(this).find('input[name="hidden_grandtotal_kg_qty[]"]').val() * 1;
				var updateQty = $(this).find('input[name="text_per_pcs_qty[]"]').attr('data-update-qty')*1;

				//if(txtSizePerKgQty>0 || coloProgUpdateId !=0)


				if(txtSizePerKgQty>0 || (coloProgUpdateId !=0 && updateQty > 0))
				{
					if(sizeProgUpdatePrimaryId>0){
						sizeProgUpdatePrimaryIds="_"+ sizeProgUpdatePrimaryId;
					}


					if (save_string == "")
					{
						save_string = txtColorId + "_" + txtGreySizeId + "_" + txtFinishSizeId + "_" + txtSizePerKgQty+ "_" + hidden_size_tot_kg+ "_" + coloProgUpdateId+ "_" + txtBodyPartId+ "_" + textCurrentQty+sizeProgUpdatePrimaryIds;
						color_name_string = txtColorName;
						color_id_string = txtColorId;
						//color_prog_qty_string = txtSizePerKgQty;

					}
					else
					{
						save_string += "##" + txtColorId + "_" + txtGreySizeId + "_" + txtFinishSizeId + "_" + txtSizePerKgQty+ "_" + hidden_size_tot_kg+ "_" + coloProgUpdateId+ "_" + txtBodyPartId+ "_" + textCurrentQty+sizeProgUpdatePrimaryIds;
						color_name_string += "," + txtColorName;
						color_id_string =  txtColorId;
						//color_prog_qty_string += "," + txtSizePerKgQty;
					}

					total_per_kg_qty += txtSizePerKgQty;
					total_kg_qty += hidden_size_tot_kg;


				}
			});

			if (total_per_kg_qty <= 0)
			{
				alert("Per KG quantity zero is not allowed");
				$('#text_per_pcs_qty_1').focus();
				return false;
			}

			$('#hidden_size_wise_prog_data').val(save_string);
			$('#hidden_total_kg_qty').val(total_per_kg_qty.toFixed(2));
			$('#hidden_grandtotal_kg_qty').val(total_kg_qty.toFixed(2));
			$('#txt_selected_id').val(color_id_string);
			$('#txt_selected').val(color_name_string);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="left" style="width:600px; margin-left:-18px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:600px; margin-top:10px; margin-left:20px">
				<div>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="60">Gmt Size</th>
							<th width="80">Finish Size</th>
							<th width="80">Qty Pcs</th>
							<th width="50">Current Qty</th>
							<th width="50">Balance</th>
							<th width="80">Per Kg Qty</th>
							<th>Total Kg</th>
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/>
							<input type="hidden" name="txt_selected" id="txt_selected" value=""/>
							<input type="hidden" name="hidden_size_wise_prog_data" id="hidden_size_wise_prog_data" class="text_boxes" value="">
							<input type="hidden" name="hidden_total_kg_qty" id="hidden_total_kg_qty" class="text_boxes" value="">
							<input type="hidden" name="hidden_grandtotal_kg_qty" id="hidden_grandtotal_kg_qty" class="text_boxes" value="">
							<input type="hidden" name="hidden_color_row_id" id="hidden_color_row_id" class="text_boxes" value="<? echo $rowId; ?>">
						</thead>
					</table>
					<div style="width:600px; overflow-y:scroll; max-height:210px;" id="buyer_list_view" align="left">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="590" class="rpt_table"
						id="tbl_list_search">
							<tbody>
								<?
								$totalSumation=0;
								if($sizeWiseSaveStringData!="")
								{
									$saveStringsExp=explode("##", $sizeWiseSaveStringData);
									foreach ($saveStringsExp as $val) {
										$saveStrData=explode("_", $val);
										$saveStringsExpArr[$saveStrData[1]][$saveStrData[2]]['perKg']=$saveStrData[3];
										$saveStringsExpArr[$saveStrData[1]][$saveStrData[2]]['totKg']=$saveStrData[4];
										$totalSumation+=$saveStrData[4];
									}
								}
								/*echo "<pre>";
								print_r($saveStringsExpArr);
								echo "</pre>";*/

								$hidden_color_id = explode(",", $hidden_color_id);
								$pre_cost_id = explode(",", $pre_cost_id);
								$pre_cost_id = implode(",", array_unique($pre_cost_id));
								$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
								$size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");


								if ($dia!= "" || $db_type == 0) {
									$dia_cond = "b.dia_width like '%$dia%'";
								} else $dia_cond = "b.dia_width is null";

								//$sql = "select b.fabric_color_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id";
								/*$sql = "select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent
								from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
								where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
								and  b.color_size_table_id=d.id and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id)
								and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
								group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent  order by d.size_number_id";*/

								$sql = "select x.fabric_color_id, x.body_part_id,x.size_number_id,x.qnty ,x.order_quantity,x.plan_cut_qnty,x.colar_cuff_per,x.body_part_type,x.colar_excess_percent, x.cuff_excess_percent from (select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent
								from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
								where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
								and  b.color_size_table_id=d.id and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id)
								and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.body_part_id in($body_part_id)
								group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent
								union all
								select b.fabric_color_id, c.body_part_id,d.size_number_id,sum(b.grey_fab_qnty) as qnty ,sum(d.order_quantity) as order_quantity,sum(plan_cut_qnty) as plan_cut_qnty,b.colar_cuff_per,e.body_part_type,a.colar_excess_percent, a.cuff_excess_percent
								from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_po_color_size_breakdown d,lib_body_part e
								where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id
								and b.po_break_down_id=d.po_break_down_id and b.gmts_size=d.size_number_id and b.fabric_color_id =d.color_number_id and c.body_part_id=e.id and c.job_no=d.job_no_mst and c.item_number_id=d.item_number_id and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id)
								and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.is_short=1 and c.body_part_id in($body_part_id) 
								group by b.fabric_color_id, c.body_part_id,d.size_number_id,e.body_part_type,b.colar_cuff_per,a.colar_excess_percent, a.cuff_excess_percent) x group by x.fabric_color_id, x.body_part_id,x.size_number_id,x.qnty ,x.order_quantity,x.plan_cut_qnty,x.colar_cuff_per,x.body_part_type,x.colar_excess_percent, x.cuff_excess_percent order by x.size_number_id";
								//and b.po_break_down_id=d.po_break_down_id and b.gmts_size=d.size_number_id and b.fabric_color_id =d.color_number_id
								//echo $sql;
								
								$sql_gmts_size = sql_select("select b.fabric_color_id, c.body_part_id,d.gmts_sizes,d.item_size
								from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d
								where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and c.id=d.pre_cost_fabric_cost_dtls_id and c.job_no=d.job_no and b.po_break_down_id=d.po_break_down_id and a.company_id=$companyID
								and a.item_category=2 and a.booking_no='$booking_no' and $dia_cond and b.po_break_down_id in ($po_id) and b.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and b.fabric_color_id=$colorId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.body_part_id in($body_part_id)
								group by b.fabric_color_id, c.body_part_id,d.gmts_sizes,d.item_size");
								foreach ($sql_gmts_size as $row)
								{
									if($row[csf('item_size')]!='0')
									{
										$finish_size_arr[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('gmts_sizes')]]['item_size']=$row[csf('item_size')];
									}
								}

								if($prog_no>0)
								{
									//color_wise_break_down_mst_id
									//$plan_sql = "select id, plan_id, program_no, color_id, color_prog_qty from ppl_color_wise_break_down where plan_id = $plan_id and status_active=1 and is_deleted=0";

									/*echo $size_plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty
									from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b , ppl_size_wise_break_down c
									where a.dtls_id = b.program_no and a.mst_id = b.plan_id and b.id=c.color_wise_mst_id and b.program_no=c.program_no and b.plan_id=c.plan_id and b.color_id=c.color_id and a.po_id in(".$po_id.") and b.plan_id =".$plan_id." and b.status_active=1 and b.is_deleted=0 and b.program_no=$prog_no and b.id=$color_wise_break_down_mst_id
									GROUP BY b.id, b.plan_id, b.program_no, b.color_id, b.color_prog_qty";*/

									$size_plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id,c.id as size_tbl_id,c.grey_size_id,c.finish_size_id,c.per_kg,c.current_qty,c.kg_wise_total_qnty,c.body_part_id
									from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b , ppl_size_wise_break_down c
									where a.dtls_id = b.program_no and a.mst_id = b.plan_id and b.id=c.color_wise_mst_id and b.program_no=c.program_no and b.plan_id=c.plan_id and b.color_id=c.color_id and a.po_id in(".$po_id.") and b.plan_id =".$plan_id." and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.program_no=$prog_no and b.id=$color_wise_break_down_mst_id and c.body_part_id in($body_part_id)
									GROUP BY b.id, b.plan_id, b.program_no, b.color_id,c.id,c.grey_size_id,c.finish_size_id,c.per_kg,current_qty,c.kg_wise_total_qnty,c.body_part_id";



									$size_plan_data = sql_select($size_plan_sql);
									$color_prog_data = array();
									foreach ($size_plan_data as $row)
									{
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['per_kg'] = $row[csf('per_kg')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['kg_wise_total_qnty'] = $row[csf('kg_wise_total_qnty')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['current_qty'] = $row[csf('current_qty')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['size_prog_update_id'] = $row[csf('id')];
										$size_prog_data[$row[csf('plan_id')]][$row[csf('program_no')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['size_prog_update_primary_id'] = $row[csf('size_tbl_id')];
									}
								}

								//cummulitive qnty
								$cumm_size_plan_sql = "select b.id, b.plan_id, b.program_no, b.color_id,c.id as size_tbl_id,c.grey_size_id,c.finish_size_id,c.per_kg,c.kg_wise_total_qnty,c.current_qty,c.body_part_id
								from ppl_planning_entry_plan_dtls a, ppl_color_wise_break_down b , ppl_size_wise_break_down c
								where a.dtls_id = b.program_no and a.mst_id = b.plan_id and b.id=c.color_wise_mst_id and b.program_no=c.program_no and b.plan_id=c.plan_id and b.color_id=c.color_id and a.po_id in(".$po_id.") and b.plan_id =".$plan_id." and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_no= '$booking_no' and a.yarn_desc in ($pre_cost_id) and c.body_part_id in($body_part_id) GROUP BY b.id, b.plan_id, b.program_no, b.color_id,c.id,c.grey_size_id,c.finish_size_id,c.per_kg,c.kg_wise_total_qnty,c.current_qty,c.body_part_id";



								$cumm_size_plan_data = sql_select($cumm_size_plan_sql);
								foreach ($cumm_size_plan_data as $row)
								{
									$cumm_size_prog_data[$row[csf('plan_id')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['per_kg']+= $row[csf('per_kg')];
									$cumm_size_prog_data[$row[csf('plan_id')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['kg_wise_total_qnty']+= $row[csf('kg_wise_total_qnty')];
									$size_prog_data_balance[$row[csf('plan_id')]][$row[csf('color_id')]][$row[csf('grey_size_id')]][$row[csf('finish_size_id')]][$row[csf('body_part_id')]]['current_qty']+= $row[csf('current_qty')];

								}


								//print_r($size_prog_data);

								$result = sql_select($sql);
								$i = 1;
								$tot_qnty = 0;
								foreach ($result as $row)
								{
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";



									if (in_array($row[csf('fabric_color_id')], $hidden_color_id)) {
										if ($color_row_id == "") $color_row_id = $i; else $color_row_id .= "," . $i;
									}
									//echo $plan_id."==".$row[csf('fabric_color_id')];

									//$colo_prog_update_id = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
									/*$colo_prog_update_id = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['colo_prog_update_id'];
									$color_prog_qty = $color_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]]['color_prog_qty'];
									$color_total_prog_qty = $color_plan_data[$plan_id][$row[csf('fabric_color_id')]]['color_prog_qty_total'];
									$blance = ($row[csf('qnty')]-($color_total_prog_qty));
									$previous_color_prog_qty = ($color_total_prog_qty-$color_prog_qty);*/

									$finshSizeId=$finish_size_arr[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('size_number_id')]]['item_size'];

									$per_kg=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['per_kg'];
									$kg_wise_total_qnty=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['kg_wise_total_qnty'];
									$current_total_qnty=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['current_qty'];
									$size_prog_update_id=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['size_prog_update_id'];
									$size_prog_update_primary_id=$size_prog_data[$plan_id][$prog_no][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['size_prog_update_primary_id'];

									$current_total_qnty_balance=$size_prog_data_balance[$plan_id][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['current_qty'];

									$cumm_per_kg=$cumm_size_prog_data[$plan_id][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['per_kg'];
									$cumm_kg_wise_total_qnty=$cumm_size_prog_data[$plan_id][$row[csf('fabric_color_id')]][$row[csf('size_number_id')]][$finshSizeId][$row[csf('body_part_id')]]['kg_wise_total_qnty'];

									//echo $cumm_kg_wise_total_qnty;

									if($row[csf('body_part_type')]==50)
									{
										$plantCutQnty=$row[csf('plan_cut_qnty')]*2;
									}
									else
									{
										$plantCutQnty=$row[csf('plan_cut_qnty')];
									}
									$collar_ex_per=$row[csf('colar_cuff_per')];
									$cuff_excess_percent=$row[csf('cuff_excess_percent')];
									$colar_excess_percent=$row[csf('colar_excess_percent')];


									if($row[csf('body_part_type')]==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                    else if($row[csf('body_part_type')]==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }


                                    $tot_exPer=($plantCutQnty*$collar_ex_per)/100;
									$colar_excess_per=$tot_exPer;
								    $collerqty=($plantCutQnty+$colar_excess_per);

								    //$tot_qnty += $row[csf('order_quantity')];
								    $tot_qnty += number_format($collerqty);



									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">

										<td width="40" align="center"><? echo $i; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
											<input type="hidden" name="size_prog_update_id[]" id="size_prog_update_id_<?php echo $i; ?>" value="<? echo  $update_id= ($size_prog_update_id!="")?$size_prog_update_id:"0"; ?>"/>
											<input type="hidden" name="size_prog_update_primary_id[]" id="size_prog_update_primary_id_<?php echo $i; ?>" value="<? echo  $size_primary_update_id= ($size_prog_update_primary_id!="")?$size_prog_update_primary_id:"0"; ?>"/>

											<input type="hidden" name="text_body_part_[]" id="text_body_part_<? echo $i;?>" value="<? echo $row[csf('body_part_id')]; ?>"/>

										</td>




										<td width="60" align="center">
											<p><? echo $size_library[$row[csf('size_number_id')]]; ?></p>
											<input type="hidden" name="text_colorid_[]" id="text_colorid_<? echo $i;?>" value="<? echo $row[csf('fabric_color_id')]; ?>"/>
											<input type="hidden" name="text_color_name_[]" id="text_color_name_<? echo $i;?>" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>"/>
											<input type="hidden" name="text_grey_size_name_[]" id="text_grey_size_name_<? echo $i;?>" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>"/>
											<input type="hidden" name="text_greySizeid_[]" id="text_greySizeid_<? echo $i;?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
										</td>
										<td width="80" align="center">
											<? echo $finshSizeId; ?>

											<input type="hidden" name="text_finishSizeid_[]" id="text_finishSizeid_<? echo $i;?>" value="<? echo $finshSizeId; ?>"/>

										</td>
										<td width="80" align="right">
											<? echo number_format($collerqty); //number_format($row[csf('order_quantity')], 2); ?>
											<input type="hidden" name="hidden_color_size_qty_pcs[]" id="hidden_color_size_qty_pcs<? echo $i;?>" value="<? echo number_format($collerqty); ?>"/>
										</td>

										<td width="50" align="right">
										<input type="text" class="text_boxes_numeric" name="text_current_qty[]" id="text_current_qty_<? echo $i;?>" value="<?


											echo  $text_current_prog_qty= ($current_total_qnty>0)?$current_total_qnty:"";


											?>" style="max-width: 40px; text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_current_prog_qty; ?>"  />

											<?
											 $cumm_size_prog_qty= ($cumm_per_kg>0)?$cumm_per_kg:"";
											 $cumm_kg_wise_prog_qty= ($cumm_kg_wise_total_qnty>0)?$cumm_kg_wise_total_qnty:"";

											//echo $cumm_kg_wise_prog_qty;

											?>
										</td>
										<td width="50" align="right" title="<? echo "Qty Pcs(".number_format($collerqty,0,'.','').")-Prev.Qty($current_total_qnty_balance)"; ?>">
											<?
												$balancing=number_format($collerqty,0,'.','')-$current_total_qnty_balance;
												echo $balancing;
												?>
										</td>

										<td width="80" align="center">
											<input type="text" class="text_boxes_numeric" name="text_per_pcs_qty[]" id="text_per_pcs_qty_<? echo $i;?>" value="<?

											/*if($sizeWiseSaveStringData!="")
											{
												echo $saveStringsExpArr[$row[csf('size_number_id')]][$finshSizeId]['perKg'];

											}else{} */
											echo  $text_size_prog_qty= ($per_kg>0)?$per_kg:"";

											?>" style="max-width: 70px; text-align: center;" placeholder="Write" data-update-qty="<?php echo $text_size_prog_qty; ?>" onKeyUp="func_onkeyup_color_size_qty('<?php echo $i; ?>');func_onkeyup_sum_qnty('<?php echo $i; ?>');"  />
										</td>
										<td align="center">

											<input  type="text" class="text_boxes_numeric" name="txt_tot_kg[]" id="txt_tot_kg_<? echo $i;?>" readonly value="<?
											echo  $text_size_total_kg_prog_qty= ($kg_wise_total_qnty>0)?$kg_wise_total_qnty:"";
											/*if($sizeWiseSaveStringData!="")
											{

												echo $saveStringsExpArr[$row[csf('size_number_id')]][$finshSizeId]['totKg'];
											}
											else{} */


												?>">
											<input type="hidden" name="hidden_size_tot_kg[]" id="hidden_size_tot_kg_<? echo $i;?>" value=""/>

										</td>
									</tr>
									<?
									$i++;
								}
								?>
								<input type="hidden" name="txt_color_row_id" id="txt_color_row_id" value="<?php echo $color_row_id; ?>"/>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3" align="right"><b>Total</b></th>
									<th align="right"><? echo number_format($tot_qnty); ?></th>
									<th align="right">&nbsp;</th>
									<th align="right">&nbsp;</th>
									<th align="right">&nbsp;</th>
									<th id="txt_sumID" align="right"><? echo number_format($totalSumation, 2); ?>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div style="width:100%; margin-left:10px; margin-top:5px">
					<div style="width:43%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/> Check /
						Uncheck All
					</div>
					<div style="width:57%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></scrip>
</html>
<?
exit();
}
if ($action == "feeder_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function fnc_close() {
			var save_string = '';
			var tot_row = $("#tbl_list_search tbody tr").length;

			for (var i = 1; i <= tot_row; i++) {
				var txtPreCostId = $('#txtPreCostId_' + i).val();
				var txtColorId = $('#txtColorId_' + i).val();
				var txtStripeColorId = $('#txtStripeColorId_' + i).val();
				var txtNoOfFeeder = $('#txtNoOfFeeder_' + i).val();

				if (save_string == "") {
					save_string = txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
				}
				else {
					save_string += "," + txtPreCostId + "_" + txtColorId + "_" + txtStripeColorId + "_" + txtNoOfFeeder;
				}

			}

			$('#hidden_no_of_feeder_data').val(save_string);

			parent.emailwindow.hide();
		}

		function calculate_total() {
			var tot_row = $("#tbl_list_search tbody tr").length;

			var ddd = {dec_type: 6, comma: 0, currency: ''}

			math_operation("txtTotFeeder", "txtNoOfFeeder_", "+", tot_row, ddd);
		}

	</script>

</head>

<body>
	<div style="width:630px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:620px; margin-top:10px; margin-left:5px">
				<input type="hidden" name="hidden_no_of_feeder_data" id="hidden_no_of_feeder_data" class="text_boxes"
				value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="140">Color</th>
						<th width="130">Stripe Color</th>
						<th width="90">Measurement</th>
						<th width="70">UOM</th>
						<th>No Of Feeder</th>
					</thead>
				</table>
				<div style="width:618px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$noOfFeeder_array = array();
						$no_of_feeder_data = explode(",", $no_of_feeder_data);
						$pre_cost_id = explode(",", $pre_cost_id);
						$pre_cost_id = implode(",", array_unique($pre_cost_id));

						$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

						for ($i = 0; $i < count($no_of_feeder_data); $i++) {
							$color_wise_data = explode("_", $no_of_feeder_data[$i]);
							$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
							$color_id = $color_wise_data[1];
							$stripe_color = $color_wise_data[2];
							$no_of_feeder = $color_wise_data[3];

							$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$i]=$no_of_feeder;
							//$noOfFeeder_array[$i] = $no_of_feeder;
						}

						$sql = "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in($pre_cost_id) and status_active=1 and is_deleted=0 order by color_number_id,id";
						$result = sql_select($sql);

						$i = 1;
						$tot_feeder = 0;
						$kl = 0;
						foreach ($result as $row) {
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							//$no_of_feeder = $noOfFeeder_array[$kl];
							$no_of_feeder=$noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$kl];
							$tot_feeder += $no_of_feeder;
							$kl++;
							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txtPreCostId[]" id="txtPreCostId_<?php echo $i ?>"
								value="<? echo $row[csf('pre_cost_id')]; ?>"/>
								<input type="hidden" name="txtColorId[]" id="txtColorId_<?php echo $i ?>"
								value="<? echo $row[csf('color_number_id')]; ?>"/>
								<input type="hidden" name="txtStripeColorId[]"
								id="txtStripeColorId_<?php echo $i ?>"
								value="<? echo $row[csf('stripe_color')]; ?>"/>
							</td>
							<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td>
							<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>
							<td width="90"><input type="text" name="txtMeasurement[]"
								id="txtMeasurement_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:80px" value="<? echo $row[csf('measurement')]; ?>"
								disabled/></td>
								<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
								</td>
								<td align="center">
									<input type="text" name="txtNoOfFeeder[]" id="txtNoOfFeeder_<? echo $i; ?>"
									class="text_boxes_numeric" style="width:90px"
									value="<? echo $no_of_feeder; ?>" onKeyUp="calculate_total();"/>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="5">Total</th>

						<th style="text-align:center"><input type="text" name="txtTotFeeder" id="txtTotFeeder"
							class="text_boxes_numeric" style="width:90px"
							value="<? echo $tot_feeder; ?>" disabled/></th>
						</tfoot>
					</table>
				</div>
				<table width="600" id="tbl_close">
					<tr>
						<td align="center">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>
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

if ($action == "advice_info_popup")
{
	echo load_html_head_contents("Advice Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

</head>

<body>
	<div style="width:430px;" align="center">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:400px; margin-top:10px;">
				<input type="hidden" name="hidden_advice_data" id="hidden_advice_data" class="text_boxes" value="">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
					<tr>
						<td><textarea name="txt_advice" id="txt_advice" class="text_area"
							style="width:385px; height:120px;"><? echo $hidden_advice_data; ?></textarea></td>
						</tr>
					</table>
					<table width="400" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="parent.emailwindow.hide();" style="width:100px"/>
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

if ($action == "collarCuff_info_popup")
{
	echo load_html_head_contents("Collar & Cuff Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function add_break_down_tr(i) {
			var row_num = $('#txt_tot_row').val();
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {
				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

            }).end();//.appendTo("#tbl_list_search")

			$("#tr_" + i).after(clone);

			$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("onDblClick").attr("onDblClick", "func_onDblClick_finishSize(" + row_num + ");");

			$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);
			reArrangeSl();
			set_all_onclick();
		}

		function reArrangeSl() {
			var i = 0;
			$("#tbl_list_search").find('tbody tr').each(function () {
				i++;
				$(this).find("td:eq(0)").text(i);
			});
		}

		function fn_deleteRow(rowNo) {
			if (rowNo != 1) {
				$("#tr_" + rowNo).remove();
				reArrangeSl();
				calculate_tot_qnty();
			}
		}

		function fnc_close() {
			var save_string = "";
			var breakOut = true;
			$("#tbl_list_search").find('tbody tr').each(function () {
				if (breakOut == false) {
					return;
				}

				var bodyPartId = $(this).find('input[name="bodyPartId[]"]').val();
				var txtGrey = $(this).find('input[name="txtGrey[]"]').val();
				var txtFinish = $(this).find('input[name="txtFinish[]"]').val();
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;

				if (txtQtyPcs < 1) {
					alert("Please Insert Qty. Pcs");
					$(this).find('input[name="txtQtyPcs[]"]').focus();
					breakOut = false;
					return false;
				}

				if (save_string == "") {
					save_string = bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
				}
				else {
					save_string += "," + bodyPartId + "_" + txtGrey + "_" + txtFinish + "_" + txtQtyPcs;
				}
			});

			if (breakOut == false) {
				return;
			}
			$('#hidden_collarCuff_data').val(save_string);
			parent.emailwindow.hide();
		}

		function calculate_tot_qnty() {
			var txtTotQtyPcs = '';
			$("#tbl_list_search").find('tbody tr').each(function () {
				var txtQtyPcs = $(this).find('input[name="txtQtyPcs[]"]').val() * 1;
				txtTotQtyPcs = txtTotQtyPcs * 1 + txtQtyPcs * 1;
			});

			$('#txtTotQtyPcs').val(Math.round(txtTotQtyPcs));
		}

		//func_onDblClick_finishSize
		function func_onDblClick_finishSize(rowNo)
		{
			//alert('su..re');
        	var page_link = 'planning_info_entry_controller.php?action=action_finishSize&pre_cost_id=' + '<? echo $pre_cost_id; ?>';
        	var title = 'Finish Size';
        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0', '../');
        	emailwindow.onclose = function ()
			{
        		var theform = this.contentDoc.forms[0];
        		var item_size = this.contentDoc.getElementById("hdn_item_size").value;
				var item_size_arr = item_size.split(',');

				if(item_size_arr.length > 1)
				{
					var i = 1;
					for(i; i<item_size_arr.length; i++)
					{
						var rowNo = $('#txt_tot_row').val();
						add_break_down_tr(rowNo);
					}

					//value assigning here
					var row_num = $('#txt_tot_row').val();
					var r = 0;
					for(r; r<=row_num; r++)
					{
						$('#txtFinish_' + r).val(item_size_arr[r]);
					}
				}
				else
				{
					var rowNo = $('#txt_tot_row').val();
					$('#txtFinish_' + rowNo).val(item_size);
				}
        	}
		}

		function add_break_down_tr_zs(i)
		{
			var row_num = $('#txt_tot_row').val();
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function () {
				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

            }).end();//.appendTo("#tbl_list_search")

			$("#tr_" + i).after(clone);

			$('#txtGrey_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("value").attr("value", "");
			$('#txtFinish_' + row_num).removeAttr("onDblClick").attr("onDblClick", "func_onDblClick_finishSize(" + row_num + ");");

			$('#txtQtyPcs_' + row_num).removeAttr("value").attr("value", "");
			$('#txtQtyPcs_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "calculate_tot_qnty(" + row_num + ");");

			$('#increase_' + row_num).removeAttr("value").attr("value", "+");
			$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
			$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
			$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

			$('#txt_tot_row').val(row_num);
			reArrangeSl();
			set_all_onclick();
		}

	</script>
	</head>
	<body>
		<div style="width:530px;" align="center">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:530px; margin-top:5px">
					<input type="hidden" name="hidden_collarCuff_data" id="hidden_collarCuff_data" class="text_boxes"
					value="">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="525" class="rpt_table">
						<thead>
							<th width="30">SL</th>
							<th width="100">Body Part</th>
							<th width="100">Grey Size</th>
							<th width="100">Finish Size</th>
							<th width="100">Qty. Pcs</th>
							<th></th>
						</thead>
					</table>
					<div style="width:525px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table"
						id="tbl_list_search">
						<tbody>
							<?
							$collarCuff_data = ($collarCuff_data != "") ? explode(",", $collarCuff_data) : array();
							if (!empty($collarCuff_data))
							{
								$sl = 1;
								for ($i = 0; $i < count($collarCuff_data); $i++)
								{
									$body_part_wise_data = explode("_", $collarCuff_data[$i]);
									$body_part_id = $body_part_wise_data[0];
									$grey = $body_part_wise_data[1];
									$finish = $body_part_wise_data[2];
									$qty = $body_part_wise_data[3];
									$totQtyPcs += $qty;
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="30" align="center"><? echo $sl++; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part[$body_part_id]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
											value="<? echo $body_part_id; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<? echo $grey; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px" value="<? echo $finish; ?>" readonly
                                            placeholder="browse" onDblClick="func_onDblClick_finishSize('<? echo $i; ?>')" />
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px" value="<? echo $qty; ?>"
											onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $i; ?>);"/>
										</td>
										</tr>
										<?
									}
							}
							else
							{
								$sql = "select collar_cuff_data from ppl_planning_info_entry_dtls where id=$update_dtls_id";
								$collar_cuff_data_arr = sql_select($sql);
								$collar_cuff_data = explode(",", $collar_cuff_data_arr[0]["collar_cuff_data"]);

								$i = 1;
								$totQtyPcs = 0;
								$sl = 1;
								foreach ($collar_cuff_data as $row)
								{
									$collar_data = explode("_", $row);
									$totQtyPcs += $collar_data[3];
									?>
									<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $sl++; ?>">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100">
											<input type="text" name="txtBodyPartId[]" id="txtBodyPartId_<?php echo $i ?>"
											value="<? echo $body_part[$hidden_bodypartID_data]; //$body_part[$collar_data[0]]; ?>" class="text_boxes"
											style="width:80px" disabled/>
											<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i ?>"
											value="<? echo $hidden_bodypartID_data; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtGrey[]" id="txtGrey_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $collar_data[1]; ?>"/>
										</td>
										<td width="100"><input type="text" name="txtFinish[]" id="txtFinish_<? echo $i; ?>"
											class="text_boxes" style="width:80px"
											value="<?php echo $collar_data[2]; ?>"/>
										</td>
										<td width="100">
											<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $i; ?>"
											class="text_boxes_numeric" style="width:80px"
											value="<?php echo $collar_data[3]; ?>" onKeyUp="calculate_tot_qnty();"/>
										</td>
										<td>
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
											style="width:30px" class="formbuttonplasminus" value="+"
											onClick="add_break_down_tr( <? echo $i; ?> )"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
											style="width:30px" class="formbuttonplasminus" value="-"
											onClick="fn_deleteRow(<? echo $i; ?>);"/>
										</td>
									</tr>
									<?
									$i++;
								}
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th style="text-align:center"><input type="text" name="txtTotQtyPcs" id="txtTotQtyPcs"
								class="text_boxes_numeric" style="width:80px"
								value="<? echo $totQtyPcs; ?>" disabled/><input
								type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $i - 1; ?>"/></th>
								<th></th>
							</tfoot>
						</table>
					</div>
					<table width="500" id="tbl_close">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="fnc_close();" style="width:100px"/>
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

//action_finishSize
if($action == 'action_finishSize')
{
	echo load_html_head_contents("Finish Size", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var item_size = new Array;
		var items = new Array;
		//var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			//alert('su..re='+tbl_row_count);
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str)
		{
			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[0], selected_id) == -1)
			{
				selected_id.push(str[0]);
				item_size.push(str[1]);
				//selected_name.push(str[2]);
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == str[0])
						break;
				}
				selected_id.splice(i, 1);
				item_size.splice(i, 1);
				//selected_name.splice(i, 1);
			}

			var id = '';
			var itms = '';
			//var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				//id += selected_id[i] + ',';
				//name += selected_name[i] + '*';

				if (jQuery.inArray(item_size[i], items) == -1)
				{
					items.push(item_size[i]);
				}
			}

			for (var i = 0; i < items.length; i++)
			{
				itms += items[i] + ',';
			}

			//id = id.substr(0, id.length - 1);
			//name = name.substr(0, name.length - 1);
			itms = itms.substr(0, itms.length - 1);

			//$('#hdn_item_size').val(id);
			//$('#hdn_season_name').val(name);
			$('#hdn_item_size').val(itms);
		}
	</script>
	</head>
	<body>
    <input type="hidden" id="hdn_item_size" name="hdn_item_size" />
    <div style="margin-top:15px" id="search_div"></div>
	</body>
	<script>
        show_list_view ('<? echo $pre_cost_id; ?>', 'action_finishSize_listview', 'search_div', 'planning_info_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

//action_create_season_listview
if ($action == "action_finishSize_listview")
{
	//$data = explode('**', $data);
	//$sql="select a.id as pre_cost_fabric_cost_dtls_id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id, a.fabric_source, a.color_type_id, a.gsm_weight, a.construction, a.composition, a.color_size_sensitive, a.costing_per, a.color, a.color_break_down, a.rate as rate_mst, b.id, b.po_break_down_id, b.color_size_table_id, b.color_number_id, b.gmts_sizes as size_number_id, b.dia_width, b.item_size, b.cons, b.process_loss_percent, b.requirment, b.rate, b.pcs, b.remarks FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.id in(".$pre_cost_id.") and a.status_active=1 and a.is_deleted=0 order by a.id, b.color_size_table_id";
	$sql="select a.id, a.item_number_id, b.gmts_sizes, b.item_size FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.id in(".$data.") and a.status_active=1 and a.is_deleted=0 group by a.id, a.item_number_id, b.gmts_sizes, b.item_size order by item_size";
	$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$arr = array(1=>$size_library);
	echo create_list_view("tbl_list_search", "Item Sizes,Gmts. Sizes", "150,150", "350", "240", 0, $sql, "js_set_value", "item_size,gmts_sizes", "", 1, "0,gmts_sizes", $arr, "item_size,gmts_sizes", "", '', '0,0', '', 1);
	exit();
}

if ($action == "count_feeding_data_popup")
{
	echo load_html_head_contents("Count Feeding", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function add_break_down_tr(i) {
			var row_num = $('#tbl_list_search tr').length;
			row_num++;

			var clone = $("#tr_" + i).clone();
			clone.attr({
				id: "tr_" + row_num,

			});

			clone.find("input,select").each(function () {

				$(this).attr({
					'id': function (_, id) {
						var id = id.split("_");
						return id[0] + "_" + row_num
					},
					'name': function (_, name) {
						return name
					},
					'value': function (_, value) {
						return value
					}
				});

			}).end();

			$("#tr_" + i).after(clone);
//$('#txtSeqNo_' + row_num).removeAttr("value").attr("value", row_num);
$('#cboCount_' + row_num).removeAttr("value").attr("value", 0);
$('#cboFeeding_' + row_num).removeAttr("value").attr("value", 0);

$('#increase_' + row_num).removeAttr("value").attr("value", "+");
$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");

reArrangeSl();
set_all_onclick();
}

function reArrangeSl() {
var i = 0;
$("#tbl_list_search").find('tbody tr').each(function () {
	i++;
	$(this).find("td:eq(0)").text(i);
	$(this).find("td:eq(1) input").val(i);
});
}

function fn_deleteRow(rowNo) {
if (rowNo != 1) {
	$("#tr_" + rowNo).remove();
	reArrangeSl();
}
}

function fnc_close() {
var save_string = "";
var breakOut = true;
$("#tbl_list_search").find('tbody tr').each(function () {
	if (breakOut == false) {
		return;
	}

	var txtSeqNo = $(this).find('input[name="txtSeqNo[]"]').val();
	var cboCount = $(this).find('select[name="cboCount[]"]').val();
	var cboFeeding = $(this).find('select[name="cboFeeding[]"]').val();


	if (save_string == "") {
		save_string = txtSeqNo + "_" + cboCount + "_" + cboFeeding;
	}
	else {
		save_string += "," + txtSeqNo + "_" + cboCount + "_" + cboFeeding;
	}


});

if (breakOut == false) {
	return;
}

$('#hidden_count_feeding_data').val(save_string);
parent.emailwindow.hide();
}





</script>

</head>

<body>
<div style="width:430px;" align="center">
<form name="searchwofrm" id="searchwofrm">
<fieldset style="width:430px; margin-top:5px">
	<input type="hidden" name="hidden_count_feeding_data" id="hidden_count_feeding_data" class="text_boxes"
	value="">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="425" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Seq. No</th>
			<th width="100">Count</th>
			<th width="100">Feeding</th>
			<th></th>
		</thead>
	</table>
	<div style="width:425px; overflow-y:scroll; max-height:230px;" id="buyer_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="405" class="rpt_table"
		id="tbl_list_search">
		<tbody>
			<?
			$yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count","id","yarn_count");


			$count_feeding_data_arr = ($count_feeding_data != "") ? explode(",", $count_feeding_data) : array();
			if (!empty($count_feeding_data)){
				$sl = 1;
				for ($i = 0; $i < count($count_feeding_data_arr); $i++) {
					$count_feeding_data = explode("_", $count_feeding_data_arr[$i]);
					$seq = $count_feeding_data[0];
					$count_id = $count_feeding_data[1];
					$feeding_id = $count_feeding_data[2];
					?>
					<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
						<td width="30" align="center"><? echo $sl++; ?></td>
						<td width="100">
							<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="<? echo $seq;?>" class="text_boxes" style="width:80px"/>
						</td>
						<td width="100">
							<?
							echo create_drop_down( "cboCount_".$i, 80, $yarn_count_arr,"", 1, "-- Count --",$count_id, "",0,"","","","","","","cboCount[]");
							?>
						</td>
						<td width="100">
							<?
							echo create_drop_down( "cboFeeding_".$i, 80, $feeding_arr,"", 1, "-- Feeding --",$feeding_id, "",0,"","","","","","","cboFeeding[]");
							?>
						</td>
						<td>
							<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
							style="width:30px" class="formbuttonplasminus" value="+"
							onClick="add_break_down_tr( <? echo $i; ?> )"/>
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
							style="width:30px" class="formbuttonplasminus" value="-"
							onClick="fn_deleteRow(<? echo $i; ?>);"/>
						</td>
					</tr>
					<?
				}
			}
			else
			{
				$i=0;$sl=0;
				?>
				<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
					<td width="30" align="center"><? echo $sl++;; ?></td>
					<td width="100">
						<input type="text" name="txtSeqNo[]" id="txtSeqNo_<?php echo $i ?>" value="1" class="text_boxes" style="width:80px"/>
					</td>
					<td width="100">
						<?
						echo create_drop_down( "cboCount_".$i, 80, $yarn_count_arr,"", 1, "-- Count --", $selected, "",0,"","","","","","","cboCount[]");
						?>
					</td>
					<td width="100">
						<?
						echo create_drop_down( "cboFeeding_".$i, 80, $feeding_arr,"", 1, "-- Feeding --", $selected, "",0,"","","","","","","cboFeeding[]");
						?>
					</td>
					<td>
						<input type="button" id="increase_<? echo $i; ?>" name="increase[]"
						style="width:30px" class="formbuttonplasminus" value="+"
						onClick="add_break_down_tr(<? echo $i; ?>)"/>
						<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]"
						style="width:30px" class="formbuttonplasminus" value="-"
						onClick="fn_deleteRow(<? echo $i; ?>);"/>
					</td>
				</tr>
				<?
			}
			?>
		</tbody>
	</table>
</div>
<table width="400" id="tbl_close">
	<tr>
		<td align="center">
			<input type="button" name="close" class="formbutton" value="Close" id="main_close"
			onClick="fnc_close();" style="width:100px"/>
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

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	/*if($_SESSION['logic_erp']['user_id'] != 1)
	{
		echo "5**0**0"; die;
	}*/

	//for color wise program qty distribution
	$colorPopupDataArr = array();
	$hdnColorWiseProgramData = str_replace("'", "", $hidden_color_wise_prog_data);
	if ($hdnColorWiseProgramData != "")
	{
		$expColorWiseProgramData = explode(",", $hdnColorWiseProgramData);
		for ($i = 0; $i < count($expColorWiseProgramData); $i++)
		{
			$colorWiseProgramData = explode("_", $expColorWiseProgramData[$i]);
			$colorPopupDataArr[$colorWiseProgramData[0]]['program_qty'] = $colorWiseProgramData[1];
			$colorPopupDataArr[$colorWiseProgramData[0]]['booking_qty'] = $colorWiseProgramData[3];
		}
	}
	//echo "<pre>";
	//print_r($colorPopupDataArr); die;
	$prog_no = str_replace("'", "", $update_dtls_id);
	$sql_ref="select ref_closing_status,id as prog_no from ppl_planning_info_entry_dtls where id in($prog_no)  and status_active=1";
	$prod_result=sql_select($sql_ref,1);
	$ref_closing_status=$prod_result[0][csf('ref_closing_status')];
	if($ref_closing_status==1)
	{
		echo "23**This prog no is closed";
		disconnect($con);
		die;
	}

	//for program no
	$programIdArr = array();
	$prog_id = trim($prog_id);
	$expProgId = explode(',', $prog_id);
	foreach($expProgId as $key=>$val)
	{
		$programIdArr[$val] = $val;
	}

	if($prog_no != '')
	{
		$expProgNo = explode(',', $prog_no);
		foreach($expProgNo as $key=>$val)
		{
			$programIdArr[$val] = $val;
		}
	}

	if ($operation == 0)
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), "yyyy-mm-dd", "");
		}
		else
		{
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), '', '', 1);
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), '', '', 1);
		}

		if (str_replace("'", '', $updateId) != "")
		{
			if($po_id!="")
			{
				$total_program_qnty = return_field_value("sum(program_qnty) program_qnty", "ppl_planning_info_entry_dtls", "mst_id=$updateId and status_active=1 and is_deleted=0 and po_id in($upd_plan_po_ids)", "program_qnty");
			}

			$program_balance = $total_program_qnty + str_replace("'", "", $txt_program_qnty)*1;
			$hdn_booking_qnty = number_format($hdn_booking_qnty,2,'.','');
			$program_balance = number_format($program_balance,2,'.','');

			if ( $program_balance  > $hdn_booking_qnty )
			{
				echo "14**Program quantity can not be greater than Booking quantity";
				disconnect($con);
				exit();
			}
		}
		else
		{

			if (str_replace("'", "", $txt_program_qnty) > $hdn_booking_qnty)
			{
				echo "14**Program quantity can not be greater than Booking quantity";
				disconnect($con);
				exit();
			}
		}

		$data_array = sql_select("select machine_id, distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where distribution_date >= '$start_date' group by machine_id, distribution_date order by distribution_date");

		foreach ($data_array as $row)
		{
			$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["days_complete"] = $row[csf('days_complete')];
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["distribution_date"] = $distribution_date;
		}

		$booking_no=str_replace("'","",$booking_no);
		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($knit_qty > str_replace("'", "", $txt_program_qnty))
		{
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}

		$id = '';
		if (str_replace("'", '', $updateId) == "")
		{
			$id = return_next_id("id", "ppl_planning_info_entry_mst", 1);
			$body_part_idArr=explode(',', $body_part_id);
			$body_part_id_mst=$body_part_idArr[0];
			$field_array = "id, company_id, buyer_id, booking_no, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, inserted_by, insert_date";

			$data_array = "(" . $id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $body_part_id_mst . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $fabric_typee . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		}
		else
		{
			$id = str_replace("'", '', $updateId);
			$flag = 1;
		}

		$dtls_id = return_next_id("id", "ppl_planning_info_entry_dtls", 1);
		$field_array_dtls = "id, mst_id, knitting_source, knitting_party, subcontract_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, machine_group, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio,  machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks, save_data, no_fo_feeder_data, collar_cuff_data, location_id, advice, grey_dia,attention, co_efficient,batch_no,knitting_pdo, inserted_by, insert_date";
		$data_array_dtls = "(" . $dtls_id . "," . $id . "," . $cbo_knitting_source . "," . $cbo_knitting_party . "," . $cbo_subcontract_party . "," . $hidden_color_id . "," . $cbo_color_range . "," . $txt_machine_dia . "," . $cbo_dia_width_type . "," . $txt_machine_gg . "," . $cbo_machine_group . "," . $txt_fabric_dia . "," . $txt_program_qnty . "," . $txt_stitch_length . "," . $txt_spandex_stitch_length . "," . $txt_draft_ratio . "," . $machine_id . "," . $txt_machine_capacity . "," . $txt_distribution_qnty . "," . $cbo_knitting_status . "," . $txt_start_date . "," . $txt_end_date . "," . $txt_program_date . "," . $cbo_feeder . "," . $txt_remarks . "," . $save_data . "," . $hidden_no_of_feeder_data . "," . $hidden_bodypartID_data . "," . $cbo_location_name . "," . $hidden_advice_data . "," . $txt_grey_dia . "," . $txt_attention . ",". $txt_co_efficient . ",". $txt_batch_no . ",". $txt_knitting_pdo . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		//for program id
		$programIdArr[$dtls_id] = $dtls_id;

		$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
		$field_array_plan_dtls = "id, mst_id, dtls_id, company_id, buyer_id, booking_no, po_id, start_date, finish_date, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, yarn_desc, program_qnty, inserted_by, insert_date";

		$data = str_replace("'", "", $data);
		//echo "10**".$data; die;
		if ($data != "")
		{
			$data = explode("_", $data);
			for ($i = 0; $i < count($data); $i++)
			{
				$plan_data = explode("**", $data[$i]);
				$booking_no = $booking_no;
				$start_date = $plan_data[1];
				$end_date = $plan_data[2];
				$po_id = $plan_data[3];
				$buyer_id = $plan_data[4];
				$body_part_id = $plan_data[5];
				$dia_width_type = $plan_data[6];
				$pre_cost_id = $plan_data[7];
				$desc = $plan_data[8];
				$gsm_weight = $plan_data[9];
				$dia_width = $plan_data[10];
				$determination_id = $plan_data[11];
				$booking_qnty = $plan_data[12];
				$color_type_id = $plan_data[13];

				if ($db_type == 0)
				{
					$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
					$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
				}
				else
				{
					$start_date = change_date_format($start_date, '', '', 1);
					$end_date = change_date_format($end_date, '', '', 1);
				}

				//$perc = ($booking_qnty / $tot_booking_qnty) * 100;
				//$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;

				//for color wise program qty distribution
				$prog_qnty = 0;
				$colorBookingQty = 0;
				$colorPopupBookingQty = 0;
				$colorPopupProgramQty = 0;
				$expColorBookingData = explode(",", $plan_data[16]);
				for ($z = 0; $z < count($expColorBookingData); $z++)
				{
					$colorBookingData = explode("-", $expColorBookingData[$z]);
					$colorBookingQty = $colorBookingData[1]*1;
					$colorPopupBookingQty = $colorPopupDataArr[$colorBookingData[0]]['booking_qty']*1;
					$colorPopupProgramQty = $colorPopupDataArr[$colorBookingData[0]]['program_qty']*1;
					if($colorPopupBookingQty !=0)
					{
						$prog_qnty += ($colorPopupProgramQty*$colorBookingQty)/$colorPopupBookingQty;
					}
					//echo "14**".$colorPopupProgramQty."=".$colorBookingQty."=".$colorPopupBookingQty."=".$prog_qnty."=";
				}

				if($prog_qnty > 0)
				{
					$prog_qnty = number_format($prog_qnty, 2, '.', '');

					if ($data_array_plan_dtls != "") $data_array_plan_dtls .= ",";

					$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $id . "," . $dtls_id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $po_id . ",'" . $start_date . "','" . $end_date . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $dia_width_type . ",0," . $pre_cost_id . "," . $prog_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$plan_dtls_id = $plan_dtls_id + 1;
				}
			}
		}

		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, date_wise_breakdown, inserted_by, insert_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty";

		$allowed_date_qnty_string = str_replace("'", "", $allowed_date_qnty_string);
		if($allowed_date_qnty_string != "")
		{
			$allowed_date_qnty_string = explode("__", $allowed_date_qnty_string);
			for ($i = 0; $i < count($allowed_date_qnty_string); $i++)
			{
				$machine_date_qnty_data = explode("=", $allowed_date_qnty_string[$i]);
				$machine_no = $machine_date_qnty_data[0];
				$date_qnty_data_str = $machine_date_qnty_data[1];
				$date_wise_machine_ref_data[$machine_no] = $date_qnty_data_str;
			}
		}
		/*echo "14**";
		print_r($date_wise_machine_ref_data);die;*/

		$data_array_machine_dtls_datewise ="";
		$save_data = str_replace("'", "", $save_data);
		if ($save_data != "")
		{
			$save_data = explode(",", $save_data);
			for ($i = 0; $i < count($save_data); $i++)
			{
				$machine_wise_data = explode("_", $save_data[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
				$capacity = $machine_wise_data[2];
				$qnty = $machine_wise_data[3];
				$noOfDays = $machine_wise_data[4];

				$dateWise_qnty = 0;
				$bl_qnty = $qnty;

				if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
				if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

				if ($startDate != "" && $endDate != "")
				{
					$date_wise_breakdown_str="";
					if($date_wise_machine_ref_data[$machine_id] != "")
					{
						$date_wise_breakdown_str = trim($date_wise_machine_ref_data[$machine_id]);
						$date_wise_breakdown_arr =  explode("*", $date_wise_breakdown_str);

						for ($j = 0; $j < count($date_wise_breakdown_arr); $j++)
						{
							$machine_date_data = explode(",", $date_wise_breakdown_arr[$j]);
							$curr_date = $machine_date_data[0];
							$dateWise_qnty = $machine_date_data[1];
							$days_complete = $machine_date_data[2];

							if($days_complete >= 1)
							{
								$fraction = 0;
							}else{
								$fraction = 1;
							}

							$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
							if($previous_occupied_days)
							{
								if($days_complete != (1-$previous_occupied_days))
								{
									echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
									disconnect($con);
									die;
								}
							}

							if ($db_type == 0) $curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-"); else $curr_date = change_date_format($curr_date, '', '', 1);



							if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
							$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $id . "," . $dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "')";
							$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
						}
					}
				}


				if ($db_type == 0) {
					$mstartDate = $startDate;
					$mendDate = $endDate;
				} else {
					$mstartDate = change_date_format($startDate, '', '', 1);
					$mendDate = change_date_format($endDate, '', '', 1);
				}

				if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $id . "," . $dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate ."','". $date_wise_breakdown_str. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}

		//echo "10**".$data_array_machine_dtls;die;
		$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
		$field_array_feeder_dtls = "id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder, inserted_by, insert_date";

		$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
		if ($hidden_no_of_feeder_data != "") {
			$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
			for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++) {
				$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
				$pre_cost_id = $color_wise_data[0];
				$color_id = $color_wise_data[1];
				$stripe_color_id = $color_wise_data[2];
				$no_of_feeder = $color_wise_data[3];

				if ($data_array_feeder_dtls != "") $data_array_feeder_dtls .= ",";

				$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $id . "," . $dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$feeder_dtls_id = $feeder_dtls_id + 1;
			}
		}

		$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
		if ($hidden_collarCuff_data != "")
		{
			$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
			$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part_id, grey_size, finish_size, qty_pcs, inserted_by, insert_date";

			$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
			for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
				$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
				$body_part_id = $collarCuff_wise_data[0];
				$grey_size = $collarCuff_wise_data[1];
				$finish_size = $collarCuff_wise_data[2];
				$qty_pcs = $collarCuff_wise_data[3];

				if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

				$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $id . "," . $dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
			}
		}

		$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
		$hidden_size_wise_prog_string = str_replace("'", "", $hidden_size_wise_prog_string);
		if( $hidden_color_wise_prog_data != "" )
		{
			$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
			$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty,size_wise_prog_string, inserted_by, insert_date";

			$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
			$size_wise_prog_data = explode("**", $hidden_size_wise_prog_string);
			$color_wise_mstID = array();
			for ($i = 0; $i < count($color_wise_prog_data); $i++)
			{
				if($color_wise_prog_data[$i] != '')
				{
					$sizeWiseDataString=$size_wise_prog_data[$i];
					$colorWiseProgData = array();
					$color_id = '';
					$color_prog_qty = 0;

					$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
					$color_id = $colorWiseProgData[0];
					$color_prog_qty = $colorWiseProgData[1];

					if ($data_array_color_wise_break_down != "")
						$data_array_color_wise_break_down .= ",";

					if($color_prog_qty>0)
					{
						$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $id . "," . $dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "','" . $sizeWiseDataString . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$color_wise_mstID[$color_id]=$color_wise_break_down_id;
						$color_wise_break_down_id = $color_wise_break_down_id + 1;

					}
				}
			}
		}


		if( $hidden_color_wise_prog_data != "" && $hidden_size_wise_prog_string != "")
		{
			$size_wise_break_down_id = return_next_id("id", "ppl_size_wise_break_down ", 1);
			$field_array_size_wise_break_down = "id,color_wise_mst_id, plan_id, program_no, color_id,grey_size_id,finish_size_id,per_kg, kg_wise_total_qnty,body_part_id,current_qty, inserted_by, insert_date";
			$size_wise_prog_datas = explode("**", $hidden_size_wise_prog_string);
			for ($inc = 0; $inc < count($size_wise_prog_datas); $inc++)
			{
				$size_wise_prog_dataz = explode("##", $size_wise_prog_datas[$inc]);
				for ($i = 0; $i < count($size_wise_prog_dataz); $i++)
				{
					if($size_wise_prog_dataz[$i] != '')
					{

						$sizeWiseProgData = array();
						$color_id = '';
						$gmts_size_id = '';
						$finish_size_id = '';
						$per_kg = '';
						$size_wise_current_qty = '';
						$size_kg_wise_prog_qty = 0;

						$sizeWiseProgData = explode("_", $size_wise_prog_dataz[$i]);
						$color_id = $sizeWiseProgData[0];
						$gmts_size_id = $sizeWiseProgData[1];
						$finish_size_id = $sizeWiseProgData[2];
						$per_kg = $sizeWiseProgData[3];
						$size_kg_wise_prog_qty = $sizeWiseProgData[4];
						$size_wise_body_part_id = $sizeWiseProgData[6];
						$size_wise_current_qty = $sizeWiseProgData[7];

						if ($data_array_size_wise_break_down != "")
							$data_array_size_wise_break_down .= ",";

						if($size_kg_wise_prog_qty>0)
						{
							$colorWiseMstId=$color_wise_mstID[$color_id];
							$data_array_size_wise_break_down .= "(" . $size_wise_break_down_id . "," . $colorWiseMstId . "," . $id . "," . $dtls_id . ",'" . $color_id . "','" . $gmts_size_id . "','" . $finish_size_id . "','" . $per_kg . "','" . $size_kg_wise_prog_qty . "','" . $size_wise_body_part_id . "','" . $size_wise_current_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$size_wise_break_down_id = $size_wise_break_down_id + 1;
						}

					}
				}

			}
		}

		$rID = $rID2 = $rIDdtls = $rID3 = $rID4 = $rID5 = $rID6 = $rID7 = true;

		if (str_replace("'", '', $updateId) == "") {
		//echo "5**insert into ppl_planning_info_entry_mst (".$field_array.") Values ".$data_array."";die;
			$rID = sql_insert("ppl_planning_info_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		} else {
			$flag = 1;
		}

		//echo "5**insert into ppl_planning_info_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		$rID2 = sql_insert("ppl_planning_info_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($data != "") {
			if ($data_array_plan_dtls != "") {
			//echo"insert into ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") Values ".$data_array_plan_dtls."";die;
				$rIDdtls = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
				if ($flag == 1) {
					if ($rIDdtls) $flag = 1; else $flag = 0;
				}
			}
		}

		if ($save_data != "") {
			if ($data_array_machine_dtls != "") {
			//echo "10**insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1) {
					if ($rID3) $flag = 1; else $flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID4) $flag = 1; else $flag = 0;
				}
			}
		}

		if ($hidden_no_of_feeder_data != "") {
			if ($data_array_feeder_dtls != "") {
			//echo "10**insert into ppl_planning_feeder_dtls (".$field_array_feeder_dtls.") Values ".$data_array_feeder_dtls."";die;
				$rID5 = sql_insert("ppl_planning_feeder_dtls", $field_array_feeder_dtls, $data_array_feeder_dtls, 0);
				if ($flag == 1) {
					if ($rID5) $flag = 1; else $flag = 0;
				}
			}
		}

		if ($data_array_collar_cuff_dtls != "") {
		//echo "10**insert into ppl_planning_collar_cuff_dtls (".$field_array_collar_cuff_dtls.") Values ".$data_array_collar_cuff_dtls."";die;
			$rID6 = sql_insert("ppl_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);
			if ($flag == 1) {
				if ($rID6) $flag = 1; else $flag = 0;
			}
		}


		if ($data_array_color_wise_break_down != "") {
		//echo "10**insert into ppl_color_wise_break_down (".$field_array_color_wise_break_down.") Values ".$data_array_color_wise_break_down."";die;
			$rID6 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
			if ($flag == 1) {
				if ($rID6) $flag = 1; else $flag = 0;
			}
		}
		if ($data_array_size_wise_break_down != "") {
		//echo "10**insert into ppl_size_wise_break_down (".$field_array_size_wise_break_down.") Values ".$data_array_size_wise_break_down."";die;
			$rID7 = sql_insert("ppl_size_wise_break_down", $field_array_size_wise_break_down, $data_array_size_wise_break_down, 0);
			if ($flag == 1) {
				if ($rID7) $flag = 1; else $flag = 0;
			}
		}

		//echo "5**$rID##$rID2##$rIDdtls##$rID3##$rID4##$rID5##$rID6##$rID7";die;

		//$prog_id_string=implode(',', array_unique($programIdArr));
		$prog_id_string=implode(',', array_filter(array_unique($programIdArr)));
		$prog_id_string=ltrim($prog_id_string," ,");
		//echo "10**".$prog_id_string; die;

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**0**".$prog_id_string;
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0**".$prog_id_string;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $id . "**0**".$prog_id_string;
			} else {
				oci_rollback($con);
				echo "5**0**0**".$prog_id_string;
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

		$sql = sql_select("select id,recv_number from inv_receive_master where entry_form=2 and receive_basis=2 and booking_no= $update_dtls_id and status_active=1 and is_deleted=0");

		$productData = array();
		$productionID = array();
		foreach($sql as $prow)
		{
			$productionID[$prow[csf('id')]] = $prow[csf('id')];
			$productData[$prow[csf('id')]] = $prow[csf('recv_number')];
		}

		$productionIds  = implode(',', $productionID);
		if($productionIds!="")
		{
			$grey_recv_arr = return_library_array("select mst_id, sum(grey_receive_qnty) as recv from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0 and mst_id in($productionIds) group by mst_id", 'mst_id', 'recv');
		}
		//print_r($grey_recv_arr);

		$programQnty = str_replace("'", "", $txt_program_qnty)*1;
		$preProgramQnty = str_replace("'", "", $hiddenProgramQnty)*1;
		$balanceQnty = str_replace("'", "", $balanceProgramQnty)*1;
		$pic_up_po_ids = str_replace("'", "", $pic_up_po_ids);
		$upd_plan_po_ids = str_replace("'", "", $upd_plan_po_ids);

		$exp_pic_up_po_ids = explode(",", $pic_up_po_ids);
		foreach($exp_pic_up_po_ids as $key=>$val)
		{
			$pic_up_po_ids_arr[$val] = $val;
		}
		//$pic_up_po_ids_arr = explode(",", $pic_up_po_ids);
		asort($pic_up_po_ids_arr);

		$exp_upd_plan_po_ids = explode(",", $upd_plan_po_ids);
		foreach($exp_upd_plan_po_ids as $key=>$val)
		{
			$updplan_po_id_arr[$val] = $val;
		}
		//$updplan_po_id_arr = explode(",", $upd_plan_po_ids);
		asort($updplan_po_id_arr);

		$picup_poid_string = implode(",", $pic_up_po_ids_arr);
		$upd_plan_poid_string = implode(",", $updplan_po_id_arr);

		if($picup_poid_string!="")
		{
			if( $picup_poid_string != $upd_plan_poid_string)
			{
				echo "14**This program is attached another order also.";
				disconnect($con);
				exit();
			}
		}

		if(number_format(($programQnty - $preProgramQnty),2,'.','') > number_format($balanceQnty,2,'.','') )
		{
			echo "14**Program Qty. Can not Be Greater Than Booking Balance Qty.";
			disconnect($con);
			exit();
		}

		/*
		| if production found then
		| machine dia, Fabric Dia and strich length can't update
		*/
		if(!empty($productData))
		{
			$sqlProg = sql_select("SELECT knitting_party AS KNITTING_PARTY, color_id AS COLOR_ID, color_range AS COLOR_RANGE, machine_gg AS MACHINE_GG, machine_group AS MACHINE_GROUP, machine_dia AS MACHINE_DIA, width_dia_type AS WIDTH_DIA_TYPE, fabric_dia AS FABRIC_DIA, stitch_length AS STITCH_LENGTH FROM ppl_planning_info_entry_dtls WHERE id = ".$update_dtls_id."");
			$exist_machine_dia = '';
			$exist_finish_dia = '';
			$exist_stitch_length = '';
			$exist_knitting_party = '';
			$exist_color_range = '';
			$exist_machine_gg = '';
			$exist_machine_group = '';
			$exist_dia_width_type = '';
			$exist_color = array();
			foreach($sqlProg as $row)
			{
				$exist_machine_dia = $row['MACHINE_DIA'];
				$exist_finish_dia = $row['FABRIC_DIA'];
				$exist_stitch_length = $row['STITCH_LENGTH'];
				$exist_knitting_party = $row['KNITTING_PARTY'];
				$exist_color_range = $row['COLOR_RANGE'];
				$exist_machine_gg = $row['MACHINE_GG'];
				$exist_machine_group = $row['MACHINE_GROUP'];
				$exist_dia_width_type = $row['WIDTH_DIA_TYPE'];

				//for color
				$expColor = explode(',', $row['COLOR_ID']);
				foreach($expColor as $key=>$val)
				{
					$exist_color[$val] = $val;
				}
			}

			//for knitting party
			if($exist_knitting_party != str_replace("'","",$cbo_knitting_party))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change knitting party, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for color
			$expClr = explode(',', str_replace("'","",$hidden_color_id));
			foreach($expClr as $key=>$val)
			{
				if (!in_array($val, $exist_color))
				{
					foreach ($productData as $proid => $proid_no)
					{
						echo "14**You can not change color, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
					}
					disconnect($con);
					exit();
				}
			}

			//for color range
			if($exist_color_range != str_replace("'","",$cbo_color_range))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change color range, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for machine gg
			if($exist_machine_gg != str_replace("'","",$txt_machine_gg))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change machine gg, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for machine group
			if($exist_machine_group != str_replace("'","",$cbo_machine_group))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change machine group, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for machine dia
			if($exist_machine_dia != str_replace("'","",$txt_machine_dia))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change machine dia, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for width dia type
			if($exist_dia_width_type != str_replace("'","",$cbo_dia_width_type))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change dia width type, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for fabric dia
			if($exist_finish_dia != str_replace("'","",$txt_fabric_dia))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change Finish Fabric Dia, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}

			//for stitch length
			if($exist_stitch_length != str_replace("'","",$txt_stitch_length))
			{
				foreach ($productData as $proid => $proid_no)
				{
					echo "14**You can not change stitch length, cause production had been made allready.\nproduction id is ".$proid_no." and quantity is ".$grey_recv_arr[$proid];
				}
				disconnect($con);
				exit();
			}
		}

		$allowed_date_qnty_string = str_replace("'", "", $allowed_date_qnty_string);
		if($allowed_date_qnty_string != "")
		{
			$allowed_date_qnty_string = explode("__", $allowed_date_qnty_string);
			for ($i = 0; $i < count($allowed_date_qnty_string); $i++)
			{
				$machine_date_qnty_data = explode("=", $allowed_date_qnty_string[$i]);
				$machine_no = $machine_date_qnty_data[0];
				$date_qnty_data_str = $machine_date_qnty_data[1];
				$date_wise_machine_ref_data[$machine_no] = $date_qnty_data_str;
			}
		}

		if ($db_type == 0) {
			$txt_start_date = change_date_format(str_replace("'", "", trim($txt_start_date)), "yyyy-mm-dd", "");
		} else {
			$txt_start_date = change_date_format(str_replace("'", "", trim($txt_start_date)), '', '', 1);
		}

		$data_array = sql_select("select machine_id, distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where distribution_date >= '$txt_start_date' group by machine_id, distribution_date order by distribution_date");

		foreach ($data_array as $row)
		{
			$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["days_complete"] = $row[csf('days_complete')];
			$previous_occupied_arr[$row[csf('machine_id')]][$distribution_date]["distribution_date"] = $distribution_date;
		}

		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");
		if ($knit_qty > str_replace("'", "", $txt_program_qnty)) {
			echo "14**Program Qty. Can not Be Less Than Knitting Qty.";
			disconnect($con);
			exit();
		}

		if ($knit_qty > 0)
		{
			$field_array_update = "machine_dia*width_dia_type*color_range*machine_gg*machine_group*stitch_length*spandex_stitch_length*draft_ratio*program_qnty*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*remarks*attention*co_efficient*batch_no*save_data*advice*grey_dia*knitting_pdo*updated_by*update_date";
			$data_array_update = $txt_machine_dia . "*" . $cbo_dia_width_type . "*" . $cbo_color_range . "*" . $txt_machine_gg . "*" . $cbo_machine_group . "*" . $txt_stitch_length . "*" . $txt_spandex_stitch_length . "*" . $txt_draft_ratio . "*" . $txt_program_qnty . "*" . $machine_id . "*" . $txt_machine_capacity . "*" . $txt_distribution_qnty . "*" . $cbo_knitting_status . "*'" . $txt_start_date . "'*" . $txt_end_date . "*" . $txt_remarks . "*" . $txt_attention . "*" . $txt_co_efficient . "*" . $txt_batch_no . "*" . $save_data . "*" .$hidden_advice_data . "*" . $txt_grey_dia. "*" . $txt_knitting_pdo . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
			$field_array_plan_dtls = "id, mst_id, dtls_id, company_id, buyer_id, booking_no, po_id, start_date, finish_date, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, yarn_desc, program_qnty, inserted_by, insert_date";

			$data = str_replace("'", "", $data);
			if ($data != "")
			{
				$data = explode("_", $data);
				for ($i = 0; $i < count($data); $i++)
				{
					$plan_data = explode("**", $data[$i]);
					$booking_no = $booking_no;
					$start_date = trim($plan_data[1]);
					$end_date = trim($plan_data[2]);
					$po_id = $plan_data[3];
					$buyer_id = $plan_data[4];
					$body_part_id = $plan_data[5];
					$dia_width_type = $plan_data[6];
					$pre_cost_id = $plan_data[7];
					$desc = $plan_data[8];
					$gsm_weight = $plan_data[9];
					$dia_width = $plan_data[10];
					$determination_id = $plan_data[11];
					$booking_qnty = $plan_data[12];
					$color_type_id = $plan_data[13];

					//$perc = ($booking_qnty / $tot_booking_qnty) * 100;
					//$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;

					//for color wise program qty distribution
					$prog_qnty = 0;
					$colorBookingQty = 0;
					$colorPopupBookingQty = 0;
					$colorPopupProgramQty = 0;
					$expColorBookingData = explode(",", $plan_data[16]);
					for ($z = 0; $z < count($expColorBookingData); $z++)
					{
						$colorBookingData = explode("-", $expColorBookingData[$z]);
						$colorBookingQty = $colorBookingData[1]*1;
						$colorPopupBookingQty = $colorPopupDataArr[$colorBookingData[0]]['booking_qty']*1;
						$colorPopupProgramQty = $colorPopupDataArr[$colorBookingData[0]]['program_qty']*1;
						if($colorPopupBookingQty !=0)
						{
							$prog_qnty += ($colorPopupProgramQty*$colorBookingQty)/$colorPopupBookingQty;
						}
						//echo "14**".$colorPopupProgramQty."=".$colorBookingQty."=".$colorPopupBookingQty."=".$prog_qnty."=";
					}

					if ($db_type == 0) {
						$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
						$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
					} else {
						$start_date = change_date_format($start_date, '', '', 1);
						$end_date = change_date_format($end_date, '', '', 1);

					}

					if($prog_qnty > 0)
					{
						$prog_qnty = number_format($prog_qnty, 2, '.', '');
						if ($data_array_plan_dtls != "") $data_array_plan_dtls .= ",";

						$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $po_id . ",'" . $start_date . "','" . $end_date . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $dia_width_type . ",0," . $pre_cost_id . "," . $prog_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$plan_dtls_id = $plan_dtls_id + 1;
					}
				}
			}
			$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
			$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, date_wise_breakdown, inserted_by, insert_date";

			$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
			$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty";

			$save_data = str_replace("'", "", $save_data);
			if ($save_data != "") {
				$save_data = explode(",", $save_data);
				for ($i = 0; $i < count($save_data); $i++) {
					$machine_wise_data = explode("_", $save_data[$i]);
					$machine_id = $machine_wise_data[0];
					$dia = $machine_wise_data[1];
					$capacity = $machine_wise_data[2];
					$qnty = $machine_wise_data[3];
					$noOfDays = $machine_wise_data[4];

					$dateWise_qnty = 0;
					$bl_qnty = $qnty;

					if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
					if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

					$date_wise_breakdown_str="";
					if($date_wise_machine_ref_data[$machine_id] != "")
					{
						$date_wise_breakdown_str = trim($date_wise_machine_ref_data[$machine_id]);
						$date_wise_breakdown_arr =  explode("*", $date_wise_breakdown_str);

						for ($j = 0; $j < count($date_wise_breakdown_arr); $j++)
						{
							$machine_date_data = explode(",", $date_wise_breakdown_arr[$j]);

							$curr_date = $machine_date_data[0];
							$dateWise_qnty = $machine_date_data[1];
							$days_complete = $machine_date_data[2];
							$days_complete = number_format($days_complete,2,".","");

							if($days_complete >= 1)
							{
								$fraction = 0;
							}else{
								$fraction = 1;
							}


							$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
							if($previous_occupied_days)
							{
								if($days_complete != (1-$previous_occupied_days))
								{
									echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
									disconnect($con);
									die;
								}
							}

							if ($db_type == 0) $curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-"); else $curr_date = change_date_format($curr_date, '', '', 1);

							if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
							$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "')";
							$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
						}
					}
					else
					{
						//$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty";

						//===========shuru

						$distribute_qnty = $qnty;
						$capacity_qnty = $capacity;
						$no_of_days = $noOfDays;
						if ($db_type == 0) {
							$start_date = change_date_format(trim($startDate), "yyyy-mm-dd", "");
						} else {
							$start_date = change_date_format(trim($startDate), '', '', 1);
						}

						$pre_sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$start_date' group by distribution_date order by distribution_date";

						$data_array = sql_select($pre_sql);
						if (count($data_array) > 0)
						{
							foreach ($data_array as $row)
							{
								$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
								$previous_occupied_arr_without_ref[$distribution_date]["days_complete"] = $row[csf('days_complete')];
								$previous_occupied_arr_without_ref[$distribution_date]["distribution_date"] = $distribution_date;
							}
						}

						$present_day = $start_date;
						$without_ref_dateWise_qnty = ""; $end_date_ref = "";
						while($distribute_qnty > 0)
						{
							$present_day = date("d-m-Y",strtotime($present_day));

							if($previous_occupied_arr_without_ref[$present_day]["distribution_date"])
							{
								if($previous_occupied_arr_without_ref[$present_day]["distribution_date"] == $present_day)
								{
									if ($previous_occupied_arr_without_ref[$present_day]["days_complete"] >= 1)
									{
										$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
									}
									else
									{
										if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
										{
											$dist_qnty = $capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]);
											$without_ref_dateWise_qnty .= $present_day.",".$dist_qnty.",".($dist_qnty/$capacity_qnty)."*";
										}
										else
										{
											$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
										}

										$distribute_qnty = $distribute_qnty - ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]));
										$end_date_ref =  $present_day;
										$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
									}
								}
							}
							else
							{
								if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
								{
									$without_ref_dateWise_qnty .= $present_day.",".$capacity_qnty.",".($capacity_qnty/$capacity_qnty)."*";
								}
								else
								{
									$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
								}

								$end_date_ref =  $present_day;
								$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
								$distribute_qnty = $distribute_qnty - $capacity_qnty;

							}


						}

						$without_ref_dateWise_qnty = chop($without_ref_dateWise_qnty,"*");

						$without_ref_arr =  array_filter(explode("*", $without_ref_dateWise_qnty));

						foreach ($without_ref_arr as  $ref_value)
						{
							$ref_value = explode(",", $ref_value);

							$curr_date =  $ref_value[0];
							$dateWise_qnty =  $ref_value[1];
							$days_complete =  $ref_value[2];
							$days_complete = number_format($days_complete,2,".","");

							if($days_complete >= 1){
								$fraction =0;
							}else{
								$fraction =1;
							}


							$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
							if($previous_occupied_days)
							{
								if($days_complete != (1-$previous_occupied_days))
								{
									echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
									disconnect($con);
									die;
								}
							}


							if ($db_type == 0) $curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-"); else $curr_date = change_date_format($curr_date, '', '', 1);


							if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
							$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "')";
							$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;


						}

						//echo "10**".$data_array_machine_dtls_datewise;die;

						//sesh----------------------------------------------=========================-------------------------
					}


					if ($db_type == 0) {
						$mstartDate = $startDate;
						$mendDate = $endDate;
					} else {
						$mstartDate = change_date_format($startDate, '', '', 1);
						$mendDate = change_date_format($endDate, '', '', 1);
					}

					if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
					$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $machine_id . ",'" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "','" .$date_wise_breakdown_str. "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$machine_dtls_id = $machine_dtls_id + 1;
				}
			}

			// == Color prgo data  ==
			$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
			$hidden_size_wise_prog_string = str_replace("'", "", $hidden_size_wise_prog_string);
			if ($hidden_color_wise_prog_data != "")
			{
				$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
				$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty,size_wise_prog_string, inserted_by, insert_date";
				$field_array_color_wise_prog_update = "color_id*color_prog_qty*updated_by*update_date*status_active*is_deleted";

				$color_wise_prog_data = array();
				$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
				$size_wise_prog_data = explode("**", $hidden_size_wise_prog_string);
				$color_wise_mstID = array();
				for ($i = 0; $i < count($color_wise_prog_data); $i++)
				{
					if($color_wise_prog_data[$i] != '')
					{
						$colorWiseProgData = array();
						$color_id = '';
						$color_prog_qty = 0;
						$color_wise_break_down_udpdateId = 0;

						$sizeWiseDataString=$size_wise_prog_data[$i];
						$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
						$color_id = $colorWiseProgData[0];
						$color_prog_qty = $colorWiseProgData[1];
						$color_wise_break_down_udpdateId = $colorWiseProgData[2];

						if ($color_wise_break_down_udpdateId>0)
						{
							$status_active = 1;
							$is_deleted = 0;
							if($color_prog_qty <= 0)
							{
								$status_active = 0;
								$is_deleted = 1;
							}

							$colorprog_upd_id_arr[] = $color_wise_break_down_udpdateId;
							$data_array_color_wise_prog_update[$color_wise_break_down_udpdateId] = explode("*", ("'" . $color_id . "'*'". $color_prog_qty . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'". $status_active . "'*'". $is_deleted . "'"));

							$color_wise_mstID[$color_id]=$color_wise_break_down_udpdateId;
						}
						else
						{
							if ($data_array_color_wise_break_down != "")
								$data_array_color_wise_break_down .= ",";
							if($color_prog_qty>0)
							{
								$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "','" . $sizeWiseDataString . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$color_wise_mstID[$color_id]=$color_wise_break_down_id;
								$color_wise_break_down_id = $color_wise_break_down_id + 1;
							}
						}
					}
				}
			}

			// == Size prog data ==

			if( $hidden_color_wise_prog_data != "" && $hidden_size_wise_prog_string != "")
			{
				$size_wise_break_down_id = return_next_id("id", "ppl_size_wise_break_down ", 1);
				$field_array_size_wise_break_down = "id,color_wise_mst_id, plan_id, program_no, color_id,grey_size_id,finish_size_id,per_kg, kg_wise_total_qnty,body_part_id,current_qty, inserted_by, insert_date";
				$size_wise_prog_datas = explode("**", $hidden_size_wise_prog_string);
				for ($inc = 0; $inc < count($size_wise_prog_datas); $inc++)
				{
					$size_wise_prog_dataz = explode("##", $size_wise_prog_datas[$inc]);
					for ($i = 0; $i < count($size_wise_prog_dataz); $i++)
					{
						if($size_wise_prog_dataz[$i] != '')
						{

							$sizeWiseProgData = array();
							$color_id = '';
							$gmts_size_id = '';
							$finish_size_id = '';
							$per_kg = '';
							$size_wise_current_qty = '';
							$size_kg_wise_prog_qty = 0;

							$sizeWiseProgData = explode("_", $size_wise_prog_dataz[$i]);
							$color_id = $sizeWiseProgData[0];
							$gmts_size_id = $sizeWiseProgData[1];
							$finish_size_id = $sizeWiseProgData[2];
							$per_kg = $sizeWiseProgData[3];
							$size_kg_wise_prog_qty = $sizeWiseProgData[4];
							$size_wise_body_part_id = $sizeWiseProgData[6];
							$size_wise_current_qty = $sizeWiseProgData[7];
							$size_wise_primary_update_id = $sizeWiseProgData[8];


							if ($data_array_size_wise_break_down != "")
								$data_array_size_wise_break_down .= ",";

							if ($size_wise_primaryUpdateId != "")
								$size_wise_primaryUpdateId .= ",";

							if($size_kg_wise_prog_qty>0)
							{
								$colorWiseMstId=$color_wise_mstID[$color_id];
								$data_array_size_wise_break_down .= "(" . $size_wise_break_down_id . "," . $colorWiseMstId . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $gmts_size_id . "','" . $finish_size_id . "','" . $per_kg . "','" . $size_kg_wise_prog_qty . "','" . $size_wise_body_part_id . "','" . $size_wise_current_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$size_wise_break_down_id = $size_wise_break_down_id + 1;

								$size_wise_primaryUpdateId.=$size_wise_primary_update_id;

							}
						}
					}

				}
			}
			if ($data != "") {
				if ($data_array_plan_dtls != "") {
					$delete = execute_query("delete from ppl_planning_entry_plan_dtls where dtls_id=$update_dtls_id", 0);
					if ($delete) $flag = 1; else $flag = 0;

                    //  echo "10**insert into ppl_planning_entry_plan_dtls (".$field_array_plan_dtls.") Values ".$data_array_plan_dtls."";die;
					$rID2 = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
					if ($flag == 1) {
						if ($rID2) $flag = 1; else $flag = 0;
					}
				}
			}
            //echo $data_array_update; die();
			$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
			if ($rID) $flag = 1; else $flag = 0;
			if ($flag == 1)
			{
				$posted_flag_updated = execute_query("update ppl_planning_info_entry_dtls set is_posted_sql=2 where id = $update_dtls_id",0);
				if ($posted_flag_updated)
					$flag = 1;
				else
					$flag = 0;
			}
			$deletem = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);
			$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);

			$rID3 = true;
			$rID4 = true;
			$rID7 = true;
			$rID8 = true;
			$rID9 = true;
			if ($save_data != "") {
				if ($data_array_machine_dtls != "") {
					//echo"insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
					$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				}

				if ($data_array_machine_dtls_datewise != "") {
					//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
					$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				}
			}

			if ($hidden_color_wise_prog_data != "")
			{
				if (count($colorprog_upd_id_arr)>0) { // update

					if (count($data_array_color_wise_prog_update) > 0) {

						//echo "10**".bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr);
						$rID7 = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr));
					}
				}

				if ($data_array_color_wise_break_down != "") { // new color insert
					//echo "10**insert into ppl_color_wise_break_down (".$field_array_color_wise_break_down.") Values ".$data_array_color_wise_break_down."";die;
					$rID8 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
				}
			}
			//=======Size wise ==============
			if ($data_array_size_wise_break_down != "") {
				foreach ($color_wise_mstID as $colorIDS => $colorMstID) {
					$delete_size_tbl = execute_query("delete from ppl_size_wise_break_down where color_wise_mst_id=$colorMstID", 0);
				}
				if ($delete_size_tbl) $flag = 1; else $flag = 0;

				//echo "10**insert into ppl_size_wise_break_down (".$field_array_size_wise_break_down.") Values ".$data_array_size_wise_break_down."";die;
				$rID9 = sql_insert("ppl_size_wise_break_down", $field_array_size_wise_break_down, $data_array_size_wise_break_down, 0);
				if ($flag == 1) {
					if ($rID9) $flag = 1; else $flag = 0;
				}
			}



            //echo "10**".$rID ."&&". $deletem ."&&". $delete_datewise ."&&". $rID3 ."&&". $rID4;die;
			if ($db_type == 0) {
				if ($rID && $rID2 && $deletem && $delete_datewise && $rID3 && $rID4 && $rID7 && $rID8 && $rID9 && $posted_flag_updated) {
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $updateId) . "**0**".implode(',', $programIdArr);
				} else {
					mysql_query("ROLLBACK");
					echo "6**0**1**".implode(',', $programIdArr);
				}
			} else if ($db_type == 2 || $db_type == 1) {
				if ($rID && $rID2 && $deletem && $delete_datewise && $rID3 && $rID4 && $rID7 && $rID8 && $rID9 && $posted_flag_updated) {
					oci_commit($con);
					echo "1**" . str_replace("'", "", $updateId) . "**0**".implode(',', $programIdArr);
				} else {
					oci_rollback($con);
					echo "6**0**1**".implode(',', $programIdArr);
				}
			}
		}
		else
		{
			$color_id = 0;
			$field_array_update = "knitting_source*knitting_party*subcontract_party*color_id*color_range*machine_dia*width_dia_type*machine_gg*machine_group*fabric_dia*program_qnty*stitch_length*spandex_stitch_length*draft_ratio*machine_id*machine_capacity*distribution_qnty*status*start_date*end_date*program_date*feeder*remarks*attention*co_efficient*batch_no*save_data*no_fo_feeder_data*collar_cuff_data*location_id*advice*grey_dia*knitting_pdo*updated_by*update_date";

			$data_array_update = $cbo_knitting_source . "*" . $cbo_knitting_party . "*" . $cbo_subcontract_party . "*" . $hidden_color_id . "*" . $cbo_color_range . "*" . $txt_machine_dia . "*" . $cbo_dia_width_type . "*" . $txt_machine_gg . "*" . $cbo_machine_group . "*" . $txt_fabric_dia . "*" . $txt_program_qnty . "*" . $txt_stitch_length . "*" . $txt_spandex_stitch_length . "*" . $txt_draft_ratio . "*" . $machine_id . "*" . $txt_machine_capacity . "*" . $txt_distribution_qnty . "*" . $cbo_knitting_status . "*'" . $txt_start_date . "'*" . $txt_end_date . "*" . $txt_program_date . "*" . $cbo_feeder . "*" . $txt_remarks . "*" . $txt_attention ."*". $txt_co_efficient ."*" . $txt_batch_no ."*" .$save_data . "*" . $hidden_no_of_feeder_data . "*" . $hidden_collarCuff_data . "*" . $cbo_location_name . "*" . $hidden_advice_data . "*" . $txt_grey_dia . "*" . $txt_knitting_pdo . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$plan_dtls_id = return_next_id("id", "ppl_planning_entry_plan_dtls", 1);
			$field_array_plan_dtls = "id, mst_id, dtls_id, company_id, buyer_id, booking_no, po_id, start_date, finish_date, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, yarn_desc, program_qnty, inserted_by, insert_date";

			$data = str_replace("'", "", $data);
			if ($data != "") {
				$data = explode("_", $data);
				for ($i = 0; $i < count($data); $i++) {
					$plan_data = explode("**", $data[$i]);
					$booking_no = $plan_data[0];
					$start_date = trim($plan_data[1]);
					$end_date = trim($plan_data[2]);
					$po_id = $plan_data[3];
					$buyer_id = $plan_data[4];
					$body_part_id = $plan_data[5];
					$dia_width_type = $plan_data[6];
					$pre_cost_id = $plan_data[7];
					$desc = $plan_data[8];
					$gsm_weight = $plan_data[9];
					$dia_width = $plan_data[10];
					$determination_id = $plan_data[11];
					$booking_qnty = $plan_data[12];
					$color_type_id = $plan_data[13];

					//$perc = ($booking_qnty / $tot_booking_qnty) * 100;
					//$prog_qnty = ($perc * str_replace("'", '', $txt_program_qnty)) / 100;

					//for color wise program qty distribution
					$prog_qnty = 0;
					$colorBookingQty = 0;
					$colorPopupBookingQty = 0;
					$colorPopupProgramQty = 0;
					$expColorBookingData = explode(",", $plan_data[16]);
					for ($z = 0; $z < count($expColorBookingData); $z++)
					{
						$colorBookingData = explode("-", $expColorBookingData[$z]);
						$colorBookingQty = $colorBookingData[1]*1;
						$colorPopupBookingQty = $colorPopupDataArr[$colorBookingData[0]]['booking_qty']*1;
						$colorPopupProgramQty = $colorPopupDataArr[$colorBookingData[0]]['program_qty']*1;
						if($colorPopupBookingQty !=0)
						{
							$prog_qnty += ($colorPopupProgramQty*$colorBookingQty)/$colorPopupBookingQty;
						}
						//echo "14**".$colorPopupProgramQty."=".$colorBookingQty."=".$colorPopupBookingQty."=".$prog_qnty."=";
					}
					if ($db_type == 0) {
						$start_date = change_date_format($start_date, "yyyy-mm-dd", "-");
						$end_date = change_date_format($end_date, "yyyy-mm-dd", "-");
					} else {
						$start_date = change_date_format($start_date, '', '', 1);
						$end_date = change_date_format($end_date, '', '', 1);
					}

					if($prog_qnty > 0)
					{
						$prog_qnty = number_format($prog_qnty, 2, '.', '');
						if ($data_array_plan_dtls != "") $data_array_plan_dtls .= ",";

						$data_array_plan_dtls .= "(" . $plan_dtls_id . "," . $updateId . "," . $update_dtls_id . "," . $companyID . "," . $buyer_id . ",'" . $booking_no . "'," . $po_id . ",'" . $start_date . "','" . $end_date . "'," . $body_part_id . "," . $color_type_id . "," . $determination_id . ",'" . $desc . "','" . $gsm . "','" . $dia . "'," . $dia_width_type . ",0," . $pre_cost_id . "," . $prog_qnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$plan_dtls_id = $plan_dtls_id + 1;
					}
				}
			}
			//echo "10**".$data_array_plan_dtls; oci_rollback($con);die;
			$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
			$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date,date_wise_breakdown, inserted_by, insert_date";
			$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
			$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty";

			$save_data = str_replace("'", "", $save_data);
			if ($save_data != "")
			{
				$save_data = explode(",", $save_data);
				for ($i = 0; $i < count($save_data); $i++)
				{
					$machine_wise_data = explode("_", $save_data[$i]);
					$machine_id = $machine_wise_data[0];
					$dia = $machine_wise_data[1];
					$capacity = $machine_wise_data[2];
					$qnty = $machine_wise_data[3];
					$noOfDays = $machine_wise_data[4];

					$dateWise_qnty = 0;
					$bl_qnty = $qnty;

					if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
					if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

					if ($startDate != "" && $endDate != "")
					{
						$date_wise_breakdown_str="";
						if($date_wise_machine_ref_data[$machine_id] != "")
						{
							$date_wise_breakdown_str = trim($date_wise_machine_ref_data[$machine_id]);
							$date_wise_breakdown_arr =  explode("*", $date_wise_breakdown_str);

							for ($j = 0; $j < count($date_wise_breakdown_arr); $j++)
							{
								$machine_date_data = explode(",", $date_wise_breakdown_arr[$j]);

								$curr_date = $machine_date_data[0];
								$dateWise_qnty = $machine_date_data[1];
								$dateWise_qnty =number_format($dateWise_qnty,2,".","");
								$days_complete = $machine_date_data[2];
								$days_complete =number_format($days_complete,2,".","");

								if($days_complete >= 1)
								{
									$fraction = 0;
								}else{
									$fraction = 1;
								}

								$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
								if($previous_occupied_days)
								{
									if($days_complete != (1-$previous_occupied_days))
									{
										echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
										disconnect($con);
										die;
									}
								}

								if ($db_type == 0) $curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-"); else $curr_date = change_date_format($curr_date, '', '', 1);

								if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
								$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "')";
								$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
							}
						}
						else
						{

							//===========shuru

							$distribute_qnty = $qnty;
							$capacity_qnty = $capacity;
							$no_of_days = $noOfDays;
							if ($db_type == 0) {
								$start_date = change_date_format(trim($startDate), "yyyy-mm-dd", "");
							} else {
								$start_date = change_date_format(trim($startDate), '', '', 1);
							}

							$pre_sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date >= '$start_date' group by distribution_date order by distribution_date";

							$data_array = sql_select($pre_sql);
							if (count($data_array) > 0)

							{
								foreach ($data_array as $row)
								{
									$distribution_date = date('d-m-Y',strtotime($row[csf('distribution_date')]));
									$previous_occupied_arr_without_ref[$distribution_date]["days_complete"] = $row[csf('days_complete')];
									$previous_occupied_arr_without_ref[$distribution_date]["distribution_date"] = $distribution_date;
								}
							}

							$present_day = $start_date;
							$without_ref_dateWise_qnty = ""; $end_date_ref = "";
							while($distribute_qnty > 0)
							{
								$present_day = date("d-m-Y",strtotime($present_day));

								if($previous_occupied_arr_without_ref[$present_day]["distribution_date"])
								{
									if($previous_occupied_arr_without_ref[$present_day]["distribution_date"] == $present_day)
									{
										if ($previous_occupied_arr_without_ref[$present_day]["days_complete"] >= 1)
										{
											$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
										}
										else
										{
											if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
											{
												$dist_qnty = $capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]);
												$without_ref_dateWise_qnty .= $present_day.",".$dist_qnty.",".($dist_qnty/$capacity_qnty)."*";
											}
											else
											{
												$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
											}

											$distribute_qnty = $distribute_qnty - ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"]));
											$end_date_ref =  $present_day;
											$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
										}
									}
								}
								else
								{
									if($distribute_qnty > ($capacity_qnty * (1 - $previous_occupied_arr_without_ref[$present_day]["days_complete"])))
									{
										$without_ref_dateWise_qnty .= $present_day.",".$capacity_qnty.",".($capacity_qnty/$capacity_qnty)."*";
									}
									else
									{
										$without_ref_dateWise_qnty .= $present_day.",".$distribute_qnty.",".($distribute_qnty/$capacity_qnty)."*";
									}

									$end_date_ref =  $present_day;
									$present_day = date('d-m-Y',strtotime($present_day . "+1 days"));
									$distribute_qnty = $distribute_qnty - $capacity_qnty;

								}


							}

							$without_ref_dateWise_qnty = chop($without_ref_dateWise_qnty,"*");

							$date_wise_breakdown_str =trim($without_ref_dateWise_qnty);

							$without_ref_arr =  array_filter(explode("*", $without_ref_dateWise_qnty));

							foreach ($without_ref_arr as  $ref_value)
							{
								$ref_value = explode(",", $ref_value);

								$curr_date =  $ref_value[0];
								$dateWise_qnty =  $ref_value[1];
								$days_complete =  $ref_value[2];
								$days_complete =number_format($days_complete,2,".","");
								if($days_complete >= 1)
								{
									$fraction =0;
								}
								else{
									$fraction =1;
								}

								$previous_occupied_days = $previous_occupied_arr[$machine_id][date("d-m-Y",strtotime($curr_date))]["days_complete"];
								if($previous_occupied_days)
								{
									if($days_complete != (1-$previous_occupied_days))
									{
										echo "14** Date : ".$curr_date .", Machine No: ".$machine_arr[$machine_id] ." already occupied other program $days_complete != (1-$previous_occupied_days) -- $machine_id == $curr_date";
										disconnect($con);
										die;
									}
								}


								if ($db_type == 0) $curr_date = change_date_format($curr_date, "yyyy-mm-dd", "-"); else $curr_date = change_date_format($curr_date, '', '', 1);


								if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
								$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "')";
								$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
							}

							//sesh----------------------------------------------=========================-------------------------
						}
					}

					if ($db_type == 0) {
						$mstartDate = $startDate;
						$mendDate = $endDate;
					} else {
						$mstartDate = change_date_format($startDate, '', '', 1);
						$mendDate = change_date_format($endDate, '', '', 1);
					}

					if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
					$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "','" . $date_wise_breakdown_str . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$machine_dtls_id = $machine_dtls_id + 1;
				}
			}
			//echo "10**".$data_array_machine_dtls_datewise;die;
			$feeder_dtls_id = return_next_id("id", "ppl_planning_feeder_dtls", 1);
			$field_array_feeder_dtls = "id, mst_id, dtls_id, pre_cost_id, color_id, stripe_color_id, no_of_feeder, inserted_by, insert_date";

			$hidden_no_of_feeder_data = str_replace("'", "", $hidden_no_of_feeder_data);
			if ($hidden_no_of_feeder_data != "") {
				$hidden_no_of_feeder_data = explode(",", $hidden_no_of_feeder_data);
				for ($i = 0; $i < count($hidden_no_of_feeder_data); $i++) {
					$color_wise_data = explode("_", $hidden_no_of_feeder_data[$i]);
					$pre_cost_id = $color_wise_data[0];
					$color_id = $color_wise_data[1];
					$stripe_color_id = $color_wise_data[2];
					$no_of_feeder = $color_wise_data[3];

					if ($data_array_feeder_dtls != "") $data_array_feeder_dtls .= ",";

					$data_array_feeder_dtls .= "(" . $feeder_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $pre_cost_id . "','" . $color_id . "','" . $stripe_color_id . "','" . $no_of_feeder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$feeder_dtls_id = $feeder_dtls_id + 1;
				}

			}

			$hidden_collarCuff_data = str_replace("'", "", $hidden_collarCuff_data);
			if ($hidden_collarCuff_data != "")
			{
				$collar_cuff_dtls_id = return_next_id("id", "ppl_planning_collar_cuff_dtls", 1);
				$field_array_collar_cuff_dtls = "id, mst_id, dtls_id, body_part_id, grey_size, finish_size, qty_pcs, inserted_by, insert_date";

				$hidden_collarCuff_data = explode(",", $hidden_collarCuff_data);
				for ($i = 0; $i < count($hidden_collarCuff_data); $i++) {
					$collarCuff_wise_data = explode("_", $hidden_collarCuff_data[$i]);
					$body_part_id = $collarCuff_wise_data[0];
					$grey_size = $collarCuff_wise_data[1];
					$finish_size = $collarCuff_wise_data[2];
					$qty_pcs = $collarCuff_wise_data[3];

					if ($data_array_collar_cuff_dtls != "") $data_array_collar_cuff_dtls .= ",";

					$data_array_collar_cuff_dtls .= "(" . $collar_cuff_dtls_id . "," . $updateId . "," . $update_dtls_id . ",'" . $body_part_id . "','" . $grey_size . "','" . $finish_size . "','" . $qty_pcs . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$collar_cuff_dtls_id = $collar_cuff_dtls_id + 1;
				}
			}

			$hidden_count_feeding_data = str_replace("'", "", $hidden_count_feeding_data);
			if($hidden_count_feeding_data  != ""){
				$count_feeding_id = return_next_id("id", "ppl_planning_count_feed_dtls", 1);
				$field_array_count_feeding_dtls = "id, mst_id, dtls_id, seq_no, count_id,feeding_id, inserted_by, insert_date";

				$hidden_count_feeding_data_arr = explode(",", $hidden_count_feeding_data);
				for ($i = 0; $i < count($hidden_count_feeding_data_arr); $i++) {
					$count_feeding_data_arr = explode("_", $hidden_count_feeding_data_arr[$i]);
					$seq_no = $count_feeding_data_arr[0];
					$count_id = $count_feeding_data_arr[1];
					$feeding_id = $count_feeding_data_arr[2];
					if ($data_array_count_feeding_dtls != "") $data_array_count_feeding_dtls .= ",";
					$data_array_count_feeding_dtls .= "(" . $count_feeding_id . "," . $updateId . "," . $update_dtls_id . "," . $seq_no . "," . $count_id . "," . $feeding_id. "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$count_feeding_id = $count_feeding_id + 1;
				}
			}

			// == Color prgo data  ==
			$hidden_color_wise_prog_data = str_replace("'", "", $hidden_color_wise_prog_data);
			$hidden_size_wise_prog_string = str_replace("'", "", $hidden_size_wise_prog_string);
			if ($hidden_color_wise_prog_data != "")
			{
				$color_wise_break_down_id = return_next_id("id", "ppl_color_wise_break_down", 1);
				$field_array_color_wise_break_down = "id, plan_id, program_no, color_id, color_prog_qty,size_wise_prog_string, inserted_by, insert_date";
				$field_array_color_wise_prog_update = "color_id*color_prog_qty*updated_by*update_date*status_active*is_deleted";

				$color_wise_prog_data = array();
				$color_wise_prog_data = explode(",", $hidden_color_wise_prog_data);
				$size_wise_prog_data = explode("**", $hidden_size_wise_prog_string);
				$color_wise_mstID = array();
				for ($i = 0; $i < count($color_wise_prog_data); $i++)
				{
					if($color_wise_prog_data[$i] != '')
					{
						$colorWiseProgData = array();
						$color_id = '';
						$color_prog_qty = 0;
						$color_wise_break_down_udpdateId = 0;

						$sizeWiseDataString=$size_wise_prog_data[$i];

						$colorWiseProgData = explode("_", $color_wise_prog_data[$i]);
						$color_id = $colorWiseProgData[0];
						$color_prog_qty = $colorWiseProgData[1];
						$color_wise_break_down_udpdateId = $colorWiseProgData[2];

						if ($color_wise_break_down_udpdateId>0)
						{
							$status_active = 1;
							$is_deleted = 0;
							if($color_prog_qty <= 0)
							{
								$status_active = 0;
								$is_deleted = 1;
							}

							$colorprog_upd_id_arr[] = $color_wise_break_down_udpdateId;
							$data_array_color_wise_prog_update[$color_wise_break_down_udpdateId] = explode("*", ("'" . $color_id . "'*'". $color_prog_qty . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'". $status_active . "'*'". $is_deleted . "'"));

							$color_wise_mstID[$color_id]=$color_wise_break_down_udpdateId;
						}
						else
						{
							if ($data_array_color_wise_break_down != "")
								$data_array_color_wise_break_down .= ",";
							if($color_prog_qty>0)
							{
								$data_array_color_wise_break_down .= "(" . $color_wise_break_down_id . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $color_prog_qty . "','" . $sizeWiseDataString . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$color_wise_mstID[$color_id]=$color_wise_break_down_id;
								$color_wise_break_down_id = $color_wise_break_down_id + 1;
							}
						}
					}
				}
			}
			/*echo "10**";
			echo "<pre>";
			print_r($data_array_color_wise_break_down); die();*/

			// == Size prog data ==

			if( $hidden_color_wise_prog_data != "" && $hidden_size_wise_prog_string != "")
			{
				$size_wise_break_down_id = return_next_id("id", "ppl_size_wise_break_down ", 1);
				$field_array_size_wise_break_down = "id,color_wise_mst_id, plan_id, program_no, color_id,grey_size_id,finish_size_id,per_kg, kg_wise_total_qnty,body_part_id,current_qty, inserted_by, insert_date";
				$size_wise_prog_datas = explode("**", $hidden_size_wise_prog_string);
				for ($inc = 0; $inc < count($size_wise_prog_datas); $inc++)
				{
					$size_wise_prog_dataz = explode("##", $size_wise_prog_datas[$inc]);
					for ($i = 0; $i < count($size_wise_prog_dataz); $i++)
					{
						if($size_wise_prog_dataz[$i] != '')
						{

							$sizeWiseProgData = array();
							$color_id = '';
							$gmts_size_id = '';
							$finish_size_id = '';
							$per_kg = '';
							$size_wise_current_qty = '';
							$size_kg_wise_prog_qty = 0;

							$sizeWiseProgData = explode("_", $size_wise_prog_dataz[$i]);
							$color_id = $sizeWiseProgData[0];
							$gmts_size_id = $sizeWiseProgData[1];
							$finish_size_id = $sizeWiseProgData[2];
							$per_kg = $sizeWiseProgData[3];
							$size_kg_wise_prog_qty = $sizeWiseProgData[4];
							$size_wise_body_part_id = $sizeWiseProgData[6];
							$size_wise_current_qty = $sizeWiseProgData[7];
							$size_wise_primary_update_id = $sizeWiseProgData[8];


							if ($data_array_size_wise_break_down != "")
								$data_array_size_wise_break_down .= ",";


							if ($size_wise_primaryUpdateId != "")
								$size_wise_primaryUpdateId .= ",";

							if($size_kg_wise_prog_qty>0)
							{
								$colorWiseMstId=$color_wise_mstID[$color_id];
								$data_array_size_wise_break_down .= "(" . $size_wise_break_down_id . "," . $colorWiseMstId . "," . $updateId . "," . $update_dtls_id . ",'" . $color_id . "','" . $gmts_size_id . "','" . $finish_size_id . "','" . $per_kg . "','" . $size_kg_wise_prog_qty . "','" . $size_wise_body_part_id . "','" . $size_wise_current_qty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$size_wise_break_down_id = $size_wise_break_down_id + 1;

								$size_wise_primaryUpdateId.=$size_wise_primary_update_id;

							}


						}
					}

				}
			}
			//echo "10**$size_wise_primaryUpdateId"; die;
			//die;
			//Query Execution Start
			$flag = 1;
			if ($data != "")
			{
				if ($data_array_plan_dtls != "")
				{
					$delete = execute_query("delete from ppl_planning_entry_plan_dtls where dtls_id=$update_dtls_id", 0);
					if ($delete)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 1);
			if ($flag == 1)
			{
				if ($rID) $flag = 1; else $flag = 0;
			}
			if ($flag == 1)
			{
				$posted_flag_updated = execute_query("update ppl_planning_info_entry_dtls set is_posted_sql=2 where id = $update_dtls_id",0);
				if ($posted_flag_updated)
					$flag = 1;
				else
					$flag = 0;
			}

			//count feeding........................
			if($hidden_count_feeding_data != "")
			{
				$delete_feeding = execute_query("delete from ppl_planning_count_feed_dtls where dtls_id=$update_dtls_id", 0);
				if($delete_feeding) $flag = 1; else $flag = 0;
				if($flag == 1 && $data_array_count_feeding_dtls)
				{
					$rID2 = sql_insert("ppl_planning_count_feed_dtls", $field_array_count_feeding_dtls, $data_array_count_feeding_dtls, 0);
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
			//....................................................

			if ($data != "")
			{
				if ($data_array_plan_dtls != "")
				{
					$rID2 = sql_insert("ppl_planning_entry_plan_dtls", $field_array_plan_dtls, $data_array_plan_dtls, 0);
					if ($flag == 1) {
						if ($rID2) $flag = 1; else $flag = 0;
					}
				}
			}

			$deletem = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);
			if ($flag == 1)
			{
				if ($deletem) $flag = 1; else $flag = 0;
			}

			$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);
			if ($flag == 1)
			{
				if ($delete_datewise) $flag = 1; else $flag = 0;
			}

			if ($save_data != "")
			{
				if ($data_array_machine_dtls != "")
				{
					$rID3 = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);

					if ($flag == 1)
					{
						if ($rID3) $flag = 1; else $flag = 0;
					}
				}

				if ($data_array_machine_dtls_datewise != "")
				{
					$rID4 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
					if ($flag == 1)
					{
						if ($rID4) $flag = 1; else $flag = 0;
					}
				}
			}

			$delete_feeder = execute_query("delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id", 0);
			if ($flag == 1)
			{
				if ($delete_feeder) $flag = 1; else $flag = 0;
			}

			if ($hidden_no_of_feeder_data != "")
			{
				if ($data_array_feeder_dtls != "")
				{
					$rID5 = sql_insert("ppl_planning_feeder_dtls", $field_array_feeder_dtls, $data_array_feeder_dtls, 0);
					if ($flag == 1)
					{
						if ($rID5) $flag = 1; else $flag = 0;
					}
				}
			}

			$delete_collar_cuff = execute_query("delete from ppl_planning_collar_cuff_dtls where dtls_id=$update_dtls_id", 0);
			if ($flag == 1)
			{
				if ($delete_collar_cuff) $flag = 1; else $flag = 0;
			}

			if ($data_array_collar_cuff_dtls != "")
			{
				$rID6 = sql_insert("ppl_planning_collar_cuff_dtls", $field_array_collar_cuff_dtls, $data_array_collar_cuff_dtls, 0);
				if ($flag == 1)
				{
					if ($rID6) $flag = 1; else $flag = 0;
				}
			}

			if ($hidden_color_wise_prog_data != "")
			{
				if (count($colorprog_upd_id_arr)>0)
				{ // update

					if (count($data_array_color_wise_prog_update) > 0)
					{
						//echo "10**".bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr);
						$rID7 = execute_query(bulk_update_sql_statement("ppl_color_wise_break_down", "id", $field_array_color_wise_prog_update, $data_array_color_wise_prog_update, $colorprog_upd_id_arr));

						if ($flag == 1)
						{
							if ($rID7) $flag = 1; else $flag = 0;
						}
					}
				}

				if ($data_array_color_wise_break_down != "")
				{ // new color insert
					//echo "10**insert into ppl_color_wise_break_down (".$field_array_color_wise_break_down.") Values ".$data_array_color_wise_break_down."";die;
					$rID8 = sql_insert("ppl_color_wise_break_down", $field_array_color_wise_break_down, $data_array_color_wise_break_down, 0);
					if ($flag == 1)
					{
						if ($rID8) $flag = 1; else $flag = 0;
					}
				}
			}
			//=======Size wise ==============
			if ($data_array_size_wise_break_down != "") {

				$size_wise_primaryUpdateIdArr= explode(",",$size_wise_primaryUpdateId);
				foreach ($color_wise_mstID as $colorIDS => $colorMstID) {
					/*foreach ($size_wise_primaryUpdateIdArr as $sizeWiseMstId)
					{*/
						//echo "10**delete from ppl_size_wise_break_down where color_wise_mst_id=$colorMstID and id=$sizeWiseMstId"; die;
						$delete_size_tbl = execute_query("delete from ppl_size_wise_break_down where color_wise_mst_id=$colorMstID", 0);
					//}

				}
				if ($delete_size_tbl) $flag = 1; else $flag = 0;
				//echo $flag;
				//echo "10**insert into ppl_size_wise_break_down (".$field_array_size_wise_break_down.") Values ".$data_array_size_wise_break_down."";die;
				$rID9 = sql_insert("ppl_size_wise_break_down", $field_array_size_wise_break_down, $data_array_size_wise_break_down, 0);
				if ($flag == 1) {
					if ($rID9) $flag = 1; else $flag = 0;
				}
			}
			//oci_rollback($con);
			//echo "10**$rID##$rID1##$rID2##$rID3##$rID4##$rID5##$rID6##$deletem##$delete_feeder##$delete_collar_cuff##$flag";
			//disconnect($con);
			//die;
			if ($db_type == 0)
			{
				if ($flag == 1)
				{
					mysql_query("COMMIT");
					echo "1**" . str_replace("'", "", $updateId) . "**0**".implode(',', $programIdArr);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "6**0**1**".implode(',', $programIdArr);
				}
			}
			else if ($db_type == 2 || $db_type == 1)
			{
				if ($flag == 1)
				{
					oci_commit($con);
					echo "1**" . str_replace("'", "", $updateId) . "**0**".implode(',', $programIdArr);
				}
				else
				{
					oci_rollback($con);
					echo "6**0**1**".implode(',', $programIdArr);
				}
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2)
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}

		$check_issue = return_field_value("c.issue_number", "ppl_yarn_requisition_entry a,inv_transaction b,inv_issue_master c", "a.requisition_no=b.requisition_no and b.mst_id=c.id and b.item_category=1 and c.item_category=1 and  a.status_active=1 and b.status_active=1 and a.knit_id=$update_dtls_id", "issue_number");

		if ($check_issue != "")
		{
			echo "13**Yarn Issue Found.Program can not be deleted.\nIssue ID: ".$check_issue;
			disconnect($con);
			exit();
		}

		$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($knit_qty > 0)
		{
			echo "13**Production Found. Delete Not Allowed.";
			disconnect($con);
			exit();
		}

		//checking requisition
		$check_requisition = return_field_value("a.requisition_no", "ppl_yarn_requisition_entry a", "a.status_active=1 and a.is_deleted=0 and a.knit_id=".$update_dtls_id."", "requisition_no");
		if ($check_requisition != "")
		{
			echo "13**Yarn Requisition Found. Program can not be delete.\Requisition No: ".$check_requisition;
			disconnect($con);
			exit();
		}

		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		if ($rID) $flag = 1; else $flag = 0;

		$rID2 = sql_update("ppl_planning_entry_plan_dtls", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 0);
		if ($flag == 1)
		{
			if ($rID2) $flag = 1; else $flag = 0;
		}

		$rID3 = sql_update("ppl_color_wise_break_down", $field_array_update, $data_array_update, "program_no", $update_dtls_id, 0);
		if ($flag == 1)
		{
			if ($rID3) $flag = 1; else $flag = 0;
		}

		$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1)
		{
			if ($delete) $flag = 1; else $flag = 0;
		}
		//count Feeding......................................................
		$delete = execute_query("delete from ppl_planning_count_feed_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1)
		{
			if ($delete) $flag = 1; else $flag = 0;
		}
		//count Feeding end......................................................

		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);
		if ($flag == 1)
		{
			if ($delete_datewise) $flag = 1; else $flag = 0;
		}

		$delete_feeder = execute_query("delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id", 1);
		if ($flag == 1)
		{
			if ($delete_feeder) $flag = 1; else $flag = 0;
		}

		$delete_collar_cuff = execute_query("delete from ppl_planning_collar_cuff_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1)
		{
			if ($delete_collar_cuff) $flag = 1; else $flag = 0;
		}

		if ($db_type == 0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0**".implode(',', $programIdArr);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "7**0**1**".implode(',', $programIdArr);
			}
		}

		if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0**".implode(',', $programIdArr);
			}
			else
			{
				oci_rollback($con);
				echo "7**0**1**".implode(',', $programIdArr);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "delete_program")
{
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	//$field_array_update="status_active*is_deleted*updated_by*update_date";
	//$data_array_update="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	//$rID=sql_update("ppl_planning_info_entry_dtls",$field_array_update,$data_array_update,"id",$program_ids,0);
	$rID = execute_query("update ppl_planning_info_entry_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in($program_ids)", 0);
	if ($rID) $flag = 1; else $flag = 0;

	//$rID2=sql_update("ppl_planning_entry_plan_dtls",$field_array_update,$data_array_update,"dtls_id",$program_ids,0);
	$rID2 = execute_query("update ppl_planning_entry_plan_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where dtls_id in($program_ids)", 0);
	if ($flag == 1) {
		if ($rID2) $flag = 1; else $flag = 0;
	}

	$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id in($program_ids)", 0);
	if ($flag == 1) {
		if ($delete) $flag = 1; else $flag = 0;
	}

	$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id in($program_ids)", 0);
	if ($flag == 1) {
		if ($delete_datewise) $flag = 1; else $flag = 0;
	}

	$delete_feeder = execute_query("delete from ppl_planning_feeder_dtls where dtls_id in($program_ids)", 1);
	if ($flag == 1) {
		if ($delete_feeder) $flag = 1; else $flag = 0;
	}

	if ($db_type == 0) {
		if ($flag == 1) {
			mysql_query("COMMIT");
			echo "2**0";
		} else {
			mysql_query("ROLLBACK");
			echo "7**0**1";
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($flag == 1) {
			oci_commit($con);
			echo "2**0";
		} else {
			oci_rollback($con);
			echo "7**0**1";
		}
	}
	disconnect($con);
	die;
}

if ($action == "update_program")
{
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$program_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

	if ($knit_qty > $prog_qty) {
		echo "20";
		disconnect($con);
		exit();
	}

	$field_array_update = "program_qnty*updated_by*update_date";
	$data_array_update = $prog_qty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
	$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $program_id, 0);

	if ($db_type == 0) {
		if ($rID) {
			mysql_query("COMMIT");
			echo "1";
		} else {
			mysql_query("ROLLBACK");
			echo "6";
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($rID) {
			oci_commit($con);
			echo "1";
		} else {
			oci_rollback($con);
			echo "6";
		}
	}
	disconnect($con);
	die;
}
if ($action == "update_revise_qnty")
{
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$knit_qty = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$txt_programID and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

	if ($knit_qty > $updateQnty) {
		echo "20**$knit_qty";
		disconnect($con);
		exit();
	}
	//echo "10**".$txt_programID;
	//die; oci_rollback($con);disconnect($con);

	$field_array_update = "program_qnty*updated_by*update_date";
	$data_array_update = $updateQnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
	$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $txt_programID, 0);

	if ($rID) $flag = 1; else $flag = 0;


	$field_array_update2 = "program_qnty*updated_by*update_date";
	$data_array_update2 = $updateQnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
	$rID2 = sql_update("ppl_planning_entry_plan_dtls", $field_array_update2, $data_array_update2, "dtls_id", $txt_programID, 0);
	if ($flag == 1) {
		if ($rID2) $flag = 1; else $flag = 0;
	}


	$field_array_update3 = "color_prog_qty*updated_by*update_date";
	$data_array_update3 = $updateQnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
	$rID3 = sql_update("ppl_color_wise_break_down", $field_array_update3, $data_array_update3, "id", $txt_color_wise_id, 0);
	if ($flag == 1) {
		if ($rID3) $flag = 1; else $flag = 0;
	}
	//echo "10**".$rID ."=". $rID2."=". $rID3 ."=". $flag;
	//echo  "10**".$updateQnty ."=". $txt_color_wise_id."=". $txt_programID ."=". $txt_plandID ."=". $txt_colorID ;


	if ($db_type == 0) {
		if ($flag == 1) {
			mysql_query("COMMIT");
			echo "1";
		} else {
			mysql_query("ROLLBACK");
			echo "6";
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($flag == 1) {
			oci_commit($con);
			echo "1";
		} else {
			oci_rollback($con);
			echo "6";
		}
	}
	disconnect($con);
	die;
}

?>