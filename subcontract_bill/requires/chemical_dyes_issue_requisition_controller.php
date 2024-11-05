<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") 
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------

$receipe_arr = array();
$liquior_arr = array();
$receipeData = sql_select("select id,labdip_no, total_liquor from pro_recipe_entry_mst where status_active=1 and is_deleted=0");
foreach ($receipeData as $row) 
{
	$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
	$liquior_arr[$row[csf("id")]] = $row[csf("total_liquor")];
}

$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

if ($action == "load_drop_down_location") 
{
	echo create_drop_down("cbo_location_name", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

if ($action == "machineNo_popup") 
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_id = str_replace("'", "", $cbo_company_id);
	$txt_batch_id = str_replace("'", "", $txt_batch_id);
	$batch_against=return_field_value("batch_against","pro_batch_create_mst","company_id='$cbo_company_id' and id='$txt_batch_id' and is_deleted=0 and status_active=1");
	if($batch_against==6){$category=6;}
	else if($batch_against==7){$category=2;}
	else if($batch_against==10){$category=3;}
	?>
	<script>
		function js_set_value(data) {
			var data = data.split("_");
			$("#hidden_machine_id").val(data[0]);
			$("#hidden_machine_name").val(data[1]);
			parent.emailwindow.hide();
		}
	</script>

	<input type="hidden" id="hidden_machine_id" name="hidden_machine_id">
	<input type="hidden" id="hidden_machine_name" name="hidden_machine_name">

	<?
	$location_name = return_library_array("select location_name,id from  lib_location where is_deleted=0", "id", "location_name");
	$floor = return_library_array("select floor_name,id from lib_prod_floor where is_deleted=0", "id", "floor_name");
	$arr = array(0 => $location_name, 1 => $floor);
	//and company_id='$cbo_company_id'
	$sql = "select location_id,floor_id,machine_no,machine_group,dia_width,gauge,id from lib_machine_name where is_deleted=0 and status_active=1 and company_id='$cbo_company_id' and category_id in($category)";
	echo create_list_view("list_view", "Location Name,Floor Name,Machine No,Machine Group,Dia Width,Gauge", "150,140,100,120,80", "740", "250", 1, $sql, "js_set_value", "id,machine_no", "", 1, "location_id,floor_id,0,0,0,0", $arr, "location_id,floor_id,machine_no,machine_group,dia_width,gauge", "", 'setFilterGrid("list_view",-1);', '');

	exit();
}

if ($action == "mrr_popup") 
{
	echo load_html_head_contents("Requisition Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data) 
		{
			$("#hidden_sys_id").val(data);
			parent.emailwindow.hide();
		}

        /*$(document).ready(function(e) {
         setFilterGrid('tbl_list_search',-1);
     });*/
 </script>

</head>
<body>
	<div align="center" style="width:860px; margin: 0 auto;">
		<form name="searchfrm" id="searchfrm">
			<fieldset style="width:855px;">
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Requisition Date Range</th>
						<th>Search By</th>
						<th width="250" id="search_by_td_up">Enter Requisition No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;"
							class="formbutton"/>
							<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to"
							class="datepicker" style="width:70px;">
						</td>
						<td>
							<?
							$search_by_arr = array(1 => "Requisition No", 2 => "Recipe No", 3 => "Batch No");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../') ";
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company; ?>', 'create_requisition_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_requisition_search_list_view") 
{
	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$start_date = trim($data[2]);
	$end_date = trim($data[3]);
	$company = $data[4];

	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_cond = "and requisition_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
		} 
		else 
		{
			$date_cond = "and requisition_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-", 1) . "'";
		}
	} 
	else 
	{
		$date_cond = "";
	}

	if ($search_string != "") 
	{
		if ($search_by == 1)
			$search_field_cond = "and requ_prefix_num='$search_string'";
		else if ($search_by == 2) 
		{
			if ($db_type == 0) $search_field_cond = " and FIND_IN_SET($search_string,recipe_id)";
			else if ($db_type == 2) $search_field_cond = " and ',' || recipe_id || ',' LIKE '%$search_string%'";
		} 
		else if ($search_by == 3) 
		{
			$batch_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$search_string'", "id");
			if ($db_type == 0) $search_field_cond = " and FIND_IN_SET($batch_ids,batch_id)";
			else if ($db_type == 2) $search_field_cond = " and ',' || batch_id || ',' LIKE '%$batch_ids%'";
		}
	} 
	else 
	{
		$search_field_cond = "";
	}

	$batch_arr = return_library_array("select id,batch_no from pro_batch_create_mst where company_id=$company and batch_against<>0 ", "id", "batch_no");
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");

	$po_arr = sql_select("select b.id,c.mst_id as batch_id,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and a.company_name=$company and b.status_active=1 and b.is_deleted=0");
	$file_ref_array = array();
	$po_array = array();
	foreach ($po_arr as $row) 
	{
		$file_ref_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$file_ref_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('batch_id')]]['po'] = $row[csf('id')];
	}

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	
	$sql = "select id, requ_no, requ_prefix_num, $year_field, company_id, requisition_date, batch_id,recipe_id,method from dyes_chem_issue_requ_mst where company_id=$company and requisition_basis=8 and entry_form=157 $date_cond $search_field_cond order by id";

	$result = sql_select($sql);
	?>
	<div align="center">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Company</th>
			<th width="90">Requisition No</th>
			<th width="60">Year</th>
			<th width="90">Requisition Date</th>
			<th width="100">Method</th>
			<th width="130">Recipe No</th>
			<th>Batch No</th>
		</thead>
	</table>
	<div style="width:800px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			$batch_no = '';
			$po_id_data = '';
			$batch_id = explode(",", $row[csf('batch_id')]);
			foreach ($batch_id as $val) 
			{
				if ($batch_no == "") $batch_no = $batch_arr[$val]; else $batch_no .= ", " . $batch_arr[$val];
				if ($po_id_data == "") $po_id_data = $po_array[$val]['po']; else $po_id_data .= "," . $po_array[$val]['po'];

			}
			$po_ids = array_unique(explode(",", $po_id_data));
			$file_no = '';
			$ref_no = '';
			foreach ($po_ids as $pid) 
			{
				if ($file_no == "") $file_no = $file_ref_array[$pid]['file']; else $file_no .= "," . $file_ref_array[$pid]['file'];
				if ($ref_no == "") $ref_no = $file_ref_array[$pid]['ref']; else $ref_no .= "," . $file_ref_array[$pid]['ref'];
			}
				//echo $po_id_data.'dddddd';
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
				<td width="40"><? echo $i; ?></td>
				<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
				<td width="90"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td>
				<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="90" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p>
				</td>
				<td width="100"><p><? echo $dyeing_method[$row[csf('method')]]; ?>&nbsp;</p></td>
				<td width="130"><p><? echo $row[csf('recipe_id')]; ?></p></td>
				<td><p><? echo $batch_no; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>
</div>
<?
exit();
}

if ($action == "populate_data_from_data") 
{
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$sql = sql_select("select id, requ_no, company_id, location_id, requisition_date, requisition_basis, recipe_id, method, machine_id, is_apply_last_update from dyes_chem_issue_requ_mst where id=$data");
	foreach ($sql as $row) 
	{
		echo "document.getElementById('txt_mrr_no').value = '" . $row[csf("requ_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value = '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('cbo_receive_basis').value = '" . $row[csf("requisition_basis")] . "';\n";
		echo "document.getElementById('cbo_method').value = '" . $row[csf("method")] . "';\n";
		echo "document.getElementById('machine_id').value = '" . $row[csf("machine_id")] . "';\n";

		$machine_name = "";
		if ($row[csf("machine_id")] > 0) 
		{
			$machine_name = return_field_value("machine_no", "lib_machine_name", "id=" . $row[csf('machine_id')]);
		}
		echo "document.getElementById('txt_machine_no').value = '" . $machine_name . "';\n";

		if ($row[csf("is_apply_last_update")] == 2) 
		{
			$s = 0;
			$msg = "";
			$recipe_data = sql_select("select a.is_apply_last_update, b.id, b.updated_by, b.update_date from dyes_chem_requ_recipe_att a, pro_recipe_entry_mst b where a.recipe_id=b.id and a.mst_id=" . $row[csf("id")] . "");
			foreach ($recipe_data as $recpRow) 
			{
				if ($recpRow[csf("is_apply_last_update")] == 2) 
				{
					$s++;
					$user_name = $user_arr[$recpRow[csf("updated_by")]];
					$update_dateTime = date("H:s:i d-M-Y", strtotime($recpRow[csf("update_date")]));
					if ($msg == "")
						$msg = "Recipe No- " . $recpRow[csf("id")] . " by " . $user_name . " on " . $update_dateTime;
					else
						$msg .= ", Recipe No- " . $recpRow[csf("id")] . " by " . $user_name . " on " . $update_dateTime;
				}
			}
			if ($s <= 1) 
			{
				echo "document.getElementById('last_update_message').innerHTML 	= 'After Requisition Recipe has been changed by $user_name on $update_dateTime To Revise Requisition Click Apply Last Update Button and Update.';\n";
			} 
			else 
			{
				echo "document.getElementById('last_update_message').innerHTML 	= 'After Requisition Recipe has been changed " . $msg . " To Revise Requisition Click Apply Last Update Button and Update.';\n";
			}
		} 
		else 
		{
			echo "document.getElementById('last_update_message').innerHTML 		= '';\n";
		}

		echo "get_php_form_data('" . $row[csf("recipe_id")] . "', 'populate_data_from_recipe_popup', 'requires/chemical_dyes_issue_requisition_controller' );\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_chemical_dyes_issue_requisition',1);\n";
		exit();
	}
}

if ($action == "labdip_popup") 
{
	echo load_html_head_contents("Labdip No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array();
		var prevsubprocess_id = '';
		var prevseq_no = '';
		var prevbooking_type = '';
		var preventry_form = '';

        /*function check_all_data()
         {
         var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

         tbl_row_count = tbl_row_count-1;
         for( var i = 1; i <= tbl_row_count; i++ ) {
         js_set_value( i );
         }
     }*/

     function toggle(x, origColor) {
     	var newColor = 'yellow';
     	if (x.style) {
     		x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
     	}
     }

     function set_all() {
     	var old = document.getElementById('txt_recipe_row_id').value;
     	if (old != "") {
     		old = old.split(",");
     		for (var k = 0; k < old.length; k++) {
     			js_set_value(old[k])
     		}
     	}
     }

     function js_set_value(str) {
     	var currsubprocess_id = $('#subprocess_id' + str).val();
     	var currseq_no = $('#seq_no' + str).val();
     	var booking_type = $('#booking_type' + str).val();
     	var entry_form = $('#entry_form' + str).val();

     	if (prevsubprocess_id == '' || selected_id.length == 0) {
     		prevsubprocess_id = $('#subprocess_id' + str).val();
     		prevseq_no = $('#seq_no' + str).val();
     		prevbooking_type = $('#booking_type' + str).val();
     		preventry_form = $('#entry_form' + str).val();
     	}
     	else {
     		if (currsubprocess_id != prevsubprocess_id || currseq_no != prevseq_no || booking_type != prevbooking_type || entry_form != preventry_form) {
     			alert("Item and Sub Process of Selected Recipe Not Uniformed. And With Order and Without Order Mix Not Allowed");
     			return;
     		}
     	}

     	toggle(document.getElementById('search' + str), '#FFFFCC');

     	if (jQuery.inArray($('#recipe_id' + str).val(), selected_id) == -1) {
     		selected_id.push($('#recipe_id' + str).val());
     	}
     	else {
     		for (var i = 0; i < selected_id.length; i++) {
     			if (selected_id[i] == $('#recipe_id' + str).val()) break;
     		}
     		selected_id.splice(i, 1);
     	}

     	var id = '';
     	for (var i = 0; i < selected_id.length; i++) {
     		id += selected_id[i] + ',';
     	}

     	id = id.substr(0, id.length - 1);

     	$('#hidden_recipe_id').val(id);
     	$('#hidden_subprocess_id').val(currsubprocess_id);
     }
 </script>
</head>

<body>
	<div align="center" style="width:1035px;">
		<form name="searchlabdipfrm" id="searchlabdipfrm">
			<fieldset style="width:1030px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="880" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Recipe Date Range</th>
						<th>Search By</th>
						<th width="250" id="search_by_td_up">Enter Recipe System No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_recipe_id" id="hidden_recipe_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_subprocess_id" id="hidden_subprocess_id" class="text_boxes"
							value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to"
							class="datepicker" style="width:70px;">
						</td>
						<td>
							<?
							$search_by_arr = array(1 => "Recipe System No", 2 => "Labdip No", 3 => "Batch No");//,3=>"Recipe Description"
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../') ";
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+'<? echo $recipe_id; ?>', 'create_recipe_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'set_all();setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px;" id="search_div" align="left"></div>
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
	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$recipe_id = $data[5];

	$batch_arr = array();
	$batchData = sql_select("select id, batch_no, booking_without_order, extention_no, entry_form from pro_batch_create_mst");
	foreach ($batchData as $batchRow) 
	{
		if ($batchRow[csf('entry_form')] == 36) $entry_form = 0; else $entry_form = $batchRow[csf('entry_form')];

		$batch_arr[$batchRow[csf('id')]]['no'] = $batchRow[csf('batch_no')];
		$batch_arr[$batchRow[csf('id')]]['bwo'] = $batchRow[csf('booking_without_order')];
		$batch_arr[$batchRow[csf('id')]]['ex'] = $batchRow[csf('extention_no')];
		$batch_arr[$batchRow[csf('id')]]['ef'] = $entry_form;
	}

	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} 
		else 
		{
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		}
	} 
	else 
	{
		$date_cond = "";
	}
	if (trim($data[0]) != "") 
	{
		if ($search_by == 1)
			$search_field_cond = "and a.id like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and a.labdip_no like '" . $search_string . "%'";
		else if ($search_by == 3)
			$search_field_cond = "and c.batch_no like '" . $search_string . "%'";
	} 
	else 
	{
		$search_field_cond = "";
	}

	$po_arr = sql_select("select b.id,c.mst_id as batch_id,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where a.job_no=b.job_no_mst and b.id=c.po_id and a.company_name=$company_id and b.status_active=1 and b.is_deleted=0");
	$file_ref_array = array();
	$po_array = array();
	foreach ($po_arr as $row) 
	{
		$file_ref_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$file_ref_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('batch_id')]]['po'] .= $row[csf('id')] . ",";
	}

	if ($db_type == 0) 
	{
		$process_id_arr = return_library_array("select mst_id,group_concat(sub_process_id order by id) as sub_process_id from pro_recipe_entry_dtls group by mst_id", "mst_id", "sub_process_id");

		$sql = "select a.id, a.labdip_no, a.recipe_date, a.order_source, a.style_or_order, a.batch_id, a.color_id, a.color_range, sum(b.total_liquor) as total_liquor, group_concat(b.sub_process_id order by b.id) as sub_process_id, group_concat(concat_ws('**',b.sub_process_id,b.prod_id,b.seq_no) order by b.id) as seq_no from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, pro_batch_create_mst c where a.id=b.mst_id  and a.entry_form=151 and a.batch_id=c.id and a.company_id='$company_id' and b.ratio>0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond group by a.id";
	} 
	else 
	{
		$process_id_arr = return_library_array("select mst_id, LISTAGG(sub_process_id, ',') WITHIN GROUP (ORDER BY id) as sub_process_id from pro_recipe_entry_dtls   group by mst_id", "mst_id", "sub_process_id");

		$sql = "select a.id, a.labdip_no, a.recipe_date, a.order_source, a.style_or_order, a.batch_id, a.color_id, a.color_range, sum(distinct b.total_liquor) as total_liquor, LISTAGG(b.sub_process_id, ',') WITHIN GROUP (ORDER BY b.id) as sub_process_id, LISTAGG(b.sub_process_id || '**' || b.prod_id || '**' || b.seq_no, ',') WITHIN GROUP (ORDER BY b.id) as seq_no from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, pro_batch_create_mst c where a.id=b.mst_id and a.entry_form=151 and a.batch_id=c.id and a.company_id='$company_id' and b.ratio>0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond group by a.id, a.labdip_no, a.recipe_date, a.order_source, a.style_or_order, a.batch_id, a.color_id, a.color_range";

	}
	//echo $sql;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1025" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="60">Recipe No</th>
			<th width="60">Labdip No</th>
			<th width="70">Recipe Date</th>
			<th width="160">Sub Process</th>
			<th width="80">Batch No</th>
			<th width="50">Ext. No</th>
			<th width="200">Color</th>
			<th width="150">Color Range</th>
			<th>Total Liquor</th>
		</thead>
	</table>
	<div style="width:1025px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1007" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$recipe_row_id = '';
		$hidden_recipe_id = explode(",", $recipe_id);
		$nameArray = sql_select($sql);
		foreach ($nameArray as $selectResult) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$subprocess = '';
			$sub_process_id = array_unique(explode(",", $selectResult[csf('sub_process_id')]));
			foreach ($sub_process_id as $process_id) 
			{
				$subprocess .= $dyeing_sub_process[$process_id] . ",";
			}
			$po_id = array_unique(explode(",", $po_array[$selectResult[csf('batch_id')]]['po']));
			$file_no = '';
			$ref_no = '';
			foreach ($po_id as $pid) 
			{
				if ($file_no == '') $file_no = $file_ref_array[$pid]['file']; else $file_no .= "," . $file_ref_array[$pid]['file'];
				if ($ref_no == '') $ref_no = $file_ref_array[$pid]['ref']; else $ref_no .= "," . $file_ref_array[$pid]['ref'];
			}

			$seq_no = array_unique(explode(",", $selectResult[csf('seq_no')]));

			if (in_array($selectResult[csf('id')], $hidden_recipe_id)) 
			{
				if ($recipe_row_id == "") $recipe_row_id = $i; else $recipe_row_id .= "," . $i;
			}

			$sub_process_id = array_unique(explode(",", $process_id_arr[$selectResult[csf('id')]]));
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
				<td width="35" align="center"><? echo $i; ?>
					<input type="hidden" name="recipe_id" id="recipe_id<? echo $i; ?>"
					value="<? echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="subprocess_id" id="subprocess_id<? echo $i; ?>"
					value="<? echo implode(",", $sub_process_id); ?>"/>
					<input type="hidden" name="seq_no" id="seq_no<? echo $i; ?>"
					value="<? echo implode(",", $seq_no); ?>"/>
					<input type="hidden" name="booking_type" id="booking_type<? echo $i; ?>"
					value="<? echo $batch_arr[$selectResult[csf('batch_id')]]['bwo']; ?>"/>
					<input type="hidden" name="entry_form" id="entry_form<? echo $i; ?>"
					value="<? echo $batch_arr[$selectResult[csf('batch_id')]]['ef']; ?>"/>
				</td>
				<td width="60"><p><? echo $selectResult[csf('id')]; ?></p></td>
				<td width="60"><p><? echo $selectResult[csf('labdip_no')]; ?></p></td>
				<td width="70" align="center"><? echo change_date_format($selectResult[csf('recipe_date')]); ?>
					&nbsp;</td>
					<td width="160"><p><? echo substr($subprocess, 0, -1); ?></p></td>
					<td width="80"><p><? echo $batch_arr[$selectResult[csf('batch_id')]]['no']; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $batch_arr[$selectResult[csf('batch_id')]]['ex']; ?>&nbsp;</p></td>
					<td width="200"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?>&nbsp;</p></td>
					<td width="150"><p><? echo $color_range[$selectResult[csf('color_range')]]; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($selectResult[csf('total_liquor')], 2, '.', ''); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="950">
		<tr>
			<td align="center">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close"
				onClick="parent.emailwindow.hide();" style="width:100px"/>
				<input type="hidden" name="txt_recipe_row_id" id="txt_recipe_row_id"
				value="<?php echo $recipe_row_id; ?>"/>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == 'get_subprocess_id') 
{
	$sub_process_id = '';
	if ($db_type == 0) 
	{
		$sql = "select a.id, group_concat(b.sub_process_id order by b.id) as sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id in($data) group by a.id";
	} 
	else 
	{
		$sql = "select a.id, LISTAGG(b.sub_process_id, ',') WITHIN GROUP (ORDER BY b.id) as sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.id in($data) group by a.id";
	}
	$selectResult = sql_select($sql);
	$sub_process_id = implode(",", array_unique(explode(",", $selectResult[0][csf('sub_process_id')])));
	echo $sub_process_id;
	exit();
}


if ($action == 'populate_data_from_recipe_popup') 
{
	/*if($db_type==0)
	{
		$data_array=sql_select("select group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data)");
	}
	else
	{
		$data_array=sql_select("select listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data)");
	}*/
	$recipe_id = '';
	$total_liquor = 0;
	$batch_new_qty = 0;
	$batch_id = '';
	$all_batch_id = '';
	$data_array = sql_select("select id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in($data)");
	foreach ($data_array as $row) {
		$total_liquor += $row[csf("total_liquor")];
		if ($row[csf("entry_form")] == 60) {
			$batch_new_qty += $row[csf("new_batch_weight")];
		} else if ($row[csf("entry_form")] == 151) //New Add from Recipe page
		{
			$batch_new_qty += $row[csf("batch_qty")];
		} else {
			$batch_id .= $row[csf("batch_id")] . ",";
		}
		$all_batch_id .= $row[csf("batch_id")] . ",";
		$recipe_id .= $row[csf("id")] . ",";


	}
	//$batch_id=implode(",",array_unique(explode(",",$data_array[0][csf("batch_id")])));
	$recipe_id = chop($recipe_id, ',');
	$batch_id = chop($batch_id, ',');
	$all_batch_id = chop($all_batch_id, ',');
	if ($batch_id == "") $batch_id = 0;
	if ($db_type == 0) 
	{
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($all_batch_id)");
	} 
	else 
	{
		$batchdata_array = sql_select("select listagg(CAST(batch_no  AS VARCHAR2(4000)),',') within group (order by id) as batch_no, sum(case when id in($batch_id) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($all_batch_id)");
	}
	$batch_weight += $batchdata_array[0][csf("batch_weight")];
	echo "document.getElementById('txt_recipe_id').value 				= '" . $recipe_id . "';\n";
	echo "document.getElementById('txt_recipe_no').value 				= '" . $recipe_id . "';\n";
	echo "document.getElementById('txt_batch_no').value 				= '" . $batchdata_array[0][csf("batch_no")] . "';\n";
	echo "document.getElementById('txt_batch_id').value 				= '" . $all_batch_id . "';\n";
	echo "document.getElementById('txt_tot_liquor').value 				= '" . $total_liquor . "';\n";
	//echo "document.getElementById('txt_batch_weight').value 			= '".$batch_weight."';\n";
	echo "document.getElementById('txt_batch_weight').value 			= '" . $batch_new_qty . "';\n";

	exit();
}

if ($action == "item_details") 
{
	$data = explode("**", $data);
	$company_id = $data[0];
	$sub_process_id = trim($data[1]);
	$recipe_id = $data[2];
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1105" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="83">Sub Process</th>
			<th width="50">Prod. ID</th>
			<th width="80">Item Category</th>
			<th width="90">Group</th>
			<th width="80">Sub Group</th>
			<th width="110">Item Description</th>
			<th width="40">UOM</th>
			<th width="50">Seq. No.</th>
			<th width="75">Dose Base</th>
			<th width="72">Ratio</th>
			<th width="80">Recipe Qnty.</th>
			<th width="53">Adj%.</th>
			<th width="87">Adj. Type</th>
			<th>Reqn. Qnty.</th>
		</thead>
	</table>
	<div style="width:1105px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1088" class="rpt_table"
		id="tbl_list_search">
		<tbody>
			<?
			$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
			//$batchWeight_arr = return_library_array("select id, batch_weight from pro_batch_create_mst", "id", "batch_weight");

			//new dev
			$recId = array();
			$getEntryFrom = sql_select("select entry_form from pro_recipe_entry_mst where id in($recipe_id)");
			foreach ($getEntryFrom as $dataEntryF) {
				$recId = $dataEntryF;
			}
			$uniqEntrF = array_unique($recId);

			if (in_array(151, $uniqEntrF)) 
			{
				$sql = "select p.total_liquor, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b,pro_batch_create_mst c  where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0  order by b.sub_process_id, b.seq_no";
			} 
			else 
			{
				$sql = "select p.total_liquor, p.batch_id, p.entry_form,p.batch_qty,c.batch_no,a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b,pro_batch_create_mst c where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7)  and a.status_active=1 and a.is_deleted=0 and b.is_checked=1 order by b.sub_process_id, b.seq_no";
			}

			//echo $sql;

			$i = 1;
			$subprocessDataArr = array();
			$subprocessProdQntyArr = array();
			$prodDataArr = array();
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) 
			{
				$subprocessDataArr[$selectResult[csf('sub_process_id')]] .= $selectResult[csf('id')] . ",";
				$prodDataArr[$selectResult[csf('id')]] = $selectResult[csf('item_category_id')] . "**" . $selectResult[csf('item_group_id')] . "**" . $selectResult[csf('sub_group_name')] . "**" . $selectResult[csf('item_description')] . "**" . $selectResult[csf('item_size')] . "**" . $selectResult[csf('unit_of_measure')];
				$ratio = $selectResult[csf('ratio')];
				if ($selectResult[csf('dose_base')] == 1) 
				{
					if ($selectResult[csf('entry_form')] == 60) {
						//$perc_calculate_qnty=$selectResult[csf('new_total_liquor')];
						$perc_calculate_qnty = $selectResult[csf('total_liquor_dtls')];

					} else {
						$perc_calculate_qnty = $selectResult[csf('total_liquor_dtls')];
					}
					$recipe_qnty = ($perc_calculate_qnty * $ratio) / 1000;
				} 
				else if ($selectResult[csf('dose_base')] == 2) 
				{
					if ($selectResult[csf('entry_form')] == 60) {
						$perc_calculate_qnty = $selectResult[csf('new_batch_weight')];
					} else if ($selectResult[csf('entry_form')] == 151) {
						$perc_calculate_qnty = $selectResult[csf('batch_qty')];
					} else {
						$perc_calculate_qnty = $selectResult[csf('batch_no')];
					}
					$recipe_qnty = ($perc_calculate_qnty * $ratio) / 100;
				}

				$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]]['dosebase'] .= $selectResult[csf('dose_base')] . ",";
				$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]]['recipe_qnty'] += $recipe_qnty;
				$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]]['lq_or_bw_qnty'] += $perc_calculate_qnty;
				$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]]['seq_no'] = $selectResult[csf('seq_no')];
				$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]]['total_liquor_dtls'] = $selectResult[csf('total_liquor_dtls')];
			}
			//echo $sub_process_id;
			$sub_process_id = explode(",", $sub_process_id);
			$dosebase_mismatch_prod_id_arr = array();
			foreach ($sub_process_id as $process_id) 
			{
				$subprocessData = array_unique(explode(",", substr($subprocessDataArr[$process_id], 0, -1)));
				foreach ($subprocessData as $prod_id) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					$subprocessData = explode("**", $prodDataArr[$prod_id]);
					$item_category_id = $subprocessData[0];
					$item_group_id = $subprocessData[1];
					$sub_group_name = $subprocessData[2];
					$item_description = $subprocessData[3] . " " . $subprocessData[4];
					$unit_of_measure = $subprocessData[5];
					$seq_no = $subprocessProdQntyArr[$process_id][$prod_id]['seq_no'];

					$dosebaseData = array_unique(explode(",", substr($subprocessProdQntyArr[$process_id][$prod_id]['dosebase'], 0, -1)));
					if (count($dosebaseData) > 1) 
					{
						$dosebase = 0;
						$ratio = 0;
						$recipe_qnty = 0;
						$dosebase_mismatch_prod_id[$process_id] .= $prod_id . ",";
						//echo "KKF";
					} 
					else 
					{
						$dosebase = implode(",", $dosebaseData);
						$recipe_qnty = number_format($subprocessProdQntyArr[$process_id][$prod_id]['recipe_qnty'], 6, '.', '');
						$lq_or_bw_qnty = $subprocessProdQntyArr[$process_id][$prod_id]['lq_or_bw_qnty'];
						if ($dosebase == 1) {
							$ratio = number_format(($recipe_qnty * 1000) / $lq_or_bw_qnty, 6, '.', '');

						} else {
							$ratio = number_format(($recipe_qnty * 100) / $lq_or_bw_qnty, 6, '.', '');

						}
					}
					if ($ratio == 'nan' || $ratio == '') $ratio = 0;
					$total_liquor_dtls = $subprocessProdQntyArr[$process_id][$prod_id]['total_liquor_dtls'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle">
						<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
						<td width="83" id="subprocess_id_<? echo $i; ?>"><? echo $dyeing_sub_process[$process_id]; ?>
							<input type="hidden" name="txt_subprocess_id[]" id="txt_subprocess_id_<? echo $i; ?>"
							class="text_boxes_numeric" value="<? echo $process_id; ?>">
						</td>
						<td width="50" id="product_id_<? echo $i; ?>"><? echo $prod_id; ?>
							<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
							class="text_boxes_numeric" style="width:38px" value="<? echo $prod_id; ?>">
						</td>
						<td width="80"><p><? echo $item_category[$item_category_id]; ?></p>
							<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>"
							class="text_boxes_numeric" style="width:38px" value="<? echo $item_category_id; ?>">
						</td>
						<td width="90" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?>
							&nbsp;</p></td>
							<td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
							<td width="110" id="item_description_<? echo $i; ?>"><p><? echo $item_description; ?></p></td>
							<td width="40" align="center"
							id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?></td>
							<td width="50" align="center" id="seq_no_<? echo $i; ?>"><? echo $seq_no; ?></td>
							<td width="75" align="center"
							id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -", $dosebase, "", 1); ?></td>
							<td width="72" align="center" title="<? echo 'Total Liquor ' . $total_liquor_dtls; ?>"
								id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]"
								id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:60px" value="<? echo $ratio; ?>" disabled>
							</td>
							<td width="80" align="center" id="recipe_qnty_<? echo $i; ?>">
								<input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $recipe_qnty; ?>" disabled>
							</td>
							<td width="53" align="center" id="adj_per_<? echo $i; ?>">
								<input type="text"
								name="txt_adj_per[]"
								id="txt_adj_per_<? echo $i; ?>"
								class="text_boxes_numeric"
								style="width:40px" value=""
								onKeyUp="calculate_requs_qty(<? echo $i; ?>)">
							</td>
							<td width="87" align="center"
							id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -", "", "calculate_requs_qty($i)"); ?></td>
							<td align="center" id="reqn_qnty_<? echo $i; ?>">
								<input type="text" name="reqn_qnty_edit[]" id="reqn_qnty_edit_<? echo $i; ?>"
								class="text_boxes_numeric" value="<? echo $recipe_qnty; ?>" style="width:75px"
								disabled>
								<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="">
								<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>"
								class="text_boxes_numeric" value="<? echo $recipe_qnty; ?>">
								<input type="hidden" name="txt_seq_no[]" id="txt_seq_no_<? echo $i; ?>"
								class="text_boxes_numeric" value="<? echo $seq_no; ?>">
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
	<div>
	</div>
	<?
	exit();
}

if ($action == "item_details_for_update") 
{
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1105" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="83">Sub Process</th>
			<th width="50">Prod. ID</th>
			<th width="80">Item Category</th>
			<th width="90">Group</th>
			<th width="80">Sub Group</th>
			<th width="110">Item Description</th>
			<th width="40">UOM</th>
			<th width="50">Seq. No.</th>
			<th width="75">Dose Base</th>
			<th width="72">Ratio</th>
			<th width="80">Recipe Qnty.</th>
			<th width="53">Adj%.</th>
			<th width="87">Adj. Type</th>
			<th>Reqn. Qnty.</th>
		</thead>
	</table>
	<div style="width:1105px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1088" class="rpt_table"
		id="tbl_list_search">
		<tbody>
			<?php
			//ini_set('display_errors', 1);
			$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
			/*$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id,b.sub_process, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, b.seq_no,b.recipe_id from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id=$data and b.status_active=1 and b.is_deleted=0 and a.item_category_id in(5,6,7)  and a.status_active=1 and a.is_deleted=0)
			union
			(
			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size, null as unit_of_measure, b.id as dtls_id, b.sub_process, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, b.seq_no,b.recipe_id  from dyes_chem_issue_requ_dtls b where b.sub_process in(93,94,95,96,97,98) and b.mst_id=$data and b.status_active=1 and b.is_deleted=0 
			) order by sub_process";*/

			$sql = "select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id,b.sub_process, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, b.seq_no,b.recipe_id from product_details_master a, dyes_chem_issue_requ_dtls b where a.id=b.product_id and b.mst_id=$data and b.status_active=1 and b.is_deleted=0 and a.item_category_id in(5,6,7)  and a.status_active=1 and a.is_deleted=0 order by sub_process";

			$liqour_ratio_rec_arr = array();

			$i = 1;
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$total_liqour = $liqour_ratio_rec_arr[$selectResult[csf('recipe_id')]][$selectResult[csf('sub_process')]]['total_ratio'];

				$dosebase = $selectResult[csf('dose_base')];
				$recipe_qnty = $selectResult[csf('recipe_qnty')];
				$ratio = $selectResult[csf('ratio')];
				if ($ratio == 'nan' || $ratio == '') $ratio = 0;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle">
					<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
					<td width="83"
					id="subprocess_id_<? echo $i; ?>"><? echo $dyeing_sub_process[$selectResult[csf('sub_process')]]; ?>
					<input type="hidden" name="txt_subprocess_id[]" id="txt_subprocess_id_<? echo $i; ?>"
					class="text_boxes_numeric" value="<? echo $selectResult[csf('sub_process')]; ?>">
				</td>
				<td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
					<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
					class="text_boxes_numeric" style="width:38px"
					value="<? echo $selectResult[csf('id')]; ?>">
				</td>
				<td width="80"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
					<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>"
					class="text_boxes_numeric" style="width:38px"
					value="<? echo $selectResult[csf('item_category_id')]; ?>">
				</td>
				<td width="90" id="item_group_id_<? echo $i; ?>">
					<p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?>&nbsp;</p></td>
					<td width="80" id="sub_group_name_<? echo $i; ?>">
						<p><? echo $selectResult[csf('sub_group_name')]; ?>&nbsp;</p></td>
						<td width="110" id="item_description_<? echo $i; ?>">
							<p><? echo $selectResult[csf('item_description')] . " " . $selectResult[csf('item_size')]; ?></p>
						</td>
						<td width="40" align="center"
						id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
						<td width="50" align="center"
						id="seq_no_<? echo $i; ?>"><? echo $selectResult[csf('seq_no')]; ?></td>
						<td width="75" align="center"
						id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -", $dosebase, "", 1); ?></td>
						<td width="72" align="center" title="<? echo 'Total Liqour ' . $total_liqour; ?>"
							id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>"
							class="text_boxes_numeric" style="width:60px"
							value="<? echo number_format($ratio, 6, '.', ''); ?>" disabled>
						</td>
						<td width="80" align="center" id="recipe_qnty_<? echo $i; ?>"><input type="text"
							name="txt_recipe_qnty[]"
							id="txt_recipe_qnty_<? echo $i; ?>"
							class="text_boxes_numeric"
							style="width:67px"
							value="<? echo number_format($recipe_qnty, 6, '.', ''); ?>"
							disabled></td>
							<td width="53" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]"
								id="txt_adj_per_<? echo $i; ?>"
								class="text_boxes_numeric"
								style="width:40px"
								value="<? echo $selectResult[csf('adjust_percent')]; ?>"
								onKeyUp="calculate_requs_qty(<? echo $i; ?>)">
							</td>
							<td width="87" align="center"
							id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')], "calculate_requs_qty($i)"); ?></td>
							<td align="center" id="reqn_qnty_<? echo $i; ?>">
								<input type="text" name="reqn_qnty_edit[]" id="reqn_qnty_edit_<? echo $i; ?>"
								class="text_boxes_numeric"
								value="<? echo number_format($selectResult[csf('req_qny_edit')], 6, '.', ''); ?>"
								style="width:75px" disabled>
								<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>"
								value="<? echo $selectResult[csf('dtls_id')]; ?>">
								<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>"
								class="text_boxes_numeric" value="<? echo $selectResult[csf('required_qnty')]; ?>">
								<input type="hidden" name="txt_seq_no[]" id="txt_seq_no_<? echo $i; ?>"
								class="text_boxes_numeric" value="<? echo $selectResult[csf('seq_no')]; ?>">
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
		<?
		exit();
	}

if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$mst_id = "";
		$requ_no = "";

		if (str_replace("'", "", $update_id) == "") 
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";//defined Later

			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'DCIR', date("Y", time()), 5, "select requ_no_prefix,requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and requisition_basis=8 and entry_form in(156,157)  and $year_cond=" . date('Y', time()) . " order by id desc ", "requ_no_prefix", "requ_prefix_num"));
			$id = return_next_id("id", "dyes_chem_issue_requ_mst", 1);
			$field_array = "id,requ_no,requ_no_prefix,requ_prefix_num,company_id,location_id,requisition_date,requisition_basis,batch_id,recipe_id,method,machine_id,inserted_by,insert_date,entry_form";
			$data_array = "(" . $id . ",'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . "," . $cbo_company_name . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $cbo_receive_basis . "," . $txt_batch_id . "," . $txt_recipe_id . "," . $cbo_method . "," . $machine_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',157)";

			$mst_id = $id;
			$requ_no = $new_requ_no[0];
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*recipe_id*batch_id*method*machine_id*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $txt_recipe_id . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $machine_id . "*" . $user_id . "*'" . $pc_date_time . "'";
			$mst_id = str_replace("'", "", $update_id);
			$requ_no = str_replace("'", "", $txt_mrr_no);
		}

		$id_att = return_next_id("id", "dyes_chem_requ_recipe_att", 1);
		$field_array_att = "id,mst_id,recipe_id";
		$recipe_id_all = explode(",", str_replace("'", "", $txt_recipe_id));
		foreach ($recipe_id_all as $recipe_id) 
		{
			if ($data_array_att != "") $data_array_att .= ",";
			$data_array_att .= "(" . $id_att . "," . $mst_id . "," . $recipe_id . ")";
			$id_att = $id_att + 1;
		}

		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		$field_array_dtls = "id,mst_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,ratio,dose_base, recipe_qnty, adjust_percent, adjust_type, required_qnty,req_qny_edit,seq_no,inserted_by,insert_date";
		for ($i = 1; $i <= $total_row; $i++) 
		{
			$txt_prod_id = "txt_prod_id_" . $i;
			$txt_item_cat = "txt_item_cat_" . $i;
			$cbo_dose_base = "cbo_dose_base_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$txt_recipe_qnty = "txt_recipe_qnty_" . $i;
			$txt_adj_per = "txt_adj_per_" . $i;
			$cbo_adj_type = "cbo_adj_type_" . $i;
			$txt_reqn_qnty = "txt_reqn_qnty_" . $i;
			$txt_reqn_qnty_edit = "reqn_qnty_edit_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$txt_subprocess_id = "txt_subprocess_id_" . $i;
			$txt_seq_no = "txt_seq_no_" . $i;
			$txt_ratio = str_replace("'", "", $$txt_ratio);

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $mst_id . ",'" . $requ_no . "'," . $txt_batch_id . "," . $txt_recipe_id . "," . $cbo_receive_basis . "," . $$txt_subprocess_id . "," . $$txt_prod_id . "," . $$txt_item_cat . ",'" . $txt_ratio . "'," . $$cbo_dose_base . "," . $$txt_recipe_qnty . "," . $$txt_adj_per . "," . $$cbo_adj_type . "," . $$txt_reqn_qnty . "," . $$txt_reqn_qnty_edit . "," . $$txt_seq_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$id_dtls = $id_dtls + 1;
		}

		if (str_replace("'", "", $update_id) == "") 
		{
			//echo  "INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."";
			$rID = sql_insert("dyes_chem_issue_requ_mst", $field_array, $data_array, 1);
		} 
		else 
		{
			$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 1);
		}

		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 1);
		//echo  "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 1);
		//echo "10**".($rID ."&&". $rID_att ."&&". $rID_dtls);die;
		//check_table_status( $_SESSION['menu_id'],0);
		if ($db_type == 0) 
		{
			if ($rID && $rID_att && $rID_dtls) 
			{
				mysql_query("COMMIT");
				echo "0**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID && $rID_att && $rID_dtls) 
			{
				oci_commit($con);
				echo "0**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1)  // Update Here
	{
		$con = connect();
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$last_update_arr = return_library_array("select recipe_id,is_apply_last_update from dyes_chem_requ_recipe_att where mst_id=$update_id", "recipe_id", "is_apply_last_update");
		$is_apply_last_update = str_replace("'", "", $is_apply_last_update);

		if ($is_apply_last_update == 1) 
		{
			$field_array_up = "location_id*requisition_date*recipe_id*batch_id*method*machine_id*is_apply_last_update*updated_by*update_date";
			$data_array = $cbo_location_name . "*" . $txt_requisition_date . "*" . $txt_recipe_id . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $machine_id . "*0*" . $user_id . "*'" . $pc_date_time . "'";
		} 
		else 
		{
			$field_array_up = "location_id*requisition_date*recipe_id*batch_id*method*machine_id*updated_by*update_date";
			$data_array = $cbo_location_name . "*" . $txt_requisition_date . "*" . $txt_recipe_id . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $machine_id . "*" . $user_id . "*'" . $pc_date_time . "'";
		}

		$mst_id = str_replace("'", "", $update_id);
		$requ_no = str_replace("'", "", $txt_mrr_no);

		$id_att = return_next_id("id", "dyes_chem_requ_recipe_att", 1);
		$field_array_att = "id,mst_id,recipe_id,is_apply_last_update";
		$recipe_id_all = explode(",", str_replace("'", "", $txt_recipe_id));
		foreach ($recipe_id_all as $recipe_id) 
		{
			if ($is_apply_last_update == 1) 
			{
				$apply_last_update = 0;
			} 
			else 
			{
				$apply_last_update = $last_update_arr[$recipe_id];
			}

			if ($data_array_att != "") $data_array_att .= ",";
			$data_array_att .= "(" . $id_att . "," . $mst_id . "," . $recipe_id . "," . $apply_last_update . ")";
			$id_att = $id_att + 1;
		}

		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		$field_array_dtls = "id,mst_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,dose_base,ratio, recipe_qnty, adjust_percent, adjust_type, required_qnty,req_qny_edit,seq_no,inserted_by,insert_date";

		for ($i = 1; $i <= $total_row; $i++) 
		{
			$txt_prod_id = "txt_prod_id_" . $i;
			$txt_item_cat = "txt_item_cat_" . $i;
			$cbo_dose_base = "cbo_dose_base_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$txt_recipe_qnty = "txt_recipe_qnty_" . $i;
			$txt_adj_per = "txt_adj_per_" . $i;
			$cbo_adj_type = "cbo_adj_type_" . $i;
			$txt_reqn_qnty = "txt_reqn_qnty_" . $i;
			$txt_reqn_qnty_edit = "reqn_qnty_edit_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$txt_subprocess_id = "txt_subprocess_id_" . $i;
			$txt_seq_no = "txt_seq_no_" . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $mst_id . ",'" . $requ_no . "'," . $txt_batch_id . "," . $txt_recipe_id . "," . $cbo_receive_basis . "," . $$txt_subprocess_id . "," . $$txt_prod_id . "," . $$txt_item_cat . "," . $$cbo_dose_base . "," . $$txt_ratio . "," . $$txt_recipe_qnty . "," . $$txt_adj_per . "," . $$cbo_adj_type . "," . $$txt_reqn_qnty . "," . $$txt_reqn_qnty_edit . "," . $$txt_seq_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$id_dtls = $id_dtls + 1;
		}

		$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array, "id", $update_id, 1);
		$delete_att = execute_query("delete from dyes_chem_requ_recipe_att where mst_id=$update_id", 0);
		$delete_dtls = execute_query("delete from dyes_chem_issue_requ_dtls where mst_id=$update_id", 0);
		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 1);

		$rID_dtls = true;
		if ($data_array_dtls != "") 
		{
			//echo "INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";
			$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 1);
		}

		if ($db_type == 0) 
		{
			if ($rID && $rID_att && $rID_dtls && $delete_att && $delete_dtls) 
			{
				mysql_query("COMMIT");
				echo "1**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID && $rID_att && $rID_dtls && $delete_att && $delete_dtls) 
			{
				oci_commit($con);
				echo "1**" . $requ_no . "**" . $mst_id;
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "chemical_dyes_issue_requisition_print") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);
	$company=$data[0];
	$location=$data[3];

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$batch_weight = 0;
	if ($db_type == 0) 
	{
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} 
	else 
	{
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}


	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}
	
	$roll_no=sql_select("select sum(roll_no) as roll_no from pro_batch_create_dtls where mst_id in ('$batch_id') and is_deleted=0 and status_active=1");

	$recipe_dlts=array();
	$recipe_dtls_arr=sql_select("SELECT a.batch_ratio, b.sub_process_id, b.total_liquor, b.recipe_time, b.recipe_temperature, b.recipe_ph, b.liquor_ratio FROM pro_recipe_entry_mst a, pro_recipe_entry_dtls b WHERE a.id=b.mst_id AND b.mst_id in ('$recipe_id') AND b.status_active=1 AND b.is_deleted=0");
	foreach ($recipe_dtls_arr as $val) 
	{
		$recipe_dlts[$val[csf('sub_process_id')]]['total_liquor']=$val[csf('total_liquor')];
		$recipe_dlts[$val[csf('sub_process_id')]]['recipe_time']=$val[csf('recipe_time')];
		$recipe_dlts[$val[csf('sub_process_id')]]['recipe_temperature']=$val[csf('recipe_temperature')];
		$recipe_dlts[$val[csf('sub_process_id')]]['recipe_ph']=$val[csf('recipe_ph')];
		$recipe_dlts[$val[csf('sub_process_id')]]['liquor_ratio']=$val[csf('liquor_ratio')];
		$recipe_dlts[$val[csf('sub_process_id')]]['batch_ratio']=$val[csf('batch_ratio')];
	}

	$po_no = '';
	$job_no = '';
	$buyer_name = '';
	$style_ref_no = '';
	$cust_buyer='';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) 
	{
		if ($entry_form_arr[$b_id] == (36||150)) 
		{
			$po_data = sql_select("select distinct b.order_no,b.cust_style_ref, b.cust_buyer, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) 
			{
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('cust_style_ref')]; else $style_ref_no .= "," . $row[csf('cust_style_ref')];
				if ($cust_buyer == '') $cust_buyer = $row[csf('cust_buyer')]; else $cust_buyer .= "," . $row[csf('cust_buyer')];
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} 
		else 
		{
			$po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) 
			{
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}
		}
	}

	if ($db_type == 0) 
	{
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} 
	else if ($db_type == 2) 
	{
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}


	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1250px;">
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="10" align="center" style="font-size:xx-large">
					<strong><? echo $com_dtls[0]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?
							echo $com_dtls[1];
						/*$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result['plot_no']; ?>
							Level No: <? echo $result['level_no'] ?>
							Road No: <? echo $result['road_no']; ?>
							Block No: <? echo $result['block_no']; ?>
							City No: <? echo $result['city']; ?>
							Zip Code: <? echo $result['zip_code']; ?>
							Province No: <?php echo $result['province']; ?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email']; ?>
							Website No: <? echo $result['website'];
						}*/
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?>
						Report</u></strong></td>
					</tr>
				</table>
				<table width="950" cellspacing="0" align="center">
					<tr>
						<td width="90"><strong>Req. ID <span style="float: right;">:</span></strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td width="100"><strong>Req. Date <span style="float: right;">:</span></strong></td>
						<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
						<td width="90"><strong>Party Name <span style="float: right;">:</span></strong></td>
						<td width="160px"><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
					</tr>
					<tr>
						<td><strong>Order No <span style="float: right;">:</span></strong></td>
						<td><? echo $po_no; ?></td>
						<td><strong>Style <span style="float: right;">:</span></strong></td>
						<td><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></td>
						<td><strong>Party Buyer <span style="float: right;">:</span></strong></td>
						<td><? echo implode(",", array_unique(explode(",", $cust_buyer))); ?></td>
					</tr>
					<tr>
						<td><strong>Batch No <span style="float: right;">:</span></strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight <span style="float: right;">:</span></strong></td>
						<td><? echo $batch_weight + $batchdata_array[0][csf('batch_weight')]; ?> Kg</td>
						<td><strong>Batch Qty(Pcs) <span style="float: right;">:</span></strong></td>
						<td><? echo $roll_no[0][csf('roll_no')]; ?></td>
					</tr>
					<tr>
						<td><strong>Recipe No <span style="float: right;">:</span></strong></td>
						<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
						<td><strong>Color <span style="float: right;">:</span></strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Issue Basis <span style="float: right;">:</span></strong></td>
						<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Machine No <span style="float: right;">:</span></strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
						<td><strong>Method <span style="float: right;">:</span></strong></td>
						<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
					</tr>
			</table>
			<br>
			<?
			$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
			$j = 1;
			$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
        <div style="width:1250px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1250" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
        			</tr>
        		</thead>
        		<?

	 $group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
	 from pro_recipe_entry_dtls b 
	 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
	 $nameArray = sql_select($sql_rec);
	 foreach ($nameArray as $row) {
	 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
	 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
	 }

	 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

	 $process_array = array();
	 $process_array_remark = array();
	 foreach ($nameArray as $row) {
	 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
	 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
	 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
	 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
	 	}

	 }

	$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
	b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
	from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7) and a.id=$data[1]) order by id";

				
	$sql_result = sql_select($sql_dtls);

	$sub_process_array = array();
	$sub_process_tot_rec_array = array();
	$sub_process_tot_req_array = array();
	$sub_process_tot_value_array = array();

	foreach ($sql_result as $row) {
		$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
		$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
		$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
	}

				//var_dump($sub_process_tot_req_array);
	$i = 1;
	$k = 1;
	$recipe_qnty_sum = 0;
	$req_qny_edit_sum = 0;
	$recipe_qnty = 0;
	$req_qny_edit = 0;
	$req_value_sum = 0;
	$req_value_grand = 0;
	$recipe_qnty_grand = 0;
	$req_qny_edit_grand = 0;

	foreach ($sql_result as $row) {
		if ($i % 2 == 0)
			$bgcolor = "#E9F3FF";
		else
			$bgcolor = "#FFFFFF";
		if (!in_array($row[csf("sub_process")], $sub_process_array)) {
			$sub_process_array[] = $row[csf('sub_process')];
			if ($k != 1) {
				?>
				<tr>
					<td colspan="7" align="right"><strong>Total :</strong></td>
					<td align="right"><?php echo number_format($recipe_qnty_sum, 2, '.', ''); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><?php echo number_format($req_qny_edit_sum, 2, '.', ''); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><?php echo number_format($req_value_sum, 2, '.', ''); ?></td>
				</tr>
				<?
			}
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$req_value_sum = 0;
			$k++;

			?>
			<tr bgcolor="#CCCCCC">
				<th colspan="6" align="left" style="border-right: none;">
					<strong>
						Sub Process Name : <? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$recipe_dlts[$row[csf("sub_process")]]['total_liquor']. ' (ltr), ' . $pro_remark; ?> 
					</strong>
				</th>
				<th colspan="13" style="border-left: none;">
					<strong>Ratio: </strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['batch_ratio'].":".$recipe_dlts[$row[csf("sub_process")]]['liquor_ratio'];  ?>  
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                <strong>Time:</strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['recipe_time']; ?> min
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                <strong>Temp:</strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['recipe_temperature']; ?> &#8451;
	                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                <strong>PH:</strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['recipe_ph']; ?>
				</th>
			</tr>
			<tr bgcolor="#EFEFEF">
				<th width="30">SL</th>
				<th width="80">Item Catg.</th>
				<th width="80">Item Group</th>
				<th width="100">Item Description</th>
				<th width="50">Lot</th>
				<th width="100">Dose Base</th>
				<th width="40">Ratio</th>
				<th width="60">Recipe Qty.</th>
				<th width="50">UOM</th>
				<th width="50">Adj%</th>
				<th width="60">Adj Type</th>
				<th width="60">Adj Qty.</th>
				<th width="80">Req. Qty.</th>
				<th width="100">Comments</th>
				<th width="70">Avg. Rate</th>
				<th width="80">Req. Value</th>
			</tr>
			<?
		}

					$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
					$iss_qnty_kg = $req_qny_edit[0];
					if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

					$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
					$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
					$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
					$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

					//echo "mg ".$req_qny_edit[1]."<br>";
					$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
					$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
					?>
					<tbody>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
                        	<td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];?></td>
                        	<td><strong><? echo $group_arr[$row[csf("item_group_id")]]; ?><strong</td>
                        	<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                        	<td><? echo chop($item_lot_arr[$row[csf("prod_id")]], ","); ?></td>
                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                        	<td align="center"><? echo number_format($row[csf("ratio")], 2, '.', ''); ?></td>
                        	<td align="right"><strong><? echo number_format($row[csf("recipe_qnty")], 2, '.', ''); ?><strong></td>
                        	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                			<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                			<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                			<td align="right"><? echo number_format($adjQty, 2, '.', ''); ?></td>
                			<td align="right"><strong><? echo number_format($row[csf("req_qny_edit")], 2, '.', ''); ?><strong></td>
        					<td align="right"><? echo $comment; ?></td>
        					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")], 2, '.', ''); ?></td>
        					<td align="right"><? $req_value = $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
        						echo number_format($req_value, 2, '.', ''); ?></td>
                        </tr>
                    </tbody>
                        				<? $i++;
                        				$recipe_qnty_sum += $row[csf('recipe_qnty')];
                        				$req_qny_edit_sum += $row[csf('req_qny_edit')];
                        				$req_value_sum += $req_value;

                        				$recipe_qnty_grand += $row[csf('recipe_qnty')];
                        				$req_qny_edit_grand += $row[csf('req_qny_edit')];
                        				$req_value_grand += $req_value;
                        			}
                        			foreach ($sub_process_tot_rec_array as $val_rec) {
                        				$totval_rec = $val_rec;
                        			}
                        			foreach ($sub_process_tot_req_array as $val_req) {
                        				$totval_req = $val_req;
                        			}
                        			foreach ($sub_process_tot_value_array as $req_value) {
                        				$tot_req_value = $req_value;
                        			}

                        			?>
                        			<tr>
                        				<td colspan="7" align="right"><strong>Total :</strong></td>
                        				<td align="right"><?php echo number_format($totval_rec, 2, '.', ''); ?></td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td align="right"><?php echo number_format($totval_req, 2, '.', ''); ?></td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td align="right"><?php echo number_format($tot_req_value, 2, '.', ''); ?></td>
                        			</tr>
                        			<tr>
                        				<td colspan="7" align="right"><strong> Grand Total :</strong></td>
                        				<td align="right"><?php echo number_format($recipe_qnty_grand, 2, '.', ''); ?></td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td align="right"><?php echo number_format($req_qny_edit_grand, 2, '.', ''); ?></td>
                        				<td>&nbsp;</td>
                        				<td>&nbsp;</td>
                        				<td align="right"><?php echo number_format($req_value_grand, 2, '.', ''); ?></td>
                        			</tr>
                        			<tr>
                        				<td colspan="14" align="right"><strong> Cost Per Kg :</strong></td>
                        				<td colspan="2"
                        				align="right"><?php echo number_format($req_value_grand / ($batch_weight + $batchdata_array[0][csf('batch_weight')]), 2, '.', ''); ?></td>
                        			</tr>
                        		</table>
                        		<br>
                        		<?
                        		echo signature_table(15, $data[0], "900px");
                        		?>
                        	</div>
                        </div>
                        <?
                        exit();
                    }

                    if ($action == "print_adding_topping") {
                    	extract($_REQUEST);
                    	$data = explode('*', $data);
	//print_r ($data);

                    	$sql = "select a.id, a.requ_no, a.company_id, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[0]'";
                    	$dataArray = sql_select($sql);

                    	$recipe_id = $dataArray[0][csf('recipe_id')];
                    	$company_id = $dataArray[0][csf('company_id')];

                    	$company_name = return_field_value("company_name", "lib_company", "id=$company_id");
                    	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
                    	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
                    	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
                    	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
                    	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

                    	$entry_form_arr = array();
                    	$batch_weight_arr = array();
                    	$batchData = sql_select("select id, entry_form, batch_weight from pro_batch_create_mst");
                    	foreach ($batchData as $rowB) {
                    		$entry_form_arr[$rowB[csf('id')]] = $rowB[csf('entry_form')];
                    		$batch_weight_arr[$rowB[csf('id')]] = $rowB[csf('batch_weight')];
                    	}

                    	$batch_weight = 0;
                    	if ($db_type == 0) {
                    		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
                    	} else {
                    		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
                    	}

                    	$batch_weight = $data_array[0][csf("batch_weight")];
                    	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

                    	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
                    	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

                    	if ($db_type == 0) {
                    		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, group_concat(booking_no) as booking_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(color_id) as color_id,group_concat(extention_no ) as extention_no  from pro_batch_create_mst where id in($batch_id)");
                    	} else {
                    		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(CAST(booking_no AS VARCHAR2(4000)),',') within group (order by id) as booking_no, listagg(color_id ,',') within group (order by id) as color_id,listagg(extention_no ,',') within group (order by id) as extention_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
                    	}


                    	$po_no = '';
                    	$job_no = '';
                    	$style_ref = '';
                    	$buyer_name = '';
                    	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
                    		if ($entry_form_arr[$b_id] == 36) {
                    			$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
                    			foreach ($po_data as $row) {
                    				$po_no .= $row[csf('order_no')] . ",";
                    				$job_no .= $row[csf('subcon_job')] . ",";
                    				$style_ref .= $row[csf('cust_style_ref')] . ",";
                    				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    			}
                    		} else {
                    			$po_data = sql_select("select b.po_number, c.job_no, c.buyer_name, c.style_ref_no from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.po_number, c.job_no, c.buyer_name, c.style_ref_no");
                    			foreach ($po_data as $row) {
                    				$po_no .= $row[csf('po_number')] . ",";
                    				$job_no .= $row[csf('job_no')] . ",";
                    				$style_ref .= $row[csf('style_ref_no')] . ",";
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                    			}
                    			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                    				$buyer_name .= $buyer_library[$buyer_id] . ",";
                    			}
                    		}
                    	}

                    	if ($db_type == 0) {
                    		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
                    	} else if ($db_type == 2) {
                    		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
                    	}

                    	$yarn_dtls_array = array();
                    	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
                    	foreach ($result_sql_yarn_dtls as $row) {
                    		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
                    		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
                    		$brand_name = "";
                    		foreach ($brand_id as $val) {
                    			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
                    		}

                    		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
                    		$count_name = "";
                    		foreach ($yarn_count as $val) {
                    			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
                    		}
                    		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
                    		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
                    		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
                    	}
	//var_dump($yarn_dtls_array);
                    	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
                    	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
                    	$style_ref = implode(", ", array_unique(explode(",", substr($style_ref, 0, -1))));
                    	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
                    	$booking_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('booking_no')]))));
                    	$extention_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('extention_no')]))));

                    	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
                    	$color_name = '';
                    	foreach ($color_id as $color) {
                    		$color_name .= $color_arr[$color] . ",";
                    	}
                    	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
                    	?>
                    	<div style="width:1000px;">
                    		<table width="1000" cellspacing="0" align="center">
                    			<tr>
                    				<td colspan="10" align="center" style="font-size:xx-large"><strong><? echo $company_name; ?></strong>
                    				</td>
                    			</tr>
                    			<tr class="form_caption">
                    				<td colspan="6" align="center" style="font-size:14px">
                    					<?
                    					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
                    					foreach ($nameArray as $result) {
                    						?>
                    						Road No: <? echo $result['road_no']; ?>
                    						Block No: <? echo $result['block_no']; ?>
                    						Zip Code: <? echo $result['zip_code']; ?>
                    						Country: <? echo $country_arr[$result['country_id']]; ?><br>
                    						Email Address: <? echo $result['email']; ?>
                    						Website No: <? echo $result['website'];
                    					}
                    					?>
                    				</td>
                    			</tr>
                    			<tr>
                    				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[1]; ?> Report
                    					(Adding/Topping)</u></strong></td>
                    				</tr>
                    			</table>
                    			<table width="950" cellspacing="0" align="center">
                    				<tr>
                    					<td width="90"><strong>Req. ID </strong></td>
                    					<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
                    					<td width="100"><strong>Req. Date</strong></td>
                    					<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                    					<td width="90"><strong>Buyer</strong></td>
                    					<td width="160px"><? echo $buyer_name; ?></td>
                    				</tr>
                    				<tr>
                    					<td><strong>Order No</strong></td>
                    					<td><? echo $po_no; ?></td>
                    					<td><strong>Style No</strong></td>
                    					<td><? echo $style_ref; ?></td>
                    					<td><strong>Booking No</strong></td>
                    					<td><? echo $booking_no; ?></td>
                    				</tr>
                    				<tr>
                    					<td><strong>Batch No</strong></td>
                    					<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
                    					<td><strong>Recipe No</strong></td>
                    					<td><? echo $data_array[0][csf("recipe_id")]; ?></td>

                    					<td><strong>Color</strong></td>
                    					<td><? echo $color_name; ?></td>
                    				</tr>
                    				<tr>
                    					<td><strong>Ext. No</strong></td>
                    					<td><? echo $extention_no; ?></td>
                    					<td>Liquor Ratio</td>
                    					<td><? echo $data_array[0][csf("ratio")]; ?></td>
                    					<td><strong>Total Liq.(ltr)</strong></td>
                    					<td><? echo $data_array[0][csf("total_liquor")]; ?></td>
                    				</tr>
                    				<tr>
                    					<td><strong>Batch Weight</strong></td>
                    					<td><? echo $batch_weight + $batchdata_array[0][csf('batch_weight')]; ?></td>
                    					<td><strong>Machine No</strong></td>
                    					<td>
                    						<?
                    						$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
                    						echo $machine_data[0][csf('machine_no')];
                    						?>
                    					</td>
                    					<td><strong>Floor Name</strong></td>
                    					<td colspan="">
                    						<?
                    						$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                    						echo $floor_name;
                    						?>
                    					</td>
                    				</tr>

                    				<tr>
                    					<td><strong>Labdip No</strong></td>
                    					<td>
                    						<?
                    						$labdip_no = '';
                    						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
                    						foreach ($recipe_ids as $recp_id) {
                    							$labdip_no .= $receipe_arr[$recp_id] . ",";
                    						}
                    						echo chop($labdip_no, ',');
                    						?>
                    					</td>
                    					<td><strong>Job No</strong></td>
                    					<td><? echo $job_no; ?></td>
                    					<td><strong>Issue Basis</strong></td>
                    					<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
                    				</tr>
                    				<tr>

                    					<td>Method</td>
                    					<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
                    				</tr>
                    			</table>
                    			<br>
                    			<? $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
                    			$j = 1;
                    			$entryForm = $entry_form_arr[$batch_id_qry[0]]; ?>
                    			<table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
                    				<thead bgcolor="#dddddd" align="center">
                    					<?
                    					if ($entryForm == 74) {
                    						?>
                    						<tr bgcolor="#CCCCFF">
                    							<th colspan="4" align="center"><strong>Fabrication</strong></th>
                    						</tr>
                    						<tr>
                    							<th width="50">SL</th>
                    							<th width="350">Gmts. Item</th>
                    							<th width="110">Gmts. Qty</th>
                    							<th>Batch Qty.</th>
                    						</tr>
                    						<?
                    					} else {
                    						?>
                    						<tr bgcolor="#CCCCFF">
                    							<th colspan="8" align="center"><strong>Fabrication</strong></th>
                    						</tr>
                    						<tr>
                    							<th width="30">SL</th>
                    							<th width="100">Dia/ W. Type</th>
                    							<th width="100">Yarn Lot</th>
                    							<th width="100">Brand</th>
                    							<th width="100">Count</th>
                    							<th width="300">Constrution & Composition</th>
                    							<th width="70">Gsm</th>
                    							<th width="70">Dia</th>
                    						</tr>
                    						<?
                    					}
                    					?>
                    				</thead>
                    				<tbody>
                    					<?
                    					foreach ($batch_id_qry as $b_id) {
				//$batch_query="select id, po_id, prod_id, item_description, width_dia_type, roll_no as gmts_qty, batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0";
                    						$batch_query = "select po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) as batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by  po_id, prod_id, item_description, width_dia_type";
                    						$result_batch_query = sql_select($batch_query);
                    						foreach ($result_batch_query as $rows) {
                    							if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                    							$fabrication_full = $rows[csf("item_description")];
                    							$fabrication = explode(',', $fabrication_full);
                    							if ($entry_form_arr[$b_id] == 36) {
                    								?>
                    								<tr bgcolor="<? echo $bgcolor; ?>">
                    									<td align="center"><? echo $j; ?></td>
                    									<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
                            	?></td>
                            	<td align="left"><? echo $fabrication[0]; ?></td>
                            	<td align="center"><? echo $fabrication[1]; ?></td>
                            	<td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                        } else if ($entry_form_arr[$b_id] == 74) {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                        	</tr>
                        	<?
                        } else {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        }
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="17" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
        			</tr>

        			<?

        			$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7) and status_active=1 and is_deleted=0", 'id', 'item_name');
        			$item_lot_arr = array();
        			$recipeData_arr = array();
        			$old_recp_id_arr = array();

				$recipeData = sql_select("select a.id, a.recipe_id, a.entry_form, a.batch_id, a.total_liquor, b.sub_process_id, b.prod_id, b.item_lot, b.ratio, b.adj_perc, b.new_item, b.dose_base from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0");// and a.id in ($recipe_id)
				foreach ($recipeData as $rowR) {
					$item_lot_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]] .= $rowR[csf('item_lot')] . ",";
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['entry_form'] = $rowR[csf('entry_form')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['recipe_id'] = $rowR[csf('recipe_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['batch_id'] = $rowR[csf('batch_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['ratio'] = $rowR[csf('ratio')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['new_item'] = $rowR[csf('new_item')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['adj_perc'] = $rowR[csf('adj_perc')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['dose_base'] = $rowR[csf('dose_base')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['total_liquor'] = $rowR[csf('total_liquor')];
					$old_recp_id_arr[$rowR[csf('id')]] = $rowR[csf('recipe_id')];
				}
				$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";
				$nameArray_re = sql_select($sql_rec_remark);
				foreach ($nameArray_re as $row) {
					$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
				}

				//print_r($recipeData_arr[276]);
				$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit, c.id as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
				where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7) and b.req_qny_edit>0 and c.item_category_id in (5,6,7) and a.id=$data[0])
				union
				(
				select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, null as item_description,  null as item_group_id, null as sub_group_name,  null as item_size,  null as unit_of_measure, null as avg_rate_per_unit, null as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
				where a.id=b.mst_id  and  b.sub_process in (93,94,95,96,97,98) and a.id=$data[0]
				) order by id";
				// echo $sql_dtls;//die;
				$sql_result = sql_select($sql_dtls);

				//var_dump($sub_process_tot_req_array);
				$i = 1;
				$k = 1;
				$recipe_qnty_sum = 0;
				$req_qny_edit_sum = 0;
				$recipe_qnty = 0;
				$req_qny_edit = 0;
				$req_value_sum = 0;
				$req_value_grand = 0;
				$recipe_qnty_grand = 0;
				$req_qny_edit_grand = 0;
				$sub_process_array = array();

				foreach ($sql_result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if (!in_array($row[csf("sub_process")], $sub_process_array))
					{
						$sub_process_array[] = $row[csf('sub_process')];
						if ($k != 1) {
							?>
							<tr>
								<td colspan="8" align="right"><strong>Total :</strong></td>
								<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
							</tr>
							<?
							$recipe_qnty_sum = 0;
							$req_qny_edit_sum = 0;
							$req_value_sum = 0;
						}
						$k++;


						if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
							$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
						}
						?>
						<tr bgcolor="#CCCCCC">
							<th colspan="17">
								<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark; ?></strong>
							</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Item Cat.</th>
							<th width="80">Item Group</th>
							<th width="100">Item Description</th>
							<th width="50">Dyes Lot</th>
							<th width="50">UOM</th>
							<th width="100">Dose Base</th>
							<th width="40">Old Ratio</th>
							<th width="60">Old Recipe Qty.</th>
							<th width="50">New Adj%</th>
							<th width="60">New Ratio</th>
							<th width="80">New Recipe Qty.</th>
							<th width="60">KG</th>
							<th width="60">GM</th>
							<th width="60">MG</th>
							<th width="70">Avg. Rate</th>
							<th width="80">Issue Vaule</th>
						</tr>
					</thead>
					<?
				}

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

				$adj_perc = $recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['adj_perc'];
				$oldRecpId = $old_recp_id_arr[$row[csf('recipe_id')]];
				$old_ratio = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['ratio'];
				$selected_dose = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['dose_base'];
				$actual_total_liquor = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['total_liquor'];
				$actual_batch_weight = $batch_weight_arr[$recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['batch_id']];

				$prev_recipe_qty = 0;
				if ($selected_dose == 1) {
					$prev_recipe_qty = number_format(($actual_total_liquor * $old_ratio) / 1000, 4);
				} else if ($selected_dose == 2) {
					$prev_recipe_qty = number_format(($actual_batch_weight * $old_ratio) / 100, 4);
				}

				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                    	?></td>
                    	<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                    	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                    	<td><? echo implode(",", array_unique(array_filter(explode(",", $item_lot_arr[$row[csf('recipe_id')]][$row[csf("sub_process")]][$row[csf("prod_id")]])))); ?></td>
                    	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($old_ratio, 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($prev_recipe_qty, 6, '.', ''); ?></td>
                    	<td align="right"><? echo $adj_perc; ?></td>
                    	<td align="right"
                    	title="<? echo "New Ratio: " . $row[csf("ratio")] . ", Adj Perc: " . $row[csf("adjust_percent")]; ?>"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo $iss_qnty_kg; ?></td>
                    	<td align="right"><? echo $iss_qnty_gm; ?></td>
                    	<td align="right"><? echo $iss_qnty_mg; ?></td>
                    	<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")], 6, '.', ''); ?></td>
                    	<td align="right"><? $req_value = $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
                    		echo number_format($req_value, 6, '.', ''); ?></td>
                    	</tr>
                    </tbody>
                    <?
                    $i++;
                    $recipe_qnty_sum += $prev_recipe_qty;
                    $req_qny_edit_sum += $row[csf('req_qny_edit')];
                    $req_value_sum += $req_value;

                    $recipe_qnty_grand += $prev_recipe_qty;
                    $req_qny_edit_grand += $row[csf('req_qny_edit')];
                    $req_value_grand += $req_value;
                }
                ?>
                <tr>
                	<td colspan="8" align="right"><strong>Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
                </tr>
                <tr>
                	<td colspan="8" align="right"><strong> Grand Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_value_grand, 6, '.', ''); ?></td>
                </tr>
                <tr>
                	<td colspan="15" align="right"><strong> Cost Per Kg :</strong></td>
                	<td colspan="2"
                	align="right"><?php echo number_format($req_value_grand / ($batch_weight + $batchdata_array[0][csf('batch_weight')]), 6, '.', ''); ?></td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(15, $data[0], "900px");
            ?>
        </div>
    </div>
    <?
    exit();
}
if ($action == "print_adding_topping_without_rate_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.company_id, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	$company_id = $dataArray[0][csf('company_id')];

	$company_name = return_field_value("company_name", "lib_company", "id=$company_id");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$entry_form_arr = array();
	$batch_weight_arr = array();
	$batchData = sql_select("select id, entry_form, batch_weight from pro_batch_create_mst");
	foreach ($batchData as $rowB) {
		$entry_form_arr[$rowB[csf('id')]] = $rowB[csf('entry_form')];
		$batch_weight_arr[$rowB[csf('id')]] = $rowB[csf('batch_weight')];
	}

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}

	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, group_concat(booking_no) as booking_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(color_id) as color_id,group_concat(extention_no) as extention_no  from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(CAST(booking_no AS VARCHAR2(4000)),',') within group (order by id) as booking_no, listagg(color_id ,',') within group (order by id) as color_id,listagg(extention_no ,',') within group (order by id) as extention_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$po_no = '';
	$job_no = '';
	$style_ref = '';
	$buyer_name = '';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
		if ($entry_form_arr[$b_id] == 36) {
			$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				$style_ref .= $row[csf('cust_style_ref')] . ",";
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} else {
			$po_data = sql_select("select b.po_number, c.job_no, c.buyer_name, c.style_ref_no from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.po_number, c.job_no, c.buyer_name, c.style_ref_no");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				$style_ref .= $row[csf('style_ref_no')] . ",";
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}
		}
	}

	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$style_ref = implode(", ", array_unique(explode(",", substr($style_ref, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
	$booking_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('booking_no')]))));
	$extention_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('extention_no')]))));


	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	?>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="10" align="center" style="font-size:xx-large"><strong><? echo $company_name; ?></strong>
				</td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
					foreach ($nameArray as $result) {
						?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no']; ?>
						Zip Code: <? echo $result['zip_code']; ?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email']; ?>
						Website No: <? echo $result['website'];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[1]; ?> Report
					(Adding/Topping)</u></strong></td>
				</tr>
			</table>
			<table width="950" cellspacing="0" align="center">
				<tr>
					<td width="90"><strong>Req. ID </strong></td>
					<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
					<td width="100"><strong>Req. Date</strong></td>
					<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					<td width="90"><strong>Buyer</strong></td>
					<td width="160px"><? echo $buyer_name; ?></td>
				</tr>
				<tr>
					<td><strong>Order No</strong></td>
					<td><? echo $po_no; ?></td>
					<td><strong>Style No</strong></td>
					<td><? echo $style_ref; ?></td>
					<td><strong>Booking No</strong></td>
					<td><? echo $booking_no; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No</strong></td>
					<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
					<td><strong>Recipe No</strong></td>
					<td><? echo $data_array[0][csf("recipe_id")]; ?></td>

					<td><strong>Color</strong></td>
					<td><? echo $color_name; ?></td>
				</tr>
				<tr>
					<td><strong>Ext. No</strong></td>
					<td><? echo $extention_no; ?></td>
					<td><strong>Batch Weight</strong></td>
					<td><? echo $batch_weight + $batchdata_array[0][csf('batch_weight')]; ?></td>
					<td><strong>Machine No</strong></td>
					<td>
						<?
						$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
						echo $machine_data[0][csf('machine_no')];
						?>
					</td>

				</tr>
				<tr>

                <!-- <td>Liquor Ratio</td><td><? echo $data_array[0][csf("ratio")]; ?></td>
                <td><strong>Total Liq.(ltr)</strong></td><td><? echo $data_array[0][csf("total_liquor")]; ?></td>-->

                <td><strong>Floor Name</strong></td>
                <td colspan="">
                	<?
                	$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                	echo $floor_name;
                	?>
                </td>
                <td>Method</td>
                <td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
                <td><strong>Labdip No</strong></td>
                <td>
                	<?
                	$labdip_no = '';
                	$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
                	foreach ($recipe_ids as $recp_id) {
                		$labdip_no .= $receipe_arr[$recp_id] . ",";
                	}
                	echo chop($labdip_no, ',');
                	?>
                </td>
            </tr>
            <tr>


            	<td><strong>Job No</strong></td>
            	<td><? echo $job_no; ?></td>
            	<td><strong>Issue Basis</strong></td>
            	<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
            </tr>

        </table>
        <br>
        <? $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
        $j = 1;
        $entryForm = $entry_form_arr[$batch_id_qry[0]]; ?>
        <table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
        	<thead bgcolor="#dddddd" align="center">
        		<?
        		if ($entryForm == 74) {
        			?>
        			<tr bgcolor="#CCCCFF">
        				<th colspan="4" align="center"><strong>Fabrication</strong></th>
        			</tr>
        			<tr>
        				<th width="50">SL</th>
        				<th width="350">Gmts. Item</th>
        				<th width="110">Gmts. Qty</th>
        				<th>Batch Qty.</th>
        			</tr>
        			<?
        		} else {
        			?>
        			<tr bgcolor="#CCCCFF">
        				<th colspan="8" align="center"><strong>Fabrication</strong></th>
        			</tr>
        			<tr>
        				<th width="30">SL</th>
        				<th width="100">Dia/ W. Type</th>
        				<th width="100">Yarn Lot</th>
        				<th width="100">Brand</th>
        				<th width="100">Count</th>
        				<th width="300">Constrution & Composition</th>
        				<th width="70">Gsm</th>
        				<th width="70">Dia</th>
        			</tr>
        			<?
        		}
        		?>
        	</thead>
        	<tbody>
        		<?
        		foreach ($batch_id_qry as $b_id) {
				//$batch_query="select id, po_id, prod_id, item_description, width_dia_type, roll_no as gmts_qty, batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0";
        			$batch_query = "select po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) as batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by  po_id, prod_id, item_description, width_dia_type";
        			$result_batch_query = sql_select($batch_query);
        			foreach ($result_batch_query as $rows) {
        				if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
        				$fabrication_full = $rows[csf("item_description")];
        				$fabrication = explode(',', $fabrication_full);
        				if ($entry_form_arr[$b_id] == 36) {
        					?>
        					<tr bgcolor="<? echo $bgcolor; ?>">
        						<td align="center"><? echo $j; ?></td>
        						<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
                            	?></td>
                            	<td align="left"><? echo $fabrication[0]; ?></td>
                            	<td align="center"><? echo $fabrication[1]; ?></td>
                            	<td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                        } else if ($entry_form_arr[$b_id] == 74) {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                        	</tr>
                        	<?
                        } else {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        }
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="17" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
        			</tr>

        			<?

        			$group_arr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0", 'id', 'item_name');
        			$item_lot_arr = array();
        			$recipeData_arr = array();
        			$old_recp_id_arr = array();

				$recipeData = sql_select("select a.id, a.recipe_id, a.entry_form, a.batch_id, a.total_liquor, b.sub_process_id, b.prod_id, b.item_lot, b.ratio, b.adj_perc, b.new_item, b.dose_base from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0");// and a.id in ($recipe_id)
				foreach ($recipeData as $rowR) {
					$item_lot_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]] .= $rowR[csf('item_lot')] . ",";
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['entry_form'] = $rowR[csf('entry_form')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['recipe_id'] = $rowR[csf('recipe_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['batch_id'] = $rowR[csf('batch_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['ratio'] = $rowR[csf('ratio')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['new_item'] = $rowR[csf('new_item')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['adj_perc'] = $rowR[csf('adj_perc')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['dose_base'] = $rowR[csf('dose_base')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['total_liquor'] = $rowR[csf('total_liquor')];
					$old_recp_id_arr[$rowR[csf('id')]] = $rowR[csf('recipe_id')];
				}


				//print_r($recipeData_arr[276]);
				$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit, c.id as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
				where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7) and b.req_qny_edit>0 and c.item_category_id in (5,6,7) and a.id=$data[0])
				union
				(
				select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, null as item_description,  null as item_group_id, null as sub_group_name,  null as item_size,  null as unit_of_measure, null as avg_rate_per_unit, null as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
				where a.id=b.mst_id  and  b.sub_process in (93,94,95,96,97,98) and a.id=$data[0]
				) order by id";
				// echo $sql_dtls;//die;
				$sql_result = sql_select($sql_dtls);

				//var_dump($sub_process_tot_req_array);
				$i = 1;
				$k = 1;
				$recipe_qnty_sum = 0;
				$req_qny_edit_sum = 0;
				$recipe_qnty = 0;
				$req_qny_edit = 0;
				$req_value_sum = 0;
				$req_value_grand = 0;
				$recipe_qnty_grand = 0;
				$req_qny_edit_grand = 0;
				$sub_process_array = array();

				$sql_rec = "select a.id,a.item_group_id,b.liquor_ratio,b.total_liquor,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7) and b.ratio>0 and b.status_active=1 and b.is_deleted=0";
				$nameArray = sql_select($sql_rec);
				$process_array = array();
				$process_array_remark = array();
				foreach ($nameArray as $row) {

					$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
					$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
					$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

				}
				$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.liquor_ratio,b.total_liquor from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";
				$nameArray_re = sql_select($sql_rec_remark);
				foreach ($nameArray_re as $row) {
					$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
					$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
					$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
				}
				foreach ($sql_result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if (!in_array($row[csf("sub_process")], $sub_process_array))
					{
						$sub_process_array[] = $row[csf('sub_process')];
						if ($k != 1) {
							?>
							<tr>
								<td colspan="8" align="right"><strong>Total :</strong></td>
								<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>

								<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
							</tr>
							<?
							$recipe_qnty_sum = 0;
							$req_qny_edit_sum = 0;
							$req_value_sum = 0;
						}
						$k++;


						if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
							$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
						}
						if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
						$total_liquor = 'Total liquor(ltr)' . ": " . $process_array_liquor[$row[csf("sub_process")]]['total_liquor'];
						$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
						?>
						<tr bgcolor="#CCCCCC">
							<th colspan="17">
								<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $liquor_ratio . ', ' . $total_liquor . $pro_remark; ?></strong>
							</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Item Cat.</th>
							<th width="80">Item Group</th>
							<th width="100">Item Description</th>
							<th width="50">Dyes Lot</th>
							<th width="50">UOM</th>
							<th width="100">Dose Base</th>
							<th width="40">Old Ratio</th>
							<th width="60">Old Recipe Qty.</th>
							<th width="50">New Adj%</th>
							<th width="60">New Ratio</th>
							<th width="80">New Recipe Qty.</th>
							<th width="60">KG</th>
							<th width="60">GM</th>
							<th width="60">MG</th>
							<th width="150">Comments</th>

						</tr>
					</thead>
					<?
				}

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
				$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
				$adj_perc = $recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['adj_perc'];
				$oldRecpId = $old_recp_id_arr[$row[csf('recipe_id')]];
				$old_ratio = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['ratio'];
				$selected_dose = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['dose_base'];
				$actual_total_liquor = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['total_liquor'];
				$actual_batch_weight = $batch_weight_arr[$recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['batch_id']];

				$prev_recipe_qty = 0;
				if ($selected_dose == 1) {
					$prev_recipe_qty = number_format(($actual_total_liquor * $old_ratio) / 1000, 4);
				} else if ($selected_dose == 2) {
					$prev_recipe_qty = number_format(($actual_batch_weight * $old_ratio) / 100, 4);
				}

				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                    	?></td>
                    	<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                    	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                    	<td><? echo implode(",", array_unique(array_filter(explode(",", $item_lot_arr[$row[csf('recipe_id')]][$row[csf("sub_process")]][$row[csf("prod_id")]])))); ?></td>
                    	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($old_ratio, 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($prev_recipe_qty, 6, '.', ''); ?></td>
                    	<td align="right"><? echo $adj_perc; ?></td>
                    	<td align="right"
                    	title="<? echo "New Ratio: " . $row[csf("ratio")] . ", Adj Perc: " . $row[csf("adjust_percent")]; ?>"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo $iss_qnty_kg; ?></td>
                    	<td align="right"><? echo $iss_qnty_gm; ?></td>
                    	<td align="right"><? echo $iss_qnty_mg;
                    		$comment ?></td>
                    		<td align="right"><? echo $comment; ?></td>
                    	</tr>
                    </tbody>
                    <?
                    $i++;
                    $recipe_qnty_sum += $prev_recipe_qty;
                    $req_qny_edit_sum += $row[csf('req_qny_edit')];
                    $req_value_sum += $req_value;

                    $recipe_qnty_grand += $prev_recipe_qty;
                    $req_qny_edit_grand += $row[csf('req_qny_edit')];
                    $req_value_grand += $req_value;
                }
                ?>
                <tr>
                	<td colspan="8" align="right"><strong>Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>

                	<td align="right"></td>
                </tr>
                <tr>
                	<td colspan="8" align="right"><strong> Grand Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>

                	<td align="right"></td>
                </tr>

            </table>
            <br>
            <?
            echo signature_table(15, $data[0], "900px");
            ?>
        </div>
    </div>
    <?
    exit();
}
if ($action == "chemical_dyes_issue_requisition_without_rate_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	// echo "<pre>";
	// print_r ($data);
	$company=$data[0];
	$location=$data[3];

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$roll_no=sql_select("select sum(roll_no) as roll_no from pro_batch_create_dtls where mst_id in ('$batch_id') and is_deleted=0 and status_active=1");

	$recipe_dlts=array();
	$recipe_dtls_arr=sql_select("SELECT a.batch_ratio, b.sub_process_id, b.total_liquor, b.recipe_time, b.recipe_temperature, b.recipe_ph, b.liquor_ratio FROM pro_recipe_entry_mst a, pro_recipe_entry_dtls b WHERE a.id=b.mst_id AND b.mst_id in ('$recipe_id') AND b.status_active=1 AND b.is_deleted=0");
	foreach ($recipe_dtls_arr as $val) 
	{
		$recipe_dlts[$val[csf('sub_process_id')]]['total_liquor']=$val[csf('total_liquor')];
		$recipe_dlts[$val[csf('sub_process_id')]]['recipe_time']=$val[csf('recipe_time')];
		$recipe_dlts[$val[csf('sub_process_id')]]['recipe_temperature']=$val[csf('recipe_temperature')];
		$recipe_dlts[$val[csf('sub_process_id')]]['recipe_ph']=$val[csf('recipe_ph')];
		$recipe_dlts[$val[csf('sub_process_id')]]['liquor_ratio']=$val[csf('liquor_ratio')];
		$recipe_dlts[$val[csf('sub_process_id')]]['batch_ratio']=$val[csf('batch_ratio')];
	}

	$po_no = '';
	$job_no = '';
	$buyer_name = '';
	$style_ref_no = '';
	$cust_buyer='';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
		if ($entry_form_arr[$b_id] == (36||150)) {
			//$po_data=sql_select("select distinct b.order_no, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ");
			$po_data = sql_select("select distinct b.order_no,b.cust_style_ref, b.cust_buyer, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('cust_style_ref')]; else $style_ref_no .= "," . $row[csf('cust_style_ref')];
				if ($cust_buyer == '') $cust_buyer = $row[csf('cust_buyer')]; else $cust_buyer .= "," . $row[csf('cust_buyer')];
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} else {
			//$po_data=sql_select("select distinct b.po_number, c.job_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ");
			$po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}

		}
	}

	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="10" align="center" style="font-size:xx-large">
					<strong><? echo $com_dtls[0]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?
							echo $com_dtls[1];
						/*$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result) {
							?>
							Plot No: <? echo $result['plot_no']; ?>
							Level No: <? echo $result['level_no'] ?>
							Road No: <? echo $result['road_no']; ?>
							Block No: <? echo $result['block_no']; ?>
							City No: <? echo $result['city']; ?>
							Zip Code: <? echo $result['zip_code']; ?>
							Province No: <?php echo $result['province']; ?>
							Country: <? echo $country_arr[$result['country_id']]; ?><br>
							Email Address: <? echo $result['email']; ?>
							Website No: <? echo $result['website'];
						}*/
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?>
						Report</u></strong></td>
					</tr>
				</table>
				<table width="950" cellspacing="0" align="center">
					<tr>
						<td width="90"><strong>Req. ID <span style="float: right;">:</span></strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td width="100"><strong>Req. Date <span style="float: right;">:</span></strong></td>
						<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
						<td width="90"><strong>Party Name <span style="float: right;">:</span></strong></td>
						<td width="160px"><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
					</tr>
					<tr>
						<td><strong>Order No <span style="float: right;">:</span></strong></td>
						<td><? echo $po_no; ?></td>
						<td><strong>Style <span style="float: right;">:</span></strong></td>
						<td><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></td>
						<td><strong>Party Buyer <span style="float: right;">:</span></strong></td>
						<td><? echo implode(",", array_unique(explode(",", $cust_buyer))); ?></td>
					</tr>
					<tr>
						<td><strong>Batch No <span style="float: right;">:</span></strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight <span style="float: right;">:</span></strong></td>
						<td><? echo $batch_weight + $batchdata_array[0][csf('batch_weight')]; ?> Kg</td>
						<td><strong>Batch Qty(Pcs) <span style="float: right;">:</span></strong></td>
						<td><? echo $roll_no[0][csf('roll_no')]; ?></td>
					</tr>
					<tr>
						<td><strong>Recipe No <span style="float: right;">:</span></strong></td>
						<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
						<td><strong>Color <span style="float: right;">:</span></strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Issue Basis <span style="float: right;">:</span></strong></td>
						<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Machine No <span style="float: right;">:</span></strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
						<td><strong>Method <span style="float: right;">:</span></strong></td>
						<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
					</tr>
				</table>
    <br>
    <?
    $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
    $j = 1;
    $entryForm = $entry_form_arr[$batch_id_qry[0]];
    ?>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="16" align="center"><strong>Dyes And Chemical Issue Requisition Without Rate</strong>
        				</th>
        			</tr>

        			<?
        			$sql_rec = "select a.id,a.item_group_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7) and b.status_active=1 and b.is_deleted=0";
        			$nameArray = sql_select($sql_rec);
        			$process_array = array();
        			$process_array_remark = array();
        			foreach ($nameArray as $row) {

        				$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        				$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

        			}
        			$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";
        			$nameArray_re = sql_select($sql_rec_remark);
        			foreach ($nameArray_re as $row) {
        				$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
        			}

        			$group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
        			if ($db_type == 0) {
        				$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
        			} else {
        				$item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
        			}
				
	    $sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
	    b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	    c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7) and a.id=$data[1] order by id";
				// echo $sql_dtls;//die;

	    $sql_result = sql_select($sql_dtls);

	    $sub_process_array = array();
	    $sub_process_tot_rec_array = array();
	    $sub_process_tot_req_array = array();
	    $sub_process_tot_value_array = array();

	    foreach ($sql_result as $row) {
	    	$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
	    	$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
	    	$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
	    }

				//var_dump($sub_process_tot_req_array);
	    $i = 1;
	    $k = 1;
	    $recipe_qnty_sum = 0;
	    $req_qny_edit_sum = 0;
	    $recipe_qnty = 0;
	    $req_qny_edit = 0;
	    $req_value_sum = 0;
	    $req_value_grand = 0;
	    $recipe_qnty_grand = 0;
	    $req_qny_edit_grand = 0;

	    foreach ($sql_result as $row)
	    {
	    	if ($i % 2 == 0)
	    		$bgcolor = "#E9F3FF";
	    	else
	    		$bgcolor = "#FFFFFF";
	    	if (!in_array($row[csf("sub_process")], $sub_process_array))
	    	{
	    		$sub_process_array[] = $row[csf('sub_process')];
	    		if ($k != 1) {
	    			?>
	    			<tr>
	    				<td colspan="7" align="right"><strong>Total :</strong></td>
	    				<td align="right"><?php echo number_format($recipe_qnty_sum, 2, '.', ''); ?></td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
	    			</tr>
	    			<? }
	    			$recipe_qnty_sum = 0;
	    			$req_qny_edit_sum = 0;
	    			$req_value_sum = 0;
	    			$k++;
	    			$pro_remark="";
	 
	    			$total_liquor = 'Total liquor(ltr)' . ": " . $process_array_liquor[$row[csf("sub_process")]]['total_liquor'];
	    			$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];

	    			?>
	    			<tr bgcolor="#CCCCCC">
			    		<th colspan="6" align="left" style="border-right: none;">
							<strong>
								Sub Process Name : <? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$recipe_dlts[$row[csf("sub_process")]]['total_liquor']. ' (ltr), ' . $pro_remark; ?> 
							</strong>
						</th>
						<th colspan="10" style="border-left: none;">
							<strong>Ratio: </strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['batch_ratio'].":".$recipe_dlts[$row[csf("sub_process")]]['liquor_ratio'];  ?>  
			                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			                <strong>Time:</strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['recipe_time']; ?> min
			                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			                <strong>Temp:</strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['recipe_temperature']; ?> &#8451;
			                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			                <strong>PH:</strong> <? echo $recipe_dlts[$row[csf("sub_process")]]['recipe_ph']; ?>
						</th>
	    			</tr>
	    			<tr>
	    				<th width="30">SL</th>
	    				<th width="80">Item Catg.</th>
	    				<th width="80">Item Group</th>
	    				<!--<th width="100">Sub Group</th>-->
	    				<th width="100">Item Description</th>
	    				<th width="50">Lot</th>
	    				<th width="100">Dose Base</th>
	    				<th width="40">Ratio</th>
	    				<th width="60">Recipe Qty.</th>
	    				<th width="50">UOM</th>
	    				<th width="50">Adj%</th>
	    				<th width="60">Adj Type</th>
	    				<th width="80">Req. Qty.</th>
	    				<th width="100">Comments</th>

	    			</tr>
	    		</thead>
	    		<?
	    	}

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
				$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"]

				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    	<td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")]; ?></td>
                    	<td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
                    	<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
                    	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                    	<td><? echo $item_lot_arr[$row[csf("prod_id")]]; ?></td>
                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($row[csf("ratio")], 2, '.', ''); ?></td>
                    	<td align="right"><b><? echo number_format($row[csf("recipe_qnty")], 2, '.', ''); ?></b></td>
                    	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                    	<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                    	<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                    	<td align="right"><b><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></b></td>
                    	<td align="right"><? echo $comment; ?></td>
                    </tr>
                </tbody>
                <? $i++;
                $recipe_qnty_sum += $row[csf('recipe_qnty')];
                $req_qny_edit_sum += $row[csf('req_qny_edit')];
                $req_value_sum += $req_value;

                $recipe_qnty_grand += $row[csf('recipe_qnty')];
                $req_qny_edit_grand += $row[csf('req_qny_edit')];
                $req_value_grand += $req_value;
            }
            foreach ($sub_process_tot_rec_array as $val_rec) {
            	$totval_rec = $val_rec;
            }
            foreach ($sub_process_tot_req_array as $val_req) {
            	$totval_req = $val_req;
            }
            foreach ($sub_process_tot_value_array as $req_value) {
            	$tot_req_value = $req_value;
            }

            ?>
            <tr>
            	<td colspan="7" align="right"><strong>Total :</strong></td>
            	<td align="right"><?php echo number_format($totval_rec, 2, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td align="right"><?php echo number_format($totval_req, 6, '.', ''); ?></td>
            </tr>
            <tr>
            	<td colspan="7" align="right"><strong> Grand Total :</strong></td>
            	<td align="right"><?php echo number_format($recipe_qnty_grand, 2, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
        echo signature_table(15, $data[0], "900px");
        ?>
    </div>
</div>
<?
exit();
}

?>