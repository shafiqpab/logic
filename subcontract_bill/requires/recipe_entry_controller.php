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

if ($action == "systemid_popup") 
{
	echo load_html_head_contents("Labdip No Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(id) 
        {
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
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
                                   value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_update_id" id="hidden_update_id" class="text_boxes"
                                   value="">
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
                            <input type="text" style="width:100px;" class="text_boxes" name="txt_search_batch"
                                   id="txt_search_batch" placeholder="Search"/>
                        </td>
                        <td>
                            <input type="text" style="width:130px;" class="text_boxes" name="txt_search_recDes"
                                   id="txt_search_recDes" placeholder="Search"/>
                        </td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view ( document.getElementById('txt_search_labdip').value+'_'+document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_batch').value, 'create_recipe_search_list_view', 'search_div', 'recipe_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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
	$batch_no_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$data = explode("_", $data);
	$labdip = $data[0];
	$sysid = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$rec_des = trim($data[5]);
	$search_type = $data[6];
	$batch_no = $data[7];

	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		} 
		else if ($db_type == 2) 
		{
			$date_cond = "and recipe_date between '" . change_date_format(trim($start_date), "mm-dd-yyyy", "/", 1) . "' and '" . change_date_format(trim($end_date), "mm-dd-yyyy", "/", 1) . "'";
		}
	} 
	else 
	{
		$date_cond = "";
	}

	if ($search_type == 1) 
	{
		if ($labdip != '') $labdip_cond = " and labdip_no='$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id=$sysid"; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description='$rec_des'"; else $rec_des_cond = "";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des%'"; else $rec_des_cond = "";
	} 
	else if ($search_type == 2) 
	{
		if ($labdip != '') $labdip_cond = " and labdip_no like '$labdip%'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '$sysid%' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '$rec_des%'"; else $rec_des_cond = "";
	} 
	else if ($search_type == 3) 
	{
		if ($labdip != '') $labdip_cond = " and labdip_no like '%$labdip'"; else $labdip_cond = "";
		if ($sysid != '') $sysid_cond = " and id like '%$sysid' "; else $sysid_cond = "";
		if ($rec_des != '') $rec_des_cond = " and recipe_description like '%$rec_des'"; else $rec_des_cond = "";
	}
	if ($batch_no != "") 
	{
		$batch_ids = return_field_value("id", "pro_batch_create_mst", "batch_no='$batch_no'", "id");
		$batch_cond = "and batch_id=$batch_ids";
	} 
	else 
	{
		$batch_cond = "";
	}
	$po_arr = sql_select("select b.id,c.mst_id as batch_id,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.job_no=b.job_no_mst and b.id=c.po_id and a.company_name=$company_id and b.status_active=1 and b.is_deleted=0");
	$po_ref_arr = array();
	$po_file_arr = array();
	foreach ($po_arr as $row) 
	{

		$po_file_arr[$row[csf('batch_id')]] = $row[csf('file_no')];
		$po_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];
	}

	$sql = "select id, labdip_no,batch_id, recipe_description, recipe_date, order_source, style_or_order, buyer_id, color_id, color_range from pro_recipe_entry_mst where company_id='$company_id' and entry_form=151 and status_active=1 and is_deleted=0 $labdip_cond $sysid_cond $rec_des_cond $date_cond $batch_cond order by id DESC";
	//echo $sql;

	$arr = array(2 => $batch_no_arr, 5 => $knitting_source, 6 => $buyer_arr, 7 => $color_arr, 8 => $color_range);

	echo "<div align='center'>";
	echo create_list_view("tbl_list_search", "ID,Labdip No,Batch No,Recipe Description,Recipe Date,Order Source,Party,Color,Color Range", "50,80,80,130,70,80,100,70,90", "870", "200", 0, $sql, "js_set_value", "id", "", 1, "0,0,batch_id,0,0,order_source,buyer_id,color_id,color_range", $arr, "id,labdip_no,batch_id,recipe_description,recipe_date,order_source,buyer_id,color_id,color_range", "", "", '0,0,0,0,3,0,0,0,0', '');
	echo "</div>";
	exit();
}

if ($action == 'populate_data_from_search_popup') 
{
	//echo "select id, labdip_no, company_id, location_id, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range from pro_recipe_entry_mst where id='$data'";
	$data_array = sql_select("select id, labdip_no, company_id, location_id, recipe_description, batch_id,batch_qty, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range from pro_recipe_entry_mst where id='$data'");
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_sys_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "document.getElementById('update_id_check').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_labdip_no').value 				= '" . $row[csf("labdip_no")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";

		//echo "load_drop_down('requires/recipe_entry_controller', ".$row[csf('company_id')].", 'load_drop_down_location', 'location_td' );\n";
		//echo "load_drop_down('requires/recipe_entry_controller', ".$row[csf('company_id')].", 'load_drop_down_buyer', 'buyer_td_id' );\n";

		echo "document.getElementById('cbo_location').value 				= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_recipe_date').value 				= '" . change_date_format($row[csf("recipe_date")]) . "';\n";
		echo "document.getElementById('cbo_order_source').value 			= '" . $row[csf("order_source")] . "';\n";

		echo "document.getElementById('txt_recipe_des').value 				= '" . $row[csf("recipe_description")] . "';\n";
		echo "document.getElementById('txt_batch_id').value 				= '" . $row[csf("batch_id")] . "';\n";
		echo "document.getElementById('cbo_method').value 					= '" . $row[csf("method")] . "';\n";

		echo "document.getElementById('txt_liquor').value 					= '" . $row[csf("total_liquor")] . "';\n";
		echo "document.getElementById('txt_batch_ratio').value 				= '" . $row[csf("batch_ratio")] . "';\n";
		echo "document.getElementById('txt_liquor_ratio').value 			= '" . $row[csf("liquor_ratio")] . "';\n";
		echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "get_php_form_data(" . $row[csf("company_id")] . "+'**'+" . $row[csf("batch_id")] . ", 'load_data_from_batch', 'requires/recipe_entry_controller');\n";
		if ($row[csf("batch_qty")] != 0) 
		{
			echo "document.getElementById('txt_batch_weight').value 				= '" . $row[csf("batch_qty")] . "';\n";
		}
		//echo "select id,sub_process_id,process_remark from pro_recipe_entry_dtls where mst_id='".$row[csf("id")]."' ";
		$sql_rec_dtls = sql_select("select id,sub_process_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $row[csf("id")] . " ");

		$liquor_ratio = $sql_rec_dtls[0][csf('liquor_ratio')];
		$total_liquor = $sql_rec_dtls[0][csf('total_liquor')];
		echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_recipe_entry',1);\n";
		exit();
	}
}

if ($action == "booking_popup") 
{
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(booking_id, booking_no, color, color_id, job_no, type) 
        {
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
                                   onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>', 'create_booking_search_list_view', 'search_div', 'recipe_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
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

	if ($db_type == 0) 
	{
		$groupby_field = "group by a.id, b.fabric_color_id";
		$groupby_u_field = "group by a.id, b.fabric_color_id";
		$groupby_d_field = "group by s.id, f.fabric_color";
	} 
	else if ($db_type == 2) 
	{
		$groupby_field = "group by a.id, b.fabric_color_id,a.booking_no, a.booking_date, a.buyer_id,c.job_no, c.style_ref_no ";
		$groupby_u_field = "group by a.id, b.fabric_color_id,a.booking_no, a.booking_date, a.buyer_id,c.job_no, c.style_ref_no ";
		$groupby_d_field = "group by s.id, f.fabric_color,s.booking_no, s.booking_date, s.buyer_id";
	}

	if ($buyer_id == 0) 
	{
		echo "Please Select Buyer First.";
		die;
	}

	$po_number_array = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');;
	if (trim($data[0]) != "") 
	{
		if ($search_by == 1)
			$search_field_cond = "and a.booking_no like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and d.po_number like '$search_string'";
		else if ($search_by == 3)
			$search_field_cond = "and c.job_no like '$search_string'";
		else
			$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
	} 
	else 
	{
		$search_field_cond = "";
	}

	if ($batch_against == 1) 
	{
		if ($db_type == 0) 
		{
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no,group_concat(distinct(d.id)) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0  $search_field_cond $groupby_field";
		} 
		else if ($db_type == 2) 
		{
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no, listagg(d.id,',') within group (order by d.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0  $search_field_cond $groupby_field";
		}
	} 
	else 
	{
		if ($search_by == 1)
			$search_field_cond_sample = "and s.booking_no like '$search_string'";
		else if ($search_by == 4)
			$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-", 1) . "'";
		else
			$search_field_cond_sample = "";
		if ($db_type == 0) 
		{
			$sql = "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no, c.style_ref_no, group_concat(distinct(d.id)) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d WHERE a.booking_no=b.booking_no and b.po_break_down_id=d.id and a.company_id=$company_id and a.buyer_id=$buyer_id and c.job_no=d.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $search_field_cond $groupby_u_field
			union all
			SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, NULL as job_no, NULL as style_ref_no, NULL as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.buyer_id=$buyer_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0  $search_field_cond_sample $groupby_d_field  
			";
		} 
		else if ($db_type == 2) 
		{
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
			foreach ($result as $row) 
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$po_no = "";
				$po_id = array_unique(explode(",", $row[csf('po_id')]));

				foreach ($po_id as $val) 
				{
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
	$sql = "select id, sub_process_id as sub_process_id,process_remark from pro_recipe_entry_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id";
	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) 
	{
		if (!in_array($row[csf("sub_process_id")], $process_array)) 
		{
			$process_array[] = $row[csf("sub_process_id")];
			$process_array_remark[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
		}
	}
	foreach ($process_array as $sub_provcess_id) 
	{
		$process_remark = $process_array_remark[$sub_provcess_id];
		?>
        <h3 align="left" id="accordion_h<? echo $sub_provcess_id; ?>" style="width:910px" class="accordion_h"
            onClick="fnc_item_details(<? echo $sub_provcess_id; ?>,'<? echo $process_remark; ?>')"><span
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
        function js_set_value(batch_id) 
        {
            //alert (batch_id);
            document.getElementById('hidden_batch_id').value = batch_id;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:600px;margin-left:0px;">
            <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
                    <thead>
                    <tr>
                        <th colspan="5">
							<?
							echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
							?>
                        </th>
                    </tr>
                    <tr>
                        <th>Batch Type</th>
                        <th class="" >Batch</th>
                        <th>From Date</th>
                        <th>To Date</th>
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
							echo create_drop_down("cbo_search_by", 150, $order_source, "", 1, "--Select--", 2, 0, 0);
							?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/>
                        </td>
                        <td>
                        	<input id="txt_date_from" class="datepicker" type="text" value="<? echo date('d-m-Y', strtotime(date("d-m-Y") . "-30days"));?>" style="width:70px;" name="txt_date_from">
                        </td>
                        <td>
                        	<input id="txt_date_to" class="datepicker" type="text" value="<? echo date("d-m-Y");?>" style="width:70px;" name="txt_date_to">
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'recipe_entry_controller', 'setFilterGrid(\'list_view\',-1);')"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center">
							<? echo load_month_buttons(1); ?>
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
	//print_r ($data);
	$data = explode('_', $data);
	$search_common = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$search_type = $data[3];
	$txt_date_from = $data[4];
	$txt_date_to = $data[5];

	if ($search_common == "") 
	{
		if($db_type==0)
		{ 
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd'); 
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd'); 
		}
		else
		{ 
			$txt_date_from=change_date_format($txt_date_from, "", "",1); 
			$txt_date_to=change_date_format($txt_date_to, "", "",1); 
		}

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.batch_date between '$txt_date_from' and '$txt_date_to'";
		
		if($date_cond== "")
		{
			echo "<p style='color:firebrick; text-align: center; font-weight: bold;'>Either Batch No Or Date is required</p>";
			exit;
		}
	}
	else
	{
		$date_cond="";
	}

	if ($search_type == 1) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no='$search_common'"; else $batch_cond = "";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 2) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 3) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common'"; else $batch_cond = "";
	}

	if ($search_by == 1) 
	{
		$batch_type_cond = " and a.entry_form in(0,74)";
	} 
	else if ($search_by == 2) 
	{
		$batch_type_cond = " and a.entry_form in (36,150)";
	} 
	else 
	{
		$batch_type_cond = " and a.entry_form in (0,36,74,150)";
	}

	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$sub_po_arr = return_library_array("select id,order_no from  subcon_ord_dtls", 'id', 'order_no');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	if ($db_type == 0) 
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form,sum(b.roll_no) as roll_no, group_concat(b.po_id) as po_id from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond $date_cond group by a.id, a.batch_no, a.extention_no order by a.id DESC";
	} 
	else if ($db_type == 2) 
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, sum(b.roll_no) as roll_no, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_type_cond $batch_cond $date_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form order by a.id DESC";
	}
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table">
            <thead>
            <th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="40">Ex.</th>
            <th width="90">Color</th>
            <th width="80">Batch Weight</th>
            <th width="80">Batch Qty(Pcs)</th>
            <th width="70">Batch Date</th>
            <th>PO No.</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view">
				<?
				$i = 1;
				$nameArray = sql_select($sql);
				foreach ($nameArray as $selectResult) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$order_no = '';
					$order_id = array_unique(explode(",", $selectResult[csf("po_id")]));
					foreach ($order_id as $val) 
					{
						if ($selectResult[csf("entry_form")] == 36 || $selectResult[csf("entry_form")] == 150) 
						{
							if ($order_no == "") $order_no = $sub_po_arr[$val]; else $order_no .= ", " . $sub_po_arr[$val];
						} 
						else 
						{
							if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
						}
					}
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                        id="search<? echo $i; ?>" onClick="js_set_value('<? echo $selectResult[csf('id')]; ?>')">
                        <td width="30"><? echo $i; ?></td>
                        <td width="70"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                        <td width="40"><? echo $selectResult[csf('extention_no')]; ?>&nbsp;</td>
                        <td width="90"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                        <td width="80" align="right"><p><? echo $selectResult[csf('batch_weight')]; ?></p></td>
                        <td width="80" align="right"><p>&nbsp;<? echo $selectResult[csf('roll_no')]; ?></p></td>
                        <td width="70" align="center">
                            <p><? echo change_date_format($selectResult[csf('batch_date')]); ?></p></td>
                        <td><p><? echo $order_no; ?>&nbsp;</p></td>
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
if ($action == "ratio_data_from_dtls") 
{
	$ex_data = explode('**', $data);
	$company = $ex_data[0];
	$sub_process = $ex_data[1];
	$update_id = $ex_data[2];
	/*if ($sub_process == 93 || $sub_process == 94 || $sub_process == 95 || $sub_process == 96 || $sub_process == 97 || $sub_process == 98) 
	{
		$sql_rec_dtls = "select id,sub_process_id,process_remark,liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id=" . $update_id . " and sub_process_id=$sub_process ";
	} 
	else 
	{*/
		$sql_rec_dtls = "select id,sub_process_id,process_remark,liquor_ratio,total_liquor,recipe_time,recipe_temperature,recipe_ph from pro_recipe_entry_dtls where mst_id=" . $update_id . " and sub_process_id=$sub_process and ratio>0";
	//}
	//echo $sql_rec_dtls;
	$result_dtl = sql_select($sql_rec_dtls);
	foreach ($result_dtl as $row) 
	{
		//txt_total_liquor_ratio*txt_liquor_ratio_dtls
		echo "document.getElementById('txt_time').value 			= '" . $row[csf("recipe_time")] . "';\n";
		echo "document.getElementById('txt_temparature').value 		= '" . $row[csf("recipe_temperature")] . "';\n";
		echo "document.getElementById('txt_ph').value 				= '" . $row[csf("recipe_ph")] . "';\n";

		echo "document.getElementById('txt_liquor_ratio_dtls').value 		= '" . $row[csf("liquor_ratio")] . "';\n";
		echo "document.getElementById('txt_total_liquor_ratio').value 			= '" . $row[csf("total_liquor")] . "';\n";
		echo "caculate_tot_liquor();\n";
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

	if ($db_type == 0) 
	{
		$sql = "select a.id, a.batch_no, a.extention_no,a.batch_against, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$ex_data[0]' and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no order by a.id DESC";
	} 
	else if ($db_type == 2) 
	{
		$sql = "select a.id, a.batch_no,a.batch_against, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$ex_data[0]' and a.id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.batch_against, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form order by a.id DESC";
	}
	//echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$order_no = '';
		$buyer_id = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		foreach ($order_id as $val) 
		{
			if ($row[csf("entry_form")] == 36 || $row[csf("entry_form")] == 150) 
			{
				if ($order_no == "") $order_no = $sub_po_arr[$val]; else $order_no .= ", " . $sub_po_arr[$val];
				if ($buyer_id == "") $buyer_id = $sub_buyer_arr[$val]; else $buyer_id .= "," . $sub_buyer_arr[$val];
			} 
			else 
			{
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$po_id = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$prod_id = implode(",", array_unique(explode(",", $row[csf("prod_id")])));

		if ($row[csf("entry_form")] == 36 || $row[csf("entry_form")] == 150) 
		{
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			$ord_source = 2;
			$buyer_name = implode(',', array_unique(explode(",", $buyer_id)));
		} 
		else 
		{
			$batch_type = "<b> SELF ORDER </b>";
			$ord_source = 1;
			if ($row[csf("entry_form")] == 74) 
			{
				$result = sql_select("select c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id='$ex_data[1]' and a.status_active=1 and a.is_deleted=0");
				$buyer_name = $result[0][csf('buyer_name')];
			} 
			else 
			{
				if ($row[csf("batch_against")] == 3) 
				{
					$buyer_name = $sample_buyer_arr[$row[csf("booking_no")]];
				} 
				else 
				{
					$buyer_name = $buyer_arr[$row[csf("booking_no")]];
				}
			}
		}

		echo "document.getElementById('cbo_order_source').value 		= '" . $ord_source . "';\n";
		echo "document.getElementById('txt_batch_no').value 			= '" . $row[csf("batch_no")] . "';\n";
		echo "document.getElementById('txt_batch_weight').value 		= '" . $row[csf("batch_weight")] . "';\n";
		echo "document.getElementById('txt_booking_order').value 		= '" . $row[csf("booking_no")] . "';\n";
		echo "document.getElementById('txt_booking_id').value 			= '" . $row[csf("booking_no_id")] . "';\n";
		echo "document.getElementById('cbo_buyer_name').value 			= '" . $buyer_name . "';\n";
		echo "document.getElementById('txt_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('txt_color_id').value 			= '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('cbo_color_range').value 			= '" . $row[csf("color_range_id")] . "';\n";
		echo "document.getElementById('txt_trims_weight').value 		= '" . $row[csf("total_trims_weight")] . "';\n";
		echo "document.getElementById('txt_order').value 				= '" . $order_no . "';\n";
		//echo "document.getElementById('batch_type').innerHTML 			= '" . $batch_type . "';\n";
		echo "get_php_form_data('" . $po_id . "'+'**'+'" . $prod_id . "', 'lode_data_from_grey_production', 'requires/recipe_entry_controller');\n";
		echo "caculate_tot_liquor();\n";
	}
	exit();
}

if ($action == "lode_data_from_grey_production") 
{
	$ex_data = explode('**', $data);
	$po_id = str_replace("'", "", $ex_data[0]);
	$prod_id = str_replace("'", "", $ex_data[1]);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	if ($db_type == 0) 
	{
		$sql_prod = "Select group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22)";
	} 
	else if ($db_type == 2) 
	{
		$sql_prod = "Select listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(cast(a.brand_id as varchar2(4000)),',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22)";
	}

	$result_sql_prod = sql_select($sql_prod);
	foreach ($result_sql_prod as $row) 
	{
		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) 
		{
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) 
		{
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		echo "document.getElementById('txt_yarn_lot').value 			= '" . $yarn_lot . "';\n";
		echo "document.getElementById('txt_brand').value 				= '" . $brand_name . "';\n";
		echo "document.getElementById('txt_count').value 				= '" . $count_name . "';\n";

		exit();
	}
}

if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');

	$recipe_data_arr=array(); $recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!="")
	{	//sum(b.req_qny_edit) as qnty
		$iss_arr=return_library_array("select b.product_id, sum(b.required_qnty) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id",'product_id','qnty');

		/*if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98)
		{
			$ration_cond="";
		}
		else
		{*/
			$ration_cond=" and ratio>0 ";
		//}
		//echo "select prod_id, id, item_lot,comments,dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no";
		$recipeData=sql_select("select prod_id, id, item_lot,comments,dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
		foreach($recipeData as $row)
		{
			$recipe_data_arr[$row[csf('prod_id')]][1]=$row[csf('item_lot')];
			$recipe_data_arr[$row[csf('prod_id')]][2]=$row[csf('dose_base')];
			$recipe_data_arr[$row[csf('prod_id')]][3]=$row[csf('ratio')];
			$recipe_data_arr[$row[csf('prod_id')]][4]=$row[csf('seq_no')];
			$recipe_data_arr[$row[csf('prod_id')]][5]=$row[csf('id')];
			$recipe_data_arr[$row[csf('prod_id')]][6]=$row[csf('comments')];
			$recipe_prod_id_arr[]=$row[csf('prod_id')];
		}
	}

	//var_dump($recipe_prod_id_arr);

	$sql="select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
	//echo $sql;
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$product_data_arr[$row[csf('id')]]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')];
	}

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" >
            <thead>
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
            </thead>
        </table>
        <div style="width:1050px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1032" class="rpt_table" id="tbl_list_search">
                <tbody>
				<?
				/*if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98) //Wash start...
				{
					$i=1; //$max_seq_no='';
					//if(count($recipe_prod_id_arr)>0)
					//{
					//echo $sub_process_id.'dsdsdsdd';

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
						if($update_id!="" && $ratio>0 && $iss_qty>0)
						{
							$disbled="disabled='disabled'";
						}

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                            <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                            <td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
                            <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                            <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                            <td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
                            <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" onDblClick="openmypage_itemLot(<? echo $i; ?>)" placeholder="Browse" value="<? echo $item_lot; ?>">
                            </td>
                            <td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
                            <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
                            <td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
                            <td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
                            <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId; ?>"></td>
                            <td align="right" width="70" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?></td>
                            <td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
                        </tr>
						<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
						$i++;
					}
					//}

				}
				else 				//Wash End....
				{*/
					$i=1; //$max_seq_no='';
					if(count($recipe_prod_id_arr)>0)
					{
						//echo $sub_process_id.'dsdsdsdd';

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

							$item_lot=$recipe_data_arr[$prodId][1];
							$dtls_id=$recipe_data_arr[$prodId][5];
							$ratio=$recipe_data_arr[$prodId][3];
							$seq_no=$recipe_data_arr[$prodId][4];
							$comments=$recipe_data_arr[$prodId][6];
							$bgcolor="yellow";

							$selected_dose=$recipe_data_arr[$prodId][2];
							$disbled="";
							$iss_qty=$iss_arr[$prodId];
							if($update_id!="" && $ratio>0 && $iss_qty>0)
							{
								$disbled="disabled='disabled'";
							}

							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                                <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                                <td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
                                <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" onDblClick="openmypage_itemLot(<? echo $i; ?>)" placeholder="Browse" value="<? echo $item_lot; ?>">
                                </td>
                                <td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
                                <td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
                                <td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>"></td>
                                <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId; ?>"></td>
                                <td align="right" width="70" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?></td>
                                <td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
                            </tr>
							<?
							//$max_seq_no[]=$selectResult[csf('seq_no')];
							$i++;
						}
					}

					foreach($product_data_arr as $prodId=>$data)
					{
						if(!in_array($prodId,$recipe_prod_id_arr))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$prodData=explode("**",$data);
							$item_category_id=$prodData[0];
							$item_group_id=$prodData[1];
							$sub_group_name=$prodData[2];
							$item_description=$prodData[3];
							$item_size=$prodData[4];
							$unit_of_measure=$prodData[5];
							$current_stock=$prodData[6];

							$ratio=''; $seq_no=''; $disbled="";$comments='';
							if($item_category_id==6)
							{
								$selected_dose=2;
							}
							else
							{
								$selected_dose=1;
							}

							//$td_color="";
							//if($selectResult[csf('current_stock')]<=0) $td_color="#FF0000"; else $td_color="";
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                                <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
                                <td width="130" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
                                <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" onDblClick="openmypage_itemLot(<? echo $i; ?>)" placeholder="Browse" value="<? echo $selectResult[csf('item_lot')]; ?>">
                                </td>
                                <td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                                <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;"  value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); "  <? echo $disbled; ?>></td>
                                <td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);"></td>
                                <td  width="100" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? //echo $dtls_id; ?>"></td>
                                <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" value="<? echo $prodId; ?>"></td>
                                <td align="right" width="70" id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock,2,'.',''); ?></td>
                                <td width="" align="center" id="comments_<? echo $i; ?>"><input type="text" name="txt_comments[]" id="txt_comments_<? echo $i; ?>" class="text_boxes" style="width:80px" value="<? echo $comments; ?>"></td>
                            </tr>
							<?
							//$max_seq_no[]=$selectResult[csf('seq_no')];
							$i++;
						}
					}
			//	}
				?>
                </tbody>
            </table>
        </div>
    </div>
	<?
	exit();
}


if ($action == "item_details_old") 
{
	$data = explode("**", $data);
	$company_id = $data[0];
	$sub_process_id = $data[1];
	$update_id = $data[2];
	$copy_val = $data[3];
	$product_ids = "";

	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');

	$recipe_data_arr = array();
	$recipe_prod_id_arr = array();
	$product_data_arr = array();
	/*if ($sub_process_id == 93 || $sub_process_id == 94 || $sub_process_id == 95 || $sub_process_id == 96 || $sub_process_id == 97 || $sub_process_id == 98) 
	{
		$ration_cond = "";
	} 
	else 
	{*/
		$ration_cond = " and ratio>0 ";
//	}
	if ($update_id != "") 
	{    //sum(b.req_qny_edit) as qnty
		$iss_arr = return_library_array("select b.product_id, sum(b.required_qnty) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id", 'product_id', 'qnty');

		$recipeData = sql_select("select prod_id, id, item_lot,comments,dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");

		foreach ($recipeData as $row) 
		{
			$recipe_data_arr[$row[csf('prod_id')]][1] = $row[csf('item_lot')];
			$recipe_data_arr[$row[csf('prod_id')]][2] = $row[csf('dose_base')];
			$recipe_data_arr[$row[csf('prod_id')]][3] = $row[csf('ratio')];
			$recipe_data_arr[$row[csf('prod_id')]][4] = $row[csf('seq_no')];
			$recipe_data_arr[$row[csf('prod_id')]][5] = $row[csf('id')];
			$recipe_data_arr[$row[csf('prod_id')]][6] = $row[csf('comments')];

			$recipe_prod_id_arr[] = $row[csf('prod_id')];
			//$product_ids .= $row[csf('prod_id')] . ",";
		}
	}
	$product_ids = implode(",", $recipe_prod_id_arr);
	//var_dump($recipe_prod_id_arr);

	$sql = "select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock
	from product_details_master
	where company_id='$company_id'
	and item_category_id in(5,6,7) and status_active=1 and is_deleted=0 and id not in($product_ids)";
	//echo $sql;

	$nameArray = sql_select($sql);
	foreach ($nameArray as $row) 
	{
		$product_data_arr[$row[csf('id')]] = $row[csf('item_category_id')] . "**" . $row[csf('item_group_id')] . "**" . $row[csf('sub_group_name')] . "**" . $row[csf('item_description')] . "**" . $row[csf('item_size')] . "**" . $row[csf('unit_of_measure')] . "**" . $row[csf('current_stock')];
	}

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table">
            <thead>
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
            </thead>
        </table>
        <div style="width:1050px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1032" class="rpt_table"
                   id="tbl_list_search">
                <tbody>
				<?
				/*if ($sub_process_id == 93 || $sub_process_id == 94 || $sub_process_id == 95 || $sub_process_id == 96 || $sub_process_id == 97 || $sub_process_id == 98) //Wash start...
				{
					$i = 1; //$max_seq_no='';

					foreach ($recipe_prod_id_arr as $prodId) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$prodData = explode("**", $product_data_arr[$prodId]);
						$item_category_id = $prodData[0];
						$item_group_id = $prodData[1];
						$sub_group_name = $prodData[2];
						$item_description = $prodData[3];
						$item_size = $prodData[4];
						$unit_of_measure = $prodData[5];
						$current_stock = $prodData[6];

						$item_lot = $recipe_data_arr[$prodId][1];
						$dtls_id = $recipe_data_arr[$prodId][5];
						//echo $dtls_id.'saa';
						$ratio = $recipe_data_arr[$prodId][3];
						$seq_no = $recipe_data_arr[$prodId][4];
						$comments = $recipe_data_arr[$prodId][6];
						$bgcolor = "yellow";

						$selected_dose = $recipe_data_arr[$prodId][2];
						$disbled = "";
						$iss_qty = $iss_arr[$prodId];
						if ($update_id != "" && $ratio > 0 && $iss_qty > 0) 
						{
							$disbled = "disabled='disabled'";
						}
						if ($copy_val == 1 && $current_stock <= 0) 
						{
							$fontcolor = 'style="color:#FF0000"';
						} 
						else 
						{
							$fontcolor = '';
						}
						?>
                        <tr <? echo $fontcolor; ?> bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                            <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                            <td width="80" id="item_category_<? echo $i; ?>">
                                <p><? echo $item_category[$item_category_id]; ?></p></td>
                            <td width="100" id="item_group_id_<? echo $i; ?>">
                                <p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                            <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p>
                            </td>
                            <td width="130" id="item_description_<? echo $i; ?>">
                                <p><? echo $item_description . " " . $item_size; ?></p></td>
                            <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]"
                                                                               id="txt_item_lot_<? echo $i; ?>"
                                                                               class="text_boxes" style="width:68px"
                                                                               onDblClick="openmypage_itemLot(<? echo $i; ?>)"
                                                                               placeholder="Browse"
                                                                               value="<? echo $item_lot; ?>">
                            </td>
                            <td width="40" align="center"
                                id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td width="70" align="center"
                                id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-", $selected_dose); ?></td>
                            <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text"
                                                                                           name="txt_ratio[]"
                                                                                           id="txt_ratio_<? echo $i; ?>"
                                                                                           class="text_boxes_numeric"
                                                                                           style="width:40px;"
                                                                                           value="<? echo $ratio; ?>"
                                                                                           onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); " <? echo $disbled; ?>>
                            </td>
                            <td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text"
                                                                                           name="txt_seqno[]"
                                                                                           id="txt_seqno_<? echo $i; ?>"
                                                                                           class="text_boxes_numeric"
                                                                                           style="width:30px"
                                                                                           value="<? echo $seq_no; ?>"
                                                                                           onBlur="row_sequence(<? echo $i; ?>);">
                            </td>
                            <td width="100" id="sub_process_<? echo $i; ?>">
                                <p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden"
                                                                                              name="updateIdDtls[]"
                                                                                              id="updateIdDtls_<? echo $i; ?>"
                                                                                              value="<? echo $dtls_id; ?>">
                            </td>
                            <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input
                                        type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
                                        value="<? echo $prodId; ?>"></td>
                            <td align="right" width="70" title="<? echo $copy_val; ?>"
                                id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock, 2, '.', ''); ?></td>
                            <td width="" align="center" id="comments_<? echo $i; ?>"><input type="text"
                                                                                            name="txt_comments[]"
                                                                                            id="txt_comments_<? echo $i; ?>"
                                                                                            class="text_boxes"
                                                                                            style="width:80px"
                                                                                            value="<? echo $comments; ?>">
                            </td>
                        </tr>
						<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
						$i++;
					}
					//}

				} 
				else                //Wash End....
				{*/
					$i = 1; //$max_seq_no='';
					if (count($recipe_prod_id_arr) > 0) 
					{
						//echo $sub_process_id.'dsdsdsdd';
						//echo "<pre>";
						//print_r($recipe_prod_id_arr); die;
						foreach ($recipe_prod_id_arr as $prodId) 
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$prodData = explode("**", $product_data_arr[$prodId]);
							$item_category_id = $prodData[0];
							$item_group_id = $prodData[1];
							$sub_group_name = $prodData[2];
							$item_description = $prodData[3];
							$item_size = $prodData[4];
							$unit_of_measure = $prodData[5];
							$current_stock = $prodData[6];

							$item_lot = $recipe_data_arr[$prodId][1];
							$dtls_id = $recipe_data_arr[$prodId][5];
							$ratio = $recipe_data_arr[$prodId][3];
							$seq_no = $recipe_data_arr[$prodId][4];
							$comments = $recipe_data_arr[$prodId][6];
							$bgcolor = "yellow";

							$selected_dose = $recipe_data_arr[$prodId][2];
							$disbled = "";
							$iss_qty = $iss_arr[$prodId];
							if ($update_id != "" && $ratio > 0 && $iss_qty > 0) 
							{
								$disbled = "disabled='disabled'";
							}
							if ($copy_val == 1 && $current_stock <= 0) 
							{
								$fontcolor = 'style="color:#FF0000"';
							} 
							else 
							{
								$fontcolor = "";
							}
							?>
                            <tr <? echo $fontcolor; ?> bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="80" id="item_category_<? echo $i; ?>">
                                    <p><? echo $item_category[$item_category_id]; ?></p></td>
                                <td width="100" id="item_group_id_<? echo $i; ?>">
                                    <p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                                <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>
                                        &nbsp;</p></td>
                                <td width="130" id="item_description_<? echo $i; ?>">
                                    <p><? echo $item_description . " " . $item_size; ?></p></td>
                                <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]"
                                                                                   id="txt_item_lot_<? echo $i; ?>"
                                                                                   class="text_boxes" style="width:68px"
                                                                                   onDblClick="openmypage_itemLot(<? echo $i; ?>)"
                                                                                   placeholder="Browse"
                                                                                   value="<? echo $item_lot; ?>">
                                </td>
                                <td width="40" align="center"
                                    id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>
                                    &nbsp;</td>
                                <td width="70" align="center"
                                    id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-", $selected_dose); ?></td>
                                <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text"
                                                                                               name="txt_ratio[]"
                                                                                               id="txt_ratio_<? echo $i; ?>"
                                                                                               class="text_boxes_numeric"
                                                                                               style="width:40px;"
                                                                                               value="<? echo $ratio; ?>"
                                                                                               onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); " <? echo $disbled; ?>>
                                </td>
                                <td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text"
                                                                                               name="txt_seqno[]"
                                                                                               id="txt_seqno_<? echo $i; ?>"
                                                                                               class="text_boxes_numeric"
                                                                                               style="width:30px"
                                                                                               value="<? echo $seq_no; ?>"
                                                                                               onBlur="row_sequence(<? echo $i; ?>);">
                                </td>
                                <td width="100" id="sub_process_<? echo $i; ?>">
                                    <p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden"
                                                                                                  name="updateIdDtls[]"
                                                                                                  id="updateIdDtls_<? echo $i; ?>"
                                                                                                  value="<? echo $dtls_id; ?>">
                                </td>
                                <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input
                                            type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
                                            value="<? echo $prodId; ?>"></td>
                                <td align="right" width="70" title="<? echo $copy_val; ?>"
                                    id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock, 2, '.', ''); ?></td>
                                <td width="" align="center" id="comments_<? echo $i; ?>"><input type="text"
                                                                                                name="txt_comments[]"
                                                                                                id="txt_comments_<? echo $i; ?>"
                                                                                                class="text_boxes"
                                                                                                style="width:80px"
                                                                                                value="<? echo $comments; ?>">
                                </td>
                            </tr>
							<?
							//$max_seq_no[]=$selectResult[csf('seq_no')];
							$i++;
						}
					}

					foreach ($product_data_arr as $prodId => $data) 
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$prodData = explode("**", $data);
						$item_category_id = $prodData[0];
						$item_group_id = $prodData[1];
						$sub_group_name = $prodData[2];
						$item_description = $prodData[3];
						$item_size = $prodData[4];
						$unit_of_measure = $prodData[5];
						$current_stock = $prodData[6];

						$ratio = '';
						$seq_no = '';
						$disbled = "";
						$comments = '';
						if ($item_category_id == 6) 
						{
							$selected_dose = 2;
						} 
						else 
						{
							$selected_dose = 1;
						}

						//$td_color="";
						//if($selectResult[csf('current_stock')]<=0) $td_color="#FF0000"; else $td_color="";
						if ($copy_val == 1 && $current_stock <= 0) 
						{
							$fontcolor = 'style="color:#FF0000"';
						} 
						else 
						{
							$fontcolor = "";
						}
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                            <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                            <td width="80" id="item_category_<? echo $i; ?>">
                                <p><? echo $item_category[$item_category_id]; ?></p></td>
                            <td width="100" id="item_group_id_<? echo $i; ?>">
                                <p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                            <td width="70" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>
                                    &nbsp;</p></td>
                            <td width="130" id="item_description_<? echo $i; ?>">
                                <p><? echo $item_description . " " . $item_size; ?></p></td>
                            <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]"
                                                                               id="txt_item_lot_<? echo $i; ?>"
                                                                               class="text_boxes" style="width:68px"
                                                                               onDblClick="openmypage_itemLot(<? echo $i; ?>)"
                                                                               placeholder="Browse"
                                                                               value="<? echo $selectResult[csf('item_lot')]; ?>">
                            </td>
                            <td width="40" align="center"
                                id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>
                                &nbsp;</td>
                            <td width="70" align="center"
                                id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-", $selected_dose); ?></td>
                            <td width="50" align="center" id="ratio_<? echo $i; ?>"><input type="text"
                                                                                           name="txt_ratio[]"
                                                                                           id="txt_ratio_<? echo $i; ?>"
                                                                                           class="text_boxes_numeric"
                                                                                           style="width:40px;"
                                                                                           value="<? echo $ratio; ?>"
                                                                                           onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>); " <? echo $disbled; ?>>
                            </td>
                            <td width="40" align="center" id="seqno_<? echo $i; ?>"><input type="text"
                                                                                           name="txt_seqno[]"
                                                                                           id="txt_seqno_<? echo $i; ?>"
                                                                                           class="text_boxes_numeric"
                                                                                           style="width:30px"
                                                                                           value="<? echo $seq_no; ?>"
                                                                                           onBlur="row_sequence(<? echo $i; ?>);">
                            </td>
                            <td width="100" id="sub_process_<? echo $i; ?>">
                                <p><? echo $dyeing_sub_process[$sub_process_id]; ?></p><input type="hidden"
                                                                                              name="updateIdDtls[]"
                                                                                              id="updateIdDtls_<? echo $i; ?>"
                                                                                              value="<? //echo $dtls_id;
																							  ?>"></td>
                            <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $prodId; ?><input
                                        type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>"
                                        value="<? echo $prodId; ?>"></td>
                            <td align="right" width="70" title="<? echo $copy_val; ?>"
                                id="stock_qty_<? echo $i; ?>"><? echo number_format($current_stock, 2, '.', ''); ?></td>
                            <td width="" align="center" id="comments_<? echo $i; ?>"><input type="text"
                                                                                            name="txt_comments[]"
                                                                                            id="txt_comments_<? echo $i; ?>"
                                                                                            class="text_boxes"
                                                                                            style="width:80px"
                                                                                            value="<? echo $comments; ?>">
                            </td>
                        </tr>
						<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
						$i++;
					}
			//	}
				?>
                </tbody>
            </table>
        </div>
    </div>
	<?
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

        function toggle(x, origColor) 
        {
            var newColor = 'yellow';
            if (x.style) 
            {
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

    </script>
    <input type="hidden" id="prod_id"/><input type="hidden" id="item_lot"/>
	<?
	if ($db_type == 0) 
	{
		$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '";
	} 
	elseif ($db_type == 2) 
	{
		$sql = "SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=' '";
	}
	//echo $sql;

	echo create_list_view("list_view", "Item Lot", "200", "330", "250", 0, $sql, "js_set_value", "batch_lot", "", 1, "", 0, "batch_lot", "recipe_entry_controller", 'setFilterGrid("list_view",-1);', '0', '', 1);
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
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$recipe_update_id = '';
		//$color_id = return_id($txt_color, $color_arr, "lib_color", "id,color_name");
		

		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","151");
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
		if (str_replace("'", "", $copy_id) == 1) 
		{
			$recipe_no = return_field_value("id", "pro_recipe_entry_mst", "batch_id=" . $batch_id . " and entry_form=151", "id");
			if ($recipe_no != '') 
			{
				echo "14**0**$recipe_no";
				disconnect($con);
				die;
			}
		}
		if (str_replace("'", "", $update_id) == "") 
		{
			/*if(is_duplicate_field( "labdip_no", "pro_recipe_entry_mst", "labdip_no=$txt_labdip_no" )==1)
			{
				echo "11**0";
				die;
			}*/
			
			$id = return_next_id("id", "pro_recipe_entry_mst", 1);

			$field_array = "id, entry_form, labdip_no, company_id, working_company_id, location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, color_id, buyer_id, color_range, booking_type, total_liquor, batch_ratio, liquor_ratio,batch_qty, remarks, inserted_by, insert_date";
			//echo $txt_liquor;
			$data_array = "(" . $id . ",151," . $txt_labdip_no . "," . $cbo_company_id . "," . $cbo_company_id . "," . $cbo_location . "," . $txt_recipe_des . "," . $txt_batch_id . "," . $cbo_method . "," . $txt_recipe_date . "," . $cbo_order_source . "," . $txt_booking_order . "," . $txt_booking_id . ",'" . $color_id . "'," . $cbo_buyer_name . "," . $cbo_color_range . "," . $txt_booking_type . "," . $txt_liquor . "," . $txt_batch_ratio . "," . $txt_liquor_ratio . "," . $txt_batch_weight . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";


			//$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=0;
			$recipe_update_id = $id;
		} 
		else 
		{
			
			if (is_duplicate_field("sub_process_id", "pro_recipe_entry_dtls", "mst_id=$update_id and sub_process_id=$cbo_sub_process") == 1) 
			{
				echo "11**0";
				disconnect($con);
				die;
			}
			$field_array_update = "labdip_no*company_id*working_company_id*location_id*recipe_description*batch_id*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*booking_id*booking_type*total_liquor*batch_ratio*liquor_ratio*batch_qty*remarks*updated_by*update_date";

			$data_array_update = $txt_labdip_no . "*" . $cbo_company_id . "*" . $cbo_company_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $txt_recipe_date . "*" . $cbo_order_source . "*" . $txt_booking_order . "*" . $color_id . "*" . $cbo_buyer_name . "*" . $cbo_color_range . "*" . $txt_booking_id . "*" . $txt_booking_type . "*" . $txt_liquor . "*" . $txt_batch_ratio . "*" . $txt_liquor_ratio . "*" . $txt_batch_weight . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0;
			$recipe_update_id = str_replace("'", "", $update_id);
		}

		if (str_replace("'", "", $copy_id) == 2) 
		{
			/*if ($subprocess == 93 || $subprocess == 94 || $subprocess == 95 || $subprocess == 96 || $subprocess == 97 || $subprocess == 98) 
			{
				$txt_comments_1 = str_replace("'", "", $txt_comments_1);
				$txt_ratio_1 = str_replace("'", "", $txt_ratio_1);
				$txt_seqno_1 = str_replace("'", "", $txt_seqno_1);
				$cbo_dose_base_1 = str_replace("'", "", $cbo_dose_base_1);

				$field_array_dtls = "id,mst_id,sub_process_id,process_remark,comments,liquor_ratio,total_liquor,ratio,seq_no,dose_base,inserted_by,insert_date";
				$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
				$data_array_dtls = "(" . $dtls_id . "," . $recipe_update_id . "," . $cbo_sub_process . "," . $txt_subprocess_remarks . ",'" . $txt_comments_1 . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . $txt_ratio_1 . "','" . $txt_seqno_1 . "','" . $cbo_dose_base_1 . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			} 
			else 
			{*/

				$field_array_dtls = "id,mst_id,sub_process_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,ratio,seq_no,inserted_by,insert_date,recipe_time,recipe_temperature,recipe_ph";
				$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);

				for ($i = 1; $i <= $total_row; $i++) 
				{
					$product_id = "product_id_" . $i;
					$txt_item_lot = "txt_item_lot_" . $i;
					$cbo_dose_base = "cbo_dose_base_" . $i;
					$txt_ratio = "txt_ratio_" . $i;
					$txt_comments = "txt_comments_" . $i;
					$txt_seqno = "txt_seqno_" . $i;
					if ($i != 1) $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . "," . $cbo_sub_process . ",'" . $txt_subprocess_remarks . "','" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_item_lot) . "','" . str_replace("'", "", $$txt_comments) . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . str_replace("'", "", $$cbo_dose_base) . "','" . str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_time . "," . $txt_temparature . "," . $txt_ph . ")";

					$dtls_id = $dtls_id + 1;
				}
				//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		//	}

		} 
		else if (str_replace("'", "", $copy_id) == 1) 
		{
		
			$field_array_dtls = "id,mst_id,sub_process_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,ratio,seq_no,inserted_by,insert_date,recipe_time,recipe_temperature,recipe_ph";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
			$sql = "select id, sub_process_id, prod_id, item_lot,comments,process_remark, dose_base,liquor_ratio,total_liquor,ratio, seq_no from pro_recipe_entry_dtls where mst_id=$update_id_check  order by id";
			$nameArray = sql_select($sql);
			$tot_row = count($nameArray);
			$i = 1;

			foreach ($nameArray as $row) 
			{

				if ($i != 1) $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",'" . $row[csf('sub_process_id')] . "','" . $row[csf('process_remark')] . "','" . $row[csf('prod_id')] . "','" . $row[csf('item_lot')] . "','" . $row[csf('comments')] . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . $row[csf('dose_base')] . "','" . $row[csf('ratio')] . "','" . $row[csf('seq_no')] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_time . "," . $txt_temparature . "," . $txt_ph . ")";
				$dtls_id = $dtls_id + 1;
				$i++;
			}
		}

		//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";
		/*$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		} */

		//test all insert
		if (str_replace("'", "", $update_id) == "") 
		{
			$rID = sql_insert("pro_recipe_entry_mst", $field_array, $data_array, 0);
			if ($rID) $flag = 1; else $flag = 0;
		} 
		else 
		{
			$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}
		$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) 
		{
			if ($rID2) $flag = 1; else $flag = 0;
		}
		//echo "10**".$rID.'=='.$rID2;die;
		if ($db_type == 0) 
		{
			if ($flag == 1) 
			{
				mysql_query("COMMIT");
				echo "0**" . $recipe_update_id . "**" . $subprocess;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}

		if ($db_type == 2 || $db_type == 1) 
		{
			if ($flag == 1) 
			{
				oci_commit($con);
				echo "0**" . $recipe_update_id . "**" . $subprocess;
			} 
			else 
			{
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
		if ($db_type == 0) 
		{
			mysql_query("BEGIN");
		}

		$batch_id = str_replace("'", "", $txt_batch_id);
		if (str_replace("'", "", $copy_id) == 1) 
		{
			$recipe_no = return_field_value("id", "pro_recipe_entry_mst", "batch_id=" . $batch_id . " and entry_form=151", "id");
			if ($recipe_no != '') 
			{
				echo "14**0**$recipe_no";
				disconnect($con);
				die;
			}
		}

		$subprocess = str_replace("'", "", $cbo_sub_process);

		//$color_id = return_id($txt_color, $color_arr, "lib_color", "id,color_name");//booking_id 	booking_type 	total_liquor
		//txt_batch_weight
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name","151");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_id]=str_replace("'","",$txt_color);

			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_id=0;
		}

		$field_array_update = "labdip_no*company_id*working_company_id*location_id*recipe_description*batch_id*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*booking_id*booking_type*total_liquor*batch_ratio*liquor_ratio*batch_qty*remarks*updated_by*update_date";

		$data_array_update = $txt_labdip_no . "*" .$cbo_company_id . "*" . $cbo_company_id . "*" . $cbo_location . "*" . $txt_recipe_des . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $txt_recipe_date . "*" . $cbo_order_source . "*" . $txt_booking_order . "*'" . $color_id . "'*" . $cbo_buyer_name . "*" . $cbo_color_range . "*" . $txt_booking_id . "*" . $txt_booking_type . "*" . $txt_liquor . "*" . $txt_batch_ratio . "*" . $txt_liquor_ratio . "*" . $txt_batch_weight . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		//echo $data_array_update;die;
		//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//if($rID) $flag=1; else $flag=0;
		/*if ($subprocess == 93 || $subprocess == 94 || $subprocess == 95 || $subprocess == 96 || $subprocess == 97 || $subprocess == 98) 
		{
			//$update_dtls_id=str_replace("'","",$updateIdDtls_1);
			$field_array_dtls_update2 = "sub_process_id*process_remark*comments*liquor_ratio*total_liquor*ratio*seq_no*dose_base*updated_by*update_date";
			//$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;
			//$data_array_dtls_update="$txt_item_lot)."'*";
			$data_array_dtls_update2 = $subprocess . "*" . $txt_subprocess_remarks . "*" . $txt_comments_1 . "*" . $txt_liquor_ratio_dtls . "*" . $txt_total_liquor_ratio . "*" . $txt_ratio_1 . "*" . $txt_seqno_1 . "*" . $cbo_dose_base_1 . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
			$update_dtls_id = str_replace("'", "", $updateIdDtls_1);


		} 
		else 
		{*/
			$field_array_dtls = "id,mst_id,sub_process_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,ratio,seq_no,inserted_by,insert_date,recipe_time,recipe_temperature,recipe_ph";
			$field_array_dtls_update = "prod_id*item_lot*comments*liquor_ratio*total_liquor*dose_base*ratio*seq_no*sub_process_id*process_remark*updated_by*update_date*recipe_time*recipe_temperature*recipe_ph";
			$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);

			for ($i = 1; $i <= $total_row; $i++) 
			{
				$product_id = "product_id_" . $i;
				$txt_item_lot = "txt_item_lot_" . $i;
				$txt_comments = "txt_comments_" . $i;
				$cbo_dose_base = "cbo_dose_base_" . $i;
				$txt_ratio = "txt_ratio_" . $i;
				$updateIdDtls = "updateIdDtls_" . $i;
				$txt_seqno = "txt_seqno_" . $i;

				if (str_replace("'", "", $$updateIdDtls) != "") 
				{
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", (str_replace("'", "", $$product_id) . "*'" . str_replace("'", "", $$txt_item_lot) . "'*'" . str_replace("'", "", $$txt_comments) . "'*" . $txt_liquor_ratio_dtls . "*" . $txt_total_liquor_ratio . "*'" . str_replace("'", "", $$cbo_dose_base) . "'*'" . str_replace("'", "", $$txt_ratio) . "'*'" . str_replace("'", "", $$txt_seqno) . "'*" . $cbo_sub_process . "*" . $txt_subprocess_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $txt_time . "*" . $txt_temparature . "*" . $txt_ph . ""));
				} 
				else 
				{
					if ($data_array_dtls != "") $data_array_dtls .= ",";

					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . "," . $cbo_sub_process . ",'" . $txt_subprocess_remarks . "','" . str_replace("'", "", $$product_id) . "','" . str_replace("'", "", $$txt_item_lot) . "','" . str_replace("'", "", $$txt_comments) . "'," . $txt_liquor_ratio_dtls . "," . $txt_total_liquor_ratio . ",'" . str_replace("'", "", $$cbo_dose_base) . "','" . str_replace("'", "", $$txt_ratio) . "','" . str_replace("'", "", $$txt_seqno) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $txt_time . "," . $txt_temparature . "," . $txt_ph . ")";

					$dtls_id = $dtls_id + 1;
				}
			}
	//	}

		// Update test all
		$rID = sql_update("pro_recipe_entry_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID) $flag = 1; else $flag = 0;
		if ($data_array_dtls_update2 != "") 
		{
			$rID = sql_update("pro_recipe_entry_dtls", $field_array_dtls_update2, $data_array_dtls_update2, "id", $update_dtls_id, 1);
			if ($rID) $flag = 1; else $flag = 0;
		}
		if ($data_array_dtls_update != "") 
		{
			$rID2 = execute_query(bulk_update_sql_statement("pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr), 1);
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($data_array_dtls != "") 
		{
			//echo "insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID2 = sql_insert("pro_recipe_entry_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($rID2) $flag = 1; else $flag = 0;
		}

		if ($db_type == 0) 
		{
			$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a, dyes_chem_requ_recipe_att b set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where a.id=b.mst_id and b.recipe_id=" . $update_id);
		} 
		else 
		{
			$reqsn_update = execute_query("update dyes_chem_issue_requ_mst a set a.is_apply_last_update=2, a.updated_by=" . $_SESSION['logic_erp']['user_id'] . ", a.update_date='" . $pc_date_time . "' where exists( select b.mst_id from dyes_chem_requ_recipe_att b where a.id=b.mst_id and b.recipe_id=" . $update_id . ")");
		}

		$reqsn_update_att = execute_query("update dyes_chem_requ_recipe_att set is_apply_last_update=2 where recipe_id=" . $update_id);

		if ($flag == 1) 
		{
			if ($reqsn_update && $reqsn_update_att) 
			{
				$flag = 1;
			} 
			else 
			{
				$flag = 0;
			}
		}

		if ($db_type == 0) 
		{
			if ($flag == 1) 
			{
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . $subprocess;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}

		if ($db_type == 2 || $db_type == 1) 
		{
			if ($flag == 1) 
			{
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . $subprocess;
			} 
			else 
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}
if ($action == "item_stock_details") 
{
	$data = explode("**", $data);
	if ($db_type == 2) $grp_con = "listagg(prod_id,',') within group (order by prod_id) as prod_id";
	else $grp_con = "group_concat(prod_id) as prod_id";

	$sql = "select $grp_con from pro_recipe_entry_dtls where   mst_id=" . $data[1] . " and is_deleted=0 and status_active=1";
	$data_recipe = sql_select($sql);
	$prod_id = $data_recipe[0][csf('prod_id')];
	$sql_stock = "select id,current_stock from product_details_master where company_id='$data[0]' and item_category_id in(5,6,7) and status_active=1 and is_deleted=0 and id in($prod_id) and current_stock<=0";
	//echo $sql;
	$item_stock = sql_select($sql_stock);
	$current_stock = $item_stock[0][csf('current_stock')];

	if ($current_stock <= 0) 
	{
		echo "1" . "_" . $item_stock[0][csf('id')];
	} 
	else 
	{
		echo "0_";
	}
	exit();
}
if ($action == "recipe_entry_print") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company=$data[0];
	$location=$data[4];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT id, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('id')]]['cust_buyer']		=$cust_val[csf('cust_buyer')]; 
		$cust_arr[$cust_val[csf('id')]]['cust_style_ref']	=$cust_val[csf('cust_style_ref')]; 
	}

	$batch_array = array();
	if ($db_type == 0) 
	{
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} 
	else if ($db_type == 2) 
	{
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight order by a.id DESC";
	}
	//echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$order_no = ''; $cust_buyers=""; $cust_style_ref="";
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36 || $row[csf("entry_form")] == 150) 
		{
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) 
			{
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];

				if ($cust_buyers=="") $cust_buyers =$cust_arr[$val]['cust_buyer']; else $cust_buyers .=", ".$cust_arr[$val]['cust_buyer'];
				if ($cust_style_ref=="") $cust_style_ref =$cust_arr[$val]['cust_style_ref']; else $cust_style_ref .=", ".$cust_arr[$val]['cust_style_ref'];
			}
		} 
		else 
		{
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) 
			{
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")]; //batch weight
		$batch_array[$row[csf("id")]]['roll_no'] = $row[csf("roll_no")]; //batch weight
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['cust_buyer'] = $cust_buyers;
		$batch_array[$row[csf("id")]]['cust_style_ref'] = $cust_style_ref;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
	}

	$sql_mst = "select id, labdip_no, company_id,location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
	$dataArray = sql_select($sql_mst);
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:930px; font-size:6px">
        <table width="930" cellspacing="0" align="right" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:x-large">
                    <strong><? echo $com_dtls[0]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
					<?

						echo $com_dtls[1];
					/*$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result) 
					{
						?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')]; ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        Zip Code: <? echo $result[csf('zip_code')];
					}*/
					?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
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
                <td><strong>Party Name:</strong></td>
                <td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
                <!-- <td><strong>Booking:</strong></td>
                <td><? //echo $dataArray[0][csf('style_or_order')]; ?></td> -->
                <td><strong>Party Buyer:</strong></td>
                <td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['cust_buyer']; ?></td>
                <td><strong>Color:</strong></td>
                <td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Color Range:</strong></td>
                <td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!-- <td><strong>B/L Ratio:</strong></td> <td><? //echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
                <td><strong>Total Liq.:</strong></td><td> <? //echo $dataArray[0][csf('total_liquor')]; ?></td>-->           
                <td><strong>Batch Weight:</strong></td>
                <td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_qty']; ?> Kg</td>
                <!-- <td><strong>Trims Weight:</strong></td>
                <td><? //echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td> -->
                <td><strong>Batch Qty(Pcs):</strong></td>
				<td> <? echo $batch_array[$dataArray[0][csf('batch_id')]]['roll_no']; ?> </td> 
            </tr>
            <tr>

                <td><strong>Order No.:</strong></td>
                <td>
					<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
					else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
                <td><strong>Method:</strong></td>
                <td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                <td><strong>Style:</strong></td>
                <td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['cust_style_ref']; ?></td>
            </tr>
            <tr>
                <td><strong>Remarks:</strong></td>
                <td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="110">Item Cat.</th>
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
				$sql = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio,recipe_time,recipe_temperature,recipe_ph from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $row) 
				{
					if (!in_array($row[csf("sub_process_id")], $process_array)) 
					{
						$process_array[] = $row[csf("sub_process_id")];
						$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
					}
					$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];

					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_time'] 			= $row[csf("recipe_time")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_temperature'] 	= $row[csf("recipe_temperature")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['recipe_ph'] 			= $row[csf("recipe_ph")];
					$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] 			= $row[csf("liquor_ratio")];

				}

				if ($db_type == 2) 
				{
					$sql = "select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by sub_process_id";
				} 
				else if ($db_type == 0) 
				{
					$sql = "select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by sub_process_id";
				}
				//echo $sql;
				$sql_result = sql_select($sql);

				foreach ($sql_result as $row) 
				{
					$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")] . ",";

				}

				foreach ($process_array as $process_id) 
				{
					$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
					$remark = $sub_process_remark_array[$process_id]['remark'];
					if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
					?>
                    <tr bgcolor="#EEEFF0">
                        <td colspan="3" align="left" style="border-right: none;"><b>Sub Process
                                Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark . $liquor_ratio; ?> (ltr)</b>

                     
                        </td>
                        <td colspan="6" style="border-left: none;">
	                    &nbsp;&nbsp;&nbsp;<strong>Ratio: </strong> <? echo $dataArray[0][csf('batch_ratio')].":".$sub_process_remark_array[$process_id]['liquor_ratio'];  ?>  
	                    &nbsp;&nbsp;&nbsp;<strong>Time:</strong> <? echo $sub_process_remark_array[$process_id]['recipe_time']; ?> min
	                    &nbsp;&nbsp;&nbsp;<strong>Temp:</strong> <? echo $sub_process_remark_array[$process_id]['recipe_temperature']; ?> &#8451;
	                    &nbsp;&nbsp;&nbsp;<strong>PH:</strong> <? echo $sub_process_remark_array[$process_id]['recipe_ph']; ?>
                        </td>
                    </tr>
					<?
					$tot_ratio = 0;
					$sub_process_data = explode(",", substr($sub_process_data_array[$process_id], 0, -1));
					foreach ($sub_process_data as $data) 
					{
						$data = explode("**", $data);
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

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i; ?></td>
                            <td><p><? echo $item_category[$item_category_id]; ?></p></td>
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
?>
