<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "sales_order_no_search_popup") 
{
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(job_no) {
			document.getElementById('hidden_job_no').value = job_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								<th>Year</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">
								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", $cbo_within_group, $dd, 0);
									?>
								</td>
								<td>
									<? echo create_drop_down("cbo_year", 70, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, ""); ?>
		                        </td>
								<td align="center">
									<?
									$serach_type_arr = array(1 => 'FSO No', 2 => 'Booking No', 3 => 'Style Ref.');
									echo create_drop_down("cbo_serach_type", 150, $serach_type_arr, "", 0, "--Select--", "", "", 0);
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'knitting_consumption_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}

if ($action == "create_sales_order_no_search_list") 
{
	$data 			= explode('_', $data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($db_type == 0) {
		if ($yearID != 0) $year_cond = " and YEAR(a.insert_date)=$yearID";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($yearID != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$yearID";
		else $year_cond = "";
	}

	if ($serach_type == 1) 
	{
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.job_no like '%$sales_order_no%'";
	} 
	else if ($serach_type == 2) 
	{
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.sales_booking_no like '%$sales_order_no%'";
	}
	else if ($serach_type == 3) 
	{
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.style_ref_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2) ? "to_char(a.insert_date,'YYYY') as year" : "YEAR(a.insert_date) as year";

	$sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  $search_field_cond $sales_order_cond $year_cond order by a.id";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:950px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) 
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1) {
					$buyer = $company_arr[$row[csf('buyer_id')]];
				} else {
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="110" align="center">
						<p>&nbsp;<? echo $row[csf('job_no')]; ?></p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('sales_booking_no')]; ?></p>
					</td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center">
						<p><? echo $row[csf('style_ref_no')]; ?></p>
					</td>
					<td>
						<p><? echo $location_arr[$row[csf('location_id')]]; ?></p>
					</td>
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

if ($action == "report_generate") 
{	
	$process = array(&$_POST);
	
	extract(check_magic_quote_gpc($process));
	$cbo_year = str_replace("'", "", $cbo_year);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	$report_type = str_replace("'", "", $report_type);
	$sales_order_no = str_replace("'", "", $txt_sales_order);
	$cbo_cust_buyer_id = str_replace("'", "", $cbo_cust_buyer_id);

	//var_dump($txt_date_to);

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	//$reqsn_details = return_library_array("select knit_id, requisition_no from ppl_yarn_requisition_entry group by knit_id,requisition_no", "knit_id", "requisition_no");
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$sales_order_cond = ($sales_order_no != "") ? " and e.job_no like '%$sales_order_no%' " : "";

	if ($report_type == 1)
	{
		// =================================================================================
		if ($db_type == 0) {

			if ($cbo_year != 0) $job_year_cond = " and YEAR(e.insert_date)=$cbo_year";
			else $job_year_cond = "";
		} else if ($db_type == 2) {

			if ($cbo_year != 0) $job_year_cond = " and to_char(e.insert_date,'YYYY')=$cbo_year";
			else $job_year_cond = "";
		} 
		$from_date = $txt_date_from;
		if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
		else $to_date = $txt_date_to;

		$date_con = "";$recv_date_cond="";
		if ($from_date != "" && $to_date != "") 
		{	
			if($db_type==0)
			{
				$recv_date_cond = "and a.receive_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$recv_date_cond = "and a.receive_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
			$date_con = " and a.receive_date<'".$txt_date_from."'";
		}

		$composition_arr = $construction_arr = array();
		$sql_deter = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array = sql_select($sql_deter);
		if (count($data_array) > 0) {
			foreach ($data_array as $row) {
				if (array_key_exists($row[csf('id')], $composition_arr)) {
					$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
				} else {
					$composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
				}

				$construction_arr[$row[csf('id')]] = $row[csf('construction')];
				$yarn_type_arr[$row[csf('id')]] = $yarn_type[$row[csf('yarn_type')]];
			}
		}
		// =================================================================================

		$con = connect();
	    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (7852)");
	    oci_commit($con);

	    $production_sql = "SELECT d.prod_id as yProdId, d.requisition_no, a.recv_number, a.company_id,a.knitting_company,a.knitting_source, a.receive_basis, a.receive_date, a.booking_id as program_no, a.booking_no, b.id, b.prod_id,b.yarn_prod_id, b.febric_description_id,b.body_part_id,b.gsm,b.width,b.yarn_lot,b.yarn_count,b.stitch_length,b.brand_id, b.color_id,b.color_range_id,c.po_breakdown_id,e.job_no,e.sales_booking_no,e.within_group, e.buyer_id,e.po_buyer,e.customer_buyer, c.quantity as production_qty, b.reject_fabric_receive as reject_qty,b.machine_no_id
		from PPL_YARN_REQUISITION_ENTRY d, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst e
		where d.KNIT_ID=a.BOOKING_ID and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id  and a.entry_form=2 and a.item_category=13 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sales_order_cond $recv_date_cond $job_year_cond  and d.status_active=1 and d.is_deleted=0  
		order by a.knitting_source,e.job_no, a.booking_id";
		// echo $production_sql.'<br>';die;// 2.12 s
		$sqlResult = sql_select($production_sql);
		foreach ($sqlResult as $key => $row) 
		{
			$program_no_array[$row[csf('program_no')]] = $row[csf('program_no')];
		}
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 7852, 3,$program_no_array, $empty_arr); // program insert
    	oci_commit($con);

		// prev_production sql-------------------------------------------------------------------
		if ($from_date != "" && $to_date != "")
		{
			$prev_production_sql = "SELECT d.prod_id as yProdId, d.requisition_no, a.recv_number, a.company_id,a.knitting_company,a.knitting_source, a.receive_basis, a.receive_date, a.booking_id as program_no, a.booking_no, b.id, b.prod_id,b.yarn_prod_id, b.febric_description_id,b.body_part_id,b.gsm,b.width,b.yarn_lot,b.yarn_count,b.stitch_length,b.brand_id, b.color_id,b.color_range_id,c.po_breakdown_id,e.job_no,e.sales_booking_no,e.within_group, e.buyer_id,e.po_buyer,e.customer_buyer, c.quantity as production_qty, b.reject_fabric_receive as reject_qty,b.machine_no_id
			from GBL_TEMP_ENGINE g, PPL_YARN_REQUISITION_ENTRY d, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst e
			where g.ref_val=d.KNIT_ID and g.user_id=$user_id and g.entry_form=7852 and g.ref_from=3 and d.KNIT_ID=a.BOOKING_ID and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id  and a.entry_form=2 and a.item_category=13 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sales_order_cond $date_con $job_year_cond  and d.status_active=1 and d.is_deleted=0";
			// echo $prev_production_sql;die;
			$prev_production_Result = sql_select($prev_production_sql);
			foreach ($prev_production_Result as $row) 
			{
				$previous_prod_qty_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$row[csf('yProdId')]]['previous_prod_qty']+=$row[csf('production_qty')];
			}
			// echo "<pre>";print_r($previous_prod_qty_arr);die;
		}
		foreach ($sqlResult as $row) 
		{
			$buyer_id="";
			if ($row[csf("within_group")] == 1) 
			{
				$buyer_id=$buyer_arr[$row[csf("po_buyer")]];
			}
			else
			{
				$buyer_id=$buyer_arr[$row[csf("buyer_id")]];
			}

			$yProdId=$row[csf('yProdId')];
			if ($txt_date_from!="" && $txt_date_to!="") 
			{
				/*if( strtotime($txt_date_from) > strtotime($row[csf('receive_date')]) )
				{
					$previous_prod_qty_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['previous_prod_qty']+=$row[csf('production_qty')];
					
				}*/

				if( strtotime($txt_date_from) <= strtotime($row[csf('receive_date')]) && strtotime($txt_date_to) >= strtotime($row[csf('receive_date')]) )
				{
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['production_qty']+=$row[csf('production_qty')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['reject_qty']+=$row[csf('reject_qty')];

					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['recv_number']=$row[csf('recv_number')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['receive_date']=$row[csf('receive_date')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['within_group']=$row[csf('within_group')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['sales_booking_no']=$row[csf('sales_booking_no')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['buyer_name']=$buyer_id;
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['deter_id']=$row[csf('febric_description_id')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['color_id']=$row[csf('color_id')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['color_range_id']=$row[csf('color_range_id')];
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['stitch_length'].=$row[csf('stitch_length')].',';
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['width'].=$row[csf('width')].',';
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['gsm'].=$row[csf('gsm')].',';
					$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['requisition_no']=$row[csf('requisition_no')];
				}		
			}
			else
			{
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['production_qty']+=$row[csf('production_qty')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['reject_qty']+=$row[csf('reject_qty')];

				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['recv_number']=$row[csf('recv_number')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['receive_date']=$row[csf('receive_date')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['within_group']=$row[csf('within_group')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['sales_booking_no']=$row[csf('sales_booking_no')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['buyer_name']=$buyer_id;
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['deter_id']=$row[csf('febric_description_id')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['color_id']=$row[csf('color_id')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['color_range_id']=$row[csf('color_range_id')];
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['stitch_length'].=$row[csf('stitch_length')].',';
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['width'].=$row[csf('width')].',';
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['gsm'].=$row[csf('gsm')].',';
				$data_arr[$row[csf('knitting_source')]][$row[csf('knitting_company')]][$row[csf('job_no')]][$row[csf('program_no')]][$row[csf('body_part_id')]][$yProdId]['requisition_no']=$row[csf('requisition_no')];
			}
			//$product_id_array[$row[csf('yarn_prod_id')]] = $row[csf('yarn_prod_id')];
			$product_id_array[$yProdId] = $yProdId;
			$requisition_no_array[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
		}
		// echo "<pre>";print_r($previous_prod_qty_arr);die;
		// echo "<pre>";print_r($product_id_array);die;

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 7852, 1,$requisition_no_array, $empty_arr); // requisition insert
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 7852, 2,$product_id_array, $empty_arr); // requisition insert
    	oci_commit($con);

		// ======================================= Yarn Info Start ==================================
		$yarn_info_array = array();
		$sql = "SELECT b.id, b.supplier_id, b.lot, b.current_stock, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_count_id, b.yarn_type, b.color, b.brand from GBL_TEMP_ENGINE a, product_details_master b where a.ref_val=b.id and a.user_id=$user_id and a.entry_form=7852 and a.ref_from=2 and b.item_category_id=1 and b.status_active=1 and b.is_deleted=0";
		//echo $sql;  //and id=4787
		$result = sql_select($sql);
		foreach ($result as $row)
		{
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			}
			else
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}
			
			$yarn_info_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos;
			$yarn_info_array[$row[csf('id')]]['brand'] = $row[csf('brand')];
			$yarn_info_array[$row[csf('id')]]['yarn_count_id'] = $row[csf('yarn_count_id')];
			$yarn_info_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$yarn_info_array[$row[csf('id')]]['yarn_type'] = $row[csf('yarn_type')];
		}
		// echo "<pre>"; print_r($yarn_info_array);
		// ======================================= Yarn Info End ==================================

		// ================================= Yarn Issue and return Start ==========================
		$yIssue_sql = "SELECT b.requisition_no, b.prod_id, b.cons_quantity, b.transaction_type, b.order_rate from GBL_TEMP_ENGINE a, inv_transaction b where a.ref_val=b.requisition_no and a.user_id=$user_id and a.entry_form=7852 and a.ref_from=1 and b.item_category=1 and b.transaction_type in(2,4) and b.status_active=1 and b.is_deleted=0"; 
		// echo $yIssue_sql;die;
		$issue_sql_result = sql_select($yIssue_sql);
		$yarn_issue_array=array();
		foreach ($issue_sql_result as $row)
		{
			$yarn_issue_array[$row[csf('requisition_no')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]['yIssue_qty'] += $row[csf('cons_quantity')];
			$yarn_issue_array[$row[csf('requisition_no')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]['yIssueRtn_qty'] += $row[csf('cons_quantity')];
			// $yarn_rcv_rate_array[$row[csf('prod_id')]]['usd_rate'][$row[csf('transaction_type')]] = $row[csf('order_rate')];//rate in usd
		}
		// ======================================= Yarn Issue and return End ============================

		// ======================================= Yarn Receive Start ==================================
		$yRecv_sql = "SELECT b.prod_id, b.order_rate from GBL_TEMP_ENGINE a, inv_transaction b where a.ref_val=b.prod_id and a.user_id=$user_id and a.entry_form=7852 and a.ref_from=2 and b.item_category=1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0"; 
		// echo $yRecv_sql;die;
		$yRecv_sql_result = sql_select($yRecv_sql);
		$yarn_rcv_rate_array=array();
		foreach ($yRecv_sql_result as $row)
		{
			$yarn_rcv_rate_array[$row[csf('prod_id')]]['usd_rate'] = $row[csf('order_rate')];//rate in usd
		}
		// ======================================= Yarn Receive End ====================================

		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (7852)");
    	oci_commit($con);

		//$colspan = 2;
		$tbl_width = 2700;
		ob_start();
		?>
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="<? echo $ship_count + 23; ?>" class="form_caption" style="font-size:12px"><strong><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
					if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></strong></td>
			</tr>
		</table>

		<fieldset style="width:<? echo $tbl_width + 20; ?>px;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="60">WG<br />(Yes/No)</th>
						<th width="100">Knitting Company</th>
						<th width="100">Buyer</th>
						<th width="100">Booking No</th>
						<th width="100">Sales Order No</th>
						<th width="70">Program No</th>
						<th width="70">Requisition No</th>
						<th width="100">Body Part</th>
						<th width="100">Yarn Count</th>
						<th width="150">Yarn Composition</th>
						<th width="100">Yarn Type</th>
						<th width="100">Lot</th>

						<th width="100">Total Yarn Issue Qty</th>
						<th width="100">Total Yarn Issue Return Qty.</th>
						<th width="100">Yarn Rate (USD)</th>
						<th width="100">Brand</th>
						<th width="100">Construction</th>
						<th width="150">Composition</th>
						<th width="130">Color</th>
						<th width="100">Color Range</th>
						<th width="60">Stich</th>
						<th width="60">Dia</th>
						<th width="60">GSM</th>						
						<th width="100">Previous production Qty</th>
						<th width="100">Current production Qty</th>	
						<th width="100">Total Production</th>
						<th width="">Total Reject Qty</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?= $tbl_width + 20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
                <table width="<?= $tbl_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?
						$program_count = array();
						foreach ($data_arr as $ksource => $ksourceV) 
						{
							foreach ($ksourceV as $knitCompa => $knitCompaV) 
							{
								foreach ($knitCompaV as $fsoNo => $fsoNoV) 
								{
									foreach ($fsoNoV as $program_no => $program_noV) 
									{
										foreach ($program_noV as $body_part_id => $body_part_idV) 
										{
											foreach ($body_part_idV as $yarn_prod_id => $row) 
											{
												$program_count[$fsoNo][$program_no]++;
											}
										}
									}
								}
							}
						}


						$i = 1;
						$current_tot_prod_qty = 0;
						// echo "<pre>";print_r($data_arr);
						$grand_tot_yarn_issue_rtn_qty=0; $grand_tot_yarn_issue_qty=0;$grand_prev_tot_prod_qty=0; $grand_tot_current_prod_qty=0; $grand_tot_prod_qty=0; $grand_tot_rejectQnty=0;
						foreach ($data_arr as $ksource => $ksourceV) 
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td colspan="<? echo 28; ?>" align="left"><b><? echo $knitting_source[$ksource]; ?></b></td>
							</tr>
							<?			
							$tot_yarn_issue_rtn_qty=0;$tot_yarn_issue_qty=0;$prev_tot_prod_qty=0; $current_tot_prod_qty=0; $tot_prod_qty=0; $tot_rejectQty=0;
							foreach ($ksourceV as $knitCompa => $knitCompaV) 
							{
								foreach ($knitCompaV as $fsoNo => $fsoNoV) 
								{
									foreach ($fsoNoV as $program_no => $program_noV) 
									{
										foreach ($program_noV as $body_part_id => $body_part_idV) 
										{
											foreach ($body_part_idV as $yarn_prod_id => $row) 
											{
												if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

												$program_span = $program_count[$fsoNo][$program_no]++;

												//for yarn type
												$yCoundId=$yarn_info_array[$yarn_prod_id]['yarn_count_id'];
												$yCount = '';
												$yarn_count = array_unique(explode(",", $yCoundId));
												foreach ($yarn_count as $count_id) {
													if ($yCount == '') $yCount = $yarn_count_details[$count_id];
													else $yCount .= "," . $yarn_count_details[$count_id];
												}

												$yarn_desc_name='';
												$yarn_prod_idArr=array_unique(explode(",",$yarn_prod_id));
												foreach($yarn_prod_idArr as $y_id)
												{
													if($yarn_desc_name=='')
														$yarn_desc_name=$yarn_info_array[$y_id]['desc'];
													else
														$yarn_desc_name.=",".$yarn_info_array[$y_id]['desc'];
												}

												//$yarn_type_name = '';
												//$yarn_type_name = getYarnType($yarn_type_arr, $yarn_prod_id);
												
												$yarn_type_name=$yarn_type[$yarn_info_array[$yarn_prod_id]['yarn_type']];

												$yLot=$yarn_info_array[$yarn_prod_id]['lot'];
												

												//var_dump($yarn_desc_name);
												$yarn_brandIds=$yarn_info_array[$yarn_prod_id]['brand'];
												
												$yarn_brand_names='';
												$yarn_brandIdsArr=array_unique(explode(",",$yarn_prod_id));
												foreach($yarn_brandIdsArr as $yp_id)
												{
													if($yarn_brand_names=='')
														$yarn_brand_names=$brand_details[$yarn_info_array[$yp_id]['brand']];
													else
														$yarn_brand_names.=",".$brand_details[$yarn_info_array[$yp_id]['brand']];
												}
												$yarn_brand_names =implode(",",array_filter(array_unique(explode(",", $yarn_brand_names))));

												$color_names='';
												$color_idsArr=array_unique(explode(",",$row["color_id"]));
												foreach($color_idsArr as $c_id)
												{
													if($color_names=='')
														$color_names=$color_library[$c_id];
													else
														$color_names.=",".$color_library[$c_id];
												}
												$color_names =implode(",",array_filter(array_unique(explode(",", $color_names))));

												$color_range_name='';
												$color_range_idsArr=array_unique(explode(",",$row["color_range_id"]));
												foreach($color_range_idsArr as $cr_id)
												{
													if($color_range_name=='')
														$color_range_name=$color_range[$cr_id];
													else
														$color_range_name.=",".$color_range[$cr_id];
												}
												$color_range_name =implode(",",array_filter(array_unique(explode(",", $color_range_name))));

												$stitch_length =implode(",",array_filter(array_unique(explode(",", chop($row["stitch_length"],",")))));
												$dia =implode(",",array_filter(array_unique(explode(",", chop($row["width"],",")))));
												$gsm =implode(",",array_filter(array_unique(explode(",", chop($row["gsm"],",")))));	
												

												if ($ksource == 1)
													$knitting_party = $company_arr[$knitCompa];
												else if ($ksource == 3)
													$knitting_party = $supplier_arr[$knitCompa];
												else
													$knitting_party = "&nbsp;";
												$yarn_issue_qty=$yarn_issue_array[$row["requisition_no"]][$yarn_prod_id][2]['yIssue_qty'];
												$yarn_issue_rtn_qty=$yarn_issue_array[$row["requisition_no"]][$yarn_prod_id][4]['yIssueRtn_qty'];
												$usd_rate=$yarn_rcv_rate_array[$yarn_prod_id]['usd_rate'];

												$previous_prod_qty=$previous_prod_qty_arr[$ksource][$knitCompa][$fsoNo][$program_no][$body_part_id][$yarn_prod_id]['previous_prod_qty'];
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
													<td width="30" align="center"><? echo $i;  ?></td>
													<td width="60"><? echo $yes_no[$row["within_group"]]; ?></td>
													<td width="100"><? echo $knitting_party; ?></td>
													<td width="100"><? echo $row["buyer_name"]; ?></td>
													<td width="100"><? echo $row["sales_booking_no"]; ?></td>
													<td width="100"><? echo $fsoNo; ?></td>
													<td width="70" title="<? echo $row["recv_number"]; ?>"><? echo $program_no; ?></td>
													<td width="70"><? echo $row["requisition_no"]; //$reqsn_details[$program_no]; ?></td>
													<td width="100" style="word-break: break-all; "><? echo $body_part[$body_part_id]; ?></td>
													<td width="100"><? echo $yCount; ?></td>
													<td width="150" style="word-break: break-all;" title="<? echo $yarn_prod_id; ?>"><? echo $yarn_desc_name; ?></td>
													<td width="100" title="<? echo $yarn_info_array[$yarn_prod_id]['yarn_type']; ?>"><? echo $yarn_type_name; ?></td>
													<td width="100" title="<? echo $yarn_prod_id; ?>"><? echo $yLot; ?></td>
													<td width="100" align="right"><? echo number_format($yarn_issue_qty,2,'.',''); $tot_yarn_issue_qty+=$yarn_issue_qty;?></td>
													<td width="100" align="right"><? echo number_format($yarn_issue_rtn_qty,2,'.',''); $tot_yarn_issue_rtn_qty+=$yarn_issue_rtn_qty;?></td>
													<td width="100" align="right"><? echo number_format($usd_rate,2,'.',''); ?></td>
													<td width="100"><? echo $yarn_brand_names; ?></td>
													<td width="100"><? echo $construction_arr[$row["deter_id"]]; ?></td>
													<td width="150"><? echo $composition_arr[$row["deter_id"]]; ?></td>
													<td width="130"><? echo $color_names; ?></td>
													<td width="100"><? echo $color_range_name; ?></td>
													<td width="60"><? echo $stitch_length; ?></td>
													<td width="60"><? echo $dia; ?></td>
													<td width="60"><? echo $gsm; ?></td>						
													
													<?
													if(!in_array($fsoNo."**".$program_no,$program_chk))
													{
														$program_chk[]=$fsoNo."**".$program_no;
														?>
														<td width="100" align="right" rowspan="<? echo $program_span ;?>" valign="middle"><? echo number_format($previous_prod_qty,2,'.',''); $prev_tot_prod_qty+=$previous_prod_qty; ?></td>
														<td width="100" align="right" rowspan="<? echo $program_span ;?>" valign="middle"><? echo number_format($row["production_qty"],2,'.',''); $current_tot_prod_qty+=$row["production_qty"]; ?></td>
														<td width="100" align="right" rowspan="<? echo $program_span ;?>" valign="middle"><? $total_prod_qty=$previous_prod_qty+$row["production_qty"]; echo number_format($total_prod_qty,2,'.',''); $tot_prod_qty+=$total_prod_qty; ?></td>
														<td width="" align="right" rowspan="<? echo $program_span ;?>" valign="middle"><? echo number_format($row["reject_qty"],2,'.',''); $tot_rejectQty+=$row["reject_qty"];?></td>
														<? 
														$grand_prev_tot_prod_qty += $previous_prod_qty;
														$grand_tot_current_prod_qty += $row["production_qty"];
														$grand_tot_prod_qty += $total_prod_qty;
														$grand_tot_rejectQnty += $row["reject_qty"];
													}
													?>	
												</tr>
												<?
												$i++;
												$grand_tot_yarn_issue_qty += $yarn_issue_qty;
												$grand_tot_yarn_issue_rtn_qty += $yarn_issue_rtn_qty;
											}
										}
									}
								}
							}
							?>
							<tr class="tbl_bottom">
								<td colspan="13" align="right"><b><? echo $knitting_source[$ksource]; ?> Total:</b></td>
								<td align="right"><? echo number_format($tot_yarn_issue_qty, 2, '.', ''); ?></td>
								<td align="right"><? echo number_format($tot_yarn_issue_rtn_qty, 2, '.', ''); ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($prev_tot_prod_qty, 2, '.', ''); ?></td>
								<td align="right"><? echo number_format($current_tot_prod_qty, 2, '.', ''); ?></td>
								<td align="right"><? echo number_format($tot_prod_qty, 2, '.', ''); ?></td>
								<td align="right"><? echo number_format($tot_rejectQty, 2, '.', ''); ?></td>
							</tr>
							<?
						}
						
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<th width="1180" align="right" colspan="13">Grand Total</th>
							<th width="100" align="right"><? echo number_format($grand_tot_yarn_issue_qty, 2, '.', ''); ?></th>
							<th width="100" align="right"><? echo number_format($grand_tot_yarn_issue_rtn_qty, 2, '.', ''); ?></th>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="130">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<th width="100" align="right"><? echo number_format($grand_prev_tot_prod_qty, 2, '.', ''); ?></th>
							<th width="100" align="right"><? echo number_format($grand_tot_current_prod_qty, 2, '.', ''); ?></th>
							<th width="100" align="right"><? echo number_format($grand_tot_prod_qty, 2, '.', ''); ?></th>				
							<th width="142" align="right"><? echo number_format($grand_tot_rejectQnty, 2, '.', ''); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<br>
		<?
		foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		echo "$total_data####$filename";
		exit();
	}
}

//getYarnType
/*function getYarnType($yarn_type_arr, $yarnProdId)
{
	global $yarn_type;
	$yarn_type_name = '';
	$expYPId = explode(",", $yarnProdId);
	$yarnTypeIdArr = array();
	foreach ($expYPId as $key => $val) {
		$yarnTypeIdArr[$yarn_type_arr[$val]] = $yarn_type_arr[$val];
	}

	foreach ($yarnTypeIdArr as $key => $val) {
		if ($yarn_type_name == '')
			$yarn_type_name = $yarn_type[$val];
		else
			$yarn_type_name .= "," . $yarn_type[$val];
	}
	return $yarn_type_name;
}*/
?>