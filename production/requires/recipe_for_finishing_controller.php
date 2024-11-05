<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_location") 
{
	$data = explode("_", $data);
	echo create_drop_down("cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

if ($action == "load_drop_down_buyer") 
{
	echo create_drop_down("cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,2,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 1);
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_receive_controller",$data);
}

if ($action == "systemid_popup") 
{
	echo load_html_head_contents("Labdip No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id) {
			$('#hidden_update_id').val(id);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchlabdipfrm" id="searchlabdipfrm">
				<fieldset style="width:1085px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
						<thead>
							<tr>
								<th colspan="6"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
							</tr>
							<tr>
								<th>Recipe Date Range</th>
								<th>System ID</th>
								<th width="130">Labdip No</th>
								<th width="100">Batch No</th>
								<th width="150">Recipe Description</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;"
									class="formbutton"/>
									<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
									<input type="hidden" name="working_company_id" id="working_company_id" class="text_boxes" value="<? echo $cbo_working_company_id; ?>">
									<input type="hidden" name="hidden_update_id" id="hidden_update_id" class="text_boxes" value="">
									<input type="hidden" name="hidden_pickup" id="hidden_pickup" class="text_boxes" value="">
									<input type="hidden" name="hidden_surplus_solution" id="hidden_surplus_solution" class="text_boxes" value="">
								</th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:60px;">To<input type="text" name="txt_date_to" id="txt_date_to"
								class="datepicker" style="width:60px;">
							</td>
							<td>
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_sysId"
								id="txt_search_sysId" placeholder="Search"/>
							</td>
							<td>
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_labdip"
								id="txt_search_labdip" placeholder="Search"/>
							</td>
							<td>
								<input type="text" style="width:100px;" class="text_boxes" name="txt_search_batch" id="txt_search_batch" placeholder="Search"/>
							</td>
							<td>
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_recDes"
								id="txt_search_recDes" placeholder="Search"/>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_labdip').value+'_'+document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('working_company_id').value, 'create_recipe_search_list_view', 'search_div', 'recipe_for_finishing_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:80px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" height="40"
							valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "create_recipe_search_list_view") 
{
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$batch_no_arr = return_library_array("select id, batch_no from pro_batch_create_mst where  status_active=1 and is_deleted=0", 'id', 'batch_no');
	$data = explode("_", $data);
	$labdip = $data[0];
	$sysid = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$rec_des = trim($data[5]);
	$search_type = $data[6];
	$batch_no = $data[7];
	$working_company_id = $data[8];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		} else if ($db_type == 2) {
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($search_type == 1) {
		if ($labdip != '') $labdip_cond = " and labdip_no='$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id=$sysid"; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'"; else $rec_des_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'"; else $rec_des_cond = "";
	} else if ($search_type == 2) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'"; else $rec_des_cond = "";
	} else if ($search_type == 3) {
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'"; else $rec_des_cond = "";
	}
	if ($batch_no != "") {
		//$batch_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$batch_no' and status_active=1 and is_deleted=0", "id");
		//$batch_cond = "and batch_id=$batch_ids";

		$sql_batch=sql_select(" select id from pro_batch_create_mst where (working_company_id=$working_company_id OR company_id=$company_id) and batch_no='$batch_no' and status_active=1 and is_deleted=0");
		foreach ($sql_batch as  $value) {
			$batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
		}

		if(!empty($batch_ids_arr))
		{
			$batch_cond = "and batch_id in(".implode(",", $batch_ids_arr).")";

		}
		
	} else {
		$batch_cond = "";
	}
	$lc_working_company_cond="";
	if($working_company_id!=0 && $company_id!=0)
	{
		$lc_working_company_cond="and (working_company_id=$working_company_id OR company_id=$company_id)";

		if($company_id!=0) $po_company_cond="and a.company_name=$company_id";else $po_company_cond="";

	}
	else
	{
		//echo $working_company_id."SS";
		if($working_company_id!=0) $lc_working_company_cond="and working_company_id=$working_company_id";
		if($company_id!=0) $lc_working_company_cond.="and company_id=$company_id";
		if($company_id!=0) $po_company_cond="and a.company_name=$company_id";else $po_company_cond="";
	}

	$sql = "select id, labdip_no,batch_id, recipe_description, recipe_date, order_source, style_or_order, buyer_id, color_id, color_range, pickup, surplus_solution from pro_recipe_entry_mst where entry_form=468 and status_active=1 and is_deleted=0 $lc_working_company_cond $labdip_cond $sysid_cond $rec_des_cond $date_cond $batch_cond order by id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$all_batch_id.=$row[csf("batch_id")].",";
	}
	$all_batch_id=chop($all_batch_id,",");
	if($all_batch_id!="")
	{
		$baIds=chop($all_batch_id,','); $ba_cond_in="";
			$ba_ids=count(array_unique(explode(",",$all_batch_id)));
			if($db_type==2 && $ba_ids>1000)
			{
			$ba_cond_in=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
			$ids=implode(",",$ids);
			$ba_cond_in.=" c.mst_id in($ids) or"; 
			}
			$ba_cond_in=chop($ba_cond_in,'or ');
			$ba_cond_in.=")";
			}
			else
			{
			$ba_cond_in=" and c.mst_id in($baIds)";
			}
		$po_arr = sql_select("select b.id,c.mst_id as batch_id,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and b.status_active=1 and b.is_deleted=0  $ba_cond_in $po_company_cond ");
		$po_ref_arr = array();
		$po_file_arr = array();
		foreach ($po_arr as $row) {

			$po_file_arr[$row[csf('batch_id')]] = $row[csf('file_no')];
			$po_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];
		}
	}
	//echo $sql;

	$arr = array(2 => $batch_no_arr, 3 => $po_file_arr, 4 => $po_ref_arr, 7 => $knitting_source, 9 => $buyer_arr, 10 => $color_arr, 11 => $color_range);

	echo create_list_view("tbl_list_search", "ID,Labdip No,Batch No,File No,Ref. No,Recipe Description,Recipe Date,Order Source,Booking,Buyer,Color,Color Range,Pick Up,Surplus Solution", "50,80,80,70,80,130,70,80,110,100,70,90,90,90", "1250", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,batch_id,batch_id,batch_id,0,0,order_source,0,buyer_id,color_id,color_range,0,0", $arr, "id,labdip_no,batch_id,batch_id,batch_id,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range,pickup,surplus_solution", "", "", '0,0,0,0,0,0,3,0,0,0,0,0,0,0', '');

	exit();
}

if ($action == 'populate_data_from_search_popup') 
{
	$data_array = sql_select("select id, labdip_no, order_source,company_id,working_company_id, location_id, recipe_description, batch_id,batch_qty, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, copy_from,batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, pickup, surplus_solution,recipe_serial_no from pro_recipe_entry_mst where id='$data'");
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_sys_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "document.getElementById('update_id_check').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_labdip_no').value 				= '" . $row[csf("labdip_no")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_working_company_id').value 		= '" . $row[csf("working_company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		echo "$('#cbo_working_company_id').attr('disabled','true')" . ";\n";

		
		echo "load_drop_down('requires/recipe_for_finishing_controller', '" . $row[csf("working_company_id")] . "', 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value 				= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_recipe_date').value 				= '" . change_date_format($row[csf("recipe_date")]) . "';\n";
		echo "document.getElementById('cbo_order_source').value 			= '" . $row[csf("order_source")] . "';\n";

		echo "document.getElementById('txt_recipe_des').value 				= '" . $row[csf("recipe_description")] . "';\n";
		echo "document.getElementById('txt_batch_id').value 				= '" . $row[csf("batch_id")] . "';\n";
		echo "document.getElementById('cbo_method').value 					= '" . $row[csf("method")] . "';\n";

		echo "document.getElementById('txt_liquor').value 					= '" . $row[csf("total_liquor")] . "';\n";
		echo "document.getElementById('txt_batch_ratio').value 				= '" . $row[csf("batch_ratio")] . "';\n";
		echo "document.getElementById('txt_liquor_ratio').value 			= '" . $row[csf("liquor_ratio")] . "';\n";
		echo "document.getElementById('txt_pick_up').value 			= '" . $row[csf("pickup")] . "';\n";
		echo "document.getElementById('surpls_solution').value 			= '" . $row[csf("surplus_solution")] . "';\n";
		echo "document.getElementById('txt_recipe_serial_no').value 			= '" . $row[csf("recipe_serial_no")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_copy_from').value 				= '" . $row[csf("copy_from")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";
		$order_source=$row[csf("order_source")];
		echo "get_php_form_data(" . $row[csf("working_company_id")] . "+'**'+" . $row[csf("batch_id")] . "+'**'+" . $order_source . ", 'load_data_from_batch', 'requires/recipe_for_finishing_controller');\n";
		if ($row[csf("batch_qty")] != 0) {
			echo "document.getElementById('txt_batch_weight').value 				= '" . $row[csf("batch_qty")] . "';\n";
		}

		//echo "select id,sub_process_id,process_remark from pro_recipe_entry_dtls where mst_id='".$row[csf("id")]."' ";
		$sql_rec_dtls = sql_select("select id,sub_process_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $row[csf("id")] . " ");

		$liquor_ratio = $sql_rec_dtls[0][csf('liquor_ratio')];
		$total_liquor = $sql_rec_dtls[0][csf('total_liquor')];

		
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_recipe_entry',1);\n";
		echo "$('#btn_recipe_calc').removeAttr('disabled','disabled');\n";
		echo "$('#btn_recipe_calc').removeClass('formbutton_disabled').addClass('formbutton');\n";

		exit();
	}
}

if ($action == "booking_popup") 
{
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_id, booking_no, color, color_id, job_no, type) {
			$('#hidden_booking_id').val(booking_id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_color').val(color);
			$('#hidden_color_id').val(color_id);
			$('#hidden_job_no').val(job_no);
			$('#booking_without_order').val(type);
			parent.emailwindow.hide();
		}

	</script>

	</head>

	<body>
		<div align="center" style="width:775px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:100%;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="200">Enter Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
								value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="txt_buyer_id" id="txt_buyer_id" class="text_boxes"
								value="<? echo $cbo_buyer_name; ?>">
								<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes"
								value="">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
								value="">
								<input type="hidden" name="hidden_color" id="hidden_color" class="text_boxes" value="">
								<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
								<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes"
								value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", $data[0]);
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*2', '../../') ";
								echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>', 'create_booking_search_list_view', 'search_div', 'recipe_for_finishing_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
					</table>
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
}

if ($action == "create_booking_search_list_view") 
{
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$batch_against = $data[4];

	if ($db_type == 0) {
		$groupby_field = "group by a.id, b.fabric_color_id";
		$groupby_u_field = "group by a.id, b.fabric_color_id";
		$groupby_d_field = "group by s.id, f.fabric_color";
	} else if ($db_type == 2) {
		$groupby_field = "group by a.id, b.fabric_color_id,a.booking_no, a.booking_date, a.buyer_id,c.job_no, c.style_ref_no ";
		$groupby_u_field = "group by a.id, b.fabric_color_id,a.booking_no, a.booking_date, a.buyer_id,c.job_no, c.style_ref_no ";
		$groupby_d_field = "group by s.id, f.fabric_color,s.booking_no, s.booking_date, s.buyer_id";
	}

	if ($buyer_id == 0) {
		echo "Please Select Buyer First.";
		die;
	}

	$po_number_array = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');;
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.booking_no like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and d.po_number like '$search_string'";
		else if ($search_by == 3)
			$search_field_cond = "and c.job_no like '$search_string'";
		else
			$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
	} else {
		$search_field_cond = "";
	}

	if ($batch_against == 1) {
		if ($db_type == 0) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no,group_concat(distinct(d.id)) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0  $search_field_cond $groupby_field";
		} else if ($db_type == 2) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no, listagg(d.id,',') within group (order by d.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0  $search_field_cond $groupby_field";
		}
	} else {
		if ($search_by == 1)
			$search_field_cond_sample = "and s.booking_no like '$search_string'";
		else if ($search_by == 4)
			$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-", 1) . "'";
		else
			$search_field_cond_sample = "";
		if ($db_type == 0) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no, group_concat(distinct(d.id)) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $search_field_cond $groupby_u_field
			union all
			SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, NULL as job_no, NULL as style_ref_no, NULL as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.buyer_id=$buyer_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0  $search_field_cond_sample $groupby_d_field
			";
		} else if ($db_type == 2) {
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no,listagg(d.id,',') within group (order by d.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $search_field_cond $groupby_u_field
			union all
			SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, NULL as job_no, NULL as style_ref_no, NULL as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.buyer_id=$buyer_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0  $search_field_cond_sample $groupby_d_field
			";
		}
	}

	//echo $sql;
	$result = sql_select($sql);
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="115">Booking No</th>
			<th width="75">Booking Date</th>
			<th width="100">Buyer</th>
			<th width="85">Job No</th>
			<th width="100">Style Ref.</th>
			<th width="70">Color</th>
			<? if ($batch_against == 3) { ?>
				<th width="60">Without Order</th><? } ?>
				<th>Buyer Order</th>
			</thead>
		</table>
		<div style="width:770px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				

				$po_no = "";
				$po_id = array_unique(explode(",", $row[csf('po_id')]));

				foreach ($po_id as $val) {
					if ($po_no == '') $po_no = $po_number_array[$val]; else $po_no .= "," . $po_number_array[$val];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $color_arr[$row[csf('fabric_color_id')]]; ?>','<? echo $row[csf('fabric_color_id')]; ?>','<? echo $po_no;//$row[csf('job_no')];
                    ?>','<? echo $row[csf('type')]; ?>');">
                    <td width="30"><? echo $i; ?></td>
                    <td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                    <td width="85" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="70"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                    <? if ($batch_against == 3) { ?>
                    	<td width="60"
                    	align="center"><? if ($row[csf('type')] == 0) echo "No"; else echo "Yes"; ?></td><? } ?>
                    	<td><p><? echo $po_no; ?></p></td>
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


if ($action == "recipe_item_details") 
{ 
	$process_array = array();
	$process_array_remark = array();
	$sql = "select id, sub_process_id as sub_process_id,process_remark, store_id from pro_recipe_entry_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id";
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) {
		if (!in_array($row[csf("sub_process_id")], $process_array)) {
			$process_array[] = $row[csf("sub_process_id")];
			$process_array_remark[$row[csf("sub_process_id")]] = $row[csf("process_remark")]."**".$row[csf("store_id")];
		}
	}
	foreach ($process_array as $sub_provcess_id) {
		$process_ref = explode("**",$process_array_remark[$sub_provcess_id]);
		$process_remark=$process_ref[0];
		$store_id=$process_ref[1];
		?>
		<h3 align="left" id="accordion_h<? echo $sub_provcess_id; ?>" style="width:910px" class="accordion_h"
			onClick="fnc_item_details(<? echo $sub_provcess_id; ?>,'<? echo $process_remark; ?>','<? echo $store_id; ?>')"><span
			id="accordion_h<? echo $sub_provcess_id; ?>span">+</span><? echo $dyeing_sub_process[$sub_provcess_id]; ?>
		</h3>
		<?
	}
}

if ($action == "batch_popup") 
{
	echo load_html_head_contents("Batch Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(batch_data) 
		{
      		//  alert (batch_data);
      		document.getElementById('hidden_batch_id').value = batch_data;
			//document.getElementById('hidden_batch_type').value = batch_type;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:600px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="500" class="rpt_table">
						<thead>
							<tr>
								<th colspan="3">
									<?
									echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
									?>
								</th>
							</tr>
							<tr>
								<th>Batch Type</th>
								<th>Batch</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
									class="formbutton"/>
									<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">

								</th>
							</tr>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								echo create_drop_down("cbo_search_by", 150, $order_source, "", 1, "--Select--", 0, 0, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_string_search_type').value, 'create_batch_search_list_view', 'search_div', 'recipe_for_finishing_controller', 'setFilterGrid(\'list_view\',-1);')"
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

if ($action == "create_batch_search_list_view") 
{
	$data = explode('_', $data);
	$search_common = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$search_type = $data[3];
	if ($search_common == "") {
		//echo "<p style='color:firebrick; text-align: center; font-weight: bold;'>Batch No is required</p>";
		//exit;

	}

	if ($search_type == 1) {
		if ($search_common != '') $batch_cond = " and a.batch_no='$search_common'"; else $batch_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common%'"; else $batch_cond = "";
	} else if ($search_type == 2) {
		if ($search_common != '') $batch_cond = " and a.batch_no like '$search_common%'"; else $batch_cond = "";
	} else if ($search_type == 3) {
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common'"; else $batch_cond = "";
	}

	if ($search_by == 1) {
		$batch_type_cond = " and a.entry_form in(0,74)";
	} else if ($search_by == 2) {
		$batch_type_cond = " and a.entry_form=36";
	} else  if ($search_by == 3) {
		$batch_type_cond = " and a.entry_form in (0,36,74)";
	}
	else  if ($search_by == 4) {
		$batch_type_cond = " and a.entry_form in (136)";
	}

	if($search_by==2)
	{
		$company_batch_cond="and a.company_id=$company_id";
	}
	else
	{
		$company_batch_cond="and a.working_company_id=$company_id";
	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$company_name_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	if($db_type==0) $select_recipe="group_concat(b.id) as recipe_id";
	else if($db_type==2) $select_recipe="listagg(b.id,',') within group (order by b.id) as recipe_id";
	$batch_result = sql_select("select a.id as batch_id,a.double_dyeing,$select_recipe ,count(b.id) total_receipe from pro_batch_create_mst a,pro_recipe_entry_mst b where a.id=b.batch_id and b.entry_form=5 and a.status_active=1 and a.is_deleted=0 $batch_type_cond $batch_cond $company_batch_cond group by a.id,a.double_dyeing");

	$batch_check_arr = array();
	foreach ($batch_result as $row) {
		/*if($row[csf("double_dyeing")]==1 && $row[csf("total_receipe")] >= 2){
			$batch_check_arr[$row[csf("batch_id")]]= $row[csf("recipe_id")];
		}else */
		if($row[csf("double_dyeing")]!=1){
			$batch_check_arr[$row[csf("batch_id")]]= $row[csf("recipe_id")];
		}
	}

	if ($search_by!= 4)
	{
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, group_concat(b.po_id) as po_id, b.is_sales from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond group by a.id, a.batch_no, a.extention_no, b.is_sales order by a.id DESC";
		} else if ($db_type == 2) {
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, b.is_sales from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond $company_batch_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.is_sales order by a.id DESC";
		}
	}
	else
	{
		$sql = "select a.id, a.batch_no,a.job_no, a.extention_no, a.batch_weight, a.batch_date, a.color_id, a.entry_form ,sum(b.trims_wgt_qnty) as trims_wgt_qnty from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=136 $batch_type_cond $batch_cond $company_batch_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.job_no, a.batch_date, a.color_id, a.entry_form order by a.id DESC";
	}
	//echo $sql;
	$nameArray = sql_select($sql);
	$po_id_arr=$subcon_po_id_arr=$sales_order_arr=array();
	foreach ($nameArray as $selectResult) {
		if($selectResult[csf("is_sales")]==1){
			$sales_order_arr[$selectResult[csf("po_id")]]=$selectResult[csf("po_id")];
		}else{
			if ($selectResult[csf("entry_form")] == 36) {
				$subcon_po_id_arr[$selectResult[csf("po_id")]]=$selectResult[csf("po_id")];
			}else{
				$po_id_arr[$selectResult[csf("po_id")]]=$selectResult[csf("po_id")];
			}
		}
	}

	$sales_arr = $po_arr = $sub_po_arr = array();
	if(!empty($sales_order_arr)){
		$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in(".implode(",",$sales_order_arr).")");
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["sales_order_no"] = $sales_row[csf("job_no")];
		}
	}

	if(!empty($po_id_arr)){
		$po_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$po_id_arr).")", 'id', 'po_number');
	}
	if(!empty($subcon_po_id_arr)){
		$sub_po_arr = return_library_array("select id,order_no from  subcon_ord_dtls where id in(".implode(",",$subcon_po_id_arr).")", 'id', 'order_no');
	}

	if ($search_by!=4)
	{
		$caption_head="PO/FSO No.";
	}
	else
	{
		$caption_head="Job No.";
	}?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table">
			<caption> <strong><? echo $company_name_arr[$company_id];?></strong></caption>
			<thead>
				<th width="30">SL</th>
				<th width="70">Batch No</th>
				<th width="40">Ex.</th>
				<th width="90">Color</th>
				<th width="80">Batch Weight</th>
				<th width="80">Total Trims Weight</th>
				<th width="70">Batch Date</th>
				<th><? echo $caption_head;?></th>
			</thead>
		</table>
		<div style="width:618px; overflow-y:scroll; max-height:240px;" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view">
				<?
				$i = 1;
				foreach ($nameArray as $selectResult) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$order_no = '';
					$order_id = array_unique(explode(",", $selectResult[csf("po_id")]));
					$is_sales = $selectResult[csf("is_sales")];
					foreach ($order_id as $val) {
						if ($selectResult[csf("entry_form")] == 36) {
							if($is_sales == 1){
								$order_no = $sales_arr[$val]["sales_order_no"];
							}else{
								if ($order_no == "") $order_no = $sub_po_arr[$val]; else $order_no .= ", " . $sub_po_arr[$val];
							}
						} else {
							if($is_sales == 1){
								$order_no = $sales_arr[$val]["sales_order_no"];
							}else{
								if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
							}
						}
					}
					if ($search_by!=4) $order_nos=$order_no;else $order_nos=$selectResult[csf("job_no")];
					if ($search_by!=4) $batch_weight_qty=$selectResult[csf("total_trims_weight")];else $batch_weight_qty=$selectResult[csf("trims_wgt_qnty")];

					if($search_by>0)
					{
						$search_by=$search_by;
					}
					else
					{
						if($selectResult[csf('entry_form')]==0 || $selectResult[csf('entry_form')]==74)
						{
							$search_by=1;
						}
						else if($selectResult[csf('entry_form')]==36)
						{
							$search_by=2;
						}
						else if($selectResult[csf('entry_form')]==136)
						{
							$search_by=3;
						}
					}

					$batch_data=$selectResult[csf('id')].'_'.$search_by;
					if($batch_check_arr[$selectResult[csf("id")]]=="")
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
							id="search<? echo $i; ?>" onClick="js_set_value('<? echo $batch_data; ?>')">
							<td width="30"><? echo $i; ?></td>
							<td width="70"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
							<td width="40"><? echo $selectResult[csf('extention_no')]; ?></td>
							<td width="90"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo $selectResult[csf('batch_weight')]; ?></p></td>
							<td width="80" align="right"><p><? echo $batch_weight_qty; ?></p>
							</td>
							<td width="70" align="center">
								<p><? echo change_date_format($selectResult[csf('batch_date')]); ?></p></td>
								<td><p><? echo $order_nos; ?></p></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
				</table>
			</div>
		</div>
		<?
		exit();
}

if ($action == "ratio_data_from_dtls") 
{
	$ex_data = explode('**', $data);
	$company = $ex_data[0];
	$sub_process = $ex_data[1];
	$update_id = $ex_data[2];
	if ($sub_process == 93 || $sub_process == 94 || $sub_process == 95 || $sub_process == 96 || $sub_process == 97 || $sub_process == 98) {
		$sql_rec_dtls = "select id,sub_process_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $update_id . " and sub_process_id=$sub_process ";
	} else {
		$sql_rec_dtls = "select id,sub_process_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $update_id . " and sub_process_id=$sub_process and ratio>0";
	}
	//echo $sql_rec_dtls;
	$result_dtl = sql_select($sql_rec_dtls);
	foreach ($result_dtl as $row) {
	//txt_total_liquor_ratio*txt_liquor_ratio_dtls
		echo "document.getElementById('txt_liquor_ratio_dtls').value 		= '" . $row[csf("liquor_ratio")] . "';\n";
		echo "document.getElementById('txt_total_liquor_ratio').value 			= '" . $row[csf("total_liquor")] . "';\n";
		echo "caculate_tot_liquor();\n";
	}
	if($update_id!='')
	{
	$sub_seq_no =return_field_value("max(a.sub_seq) as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . " and a.ratio>0 and a.status_active=1 and a.is_deleted=0", "sub_seq");
	}
	$sub_seq=$sub_seq_no+1;
	echo "document.getElementById('txt_subprocess_seq').value 			= '" . $sub_seq . "';\n";
	
	$sub_seq_no =return_field_value("a.sub_seq as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . " and a.ratio>0 and a.sub_process_id=$sub_process and a.status_active=1 and a.is_deleted=0", "sub_seq");
	if($sub_seq_no!="")
	{
	echo "document.getElementById('txt_subprocess_seq').value 			= '" . $sub_seq_no . "';\n";
	}
		

	exit();
}
if ($action == "recipe_previ_copy_check")
{
	$ex_data = explode('**', $data);
	$company =$ex_data[0];
	if($db_type==0)
	{
	 $from_date="2019-03-07";
	}
	else
	{
	 $from_date="07-Mar-2019";
	}
	//$recipe_date =$ex_data[1];

	$sql_recipe="select recipe_date from pro_recipe_entry_mst where  entry_form=468 and  recipe_date>='$from_date' and status_active=1";
		//echo $sql_recipe;die;
		$data_array=sql_select($sql_recipe);
		if(count($data_array)>0)
		{
			echo date("Y-m-d",strtotime($data_array[0][csf('recipe_date')]));
			//echo date("Y-m-d",strtotime($data_array[0][csf('process_end_date')]));
		}
		else
		{
			echo "";
		}
		exit();	
		
}

if ($action == "load_data_from_batch") 
{
	$ex_data = explode('**', $data);
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$sub_po_arr = return_library_array("select id,order_no from  subcon_ord_dtls", 'id', 'order_no');
	$buyer_arr = return_library_array("select booking_no,buyer_id from wo_booking_mst", 'booking_no', 'buyer_id');
	$sample_buyer_arr = return_library_array("select booking_no,buyer_id from wo_non_ord_samp_booking_mst", 'booking_no', 'buyer_id');
	$sub_buyer_arr = return_library_array("select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst", 'id', 'party_id');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
	}
	$batch_cat=$ex_data[2];
   //and a.working_company_id='$ex_data[0]'
	if($batch_cat!=4)
	{
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no,a.company_id, a.extention_no,a.batch_against, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no,a.booking_without_order, a.booking_no_id, a.entry_form, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.company_id, a.extention_no,b.is_sales,a.re_dyeing_from,a.booking_without_order order by a.id DESC";
		} else if ($db_type == 2) {
			$sql = "select a.id, a.batch_no,a.company_id,a.batch_against, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no,a.booking_without_order, a.booking_no_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.company_id,a.batch_against, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no,a.booking_without_order, a.booking_no_id, a.entry_form,b.is_sales,a.re_dyeing_from order by a.id DESC";
		}
	}
	else
	{
		$sql = "select a.id, a.batch_no,a.company_id,a.job_no,a.batch_against, a.extention_no, a.batch_weight, sum(b.trims_wgt_qnty) as total_trims_weight, a.batch_date, a.color_id, a.color_range_id,a.booking_without_order,a.entry_form,a.re_dyeing_from from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id  and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.company_id,a.job_no,a.batch_against, a.extention_no, a.batch_weight,a.booking_without_order, a.batch_date, a.color_id, a.color_range_id, a.entry_form,a.re_dyeing_from order by a.id DESC";
	}
   //echo $sql;
	if ($db_type == 2) {
		$group_po="listagg((cast(b.po_number as varchar2(4000))),',') within group (order by b.po_number) as po_number ";
	}
	else
	{
		$group_po="group_concat(b.po_number) as po_number ";
	}
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = "";
		$buyer_id = "";
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		$is_sales = $row[csf("is_sales")];
		foreach ($order_id as $val) {
			if ($row[csf("entry_form")] == 36) {
				if($is_sales == 1){
					$order_no = $sales_arr[$val]["sales_order_no"];
				}else{
					if ($order_no == "") $order_no = $sub_po_arr[$val]; else $order_no .= ", " . $sub_po_arr[$val];
				}
				if ($buyer_id == "") $buyer_id = $sub_buyer_arr[$val]; else $buyer_id .= "," . $sub_buyer_arr[$val];
			} else {
				if($is_sales == 1){
					$order_no = $sales_arr[$val]["sales_order_no"];
				}else{
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
			$booking_no=$row[csf("booking_no")];$booking_without_order=$row[csf("booking_without_order")];
		}
		$po_id = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$prod_id = implode(",", array_unique(explode(",", $row[csf("prod_id")])));
		$batch_id = implode(",", array_unique(explode(",", $row[csf("id")])));

		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
		//$ord_source = 2;
			$buyer_name = implode(',', array_unique(explode(",", $buyer_id)));
		}
		else {
			$batch_type = "<b> SELF ORDER </b>";
		//$ord_source = 1;
			if ($row[csf("entry_form")] == 74) {
				$result = sql_select("select c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0");
				$buyer_name = $result[0][csf('buyer_name')];
			}
			else if ($row[csf("entry_form")] == 136) {
				$result = sql_select("select c.buyer_name,$group_po from pro_batch_create_mst a, wo_po_break_down b, wo_po_details_master c where  a.job_no=c.job_no  and b.job_no_mst=c.job_no and a.id='$ex_data[1]' and a.entry_form=136 and a.status_active=1 and a.is_deleted=0 group by c.buyer_name");
				$buyer_name = $result[0][csf('buyer_name')];
				$order_no = $result[0][csf('po_number')];
			}
			else {
				if ($row[csf("batch_against")] == 3) {
					if ($booking_without_order == 1)
					{
						$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
					}
					else
					{
						$buyer_name = $buyer_arr[$row[csf("booking_no")]];
					}
				} else {
					if($row[csf("re_dyeing_from")] > 0){
						$batch_against= return_field_value("batch_against","pro_batch_create_mst","id=".$row[csf("re_dyeing_from")]);
					}
					if($batch_against==3 && $booking_without_order==1 ){
						$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
					}else{
						$buyer_name = $buyer_arr[$row[csf("booking_no")]];
					}
				}
			}
		}
		$order_no = implode(",", array_unique(explode(",", $order_no)));
		$tot_batch_wgtTrim=$row[csf("batch_weight")];//+$row[csf("total_trims_weight")]; comments by kausar
		if($row[csf("color_range_id")]==0) 
		{
			echo "$('#cbo_color_range').removeAttr('disabled',true);\n";
		}
		else 
		{
			echo "$('#cbo_color_range').attr('disabled',true);\n";
		}
		
		echo "document.getElementById('cbo_order_source').value 		= '" . $batch_cat . "';\n";
		echo "document.getElementById('txt_batch_no').value 			= '" . $row[csf("batch_no")] . "';\n";
		echo "document.getElementById('txt_batch_weight').value 		= '" . $tot_batch_wgtTrim . "';\n";
		echo "document.getElementById('txt_hidden_batch_weight').value 		= '" . $tot_batch_wgtTrim . "';\n";
		echo "document.getElementById('txt_booking_order').value 		= '" . $row[csf("booking_no")] . "';\n";
		echo "document.getElementById('txt_booking_id').value 			= '" . $row[csf("booking_no_id")] . "';\n";

		echo "document.getElementById('cbo_company_id').value 	= '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
	
		echo "load_drop_down( 'requires/recipe_for_finishing_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_buyer', 'buyer_td_id' );\n";

		echo "document.getElementById('cbo_buyer_name').value 			= '" . $buyer_name . "';\n";
		echo "document.getElementById('txt_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('txt_color_id').value 			= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 			= '" . $row[csf("color_range_id")] . "';\n";
		echo "document.getElementById('txt_trims_weight').value 		= '" . $row[csf("total_trims_weight")] . "';\n";
		echo "document.getElementById('txt_order').value 				= '" . $order_no . "';\n";
		echo "document.getElementById('batch_type').innerHTML 			= '" . $batch_type . "';\n";
		if($batch_cat!=4)
		{
			echo "get_php_form_data('" . $po_id . "'+'**'+'" . $prod_id . "'+'**'+'" . $booking_no . "'+'**'+'" . $booking_without_order . "'+'**'+'" . $ex_data[1] . "'+'**'+'" . $row[csf("company_id")] . "', 'lode_data_from_grey_production', 'requires/recipe_for_finishing_controller');\n";
		}
		echo "caculate_tot_liquor();\n";
	}
	exit();
}

if ($action == "lode_data_from_grey_production") 
{
	$ex_data = explode('**', $data);
	$po_id = str_replace("'", "", $ex_data[0]);
	$prod_id = str_replace("'", "", $ex_data[1]);
	$booking_no = str_replace("'", "", $ex_data[2]);
	$booking_without_order = str_replace("'", "", $ex_data[3]);
	$batch_id = str_replace("'", "", $ex_data[4]);
	$company_id = str_replace("'", "", $ex_data[5]);
	

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name=$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	
	
	if ($roll_maintained==1) 
	{
		if ($db_type==0) 
		{
			$sql_prod="SELECT group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id 
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d 
			where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$company_id and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and 
			a.is_deleted=0 and c.entry_form in(2,22)";
		}
		else
		{
			$sql_prod="SELECT LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, 
			LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, 
			LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id 
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d 
			where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$company_id and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and 
			a.is_deleted=0 and c.entry_form in(2,22)";
		}		
	}
	else
	{
		$sql_prod = "SELECT  d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
		and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  d.yarn_lot,d.yarn_count,d.brand_id";
	}

	$result_sql_prod = sql_select($sql_prod);
	$yarn_lot="";$all_brand_name="";$all_count_name="";
	foreach ($result_sql_prod as $row) 
	{
		if($row[csf('yarn_lot')]!='' || $row[csf('brand_id')]!='' || $row[csf('yarn_count')]!='')
		{
			$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
			$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
			$brand_name = "";
			foreach ($brand_id as $val) {
				if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= "," . $brand_arr[$val];
			}

			$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
			$count_name = "";
			foreach ($yarn_count as $val) {
				if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= "," . $count_arr[$val];
			}
			if($yarn_lot=="") $yarn_lot=$row[csf('yarn_lot')];else $yarn_lot.=",".$row[csf('yarn_lot')];
			if($all_brand_name=="") $all_brand_name=$brand_name;else $all_brand_name.=",".$brand_name;
			if($all_count_name=="") $all_count_name=$count_name;else $all_count_name.=",".$count_name;
		}
	}
	echo "document.getElementById('txt_yarn_lot').value 			= '" . implode(",", array_unique(explode(",", $yarn_lot))) . "';\n";
	echo "document.getElementById('txt_brand').value 				= '" . implode(",", array_unique(explode(",", $all_brand_name))) . "';\n";
	echo "document.getElementById('txt_count').value 				= '" . implode(",", array_unique(explode(",", $all_count_name))) . "';\n";
	exit();
}



if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$update_id=$data[2];
	$store_id=$data[4];
	$variable_lot=$data[5];
	$from_lib_check_id=$data[6];
	$sql_lot_variable = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$lot_variable=$sql[0][csf("auto_transfer_rcv")];
	
	$lot_variable=$sql[0][csf("auto_transfer_rcv")];
	$sql_chk = sql_select("select is_control, id from variable_settings_production where company_name = $company_id and variable_list = 54 and is_deleted = 0 and status_active = 1");
	$stock_check=$sql_chk[0][csf("is_control")];
	if($stock_check==0 || $stock_check==2) $stock_check=0;else $stock_check=$stock_check;
	//echo "select is_control, id from variable_settings_production where company_name = $company_id and variable_list = 54 and is_deleted = 0 and status_active = 1";
	//echo $stoch_check.'G';
	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');

	$recipe_data_arr=array(); $recipe_prod_id_arr=array(); $product_data_arr=array();
	//echo $update_id.test;die;
	if($update_id!="")
	{	//sum(b.req_qny_edit) as qnty
		$iss_arr=return_library_array("select b.product_id, sum(b.req_qny_edit) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id",'product_id','qnty');
		$sql_req_res=sql_select("select c.product_id, c.store_id, c.item_lot, sum(c.recipe_qnty) as qnty from dyes_chem_issue_requ_mst a,dyes_chem_requ_recipe_att b, dyes_chem_issue_requ_dtls c where  a.id=c.mst_id and c.mst_id=b.mst_id  and a.id=b.mst_id  and a.entry_form=156 and a.requisition_basis=8 and b.recipe_id=$update_id and c.sub_process=$sub_process_id group by c.product_id, c.store_id, c.item_lot");

		foreach($sql_req_res as $row)
		{
			if($variable_lot==1) $dyes_lot=$row[csf('item_lot')]; else $dyes_lot="";
			$prod_key=$row[csf('product_id')]."_".$row[csf('store_id')]."_".$dyes_lot;
			$iss_req_arr[$prod_key]=$row[csf('qnty')];
		}

		if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98)
		{
			$ration_cond="";
		}
		else
		{
			$ration_cond=" and ratio>0 ";
		}
		$recipeData=sql_select("select prod_id, id, item_lot, comments, dose_base, ratio, seq_no, store_id, item_lot from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
		foreach($recipeData as $row)
		{
			$recepi_prod_id[$row[csf('prod_id')]]=$row[csf('prod_id')];
			//$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')]."_".$row[csf('item_lot')];
			$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
			if(trim($row[csf('item_lot')])!="" && $variable_lot==1) $prod_key .="_".$row[csf('item_lot')];
			
			$recipe_data_arr[$prod_key][1]=$row[csf('item_lot')];
			$recipe_data_arr[$prod_key][2]=$row[csf('dose_base')];
			$recipe_data_arr[$prod_key][3]=$row[csf('ratio')];
			$recipe_data_arr[$prod_key][4]=$row[csf('seq_no')];
			$recipe_data_arr[$prod_key][5]=$row[csf('id')];
			$recipe_data_arr[$prod_key][6]=$row[csf('comments')];
			$recipe_prod_id_arr[]=$prod_key;
			$stock_check_data_arr[$prod_key]=$row[csf('prod_id')];
		}
	}
	//echo "<pre>";print_r($recipe_data_arr);
	if($stock_check==1) $stock_check=" and a.current_stock>0 and b.cons_qty>0";else $stock_check="";
	$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name,a.subprocess_id, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot, b.store_id 
	from product_details_master a, inv_store_wise_qty_dtls b 
	where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in(5,7) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $stock_check";
	$sql.=" order by a.id,b.lot";
	// echo $sql;
	//echo $from_lib_check_id.'DX';
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$prod_key=$row[csf('id')]."_".$row[csf('store_id')];
		$subprocess_Arr=array_unique(explode(",",$row[csf('subprocess_id')]));
		if(trim($row[csf('lot')]) && $variable_lot==1) $prod_key .="_".$row[csf('lot')];
		//echo $sub_process_id.'='.$row[csf('subprocess_id')].'<br>';
		if($from_lib_check_id==1 && in_array($sub_process_id,$subprocess_Arr)) //CheckBox Checked //Lib-> Item Account Creation -Sub Process:: Issue Id=8876
		{
		$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')]."**".$row[csf('lot')];
		}
		if($from_lib_check_id==2) //When uncheck the box then data come as usual business
		{
			$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')]."**".$row[csf('lot')];
		}
		
	}
	//echo "<pre>";print_r($product_data_arr);

	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" >
			<thead>
             <tr>
                   <th colspan="14"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                </tr>
                <tr>
				<th width="30">SL</th>
				<th width="80">Item Category</th>
				<th width="100">Item Group</th>
				<th width="70">Sub Group</th>
				<th width="130">Item Description</th>
				<th width="80">Item Lot</th>
				<th width="40">UOM</th>
				<th width="70" class="must_entry_caption">Dose Base</th>
				<th width="55" class="must_entry_caption">Ratio</th>
				<th width="40" class="must_entry_caption">Seq. No</th>
				<th width="100">Sub Process</th>
				<th width="50">Prod. ID</th>
				<th width="70">Stock Qty</th>
				<th width="">Comments</th>
                </tr>
			</thead>
		</table>
		<div style="width:1050px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1032" class="rpt_table" id="tbl_list_search">
				<tbody>
				<?
				//echo $variable_lot.'xxxxddd';
				if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98) //Wash start...
				{
					$i=1; //$max_seq_no='';
					//if(count($recipe_prod_id_arr)>0)
					//{
					//echo $sub_process_id.'dsdsdsdd';
					if($variable_lot==1)
					{
						$lot_popup=''; 
						$place_holder='';
					}
					else 
					{
						$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
						$place_holder='Browse';
					}
					foreach($recipe_prod_id_arr as $prodId)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$prodData=explode("**",$product_data_arr[$prodId]);
						$item_category_id=$prodData[0];
						$item_group_id=$prodData[1];
						$sub_group_name=$prodData[2];
						$item_description=$prodData[3];
						$item_size=$prodData[4];
						$unit_of_measure=$prodData[5];
						$current_stock=$prodData[6];
						$store_stock=$prodData[7];
						$lot_no=$prodData[6];

						$item_lot=$recipe_data_arr[$prodId][1];
						$dtls_id=$recipe_data_arr[$prodId][5];
						//echo $dtls_id.'saa';
						$ratio=$recipe_data_arr[$prodId][3];
						$seq_no=$recipe_data_arr[$prodId][4];
						$comments=$recipe_data_arr[$prodId][6];
						$bgcolor="yellow";

						$selected_dose=$recipe_data_arr[$prodId][2];
						$disbled="";
						$iss_qty=$iss_arr[$prodId];
						$iss_req_qty=$iss_req_arr[$prodId];
						if($iss_req_qty=='' || $iss_req_qty==0) $iss_req_qty=0;else $iss_req_qty=$iss_req_qty;
						/*if(($update_id!="" && $ratio>0) && ($iss_qty>0 || $iss_req_qty>0) )
						{
							$disbled="disabled='disabled'";
						}*/

						if($iss_qty>0)  //Issue-Id=4748 as per cto/Saeed
						{
							$disbled="disabled='disabled'";
						}
						$prodId_ref=explode("_",$prodId);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
							<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
							<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
							<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
							<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
							<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
							<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $item_lot; ?>" readonly>
							</td>
							<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
							<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
							<td width="50" align="center" id="ratio_<? echo $i; ?>" title="<? echo 'Issue Qty Found='.$iss_qty;?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
							<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
							<td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
							<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId_ref[0]; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId_ref[0]; ?>"></td>
							<td align="right" width="70" id="stock_qty_<? echo $i; ?>" title="<? echo "current stock=".$prodData[6]."store stock=".$store_stock  ?>"><? echo number_format($store_stock,2,'.',''); ?></td>
							<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
						</tr>
						<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
						$i++;
					}
					//}

				}
				else 				//Wash End....
				{
					$i=1; //$max_seq_no='';
					
					if(count($recipe_prod_id_arr)>0)
					{
						//echo $sub_process_id.'dsdsdsdd';

						foreach($recipe_prod_id_arr as $prodId)
						{
							if($variable_lot==1)
							{
								$lot_popup=''; 
								$place_holder='';
							}
							else 
							{
								$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
								$place_holder='Browse';
							}
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//echo $prodId."<br>";
							$prodData=explode("**",$product_data_arr[$prodId]);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];
							$store_stock=$prodData[7];
							$lot_no=$prodData[8];

							$item_lot=$recipe_data_arr[$prodId][1];
							$dtls_id=$recipe_data_arr[$prodId][5];
							$ratio=$recipe_data_arr[$prodId][3];
							$seq_no=$recipe_data_arr[$prodId][4];
							$comments=$recipe_data_arr[$prodId][6];
							$bgcolor="yellow";

							$selected_dose=$recipe_data_arr[$prodId][2];
							$disbled="";
							$iss_qty=$iss_arr[$prodId];
							if($iss_qty=='' || $iss_qty==0) $iss_qty=0;else $iss_qty=$iss_qty;
							$iss_req_qty=$iss_req_arr[$prodId];
							if($iss_req_qty=='' || $iss_req_qty==0) $iss_req_qty=0;else $iss_req_qty=$iss_req_qty;
							//echo $update_id.'='.$ratio.'='.$iss_qty.'='.$iss_req_qty.'<br/>';
							/*if(($update_id!="" && $ratio>0) && ($iss_qty>0 || $iss_req_qty>0) )
							{

								$disbled="disabled='disabled'";
							}*/
							//echo $iss_qty.'<br>';
							if($iss_qty>0)  //Issue-Id=4748 as per cto/Saeed
							{

								$disbled="disabled='disabled'";
							}

							$current_stock_check=number_format($store_stock,7,'.','');
							$prodId_ref=explode("_",$prodId);
							//if($current_stock_check>0)
							//{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $item_lot; ?>" readonly>
									</td>
									<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
									<td width="50" align="center" title="<? echo 'Issue Qty Found='.$iss_qty;?>" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
									<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
									<td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
									<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId_ref[0]; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId_ref[0]; ?>"></td>
									<td align="right" width="70" title="<? echo 'Stock Qty Allowed 12 Digit after decimal='.$store_stock; ?>" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,2,'.',''); ?></td>
									<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
								</tr>
								<?
							    //$max_seq_no[]=$selectResult[csf('seq_no')];
								$i++;
							//}
						}
					}

					foreach($product_data_arr as $prodId=>$data)
					{
						if(!in_array($prodId,$recipe_prod_id_arr))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if($variable_lot==1)
							{
								$lot_popup=''; 
								$place_holder='';
							}
							else 
							{
								$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
								$place_holder='Browse';
							}
							
							$prodData=explode("**",$data);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];
							$store_stock=$prodData[7];
							$lot_no=$prodData[8];

							
							$ratio=''; $seq_no=''; $disbled="";$comments='';
							if($item_category_id==6)
							{
								$selected_dose=2;
							}
							else
							{
								$selected_dose=1;
							}

							$td_color="";
							if($store_stock<=0) $td_color="#FF0000"; else $td_color="";
							$current_stock_check=number_format($store_stock,2,'.','');
							$prodId_ref=explode("_",$prodId);
							if($current_stock_check>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="100" bgcolor="<? echo $td_color;?>" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
									<td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?> placeholder="<? echo $place_holder; ?>" value="<? echo $lot_no; ?>" readonly>
									</td>
									<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
									<td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
									<td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
									<td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? //echo $dtls_id; ?>"></td>
									<td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId_ref[0]; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId_ref[0]; ?>"></td>
									<td align="right" width="70" title="<? echo "current stock=".$prodData[6]."store stock=".$store_stock  ?>" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,2,'.',''); ?></td>
									<td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
								</tr>
								<?
							
								$i++;
							}

						}
					}
				}
				?>
			</tbody>
		</table>
	</div>
	</div>
	<?
	exit();
}


if($action=="populate_data_lib_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo $sql[0][csf("auto_transfer_rcv")];
	exit();
}
if($action=="populate_stock_data")
{
	$sql_chk = sql_select("select is_control, id from variable_settings_production where company_name = $data and variable_list = 54 and is_deleted = 0 and status_active = 1");
	
	if($sql_chk[0][csf("is_control")]==0 || $sql_chk[0][csf("is_control")]==2) $chk_stock=2;else  $chk_stock=$sql_chk[0][csf("is_control")];
	echo "document.getElementById('variable_stock').value 			= '" . $chk_stock . "';\n";
	exit();
}
if($action=="populate_recipe_data")
{
	$sql_chk = sql_select("select production_entry, id from variable_settings_production where company_name = $data and variable_list = 59 and is_deleted = 0 and status_active = 1");

	if($sql_chk[0][csf("production_entry")]==0 || $sql_chk[0][csf("production_entry")]==2) $chk_recipe=2;else  $chk_recipe=$sql_chk[0][csf("production_entry")];
	echo "document.getElementById('variable_recipe').value 			= '" . $chk_recipe . "';\n";
	exit();
}

if ($action == "itemLot_popup")
{
	echo load_html_head_contents("Item Lot Info", "../../", 1, 1, '', 1, '');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array, selected_name = new Array();
	selected_attach_id = new Array();
	
	function toggle(x, origColor) {
	var newColor = 'yellow';
	if (x.style) {
	x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
	}
	}
	
	function js_set_value(id) {
	var str = id.split("_");
	toggle(document.getElementById('tr_' + str[0]), '#FFFFFF');
	var strdt = str[2];
	str = str[1];
	
	if (jQuery.inArray(str, selected_id) == -1) {
	selected_id.push(str);
	selected_name.push(strdt);
	}
	else {
	for (var i = 0; i < selected_id.length; i++) {
	if (selected_id[i] == str) break;
	}
	selected_id.splice(i, 1);
	selected_name.splice(i, 1);
	}
	var id = '';
	var ddd = '';
	for (var i = 0; i < selected_id.length; i++) {
	id += selected_id[i] + ',';
	ddd += selected_name[i] + ',';
	}
	id = id.substr(0, id.length - 1);
	ddd = ddd.substr(0, ddd.length - 1);
	$('#item_lot').val(id);
	//$('#prod_id').val( ddd );
	}
	
	/*function js_set_value(str)
	{
	var splitData = str.split("_");
	//alert (splitData[1]);
	$("#hide_job_id").val(splitData[0]);
	$("#hide_job_no").val(splitData[1]);
	parent.emailwindow.hide();
	}*/
	</script>
	<input type="hidden" id="prod_id"/><input type="hidden" id="item_lot"/>
	<?
	if ($db_type == 0) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '  order by batch_lot desc";
	} elseif ($db_type == 2) {
	$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '  order by batch_lot desc";
	}
	//echo $sql;
	
	echo create_list_view("list_view", "Item Lot", "200", "330", "250", 0, $sql, "js_set_value", "batch_lot", "", 1, "", 0, "batch_lot", "recipe_for_finishing_controller", 'setFilterGrid("list_view",-1);', '0', '', 1);
	die;
}

if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	//echo '10**';die;

	
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$recipe_update_id = '';
		//$color_id = return_id($txt_color, $color_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","468");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$subprocess = str_replace("'", "", $cbo_sub_process);

		$batch_id = str_replace("'", "", $txt_batch_id);
		$txt_recipe_serial_no = str_replace("'", "", $txt_recipe_serial_no);
		
		
		$sql_chk = sql_select("select is_control, id from variable_settings_production where company_name = $cbo_working_company_id and variable_list = 54 and is_deleted = 0 and status_active = 1");
	$stock_check=$sql_chk[0][csf("is_control")];
	if($stock_check==0 || $stock_check==2) $stock_check=0;else $stock_check=$stock_check;
		
		if (str_replace("'", "", $update_id) == "" && str_replace("'", "", $copy_id) == 2) 
		{
			$recipe_serial_no =return_field_value("max(a.recipe_serial_no) as recipe_serial_no","pro_recipe_entry_mst a"," a.batch_id=" . $batch_id . " and a.entry_form=468 and a.status_active=1 and a.is_deleted=0", "recipe_serial_no");
			$recipe_serial_no=$recipe_serial_no+1;
		}
		else
		{
			$recipe_serial_no=$txt_recipe_serial_no;
		}
		
		if (str_replace("'", "", $copy_id) == 1 || str_replace("'", "", $copy_id) == 2) {

			$batch_process_ids = return_field_value("process_id","pro_batch_create_mst","id = $batch_id");
			if($batch_process_ids!= "")
			{
				$batch_process_id_arr = explode(",", $batch_process_ids);
				if(!in_array("137", $batch_process_id_arr))
				{

					$double_dyeing = return_field_value("double_dyeing", "pro_batch_create_mst", "id=" . $batch_id . " and status_active=1 and is_deleted=0 and status_active=1 and is_deleted=0", "double_dyeing");

					$batch_id_count =return_field_value("count(a.batch_id) as batch_id_count","pro_recipe_entry_mst a"," a.batch_id=" . $batch_id . " and a.entry_form=468 and a.status_active=1 and a.is_deleted=0", "batch_id_count");

					/*$recipe_no = return_field_value("a.id as id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and b.sub_process_id=$subprocess and a.batch_id=" . $batch_id . " and a.entry_form=468 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");*/
					$recipe_nos=sql_select("select a.id as id from pro_recipe_entry_mst a,pro_recipe_entry_dtls b where a.id=b.mst_id and b.sub_process_id=$subprocess and a.batch_id=" . $batch_id . " and a.entry_form=468 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.id");
					$recipe_no="";
					foreach ($recipe_nos as $row) {
						$recipe_no.=$row[csf('id')].",";
					}
					$recipe_no =chop($recipe_no,",");
					if ($recipe_no != '') {
						/*if ($double_dyeing==1 && $batch_id_count==2) {
							echo "14**0**$recipe_no"; die;
						}
						else*/ 
						if($double_dyeing==2 || $double_dyeing==0){
 							echo "14**0**$recipe_no";
 							disconnect($con);
							die;
						}
						
					}
				}
			}
		}


		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $batch_id . " and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1)
		{
			//disconnect($con);
			//echo "13**0**$batch_id";
			//die;
		}


		if (str_replace("'", "", $update_id) == "") {
			/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no" )==1)
			{
				echo "11**0";
				die;
			}*/

			$id = return_next_id("id", "pro_recipe_entry_mst", 1);
			
			$field_array = "id, entry_form, labdip_no, company_id,working_company_id, location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, color_id, buyer_id, color_range, booking_type, total_liquor, batch_ratio, liquor_ratio,batch_qty, remarks, inserted_by, insert_date, pickup, surplus_solution,copy_from,recipe_serial_no";
			//echo $txt_liquor;
			$data_array = "(" . $id . ",468," . $txt_labdip_no . "," . $cbo_company_id . "," . $cbo_working_company_id . "," . $cbo_location . "," . $txt_recipe_des . "," . $txt_batch_id . "," . $cbo_method . "," . $txt_recipe_date . "," . $cbo_order_source . "," . $txt_booking_order . "," . $txt_booking_id . ",'" . $color_id . "'," . $cbo_buyer_name . "," . $cbo_color_range . "," . $txt_booking_type . "," . $txt_liquor . "," . $txt_batch_ratio . "," . $txt_liquor_ratio . "," . $txt_batch_weight . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_pick_up . "," .$surpls_solution . "," .$txt_copy_from . ",'" .$recipe_serial_no . "')";

			//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=0;
			$recipe_update_id = $id;
		} else {
			/*$requisition_no="";
			$sql_reqs="select requ_no from dyes_chem_issue_requ_mst where recipe_id=$update_id and status_active=1 and is_deleted=0 order by id";
			$data=sql_select($sql_reqs);
			if(count($data)>0)
			{
				foreach($data as $row)
				{
					if($requisition_no=="") $requisition_no=$row[csf('requ_no')]; else $requisition_no.=",\n".$row[csf('requ_no')];
				}

				echo "14**".$requisition_no."**1";
				die;
			}*/

			/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no and id<>$update_id" )==1)
			{
				echo "11**0";
				die;
			}*/
			if ($db_type == 0)
			{
				$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a, dyes_chem_requ_recipe_att b set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where a.id=b.mst_id and b.recipe_id=" . $update_id);
			} else {
				$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where exists( select b.mst_id from dyes_chem_requ_recipe_att b where a.id=b.mst_id and b.recipe_id=" . $update_id . ")");
			}

			$reqsn_update_att = execute_query("update dyes_chem_requ_recipe_att set is_apply_last_update=2 where recipe_id=" . $update_id);


			if ($reqsn_update && $reqsn_update_att) {
				$flag = 1;
			} else {
				$flag = 0;
			}



			if (is_duplicate_field("sub_process_id", "pro_recipe_entry_dtls", "mst_id=$update_id and sub_process_id=$cbo_sub_process and status_active=1 and is_deleted=0") == 1) {
				echo "11**0";
				disconnect($con);
				die;
			}
			$field_array_update = "labdip_no*company_id*working_company_id*location_id*recipe_description*batch_id*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*booking_id*booking_type*total_liquor*batch_ratio*liquor_ratio*batch_qty*remarks*updated_by*update_date*pickup*surplus_solution";

			$data_array_update = $txt_labdip_no . "*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $txt_recipe_date . "*" . $cbo_order_source . "*" . $txt_booking_order . "*" . $color_id . "*" . $cbo_buyer_name . "*" . $cbo_color_range . "*" . $txt_booking_id . "*" . $txt_booking_type . "*" . $txt_liquor . "*" . $txt_batch_ratio . "*" . $txt_liquor_ratio . "*" . $txt_batch_weight . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . $txt_pick_up . "*" . $surpls_solution;

			//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0;
			$recipe_update_id = str_replace("'", "", $update_id);
		}
		/*if (str_replace("'", "", $copy_id) == 2) 
		{
			$sub_seq_no =return_field_value("max(a.sub_seq) as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . " and a.status_active=1 and a.is_deleted=0", "sub_seq");
			$sub_seq=$sub_seq_no+1;
		}*/
		//echo "10**".$sub_seq;die;
		
		if (str_replace("'", "", $copy_id) == 2) {
			if ($subprocess == 93 || $subprocess == 94 || $subprocess == 95 || $subprocess == 96 || $subprocess == 97 || $subprocess == 98) {
				$txt_comments_1 = str_replace("'", "", $txt_comments_1);
				$txt_ratio_1 = str_replace("'", "", $txt_ratio_1);
				$txt_seqno_1 = str_replace("'", "", $txt_seqno_1);
				$cbo_dose_base_1 = str_replace("'", "", $cbo_dose_base_1);

				$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,comments,liquor_ratio,total_liquor,ratio,seq_no,sub_seq,dose_base,inserted_by,insert_date";
				$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
				$data_array_dtls = "(" . $dtls_id . "," . $recipe_update_id . "," . $cbo_sub_process . "," . $cbo_store_name . "," . $txt_subprocess_remarks . ",'" . $txt_comments_1 . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . $txt_ratio_1 . "','" . $txt_seqno_1 . "'," . $txt_subprocess_seq . ",'" . $cbo_dose_base_1 . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			} else {

				$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,ratio,seq_no,sub_seq,inserted_by,insert_date";
				$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);

				for ($i = 1; $i <= $total_row; $i++) {
					$product_id = "product_id_" . $i;
					$txt_item_lot = "txt_item_lot_" . $i;
					$cbo_dose_base = "cbo_dose_base_" . $i;
					$txt_ratio = "txt_ratio_" . $i;
					$txt_comments = "txt_comments_" . $i;
					$txt_seqno = "txt_seqno_" . $i;
					if ($i != 1) $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . "," . $cbo_sub_process . "," . $cbo_store_name . "," . $txt_subprocess_remarks . ",'" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_item_lot) . "','" . str_replace("'", "", $$txt_comments) . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . str_replace("'", "", $$cbo_dose_base) . "','" . str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "','" . str_replace("'", "", $txt_subprocess_seq) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$dtls_id = $dtls_id + 1; 
				}
				//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			}

		} else if (str_replace("'", "", $copy_id) == 1) {
			/*if($subprocess==93 || $subprocess==94 || $subprocess==95 || $subprocess==96 || $subprocess==97 || $subprocess==98)
		  {
				$field_array_dtls="id,mst_id,sub_process_id,process_remark,comments,ratio,seq_no,dose_base,inserted_by,insert_date";
				$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;
				$sql="select id, sub_process_id, prod_id, item_lot,comments,process_remark, dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id_check order by id";

				$nameArray=sql_select( $sql );
				$data_array_dtls="(".$dtls_id.",".$recipe_update_id.",'".$nameArray[0][csf('sub_process_id')]."','".$nameArray[0][csf('process_remark')]."','".$nameArray[0][csf('comments')]."','".$nameArray[0][csf('ratio')]."','".$nameArray[0][csf('seq_no')]."','".$nameArray[0][csf('dose_base')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			}*/
			// else
			//{
			if($stock_check==1)//Stock Qty Check Here
			{ 
			 $sql_stock = "select c.id,c.current_stock
	from product_details_master c,pro_recipe_entry_dtls b
	where c.id=b.prod_id and c.company_id=$cbo_working_company_id
	and c.item_category_id in(5) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.mst_id=$update_id_check  ";
		$result_stock = sql_select($sql_stock);
				foreach ($result_stock as $row) 
				{
					if($row[csf('current_stock')]<=0)
					{
						echo "20**Copy not allowed,Stock Zero/Minus Found,Prod Id=".$row[csf('id')];
						disconnect($con);
						die;
					}
					
				}
			
			}
				
			$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,ratio,seq_no,sub_seq,inserted_by,insert_date";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			$sql = "select id, sub_process_id, prod_id, item_lot,comments,process_remark, dose_base,liquor_ratio,total_liquor,ratio,seq_no,sub_seq, store_id from pro_recipe_entry_dtls where mst_id=$update_id_check  and status_active=1  order by id";
			$nameArray = sql_select($sql);
			$tot_row = count($nameArray);
			$i = 1;

			foreach ($nameArray as $row) {
				//$row[csf('sub_process_id')];
				//$product_id="product_id_".$i;
				//$txt_item_lot="txt_item_lot_".$i;
				//$cbo_dose_base="cbo_dose_base_".$i;

				//$txt_seqno="txt_seqno_".$i;
				//$txt_ratio="txt_ratio_".$i; //,".$txt_liquor_ratio_dtls.",".$txt_total_liquor_ratio."
				//$ratio=str_replace("'","",$$txt_ratio);
			
	
				$process_remark=str_replace("'", "", $row[csf('process_remark')]);
				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",'" . $row[csf('sub_process_id')] . "','" . $row[csf('store_id')] . "','" . $process_remark . "','" . $row[csf('prod_id')] . "','" . $row[csf('item_lot')] . "','" . $row[csf('comments')] . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . $row[csf('dose_base')] . "','" . $row[csf('ratio')] . "','" . $row[csf('seq_no')] . "','" . $row[csf('sub_seq')] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$dtls_id = $dtls_id + 1;
				$i++;
			}
			//}

		}

	//	echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		/*$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		} */

		//test all insert
		if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("pro_recipe_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		} else {
			$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}
		$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}
		//echo "10**".$rID.'=='.$rID2.'=='.$flag;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $recipe_update_id . "**" . $subprocess. "**" . $recipe_serial_no; 
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $recipe_update_id . "**" . $subprocess. "**" . $recipe_serial_no;
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

		/*$requisition_no="";
		$sql_reqs="select requ_no from dyes_chem_issue_requ_mst where recipe_id=$update_id and status_active=1 and is_deleted=0 order by id";
		$data=sql_select($sql_reqs);
		if(count($data)>0)
		{
			foreach($data as $row)
			{
				if($requisition_no=="") $requisition_no=$row[csf('requ_no')]; else $requisition_no.=",\n".$row[csf('requ_no')];
			}

			echo "14**".$requisition_no."**1";
			die;
		}*/

		/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no and id<>$update_id" )==1)
		{
			echo "11**0";
			die;
		}*/
	//	txt_subprocess_seq
		$batch_id = str_replace("'", "", $txt_batch_id);
		$subprocess = str_replace("'", "", $cbo_sub_process);
		if (str_replace("'", "", $copy_id) == 1) {
			//$recipe_no = return_field_value("id", "pro_recipe_entry_mst", "batch_id=" . $batch_id . " and entry_form=468", "id");

			$batch_process_ids = return_field_value("process_id","pro_batch_create_mst","id = $batch_id");
			if($batch_process_ids!= "")
			{
				$batch_process_id_arr = explode(",", $batch_process_ids);
				if(!in_array("137", $batch_process_id_arr))
				{
					$recipe_no = return_field_value("a.id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and b.sub_process_id=$subprocess and a.batch_id=" . $batch_id . " and a.entry_form=468 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
					if ($recipe_no != '') {
						echo "14**0**$recipe_no";
						disconnect($con);
						die;
					}
				}
			}
		}

		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $batch_id . " and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1)
		{
			disconnect($con);
			echo "13**0**$batch_id";
			disconnect($con);
			die;
		}



		//$color_id = return_id($txt_color, $color_arr, "lib_color", "id,color_name");//booking_id 	booking_type 	total_liquor
		//txt_batch_weight
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","468");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$field_array_update = "labdip_no*company_id*working_company_id*location_id*recipe_description*batch_id*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*booking_id*booking_type*total_liquor*batch_ratio*liquor_ratio*batch_qty*remarks*updated_by*update_date*pickup*surplus_solution";

		$data_array_update = $txt_labdip_no . "*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $txt_recipe_date . "*" . $cbo_order_source . "*" . $txt_booking_order . "*'" . $color_id . "'*" . $cbo_buyer_name . "*" . $cbo_color_range . "*" . $txt_booking_id . "*" . $txt_booking_type . "*" . $txt_liquor . "*" . $txt_batch_ratio . "*" . $txt_liquor_ratio . "*" . $txt_batch_weight . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'" . "*" . $txt_pick_up . "*" . $surpls_solution;

		//echo $data_array_update;die;
		//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//if($rID) $flag=1; else $flag=0;
		if ($subprocess == 93 || $subprocess == 94 || $subprocess == 95 || $subprocess == 96 || $subprocess == 97 || $subprocess == 98) {
			//$update_dtls_id=str_replace("'","",$updateIdDtls_1);
			$field_array_dtls_update2 = "sub_process_id*process_remark*comments*liquor_ratio*total_liquor*ratio*seq_no*sub_seq*dose_base*updated_by*update_date";
			//$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;
			//$data_array_dtls_update="$txt_item_lot)."'*"; 
			$data_array_dtls_update2 = $subprocess . "*" . $txt_subprocess_remarks . "*" . $txt_comments_1 . "*" . $txt_liquor_ratio_dtls . "*" . $txt_total_liquor_ratio . "*" . $txt_ratio_1 . "*" . $txt_seqno_1 . "*" . $txt_subprocess_seq . "*" . $cbo_dose_base_1 . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$update_dtls_id = str_replace("'", "", $updateIdDtls_1);


		} else {
			$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,ratio,seq_no,sub_seq,inserted_by,insert_date";
			$field_array_dtls_update = "prod_id*item_lot*comments*liquor_ratio*total_liquor*dose_base*ratio*seq_no*sub_seq*sub_process_id*process_remark*updated_by*update_date";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);

			for ($i = 1; $i <= $total_row; $i++) {
				$product_id = "product_id_" . $i;
				$txt_item_lot = "txt_item_lot_" . $i;
				$txt_comments = "txt_comments_" . $i;
				$cbo_dose_base = "cbo_dose_base_" . $i;
				$txt_ratio = "txt_ratio_" . $i;
				$updateIdDtls = "updateIdDtls_" . $i;
				$txt_seqno = "txt_seqno_" . $i;

				if (str_replace("'", "", $$updateIdDtls) != "") {
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", (str_replace("'", "", $$product_id) . "*'" . str_replace("'", "", $$txt_item_lot) . "'*'" . str_replace("'", "", $$txt_comments) . "'*" . $txt_liquor_ratio_dtls . "*" . $txt_total_liquor_ratio . "*'" . str_replace("'", "", $$cbo_dose_base) . "'*'" . str_replace("'", "", $$txt_ratio) . "'*'" . str_replace("'", "", $$txt_seqno) . "'*" . $txt_subprocess_seq . "*" . $cbo_sub_process . "*" . $txt_subprocess_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				} else {
					if ($data_array_dtls != "") $data_array_dtls .= ",";

					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . "," . $cbo_sub_process . "," . $cbo_store_name . "," . $txt_subprocess_remarks . ",'" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_item_lot) . "','" . str_replace("'", "", $$txt_comments) . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . str_replace("'", "", $$cbo_dose_base) . "','" . str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "'," . $txt_subprocess_seq . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$dtls_id = $dtls_id + 1;
				}
			}
		}
		//print_r ($data_array_dtls_update);die;
		/*if($data_array_dtls_update!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ),1);
			//echo bulk_update_sql_statement( "pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if($data_array_dtls!="")
		{
			//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}*/

		// Update test all
		$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID) $flag = 1; else $flag = 0;
		if ($data_array_dtls_update2 != "") {
			$rID = sql_update("pro_recipe_entry_dtls", $field_array_dtls_update2, $data_array_dtls_update2, "id", $update_dtls_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}
		if ($data_array_dtls_update != "") {
			$rID2 = execute_query(bulk_update_sql_statement("pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr), 1);
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($data_array_dtls != "") {
			//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($db_type == 0) {
			$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a, dyes_chem_requ_recipe_att b set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where a.id=b.mst_id and b.recipe_id=" . $update_id);
		} else {
			$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where exists( select b.mst_id from dyes_chem_requ_recipe_att b where a.id=b.mst_id and b.recipe_id=" . $update_id . ")");
		}

		$reqsn_update_att = execute_query("update dyes_chem_requ_recipe_att set is_apply_last_update=2 where recipe_id=" . $update_id);

		if ($flag == 1) {
			if ($reqsn_update && $reqsn_update_att) {
				$flag = 1;
			} else {
				$flag = 0;
			}
		}

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . $subprocess;
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . $subprocess;
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$batch_id = str_replace("'", "", $txt_batch_id);

		$recipe_no = return_field_value("a.id as id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and a.id=" . $update_id . " and a.entry_form!=468 and  b.status_active=1 and b.is_deleted=0", "id");
		if ($recipe_no != '')
		{
			echo "133**0**$recipe_no";
			disconnect($con);
			die;
		}

		if($recipe_no=='')
		{
			$req_no = return_field_value("b.requ_no as id", "dyes_chem_issue_requ_mst b,dyes_chem_requ_recipe_att a", " b.id=a.mst_id and a.recipe_id=" .$update_id." and  b.status_active=1 and b.is_deleted=0 ", "id");
		}
		if ($req_no != '')
		{
			echo "11**0**$req_no";
			disconnect($con);
			die;
		}
		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $batch_id . " and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1)
		{
			disconnect($con);
			echo "13**0**$batch_id";
			disconnect($con);
			die;
		}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

			//echo "10**".$delete_cause;die;

			/*$rID1=execute_query( "update pro_recipe_entry_mst set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$update_id).")",0);
			if($rID1) $flag = 1; else $flag = 0;
			$rID2=execute_query( "update pro_recipe_entry_dtls set status_active=0,is_deleted=1 where  mst_id in(".str_replace("'","",$update_id).") ",0);
			if( $flag==1)
			{
				if($rID2) $flag = 1; else $flag = 0;
			}*/
			$rID2=execute_query( "update pro_recipe_entry_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause' where  mst_id in(".str_replace("'","",$update_id).") and sub_process_id=".str_replace("'","",$cbo_sub_process)." ",0);
			if($rID2) $flag = 1; else $flag = 0;

				//echo "10**".$rID2;die;
			if ($db_type == 0) {
				if ($flag == 1) {
					mysql_query("COMMIT");
					echo "2**" . str_replace("'","",$update_id);
				} else {
					mysql_query("ROLLBACK");
					echo "5**0**0";
				}
			}

			if ($db_type == 2 || $db_type == 1) {
				if ($flag == 1) {
					oci_commit($con);
					echo "2**" . str_replace("'","",$update_id);
				} else {
					oci_rollback($con);
					echo "5**0**0";
				}
			}
			disconnect($con);
			die;
		}
	}

	if ($action == "item_req_check_data") {
		$data = explode("**", $data);

		$company=$data[0];
		$update_id=$data[1];
		$cbo_sub_process=$data[2];
		$batch_id=$data[3];

		$recipe_no = return_field_value("a.id as id", "pro_recipe_entry_mst a,pro_recipe_entry_dtls b", "a.id=b.mst_id and a.id=" . $update_id . " and a.entry_form!=468 and  b.status_active=1 and b.is_deleted=0", "id");
		if ($recipe_no != '')
		{
			echo "133**0**$recipe_no";
			die;
		}

		if($recipe_no=='')
		{
			$req_no = return_field_value("b.requ_no as id", "dyes_chem_issue_requ_mst b,dyes_chem_requ_recipe_att a", " b.id=a.mst_id and a.recipe_id=" .$update_id." and  b.status_active=1 and b.is_deleted=0", "id");
		}
		if ($req_no != '')
		{
			echo "11**0**$req_no";
			die;
		}
		$batch_no = return_field_value("batch_no as id", "pro_fab_subprocess", "batch_id=" .$batch_id." and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0", "id");

		if ($batch_no!= '')
		{
			echo "13**0**$batch_no";
		}

		exit();
	}

	if ($action == "item_stock_details") {
		$data = explode("**", $data);
		if ($db_type == 2) $grp_con = "listagg(prod_id,',') within group (order by prod_id) as prod_id";
		else $grp_con = "group_concat(prod_id) as prod_id";

		$sql = "select $grp_con from pro_recipe_entry_dtls where   mst_id=" . $data[1] . " and is_deleted=0 and status_active=1";
		$data_recipe = sql_select($sql);
		$prod_id = $data_recipe[0][csf('prod_id')];
		$sql_stock = "select id,current_stock from product_details_master where company_id='$data[0]' and item_category_id in(5,6,7,23) and status_active=1 and is_deleted=0 and id in($prod_id) and current_stock<=0";
	//echo $sql;
		$item_stock = sql_select($sql_stock);
		$current_stock = $item_stock[0][csf('current_stock')];

		if ($current_stock <= 0) {
			echo "1" . "_" . $item_stock[0][csf('id')];
		} else {
			echo "0_";
		}
		exit();
	}

	if ($action == "recipe_entry_print") {
		extract($_REQUEST);
		$data = explode('*', $data);
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
		$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
		$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

		$batch_array = array();
		if ($db_type == 0) {
			$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
		} else if ($db_type == 2) {
			$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight order by a.id DESC";
		}
		// echo $sql;
		$result_sql = sql_select($sql);
		foreach ($result_sql as $row) {
			$order_no = '';
			$order_id = array_unique(explode(",", $row[csf("po_id")]));
			if ($row[csf("entry_form")] == 36) {
				$batch_type = "<b> SUBCONTRACT ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
				}
			} else {
				$batch_type = "<b> SELF ORDER </b>";
				foreach ($order_id as $val) {
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
			$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
			$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
			$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
			$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
			$batch_array[$row[csf("id")]]['order'] = $order_no;
			$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		}


		$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
		$dataArray = sql_select($sql_recipe_mst);
		$w_com_id=$dataArray[0][csf('w_com_id')];
		$company_id=$dataArray[0][csf('company_id')];

		$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
		$group_id=$nameArray[0][csf('group_id')];
		?>
		<div style="width:930px; font-size:6px">
			<table width="930" cellspacing="0" align="right" border="0">
				<tr>
					<td colspan="6" align="center" style="font-size:x-large">
						<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
					</strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center">
						<?

						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')]; ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							Zip Code: <? echo $result[csf('zip_code')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
				</tr>
				<tr>
					<td width="130"><strong>System ID:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="130"><strong>Labdip No: </strong></td>
					<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
					<td width="130"><strong>Recipe Des.:</strong></td>
					<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No:</strong></td>
					<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
					<td><strong>Recipe Date:</strong></td>
					<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
					<td><strong>Order Source:</strong></td>
					<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer Name:</strong></td>
					<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
					<td><strong>Booking:</strong></td>
					<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
					<td><strong>Color:</strong></td>
					<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range:</strong></td>
					<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
                	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
                	<td><strong>Batch Weight:</strong></td>
                	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
                	<td><strong>Trims Weight:</strong></td>
                	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
                </tr>
                <tr>

                	<td><strong>Order No.:</strong></td>
                	<td>
                		<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
                		else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
                		<td><strong>Method:</strong></td>
                		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                	</tr>
                	<tr>
                		<td><strong>Remarks:</strong></td>
                		<td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
                	</tr>
                </table>
                <br>
                <?
				$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
                <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
				<thead bgcolor="#dddddd" align="center">
					
						<tr bgcolor="#CCCCFF">
							<th colspan="5" align="center"><strong>Fabrication</strong></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Dia/ W. Type</th>
							<th width="200">Constrution & Composition</th>
							<th width="70">Gsm</th>
							<th width="70">Dia</th>
						</tr>
				</thead>
				<tbody>
					<?
						foreach ($batch_id_qry as $b_id) {
							 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
					?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>			
									<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
									<td align="center"><? echo $fabrication[2]; ?></td>
									<td align="center"><? echo $fabrication[3]; ?></td>
								</tr>
					<?
								$j++;
							}
						}
					?>
				</tbody>
			</table>
            <br> <br> <br>
                <div style="width:100%;">
                	<table align="right" style="margin:5px;" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                		<thead bgcolor="#dddddd" align="center">
                			<th width="30">SL</th>
                			<th width="110">Item Cat.</th>
                			<th width="110">Product ID</th>
                			<th width="200">Item Group</th>
                			<th width="220">Item Description</th>
                			<th width="80">Item Lot</th>
                			<th width="50">UOM</th>
                			<th width="100">Dose Base</th>
                			<th width="100">Ratio</th>
                			<th width="">Comments</th>
                		</thead>
                		<?
                		$i = 1;
                		$j = 1;
                		$mst_id = $data[1];
                		$com_id = $data[0];


                		$process_array = array();
                		$sub_process_data_array = array();
                		$sub_process_remark_array = array();
                		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
                		$nameArray = sql_select($sql_ratio);
                		foreach ($nameArray as $row) {
                			if (!in_array($row[csf("sub_process_id")], $process_array)) {
                				$process_array[] = $row[csf("sub_process_id")];
                				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
                			}
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
                			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];


                		}

                		if ($db_type == 2) {
                			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/

                			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
                			union
                			(
                			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in(93,94,95,95,96,97,98)
                		)  order by seq_no,sub_process_id";
                	} else if ($db_type == 0) {
                		$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
                		union
                		(
                		select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in(93,94,95,95,96,97,98)
                	)  ";
                }
				// echo $sql;
                $sql_result = sql_select($sql);

                foreach ($sql_result as $row) {
                	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . ",";

                }

                foreach ($process_array as $process_id) {
                	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

                	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
                	$remark = $sub_process_remark_array[$process_id]['remark'];
                	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
                	$tot_ratio=1.5;
                	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

                	?>
                	<tr bgcolor="#EEEFF0">
                		<td colspan="9" align="left"><b>Sub Process
                			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark .' Total Liquor(ltr): '.$liquor_ratio.', '. 'Levelling  Water(Ltr): '.number_format($leveling_water,2,'.',''); ?></b>
                		</td>
                	</tr>
                	<?
                	$tot_ratio = 0;
                	$sub_process_data = explode(",", substr($sub_process_data_array[$process_id], 0, -1));
                	foreach ($sub_process_data as $data) {
                		$data = explode("**", $data);
                		$current_stock = $data[13];
                		$current_stock_check=number_format($current_stock,7,'.','');
                		if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98)
						{
							$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$i++;
						}
						else
						{
							
						
	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td><? echo $i; ?></td>
	                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td><p><? echo $prod_id; ?></p></td>
	                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td><p><? echo $item_description; ?></p></td>
	                				<td><p><? echo $item_lot; ?></p></td>
	                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
	                				<td align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$i++;
	                		}
                		}

                	}
                	?>
                	<tr class="tbl_bottom">
                		<td align="right" colspan="7"><strong>Sub Process Total</strong></td>
                		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                	</tr>
                	<?
                }
                ?>

                <tr class="tbl_bottom">
                	<td align="right" colspan="7"><strong>Grand Total</strong></td>
                	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(62, $com_id, "1030px");
            ?>
        </div>
    </div>
    <?
}

if($action == "recipe_entry_print_2")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$update_id = $data[1];
	$txt_labdip_no = $data[2];
	$txt_yarn_lot = $data[3];
	$txt_brand= $data[4];
	$txt_count= $data[5];
	$txt_pick_up= $data[6];
	$surpls_solution= $data[7];
	$batch_id= $data[8];
	$sub_process_id = $data[9];
	$report_title = $data[10];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.batch_weight, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id'  and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		 $sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.batch_weight, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form, a.batch_weight, a.total_trims_weight order by a.id DESC";
	}
	//echo $sql;

	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['batch_weight'] = $row[csf("batch_weight")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		$batch_nos=$row[csf("batch_no")];
	}

	$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$update_id'";

	$dataArray = sql_select($sql_recipe_mst);
	//var_dump( $dataArray);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];

	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");

	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	//total_solution = batch_weight*pickup/100+surplus
	//total_solution = batch_weight+surplus ==> modified by shehab
	//$total_solution = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']+$surpls_solution);
	 $total_solution = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*$txt_pick_up)/100)+$surpls_solution); 

	//solution_amount = (total_solution/5)*4;
	$solution_amount = ($total_solution/5)*4;

	//Alkali_Solution = total_solution/5;
	$alkali_solution_amount = $total_solution/5;

	$construction_sql = "
	select a.id,
	a.mst_id,
	a.prod_id,
	a.item_description,
	a.gsm,
	b.detarmination_id,
	b.item_category_id,
	b.unit_of_measure,
	c.construction
	from pro_batch_create_dtls a,
	product_details_master b,
	lib_yarn_count_determina_mst c
	where   a.prod_id = b.id
	and b.detarmination_id = c.id
	and b.item_category_id = 13
	and a.is_deleted = 0
	and a.status_active = 1
	and b.is_deleted = 0
	and b.status_active = 1
	and c.is_deleted = 0
	and c.status_active = 1
	and a.mst_id = $batch_id

	order by a.id";
	  //echo $construction_sql;
	$const_result = sql_select($construction_sql);
	  //var_dump ($const_result);
	foreach ($const_result as $row) {
		$construction_data["mst_id"] = $row[csf("mst_id")];
		$construction_data["prod_id"] = $row[csf("prod_id")];
		$construction_data["item_description"] = $row[csf("item_description")];
		$construction_data["uom"] = $row[csf("unit_of_measure")];
		$construction_data["fabric_type"] = $row[csf("construction")];
		$construction_data["gsm"] = $row[csf("gsm")];

	}

	$composition_arra = explode(",",$construction_data["item_description"]);

		//Total Length = (Batch Weight/GSM/width)*1000;
	//$total_length = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']/$composition_arra[2])/$composition_arra[3])*1000);
	$width_new=$composition_arra[3]/39.37;
	$total_length = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*1000)/($width_new*$composition_arra[2]);
	//echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight'].'='.$width_new.'='.$composition_arra[2];
	?>
	<div style="width:1000px; font-size:6px">
		<table width="1000" cellspacing="0" align="center" border="0" role="all">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" style="font-size:x-large; text-align:center;"><u><strong><? echo $company_library[$data[0]]; //.data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" align="center" border="1" width="1000" style="margin-top:20px;" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="8" align="center" style="font-size:20px"><u><strong>Recipe Calculation for CPB Bulk</strong></u></th>
				</tr>
			</thead>
			<tr>
				<td width="180" align="left"><strong>Labdip No </strong></td>
				<td width="220px" align="center"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="150" align="left"><strong>Color Name</strong></td>
				<td colspan="2" align="center"> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td width="130" align="left"><strong>Date</strong></td>
				<td colspan="2" align="center"> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
			</tr>
			<tr bgcolor="#dddddd" >
				<td width="180" align="left"><strong>Buyer</strong></td>
				<td width="220" align="center"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td width="150" align="left"><strong>Shade Type</strong></td>
				<td colspan="2" align="center"><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td width="130" rowspan="3" align="left"><strong>PH</strong></td>
				<td width="150" align="left"><strong>Dyes Solution</strong></td>
				<td width="60" align="center"></td>
			</tr>
			<tr>
				<td width="180" align="left"><strong>Order No</strong></td>
				<td width="220" align="center">
					<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
					else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
					<td colspan="3" align="center"><strong>Fabric Specifications</strong></td>
					<td width="150" align="left"><strong>Alkali Solution</strong></td>
					<td width="60" align="center"></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Fabric Type</strong></td>
					<td width="220" align="center"> <? echo $construction_data["fabric_type"];//need fabric type data ?></td>
					<td width="150" align="left"><strong>Width</strong></td>
					<td width="90" align="center" title="Width/39.37"><? echo number_format($composition_arra[3]/39.37,4);?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]]?></td>
					<td width="150" align="left"><strong>Dye Lqour</strong></td>
					<td width="60" align="center"></td>

				</tr>
				<tr>
					<td width="180" align="left"><strong>Composition</strong></td>
					<td width="220" align="center"> <? echo $composition_arra[1]; ?></td>
					<td width="150" align="left"><strong>GSM</strong></td>
					<td width="90" align="center"><? echo $composition_arra[2];?></td>
					<td width="90" align="center">G<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Lot</strong></td>
					<td colspan="2" align="center"><? echo $txt_yarn_lot;?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padder Pressure:</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left" title="Batch Weight*1000/(Width*GSM)"><strong>Total Length</strong></td>
					<td width="90" align="center"><? echo number_format($total_length,4); ?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Brand</strong></td>
					<td colspan="2" align="center"><? echo $txt_brand;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>M/M For Body Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Batch weight</strong></td>
					<td width="90" align="center"><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']; ?></td>
					<td width="90" align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					<td width="150" align="left"><strong>Dye Lot No</strong></td>
					<td colspan="2" align="center"></td>
                   
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>M/M For Rib Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Pick Up</strong></td>
					<td width="90" align="center"><? echo $txt_pick_up;?></td>
					<td width="90" align="center">%</td>
                    <td width="150" align="left"><strong>Batch No</strong></td>
					<td colspan="2" align="center"><? echo $batch_nos;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Rotation Hours</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Surplus Solution</strong></td>
					<td width="90" align="center"><? echo $surpls_solution;?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padding Complition Time</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Total Solution</strong></td> 
					<td width="90" align="center" title="(Batch Wgt*PickUp/100)+Surplus  Solution"><? echo number_format($total_solution,4); ?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Washing Time</strong></td>
					<td width="220" align="center"> </td>
                    
				</tr>
			</table>
			<br/><br/>
			<div style="width: 600px; float: left; margin-top:15px; margin-right:10px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th colspan="5">Dyes Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="130" align="center">Solution Amount</td>
						<td colspan="3" align="center"><? echo number_format($solution_amount,0);?></td>
						<td align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td width="130" align="center">Particulars</td>
						<td width="170" align="center">Brand Name</td>
						<td width="50" align="center">GPL</td>
						<td width="90" align="center">Amount</td>
						<td width="60" align="center"></td>
					</tr>
					<?

					if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98)
					{
						$ratio_cond="";
					}
					else
					{
						$ratio_cond=" and ratio>0 ";
					}
					//AND a.sub_process_id = $sub_process_id //Remove it Faisal-30-01-2020
					
					$recipeData=sql_select("SELECT
						a.id,
						a.prod_id,
						a.ratio,
						b.item_category_id,
						b.item_description,
						b.unit_of_measure
						FROM pro_recipe_entry_dtls a, product_details_master b
						WHERE     a.prod_id = b.id
						AND a.mst_id = $update_id
						
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						$ratio_cond
						ORDER BY seq_no");
					$tot_dyes_soluton_item_amount=0;
					$recipe_prod_id_arr=array();
					$prod_id_chk_arr=array(9714,89815,80530,9716,9704,100889,16274,16863);
					foreach($recipeData as $row)
					{
						if((in_array($row[csf('prod_id')],$prod_id_chk_arr)) ||  $row[csf('item_category_id')] == 6) {
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] = $row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];
						}
						if((!in_array($row[csf('prod_id')],$prod_id_chk_arr)) && $row[csf('item_category_id')] == 5 || $row[csf('item_category_id')] == 7 ){
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']=$row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];

						}
						$recipe_prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];

						//Dyes Solution total amount = (Total Solution * GPL)/1000 {if uom is kg else if uom is % then divided by 100}
						if($recipe_data_arr[$row[csf('prod_id')]]['uom'] == 12){
							$dyes_soluton_item_amount = ($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/1000;
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}else{
							$dyes_soluton_item_amount = (($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/100);
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}



						if((in_array($row[csf('prod_id')],$prod_id_chk_arr)) || $recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] == 6){
							//Required_water = solution_amount - (sum_of_particulars_amount)
							//$required_water = $solution_amount - $dyes_soluton_item_total;
							
							$tot_dyes_soluton_item_amount+=$dyes_soluton_item_amount;
							?>
							<tr>
								<td align="center"  width="130"><? echo $item_category[$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']];?></td>
								<td align="center"  width="170"><? echo $recipe_data_arr[$row[csf('prod_id')]]['item_description'];?></td>
								<td  align="center" title="ProdID=<? echo $row[csf('prod_id')]; ?>" width="50"><? echo $recipe_data_arr[$row[csf('prod_id')]]['ratio'];?></td>
								<td align="center"  width="90"><? echo $dyes_soluton_item_amount; ?></td>
								<td align="center"  width="60"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td  align="center" colspan="2">Required Water</td>
						<td align="center"  colspan="2" title="Dyes Solution/Amount"><? $required_water=$solution_amount-$tot_dyes_soluton_item_amount; echo number_format($required_water,4);?></td>
						<td align="center" >L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>

				</table>
			</div>
			<div style="width: 370px; float: left; margin-top:15px; margin-left:3px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="360" class="rpt_table">
					<thead>
						<tr bgcolor="#dddddd" align="center">
							<th colspan="4">Alkali Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="110"></td>
						<td width="110" title="<? echo $alkali_solution_amount;?>"  align="center"><? echo number_format($alkali_solution_amount,0); ?></td>
						<td width="80" align="center"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
						<td width="60"></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td align="center"  width="110"><strong>Chemicals</strong></td>
						<td align="center"  width="110"><strong>Recipe GPL</strong></td>
						<td align="center"  width="110"><strong>Amount</strong></td>
						<td align="center"  width="70"></td>
					</tr>
					<?
			//var_dump($recipe_data_arr);
			$tot_alkali_item_soluton_amount=0;
					foreach ($recipe_data_arr as $key => $value) {
						$recipe_alkali_data_arr[$key]['item_category_id'] = $value['item_category_id'];
						$recipe_alkali_data_arr[$key]['item_description'] = $value['item_description'];
						$recipe_alkali_data_arr[$key]['uom'] = $value['unit_of_measure'];
						$recipe_alkali_data_arr[$key]['ratio'] = $value['ratio'];

						if($recipe_alkali_data_arr[$key]['uom'] == 12){
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/1000;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}else{
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/100;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}
					//alkali_water = alkali_solution_amount - (sum_of_chemicals_amount)
						//$water = $alkali_solution_amount - $alkali_item_soluton_total;
						

				if((!in_array($key,$prod_id_chk_arr)) && ($recipe_alkali_data_arr[$key]['item_category_id'] == 5 || $recipe_alkali_data_arr[$key]['item_category_id'] == 7))
						{	
							$tot_alkali_item_soluton_amount+=$alkali_item_soluton_amount;
							?>
							<tr>
								<td width="110"><? echo $recipe_alkali_data_arr[$key]['item_description'];?></td>
								<td align="center" title="ProdID=<? echo $key; ?>" width="110"><? echo $recipe_alkali_data_arr[$key]['ratio'];?></td>
								<td align="right"  width="110"><? echo $alkali_item_soluton_amount; ?></td>
								<td align="center"  width="70"><? echo $unit_of_measurement[$recipe_alkali_data_arr[$key]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td align="center">Water</td>
						<td align="center" colspan="2" title="Alkali Solution/Amount"><? $water = $alkali_solution_amount - $tot_alkali_item_soluton_amount;echo number_format($water,4);?></td>
						<td align="center">L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?
}
?>
