<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$action_from = $_REQUEST['action_from'];

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}


if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin($action_from,$data);
	die;
}

$distribiution_method = array(1 => "Distribute Based On Lowest Shipment Date", 2 => "Manually");

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=171 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#print1').hide();\n";
		echo "$('#print2').hide();\n";
		echo "$('#print3').hide();\n";
		echo "$('#print_barcode').hide();\n";
		echo "$('#btn_fabric_details').hide();\n";
		echo "$('#print5').hide();\n";
		echo "$('#print_mg').hide();\n";
		if($print_report_format != "")
		{
			foreach($print_report_format_arr as $id)
			{
				if($id==86){echo "$('#print1').show();\n";}
				if($id==84){echo "$('#print2').show();\n";}
				if($id==85){echo "$('#print3').show();\n";}
				if($id==68){echo "$('#print_barcode').show();\n";}
				if($id==69){echo "$('#btn_fabric_details').show();\n";}
				if($id==129){echo "$('#print5').show();\n";}
				if($id==848){echo "$('#print_mg').show();\n";}
			}
		}
		else
		{
			echo "$('#print1').show();\n";
			echo "$('#print2').show();\n";
			echo "$('#print3').show();\n";
			echo "$('#print_barcode').show();\n";
			echo "$('#btn_fabric_details').show();\n";
			echo "$('#print5').show();\n";
			echo "$('#print_mg').show();\n";
		}
	exit();
}

if ($action == "load_drop_down_location_pop")
{
	echo create_drop_down("cbo_location_id", 120, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select --", $selected, "");
	die;
}

if ($action == "grey_receive_popup_search")
{
	echo load_html_head_contents("Grey Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data, id) {
			$('#hidden_data').val(data);
			$('#hidden_recv_id').val(id);
			parent.emailwindow.hide();
		}
	</script>
    </head>

	<body>
		<div align="center" style="width:890px;">
			<form name="searchwofrm" id="searchwofrm">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="880" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Location</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="190">Receive ID</th>
						<th>Received Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:90px" class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_data" id="hidden_data">
						</th>
					</thead>
					<tr class="general">
						<td width="">
							<?
							echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'grey_fabric_receive_roll_controller', this.value, 'load_drop_down_location_pop', 'location_td' );", "");
							?>
						</td>
						<td align="center" id="location_td">
							<? echo create_drop_down("cbo_location_id", 120, $blank_array, "", 1, "-- Select --", $selected, "", 0, ""); ?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "WO/PI/Production No", 2 => "Received ID", 3 => "Challan No", 4 => "File No", 5 => "Internal Ref. No", 6 => "Barcode No");
							$dd = "change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../../') ";
							echo create_drop_down("cbo_search_by", 135, $search_by_arr, "", 0, "--Select--", 2, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_id').value, 'create_grey_recv_search_list_view', 'search_div', 'grey_fabric_receive_roll_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
			    <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
		    </form>
	    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	die;
}

if ($action == "create_grey_recv_search_list_view")
{
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]);
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$location_id = $data[5];

	if (str_replace("'", "", $company_id) == 0) {
		echo "Please Select Company First";
		die;
	}
	if ($location_id == 0) {
		$location_cond = '';
	} else {
		$location_cond = " and a.location_id='" . $location_id . "'";
	}

	$entry_form = 58;
	$garments_nature = 2;
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($db_type == 2) {
		$group_con = "LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id desc) as mst_id";
	} else {
		$group_con = "group_concat(mst_id) as mst_id";
	}
	if ($search_by == 6) {
		$barcode_no = trim($data[0]);
		if ($barcode_no != '') {
			$mst_id = return_field_value("$group_con", "pro_roll_details", "barcode_no=$barcode_no and entry_form=58 and status_active=1 and is_deleted=0 ", "mst_id");
		}
	}
	if(!$mst_id){$mst_id =0;}
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.booking_no like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and a.recv_number like '$search_string'";
		else if ($search_by == 3)
			$search_field_cond = "and a.challan_no like '$search_string'";
		else if ($search_by == 4)
			$search_field_cond = "and d.file_no like '$search_string'";
		else if ($search_by == 5)
			$search_field_cond = "and d.grouping like '$search_string'";
		else if ($search_by == 6)
			$search_field_cond = "and a.id in($mst_id)";
	} else {
		$search_field_cond = "";
	}
	//else if($search_by==2) $search_field_cond="and a.id in($mst_id)";

	//echo $mst_id;
	if ($db_type == 0) $year_field = "YEAR(a.insert_date)";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY')";
	else $year_field = "";//defined Later
	if (trim($data[0]) != "") {
		if ($search_by > 3) {
			if ($search_by == 6) {
				$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, a.booking_id, a.company_id, a.location_id, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, $year_field as year,is_posted_account from inv_receive_master a where a.entry_form=$entry_form and a.fabric_nature=$garments_nature and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $search_field_cond $date_cond $location_cond order by a.recv_number_prefix_num";
			} else {
				$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, a.booking_id, a.company_id, a.location_id, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, $year_field as year,is_posted_account
				from inv_receive_master a, inv_transaction b, order_wise_pro_details c,  wo_po_break_down d
				where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id=d.id and b.transaction_type=1 and c.trans_type=1 and c.entry_form=$entry_form and a.entry_form=$entry_form and a.fabric_nature=$garments_nature and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $search_field_cond $date_cond $location_cond
				group by a.id, a.recv_number_prefix_num, a.recv_number,a.location_id, a.booking_id, a.company_id, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, a.insert_date
				order by a.recv_number_prefix_num";
			}
		} else {
			$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, a.booking_id, a.company_id, a.location_id, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, $year_field as year,is_posted_account from inv_receive_master a where a.entry_form=$entry_form and a.fabric_nature=$garments_nature and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $search_field_cond $date_cond $location_cond order by a.recv_number_prefix_num";
		}

	} else {
		$sql = "select a.id, a.recv_number_prefix_num, a.recv_number, a.booking_id, a.company_id, a.location_id, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.challan_no, $year_field as year,is_posted_account from inv_receive_master a where a.entry_form=$entry_form and a.fabric_nature=$garments_nature and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $search_field_cond $date_cond $location_cond order by a.recv_number_prefix_num";
	}

	//echo $sql;
	$result = sql_select($sql);
	$grey_dtls_id_arr = array();
	foreach ($result as $row) {
		$grey_dtls_id_arr[$row[csf("id")]] = $row[csf("id")];

	}

	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	if(!empty($grey_dtls_id_arr))
	{
		$grey_dtls_id=implode(",",$grey_dtls_id_arr);

	    $grey_dtls_id_cond="";
		$dtls_ids=count($grey_dtls_id_arr);
		if($db_type==2 && $dtls_ids>1000)
		{
			$grey_dtls_id_cond=" and (";
			$grey_dtls_idArr=array_chunk(array_unique(explode(",",$grey_dtls_id)),999);
			foreach($grey_dtls_idArr as $ids)
			{
				$ids=implode(",",$ids);
				$grey_dtls_id_cond.=" mst_id in($ids) or";
			}
			$grey_dtls_id_cond=chop($grey_dtls_id_cond,'or ');
			$grey_dtls_id_cond.=")";
		}
		else
		{
			$grey_dtls_id_cond=" and mst_id in($grey_dtls_id)";
		}

		$grey_recv_arr = return_library_array("select mst_id, sum(qnty) as qnty from pro_roll_details where entry_form=58 and status_active=1 and is_deleted=0 $grey_dtls_id_cond group by mst_id", 'mst_id', 'qnty');
	}
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="60">Year</th>
			<th width="70">Received ID</th>
			<th width="120">Booking/PI /Production No</th>
			<th width="120">Location</th>
			<th width="100">Knitting Source</th>
			<th width="110">Knitting Company</th>
			<th width="80">Receive date</th>
			<th width="80">Receive Qnty</th>
			<th>Challan No</th>
		</thead>
	</table>
	<div style="width:890px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			$knit_comp = "&nbsp;";
			if ($row[csf('knitting_source')] == 1)
				$knit_comp = $company_arr[$row[csf('knitting_company')]];
			else
				$knit_comp = $supllier_arr[$row[csf('knitting_company')]];

			$data_all = $row[csf('recv_number')] . "_" . $row[csf('company_id')] . "_" . $company_arr[$row[csf('company_id')]] . "_" . $row[csf('knitting_source')] . "_" . $row[csf('knitting_company')] . "_" . $knit_comp . "_" . $row[csf('booking_id')] . "_" . $row[csf('booking_no')] . "_" . $row[csf('is_posted_account')];
			$recv_qnty = $grey_recv_arr[$row[csf('id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>');">
				<td width="35"><? echo $i; ?></td>
				<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</p></td>
				<td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
				<td width="110"><p><? echo $knit_comp; ?></p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
				<td width="80" align="right"><? echo number_format($recv_qnty, 2, '.', ''); ?></td>
				<td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
    </div>
    <?
    die;
}

if ($action == "load_php_update_form")
{
	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1","company_name","store_method");

	$sql = sql_select("select a.id, a.receive_date, a.company_id, a.store_id, a.location_id,a.knitting_location_id, a.recv_number, a.booking_no, a.knitting_source, a.knitting_company, a.yarn_issue_challan_no, a.remarks, a.booking_no, a.challan_no,boe_mushak_challan_no, boe_mushak_challan_date
		from inv_receive_master a where  a.id='$data' and a.entry_form=58 ");
	foreach ($sql as $val)
	{
		//echo "$('#cbo_location_name').attr('disabled',true);\n";
		echo "document.getElementById('store_update_upto').value  = '" . ($variable_inventory_arr[$val[csf("company_id")]]) . "';\n";
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', " . $val[csf("company_id")] . "+'_'+" . $val[csf("location_id")] . ", 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('txt_receive_date').value  = '" . change_date_format($val[csf("receive_date")]) . "';\n";
		echo "document.getElementById('cbo_store_name').value  = '" . ($val[csf("store_id")]) . "';\n";
		echo "document.getElementById('txt_yarn_issue_challan_no').value  = '" . ($val[csf("yarn_issue_challan_no")]) . "';\n";
		echo "document.getElementById('txt_boe_mushak_challan_no').value  = '" . ($val[csf("boe_mushak_challan_no")]) . "';\n";
		echo "document.getElementById('txt_boe_mushak_challan_date').value  = '" . change_date_format($val[csf("boe_mushak_challan_date")]) . "';\n";
		echo "document.getElementById('txt_remarks').value  = '" . ($val[csf("remarks")]) . "';\n";
		echo "document.getElementById('update_id').value  = '" . ($val[csf("id")]) . "';\n";
		echo "document.getElementById('txt_challan_no').value  = '" . ($val[csf("booking_no")]) . "';\n";
		echo "document.getElementById('txt_recieved_id').value  = '" . ($val[csf("recv_number")]) . "';\n";
		echo "document.getElementById('txt_receive_chal_no').value  = '" . ($val[csf("challan_no")]) . "';\n";

		echo "document.getElementById('hidden_delevery_scan').value  = '" . ($val[csf("booking_no")]) . "';\n";

		if($val[csf("knitting_source")]==3)
		{
			echo "$('#txt_receive_chal_no').attr('disabled',true);\n";

			//echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("company_id")] . "', 'load_drop_down_location', 'location_td');\n";
		}
		else
		{
			echo "$('#txt_receive_chal_no').attr('disabled',false);\n";
			//echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("knitting_company")] . "', 'load_drop_down_location', 'location_td');\n";
		}


		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("company_id")] . "', 'load_drop_down_location', 'location_td');\n";
		echo "document.getElementById('cbo_location_name').value  = '" . ($val[csf("location_id")]) . "';\n";
		//echo "$('#cbo_location_name').attr('disabled',true);\n";
		$location_name=return_field_value("location_name","lib_location","id='".$val[csf('knitting_location_id')]."'  and status_active =1 and is_deleted=0");
		echo "document.getElementById('cbo_knitting_location_id').value  = '" . ($val[csf("knitting_location_id")]) . "';\n";
		echo "document.getElementById('txt_knitting_location_id').value  = '" . ($location_name) . "';\n";
		echo "$('#txt_knitting_location_id').attr('disabled',true);\n";
		echo "$('#cbo_store_name').attr('disabled',true);\n";
		echo "$('#cbo_location_name').attr('disabled',true);\n";

		$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$val[csf("company_id")]."'  and module_id=6 and report_id=171 and is_deleted=0 and status_active=1");

		$print_report_format_arr=explode(",",$print_report_format);

		echo "$('#print1').hide();\n";
		echo "$('#print2').hide();\n";
		echo "$('#print3').hide();\n";
		echo "$('#print4').hide();\n";
		echo "$('#print_barcode').hide();\n";
		echo "$('#btn_fabric_details').hide();\n";
		if($print_report_format != "")
		{
			foreach($print_report_format_arr as $id)
			{
				if($id==86){echo "$('#print1').show();\n";}
				if($id==84){echo "$('#print2').show();\n";}
				if($id==85){echo "$('#print3').show();\n";}
				if($id==89){echo "$('#print4').show();\n";}
				if($id==68){echo "$('#print_barcode').show();\n";}
				if($id==69){echo "$('#btn_fabric_details').show();\n";}
			}
		}
		else
		{
			echo "$('#print1').show();\n";
			echo "$('#print2').show();\n";
			echo "$('#print3').show();\n";
			echo "$('#print4').show();\n";
			echo "$('#print_barcode').show();\n";
			echo "$('#btn_fabric_details').show();\n";
		}
	}
	die;
}


if ($action == "load_php_challan_form") {

	$sql = sql_select("select a.id,a.delevery_date,a.company_id,a.order_status,a.location_id,a.buyer_id from  pro_grey_prod_delivery_mst a where  a.sys_number='$data'  and a.entry_form=56 ");


	foreach ($sql as $val) {
		echo "document.getElementById('cbo_company_id').value  = '" . ($val[csf("company_id")]) . "';\n";
		//echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '".$val[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n";
		echo "document.getElementById('cbo_location_name').value  = '" . ($val[csf("location_id")]) . "';\n";
		//echo "document.getElementById('txt_receive_date').value = '".($val[csf("delevery_date")])."';\n";
		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', " . $val[csf("company_id")] . "+'_'+" . $val[csf("location_id")] . ", 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('hidden_delivery_id').value  = '" . ($val[csf("id")]) . "';\n";
	}
	die;
}

if ($action == "load_drop_down_location") {
	$sql_location = "select id, location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 group by id, location_name order by location_name";
	$location_id = sql_select($sql_location);
	$selected_location = "";

	if (count($location_id) == 1){
		$selected_location = $location_id[0][csf('id')];
	}

	echo create_drop_down("cbo_location_name", 151, $sql_location, "id,location_name", 1, "--Select Location--", 0,"load_drop_down( 'requires/grey_fabric_receive_roll_controller', $data+'_'+this.value, 'load_drop_down_store', 'store_td');");
	//echo create_drop_down("cbo_location_name", 151, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/grey_fabric_receive_roll_controller*13', 'store','store_td',$data,this.value,'','','','','','','','151','V');");
	die;
}

// ================================== floor room rack shelf function Start ============
if ($action == "load_drop_down_store")
{
	$data = explode("_", $data);
	$category_id = 13;
	$sql_store = "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and
	a.company_id='$data[0]' and b.category_type=$category_id and a.location_id='$data[1]' $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name";
	//echo $sql_store;die;
	$store_id = sql_select($sql_store);
	$selected_store = "";
	if (count($store_id) == 1) $selected_store = $store_id[0][csf('id')];
	echo create_drop_down("cbo_store_name", 152, $sql_store, "id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);reset_room_rack_shelf('1','cbo_store_name');");
	exit();
}

if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[2]'";
	$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('floor_room_rack_name')]]=$row[csf('floor_id')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.floor_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('floor_room_rack_name')]]=$row[csf('room_id')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
	$rack_data=sql_select("SELECT b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.room_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name, b.serial_no");
	foreach($rack_data as $row)
	{
		$rack_arr[$row[csf('floor_room_rack_name')]]=$row[csf('rack_id')];
	}
	// echo "<pre>";print_r($rack_arr);die;
	$jsRack_arr= json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
	$shelf_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.rack_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($shelf_data as $row)
	{
		$shelf_arr[$row[csf('floor_room_rack_name')]]=$row[csf('shelf_id')];
	}
	$jsShelf_arr= json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$bin_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[1]'";
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.shelf_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('floor_room_rack_name')]]=$row[csf('bin_id')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}
// =================================== floor room rack shelf function End =============

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "$company_id", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 1, "");
	} else {
		echo create_drop_down("cbo_knitting_company", 152, $blank_array, "", 1, "--Select Knit Company--", 1, "");
	}
	exit();
}

if ($action == "issue_num_check") {
	$issue_no = return_field_value("issue_number_prefix_num as issue_number_prefix_num", "inv_issue_master", "status_active=1 and is_deleted=0 and entry_form=3 and issue_number_prefix_num=$data", "issue_number_prefix_num");
	echo $issue_no;
	exit();
}

if ($action == "process_costing_check") {
	$sql = sql_select("select sys_number, company_id,knitting_source from pro_grey_prod_delivery_mst where entry_form=56 and status_active=1 and is_deleted=0 and sys_number='$data'");
	$knittint_source = $sql[0][csf('knitting_source')];
	$company_id = $sql[0][csf('company_id')];
	$process_costing_maintain = return_field_value("process_costing_maintain", "variable_settings_production", "variable_list=34 and status_active=1 and is_deleted=0 and company_name=$company_id", "process_costing_maintain");

	if ($process_costing_maintain == 1 && $knittint_source == 3) {
		echo 1;
	} else {
		echo 0;
	}
	exit();
}

if ($action == "previous_challan_comp_chk")
{
	$sql = sql_select("select sys_number, company_id,knitting_source from pro_grey_prod_delivery_mst where entry_form=56 and status_active=1 and is_deleted=0 and sys_number='$data'");
	$company_id = $sql[0][csf('company_id')];
	echo $company_id;
	exit();
}


if ($action == "challan_popup") {
	echo load_html_head_contents("Challan Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$company_cond = ($cbo_company_id>0) ? " and comp.id =$cbo_company_id" : "";
	?>

	<script>

		function js_set_value(data, id, process_costing) {
			$('#hidden_data').val(data);
			$('#hidden_receive_id').val(id);
			$('#hidden_process_costing').val(process_costing);
			parent.emailwindow.hide();
		}

		function fnc_show()
		{
			if($('#txt_date_from').val()=="" && $('#txt_date_to').val()=="" && $('#txt_search_common').val()=="")
			{
				if( form_validation('txt_date_from*txt_date_to','From date*To date')==false )
				{
					return;
				}
			}

			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value, 'create_challan_search_list_view', 'search_div', 'grey_fabric_receive_roll_controller', 'setFilterGrid(\'tbl_list_search\',-1);')
		}

	</script>

	</head>

	<body>
		<div align="center" style="width:780px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:780px; margin-left:2px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="780" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Company</th>
							<th>Location</th>
							<th>Delivery Date Range</th>
							<th>Search By</th>
							<th>Please Enter Challan No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px"
								class="formbutton"/>
								<input type="hidden" name="hidden_data" id="hidden_data">
								<input type="hidden" name="hidden_receive_id" id="hidden_receive_id">
								<input type="hidden" name="hidden_process_costing" id="hidden_process_costing">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<? echo create_drop_down("cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', 0, "load_drop_down( 'grey_fabric_receive_roll_controller', this.value, 'load_drop_down_location_pop', 'location_td' );", 0); ?>
							</td>
							<td align="center" id="location_td">
								<? echo create_drop_down("cbo_location_id", 120, $blank_array, "", 1, "-- Select --", $selected, "", 0, ""); ?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								readonly>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Challan No");
								echo create_drop_down("cbo_search_by", 90, $search_by_arr, "", 0, "--Select--", 1, '', 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:100px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show();" style="width:80px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" height="40"
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
	exit();
}

$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");


if ($action == "create_challan_search_list_view") {
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$location_id = $data[5];

	if ($company_id == 0) {
		echo "Please Select Company First.";
		die;
	}

	if ($search_string == "" && ($start_date == "" && $end_date == "")) {
		echo "<p style='color:red; font-weight:bold; text-align:center;'>Delivery date range is required</p>";
		exit;
	}
	if ($location_id == 0) {
		$location_cond = '';
	} else {
		$location_cond = " and a.location_id='" . $location_id . "'";
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.delevery_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.delevery_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) $search_field_cond = "and sys_number like '%$search_string'";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "to_char(insert_date,'YYYY') as year,";
	} else $year_field = "";//defined Later

	$con = connect();
	$r_id=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=61 and type=1");
	oci_commit($con);

	$data_array = sql_select("SELECT c.barcode_no,a.sys_number FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=56 and a.entry_form=56 and a.company_id=$company_id $search_field_cond $date_cond $location_cond and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$challan_barcode = array();
	$inserted_barcode = array();
	foreach ($data_array as $val)
	{
		$challan_barcode[$val[csf('barcode_no')]] = $val[csf('barcode_no')];
		$rID2=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form, type) values ($user_id,".$val[csf('barcode_no')].",61,1)");
	}
	oci_commit($con);

	if(!empty($challan_barcode))
	{
		$inserted_roll = sql_select("select a.barcode_no from pro_roll_details a,inv_receive_master b, tmp_barcode_no c where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=58 and b.company_id=$company_id and a.entry_form=58 and b.entry_form=58 and a.barcode_no=c.barcode_no and c.entry_form=61 and c.userid=$user_id and c.type=1");
	}
	else
	{
		echo "No data found";
		disconnect($con);
		die;
	}

	if (!empty($inserted_roll))
	{
		$received_barcode_check=array();
		foreach ($inserted_roll as $b_id)
		{
			$received_barcode_check[$b_id[csf('barcode_no')]] = $b_id[csf('barcode_no')];
		}
	} // end if inserted_roll
	$challan_barcode2=array();
	foreach ($challan_barcode as $barcode_key=> $barcode)
	{
		if($received_barcode_check[$barcode_key]=="")
		{
			$challan_barcode2[$barcode_key]=$barcode_key;
			$rID3=execute_query("insert into tmp_barcode_no (userid, barcode_no, entry_form, type) values ($user_id,".$barcode_key.",61,2)");
		}
	}
	oci_commit($con);

	if($db_type==0)
	{
		$year_select=" year(a.insert_date) as year";
	}
	else if($db_type==2)
	{
		$year_select="to_char(a.insert_date,'YYYY') as year";
	}

	if(!empty($challan_barcode2))
	{
		$sql = "SELECT a.id, $year_select,a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.knitting_source, a.knitting_company, a.delevery_date, min(b.grey_sys_id) as grey_sys_id
		from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c, tmp_barcode_no d
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=56 and c.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $location_cond and c.barcode_no= d.barcode_no and d.userid=$user_id and d.entry_form=61 and d.type=2
		group by  a.id,a.insert_date,a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.knitting_source, a.knitting_company, a.delevery_date";
	}
	else
	{
		echo "No data found";
		disconnect($con);
		die;
	}

	$result = sql_select($sql);
	$knitting_production_id_arr=$production_arr=array();
	//$barcode_no_str="";
	foreach ($result as $row) {
		$knitting_production_id_arr[$row[csf("grey_sys_id")]] = $row[csf("grey_sys_id")];
	}

	if(!empty($knitting_production_id_arr)){
		$production_arr = return_library_array("select id, challan_no from inv_receive_master where id in(".implode(",",$knitting_production_id_arr).")", 'id', 'challan_no');
	}
	$process_costing_maintain = return_field_value("process_costing_maintain", "variable_settings_production", "variable_list=34 and status_active=1 and is_deleted=0 and company_name=$company_id", "process_costing_maintain");


	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1","company_name","store_method");

	$r_id=execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=61 and type in (1,2)");
	oci_commit($con);
	disconnect($con);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Location</th>
			<th width="70">Challan No</th>
			<th width="60">Year</th>
			<th width="90">Knitting Source</th>
			<th width="130">Knitting Company</th>
			<th>Delivery date</th>
		</thead>
	</table>
	<div style="width:770px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table"
		id="tbl_list_search">
		<?
			$i = 1;
			if (!empty($result))
			{
				foreach ($result as $row)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$knit_comp = "&nbsp;";
					if ($row[csf('knitting_source')] == 1) $knit_comp = $company_arr[$row[csf('knitting_company')]];
					else $knit_comp = $supplier_arr[$row[csf('knitting_company')]];
					$data_all = $row[csf('sys_number')] . "_" . $row[csf('company_id')] . "_" . $company_arr[$row[csf('company_id')]] . "_" . $row[csf('knitting_source')] . "_" . $row[csf('knitting_company')] . "_" . $row[csf('location_id')] . "_" . $knit_comp . "_" . $production_arr[$row[csf("grey_sys_id")]] . "_" . $location_arr[$row[csf('location_id')]]. "_" . $variable_inventory_arr[$row[csf('company_id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>','<? echo $process_costing_maintain; ?>');">
						<td width="40"><? echo $i; ?></td>
						<td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="120"><p><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</p></td>
						<td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
						<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
						<td width="130"><p><? echo $knit_comp; ?>&nbsp;</p></td>
						<td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			else
			{
				echo "<p style='font-weight: bold;color:red; text-align: center;'>No data found</p>";
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action == "check_left_barcode_exist")
{
	$pre_rcv_barcode = sql_select("SELECT c.barcode_no
		FROM inv_receive_master a, pro_roll_details c
		WHERE a.id=c.mst_id  and a.entry_form in(58) and c.entry_form in(58) and a.booking_no='" . $data . "' and a.booking_without_order in(0) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

	foreach ($pre_rcv_barcode as $val)
	{
		$pre_rcv_barcode_arr[$val[csf("barcode_no")]] =$val[csf("barcode_no")];
	}

	$left_barcode = sql_select("SELECT c.barcode_no from  pro_grey_prod_delivery_mst a,  pro_roll_details c where a.id=c.mst_id and a.sys_number='" . $data . "'  and a.entry_form=56 and c.entry_form=56 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");

	foreach ($left_barcode as $value)
	{
		if($pre_rcv_barcode_arr[$value[csf("barcode_no")]]=="")
		{
			$left_barcode_arr[$value[csf("barcode_no")]] = $value[csf("barcode_no")];
		}
	}

	if(empty($left_barcode_arr))
	{
		echo "0";
	}else{
		echo "1";
	}
}

if ($action == "load_php_mst_form")
{
	$data_arr = explode("**", $data);
	$variable_inventory_arr=return_library_array("select company_name, store_method from variable_settings_inventory  where item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1","company_name","store_method");

	if ($db_type==0)
	{
		$sql = "SELECT a.id, a.delevery_date, a.company_id, a.knitting_source, a.knitting_company, a.order_status, a.location_id, group_concat(b.grey_sys_id) as grey_sys_id
		from  pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
		where a.id=b.mst_id and a.sys_number='$data_arr[0]'  and a.entry_form=56 and a.status_active=1 and b.status_active=1
		group by a.id, a.delevery_date, a.company_id, a.knitting_source, a.knitting_company, a.order_status, a.location_id";
	}
	else
	{
		$sql = "SELECT a.id, a.delevery_date, a.company_id, a.knitting_source, a.knitting_company, a.order_status, a.location_id, listagg(cast(b.grey_sys_id as varchar2(4000)),',') within group (order by a.id) as grey_sys_id
		from  pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
		where a.id=b.mst_id and a.sys_number='$data_arr[0]'  and a.entry_form=56 and a.status_active=1 and b.status_active=1
		group by a.id, a.delevery_date, a.company_id, a.knitting_source, a.knitting_company, a.order_status, a.location_id";
	}

	$sql_result = sql_select($sql);

	echo "document.getElementById('challan_discurd').value  = '" . $data_arr[1] . "';\n";
	echo "document.getElementById('hidden_prev_barcode').value  = '" . $data_arr[2] . "';\n";
	echo "document.getElementById('hidden_prev_dtls_id').value  = '" . $data_arr[3] . "';\n";
	echo "document.getElementById('hidden_prev_trans_id').value  = '" . $data_arr[4] . "';\n";
	echo "document.getElementById('hidden_prev_prod_id').value  = '" . $data_arr[5] . "';\n";

	foreach ($sql_result as $val)
	{
		if ($val[csf('knitting_source')] == 1) $knit_comp = $company_arr[$val[csf('knitting_company')]];
		else $knit_comp = $supplier_arr[$val[csf('knitting_company')]];

		echo "document.getElementById('cbo_company_id').value  = '" . ($val[csf("company_id")]) . "';\n";
		echo "document.getElementById('store_update_upto').value  = '" . ($variable_inventory_arr[$val[csf("company_id")]]) . "';\n";
		echo "document.getElementById('txt_company_name').value  = '" . ($company_arr[$val[csf("company_id")]]) . "';\n";
		echo "document.getElementById('cbo_knitting_source').value  = '" . ($val[csf("knitting_source")]) . "';\n";
		echo "document.getElementById('cbo_knitting_company').value  = '" . ($val[csf("knitting_company")]) . "';\n";
		echo "document.getElementById('txt_knitting_company').value  = '" . $knit_comp . "';\n";

		if($val[csf("knitting_source")]==3)
		{
			$grey_sys_id = $val[csf("grey_sys_id")];
			$sql_sys = "SELECT id, challan_no from inv_receive_master where id in($grey_sys_id) ";
			$sql_sys_result = sql_select($sql_sys);
			$grey_sys_arr = array();
			foreach ($sql_sys_result as $value) {
				$grey_sys_arr[$value[csf('id')]] = $value[csf('challan_no')];
			}
			$knit_production_challan = implode(",", array_unique($grey_sys_arr));
			echo "$('#txt_receive_chal_no').val('".$knit_production_challan."');\n";
			echo "$('#txt_receive_chal_no').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_receive_chal_no').val('');\n";
			echo "$('#txt_receive_chal_no').attr('disabled',false);\n";
		}

		/*if($val[csf("knitting_source")]==1)
		{
			echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("knitting_company")] . "', 'load_drop_down_location', 'location_td');\n";
		}else {
			echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("company_id")] . "', 'load_drop_down_location', 'location_td');\n";
		}*/

		echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("company_id")] . "', 'load_drop_down_location', 'location_td');\n";


		//echo "document.getElementById('cbo_location_name').value  = '" . ($val[csf("location_id")]) . "';\n";
		//echo "$('#cbo_location_name').attr('disabled',true);\n";
		//echo "document.getElementById('txt_receive_date').value = '".($val[csf("delevery_date")])."';\n";


		$location_name=return_field_value("location_name","lib_location","id='".$val[csf('location_id')]."'  and status_active =1 and is_deleted=0");
		echo "document.getElementById('cbo_knitting_location_id').value  = '" . ($val[csf("location_id")]) . "';\n";
		echo "document.getElementById('txt_knitting_location_id').value  = '" . ($location_name) . "';\n";
		echo "$('#txt_knitting_location_id').attr('disabled',true);\n";


		//echo "load_drop_down( 'requires/grey_fabric_receive_roll_controller', '" . $val[csf("company_id")] . "', 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('hidden_delivery_id').value  = '" . ($val[csf("id")]) . "';\n";
		//echo "$('#cbo_location_name').attr('disabled','disabled');\n";
	}
	exit();
}


//-------------------START ----------------------------------------
$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');

$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");


/*if ($db_type == 2) $select_year = "to_char(a.insert_date,'YYYY') as job_year"; else if ($db_type == 0) $select_year = "year(a.insert_date) as job_year";
$sql_job = "select
b.id as po_id,
b.po_number,
a.job_no,
a.job_no_prefix_num,
$select_year
from
wo_po_details_master a, wo_po_break_down b
where
a.job_no=b.job_no_mst ";

$job_po_arr = array();
$sql_job_result = sql_select($sql_job);
foreach ($sql_job_result as $row) {
	$job_po_arr[$row[csf("po_id")]]["po_id"] = $row[csf("po_id")];
	$job_po_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no_prefix_num")];
	$job_po_arr[$row[csf("po_id")]]["po_number"] = $row[csf("po_number")];
	$job_po_arr[$row[csf("po_id")]]["job_year"] = $row[csf("job_year")];
}

$composition_arr = array();
$construction_arr = array();
$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
$data_array = sql_select($sql_deter);
if (count($data_array) > 0) {
	foreach ($data_array as $row) {
		$construction_arr[$row[csf('id')]] = $row[csf('construction')];
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
}*/


if ($action == "grey_item_details_update")
{

	$data = explode("_", $data);
	$floor_name_array = return_library_array("select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 group by a.id, a.floor_name", "id", "floor_name");


	$data_array_mst = sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_no, c.barcode_no, c.id as roll_id, c.roll_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.booking_no='$data[1]' and a.id not in($data[0]) and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 ");
	foreach ($data_array_mst as $inf) {
		$update_barcode_arr[] = "'" . $inf[csf('barcode_no')] . "'";
	}
	$data_array_mst = sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.rate, c.qc_pass_qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.po_breakdown_id, c.booking_without_order, c.is_sales, c.qc_pass_qnty_pcs, c.fso_delivery_type
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$data[0] and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$roll_details_array = array();
	$barcode_array = array();
	$kint_prod_buyer = array();
	$roll_ids = "";
	$order_ids = "";
	foreach ($data_array_mst as $row)
	{
		$booking_no_update = $row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_mst_id'] = $row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id'] = $row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number'] = $row[csf("recv_number")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date'] = change_date_format($row[csf("receive_date")]);
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id'] = $row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source'] = $knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id'] = $row[csf("knitting_company")];
		$roll_details_array[$row[csf("barcode_no")]]['room'] = $row[csf("room")];
		$roll_details_array[$row[csf("barcode_no")]]['rack'] = $row[csf("rack")];
		$roll_details_array[$row[csf("barcode_no")]]['self'] = $row[csf("self")];
		$roll_details_array[$row[csf("barcode_no")]]['bin_box'] = $row[csf("bin_box")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_no'] = $row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_without_order'] = $row[csf("booking_without_order")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_id'] = $row[csf("booking_id")];

		if ($row[csf("knitting_source")] == 1) {
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $company_arr[$row[csf("knitting_company")]];
		} else if ($row[csf("knitting_source")] == 3) {
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
		}


		$roll_details_array[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
		$roll_details_array[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
		$roll_details_array[$row[csf("barcode_no")]]['brand_id'] = $row[csf("brand_id")];
		$roll_details_array[$row[csf("barcode_no")]]['shift_name'] = $row[csf("shift_name")];
		$roll_details_array[$row[csf("barcode_no")]]['floor_id'] = $row[csf("floor_id")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_no_id'] = $row[csf("machine_no_id")];
		$roll_details_array[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['color_range_id'] = $row[csf("color_range_id")];
		$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['trans_id'] = $row[csf("trans_id")];
		$roll_details_array[$row[csf("barcode_no")]]['dtls_id'] = $row[csf("dtls_id")];
		$roll_details_array[$row[csf("barcode_no")]]['prod_id'] = $row[csf("prod_id")];
		$roll_details_array[$row[csf("barcode_no")]]['deter_id'] = $row[csf("febric_description_id")];
		$roll_details_array[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
		$roll_details_array[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
		//$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty'] = number_format($row[csf("qc_pass_qnty")], 2, '.', '');
		$roll_details_array[$row[csf("barcode_no")]]['qnty_in_pcs'] = $row[csf("qc_pass_qnty_pcs")]*1;
		$roll_details_array[$row[csf("barcode_no")]]['fso_delivery_type'] = $row[csf("fso_delivery_type")];

		$roll_rcv_barcode_58_array[$row[csf("barcode_no")]] = $row[csf("barcode_no")];

		$roll_ids .= $row[csf("roll_id")] . ",";

		if($row[csf("is_sales")] == 1){
			$sales_order_ids .= $row[csf("po_breakdown_id")] . ",";
		}else{
			$po_order_ids .= $row[csf("po_breakdown_id")] . ",";
		}

		if($row[csf("booking_without_order")]==1)
		{
			$non_ord_samp_booking_id .= $row[csf("po_breakdown_id")] . ",";
		}

		$all_color_ids .=$row[csf("color_id")].",";
		$all_deter_ids .=$row[csf("febric_description_id")].",";
	}

	$all_color_ids = implode(",",array_filter(array_unique(explode(",",chop($all_color_ids,",")))));
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0 and id in ($all_color_ids)", 'id', 'color_name');
	$composition_arr = array();
	$constructtion_arr = array();
	$all_deter_ids = implode(",",array_filter(array_unique(explode(",",chop($all_deter_ids,",")))));
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active =1 and a.id in ($all_deter_ids)";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$non_ord_samp_booking_id = implode(",",array_unique(explode(",",chop($non_ord_samp_booking_id,","))));
	//echo $non_ord_samp_booking_id.test;die;
	if($non_ord_samp_booking_id!="")
	{
		$sqlsamplnoOrder = sql_select("select id,insert_date,buyer_id from wo_non_ord_samp_booking_mst where id in($non_ord_samp_booking_id) and status_active=1 and is_deleted=0");

		foreach ($sqlsamplnoOrder as $row)
		{
			$no_order_details_array[$row[csf("id")]]['buyer_id'] = $buyer_array[$row[csf("buyer_id")]];
			$no_order_details_array[$row[csf("id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		}
	}

	$roll_ids = trim($roll_ids, ",");
	$order_ids = trim($po_order_ids, ",");
	$sales_order_ids = trim($sales_order_ids, ",");
	if ($roll_ids != "")
	{
		$issued_roll = sql_select("select barcode_no from pro_roll_details where id in($roll_ids) and entry_form=61 and is_deleted=0 and status_active=1 ");
		$issued_roll_arr = array();
		foreach ($issued_roll as $inf)
		{
			$issued_roll_arr[$inf[csf('barcode_no')]] = $inf[csf('barcode_no')];
		}
	}

	if ($order_ids != "")
	{
		//echo "SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in($order_ids) <br>";
		$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, b.grouping, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in($order_ids)");
		$po_details_array = array();
		foreach ($data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $buyer_array[$row[csf("buyer_name")]];
			$po_details_array[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['int_ref'] = $row[csf("grouping")];
		}
	}

	$sales_arr=array();
	$sales_order_ids=implode(",",array_unique(explode(",",$sales_order_ids)));
	if ($sales_order_ids != "")
	{
		$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date,insert_date from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in(".$sales_order_ids.")");
		foreach ($sql_sales as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]["id"] 			    = $sales_row[csf('id')];
			$sales_arr[$sales_row[csf('id')]]["po_number"] 			= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["year"] 				= date("Y", strtotime($sales_row[csf("insert_date")]));
			$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $buyer_array[$sales_row[csf('buyer_id')]];
			$booking_no_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
		}

		$job_arr=array();
		if(!empty($booking_no_arr))
		{
			$sql_job=sql_select("SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.insert_date, c.grouping from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and b.booking_no in(".implode(",",$booking_no_arr).") group by a.buyer_id, b.job_no,b.booking_no,b.insert_date, c.grouping");

			foreach ($sql_job as $job_row)
			{
				$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] = $job_row[csf('job_no_mst')];
				$job_arr[$job_row[csf('booking_no')]]["int_ref"] = $job_row[csf('grouping')];
				$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 	= $buyer_array[$job_row[csf('buyer_id')]];
				$job_arr[$job_row[csf("booking_no")]]['year'] 		= date("Y", strtotime($job_row[csf("insert_date")]));
			}
		}
	}
	//die;

	if (count($update_barcode_arr) > 0) $roll_cond = " and c.barcode_no not in (" . implode(",", $update_barcode_arr) . ")";
	$sql_result = sql_select("select a.id, a.delevery_date, a.company_id, a.order_status, a.location_id, a.buyer_id, c.barcode_no, b.id as dtls_id, c.id as roll_id, c.is_sales, c.qc_pass_qnty_pcs, b.grey_sys_id,c.coller_cuff_size
		from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and b.barcode_num=c.barcode_no and a.sys_number='" . trim($booking_no_update) . "' and a.entry_form in(56) and c.entry_form in(56)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 $roll_cond");

	$delivery_barcode_arr = array();
	foreach ($sql_result as $bar_row)
	{
		$delivery_barcode_arr[$bar_row[csf('barcode_no')]] = $bar_row[csf('barcode_no')];
	}
	if(!empty($delivery_barcode_arr))
	{
		$data_array = sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_id, a.booking_no, a.knitting_source, a.knitting_company, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.stitch_length, b.body_part_id, b.yarn_lot,b.brand_id, b.shift_name,b.floor_id, b.machine_no_id, b.yarn_count, b.uom, b.color_id, b.color_range_id,b.trans_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.rate,b.kniting_charge, b.yarn_rate, c.booking_without_order, c.qc_pass_qnty_pcs, c.is_sales, c.coller_cuff_size, c.fso_delivery_type
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . implode(",", $delivery_barcode_arr) . ") order by c.barcode_no desc");

	}
	$all_book_id_arr=array();
	foreach ($data_array as $row)
	{
		$all_book_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
		$all_book_no_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
		if($prod_book_check[$row[csf("booking_no")]]=="")
		{
			$prod_book_check[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$prod_booking_no.="'".$row[csf("booking_no")]."',";
		}
		if ($row[csf("receive_basis")] == 2)
		{
			$booking_column_2 = "sales_booking_no";
		}
		if ($row[csf("receive_basis")] == 4)
		{
			$booking_column_4 = "job_no";
		}

		$kint_prod_data[$row[csf("id")]]["booking_id"]=$row[csf("booking_id")];
		$kint_prod_data[$row[csf("id")]]["receive_basis"]=$row[csf("receive_basis")];

		if($row[csf("is_sales")] == 1)
		{
			$all_sales_id_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		}

	}

	$prod_booking_no=chop($prod_booking_no,",");

	if(count($all_book_id_arr)>0)
	{
		/*$plan_sql="select a.mst_id, b.booking_no, a.po_id, a.is_sales, a.dtls_id from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.dtls_id in(".implode(",",$all_book_id_arr).")";
		$plan_sql_result=sql_select($plan_sql);
		$plan_data=array();
		foreach($plan_sql_result as $row)
		{
			$plan_data[$row[csf("dtls_id")]]['mst_id'] = $row[csf('mst_id')];
			$plan_data[$row[csf("dtls_id")]]['booking_no'] = $row[csf('booking_no')];
			$plan_data[$row[csf("dtls_id")]]['po_id'] = $row[csf('po_id')];
			$plan_data[$row[csf("dtls_id")]]['is_sales'] = $row[csf('is_sales')];
			$plan_data[$row[csf("dtls_id")]]['dtls_id'] = $row[csf('dtls_id')];
			if($plan_book_check[$row[csf('booking_no')]]=="")
			{
				$plan_book_check[$row[csf('booking_no')]]=$row[csf('booking_no')];
				$plan_booking_no.="'".$row[csf('booking_no')]."',";
			}
		}
		$plan_booking_no=chop($plan_booking_no,",");

		if($booking_column_2 == "sales_booking_no")
		{
			$salesOrder_sql = "SELECT a.dtls_id, b.id, b.job_no FROM ppl_planning_entry_plan_dtls a, fabric_sales_order_mst b WHERE a.po_id=b.id and $booking_column_2 in($plan_booking_no)";
		}
		if($booking_column_4 == "job_no")
		{
			$salesOrder_sql = "SELECT a.dtls_id, b.id, b.job_no FROM ppl_planning_entry_plan_dtls a, fabric_sales_order_mst b WHERE a.po_id=b.id and $booking_column_4 in($prod_booking_no)";
		}

		$salesOrder_id=sql_select($salesOrder_sql);
		foreach($salesOrder_id as $srow)
		{
			$salesOrderArr[$srow[csf("dtls_id")]]['id'] = $srow[csf('id')];
			$salesOrderArr[$srow[csf("dtls_id")]]['job_no'] = $srow[csf('job_no')];
		}*/



		if(!empty($all_sales_id_arr))
		{
			$salesOrder_id = sql_select("SELECT id,job_no FROM fabric_sales_order_mst WHERE id in (".implode(',',$all_sales_id_arr).")");

			foreach($salesOrder_id as $srow)
			{
				$salesRefArr[$srow[csf('id')]]["id"] = $srow[csf('id')];
				$salesRefArr[$srow[csf('id')]]["job_no"] = $srow[csf('job_no')];
			}
		}


	}

	foreach ($data_array as $row)
	{
		/*$booking_no_id = $row[csf('booking_no')];
		if ($row[csf("receive_basis")] == 2)
		{
			//echo $plan_data[$row[csf("booking_id")]]['is_sales']."te";die;
			$is_salesOrder = $plan_data[$row[csf("booking_id")]]['is_sales'];
			if ($is_salesOrder == "" || $is_salesOrder == 0)
			{
				$is_salesOrder = 0;
			}
			else
			{
				$is_salesOrder = 1;
			}

		}

		if ($row[csf("receive_basis")] == 4) // SALES ORDER
		{
			$is_salesOrder = 1;
		}*/

		if($row[csf("is_sales")]==1)
		{
			$is_salesOrder = 1;
		}else{
			$is_salesOrder = 0;
		}

		// IF BASIS IS SALES ORDER
		if ($is_salesOrder == 1)
		{
			/*if ($row[csf("receive_basis")] == 2) // SALES ORDER
			{
				$data_array = sql_select("select a.mst_id, b.booking_no,a.po_id from ppl_planning_entry_plan_dtls a
					inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
					where a.dtls_id= $booking_no_id");
				$booking_no = trim($data_array[0][csf("booking_no")]);
				$booking_column = "sales_booking_no";
			}
			else
			{
				$booking_no = $booking_no_id;
				$booking_column = "job_no";
			}
			// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
			$salesOrder_id = sql_select("SELECT id,job_no FROM fabric_sales_order_mst WHERE $booking_column='$booking_no'");
			foreach($salesOrder_id as $srow)
			{
				$po_id = $srow['ID'];
				$salesOrderArr[$row[csf("barcode_no")]] = $srow[csf('id')];
				$salesOrderArr[$row[csf("barcode_no")]] = $srow[csf('job_no')];
			}*/

			//echo $salesOrderArr[$row[csf("booking_id")]]["id"]."tds";die;



			$salesOrderArr[$row[csf("barcode_no")]]['job_no'] = $salesRefArr[$row[csf('po_breakdown_id')]]["job_no"];


			//$po_id = $salesOrderArr[$row[csf("booking_id")]]["id"];
			$po_id = $row[csf("po_breakdown_id")];

		}
		else
		{
			$po_id = $row[csf("po_breakdown_id")];
		}


		$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['grey_sys_number'] = $row[csf("recv_number")];
		$kint_prod_buyer[$row[csf("barcode_no")]] = $row[csf("buyer_id")];

		if (!in_array($row[csf("barcode_no")], $update_barcode_arr) )
		{
			$roll_details_array[$row[csf("barcode_no")]]['is_sales_order'] = $is_salesOrder ;
			$roll_details_array[$row[csf("barcode_no")]]['company_id'] = $row[csf("company_id")];
			$roll_details_array[$row[csf("barcode_no")]]['recv_number'] = $row[csf("recv_number")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_date'] = change_date_format($row[csf("receive_date")]);
			$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id'] = $row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source'] = $knitting_source[$row[csf("knitting_source")]];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id'] = $row[csf("knitting_company")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_without_order'] = $row[csf("booking_without_order")];

			if ($row[csf("knitting_source")] == 1)
			{
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $company_arr[$row[csf("knitting_company")]];
			}
			else if ($row[csf("knitting_source")] == 3)
			{
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
			}
			$roll_details_array[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$roll_details_array[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id'] = $row[csf("brand_id")];
			$roll_details_array[$row[csf("barcode_no")]]['shift_name'] = $row[csf("shift_name")];
			$roll_details_array[$row[csf("barcode_no")]]['production_floor_id'] = $row[csf("floor_id")];
			$roll_details_array[$row[csf("barcode_no")]]['machine_no_id'] = $row[csf("machine_no_id")];
			$roll_details_array[$row[csf("barcode_no")]]['yarn_count'] = $row[csf("yarn_count")];
			$roll_details_array[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
			$roll_details_array[$row[csf("barcode_no")]]['color_range_id'] = $row[csf("color_range_id")];
			$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];

			$roll_details_array[$row[csf("barcode_no")]]['uom'] = $row[csf("uom")];

			$roll_details_array[$row[csf("barcode_no")]]['prod_id'] = $row[csf("prod_id")];
			$roll_details_array[$row[csf("barcode_no")]]['deter_id'] = $row[csf("febric_description_id")];
			$roll_details_array[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$roll_details_array[$row[csf("barcode_no")]]['width'] = $row[csf("width")];

			if( $roll_rcv_barcode_58_array[$row[csf("barcode_no")]] =="")
			{
				$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
			}

			$roll_details_array[$row[csf("barcode_no")]]['roll_no'] = $row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id'] = $po_id;
			$roll_details_array[$row[csf("barcode_no")]]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['qnty_in_pcs'] = $row[csf("qc_pass_qnty_pcs")]*1;
			$roll_details_array[$row[csf("barcode_no")]]['reject_qnty'] = number_format($row[csf("reject_qnty")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['rate'] = number_format($row[csf("rate")], 2, '.', '');

			$roll_details_array[$row[csf("barcode_no")]]['knitting_charge'] = number_format($row[csf("kniting_charge")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['yarn_rate'] = number_format($row[csf("yarn_rate")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['coller_cuff_size'] = $row[csf("coller_cuff_size")];
			$roll_details_array[$row[csf("barcode_no")]]['fso_delivery_type'] = $row[csf("fso_delivery_type")];
			$barcode_array[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}
	}
	//echo "<pre>";print_r($kint_prod_data);echo "<pre>";print_r($salesOrderArr);echo "<pre>";print_r($sql_result);die;
	//echo "<pre>";
	//print_r($salesOrderArr);
	//print_r($roll_details_array);
	//die;

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,
	a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$data[3] and b.store_id=$data[5] and b.location_id=$data[4]
	order by a.floor_room_rack_name , c.floor_room_rack_name , d.floor_room_rack_name , e.floor_room_rack_name , f.floor_room_rack_name";
	// echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!=""){
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!=""){
				$lib_room_arr[$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!=""){
				$lib_rack_arr[$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!=""){
				$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	}
	else
	{
		$lib_floor_arr[0]="";
		$lib_room_arr[0]="";
		$lib_rack_arr[0]="";
		$lib_shelf_arr[0]="";
		$lib_bin_arr[0]="";
	}

	/*
	|--------------------------------------------------------------------------
	| for floor, room, rack and shelf disable
	|--------------------------------------------------------------------------
	|
	*/
	if(!empty($delivery_barcode_arr))
	{
		$sql_floorRoomRackShelf = sql_select("SELECT c.barcode_no FROM pro_roll_details c WHERE c.entry_form IN(61,82,83,133) AND c.status_active=1 AND c.is_deleted=0 AND c.barcode_no IN(".implode(",", $delivery_barcode_arr).")");
		$floorRoomRackShelf_disable_arr=array();
		foreach($sql_floorRoomRackShelf as $row)
		{
			$floorRoomRackShelf_disable_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}
	}

	//pro_roll_details
	if(!empty($delivery_barcode_arr))
	{
		$sql_zs=sql_select("SELECT barcode_no FROM pro_roll_details WHERE barcode_no  IN(".implode(",", $delivery_barcode_arr).") AND entry_form = 58 AND status_active = 1 AND roll_split_from<>0");
		foreach($sql_zs as $row)
		{
			$floorRoomRackShelf_disable_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
		unset($sql_zs);
	}


	//pro_roll_split
	if(!empty($delivery_barcode_arr))
	{
		$sql_zs=sql_select("SELECT barcode_no FROM pro_roll_split WHERE barcode_no  IN(".implode(",", $delivery_barcode_arr).") AND entry_form = 113 AND status_active = 1");
		foreach($sql_zs as $row)
		{
			$floorRoomRackShelf_disable_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
		unset($sql_zs);
	}
	?>

    <div style="width:2580px;" id="">
        <form name="delivery_details" id="delivery_details" autocomplete="off">
            <div id="report_print" style="width:2530px;">
                <table width="2530" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
                    <thead>
                        <th width="40">SL<input type="checkbox" id="txt_check_all" name="txt_check_all" onClick="check_all(this.value)"></th>
                        <th width="80">Barcode</th>
                        <th width="100">System Id</th>
                        <th width="100">Progm/ Booking No</th>
                        <th width="100">Production Basis</th>
                        <th width="50">Prod. Id</th>
                        <th width="50">Current Delv.</th>
                        <th width="50">Qty. in pcs</th>
                        <th width="50">Reject Qty.</th>
                        <th width="46">Roll</th>
                        <th width="50">Size</th>
                        <th width="50"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
						<th width="50"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
						<th width="50"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
						<th width="50"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
						<th width="50"><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
                        <th width="80">Knitting Source</th>
                        <th width="70">Prd. date</th>
                        <th width="40">Year</th>
                        <th width="50">Job No</th>
                        <th width="60">Buyer</th>
                        <th width="70">Order No</th>
                        <th width="100">Sales/Booking No</th>
                        <th width="70">Internal Ref No.</th>
                        <th width="80">Body Part</th>
                        <th width="80">Construction</th>
                        <th width="80">Composition</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="80">Fabric Color</th>
                        <th width="80">Color Range</th>
                        <th width="60">Yarn Lot</th>
                        <th width="60">Stitch Length</th>
                        <th width="50">UOM</th>
                        <th width="50">Yarn Count</th>
                        <th width="50">Brand</th>
                        <th width="50">Shift Name</th>
                        <th width="60">Prod. Floor</th>
                        <th width="60">Machine No.</th>
                        <th>Rate</th>
                    </thead>
                </table>
            </div>
            <div style="width:2550px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body">
                <table width="2530" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
                    <tbody>
					<?
                    $total_row = count($sql_result);
                    $current_row_array = array();
                    $i = 1;
                    foreach ($sql_result as $val)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($roll_details_array[$val[csf("barcode_no")]]['receive_mst_id'] != "")
						{
                            $checked = "checked='checked'";
                        }
						else
						{
                            $checked = "";
                        }

                        if ($issued_roll_arr[$val[csf("barcode_no")]]['mst_id'] != "")
						{
                            $roll_dissable = "disabled";
                        }
						else
						{
                            $roll_dissable = "";
                        }

                        $color_names = '';
                        $colorIds = array_unique(explode(",", $roll_details_array[$val[csf("barcode_no")]]['color_id']));
                        foreach ($colorIds as $color_id)
						{
                            $color_names .= $color_arr[$color_id] . ",";
                        }
                        $color_names = chop($color_names, ',');

						/*
						|--------------------------------------------------------------------------
						| for floor, room, rack and shelf disable
						|--------------------------------------------------------------------------
						|
						*/
						$isFloorRoomRackShelfDisable=0;
						if(!empty($floorRoomRackShelf_disable_arr[$val[csf("barcode_no")]]))
						{
							$isFloorRoomRackShelfDisable=1;
						}

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr__<? echo $i; ?>">
                            <td width="40" align="center"><? echo $i; ?>
                            	<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
                                <input type="checkbox" id="checkedId_<? echo $i; ?>" name="checkedId[]" <? echo $checked; ?>
                                value="" <? echo $roll_dissable; ?> />
                                <input type="hidden" id="hidesysid_<? echo $i; ?>" name="hidesysid_<? echo $i; ?>"
                                value=""/>
                                <input type="hidden" id="hiden_transid_<? echo $i; ?>" name="hiden_transid[]"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['trans_id']; ?>"/>
                                <input type="hidden" id="hidden_greyid_<? echo $i; ?>" name="hidden_greyid[]"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['dtls_id']; ?>"/>
                                <input type="hidden" id="hidden_rollid_<? echo $i; ?>" name="hidden_rollid[]"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_id']; ?>"/>
                                <input type="hidden" id="hidden_stl_<? echo $i; ?>" name="hidden_stl[]"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['stitch_length']; ?>"/>
                                <input type="hidden" id="hidden_withoutOrder_<? echo $i; ?>" name="hidden_withoutOrder[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['booking_without_order']; ?>"/>
                                <input type="hidden" id="isSales_<? echo $i; ?>" name="isSales[]" value="<? echo $val[csf("is_sales")]; ?>"/>
                                <input type="hidden" id="fsoDeliveryType_<? echo $i; ?>" name="fsoDeliveryType[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['fso_delivery_type']; ?>"/>
                            </td>
                            <td width="80">
                                <input type="hidden" id="hidesysnum_<? echo $i; ?>" name="hidesysnum[]"
                                value="<? echo $row[csf("recv_number")]; ?>"/>
                                <input type="hidden" id="hidenBarcode_<? echo $i; ?>" name="hidenBarcode[]"
                                value="<? echo $val[csf("barcode_no")]; ?>"/>
                                <?
                                echo $val[csf("barcode_no")];
                                ?>
                            </td>
                            <td width="100">
                                <input type="hidden" id="hidenReceiveId_<? echo $i; ?>" name="hidenReceiveId[]"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['mst_id']; ?>"/>
                                <?
                                echo $roll_details_array[$val[csf("barcode_no")]]['grey_sys_number'];
                                ?>
                            </td>
                            <td width="100" align="center" style="word-break:break-all;">
                                <input type="hidden" id="hideprogrum_<? echo $i; ?>" name="hideprogrum_<? echo $i; ?>"
                                value="<? echo $row[csf("prog_id")]; ?>"/>
                                <input type="hidden" id="hideBooking_<? echo $i; ?>" name="hideBooking_<? echo $i; ?>"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['booking_no']; ?>"/>
                                <?
                                if ($roll_details_array[$val[csf("barcode_no")]]['receive_basis'] == 0) echo "Independent"; else
                                echo $roll_details_array[$val[csf("barcode_no")]]['booking_no']; ?>
                            </td>
                            <td width="100" align="center" style="word-break:break-all;">
                                <input type="hidden" id="txtBasis_<? echo $i; ?>" name="txtBasis[]"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['receive_basis']; ?>">
                                <?
                                $receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => 'Sales Order');
                                echo $receive_basis[$roll_details_array[$val[csf("barcode_no")]]['receive_basis']];
                                ?>
                            </td>
                            <td width="50" align="center">
                                <input type="hidden" id="hideprodid_<? echo $i; ?>" name="hideprodid_<? echo $i; ?>"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['prod_id']; ?>"/>
                                <? echo $roll_details_array[$val[csf("barcode_no")]]['prod_id']; ?>
                            </td>
                            <td width="50" align="right">
                                <input type="hidden" id="hidden_delivery_qty_<? echo $i; ?>" name="hidden_delivery_qty[]"
                                class="text_boxes_numeric" style="width:35px;"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>"/>
                                <input type="hidden" id="txtcurrentdelivery_<? echo $i; ?>" name="txtcurrentdelivery[]"
                                class="text_boxes_numeric" style="width:35px;"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty'];
                                //$total_balance += $roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>"
                               disabled <? //echo $roll_dissable;
                               ?>/>
                               <? echo $roll_details_array[$val[csf("barcode_no")]]['qnty'];
                                $total_balance += $roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>
                            </td>
                           	<td width="50">
                                <input type="hidden" id="hidden_qnty_in_pcs_<? echo $i; ?>" name="hidden_qnty_in_pcs[]"
                                class="text_boxes_numeric" style="width:35px;"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty_in_pcs']; ?>"/>
                                <input type="text" id="txtqntyinpcs_<? echo $i; ?>" name="txtqntyinpcs[]"
                                class="text_boxes_numeric" style="width:35px;"
                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty_in_pcs']; $total_qnty_in_pcs += $roll_details_array[$val[csf("barcode_no")]]['qnty_in_pcs']; ?>"
                               disabled <? //echo $roll_dissable;
                               ?>/>
                            </td>
                           	<td width="50"><p>
                            <input type="hidden" id="hidden_reject_qnty_<? echo $i; ?>"
                            name="hidden_reject_qnty[]" class="text_boxes_numeric"
                            style="width:30px;"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['reject_qnty']; ?>"/>
                            <input type="text" id="txtrejectqnty_<? echo $i; ?>"
                            name="txtrejectqnty[]" class="text_boxes_numeric"
                            style="width:35px;"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['reject_qnty']; ?>"
                            disabled/>&nbsp;</p>
                       		</td>
                           	<td width="45">
                            <input type="text" id="txtroll_<? echo $i; ?>" name="txtroll[]" class="text_boxes_numeric"
                            style="width:35px;"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>"
                            disabled/>
                            <input type="hidden" id="hideroll_<? echo $i; ?>"
                            value="<? echo $update_row_check[$index_pk]["roll"];
                            $to_roll += $roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>">
                           </td>
                           <td width="50" align="center"><p><? echo $roll_details_array[$val[csf("barcode_no")]]['coller_cuff_size']; ?> &nbsp;</p>

                           	<td width="50" align="center" id="floor_td_to" title="<? echo $lib_floor_arr[$roll_details_array[$val[csf("barcode_no")]]['floor_id']] ; ?>"><p><?
                           	$argument = "'".$i.'_0'."'";
							if($isFloorRoomRackShelfDisable==1)
							{
								echo create_drop_down( "cbo_floor_to_$i", 50,$lib_floor_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['floor_id'], "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",$isFloorRoomRackShelfDisable,"","","","","","","cbo_floor_to[]" ,"onchange_void","title**tttt");
							}
							else
							{
								echo create_drop_down( "cbo_floor_to_$i", 50,$lib_floor_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['floor_id'], "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",$isFloorRoomRackShelfDisable,"","","","","","","cbo_floor_to[]" ,"onchange_void","title**ttyy");
							}
							?>
                            <!--<input type="text" class="text_boxes_numeric" id="txtBin_<?// echo $i; ?>"
                            name="txtBin[]" style="width:30px;" /onBlur="copy_all('<?// echo $i . "_3"; ?>')" >-->
                            </p></td>
                            <td width="50" align="center" id="room_td_to" title="<? echo $lib_room_arr[ $roll_details_array[$val[csf("barcode_no")]]['room']]; ?>"><p><?
                            $argument = "'".$i.'_1'."'";
							if($isFloorRoomRackShelfDisable==1)
							{
								echo create_drop_down( "cbo_room_to_$i", 50,$lib_room_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['room'], "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",$isFloorRoomRackShelfDisable,"","","","","","","cbo_room_to[]","onchange_void" );
							}
							else
							{
								echo create_drop_down( "cbo_room_to_$i", 50,$lib_room_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['room'], "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",$isFloorRoomRackShelfDisable,"","","","","","","cbo_room_to[]","onchange_void" );
							}
							?>
                            <!-- <input type="text" class="text_boxes_numeric"
                                id="txtRoom_<?// echo $i; ?>"
                                name="txtRoom[]"
                                style="width:35px;"
                                onBlur="copy_all('<?// echo $i . "_0"; ?>')"/> -->
                            </p></td>
                            <td width="50" align="center" id="rack_td_to" title="<? echo $lib_rack_arr[ $roll_details_array[$val[csf("barcode_no")]]['rack']]; ?>"><p><?
                            $argument = "'".$i.'_2'."'";
							if($isFloorRoomRackShelfDisable==1)
							{
								echo create_drop_down( "txt_rack_to_$i", 50,$lib_rack_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['rack'], "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",$isFloorRoomRackShelfDisable,"","","","","","","txt_rack_to[]","onchange_void" );
							}
							else
							{
								echo create_drop_down( "txt_rack_to_$i", 50,$lib_rack_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['rack'], "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",$isFloorRoomRackShelfDisable,"","","","","","","txt_rack_to[]","onchange_void" );
							}
							?>
                            <!--<input type="text" class="text_boxes" id="txtRack_<?// echo $i; ?>"
                                name="txtRack[]" style="width:30px;"
                                onBlur="copy_all('<?// echo $i . "_1"; ?>')"/>-->
                            </p></td>
                            <td width="50" align="center" id="shelf_td_to" title="<? echo $lib_shelf_arr[ $roll_details_array[$val[csf("barcode_no")]]['self']]; ?>"><p>
                            <?
                            	$argument = "'".$i.'_3'."'";
                            	echo create_drop_down( "txt_shelf_to_$i", 50,$lib_shelf_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['self'], "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",$isFloorRoomRackShelfDisable,"","","","","","","txt_shelf_to[]","onchange_void" );
                            ?>
                            <!--<input type="text" class="text_boxes_numeric"
                            id="txtSelf_<?// echo $i; ?>"
                            name="txtSelf[]" style="width:35px;"
                            onBlur="copy_all('<?// echo $i . "_2"; ?>')"/>-->
                            </p></td>

                            <td width="50" align="center" id="bin_td_to" title="<? echo $lib_bin_arr[ $roll_details_array[$val[csf("barcode_no")]]['bin_box']]; ?>"><p>
                            <?
                            	$argument = "'".$i.'_4'."'";
                            	echo create_drop_down( "txt_bin_to_$i", 50,$lib_bin_arr,"", 1, "--Select--", $roll_details_array[$val[csf("barcode_no")]]['bin_box'], "copy_all($argument);",$isFloorRoomRackShelfDisable,"","","","","","","txt_bin_to[]","onchange_void" );
                            ?>
                            </p></td>
                            <td width="80" align="center" style="word-break:break-all;">
                            <input type="hidden" id="knittingsource_<? echo $i; ?>" name="knittingsource[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']; ?>">
                            <?
                            echo $knitting_source[$roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']];
                            ?>
                            </td>
                            <td width="70" align="center"
                            id="receive_date">
                            <?php
                            if ($roll_details_array[$val[csf("barcode_no")]]['receive_date'] != '0000-00-00') echo change_date_format($roll_details_array[$val[csf("barcode_no")]]['receive_date']); else echo ""; ?>
                            </td>
                            <?
                            if ($roll_details_array[$val[csf("barcode_no")]]['booking_without_order'] == 1)
	                        {
	                            ?>
	                            <td width="40" align="center">
	                            <?
		                            $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
		                            $within_group 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["within_group"];

		                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] == 1)
		                            {
			                            if ($within_group == 1)
			                            {
			                            	echo $job_arr[$sales_booking_no]['year'];
			                            }
		                            }
		                            else
	                            	{
	                            		echo $no_order_details_array[$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']]['year'];
	                            	}
	                            ?>
	                            </td>
	                            <td width="50" align="center"><p>
	                            <input type="hidden" id="hiddenPoId_<? echo $i; ?>" name="hiddenPoId[]"
	                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id'];
	                            ?>"/>
	                            <? //echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['job_no'];
	                            $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
	                            $within_group 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["within_group"];
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] == 1) {
	                            if ($within_group == 1) {
	                            echo $job_arr[$sales_booking_no]['job_no_mst'];
	                            }else{
	                            echo "";
	                            }
	                            }
	                            ?>&nbsp;
	                            </p>
	                            </td>
	                            <td width="60">
	                            <input type="hidden" id="hiddenBuyer_<? echo $i; ?>" name="hiddenBuyer_<? echo $i; ?>"
	                            value="<? //echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_id'];
	                            ?>"/>
	                            <?
	                            $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
	                            $within_group 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["within_group"];
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] == 1) {
	                            if ($within_group == 1) {
	                            echo $job_arr[$sales_booking_no]['buyer_id'];
	                            }
	                            }else {
	                            echo $no_order_details_array[$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']]['buyer_id'];
	                            } //echo $buyer_array[$kint_prod_buyer[$val[csf("barcode_no")]]];
	                            ?>
	                            </td>
	                            <td width="70" style="word-break:break-all;">
	                            <input type="hidden" id="hideorder_<? echo $i; ?>" name="hideorder_<? echo $i; ?>"
	                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['booking_id']; ?>"/>
	                            <?
	                            echo $sales_arr[$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']]["po_number"];
	                            ?>
	                            </td>
	                            <td width="100" style="word-break:break-all;" title="Sales/Booking No"></td>
	                            <td width="70" style="word-break:break-all;" title="Int. Ref."></td>
	                            <?
	                        }
                            else
	                        {
	                            ?>
	                            <td width="40" align="center">
	                            <?
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] != 1) {
	                            echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['year'];
	                            }else{
	                            if ($within_group == 1) {
	                            echo $job_arr[$sales_booking_no]['year'];
	                            }else{
	                            echo $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["year"];
	                            }
	                            }
	                            ?>
	                            </td>
	                            <td width="50" align="center">
	                            <input type="hidden" id="hiddenPoId_<? echo $i; ?>" name="hiddenPoId[]"
	                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"/>
	                            <?
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] != 1) {
	                            echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['job_no'];
	                            ?>
	                            <input type="hidden" id="hiddenIsSales_<? echo $i; ?>" name="hiddenIsSales_[]"
	                            value="0"/>
	                            <?
	                            } else {
	                            ?>
	                            <input type="hidden" id="hiddenIsSales_<? echo $i; ?>" name="hiddenIsSales_[]"
	                            value="1"/>
	                            <?
	                            $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
	                            $within_group 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["within_group"];
	                            if ($within_group == 1) {
	                            $job_no=$job_arr[$sales_booking_no]['job_no_mst'];
	                            $job_nos=explode("-",$job_no);
	                            $job_no_prefix= (int)$job_nos[2];
	                            echo $job_no_prefix;
	                            }else{
	                            echo "";
	                            }
	                            }
	                            ?>
	                            </td>
	                            <td width="60"><p>
	                            <input type="hidden" id="hiddenBuyer_<? echo $i; ?>"
	                            name="hiddenBuyer_<? echo $i; ?>"
	                            value="<? echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_id']; ?>"/>
	                            <?
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] != 1)
	                            {
	                            	echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['buyer_name'];
	                            }
	                            else
	                            {
		                            $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
		                            $within_group 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["within_group"];
		                            if ($within_group == 1)
		                            {
		                            	echo $job_arr[$sales_booking_no]['buyer_id'];
		                            }
		                            else
		                            {
		                            	echo $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["buyer_id"];
		                            }
	                            }
	                            ?>&nbsp;</p></td>

	                            <td width="70" style="word-break:break-all;">
	                            <input type="hidden" id="hideorder_<? echo $i; ?>" name="hideorder_<? echo $i; ?>"
	                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"/>
	                            <?
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] != 1)
	                            {
	                            	echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['po_number'];
	                            }
	                            else
	                            {
		                            //echo $salesOrderArr[$kint_prod_data[$val[csf("grey_sys_id")]]["booking_id"]]['job_no'] ;
		                            echo $salesOrderArr[$val[csf("barcode_no")]]['job_no'];
	                            }
	                            ?>
	                            </td>
	                            <td width="100" style="word-break:break-all;">
	                            <?
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] != 1)
	                            {
	                            	echo '';
	                            }
	                            else
	                            {
		                            echo $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
	                            }
	                            ?>
	                            </td>
	                            <td width="70" style="word-break:break-all;" title="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>">
	                            <?
	                            if ($roll_details_array[$val[csf("barcode_no")]]['is_sales_order'] != 1)
	                            {
	                            	echo $po_details_array[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]['int_ref'];
	                            }
	                            else
	                            {
		                            $sales_booking_no 	= $sales_arr[$roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']]["sales_booking_no"];
									echo $job_arr[$sales_booking_no]['int_ref'];
	                            }
	                            ?>
	                            </td>
	                            <?
	                        }
                            ?>
                            <td width="80"><p>
                            <input type="hidden" id="hidden_bodypart_<? echo $i; ?>" name="hidden_bodypart[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['body_part_id']; ?>"/>
                            <? echo $body_part[$roll_details_array[$val[csf("barcode_no")]]['body_part_id']]; ?>
                            &nbsp;
                            </p>
                            </td>
                            <td width="80" style="word-break:break-all;"><p>
                            <input type="hidden" id="hideconstruction_<? echo $i; ?>" name="hideconstruction[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['deter_id']; ?>"/>
                            <? echo $constructtion_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>
                            &nbsp;
                            </p>
                            </td>
                            <td width="80" style="word-break:break-all;"><p>
                            <input type="hidden" id="hidecomposition_<? echo $i; ?>" name="hidecomposition[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['deter_id']; ?>"/>
                            <? echo $composition_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>
                            &nbsp;
                            </p>
                            </td>
                            <td width="40" align="center"><p>
                            <input type="hidden" id="hidegsm_<? echo $i; ?>" name="hidegsm[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>"/>
                            <? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>&nbsp;
                            </p>
                            </td>
                            <td width="40" align="center"><p>
                            <input type="hidden" id="hidedia_<? echo $i; ?>" name="hidedia[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>"/>
                            <? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>&nbsp;
                            </p>
                            </td>

                            <td width="80" align="center" style="word-break:break-all;"><p>
                            <input type="hidden" id="hiddenColor_<? echo $i; ?>" name="hiddenColor[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_id']; ?>"/>
                            <? echo $color_names; ?>&nbsp;
                            </p>
                            </td>
                            <td width="80" align="center"><p>
                            <input type="hidden" id="hiddenColorRange_<? echo $i; ?>" name="hiddenColorRange[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_range_id']; ?>"/>
                            <? echo $color_range[$roll_details_array[$val[csf("barcode_no")]]['color_range_id']]; ?>
                            &nbsp;
                            </p>
                            </td>
                            <td width="60" align="center" id="yean_lot_id"><p>
                            <input type="hidden" id="hidden_yeanlot_<? echo $i; ?>" name="hidden_yeanlot[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>"/>
                            <?
                            echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>&nbsp;
                            </p>
                            </td>
                            <td width="60" align="center" id="stitch_length_id"><p>
                            <input type="hidden" id="hidden_stitchLength_<? echo $i; ?>" name="hidden_stitchLength[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['stitch_length']; ?>"/>
                            <?
                            echo $roll_details_array[$val[csf("barcode_no")]]['stitch_length']; ?>&nbsp;
                            </p>
                            </td>
                            <td width="50" align="center"><p>
                            <input type="hidden" id="hiddenUom_<? echo $i; ?>" name="hiddenUom[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['uom']; ?>"/>
                            <? echo $unit_of_measurement[$roll_details_array[$val[csf("barcode_no")]]['uom']]; ?>
                            &nbsp;
                            </p>
                            </td>
                            <td width="50" align="center"><p>
                            <input type="hidden" id="hiddenYeanCount_<? echo $i; ?>" name="hiddenYeanCount[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_count']; ?>"/>
                            <?
                            $yean_count = "";
                            foreach (explode(",", $roll_details_array[$val[csf("barcode_no")]]['yarn_count']) as $y_id) {
                            if ($yean_count == "") $yean_count = $count_arr[$y_id];
                            else                   $yean_count .= "," . $count_arr[$y_id];
                            }
                            echo $yean_count; ?>&nbsp;
                            </p>
                            </td>
                            <td width="50" align="center" style="word-break:break-all;">
                            <input type="hidden" id="hiddenBand_<? echo $i; ?>" name="hiddenBand[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['brand_id']; ?>"/>
                            <? echo $brand_arr[$roll_details_array[$val[csf("barcode_no")]]['brand_id']]; ?>&nbsp;
                            </td>
                            <td width="50" align="center" style="word-break:break-all;">
                            <input type="hidden" id="hiddenShift_<? echo $i; ?>" name="hiddenShift[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['shift_name']; ?>"/>
                            <? echo $shift_name[$roll_details_array[$val[csf("barcode_no")]]['shift_name']]; ?>&nbsp;
                            </td>
                            <td width="60" align="center" style="word-break:break-all;">
                            <input type="hidden" id="hiddenFloorId_<? echo $i; ?>" name="hiddenFloorId[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['production_floor_id']; ?>"/>
                            <? echo $floor_name_array[$roll_details_array[$val[csf("barcode_no")]]['production_floor_id']]; ?>
                            &nbsp;
                            </td>
                            <td width="60" align="center" style="word-break:break-all;">
                            <input type="hidden" id="hiddenMachine_<? echo $i; ?>" name="hiddenMachine[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['machine_no_id']; ?>"/>
                            <? echo $machine_arr[$roll_details_array[$val[csf("barcode_no")]]['machine_no_id']]; ?>
                            &nbsp;
                            </td>
                            <td align="right" style="word-break:break-all;">
                            <? echo $roll_details_array[$val[csf("barcode_no")]]['rate']; ?>
                            <input type="hidden" id="rollRate_<? echo $i; ?>" name="rollRate[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['rate']; ?>"/>
                            <input type="hidden" id="knittingCharge_<? echo $i; ?>" name="knittingCharge[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['knitting_charge']; ?>"/>
                            <input type="hidden" id="yarnRate_<? echo $i; ?>" name="yarnRate[]"
                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_rate']; ?>"/>
                            </td>
                        </tr>
                        <?
                        $i++;
                        $previousBarcode .= $val[csf("barcode_no")].",";
                        $previousTransid .= $roll_details_array[$val[csf("barcode_no")]]['trans_id'].",";
                        $previousDtlsid .= $roll_details_array[$val[csf("barcode_no")]]['dtls_id'].",";
                        $previousProdid .= $roll_details_array[$val[csf("barcode_no")]]['prod_id'].",";
					}
                    $previousBarcode = implode(",",array_unique(explode(",",chop($previousBarcode,","))));
                    $previousTransid = implode(",",array_unique(explode(",",chop($previousTransid,","))));
                    $previousDtlsid = implode(",",array_unique(explode(",",chop($previousDtlsid,","))));
                    $previousProdid = implode(",",array_unique(explode(",",chop($previousProdid,","))));
					?>
					</tbody>
                </table>
                <table width="2530" class="rpt_table" id="tbl_footer" cellpadding="0" cellspacing="1" rules="all">
					<tfoot>
                        <input type="hidden" name="previous_hdn_barcode" id="previous_hdn_barcode" value="<? echo $previousBarcode; ?>">
                        <input type="hidden" name="previous_hdn_trans_id" id="previous_hdn_trans_id" value="<? echo $previousTransid; ?>">
                        <input type="hidden" name="previous_hdn_dtls_id" id="previous_hdn_dtls_id" value="<? echo $previousDtlsid; ?>">
                        <input type="hidden" name="previous_hdn_prod_id" id="previous_hdn_prod_id" value="<? echo $previousProdid; ?>">

                        <input type="hidden" name="previous_challan_discurd" id="previous_challan_discurd" value="">

                        <th width="40"><input type="hidden" value="<? echo $i - 1; ?>" id="txt_tr_length" name="txt_tr_length"/>
                        </th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="50">Total:
                        <input type="hidden" id="total_row" name="total_row" value="<? echo $total_row; ?>"/></th>
                        <th width="50" id="value_tot_qnty"><? echo number_format($total_balance, 2); ?></th>
                        <th width="50"><? echo $total_qnty_in_pcs; ?></th>
                        <th colspan="33"></th>
					</tfoot>
                </table>
            </div>
		</form>
	</div>
	<?
    exit();
}

if ($action == "grey_item_details")
{
	$data = explode("**", $data);

	$floor_name_array = return_library_array("select a.id, a.floor_name from lib_prod_floor a,lib_machine_name b where a.id=b.floor_id and b.category_id=1", "id", "floor_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";

	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$update_barcode_arr = array();
	$data_array_mst = sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_no, c.barcode_no, c.id as roll_id, c.roll_no
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(58) and c.entry_form in(58) and a.booking_no='" . $data[0] . "' and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(0) ");
	foreach ($data_array_mst as $inf)
	{
		$update_barcode_arr[] = "'" . $inf[csf('barcode_no')] . "'";
	}

	if (count($update_barcode_arr) > 0) $update_barcode_cond = " and c.barcode_no not in (" . implode(",", $update_barcode_arr) . ") ";

	$sql_result = sql_select("SELECT a.id, a.delevery_date, a.company_id, a.order_status, a.location_id, a.buyer_id, c.barcode_no, b.id as dtls_id, c.id as roll_id, c.is_sales, c.po_breakdown_id, c.booking_without_order, c.qc_pass_qnty_pcs,c.coller_cuff_size
	from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and b.barcode_num=c.barcode_no and a.sys_number='" . $data[0] . "' and a.entry_form in(56) and c.entry_form in(56) $update_barcode_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and c.status_active=1 order by c.id");

	$delivery_barcode_arr = array();
	foreach ($sql_result as $bar_row)
	{
		$delivery_barcode_arr[$bar_row[csf('barcode_no')]] = $bar_row[csf('barcode_no')];

		if($bar_row[csf('booking_without_order')] == 1)
		{
			$n_non_ord_id_arr[$bar_row[csf('po_breakdown_id')]] = $bar_row[csf('po_breakdown_id')];
		}
		else
		{
			if($bar_row[csf('is_sales')] == 1)
			{
				$n_sales_id_arr[$bar_row[csf('po_breakdown_id')]] = $bar_row[csf('po_breakdown_id')];
			}
			else
			{
				$n_order_id_arr[$bar_row[csf('po_breakdown_id')]] = $bar_row[csf('po_breakdown_id')];
			}
		}
	}

	$delivery_barcode_arr = array_filter($delivery_barcode_arr);
	if(!empty($delivery_barcode_arr))
	{
		if($db_type==2 && count($delivery_barcode_arr)>999)
		{
			$barcodeArr =array_chunk($delivery_barcode_arr, 999);
			$all_barcode_cond=" and (";
			foreach ($barcodeArr as $barcode)
			{
				$all_barcode_cond .="c.barcode_no in (".implode(",", $barcode).") or ";
			}
			$all_barcode_cond=chop($all_barcode_cond,"or ");
			$all_barcode_cond.=")";
		}
		else
		{
			$all_barcode_cond=" and c.barcode_no in (".implode(",", $delivery_barcode_arr).")";
		}

		$data_array = sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_id, a.booking_no, a.knitting_source, a.knitting_company, a.buyer_id, c.booking_without_order, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.body_part_id, b.yarn_lot, b.brand_id, b.shift_name, b.floor_id, b.machine_no_id, c.rate, b.yarn_count, b.uom, b.color_id, b.color_range_id, b.stitch_length, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.is_sales, c.qc_pass_qnty_pcs, b.kniting_charge, b.yarn_rate, c.coller_cuff_size, c.fso_delivery_type
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $all_barcode_cond order by c.id desc");
	}

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$non_ord_samp_booking_id = implode(",",array_filter($n_non_ord_id_arr));
	if($non_ord_samp_booking_id!="")
	{
		$sqlsamplnoOrder = sql_select("select id,insert_date,buyer_id from wo_non_ord_samp_booking_mst where id in($non_ord_samp_booking_id) and status_active=1 and is_deleted=0");

		foreach ($sqlsamplnoOrder as $row) {
			$no_order_details_array[$row[csf("id")]]['buyer_id'] = $buyer_array[$row[csf("buyer_id")]];
			$no_order_details_array[$row[csf("id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		}
	}

	$sales_order_arr = array_filter($n_sales_id_arr);
	$sales_order_ids = implode(",",$sales_order_arr);
	if(!empty($sales_order_arr))
	{
		if($db_type==2 && count($sales_order_arr)>999)
		{
			$sales_orderArr =array_chunk($sales_order_arr, 999);
			$all_salesid_cond=" and (";
			foreach ($sales_orderArr as $value)
			{
				$all_salesid_cond .="a.id in (".implode(",", $value).") or ";
			}
			$all_salesid_cond=chop($all_salesid_cond,"or ");
			$all_salesid_cond.=")";
		}
		else
		{
			$all_salesid_cond=" and a.id in ($sales_order_ids)";
		}

		$salesOrder_id = sql_select("SELECT a.id, a.job_no, a.within_group,a.buyer_id, a.sales_booking_no, a.po_buyer, a.po_job_no, a.insert_date  from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted =0 $all_salesid_cond");
		foreach($salesOrder_id as $srow)
		{
			$sales_order_ref_arr[$srow[csf('id')]]['id'] = $srow[csf('id')];
			$sales_order_ref_arr[$srow[csf('id')]]['sales_order'] = $srow[csf('job_no')];
			$sales_order_ref_arr[$srow[csf('id')]]['job_no'] = $srow[csf('po_job_no')];
			$sales_order_ref_arr[$srow[csf('id')]]['year'] =  date("Y", strtotime($srow[csf("insert_date")]));
			$sales_order_ref_arr[$srow[csf("id")]]['sales_booking_no']=$srow[csf("sales_booking_no")];
			if($srow[csf('within_group')] == 2)
			{
				$sales_order_ref_arr[$srow[csf('id')]]['buyer_id'] =  $srow[csf("buyer_id")];
			}
			else
			{
				$sales_order_ref_arr[$srow[csf('id')]]['buyer_id'] =  $srow[csf("po_buyer")];
			}
			$sales_booking_no .= "'".$srow[csf("sales_booking_no")]."',";
		}
		$all_sales_booking_nos = rtrim($sales_booking_no,", ");
		$int_data_array=sql_select("SELECT b.job_no,a.buyer_id,b.booking_no, c.grouping from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos) group by b.job_no,a.buyer_id,b.booking_no, c.grouping");
		$po_details_arr=array();
		foreach($int_data_array as $row)
		{
			// $po_details_arr[$row[csf("booking_no")]]['job_no']=$row[csf("job_no")];
			$po_details_arr[$row[csf("booking_no")]]['int_ref']=$row[csf("grouping")];
		}
	}

	$order_id_arr = array_filter($n_order_id_arr);
	$order_ids = implode(",",$order_id_arr);
	if(!empty($order_id_arr))
	{
		if($db_type==2 && count($order_id_arr)>999)
		{
			$order_id_chunk_arr =array_chunk($order_id_arr, 999);
			$all_order_id_cond=" and (";
			foreach ($order_id_chunk_arr as $value)
			{
				$all_order_id_cond .="b.id in (".implode(",", $value).") or ";
			}
			$all_order_id_cond=chop($all_order_id_cond,"or ");
			$all_order_id_cond.=")";
		}
		else
		{
			$all_order_id_cond=" and b.id in ($order_ids)";
		}

		$po_sql = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, b.grouping, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $all_order_id_cond");

		$po_details_array = array();
		foreach ($po_sql as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $buyer_array[$row[csf("buyer_name")]];
			$po_details_array[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['int_ref'] = $row[csf("grouping")];
		}
	}

	$roll_details_array = array();
	$barcode_array = array();
	$order_ids_arr = array();

	if (!empty($data_array))
	{
		foreach ($data_array as $row)
		{
			$booking_no_id = $row[csf('booking_no')];
			$color_id_show='';$color='';
			$color_id=explode(",",$row[csf('color_id')]);
			foreach($color_id as $val)
			{
				if($val>0) $color_id_show.=$color_arr[$val].",";
				if($val>0) $color.=$val.",";
			}
			$color_id_show=chop($color_id_show,',');
			$color=chop($color,',');

			$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
			$roll_details_array[$row[csf("barcode_no")]]['is_sales_order'] = $is_salesOrder;
			$roll_details_array[$row[csf("barcode_no")]]['company_id'] = $row[csf("company_id")];
			$roll_details_array[$row[csf("barcode_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$roll_details_array[$row[csf("barcode_no")]]['recv_number'] = $row[csf("recv_number")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_date'] = change_date_format($row[csf("receive_date")]);
			$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id'] = $row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source'] = $knitting_source[$row[csf("knitting_source")]];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id'] = $row[csf("knitting_company")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_without_order'] = $row[csf("booking_without_order")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_id'] = $row[csf("booking_id")];

			if ($row[csf("knitting_source")] == 1) {
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $company_arr[$row[csf("knitting_company")]];
			} else if ($row[csf("knitting_source")] == 3) {
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
			}
			$roll_details_array[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$roll_details_array[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id'] = $row[csf("brand_id")];
			$roll_details_array[$row[csf("barcode_no")]]['shift_name'] = $row[csf("shift_name")];
			$roll_details_array[$row[csf("barcode_no")]]['floor_id'] = $row[csf("floor_id")];
			$roll_details_array[$row[csf("barcode_no")]]['machine_no_id'] = $row[csf("machine_no_id")];
			$roll_details_array[$row[csf("barcode_no")]]['yarn_count'] = $row[csf("yarn_count")];
			$roll_details_array[$row[csf("barcode_no")]]['color_id_show'] = $color_id_show;
			$roll_details_array[$row[csf("barcode_no")]]['color_id'] = $color;
			$roll_details_array[$row[csf("barcode_no")]]['color_range_id'] = $row[csf("color_range_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['uom'] = $row[csf("uom")];
			$roll_details_array[$row[csf("barcode_no")]]['dtls_id'] = $row[csf("dtls_id")];
			$roll_details_array[$row[csf("barcode_no")]]['prod_id'] = $row[csf("prod_id")];
			$roll_details_array[$row[csf("barcode_no")]]['deter_id'] = $row[csf("febric_description_id")];
			$roll_details_array[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$roll_details_array[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
			$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no'] = $row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
			$roll_details_array[$row[csf("barcode_no")]]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['qnty_in_pcs'] = $row[csf("qc_pass_qnty_pcs")]*1;
			$roll_details_array[$row[csf("barcode_no")]]['reject_qnty'] = number_format($row[csf("reject_qnty")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['rate'] = number_format($row[csf("rate")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['knitting_charge'] = number_format($row[csf("kniting_charge")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['yarn_rate'] = number_format($row[csf("yarn_rate")], 2, '.', '');
			$roll_details_array[$row[csf("barcode_no")]]['coller_cuff_size'] = $row[csf("coller_cuff_size")];
			$roll_details_array[$row[csf("barcode_no")]]['fso_delivery_type'] = $row[csf("fso_delivery_type")];
			$barcode_array[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}
	}
	?>
	<div style="width:2530px;">
		<form name="delivery_details" id="delivery_details" autocomplete="off">
			<div id="report_print" style="width:2530px;">
				<table width="2530" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1"
				rules="all">
					<thead>
						<th width="40">SL<br><input type="checkbox" id="txt_check_all" name="txt_check_all" onClick="check_all(this.value)"></th>
						<th width="80">Barcode</th>
						<th width="100">System Id</th>
						<th width="100">Progm/ Booking No</th>
						<th width="100">Production Basis</th>
						<th width="50">Prod. Id</th>
						<th width="50">Current Delv.</th>
	                    <th width="50">Qty. in pcs</th>
						<th width="45">Reject Qty.</th>
	                    <th width="50">Roll</th>
	                    <th width="50">Size</th>
	                    <th width="50"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
						<th width="50"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
						<th width="50"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
						<th width="50"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
						<th width="50"><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
						<th width="80">Knitting Source</th>
						<th width="70">Prd. date</th>
						<th width="40">Year</th>
						<th width="50">Job No</th>
						<th width="60">Buyer</th>
						<th width="70">Order/FSO No</th>
						<th width="100">Sales/Booking No</th>
						<th width="70">Internal Ref No.</th>
						<th width="80">Body Part</th>
						<th width="80">Construction</th>
						<th width="80">Composition</th>
						<th width="40">GSM</th>
						<th width="40">Dia</th>
						<th width="80">Fabric Color</th>
						<th width="80">Color Range</th>
						<th width="60">Yarn Lot</th>
						<th width="60">Stitch Length</th>
						<th width="50">UOM</th>
						<th width="50">Yarn Count</th>
						<th width="50">Brand</th>
						<th width="50">Shift Name</th>
						<th width="60">Prod. Floor</th>
						<th width="60">Machine No.</th>
						<th>Rate</th>
					</thead>
				</table>
			</div>
			<div style="width:2530px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;"
			id="scroll_body">
				<table width="2530" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1"
				rules="all">
					<tbody>
						<?
						$total_row = count($sql_result);
						$current_row_array = array();
						$i = 1;
						if (!empty($sql_result))
						{
							foreach ($sql_result as $val)
							{
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr__<? echo $i; ?>">
									<td width="40" align="center" style="word-break:break-all;"><? echo $i; ?>
										&nbsp;&nbsp;&nbsp;<input type="checkbox" id="checkedId_<? echo $i; ?>"
										name="checkedId[]" checked="checked" value="0"/>
										<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" value="<? echo $i; ?>" readonly/>
										<input type="hidden" id="hidesysid_<? echo $i; ?>"
										name="hidesysid_<? echo $i; ?>"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['mst_id']; ?>"/>
										<input type="hidden" id="hiden_transid_<? echo $i; ?>"
										name="hiden_transid[]"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['trans_id']; ?>"/>
										<input type="hidden" id="hidden_greyid_<? echo $i; ?>"
										name="hidden_greyid[]"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['dtls_id']; ?>"/>
										<input type="hidden" id="hidden_rollid_<? echo $i; ?>"
										name="hidden_rollid[]"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_id']; ?>"/>
										<input type="hidden" id="hidden_stl_<? echo $i; ?>" name="hidden_stl[]"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['stitch_length']; ?>"/>
										<input type="hidden" id="hidden_withoutOrder_<? echo $i; ?>" name="hidden_withoutOrder[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['booking_without_order']; ?>"/>
										<input type="hidden" id="isSales_<? echo $i; ?>" name="isSales[]" value="<? echo $val[csf("is_sales")]; ?>"/>
										<input type="hidden" id="fsoDeliveryType_<? echo $i; ?>" name="fsoDeliveryType[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['fso_delivery_type']; ?>"/>
									</td>
									<td width="80" style="word-break:break-all;"><input type="hidden"
										id="hidesysnum_<? echo $i; ?>"
										name="hidesysnum_<? echo $i; ?>"
										value="<? echo $row[csf("recv_number")]; ?>"/>
										<input type="hidden" id="hidenBarcode_<? echo $i; ?>" name="hidenBarcode[]"
										value="<? echo $barcode_array[$val[csf("barcode_no")]]; ?>"/>
										<?
										echo $barcode_array[$val[csf("barcode_no")]];
										?>&nbsp;
									</td>
									<td width="100" style="word-break:break-all;">
										<input type="hidden" id="hidenReceiveId_<? echo $i; ?>"
										name="hidenReceiveId[]"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['mst_id']; ?>"/>
										<?
										echo $roll_details_array[$val[csf("barcode_no")]]['recv_number'];
										?>
									</td>
									<td width="100" align="center"><p>
										<input type="hidden" id="hideprogrum_<? echo $i; ?>"
										name="hideprogrum_<? echo $i; ?>"
										value="<? echo $row[csf("prog_id")]; ?>"/>
										<input type="hidden" id="hideBooking_<? echo $i; ?>"
										name="hideBooking_<? echo $i; ?>"
										value="<? echo $roll_details_array[$val[csf("barcode_no")]]['booking_no']; ?>"/>
										<? if ($roll_details_array[$val[csf("barcode_no")]]['receive_basis'] == 0) echo "Independent"; else  echo $roll_details_array[$val[csf("barcode_no")]]['booking_no']; ?>
									&nbsp;</p>
									</td>
			                        <td width="100" align="center"><p>
			                            <input type="hidden" id="txtBasis_<? echo $i; ?>" name="txtBasis[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['receive_basis']; ?>">
			                            <?
			                            $receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => 'Sales Order');
			                            echo $receive_basis[$roll_details_array[$val[csf("barcode_no")]]['receive_basis']];
			                            ?>&nbsp;
			                        </p>
			                        </td>
			                        <td width="50" align="center"><p>
			                            <input type="hidden" id="hideprodid_<? echo $i; ?>"
			                            name="hideprodid_<? echo $i; ?>"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['prod_id']; ?>"/>
			                            <? echo $roll_details_array[$val[csf("barcode_no")]]['prod_id']; ?>
			                            &nbsp;
			                        </p></td>
			                        <td width="50" align="right">
			                            <p>
			                                <input type="hidden" id="hidden_delivery_qty_<? echo $i; ?>"
			                                name="hidden_delivery_qty[]" class="text_boxes_numeric"
			                                style="width:30px;"
			                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>"/>
			                                <input type="hidden" id="txtcurrentdelivery_<? echo $i; ?>"
			                                name="txtcurrentdelivery[]" class="text_boxes_numeric"
			                                style="width:35px;"
			                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty'];
			                                //$total_balance += $roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>"
			                                disabled/>
			                                <? echo $roll_details_array[$val[csf("barcode_no")]]['qnty'];
			                                $total_balance += $roll_details_array[$val[csf("barcode_no")]]['qnty']; ?>

			                                <input type="hidden" id="hidden_reject_fabric_recv_qnty_<? echo $i; ?>"
			                                name="hidden_reject_fabric_recv_qnty[]" class="text_boxes_numeric"
			                                style="width:30px;"
			                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['reject_qnty']; ?>"/>

			                            &nbsp;</p>
			                        </td>
			                       	<td width="50">
			                            <input type="hidden" id="hidden_qnty_in_pcs_<? echo $i; ?>" name="hidden_qnty_in_pcs[]"
			                            class="text_boxes_numeric" style="width:35px;"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty_in_pcs']; ?>"/>
			                            <input type="text" id="txtqntyinpcs_<? echo $i; ?>" name="txtqntyinpcs[]"
			                            class="text_boxes_numeric" style="width:35px;"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['qnty_in_pcs']; $total_qnty_in_pcs += $roll_details_array[$val[csf("barcode_no")]]['qnty_in_pcs']; ?>"
			                           disabled <? //echo $roll_dissable;
			                           ?>/>
			                       	</td>

			                        <td width="50">
			                            <p>
			                                <input type="hidden" id="hidden_reject_qnty_<? echo $i; ?>"
			                                name="hidden_reject_qnty[]" class="text_boxes_numeric"
			                                style="width:30px;"
			                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['reject_qnty']; ?>"/>
			                                <input type="text" id="txtrejectqnty_<? echo $i; ?>"
			                                name="txtrejectqnty[]" class="text_boxes_numeric"
			                                style="width:35px;"
			                                value="<? echo $roll_details_array[$val[csf("barcode_no")]]['reject_qnty']; ?>"
			                                disabled/>
			                            &nbsp;</p>
			                        </td>
			                        <td width="45"><p>
			                            <input type="text" id="txtroll_<? echo $i; ?>" name="txtroll[]"
			                            class="text_boxes_numeric" style="width:30px;"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>"
			                            disabled/>
			                            <input type="hidden" id="hideroll_<? echo $i; ?>"
			                            value="<? echo $update_row_check[$index_pk]["roll"];
			                            $to_roll += $roll_details_array[$val[csf("barcode_no")]]['roll_no']; ?>">
			                        &nbsp;</p>
			                        </td>
			                        <td width="50" align="center"><p><? echo $roll_details_array[$val[csf("barcode_no")]]['coller_cuff_size']; ?> &nbsp;</p>
			                        </td>
			                        <td width="50" align="center" id="floor_td_to" class="floor_td_to"><p>
			                            <? $argument = "'".$i.'_0'."'";
			                            echo create_drop_down( "cbo_floor_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
			                            <!--<input type="text" class="text_boxes_numeric" id="txtBin_<?// echo $i; ?>"
			                            name="txtBin[]" style="width:30px;" /onBlur="copy_all('<?// echo $i . "_3"; ?>')" >-->
			                        </p></td>
			                        <td width="50" align="center" id="room_td_to"><p>
			                        <? $argument = "'".$i.'_1'."'";
			                        echo create_drop_down( "cbo_room_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
			                        <!-- <input type="text" class="text_boxes_numeric" id="txtRoom_<? //echo $i; ?>" name="txtRoom[]" style="width:35px;" onBlur="copy_all('<? //echo $i . "_0"; ?>')"/> -->
			                        </p>
			                    	</td>
			                        <td width="50" align="center" id="rack_td_to"><p>
			                        <? $argument = "'".$i.'_2'."'";
			                        echo create_drop_down( "txt_rack_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
			                            <!--<input type="text" class="text_boxes" id="txtRack_<?// echo $i; ?>"
			                            name="txtRack[]" style="width:30px;"
			                            onBlur="copy_all('<?// echo $i . "_1"; ?>')"/>-->
			                        </p></td>

			                        <td width="50" align="center" id="shelf_td_to"><p>
			                        <? $argument = "'".$i.'_3'."'";
			                        echo create_drop_down( "txt_shelf_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
			                            <!--<input type="text" class="text_boxes_numeric"
			                            id="txtSelf_<?// echo $i; ?>"
			                            name="txtSelf[]" style="width:35px;"
			                            onBlur="copy_all('<?// echo $i . "_2"; ?>')"/>-->
			                        </p></td>
			                        <td width="50" align="center" id="bin_td_to"><p>
			                        <? $argument = "'".$i.'_4'."'";
			                        echo create_drop_down( "txt_bin_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
			                            <!--<input type="text" class="text_boxes_numeric"
			                            id="txtSelf_<?// echo $i; ?>"
			                            name="txtSelf[]" style="width:35px;"
			                            onBlur="copy_all('<?// echo $i . "_2"; ?>')"/>-->
			                        </p></td>
			                        <td width="80" align="center" style="word-break:break-all;">
			                            <input type="hidden" id="knittingsource_<? echo $i; ?>"
			                            name="knittingsource[]"
			                            value="<? echo $knitting_source[$roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']]; ?>">
			                            <?
			                            echo $knitting_source[$roll_details_array[$val[csf("barcode_no")]]['knitting_source_id']];
			                            ?>&nbsp;
			                        </td>
			                        <td width="70" align="center" id="receive_date">
			                            <p><? if ($roll_details_array[$val[csf("barcode_no")]]['receive_date'] != '0000-00-00') echo change_date_format($roll_details_array[$val[csf("barcode_no")]]['receive_date']); else echo ""; ?>
			                            </p>
			                        </td>
			                        <?
			                        if ($roll_details_array[$val[csf("barcode_no")]]['booking_without_order'] == 1)
			                        {
			                            ?>
			                            <td width="40" align="center">
			                                <? echo $no_order_details_array[$val[csf("po_breakdown_id")]]['year']; ?>
			                            </td>
			                            <td width="50" align="center">
			                            <p>
			                                <input type="hidden" id="hiddenPoId_<? echo $i; ?>" name="hiddenPoId[]" value="<? echo $val[csf("po_breakdown_id")]; ?>"/>
			                            </p>
			                            </td>
			                            <td width="60">
			                                <p>
			                                    <input type="hidden" id="hiddenBuyer_<? echo $i; ?>" name="hiddenBuyer_<? echo $i; ?>" value="<? echo $no_order_details_array[$val[csf('po_breakdown_id')]]['buyer_id'];?>">
			                                    <? echo $buyer_array[$no_order_details_array[$val[csf("po_breakdown_id")]]['buyer_id']]; ?>
			                                </p>
			                            </td>
			                            <td width="70" style="word-break:break-all;">
			                                <input type="hidden" id="hideorder_<? echo $i; ?>" name="hideorder_<? echo $i; ?>" value="<? echo $val[csf("po_breakdown_id")]; ?>"/>
			                            </td>
			                            <td width="100" style="word-break:break-all;" title="Sales/Booking No"></td>
			                            <td width="70" style="word-break:break-all;" title="non order"></td>
			                        	<?
			                    	}
			                        else
			                        {
			                            ?>
			                            <td width="40" align="center">
			                                <p>
			                                    <?
			                                    if($val[csf("is_sales")] == 1)
			                                    {
			                                        echo $sales_order_ref_arr[$val[csf('po_breakdown_id')]]['year'];
			                                    }else{
			                                        echo $po_details_array[$val[csf('po_breakdown_id')]]['year'];
			                                    }
			                                    ?>
			                                &nbsp;
			                                </p>
			                            </td>
			                            <td width="50" align="center"><p>
			                                <input type="hidden" id="hiddenPoId_<? echo $i; ?>"
			                                name="hiddenPoId[]" value="<? echo $roll_details_array[$val[csf("barcode_no")]]['po_breakdown_id']; ?>"/>
			                                <?
			                                if($val[csf("is_sales")] == 1)
			                                {
			                                    echo $sales_order_ref_arr[$val[csf('po_breakdown_id')]]['job_no'];
			                                }
			                                else
			                                {
			                                    echo $po_details_array[$val[csf("po_breakdown_id")]]['job_no'];
			                                }
			                                ?>&nbsp;
			                            </p>
			                            </td>
			                            <td width="60">
			                                <?
			                                    $buyer_id = "";
			                                    if($val[csf("is_sales")] == 1){
			                                        $buyer_id = $sales_order_ref_arr[$val[csf('po_breakdown_id')]]['buyer_id'];
			                                    }else{
			                                        $buyer_id = $po_details_array[$val[csf('po_breakdown_id')]]['buyer_id'];
			                                    }
			                                ?>
			                                <p>
			                                    <input type="hidden" id="hiddenBuyer_<? echo $i; ?>" name="hiddenBuyer_<? echo $i; ?>" value="<? echo $buyer_id; ?>"/>
			                                    <? echo $buyer_array[$buyer_id]; ?>&nbsp;
			                                </p>
			                            </td>
			                            <td width="70" style="word-break:break-all;">
			                                <input type="hidden" id="hideorder_<? echo $i; ?>" name="hideorder_<? echo $i; ?>" value="<? echo $val[csf('po_breakdown_id')]; ?>"/>
			                                <?
			                                if($val[csf("is_sales")] == 1)
			                                {
			                                    echo $sales_order_ref_arr[$val[csf('po_breakdown_id')]]['sales_order'];
			                                }
			                                else
			                                {
			                                    echo $po_details_array[$val[csf('po_breakdown_id')]]['po_number'];
			                                }
			                                ?>

			                            </td>
			                            <td width="100" style="word-break:break-all;" title="<? echo $val[csf('po_breakdown_id')]; ?>">
			                                <?
			                                if($val[csf("is_sales")] == 1)
			                                {
			                                	echo $sales_booking=$sales_order_ref_arr[$val[csf('po_breakdown_id')]]['sales_booking_no'];
			                                }
			                                else
			                                {
			                                    echo '';
			                                }
			                                ?>

			                            </td>
			                            <td width="70" style="word-break:break-all;" title="<? echo $val[csf('po_breakdown_id')]; ?>">
			                                <?
			                                if($val[csf("is_sales")] == 1)
			                                {
			                                	$sales_booking=$sales_order_ref_arr[$val[csf('po_breakdown_id')]]['sales_booking_no'];
			                                	echo $po_details_arr[$sales_booking]['int_ref'];
			                                }
			                                else
			                                {
			                                    echo $po_details_array[$val[csf('po_breakdown_id')]]['int_ref'];
			                                }
			                                ?>
			                            </td>
			                            <?
			                        }
			                        ?>

			                        <td width="80" style="word-break:break-all;">
			                            <input type="hidden" id="hidden_bodypart_<? echo $i; ?>"
			                            name="hidden_bodypart[]"
			                            value="<? echo $roll_details_array[$val[csf('barcode_no')]]['body_part_id']; ?>"/>
			                            <? echo $body_part[$roll_details_array[$val[csf("barcode_no")]]['body_part_id']]; ?>
			                            &nbsp;
			                        </td>
			                        <td width="80" style="word-break:break-all;">
			                            <input type="hidden" id="hideconstruction_<? echo $i; ?>"
			                            name="hideconstruction[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['deter_id']; ?>"/>
			                            <? echo $constructtion_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>
			                            &nbsp;
			                        </td>
			                        <td width="80" style="word-break:break-all;">
			                            <input type="hidden" id="hidecomposition_<? echo $i; ?>"
			                            name="hidecomposition[]"
			                            value="<? echo $row[csf("detarmination_id")]; ?>"/>
			                            <? echo $composition_arr[$roll_details_array[$val[csf("barcode_no")]]['deter_id']]; ?>
			                            &nbsp;
			                        </td>
			                        <td width="40" align="center"><p>
			                            <input type="hidden" id="hidegsm_<? echo $i; ?>" name="hidegsm[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>"/>
			                            <? echo $roll_details_array[$val[csf("barcode_no")]]['gsm']; ?>&nbsp;
			                        </p>
			                        </td>
			                        <td width="40" align="center"><p>
			                            <input type="hidden" id="hidedia_<? echo $i; ?>" name="hidedia[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>"/>
			                            <? echo $roll_details_array[$val[csf("barcode_no")]]['width']; ?>&nbsp;
			                        </p></td>

			                        <td width="80" align="center" style="word-break:break-all;">
			                            <input type="hidden" id="hiddenColor_<? echo $i; ?>" name="hiddenColor[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_id']; ?>"/>
			                            <? echo $roll_details_array[$val[csf("barcode_no")]]['color_id_show']; ?>
			                            &nbsp;
			                        </td>
			                        <td width="80" align="center" style="word-break:break-all;">
			                            <input type="hidden" id="hiddenColorRange_<? echo $i; ?>"
			                            name="hiddenColorRange[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['color_range_id']; ?>"/>
			                            <? echo $color_range[$roll_details_array[$val[csf("barcode_no")]]['color_range_id']]; ?>
			                            &nbsp;
			                        </td>
			                        <td width="60" align="center" id="yean_lot_id"><p>
			                            <input type="hidden" id="hidden_yeanlot_<? echo $i; ?>"
			                            name="hidden_yeanlot[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>"/>
			                            <?
			                            echo $roll_details_array[$val[csf("barcode_no")]]['yarn_lot']; ?>&nbsp;
			                        </p></td>

			                        <td width="60" align="center" id="stitch_length"><p>
			                            <input type="hidden" id="hidden_stitchLength_<? echo $i; ?>"
			                            name="hidden_stitchLength[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['stitch_length']; ?>"/>
			                            <?
			                            echo $roll_details_array[$val[csf("barcode_no")]]['stitch_length']; ?>&nbsp;
			                        </p></td>

			                        <td width="50" align="center"><p>
			                            <input type="hidden" id="hiddenUom_<? echo $i; ?>" name="hiddenUom[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['uom']; ?>"/>
			                            <? echo $unit_of_measurement[$roll_details_array[$val[csf("barcode_no")]]['uom']]; ?>
			                            &nbsp;
			                        </p>
			                        </td>
			                        <td width="50" align="center"><p>
			                            <input type="hidden" id="hiddenYeanCount_<? echo $i; ?>"
			                            name="hiddenYeanCount[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_count']; ?>"/>
			                            <?
			                            $yean_count = "";
			                            foreach (explode(",", $roll_details_array[$val[csf("barcode_no")]]['yarn_count']) as $y_id) {
			                                if ($yean_count == "") $yean_count = $count_arr[$y_id];
			                                else                   $yean_count .= "," . $count_arr[$y_id];
			                            }
			                            echo $yean_count; ?>&nbsp;
			                        </p>
			                        </td>
			                        <td width="50" align="center" style="word-break:break-all;"><p>
			                            <input type="hidden" id="hiddenBand_<? echo $i; ?>" name="hiddenBand[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['brand_id']; ?>"/>
			                            <? echo $brand_arr[$roll_details_array[$val[csf("barcode_no")]]['brand_id']]; ?>
			                            &nbsp;
			                        </p>
			                        </td>
			                        <td width="50" align="center" style="word-break:break-all;">
			                            <input type="hidden" id="hiddenShift_<? echo $i; ?>" name="hiddenShift[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['shift_name']; ?>"/>
			                            <? echo $shift_name[$roll_details_array[$val[csf("barcode_no")]]['shift_name']]; ?>
			                            &nbsp;
			                        </td>
			                        <td width="60" align="center" style="word-break:break-all;">
			                            <!--<input type="hidden" id="hiddenFloorId_<?// echo $i; ?>"
			                            name="hiddenFloorId[]"
			                            value="<?// echo $roll_details_array[$val[csf("barcode_no")]]['floor_id']; ?>"/>
			                            <?// echo $floor_name_array[$roll_details_array[$val[csf("barcode_no")]]['floor_id']]; ?>
			                            &nbsp;-->
			                        </td>
			                        <td width="60" align="center" style="word-break:break-all;">
			                            <input type="hidden" id="hiddenMachine_<? echo $i; ?>"
			                            name="hiddenMachine[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['machine_no_id']; ?>"/>
			                            <? echo $machine_arr[$roll_details_array[$val[csf("barcode_no")]]['machine_no_id']]; ?>
			                            &nbsp;
			                        </td>
			                        <td align="right" style="word-break:break-all;">
			                            <? echo $roll_details_array[$val[csf("barcode_no")]]['rate']; ?>
			                            <input type="hidden" id="rollRate_<? echo $i; ?>" name="rollRate[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['rate']; ?>"/>
			                            <input type="hidden" id="knittingCharge_<? echo $i; ?>"
			                            name="knittingCharge[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['knitting_charge']; ?>"/>
			                            <input type="hidden" id="yarnRate_<? echo $i; ?>" name="yarnRate[]"
			                            value="<? echo $roll_details_array[$val[csf("barcode_no")]]['yarn_rate']; ?>"/>
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
			<table width="2530" class="rpt_table" id="tbl_footer" cellpadding="0" cellspacing="1" rules="all">
				<tfoot>
					<th width="40"><input type="hidden" value="<? echo $i - 1; ?>" id="txt_tr_length"
						name="txt_tr_length"/></th>
					<th width="80"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="50">Total:
					<input type="hidden" id="total_row" name="total_row" value="<? echo $total_row; ?>"/></th>
					<th width="50" id="value_tot_qnty"><? echo number_format($total_balance, 2); ?></th>
		        	<th width="50"><? echo $total_qnty_in_pcs; ?></th>
					<th colspan="33"></th>
				</tfoot>
			</table>
		</form>
	</div>
	<?
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


		if ($db_type == 0)
		{
			$txt_receive_date = change_date_format($txt_receive_date, "yyyy-mm-dd");
			$txt_boe_mushak_challan_date = change_date_format($txt_boe_mushak_challan_date, "yyyy-mm-dd");
		}
		else
		{
			$txt_receive_date = change_date_format($txt_receive_date, 'yyyy-mm-dd', "-", 1);
			$txt_boe_mushak_challan_date = change_date_format($txt_boe_mushak_challan_date, 'yyyy-mm-dd', "-", 1);
		}

		$garments_nature = 2;
		$category_id = 13;
		$entry_form = 58;
		$prefix = 'KNGFRR';
		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";//defined Later
		//$new_grey_recv_system_id = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', $prefix, date("Y", time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='$entry_form' and $year_cond=" . date('Y', time()) . " order by id desc", "recv_number_prefix", "recv_number_prefix_num"));
		//echo "0";
		//print_r($new_grey_recv_system_id);
		//$id = return_next_id("id", "inv_receive_master", 1);
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,$prefix,$entry_form,date("Y",time()),$category_id ));

		/*
		|--------------------------------------------------------------------------
		| inv_receive_master
		| $data_array preparing here
		|
		|--------------------------------------------------------------------------
		|
		*/
		$data_array = "(" . $id . ",'" . $new_grey_recv_system_id[1] . "'," . $new_grey_recv_system_id[2] . ",'" . $new_grey_recv_system_id[0] . "',$entry_form,$category_id,10," . $cbo_company_id . ",'" . $txt_receive_date . "','" . $txt_receive_chal_no . "','" . $hidden_delivery_id . "','" . $txt_challan_no . "','0'," . $cbo_store_name . "," . $cbo_location_name . "," . $cbo_knitting_source . "," . $cbo_knitting_company . ",'" . $cbo_knitting_location_id . "','" . $yarn_issue_challan_no . "','" . $txt_boe_mushak_challan_no. "','" . $txt_boe_mushak_challan_date. "','" . $txt_remarks . "'," . $garments_nature ."," .$txt_no_bill. "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";


		//echo "10**$barcode_id";die;
		$barcode_id = explode("*", $barcode_id);

		//$dublicate_barcode = return_field_value("barcode_no", "pro_roll_details", "barcode_no in(" . implode(",", $barcode_id) . ") and entry_form=58 and status_active=1", "barcode_no");
		$dublicate_barcode = sql_select("select a.barcode_no as BARCODE_NO from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id = b.id and  a.barcode_no in(" . implode(",", $barcode_id) . ") and a.entry_form in (2,58) and b.trans_id<>0 and a.status_active=1");

		if (!empty($dublicate_barcode))
		{
			echo "11**Duplicate Roll Not Allow. Barcode No = ".$dublicate_barcode[0]["BARCODE_NO"];disconnect($con);
			die;
		}

		$challan_check = sql_select("select b.barcode_num, a.sys_number from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c where a.id = b.mst_id and b.id=c.dtls_id and b.barcode_num in (" . implode(",", $barcode_id) . ") and a.entry_form in (56) and c.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by b.barcode_num, a.sys_number");

		foreach ($challan_check as $val)
		{
			if(str_replace("'", "", $txt_challan_no) != $val[csf("sys_number")])
			{
				$wrongChallanRoll .= $val[csf("barcode_num")].",";
			}
		}
		$wrongChallanRoll = chop($wrongChallanRoll,",");
		if ($wrongChallanRoll !="")
		{
			echo "11**Duplicate challan roll not allow. Below barcodes are not is this challan no.\nBarcode No : ".$wrongChallanRoll;disconnect($con);
			die;
		}

		// If FSO > Sales Order Type = Service and Main Process = only knitting.
		// is_service=1 flag use only for Roll Wise Grey Fabric Delivery Page.
		$fso_sql="SELECT a.id, c.po_breakdown_id, b.body_part_id, b.determination_id, b.color_id, c.barcode_no
		FROM fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and a.id=c.po_breakdown_id and a.id=c.po_breakdown_id  and c.entry_form=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . implode(",", $barcode_id) . ") and a.sales_order_type=2 and b.process_id_main='1'";
		$result=sql_select($fso_sql);
		$is_service_arr=array();
		foreach ($result as $row)
		{
			$is_service_arr[$row[csf("barcode_no")]][$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("color_id")]]=1;
		}
		// echo "<pre>";print_r($is_service_arr);die;

		//echo "10**$dublicate_barcode==jahid";die;
		$grey_recv_num = $new_grey_recv_system_id[0];
		$grey_update_id = $id;
		$receive_number = explode("*", $receive_number);
		$receive_basis = explode("*", $receive_basis);

		$receive_id = explode("*", $receive_id);
		$program_id = explode("*", $program_id);
		$prod_id = explode("*", $prod_id);
		$txt_receive_qnty = explode("*", $issue_qty);
		$txt_reject_fabric_recv_qnty = explode("*", $reject_qnty);
		$roll_no = explode("*", $roll_no);
		$knitting_source = explode("*", $knitting_source);
		$receive_date = explode("*", $receive_date);
		$buyer_id = explode("*", $buyer_id);
		$po_id = explode("*", $po_id);
		$dia = explode("*", $dia);
		$determination_id = explode("*", $determination_id);
		$body_part = explode("*", $body_part);
		$color_id = explode("*", $color_id);
		$color_range = explode("*", $color_range);
		$uom = explode("*", $uom);
		$gsm = explode("*", $gsm);
		$yean_cont = explode("*", $yean_cont);
		$band_id = explode("*", $band_id);
		$floor_id = explode("*", $floor_id);
		$room_no = explode("*", $room_no);
		$rack = explode("*", $rack);
		$self = explode("*", $self);
		$bin = explode("*", $bin);
		$shift_id = explode("*", $shift_id);
		$yean_lot = explode("*", $yean_lot);
		$roll_id = explode("*", $roll_id);
		$machine_name = explode("*", $machine_name);
		$hidden_qty = explode("*", $hidden_qty);
		$stitch_length = explode("*", $stitch_length);
		$roll_rate = explode("*", $roll_rate);
		$knitting_charge = explode("*", $knitting_charge);
		$yarn_rate = explode("*", $yarn_rate);
		$hidden_withoutOrder = explode("*", $hidden_withoutOrder);
		$hidden_booking = explode("*", $hidden_booking);
		$isSales = explode("*", $isSales);
		$hidden_qnty_in_pcs = explode("*", $hidden_qnty_in_pcs);
		$fsoDeliveryType = explode("*", $fsoDeliveryType);

		if ($brand_id == "") $brand_id = 0;
		if ($color_id == "") $color_id = 0;
		$i = 0;

		//$id_prop = return_next_id("id", "order_wise_pro_details", 1);
		//$id_dtls = return_next_id("id", "pro_grey_prod_entry_dtls", 1);
		//$id_roll = return_next_id("id", "pro_roll_details", 1);
		//$id_trans = return_next_id("id", "inv_transaction", 1);

		$cur_st_qnty = 0;
		$data_array_roll = "";
		foreach ($barcode_id as $row)
		{
			$prodData_array[$prod_id[$i]] += $txt_receive_qnty[$i];

			if ($data_array_trans != "") $data_array_trans .= ",";
			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$cons_rate = $roll_rate[$i];
			$cons_amount = $cons_rate * $txt_receive_qnty[$i];
			$prodData_amount_array[$prod_id[$i]] += $cons_amount;

			if (!empty($is_service_arr))
			{
				$is_service=$is_service_arr[$barcode_id[$i]][$po_id[$i]][$body_part[$i]][$determination_id[$i]][$color_id[$i]];
			}
			else{
				$is_service=0;
			}

			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| $data_array_trans preparing here
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_trans .= "(" . $id_trans . "," . $grey_update_id . ",10,'" . $id_dtls . "'," . $cbo_company_id . "," . $prod_id[$i] . "," . $category_id . ",1,'" . $txt_receive_date . "'," . $cbo_store_name . ",'" . $band_id[$i] . "','" . $uom[$i] . "'," . $txt_receive_qnty[$i] . ",'" . $order_rate . "','" . $order_amount . "','" . $uom[$i] . "','" . $txt_receive_qnty[$i] . "','" . $txt_reject_fabric_recv_qnty[$i] . "','" . $cons_rate . "','" . $cons_amount . "','" . $txt_receive_qnty[$i] . "','" . $cons_amount . "','" . $floor_id[$i] . "','" . $machine_name[$i] . "','" . $room_no[$i] . "','" . $rack[$i] . "','" . $self[$i] . "','" . $bin[$i] . "','" . $hidden_withoutOrder[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			/*
			|--------------------------------------------------------------------------
			| pro_grey_prod_entry_dtls
			| $data_array_dtls preparing here
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_dtls .= "(" . $id_dtls . ", " . $grey_update_id . ", " . $id_trans . "," . $prod_id[$i] . ", '" . $body_part[$i] . "', '" . $determination_id[$i] . "', '" . $gsm[$i] . "', '" . $dia[$i] . "', '" . $roll_no[$i] . "', '" . $po_id[$i] . "', " . $txt_receive_qnty[$i] . ", '" . $txt_reject_fabric_recv_qnty[$i] . "', '" . $cons_rate . "', '" . $cons_amount . "', '" . $cbo_uom . "', '" . $yean_lot[$i] . "', '" . $yean_cont[$i] . "', '" . $band_id[$i] . "', '" . $shift_id[$i] . "', '" . $floor_id[$i] . "', '" . $machine_name[$i] . "', '" . $room_no[$i] . "', '" . $rack[$i] . "', '" . $self[$i] . "', '" . $bin[$i] . "', '" . $color_id[$i] . "', '" . $color_range[$i] . "', '" . $stitch_length[$i] . "','" . $knitting_charge[$i] . "', '" . $yarn_rate[$i] . "', " . $_SESSION['logic_erp']['user_id'] . ", '" . $pc_date_time . "')";

			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| $data_array_roll preparing here
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_roll .= "(" . $id_roll . "," . $grey_update_id . "," . $id_dtls . ",'" . $po_id[$i] . "',$entry_form,'" . $txt_receive_qnty[$i] . "','" . $hidden_qty[$i] . "','" . $roll_id[$i] . "','" . $roll_no[$i] . "','" . $barcode_id[$i] . "','" . $cons_rate . "','" . $cons_amount . "','" . $hidden_booking[$i] . "','" . $hidden_withoutOrder[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$isSales[$i].",".$hidden_qnty_in_pcs[$i].",".$is_service . ",'" . $fsoDeliveryType[$i] ."'),";

			$inserted_roll_id_arr[$id_roll] =  $id_roll;

			/*
			|--------------------------------------------------------------------------
			| order_wise_pro_details
			| $data_array_prop preparing here
			|--------------------------------------------------------------------------
			|
			*/
			if ($hidden_withoutOrder[$i] != 1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . ", " . $id_trans . ", 1, $entry_form, " . $id_dtls . ", '" . $po_id[$i] . "' ," . $prod_id[$i] . ", '" . $txt_receive_qnty[$i] . "', " . $_SESSION['logic_erp']['user_id'] . ", '" . $pc_date_time . "',".$isSales[$i].",".$hidden_qnty_in_pcs[$i].")";
				//$id_prop = $id_prop + 1;
			}

			$all_prod_id .= $prod_id[$i] . ",";
			//$id_roll = $id_roll + 1;
			//$id_dtls++;
			//$id_trans++;
			$i++;
		}
		//echo $data_array_roll;
		//echo "10**".$data_array_roll;
		$prod_id_array = array();
		$all_prod_id = implode(",", array_unique(explode(",", substr($all_prod_id, 0, -1))));
						//------------------Check Receive Date with last Issue Date-------------------
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in($all_prod_id) and store_id =$cbo_store_name and status_active = 1 and is_deleted = 0", "max_date");
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

			if ($receive_date < $max_issue_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}

		//-----------------------------------------------------------------------------
		$field_array_prod_update ="last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$prodResult = sql_select("select id,current_stock,stock_value from product_details_master where id in($all_prod_id)");
		foreach ($prodResult as $row)
		{
			$issue_qty = $prodData_array[$row[csf('id')]];
			$product_amount = $prodData_amount_array[$row[csf('id')]];
			$current_stock = $row[csf('current_stock')] + $issue_qty;
			$stockValue = $row[csf('stock_value')] + $product_amount;

			if($current_stock > 0)
			{
			   //$avg_rate=$stock_value/$current_stock;
				$avg_rate_per_unit = number_format($stockValue / $current_stock, $dec_place[3], '.', '');
			}
			else
			{
				$avg_rate_per_unit=0;
			}
			// if Qty is zero then rate & value will be zero
			if ($current_stock<=0)
			{
				$stockValue=0;
				$avg_rate_per_unit=0;
			}

			if(is_nan($avg_rate_per_unit)) $avg_rate=0;
			$prod_id_array[] = $row[csf('id')];
			$data_array_prod_update[$row[csf('id')]] = explode("*", ($issue_qty . "*'" . $current_stock . "'*'" . $avg_rate_per_unit . "'*'" . $stockValue . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
		}

		/*
		|--------------------------------------------------------------------------
		| inv_receive_master
		| data inserting here
		|
		|--------------------------------------------------------------------------
		|
		*/
		$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order,store_id, location_id, knitting_source, knitting_company, knitting_location_id, yarn_issue_challan_no, boe_mushak_challan_no, boe_mushak_challan_date,remarks, fabric_nature, no_bill, inserted_by, insert_date";
		$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
		if ($rID) $flag = 1; else $flag = 0;

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating here
		|
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_prod_update = "current_stock";
		$rID2 = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array), 1);
		if ($flag == 1)
		{
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting here
		|
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self, bin_box, booking_without_order, inserted_by, insert_date";
		//echo "10**insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
		$rID3 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		if ($flag == 1)
		{
			if ($rID3)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_grey_prod_entry_dtls
		| data inserting here
		|
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls = "id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length,kniting_charge, yarn_rate, inserted_by, insert_date";
		//echo "10**insert into pro_grey_prod_entry_dtls ($field_array_dtls) values $data_array_dtls";die;
		$rID4 = sql_insert("pro_grey_prod_entry_dtls", $field_array_dtls, $data_array_dtls, 0);
		if ($flag == 1)
		{
			if ($rID4)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		|
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll = "id,mst_id,dtls_id,po_breakdown_id, entry_form,qc_pass_qnty,qnty,roll_id,roll_no,barcode_no,rate,amount,booking_no,booking_without_order,inserted_by,insert_date,is_sales,qc_pass_qnty_pcs,is_service, fso_delivery_type";
		$data_array_roll = rtrim($data_array_roll, ",");
		// echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
		$rID5 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		if ($flag == 1)
		{
			if ($rID5)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting here
		|
		|--------------------------------------------------------------------------
		|
		*/
		if ($data_array_prop != "")
		{
			$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date, is_sales, quantity_pcs";
			$rID6 = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
			if ($flag == 1)
			{
				if ($rID6)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		//implode(",", $barcode_id)
		/*echo "10**"."update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $barcode_id).") and id  not in (".implode(',', $inserted_roll_id_arr).")";
		oci_rollback($con);
		die;*/

		$rID7=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $barcode_id).") and id  not in (".implode(',', $inserted_roll_id_arr).")");
		if ($flag == 1)
		{
			if ($rID7)
				$flag = 1;
			else
				$flag = 0;
		}

		//echo "10**insert into pro_roll_details ($field_array_roll) values".rtrim($data_array_roll,",");check_table_status( $_SESSION['menu_id'],0);die;
		// echo "10**$rID**$rID2**$rID3**$rID4**$rID5**$rID6**$rID7";oci_rollback($con);die;
		//echo "10**".$flag;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $grey_update_id . "**" . $grey_recv_num . "**0" . "**" . str_replace("'", "", $txt_challan_no);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**" . "&nbsp;" . "**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $grey_update_id . "**" . $grey_recv_num . "**0" . "**" . str_replace("'", "", $txt_challan_no);
			} else {
				oci_rollback($con);
				echo "5**0**" . "&nbsp;" . "**0";
			}
		}

		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}
	else if ($operation == 1)   // Update Here
	{
		/*
		|--------------------------------------------------------------------------
		| for floor, room, rack and shelf
		| start
		|--------------------------------------------------------------------------
		|
		*/
		$barcodeString=str_replace('*',',',$barcode_id);
		$transactionRelatedBarcode_arr=array();
		//$sql_zs=sql_select("SELECT barcode_no FROM pro_roll_details WHERE barcode_no IN(".$barcodeString.") AND entry_form IN(61,82, 83,110,133,180,183) AND is_returned != 1 AND status_active = 1");

		$sql_zs=sql_select("SELECT barcode_no FROM pro_roll_details WHERE barcode_no IN(".$barcodeString.") AND entry_form IN(61,82, 83,110,133,180,183,84) AND status_active = 1 and is_deleted=0");
		foreach($sql_zs as $row)
		{
			$transactionRelatedBarcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
		unset($sql_zs);

		//pro_roll_details
		$sql_zs=sql_select("SELECT barcode_no FROM pro_roll_details WHERE barcode_no IN(".$barcodeString.") AND entry_form = 58 AND status_active = 1 AND roll_split_from<>0");
		foreach($sql_zs as $row)
		{
			$transactionRelatedBarcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
		unset($sql_zs);

		//pro_roll_split
		$sql_zs=sql_select("SELECT barcode_no FROM pro_roll_split WHERE barcode_no IN(".$barcodeString.") AND entry_form = 113 AND status_active = 1");
		foreach($sql_zs as $row)
		{
			$transactionRelatedBarcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
		unset($sql_zs);

		$challan_check = sql_select("select b.barcode_num, a.sys_number from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.id = b.mst_id and b.barcode_num in(" . $barcodeString . ") and a.entry_form in (56) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.barcode_num, a.sys_number");

		foreach ($challan_check as $val)
		{
			if(str_replace("'", "", $txt_challan_no) != $val[csf("sys_number")])
			{
				$wrongChallanRoll .= $val[csf("barcode_num")].",";
			}
		}
		$wrongChallanRoll = chop($wrongChallanRoll,",");
		if ($wrongChallanRoll !="")
		{
			echo "11**Duplicate challan barcode not allow. Below barcodes are not is this challan no.\nBarcode No : ".$wrongChallanRoll;disconnect($con);
			die;
		}
		unset($challan_check);
		//echo "10**<pre>";
		//print_r($transactionRelatedBarcode_arr); die;
		/*
		|--------------------------------------------------------------------------
		| for floor, room, rack and shelf
		| end
		|--------------------------------------------------------------------------
		|
		*/

		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}
		/*if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**1";
			die;
		}*/

		//bill check start
		$sql_knitting_bill = sql_select("select a.bill_no from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id= b.mst_id and a.process_id =2 and b.status_active =1 and b.delivery_id = '$update_id'
			union all
			select a.bill_no from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id = b.mst_id and a.process_id =2 and b.status_active =1 and b.receive_id = '$update_id'");
		if($sql_knitting_bill[0][csf("bill_no")] != "")
		{
			echo "11**Knitting Bill Found. Update Not Allow.\nBill no: ".$sql_knitting_bill[0][csf("bill_no")];disconnect($con);
			die;
		}
		//bill check end

		// If FSO > Sales Order Type = Service and Main Process = only knitting.
		// is_service=1 flag use only for Roll Wise Grey Fabric Delivery Page.
		$fso_sql="SELECT a.id, c.po_breakdown_id, b.body_part_id, b.determination_id, b.color_id, c.barcode_no
		FROM fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and a.id=c.po_breakdown_id and a.id=c.po_breakdown_id  and c.entry_form=2 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (".$barcodeString.") and a.sales_order_type=2 and b.process_id_main='1'";
		$result=sql_select($fso_sql);
		$is_service_arr=array();
		foreach ($result as $row)
		{
			$is_service_arr[$row[csf("barcode_no")]][$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("color_id")]]=1;
		}
		// echo "<pre>";print_r($is_service_arr);die;

		if ($db_type == 0)
		{
			$txt_receive_date = change_date_format($txt_receive_date, "yyyy-mm-dd");
			$txt_boe_mushak_challan_date = change_date_format($txt_boe_mushak_challan_date, "yyyy-mm-dd");
		}
		else
		{
			$txt_receive_date = change_date_format($txt_receive_date, 'yyyy-mm-dd', "-", 1);
			$txt_boe_mushak_challan_date = change_date_format($txt_boe_mushak_challan_date, 'yyyy-mm-dd', "-", 1);
		}

		$garments_nature = 2;
		$category_id = 13;
		$entry_form = 58;

		/*#### Stop not eligible field from update operation start ####*/
		// location_id*knitting_source*
		// $cbo_location_name . "*" . $cbo_knitting_source . "*" .
		/*#### Stop not eligible field from update operation end ####*/

		$field_array_update = "receive_date*challan_no*store_id*knitting_company*knitting_location_id*boe_mushak_challan_no*boe_mushak_challan_date*remarks*updated_by*update_date*location_id*booking_id*booking_no*no_bill";
		$data_array_update = "'" . $txt_receive_date . "'*'" . $txt_receive_chal_no . "'*" . $cbo_store_name . "*" . $cbo_knitting_company . "*'" .$cbo_knitting_location_id. "'*'" . $txt_boe_mushak_challan_no . "'*'". $txt_boe_mushak_challan_date . "'*'". $txt_remarks . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*".$cbo_location_name."*".$hidden_delivery_id."*'".$txt_challan_no."'*".$txt_no_bill;

		$check_data = explode("*", $check_data);
		$receive_number = explode("*", $receive_number);
		$receive_basis = explode("*", $receive_basis);
		$barcode_id = explode("*", $barcode_id);
		//echo "6**";print_r($barcode_id);die;
		$tran_id = explode("*", $tran_id);

		$gray_dtlsid = explode("*", $gray_dtlsid);
		$roll_id = explode("*", $roll_id);
		$receive_id = explode("*", $receive_id);
		$program_id = explode("*", $program_id);
		$prod_id = explode("*", $prod_id);
		$floor_id = explode("*", $floor_id);
		$room_no = explode("*", $room_no);
		$rack = explode("*", $rack);
		$self = explode("*", $self);
		$bin = explode("*", $bin);
		$txt_receive_qnty = explode("*", $issue_qty);
		$roll_no = explode("*", $roll_no);
		$knitting_source = explode("*", $knitting_source);
		$receive_date = explode("*", $receive_date);
		$buyer_id = explode("*", $buyer_id);
		$po_id = explode("*", $po_id);
		$dia = explode("*", $dia);
		$determination_id = explode("*", $determination_id);
		$body_part = explode("*", $body_part);
		$color_id = explode("*", $color_id);
		$color_range = explode("*", $color_range);
		$uom = explode("*", $uom);
		$gsm = explode("*", $gsm);
		$yean_cont = explode("*", $yean_cont);
		$band_id = explode("*", $band_id);
		$shift_id = explode("*", $shift_id);
		$yean_lot = explode("*", $yean_lot);
		$machine_name = explode("*", $machine_name);
		$hidden_qty = explode("*", $hidden_qty);
		$stitch_length = explode("*", $stitch_length);
		$roll_rate = explode("*", $roll_rate);
		$knitting_charge = explode("*", $knitting_charge);
		$yarn_rate = explode("*", $yarn_rate);
		$hidden_withoutOrder = explode("*", $hidden_withoutOrder);
		$hidden_booking = explode("*", $hidden_booking);
		$isSales = explode("*", $isSales);
		$hidden_qnty_in_pcs = explode("*", $hidden_qnty_in_pcs);
		$fsoDeliveryType = explode("*", $fsoDeliveryType);

		/*$field_array_prod_update = "current_stock";
		$field_array_trans_update = "transaction_date*store_id*cons_quantity*room*rack*self*bin_box*updated_by*update_date";
		$field_array_dtls_update = "grey_receive_qnty*stitch_length*room*rack*self*bin_box*updated_by*update_date";
		$field_array_roll_update = "qc_pass_qnty* updated_by* update_date";
		$field_array_propo_update = "quantity*updated_by*update_date";*/

		$field_array_prod_update = "current_stock";
		$field_array_trans_update = "transaction_date*store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";
		$field_array_dtls_update = "stitch_length*floor_id*room*rack*self*bin_box*updated_by*update_date";
		$field_array_roll_update = "qc_pass_qnty*qc_pass_qnty_pcs*updated_by*update_date";
		$field_array_propo_update = "updated_by*update_date";

		$field_array_trans_remove = "updated_by*update_date*status_active*is_deleted";
		$field_array_dtls_remove = "updated_by*update_date*status_active*is_deleted";
		$field_array_roll_remove = "updated_by* update_date*status_active*is_deleted";
		$field_array_propor_remove = "updated_by*update_date*status_active*is_deleted";
		$cur_st_qnty = 0;

		//********************************insert field *********************************
		$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self,bin_box,  booking_without_order, inserted_by, insert_date";

		$field_array_prod_update = "current_stock";

		$field_array_dtls = "id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id,room, rack, self,bin_box, color_id, color_range_id, stitch_length,kniting_charge, yarn_rate, inserted_by, insert_date";

		$field_array_roll = "id,mst_id,dtls_id,po_breakdown_id,entry_form,qc_pass_qnty, qnty,roll_id,roll_no,barcode_no, rate,amount,booking_no, booking_without_order, inserted_by, insert_date,is_sales,qc_pass_qnty_pcs,is_service, fso_delivery_type";

		$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date, is_sales, quantity_pcs";

		$update_roll_id = array();
		$update_array_roll = array();
		$update_array_dtls = array();
		$prodData_amount_array = array();
		$i = 0;
		foreach ($barcode_id as $row)
		{
			$cons_rate = $roll_rate[$i];
			if ($tran_id[$i] != "" && $tran_id[$i] != 0)
			{
				if ($check_data[$i] == 1)
				{
					if(empty($transactionRelatedBarcode_arr[$barcode_id[$i]]))
					{
						$cons_amount = $cons_rate * $txt_receive_qnty[$i];
						$update_roll_id[] = $roll_id[$i];
						//$update_array_roll[$roll_id[$i]] = explode("*", ("" . $txt_receive_qnty[$i] . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
						$update_array_roll[$roll_id[$i]] = explode("*", ("" . $txt_receive_qnty[$i] . "*" . $hidden_qnty_in_pcs[$i] . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

						$prodData_array[$prod_id[$i]] += $txt_receive_qnty[$i] - $hidden_qty[$i];
						$prodData_amount_array[$prod_id[$i]] += ($txt_receive_qnty[$i] - $hidden_qty[$i]) * $cons_rate;
						$update_data_arr[$prod_id[$i]] = explode("*", ("" . $cur_st_qnty . ""));
						$update_trans_id[] = $tran_id[$i];

						//
						$update_trans_arr[$tran_id[$i]] = explode("*", ("'" . $txt_receive_date . "'*'" . $cbo_store_name  . "'*'" . $floor_id[$i]  . "'*'" . $room_no[$i] . "'*'" . $rack[$i] . "'*'" . $self[$i] . "'*'" . $bin[$i] . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

						$update_detl_id[] = $gray_dtlsid[$i];
						$update_array_dtls[$gray_dtlsid[$i]] = explode("*", ("'" . $stitch_length[$i]  . "'*'" . $floor_id[$i] . "'*'" . $room_no[$i] . "'*'" . $rack[$i] . "'*'" . $self[$i] . "'*'" . $bin[$i] . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

						$update_prop_id[] = $tran_id[$i];
						$update_array_prop[$tran_id[$i]] = explode("*", ("" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
						$all_prod_id .= $prod_id[$i] . ",";

						$all_check_barcode_id .= $barcode_id[$i].",";
					}
				}
				else if ($check_data[$i] == 0)
				{

					$cons_amount = $cons_rate * $hidden_qty[$i];
					$remove_roll_id[] = $roll_id[$i];
					$remove_array_roll[$roll_id[$i]] = explode("*", ("" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1"));
					$deleted_barcode.=$barcode_id[$i].",";

					$prodData_array[$prod_id[$i]] -= $hidden_qty[$i];
					$prodData_amount_array[$prod_id[$i]] -= ($hidden_qty[$i]) * $cons_rate;
					$update_data_arr[$prod_id[$i]] = explode("*", ("" . $cur_st_qnty . ""));
					$remove_trans_id[] = $tran_id[$i];
					$remove_trans_arr[$tran_id[$i]] = explode("*", ("" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1"));
					$remove_detl_id[] = $gray_dtlsid[$i];
					$remove_array_dtls[$gray_dtlsid[$i]] = explode("*", ("" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1"));
					$remove_prop_id[] = $tran_id[$i];
					$remove_array_prop[$tran_id[$i]] = explode("*", ("" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1"));
					$all_prod_id .= $prod_id[$i] . ",";
				}
			}
			else
			{
				if ($check_data[$i] == 1)
				{
					if (!empty($is_service_arr))
					{
						$is_service=$is_service_arr[$barcode_id[$i]][$po_id[$i]][$body_part[$i]][$determination_id[$i]][$color_id[$i]];
					}
					else{
						$is_service=0;
					}

					$cons_amount = $cons_rate * $txt_receive_qnty[$i];
					$prodData_array[$prod_id[$i]] += $txt_receive_qnty[$i];
					$prodData_amount_array[$prod_id[$i]] += $cons_amount;
					if ($data_array_roll != "") $data_array_roll .= ",";
					if ($data_array_trans != "") $data_array_trans .= ",";
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					if ($data_array_prop != "") $data_array_prop .= ",";

					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

					$data_array_trans .= "(" . $id_trans . "," . $update_id . ",10,'" . $id_dtls . "'," . $cbo_company_id . "," . $prod_id[$i] . "," . $category_id . ",1,'" . $txt_receive_date . "'," . $cbo_store_name . ",'" . $band_id[$i] . "','" . $uom[$i] . "'," . $txt_receive_qnty[$i] . ",'" . $order_rate . "','" . $order_amount . "','" . $uom[$i] . "','" . $txt_receive_qnty[$i] . "','" . $txt_reject_fabric_recv_qnty . "','" . $cons_rate . "','" . $cons_amount . "','" . $txt_receive_qnty[$i] . "','" . $cons_amount . "','" . $floor_id[$i] . "','" . $machine_name[$i] . "','" . $room_no[$i] . "','" . $rack[$i] . "','" . $self[$i] . "','" . $bin[$i] . "','" . $hidden_withoutOrder[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$data_array_dtls .= "(" . $id_dtls . "," . $update_id . "," . $id_trans . "," . $prod_id[$i] . ",'" . $body_part[$i] . "','" . $determination_id[$i] . "','" . $gsm[$i] . "','" . $dia[$i] . "','" . $roll_no[$i] . "','" . $po_id[$i] . "'," . $txt_receive_qnty[$i] . ",'" . $txt_reject_fabric_recv_qnty . "','" . $cons_rate . "','" . $cons_amount . "','" . $cbo_uom . "','" . $yean_lot[$i] . "','" . $yean_cont[$i] . "','" . $band_id[$i] . "','" . $shift_id[$i] . "','" . $floor_id[$i] . "','" . $machine_name[$i] . "','" . $room_no[$i] . "','" . $rack[$i] . "','" . $self[$i] . "','" . $bin[$i] . "','" . $color_id[$i] . "','" . $color_range[$i] . "','" . $stitch_length[$i] . "','" . $knitting_charge[$i] . "', '" . $yarn_rate[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					if ($po_id[$i] == "") $orderID = 0; else $orderID = $po_id[$i];
					$data_array_roll .= "(" . $id_roll . "," . $update_id . "," . $id_dtls . ",'" . $po_id[$i] . "',$entry_form,'" . $txt_receive_qnty[$i] . "','" . $hidden_qty[$i] . "','" . $roll_id[$i] . "','" . $roll_no[$i] . "','" . $barcode_id[$i] . "','" . $cons_rate . "','" . $cons_amount . "','" . $hidden_booking[$i] . "','" . $hidden_withoutOrder[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$isSales[$i].",".$hidden_qnty_in_pcs[$i].",".$is_service.",'" . $fsoDeliveryType[$i] . "')";

					if ($hidden_withoutOrder[$i] != 1) {
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						$data_array_prop .= "(" . $id_prop . "," . $id_trans . ",1,$entry_form," . $id_dtls . ",'" . $po_id[$i] . "'," . $prod_id[$i] . ",'" . $txt_receive_qnty[$i] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$isSales[$i].",".$hidden_qnty_in_pcs[$i].")";
						//$id_prop = $id_prop + 1;
					}

					$all_prod_id .= $prod_id[$i] . ",";
					$all_check_barcode_id .= $barcode_id[$i].",";

					$inserted_roll_id_arr[$id_roll] = $id_roll;
					$new_inserted[$barcode_id[$i]] = $barcode_id[$i];

					//$id_roll = $id_roll + 1;
					//$id_dtls++;
					//$id_trans++;
				}
			}
			$i++;
		}

		//echo "10**<pre>"; die;
		//print_r($update_array_roll); die;

		$deleted_barcode=chop($deleted_barcode,",");
		$all_check_barcode_id = chop($all_check_barcode_id,",");
		//echo "6**".$all_check_barcode_id;die;
		if($deleted_barcode!="")
		{
			$further_process_check=sql_select("select entry_form, barcode_no from pro_roll_details where barcode_no in($deleted_barcode) and entry_form in (80,82,110,183,61,84) and status_active=1");
			if(count($further_process_check)>0)
			{
				if($further_process_check[0][csf("entry_form")] ==61)
				{
					$msg = "Issue";
				}
				else if($further_process_check[0][csf("entry_form")] ==84)
				{
					$msg = "Issue Return";
				}
				else
				{
					$msg = "Transfer";
				}
				echo "11**$msg Found. Update Not Allow.";disconnect($con);
				die;
			}
			unset($further_process_check);

			$split_check=sql_select("select barcode_no from pro_roll_details where  barcode_no in($deleted_barcode) and entry_form=58 and status_active=1 and roll_split_from<>0");

			if(count($split_check)>0)
			{
				echo "11**Splited Roll Found. Update Not Allow.";disconnect($con);
				die;
			}

			$mother_split_check=sql_select("select barcode_no from pro_roll_split where barcode_no in($deleted_barcode) and entry_form=113 and status_active=1 ");

			if(count($mother_split_check)>0)
			{
				echo "11**Splited Roll Found. Update Not Allow.";disconnect($con);
				die;
			}
		}

		if($all_check_barcode_id!="")
		{
			$receive_check = sql_select("select barcode_no from pro_roll_details where barcode_no in($all_check_barcode_id) and entry_form=58 and mst_id != $update_id and status_active=1 and is_deleted=0");

			foreach ($receive_check as $row) {
				$receive_barcode .= $row[csf('barcode_no')].",";
			}

			$receive_barcode = chop($receive_barcode,",");
			if($receive_barcode!="")
			{
				echo "11**All ready receive. Barcode No =  $receive_barcode";disconnect($con);
				die;
			}
		}
		//echo "10**fail";die;
		//echo "10**insert into pro_grey_prod_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//print_r($prodData_array);die;

		$prod_id_array = array();
		$all_prod_id = implode(",", array_unique(explode(",", substr($all_prod_id, 0, -1))));
		//$update_trans_id = implode(",", array_unique(explode(",", chop($update_trans_id,","))));

		$update_trans_ids = implode(",", array_unique($update_trans_id));
		if($update_trans_ids!="")
		{
			$trans_update_cond = " and id not in ($update_trans_ids) ";
		}
		else
		{
			$trans_update_cond = "";
		}
		//------------------Check Receive Date with last Transdate Date-------------------
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($all_prod_id) and store_id = $cbo_store_name $trans_update_cond and status_active = 1 and is_deleted = 0", "max_date");
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_issue_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}

		//-----------------------------------------------------------------------------
		$field_array_prod_update ="last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$prodResult = sql_select("select id, current_stock,avg_rate_per_unit,stock_value from product_details_master where id in($all_prod_id)");

		foreach ($prodResult as $row)
		{
			$issue_qty = $prodData_array[$row[csf('id')]];

			$product_amount = $prodData_amount_array[$row[csf('id')]];
			$current_stock = $row[csf('current_stock')] + $issue_qty;
			$stockValue = $row[csf('stock_value')] + $product_amount;

			if($stockValue!=0 && $current_stock!=0)
			{
				$avg_rate_per_unit = number_format($stockValue / $current_stock, $dec_place[3], '.', '');
			}
			// if Qty is zero then rate & value will be zero
			if ($current_stock<=0)
			{
				$stockValue=0;
				$avg_rate_per_unit=0;
			}

			$prod_id_array[] = $row[csf('id')];
			$data_array_prod_update[$row[csf('id')]] = explode("*", ($issue_qty . "*'" . $current_stock . "'*'" . $avg_rate_per_unit . "'*'" . $stockValue . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

		}

		// ======================================== Previous challan discurd =============================//
		if($hidden_delevery_scan!=$txt_challan_no)
		{
			//echo "10**==".$update_id;die;
			$sys_barcode = sql_select("select barcode_no,dtls_id from pro_roll_details where  mst_id = $update_id and entry_form=58 and status_active=1 and is_deleted=0");
			if(empty($sys_barcode))
			{
				echo "10**details not found";disconnect($con);
				die;
			}
			foreach ($sys_barcode as $val)
			{
				$sys_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
				$sys_dtls_arr[$val[csf("dtls_id")]] = $val[csf("dtls_id")];
			}

			$sys_barcode_arr = array_filter($sys_barcode_arr);
			if(count($sys_barcode_arr)>0)
			{
				$all_sys_barcodes = implode(",", $sys_barcode_arr);
				$all_sys_barcode_cond=""; $barCond="";
				if($db_type==2 && count($sys_barcode_arr)>999)
				{
					$sys_barcode_arr_chunk=array_chunk($sys_barcode_arr,999) ;
					foreach($sys_barcode_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$barCond.="  barcode_no in($chunk_arr_value) or ";
					}

					$all_sys_barcode_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$all_sys_barcode_cond=" and barcode_no in($all_sys_barcodes)";
				}

				//$issue_check=sql_select("select barcode_no from pro_roll_details where entry_form=61 $all_sys_barcode_cond and is_returned!=1  and status_active=1");

				$further_process_check=sql_select("select entry_form, barcode_no from pro_roll_details where entry_form in (83,82,110,183,133,61,84) $all_sys_barcode_cond and status_active=1");
				if(count($further_process_check)>0)
				{
					if($further_process_check[0][csf("entry_form")] ==61)
					{
						$msg = "Issue";
					}
					else if($further_process_check[0][csf("entry_form")] ==84)
					{
						$msg = "Issue Return";
					}
					else
					{
						$msg = "Transfer";
					}
					echo "11**$msg Found. Update Not Allow.";disconnect($con);
					die;
				}

				/*$transfer_check=sql_select("select barcode_no from pro_roll_details where entry_form in(83,82,110,183,133) $all_sys_barcode_cond and status_active=1");
				if(count($transfer_check)>0)
				{
					echo "11**Transfer Found. Update Not Allow.";
					die;
				}*/

				$split_check=sql_select("select barcode_no from pro_roll_details where entry_form=58 $all_sys_barcode_cond and status_active=1 and roll_split_from<>0");

				if(count($split_check)>0)
				{
					echo "11**Splited Roll Found. Update Not Allow.";disconnect($con);
					die;
				}

				$mother_split_check=sql_select("select barcode_no from pro_roll_split where entry_form=113 $all_sys_barcode_cond and status_active=1 ");

				if(count($mother_split_check)>0)
				{
					echo "11**Splited Roll Found. Update Not Allow.";disconnect($con);
					die;
				}

				$sys_dtls_arr = array_filter($sys_dtls_arr);
				$all_sys_dtls_ids = implode(",", $sys_dtls_arr);
				$all_sys_dtls_id_cond=""; $dtlsCond="";
				$all_sys_dtls_id_cond2=""; $dtlsCond2="";
				if($db_type==2 && count($sys_dtls_arr)>999)
				{
					$sys_dtls_arr_chunk=array_chunk($sys_dtls_arr,999) ;
					foreach($sys_dtls_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$dtlsCond.="  id in($chunk_arr_value) or ";
						$dtlsCond2.="  dtls_id in($chunk_arr_value) or ";
					}

					$all_sys_dtls_id_cond.=" and (".chop($dtlsCond,'or ').")";
					$all_sys_dtls_id_cond2.=" and (".chop($dtlsCond2,'or ').")";
				}
				else
				{
					$all_sys_dtls_id_cond=" and id in($all_sys_dtls_ids)";
					$all_sys_dtls_id_cond2=" and dtls_id in($all_sys_dtls_ids)";
				}

				$sql_prevqty = sql_select("select prod_id,sum(grey_receive_qnty) as prev_qty from pro_grey_prod_entry_dtls where mst_id =$update_id  $all_sys_dtls_id_cond and status_active=1 and is_deleted=0 group by prod_id");

				$previousProdData = array();
				foreach ($sql_prevqty as $row) {
					$previousProdData[$row[csf('prod_id')]]['prev_qty'] = $row[csf('prev_qty')];
					$sys_prod_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
				}

				$sys_prod_ids = implode(",", $sys_prod_id_arr);
				$field_array_previous_prod_update = "current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
				$prodResult = sql_select("select id, current_stock,avg_rate_per_unit,stock_value from product_details_master where id in($sys_prod_ids)");

				foreach ($prodResult as $row)
				{

					$currentStock = $row[csf('current_stock')]-$previousProdData[$row[csf('id')]]['prev_qty'];
					$currentStockValue = $currentStock*$row[csf('avg_rate_per_unit')];

					if($currentStockValue!=0 && $currentStock!=0)
					{
						$avg_rate_per_unit = number_format($currentStockValue / $currentStock, $dec_place[3], '.', '');
					}
					// if Qty is zero then rate & value will be zero
					if ($currentStock<=0)
					{
						$currentStockValue=0;
						$avg_rate_per_unit=0;
					}

					$previous_prod_id_array[] = $row[csf('id')];
					$previous_data_array_prod_update[$row[csf('id')]] = explode("*", ($currentStock . "*" .$currentStockValue. "*" .$avg_rate_per_unit. "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				}

				$previousRemove_roll=execute_query("update pro_roll_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where mst_id=$update_id and entry_form=58 ");
				if ($previousRemove_roll)
					$flag = 1;
				else
					$flag = 0;

				$previousremove_transection=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where mst_id=$update_id and transaction_type= 1 and item_category = 13 and status_active =1");
				if($previousremove_transection)
					$flag = 1;
				else
					$flag = 0;

				$previousremove_prod_entry_dtls=execute_query("update pro_grey_prod_entry_dtls set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where mst_id=$update_id $all_sys_dtls_id_cond");
				if($previousremove_prod_entry_dtls)
					$flag = 1;
				else
					$flag = 0;

				$previousremove_order_wise_dtls=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where entry_form =58  $all_sys_dtls_id_cond2 ");
				if($previousremove_order_wise_dtls)
					$flag = 1;
				else
					$flag = 0;

				if (count($previous_data_array_prod_update) > 0)
				{
					$previous_update_product = execute_query(bulk_update_sql_statement(" product_details_master", "id", $field_array_previous_prod_update, $previous_data_array_prod_update, $previous_prod_id_array), 1);

					if ($previous_update_product)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		// ======================================== Previous challan discurd end =============================//

		$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "id", $update_id, 0);
		//echo "10**".$data_array_update;die;
		if ($rID)
				$flag = 1;
			else
				$flag = 10;

		if (count($data_array_prod_update) > 0)
		{
			$update_product = execute_query(bulk_update_sql_statement(" product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array), 1);
			if ($flag == 1)
			{
				if ($update_product)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($remove_trans_arr) > 0)
		{
			$remove_tran = execute_query(bulk_update_sql_statement(" inv_transaction", "id", $field_array_trans_remove, $remove_trans_arr, $remove_trans_id), 1);
			if ($flag == 1)
			{
				if ($remove_tran)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($remove_array_dtls) > 0)
		{
			$remove_grey = execute_query(bulk_update_sql_statement("pro_grey_prod_entry_dtls", "id", $field_array_dtls_remove, $remove_array_dtls, $remove_detl_id), 1);
			if ($flag == 1)
			{
				if ($remove_grey)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		/*if (count($remove_array_roll) > 0)
		{
			$remove_roll = execute_query(bulk_update_sql_statement(" pro_roll_details", "id", $field_array_roll_remove, $remove_array_roll, $remove_roll_id), 1);
			if ($flag == 1)
			{
				if ($remove_roll)
					$flag = 1;
				else
					$flag = 0;
			}
		}*/

		if($deleted_barcode!="")
		{
			$remove_roll=execute_query("update pro_roll_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where barcode_no in($deleted_barcode) and entry_form=58");
			if ($flag == 1)
			{
				if ($remove_roll)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($remove_array_prop) > 0)
		{
			$remove_order = execute_query(bulk_update_sql_statement(" order_wise_pro_details", "trans_id", $field_array_propor_remove, $remove_array_prop, $remove_prop_id), 1);
			if ($flag == 1)
			{
				if ($remove_order)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		//***************************************************************************************************************************************

		if (count($update_array_roll) > 0)
		{
			$update_roll = execute_query(bulk_update_sql_statement("pro_roll_details", "id", $field_array_roll_update, $update_array_roll, $update_roll_id), 1);
			if ($flag == 1)
			{
				if ($update_roll)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($update_array_dtls) > 0)
		{
			// echo "10**".bulk_update_sql_statement("pro_grey_prod_entry_dtls", "id", $field_array_dtls_update, $update_array_dtls, $update_detl_id);die;
			$update_grey_prod = execute_query(bulk_update_sql_statement("pro_grey_prod_entry_dtls", "id", $field_array_dtls_update, $update_array_dtls, $update_detl_id), 1);
			if ($flag == 1)
			{
				if ($update_grey_prod)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($update_trans_arr) > 0)
		{
			// echo "10**".bulk_update_sql_statement(" inv_transaction", "id", $field_array_trans_update, $update_trans_arr, $update_trans_id);die;
			$update_trans = execute_query(bulk_update_sql_statement(" inv_transaction", "id", $field_array_trans_update, $update_trans_arr, $update_trans_id), 1);
			if ($flag == 1)
			{
				if ($update_trans)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($update_array_prop) > 0)
		{
			$update_order = execute_query(bulk_update_sql_statement(" order_wise_pro_details", "trans_id", $field_array_propo_update, $update_array_prop, $update_prop_id), 1);
			if ($flag == 1)
			{
				if ($update_order)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		//may be not use this block
		if (count($roll_data_array_update) > 0)
		{
			$rollUpdate = execute_query(bulk_update_sql_statement("pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr));
			if ($flag == 1)
			{
				if ($rollUpdate)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($data_array_trans != "")
		{
			$rID3 = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
			if ($flag == 1)
			{
				if ($rID3)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		if ($data_array_dtls != "")
		{
			$rID4 = sql_insert("pro_grey_prod_entry_dtls", $field_array_dtls, $data_array_dtls, 0);
			if ($flag == 1)
			{
				if ($rID4)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($data_array_roll != "")
		{
			// echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
			$rID5 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1)
			{
				if ($rID5)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($data_array_prop != "")
		{
			$rID6 = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
			if ($flag == 1)
			{
				if ($rID6)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if(!empty($new_inserted))
		{
			$rID7=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $new_inserted).") and id  not in (".implode(',', $inserted_roll_id_arr).")");
			if ($flag == 1)
			{
				if ($rID7)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		// echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
		//echo "10**".$flag."update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $new_inserted).") and id  not in (".implode(',', $inserted_roll_id_arr).")";
		// echo "10**failed"; oci_rollback($con); die;

		if ($db_type == 0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_recieved_id) . "**0" . "**" . str_replace("'", "", $txt_challan_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**1";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_recieved_id) . "**0" . "**" . str_replace("'", "", $txt_challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}
}

if ($action == "grey_fabric_receive_print-------------------------(old action)------------------------")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name, b.file_no, b.grouping as int_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['int_ref_no'] = $row[csf('int_ref_no')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	?>
	<div>
		<table width="100%" cellspacing="0" align="center" border="0"
		style="font-family: tahoma; font-size: 12px;">
		<tr>
			<td align="center" style="font-size:x-large">
				<strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Fabric Roll Receive</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><u>Receive
					Challan <? echo $txt_challan_no; ?></u></strong></td>
				</tr>
			</table>
			<br>
			<?
			$sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,store_id,location_id, yarn_issue_challan_no, remarks,supplier_id from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
			$chalan_no = $sql_data[0][csf('booking_no')];
			$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
				inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
				inner join inv_receive_master c on b.grey_sys_id = c.id
				where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
			$search_param = $mstData[0][csf('booking_no')];
			if ($mstData[0][csf('receive_basis')] == 2) {
				$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
					inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
					inner join wo_booking_mst c on b.booking_no = c.booking_no
					where a.id = $search_param");

				$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
				if ($is_salesOrder == "" || $is_salesOrder == 0) {
					$is_salesOrder = 0;
				} else {
					$is_salesOrder = 1;
				}
			} else if ($mstData[0][csf('receive_basis')] == 4) {
				$is_salesOrder = 1;
				$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
					inner join wo_booking_mst b on a.booking_id = b.id
					where a.job_no = '$search_param'");
			} else {
				$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
			}
			?>
			<table width="100%" cellspacing="0" align="center" border="0"
			style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="150">Delivery Challan</td>
				<td width="200" style="font-size:18px; font-weight:bold;">
					:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">Company</td>
					<td width="200" align=""><? echo $company_array[$company]['name']; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">Store Name</td>
					<td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="150">Challan No</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">Location</td>
					<td width="200" id="" align=""><? echo $location_arr[$sql_data[0][csf('location_id')]]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">Knitting Source</td>
					<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="150">Knitting Com</td>
					<td width="200">:&nbsp;
						<?
						if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
						else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
						?>
					</td>
					<td style="font-size:14px; font-weight:bold;" width="150">Yarn Issue Ch. No</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('yarn_issue_challan_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">Remarks</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px; font-weight:bold;">Recevied Date</td>
					<td width="">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
					<td width="" id="barcode_img_id" colspan="2"></td>
					<?php if ($data[4]) { ?>
					<td style="font-size:14px; font-weight:bold;" width="150">PO Company</td>
					<td width="200">
						:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
						<?php } ?>
					</tr>
				</table>
				<br>
				<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1310" class="rpt_table"
				style="font-family: tahoma; font-size: 12px;">
				<?php
				ini_set('memory_limit', '-1');
				$data_array = sql_select("SELECT a.id,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order
					FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
					and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) order by c.id desc");
				$roll_details_array = array();
				$barcode_array = array();
				foreach ($data_array as $row) {
					$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
					$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
					$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
					$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
					$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
				}

				$i = 1;
				$tot_qty = 0;
				$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
				$receive_basis_arr = array();
				$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
				foreach ($data_array_receive_basis as $row) {
					$receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
				}
				if ($sql_data[0][csf('knitting_source')] == 1) {
					$data_array_mst = sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,
						a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
						b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no
						FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
						WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
						c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by d.seq_no, c.id ");
				} else {
					$data_array_mst = sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date,
						a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
						b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,	b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
						c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by c.id ");
				}
				$order_data = array();
				$job_no_data = array();
				$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0");
				foreach ($booking_data as $row) {
					$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
					$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
				}

				$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
				foreach ($job_data as $row) {
					$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
				}
				$i = 1;
				foreach ($data_array_mst as $row) {
					$po_number = $row[csf('job_no')];
					$booking_no = $row[csf('sales_booking_no')];
					$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
						inner join wo_booking_dtls b on a.booking_no = b.booking_no
						where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");
					if ($loc_arr[$row[csf('location_id')]] == "") {
						$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
						$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
					}

					$knit_company = "&nbsp;";
					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}

					$count = '';
					$yarn_count = explode(",", $row[csf('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
					}



					if ($row[csf('receive_basis')] == 1) {
						$booking_no = explode("-", $row[csf('booking_no')]);
						$prog_book_no = (int)$booking_no[3];

					} else $prog_book_no = $row[csf('booking_no')];

					$sales_order_no = "";
					$r_basis = $roll_details_array[$row[csf("barcode_no")]]['receive_basis'];
					if ($r_basis == 2) {
						$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];
						$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$plan_id");
						if ($is_salesOrder == "" || $is_salesOrder == 0) {
							$is_salesOrder = 0;
						} else {
							$is_salesOrder = 1;

						}
					}

					if ($r_basis == 4) {
						$is_salesOrder = 1;
					}

					$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
					if ($without_order == 1) {

						$data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
							FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $roll_details_array[$row[csf("barcode_no")]]['booking_no'] . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
						$buyer = $data_none_array[0][csf("buyer_id")];
						$job_sys = $data_none_array[0][csf("booking_no")];
						$job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
					} else {
						if ($is_salesOrder == 1) {
							$data_array = sql_select("select a.mst_id, b.booking_no, c.job_no, c.po_break_down_id from ppl_planning_info_entry_dtls a
								inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
								inner join wo_booking_mst c on b.booking_no = c.booking_no
								where a.id= $plan_id");
							$booking_no = $data_array[0]['BOOKING_NO'];
							if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
							} else {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
							}

							$po_jobs = explode(",", $order_data[$booking_no]['job_no']);
							foreach ($po_jobs as $job) {
								$po_job .= $job_no_data[$job] . ",";
							}
							$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];

						// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO

							$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "SALES_BOOKING_NO='$booking_no'");
							$salesOrder_id = sql_select("SELECT ID,JOB_NO,JOB_NO_PREFIX_NUM, BUYER_ID  FROM FABRIC_SALES_ORDER_MST WHERE SALES_BOOKING_NO='$booking_no'");
							$po_id = $salesOrder_id[0]['ID'];
							$po_num = $salesOrder_id[0]['JOB_NO'];
							$job_buyer = "B:" . $buyer_array[$order_data[$booking_no]['buyer_id']] . "<BR />J:" . rtrim($po_job, ',');
						} else {
							$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
							$booking_no = $row[csf('book_no')];
							$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']] . "<br>" . $job_array[$row[csf('po_breakdown_id')]]['job'];
						}

						$file = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					}

					$color_names = '';
					$colorIds = array_unique(explode(",", $row[csf("color_id")]));
					foreach ($colorIds as $color_id) {
						$color_names .= $color_arr[$color_id] . ",";
					}
					$color_names = chop($color_names, ',');
					if ($i == 1) {
						?>
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order
									No
								</th>
								<th width="60">Buyer<br> Job</th>
								<th width="140">Booking No</th>
								<th width="140">System ID</th>
								<th width="100">Production Basis</th>
								<!--<th width="70">Knitting Source</th>-->
								<th width="70">Knitting Company</th>
								<th width="50">Yarn Count</th>
								<th width="70">Yarn Brand</th>
								<th width="60">Lot No</th>
								<th width="70">Fab Color</th>
								<th width="70">Color Range</th>
								<th width="150">Fabric Type</th>
								<th width="50">Stich</th>
								<th width="50">Fin GSM</th>
								<th width="40">Fab. Dia</th>
								<th width="40">Machine No</th>
								<th width="40">Roll No</th>
								<th>QC Pass Qty</th>
							</tr>
						</thead>
						<?php } ?>
						<tr>
							<td width="30"><? echo $i; ?></td>
							<td width="140"
						style="word-break:break-all;"><? echo $po_num;//;//$job_array[$row[csf('po_breakdown_id')]]['po'];
						?></td>
					<td width="60"><? echo $job_buyer;//$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job'];
					?></td>
					<td width="140" style="text-align: center;"><? echo $booking_no; ?></td>
					<td width="140" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
					<td width="100"><? echo $receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']]; ?></td>
					<td width="70"><? echo $knit_company; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $color_names; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="150"
					style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;"
					align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
					<td width="50" style="word-break:break-all;"
					align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
					<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
				</tr>
				<?
				$tot_qty += $row[csf('qnty')];
				$i++;
			}
			?>
			<tr>
				<td align="right" colspan="18"><strong>Total= </strong></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(16, $company, "1210px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$location = $data[4];
	$storeId = $data[5];

	// echo "<pre>";
	// print_r($data);

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
		else
		{
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div>
		<table width="100%" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
			<td align="center" style="font-size:x-large">
				<strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
				<strong><? echo $com_dtls[1]; ?></strong>
			</td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Fabric Roll Receive</u></strong></td>
			</tr>
			<tr>
            <td align="center" style="font-size:18px"><strong><u>Receive
                Challan <? echo $txt_challan_no; ?></u></strong></td>
            </tr>
		</table>
		<br>
		<?
        $sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,store_id,location_id, yarn_issue_challan_no, remarks,supplier_id from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
        $chalan_no = $sql_data[0][csf('booking_no')];
        $mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
            inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
            inner join inv_receive_master c on b.grey_sys_id = c.id
            where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
        $search_param = $mstData[0][csf('booking_no')];
        if ($mstData[0][csf('receive_basis')] == 2)
        {
            $booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
                inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
                inner join wo_booking_mst c on b.booking_no = c.booking_no
                where a.id = $search_param");

            $is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
            if ($is_salesOrder == "" || $is_salesOrder == 0)
            {
                $is_salesOrder = 0;
            }
            else
            {
                $is_salesOrder = 1;
            }
        }
        else if ($mstData[0][csf('receive_basis')] == 4)
        {
            $is_salesOrder = 1;
            $booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
                inner join wo_booking_mst b on a.booking_id = b.id
                where a.job_no = '$search_param'");
        }
        else
        {
            $booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
        }
        ?>
        <table width="100%" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="150">Delivery Challan</td>
                <td width="200" style="font-size:18px; font-weight:bold;">:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Company</td>
                <td width="200" align=""><? echo $company_array[$company]['name']; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Store Name</td>
                <td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="150">Challan No</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Location</td>
                <td width="200" id="" align=""><? echo $location_arr[$sql_data[0][csf('location_id')]]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Knitting Source</td>
                <td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="150">Knitting Com</td>
                <td width="200">:&nbsp;
                    <?
                    if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
                    else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
                    ?>
                </td>
                <td style="font-size:14px; font-weight:bold;" width="150">Yarn Issue Ch. No</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('yarn_issue_challan_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Remarks</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:16px; font-weight:bold;">Recevied Date</td>
                <td width="">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td width="" id="barcode_img_id" colspan="2"></td>
                <?php if ($data[4]) { ?>
                <td style="font-size:14px; font-weight:bold;" width="150">PO Company</td>
                <td width="200">
                    :&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
                    <?php } ?>
                </tr>
        </table>
        <br>
        <table cellspacing="0" cellpadding="3" border="1" rules="all" width="1710" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
            <?php



			/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf disable
			|--------------------------------------------------------------------------
			|
			*/
			$lib_floor_arr=array(); $lib_room_arr=array(); $lib_rack_arr=array(); $lib_shelf_arr=array();
			 $lib_bin_arr=array();
			$lib_room_rack_shelf_sql = "SELECT b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
			FROM lib_floor_room_rack_dtls b
			left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
			left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
			left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
			left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
			left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
			WHERE b.status_active=1 and b.is_deleted=0 and b.company_id=".$company." and b.store_id=".$storeId." and b.location_id=".$location."";
			//echo $lib_room_rack_shelf_sql;die;
			$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
			if(!empty($lib_rrsb_arr))
			{
				foreach ($lib_rrsb_arr as $room_rack_shelf_row)
				{
					//$companyId  = $room_rack_shelf_row[csf("company_id")];
					$floorId = $room_rack_shelf_row[csf("floor_id")];
					$roomId  = $room_rack_shelf_row[csf("room_id")];
					$rackId  = $room_rack_shelf_row[csf("rack_id")];
					$shelfId = $room_rack_shelf_row[csf("shelf_id")];
					$binId   = $room_rack_shelf_row[csf("bin_id")];

					if($floorId!="")
					{
						$lib_floor_arr[$floorId] = $room_rack_shelf_row[csf("floor_name")];
					}

					if($floorId!="" && $roomId!="")
					{
						$lib_room_arr[$roomId] = $room_rack_shelf_row[csf("room_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="")
					{
						$lib_rack_arr[$rackId] = $room_rack_shelf_row[csf("rack_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="")
					{
						$lib_shelf_arr[$shelfId] = $room_rack_shelf_row[csf("shelf_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="" && $binId!="")
					{
						$lib_bin_arr[$binId] = $room_rack_shelf_row[csf("bin_name")];
					}
				}
			}
			else
			{
				$lib_floor_arr[0]="";
				$lib_room_arr[0]="";
				$lib_rack_arr[0]="";
				$lib_shelf_arr[0]="";
				$lib_bin_arr[0]="";
			}
			//end

            $i = 1;
            $tot_qty = 0;
            $receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
            /*$receive_basis_arr = array();
            $data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
            foreach ($data_array_receive_basis as $row)
            {
                $receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
            }*/

            if ($sql_data[0][csf('knitting_source')] == 1)
            {
                $sql = "SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no,c.is_sales,c.booking_without_order
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
                WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0 order by d.seq_no, c.id ";
                    //echo $sql;
            }
            else
            {
                $sql = "SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date,
                a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty ,c.is_sales,c.booking_without_order
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
                WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0 order by c.id ";
                    //echo $sql;
            }

            //echo $sql;//die();
            $data_array_mst = sql_select($sql);
            $po_id_array = $sales_id_array = $booking_program_arr = array();
            foreach ($data_array_mst as $row)
            {

                if($row[csf("is_sales")] == 1)
                {
                    $sales_id_array[] = $row[csf("po_breakdown_id")];
                }
                else
                {
                    if($row[csf("booking_without_order")] ==0)
                    {
                        $po_id_array[] = $row[csf("po_breakdown_id")];
                    }
                }

                if ($row[csf('receive_basis')] == 2)
                {
                    $booking_program_arr[] = $row[csf("booking_no")];
                }
                else
                {
                    $booking_no = explode("-", $row[csf('booking_no')]);
                    $booking_program_arr[] = (int)$booking_no[3];
                }

                $booking_pro=$roll_details_array[$row[csf("barcode_no")]]['booking_no'];
                $ref_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            }



			$ref_barcode_no_cond = where_con_using_array($ref_barcode_arr,0,'c.barcode_no');
			ini_set('memory_limit', '-1');
            $data_array = sql_select("SELECT a.id,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 $ref_barcode_no_cond
                and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) order by c.id desc");
            $roll_details_array = array();
            $barcode_array = array();
            foreach ($data_array as $row)
            {
                $roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
                $roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
                $roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
                $roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
                $roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
            }
            // ===========================

            $planOrder = sql_select("select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
            $plan_arr = array();
            foreach ($planOrder as $plan_row)
            {
                $plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
            }

            $job_array = $sales_arr = $sales_booking_arr = $booking_arr = array();
            if(!empty($po_id_array))
            {
                $job_sql = "select a.job_no_prefix_num, a.job_no,a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
                $job_sql_result = sql_select($job_sql);
                foreach ($job_sql_result as $row)
                {
                    $job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
                    $job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
                    $job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                }
            }

            if(!empty($sales_id_array))
            {
                $sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id,insert_date from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
                foreach ($sales_details as $sales_row)
                {
                    $sales_arr[$sales_row[csf('id')]]['po_number'] 		= $sales_row[csf('job_no')];
                    $sales_arr[$sales_row[csf('id')]]['sales_booking_no']= $sales_row[csf('sales_booking_no')];
                    $sales_arr[$sales_row[csf('id')]]['within_group'] 	= $sales_row[csf('within_group')];
                    $sales_arr[$sales_row[csf('id')]]['year'] 			= date("Y", strtotime($sales_row[csf("insert_date")]));
                    $sales_arr[$sales_row[csf('id')]]['buyer_id'] 		= $sales_row[csf('buyer_id')];
                    $sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
                }
            }

            $booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",array_unique($po_id_array)).")";

            $booking_details = sql_select("SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

            foreach ($booking_details as $booking_row)
            {
                $booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
                $booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
                $booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
                $booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
                $booking_arr[$booking_row[csf("booking_no")]]["int_ref"] = $booking_row[csf("ref_no")];
                $booking_arr[$booking_row[csf("po_break_down_id")]]["grouping"] = $booking_row[csf("ref_no")];
            }

            $data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
                FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $booking_pro . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
            foreach ($data_none_array as $row)
            {
                $data_non_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf("buyer_id")];
                $data_non_arr[$row[csf('booking_no')]]['booking_no']=$row[csf("booking_no")];
            }

            $i = 1;
            foreach ($data_array_mst as $row)
            {
                $is_sales_id=$row[csf("is_sales")];
                $sales_booking=$sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
                $within_group=$sales_arr[$row[csf('po_breakdown_id')]]['within_group'];

                $without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
                if ($without_order == 1)
                {
                    $buyer = $data_non_arr[$row[csf('book_no')]]['buyer_id'];
                    $job_sys = $data_non_arr[$row[csf('book_no')]]['booking_no'];
                    $job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
                    $int_ref = "";
                }
                else
                {
                    if($is_sales_id==1)
                    {
                        if($within_group==1)
                        {
                            $job_no=explode("-",$booking_arr[$sales_booking]['job_no']);
                            $job_no_prefix=(int)$job_no[2];
                            $job_buyer = "<b>B: </b>".$buyer_array[$booking_arr[$sales_booking]['buyer_id']] . "<br>" ."<b>J: </b>".$job_no_prefix;
                            $int_ref = $booking_arr[$sales_booking]['int_ref'];
                        }
                        else
                        {
                            $job_buyer = "<b>B: </b>".$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']] . "<br>" ."<b>J:";
                            $int_ref = "";
                        }
                        $po_number=$sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
                        $booking_no = $sales_booking;

                    }
                    else
                    {
                        $po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
                        $job_buyer = "<b>B: </b>".$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>" ."<b>J: </b>".$job_array[$row[csf('po_breakdown_id')]]['job'];
                        $booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
                            //echo $booking_no."test";
                        $int_ref = $booking_arr[$row[csf('po_breakdown_id')]]["grouping"];
                    }
                }

                if ($row[csf('receive_basis')] == 1)
                {
                    $booking_no = explode("-", $row[csf('booking_no')]);
                    $prog_book_no = (int)$booking_no[3];
                }
                else
                {
                    $prog_book_no = $row[csf('booking_no')];
                }

                $knit_company = "&nbsp;";
                if ($row[csf("knitting_source")] == 1)
                {
                    $knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
                }
                else if ($row[csf("knitting_source")] == 3)
                {
                    $knit_company = $supplier_arr[$row[csf("knitting_company")]];
                }

                $count = '';
                $yarn_count = explode(",", $row[csf('yarn_count')]);
                foreach ($yarn_count as $count_id)
                {
                    if ($count == '')
                        $count = $yarn_count_details[$count_id];
                    else
                        $count .= "," . $yarn_count_details[$count_id];
                }

                $color_names = '';
                $colorIds = array_unique(explode(",", $row[csf("color_id")]));
                foreach ($colorIds as $color_id)
                {
                    $color_names .= $color_arr[$color_id] . ",";
                }
                $color_names = chop($color_names, ',');
                if ($i == 1)
                {
                    ?>
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order No</th>
                            <th width="100">Internal Ref.</th>
                            <th width="80">Buyer<br> Job</th>
                            <th width="140">Booking No</th>
                            <th width="140">System ID</th>
                            <th width="100">Production Basis</th>
                            <!--<th width="70">Knitting Source</th>-->
                            <th width="70">Knitting Company</th>
                            <th width="50">Yarn Count</th>
                            <th width="70">Yarn Brand</th>
                            <th width="60">Lot No</th>
                            <th width="70">Fab Color</th>
                            <th width="70">Color Range</th>
                            <th width="150">Fabric Type</th>
                            <th width="50">Stich</th>
                            <th width="50">Fin GSM</th>
                            <th width="40">Fab. Dia</th>
                            <th width="40">Machine No</th>
                            <th width="40">Roll No</th>
                            <th width="40">QC Pass Qty</th>
                            <th width="70">Floor</th>
                            <th width="70">Room</th>
                            <th width="70">Rack</th>
                            <th width="70">Shelf</th>
                            <th>Bin/Box</th>
                        </tr>
                    </thead>
                	<?php
                }
                ?>
                <tr>
                    <td width="30"><? echo $i; ?></td>
                    <td width="140" style="word-break:break-all;"><? echo $po_number;?></td>
                    <td width="100" style="word-break:break-all;"><? echo $int_ref;?></td>
                    <td width="80"><? echo $job_buyer; ?></td>
                    <td width="140" style="text-align: center;"><? echo $booking_no; ?></td>
                    <td width="140" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
                    <td width="100"><? echo $receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']]; ?></td>
                    <td width="70"><? echo $knit_company; ?></td>
                    <td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                    <td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $color_names; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                    <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                    <td width="50" style="word-break:break-all;"
                    align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
                    <td width="50" style="word-break:break-all;"
                    align="center"><? echo $row[csf('gsm')]; ?></td>
                    <td width="40" style="word-break:break-all;"
                    align="center"><? echo $row[csf('width')]; ?></td>
                    <td width="40" style="word-break:break-all;"
                    align="center"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
                    <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                    <td width="40" align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_floor_arr[$row[csf('floor_id')]]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_room_arr[$row[csf('room')]]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_rack_arr[$row[csf('rack')]]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_shelf_arr[$row[csf('self')]]; ?></td>
                    <td style="word-break:break-all;"><? echo $lib_bin_arr[$row[csf('bin_box')]]; ?></td>
                </tr>
                <?
                $tot_qty += $row[csf('qnty')];
                $i++;
            }
            ?>
            <tr>
                <td align="right" colspan="19"><strong>Total= </strong></td>
                <td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
    <? echo signature_table(16, $company, "1710px"); ?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
        var value = valuess;//$("#barcodeValue").val();
        //alert(value)
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer = 'bmp';// $("input[name=renderer]:checked").val();

        var settings = {
            output: renderer,
            bgColor: '#FFFFFF',
            color: '#000000',
            barWidth: 1,
            barHeight: 40,
            moduleSize: 5,
            posX: 10,
            posY: 20,
            addQuietZone: 1
        };
        //$("#barcode_img_id").html('11');
        value = {code: value, rect: false};

        $("#barcode_img_id").show().barcode(value, btype, settings);
    }
    generateBarcode('<? echo $txt_challan_no; ?>');
</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_printmg")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$location = $data[4];
	$storeId = $data[5];

	// echo "<pre>";
	// print_r($data);

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');


	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div>
		<table cellspacing="0"  border="0" width="1010" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
			<tr>
			<td align="center" style="font-size:x-large">
				<strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
				<strong><? echo $com_dtls[1]; ?></strong>
			</td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Fabric Roll Receive</u></strong></td>
			</tr>
			<tr>
            <td align="center" style="font-size:18px"><strong><u>Receive
                Challan <? echo $txt_challan_no; ?></u></strong></td>
            </tr>
		</table>
		<br>
		<?
        $sql_data = sql_select("SELECT recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,store_id,location_id, yarn_issue_challan_no, remarks,supplier_id from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
        $chalan_no = $sql_data[0][csf('booking_no')];
        $mstData = sql_select("SELECT a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
            inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
            inner join inv_receive_master c on b.grey_sys_id = c.id
            where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
        $search_param = $mstData[0][csf('booking_no')];
        if ($mstData[0][csf('receive_basis')] == 2)
        {
            $booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
                inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
                inner join wo_booking_mst c on b.booking_no = c.booking_no
                where a.id = $search_param");

            $is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
            if ($is_salesOrder == "" || $is_salesOrder == 0)
            {
                $is_salesOrder = 0;
            }
            else
            {
                $is_salesOrder = 1;
            }
        }
        else if ($mstData[0][csf('receive_basis')] == 4)
        {
            $is_salesOrder = 1;
            $booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
                inner join wo_booking_mst b on a.booking_id = b.id
                where a.job_no = '$search_param'");
        }
        else
        {
            $booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
        }

		if ($sql_data[0][csf('knitting_source')] == 1)
		{
			$sql = "SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no,c.is_sales,c.booking_without_order
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0 order by d.seq_no, c.id ";
				//echo $sql;
		}
		else
		{
			$sql = "SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date,
			a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty ,c.is_sales,c.booking_without_order
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0 order by c.id ";
				//echo $sql;
		}

		//echo $sql;//die();
		$data_array_mst = sql_select($sql);

		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

		$po_id_array = $sales_id_array = $booking_program_arr = $feb_des_chk = $feb_des_arr = array();
		foreach ($data_array_mst as $row)
		{

			if($row[csf("is_sales")] == 1)
			{
				$sales_id_array[] = $row[csf("po_breakdown_id")];
			}
			else
			{
				if($row[csf("booking_without_order")] ==0)
				{
					$po_id_array[] = $row[csf("po_breakdown_id")];
				}
			}

			if ($row[csf('receive_basis')] == 2)
			{
				$booking_program_arr[] = $row[csf("booking_no")];
			}
			else
			{
				$booking_no = explode("-", $row[csf('booking_no')]);
				$booking_program_arr[] = (int)$booking_no[3];
			}

			$booking_pro=$roll_details_array[$row[csf("barcode_no")]]['booking_no'];
			$ref_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];

			if($feb_des_chk[$row[csf('febric_description_id')]] == "")
			{
				$feb_des_chk[$row[csf('febric_description_id')]] = $row[csf('febric_description_id')];
				array_push($feb_des_arr,$row[csf('febric_description_id')]);
			}
		}

		$composition_arr = array();
		/* $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row)
		{
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
			else
			{
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		} */
		$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active =1 ".where_con_using_array($feb_des_arr,0,'a.id')."";
		//echo $sql_deter;
		$data_array = sql_select($sql_deter);
		foreach ($data_array as $row) {
			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
		}

		$ref_barcode_no_cond = where_con_using_array($ref_barcode_arr,0,'c.barcode_no');
		ini_set('memory_limit', '-1');
		$data_array = sql_select("SELECT a.id,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 $ref_barcode_no_cond
			and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) order by c.id desc");
		$roll_details_array = array();
		$barcode_array = array();
		foreach ($data_array as $row)
		{
			$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
		}
		// ===========================

		$planOrder = sql_select("select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
		$plan_arr = array();
		foreach ($planOrder as $plan_row)
		{
			$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
		}

		$job_array = $sales_arr = $sales_booking_arr = $booking_arr = array();
		if(!empty($po_id_array))
		{
			$job_sql = "select a.job_no_prefix_num, a.job_no,a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
			$job_sql_result = sql_select($job_sql);
			foreach ($job_sql_result as $row)
			{
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			}
		}

		if(!empty($sales_id_array))
		{
			$sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id,insert_date from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			foreach ($sales_details as $sales_row)
			{
				$sales_arr[$sales_row[csf('id')]]['po_number'] 		= $sales_row[csf('job_no')];
				$sales_arr[$sales_row[csf('id')]]['sales_booking_no']= $sales_row[csf('sales_booking_no')];
				$sales_arr[$sales_row[csf('id')]]['within_group'] 	= $sales_row[csf('within_group')];
				$sales_arr[$sales_row[csf('id')]]['year'] 			= date("Y", strtotime($sales_row[csf("insert_date")]));
				$sales_arr[$sales_row[csf('id')]]['buyer_id'] 		= $sales_row[csf('buyer_id')];
				$sales_arr[$sales_row[csf('id')]]['style_ref_no'] 	= $sales_row[csf('style_ref_no')];
				$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
			}
		}

		$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",array_unique($po_id_array)).")";

		$booking_details = sql_select("SELECT a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

		foreach ($booking_details as $booking_row)
		{
			$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
			$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			$booking_arr[$booking_row[csf("booking_no")]]["int_ref"] = $booking_row[csf("ref_no")];
			$booking_po_arr[$booking_row[csf("po_break_down_id")]]["grouping"] = $booking_row[csf("ref_no")];
			$booking_po_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
		}

		$data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
			FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $booking_pro . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
		foreach ($data_none_array as $row)
		{
			$data_non_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf("buyer_id")];
			$data_non_arr[$row[csf('booking_no')]]['booking_no']=$row[csf("booking_no")];
		}

		/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf disable
			|--------------------------------------------------------------------------
			|
			*/
			$lib_floor_arr=array(); $lib_room_arr=array(); $lib_rack_arr=array(); $lib_shelf_arr=array();
			 $lib_bin_arr=array();
			$lib_room_rack_shelf_sql = "SELECT b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
			FROM lib_floor_room_rack_dtls b
			left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
			left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
			left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
			left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
			left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
			WHERE b.status_active=1 and b.is_deleted=0 and b.company_id=".$company." and b.store_id=".$storeId." and b.location_id=".$location."";
			//echo $lib_room_rack_shelf_sql;die;
			$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
			if(!empty($lib_rrsb_arr))
			{
				foreach ($lib_rrsb_arr as $room_rack_shelf_row)
				{
					//$companyId  = $room_rack_shelf_row[csf("company_id")];
					$floorId = $room_rack_shelf_row[csf("floor_id")];
					$roomId  = $room_rack_shelf_row[csf("room_id")];
					$rackId  = $room_rack_shelf_row[csf("rack_id")];
					$shelfId = $room_rack_shelf_row[csf("shelf_id")];
					$binId   = $room_rack_shelf_row[csf("bin_id")];

					if($floorId!="")
					{
						$lib_floor_arr[$floorId] = $room_rack_shelf_row[csf("floor_name")];
					}

					if($floorId!="" && $roomId!="")
					{
						$lib_room_arr[$roomId] = $room_rack_shelf_row[csf("room_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="")
					{
						$lib_rack_arr[$rackId] = $room_rack_shelf_row[csf("rack_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="")
					{
						$lib_shelf_arr[$shelfId] = $room_rack_shelf_row[csf("shelf_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="" && $binId!="")
					{
						$lib_bin_arr[$binId] = $room_rack_shelf_row[csf("bin_name")];
					}
				}
			}
			else
			{
				$lib_floor_arr[0]="";
				$lib_room_arr[0]="";
				$lib_rack_arr[0]="";
				$lib_shelf_arr[0]="";
				$lib_bin_arr[0]="";
			}
			//end

			foreach ($data_array_mst as $row)
            {
                $is_sales_id=$row[csf("is_sales")];
                $sales_booking=$sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
                $within_group=$sales_arr[$row[csf('po_breakdown_id')]]['within_group'];

                $without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
                if ($without_order == 1)
                {
                    $buyer = $buyer_array[$buyer];
                    $int_ref = "";
                }
                else
                {
                    if($is_sales_id==1)
                    {
                        if($within_group==1)
                        {
                            $buyer = $buyer_array[$booking_arr[$sales_booking]['buyer_id']];
							$style_ref_no = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
                        }
                        else
                        {
                            $buyer = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']];
							$style_ref_no = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
                        }

                    }
                    else
                    {
                        $buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']];
                    }
                }
			}

        ?>
		<table cellspacing="0"  border="0" width="1110" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="100" >Delivery Challan</td>
                <td width="180" style="font-size:16px; font-weight:bold;">:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="100">Company</td>
                <td width="180" align=""><? echo $company_array[$company]['name']; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="100">Store Name</td>
                <td width="180">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="100">Challan No</td>
                <td width="180">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="100">Location</td>
                <td width="180" id="" align=""><? echo $location_arr[$sql_data[0][csf('location_id')]]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="100">Knitting Source</td>
                <td width="180">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="100">Knitting Com</td>
                <td width="180">:&nbsp;
                    <?
                    if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
                    else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
                    ?>
                </td>
                <td style="font-size:14px; font-weight:bold;" width="100">Yarn Issue Ch. No</td>
                <td width="180">:&nbsp;<? echo $sql_data[0][csf('yarn_issue_challan_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="100">Production Basis</td>
                <td width="180">:<? echo $receive_basis[$roll_details_array[$data_array_mst[0][csf("barcode_no")]]['receive_basis']]; ?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:16px; font-weight:bold;">Recevied Date</td>
                <td width="180">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td width="100" style="font-size:14px; font-weight:bold;">Buyer</td>
                <td width="180">:&nbsp;<? echo $buyer; ?></td>
                <?php if ($data[4]) { ?>
                <td style="font-size:14px; font-weight:bold;" width="100">PO Company</td>
                <td width="180">
                    :&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
                    <?php } ?>
            </tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="100">Remarks</td>
                <td colspan="3" width="180">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
                <td width="" id="barcode_img_id" colspan="2"></td>

            </tr>
        </table>
        <br>
        <table cellspacing="0" cellpadding="3" border="1" rules="all" width="1110" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
            <?php


            $i = 1;
            $tot_qty = 0;


            $i = 1;
            foreach ($data_array_mst as $row)
            {
                $is_sales_id=$row[csf("is_sales")];
                $sales_booking=$sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
                $within_group=$sales_arr[$row[csf('po_breakdown_id')]]['within_group'];

                $without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
                if ($without_order == 1)
                {
                    $buyer = $data_non_arr[$row[csf('book_no')]]['buyer_id'];
                    $job_sys = $data_non_arr[$row[csf('book_no')]]['booking_no'];
                    $job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
                    $int_ref = "";
                }
                else
                {
                    if($is_sales_id==1)
                    {
                        if($within_group==1)
                        {
                            $job_no=explode("-",$booking_arr[$sales_booking]['job_no']);
                            $job_no_prefix=(int)$job_no[2];
                            $job_buyer = "<b>B: </b>".$buyer_array[$booking_arr[$sales_booking]['buyer_id']] . "<br>" ."<b>J: </b>".$job_no_prefix;
                            $int_ref = $booking_arr[$sales_booking]['int_ref'];
                        }
                        else
                        {
                            $job_buyer = "<b>B: </b>".$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']] . "<br>" ."<b>J:";
                            $int_ref = "";
                        }
                        $po_number=$sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
                        $booking_no = $sales_booking;

                    }
                    else
                    {
                        $po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
                        $job_buyer = "<b>B: </b>".$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>" ."<b>J: </b>".$job_array[$row[csf('po_breakdown_id')]]['job'];
                        $booking_no = $booking_po_arr[$row[csf('po_breakdown_id')]]["booking_no"];
                            //echo $booking_no."test";
                        $int_ref = $booking_po_arr[$row[csf('po_breakdown_id')]]["grouping"];
                    }
                }

                if ($row[csf('receive_basis')] == 1)
                {
                    $booking_no = explode("-", $row[csf('booking_no')]);
                    $prog_book_no = (int)$booking_no[3];
                }
                else
                {
                    $prog_book_no = $row[csf('booking_no')];
                }

                $knit_company = "&nbsp;";
                if ($row[csf("knitting_source")] == 1)
                {
                    $knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
                }
                else if ($row[csf("knitting_source")] == 3)
                {
                    $knit_company = $supplier_arr[$row[csf("knitting_company")]];
                }

                $count = '';
                $yarn_count = explode(",", $row[csf('yarn_count')]);
                foreach ($yarn_count as $count_id)
                {
                    if ($count == '')
                        $count = $yarn_count_details[$count_id];
                    else
                        $count .= "," . $yarn_count_details[$count_id];
                }

                $color_names = '';
                $colorIds = array_unique(explode(",", $row[csf("color_id")]));
                foreach ($colorIds as $color_id)
                {
                    $color_names .= $color_arr[$color_id] . ",";
                }
                $color_names = chop($color_names, ',');
                if ($i == 1)
                {
                    ?>
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order No</th>
                            <th width="80">Style</th>
                            <th width="140">Booking No</th>
                            <th width="50">Yarn Count</th>
                            <th width="70">Yarn Brand</th>
                            <th width="60">Lot No</th>
                            <th width="170">Fab Color</th>
                            <th width="250">Fabric Type</th>
                            <th width="50">Stich</th>
                            <th width="50">Fin GSM</th>
                            <th width="40">Fab. Dia</th>
                            <th width="40">Roll No</th>
                            <th>QC Pass Qty</th>
                        </tr>
                    </thead>
                	<?php
                }
                ?>
                <tr>
                    <td width="30"><? echo $i; ?></td>
                    <td width="140" style="word-break:break-all;"><? echo $po_number;?></td>
                    <td width="80"><? echo $style_ref_no; ?></td>
                    <td width="140" style="text-align: center;"><? echo $booking_no; ?></td>
                    <td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                    <td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                    <td width="170" style="word-break:break-all;"><? echo $color_names; ?></td>
                    <td width="250" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                    <td width="50" style="word-break:break-all;"
                    align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
                    <td width="50" style="word-break:break-all;"
                    align="center"><? echo $row[csf('gsm')]; ?></td>
                    <td width="40" style="word-break:break-all;"
                    align="center"><? echo $row[csf('width')]; ?></td>
                    <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
                </tr>
                <?
                $tot_qty += $row[csf('qnty')];
                $i++;
            }
            ?>
            <tr>
                <td align="right" colspan="13"><strong>Total= </strong></td>
                <td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
            </tr>
        </table>
    </div>
    <? echo signature_table(16, $company, "1010px"); ?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess)
		{
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_print4") // Print 4 for Palmal
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$location = $data[4];
	$storeId = $data[5];

	// echo "<pre>";
	// print_r($data);

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
		else
		{
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div>
		<table width="100%" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
			<td align="center" style="font-size:x-large">
				<strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
				<strong><? echo $com_dtls[1]; ?></strong>
			</td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Fabric Roll Receive</u></strong></td>
			</tr>
			<tr>
            <td align="center" style="font-size:18px"><strong><u>Receive
                ID <? echo $txt_challan_no; ?></u></strong></td>
            </tr>
		</table>
		<br>
		<?
        $sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,store_id,location_id, yarn_issue_challan_no, remarks,supplier_id from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
        $chalan_no = $sql_data[0][csf('booking_no')];
        $mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
            inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
            inner join inv_receive_master c on b.grey_sys_id = c.id
            where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
        $search_param = $mstData[0][csf('booking_no')];
        if ($mstData[0][csf('receive_basis')] == 2)
        {
            $booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
                inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
                inner join wo_booking_mst c on b.booking_no = c.booking_no
                where a.id = $search_param");

            $is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
            if ($is_salesOrder == "" || $is_salesOrder == 0)
            {
                $is_salesOrder = 0;
            }
            else
            {
                $is_salesOrder = 1;
            }
        }
        else if ($mstData[0][csf('receive_basis')] == 4)
        {
            $is_salesOrder = 1;
            $booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
                inner join wo_booking_mst b on a.booking_id = b.id
                where a.job_no = '$search_param'");
        }
        else
        {
            $booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
        }
        ?>
        <table width="100%" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="150">Delivery Challan</td>
                <td width="200" style="font-size:18px; font-weight:bold;">:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Recevied Date</td>
                <td width="200" align=""><? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Store Name</td>
                <td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="150">Challan No</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Company</td>
                <td width="200" id="" align=""><? echo $company_array[$company]['name']; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Knitting</td>
                <td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:14px; font-weight:bold;" width="150">Knitting Com</td>
                <td width="200">:&nbsp;
                    <?
                    if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
                    else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
                    ?>
                </td>
                <td style="font-size:14px; font-weight:bold;" width="150">Location</td>
                <td width="200">:&nbsp;<? echo $location_arr[$sql_data[0][csf('location_id')]]; ?></td>
                <td style="font-size:14px; font-weight:bold;" width="150">Remarks</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" cellpadding="3" border="1" rules="all" width="1710" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
            <?php
			/*
			|--------------------------------------------------------------------------
			| for floor, room, rack and shelf disable
			|--------------------------------------------------------------------------
			|
			*/
			$lib_floor_arr=array(); $lib_room_arr=array(); $lib_rack_arr=array(); $lib_shelf_arr=array();
			 $lib_bin_arr=array();
			$lib_room_rack_shelf_sql = "SELECT b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
			FROM lib_floor_room_rack_dtls b
			left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
			left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
			left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
			left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
			left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
			WHERE b.status_active=1 and b.is_deleted=0 and b.company_id=".$company." and b.store_id=".$storeId." and b.location_id=".$location."";
			//echo $lib_room_rack_shelf_sql;die;
			$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
			if(!empty($lib_rrsb_arr))
			{
				foreach ($lib_rrsb_arr as $room_rack_shelf_row)
				{
					//$companyId  = $room_rack_shelf_row[csf("company_id")];
					$floorId = $room_rack_shelf_row[csf("floor_id")];
					$roomId  = $room_rack_shelf_row[csf("room_id")];
					$rackId  = $room_rack_shelf_row[csf("rack_id")];
					$shelfId = $room_rack_shelf_row[csf("shelf_id")];
					$binId   = $room_rack_shelf_row[csf("bin_id")];

					if($floorId!="")
					{
						$lib_floor_arr[$floorId] = $room_rack_shelf_row[csf("floor_name")];
					}

					if($floorId!="" && $roomId!="")
					{
						$lib_room_arr[$roomId] = $room_rack_shelf_row[csf("room_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="")
					{
						$lib_rack_arr[$rackId] = $room_rack_shelf_row[csf("rack_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="")
					{
						$lib_shelf_arr[$shelfId] = $room_rack_shelf_row[csf("shelf_name")];
					}

					if($floorId!="" && $roomId!="" && $rackId!="" && $shelfId!="" && $binId!="")
					{
						$lib_bin_arr[$binId] = $room_rack_shelf_row[csf("bin_name")];
					}
				}
			}
			else
			{
				$lib_floor_arr[0]="";
				$lib_room_arr[0]="";
				$lib_rack_arr[0]="";
				$lib_shelf_arr[0]="";
				$lib_bin_arr[0]="";
			}
			//end

            $i = 1;
            $tot_qty = 0;
            $receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
            /*$receive_basis_arr = array();
            $data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
            foreach ($data_array_receive_basis as $row)
            {
                $receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
            }*/

            if ($sql_data[0][csf('knitting_source')] == 1)
            {
                $sql = "SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no,c.is_sales,c.booking_without_order
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
                WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0 order by d.seq_no, c.id ";
                    //echo $sql;
            }
            else
            {
                $sql = "SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date,
                a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty ,c.is_sales,c.booking_without_order
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
                WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=0 order by c.id ";
                    //echo $sql;
            }

            // echo $sql;//die();
            $data_array_mst = sql_select($sql);
            $po_id_array = $sales_id_array = $booking_program_arr = array();
            foreach ($data_array_mst as $row)
            {

                if($row[csf("is_sales")] == 1)
                {
                    $sales_id_array[] = $row[csf("po_breakdown_id")];
                }
                else
                {
                    if($row[csf("booking_without_order")] ==0)
                    {
                        $po_id_array[] = $row[csf("po_breakdown_id")];
                    }
                }

                if ($row[csf('receive_basis')] == 2)
                {
                    $booking_program_arr[] = $row[csf("booking_no")];
                }
                else
                {
                    $booking_no = explode("-", $row[csf('booking_no')]);
                    $booking_program_arr[] = (int)$booking_no[3];
                }

                $booking_pro=$roll_details_array[$row[csf("barcode_no")]]['booking_no'];
                $ref_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            }

            // ===========================
            $ref_barcode_nos = implode(",", $ref_barcode_arr);
			$barCond = $ref_barcode_no_cond = "";
			if($db_type==2 && count($ref_barcode_arr)>999)
			{
				$ref_barcode_arr_chunk=array_chunk($ref_barcode_arr,999) ;
				foreach($ref_barcode_arr_chunk as $chunk_arr)
				{
					$barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
				}

				$ref_barcode_no_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$ref_barcode_no_cond=" and c.barcode_no in($ref_barcode_nos)";
			}
			ini_set('memory_limit', '-1');
            $data_array = sql_select("SELECT a.id,a.recv_number,a.recv_number_prefix_num,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order, b.machine_dia, b.machine_gg
                FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 $ref_barcode_no_cond
                and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) order by c.id desc");
            $roll_details_array = array();
            $barcode_array = array();
            foreach ($data_array as $row)
            {
                $roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
                $roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
                $roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
                $roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
                $roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
                $roll_details_array[$row[csf("barcode_no")]]['machine_dia'] = $row[csf("machine_dia")];
                $roll_details_array[$row[csf("barcode_no")]]['machine_gg'] = $row[csf("machine_gg")];
                $roll_details_array[$row[csf("barcode_no")]]['production_id'] = $row[csf("recv_number_prefix_num")];
                $roll_details_array[$row[csf("barcode_no")]]['production_number'] = $row[csf("recv_number")];
            }
            // ===========================

            $planOrder = sql_select("select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
            $plan_arr = array();
            foreach ($planOrder as $plan_row)
            {
                $plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
            }

            $job_array = $sales_arr = $sales_booking_arr = $booking_arr = array();
            if(!empty($po_id_array))
            {
                $job_sql = "select a.job_no_prefix_num, a.job_no,a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
                $job_sql_result = sql_select($job_sql);
                foreach ($job_sql_result as $row)
                {
                    $job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
                    $job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
                    $job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                }
            }

            if(!empty($sales_id_array))
            {
                $sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id, customer_buyer,insert_date from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
                foreach ($sales_details as $sales_row)
                {
                    $sales_arr[$sales_row[csf('id')]]['po_number'] 		= $sales_row[csf('job_no')];
                    $sales_arr[$sales_row[csf('id')]]['sales_booking_no']= $sales_row[csf('sales_booking_no')];
                    $sales_arr[$sales_row[csf('id')]]['within_group'] 	= $sales_row[csf('within_group')];
                    $sales_arr[$sales_row[csf('id')]]['year'] 			= date("Y", strtotime($sales_row[csf("insert_date")]));
                    $sales_arr[$sales_row[csf('id')]]['buyer_id'] 		= $sales_row[csf('buyer_id')];
                    $sales_arr[$sales_row[csf('id')]]['customer_buyer'] 		= $sales_row[csf('customer_buyer')];
                    $sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
                }
            }

            $booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",array_unique($po_id_array)).")";

            $booking_details = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and b.booking_type in(1,4) $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

            foreach ($booking_details as $booking_row)
            {
                $booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
                $booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
                $booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
                $booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
            }

            $data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
                FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $booking_pro . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
            foreach ($data_none_array as $row)
            {
                $data_non_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf("buyer_id")];
                $data_non_arr[$row[csf('booking_no')]]['booking_no']=$row[csf("booking_no")];
            }

            $i = 1;
            foreach ($data_array_mst as $row)
            {
                $is_sales_id=$row[csf("is_sales")];
                $sales_booking=$sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
                $within_group=$sales_arr[$row[csf('po_breakdown_id')]]['within_group'];

                $without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
                if ($without_order == 1)
                {
                    $buyer = $data_non_arr[$row[csf('book_no')]]['buyer_id'];
                    $job_sys = $data_non_arr[$row[csf('book_no')]]['booking_no'];
                    $buyer_customer_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
                }
                else
                {
                    if($is_sales_id==1)
                    {
                        if($within_group==1)
                        {
                            $job_no=explode("-",$booking_arr[$sales_booking]['job_no']);
                            $job_no_prefix=(int)$job_no[2];
                            $buyer_customer_buyer = "<b>C: </b>".$buyer_array[$booking_arr[$sales_booking]['buyer_id']] . "<br>" ."<b>C.B: </b>";
                        }
                        else
                        {
                            $buyer_customer_buyer = "<b>C: </b>".$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']] . "<br>" ."<b>C.B:</b>".$customer_buyer_name = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["customer_buyer"]];
                        }
                        $po_number=$sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
                        $booking_no = $sales_booking;

                    }
                    else
                    {
                        $po_number = $job_array[$row[csf('po_breakdown_id')]]['po'];
                        $buyer_customer_buyer = "<b>C: </b>".$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>" ."<b>C.B: </b>".$job_array[$row[csf('po_breakdown_id')]]['job'];
                        $booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
                            //echo $booking_no."test";
                    }
                }

                if ($row[csf('receive_basis')] == 1)
                {
                    $booking_no = explode("-", $row[csf('booking_no')]);
                    $prog_book_no = (int)$booking_no[3];
                }
                else
                {
                    $prog_book_no = $row[csf('booking_no')];
                }

                $knit_company = "&nbsp;";
                if ($row[csf("knitting_source")] == 1)
                {
                    $knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
                }
                else if ($row[csf("knitting_source")] == 3)
                {
                    $knit_company = $supplier_arr[$row[csf("knitting_company")]];
                }

                $count = '';
                $yarn_count = explode(",", $row[csf('yarn_count')]);
                foreach ($yarn_count as $count_id)
                {
                    if ($count == '')
                        $count = $yarn_count_details[$count_id];
                    else
                        $count .= "," . $yarn_count_details[$count_id];
                }

                $color_names = '';
                $colorIds = array_unique(explode(",", $row[csf("color_id")]));
                foreach ($colorIds as $color_id)
                {
                    $color_names .= $color_arr[$color_id] . ",";
                }
                $color_names = chop($color_names, ',');
                if ($i == 1)
                {
                    ?>
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order No</th>
                            <th width="80">Customer/<br>Customer Buyer</th>
                            <th width="140">Booking No/<br>Prog No</th>
                            <th width="140">Production ID</th>
                            <th width="100">Production Basis</th>
                            <!--<th width="70">Knitting Source</th>-->
                            <th width="70">Knitting Company</th>
                            <th width="50">Yarn Count</th>
                            <th width="70">Yarn Brand</th>
                            <th width="60">Lot No</th>
                            <th width="70">Fab Color</th>
                            <th width="70">Color Range</th>
                            <th width="150">Fabric Type</th>
                            <th width="50">Stich Length</th>
                            <th width="50">Fin GSM</th>
                            <th width="40">Fab. Dia</th>
                            <th width="40">Machine No</th>
                            <th width="40">Barcode No</th>
                            <th width="40">Roll No</th>
                            <th width="40">QC Pass Qty</th>
                            <th width="70">Floor</th>
                            <th width="70">Room</th>
                            <th width="70">Rack</th>
                            <th width="70">Shelf</th>
                            <th>Bin/Box</th>
                        </tr>
                    </thead>
                	<?php
                }
                ?>
                <tr>
                    <td width="30"><? echo $i; ?></td>
                    <td width="140" style="word-break:break-all;"><? echo $po_number;?></td>
                    <td width="80"><? echo $buyer_customer_buyer; ?></td>
                    <td width="140" style="text-align: center;"><? echo $booking_no.'<br>'.$row[csf("book_no")]; ?></td>
                    <td width="140" align="center" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['production_number'];//$prog_book_no; ?></td>
                    <td width="100"><? echo $receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']]; ?></td>
                    <td width="70"><? echo $knit_company; ?></td>
                    <td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                    <td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $color_names; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                    <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                    <td width="50" style="word-break:break-all;"
                    align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
                    <td width="50" style="word-break:break-all;"
                    align="center"><? echo $row[csf('gsm')]; ?></td>
                    <td width="40" style="word-break:break-all;"
                    align="center"><? echo $row[csf('width')]; ?></td>
                    <td width="40" style="word-break:break-all;" align="center"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
                    <td width="40" align="center"><? echo $row[csf("barcode_no")]; ?></td>
                    <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                    <td width="40" align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_floor_arr[$row[csf('floor_id')]]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_room_arr[$row[csf('room')]]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_rack_arr[$row[csf('rack')]]; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $lib_shelf_arr[$row[csf('self')]]; ?></td>
                    <td style="word-break:break-all;"><? echo $lib_bin_arr[$row[csf('bin_box')]]; ?></td>
                </tr>
                <?
                $tot_qty += $row[csf('qnty')];
                $i++;
            }
            ?>
            <tr>
                <td align="right" colspan="19"><strong>Total= </strong></td>
                <td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
    <? echo signature_table(16, $company, "1710px"); ?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
	        var value = valuess;//$("#barcodeValue").val();
	        //alert(value)
	        var btype = 'code39';//$("input[name=btype]:checked").val();
	        var renderer = 'bmp';// $("input[name=renderer]:checked").val();

	        var settings = {
	            output: renderer,
	            bgColor: '#FFFFFF',
	            color: '#000000',
	            barWidth: 1,
	            barHeight: 40,
	            moduleSize: 5,
	            posX: 10,
	            posY: 20,
	            addQuietZone: 1
	        };
	        //$("#barcode_img_id").html('11');
	        value = {code: value, rect: false};

	        $("#barcode_img_id").show().barcode(value, btype, settings);
    	}
    	generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_print3")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$location = $data[8];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$location_name = $data[6];
	$knitting_source = $data[7];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
	/*if (array_key_exists($row[csf('id')], $composition_arr)) {
		$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	} else {
		$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	}*/
	if (array_key_exists($row[csf('id')], $composition_arr)) {
		$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]];
	} else {
		$composition_arr[$row[csf('id')]] = $row[csf('construction')];
	}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div>
	<table width="100%" cellspacing="0" align="center" border="0"
	style="font-family: tahoma; font-size: 12px;">
	<tr>
		<td align="center" style="font-size:x-large">
			<strong><? echo $com_dtls[0]; ?></strong></td>
	</tr>
	<tr>
		<td align="center" style="font-size:14px">
			<strong><? echo $com_dtls[1]; ?></strong>
		</td>
	</tr>

		<tr>
			<td align="center" style="font-size:16px"><strong><u>Knit Grey Fabric Receive Report</u></strong></td>
		</tr>

	</table>
	<br>
	<?
	$sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,service_booking_no, store_id,location_id, yarn_issue_challan_no, boe_mushak_challan_no, boe_mushak_challan_date, remarks,supplier_id,knitting_location_id, location_id as lc_location from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
	$chalan_no = $sql_data[0][csf('booking_no')];
	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
	$search_param = $mstData[0][csf('booking_no')];
	if ($mstData[0][csf('receive_basis')] == 2) {
		$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	} else if ($mstData[0][csf('receive_basis')] == 4) {
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	} else {
		$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
	}
	/*================================================================================================*/
	$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
	/*$receive_basis_arr = array();
	$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
	foreach ($data_array_receive_basis as $row) {
		$receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
	}*/
	if ($sql_data[0][csf('knitting_source')] == 1) {
		$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no, c.booking_without_order,
		a.knitting_source, a.knitting_company, a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
		b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no,c.is_sales
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
		c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by d.seq_no, c.id ";
	} else {
		$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, c.booking_without_order,
		a.knitting_source, a.knitting_company,a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
		b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,	b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
		c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by c.id ";
	}
	//echo $sql;die();
	$data_array_mst = sql_select($sql);
	foreach($data_array_mst as $b_data)
	{
		$knbarcode_arr[]=$b_data[csf("barcode_no")];
	}
	$nbarcode = "'" . implode("','",$knbarcode_arr) . "'";

	ini_set('memory_limit', '-1');
	$data_array = sql_select("SELECT a.id,a.receive_basis,a.service_booking_no,a.challan_no, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order, a.recv_number, b.machine_dia, b.machine_gg, b.body_part_id, c.po_breakdown_id,c.coller_cuff_size, c.qc_pass_qnty_pcs,  d.body_part_type,body_part_full_name
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_body_part d on b.body_part_id = d.id and d.body_part_type in (40,50) and d.status_active =1, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
		and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in ($nbarcode) order by c.id desc");
	$roll_details_array = array();
	$barcode_array = array();
	$collar_cuff_array = array();
	foreach ($data_array as $row) {
		$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
		$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number'] = $row[csf("recv_number")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_dia'] = $row[csf("machine_dia")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_gg'] = $row[csf("machine_gg")];
		if($row[csf("body_part_type")] == 40 || $row[csf("body_part_type")] == 50)
		{
			$collar_cuff_array[$row[csf("body_part_type")]][$row[csf("without_order")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("coller_cuff_size")]] += $row[csf("qc_pass_qnty_pcs")];
		}


		$roll_details_array[$row[csf("barcode_no")]]['total_roll']++;
		$challan_no[]=$row[csf("challan_no")];
		$service_booking_no[]=$row[csf("service_booking_no")];

		if($row[csf("without_order")]==1)
		{
			$non_ord_samp_booking_id .= $row[csf("po_breakdown_id")] . ",";
		}
	}

	$non_ord_samp_booking_id = implode(",",array_unique(explode(",",chop($non_ord_samp_booking_id,","))));
	if($non_ord_samp_booking_id!="")
	{
		$sqlsamplnoOrder = sql_select("select id,booking_no,insert_date,buyer_id,grouping from wo_non_ord_samp_booking_mst where id in($non_ord_samp_booking_id) and status_active=1 and is_deleted=0");

		foreach ($sqlsamplnoOrder as $row) {
			$no_order_details_array[$row[csf("id")]]['booking_no'] = $row[csf("booking_no")];
			$no_order_details_array[$row[csf("id")]]['buyer_id'] = $row[csf("buyer_id")];
			$no_order_details_array[$row[csf("id")]]['int_ref'] = $row[csf("grouping")];
			$no_order_details_array[$row[csf("id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		}
	}

	?>
	<table width="100%" cellspacing="0" align="center" border="0"
	style="font-family: tahoma; font-size: 12px;">
	<tr>
		<td width="" id="barcode_img_id" colspan="2"></td>
	</tr>
	<tr>
		<td style="font-size:14px; font-weight:bold;" width="150">MRR No</td>
		<td width="200" style="font-size:18px; font-weight:bold;">
			:&nbsp;<? echo $txt_challan_no; ?></td>
			<td width="80" style="font-size:16px; font-weight:bold;">SB No</td>
			<td width="250" style="font-size:16px;">:&nbsp;<? echo implode(",",array_unique($service_booking_no)); ?></td>
			<td width="100" style="font-size:16px; font-weight:bold;">Recevied Date</td>
			<td width="">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
		</tr>
		<tr>
			<td style="font-size:14px; font-weight:bold;" width="150">Delivery Challan</td>
			<td width="200">
				:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="80">Sub.Con Chal. No</td>
				<td width="250">
					:&nbsp;<? echo implode(",",array_unique($challan_no)); ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">Store</td>
					<td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="150">Knitting Source</td>
					<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="80">Knitting Com</td>
					<td width="250" style="font-weight:bold;">:&nbsp;
						<?
						if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
						else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
						?>
					</td>
					<td style="font-size:14px; font-weight:bold;" width="150">Knit. Location</td>
					<td width="200" id="" align="">:&nbsp;<? echo $location_arr[$sql_data[0][csf('knitting_location_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="150">LC Company Location</td>
					<td width="200" id="" align="">:&nbsp;<? echo $location_arr[$sql_data[0][csf('lc_location')]]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">BOE/Mushak Challan No</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('boe_mushak_challan_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="150">BOE/Mushak Challan Date</td>
					<td width="200">:&nbsp;<? echo change_date_format($sql_data[0][csf('boe_mushak_challan_date')]); ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="150">Remarks</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
					<td></td><td></td>
					<td></td><td></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1310" class="rpt_table"
			style="font-family: tahoma; font-size: 12px;">
			<?php
			foreach($data_array_mst as $row)
			{
				if($row[csf("is_sales")] == 1){
					$sales_id_array[] = $row[csf("po_breakdown_id")];
				}else{
					$po_id_array[] = $row[csf("po_breakdown_id")];
				}
				$booking_pro=$roll_details_array[$row[csf("barcode_no")]]['booking_no'];
			}

			$job_array = $sales_arr = $sales_booking_arr = $booking_arr = array();
			if(!empty($po_id_array)){
				$job_sql = "select a.job_no_prefix_num, a.job_no,a.buyer_name, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['int_ref'] = $row[csf('grouping')];
				}
			}

			if(!empty($sales_id_array)){
				$sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id,insert_date from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
				foreach ($sales_details as $sales_row) {
					$sales_arr[$sales_row[csf('id')]]['po_number'] 		= $sales_row[csf('job_no')];
					$sales_arr[$sales_row[csf('id')]]['sales_booking_no']= $sales_row[csf('sales_booking_no')];
					$sales_arr[$sales_row[csf('id')]]['within_group'] 	= $sales_row[csf('within_group')];
					$sales_arr[$sales_row[csf('id')]]['year'] 			= date("Y", strtotime($sales_row[csf("insert_date")]));
					$sales_arr[$sales_row[csf('id')]]['buyer_id'] 		= $sales_row[csf('buyer_id')];
					$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
				}
			}

			$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
			$booking_details = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 $booking_cond and b.booking_type = 1 and a.is_short=2 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");
			foreach ($booking_details as $booking_row) {
				$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
				$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			}

			$i = 1;
			$tot_qty = 0;
			foreach ($data_array_mst as $row) {

				if ($loc_arr[$row[csf('location_id')]] == "") {
					$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
					$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
				}

				$knit_company = "&nbsp;";
				if ($row[csf("knitting_source")] == 1) {
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				} else if ($row[csf("knitting_source")] == 3) {
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('receive_basis')] == 1) {
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];

				} else $prog_book_no = $row[csf('booking_no')];

				$color_names = '';
				$colorIds = array_unique(explode(",", $row[csf("color_id")]));
				foreach ($colorIds as $color_id) {
					$color_names .= $color_arr[$color_id] . ",";
				}
				$color_names = chop($color_names, ',');

				$is_sales_id=$row[csf("is_sales")];
				$sales_booking=$sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
				$within_group=$sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
				$po_num = "";$job_buyer="";
				//$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
				$without_order = $row[csf("booking_without_order")];
				if ($without_order == 1) {
					$buyer = $no_order_details_array[$row[csf('po_breakdown_id')]]['buyer_id'];
					$job_sys = '';//$data_non_arr[$row[csf('book_no')]]['booking_no'];
					$job_buyer = $buyer_array[$buyer].'_'.$job_sys;
					if($row[csf("is_sales")]==1 && $sales_arr[$row[csf('po_breakdown_id')]]['within_group']==2)
					{
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
					}
					else
					{
						$booking_no = $no_order_details_array[$row[csf('po_breakdown_id')]]['booking_no'];
					}
					$int_ref = $no_order_details_array[$row[csf("po_breakdown_id")]]['int_ref'];
					$po_num = "";
				}
				else
				{
					if($is_sales_id==1){
						if($within_group==1){
							$job_no=explode("-",$booking_arr[$sales_booking]['job_no']);
							$job_no_prefix=(int)$job_no[2];
							$job_buyer = $buyer_array[$booking_arr[$sales_booking]['buyer_id']] . "_".$job_no_prefix;
						}else{
							$job_buyer =$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']] . "_";
						}
						$po_num=$sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
						$int_ref = "";
						$booking_no = $sales_booking;

					}else{
						$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
						$int_ref = $job_array[$row[csf('po_breakdown_id')]]['int_ref'];
						$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']] . "_".$job_array[$row[csf('po_breakdown_id')]]['job'];
						$booking_no = $row[csf('book_no')];
					}
				}
				$jobBuyer=explode("_",$job_buyer);

				$fabDesc = $row[csf('febric_description_id')];
				$gsm = $row[csf('gsm')];
				$dia = $row[csf('width')];

				$recv_number=$roll_details_array[$row[csf("barcode_no")]]['recv_number'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['job']	=$jobBuyer[1];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['basis']	=$receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['yarn_c']=$count;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['yarn_b']=$brand_details[$row[csf("brand_id")]];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['lot']	=$row[csf('yarn_lot')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['color']	=$color_names;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['color_r']=$color_range[$row[csf("color_range_id")]];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['fab_typ'] .=$composition_arr[$row[csf('febric_description_id')]]."__";
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['stich']	=$roll_details_array[$row[csf("barcode_no")]]['stitch_length'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['gsm']	=$row[csf('gsm')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['fab_dia']=$row[csf('width')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['machn_no']=$machine_details[$row[csf('machine_no_id')]];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['roll_no']+=$roll_details_array[$row[csf("barcode_no")]]['total_roll'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['qnty']+=$row[csf('qnty')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['recv_number'] .=",".$recv_number;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['int_ref'] =$int_ref;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['machine_dia'] =$roll_details_array[$row[csf("barcode_no")]]['machine_dia'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['machine_gg'] =$roll_details_array[$row[csf("barcode_no")]]['machine_gg'];
			}

			$feeder_sql = sql_select("SELECT b.id as program, b.feeder from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id=$company and b.location_id=$location_name and b.knitting_source=$knitting_source");
			foreach ($feeder_sql as $feeder_row)
			{
				$feeder_arr[$feeder_row[csf('program')]]['feeder'] = $feeder_row[csf('feeder')];;
			}

			foreach ($rpt_data as $jobn=>$jobrow) {
				foreach ($jobrow as $ponum=>$porow) {
					foreach ($porow as $buyn=>$buyrow) {
						foreach ($buyrow as $bookin=>$bookrow) {
							foreach ($bookrow as $program=>$fabdescrow) {
								foreach ($fabdescrow as $fabdesId=>$gsmrow) {
									foreach ($gsmrow as $gsm=>$diarow) {
										foreach ($diarow as $dia=>$row) {
											if ($i == 1) {

												?>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="60">Job No</th>
														<th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order
															No
														</th>
														<th width="100">Internal Ref.</th>
														<th width="140">Buyer</th>
														<th width="100">Production Basis</th>
														<th width="100">Prog/Booking No</th>
														<th width="140">Production ID</th>
														<th width="50">Yarn Count</th>
														<th width="70">Yarn Brand</th>
														<th width="60">Lot No</th>
														<th width="70">Fab Color</th>
														<th width="70">Color Range</th>
														<th width="150">Fabric Type</th>
														<th width="50">Stich</th>
														<th width="50">Fin GSM</th>
														<th width="40">Feeder </th>
														<th width="40">Fab. Dia</th>
														<th width="40">MC Dia</th>
														<th width="40">MC GG</th>
														<th width="40">Machine No</th>
														<th width="40">Total Roll Qty</th>
														<th>Total QC Pass Qty</th>
													</tr>
												</thead>
												<?php
											}
											?>
											<tr>
												<td width="30"><? echo $i; ?></td>

																<td width="60"><? echo $jobn;
																?></td>
																<td width="140"
																style="word-break:break-all;"><? echo $ponum;
																?></td>
																<td width="100"><? echo $row['int_ref'];?></td>

																<td width="140" style="text-align: center;"><? echo $buyn; ?></td>
																<td width="100"><? echo $row['basis']; ?></td>
																<td width="100" style="word-break:break-all;"><? echo $bookin; ?></td>
																<td width="70">
																	<?
																	echo implode(",",array_unique(explode(",",substr($row['recv_number'],1))));

																	?></td>
																	<td width="50" style="word-break:break-all;"><? echo $row['yarn_c']; ?></td>
																	<td width="70"
																	style="word-break:break-all;"><? echo $row['yarn_b']; ?></td>
																	<td width="60" style="word-break:break-all;"><? echo $row['lot']; ?></td>
																	<td width="70" style="word-break:break-all;"><? echo $row['color']; ?></td>
																	<td width="70"
																	style="word-break:break-all;"><? echo $row['color_r']; ?></td>
																	<td width="150" style="word-break:break-all;">
																		<?
																		$febType = implode(", ",array_filter(array_unique(explode("__", chop($row['fab_typ'],"__")))));
																		echo $febType; ?>
																	</td>
																	<td width="50" style="word-break:break-all;" align="center"><? echo $row['stich']; ?></td>
																	<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $feeder[$feeder_arr[$bookin]['feeder']]; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['fab_dia']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['machn_no']; ?></td>
																	<td width="40" align="center"><? echo $row['roll_no']; ?></td>
																	<td align="right"><? echo number_format($row['qnty'], 2); ?></td>
																</tr>
																<?
																$tot_roll_qty += $row['roll_no'];
																$tot_qty += $row['qnty'];
																$i++;
															}
														}
													}
												}
											}
										}
									}
								}
								?>
								<tr>
									<td align="right" colspan="21"><strong>Total= </strong></td>
									<td align="center"><? echo $tot_roll_qty; ?></td>
									<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
								</tr>
							</table>
							<br>
							<?
							if(!empty($collar_cuff_array))
							{
								$collar_array = $collar_cuff_array[40];
								$cuff_array = $collar_cuff_array[50];
								if(!empty($collar_array))
								{
									?>
									<table cellspacing="0" cellpadding="3" border="1" rules="all" width="400" class="rpt_table" align="left">
										<thead>
										<tr>
											<th colspan="4">Collar Details</th>
										</tr>
										<tr>
											<th>Internal Ref. No</th>
											<th>Fabric Booking No</th>
											<th>Size</th>
											<th>Qty Pcs</th>
										</tr>
										</thead>
										<tbody>
											<?
											$booking_without_order=$po_id=$size_no=$int_ref_no =$booking_number =""; $poNsizeArr = array();
											foreach ($collar_array as $poNsize => $val)
											{
												$poNsizeArr = explode("*", $poNsize);
												$booking_without_order = $poNsizeArr[0];
												$po_id = $poNsizeArr[1];
												$size_no = $poNsizeArr[2];
												if($booking_without_order == 0)
												{
													$int_ref_no = $job_array[$po_id]['int_ref'];
													$booking_number = $booking_arr[$po_id]["booking_no"];
												}
												else
												{
													$int_ref_no = $no_order_details_array[$po_id]['int_ref'];
													$booking_number = $no_order_details_array[$po_id]['booking_no'];
												}
												?>
												<tr>
													<td><? echo $int_ref_no;?></td>
													<td><? echo $booking_number;?></td>
													<td><? echo $size_no;?></td>
													<td><? echo $val;?></td>
												</tr>
												<?
												$collar_total += $val;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="3">Total</th>
												<th align="right"><? echo $collar_total;?></th>
											</tr>
										</tfoot>
									</table>
									<table align="left">
										<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
									</table>
									<?
								}
								if(!empty($cuff_array))
								{
									?>
									<table cellspacing="0" cellpadding="3" border="1" rules="all" width="400" class="rpt_table" align="left">
										<thead>
										<tr>
											<th colspan="4">Cuff Details</th>
										</tr>
										<tr>
											<th>Internal Ref. No</th>
											<th>Fabric Booking No</th>
											<th>Size</th>
											<th>Qty Pcs</th>
										</tr>
										</thead>
										<tbody>
											<?
											$booking_without_order=$po_id=$size_no=$int_ref_no =$booking_number =""; $poNsizeArr = array();
											foreach ($cuff_array as $poNsize => $val)
											{
												$poNsizeArr = explode("*", $poNsize);
												$booking_without_order = $poNsizeArr[0];
												$po_id = $poNsizeArr[1];
												$size_no = $poNsizeArr[2];
												if($booking_without_order == 0)
												{
													$int_ref_no = $job_array[$po_id]['int_ref'];
													$booking_number = $booking_arr[$po_id]["booking_no"];
												}
												else
												{
													$int_ref_no = $no_order_details_array[$po_id]['int_ref'];
													$booking_number = $no_order_details_array[$po_id]['booking_no'];
												}
												?>
												<tr>
													<td><? echo $int_ref_no;?></td>
													<td><? echo $booking_number;?></td>
													<td><? echo $size_no;?></td>
													<td><? echo $val;?></td>
												</tr>
												<?
												$cuff_total += $val;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="3">Total</th>
												<th align="right"><? echo $cuff_total;?></th>
											</tr>
										</tfoot>
									</table>
									<?
								}
							}
							?>

						</div>
						<? echo signature_table(16, $company, "1210px"); ?>
						<script type="text/javascript" src="../../../js/jquery.js"></script>
						<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
						<script>
							function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_print5")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$location = $data[8];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$location_name = $data[6];
	$knitting_source = $data[7];

	//var_dump($data);

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
	/*if (array_key_exists($row[csf('id')], $composition_arr)) {
		$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	} else {
		$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
	}*/
	if (array_key_exists($row[csf('id')], $composition_arr)) {
		$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]];
	} else {
		$composition_arr[$row[csf('id')]] = $row[csf('construction')];
	}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div>
	<table width="100%" cellspacing="0" align="center" border="0"
	style="font-family: tahoma; font-size: 12px;">
	<tr>
		<td align="center" style="font-size:x-large">
			<strong><? echo $com_dtls[0]; ?></strong></td>
	</tr>
	<tr>
		<td align="center" style="font-size:14px">
			<strong><? echo $com_dtls[1]; ?></strong>
		</td>
	</tr>

		<tr>
			<td align="center" style="font-size:16px"><strong><u>Knit Grey Fabric Receive Report</u></strong></td>
		</tr>

	</table>
	<br>
	<?
	$sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,service_booking_no, store_id,location_id, yarn_issue_challan_no, remarks,supplier_id,knitting_location_id, location_id as lc_location from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
	$chalan_no = $sql_data[0][csf('booking_no')];
	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
	$search_param = $mstData[0][csf('booking_no')];
	if ($mstData[0][csf('receive_basis')] == 2) {
		$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	} else if ($mstData[0][csf('receive_basis')] == 4) {
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	} else {
		$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
	}
	/*================================================================================================*/
	$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
	/*$receive_basis_arr = array();
	$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
	foreach ($data_array_receive_basis as $row) {
		$receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
	}*/
	if ($sql_data[0][csf('knitting_source')] == 1) {
		$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no, c.booking_without_order,
		a.knitting_source, a.knitting_company, a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
		b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no,c.is_sales
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
		c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by d.seq_no, c.id ";
	} else {
		$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, c.booking_without_order,
		a.knitting_source, a.knitting_company,a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
		b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,	b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
		c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by c.id ";
	}
	//echo $sql;die();
	$data_array_mst = sql_select($sql);
	foreach($data_array_mst as $b_data)
	{
		$knbarcode_arr[]=$b_data[csf("barcode_no")];
	}
	$nbarcode = "'" . implode("','",$knbarcode_arr) . "'";

	ini_set('memory_limit', '-1');
	$data_array = sql_select("SELECT a.id,a.receive_basis,a.service_booking_no,a.challan_no, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order, a.recv_number, b.machine_dia, b.machine_gg, b.body_part_id, c.po_breakdown_id,c.coller_cuff_size, c.qc_pass_qnty_pcs,  d.body_part_type,body_part_full_name
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_body_part d on b.body_part_id = d.id and d.body_part_type in (40,50) and d.status_active =1, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
		and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in ($nbarcode) order by c.id desc");
	$roll_details_array = array();
	$barcode_array = array();
	$collar_cuff_array = array();
	foreach ($data_array as $row) {
		$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
		$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
		$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
		$roll_details_array[$row[csf("barcode_no")]]['recv_number'] = $row[csf("recv_number")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_dia'] = $row[csf("machine_dia")];
		$roll_details_array[$row[csf("barcode_no")]]['machine_gg'] = $row[csf("machine_gg")];
		if($row[csf("body_part_type")] == 40 || $row[csf("body_part_type")] == 50)
		{
			$collar_cuff_array[$row[csf("body_part_type")]][$row[csf("without_order")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("coller_cuff_size")]] += $row[csf("qc_pass_qnty_pcs")];
		}


		$roll_details_array[$row[csf("barcode_no")]]['total_roll']++;
		$challan_no[]=$row[csf("challan_no")];
		$service_booking_no[]=$row[csf("service_booking_no")];

		if($row[csf("without_order")]==1)
		{
			$non_ord_samp_booking_id .= $row[csf("po_breakdown_id")] . ",";
		}
	}

	$non_ord_samp_booking_id = implode(",",array_unique(explode(",",chop($non_ord_samp_booking_id,","))));
	if($non_ord_samp_booking_id!="")
	{
		$sqlsamplnoOrder = sql_select("select id,booking_no,insert_date,buyer_id,grouping from wo_non_ord_samp_booking_mst where id in($non_ord_samp_booking_id) and status_active=1 and is_deleted=0");

		foreach ($sqlsamplnoOrder as $row) {
			$no_order_details_array[$row[csf("id")]]['booking_no'] = $row[csf("booking_no")];
			$no_order_details_array[$row[csf("id")]]['buyer_id'] = $row[csf("buyer_id")];
			$no_order_details_array[$row[csf("id")]]['int_ref'] = $row[csf("grouping")];
			$no_order_details_array[$row[csf("id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		}
	}

	?>
			<table width="100%" cellspacing="0" align="center" border="0"
			style="font-family: tahoma; font-size: 12px;">
				<tr>
					<td width="" id="barcode_img_id" colspan="2"></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="100">MRR No</td>
					<td width="150" style="font-size:18px; font-weight:bold;">
						:&nbsp;<? echo $txt_challan_no; ?></td>
						<td width="50" style="font-size:16px; font-weight:bold;">SB No</td>
						<td width="100" style="font-size:16px;">:&nbsp;<? echo implode(",",array_unique($service_booking_no)); ?></td>
						<td width="50" style="font-size:16px; font-weight:bold;">Recevied Date</td>
						<td width="">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="100">Delivery Challan</td>
					<td width="150">
						:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="50">Sub.Con Chal. No</td>
					<td width="100">
						:&nbsp;<? echo implode(",",array_unique($challan_no)); ?></td>
					<td style="font-size:14px; font-weight:bold;" width="50">Store</td>
					<td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="100">Knitting Source</td>
					<td width="150">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="50">Knitting Com</td>
					<td width="100" style="font-weight:bold;">:&nbsp;
						<?
						if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
						else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
						?>
					</td>
					<td style="font-size:14px; font-weight:bold;" width="50">Knit. Location</td>
					<td width="200" id="" align="">:&nbsp;<? echo $location_arr[$sql_data[0][csf('knitting_location_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:14px; font-weight:bold;" width="100">LC Company Location</td>
					<td width="150" id="" align="">:&nbsp;<? echo $location_arr[$sql_data[0][csf('lc_location')]]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="50">Remarks</td>
					<td width="100" colspan="2">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1170" class="rpt_table"
			style="font-family: tahoma; font-size: 12px;">
			<?php
			foreach($data_array_mst as $row)
			{
				if($row[csf("is_sales")] == 1){
					$sales_id_array[] = $row[csf("po_breakdown_id")];
				}else{
					$po_id_array[] = $row[csf("po_breakdown_id")];
				}
				$booking_pro=$roll_details_array[$row[csf("barcode_no")]]['booking_no'];
			}

			$job_array = $sales_arr = $sales_booking_arr = $booking_arr = array();
			if(!empty($po_id_array)){
				$job_sql = "select a.job_no_prefix_num, a.job_no,a.buyer_name, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
				$job_sql_result = sql_select($job_sql);
				foreach ($job_sql_result as $row) {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['int_ref'] = $row[csf('grouping')];
				}
			}

			if(!empty($sales_id_array)){
				$sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id,insert_date from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
				foreach ($sales_details as $sales_row) {
					$sales_arr[$sales_row[csf('id')]]['po_number'] 		= $sales_row[csf('job_no')];
					$sales_arr[$sales_row[csf('id')]]['sales_booking_no']= $sales_row[csf('sales_booking_no')];
					$sales_arr[$sales_row[csf('id')]]['within_group'] 	= $sales_row[csf('within_group')];
					$sales_arr[$sales_row[csf('id')]]['year'] 			= date("Y", strtotime($sales_row[csf("insert_date")]));
					$sales_arr[$sales_row[csf('id')]]['buyer_id'] 		= $sales_row[csf('buyer_id')];
					$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
				}
			}

			$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
			$booking_details = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 $booking_cond and b.booking_type = 1 and a.is_short=2 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");
			foreach ($booking_details as $booking_row) {
				$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
				$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			}

			$i = 1;
			$tot_qty = 0;
			foreach ($data_array_mst as $row) {

				if ($loc_arr[$row[csf('location_id')]] == "") {
					$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
					$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
				}

				$knit_company = "&nbsp;";
				if ($row[csf("knitting_source")] == 1) {
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				} else if ($row[csf("knitting_source")] == 3) {
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('receive_basis')] == 1) {
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];

				} else $prog_book_no = $row[csf('booking_no')];

				$color_names = '';
				$colorIds = array_unique(explode(",", $row[csf("color_id")]));
				foreach ($colorIds as $color_id) {
					$color_names .= $color_arr[$color_id] . ",";
				}
				$color_names = chop($color_names, ',');

				$is_sales_id=$row[csf("is_sales")];
				$sales_booking=$sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
				$within_group=$sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
				$po_num = "";$job_buyer="";
				//$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
				$without_order = $row[csf("booking_without_order")];
				if ($without_order == 1) {
					$buyer = $no_order_details_array[$row[csf('po_breakdown_id')]]['buyer_id'];
					$job_sys = '';//$data_non_arr[$row[csf('book_no')]]['booking_no'];
					$job_buyer = $buyer_array[$buyer].'_'.$job_sys;
					if($row[csf("is_sales")]==1 && $sales_arr[$row[csf('po_breakdown_id')]]['within_group']==2)
					{
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
					}
					else
					{
						$booking_no = $no_order_details_array[$row[csf('po_breakdown_id')]]['booking_no'];
					}
					$int_ref = $no_order_details_array[$row[csf("po_breakdown_id")]]['int_ref'];
					$po_num = "";
				}
				else
				{
					if($is_sales_id==1){
						if($within_group==1){
							$job_no=explode("-",$booking_arr[$sales_booking]['job_no']);
							$job_no_prefix=(int)$job_no[2];
							$job_buyer = $buyer_array[$booking_arr[$sales_booking]['buyer_id']] . "_".$job_no_prefix;
						}else{
							$job_buyer =$buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id']] . "_";
						}
						$po_num=$sales_arr[$row[csf('po_breakdown_id')]]['po_number'];
						$int_ref = "";
						$booking_no = $sales_booking;

					}else{
						$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
						$int_ref = $job_array[$row[csf('po_breakdown_id')]]['int_ref'];
						$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']] . "_".$job_array[$row[csf('po_breakdown_id')]]['job'];
						$booking_no = $row[csf('book_no')];
					}
				}
				$jobBuyer=explode("_",$job_buyer);

				$fabDesc = $row[csf('febric_description_id')];
				$gsm = $row[csf('gsm')];
				$dia = $row[csf('width')];

				$recv_number=$roll_details_array[$row[csf("barcode_no")]]['recv_number'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['job']	=$jobBuyer[1];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['basis']	=$receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['yarn_c']=$count;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['yarn_b']=$brand_details[$row[csf("brand_id")]];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['lot']	=$row[csf('yarn_lot')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['color']	=$color_names;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['color_r']=$color_range[$row[csf("color_range_id")]];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['fab_typ'] .=$composition_arr[$row[csf('febric_description_id')]]."__";
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['stich']	=$roll_details_array[$row[csf("barcode_no")]]['stitch_length'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['gsm']	=$row[csf('gsm')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['fab_dia']=$row[csf('width')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['machn_no']=$machine_details[$row[csf('machine_no_id')]];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['roll_no']+=$roll_details_array[$row[csf("barcode_no")]]['total_roll'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['qnty']+=$row[csf('qnty')];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['recv_number'] .=",".$recv_number;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['int_ref'] =$int_ref;
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['machine_dia'] =$roll_details_array[$row[csf("barcode_no")]]['machine_dia'];
				$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no][$fabDesc][$gsm][$dia]['machine_gg'] =$roll_details_array[$row[csf("barcode_no")]]['machine_gg'];
			}

			$feeder_sql = sql_select("SELECT b.id as program, b.feeder from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.company_id=$company and b.location_id=$location_name and b.knitting_source=$knitting_source");
			foreach ($feeder_sql as $feeder_row)
			{
				$feeder_arr[$feeder_row[csf('program')]]['feeder'] = $feeder_row[csf('feeder')];;
			}

			foreach ($rpt_data as $jobn=>$jobrow) {
				foreach ($jobrow as $ponum=>$porow) {
					foreach ($porow as $buyn=>$buyrow) {
						foreach ($buyrow as $bookin=>$bookrow) {
							foreach ($bookrow as $program=>$fabdescrow) {
								foreach ($fabdescrow as $fabdesId=>$gsmrow) {
									foreach ($gsmrow as $gsm=>$diarow) {
										foreach ($diarow as $dia=>$row) {
											if ($i == 1) {

												?>
												<thead>
													<tr>
														<th width="30">SL</th>
														<th width="60">Job No</th>
														<th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order
															No
														</th>
														<th width="100">Internal Ref.</th>
														<th width="140">Buyer</th>
														<th width="100">Production Basis</th>
														<th width="100">Prog/Booking No</th>
														<th width="50">Yarn Count</th>
														<th width="70">Yarn Brand</th>
														<th width="60">Lot No</th>
														<th width="70">Fab Color</th>
														<th width="70">Color Range</th>
														<th width="150">Fabric Type</th>
														<th width="50">Stich</th>
														<th width="50">Fin GSM</th>
														<th width="40">Feeder </th>
														<th width="40">Fab. Dia</th>
														<th width="40">MC Dia</th>
														<th width="40">MC GG</th>
														<th width="40">Machine No</th>
														<th width="40">Total Roll Qty</th>
														<th>Total QC Pass Qty</th>
													</tr>
												</thead>
												<?php
											}
											?>
											<tr>
												<td width="30"><? echo $i; ?></td>

																<td width="60"><? echo $jobn;
																?></td>
																<td width="140"
																style="word-break:break-all;"><? echo $ponum;
																?></td>
																<td width="100"><? echo $row['int_ref'];?></td>

																<td width="140" style="text-align: center;"><? echo $buyn; ?></td>
																<td width="100"><? echo $row['basis']; ?></td>
																<td width="100" style="word-break:break-all;"><? echo $bookin; ?></td>
																	<td width="50" style="word-break:break-all;"><? echo $row['yarn_c']; ?></td>
																	<td width="70"
																	style="word-break:break-all;"><? echo $row['yarn_b']; ?></td>
																	<td width="60" style="word-break:break-all;"><? echo $row['lot']; ?></td>
																	<td width="70" style="word-break:break-all;"><? echo $row['color']; ?></td>
																	<td width="70"
																	style="word-break:break-all;"><? echo $row['color_r']; ?></td>
																	<td width="150" style="word-break:break-all;">
																		<?
																		$febType = implode(", ",array_filter(array_unique(explode("__", chop($row['fab_typ'],"__")))));
																		echo $febType; ?>
																	</td>
																	<td width="50" style="word-break:break-all;" align="center"><? echo $row['stich']; ?></td>
																	<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $feeder[$feeder_arr[$bookin]['feeder']]; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['fab_dia']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
																	<td width="40" style="word-break:break-all;" align="center"><? echo $row['machn_no']; ?></td>
																	<td width="40" align="center"><? echo $row['roll_no']; ?></td>
																	<td align="right"><? echo number_format($row['qnty'], 2); ?></td>
																</tr>
																<?
																$tot_roll_qty += $row['roll_no'];
																$tot_qty += $row['qnty'];
																$i++;
															}
														}
													}
												}
											}
										}
									}
								}
								?>
								<tr>
									<td align="right" colspan="20"><strong>Total= </strong></td>
									<td align="center"><? echo $tot_roll_qty; ?></td>
									<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
								</tr>
							</table>
							<br>
							<?
							if(!empty($collar_cuff_array))
							{
								$collar_array = $collar_cuff_array[40];
								$cuff_array = $collar_cuff_array[50];
								if(!empty($collar_array))
								{
									?>
									<table cellspacing="0" cellpadding="3" border="1" rules="all" width="400" class="rpt_table" align="left">
										<thead>
										<tr>
											<th colspan="4">Collar Details</th>
										</tr>
										<tr>
											<th>Internal Ref. No</th>
											<th>Fabric Booking No</th>
											<th>Size</th>
											<th>Qty Pcs</th>
										</tr>
										</thead>
										<tbody>
											<?
											$booking_without_order=$po_id=$size_no=$int_ref_no =$booking_number =""; $poNsizeArr = array();
											foreach ($collar_array as $poNsize => $val)
											{
												$poNsizeArr = explode("*", $poNsize);
												$booking_without_order = $poNsizeArr[0];
												$po_id = $poNsizeArr[1];
												$size_no = $poNsizeArr[2];
												if($booking_without_order == 0)
												{
													$int_ref_no = $job_array[$po_id]['int_ref'];
													$booking_number = $booking_arr[$po_id]["booking_no"];
												}
												else
												{
													$int_ref_no = $no_order_details_array[$po_id]['int_ref'];
													$booking_number = $no_order_details_array[$po_id]['booking_no'];
												}
												?>
												<tr>
													<td><? echo $int_ref_no;?></td>
													<td><? echo $booking_number;?></td>
													<td><? echo $size_no;?></td>
													<td><? echo $val;?></td>
												</tr>
												<?
												$collar_total += $val;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="3">Total</th>
												<th align="right"><? echo $collar_total;?></th>
											</tr>
										</tfoot>
									</table>
									<table align="left">
										<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
									</table>
									<?
								}
								if(!empty($cuff_array))
								{
									?>
									<table cellspacing="0" cellpadding="3" border="1" rules="all" width="400" class="rpt_table" align="left">
										<thead>
										<tr>
											<th colspan="4">Cuff Details</th>
										</tr>
										<tr>
											<th>Internal Ref. No</th>
											<th>Fabric Booking No</th>
											<th>Size</th>
											<th>Qty Pcs</th>
										</tr>
										</thead>
										<tbody>
											<?
											$booking_without_order=$po_id=$size_no=$int_ref_no =$booking_number =""; $poNsizeArr = array();
											foreach ($cuff_array as $poNsize => $val)
											{
												$poNsizeArr = explode("*", $poNsize);
												$booking_without_order = $poNsizeArr[0];
												$po_id = $poNsizeArr[1];
												$size_no = $poNsizeArr[2];
												if($booking_without_order == 0)
												{
													$int_ref_no = $job_array[$po_id]['int_ref'];
													$booking_number = $booking_arr[$po_id]["booking_no"];
												}
												else
												{
													$int_ref_no = $no_order_details_array[$po_id]['int_ref'];
													$booking_number = $no_order_details_array[$po_id]['booking_no'];
												}
												?>
												<tr>
													<td><? echo $int_ref_no;?></td>
													<td><? echo $booking_number;?></td>
													<td><? echo $size_no;?></td>
													<td><? echo $val;?></td>
												</tr>
												<?
												$cuff_total += $val;
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="3">Total</th>
												<th align="right"><? echo $cuff_total;?></th>
											</tr>
										</tfoot>
									</table>
									<?
								}
							}
							?>

						</div>
						<? echo signature_table(16, $company, "1210px"); ?>
						<script type="text/javascript" src="../../../js/jquery.js"></script>
						<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
						<script>
							function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_print3---------------old action------------")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name, b.file_no, b.grouping as int_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['int_ref_no'] = $row[csf('int_ref_no')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	?>
	<div>
		<table width="100%" cellspacing="0" align="center" border="0"
		style="font-family: tahoma; font-size: 12px;">
		<tr>
			<td align="center" style="font-size:x-large">
				<strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Knit Grey Fabric Receive Report</u></strong></td>
			</tr>

		</table>
		<br>
		<?
		$sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,service_booking_no, store_id,location_id, yarn_issue_challan_no, remarks,supplier_id from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
		$chalan_no = $sql_data[0][csf('booking_no')];
		$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
			inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
			inner join inv_receive_master c on b.grey_sys_id = c.id
			where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
		$search_param = $mstData[0][csf('booking_no')];
		if ($mstData[0][csf('receive_basis')] == 2) {
			$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
				inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
				inner join wo_booking_mst c on b.booking_no = c.booking_no
				where a.id = $search_param");

			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
			if ($is_salesOrder == "" || $is_salesOrder == 0) {
				$is_salesOrder = 0;
			} else {
				$is_salesOrder = 1;
			}
		} else if ($mstData[0][csf('receive_basis')] == 4) {
			$is_salesOrder = 1;
			$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
				inner join wo_booking_mst b on a.booking_id = b.id
				where a.job_no = '$search_param'");
		} else {
			$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
		}
		/*================================================================================================*/
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
		$receive_basis_arr = array();
		$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		foreach ($data_array_receive_basis as $row) {
			$receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
		}
		if ($sql_data[0][csf('knitting_source')] == 1) {
			$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,
			a.knitting_source, a.knitting_company, a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
			b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
			c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by d.seq_no, c.id ";
		} else {
			$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date,
			a.knitting_source, a.knitting_company,a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
			b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,	b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
			c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by c.id ";
		}
			// echo $sql;die();
		$data_array_mst = sql_select($sql);
		foreach($data_array_mst as $b_data)
		{
			$knbarcode_arr[]=$b_data[csf("barcode_no")];
		}
		$nbarcode = "'" . implode("','",$knbarcode_arr) . "'";

		ini_set('memory_limit', '-1');
		$data_array = sql_select("SELECT a.id,a.receive_basis,a.service_booking_no,a.challan_no, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order, a.recv_number
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
			and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in ($nbarcode) order by c.id desc");
		$roll_details_array = array();
		$barcode_array = array();
		foreach ($data_array as $row) {
			$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
			$roll_details_array[$row[csf("barcode_no")]]['recv_number'] = $row[csf("recv_number")];
			$roll_details_array[$row[csf("barcode_no")]]['total_roll']++;
			$challan_no[]=$row[csf("challan_no")];
			$service_booking_no[]=$row[csf("service_booking_no")];
		}



		?>
		<table width="100%" cellspacing="0" align="center" border="0"
		style="font-family: tahoma; font-size: 12px;">
		<tr>
			<td width="" id="barcode_img_id" colspan="2"></td>
		</tr>
		<tr>
			<td style="font-size:14px; font-weight:bold;" width="150">MRR No</td>
			<td width="200" style="font-size:18px; font-weight:bold;">
				:&nbsp;<? echo $txt_challan_no; ?></td>
				<td width="80" style="font-size:16px; font-weight:bold;">SB No</td>
				<td width="250" style="font-size:16px;">:&nbsp;<? echo implode(",",array_unique($service_booking_no)); ?></td>
				<td width="100" style="font-size:16px; font-weight:bold;">Recevied Date</td>
				<td width="">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="150">Delivery Challan</td>
				<td width="200">
					:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="80">Sub.Con Chal. No</td>
					<td width="250">
						:&nbsp;<? echo implode(",",array_unique($challan_no)); ?></td>
						<td style="font-size:14px; font-weight:bold;" width="150">Store</td>
						<td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
					</tr>
					<tr>
						<td style="font-size:14px; font-weight:bold;" width="150">Knitting Source</td>
						<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
						<td style="font-size:14px; font-weight:bold;" width="80">Knitting Com</td>
						<td width="250" style="font-weight:bold;">:&nbsp;
							<?
							if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
							else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
							?>
						</td>
						<td style="font-size:14px; font-weight:bold;" width="150">Knit. Location</td>
						<td width="200" id="" align="">:&nbsp;<? echo $location_arr[$sql_data[0][csf('location_id')]]; ?></td>
					</tr>
					<tr>


						<td style="font-size:14px; font-weight:bold;" width="150">Remarks</td>
						<td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
					</tr>
				</table>
				<br>
				<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1310" class="rpt_table"
				style="font-family: tahoma; font-size: 12px;">
				<?php


				$i = 1;
				$tot_qty = 0;

				$order_data = array();
				$job_no_data = array();
				$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0");
				foreach ($booking_data as $row) {
					$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
					$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
				}

				$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
				foreach ($job_data as $row) {
					$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
				}
				$i = 1;

				foreach ($data_array_mst as $row) {

					$po_number = $row[csf('job_no')];
					$booking_no = $row[csf('sales_booking_no')];
					$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
						inner join wo_booking_dtls b on a.booking_no = b.booking_no
						where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

					if ($loc_arr[$row[csf('location_id')]] == "") {
						$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
						$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
					}

					$knit_company = "&nbsp;";
					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}

					$count = '';
					$yarn_count = explode(",", $row[csf('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
					}

					if ($row[csf('receive_basis')] == 1) {
						$booking_no = explode("-", $row[csf('booking_no')]);
						$prog_book_no = (int)$booking_no[3];

					} else $prog_book_no = $row[csf('booking_no')];

					$sales_order_no = "";
					$r_basis = $roll_details_array[$row[csf("barcode_no")]]['receive_basis'];
					if ($r_basis == 2) {
						$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];
						$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$plan_id");
						if ($is_salesOrder == "" || $is_salesOrder == 0) {
							$is_salesOrder = 0;
						} else {
							$is_salesOrder = 1;

						}
					}

					if ($r_basis == 4) {
						$is_salesOrder = 1;
					}

					$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
					if ($without_order == 1) {

						$data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
							FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $roll_details_array[$row[csf("barcode_no")]]['booking_no'] . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
						$buyer = $data_none_array[0][csf("buyer_id")];
						$job_sys = $data_none_array[0][csf("booking_no")];
						$job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
					} else {
						if ($is_salesOrder == 1) {
							$data_array = sql_select("select a.mst_id, b.booking_no, c.job_no, c.po_break_down_id from ppl_planning_info_entry_dtls a
								inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
								inner join wo_booking_mst c on b.booking_no = c.booking_no
								where a.id= $plan_id");
							$booking_no = $data_array[0]['BOOKING_NO'];
							if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
							} else {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
							}

							$po_jobs = explode(",", $order_data[$booking_no]['job_no']);
							foreach ($po_jobs as $job) {
								$po_job .= $job_no_data[$job] . ",";
							}
							$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];

						// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO

							$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "SALES_BOOKING_NO='$booking_no'");
							$salesOrder_id = sql_select("SELECT ID,JOB_NO,JOB_NO_PREFIX_NUM, BUYER_ID  FROM FABRIC_SALES_ORDER_MST WHERE SALES_BOOKING_NO='$booking_no'");
							$po_id = $salesOrder_id[0]['ID'];
							$po_num = $salesOrder_id[0]['JOB_NO'];
							$job_buyer = $buyer_array[$order_data[$booking_no]['buyer_id']] ."_". rtrim($po_job, ',');
						} else {
							$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
							$booking_no = $row[csf('book_no')];
							$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."_". $job_array[$row[csf('po_breakdown_id')]]['job'];
						}
						$jobBuyer=explode("_",$job_buyer);

						$file = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					}

					$color_names = '';
					$colorIds = array_unique(explode(",", $row[csf("color_id")]));
					foreach ($colorIds as $color_id) {
						$color_names .= $color_arr[$color_id] . ",";
					}
					$color_names = chop($color_names, ',');

					$recv_number=$roll_details_array[$row[csf("barcode_no")]]['recv_number'];

					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['job']	=$jobBuyer[1];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['basis']	=$receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['yarn_c']=$count;
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['yarn_b']=$brand_details[$row[csf("brand_id")]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['lot']	=$row[csf('yarn_lot')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['color']	=$color_names;
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['color_r']=$color_range[$row[csf("color_range_id")]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['fab_typ']=$composition_arr[$row[csf('febric_description_id')]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['stich']	=$roll_details_array[$row[csf("barcode_no")]]['stitch_length'];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['gsm']	=$row[csf('gsm')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['fab_dia']=$row[csf('width')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['machn_no']=$machine_details[$row[csf('machine_no_id')]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['roll_no']+=$roll_details_array[$row[csf("barcode_no")]]['total_roll'];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['qnty']+=$row[csf('qnty')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['recv_number'] .=",".$recv_number;
				}
			//echo "<pre>";
			// print_r($rpt_data);
			// die;
				foreach ($rpt_data as $jobn=>$jobrow) {
					foreach ($jobrow as $ponum=>$porow) {
						foreach ($porow as $buyn=>$buyrow) {
							foreach ($buyrow as $bookin=>$bookrow) {
								foreach ($bookrow as $program=>$row) {
									if ($i == 1) {
										?>
										<thead>
											<tr>
												<th width="30">SL</th>
												<th width="60">Job No</th>
												<th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order
													No
												</th>
												<th width="140">Buyer</th>
												<th width="100">Production Basis</th>
												<th width="100">Prog/Booking No</th>
												<th width="140">Production ID</th>
												<th width="50">Yarn Count</th>
												<th width="70">Yarn Brand</th>
												<th width="60">Lot No</th>
												<th width="70">Fab Color</th>
												<th width="70">Color Range</th>
												<th width="150">Fabric Type</th>
												<th width="50">Stich</th>
												<th width="50">Fin GSM</th>
												<th width="40">Fab. Dia</th>
												<th width="40">Machine No</th>
												<th width="40">Total Roll Qty</th>
												<th>Total QC Pass Qty</th>
											</tr>
										</thead>
										<?php } ?>
										<tr>
											<td width="30"><? echo $i; ?></td>

										<td width="60"><? echo $jobn;//$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job'];
										?></td>
										<td width="140"
											style="word-break:break-all;"><? echo $ponum;//;//$job_array[$row[csf('po_breakdown_id')]]['po'];
											?></td>

											<td width="140" style="text-align: center;"><? echo $buyn; ?></td>
											<td width="100"><? echo $row['basis']; ?></td>
											<td width="100" style="word-break:break-all;"><? echo $bookin; ?></td>
											<td width="70">
												<?
												echo implode(",",array_unique(explode(",",substr($row['recv_number'],1))));

												?></td>
												<td width="50" style="word-break:break-all;"><? echo $row['yarn_c']; ?></td>
												<td width="70"
												style="word-break:break-all;"><? echo $row['yarn_b']; ?></td>
												<td width="60" style="word-break:break-all;"><? echo $row['lot']; ?></td>
												<td width="70" style="word-break:break-all;"><? echo $row['color']; ?></td>
												<td width="70"
												style="word-break:break-all;"><? echo $row['color_r']; ?></td>
												<td width="150"
												style="word-break:break-all;"><? echo $row['fab_typ']; ?></td>
												<td width="50" style="word-break:break-all;"
												align="center"><? echo $row['stich']; ?></td>
												<td width="50" style="word-break:break-all;"
												align="center"><? echo $row['gsm']; ?></td>
												<td width="40" style="word-break:break-all;"
												align="center"><? echo $row['fab_dia']; ?></td>
												<td width="40" style="word-break:break-all;"
												align="center"><? echo $row['machn_no']; ?></td>
												<td width="40" align="center"><? echo $row['roll_no']; ?></td>
												<td align="right"><? echo number_format($row['qnty'], 2); ?></td>
											</tr>
											<?
											$tot_roll_qty += $row['roll_no'];
											$tot_qty += $row['qnty'];
											$i++;
										}
									}
								}
							}
						}
						?>
						<tr>
							<td align="right" colspan="17"><strong>Total= </strong></td>
							<td align="center"><? echo $tot_roll_qty; ?></td>
							<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
						</tr>
					</table>
				</div>
				<? echo signature_table(16, $company, "1210px"); ?>
				<script type="text/javascript" src="../../../js/jquery.js"></script>
				<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
				<script>
					function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "grey_fabric_receive_print3______")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name, b.file_no, b.grouping as int_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['int_ref_no'] = $row[csf('int_ref_no')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	?>
	<div>
		<table width="100%" cellspacing="0" align="center" border="0"
		style="font-family: tahoma; font-size: 12px;">
		<tr>
			<td align="center" style="font-size:x-large">
				<strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>

			<tr>
				<td align="center" style="font-size:16px"><strong><u>Knit Grey Fabric Receive Report</u></strong></td>
			</tr>

		</table>
		<br>
		<?
		$sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,service_booking_no, store_id,location_id, yarn_issue_challan_no, remarks,supplier_id from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");
		$chalan_no = $sql_data[0][csf('booking_no')];
		$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
			inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
			inner join inv_receive_master c on b.grey_sys_id = c.id
			where a.sys_number='$chalan_no' group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");
		$search_param = $mstData[0][csf('booking_no')];
		if ($mstData[0][csf('receive_basis')] == 2) {
			$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
				inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
				inner join wo_booking_mst c on b.booking_no = c.booking_no
				where a.id = $search_param");

			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
			if ($is_salesOrder == "" || $is_salesOrder == 0) {
				$is_salesOrder = 0;
			} else {
				$is_salesOrder = 1;
			}
		} else if ($mstData[0][csf('receive_basis')] == 4) {
			$is_salesOrder = 1;
			$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
				inner join wo_booking_mst b on a.booking_id = b.id
				where a.job_no = '$search_param'");
		} else {
			$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
		}
		/*================================================================================================*/
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
		$receive_basis_arr = array();
		$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		foreach ($data_array_receive_basis as $row) {
			$receive_basis_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
		}
		if ($sql_data[0][csf('knitting_source')] == 1) {
			$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no,
			a.knitting_source, a.knitting_company, a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,
			b.bin_box,c.qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty,c.booking_no book_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name d, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=d.id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
			c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by d.seq_no, c.id ";
		} else {
			$sql="SELECT a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date,
			a.knitting_source, a.knitting_company,a.service_booking_no,a.challan_no, b.id as dtls_id, b.prod_id, b.febric_description_id,b., b.gsm,b.room,b.rack,b.self,
			b.bin_box,c.qnty,c.roll_no,b.width,c.booking_no book_no,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,	b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.qc_pass_qnty
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
			c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0 order by c.id ";
		}
			//echo $sql;die();
		$data_array_mst = sql_select($sql);
		foreach($data_array_mst as $b_data)
		{
			$knbarcode_arr[]=$b_data[csf("barcode_no")];

		}
		$nbarcode = "'" . implode("','",$knbarcode_arr) . "'";
		print_r($nbarcode);
		die;
		ini_set('memory_limit', '-1');
		$data_array = sql_select("SELECT a.id,a.receive_basis,a.service_booking_no,a.challan_no, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
			and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in ($nbarcode) order by c.id desc");
		$roll_details_array = array();
		$barcode_array = array();
		foreach ($data_array as $row) {
			$roll_details_array[$row[csf("barcode_no")]]['mst_id'] = $row[csf("id")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
			$roll_details_array[$row[csf("barcode_no")]]['total_roll']++;
			$challan_no[]=$row[csf("challan_no")];
			$service_booking_no[]=$row[csf("service_booking_no")];
		}



		?>
		<table width="100%" cellspacing="0" align="center" border="0"
		style="font-family: tahoma; font-size: 12px;">
		<tr>
			<td width="" id="barcode_img_id" colspan="2"></td>
		</tr>
		<tr>
			<td style="font-size:14px; font-weight:bold;" width="150">MRR No</td>
			<td width="200" style="font-size:18px; font-weight:bold;">
				:&nbsp;<? echo $txt_challan_no; ?></td>
				<td width="80" style="font-size:16px; font-weight:bold;">SB No</td>
				<td width="250" style="font-size:16px;">:&nbsp;<? echo implode(",",array_unique($service_booking_no)); ?></td>
				<td width="100" style="font-size:16px; font-weight:bold;">Recevied Date</td>
				<td width="">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="150">Delivery Challan</td>
				<td width="200">
					:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
					<td style="font-size:14px; font-weight:bold;" width="80">Sub.Con Chal. No</td>
					<td width="250">
						:&nbsp;<? echo implode(",",array_unique($challan_no)); ?></td>
						<td style="font-size:14px; font-weight:bold;" width="150">Store</td>
						<td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
					</tr>
					<tr>
						<td style="font-size:14px; font-weight:bold;" width="150">Knitting Source</td>
						<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
						<td style="font-size:14px; font-weight:bold;" width="80">Knitting Com</td>
						<td width="250" style="font-weight:bold;">:&nbsp;
							<?
							if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
							else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
							?>
						</td>
						<td style="font-size:14px; font-weight:bold;" width="150">Knit. Location</td>
						<td width="200" id="" align="">:&nbsp;<? echo $location_arr[$sql_data[0][csf('knitting_location_id')]]; ?></td>
					</tr>
					<tr>


						<td style="font-size:14px; font-weight:bold;" width="150">Remarks</td>
						<td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
					</tr>
				</table>
				<br>
				<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1310" class="rpt_table"
				style="font-family: tahoma; font-size: 12px;">
				<?php

				$i = 1;
				$tot_qty = 0;

				$order_data = array();
				$job_no_data = array();
				$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0");
				foreach ($booking_data as $row) {
					$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
					$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
				}

				$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
				foreach ($job_data as $row) {
					$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
				}
				$i = 1;

				foreach ($data_array_mst as $row) {

					$po_number = $row[csf('job_no')];
					$booking_no = $row[csf('sales_booking_no')];
					$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
						inner join wo_booking_dtls b on a.booking_no = b.booking_no
						where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

					if ($loc_arr[$row[csf('location_id')]] == "") {
						$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
						$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
					}

					$knit_company = "&nbsp;";
					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}

					$count = '';
					$yarn_count = explode(",", $row[csf('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
					}

					if ($row[csf('receive_basis')] == 1) {
						$booking_no = explode("-", $row[csf('booking_no')]);
						$prog_book_no = (int)$booking_no[3];

					} else $prog_book_no = $row[csf('booking_no')];

					$sales_order_no = "";
					$r_basis = $roll_details_array[$row[csf("barcode_no")]]['receive_basis'];
					if ($r_basis == 2) {
						$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];
						$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$plan_id");
						if ($is_salesOrder == "" || $is_salesOrder == 0) {
							$is_salesOrder = 0;
						} else {
							$is_salesOrder = 1;

						}
					}

					if ($r_basis == 4) {
						$is_salesOrder = 1;
					}

					$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
					if ($without_order == 1) {

						$data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
							FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $roll_details_array[$row[csf("barcode_no")]]['booking_no'] . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
						$buyer = $data_none_array[0][csf("buyer_id")];
						$job_sys = $data_none_array[0][csf("booking_no")];
						$job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
					} else {
						if ($is_salesOrder == 1) {
							$data_array = sql_select("select a.mst_id, b.booking_no, c.job_no, c.po_break_down_id from ppl_planning_info_entry_dtls a
								inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
								inner join wo_booking_mst c on b.booking_no = c.booking_no
								where a.id= $plan_id");
							$booking_no = $data_array[0]['BOOKING_NO'];
							if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
							} else {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
							}

							$po_jobs = explode(",", $order_data[$booking_no]['job_no']);
							foreach ($po_jobs as $job) {
								$po_job .= $job_no_data[$job] . ",";
							}
							$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];

						// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO

							$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "SALES_BOOKING_NO='$booking_no'");
							$salesOrder_id = sql_select("SELECT ID,JOB_NO,JOB_NO_PREFIX_NUM, BUYER_ID  FROM FABRIC_SALES_ORDER_MST WHERE SALES_BOOKING_NO='$booking_no'");
							$po_id = $salesOrder_id[0]['ID'];
							$po_num = $salesOrder_id[0]['JOB_NO'];
							$job_buyer = $buyer_array[$order_data[$booking_no]['buyer_id']] ."_". rtrim($po_job, ',');
						} else {
							$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
							$booking_no = $row[csf('book_no')];
							$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."_". $job_array[$row[csf('po_breakdown_id')]]['job'];
						}
						$jobBuyer=explode("_",$job_buyer);

						$file = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					}
					echo "TEST";
					$color_names = '';
					$colorIds = array_unique(explode(",", $row[csf("color_id")]));
					foreach ($colorIds as $color_id) {
						$color_names .= $color_arr[$color_id] . ",";
					}
					$color_names = chop($color_names, ',');


					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['job']	=$jobBuyer[1];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['basis']	=$receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['yarn_c']=$count;
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['yarn_b']=$brand_details[$row[csf("brand_id")]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['lot']	=$row[csf('yarn_lot')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['color']	=$color_names;
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['color_r']=$color_range[$row[csf("color_range_id")]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['fab_typ']=$composition_arr[$row[csf('febric_description_id')]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['stich']	=$roll_details_array[$row[csf("barcode_no")]]['stitch_length'];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['gsm']	=$row[csf('gsm')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['fab_dia']=$row[csf('width')];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['machn_no']=$machine_details[$row[csf('machine_no_id')]];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['roll_no']+=$roll_details_array[$row[csf("barcode_no")]]['total_roll'];
					$rpt_data[$jobBuyer[1]][$po_num][$jobBuyer[0]][$booking_no][$prog_book_no]['qnty']+=$row[csf('qnty')];


				}
				echo "<pre>";
				print_r($rpt_data);
				die;
			//Old data
				{
					$po_number = $row[csf('job_no')];
					$booking_no = $row[csf('sales_booking_no')];
					$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
						inner join wo_booking_dtls b on a.booking_no = b.booking_no
						where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

					if ($loc_arr[$row[csf('location_id')]] == "") {
						$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
						$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
					}

					$knit_company = "&nbsp;";
					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}

					$count = '';
					$yarn_count = explode(",", $row[csf('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
					}

					if ($row[csf('receive_basis')] == 1) {
						$booking_no = explode("-", $row[csf('booking_no')]);
						$prog_book_no = (int)$booking_no[3];

					} else $prog_book_no = $row[csf('booking_no')];

					$sales_order_no = "";
					$r_basis = $roll_details_array[$row[csf("barcode_no")]]['receive_basis'];
					if ($r_basis == 2) {
						$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];
						$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$plan_id");
						if ($is_salesOrder == "" || $is_salesOrder == 0) {
							$is_salesOrder = 0;
						} else {
							$is_salesOrder = 1;

						}
					}

					if ($r_basis == 4) {
						$is_salesOrder = 1;
					}

					$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
					if ($without_order == 1) {

						$data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
							FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $roll_details_array[$row[csf("barcode_no")]]['booking_no'] . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
						$buyer = $data_none_array[0][csf("buyer_id")];
						$job_sys = $data_none_array[0][csf("booking_no")];
						$job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
					} else {
						if ($is_salesOrder == 1) {
							$data_array = sql_select("select a.mst_id, b.booking_no, c.job_no, c.po_break_down_id from ppl_planning_info_entry_dtls a
								inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
								inner join wo_booking_mst c on b.booking_no = c.booking_no
								where a.id= $plan_id");
							$booking_no = $data_array[0]['BOOKING_NO'];
							if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
							} else {
								$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
							}

							$po_jobs = explode(",", $order_data[$booking_no]['job_no']);
							foreach ($po_jobs as $job) {
								$po_job .= $job_no_data[$job] . ",";
							}
							$plan_id = $receive_basis_arr[$row[csf('barcode_no')]];

						// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO

							$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "SALES_BOOKING_NO='$booking_no'");
							$salesOrder_id = sql_select("SELECT ID,JOB_NO,JOB_NO_PREFIX_NUM, BUYER_ID  FROM FABRIC_SALES_ORDER_MST WHERE SALES_BOOKING_NO='$booking_no'");
							$po_id = $salesOrder_id[0]['ID'];
							$po_num = $salesOrder_id[0]['JOB_NO'];
							$job_buyer = $buyer_array[$order_data[$booking_no]['buyer_id']] ."_". rtrim($po_job, ',');
						} else {
							$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
							$booking_no = $row[csf('book_no')];
							$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."_". $job_array[$row[csf('po_breakdown_id')]]['job'];
						}
						$jobBuyer=explode("_",$job_buyer);

						$file = $job_array[$row[csf('po_breakdown_id')]]['file_no'];
					}

					$color_names = '';
					$colorIds = array_unique(explode(",", $row[csf("color_id")]));
					foreach ($colorIds as $color_id) {
						$color_names .= $color_arr[$color_id] . ",";
					}
					$color_names = chop($color_names, ',');
					if ($i == 1) {
						?>
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="60">Job No</th>
								<th width="140"><?php echo ($data[4] && $is_salesOrder == 1) ? "Sales" : ""; ?> Order
									No
								</th>
								<th width="140">Buyer</th>
								<th width="100">Production Basis</th>
								<th width="140">Progm/ Booking No</th>
								<th width="140">Production ID</th>
								<th width="50">Yarn Count</th>
								<th width="70">Yarn Brand</th>
								<th width="60">Lot No</th>
								<th width="70">Fab Color</th>
								<th width="70">Color Range</th>
								<th width="150">Fabric Type</th>
								<th width="50">Stich</th>
								<th width="50">Fin GSM</th>
								<th width="40">Fab. Dia</th>
								<th width="40">Machine No</th>
								<th width="40">Total Roll Qty</th>
								<th>Total QC Pass Qty</th>
							</tr>
						</thead>
						<?php } ?>
						<tr>
							<td width="30"><? echo $i; ?></td>

					<td width="60"><? echo $jobBuyer[1];//$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job'];
					?></td>
					<td width="140"
						style="word-break:break-all;"><? echo $po_num;//;//$job_array[$row[csf('po_breakdown_id')]]['po'];
						?></td>
						<td width="140" style="text-align: center;"><? echo $jobBuyer[0]; ?></td>
						<td width="100"><? echo $receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']]; ?></td>
						<td width="140" style="word-break:break-all;"><? echo $booking_no; ?></td>
						<td width="70"><? echo $prog_book_no; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $color_names; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
						<td width="150"
						style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;"
						align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
						<td width="50" style="word-break:break-all;"
						align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;"
						align="center"><? echo $row[csf('width')]; ?></td>
						<td width="40" style="word-break:break-all;"
						align="center"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
					</tr>
					<?
					$tot_qty += $row[csf('qnty')];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="18"><strong>Total= </strong></td>
					<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				</tr>
			</table>
		</div>
		<? echo signature_table(16, $company, "1210px"); ?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "issue_challan_no_popup")
{
	echo load_html_head_contents("Issue Challan Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id) {
			$('#issue_challan').val(id);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="issue_challan" id="issue_challan" value=""/>
	<?
	if ($db_type == 0) {
		$year_cond = "year(insert_date)as year";
	} else if ($db_type == 2) {
		$year_cond = "TO_CHAR(insert_date,'YYYY') as year";
	}
	$sql = "select issue_number_prefix_num, issue_number, $year_cond from inv_issue_master where company_id=$cbo_company_id and entry_form=3 and status_active=1 and is_deleted=0 order by issue_number_prefix_num DESC";

	echo create_list_view("tbl_list_search", "System ID, Challan No,Year", "150,80,70", "380", "350", 0, $sql, "js_set_value", "issue_number_prefix_num", "", 1, "0,0,0", $arr, "issue_number,issue_number_prefix_num,year", "", 'setFilterGrid("tbl_list_search",-1);', '0,0,0', '', 0);
	exit();
}

if ($action == "receive_challan_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];
	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where id=$company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	//  recv_number,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,store_id,location_id, yarn_issue_challan_no, remarks from inv_receive_master
	$dataArray = sql_select("select count(b.id) as total_roll,sum(b.qnty) as total_qty,a.receive_date,a.knitting_source,a.knitting_company from inv_receive_master a, pro_roll_details b where a.id=$update_id and  a.id=b.mst_id and b.entry_form=58 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.knitting_source,a.knitting_company");

	?>
	<div align="center">
		<table width="350" cellspacing="0">
			<tr>
				<td colspan="2" align="left" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td width="130"><strong>Issue No :</strong></td>
				<td width="200"><? echo $txt_issue_no; ?></td>
			</tr>

			<tr>
				<td><strong>Issue Date:</strong></td>
				<td width="200"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>No of roll:</strong></td>
				<td width="200"><? echo $dataArray[0][csf('total_roll')]; ?></td>
			</tr>
			<tr>
				<td><strong>Total Quantity:</strong></td>
				<td width="200"><? echo $dataArray[0][csf('total_qty')]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="200"><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Company:</strong></td>
				<td width="200">
					<?
					if ($dataArray[0][csf('knitting_source')] == 1) echo $company_array[$dataArray[0][csf('knitting_company')]]['name']; else if ($dataArray[0][csf('knitting_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]];
					?>
				</td>
			</tr>

		</table>

	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
	<?
	exit();
}

if ($action == "fabric_details_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$location_arr = return_library_array("select id, location_name from  lib_location", 'id', 'location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ", "id", "store_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {

			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	?>
	<div style="width:1010px;">
		<div style="width:1010px;">
			<table width="1010" cellspacing="0" align="center" border="0">
				<tr>
					<td align="center" style="font-size:x-large">
						<strong><? echo $company_array[$company]['name']; ?></strong></td>
				</tr>

				<tr>
					<td align="center" style="font-size:16px"><strong><u>Fabric Roll Receive</u></strong></td>
				</tr>
				<tr>
					<td align="center" style="font-size:18px"><strong><u>Receive
						Challan <? echo $txt_challan_no; ?></u></strong></td>
				</tr>
			</table>
			<br>
			<?
			$sql_data = sql_select("select  recv_number,receive_date,booking_id,company_id, booking_no, knitting_source, knitting_company, receive_date, challan_no,store_id,location_id, yarn_issue_challan_no, remarks from inv_receive_master where id=$update_id and status_active=1 and is_deleted=0 and company_id=$company ");

			?>


			<table width="1210" cellspacing="0" align="center" border="0">
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Delivery Challan</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('booking_no')]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Company</td>
					<td width="200" align=""><? echo $company_array[$company]['name']; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Store Name</td>
					<td width="200">:&nbsp;<? echo $store_arr[$sql_data[0][csf('store_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Challan No</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Location</td>
					<td width="200" id="" align=""><? echo $location_arr[$sql_data[0][csf('location_id')]]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Knitting Source</td>
					<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Knitting Com</td>
					<td width="200">:&nbsp;
						<?
						if ($sql_data[0][csf('knitting_source')] == 1) echo $company_arr[$sql_data[0][csf('knitting_company')]];
						else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
						?>
					</td>

					<td style="font-size:16px; font-weight:bold;" width="150">Yarn Issue Ch. No</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('yarn_issue_challan_no')]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Remarks</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="100">Recevied Date</td>
					<td width="100">:&nbsp; <? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
					<td width="" id="barcode_img_id" colspan="2"></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="90">Order No</th>
						<th width="60">Buyer <br> Job</th>
						<th width="100">System ID</th>
						<th width="70">Knitting Company</th>
						<th width="50">Yarn Count</th>
						<th width="70">Yarn Brand</th>
						<th width="60">Lot No</th>
						<th width="70">Fab Color</th>
						<th width="70">Color Range</th>
						<th width="150">Fabric Type</th>
						<th width="50"> GSM</th>
						<th width="40">Fab. Dia</th>
						<th width="60">Machine No</th>
						<th width="60">No of Roll</th>
						<th>QC Pass Qty</th>
					</tr>
				</thead>
				<?

				/*$data_array=sql_select("SELECT   a.id,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
				and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 order by c.id desc");
				$roll_details_array=array(); $barcode_array=array();
				foreach($data_array as $row)
				{
				$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
				$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
				$roll_details_array[$row[csf("barcode_no")]]['stitch_length']=$row[csf("stitch_length")];
				}
				//print_r($roll_details_array);*/


				$i = 1;
				$tot_qty = 0;
				$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

				$data_array_mst = sql_select("SELECT a.booking_no,a.receive_basis,a.knitting_source, a.knitting_company,b.prod_id, b.febric_description_id,
				b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.machine_no_id,b.yarn_count,b.color_id,b.gsm,
				b.color_range_id, count(c.id) as number_of_roll,c.po_breakdown_id,c.barcode_no, sum(c.qc_pass_qnty) as qnty
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
				WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 and a.id=$update_id and c.status_active=1 and
				c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=0
				group by a.booking_no,a.receive_basis,a.knitting_source, a.knitting_company,  b.prod_id, b.febric_description_id,b.gsm,
				b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id,c.po_breakdown_id,c.barcode_no");
				foreach ($data_array_mst as $row)
				{
					$ref_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
				}
				// =========================================
				$ref_barcode_nos = implode(",", $ref_barcode_arr);
				$barCond = $ref_barcode_no_cond = "";
				if($db_type==2 && count($ref_barcode_arr)>999)
				{
					$ref_barcode_arr_chunk=array_chunk($ref_barcode_arr,999) ;
					foreach($ref_barcode_arr_chunk as $chunk_arr)
					{
						$barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
					}

					$ref_barcode_no_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$ref_barcode_no_cond=" and c.barcode_no in($ref_barcode_nos)";
				}
				$data_array = sql_select("SELECT   a.id,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length,c.booking_without_order as without_order
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 $ref_barcode_no_cond and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) order by c.id desc");
				$roll_details_array = array();
				$barcode_array = array();
				foreach ($data_array as $row)
				{
					$roll_details_array[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
					$roll_details_array[$row[csf("barcode_no")]]['without_order'] = $row[csf("without_order")];
				}
				foreach ($data_array_mst as $row)
				{
					$knit_company = "&nbsp;";
					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}

					$count = '';
					$yarn_count = explode(",", $row[csf('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
					}

					if ($row[csf('receive_basis')] == 1) {
						$booking_no = explode("-", $row[csf('booking_no')]);
						$prog_book_no = (int)$booking_no[3];
					} else $prog_book_no = $row[csf('booking_no')];
					$without_order = $roll_details_array[$row[csf("barcode_no")]]['without_order'];
					if ($without_order == 1) {

						$data_none_array = sql_select("SELECT   a.booking_no,a.buyer_id
							FROM wo_non_ord_samp_booking_mst a WHERE a.booking_no='" . $roll_details_array[$row[csf("barcode_no")]]['booking_no'] . "'  and a.status_active=1 and a.is_deleted=0 order by a.booking_no desc");
						$buyer = $data_none_array[0][csf("buyer_id")];
						$job_sys = $data_none_array[0][csf("booking_no")];
						$job_buyer = $buyer_array[$buyer] . '<br>' . $job_sys;
						$po_num = '';
					} else {
						$po_num = $job_array[$row[csf('po_breakdown_id')]]['po'];
						$job_buyer = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']] . "<br>" . $job_array[$row[csf('po_breakdown_id')]]['job'];
								//$file=$job_array[$row[csf('po_breakdown_id')]]['file_no'];
					}
					?>
					<tr>
						<td width="30"><? echo $i; ?></td>
						<td width="90" style="word-break:break-all;"><? echo $po_num;;//$job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
						<td width="60">
						<? echo $job_buyer;//;$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job'];
						?>
						</td>
						<td width="100" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
						<td width="70"><? echo $knit_company; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
						<td width="150"
						style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;"
						align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;"
						align="center"><? echo $row[csf('width')]; ?></td>
						<td width="60" style="word-break:break-all;"
						align="center"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
						<td width="60" align="center"><? echo $row[csf('number_of_roll')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
					</tr>
					<?
					$tot_qty += $row[csf('qnty')];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="15"><strong>Total</strong></td>
					<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				</tr>

			</table>
		</div>
	<? echo signature_table(16, $company, "1210px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}
?>