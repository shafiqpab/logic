<?
/*-------------------------------------------- Comments
Purpose			: 	This form created qc barcode  Report
Functionality	:	
JS Functions	:
Created by		:	Md Didarul Alam 
Creation date 	: 	19/03/2018
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}

//--------------------------------------------------------------------------------------------

if ($action == "roll_maintained") {
	
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
	
	if ($roll_maintained == 1)
	{
	 $roll_maintained = $roll_maintained;
	}
	else {
	 	$roll_maintained = 0;
	}
	
	echo "document.getElementById('roll_maintained').value 	= '" . $roll_maintained . "';\n";
	exit();
}

if ($action == "grey_receive_popup_search") {
	echo load_html_head_contents("Grey Receive Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

<script>
	function js_set_value(id) {
		$('#hidden_recv_id').val(id);
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:880px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:875px; margin-left:5px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="820" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Buyer</th>
						<th>Received Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="200">Enter Receive No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", $data[0]);
							?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Received ID", 2 => "Challan No", 3 => "Booking No");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_grey_recv_search_list_view', 'search_div', 'knitting_production_qc_result_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:15px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_grey_recv_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$buyer_id = $data[5];
	$selectedYear = $data[6];

	if ($buyer_id == 0) $buyer_name = ""; else $buyer_name = " and buyer_id=$buyer_id ";//{ echo "Please Select Buyer First."; die; }

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
		if($selectedYear!="")
		{
			if($db_type == 0)
			{
				$yearCond = " and insert_date like '%$selectedYear%'";
			} else {
				$yearCond = " and to_char(insert_date,'YYYY') like '%$selectedYear%'";
			}
		}
	}

	if (trim($data[0]) != "") {
		if ($search_by == 3)
			$search_field_cond = "and booking_no like '$search_string'";
		else if ($search_by == 1) {
			$search_string = "%" . trim($data[0]);
			$search_field_cond = "and recv_number like '$search_string'";
		} else
		$search_field_cond = "and challan_no like '$search_string'";
	} else {
		$search_field_cond = "";
	}

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$sql = "select id, $year_field recv_number_prefix_num, recv_number, booking_no, buyer_id, knitting_source, knitting_company, receive_date, challan_no, within_group from inv_receive_master where entry_form=2 and status_active=1 and is_deleted=0 and company_id=$company_id $buyer_name $search_field_cond $date_cond $yearCond";
	//echo $sql;//die;
	$result = sql_select($sql);

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$grey_recv_arr = return_library_array("select mst_id, sum(grey_receive_qnty) as recv from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'recv');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Received ID</th>
			<th width="60">Year</th>
			<th width="115">Booking No/ Knit Id</th>
			<th width="100">Knitting Source</th>
			<th width="110">Knitting Company</th>
			<th width="80">Receive date</th>
			<th width="80">Receive Qnty</th>
			<th width="80">Challan No</th>
			<th>Buyer</th>
		</thead>
	</table>
	<div style="width:870px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('knitting_source')] == 1)
				$knit_comp = $company_arr[$row[csf('knitting_company')]];
			else
				$knit_comp = $supllier_arr[$row[csf('knitting_company')]];

				//$recv_qnty=return_field_value("sum(grey_receive_qnty)","pro_grey_prod_entry_dtls","mst_id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
			$recv_qnty = $grey_recv_arr[$row[csf('id')]];

			if ($row[csf('within_group')] == 1)
				$buyer = $company_arr[$row[csf('buyer_id')]];
			else
				$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
				<td width="40"><? echo $i; ?></td>
				<td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
				<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="100"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
				<td width="110"><p><? echo $knit_comp; ?></p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
				<td width="80" align="right"><? echo number_format($recv_qnty, 2, '.', ''); ?></td>
				<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
				<td><p><? echo $buyer; ?></p></td>
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


if ($action == 'populate_data_from_grey_recv') {

	$data_array = sql_select("select id, recv_number_prefix_num, company_id from inv_receive_master where id='$data'");

	foreach ($data_array as $row) {
		echo "document.getElementById('txt_recieved_id').value 				= '" . $row[csf("recv_number_prefix_num")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		}
		exit();
}


//report generated here--------------------//

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$company_id = str_replace("'", "", $cbo_company_id);
	$search_category = str_replace("'", "", $cbo_search_category); 
	$cbo_year = str_replace("'", "", $cbo_year); 
	$txt_date_from = trim($txt_date_from);
	$txt_date_to = trim($txt_date_to);
	$bookingNo = trim($txt_booking_no);	
	$productReceieveNo = trim($txt_recieved_number);
	$barcode = trim($txt_barcode);
	
	if($barcode=="" && $bookingNo=="" && $productReceieveNo=="" && ($txt_date_from == "" && $txt_date_to =="" ) )
	{
		exit("Please Fill either booking number or barcode number or production number ");
	}

	if($db_type==0){
	$receive_year_cond=" and SUBSTRING_INDEX(a.receive_date, '-', 1)= $cbo_year";	

		if ($txt_date_from!="" &&  $txt_date_to!="")
		{
			$receive_date_cond  = "and a.receive_date  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'"; 
			$receive_year_cond ="";
		} else { 
			$receive_date_cond ="";
		}
	}
	if($db_type==2){
		$receive_year_cond=" and to_char(a.receive_date,'YYYY')=$cbo_year";
		if ($txt_date_from!="" &&  $txt_date_to!="") 
		{
			$receive_date_cond  = "and a.receive_date  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'"; 
			$receive_year_cond ="";
		} else { 
			$receive_date_cond ="";
		}
	}
	
	if($company_id!=0) {
		$company_conds=" and a.company_id=$company_id";
	} else {
		$company_conds="";
	}

	if($barcode!="") {
		$barcode_conds=" and c.barcode_no=$barcode";
	} else {
		$barcode_conds="";
	}

	
	if($search_category==1 || $search_category=="" || $search_category==0){
		if ($productReceieveNo!="") {
			$receieveNo_conds=" and a.recv_number_prefix_num ='$productReceieveNo'";
			$receive_year_cond="";
		} else {
			$receieveNo_conds="";
		}	
		if ($bookingNo != "") $booking_no_cond = "and a.booking_no = '$bookingNo'"; else $booking_no_cond = "";	
	}

	if($search_category==2){
		if ($productReceieveNo!="") {
			$receieveNo_conds=" and a.recv_number_prefix_num like '$productReceieveNo%'";
			$receive_year_cond="";
		} else {
			$receieveNo_conds="";
		}
		if ($bookingNo != "") $booking_no_cond = "and a.booking_no like '$bookingNo%'"; else $booking_no_cond = "";	
		
	}

	if($search_category==3){
		if ($productReceieveNo!="") {
			$receieveNo_conds=" and a.recv_number_prefix_num like '%$productReceieveNo'";
			$receive_year_cond="";
		} else {
			$receieveNo_conds="";
		}
		if ($bookingNo != "") $booking_no_cond = "and a.booking_no like '%$bookingNo'"; else $booking_no_cond = "";	
		
	}if($search_category==4){
		if ($productReceieveNo!="") 
		{
			$receieveNo_conds=" and a.recv_number_prefix_num like '%$productReceieveNo%' ";
			$receive_year_cond = "";
		} else {
			$receieveNo_conds="";
		}

		if ($bookingNo != "") $booking_no_cond = "and a.booking_no like '%$bookingNo%'"; else $booking_no_cond = "";	
	}
 
	$composition_arr = array(); 
	$constructtion_arr = array();
	$sql_deter = "select a.id as ID, a.construction as CONSTRUCTION, b.copmposition_id as COPMPOSITION_ID, b.percent as PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row['ID']] = $row['CONSTRUCTION'];
		$composition_arr[$row['ID']] .= $composition[$row['COPMPOSITION_ID']] . " " . $row['PERCENT'] . "% ";
	}
	unset($data_array);
	
	$need_data_array = sql_select("select a.company_id,a.receive_basis, a.booking_no, a.knitting_company, c.po_breakdown_id,c.is_sales,b.color_id,c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond");
	
	$plan_arr=array();
	$barcode_arr=array();
	$sales_ids_arr=array();
	$po_ids_arr=array();
	$po_ids_arr=array();
	foreach ($need_data_array as $row) {
		if($row[csf('receive_basis')] == 2){
			$plan_arr[] = $row[csf('booking_no')];

			if($chk_plan_arr[$row[csf('booking_no')]]=='')
			{
				$chk_plan_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
				array_push($plan_arr,$row[csf('booking_no')]);
			}
		}
		//$plan_id=implode(',',$plan_arr);
		if($row[csf("is_sales")] == 1){
			$sales_ids[] = $row[csf('po_breakdown_id')];

			if($chk_sales_ids_arr[$row[csf('po_breakdown_id')]]=='')
			{
				$chk_sales_ids_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				array_push($sales_ids_arr,$row[csf('po_breakdown_id')]);
			}
		}else{
			$po_ids[] = $row[csf('po_breakdown_id')];

			if($chk_poids_arr[$row[csf('po_breakdown_id')]]=='')
			{
				$chk_poids_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				array_push($po_ids_arr,$row[csf('po_breakdown_id')]);
			}
		}

		$barcodeArr[$row[csf('barcode_no')]] =  $row[csf('barcode_no')];

		$companyIds[$row[csf("knitting_company")]] = $row[csf("knitting_company")];
		$colorIds[$row[csf('color_id')]] = $row[csf('color_id')];

		if($chk_barcode_arr[$row[csf('barcode_no')]]=='')
		{
			$chk_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			array_push($barcode_arr,$row[csf('barcode_no')]);
		}

	}
	//var_dump($po_ids_arr);
	
	if(empty($barcodeArr)){ echo "No data found for QC.";die;}
	

	$data_delivery_array = sql_select("select d.barcode_num from pro_grey_prod_delivery_dtls d where  d.entry_form=56 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($barcode_arr,1,'barcode_num')."");
	
	foreach ($data_delivery_array as $row) {
		unset($barcodeArr[$row[csf('barcode_num')]]);
	}
	
	
	
	if(!empty($barcodeArr)) // all ready qc barcode 
	{
		$qcBarcodeResult = sql_select("SELECT barcode_no, roll_status FROM pro_qc_result_mst a WHERE a.status_active=1 and a.is_deleted=0 ".where_con_using_array($barcode_arr,1,'a.barcode_no')." ");
		foreach ($qcBarcodeResult as $row) {
			$qcBarcodeArr[$row[csf('barcode_no')]] =  $row[csf('barcode_no')];
			$qcBarcodeStatusArr[$row[csf('barcode_no')]]['roll_status'] =  $row[csf('roll_status')];
			//unset($barcodeArr[$row[csf('barcode_no')]]); // Remove all ready qc barcode 
		}
	}

	if(empty($barcodeArr)){ echo "No data found for QC.";die;}
 
	if(!empty($sales_ids)){

		$sales_order_result = sql_select("select within_group,sales_booking_no,id po_id,job_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 ".where_con_using_array($sales_ids_arr,0,'id')."");
		$sales_arr = array();
		$booking_nos_arr = array();
		foreach ($sales_order_result as $sales_row) 
		{
			$sales_arr[$sales_row[csf("po_id")]]["job_no"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("po_id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("po_id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$booking_nos[] = "'".$sales_row[csf("sales_booking_no")]."'";

			if($chk_sales_arr[$sales_row[csf('sales_booking_no')]]=='')
			{
				$chk_sales_arr[$sales_row[csf('sales_booking_no')]] = $sales_row[csf('sales_booking_no')];
				array_push($booking_nos_arr,$sales_row[csf('sales_booking_no')]);
			}
			
		}
		
	}
	
	//$po_cond = (!empty($po_ids))?" and c.id in(".implode(",",$po_ids).")":" and a.booking_no in(".implode(",",$booking_nos).")";
	$po_cond = (!empty($po_ids_arr))?" ".where_con_using_array($po_ids_arr,0,'c.id')." ":" ".where_con_using_array($booking_nos_arr,1,'a.booking_no')." ";
	
	$po_array = sql_select("select b.job_no,c.po_number,a.buyer_id,c.id po_id,b.booking_no,b.insert_date, c.grouping from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_cond");

	$po_details_array = array();
	foreach ($po_array as $row) {
		$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
		$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['grouping'] = $row[csf("grouping")];
		$po_details_array[$row[csf("booking_no")]]['job_no'] 		= $row[csf("job_no")];
		$po_details_array[$row[csf("booking_no")]]['buyer_name'] 	= $row[csf("buyer_id")];
		$po_details_array[$row[csf("booking_no")]]['po_id'] 		= $row[csf("po_id")];
		$po_details_array[$row[csf("booking_no")]]['po_number'] 	= $row[csf("po_number")];
		$po_details_array[$row[csf("booking_no")]]['grouping'] 	= $row[csf("grouping")];
		$po_details_array[$row[csf("booking_no")]]['year'] 			= date("Y", strtotime($row[csf("insert_date")]));
	}
	
	//$plan_cond = ($plan_arr != "")?" and a.id in ($plan_id)":"";
	$plan_cond = ($plan_arr != "")?" ".where_con_using_array($plan_arr,0,'a.id')."":"";
	
	$ppl_data = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $plan_cond");
	$plan_array=array();
	foreach ($ppl_data as $row) {
		$plan_array[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
	}
	//var_dump($plan_array);

	$company_name_array = return_library_array("select id,company_name from lib_company where id in(".implode(',', $companyIds).")", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where id in(".implode(',', $companyIds).")", "id", "supplier_name");
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color  where id in('".implode("','", $colorIds)."')", 'id', 'color_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$receive_basis = array(0=>'Independent',1=>'Fabric Booking',2=>'Knitting Plan',3=>'Sales Order');
	
	$roll_status = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');

	
	/*echo "<pre>";
	print_r($barcodeArr); die();*/
	// echo "select a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond and c.barcode_no in('".implode("','",$barcodeArr)."')";
	
	
	$data_array = sql_select("SELECT a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.shift_name,b.machine_no_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond ".where_con_using_array($barcode_arr,1,'c.barcode_no')."");
	//echo "SELECT a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.shift_name,b.machine_no_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond and c.barcode_no in('".implode("','",$barcodeArr)."')";

	ob_start();
	?>
	<script type="text/javascript">
		$('.hide_td_header').hide();
		//$('.hide_td_header').fadeOut();
	</script>
	<div style="width:1540;">
		<table width="1540" border="1" rules="all" class="rpt_table">
			<thead>
		        <tr>
		            <th width="35">SL</th>
                    <th width="75">&nbsp;</th>
		            <th width="100">Barcode</th>
					<th width="100">System Id</th>	
					<th width="85">Booking/ Programm No</th> 	
					<th width="80">Production Basis</th> 	
					<th width="80">Knitting Source</th> 	
					<th width="80">Production date</th> 	
					<th width="80">Rcv. Challan No.</th> 	
					<th width="80">Service Booking No.</th> 	
					<th width="80">Product Id</th>  	
					<th width="80">Year</th> 	
					<th width="80">Job No</th> 	
					<th width="80">Buyer</th> 	
					<th width="80">Order/FSO No</th>  	
					<th width="80">Fabric Color</th>  	
					<th width="80">Construction</th>  	
					<th width="80">Composition</th>  	
					<th width="80">GSM</th>  	
					<th width="80">Dia</th>  	
					<th width="80">Roll No</th>  	
					<th width="80">Internal Ref.</th>  	
					<th width="80">Roll Status</th>  	
					<th width="80">Machine Name</th>  	
					<th width="80">Shift Name</th>  	
					<th width="80">Production Qty</th> 	
					<th width="80">QC Pass Qty</th>
					
		        </tr>
		    </thead>
			<? 
			$i = 1;
			$po_ids_arr = array();
			foreach ($data_array as $row) 
			{			  
				if($barcodeArr[$row[csf('barcode_no')]]!="")
				{
					$booking_no_id = $row[csf('booking_no')];
					$sales_order_no = "";
					$is_salesOrder = $row[csf("is_sales")];

					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_name_array[$row[csf("knitting_company")]];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}
					$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]), 2, '.', '');

					if ($row[csf("booking_without_order")] != 1) {
						if ($is_salesOrder == 1) {
							// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
							$sales_id = $row[csf("po_breakdown_id")];
							$within_group = $sales_arr[$sales_id]["within_group"];	
							if($within_group == 1){					
								$booking_no = $sales_arr[$sales_id]["sales_booking_no"];

								$sales_order_no = $sales_arr[$sales_id]["job_no"];
								$job_no = $po_details_array[$booking_no]['job_no'];
								$grouping = $po_details_array[$booking_no]['grouping'];
								$po_id = $row[csf("po_breakdown_id")];
								$po_no = $sales_arr[$sales_id]["job_no"];
								$buyer_id = $po_details_array[$booking_no]['buyer_name'];
								$year = $po_details_array[$booking_no]['year'];
							}else{
								$sales_order_no = $sales_arr[$sales_id]["job_no"];
								$job_no = "";
								$grouping = "";
								$po_id = $sales_id;
								$po_no = $sales_order_no;
								$buyer_id = "";
								$year = "";
							}
						} else {
							$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
							$po_id = $row[csf("po_breakdown_id")];
							$year = $po_details_array[$po_id]['year'];
							$job_no = $po_details_array[$po_id]['job_no'];
							$grouping = $po_details_array[$po_id]['grouping'];
							$buyer_id = $po_details_array[$po_id]['buyer_name'];
							$po_no=$po_details_array[$po_id]['po_number'];
						}
					} else {
						// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO	
						if ($is_salesOrder == 1) {
							$sales_id = $row[csf("po_breakdown_id")];
							$sales_order_no = $sales_arr[$sales_id]["job_no"];
							$within_group = $sales_arr[$sales_id]["within_group"];
							$po_id = $sales_id;
						} else {
							$po_id = $row[csf("po_breakdown_id")];
						}
					}
					$color = '';
					$color_id = explode(",", $row[csf('color_id')]);
					foreach ($color_id as $val) {
						if ($val > 0) $color .= $color_arr[$val] . ",";
					}
					$color = chop($color, ',');

					if($row[csf('receive_basis')]==1){
						$booking_no = $row[csf('booking_no')];
					}elseif ($row[csf('receive_basis')]==2) {
						$booking_no = $plan_array[$row[csf('booking_no')]]['booking_no']." / ".$row[csf('booking_no')];
					}elseif ($row[csf('receive_basis')]==4) {
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
					}
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

					if($qcBarcodeArr[$row[csf('barcode_no')]]!="")
					{
						$bgcolor="#FFFGGF";
					}
					
					
					?> 
				    <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">

				    	<td align="center" width="35"><? echo $i;?></td>
				        <td>
				    	<input id="knit_defect" name="knit_defect" class="formbuttonplasminus" style="width:70px;" value="QC Result" onClick="fn_knit_defect(<?php echo $row[csf("dtls_id")]; ?>,<?php echo $row[csf('barcode_no')]; ?> )" type="button">
				    	</td>
				    	<td width="100"><? echo $row[csf('barcode_no')]; ?></td>
				    	<td width="100"><? echo $row[csf("recv_number")]; ?></td>
				    	<td width="100"><? echo $booking_no; ?></td>
				    	<td width="100"><? echo $receive_basis[$row[csf("receive_basis")]]; ?></td>
				    	<td><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td>
				    	<td><? echo change_date_format($row[csf("receive_date")]) ?></td>
				    	<td><? echo $row[csf("challan_no")]; ?></td>
				    	<td><? echo $row[csf("service_booking_no")]; ?></td>
				    	<td><? echo $row[csf("prod_id")]; ?></td>
				    	<td><? echo $year; ?></td>
				    	<td><? echo $job_no; ?></td>
				    	<td><? echo $buyer_name_array[$buyer_id]; ?></td>
				    	<td><? echo $po_id; ?></td>
				    	<td><? echo $color_arr[$row[csf('color_id')]]?></td>
				    	<td><? echo $constructtion_arr[$row[csf("febric_description_id")]];?></td>
				    	<td><? echo $composition_arr[$row[csf("febric_description_id")]];?></td>
				    	<td><? echo $row[csf("gsm")]; ?></td>
				    	<td><? echo $row[csf("width")]; ?></td>
				    	<td><? echo $row[csf("roll_no")]; ?></td>
				    	<td><? echo $grouping; ?></td>
				    	<td><? echo $roll_status[$qcBarcodeStatusArr[$row[csf('barcode_no')]]['roll_status']]; ?></td>
				    	<td><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
				    	<td><? echo $shift_name[$row[csf('shift_name')]]; ?></td>
				    	<td><? echo $prodQnty; ?></td>
				    	<td><? echo number_format($row[csf("qnty")],2); ?></td>	
				    </tr>
					<? 
					$i++;
					
				}
			}
			?>
		    </table>
		    <table class="rpt_table" width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
		    <tfoot>
		        <th width="28" style="word-break: break-all;" >&nbsp;</th>
		    </tfoot>
		</table>
		</div>
	</div> <!-- // end main div -->
	<?
	$html=ob_get_contents();	
	ob_end_clean();	
				
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename"; 
	exit();
		
}


if($action=="generate_report_(28-04-2022)")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	

	$company_id = str_replace("'", "", $cbo_company_id);
	$search_category = str_replace("'", "", $cbo_search_category); 
	$cbo_year = str_replace("'", "", $cbo_year); 
	$txt_date_from = trim($txt_date_from);
	$txt_date_to = trim($txt_date_to);
	$bookingNo = trim($txt_booking_no);	
	$productReceieveNo = trim($txt_recieved_number);
	$barcode = trim($txt_barcode);
	
	if($barcode=="" && $bookingNo=="" && $productReceieveNo=="" && ($txt_date_from == "" && $txt_date_to =="" ) )
	{
		exit("Please Fill either booking number or barcode number or production number ");
	}

	if($db_type==0){
	$receive_year_cond=" and SUBSTRING_INDEX(a.receive_date, '-', 1)= $cbo_year";	

		if ($txt_date_from!="" &&  $txt_date_to!="")
		{
			$receive_date_cond  = "and a.receive_date  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'"; 
			$receive_year_cond ="";
		} else { 
			$receive_date_cond ="";
		}
	}
	if($db_type==2){
		$receive_year_cond=" and to_char(a.receive_date,'YYYY')=$cbo_year";
		if ($txt_date_from!="" &&  $txt_date_to!="") 
		{
			$receive_date_cond  = "and a.receive_date  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'"; 
			$receive_year_cond ="";
		} else { 
			$receive_date_cond ="";
		}
	}
	
	if($company_id!=0) {
		$company_conds=" and a.company_id=$company_id";
	} else {
		$company_conds="";
	}

	if($barcode!="") {
		$barcode_conds=" and c.barcode_no=$barcode";
	} else {
		$barcode_conds="";
	}

	
	if($search_category==1 || $search_category=="" || $search_category==0){
		if ($productReceieveNo!="") {
			$receieveNo_conds=" and a.recv_number_prefix_num ='$productReceieveNo'";
			$receive_year_cond="";
		} else {
			$receieveNo_conds="";
		}	
		if ($bookingNo != "") $booking_no_cond = "and a.booking_no = '$bookingNo'"; else $booking_no_cond = "";	
	}

	if($search_category==2){
		if ($productReceieveNo!="") {
			$receieveNo_conds=" and a.recv_number_prefix_num like '$productReceieveNo%'";
			$receive_year_cond="";
		} else {
			$receieveNo_conds="";
		}
		if ($bookingNo != "") $booking_no_cond = "and a.booking_no like '$bookingNo%'"; else $booking_no_cond = "";	
		
	}

	if($search_category==3){
		if ($productReceieveNo!="") {
			$receieveNo_conds=" and a.recv_number_prefix_num like '%$productReceieveNo'";
			$receive_year_cond="";
		} else {
			$receieveNo_conds="";
		}
		if ($bookingNo != "") $booking_no_cond = "and a.booking_no like '%$bookingNo'"; else $booking_no_cond = "";	
		
	}if($search_category==4){
		if ($productReceieveNo!="") 
		{
			$receieveNo_conds=" and a.recv_number_prefix_num like '%$productReceieveNo%' ";
			$receive_year_cond = "";
		} else {
			$receieveNo_conds="";
		}

		if ($bookingNo != "") $booking_no_cond = "and a.booking_no like '%$bookingNo%'"; else $booking_no_cond = "";	
	}
 
	$composition_arr = array(); 
	$constructtion_arr = array();
	$sql_deter = "select a.id as ID, a.construction as CONSTRUCTION, b.copmposition_id as COPMPOSITION_ID, b.percent as PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row['ID']] = $row['CONSTRUCTION'];
		$composition_arr[$row['ID']] .= $composition[$row['COPMPOSITION_ID']] . " " . $row['PERCENT'] . "% ";
	}
	unset($data_array);
	
	$need_data_array = sql_select("select a.company_id,a.receive_basis, a.booking_no, a.knitting_company, c.po_breakdown_id,c.is_sales,b.color_id,c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond");
	
	$plan_arr=array();
	foreach ($need_data_array as $row) {
		if($row[csf('receive_basis')] == 2){
			$plan_arr[] = $row[csf('booking_no')];
		}
		$plan_id=implode(',',$plan_arr);
		if($row[csf("is_sales")] == 1){
			$sales_ids[] = $row[csf('po_breakdown_id')];
		}else{
			$po_ids[] = $row[csf('po_breakdown_id')];
		}

		$barcodeArr[$row[csf('barcode_no')]] =  $row[csf('barcode_no')];

		$companyIds[$row[csf("knitting_company")]] = $row[csf("knitting_company")];
		$colorIds[$row[csf('color_id')]] = $row[csf('color_id')];

	}
	
	if(empty($barcodeArr)){ echo "No data found for QC.";die;}
	
	
	$data_delivery_array = sql_select("select d.barcode_num from pro_grey_prod_delivery_dtls d where  d.entry_form=56 and d.status_active=1 and d.is_deleted=0 and barcode_num in (".implode(",",$barcodeArr).")");
	
	foreach ($data_delivery_array as $row) {
		unset($barcodeArr[$row[csf('barcode_num')]]);
	}
	
	
	
	if(!empty($barcodeArr)) // all ready qc barcode 
	{
		$qcBarcodeResult = sql_select("SELECT barcode_no FROM pro_qc_result_mst a WHERE a.status_active=1 and a.is_deleted=0 and a.barcode_no in (". implode(',', $barcodeArr) .")");
		foreach ($qcBarcodeResult as $row) {
			$qcBarcodeArr[$row[csf('barcode_no')]] =  $row[csf('barcode_no')];
			//unset($barcodeArr[$row[csf('barcode_no')]]); // Remove all ready qc barcode 
		}
	}

	if(empty($barcodeArr)){ echo "No data found for QC.";die;}
 
	if(!empty($sales_ids)){
		$sales_order_result = sql_select("select within_group,sales_booking_no,id po_id,job_no from fabric_sales_order_mst where id in(".implode(",",$sales_ids).") and status_active=1 and is_deleted=0");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("po_id")]]["job_no"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("po_id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("po_id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$booking_nos[] = "'".$sales_row[csf("sales_booking_no")]."'";
		}
	}
	
	$po_cond = (!empty($po_ids))?"and c.id in(".implode(",",$po_ids).")":" and a.booking_no in(".implode(",",$booking_nos).")";
	$po_array = sql_select("select b.job_no,c.po_number,a.buyer_id,c.id po_id,b.booking_no,b.insert_date from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_cond");

	$po_details_array = array();
	foreach ($po_array as $row) {
		$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
		$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
		$po_details_array[$row[csf("booking_no")]]['job_no'] 		= $row[csf("job_no")];
		$po_details_array[$row[csf("booking_no")]]['buyer_name'] 	= $row[csf("buyer_id")];
		$po_details_array[$row[csf("booking_no")]]['po_id'] 		= $row[csf("po_id")];
		$po_details_array[$row[csf("booking_no")]]['po_number'] 	= $row[csf("po_number")];
		$po_details_array[$row[csf("booking_no")]]['year'] 			= date("Y", strtotime($row[csf("insert_date")]));
	}
	
	$plan_cond = ($plan_id != "")?" and a.id in ($plan_id)":"";
	$ppl_data = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $plan_cond");
	$plan_array=array();
	foreach ($ppl_data as $row) {
		$plan_array[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
	}

	$company_name_array = return_library_array("select id,company_name from lib_company where id in(".implode(',', $companyIds).")", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier where id in(".implode(',', $companyIds).")", "id", "supplier_name");
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color  where id in('".implode("','", $colorIds)."')", 'id', 'color_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$receive_basis = array(0=>'Independent',1=>'Fabric Booking',2=>'Knitting Plan',3=>'Sales Order');

	
	/*echo "<pre>";
	print_r($barcodeArr); die();*/
	// echo "select a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond and c.barcode_no in('".implode("','",$barcodeArr)."')";
	
	$data_array = sql_select("SELECT a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.shift_name,b.machine_no_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond and c.barcode_no in('".implode("','",$barcodeArr)."')");
	//echo "SELECT a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.shift_name,b.machine_no_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  $company_conds $barcode_conds $receieveNo_conds $booking_no_cond $receive_date_cond $receive_year_cond and c.barcode_no in('".implode("','",$barcodeArr)."')";

	ob_start();
	?>
	<script type="text/javascript">
		$('.hide_td_header').hide();
		//$('.hide_td_header').fadeOut();
	</script>
	<div style="width:1380px;">
		<table width="1380" border="1" rules="all" class="rpt_table">
			<thead>
		        <tr>
		            <th width="35">SL</th>
                    <th width="75">&nbsp;</th>
		            <th width="100">Barcode</th>
					<th width="100">System Id</th>	
					<th width="85">Booking/ Programm No</th> 	
					<th width="80">Production Basis</th> 	
					<th width="80">Knitting Source</th> 	
					<th width="80">Production date</th> 	
					<th width="80">Rcv. Challan No.</th> 	
					<th width="80">Service Booking No.</th> 	
					<th width="80">Product Id</th>  	
					<th width="80">Year</th> 	
					<th width="80">Job No</th> 	
					<th width="80">Buyer</th> 	
					<th width="80">Order/FSO No</th>  	
					<th width="80">Fabric Color</th>  	
					<th width="80">Construction</th>  	
					<th width="80">Composition</th>  	
					<th width="80">GSM</th>  	
					<th width="80">Dia</th>  	
					<th width="80">Roll No</th>  	
					<th width="80">Machine Name</th>  	
					<th width="80">Shift Name</th>  	
					<th width="80">Production Qty</th> 	
					<th width="80">QC Pass Qty</th>
					
		        </tr>
		    </thead>
			<? 
			$i = 1;
			$po_ids_arr = array();
			foreach ($data_array as $row) 
			{			  
				if($barcodeArr[$row[csf('barcode_no')]]!="")
				{
					$booking_no_id = $row[csf('booking_no')];
					$sales_order_no = "";
					$is_salesOrder = $row[csf("is_sales")];

					if ($row[csf("knitting_source")] == 1) {
						$knit_company = $company_name_array[$row[csf("knitting_company")]];
					} else if ($row[csf("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}
					$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]), 2, '.', '');

					if ($row[csf("booking_without_order")] != 1) {
						if ($is_salesOrder == 1) {
							// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
							$sales_id = $row[csf("po_breakdown_id")];
							$within_group = $sales_arr[$sales_id]["within_group"];	
							if($within_group == 1){					
								$booking_no = $sales_arr[$sales_id]["sales_booking_no"];

								$sales_order_no = $sales_arr[$sales_id]["job_no"];
								$job_no = $po_details_array[$booking_no]['job_no'];
								$po_id = $row[csf("po_breakdown_id")];
								$po_no = $sales_arr[$sales_id]["job_no"];
								$buyer_id = $po_details_array[$booking_no]['buyer_name'];
								$year = $po_details_array[$booking_no]['year'];
							}else{
								$sales_order_no = $sales_arr[$sales_id]["job_no"];
								$job_no = "";
								$po_id = $sales_id;
								$po_no = $sales_order_no;
								$buyer_id = "";
								$year = "";
							}
						} else {
							$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
							$po_id = $row[csf("po_breakdown_id")];
							$year = $po_details_array[$po_id]['year'];
							$job_no = $po_details_array[$po_id]['job_no'];
							$buyer_id = $po_details_array[$po_id]['buyer_name'];
							$po_no=$po_details_array[$po_id]['po_number'];
						}
					} else {
						// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO	
						if ($is_salesOrder == 1) {
							$sales_id = $row[csf("po_breakdown_id")];
							$sales_order_no = $sales_arr[$sales_id]["job_no"];
							$within_group = $sales_arr[$sales_id]["within_group"];
							$po_id = $sales_id;
						} else {
							$po_id = $row[csf("po_breakdown_id")];
						}
					}
					$color = '';
					$color_id = explode(",", $row[csf('color_id')]);
					foreach ($color_id as $val) {
						if ($val > 0) $color .= $color_arr[$val] . ",";
					}
					$color = chop($color, ',');

					if($row[csf('receive_basis')]==1){
						$booking_no = $row[csf('booking_no')];
					}elseif ($row[csf('receive_basis')]==2) {
						$booking_no = $plan_array[$row[csf('booking_no')]]['booking_no']." / ".$row[csf('booking_no')];
					}elseif ($row[csf('receive_basis')]==4) {
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
					}
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

					if($qcBarcodeArr[$row[csf('barcode_no')]]!="")
					{
						$bgcolor="#FFFGGF";
					}
					?> 
				    <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">

				    	<td align="center" width="35"><? echo $i;?></td>
				        <td>
				    	<input id="knit_defect" name="knit_defect" class="formbuttonplasminus" style="width:70px;" value="QC Result" onClick="fn_knit_defect(<?php echo $row[csf("dtls_id")]; ?>,<?php echo $row[csf('barcode_no')]; ?> )" type="button">
				    	</td>
				    	<td width="100"><? echo $row[csf('barcode_no')]; ?></td>
				    	<td width="100"><? echo $row[csf("recv_number")]; ?></td>
				    	<td width="100"><? echo $booking_no; ?></td>
				    	<td width="100"><? echo $receive_basis[$row[csf("receive_basis")]]; ?></td>
				    	<td><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td>
				    	<td><? echo change_date_format($row[csf("receive_date")]) ?></td>
				    	<td><? echo $row[csf("challan_no")]; ?></td>
				    	<td><? echo $row[csf("service_booking_no")]; ?></td>
				    	<td><? echo $row[csf("prod_id")]; ?></td>
				    	<td><? echo $year; ?></td>
				    	<td><? echo $job_no; ?></td>
				    	<td><? echo $buyer_name_array[$buyer_id]; ?></td>
				    	<td><? echo $po_id; ?></td>
				    	<td><? echo $color_arr[$row[csf('color_id')]]?></td>
				    	<td><? echo $constructtion_arr[$row[csf("febric_description_id")]];?></td>
				    	<td><? echo $composition_arr[$row[csf("febric_description_id")]];?></td>
				    	<td><? echo $row[csf("gsm")]; ?></td>
				    	<td><? echo $row[csf("width")]; ?></td>
				    	<td><? echo $row[csf("roll_no")]; ?></td>
				    	<td><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
				    	<td><? echo $shift_name[$row[csf('shift_name')]]; ?></td>
				    	<td><? echo $prodQnty; ?></td>
				    	<td><? echo $row[csf("qnty")]; ?></td>	
				    </tr>
					<? 
					$i++;
					
				}
			}
			?>
		    </table>
		    <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
		    <tfoot>
		        <th width="23" style="word-break: break-all;">&nbsp;</th>
		    </tfoot>
		</table>
		</div>
	</div> <!-- // end main div -->
	<?
	$html=ob_get_contents();	
	ob_end_clean();	
				
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename"; 
	exit();
		
}


if ($action == "knit_defect_popup") 
{
	echo load_html_head_contents("Order Search", "../../", 1, 1, $unicode);
	extract($_REQUEST);

	//echo $update_dtls_id."**".$roll_maintained."**".$company_id."**".test;die;
	
	$variable_data = sql_select("select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  variable_list=36 order by company_name,get_upvalue_first asc"); //company_name=$company_id and
	//echo "select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  variable_list=36 order by company_name,get_upvalue_first asc";
	$exc_perc = array();
	$i = 0;
	$variable_data_count = count($variable_data);
	foreach ($variable_data as $row) 
	{
		if ($exp[$row[csf("company_name")]] == '') $i = 0;
		$exc_perc[$row[csf("company_name")]]['limit'][$i] = $row[csf("get_upvalue_first")] . "__" . $row[csf("get_upvalue_second")];
		$exc_perc[$row[csf("company_name")]]['grade'][$i] = $row[csf("fabric_grade")];
		$i++;
		$exp[$row[csf("company_name")]] = 1;
	}
	//print_r($exc_perc);
	//$js_variable_data_arr=json_encode($exc_perc);

	echo load_html_head_contents("Grey Production Entry", "../../", 1, 1, $unicode, '', '');
	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");

	/*$machine_dia_arr=return_library_array("select id, dia_width from lib_machine_name","id","dia_width");
	$color_arr=return_library_array("select id, color_name from  lib_color","id","color_name");
	$yarn_count_arr=return_library_array("select id, yarn_count from  lib_yarn_count","id","yarn_count");
	$supplier_arr=return_library_array("select a.lot, b.short_name from product_details_master a, lib_supplier b where a.supplier_id=b.id and a.item_category_id=1","lot","short_name");*/

	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	if ($roll_maintained == 1) 
	{

		/*$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and b.id=$update_dtls_id");
		$roll_dtls_data_arr=array();
		foreach($data_array as $row)
		{
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["company_id"]=$row[csf("company_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["knitting_source"]=$row[csf("knitting_source")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["knitting_company"]=$row[csf("knitting_company")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["dtls_id"]=$row[csf("dtls_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["prod_id"]=$row[csf("prod_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["febric_description_id"]=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf('febric_description_id')]];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["machine_no_id"]=$row[csf("machine_no_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["gsm"]=$row[csf("gsm")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["width"]=$row[csf("width")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["color_id"]=$row[csf("color_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["yarn_lot"]=$row[csf("yarn_lot")];

			$roll_dtls_data_arr[$row[csf("barcode_no")]]["yarn_count"]=$row[csf("yarn_count")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["roll_id"]=$row[csf("roll_id")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["roll_no"]=$row[csf("roll_no")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["qnty"]=$row[csf("qnty")];
		}*/
	} 
	else 
	{

		$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.grey_receive_qnty as qnty
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b 
		WHERE a.id=b.mst_id and a.entry_form in(2) and b.id=$update_dtls_id");

		$roll_dtls_data_arr = array();
		foreach ($data_array as $row) 
		{

			$constraction_comp = $constructtion_arr[$row[csf("febric_description_id")]] . " " . $composition_arr[$row[csf('febric_description_id')]];
			$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $row[csf("machine_no_id")], "dia_width");
			$gsm = $row[csf("gsm")];
			$width = $row[csf("width")];
			$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
			$all_color = "";
			foreach ($color_id_arr as $color_id) {
				$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
			}
			$all_color = chop($all_color, ",");

			$yarn_count_arr = array_unique(explode(",", $row[csf("yarn_count")]));
			$all_yarn_count = "";
			foreach ($yarn_count_arr as $count_id) {
				$all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
			}
			$all_yarn_count = chop($all_yarn_count, ",");
			$yarn_lot = $row[csf("yarn_lot")];
			$qnty = $row[csf("qnty")];
			$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
			foreach ($lot_arr as $lot) {
				$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
				$all_supplier .= $supplier_arr[$supplier_id] . ",";
			}
			$all_supplier = chop($all_supplier, ",");
		}
		$disable = "disabled";
	}
	?>
	<script>
		var pemission = '<? echo $_SESSION['page_permission']; ?>';
		var exc_perc =<? echo json_encode($exc_perc); ?>;
		
        function fabric_grading(comp, point) {
            //alert(comp)
            var newp = exc_perc[comp]["limit"];
            newp = JSON.stringify(newp);
            var newstr = newp.split(",");
            for (var m = 0; m < newstr.length; m++) {
            	var limit = exc_perc[comp]["limit"][m].split("__");
            	if ((limit[1] * 1) == 0 && (point * 1) >= (limit[0] * 1)) {
            		return ( exc_perc[comp]["grade"][m]);
            	}
            	if ((point * 1) >= (limit[0] * 1) && (point * 1) <= (limit[1] * 1)) {
            		return exc_perc[comp]["grade"][m];
            	}
                // alert( newstr[m]+"=="+m)
            }
            return '';
        }

        var roll_maintain = '<? echo $roll_maintained; ?>';
        function fn_barcode() {
        	var dtls_id = $('#hide_dtls_id').val();
        	var roll_maintain = $('#hide_roll_maintain').val();
        	if (dtls_id == "" && roll_maintain != 1) {
        		alert("Select Data First.");
        		return;
        	}
        	else {
        		var title = 'Barcode Or Details Info';
        		var page_link = 'knitting_production_qc_result_controller.php?update_dtls_id=' + dtls_id + '&roll_maintained=' + roll_maintain + '&action=barcode_defect_popup';
        		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
        		emailwindow.onclose = function () {
        			var bar_code_ref = this.contentDoc.getElementById("hide_barcode_id").value.split("**");
        			if (bar_code_ref[1] != "") {
        				get_php_form_data(bar_code_ref[0], 'barcode_roll_find', 'knitting_production_qc_result_controller');
        			}
        		}
        	}
        }


        if (roll_maintain == 1) {
        	$('#txt_barcode').live('keydown', function (e) {
        		if (e.keyCode === 13) {
        			e.preventDefault();
        			var bar_code = $('#txt_barcode').val();
        			get_php_form_data(bar_code, 'barcode_roll_find', 'knitting_production_qc_result_controller');
        		}
        	});

        	$(document).ready(function (e) {
        		var roll_maintain = $('#hide_roll_maintain').val() * 1;
        		if (roll_maintain > 0) {
        			$('#txt_barcode').focus();
        		}
        		else {
        			$('#txt_qc_name').focus();
        		}

        	});
        }

        function caculate_roll_length() {
        	var roll_weight = $('#txt_roll_weight').val() * 1;
        	var roll_width = $('#txt_roll_width').val() * 1;
        	var gsm = $('#txt_gsm').val() * 1;
        	var roll_length = ((roll_weight * 1000) / (gsm * roll_width * 0.0254) * 1.09361);
        	$('#txt_roll_length').val(number_format(roll_length, 4, '.', ''));
        }

        function fn_panelty_point(i) {

        	var defect_count = $('#defectcount_' + i).val() * 1;
        	var found_inche = $('#foundInche_' + i).val() * 1;
        	var company_id = $('#company_id').val();
        	var found_inche_calc = "";
        	if (found_inche == 1) found_inche_calc = 1;
        	else if (found_inche == 2) found_inche_calc = 2;
        	else if (found_inche == 3) found_inche_calc = 3;
        	else if (found_inche == 4) found_inche_calc = 4;
        	else if (found_inche == 5) found_inche_calc = 2;
        	else if (found_inche == 6) found_inche_calc = 4;
        	var penalty_val = defect_count * found_inche_calc;
        	$('#penaltycount_' + i).val(penalty_val);
        	var ddd = {dec_type: 4, comma: 0, currency: ''}
        	var numRow = $('table#dtls_part tbody tr').length;
        	math_operation("total_penalty_point", "penaltycount_", "+", numRow, ddd);
        	var penalty_ratio = (($('#total_penalty_point').val() * 1) * 36 * 100) / (($('#txt_roll_length').val() * 1) * ($('#txt_roll_width').val() * 1));
        	$('#total_point').val(number_format(penalty_ratio, 4, '.', ''));
            //alert(penalty_ratio);
            /*if(penalty_ratio<21) fab_grade="A";
             else if(penalty_ratio<29 && penalty_ratio>20) fab_grade="B";
             else fab_grade="Reject";*/

             $('#fabric_grade').val(fabric_grading(company_id, penalty_ratio));
         }
         function generate_report_file(data,action,page)
         {
         	window.open("knitting_production_qc_result_controller.php?data=" + data+'&action='+action, true );
         }

        function fnc_grey_defect_entry(operation) {

         	if (operation == 2) {
         		show_msg('13');
         		return;
         	}

         	if(operation == 4)
         	{	
         		generate_report_file($('#update_id').val() ,
         			'KnittingProductionPrint', 'requires/knitting_production_qc_result_controller');
         		return;
         	}

         	if (form_validation('txt_roll_length*txt_qc_date*cbo_roll_status*fabric_grade', 'Roll Length*QC Date*Roll Status*Fabric Grade') == false) {
         		return;
         	}
         	var table_length = $('#dtls_part tbody tr').length;
         	var data_string = "";
         	var k = 1;
         	var count_tbl_length = 0;
         	for (var i = 1; i <= table_length; i++) {
         		var defect_name = $('#defectId_' + i).val();
         		var defect_count = $('#defectcount_' + i).val();
         		var found_in_inche = $('#foundInche_' + i).val();
         		var found_inche_val = "";
         		var penalty_point = $('#penaltycount_' + i).val() * 1;

         		if (penalty_point > 0) {
         			if (found_in_inche == 5) found_inche_val = 2;
         			else if (found_in_inche == 6) found_inche_val = 4;
         			else found_inche_val = found_in_inche;
         			data_string += '&defectId_' + k + '=' + defect_name + '&defectcount_' + k + '=' + defect_count + '&foundInche_' + k + '=' + found_in_inche + '&foundIncheVal_' + k + '=' + found_inche_val + '&penaltycount_' + k + '=' + penalty_point;
         			count_tbl_length++;
         			k++;

         		}
         	}
         	data_string = data_string + '&count_tbl_length=' + count_tbl_length;


         	var data = "action=save_update_delete_defect&operation=" + operation + get_submitted_data_string('hide_dtls_id*company_id*hide_roll_maintain*txt_barcode*txt_roll_no*roll_id*txt_qc_name*txt_roll_width*txt_roll_weight*txt_roll_length*txt_reject_qnty*hid_reject_qnty*txt_qc_date*total_penalty_point*total_point*fabric_grade*fabric_comments*cbo_roll_status*txt_knitting_density*update_id', "../../") + data_string;
            //alert(data);return;
            //alert(data);
            freeze_window(operation);

            http.open("POST", "knitting_production_qc_result_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_grey_defect_entry_response;
        }

        function fnc_grey_defect_entry_response() {
        	if (http.readyState == 4) {
                //release_freezing();return;
                var reponse = trim(http.responseText).split('**');
                if (reponse[0] == 20) {
                	alert(reponse[1]);
                	release_freezing();
                	return;
                }
                show_msg(reponse[0]);
                if ((reponse[0] == 0 || reponse[0] == 1)) {
                	document.getElementById('update_id').value = reponse[1];
                	var prod_dtls_id = $('#hide_dtls_id').val();
                	$('#dtls_list_container').html("");
                	show_list_view(prod_dtls_id, 'show_qc_listview', 'dtls_list_container', 'knitting_production_qc_result_controller', '');
                	set_button_status(0, pemission, 'fnc_grey_defect_entry', 1);
                	$('#master_part').find('input', 'select').val("");
                	release_freezing();
                }
                else {
                	release_freezing();
                }

            }
        }

        function fn_recet_details() {
        	$('#dtls_part').find('input').val("");
        	$('#dtls_part').find('select').val(0);
        }

        function reject_status(type)
        {
        	//alert(type);
        	if (type==3) // reject
        	{
        		var roll_weight = $('#txt_roll_weight').val();
        		$('#txt_reject_qnty').val(roll_weight);
        	}
        	else // QC Pass, Held Up
        	{
        		//$('#txt_reject_qnty').val('');
        	}
        }

        /*$(function(){  
        	// alert(typeof(roll_weight));      	
        	$('#txt_reject_qnty').keyup(function(e) 
        	{
        		var roll_weight = parseInt($('#txt_roll_weight').val());
        		var Reject_Qty = parseInt($(this).val());
        		//alert(roll_weight+'='+Reject_Qty);   
        		if (roll_weight < Reject_Qty) 
        		{
        			e.preventDefault();
        			alert('Over Quantity Not Allowed');
        			$(this).val(roll_weight);
        			// return;
        		}
        	});
        });*/
        
        $(function() {
        	$('#txt_reject_qnty').keyup(function(e)
        	{
        	    var roll_weight = parseInt($('#txt_roll_weight').val());
        	    var Reject_Qty = parseInt($(this).val());
                if ($(this).val() > roll_weight)
                {
                   e.preventDefault();     
                   $(this).val(roll_weight);
                   alert('Over Qty Not Alowed');
                }
            });
        });
    </script>
    <body onLoad="set_hotkey()">
    	<? echo load_freeze_divs("../../", $permission); ?>
    	<form name="defectQcResult_1" id="defectQcResult_1" autocomplete="off">
    		<div style="width:1160px">
    			<input type="hidden" id="hide_dtls_id" value="<? echo $update_dtls_id; ?>"/>
    			<input type="hidden" id="hide_roll_maintain" value="<? echo $roll_maintained; ?>"/>
    			<input type="hidden" id="company_id" value="<? echo $company_id; ?>"/>
    			<table width="1100" border="0">
    				<tr>
    					<td width="400" valign="top">
    						<table cellpadding="0" cellspacing="0" border="1" width="400" class="rpt_table" rules="all"
    						id="master_part">
    						<tr bgcolor="#E9F3FF">
    							<td width="200">Barcode Number</td>
    							<td align="center"><input type="text" id="txt_barcode" name="txt_barcode"
    								class="text_boxes" style="width:150px;"
    								onDblClick="fn_barcode()"
    								placeholder="Browse or Scan" <? echo $disable; ?> ></td>
    							</tr>
    							<tr bgcolor="#FFFFFF">
    								<td>Roll Number</td>
    								<td align="center">
    									<input type="text" id="txt_roll_no" name="txt_roll_no" class="text_boxes"
    									style="width:150px;" readonly placeholder="Display" <? echo $disable; ?> >
    									<input type="hidden" id="roll_id" name="roll_id">
    								</td>
    							</tr>
    							<tr bgcolor="#FFFFFF">
    								<td>QC Date</td>
    								<td align="center"><input type="text" id="txt_qc_date" name="txt_qc_date"
    									class="datepicker" style="width:150px;" value="<? echo date("d-m-Y")?>"></td>
    								</tr>
    								<tr bgcolor="#E9F3FF">
    									<td>QC Name</td>
    									<td align="center"><input type="text" id="txt_qc_name" name="txt_qc_name"
    										class="text_boxes" style="width:150px;" placeholder="write">
    									</td>
    								</tr>
    								<tr bgcolor="#FFFFFF">
    									<td>Roll Width (inch)</td>
    									<td align="center"><input type="text" id="txt_roll_width" name="txt_roll_width"
    										class="text_boxes_numeric" style="width:150px;"
    										placeholder="write" onBlur="caculate_roll_length();"></td>
    									</tr>
    									<tr bgcolor="#E9F3FF">
    										<td>Roll Wgt. (Kg)</td>
    										<td align="center"><input type="text" id="txt_roll_weight" name="txt_roll_weight"
    											class="text_boxes_numeric" style="width:150px;" readonly
    											placeholder="Display" value="<? echo $qnty; ?>"></td>
    										</tr>
    										<tr bgcolor="#FFFFFF">
    											<td>Roll Length (Yds)</td>
    											<td align="center" title="((roll_weight * 1000) / (gsm * roll_width * 0.0254) * 1.09361)"><input type="text" id="txt_roll_length" name="txt_roll_length"
    												class="text_boxes_numeric" style="width:150px;"
    												placeholder="Display"></td>
    											</tr>
    											<tr bgcolor="#E9F3FF">
    												<td>Reject Qty</td>
    												<td align="center"><input type="text" id="txt_reject_qnty" name="txt_reject_qnty"
    													class="text_boxes_numeric" style="width:150px;"
    													placeholder="write"></td>
    												<input type="hidden" id="hid_reject_qnty" name="hid_reject_qnty">	
    												</tr>
    												<tr bgcolor="#FFFFFF">
    													<td>Construction & Composition</td>
    													<td align="center"><input type="text" id="txt_constract_comp" name="txt_constract_comp"
    														class="text_boxes" style="width:150px;" readonly
    														placeholder="Display" value="<? echo $constraction_comp; ?>">
    													</td>
    												</tr>
    												<tr bgcolor="#FFFFFF">
    													<td>FB GSM</td>
    													<td align="center"><input type="text" id="txt_fb_gsm" name="txt_fb_gsm"
    														class="text_boxes" style="width:150px;" readonly
    														placeholder="Display" value="<? echo $fb_gsm; ?>">
    													</td>
    												</tr>
    												<tr bgcolor="#E9F3FF">
    													<td>GSM</td>
    													<td align="center"><input type="text" id="txt_gsm" name="txt_gsm" class="text_boxes"
    														style="width:150px;" readonly placeholder="Display"
    														value="<? echo $gsm; ?>"></td>
    												</tr>
													<tr bgcolor="#FFFFFF">
														<td>Dia</td>
														<td align="center"><input type="text" id="txt_dia" name="txt_dia" class="text_boxes"
															style="width:150px;" readonly placeholder="Display"
															value="<? echo $width; ?>"></td>
													</tr>
													<tr bgcolor="#E9F3FF">
														<td>M/C Dia</td>
														<td align="center"><input type="text" id="txt_mc_dia" name="txt_mc_dia"
														class="text_boxes" style="width:150px;" readonly
														placeholder="Display" value="<? echo $machine_dia; ?>"></td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td>Actual Knitting Density</td>
														<td align="center">
														<p>
															<input type="text" id="txt_knitting_density" name="txt_knitting_density" class="text_boxes_numeric" style="width:150px;" placeholder="write" value="<? echo $knitting_density; ?>">
														</p>
														</td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td>Color</td>
														<td align="center"><input type="text" id="txt_color" name="txt_color" class="text_boxes"
															style="width:150px;" readonly placeholder="Display"
															value="<? echo $all_color; ?>"></td>
													</tr>
													<tr bgcolor="#E9F3FF">
														<td>Yarn Count</td>
														<td align="center"><input type="text" id="txt_yarn_count" name="txt_yarn_count"
															class="text_boxes" style="width:150px;" readonly
															placeholder="Display" value="<? echo $all_yarn_count; ?>">
														</td>
													</tr>
													<tr bgcolor="#FFFFFF">
														<td>Yarn Lot</td>
														<td align="center"><input type="text" id="txt_yarn_lot" name="txt_yarn_lot"
															class="text_boxes" style="width:150px;" readonly
															placeholder="Display" value="<? echo $yarn_lot; ?>"></td>
													</tr>
    																	<tr bgcolor="#E9F3FF">
    																		<td>Spinning Mill</td>
    																		<td align="center"><input type="text" id="txt_spning_mill" name="txt_spning_mill"
    																			class="text_boxes" style="width:150px;" readonly
    																			placeholder="Display" value="<? echo $all_supplier; ?>"></td>
    																		</tr>
    																	<tr bgcolor="#FFFFFF">
																		<td>Roll Status</td>
																		<td align="center">
																			<p><? $roll_status = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');
																			echo create_drop_down("cbo_roll_status", 152, $roll_status, "", 1, "-- Select --", 0, "reject_status(this.value);", ''); ?></p>
																		</td>
																		</tr>
    																	</table>
    																</td>
    																<td width="50">&nbsp;</td>
    																<td width="600">
    																	<table cellpadding="0" cellspacing="0" border="1" width="600" class="rpt_table" rules="all">
    																		<tr>
    																			<td colspan="5" align="center"><input type="button" id="reset_details"
    																				class="formbuttonplasminus"
    																				value="Reset Defect Counter" style="width:200px;"
    																				onClick="fn_recet_details();"></td>
    																			</tr>
    																		</table>

    																		<table cellpadding="0" cellspacing="0" border="1" width="600" class="rpt_table" rules="all"
    																		id="dtls_part">
    																		<thead>
    																			<tr>
    																				<th width="50">SL</th>
    																				<th width="150">Defect Name</th>
    																				<th width="100">Defect Count</th>
    																				<th width="150">Found in (Inch)</th>
    																				<th>Penalty Point</th>
    																			</tr>
    																		</thead>
    																		<tbody>
    																			<?
    																			$i = 1;
    																			foreach ($knit_defect_array as $defect_id => $val) {
    																				if ($i % 2 == 0)
    																					$bgcolor = "#E9F3FF";
    																				else
    																					$bgcolor = "#FFFFFF";
    																				?>
    																				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
    																					<td align="center"><? echo $i; ?></td>
    																					<td><p><? echo $val; ?></p>
    																						<input type="hidden" id="defectId_<? echo $i; ?>" name="defectId[]" class="defectId" value="<? echo $defect_id; ?>">
    																						<input type="hidden" class="UpdefectId" id="UpdefectId_<? echo $i; ?>" name="UpdefectId[]"
    																						value="">
    																					</td>
    																					<td><p><input type="text" id="defectcount_<? echo $i; ?>" name="defectcount[]"
    																						class="text_boxes_numeric" style="width:90px"
    																						onBlur="fn_panelty_point(<? echo $i; ?>)"></p></td>
    																						<?
    																						if ($defect_id == 1) $defect_show = '5,6'; else $defect_show = '1,2,3,4';
    																						?>
    																						<td>
    																							<p><? echo create_drop_down("foundInche_" . $i, 152, $knit_defect_inchi_array, "", 1, "-- Select --", 0, "fn_panelty_point(" . $i . ")", '', $defect_show); ?></p>
    																							<input type="hidden" id="foundInchePoint_<? echo $i; ?>"
    																							name="foundInchePoint[]" value="">
    																						</td>
    																						<td><p><input type="text" id="penaltycount_<? echo $i; ?>" name="penaltycount[]"
    																							class="text_boxes_numeric" style="width:130px" readonly></p></td>
    																						</tr>
    																						<?
    																						$i++;
    																					}
    																					?>
    																				</tbody>
    																				<tfoot>
    																					<tr bgcolor="#CCCCCC">
    																						<td colspan="4" align="right">Total Penalty Point: &nbsp;</td>
    																						<td align="center"><input type="text" class="text_boxes_numeric"
    																							id="total_penalty_point" name="total_penalty_point"
    																							style="width:130px" readonly></td>
    																						</tr>
    																						<tr bgcolor="#CCCCCC">
    																							<td colspan="4" align="right">Total Point: &nbsp;</td>
    																							<td align="center"><input type="text" class="text_boxes_numeric" id="total_point"
    																								name="total_point" style="width:130px" readonly></td>
    																							</tr>
    																							<tr bgcolor="#CCCCCC">
    																								<td colspan="4" align="right">Fabric Grade: &nbsp;</td>
    																								<td align="center"><input type="text" class="text_boxes" id="fabric_grade"
    																									name="fabric_grade" style="width:130px" readonly></td>
    																								</tr>
    																								<tr>
    																									<td>Comments</td>
    																									<td colspan="4"><input type="text" class="text_boxes" id="fabric_comments"
    																										name="fabric_comments" style="width:98%"></td>
    																									</tr>
    																								</tfoot>
    																							</table>
    																						</td>
    																					</tr>
    																					<tr>
    																						<td colspan="3">&nbsp;</td>
    																					</tr>
    																					<tr>
    																						<td colspan="3" align="center" class="button_container">
    																							<?
						echo load_submit_buttons($permission, "fnc_grey_defect_entry", 0, 1, "reset_form('','','','')", 1);//set_auto_complete(1);
						?>
						<input type="hidden" id="update_id" name="update_id"/>
					</td>
				</tr>
			</table>
			<div id="dtls_list_container" style="margin-top:5px;" align="center">
				<?
				$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$update_dtls_id and status_active = 1");
				if (count($sql_dtls) > 0) 
				{
					?>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th width="100">Roll No</th>
								<th width="100">Barcode</th>
								<th width="100">Penalty Point</th>
								<th width="100">Total Point</th>
								<th width="100">Fabric Grade</th>
								<th>Comments</th>
							</tr>
						</thead>
						<tbody>
							<?
							$i = 1;
							foreach ($sql_dtls as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"
									onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "knitting_production_qc_result_controller" );'
									style="cursor:pointer;">
									<td align="center"><? echo $i; ?></td>
									<td align="center"><? echo $row[csf("roll_no")]; ?></td>
									<td align="center"><? echo $row[csf("barcode_no")]; ?></td>
									<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
									<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
									<td><? echo $row[csf("fabric_grade")]; ?></td>
									<td><? echo $row[csf("comments")]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>
                        <!--<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>-->
					</table>
					<?
				}
				?>
			</div>
			</div>
		</form>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

	<script> 
		var barcode_no = '<?php echo trim($barcode_no); ?>';
	    if(barcode_no!="")
		{
			get_php_form_data(barcode_no, 'barcode_roll_find', 'knitting_production_qc_result_controller');
		}
	</script>
	</html>
	<?
	exit();
}


if ($action == "barcode_defect_popup") {
	extract($_REQUEST);
	echo load_html_head_contents("Barcode Popup", "../../", 1, 1, '', '', '');
	
	?>
	<script>
		function js_set_value(str) {
			$('#hide_barcode_id').val(str);
			parent.emailwindow.hide();
			
		}
	</script>
	<?

	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$sql = "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping 
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.id=$update_dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0";

	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<input type="hidden" id="hide_barcode_id"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="150">Fabric Description</th>
			<th width="100">Job No</th>
			<th width="110">Order No</th>
			<th width="110">Location</th>
			<th width="70">File NO</th>
			<th width="70">Ref No</th>
			<th width="70">Shipment Date</th>
			<th width="90">Barcode No</th>
			<th width="50">Roll No</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:960px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				id="search<? echo $i; ?>"
				onClick="js_set_value('<? echo $row[csf('barcode_no')] . "**" . $row[csf('roll_id')]; ?>')">
				<td width="30" align="center">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>"
					value="<?php echo $row[csf('barcode_no')]; ?>"/>
				</td>
				<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
				<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
				<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
				<td width="110" align="center"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
				<td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
				<td width="70" align="center"><? echo $row[csf('grouping')]; ?></td>
				<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
				<td width="90"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
				<td width="50" align="center"><? echo $row[csf('roll_no')]; ?></td>
				<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
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

if ($action == "barcode_roll_find") 
{
	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty, c.booking_no
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.barcode_no=$data");
	foreach ($data_array as $row) {
		//echo "'".$row[csf("booking_no")]."'";die;
		//echo $row[csf("order_id")].';die;
		echo "document.getElementById('txt_barcode').value 				= '" . $row[csf("barcode_no")] . "';\n";
		echo "document.getElementById('txt_roll_no').value 				= '" . $row[csf("roll_no")] . "';\n";
		echo "document.getElementById('roll_id').value 				= '" . $row[csf("roll_id")] . "';\n";
		echo "document.getElementById('txt_roll_weight').value 				= '" . $row[csf("qnty")] . "';\n";
		echo "document.getElementById('txt_constract_comp').value 				= '" . $constructtion_arr[$row[csf("febric_description_id")]] . ' ' . $composition_arr[$row[csf('febric_description_id')]] . "';\n";
		echo "document.getElementById('txt_gsm').value 				= '" . $row[csf("gsm")] . "';\n";
		echo "document.getElementById('txt_dia').value 				= '" . $row[csf("width")] . "';\n";
		$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $row[csf("machine_no_id")], "dia_width");
		echo "document.getElementById('txt_mc_dia').value 				= '" . $machine_dia . "';\n";
		$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
		$all_color = "";
		foreach ($color_id_arr as $color_id) {
			$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
		}
		$all_color = chop($all_color, ",");
		echo "document.getElementById('txt_color').value 				= '" . $all_color . "';\n";
		$yarn_count_arr = array_unique(explode(",", $row[csf("yarn_count")]));
		$all_yarn_count = "";
		foreach ($yarn_count_arr as $count_id) {
			$all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
		}
		$all_yarn_count = chop($all_yarn_count, ",");
		echo "document.getElementById('txt_yarn_count').value 				= '" . $all_yarn_count . "';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '" . $row[csf("yarn_lot")] . "';\n";
		$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
		foreach ($lot_arr as $lot) {
			$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
			$all_supplier .= $supplier_arr[$supplier_id] . ",";
		}
		$all_supplier = chop($all_supplier, ',');
		echo "document.getElementById('txt_spning_mill').value 				= '" . $all_supplier . "';";

		//$fb_gsm = return_field_value("gsm_weight", "wo_booking_mst a, wo_booking_dtls b", "a.booking_no=b.booking_no and a.po_break_down_id=" . "'".$row[csf("order_id")]."'" . "order by a.id", "gsm_weight");
		// Only planning basis FB GSM Show (Page: Planning Info Entry)
		$fb_gsm = return_field_value("gsm_weight", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and b.id=" . "'".$row[csf("booking_no")]."'" . "order by a.id", "gsm_weight");
		echo "document.getElementById('txt_fb_gsm').value 				= '" . $fb_gsm . "';";
	}

	exit();
}



if ($action == "save_update_delete_defect") {
	$process = array(&$_POST);

	extract(check_magic_quote_gpc($process));
	$roll_status_id = str_replace("'", "", $cbo_roll_status);
	//echo $status;die; 
	$variable_settingAutoQC = sql_select("select auto_update from variable_settings_production where company_name =$company_id and variable_list in(47) and item_category_id=13 and is_deleted=0 and status_active=1");

	$autoProductionQuantityUpdatebyQC = $variable_settingAutoQC[0][csf("auto_update")];

	$prod_dtls_id = str_replace("'", "", $hide_dtls_id);
	$barcode_no = str_replace("'", "", $txt_barcode);
	$roll_maintain = str_replace("'", "", $hide_roll_maintain);

	$qnty = str_replace("'", "", $txt_roll_weight);
	$rejectQty = str_replace("'", "", $txt_reject_qnty);
	$hid_reject_qnty = str_replace("'", "", $hid_reject_qnty);
	/*	if ($roll_status_id!=2) 
	{
		$qc_qnty = ($qnty-$rejectQty);
	}
	else
	{
		$qc_qnty=$qnty;
	}*/

	//$qc_qnty = ($roll_status_id!=2) ? ($qnty-$rejectQty) : $qnty;
	$qc_qnty = ($roll_status_id!=2) ? ($qnty-($rejectQty-$hid_reject_qnty)) : $qnty;
	// echo $qc_qnty.'='.$qnty.'-'.$rejectQty.'-'.$hid_reject_qnty;die;

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (str_replace("'", "", $prod_dtls_id) != "") {
			if ($roll_maintain == 1) 
			{
				$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$prod_dtls_id and barcode_no='$barcode_no' and status_active=1 and is_deleted=0");
				if ($pre_count[0][csf("count")] > 0) 
				{
					echo "20**Barcode Number is Already Exists";
					disconnect($con);
					die;
				}
			} else 
			{
				$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0");
				if($pre_count[0][csf("count")] > 0) 
				{
					echo "20**Duplicate Fabric is Not Allow in Same QC.";
					disconnect($con);
					die;
				}
			}
		}
		
		//$id = return_next_id("id", "pro_qc_result_mst", 1);
		$id = return_next_id_by_sequence("PRO_QC_RESULT_MST_SEQ", "pro_qc_result_mst", $con);
		$field_array_mst = "id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, roll_status, knitting_density, total_penalty_point, total_point, fabric_grade, comments, inserted_by, insert_date";

		$data_array_mst = "(" . $id . "," . $hide_dtls_id . "," . $hide_roll_maintain . "," . $txt_barcode . "," . $roll_id . "," . $txt_roll_no . "," . $txt_qc_name . "," . $txt_roll_width . ",'" . $qc_qnty . "'," . $txt_roll_length . ",'" . $rejectQty . "'," . $txt_qc_date . "," . $cbo_roll_status . "," . $txt_knitting_density . "," . $total_penalty_point . "," . $total_point . "," . $fabric_grade . "," . $fabric_comments . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		//echo "insert into pro_qc_result_mst (".$field_array_mst.") values ".$data_array_mst;die;
		$qc_update_id = $id;

		$count_tbl_length = str_replace("'", "", $count_tbl_length);

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, inserted_by, insert_date";
		$data_array_dtls = "";

		for ($i = 0; $i <= $count_tbl_length; $i++) {
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId = 'defectId_' . $i;
			$defectcount = 'defectcount_' . $i;
			$foundInche = 'foundInche_' . $i;
			$foundIncheVal = 'foundIncheVal_' . $i;
			$penaltycount = 'penaltycount_' . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$defectId . "','" . $$defectcount . "','" . $$foundInche . "','" . $$foundIncheVal . "','" . $$penaltycount . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//$dtls_id++;
		}

		//echo "10**insert into pro_qc_result_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID = $rID2 = true;
		if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
		{
			$pro_roll_sql ="UPDATE pro_roll_details SET qnty=$qc_qnty,reject_qnty='$rejectQty' WHERE barcode_no = $txt_barcode AND entry_form=2 and dtls_id=$prod_dtls_id"; 

			$rID3 = execute_query($pro_roll_sql,1);

			if($rID3)
			{
				$roll_qc_rj_result =sql_select("SELECT sum(qnty) as qc_qnty,sum(reject_qnty) as reject_qnty from pro_roll_details where dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0 and entry_form=2");
				if ($roll_qc_rj_result[0][csf('reject_qnty')]=="") 
				{
					$roll_qc_rj_result[0][csf('reject_qnty')]=0;
				}
				$pro_grey_prod_sql ="UPDATE pro_grey_prod_entry_dtls SET grey_receive_qnty=".$roll_qc_rj_result[0][csf('qc_qnty')].",reject_fabric_receive=".$roll_qc_rj_result[0][csf('reject_qnty')]." WHERE id=$prod_dtls_id";

				$rID4 = execute_query($pro_grey_prod_sql,1);
			}

		}

		$rID = sql_insert("pro_qc_result_mst", $field_array_mst, $data_array_mst, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);

		//echo "10**$rID**$rID2";die;
		if ($db_type == 0) {
			if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
			{			
				if ($rID && $rID2 && $rID3 && $rID4) {
					mysql_query("COMMIT");
					echo "0**" . $qc_update_id;
				} else {
					mysql_query("ROLLBACK");
					echo "5**0";
				}
			}else {
				if ($rID && $rID2) {
					mysql_query("COMMIT");
					echo "0**" . $qc_update_id;
				} else {
					mysql_query("ROLLBACK");
					echo "5**0";
				}
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
			{
				if ($rID && $rID2 && $rID3 && $rID4) {
					oci_commit($con);
					echo "0**" . $qc_update_id;
				} else {
					oci_rollback($con);
					echo "5**0";
				}
			}else {
				if ($rID && $rID2) {
					oci_commit($con);
					echo "0**" . $qc_update_id;
				} else {
					oci_rollback($con);
					echo "5**0";
				}
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

		$roll_maintain = str_replace("'", "", $hide_roll_maintain);
		if ($roll_maintain == 1) 
		{
			$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$hide_dtls_id and barcode_no=$txt_barcode and id <> $update_id and status_active=1 and is_deleted=0");
			if ($pre_count[0][csf("count")] > 0) 
			{
				echo "20**Barcode Number is Already Exists.";
				disconnect($con);
				die;
			}
		}

		$field_array_update = "pro_dtls_id*roll_maintain*barcode_no*roll_id*roll_no*qc_name*roll_width*roll_weight*roll_length*reject_qnty*qc_date*roll_status*knitting_density*total_penalty_point*total_point*fabric_grade*comments*update_by*update_date";

		$data_array_update = $hide_dtls_id . "*" . $hide_roll_maintain . "*" . $txt_barcode . "*" . $roll_id . "*" . $txt_roll_no . "*" . $txt_qc_name . "*" . $txt_roll_width . "*" . $qc_qnty . "*" . $txt_roll_length . "*'" . $rejectQty . "'*" . $txt_qc_date . "*" . $cbo_roll_status. "*" . $txt_knitting_density . "*" . $total_penalty_point . "*" . $total_point . "*" . $fabric_grade . "*" . $fabric_comments . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$deleteDetails = execute_query("delete from  pro_qc_result_dtls where mst_id=$update_id");

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, inserted_by, insert_date";
		$data_array_dtls = "";

		for ($i = 1; $i <= $count_tbl_length; $i++) {
			$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
			$defectId = 'defectId_' . $i;
			$defectcount = 'defectcount_' . $i;
			$foundInche = 'foundInche_' . $i;
			$foundIncheVal = 'foundIncheVal_' . $i;
			$penaltycount = 'penaltycount_' . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";

			$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$defectId . "','" . $$defectcount . "','" . $$foundInche . "','" . $$foundIncheVal . "','" . $$penaltycount . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			//$dtls_id++;
		}

		//echo "insert into pro_qc_result_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo "10**".$field_array_update."<br>".$data_array_update;die;

		if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
		{
			
			$pro_roll_sql ="UPDATE pro_roll_details SET qnty=$qc_qnty,reject_qnty='$rejectQty' WHERE barcode_no = $txt_barcode AND entry_form=2 and dtls_id=$prod_dtls_id"; 

			$rID3 = execute_query($pro_roll_sql,1);

			if($rID3)
			{
				$roll_qc_rj_result =sql_select("SELECT sum(qnty) as qc_qnty,sum(reject_qnty) as reject_qnty from pro_roll_details where dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0 and entry_form=2");
				if ($roll_qc_rj_result[0][csf('reject_qnty')]=="") 
				{
					$roll_qc_rj_result[0][csf('reject_qnty')]=0;
				}
				$pro_grey_prod_sql ="UPDATE pro_grey_prod_entry_dtls SET grey_receive_qnty=".$roll_qc_rj_result[0][csf('qc_qnty')].",reject_fabric_receive=".$roll_qc_rj_result[0][csf('reject_qnty')]." WHERE id=$prod_dtls_id";
				// echo $pro_grey_prod_sql;die;
				$rID4 = execute_query($pro_grey_prod_sql,1);
			}
		}

		$rID = sql_update("pro_qc_result_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);

		//echo "10**$rID && $deleteDetails && $rID2 && $rID3 && $rID4";oci_rollback($con);die;
		$qc_update_id = str_replace("'", "", $update_id);

		if ($db_type == 0) {
			if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
			{
				if ($rID && $deleteDetails && $rID2 && $rID3 && $rID4) {
					mysql_query("COMMIT");
					echo "1**" . $qc_update_id;
				} else {
					mysql_query("ROLLBACK");
					echo "6**0";
				}
			}else {
				if ($rID && $deleteDetails && $rID2) {
					mysql_query("COMMIT");
					echo "1**" . $qc_update_id;
				} else {
					mysql_query("ROLLBACK");
					echo "6**0";
				}
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
			{
				if ($rID && $deleteDetails && $rID2 &&  $rID3 && $rID4) {
					oci_commit($con);
					echo "1**" . $qc_update_id;
				} else {
					oci_rollback($con);
					echo "6**0";
				}
			}else {
				if ($rID && $deleteDetails && $rID2 ) {
					oci_commit($con);
					echo "1**" . $qc_update_id;
				} else {
					oci_rollback($con);
					echo "6**0";
				}
			}
		}
		disconnect($con);
		die;
	}
	exit();
}

if ($action == "show_qc_listview") {
	$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$data and status_active=1");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
		<thead>
			<tr>
				<th width="50">SL</th>
				<th width="100">Roll No</th>
				<th width="100">Barcode</th>
				<th width="100">Penalty Point</th>
				<th width="100">Total Point</th>
				<th width="100">Fabric Grade</th>
				<th>Comments</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 1;
			foreach ($sql_dtls as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "knitting_production_qc_result_controller" );'
					style="cursor:pointer;">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $row[csf("roll_no")]; ?></td>
					<td><? echo $row[csf("barcode_no")]; ?></td>
					<td align="right"><? echo number_format($row[csf("total_penalty_point")], 2); ?></td>
					<td align="right"><? echo number_format($row[csf("total_point")], 2); ?></td>
					<td><? echo $row[csf("fabric_grade")]; ?></td>
					<td><? echo $row[csf("comments")]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
        <!--<tfoot>
        	<tr>
            	<th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>-->
    </table>
    <?
    exit();
}

if ($action == "populate_qc_from_grey_recv") {

	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	$sql_qc = sql_select("select id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, roll_status, knitting_density, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where id=$data");

	foreach ($sql_qc as $row) {
		echo "document.getElementById('update_id').value 			= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_barcode').value 			= '" . $row[csf("barcode_no")] . "';\n";
		echo "document.getElementById('txt_roll_no').value 			= '" . $row[csf("roll_no")] . "';\n";
		echo "document.getElementById('roll_id').value 				= '" . $row[csf("roll_id")] . "';\n";
		echo "document.getElementById('txt_qc_name').value 			= '" . $row[csf("qc_name")] . "';\n";
		echo "document.getElementById('cbo_roll_status').value 		= '" . $row[csf("roll_status")] . "';\n";
		echo "document.getElementById('txt_knitting_density').value = '" . $row[csf("knitting_density")] . "';\n";

		echo "document.getElementById('txt_roll_width').value 		= '" . $row[csf("roll_width")] . "';\n";
		echo "document.getElementById('txt_roll_weight').value 		= '" . $row[csf("roll_weight")] . "';\n";
		echo "document.getElementById('txt_roll_length').value 		= '" . $row[csf("roll_length")] . "';\n";

		echo "document.getElementById('txt_reject_qnty').value 		= '" . $row[csf("reject_qnty")] . "';\n";
		echo "document.getElementById('hid_reject_qnty').value 		= '" . $row[csf("reject_qnty")] . "';\n";

		$data_array = sql_select("SELECT  b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count
			FROM  pro_grey_prod_entry_dtls b 
			WHERE b.id=" . $row[csf("pro_dtls_id")]);

		echo "document.getElementById('txt_constract_comp').value 	= '" . $constructtion_arr[$data_array[0][csf("febric_description_id")]] . ' ' . $composition_arr[$data_array[0][csf("febric_description_id")]] . "';\n";
		echo "document.getElementById('txt_gsm').value 				= '" . $data_array[0][csf("gsm")] . "';\n";
		echo "document.getElementById('txt_dia').value 				= '" . $data_array[0][csf("width")] . "';\n";
		$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $data_array[0][csf("machine_no_id")], "dia_width");
		echo "document.getElementById('txt_mc_dia').value 			= '" . $machine_dia . "';\n";

		$color_id_arr = array_unique(explode(",", $data_array[0][csf("color_id")]));
		$all_color = "";
		foreach ($color_id_arr as $color_id) {
			$all_color .= return_field_value("color_name", "lib_color", "id='$color_id'", "color_name") . ",";
		}
		$all_color = chop($all_color, ",");
		echo "document.getElementById('txt_color').value 			= '" . $all_color . "';\n";
		$yarn_count_arr = array_unique(explode(",", $data_array[0][csf("yarn_count")]));
		$all_yarn_count = "";
		foreach ($yarn_count_arr as $count_id) {
			$all_yarn_count .= return_field_value("yarn_count", "lib_yarn_count", "id='$count_id'", "yarn_count") . ",";
		}
		$all_yarn_count = chop($all_yarn_count, ",");
		echo "document.getElementById('txt_yarn_count').value 		= '" . $all_yarn_count . "';\n";
		echo "document.getElementById('txt_yarn_lot').value 		= '" . $data_array[0][csf("yarn_lot")] . "';\n";
		$lot_arr = array_unique(explode(",", $data_array[0][csf("yarn_lot")]));
		foreach ($lot_arr as $lot) {
			$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
			$all_supplier .= $supplier_arr[$supplier_id] . ",";
		}
		$all_supplier = chop($all_supplier, ',');

		// $sup=return_field_value("supplier_id", "lib_supplier", "id=1");
		// return_field_value("buyer_name", "lib_buyer", "id=1");

		echo "document.getElementById('txt_spning_mill').value 				= '" . $all_supplier . "';\n";
		echo "document.getElementById('txt_qc_date').value 				= '" . change_date_format($row[csf("qc_date")]) . "';\n";

		$dtls_part_tbody_data = "";
		$dtls_sql = sql_select("select id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point from pro_qc_result_dtls where mst_id=" . $row[csf('id')] . " and status_active=1 and is_deleted=0");
		$dtls_data_arr = array();
		foreach ($dtls_sql as $dtls_row) {
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["dtls_id"] = $dtls_row[csf("id")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["defect_name"] = $dtls_row[csf("defect_name")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["defect_count"] = $dtls_row[csf("defect_count")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["found_in_inch"] = $dtls_row[csf("found_in_inch")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["found_in_inch_point"] = $dtls_row[csf("found_in_inch_point")];
			$dtls_data_arr[$dtls_row[csf("defect_name")]]["penalty_point"] = $dtls_row[csf("penalty_point")];
		}
		$i = 1;
		echo "$('#dtls_part').find('input').not('.defectId, .UpdefectId').val('');\n";
		foreach ($knit_defect_array as $defect_id => $val) {
			if ($dtls_data_arr[$defect_id]["defect_name"] > 0) {
				echo "document.getElementById('UpdefectId_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_name"] . "';\n";
				echo "document.getElementById('defectcount_$i').value 				= '" . $dtls_data_arr[$defect_id]["defect_count"] . "';\n";
				echo "document.getElementById('foundInche_$i').value 				= '" . $dtls_data_arr[$defect_id]["found_in_inch"] . "';\n";
				echo "document.getElementById('foundInchePoint_$i').value 				= '" . $dtls_data_arr[$defect_id]["found_in_inch_point"] . "';\n";
				echo "document.getElementById('penaltycount_$i').value 				= '" . $dtls_data_arr[$defect_id]["penalty_point"] . "';\n";
			}
			$i++;
		}
		echo "document.getElementById('total_penalty_point').value 				= '" . $row[csf("total_penalty_point")] . "';\n";
		echo "document.getElementById('total_point').value 				= '" . $row[csf("total_point")] . "';\n";
		echo "document.getElementById('fabric_grade').value 				= '" . $row[csf("fabric_grade")] . "';\n";
		echo "document.getElementById('fabric_comments').value 				= '" . $row[csf("comments")] . "';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_grey_defect_entry',1);\n";
		exit();
	}
}


if ($action == 'KnittingProductionPrint') {
	
		$sqls_mst = "SELECT a.id, a.pro_dtls_id, a.roll_maintain, a.barcode_no, a.roll_id, a.roll_no, a.qc_name, a.roll_width, a.roll_weight, a.roll_length, a.reject_qnty, a.qc_date, a.roll_status, a.total_penalty_point, a.total_point, a.fabric_grade, a.comments,b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count FROM  pro_qc_result_mst a,   pro_grey_prod_entry_dtls b where a.pro_dtls_id=b.id and a.id=$data and b.status_active=1 and b.is_deleted=0";
		$results=sql_select($sqls_mst);

	$roll_status = array(1 => 'QC Pass', 2 => 'Held Up', 3 => 'Reject');
	foreach ($results as $key => $row) 
	{
		$status_roll = $roll_status[$row[csf("roll_status")]];
		//$row[csf('qc_name')];$roll_status[
	}
	
		$sql_dtls="SELECT id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point from PRO_QC_RESULT_DTLS
		where mst_id=$data";
		$results_dtls=sql_select($sql_dtls);
		foreach($results_dtls as $val_dtls)
		{ 
			$array_for_dtls_data[$val_dtls[csf("mst_id")]][$val_dtls[csf("defect_name")]]['name']=$val_dtls[csf("defect_name")];
			$array_for_dtls_data[$val_dtls[csf("mst_id")]][$val_dtls[csf("defect_name")]]['counts']=$val_dtls[csf("defect_count")];
			$array_for_dtls_data[$val_dtls[csf("mst_id")]][$val_dtls[csf("defect_name")]]['inch']=$val_dtls[csf("found_in_inch")];
			$array_for_dtls_data[$val_dtls[csf("mst_id")]][$val_dtls[csf("defect_name")]]['inch_point']=$val_dtls[csf("found_in_inch_point")];
			$array_for_dtls_data[$val_dtls[csf("mst_id")]][$val_dtls[csf("defect_name")]]['penalty']=$val_dtls[csf("penalty_point")];
	
		}
	//echo $data_array = sql_select("SELECT  b.body_part_id,c.pro_dtls_id ,c.id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count FROM  pro_grey_prod_entry_dtls b,pro_qc_result_mst c WHERE b.id=c.pro_dtls_id");
		$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$cons_and_comp=$constructtion_arr[$results[0][csf("febric_description_id")]] . ' ' . $composition_arr[$results[0][csf("febric_description_id")]]; 
	
		$machine_dia = return_field_value("dia_width", "lib_machine_name", "id=" . $results[0][csf("machine_no_id")], "dia_width");
	//echo "document.getElementById('txt_mc_dia').value 				= '" . $machine_dia . "';\n";
	
		$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");
	
		$lot_arr = array_unique(explode(",", $results[0][csf("yarn_lot")]));
		foreach ($lot_arr as $lot) {
			$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
			$all_supplier .= $supplier_arr[$supplier_id] . ",";
		}
		$all_supplier = chop($all_supplier, ',');
	
	
		?>
		<div> <h3 style="margin-left: 400px;">Knitting Production</h3></div>
		<div>
			<div style="float: left;">
				<table width="100%" border="1" cellpadding="3" cellspacing="0" style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;" class="rpt_table">
					<tr>
						<td>Barcode Number</td>
						<td><? echo $results[0][csf("barcode_no")];?></td>
					</tr>
	
					<tr>
						<td>Roll Number</td>
						<td><? echo $results[0][csf("roll_no")];?></td>
					</tr>
	
					<tr>
						<td>QC Date</td>
						<td><? echo $results[0][csf("qc_date")];?></td>
					</tr>
	
					<tr>
						<td>QC Name</td>
						<td><? echo $results[0][csf("qc_name")];?></td>
					</tr>
	
					<tr>
						<td>Roll Width (inch)</td>
						<td><? echo $results[0][csf("roll_width")];?></td>
					</tr>
	
					<tr>
						<td>Roll Wgt. (Kg)</td>
						<td><? echo $results[0][csf("roll_weight")];?></td>
					</tr>
					<tr>
						<td>Roll Length (Yds)</td>
						<td><? echo $results[0][csf("roll_length")];?></td>
					</tr>
	
					<tr>
						<td>Reject Qty</td>
						<td><? echo $results[0][csf("reject_qnty")];?></td>
	
					</tr>
	
					<tr>
						<td>Construction & Composition</td>
						<td><? echo $cons_and_comp;?></td>
					</tr>
					<tr>
						<td>GSM</td>
						<td><? echo $results[0][csf("gsm")];?></td>
					</tr>
	
					<tr>
						<td>Dia</td>
						<td><? echo $results[0][csf("width")];?></td>
					</tr>
	
					<tr>
						<td>M/C Dia</td>
						<td><? echo $machine_dia;?></td>
					</tr>
	
					<tr>
						<td>Color</td>
						<td><? echo $color_arr[$results[0][csf("color_id")]];?></td>
					</tr>
	
					<tr>
						<td>Yarn Count</td>
						<td><? echo $yarn_count_arr[$results[0][csf("yarn_count")]];?></td>
					</tr>
	
					<tr>
						<td>Yarn Lot</td>
						<td><? echo $results[0][csf("yarn_lot")];?></td>
					</tr>
	
					<tr>
						<td>Spinning Mill</td>
						<td><? echo $all_supplier;?></td>
					</tr>

					<tr>
						<td>Roll Status</td>
						<td><? echo $status_roll;?></td>
					</tr>
	
				</table>
			</div>
			<div style="float: left;margin-left: 150px;">
				<table width="100%" border="1" cellpadding="3" cellspacing="0" style="font: 12px tahoma; border-bottom: 1px solid #999; margin-bottom: 2px;">
					<tr>
						<th align="center">SL</th>
						<th align="center">Defect Name</th>
						<th align="center">Defect Count </th>
						<th align="center">Found in (Inch) </th>
						<th align="center">Penalty Point</th>
					</tr>
	
					<tr>
						<? 
						$i=1;
		//name counts inch inch_point penalty
						foreach ($knit_defect_array as $defect_id => $val)
						{
							?>
							<tr>
								<td align="center"><? echo $i;?></td>
								<td align="center"><? echo $val;?></td>
								<td align="center"><? echo $array_for_dtls_data[$data][$defect_id]['counts'];?></td>
								<td align="center"><? echo $knit_defect_inchi_array[$array_for_dtls_data[$data][$defect_id]['inch']];?></td>
								<td align="center"><? echo $array_for_dtls_data[$data][$defect_id]['penalty']; $total_penalty+=$array_for_dtls_data[$data][$defect_id]['penalty'];?></td>	 
							</tr>
	
							<?
							$i++;
						}
						?>
					</tr>
	
					<tr>
						<td align="right" colspan="4">Total Penalty Point:</td>
						<td align="center"><? echo $total_penalty;?></td>
					</tr>
	
					<tr>
						<td align="right" colspan="4">Total  Point:</td>
						<td align="center"><? //echo $total_penalty;?></td>
					</tr>
	
					<tr>
						<td align="right" colspan="4">Febric Grade:</td>
						<td align="center"><? echo $results[0][csf("fabric_grade")];?></td>
					</tr>
	
					<tr>
						<td align="center">Comments</td>
						<td align="left" colspan="4"><? echo $results[0][csf("comments")];?></td>
					</tr>
	
	
	
				</table>
			</div>
		</div>
		<?php
		exit();
    }
if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	?> 

	<script>

		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str, stat) 
		{
			if(stat==3){
				alert("Barcode is delivered to store.");
				return;
			}else if(stat==2){
				alert("Barcode already is in QC.");
				return;
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			var total_selected_val=$('#hidden_selected_row_total').val()*1;// txt_individual_qty

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
			$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}
		
		
		function change_booking_placeholder()
		{
			if(document.getElementById('chkIsSales').checked)
			{
				$("#txt_booking_no").attr("placeholder", "Full Booking No");
			}
			else
			{
				$("#txt_booking_no").attr("placeholder", "Booking No Prefix");
			}
		}
		
		var tableFilters = 
		{
			col_operation: { 
				id: ["total_selected_value_td"],
				//col: [7,14,16,17,18,19,20,21,22,24,25,26],
				col: [15],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>

</head>

<body>
	<div align="center" style="width:960px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:960px; margin-left:2px;">
				<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="1090" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Year</th>
						<th>Location</th>
						<th>Job No</th>
						<th>Order No</th>
						<th>File No</th>
						<th>Internal Ref. No</th>
						<th>Barcode No</th>
						<th>Sales Order No</th>
						<th>Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:50px" class="formbutton" />
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
						</th> 
					</thead>
					<tr class="general">
						<td>
							<?php
							echo create_drop_down( "cbo_year_selection", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
							?>
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_location_name", 120, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
							?>
						</td>
						<td align="center">				
							<input type="text" style="width:60px" class="text_boxes"  name="txt_job_no" id="txt_job_no" placeholder="Job No Prefix" />	
						</td>
						<td align="center">				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
						</td>
						<td align="center">				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />	
						</td>
						<td align="center">				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
						</td>			
						<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td> 
						<td align="center">				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_sales_order_no" id="txt_sales_order_no" />	
						</td> 
						<td align="center">				
							<input type="text" style="width:80px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" placeholder=" Booking Prefix" />
							<input type="checkbox" name="chkIsSales" id="chkIsSales" onChange="change_booking_placeholder()"/> <label for="chkIsSales">Is sales order </label>	
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year_selection').value, 'create_barcode_search_list_view', 'search_div', 'knitting_production_qc_result_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);reset_hide_field();')" style="width:50px;" />
						</td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body> 

<script>
</script>          
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$location_id=trim($data[0]);
	$order_no=$data[1];
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	$booking_no =trim($data[6]);
	$sales_order_no = trim($data[7]);
	$is_sales = trim($data[8]);
	$job_no = trim($data[9]);
	$year = trim($data[10]);
	//print_r($data);die;
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$search_field_cond=$search_field_cond2=$booking_cond="";

	if($barcode_no =="" && $job_no =="" && $order_no=="" && $file_no=="" && $ref_no=="" && $booking_no=="" && $sales_order_no=="") 
	{
		echo "<div style='color:red; font-weight:bold; text-align:center;'>Please enter Order No</div>";		
		die;
	}

	if($order_no!="") {
		$po_number_cond=" and d.po_number like '%$order_no%'";
	}
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}
	if($job_no!="")
	{
		$job_no_cond =" and d.job_no_mst like '%$job_no%'";
		if($db_type==0) $job_no_cond.=" and YEAR(d.insert_date)=$year"; 
		else if($db_type==2) $job_no_cond.=" and to_char(d.insert_date,'YYYY')=$year";
	}
	if($sales_order_no!="")
	{
		$sales_order_cond =" and d.job_no like '%$sales_order_no%'";
		if($db_type==0) $sales_order_cond.=" and YEAR(d.insert_date)=$year"; 
		else if($db_type==2) $sales_order_cond.=" and to_char(d.insert_date,'YYYY')=$year";
	}
	if($booking_no!="")
	{ 
		$booking_cond =" and e.booking_no  like'%$booking_no%'";
		$sales_booking_cond =" and d.sales_booking_no  like'%$booking_no%'";
		$sample_booking_cond =" and d.booking_no  like'%$booking_no%'";
		/*if($db_type==0) $search_field_cond.=" and YEAR(e.insert_date)=$year"; 
		else if($db_type==2) $search_field_cond.=" and to_char(e.insert_date,'YYYY')=$year";*/
	}

	if($file_no!="") $file_cond=" and d.file_no like '%$file_no%'";
	if($ref_no!="") 
	{
		$ref_cond=" and d.grouping like '%$ref_no%'";
		$sample_ref_cond=" and f.internal_ref like '%$ref_no%'"; 
	}


	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";


	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');


	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	if(($barcode_no!="" || $job_no!="" || $order_no!="" || $file_no!="" || $ref_no!="" || $booking_no!="") && ($sales_order_no =="" && $is_sales == 'false'))
	{
		$order_sql = "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,  e.booking_no, d.po_number as order_no, 0 as within_group, 0 as is_sales, d.pub_shipment_date, d.job_no_mst as job_no, d.file_no, d.grouping, b.color_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_booking_dtls e 
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id $barcode_cond $file_cond $ref_cond $job_no_cond $po_number_cond $booking_cond  and a.entry_form in(2) and c.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales<>1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.booking_without_order!=1 and d.id=e.po_break_down_id and e.booking_type=1 
		group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, b.color_id, e.booking_no";

		$sql = $order_sql;
	}

	if(($barcode_no!="" || $sales_order_no !="" || ($is_sales == 'true' && $booking_no!="")) && ($job_no =="" && $order_no =="" && $file_no =="" && $ref_no ==""))
	{
		$sales_sql = "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.sales_booking_no as booking_no,d.job_no as order_no,d.within_group,c.is_sales, null as pub_shipment_date, null as job_no, null as file_no, null as grouping, b.color_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d 
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id $barcode_cond $sales_booking_cond $sales_order_cond
		and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.is_sales=1
		group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_without_order,d.sales_booking_no,d.job_no,d.within_group,c.is_sales,b.color_id";

		if($sql =="")
		{
			$sql = $sales_sql;
		}else{
			$sql .= " union all ". $sales_sql;
		}
	}

	if(($barcode_no!="" || $booking_no!="" || $ref_no!="") && ($is_sales == 'false' && $sales_order_no =="" && $job_no =="" && $order_no =="" && $file_no ==""))
	{
		$sample_sql = "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.booking_no, null as order_no, 0 as within_group, 0 as is_sales, null as pub_shipment_date, null as job_no, null as file_no, f.internal_ref as grouping, b.color_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c,wo_non_ord_samp_booking_mst d, wo_non_ord_samp_booking_dtls e left join sample_development_mst f on e.style_id=f.id 
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.booking_no=e.booking_no and a.company_id=$company_id $barcode_cond $sample_booking_cond $sample_ref_cond and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.is_sales<>1 and c.booking_without_order=1
		group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.booking_no,f.internal_ref,b.color_id";

		if($sql =="")
		{
			$sql = $sample_sql;
		}else{
			$sql .= " union all ". $sample_sql;
		}
	}

	//echo $sql;//die;
	
	$result = sql_select($sql);
	$barcode_arr = array();
	foreach ($result as $row) 
	{
		$barcodeArr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	if(count($barcodeArr)>0)
	{
		$all_barcode_no = implode(",", $barcodeArr);
		$barCond = $all_barcode_no_cond = "";
		$barCond_1 = $all_barcode_no_cond_1 = "";

		if($db_type==2 && count($barcodeArr)>999)
		{
			$barcodeArr_chunk=array_chunk($barcodeArr,999) ;
			foreach($barcodeArr_chunk as $chunk_arr)
			{
				$barCond.=" barcode_num in(".implode(",",$chunk_arr).") or ";
				$barCond_1.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
			$all_barcode_no_cond_1.=" and (".chop($barCond_1,'or ').")";

		}
		else
		{
			$all_barcode_no_cond=" and barcode_num in($all_barcode_no)";
			$all_barcode_no_cond_1=" and a.barcode_no in($all_barcode_no)";
		}

		$data_delivery_array = sql_select("select d.barcode_num from pro_grey_prod_delivery_dtls d where  d.entry_form=56 and d.status_active=1 and d.is_deleted=0 $all_barcode_no_cond");
	
		foreach ($data_delivery_array as $row) {
			$delivered_barcode_arr[$row[csf('barcode_num')]]=$row[csf('barcode_num')];
		}
		
		
		if(!empty($barcodeArr)) // all ready qc barcode 
		{
			$qcBarcodeResult = sql_select("SELECT barcode_no FROM pro_qc_result_mst a WHERE a.status_active=1 and a.is_deleted=0 $all_barcode_no_cond_1");
			foreach ($qcBarcodeResult as $row) {
				$scanned_barcode_arr[$row[csf('barcode_no')]] =  $row[csf('barcode_no')];
				//unset($barcodeArr[$row[csf('barcode_no')]]); // Remove all ready qc barcode 
			}
		}
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="160">Fabric Description</th>
			<th width="40">Gsm</th>
			<th width="40">Dia</th>
			<th width="90">Job No</th>
			<th width="110">Booking No</th>
			<th width="110">Order/FSO No</th>
			<th width="50">Within Group</th>
			<th width="70">Color Name</th>
			<th width="105">Location</th>
			<th width="70">File No</th>
			<th width="70">Ref No</th>
			<th width="65">Shipment Date</th>
			<th width="75">Barcode No</th>
			<th width="40">Roll No</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:1211px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1192" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;$total_roll_weight=0;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($scanned_barcode_arr[$row[csf('barcode_no')]]=="" && $delivered_barcode_arr[$row[csf('barcode_no')]]=="")
				//{
					if($delivered_barcode_arr[$row[csf('barcode_no')]])
					{
						$barcode_stat =3;
					} 
					else if($scanned_barcode_arr[$row[csf('barcode_no')]])
					{
						$barcode_stat =2;
					}
					else
					{
						$barcode_stat =1;
					}

					$within_group_con=($row[csf('within_group')] == 1)?"Yes":"No";				

					$within_group = $row[csf('within_group')];
					$is_sales = $row[csf('is_sales')];

					$sales_order_order = $row[csf('order_no')];
					$job_no = $row[csf('job_no')];
					$sales_booking_no = $row[csf('booking_no')];
					$po_shipdate_no = $row[csf('pub_shipment_date')];
					$file_no = $row[csf('file_no')];
					$group_no = $row[csf('grouping')];


					$color='';
					$color_id=explode(",",$row[csf('color_id')]);
					foreach($color_id as $val)
					{
						if($val>0) $color.=$color_arr[$val].",";
					}
					$color=chop($color,',');

					$product_data=explode(",",$product_arr[$row[csf('prod_id')]]);

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>,<? echo $barcode_stat;?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
						</td>
						<td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="40"><p><? echo $product_data[2]; ?></p></td>
						<td width="40"><p><? echo $product_data[3]; ?></p></td>
						<td width="90"><p><? echo $job_no; ?></p></td>
						<td width="110"><p><? echo $sales_booking_no; ?></p></td>
						<td width="110"><p><? echo $sales_order_order; ?></p></td>
						<td width="50" align="center"><p><? echo $within_group_con; ?></p></td>
						<td width="70"><p><? echo $color; ?></p></td>
						<td width="105"><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</td>
						<td width="70"><? echo $row[csf('file_no')]; ?>&nbsp;</td>
						<td width="70"><? echo $row[csf('grouping')]; ?>&nbsp;</td>
						<td width="65" align="center"><? if($row[csf('booking_without_order')]==1) echo '&nbsp;'; else echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="75"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$total_roll_weight+=$row[csf('qnty')];
					
					$i++;
				//}
			}
			?>
		</table>
	</div>
	<table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table">
		<tr class="tbl_bottom">
			<td  width="30"></td>
			<td  width="160"></td>
			<td  width="40"></td>
			<td  width="40"></td>
			<td  width="90"></td>
			<td  width="110"></td>
			<td  width="110"></td>
			<td  width="50"></td>
			<td  width="70"></td>
			<td  width="105"></td>
			<td  width="70"></td>
			<td  width="70"></td>
			<td  width="65"></td>

			<td width="75" ></td>
			<td width="40" >Total</td>
			<td id="total_selected_value_td"  align="right"><?php echo number_format($total_roll_weight,2); ?></td>
		</tr>
		<tr class="tbl_bottom">
			<td  width="30"></td>
			<td  width="160"></td>
			<td  width="40"></td>
			<td  width="40"></td>
			<td  width="90"></td>
			<td  width="110"></td>
			<td  width="110"></td>
			<td  width="50"></td>
			<td  width="70"></td>
			<td  width="105"></td>
			<td  width="70"></td>
			<td  width="70"></td>


			<td width="140"  colspan="2"> Selected Row Total</td>
			<td colspan="2" align="right"  >
				<input type="text"  style="width:70px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0"> 
			</td>
		</tr>
		<tr>
			<td align="center" colspan="16" >
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>

	</table>
	<?	
	exit();
}

if($action=="create_barcode_search_list_view_backup")
{
	$data = explode("_",$data);
	
	$location_id=trim($data[0]);
	$order_no=$data[1];
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	$booking_no =trim($data[6]);
	$sales_order_no = trim($data[7]);
	$is_sales = trim($data[8]);
	$job_no = trim($data[9]);
	$year = trim($data[10]);
	//print_r($data);die;
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$search_field_cond=$search_field_cond2=$booking_cond="";
	if($order_no!="") {
		$search_field_cond=" and d.po_number like '%$order_no%'";
	}else if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}else if($job_no!="")
	{
		$search_field_cond.=" and d.job_no_mst like '%$job_no%'";
		/*if($db_type==0) $search_field_cond.=" and YEAR(d.insert_date)=$year"; 
		else if($db_type==2) $search_field_cond.=" and to_char(d.insert_date,'YYYY')=$year";*/
		
	}else if($sales_order_no!=""){
		$search_field_cond.=" and d.job_no like '%$sales_order_no%'";
		//if($db_type==0) $search_field_cond.=" and YEAR(d.insert_date)=$year"; 
		//else if($db_type==2) $search_field_cond.=" and to_char(d.insert_date,'YYYY')=$year";
	}
	else if($booking_no!=""){ 
		$search_field_cond.=" and e.booking_no  like'%$booking_no%'";
		/*if($db_type==0) $search_field_cond.=" and YEAR(e.insert_date)=$year"; 
		else if($db_type==2) $search_field_cond.=" and to_char(e.insert_date,'YYYY')=$year";*/
	}
	else{
		echo "<div style='color:red; font-weight:bold; text-align:center;'>Please enter Order No</div>";		
		die;
	}
	//if($job_no!="") $search_field_cond.=" and d.job_no_mst like '%$job_no%'";//d.job_no_mst
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";

	if($sales_order_no!="") $search_field_cond.=" and d.job_no_prefix_num=$sales_order_no";
	//if($booking_no!="") $search_field_cond=" and d.sales_booking_no='$booking_no'";

	if($booking_no!="") $search_field_cond2.=" and d.sales_booking_no='$booking_no'";
	if($sales_order_no!="") $search_field_cond2.=" and d.job_no like '%$sales_order_no%'";

	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";



	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	//$sql_product=sql_select();

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	if ($sales_order_no != '' || ( $barcode_no != '' && $is_sales == 'true') || ( $booking_no != '' && $is_sales == 'true')) 
	{
		//if ($is_sales == true){
		$sales_order = 1;
		$sql="select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.sales_booking_no,d.job_no  sales_order_no,d.within_group,c.is_sales 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.re_transfer=0 and c.roll_no>0 and c.booking_without_order!=1 $search_field_cond2 $location_cond $barcode_cond
		group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_without_order,d.sales_booking_no,d.job_no,d.within_group,c.is_sales 
		union all
		select a.transfer_system_id as recv_number,0 as location_id,b.from_prod_id as  prod_id, c.barcode_no, c.roll_no, c.qnty,d.sales_booking_no,d.job_no sales_order_no,d.within_group,c.is_sales 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in(133) and c.entry_form in(133) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and c.booking_without_order!=1 $search_field_cond2 $barcode_cond
		group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.roll_no, c.qnty,d.sales_booking_no,d.job_no,d.within_group,c.is_sales";
	}
	else
	{
		if($search_field_cond!="")
		{
			/*$sql="select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.booking_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id po_id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.re_transfer=0 and c.roll_no>0 and c.booking_without_order!=1 $search_field_cond $location_cond $booking_cond group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.booking_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,b.color_id			
			union all
			select a.transfer_system_id as recv_number,0 as location_id,b.from_prod_id as  prod_id, c.barcode_no, c.booking_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty,d.id po_id, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,null as color_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and c.booking_without_order!=1 $search_field_cond $barcode_cond group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.booking_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order"; */

			$sql="select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id po_id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,b.color_id, e.booking_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_booking_dtls e where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.re_transfer=0 and c.roll_no>0 and c.booking_without_order!=1 and d.id=e.po_break_down_id and e.booking_type=1 $search_field_cond $location_cond $booking_cond group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,b.color_id, e.booking_no			
			union all
			select a.transfer_system_id as recv_number,0 as location_id,b.from_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty,d.id po_id, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,null as color_id, e.booking_no from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_booking_dtls e where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in(83) and c.entry_form in(83)  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and c.roll_no>0 and c.re_transfer=0 and c.booking_without_order!=1 and d.id=e.po_break_down_id and e.booking_type=1 $search_field_cond $barcode_cond group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order, e.booking_no"; 

			// code is block for request of nasir vie for client of metro booking no search

			/*$sql="select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id po_id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.re_transfer=0 and c.roll_no>0 and c.booking_without_order!=1 $search_field_cond $location_cond $booking_cond group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,b.color_id			
			union all
			select a.transfer_system_id as recv_number,0 as location_id,b.from_prod_id as  prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty,d.id po_id, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,null as color_id from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in(83) and c.entry_form in(83)  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and c.booking_without_order!=1 $search_field_cond $barcode_cond group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order"; */
			
		}
		else
		{
			$sql="select a.recv_number,a.location_id,b.prod_id,c.barcode_no,c.roll_no,c.qnty,d.id po_id,d.po_number,d.pub_shipment_date, d.job_no_mst,d.file_no, d.grouping, c.booking_without_order,b.color_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58)  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.roll_no>0 and c.re_transfer=0 and c.booking_without_order!=1 $search_field_cond $barcode_cond $location_cond
			group by a.recv_number,a.location_id,b.prod_id,c.barcode_no,c.roll_no,c.qnty,d.id,d.po_number,d.pub_shipment_date,d.job_no_mst,d.file_no, d.grouping, c.booking_without_order,b.color_id
			union all
			select a.transfer_system_id as recv_number,0 as location_id,b.from_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.id po_id, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order,null as color_id
			from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, wo_booking_dtls e
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in(83,133) and c.entry_form in(83,133)  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and c.roll_no>0 and d.id=e.po_break_down_id and c.re_transfer=0 and c.booking_without_order!=1 $search_field_cond $barcode_cond
			group by a.transfer_system_id,b.from_prod_id, c.barcode_no, c.roll_no, c.qnty, d.id,d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, c.booking_without_order
			union all
			select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, null as po_id,null as po_number, null as pub_shipment_date, null as job_no_mst, null as file_no, null as grouping, c.booking_without_order,b.color_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order=1 $barcode_cond $location_cond 
			group by a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order,b.color_id"; 	
		}
	}	
	//echo $sql;
	$result = sql_select($sql);
	$barcode_arr = array();
	foreach ($result as $row) {
		if ($sales_order == 1 && $row[csf('within_group')] == 1) {
			$sales_within_group = true;			
		} else {
			$sales_within_group = false;
		}
		$po_nos[$row[csf('po_id')]] = $row[csf('po_id')];
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	if(!empty($po_nos)){
		$po_arr = array();
		$po_info = sql_select("select b.job_no job_no_mst,b.booking_no,b.po_break_down_id from wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in(".implode(",", array_unique($po_nos)).")");
		if (!empty($po_info)) {
			foreach ($po_info as $po_row) {
				$po_arr[$po_row[csf('po_break_down_id')]] = $po_row[csf('job_no_mst')];
			}
		}
	}
	
	if(!empty($barcode_arr)){
		$scanned_barcode_arr=array();
		$barcodeData=sql_select("select a.barcode_no from pro_roll_details a where a.entry_form=61 and a.status_active=1 and a.is_deleted=0 			
			and a.po_breakdown_id in(".implode(",", array_unique($po_nos)).") and a.barcode_no not in(select b.barcode_no from pro_roll_details b where b.entry_form=84 and status_active=1 and b.po_breakdown_id in(".implode(",", array_unique($po_nos))."))");//and a.barcode_no in(".implode(",", $barcode_arr).") 
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="160">Fabric Description</th>
			<th width="40">Gsm</th>
			<th width="40">Dia</th>
			<th width="90">Job No</th>
			<th width="110">Booking No</th>
			<th width="110">Order/FSO No</th>
			<th width="50">Within Group</th>
			<th width="70">Color Name</th>
			<th width="105">Location</th>
			<th width="70">File No</th>
			<th width="70">Ref No</th>
			<th width="65">Shipment Date</th>
			<th width="75">Barcode No</th>
			<th width="40">Roll No</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:1210px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1192" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;$total_roll_weight=0;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$within_group_con=($row[csf('within_group')] == 1)?"Yes":"No";				

					$within_group = $row[csf('within_group')];
					$is_sales = $row[csf('is_sales')];
					if ($sales_order == 1) {
						$sales_order_order = $row[csf('sales_order_no')];
						$sales_booking_no = $row[csf('sales_booking_no')];
						if ($within_group == 1) {
							$po_data = explode("**", $po_arr[$row[csf('po_id')]]);
							$job_no = $po_data[0];
							//$po_shipdate_no = change_date_format($po_data[0]);
						} else {
							$job_no = '';
							$po_shipdate_no = '';
						}
					} else {
						$sales_order_order = $row[csf('po_number')];
						$job_no = $row[csf('job_no_mst')];
						$sales_booking_no = $row[csf('booking_no')];
						$po_shipdate_no = $row[csf('pub_shipment_date')];
						$file_no = $row[csf('file_no')];
						$group_no = $row[csf('grouping')];
					}
					$color='';
					$color_id=explode(",",$row[csf('color_id')]);
					foreach($color_id as $val)
					{
						if($val>0) $color.=$color_arr[$val].",";
					}
					$color=chop($color,',');

					$product_data=explode(",",$product_arr[$row[csf('prod_id')]]);

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
						</td>
						<td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="40"><p><? echo $product_data[2]; ?></p></td>
						<td width="40"><p><? echo $product_data[3]; ?></p></td>
						<td width="90"><p><? echo $job_no; ?></p></td>
						<td width="110"><p><? echo $sales_booking_no; ?></p></td>
						<td width="110"><p><? echo $sales_order_order; ?></p></td>
						<td width="50" align="center"><p><? echo $within_group_con; ?></p></td>
						<td width="70"><p><? echo $color; ?></p></td>
						<td width="105"><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</td>
						<td width="70"><? echo $row[csf('file_no')]; ?>&nbsp;</td>
						<td width="70"><? echo $row[csf('grouping')]; ?>&nbsp;</td>
						<td width="65" align="center"><? if($row[csf('booking_without_order')]==1) echo '&nbsp;'; else echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="75"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$total_roll_weight+=$row[csf('qnty')];
					
					$i++;
				}
			}
			?>
		</table>
	</div>
	<table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table">
		<tr class="tbl_bottom">
			<td  width="30"></td>
			<td  width="160"></td>
			<td  width="40"></td>
			<td  width="40"></td>
			<td  width="90"></td>
			<td  width="110"></td>
			<td  width="110"></td>
			<td  width="50"></td>
			<td  width="70"></td>
			<td  width="105"></td>
			<td  width="70"></td>
			<td  width="70"></td>
			<td  width="65"></td>

			<td width="75" ></td>
			<td width="40" >Total</td>
			<td id="total_selected_value_td"  align="right"><?php echo number_format($total_roll_weight,2); ?></td>
		</tr>
		<tr class="tbl_bottom">
			<td  width="30"></td>
			<td  width="160"></td>
			<td  width="40"></td>
			<td  width="40"></td>
			<td  width="90"></td>
			<td  width="110"></td>
			<td  width="110"></td>
			<td  width="50"></td>
			<td  width="70"></td>
			<td  width="105"></td>
			<td  width="70"></td>
			<td  width="70"></td>


			<td width="140"  colspan="2"> Selected Row Total</td>
			<td colspan="2" align="right"  >
				<input type="text"  style="width:70px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0"> 
			</td>
		</tr>
		<tr>
			<td align="center" colspan="16" >
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>

	</table>
	<?	
	exit();
}

?>

