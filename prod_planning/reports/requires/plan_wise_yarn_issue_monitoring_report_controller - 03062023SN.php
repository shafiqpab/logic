<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer")
{
    echo create_drop_down("cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select --", $selected, "");
    exit();
}

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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**', 'create_job_no_search_list_view', 'search_div', 'plan_wise_yarn_issue_monitoring_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

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
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '',1);
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
			//alert(booking_no);
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
								<th width="150">Company Name</th>
								<th width="140">Buyer Name</th>
								<th width="80">Booking No</th>
								<th width="180">Short Booking Date</th>
								<th>&nbsp;</th>
							</thead>
							<tr>
								<td>
									<input type="hidden" id="selected_booking">
									<input type="hidden" id="job_no" value="<? echo $job_IDs;?>">
									<?
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and id = $companyID order by company_name","id,company_name",1, "-- Select Company --", '', "",1);
									?>
								</td>
								<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --",0,"",0 ); ?></td>
								<td>
									<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:75px" />
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('txt_booking_no').value, 'create_booking_search_list_view', 'search_div', 'plan_wise_yarn_issue_monitoring_report_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
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
	if ($data[0]!=0) $company="  company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";
	if ($data[4]!=0) $job_no=" and job_no='$data[4]'"; else $job_no='';
	if ($data[5]!=0) $booking_no=" and booking_no_prefix_num='$data[5]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = " and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = " and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_array=array();
	$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=2 and status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		//print_r( $po_id);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}


	$sql= "select booking_no_prefix_num, booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, supplier_id, is_approved, ready_to_approved,pay_mode from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type in(1,4) and is_short=2 and status_active=1 and is_deleted=0 order by id Desc";


	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="110">Booking No</th>
				<th width="70">Booking Date</th>
				<th width="110">Company</th>
				<th width="110">Buyer</th>
				<th width="110">Job No</th>
				<th width="70">Fabric Nature</th>
				<th width="70">Fabric Source</th>
				<th width="60">Supplier</th>
				<th width="50">Approved</th>
				<th>Is-Ready</th>
			</thead>
		</table>
	</div>
	<div style="width:850px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="845" class="rpt_table" id="tbl_list_search" style="float:left; ">
			<?
			$sqlQuery=sql_select($sql);

			$i=1;
			foreach( $sqlQuery as $row )
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("booking_no")]; ?>');" >
					<td width="30"><?php echo $i; ?></td>
					<td width="110"><?php echo $row[csf("booking_no")]; ?></td>
					<td width="70"><?php echo change_date_format($row[csf("booking_date")]); ?></td>
					<td width="110"><?php echo $comp[$row[csf("company_id")]]; ?></td>
					<td width="110"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
					<td width="110"><?php echo $row[csf("job_no")]; ?></td>
					<td width="70"><?php echo $item_category[$row[csf("item_category")]]; ?></td>
					<td width="70"><?php echo $fabric_source[$row[csf("fabric_source")]]; ?></td>
					<td width="60"><?php if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5){ echo $comp[$row[csf("supplier_id")]];}else {echo $suplier[$row[csf("supplier_id")]];}; ?></td>
					<td width="50"><?php echo $approved[$row[csf("is_approved")]]; ?></td>
					<td><?php echo $is_ready[$row[csf("ready_to_approved")]]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?

		//$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
		//echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "130,80,70,100,90,150,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');

	exit();
}

//for show button

if ($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_allocation_type = str_replace("'", "", $cbo_allocation_type);
	$report_type = str_replace("'", "", $report_type);
	$companyID = str_replace("'", "", $cbo_company_name);
	$buyerId = str_replace("'", "", $cbo_buyer_name);
	$internalRefNo = str_replace("'", "", $txt_internal_ref_no);


	if($companyID>0) $company_cond="and a.company_id=$companyID"; else $company_cond="";
	if($companyID>0) $company_cond_2="and a.company_name=$companyID"; else $company_cond_2="";

	if($buyerId>0)
	{
		$buyer_cond="and a.buyer_id = ".$buyerId."";
		$buyer_cond2="and b.buyer_id = ".$buyerId."";
		$buyer_cond3="and c.buyer_id = ".$buyerId."";
		$buyer_cond4="and e.buyer_name = ".$buyerId."";
	}
	else
	{
		$buyer_cond="";
		$buyer_cond2="";
		$buyer_cond3="";
	}

	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "")
	{

		$jobArr = explode("*",str_replace("'", "", trim($txt_job_no)));

		foreach($jobArr as $job)
		{
			$job_nos .= "'".$job."',";
		}
		$job_nos = chop($job_nos,",");
		$job_cond .= " and b.job_no in ( $job_nos )" ;

	}
	if ($internalRefNo!="") {$internalRefNo_cond="and c.grouping='$internalRefNo'";}else{$internalRefNo_cond="";}
	$booking_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = " and a.booking_no = $txt_booking_no";
	}

	$booking_date_cond = "";
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		$booking_date_cond = " and a.booking_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
	}

	//for program no
	$txt_program_no = str_replace("'", "", $txt_program_no );
	if($txt_program_no != '')
	{
        $sqlProgram = "select a.booking_no as BOOKING_NO from ppl_planning_entry_plan_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.is_sales !=1 and a.dtls_id in(".$txt_program_no.") ".$booking_cond." group by a.booking_no";
		$sqlProgramRslt = sql_select($sqlProgram);
		$bookingNoArr = array();
		foreach($sqlProgramRslt as $row)
		{
			$bookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}

		$booking_cond = " and a.booking_no in('".implode("','",$bookingNoArr)."')";
	}

	//for Print Button Permission
	$print_report = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(1) and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report);
	$print_report2 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(2) and is_deleted=0 and status_active=1");
	$format_ids2 = explode(",", $print_report2);
	$print_report3 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(3) and is_deleted=0 and status_active=1");
	$format_ids3 = explode(",", $print_report3);
	//end for Print Button Permission

	if ($report_type == 1)
	{
		// Main SQL ====>
		$main_sql = "select a.id, a.company_id, a.booking_no, a.booking_type, a.is_approved, a.is_short, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping, c.po_number, b.grey_fab_qnty as qnty
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and a.item_category=2 and a.fabric_source=1
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4)
			and b.grey_fab_qnty>0 $job_cond $booking_cond $booking_date_cond $company_cond $internalRefNo_cond $buyer_cond
			order by  a.job_no,a.buyer_id,a.booking_no";// a.id=b.booking_mst_id
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

			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po'] .= $row[csf("po_number")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po_id'] .= $row[csf("po_break_down_id")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['qnty'] += $row[csf("qnty")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['company_id'] = $row[csf("company_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_approved'] = $row[csf("is_approved")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['booking_type'] = $row[csf("booking_type")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_short'] = $row[csf("is_short")];

			$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			$booking_nos_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$all_po_ids_for_buyer_issue .= $row[csf("po_break_down_id")].",";



			if($poIdChk[$row[csf('booking_no')]] == "")
			{
				$poIdChk[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
				$all_po_id_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
			}

			if($bookingNoChk[$row[csf('booking_no')]] == "")
			{
				$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$all_booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
			}

			if($jobgNoChk[$row[csf('job_no')]] == "")
			{
				$jobgNoChk[$row[csf('job_no')]] = $row[csf('job_no')];
				$all_job_no_arr[$row[csf("job_no")]] = $row[csf("job_no")];
			}


			if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==2)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Main";
			}
			else if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==1)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Short";
			}
			else if($row[csf("booking_type")] ==4 )
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Sample";
			}
		}
		unset($result);



		$job_numbers="'".implode("','",$job_no_arr)."'";
		$booking_nos="'".implode("','",$booking_nos_arr)."'";

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		$job_number_arr = array_unique(explode(",",chop($job_numbers,",")));
		$booking_nos = implode(",",array_unique(explode(",",chop($booking_nos,","))));
		$booking_nos_arr = array_unique(explode(",",chop($booking_nos,",")));

		$all_booking_no_arr = array_filter($all_booking_no_arr);

		if(!empty($all_booking_no_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_booking_no_arr as $bookNo)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_NO,USERID) VALUES('".$bookNo."', ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;



		$all_job_no_arr = array_filter($all_job_no_arr);

		if(!empty($all_job_no_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_JOB_NO WHERE USERID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_job_no_arr as $jobNo)
			{
				execute_query("INSERT INTO TMP_JOB_NO(JOB_NO,USERID) VALUES('".$jobNo."', ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;
		$all_po_id_arr = array_filter($all_po_id_arr);
		if(!empty($all_po_id_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_po_id_arr as $poId)
			{
				execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;

		$allocation_sql ="select a.job_no, a.booking_no, a.qnty, a.po_break_down_id,b.buyer_id
		from inv_material_allocation_mst a,wo_booking_mst b, tmp_booking_id c, tmp_job_no d
		where a.booking_no=b.booking_no $buyer_cond2 and b.company_id=$companyID and a.booking_no=c.booking_no and c.userid=$user_name and a.job_no=d.job_no and d.userid=$user_name AND (a.is_dyied_yarn != 1 or a.is_dyied_yarn is null) $booking_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
		//echo $allocation_sql; die;
		$allocation_sql_resutl = sql_select($allocation_sql);

		$allocation_data = array();
		foreach($allocation_sql_resutl as $row)
		{
			$allocation_data[$row[csf("job_no")]][$row[csf("booking_no")]]['alloc_qnty'] += $row[csf("qnty")];
			$allocation_data[$row[csf("buyer_id")]]['alloc_buyer_qnty'] += $row[csf("qnty")];
		}
		unset($allocation_sql_resutl);

        //Program Qnty
        $progQnty_sql = "SELECT a.booking_no,a.buyer_id,
			b.program_qnty as program_qnty, b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d, tmp_booking_id e
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $company_cond $buyer_cond and a.booking_no=e.booking_no and e.userid=$user_name
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, b.id,b.program_qnty order by b.id";
		//echo $progQnty_sql;die;
		$progQntyResult = sql_select($progQnty_sql);

		$progQntyArr =$progNoArr= array(); $programIdQntyChk = array();

		foreach($progQntyResult as $row)
		{
			if($programIdQntyChk[$row[csf("id")]] == "")
			{
				$programIdQntyChk[$row[csf("id")]] = $row[csf("id")];
				$progQntyArr[$row[csf("booking_no")]]['book_prog_qnty'] += $row[csf("program_qnty")];
				$progNoArr[$row[csf("booking_no")]][] = $row[csf("program_id")];
				$progQntyArr[$row[csf("buyer_id")]]['buyer_prog_qnty'] += $row[csf("program_qnty")];
			}
		}
		unset($progQntyResult);



		$reqs_sql = "SELECT a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, wo_booking_mst d,tmp_booking_id e, tmp_job_no f
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $buyer_cond3 and c.company_id=$companyID and c.booking_no=e.booking_no and e.userid=$user_name and d.job_no=f.job_no and f.userid=$user_name
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
				$reqs_array[$row[csf('booking_no')]]['qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_array[$row[csf('booking_no')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$reqs_array[$row[csf('booking_no')]]['program_no'] .= $row[csf('program_no')].",";
				$reqs_array[$row[csf('buyer_id')]]['buyer_req_qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_buyer_array[$row[csf('buyer_id')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$program_buyer_array[$row[csf('buyer_id')]]['program_no'] .= $row[csf('program_no')].",";
			}
		}
		unset($reqs_sql_result);

		$product_sql = "SELECT a.booking_id, c.quantity as knitting_qnty,c.returnable_qnty as return_qnty, c.id, d.booking_no , d.job_no,a.buyer_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c , wo_booking_dtls d,tmp_booking_id e
			where a.id=b.mst_id and c.dtls_id=b.id $company_cond $buyer_cond
			and c.po_breakdown_id = d.po_break_down_id and d.booking_no=e.booking_no and e.userid=$user_name
			and a.item_category=13 and a.entry_form=2 and c.entry_form=2
			and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active  = 1 and c.is_deleted = 0
			and a.status_active = 1 and a.is_deleted = 0";
			//echo $product_sql;die; //and d.job_no=f.job_no and f.userid=$user_name
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
				}
			}
		}
		unset($productionArr);


		$sql_issue = "SELECT (b.quantity) as issue_qnty, e.buyer_name, d.requisition_no
		from order_wise_pro_details b, inv_transaction d, wo_po_break_down c, wo_po_details_master e, tmp_po_id f
		where d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_id = e.id and b.po_breakdown_id=f.po_id and f.user_id=$user_name and b.trans_type=2 and b.entry_form=3 and b.status_active=1 and d.receive_basis = 3 and b.is_deleted=0 and d.status_active= 1 and b.issue_purpose not in (2,8) and d.company_id=$companyID $buyer_cond4";

		//echo $sql_issue;die;
		$sql_issue_data = sql_select( $sql_issue );

		$issue_qty_arr=array();
		foreach ($sql_issue_data as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]]['issue_qnty'] += $row[csf('issue_qnty')];
			$issue_qty_summary_arr[$row[csf('buyer_name')]]['issue_qnty'] += $row[csf('issue_qnty')];
		}
		unset($sql_issue_data);

		$sql_return = "SELECT (b.quantity) as returned_qnty, b.reject_qty as cons_reject_qnty, e.buyer_name, a.booking_no as requ_no from inv_receive_master a, order_wise_pro_details b, inv_transaction d,wo_po_break_down c, wo_po_details_master e, tmp_po_id f where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.po_breakdown_id=f.po_id and f.user_id=$user_name and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $company_cond $buyer_cond4";
		//echo $sql_return;die;
		$sql_return_data = sql_select($sql_return);

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('requ_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
			$return_qty_summary_arr[$row[csf('buyer_name')]]['returned_qnty'] += $row[csf('returned_qnty')];

			$reject_qty_arr[$row[csf('requ_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
			$reject_qty_summary_arr[$row[csf('buyer_name')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
		}
		unset($sql_return_data);

		$r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_name ");
		if($r_id111)
		{
			oci_commit($con);
		}

		$r_id222=execute_query("DELETE FROM TMP_JOB_NO WHERE USERID=$user_name ");
		if($r_id222)
		{
			oci_commit($con);
		}

		$r_id333=execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID=$user_name ");
		if($r_id333)
		{
			oci_commit($con);
		}

		ob_start();
		?>
		<fieldset style="width:1650px;">
			<table cellpadding="0" cellspacing="0" width="1530">
				<tr>
					<td align="center" width="100%" colspan="17" style="font-size:16px"><strong>Booking and Plan Wise Yarn Issue Monitoring Report</strong></td>
				</tr>
				<tr>
					<td  width="100%" colspan="13" style="font-size:12px"><strong>Summury : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="80">Buyer</th>
					<th width="100">Book Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130"><p>Yarn Allocation<br> Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></p></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="120"><p>Issue Balance<br><small style="font-size: 8px;">(Knit Program - Yarn Issue + issue return)</small></p></th>
					<th width="90">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:1500px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$z = 1;
						foreach($data_summury as $buyer_id =>$row)
						{
							if ($z % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$alloc_buyer_qnty = $allocation_data[$buyer_id]["alloc_buyer_qnty"];
							$alloc_buyer_qnty_balance = $row["qnty"] - $alloc_buyer_qnty;
							$buyer_prog_qnty = $progQntyArr[$buyer_id]['buyer_prog_qnty'];
							$buyer_prog_qnty_balance = $row["qnty"] - $buyer_prog_qnty;
							//$buyer_prog_qnty_balance = number_format($buyer_prog_qnty_balance,2,".","");
							$buyer_req_qnty = $reqs_array[$buyer_id]['buyer_req_qnty'];
							$buyer_req_qnty_balance = $alloc_buyer_qnty-$buyer_req_qnty;

							//$buyer_issue_qnty = $issue_qty_summary_arr[$buyer_id]['issue_qnty'];
							//$buyer_issue_ret_qnty = $return_qty_summary_arr[$buyer_id]['returned_qnty'];
							//$buyer_reject_qnty = $reject_qty_summary_arr[$buyer_id]['reject_qnty'];
							$buyer_issue_qnty = 0; $buyer_issue_ret_qnty=0; $buyer_reject_qnty = 0;
							$buyer_requ_id_arr = array_unique(explode(",",chop($reqs_buyer_array[$buyer_id]['requ_no'],",")));
							foreach($buyer_requ_id_arr as $requ_id)
							{
								$buyer_issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
								$buyer_issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
								$buyer_reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
							}

							$buyer_program_nos =array_unique(explode(",",chop($program_buyer_array[$buyer_id]['program_no'],",")));
							$buyer_production_qnty=$buyer_return_qnty=0;
							foreach($buyer_program_nos as $program_id)
							{
								$buyer_production_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
								$buyer_return_qnty += $productionQntyArr[$program_id]["return_qnty"];
							}

							$buyer_issue_qnty_balance = $buyer_prog_qnty-$buyer_issue_qnty + $buyer_issue_ret_qnty;
							//$buyer_production_qnty = $productionQntyArr_buy[$buyer_id]["buyer_prod_qnty"];
							//$buyer_return_qnty = $productionQntyArr_buy[$buyer_id]["buyer_return_qnty"];
							$buyer_balance_qnty = $buyer_issue_qnty - $buyer_issue_ret_qnty -$buyer_production_qnty- $buyer_return_qnty-$buyer_reject_qnty;
							if((($cbo_allocation_type == 1) && (number_format($alloc_buyer_qnty_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($alloc_buyer_qnty_balance,2) <= 0.00)) || ($cbo_allocation_type == 0))
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_s_<? echo $z; ?>', '<? echo $bgcolor; ?>')" id="tr_s_<? echo $z; ?>">
									<td width="40"><? echo $z;?></td>
									<td width="80" align="right"><? echo $buyer_arr[$buyer_id];?></td>
									<td width="100" align="right"><p><? echo number_format($row["qnty"],2);?></p></td>
									<td width="100" align="right"><p><? echo number_format($alloc_buyer_qnty,2);?></p></td>
									<td width="130" align="right"><p><? echo number_format($alloc_buyer_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_ret_qnty,2);?></p></td>
									<td width="120" align="right"><p><? echo number_format($buyer_issue_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_production_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_return_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_reject_qnty,2);?></p></td>
									<td width="" align="right"><p><? echo number_format($buyer_balance_qnty,2);?></p></td>
								</tr>
								<?
								$z++;
								$tot_booking += $row["qnty"];
								$tot_alloc_buyer_qnty += $alloc_buyer_qnty;
								$tot_buyer_prog_qnty += $buyer_prog_qnty;
								$tot_buyer_req_qnty += $buyer_req_qnty;
								$tot_buyer_issue_qnty += $buyer_issue_qnty;
								$tot_buyer_issue_ret_qnty += $buyer_issue_ret_qnty;
								$tot_buyer_production_qnty+=$buyer_production_qnty;
								$tot_buyer_return_qnty += $buyer_return_qnty;
								$tot_buyer_reject_qnty += $buyer_reject_qnty;
								$tot_buyer_balance_qnty += $buyer_balance_qnty;
							}
						}
						?>
						<tr style="font-weight:bold;background-color: #e0e0e0">
							<td colspan="2" align="left" width="120">Total:</td>
							<td align="right" width="100"><p><? echo number_format($tot_booking,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($tot_alloc_buyer_qnty,2);?></p></td>
							<td width="130"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_prog_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_req_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_ret_qnty,2);?></p></td>
							<td width="120"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_production_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_reject_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($tot_buyer_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br/>
			<table cellpadding="0" cellspacing="0" width="1800">
				<tr>
					<td width="100%" colspan="16" style="font-size:12px"><strong>Details : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2090" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Job No.</th>
					<th width="80">Buyer</th>
					<th width="100">Fabric Booking No</th>
					<th width="100">Booking Type</th>
					<th width="100">Internal Ref. No</th>
					<th width="130">PO No</th>
					<th width="100">Booking Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130">Yarn Allocation Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="90">Booking Yarn Balance<br><p><small style="font-size: 8px;">(Booking Qty - Yarn Issue) + (Issue Return + Reject Qty)</small></p></th>
					<th width="120">Issue Balance<br><p><small style="font-size: 8px;">(Knit Program - Yarn Issue) + Issue Return</small></p></th>
					<th width="70">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:2110px; overflow-y:scroll; max-height:330px;" id="scroll_body_dtls">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2090" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?


						foreach($data_array as $job_no => $job_data)
						{
							$job_row_span =0;
							foreach($job_data as $booking_no => $row)
							{
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								$allocation_balance = $row["qnty"] - $allocation_qnty;
								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$job_row_span++;
								}
							}
							$buyer_row_span_arr[$job_no]=$job_row_span;
						}

						$i=$m=1;
						foreach($data_array as $job_no => $job_data)
						{
							$y=1;
							$show= false;
							$sub_job_book_qnty=$sub_job_alloc_qnty=$sub_alloc_balance=$sub_prog_Qnty=$sub_prog_balance=$sub_requ_qnty=$sub_requ_balance=$sub_issue_qnty=$sub_issue_ret_qnty=$sub_issue_balance=$sub_prod_qnty=$sub_return_qnty=$sub_reject_qnty=$sub_balance_qnty=0;
							foreach($job_data as $booking_no => $row)
							{
								if ($m % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$job_row_span=$buyer_row_span_arr[$job_no];
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								if ($allocation_qnty == 0) $alloc_bgcolor = "background-color:red;";else $alloc_bgcolor= "";
								$allocation_balance = $row["qnty"] - $allocation_qnty;

								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$po_ids = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
									$prog_Qnty = $progQntyArr[$booking_no]['book_prog_qnty'];
									$program_ids = implode(", ",array_filter(array_unique($progNoArr[$booking_no])));
									if ($prog_Qnty == 0) $program_bgcolor = "background-color:red;";else $program_bgcolor= "";
									$prog_balance = $row["qnty"] - $prog_Qnty;
									//$prog_balance = number_format($prog_balance,2,".","");
									$requ_qnty = $reqs_array[$booking_no]['qnty'];
									if ($requ_qnty == 0) $requ_bgcolor = "background-color:red;";else $requ_bgcolor= "";
									$requ_balance = $allocation_qnty - $requ_qnty;
									$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_no]['requ_no'],",")));

									$issue_qnty = 0; $issue_ret_qnty = 0; $requ_nos = "";$reject_qnty=0;$balance_qnty=0;
									foreach($requ_id_arr as $requ_id)
									{
										$issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
										$issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
										if($requ_nos=="") $requ_nos .= $requ_id; else $requ_nos .= ",".$requ_id;

										$reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
									}

									$issue_balance = $prog_Qnty - $issue_qnty + $issue_ret_qnty;
									$program_nos =array_unique(explode(",",chop($reqs_array[$booking_no]['program_no'],",")));
									$prod_qnty=$return_qnty=0;
									foreach($program_nos as $program_id)
									{
										//$progss[$program_id]=$program_id;
										$prod_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
										$return_qnty += $productionQntyArr[$program_id]["return_qnty"];
									}
									$po_number = implode(",",array_unique(explode(",",chop($row['po'],','))));
									$balance_qnty = $issue_qnty - $issue_ret_qnty - $prod_qnty - $return_qnty - $reject_qnty;

									//for booking yarn balance
									$booking_yarn_balance = (number_format($row['qnty'],2,'.','')-(number_format($issue_qnty,2,'.',''))+(number_format($issue_ret_qnty,2,'.','')+number_format($reject_qnty,2,'.','')));

									//$po_ids = implode(",",array_unique(explode(",",chop($row['po_id'],','))));

									//===========================================
									if ($row['booking_type'] == 4)
									{
										$booking_type = 3;
									}
									else
									{
										$booking_type = $row['is_short'];
									}

									$row[csf('booking_no')] = $booking_no;
									$row[csf('company_id')] = $row['company_id'];
									$row[csf('po_break_down_id')] = $po_ids;
									$row[csf('item_category')] = 2;
									$row[csf('fabric_source')] = 1;
									$row[csf('job_no')] = $job_no;
									$row[csf('is_approved')] = $row['is_approved'];


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
											//$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$po_id_str = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
											$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $po_id_str . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
										}
										else if ($row_id == 370)
										{
											//$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$po_id_str = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
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
									//============================================
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<?
										if($y == 1)
										{
											?>
											<td width="40" rowspan="<? echo $job_row_span;?>"><? echo $i;?></td>
											<td width="100" rowspan="<? echo $job_row_span;?>"><p>&nbsp;<? echo $job_no;?></p></td>
											<td width="80" rowspan="<? echo $job_row_span;?>"><? echo $buyer_arr[$row['buyer_id']];?></td>
											<?
										}
										?>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $print_btn;?></p></td>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $booking_type_arr[$booking_no];?></p></td>
										<td width="100"><p style="word-break: break-all; text-align: center;">&nbsp;<?
											$internal_ref_no="";
											foreach(explode(",", $po_ids) as $po_id)
											{
												if($internal_ref_no =="") $internal_ref_no =  $internalRefArr[$po_id]["internalRefNumber"]; else $internal_ref_no .= ",".$internalRefArr[$po_id]["internalRefNumber"];
											}
											echo $internal_ref_no;
											?>
											</p>
										</td>
										<td width="130" style="max-width:130px;"><p style="word-break: break-all;">&nbsp;<? echo $po_number;//chop($row['po'],',');?></p></td>
										<td width="100" align="right"><p><? echo number_format($row['qnty'],2);?></p></td>
										<td width="100" align="right" style="<? echo $alloc_bgcolor;?>"><a href='##' onClick="openmypage_allocation('<? echo $job_no . "_" . $booking_no ?>', 'allocation_popup')"><p><? echo number_format($allocation_qnty,2);?></p></a></td>
										<td width="130" align="right"><p><? echo number_format($allocation_balance,2);?></p></td>
										<td width="90" align="right" style="<? echo $program_bgcolor;?>"><a href='##' onClick="openmypage_program('<? echo $program_ids; ?>', 'program_popup')"><p><? echo number_format($prog_Qnty,2);?></p></a></td>

										<td width="90" align="right" ><p><? echo number_format($prog_balance,2);?></p></td>

										<td width="90" align="right" style="<? echo $requ_bgcolor;?>" title="Requisition no=<? echo $requ_nos;?>"><a href='##' onClick="openmypage_requisition('<? echo $program_ids; ?>', 'requisition_popup')"><p><? echo number_format($requ_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($requ_balance,2);?></p></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_popup')"><p><? echo number_format($issue_qnty,2);?></p></a></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_return_popup')"><p><? echo number_format($issue_ret_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($booking_yarn_balance,2);?></p></td>
										<td width="120" align="right"><p><? echo number_format($issue_balance,2);?></p></td>
										<td width="70" align="right" title="<? echo chop($reqs_array[$booking_no]['program_no'],",");?>"><a href='##' onClick="openmypage_production('<? echo $job_no . "_" . $po_ids . "_" . $program_ids  ?>', 'production_popup')"><p><? echo number_format($prod_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($return_qnty,2);?></p></td>
										<td width="90" align="right"><p><? echo number_format($reject_qnty,2);?></p></td>
										<td width="" align="right"><p><? echo number_format($balance_qnty,2);?></p></td>
									</tr>
									<?
									$m++;$y++;//$job_no
									$sub_job_book_qnty +=  $row['qnty'];
									$sub_job_alloc_qnty += $allocation_qnty;
									$sub_alloc_balance += $allocation_balance;
									$sub_prog_Qnty += $prog_Qnty;
									$sub_prog_balance += $prog_balance;
									$sub_requ_qnty += $requ_qnty;
									$sub_requ_balance +=$requ_balance;
									$sub_issue_qnty += $issue_qnty;
									$sub_issue_ret_qnty += $issue_ret_qnty;
									$sub_issue_balance += $issue_balance;
									$sub_prod_qnty +=$prod_qnty;
									$sub_return_qnty += $return_qnty;
									$sub_reject_qnty += $reject_qnty;
									$sub_balance_qnty += $balance_qnty;
									$sub_booking_yarn_balance += number_format($booking_yarn_balance,2,'.','');
									$show = true;
								}
							}

							$i++;

							if($show == true)
							{
								?>
								<tr style="font-weight: bold;background-color: #e0e0e0">
									<td colspan="3" width="220">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td colspan="2" align="right" width="230">Job Sub Total </td>
									<td align="right" width="100"><p><? echo number_format($sub_job_book_qnty,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_job_alloc_qnty,2)?></p></td>
									<td align="right" width="130"><p><? echo number_format($sub_alloc_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_Qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_ret_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_booking_yarn_balance,2);?></p></td>
									<td align="right" width="120"><p><? echo number_format($sub_issue_balance,2);?></p></td>
									<td align="right" width="70"><p><? echo number_format($sub_prod_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_return_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_reject_qnty,2);?></p></td>
									<td align="right"  width=""><p><? echo number_format($sub_balance_qnty,2);?></p></td>
								</tr>
								<?
							}
							$grand_job_book_qnty +=  $sub_job_book_qnty;
							$grand_job_alloc_qnty += $sub_job_alloc_qnty;
							$grand_alloc_balance += $sub_alloc_balance;
							$grand_prog_Qnty += $sub_prog_Qnty;
							$grand_prog_balance += $sub_prog_balance;
							$grand_requ_qnty += $sub_requ_qnty;
							$grand_requ_balance +=$sub_requ_balance;
							$grand_issue_qnty += $sub_issue_qnty;
							$grand_issue_ret_qnty += $sub_issue_ret_qnty;
							$grand_issue_balance += $sub_issue_balance;
							$grand_prod_qnty +=$sub_prod_qnty;
							$grand_return_qnty += $sub_return_qnty;
							$grand_reject_qnty += $sub_reject_qnty;
							$grand_balance_qnty += $sub_balance_qnty;
							$grand_booking_yarn_balance += number_format($sub_booking_yarn_balance,2,'.','');
						}

						?>
						<tr style="font-weight: bold;background-color: #ccc;border-top: 2px solid">
							<td colspan="3" width="220">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td colspan="2" align="right" width="230">Job Grand Total </td>
							<td align="right" width="100"><p><? echo number_format($grand_job_book_qnty,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($grand_job_alloc_qnty,2)?></p></td>
							<td align="right" width="130"><p><? echo number_format($grand_alloc_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_Qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_ret_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_booking_yarn_balance,2);?></p></td>
							<td align="right" width="120"><p><? echo number_format($grand_issue_balance,2);?></p></td>
							<td align="right" width="70"><p><? echo number_format($grand_prod_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_reject_qnty,2);?></p></td>
							<td align="right" width=""><p><? echo number_format($grand_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
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

if ($action == "report_generate_sales")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_allocation_type = str_replace("'", "", $cbo_allocation_type);
	$report_type = str_replace("'", "", $report_type);
	$companyID = str_replace("'", "", $cbo_company_name);
	$buyerId = str_replace("'", "", $cbo_buyer_name);
	$internalRefNo = str_replace("'", "", $txt_internal_ref_no);


	if($companyID>0) $company_cond="and a.company_id=$companyID"; else $company_cond="";
	if($companyID>0) $company_cond_2="and a.company_name=$companyID"; else $company_cond_2="";

	if($buyerId>0)
	{
		$buyer_cond="and a.buyer_id = ".$buyerId."";
		$buyer_cond2="and b.buyer_id = ".$buyerId."";
		$buyer_cond3="and c.buyer_id = ".$buyerId."";
		$buyer_cond4="and e.buyer_name = ".$buyerId."";
	}
	else
	{
		$buyer_cond="";
		$buyer_cond2="";
		$buyer_cond3="";
	}

	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "")
	{

		$jobArr = explode("*",str_replace("'", "", trim($txt_job_no)));

		foreach($jobArr as $job)
		{
			$job_nos .= "'".$job."',";
		}
		$job_nos = chop($job_nos,",");
		$job_cond .= " and b.job_no in ( $job_nos )" ;

	}
	if ($internalRefNo!="") {$internalRefNo_cond="and c.grouping='$internalRefNo'";}else{$internalRefNo_cond="";}
	$booking_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = " and a.booking_no = $txt_booking_no";
	}

	$booking_date_cond = "";
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		$booking_date_cond = " and a.booking_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
	}

	//for program no
	$txt_program_no = str_replace("'", "", $txt_program_no );
	if($txt_program_no != '')
	{
        $sqlProgram = "select a.booking_no as BOOKING_NO from ppl_planning_entry_plan_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.is_sales !=1 and a.dtls_id in(".$txt_program_no.") ".$booking_cond." group by a.booking_no";
		$sqlProgramRslt = sql_select($sqlProgram);
		$bookingNoArr = array();
		foreach($sqlProgramRslt as $row)
		{
			$bookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}

		$booking_cond = " and a.booking_no in('".implode("','",$bookingNoArr)."')";
	}

	//for Print Button Permission
	$print_report = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(1) and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report);
	$print_report2 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(2) and is_deleted=0 and status_active=1");
	$format_ids2 = explode(",", $print_report2);
	$print_report3 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(3) and is_deleted=0 and status_active=1");
	$format_ids3 = explode(",", $print_report3);
	//end for Print Button Permission


		// Main SQL ====>
		$main_sql = "SELECT a.id, a.company_id, a.booking_no, a.booking_type, a.is_approved, a.is_short, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping, c.po_number, b.grey_fab_qnty as qnty, d.id as sales_id, d.job_no as sales_order_no
		from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, fabric_sales_order_mst d
		where a.booking_no=b.booking_no and b.po_break_down_id = c.id and a.id=d.booking_id and a.item_category=2 and a.fabric_source=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4)
		and b.grey_fab_qnty>0 $job_cond $booking_cond $booking_date_cond $company_cond $internalRefNo_cond $buyer_cond
		order by  a.job_no,a.buyer_id,a.booking_no";// a.id=b.booking_mst_id
		//echo $main_sql;die;
		$result = sql_select($main_sql);

		$data_summury = array();
		$data_array = array();
		foreach($result as $row)
		{
			$poArr[$row[csf("po_break_down_id")]] = $row[csf("po_number")];
			//$internalRefArr[$row[csf("po_break_down_id")]]["internalRefNumber"]=$row[csf("grouping")];
			$internalRefArr[$row[csf("sales_id")]]["internalRefNumber"]=$row[csf("grouping")];

			$data_summury[$row[csf("buyer_id")]]["qnty"] += $row[csf("qnty")];
			$data_summury[$row[csf("buyer_id")]]["job_no"] .= "'".$row[csf("job_no")]."',";
			$data_summury[$row[csf("buyer_id")]]["booking_no"] .= "'".$row[csf("booking_no")]."',";

			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po'] .= $row[csf("po_number")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po_id'] .= $row[csf("sales_id")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['qnty'] += $row[csf("qnty")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['company_id'] = $row[csf("company_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_approved'] = $row[csf("is_approved")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['booking_type'] = $row[csf("booking_type")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_short'] = $row[csf("is_short")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['sales_id'] = $row[csf("sales_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['sales_order_no'] = $row[csf("sales_order_no")];

			$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			$booking_nos_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$all_po_ids_for_buyer_issue .= $row[csf("po_break_down_id")].",";



			if($poIdChk[$row[csf('booking_no')]] == "")
			{
				$poIdChk[$row[csf('sales_id')]] = $row[csf('sales_id')];
				$all_po_id_arr[$row[csf("sales_id")]] = $row[csf("sales_id")];
			}

			if($bookingNoChk[$row[csf('booking_no')]] == "")
			{
				$bookingNoChk[$row[csf('booking_no')]] = $row[csf('booking_no')];
				$all_booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
			}

			if($salesNoChk[$row[csf('sales_order_no')]] == "")
			{
				$salesNoChk[$row[csf('sales_order_no')]] = $row[csf('sales_order_no')];
				$all_sales_no_arr[$row[csf("sales_order_no")]] = $row[csf("sales_order_no")];
			}


			if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==2)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Main";
			}
			else if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==1)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Short";
			}
			else if($row[csf("booking_type")] ==4 )
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Sample";
			}
		}
		unset($result);
		//var_dump($data_array);die;


		$job_numbers="'".implode("','",$job_no_arr)."'";
		$booking_nos="'".implode("','",$booking_nos_arr)."'";

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		$job_number_arr = array_unique(explode(",",chop($job_numbers,",")));
		$booking_nos = implode(",",array_unique(explode(",",chop($booking_nos,","))));
		$booking_nos_arr = array_unique(explode(",",chop($booking_nos,",")));

		$all_booking_no_arr = array_filter($all_booking_no_arr);

		if(!empty($all_booking_no_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_booking_no_arr as $bookNo)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_NO,USERID) VALUES('".$bookNo."', ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;



		$all_sales_no_arr = array_filter($all_sales_no_arr);

		if(!empty($all_sales_no_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_JOB_NO WHERE USERID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_sales_no_arr as $jobNo)
			{
				execute_query("INSERT INTO TMP_JOB_NO(JOB_NO,USERID) VALUES('".$jobNo."', ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;
		$all_po_id_arr = array_filter($all_po_id_arr);
		if(!empty($all_po_id_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_po_id_arr as $poId)
			{
				execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;

		$allocation_sql ="select a.job_no, a.booking_no, a.qnty, a.po_break_down_id,b.buyer_id
		from inv_material_allocation_mst a,wo_booking_mst b, tmp_booking_id c, tmp_job_no d
		where a.booking_no=b.booking_no $buyer_cond2 and b.company_id=$companyID and a.booking_no=c.booking_no and c.userid=$user_name and a.job_no=d.job_no and d.userid=$user_name AND (a.is_dyied_yarn != 1 or a.is_dyied_yarn is null) $booking_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
		//echo $allocation_sql; die;
		$allocation_sql_resutl = sql_select($allocation_sql);

		$allocation_data = array();
		foreach($allocation_sql_resutl as $row)
		{
			$allocation_data[$row[csf("job_no")]][$row[csf("booking_no")]]['alloc_qnty'] += $row[csf("qnty")];
			$allocation_data[$row[csf("buyer_id")]]['alloc_buyer_qnty'] += $row[csf("qnty")];
		}
		unset($allocation_sql_resutl);

        //Program Qnty
        $progQnty_sql = "SELECT a.booking_no,a.buyer_id,
			b.program_qnty as program_qnty, b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d, tmp_booking_id e
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $company_cond $buyer_cond and a.booking_no=e.booking_no and e.userid=$user_name
			and a.status_active=1 and a.is_deleted=0 and a.is_sales=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, b.id,b.program_qnty order by b.id";
		//echo $progQnty_sql;die;
		$progQntyResult = sql_select($progQnty_sql);

		$progQntyArr =$progNoArr= array(); $programIdQntyChk = array();

		foreach($progQntyResult as $row)
		{
			if($programIdQntyChk[$row[csf("id")]] == "")
			{
				$programIdQntyChk[$row[csf("id")]] = $row[csf("id")];
				$progQntyArr[$row[csf("booking_no")]]['book_prog_qnty'] += $row[csf("program_qnty")];
				$progNoArr[$row[csf("booking_no")]][] = $row[csf("program_id")];
				$progQntyArr[$row[csf("buyer_id")]]['buyer_prog_qnty'] += $row[csf("program_qnty")];
			}

		}
		unset($progQntyResult);



		$reqs_sql = "SELECT a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, fabric_sales_order_mst d,tmp_booking_id e, tmp_job_no f
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.sales_booking_no
			and b.is_sales = 1 $buyer_cond3 and c.company_id=$companyID and c.booking_no=e.booking_no and e.userid=$user_name and d.job_no=f.job_no and f.userid=$user_name
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";

		//echo $reqs_sql;die;
		$reqs_sql_result = sql_select($reqs_sql);

		$requIdCheckArr = array();
		$reqs_array = array();
		$all_req_no_arr = array();
		foreach ($reqs_sql_result as $row)
		{
			if($requIdCheckArr[$row[csf('id')]] == "")
			{
				$requIdCheckArr[$row[csf('id')]] = $row[csf('id')];
				$reqs_array[$row[csf('booking_no')]]['qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_array[$row[csf('booking_no')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$reqs_array[$row[csf('booking_no')]]['program_no'] .= $row[csf('program_no')].",";
				$reqs_array[$row[csf('buyer_id')]]['buyer_req_qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_buyer_array[$row[csf('buyer_id')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$program_buyer_array[$row[csf('buyer_id')]]['program_no'] .= $row[csf('program_no')].",";

				//array_push($allreqNoArr,$row[csf('requisition_no')]);
				$all_req_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
			}
		}
		unset($reqs_sql_result);
		//var_dump($all_req_no_arr);die;

		$all_req_no_arr = array_filter($all_req_no_arr);

		if(!empty($all_req_no_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_REQS_NO WHERE USERID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_req_no_arr as $reqNo)
			{
				execute_query("INSERT INTO TMP_REQS_NO(REQS_NO,USERID) VALUES('".$reqNo."', ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;

		$product_sql = "SELECT a.booking_id, c.quantity as knitting_qnty,c.returnable_qnty as return_qnty, c.id, d.sales_booking_no , d.job_no,a.buyer_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c, fabric_sales_order_mst d,tmp_booking_id e
			where a.id=b.mst_id and c.dtls_id=b.id $company_cond $buyer_cond
			and c.po_breakdown_id = d.id and d.sales_booking_no=e.booking_no and e.userid=$user_name
			and a.item_category=13 and a.entry_form=2 and c.entry_form=2
			and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active  = 1 and c.is_deleted = 0
			and a.status_active = 1 and a.is_deleted = 0";
			//echo $product_sql;die; //and d.job_no=f.job_no and f.userid=$user_name
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
				}
			}
		}
		unset($productionArr);


		$sql_issue = "SELECT (b.quantity) as issue_qnty, e.buyer_name, d.requisition_no, d.mst_id
		from order_wise_pro_details b, inv_transaction d, fabric_sales_order_mst c, wo_po_details_master e, tmp_po_id f
		where d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.po_job_no = e.job_no and b.po_breakdown_id=f.po_id and f.user_id=$user_name and b.trans_type=2 and b.entry_form=3 and b.status_active=1 and d.receive_basis in(3,8) and b.is_deleted=0 and d.status_active= 1 and b.issue_purpose not in (2,8) and d.company_id=$companyID $buyer_cond4";

		//echo $sql_issue;die;
		$sql_issue_data = sql_select( $sql_issue );

		$issue_qty_arr=array();
		$issue_info_arr=array();
		$issue_qty_summary_arr=array();
		foreach ($sql_issue_data as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]]['issue_qnty'] += $row[csf('issue_qnty')];
			$issue_info_arr[$row[csf('requisition_no')]]['mst_id'] .= $row[csf('mst_id')].',';
			$issue_qty_summary_arr[$row[csf('buyer_name')]]['issue_qnty'] += $row[csf('issue_qnty')];
		}
		unset($sql_issue_data);

		$sql_return = "SELECT (b.quantity) as returned_qnty, b.reject_qty as cons_reject_qnty, e.buyer_name, a.booking_no as requ_no, a.requisition_no from inv_receive_master a, order_wise_pro_details b, inv_transaction d,wo_po_break_down c, wo_po_details_master e, tmp_po_id f where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis in(3,8) and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.po_breakdown_id=f.po_id and f.user_id=$user_name and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $company_cond $buyer_cond4";
		//echo $sql_return;die;
		$sql_return_data = sql_select($sql_return);

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			if($row[csf('receive_basis')]==3)
			{
				$return_qty_arr[$row[csf('requ_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
				$reject_qty_arr[$row[csf('requ_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];

			}
			else
			{
				$return_qty_arr[$row[csf('requisition_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
				$reject_qty_arr[$row[csf('requisition_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
			}
			$return_qty_summary_arr[$row[csf('buyer_name')]]['returned_qnty'] += $row[csf('returned_qnty')];
			$reject_qty_summary_arr[$row[csf('buyer_name')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];

		}
		unset($sql_return_data);

		$sql_demand = "SELECT a.id, a.demand_system_no, b.requisition_no as requ_no, b.demand_qnty from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b, tmp_reqs_no c
		where a.id=b.mst_id $company_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.requisition_no=c.reqs_no and c.userid=$user_name";

		//echo $sql_demand;die;
		$sql_demand_data = sql_select($sql_demand);

		$demand_qty_arr = array();
		foreach ($sql_demand_data as $row)
		{
			$demand_qty_arr[$row[csf('requ_no')]]['demand_qnty'] += $row[csf('demand_qnty')];
		}

		unset($sql_demand_data);
		//var_dump($demand_qty_arr);die;


		$r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_name ");
		if($r_id111)
		{
			oci_commit($con);
		}

		$r_id222=execute_query("DELETE FROM TMP_JOB_NO WHERE USERID=$user_name ");
		if($r_id222)
		{
			oci_commit($con);
		}

		$r_id333=execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID=$user_name ");
		if($r_id333)
		{
			oci_commit($con);
		}

		$r_id444=execute_query("DELETE FROM TMP_REQS_NO WHERE USERID=$user_name ");
		if($r_id444)
		{
			oci_commit($con);
		}

		ob_start();
		?>
		<fieldset style="width:1650px;">
			<table cellpadding="0" cellspacing="0" width="1530">
				<tr>
					<td align="center" width="100%" colspan="17" style="font-size:16px"><strong>Booking and Plan Wise Yarn Issue Monitoring Report</strong></td>
				</tr>
				<tr>
					<td  width="100%" colspan="13" style="font-size:12px"><strong>Summury : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="80">Buyer</th>
					<th width="100">Book Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130"><p>Yarn Allocation<br> Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></p></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="120"><p>Issue Balance<br><small style="font-size: 8px;">(Knit Program - Yarn Issue + issue return)</small></p></th>
					<th width="90">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:1500px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$z = 1;
						foreach($data_summury as $buyer_id =>$row)
						{
							if ($z % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$alloc_buyer_qnty = $allocation_data[$buyer_id]["alloc_buyer_qnty"];
							$alloc_buyer_qnty_balance = $row["qnty"] - $alloc_buyer_qnty;
							$buyer_prog_qnty = $progQntyArr[$buyer_id]['buyer_prog_qnty'];
							$buyer_prog_qnty_balance = $row["qnty"] - $buyer_prog_qnty;
							//$buyer_prog_qnty_balance = number_format($buyer_prog_qnty_balance,2,".","");
							$buyer_req_qnty = $reqs_array[$buyer_id]['buyer_req_qnty'];
							$buyer_req_qnty_balance = $alloc_buyer_qnty-$buyer_req_qnty;

							//$buyer_issue_qnty = $issue_qty_summary_arr[$buyer_id]['issue_qnty'];
							//$buyer_issue_ret_qnty = $return_qty_summary_arr[$buyer_id]['returned_qnty'];
							//$buyer_reject_qnty = $reject_qty_summary_arr[$buyer_id]['reject_qnty'];
							$buyer_issue_qnty = 0; $buyer_issue_ret_qnty=0; $buyer_reject_qnty = 0;
							$buyer_requ_id_arr = array_unique(explode(",",chop($reqs_buyer_array[$buyer_id]['requ_no'],",")));
							foreach($buyer_requ_id_arr as $requ_id)
							{
								$buyer_issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
								$buyer_issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
								$buyer_reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
							}

							$buyer_program_nos =array_unique(explode(",",chop($program_buyer_array[$buyer_id]['program_no'],",")));
							$buyer_production_qnty=$buyer_return_qnty=0;
							foreach($buyer_program_nos as $program_id)
							{
								$buyer_production_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
								$buyer_return_qnty += $productionQntyArr[$program_id]["return_qnty"];
							}

							$buyer_issue_qnty_balance = $buyer_prog_qnty-$buyer_issue_qnty + $buyer_issue_ret_qnty;
							//$buyer_production_qnty = $productionQntyArr_buy[$buyer_id]["buyer_prod_qnty"];
							//$buyer_return_qnty = $productionQntyArr_buy[$buyer_id]["buyer_return_qnty"];
							$buyer_balance_qnty = $buyer_issue_qnty - $buyer_issue_ret_qnty -$buyer_production_qnty- $buyer_return_qnty-$buyer_reject_qnty;
							if((($cbo_allocation_type == 1) && (number_format($alloc_buyer_qnty_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($alloc_buyer_qnty_balance,2) <= 0.00)) || ($cbo_allocation_type == 0))
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_s_<? echo $z; ?>', '<? echo $bgcolor; ?>')" id="tr_s_<? echo $z; ?>">
									<td width="40"><? echo $z;?></td>
									<td width="80" align="right"><? echo $buyer_arr[$buyer_id];?></td>
									<td width="100" align="right"><p><? echo number_format($row["qnty"],2);?></p></td>
									<td width="100" align="right"><p><? echo number_format($alloc_buyer_qnty,2);?></p></td>
									<td width="130" align="right"><p><? echo number_format($alloc_buyer_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_ret_qnty,2);?></p></td>
									<td width="120" align="right"><p><? echo number_format($buyer_issue_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_production_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_return_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_reject_qnty,2);?></p></td>
									<td width="" align="right"><p><? echo number_format($buyer_balance_qnty,2);?></p></td>
								</tr>
								<?
								$z++;
								$tot_booking += $row["qnty"];
								$tot_alloc_buyer_qnty += $alloc_buyer_qnty;
								$tot_buyer_prog_qnty += $buyer_prog_qnty;
								$tot_buyer_req_qnty += $buyer_req_qnty;
								$tot_buyer_issue_qnty += $buyer_issue_qnty;
								$tot_buyer_issue_ret_qnty += $buyer_issue_ret_qnty;
								$tot_buyer_production_qnty+=$buyer_production_qnty;
								$tot_buyer_return_qnty += $buyer_return_qnty;
								$tot_buyer_reject_qnty += $buyer_reject_qnty;
								$tot_buyer_balance_qnty += $buyer_balance_qnty;
							}
						}
						?>
						<tr style="font-weight:bold;background-color: #e0e0e0">
							<td colspan="2" align="left" width="120">Total:</td>
							<td align="right" width="100"><p><? echo number_format($tot_booking,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($tot_alloc_buyer_qnty,2);?></p></td>
							<td width="130"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_prog_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_req_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_ret_qnty,2);?></p></td>
							<td width="120"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_production_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_reject_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($tot_buyer_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br/>
			<table cellpadding="0" cellspacing="0" width="1900">
				<tr>
					<td width="100%" colspan="16" style="font-size:12px"><strong>Details : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2390" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Job No.</th>
					<th width="80">Buyer</th>
					<th width="100">Fabric Booking No</th>
					<th width="100">Sales Order No</th>
					<th width="100">Booking Type</th>
					<th width="100">Internal Ref. No</th>
					<th width="130">PO No</th>
					<th width="100">Booking Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130">Yarn Allocation Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="100">Yarn Demand</th>
					<th width="100">Yarn Demand Balance<br><p><small style="font-size: 8px;">(Yarn Requisition - Yarn Demand)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="90">Booking Yarn Balance<br><p><small style="font-size: 8px;">(Booking Qty - Yarn Issue) + (Issue Return + Reject Qty)</small></p></th>
					<th width="120">Issue Balance<br><p><small style="font-size: 8px;">(Knit Program - Yarn Issue) + Issue Return</small></p></th>
					<th width="70">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:2410px; overflow-y:scroll; max-height:330px;" id="scroll_body_dtls">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2390" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?

						foreach($data_array as $job_no => $job_data)
						{
							$job_row_span =0;
							foreach($job_data as $booking_no => $row)
							{
								$allocation_qnty = $allocation_data[$row['sales_order_no']][$booking_no]['alloc_qnty'];
								$allocation_balance = $row["qnty"] - $allocation_qnty;
								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$job_row_span++;
								}
							}
							$buyer_row_span_arr[$job_no]=$job_row_span;
						}

						$i=$m=1;
						foreach($data_array as $job_no => $job_data)
						{
							$y=1;
							$show= false;
							$sub_job_book_qnty=$sub_job_alloc_qnty=$sub_alloc_balance=$sub_prog_Qnty=$sub_prog_balance=$sub_requ_qnty=$sub_requ_balance=$sub_issue_qnty=$sub_issue_ret_qnty=$sub_issue_balance=$sub_prod_qnty=$sub_return_qnty=$sub_reject_qnty=$sub_balance_qnty=$sub_demand_qty=$sub_demand_balance=0;
							foreach($job_data as $booking_no => $row)
							{
								if ($m % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$job_row_span=$buyer_row_span_arr[$job_no];
								$allocation_qnty = $allocation_data[$row['sales_order_no']][$booking_no]['alloc_qnty'];
								if ($allocation_qnty == 0) $alloc_bgcolor = "background-color:red;";else $alloc_bgcolor= "";
								$allocation_balance = $row["qnty"] - $allocation_qnty;

								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$po_ids = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
									$prog_Qnty = $progQntyArr[$booking_no]['book_prog_qnty'];
									$program_ids = implode(", ",array_filter(array_unique($progNoArr[$booking_no])));
									if ($prog_Qnty == 0) $program_bgcolor = "background-color:red;";else $program_bgcolor= "";
									$prog_balance = $row["qnty"] - $prog_Qnty;
									//$prog_balance = number_format($prog_balance,2,".","");
									$requ_qnty = $reqs_array[$booking_no]['qnty'];
									if ($requ_qnty == 0) $requ_bgcolor = "background-color:red;";else $requ_bgcolor= "";
									$requ_balance = $allocation_qnty - $requ_qnty;
									$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_no]['requ_no'],",")));

									$issue_qnty = 0; $issue_ret_qnty = 0; $requ_nos = "";$reject_qnty=0;$balance_qnty=0; $demand_qty = 0; $issue_id = "";
									foreach($requ_id_arr as $requ_id)
									{
										$issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
										$issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
										$issue_id = $issue_info_arr[$requ_id]['mst_id'];
										if($requ_nos=="") $requ_nos .= $requ_id; else $requ_nos .= ",".$requ_id;

										$reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
										$demand_qty +=$demand_qty_arr[$requ_id]['demand_qnty'];
									}
									//echo $demand_qty;

									$demand_balance = $requ_qnty - $demand_qty;
									$issue_balance = $prog_Qnty - $issue_qnty + $issue_ret_qnty;
									$program_nos =array_unique(explode(",",chop($reqs_array[$booking_no]['program_no'],",")));
									$prod_qnty=$return_qnty=0;
									foreach($program_nos as $program_id)
									{
										//$progss[$program_id]=$program_id;
										$prod_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
										$return_qnty += $productionQntyArr[$program_id]["return_qnty"];
									}
									$po_number = implode(",",array_unique(explode(",",chop($row['po'],','))));
									$balance_qnty = $issue_qnty - $issue_ret_qnty - $prod_qnty - $return_qnty - $reject_qnty;

									//for booking yarn balance
									$booking_yarn_balance = (number_format($row['qnty'],2,'.','')-(number_format($issue_qnty,2,'.',''))+(number_format($issue_ret_qnty,2,'.','')+number_format($reject_qnty,2,'.','')));

									//$po_ids = implode(",",array_unique(explode(",",chop($row['po_id'],','))));

									//===========================================
									if ($row['booking_type'] == 4)
									{
										$booking_type = 3;
									}
									else
									{
										$booking_type = $row['is_short'];
									}

									$row[csf('booking_no')] = $booking_no;
									$row[csf('company_id')] = $row['company_id'];
									$row[csf('po_break_down_id')] = $po_ids;
									$row[csf('item_category')] = 2;
									$row[csf('fabric_source')] = 1;
									$row[csf('job_no')] = $job_no;
									$row[csf('is_approved')] = $row['is_approved'];


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
											//$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$po_id_str = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
											$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $po_id_str . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
										}
										else if ($row_id == 370)
										{
											//$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$po_id_str = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
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
									//============================================
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<?
										if($y == 1)
										{
											?>
											<td width="40" rowspan="<? echo $job_row_span;?>"><? echo $i;?></td>
											<td width="100" rowspan="<? echo $job_row_span;?>"><p>&nbsp;<? echo $job_no;?></p></td>
											<td width="80" rowspan="<? echo $job_row_span;?>"><? echo $buyer_arr[$row['buyer_id']];?></td>
											<?
										}
										?>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $print_btn;?></p></td>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $row['sales_order_no'];?></p></td>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $booking_type_arr[$booking_no];?></p></td>
										<td width="100" title="<?=$po_ids;?>"><p style="word-break: break-all; text-align: center;">&nbsp;<?
											$internal_ref_no="";
											foreach(explode(",", $po_ids) as $po_id)
											{
												if($internal_ref_no =="") $internal_ref_no =  $internalRefArr[$po_id]["internalRefNumber"]; else $internal_ref_no .= ",".$internalRefArr[$po_id]["internalRefNumber"];
											}
											echo $internal_ref_no;
											?>
											</p>
										</td>
										<td width="130" style="max-width:130px;"><p style="word-break: break-all;">&nbsp;<? echo $po_number;//chop($row['po'],',');?></p></td>
										<td width="100" align="right"><p><? echo number_format($row['qnty'],2);?></p></td>
										<td width="100" align="right" style="<? echo $alloc_bgcolor;?>"><a href='##' onClick="openmypage_allocation('<? echo $row['sales_order_no'] . "_" . $booking_no ?>', 'allocation_sales_popup')"><p><? echo number_format($allocation_qnty,2);?></p></a></td>
										<td width="130" align="right"><p><? echo number_format($allocation_balance,2);?></p></td>
										<td width="90" align="right" style="<? echo $program_bgcolor;?>"><a href='##' onClick="openmypage_program('<? echo $program_ids; ?>', 'program_sales_popup')"><p><? echo number_format($prog_Qnty,2);?></p></a></td>

										<td width="90" align="right" ><p><? echo number_format($prog_balance,2);?></p></td>

										<td width="90" align="right" style="<? echo $requ_bgcolor;?>" title="Requisition no=<? echo $requ_nos;?>"><a href='##' onClick="openmypage_requisition('<? echo $program_ids; ?>', 'requisition_popup')"><p><? echo number_format($requ_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($requ_balance,2);?></p></td>
										<td width="90" align="right" ><a href='##' onClick="openmypage_requisition('<? echo $program_ids; ?>', 'demand_popup')"><p><? echo number_format($demand_qty,2);?></p></a></td>
										<td width="100" align="right"><p><? echo number_format($demand_balance,2);?></p></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_sales_popup')"><p><? echo number_format($issue_qnty,2);?></p></a></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo chop($issue_id,',')."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_return_sales_popup')"><p><? echo number_format($issue_ret_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($booking_yarn_balance,2);?></p></td>
										<td width="120" align="right"><p><? echo number_format($issue_balance,2);?></p></td>
										<td width="70" align="right" title="<? echo chop($reqs_array[$booking_no]['program_no'],",");?>"><p><? echo number_format($prod_qnty,2);?></p></td>
										<td width="90" align="right"><p><? echo number_format($return_qnty,2);?></p></td>
										<td width="90" align="right"><p><? echo number_format($reject_qnty,2);?></p></td>
										<td width="" align="right"><p><? echo number_format($balance_qnty,2);?></p></td>
									</tr>
									<?
									$m++;$y++;//$job_no
									$sub_job_book_qnty +=  $row['qnty'];
									$sub_job_alloc_qnty += $allocation_qnty;
									$sub_alloc_balance += $allocation_balance;
									$sub_prog_Qnty += $prog_Qnty;
									$sub_prog_balance += $prog_balance;
									$sub_requ_qnty += $requ_qnty;
									$sub_requ_balance +=$requ_balance;
									$sub_issue_qnty += $issue_qnty;
									$sub_issue_ret_qnty += $issue_ret_qnty;
									$sub_issue_balance += $issue_balance;
									$sub_prod_qnty +=$prod_qnty;
									$sub_return_qnty += $return_qnty;
									$sub_reject_qnty += $reject_qnty;
									$sub_balance_qnty += $balance_qnty;
									$sub_demand_qty += $demand_qty;
									$sub_demand_balance += $demand_balance;
									$sub_booking_yarn_balance += number_format($booking_yarn_balance,2,'.','');
									$show = true;
								}
							}

							$i++;

							if($show == true)
							{
								?>
								<tr style="font-weight: bold;background-color: #e0e0e0">
									<td colspan="3" width="220">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td colspan="2" align="right" width="230">Job Sub Total </td>
									<td align="right" width="100"><p><? echo number_format($sub_job_book_qnty,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_job_alloc_qnty,2)?></p></td>
									<td align="right" width="130"><p><? echo number_format($sub_alloc_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_Qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_balance,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_demand_qty,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_demand_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_ret_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_booking_yarn_balance,2);?></p></td>
									<td align="right" width="120"><p><? echo number_format($sub_issue_balance,2);?></p></td>
									<td align="right" width="70"><p><? echo number_format($sub_prod_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_return_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_reject_qnty,2);?></p></td>
									<td align="right"  width=""><p><? echo number_format($sub_balance_qnty,2);?></p></td>
								</tr>
								<?
							}
							$grand_job_book_qnty +=  $sub_job_book_qnty;
							$grand_job_alloc_qnty += $sub_job_alloc_qnty;
							$grand_alloc_balance += $sub_alloc_balance;
							$grand_prog_Qnty += $sub_prog_Qnty;
							$grand_prog_balance += $sub_prog_balance;
							$grand_requ_qnty += $sub_requ_qnty;
							$grand_requ_balance +=$sub_requ_balance;
							$grand_issue_qnty += $sub_issue_qnty;
							$grand_issue_ret_qnty += $sub_issue_ret_qnty;
							$grand_issue_balance += $sub_issue_balance;
							$grand_prod_qnty +=$sub_prod_qnty;
							$grand_return_qnty += $sub_return_qnty;
							$grand_reject_qnty += $sub_reject_qnty;
							$grand_balance_qnty += $sub_balance_qnty;
							$grand_booking_yarn_balance += number_format($sub_booking_yarn_balance,2,'.','');
						}

						?>
						<tr style="font-weight: bold;background-color: #ccc;border-top: 2px solid">
							<td colspan="3" width="220">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td colspan="2" align="right" width="230">Job Grand Total </td>
							<td align="right" width="100"><p><? echo number_format($grand_job_book_qnty,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($grand_job_alloc_qnty,2)?></p></td>
							<td align="right" width="130"><p><? echo number_format($grand_alloc_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_Qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_balance,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($sub_demand_qty,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($sub_demand_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_ret_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_booking_yarn_balance,2);?></p></td>
							<td align="right" width="120"><p><? echo number_format($grand_issue_balance,2);?></p></td>
							<td align="right" width="70"><p><? echo number_format($grand_prod_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_reject_qnty,2);?></p></td>
							<td align="right" width=""><p><? echo number_format($grand_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
		</fieldset>
		<?

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

if ($action == "report_generate20122022")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_allocation_type = str_replace("'", "", $cbo_allocation_type);
	$report_type = str_replace("'", "", $report_type);
	$companyID = str_replace("'", "", $cbo_company_name);
	$buyerId = str_replace("'", "", $cbo_buyer_name);
	$internalRefNo = str_replace("'", "", $txt_internal_ref_no);


	if($companyID>0) $company_cond="and a.company_id=$companyID"; else $company_cond="";
	if($companyID>0) $company_cond_2="and a.company_name=$companyID"; else $company_cond_2="";

	if($buyerId>0)
	{
		$buyer_cond="and a.buyer_id = ".$buyerId."";
		$buyer_cond2="and b.buyer_id = ".$buyerId."";
		$buyer_cond3="and c.buyer_id = ".$buyerId."";
		$buyer_cond4="and e.buyer_name = ".$buyerId."";
	}
	else
	{
		$buyer_cond="";
		$buyer_cond2="";
		$buyer_cond3="";
	}

	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "")
	{

		$jobArr = explode("*",str_replace("'", "", trim($txt_job_no)));

		foreach($jobArr as $job)
		{
			$job_nos .= "'".$job."',";
		}
		$job_nos = chop($job_nos,",");
		$job_cond .= " and b.job_no in ( $job_nos )" ;

	}
	if ($internalRefNo!="") {$internalRefNo_cond="and c.grouping='$internalRefNo'";}else{$internalRefNo_cond="";}
	$booking_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = " and a.booking_no = $txt_booking_no";
	}

	$booking_date_cond = "";
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		$booking_date_cond = " and a.booking_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
	}

	//for program no
	$txt_program_no = str_replace("'", "", $txt_program_no );
	if($txt_program_no != '')
	{
        $sqlProgram = "select a.booking_no as BOOKING_NO from ppl_planning_entry_plan_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.is_sales !=1 and a.dtls_id in(".$txt_program_no.") ".$booking_cond." group by a.booking_no";
		$sqlProgramRslt = sql_select($sqlProgram);
		$bookingNoArr = array();
		foreach($sqlProgramRslt as $row)
		{
			$bookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}

		$booking_cond = " and a.booking_no in('".implode("','",$bookingNoArr)."')";
	}

	//for Print Button Permission
	$print_report = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(1) and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report);
	$print_report2 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(2) and is_deleted=0 and status_active=1");
	$format_ids2 = explode(",", $print_report2);
	$print_report3 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(3) and is_deleted=0 and status_active=1");
	$format_ids3 = explode(",", $print_report3);
	//end for Print Button Permission

	if ($report_type == 1)
	{
		// Main SQL ====>
		$main_sql = "select a.id, a.company_id, a.booking_no, a.booking_type, a.is_approved, a.is_short, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping, c.po_number, b.grey_fab_qnty as qnty
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and a.item_category=2 and a.fabric_source=1
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4)
			and b.grey_fab_qnty>0 $job_cond $booking_cond $booking_date_cond $company_cond $internalRefNo_cond $buyer_cond
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

			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po'] .= $row[csf("po_number")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po_id'] .= $row[csf("po_break_down_id")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['qnty'] += $row[csf("qnty")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['company_id'] = $row[csf("company_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_approved'] = $row[csf("is_approved")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['booking_type'] = $row[csf("booking_type")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_short'] = $row[csf("is_short")];

			$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			$booking_nos_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$all_po_ids_for_buyer_issue .= $row[csf("po_break_down_id")].",";

			$all_po_id_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];

			if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==2)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Main";
			}
			else if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==1)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Short";
			}
			else if($row[csf("booking_type")] ==4 )
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Sample";
			}
		}
		unset($result);

		$job_numbers="'".implode("','",$job_no_arr)."'";
		$booking_nos="'".implode("','",$booking_nos_arr)."'";

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		$job_number_arr = array_unique(explode(",",chop($job_numbers,",")));
		$booking_nos = implode(",",array_unique(explode(",",chop($booking_nos,","))));
		$booking_nos_arr = array_unique(explode(",",chop($booking_nos,",")));

		$alloc_booking_cond = "";$requisition_booking_cond="";$issue_booking_cond="";$production_booking_cond="";

		if($booking_nos != "")
		{
			if ($db_type == 0)
			{
				$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
				$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
				$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
				$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
			}
			else
			{
				if (count($booking_nos_arr) > 1000)
				{
					$alloc_booking_cond = " and (";
					$booking_nos_arr = array_chunk($booking_nos_arr, 1000);
					$z = 0;
					foreach ($booking_nos_arr as $booking_no)
					{
						$booking_no = implode(",", $booking_no);
						if ($z == 0)
						{
							$alloc_booking_cond .= " a.booking_no in(" . $booking_no . ")";
							$requisition_booking_cond .= " c.booking_no in(" . $booking_no . ")";
							$issue_booking_cond .= " d.booking_no in(" . $booking_no . ")";
							$production_booking_cond .= " d.booking_no in(" . $booking_no . ")";
						}
						else
						{
							$alloc_booking_cond .= " or a.booking_no in(" . $booking_no . ")";
							$requisition_booking_cond .= " or c.booking_no in(" . $booking_no . ")";
							$issue_booking_cond .= " or d.booking_no in(" . $booking_no . ")";
							$production_booking_cond .= " or d.booking_no in(" . $booking_no . ")";
						}
						$z++;
					}
					$alloc_booking_cond .= ")";
					$requisition_booking_cond .= ")";
					$issue_booking_cond .= ")";
					$production_booking_cond .= ")";
				}
				else
				{
					$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
					$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
					$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
					$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
				}
			}
		}



		$alloc_job_cond = "";$program_job_cond="";$requistion_job_cond="";
		if($job_numbers != "")
		{
			if ($db_type == 0)
			{
				$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
				$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
				$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
				$issue_job_cond .= " and d.job_no in ( $job_numbers )" ;
				$production_job_cond .= " and d.job_no in ( $job_numbers )" ;
			}
			else
			{
				if (count($job_number_arr) > 1000)
				{
					$alloc_job_cond = " and (";
					$job_number_arr = array_chunk($job_number_arr, 1000);
					$z = 0;
					foreach ($job_number_arr as $job_number )
					{
						$job_number = implode(",", $job_number);
						if ($z == 0)
						{
							$alloc_job_cond .= " a.job_no in(" . $job_number . ")";
							$program_job_cond .= " c.job_no in(" . $job_number . ")";
							$requistion_job_cond .= " d.job_no in(" . $job_number . ")";
							$issue_job_cond .= " d.job_no in(" . $job_number . ")";
							$production_job_cond .= " d.job_no in(" . $job_number . ")";
						}
						else
						{
							$alloc_job_cond .= " or a.job_no in(" . $job_number . ")";
							$program_job_cond .= " or c.job_no in(" . $job_number . ")";
							$requistion_job_cond .= " or d.job_no in(" . $job_number . ")";
							$issue_job_cond .= " or d.job_no in(" . $job_number . ")";
							$production_job_cond .= " or d.job_no in(" . $job_number . ")";
						}
						$z++;
					}
					$alloc_job_cond .= ")";
					$program_job_cond .= ")";
					$requistion_job_cond .= ")";
					$issue_job_cond .= ")";
					$production_job_cond .= ")";
				}
				else
				{
					$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
					$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
					$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
					$issue_job_cond = " and d.job_no in(" . $job_numbers . ")";
					$production_job_cond = " and d.job_no in(" . $job_numbers . ")";
				}
			}
		}

        //allocation Qnty
        $allocation_sql ="select a.job_no, a.booking_no, a.qnty, a.po_break_down_id,b.buyer_id
			from inv_material_allocation_mst a,wo_booking_mst b
			where a.booking_no=b.booking_no $alloc_job_cond $alloc_booking_cond $buyer_cond2 and b.company_id=$companyID AND (a.is_dyied_yarn != 1 or a.is_dyied_yarn is null) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";

		$allocation_sql_resutl = sql_select($allocation_sql);

		$allocation_data = array();
		foreach($allocation_sql_resutl as $row)
		{
			$allocation_data[$row[csf("job_no")]][$row[csf("booking_no")]]['alloc_qnty'] += $row[csf("qnty")];
			$allocation_data[$row[csf("buyer_id")]]['alloc_buyer_qnty'] += $row[csf("qnty")];
		}
		unset($allocation_sql_resutl);

        //Program Qnty
        $progQnty_sql = "select a.booking_no,a.buyer_id,
			b.program_qnty as program_qnty, b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond $buyer_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, b.id,b.program_qnty order by b.id";
        /* $progQnty_sql = "select a.booking_no,a.buyer_id,
			a.program_qnty as program_qnty, a.id,b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, a.id,b.id,a.program_qnty order by b.id";*/
		$progQntyResult = sql_select($progQnty_sql);

		$progQntyArr =$progNoArr= array(); $programIdQntyChk = array();

		foreach($progQntyResult as $row)
		{
			if($programIdQntyChk[$row[csf("id")]] == "")
			{
				$programIdQntyChk[$row[csf("id")]] = $row[csf("id")];
				$progQntyArr[$row[csf("booking_no")]]['book_prog_qnty'] += $row[csf("program_qnty")];
				$progNoArr[$row[csf("booking_no")]][] = $row[csf("program_id")];
				$progQntyArr[$row[csf("buyer_id")]]['buyer_prog_qnty'] += $row[csf("program_qnty")];
			}
		}
		unset($progQntyResult);

        //Requisition Qnty

		/*$reqs_sql = "select a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b,ppl_planning_info_entry_mst c, wo_booking_dtls d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond and c.company_id=$companyID
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";*/

		$reqs_sql = "select a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, wo_booking_mst d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond $buyer_cond3 and c.company_id=$companyID
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";

		$reqs_sql_result = sql_select($reqs_sql);

		$requIdCheckArr = array();
		$reqs_array = array();
		foreach ($reqs_sql_result as $row)
		{
			if($requIdCheckArr[$row[csf('id')]] == "")
			{
				$requIdCheckArr[$row[csf('id')]] = $row[csf('id')];
				$reqs_array[$row[csf('booking_no')]]['qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_array[$row[csf('booking_no')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$reqs_array[$row[csf('booking_no')]]['program_no'] .= $row[csf('program_no')].",";
				$reqs_array[$row[csf('buyer_id')]]['buyer_req_qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_buyer_array[$row[csf('buyer_id')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$program_buyer_array[$row[csf('buyer_id')]]['program_no'] .= $row[csf('program_no')].",";
			}
		}
		unset($reqs_sql_result);

		$product_sql = "select a.booking_id, c.quantity as knitting_qnty,c.returnable_qnty as return_qnty, c.id, d.booking_no , d.job_no,a.buyer_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c , wo_booking_dtls d
			where a.id=b.mst_id and c.dtls_id=b.id $production_job_cond $production_booking_cond $company_cond $buyer_cond
			and c.po_breakdown_id = d.po_break_down_id
			and a.item_category=13 and a.entry_form=2 and c.entry_form=2
			and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active  = 1 and c.is_deleted = 0
			and a.status_active = 1 and a.is_deleted = 0";
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
				}
			}
		}
		unset($productionArr);
		// echo "<pre>";
		// print_r($nprog);
		//print_r($productionQntyArr_buy);
		$all_po_ids_for_buyer_issue;

		$all_po_ids=chop($all_po_ids_for_buyer_issue,',');
		$all_po_ids_arr = array_filter(array_unique(explode(",",$all_po_ids)));

		if($db_type==2 && count($all_po_ids_arr)>1000)
		{
			$po_ids_cond=" and (";
			$all_po_ids_chunk_arr=array_chunk($all_po_ids_arr,999);
			foreach($all_po_ids_chunk_arr as $ids)
			{
				$po_ids_cond.=" b.po_breakdown_id in($ids) or";
			}
			$po_ids_cond=chop($po_ids_cond,'or ');
			$po_ids_cond.=")";
		}
		else
		{
			$all_poids=implode(",",$all_po_ids_arr);
			$po_ids_cond=" and b.po_breakdown_id in($all_poids)";
		}

		$all_po_id_arr = array_filter($all_po_id_arr);
		if(!empty($all_po_id_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID = ".$user_name."");
			oci_commit($con);

			$con = connect();
			foreach($all_po_id_arr as $poId)
			{
				execute_query("INSERT INTO TMP_PO_ID(PO_ID,USER_ID) VALUES(".$poId.", ".$user_name.")");
				oci_commit($con);
			}
		}
		//die;

		$sql_issue = "SELECT (b.quantity) as issue_qnty, e.buyer_name, d.requisition_no
		from order_wise_pro_details b, inv_transaction d, wo_po_break_down c, wo_po_details_master e, tmp_po_id f
		where d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.po_breakdown_id=f.po_id and f.user_id=$user_name and b.trans_type=2 and b.entry_form=3 and b.status_active=1 and d.receive_basis = 3 and b.is_deleted=0 and d.status_active= 1 and b.issue_purpose not in (2,8) and d.company_id=$companyID $buyer_cond4";

		//echo $sql_issue;die;
		$sql_issue_data = sql_select( $sql_issue );

		$issue_qty_arr=array();
		foreach ($sql_issue_data as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]]['issue_qnty'] += $row[csf('issue_qnty')];
			$issue_qty_summary_arr[$row[csf('buyer_name')]]['issue_qnty'] += $row[csf('issue_qnty')];
		}
		unset($sql_issue_data);

		$sql_return = "SELECT (b.quantity) as returned_qnty, b.reject_qty as cons_reject_qnty, e.buyer_name, a.booking_no as requ_no from inv_receive_master a, order_wise_pro_details b, inv_transaction d,wo_po_break_down c, wo_po_details_master e, tmp_po_id f where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.po_breakdown_id=f.po_id and f.user_id=$user_name and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $company_cond $buyer_cond4";
		$sql_return_data = sql_select($sql_return);

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('requ_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
			$return_qty_summary_arr[$row[csf('buyer_name')]]['returned_qnty'] += $row[csf('returned_qnty')];

			$reject_qty_arr[$row[csf('requ_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
			$reject_qty_summary_arr[$row[csf('buyer_name')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
		}
		unset($sql_return_data);

		$r_id111=execute_query("DELETE FROM TMP_PO_ID WHERE USER_ID=$user_name ");
		if($r_id111)
		{
			oci_commit($con);
		}


		ob_start();
		?>
		<fieldset style="width:1650px;">
			<table cellpadding="0" cellspacing="0" width="1530">
				<tr>
					<td align="center" width="100%" colspan="17" style="font-size:16px"><strong>Booking and Plan Wise Yarn Issue Monitoring Report</strong></td>
				</tr>
				<tr>
					<td  width="100%" colspan="13" style="font-size:12px"><strong>Summury : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="80">Buyer</th>
					<th width="100">Book Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130"><p>Yarn Allocation<br> Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></p></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="120"><p>Issue Balance<br><small style="font-size: 8px;">(Knit Program - Yarn Issue + issue return)</small></p></th>
					<th width="90">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:1500px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$z = 1;
						foreach($data_summury as $buyer_id =>$row)
						{
							if ($z % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$alloc_buyer_qnty = $allocation_data[$buyer_id]["alloc_buyer_qnty"];
							$alloc_buyer_qnty_balance = $row["qnty"] - $alloc_buyer_qnty;
							$buyer_prog_qnty = $progQntyArr[$buyer_id]['buyer_prog_qnty'];
							$buyer_prog_qnty_balance = $row["qnty"] - $buyer_prog_qnty;
							//$buyer_prog_qnty_balance = number_format($buyer_prog_qnty_balance,2,".","");
							$buyer_req_qnty = $reqs_array[$buyer_id]['buyer_req_qnty'];
							$buyer_req_qnty_balance = $alloc_buyer_qnty-$buyer_req_qnty;

							//$buyer_issue_qnty = $issue_qty_summary_arr[$buyer_id]['issue_qnty'];
							//$buyer_issue_ret_qnty = $return_qty_summary_arr[$buyer_id]['returned_qnty'];
							//$buyer_reject_qnty = $reject_qty_summary_arr[$buyer_id]['reject_qnty'];
							$buyer_issue_qnty = 0; $buyer_issue_ret_qnty=0; $buyer_reject_qnty = 0;
							$buyer_requ_id_arr = array_unique(explode(",",chop($reqs_buyer_array[$buyer_id]['requ_no'],",")));
							foreach($buyer_requ_id_arr as $requ_id)
							{
								$buyer_issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
								$buyer_issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
								$buyer_reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
							}

							$buyer_program_nos =array_unique(explode(",",chop($program_buyer_array[$buyer_id]['program_no'],",")));
							$buyer_production_qnty=$buyer_return_qnty=0;
							foreach($buyer_program_nos as $program_id)
							{
								$buyer_production_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
								$buyer_return_qnty += $productionQntyArr[$program_id]["return_qnty"];
							}

							$buyer_issue_qnty_balance = $buyer_prog_qnty-$buyer_issue_qnty + $buyer_issue_ret_qnty;
							//$buyer_production_qnty = $productionQntyArr_buy[$buyer_id]["buyer_prod_qnty"];
							//$buyer_return_qnty = $productionQntyArr_buy[$buyer_id]["buyer_return_qnty"];
							$buyer_balance_qnty = $buyer_issue_qnty - $buyer_issue_ret_qnty -$buyer_production_qnty- $buyer_return_qnty-$buyer_reject_qnty;
							if((($cbo_allocation_type == 1) && (number_format($alloc_buyer_qnty_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($alloc_buyer_qnty_balance,2) <= 0.00)) || ($cbo_allocation_type == 0))
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_s_<? echo $z; ?>', '<? echo $bgcolor; ?>')" id="tr_s_<? echo $z; ?>">
									<td width="40"><? echo $z;?></td>
									<td width="80" align="right"><? echo $buyer_arr[$buyer_id];?></td>
									<td width="100" align="right"><p><? echo number_format($row["qnty"],2);?></p></td>
									<td width="100" align="right"><p><? echo number_format($alloc_buyer_qnty,2);?></p></td>
									<td width="130" align="right"><p><? echo number_format($alloc_buyer_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_ret_qnty,2);?></p></td>
									<td width="120" align="right"><p><? echo number_format($buyer_issue_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_production_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_return_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_reject_qnty,2);?></p></td>
									<td width="" align="right"><p><? echo number_format($buyer_balance_qnty,2);?></p></td>
								</tr>
								<?
								$z++;
								$tot_booking += $row["qnty"];
								$tot_alloc_buyer_qnty += $alloc_buyer_qnty;
								$tot_buyer_prog_qnty += $buyer_prog_qnty;
								$tot_buyer_req_qnty += $buyer_req_qnty;
								$tot_buyer_issue_qnty += $buyer_issue_qnty;
								$tot_buyer_issue_ret_qnty += $buyer_issue_ret_qnty;
								$tot_buyer_production_qnty+=$buyer_production_qnty;
								$tot_buyer_return_qnty += $buyer_return_qnty;
								$tot_buyer_reject_qnty += $buyer_reject_qnty;
								$tot_buyer_balance_qnty += $buyer_balance_qnty;
							}
						}
						?>
						<tr style="font-weight:bold;background-color: #e0e0e0">
							<td colspan="2" align="left" width="120">Total:</td>
							<td align="right" width="100"><p><? echo number_format($tot_booking,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($tot_alloc_buyer_qnty,2);?></p></td>
							<td width="130"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_prog_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_req_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_ret_qnty,2);?></p></td>
							<td width="120"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_production_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_reject_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($tot_buyer_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br/>
			<table cellpadding="0" cellspacing="0" width="1800">
				<tr>
					<td width="100%" colspan="16" style="font-size:12px"><strong>Details : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2090" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Job No.</th>
					<th width="80">Buyer</th>
					<th width="100">Fabric Booking No</th>
					<th width="100">Booking Type</th>
					<th width="100">Internal Ref. No</th>
					<th width="130">PO No</th>
					<th width="100">Booking Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130">Yarn Allocation Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="90">Booking Yarn Balance<br><p><small style="font-size: 8px;">(Booking Qty - Yarn Issue) + (Issue Return + Reject Qty)</small></p></th>
					<th width="120">Issue Balance<br><p><small style="font-size: 8px;">(Knit Program - Yarn Issue) + Issue Return</small></p></th>
					<th width="70">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:2110px; overflow-y:scroll; max-height:330px;" id="scroll_body_dtls">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2090" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						foreach($data_array as $job_no => $job_data)
						{
							$job_row_span =0;
							foreach($job_data as $booking_no => $row)
							{
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								$allocation_balance = $row["qnty"] - $allocation_qnty;
								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$job_row_span++;
								}
							}
							$buyer_row_span_arr[$job_no]=$job_row_span;
						}

						$i=$m=1;
						foreach($data_array as $job_no => $job_data)
						{
							$y=1;
							$show= false;
							$sub_job_book_qnty=$sub_job_alloc_qnty=$sub_alloc_balance=$sub_prog_Qnty=$sub_prog_balance=$sub_requ_qnty=$sub_requ_balance=$sub_issue_qnty=$sub_issue_ret_qnty=$sub_issue_balance=$sub_prod_qnty=$sub_return_qnty=$sub_reject_qnty=$sub_balance_qnty=0;
							foreach($job_data as $booking_no => $row)
							{
								if ($m % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$job_row_span=$buyer_row_span_arr[$job_no];
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								if ($allocation_qnty == 0) $alloc_bgcolor = "background-color:red;";else $alloc_bgcolor= "";
								$allocation_balance = $row["qnty"] - $allocation_qnty;

								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$po_ids = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
									$prog_Qnty = $progQntyArr[$booking_no]['book_prog_qnty'];
									$program_ids = implode(", ",array_filter(array_unique($progNoArr[$booking_no])));
									if ($prog_Qnty == 0) $program_bgcolor = "background-color:red;";else $program_bgcolor= "";
									$prog_balance = $row["qnty"] - $prog_Qnty;
									//$prog_balance = number_format($prog_balance,2,".","");
									$requ_qnty = $reqs_array[$booking_no]['qnty'];
									if ($requ_qnty == 0) $requ_bgcolor = "background-color:red;";else $requ_bgcolor= "";
									$requ_balance = $allocation_qnty - $requ_qnty;
									$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_no]['requ_no'],",")));

									$issue_qnty = 0; $issue_ret_qnty = 0; $requ_nos = "";$reject_qnty=0;$balance_qnty=0;
									foreach($requ_id_arr as $requ_id)
									{
										$issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
										$issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
										if($requ_nos=="") $requ_nos .= $requ_id; else $requ_nos .= ",".$requ_id;

										$reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
									}

									$issue_balance = $prog_Qnty - $issue_qnty + $issue_ret_qnty;
									$program_nos =array_unique(explode(",",chop($reqs_array[$booking_no]['program_no'],",")));
									$prod_qnty=$return_qnty=0;
									foreach($program_nos as $program_id)
									{
										//$progss[$program_id]=$program_id;
										$prod_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
										$return_qnty += $productionQntyArr[$program_id]["return_qnty"];
									}
									$po_number = implode(",",array_unique(explode(",",chop($row['po'],','))));
									$balance_qnty = $issue_qnty - $issue_ret_qnty - $prod_qnty - $return_qnty - $reject_qnty;

									//for booking yarn balance
									$booking_yarn_balance = (number_format($row['qnty'],2,'.','')-(number_format($issue_qnty,2,'.',''))+(number_format($issue_ret_qnty,2,'.','')+number_format($reject_qnty,2,'.','')));

									//$po_ids = implode(",",array_unique(explode(",",chop($row['po_id'],','))));

									//===========================================
									if ($row['booking_type'] == 4)
									{
										$booking_type = 3;
									}
									else
									{
										$booking_type = $row['is_short'];
									}

									$row[csf('booking_no')] = $booking_no;
									$row[csf('company_id')] = $row['company_id'];
									$row[csf('po_break_down_id')] = $po_ids;
									$row[csf('item_category')] = 2;
									$row[csf('fabric_source')] = 1;
									$row[csf('job_no')] = $job_no;
									$row[csf('is_approved')] = $row['is_approved'];


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
											//$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$po_id_str = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
											$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $po_id_str . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
										}
										else if ($row_id == 370)
										{
											//$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$po_id_str = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
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
									//============================================
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<?
										if($y == 1)
										{
											?>
											<td width="40" rowspan="<? echo $job_row_span;?>"><? echo $i;?></td>
											<td width="100" rowspan="<? echo $job_row_span;?>"><p>&nbsp;<? echo $job_no;?></p></td>
											<td width="80" rowspan="<? echo $job_row_span;?>"><? echo $buyer_arr[$row['buyer_id']];?></td>
											<?
										}
										?>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $print_btn;?></p></td>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $booking_type_arr[$booking_no];?></p></td>
										<td width="100"><p style="word-break: break-all; text-align: center;">&nbsp;<?
											$internal_ref_no="";
											foreach(explode(",", $po_ids) as $po_id)
											{
												if($internal_ref_no =="") $internal_ref_no =  $internalRefArr[$po_id]["internalRefNumber"]; else $internal_ref_no .= ",".$internalRefArr[$po_id]["internalRefNumber"];
											}
											echo $internal_ref_no;
											?>
											</p>
										</td>
										<td width="130" style="max-width:130px;"><p style="word-break: break-all;">&nbsp;<? echo $po_number;//chop($row['po'],',');?></p></td>
										<td width="100" align="right"><p><? echo number_format($row['qnty'],2);?></p></td>
										<td width="100" align="right" style="<? echo $alloc_bgcolor;?>"><a href='##' onClick="openmypage_allocation('<? echo $job_no . "_" . $booking_no ?>', 'allocation_popup')"><p><? echo number_format($allocation_qnty,2);?></p></a></td>
										<td width="130" align="right"><p><? echo number_format($allocation_balance,2);?></p></td>
										<td width="90" align="right" style="<? echo $program_bgcolor;?>"><a href='##' onClick="openmypage_program('<? echo $program_ids; ?>', 'program_popup')"><p><? echo number_format($prog_Qnty,2);?></p></a></td>

										<td width="90" align="right" ><p><? echo number_format($prog_balance,2);?></p></td>

										<td width="90" align="right" style="<? echo $requ_bgcolor;?>" title="Requisition no=<? echo $requ_nos;?>"><a href='##' onClick="openmypage_requisition('<? echo $program_ids; ?>', 'requisition_popup')"><p><? echo number_format($requ_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($requ_balance,2);?></p></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_popup')"><p><? echo number_format($issue_qnty,2);?></p></a></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_return_popup')"><p><? echo number_format($issue_ret_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($booking_yarn_balance,2);?></p></td>
										<td width="120" align="right"><p><? echo number_format($issue_balance,2);?></p></td>
										<td width="70" align="right" title="<? echo chop($reqs_array[$booking_no]['program_no'],",");?>"><a href='##' onClick="openmypage_production('<? echo $job_no . "_" . $po_ids . "_" . $program_ids  ?>', 'production_popup')"><p><? echo number_format($prod_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($return_qnty,2);?></p></td>
										<td width="90" align="right"><p><? echo number_format($reject_qnty,2);?></p></td>
										<td width="" align="right"><p><? echo number_format($balance_qnty,2);?></p></td>
									</tr>
									<?
									$m++;$y++;//$job_no
									$sub_job_book_qnty +=  $row['qnty'];
									$sub_job_alloc_qnty += $allocation_qnty;
									$sub_alloc_balance += $allocation_balance;
									$sub_prog_Qnty += $prog_Qnty;
									$sub_prog_balance += $prog_balance;
									$sub_requ_qnty += $requ_qnty;
									$sub_requ_balance +=$requ_balance;
									$sub_issue_qnty += $issue_qnty;
									$sub_issue_ret_qnty += $issue_ret_qnty;
									$sub_issue_balance += $issue_balance;
									$sub_prod_qnty +=$prod_qnty;
									$sub_return_qnty += $return_qnty;
									$sub_reject_qnty += $reject_qnty;
									$sub_balance_qnty += $balance_qnty;
									$sub_booking_yarn_balance += number_format($booking_yarn_balance,2,'.','');
									$show = true;
								}
							}

							$i++;

							if($show == true)
							{
								?>
								<tr style="font-weight: bold;background-color: #e0e0e0">
									<td colspan="3" width="220">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td colspan="2" align="right" width="230">Job Sub Total </td>
									<td align="right" width="100"><p><? echo number_format($sub_job_book_qnty,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_job_alloc_qnty,2)?></p></td>
									<td align="right" width="130"><p><? echo number_format($sub_alloc_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_Qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_ret_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_booking_yarn_balance,2);?></p></td>
									<td align="right" width="120"><p><? echo number_format($sub_issue_balance,2);?></p></td>
									<td align="right" width="70"><p><? echo number_format($sub_prod_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_return_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_reject_qnty,2);?></p></td>
									<td align="right"  width=""><p><? echo number_format($sub_balance_qnty,2);?></p></td>
								</tr>
								<?
							}
							$grand_job_book_qnty +=  $sub_job_book_qnty;
							$grand_job_alloc_qnty += $sub_job_alloc_qnty;
							$grand_alloc_balance += $sub_alloc_balance;
							$grand_prog_Qnty += $sub_prog_Qnty;
							$grand_prog_balance += $sub_prog_balance;
							$grand_requ_qnty += $sub_requ_qnty;
							$grand_requ_balance +=$sub_requ_balance;
							$grand_issue_qnty += $sub_issue_qnty;
							$grand_issue_ret_qnty += $sub_issue_ret_qnty;
							$grand_issue_balance += $sub_issue_balance;
							$grand_prod_qnty +=$sub_prod_qnty;
							$grand_return_qnty += $sub_return_qnty;
							$grand_reject_qnty += $sub_reject_qnty;
							$grand_balance_qnty += $sub_balance_qnty;
							$grand_booking_yarn_balance += number_format($sub_booking_yarn_balance,2,'.','');
						}

						?>
						<tr style="font-weight: bold;background-color: #ccc;border-top: 2px solid">
							<td colspan="3" width="220">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td colspan="2" align="right" width="230">Job Grand Total </td>
							<td align="right" width="100"><p><? echo number_format($grand_job_book_qnty,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($grand_job_alloc_qnty,2)?></p></td>
							<td align="right" width="130"><p><? echo number_format($grand_alloc_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_Qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_ret_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_booking_yarn_balance,2);?></p></td>
							<td align="right" width="120"><p><? echo number_format($grand_issue_balance,2);?></p></td>
							<td align="right" width="70"><p><? echo number_format($grand_prod_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_reject_qnty,2);?></p></td>
							<td align="right" width=""><p><? echo number_format($grand_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
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

//for show-2 button
if ($action == "report_generate_2")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$cbo_allocation_type = str_replace("'", "", $cbo_allocation_type);
	$report_type = str_replace("'", "", $report_type);
	$companyID = str_replace("'", "", $cbo_company_name);
	$buyerId = str_replace("'", "", $cbo_buyer_name);
	$internalRefNo = str_replace("'", "", $txt_internal_ref_no);

	if($companyID>0) $company_cond="and a.company_id=$companyID"; else $company_cond="";
	if($companyID>0) $company_cond_2="and a.company_name=$companyID"; else $company_cond_2="";

	if($buyerId>0)
	{
		$buyer_cond="and a.buyer_id = ".$buyerId."";
		$buyer_cond2="and b.buyer_id = ".$buyerId."";
		$buyer_cond3="and c.buyer_id = ".$buyerId."";
		$buyer_cond4="and e.buyer_name = ".$buyerId."";
	}
	else
	{
		$buyer_cond="";
		$buyer_cond2="";
		$buyer_cond3="";
	}

	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "")
	{
		$jobArr = explode("*",str_replace("'", "", trim($txt_job_no)));

		foreach($jobArr as $job)
		{
			$job_nos .= "'".$job."',";
		}
		$job_nos = chop($job_nos,",");
		$job_cond .= " and b.job_no in ( $job_nos )" ;

	}

	if ($internalRefNo!="")
	{$internalRefNo_cond="and c.grouping='$internalRefNo'";}else{$internalRefNo_cond="";}
	$booking_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = " and a.booking_no = $txt_booking_no";
	}

	$booking_date_cond = "";
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		$booking_date_cond = " and a.booking_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
	}

	//for program no
	$txt_program_no = str_replace("'", "", $txt_program_no );
	if($txt_program_no != '')
	{
        $sqlProgram = "select a.booking_no as BOOKING_NO from ppl_planning_entry_plan_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.is_sales !=1 and a.dtls_id in(".$txt_program_no.") ".$booking_cond." group by a.booking_no";
		$sqlProgramRslt = sql_select($sqlProgram);
		$bookingNoArr = array();
		foreach($sqlProgramRslt as $row)
		{
			$bookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}

		$booking_cond = " and a.booking_no in('".implode("','",$bookingNoArr)."')";
	}

	//for Print Button Permission
	$print_report = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(1) and is_deleted=0 and status_active=1");
	$format_ids = explode(",", $print_report);
	$print_report2 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(2) and is_deleted=0 and status_active=1");
	$format_ids2 = explode(",", $print_report2);
	$print_report3 = return_field_value("format_id", "lib_report_template", "template_name=" . $companyID . "  and module_id=2 and report_id in(3) and is_deleted=0 and status_active=1");
	$format_ids3 = explode(",", $print_report3);
	//end for Print Button Permission

	//for vs
	$varialbe_production_sql = sql_select("select fabric_roll_level from variable_settings_production where company_name =".$companyID." and item_category_id = 13 and variable_list in(3) and is_deleted=0 and status_active=1");
	$is_roll_maintain = 0;
	foreach($varialbe_production_sql as $row)
	{
		$is_roll_maintain = $row[csf('fabric_roll_level')];
	}
	//end for vs

	if ($report_type == 2)
	{
		// Main SQL ====>
		$main_sql = "select a.id, a.company_id, a.booking_no, a.booking_type, a.is_approved, a.is_short, a.buyer_id, b.id as dtls_id, b.job_no, b.po_break_down_id, b.grey_fab_qnty as qnty, c.grouping, c.po_number
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and a.item_category=2 and a.fabric_source=1
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4)
			and b.grey_fab_qnty>0 $job_cond $booking_cond $booking_date_cond $company_cond $internalRefNo_cond $buyer_cond
			order by  a.job_no,a.buyer_id,a.booking_no";
		//echo $main_sql;die;
		$result = sql_select($main_sql);
		$data_summury = array();
		$data_array = array();
		$bkn_dtls_id = array();
		$byr_job_arr = array();
		foreach($result as $row)
		{
			$bkn_dtls_id[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			$poArr[$row[csf("po_break_down_id")]] = $row[csf("po_number")];
			$byr_job_arr[$row[csf("buyer_id")]][$row[csf("job_no")]] = $row[csf("job_no")];
			$internalRefArr[$row[csf("po_break_down_id")]]["internalRefNumber"]=$row[csf("grouping")];

			$data_summury[$row[csf("buyer_id")]]["qnty"] += $row[csf("qnty")];
			$data_summury[$row[csf("buyer_id")]]["job_no"] .= "'".$row[csf("job_no")]."',";
			$data_summury[$row[csf("buyer_id")]]["booking_no"] .= "'".$row[csf("booking_no")]."',";

			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po'] .= $row[csf("po_number")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po_id'] .= $row[csf("po_break_down_id")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['qnty'] += $row[csf("qnty")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['company_id'] = $row[csf("company_id")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_approved'] = $row[csf("is_approved")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['booking_type'] = $row[csf("booking_type")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['is_short'] = $row[csf("is_short")];

			$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			$booking_nos_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$all_po_ids_for_buyer_issue .= $row[csf("po_break_down_id")].",";

			if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==2)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Main";
			}
			else if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==1)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Short";
			}
			else if($row[csf("booking_type")] ==4 )
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Sample";
			}
		}
		unset($result);

		$job_numbers="'".implode("','",$job_no_arr)."'";
		$booking_nos="'".implode("','",$booking_nos_arr)."'";

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		$job_number_arr = array_unique(explode(",",chop($job_numbers,",")));
		$booking_nos = implode(",",array_unique(explode(",",chop($booking_nos,","))));
		$booking_nos_arr = array_unique(explode(",",chop($booking_nos,",")));

		$alloc_booking_cond = "";$requisition_booking_cond="";$issue_booking_cond="";$production_booking_cond="";

		if($booking_nos != "")
		{
			if ($db_type == 0)
			{
				$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
				$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
				$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
				$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
			}
			else
			{
				if (count($booking_nos_arr) > 1000)
				{
					$alloc_booking_cond = " and (";
					$booking_nos_arr = array_chunk($booking_nos_arr, 1000);
					$z = 0;
					foreach ($booking_nos_arr as $booking_no)
					{
						$booking_no = implode(",", $booking_no);
						if ($z == 0)
						{
							$alloc_booking_cond .= " a.booking_no in(" . $booking_no . ")";
							$requisition_booking_cond .= " c.booking_no in(" . $booking_no . ")";
							$issue_booking_cond .= " d.booking_no in(" . $booking_no . ")";
							$production_booking_cond .= " d.booking_no in(" . $booking_no . ")";
						}
						else
						{
							$alloc_booking_cond .= " or a.booking_no in(" . $booking_no . ")";
							$requisition_booking_cond .= " or c.booking_no in(" . $booking_no . ")";
							$issue_booking_cond .= " or d.booking_no in(" . $booking_no . ")";
							$production_booking_cond .= " or d.booking_no in(" . $booking_no . ")";
						}
						$z++;
					}
					$alloc_booking_cond .= ")";
					$requisition_booking_cond .= ")";
					$issue_booking_cond .= ")";
					$production_booking_cond .= ")";
				}
				else
				{
					$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
					$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
					$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
					$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
				}
			}
		}


		$alloc_job_cond = "";$program_job_cond="";$requistion_job_cond="";
		if($job_numbers != "")
		{
			if ($db_type == 0)
			{
				$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
				$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
				$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
				$issue_job_cond .= " and d.job_no in ( $job_numbers )" ;
				$production_job_cond .= " and d.job_no in ( $job_numbers )" ;
			}
			else
			{
				if (count($job_number_arr) > 1000)
				{
					$alloc_job_cond = " and (";
					$job_number_arr = array_chunk($job_number_arr, 1000);
					$z = 0;
					foreach ($job_number_arr as $job_number )
					{
						$job_number = implode(",", $job_number);
						if ($z == 0)
						{
							$alloc_job_cond .= " a.job_no in(" . $job_number . ")";
							$program_job_cond .= " c.job_no in(" . $job_number . ")";
							$requistion_job_cond .= " d.job_no in(" . $job_number . ")";
							$issue_job_cond .= " d.job_no in(" . $job_number . ")";
							$production_job_cond .= " d.job_no in(" . $job_number . ")";
						}
						else
						{
							$alloc_job_cond .= " or a.job_no in(" . $job_number . ")";
							$program_job_cond .= " or c.job_no in(" . $job_number . ")";
							$requistion_job_cond .= " or d.job_no in(" . $job_number . ")";
							$issue_job_cond .= " or d.job_no in(" . $job_number . ")";
							$production_job_cond .= " or d.job_no in(" . $job_number . ")";
						}
						$z++;
					}
					$alloc_job_cond .= ")";
					$program_job_cond .= ")";
					$requistion_job_cond .= ")";
					$issue_job_cond .= ")";
					$production_job_cond .= ")";
				}
				else
				{
					$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
					$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
					$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
					$issue_job_cond = " and d.job_no in(" . $job_numbers . ")";
					$production_job_cond = " and d.job_no in(" . $job_numbers . ")";
				}
			}
		}

        //allocation Qnty
        $allocation_sql ="select a.job_no, a.booking_no, a.qnty, a.po_break_down_id,b.buyer_id
		from inv_material_allocation_mst a,wo_booking_mst b
		where a.booking_no=b.booking_no $alloc_job_cond $alloc_booking_cond $buyer_cond2 and b.company_id=$companyID AND (a.is_dyied_yarn != 1 or a.is_dyied_yarn is null) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
		$allocation_sql_resutl = sql_select($allocation_sql);

		$allocation_data = array();
		foreach($allocation_sql_resutl as $row)
		{
			$allocation_data[$row[csf("job_no")]][$row[csf("booking_no")]]['alloc_qnty'] += $row[csf("qnty")];
			$allocation_data[$row[csf("buyer_id")]]['alloc_buyer_qnty'] += $row[csf("qnty")];
		}
		unset($allocation_sql_resutl);

        //Program Qnty
        $progQnty_sql = "select a.booking_no,a.buyer_id,
			b.program_qnty as program_qnty, b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond $buyer_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, b.id,b.program_qnty order by b.id";
        /* $progQnty_sql = "select a.booking_no,a.buyer_id,
			a.program_qnty as program_qnty, a.id,b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, a.id,b.id,a.program_qnty order by b.id";*/
		$progQntyResult = sql_select($progQnty_sql);
		$progQntyArr =$progNoArr= array(); $programIdQntyChk = array();
		foreach($progQntyResult as $row)
		{
			if($programIdQntyChk[$row[csf("id")]] == "")
			{
				$programIdQntyChk[$row[csf("id")]] = $row[csf("id")];
				$progQntyArr[$row[csf("booking_no")]]['book_prog_qnty'] += $row[csf("program_qnty")];
				$progNoArr[$row[csf("booking_no")]][] = $row[csf("program_id")];
				$progQntyArr[$row[csf("buyer_id")]]['buyer_prog_qnty'] += $row[csf("program_qnty")];
			}
		}
		unset($progQntyResult);

        //Requisition Qnty

		/*$reqs_sql = "select a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b,ppl_planning_info_entry_mst c, wo_booking_dtls d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond and c.company_id=$companyID
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";*/

		$reqs_sql = "select a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, wo_booking_mst d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond $buyer_cond3 and c.company_id=$companyID
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";
		$reqs_sql_result = sql_select($reqs_sql);
		$requIdCheckArr = array();
		$reqs_array = array();
		foreach ($reqs_sql_result as $row)
		{
			if($requIdCheckArr[$row[csf('id')]] == "")
			{
				$requIdCheckArr[$row[csf('id')]] = $row[csf('id')];
				$reqs_array[$row[csf('booking_no')]]['qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_array[$row[csf('booking_no')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$reqs_array[$row[csf('booking_no')]]['program_no'] .= $row[csf('program_no')].",";
				$reqs_array[$row[csf('buyer_id')]]['buyer_req_qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_buyer_array[$row[csf('buyer_id')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$program_buyer_array[$row[csf('buyer_id')]]['program_no'] .= $row[csf('program_no')].",";
			}
		}
		unset($reqs_sql_result);

		$product_sql = "select a.booking_id, c.quantity as knitting_qnty,c.returnable_qnty as return_qnty, c.id, d.booking_no , d.job_no,a.buyer_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_booking_dtls d
			where a.id=b.mst_id and c.dtls_id=b.id $production_job_cond $production_booking_cond $company_cond $buyer_cond
			and c.po_breakdown_id = d.po_break_down_id
			and a.item_category=13 and a.entry_form=2 and c.entry_form=2
			and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active  = 1 and c.is_deleted = 0
			and a.status_active = 1 and a.is_deleted = 0";
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
				}
			}
		}
		unset($productionArr);
		// echo "<pre>";
		// print_r($nprog);
		//print_r($productionQntyArr_buy);

		$all_po_ids=chop($all_po_ids_for_buyer_issue,',');
		$all_po_ids_arr = array_filter(array_unique(explode(",",$all_po_ids)));

		if($db_type==2 && count($all_po_ids_arr)>1000)
		{
			$po_ids_cond=" and (";
			$po_ids_cond2=" and (";
			$po_ids_cond3=" and (";
			$all_po_ids_chunk_arr=array_chunk($all_po_ids_arr,999);
			foreach($all_po_ids_chunk_arr as $ids)
			{
				$po_ids_cond.=" b.po_breakdown_id in($ids) or";
				$po_ids_cond2.=" b.to_order_id in($ids) or";
				$po_ids_cond3.=" b.from_order_id in($ids) or";
			}
			$po_ids_cond=chop($po_ids_cond,'or ');
			$po_ids_cond2=chop($po_ids_cond2,'or ');
			$po_ids_cond3=chop($po_ids_cond3,'or ');
			$po_ids_cond.=")";
			$po_ids_cond2.=")";
			$po_ids_cond3.=")";
		}
		else
		{
			$all_poids=implode(",",$all_po_ids_arr);
			$po_ids_cond=" and b.po_breakdown_id in($all_poids)";
			$po_ids_cond2=" and b.to_order_id in($all_poids)";
			$po_ids_cond3=" and b.from_order_id in($all_poids)";
		}

		//for issue qty
		$sql_issue = "select (b.quantity) as issue_qnty, e.buyer_name, d.requisition_no
		from order_wise_pro_details b, inv_transaction d, wo_po_break_down c, wo_po_details_master e
		where d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.trans_type=2 and b.entry_form=3 and b.status_active=1 and d.receive_basis = 3 and b.is_deleted=0 and d.status_active= 1 and b.issue_purpose not in (2,8) and d.company_id=$companyID $po_ids_cond $buyer_cond4";
		$sql_issue_data = sql_select( $sql_issue );
		$issue_qty_arr=array();
		foreach ($sql_issue_data as $row)
		{
			$issue_qty_arr[$row[csf('requisition_no')]]['issue_qnty'] += $row[csf('issue_qnty')];
			$issue_qty_summary_arr[$row[csf('buyer_name')]]['issue_qnty'] += $row[csf('issue_qnty')];
		}
		unset($sql_issue_data);

		//for issue return qty
		$sql_return = "select (b.quantity) as returned_qnty, b.reject_qty as cons_reject_qnty, e.buyer_name, a.booking_no as requ_no from inv_receive_master a, order_wise_pro_details b, inv_transaction d,wo_po_break_down c, wo_po_details_master e where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $po_ids_cond $company_cond $buyer_cond4";
		$sql_return_data = sql_select($sql_return);
		$return_qty_arr=array();
		foreach ($sql_return_data as $row)
		{
			$return_qty_arr[$row[csf('requ_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
			$return_qty_summary_arr[$row[csf('buyer_name')]]['returned_qnty'] += $row[csf('returned_qnty')];

			$reject_qty_arr[$row[csf('requ_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
			$reject_qty_summary_arr[$row[csf('buyer_name')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
		}
		unset($sql_return_data);

		//for transfer
		if($db_type==2 && count($bkn_dtls_id)>1000)
		{
			$booking_cond=" and (";
			$bkn_dtls_id_chunk_arr=array_chunk($bkn_dtls_id,999);
			foreach($bkn_dtls_id_chunk_arr as $ids)
			{
				$booking_cond.=" c.id in($ids) or";
			}
			$booking_cond=chop($booking_cond,'or ');
			$booking_cond.=")";
		}
		else
		{
			$bkn_dtls_ids=implode(",",$bkn_dtls_id);
			$booking_cond=" and c.id in(".$bkn_dtls_ids.")";
		}

		$sql_trnf = "SELECT b.id, b.transfer_qnty, c.job_no, c.booking_no, d.buyer_id
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_booking_dtls c, wo_booking_mst d
		WHERE a.id = b.mst_id and b.to_order_id = c.po_break_down_id and c.booking_no = d.booking_no and a.item_category = 13 and a.entry_form = 82 and a.transfer_criteria in(1, 4)".$po_ids_cond2.$booking_cond;
		//echo $sql_trnf;
		$sql_trnf_rslt = sql_select($sql_trnf);
		$transfer_data = array();
		$duplicate_check = array();
		foreach($sql_trnf_rslt as $row)
		{
			if($duplicate_check[$row[csf('id')]] != $row[csf('id')])
			{
				$duplicate_check[$row[csf('id')]] = $row[csf('id')];
				$transfer_data['sumarry'][$row[csf('buyer_id')]]['qty'] += $row[csf('transfer_qnty')];
				$transfer_data['details'][$row[csf('job_no')]][$row[csf('booking_no')]]['qty'] += $row[csf('transfer_qnty')];
			}
		}

		//========== Transfer Out =====================
		$sql_trnf_out = "SELECT b.id, b.transfer_qnty, c.job_no, c.booking_no, d.buyer_id
		FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_booking_dtls c, wo_booking_mst d
		WHERE a.id = b.mst_id and b.from_order_id = c.po_break_down_id and c.booking_no = d.booking_no and a.item_category = 13 and a.entry_form = 82 and a.transfer_criteria in(1, 4)".$po_ids_cond3.$booking_cond;
		//echo $sql_trnf_out;
		$sql_trnf_out_rslt = sql_select($sql_trnf_out);
		$trans_out_data = array();
		$duplicate_trns_out_check = array();
		foreach($sql_trnf_out_rslt as $row)
		{
			if($duplicate_trns_out_check[$row[csf('id')]] != $row[csf('id')])
			{
				$duplicate_trns_out_check[$row[csf('id')]] = $row[csf('id')];
				$trans_out_data['sumarry'][$row[csf('buyer_id')]]['qty'] += $row[csf('transfer_qnty')];
				$trans_out_data['details'][$row[csf('job_no')]][$row[csf('booking_no')]]['qty'] += $row[csf('transfer_qnty')];
			}
		}

		$tbl1 = 1530;
		$tbl2 = 1480;
		$tbl3 = 2090;
		$col1 = 16;
		$col2 = 22;
		if($is_roll_maintain == 1)
		{
			$tbl1 = 1690;
			$tbl2 = 1720;
			$tbl3 = 2330;
			$col1 = 18;
			$col2 = 24;
		}

		ob_start();
		?>
		<fieldset style="width:1650px;">
			<table cellpadding="0" cellspacing="0" width="<? echo $tbl1; ?>">
				<tr>
					<td align="center" width="100%" colspan="<? echo $col1; ?>" style="font-size:16px"><strong>Booking and Plan Wise Yarn Issue Monitoring Report</strong></td>
				</tr>
				<tr>
					<td width="100%" colspan="<? echo $col1; ?>" style="font-size:12px"><strong>Summury : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl2; ?>" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="80">Buyer</th>
					<th width="100">Book Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130"><p>Yarn Allocation<br> Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></p></th>
                    <?
                    if($is_roll_maintain == 1)
                    {
                        ?>
                        <th width="80" style="background:#999;">grey Transfer In</th>
                        <th width="80" style="background:#999;">grey Transfer Out</th>
                        <th width="80" style="background:#999;">Net Balance</th>
                        <?
                    }
                    ?>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="120"><p>Issue Balance<br><small style="font-size: 8px;">(Knit Program - Yarn Issue + issue return)</small></p></th>
					<th width="90">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:<? echo $tbl2+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl2; ?>" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$z = 1;
						foreach($data_summury as $buyer_id =>$row)
						{
							if ($z % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$alloc_buyer_qnty = $allocation_data[$buyer_id]["alloc_buyer_qnty"];
							$alloc_buyer_qnty_balance = $row["qnty"] - $alloc_buyer_qnty;
							$buyer_prog_qnty = $progQntyArr[$buyer_id]['buyer_prog_qnty'];
							$buyer_prog_qnty_balance = $row["qnty"] - $buyer_prog_qnty;
							//$buyer_prog_qnty_balance = number_format($buyer_prog_qnty_balance,2,".","");
							$buyer_req_qnty = $reqs_array[$buyer_id]['buyer_req_qnty'];
							$buyer_req_qnty_balance = $alloc_buyer_qnty-$buyer_req_qnty;

							//$buyer_issue_qnty = $issue_qty_summary_arr[$buyer_id]['issue_qnty'];
							//$buyer_issue_ret_qnty = $return_qty_summary_arr[$buyer_id]['returned_qnty'];
							//$buyer_reject_qnty = $reject_qty_summary_arr[$buyer_id]['reject_qnty'];
							$buyer_issue_qnty = 0;
							$buyer_issue_ret_qnty=0;
							$buyer_reject_qnty = 0;
							$buyer_requ_id_arr = array_unique(explode(",",chop($reqs_buyer_array[$buyer_id]['requ_no'],",")));
							foreach($buyer_requ_id_arr as $requ_id)
							{
								$buyer_issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
								$buyer_issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
								$buyer_reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
							}

							$buyer_program_nos =array_unique(explode(",",chop($program_buyer_array[$buyer_id]['program_no'],",")));
							$buyer_production_qnty=$buyer_return_qnty=0;
							foreach($buyer_program_nos as $program_id)
							{
								$buyer_production_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
								$buyer_return_qnty += $productionQntyArr[$program_id]["return_qnty"];
							}

							$buyer_issue_qnty_balance = $buyer_prog_qnty-$buyer_issue_qnty + $buyer_issue_ret_qnty;
							//$buyer_production_qnty = $productionQntyArr_buy[$buyer_id]["buyer_prod_qnty"];
							//$buyer_return_qnty = $productionQntyArr_buy[$buyer_id]["buyer_return_qnty"];
							$buyer_balance_qnty = $buyer_issue_qnty - $buyer_issue_ret_qnty -$buyer_production_qnty- $buyer_return_qnty-$buyer_reject_qnty;

							//for transfer qty
							$buyer_transfer_qty = $transfer_data['sumarry'][$buyer_id]['qty'];
							$buyer_trans_out_qty = $trans_out_data['sumarry'][$buyer_id]['qty'];
							$buyer_yrn_net_qty = ($alloc_buyer_qnty_balance - $buyer_transfer_qty)+$buyer_trans_out_qty;
							//$buyer_yrn_net_qty = $alloc_buyer_qnty_balance - $buyer_transfer_qty;

							//for buyer job
							$byr_job_str = implode(',', $byr_job_arr[$buyer_id]);

							if((($cbo_allocation_type == 1) && (number_format($alloc_buyer_qnty_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($alloc_buyer_qnty_balance,2) <= 0.00)) || ($cbo_allocation_type == 0))
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_s_<? echo $z; ?>', '<? echo $bgcolor; ?>')" id="tr_s_<? echo $z; ?>">
									<td width="40"><? echo $z;?></td>
									<td width="80" align="right"><? echo $buyer_arr[$buyer_id];?></td>
									<td width="100" align="right"><p><? echo number_format($row["qnty"],2);?></p></td>
									<td width="100" align="right"><p><? echo number_format($alloc_buyer_qnty,2);?></p></td>
									<td width="130" align="right"><p><a href='##' onClick="func_qty_popup('<? echo $byr_job_str; ?>')"><? echo number_format($alloc_buyer_qnty_balance,2); ?></a></p>
									<?
                                    if($is_roll_maintain == 1)
                                    {
                                        ?>
                                        <td width="80" align="right"><? echo number_format($buyer_transfer_qty,2);?></td>
                                        <td width="80" align="right"><? echo number_format($buyer_trans_out_qty,2);?></td>
                                        <td width="80" align="right"><? echo number_format($buyer_yrn_net_qty,2);?></td>
                                        <?
                                    }
                                    ?>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_ret_qnty,2);?></p></td>
									<td width="120" align="right"><p><? echo number_format($buyer_issue_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_production_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_return_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_reject_qnty,2);?></p></td>
									<td width="" align="right"><p><? echo number_format($buyer_balance_qnty,2);?></p></td>
								</tr>
								<?
								$z++;
								$tot_booking += $row["qnty"];
								$tot_alloc_buyer_qnty += $alloc_buyer_qnty;
								$tot_buyer_prog_qnty += $buyer_prog_qnty;
								$tot_buyer_req_qnty += $buyer_req_qnty;
								$tot_buyer_issue_qnty += $buyer_issue_qnty;
								$tot_buyer_issue_ret_qnty += $buyer_issue_ret_qnty;
								$tot_buyer_production_qnty+=$buyer_production_qnty;
								$tot_buyer_return_qnty += $buyer_return_qnty;
								$tot_buyer_reject_qnty += $buyer_reject_qnty;
								$tot_buyer_balance_qnty += $buyer_balance_qnty;

								$tot_buyer_transfer_qty += $buyer_transfer_qty;
								$tot_buyer_trans_out_qty += $buyer_trans_out_qty;
								$tot_buyer_yrn_net_qty += $buyer_yrn_net_qty;
							}
						}
						?>
						<tr style="font-weight:bold;background-color: #e0e0e0">
							<td colspan="2" align="left" width="120">Total:</td>
							<td align="right" width="100"><p><? echo number_format($tot_booking,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($tot_alloc_buyer_qnty,2);?></p></td>
							<td width="130"></td>
							<?
                            if($is_roll_maintain == 1)
                            {
                                ?>
                                <td width="80" align="right"><? echo number_format($tot_buyer_transfer_qty,2);?></td>
                                <td width="80" align="right"><? echo number_format($tot_buyer_trans_out_qty,2);?></td>
                                <td width="80" align="right"><? echo number_format($tot_buyer_yrn_net_qty,2);?></td>
                                <?
                            }
                            ?>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_prog_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_req_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_ret_qnty,2);?></p></td>
							<td width="120"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_production_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_reject_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($tot_buyer_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br/>
			<table cellpadding="0" cellspacing="0" width="<? echo $tbl3; ?>">
				<tr>
					<td width="100%" colspan="<? echo $col2; ?>" style="font-size:12px"><strong>Details : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl3; ?>" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Job No.</th>
					<th width="80">Buyer</th>
					<th width="100">Fabric Booking No</th>
					<th width="100">Booking Type</th>
					<th width="100">Internal Ref. No</th>
					<th width="130">PO No</th>
					<th width="100">Booking Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130">Yarn Allocation Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></th>
                    <?
                    if($is_roll_maintain == 1)
                    {
                        ?>
                        <th width="80" style="background:#999;">grey Transfer In</th>
                        <th width="80" style="background:#999;">grey Transfer Out</th>
                        <th width="80" style="background:#999;">Net Balance</th>
                        <?
                    }
                    ?>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="90">Booking Yarn Balance<br><p><small style="font-size: 8px;">(Booking Qty - Yarn Issue) + (Issue Return + Reject Qty)</small></p></th>
					<th width="120">Issue Balance<br><p><small style="font-size: 8px;">(Knit Program - Yarn Issue) + Issue Return</small></p></th>
					<th width="70">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:<? echo $tbl3+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body_dtls">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl3; ?>" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						foreach($data_array as $job_no => $job_data)
						{
							$job_row_span =0;
							foreach($job_data as $booking_no => $row)
							{
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								$allocation_balance = $row["qnty"] - $allocation_qnty;
								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$job_row_span++;
								}
							}
							$buyer_row_span_arr[$job_no]=$job_row_span;
						}

						$i=$m=1;
						foreach($data_array as $job_no => $job_data)
						{
							$y=1;
							$show= false;
							$sub_job_book_qnty=$sub_job_alloc_qnty=$sub_alloc_balance=$sub_prog_Qnty=$sub_prog_balance=$sub_requ_qnty=$sub_requ_balance=$sub_issue_qnty=$sub_issue_ret_qnty=$sub_issue_balance=$sub_prod_qnty=$sub_return_qnty=$sub_reject_qnty=$sub_balance_qnty=0;
							$sub_booking_transfer_qty = 0;
							$sub_booking_yrn_net_qty = 0;

							foreach($job_data as $booking_no => $row)
							{
								if ($m % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$job_row_span=$buyer_row_span_arr[$job_no];
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								if ($allocation_qnty == 0) $alloc_bgcolor = "background-color:red;";else $alloc_bgcolor= "";
								$allocation_balance = $row["qnty"] - $allocation_qnty;

								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$po_ids = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
									$prog_Qnty = $progQntyArr[$booking_no]['book_prog_qnty'];
									$program_ids = implode(", ",array_filter(array_unique($progNoArr[$booking_no])));
									if ($prog_Qnty == 0) $program_bgcolor = "background-color:red;";else $program_bgcolor= "";
									$prog_balance = $row["qnty"] - $prog_Qnty;
									//$prog_balance = number_format($prog_balance,2,".","");
									$requ_qnty = $reqs_array[$booking_no]['qnty'];
									if ($requ_qnty == 0) $requ_bgcolor = "background-color:red;";else $requ_bgcolor= "";
									$requ_balance = $allocation_qnty - $requ_qnty;
									$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_no]['requ_no'],",")));

									$issue_qnty = 0; $issue_ret_qnty = 0; $requ_nos = "";$reject_qnty=0;$balance_qnty=0;
									foreach($requ_id_arr as $requ_id)
									{
										$issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
										$issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
										if($requ_nos=="") $requ_nos .= $requ_id; else $requ_nos .= ",".$requ_id;

										$reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
									}

									$issue_balance = $prog_Qnty - $issue_qnty + $issue_ret_qnty;
									$program_nos =array_unique(explode(",",chop($reqs_array[$booking_no]['program_no'],",")));
									$prod_qnty=$return_qnty=0;
									foreach($program_nos as $program_id)
									{
										//$progss[$program_id]=$program_id;
										$prod_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
										$return_qnty += $productionQntyArr[$program_id]["return_qnty"];
									}
									$po_number = implode(",",array_unique(explode(",",chop($row['po'],','))));
									$balance_qnty = $issue_qnty - $issue_ret_qnty - $prod_qnty - $return_qnty - $reject_qnty;

									//for booking yarn balance
									$booking_yarn_balance = (number_format($row['qnty'],2,'.','')-(number_format($issue_qnty,2,'.',''))+(number_format($issue_ret_qnty,2,'.','')+number_format($reject_qnty,2,'.','')));

									//for transfer qty
									$booking_transfer_qty = $transfer_data['details'][$job_no][$booking_no]['qty'];
									$booking_trans_out_qty = $trans_out_data['details'][$job_no][$booking_no]['qty'];
									$booking_yrn_net_qty = ($allocation_balance-$booking_transfer_qty)+$booking_trans_out_qty;

									//for booking link
									if ($row['booking_type'] == 4)
									{
										$booking_type = 3;
									}
									else
									{
										$booking_type = $row['is_short'];
									}

									$row[csf('booking_no')] = $booking_no;
									$row[csf('company_id')] = $row['company_id'];
									$row[csf('po_break_down_id')] = $po_ids;
									$row[csf('item_category')] = 2;
									$row[csf('fabric_source')] = 1;
									$row[csf('job_no')] = $job_no;
									$row[csf('is_approved')] = $row['is_approved'];

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
											$po_id_str = implode(',', $booking_po_arr[$row[csf("booking_no")]]);
											$print_btn = "<a href='#'  onClick=\"generate_worder_report('" . $booking_type . "','" . $row[csf('booking_no')] . "','" . $row[csf('company_id')] . "','" . $po_id_str . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "','" . $row_id . "','show_fabric_booking_report16','" . $i . "')\">" . $row[csf('booking_no')] . $pre . " <a/>";
										}
										else if ($row_id == 370)
										{
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
									//============================================
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<?
										if($y == 1)
										{
											?>
											<td width="40" rowspan="<? echo $job_row_span;?>"><? echo $i;?></td>
											<td width="100" rowspan="<? echo $job_row_span;?>"><p>&nbsp;<? echo $job_no;?></p></td>
											<td width="80" rowspan="<? echo $job_row_span;?>"><? echo $buyer_arr[$row['buyer_id']];?></td>
											<?
										}
										?>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $print_btn;?></p></td>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $booking_type_arr[$booking_no];?></p></td>
										<td width="100"><p style="word-break: break-all; text-align: center;">&nbsp;<?
											$internal_ref_no="";
											foreach(explode(",", $po_ids) as $po_id)
											{
												if($internal_ref_no =="") $internal_ref_no =  $internalRefArr[$po_id]["internalRefNumber"]; else $internal_ref_no .= ",".$internalRefArr[$po_id]["internalRefNumber"];
											}
											echo $internal_ref_no;
											?>
											</p>
										</td>
										<td width="130" style="max-width:130px;"><p style="word-break: break-all;">&nbsp;<? echo $po_number;//chop($row['po'],',');?></p></td>
										<td width="100" align="right"><p><? echo number_format($row['qnty'],2);?></p></td>
										<td width="100" align="right" style="<? echo $alloc_bgcolor;?>"><a href='##' onClick="openmypage_allocation('<? echo $job_no . "_" . $booking_no ?>', 'allocation_popup')"><p><? echo number_format($allocation_qnty,2);?></p></a></td>
										<td width="130" align="right"><p><? echo number_format($allocation_balance,2);?></p></td>
										<?
                                        if($is_roll_maintain == 1)
                                        {
											?>
                                            <td width="80" align="right"><? echo number_format($booking_transfer_qty,2);?></td>
                                            <td width="80" align="right"><? echo number_format($booking_trans_out_qty,2);?></td>
                                            <td width="80" align="right"><? echo number_format($booking_yrn_net_qty,2);?></td>
                                            <?
                                        }
                                        ?>
										<td width="90" align="right" style="<? echo $program_bgcolor;?>"><a href='##' onClick="openmypage_program('<? echo $program_ids; ?>', 'program_popup')"><p><? echo number_format($prog_Qnty,2);?></p></a></td>

										<td width="90" align="right" ><p><? echo number_format($prog_balance,2);?></p></td>

										<td width="90" align="right" style="<? echo $requ_bgcolor;?>" title="Requisition no=<? echo $requ_nos;?>"><a href='##' onClick="openmypage_requisition('<? echo $program_ids; ?>', 'requisition_popup')"><p><? echo number_format($requ_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($requ_balance,2);?></p></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_popup')"><p><? echo number_format($issue_qnty,2);?></p></a></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_return_popup')"><p><? echo number_format($issue_ret_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($booking_yarn_balance,2);?></p></td>
										<td width="120" align="right"><p><? echo number_format($issue_balance,2);?></p></td>
										<td width="70" align="right" title="<? echo chop($reqs_array[$booking_no]['program_no'],",");?>"><a href='##' onClick="openmypage_production('<? echo $job_no . "_" . $po_ids . "_" . $program_ids  ?>', 'production_popup')"><p><? echo number_format($prod_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($return_qnty,2);?></p></td>
										<td width="90" align="right"><p><? echo number_format($reject_qnty,2);?></p></td>
										<td width="" align="right"><p><? echo number_format($balance_qnty,2);?></p></td>
									</tr>
									<?
									$m++;$y++;//$job_no
									$sub_job_book_qnty +=  $row['qnty'];
									$sub_job_alloc_qnty += $allocation_qnty;
									$sub_alloc_balance += $allocation_balance;
									$sub_prog_Qnty += $prog_Qnty;
									$sub_prog_balance += $prog_balance;
									$sub_requ_qnty += $requ_qnty;
									$sub_requ_balance +=$requ_balance;
									$sub_issue_qnty += $issue_qnty;
									$sub_issue_ret_qnty += $issue_ret_qnty;
									$sub_issue_balance += $issue_balance;
									$sub_prod_qnty +=$prod_qnty;
									$sub_return_qnty += $return_qnty;
									$sub_reject_qnty += $reject_qnty;
									$sub_balance_qnty += $balance_qnty;
									$sub_booking_yarn_balance += number_format($booking_yarn_balance,2,'.','');
									$sub_booking_transfer_qty += $booking_transfer_qty;
									$sub_booking_trans_out_qty += $booking_trans_out_qty;
									$sub_booking_yrn_net_qty += $booking_yrn_net_qty;
									$show = true;
								}
							}

							$i++;
							if($show == true)
							{
								?>
								<tr style="font-weight: bold;background-color: #e0e0e0">
									<td colspan="3" width="220">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td colspan="2" align="right" width="230">Job Sub Total </td>
									<td align="right" width="100"><p><? echo number_format($sub_job_book_qnty,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_job_alloc_qnty,2)?></p></td>
									<td align="right" width="130"><p><? echo number_format($sub_alloc_balance,2);?></p></td>
									<?
                                    if($is_roll_maintain == 1)
                                    {
                                        ?>
                                        <td width="80" align="right"><? echo number_format($sub_booking_transfer_qty,2);?></td>
                                        <td width="80" align="right"><? echo number_format($sub_booking_trans_out_qty,2);?></td>
                                        <td width="80" align="right"><? echo number_format($sub_booking_yrn_net_qty,2);?></td>
                                        <?
                                    }
                                    ?>
									<td align="right" width="90"><p><? echo number_format($sub_prog_Qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_ret_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_booking_yarn_balance,2);?></p></td>
									<td align="right" width="120"><p><? echo number_format($sub_issue_balance,2);?></p></td>
									<td align="right" width="70"><p><? echo number_format($sub_prod_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_return_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_reject_qnty,2);?></p></td>
									<td align="right"  width=""><p><? echo number_format($sub_balance_qnty,2);?></p></td>
								</tr>
								<?
							}
							$grand_job_book_qnty +=  $sub_job_book_qnty;
							$grand_job_alloc_qnty += $sub_job_alloc_qnty;
							$grand_alloc_balance += $sub_alloc_balance;
							$grand_prog_Qnty += $sub_prog_Qnty;
							$grand_prog_balance += $sub_prog_balance;
							$grand_requ_qnty += $sub_requ_qnty;
							$grand_requ_balance +=$sub_requ_balance;
							$grand_issue_qnty += $sub_issue_qnty;
							$grand_issue_ret_qnty += $sub_issue_ret_qnty;
							$grand_issue_balance += $sub_issue_balance;
							$grand_prod_qnty +=$sub_prod_qnty;
							$grand_return_qnty += $sub_return_qnty;
							$grand_reject_qnty += $sub_reject_qnty;
							$grand_balance_qnty += $sub_balance_qnty;
							$grand_booking_yarn_balance += number_format($sub_booking_yarn_balance,2,'.','');
							$grand_booking_transfer_qty += $sub_booking_transfer_qty;
							$grand_booking_trans_out_qty += $sub_booking_trans_out_qty;
							$grand_booking_yrn_net_qty += $sub_booking_yrn_net_qty;
						}

						?>
						<tr style="font-weight: bold;background-color: #ccc;border-top: 2px solid">
							<td colspan="3" width="220">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td colspan="2" align="right" width="230">Job Grand Total </td>
							<td align="right" width="100"><p><? echo number_format($grand_job_book_qnty,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($grand_job_alloc_qnty,2)?></p></td>
							<td align="right" width="130"><p><? echo number_format($grand_alloc_balance,2);?></p></td>
							<?
                            if($is_roll_maintain == 1)
                            {
                                ?>
                                <td width="80" align="right"><? echo number_format($grand_booking_transfer_qty,2);?></td>
                                <td width="80" align="right"><? echo number_format($grand_booking_trans_out_qty,2);?></td>
                                <td width="80" align="right"><? echo number_format($grand_booking_yrn_net_qty,2);?></td>
                                <?
                            }
                            ?>
							<td align="right" width="90"><p><? echo number_format($grand_prog_Qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_ret_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_booking_yarn_balance,2);?></p></td>
							<td align="right" width="120"><p><? echo number_format($grand_issue_balance,2);?></p></td>
							<td align="right" width="70"><p><? echo number_format($grand_prod_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_reject_qnty,2);?></p></td>
							<td align="right" width=""><p><? echo number_format($grand_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
		</fieldset>
		<?
	}
    //print_r($progss); echo "<pre>";
	foreach (glob("$user_name*.xls") as $filename)
	{
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

//for yarn_qty_popup
if ($action == "yarn_qty_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	//b.grey_fab_qnty as qnty
	$job_no = "'".str_replace(",", "','", $job_no."'");
	$sql = "SELECT B.JOB_NO, B.CONS_QNTY, B.COUNT_ID, B.COPM_ONE_ID, B.PERCENT_ONE, SUM((A.PLAN_CUT/12)*B.CONS_QNTY) AS REQ_QNTY FROM WO_PRE_COST_FAB_YARN_COST_DTLS B, WO_PO_BREAK_DOWN A WHERE B.JOB_NO = A.JOB_NO_MST AND B.JOB_NO IN(".$job_no.") AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 GROUP BY B.JOB_NO, B.CONS_QNTY, B.COUNT_ID, B.COPM_ONE_ID, B.PERCENT_ONE";
	//echo $sql;
	$sql_rslt = sql_select($sql);

	$sql_allocation = "SELECT A.ID, A.QNTY, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND FROM INV_MATERIAL_ALLOCATION_MST A, PRODUCT_DETAILS_MASTER B WHERE A.ITEM_ID = B.ID AND A.JOB_NO IN(".$job_no.") AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0";
	//echo $sql_allocation;
	$sql_allocation_rslt = sql_select($sql_allocation);
	$allocation_data = array();
	$duplicate_check = array();
	foreach($sql_allocation_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			//for composition
			$arr = array();
			$arr['yarn_comp_type1st'] = $row['YARN_COMP_TYPE1ST'];
			$arr['yarn_comp_percent1st'] = $row['YARN_COMP_PERCENT1ST'];
			$arr['yarn_comp_type2nd'] = $row['YARN_COMP_TYPE2ND'];
			$arr['yarn_comp_percent2nd'] = $row['YARN_COMP_PERCENT2ND'];
			$composition_ = get_composition($arr);
			//end for composition

			$allocation_data[$row['YARN_COUNT_ID']][$composition_]['qty'] += $row['QNTY'];
		}
	}
	?>
	<div align="center">
		<table width="340" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
            	<tr>
                    <th width="30">SL</th>
                    <th width="60">Count</th>
                    <th width="150">Composition</th>
                    <th width="100">Required Qty</th>
                </tr>
			</thead>
		</table>
		<div style="width:357px; max-height:300px" id="scroll_body">
			<table width="340" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            	<thead>
                	<tr><th colspan="4">Pre-Costing Yarn Details</th></tr>
                </thead>
            	<tbody>
                <?
				$sl = 0;
                foreach($sql_rslt as $row)
				{
					$sl++;
					if ($sl % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					/*$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					if($row['copm_two_id'] !=0)
					{
						$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
					}
					$yarn_des.=$color_library[$row[csf('color')]]." ";
					$yarn_des.=$yarn_type[$row[csf('type_id')]];*/
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" height="25" valign="middle">
                    	<td width="30" align="center"><? echo $sl; ?></td>
                    	<td width="60"><? echo $yarn_count_arr[$row['COUNT_ID']]; ?></td>
                    	<td width="150"><? echo $composition[$row['COPM_ONE_ID']].' '.$row['PERCENT_ONE'].'%'; ?></td>
                    	<td width="100" align="right"><? echo decimal_format($row['REQ_QNTY'], '1', ','); ?></td>
                    </tr>
                    <?
					$total_qty += decimal_format($row['REQ_QNTY'], '1', '');
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="3">Total</th>
                        <th width="100" align="right"><? echo decimal_format($total_qty, '1', ','); ?></th>
                    </tr>
                </tfoot>
			</table>

            <table width="340" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" style="margin-top:10px;">
            	<thead>
                	<tr><th colspan="4">Allocation Yarn Details</th></tr>
                </thead>
            	<tbody>
                <?
				$sl = 0;
                foreach($allocation_data as $k_count=>$v_count)
				{
					foreach($v_count as $compo=>$row)
					{
						$sl++;
						if ($sl % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" height="25" valign="middle">
							<td width="30" align="center"><? echo $sl; ?></td>
							<td width="60"><? echo $yarn_count_arr[$k_count]; ?></td>
							<td width="150"><? echo $compo; ?></td>
							<td width="100" align="right"><? echo decimal_format($row['qty'], '1', ','); ?></td>
						</tr>
						<?
						$total_allo_qty += decimal_format($row['qty'], '1', '');
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="3">Total</th>
                        <th width="100" align="right"><? echo decimal_format($total_allo_qty, '1', ','); ?></th>
                    </tr>
                	<tr>
                    	<th colspan="3">Allocation Balance</th>
                        <th width="100" align="right"><? echo decimal_format(($total_qty-$total_allo_qty), '1', ','); ?></th>
                    </tr>
                </tfoot>
			</table>
		</div>
	</div>
	<?
	exit();
}

if ($action == "report_generate_30012022_b4_booking_link")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$cbo_allocation_type = str_replace("'", "", $cbo_allocation_type);
	$report_type = str_replace("'", "", $report_type);
	$companyID = str_replace("'", "", $cbo_company_name);
	$buyerId = str_replace("'", "", $cbo_buyer_name);
	$internalRefNo = str_replace("'", "", $txt_internal_ref_no);


	if($companyID>0) $company_cond="and a.company_id=$companyID"; else $company_cond="";
	if($companyID>0) $company_cond_2="and a.company_name=$companyID"; else $company_cond_2="";

	if($buyerId>0)
	{
		$buyer_cond="and a.buyer_id = ".$buyerId."";
		$buyer_cond2="and b.buyer_id = ".$buyerId."";
		$buyer_cond3="and c.buyer_id = ".$buyerId."";
		$buyer_cond4="and e.buyer_name = ".$buyerId."";
	}
	else
	{
		$buyer_cond="";
		$buyer_cond2="";
		$buyer_cond3="";
	}

	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "")
	{

		$jobArr = explode("*",str_replace("'", "", trim($txt_job_no)));

		foreach($jobArr as $job)
		{
			$job_nos .= "'".$job."',";
		}
		$job_nos = chop($job_nos,",");
		$job_cond .= " and b.job_no in ( $job_nos )" ;

	}
	if ($internalRefNo!="") {$internalRefNo_cond="and c.grouping='$internalRefNo'";}else{$internalRefNo_cond="";}
	$booking_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = " and a.booking_no = $txt_booking_no";
	}

	$booking_date_cond = "";
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") {
		$booking_date_cond = " and a.booking_date between " . trim($txt_date_from) . " and " . trim($txt_date_to) . "";
	}

	//for program no
	$txt_program_no = str_replace("'", "", $txt_program_no );
	if($txt_program_no != '')
	{
        $sqlProgram = "select a.booking_no as BOOKING_NO from ppl_planning_entry_plan_dtls a where a.status_active = 1 and a.is_deleted = 0 and a.is_sales !=1 and a.dtls_id in(".$txt_program_no.") ".$booking_cond." group by a.booking_no";
		$sqlProgramRslt = sql_select($sqlProgram);
		$bookingNoArr = array();
		foreach($sqlProgramRslt as $row)
		{
			$bookingNoArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
		}

		$booking_cond = " and a.booking_no in('".implode("','",$bookingNoArr)."')";
	}

	if ($report_type == 1)
	{
		// Main SQL ====>
		$main_sql = "select a.id, a.company_id,a.booking_no,a.booking_type, a.is_short,b.job_no,a.buyer_id, b.po_break_down_id,c.grouping, c.po_number, b.grey_fab_qnty as qnty
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and a.item_category=2 and a.fabric_source=1
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,4)
			and b.grey_fab_qnty>0 $job_cond $booking_cond $booking_date_cond $company_cond $internalRefNo_cond $buyer_cond
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

			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po'] .= $row[csf("po_number")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['po_id'] .= $row[csf("po_break_down_id")].",";
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['qnty'] += $row[csf("qnty")];
			$data_array[$row[csf("job_no")]][$row[csf("booking_no")]]['buyer_id'] = $row[csf("buyer_id")];

			$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
			$booking_nos_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$all_po_ids_for_buyer_issue .= $row[csf("po_break_down_id")].",";

			if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==2)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Main";
			}
			else if($row[csf("booking_type")] ==1 && $row[csf("is_short")] ==1)
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Short";
			}
			else if($row[csf("booking_type")] ==4 )
			{
				$booking_type_arr[$row[csf("booking_no")]] = "Sample";
			}
		}
		unset($result);

		$job_numbers="'".implode("','",$job_no_arr)."'";
		$booking_nos="'".implode("','",$booking_nos_arr)."'";

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		$job_number_arr = array_unique(explode(",",chop($job_numbers,",")));
		$booking_nos = implode(",",array_unique(explode(",",chop($booking_nos,","))));
		$booking_nos_arr = array_unique(explode(",",chop($booking_nos,",")));

		$alloc_booking_cond = "";$requisition_booking_cond="";$issue_booking_cond="";$production_booking_cond="";

		if($booking_nos != "")
		{
			if ($db_type == 0)
			{
				$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
				$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
				$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
				$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
			}
			else
			{
				if (count($booking_nos_arr) > 1000)
				{
					$alloc_booking_cond = " and (";
					$booking_nos_arr = array_chunk($booking_nos_arr, 1000);
					$z = 0;
					foreach ($booking_nos_arr as $booking_no)
					{
						$booking_no = implode(",", $booking_no);
						if ($z == 0)
						{
							$alloc_booking_cond .= " a.booking_no in(" . $booking_no . ")";
							$requisition_booking_cond .= " c.booking_no in(" . $booking_no . ")";
							$issue_booking_cond .= " d.booking_no in(" . $booking_no . ")";
							$production_booking_cond .= " d.booking_no in(" . $booking_no . ")";
						}
						else
						{
							$alloc_booking_cond .= " or a.booking_no in(" . $booking_no . ")";
							$requisition_booking_cond .= " or c.booking_no in(" . $booking_no . ")";
							$issue_booking_cond .= " or d.booking_no in(" . $booking_no . ")";
							$production_booking_cond .= " or d.booking_no in(" . $booking_no . ")";
						}
						$z++;
					}
					$alloc_booking_cond .= ")";
					$requisition_booking_cond .= ")";
					$issue_booking_cond .= ")";
					$production_booking_cond .= ")";
				}
				else
				{
					$alloc_booking_cond = " and a.booking_no in(" . $booking_nos . ")";
					$requisition_booking_cond = " and c.booking_no in(" . $booking_nos . ")";
					$issue_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
					$production_booking_cond = " and d.booking_no in(" . $booking_nos . ")";
				}
			}
		}


		$alloc_job_cond = "";$program_job_cond="";$requistion_job_cond="";
		if($job_numbers != "")
		{
			if ($db_type == 0)
			{
				$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
				$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
				$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
				$issue_job_cond .= " and d.job_no in ( $job_numbers )" ;
				$production_job_cond .= " and d.job_no in ( $job_numbers )" ;
			}
			else
			{
				if (count($job_number_arr) > 1000)
				{
					$alloc_job_cond = " and (";
					$job_number_arr = array_chunk($job_number_arr, 1000);
					$z = 0;
					foreach ($job_number_arr as $job_number )
					{
						$job_number = implode(",", $job_number);
						if ($z == 0)
						{
							$alloc_job_cond .= " a.job_no in(" . $job_number . ")";
							$program_job_cond .= " c.job_no in(" . $job_number . ")";
							$requistion_job_cond .= " d.job_no in(" . $job_number . ")";
							$issue_job_cond .= " d.job_no in(" . $job_number . ")";
							$production_job_cond .= " d.job_no in(" . $job_number . ")";
						}
						else
						{
							$alloc_job_cond .= " or a.job_no in(" . $job_number . ")";
							$program_job_cond .= " or c.job_no in(" . $job_number . ")";
							$requistion_job_cond .= " or d.job_no in(" . $job_number . ")";
							$issue_job_cond .= " or d.job_no in(" . $job_number . ")";
							$production_job_cond .= " or d.job_no in(" . $job_number . ")";
						}
						$z++;
					}
					$alloc_job_cond .= ")";
					$program_job_cond .= ")";
					$requistion_job_cond .= ")";
					$issue_job_cond .= ")";
					$production_job_cond .= ")";
				}
				else
				{
					$alloc_job_cond = " and a.job_no in(" . $job_numbers . ")";
					$program_job_cond = " and c.job_no in(" . $job_numbers . ")";
					$requistion_job_cond = " and d.job_no in(" . $job_numbers . ")";
					$issue_job_cond = " and d.job_no in(" . $job_numbers . ")";
					$production_job_cond = " and d.job_no in(" . $job_numbers . ")";
				}
			}
		}

        //allocation Qnty
        $allocation_sql ="select a.job_no, a.booking_no, a.qnty, a.po_break_down_id,b.buyer_id
			from inv_material_allocation_mst a,wo_booking_mst b
			where a.booking_no=b.booking_no $alloc_job_cond $alloc_booking_cond $buyer_cond2 and b.company_id=$companyID
			and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";

		$allocation_sql_resutl = sql_select($allocation_sql);

		$allocation_data = array();
		foreach($allocation_sql_resutl as $row)
		{
			$allocation_data[$row[csf("job_no")]][$row[csf("booking_no")]]['alloc_qnty'] += $row[csf("qnty")];
			$allocation_data[$row[csf("buyer_id")]]['alloc_buyer_qnty'] += $row[csf("qnty")];
		}
		unset($allocation_sql_resutl);

        //Program Qnty
        $progQnty_sql = "select a.booking_no,a.buyer_id,
			b.program_qnty as program_qnty, b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond $buyer_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, b.id,b.program_qnty order by b.id";
        /* $progQnty_sql = "select a.booking_no,a.buyer_id,
			a.program_qnty as program_qnty, a.id,b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no $alloc_booking_cond $program_job_cond $company_cond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 group by a.booking_no,a.buyer_id, a.id,b.id,a.program_qnty order by b.id";*/
		$progQntyResult = sql_select($progQnty_sql);

		$progQntyArr =$progNoArr= array(); $programIdQntyChk = array();

		foreach($progQntyResult as $row)
		{
			if($programIdQntyChk[$row[csf("id")]] == "")
			{
				$programIdQntyChk[$row[csf("id")]] = $row[csf("id")];
				$progQntyArr[$row[csf("booking_no")]]['book_prog_qnty'] += $row[csf("program_qnty")];
				$progNoArr[$row[csf("booking_no")]][] = $row[csf("program_id")];
				$progQntyArr[$row[csf("buyer_id")]]['buyer_prog_qnty'] += $row[csf("program_qnty")];
			}
		}
		unset($progQntyResult);

        //Requisition Qnty

		/*$reqs_sql = "select a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b,ppl_planning_info_entry_mst c, wo_booking_dtls d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond and c.company_id=$companyID
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";*/

$reqs_sql = "select a.yarn_qnty as yarn_req_qnty, a.requisition_no,c.booking_no, b.id as program_no,a.id, c.buyer_id
			from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c, wo_booking_mst d
			where a.knit_id = b.id and b.mst_id = c.id and c.booking_no = d.booking_no
			and b.is_sales != 1 $requisition_booking_cond $requistion_job_cond $buyer_cond3 and c.company_id=$companyID
			and a.yarn_qnty>0
			and a.status_active = 1 and a.is_deleted = 0
			and b.status_active = 1 and b.is_deleted = 0
			and c.status_active = 1 and c.is_deleted = 0
			and d.status_active = 1 and d.is_deleted = 0";

		$reqs_sql_result = sql_select($reqs_sql);

		$requIdCheckArr = array();
		$reqs_array = array();
		foreach ($reqs_sql_result as $row)
		{
			if($requIdCheckArr[$row[csf('id')]] == "")
			{
				$requIdCheckArr[$row[csf('id')]] = $row[csf('id')];
				$reqs_array[$row[csf('booking_no')]]['qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_array[$row[csf('booking_no')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$reqs_array[$row[csf('booking_no')]]['program_no'] .= $row[csf('program_no')].",";
				$reqs_array[$row[csf('buyer_id')]]['buyer_req_qnty'] += $row[csf('yarn_req_qnty')];
				$reqs_buyer_array[$row[csf('buyer_id')]]['requ_no'] .= $row[csf('requisition_no')].",";
				$program_buyer_array[$row[csf('buyer_id')]]['program_no'] .= $row[csf('program_no')].",";
			}
		}
		unset($reqs_sql_result);

		$product_sql = "select a.booking_id, c.quantity as knitting_qnty,c.returnable_qnty as return_qnty, c.id, d.booking_no , d.job_no,a.buyer_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c , wo_booking_dtls d
			where a.id=b.mst_id and c.dtls_id=b.id $production_job_cond $production_booking_cond $company_cond $buyer_cond
			and c.po_breakdown_id = d.po_break_down_id
			and a.item_category=13 and a.entry_form=2 and c.entry_form=2
			and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active  = 1 and c.is_deleted = 0
			and a.status_active = 1 and a.is_deleted = 0";
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
				}
			}
		}
		unset($productionArr);
		// echo "<pre>";
		// print_r($nprog);
		//print_r($productionQntyArr_buy);
		$all_po_ids_for_buyer_issue;

		$all_po_ids=chop($all_po_ids_for_buyer_issue,',');
		$all_po_ids_arr = array_filter(array_unique(explode(",",$all_po_ids)));

		if($db_type==2 && count($all_po_ids_arr)>1000)
		{
			$po_ids_cond=" and (";
			$all_po_ids_chunk_arr=array_chunk($all_po_ids_arr,999);
			foreach($all_po_ids_chunk_arr as $ids)
			{
				$po_ids_cond.=" b.po_breakdown_id in($ids) or";
			}
			$po_ids_cond=chop($po_ids_cond,'or ');
			$po_ids_cond.=")";
		}
		else
		{
			$all_poids=implode(",",$all_po_ids_arr);
			$po_ids_cond=" and b.po_breakdown_id in($all_poids)";
		}

		$sql_issue = "select (b.quantity) as issue_qnty, e.buyer_name, d.requisition_no
		from order_wise_pro_details b, inv_transaction d, wo_po_break_down c, wo_po_details_master e
		where d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.trans_type=2 and b.entry_form=3 and b.status_active=1 and d.receive_basis = 3 and b.is_deleted=0 and d.status_active= 1 and b.issue_purpose not in (2,8) and d.company_id=$companyID $po_ids_cond $buyer_cond4";

		$sql_issue_data = sql_select( $sql_issue );

		$issue_qty_arr=array();
		foreach ($sql_issue_data as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]]['issue_qnty'] += $row[csf('issue_qnty')];
			$issue_qty_summary_arr[$row[csf('buyer_name')]]['issue_qnty'] += $row[csf('issue_qnty')];
		}
		unset($sql_issue_data);

		$sql_return = "select (b.quantity) as returned_qnty, b.reject_qty as cons_reject_qnty, e.buyer_name, a.booking_no as requ_no from inv_receive_master a, order_wise_pro_details b, inv_transaction d,wo_po_break_down c, wo_po_details_master e where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id and c.job_no_mst = e.job_no and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 $po_ids_cond $company_cond $buyer_cond4";
		$sql_return_data = sql_select($sql_return);

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$return_qty_arr[$row[csf('requ_no')]]['returned_qnty'] += $row[csf('returned_qnty')];
			$return_qty_summary_arr[$row[csf('buyer_name')]]['returned_qnty'] += $row[csf('returned_qnty')];

			$reject_qty_arr[$row[csf('requ_no')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
			$reject_qty_summary_arr[$row[csf('buyer_name')]]['reject_qnty'] += $row[csf('cons_reject_qnty')];
		}
		unset($sql_return_data);

		ob_start();
		?>
		<fieldset style="width:1650px;">
			<table cellpadding="0" cellspacing="0" width="1530">
				<tr>
					<td align="center" width="100%" colspan="17" style="font-size:16px"><strong>Booking and Plan Wise Yarn Issue Monitoring Report</strong></td>
				</tr>
				<tr>
					<td  width="100%" colspan="13" style="font-size:12px"><strong>Summury : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="80">Buyer</th>
					<th width="100">Book Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130"><p>Allocation Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></p></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="120"><p>Issue Balance<br><small style="font-size: 8px;">(Knit Program - Yarn Issue + issue return)</small></p></th>
					<th width="90">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:1500px; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$z = 1;
						foreach($data_summury as $buyer_id =>$row)
						{
							if ($z % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							$alloc_buyer_qnty = $allocation_data[$buyer_id]["alloc_buyer_qnty"];
							$alloc_buyer_qnty_balance = $row["qnty"] - $alloc_buyer_qnty;
							$buyer_prog_qnty = $progQntyArr[$buyer_id]['buyer_prog_qnty'];
							$buyer_prog_qnty_balance = $row["qnty"] - $buyer_prog_qnty;
							//$buyer_prog_qnty_balance = number_format($buyer_prog_qnty_balance,2,".","");
							$buyer_req_qnty = $reqs_array[$buyer_id]['buyer_req_qnty'];
							$buyer_req_qnty_balance = $alloc_buyer_qnty-$buyer_req_qnty;

							//$buyer_issue_qnty = $issue_qty_summary_arr[$buyer_id]['issue_qnty'];
							//$buyer_issue_ret_qnty = $return_qty_summary_arr[$buyer_id]['returned_qnty'];
							//$buyer_reject_qnty = $reject_qty_summary_arr[$buyer_id]['reject_qnty'];
							$buyer_issue_qnty = 0; $buyer_issue_ret_qnty=0; $buyer_reject_qnty = 0;
							$buyer_requ_id_arr = array_unique(explode(",",chop($reqs_buyer_array[$buyer_id]['requ_no'],",")));
							foreach($buyer_requ_id_arr as $requ_id)
							{
								$buyer_issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
								$buyer_issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
								$buyer_reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
							}

							$buyer_program_nos =array_unique(explode(",",chop($program_buyer_array[$buyer_id]['program_no'],",")));
							$buyer_production_qnty=$buyer_return_qnty=0;
							foreach($buyer_program_nos as $program_id)
							{
								$buyer_production_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
								$buyer_return_qnty += $productionQntyArr[$program_id]["return_qnty"];
							}

							$buyer_issue_qnty_balance = $buyer_prog_qnty-$buyer_issue_qnty + $buyer_issue_ret_qnty;
							//$buyer_production_qnty = $productionQntyArr_buy[$buyer_id]["buyer_prod_qnty"];
							//$buyer_return_qnty = $productionQntyArr_buy[$buyer_id]["buyer_return_qnty"];
							$buyer_balance_qnty = $buyer_issue_qnty - $buyer_issue_ret_qnty -$buyer_production_qnty- $buyer_return_qnty-$buyer_reject_qnty;
							if((($cbo_allocation_type == 1) && (number_format($alloc_buyer_qnty_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($alloc_buyer_qnty_balance,2) <= 0.00)) || ($cbo_allocation_type == 0))
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_s_<? echo $z; ?>', '<? echo $bgcolor; ?>')" id="tr_s_<? echo $z; ?>">
									<td width="40"><? echo $z;?></td>
									<td width="80" align="right"><? echo $buyer_arr[$buyer_id];?></td>
									<td width="100" align="right"><p><? echo number_format($row["qnty"],2);?></p></td>
									<td width="100" align="right"><p><? echo number_format($alloc_buyer_qnty,2);?></p></td>
									<td width="130" align="right"><p><? echo number_format($alloc_buyer_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_prog_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_req_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_issue_ret_qnty,2);?></p></td>
									<td width="120" align="right"><p><? echo number_format($buyer_issue_qnty_balance,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_production_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_return_qnty,2);?></p></td>
									<td width="90" align="right"><p><? echo number_format($buyer_reject_qnty,2);?></p></td>
									<td width="" align="right"><p><? echo number_format($buyer_balance_qnty,2);?></p></td>
								</tr>
								<?
								$z++;
								$tot_booking += $row["qnty"];
								$tot_alloc_buyer_qnty += $alloc_buyer_qnty;
								$tot_buyer_prog_qnty += $buyer_prog_qnty;
								$tot_buyer_req_qnty += $buyer_req_qnty;
								$tot_buyer_issue_qnty += $buyer_issue_qnty;
								$tot_buyer_issue_ret_qnty += $buyer_issue_ret_qnty;
								$tot_buyer_production_qnty+=$buyer_production_qnty;
								$tot_buyer_return_qnty += $buyer_return_qnty;
								$tot_buyer_reject_qnty += $buyer_reject_qnty;
								$tot_buyer_balance_qnty += $buyer_balance_qnty;
							}
						}
						?>
						<tr style="font-weight:bold;background-color: #e0e0e0">
							<td colspan="2" align="left" width="120">Total:</td>
							<td align="right" width="100"><p><? echo number_format($tot_booking,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($tot_alloc_buyer_qnty,2);?></p></td>
							<td width="130"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_prog_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_req_qnty,2);?></p></td>
							<td width="90"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_issue_ret_qnty,2);?></p></td>
							<td width="120"></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_production_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($tot_buyer_reject_qnty,2);?></p></td>
							<td align="right"><p><? echo number_format($tot_buyer_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br/>
			<table cellpadding="0" cellspacing="0" width="1800">
				<tr>
					<td width="100%" colspan="16" style="font-size:12px"><strong>Details : </strong></td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2090" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Job No.</th>
					<th width="80">Buyer</th>
					<th width="100">Fabric Booking No</th>
					<th width="100">Booking Type</th>
					<th width="100">Internal Ref. No</th>
					<th width="130">PO No</th>
					<th width="100">Booking Qnty</th>
					<th width="100">Allocation Qnty</th>
					<th width="130">Allocation Balance<br><small style="font-size: 8px;">(Book - Allocation)</small></th>
					<th width="90">Knit Program</th>
					<th width="90">Prog. Balance<br><small style="font-size: 8px;">(Book - Program)</small></th>
					<th width="90">Yarn Requisition</th>
					<th width="90">Yarn Req Balance<br><p><small style="font-size: 8px;">(Allocation - Requisition)</small></p></th>
					<th width="90">Yarn Issue</th>
					<th width="90">Yarn Issue Return</th>
					<th width="90">Booking Yarn Balance<br><p><small style="font-size: 8px;">(Booking Qty - Yarn Issue) + (Issue Return + Reject Qty)</small></p></th>
					<th width="120">Issue Balance<br><p><small style="font-size: 8px;">(Knit Program - Yarn Issue) + Issue Return</small></p></th>
					<th width="70">Knit Production</th>
					<th width="90">Fabric Reject</th>
					<th width="90">Yarn Reject Qnty</th>
					<th width="">Balance</th>
				</thead>
			</table>
			<div style="width:2110px; overflow-y:scroll; max-height:330px;" id="scroll_body_dtls">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2090" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						foreach($data_array as $job_no => $job_data)
						{
							$job_row_span =0;
							foreach($job_data as $booking_no => $row)
							{
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								$allocation_balance = $row["qnty"] - $allocation_qnty;
								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$job_row_span++;
								}
							}
							$buyer_row_span_arr[$job_no]=$job_row_span;
						}

						$i=$m=1;
						foreach($data_array as $job_no => $job_data)
						{
							$y=1; $show= false;
							$sub_job_book_qnty=$sub_job_alloc_qnty=$sub_alloc_balance=$sub_prog_Qnty=$sub_prog_balance=$sub_requ_qnty=$sub_requ_balance=$sub_issue_qnty=$sub_issue_ret_qnty=$sub_issue_balance=$sub_prod_qnty=$sub_return_qnty=$sub_reject_qnty=$sub_balance_qnty=0;
							foreach($job_data as $booking_no => $row)
							{
								if ($m % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";

								$job_row_span=$buyer_row_span_arr[$job_no];
								$allocation_qnty = $allocation_data[$job_no][$booking_no]['alloc_qnty'];
								if ($allocation_qnty == 0) $alloc_bgcolor = "background-color:red;";else $alloc_bgcolor= "";
								$allocation_balance = $row["qnty"] - $allocation_qnty;

								if((($cbo_allocation_type == 1) && (number_format($allocation_balance,2) > 0.00)) || ($cbo_allocation_type == 2 && (number_format($allocation_balance,2) <= 0.00 )) || ($cbo_allocation_type == 0))
								{
									$po_ids = implode(",",array_unique(explode(",",chop($row["po_id"],','))));
									$prog_Qnty = $progQntyArr[$booking_no]['book_prog_qnty'];
									$program_ids = implode(", ",array_filter(array_unique($progNoArr[$booking_no])));
									if ($prog_Qnty == 0) $program_bgcolor = "background-color:red;";else $program_bgcolor= "";
									$prog_balance = $row["qnty"] - $prog_Qnty;
									//$prog_balance = number_format($prog_balance,2,".","");
									$requ_qnty = $reqs_array[$booking_no]['qnty'];
									if ($requ_qnty == 0) $requ_bgcolor = "background-color:red;";else $requ_bgcolor= "";
									$requ_balance = $allocation_qnty - $requ_qnty;
									$requ_id_arr = array_unique(explode(",",chop($reqs_array[$booking_no]['requ_no'],",")));

									$issue_qnty = 0; $issue_ret_qnty = 0; $requ_nos = "";$reject_qnty=0;$balance_qnty=0;
									foreach($requ_id_arr as $requ_id)
									{
										$issue_qnty += $issue_qty_arr[$requ_id]['issue_qnty'];
										$issue_ret_qnty += $return_qty_arr[$requ_id]['returned_qnty'];
										if($requ_nos=="") $requ_nos .= $requ_id; else $requ_nos .= ",".$requ_id;

										$reject_qnty += $reject_qty_arr[$requ_id]['reject_qnty'];
									}

									$issue_balance = $prog_Qnty - $issue_qnty + $issue_ret_qnty;
									$program_nos =array_unique(explode(",",chop($reqs_array[$booking_no]['program_no'],",")));
									$prod_qnty=$return_qnty=0;
									foreach($program_nos as $program_id)
									{
										//$progss[$program_id]=$program_id;
										$prod_qnty +=  $productionQntyArr[$program_id]["prod_qnty"];
										$return_qnty += $productionQntyArr[$program_id]["return_qnty"];
									}
									$po_number = implode(",",array_unique(explode(",",chop($row['po'],','))));
									$balance_qnty = $issue_qnty - $issue_ret_qnty - $prod_qnty - $return_qnty - $reject_qnty;

									//for booking yarn balance
									$booking_yarn_balance = (number_format($row['qnty'],2,'.','')-(number_format($issue_qnty,2,'.',''))+(number_format($issue_ret_qnty,2,'.','')+number_format($reject_qnty,2,'.','')));

									//$po_ids = implode(",",array_unique(explode(",",chop($row['po_id'],','))));
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
										<?
										if($y == 1)
										{
											?>
											<td width="40" rowspan="<? echo $job_row_span;?>"><? echo $i;?></td>
											<td width="100" rowspan="<? echo $job_row_span;?>"><p>&nbsp;<? echo $job_no;?></p></td>
											<td width="80" rowspan="<? echo $job_row_span;?>"><? echo $buyer_arr[$row['buyer_id']];?></td>
											<?
										}
										?>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $booking_no;?></p></td>
										<td width="100"><p style="word-break: break-all;">&nbsp;<? echo $booking_type_arr[$booking_no];?></p></td>
										<td width="100"><p style="word-break: break-all; text-align: center;">&nbsp;<?
											$internal_ref_no="";
											foreach(explode(",", $po_ids) as $po_id)
											{
												if($internal_ref_no =="") $internal_ref_no =  $internalRefArr[$po_id]["internalRefNumber"]; else $internal_ref_no .= ",".$internalRefArr[$po_id]["internalRefNumber"];
											}
											echo $internal_ref_no;
											?>
											</p>
										</td>
										<td width="130" style="max-width:130px;"><p style="word-break: break-all;">&nbsp;<? echo $po_number;//chop($row['po'],',');?></p></td>
										<td width="100" align="right"><p><? echo number_format($row['qnty'],2);?></p></td>
										<td width="100" align="right" style="<? echo $alloc_bgcolor;?>"><a href='##' onClick="openmypage_allocation('<? echo $job_no . "_" . $booking_no ?>', 'allocation_popup')"><p><? echo number_format($allocation_qnty,2);?></p></a></td>
										<td width="130" align="right"><p><? echo number_format($allocation_balance,2);?></p></td>
										<td width="90" align="right" style="<? echo $program_bgcolor;?>"><a href='##' onClick="openmypage_program('<? echo $program_ids; ?>', 'program_popup')"><p><? echo number_format($prog_Qnty,2);?></p></a></td>

										<td width="90" align="right" ><p><? echo number_format($prog_balance,2);?></p></td>

										<td width="90" align="right" style="<? echo $requ_bgcolor;?>" title="Requisition no=<? echo $requ_nos;?>"><a href='##' onClick="openmypage_requisition('<? echo $program_ids; ?>', 'requisition_popup')"><p><? echo number_format($requ_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($requ_balance,2);?></p></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_popup')"><p><? echo number_format($issue_qnty,2);?></p></a></td>
										<td width="90" align="right"><a href='##' onClick="openmypage_issue('<? echo $requ_nos."_".$booking_no. "_" . $po_ids. "_" . $job_no; ?>', 'issue_return_popup')"><p><? echo number_format($issue_ret_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($booking_yarn_balance,2);?></p></td>
										<td width="120" align="right"><p><? echo number_format($issue_balance,2);?></p></td>
										<td width="70" align="right" title="<? echo chop($reqs_array[$booking_no]['program_no'],",");?>"><a href='##' onClick="openmypage_production('<? echo $job_no . "_" . $po_ids . "_" . $program_ids  ?>', 'production_popup')"><p><? echo number_format($prod_qnty,2);?></p></a></td>
										<td width="90" align="right"><p><? echo number_format($return_qnty,2);?></p></td>
										<td width="90" align="right"><p><? echo number_format($reject_qnty,2);?></p></td>
										<td width="" align="right"><p><? echo number_format($balance_qnty,2);?></p></td>
									</tr>
									<?
									$m++;$y++;//$job_no
									$sub_job_book_qnty +=  $row['qnty'];
									$sub_job_alloc_qnty += $allocation_qnty;
									$sub_alloc_balance += $allocation_balance;
									$sub_prog_Qnty += $prog_Qnty;
									$sub_prog_balance += $prog_balance;
									$sub_requ_qnty += $requ_qnty;
									$sub_requ_balance +=$requ_balance;
									$sub_issue_qnty += $issue_qnty;
									$sub_issue_ret_qnty += $issue_ret_qnty;
									$sub_issue_balance += $issue_balance;
									$sub_prod_qnty +=$prod_qnty;
									$sub_return_qnty += $return_qnty;
									$sub_reject_qnty += $reject_qnty;
									$sub_balance_qnty += $balance_qnty;
									$sub_booking_yarn_balance += number_format($booking_yarn_balance,2,'.','');
									$show = true;
								}
							}

							$i++;

							if($show == true)
							{
								?>
								<tr style="font-weight: bold;background-color: #e0e0e0">
									<td colspan="3" width="220">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td colspan="2" align="right" width="230">Job Sub Total </td>
									<td align="right" width="100"><p><? echo number_format($sub_job_book_qnty,2);?></p></td>
									<td align="right" width="100"><p><? echo number_format($sub_job_alloc_qnty,2)?></p></td>
									<td align="right" width="130"><p><? echo number_format($sub_alloc_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_Qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_prog_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_requ_balance,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_issue_ret_qnty,2);?></p></td>
									<td align="right" width="90"><p><? echo number_format($sub_booking_yarn_balance,2);?></p></td>
									<td align="right" width="120"><p><? echo number_format($sub_issue_balance,2);?></p></td>
									<td align="right" width="70"><p><? echo number_format($sub_prod_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_return_qnty,2);?></p></td>
									<td align="right"  width="90"><p><? echo number_format($sub_reject_qnty,2);?></p></td>
									<td align="right"  width=""><p><? echo number_format($sub_balance_qnty,2);?></p></td>
								</tr>
								<?
							}
							$grand_job_book_qnty +=  $sub_job_book_qnty;
							$grand_job_alloc_qnty += $sub_job_alloc_qnty;
							$grand_alloc_balance += $sub_alloc_balance;
							$grand_prog_Qnty += $sub_prog_Qnty;
							$grand_prog_balance += $sub_prog_balance;
							$grand_requ_qnty += $sub_requ_qnty;
							$grand_requ_balance +=$sub_requ_balance;
							$grand_issue_qnty += $sub_issue_qnty;
							$grand_issue_ret_qnty += $sub_issue_ret_qnty;
							$grand_issue_balance += $sub_issue_balance;
							$grand_prod_qnty +=$sub_prod_qnty;
							$grand_return_qnty += $sub_return_qnty;
							$grand_reject_qnty += $sub_reject_qnty;
							$grand_balance_qnty += $sub_balance_qnty;
							$grand_booking_yarn_balance += number_format($sub_booking_yarn_balance,2,'.','');
						}

						?>
						<tr style="font-weight: bold;background-color: #ccc;border-top: 2px solid">
							<td colspan="3" width="220">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td colspan="2" align="right" width="230">Job Grand Total </td>
							<td align="right" width="100"><p><? echo number_format($grand_job_book_qnty,2);?></p></td>
							<td align="right" width="100"><p><? echo number_format($grand_job_alloc_qnty,2)?></p></td>
							<td align="right" width="130"><p><? echo number_format($grand_alloc_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_Qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_prog_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_requ_balance,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_issue_ret_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_booking_yarn_balance,2);?></p></td>
							<td align="right" width="120"><p><? echo number_format($grand_issue_balance,2);?></p></td>
							<td align="right" width="70"><p><? echo number_format($grand_prod_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_return_qnty,2);?></p></td>
							<td align="right" width="90"><p><? echo number_format($grand_reject_qnty,2);?></p></td>
							<td align="right" width=""><p><? echo number_format($grand_balance_qnty,2);?></p></td>
						</tr>
					</tbody>
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

if ($action == "allocation_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$data = explode("_", $data);
	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$supplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
	$color = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	$job_nos = explode(",", $data[0]);
	$jobs = "";
	foreach ($job_nos as $job) {
		$jobs .= "'" . $job . "',";
	}
	$jobs = rtrim($jobs, ",");

	if ($db_type == 0)
	{
		$po_sql = sql_select("select distinct a.po_number,a.grouping,a.file_no,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where b.id=c.mst_id and a.id=c.po_break_down_id and b.job_no in($jobs) and b.booking_no='$data[1]' and  FIND_IN_SET(a.id, b.po_break_down_id)");
	}
	else
	{
		$po_sql = sql_select("select a.po_number,a.grouping,a.file_no,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where b.id=c.mst_id and b.job_no in($jobs) and b.booking_no='$data[1]' and a.id=c.po_break_down_id group by b.id, a.po_number,a.grouping,a.file_no");
	}

	$po_num_array = array();
	$po_data_arr = array();
	foreach ($po_sql as $row)
	{
		$po_data_arr[$row[csf('po_number')]]['ref'] = $row[csf('grouping')];
		$po_data_arr[$row[csf('po_number')]]['file'] = $row[csf('file_no')];

		if (array_key_exists($row[csf('id')], $po_num_array)) {
			$po_num_array[$row[csf('id')]] = $po_num_array[$row[csf('id')]] . "," . $row[csf('po_number')];
		}
		else
		{
			$po_num_array[$row[csf('id')]] = $row[csf('po_number')];
		}
	}

	//for product info
	$prod_data_arr = array();
	$prod_data = sql_select("select id, product_name_details, supplier_id, lot from product_details_master where item_category_id=1 and id in(select a.item_id from inv_material_allocation_mst a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in(".$jobs.") and a.booking_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0)");
	foreach ($prod_data as $row)
	{
		$prod_data_arr[$row[csf('id')]]['prod_details'] = $row[csf('product_name_details')];
		$prod_data_arr[$row[csf('id')]]['supp'] = $row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
	}

	//main query
	$grey_yarn_sql = "select a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_name,b.buyer_name,b.location_name from inv_material_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and a.job_no in($jobs) and a.booking_no='$data[1]' and a.is_dyied_yarn=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $grey_yarn_sql;
	$grey_yarn_result = sql_select($grey_yarn_sql);

	$dyed_yarn_sql = "select a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_name,b.buyer_name,b.location_name from inv_material_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and a.job_no in($jobs) and a.booking_no='$data[1]' and a.is_dyied_yarn=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $dyed_yarn_sql;
	$dyed_yarn_result = sql_select($dyed_yarn_sql);

	?>
    <script>
		function func_order_wise_allocation_qty(data)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action=actn_order_wise_allocation_qty', 'Allocation Details', 'width=550px, height=350px,center=1,resize=0,scrolling=0','../../');
		}
	</script>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="60">Company</th>
			<th width="60">Buyer</th>
			<th width="70">Supplier</th>
			<th width="90">Internal Ref</th>
			<th width="90">File No</th>
			<th width="110">Order No</th>
			<th width="130">Allocated Yarn</th>
			<th width="70">Lot</th>
			<th>Qnty</th>
		</thead>
	</table>
	<div style="width:898px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$qntyGrndTotal=0;
		foreach ($grey_yarn_result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$po_no = array_unique(explode(",", $po_num_array[$row[csf('id')]]));

			$ref_cond = '';
			$file_cond = '';
			foreach ($po_no as $row_data) {
				if ($ref_cond == "") $ref_cond = $po_data_arr[$row_data]['ref']; else $ref_cond .= "," . $po_data_arr[$row_data]['ref'];
				if ($file_cond == "") $file_cond = $po_data_arr[$row_data]['file']; else $file_cond .= "," . $po_data_arr[$row_data]['file'];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_material_allocation_data','requires/yarn_allocation_controller');remove_qnty_popup('txt_qnty',0,0);">
				<td width="30"><? echo $i; ?></td>
				<td width="50"><p><? echo $row[csf('sid')]; ?></p></td>
				<td width="60"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>
				<td width="60"><p><? echo $buyer[$row[csf('buyer_name')]]; ?></p></td>
				<td width="70"><p><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $ref_cond))); ?>&nbsp;</p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $file_cond))); ?>&nbsp;</p></td>
				<td width="110"><p><? echo $po_num_array[$row[csf('id')]]; ?></p></td>
				<td width="130"><p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
				<td width="70"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
				<td align="right"><a href='##' onClick="func_order_wise_allocation_qty('<? echo $row[csf('sid')] ?>')"><p><? echo number_format($row[csf('qnty')], 2);?></p></a>&nbsp;</td>
			</tr>
			<?
			$qntyGrndTotal+=$row[csf('qnty')];
			$i++;
		}
		?>
		<tr style="background-color:#CCCCCC;">
			<td colspan="10" align="right"><strong>Total</strong></td>
			<td align="right"><strong><? echo number_format($qntyGrndTotal,2); ?> </strong></td>
		</tr>
	</table>
	</div>

	<br>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="60">Company</th>
			<th width="60">Buyer</th>
			<th width="70">Supplier</th>
			<th width="90">Internal Ref</th>
			<th width="90">File No</th>
			<th width="110">Order No</th>
			<th width="130">Allocated Yarn</th>
			<th width="70">Lot</th>
			<th>Qnty</th>
		</thead>
	</table>
	<div style="width:898px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$qntyGrndTotal=0;
		foreach ($dyed_yarn_result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$po_no = array_unique(explode(",", $po_num_array[$row[csf('id')]]));

			$ref_cond = '';
			$file_cond = '';
			foreach ($po_no as $row_data) {
				if ($ref_cond == "") $ref_cond = $po_data_arr[$row_data]['ref']; else $ref_cond .= "," . $po_data_arr[$row_data]['ref'];
				if ($file_cond == "") $file_cond = $po_data_arr[$row_data]['file']; else $file_cond .= "," . $po_data_arr[$row_data]['file'];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_material_allocation_data','requires/yarn_allocation_controller');remove_qnty_popup('txt_qnty',0,0);">
				<td width="30"><? echo $i; ?></td>
				<td width="50"><p><? echo $row[csf('sid')]; ?></p></td>
				<td width="60"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>
				<td width="60"><p><? echo $buyer[$row[csf('buyer_name')]]; ?></p></td>
				<td width="70"><p><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $ref_cond))); ?>&nbsp;</p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $file_cond))); ?>&nbsp;</p></td>
				<td width="110"><p><? echo $po_num_array[$row[csf('id')]]; ?></p></td>
				<td width="130"><p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
				<td width="70"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
				<td align="right"><a href='##' onClick="func_order_wise_allocation_qty('<? echo $row[csf('sid')] ?>')"><p><? echo number_format($row[csf('qnty')], 2);?></p></a>&nbsp;</td>
			</tr>
			<?
			$qntyGrndTotal+=$row[csf('qnty')];
			$i++;
		}
		?>
		<tr style="background-color:#CCCCCC;">
			<td colspan="10" align="right"><strong>Total</strong></td>
			<td align="right"><strong><? echo number_format($qntyGrndTotal,2); ?> </strong></td>
		</tr>
	</table>
	</div>

	<?
	exit();
}

if ($action == "allocation_sales_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$data = explode("_", $data);
	$comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$supplier = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
	$color = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	$job_nos = explode(",", $data[0]);
	$jobs = "";
	foreach ($job_nos as $job) {
		$jobs .= "'" . $job . "',";
	}
	$jobs = rtrim($jobs, ",");

	if ($db_type == 0)
	{
		$po_sql = sql_select("select distinct a.po_number,a.grouping,a.file_no,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where b.id=c.mst_id and a.id=c.po_break_down_id and b.job_no in($jobs) and b.booking_no='$data[1]' and  FIND_IN_SET(a.id, b.po_break_down_id)");
	}
	else
	{
		$po_sql = sql_select("select a.po_number,a.grouping,a.file_no,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where b.id=c.mst_id and b.job_no in($jobs) and b.booking_no='$data[1]' and a.id=c.po_break_down_id group by b.id, a.po_number,a.grouping,a.file_no");
	}

	$po_num_array = array();
	$po_data_arr = array();
	foreach ($po_sql as $row)
	{
		$po_data_arr[$row[csf('po_number')]]['ref'] = $row[csf('grouping')];
		$po_data_arr[$row[csf('po_number')]]['file'] = $row[csf('file_no')];

		if (array_key_exists($row[csf('id')], $po_num_array)) {
			$po_num_array[$row[csf('id')]] = $po_num_array[$row[csf('id')]] . "," . $row[csf('po_number')];
		}
		else
		{
			$po_num_array[$row[csf('id')]] = $row[csf('po_number')];
		}
	}

	//for product info
	$prod_data_arr = array();
	$prod_data = sql_select("select id, product_name_details, supplier_id, lot from product_details_master where item_category_id=1 and id in(select a.item_id from inv_material_allocation_mst a, fabric_sales_order_mst b where a.job_no=b.job_no and a.job_no in(".$jobs.") and a.booking_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0)");
	foreach ($prod_data as $row)
	{
		$prod_data_arr[$row[csf('id')]]['prod_details'] = $row[csf('product_name_details')];
		$prod_data_arr[$row[csf('id')]]['supp'] = $row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
	}

	//main query
	$grey_yarn_sql = "SELECT a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_id,b.po_buyer,b.location_id  from inv_material_allocation_mst a,fabric_sales_order_mst b where a.job_no=b.job_no and a.job_no in($jobs) and a.booking_no='$data[1]' and a.is_dyied_yarn=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $grey_yarn_sql;
	$grey_yarn_result = sql_select($grey_yarn_sql);

	$dyed_yarn_sql = "SELECT a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_id,b.po_buyer,b.location_id  from inv_material_allocation_mst a,fabric_sales_order_mst b where a.job_no=b.job_no and a.job_no in($jobs) and a.booking_no='$data[1]' and a.is_dyied_yarn=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $dyed_yarn_sql;
	$dyed_yarn_result = sql_select($dyed_yarn_sql);

	?>
    <script>
		function func_order_wise_allocation_qty(data)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action=actn_order_wise_allocation_qty', 'Allocation Details', 'width=550px, height=350px,center=1,resize=0,scrolling=0','../../');
		}
	</script>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="60">Company</th>
			<th width="60">Buyer</th>
			<th width="70">Supplier</th>
			<th width="90">Internal Ref</th>
			<th width="90">File No</th>
			<th width="110">Order No</th>
			<th width="130">Allocated Yarn</th>
			<th width="70">Lot</th>
			<th>Qnty</th>
		</thead>
	</table>
	<div style="width:898px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$qntyGrndTotal=0;
		foreach ($grey_yarn_result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$po_no = array_unique(explode(",", $po_num_array[$row[csf('id')]]));

			$ref_cond = '';
			$file_cond = '';
			foreach ($po_no as $row_data) {
				if ($ref_cond == "") $ref_cond = $po_data_arr[$row_data]['ref']; else $ref_cond .= "," . $po_data_arr[$row_data]['ref'];
				if ($file_cond == "") $file_cond = $po_data_arr[$row_data]['file']; else $file_cond .= "," . $po_data_arr[$row_data]['file'];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_material_allocation_data','requires/yarn_allocation_controller');remove_qnty_popup('txt_qnty',0,0);">
				<td width="30"><? echo $i; ?></td>
				<td width="50"><p><? echo $row[csf('sid')]; ?></p></td>
				<td width="60"><p><? echo $comp[$row[csf('company_id')]]; ?></p></td>
				<td width="60"><p><? echo $buyer[$row[csf('po_buyer')]]; ?></p></td>
				<td width="70"><p><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $ref_cond))); ?>&nbsp;</p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $file_cond))); ?>&nbsp;</p></td>
				<td width="110"><p><? echo $po_num_array[$row[csf('id')]]; ?></p></td>
				<td width="130"><p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
				<td width="70"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
				<td align="right"><a href='##' onClick="func_order_wise_allocation_qty('<? echo $row[csf('sid')] ?>')"><p><? echo number_format($row[csf('qnty')], 2);?></p></a>&nbsp;</td>
			</tr>
			<?
			$qntyGrndTotal+=$row[csf('qnty')];
			$i++;
		}
		?>
		<tr style="background-color:#CCCCCC;">
			<td colspan="10" align="right"><strong>Total</strong></td>
			<td align="right"><strong><? echo number_format($qntyGrndTotal,2); ?> </strong></td>
		</tr>
	</table>
	</div>

	<br>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="60">Company</th>
			<th width="60">Buyer</th>
			<th width="70">Supplier</th>
			<th width="90">Internal Ref</th>
			<th width="90">File No</th>
			<th width="110">Order No</th>
			<th width="130">Allocated Yarn</th>
			<th width="70">Lot</th>
			<th>Qnty</th>
		</thead>
	</table>
	<div style="width:898px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$qntyGrndTotal=0;
		foreach ($dyed_yarn_result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$po_no = array_unique(explode(",", $po_num_array[$row[csf('id')]]));

			$ref_cond = '';
			$file_cond = '';
			foreach ($po_no as $row_data) {
				if ($ref_cond == "") $ref_cond = $po_data_arr[$row_data]['ref']; else $ref_cond .= "," . $po_data_arr[$row_data]['ref'];
				if ($file_cond == "") $file_cond = $po_data_arr[$row_data]['file']; else $file_cond .= "," . $po_data_arr[$row_data]['file'];
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_material_allocation_data','requires/yarn_allocation_controller');remove_qnty_popup('txt_qnty',0,0);">
				<td width="30"><? echo $i; ?></td>
				<td width="50"><p><? echo $row[csf('sid')]; ?></p></td>
				<td width="60"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>
				<td width="60"><p><? echo $buyer[$row[csf('buyer_name')]]; ?></p></td>
				<td width="70"><p><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $ref_cond))); ?>&nbsp;</p></td>
				<td width="90"><p><? echo implode(",", array_unique(explode(",", $file_cond))); ?>&nbsp;</p></td>
				<td width="110"><p><? echo $po_num_array[$row[csf('id')]]; ?></p></td>
				<td width="130"><p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
				<td width="70"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
				<td align="right"><a href='##' onClick="func_order_wise_allocation_qty('<? echo $row[csf('sid')] ?>')"><p><? echo number_format($row[csf('qnty')], 2);?></p></a>&nbsp;</td>
			</tr>
			<?
			$qntyGrndTotal+=$row[csf('qnty')];
			$i++;
		}
		?>
		<tr style="background-color:#CCCCCC;">
			<td colspan="10" align="right"><strong>Total</strong></td>
			<td align="right"><strong><? echo number_format($qntyGrndTotal,2); ?> </strong></td>
		</tr>
	</table>
	</div>

	<?
	exit();
}

if ($action == "actn_order_wise_allocation_qty")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql = "SELECT a.job_no AS JOB_NO, a.po_break_down_id AS PO_ID, a.qnty AS QTY, b.po_number AS PO_NUMBER, b.grouping AS INTERNAL_REF, b.file_no AS FILE_NO FROM inv_material_allocation_dtls a, wo_po_break_down b WHERE a.po_break_down_id = b.id AND a.mst_id = '".$data."' AND a.status_active=1 AND a.is_deleted=0";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Job No/Sales No</th>
			<th width="100">Order No</th>
			<th width="100">Internal Ref</th>
			<th width="100">File No</th>
			<th>Qnty</th>
		</thead>
	</table>
	<div style="width:548px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 0;
		$qntyGrndTotal=0;
		foreach ($result as $row)
		{
			$i++;
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><p><? echo $row['JOB_NO']; ?></p></td>
				<td width="100"><p><? echo $row['PO_NUMBER']; ?></p></td>
				<td width="100"><p><? echo $row['INTERNAL_REF']; ?></p></td>
				<td width="100"><p><? echo $row['FILE_NO']; ?></p></td>
				<td align="right"><? echo number_format($row['QTY'], 2);?>&nbsp;</td>
			</tr>
			<?
			$qntyGrndTotal+=$row['QTY'];
		}
		?>
		<tr style="background-color:#CCCCCC;">
			<td colspan="5" align="right"><strong>Total</strong></td>
			<td align="right"><strong><? echo number_format($qntyGrndTotal,2); ?> </strong></td>
		</tr>
	</table>
	</div>
    <?php
	exit();
}

if ($action == "program_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$program_ids = $data;
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$suplier_details = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	$knit_id_array = array();
	$prod_id_array = array();
	$prod_no_array = array();
	$rqsn_array = array();
	$rqsn_no_arr= array();
	$prod_id_arr=array();
	$knit_qty_arr=array();
	$issue_qty_arr=array();
	$issue_return_arr=array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id = b.id and a.knit_id in($program_ids) and b.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.knit_id, a.prod_id, a.requisition_no");

	foreach ($reqsn_dataArray as $row)
	{
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$prod_no_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('requisition_no')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
		$rqsn_no_arr[]=$row[csf('requisition_no')];
		$prod_id_arr[]=$row[csf('prod_id')];
	}
	$requ_no=implode(",",array_unique($rqsn_no_arr));
	$prod_ids=implode(",",array_unique($prod_id_arr));

	$recv_sql = "SELECT a.booking_id, sum(b.grey_receive_qnty) as knit_qty, sum(reject_fabric_receive) as fabric_reject FROM inv_receive_master a, pro_grey_prod_entry_dtls b  where a.id = b.mst_id and a.entry_form=2 and item_category=13 and a.receive_basis=2 and a.booking_id in ($program_ids) and b.status_active=1 and b.is_deleted=0 group by a.booking_id";
	//echo $recv_sql;
	$result = sql_select($recv_sql);
	foreach ($result as $row) {
		$knit_qty_arr[$row[csf('booking_id')]]['knit_qty']=$row[csf('knit_qty')];
		$knit_qty_arr[$row[csf('booking_id')]]['fabric_reject']=$row[csf('fabric_reject')];
	}
	$issue_qty_arr =array();
	if($requ_no !='')
	{
		$issue_sql = "select b.requisition_no,b.prod_id,sum(b.cons_quantity) as issue_qty,sum(b.return_qnty) as return_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=3 and a.item_category=1 and a.issue_basis=3 and b.transaction_type=2 and b.requisition_no in ($requ_no) and b.status_active=1 and b.is_deleted=0 group by b.requisition_no,b.prod_id";

		$result = sql_select($issue_sql);
		foreach ($result as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['issue_qty']=$row[csf('issue_qty')];
			$issue_qty_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['return_qnty']=$row[csf('return_qnty')];
		}
	}

	$issue_return_arr=array();

	if($prod_ids != '')
	{
		$issue_return_sql = "select b.prod_id,a.booking_id,sum(b.cons_quantity) as issu_return_qty, sum(b.cons_reject_qnty) reject_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=9 and a.item_category=1 and a.receive_basis=3 and transaction_type=4 and b.prod_id in ($prod_ids) and b.status_active = 1 and b.is_deleted = 0 group by b.prod_id,a.booking_id";
		$result = sql_select($issue_return_sql);
		foreach ($result as $row) {
			$issue_return_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['issue_return']=$row[csf('issu_return_qty')];
			$issue_return_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['reject_qnty']=$row[csf('reject_qnty')];
		}
	}

	// echo "<pre>";
	// print_r($issue_qty_arr);
	?>
	<fieldset style="width:2095px;">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2085" class="rpt_table" >
        <thead>
			<th width="25">SL</th>
			<th width="60">Program No & Date</th>
			<th width="100">Knitting Company</th>
			<th width="130">Fabrication</th>
			<th width="60">GSM</th>
			<th width="50">F. Dia</th>
			<th width="70">Dia Type</th>
			<th width="55">Floor</th>
			<th width="55">M/c. No</th>
			<th width="60">M/c. Dia & GG</th>
			<th width="110">Color</th>
			<th width="70">Color Range</th>
			<th width="60">S/L</th>
			<th width="60">Spandex S/L</th>
			<th width="60">Feeder</th>
			<th width="80">Knit Start</th>
			<th width="80">Knit End</th>
			<th width="60">Prpgram Qty.</th>
			<th width="60">Knit Qty.</th>
			<th width="60">Balance</th>
			<th width="60">Requisition NO</th>
			<th width="120">Yarn Description</th>
			<th width="60">Lot</th>
			<th width="60">Yarn Qty.(KG)</th>
			<th width="60">Yarn Issue</th>
			<th width="60">Balance</th>
			<th width="60">Returnable</th>
			<th width="60">Issue Return</th>
			<th width="60">Yarn Rej. Qty</th>
			<th width="60">Fabric Rej. Qnty</th>
			<th width="60">Remarks</th>
		</thead>
	</table>
    <div style="width:2105px; overflow-y:scroll; max-height:450px;" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2085" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		$s = 1;
		$tot_program_qnty = 0;
		$tot_knit_qnty = 0;
		$tot_yarn_reqsn_qnty = 0;
		$tot_yarn_issue_qnty = 0;
		$tot_yarn_returnble_qnty = 0;
		$tot_yarn_issue_reqsn_qnty = 0;
		$total_knit_balance = 0;
		$company_id = '';
		$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice, b.knitting_source, b.knitting_party from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_sales!=1";
		//$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and c.dtls_id=b.id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_sales!=1";
		//echo $sql;
		$nameArray = sql_select($sql);
		$advice = "";
		foreach ($nameArray as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			$color = '';
			$color_id = explode(",", $row[csf('color_id')]);
			foreach ($color_id as $val) {
				if ($val > 0) $color .= $color_library[$val] . ",";
			}
			$color = chop($color, ',');

			if ($company_id == '')
				$company_id = $row[csf('company_id')];

			$machine_no = '';
			$machine_id = explode(",", $row[csf('machine_id')]);

			foreach ($machine_id as $val) {
				if ($machine_no == '')
					$machine_no = $machine_arr[$val];
				else
					$machine_no .= "," . $machine_arr[$val];
			}
			if ($machine_id[0] != "") {
				$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
			}

			if ($knit_id_array[$row[csf('program_id')]] != "")
			{
				$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
				$row_span = count($all_prod_id);
				$z = 0;
				foreach ($all_prod_id as $prod_id)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<?
						if ($z == 0)
						{
							?>
							<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
							<td width="60" rowspan="<? echo $row_span; ?>" align="center"  style="font-size:14px;"><b><? echo $row[csf('program_id')]; ?></b><br><p style="font-size:12px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
							<td width="100" align="center" rowspan="<? echo $row_span; ?>"><p><? if ($row[csf('knitting_source')] == 1) echo $company_details[$row[csf('knitting_party')]]; else echo $suplier_details[$row[csf('knitting_party')]]; ?></p></td>
							<td width="130" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
							<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

							<td width="55" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

							<td width="55" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
							<td width="110" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
							<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>


							<td width="80" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
							<td width="80" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
							<td width="60" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
							<td width="60" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($knit_qty_arr[$row[csf('program_id')]]['knit_qty'], 2, '.', ''); ?>&nbsp;</td>
							<td width="60" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')]-$knit_qty_arr[$row[csf('program_id')]]['knit_qty'], 2, '.', ''); ?>&nbsp;</td>
							<?
							$tot_program_qnty += $row[csf('program_qnty')];
							$tot_knit_qnty += $knit_qty_arr[$row[csf('program_id')]]['knit_qty'];
							$total_knit_balance += $row[csf('program_qnty')]-$knit_qty_arr[$row[csf('program_id')]]['knit_qty'];
							$i++;
							$requisition_no=$prod_no_array[$row[csf('program_id')]][$prod_id];
							$tot_fabric_reject += $knit_qty_arr[$row[csf('program_id')]]['fabric_reject'];
						}
						?>
						<td width="60" align="center"><p><? echo chop($requisition_no,","); ?></p></td>

						<td width="120"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
						<td width="60" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
						<td width="60" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_qty_arr[$requisition_no][$prod_id]['issue_qty'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id]-$issue_qty_arr[$requisition_no][$prod_id]['issue_qty'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_qty_arr[$requisition_no][$prod_id]['return_qnty'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_return_arr[$requisition_no][$prod_id]['issue_return'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_return_arr[$requisition_no][$prod_id]['reject_qnty'], 2, '.', ''); ?></td>
						<?
						if ($z == 0)
						{
							?>
							<td  width="60" align="right" rowspan="<? echo $row_span; ?>"><p><? echo number_format($knit_qty_arr[$row[csf('program_id')]]['fabric_reject'], 2, '.', ''); ?>&nbsp;</p></td>
							<td  width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
							<?
						}
						?>
					</tr>
					<?

					$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
					$tot_yarn_issue_qnty += $issue_qty_arr[$requisition_no][$prod_id]['issue_qty'];
					$tot_yarn_returnble_qnty += $issue_qty_arr[$requisition_no][$prod_id]['return_qnty'];
					$tot_yarn_issue_reqsn_qnty += $issue_return_arr[$requisition_no][$prod_id]['issue_return'];
					$tot_yarn_issue_reject_qnty += $issue_return_arr[$requisition_no][$prod_id]['reject_qnty'];
					$tot_yarn_issue_balance += $prod_id_array[$row[csf('program_id')]][$prod_id]-$issue_qty_arr[$requisition_no][$prod_id]['issue_qty'];
					$z++;
				}
			}
			else
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="25"><? echo $i; ?></td>
					<td width="60" align="center" style="font-size:14px;"><b><? echo $row[csf('program_id')]; ?></b><br><p style="font-size:12px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
					<td width="100"><p><? if ($row[csf('knitting_source')] == 1) echo $company_details[$row[csf('knitting_party')]]; else echo $suplier_details[$row[csf('knitting_party')]]; ?></p></td>

					<td width="130"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
					<td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
					<td width="70"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

					<td width="55" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

					<td width="55" align="center"><p><? echo $machine_no; ?></p></td>
					<td width="60"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
					<td width="110"><p><? echo $color; ?></p></td>
					<td width="70"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
					<td width="60"><p><? //echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
					<td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
					<td width="60"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
					<td width="80" rowspan="<? //echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
					<td width="80" rowspan="<? //echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
					<td width="60" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
					<td width="60" align="right"><? //echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
					<td width="60" align="right"><? //echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="120"><p>&nbsp;</p></td>
					<td width="60" align="right">&nbsp;</td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
				</tr>
				<?
				$tot_program_qnty += $row[csf('program_qnty')];
				$i++;
			}
			$advice = $row[csf('advice')];
			$advice = str_replace(array(";","\n"), "<br/>", $advice);
		}
		?>
		<tfoot>
			<th width="25">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="100">&nbsp;</th>
			<th width="130">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="50">&nbsp;</th>
			<th width="70">&nbsp;</th>
			<th width="55">&nbsp;</th>
			<th width="55">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="110">&nbsp;</th>
			<th width="70">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="80">&nbsp;</th>
			<th width="80"><b>Total</b></th>
			<th align="right" width="60"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
			<th align="right" width="60"><? echo number_format($tot_knit_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($total_knit_balance, 2, '.', ''); ?>&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="120">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th align="right" width="60"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
			<th width="60"><? echo number_format($tot_yarn_issue_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_issue_balance, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_returnble_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_issue_reqsn_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_issue_reject_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60" align="right"><? echo number_format($tot_fabric_reject, 2, '.', ''); ?>&nbsp;</th>
			<th>&nbsp;</th>
		</tfoot>
	</table>
	</div>
	</fieldset>
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}
if ($action == "program_sales_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$program_ids = $data;
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$suplier_details = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	$knit_id_array = array();
	$prod_id_array = array();
	$prod_no_array = array();
	$rqsn_array = array();
	$rqsn_no_arr= array();
	$prod_id_arr=array();
	$knit_qty_arr=array();
	$issue_qty_arr=array();
	$issue_return_arr=array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id = b.id and a.knit_id in($program_ids) and b.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.knit_id, a.prod_id, a.requisition_no");

	foreach ($reqsn_dataArray as $row)
	{
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$prod_no_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('requisition_no')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
		$rqsn_no_arr[]=$row[csf('requisition_no')];
		$prod_id_arr[]=$row[csf('prod_id')];
	}
	$requ_no=implode(",",array_unique($rqsn_no_arr));
	$prod_ids=implode(",",array_unique($prod_id_arr));

	$recv_sql = "SELECT a.booking_id, sum(b.grey_receive_qnty) as knit_qty, sum(reject_fabric_receive) as fabric_reject FROM inv_receive_master a, pro_grey_prod_entry_dtls b  where a.id = b.mst_id and a.entry_form=2 and item_category=13 and a.receive_basis=2 and a.booking_id in ($program_ids) and b.status_active=1 and b.is_deleted=0 group by a.booking_id";
	//echo $recv_sql;
	$result = sql_select($recv_sql);
	foreach ($result as $row) {
		$knit_qty_arr[$row[csf('booking_id')]]['knit_qty']=$row[csf('knit_qty')];
		$knit_qty_arr[$row[csf('booking_id')]]['fabric_reject']=$row[csf('fabric_reject')];
	}
	$issue_qty_arr =array();
	if($requ_no !='')
	{
		$issue_sql = "select b.requisition_no,b.prod_id,sum(b.cons_quantity) as issue_qty,sum(b.return_qnty) as return_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=3 and a.item_category=1 and a.issue_basis=3 and b.transaction_type=2 and b.requisition_no in ($requ_no) and b.status_active=1 and b.is_deleted=0 group by b.requisition_no,b.prod_id";

		$result = sql_select($issue_sql);
		foreach ($result as $row) {
			$issue_qty_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['issue_qty']=$row[csf('issue_qty')];
			$issue_qty_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]]['return_qnty']=$row[csf('return_qnty')];
		}
	}

	$issue_return_arr=array();

	if($prod_ids != '')
	{
		$issue_return_sql = "select b.prod_id,a.booking_id,sum(b.cons_quantity) as issu_return_qty, sum(b.cons_reject_qnty) reject_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=9 and a.item_category=1 and a.receive_basis=3 and transaction_type=4 and b.prod_id in ($prod_ids) and b.status_active = 1 and b.is_deleted = 0 group by b.prod_id,a.booking_id";
		$result = sql_select($issue_return_sql);
		foreach ($result as $row) {
			$issue_return_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['issue_return']=$row[csf('issu_return_qty')];
			$issue_return_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['reject_qnty']=$row[csf('reject_qnty')];
		}
	}

	// echo "<pre>";
	// print_r($issue_qty_arr);
	?>
	<fieldset style="width:2095px;">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2085" class="rpt_table" >
        <thead>
			<th width="25">SL</th>
			<th width="60">Program No & Date</th>
			<th width="100">Knitting Company</th>
			<th width="130">Fabrication</th>
			<th width="60">GSM</th>
			<th width="50">F. Dia</th>
			<th width="70">Dia Type</th>
			<th width="55">Floor</th>
			<th width="55">M/c. No</th>
			<th width="60">M/c. Dia & GG</th>
			<th width="110">Color</th>
			<th width="70">Color Range</th>
			<th width="60">S/L</th>
			<th width="60">Spandex S/L</th>
			<th width="60">Feeder</th>
			<th width="80">Knit Start</th>
			<th width="80">Knit End</th>
			<th width="60">Prpgram Qty.</th>
			<th width="60">Knit Qty.</th>
			<th width="60">Balance</th>
			<th width="60">Requisition NO</th>
			<th width="120">Yarn Description</th>
			<th width="60">Lot</th>
			<th width="60">Yarn Qty.(KG)</th>
			<th width="60">Yarn Issue</th>
			<th width="60">Balance</th>
			<th width="60">Returnable</th>
			<th width="60">Issue Return</th>
			<th width="60">Yarn Rej. Qty</th>
			<th width="60">Fabric Rej. Qnty</th>
			<th width="60">Remarks</th>
		</thead>
	</table>
    <div style="width:2105px; overflow-y:scroll; max-height:450px;" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2085" class="rpt_table" id="tbl_list_search">
		<?
		$i = 1;
		$s = 1;
		$tot_program_qnty = 0;
		$tot_knit_qnty = 0;
		$tot_yarn_reqsn_qnty = 0;
		$tot_yarn_issue_qnty = 0;
		$tot_yarn_returnble_qnty = 0;
		$tot_yarn_issue_reqsn_qnty = 0;
		$total_knit_balance = 0;
		$company_id = '';
		$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice, b.knitting_source, b.knitting_party from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_sales=1";
		//$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and c.dtls_id=b.id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_sales!=1";
		//echo $sql;
		$nameArray = sql_select($sql);
		$advice = "";
		foreach ($nameArray as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			$color = '';
			$color_id = explode(",", $row[csf('color_id')]);
			foreach ($color_id as $val) {
				if ($val > 0) $color .= $color_library[$val] . ",";
			}
			$color = chop($color, ',');

			if ($company_id == '')
				$company_id = $row[csf('company_id')];

			$machine_no = '';
			$machine_id = explode(",", $row[csf('machine_id')]);

			foreach ($machine_id as $val) {
				if ($machine_no == '')
					$machine_no = $machine_arr[$val];
				else
					$machine_no .= "," . $machine_arr[$val];
			}
			if ($machine_id[0] != "") {
				$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
			}

			if ($knit_id_array[$row[csf('program_id')]] != "")
			{
				$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
				$row_span = count($all_prod_id);
				$z = 0;
				foreach ($all_prod_id as $prod_id)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<?
						if ($z == 0)
						{
							?>
							<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
							<td width="60" rowspan="<? echo $row_span; ?>" align="center"  style="font-size:14px;"><b><? echo $row[csf('program_id')]; ?></b><br><p style="font-size:12px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
							<td width="100" align="center" rowspan="<? echo $row_span; ?>"><p><? if ($row[csf('knitting_source')] == 1) echo $company_details[$row[csf('knitting_party')]]; else echo $suplier_details[$row[csf('knitting_party')]]; ?></p></td>
							<td width="130" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
							<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
							<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

							<td width="55" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

							<td width="55" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
							<td width="110" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
							<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
							<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>


							<td width="80" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
							<td width="80" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
							<td width="60" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
							<td width="60" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($knit_qty_arr[$row[csf('program_id')]]['knit_qty'], 2, '.', ''); ?>&nbsp;</td>
							<td width="60" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')]-$knit_qty_arr[$row[csf('program_id')]]['knit_qty'], 2, '.', ''); ?>&nbsp;</td>
							<?
							$tot_program_qnty += $row[csf('program_qnty')];
							$tot_knit_qnty += $knit_qty_arr[$row[csf('program_id')]]['knit_qty'];
							$total_knit_balance += $row[csf('program_qnty')]-$knit_qty_arr[$row[csf('program_id')]]['knit_qty'];
							$i++;
							$requisition_no=$prod_no_array[$row[csf('program_id')]][$prod_id];
							$tot_fabric_reject += $knit_qty_arr[$row[csf('program_id')]]['fabric_reject'];
						}
						?>
						<td width="60" align="center"><p><? echo chop($requisition_no,","); ?></p></td>

						<td width="120"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
						<td width="60" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
						<td width="60" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_qty_arr[$requisition_no][$prod_id]['issue_qty'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id]-$issue_qty_arr[$requisition_no][$prod_id]['issue_qty'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_qty_arr[$requisition_no][$prod_id]['return_qnty'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_return_arr[$requisition_no][$prod_id]['issue_return'], 2, '.', ''); ?></td>
						<td width="60" align="right"><? echo number_format($issue_return_arr[$requisition_no][$prod_id]['reject_qnty'], 2, '.', ''); ?></td>
						<?
						if ($z == 0)
						{
							?>
							<td  width="60" align="right" rowspan="<? echo $row_span; ?>"><p><? echo number_format($knit_qty_arr[$row[csf('program_id')]]['fabric_reject'], 2, '.', ''); ?>&nbsp;</p></td>
							<td  width="60" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
							<?
						}
						?>
					</tr>
					<?

					$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
					$tot_yarn_issue_qnty += $issue_qty_arr[$requisition_no][$prod_id]['issue_qty'];
					$tot_yarn_returnble_qnty += $issue_qty_arr[$requisition_no][$prod_id]['return_qnty'];
					$tot_yarn_issue_reqsn_qnty += $issue_return_arr[$requisition_no][$prod_id]['issue_return'];
					$tot_yarn_issue_reject_qnty += $issue_return_arr[$requisition_no][$prod_id]['reject_qnty'];
					$tot_yarn_issue_balance += $prod_id_array[$row[csf('program_id')]][$prod_id]-$issue_qty_arr[$requisition_no][$prod_id]['issue_qty'];
					$z++;
				}
			}
			else
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="25"><? echo $i; ?></td>
					<td width="60" align="center" style="font-size:14px;"><b><? echo $row[csf('program_id')]; ?></b><br><p style="font-size:12px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
					<td width="100"><p><? if ($row[csf('knitting_source')] == 1) echo $company_details[$row[csf('knitting_party')]]; else echo $suplier_details[$row[csf('knitting_party')]]; ?></p></td>

					<td width="130"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
					<td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
					<td width="70"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

					<td width="55" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

					<td width="55" align="center"><p><? echo $machine_no; ?></p></td>
					<td width="60"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
					<td width="110"><p><? echo $color; ?></p></td>
					<td width="70"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
					<td width="60"><p><? //echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
					<td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
					<td width="60"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
					<td width="80" rowspan="<? //echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
					<td width="80" rowspan="<? //echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
					<td width="60" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
					<td width="60" align="right"><? //echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
					<td width="60" align="right"><? //echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="120"><p>&nbsp;</p></td>
					<td width="60" align="right">&nbsp;</td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p>&nbsp;</p></td>
					<td width="60"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
				</tr>
				<?
				$tot_program_qnty += $row[csf('program_qnty')];
				$i++;
			}
			$advice = $row[csf('advice')];
			$advice = str_replace(array(";","\n"), "<br/>", $advice);
		}
		?>
		<tfoot>
			<th width="25">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="100">&nbsp;</th>
			<th width="130">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="50">&nbsp;</th>
			<th width="70">&nbsp;</th>
			<th width="55">&nbsp;</th>
			<th width="55">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="110">&nbsp;</th>
			<th width="70">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="80">&nbsp;</th>
			<th width="80"><b>Total</b></th>
			<th align="right" width="60"><? echo number_format($tot_program_qnty, 2, '.', ''); ?></th>
			<th align="right" width="60"><? echo number_format($tot_knit_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($total_knit_balance, 2, '.', ''); ?>&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th width="120">&nbsp;</th>
			<th width="60">&nbsp;</th>
			<th align="right" width="60"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
			<th width="60"><? echo number_format($tot_yarn_issue_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_issue_balance, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_returnble_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_issue_reqsn_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60"><? echo number_format($tot_yarn_issue_reject_qnty, 2, '.', ''); ?>&nbsp;</th>
			<th width="60" align="right"><? echo number_format($tot_fabric_reject, 2, '.', ''); ?>&nbsp;</th>
			<th>&nbsp;</th>
		</tfoot>
	</table>
	</div>
	</fieldset>
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}

if ($action == "requisition_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$program_ids = $data;
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$suplier_details = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	$knit_id_array = array();
	$prod_id_array = array();
	$prod_no_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row)
	{
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$prod_no_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('requisition_no')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}
	?>
	<table style="margin-top:10px;" width="900" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
		<thead align="center">
			<th width="25">SL</th>
			<th width="100">Program No</th>
			<th width="100">Program Date</th>
			<th width="100">Requisition NO</th>
			<th width="100">Requisition Company</th>
			<th width="80">Brand</th>
			<th width="120">Yarn Description</th>
			<th width="50">Lot</th>
			<th width="100">Yarn Qty.(KG)</th>
			<th>Remarks</th>
		</thead>
	</table>
	<div style="width:920px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" width="900" rules="all" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$s = 1;
			$tot_program_qnty = 0;
			$tot_yarn_reqsn_qnty = 0;
			$company_id = '';
			$sql = "SELECT a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice, b.knitting_source, b.knitting_party from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

			//echo $sql;
			$nameArray = sql_select($sql);

			$advice = "";
			foreach ($nameArray as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$color = '';
				$color_id = explode(",", $row[csf('color_id')]);

				foreach ($color_id as $val) {
					if ($color == '')
						$color = $color_library[$val];
					else
						$color .= "," . $color_library[$val];
				}

				if ($company_id == '')
					$company_id = $row[csf('company_id')];

				$machine_no = '';
				$machine_id = explode(",", $row[csf('machine_id')]);

				foreach ($machine_id as $val) {
					if ($machine_no == '')
						$machine_no = $machine_arr[$val];
					else
						$machine_no .= "," . $machine_arr[$val];
				}
				if ($machine_id[0] != "") {
					$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
				}

				if ($knit_id_array[$row[csf('program_id')]] != "")
				{
					$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
					$row_span = count($all_prod_id);
					$z = 0;
					foreach ($all_prod_id as $prod_id)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<?
							if ($z == 0) {
								?>
								<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
								<?
								$i++;
								$requisition_no = $prod_no_array[$row[csf('program_id')]][$prod_id];
							}
							?>
							<td width="100" align="center"><p><? echo $row[csf('program_id')]; ?></p></td>
							<td width="100" align="center"><p><? echo change_date_format($row[csf('program_date')]); ?></p></td>
							<td width="100" align="center"><p><? echo chop($requisition_no,","); ?></p></td>
							<td width="100" align="center"><p><? if ($row[csf('knitting_source')] == 1) echo $company_details[$row[csf('knitting_party')]]; else echo $suplier_details[$row[csf('knitting_party')]]; ?></p></td>
							<td width="80"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
							<td width="100" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
							<?
							if ($z == 0) {
								?>
								<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								<?
							}
							?>
						</tr>
						<?
						$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
						$z++;
					}
				}
				else
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="25"><? echo $i; ?></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="80"><p>&nbsp;</p></td>
						<td width="120"><p>&nbsp;</p></td>
						<td width="50"><p>&nbsp;</p></td>
						<td width="100" align="right">&nbsp;</td>
						<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$tot_program_qnty += $row[csf('program_qnty')];
					$i++;
				}
				$advice = $row[csf('advice')];
				$advice = str_replace(array(";","\n"), "<br/>", $advice);
			}
			?>
			<tfoot>
				<th colspan="8" align="right"><b>Total</b></th>
				<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
				<th>&nbsp;</th>
			</tfoot>

		</table>
	</div>
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
	exit();
}

if ($action == "demand_popup")
{
	echo load_html_head_contents("Allocation Details", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$program_ids = $data;
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$suplier_details = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}

	$knit_id_array = array();
	$prod_id_array = array();
	$prod_no_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	$all_req_no_arr = array();
	foreach ($reqsn_dataArray as $row)
	{
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$prod_no_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('requisition_no')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];

		array_push($all_req_no_arr,$row[csf('requisition_no')]);
	}

	$sql_demand = "SELECT a.id, a.demand_system_no, b.requisition_no as requ_no, b.demand_qnty from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b
	where a.id=b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 ".where_con_using_array($all_req_no_arr,0,'b.requisition_no')." ";

	//echo $sql_demand;die;
	$sql_demand_data = sql_select($sql_demand);

	$demand_info_arr = array();
	foreach ($sql_demand_data as $row)
	{
		$demand_info_arr[$row[csf('requ_no')]]['demand_qnty'] += $row[csf('demand_qnty')];
		$demand_info_arr[$row[csf('requ_no')]]['demand_system_no'] = $row[csf('demand_system_no')];
	}
	?>
	<table style="margin-top:10px;" width="800" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
		<thead align="center">
			<th width="25">SL</th>
			<th width="100">Requisition NO</th>
			<th width="100">Demand NO</th>
			<th width="100">Requisition Company</th>
			<th width="80">Brand</th>
			<th width="120">Yarn Description</th>
			<th width="50">Lot</th>
			<th width="100">Yarn Demand Qty</th>
			<th>Remarks</th>
		</thead>
	</table>
	<div style="width:820px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" width="800" rules="all" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			$s = 1;
			$tot_program_qnty = 0;
			$tot_yarn_reqsn_qnty = 0;
			$company_id = '';
			$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice, b.knitting_source, b.knitting_party from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";


			$nameArray = sql_select($sql);

			unset($sql_demand_data);
			//var_dump($demand_qty_arr);die

			$advice = "";
			foreach ($nameArray as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$color = '';
				$color_id = explode(",", $row[csf('color_id')]);

				foreach ($color_id as $val) {
					if ($color == '')
						$color = $color_library[$val];
					else
						$color .= "," . $color_library[$val];
				}

				if ($company_id == '')
					$company_id = $row[csf('company_id')];

				$machine_no = '';
				$machine_id = explode(",", $row[csf('machine_id')]);

				foreach ($machine_id as $val) {
					if ($machine_no == '')
						$machine_no = $machine_arr[$val];
					else
						$machine_no .= "," . $machine_arr[$val];
				}
				if ($machine_id[0] != "") {
					$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
				}

				if ($knit_id_array[$row[csf('program_id')]] != "")
				{
					$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
					$row_span = count($all_prod_id);
					$z = 0;
					foreach ($all_prod_id as $prod_id)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<?
							if ($z == 0) {
								?>
								<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
								<?
								$i++;
								$requisition_no = $prod_no_array[$row[csf('program_id')]][$prod_id];

								$all_requisition_no_arr=array_unique(explode(",",chop($requisition_no,",")));
								$demand_qnty = 0; $demand_system_no = '';
								foreach($all_requisition_no_arr as $requ_id)
								{
									$demand_qnty += $demand_info_arr[$requ_id]['demand_qnty'];
									$demand_system_no = $demand_info_arr[$requ_id]['demand_system_no'];
								}
							}
							?>
							<td width="100" align="center"><p><? echo chop($requisition_no,","); ?></p></td>
							<td width="100" align="center"><p><? echo $demand_system_no; ?></p></td>
							<td width="100" align="center"><p><? if ($row[csf('knitting_source')] == 1) echo $company_details[$row[csf('knitting_party')]]; else echo $suplier_details[$row[csf('knitting_party')]]; ?></p></td>
							<td width="80"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
							<td width="100" align="right"><? echo number_format($demand_qnty, 2, '.', ''); ?></td>
							<?
							if ($z == 0) {
								?>
								<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								<?
							}
							?>
						</tr>
						<?
						$tot_yarn_reqsn_qnty += $demand_qnty;
						$z++;
					}
				}
				else
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="25"><? echo $i; ?></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="100"><p>&nbsp;</p></td>
						<td width="80"><p>&nbsp;</p></td>
						<td width="120"><p>&nbsp;</p></td>
						<td width="50"><p>&nbsp;</p></td>
						<td width="100" align="right">&nbsp;</td>
						<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$tot_program_qnty += $row[csf('program_qnty')];
					$i++;
				}
				$advice = $row[csf('advice')];
				$advice = str_replace(array(";","\n"), "<br/>", $advice);
			}
			?>
			<tfoot>
				<th colspan="7" align="right"><b>Total</b></th>
				<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
				<th>&nbsp;</th>
			</tfoot>

		</table>
	</div>
	<script>setFilterGrid("tbl_list_search",-1);</script>
	<?
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
	$job_nos=trim(str_replace("'","",$data[3]));

	//comments date 30.01.2022
	/*
	$job_sql="select id from wo_po_break_down where job_no_mst='$job_nos'";
	$job_sql_result=sql_select($job_sql);
	$po_ids="";
	foreach($job_sql_result as $row)
	{
		$po_ids.=$row[csf("id")].",";
	}
	$po_ids=chop($po_ids,",");
	*/

	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
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
					<th colspan="11"><b>Yarn Issue</b></th>
				</thead>
				<thead>
					<th width="105">Issue Id</th>
					<th width="90">Issue To</th>
					<th width="105">Req.No./Booking No</th>
					<th width="70">Challan No</th>
					<th width="75">Issue Date</th>
					<th width="80">Store</th>
					<th width="70">Brand</th>
					<th width="60">Lot No</th>
					<th width="180">Yarn Description</th>
					<th width="90">Issue Qnty (In)</th>
					<th>Issue Qnty (Out)</th>
				</thead>
			</table>
			<div style="width:1060px; overflow-y:scroll; max-height:400px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				$total_yarn_issue_qnty = 0;
				$total_yarn_issue_qnty_out = 0;

				if($requ_ids !='')
				{
					$sql = "SELECT a.issue_number, a.issue_date,a.issue_basis, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.store_id, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id,d.requisition_no
					from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
					where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and d.requisition_no in ($requ_ids) and b.po_breakdown_id in($po_ids) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.issue_purpose not in (2,8)
					group by a.id,c.id,a.issue_number,a.issue_date, a.challan_no, a.knit_dye_source,a.knit_dye_company, a.booking_no, a.store_id, c.lot, c.yarn_type, c.product_name_details, d.brand_id,a.issue_basis,d.requisition_no";

					$result = sql_select($sql);
				}

				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if ($row[csf('knit_dye_source')] == 1) {
						$issue_to = $company_library[$row[csf('knit_dye_company')]];
					} else if ($row[csf('knit_dye_source')] == 3) {
						$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
					} else {
						$issue_to = "&nbsp;";
					}
					$yarn_issued = $row[csf('issue_qnty')];

					if($row[csf('issue_basis')]==1){
						$booking_no=$row[csf('booking_no')];
					}elseif ($row[csf('issue_basis')]==3) {
						$booking_no=$row[csf('requisition_no')];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="90"><p><? echo $issue_to; ?></p></td>
						<td width="105" align="center" ><p><? echo $booking_no."<br/>"; if($row[csf('issue_basis')]==3){echo $bookingNo;}  ?>&nbsp;</p></td>
						<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="80"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
						<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
						<td align="right" width="90">
							<?
							if ($row[csf('knit_dye_source')] != 3) {
								echo number_format($yarn_issued, 2, '.', '');
								$total_yarn_issue_qnty += $yarn_issued;
							} else
							echo "&nbsp;";
							?>
						</td>
						<td align="right">
							<?
							if ($row[csf('knit_dye_source')] == 3) {
								echo number_format($yarn_issued, 2, '.', '');
								$total_yarn_issue_qnty_out += $yarn_issued;
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
					<td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
				</tr>
				<tfoot>
					<tr>
						<th align="right" colspan="10">Issue Total</th>
						<th align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2, '.', ''); ?></th>
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

if ($action == "issue_sales_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$requ_ids=$data[0];
	$bookingNo=$data[1];
	$po_ids=$data[2];
	$job_nos=trim(str_replace("'","",$data[3]));

	//comments date 30.01.2022
	/*
	$job_sql="select id from wo_po_break_down where job_no_mst='$job_nos'";
	$job_sql_result=sql_select($job_sql);
	$po_ids="";
	foreach($job_sql_result as $row)
	{
		$po_ids.=$row[csf("id")].",";
	}
	$po_ids=chop($po_ids,",");
	*/

	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
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
	<div style="width:1150px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1150px; margin-left:3px">
		<div style="width:100%" id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Yarn Issue</b></th>
				</thead>
				<thead>
					<th width="105">Issue Id</th>
					<th width="90">Issue To</th>
					<th width="105">Req.No./Booking No</th>
					<th width="100">Demand No</th>
					<th width="70">Challan No</th>
					<th width="75">Issue Date</th>
					<th width="80">Store</th>
					<th width="70">Brand</th>
					<th width="60">Lot No</th>
					<th width="180">Yarn Description</th>
					<th width="90">Issue Qnty (In)</th>
					<th>Issue Qnty (Out)</th>
				</thead>
			</table>
			<div style="width:1160px; overflow-y:scroll; max-height:400px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				$total_yarn_issue_qnty = 0;
				$total_yarn_issue_qnty_out = 0;

				if($requ_ids !='')
				{
					$sql = "SELECT a.issue_number, a.issue_date,a.issue_basis, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.store_id, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id,d.requisition_no
					from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
					where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and d.requisition_no in ($requ_ids) and b.po_breakdown_id in($po_ids) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.issue_basis in(3,8) and  b.issue_purpose not in (2,8) and b.is_sales=1
					group by a.id,c.id,a.issue_number,a.issue_date, a.challan_no, a.knit_dye_source,a.knit_dye_company, a.booking_no, a.store_id, c.lot, c.yarn_type, c.product_name_details, d.brand_id,a.issue_basis,d.requisition_no";
					//echo $sql;
					$result = sql_select($sql);
				}

				$all_req_no_arr = array();
				foreach ($result as $row)
				{
					array_push($all_req_no_arr,$row[csf('requisition_no')]);
				}

				$sql_demand = "SELECT a.id, a.demand_system_no, b.requisition_no as requ_no, b.demand_qnty from ppl_yarn_demand_entry_mst a, ppl_yarn_demand_entry_dtls b
				where a.id=b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 ".where_con_using_array($all_req_no_arr,0,'b.requisition_no')." ";

				//echo $sql_demand;//die;
				$sql_demand_data = sql_select($sql_demand);

				$demand_info_arr = array();
				foreach ($sql_demand_data as $row)
				{
					$demand_info_arr[$row[csf('requ_no')]]['demand_system_no'] = $row[csf('demand_system_no')];
				}

				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if ($row[csf('knit_dye_source')] == 1) {
						$issue_to = $company_library[$row[csf('knit_dye_company')]];
					} else if ($row[csf('knit_dye_source')] == 3) {
						$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
					} else {
						$issue_to = "&nbsp;";
					}
					$yarn_issued = $row[csf('issue_qnty')];

					if($row[csf('issue_basis')]==1){
						$booking_no=$row[csf('booking_no')];
					}elseif ($row[csf('issue_basis')]==3 || $row[csf('issue_basis')]==8) {
						$booking_no = $row[csf('requisition_no')];
						$demand_no = $demand_info_arr[$row[csf('requisition_no')]]['demand_system_no'];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td width="90"><p><? echo $issue_to; ?></p></td>
						<td width="105" align="center" ><p><? echo $booking_no."<br/>"; if($row[csf('issue_basis')]==3){echo $bookingNo;}  ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? echo $demand_no; ?></p></td>
						<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td width="80"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
						<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
						<td align="right" width="90">
							<?
							if ($row[csf('knit_dye_source')] != 3) {
								echo number_format($yarn_issued, 2, '.', '');
								$total_yarn_issue_qnty += $yarn_issued;
							} else
							echo "&nbsp;";
							?>
						</td>
						<td align="right">
							<?
							if ($row[csf('knit_dye_source')] == 3) {
								echo number_format($yarn_issued, 2, '.', '');
								$total_yarn_issue_qnty_out += $yarn_issued;
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
					<td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2, '.', ''); ?></td>
				</tr>
				<tfoot>
					<tr>
						<th align="right" colspan="11">Issue Total</th>
						<th align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2, '.', ''); ?></th>
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

if ($action == "issue_return_popup")
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

				$sql = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, a.store_id, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and a.booking_id in ($requ_ids) and b.po_breakdown_id IN($po_ids) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose not in (2,8) group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, a.store_id, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
				//echo $sql;
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

if ($action == "issue_return_sales_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data=explode('_',$data);
	$issue_id=$data[0];
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

				$sql = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, a.store_id, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and a.issue_id in ($issue_id) and b.po_breakdown_id IN($po_ids) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose not in (2,8) group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, a.store_id, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
				//echo $sql;
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


	$sql="SELECT a.body_part_id, a.construction, a.composition, b.fabric_color_id, sum(b.grey_fab_qnty) as grey_fab_qnty, c.id as po_id, c.po_number, d.id as job_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no
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

function get_composition($arr)
{
	global $composition;
	if ($arr['yarn_comp_percent2nd'] != 0)
	{
		$data = $composition[$arr['yarn_comp_type1st']] . " " . $arr['yarn_comp_percent1st'] . "%" . " " . $composition[$arr['yarn_comp_type2nd']] . " " . $arr['yarn_comp_percent2nd'] . "%";
	}
	else
	{
		$data = $composition[$arr['yarn_comp_type1st']] . " " . $arr['yarn_comp_percent1st'] . "%" . " " . $composition[$arr['yarn_comp_type2nd']];
	}
	return $data;
}
