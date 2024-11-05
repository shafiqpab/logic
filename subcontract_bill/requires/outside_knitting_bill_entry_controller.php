<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
//print_r ($data[0]);
//echo session_id();
$action = $_REQUEST['action'];
if ($action == "print_button_variable_setting") {

	$print_report_format = 0;
	$print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $data . "' and module_id=8 and report_id=268 and is_deleted=0 and status_active=1");
	$printButton = explode(',', $print_report_format);

	echo "$('#printb1').hide();";
	echo "$('#printb2').hide();";

	foreach ($printButton as $id) {
		if ($id == 143) {
			echo "$('#printb1').show();";
		}
		if ($id == 777) {
			echo "$('#printb2').show();";
		}
	}

	exit();
}
if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 145, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "--Select Location--", $selected, "", "", "", "", "", "", 3);
	exit();
}
if ($action == "load_drop_down_party_name") {
	$data = explode('_', $data);

	if ($data[1] == 3) {
		echo create_drop_down("cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (9,20) ) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "", "", "", "", "", "", 5);
	} else if ($data[1] == 1) {
		$party_arr = return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id", "company_name");
		$value = 1;
		if (count($party_arr) == 1) {
			$value = 0;
		}
		echo create_drop_down("cbo_supplier_company", 150, $party_arr, "", 1, "-- Select Party --", "", "load_drop_down( 'requires/outside_knitting_bill_entry_controller', this.value, 'load_drop_down_party_location', 'party_location_td');", "", "", "", "", "", 5);
	} else {
		echo create_drop_down("cbo_supplier_company", 150, $blank_array, "", 1, "-- Select Party --", $selected, "", 0, "", "", "", "", 5);
	}
	exit();
}

if ($action == "load_drop_down_supplier_name") {
	//echo "select sup.id, sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (9,20) ) order by supplier_name";
	echo create_drop_down("cbo_supplier_company", 145, "select sup.id, sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (9,20) ) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "", "", "", "", "", "", 5);
	exit();
}
if ($action == "load_variable_settings") {

	$sql_result = sql_select("select dyeing_fin_bill,variable_list,allow_per from  variable_settings_subcon where company_id='$data' and variable_list in(18) order by id");
	foreach ($sql_result as $result) {

		if ($result[csf("variable_list")] == 18) {
			if ($result[csf("dyeing_fin_bill")] == 2) //Outside Knitting Bill
			{
				echo "$('#mandatory_check').val(" . $result[csf("allow_per")] . ");\n";
			}
		}
	}
	exit();
}



if ($action == "bill_no_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode, '', '');
	$ex_data = explode('_', $data);
?>
	<script>
		function js_set_value(id) {
			document.getElementById('outkintt_receive_id').value = id;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="knittingbill_1" id="knittingbill_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th width="150">Company Name</th>
						<th width="150">Supplier Name</th>
						<th width="80">Bill ID</th>
						<th width="80">Recv Challan No</th>
						<th width="80">Party Bill No</th>
						<th width="170">Date Range</th>
						<th width=""><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<input type="hidden" id="outkintt_receive_id">
								<? echo create_drop_down("cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $ex_data[0], "load_drop_down( 'outside_knitting_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td' );", 0); ?>
							</td>
							<td id="supplier_td"><? echo create_drop_down("cbo_supplier_company", 140, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=20) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $ex_data[1], "", "", "", "", "", "", 5); ?></td>
							<td><input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" /></td>
							<td><input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:75px" placeholder="Prefix No" /></td>
							<td><input type="text" name="txt_party_bill" id="txt_party_bill" class="text_boxes" style="width:75px" /></td>

							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_party_bill').value+'_'+document.getElementById('txt_challan').value, 'knitting_bill_list_view', 'search_div', 'outside_knitting_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1);  ?>
							</td>
						</tr>
					</tbody>
				</table>
				<div id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "knitting_bill_list_view") {
	$data = explode('_', $data);

	$challan_no = $data[6];
	if ($data[0] != 0) $company_name = " and a.company_id='$data[0]'";
	else {
		echo "Please Select Company First.";
		die;
	}
	if ($data[1] != 0) $party_name = " and a.supplier_id='$data[1]'";
	else $party_name = "";
	//if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $return_date=""; subcon_outbound_bill_mst

	if ($challan_no != "") $challan_no_cond = "and b.challan_no='$challan_no'";
	else $challan_no_cond = "";
	if ($db_type == 0) {
		if ($data[2] != "" &&  $data[3] != "") $return_date = "and a.bill_date between '" . change_date_format($data[2], 'yyyy-mm-dd') . "' and '" . change_date_format($data[3], 'yyyy-mm-dd') . "'";
		else $return_date = "";
	} else {
		if ($data[2] != "" &&  $data[3] != "") $return_date = "and a.bill_date between '" . change_date_format($data[2], "", "", 1) . "' and '" . change_date_format($data[3], "", "", 1) . "'";
		else $return_date = "";
	}
	if ($data[4] != '') $bill_id_cond = " and a.prefix_no_num='$data[4]'";
	else $bill_id_cond = "";
	if ($data[5] != '') $party_bill_cond = " and a.party_bill_no='$data[5]'";
	else $party_bill_cond = "";

	$company_id = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	$supplier_library_arr = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");

	$arr = array(2 => $location, 4 => $supplier_library_arr, 6 => $bill_for);
	if ($db_type == 0) {
		$year_cond = "year(a.insert_date)as year";
	} else if ($db_type == 2) {
		$year_cond = "TO_CHAR(a.insert_date,'YYYY') as year";
	}

	$sql = "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.supplier_id, a.bill_for, a.party_bill_no from subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and a.status_active=1 $company_name $party_name $return_date $bill_id_cond $party_bill_cond $challan_no_cond  group by  a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.supplier_id, a.bill_for, a.party_bill_no order by a.id Desc";

	echo  create_list_view("list_view", "Bill No,Year,Location,Bill Date,Supplier,Party Bill No,Bill For", "70,70,100,100,120,100,100", "730", "250", 0, $sql, "js_set_value", "id", "", 1, "0,0,location_id,0,supplier_id,0,bill_for", $arr, "prefix_no_num,year,location_id,bill_date,supplier_id,party_bill_no,bill_for", "outside_knitting_bill_entry_controller", "", '0,0,0,3,0,0,0');
	exit();
}

if ($action == "load_php_data_to_form_issue") {
	$sql = "SELECT min(receive_date) as min_date, max(receive_date) as max_date FROM subcon_outbound_bill_dtls WHERE mst_id=$data and process_id=2 and status_active=1 and is_deleted=0 group by mst_id";

	$sql_result_arr = sql_select($sql);
	$mindate = '';
	$maxdate = '';
	$mindate = $sql_result_arr[0][csf('min_date')];
	$maxdate = $sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	//echo change_date_format($mindate).'='.change_date_format($maxdate);
	$nameArray = sql_select("select id, bill_no, company_id, location_id, bill_date, supplier_id,party_source_id,party_id, bill_for,upcharge,discount, party_bill_no, is_posted_account from subcon_outbound_bill_mst where id='$data'");
	foreach ($nameArray as $row) {
		echo "document.getElementById('txt_bill_no').value 					= '" . $row[csf("bill_no")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "load_drop_down( 'requires/outside_knitting_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_bill_date').value 				= '" . change_date_format($row[csf("bill_date")]) . "';\n";
		//echo "load_drop_down('requires/outside_knitting_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier_name', 'supplier_td' );\n";
		
		echo "document.getElementById('cbo_bill_for').value					= '" . $row[csf("bill_for")] . "';\n";
		echo "document.getElementById('txt_party_bill_no').value			= '" . $row[csf("party_bill_no")] . "';\n";
		echo "document.getElementById('txt_bill_form_date').value 			= '" . change_date_format($mindate) . "';\n";
		echo "document.getElementById('txt_bill_to_date').value 			= '" . change_date_format($maxdate) . "';\n";
		echo "document.getElementById('cbo_party_source').value				= '" . $row[csf("party_source_id")] . "';\n";
		echo "load_drop_down( 'requires/outside_knitting_bill_entry_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n";
		//echo "document.getElementById('cbo_party_name').value				= '" . $row[csf("party_id")] . "';\n";
		echo "document.getElementById('cbo_supplier_company').value			= '" . $row[csf("supplier_id")] . "';\n";

		echo "document.getElementById('update_id').value            		= '" . $row[csf("id")] . "';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_supplier_company*cbo_bill_for',1);\n";
		echo "document.getElementById('txt_upcharge').value				= '" . $row[csf("upcharge")] . "';\n";
		echo "document.getElementById('txt_discount').value				= '" . $row[csf("discount")] . "';\n";
		echo "document.getElementById('hidden_acc_integ').value				= '" . $row[csf("is_posted_account")] . "';\n";
		if ($row[csf("is_posted_account")] == 1) {
			echo "$('#accounting_integration_div').text('All Ready Posted in Accounting.');\n";
		} else {
			echo "$('#accounting_integration_div').text('');\n";
		}
	}
	unset($nameArray);
	exit();
}

if ($action == "wo_num_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, '', 1, '');
	$data = explode('_', $data);
?>
	<script>
		function js_set_value(id) {
			document.getElementById('hidd_item_id').value = id;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<form name="searchpofrm" id="searchpofrm">
			<input type="hidden" id="hidd_item_id" />
			<div style="width:100%;">
				<table cellspacing="0" border="1" cellpadding="0" rules="all" width="100%" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th width="140" align="center">Wo No</th>
						<th width="140" align="center">Supplier id </th>
						<th width="100" align="center">Color</th>
						<th width="100" align="center">Construction</th>
						<th width="100" align="center">Copmposition</th>
						<th width="50" align="center">GSM</th>
						<th width="50" align="center">Dia</th>
						<th width="50" align="center">Rate</th>
						<th width="" align="center">Uom</th>
					</thead>
				</table>
			</div>
			<div style="width:100%;max-height:380px; overflow-y:scroll" id="sewing_production_list_view" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="table_body">
					<?php
					$supplier_library_arr = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
					$color_library_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");
					$i = 1;
					//wo_non_ord_knitdye_booking_mst
					$bill_for = $data[3];
					//echo $data[2].'ddd'.$bill_for;
					if ($data[2] != "" and $bill_for == 3) {
						$wo_rec_idcond = "and c.id in($data[2])";
						$entry_form_no = return_field_value("entry_form", "inv_receive_master ", " id in($data[2]) and status_active=1 and is_deleted=0", "entry_form");
						//echo $entry_form_no.'D';


						if ($entry_form_no == 58) {
							$booking_no = return_field_value("booking_no", "inv_receive_master ", " id in($data[2]) and status_active=1 and is_deleted=0", "booking_no");
							$sql_roll = sql_select("SELECT
					       listagg(cast(c.barcode_no as varchar2(4000)),',') within group (order by c.barcode_no) AS barcode_no
					  FROM pro_grey_prod_delivery_mst a,
					       pro_grey_prod_delivery_dtls b,
					       pro_roll_details c
					 WHERE     a.id = b.mst_id
					       AND b.id = c.dtls_id
					       AND b.mst_id = c.mst_id
					       AND b.order_id = c.po_breakdown_id
					       AND c.status_active = 1
					       AND c.status_active = 1
					       AND a.status_active = 1
					       AND b.status_active = 1
					       AND a.is_deleted = 0
					       AND b.is_deleted = 0
					       AND a.entry_form = 56
					       AND c.entry_form = 56
					       and A.SYS_NUMBER='$booking_no'
					 ");

							$roll_nos = '';
							if (count($sql_roll)) {
								$roll_nos = $sql_roll[0][csf('barcode_no')];
							}
							$booking_no = "";

							$barcode_no_cond = where_con_using_array(array_unique(explode(",", $roll_nos)), 0, "c.barcode_no");
							$sql_booking = sql_select("SELECT 
					        listagg(cast(a.service_booking_no as varchar2(4000)),',') within group (order by a.service_booking_no) AS booking_no
					  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					 WHERE     a.id = b.mst_id
					       AND b.id = c.dtls_id
					       AND a.entry_form = 2
					       AND c.entry_form = 2
					       AND c.status_active = 1
					       AND c.is_deleted = 0
					       $barcode_no_cond ");

							if (count($sql_booking)) {
								$booking_no = $sql_booking[0][csf('booking_no')];
							}

							$booking_cond = "";

							$booking_cond = where_con_using_array(array_unique(explode(",", $booking_no)), 1, "a.booking_no");

							$sql_wo = "select a.id,a.booking_no,a.supplier_id,b.gmts_color as gmts_color_id,b.rate,b.fab_des_id as fab_des_id,b.gsm as gsm_weight,b.dia as dia_width,b.uom,b.process_id as process,a.currency_id from wo_non_ord_knitdye_booking_mst a,wo_non_ord_knitdye_booking_dtl b where a.id=b.mst_id  and a.company_id=$data[0] and a.supplier_id=$data[1] and b.process_id in (1,31,32,33,34,35,36,37,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88) $booking_cond";
						}
					} else if ($data[2] != "" and $bill_for != 3) {
						$wo_rec_idcond = "and c.id in($data[2])";
						$entry_form_no = return_field_value("entry_form", "inv_receive_master ", " id in($data[2]) and status_active=1 and is_deleted=0", "entry_form");
						//echo $entry_form_no.'D';

						if ($entry_form_no == 58) {
							$booking_no = return_field_value("booking_no", "inv_receive_master ", " id in($data[2]) and status_active=1 and is_deleted=0", "booking_no");
							$sql_roll = sql_select("SELECT
					       listagg(cast(c.barcode_no as varchar2(4000)),',') within group (order by c.barcode_no) AS barcode_no
					  FROM pro_grey_prod_delivery_mst a,
					       pro_grey_prod_delivery_dtls b,
					       pro_roll_details c
					 WHERE     a.id = b.mst_id
					       AND b.id = c.dtls_id
					       AND b.mst_id = c.mst_id
					       AND b.order_id = c.po_breakdown_id
					       AND c.status_active = 1
					       AND c.status_active = 1
					       AND a.status_active = 1
					       AND b.status_active = 1
					       AND a.is_deleted = 0
					       AND b.is_deleted = 0
					       AND a.entry_form = 56
					       AND c.entry_form = 56
					       and A.SYS_NUMBER='$booking_no'
					 ");
							$roll_nos = '';
							if (count($sql_roll)) {
								$roll_nos = $sql_roll[0][csf('barcode_no')];
							}
							$booking_no = "";

							$barcode_no_cond = where_con_using_array(array_unique(explode(",", $roll_nos)), 0, "c.barcode_no");
							$sql_booking = sql_select("SELECT 
					        listagg(cast(a.service_booking_no as varchar2(4000)),',') within group (order by a.service_booking_no) AS booking_no
					  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					 WHERE     a.id = b.mst_id
					       AND b.id = c.dtls_id
					       AND a.entry_form = 2
					       AND c.entry_form = 2
					       AND c.status_active = 1
					       AND c.is_deleted = 0
					       $barcode_no_cond ");
							if (count($sql_booking)) {
								$booking_no = $sql_booking[0][csf('booking_no')];
							}

							$booking_cond = "";

							$booking_cond = where_con_using_array(array_unique(explode(",", $booking_no)), 1, "a.booking_no");

							$sql_wo = "select a.id,a.booking_no,a.supplier_id,b.color_size_table_id,b.gmts_color_id,b.rate,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.uom,b.process,a.currency_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$data[0] and a.supplier_id=$data[1] and b.process in (1,31,32,33,34,35,36,37,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88) $booking_cond";
						} else if ($data[3] == 4) // FSO ////knitting_work_order_mst
						{
							$sql_wo = "select a.id,a.wo_no as booking_no,a.supplier_id,b.color_range as gmts_color_id,b.rate,b.fabric_desc as construction,a.currency_id from knitting_work_order_mst a,knitting_work_order_dtls b,fabric_sales_order_mst c where a.id=b.mst_id  and b.fabric_sales_order_no=c.job_no   and a.company_id=$data[0] and a.supplier_id=$data[1] and c.id=$data[4]";
						}
					} else {
						$sql_wo = "select a.id,a.booking_no,a.supplier_id,b.color_size_table_id,b.gmts_color_id,b.rate,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.uom,b.process,a.currency_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$data[0] and a.supplier_id=$data[1] and b.process in (1,31,32,33,34,35,36,37,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88)";
					}



					// echo $sql_wo;
					//echo "select a.id,a.booking_no,a.supplier_id,b.color_size_table_id,b.rate,b.construction,b.copmposition,b.gsm_weight,b.dia_width,b.uom,b.process from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.supplier_id=$data[1] and b.process in (31,32,33,34,35,36,37,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88)  $wo_rec_idcond";

					$sql_result = sql_select($sql_wo);
					foreach ($sql_result as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						if ($data[3] == 4) //FSO-------
						{
							$fabric_descriptionArr = explode(",", $row[csf('construction')]);
							$fabric_description = $fabric_descriptionArr[0];
							$copmposition_val = $fabric_descriptionArr[1];
						} else {
							$fab_des_id = $row[csf('fab_des_id')];
							$fabric_description = return_field_value("fabric_description", "wo_non_ord_samp_booking_dtls ", " id=$fab_des_id and status_active=1 and is_deleted=0", "fabric_description");
							$copmposition_val = $row[csf('copmposition')];
						}



					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')] . "_" . $row[csf('booking_no')] . "_" . $row[csf('rate')] . "_" . $row[csf('currency_id')]; ?>');">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="140" align="center">
								<p><? echo $row[csf('booking_no')]; ?></p>
							</td>
							<td width="140" align="center"><? echo $supplier_library_arr[$row[csf('supplier_id')]]; ?></td>
							<td width="100" align="center"><? echo $color_library_arr[$row[csf('gmts_color_id')]]; ?></td>
							<td width="100" align="center"><? echo $fabric_description; ?></td>
							<td width="100" align="center"><? echo $copmposition_val; ?></td>
							<td width="50" align="center"><? echo $row[csf('gsm_weight')]; ?></td>
							<td width="50" align="center"><? echo $row[csf('dia_width')]; ?></td>
							<td width="50" align="center"><? echo $row[csf('rate')]; ?></td>
							<td width="" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
			</div>
		</form>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		//setFilterGrid("table_body",-1);
	</script>

	</html>
<?
	exit();
}

if ($action == "knitting_entry_list_view") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode, 1, '');
	$from = 1;

	$data = explode('***', $data);
	$ex_bill_for = $data[2];
	$supplier_id = $data[3];
	//echo $supplier_id.'DD';
	$date_from = $data[4];
	$date_to = $data[5];
	$update_id = $data[6];
	//$str_data=$data[7];

	$job_id = $data[7];
	if ($job_id) {
		$po_ids = "";
		$po_sql = "SELECT b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.id=$job_id and a.status_active=1 and b.status_active=1";
		foreach (sql_select($po_sql) as $value) {
			$po_ids .= $value[csf("id")] . ",";
		}
		$po_ids = chop($po_ids, ",");
	}

	//echo $str_data.'kausar';
	$date_cond = "";$date_cond2 = "";$date_cond3 = "";
	if ($db_type == 0) {
		if ($date_from != "" &&  $date_to != "") $date_cond = "and a.receive_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
		else $date_cond = "";
		$year_cond = "year(b.insert_date)as year";
	} else if ($db_type == 2) {
		if ($date_from != "" &&  $date_to != "") $date_cond = "and a.receive_date between '" . change_date_format($date_from, "", "", 1) . "' and '" . change_date_format($date_to, "", "", 1) . "'";
		else $date_cond = "";
		$year_cond = "TO_CHAR(b.insert_date,'YYYY') as year";
		//$date_cond2= "and e.delevery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
		if ($date_from != "" &&  $date_to != "") $date_cond2 = "and e.delevery_date between '" . change_date_format($date_from, "", "", 1) . "' and '" . change_date_format($date_to, "", "", 1) . "'";
		if ($date_from != "" &&  $date_to != "") $date_cond3 = "and g.delevery_date between '" . change_date_format($date_from, "", "", 1) . "' and '" . change_date_format($date_to, "", "", 1) . "'";
		
	}

	//print_r ($ex_bill_for);
	if ($ex_bill_for == 3) $tbl_wight = "920";
	else $tbl_wight = "1200";

	$nameArray_vari= sql_select("select id, dyeing_fin_bill, allow_per,variable_list from variable_settings_subcon where company_id='$data[0]' and variable_list=7    order by id");
				//echo "select id, dyeing_fin_bill, allow_per,variable_list from variable_settings_subcon where company_id='$data[3]' and variable_list=7    order by id";
		$in_house_knit_bill_from=0; // Subcon Variable-In House Knit Bill From
	foreach($nameArray_vari as $row)
	{
		$in_house_knit_bill_from=$row[csf('dyeing_fin_bill')];
	}
	if($in_house_knit_bill_from==0) $in_house_knit_bill_from=1;
	//echo $in_house_knit_bill_from.'D=';
?>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight; ?>px" class="rpt_table">
					<thead>
						<?
						if ($ex_bill_for == 3) {
						?>
							<th width="40">&nbsp;</th>
							<th width="30">SL</th>
							<th width="60">Sys. Challan</th>
							<th width="70">Rec. Challan</th>
							<th width="70">Receive Date</th>
							<th width="90">Body Part</th>
							<th width="160">Fabric Description</th>
							<th width="60">Color Type</th>
							<th width="80">Receive Qty</th>
							<th width="60">No Of Roll</th>
							<th width="100">Style Ref. No</th>
							<th>Buyer</th>
						<?
						} else {
						?>
							<th width="40">&nbsp;</th>
							<th width="30">SL</th>
							<th width="60">Sys. Challan</th>
							<th width="70">Rec. Challan</th>
							<th width="70">Recive Date</th>
							<th width="60">No Of Roll</th>
							<th width="180">Fabric Description</th>
							<th width="80">Body Part</th>
							<th width="40">UOM</th>
							<th width="60">Receive Qty</th>
							<th width="100"> <? if ($ex_bill_for == 3) echo "Order No";
												else echo "FSO No"; ?> </th>
							<th width="100">Style Ref. No</th>
							<th width="100">Job No</th>
							<th width="100">Buyer</th>
							<th width="100">Internal Ref.</th>
						<? } ?>
					</thead>
				</table>
			</div>
			<div style="width:<? echo $tbl_wight; ?>px;max-height:180px; overflow-y:scroll" id="kintt_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight - 20; ?>px" class="rpt_table" id="tbl_list_search">
					<? 
					$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
					$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
					$item_id_arr = return_library_array("select id,product_name_details from  product_details_master", 'id', 'product_name_details');
					//$roll_no_arr=return_library_array( "select id, no_of_roll from pro_grey_prod_entry_dtls",'id','no_of_roll');
					if ($ex_bill_for != 3) {
						$po_array = array();
						$sql_po = "Select a.job_no, a.job_no_prefix_num, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 "; //and b.status_active=1 and b.is_deleted=0
						$sql_po_result = sql_select($sql_po);
						foreach ($sql_po_result as $row) {
							$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_prefix_num')];
							$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
							$po_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
							$po_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
							$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
						}
						unset($sql_po_result);
					}

					$bill_qty_array = array();
					//$sql_bill="SELECT id as upd_id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, body_part_id, febric_description_id, receive_qty, uom, 0 as dtls_id, rate, amount, remarks FROM subcon_outbound_bill_dtls WHERE status_active=1 and is_deleted=0 and process_id=2";
					$sql_bill = "SELECT b.id as upd_id, b.mst_id, b.receive_id, b.receive_date, b.challan_no, b.order_id, b.item_id, b.body_part_id, b.febric_description_id, b.receive_qty, b.uom, 0 as dtls_id, b.rate, b.amount, b.remarks FROM subcon_outbound_bill_mst a join subcon_outbound_bill_dtls b on a.id=b.mst_id WHERE b.status_active=1 and b.is_deleted=0 and b.process_id=2 and a.entry_form<>438";

					$sql_bill_result = sql_select($sql_bill);
					$str_data = "";
					foreach ($sql_bill_result as $row) {
						$bill_qty_array[$row[csf('receive_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'] = $row[csf('receive_qty')];

						if ($row[csf('mst_id')] == $update_id) {
							if ($str_data == "") $str_data = $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $row[csf('item_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
							else $str_data .= '!!!!' . $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $row[csf('item_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
						}
					}
					unset($sql_bill_result);
					//print_r($bill_qty_array);	
					$i = 1;
					if ($db_type == 0) {
						$group_cond = " group by c.id, a.challan_no, c.po_breakdown_id";
					} else if ($db_type == 2) {
						$group_cond = " group by c.id, a.currency_id, a.challan_no, a.receive_date, a.recv_number, a.recv_number_prefix_num, a.buyer_id, b.prod_id, b.uom, c.po_breakdown_id, c.dtls_id";
					}

					$ex_str_data = explode("!!!!", $str_data);
					$str_arr = array();
					foreach ($ex_str_data as $str) {
						$str_arr[] = $str;
					}
					//print_r ($str_arr); 

					if ($ex_bill_for == 1) $bill_for_id = "Fb";
					else if ($ex_bill_for == 2) $bill_for_id = "SM";
					if ($ex_bill_for != 3) {
						if ($db_type == 0) {
							$year_cond = "year(a.insert_date)";
							$booking_without_order = "IFNULL(c.booking_without_order,0)";
							$barcode_no_cond = "group_concat(c.barcode_no)";
						} else if ($db_type == 2) {
							$year_cond = "TO_CHAR(a.insert_date,'YYYY')";
							$booking_without_order = "nvl(c.booking_without_order,0)";
							$barcode_no_cond = "rtrim(xmlagg(xmlelement(e,c.barcode_no,',').extract('//text()') order by c.barcode_no).GetClobVal(),',')";
						}

						$po_breakdown_id_conds = "";
						if ($job_id) {
							$po_breakdown_id_conds = " and c.po_breakdown_id in ($po_ids)";
							$date_cond = "";
						}


						//echo $sql; //die;
						   //echo $in_house_knit_bill_from.'D';die;
						if($in_house_knit_bill_from==1 || $in_house_knit_bill_from==3)   //From in house KNITTING Bill from -Prod Type Auto fabric yes
						{
						if ($ex_bill_for == 1) {
//
							  $sql = "select a.id as id, a.recv_number, a.recv_number_prefix_num, a.challan_no, a.entry_form, a.receive_date, a.booking_id as prog_id, a.booking_no as prog_no, b.body_part_id, a.buyer_id, a.receive_basis, a.knitting_source, a.booking_without_order, b.febric_description_id, max(b.gsm) as gsm, max(b.width) as dia_width, b.prod_id, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as dtls_id, c.po_breakdown_id, sum(c.quantity) as quantity,sum(c.quantity_pcs) as grey_receive_qnty_pcs, d.po_number, e.job_no, e.style_ref_no, e.job_no_prefix_num, c.is_sales, to_char(e.insert_date,'YYYY') as job_year, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.entry_form in(2,58) and c.entry_form in(2,58) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id!=0 and c.trans_id>0 and c.is_sales=0 and a.location_id='$data[1]' and a.company_id='$data[0]' and a.KNITTING_COMPANY=$supplier_id $date_cond $po_breakdown_id_conds  group by a.id, a.recv_number, a.recv_number_prefix_num, a.challan_no, a.entry_form, b.febric_description_id, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source, b.body_part_id,a.booking_without_order,b.prod_id,c.po_breakdown_id ,d.po_number,e.job_no,e.style_ref_no,e.job_no_prefix_num,c.is_sales,e.insert_date, a.booking_id order by e.style_ref_no,a.id ";
							$sql_result = sql_select($sql);
						} else if ($ex_bill_for == 4) { //FSO for Service

							 $sql = "select a.id as id, a.recv_number, a.recv_number_prefix_num, a.challan_no, a.entry_form, a.receive_date, a.booking_id as prog_id, a.booking_no as prog_no, b.body_part_id, a.buyer_id, a.receive_basis, a.knitting_source, a.booking_without_order, sum(b.no_of_roll) as no_of_roll,b.febric_description_id, max(b.gsm) as gsm, max(b.width) as dia_width, b.prod_id, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as dtls_id, c.po_breakdown_id, sum(c.quantity) as quantity,sum(c.quantity_pcs) as grey_receive_qnty_pcs, d.po_buyer,d.po_job_no,d.job_no as po_number,d.sales_booking_no, d.job_no, d.style_ref_no, d.job_no_prefix_num, c.is_sales, to_char(d.insert_date,'YYYY') as job_year, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id  and a.entry_form=2 and c.entry_form=2 and c.trans_id>0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id!=0 and c.is_sales=1 and a.location_id='$data[1]' and a.company_id='$data[0]'  and a.KNITTING_COMPANY=$supplier_id $date_cond $po_breakdown_id_conds  group by a.id, a.recv_number, a.recv_number_prefix_num, a.challan_no, a.entry_form, b.febric_description_id, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source, b.body_part_id,a.booking_without_order,b.prod_id,c.po_breakdown_id ,d.po_buyer,d.po_job_no,d.job_no,d.sales_booking_no,d.style_ref_no,d.job_no_prefix_num,c.is_sales,d.insert_date, a.booking_id order by d.style_ref_no,a.id ";
							$sql_result = sql_select($sql);
						} else {
								$sql = "select a.id, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.entry_form, b.prod_id, b.body_part_id, b.febric_description_id, count(c.id) as roll_qty, c.po_breakdown_id, sum(c.qc_pass_qnty) as quantity, a.receive_basis, a.booking_no, a.booking_id, $barcode_no_cond as barcode_no,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
								where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.company_id='$data[0]' and a.knitting_company='$supplier_id' and a.location_id='$data[1]' and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0 and $booking_without_order=0 and a.no_bill !=1
								and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $po_breakdown_id_conds
								group by a.id, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.entry_form, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id order by a.id";
							$sql_result = sql_select($sql);
						}
					}
					else{ //Fabric Delivery to Store setting 

						if ($ex_bill_for == 1) {

							$sql = "select g.id as id, g.sys_number as recv_number, g.sys_number_prefix_num as recv_number_prefix_num, f.grey_sys_number as challan_no, g.entry_form, g.delevery_date as receive_date, a.booking_id as prog_id, a.booking_no as prog_no, b.body_part_id, a.buyer_id, a.receive_basis, a.knitting_source, a.booking_without_order, b.febric_description_id, max(b.gsm) as gsm, max(b.width) as dia_width, b.prod_id, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as dtls_id, c.po_breakdown_id, sum(c.quantity) as quantity,sum(c.quantity_pcs) as grey_receive_qnty_pcs, d.po_number, e.job_no, e.style_ref_no, e.job_no_prefix_num, c.is_sales, to_char(e.insert_date,'YYYY') as job_year, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e,pro_grey_prod_delivery_mst g,pro_grey_prod_delivery_dtls f  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id  and g.id=f.mst_id and f.grey_sys_id=a.id and f.order_id=d.id and f.order_id=c.po_breakdown_id and a.entry_form=2 and c.entry_form=2 and g.entry_form in(56,53) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id!=0 and c.is_sales=0 and a.location_id='$data[1]' and a.company_id='$data[0]' and g.company_id='$data[0]' and g.knitting_company='$data[3]' $date_cond3 $po_breakdown_id_conds  group by g.id, g.sys_number, g.sys_number_prefix_num, f.grey_sys_number, g.entry_form, b.febric_description_id, g.delevery_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source, b.body_part_id,a.booking_without_order,b.prod_id,c.po_breakdown_id ,d.po_number,e.job_no,e.style_ref_no,e.job_no_prefix_num,c.is_sales,e.insert_date, a.booking_id order by e.style_ref_no,a.id ";
							$sql_result = sql_select($sql);
						} else if ($ex_bill_for == 4) { //FSO for Service

							   $sql = "select e.id as id, e.sys_number as recv_number, e.sys_number_prefix_num as recv_number_prefix_num, f.grey_sys_number as challan_no,e.entry_form, e.delevery_date as receive_date, a.booking_id as prog_id, a.booking_no as prog_no, b.body_part_id, a.buyer_id, a.receive_basis, a.knitting_source, a.booking_without_order, sum(b.no_of_roll) as no_of_roll,b.febric_description_id, max(b.gsm) as gsm, max(b.width) as dia_width, b.prod_id, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as dtls_id, c.po_breakdown_id, sum(f.current_delivery) as quantity,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs, d.po_buyer,d.po_job_no,d.job_no as po_number,d.sales_booking_no, d.job_no, d.style_ref_no, d.job_no_prefix_num, c.is_sales, to_char(d.insert_date,'YYYY') as job_year, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d,pro_grey_prod_delivery_mst e,pro_grey_prod_delivery_dtls f where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and e.id=f.mst_id and f.grey_sys_id=a.id and f.order_id=d.id  and c.po_breakdown_id=d.id and a.id=c.mst_id and f.roll_id=c.id and a.entry_form=2 and c.entry_form=2   and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and c.po_breakdown_id!=0 and c.is_sales=1 and a.location_id='$data[1]' and a.company_id='$data[0]' and e.knitting_company='$data[3]' and e.company_id='$data[0]'  and e.entry_form in(56,53)
							   $po_breakdown_id_conds $date_cond2  group by e.id, e.sys_number, e.sys_number_prefix_num, f.grey_sys_number, e.entry_form, b.febric_description_id, e.delevery_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source, b.body_part_id,a.booking_without_order,b.prod_id,c.po_breakdown_id ,d.po_buyer,d.po_job_no,d.job_no,d.sales_booking_no,d.style_ref_no,d.job_no_prefix_num,c.is_sales,d.insert_date, a.booking_id order by d.style_ref_no,e.id ";
							// $sql=" select a.id, a.entry_form, a.sys_number_prefix_num as recv_number_prefix_num, b.grey_sys_number as challan_no, a.delevery_date as receive_date, b.product_id as prod_id, 0 as body_part_id, b.determination_id as febric_description_id, sum(b.roll) as roll_qty, b.order_id as po_breakdown_id, sum(b.current_delivery) as quantity,sum(b.current_delivery_qnty_in_pcs) as qnty_pcs,sum(d.qc_pass_qnty_pcs) as quantity_pcs, 0 as rate, 0 as amount, c.receive_basis, c.booking_no, c.booking_id,b.size_coller_cuff, e.po_buyer,e.po_job_no,e.job_no as po_number,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num, d.is_sales from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c,pro_roll_details d,fabric_sales_order_mst e where a.id=b.mst_id and b.grey_sys_id=c.id and c.id=d.mst_id and b.roll_id=d.id and b.order_id=e.id and d.po_breakdown_id=e.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2  and c.knitting_source=1 and a.company_id='$data[3]' and c.knitting_company='$data[0]' and c.location_id='$data[1]'   and a.entry_form in(56,53) and c.entry_form=2 and c.receive_basis in (2,4,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond2 $man_challan_cond $po_breakdown_id_conds $sys_challan_cond group by a.id, a.entry_form, a.sys_number_prefix_num, c.receive_basis,b.grey_sys_number, a.delevery_date, b.product_id, b.determination_id, b.order_id, c.booking_no, c.booking_id,b.size_coller_cuff,e.po_buyer,e.po_job_no,e.job_no,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num, d.is_sales order by a.sys_number_prefix_num ASC";

							$sql_result = sql_select($sql);
						} else {
							$sql = "select a.id, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.entry_form, b.prod_id, b.body_part_id, b.febric_description_id, count(c.id) as roll_qty, c.po_breakdown_id, sum(c.qc_pass_qnty) as quantity, a.receive_basis, a.booking_no, a.booking_id, $barcode_no_cond as barcode_no,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.company_id='$data[0]' and a.knitting_company='$data[3]' and a.location_id='$data[1]' and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0 and $booking_without_order=0 and a.no_bill !=1
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $po_breakdown_id_conds
					group by a.id, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.entry_form, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id order by a.id";
							$sql_result = sql_select($sql);
						}
					}


						$bookingNo_arr = array();
						$knit_recMstId_arr = array();
						$productionId = "";
						$is_production = 0;
						$receiveId = "";
						$is_receive = 0;
						$deliveryId = "";
						$is_delivery = 0;
						$poId = "";
						$rollMstId = "";
						$dtls_id = "";
						$recBasis_arr = array();
						foreach ($sql_result as $row) {

							$bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
							$knit_recMstId_arr[$row[csf('id')]] = $row[csf('id')];
							if (isset($row[csf('barcode_no')])) {
								if ($db_type == 2) $barcode_no .= $row[csf('barcode_no')]->load() . ',';
								else $barcode_no .= $row[csf('barcode_no')] . ',';
							}

							$order_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
						}
						//echo $barcode_no;
						//echo $is_production.'-'.$is_receive.'-'.$is_delivery;

						$bill_qty_array = array();
						$sql_bill = "SELECT id as upd_id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, body_part_id, febric_description_id, receive_qty, uom, 0 as dtls_id, rate, amount, remarks FROM subcon_outbound_bill_dtls WHERE status_active=1 and is_deleted=0 and process_id=2 " . where_con_using_array($order_arr, 0, 'order_id') . "";
						//echo $sql_bill; die;
						$sql_bill_result = sql_select($sql_bill);
						$str_data = "";
						foreach ($sql_bill_result as $row) {
							$bill_qty_array[$row[csf('receive_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'] = $row[csf('receive_qty')];

							if ($row[csf('mst_id')] == $update_id) {
								if ($str_data == "") $str_data = $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $row[csf('item_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
								else $str_data .= '!!!!' . $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $row[csf('item_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
							}
						}
						unset($sql_bill_result);


						$barcode_nos = chop($barcode_no, ',');
						$barcode_nosCond = "";
						$barcode_count = count(array_unique(explode(",", $barcode_nos)));
						if ($db_type == 2 && $barcode_count > 1000) {
							$barcode_nosCond = " and (";
							$barcode_nosArr = array_chunk(explode(",", $barcode_nos), 999);
							foreach ($barcode_nosArr as $ids) {
								$ids = implode(",", $ids);
								$barcode_nosCond .= " c.barcode_no in($ids) or";
							}
							$barcode_nosCond = chop($barcode_nosCond, 'or ');
							$barcode_nosCond .= ")";
						} else $barcode_nosCond = " and c.barcode_no in($barcode_nos)";

						if (count($knit_recMstId_arr) > 0) {
							$barcode_nosCond = where_con_using_array($knit_recMstId_arr, 0, 'a.id');;
						}


						if ($ex_bill_for != 4) // not for FSO
						{


							$sql_roll = "select a.id, a.receive_basis, a.booking_no, a.booking_id, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2) and c.entry_form in (2) and a.item_category=13 and a.receive_basis in (1,2) and b.trans_id=0 and $booking_without_order=0
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_nosCond";
							//echo $sql_roll;
							$sql_rollRes = sql_select($sql_roll);
							$planid = "";
							$is_plan = 0;
							$planBooking_arr = array();
							foreach ($sql_rollRes as $row) {
								if ($row[csf('receive_basis')] == 1) //WO/Booking Based
								{
									$bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
								}
								if ($row[csf('receive_basis')] == 2) //Kniting Plan
								{
									$is_plan = 1;
									$planid .= $row[csf('booking_id')] . ',';
									$planBooking_arr[$row[csf('booking_id')]]['prodPlan'] .= $row[csf('barcode_no')] . ',';
								}
							}
							unset($sql_rollRes);
						}
						if ($is_plan == 1) {
							$planIds = implode(",", array_filter(array_unique(explode(",", $planid))));
							$plan_idsCond = "";
							$plan_ids = count(explode(",", $planIds));
							if ($db_type == 2 && $plan_ids > 1000) {
								$plan_idsCond = " and (";
								$planIdsArr = array_chunk(explode(",", $planIds), 999);
								foreach ($planIdsArr as $ids) {
									$ids = implode(",", $ids);
									$plan_idsCond .= " b.id in($ids) or";
								}
								$plan_idsCond = chop($plan_idsCond, 'or ');
								$plan_idsCond .= ")";
							} else $plan_idsCond = " and b.id in($planIds)";

							$plan_booking_arr = array();
							$plan_bookingNo_arr = array();
							$knit_booking = "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 $plan_idsCond";
							$knit_booking_result = sql_select($knit_booking);
							foreach ($knit_booking_result as $row) {
								$exbarcodeNo = explode(",", chop($planBooking_arr[$row[csf('id')]]['prodPlan'], ','));
								foreach ($exbarcodeNo as $barcodeNo) {
									$bookingNo_arr[$barcodeNo] = $row[csf('booking_no')];
								}
								$plan_bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
							}
							unset($knit_booking_result);
						}

						$updateData_arr = array();
						$insertData_arr = array();
						foreach ($sql_result as $row) {
							$ProdIdArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
						}
						 
						$ProdId_cond=implode(",",$ProdIdArr);
						$item_id_arr = return_library_array("select id,product_name_details from  product_details_master where id in($ProdId_cond)", 'id', 'product_name_details');

						foreach ($sql_result as $row) {

							if (isset($row[csf('barcode_no')])) {
								if ($db_type == 2) $row[csf('barcode_no')] = $row[csf('barcode_no')]->load();
							}

							$booking_no = '';
							$is_sales = $row[csf('is_sales')];
							if ($row[csf('entry_form')] == 58) {
								$exbarcodeNo = array_unique(explode(",", $row[csf('barcode_no')]));
								foreach ($exbarcodeNo as $barcodeNo) {
									$booking_no = $bookingNo_arr[$barcodeNo];
								}
							} else if ($row[csf('entry_form')] == 22) {
								$booking_no = $bookingNo_arr[$row[csf('booking_id')]];
								if ($booking_no == "") $booking_no = $bookingNo_arr[$row[csf('id')]];
							} elseif ($row[csf('entry_form')] == 2) {
								//$booking_no=$bookingNo_arr[$row[csf('id')]];
								//if($booking_no=="") $booking_no=$bookingNo_arr[$row[csf('booking_id')]];
							}
							//echo $booking_no.'<br>';
							if ($row[csf('entry_form')] == 2) //Production
							{
								if ($row[csf('receive_basis')] == 2) {
									$booking_no = $plan_bookingNo_arr[$row[csf('booking_id')]];
									if ($booking_no == "") $booking_no = $bookingNo_arr[$row[csf('booking_id')]];
								} else {
									$booking_no = $bookingNo_arr[$row[csf('id')]];
									if ($booking_no == "") $booking_no = $bookingNo_arr[$row[csf('booking_id')]];
								}
								if ($ex_bill_for == 1) {
									if ($row[csf('receive_basis')] == 0) $independent = 4;
								}
								if ($ex_bill_for == 1) $bill_for_id = "Fb";
								else if ($ex_bill_for == 2 && $row[csf('receive_basis')] != 0) $bill_for_id = "SM";
							} else if ($row[csf('entry_form')] == 2 && $is_sales == 1) //===========FSO=============
							{

								$booking_no = $row[csf('sales_booking_no')];
								if ($ex_bill_for == 4) $bill_for_id = "Fb";
								else if ($ex_bill_for == 2 && $row[csf('receive_basis')] != 0) $bill_for_id = "SM";
							} else //22,58 entry form
							{
								if ($row[csf('entry_form')] == 22) {
									if ($ex_bill_for == 1) {
										if ($row[csf('receive_basis')] == 4) $independent = 4;
									}
									if ($ex_bill_for == 1) {
										if ($booking_no == "" && $row[csf('receive_basis')] == 9) $independent = 4;
									}
								} else {
									if ($ex_bill_for == 1) {
										if ($booking_no == "" && $row[csf('receive_basis')] == 10) $independent = 4;
									}
								}
								if ($ex_bill_for == 1) {
									$bill_for_id = "Fb";
									$bill_for_sb = "SB";
								} else if ($ex_bill_for == 2 && $row[csf('receive_basis')] != 4) $bill_for_id = "SM";
							}


							//echo $booking_no.'='.$bill_for_id.'<br>';
							$ex_booking = "";
							$str_val = "";
							if ($booking_no != '') $ex_booking = explode('-', $booking_no);
							else $ex_booking = "_";

							$all_value = $row[csf('recv_number_prefix_num')] . '_' . $row[csf('po_breakdown_id')] . '_' . $row[csf('prod_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
							$grey_receive_qnty_pcs = $row[csf('grey_receive_qnty_pcs')];
							if ($is_sales == 1) //========If Sales ============
							{
								$po_number = $row[csf('po_number')];
								$style_ref_no = $row[csf('style_ref_no')];
								$buyerId = $row[csf('po_buyer')];
								$job_no = $row[csf('po_job_no')];
							} else {
								$po_number = $po_array[$row[csf('po_breakdown_id')]]['po_number'];
								$style_ref_no = $po_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
								$buyerId = $po_array[$row[csf('po_breakdown_id')]]['buyer'];
								$job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
							}

							$bodyPartType=$bodyPartTypeArr[$row[csf('body_part_id')]];

							if (in_array($all_value, $str_arr) && $update_id != '') {

								$str_val = $row[csf('id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('recv_number_prefix_num')] . '_' . $row[csf('po_breakdown_id')] . '_' . $po_number . '_' . $style_ref_no . '_' . $buyer_arr[$buyerId] . '_' . $row[csf('no_of_roll')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $row[csf('quantity')] . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $grey_receive_qnty_pcs . '_' . $is_sales. '_' . $bodyPartType;

								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['qty'] += $row[csf('quantity')];
								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['rollQty'] += $row[csf('no_of_roll')];
								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['job_no'] = $job_no;
								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['po_number'] = $po_number;
								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['style_ref_no'] = $style_ref_no;
								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['buyer'] = $buyerId;
								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['is_sales'] = $is_sales;

								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['allval'] = $all_value;

								$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['commnData'] = $row[csf('recv_number_prefix_num')] . '**' . $row[csf('challan_no')] . '**' . $row[csf('receive_date')] . '**' . $row[csf('entry_form')] . '**' . $ex_booking[1] . '**' . $bill_for_id . '**' . $bill_for_sb . '**' . $independent . '**' . $str_val;
							} else {
								$bill_qty = $bill_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
								$avilable_qty = $row[csf('quantity')] - $bill_qty;

								if ($avilable_qty > 0) {
									//echo $str_val.'<br>';
									//$grey_receive_qnty_pcs=99;

									$str_val = $row[csf('id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('recv_number_prefix_num')] . '_' . $row[csf('po_breakdown_id')] . '_' . $po_number . '_' . $style_ref_no . '_' . $buyer_arr[$buyerId] . '_' . $row[csf('no_of_roll')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $avilable_qty . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $grey_receive_qnty_pcs . '_' . $is_sales. '_' . $bodyPartType;


									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['qty'] = $avilable_qty;
									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['rollQty'] += $row[csf('no_of_roll')];
									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['job_no'] = $job_no;
									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['po_number'] = $po_number;
									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['style_ref_no'] = $style_ref_no;
									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['buyer'] = $buyerId;
									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['is_sales'] = $is_sales;

									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['allval'] = $all_value;

									$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['commnData'] = $row[csf('recv_number_prefix_num')] . '**' . $row[csf('challan_no')] . '**' . $row[csf('receive_date')] . '**' . $row[csf('entry_form')] . '**' . $ex_booking[1] . '**' . $bill_for_id . '**' . $bill_for_sb . '**' . $independent . '**' . $str_val;
								}
							}
						}
						//echo "<pre>";
						//print_r($insertData_arr);

						foreach ($updateData_arr as $mstid => $mstdata) // for update row
						{
							foreach ($mstdata as $prod_id => $productdata) {
								foreach ($productdata as $body_part_id => $bodypartdata) {
									foreach ($bodypartdata as $febric_description_id => $fabdescdata) {
										foreach ($fabdescdata as $poid => $podata) {
											$quantity = $no_of_roll = $independent = 0;

											$quantity = $podata['qty'];
											$no_of_roll = $podata['no_of_roll'];

											$extraData = $all_value = $recv_number_prefix_num = $challan_no = $receive_date = $entry_form = $bookingpref = $bill_for_id = $bill_for_sb = $independent = $str_val = '';
											$all_value = $podata['allval'];
											$extraData = explode("**", $podata['commnData']);

											$recv_number_prefix_num = $extraData[0];
											$challan_no = $extraData[1];
											$receive_date = $extraData[2];
											$entry_form = $extraData[3];
											$bookingpref = $extraData[4];
											$bill_for_id = $extraData[5];
											$bill_for_sb = $extraData[6];
											$independent = $extraData[7];
											$str_val = $extraData[8];

											$po_number = $podata['po_number'];
											$style_ref_no = $podata['style_ref_no'];
											$job_no = $podata['job_no'];
											$buyer = $podata['buyer'];
											$is_sales = $podata['is_sales'];




											if ($independent == 4) {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
					?>
												<tr id="tr_<?= $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
													<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="1" checked></td>
													<td width="30"><?= $i; ?></td>
													<td width="60"><?= $recv_number_prefix_num . '-' . $bill_for_id; ?></td>
													<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
													<td width="70"><?= change_date_format($receive_date); ?></td>
													<td width="60"><?= $no_of_roll; ?></td>
													<td width="180" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
													<td width="80" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
													<td width="40"><?= $unit_of_measurement[$row[csf('uom')]]; ?></td>
													<td width="60" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
													<td width="100" style="word-break:break-all"><?= $po_number; ?></td>
													<td width="100" style="word-break:break-all"><?= $style_ref_no; ?></td>
													<td width="100"><?= $job_no; ?></td>
													<td style="word-break:break-all; width: 100px;" align="center"><?= $buyer_arr[$buyer]; ?>
														<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
														<input type="hidden" id="currid<?= $all_value; ?>" value="<?= '1'; ?>">
													</td>
													<td style="word-break:break-all; width: 100px;" align="center"><? if ($is_sales != 1) $po_array[$poid]['grouping']; ?></td>
												</tr>
												<?php
												$i++;
											} else {
												if (strtolower($bookingpref) == strtolower($bill_for_id) || strtolower($bookingpref) == strtolower($bill_for_sb)) {
													if ($i % 2 == 0) $bgcolor = "#E9F3FF";
													else $bgcolor = "#FFFFFF";
												?>
													<tr id="tr_<?= $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
														<td width="40" align="center"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="1" checked></td>
														<td width="30"><?= $i; ?></td>
														<td width="60"><?= $recv_number_prefix_num; //.'-'.$bookingpref.'-'.$bill_for_id.'-'.$bill_for_sb 
																		?></td>
														<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
														<td width="70"><?= change_date_format($receive_date); ?></td>
														<td width="60"><?= $no_of_roll; ?></td>
														<td width="180" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
														<td width="80" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
														<td width="40"><?= $unit_of_measurement[$row[csf('uom')]]; ?></td>
														<td width="60" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
														<td width="100" style="word-break:break-all"><?= $po_number; ?></td>
														<td width="100" style="word-break:break-all"><?= $style_ref_no; ?></td>
														<td width="100"><?= $job_no; ?></td>
														<td style="word-break:break-all; width: 100px;" align="center"><?= $buyer_arr[$buyer]; ?>
															<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
															<input type="hidden" id="currid<?= $all_value; ?>" value="<?= '1'; ?>">
														</td>
														<td style="word-break:break-all; width: 100px;" align="center"><? if ($is_sales != 1) $po_array[$poid]['grouping']; ?></td>
													</tr>
												<?php
													$i++;
												}
											}
										}
									}
								}
							}
						}
						unset($updateData_arr);

						foreach ($insertData_arr as $mstid => $mstdata) // for new row
						{
							foreach ($mstdata as $prod_id => $productdata) {
								foreach ($productdata as $body_part_id => $bodypartdata) {
									foreach ($bodypartdata as $febric_description_id => $fabdescdata) {
										foreach ($fabdescdata as $poid => $podata) {
											$quantity = $no_of_roll = $independent = 0;

											$quantity = $podata['qty'];
											$no_of_roll = $podata['no_of_roll'];

											$extraData = $all_value = $recv_number_prefix_num = $challan_no = $receive_date = $entry_form = $bookingpref = $bill_for_id = $bill_for_sb = $independent = $str_val = '';

											$all_value = $podata['allval'];
											$extraData = explode("**", $podata['commnData']);

											$recv_number_prefix_num = $extraData[0];
											$challan_no = $extraData[1];
											$receive_date = $extraData[2];
											$entry_form = $extraData[3];
											$bookingpref = $extraData[4];
											$bill_for_id = $extraData[5];
											$bill_for_sb = $extraData[6];
											$independent = $extraData[7];
											$str_val = $extraData[8];
											$po_number = $podata['po_number'];
											$style_ref_no = $podata['style_ref_no'];
											$job_no = $podata['job_no'];
											$buyer = $podata['buyer'];

											if ($independent == 4) {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
												?>
												<tr id="tr_<?= $all_value; ?>" bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
													<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="2"></td>
													<td width="30"><?= $i; ?></td>
													<td width="60"><?= $recv_number_prefix_num; //.'-'.$bookingpref.'-'.$bill_for_id.'-'.$bill_for_sb 
																	?></td>
													<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
													<td width="70"><?= change_date_format($receive_date); ?></td>
													<td width="60"><?= $no_of_roll; ?></td>
													<td width="180" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
													<td width="80" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
													<td width="40"><?= $unit_of_measurement[$row[csf('uom')]]; ?></td>
													<td width="60" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
													<td width="100" style="word-break:break-all"><?= $po_number; ?></td>
													<td width="100" style="word-break:break-all"><?= $style_ref_no; ?></td>
													<td width="100"><?= $job_no; ?></td>
													<td style="word-break:break-all; width: 100px;" align="center"><?= $buyer_arr[$buyer]; ?>
														<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
														<input type="hidden" id="currid<?= $all_value; ?>" value="<?= '1'; ?>">
													</td>
													<td style="word-break:break-all; width: 100px;" align="center"><? if ($is_sales != 1) $po_array[$poid]['grouping']; ?></td>
												</tr>
												<?php
												$i++;
											} else {
												if (strtolower($ex_booking[1]) == strtolower($bill_for_id) || strtolower($ex_booking[1]) == strtolower($bill_for_sb)) {
													if ($i % 2 == 0) $bgcolor = "#E9F3FF";
													else $bgcolor = "#FFFFFF";
												?>
													<tr id="tr_<?= $all_value; ?>" bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
														<td width="40" align="center"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="2"></td>
														<td width="30"><?= $i; ?></td>
														<td width="60"><?= $recv_number_prefix_num; //.'-'.$bookingpref.'-'.$bill_for_id.'-'.$bill_for_sb 
																		?></td>
														<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
														<td width="70"><?= change_date_format($receive_date); ?></td>
														<td width="60"><?= $no_of_roll; ?></td>
														<td width="180" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
														<td width="80" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
														<td width="40"><?= $unit_of_measurement[$row[csf('uom')]]; ?></td>
														<td width="60" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
														<td width="100" style="word-break:break-all"><?= $po_number; ?></td>
														<td width="100" style="word-break:break-all"><?= $style_ref_no; ?></td>
														<td width="100"><?= $job_no; ?></td>
														<td style="word-break:break-all; width: 100px;" align="center"><?= $buyer_arr[$buyer]; ?>
															<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
															<input type="hidden" id="currid<?= $all_value; ?>" value="<?= '1'; ?>">
														</td>
														<td style="word-break:break-all; width: 100px;" align="center"><? if ($is_sales != 1) $po_array[$poid]['grouping']; ?></td>
													</tr>
												<?php
													$i++;
												}
											}
										}
									}
								}
							}
						}
					} else if ($ex_bill_for == 3) // sample without order
					{
						if ($db_type == 0) {
							$booking_without_order = "IFNULL(c.booking_without_order,0)";
							$barcode_no_cond = "group_concat(c.barcode_no)";
						} else if ($db_type == 2) {
							$booking_without_order = "nvl(c.booking_without_order,0)";
							$barcode_no_cond = "rtrim(xmlagg(xmlelement(e,c.barcode_no,',').extract('//text()') order by c.barcode_no).GetClobVal(),',')";
						}

						$sql = "select a.id, 0 as grey_sys_id, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.entry_form, b.prod_id, b.body_part_id, b.febric_description_id, count(c.id) as roll_qty, null as po_breakdown_id, sum(c.qc_pass_qnty) as quantity,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs, a.receive_basis, a.booking_no, a.booking_id, $barcode_no_cond as barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.company_id='$data[0]' and a.knitting_company='$data[3]' and a.location_id='$data[1]' and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0 and a.no_bill !=1
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $po_breakdown_id_conds
						group by a.id, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.entry_form, b.prod_id, b.body_part_id, b.febric_description_id, a.receive_basis, a.booking_no, a.booking_id";
						//echo $sql;
						$sql_result = sql_select($sql);
						$bookingNo_arr = array();
						$barcode_no = '';

						foreach ($sql_result as $row) {
							$bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
							if ($db_type == 2) $barcode_no .= $row[csf('barcode_no')]->load() . ',';
							else $barcode_no .= $row[csf('barcode_no')] . ',';
						}

						$barcode_nos = chop($barcode_no, ',');
						$barcode_nosCond = "";
						$barcode_count = count(array_unique(explode(",", $barcode_nos)));
						if ($db_type == 2 && $barcode_count > 1000) {
							$barcode_nosCond = " and (";
							$barcode_nosArr = array_chunk(explode(",", $barcode_nos), 999);
							foreach ($barcode_nosArr as $ids) {
								$ids = implode(",", $ids);
								$barcode_nosCond .= " c.barcode_no in($ids) or";
							}
							$barcode_nosCond = chop($barcode_nosCond, 'or ');
							$barcode_nosCond .= ")";
						} else {
							$barcode_nos = implode(",", array_unique(explode(",", $barcode_nos)));
							$barcode_nosCond = " and c.barcode_no in($barcode_nos)";
						}

						$sql_roll = "select a.id, a.receive_basis, a.booking_no, a.booking_id, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2) and c.entry_form in (2) and a.item_category=13 and a.receive_basis in (1,2) and b.trans_id=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_nosCond";
						//echo $sql_roll;
						$sql_rollRes = sql_select($sql_roll);
						$planid = "";
						$is_plan = 0;
						$planBooking_arr = array();
						$bookingNo = "";
						$is_booking = 0;
						foreach ($sql_rollRes as $row) {
							if ($row[csf('receive_basis')] == 1) //WO/Booking Based
							{
								$bookingNo .= "'" . $row[csf('booking_no')] . "',";
								$is_booking = 1;
								$bookingNo_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
							}
							if ($row[csf('receive_basis')] == 2) //Kniting Plan
							{
								$is_plan = 1;
								$planid .= $row[csf('booking_id')] . ',';
								$planBooking_arr[$row[csf('booking_id')]]['prodPlan'] .= $row[csf('barcode_no')] . ',';
							}
						}
						unset($sql_rollRes);
						$styleBuyer_arr = array();
						if ($is_booking == 1) {
							$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
							$bookingNosCond = "";
							$booking_nos = count(array_unique(explode(",", $bookingNos)));
							if ($db_type == 2 && $booking_nos > 1000) {
								$bookingNosCond = " and (";
								$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
								foreach ($bookingNosArr as $ids) {
									$ids = implode(",", $ids);
									$bookingNosCond .= " a.booking_no in($ids) or";
								}
								$bookingNosCond = chop($bookingNosCond, 'or ');
								$bookingNosCond .= ")";
							} else {
								$bookingNos = implode(",", array_unique(explode(",", $bookingNos)));
								$bookingNosCond = " and a.booking_no in($bookingNos)";
							}

							$sampleSql = "select a.booking_no, b.style_ref_no, b.buyer_name from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingNosCond";
							$sampleSqlRes = sql_select($sampleSql);
							foreach ($sampleSqlRes as $row) {
								$styleBuyer_arr[$row[csf('booking_no')]]['style'] = $row[csf('style_ref_no')];
								$styleBuyer_arr[$row[csf('booking_no')]]['buyer'] = $row[csf('buyer_name')];
							}
							unset($sampleSqlRes);
						}

						if ($is_plan == 1) {
							$planIds = chop($planid, ',');
							$plan_idsCond = "";
							$plan_ids = count(array_unique(explode(",", $planIds)));
							if ($db_type == 2 && $plan_ids > 1000) {
								$plan_idsCond = " and (";
								$planIdsArr = array_chunk(explode(",", $planIds), 999);
								foreach ($planIdsArr as $ids) {
									$ids = implode(",", $ids);
									$plan_idsCond .= " b.id in($ids) or";
								}
								$plan_idsCond = chop($production_idsCond, 'or ');
								$plan_idsCond .= ")";
							} else {
								$planIds = implode(",", array_unique(explode(",", $planIds)));
								$plan_idsCond = " and b.id in($planIds)";
							}

							$plan_booking_arr = array();
							$knit_booking = "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 $plan_idsCond";
							$knit_booking_result = sql_select($knit_booking);
							$bookingNo = "";
							$is_booking = 0;
							foreach ($knit_booking_result as $row) {
								$bookingNo .= "'" . $row[csf('booking_no')] . "',";
								$is_booking = 1;
								$exbarcodeNo = explode(",", chop($planBooking_arr[$row[csf('id')]]['prodPlan'], ','));
								foreach ($exbarcodeNo as $barcodeNo) {
									$bookingNo_arr[$barcodeNo] = $row[csf('booking_no')];
								}
							}
							unset($knit_booking_result);

							if ($is_booking == 1) {
								$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
								$bookingNosCond = "";
								$booking_nos = count(array_unique(explode(",", $bookingNos)));
								if ($db_type == 2 && $booking_nos > 1000) {
									$bookingNosCond = " and (";
									$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
									foreach ($bookingNosArr as $ids) {
										$ids = implode(",", $ids);
										$bookingNosCond .= " a.booking_no in($ids) or";
									}
									$bookingNosCond = chop($bookingNosCond, 'or ');
									$bookingNosCond .= ")";
								} else $bookingNosCond = " and a.booking_no in($bookingNos)";

								$sampleSql = "select a.booking_no, b.style_ref_no, b.buyer_name from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingNosCond";
								$sampleSqlRes = sql_select($sampleSql);
								foreach ($sampleSqlRes as $row) {
									$styleBuyer_arr[$row[csf('booking_no')]]['style'] = $row[csf('style_ref_no')];
									$styleBuyer_arr[$row[csf('booking_no')]]['buyer'] = $row[csf('buyer_name')];
								}
								unset($sampleSqlRes);
							}
						}

						$updateData_arr = array();
						$insertData_arr = array();
						foreach ($sql_result as $row) {
							if ($db_type == 2) $row[csf('barcode_no')] = $row[csf('barcode_no')]->load();
							$bill_for_id = "SMN";$bill_for_id2 = "FB";
							$bill_for_sb = "SBKD";
							$booking_no = '';
							if ($row[csf('entry_form')] == 58) {
								$exbarcodeNo = explode(",", $row[csf('barcode_no')]);
								foreach ($exbarcodeNo as $barcodeNo) {
									$booking_no = $bookingNo_arr[$barcodeNo];
								}
							} else if ($row[csf('entry_form')] == 22) {
								$booking_no = $bookingNo_arr[$row[csf('booking_id')]];
								if ($booking_no == "") $booking_no = $bookingNo_arr[$row[csf('id')]];
							} elseif ($row[csf('entry_form')] == 2) {
								$booking_no = $bookingNo_arr[$row[csf('id')]];
								if ($booking_no == "") $booking_no = $bookingNo_arr[$row[csf('booking_id')]];
							}
							//echo $booking_no.'<br>';
							if ($row[csf('entry_form')] == 2) //Production
							{
								if ($row[csf('receive_basis')] == 0) $independent = 4;
							} else //22,58 entry form
							{
								if ($row[csf('entry_form')] == 22) {
									if ($ex_bill_for == 1) {
										if ($row[csf('receive_basis')] == 4) $independent = 4;
									}
									if ($ex_bill_for == 1) {
										if ($booking_no == "" && $row[csf('receive_basis')] == 9) $independent = 4;
									}
								} else {
									if ($ex_bill_for == 1) {
										if ($booking_no == "" && $row[csf('receive_basis')] == 10) $independent = 4;
									}
								}
							}
							$sample_buyer_id = $styleBuyer_arr[$booking_no]['buyer'];
							$sample_style = $styleBuyer_arr[$booking_no]['style'];

							$ex_booking = "";
							$str_val = "";
							if ($booking_no != '') $ex_booking = explode('-', $booking_no);
							else $ex_booking = "_";

							$row[csf('po_breakdown_id')] = "";
							$all_value = $row[csf('recv_number_prefix_num')] . '_' . $row[csf('po_breakdown_id')] . '_' . $row[csf('prod_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
							//echo $update_id.'ghj';
							$grey_receive_qnty_pcs = $row[csf('grey_receive_qnty_pcs')];
							// echo $grey_receive_qnty_pcs.'DDDS';
							if (strtolower($ex_booking[1]) == strtolower($bill_for_id) || strtolower($ex_booking[1]) == strtolower($bill_for_sb) || strtolower($ex_booking[1]) == strtolower($bill_for_id2)) {
								if (in_array($all_value, $str_arr) && $update_id != '') {
									$str_val = $row[csf('id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('recv_number_prefix_num')] . '_' . $row[csf('po_breakdown_id')] . '_' . $po_array[$row[csf('po_breakdown_id')]]['po_number'] . '_' . $sample_style . '_' . $buyer_arr[$sample_buyer_id] . '_' . $row[csf('no_of_roll')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $row[csf('quantity')] . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $grey_receive_qnty_pcs;

									$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['qty'] += $row[csf('quantity')];
									$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['rollQty'] += $row[csf('no_of_roll')];
									$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['allval'] = $all_value;

									$updateData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['commnData'] = $row[csf('recv_number_prefix_num')] . '**' . $row[csf('challan_no')] . '**' . $row[csf('receive_date')] . '**' . $row[csf('entry_form')] . '**' . $ex_booking[1] . '**' . $bill_for_id . '**' . $bill_for_sb . '**' . $independent . '**' . $sample_style . '**' . $buyer_arr[$sample_buyer_id] . '**' . $str_val;
								} else {
									$bill_qty = $bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
									$avilable_qty = $row[csf('quantity')] - $bill_qty;
									if ($avilable_qty > 0) {
										$str_val = $row[csf('id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('recv_number_prefix_num')] . '_' . $row[csf('po_breakdown_id')] . '_' . $po_array[$row[csf('po_breakdown_id')]]['po_number'] . '_' . $sample_style . '_' . $buyer_arr[$sample_buyer_id] . '_' . $row[csf('no_of_roll')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $avilable_qty . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $grey_receive_qnty_pcs;


										$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['qty'] += $avilable_qty;
										$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['rollQty'] += $row[csf('no_of_roll')];
										$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['allval'] = $all_value;

										$insertData_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('po_breakdown_id')]]['commnData'] = $row[csf('recv_number_prefix_num')] . '**' . $row[csf('challan_no')] . '**' . $row[csf('receive_date')] . '**' . $row[csf('entry_form')] . '**' . $ex_booking[1] . '**' . $bill_for_id . '**' . $bill_for_sb . '**' . $independent . '**' . $sample_style . '**' . $buyer_arr[$sample_buyer_id] . '**' . $str_val;
									}
								}
							}
						}

						foreach ($updateData_arr as $mstid => $mstdata) // for update row
						{
							foreach ($mstdata as $prod_id => $productdata) {
								foreach ($productdata as $body_part_id => $bodypartdata) {
									foreach ($bodypartdata as $febric_description_id => $fabdescdata) {
										foreach ($fabdescdata as $poid => $podata) {
											$quantity = $no_of_roll = $independent = 0;

											$quantity = $podata['qty'];
											$no_of_roll = $podata['no_of_roll'];

											$extraData = $all_value = $recv_number_prefix_num = $challan_no = $receive_date = $entry_form = $bookingpref = $bill_for_id = $bill_for_sb = $independent = $str_val = '';
											$all_value = $podata['allval'];
											$extraData = explode("**", $podata['commnData']);

											$recv_number_prefix_num = $extraData[0];
											$challan_no = $extraData[1];
											$receive_date = $extraData[2];
											$entry_form = $extraData[3];
											$bookingpref = $extraData[4];
											$bill_for_id = $extraData[5];
											$bill_for_sb = $extraData[6];
											$independent = $extraData[7];
											$sample_style = $extraData[8];
											$buyerName = $extraData[9];
											$str_val = $extraData[10];

											if ($independent == 4) {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
												?>
												<tr id="tr_<?= $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
													<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="1" checked></td>
													<td width="30"><?= $i; ?></td>
													<td width="60"><?= $recv_number_prefix_num; ?></td>
													<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
													<td width="70" align="center"><?= change_date_format($receive_date); ?></td>
													<td width="90" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
													<td width="160" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
													<td width="60">&nbsp;</td>
													<td width="80" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
													<td width="60" align="right"><?= number_format($no_of_roll, 2, '.', ''); ?></td>
													<td width="100" style="word-break:break-all"><?= $sample_style; ?></td>
													<td style="word-break:break-all"><?= $buyerName; ?>
														<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
														<input type="hidden" style="width:40px" id="currid<?= $all_value; ?>" value="<?= 1; ?>">
													</td>
												</tr>
											<?php
												$i++;
											} else {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
											?>
												<tr id="tr_<?= $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
													<td width="40" align="center"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="1" checked></td>
													<td width="30"><?= $i; ?></td>
													<td width="60"><?= $recv_number_prefix_num; ?></td>
													<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
													<td width="70" align="center"><?= change_date_format($receive_date); ?></td>
													<td width="90" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
													<td width="160" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
													<td width="60">&nbsp;</td>
													<td width="80" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
													<td width="60" align="right"><?= number_format($no_of_roll, 2, '.', ''); ?></td>
													<td width="100" style="word-break:break-all"><?= $sample_style; ?></td>
													<td style="word-break:break-all"><?= $buyerName; ?>
														<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
														<input type="hidden" style="width:40px" id="currid<?= $all_value; ?>" value="<?= 1; ?>">
													</td>
												</tr>
											<?php
												$i++;
											}
										}
									}
								}
							}
						}
						unset($updateData_arr);

						foreach ($insertData_arr as $mstid => $mstdata) // for new row
						{
							foreach ($mstdata as $prod_id => $productdata) {
								foreach ($productdata as $body_part_id => $bodypartdata) {
									foreach ($bodypartdata as $febric_description_id => $fabdescdata) {
										foreach ($fabdescdata as $poid => $podata) {
											$quantity = $no_of_roll = $independent = 0;

											$quantity = $podata['qty'];
											$no_of_roll = $podata['no_of_roll'];

											$extraData = $all_value = $recv_number_prefix_num = $challan_no = $receive_date = $entry_form = $bookingpref = $bill_for_id = $bill_for_sb = $independent = $str_val = '';
											$all_value = $podata['allval'];
											$extraData = explode("**", $podata['commnData']);

											$recv_number_prefix_num = $extraData[0];
											$challan_no = $extraData[1];
											$receive_date = $extraData[2];
											$entry_form = $extraData[3];
											$bookingpref = $extraData[4];
											$bill_for_id = $extraData[5];
											$bill_for_sb = $extraData[6];
											$independent = $extraData[7];
											$sample_style = $extraData[8];
											$buyerName = $extraData[9];
											$str_val = $extraData[10];

											if ($independent == 4) {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
											?>
												<tr id="tr_<?= $all_value; ?>" bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
													<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="2"></td>
													<td width="30"><?= $i; ?></td>
													<td width="60"><?= $recv_number_prefix_num . '=' . $independent; ?></td>
													<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
													<td width="70" align="center"><?= change_date_format($receive_date); ?></td>
													<td width="90" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
													<td width="160" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
													<td width="60">&nbsp;</td>
													<td width="80" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
													<td width="60" align="right"><?= number_format($no_of_roll, 2, '.', ''); ?></td>
													<td width="100" style="word-break:break-all"><?= $sample_style; ?></td>
													<td style="word-break:break-all"><?= $buyerName; ?>
														<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
														<input type="hidden" style="width:40px" id="currid<?= $all_value; ?>" value="<?= 1; ?>">
													</td>
												</tr>
											<?php
												$i++;
											} else {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
											?>
												<tr id="tr_<?= $all_value; ?>" bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<?= $all_value; ?>');">
													<td width="40" align="center"><input type="checkbox" name="checkid<?= $i; ?>" id="checkid<?= $i; ?>" onClick="fnc_check(<?= $i; ?>)" value="2"></td>
													<td width="30"><?= $i; ?></td>
													<td width="60"><?= $recv_number_prefix_num; ?></td>
													<td width="70" style="word-break:break-all"><?= $challan_no; ?></td>
													<td width="70" align="center"><?= change_date_format($receive_date); ?></td>
													<td width="90" style="word-break:break-all"><?= $body_part[$body_part_id]; ?></td>
													<td width="160" style="word-break:break-all"><?= $item_id_arr[$prod_id]; ?></td>
													<td width="60">&nbsp;</td>
													<td width="80" align="right"><?= number_format($quantity, 2, '.', ''); ?></td>
													<td width="60" align="right"><?= number_format($no_of_roll, 2, '.', ''); ?></td>
													<td width="100" style="word-break:break-all"><?= $sample_style; ?></td>
													<td style="word-break:break-all"><?= $buyerName; ?>
														<input type="hidden" id="strid<?= $i; ?>" value="<?= $str_val; ?>">
														<input type="hidden" style="width:40px" id="currid<?= $all_value; ?>" value="<?= 1; ?>">
													</td>
												</tr>
					<?php
												$i++;
											}
										}
									}
								}
							}
						}
					}
					?>
				</table>
			</div>
			<table width="800">
				<tr>
					<td colspan="12" align="center">
						<input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
					</td>
				</tr>
			</table>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	//}
	exit();
}

if ($action == "load_dtls_data") {
	$exdata = explode("__", $data);
	$mstid = $exdata[0];
	$billfor = $exdata[1];
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
	//$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
	$item_id_arr = return_library_array("select id,product_name_details from  product_details_master", 'id', 'product_name_details');
	$booking_arr = return_library_array("select a.id,a.booking_no from wo_booking_mst a,subcon_outbound_bill_dtls b where a.id=b.wo_num_id and b.mst_id='$mstid' ", 'id', 'booking_no');
	//echo "select a.id,a.booking_no from wo_booking_mst a,subcon_outbound_bill_dtls b where a.id=b.wo_num_id and b.mst_id='$mstid' ";
	//echo $billfor.'==';die;

	$po_array = array();
	if ($billfor != 3) {
		if ($billfor == 4) //========FSO================ 
		{
			$sql_order_sales = "Select b.id, b.job_no as po_number,b.po_buyer as buyer_name, b.style_ref_no from subcon_outbound_bill_dtls a, fabric_sales_order_mst b where a.order_id=b.id  and a.mst_id='$mstid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$sql_order_sales_result = sql_select($sql_order_sales);
			foreach ($sql_order_sales_result as $row) {
				$po_array[$row[csf("id")]]['po_number'] = $row[csf("po_number")];
				$po_array[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
				$po_array[$row[csf("id")]]['buyer'] = $row[csf("buyer_name")];
			}
			unset($sql_order_result);
			//print_r($po_array);
		}

		if ($billfor == 2 || $billfor == 1) {
			$sql_order = "Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$sql_order_result = sql_select($sql_order);
			foreach ($sql_order_result as $row) {
				$po_array[$row[csf("id")]]['po_number'] = $row[csf("po_number")];
				$po_array[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
				$po_array[$row[csf("id")]]['buyer'] = $row[csf("buyer_name")];
			}
			unset($sql_order_result);
		}

		//var_dump($order_array);
	}

	$sql = "SELECT id as dtls_id, receive_id,wo_num_id, is_sales,receive_date, challan_no, order_id, roll_no, body_part_id, febric_description_id, item_id as prod_id, receive_qty, rec_qty_pcs, uom, rate, amount, remarks,currency_id FROM subcon_outbound_bill_dtls WHERE mst_id='$mstid' and process_id=2 and status_active=1 and is_deleted=0 order by id asc";
	$sql_result_arr = sql_select($sql);
	if ($billfor == 3) {
		$reciveid = "";
		foreach ($sql_result_arr as $row) {
			$reciveid .= $row[csf('receive_id')] . ',';
		}

		$reciveIds = implode(",", array_filter(array_unique(explode(",", $reciveid))));
		$reciveIdsCond = "";
		$receive_ids = count(explode(",", $reciveIds));
		if ($db_type == 2 && $receive_ids > 1000) {
			$reciveIdsCond = " and (";
			$reciveIdsArr = array_chunk(explode(",", $reciveIds), 999);
			foreach ($reciveIdsArr as $ids) {
				$ids = implode(",", $ids);
				$reciveIdsCond .= " a.id in($ids) or";
			}
			$reciveIdsCond = chop($reciveIdsCond, 'or ');
			$reciveIdsCond .= ")";
		} else $reciveIdsCond = " and a.id in($reciveIds)";

		if ($db_type == 0) {
			$booking_without_order = "IFNULL(c.booking_without_order,0)";
			$barcode_no_cond = "group_concat(c.barcode_no)";
		} else if ($db_type == 2) {
			$booking_without_order = "nvl(c.booking_without_order,0)";
			$barcode_no_cond = "rtrim(xmlagg(xmlelement(e,c.barcode_no,',').extract('//text()') order by c.barcode_no).GetClobVal(),',')";
		}

		$sql = "select a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, $barcode_no_cond as barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $reciveIdsCond
		group by a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id";
		//echo $sql;
		$sql_result = sql_select($sql);
		$bookingNo_arr = array();
		$barcode_no = '';
		$barcodeWiseRecIdArr = array();

		foreach ($sql_result as $row) {
			if ($db_type == 2) $row[csf('barcode_no')] = $row[csf('barcode_no')]->load();

			$bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
			$barcode_no .= $row[csf('barcode_no')] . ',';
			$exbarcode = explode(",", $row[csf('barcode_no')]);
			foreach ($exbarcode as $barcode) {
				$barcodeWiseRecIdArr[$barcode] = $row[csf('id')];
			}
		}

		$barcode_nos = chop($barcode_no, ',');
		$barcode_nosCond = "";
		$barcode_count = count(array_unique(explode(",", $barcode_nos)));
		if ($db_type == 2 && $barcode_count > 1000) {
			$barcode_nosCond = " and (";
			$barcode_nosArr = array_chunk(explode(",", $barcode_nos), 999);
			foreach ($barcode_nosArr as $ids) {
				$ids = implode(",", $ids);
				$barcode_nosCond .= " c.barcode_no in($ids) or";
			}
			$barcode_nosCond = chop($barcode_nosCond, 'or ');
			$barcode_nosCond .= ")";
		} else $barcode_nosCond = " and c.barcode_no in($barcode_nos)";

		$sql_roll = "select a.id, a.receive_basis, a.booking_no, a.booking_id, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2) and c.entry_form in (2) and a.item_category=13 and a.receive_basis in (1,2) and b.trans_id=0
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_nosCond";
		//echo $sql_roll;
		$sql_rollRes = sql_select($sql_roll);
		$planid = "";
		$is_plan = 0;
		$planBooking_arr = array();
		$bookingNo = "";
		$is_booking = 0;
		$bookingWiseStyleBuyerArr = array();
		foreach ($sql_rollRes as $row) {
			if ($row[csf('receive_basis')] == 1) //WO/Booking Based
			{
				$bookingNo .= "'" . $row[csf('booking_no')] . "',";
				$is_booking = 1;
				$bookingNo_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
			}
			if ($row[csf('receive_basis')] == 2) //Kniting Plan
			{
				$is_plan = 1;
				$planid .= $row[csf('booking_id')] . ',';
				$planBooking_arr[$row[csf('booking_id')]]['prodPlan'] .= $row[csf('barcode_no')] . ',';
			}
			$bookingWiseStyleBuyerArr[$row[csf('booking_no')]] = $barcodeWiseRecIdArr[$row[csf('barcode_no')]];
		}
		unset($sql_rollRes);
		$styleBuyer_arr = array();
		if ($is_booking == 1) {
			$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
			$bookingNosCond = "";
			$booking_nos = count(array_unique(explode(",", $bookingNos)));
			if ($db_type == 2 && $booking_nos > 1000) {
				$bookingNosCond = " and (";
				$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
				foreach ($bookingNosArr as $ids) {
					$ids = implode(",", $ids);
					$bookingNosCond .= " a.booking_no in($ids) or";
				}
				$bookingNosCond = chop($bookingNosCond, 'or ');
				$bookingNosCond .= ")";
			} else $bookingNosCond = " and a.booking_no in($bookingNos)";

			$sampleSql = "select a.booking_no, b.style_ref_no, b.buyer_name from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingNosCond";
			$sampleSqlRes = sql_select($sampleSql);
			foreach ($sampleSqlRes as $row) {
				$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style'] = $row[csf('style_ref_no')];
				$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer'] = $buyer_arr[$row[csf('buyer_name')]];
			}
			unset($sampleSqlRes);
			//print_r($styleBuyer_arr);
		}

		if ($is_plan == 1) {
			$planIds = chop($planid, ',');
			$plan_idsCond = "";
			$plan_ids = count(array_unique(explode(",", $planIds)));
			if ($db_type == 2 && $plan_ids > 1000) {
				$plan_idsCond = " and (";
				$planIdsArr = array_chunk(explode(",", $planIds), 999);
				foreach ($planIdsArr as $ids) {
					$ids = implode(",", $ids);
					$plan_idsCond .= " b.id in($ids) or";
				}
				$plan_idsCond = chop($production_idsCond, 'or ');
				$plan_idsCond .= ")";
			} else $plan_idsCond = " and b.id in($planIds)";

			$plan_booking_arr = array();
			$knit_booking = "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 $plan_idsCond";
			$knit_booking_result = sql_select($knit_booking);
			$bookingNo = "";
			$is_booking = 0;
			foreach ($knit_booking_result as $row) {
				$bookingNo .= "'" . $row[csf('booking_no')] . "',";
				$is_booking = 1;
				$exbarcodeNo = explode(",", chop($planBooking_arr[$row[csf('id')]]['prodPlan'], ','));
				foreach ($exbarcodeNo as $barcodeNo) {
					$bookingNo_arr[$barcodeNo] = $row[csf('booking_no')];
				}
			}
			unset($knit_booking_result);

			if ($is_booking == 1) {
				$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
				$bookingNosCond = "";
				$booking_nos = count(array_unique(explode(",", $bookingNos)));
				if ($db_type == 2 && $booking_nos > 1000) {
					$bookingNosCond = " and (";
					$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
					foreach ($bookingNosArr as $ids) {
						$ids = implode(",", $ids);
						$bookingNosCond .= " a.booking_no in($ids) or";
					}
					$bookingNosCond = chop($bookingNosCond, 'or ');
					$bookingNosCond .= ")";
				} else $bookingNosCond = " and a.booking_no in($bookingNos)";

				$sampleSql = "select a.booking_no, b.style_ref_no, b.buyer_name from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingNosCond";
				$sampleSqlRes = sql_select($sampleSql);
				foreach ($sampleSqlRes as $row) {
					$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style'] = $row[csf('style_ref_no')];
					$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer'] = $buyer_arr[$row[csf('buyer_name')]];
				}
				unset($sampleSqlRes);
			}
		}
	}

	$str_val = "";
	if ($billfor != 3) {
		foreach ($sql_result_arr as $row) {
			$booking_no = $booking_arr[$row[csf('wo_num_id')]];
			$bodyPartType=$bodyPartTypeArr[$row[csf('body_part_id')]];
			//echo $booking_no.','; wo_num_id
			if ($str_val == "") $str_val = $row[csf('receive_id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $po_array[$row[csf('order_id')]]['po_number'] . '_' . $po_array[$row[csf('order_id')]]['style_ref_no'] . '_' . $buyer_arr[$po_array[$row[csf('order_id')]]['buyer']] . '_' . $row[csf('roll_no')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $row[csf('receive_qty')] . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $row[csf('rate')] . '_' . $row[csf('amount')] . '_' . $row[csf('remarks')] . '_' . $row[csf('uom')] . '_' . $row[csf('dtls_id')] . '_' . $row[csf('rec_qty_pcs')] . '_' . $booking_no . '_' . $row[csf('wo_num_id')] . '_' . $row[csf('currency_id')].'_'.$bodyPartType;
			else $str_val .= "###" . $row[csf('receive_id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $po_array[$row[csf('order_id')]]['po_number'] . '_' . $po_array[$row[csf('order_id')]]['style_ref_no'] . '_' . $buyer_arr[$po_array[$row[csf('order_id')]]['buyer']] . '_' . $row[csf('roll_no')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $row[csf('receive_qty')] . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $row[csf('rate')] . '_' . $row[csf('amount')] . '_' . $row[csf('remarks')] . '_' . $row[csf('uom')] . '_' . $row[csf('dtls_id')] . '_' . $row[csf('rec_qty_pcs')] . '_' . $booking_no . '_' . $row[csf('wo_num_id')] . '_' . $row[csf('currency_id')].'_'.$bodyPartType;;
		}
	} else {
		foreach ($sql_result_arr as $row) {
			$booking_no = $booking_arr[$row[csf('wo_num_id')]];
			$bodyPartType=$bodyPartTypeArr[$row[csf('body_part_id')]];
			if ($str_val == "") $str_val = $row[csf('receive_id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $po_array[$row[csf('order_id')]]['po_number'] . '_' . $styleBuyer_arr[$row[csf('receive_id')]]['style'] . '_' . $styleBuyer_arr[$row[csf('receive_id')]]['buyer'] . '_' . $row[csf('roll_no')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $row[csf('receive_qty')] . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $row[csf('rate')] . '_' . $row[csf('amount')] . '_' . $row[csf('remarks')] . '_' . $row[csf('uom')] . '_' . $row[csf('dtls_id')] . '_' . $row[csf('rec_qty_pcs')] . '_' . $booking_no . '_' . $row[csf('wo_num_id')] . '_' . $row[csf('currency_id')].'_'.$bodyPartType;
			else $str_val .= "###" . $row[csf('receive_id')] . '_' . change_date_format($row[csf('receive_date')]) . '_' . $row[csf('challan_no')] . '_' . $row[csf('order_id')] . '_' . $po_array[$row[csf('order_id')]]['po_number'] . '_' . $styleBuyer_arr[$row[csf('receive_id')]]['style'] . '_' . $styleBuyer_arr[$row[csf('receive_id')]]['buyer'] . '_' . $row[csf('roll_no')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')] . '_' . $row[csf('prod_id')] . '_' . $item_id_arr[$row[csf('prod_id')]] . '_' . $row[csf('receive_qty')] . '_' . $body_part[$row[csf('body_part_id')]] . '_' . $row[csf('rate')] . '_' . $row[csf('amount')] . '_' . $row[csf('remarks')] . '_' . $row[csf('uom')] . '_' . $row[csf('dtls_id')] . '_' . $row[csf('rec_qty_pcs')] . '_' . $booking_no . '_' . $row[csf('wo_num_id')] . '_' . $row[csf('currency_id')].'_'.$bodyPartType;;
		}
	}
	echo $str_val;
	exit();
}

if ($action == "load_php_dtls_form") {
	$data = explode("***", $data);
	$old_selected_id = explode(",", $data[0]);
	//$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
	//$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	//$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	//$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
	$booking_arr = return_library_array("select id,booking_no from wo_booking_mst", 'id', 'booking_no');

	$challan = "";
	$po_id = "";
	$item_id = "";
	$body_part_id = "";
	$febric_description_id = ""; // $selected_id_arr=array();
	foreach ($old_selected_id as $val) {
		$selected_id_arr[] = $val;
		$ex_data = explode("_", $val);
		if ($challan == "") $challan = $ex_data[0];
		else $challan .= ',' . $ex_data[0];
		if ($po_id == "") $po_id = $ex_data[1];
		else $po_id .= ',' . $ex_data[1];
		if ($item_id == "") $item_id = $ex_data[2];
		else $item_id .= ',' . $ex_data[2];
		if ($body_part_id == "") $body_part_id = $ex_data[3];
		else $body_part_id .= ',' . $ex_data[3];
		if ($febric_description_id == "") $febric_description_id = $ex_data[4];
		else $febric_description_id .= ',' . $ex_data[4];
	}

	$old_issue_id = explode(",", $data[1]);
	$old_challan = "";
	$old_po_id = "";
	$old_item_id = "";
	$old_body_part_id = "";
	$old_febric_description_id = "";
	foreach ($old_issue_id as $value) {
		$old_selected_id_arr[] = $value;
		$old_data = explode("_", $value);
		if ($old_challan == "") $old_challan = $old_data[0];
		else $old_challan .= ',' . $old_data[0];
		if ($old_po_id == "") $old_po_id = $old_data[1];
		else $old_po_id .= ',' . $old_data[1];
		if ($old_item_id == "") $old_item_id = $old_data[2];
		else $old_item_id .= ',' . $old_data[2];
		if ($old_body_part_id == "") $old_body_part_id = $old_data[3];
		else $old_body_part_id .= ',' . $old_data[3];
		if ($old_febric_description_id == "") $old_febric_description_id = $old_data[4];
		else $old_febric_description_id .= ',' . $old_data[4];
	}

	$bill_challan = implode(",", array_intersect(explode(",", $challan), explode(",", $old_challan)));
	$bill_po_id = implode(",", array_intersect(explode(",", $po_id), explode(",", $old_po_id)));
	$bill_item_id = implode(",", array_intersect(explode(",", $item_id), explode(",", $old_item_id)));
	$bill_body_part_id = implode(",", array_intersect(explode(",", $body_part_id), explode(",", $old_body_part_id)));

	$bill_febric_description_id = implode(",", array_intersect(explode(",", $febric_description_id), explode(",", $old_febric_description_id)));
	$dele_item_id = "'" . implode("','", explode(",", $bill_item_id)) . "'";

	$del_challan = implode(",", array_diff(explode(",", $challan), explode(",", $old_challan)));
	$del_po_id = implode(",", array_diff(explode(",", $po_id), explode(",", $old_po_id)));
	$del_item_id = implode(",", array_diff(explode(",", $item_id), explode(",", $old_item_id)));
	$del_body_part_id = implode(",", array_diff(explode(",", $body_part_id), explode(",", $old_body_part_id)));
	$del_febric_description_id = implode(",", array_diff(explode(",", $febric_description_id), explode(",", $old_febric_description_id)));
	//$add_del_item_id="'".implode("','",explode(",",$del_item_id))."'";
	if ($del_item_id == "") $add_del_item_id = "'" . implode("','", explode(",", $old_item_id)) . "'";
	else $add_del_item_id = "'" . implode("','", explode(",", $item_id)) . "'";
	//echo $febric_description_id.'=='.$old_febric_description_id.'=='.$bill_febric_description_id.'=='.$del_febric_description_id;
	$old_selected_id = "'" . implode("','", explode(",", $data[0])) . "'";
	$old_issue_id = "'" . implode("','", explode(",", $data[1])) . "'";

	$old_bill_id = array_intersect(explode(",", $old_selected_id), explode(",", $old_issue_id));
	$old_bill_id = implode(",", $old_bill_id);

	$data_selected = implode(',', explode('_', $data[0]));
	$data_issue = implode(',', explode('_', $data[1]));

	$del_id = array_diff(explode(",", $data_selected), explode(",", $data_issue));
	//$bill_id=array_intersect(explode(",",$data_selected), explode(",",$data_issue));
	//$delete_id=array_diff(explode(",",$data_issue), explode(",",$data_selected));
	$bill_id = array_intersect(explode(",", $old_selected_id), explode(",", $old_issue_id));
	$delete_id = array_diff(explode(",", $old_issue_id), explode(",", $old_selected_id));

	$del_id = implode(",", $del_id);
	$bill_id = implode(",", $bill_id);
	$delete_id = implode(",", $delete_id);

	if ($del_body_part_id != "") $body_part_cond = " and b.body_part_id in ($body_part_id)";
	else $body_part_cond = "";
	if ($del_challan != "") $del_challan_cond = " and a.recv_number_prefix_num in ($del_challan)";
	else $del_challan_cond = "";
	if ($del_po_id != "") $del_po_id_cond = "  and c.po_breakdown_id in ($po_id)";
	else $del_po_id_cond = "";
	if ($add_del_item_id != "") $del_item_id_cond = "  and c.prod_id in ($add_del_item_id)";
	else $del_item_id_cond = "";
	if ($febric_description_id != "") $del_febric_id_cond = "  and b.febric_description_id in ($febric_description_id)";
	else $del_febric_id_cond = "";
	if ($add_del_item_id != "") $wout_item_id_cond = "  and b.prod_id in ($add_del_item_id)";
	else $wout_item_id_cond = "";

	$order_array = array();
	$sql_order = "Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$sql_order_result = sql_select($sql_order);
	foreach ($sql_order_result as $row) {
		$order_array[$row[csf("id")]]['po_number'] = $row[csf("po_number")];
		$order_array[$row[csf("id")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$order_array[$row[csf("id")]]['buyer_name'] = $row[csf("buyer_name")];
	}
	//var_dump($order_array);
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$roll_no_arr = return_library_array("select id, no_of_roll from  pro_grey_prod_entry_dtls", 'id', 'no_of_roll');
	$item_id_arr = return_library_array("select id,product_name_details from  product_details_master", 'id', 'product_name_details');
	if ($data[3] != 3) {
		if ($data[2] != "") //update===========
		{
			$sql = "SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, body_part_id, febric_description_id, receive_qty, uom, 0 as dtls_id, rate, amount, remarks FROM subcon_outbound_bill_dtls  WHERE mst_id=$data[2] and process_id=2";

			$sql_result_arr = sql_select($sql);
			foreach ($sql_result_arr as $row) {
				$update_chk_str[] = $row[csf("challan_no")] . '_' . $row[csf('order_id')] . '_' . $row[csf('prod_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
				$issue_chk_str[] = $row[csf("challan_no")] . '_' . $row[csf('order_id')] . '_' . $row[csf('prod_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
			}
		} else //insert=================
		{
			/*if($db_type==0)
			{
				if($bill_id!="" && $del_id!="")
					$sql="(SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id=2)
					 union 
					 (SELECT 0 as upd_id, c.id as receive_id, a.receive_date, a.recv_number_prefix_num as challan_no, c.po_breakdown_id as order_id, b.prod_id, sum(c.quantity) as order_qnty, b.uom, c.dtls_id, 0 as rate, 0 as amount, null as remarks FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (2,22) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=13 and a.id not in (select id from inv_receive_master where knitting_source=3 AND entry_form=22 AND receive_basis in (1,6,9)) group by c.id, a.receive_date, a.challan_no, c.po_breakdown_id, b.prod_id, c.dtls_id)";
				else if($bill_id!="" && $del_id=="")
					$sql="SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, 0 as uom, roll_no as no_of_roll, receive_qty as order_qnty, uom, 0 as dtls_id, rate, amount, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id='2' ";
				else if($bill_id=="" && $del_id!="")
					$sql="SELECT 0 as upd_id, c.id as receive_id, a.receive_date, a.recv_number_prefix_num as challan_no, c.po_breakdown_id as order_id, b.prod_id, sum(c.quantity) as order_qnty, b.uom, c.dtls_id, 0 as rate, 0 as amount, null as remarks FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (2,22) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=13 and a.id not in (select id from inv_receive_master where knitting_source=3 AND entry_form=22 AND receive_basis in (1,6,9)) group by c.id, a.receive_date, a.recv_number_prefix_num, c.po_breakdown_id, b.prod_id, c.dtls_id";
			}
			else if($db_type==2)
			{*/
			if ($bill_id != "" && $del_id != "")
				$sql = "(SELECT id as upd_id, receive_date, challan_no, item_id as prod_id, body_part_id, febric_description_id, roll_no, receive_qty, rate, amount, remarks, order_id FROM subcon_outbound_bill_dtls WHERE challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2)
					 union 
					 (select 0 as upd_id, a.receive_date, a.recv_number_prefix_num as challan_no, c.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_no, sum(c.quantity) as receive_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and c.trans_type=1 and c.trans_id!=0 and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond  $del_febric_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id) order by challan_no DESC"; //
			/*$sql="(SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, receive_qty, uom, roll_no, 0 as dtls_id, rate, amount, null as body_part_id, febric_description_id, remarks  FROM subcon_outbound_bill_dtls  WHERE receive_id in ($bill_id) and process_id=2)
					 union 
					 (SELECT 0 as upd_id, b.id as receive_id, a.receive_date, a.recv_number_prefix_num as challan_no, c.po_breakdown_id as order_id, b.prod_id, sum(c.quantity) as receive_qty, null as uom, sum(b.no_of_roll) as roll_no, c.dtls_id,  sum(b.rate) as rate,  sum(b.amount) as amount, b.body_part_id, b.febric_description_id, null as remarks  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (2,22) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=13 group by b.id, a.receive_date, a.recv_number_prefix_num, b.body_part_id, b.febric_description_id, c.po_breakdown_id, b.prod_id, b.uom, c.dtls_id)";*/
			else if ($bill_id != "" && $del_id == "")
				$sql = "SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, receive_qty, uom, roll_no, 0 as dtls_id, rate, amount, null as body_part_id, null as febric_description_id, remarks FROM subcon_outbound_bill_dtls  WHERE challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and process_id='2' ";
			else if ($bill_id == "" && $del_id != "")
				$sql = "select 0 as upd_id, a.receive_date, a.recv_number_prefix_num as challan_no, c.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as receive_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and c.trans_type=1 and c.trans_id!=0 and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id order by a.recv_number_prefix_num DESC";

			//$sql="SELECT 0 as upd_id, b.id as receive_id, a.receive_date, a.recv_number_prefix_num as challan_no, c.po_breakdown_id as order_id, b.prod_id, sum(c.quantity) as receive_qty, null as uom, sum(b.no_of_roll) as roll_no, c.dtls_id, sum(b.rate) as rate, sum(b.amount) as amount, b.body_part_id, b.febric_description_id, null as remarks FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id = b.mst_id and b.id=c.dtls_id and c.trans_type=1 and c.entry_form in (2,22) AND a.knitting_source=3 AND c.id in ($del_id) and a.item_category=13 group by b.id, a.receive_date, a.recv_number_prefix_num, c.po_breakdown_id, b.prod_id, c.dtls_id, b.body_part_id, b.febric_description_id";
		}
		//}
	} else {
		if ($data[2] != "") {
			$sql = "SELECT id as upd_id, receive_id, receive_date, challan_no, order_id, item_id as prod_id, body_part_id, febric_description_id, receive_qty, uom, 0 as dtls_id, rate, amount, remarks FROM subcon_outbound_bill_dtls  WHERE mst_id=$data[2] and process_id=2";

			$sql_result_arr = sql_select($sql);
			foreach ($sql_result_arr as $row) {
				$update_chk_str[] = $row[csf("challan_no")] . '_' . $row[csf('order_id')] . '_' . $row[csf('item_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
				$issue_chk_str[] = $row[csf("challan_no")] . '_' . $row[csf('order_id')] . '_' . $row[csf('item_id')] . '_' . $row[csf('body_part_id')] . '_' . $row[csf('febric_description_id')];
			}
		} else {
			if ($bill_id != "" && $del_id != "")
				$sql = "(SELECT id as upd_id, receive_date, challan_no, item_id as prod_id, body_part_id, febric_description_id, roll_no, receive_qty, rate, amount, remarks, order_id FROM subcon_outbound_bill_dtls WHERE challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2)
						 union
						 (select 0 as upd_id, a.receive_date, a.recv_number_prefix_num as challan_no, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_no, sum(b.grey_receive_qnty) as receive_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=3 and a.entry_form in (2,22) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $del_challan_cond $wout_item_id_cond $body_part_cond $del_febric_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id) order by challan_no";
			else if ($bill_id != "" && $del_id == "")
				$sql = "select id as upd_id, delivery_date, challan_no, item_id as prod_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2 order by challan_no";
			else  if ($bill_id == "" && $del_id != "")
				$sql = "select 0 as upd_id, a.receive_date, a.recv_number_prefix_num as challan_no, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_no, sum(b.grey_receive_qnty) as receive_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=3 and a.entry_form in (2,22) and a.item_category=13 and a.recv_number_prefix_num in ($challan)  and b.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id order by a.recv_number_prefix_num";
			/*$sql="select a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_without_order=1 and a.knitting_source=1 and a.entry_form=22 and a.receive_basis=9 and a.item_category=13 and a.recv_number_prefix_num in($challan) and b.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id)
			
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id order by a.recv_number_prefix_num";*/
		}
	}
	echo $sql;
	$sql_result = sql_select($sql);
	$k = 0;
	$num_rowss = count($sql_result);
	foreach ($sql_result as $row) {
		$k++;
		if ($data[2] != "") {
			//if($data[1]=="") $data[1]=$row[csf("receive_id")]; else $data[1].=",".$row[csf("receive_id")];
			$data[1] = "";
			foreach ($issue_chk_str as $val) {
				if ($data[1] == "") $data[1] = $val;
				else $data[1] .= "," . $val;
			}
		}
	?>
		<tr align="center">
			<td>
				<? if ($k == $num_rowss) { ?>
					<input type="hidden" name="issue_id_all" id="issue_id_all" style="width:80px" value="<? echo $data[1]; ?>" />
					<input type="hidden" name="delete_id" id="delete_id" style="width:80px" value="<? echo $delete_id; ?>" />
				<? } ?>
				<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
				<input type="hidden" name="reciveid_<? echo $k; ?>" id="reciveid_<? echo $k; ?>" value="<? echo $row[csf("receive_id")]; ?>">
				<input type="date" name="txt_receive_date_<? echo $k; ?>" id="txt_receive_date_<? echo $k; ?>" class="text_boxes" style="width:80px" value="<? echo change_date_format($row[csf("receive_date")]); ?>" readonly />
			</td>
			<td>
				<input type="text" name="txt_challenno_<? echo $k; ?>" id="txt_challenno_<? echo $k; ?>" class="text_boxes" style="width:70px" value="<? echo $row[csf("challan_no")]; ?>" readonly />
			</td>
			<td>
				<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:60px" readonly />
				<input type="text" name="txt_orderno_<? echo $k; ?>" id="txt_orderno_<? echo $k; ?>" class="text_boxes" style="width:60px" value="<? echo $order_array[$row[csf("order_id")]]['po_number']; ?>" readonly />
			</td>
			<td>
				<input type="text" name="txt_stylename_<? echo $k; ?>" id="txt_stylename_<? echo $k; ?>" class="text_boxes" style="width:80px;" value="<? echo $order_array[$row[csf("order_id")]]['style_ref_no']; ?>" readonly />
			</td>
			<td>
				<input type="text" name="txt_partyname_<? echo $k; ?>" id="txt_partyname_<? echo $k; ?>" class="text_boxes" style="width:60px" value="<? echo $buyer_arr[$order_array[$row[csf("order_id")]]['buyer_name']]; ?>" readonly />
			</td>
			<td>
				<input name="txt_numberroll_<? echo $k; ?>" id="txt_numberroll_<? echo $k; ?>" type="text" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("roll_no")]; ?>" readonly />
			</td>
			<td>
				<input type="hidden" name="bodyPartId_<? echo $k; ?>" id="bodyPartId_<? echo $k; ?>" value="<? echo $row[csf("body_part_id")]; ?>">
				<input type="hidden" name="febDescId_<? echo $k; ?>" id="febDescId_<? echo $k; ?>" value="<? echo $row[csf("febric_description_id")]; ?>">
				<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("prod_id")]; ?>">
				<input type="text" name="txt_febricdesc_<? echo $k; ?>" id="txt_febricdesc_<? echo $k; ?>" class="text_boxes" style="width:100px" title="<? echo $item_id_arr[$row[csf("prod_id")]]; ?>" value="<? echo $item_id_arr[$row[csf("prod_id")]]; ?>" readonly />
			</td>
			<td>
				<input type="hidden" name="txtwonumid_<? echo $k; ?>" id="txtwonumid_<? echo $k; ?>" value="<? echo $row[csf("wo_num_id")]; ?>">
				<input type="text" name="text_wo_num_<? echo $k; ?>" id="text_wo_num_<? echo $k; ?>" class="text_boxes" style="width:60px" value="<? echo $booking_arr[$row[csf("wo_num_id")]]; ?>" placeholder="Browse" onDblClick="openmypage_wonum();" readonly />
			</td>
			<td>
				<input type="text" name="txt_qnty_<? echo $k; ?>" id="txt_qnty_<? echo $k; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("receive_qty")]; ?>" readonly />
			</td>
			<td>
				<? echo create_drop_down("cbouom_$k", 55, $unit_of_measurement, "", 0, "--Select UOM--", 12, "", 1, 0, ""); ?>
			</td>
			<td>
				<input type="text" name="txt_rate_<? echo $k; ?>" id="txt_rate_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("rate")]; ?>" onBlur="amount_caculation(<? echo $k; ?>);" />
			</td>
			<td>
				<?
				$total_amount = $row[csf("receive_qty")] * $row[csf("rate")];
				?>
				<input type="text" name="txt_amount_<? echo $k; ?>" id="txt_amount_<? echo $k; ?>" style="width:60px" class="text_boxes_numeric" value="<? echo  $total_amount; ?>" readonly />
			</td>
			<td>
				<input type="text" name="txt_remarks_<? echo $k; ?>" id="txt_remarks_<? echo $k; ?>" class="text_boxes" style="width:80px" value="<? echo $row[csf("remarks")]; ?>" />
			</td>
		</tr>
	<?
	}
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$bill_process_id = "2";
	$bill_for_id = str_replace("'", "", $cbo_bill_for);
	$is_sales = 0;
	if ($bill_for_id == 4) {
		$is_sales = 1;
	}

	if ($operation == 0)   // Insert Here========================================================================================receive_id
	{
		$con = connect();
		if ($db_type == 0) mysql_query("BEGIN");
		if ($db_type == 0) $year_cond = " and YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = " and TO_CHAR(insert_date,'YYYY')";

		$new_bill_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', 'KNT', date("Y", time()), 5, "select prefix_no,prefix_no_num from  subcon_outbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=" . date('Y', time()) . " order by id desc ", "prefix_no", "prefix_no_num"));

		if (str_replace("'", '', $update_id) == "") {
			$id = return_next_id("id", "subcon_outbound_bill_mst", 1);
			$field_array = "id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id,party_source_id, bill_for,upcharge,discount, party_bill_no, process_id, inserted_by, insert_date";
			$data_array = "(" . $id . ",'" . $new_bill_no[1] . "','" . $new_bill_no[2] . "','" . $new_bill_no[0] . "'," . $cbo_company_id . "," . $cbo_location_name . "," . $txt_bill_date . "," . $cbo_supplier_company . "," . $cbo_party_source . "," . $cbo_bill_for . "," . $txt_upcharge . "," . $txt_discount . "," . $txt_party_bill_no . ",'" . $bill_process_id . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')"; //cbo_party_source*cbo_party_name

			$return_no = $new_bill_no[0];
		} else {
			$id = str_replace("'", '', $update_id);
			$field_array = "bill_no*company_id*location_id*bill_date*supplier_id*party_source_id*bill_for*upcharge*discount*party_bill_no*updated_by*update_date";
			$data_array = "" . $txt_bill_no . "*" . $cbo_company_id . "*" . $cbo_location_name . "*" . $txt_bill_date . "*" . $cbo_supplier_company . "*" . $cbo_party_source . "*" . $cbo_bill_for . "*" . $txt_upcharge . "*" . $txt_discount . "*" . $txt_party_bill_no . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			$return_no = str_replace("'", '', $txt_bill_no);
		}
		$id1 = return_next_id("id", "subcon_outbound_bill_dtls", 1);
		$field_array1 = "id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, body_part_id, febric_description_id, roll_no, wo_num_id, receive_qty, rec_qty_pcs, uom, rate, amount, remarks, process_id,is_sales, inserted_by, insert_date, currency_id";
		$field_array_up = "receive_id*receive_date*challan_no*order_id*item_id*body_part_id*febric_description_id*roll_no*receive_qty*rec_qty_pcs*rate*amount*remarks*currency_id*updated_by*update_date";
		$add_comma = 0;
		for ($i = 1; $i <= $tot_row; $i++) {
			$reciveid = "reciveid_" . $i;
			$receive_date = "txtReceiveDate_" . $i;
			$challen_no = "txtChallenno_" . $i;
			$orderid = "ordernoid_" . $i;
			$style_name = "txtStylename_" . $i;
			$buyer_name = "txtPartyname_" . $i;
			$item_id = "itemid_" . $i;
			$bodyPartId = "bodyPartId_" . $i;
			$febDescId = "febDescId_" . $i;
			$wo_number = "txtwonumid_" . $i;
			$number_roll = "txtNumberroll_" . $i;
			$quantity = "txtQnty_" . $i;
			$txtqntypcs = "txtqntypcs_" . $i;
			$rate = "txtRate_" . $i;
			$amount = "txtAmount_" . $i;
			$remarks = "txtRemarks_" . $i;
			$cbo_uom = "cbouom_" . $i;
			$update_id_dtls = "updateiddtls_" . $i;
			$curanci = "curanci_" . $i;
			if ($db_type == 2) {
				$change_date = change_date_format(str_replace("'", '', $$receive_date), '', '', 1);
			}

			if ($db_type == 0) {
				$change_date = change_date_format(str_replace("'", "", $$receive_date), "yyyy-mm-dd", "-", 1);
			}

			if (str_replace("'", '', $$update_id_dtls) == "") {
				if ($add_comma != 0) $data_array1 .= ",";
				$data_array1 .= "(" . $id1 . "," . $id . "," . $$reciveid . ",'" . $change_date . "'," . $$challen_no . "," . $$orderid . "," . $$item_id . "," . $$bodyPartId . "," . $$febDescId . "," . $$number_roll . "," . $$wo_number . "," . $$quantity . "," . $$txtqntypcs . "," . $$cbo_uom . "," . $$rate . "," . $$amount . "," . $$remarks . "," . $bill_process_id . "," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$curanci . ")";
				$id1 = $id1 + 1;
				$add_comma++;
			} else {
				$id_arr[] = str_replace("'", '', $$update_id_dtls);
				$data_array_up[str_replace("'", '', $$update_id_dtls)] = explode("*", ("" . $$receive_id . "*" . $$receive_date . "*" . $$challen_no . "*" . $$orderid . "*" . $$item_id . "*" . $$bodyPartId . "*" . $$febDescId . "*" . $$number_roll . "*" . $$wo_number . "*" . $$quantity . "*" . $$txtqntypcs . "*" . $$cbo_uom . "*" . $$rate . "*" . $$amount . "*" . $$remarks . "*" . $bill_process_id . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
				//$id_arr_delivery[]=str_replace("'",'',$$receive_id);
				//$data_array_delivery[str_replace("'",'',$$receive_id)] =explode("*",("1"));
			}
		}
		$flag = 1;

		if (str_replace("'", '', $update_id) == "") {
			//echo "INSERT INTO subcon_outbound_bill_mst (".$field_array.") VALUES ".$data_array; die;
			$rID = sql_insert("subcon_outbound_bill_mst", $field_array, $data_array, 0);
			if ($rID == 1 && $flag == 1) $flag = 1;
			else $flag = 0;
		} else {
			$rID = sql_update("subcon_outbound_bill_mst", $field_array, $data_array, "id", $update_id, 0);
			if ($rID == 1 && $flag == 1) $flag = 1;
			else $flag = 0;
		}

		if ($data_array1 != "") {
			//echo "insert into subcon_outbound_bill_dtls(".$field_array1.") values ".$data_array1;die;
			$rID1 = sql_insert("subcon_outbound_bill_dtls", $field_array1, $data_array1, 0);
			if ($rID1 == 1 && $flag == 1) $flag = 1;
			else $flag = 0;
		}
		//check_table_status( $_SESSION['menu_id'],0);
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			}
		} else if ($db_type == 2) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here=============================================================================
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$sql_dtls = "Select id from subcon_outbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray = sql_select($sql_dtls);
		foreach ($nameArray as $row) {
			$dtls_update_id_array[] = $row[csf('id')];
		}

		$id = str_replace("'", '', $update_id);
		$field_array = "bill_date*party_bill_no*upcharge*discount*updated_by*update_date";
		$data_array = "" . $txt_bill_date . "*" . $txt_party_bill_no . "*" . $txt_upcharge . "*" . $txt_discount . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$return_no = str_replace("'", '', $txt_bill_no);

		$id1 = return_next_id("id", "subcon_outbound_bill_dtls", 1);
		$field_array1 = "id, mst_id, receive_id, receive_date, challan_no, order_id, item_id, body_part_id, febric_description_id, roll_no, wo_num_id, receive_qty, rec_qty_pcs, uom, rate, amount, remarks, process_id,is_sales, inserted_by, insert_date, currency_id";
		$field_array_up = "receive_id*receive_date*challan_no*order_id*item_id*body_part_id*febric_description_id*roll_no*wo_num_id*receive_qty*rec_qty_pcs*uom*rate*amount*remarks*process_id*currency_id";
		$add_comma = 0;
		for ($i = 1; $i <= $tot_row; $i++) {
			$receive_date = "txtReceiveDate_" . $i;
			$challen_no = "txtChallenno_" . $i;
			$orderid = "ordernoid_" . $i;
			$style_name = "txtStylename_" . $i;
			$buyer_name = "txtPartyname_" . $i;
			$item_id = "itemid_" . $i;
			$bodyPartId = "bodyPartId_" . $i;
			$febDescId = "febDescId_" . $i;
			$wo_number = "txtwonumid_" . $i;
			$number_roll = "txtNumberroll_" . $i;
			$quantity = "txtQnty_" . $i;
			$txtqntypcs = "txtqntypcs_" . $i;
			$rate = "txtRate_" . $i;
			$amount = "txtAmount_" . $i;
			$remarks = "txtRemarks_" . $i;
			$cbo_uom = "cbouom_" . $i;
			$reciveid = "reciveid_" . $i;
			$update_id_dtls = "updateiddtls_" . $i;
			$curanci = "curanci_" . $i;
			if ($db_type == 2) {
				$change_date = change_date_format(str_replace("'", '', $$receive_date), 'mm-dd-yyyy', '/', 1);
			}
			if ($db_type == 0) {
				$change_date = change_date_format(str_replace("'", "", $$receive_date), "yyyy-mm-dd", "-", 1);
			}
			if (str_replace("'", '', $$update_id_dtls) == "") {
				if ($add_comma != 0) $data_array1 .= ",";
				$data_array1 .= "(" . $id1 . "," . $id . "," . $$reciveid . ",'" . $change_date . "'," . $$challen_no . "," . $$orderid . "," . $$item_id . "," . $$bodyPartId . "," . $$febDescId . "," . $$number_roll . "," . $$wo_number . "," . $$quantity . "," . $$txtqntypcs . "," . $$cbo_uom . "," . $$rate . "," . $$amount . "," . $$remarks . "," . $bill_process_id . "," . $is_sales . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$curanci . ")";
				$id1 = $id1 + 1;
				$add_comma++;
			} else {
				$id_arr[] = str_replace("'", '', $$update_id_dtls);

				$data_array_up[str_replace("'", '', $$update_id_dtls)] = explode("*", ("" . $$reciveid . "*'" . $change_date . "'*" . $$challen_no . "*" . $$orderid . "*" . $$item_id . "*" . $$bodyPartId . "*" . $$febDescId . "*" . $$number_roll . "*" . $$wo_number . "*" . $$quantity . "*" . $$txtqntypcs . "*" . $$cbo_uom . "*" . $$rate . "*" . $$amount . "*" . $$remarks . "*" . $bill_process_id . "*" . $$curanci . ""));
			}
		}

		$flag = 1;
		$rID = sql_update("subcon_outbound_bill_mst", $field_array, $data_array, "id", $update_id, 0);
		if ($rID == 1 && $flag == 1) $flag = 1;
		else $flag = 0;

		//echo bulk_update_sql_statement2( "subcon_outbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );die;
		$rID1 = execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id", $field_array_up, $data_array_up, $id_arr));
		if ($rID1 == 1 && $flag == 1) $flag = 1;
		else $flag = 0;

		if ($data_array1 != "") {
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1 = sql_insert("subcon_outbound_bill_dtls", $field_array1, $data_array1, 1);
			if ($rID1 == 1 && $flag == 1) $flag = 1;
			else $flag = 0;
		}

		if (implode(',', $id_arr) != "") {
			$distance_delete_id = implode(',', array_diff($dtls_update_id_array, $id_arr));
		} else {
			$distance_delete_id = implode(',', $dtls_update_id_array);
		}
		if (str_replace("'", '', $distance_delete_id) != "") {
			$rID3 = execute_query("delete from subcon_outbound_bill_dtls where id in ($distance_delete_id)", 0);
			if ($rID3 == 1 && $flag == 1) $flag = 1;
			else $flag = 0;
		}

		check_table_status($_SESSION['menu_id'], 0);
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			}
		} else if ($db_type == 2) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", '', $id) . "**" . str_replace("'", '', $return_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "remarks_popup") {
	echo load_html_head_contents("Remarks", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(val) {
			document.getElementById('text_new_remarks').value = val;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<fieldset style="width:400px;margin-left:4px;">
				<form name="remarksfrm_1" id="remarksfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="370">
						<tr>
							<td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
								<textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character."><? echo $data; ?></textarea>
							</td>
						</tr>
						<tr>
							<td align="center">
								<input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
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

if ($action == "job_popup") {
	echo load_html_head_contents("Job Popup Info", "../../", 1, 1, $unicode, '', '');
	$data = explode("_", $data);
	$bill_for = $data[2];
	$supplier = $data[3];

?>
	<script>
		function js_set_value(id) {
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value = id;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">

			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Company Name</th>
							<th width="150">Location</th>
							<th width="80">Job Year</th>
							<th width="110">Job No</th>
							<th width="110">Order No</th>
							<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<input type="hidden" id="selected_job">
								<?
								echo create_drop_down("cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );", 1);
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "--Select Location--", $data[1], "", 1, "", "", "", "", 3);
								?>
							</td>
							<td align="center">
								<? echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");  ?>
							</td>
							<td align="center">
								<input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Search Job" />
							</td>
							<td align="center">
								<input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Search Order" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+<? echo $bill_for; ?>+'_'+<? echo $supplier; ?>+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value,'create_job_search_list_view','search_div','outside_knitting_bill_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" valign="top" id="">
								<div id="search_div"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_job_search_list_view") {
	$data = explode('_', $data);
	$company_name = str_replace("'", "", $data[0]);
	$location_name = str_replace("'", "", $data[1]);
	$bill_for = str_replace("'", "", $data[2]);
	$supplier = str_replace("'", "", $data[3]);
	$year = str_replace("'", "", $data[4]);
	$search_job = str_replace("'", "", $data[5]);
	$search_order = trim(str_replace("'", "", $data[6]));

	if ($search_job == "" && $search_order == "") {
		echo "<p style='text-align:center;'>Please Give Job or Order.</p>";
		die;
	} else {
		if ($search_job != '') $search_job_cond = " and a.job_no_prefix_num like '%$search_job%'";
		else $search_job_cond = "";
		if ($search_order != '') $search_order_cond = " and b.po_number like '%$search_order%'";
		else $search_order_cond = "";
	}

	$date_cond = "";
	if ($db_type == 0) {
		$booking_without_order = "IFNULL(a.booking_without_order,0)";
		$date_sql = "YEAR(a.insert_date) as year";

		if ($year > 0) {
			$date_cond = " and YEAR(a.insert_date)=$year";
		}
	} else if ($db_type == 2) {
		$booking_without_order = "nvl(a.booking_without_order,0)";
		$date_sql = "TO_CHAR(a.insert_date,'YYYY') as year";

		if ($year > 0) {
			$date_cond = " and TO_CHAR(a.insert_date,'YYYY')=$year";
		}
	}

	$po_sql = "SELECT distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.company_id='$data[0]' and a.knitting_company='$data[3]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and c.trans_id!=0 and $booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.po_breakdown_id Desc";

	foreach (sql_select($po_sql) as $value) {
		$po_arr[$value[csf("po_breakdown_id")]] = $value[csf("po_breakdown_id")];
	}

	if (count($po_arr) > 999) {
		if ($db_type == 0) {
			$po_conds = "and b.id in (" . trim(implode(',', array_filter($po_arr)), ',') . ")";
		} else if ($db_type == 2) {
			$chunked_arr = array_chunk(array_filter($po_arr), 999);
			$po_conds = " and (";
			foreach ($chunked_arr as $po) {
				$po_conds .= "b.id in (" . implode(',', $po) . ") or ";
			}
			$po_conds = chop($po_conds, " or ");
			$po_conds .= ")";
		}
	} else {
		$po_conds = "and b.id in (" . trim(implode(',', array_filter($po_arr)), ',') . ")";
	}

	$sql = "SELECT a.id as job_id, a.job_no, a.job_no_prefix_num, $date_sql, a.company_name, a.location_name, a.buyer_name as party_id, b.id, b.job_no_mst, b.po_number as order_no, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_conds $search_job_cond $search_order_cond $date_cond order by a.id DESC";

	echo  create_list_view("list_view", "Job No,Year,Order No,Shipment Date", "100,100,100,150", "550", "350", 0, $sql, "js_set_value", "job_no,job_id", "", 1, "0,0,0,0", $arr, "job_no_prefix_num,year,order_no,shipment_date", "", '', '0,0,0,0');
	exit();
}

if ($action == "outbound_knitting_bill_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$yearn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');

	$sql_mst = "Select id, bill_no, bill_date, supplier_id, location_id, bill_for,upcharge,discount, party_bill_no from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";

	$booking_arr = return_library_array("select id,booking_no from wo_booking_mst", 'id', 'booking_no');
	$dataArray = sql_select($sql_mst);

	$mst_id = $dataArray[0][csf('id')];
	$billFor = $dataArray[0][csf('bill_for')];
	$partyBillNo = $dataArray[0][csf('party_bill_no')];
	$sql_result = sql_select("select id, receive_id as delivery_id, receive_date as delivery_date, challan_no, order_id, item_id, uom, roll_no as packing_qnty, receive_qty as delivery_qty, rec_qty_pcs as delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id,wo_num_id, febric_description_id from subcon_outbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by id ASC");
	//echo "select id, receive_id as delivery_id, receive_date as delivery_date, challan_no, order_id, item_id, uom, roll_no as packing_qnty, receive_qty as delivery_qty, rec_qty_pcs as delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_outbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by id ASC";
	//echo "select id, receive_id as delivery_id, receive_date as delivery_date, challan_no, order_id, item_id, uom, roll_no as packing_qnty, receive_qty as delivery_qty, rec_qty_pcs as delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_outbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by id ASC";
	$po_id_arr = array();
	$reciveid = "";
	foreach ($sql_result as $row) {
		$po_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		$receive_id_arr[$row[csf('delivery_id')]] = $row[csf('delivery_id')];
		$reciveid .= $row[csf('delivery_id')] . ',';
	}
?>
	<div style="width:1150px; margin-left:20px;">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[str_replace("'", "", $data[0])]; ?>' height='70' width='200' />
				</td>
				<td>
					<table width="800" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
						</tr>
						<tr>
							<td align="center" style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<td align="center" class="form_caption"><? echo show_company($data[0], '', ''); ?></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
			</tr>
		</table>
		</td>
		</tr>
		</table>
		<table width="1150" cellspacing="0" align="" border="0">
			<tr>
				<td width="150" valign="top"><strong>Bill No :</strong></td>
				<td width="200"><? echo $dataArray[0][csf('bill_no')]; ?></td>
				<td width="150"><strong>Bill Date: </strong></td>
				<td width="200px"><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
				<td width="150"><strong>Source :</strong></td>
				<td>Out-bound Subcontract</td>
			</tr>
			<tr>
				<?
				$party_add = $dataArray[0][csf('supplier_id')];
				$nameArray = sql_select("select address_1, web_site, email, country_id from lib_supplier where id=$party_add");
				foreach ($nameArray as $result) {
					$address = "";
					if ($result != "") $address = $result[csf('address_1')];
				}
				$party_name = $party_library[$dataArray[0][csf('supplier_id')]];
				$party_location = $address;
				?>
				<td><strong>Party Name: </strong></td>
				<td style="word-break:break-all"><? echo $party_name; ?></td>
				<td><strong>Party Location: </strong></td>
				<td style="word-break:break-all"><? echo $party_location; ?></td>
				<td><strong>Bill For : </strong></td>
				<td style="word-break:break-all"><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Party Bill No : </strong></td>
				<td style="word-break:break-all"><?php echo $partyBillNo; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table cellspacing="0" width="1515" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:16px">
					<th width="30">SL</th>
					<th width="40">Sys. Challan</th>
					<th width="40">Rec. Challan</th>
					<th width="55">Ch. Date</th>
					<th width="60">Order</th>
					<th width="60">Buyer</th>
					<th width="90">Booking/ Del/Prod No</th>
					<th width="60">Style</th>
					<th width="40">Job</th>
					<th width="100">WO Num</th>
					<th width="50">Internal Ref.</th>
					<th width="35">Job Year</th>
					<th width="150">Fabric Description</th>

					<th width="100">Fabric Color</th>
					<th width="40">Feeder</th>
					<th width="40">Yarn Count</th>
					<th width="40">MC Dia</th>
					<th width="40">MC Gauge</th>

					<th width="25">Roll</th>
					<th width="55">R. Qty (W)</th>
					<th width="55">R. Qty (P)</th>
					<th width="30">UOM</th>
					<th width="40">Rate</th>
					<th width="70">Amount</th>
					<th width="70">Currency</th>
					<th>Remarks</th>
				</thead>
				<?
				if ($db_type == 0) $job_year = "YEAR(a.insert_date) as year";
				else $job_year = "to_char(a.insert_date,'YYYY') as year";

				$order_array = array();
				$internal_ref_arr = array();
				if ($billFor != 3) {
					$job_sql = "select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, (b.po_quantity) as po_quantity, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0 and b.id in(" . implode(',', $po_id_arr) . ")"; //and a.company_name=$data[0]
					$job_sql_result = sql_select($job_sql);
					foreach ($job_sql_result as $row) {
						$order_array[$row[csf('id')]]['buyer_name'] = $buyerArr[$row[csf('buyer_name')]];
						$order_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
						$order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
						$order_array[$row[csf('id')]]['job'] = $row[csf('job_no')];
						$order_array[$row[csf('id')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
						$order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
						$order_array[$row[csf('id')]]['plan_cut'] = $row[csf('plan_cut')];
						$order_array[$row[csf('id')]]['ratio'] = $row[csf('ratio')];
						$order_array[$row[csf('id')]]['year'] = $row[csf('year')];
						$job_array[$row[csf('job_no')]] = $row[csf('job_no')];
					}
					unset($job_sql_result);

					$internal_ref_sql = "select job_no, internal_ref from wo_order_entry_internal_ref where job_no in('" . implode("','", $job_array) . "')";
					$internal_ref_sql_result = sql_select($internal_ref_sql);
					foreach ($internal_ref_sql_result as $row) {
						$internal_ref_arr[$row[csf('job_no')]][$row[csf('internal_ref')]] = $row[csf('internal_ref')];
					}
				}
				$const_comp_arr = return_library_array("select id,product_name_details from product_details_master", 'id', 'product_name_details');

				$knit_plan_arr = array();
				$knit_plan = "select id, feeder from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 and feeder!=0";
				$knit_plan_res = sql_select($knit_plan);
				foreach ($knit_plan_res as $row) {
					$knit_plan_arr[$row[csf('id')]] = $row[csf('feeder')];
				}
				unset($knit_plan_res);

				$production_arr = array();
				$production_sql = sql_select("select id, booking_id, booking_no from inv_receive_master where company_id='" . $dataArray[0][csf('supplier_id')] . "' and knitting_company='$data[0]' and location_id='" . $dataArray[0][csf('location_id')] . "' and entry_form in (2) and receive_basis=2 and status_active=1 and is_deleted=0");
				foreach ($production_sql as $row) {
					$production_arr[$row[csf('id')]] = $feeder[$knit_plan_arr[$row[csf('booking_id')]]];
				}
				unset($production_sql);

				$rec_data_arr = array();
				$recChallan_arr = array();
				$bookingNoArr = array();

				$res_sql = "select c.barcode_no,a.id, a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no, b.prod_id, b.body_part_id, b.febric_description_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='$data[0]' and a.knitting_company='" . $dataArray[0][csf('supplier_id')] . "' and a.location_id='" . $dataArray[0][csf('location_id')] . "' and a.id=b.mst_id and b.id=c.DTLS_ID and a.id=c.mst_id and b.trans_id > 0 and a.entry_form in (2,22,58)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in(" . implode(',', $receive_id_arr) . ")";
				$res_sql_res = sql_select($res_sql);
				foreach ($res_sql_res as $row) {
					$barCodeArr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
					$bookingNoArr[$row[csf('id')]]['bookno'] = $row[csf('booking_no')];
				}

				$barCodeWiseProductionSql = "select c.barcode_no, c.booking_no, b.id, b.yarn_count, b.color_id, b.machine_dia, b.machine_gg from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='" . $data[0] . "' and a.knitting_company='" . $dataArray[0][csf('supplier_id')] . "'  and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.barcode_no in(" . implode(',', $barCodeArr) . ")"; //and a.location_id='".$dataArray[0][csf('location_id')]."'

				$barCodeWiseProductionResult = sql_select($barCodeWiseProductionSql);
				foreach ($barCodeWiseProductionResult as $row) {
					$barCodeDataArr[$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
					$barCodeDataArr[$row[csf('barcode_no')]]['color_id'] = $row[csf('color_id')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'] = $row[csf('machine_dia')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'] = $row[csf('machine_gg')];
					$barCodeDataArr[$row[csf('barcode_no')]]['plan_id'] = $row[csf('booking_no')];
				}
				//var_dump($barCodeDataArr);
				foreach ($res_sql_res as $row) {
					$sys_challan = $row[csf('id')];
					$recChallan_arr[$sys_challan][change_date_format($row[csf('receive_date')])] = $row[csf('challan_no')];

					$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'] .= $barCodeDataArr[$row[csf('barcode_no')]]['yarn_count'] . ',';
					$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'] .= $barCodeDataArr[$row[csf('barcode_no')]]['color_id'] . ',';
					$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia'] .= $barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'] . ',';
					$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg'] .= $barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'] . ',';

					if (($row[csf('receive_basis')] == 9 || $row[csf('receive_basis')] == 10) && ($row[csf('entry_form')] == 22 || $row[csf('entry_form')] == 58)) {
						$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'] .= $feeder[$knit_plan_arr[$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']]] . ',';
					} else if ($row[csf('receive_basis')] == 2 && $row[csf('entry_form')] == 2) {
						$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'] .= $production_arr[$row[csf('booking_id')]] . ',';
					}
				}
				unset($res_sql_res);
				if ($dataArray[0][csf('bill_for')] == 3) {
					$buyer_id_arr = array();
					$sql_non_booking = sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='" . $dataArray[0][csf('party_id')] . "' and knitting_company='$data[0]' and location_id='" . $dataArray[0][csf('location_id')] . "' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
					foreach ($sql_non_booking as $row) {
						$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])] = $party_library[$row[csf('buyer_id')]];
					}

					$reciveIds = implode(",", array_filter(array_unique(explode(",", $reciveid))));
					$reciveIdsCond = "";
					$receive_ids = count(explode(",", $reciveIds));
					if ($db_type == 2 && $receive_ids > 1000) {
						$reciveIdsCond = " and (";
						$reciveIdsArr = array_chunk(explode(",", $reciveIds), 999);
						foreach ($reciveIdsArr as $ids) {
							$ids = implode(",", $ids);
							$reciveIdsCond .= " a.id in($ids) or";
						}
						$reciveIdsCond = chop($reciveIdsCond, 'or ');
						$reciveIdsCond .= ")";
					} else $reciveIdsCond = " and a.id in($reciveIds)";

					if ($db_type == 0) {
						$booking_without_order = "IFNULL(c.booking_without_order,0)";
						$barcode_no_cond = "group_concat(c.barcode_no)";
					} else if ($db_type == 2) {
						$booking_without_order = "nvl(c.booking_without_order,0)";
						$barcode_no_cond = "rtrim(xmlagg(xmlelement(e,c.barcode_no,',').extract('//text()') order by c.barcode_no).GetClobVal(),',')";
					}

					$sql = "select a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, $barcode_no_cond as barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $reciveIdsCond
			group by a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id";
					//echo $sql;
					$sql_resultRec = sql_select($sql);
					$bookingNo_arr = array();
					$barcode_no = '';
					$barcodeWiseRecIdArr = array();

					foreach ($sql_resultRec as $row) {
						if ($db_type == 2) $row[csf('barcode_no')] = $row[csf('barcode_no')]->load();

						$bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
						$barcode_no .= $row[csf('barcode_no')] . ',';
						$exbarcode = explode(",", $row[csf('barcode_no')]);
						foreach ($exbarcode as $barcode) {
							$barcodeWiseRecIdArr[$barcode] = $row[csf('id')];
						}
					}
					unset($sql_resultRec);

					$barcode_nos = chop($barcode_no, ',');
					$barcode_nosCond = "";
					$barcode_count = count(array_unique(explode(",", $barcode_nos)));
					if ($db_type == 2 && $barcode_count > 1000) {
						$barcode_nosCond = " and (";
						$barcode_nosArr = array_chunk(explode(",", $barcode_nos), 999);
						foreach ($barcode_nosArr as $ids) {
							$ids = implode(",", $ids);
							$barcode_nosCond .= " c.barcode_no in($ids) or";
						}
						$barcode_nosCond = chop($barcode_nosCond, 'or ');
						$barcode_nosCond .= ")";
					} else $barcode_nosCond = " and c.barcode_no in($barcode_nos)";

					$sql_roll = "select a.id, a.receive_basis, a.booking_no, a.booking_id, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2) and c.entry_form in (2) and a.item_category=13 and a.receive_basis in (1,2) and b.trans_id=0
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_nosCond";
					//echo $sql_roll;
					$sql_rollRes = sql_select($sql_roll);
					$planid = "";
					$is_plan = 0;
					$planBooking_arr = array();
					$bookingNo = "";
					$is_booking = 0;
					$bookingWiseStyleBuyerArr = array();
					foreach ($sql_rollRes as $row) {
						if ($row[csf('receive_basis')] == 1) //WO/Booking Based
						{
							$bookingNo .= "'" . $row[csf('booking_no')] . "',";
							$is_booking = 1;
							$bookingNo_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
						}
						if ($row[csf('receive_basis')] == 2) //Kniting Plan
						{
							$is_plan = 1;
							$planid .= $row[csf('booking_id')] . ',';
							$planBooking_arr[$row[csf('booking_id')]]['prodPlan'] .= $row[csf('barcode_no')] . ',';
						}
						$bookingWiseStyleBuyerArr[$row[csf('booking_no')]] = $barcodeWiseRecIdArr[$row[csf('barcode_no')]];
					}
					unset($sql_rollRes);
					$styleBuyer_arr = array();
					if ($is_booking == 1) {
						$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
						$bookingNosCond = "";
						$booking_nos = count(array_unique(explode(",", $bookingNos)));
						if ($db_type == 2 && $booking_nos > 1000) {
							$bookingNosCond = " and (";
							$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
							foreach ($bookingNosArr as $ids) {
								$ids = implode(",", $ids);
								$bookingNosCond .= " a.booking_no in($ids) or";
							}
							$bookingNosCond = chop($bookingNosCond, 'or ');
							$bookingNosCond .= ")";
						} else $bookingNosCond = " and a.booking_no in($bookingNos)";

						$sampleSql = "select a.booking_no, c.grouping, b.style_ref_no, b.buyer_name, b.internal_ref from wo_non_ord_samp_booking_dtls a, sample_development_mst b, wo_non_ord_samp_booking_mst c where a.style_id=b.id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $bookingNosCond";
						$sampleSqlRes = sql_select($sampleSql);
						foreach ($sampleSqlRes as $row) {
							if ($row[csf('internal_ref')] == "") $row[csf('internal_ref')] = $row[csf('grouping')];
							$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style'] = $row[csf('style_ref_no')];
							$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer'] = $buyerArr[$row[csf('buyer_name')]];
							$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['internal_ref'] = $row[csf('internal_ref')];
						}
						unset($sampleSqlRes);
						//print_r($styleBuyer_arr);
					}

					if ($is_plan == 1) {
						$planIds = chop($planid, ',');
						$plan_idsCond = "";
						$plan_ids = count(array_unique(explode(",", $planIds)));
						if ($db_type == 2 && $plan_ids > 1000) {
							$plan_idsCond = " and (";
							$planIdsArr = array_chunk(explode(",", $planIds), 999);
							foreach ($planIdsArr as $ids) {
								$ids = implode(",", $ids);
								$plan_idsCond .= " b.id in($ids) or";
							}
							$plan_idsCond = chop($production_idsCond, 'or ');
							$plan_idsCond .= ")";
						} else $plan_idsCond = " and b.id in($planIds)";

						$plan_booking_arr = array();
						$knit_booking = "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 $plan_idsCond";
						$knit_booking_result = sql_select($knit_booking);
						$bookingNo = "";
						$is_booking = 0;
						foreach ($knit_booking_result as $row) {
							$bookingNo .= "'" . $row[csf('booking_no')] . "',";
							$is_booking = 1;
							$exbarcodeNo = explode(",", chop($planBooking_arr[$row[csf('id')]]['prodPlan'], ','));
							foreach ($exbarcodeNo as $barcodeNo) {
								$bookingNo_arr[$barcodeNo] = $row[csf('booking_no')];
							}
						}
						unset($knit_booking_result);

						if ($is_booking == 1) {
							$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
							$bookingNosCond = "";
							$booking_nos = count(array_unique(explode(",", $bookingNos)));
							if ($db_type == 2 && $booking_nos > 1000) {
								$bookingNosCond = " and (";
								$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
								foreach ($bookingNosArr as $ids) {
									$ids = implode(",", $ids);
									$bookingNosCond .= " a.booking_no in($ids) or";
								}
								$bookingNosCond = chop($bookingNosCond, 'or ');
								$bookingNosCond .= ")";
							} else $bookingNosCond = " and a.booking_no in($bookingNos)";

							$sampleSql = "select a.booking_no, c.grouping, b.style_ref_no, b.buyer_name, b.internal_ref from wo_non_ord_samp_booking_dtls a, sample_development_mst b, wo_non_ord_samp_booking_mst c where a.style_id=b.id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $bookingNosCond";
							$sampleSqlRes = sql_select($sampleSql);
							foreach ($sampleSqlRes as $row) {
								if ($row[csf('internal_ref')] == "") $row[csf('internal_ref')] = $row[csf('grouping')];
								$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style'] = $row[csf('style_ref_no')];
								$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer'] = $buyerArr[$row[csf('buyer_name')]];
								$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['internal_ref'] = $row[csf('internal_ref')];
							}
							unset($sampleSqlRes);
						}
					}
				}



				$po_id = "";
				$i = 1;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					if ($po_id == "") $po_id = $row[csf('order_id')];
					else $po_id .= ',' . $row[csf('order_id')];
					$buyer_id_name = "";
					if ($dataArray[0][csf('bill_for')] == 3) {
						$buyer_id_name = $buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
					} else {
						$buyer_id_name = $order_array[$row[csf('order_id')]]['buyer_name'];
					}

					$fab_color = "";
					$feeder_str = "";
					$yarn_count = "";
					$mc_dia = '';
					$mc_gg = "";

					$fab_color = array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));

					$color_srt_arr = array();
					foreach ($fab_color as $color_id) {
						$color_srt_arr[$color_id] = $color_arr[$color_id];
					}
					$fab_color = implode(",", $color_srt_arr);

					$feeder_str = implode(",", array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					$yarn_count_id = array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					foreach ($yarn_count_id as $count_id) {
						if ($yarn_count == "") $yarn_count = $yearn_count_arr[$count_id];
						else $yarn_count .= ', ' . $yearn_count_arr[$count_id];
					}
					$yarn_count = implode(",", explode(',', $yarn_count));

					$mc_dia = implode(",", array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg = implode(",", array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
					$buyerNameStr = "";
					$styleRef = "";
					$intRef = "";
					if ($dataArray[0][csf('bill_for')] == 3) {
						$buyerNameStr = $styleBuyer_arr[$row[csf('delivery_id')]]['buyer'];
						$styleRef = $styleBuyer_arr[$row[csf('delivery_id')]]['style'];
						$intRef = $styleBuyer_arr[$row[csf('delivery_id')]]['internal_ref'];
					} else {
						$buyerNameStr = $buyer_id_name;
						$styleRef = $order_array[$row[csf('order_id')]]['style_ref_no'];
						$intRef = implode(',', $internal_ref_arr[$order_array[$row[csf('order_id')]]['job']]);
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px">
						<td><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
						<td style="word-break:break-all"><? echo $recChallan_arr[$row[csf('delivery_id')]][change_date_format($row[csf('delivery_date')])]; ?></td>
						<td style="word-break:break-all"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></td>
						<td style="word-break:break-all"><? echo $buyerNameStr; ?></td>
						<td style="word-break:break-all"><?= $bookingNoArr[$row[csf('delivery_id')]]['bookno']; ?></td>
						<td style="word-break:break-all"><? echo $styleRef; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
						<td align="center" style="word-break:break-all"><? echo $booking_arr[$row[csf('wo_num_id')]]; ?></td>
						<td align="center" style="word-break:break-all"><? echo $intRef; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['year']; ?></td>
						<td style="word-break:break-all"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
						<td align="center" style="word-break:break-all"><? echo $fab_color; ?></td>
						<td>
							<div style="word-wrap:break-word; width:60px"><? echo $feeder_str; ?></div>
						</td>
						<td align="center">
							<div style="word-wrap:break-word; width:40px"><? echo $yarn_count; ?></div>
						</td>
						<td align="center">
							<div style="word-wrap:break-word; width:40px"><? echo $mc_dia; ?></div>
						</td>
						<td align="center">
							<div style="word-wrap:break-word; width:40px"><? echo $mc_gg; ?></div>
						</td>

						<td align="right">
							<p><? echo $row[csf('packing_qnty')];
								$tot_packing_qty += $row[csf('packing_qnty')]; ?>&nbsp;</p>
						</td>
						<td align="right">
							<p><b><? echo number_format($row[csf('delivery_qty')], 2, '.', '');
									$tot_delivery_qty += $row[csf('delivery_qty')]; ?>&nbsp;</b></p>
						</td>
						<td align="right">
							<p><b><? echo number_format($row[csf('delivery_qtypcs')], 2, '.', '');
									$tot_delivery_qtypcs += $row[csf('delivery_qtypcs')]; ?>&nbsp;</b></p>
						</td>
						<td>
							<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
						</td>
						<td align="right">
							<p><? echo number_format($row[csf('rate')], 4, '.', ''); ?>&nbsp;</p>
						</td>
						<td align="right">
							<p><b><? echo number_format($row[csf('amount')], 2, '.', '');
									$total_amount += $row[csf('amount')]; ?>&nbsp;</b></p>
						</td>

						<td align="center">
							<p><? echo $currency[$row[csf('currency_id')]]; ?></p>
						</td>
						<td align="center">
							<p><? echo $row[csf('remarks')]; ?></p>
						</td>

						<?
						$carrency_id = $row['currency_id'];
						if ($carrency_id == 1) {
							$paysa_sent = "Paisa";
						} else if ($carrency_id == 2) {
							$paysa_sent = "CENTS";
						}
						?>
					</tr>
				<?
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="18"><strong>Total</strong></td>
					<td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
					<td align="right"><b><? echo number_format($tot_delivery_qty, 2, '.', ''); ?></b></td>
					<td align="right"><b><? echo number_format($tot_delivery_qtypcs, 2, '.', ''); ?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><b><? echo $format_total_amount = number_format($total_amount, 2, '.', ''); ?></b></td>
					<td>&nbsp;</td>
				</tr>
				<tr style="font-size:15px">
					<td align="right" colspan="23"><strong>Upcharge</strong></td>
					<td align="right">
						<?
						echo $dataArray[0][csf('upcharge')];
						?>
						&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="font-size:15px">
					<td align="right" colspan="23"><strong>Discount</strong></td>
					<td align="right"><? echo $dataArray[0][csf('discount')]; ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="font-size:15px">
					<td align="right" colspan="23"><strong>Net total</strong></td>
					<td align="right">
						<?
						$upcharge = $dataArray[0][csf('upcharge')];
						$discount = $dataArray[0][csf('discount')];
						$tot_up = $total_amount + $upcharge;
						$net_total = $tot_up - $discount;
						echo $format_total_amount = number_format($net_total, 2, '.', '');
						?>
						&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
				<tr>
					<td colspan="25" align="left"><b>In Word: <? echo number_to_words($format_total_amount, $currency[$carrency_id], $paysa_sent); ?></b></td>
				</tr>
			</table>
			<?
			$bill_no = $dataArray[0][csf('bill_no')];
			$sql_terms = "Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms = sql_select($sql_terms);

			$i = 1;
			if (count($result_sql_terms) > 0) {
			?>
				<table width="930" align="left">
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><b>TERMS & CONDITION</b></td>
					</tr>
					<?
					foreach ($result_sql_terms as $rows) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30"><? echo $i; ?></td>
							<td>
								<p><? echo $rows[csf('terms')]; ?></p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</table><?
					}
						?>
			<br>
			<? if ($data[4] == 1) {
				if ($dataArray[0][csf('bill_for')] != 3) {
			?>
					<table align="left" cellspacing="0" width="980" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd" align="center">
							<th width="120">Order No</th>
							<th width="110">Buyer Name</th>
							<th width="100">Grey Required (KG)</th>
							<th width="100">Charge Required (USD)</th>
							<th width="100">Bill Qty (KG)</th>
							<th width="100">Bill Amount (USD)</th>
							<th width="100">Balance Qty (KG)</th>
							<th>Balance Amount (USD)</th>
						</thead>
						<tbody>
							<?
							$grey_req_arr = array();
							$grey_req_sql = "select po_break_down_id, avg(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
							$grey_req_sql_result = sql_select($grey_req_sql);
							foreach ($grey_req_sql_result as $row) {
								$grey_req_arr[$row[csf('po_break_down_id')]] = $row[csf('grey_req')];
							}

							$charge_req_arr = array();
							$charge_req_sql = "select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no";
							$charge_req_sql_result = sql_select($charge_req_sql);
							foreach ($charge_req_sql_result as $row) {
								$charge_req_arr[$row[csf('job_no')]] = $row[csf('charge_req')];
							}

							$bill_arr = array();
							$bill_sql = "select b.order_id, sum(b.receive_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
							$bill_sql_result = sql_select($bill_sql);
							foreach ($bill_sql_result as $row) {
								$bill_arr[$row[csf('order_id')]]['bill_qty'] = $row[csf('bill_qty')];
								$bill_arr[$row[csf('order_id')]]['bill_amount'] = $row[csf('bill_amount')];
							}

							$currency_rate = set_conversion_rate(2, $dataArray[0][csf('bill_date')]);
							$costingper_id_arr = return_library_array("select job_no,costing_per_id from wo_pre_cost_dtls", 'job_no', 'costing_per_id');

							$ex_po = array_unique(explode(",", $po_id));
							$k = 1;
							foreach ($ex_po as $po_id) {
								if ($k % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
								$po_quantity = $order_array[$po_id]['po_quantity'];
								$plan_cut = $order_array[$po_id]['plan_cut'];

								$costing_per_id = $costingper_id_arr[$order_array[$po_id]['job']];
								$dzn_qnty = 0;
								if ($costing_per_id == 1) $dzn_qnty = 12;
								else if ($costing_per_id == 3) $dzn_qnty = 12 * 2;
								else if ($costing_per_id == 4) $dzn_qnty = 12 * 3;
								else if ($costing_per_id == 5) $dzn_qnty = 12 * 4;
								else $dzn_qnty = 1;
								$dzn_qnty_req = $dzn_qnty;
								$grey_req = ($grey_req_arr[$po_id] / $dzn_qnty_req) * $plan_cut;

								//($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
								$charge_req = (($charge_req_arr[$order_array[$po_id]['job']] / $dzn_qnty_req) * $plan_cut);

								//($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
								$bill_qty = $bill_arr[$po_id]['bill_qty'];
								$bill_amount = $bill_arr[$po_id]['bill_amount'] / $currency_rate;
								$balance_qty = $grey_req - $bill_qty;
								$balance_amount = $charge_req - $bill_amount;
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td style="word-break:break-all"><? echo $order_array[$po_id]['po_number']; ?></td>
									<td style="word-break:break-all"><? echo $order_array[$po_id]['buyer_name']; ?></td>

									<td style="word-break:break-all" align="right" title="<? echo $grey_req_arr[$po_id] . '=' . $charge_req_arr[$order_array[$po_id]['job']]; ?>"><? echo number_format($grey_req, 2, '.', ''); ?></td>
									<td style="word-break:break-all" align="right"><? echo number_format($charge_req, 2, '.', ''); ?></td>

									<td style="word-break:break-all" align="right"><? echo number_format($bill_qty, 2, '.', ''); ?></td>
									<td style="word-break:break-all" align="right"><? echo number_format($bill_amount, 2, '.', ''); ?></td>

									<td style="word-break:break-all" align="right"><? echo number_format($balance_qty, 2, '.', ''); ?></td>
									<td style="word-break:break-all" align="right"><? echo number_format($balance_amount, 2, '.', ''); ?></td>
								</tr>
							<?
							}
							?>
						</tbody>
					</table>
			<? }
			} ?>
			<br>
			<? echo signature_table(175, $data[0], "980px"); ?>
		</div>
	</div>
<?
	exit();
}
if ($action == "outbound_knitting_bill_fso_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$yearn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');

	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');

	$sql_mst = "Select id, bill_no, bill_date, supplier_id, location_id, bill_for,upcharge,discount, party_bill_no from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";

	$booking_arr = return_library_array("select id,booking_no from wo_booking_mst", 'id', 'booking_no');
	$dataArray = sql_select($sql_mst);

	$mst_id = $dataArray[0][csf('id')];
	$billFor = $dataArray[0][csf('bill_for')];
	$partyBillNo = $dataArray[0][csf('party_bill_no')];
	$sql_result = sql_select("select c.job_no_prefix_num as job_no,c.po_buyer,c.po_job_no, b.id, b.receive_id as delivery_id,b.is_sales, b.receive_date as delivery_date, b.challan_no, b.order_id, b.item_id, b.uom, b.roll_no as packing_qnty, b.receive_qty as delivery_qty, b.rec_qty_pcs as delivery_qtypcs, b.rate, b.amount, b.remarks, b.currency_id, b.process_id, b.body_part_id,b.wo_num_id, b.febric_description_id from subcon_outbound_bill_dtls b,fabric_sales_order_mst c  where b.order_id=c.id  and b.mst_id='$mst_id' and b.process_id='2' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id ASC");
	foreach ($sql_result as $row) {
		$po_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		$is_sales = $row[csf('is_sales')];
		$po_job_no .= "'" . $row[csf('po_job_no')] . "'" . ',';
		$receive_id_arr[$row[csf('delivery_id')]] = $row[csf('delivery_id')];
		$reciveid .= $row[csf('delivery_id')] . ',';
	}
	$sql_result_fso = sql_select("select c.job_no,c.po_buyer,c.po_job_no, b.id, b.receive_id as delivery_id,b.is_sales, b.receive_date as delivery_date, b.challan_no, b.order_id, b.item_id, b.uom, b.roll_no as packing_qnty, b.receive_qty as delivery_qty, a.grey_qty as grey_qty, b.rate, b.amount, b.remarks, b.currency_id, b.process_id, b.body_part_id,b.wo_num_id, b.febric_description_id from subcon_outbound_bill_dtls b,fabric_sales_order_mst c,fabric_sales_order_dtls a  where b.order_id=c.id  and a.mst_id=c.id and b.febric_description_id=a.determination_id and a.mst_id=b.order_id and b.mst_id='$mst_id' and b.process_id='2' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id ASC");

	foreach ($sql_result_fso as $row) {
		$fso_no_arr[$row[csf('order_id')]]['job_no'] = $row[csf('job_no')];
		$fso_no_arr[$row[csf('order_id')]]['grey_qty'] += $row[csf('grey_qty')];
		$fso_no_arr[$row[csf('order_id')]]['po_job_no'] = $row[csf('po_job_no')];
	}
	$po_job_no = rtrim($po_job_no, ",");

	//echo $is_sales.'D';
?>
	<div style="width:1150px; margin-left:20px;">
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td width="70" align="right">
					<img src='../../<? echo $imge_arr[str_replace("'", "", $data[0])]; ?>' height='70' width='200' />
				</td>
				<td>
					<table width="800" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
						</tr>
						<tr>
							<td align="center" style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<td align="center" class="form_caption"><? echo show_company($data[0], '', ''); ?></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
			</tr>
		</table>
		</td>
		</tr>
		</table>
		<table width="1150" cellspacing="0" align="" border="0">
			<tr>
				<td width="150" valign="top"><strong>Bill No :</strong></td>
				<td width="200"><? echo $dataArray[0][csf('bill_no')]; ?></td>
				<td width="150"><strong>Bill Date: </strong></td>
				<td width="200px"><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
				<td width="150"><strong>Source :</strong></td>
				<td>Out-bound Subcontract</td>
			</tr>
			<tr>
				<?
				$party_add = $dataArray[0][csf('supplier_id')];
				$nameArray = sql_select("select address_1, web_site, email, country_id from lib_supplier where id=$party_add");
				foreach ($nameArray as $result) {
					$address = "";
					if ($result != "") $address = $result[csf('address_1')];
				}
				$party_name = $party_library[$dataArray[0][csf('supplier_id')]];
				$party_location = $address;
				?>
				<td><strong>Party Name: </strong></td>
				<td style="word-break:break-all"><? echo $party_name; ?></td>
				<td><strong>Party Location: </strong></td>
				<td style="word-break:break-all"><? echo $party_location; ?></td>
				<td><strong>Bill For : </strong></td>
				<td style="word-break:break-all"><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Party Bill No : </strong></td>
				<td style="word-break:break-all"><?php echo $partyBillNo; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table cellspacing="0" width="1515" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:16px">
					<th width="30">SL</th>
					<th width="40">Sys. Challan</th>
					<th width="40">Rec. Challan</th>
					<th width="55">Ch. Date</th>
					<th width="60">FSO No</th>
					<th width="60">Buyer</th>
					<th width="90">Booking/ Del/Prod No</th>
					<th width="60">Style</th>
					<th width="40">Job</th>
					<th width="100">WO Num</th>
					<th width="50">Internal Ref.</th>
					<th width="35">Job Year</th>
					<th width="150">Fabric Description</th>

					<th width="100">Fabric Color</th>


					<th width="25">Roll</th>
					<th width="55">R. Qty (W)</th>
					<th width="55">R. Qty (P)</th>
					<th width="30">UOM</th>
					<th width="40">Rate</th>
					<th width="70">Amount</th>
					<th width="70">Currency</th>
					<th>Remarks</th>
				</thead>
				<?
				if ($db_type == 0) $job_year = "YEAR(a.insert_date) as year";
				else $job_year = "to_char(a.insert_date,'YYYY') as year";

				$order_array = array();
				$internal_ref_arr = array();
				if ($billFor != 3) {
					//job_no_prefix_num
					$job_sql = "select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, (b.po_quantity) as po_quantity, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0 and a.job_no in(" . $po_job_no . ")"; //and a.company_name=$data[0]
					$job_sql_result = sql_select($job_sql);
					foreach ($job_sql_result as $row) {
						$order_array[$row[csf('job_no')]]['buyer_name'] = $buyerArr[$row[csf('buyer_name')]];
						$order_array[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
						$order_array[$row[csf('job_no')]]['po_number'] = $row[csf('po_number')];
						$order_array[$row[csf('job_no')]]['job'] = $row[csf('job_no')];
						$order_array[$row[csf('job_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
						$order_array[$row[csf('job_no')]]['po_quantity'] = $row[csf('po_quantity')];
						$order_array[$row[csf('job_no')]]['plan_cut'] = $row[csf('plan_cut')];
						$order_array[$row[csf('job_no')]]['ratio'] = $row[csf('ratio')];
						$order_array[$row[csf('job_no')]]['year'] = $row[csf('year')];
						$job_array[$row[csf('job_no')]] = $row[csf('job_no')];
					}
					/* echo '<pre>';
			print_r($order_array);die;  */
					unset($job_sql_result);
					$sql_result_fabric_sales = sql_select("select c.sales_booking_no,c.po_job_no from fabric_sales_order_mst c where c.po_job_no in('" . implode("','", $job_array) . "')");
					foreach ($sql_result_fabric_sales as $row) {
						$fabric_sales_arr[$row[csf('po_job_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
					}

					$internal_ref_sql = "select job_no, internal_ref from wo_order_entry_internal_ref where job_no in('" . implode("','", $job_array) . "')";
					$internal_ref_sql_result = sql_select($internal_ref_sql);
					foreach ($internal_ref_sql_result as $row) {
						$internal_ref_arr[$row[csf('job_no')]][$row[csf('internal_ref')]] = $row[csf('internal_ref')];
					}
					$sql_wo = "select a.wo_no,c.po_job_no from knitting_work_order_mst a,knitting_work_order_dtls b,fabric_sales_order_mst c where a.id=b.mst_id  and b.fabric_sales_order_no=c.job_no and c.po_job_no in('" . implode("','", $job_array) . "') and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 ";
					$sql_wo_result = sql_select($sql_wo);
					foreach ($sql_wo_result as $row) {
						$wo_arr[$row[csf('po_job_no')]]['wo_no'] = $row[csf('wo_no')];
					}
				}
				$const_comp_arr = return_library_array("select id,product_name_details from product_details_master", 'id', 'product_name_details');


				$rec_data_arr = array();
				$recChallan_arr = array();
				$bookingNoArr = array();

				$res_sql = "select a.id, a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no, b.prod_id, b.body_part_id, b.febric_description_id,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.company_id='$data[0]' and a.knitting_company='" . $dataArray[0][csf('supplier_id')] . "' and a.location_id='" . $dataArray[0][csf('location_id')] . "' and a.id=b.mst_id and b.trans_id > 0 and a.entry_form in (2,22,58)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in(" . implode(',', $receive_id_arr) . ")";
				$res_sql_res = sql_select($res_sql);
				foreach ($res_sql_res as $row) {
					$bookingNoArr[$row[csf('id')]]['bookno'] = $row[csf('booking_no')];
					$recChallan_arr[$row[csf('id')]][change_date_format($row[csf('receive_date')])] = $row[csf('challan_no')];
					$rec_data_arr[$row[csf('id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'] .= $row[csf('color_id')] . ',';
				}


				if ($dataArray[0][csf('bill_for')] == 3) {
					$buyer_id_arr = array();
					$sql_non_booking = sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='" . $dataArray[0][csf('party_id')] . "' and knitting_company='$data[0]' and location_id='" . $dataArray[0][csf('location_id')] . "' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
					foreach ($sql_non_booking as $row) {
						$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])] = $party_library[$row[csf('buyer_id')]];
					}

					$reciveIds = implode(",", array_filter(array_unique(explode(",", $reciveid))));
					$reciveIdsCond = "";
					$receive_ids = count(explode(",", $reciveIds));
					if ($db_type == 2 && $receive_ids > 1000) {
						$reciveIdsCond = " and (";
						$reciveIdsArr = array_chunk(explode(",", $reciveIds), 999);
						foreach ($reciveIdsArr as $ids) {
							$ids = implode(",", $ids);
							$reciveIdsCond .= " a.id in($ids) or";
						}
						$reciveIdsCond = chop($reciveIdsCond, 'or ');
						$reciveIdsCond .= ")";
					} else $reciveIdsCond = " and a.id in($reciveIds)";

					if ($db_type == 0) {
						$booking_without_order = "IFNULL(c.booking_without_order,0)";
						$barcode_no_cond = "group_concat(c.barcode_no)";
					} else if ($db_type == 2) {
						$booking_without_order = "nvl(c.booking_without_order,0)";
						$barcode_no_cond = "rtrim(xmlagg(xmlelement(e,c.barcode_no,',').extract('//text()') order by c.barcode_no).GetClobVal(),',')";
					}

					$sql = "select a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, $barcode_no_cond as barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2,22,58) and c.entry_form in (2,22,58) and a.item_category=13 and a.receive_basis in (0,1,2,4,9,10,11) and b.trans_id>0
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $reciveIdsCond
			group by a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id";
					//echo $sql;
					$sql_resultRec = sql_select($sql);
					$bookingNo_arr = array();
					$barcode_no = '';
					$barcodeWiseRecIdArr = array();

					foreach ($sql_resultRec as $row) {
						if ($db_type == 2) $row[csf('barcode_no')] = $row[csf('barcode_no')]->load();

						$bookingNo_arr[$row[csf('id')]] = $row[csf('booking_no')];
						$barcode_no .= $row[csf('barcode_no')] . ',';
						$exbarcode = explode(",", $row[csf('barcode_no')]);
						foreach ($exbarcode as $barcode) {
							$barcodeWiseRecIdArr[$barcode] = $row[csf('id')];
						}
					}
					unset($sql_resultRec);

					$barcode_nos = chop($barcode_no, ',');
					$barcode_nosCond = "";
					$barcode_count = count(array_unique(explode(",", $barcode_nos)));
					if ($db_type == 2 && $barcode_count > 1000) {
						$barcode_nosCond = " and (";
						$barcode_nosArr = array_chunk(explode(",", $barcode_nos), 999);
						foreach ($barcode_nosArr as $ids) {
							$ids = implode(",", $ids);
							$barcode_nosCond .= " c.barcode_no in($ids) or";
						}
						$barcode_nosCond = chop($barcode_nosCond, 'or ');
						$barcode_nosCond .= ")";
					} else $barcode_nosCond = " and c.barcode_no in($barcode_nos)";

					$sql_roll = "select a.id, a.receive_basis, a.booking_no, a.booking_id, c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=3 and a.entry_form in (2) and c.entry_form in (2) and a.item_category=13 and a.receive_basis in (1,2) and b.trans_id=0
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_nosCond";
					//echo $sql_roll;
					$sql_rollRes = sql_select($sql_roll);
					$planid = "";
					$is_plan = 0;
					$planBooking_arr = array();
					$bookingNo = "";
					$is_booking = 0;
					$bookingWiseStyleBuyerArr = array();
					foreach ($sql_rollRes as $row) {
						if ($row[csf('receive_basis')] == 1) //WO/Booking Based
						{
							$bookingNo .= "'" . $row[csf('booking_no')] . "',";
							$is_booking = 1;
							$bookingNo_arr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
						}
						if ($row[csf('receive_basis')] == 2) //Kniting Plan
						{
							$is_plan = 1;
							$planid .= $row[csf('booking_id')] . ',';
							$planBooking_arr[$row[csf('booking_id')]]['prodPlan'] .= $row[csf('barcode_no')] . ',';
						}
						$bookingWiseStyleBuyerArr[$row[csf('booking_no')]] = $barcodeWiseRecIdArr[$row[csf('barcode_no')]];
					}
					unset($sql_rollRes);
					$styleBuyer_arr = array();
					if ($is_booking == 1) {
						$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
						$bookingNosCond = "";
						$booking_nos = count(array_unique(explode(",", $bookingNos)));
						if ($db_type == 2 && $booking_nos > 1000) {
							$bookingNosCond = " and (";
							$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
							foreach ($bookingNosArr as $ids) {
								$ids = implode(",", $ids);
								$bookingNosCond .= " a.booking_no in($ids) or";
							}
							$bookingNosCond = chop($bookingNosCond, 'or ');
							$bookingNosCond .= ")";
						} else $bookingNosCond = " and a.booking_no in($bookingNos)";

						$sampleSql = "select a.booking_no, c.grouping, b.style_ref_no, b.buyer_name, b.internal_ref from wo_non_ord_samp_booking_dtls a, sample_development_mst b, wo_non_ord_samp_booking_mst c where a.style_id=b.id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $bookingNosCond";
						$sampleSqlRes = sql_select($sampleSql);
						foreach ($sampleSqlRes as $row) {
							if ($row[csf('internal_ref')] == "") $row[csf('internal_ref')] = $row[csf('grouping')];
							$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style'] = $row[csf('style_ref_no')];
							$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer'] = $buyerArr[$row[csf('buyer_name')]];
							$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['internal_ref'] = $row[csf('internal_ref')];
						}
						unset($sampleSqlRes);
						//print_r($styleBuyer_arr);
					}

					if ($is_plan == 1) {
						$planIds = chop($planid, ',');
						$plan_idsCond = "";
						$plan_ids = count(array_unique(explode(",", $planIds)));
						if ($db_type == 2 && $plan_ids > 1000) {
							$plan_idsCond = " and (";
							$planIdsArr = array_chunk(explode(",", $planIds), 999);
							foreach ($planIdsArr as $ids) {
								$ids = implode(",", $ids);
								$plan_idsCond .= " b.id in($ids) or";
							}
							$plan_idsCond = chop($production_idsCond, 'or ');
							$plan_idsCond .= ")";
						} else $plan_idsCond = " and b.id in($planIds)";

						$plan_booking_arr = array();
						$knit_booking = "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 $plan_idsCond";
						$knit_booking_result = sql_select($knit_booking);
						$bookingNo = "";
						$is_booking = 0;
						foreach ($knit_booking_result as $row) {
							$bookingNo .= "'" . $row[csf('booking_no')] . "',";
							$is_booking = 1;
							$exbarcodeNo = explode(",", chop($planBooking_arr[$row[csf('id')]]['prodPlan'], ','));
							foreach ($exbarcodeNo as $barcodeNo) {
								$bookingNo_arr[$barcodeNo] = $row[csf('booking_no')];
							}
						}
						unset($knit_booking_result);

						if ($is_booking == 1) {
							$bookingNos = implode(",", array_unique(explode(",", chop($bookingNo, ','))));
							$bookingNosCond = "";
							$booking_nos = count(array_unique(explode(",", $bookingNos)));
							if ($db_type == 2 && $booking_nos > 1000) {
								$bookingNosCond = " and (";
								$bookingNosArr = array_chunk(explode(",", $bookingNos), 999);
								foreach ($bookingNosArr as $ids) {
									$ids = implode(",", $ids);
									$bookingNosCond .= " a.booking_no in($ids) or";
								}
								$bookingNosCond = chop($bookingNosCond, 'or ');
								$bookingNosCond .= ")";
							} else $bookingNosCond = " and a.booking_no in($bookingNos)";

							$sampleSql = "select a.booking_no, c.grouping, b.style_ref_no, b.buyer_name, b.internal_ref from wo_non_ord_samp_booking_dtls a, sample_development_mst b, wo_non_ord_samp_booking_mst c where a.style_id=b.id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $bookingNosCond";
							$sampleSqlRes = sql_select($sampleSql);
							foreach ($sampleSqlRes as $row) {
								if ($row[csf('internal_ref')] == "") $row[csf('internal_ref')] = $row[csf('grouping')];
								$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style'] = $row[csf('style_ref_no')];
								$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer'] = $buyerArr[$row[csf('buyer_name')]];
								$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['internal_ref'] = $row[csf('internal_ref')];
							}
							unset($sampleSqlRes);
						}
					}
				}



				$po_id = "";
				$i = 1;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					if ($po_id == "") $po_id = $row[csf('order_id')];
					else $po_id .= ',' . $row[csf('order_id')];
					$buyer_id_name = "";
					if ($dataArray[0][csf('bill_for')] == 3) {
						$buyer_id_name = $buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
					} else {
						$buyer_id_name = $order_array[$row[csf('po_job_no')]]['buyer_name'];
					}

					$fab_color = "";
					$feeder_str = "";
					$yarn_count = "";
					$mc_dia = '';
					$mc_gg = "";

					$fab_color = array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));

					$color_srt_arr = array();
					foreach ($fab_color as $color_id) {
						$color_srt_arr[$color_id] = $color_arr[$color_id];
					}
					$fab_color = implode(",", $color_srt_arr);

					$feeder_str = implode(",", array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					$yarn_count_id = array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					foreach ($yarn_count_id as $count_id) {
						if ($yarn_count == "") $yarn_count = $yearn_count_arr[$count_id];
						else $yarn_count .= ', ' . $yearn_count_arr[$count_id];
					}
					$yarn_count = implode(",", explode(',', $yarn_count));

					$mc_dia = implode(",", array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg = implode(",", array_filter(array_unique(explode(",", $rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
					$buyerNameStr = "";
					$styleRef = "";
					$intRef = "";
					if ($dataArray[0][csf('bill_for')] == 3) {
						$buyerNameStr = $styleBuyer_arr[$row[csf('delivery_id')]]['buyer'];
						$styleRef = $styleBuyer_arr[$row[csf('delivery_id')]]['style'];
						$intRef = $styleBuyer_arr[$row[csf('delivery_id')]]['internal_ref'];
					} else {
						$buyerNameStr = $buyer_id_name;
						$styleRef = $order_array[$row[csf('po_job_no')]]['style_ref_no'];
						$intRef = implode(',', $internal_ref_arr[$order_array[$row[csf('po_job_no')]]['job']]);
					}
					$bill_arr[$row[csf('order_id')]]['bill_qty'] += $row[csf('delivery_qty')];
					$bill_arr[$row[csf('order_id')]]['buyer'] = $buyerArr[$row[csf('po_buyer')]];
					//po_buyer,c.po_job_no
					//print_r($bill_arr);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px">
						<td><? echo $i; ?></td>
						<td style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
						<td style="word-break:break-all"><? echo $recChallan_arr[$row[csf('delivery_id')]][change_date_format($row[csf('delivery_date')])]; ?></td>
						<td style="word-break:break-all"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>
						<td style="word-break:break-all"><? echo $buyerArr[$row[csf('po_buyer')]]; ?></td>
						<td style="word-break:break-all"><?= $fabric_sales_arr[$row[csf('po_job_no')]]['sales_booking_no']; ?></td>
						<td style="word-break:break-all"><? echo $styleRef; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('po_job_no')]]['job']; // $row[csf('job_no_prefix_num')]; 
																		?></td>
						<td align="center" style="word-break:break-all"><? echo $wo_arr[$row[csf('po_job_no')]]['wo_no']; ?></td>
						<td align="center" style="word-break:break-all"><? echo $intRef; ?></td>
						<td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('po_job_no')]]['year']; ?></td>
						<td style="word-break:break-all"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
						<td align="center" style="word-break:break-all"><? echo $fab_color; ?></td>
						<td align="right">
							<p><? echo $row[csf('packing_qnty')];
								$tot_packing_qty += $row[csf('packing_qnty')]; ?>&nbsp;</p>
						</td>
						<td align="right">
							<p><b><? echo number_format($row[csf('delivery_qty')], 2, '.', '');
									$tot_delivery_qty += $row[csf('delivery_qty')]; ?>&nbsp;</b></p>
						</td>
						<td align="right">
							<p><b><? echo number_format($row[csf('delivery_qtypcs')], 2, '.', '');
									$tot_delivery_qtypcs += $row[csf('delivery_qtypcs')]; ?>&nbsp;</b></p>
						</td>
						<td>
							<p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p>
						</td>
						<td align="right">
							<p><? echo number_format($row[csf('rate')], 4, '.', ''); ?>&nbsp;</p>
						</td>
						<td align="right">
							<p><b><? echo number_format($row[csf('amount')], 2, '.', '');
									$total_amount += $row[csf('amount')]; ?>&nbsp;</b></p>
						</td>
						<td align="center">
							<p><? echo $currency[$row[csf('currency_id')]]; ?></p>
						</td>
						<td align="center">
							<p><? echo $row[csf('remarks')]; ?></p>
						</td>

						<?
						$carrency_id = $row['currency_id'];
						if ($carrency_id == 1) {
							$paysa_sent = "Paisa";
						} else if ($carrency_id == 2) {
							$paysa_sent = "CENTS";
						}
						?>
					</tr>
				<?
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="14"><strong>Total</strong></td>
					<td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
					<td align="right"><b><? echo number_format($tot_delivery_qty, 2, '.', ''); ?></b></td>
					<td align="right"><b><? echo number_format($tot_delivery_qtypcs, 2, '.', ''); ?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><b><? echo $format_total_amount = number_format($total_amount, 2, '.', ''); ?></b></td>
					<td>&nbsp;</td>
				</tr>
				<tr style="font-size:15px">
					<td align="right" colspan="19"><strong>Upcharge</strong></td>
					<td align="right">
						<?
						echo $dataArray[0][csf('upcharge')];
						?>
						&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="font-size:15px">
					<td align="right" colspan="19"><strong>Discount</strong></td>
					<td align="right"><? echo $dataArray[0][csf('discount')]; ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="font-size:15px">
					<td align="right" colspan="19"><strong>Net total</strong></td>
					<td align="right">
						<?
						$upcharge = $dataArray[0][csf('upcharge')];
						$discount = $dataArray[0][csf('discount')];
						$tot_up = $total_amount + $upcharge;
						$net_total = $tot_up - $discount;
						echo $format_total_amount = number_format($net_total, 2, '.', '');
						?>
						&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
				<tr>
					<td colspan="25" align="left"><b>In Word: <? echo number_to_words($format_total_amount, $currency[$carrency_id], $paysa_sent); ?></b></td>
				</tr>
			</table>
			<?
			$bill_no = $dataArray[0][csf('bill_no')];
			$sql_terms = "Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms = sql_select($sql_terms);

			$i = 1;
			if (count($result_sql_terms) > 0) {
			?>
				<table width="930" align="left">
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" align="center"><b>TERMS & CONDITION</b></td>
					</tr>
					<?
					foreach ($result_sql_terms as $rows) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30"><? echo $i; ?></td>
							<td>
								<p><? echo $rows[csf('terms')]; ?></p>
							</td>
						</tr>
					<?
						$i++;
					}
					?>
				</table><?
					}
						?>
			<br>
			<? if ($data[4] == 1) {
				if ($dataArray[0][csf('bill_for')] != 3) {
			?>
					<table align="left" cellspacing="0" width="980" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd" align="center">
							<th width="120">FSO No</th>
							<th width="110">Buyer Name</th>
							<th width="100">Grey Required (KG)</th>
							<th width="100">Bill Qty (KG)</th>
							<th width="100">Balance Qty (KG)</th>
						</thead>
						<tbody>
							<?


							// $bill_arr=array(); 
							// $bill_sql="select b.order_id, sum(b.receive_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
							// $bill_sql_result =sql_select($bill_sql);
							// foreach($bill_sql_result as $row)
							// {
							// 	$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
							// 	$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
							// }

							$currency_rate = set_conversion_rate(2, $dataArray[0][csf('bill_date')]);
							//$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');

							$ex_po = array_unique(explode(",", $po_id));
							$k = 1;
							///print_r($ex_po);
							foreach ($ex_po as $po_id) {
								if ($k % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
								$po_quantity = $order_array[$po_id]['po_quantity'];
								$grey_req = $fso_no_arr[$po_id]['grey_qty'];
								$fso_no = $fso_no_arr[$po_id]['job_no'];
								$job_no = $fso_no_arr[$po_id]['po_job_no'];
								$bill_qty = $bill_arr[$po_id]['bill_qty'];
								$balance_qty = $grey_req - $bill_qty;
							?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td style="word-break:break-all"><? echo $fso_no; ?></td>
									<td style="word-break:break-all"><? echo $bill_arr[$po_id]['buyer']; ?></td>

									<td style="word-break:break-all" align="right" title="<? // echo $grey_req_arr[$po_id].'='.$charge_req_arr[$order_array[$po_id]['job']]; 
																							?>"><? echo number_format($grey_req, 2, '.', ''); ?></td>
									<td style="word-break:break-all" align="right"><? echo number_format($bill_qty, 2, '.', ''); ?></td>
									<td style="word-break:break-all" align="right"><? echo number_format($balance_qty, 2, '.', ''); ?></td>

								</tr>
							<?
							}
							?>
						</tbody>
					</table>
			<? }
			} ?>
			<br>
			<? echo signature_table(175, $data[0], "980px"); ?>
		</div>
	</div>
<?
	exit();
}

if ($action == "file_upload") {
	header("Content-Type: application/json");
	$filename = time() . $_FILES['file']['name'];
	$location = "../../file_upload/" . $filename;
	//echo "0**".$filename; die;
	$uploadOk = 1;
	if (empty($mst_id)) {
		$mst_id = $_GET['mst_id'];
	}

	if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
		$uploadOk = 1;
	} else {
		$uploadOk = 0;
	}
	// echo "0**".$uploadOk; die;
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	$id = return_next_id("id", "COMMON_PHOTO_LIBRARY", 1);
	$data_array .= "(" . $id . "," . $mst_id . ",'outside_knitting_bill_entry','file_upload/" . $filename . "','2','" . $filename . "','" . $pc_date_time . "')";
	$field_array = "id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID = sql_insert("COMMON_PHOTO_LIBRARY", $field_array, $data_array, 1);

	if ($db_type == 0) {
		if ($rID == 1 && $uploadOk == 1) {
			mysql_query("COMMIT");
			echo "0**" . $new_system_id[0] . "**" . $mst_id;
		} else {
			mysql_query("ROLLBACK");
			echo "10**" . $mst_id;
		}
	} else if ($db_type == 2 || $db_type == 1) {
		if ($rID == 1 && $uploadOk == 1) {
			oci_commit($con);
			echo "0**" . $new_system_id[0] . "**" . $mst_id;
		} else {
			oci_rollback($con);
			echo "10**" . $rID . "**" . $uploadOk . "**INSERT INTO COMMON_PHOTO_LIBRARY(" . $field_array . ") VALUES " . $data_array;
		}
	}
	disconnect($con);
	die;
}
?>