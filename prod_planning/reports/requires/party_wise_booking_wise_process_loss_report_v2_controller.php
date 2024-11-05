<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];





$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");


if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
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
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref", 3 => "Order No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**', 'create_job_no_search_list_view', 'search_div', 'party_wise_booking_wise_process_loss_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "a.job_no_prefix_num";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else
		$search_field = "b.po_number";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $buyer_arr);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
		{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
    if ($db_type == 0)
    	$select_field = "group_concat(b.po_number) as po_number";
    else if ($db_type == 2)
    	$select_field = "listagg(cast(b.po_number as varchar(4000)), ',') within group (order by b.po_number) as po_number";

    $sql = "select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $select_field, b.pub_shipment_date 
    from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond 
    group by a.id, a.job_no, a.insert_date,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.pub_shipment_date 
    order by a.id, b.pub_shipment_date";
   //echo $sql;	
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "320", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}


if ($action == "booking_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
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
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>Booking Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref", 3 => "Order No", 4 => "Booking No");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no;?>" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'party_wise_booking_wise_process_loss_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if ($action == "create_booking_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$job_no_id = $data[6];
	//echo $job_no_id.'d';
	$cbo_year = "";

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "a.job_no";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else if($search_by ==3)
		$search_field = "b.po_number";
	else if($search_by ==4)
		$search_field = "d.booking_no";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and d.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and d.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $buyer_arr);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
		{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
    if ($db_type == 0)
    	$select_field = "group_concat(b.po_number) as po_number";
    else if ($db_type == 2)
    	$select_field = "listagg(cast(b.po_number as varchar(4000)), ',') within group (order by b.po_number) as po_number";

    $sql = "select d.id as id,a.company_name,a.buyer_name,d.booking_no,d.booking_date
    from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and c.job_no=b.job_no_mst and c.booking_no=d.booking_no and a.job_no=c.job_no  and d.fabric_source=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond 
    group by d.id,a.company_name,a.buyer_name, d.booking_date,d.booking_no
    order by d.id";
    //echo $sql;	
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Booking No,Booking Date", "80,80,130,100", "660", "420", 0, $sql, "js_set_value_job", "id,booking_no", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,booking_no,booking_date", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}
if ($action == "po_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
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
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>Po Received Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref", 3 => "Order No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no;?>" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_po_no_search_list_view', 'search_div', 'party_wise_booking_wise_process_loss_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if ($action == "create_po_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$job_no_id = $data[6];
	//echo $job_no_id.'d';
	$cbo_year = "";

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "" . trim($data[3]) . "";

	if ($search_by == 1)
		$search_field = "a.job_no";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else if($search_by ==3)
		$search_field = "b.po_number";
	

	$start_date = $data[4];
	$end_date = $data[5];

	$search_cond='';
	if(!empty($data[3]))
	{
		$search_cond=" and ".$search_field." = '$search_string'";
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and b.po_received_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and b.po_received_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $buyer_arr);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
		{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
    if ($db_type == 0)
    	$select_field = "group_concat(b.po_number) as po_number";
    else if ($db_type == 2)
    	$select_field = "listagg(cast(b.po_number as varchar(4000)), ',') within group (order by b.po_number) as po_number";

    $sql = "select b.id as id,a.company_name,a.buyer_name,a.job_no,b.po_number
from wo_po_details_master a, wo_po_break_down b
where a.job_no=b.job_no_mst and    a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $date_cond 
    group by b.id,a.company_name,a.buyer_name,a.job_no,b.po_number
    order by b.id";
    //echo $sql;	
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,PO", "80,80,130,100", "660", "420", 0, $sql, "js_set_value_job", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,job_no,po_number", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}
if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
     
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_dyeing_source==3)
	{
		$sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(1,9,20,21,24) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name";
	}
	elseif($cbo_dyeing_source==1)
	{
		$sql="select id, company_name as party_name from lib_company comp where  status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 


if ($action == "report_generate")
{
	$process = array(&$_POST);
	//print_r($process);die;
	extract(check_magic_quote_gpc($process));

	$txt_job_no = str_replace("'", "", $txt_job_no); 
	$report_type = str_replace("'", "", $report_type);
	$companyID = str_replace("'", "", $cbo_company_name);
	$txt_booking_no = str_replace("'", "", $txt_booking_no);
	$internalRefNo = str_replace("'", "", $txt_internal_ref_no);
	$hide_booking_id = str_replace("'", "", $hide_booking_id);
	$cbo_dyeing_source = str_replace("'", "", $cbo_dyeing_source);
	$hide_party_id = str_replace("'", "", $hide_party_id);
	$hide_po_id = str_replace("'", "", $hide_po_id);
	$txt_po_no = str_replace("'", "", $txt_po_no);

	function where_con($arrayData,$dataType=0,$table_coloum){
			$chunk_list_arr=array_chunk($arrayData,999);
			$p=1;
			foreach($chunk_list_arr as $process_arr)
			{
				if($dataType==0){
					if($p==1){$sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
					else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
				}
				else{
					if($p==1){$sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
					else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
				}
				$p++;
			}
			
			$sql.=") ";
			return $sql;
	}

	$fabricData = sql_select("select FABRIC_ROLL_LEVEL from variable_settings_production where company_name ='$companyID' and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
	
	$roll_maintained_yesNo = $fabricData[0]['FABRIC_ROLL_LEVEL'];

	
	$sourc_cond="";
	if($cbo_dyeing_source>0)
	{
		$sourc_cond=" and b.knitting_source=$cbo_dyeing_source";
	}
	$party_cond="";
	if(!empty($hide_party_id))
	{
		$party_cond=" and b.knitting_party in (".$hide_party_id.")";
	}

	$po_search_cond="";

	if(!empty($hide_po_id) && !empty($txt_po_no))
	{
		$po_ids=explode(",", $hide_po_id);
		//$po_search_cond=" and c.id in(".$hide_po_id.")";
		$po_search_cond=where_con($po_ids,0,"c.id");

	}
	

	if($companyID>0) $company_cond="and a.company_id=$companyID"; else $company_cond="";
	if($companyID>0) $company_cond_2="and a.company_name=$companyID"; else $company_cond_2="";
	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "") 
	{

		$jobArr = explode("*",str_replace("'", "", trim($txt_job_no)));

		foreach($jobArr as $job)
		{
			$job_nos .= "'".$job."',";
		}
		$job_nos = chop($job_nos,",");
		$job_cond = " and b.job_no in ( $job_nos )" ;

	}
	if ($internalRefNo!="") {$internalRefNo_cond="and c.grouping='$internalRefNo'";}else{$internalRefNo_cond="";}
	$booking_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "") 
	{
		$booking_cond = " and a.booking_no = $txt_booking_no";    
	}
	$booking_cond = "";
	if (str_replace("'", "", trim($hide_booking_id)) != "") 
	{
		$booking_cond = " and a.id in($hide_booking_id)";    
	}

	$booking_date_cond = "";
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		$booking_date_cond = " and a.booking_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";

	} 

	if ($report_type == 1) 
	{
		// Main SQL ====>
		$main_sql = "SELECT a.id, a.company_id,a.booking_no,a.uom, a.is_short,b.job_no,a.buyer_id, b.po_break_down_id,c.grouping, c.po_number, b.grey_fab_qnty as qnty,d.style_ref_no
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and c.job_no_mst=d.job_no and d.job_no=d.job_no and a.fabric_source=1 
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4)
			and b.grey_fab_qnty>0 $job_cond $booking_cond $booking_date_cond $company_cond $internalRefNo_cond $po_search_cond
			order by  a.job_no,a.buyer_id,a.booking_no";
		//echo $main_sql;die;

		$result = sql_select($main_sql);
		
		$data_summury = array();
		$data_array = array();
		foreach($result as $row)
		{
			$poArr[$row[csf("po_break_down_id")]] = $row[csf("po_number")];
			$internalRefArr[$row[csf("po_break_down_id")]]["internalRefNumber"]=$row[csf("grouping")];

			$data_summury[$row[csf("buyer_id")]]["qnty"] += $row[csf("qnty")];
			$data_summury[$row[csf("buyer_id")]]["job_no"] .= "'".$row[csf("job_no")]."',";
			$data_summury[$row[csf("buyer_id")]]["booking_no"] .= "'".$row[csf("booking_no")]."',";

			$data_array[$row[csf("booking_no")]]['ref_no'] .= $row[csf("grouping")].",";
			$data_array[$row[csf("booking_no")]]['po_id'] .= $row[csf("po_break_down_id")].",";
			$data_array[$row[csf("booking_no")]]['po_number'] .= $row[csf("po_number")].",";
			$data_array[$row[csf("booking_no")]]['style_ref_no']= $row[csf("style_ref_no")];
			$data_array[$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$data_array[$row[csf("booking_no")]]['uom'] = $row[csf("uom")];

			$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			
			$job_po_no_arr[$row[csf("po_break_down_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$job_po_no_arr[$row[csf("po_break_down_id")]]['grouping'].= $row[csf("grouping")].",";
			//$job_po_no_arr[$row[csf("po_break_down_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			//$job_po_no_arr[$row[csf("po_break_down_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			
			$booking_nos_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$all_po_ids_for_buyer_issue .= $row[csf("po_break_down_id")].","; 
			
			
		}
		unset($result);
		//echo "h";
		//print_r($data_array['OG-Fb-20-00325']);die;
		
		$job_numbers="'".implode("','",$job_no_arr)."'";
		$booking_nos="'".implode("','",$booking_nos_arr)."'";

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		$job_number_arr = array_unique(explode(",",chop($job_numbers,",")));
		$booking_nos = implode(",",array_unique(explode(",",chop($booking_nos,","))));
		$booking_nos_arr = array_unique(explode(",",chop($booking_nos,",")));

		$alloc_booking_cond = "";$requisition_booking_cond="";$issue_booking_cond="";$production_booking_cond="";

		if($booking_nos != "")
		{
			//$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
			$alloc_booking_cond = where_con($booking_nos_arr,0,"a.booking_no");
			$requisition_booking_cond = where_con($booking_nos_arr,0,"c.booking_no");
			//$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
			$issue_booking_cond =where_con($booking_nos_arr,0,"d.booking_no");
			//$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
			$production_booking_cond = where_con($booking_nos_arr,0,"d.booking_no");
			//$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";

			
		}


		$alloc_job_cond = "";$program_job_cond="";$requistion_job_cond="";
		if($job_numbers != "")
		{
			//$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
			$alloc_job_cond = where_con($job_number_arr,0,"a.job_no");
			$program_job_cond = where_con($job_number_arr,0,"c.job_no");
			//$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
			$requistion_job_cond = where_con($job_number_arr,0,"d.job_no");
			//$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
			$issue_job_cond =  where_con($job_number_arr,0,"d.job_no");
			//$issue_job_cond .= " and d.job_no in ( $job_numbers )" ;
			$production_job_cond = where_con($job_number_arr,0,"d.job_no");
			//$production_job_cond .= " and d.job_no in ( $job_numbers )" ;



		}

        //Program Qnty 
        $progQnty_sql = "SELECT a.booking_no,a.buyer_id,a.po_id,
			b.program_qnty as program_qnty, b.id program_id,b.knitting_source,b.knitting_party,d.within_group
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d 
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $program_job_cond $company_cond $alloc_booking_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $party_cond $sourc_cond order by b.knitting_party,b.id,a.booking_no";  
        /* $progQnty_sql = "select a.booking_no,a.buyer_id,
			a.program_qnty as program_qnty, a.id,b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d 
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, a.id,b.id,a.program_qnty order by b.id";*/ 
		//echo $progQnty_sql;        
		$progQntyResult = sql_select($progQnty_sql);

		$progQntyArr =$progNoArr= array(); $programIdQntyChk = array(); 

		$programs_in_booking=array();

		foreach($progQntyResult as $row)
		{
			if($programIdQntyChk[$row[csf("id")]] == "")
			{
				$programIdQntyChk[$row[csf("id")]] = $row[csf("id")];
				$progQntyArr[$row[csf("booking_no")]]['book_prog_qnty'] += $row[csf("program_qnty")];   
				$progNoArr[$row[csf("booking_no")]][] = $row[csf("program_id")];   
				$progQntyArr[$row[csf("buyer_id")]]['buyer_prog_qnty'] += $row[csf("program_qnty")]; 
			}
			$style_ref_no=$job_po_no_arr[$row[csf("po_id")]]['style_ref_no'];
			$ref_no=$job_po_no_arr[$row[csf("po_id")]]['grouping'];
			
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['booking_no'] = $row[csf("booking_no")];
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['knitting_source'] = $row[csf("knitting_source")];
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['buyer_id'] = $row[csf("buyer_id")];
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['po_id'] .= $row[csf("po_id")].',';
			
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['style_ref_no'] = $style_ref_no;
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['ref_no'] = $ref_no;
			$prog_data_array[$row[csf("knitting_party")]][$row[csf("booking_no")]][$row[csf("program_id")]]['program_id'] = $row[csf("program_id")];
			$programs_in_booking[$row[csf("knitting_party")]][$row[csf("booking_no")]]['program_id'].= $row[csf("program_id")].",";

			$all_po_ids_for_buyer_issue .= $row[csf("po_id")].","; 
		
		}
		unset($progQntyResult);

        
			
		$reqs_sql = "SELECT a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b,ppl_planning_info_entry_mst c, wo_booking_mst d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond and c.company_id=$companyID 
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";	
		//echo $reqs_sql;die;		
			
		$reqs_sql_result = sql_select($reqs_sql);

		$requIdCheckArr = array();
		$reqs_array = array();
		foreach ($reqs_sql_result as $row) 
		{
			if($requIdCheckArr[$row[csf('id')]] == "")
			{
				$requIdCheckArr[$row[csf('id')]] = $row[csf('id')];
				$reqs_array[$row[csf('program_no')]]['qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_array[$row[csf('program_no')]]['requ_no']= $row[csf('requisition_no')];
				$reqs_array[$row[csf('program_no')]]['program_no'] .= $row[csf('program_no')].",";
				$reqs_array[$row[csf('program_no')]]['buyer_req_qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_buyer_array[$row[csf('buyer_id')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$program_buyer_array[$row[csf('buyer_id')]]['program_no'] .= $row[csf('program_no')].",";
			}
		}
		unset($reqs_sql_result);

		$product_sql = "SELECT a.booking_id,a.ref_closing_status, c.quantity as knitting_qnty,c.returnable_qnty as return_qnty, c.id, d.booking_no , d.job_no,a.buyer_id 
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c , wo_booking_dtls d
			where a.id=b.mst_id and c.dtls_id=b.id $production_job_cond $production_booking_cond $company_cond
			and c.po_breakdown_id = d.po_break_down_id 
			and a.item_category=13 and a.entry_form=2 and c.entry_form=2 
			and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active  = 1 and c.is_deleted = 0 
			and a.status_active = 1 and a.is_deleted = 0";
		// echo $product_sql;	
		$productionArr = sql_select($product_sql);

		$productionQntyArr = array();$productionQntyArr_buy = array(); $prodQntyIdChk = array();
		foreach($productionArr as $row)
		{
			if($row[csf("booking_id")])
			{
				if($prodQntyIdChk[$row[csf("id")]] == "")
				{
					$prodQntyIdChk[$row[csf("id")]] = $row[csf("id")];
					$productionQntyArr[$row[csf("booking_id")]]["prod_qnty"] += $row[csf("knitting_qnty")];
					$productionQntyArr[$row[csf("booking_id")]]["return_qnty"] += $row[csf("return_qnty")];
					//$nprog[$row[csf("booking_id")]]=$row[csf("booking_id")];
					$productionQntyArr_buy[$row[csf("buyer_id")]]["buyer_prod_qnty"] += $row[csf("knitting_qnty")];
					$productionQntyArr_buy[$row[csf("buyer_id")]]["buyer_return_qnty"] += $row[csf("return_qnty")];
					$productionQntyArr[$row[csf("booking_id")]]["ref_closing_status"]= $row[csf("ref_closing_status")];
				}
			}
		}
		unset($productionArr);
		// echo "<pre>";
		// print_r($nprog);
		//print_r($productionQntyArr_buy);
		//$all_po_ids_for_buyer_issue;

		$all_po_ids=chop($all_po_ids_for_buyer_issue,',');
		$all_po_ids_arr = array_filter(array_unique(explode(",",$all_po_ids)));
		$po_ids_cond=where_con($all_po_ids_arr,0,"b.po_breakdown_id");
		$roll_maintained_yesNo=0;
		if($roll_maintained_yesNo==1) // rloo level
		{
			$sql_grey_recv_trans="SELECT c.booking_no as prog_no,sum(b.reject_qty) as reject_qty, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b,pro_roll_details c where a.id=b.trans_id and c.mst_id=a.mst_id and c.dtls_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(22,23,58) and a.item_category=13 and a.transaction_type in(1) and b.trans_type in(1) $po_ids_cond group by c.booking_no";
			// echo $sql_grey_recv_trans;
			
			$result_recv_trans=sql_select( $sql_grey_recv_trans );
			foreach ($result_recv_trans as $row)
			{
				$grey_recv_data_array[$row[csf('prog_no')]]['grey_recv']=$row[csf('qnty')];
				$grey_recv_data_array[$row[csf('prog_no')]]['reject_qty']=$row[csf('reject_qty')];
				//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			}
		}
		else // gross level
		{
			$sql_grey_recv_trans="SELECT c.booking_no as prog_no,sum(b.reject_qty) as reject_qty, sum(b.quantity) as qnty,b.entry_form from inv_transaction a, order_wise_pro_details b,inv_receive_master c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(22,2) and a.item_category=13 and a.transaction_type in(1) and b.trans_type in(1) $po_ids_cond group by c.booking_no,b.entry_form";
			 //echo $sql_grey_recv_trans;
			
			$result_recv_trans=sql_select( $sql_grey_recv_trans );
			$production_nos=array();
			foreach ($result_recv_trans as $row)
			{
				if($row[csf('entry_form')]==22)
				{
					$production_nos[]="'". $row[csf('prog_no')]."'";
				}
				
				
			}
			$production_nos=array_unique($production_nos);
			//print_r($production_nos);die;
			//$production_noss = implode(",", $production_nos);
			$issue_job_cond =  where_con($production_nos,0,"recv_number");
			$program_in_production=return_library_array( "select recv_number, booking_no from inv_receive_master where status_active=1  $issue_job_cond ", "recv_number", "booking_no");
			



			foreach ($result_recv_trans as $row)
			{
				if($row[csf('entry_form')]==22)
				{
					$grey_recv_data_array[$program_in_production[$row[csf('prog_no')]]]['grey_recv']+=$row[csf('qnty')];
					$grey_recv_data_array[$program_in_production[$row[csf('prog_no')]]]['reject_qty']+=$row[csf('reject_qty')];
				}else{
					$grey_recv_data_array[$row[csf('prog_no')]]['grey_recv']+=$row[csf('qnty')];
					$grey_recv_data_array[$row[csf('prog_no')]]['reject_qty']+=$row[csf('reject_qty')];
				}
				
				//$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			}
		}

		// echo "<pre>";
		// print_r($grey_recv_data_array);
		// echo "</pre>";

		//echo $sql_grey_recv_trans;
		// $sql_grey_recv_trans="SELECT c.booking_no as prog_no,sum(a.cons_reject_qnty) as reject_qty, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b,inv_receive_master c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22) and a.item_category=13 and a.transaction_type in(1) and b.trans_type in(1) $po_ids_cond group by c.booking_no";
			// echo $sql_grey_recv_trans;
		$sql_issue = "SELECT (b.quantity) as issue_qnty, e.buyer_name , d.requisition_no from order_wise_pro_details b, inv_transaction d, wo_po_break_down c, wo_po_details_master e where d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.trans_type=2 and b.entry_form=3 and b.status_active=1 and d.receive_basis = 3 and b.is_deleted=0 and d.status_active= 1 and b.issue_purpose not in (2,8) and d.company_id=$companyID $po_ids_cond ";

		//echo $sql_issue;die;
		$sql_issue_data = sql_select( $sql_issue );

		$issue_qty_arr=array();
		foreach ($sql_issue_data as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]]['issue_qnty'] += $row[csf('issue_qnty')];
			$issue_qty_summary_arr[$row[csf('buyer_name')]]['issue_qnty'] += $row[csf('issue_qnty')];
		}		
		unset($sql_issue_data);

		$sql_return = "SELECT (b.quantity) as returned_qnty, b.reject_qty as cons_reject_qnty, e.buyer_name, a.booking_no as requ_no from inv_receive_master a, order_wise_pro_details b, inv_transaction d,wo_po_break_down c, wo_po_details_master e where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $po_ids_cond $company_cond";
		$sql_return_data = sql_select($sql_return);
		//echo $sql_return;die;

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('requ_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
			$return_qty_summary_arr[$row[csf('buyer_name')]]['returned_qnty'] += $row[csf('returned_qnty')];

			$yarn_issue_reject_qty_arr[$row[csf('requ_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
			$reject_qty_summary_arr[$row[csf('buyer_name')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];	
		}
		unset($sql_return_data);

		ob_start();
		?>
		<fieldset style="width:1250px;margin:0 auto;">
			<table cellpadding="0" cellspacing="0" width="1190">
				<tr>
					<td align="center" width="100%" colspan="17" style="font-size:16px"><strong>Party Wise/Booking Wise Knitting Process Loss Statements</strong></td>
				</tr>
			</table>	
			<table cellpadding="0" cellspacing="0" width="1190">
				<tr>
					<td width="100%" colspan="16" style="font-size:12px"><strong>Details : </strong></td>
				</tr>
			</table>	
			
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1190" class="rpt_table" >
				<thead>
					<tr>
						<th width="40">SL</th>
						
						<th width="120">Booking No</th>
						<th width="180">Order No</th>
						<th width="70">Buyer</th>
						<th width="80">Style Ref.</th>
						<th width="80">Int. Ref. No</th>                   
						<th width="40">UOM</th>                 
						<th width="60">Yarn Issued</th>
						<th width="60">Fabric Received</th>
						<th width="60">Reject Fabric Received</th>
						<th width="60">Yarn Issue Returned</th>
						<th width="60">Reject Yarn Issue Returned</th>
						<th width="60">Before Process Loss Balance</th>
						<th width="60">Process Loss Qty.</th>
						<th width="60">After Process Loss Balance</th>
					</tr>
					
					
				</thead>
			</table>
			
			<div style="width:1230px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1190" class="rpt_table" id="tbl_list_search">
					<tbody  >
						<tr>
							<th width="40"></th>
							
							<th width="120"></th>
							<th width="180"></th>
							<th width="70"></th>
							<th width="80"></th>
							<th width="80"></th>                   
							<th width="40"></th>                 
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
							<th width="60"></th>
						</tr>
						<? 
						$i=$m=$k=$z=1;$party_array=$booking_array=array();
						$total_issue_qnty=$grand_grey_recv_qty=$grand_before_process_loss_balance=$grand_process_loss_qty=$grand_after_process_loss_balance=$grand_yarn_reject_qnty=$grand_grey_recv_reject=0;
						foreach($prog_data_array as $party_id => $party_data)
						{
							$sub_grey_recv_reject=$sub_fab_recv_qnty=$sub_yarn_reject_qnty==$sub_process_loss_qty=$sub_requ_balance=$sub_issue_qnty=$sub_issue_ret_qnty=$sub_issue_balance=$sub_prod_qnty=$sub_before_process_loss_balance=$sub_reject_qnty=$sub_balance_qnty=0;
							$fab_rec_part=0;
							$fab_reject_part=0;
							foreach ($party_data as $booking_no => $booking_data) 
							{
								if ($m % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								//print_r($booking_data);die;
								$booking_row;
								foreach($booking_data as $prog_no => $row)
								{
										$booking_row=$row;
										
										//echo $po_ids.'ss';
										$prog_Qnty = $progQntyArr[$prog_no]['book_prog_qnty'];
										//$program_ids = implode(", ",array_filter(array_unique($progNoArr[$prog_no])));
										//	if ($prog_Qnty == 0) $program_bgcolor = "background-color:red;";else $program_bgcolor= "";
										//$prog_balance = $row["qnty"] - $prog_Qnty;
										//$requ_qnty = $reqs_array[$booking_no]['qnty'];
										//if ($requ_qnty == 0) $requ_bgcolor = "background-color:red;";else $requ_bgcolor= "";
										$requ_balance = $allocation_qnty - $requ_qnty;
										$requ_id=$reqs_array[$prog_no]['requ_no'];// array_unique(explode(",",chop($reqs_array[$prog_no]['requ_no'],",")));
												
										$issue_qnty = $issue_qty_arr[$requ_id]['issue_qnty']; 
										$issue_ret_qnty = $return_qty_arr[$requ_id]['returned_qnty'];
										$yarn_reject_qnty = $yarn_issue_reject_qty_arr[$requ_id]['reject_qnty'];
										$issue_balance = $prog_Qnty - $issue_qnty + $issue_ret_qnty;
										$grey_recv_qty=$grey_recv_data_array[$prog_no]['grey_recv'];
										$grey_recv_reject=$grey_recv_data_array[$prog_no]['reject_qty'];
										$ref_closing_status=$productionQntyArr[$prog_no]["ref_closing_status"];
										$before_process_loss_balance=$issue_qnty-($grey_recv_qty+$issue_ret_qnty+$yarn_reject_qnty);
										if($ref_closing_status==1)
										{
											$process_loss_qty=$before_process_loss_balance;
										}
										else 
										{ 
										 	$process_loss_qty=0;
										}
										
										 $after_process_loss_balance=$before_process_loss_balance-$process_loss_qty;
										
										
										
										
										
										$total_issue_qnty += $issue_qnty;
										//=====Booking Subtotal
										
										$booking_sub_yarn_issue += $issue_qnty;
										$booking_sub_grey_recv_qty += $grey_recv_qty;
										$booking_sub_issue_ret_qnty +=$issue_ret_qnty;
										$booking_sub_grey_recv_reject += $grey_recv_reject;
										$booking_sub_yarn_reject_qnty += $yarn_reject_qnty;
										$booking_sub_before_process_loss_balance += $before_process_loss_balance;
										$booking_sub_process_loss_qty += $process_loss_qty;
										$booking_sub_after_process_loss_balance += $after_process_loss_balance;
										$show = true;
										
										
										$grand_yarn_reject_qnty += $yarn_reject_qnty;
										$grand_before_process_loss_balance += $before_process_loss_balance;
										$grand_process_loss_qty += $process_loss_qty;
										$grand_after_process_loss_balance +=$after_process_loss_balance;
										$grand_issue_qnty += $issue_qnty;
										$grand_issue_ret_qnty += $issue_ret_qnty;
										
									
								}
								if (!in_array($party_id,$party_array) )
								{ ?>
									
                                    <tr bgcolor="#dddddd">
									<td colspan="15" align="left" ><b>Party Name: <? if ($booking_row[("knitting_source")]==1) echo $company_library[$party_id]; else if ($booking_row[("knitting_source")]==3) echo $supplier_arr[$party_id]; ?></b>
                                    </td>
									</tr>
                                    <?
									$party_array[$k]=$party_id;
									$k++;
								}

								$prog_no=$booking_row['program_id'];
								$program_nos=chop($programs_in_booking[$party_id][$booking_no]['program_id'],",");

								$requisitions='';
								$progrm_no_arr=explode(",", $program_nos);

								
								for($cnt=0;$cnt<count($progrm_no_arr);$cnt++)
								{
									$requisitions.=$reqs_array[$progrm_no_arr[$cnt]]['requ_no'].",";
								}
								$progrm_no_arr=array_unique($progrm_no_arr);
								$program_nos=implode(",", $progrm_no_arr);

								$requisitions=chop($requisitions,",");

								$requisitions_arr=explode(",", $requisitions);
								$requisitions_arr=array_unique($requisitions_arr);
								$requisitions=implode(",", $requisitions_arr);
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>"> 
									
								    <td ><? echo $m;?></td>
									<?php $po_nums=explode(",", chop($data_array[$booking_no]['po_number'],","));

										$po_nums=array_unique($po_nums);
										$po_numbers=implode(",", $po_nums);
									 ?>
									
									<td ><p style="word-break: break-all;">&nbsp;<? echo $booking_no;?></p></td>
									<td ><p style="word-break: break-all;">&nbsp;<? echo $po_numbers;?></p></td>
									<td ><p style="word-break: break-all;">&nbsp;<? echo $buyer_arr[$booking_row['buyer_id']];?></p></td>													
									<td ><p style="word-break: break-all; text-align: center;">&nbsp;<? 
										$internal_ref_no="";
										$po_ids = implode(",",array_unique(explode(",",chop($booking_row["po_id"],','))));
											foreach(explode(",", $po_ids) as $po_id)
											{
												if($internal_ref_no =="") $internal_ref_no =  $internalRefArr[$po_id]["internalRefNumber"]; else $internal_ref_no .= ",".$internalRefArr[$po_id]["internalRefNumber"];
											}
											echo $booking_row['style_ref_no'];
											// $before_process_loss_balance=$issue_qnty-($grey_recv_qty+$issue_qnty+$issue_ret_qnty+$yarn_reject_qnty+$issue_qnty);
											
										?>
										</p>
									</td>
									<td  ><p style="word-break: break-all;">&nbsp;<? echo chop($internal_ref_no,',');?></p></td>
									<td  align="center"><p><? echo $unit_of_measurement[12];?></p></td>
									<td  align="right" style="">
										<?php if($booking_sub_yarn_issue>0){ ?>
										<a href='##' onClick="openmypage_issue('<? echo $requisitions."_".$booking_no. "_" . $po_ids. "_" . $program_nos; ?>', 'issue_popup')"><p><? echo number_format($booking_sub_yarn_issue,2);?></p></a>
										<?php } else echo number_format($booking_sub_yarn_issue,2); ?>
									</td>
									<td  align="right" style="">
										<?php if($booking_sub_grey_recv_qty>0){ ?>
										<a href='##' onClick="openmypage_issue('<? echo $requisitions."_".$booking_no. "_" . $po_ids. "_" . $program_nos."_".$companyID; ?>', 'grey_recv_popup')"><p><? echo number_format($booking_sub_grey_recv_qty,2);?></p></a>
										<?php } else echo number_format($booking_sub_grey_recv_qty,2); ?>

										<?php $fab_rec_part+=$booking_sub_grey_recv_qty ?>
									</td>
									<td  align="right" style="">
										
										<?php if($booking_sub_grey_recv_reject>0){ ?>
										<a href='##' onClick="openmypage_issue('<? echo $requisitions."_".$booking_no. "_" . $po_ids. "_" . $program_nos."_".$companyID; ?>', 'reject_fabric_recv')"><p><? echo number_format($booking_sub_grey_recv_reject,2);?></p></a>
										<?php } else echo number_format($booking_sub_grey_recv_reject,2); ?>

										<?php $fab_reject_part+=$booking_sub_grey_recv_reject; ?>
									</td>
									
									<td  align="right" >
										<?php if($booking_sub_issue_ret_qnty>0){ ?>
										<a href='##' onClick="openmypage_issue('<? echo $requisitions."_".$booking_no. "_" . $po_ids. "_" . $program_nos; ?>', 'issue_return_popup')"><p><? echo number_format($booking_sub_issue_ret_qnty,2);?></p></a>
										<?php } else echo number_format($booking_sub_issue_ret_qnty,2); ?>
									</td>
									<td  align="right" style="" title="Requisition no=<? echo $requ_nos;?>">
										<?php if($booking_sub_yarn_reject_qnty>0){ ?>
										<a href='##' onClick="openmypage_issue('<? echo $requisitions."_".$booking_no. "_" . $po_ids. "_" . $program_nos; ?>', 'reject_yarn_returned')"><p><? echo number_format($booking_sub_yarn_reject_qnty,2);?></p></a>
										<?php } else echo number_format($booking_sub_yarn_reject_qnty,2); ?>
									</td>
									<td  title="Yarn Issue-(Grey Recv+Grey Reject+Yarn Issue Return)" align="right"><p><? echo number_format($booking_sub_before_process_loss_balance,2);?></p></td>
									<td  title="<? if($ref_closing_status==1) echo "Ref Full Close";else echo " Not Ref Close";?>" align="right"><p><? 
									
									 echo $booking_sub_process_loss_qty;
									
									 ?></p></td>
									<td   title="Before Process Loss-Full Process Loss Qty" align="right"><p><? echo number_format($booking_sub_after_process_loss_balance,2);?></p></td>
								</tr>
								<?

								$sub_issue_qnty += $booking_sub_yarn_issue;

								$sub_issue_ret_qnty += $booking_sub_issue_ret_qnty;

								$sub_yarn_reject_qnty +=$booking_sub_yarn_reject_qnty;
								$sub_before_process_loss_balance += $booking_sub_before_process_loss_balance;
								$sub_reject_qnty += $reject_qnty;

								$sub_fab_recv_qnty +=$booking_sub_grey_recv_qty;

								$sub_grey_recv_reject += $booking_sub_grey_recv_reject;
								$sub_process_loss_qty += $booking_sub_process_loss_qty;
								$sub_after_process_loss_balance += $booking_sub_after_process_loss_balance;


								
								
								

								
								
								
								

								$booking_sub_yarn_issue =0;
								$booking_sub_grey_recv_qty =0;
								$booking_sub_issue_ret_qnty =0;
								$booking_sub_grey_recv_reject =0;
								$booking_sub_yarn_reject_qnty =0;
								$booking_sub_before_process_loss_balance =0;
								$booking_sub_process_loss_qty =0;
								$booking_sub_after_process_loss_balance =0;
								$m++;$y++;//$job_no
							}
									?>
	                                <tr style="font-weight: bold;background-color: #e0e0e0">
										<td colspan="2">&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td colspan="3" align="right">Party Total</td>
										
										<td align="right"><p><? echo number_format($sub_issue_qnty,2);//$sub_issue_qnty=0;?></p></td>
										<td align="right"><p><? echo number_format($fab_rec_part,2);?></p></td>                                        
										<td align="right"><p><? echo number_format($fab_reject_part,2);?></p></td>
										<td align="right"><p><? echo number_format($sub_issue_ret_qnty,2);?></p></td>
										<td align="right"><p><? echo number_format($sub_yarn_reject_qnty,2);?></p></td>
										<td align="right"><p><? echo number_format($sub_before_process_loss_balance,2);?></p></td>
										<td align="right"><p><? echo number_format($sub_process_loss_qty,2);?></p></td>
										<td align="right"> <p><? echo number_format($sub_after_process_loss_balance,2);?></p></td>
									</tr>
	                                <?

	                                $grand_grey_recv_qty+=$fab_rec_part;
	                                $grand_grey_recv_reject+=$fab_reject_part;


									
							}
							
						//}

						?>
						</tbody>
						
						<tfoot>
						<tr style="font-weight: bold;background-color: #ccc;border-top: 2px solid">
							<td colspan="2">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td colspan="3" align="right">Grand Total </td>
							
							<td align="right"><p><? echo number_format($grand_issue_qnty,2)?></p></td>
							<td align="right"><p><? echo number_format($grand_grey_recv_qty,2);?></p></td>                                        
							<td align="right"><p><? echo number_format($grand_grey_recv_reject,2);?></p></td>
							<td align="right"><p><? echo number_format($grand_issue_ret_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($grand_yarn_reject_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($grand_before_process_loss_balance,2);?></p></td>
							<td align="right"><p><? echo number_format($grand_process_loss_qty,2);?></p></td>
							
							<td align="right"><p><? echo number_format($grand_after_process_loss_balance,2);?></p></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>      
		<?            
	}
    //print_r($progss); echo "<pre>";
	foreach (glob("$user_name*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}

	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = "requires/" . $user_name . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();
}


if ($action == "issue_popup") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$bookingNo=$data[1];
	$po_ids=$data[2];
	$prog_no=$data[3];
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//print_r($yarn_desc_array);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>	
	<div style="width:1050px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1050px; margin-left:3px">
     <?
     		$req=sql_select("select a.requisition_no, b.id as program_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id = b.id and a.requisition_no in ($requ_ids)");
     		$prog_arr=array();
     		foreach ($req as $row) {
     			$prog_arr[$row[csf('requisition_no')]]=$row[csf('program_no')];
     		}

            $sql = "SELECT a.company_id,a.issue_number, a.issue_date,a.issue_basis, a.challan_no, a.supplier_id,a.knit_dye_source, a.knit_dye_company, a.booking_no, a.store_id, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details,c.yarn_count_id, d.brand_id,d.requisition_no from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and d.requisition_no in ($requ_ids) and b.po_breakdown_id in($po_ids) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.issue_purpose not in (2,8) group by a.id,c.id,a.issue_number,a.issue_date, a.challan_no, a.knit_dye_source,a.knit_dye_company, a.company_id,a.booking_no,a.supplier_id, a.store_id, c.lot, c.yarn_type, c.yarn_count_id,c.product_name_details, d.brand_id,a.issue_basis,d.requisition_no order by a.id,a.issue_date ";
           		//echo $sql;	
				$result = sql_select($sql);
			?>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0">
           		<caption><b>Company: &nbsp;&nbsp; <? echo $company_library[$result[0][csf('company_id')]]; ?>  &nbsp;<b>Booking No: &nbsp;&nbsp; <? echo $bookingNo; ?> </b></caption>
            
				<thead>
						<th colspan="11"><b>Yarn Issue</b></th>
				</thead>
				<thead>
						<tr>
							<th width="20">SL</th>
		                    <th width="70">Issue Date</th>
							<th width="100">Issue No</th>
							<th width="100">Req No</th>
							<th width="70">Prog. No</th>
							<th width="70">Count</th>
							<th width="200">Yarn Composition</th>
							<th width="120">Yarn Supplier</th>
							<th width="70">Yarn Type</th>
							<th width="70">Yarn Lot</th>
							<th width="">Issue Qnty</th>
						
						</tr>
					
				</thead>
			</table>
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">	
					<?
						$i = 1;
						$total_yarn_issue_qnty = 0;
						$total_yarn_issue_qnty_out = 0;
					
				
					foreach ($result as $row) 
					{
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						
						$yarn_issued = $row[csf('issue_qnty')];

						if($row[csf('issue_basis')]==1){
							$booking_no=$row[csf('booking_no')];
						}elseif ($row[csf('issue_basis')]==3) {
							$booking_no=$row[csf('requisition_no')];
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="20"><p><? echo $i; ?></p></td>
	                        <td width="70"><p><? echo change_date_format($row[csf('issue_date')]) ?></p></td>
							<td width="100"><p><? echo $row[csf('issue_number')];; ?></p></td>
							<td width="100" align="center" ><p><? echo $booking_no  ?>&nbsp;</p></td>
							<td width="70"><p><? echo $prog_arr[$booking_no];//$row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
							<td width="200"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
							<td width="70"><p><? echo $row[csf('lot')]; ?></p></td>
							<td align="right" width="">
								<?
								echo number_format($yarn_issued, 2, '.', '');
								?>
							</td>
							
						</tr>
						<?
						$total_yarn_issue_qnty+=$yarn_issued;
						$i++;
					}
				
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
	                    <td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
						
					</tr>
					<tfoot>    
						<tr>
							<th align="right" colspan="10">Issue Total</th>
							<th align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2, '.', ''); ?></th>
						</tr>
					</tfoot>
				</table>
            
			
		</div>		
	</fieldset>  
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}
if ($action == "issue_return_popup") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$bookingNo=$data[1];
	$po_ids=$data[2];
	$prog_no=$data[3];
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//print_r($yarn_desc_array);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>	
	<div style="width:1050px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1050px; margin-left:3px">
    
		<div style="width:100%" id="report_container">
			
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
			
	            <table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0">
	            	<caption><b>Company: &nbsp;&nbsp; <? echo $company_library[$result[0][csf('company_id')]]; ?>  &nbsp;<b>Booking No: &nbsp;&nbsp; <? echo $bookingNo; ?> </b></caption>
					<thead>
						<th colspan="12"><b>Yarn Issue Return</b></th>
					</thead>
					<thead>
						<th width="20">SL</th>
	                    <th width="70">Issue Date</th>
						<th width="120">Issue No</th>
						<th width="120">Req No</th>
						<th width="70">Prog. No</th>
						<th width="70">Count</th>
						<th width="180">Yarn Composition</th>
						<th width="120">Yarn Supplier</th>
						<th width="70">Yarn Type</th>
						<th width="70">Yarn Lot</th>
	                    <th width="70">Issue Return Qty</th>
						
						
					</thead>
				</table>
				<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">	
						<?
						$i = 1;
						
						$total_yarn_issue_ret_qnty=$total_yarn_issue_ret_rej_qnty= 0;
						$req=sql_select("select a.requisition_no, b.id as program_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id = b.id and a.requisition_no in ($requ_ids)");
			     		$prog_arr=array();
			     		foreach ($req as $row) {
			     			$prog_arr[$row[csf('requisition_no')]]=$row[csf('program_no')];
			     		}

						
						$sql = "SELECT a.recv_number, a.receive_date,a.receive_basis, a.supplier_id,a.knitting_source,a.knitting_company,a.booking_no, a.receive_basis, sum(b.quantity) as issue_qnty_ret,sum(b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details,c.yarn_count_id, d.brand_id,d.requisition_no from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.item_category=1 and d.id=b.trans_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and a.booking_no in ($requ_ids) and b.po_breakdown_id in($po_ids)  and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.issue_purpose!=2  group by a.id,c.id,a.recv_number, a.receive_date, a.knitting_source,a.knitting_company, a.booking_no,a.supplier_id, a.receive_basis, c.lot, c.yarn_type, c.yarn_count_id,c.product_name_details, d.brand_id,d.requisition_no order by a.id,a.receive_date";

						// and b.po_breakdown_id in($po_ids) remove from where cond
						//echo $sql;
						$result = sql_select($sql);
						foreach ($result as $row) 
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							
							$yarn_issued_ret = $row[csf('issue_qnty_ret')];

							if($row[csf('receive_basis')]==1){
								$booking_no=$row[csf('booking_no')];
							}elseif ($row[csf('receive_basis')]==3) {
								$booking_no=$row[csf('requisition_no')];
								if(empty($row[csf('requisition_no')]))
								{
									$booking_no=$row[csf('booking_no')];
								}
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trd_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="trd_<? echo $i; ?>">
								<td width="20"><p><? echo $i; ?></p></td>
		                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]) ?></p></td>
								<td width="120"><p><? echo $row[csf('recv_number')];; ?></p></td>
								<td width="120" align="center" ><p><? echo $booking_no;  ?>&nbsp;</p></td>
								<td width="70"><p><? echo $prog_arr[$booking_no];//$row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
								<td width="180"  style="word-break:break-all"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td width="70"><p><? echo $row[csf('lot')]; ?></p></td>
								<td align="right" width="70">
									<?
									echo number_format($yarn_issued_ret, 2, '.', '');
									?>
								</td>
		                       
								
							</tr>
							<?
							$total_yarn_issue_ret_qnty+=$yarn_issued_ret;
							$total_yarn_issue_ret_rej_qnty+=$row[csf('reject_qty')];
							$i++;
						}
					
						?>
						<tr style="font-weight:bold">
							<td>&nbsp;</td>
		                    <td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_ret_qnty, 2, '.', ''); ?></td>
		                   
							
						</tr>
						<tfoot>    
							<tr>
								<th align="right" colspan="10">Issue Return Total</th>
								<th align="right"><? echo number_format($total_yarn_issue_ret_qnty, 2, '.', ''); ?></th>
		                        
							</tr>
						</tfoot>
					</table>
				</div>
			</div>	
		</div>	
	</fieldset>  
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}
if ($action == "reject_yarn_returned") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$bookingNo=$data[1];
	$po_ids=$data[2];
	$prog_no=$data[3];
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//print_r($yarn_desc_array);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>	
	<div style="width:1050px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1050px; margin-left:3px">
    
		<div style="width:100%" id="report_container">
			
	            <table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0">
	            	 	<caption><b>Company: &nbsp;&nbsp; <? echo $company_library[$result[0][csf('company_id')]]; ?>  &nbsp;<b>Booking No: &nbsp;&nbsp; <? echo $bookingNo; ?> </b></caption>
						<thead>
							<th colspan="12"><b>Reject Yarn Returned</b></th>
						</thead>
						<thead>
							<th width="20">SL</th>
		                    <th width="70">Issue Date</th>
							<th width="120">Issue No</th>
							<th width="120">Req No</th>
							<th width="70">Prog. No</th>
							<th width="70">Count</th>
							<th width="180">Yarn Composition</th>
							<th width="120">Yarn Supplier</th>
							<th width="70">Yarn Type</th>
							<th width="70">Yarn Lot</th>
							<th >Reject Return Qty</th>
							
						</thead>
				</table>
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">	
					<?
						$i = 1;
					
						$total_yarn_issue_ret_qnty=$total_yarn_issue_ret_rej_qnty= 0;
						$req=sql_select("select a.requisition_no, b.id as program_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id = b.id and a.requisition_no in ($requ_ids)");
			     		$prog_arr=array();
			     		foreach ($req as $row) {
			     			$prog_arr[$row[csf('requisition_no')]]=$row[csf('program_no')];
			     		}
						$sql = "SELECT a.recv_number, a.receive_date,a.receive_basis, a.supplier_id,a.knitting_source,a.knitting_company,a.booking_no, a.receive_basis, sum(b.quantity) as issue_qnty_ret,sum(b.reject_qty) as reject_qty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details,c.yarn_count_id, d.brand_id,d.requisition_no from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.item_category=1 and d.id=b.trans_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and a.booking_no in ($requ_ids) and b.po_breakdown_id in($po_ids)  and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.issue_purpose!=2  group by a.id,c.id,a.recv_number, a.receive_date, a.knitting_source,a.knitting_company, a.booking_no,a.supplier_id, a.receive_basis, c.lot, c.yarn_type, c.yarn_count_id,c.product_name_details, d.brand_id,d.requisition_no order by a.id, a.receive_date";

						// and b.po_breakdown_id in($po_ids) remove from where condition
		
						$result = sql_select($sql);
						foreach ($result as $row) 
						{
							if($row[csf('reject_qty')]>0)
							{
								if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								
								$yarn_issued_ret = $row[csf('issue_qnty_ret')];

								if($row[csf('receive_basis')]==1){
									$booking_no=$row[csf('booking_no')];
								}elseif ($row[csf('receive_basis')]==3) {
									$booking_no=$row[csf('requisition_no')];
									if(empty($row[csf('requisition_no')]))
									{
										$booking_no=$row[csf('booking_no')];
									}
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trd_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="trd_<? echo $i; ?>">
									<td width="20"><p><? echo $i; ?></p></td>
			                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]) ?></p></td>
									<td width="120"><p><? echo $row[csf('recv_number')];; ?></p></td>
									<td width="120" align="center" ><p><? echo $booking_no;  ?>&nbsp;</p></td>
									<td width="70"><p><? echo $prog_arr[$booking_no];//$row[csf('challan_no')]; ?>&nbsp;</p></td>
									<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
									<td width="180"  style="word-break:break-all"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
									<td width="70"><p><? echo $row[csf('lot')]; ?></p></td>
									
			                       
									<td align="right"> <?php echo number_format($row[csf('reject_qty')],2) ?></td>
								</tr>
								<?
								$total_yarn_issue_ret_qnty+=$yarn_issued_ret;
								$total_yarn_issue_ret_rej_qnty+=$row[csf('reject_qty')];
								$i++;
							}
							
						}
				
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
	                    <td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						
	                    <td align="right"><? echo number_format($total_yarn_issue_ret_rej_qnty, 2, '.', ''); ?></td>
						
					</tr>
					<tfoot>    
						<tr>
							<th align="right" colspan="10">Reject Return Total</th>
							
	                        <th align="right"><? echo number_format($total_yarn_issue_ret_rej_qnty, 2, '.', ''); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>		
	</fieldset>  
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}
if ($action == "grey_recv_popup") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$bookingNo=$data[1];
	$po_ids=$data[2];
	$prog_no=$data[3];
	$companyID=$data[4];
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$fabricData = sql_select("select FABRIC_ROLL_LEVEL from variable_settings_production where company_name ='$companyID' and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
	
	$roll_maintained_yesNo = $fabricData[0]['FABRIC_ROLL_LEVEL'];
	//print_r($yarn_desc_array);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>	
	<div style="width:1050px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1050px; margin-left:3px">
    <?
    $programs_arr=explode(",", $prog_no);
    $program_cond='';

	if(count($programs_arr))
	{
		$program_cond=where_con_using_array($programs_arr,1,"d.booking_no");
	}
    $roll_maintained_yesNo=0;
    if($roll_maintained_yesNo==1)// roll level
    {	
		$sql_grey_recv_trans="SELECT d.company_id,a.gsm,a.width,a.machine_dia,d.recv_number, d.challan_no,d.receive_date,c.roll_no,c.booking_no as prog_no,sum(b.reject_qty) as reject_qty, sum(b.quantity) as qnty,e.product_name_details from pro_grey_prod_entry_dtls a,inv_receive_master d, order_wise_pro_details b,pro_roll_details c,product_details_master e where d.id=a.mst_id and d.id=c.mst_id and a.id=b.dtls_id  and c.mst_id=a.mst_id and c.dtls_id=b.dtls_id  and e.id=a.prod_id and e.id=b.prod_id and e.id=a.prod_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(22,23,58) and d.item_category=13  and b.trans_type in(1)  and b.po_breakdown_id in($po_ids)  $program_cond  group by d.id, d.company_id,a.gsm,a.width,a.machine_dia,c.booking_no,d.recv_number, d.challan_no,d.receive_date,c.roll_no,c.booking_no,e.product_name_details order by d.id,d.receive_date";
		//echo $sql_trans;
	}
	else
	{
		$sql_grey_recv_trans="SELECT d.company_id,a.gsm,a.width,a.machine_dia,d.recv_number, d.challan_no,d.receive_date,0 as roll_no,d.booking_no as prog_no,sum(b.reject_qty) as reject_qty, sum(b.quantity) as qnty,e.product_name_details from pro_grey_prod_entry_dtls a,inv_receive_master d, order_wise_pro_details b,product_details_master e where d.id=a.mst_id and a.id=b.dtls_id  and e.id=a.prod_id and e.id=b.prod_id and e.id=a.prod_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(22,2) and d.item_category=13  and b.trans_type in(1)   $program_cond  group by d.id, d.company_id,a.gsm,a.width,a.machine_dia,d.booking_no,d.recv_number, d.challan_no,d.receive_date,d.booking_no,e.product_name_details order by d.id,d.receive_date";
		//echo $sql_trans;
	}
	// echo $sql_grey_recv_trans;



	$result_recv = sql_select($sql_grey_recv_trans);

	?>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0">
            <caption><b>Company: &nbsp;&nbsp; <? echo $company_library[$result_recv[0][csf('company_id')]]; ?>  &nbsp;<b>Booking No: &nbsp;&nbsp; <? echo $bookingNo; ?> </b></caption>
				<thead>
					<th colspan="11"><b>Grey Fab Recv</b></th>
				</thead>
				<thead>
					<th width="20">SL</th>
                    <th width="70">Receive Date</th>
					<th width="100">Prog. No</th>
					<th width="100">Receive ID</th>
					<th width="130">Fabric Description</th>
					<th width="70">Fab. GSM</th>
					<th width="80">Receive Mc/F.Dia</th>
					<th width="120">Receive Ch. No</th>
					<th width="70">Receive Qty</th>
					
					<th width="">Roll No</th>
					
				</thead>
			</table>
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">	
				<?
				$i = 1;
				$total_recv_qnty = 0;
				
			
				foreach ($result_recv as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					
					$recv_qty = $row[csf('qnty')];


					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="20"><p><? echo $i; ?></p></td>
                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
						<td width="100"><p><? echo $row[csf('prog_no')];; ?></p></td>
						<td width="100" align="center" ><p><? echo $row[csf('recv_number')];  ?>&nbsp;</p></td>
						<td width="130" style="word-break:break-all"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
						<td width="70" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="80"><p><? echo $row[csf('machine_dia')]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="70" align="right"><p><? echo $recv_qty; ?></p></td>
						
						<td align="right" width="">
							<?
							echo $row[csf('roll_no')];;
							?>
						</td>
						
					</tr>
					<?
					$total_recv_qnty+=$recv_qty;
					$i++;
				}
			
				?>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					
					
				
                    <td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format($total_recv_qnty, 2, '.', ''); ?></td>
                     <td>&nbsp;</td>
					
				</tr>
				<tfoot>    
					<tr>
						<th align="right" colspan="8">Recv Total</th>
						<th align="right"><? echo number_format($total_recv_qnty, 2, '.', ''); ?></th>
                        
                        <th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			
			</div>
		</div>		
	</fieldset>  
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}
if ($action == "reject_fabric_recv") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$bookingNo=$data[1];
	$po_ids=$data[2];
	$prog_no=$data[3];
	$companyID=$data[4];
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$fabricData = sql_select("select FABRIC_ROLL_LEVEL from variable_settings_production where company_name ='$companyID' and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
	
	$roll_maintained_yesNo = $fabricData[0]['FABRIC_ROLL_LEVEL'];
	//print_r($yarn_desc_array);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>	
	<div style="width:1050px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1050px; margin-left:3px">
    <?
    $programs_arr=explode(",", $prog_no);
    $roll_maintained_yesNo=0;
    if($roll_maintained_yesNo==1)// roll level
    {	
		$sql_grey_recv_trans="SELECT d.company_id,a.gsm,a.width,a.machine_dia,d.recv_number, d.challan_no,d.receive_date,c.roll_no,c.booking_no as prog_no,sum(b.reject_qty) as reject_qty, sum(b.quantity) as qnty,e.product_name_details from pro_grey_prod_entry_dtls a,inv_receive_master d, order_wise_pro_details b,pro_roll_details c,product_details_master e where d.id=a.mst_id and d.id=c.mst_id and a.id=b.dtls_id  and c.mst_id=a.mst_id and c.dtls_id=b.dtls_id  and e.id=a.prod_id and e.id=b.prod_id and e.id=a.prod_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(22,23,58) and d.item_category=13  and b.trans_type in(1)  and b.po_breakdown_id in($po_ids)   group by d.id,d.company_id,a.gsm,a.width,a.machine_dia,c.booking_no,d.recv_number, d.challan_no,d.receive_date,c.roll_no,c.booking_no,e.product_name_details order by d.id,d.receive_date";
		//echo $sql_trans;
	}
	else
	{
		$sql_grey_recv_trans="SELECT d.company_id,a.gsm,a.width,a.machine_dia,d.recv_number, d.challan_no,d.receive_date,0 as roll_no,d.booking_no as prog_no,sum(b.reject_qty) as reject_qty, sum(b.quantity) as qnty,e.product_name_details from pro_grey_prod_entry_dtls a,inv_receive_master d, order_wise_pro_details b,product_details_master e where d.id=a.mst_id and a.id=b.dtls_id  and e.id=a.prod_id and e.id=b.prod_id and e.id=a.prod_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(22) and d.item_category=13  and b.trans_type in(1)  and b.po_breakdown_id in($po_ids)  group by d.id,d.company_id,a.gsm,a.width,a.machine_dia,d.booking_no,d.recv_number, d.challan_no,d.receive_date,d.booking_no,e.product_name_details order by d.id,d.receive_date";
		//echo $sql_trans;
	}
	// echo $sql_grey_recv_trans;		

	$result_recv = sql_select($sql_grey_recv_trans);
	?>
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0">
            <caption><b>Company: &nbsp;&nbsp; <? echo $company_library[$result_recv[0][csf('company_id')]]; ?>  &nbsp;<b>Booking No: &nbsp;&nbsp; <? echo $bookingNo; ?> </b></caption>
				<thead>
					<th colspan="11"><b>Grey Fab Recv</b></th>
				</thead>
				<thead>
					<th width="20">SL</th>
                    <th width="70">Receive Date</th>
					<th width="120">Prog. No</th>
					<th width="120">Receive ID</th>
					<th width="130">Fabric Description</th>
					<th width="70">Fab. GSM</th>
					<th width="80">Receive Mc/F.Dia</th>
					<th width="120">Receive Ch. No</th>
					<th width="70">Reject Qty</th>
					
					<th width="">Roll No</th>
					
				</thead>
			</table>
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">	
				<?
				$i = 1;
				$total_recv_qnty = 0;
				
			
				foreach ($result_recv as $row) 
				{
					if($row[csf('reject_qty')]>0)
					{
						if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						
						$recv_qty = $row[csf('reject_qty')];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="20"><p><? echo $i; ?></p></td>
	                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="120"><p><? echo $row[csf('prog_no')];; ?></p></td>
							<td width="120" align="center" ><p><? echo $row[csf('recv_number')];  ?>&nbsp;</p></td>
							<td width="130" style="word-break:break-all"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
							<td width="70" align="center"><? echo $row[csf('challan_no')]; ?></td>
							<td width="80"><p><? echo $row[csf('machine_dia')]; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td width="70" align="right"><p><? echo $recv_qty; ?></p></td>
							
							<td align="right" width="">
								<?
								echo $row[csf('roll_no')];;
								?>
							</td>
							
						</tr>
						<?
						$total_recv_qnty+=$recv_qty;
						$i++;
					}
					
				}
			
				?>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					
					
					<td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format($total_recv_qnty, 2, '.', ''); ?></td>
                    
                     <td>&nbsp;</td>
					
				</tr>
				<tfoot>    
					<tr>
						<th align="right" colspan="8">Reject Total</th>
						<th align="right"><? echo number_format($total_recv_qnty, 2, '.', ''); ?></th>
                        
                        <th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
			
			</div>
		</div>		
	</fieldset>  
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}
if ($action == "issue_return_popup_2") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$po_ids=$data[2];
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_desc_array = explode(",", $yarn_count);
	//print_r($yarn_desc_array);

	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$(".flt").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
			$(".flt").show();
		}
	</script>
	
	<div style="width:1050px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1050px; margin-left:3px">
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1040" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Yarn Return</b></th>
				</thead>
				<thead>
					<th width="120">Return Id</th>
					<th width="90">Return From</th>
					<th width="105">Req.No./Booking No</th>
					<th width="70">Challan No</th>
					<th width="75">Return Date</th>
					<th width="80">Store</th>
					<th width="70">Brand</th>
					<th width="60">Lot No</th>
					<th width="180">Yarn Description</th>
					<th width="90">Return Qnty (In)</th>
					<th>Return Qnty (Out)</th>
				</thead>
			</table>
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">	
				<?
				$total_yarn_return_qnty = 0;
				$total_yarn_return_qnty_out = 0;
				
				 $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, a.store_id, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and a.booking_id in ($requ_ids) and b.po_breakdown_id IN($po_ids) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose not in (2,8) group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, a.store_id, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if ($row[csf('knitting_source')] == 1) {
						$return_from = $company_library[$row[csf('knitting_company')]];
					} else if ($row[csf('knitting_source')] == 3) {
						$return_from = $supplier_details[$row[csf('knitting_company')]];
					} else
					$return_from = "&nbsp;";

					$yarn_returned = $row[csf('returned_qnty')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="90"><p><? echo $return_from; ?></p></td>
						<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="80"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
						<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
						<td align="right" width="90">
							<?
							if ($row[csf('knitting_source')] != 3) {
								echo number_format($yarn_returned, 2, '.', '');
								$total_yarn_return_qnty += $yarn_returned;
							} else
							echo "&nbsp;";
							?>
						</td>
						<td align="right">
							<?
							if ($row[csf('knitting_source')] == 3) {
								echo number_format($yarn_returned, 2, '.', '');
								$total_yarn_return_qnty_out += $yarn_returned;
							} else
							echo "&nbsp;";
							?>
						</td>
					</tr>
					<?
					$i++;
				}
				?>
				<tr style="font-weight:bold">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format( $total_yarn_return_qnty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format( $total_yarn_return_qnty_out, 2, '.', ''); ?></td>
				</tr>
				<tfoot>    
					<tr>
						<th align="right" colspan="10">Total Return</th>
						<th align="right"><? echo number_format( ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
					</tr>
				</tfoot>
			</table>
			</div>	
		</div>
	</fieldset>  
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}

if ($action == "production_popup") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$job_no=$data[0];
	$po_ids=$data[1];
	$program_no = $data[2];

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$suplier_details = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$sql="select a.body_part_id, a.construction, a.composition, b.fabric_color_id, sum(b.grey_fab_qnty) as grey_fab_qnty, c.id as po_id, c.po_number, d.id as job_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no
		from wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
		where a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.job_no='$job_no' and c.id in ($po_ids)
		group by a.body_part_id, a.construction, a.composition, b.fabric_color_id, c.id, c.po_number, d.id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no";
		 
	
	//echo $sql;//die;
		 
	$sql_result=sql_select($sql);
	$details_data=array();
	foreach($sql_result as $row)
	{
		$all_po_id.=$row[csf("po_id")].",";
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["body_part_id"]=$row[csf("body_part_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["construction"]=$row[csf("construction")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["composition"]=$row[csf("composition")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["fabric_color_id"]=$row[csf("fabric_color_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["grey_fab_qnty"]+=$row[csf("grey_fab_qnty")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["po_id"]=$row[csf("po_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["po_number"]=$row[csf("po_number")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["job_id"]=$row[csf("job_id")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["job_no"]=$row[csf("job_no")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["company_name"]=$row[csf("company_name")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$details_data[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("fabric_color_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
	}
	
	
	/*$sql_knitting="select a.po_breakdown_id, b.body_part_id, b.color_id, c.mst_id, c.dtls_id, c.barcode_no, sum(a.quantity) as quantity  
		from order_wise_pro_details a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		where a.dtls_id=b.id and b.id=c.dtls_id and b.mst_id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.po_breakdown_id in(".implode(",",$all_po_arr).")";*/
	
	
	$all_po_arr=array_unique(explode(",",chop($all_po_id,",")));
	
	if($db_type==0)
	{
		$sql_knitting="select b.body_part_id, b.color_id, c.mst_id, c.dtls_id, c.po_breakdown_id, c.barcode_no, c.qnty as quantity  
		from pro_grey_prod_entry_dtls b, pro_roll_details c 
		where b.id=c.dtls_id and b.mst_id=c.mst_id and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in(".implode(",",$all_po_arr).")";
	}
	else
	{
		$all_po_arr=array_chunk($all_po_arr,999);
		$sql_knitting="select a.knitting_source, a.knitting_company, b.mst_id, b.body_part_id, b.color_id, c.mst_id, c.dtls_id, c.po_breakdown_id, c.barcode_no, c.qnty as quantity  
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 and a.entry_form=2 
        and a.receive_basis=2 and a.status_active = 1 and a.is_deleted = 0 and a.booking_no in($program_no)";



		$p=1;
		if(!empty($all_po_arr))
		{
			foreach($all_po_arr as $po_id)
			{
				if($p==1) $sql_knitting .=" and (c.po_breakdown_id in(".implode(',',$po_id).")"; else $sql_knitting .=" or c.po_breakdown_id in(".implode(',',$po_id).")";
				$p++;
			}
			$sql_knitting .=" )";
		}
	}
	$sql_knitting_result=sql_select($sql_knitting);
	$kinitting_data=array();
	$all_barcode="";
	foreach($sql_knitting_result as $row)
	{
		$all_barcode.=$row[csf("barcode_no")].",";
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["quantity"]+=$row[csf("quantity")];
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["mst_id"].=$row[csf("mst_id")].",";
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["barcode_no"].=$row[csf("barcode_no")].",";
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["knitting_source"]=$row[csf("knitting_source")];
		$kinitting_data[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]["knitting_company"]=$row[csf("knitting_company")];
	}
	//echo '<pre>';print_r($kinitting_data);
	
	$all_barcode=chop($all_barcode,",");
	
	// delivery info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_delivery="select barcode_no, qnty  from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=56 and barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_delivery="select barcode_no, qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=56 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_delivery .=" and (barcode_no in(".implode(',',$bar_code).")"; else $sql_delivery .=" or barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_delivery .=" )";
		}
		
	}
	
	$sql_delivery_result=sql_select($sql_delivery);
	$delivery_data=array();
	foreach($sql_delivery_result as $row)
	{
		$delivery_data[$row[csf("barcode_no")]]=$row[csf("qnty")];
	}
	
	
	// receive info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_receive="select a.barcode_no, a.qnty  from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(2,58) and b.trans_id<>0 and a.barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_receive="select a.barcode_no, a.qnty  from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(2,58) and b.trans_id<>0 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_receive .=" and (a.barcode_no in(".implode(',',$bar_code).")"; else $sql_receive .=" or a.barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_receive .=" )";
		}
		
	}
	
	$sql_receive_result=sql_select($sql_receive);
	$receive_data=array();
	foreach($sql_receive_result as $row)
	{
		$receive_data[$row[csf("barcode_no")]]=$row[csf("qnty")];
	}
	
	// Issue info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_issue="select barcode_no, qnty  from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=61 and barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_issue="select barcode_no, qnty from pro_roll_details where status_active=1 and is_deleted=0 and entry_form=61 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_issue .=" and (barcode_no in(".implode(',',$bar_code).")"; else $sql_issue .=" or barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_issue .=" )";
		}
		
	}
	
	$sql_issue_result=sql_select($sql_issue);
	$issue_data=array();
	foreach($sql_issue_result as $row)
	{
		$issue_data[$row[csf("barcode_no")]]=$row[csf("qnty")];
	}
	
	
	// Transfer info
	if($all_barcode!="")
	{
		$all_barcode_arr=array_unique(explode(",",$all_barcode));
		if($db_type==0)
		{
			$sql_transfer="select a.barcode_no, (case when b.trans_type=5 then a.qnty else 0 end) as trans_in, (case when b.trans_type=6 then a.qnty else 0 end) as trans_out  from pro_roll_details a, order_wise_pro_details b where a.dtls_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=83 and b.entry_form=83 and a.barcode_no in (".implode(",",$all_barcode_arr).")";
		}
		else
		{
			$all_barcode_arr=array_chunk($all_barcode_arr,999);
			$sql_transfer="select a.barcode_no, b.trans_type, a.qnty from pro_roll_details a, order_wise_pro_details b where a.dtls_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=83 and b.entry_form=83 ";
			$p=1;
			foreach($all_barcode_arr as $bar_code)
			{
				if($p==1) $sql_transfer .=" and (a.barcode_no in(".implode(',',$bar_code).")"; else $sql_transfer .=" or a.barcode_no in(".implode(',',$bar_code).")";
				$p++;
			}
			$sql_transfer .=" )";
		}
		
	}
	
	//echo $sql_transfer;
	
	$sql_transfer_result=sql_select($sql_transfer);
	$transfer_data=array();
	foreach($sql_transfer_result as $row)
	{
		if($row[csf("trans_type")]==5)
			$transfer_data[$row[csf("barcode_no")]]["trans_in"]=$row[csf("qnty")];
		else
			$transfer_data[$row[csf("barcode_no")]]["trans_out"]=$row[csf("qnty")];
	}
	//var_dump($transfer_data);
	ob_start();
	?>
	<fieldset style="width:2020px;">
    <div style="color:#FF0000; font-size:16px; font-weight:bold; float:left; width:635px">Report will generate correctly if Color wise production maintained.</div>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000" class="rpt_table" >
        <thead>
            <th width="30">SL No</th>
            <th width="100">Company Name</th>
            <th width="100">Working Company</th>
            <th width="100">Job No</th>
            <th width="110">Style No</th>
            <th width="110">Order No</th>
            <th width="120">Body Part</th>
            <th width="110">Composition</th>
            <th width="110">Constraction</th>
            <th width="120">Color Name</th>
            <th width="100">Grey Qty.</th>
            <th width="100">Kniting Production Qty.</th>
            <th width="100">Knitting Balance Qty.</th>
            <th width="100">Knitting Delivery TO Store</th>
            <th width="100">Delivery Balance</th>
            <th width="100">Grey Fabric Rccvd</th>
            <th width="100">Rcvd Balance</th>
            <th width="100">Issue Qty.</th>
            <th width="100">In Hand Qty.</th>
            <th width="">Remarks</th>
        </thead>
    </table>
    <div style="width:2020px; overflow-y:scroll; max-height:450px;" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000" class="rpt_table" id="tbl_list_search">
		<?
		$i=1;
		foreach($details_data as $po_id=>$po_result)
		{
			foreach($po_result as $body_part_id=>$body_result)
			{
				foreach($body_result as $color_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$knit_qnty=$grey_delivery=$grey_receive=$grey_issue=$knit_balance=$delivery_balance=$receive_balance=$in_hand=$transfer_in=$transfer_out=0;
					$all_barcode="";
					$knit_qnty=$kinitting_data[$po_id][$body_part_id][$color_id]["quantity"];
					$all_barcode=array_unique(explode(",",chop($kinitting_data[$po_id][$body_part_id][$color_id]["barcode_no"],",")));
					foreach($all_barcode as $b_code)
					{
						$grey_delivery+=$delivery_data[$b_code];
						$grey_receive+=$receive_data[$b_code];
						$grey_issue+=$issue_data[$b_code];
						$transfer_in+=$transfer_data[$b_code]["trans_in"];
						$transfer_out+=$transfer_data[$b_code]["trans_out"];
					}
					$grey_receive=$grey_receive+$transfer_in;
					$grey_issue=$grey_issue+$transfer_out;
					
					$knit_balance=$row[('grey_fab_qnty')]-$knit_qnty;
					$delivery_balance=$knit_qnty-$grey_delivery;
					$receive_balance=$row[('grey_fab_qnty')]-$grey_receive;
					$in_hand=$grey_receive-$grey_issue;
					
					$all_mst_id=chop($kinitting_data[$po_id][$body_part_id][$color_id]["mst_id"],",");
					
					$data_p= $row[('job_no')]."_".$row[('body_part_id')]."_".$row[('construction')]."_".$row[('fabric_color_id')]."_".chop($kinitting_data[$po_id][$body_part_id][$color_id]["barcode_no"],",");



					if ($kinitting_data[$po_id][$body_part_id][$color_id]["knitting_source"] == 1)
						$knitting_company = $company_library[$kinitting_data[$po_id][$body_part_id][$color_id]["knitting_company"]];
					else
						$knitting_company = $suplier_details[$kinitting_data[$po_id][$body_part_id][$color_id]["knitting_company"]];
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100"><p><? echo $company_library[$row[('company_name')]]; ?></p></td>
						<td width="100"><p><? echo $knitting_company; ?></p></td>
						<td width="100"><p><? echo $row[('job_no')]; ?></p></td>
						<td width="110"><p><? echo $row[('style_ref_no')]; ?></p></td>
						<td width="110"><p><? echo $row[('po_number')]; ?></p></td>
						<td width="120"><p><? echo $body_part[$row[('body_part_id')]]; ?></p></td>
						<td width="110"><p><? echo $row[('composition')]; ?></p></td>
						<td width="110"><p><? echo $row[('construction')]; ?></p></td>
						<td width="120"><p><? echo $color_library[$row[('fabric_color_id')]]; ?></p></td>
						<td width="100" align="right"><? echo number_format($row[('grey_fab_qnty')],2); ?></td>
						<td align="right" width="100"><? echo number_format($knit_qnty,2); ?></td>
						<td width="100" align="right"><? echo number_format($knit_balance,2);  ?></td>
						<td width="100" align="right"><? echo number_format($grey_delivery,2);  ?></td>
						<td width="100" align="right"><? echo number_format($delivery_balance,2); ?></td>
						<td width="100" align="right"><? echo number_format($grey_receive,2);  ?></td>
						<td width="100" align="right"><? echo number_format($receive_balance,2);  ?></td>
						<td width="100" align="right"><? echo number_format($grey_issue,2); ?></td>
						<td align="right" width="100"><? echo number_format($in_hand,2,'.',''); ?>  </td>
						<td><p>&nbsp;</p></td>
					</tr>
					<?
					$total_grey_fab_qnty+=$row[('grey_fab_qnty')];
					$total_knit_qnty+=$knit_qnty;
					$total_knit_balance+=$knit_balance;
					$total_grey_delivery+=$grey_delivery;
					$total_delivery_balance+=$delivery_balance;
					
					$total_grey_receive+=$grey_receive;
					$total_receive_balance+=$receive_balance;
					$total_grey_issue+=$grey_issue;
					$total_in_hand+=$in_hand;
					
					$i++; 
				}
			}
			 
		}
    ?>
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000" class="rpt_table">
         <tfoot>
            <th width="30"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="100"></th>
            <th width="110"></th>
            <th width="110"></th>
            <th width="120"></th>
            <th width="110"></th>
            <th width="110"></th>
            <th width="120" align="right">Total: </th>
            <th width="100" align="right" id="value_total_grey_fab_qnty"><? echo number_format($total_grey_fab_qnty,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_knit_qnty"><? echo number_format($total_knit_qnty,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_knit_balance"><? echo number_format($total_knit_balance,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_grey_delivery"><? echo number_format($total_grey_delivery,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_delivery_balance"><? echo number_format($total_delivery_balance,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_grey_receive"><? echo number_format($total_grey_receive,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_receive_balance"><? echo number_format($total_receive_balance,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_grey_issue"><? echo number_format($total_grey_issue,2,'.',''); ?></th>
            <th width="100" align="right" id="value_total_in_hand"><? echo number_format($total_in_hand,2,'.',''); ?></th>
            <th width=""></th>
        </tfoot>
     </table>
    </div>
	</fieldset>
	 <script type="text/javascript">
		var tableFilters = 
		{
			//col_47: "none",
			col_operation: {
				id: ["value_total_grey_fab_qnty","value_total_knit_qnty","value_total_knit_balance","value_total_grey_delivery","value_total_delivery_balance","value_total_grey_receive","value_total_receive_balance","value_total_grey_issue","value_total_in_hand"],
		   col: [10,11,12,13,14,15,16,17,18],
		   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}	
		}
		setFilterGrid('tbl_list_search',-1,tableFilters);
	</script>
	<?

	exit();
}

if ($action == "production_popup--------") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	?>
	<script>

		var tableFilters = {
			col_operation: {
				id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
				col: [7, 8, 9],
				operation: ["sum", "sum", "sum"],
				write_method: ["innerHTML", "innerHTML", "innerHTML"]
			}
		}
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1, tableFilters);
		});

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";

			$('#tbl_list_search tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";

			$('#tbl_list_search tr:first').show();
		}

	</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="115">Receive Id</th>
					<th width="95">Receive Basis</th>
					<th width="110">Product Details</th>
					<th width="100">Booking / Program No</th>
					<th width="60">Machine No</th>
					<th width="75">Production Date</th>
					<th width="80">Inhouse Production</th>
					<th width="80">Outside Production</th>
					<th width="80">Production Qnty</th>
					<th width="70">Challan No</th>
					<th>Kniting Com.</th>
				</thead>
			</table>
			<div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="tbl_list_search">
					<?
					$i = 1;
					$total_receive_qnty = 0;
					$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

					$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
    //echo $sql;
					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$total_receive_qnty += $row[csf('quantity')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
							<td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
							<td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="right" width="80">
								<?
								if ($row[csf('knitting_source')] != 3) {
									echo number_format($row[csf('quantity')], 2, '.', '');
									$total_receive_qnty_in += $row[csf('quantity')];
								} else
								echo "&nbsp;";
								?>
							</td>
							<td align="right" width="80">
								<?
								if ($row[csf('knitting_source')] == 3) {
									echo number_format($row[csf('quantity')], 2, '.', '');
									$total_receive_qnty_out += $row[csf('quantity')];
								} else
								echo "&nbsp;";
								?>
							</td>
							<td align="right" width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
							<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td><p><?
							if ($row[csf('knitting_source')] == 1)
								echo $company_library[$row[csf('knitting_company')]];
							else if ($row[csf('knitting_source')] == 3)
								echo $supplier_details[$row[csf('knitting_company')]];
							?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">  
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="115">&nbsp;</th>
					<th width="95">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="75" align="right">Total</th>
					<th width="80" align="right" id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
					<th width="80" align="right" id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
					<th width="80" align="right" id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
					<th width="70">&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>	
		</div>
	</fieldset>


	<?
	exit();
}