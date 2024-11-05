<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action == "order_no_search_popup")
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

		function toggle(x, origColor) 
		{
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) 
		{

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
			var id = ''; var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_order_id').val(id);
			$('#hide_order_no').val(name);
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:850px;">
					<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Job Year</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Order No</th>
							<th>Shipment Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
							<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
									?>
								</td>
								<td>
		                            <?
		                                echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
		                            ?>
		                        </td>
								<td align="center">	
									<?
									$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No", 4 => "Int Ref");
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + 
									document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value + '**' + document.getElementById('cbo_year').value, 'create_order_no_search_list_view', 'search_div', 'dia_wise_program_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action == "create_order_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];

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
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else if ($search_by == 3)
		$search_field = "a.job_no";
	else
		$search_field = "b.grouping";

	$start_date = $data[4];
	$end_date = $data[5];
	$cbo_year = str_replace("'", "", $data[6]);
	$year_cond = "";
	if (trim($cbo_year) != 0) 
	{
		if ($db_type == 0)
			$year_cond = " and YEAR(a.insert_date)=$cbo_year";
		else if ($db_type == 2)
			$year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
		else
			$year_cond = "";
	}

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
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
        $year_field = ""; //defined Later

    $sql = "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, PO No, Internal Ref., Shipment Date", "80,80,50,70,140,170,80", "850", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,pub_shipment_date", "", '', '0,0,0,0,0,0,0,3', '', 1);
    exit();
}

if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
	<script>
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
					<tr>
						<td align="center" width="100%">
							<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
								<thead>
									<th width="140">Buyer Name</th>
									<th>Job Year</th>
									<th>Search By</th>
									<th id="search_by_td_up" width="80">Booking No</th>
									<th width="180">Booking Date</th>
									<th>&nbsp;</th>
									<input type="hidden" name="selected_booking" id="selected_booking" value="" />
								</thead>
								<tr>
									<td align="center">
										<?
										echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
										?>
									</td>
									<td>
			                            <?
			                                echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
			                            ?>
			                        </td>
									<td align="center">	
										<?
										$search_by_arr = array(1 => "Booking No", 2 => "Job No");
										$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
										echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
										?>
									</td>     
									<td align="center" id="search_by_td">				
										<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
									</td>
									<td>
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '_' + document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('cbo_search_by').value + '_' + 
										document.getElementById('txt_search_common').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' + document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'dia_wise_program_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<? 
							echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
							echo load_month_buttons();  ?>
						</td>
					</tr>
					<tr>
						<td align="center"valign="top" id="search_div"></td>
					</tr>
				</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company_id = $data[0];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_id=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "a.booking_no_prefix_num";
	else
		$search_field = "a.job_no";

	$start_date = $data[4];
	$end_date = $data[5];
	$cbo_year = str_replace("'", "", $data[6]);
	$year_cond = "";
	if (trim($cbo_year) != 0) 
	{
		if ($db_type == 0)
			$year_cond = " and YEAR(c.insert_date)=$cbo_year";
		else if ($db_type == 2)
			$year_cond = " and to_char(c.insert_date,'YYYY')=$cbo_year";
		else
			$year_cond = "";
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and b.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and b.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

	$sql = "SELECT a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved 
	from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0  group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by a.booking_no_prefix_num Desc";
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	
	exit(); 
}

if ($action == "mc_group_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	echo "Development Later";die;
	?>

	<script>
		function js_set_value(str)
		{
			document.getElementById('hide_mc_group').value=str;
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:850px;">
					<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Job Year</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Order No</th>
							<th>Shipment Date</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
							<input type="hidden" name="hide_mc_group" id="hide_mc_group" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
									?>
								</td>
								<td>
		                            <?
		                                echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
		                            ?>
		                        </td>
								<td align="center">	
									<?
									$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No", 4 => "Int Ref");
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + 
									document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value + '**' + document.getElementById('cbo_year').value, 'create_mc_group_search_list_view', 'search_div', 'dia_wise_program_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action == "create_mc_group_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];

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
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else if ($search_by == 3)
		$search_field = "a.job_no";
	else
		$search_field = "b.grouping";

	$start_date = $data[4];
	$end_date = $data[5];
	$cbo_year = str_replace("'", "", $data[6]);
	$year_cond = "";
	if (trim($cbo_year) != 0) 
	{
		if ($db_type == 0)
			$year_cond = " and YEAR(a.insert_date)=$cbo_year";
		else if ($db_type == 2)
			$year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
		else
			$year_cond = "";
	}

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
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
        $year_field = ""; //defined Later

    $sql = "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $year_cond order by b.id, b.pub_shipment_date";

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, PO No, Internal Ref., Shipment Date", "80,80,50,70,140,170,80", "850", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,pub_shipment_date", "", '', '0,0,0,0,0,0,0,3', '', 1);
    exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_int_ref_no=str_replace("'","",$txt_ref_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$hide_order_id=str_replace("'","",$hide_order_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_mc_dia=str_replace("'","",$txt_mc_dia);
	$txt_mc_gg=str_replace("'","",$txt_mc_gg);
	$txt_mc_group=str_replace("'","",$txt_mc_group);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
 	if($template==1)
	{
		if($cbo_buyer_name==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
					$buyer_id_cond2=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else 
				{	
					$buyer_id_cond="";
					$buyer_id_cond2="";
				}
			}
			else
			{
				$buyer_id_cond="";
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
			$buyer_id_cond2=" and a.buyer_name=$cbo_buyer_name";
		}
		if ($txt_mc_dia!="") 
		{
			$mc_dia_cond=" and dia_width='$txt_mc_dia'";
			$mc_dia_cond2=" and b.machine_dia='$txt_mc_dia'";
			$booking_dia_cond=" and b.dia_width='$txt_mc_dia'";
		}
		if ($txt_mc_gg!="") 
		{
			$mc_gg_cond=" and gauge='$txt_mc_gg'";
			$mc_gg_cond2=" and b.machine_gg='$txt_mc_gg'";
		}
		if ($txt_mc_group!="") 
		{
			$mc_group_cond=" and machine_group='$txt_mc_group'";
			$mc_group_cond2=" and b.machine_group='$txt_mc_group'";
		}

		//cbo_year
		$year_cond = "";
		$cbo_year = str_replace("'", "", $cbo_year);
		if (trim($cbo_year) != 0) 
		{
			if ($db_type == 0)
				$year_cond = " AND YEAR(a.insert_date) = ".$cbo_year."";
			else if ($db_type == 2)
				$year_cond = " AND to_char(a.insert_date,'YYYY') = ".$cbo_year."";
			else
				$year_cond = "";
		}
		
		//txt_file_no
		if ($txt_file_no == "")
			$file_no_cond = "";
		else
			$file_no_cond = " AND b.file_no = '".$txt_file_no."' ";

		//txt_int_ref_no
		if ($txt_int_ref_no == "")
			$internal_cond = "";
		else
			$internal_cond = " AND b.grouping = '".$txt_int_ref_no."' ";

		// $txt_order_no
		// $hide_order_id
		$order_cond = "";
		if (trim($txt_order_no) != "") 
		{
			if (str_replace("'", "", $hide_order_id) != "")
			{
				$order_cond = " AND b.id IN(" . str_replace("'", "", $hide_order_id) . ")";
				// $booking_poId_cond = " AND b.po_break_down_id=$hide_order_id";
			}
			else
			{
				$po_number = "%" . trim(str_replace("'", "", $txt_order_no)) . "%";
				$order_cond = " AND b.po_number LIKE '".$po_number."'";
			}
		}

		//txt_booking_no
		$booking_search_cond = "";
		if (trim($txt_booking_no) != "") 
		{
			$booking_number = "%" . trim($txt_booking_no) . "%";
			$booking_search_cond = " AND a.booking_no LIKE '".$booking_number."'";
			$booking_cond = " AND a.booking_no_prefix_num =trim($txt_booking_no)"; 
		}
		
		//txt_date_from
		//txt_date_to
		if (trim($txt_date_from) != "" && trim($txt_date_to) != "") 
		{
			if ($based_on == 2)
			{
				$date_cond = " AND b.pub_shipment_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
			}
			else
			{
				$date_cond = " AND b.pub_shipment_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
			}
		} 
		else
		{
			$date_cond = "";
		}
		
		ob_start();

		$con = connect();
		$r_id=execute_query("delete from tmp_prog_no where userid=$user_name");
		$r_id2=execute_query("delete from tmp_poid where userid=$user_name");
        oci_commit($con);

		$machine_no_sql="SELECT id, company_id, machine_no, machine_group, dia_width, gauge, prod_capacity, seq_no, machine_type
		from lib_machine_name where company_id=$company_name and category_id=1 and status_active=1 and is_deleted=0 and machine_group is not null and dia_width is not null and gauge is not null $mc_dia_cond $mc_gg_cond $mc_group_cond";
		// echo $machine_no_sql;
		$machine_no_sql_result=sql_select($machine_no_sql);
		$machine_data_array=array();
		foreach($machine_no_sql_result as $row)
		{
			$mc_dia_gg_str=$row[csf('dia_width')].'x'.$row[csf('gauge')];
			$machine_data_array[$row[csf('machine_group')]][$mc_dia_gg_str]['prod_capacity']+=$row[csf('prod_capacity')];
			$machine_data_array[$row[csf('machine_group')]][$mc_dia_gg_str]['no_of_mc']++;
			$machine_id_array[$row[csf('id')]]=$row[csf('machine_group')];
		}
		// echo "<pre>";print_r($machine_data_array);die;

		if($order_cond != "" || $job_cond!="" || $internal_cond!="" || $file_no_cond!="")
		{
			$costing_sql = sql_select("SELECT a.job_no, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_name $order_cond $year_cond $internal_cond $file_no_cond $buyer_id_cond2 $date_cond");
			foreach ($costing_sql as $row)
			{
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
				$po_array[$row[csf('id')]]['year'] = $row[csf('year')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['ref_no'] = $row[csf('grouping')];
				$po_id .= $row[csf('id')] . ",";
			}
		}
		//echo "<pre>";
		//print_r($po_array); die;

		$po_id_cond = "";
		if ($po_id != "") 
		{
			$po_id = substr($po_id, 0, -1);
			if ($db_type == 0)
			{
				$po_id_cond = "AND c.po_id IN(".$po_id.")";
				$booking_poId_cond = "AND b.po_break_down_id IN(".$po_id.")";
			}
			else
			{
				$po_ids = explode(",", $po_id);
				if (count($po_ids) > 999)
				{
					$po_id_cond = "AND (";
					$booking_poId_cond = "AND (";
					$po_ids = array_chunk($po_ids, 999);
					$z = 0;
					foreach ($po_ids as $id)
					{
						$id = implode(",", $id);
						if ($z == 0)
						{
							$po_id_cond .= " c.po_id IN(".$id.")";
							$booking_poId_cond .= " b.po_break_down_id IN(".$id.")";
						}
							
						else
						{
							$po_id_cond .= " OR c.po_id IN(".$id.")";
							$booking_poId_cond .= " OR b.po_break_down_id IN(".$id.")";
						}
							
						$z++;
					}
					$po_id_cond .= ")";
					$booking_poId_cond .= ")";
				}
				else
				{
					$po_id_cond = "AND c.po_id IN(".$po_id.")";
					$booking_poId_cond = "AND b.po_break_down_id IN(".$po_id.")";
				}
			}
		}

		// =================================================== Program Start ===============================
		$program_sql="SELECT a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, 
		b.machine_dia, b.machine_gg, b.machine_group, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, b.location_id, b.stitch_length, c.program_qnty as prog_qty, c.booking_no, c.po_id, c.yarn_desc, d.booking_type, d.is_short 
		FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_booking_mst d 
		WHERE a.id=b.mst_id AND b.id=c.dtls_id AND a.booking_no=d.booking_no AND a.company_id=$company_name 
		AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 AND c.is_sales=0 $buyer_id_cond $po_id_cond $booking_search_cond $mc_dia_cond2 $mc_gg_cond2 $mc_group_cond2 "; //$year_cond
		// AND b.id LIKE '12370'
		// echo $program_sql;
		$program_sql_result=sql_select($program_sql);
		$plan_data_arr=array();$prog_no_check=array();
		foreach($program_sql_result as $row)
		{
			$prog_mc_dia_gg=$row[csf('machine_dia')].'x'.$row[csf('machine_gg')];
			if ($row[csf('knitting_source')]==1) 
			{
				$plan_data_arr[$row[csf('machine_group')]][$prog_mc_dia_gg]['inside_program_qty']+=$row[csf('prog_qty')];
			}
			if ($row[csf('knitting_source')]==3) 
			{
				$plan_data_arr[$row[csf('machine_group')]][$prog_mc_dia_gg]['outside_program_qty']+=$row[csf('prog_qty')];
			}
			$plan_data_arr[$row[csf('machine_group')]][$prog_mc_dia_gg]['prog_no']=$row[csf('id')];
			$plan_array[$row[csf('booking_no')]][$row[csf('po_id')]][$row[csf('dia')]][$row[csf('yarn_desc')]]=$row[csf('prog_qty')];

			if($machind_dia_ids=='') $machind_dia_ids=$row[csf('machine_dia')];else $machind_dia_ids.=",".$row[csf('machine_dia')];


            // $program_id_arr[$row[csf('id')]]=$row[csf('id')];
            if( $prog_no_check[$row[csf('id')]] == "" )
            {
                $prog_no_check[$row[csf('id')]]=$row[csf('id')];
                $prog_no = $row[csf('id')];
                // echo "insert into tmp_prog_no (userid, prog_no) values ($user_name,$prog_no)";
                $r_id=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,$prog_no)");
            }
		}
		$machine_dia=implode(",",array_unique(explode(",",$machind_dia_ids)));
		oci_commit($con);
		// echo "<pre>";print_r($plan_array);die;
		// ============================================== Program End ===============================

		// ============================================== Knitting Production Start =================
		$prod_data_array=array();
		$sql_prod=sql_select("SELECT a.booking_id as prog_no, a.knitting_source, b.machine_dia, b.grey_receive_qnty
		from inv_receive_master a, pro_grey_prod_entry_dtls b, tmp_prog_no c where a.id=b.mst_id and a.booking_id=c.prog_no and a.company_id=$company_name and a.entry_form=2 and a.item_category=13 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.userid=$user_name");

		foreach($sql_prod as $row)
		{
			$prod_data_array[$row[csf('prog_no')]]['prod_qty']+=$row[csf('grey_receive_qnty')];
		}
		// ============================================== Knitting Production Start =================

		?>
		<fieldset style="width:100%;margin-left:10px;">
			<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong>Dia Wise Knitting Program</strong></td>
                </tr>
            </table>
            <div align="left" style="margin-left:10px"><b><u>Program Done</u></b></div>
		<?
		foreach ($machine_data_array as $mc_group => $mc_group_arr) 
		{
			?>			
			<table style="margin-left:10px; margin-bottom: 10px;" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width-20; ?>" class="rpt_table" >
            <thead>
				<tr>
					<th width="100"></th>
					<th width="100">Total No of M/C</th>
					<?
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<th width="80"><? echo $value['no_of_mc']?></th>
						<?
					}
					?>
					<th width="80"></th>
				</tr>
				<tr>
					<th width="100"><? echo $mc_group; ?></th>
					<th width="100">Avg. Capacity</th>
					<?
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<th width="80"><? echo $mc_avg_capacity=decimal_format($value['prod_capacity']/$value['no_of_mc'],1,',');?></th>
						<?
					}
					?>
					<th width="80"></th>
				</tr>
				<tr>
					<th width="100"></th>
					<th width="100">Capacity per day</th>
					<?
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<th width="80"><? echo decimal_format($mc_avg_capacity*$value['no_of_mc'],1,','); ?></th>
						<?
					}
					?>
					<th width="80"></th>
				</tr>
				<tr>
					<th width="100"></th>
					<th width="100">M/Dia & Gauge</th>
					<?
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<th width="80"><? echo $mc_dia_gg; ?></th>
						<?
					}
					?>
					<th width="80">Total</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td width="100" colspan="2" align="center"><?echo $mc_group;?> Inside Program</td>
					<?
					$total_in_qnty=0;
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						$all_program .= $plan_data_arr[$mc_group][$mc_dia_gg]['prog_no'].',';
						// echo chop($all_program,',');
						$all_program=implode(",", array_filter(array_unique(explode(",", chop($all_program,',')))));
						?>
						<td width="80" align="right" title="<? echo chop($all_program,','); ?>"><? echo decimal_format($plan_data_arr[$mc_group][$mc_dia_gg]['inside_program_qty'],1,','); ?></td>
						<?
						$total_in_qnty+=$plan_data_arr[$mc_group][$mc_dia_gg]['inside_program_qty'];
						$total_mc_dia_gg_in_qnty[$mc_group][$mc_dia_gg]+=$plan_data_arr[$mc_group][$mc_dia_gg]['inside_program_qty'];
					}
					?>
					<td width="80" align="right"><? echo decimal_format($total_in_qnty,1,','); ?></td>
				</tr>
				<tr bgcolor="#E9F3FF">
					<td width="100" colspan="2" align="center"><?echo $mc_group;?> Outside Program</td>
					<?
					$total_out_qnty=0;
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<td width="80" align="right"><? echo decimal_format($plan_data_arr[$mc_group][$mc_dia_gg]['outside_program_qty'],1,','); ?></td>
						<?
						$total_out_qnty+=$plan_data_arr[$mc_group][$mc_dia_gg]['outside_program_qty'];
						$total_mc_dia_gg_out_qnty[$mc_group][$mc_dia_gg]+=$plan_data_arr[$mc_group][$mc_dia_gg]['outside_program_qty'];
					}
					?>
					<td width="80" align="right"><? echo decimal_format($total_out_qnty,1,','); ?></td>
				</tr>
				<tr>
					<td width="100" colspan="2" align="center"><strong><?echo $mc_group;?> Program Total</strong></td>
					<?
					$total_program_qty=0;
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<td width="80" align="right"><? echo decimal_format($total_mc_dia_gg_in_qnty[$mc_group][$mc_dia_gg]+$total_mc_dia_gg_out_qnty[$mc_group][$mc_dia_gg],1,','); ?></td>
						<?
						$total_program_qty+=$total_mc_dia_gg_in_qnty[$mc_group][$mc_dia_gg]+$total_mc_dia_gg_out_qnty[$mc_group][$mc_dia_gg];
						$total_program_qty_arr[$mc_group][$mc_dia_gg]+=$total_mc_dia_gg_in_qnty[$mc_group][$mc_dia_gg]+$total_mc_dia_gg_out_qnty[$mc_group][$mc_dia_gg];
					}
					$summary_total_in_qnty+=$total_in_qnty;
					$summary_total_out_qnty+=$total_out_qnty;
					?>
					<td width="80" align="right"><? echo decimal_format($total_program_qty,1,','); ?></td>
				</tr>
				<tr bgcolor="#E9F3FF">
					<td width="100" colspan="2" align="center"><strong><?echo $mc_group;?> Production Total</strong></td>
					<?
					$total_prod_qty=0;
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						$program_arr = array_filter(array_unique(explode(",", chop($all_program,','))));
                        $prod_total=0;
                        foreach ($program_arr as $prog) 
                        {
                        	// echo $prog.'<br>';
                            $prod_total+=$prod_data_array[$prog]['prod_qty'];
                        }
						?>
						<td width="80" align="right" title="<? echo chop($all_program,',');?>"><? echo decimal_format($prod_total,1,','); ?></td>
						<?
						$total_prod_qty+=$prod_total;
					}
					?>
					<td width="80" align="right"><? echo decimal_format($total_prod_qty,1,','); ?></td>
				</tr>
				<tr>
					<td width="80" colspan="2" align="center"><strong><?echo $mc_group;?> Production Balance</strong></td>
					<?
					$total_production_balance=0;
					foreach ($mc_group_arr as $mc_dia_gg => $value) 
					{
						?>
						<td width="80" align="right"><? echo decimal_format($total_program_qty_arr[$mc_group][$mc_dia_gg]-$prod_total,1,','); ?></td>
						<?
						$total_production_balance+=$total_program_qty_arr[$mc_group][$mc_dia_gg]-$prod_total;
					}
					?>
					<td width="80" align="right"><? echo decimal_format($total_production_balance,1,','); ?></td>
				</tr>
			</tbody>
			</table>
			<?
		}
		// die;



		// $po_array=return_library_array( "select id, is_confirmed from wo_po_break_down", "id", "is_confirmed"  );
		// ====================== Pending Program / Unprogram sql Start======================
		$block_qnty_rib=0; $block_qnty_sj=0; $confirmed_qnty_rib=0; $confirmed_qnty_sj=0;
		$sql="SELECT a.booking_no, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.dia_width, b.construction, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $booking_poId_cond $booking_cond $booking_dia_cond  $year_cond
		group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width, b.construction order by b.dia_width"; // $year_cond
		//  and b.construction='Single Jersey' and b.dia_width='60'
		// echo $sql;
		$result=sql_select($sql);
		$prog_no_check=array();
		foreach($result as $row)
		{
			if( $prog_no_check[$row[csf('po_break_down_id')]] == "" )
            {
                $po_id_check[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
                $po_id = $row[csf('po_break_down_id')];
                $r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_name,$po_id)");
            }
		}
		oci_commit($con);
		$po_array=return_library_array( "select id, is_confirmed from wo_po_break_down a, tmp_poid b where a.id=b.poid ", "id", "is_confirmed"  );
		
		$fabric_dia_array=array(); $fabric_construction_array=array();		
		foreach($result as $row)
		{
			$is_confirmed=$po_array[$row[csf('po_break_down_id')]];
			$program_qnty=$plan_array[$row[csf('booking_no')]][$row[csf('po_break_down_id')]][$row[csf('dia_width')]][$row[csf('pre_cost_fabric_cost_dtls_id')]];
			// echo $program_qnty.'<br>';
			$pending_qnty=$row[csf('qnty')]-$program_qnty;

			// if($is_confirmed==1) $confirmed_qnty_rib+=$pending_qnty; else $block_qnty_rib+=$pending_qnty;

			$fabric_dia_array[$row[csf('dia_width')]]=$row[csf('dia_width')];
			$fabric_construction_array[$row[csf('construction')]]['construction']=$row[csf('construction')];
			$fabric_construction_array[$row[csf('construction')]]['fab_dia'][$row[csf('dia_width')]]+=$pending_qnty;

			$fabric_cons_array[$row[csf('construction')]]=$row[csf('construction')];
			if ($is_confirmed==1) 
			{
				// $confirmed_qnty+=$pending_qnty;
				$confirm_qty_array[$row[csf('construction')]]+=$pending_qnty;
				$tot_confirmed_qnty+=$pending_qnty;
			}
			else
			{
				// $block_qnty+=$pending_qnty;
				$block_qty_array[$row[csf('construction')]]+=$pending_qnty;
				$tot_block_qnty+=$pending_qnty;
			}
		}// end for each
		// echo "<pre>";print_r($fabric_cons_array);die;
		// ====================== Pending Program / Unprogram sql End======================
		
		$machine_dia=array_filter(explode(",",$machine_dia));
		
		$tbl_width=300+count($machine_dia)*100;
		$colspan=count($machine_dia);

		$tbl_width=260+count($fabric_dia_array)*100;
		$tbl_width2=260+count($fabric_cons_array)*80; //Pending Program/Plan summary

		?>
       	<div align="left" style="margin-left:10px"><b><u>Pending Program/ Unprogram</u></b></div>
        <table style="margin-left:10px" cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table">
            <thead>
                <th width="120">Fabric Dia</th>
                <?
					foreach($fabric_dia_array as $val)
					{
						echo "<th width='80'>".$val."</th>";
					}
				?>
                <th>Total</th>
            </thead>
            <tbody>
            	<?
            	$i=1;
            	foreach ($fabric_construction_array as $key => $value) 
            	{
            		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            		?>
            		<tr bgcolor="<? echo $bgcolor;?>" id="trr_<? echo $i;?>" onclick="change_color('trr_<? echo $i;?>','<? echo $bgcolor;?>')">
            			<td width='80'><? echo $value['construction']; ?></td>
            			<?
            			foreach ($fabric_dia_array as $dia_val) 
            			{
            				?>
            				<td width='80' align="right"><? echo decimal_format($value['fab_dia'][$dia_val],1,','); ?></td>
            				<?
            				$total_qnty_pending+=$value['fab_dia'][$dia_val];
            				$total_dia_wise[$dia_val]+=$value['fab_dia'][$dia_val];
            			}
            			?>
            			<td align="right" align="right"><? echo decimal_format($total_qnty_pending,1,','); ?></td>
            		</tr>
            		<?
            		$i++;
            	}
            	?>                    
            </tbody>
            <tfoot>
                <th>Sub Total</th>
                <?
                    $sub_total_qnty_pending=0;
                    foreach($fabric_dia_array as $fb_dia)
                    { 
                        echo "<th width='100' align='right'>".decimal_format($total_dia_wise[$fb_dia],1,',')."</th>";
                        $sub_total_qnty_pending+=$total_dia_wise[$fb_dia];
                    }
                ?>
                <th align="right"><? echo decimal_format($sub_total_qnty_pending,1,','); ?></th>
            </tfoot>
        </table>
        <br />
        <?
			$tot_inside=$summary_total_in_qnty;
			$tot_outside=$summary_total_out_qnty;
			$tot_program_qnty=$tot_inside+$tot_outside;
			$tot_inside_perc=($tot_inside/$tot_program_qnty)*100;
			$tot_outside_perc=($tot_outside/$tot_program_qnty)*100;
			
			//$tot_block_qnty=$block_qnty_rib+$block_qnty_sj;
			//$tot_confirmed_qnty=$confirmed_qnty_rib+$confirmed_qnty_sj;
			
			//$tot_sj=$block_qnty_sj+$confirmed_qnty_sj;
			//$tot_rib=$block_qnty_rib+$confirmed_qnty_rib;
			
			$tot_pending_qnty=$tot_confirmed_qnty+$tot_block_qnty;
			
			//$tot_block_perc=($tot_block_qnty/$tot_pending_qnty)*100;
			//$tot_confirmed_perc=($tot_confirmed_qnty/$tot_pending_qnty)*100;
			
			//$sj_perc=($tot_sj/$tot_pending_qnty)*100;
			//$rib_perc=($tot_rib/$tot_pending_qnty)*100;
			
			$grand_tot_booking_qnty=$tot_pending_qnty+$tot_program_qnty;
			$grand_tot_pending_qnty=$grand_tot_booking_qnty-$tot_program_qnty;
			
			$grand_program_perc=($tot_program_qnty/$grand_tot_booking_qnty)*100;
			$grand_pending_perc=($grand_tot_pending_qnty/$grand_tot_booking_qnty)*100;
		?>
        <table style="margin-left:10px" width="1500" border="0">
        	<tr>
            	<td valign="top" width="360">
                	<div align="left"><b><u>Program/Plan Done</u></b></div>
                	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
                    	<thead>
                            <th width="120">Inside</th>
                            <th width="120">Outside</th>
                            <th>Total</th>
           				</thead>
                    	<tr bgcolor="#FFFFFF" id="trd_1" onclick="change_color('trd_1','#FFFFFF')">
                        	<td align="center"><? echo decimal_format($tot_inside,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="center"><? echo decimal_format($tot_outside,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="center"><? echo decimal_format($tot_program_qnty,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF" id="trd_2" onclick="change_color('trd_2','#E9F3FF')">
                        	<td align="center"><? echo decimal_format($tot_inside_perc,1,',')."%"; ?>&nbsp;&nbsp;</td>
                            <td align="center"><? echo decimal_format($tot_outside_perc,1,',')."%"; ?>&nbsp;&nbsp;</td>
                            <td align="center"><? echo decimal_format($tot_inside_perc+$tot_outside_perc,1,',')."%"; ?>&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td></td>
                <td valign="top"  width="<? echo $tbl_width2; ?>">
                	<div align="left"><b><u>Pending Program/Plan</u></b></div>
                	<table cellspacing="0" cellpadding="0" border="1" rules="all"  width="<? echo $tbl_width2; ?>" class="rpt_table">
                    	<thead>
                            <th width="120">Particulars</th>
                            <?
							foreach ($fabric_cons_array as $cons => $value) 
							{
								?>
								<th width="110" align="center"><? echo $value?></th>
								<?
							}
							?>
                            <th width="120">Total</th>
                            <th>%</th>
           				</thead>
                    	<tr bgcolor="#FFFFFF" id="trd_3" onclick="change_color('trd_3','#FFFFFF')">
                        	<td>Block Booking</td>
                        	<?
                        	$tot_block_qnty=0;$tot_block_perc=0;
							foreach ($fabric_cons_array as $cons => $value) 
							{
								?>
								<td width="80" align="right"><? echo decimal_format($block_qty_array[$value],1,','); ?></td>
								<?
								$tot_block_qnty+=$block_qty_array[$value];
								$tot_block_perc=($tot_block_qnty/$tot_pending_qnty)*100;
								$total_block_qnty[$value]+=$block_qty_array[$value];
							}
							?>
                            <td align="right"><? echo decimal_format($tot_block_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right"><? echo decimal_format($tot_block_perc,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF" id="trd_4" onclick="change_color('trd_4','#E9F3FF')">
                        	<td>Confirm Order</td>
                        	<?
                        	$tot_confirmed_qnty=0;$tot_confirmed_perc=0;
							foreach ($fabric_cons_array as $cons => $value) 
							{
								?>
								<td width="80" align="right"><? echo decimal_format($confirm_qty_array[$value],1,','); ?></td>
								<?
								$tot_confirmed_qnty+=$confirm_qty_array[$value];
								$tot_confirmed_perc=($tot_confirmed_qnty/$tot_pending_qnty)*100;
								$total_confirmed_qnty[$value]+=$confirm_qty_array[$value];
							}
							?>
                            <td align="right"><? echo decimal_format($tot_confirmed_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right"><? echo decimal_format($tot_confirmed_perc,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF" id="trd_5" onclick="change_color('trd_5','#FFFFFF')">
                        	<td>Total</td>
                        	<?
							foreach ($fabric_cons_array as $cons => $value) 
							{
								$total=$total_confirmed_qnty[$value]+$total_block_qnty[$value];
								?>
								<td width="80" align="right"><? echo decimal_format($total,1,','); ?></td>
								<?
							}
							?>
                            <td align="right"><? echo decimal_format($tot_pending_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right"><? echo decimal_format($tot_block_perc+$tot_confirmed_perc,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF" id="trd_6" onclick="change_color('trd_6','#E9F3FF')">
                        	<td>In %</td>
                        	<?
                        	$tot_perc_qnty=0;
							foreach ($fabric_cons_array as $cons => $value) 
							{
								$total_block_confirm_qty=$total_confirmed_qnty[$value]+$total_block_qnty[$value];
								$perc=($total_block_confirm_qty/$tot_pending_qnty)*100;
								?>
								<td width="80" align="right"><? echo decimal_format($perc,1,','); ?></td>
								<?
								$tot_perc_qnty+=$perc;
							}
							?>
                            <td align="right"><? echo decimal_format($tot_perc_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right">&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td></td>
                <td valign="top">
                	<div align="left"><b><u>Grand Summary</u></b></div>
                	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table">
                    	<thead>
                            <th width="200">Particulars</th>
                            <th width="120">Qnty</th>
                            <th>%</th>
           				</thead>
                    	<tr bgcolor="#FFFFFF" id="trd_7" onclick="change_color('trd_7','#FFFFFF')">
                        	<td>Total Booking Qnty.</td>
                            <td align="right"><? echo decimal_format($grand_tot_booking_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right"><? echo decimal_format($grand_program_perc+$grand_pending_perc,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF" id="trd_8" onclick="change_color('trd_8','#E9F3FF')">
                        	<td>Total Program Qnty.</td>
                            <td align="right"><? echo decimal_format($tot_program_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right"><? echo decimal_format($grand_program_perc,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF" id="trd_9" onclick="change_color('trd_9','#FFFFFF')">
                        	<td>Total Pending Program Qnty.</td>
                            <td align="right"><? echo decimal_format($grand_tot_pending_qnty,1,','); ?>&nbsp;&nbsp;</td>
                            <td align="right"><? echo decimal_format($grand_pending_perc,1,','); ?>&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
      	</fieldset>      
		<? 
	}
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();	
}


?>