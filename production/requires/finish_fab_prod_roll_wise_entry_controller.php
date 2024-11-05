<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");
$permission=$_SESSION['page_permission'];
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


	$sql = "SELECT a.company_id,b.prod_id,b.body_part_id,b.item_description as febric_description_id, b.gsm, b.width_dia_type as width, b.barcode_no,b.roll_id,b.roll_no,b.po_id as po_breakdown_id,b.batch_qnty as qnty,b.is_sales, b.roll_id as roll_origin_id,a.booking_without_order,a.booking_no,a.booking_no_id, 1 as type FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and b.barcode_no in($data)";

	/*
	$sql = "SELECT a.company_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales, c.roll_id as roll_origin_id,c.booking_without_order,c.booking_no, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9  and c.entry_form in(64)  and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)";*/


	//echo $sql;
	$data_array = sql_select($sql);
	$poIDs="";$salesIDs="";$prodIDs="";$all_barcode_arr=array();
	foreach ($data_array as $row) {
		if($row[csf('is_sales')] == 1){
			$salesIDs.=$row[csf('po_breakdown_id')].',';
		}else{
			$poIDs.=$row[csf('po_breakdown_id')].',';
		}
		$prodIDs.=$row[csf('prod_id')].',';

		$all_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

	// --------------------------------------------------------Start
	$roll_recv_by_batch_sql="SELECT d.barcode_no, d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size 
	from pro_roll_details d where d.status_active = 1 and d.ENTRY_FORM= 62 and d.barcode_no in(".implode(',',$all_barcode_arr).")";
	// echo $roll_recv_by_batch_sql;die;
	$roll_recv_by_batch_sql_data = sql_select($roll_recv_by_batch_sql);
	foreach ($roll_recv_by_batch_sql_data as $key => $row)
	{
		$qc_pass_qnty_pcs_data_array[$row[csf('barcode_no')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
	}
	unset($roll_recv_by_batch_sql_data);

	$split_sql="SELECT c.barcode_no as mother_barcode, d.barcode_no , d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size from pro_roll_split c , pro_roll_details d, pro_batch_create_dtls e
	where c.entry_form = 75 and  c.split_from_id = d.roll_split_from and D.BARCODE_NO=E.BARCODE_NO and e.status_active = 1  and c.status_active = 1 and d.status_active = 1 and c.barcode_no in(".implode(',',$all_barcode_arr).")";
	$split_sql_data = sql_select($split_sql);
	foreach ($split_sql_data as $key => $row)
	{
		// $split_data_array[$row[csf('mother_barcode')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
		//$qc_pass_qnty_pcs_data_array[$row[csf('barcode_no')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
		$all_barcode_arr[$row[csf('mother_barcode')]] = $row[csf('mother_barcode')];
		$mother_barcode_array[$row[csf('barcode_no')]]['mom'] = $row[csf('mother_barcode')];
	}
	unset($split_sql_data);

	// create batch using child barcode but mother barcode not in this batch
	$split_sql="SELECT e.barcode_no as mother_barcode, d.barcode_no,d.qnty, d.qc_pass_qnty_pcs, d.coller_cuff_size 
	from pro_roll_details d, pro_roll_details e where d.roll_split_from=e.ID and d.status_active = 1 and e.status_active = 1 and d.ENTRY_FORM= 62 and e.ENTRY_FORM= 62 and d.barcode_no in(".implode(',',$all_barcode_arr).")";
	$split_sql_data = sql_select($split_sql);
	foreach ($split_sql_data as $key => $row)
	{
		// $split_data_array[$row[csf('mother_barcode')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
		//$qc_pass_qnty_pcs_data_array[$row[csf('barcode_no')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
		$all_barcode_arr[$row[csf('mother_barcode')]] = $row[csf('mother_barcode')];
		$mother_barcode_array[$row[csf('barcode_no')]]['mom'] = $row[csf('mother_barcode')];
	}
	unset($split_sql_data);

	$production_dtls = "SELECT c.barcode_no, c.qc_pass_qnty_pcs, c.coller_cuff_size , e.receive_basis, e.booking_id, e.knitting_source
	from pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e,product_details_master p 
	where c.dtls_id=d.id and d.mst_id=e.id and d.prod_id = p.id and e.entry_form in(2,22) and c.entry_form in(2,22) and c.barcode_no in(".implode(',',$all_barcode_arr).")";

	$production_data = sql_select($production_dtls);
	foreach ($production_data as $row)
	{
		// $production_info[$row[csf("barcode_no")]]['qc_pass_qnty_pcs']=$row[csf("qc_pass_qnty_pcs")];
		//$qc_pass_qnty_pcs_data_array[$row[csf('barcode_no')]]["qc_pass_qnty_pcs"] = $row[csf('qc_pass_qnty_pcs')];
		$production_info[$row[csf("barcode_no")]]['coller_cuff_size']=$row[csf("coller_cuff_size")];
	}
	unset($production_data);

	// echo "<pre>";print_r($qc_pass_qnty_pcs_data_array);die;
	// ---------------------------------------------------------End

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
 	$prod_determination=sql_select("SELECT a.id, a.detarmination_id,b.barcode_no, a.gsm,a.dia_width from product_details_master a,pro_batch_create_dtls b where a.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and a.id in($prodIDs_all) and b.barcode_no in($data) group by a.id, a.detarmination_id,b.barcode_no, a.gsm,a.dia_width");
 	foreach($prod_determination as $row){
 		$prod_arr[$row[csf("barcode_no")]]['deter_d']=$row[csf("detarmination_id")];
 		$prod_arr[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
 		$prod_arr[$row[csf("barcode_no")]]['dia']=$row[csf("dia_width")];
 	}
	$po_arr = array();
	$po_sql = sql_select("SELECT a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond");

	//echo "select a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond";

	foreach($po_sql as $po_row){
		$po_arr[$po_row[csf('id')]]['po_number'] = $po_row[csf('po_number')];
		$po_arr[$po_row[csf('id')]]['job_number'] = $po_row[csf('job_no_mst')];
		$po_arr[$po_row[csf('id')]]['style_ref_no'] = $po_row[csf('style_ref_no')];
	}

	$sales_arr=array();
	//$sql_sales=sql_select("select id,job_no,style_ref_no,po_job_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 $sales_po_id_cond");
	$sql_sales=sql_select("SELECT a.id,a.job_no,a.style_ref_no,a.po_job_no,b.determination_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $sales_po_id_cond group by a.id,a.job_no,a.style_ref_no,a.po_job_no,b.determination_id");

	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["po_number"] 			= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 		= $sales_row[csf('style_ref_no')];
		$sales_arr[$sales_row[csf('id')]]["job_number"] 		= $sales_row[csf('po_job_no')];
		$sales_arr[$sales_row[csf('id')]]["deter_d"] 			= $sales_row[csf('determination_id')];
	}

	// inserted data in this page-------------------------------------------------
	$scanned_barcode_data = sql_select("SELECT a.gsm, a.width, a.color_id,a.production_qty, a.receive_qnty, a.reject_qty, a.dia_width_type, a.machine_no_id, a.shift_name, a.rack_no, a.shelf_no, a.original_gsm, a.original_width, a.batch_status, b.id, b.barcode_no, b.dtls_id, b.booking_without_order, b.booking_no, b.qc_pass_qnty_pcs, b.coller_cuff_size from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in($data)");

	foreach ($scanned_barcode_data as $row) {
		$scanned_barcode_array[] = $row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]] = $row[csf('dtls_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]] = $row[csf('id')];
		$dtls_data_arr[$row[csf('barcode_no')]] = $row[csf('gsm')] . "**" . $row[csf('width')] . "**" . $color_arr[$row[csf('color_id')]] . "**" . number_format($row[csf('receive_qnty')],2,'.','') . "**" . number_format($row[csf('reject_qty')],2,'.','') . "**" . $row[csf('dia_width_type')] . "**" . $row[csf('machine_no_id')] . "**" . $row[csf('shift_name')] . "**" . $row[csf('rack_no')] . "**" . $row[csf('shelf_no')] . "**" . $row[csf('production_qty')] . "**". $row[csf('booking_without_order')] ."**". $row[csf('booking_no')] ."**". $row[csf('original_gsm')]."**". $row[csf('original_width')]."**". $row[csf('batch_status')]."**". $row[csf('qc_pass_qnty_pcs')]."**". $row[csf('coller_cuff_size')];
	}

	$jsscanned_barcode_array = json_encode($scanned_barcode_array);
	$jsbarcode_dtlsId_array = json_encode($barcode_dtlsId_array);
	$jsbarcode_rollTableId_array = json_encode($barcode_rollTableId_array);
	$jsdtls_data_arr = json_encode($dtls_data_arr);
	// ---------------------------------------------------------------------------

	// echo "<pre>";print_r($qc_pass_qnty_pcs_data_array);die;
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
		$roll_details_array[$b_code]['deter_d'] 		= $prod_arr[$row[csf("barcode_no")]]['deter_d'];
		$roll_details_array[$b_code]['gsm'] 			= $prod_arr[$row[csf("barcode_no")]]['gsm'];
		$roll_details_array[$b_code]['width'] 			= $prod_arr[$row[csf("barcode_no")]]['dia'];
		//$roll_details_array[$b_code]['deter_d'] 		= $row[csf("febric_description_id")];
		//$roll_details_array[$b_code]['gsm'] 			= trim($febric_des_data[2]);
		//$roll_details_array[$b_code]['width'] 		= trim($febric_des_data[3]);
		$roll_details_array[$b_code]['is_sales'] 		= $row[csf("is_sales")];
		$roll_details_array[$b_code]['roll_id'] 		= $row[csf("roll_origin_id")];
		$roll_details_array[$b_code]['roll_no'] 		= $row[csf("roll_no")];

		if ($row[csf("is_sales")] == 1) {
			if($row[csf("booking_without_order")]==0)
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("po_breakdown_id")]; 
			}
			else
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("booking_no_id")];
			}
			
			$roll_details_array[$b_code]['po_number'] 	 		= $sales_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['job_number'] 			= $sales_arr[$row[csf("po_breakdown_id")]]['job_number'];
			$roll_details_array[$b_code]['style_ref_no'] 		= $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
		}
		else
		{
			if($row[csf("booking_without_order")]==0)
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
			}
			else
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("booking_no_id")];
			}
			$roll_details_array[$b_code]['po_number'] 			= $po_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['job_number'] 			= $po_arr[$row[csf("po_breakdown_id")]]['job_number'];
			$roll_details_array[$b_code]['style_ref_no']		= $po_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];

		}

		$roll_details_array[$b_code]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');
		$roll_details_array[$b_code]['booking_without_order'] = $row[csf("booking_without_order")];
		$roll_details_array[$b_code]['booking_no'] = $row[csf("booking_no")];
		$barcode_array[$b_code] = $b_code;

		if($mother_barcode_array[$b_code]['mom']!="")
		{
			$barcode_number= $mother_barcode_array[$b_code]['mom'];
		}
		else
		{
			$barcode_number= $b_code;
		}

		$roll_details_array[$b_code]['qnty_pcs'] = $qc_pass_qnty_pcs_data_array[$b_code]['qc_pass_qnty_pcs'];
		$roll_details_array[$b_code]['size'] = $production_info[$barcode_number]['coller_cuff_size'];
	}
	//echo "<pre>";print_r($roll_details_array); //echo "<br>"; echo "<pre>";print_r($mother_barcode_array);
	//die;

	$jsroll_details_array = json_encode($roll_details_array);
	$jsbarcode_array = json_encode($barcode_array);

	$batch_dtls_arr = array();
	$batch_barcode_arr = array();

	$shade_matched_sql =  sql_select("SELECT a.batch_id,a.batch_ext_no, e.barcode_no, e.batch_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls e where a.id = e.mst_id and e.barcode_no in($data) and a.load_unload_id=2 and a.result=1 and a.status_active = 1 and a.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0");

	foreach ($shade_matched_sql as  $val) 
	{
		$shade_matched_arr[$val[csf("batch_id")]][$val[csf("batch_ext_no")]][$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	//$chck_aop = return_library_array("select barcode_no from pro_roll_details where entry_form in(65) and status_active=1 and is_deleted=0 and barcode_no in($data)", "barcode_no", "barcode_no");
	
	$all_barcode_without_aop= explode(",", $data);
	$chck_aop_sql = sql_select("select barcode_no from pro_roll_details where entry_form in(65) and status_active=1 and is_deleted=0 and barcode_no in($data)");
	foreach ($chck_aop_sql as $val) 
	{
		$chck_aop[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	if(!empty($chck_aop))
	{
		$all_barcode_without_aop=array_diff($all_barcode_without_aop, $chck_aop);
	}

	if ($chck_aop) 
	{
		$sql = "SELECT d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty as batch_qnty from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c,pro_batch_create_mst d,pro_batch_create_dtls e where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and d.id=e.mst_id and  c.entry_form in(65) and a.entry_form in(65) and c.barcode_no in(". implode(",",$chck_aop).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty ";

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
	}

	if($all_barcode_without_aop)
	{
		$sql = "SELECT a.id, a.extention_no, a.entry_form, a.batch_no, a.color_id, b.barcode_no, b.width_dia_type, b.batch_qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and b.barcode_no in(".implode(",",$all_barcode_without_aop).")";

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
	}


	$jsbatch_dtls_arr = json_encode($batch_dtls_arr);
	$jsbatch_barcode_arr = json_encode($batch_barcode_arr);

	$grey_iss_barcode_arr = return_library_array("SELECT barcode_no from pro_roll_details where entry_form in(61,63) and status_active=1 and is_deleted=0 and barcode_no in($data)", "barcode_no", "barcode_no");
	$jsgrey_iss_barcode_arr = json_encode($grey_iss_barcode_arr);


	//Compacting and Stantering check here
	
	$compacting_arr = array();
	$compacting_details_arr = array();
	//$sql_compact = sql_select("select a.barcode_no,b.production_qty from pro_roll_details a,pro_fab_subprocess_dtls b where b.roll_id=a.id and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page=33 and a.barcode_no in($data)");
	$sql_compact = sql_select("SELECT a.barcode_no,b.production_qty from pro_roll_details a,pro_fab_subprocess_dtls b where b.barcode_no=a.barcode_no and a.entry_form=64 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.entry_page in (48,33) and a.barcode_no in($data) order by b.entry_page desc");

	/*
	|
	|	N.B Here First priority is last process compacting(33) then last process stantering(48), so Order by entry_page DESC here
	|
	*/

	foreach ($sql_compact as $c_id) {
		$compacting_arr[] = $c_id[csf('barcode_no')];
		$compacting_details_arr[$c_id[csf('barcode_no')]]['prod_qty'] = $c_id[csf('production_qty')];
	}
	$jscompacting_arr = json_encode($compacting_arr);
	$jscompacting_details_arr = json_encode($compacting_details_arr);

	$sql_delivery = sql_select("SELECT barcode_no from  pro_roll_details where entry_form=67 and  status_active=1 and is_deleted=0 and barcode_no in($data)");
	$roll_delivery_arr = array();
	foreach ($sql_delivery as $inf) {
		$roll_delivery_arr[] = $inf[csf('barcode_no')];
	}
	$roll_delivery_arr = json_encode($roll_delivery_arr);

	//new dev
	$sqlStyle = sql_select("SELECT a.id,b.style_ref_no from wo_po_break_down a, wo_po_details_master b,pro_roll_details c where a.job_no_mst=b.job_no and a.id=c.po_breakdown_id and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");

	$styleArr = array();
	foreach ($sqlStyle as $rowStyle) {
		$styleArr[] = $rowStyle[csf('style_ref_no')];
	}
	$styleArr = json_encode($styleArr);


	echo $all_json_data = $jsscanned_barcode_array . "__" . $jsbarcode_dtlsId_array . "__" . $jsbarcode_rollTableId_array . "__" . $jsdtls_data_arr . "__" . $jsroll_details_array . "__" . $jsbarcode_array . "__" . $jsbatch_dtls_arr . "__" . $jsbatch_barcode_arr . "__" . $jsgrey_iss_barcode_arr . "__" . $jscompacting_arr . "__" . $jscompacting_details_arr . "__" . $roll_delivery_arr . "__" . $styleArr;
	die;

}

if ($action == "json_barcode_data_outbound") {
	$scanned_barcode_array = array();
	$barcode_dtlsId_array = array();
	$barcode_rollTableId_array = array();
	$dtls_data_arr = array();
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	/* $sql = "SELECT d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty as batch_qnty, e.prod_id,e.body_part_id,e.item_description as febric_description_id, e.gsm, e.width_dia_type as width, e.barcode_no, e.roll_id, e.roll_no, e.po_id as po_breakdown_id, e.batch_qnty as qnty,e.is_sales, e.roll_id as roll_origin_id, d.company_id, d.booking_without_order, d.booking_no, d.booking_no_id, 1 as type from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c,pro_batch_create_mst d,pro_batch_create_dtls e where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and d.id=e.mst_id and  c.entry_form in(65) and a.entry_form in(65) and c.barcode_no in(".$data.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty,e.prod_id, e.body_part_id, e.item_description, e.gsm, e.width_dia_type, e.barcode_no, e.roll_id, e.roll_no, e.po_id, e.batch_qnty, e.is_sales, d.company_id, d.booking_without_order, d.booking_no, d.booking_no_id"; */

	/* $sql = "SELECT x.id, x.extention_no, x.entry_form, x.batch_no, x.color_id, x.barcode_no, x.width_dia_type, x.batch_qnty, x.prod_id,x.body_part_id,x.febric_description_id, x.gsm, x.width, x.roll_id, x.roll_no, x.po_breakdown_id, x.qnty,x.is_sales, x.roll_origin_id, x.company_id, x.booking_without_order, x.booking_no, x.booking_no_id, 1 as type 
	FROM (
	SELECT c.id as roll_table_id, d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty as batch_qnty, e.prod_id,b.body_part_id,e.item_description as febric_description_id, e.gsm, e.width_dia_type as width, e.roll_id, e.roll_no, e.po_id as po_breakdown_id, e.batch_qnty as qnty,e.is_sales, e.roll_id as roll_origin_id, d.company_id, d.booking_without_order, d.booking_no, d.booking_no_id, 1 as type, max(c.id) over (partition by c.barcode_no) max_rcv_roll_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c,pro_batch_create_mst d,pro_batch_create_dtls e where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and d.id=e.mst_id and b.id=e.dtls_id and  c.entry_form in(65) and a.entry_form in(65) and c.barcode_no in(".$data.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 GROUP BY c.id, d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty,e.prod_id, b.body_part_id, e.item_description, e.gsm, e.width_dia_type, e.roll_id, e.roll_no, e.po_id, e.batch_qnty, e.is_sales, d.company_id, d.booking_without_order, d.booking_no, d.booking_no_id
	) x
	WHERE x.roll_table_id= x.max_rcv_roll_id"; */



	$sql = "SELECT x.id, x.extention_no, x.entry_form, x.batch_no, x.color_id, x.barcode_no, x.qnty, x.batch_qnty, x.prod_id,x.body_part_id,x.febric_description_id, x.gsm, x.width, x.roll_id, x.roll_no, x.po_breakdown_id, x.is_sales, x.roll_origin_id, x.company_id, x.booking_without_order, x.booking_no, x.booking_no_id, 1 as type 
	FROM (SELECT c.id as roll_table_id, d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, c.qnty, c.qnty as batch_qnty, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.roll_id, c.roll_no, c.po_breakdown_id, c.is_sales, c.roll_id as roll_origin_id, d.company_id, d.booking_without_order, d.booking_no, d.booking_no_id, 1 as type, max(c.id) over (partition by c.barcode_no) max_rcv_roll_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c,pro_batch_create_mst d where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and c.entry_form in(65) and a.entry_form in(65) and c.barcode_no in(".$data.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 GROUP BY c.id, d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, c.qnty,b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, c.roll_id, c.roll_no, c.po_breakdown_id, c.is_sales, d.company_id, d.booking_without_order, d.booking_no, d.booking_no_id) x
 	WHERE x.roll_table_id= x.max_rcv_roll_id";

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
 	$prod_determination=sql_select("select a.id, a.detarmination_id,b.barcode_no, a.gsm,a.dia_width from product_details_master a,pro_batch_create_dtls b where a.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and a.id in($prodIDs_all) and b.barcode_no in($data) group by a.id, a.detarmination_id,b.barcode_no, a.gsm,a.dia_width");
 	foreach($prod_determination as $row){
 		$prod_arr[$row[csf("barcode_no")]]['deter_d']=$row[csf("detarmination_id")];
 		$prod_arr[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
 		$prod_arr[$row[csf("barcode_no")]]['dia']=$row[csf("dia_width")];
 	}
	$po_arr = array();
	$po_sql = sql_select("select a.id,a.po_number,b.style_ref_no,a.job_no_mst from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_id_cond");

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

	$width_dia_arr = return_library_array("SELECT a.barcode_no, c.width_dia_type from pro_roll_details a, inv_receive_master b, ppl_planning_info_entry_dtls c where b.entry_form =2 and b.receive_basis=2 and a.mst_id=b.id and a.entry_form=2 and b.booking_id=c.id and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".$data.")", "barcode_no", "width_dia_type");

	// inserted data in this page-------------------------------------------------
	$scanned_barcode_data = sql_select("SELECT a.gsm, a.width, a.color_id,a.production_qty, a.receive_qnty, a.reject_qty, a.dia_width_type, a.machine_no_id, a.shift_name, a.rack_no, a.shelf_no, a.original_gsm, a.original_width, a.batch_status, b.id, b.barcode_no, b.dtls_id, b.booking_without_order, b.booking_no, b.qc_pass_qnty_pcs, b.coller_cuff_size from pro_finish_fabric_rcv_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=66 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in($data)");

	foreach ($scanned_barcode_data as $row) {
		$scanned_barcode_array[] = $row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]] = $row[csf('dtls_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]] = $row[csf('id')];
		$dtls_data_arr[$row[csf('barcode_no')]] = $row[csf('gsm')] . "**" . $row[csf('width')] . "**" . $color_arr[$row[csf('color_id')]] . "**" . number_format($row[csf('receive_qnty')],2,'.','') . "**" . number_format($row[csf('reject_qty')],2,'.','') . "**" . $row[csf('dia_width_type')] . "**" . $row[csf('machine_no_id')] . "**" . $row[csf('shift_name')] . "**" . $row[csf('rack_no')] . "**" . $row[csf('shelf_no')] . "**" . $row[csf('production_qty')] . "**". $row[csf('booking_without_order')] ."**". $row[csf('booking_no')] ."**". $row[csf('original_gsm')]."**". $row[csf('original_width')]."**". $row[csf('batch_status')]."**". $row[csf('qc_pass_qnty_pcs')]."**". $row[csf('coller_cuff_size')];
	}

	$jsscanned_barcode_array = json_encode($scanned_barcode_array);
	$jsbarcode_dtlsId_array = json_encode($barcode_dtlsId_array);
	$jsbarcode_rollTableId_array = json_encode($barcode_rollTableId_array);
	$jsdtls_data_arr = json_encode($dtls_data_arr);
	// ---------------------------------------------------------------------------

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
		$roll_details_array[$b_code]['deter_d'] 		= $prod_arr[$row[csf("barcode_no")]]['deter_d'];
		$roll_details_array[$b_code]['gsm'] 			= $prod_arr[$row[csf("barcode_no")]]['gsm'];
		$roll_details_array[$b_code]['width'] 			= $prod_arr[$row[csf("barcode_no")]]['dia'];
		//$roll_details_array[$b_code]['deter_d'] 		= $row[csf("febric_description_id")];
		//$roll_details_array[$b_code]['gsm'] 			= trim($febric_des_data[2]);
		//$roll_details_array[$b_code]['width'] 			= trim($febric_des_data[3]);
		$roll_details_array[$b_code]['is_sales'] 		= $row[csf("is_sales")];
		$roll_details_array[$b_code]['roll_id'] 		= $row[csf("roll_origin_id")];
		$roll_details_array[$b_code]['roll_no'] 		= $row[csf("roll_no")];

		if ($row[csf("is_sales")] == 1) {
			if($row[csf("booking_without_order")]==0)
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("po_breakdown_id")]; 
			}
			else
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("booking_no_id")];
			}
			
			$roll_details_array[$b_code]['po_number'] 	 		= $sales_arr[$row[csf("po_breakdown_id")]]['po_number'];
			$roll_details_array[$b_code]['job_number'] 			= $sales_arr[$row[csf("po_breakdown_id")]]['job_number'];
			$roll_details_array[$b_code]['style_ref_no'] 		= $sales_arr[$row[csf("po_breakdown_id")]]['style_ref_no'];
		}
		else
		{
			if($row[csf("booking_without_order")]==0)
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
			}
			else
			{
				$roll_details_array[$b_code]['po_breakdown_id'] = $row[csf("booking_no_id")];
			}
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

	//$sql = "SELECT d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty as batch_qnty from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c,pro_batch_create_mst d,pro_batch_create_dtls e where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and d.id=e.mst_id and  c.entry_form in(65) and a.entry_form in(65) and c.barcode_no in(".$data.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id, d.extention_no, a.entry_form, d.batch_no, b.color_id, c.barcode_no, e.width_dia_type, c.qnty ";

	//$result = sql_select($sql);
	foreach ($data_array as $row) 
	{
		$batch_dtls_arr[$row[csf("barcode_no")]]['batch_id'] = $row[csf("id")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['color_id'] = $row[csf("color_id")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['color'] = $color_arr[$row[csf("color_id")]];
		$batch_dtls_arr[$row[csf("barcode_no")]]['entry_form'] = $row[csf("entry_form")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['width_dia_type'] = $width_dia_arr[$row[csf("barcode_no")]];//$row[csf("width_dia_type")];
		$batch_dtls_arr[$row[csf("barcode_no")]]['batch_qnty'] = $row[csf("batch_qnty")];
		$batch_barcode_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}

	$jsbatch_dtls_arr = json_encode($batch_dtls_arr);
	$jsbatch_barcode_arr = json_encode($batch_barcode_arr);

	$grey_iss_barcode_arr = array();
	/*$grey_iss_barcode_arr = return_library_array("select barcode_no from pro_roll_details where entry_form in(63) and status_active=1 and is_deleted=0 and barcode_no in($data)", "barcode_no", "barcode_no"); */
	$jsgrey_iss_barcode_arr = json_encode($grey_iss_barcode_arr);


	$compacting_arr = array();
	$compacting_details_arr = array();
	$jscompacting_arr = json_encode($compacting_arr);
	$jscompacting_details_arr = json_encode($compacting_details_arr);

	$roll_delivery_arr = array();
	$sql_delivery = sql_select("select barcode_no from  pro_roll_details where entry_form=67 and  status_active=1 and is_deleted=0 and barcode_no in($data)");
	foreach ($sql_delivery as $inf) {
		$roll_delivery_arr[] = $inf[csf('barcode_no')];
	}
	$roll_delivery_arr = json_encode($roll_delivery_arr);

	//new dev
	$styleArr = array();
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


	//echo $sql;
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

	// validation next process found start Here
	$all_production_barcode="";
	for ($k = 1; $k <= $tot_row; $k++)
	{
		$proBarcodeNo = "barcodeNo_" . $k;
		$all_production_barcode .= $$proBarcodeNo . ",";
	}

	$all_production_barcode = chop($all_production_barcode, ",");
	if($all_production_barcode!="")
	{
		$all_production_barcode_arr=array_unique(explode(",",$all_production_barcode));
		if($db_type==2 && count($all_production_barcode_arr)>999)
		{
			$barcode_cond_production=" and (";
			$all_production_barcode_arr_chunk=array_chunk($all_production_barcode_arr,999);
			foreach($all_production_barcode_arr_chunk as $barcode)
			{
				$barcodes=implode(",",$barcode);
				$barcode_cond_production.=" barcode_no in($barcodes) or ";
			}

			$barcode_cond_production=chop($barcode_cond_production,'or ');
			$barcode_cond_production.=")";
		}
		else
		{
			$barcode_cond_production=" and barcode_no in (".implode(",",$all_production_barcode_arr).")";
		}

		$delivery_sql_data=sql_select("SELECT a.sys_number from pro_grey_prod_delivery_mst a, pro_roll_details b where a.id=b.mst_id and a.entry_form in(67) and b.entry_form in(67) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $barcode_cond_production");
		foreach ($delivery_sql_data as $val)
		{
			$delivery_no[$val[csf("sys_number")]] = $val[csf("sys_number")];
		}

		if(!empty($delivery_no)){
			echo "20**Next Process Found. So Roll Can Not Update.\nDelivery no :".implode(',', $delivery_no);
			disconnect($con);
			die;
		}

		$recv_sql_data=sql_select("SELECT a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form in(317) and b.entry_form in(317) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  $barcode_cond_production");
		foreach ($recv_sql_data as $val)
		{
			$recv_no[$val[csf("recv_number")]] = $val[csf("recv_number")];
		}

		if(!empty($recv_no)){
			echo "20**Next Process Found. So Roll Can Not Update.\nReceive no :".implode(',', $recv_no);
			disconnect($con);
			die;
		}
	}
	// echo "20**next_process_barcode check";die;
	// validation next process found end Here

	if ($operation == 0) // Insert Here
	{
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
		$field_array_dtls = "id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, dia_width_type, color_id,production_qty, receive_qnty, reject_qty, order_id, machine_no_id, shift_name, batch_status, rack_no, shelf_no, roll_id, roll_no, barcode_no, original_gsm, original_width, inserted_by, insert_date";

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form,qnty, reject_qnty, qc_pass_qnty, roll_no, roll_id, inserted_by, insert_date,is_sales,booking_without_order,booking_no, qc_pass_qnty_pcs, coller_cuff_size";

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
			$cboBatchStatus = "cboBatchStatus_" . $j;
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
			$greygsm = "greygsm_" . $j;
			$greydia = "greydia_" . $j;
			$greyQntyPcs = "greyQntyPcs_" . $j;
			$collerCuffSize = "collerCuffSize_" . $j;

			$batch_id = $$batchId;
			$color_name = trim($$color);

			$$dia = strtoupper($$dia); //For avoiding case sensitivity of dia. 05/08/2023
			$$greydia = strtoupper($$greydia);

			
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
			$data_array_dtls .= "(" . $id_dtls . "," . $id . "," . $id_trans . ",'" . $prod_id . "','" . $batch_id . "','" . $$bodyPartId . "','" . $$deterId . "','" . $$gsm . "','" . $$dia . "','" . $$diaType . "','" . $color_id . "','" . $$rollWgt . "','" . $$qcPassQty . "','" . $$reJectQty . "','" . $$orderId . "','" . $$cboMachine . "','" . $$cboShift . "','". $$cboBatchStatus . "','" . $$rack . "','" . $$shelf . "','" . $$rollId . "','" . $$rollNo . "','" . $$barcodeNo . "','" .$$greygsm. "','" .$$greydia ."',". $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			if ($data_array_roll != "") $data_array_roll .= ",";
			$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $id . "," . $id_dtls . ",'" . $$orderId . "',66,'" . $$rollWgt . "','" . $$reJectQty . "','" . $$qcPassQty . "','" . $$rollNo . "','" . $$rollId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time. "','" . $$IsSalesId . "','" . $$booking_without_order . "','" . $$booking_no . "','" . $$greyQntyPcs . "','" . $$collerCuffSize . "')";
			
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
		//oci_rollback($con);
		 // echo "10**".$flag;die;
		 //echo "10**".$rID."=".$rID2."=".$rID3."=".$rID4;oci_rollback($con);die;
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
	}
	else if ($operation == 1) // Update Here
	{   
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
		$field_array_dtls = "id, mst_id, trans_id, prod_id, batch_id, body_part_id, fabric_description_id, gsm, width, dia_width_type, color_id,production_qty, receive_qnty, reject_qty, order_id, machine_no_id, shift_name, batch_status, rack_no, shelf_no, roll_id, roll_no, barcode_no, original_gsm, original_width, inserted_by, insert_date";
		$field_array_dtls_update = "prod_id*batch_id*body_part_id*fabric_description_id*gsm*width*dia_width_type*color_id*production_qty*receive_qnty*
		reject_qty*order_id*machine_no_id*shift_name*batch_status*rack_no*shelf_no*roll_id*roll_no*barcode_no*updated_by*update_date";
		//$id_dtls = return_next_id("id", "pro_finish_fabric_rcv_dtls", 1);

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, reject_qnty, qc_pass_qnty, qc_pass_qnty_pcs, coller_cuff_size, roll_no, roll_id, inserted_by, insert_date,is_sales,booking_without_order,booking_no";

		$field_array_roll_update = "qnty*qc_pass_qnty*reject_qnty*qc_pass_qnty_pcs*coller_cuff_size*updated_by*update_date";

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
			$cboBatchStatus = "cboBatchStatus_" . $j;
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
			$greygsm = "greygsm_" . $j;
			$greydia = "greydia_" . $j;

			$greyQntyPcs = "greyQntyPcs_" . $j;
			$collerCuffSize = "collerCuffSize_" . $j;


			$$dia = strtoupper($$dia); //For avoiding case sensitivity of dia. 05/08/2023
			$$greydia = strtoupper($$greydia);

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
					$data_array_trans .= "(" . $id_trans . "," . $update_id . "," . $cbo_company_id . ",'" . $prod_id . "',2,1," . $txt_recv_date . "," . $cbo_store_name . ",12,'" . $$qcPassQty . "'," . $rate . "," . $amount . ",12," . $$qcPassQty . ",'" . $$reJectQty . "'," . $rate . "," . $amount . "," . $$qcPassQty . "," . $amount . ",'" . $$cboMachine . "','" . $$rack . "','" . $$shelf . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$transId = $id_trans;
				}
			} else {
				$transId = 0;
			}

			if ($$rolltableId > 0) {
				$dtlsId_arr[] = $$dtlsId;
				$data_array_update_dtls[$$dtlsId] = explode("*", ($prod_id . "*'" . $batch_id . "'*'" . $$bodyPartId . "'*'" . $$deterId . "'*'" . $$gsm . "'*'" . $$dia . "'*'" . $$diaType . "'*'" . $color_id . "'*'" . $$rollWgt . "'*'" . $$qcPassQty . "'*'" . $$reJectQty . "'*'" . $$orderId . "'*'" . $$cboMachine . "'*'" . $$cboShift . "'*'" . $$cboBatchStatus . "'*'" . $$rack . "'*'" . $$shelf . "'*'" . $$rollId . "'*'" . $$rollNo . "'*'" . $$barcodeNo . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				$dtlsId_prop = $$dtlsId;


				$rollId_arr[] = $$rolltableId;
				$data_array_update_roll[$$rolltableId] = explode("*", ( $$rollWgt . "*'" . $$qcPassQty . "'*'" . $$reJectQty . "'*'" . $$greyQntyPcs . "'*'" . $$collerCuffSize . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				//qnty*reject_qnty*qc_pass_qnty*updated_by*update_date

			} 
			else 
			{
				if ($data_array_dtls != "")
					$data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $update_id . "," . $transId . ",'" . $prod_id . "','" . $batch_id . "','" . $$bodyPartId . "','" . $$deterId . "','" . $$gsm . "','" . $$dia . "','" . $$diaType . "','" . $color_id . "','" . $$rollWgt . "','" . $$qcPassQty . "','" . $$reJectQty . "','" . $$orderId . "','" . $$cboMachine . "','" . $$cboShift . "','". $$cboBatchStatus . "','" . $$rack . "','" . $$shelf . "','" . $$rollId . "','" . $$rollNo . "','" . $$barcodeNo . "','" .$$greygsm."','" .$greydia."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$dtlsId_prop = $id_dtls;

				
				if ($data_array_roll != "")
					$data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $update_id . "," . $dtlsId_prop . ",'" . $$orderId . "',66,'" . $$rollWgt . "','" . $$reJectQty . "','" . $$qcPassQty . "','" . $$greyQntyPcs . "','" . $$collerCuffSize . "','" . $$rollNo . "','" . $$rollId . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$IsSalesId . "','" . $$booking_without_order . "','" . $$booking_no . "')";
			}

			

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

		
		//echo "10**".$field_array."=".$data_array;oci_rollback($con);die;

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

		//echo "10**$flag";oci_rollback($con);die;
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);die;
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
			//echo "10**".bulk_update_sql_statement("pro_finish_fabric_rcv_dtls", "id", $field_array_dtls_update, $data_array_update_dtls, $dtlsId_arr );die;
			$rID2 = execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls", "id", $field_array_dtls_update, $data_array_update_dtls, $dtlsId_arr));
			if ($flag == 1) {
				if ($rID2)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if (count($rollId_arr) > 0) {
			//echo "10**".bulk_update_sql_statement("pro_roll_details", "id", $field_array_roll_update, $data_array_update_roll, $rollId_arr );oci_rollback($con);die;
			$rID2_2 = execute_query(bulk_update_sql_statement("pro_roll_details", "id", $field_array_roll_update, $data_array_update_roll, $rollId_arr));
			if ($flag == 1) {
				if ($rID2_2)
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
				$statusChangeTrans =sql_multirow_update("inv_transaction", $field_array_status, $data_array_status, "id", $update_trans_id, 0);
			}
			$statusChangeDtls = sql_multirow_update("pro_finish_fabric_rcv_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);

			$txt_deleted_id = str_replace("'","",$txt_deleted_id);
			$statusChangeRolls = execute_query("update pro_roll_details set status_active=0, is_deleted=1, update_date='".$pc_date_time."', updated_by=".$_SESSION['logic_erp']['user_id']." where entry_form=66 and mst_id=$update_id and dtls_id in (".$txt_deleted_id.")");

			if ($flag == 1) {
				if ($statusChangeTrans && $statusChangeDtls && $statusChangeRolls)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		//echo "10**$flag";oci_rollback($con);die;
		//echo "10**delete from order_wise_pro_details where dtls_id in(" . substr($update_dtls_id, 0, -1) . ") and entry_form=66";die;

		//$delete_roll = execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=66", 0);
		//$deletBatch_dtls = execute_query("delete from pro_batch_create_dtls where mst_id=$batchtbl_id", 0);
		//echo "6**delete from pro_roll_details where mst_id=$batchtbl_id and entry_form=66";die;
		//$deletBatch_roll = execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=66", 0);
		$delete_prop = execute_query("delete from order_wise_pro_details where dtls_id in(" . substr($update_dtls_id, 0, -1) . ") and entry_form=66", 0);

		if ($flag == 1) {
			//echo "10**"."$delete_roll && $deletBatch_dtls && $deletBatch_roll && $delete_prop";oci_rollback($con);die;
			if ( $delete_prop)
				$flag = 1;
			else
				$flag = 0;
		}

		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;oci_rollback($con); die;
		if($data_array_roll !="")
		{
			$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1) {
				if ($rID4)
					$flag = 1;
				else
					$flag = 0;
			}
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

		// echo "10**$flag==$rID==$rID2==$rID2_2==$rID3==$rID4==$rID5";oci_rollback($con);die;
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
	where a.id=b.mst_id and b.dtls_id=c.id and a.id=c.mst_id and c.batch_id=d.id and a.entry_form=66 and b.entry_form=66  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond
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

if ($action == "barcode_nos__bk") {
	if ($db_type == 0) {
		$barcode_nos = return_field_value("group_concat(barcode_no order by id desc) as barcode_nos", "pro_roll_details", "entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");
	} else if ($db_type == 2) {
		$barcode_nos = return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos", "pro_roll_details", "entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data", "barcode_nos");
	}
	echo $barcode_nos;
	exit();
}

if ($action == "barcode_nos") 
{
	$barcode_sql="SELECT barcode_no as barcode_nos from pro_roll_details where entry_form=66 and status_active=1 and is_deleted=0 and mst_id=$data";
	// echo $barcode_sql;die;
	$barcode_sql_rslt=sql_select($barcode_sql);
	$barcode_nos_arr=array();
	foreach ($barcode_sql_rslt as $key => $row) 
	{
		$barcode_nos_arr[$row[csf('barcode_nos')]]=$row[csf('barcode_nos')];
	}
	$barcode_nos=implode(",", $barcode_nos_arr);
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
            var txt_date_from = document.getElementById('txt_date_from').value;
            var txt_date_to = document.getElementById('txt_date_to').value;
            var cbo_search_category = document.getElementById('cbo_search_category').value;
			var cbo_year_selection = document.getElementById('cbo_year_selection').value;
            if(cbo_service_source == 0){
                alert("Service Source is required");
                return;
            }
            if(txt_date_from=="" || txt_date_to=="")
            {
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
	            if (search_by == 4 && order_no == "") {
	                alert("Fabric Booking no is required");
	                return;
	            }
            }
	           
            show_list_view(order_no + '_' + search_by + '_' +<? echo $company_id; ?> +'_' + barcode + '_' + cbo_service_source + '_' + txt_date_from + '_' + txt_date_to + '_' + cbo_search_category+ '_' + cbo_year_selection, 'create_barcode_search_list_view', 'search_div', 'finish_fab_prod_roll_wise_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:860px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:860px; margin-left:2px">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
                	<thead>
						<th  colspan="14">
							<?
							echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",4 );
							?>
						</th>
					</thead>
                    <thead>
	                    <th class="must_entry_caption">Service Source</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="150">Please Enter Order No</th>
	                    <th width="130">Barcode No</th>
	                    <th>Process End Date Range</th>
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
							$search_by_arr = array(1 => "Order No", 2 => "Batch No", 3=>"Sales Order No", 4=>"Fabric Booking No");
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
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:50px;"> To <input type="text" name="txt_date_to" id="txt_date_to"
							class="datepicker" style="width:50px;">
						</td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="validate_form(document.getElementById('cbo_search_by').value);"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40"
                            valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$txt_date_from = $data[5];
	$txt_date_to = $data[6];
	$search_category = $data[7];
	$year_selection = $data[8];
	
	
	if ($db_type == 0)
		$year_cond = " and YEAR(d.insert_date)=".$year_selection;
	else if ($db_type == 2)
		$year_cond = " and to_char(d.insert_date,'YYYY')=".$year_selection;
	else
		$year_cond = ""; //defined Later




	$batch_no_cond= "";
	if ($search_by == 2) 
	{
		$search_string = trim($data[0]);
		if($search_category==1)
		{
			if($search_string!="") $batch_no_cond =" and a.batch_no='$search_string'"; else $batch_no_cond="";
		}
		else if($search_category==0 || $search_category==4)
		{
			if($search_string!="") $batch_no_cond =" and a.batch_no like '%$search_string%'"; else $batch_no_cond="";
		}
		else if($search_category==2)
		{
			if($search_string!="") $batch_no_cond =" and a.batch_no like '$search_string%'"; else $batch_no_cond="";
		}
		else if($search_category==3)
		{
			if($search_string!="") $batch_no_cond =" and a.batch_no like '%$search_string'"; else $batch_no_cond="";
		}
	}
	// echo $batch_no_cond;die;

	$booking_cond= "";
	if ($search_by == 4) 
	{
		$search_string = trim($data[0]);
		if($search_category==1)
		{
			if($search_string!="") $booking_cond =" and a.booking_no='$search_string'"; else $booking_cond="";
		}
		else if($search_category==0 || $search_category==4)
		{
			if($search_string!="") $booking_cond =" and a.booking_no like '%$search_string%'"; else $booking_cond="";
		}
		else if($search_category==2)
		{
			if($search_string!="") $booking_cond =" and a.booking_no like '$search_string%'"; else $booking_cond="";
		}
		else if($search_category==3)
		{
			if($search_string!="") $booking_cond =" and a.booking_no like '%$search_string'"; else $booking_cond="";
		}
	}


	if ($db_type == 2) {
		if ($txt_date_from != "" && $txt_date_to != "") $process_end_date_con = " and a.process_end_date between '" . change_date_format($txt_date_from, "mm-dd-yyyy", "-", 1) . "' and '" . change_date_format($txt_date_to, "mm-dd-yyyy", "-", 1) . "'"; else $process_end_date_con = "";
	}
	if ($db_type == 0) {
		if ($txt_date_from != "" && $txt_date_to != "") $process_end_date_con = " and a.process_end_date between '" . change_date_format($txt_date_from, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($txt_date_to, "yyyy-mm-dd", "-") . "'"; else $process_end_date_con = "";
	}

	if ($service_source == 0) {
		echo "Please Select Service Source";
		die;
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") 
	{
		if ($search_by == 1) 
		{
			$search_field_cond = "and d.po_number like '$search_string'";
		} 
		elseif ($search_by == 2) 
		{
			//$search_field_cond = "and a.batch_no like '$search_string'";
		}
		elseif ($search_by == 3) 
		{
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
	$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date, po_job_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["job_no_mst"] 		= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $sales_row[csf('buyer_id')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["delivery_date"] 	= $sales_row[csf('delivery_date')];
		$sales_arr[$sales_row[csf('id')]]["po_job_no"] 	= $sales_row[csf('po_job_no')];
	}

	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

	if ($service_source == 1) 
	{
		if($search_by == 3)// || $search_by == 4
		{
			$sql = "SELECT a.id, max(a.extention_no) as extention_no, a.batch_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id, c.booking_without_order
			 FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c,fabric_sales_order_mst d 
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond $batch_no_cond $year_cond
			 group by  a.id, a.batch_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order order by a.id desc";
		}
		else if($search_by == 2)
		{

			$sql = "SELECT a.id, a.batch_no, max(a.extention_no) as extention_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id,c.booking_without_order 
			FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond $batch_no_cond
			group by  a.id, a.batch_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order order by a.id desc ";
		}
		else if ($search_by == 4) 
		{
			$sql = "SELECT a.id, a.batch_no, max(a.extention_no) as extention_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id,c.booking_without_order
			FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond $batch_no_cond  $booking_cond
			group by  a.id, a.batch_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order order by a.id desc ";
		}
		else
		{
			$sql = "SELECT a.id, a.batch_no, max(a.extention_no) as extention_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no, sum(c.qnty) as qnty,c.is_sales,c.po_breakdown_id,c.booking_without_order
			FROM pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, wo_po_break_down d 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=0 and c.entry_form=64 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond $batch_no_cond  and c.is_sales=0
			group by  a.id, a.batch_no, b.prod_id, b.body_part_id, c.barcode_no, c.roll_no,c.is_sales,c.po_breakdown_id, c.booking_without_order
			order by a.id desc";
		}
		//echo $sql;//die;
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
		$shade_matched_sql =  sql_select("select a.batch_id,a.batch_ext_no, e.barcode_no, e.batch_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls e where a.id = e.mst_id $all_barcode_cond and a.load_unload_id=2 and a.result=1 and a.status_active = 1 and a.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 $process_end_date_con");

		foreach ($shade_matched_sql as  $val) 
		{
			$shade_matched_arr[$val[csf("batch_id")]][$val[csf("batch_ext_no")]][$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
            <thead>
            <th width="40">SL</th>
            <th width="80">Batch No</th>
            <th width="50">Ext.No</th>

            <th width="150">Fabric Description</th>
            <th width="120">Body Part</th>
            <th width="80">Job No</th>
            <th width="100">Order/FSO No</th>
            <th width="80">Shipment Date</th>
            <th width="80">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
            </thead>
        </table>
        <div style="width:970px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table"
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
								//echo "==========".$job_arr[$sales_booking_no]["job_no_mst"];
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
			            		$shipment 	= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
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
                            <td width="120"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
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
		/*if($search_by == 3){
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=63 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)
			union all
			select b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)";
		}else{
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=63 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)
			union all
			select b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id FROM inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=61 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond and c.barcode_no not in(select e.id from pro_roll_details e where c.id=e.id and e.entry_form=66 and e.status_active=1 and e.is_deleted=0)";
		}*/
		//echo $sql;//die;

		if($search_by == 3)
		{
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id 
			FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, fabric_sales_order_mst d 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=65 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1 and c.roll_no>0 $search_field_cond $barcode_cond";
		}
		else if ($search_by == 2) 
		{
			$search_field_cond = "and e.batch_no like '$search_string'";
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id 
			FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, fabric_sales_order_mst d, pro_batch_create_mst e 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.batch_id=e.id and a.company_id=$company_id and c.entry_form=65 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1 and c.roll_no>0 $search_field_cond $barcode_cond
			union all
			SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id 
			FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, wo_po_break_down d, pro_batch_create_mst e 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.batch_id=e.id and a.company_id=$company_id and c.entry_form=65 and c.status_active=1 and c.is_deleted=0 and c.is_sales=0 and c.roll_no>0 $search_field_cond $barcode_cond";
		}
		else
		{
			$sql = "SELECT b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.is_sales,c.po_breakdown_id 
			FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, wo_po_break_down d 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and c.entry_form=65 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond";
		}
		//echo $sql;

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
		$scanned_barcode_arr = return_library_array("select e.barcode_no from pro_roll_details e where e.entry_form=63 and e.status_active=1 and e.is_deleted=0 and e.is_rcv_done=0 and e.is_returned=0 $all_barcode_cond", 'barcode_no', 'barcode_no');

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
		            			$job_no 			= $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
		            		}else{
		            			$job_no 			= "";
		            		}
		            		$shipment 			= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
		            		$order_no 			= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
			            }else{
			            	if($is_sales == 1){
			            		$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
			            		if($within_group == 1){
			            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
			            			//$job_no 	= $job_arr[$sales_booking_no]["job_no_mst"];
									$job_no 			= $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
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
	//$variable_excess = return_field_value("distribute_qnty", "variable_settings_production", "company_name='$data' and variable_list=51 and item_category_id=2 and is_deleted=0 and status_active=1");
	//$fabric_control_val = return_field_value("auto_update", "variable_settings_production", "company_name='$data' and variable_list=51 and item_category_id=2 and is_deleted=0 and status_active=1");

	$fabric_vari_sql = sql_select("SELECT auto_update, distribute_qnty, production_entry from variable_settings_production where company_name='$data' and variable_list=51 and item_category_id=2 and is_deleted=0 and status_active=1");

	foreach ($fabric_vari_sql as  $val) {
		
		$fabric_control_val = $val[csf("auto_update")];
		$variable_excess = $val[csf("distribute_qnty")];
		$variable_excess_qty_kg = $val[csf("production_entry")];
	}

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
	echo "document.getElementById('variable_excess_qty_kg').value 	= '" . $variable_excess_qty_kg . "';\n";

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

// ================================================================Start
if ($action == "finish_defect_popup") 
{
	echo load_html_head_contents("Order Search", "../../", 1, 1, $unicode);
	extract($_REQUEST);

	//echo $update_dtls_id."**".$roll_maintained."**".$company_id."**".test;die;
	
	$variable_data = sql_select("select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  variable_list=45 order by company_name,get_upvalue_first asc"); //company_name=$company_id and
	//echo "select fabric_grade, get_upto_first, get_upvalue_first, get_upto_second,company_name, get_upvalue_second from variable_settings_production where  variable_list=36 order by company_name,get_upvalue_first asc";
	$exc_perc = array();
	$i = 0;
	$variable_data_count = count($variable_data);
	foreach ($variable_data as $row) {
		if ($exp[$row[csf("company_name")]] == '') $i = 0;
		$exc_perc[$row[csf("company_name")]]['limit'][$i] = $row[csf("get_upvalue_first")] . "__" . $row[csf("get_upvalue_second")];
		$exc_perc[$row[csf("company_name")]]['grade'][$i] = $row[csf("fabric_grade")];
		$i++;
		$exp[$row[csf("company_name")]] = 1;
	}
	//print_r($exc_perc);
	//$js_variable_data_arr=json_encode($exc_perc);

	echo load_html_head_contents("Finish Production Entry", "../../", 1, 1, $unicode, '', '');
	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");

	/*$machine_dia_arr=return_library_array("select id, dia_width from lib_machine_name","id","dia_width");
	$color_arr=return_library_array("select id, color_name from  lib_color","id","color_name");
	$yarn_count_arr=return_library_array("select id, yarn_count from  lib_yarn_count","id","yarn_count");
	$supplier_arr=return_library_array("select a.lot, b.short_name from product_details_master a, lib_supplier b where a.supplier_id=b.id and a.item_category_id=1","lot","short_name");*/

	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	if ($roll_maintained == 1) 
	{
		/*$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.finish_production_source, a.finish_production_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c
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
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["finish_production_source"]=$row[csf("finish_production_source")];
			$roll_dtls_data_arr[$row[csf("barcode_no")]]["finish_production_company"]=$row[csf("finish_production_company")];
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

	$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source as  finish_production_source, a.knitting_company as finish_production_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.fabric_description_id as febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, null as yarn_lot,null as yarn_count, b.receive_qnty as qnty2 ,c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b ,pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.status_active=1 and b.status_active=1 and a.status_active=1 and a.entry_form in(66) and b.id=$update_dtls_id   and c.roll_no=b.roll_no ");

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
		$barcode_no = $row[csf("barcode_no")];
		$roll_no = $row[csf("roll_no")];
		$roll_id = $row[csf("roll_id")];
		$lot_arr = array_unique(explode(",", $row[csf("yarn_lot")]));
		foreach ($lot_arr as $lot) {
			$supplier_id = return_field_value("max(supplier_id) as supplier_id", "product_details_master", "item_category_id=1 and lot='$lot'", "supplier_id");
			$all_supplier .= $supplier_arr[$supplier_id] . ",";
		}
		$all_supplier = chop($all_supplier, ",");
	}
	$disable = "disabled";
	 
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
        		var page_link = 'finish_fab_prod_roll_wise_entry_controller.php?update_dtls_id=' + dtls_id + '&roll_maintained=' + roll_maintain + '&action=barcode_defect_popup';
        		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0', '../');
        		emailwindow.onclose = function () {
        			var bar_code_ref = this.contentDoc.getElementById("hide_barcode_id").value.split("**");
        			if (bar_code_ref[1] != "") {
        				get_php_form_data(bar_code_ref[0], 'barcode_roll_find', 'finish_fab_prod_roll_wise_entry_controller');
        			}
        		}
        	}
        }


        if (roll_maintain == 1) {
        	$('#txt_barcode').live('keydown', function (e) {
        		if (e.keyCode === 13) {
        			e.preventDefault();
        			var bar_code = $('#txt_barcode').val();
        			get_php_form_data(bar_code, 'barcode_roll_find', 'finish_fab_prod_roll_wise_entry_controller');
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
         	window.open("finish_fab_prod_roll_wise_entry_controller.php?data=" + data+'&action='+action, true );
         }

        function fnc_grey_defect_entry(operation) {

         	if (operation == 2) {
         		show_msg('13');
         		return;
         	}

         	if(operation == 4)
         	{	
         		generate_report_file($('#update_id').val() ,
         			'finish_productionProductionPrint', 'requires/finish_fab_prod_roll_wise_entry_controller');
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


         	var data = "action=save_update_delete_defect&operation=" + operation + get_submitted_data_string('hide_dtls_id*company_id*hide_roll_maintain*txt_barcode*txt_roll_no*roll_id*txt_qc_name*txt_roll_width*txt_roll_weight*txt_roll_length*txt_reject_qnty*txt_qc_date*total_penalty_point*total_point*fabric_grade*fabric_comments*cbo_roll_status*cbo_fabric_shade*txt_knitting_density*update_id', "../../") + data_string;
            //alert(data);return;
            //alert(data);
            freeze_window(operation);

            http.open("POST", "finish_fab_prod_roll_wise_entry_controller.php", true);
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
                	show_list_view(prod_dtls_id, 'show_qc_listview', 'dtls_list_container', 'finish_fab_prod_roll_wise_entry_controller', '');
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
        	if (type==3) 
        	{
        		var roll_weight = $('#txt_roll_weight').val();
        		$('#txt_reject_qnty').val(roll_weight);
        	}
        	else
        	{
        		$('#txt_reject_qnty').val('');
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
    								onDblClick="fn_barcode()" value="<? echo $barcode_no; ?>"
    								placeholder="Browse or Scan" <? echo $disable; ?> ></td>
    							</tr>
    							<tr bgcolor="#FFFFFF">
    								<td>Roll Number</td>
    								<td align="center">
    									<input type="text" id="txt_roll_no" name="txt_roll_no" class="text_boxes"
    									style="width:150px;" value="<? echo $roll_no; ?>" readonly placeholder="Display" <? echo $disable; ?> >
    									<input type="hidden" id="roll_id" value="<? echo $roll_id; ?>" name="roll_id">
    								</td>
    							</tr>
    							<tr bgcolor="#FFFFFF">
    								<td>QC Date</td>
    								<td align="center"><input type="text" id="txt_qc_date" name="txt_qc_date"
    									class="datepicker" style="width:150px;" placeholder="wirte"
    									readonly></td>
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
									<td align="center"><input type="text" id="txt_roll_length" name="txt_roll_length"
										class="text_boxes_numeric" style="width:150px;"
										placeholder="Display"></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>Reject Qty</td>
									<td align="center"><input type="text" id="txt_reject_qnty" name="txt_reject_qnty"
										class="text_boxes_numeric" style="width:150px;"
										placeholder="write"></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td>Construction & Composition</td>
									<td align="center"><input type="text" id="txt_constract_comp" name="txt_constract_comp"
										class="text_boxes" style="width:150px;" readonly
										placeholder="Display" value="<? echo $constraction_comp; ?>">
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
									<td>Finish Production Density</td>
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
								<tr bgcolor="#E9F3FF">
									<td>Fabric Shade</td>
									<td align="center">
										<p><?
										echo create_drop_down("cbo_fabric_shade", 152, $fabric_shade, "", 1, "-- Select --", 0, "", ''); ?></p>
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
									foreach ($finish_qc_defect_array as $defect_id => $val) 
									{
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
												onBlur="fn_panelty_point(<? echo $i; ?>)"></p>
											</td>
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
					$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$update_dtls_id and entry_form=267 and status_active = 1");
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
										onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "finish_fab_prod_roll_wise_entry_controller" );'
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
			get_php_form_data(barcode_no, 'barcode_roll_find', 'finish_fab_prod_roll_wise_entry_controller');
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
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.id=$update_dtls_id and a.entry_form in(66) and c.entry_form in(66) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0";

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

if ($action == "barcode_roll_find") {
	$supplier_arr = return_library_array("select id, short_name from  lib_supplier", "id", "short_name");
	// $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	// $data_array = sql_select($sql_deter);
	// foreach ($data_array as $row) {
	// 	$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
	// 	$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	// }

	$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.finish_production_source, a.finish_production_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(66) and c.entry_form in(66) and c.status_active=1 and c.is_deleted=0 and c.barcode_no=$data");
	foreach ($data_array as $row) {
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
	}

	exit();
}

if ($action == "save_update_delete_defect") 
{
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
	$cbo_fabric_shade = str_replace("'", "", $cbo_fabric_shade);
	/*	if ($roll_status_id!=2) 
	{
		$qc_qnty = ($qnty-$rejectQty);
	}
	else
	{
		$qc_qnty=$qnty;
	}*/

	$qc_qnty = ($roll_status_id!=2) ? ($qnty-$rejectQty) : $qnty;

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if (str_replace("'", "", $prod_dtls_id) != "") 
		{
			if ($roll_maintain == 1) 
			{
				$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$prod_dtls_id and entry_form=267 and barcode_no='$barcode_no' and status_active=1 and is_deleted=0");
				if ($pre_count[0][csf("count")] > 0) 
				{
					echo "20**Barcode Number is Already Exists";
					disconnect($con);
					die;
				}
			} else 
			{
				$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0 and entry_form=267");
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
		$field_array_mst = "id, pro_dtls_id,entry_form, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, roll_status, knitting_density, total_penalty_point, total_point, fabric_grade, comments, fabric_shade, inserted_by, insert_date";

		$data_array_mst = "(" . $id . "," . $hide_dtls_id . ",267," . $hide_roll_maintain . "," . $txt_barcode . "," . $roll_id . "," . $txt_roll_no . "," . $txt_qc_name . "," . $txt_roll_width . ",'" . $qc_qnty . "'," . $txt_roll_length . ",'" . $rejectQty . "'," . $txt_qc_date . "," . $cbo_roll_status . "," . $txt_knitting_density . "," . $total_penalty_point . "," . $total_point . "," . $fabric_grade . "," . $fabric_comments . "," . $cbo_fabric_shade . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		//echo "10**insert into pro_qc_result_mst (".$field_array_mst.") values ".$data_array_mst;die;
		$qc_update_id = $id;

		$count_tbl_length = str_replace("'", "", $count_tbl_length);

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, inserted_by, insert_date";
		$data_array_dtls = "";

		for ($i = 0; $i <= $count_tbl_length; $i++) 
		{
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
		$rID = sql_insert("pro_qc_result_mst", $field_array_mst, $data_array_mst, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);

		if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
		{
			$pro_roll_sql ="UPDATE pro_roll_details SET qnty=$qc_qnty,reject_qnty='$rejectQty' WHERE barcode_no = $txt_barcode AND entry_form=66 and dtls_id=$prod_dtls_id"; 

			$rID3 = execute_query($pro_roll_sql,1);

			if($rID3)
			{
				$roll_qc_rj_result =sql_select("SELECT sum(qnty) as qc_qnty,sum(reject_qnty) as reject_qnty from pro_roll_details where dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0 and entry_form=66");

			$pro_grey_prod_sql ="UPDATE pro_finish_fabric_rcv_dtls SET receive_qnty='".$roll_qc_rj_result[0][csf('qc_qnty')]."',reject_qty='".$roll_qc_rj_result[0][csf('reject_qnty')]."' WHERE id=$prod_dtls_id";

				$rID4 = execute_query($pro_grey_prod_sql,1);
			}
		}

		//echo "10**$rID**$rID2";die;
		// echo "10**$rID**$rID2**$rID3**$rID4";die;
		if ($db_type == 0) 
		{
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
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
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
			$pre_count =  sql_select("select count(id) as count from pro_qc_result_mst where pro_dtls_id=$hide_dtls_id and barcode_no=$txt_barcode and id <> $update_id and entry_form=267 and status_active=1 and is_deleted=0");
			if ($pre_count[0][csf("count")] > 0) 
			{
				echo "20**Barcode Number is Already Exists.";
				disconnect($con);
				die;
			}
		}

		$field_array_update = "pro_dtls_id*roll_maintain*barcode_no*roll_id*roll_no*qc_name*roll_width*roll_weight*roll_length*reject_qnty*qc_date*roll_status*knitting_density*total_penalty_point*total_point*fabric_grade*comments*fabric_shade*update_by*update_date";
		
		if ($rejectQty=="") { $rejectQty=0; }

		$data_array_update = $hide_dtls_id . "*" . $hide_roll_maintain . "*" . $txt_barcode . "*" . $roll_id . "*" . $txt_roll_no . "*" . $txt_qc_name . "*" . $txt_roll_width . "*" . $qc_qnty . "*" . $txt_roll_length . "*" . $rejectQty . "*" . $txt_qc_date . "*" . $cbo_roll_status. "*" . $txt_knitting_density . "*" . $total_penalty_point . "*" . $total_point . "*" . $fabric_grade . "*" . $fabric_comments . "*" . $cbo_fabric_shade . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$deleteDetails = execute_query("delete from  pro_qc_result_dtls where mst_id=$update_id");

		//$dtls_id = return_next_id("id", "pro_qc_result_dtls", 1);
		$field_array_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, inserted_by, insert_date";
		$data_array_dtls = "";

		for ($i = 1; $i <= $count_tbl_length; $i++) 
		{
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

		$rID = sql_update("pro_qc_result_mst", $field_array_update, $data_array_update, "id", $update_id, 0);
		$rID2 = sql_insert("pro_qc_result_dtls", $field_array_dtls, $data_array_dtls, 0);

		if($autoProductionQuantityUpdatebyQC == 1 && $roll_status_id!=2)
		{
			
			$pro_roll_sql ="UPDATE pro_roll_details SET qnty=$qc_qnty,reject_qnty='$rejectQty' WHERE barcode_no = $txt_barcode AND  entry_form=66 and dtls_id=$prod_dtls_id"; 

			$rID3 = execute_query($pro_roll_sql,1);

			if($rID3)
			{
				$roll_qc_rj_result =sql_select("SELECT sum(qnty) as qc_qnty,sum(reject_qnty) as reject_qnty from pro_roll_details where dtls_id=$prod_dtls_id and status_active=1 and is_deleted=0 and  entry_form=66");

				$pro_grey_prod_sql ="UPDATE pro_finish_fabric_rcv_dtls SET receive_qnty='".$roll_qc_rj_result[0][csf('qc_qnty')]."',reject_qty='".$roll_qc_rj_result[0][csf('reject_qnty')]."' WHERE id=$prod_dtls_id";

				$rID4 = execute_query($pro_grey_prod_sql,1);
			}
		}

		// echo "10**$rID && $deleteDetails && $rID2 && $rID3 && $rID4";die;
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
		} 
		else if ($db_type == 2 || $db_type == 1) {
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
	$sql_dtls = sql_select("select id, pro_dtls_id, barcode_no, roll_id, roll_no, total_penalty_point, total_point, fabric_grade, comments from pro_qc_result_mst where pro_dtls_id=$data and status_active=1 and entry_form=267");
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
					onClick='get_php_form_data(<? echo $row[csf("id")]; ?>, "populate_qc_from_grey_recv", "finish_fab_prod_roll_wise_entry_controller" );'
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
	$sql_qc = sql_select("select id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, roll_status, knitting_density, total_penalty_point, total_point, fabric_grade, comments, fabric_shade from pro_qc_result_mst where id=$data and entry_form=267");

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

		$data_array = sql_select("SELECT  b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count
			FROM  pro_finish_fabric_rcv_dtls b 
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
		foreach ($finish_qc_defect_array as $defect_id => $val) {
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
		echo "document.getElementById('cbo_fabric_shade').value 		= '" . $row[csf("fabric_shade")] . "';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_grey_defect_entry',1);\n";
		exit();
	}
}
// ================================================================End

if ($action == "print_barcode_b___") 
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$data = explode("***", $data);
	print_r($data);die;
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	//$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');
	///print_r($brand_id_arr['6112018']);die;
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	//N.B. Only for Knitting plan basis with Sales Order

	$sql ="SELECT a.company_id, a.recv_number, a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id, b.shift_name, b.operator_name, c.is_sales 
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	 where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and b.id=$data[1] and c.is_sales in(0,1) and a.receive_basis=2
	 group by a.company_id, a.recv_number, a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id, b.shift_name, b.operator_name, c.is_sales";


	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) 
	{
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$is_sales = $row[csf('is_sales')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$shift_name_id = $row[csf('shift_name')];
		$recv_number = $row[csf('recv_number')];
		$color_range_id = $row[csf('color_range_id')];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];

		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) {
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}


		$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
		$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.batch_no, b.tube_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
		
		$batch_no = $planning_data[0][csf('batch_no')];
		$tube_ref_no = $planning_data[0][csf('tube_ref_no')];
		$machine_name = $machine_data[0][csf('machine_no')];
		$machine_dia_width = $planning_data[0][csf('machine_dia')];
		$machine_gauge = $planning_data[0][csf('machine_gg')];
		$row[csf("within_group")] = $planning_data[0][csf('within_group')];

		$program_no = $row[csf('booking_id')];
		$grey_dia = $planning_data[0][csf('machine_dia')];
		$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
		

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$constuction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	if ($is_sales==1) 
	{
		$sales_info = sql_select("select a.job_no, a.style_ref_no, a.within_group, a.sales_booking_no, a.buyer_id, a.po_buyer from fabric_sales_order_mst a where a.id='" . $order_id . "'");
		if($sales_info[0][csf("within_group")] ==1)
		{
			$buyer_id = $sales_info[0][csf("po_buyer")];
		}else{
			$buyer_id = $sales_info[0][csf("buyer_id")];
		}
		$sales_booking_no = $sales_info[0][csf("sales_booking_no")];
		$style_ref_no = $sales_info[0][csf("style_ref_no")];
		$sales_order_str = $sales_info[0][csf("job_no")];
		$sales_order_arr = explode("-", $sales_order_str);
		$sales_order_str =  $sales_order_arr[2]."-".$sales_order_arr[3];
		$sales_order_str = "FSO: " . $sales_order_str;

		$batch_no="Batch No: ".$batch_no;
		$tube_ref_no="Tube Ref: ".$tube_ref_no;
	}
	else
	{
		if ($row[csf("receive_basis")] == 2)
		{
			$planning_booking_sql = sql_select("select a.booking_no from wo_booking_mst a,ppl_planning_entry_plan_dtls b where a.booking_no=b.booking_no and   b.dtls_id='" . $row[csf('booking_id')] . "'");
			$planning_booking_prefix=$planning_booking_sql[0][csf('booking_no')];

		}

		$po_sql = sql_select("SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no ,a.style_ref_no from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no,a.style_ref_no");

		$buyer_id = $po_sql[0][csf("buyer_name")];
		$sales_booking_no = $po_sql[0][csf('booking_no')];
		$style_ref_no = $po_sql[0][csf('style_ref_no')];
		$sales_order_str = "Job No: " . $po_sql[0][csf('job_no')];
		$batch_no='';
		$tube_ref_no='';
	}
	

	$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $buyer_id);



	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.coller_cuff_size, a.batch_no, a.tube_ref_no from pro_roll_details a where a.id in($data[0]) and a.is_sales in(0,1)";
	$res = sql_select($query);


	//$pdf=new PDF_Code128('P','mm',array(80,65));
	$pdf=new PDF_Code128('P','mm',array(76.2,76.2));
	$pdf->AddPage();
	$pdf->SetFont('Times','',10);

	$pdf->SetAutoPageBreak(false);


	$i=2; $j=1; $k=0; $br=0; $n=0;

	foreach ($res as $row) 
	{
		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}

		$pdf->SetXY($i, $j+2);
		$pdf->Write(0, $company_short_name.", " . $sales_order_str.", M/C:" . $machine_name . " - " . $machine_dia_width . "X" . $machine_gauge);
		

		$pdf->Code128($i,$j+4,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i, $j+14);
		$pdf->Write(0, $row[csf("barcode_no")]);
		$pdf->SetXY($i, $j+18);

		$pdf->Write(0, "Dt:".change_date_format($receive_date). ",BUY:".$buyer_name);
		$pdf->SetXY($i, $j+22);

		$pdf->Write(0, "Fab:".substr($comp, 0, 45));
		$pdf->SetXY($i, $j+26);

		$dia_length_tube=trim($grey_dia);
		if($dia_length_tube!="") $dia_length_tube.="/";
		$dia_length_tube.=trim($finish_dia) ." ". trim($tube_type) . ", S/L :" . trim($stitch_length) ;

		$pdf->Write(0, "Dia:".$dia_length_tube );
		$pdf->SetXY($i, $j+30);
		 
		$pdf->Write(0, "GSM:". $gsm .", ". $yarn_count . ", ". $brand );
		$pdf->SetXY($i, $j+34);

		$pdf->Write(0, "LOT: ". $yarn_lot);
		$pdf->SetXY($i, $j+38);

		$pdf->Write(0, "WT:" . number_format($row[csf('qnty')], 2, '.', '').", Sft:". $shift_name[$shift_name_id]);
		$pdf->SetXY($i, $j+42);

		$pdf->Write(0, "Roll No:". $row[csf("roll_no")].", Clr:" .substr($color, 0, 35));
		$pdf->SetXY($i, $j+46);

		$pdf->Write(0, "Color Range:". $color_range[$color_range_id]);
		$pdf->SetXY($i, $j+50);

		$pdf->Write(0, "Style Ref:". $style_ref_no);
		$pdf->SetXY($i, $j+54);

		$pdf->Write(0,  $sales_booking_no);
		$pdf->SetXY($i, $j+58);

		$pdf->Write(0, $recv_number);
		$pdf->SetXY($i, $j+62);

		$pdf->Write(0, "Batch No: ".$row[csf('batch_no')]);
		$pdf->SetXY($i, $j+66);

		$pdf->Write(0, "Tube Ref: ".$row[csf('tube_ref_no')]);

		$k++;
		$br++;
	}


	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_b") // 
{
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');
	require('../../ext_resource/pdf/code39.php');
	

	$userid=$_SESSION['logic_erp']['user_id'];
	$company_arr = return_library_array("select id company_short_name from lib_company", "id", "company_short_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=2", 'lot', 'brand');
	$buyer_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	// roll id form Finish Fabric Production and QC By Roll
	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}

	// sql from Knitting Production
	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id,a.recv_number, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name,b.body_part_id,b.grey_receive_qnty_pcs, c.batch_no, c.tube_ref_no, c.roll_no,c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";

	// echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$recv_number = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$batch_no = '';
	$tube_ref_no = '';
	$roll_no = '';
	$production_roll_arr=array();
	foreach ($result as $row) 
	{
		
		$body_part_name = $body_part[$row[csf('body_part_id')]];
		if ($row[csf('knitting_source')] == 1) 
		{
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} 
		else if ($row[csf('knitting_source')] == 3) 
		{
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$production_roll_arr[$row[csf('barcode_no')]]['tube_ref_no']=$row[csf('tube_ref_no')];
		$production_roll_arr[$row[csf('barcode_no')]]['roll_no']=$row[csf('roll_no')];
		$production_roll_arr[$row[csf('barcode_no')]]['yarn_lot']=$row[csf('yarn_lot')];
		$production_roll_arr[$row[csf('barcode_no')]]['stitch_length']=$row[csf('stitch_length')];

		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$recv_number_ex = explode("-", $row[csf('recv_number')]);
		$recv_number = $recv_number_ex[2]."-".$recv_number_ex[3];
		$booking_without_order = $row[csf('booking_without_order')];
		$qtyInPcs = $row[csf('grey_receive_qnty_pcs')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		// $job_no = return_field_value("job_no_mst", "wo_po_break_down", "id in(" . $row[csf("order_id")] . ")");
		//echo "SELECT job_no_mst from wo_po_break_down where id in('" . $row[csf("order_id")] . "')";die;
		//echo $job_no.'='.$row[csf("order_id")].'***';die;
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) 
		{
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		
		$yarn_lot = $row[csf('yarn_lot')];

		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) 
		{
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');
		$production_roll_arr[$row[csf('barcode_no')]]['brand']=$brand;
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) 
		{
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}
		$production_roll_arr[$row[csf('barcode_no')]]['yarn_count']=$yarn_count;

		if ($row[csf("receive_basis")] == 2) 
		{
			$machine_data = sql_select("SELECT machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.body_part_id, b.batch_no, b.tube_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			
			$batch_no = $planning_data[0][csf('batch_no')];
			//$tube_ref_no = $planning_data[0][csf('tube_ref_no')];
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			// $fabric_typee = array(1 => "Open Width", 2 => "Tubular", 3 => "Needle Open");			
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$tube_type_nm = '';
			if($tube_type=="Open Width")
			{
				$tube_type_nm = "O";
			}
			elseif ($tube_type=="Tubular") 
			{
				$tube_type_nm = "T";
			}
			else
			{
				$tube_type_nm = "NO";
			}
		} 
		else 
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$program_no = $row[csf('booking_id')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") 
		{
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} 
		else 
		{
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				//$comp = $determination_sql[0][csf('construction')] . ", ";
				$constuction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}
	// echo $roll_no.'<br>';
	/*$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) 
	{
		if ($row[csf("receive_basis")] == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
		}
	} 
	else 
	{
		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) 
		{
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $row[csf("booking_id")]);
		}
		if ($is_salesOrder == 1) 
		{
			$po_sql = sql_select("SELECT a.id,a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) 
			{
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		}
		else
		{

			if ($row[csf("receive_basis")] == 2)
			{
				$planning_booking_sql = sql_select("SELECT a.booking_no_prefix_num from wo_booking_mst a,ppl_planning_entry_plan_dtls b where a.booking_no=b.booking_no and   b.dtls_id='" . $row[csf('booking_id')] . "'");
				$planning_booking_prefix=$planning_booking_sql[0][csf('booking_no_prefix_num')];

			}

			$po_sql = sql_select("SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no_prefix_num from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id)");
			foreach ($po_sql as $row1) {
				$po_array[$row1[csf('id')]]['no'] = $row1[csf('po_number')];
				$po_array[$row1[csf('id')]]['job_no'] = $row1[csf('job_no')];
				$po_array[$row1[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				if ($row[csf("receive_basis")] == 2)
				{
					$po_array[$row1[csf('id')]]['booking_no'] = $planning_booking_prefix;
				}
				else
				{
					$po_array[$row1[csf('id')]]['booking_no'] = $row1[csf('booking_no_prefix_num')];
				}

				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row1[csf('buyer_name')]);
			}
		}
	}*/
	// echo "<pre>";print_r($production_roll_arr);
	$i = 1;
	$barcode_array = array();

	// query form Finish Fabric Production and QC By Roll
	// $query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.coller_cuff_size,a.qc_pass_qnty_pcs,a.barcode_no, a.qc_pass_qnty as qnty, b.fabric_grade,c.shift_name,d.recv_number_prefix_num, d.inserted_by, d.insert_date, c.batch_id, e.batch_no, c.color_id, c.gsm, c.width,c.body_part_id from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_finish_fabric_rcv_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id  left join pro_batch_create_mst e on c.batch_id=e.id where a.id in($data)";
	/*$query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.coller_cuff_size,a.qc_pass_qnty_pcs,a.barcode_no, a.qc_pass_qnty as qnty, b.fabric_grade,c.shift_name,d.recv_number_prefix_num, d.inserted_by, 
	d.insert_date, c.batch_id, e.batch_no, c.color_id, c.gsm, c.width,c.body_part_id, x.job_no, x.buyer_id, x.sales_booking_no 
	from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_finish_fabric_rcv_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id left join pro_batch_create_mst e on c.batch_id=e.id left join fabric_sales_order_mst x on x.id=a.po_breakdown_id where a.id in($data)";*/
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.coller_cuff_size,a.qc_pass_qnty_pcs, a.barcode_no, a.qc_pass_qnty as qnty, c.shift_name,d.recv_number_prefix_num, d.inserted_by, d.insert_date, c.batch_id, e.batch_no, c.color_id, c.gsm, c.width,c.body_part_id, x.job_no, x.buyer_id, x.po_buyer, x.within_group, x.sales_booking_no  , d.updated_by 
	from inv_receive_master d,  pro_finish_fabric_rcv_dtls c, pro_roll_details a, pro_batch_create_mst e, fabric_sales_order_mst x where d.id=a.mst_id and d.id=c.mst_id and d.id=a.mst_id and c.id=a.dtls_id and c.batch_id=e.id and a.po_breakdown_id= x.id and a.id in($data)";
	// echo $query;
	$res = sql_select($query);
	// buyer_name_arr[buyer_id]

	$pdf=new PDF_Code39('P','mm',array(76.2,76.2));
	$pdf->AddPage();
	$pdf->SetFont('Times','',10);
	$pdf->SetAutoPageBreak(false);


	$i=2; $j=0; $k=0; $bundle_array=array(); $br=0; $n=0;
	foreach ($res as $row) 
	{
		// $coller_cuff_size = $row[csf('coller_cuff_size')];
		$tube_ref_no=$production_roll_arr[$row[csf('barcode_no')]]['tube_ref_no'];
		$roll_no=$production_roll_arr[$row[csf('barcode_no')]]['roll_no'];
		$yarn_lot=$production_roll_arr[$row[csf('barcode_no')]]['yarn_lot'];
		$yarn_brand=$production_roll_arr[$row[csf('barcode_no')]]['brand'];
		$yarn_count=$production_roll_arr[$row[csf('barcode_no')]]['yarn_count'];
		$stitch_length=$production_roll_arr[$row[csf('barcode_no')]]['stitch_length'];
		if ($row[csf("within_group")]==1) 
		{
			$buyer_name=$buyer_name_arr[$row[csf("po_buyer")]];
		}
		else
		{			
			$buyer_name=$buyer_name_arr[$row[csf("buyer_id")]];
		}				
		$body_part_name=$body_part[$row[csf('body_part_id')]];
		$insert_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$insert_time = date("h:i a", strtotime($row[csf('insert_date')]));
		// $order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		// $sales_booking_no = $po_array[$row[csf('po_breakdown_id')]]['sales_booking_no'];

		$length_mtrs= $row[csf('qnty')] / ( $row[csf('width')] * $stitch_length / 100 ) / ( $row[csf('gsm')] / 1000 );

		if($br==1) 
		{
			$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
		}



		$pdf->Code40($i+18, $j-1.5, "Tube/Ref No-".substr($tube_ref_no,0,18), $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 0.2, 76, 0.2); // top
		$pdf -> Line(0, 4.5, 76, 4.5);

		$pdf -> Line(18, 12, 18, 76); // verticale border

		$pdf -> Line(40, 27, 40, 37); // verticale border
		$pdf -> Line(62, 27, 62, 37);// verticale border

		$pdf -> Line(40, 47, 40, 72);// verticale border
		$pdf -> Line(62, 47, 62, 72);// verticale border

		$pdf -> Line(0.2, 0, 0.2, 76); // left border
		$pdf -> Line(75.8, 0, 75.8, 76); // right border

		// $pdf->Code39($i+2, $j+5, $row[csf("barcode_no")],'','',0.30,5.5);
		$pdf->Code40($i+28, $j+4, $row[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 12, 76, 12);

		$pdf->Code40($i, $j+10.5, "FSO No. ", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+34, $j+10.5, $row[csf("job_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 17, 76, 17);

		$pdf->Code40($i, $j+15.5, "Booking No. ", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+34, $j+15.5, $row[csf("sales_booking_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 22, 76, 22);

		$pdf->Code40($i, $j+20, "Batch No. ", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+34, $j+20, $row[csf("batch_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 27, 76, 27);

		$pdf->Code40($i, $j+26, "Buyer", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+26, $buyer_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+26, "Roll No.", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+26, $roll_no, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 32, 76, 32);

		$pdf->Code40($i, $j+30, "Body Part", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+30, $body_part_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+30, "Roll Weight [Kg.]".$internal_ref, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+30, number_format($row[csf('qnty')], 2, '.', ''), $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 37, 76, 37);

		$pdf->Code40($i, $j+35.5, "Colour", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+35.5, $color_arr[$row[csf('color_id')]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 42, 76, 42);

		$pdf->Code40($i, $j+40.5, "Composition", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+40.5, $constuction.','.$comp, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 47, 76, 47);


		$pdf->Code40($i, $j+45.5, "Yarn Lot No.", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+45.5, $yarn_lot, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+45.5, "Length [Mtrs]", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+45.5, number_format($length_mtrs, 2, '.', ''), $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 52, 76, 52);

		$pdf->Code40($i, $j+50.5, "Yarn Brand", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+50.5, $yarn_brand, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+50.5, "Finishing MC", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+50.5, " ", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 57, 76, 57);

		$pdf->Code40($i, $j+55.5, "Count", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+55.5, $yarn_count, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+55.5, "Scanned Date", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+55.5, $insert_date, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 62, 76, 62);

		$pdf->Code40($i, $j+60.5, "DIA", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+60.5, $row[csf('width')], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+60.5, "Scanned Time", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+60.5, $insert_time, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 67, 76, 67);

		$pdf->Code40($i, $j+65.5, "GSM", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+16.5, $j+65.5, $row[csf('gsm')], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+39, $j+65.5, "Shift", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+60.5, $j+65.5, " ", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 72, 76, 72);

		$pdf->Code40($i, $j+70, "Packer", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
		$pdf->Code40($i+34, $j+70, $user_arr[$row[csf('updated_by')]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;

		$pdf -> Line(0, 75.8, 76, 75.8);
		
		$k++;
		$i=2; $j=$j+23;
		$br++;
	}


	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='finish_production_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_2")  
{
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');
	require('../../ext_resource/pdf/code39.php');
	

	$userid=$_SESSION['logic_erp']['user_id'];
	$company_arr = return_library_array("select id company_short_name from lib_company", "id", "company_short_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=2", 'lot', 'brand');
	$buyer_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$mc_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');

	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	// roll id form Finish Fabric Production and QC By Roll
	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}

	// sql from Knitting Production
	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id,a.recv_number, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name,b.body_part_id,b.grey_receive_qnty_pcs, c.batch_no, c.tube_ref_no, c.roll_no,c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";

	// echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$recv_number = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$batch_no = '';
	$tube_ref_no = '';
	$roll_no = '';
	$production_roll_arr=array();
	foreach ($result as $row) 
	{
		
		$body_part_name = $body_part[$row[csf('body_part_id')]];
		if ($row[csf('knitting_source')] == 1) 
		{
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} 
		else if ($row[csf('knitting_source')] == 3) 
		{
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$production_roll_arr[$row[csf('barcode_no')]]['tube_ref_no']=$row[csf('tube_ref_no')];
		$production_roll_arr[$row[csf('barcode_no')]]['roll_no']=$row[csf('roll_no')];
		$production_roll_arr[$row[csf('barcode_no')]]['yarn_lot']=$row[csf('yarn_lot')];
		$production_roll_arr[$row[csf('barcode_no')]]['stitch_length']=$row[csf('stitch_length')];

		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$recv_number_ex = explode("-", $row[csf('recv_number')]);
		$recv_number = $recv_number_ex[2]."-".$recv_number_ex[3];
		$booking_without_order = $row[csf('booking_without_order')];
		$qtyInPcs = $row[csf('grey_receive_qnty_pcs')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) 
		{
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		
		$yarn_lot = $row[csf('yarn_lot')];

		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) 
		{
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');
		$production_roll_arr[$row[csf('barcode_no')]]['brand']=$brand;
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) 
		{
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}
		$production_roll_arr[$row[csf('barcode_no')]]['yarn_count']=$yarn_count;

		if ($row[csf("receive_basis")] == 2) 
		{
			$machine_data = sql_select("SELECT machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.body_part_id, b.batch_no, b.tube_ref_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			
			$batch_no = $planning_data[0][csf('batch_no')];
			//$tube_ref_no = $planning_data[0][csf('tube_ref_no')];
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			// $fabric_typee = array(1 => "Open Width", 2 => "Tubular", 3 => "Needle Open");			
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$tube_type_nm = '';
			if($tube_type=="Open Width")
			{
				$tube_type_nm = "O";
			}
			elseif ($tube_type=="Tubular") 
			{
				$tube_type_nm = "T";
			}
			else
			{
				$tube_type_nm = "NO";
			}
		} 
		else 
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$program_no = $row[csf('booking_id')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") 
		{
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} 
		else 
		{
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				//$comp = $determination_sql[0][csf('construction')] . ", "; 
				$constuction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}
	
	$i = 1;
	$barcode_array = array();

	$query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.coller_cuff_size,a.qc_pass_qnty_pcs, a.barcode_no, a.qc_pass_qnty as qnty, c.shift_name, d.recv_number_prefix_num, d.inserted_by, d.insert_date, c.batch_id, e.batch_no, c.color_id, c.gsm, c.width,c.body_part_id, x.job_no, x.buyer_id, x.po_buyer, x.within_group, x.sales_booking_no , y.item_description,  x.style_ref_no from inv_receive_master d, pro_finish_fabric_rcv_dtls c, pro_roll_details a, pro_batch_create_mst e, fabric_sales_order_mst x , pro_batch_create_dtls y , pro_batch_create_mst z where d.id=a.mst_id and d.id=c.mst_id and d.id=a.mst_id and c.id=a.dtls_id and c.batch_id=e.id and a.po_breakdown_id= x.id and y.barcode_no = a.barcode_no and z.id=y.mst_id and z.entry_form in(0,66) and a.is_deleted=0 and a.status_active=1 and a.id in($data)";
	// echo $query;
	$res = sql_select($query);

	$pdf=new PDF_Code39('P','mm',array(76.2,76.2)); 

	$pdf->AddPage();
	$pdf->SetFont('Times','',10);
	$pdf->SetAutoPageBreak(false);



	// $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../../images/mfg.jpg';

	$machine_arr=array();
	$barcode_data="";

	foreach ($res as $row) {
		$barcode_data .= ",". $row[csf('barcode_no')]; 
	}
	
	$barcode_data= ltrim($barcode_data,',');
	$sql_machine= sql_select("select  a.entry_page, b.machine_id, a.barcode_no from pro_fab_subprocess_dtls a , pro_fab_subprocess b where a.is_deleted=0 and b.is_deleted=0 and a.status_active=1 and b.status_active=1  and a.mst_id = b.id and b.entry_form in (33,48) and a.barcode_no in ($barcode_data) order by b.entry_form");

	foreach($sql_machine as $row){
		$machine_arr[$row[csf('barcode_no')]]=$row[csf('machine_id')];
	}


	/* 

		select  a.entry_page, b.machine_id, a.barcode_no
	from pro_fab_subprocess_dtls a , pro_fab_subprocess b 
	where a.is_deleted=0 and b.is_deleted=0 and a.status_active=1 and b.status_active=1  and a.mst_id = b.id and b.entry_form in (33,48) and a.barcode_no in (24020000067,24020000066) order by b.entry_form

	*/



	$shift_arr = sql_select("select shift_name, start_time, end_time from shift_duration_entry where production_type = 2 and status_active=1 and is_deleted=0");


	$i=2; $j=0; $k=0; $bundle_array=array(); $br=0; $n=0;
	foreach ($res as $row) 
	{
		// $coller_cuff_size = $row[csf('coller_cuff_size')];
		$tube_ref_no=$production_roll_arr[$row[csf('barcode_no')]]['tube_ref_no'];
		$roll_no=$production_roll_arr[$row[csf('barcode_no')]]['roll_no'];
		$yarn_lot=$production_roll_arr[$row[csf('barcode_no')]]['yarn_lot'];
		$yarn_brand=$production_roll_arr[$row[csf('barcode_no')]]['brand'];
		$yarn_count=$production_roll_arr[$row[csf('barcode_no')]]['yarn_count'];
		$stitch_length=$production_roll_arr[$row[csf('barcode_no')]]['stitch_length'];
		if ($row[csf("within_group")]==1) 
		{
			$buyer_name=$buyer_name_arr[$row[csf("po_buyer")]];
		}
		else
		{			
			$buyer_name=$buyer_name_arr[$row[csf("buyer_id")]];
		}				
		$body_part_name=$body_part[$row[csf('body_part_id')]];
		$insert_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$insert_time = date("h:i a", strtotime($row[csf('insert_date')]));
		$item_description = explode(",",$row[csf('item_description')]);

		// $time_value = date("h:i ", strtotime($row[csf('insert_date')]));
		
		
		
		$length_mtrs= $row[csf('qnty')] / ( $row[csf('width')] * $stitch_length / 100 ) / ( $row[csf('gsm')] / 1000 );

		if($br==1) 
		{
			$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
		}

		$pdf -> Line(0, 0.2, 76, 0.2); // top
		$pdf -> Line(18, 14, 18, 76); // verticale border
		$pdf -> Line(0, 0.2, 76, 0.2); // top
		$pdf -> Line(0.2, 0, 0.2, 76); // left border
		$pdf -> Line(75.9, 0, 75.9, 76); // right border

		// $pdf -> Line(18, 0, 18, 14);
		// $pdf -> Line(62, 0, 62, 14);

		// $logo = "../../file_upload/barcode_img.jpg";
		$logo = "../../" . $_SESSION['logic_erp']["group_logo"]; // Only jpg formate are allowe 
		$pdf -> pdf_image($logo, $i-1, $j+1, 16, 10, "","jpg");
 
		$pdf->Code39($i+17, $j+1.5, $row[csf("barcode_no")],'','',$w = .2, $h = 7);
		$pdf->Code40($i+62.5, $j-1.5, $insert_date, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,5.5) ;
		$pdf->Code40($i+62.5, $j+4, $insert_time, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,5.5) ;

		$pdf -> Line(0, 14, 76, 14);
		$pdf -> Line(40, 14, 40, 20.88);
		$pdf -> Line(55, 14, 55, 20.88);

		

		$pdf->Code40($i, $j+13, "FSO No", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$fso_no = str_split($row[csf("job_no")],16);
		if(sizeof($fso_no)==1){
			$pdf->Code40($i+16.5, $j+13, $fso_no[0] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}else{
			$pdf->Code40($i+16.5, $j+11,  $fso_no[0] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
			$pdf->Code40($i+16.5, $j+14,  $fso_no[1], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}
		// $pdf->Code40($i+16.5, $j+13, $row[csf("job_no")], $ext = true, $cks = true, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+13, "Booking No", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$sales_booking_no = str_split($row[csf("sales_booking_no")],16);
		if(sizeof($sales_booking_no)==1){
			$pdf->Code40($i+54.5, $j+13, $sales_booking_no[0] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}else{
			$pdf->Code40($i+54.5, $j+11,  $sales_booking_no[0] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
			$pdf->Code40($i+54.5, $j+14,  $sales_booking_no[1], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}
		// $pdf->Code40($i+54.5, $j+13, $row[csf("sales_booking_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 20.88, 76, 20.88);
		$pdf -> Line(40, 20.88, 40, 27.77);
		$pdf -> Line(55, 20.88, 55, 27.77);
		$pdf->Code40($i, $j+20, "Buyer", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+16.5, $j+20, $buyer_name, $ext = true, $cks = true, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+20, "Style Order", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+55, $j+20, $row[csf('style_ref_no')] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 27.77, 76, 27.77);
		$pdf -> Line(40, 27.77, 40, 34.1);
		$pdf -> Line(55, 27.77, 55, 34.1);
		$pdf->Code40($i, $j+27, "Batch No.", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$batch_no = str_split($row[csf("batch_no")],16);
		if(sizeof($batch_no)==1){
			$pdf->Code40($i+16.5, $j+27, $batch_no[0], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}else{
			$pdf->Code40($i+16.5, $j+25.5, $batch_no[0], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
			$pdf->Code40($i+16.5, $j+28, $batch_no[1], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}
		// $pdf->Code40($i+16.5, $j+27, $row[csf("batch_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+27, "Colour.", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+55, $j+27, $color_arr[$row[csf('color_id')]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 34.66, 76, 34.66);
		$pdf->Code40($i, $j+33, "Fabric.", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$fabric_dtls = $constuction.','.$comp ;
		$fabric_dtls = str_split($fabric_dtls,52);
		if(sizeof($fabric_dtls)==1){
			$pdf->Code40($i+16.5, $j+33.5, $fabric_dtls[0] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}else{
			$pdf->Code40($i+16.5, $j+32.5, $fabric_dtls[0] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
			$pdf->Code40($i+16.5, $j+35.5, $fabric_dtls[1] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}

		$pdf -> Line(0, 41.55, 76, 41.55);
		$pdf -> Line(40, 41.55, 40, 48.44);
		$pdf -> Line(55, 41.55, 55, 48.44);
		$pdf->Code40($i, $j+40.5, "Booking Dia", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+23, $j+40.5, $item_description[3], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+38.8, $j+40.5, "Booking GSM", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+60.5, $j+40.5,  $item_description[2], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 48.44, 76, 48.44);
		$pdf -> Line(40, 48.44, 40, 55.33);
		$pdf -> Line(55, 48.44, 55, 55.33);
		$pdf->Code40($i, $j+47.5, "Actual Dia", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+23, $j+47.5, $row[csf('width')], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+47.5, "Actual GSM", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+60.5, $j+47.5, $row[csf('gsm')] , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 55.33, 76, 55.33);
		$pdf -> Line(40, 55.33, 40, 62.22);
		$pdf -> Line(55, 55.33, 55, 62.22);
		$pdf->Code40($i, $j+53, "Fabric", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i, $j+56, "Length(m)", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+23, $j+54, number_format($length_mtrs, 2, '.', '') , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+54, "Finish(Wgt.)", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+60.5, $j+54, number_format($row[csf('qnty')], 2, '.', '') , $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 62.22, 76, 62.22);
		$pdf -> Line(40, 62.22, 40, 69.11);
		$pdf -> Line(55, 62.22, 55, 69.11);
		$pdf->Code40($i, $j+61.5, "Roll No.", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+23, $j+61.5, $roll_no, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+61.5, "M/C No", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+60.5, $j+61.5, $mc_arr[$machine_arr[$row[csf('barcode_no')]]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;

		$pdf -> Line(0, 69.11, 76, 69.11);
		$pdf -> Line(40, 69.11, 40, 75.8);
		$pdf -> Line(55, 69.11, 55, 75.8);
		$pdf->Code40($i, $j+67.5, "Shift", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+23, $j+67.5, $shift, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$pdf->Code40($i+39, $j+67.5, "Packer", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		$packer = $user_arr[$row[csf('inserted_by')]] ;
		$packer = str_split($packer,17);
		if(sizeof($packer)==1){
			$pdf->Code40($i+54, $j+67.5, $packer[0] ,  $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}else{
			$pdf->Code40($i+54, $j+66, $packer[0] ,  $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
			$pdf->Code40($i+54, $j+68, $packer[1] ,  $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,6) ;
		}

		$pdf -> Line(0, 75.8, 76, 75.8);
		
		$k++;
		$i=2; $j=$j+23;
		$br++;
	}


	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='finish_production_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=309 and is_deleted=0 and status_active=1");
	echo trim($print_report_format);
	exit();

}

// function get_shift($inputTime) {

// 	$shift_arr = sql_select("select shift_name, start_time, end_time from shift_duration_entry where production_type = 2 and status_active=1 and is_deleted=0");
// 	$inputTimestamp = strtotime($inputTime);
	

// 	foreach($shift_arr as $row){
// 		$start_time = $row[csf('start_time')];
// 		$end_time= $row[csf('end_time')];
// 		$start = strtotime($start_time);
// 		$end = strtotime($end_time);
// 		if ($inputTimestamp >= $start && $inputTimestamp < $end) {
// 			return $row[csf('shift_name')];
// 			echo $end;
// 		}
// 	}
// }

?>
