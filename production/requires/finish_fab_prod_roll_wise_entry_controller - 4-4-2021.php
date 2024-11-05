<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

if ($action == "load_drop_down_store") {
	echo create_drop_down("cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select --", 0, "");
	exit();
}
if ($action == "load_drop_down_knit_loc") {
	$data = explode("**", $data);
	if($data[0] == 1)
	{
		echo create_drop_down("cbo_knit_location", 152, "select id,location_name from lib_location where company_id='$data[1]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	}
	else
	{
		echo create_drop_down("cbo_knit_location", 152, $blank_array, "", 1, "-- Select --", 0, "");
	}
	exit();
}

if ($action == "load_drop_down_loc")
{
	echo create_drop_down("cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");

	exit();
}
if ($action == "json_barcode_data") {
	$scanned_barcode_array = array();
	$barcode_dtlsId_array = array();
	$barcode_rollTableId_array = array();
	$dtls_data_arr = array();
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	$sql = "SELECT a.company_id,b.prod_id,b.body_part_id,b.item_description as febric_description_id, b.gsm, b.width_dia_type as width, b.barcode_no,b.roll_id,b.roll_no,b.po_id as po_breakdown_id,b.batch_qnty as qnty,b.is_sales, b.roll_id as roll_origin_id,a.booking_without_order,a.booking_no, 1 as type FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and b.barcode_no in($data)";

	/*
	$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales, c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(64)  and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)";*/


	//echo $sql;
	$data_array = sql_select($sql);
	$poIDs="";$salesIDs="";$prodIDs="";
	foreach ($data_array as $row) {
		if($row[csf('is_sales')] == 1){
			$salesIDs.=$row[csf('po_breakdown_id')].',';
		}else{
			$poIDs.=$row[csf('po_breakdown_id')].',';
		}
		$prodIDs.=$row[csf('prod_id')].',';
	}

	$poIDs_all=rtrim($poIDs,",");
	$prodIDs_all=rtrim($prodIDs,",");
	$poIDs_alls=explode(",",$poIDs_all);
	$poIDs_alls=array_chunk($poIDs_alls,999); // chunk for PO ID
	$po_id_cond=" and";
	foreach($poIDs_alls as $dtls_id)
	{
		if($po_id_cond==" and")  $po_id_cond.="(a.id in(".implode(',',$dtls_id).")"; else $po_id_cond.=" or a.id in(".implode(',',$dtls_id).")";
	}
	$po_id_cond.=")";
 	//echo $po_id_cond;die;
 	if ($salesIDs!="") {
	 	$isSalesPoIDs_all=rtrim($salesIDs,",");
	 	$isSalespoIDs_alls=explode(",",$isSalesPoIDs_all);
		$isSalespoIDs_alls=array_chunk($isSalespoIDs_alls,999); // chunk for Sales ID as PO ID
		$sales_po_id_cond=" and";
		foreach($isSalespoIDs_alls as $salesPO_id)
		{
			if($sales_po_id_cond==" and")  $sales_po_id_cond.="(a.id in(".implode(',',$salesPO_id).")"; else $sales_po_id_cond.=" or a.id in(".implode(',',$salesPO_id).")";
		}
		$sales_po_id_cond.=")";
		 // echo $salesPO_id;die;
 	}
 	$prod_determination=sql_select("select a.id, a.detarmination_id,b.barcode_no from product_details_master a,pro_batch_create_dtls b where a.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and a.id in($prodIDs_all) and b.barcode_no in($data) group by a.id, a.detarmination_id,b.barcode_no");
 	foreach($prod_determination as $row){
 		$prod_arr[$row[csf("barcode_no")]]['deter_d']=$row[csf("detarmination_id")];
 	}
	$po_arr = array();
	$po_sql = sql_select("select a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond");

	//echo "select a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond";

	foreach($po_sql as $po_row){
		$po_arr[$po_row[csf('id')]]['po_number'] = $po_row[csf('po_number')];
		$po_arr[$po_row[csf('id')]]['job_number'] = $po_row[csf('job_no_mst')];
		$po_arr[$po_row[csf('id')]]['style_ref_no'] = $po_row[csf('style_ref_no')];
	}

	$sales_arr=array();
	//$sql_sales=sql_select("select id,job_no,style_ref_no,po_job_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 $sales_po_id_cond");
	$sql_sales=sql_select("select a.id,a.job_no,a.style_ref_no,a.po_job_no,b.determination_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sales_po_id_cond group by a.id,a.job_no,a.style_ref_no,a.po_job_no,b.determination_id");

	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["po_number"] 			= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 		= $sales_row[csf('style_ref_no')];
		$sales_arr[$sales_row[csf('id')]]["job_number"] 		= $sales_row[csf('po_job_no')];
		$sales_arr[$sales_row[csf('id')]]["deter_d"] 			= $sales_row[csf('determination_id')];
	}

	$scanned_barcode_data = sql_select("select a.gsm, a.width, a.color_id,a.production_qty, a.receive_qnty, a.reject_qty, a.dia_width_type, a.machine_no_id, a.shift_name, a.rack_no, a.shelf_no, b.id, b.barcode_no, b.dtls_id,b.booking_without_order,b.booking_no from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in($data)");

	foreach ($scanned_barcode_data as $row) {
		$scanned_barcode_array[] = $row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]] = $row[csf('dtls_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]] = $row[csf('id')];
		$dtls_data_arr[$row[csf('barcode_no')]] = $row[csf('gsm')] . "**" . $row[csf('width')] . "**" . $color_arr[$row[csf('color_id')]] . "**" . number_format($row[csf('receive_qnty')],2) . "**" . number_format($row[csf('reject_qty')],2) . "**" . $row[csf('dia_width_type')] . "**" . $row[csf('machine_no_id')] . "**" . $row[csf('shift_name')] . "**" . $row[csf('rack_no')] . "**" . $row[csf('shelf_no')] . "**" . $row[csf('production_qty')] . "**". $row[csf('booking_without_order')] ."**". $row[csf('booking_no')];
	}

	$jsscanned_barcode_array = json_encode($scanned_barcode_array);
	$jsbarcode_dtlsId_array = json_encode($barcode_dtlsId_array);
	$jsbarcode_rollTableId_array = json_encode($barcode_rollTableId_array);
	$jsdtls_data_arr = json_encode($dtls_data_arr);

	$roll_details_array = array();
	$barcode_array = array();
	foreach ($data_array as $row) {

		if ($row[csf("type")] == 1) {
			$b_code = $row[csf("barcode_no")];
		} else {
			$b_code = $split_roll_bar_bf_batch_arr[$row[csf("roll_origin_id")]];
		}
		$febric_des_data=explode(",", $row[csf("febric_description_id")]);
		$roll_details_array[$b_code]['company_id'] 		= $row[csf("company_id")];
		$roll_details_array[$b_code]['body_part'] 		= $body_part[$row[csf("body_part_id")]];
		$roll_details_array[$b_code]['body_part_id'] 	= $row[csf("body_part_id")];
		$roll_details_array[$b_code]['prod_id'] 		= $row[csf("prod_id")];
		$roll_details_array[$b_code]['deter_d'] 		=$prod_arr[$row[csf("barcode_no")]]['deter_d'];
		//$roll_details_array[$b_code]['deter_d'] 		= $row[csf("febric_description_id")];
		$roll_details_array[$b_code]['gsm'] 			= trim($febric_des_data[2]);
		$roll_details_array[$b_code]['width'] 			= trim($febric_des_data[3]);
		$roll_details_array[$b_code]['is_sales'] 		= $row[csf("is_sales")];
		$roll_details_array[$b_code]['roll_id'] 		= $row[csf("roll_origin_id")];
		$roll_details_array[$b_code]['roll_no'] 		= $row[csf("roll_no")];

		if ($row[csf("is_sales")] == 1) {
			$roll_details_array[$b_code]['po_breakdown_id'] 	= $row[csf("po_breakdown_id")];
			$roll_details_array[$b_code]['po_number'] 	 		= $sales_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['job_number'] 			= $sales_arr[$row[csf("po_breakdown_id")]]['job_number'];
			$roll_details_array[$b_code]['style_ref_no'] 		= $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
		}
		else
		{
			$roll_details_array[$b_code]['po_breakdown_id'] 	= $row[csf("po_breakdown_id")];
			$roll_details_array[$b_code]['po_number'] 			= $po_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['job_number'] 			= $po_arr[$row[csf("po_breakdown_id")]]['job_number'];
			$roll_details_array[$b_code]['style_ref_no']		= $po_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];

		}

		$roll_details_array[$b_code]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');
		$roll_details_array[$b_code]['booking_without_order'] = $row[csf("booking_without_order")];
		$roll_details_array[$b_code]['booking_no'] = $row[csf("booking_no")];
		$barcode_array[$b_code] = $b_code;
	}

	$jsroll_details_array = json_encode($roll_details_array);
	$jsbarcode_array = json_encode($barcode_array);

	$batch_dtls_arr = array();
	$batch_barcode_arr = array();

	$shade_matched_sql =  sql_select("select a.batch_id,a.batch_ext_no, e.barcode_no, e.batch_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls e where a.id = e.mst_id and e.barcode_no in($data) and a.load_unload_id=2 and a.result=1 and a.status_active = 1 and a.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0");

	foreach ($shade_matched_sql as  $val) 
	{
		$shade_matched_arr[$val[csf("batch_id")]][$val[csf("batch_ext_no")]][$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	$sql = "SELECT a.id, a.extention_no, a.entry_form, a.batch_no, a.color_id, b.barcode_no, b.width_dia_type, b.batch_qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and b.barcode_no in($data)";
	$result = sql_select($sql);
	foreach ($result as $row) 
	{
		if($shade_matched_arr[$row[csf("id")]][$row[csf("extention_no")]][$row[csf("barcode_no")]])
		{
			$batch_dtls_arr[$row[csf("barcode_no")]]['batch_id'] = $row[csf("id")];
			$batch_dtls_arr[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
			$batch_dtls_arr[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
			$batch_dtls_arr[$row[csf("barcode_no")]]['color'] = $color_arr[$row[csf("color_id")]];
			$batch_dtls_arr[$row[csf("barcode_no")]]['entry_form'] = $row[csf("entry_form")];
			$batch_dtls_arr[$row[csf("barcode_no")]]['width_dia_type'] = $row[csf("width_dia_type")];
			$batch_dtls_arr[$row[csf("barcode_no")]]['batch_qnty'] = $row[csf("batch_qnty")];
			$batch_barcode_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}
	}

	$jsbatch_dtls_arr = json_encode($batch_dtls_arr);
	$jsbatch_barcode_arr = json_encode($batch_barcode_arr);

	$grey_iss_barcode_arr = return_library_array("select barcode_no from pro_roll_details where entry_form in(61,63) and status_active=1 and is_deleted=0 and barcode_no in($data)", "barcode_no", "barcode_no");
	$jsgrey_iss_barcode_arr = json_encode($grey_iss_barcode_arr);


	$compacting_arr = array();
	$compacting_details_arr = array();
	//$sql_compact = sql_select("select a.barcode_no,b.production_qty from pro_roll_details a,pro_fab_subprocess_dtls b where b.roll_id=a.id and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page=33 and a.barcode_no in($data)");
	$sql_compact = sql_select("select a.barcode_no,b.production_qty from pro_roll_details a,pro_fab_subprocess_dtls b where b.barcode_no=a.barcode_no and a.entry_form=64 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page=33 and a.barcode_no in($data)");
	foreach ($sql_compact as $c_id) {
		$compacting_arr[] = $c_id[csf('barcode_no')];
		$compacting_details_arr[$c_id[csf('barcode_no')]]['prod_qty'] = $c_id[csf('production_qty')];
	}
	$jscompacting_arr = json_encode($compacting_arr);
	$jscompacting_details_arr = json_encode($compacting_details_arr);

	$sql_delivery = sql_select("select barcode_no from  pro_roll_details where entry_form=67 and  status_active=1 and is_deleted=0 and barcode_no in($data)");
	$roll_delivery_arr = array();
	foreach ($sql_delivery as $inf) {
		$roll_delivery_arr[] = $inf[csf('barcode_no')];
	}
	$roll_delivery_arr = json_encode($roll_delivery_arr);

	//new dev
	$sqlStyle = sql_select("select a.id,b.style_ref_no from wo_po_break_down a, wo_po_details_master b,pro_roll_details c where a.job_no_mst=b.job_no and a.id=c.po_breakdown_id and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");

	$styleArr = array();
	foreach ($sqlStyle as $rowStyle) {
		$styleArr[] = $rowStyle[csf('style_ref_no')];
	}
	$styleArr = json_encode($styleArr);


	echo $all_json_data = $jsscanned_barcode_array . "__" . $jsbarcode_dtlsId_array . "__" . $jsbarcode_rollTableId_array . "__" . $jsdtls_data_arr . "__" . $jsroll_details_array . "__" . $jsbarcode_array . "__" . $jsbatch_dtls_arr . "__" . $jsbatch_barcode_arr . "__" . $jsgrey_iss_barcode_arr . "__" . $jscompacting_arr . "__" . $jscompacting_details_arr . "__" . $roll_delivery_arr . "__" . $styleArr;
	die;

}
if ($action == "json_barcode_data_old") {
	$scanned_barcode_array = array();
	$barcode_dtlsId_array = array();
	$barcode_rollTableId_array = array();
	$dtls_data_arr = array();
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	$roll_split_id = sql_select("select roll_id, barcode_no from pro_roll_details where ROLL_SPLIT_FROM > 0 AND ENTRY_FORM = 62 and barcode_no in($data) and status_active=1 and is_deleted=0");
	$roll_splt_before_batch_id = "";
	$split_roll_bar_bf_batch_arr = array();
	foreach ($roll_split_id as $row) {
		$roll_splt_before_batch_id .= $row[csf("roll_id")] . ",";
		$split_roll_bar_bf_batch_arr[$row[csf("roll_id")]] = $row[csf("barcode_no")];
	}
	$roll_splt_before_batch_id = chop($roll_splt_before_batch_id, ",");

	$sql_check_barcode_with_booking = sql_select("SELECT  c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(64) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");

	foreach ($sql_check_barcode_with_booking as $row) {
		$barcode_batch=$row[csf("barcode_no")];
	}

	$sql_check_barcode_in_transfter = sql_select("SELECT  c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(180) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");

	foreach ($sql_check_barcode_in_transfter as $row) {
		$barcode_transfer=$row[csf("barcode_no")];
	}



	if ($barcode_batch!="") // check latest batch creation for booking
	{
		if ($roll_splt_before_batch_id != "") {

			if ($barcode_transfer!="") // check booking  transfer for booking
			{
				$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales, c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.is_sales and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)
				union all
				SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales, c.id as roll_origin_id,c.booking_without_order,c.booking_no, 2 as type from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 and c.id in($roll_splt_before_batch_id)";
			}
			else
			{
				$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales,c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and c.entry_form in(64) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)
				union all
				SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id,b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales, c.id as roll_origin_id,c.booking_without_order,c.booking_no, 2 as type
				from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and c.entry_form in(64) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 and c.id in($roll_splt_before_batch_id)";
			}

		}else {
			$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales, c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(64)  and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)";
		}
	}
	else
	{
		if ($roll_splt_before_batch_id != "") {
			$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales, c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.is_sales and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)
			union all
			SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales, c.id as roll_origin_id,c.booking_without_order,c.booking_no, 2 as type from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 and c.id in($roll_splt_before_batch_id)";
		}else {
			$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales, c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)";
		}
	}


	echo $sql;
	$data_array = sql_select($sql);
	$poIDs="";$salesIDs="";
	foreach ($data_array as $row) {
		if($row[csf('is_sales')] == 1){
			$salesIDs.=$row[csf('po_breakdown_id')].',';
		}else{
			$poIDs.=$row[csf('po_breakdown_id')].',';
		}
	}



	$poIDs_all=rtrim($poIDs,",");
	$poIDs_alls=explode(",",$poIDs_all);
	$poIDs_alls=array_chunk($poIDs_alls,999); // chunk for PO ID
	$po_id_cond=" and";
	foreach($poIDs_alls as $dtls_id)
	{
		if($po_id_cond==" and")  $po_id_cond.="(a.id in(".implode(',',$dtls_id).")"; else $po_id_cond.=" or a.id in(".implode(',',$dtls_id).")";
	}
	$po_id_cond.=")";
 	//echo $po_id_cond;die;
 	/*if ($salesIDs!="") {
	 	$isSalesPoIDs_all=rtrim($salesIDs,",");
	 	$isSalespoIDs_alls=explode(",",$isSalesPoIDs_all);
		$isSalespoIDs_alls=array_chunk($isSalespoIDs_alls,999); // chunk for Sales ID as PO ID
		$sales_po_id_cond=" and";
		foreach($isSalespoIDs_alls as $salesPO_id)
		{
			if($sales_po_id_cond==" and")  $sales_po_id_cond.="(id in(".implode(',',$salesPO_id).")"; else $sales_po_id_cond.=" or id in(".implode(',',$salesPO_id).")";
		}
		$sales_po_id_cond.=")";
		 // echo $salesPO_id;die;
 	}*/

	$po_arr = array();
	$po_sql = sql_select("select a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond");

	//echo "select a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond";

	foreach($po_sql as $po_row){
		$po_arr[$po_row[csf('id')]]['po_number'] = $po_row[csf('po_number')];
		$po_arr[$po_row[csf('id')]]['job_number'] = $po_row[csf('job_no_mst')];
		$po_arr[$po_row[csf('id')]]['style_ref_no'] = $po_row[csf('style_ref_no')];
	}

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,style_ref_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");

	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["po_number"] 			= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 		= $sales_row[csf('style_ref_no')];
	}

	$scanned_barcode_data = sql_select("select a.gsm, a.width, a.color_id,a.production_qty, a.receive_qnty, a.reject_qty, a.dia_width_type, a.machine_no_id, a.shift_name, a.rack_no, a.shelf_no, b.id, b.barcode_no, b.dtls_id,b.booking_without_order,b.booking_no from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in($data)");
	foreach ($scanned_barcode_data as $row) {
		$scanned_barcode_array[] = $row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]] = $row[csf('dtls_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]] = $row[csf('id')];
		$dtls_data_arr[$row[csf('barcode_no')]] = $row[csf('gsm')] . "**" . $row[csf('width')] . "**" . $color_arr[$row[csf('color_id')]] . "**" . number_format($row[csf('receive_qnty')],2) . "**" . number_format($row[csf('reject_qty')],2) . "**" . $row[csf('dia_width_type')] . "**" . $row[csf('machine_no_id')] . "**" . $row[csf('shift_name')] . "**" . $row[csf('rack_no')] . "**" . $row[csf('shelf_no')] . "**" . $row[csf('production_qty')] . "**". $row[csf('booking_without_order')] ."**". $row[csf('booking_no')];
	}

	$jsscanned_barcode_array = json_encode($scanned_barcode_array);
	$jsbarcode_dtlsId_array = json_encode($barcode_dtlsId_array);
	$jsbarcode_rollTableId_array = json_encode($barcode_rollTableId_array);
	$jsdtls_data_arr = json_encode($dtls_data_arr);

	$transPoIds = sql_select("select a.barcode_no, a.po_breakdown_id from pro_roll_details a where a.entry_form=83 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in($data) and a.re_transfer=0");
	foreach ($transPoIds as $rowP) {
		$po_ids_arr[$rowP[csf("po_breakdown_id")]] = $rowP[csf("po_breakdown_id")];
		$transPoIdsArr[$rowP[csf("barcode_no")]]['po_breakdown_id'] = $rowP[csf("po_breakdown_id")];
		$transPoIdsArr[$rowP[csf("barcode_no")]]['po_number'] = $po_arr[$rowP[csf("po_breakdown_id")]]['po_number'];
		$transPoIdsArr[$rowP[csf("barcode_no")]]['job_number'] = $po_arr[$rowP[csf("po_breakdown_id")]]['job_number'];

		$transPoIdsArr[$rowP[csf("barcode_no")]]['style_ref_no'] = $po_arr[$rowP[csf("po_breakdown_id")]]['style_ref_no'];

		$transPoIdsArr[$rowP[csf("barcode_no")]]['po_number'] = $sales_arr[$rowP[csf("po_breakdown_id")]]['po_number'];
		$transPoIdsArr[$rowP[csf("barcode_no")]]['style_ref_no'] = $sales_arr[$rowP[csf("po_breakdown_id")]]['style_ref_no'];
	}






	$roll_details_array = array();
	$barcode_array = array();
	foreach ($data_array as $row) {

		if ($row[csf("type")] == 1) {
			$b_code = $row[csf("barcode_no")];
		} else {
			$b_code = $split_roll_bar_bf_batch_arr[$row[csf("roll_origin_id")]];
		}

		$roll_details_array[$b_code]['company_id'] = $row[csf("company_id")];
		$roll_details_array[$b_code]['body_part'] = $body_part[$row[csf("body_part_id")]];
		$roll_details_array[$b_code]['body_part_id'] = $row[csf("body_part_id")];
		$roll_details_array[$b_code]['prod_id'] = $row[csf("prod_id")];
		$roll_details_array[$b_code]['deter_d'] = $row[csf("febric_description_id")];
		$roll_details_array[$b_code]['gsm'] = $row[csf("gsm")];
		$roll_details_array[$b_code]['width'] = $row[csf("width")];
		$roll_details_array[$b_code]['is_sales'] = $row[csf("is_sales")];
		//$roll_details_array[$b_code]['roll_id'] = $row[csf("roll_id")];
		$roll_details_array[$b_code]['roll_id'] = $row[csf("roll_origin_id")];
		$roll_details_array[$b_code]['roll_no'] = $row[csf("roll_no")];


		if ($transPoIdsArr[$b_code] == "") {
			$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("po_breakdown_id")];


			$roll_details_array[$b_code]['po_number'] = $po_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['job_number'] = $po_arr[$row[csf("po_breakdown_id")]]['job_number'];
			$roll_details_array[$b_code]['style_ref_no'] = $po_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];

			//$roll_details_array[$b_code]['po_number'] 	 = $sales_arr[$row[csf("po_breakdown_id")]]['po_number'];
			//$roll_details_array[$b_code]['style_ref_no'] = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
		} else {
			$roll_details_array[$b_code]['po_breakdown_id'] = $transPoIdsArr[$b_code]['po_breakdown_id'];
			$roll_details_array[$b_code]['po_number'] = $po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['po_number'];
			$roll_details_array[$b_code]['job_number'] = $po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['job_number'];

			$roll_details_array[$b_code]['style_ref_no'] = $po_arr[$transPoIdsArr[$b_code]['po_breakdown_id']]['style_ref_no'];

			$roll_details_array[$b_code]['po_number']  = $sales_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['style_ref_no'] = $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
		}

		$roll_details_array[$b_code]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');
		$roll_details_array[$b_code]['booking_without_order'] = $row[csf("booking_without_order")];
		$roll_details_array[$b_code]['booking_no'] = $row[csf("booking_no")];

		$barcode_array[$b_code] = $b_code;
	}

	//if($barcode_array
	//print_r($roll_details_array);echo "jahid";die;
	//echo count($barcode_array); echo "jahid";die;


	$jsroll_details_array = json_encode($roll_details_array);
	$jsbarcode_array = json_encode($barcode_array);


	$batch_dtls_arr = array();
	$batch_barcode_arr = array();
	$sql = "SELECT a.id, a.entry_form, a.batch_no, a.color_id, b.barcode_no, b.width_dia_type, b.batch_qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and b.barcode_no in($data)";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$batch_dtls_arr[$row[csf("barcode_no")]]['batch_id'] = $row[csf("id")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['color'] = $color_arr[$row[csf("color_id")]];
		$batch_dtls_arr[$row[csf("barcode_no")]]['entry_form'] = $row[csf("entry_form")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['width_dia_type'] = $row[csf("width_dia_type")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['batch_qnty'] = $row[csf("batch_qnty")];
		$batch_barcode_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}

	$jsbatch_dtls_arr = json_encode($batch_dtls_arr);
	$jsbatch_barcode_arr = json_encode($batch_barcode_arr);

	$grey_iss_barcode_arr = return_library_array("select barcode_no from pro_roll_details where entry_form in(61,63) and status_active=1 and is_deleted=0 and barcode_no in($data)", "barcode_no", "barcode_no");
	$jsgrey_iss_barcode_arr = json_encode($grey_iss_barcode_arr);


	$compacting_arr = array();
	$compacting_details_arr = array();
	$sql_compact = sql_select("select a.barcode_no,b.production_qty from pro_roll_details a,pro_fab_subprocess_dtls b where b.roll_id=a.id and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page=33 and a.barcode_no in($data)");
	foreach ($sql_compact as $c_id) {
		$compacting_arr[] = $c_id[csf('barcode_no')];
		$compacting_details_arr[$c_id[csf('barcode_no')]]['prod_qty'] = $c_id[csf('production_qty')];
	}
	$jscompacting_arr = json_encode($compacting_arr);
	$jscompacting_details_arr = json_encode($compacting_details_arr);

	$sql_delivery = sql_select("select barcode_no from  pro_roll_details where entry_form=67 and  status_active=1 and is_deleted=0 and barcode_no in($data)");
	$roll_delivery_arr = array();
	foreach ($sql_delivery as $inf) {
		$roll_delivery_arr[] = $inf[csf('barcode_no')];
	}
	$roll_delivery_arr = json_encode($roll_delivery_arr);

	//new dev
	$sqlStyle = sql_select("select a.id,b.style_ref_no from wo_po_break_down a, wo_po_details_master b,pro_roll_details c where a.job_no_mst=b.job_no and a.id=c.po_breakdown_id and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");

	$styleArr = array();
	foreach ($sqlStyle as $rowStyle) {
		$styleArr[] = $rowStyle[csf('style_ref_no')];
	}
	$styleArr = json_encode($styleArr);


	echo $all_json_data = $jsscanned_barcode_array . "__" . $jsbarcode_dtlsId_array . "__" . $jsbarcode_rollTableId_array . "__" . $jsdtls_data_arr . "__" . $jsroll_details_array . "__" . $jsbarcode_array . "__" . $jsbatch_dtls_arr . "__" . $jsbatch_barcode_arr . "__" . $jsgrey_iss_barcode_arr . "__" . $jscompacting_arr . "__" . $jscompacting_details_arr . "__" . $roll_delivery_arr . "__" . $styleArr;
	die;

}

if ($action == "load_drop_down_knitting_com") {
	$data = explode("**", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_service_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $company_id, "check_is_inhouse($data[0]);", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_service_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "check_is_inhouse($data[0]);");
	} else {
		echo create_drop_down("cbo_service_company", 152, $blank_array, "", 1, "-- Select --", 0, "");
	}
	exit();
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0) {  // Insert Here
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0)
			$year_cond = "YEAR(insert_date)";
		else if ($db_type == 2)
			$year_cond = "to_char(insert_date,'YYYY')";
		else
			$year_cond = ""; //defined Later

		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_id),'FFPR',66,date("Y",time())));

		$field_array = "id,recv_number_prefix,recv_number_prefix_num,recv_number,receive_date,company_id,knitting_source,knitting_company,item_category,entry_form,challan_no,store_id,location_id, knitting_location_id,inserted_by,insert_date";
		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "'," . $txt_recv_date . "," . $cbo_company_id . "," . $cbo_service_source . "," . $cbo_service_company . ",2,66," . $txt_recv_challan . "," . $cbo_store_name . "," . $cbo_location .",".$cbo_knit_location. "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		$productDataArray = array();
		$stockArray = array();
		$productData = sql_select("select id, company_id, detarmination_id, current_stock, gsm, dia_width, color from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0");
		foreach ($productData as $row) {
			$productDataArray[$row[csf('company_id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color')]] = $row[csf('id')];
			$stockArray[$row[csf('id')]] = $row[csf('current_stock')];
		}

		$field_array_prod = "id, company_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, inserted_by, insert_date";
		$field_array_prod_update = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";

		if (str_replace("'", "", $fabric_store_auto_update) == 1) {
			$order_rate = 0;
			$order_amount = 0;
			$cons_rate = 0;
			$cons_amount = 0;
			$field_array_trans = "id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, rack, self, inserted_by, insert_date";
		}

		$rate = 0;
		$amount = 0;
		$field_array_dtls = "id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, dia_width_type, color_id,production_qty, receive_qnty, reject_qty, order_id, machine_no_id, shift_name, rack_no, shelf_no, roll_id, roll_no, barcode_no, inserted_by, insert_date";

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form,qnty, reject_qnty, qc_pass_qnty, roll_no, roll_id, inserted_by, insert_date,is_sales,booking_without_order,booking_no";

		$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, inserted_by, insert_date,is_sales";

		$batchtbl_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
		//$field_array_batch = "id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, inserted_by, insert_date";

		//$field_array_batch_dtls = "id, mst_id, po_id, prod_id, item_description, roll_no, width_dia_type, roll_id, barcode_no, batch_qnty, dtls_id, inserted_by, insert_date";


		$barcodeNos = '';
		$prod_id_array = array();
		$prod_data_array = array();
		$prod_new_array = array();
		$company_id = str_replace("'", "", $cbo_company_id);
		$z = 1;
		$batch_weight = 0;
		$txt_batch_no = '';
		for ($j = 1; $j <= $tot_row; $j++)
		{
			$id_dtls=return_next_id_by_sequence( "PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con) ;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);

			$barcodeNo = "barcodeNo_" . $j;
			$rollNo = "rollNo_" . $j;
			$batchNo = "batchNo_" . $j;
			$bodyPartId = "bodyPartId_" . $j;
			$consComp = "consComp_" . $j;
			$gsm = "gsm_" . $j;
			$dia = "dia_" . $j;
			$color = "color_" . $j;
			$diaType = "diaType_" . $j;
			$reJectQty = "reJectQty_" . $j;
			$qcPassQty = "qcPassQty_" . $j;
			$cboMachine = "cboMachine_" . $j;
			$cboShift = "cboShift_" . $j;
			$rack = "rack_" . $j;
			$shelf = "shelf_" . $j;
			$batchId = "batchId_" . $j;
			$deterId = "deterId_" . $j;
			$orderId = "orderId_" . $j;
			$rollWgt = "rollWgt_" . $j;
			$rollId = "rollId_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$booking_without_order = "booking_without_order_".$j;
			$booking_no = "booking_no_".$j;

			$batch_id = $$batchId;
			$color_name = trim($$color);

			/*if (!in_array($color_name, $new_array_color)) {
				$color_id = return_id($color_name, $color_arr, "lib_color", "id,color_name");
				$new_array_color[$color_id] = $color_name;
			} else
				$color_id = array_search($color_name, $new_array_color);*/

			if(str_replace("'","",$color_name)!="")
			{
				if (!in_array(str_replace("'","",$color_name),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$color_name), $color_arr, "lib_color", "id,color_name","66");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$color_name);

				}
				else $color_id =  array_search(str_replace("'","",$color_name), $new_array_color);
			}
			else
			{
				$color_id=0;
			}	

			$prod_id = $productDataArray[$company_id][$$deterId][$$gsm][$$dia][$color_id];
			if (str_replace("'", "", $fabric_store_auto_update) == 1) {
				$stock_qnty = $$qcPassQty;
				$last_purchased_qnty = $$qcPassQty;
			} else {
				$stock_qnty = 0;
				$last_purchased_qnty = 0;
			}

			$prod_name_dtls = trim($$consComp) . ", " . trim($$gsm) . ", " . trim($$dia);
			if ($prod_id == "") {
				$dataString = $$deterId . "**" . $$consComp . "**" . $prod_name_dtls . "**" . $color_id . "**" . trim($$gsm) . "**" . trim($$dia);
				$prod_id = array_search($dataString, $prod_data_array);
				if ($prod_id == "")
				{
					$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$prod_id = $product_id;
					$prod_data_array[$prod_id] = $dataString;
					$prod_new_array[$prod_id] = $stock_qnty;
					//$product_id = $product_id + 1;
				}
				else
				{
					$prod_new_array[$prod_id] += $stock_qnty;
				}
			} else {
				$current_stock = $stockArray[$prod_id] + $stock_qnty;
				$prod_id_array[] = $prod_id;
				$data_array_prod_update[$prod_id] = explode("*", ($avg_rate_per_unit . "*'" . $last_purchased_qnty . "'*'" . $current_stock . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}

			if (str_replace("'", "", $fabric_store_auto_update) == 1)
			{
				$id_trans =return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				if ($data_array_trans != "")
					$data_array_trans .= ",";
				$data_array_trans .= "(" . $id_trans . "," . $id . "," . $cbo_company_id . ",'" . $prod_id . "',2,1," . $txt_recv_date . "," . $cbo_store_name . ",12,'" . $$qcPassQty . "'," . $rate . "," . $amount . ",12," . $$qcPassQty . ",'" . $$reJectQty . "'," . $rate . "," . $amount . "," . $$qcPassQty . "," . $amount . ",'" . $$cboMachine . "','" . $$rack . "','" . $$shelf . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
			else
			{
				$id_trans = 0;
			}

			/*if ($batch_id == "")
			{
				if ($z == 1)
				{
					if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no='" . $$batchNo . "' and company_id=$cbo_company_id") == 1)
					{
						//check_table_status($_SESSION['menu_id'], 0);
						echo "11**0";
						die;
					}
					$batch_weight = $$qcPassQty;
					$txt_batch_no = $$batchNo;
				} else {
					$batch_weight += $$qcPassQty;
				}
				$color_id_batch = $color_id;
				$batch_id = $batchtbl_id;
				//if ($data_array_batch_dtls != "") $data_array_batch_dtls .= ",";
				//$data_array_batch_dtls .= "(" . $id_dtls_batch . ",'" . $batch_id . "'," . $$orderId . "," . $prod_id . ",'" . $prod_name_dtls . "','" . $$rollNo . "','" . $$diaType . "','" . $$rollId . "'," . $$barcodeNo . "," . $$qcPassQty . "," . $id_dtls . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//$id_dtls_batch = $id_dtls_batch + 1;

				//if ($data_array_roll_for_batch != "") $data_array_roll_for_batch .= ",";

				//$data_array_roll_for_batch .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $batch_id . "," . $id_dtls_batch . ",'" . $$orderId . "',66,'" . $$rollWgt . "','" . $$reJectQty . "','" . $$qcPassQty . "','" . $$rollNo . "','" . $$rollId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//$id_roll = $id_roll + 1;

				$z++;
			}
		*/
			if ($data_array_dtls != "")
				$data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $id . "," . $id_trans . ",'" . $prod_id . "','" . $batch_id . "','" . $$bodyPartId . "','" . $$deterId . "','" . $$gsm . "','" . $$dia . "','" . $$diaType . "','" . $color_id . "','" . $$rollWgt . "','" . $$qcPassQty . "','" . $$reJectQty . "','" . $$orderId . "','" . $$cboMachine . "','" . $$cboShift . "','" . $$rack . "','" . $$shelf . "','" . $$rollId . "','" . $$rollNo . "','" . $$barcodeNo . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			if ($data_array_roll != "") $data_array_roll .= ",";
			$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $id . "," . $id_dtls . ",'" . $$orderId . "',66,'" . $$rollWgt . "','" . $$reJectQty . "','" . $$qcPassQty . "','" . $$rollNo . "','" . $$rollId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time. "','" . $$IsSalesId . "','" . $$booking_without_order . "','" . $$booking_no . "')";

			if(str_replace("'", "", $$booking_without_order) != 1)
			{
				if ($data_array_prop != "") $data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $id_trans . ",1,66,'" . $id_dtls . "','" . $$orderId . "','" . $prod_id . "','" . $color_id . "','" . $$qcPassQty . "','" . $$reJectQty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$IsSalesId . "')";
			}
			$barcodeNos .= $$barcodeNo . "__" . $id_dtls . "__" . $id_roll . ",";

		}


		$avg_rate_per_unit = 0;
		$stock_value = 0;
		foreach ($prod_new_array as $prod_id => $current_stock) {
			$product_data = explode("**", $prod_data_array[$prod_id]);
			$deterId = $product_data[0];
			$consComp = trim($product_data[1]);
			$prod_name_dtls = $product_data[2];
			$color_id = $product_data[3];
			$gsm = $product_data[4];
			$dia = $product_data[5];
			$last_purchased_qnty = $current_stock;

			if ($data_array_prod != "")
				$data_array_prod .= ",";
			$data_array_prod .= "(" . $prod_id . "," . $cbo_company_id . ",2," . $deterId . ",'" . $consComp . "','" . $prod_name_dtls . "',12," . $avg_rate_per_unit . "," . $last_purchased_qnty . "," . $current_stock . "," . $stock_value . "," . $color_id . ",'" . $gsm . "','" . $dia . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		}

		//echo "10**insert into inv_receive_master (".$field_array.") values ".$data_array;die;
		$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
		if ($rID)
			$flag = 1;
		else
			$flag = 0;

		if (count($prod_id_array) > 0) {
			if (str_replace("'", "", $fabric_store_auto_update) == 1) {
				$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array));
				if ($flag == 1) {
					if ($prodUpdate)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		if ($data_array_prod != "") {
			//echo "10**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
			$prodInsert = sql_insert("product_details_master", $field_array_prod, $data_array_prod, 0);
			if ($flag == 1) {
				if ($prodInsert)
					$flag = 1;
				else
					$flag = 0;
			}
		}


		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		if (str_replace("'", "", $fabric_store_auto_update) == 1) {
			$transInsert = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
			if ($flag == 1) {
				if ($transInsert)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		//echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID2 = sql_insert("pro_finish_fabric_rcv_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1) {
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}

		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID3 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		if ($flag == 1) {
			if ($rID3)
				$flag = 1;
			else
				$flag = 0;
		}

		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop != "")
		{
			$rID4 = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
			if ($flag == 1) {
				if ($rID4)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		

		/*if ($data_array_batch_dtls != "") {
			//$data_array_batch = "(" . $batchtbl_id . ",'" . $txt_batch_no . "',66," . $txt_recv_date . "," . $cbo_company_id . ",'" . $color_id_batch . "','" . $batch_weight . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$rID5 = sql_insert("pro_batch_create_mst", $field_array_batch, $data_array_batch, 1);
			if ($flag == 1) {
				if ($rID5)
					$flag = 1;
				else
					$flag = 0;
			}

			$rID6 = sql_insert("pro_batch_create_dtls", $field_array_batch_dtls, $data_array_batch_dtls, 1);
			if ($flag == 1) {
				if ($rID6)
					$flag = 1;
				else
					$flag = 0;
			}

			//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$rID7 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll_for_batch, 1);
			if ($flag == 1) {
				if ($rID7)
					$flag = 1;
				else
					$flag = 0;
			}
		}*/
		//echo $flag;die;
		/* oci_rollback($con);
		  echo "10**".$flag;die; */
		//echo $rID."=".$rID2."=".$rID3."=".$rID4."=".$rID5."=".$rID6."=".$rID7;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 1) {   // Update Here
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		//echo "10**".$new_batch_no."**".$new_batch_id;die;
		$field_array = "receive_date*knitting_source*knitting_company*challan_no*store_id*location_id*knitting_location_id*updated_by*update_date";
		$data_array = $txt_recv_date . "*" . $cbo_service_source . "*" . $cbo_service_company . "*" . $txt_recv_challan . "*" . $cbo_store_name . "*" . $cbo_location ."*" . $cbo_knit_location . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$productDataArray = array();
		$stockArray = array();
		$productData = sql_select("select id, company_id, detarmination_id, current_stock, gsm, dia_width, color from product_details_master where item_category_id=2 and status_active=1 and is_deleted=0");
		foreach ($productData as $row) {
			$productDataArray[$row[csf('company_id')]][$row[csf('detarmination_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color')]] = $row[csf('id')];
			$stockArray[$row[csf('id')]] = $row[csf('current_stock')];
		}

		$txt_deleted_id = str_replace("'", "", $txt_deleted_id);
		$deleted_id_arr = explode(",", $txt_deleted_id);
		$new_batch_no = str_replace("'", "", $new_batch_no);

		$new_batch_no = implode(",",array_unique(array_filter(explode(",", $new_batch_no))));
		$new_batch_id = str_replace("'", "", $new_batch_id);
		$new_batch_id = implode(",",array_unique(array_filter(explode(",", $new_batch_id))));
		//echo "10**".$new_batch_no;die;
		$adj_prod_array = array();
		$update_dtls_id = '';
		$batch_ids = '';
		$update_trans_id_arr = array();
		$update_trans_id = '';
		$prevData = sql_select("select id, trans_id, prod_id, batch_id, receive_qnty from pro_finish_fabric_rcv_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach ($prevData as $row) {
			$stockArray[$row[csf('prod_id')]] -= $row[csf('receive_qnty')];
			$update_trans_id_arr[$row[csf('id')]] = $row[csf('trans_id')];
			$update_dtls_id .= $row[csf('id')] . ",";
			$batch_ids = $row[csf('batch_id')] . ",";

			if (in_array($row[csf('id')], $deleted_id_arr)) {
				if($row[csf('trans_id')]>0)
				{
					$update_trans_id .= $row[csf('trans_id')] . ",";
				}
				$adj_prod_array[$row[csf('prod_id')]] = $row[csf('prod_id')];
			}
		}

		if ($new_batch_id == "") {
			$prev_new_batch_id = return_field_value("id", "pro_batch_create_mst", "id in(" . substr($batch_ids, 0, -1) . ") and entry_form=66");
			if ($prev_new_batch_id != "")
				$cond = " and id<>$prev_new_batch_id";
			if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no ='" . implode(",",array_filter(explode(",", $new_batch_no))) . "' and company_id=$cbo_company_id $cond") == 1) {
				//check_table_status($_SESSION['menu_id'], 0);
				echo "11**0";
				die;
			}

			if ($prev_new_batch_id == "") {
				$batchtbl_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
				//$field_array_batch = "id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, inserted_by, insert_date";
			} else {
				//$field_array_batch = "batch_no*batch_date*color_id*batch_weight*updated_by*update_date";
				$batchtbl_id = $prev_new_batch_id;
			}
		} else {
			//$field_array_batch = "batch_no*batch_date*color_id*batch_weight*updated_by*update_date";
			$batchtbl_id = $new_batch_id;
			$entry_form[$new_batch_id] = 66;
		}

		$field_array_prod = "id, company_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, gsm, dia_width, inserted_by, insert_date";
		$field_array_prod_update = "avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";

		if (str_replace("'", "", $fabric_store_auto_update) == 1) {
			$order_rate = 0;
			$order_amount = 0;
			$cons_rate = 0;
			$cons_amount = 0;
			$field_array_trans = "id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, rack, self, inserted_by, insert_date";
			$field_array_trans_update = "prod_id*transaction_date*store_id*order_qnty*cons_quantity*cons_reject_qnty*balance_qnty*machine_id*rack*self*updated_by*update_date";
		}

		$rate = 0;
		$amount = 0;
		$stock_value = 0;
		$avg_rate_per_unit = 0;
		$field_array_dtls = "id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, dia_width_type, color_id,production_qty, receive_qnty, reject_qty, order_id, machine_no_id, shift_name, rack_no, shelf_no, roll_id, roll_no, barcode_no, inserted_by, insert_date";
		$field_array_dtls_update = "prod_id*batch_id*body_part_id*fabric_description_id*gsm*width*dia_width_type*color_id*production_qty*receive_qnty*
		reject_qty*order_id*machine_no_id*shift_name*rack_no*shelf_no*roll_id*roll_no*barcode_no*updated_by*update_date";
		//$id_dtls = return_next_id("id", "pro_finish_fabric_rcv_dtls", 1);

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, reject_qnty, qc_pass_qnty, roll_no, roll_id, inserted_by, insert_date,is_sales,booking_without_order,booking_no";

		$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, returnable_qnty, inserted_by, insert_date,is_sales";

		//$field_array_batch_dtls = "id, mst_id, po_id, prod_id, item_description, roll_no, width_dia_type, roll_id, barcode_no, batch_qnty, dtls_id, inserted_by, insert_date";
		//$id_dtls_batch = return_next_id("id", "pro_batch_create_dtls", 1);

		$barcodeNos = '';
		$prod_id_array = array();
		$prod_data_array = array();
		$prod_new_array = array();
		$company_id = str_replace("'", "", $cbo_company_id);
		$z = 1;
		$batch_weight = 0;
		$txt_batch_no = '';
		for ($j = 1; $j <= $tot_row; $j++) 
		{

			$id_dtls=return_next_id_by_sequence( "PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con) ;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);

			$barcodeNo = "barcodeNo_" . $j;
			$rollNo = "rollNo_" . $j;
			$batchNo = "batchNo_" . $j;
			$bodyPartId = "bodyPartId_" . $j;
			$consComp = "consComp_" . $j;
			$gsm = "gsm_" . $j;
			$dia = "dia_" . $j;
			$color = "color_" . $j;
			$diaType = "diaType_" . $j;
			$reJectQty = "reJectQty_" . $j;
			$qcPassQty = "qcPassQty_" . $j;
			$cboMachine = "cboMachine_" . $j;
			$cboShift = "cboShift_" . $j;
			$rack = "rack_" . $j;
			$shelf = "shelf_" . $j;
			$batchId = "batchId_" . $j;
			$deterId = "deterId_" . $j;
			$orderId = "orderId_" . $j;
			$rollWgt = "rollWgt_" . $j;
			$rollId = "rollId_" . $j;
			$dtlsId = "dtlsId_" . $j;
			$rolltableId = "rolltableId_" . $j;
			$IsSalesId = "IsSalesId_" . $j;
			$booking_without_order = "booking_without_order_" . $j;
			$booking_no = "booking_no_" . $j;


			$batch_id = $$batchId;
			$color_name = trim($$color);

			/*if (!in_array($color_name, $new_array_color)) {
				$color_id = return_id($color_name, $color_arr, "lib_color", "id,color_name");
				$new_array_color[$color_id] = $color_name;
			} else
				$color_id = array_search($color_name, $new_array_color);*/

			if(str_replace("'","",$color_name)!="")
			{
				if (!in_array(str_replace("'","",$color_name),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$color_name), $color_arr, "lib_color", "id,color_name","66");
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$color_name);

				}
				else $color_id =  array_search(str_replace("'","",$color_name), $new_array_color);
			}
			else
			{
				$color_id=0;
			}	

			$prod_id = $productDataArray[$company_id][$$deterId][$$gsm][$$dia][$color_id];
			if (str_replace("'", "", $fabric_store_auto_update) == 1) {
				$stock_qnty = $$qcPassQty;
				$last_purchased_qnty = $$qcPassQty;
			} else {
				$stock_qnty = 0;
				$last_purchased_qnty = 0;
			}

			$prod_name_dtls = trim($$consComp) . ", " . trim($$gsm) . ", " . trim($$dia);
			if ($prod_id == "") {
				$dataString = $$deterId . "**" . $$consComp . "**" . $prod_name_dtls . "**" . $color_id . "**" . trim($$gsm) . "**" . trim($$dia);
				$prod_id = array_search($dataString, $prod_data_array);
				if ($prod_id == "") {
					$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$prod_data_array[$prod_id] = $dataString;
					$prod_new_array[$prod_id] = $stock_qnty;
					$product_id = $product_id + 1;
				} else {
					$prod_new_array[$prod_id] += $stock_qnty;
				}
			} else {
				$current_stock = $stockArray[$prod_id] + $stock_qnty;
				$prod_id_array[] = $prod_id;
				$data_array_prod_update[$prod_id] = explode("*", ($avg_rate_per_unit . "*'" . $last_purchased_qnty . "'*'" . $current_stock . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}

			if (str_replace("'", "", $fabric_store_auto_update) == 1) {
				if ($$rolltableId > 0) {
					$transId = $update_trans_id_arr[$$dtlsId];
					$transId_arr[] = $transId;
					$data_array_update_trans[$transId] = explode("*", ($prod_id . "*" . $txt_recv_date . "*" . $cbo_store_name . "*'" . $$qcPassQty . "'*'" . $$qcPassQty . "'*'" . $$reJectQty . "'*'" . $$qcPassQty . "'*'" . $$cboMachine . "'*'" . $$rack . "'*'" . $$shelf . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				} else {
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					if ($data_array_trans != "")
						$data_array_trans .= ",";
					$data_array_trans .= "(" . $id_trans . "," . $update_id . "," . $cbo_company_id . ",'" . $prod_id . "',2,1," . $txt_recv_date . "," . $cbo_store_name . ",12,'" . $$qcPassQty . "'," . $rate . "," . $amount . ",12," . $$qcPassQty . "," . $$qcPassQty . ",'" . $$reJectQty . "'," . $rate . "," . $amount . "," . $$qcPassQty . "," . $amount . ",'" . $$cboMachine . "','" . $$rack . "','" . $$shelf . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$transId = $id_trans;
				}
			} else {
				$transId = 0;
			}

			if ($$rolltableId > 0) {
				$dtlsId_arr[] = $$dtlsId;
				$data_array_update_dtls[$$dtlsId] = explode("*", ($prod_id . "*'" . $batch_id . "'*'" . $$bodyPartId . "'*'" . $$deterId . "'*'" . $$gsm . "'*'" . $$dia . "'*'" . $$diaType . "'*'" . $color_id . "'*'" . $$rollWgt . "'*'" . $$qcPassQty . "'*'" . $$reJectQty . "'*'" . $$orderId . "'*'" . $$cboMachine . "'*'" . $$cboShift . "'*'" . $$rack . "'*'" . $$shelf . "'*'" . $$rollId . "'*'" . $$rollNo . "'*'" . $$barcodeNo . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				$dtlsId_prop = $$dtlsId;
			} else {
				if ($data_array_dtls != "")
					$data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $update_id . "," . $transId . ",'" . $prod_id . "','" . $batch_id . "','" . $$bodyPartId . "','" . $$deterId . "','" . $$gsm . "','" . $$dia . "','" . $$diaType . "','" . $color_id . "','" . $$rollWgt . "','" . $$qcPassQty . "','" . $$reJectQty . "','" . $$orderId . "','" . $$cboMachine . "','" . $$cboShift . "','" . $$rack . "','" . $$shelf . "','" . $$rollId . "','" . $$rollNo . "','" . $$barcodeNo . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$dtlsId_prop = $id_dtls;
				//$id_dtls = $id_dtls + 1;
			}

			if ($data_array_roll != "")
				$data_array_roll .= ",";
			$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $update_id . "," . $dtlsId_prop . ",'" . $$orderId . "',66,'" . $$rollWgt . "','" . $$reJectQty . "','" . $$qcPassQty . "','" . $$rollNo . "','" . $$rollId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$IsSalesId . "','" . $$booking_without_order . "','" . $$booking_no . "')";

			$barcodeNos .= $$barcodeNo . "__" . $dtlsId_prop . "__" . $id_roll . ",";

			if(str_replace("'", "", $$booking_without_order) != 1 )
			{
				if ($data_array_prop != "")
				$data_array_prop .= ",";
				$data_array_prop .= "(" . $id_prop . "," . $transId . ",1,66,'" . $dtlsId_prop . "','" . $$orderId . "','" . $prod_id . "','" . $color_id . "','" . $$qcPassQty . "','" . $$reJectQty . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$IsSalesId . "')";
			}

			if ($batch_id == "" || $entry_form[$batch_id] == 66) {
				$batch_weight += $$qcPassQty;
				$color_id_batch = $color_id;

				//if ($data_array_batch_dtls != "") $data_array_batch_dtls .= ",";
				//$data_array_batch_dtls .= "(" . $id_dtls_batch . ",'" . $batchtbl_id . "'," . $$orderId . "," . $prod_id . ",'" . $prod_name_dtls . "','" . $$rollNo . "','" . $$diaType . "','" . $$rollId . "'," . $$barcodeNo . "," . $$qcPassQty . "," . $id_dtls . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				//$id_dtls_batch = $id_dtls_batch + 1;
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				//if ($data_array_roll_for_batch != "") $data_array_roll_for_batch .= ",";
				//$data_array_roll_for_batch .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $batchtbl_id . "," . $id_dtls_batch . ",'" . $$orderId . "',66,'" . $$rollWgt . "','" . $$reJectQty . "','" . $$qcPassQty . "','" . $$rollNo . "','" . $$rollId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time  . "','" . $$IsSalesId . "','" . $$booking_without_order . "','" . $$booking_no . "')";
				//$id_roll = $id_roll + 1;

				$z++;
			}
			//$id_prop = $id_prop + 1;
		}

		foreach ($adj_prod_array as $prod_id) {
			if (!in_array($prod_id, $prod_id_array)) {
				$current_stock = $stockArray[$prod_id];
				$prod_id_array[] = $prod_id;
				$data_array_prod_update[$prod_id] = explode("*", ($avg_rate_per_unit . "*'" . $last_purchased_qnty . "'*'" . $current_stock . "'*'" . $stock_value . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}
		}

		$avg_rate_per_unit = 0;
		$stock_value = 0;
		foreach ($prod_new_array as $prod_id => $current_stock) {
			$product_data = explode("**", $prod_data_array[$prod_id]);
			$deterId = $product_data[0];
			$consComp = trim($product_data[1]);
			$prod_name_dtls = $product_data[2];
			$color_id = $product_data[3];
			$gsm = $product_data[4];
			$dia = $product_data[5];
			$last_purchased_qnty = $current_stock;

			if ($data_array_prod != "")
				$data_array_prod .= ",";
			$data_array_prod .= "(" . $prod_id . "," . $cbo_company_id . ",2," . $deterId . ",'" . $consComp . "','" . $prod_name_dtls . "',12," . $avg_rate_per_unit . "," . $last_purchased_qnty . "," . $current_stock . "," . $stock_value . "," . $color_id . "," . $gsm . ",'" . $dia . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		}
		//echo "10**$color_id";die;
		$rID = sql_update("inv_receive_master", $field_array, $data_array, "id", $update_id, 1);
		if ($rID)
			$flag = 1;
		else
			$flag = 0;

		if (count($prod_id_array) > 0) {
			if (str_replace("'", "", $fabric_store_auto_update) == 1) {
				$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array));
				if ($flag == 1) {
					if ($prodUpdate)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		if ($data_array_prod != "") {
			$prodInsert = sql_insert("product_details_master", $field_array_prod, $data_array_prod, 0);
			if ($flag == 1) {
				if ($prodInsert)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		if (str_replace("'", "", $fabric_store_auto_update) == 1) {
			if (count($transId_arr) > 0) {
				$transUpdate = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_update, $data_array_update_trans, $transId_arr));
				if ($flag == 1) {
					if ($transUpdate)
						$flag = 1;
					else
						$flag = 0;
				}
			}

			if ($data_array_trans != "") {
				$transInsert = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
				if ($flag == 1) {
					if ($transInsert)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}

		if (count($dtlsId_arr) > 0) {
			//echo bulk_update_sql_statement("pro_finish_fabric_rcv_dtls", "id", $field_array_dtls_update, $data_array_update_dtls, $dtlsId_arr );die;
			$rID2 = execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls", "id", $field_array_dtls_update, $data_array_update_dtls, $dtlsId_arr));
			if ($flag == 1) {
				if ($rID2)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($data_array_dtls != "") {
			//echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID3 = sql_insert("pro_finish_fabric_rcv_dtls", $field_array_dtls, $data_array_dtls, 1);
			if ($flag == 1) {
				if ($rID3)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($txt_deleted_id != "") {
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$statusChangeTrans=true;
			if($update_trans_id!="")
			{
				$statusChangeTrans = sql_multirow_update("inv_transaction", $field_array_status, $data_array_status, "id", $update_trans_id, 0);
			}
			$statusChangeDtls = sql_multirow_update("pro_finish_fabric_rcv_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);

			if ($flag == 1) {
				if ($statusChangeTrans && $statusChangeDtls)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		//echo "10**$flag";die;
		//echo "10**delete from order_wise_pro_details where dtls_id in(" . substr($update_dtls_id, 0, -1) . ") and entry_form=66";die;

		$delete_roll = execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=66", 0);
		//$deletBatch_dtls = execute_query("delete from pro_batch_create_dtls where mst_id=$batchtbl_id", 0);
		//echo "6**delete from pro_roll_details where mst_id=$batchtbl_id and entry_form=66";die;
		$deletBatch_roll = execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=66", 0);
		$delete_prop = execute_query("delete from order_wise_pro_details where dtls_id in(" . substr($update_dtls_id, 0, -1) . ") and entry_form=66", 0);

		if ($flag == 1) {
			//echo "10**"."$delete_roll && $deletBatch_dtls && $deletBatch_roll && $delete_prop";die;
			if ($delete_roll && $deletBatch_roll && $delete_prop)
				$flag = 1;
			else
				$flag = 0;
		}

		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		if ($flag == 1) {
			if ($rID4)
				$flag = 1;
			else
				$flag = 0;
		}

		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop != "")
		{
			$rID5 = sql_insert("order_wise_pro_details", $field_array_proportionate, $data_array_prop, 0);
			if ($flag == 1) {
				if ($rID5)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		/*if ($data_array_batch_dtls != "") {
			if ($prev_new_batch_id == "" && $new_batch_id == "") {
				//$data_array_batch = "(" . $batchtbl_id . ",'" . $new_batch_no . "',66," . $txt_recv_date . "," . $cbo_company_id . ",'" . $color_id_batch . "','" . $batch_weight . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$rID6 = sql_insert("pro_batch_create_mst", $field_array_batch, $data_array_batch, 1);
			} else {
				//$data_array_batch = "'" . $new_batch_no . "'*" . $txt_recv_date . "*'" . $color_id_batch . "'*'" . $batch_weight . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
				$rID6 = sql_update("pro_batch_create_mst", $field_array_batch, $data_array_batch, "id", $batchtbl_id, 1);
			}
			if ($flag == 1) {
				if ($rID6)
					$flag = 1;
				else
					$flag = 0;
			}

			$rID7 = sql_insert("pro_batch_create_dtls", $field_array_batch_dtls, $data_array_batch_dtls, 1);
			if ($flag == 1) {
				if ($rID7)
					$flag = 1;
				else
					$flag = 0;
			}

			//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll_for_batch;die;
			$rID8 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll_for_batch, 1);
			if ($flag == 1) {
				if ($rID8)
					$flag = 1;
				else
					$flag = 0;
			}
		}*/

		//echo "10**$flag==$rID==$rID2==$rID3==$rID4==$rID5";die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_recv_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $update_id) . "**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_recv_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $update_id) . "**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "mrr_popup") {
	echo load_html_head_contents("Receive Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

    <script>

        function js_set_value(id) {
            $('#hidden_system_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:760px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:760px; margin-left:2px">
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Receive Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Receive No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                               class="formbutton"/>
                        <input type="hidden" name="hidden_system_id" id="hidden_system_id">
                    </th>
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                                   style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                                   readonly>
                        </td>
                        <td align="center">
							<?
							$search_by_arr = array(1 => "Receive No", 2 => "Challan No", 3 => "Barcode No", 4 => "Batch No");
							$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/>
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view(document.getElementById('txt_search_common').value + '_' + document.getElementById('cbo_search_by').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' +<? echo $cbo_company_id; ?>+'_'+ document.getElementById('cbo_year_selection').value, 'create_challan_search_list_view', 'search_div', 'finish_fab_prod_roll_wise_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40"
                            valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}





if ($action == "create_challan_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0])."%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$year_id=$data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and a.recv_number like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and a.challan_no like '$search_string'";
		else if ($search_by == 3)
			$search_field_cond = "and b.barcode_no like '$search_string'";
		else if ($search_by == 4)
			$search_field_cond = "and d.batch_no like '$search_string'";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year,";
	} else
		$year_field = ""; //defined Later


	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and YEAR(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}

	//$sql = "select id, $year_field recv_number_prefix_num, recv_number, knitting_source, knitting_company, receive_date, store_id, challan_no from inv_receive_master where entry_form=66 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond $year_cond order by id";

	/*	$sql = "select a.id,  $year_field a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no
	from inv_receive_master a,pro_roll_details b
	where a.id=b.mst_id and a.entry_form=66 and b.entry_form=66  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond
	group by  a.id,a.insert_date, a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no
	order by a.id";*/

	$sql = "SELECT a.id,  $year_field a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no, d.batch_no
	from inv_receive_master a,pro_roll_details b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst d 
	where a.id=b.mst_id and a.id=c.mst_id and c.batch_id=d.id and a.entry_form=66 and b.entry_form=66  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond
	group by  a.id,a.insert_date, a.recv_number_prefix_num, a.recv_number, a.knitting_source, a.knitting_company, a.receive_date, a.store_id, a.challan_no, d.batch_no
	order by a.id";

	//$barcode_nos = return_field_value("group_concat(barcode_no order by id desc) as barcode_nos", "pro_roll_details", "entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");

	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
        <thead>
        <th width="40">SL</th>
        <th width="70">Receive No</th>
        <th width="70">Batch No</th>
        <th width="60">Year</th>
        <th width="140">Challan No</th>
        <th width="120">Service Source</th>
        <th width="140">Service Company</th>
        <th>Receive date</th>
        </thead>
    </table>
    <div style="width:810px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table"
               id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$dye_comp = "&nbsp;";
				if ($row[csf('knitting_source')] == 1)
					$dye_comp = $company_arr[$row[csf('knitting_company')]];
				else
					$dye_comp = $supllier_arr[$row[csf('knitting_company')]];
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value('<? echo $row[csf('id')]; ?>');">
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('batch_no')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="140"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                 	<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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

if ($action == "populate_data_from_data") {
	$sql = "select id, company_id, recv_number, knitting_source, knitting_company, knitting_location_id, receive_date, store_id, challan_no,location_id from inv_receive_master where id=$data and entry_form=66";
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#txt_recv_no').val('" . $row[csf("recv_number")] . "');\n";
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		echo "$('#cbo_service_source').attr('disabled','true')" . ";\n";
		echo "$('#txt_recv_date').val('" . change_date_format($row[csf("receive_date")]) . "');\n";
		echo "$('#cbo_service_source').val(" . $row[csf("knitting_source")] . ");\n";
		echo "load_drop_down( 'requires/finish_fab_prod_roll_wise_entry_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(" . $row[csf("knitting_company")] . ");\n";
		echo "$('#txt_recv_challan').val('" . $row[csf("challan_no")] . "');\n";
		echo "$('#cbo_store_name').val(" . $row[csf("store_id")] . ");\n";
		echo "$('#cbo_location').val(" . $row[csf("location_id")] . ");\n";

		echo "load_drop_down( 'requires/finish_fab_prod_roll_wise_entry_controller', " . $row[csf("knitting_source")] . "+'**'+" . $row[csf("knitting_company")] . ", 'load_drop_down_knit_loc', 'knit_location_td' );\n";
		echo "$('#cbo_knit_location').val(" . $row[csf("knitting_location_id")] . ");\n";

		echo "$('#update_id').val(" . $row[csf("id")] . ");\n";
	}
	exit();
}

if ($action == "barcode_nos") {
	if ($db_type == 0) {
		$barcode_nos = return_field_value("group_concat(barcode_no order by id desc) as barcode_nos", "pro_roll_details", "entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");
	} else if ($db_type == 2) {
		$barcode_nos = return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos", "pro_roll_details", "entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");
	}
	echo $barcode_nos;
	exit();
}


if ($action == "barcode_popup") {
	echo load_html_head_contents("Barcode Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	if ($service_source > 0)
		$disable = 1;
	else
		$disable = 0;
	?>

    <script>

        var selected_id = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual_id' + str).val());

            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual_id' + str).val())
                        break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#hidden_barcode_nos').val(id);
        }

        function fnc_close() {
            parent.emailwindow.hide();
        }

        function reset_hide_field() {
            $('#hidden_barcode_nos').val('');
            selected_id = new Array();
        }

        function validate_form(search_by) {
            var cbo_service_source = document.getElementById('cbo_service_source').value;
            var barcode = document.getElementById('barcode_no').value;
            var order_no = document.getElementById('txt_search_common').value;
            if(cbo_service_source == 0){
                alert("Service Source is required");
                return;
            }
            if (search_by == 2 && order_no == "") {
                alert("Batch No is required");
                return;
            }
            if (search_by == 1 && order_no == "") {
                alert("Order no is required");
                return;
            }
            if (search_by == 3 && order_no == "") {
                alert("Sales Order no is required");
                return;
            }
            show_list_view(order_no + '_' + search_by + '_' +<? echo $company_id; ?> +'_' + barcode + '_' + cbo_service_source, 'create_barcode_search_list_view', 'search_div', 'finish_fab_prod_roll_wise_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:760px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:760px; margin-left:2px">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th class="must_entry_caption">Service Source</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Order No</th>
                    <th>Barcode No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                               class="formbutton"/>
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
                        <input type="hidden" name="hidden_is_sales" id="hidden_is_sales">
                    </th>
                    </thead>
                    <tr class="general">
                        <td>
							<? echo create_drop_down("cbo_service_source", 152, $knitting_source, "", 1, "-- Select --", $service_source, "", $disable, "1,3"); ?>
                        </td>
                        <td align="center">
							<?
							$search_by_arr = array(1 => "Order No", 2 => "Batch No", 3=>"Sales Order No");
							$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 120, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/>
                        </td>
                        <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px"
                                   class="text_boxes"/></td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="validate_form(document.getElementById('cbo_search_by').value);"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}

if ($action == "create_barcode_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$barcode_no = trim($data[3]);
	$service_source = $data[4];

	if ($service_source == 0) {
		echo "Please Select Service Source";
		die;
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) {
			$search_field_cond = "and d.po_number like '$search_string'";
		} elseif ($search_by == 2) {
			$search_field_cond = "and a.batch_no like '$search_string'";
		}elseif ($search_by == 3) {
			$search_field_cond = "and d.job_no like '$search_string'";
		}
	}

	if ($barcode_no != "") {
		$barcode_cond = "and c.barcode_no='$barcode_no'";
	}

	$job_arr=array();
	$sql_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) group by b.job_no,b.booking_no,a.buyer_id,b.po_break_down_id,c.po_number,c.shipment_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
		$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 			= $job_row[csf('buyer_id')];
		$job_arr[$job_row[csf('booking_no')]]["po_number"] 			= $job_row[csf('po_number')];

		$job_arr[$job_row[csf('po_break_down_id')]]["job_no_mst"] 	= $job_row[csf('job_no_mst')];
		$job_arr[$job_row[csf('po_break_down_id')]]["buyer_id"] 	= $job_row[csf('buyer_id')];
		$job_arr[$job_row[csf('po_break_down_id')]]["po_number"] 	= $job_row[csf('po_number')];
		$job_arr[$job_row[csf('po_break_down_id')]]["shipment_date"]= $job_row[csf('shipment_date')];
	}

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["job_no_mst"] 		= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $sales_row[csf('buyer_id')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["delivery_date"] 	= $sales_row[csf('delivery_date')];
	}

	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

	if ($service_source == 1) 
	{
		if($search_by == 3)
		{
			 $sql = "SELECT a.id, max(a.extention_no) as extention_no, a.batch_no, b.prod_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id, c.booking_without_order FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c,fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond group by  a.id, a.batch_no, b.prod_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order order by a.id desc";
		}
		else if($search_by == 2)
		{

			$sql = "SELECT a.id, a.batch_no, max(a.extention_no) as extention_no, b.prod_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id,c.booking_without_order FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond group by  a.id, a.batch_no, b.prod_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order order by a.id desc ";
		}
		else
		{
			$sql = "SELECT a.id, a.batch_no, max(a.extention_no) as extention_no, b.prod_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id,c.booking_without_order FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond  group by  a.id, a.batch_no, b.prod_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order order by a.id desc";
		}
		//echo $sql;
		$result = sql_select($sql);
		if(empty($result))
		{
			echo "Data Not Found";
			die;
		}
		foreach ($result as $row)
		{
			$all_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		}

		if(count($all_barcode_arr)>0)
		{
			$all_barcode_nos=implode(",",$all_barcode_arr);
			$all_barcode_cond=""; $barcodeCond="";
			if($db_type==2 && count($all_barcode_arr)>999)
			{
				$all_barcode_chunk=array_chunk($all_barcode_arr,999) ;
				foreach($all_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barcodeCond.="  e.barcode_no in($chunk_arr_value) or ";
				}

				$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
				//echo $booking_id_cond;die;
			}
			else
			{
				$all_barcode_cond=" and e.barcode_no in($all_barcode_nos)";
			}

		}

		$scanned_barcode_arr = return_library_array("select e.barcode_no from pro_roll_details e where e.entry_form=66 and e.status_active=1 and e.is_deleted=0 $all_barcode_cond", 'barcode_no', 'barcode_no');

		$shade_matched_sql =  sql_select("select a.batch_id,a.batch_ext_no, e.barcode_no, e.batch_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls e where a.id = e.mst_id $all_barcode_cond and a.load_unload_id=2 and a.result=1 and a.status_active = 1 and a.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0");

		foreach ($shade_matched_sql as  $val) 
		{
			$shade_matched_arr[$val[csf("batch_id")]][$val[csf("batch_ext_no")]][$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
            <thead>
            <th width="40">SL</th>
            <th width="80">Batch No</th>
            <th width="50">Ext.No</th>

            <th width="150">Fabric Description</th>
            <th width="80">Job No</th>
            <th width="100">Order/FSO No</th>
            <th width="80">Shipment Date</th>
            <th width="80">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
            </thead>
        </table>
        <div style="width:800px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table"
                   id="tbl_list_search">
				<?
				$i = 1;
				foreach ($result as $row)
				{
					if($scanned_barcode_arr[$row[csf('barcode_no')]] == "" && $shade_matched_arr[$row[csf('id')]][$row[csf('extention_no')]][$row[csf('barcode_no')]] ==$row[csf('barcode_no')])
					{
						$is_sales = $row[csf('is_sales')];
						if($search_by == 3){
							$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
		            		if($within_group == 1){
		            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
		            			$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];
		            		}else{
		            			$job_no 			= "";
		            		}
		            		$shipment 			= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
		            		$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            }else{
			            	if($is_sales == 1){
			            		$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
			            		if($within_group == 1){
			            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
			            			$job_no 	= $job_arr[$sales_booking_no]["job_no_mst"];
			            		}else{
			            			$job_no 	= "";
			            		}
			            		$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            		$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);
			            	}else{
			            		if($row[csf('booking_without_order')] != 1){
			            			$job_no 	= $job_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            			$order_no 	= $job_arr[$row[csf('po_breakdown_id')]]["po_number"];
			            			$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);
			            		}

			            	}
			            }
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                            id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
                            <td width="40">
								<? echo $i; ?>
                                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>"
                                       value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="80"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
                            <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="80"><p><? echo $job_no; ?></p></td>
                            <td width="100"><p><? echo $order_no; ?></p></td>
                            <td width="80"
                                align="center"><? echo $shipment; ?></td>
                            <td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                            <td width="60">&nbsp;&nbsp;<? echo $row[csf('roll_no')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
                        </tr>
						<?
						$i++;
					}
				}
				?>
            </table>
        </div>
		<?
	} 
	else 
	{
		if($search_by == 3){
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=63 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)
			union all
			select b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)";
		}else{
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=63 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)
			union all
			select b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)";
		}
		//echo $sql;//die;
		$result = sql_select($sql);

		foreach ($result as $row)
		{
			$all_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		}

		if(count($all_barcode_arr)>0)
		{
			$all_barcode_nos=implode(",",$all_barcode_arr);
			$all_barcode_cond=""; $barcodeCond="";
			if($db_type==2 && count($all_barcode_arr)>999)
			{
				$all_barcode_chunk=array_chunk($all_barcode_arr,999) ;
				foreach($all_barcode_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barcodeCond.="  e.barcode_no in($chunk_arr_value) or ";
				}

				$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
				//echo $booking_id_cond;die;
			}
			else
			{
				$all_barcode_cond=" and e.barcode_no in($all_barcode_nos)";
			}

		}

		$scanned_barcode_arr = return_library_array("select e.barcode_no from pro_roll_details e where e.entry_form=66 and e.status_active=1 and e.is_deleted=0 $all_barcode_cond", 'barcode_no', 'barcode_no');

		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
            <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order/FSO No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="70">Roll No</th>
            <th>Roll Qty.</th>
            </thead>
        </table>
        <div style="width:750px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table"
                   id="tbl_list_search">
				<?
				$i = 1;
				$print_barcode_array = array();
				foreach ($result as $row)
				{
					if($scanned_barcode_arr[$row[csf("barcode_no")]] == "")
					{
						$is_sales = $row[csf('is_sales')];
						if($search_by == 3){
							$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
		            		if($within_group == 1){
		            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
		            			$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];
		            		}else{
		            			$job_no 			= "";
		            		}
		            		$shipment 			= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
		            		$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            }else{
			            	if($is_sales == 1){
			            		$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
			            		if($within_group == 1){
			            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
			            			$job_no 	= $job_arr[$sales_booking_no]["job_no_mst"];
			            		}else{
			            			$job_no 	= "";
			            		}
			            		$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            		$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);
			            	}else{
			            		$job_no 	= $job_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            		$order_no 	= $job_arr[$row[csf('po_breakdown_id')]]["po_number"];
			            		$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);
			            	}
			            }
						if (!in_array($row[csf('barcode_no')], $print_barcode_array)) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
	                            id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
	                            <td width="40">
									<? echo $i; ?>
	                                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>"
	                                       value="<?php echo $row[csf('barcode_no')]; ?>"/>
	                            </td>
	                            <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
	                            <td width="100"><p><? echo $job_no; ?></p></td>
	                            <td width="110"><p><? echo $order_no; ?></p></td>
	                            <td width="80"
	                                align="center"><? echo $shipment; ?></td>
	                            <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
	                            <td width="70">&nbsp;&nbsp;<? echo $row[csf('roll_no')]; ?></td>
	                            <td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
	                        </tr>
							<?
							$print_barcode_array[] = $row[csf('barcode_no')];
							$i++;
						}
					}
				}
				?>
            </table>
        </div>
		<?
	}
	?>
    <table width="720">
        <tr>
        	<td align="left">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/><strong> Check/Uncheck all</strong>
			</td>

            <td align="center">
                <input type="hidden" name="service_source" id="service_source" value="<?php echo $service_source; ?>"/>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close"
                       onClick="fnc_close();" style="width:100px"/>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action == "fabric_store_auto_update") 
{
	$variable_excess = return_field_value("distribute_qnty", "variable_settings_production", "company_name='$data' and variable_list=51 and item_category_id=2 and is_deleted=0 and status_active=1");

	$fabric_control_val = return_field_value("auto_update", "variable_settings_production", "company_name='$data' and variable_list=51 and item_category_id=2 and is_deleted=0 and status_active=1");

	$fabric_store_auto_update = return_field_value("auto_update", "variable_settings_production", "company_name='$data' and variable_list=15 and item_category_id=2 and is_deleted=0 and status_active=1");
	//echo "5".$fabric_store_auto_update;die;

	if ($fabric_store_auto_update == 1)
	{
		$fabric_store_auto_update = 1;
	}
	else
	{
		$fabric_store_auto_update = 0;
	}

	/*if ($fabric_store_auto_update == 2)
		$fabric_store_auto_update = 0;
	else
		$fabric_store_auto_update = 1;*/
	echo "document.getElementById('fabric_store_auto_update').value 	= '" . $fabric_store_auto_update . "';\n";
	echo "document.getElementById('fabric_control_val').value 	= '" . $fabric_control_val . "';\n";
	echo "document.getElementById('variable_excess').value 	= '" . $variable_excess . "';\n";

	exit();
}
//pupup color action
if ($action == "color_popup") {
	echo load_html_head_contents("Color Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $cbo_company_id;
	//echo $ord_id;
	?>

    <script>

        function js_set_value(color_name) {
            $('#hidden_color_name').val(color_name);
            //return;
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:260px;">

        <fieldset style="width:260px; margin-left:2px">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="250" class="rpt_table">
                <input type="hidden" id="hidden_color_name">
                <thead>
                <th>Sl</th>
                <th>Color</th>
                </thead>


                <tbody>
				<?
				$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
				$sql_color = "select color_number_id from wo_po_color_size_breakdown where po_break_down_id='$ord_id' group by color_number_id";
				$result_color = sql_select($sql_color);
				$sl = 0;
				foreach ($result_color as $color) {
					$sl++;
					?>

                    <tr onClick="js_set_value('<? echo $color_arr[$color[csf('color_number_id')]]; ?>');">
                        <td><? echo $sl; ?></td>
                        <td><? echo $color_arr[$color[csf('color_number_id')]]; ?></td>
                    </tr>
					<?
				}
				?>
                </tbody>
            </table>
        </fieldset>

    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}


?>
